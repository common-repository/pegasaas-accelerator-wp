<?php
if (!class_exists("PegasaasDeferment")) :

class PegasaasDeferment {
	static $building_critical_css = false;
	static $post_type_cpcss = array();
	static $page_level_cpcss = array();
	
	function __construct() {
		
	}
	
	static function inject_essential_css($buffer) {
		global $pegasaas;
		
		$essential_css = PegasaasAccelerator::$settings['settings']['essential_css']['css'];
		
		// find the first <link> and place cpcss before that 
		$position_of_closing_head_tag 	= strpos($buffer, "</head>");
		list($position_of_first_style_tag, $first_style_tag, $buffer) 	= self::get_position_of_first_tag("style", $buffer);
		list($position_of_first_link_tag, $first_link_tag, $buffer)		= self::get_position_of_first_tag("link", $buffer, "rel", "(stylesheet|preload)");
		$position_of_first_title_tag 	= strpos($buffer, "<title");

		

		
		$inject_essential_css_status = 3;
			
			// should always put the essential css just before the first stylesheet tag, but still after any styles
			// previous to this update, we only put it before the first stylesheet if it came before a <style> tag
			if ($position_of_first_link_tag > 0 && $position_of_closing_head_tag > $position_of_first_link_tag && ($inject_essential_css_status == 3 || $inject_essential_css_status == 1)) {
			   $inject_essential_css_status = 4;
			} 
			
			
			$injectable_code = "<style>/* Pegasaas Accelerator Global Essential CSS */ {$essential_css}</style>";
			
			if ($inject_essential_css_status == "4" && $position_of_first_link_tag > 0 && $position_of_closing_head_tag > $position_of_first_link_tag) {
		
				
				$buffer = str_replace($first_link_tag, "\n{$injectable_code}\n{$first_link_tag}", $buffer);
			} else if ($inject_essential_css_status == "3" && $position_of_first_style_tag > 0 && $position_of_closing_head_tag > $position_of_first_style_tag) {
				$buffer = preg_replace('/<style/', "\n{$injectable_code}\n<style", $buffer, 1);
			} else  if ($inject_essential_css_status > 0) {
				$buffer = str_replace("</head>", "\n{$injectable_code}\n</head>", $buffer);

			}  else {
							

			}
		
		return $buffer;
		
	}
	
	
	static function clear_deferred_js() {
		global $pegasaas;
		
		$deferred_js_records = get_option('pegasaas_deferred_js', array());
		
		foreach ($deferred_js_records as $resource_id => $info) {	
			$resource_path = $pegasaas->utils->strip_query_string($resource_id);
			$path =  PEGASAAS_CACHE_FOLDER_PATH."{$resource_path}deferred-*.js";
			
			$pegasaas->cache->clear_cache_file($path);
					
			$pegasaas->utils->delete_object_meta($resource_id, "deferred_js");
		}
		
		delete_option("pegasaas_deferred_js");
	}	

	
	static function defer_css($buffer) {
		global $pegasaas;
	
		
		$stylesheet_count = 0;
		

		if (strstr($_SERVER['HTTP_USER_AGENT'], "PegasaasAccelerator") > -1 || $_GET['build_css'] == 1) { 
			$critical_css_builder_request = true;
			self::$building_critical_css = true;
		}
		
		$css_deferal_version 		= 3; // 1 = loadCSS, 2 = lightweight loader, 3 = hybrid with rel="preload"
		$defer_stylesheet_method    = 0;
		
				
		// user defined stylesheets to not defer
		$defer_render_blocking_css_exclude_stylesheets = explode("\n", trim(str_replace("\r", "", PegasaasAccelerator::$settings['settings']['defer_render_blocking_css']['exclude_stylesheets'])));
		
		
		// tempoarily convert all IE conditional statements to something that won't be identified by this
		// replace IE conditional statements with temporary code
		$matches = array();
		$pattern = '/\<'.'!--\[if(.*?)\<'.'!\[endif/s';
		preg_match_all($pattern, $buffer, $matches);
		foreach ($matches[0] as $match_data) {
		  $find 		= $match_data;
		  $replace 		= str_replace("<link", "<ielink", $find);
		  $buffer = str_replace($find, $replace, $buffer);  
		}	
		$loader_html = "<script>".file_get_contents(PEGASAAS_ACCELERATOR_DIR.'assets/js/cssrelpreload.min.js')."</script>";
		
		// strip all <script>...</script> tags, so that we don't attempt to modify javascript injected <link> tags
		$stripped_buffer = preg_replace("/<script(.*?)<\/script>/si", "[shouldhavebeenscript]", $buffer);
		
		
		// fetch all link references
		$matches = array();
		$pattern = "/<link(.*?)>/si";
    	preg_match_all($pattern, $stripped_buffer, $matches);
	 
		$href_pattern 	= "/href=['\"](.*?)['\"]/si";
		$media_pattern 	= "/media=['\"](.*?)['\"]/si";
		$rel_pattern 	= "/rel=['\"](.*?)['\"]/si";

		
		$combine_css = "";
		
		foreach ($matches[0] as $css_link) {
			$match_href 	= array();
			
			
			$match_media 	= array();
			$match_rel 		= array();

			preg_match($href_pattern, $css_link, $match_href);
			preg_match($media_pattern, $css_link, $match_media);
			preg_match($rel_pattern, $css_link, $match_rel);
		
			$href 	= $match_href[1];
			$media 	= $match_media[1];
			$rel 	= $match_rel[1];
			
		
			
			
			
			// skip this iteration if this is not a stylesheet
			if ($rel != "stylesheet") { continue; }
			
			// if this is an external reference then push it through external CSS loader because external
			// CSS files do not load for the critical above-the-fold-css builder
			if (strstr($href, "//") && !strstr($href, $_SERVER['HTTP_HOST'])) {
				$old_href 	= $href;
				
				if ($critical_css_builder_request) {
					$new_css_link = str_replace($old_href, $href, $css_link);
					$buffer = str_replace($css_link, $new_css_link, $buffer);	
				}
			}
		
			if ($critical_css_builder_request) {
				// do nothing
						
			} else if ($css_deferal_version == 3) {
				$new_css_link = $css_link;
				
				$file_extension = PegasaasUtils::get_file_extension($pegasaas->utils->strip_query_string($href));

				if (strstr($href, "?") !== false && 
					strstr($href, "fonts.googleapis.com/css?") === false && 
					strstr($href, "sfgcdn.pegasaas.io") === false &&
					$file_extension == "css"
				   ) {
					$stripped_href 	= $pegasaas->utils->strip_query_string($href);
					$new_css_link 	= str_replace($href, $stripped_href, $new_css_link);
				}
				
				// if this does not contain a preload attribute already, then apply it
				if (strstr($new_css_link, 'preload') !== true ) {
					
					$new_css_link = str_replace("rel='stylesheet'", 'rel="stylesheet"', $new_css_link);
					
					if ($defer_stylesheet_method == 0 || $defer_stylesheet_method == "") { 
						if (PegasaasUtils::is_in_list($href, $defer_render_blocking_css_exclude_stylesheets)) {
							$new_css_link = $new_css_link;
						} else {
							// condition DIVI attributes so they don't interfere
							$new_css_link = str_replace("onload=", "data-onload=", $new_css_link);
							$new_css_link = str_replace("onerror=", "data-onerror=", $new_css_link);
							
							$new_css_link = str_replace('rel="stylesheet"', 'rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\';"', $new_css_link);
						}
						
					
					} 
					
					/* pro component begin */
					else if ($defer_stylesheet_method >= 1) {
						// do not defer, if the stylesheet has been flagged by the user in the settings panel
						if (PegasaasUtils::is_in_list($href, $defer_render_blocking_css_exclude_stylesheets)) {		
							$new_css_link = $new_css_link;
						
						// do not lazy load, if the stylesheet has been flagged by the user in the settings panel	
						} else if (PegasaasUtils::is_in_list($href, $defer_unused_css_exclude_stylesheets)) {
							// condition DIVI attributes so they don't interfere
							$new_css_link = str_replace("onload=", "data-onload=", $new_css_link);
							$new_css_link = str_replace("onerror=", "data-onerror=", $new_css_link);
							
							$new_css_link = str_replace('rel="stylesheet"', 'rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\';"', $new_css_link);

						} else {
							// condition DIVI attributes so they don't interfere
							$new_css_link = str_replace("onload=", "data-onload=", $new_css_link);
							$new_css_link = str_replace("onerror=", "data-onerror=", $new_css_link);
							
							$new_css_link = str_replace('rel="stylesheet"', 'rel="pending-stylesheet" as="style" ', $new_css_link);
							$new_css_link = str_replace(' href=', ' data-href=', $new_css_link);

						}
						
					} 
					/* pro component end */
					
					
					
					
					
					
					
					
					if (strstr($new_css_link, "preload") !== false) {
					
						$noscript_css_link = $css_link;
						
						if (strstr($href, "?") !== false && strstr($href, "fonts.googleapis.com/css?") === false && strstr($href, "sfgcdn.pegasaas.io") === false) {
							$stripped_href 	= $pegasaas->utils->strip_query_string($href);
							$noscript_css_link 	= str_replace($href, $stripped_href, $noscript_css_link);
						}					
						$new_css_link .= "\n<noscript>{$noscript_css_link}</noscript>";
					}
					/* pro component begin */
					else if (strstr($new_css_link, "pending-stylesheet") !== false) {
						$noscript_css_link = $css_link;
						
						if (strstr($href, "?") !== false && strstr($href, "fonts.googleapis.com/css?") === false && strstr($href, "sfgcdn.pegasaas.io") === false) {
							$stripped_href 	= $pegasaas->utils->strip_query_string($href);
							$noscript_css_link 	= str_replace($href, $stripped_href, $noscript_css_link);
						}					
						$new_css_link .= "\n<noscript>{$noscript_css_link}</noscript>";
						
					}
					/* pro component end */
				}
				
				$buffer = str_replace($css_link, $new_css_link, $buffer);
				
			} 
		}

		
		
		/**** 
		 * condition code for styesheet calls which are injected via the insertAdjacentHTML mechanism, commonly seen in Themify themes
		 ****/
		// regular expression to find all script that contains the insertAdjcentHTML
		$matches = array();
		$pattern = "/\.insertAdjacentHTML\((.*?)\)/si";
    	preg_match_all($pattern, $buffer, $matches);
	
		foreach ($matches[1] as $insert_adjacent_code) {
			// regular expression to find any link tag within the match
			// fetch all link references
			$matches2 = array();
			$pattern = "/([\"'])<link(.*?)>([\"'])/si";
			preg_match_all($pattern, $insert_adjacent_code, $matches2);
			$existing_quote = $matches2[1][0];
			
			// made copy of the original
			$new_insert_adjacent = $insert_adjacent_code;
			
			
			// do conditioning if match is found
			if ($existing_quote == "\"") {
				
				$new_insert_adjacent = str_replace('rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\';"', 
												   'rel=\\"preload\\" as=\\"style\\" onload=\\"this.onload=null;this.rel=\'stylesheet\';\\"', 
												   $new_insert_adjacent);

				/* pro component begin */
				$new_insert_adjacent = str_replace('rel="preload" as="style" onload="this.onload=null;this.rel=\'pending-stylesheet\';"', 
												   'rel=\\"preload\\" as=\\"style\\" onload=\\"this.onload=null;this.rel=\'pending-stylesheet\';\\"', 
												   $new_insert_adjacent);

				$new_insert_adjacent = str_replace('rel="preload-stylesheet" as="style"', 
												   'rel=\\"preload-stylesheet\\" as=\\"style\\" ', 
												   $new_insert_adjacent);
				/* pro component end */
				
			} else {
				$new_insert_adjacent = str_replace('rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\';"',
												   'rel="preload" as="style" onload="this.onload=null;this.rel=\\\'stylesheet\\\';"', 
												   $new_insert_adjacent);
				
				/* pro component begin */
				$new_insert_adjacent = str_replace('rel="preload" as="style" onload="this.onload=null;this.rel=\'pending-stylesheet\';"',
												   'rel="preload" as="style" onload="this.onload=null;this.rel=\\\'pending-stylesheet\\\';"', 
												   $new_insert_adjacent);
				
				/* pro component end */
	
			}
			
			// replace
			if ($insert_adjacent_code != $new_insert_adjacent) {
				$buffer = str_replace($insert_adjacent_code, $new_insert_adjacent, $buffer);
			}
		}	

		
		/**** 
		 * condition code for styesheet calls which are injected via the ('head').append mechanism, commonly seen in Themify themes
		 ****/
		
		$matches = array();
		$pattern = "/\(\s?\Whead\W\s?\)\.append\((.*?)\)/si";
    	preg_match_all($pattern, $buffer, $matches);
		
	
		foreach ($matches[1] as $insert_adjacent_code) {
			// regular expression to find any link tag within the match
			// fetch all link references

			$matches2 = array();
			$pattern = "/([\"'])<link(.*?)>([\"'])/si";
			preg_match_all($pattern, $insert_adjacent_code, $matches2);
			$existing_quote = $matches2[1][0];
			
			// made copy of the original
			$new_insert_adjacent = $insert_adjacent_code;
			
			// do conditioning if match is found
			if ($existing_quote == "\"") {
				$new_insert_adjacent = str_replace('rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\';"', 
												   'rel=\\"preload\\" as=\\"style\\" onload=\\"this.onload=null;this.rel=\'stylesheet\';\\"', 
												   $new_insert_adjacent);
				
				/* pro component begin */
				$new_insert_adjacent = str_replace('rel="preload" as="style" onload="this.onload=null;this.rel=\'pending-stylesheet\';"', 
												   'rel=\\"preload\\" as=\\"style\\" onload=\\"this.onload=null;this.rel=\'pending-stylesheet\';\\"', 
												   $new_insert_adjacent);
				
				$new_insert_adjacent = str_replace('rel="preload-stylesheet" as="style"', 
												   'rel=\\"preload-stylesheet\\" as=\\"style\\" ', 
												   $new_insert_adjacent);
				/* pro component end */
				
			} else {
				$new_insert_adjacent = str_replace('rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\';"',
												   'rel="preload" as="style" onload="this.onload=null;this.rel=\\\'stylesheet\\\';"', 
												   $new_insert_adjacent);				
				/* pro component begin */
				$new_insert_adjacent = str_replace('rel="preload" as="style" onload="this.onload=null;this.rel=\'pending-stylesheet\';"',
												   'rel="preload" as="style" onload="this.onload=null;this.rel=\\\'pending-stylesheet\\\';"', 
												   $new_insert_adjacent);
				/* pro component end */
			}
			
			// replace
			if ($insert_adjacent_code != $new_insert_adjacent) {
				$buffer = str_replace($insert_adjacent_code, $new_insert_adjacent, $buffer);
			}
		}			
		
		
		
		// fetch all import references
		$matches = array();
		$pattern = "/@import url\((.*?)\);/si";
    	preg_match_all($pattern, $buffer, $matches);
		foreach ($matches[0] as $index => $import_statement) {
			$href = $matches[1]["{$index}"];
			$href = trim($href, '"');
			$href = trim($href, "'");
			
			
			
			// inject the actual code into the html
			if ((strstr($href, "fonts.googleapis.com") || strstr($href, "fonts-google-cdn")) && (PegasaasAccelerator::$settings['settings']['google_fonts']['status'] != 0)) {
				if (substr($href, 0, 2) == "//") {
					$href = "https:".$href;
				}
				$file_contents = $pegasaas->utils->get_file($href);
				
				// if google returned an error, then we need to try to fetch the alternate location
				if (strstr($file_contents, "The requested URL was not found on this server.")) {
					$href = str_replace("fonts-google-cdn.pegasaas.io", "fonts.googleapis.com", $href);
					$file_contents = $pegasaas->utils->get_file($href);
				}
				// so long as there is no error, then inject the direct code and continue to the next import, otherwise, do not replace
				if (!strstr($file_contents, "The requested URL was not found on this server.")) {
					$buffer = str_replace($import_statement, $file_contents, $buffer);
					continue;
				} 
				
				
			}

			
		
			if ($critical_css_builder_request) {
				// do nothing
				
			} else if ($css_deferal_version == 3) { 
				
				if ($defer_stylesheet_method == 0 || $defer_stylesheet_method == "") { 
					// do not defer, if the stylesheet has been flagged by the user in the settings panel
					if (PegasaasUtils::is_in_list($href, $defer_render_blocking_css_exclude_stylesheets)) {
						$new_import_statement = '<link rel="stylesheet" href="'.$href.'">';
					} else {
						$new_import_statement = '<link rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'" href="'.$href.'">';
						$new_import_statement .= '<noscript><link rel="stylesheet" href="'.$href.'"></noscript>';

					}
	
				} 
			
		
				// replace stylesheet link with a placeholder
				$buffer = str_replace($import_statement, "</style>{$new_import_statement}<style>", $buffer);
				
			}  
		}		
		if ($defer_stylesheet_method == 1) {
			$defer_stylesheet_method = 3; // as soon as possible
			
		}
		
		
		
		/* pro component begin */
		if ($defer_stylesheet_method > 0) { 
			$lazy_loader_html = "<script>/* pegasaas-inline */ 
			var pegasaas_styles_lazy_loaded = false;
		function lazy_load_styles() {
			if (pegasaas_styles_lazy_loaded) {
				return;
			}
				var all_stylesheets 			= document.getElementsByTagName('link');

				for(var i = 0; i < all_stylesheets.length ; i++) {
					var stylesheet = all_stylesheets[i];
					
					if (stylesheet.getAttribute('rel') == 'pending-stylesheet') {
							stylesheet.setAttribute('rel', 'stylesheet');
					}
					if (stylesheet.getAttribute('data-href') != '' && stylesheet.getAttribute('data-href') != null) {
							stylesheet.setAttribute('href', stylesheet.getAttribute('data-href'));
							stylesheet.removeAttribute('data-href');
					}					
				}
				pegasaas_styles_lazy_loaded = true
			}


			window.addEventListener('scroll', function(event) 	{ lazy_load_styles(); });
			document.addEventListener('click', function(event) 	{ lazy_load_styles(); });
			";
			
			if ($defer_stylesheet_method != 2) {
				$page_check_delay['1'] = 0;
				$page_check_delay['3'] = 0;
				$page_check_delay['4'] = 1500;
				$page_check_delay['5'] = 5000;
				
				$lazy_loader_html .= "
			function pegasaas_lazy_load_style_check_page_state() {
				if (document.readyState === 'complete') {
					clearTimeout(pegasaas_page_sate_observer);
					lazy_load_styles();

				} else {
					setTimeout('pegasaas_lazy_load_style_check_page_state()', 500);
				}
			}
			var pegasaas_page_sate_observer = setTimeout('pegasaas_lazy_load_style_check_page_state()', ".$page_check_delay["{$defer_stylesheet_method}"].");
				";
			} 
			
			$lazy_loader_html .= "</script>";
			
			$lazy_loader_mobile_styles = "<style>@media(max-width:767px) { 
		.et_mobile_menu { display: none; }
		}</style>";
		}
		/* pro component end */
		
		if ($css_deferal_version == 3) {
			if (strstr($buffer, "loadCSS. [c]2017 Filament Group") === false) {
					$buffer = str_replace("</head>", "{$loader_html}{$lazy_loader_mobile_styles}</head>", $buffer);
					$buffer = str_replace("</body>", "{$lazy_loader_html}</body>", $buffer);
			} 
		} 
	
		// remove trailing comma from webfonts
		$webfontCode = '/var pegasaas_async_stylesheets = {(.*?)};/si';
		$webfontMatches = array();
	
		preg_match($webfontCode, $buffer, $webfontMatches);
		$buffer = str_replace($webfontMatches[1], rtrim($webfontMatches[1], ' ,'), $buffer);
		
		// restore IE conditional comment script to the HTML
		$buffer = str_replace("<ielink", "<link", $buffer);

		return $buffer;


	}

	
	
	static function inject_critical_css($buffer) {
		global $pegasaas;
		
		$debug = false; 
		$resource_id = PegasaasUtils::get_object_id();
		
		
		$image_cache_server = PegasaasCache::get_cache_server(PegasaasAccelerator::$settings['settings']['webp_images']['status'] == 1, "img");
		
		
		$resource_cache_server = PegasaasCache::get_cache_server(false, "css");
	
		$critical_css 			= self::get_critical_css();
	
		
		$page_critical_css = $critical_css['css'];
		$page_critical_css = self::fix_cpcss($page_critical_css);
		if ($debug) { $buffer .= "\n".$pegasaas->condition_comment_codes($pegasaas->utils->get_total_page_build_time(false, "In Inject CPCSS skip: {$critical_css['skip']}")); }
		$preload_html = "";
		
		if (PegasaasAccelerator::$settings['settings']['image_optimization']['status'] != 0) {
			// save the domain name, as it is in the top comment, by changing the url slightly
			$page_critical_css 		= str_replace(" for ".home_url("", "http")."/"," for ".home_url("", "http")."#", $page_critical_css);
			$page_critical_css 		= str_replace(" for ".home_url("", "https")."/"," for ".home_url("", "https")."#", $page_critical_css);
					
			$matches = array();
			$bg_image_tag_pattern 	= '/[\s]?url\([\'"]?(.*?)[\'"]?\)/si';
			preg_match_all($bg_image_tag_pattern, $page_critical_css, $matches);
			
			$protocol_independant_url = str_replace("https://", "//", home_url("", "https"));
			$protocol_independant_url_no_www = str_replace("//www.", "//", $protocol_independant_url);
			
			// iterate through the matches
			foreach ($matches[0] as $i => $find) {  
				
				$src = $matches[1][$i];
				$src_name_value = $find;
				$src_name_value_replace = $find;
			
				$replace 	= $find;
				$domain_match = array();
			
				$file_extension = PegasaasUtils::get_file_extension($pegasaas->utils->strip_query_string($src));
				
				if ($file_extension == "png" || $file_extension == "jpg" || $file_extension == "jpeg") {
					if (!strstr($replace, $image_cache_server)) {
						$replace 		= str_replace(home_url("", "http")."/", $image_cache_server, $replace);
					}
					if (!strstr($replace, $image_cache_server)) {
						$replace 		= str_replace(str_replace("http://www.", "http://", home_url("", "http"))."/", $image_cache_server, $replace);
					}
					if (!strstr($replace, $image_cache_server)) {
						$replace 		= str_replace(home_url("", "https")."/", $image_cache_server, $replace);
					}
					if (!strstr($replace, $image_cache_server)) {
						$replace 		= str_replace(str_replace("https://www.", "https://", home_url("", "https"))."/", $image_cache_server, $replace);
					}
					if (!strstr($replace, $image_cache_server)) {
						$replace 		= str_replace($protocol_independant_url."/", $image_cache_server, $replace);
					}
					if (!strstr($replace, $image_cache_server)) {
						$replace 		= str_replace($protocol_independant_url_no_www."/", $image_cache_server, $replace);
					}
				
					
					$page_critical_css = str_replace($find, $replace, $page_critical_css);
				} else {
					if (!strstr($replace, $resource_cache_server)) {
						$replace 		= str_replace(home_url("", "http")."/", $resource_cache_server, $replace);
					}
					if (!strstr($replace, $resource_cache_server)) {
						$replace 		= str_replace(str_replace("http://www.", "http://", home_url("", "http"))."/", $resource_cache_server, $replace);
					}
					if (!strstr($replace, $resource_cache_server)) {
						$replace 		= str_replace(home_url("", "https")."/", $resource_cache_server, $replace);
					}
					if (!strstr($replace, $resource_cache_server)) {
						$replace 		= str_replace(str_replace("https://www.", "https://", home_url("", "https"))."/", $resource_cache_server, $replace);
					}
					if (!strstr($replace, $resource_cache_server)) {
						$replace 		= str_replace($protocol_independant_url."/", $resource_cache_server, $replace);
					}
					if (!strstr($replace, $resource_cache_server)) {
						$replace 		= str_replace($protocol_independant_url_no_www."/", $resource_cache_server, $replace);
					}
			
					$page_critical_css = str_replace($find, $replace, $page_critical_css);
				
				}
				
				if ($file_extension == "ttf" || $file_extension == "woff" || $file_extension == "woff2") {
				
							$preload_html .= "<link rel='preload' as='font' href='{$src}' />";
					
				}
					
			}
			//exit;
					
			// save the domain name, as it is in the top comment, by reverting the url to the original form
			$page_critical_css 		= str_replace(" for ".home_url("", "http")."#"," for ".home_url("", "http")."/", $page_critical_css);
			$page_critical_css 		= str_replace(" for ".home_url("", "https")."#"," for ".home_url("", "https")."/", $page_critical_css);
		
			/* pro component begin */
						if (PegasaasAccelerator::$settings['settings']['enable_default_font_display'] != 0 || PegasaasAccelerator::$settings['settings']['enable_default_font_display'] != "") { 
						  if (PegasaasAccelerator::$settings['settings']['enable_default_font_display']['status'] == 1) {
							  $font_display = "fallback";
						  } else {
							  $font_display = PegasaasAccelerator::$settings['settings']['enable_default_font_display']['status'];
						  }
						
							$page_critical_css = str_replace("font-face{", "font-face{font-display:{$font_display};", $page_critical_css);
						}			
			
			/* pro component end */
			
			
		}
		
		
		
		
		
		if (PegasaasAccelerator::$settings['settings']['remove_query_strings']['status'] != 0) {
			$page_critical_css = self::strip_query_strings_from_css($page_critical_css);
		}

		
		
		$page_critical_css = str_replace(" */", " [{$critical_css['type']}] */", $page_critical_css);

		
		// if the critical CSS exists and is not stale then inject it into the page
		if (!$critical_css['skip']) {
			$temporary_scripts = array();
			
			// temporarily change conditional comment tags to preserve them
			$pattern = "/<!--\[if(.*?)<!\[endif\]-->/si";
			$script_count = 0;
		
			$matches = array();

			preg_match_all($pattern, $buffer, $matches);

			foreach ($matches[0] as $script_html) {
			  $temporary_scripts["{$script_count}"] = $script_html;
			  $buffer = str_replace($script_html, "[temp_conditional_{$script_count}]", $buffer);
			  $script_count++;
			}
 

			$buffer_array['critical_css_available'] = true;
			
			// find the first <link> and place cpcss before that 
			$position_of_closing_head_tag 	= strpos($buffer, "</head>");
			list($position_of_first_style_tag, $first_style_tag, $buffer) 	= self::get_position_of_first_tag("style", $buffer);
			list($position_of_first_link_tag, $first_link_tag, $buffer)		= self::get_position_of_first_tag("link", $buffer, "rel", "(stylesheet|preload)");
			$position_of_first_title_tag 	= strpos($buffer, "<title");

			$inject_critical_css_status = 0; 
			
			//$page_level_settings = (array)get_post_meta($this->post->ID, "pegasaas_accelerator_overrides", true); 
			$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides"); 

		
			if (@$page_level_settings['inject_critical_css'] == "") {
				$inject_critical_css_status = PegasaasAccelerator::$settings['settings']['inject_critical_css']['status'];
			} else {
				$inject_critical_css_status = $page_level_settings['inject_critical_css'];		
			}	
			
			// should always put the cpcss just before the first stylesheet tag, but still after any styles
			// previous to this update, we only put it before the first stylesheet if it came before a <style> tag
			if ($position_of_first_link_tag > 0 && $position_of_closing_head_tag > $position_of_first_link_tag && ($inject_critical_css_status == 3 || $inject_critical_css_status == 1)) {
			   $inject_critical_css_status = 4;
			} 
			
			
			$injectable_code = "<style>{$page_critical_css}</style>";
	
			
			if ($inject_critical_css_status == "4" && $position_of_first_link_tag > 0 && $position_of_closing_head_tag > $position_of_first_link_tag) {
		
				
				$buffer = str_replace($first_link_tag, "\n{$injectable_code}\n{$first_link_tag}", $buffer);
			} else if ($inject_critical_css_status == "3" && $position_of_first_style_tag > 0 && $position_of_closing_head_tag > $position_of_first_style_tag) {
				$buffer = preg_replace('/<style/', "\n{$injectable_code}\n<style", $buffer, 1);
			} else  if ($inject_critical_css_status > 0) {
				$buffer = str_replace("</head>", "\n{$injectable_code}\n</head>", $buffer);

			}  else {
							

			}
			

			foreach ($temporary_scripts as $i => $script_html) {
			 	$buffer = str_replace("[temp_conditional_{$i}]", $script_html, $buffer);
			}			
			
			$buffer_array['buffer'] = $buffer; // need to modify the HTML here

		
		} else {
			$queued = $critical_css['queued'];
		
			$buffer_array['critical_css_available'] = false;
			
			$buffer_array['buffer'] = $buffer;
			if ($queued) { 
			    self::$building_critical_css = true;
				$buffer_array['buffer'] .= "\n<!-- pegasaas://accelerator - critical css queued / {$critical_css['status']} -->";
			} else {
				$buffer_array['buffer'] .= "\n<!-- pegasaas://accelerator - critical css not queued / {$critical_css['status']} -->";
			}
		}
		
		

	  	return $buffer_array;	
	}
	
	static function get_critical_css_records($type) {
		global $pegasaas;
		
		$all = get_option('pegasaas_critical_css', array());
		$return = array();
		foreach ($all as $id => $item) {
			if (($type == "post_type" || $type == "all") && strstr($id, "post_type__")) {
			  $return["$id"] = $item;	
			
			}
			if (($type == "custom" || $type == "all") && !strstr($id, "post_type__")) {
			  $return["$id"] = $item;	
			
			}
			
		}
		return $return;
		
	}
	
	/**
     * Return position of tag within supplied html
     *
  	 * @param string $tag -- html tag name to search for
  	 * @param string $html -- html to search
  	 * @param string $required_attribute_name -- tag attribute that must exist
  	 * @param string $required_attribute_value -- tag attribute value that must exist
     *
     * @return integer the position of $tag within $search
     */	
	static function get_position_of_first_tag($tag, $html, $required_attribute_name = "", $required_attribute_value = "") {
		// first strip out comments as we do not want a tag that is found in comments
		$comment_pattern	= '/\<!--.*?--\>/s';
		$stripped_html		= preg_replace($comment_pattern, "", $html);
		
		// search for all occurences of $tag
		$pattern = '/<'.$tag.'(.*?)>/si';
		$matches = array();
    	preg_match_all($pattern, $stripped_html, $matches);
		
		foreach ($matches[0] as $matched_tag_html) {
			if ($required_attribute_name != "") {
				
				if ($required_attribute_value != "") {
					$attribute_pattern = "/{$required_attribute_name}=['\"]{$required_attribute_value}['\"]/i";	
				} else {
					$attribute_pattern = "/{$tag}/i";	
				}
				
			  	$matched_attributes = array();
			  	preg_match_all($attribute_pattern, $matched_tag_html, $matched_attributes);
				
				foreach ($matched_attributes[0] as $matched_attribute) {
					// if this a preload attribute, we also need to check for as='style' otherwise it could be an image
					// the match should only be a rel='stylesheet' or a rel='preload' as='style'
				
					if ($tag == "link" && $required_attribute_name == "rel" && strstr($matched_attribute, "preload")) {
						
						if (!strstr($matched_tag_html, "as='style'") && !strstr($matched_tag_html, 'as="style"')) {
							
							$matched_attribute = "";
							continue;
						} 
					
						break;
						
					} else {
					
						break;
					}

					
				}
				if ($matched_attribute != "") {
					break;
				}
			} else { 
			  break;
			}
		}
		
		if ($matched_tag_html == "") {
			return array(-1, "", $html);
		}
		
		// return the position of the fully matched tag html, within the originally specified html string
		// - this is done as we need to not return results within commented code, or we may be looking for
		// - a tag with a specific attribute, example: <link href='/somefile.css'> or <link rel='stylesheet'> or <script async>
		//return strpos($html, $matched_tag_html);
		$position = strpos($html, $matched_tag_html);
		return array($position, $matched_tag_html, $html);
			
	}
	
	
	static function strip_query_strings_from_css($css) {
		// strip query strings
		$pattern = "/url\(['\"]?(.*?)['\"]?\)/si";
		$matches = array();
		
		preg_match_all($pattern, $css, $matches);
			

		foreach ($matches[0] as $index => $find) {
		//	$page_critical_css .= "\n{$find}\n";
			$src = $matches[1]["$index"];
			
			if (strstr($src, "data:") !== false) {
				continue;
			} else if (strstr($src, "fonts.googleapis.com/css") !== false ) {
				continue;
			} else if (strstr($src, "sfgcdn.pegasaas.io/css") !== false ) {
				continue;
			}
			
			
			$replace_data = explode("?", $src);
			$replace = $replace_data[0];
			
			$css = str_replace($src, $replace, $css);
		}		
		
		return $css;
	}	
	
	
	
	static function get_critical_css($post = "", $resource_id = "") {
		global $pegasaas;
		
		if ($resource_id == "") {
			$resource_id = PegasaasUtils::get_object_id();
		}
		if ($post == "") {
			$post = $pegasaas->post;
		}

		
		
		
		$post_id = $post->ID;
		if ($_GET['pegasaas_debug'] == "1") {
			print "post_id={$post_id}\n";
			print "resource_id={$resource_id}\n";
		}
		
		if ($post->ID == "" && $resource_id != "") {
			$post = $pegasaas->utils->get_post_object($resource_id);
			if ($post->is_category) {
				if ($_GET['pegasaas_debug'] == "1") {
					print "YES is category of type {$post->category_post_type}\n";
				}
				$post->post_type = $post->category_post_type;
			} else if ($post->is_woocommerce_product_category) {
				if ($_GET['pegasaas_debug'] == "1") {
					print "YES is woocommece_product_category of type {$post->category_post_type}\n";
				}
				$post->post_type = $post->category_post_type;
			} else if ($post->is_woocommerce_product_tag) {
				if ($_GET['pegasaas_debug'] == "1") {
					print "YES is woocommece_product_tag of type {$post->category_post_type}\n";
				}
				$post->post_type = $post->category_post_type;
			}
			
			if ($post->ID == "") {
				@$post->ID = url_to_postid($resource_id);
			}		
		} else if ($post->ID == "" && $resource_id == "") {
			return array("css" => "");
		}


		$pegasaas->utils->log("get_critical_css resource_id {$resource_id}",  "cpcss");
		$pegasaas->utils->log("get_critical_css post->slug {$post->slug}  -- id {$post->ID}",  "cpcss");
		
			$debug_backtrace = debug_backtrace();
			$calling_file = explode("/", $debug_backtrace[0]['file']);
			$calling_file = array_pop($calling_file);
			$calling_function = $debug_backtrace[1]['function'];
			$calling_class = $debug_backtrace[1]['class'];
			$calling_line = $debug_backtrace[0]['line'];
			
			$debug_backtrace_string = "{$calling_file} ".($calling_class != "" ? $calling_class."->" : "")."{$calling_function}() line #{$calling_line}";

		$pegasaas->utils->log("get critical css calling function {$debug_backtrace_string}",  "cpcss");
		
		$critical_css_cache_filename = PEGASAAS_CACHE_FOLDER_PATH.$resource_id."/critical.css";
		
		if (file_exists($critical_css_cache_filename)) {
			$critical_css['type'] = "page_level";
			$when_built = filemtime($critical_css_cache_filename);
			$critical_css['css'] = "/* Critical CSS Snapshot By Pegasaas.com (".date("Y-m-d H:i:s", $when_built)." UTC) */ ".file_get_contents($critical_css_cache_filename);
			$critical_css['built'] = $when_built;
		}
		// check to see if page level critical path CSS exists and i
		// if it does, if it is not stale, then use it
		//$critical_css 			= PegasaasUtils::get_object_meta($resource_id, 'critical_css');
		
		if (is_array($critical_css) && array_key_exists("css", $critical_css) && strlen($critical_css['css']) > 0) {
			$page_level_css_exists = true;
			$critical_css['type'] = "page_level";
		}
		
		if (is_array($critical_css) && array_key_exists("built", $critical_css)) {
			$page_level_build_date = $critical_css['built']; 
		}
		
		if ($page_level_css_exists) {
			$pegasaas->utils->log("get_critical_css resource_id {$resource_id} Page Level CPCSS Exists",  "cpcss");
			// if it is stale, request fresh cpcss for this page
			if ($page_level_build_date < strtotime($post->post_date)) {
				$existing_requests 		= get_option("pegasaas_pending_critical_css_request", array());
				$existing_page_requests = array();
			
				// check to see if there is an existing queued request
				if (array_key_exists($resource_id, $existing_requests)) {
					$critical_css['queued'] = true;
				
				// if there is no existing queued request, then execute a request for critical path css
				} else {
					$critical_css['queued'] = PegasaasDeferment::request_critical_css("", $post, $resource_id);
				}			
				
				// rebuild
				$critical_css['status'] = 'Page Level CPCSS Being Rebuilt';
				$critical_css['skip'] 	= true;
			} else {
				$critical_css['skip'] 	= false;
			}
		
			
		// if it does not exist, does the post type cpcss exist
		} else {
			$pegasaas->utils->log("get_critical_css resource_id {$resource_id} Post Type CPCSS maybe?",  "cpcss");

		//	var_dump($post);
			if ($post->ID === 0 || $post->ID == get_option("page_on_front")) {
				$post_type = "home_page"; 
				$post_type_url = PegasaasAccelerator::get_home_url();
				
			} else {
				if ($post->post_type == "") {
					$post_type = get_post_type($post->ID);
				} else {
					$post_type = $post->post_type;
				}
				
				$post_type_obj = $pegasaas->utils->get_post_type_object($post_type);
				
				$post_type_url = get_permalink( $post_type_obj->ID );
				if (!strstr($post_type_url, $pegasaas->get_home_url())) {
					$post_type_url = $pegasaas->get_home_url()."/".$post_type_obj->post_name."/";
				}
				$post_type_url = str_replace($pegasaas->get_home_url(), "", $post_type_url);
		
			}
			
			$pegasaas->utils->log("get_critical_css resource_id {$resource_id} post type ({$post_type}) url = {$post_type_url}",  "cpcss");

			
			$critical_css 	= PegasaasUtils::get_object_meta("post_type__{$post_type}", "critical_css");
		

			if (is_array($critical_css) && array_key_exists("css", $critical_css) && strlen($critical_css['css']) > 0) {
				$post_type_css_exists = true;
				
				$critical_css['type'] = "post_type";
				
				
			} 
		

			if (is_array($critical_css) && array_key_exists("built", $critical_css)) {
				$post_type_build_date = $critical_css['built']; 
			}			
			
			// if this critical css is older than a month, then rebuild it
			$one_month = 2592000;
			if ($post_type_build_date < time() - $one_month) {
				$post_type_css_exists = false;
			}
			
			if ($post_type_css_exists) {
				
				$critical_css['skip'] = false;
				
			} else {
				$existing_requests 		= get_option("pegasaas_pending_critical_css_request", array());
				$existing_page_requests = array();
			
				// check to see if there is an existing queued request
				if (array_key_exists("post_type__".$post_type, $existing_requests)) {
					$critical_css['queued'] = true;
				
				// if there is no existing queued request, then execute a request for critical path css
				} else {
					$critical_css['queued'] = PegasaasDeferment::request_critical_css("", "", $post_type_url,  $post_type);
				}						
				
				
				$critical_css['skip'] = true;
				$critical_css['status'] = 'Post Type CPCSS Being Built';
				// request post type critical css
			}
		} 
		
		// if we are not skipping, then condition the CPCSS
		if (!$critical_css['skip']) {
			
			$critical_css['css'] = str_replace("\\'", "'", $critical_css['css']);
			$critical_css['css'] = str_replace('\"', '"', $critical_css['css']);
			$critical_css['css'] = str_replace("content:'\\\\f", "content:'\\f", $critical_css['css']);
			$critical_css['css'] = str_replace("content:'\\\\e", "content:'\\e", $critical_css['css']);
			
		}
		
		
		
		return $critical_css;

	}
	
	static function needs_global_cpcss() {
		return self::assert_global_cpcss(false) > 0;
	}
	
	static function assert_global_cpcss($submit_requests = true) {
		$debug = false;
		$post_types = PegasaasAccelerator::get_global_cpcss_types();
		
	
		$requests = 0;
		if ($debug) { print "<pre class='admin'>"; }
		if ($debug) {	var_dump($post_types); }
		$cpcss_map = get_option('pegasaas_critical_css', array());
		foreach ($post_types as $post_type) {
			$critical_css = PegasaasUtils::get_object_meta("post_type__".$post_type, "critical_css"); 
			
			
			if (is_array($critical_css) && array_key_exists("css", $critical_css) && strlen($critical_css['css']) > 0) {
	
				// handle the case of a cpcss request getting saved in the object, but not the overall map								
					if(!isset($cpcss_map["post_type__{$post_type}"]) ){								
						if (!$cpcss_map["post_type__{$post_type}"]) {
							$cpcss_map["post_type__{$post_type}"] = true;
							update_option('pegasaas_critical_css', $cpcss_map);
						}
					}
		
				
				
				
				
			} else {
				if ($debug) {  
					print "cpcss does not exist for $post_type\n";
					
				} 

				if (isset(PegasaasAccelerator::$settings['settings']['inject_critical_css']['global_cpcss_template_override']["$post_type"]) && PegasaasAccelerator::$settings['settings']['inject_critical_css']['global_cpcss_template_override']["$post_type"] != "") {
					$post_type_obj = get_post(PegasaasAccelerator::$settings['settings']['inject_critical_css']['global_cpcss_template_override']["$post_type"]);
					$post_type_url = get_permalink( $post_type_obj->ID );
				} else {
					$post_type_obj = PegasaasUtils::get_post_type_object($post_type);
					$post_type_url = get_permalink( $post_type_obj->ID );
				}
				if ($post_type == "home_page") {
					$post_type_url = PegasaasAccelerator::get_home_url()."/";
				} else	if (!strstr($post_type_url, PegasaasAccelerator::get_home_url())) {
					
					// we should skip if there is no "post name" -- this indicates that there is no existing post for this post type
					if ($post_type_obj->post_name == "") {
						continue; 	
					} else {
						$post_type_url = PegasaasAccelerator::get_home_url()."/".$post_type_obj->post_name."/";	
					}
					
				}
				
				$post_type_url = str_replace(PegasaasAccelerator::get_home_url(), "", $post_type_url);
				//print $post_type."=".$post_type_url."\n";
			
				if ($post_type_url != "") {
					$requests++;
					if ($debug) { 
						print "requesting cpcss for $post_type\n";

					} 
					if ($submit_requests) { 
						PegasaasDeferment::request_critical_css($buffer = "", $post = "", $post_type_url, $post_type, $oncomplete = "", $priority = true);
					}
				}
			}
			
		}
		if ($debug) { print "</pre>"; }
		return $requests;
		
		
	}
	

	static function defer_js($buffer) {
		global $pegasaas;
		
		// do not defer the render blocking js, if the site is currently building critical css
		if (self::$building_critical_css) {
			return $buffer;
		}	
		
		if (PegasaasAccelerator::$settings['settings']['inline_js_deferral']['status'] == 0) {
			return $buffer;
		}
		
		$elementor_handling     = false;
		$debug 					= false;
		$build_deferred_js 		= false;
		$render_storage 		= "file";
		$cdn 					= PegasaasAccelerator::$settings['settings']['cdn']['status'] == 1;
		$cache_server 			= PegasaasCache::get_cache_server(false, "js");
		$resource_id 			= PegasaasUtils::get_object_id();
		$now 					= time();
		$deferred_file_count 	= 0;
		$inline_block_count		= 0;
		$defer_render_blocking_js_status = 0;
		
		if (PegasaasAccelerator::$settings['settings']['cloudflare']['status'] == 1) {
			$cloudflare_instruction = "data-cfasync='false'";
		} else {
			$cloudflare_instruction = "";
		}	

		
		$page_level_settings 	= PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides"); 
		
		$defer_render_blocking_js_status = PegasaasUtils::get_feature_status("defer_render_blocking_js");
		
		
		// if the status is set to "default" then set it to the proper 
		if ($defer_render_blocking_js_status == 1) {
			$defer_render_blocking_js_status = 5;
		}
	//	$buffer .= "x".PegasaasAccelerator::$settings['settings']['defer_render_blocking_js']['override'];
		//$buffer .= "defer render_blocking js status: ".$defer_render_blocking_js_status."\n";
		if ($_GET['rebuild-js'] == "1") { 
	  		$build_deferred_js = true;
			$buffer .= "\n<!-- pegasaas://accelerator - should rebuild JS -->";	
		}
		
		$build_deferred_js = true; // as of November 13, 2018, we always build if we reach this function
		
		
	
		// postscribe is an ajax loader that prevents document.write and innerHTML writes from negatively affecting the page
		$scripts_containing_document_write = array();
		$scripts_containing_document_write[] = "platform.twitter.com/widgets.js";
		$scripts_containing_document_write[] = "adfarm.mediaplex.com/ad/js/";
		$scripts_containing_document_write[] = "calendarDateInput";
		//$scripts_containing_document_write[] = "adsbygoogle";
		$scripts_containing_document_write[] = "doubleclick.net";
		$scripts_containing_document_write[] = "test-document-write";
		$scripts_containing_document_write[] = "infusionsoft.com/app/form";
		

		$include_post_scribe 	= false;
		$post_scribe_count 		= 0;
		$post_scribe_this 		= false;
	
		// temporarily change html code in order to exclude IE specific comment conditionals from the deferal process
		$matches = array();
		$pattern = '/\<'.'!--\[if(.*?)\<'.'!\[endif/s';
		preg_match_all($pattern, $buffer, $matches);
	
		foreach ($matches[0] as $match_data) {
		  $find 		= $match_data;
		  $replace 		= str_replace("<script", "<iescript", $find);
		  $buffer = str_replace($find, $replace, $buffer);  
		}
		

		$deferred_js_record 	= PegasaasUtils::get_object_meta($resource_id, "deferred_js");


		$resource_path = $pegasaas->utils->strip_query_string($resource_id);
		$file = WP_CONTENT_DIR."/pegasaas-cache{$resource_path}deferred-js.js";
		

		if (!file_exists($file) || filesize($file) == 0) {
			$build_deferred_js = true;
		}
	
		/* rebuild deferred js if we have stale time, or no record */
		$post = get_page_by_path($resource_id, 'OBJECT', array("page","post"));
		
		if (($deferred_js_record['when_updated'] < strtotime($post->post_date) || $post->post_date == "") && !$pegasaas->building_critical_css) {
		  	$build_deferred_js = true; 
		}

		if ($build_deferred_js == true) {
	  		$deferred_js 	 = "";
	  		$end_deferred_js = "";
		}
		
		
		// pre-condition javascript in the event that there are scripts that should be delayed
		$special_deferment_scripts 	= array();
		$special_deferment_scripts[] = array('src' => 'bbb.org/inc/legacy.js', 'defer_type' => 'lazy-load', 'requires' => 'jQuery');

		// defer_type - lazy_load
		if (PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['status'] == 1 && PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['instagram_feed']) {
			$special_deferment_scripts[] = array('src' => '/instagram-feed/js/sb-instagram.min.js', 'defer_type' => 'lazy-load', 'requires' => 'jQuery');
		}
		if (PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['status'] == 1 && PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['thirstyaffiliates']) {
			$special_deferment_scripts[] = array('src' => '/thirstyaffiliates/js/app/ta.js', 'defer_type' => 'lazy-load', 'requires' => 'jQuery');
		}		
		if (PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['status'] == 1 && PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['jetpack_twitter']) {
			$special_deferment_scripts[] = array('src' => '/jetpack/_inc/build/twitter-timeline.min.js', 'defer_type' => 'lazy-load');
		}	
		if (PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['status'] == 1 && PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['jetpack_facebook']) {
			$special_deferment_scripts[] = array('src' => '/jetpack/_inc/build/facebook-embed.min.js', 'defer_type' => 'lazy-load', 'requires' => 'jQuery');
		}
		if (PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['status'] == 1 && PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['wordpress']) {
			$special_deferment_scripts[] = array('src' => 'comment-reply.min.js', 'defer_type' => 'lazy-load');
			$special_deferment_scripts[] = array('src' => 'wp-embed.js', 'defer_type' => 'lazy-load');
		}
		if (PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['status'] == 1 && PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['google_recaptcha']) {
			$special_deferment_scripts[] = array('src' => 'recaptcha/api.js', 'defer_type' => 'lazy-load');
		}

		
		$additional_scripts_to_lazy_load = explode("\n", trim(str_replace("\r", "", PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['additional_scripts'])));
		foreach ($additional_scripts_to_lazy_load as $script_to_lazy_load) {
			if ($script_to_lazy_load != "") { 
				$special_deferment_scripts[] = array('src' => $script_to_lazy_load, 'defer_type' => 'lazy-load'); 
			}
		}		
		
		
		//$special_deferment_scripts[] = array('src' => '/contact-form-7/includes/js/scripts.js', 'defer_type' => 'lazy-load', 'requires' => 'jQuery');
	
		
		
		$matches 		= array();
		$pattern 		= "/<script(.*?)>(.*?)<\/script>/si";
    	preg_match_all($pattern, $buffer, $matches);
	
		$src_pattern 	= "/\ssrc=['\"](.*?)['\"]/si";
		$have_src = false;

		// user defined exclude scripts
		$exclude_scripts = explode("\n", trim(str_replace("\r", "", PegasaasAccelerator::$settings['settings']['defer_render_blocking_js']['exclude_scripts'])));
		$exclude_scripts[] = "https://www.googleoptimize.com/optimize.js";

		$exclude_scripts[] = "https://www.google.com/recaptcha/api.js";
		//$exclude_scripts[] = "https://www.google.com/recaptcha/api.js";
		
		// 1 = defer all, with exceptions
		// 2 = defer none, with exceptions
		$inline_defer_method_version = 2;
		$jquery_signature = "";
		$jquery_migrate_signature = "";
		
		foreach ($matches[0] as $index => $find) {
			$match_src = array();
	  		preg_match($src_pattern, $matches[1]["$index"], $match_src);
	  		$src 	= $match_src[1];
	  		$script = $find;
		//	print "src = $src<br>";
			
			
			
				$defer_this_script = false;
				if ($src != "") {
					$defer_this_script = true;
					
					foreach ($exclude_scripts as $excluded_script) {
					
						if ($excluded_scripts != "" && strstr($src, $excluded_script)) { 
							
							$defer_this_script = false;
							continue;
						}
					}
					
					foreach ($special_deferment_scripts as $special_deferment_script) {

						if (strstr($src, $special_deferment_script['src'])) {
							
							// condition the tag
							$replace = $find;
							
							// do not strip arguments from recaptcha (they are used for callbacks)
							if (strstr($src, "recaptcha/api.js")) {
							    $stripped_src = $src;	
							} else {
								$stripped_src = $pegasaas->utils->strip_query_string($src);
							}
							$replace = str_replace($src, $stripped_src, $replace);
							$replace = str_replace("src=", "data-src=", $replace);
							if ($special_deferment_script['requires'] != "") {
								$replace = str_replace("<script ", "<script {$cloudflare_instruction} data-requires='{$special_deferment_script['requires']}' ", $replace);
							}
							$replace = str_replace("<script ", "<script {$cloudflare_instruction} data-pegasaas-defer-script='{$special_deferment_script['defer_type']}' ", $replace);
							
							$buffer  = str_replace($find, $replace, $buffer);
							
							// add the preload tag to the page
							//$preload_tag = "<link rel='preload' as='script' href='{$stripped_src}' />";
						//	$buffer = str_replace("</head>", "{$preload_tag}</head>", $buffer);
							
							$defer_this_script = false;
						}
					}
					
					
					
				} else {
					if (strstr($find, "id='pegasaas-lighthouse-ua-opt-out'") ||
						strstr($find, "id='pa-lighthouse-ua-opt-out'") ||
						strstr($find, "id='pegasaas-ua-opt-out'") ||
						strstr($find, "id='pa-ua-opt-out'")||
						strstr($find, "wpcp_css_disable_selection") ||
							strstr($find, "data-pegasaas-defer='1'") ||
							strstr($find, "data-no-defer='1'") ||
							strstr($find, "rs-plugin-settings-inline-css") ||
							strstr($find, "cli-blocker-script") ||
							strstr($find, "\$zoho=") ||
							strstr($find, "a,s,y,n,c,h,i,d,e") ||
						strstr($find, "/* pegasaas-inline */")  ) {
								// do not defer these scripts
						} else {
							$defer_this_script = true;
							$defer_inline_scripts_method_3_run = true;
						}
					
					
					
				}
				
				
				if (!$defer_this_script) {
					continue;
				} 
			
			
			
	  		$post_scribe_this = false;
	 	
			
			
		
			// we cannot load the google maps interface through proxy, it must be done directly
			// ensure that it is loaded defer (not asyncronously, as it may load after scripts that depend upon it)
			if (strstr($src, "//maps.google.com/maps/api") || strstr($src, "//maps.googleapis.com/maps/api")) {
				$replace = str_replace(" async ", " ", $find);
				
				
				if (strstr($src, "defer") !== true) {
					$replace = str_replace("<script ", "<script defer ", $find);
					$buffer = str_replace($find, $replace, $buffer);
					$find = $replace;
				}
				continue; 
			}
			
			// this script should be async -- even if it isn't, we should not include it in deferred js
			if (strstr($find, "https://www.google.com/recaptcha/api.js")) {
				if (strstr($find, "async") || strstr($find, "defer")) {
					
				} else {
					$replace = $find;
					$replace = str_replace("<script ", "<script defer ", $replace);
					$buffer  = str_replace($find, $replace, $buffer);
				}
				continue;
			} 
			
	
		
			
		  	// completely exclude any scripts that need to be immediately inline, but do not require any document.write
			if (strstr($src, "sitemeter.com") !== false) {
	    		continue;
				
			// if no src and no contents of script tag, leave it
	  		} else if ($src == "" && $matches[2]["{$index}"] == "") {
			   continue;	
			}

			// if there is async as an attribute in the script tag
			if (strstr($matches[1]["$index"], " async") !== false) {
				// if it is just in the src name, then ignore
				if (strstr($src, "async") !== false) {
					
				// otherwise, so long as it is not the mailmunch, then skip this script to include in deferred js
				} else if (strstr($src, "mailmunch") === false 		&& strstr($src, "intense.icon.min") === false) {
			  		
			  		continue;
				} 
			}
			
			if (strstr($matches[1]["$index"], "data-cfasync='false'") !== false) {
				if (strstr($src, "mailmunch") === false) {
					continue;
				}
			}		
	  

	  		foreach ($scripts_containing_document_write as $script_with_document_write) {
			//	print $script_with_document_write." =? $src \n<br>";
	    		if (strstr($src, $script_with_document_write) !== false) {
	      			$include_post_scribe = true;
	      			$post_scribe_this 	 = true;
	      			$post_scribe_count++;
	    		}
	  		}
	
	  		if ($post_scribe_this) {
				//print "yes postscribe $src";
	      		
				$find2 = str_replace("</script>", '<\/script>', $find);
	 	  	
				$ps_code = $matches[2]["{$index}"];
				$ps_code_replace = $ps_code;
				$ps_code_replace = preg_replace('/\/\/(.*)?/', '', $ps_code_replace);
				$ps_code_replace = preg_replace('/\/\*(.*)?\*//', '', $ps_code_replace);
				$find2 = str_replace($ps_code, $ps_code_replace, $find2);
				if (strstr($find2, '"') && strstr($find2, "'")) {
					$find2 = str_replace("'", '"', $find2);
				}
				if (strstr($find2, '"')) {
					$find2 = str_replace("<script", "<'+'script", $find2);
					$find2 = "'{$find2}'";
				} else {
					$find2 = str_replace("<script", '<"+"script', $find2);
					$find2 = '"'.$find2.'"';
				}
		  	 	
				
				
	      		$newfind = "<div id='numo-ps-{$post_scribe_count}'></div>";
	      		$buffer = str_replace($find, $newfind, $buffer);    
				
	      		$end_deferred_js = $end_deferred_js . 
					"
					function execute_pegasaas_postscribe_{$post_scribe_count}() { 
					postscribe('#numo-ps-{$post_scribe_count}', 
					{$find2}
					); 
					}
					setTimeout('execute_pegasaas_postscribe_{$post_scribe_count}()', 100);";
/*
$end_deferred_js = $end_deferred_js . 
					"alert('y'); jQuery(document).ready(function() { alert('x');setTimeout('execute_pegasaas_postscribe_{$post_scribe_count}()', 100); });
function execute_pegasaas_postscribe_{$post_scribe_count}() { postscribe('#numo-ps-{$post_scribe_count}', '{$find2}', {done: function() { console.info('script has been delivered');}}); }
";				
*/
				
	     
	  		} else {

	  
	    		
	
					// append the deferred_js variable with the code in the src tag
					if ($src == "") {
			 			if (strstr($find, "WebFontConfig")) {
			 			} else { 
							
			 				$inline_script = $matches[2]["{$index}"];
							$inline_script = str_replace(array("<![CDATA[", "]]>", "<p>", "</p>"), array("", "", "", ""), $inline_script);
							
							// in patch 1.3.0
							$inline_script = str_replace(array("//-->//>", "//--><!", "<!--"), array("", "", ""), $inline_script); // clear data
							$inline_script = str_replace(array('sfhover";}sfEls[i].onmouseout', '), "");}}}if (window.attachEvent) '), array('sfhover";};sfEls[i].onmouseout', '), "");}}};if (window.attachEvent) '), $inline_script); // fix missing ;
							
							// in patch 1.12.4
							$inline_script = str_replace(array("-->"), array(""), $inline_script); // clear data
							
							
							
							$replace = $find;
								if (strstr($replace, "pegasaas-inline") || strstr($replace, "window.googlesitekitAdminbar")) {
									continue;
								} else if (strstr($replace, "document.write")) {			
									continue;
	
								} else if (!$have_src) {
									continue;
								
								} else	if (strstr($replace, " type='text/javascript'")) {
									$replace = str_replace(" type='text/javascript'", " type='text/deferred-javascript'", $replace);
								
								} else if (strstr($replace, ' type="text/javascript"')) {
									$replace = str_replace(' type="text/javascript"', ' type="text/deferred-javascript"', $replace);
								
								} else if (!strstr($replace, ' type=')) {
									$replace = str_replace("<script", "<script type='text/deferred-javascript'", $replace);
								}
							
								$buffer  = str_replace($find, $replace, $buffer);
							
							
							
							
							
			 			}
						
					} else {
						$have_src = true;
							
							
							// don't do any thing if there is already an async or defer attribute in the script tag
							if (strstr($find, " async") || strstr($find, " defer")) {
								
								$replace = $find;
							} else {
								$replace = $find;
								/*
								if ((strstr($src, "Divi") || strstr($src, "divi") && $cdn && strstr($src, "pegasaas"))) {
									$src_replace = str_replace(".js", "---deferred.js", $src);
									$replace = str_replace($src, $src_replace, $replace);
								}
								*/

								$replace = str_replace("<script ", "<script {$cloudflare_instruction} defer ", $replace);
								$buffer  = str_replace($find, $replace, $buffer);
								
							}
							if (strstr($find, "elementor/assets/js/frontend-modules")) {
								$elementor_handling = true;
							}	

							

							
						if (preg_match('#/jquery\.js$#', $src) || strstr($src, '/jquery.min.js')
							   || strstr($find, " id='jquery-core-js'")) {
						
								$jquery_signature = $replace;
						} else if (strstr($src, "jquery-migrate.min.js") || strstr($find, " id='jquery-migrate-js'")) {		
							
								
								$jquery_migrate_signature = $replace;
							
							}
					}
				
			
	  		} // end of else (!post_scribe_this
		} // end of foreach
		

		if ($build_deferred_js) {

	 		if ($include_post_scribe) {
	   			$ps = PegasaasUtils::get_file_contents("https://cdnjs.cloudflare.com/ajax/libs/postscribe/2.0.8/postscribe.min.js")."\n\n";
	 		}
 			
			
			
	    	$deferred_js = $deferred_js . $ps. $end_deferred_js;
			$minified_js = $minified_js . $ps . $end_deferred_js;
			//$minified_js = $deferred_js;
			
			//$deferred_js .= "\nwindow.performance.mark('Deferred JS Loaded');\n";
			//$minified_js .= "\nwindow.performance.mark('Deferred JS Loaded');\n";
		
			
			$deferred_js_record['when_updated'] = time();
			$deferred_js_record['storage_method'] = $render_storage;

					
			
		} else { 
		}

		// load event handler 
		$deferral_script = "https://pegasaas.io/cache/0/d22.min.js"; 
		
		if ($jquery_migrate_signature != "") {
			$find = $jquery_migrate_signature;
			$replace = $find."\n<script {$cloudflare_instruction} defer type='text/javascript' src='{$deferral_script}'></script>";
			$buffer = str_replace($find, $replace, $buffer);
			
		} else if ($jquery_signature != "") {
			$find = $jquery_signature;
			$replace = $find."\n<script {$cloudflare_instruction} defer type='text/javascript' src='{$deferral_script}'></script>";
			$buffer = str_replace($find, $replace, $buffer);
			
		}
		
		
		$minified_js = str_replace("</script>", "&lt;/script&gt;", $minified_js);
		$deferred_js = str_replace("</script>", "&lt;/script&gt;", $deferred_js);
		$defer_inline_scripts_method_js = "";

		
		
		
		if ($defer_render_blocking_js_status == 5 || $defer_render_blocking_js_status == 4 || $defer_render_blocking_js_status == 2 || $defer_render_blocking_js_status == 1) {
			if (strlen($minified_js) > 0) {
				if ($defer_inline_scripts_method_3_run) {
					$buffer = str_replace("</body>", "<script {$cloudflare_instruction} type='text/javascript'>{$minified_js}</script></body>", $buffer);

				}
				//$buffer = str_replace("</body>", "<script {$cloudflare_instruction} type='text/javascript' defer src='{$href}'></script></body>", $buffer);
				//$buffer = str_replace("</head>", "<link rel='preload' as='script' href='{$href}'></head>", $buffer);
			}
		} else if ($defer_render_blocking_js_status > 0) {
			if (PegasaasAccelerator::$settings['settings']['minify_js']['status'] == "1") { 
				$buffer = str_replace("</body>", "<script {$cloudflare_instruction} type='text/javascript'>{$minified_js}\n<!-- minified --></script></body>", $buffer);
			} else { 
				$buffer = str_replace("</body>", "<script {$cloudflare_instruction} type='text/javascript'>{$deferred_js}\n<!-- not minified --></script></body>", $buffer);
			}
			
		}
		
	if ($defer_inline_scripts_method_3_run) {
				$defer_inline_scripts_method_js = "
	var all_scripts = document.getElementsByTagName('script');
	var script_count = 0;

	for (var i = 0; i < all_scripts.length; i++) {
		var script = all_scripts[i];

		if (script.getAttribute('defer') !== null) {

			script.addEventListener('load', function() {
				var all_scripts = document.getElementsByTagName('script');
				var script_id = this.getAttribute('data-script-id');
				var dependents 	= document.querySelectorAll('script[data-script-dependency=\"' + script_id + '\"]');
				for(var i = 0; i < dependents.length ; i++) {


					var script = dependents[i];
					var new_script = document.createElement('script');
					new_script.setAttribute('type', 'text/javascript');
					new_script.innerHTML = script.innerText;


					script.parentNode.insertBefore(new_script, script.nextSibling);
					script.removeAttribute('data-pegasaas-defer-script');
				}			


			});

			script_count = script_count + 1;
			script.setAttribute('data-script-id', script_count);          
		} else {

			if (script.getAttribute('type') == 'text/deferred-javascript' && (script.getAttribute('data-pegasaas-defer-script') != 'lazy-load')) {

				script.setAttribute('data-script-dependency', script_count);
			}
		}
	 }
	";
	   $buffer = str_replace("</body>", "<script {$cloudflare_instruction} type='text/javascript'>{$defer_inline_scripts_method_js}</script></body>", $buffer);
	}	
		
		
		if ($elementor_handling) {
			$buffer = str_replace("</body>", "<script>window.addEventListener('load', function() { 
		if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1){
		var evt = document.createEvent('Event');  
		evt.initEvent('resize', false, false);  
		window.dispatchEvent(evt);
	}
	});
</script>", $buffer);
		}		
		
		if ($debug) { $buffer .= "\n".$pegasaas->condition_comment_codes($pegasaas->utils->get_total_page_build_time(false, "defer_render_blocking_js:6")); }

		// restore IE conditional comment script to the HTML
		$buffer = str_replace("<iescript", "<script", $buffer);
		
	  	return $buffer;	
	}
	
	
	
	
	static function request_critical_css($buffer = "", $post = "", $url = "", $type = "", $on_complete = "", $priority = false) {
		
		global $post;
		global $pegasaas;
		
		$debug = false;
		
		if (!PegasaasUtils::memory_within_limits()) {
			return false;
		}
		
		if ($buffer == "") { 
			$return_buffer = false;
		} else {
			 
			$return_buffer = true;
		}
		if ($debug) {
			$return_buffer = true;
		}

		$pegasaas->utils->log("Request Critical CSS Post Slug {$post->slug} for ID {$post->ID} and url {$url}, type {$type}, priority {$priority}");

		// if no post was passed, then grab the one currently defined
		if ($type != "") {
			$extra = "requesting post type cpcc {$type}";
			$post = new stdClass();
			$post->slug = "post_type__{$type}";
			$post->resource_id = "post_type__{$type}";
		} else if ($post == "") {
			$post = $this->post;
			$extra = "was blank ".$post->ID;
			
			if ($post->slug == "" && $post->ID == "" && $url != "") {
		  		$post = $pegasaas->utils->get_post_object($url);	
			}
		} else {
			$extra = "was not blank";
		}
		
		if ($url == "") {
			$url = $_SERVER['REQUEST_URI'];
		}
		
		if ($url == $pegasaas->get_home_url()) {
			$url = "/";
		}
		$pegasaas->utils->log("Request Critical CSS url {$url}");
		if (strstr($url, "wp-admin")) {

			$pegasaas->utils->log("Request Critical CSS url wp-admin detected -- existing");
			return false;
		}		
		
		$calling_function		= debug_backtrace()[1]['function'];
		$calling_function_line	= debug_backtrace()[1]['line'];
		$pegasaas->utils->log("Request Critical CSS called by $calling_function $calling_function_line");
		
		
  		if (PegasaasAccelerator::$settings['api_key'] != "") {
			$api_key = PegasaasAccelerator::$settings['api_key'];
			
			$url = str_replace("?accelerator=off", "", $url);
			
			
			$request_id = md5($url.time());
			
			$location = $pegasaas->utils->get_wp_location();

			
			$post_fields = array();
			$post_fields['api_key'] = $api_key;
			$post_fields['domain'] 	= $_SERVER["HTTP_HOST"];
			$post_fields['url']		= ($_SERVER['HTTPS'] == "on" ? "https://" : "http://").$_SERVER['HTTP_HOST'].$url;
			$post_fields['url']		= PegasaasAccelerator::get_home_url().$url;
			$post_fields['version']	= 2;
			$post_fields['command']	= "submit-css-request";
			if ($priority) {
				$post_fields['priority'] = true;
			}
			$post_fields['callback_url'] = ($_SERVER['HTTPS'] == "on" ? "https://" : "http://").$_SERVER['HTTP_HOST'].$location."/wp-admin/admin-ajax.php?wp_rid=".$post->slug."&v=2"; 
			$post_fields['callback_url'] 		= admin_url( 'admin-ajax.php' )."?wp_rid=".$post->slug."&v=2"; 

			if ($debug) {
				var_dump($post_fields);
			}

		
			//send request and save response to variable
		
			$response = $pegasaas->api->post($post_fields);
			$api_key = PegasaasAccelerator::$settings['api_key'];
			$data = json_decode($response, true);

			if ($data['api_error'] != "") {
		
				$response = json_encode(array("status" => -2, "reason" => "Unable to connect to API Key Server"));
			} else {
				$buffer .= $response;
				$data = json_decode($response, true);
				
				if ($debug) {
					print "<pre class='admin'>";
					print $response;
					print "</pre>";
				}
				
				if ($data['css_request_status'] == 1) {

					$pending_request = array();
					$pending_request['request_id']  = $request_id;
					$pending_request['post_id'] 	= $post->ID;			//If you can get an Isset Error here, Tell us how you did it!!
					$pending_request['request_type'] 	= $type;
					$pegasaas->utils->log("CPCSS Request oncomplete is {$on_complete}");
					$pending_request['on_complete'] 	= $on_complete;
					$pending_request['when_requested'] = date("Y-m-d H:i:s");
					if ($pegasaas->utils->semaphore("pegasaas_pending_critical_css_request")) {
	
						$existing_requests = get_option("pegasaas_pending_critical_css_request", array());
						if (!array_key_exists($post->resource_id, $existing_requests)) {
							$existing_requests["{$post->resource_id}"] = array("requested" => time(), "extra" => $extra, "type" => $type, "on_complete" => $on_complete);
							update_option("pegasaas_pending_critical_css_request", $existing_requests);
						}
						
						$pegasaas->utils->release_semaphore("pegasaas_pending_critical_css_request");

						if ($return_buffer) {
							return $buffer;
						} else {							 
							return true;	
						}
					} else {
						return false;
					}
				} else {
					if ($return_buffer) {
						return $buffer;
					} else {
						return false;
					}
				}
			}			
		}
	}
	
	static function process_critical_css($css, $resource_id) { 
		global $pegasaas;
		PegasaasUtils::log("Processing CPCSS for {$resource_id}");
		$css = str_replace("&#039;", "'", $css);
		$css = str_replace("&#034;", '"', $css);
		$css = str_replace("&quot;", '"', $css);
		$css = str_replace("&lt;br&gt;", "", $css);
		$css = str_replace("&gt;", ">", $css);
		if (strstr($css, ".navbar-nav")) {
			$css .= ".navbar-nav > li > ul {display:none;}";
		}
		$is_post_type_cpcss = false;
		if ($pegasaas->utils->semaphore("pegasaas_critical_css")) {
			if ($pegasaas->utils->semaphore("pegasaas_pending_critical_css_request")) {
				if (true) {
					if ($pegasaas->utils->semaphore("pegasaas_deferred_js")) {
						$critical_css_records = get_option('pegasaas_critical_css', array());
						$critical_css_records["{$resource_id}"] = true;
						update_option("pegasaas_critical_css", $critical_css_records);
						$existing_requests = get_option('pegasaas_pending_critical_css_request', array());
						
						// do asnot store if this is a blank request 
						if (strstr($css, "[Blank]")) {
							$pegasaas->utils->log("Blank Critical CSS for {$resource_id} -- not stored.");
							
							if ($existing_requests["{$resource_id}"]['type'] != "") {
								$is_post_type_cpcss = true;
							}							
							
						} else {
							$critical_css = array("css" => $css, "built" => time());
							
							$object_storage_id = $resource_id;
							
							if ($existing_requests["{$resource_id}"]['type'] != "") {
								$is_post_type_cpcss = true;
								$object_storage_id = "post_type__".$existing_requests["{$resource_id}"]['type'];
							}
							$pegasaas->utils->log("Storing CPCSS for $resource_id / $object_storage_id");
							$pegasaas->utils->update_object_meta($object_storage_id, "critical_css", $critical_css);
							
							$pegasaas->utils->log("CPCSS Request completed.  oncomplete is: ".$existing_requests["{$resource_id}"]["on_complete"] );
							if ($existing_requests["{$resource_id}"]["on_complete"] != "") {
								if ($existing_requests["{$resource_id}"]["on_complete"] == "request_gpsi_score") {
									$_POST['resource_id'] = $resource_id;
									$_POST['api_key'] = PegasaasAccelerator::$settings['api_key'];
									$pegasaas->scanner->pegasaas_request_pagespeed_score($die = false);
								}
								
							}
						}
						
						unset($existing_requests["{$resource_id}"]);
						update_option('pegasaas_pending_critical_css_request', $existing_requests);
						
						$pegasaas->utils->release_semaphore("pegasaas_deferred_js");
					}
					
					
					$pegasaas->utils->release_semaphore("pegasaas_cache_map");
				
					
					// if this is not a post type critical path CSS, then make sure to clear the cache for this page
					// POSSIBLE UPGRADE: in the future, we may choose to clear the cache for any page that has the post type critical path css
					//if (!$is_post_type_cpcss && $resource_id != "") {
					//	$pegasaas->cache->clear_cache($resource_id);
					//	$pegasaas->utils->touch_url($resource_id);
					//}
					
				}
				
				$pegasaas->utils->release_semaphore("pegasaas_pending_critical_css_request");
			} else {
			
			}

			$pegasaas->utils->release_semaphore("pegasaas_critical_css");
		} else {
			
		}
				
		return $css;
	}
	
	static function fix_cpcss($page_critical_css) {
		$pattern = '/{(.*?)}/si';
		$matches = array();
		preg_match_all($pattern, $page_critical_css, $matches);
		
		foreach ($matches[1] as $index => $css_attributes) {
			
			if (strstr($css_attributes, '{')) {
				// handle the case of this being a media query
			} else {
				
				
				$background_images = 0;
				$background_repeats = 0;
				$items = explode(';', $css_attributes);
				
				foreach ($items as $item) {
					
					$item_data = explode(":", $item);
					if (strstr($item, "background-image")) {
						$background_images = substr_count($item, ',') + 1;
					//	print "MAYBE $item_data[1] -- $background_images\n";
					} else if ($item_data[0] == "background-repeat") {
						$background_repeats = substr_count($item_data[1], ',') + 1;
						$background_repeats_value = $item_data[1];
						//print "YES $background_repeats_value -- $background_repeats\n"; 
					}
				}
				if ($background_repeats > 0 && $background_images > $background_repeats) {
					$extra_repeats_required = $background_images - $background_repeats;
					if ($extra_repeats_required > 0) {
						$new_background_repeats_value = $background_repeats_value . str_repeat(",repeat", $extra_repeats_required);
						
						$replace = str_replace($background_repeats_value, $new_background_repeats_value, $css_attributes);
						$page_critical_css = str_replace($css_attributes, $replace, $page_critical_css);
						//print "have one and did one $new_background_repeats_value\n";
						
					}
				}
			}
		}
		return $page_critical_css;	
	}


	
	static function clear_critical_css_cache($object_id = "") {
		global $pegasaas;
		
		if ($object_id == "post_type") {
			$cpcss_type = "post_type";
			$object_id = "";
		} else if ($object_id == "custom") {
			$cpcss_type = "custom";
			$object_id = "";
		} else {
			$cpss_type = "any";
		}

		if ($object_id == "") {
			$critical_css_records = get_option('pegasaas_critical_css', array());
			
			if ($cpcss_type == "post_type") {
				
				
			} else {
				$all = PegasaasUtils::get_all_pages_and_posts();
				foreach ($all as $post) {
					$pegasaas->utils->delete_object_meta($post->resource_id, "critical_css");
				}	 
			}
			
			$new_critical_css_records_array = $critical_css_records;

			foreach ($critical_css_records as $resource_id => $active) {
				if ($cpcss_type == "post_type") {
					if (strstr($resource_id, "post_type__")) {
						$pegasaas->utils->delete_object_meta($resource_id, "critical_css");
						unset($new_critical_css_records_array["$resource_id"]);
					}


				} else if ($cpcss_type == "custom") {
					if (!strstr($resource_id, "post_type__")) {
						$pegasaas->utils->delete_object_meta($resource_id, "critical_css");
						unset($new_critical_css_records_array["$resource_id"]);
					}

				} else {
					$pegasaas->utils->delete_object_meta($resource_id, "critical_css");
					unset($new_critical_css_records_array["$resource_id"]);
				}
			}
	
			if (sizeof($new_critical_css_records_array) == 0) {
				delete_option('pegasaas_critical_css', array());
			} else {
				update_option('pegasaas_critical_css', $new_critical_css_records_array);
			}
		
			
		} else {
			$pegasaas->utils->delete_object_meta($object_id, "critical_css");
			$critical_css_records = get_option('pegasaas_critical_css', array());
			unset($critical_css_records["{$object_id}"]);
			update_option('pegasaas_critical_css', $critical_css_records);
			
		}
		self::$post_type_cpcss 	= PegasaasDeferment::get_critical_css_records("post_type");
		self::$page_level_cpcss = PegasaasDeferment::get_critical_css_records("custom");
	}	
	
	
}
endif;
?>