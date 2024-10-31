<?php
class PegasaasMinify {
	static function minify_html($buffer) {
		global $pegasaas;
		
		$page_level_settings = PegasaasUtils::get_object_meta(PegasaasUtils::get_object_id(), "accelerator_overrides");

		if ($_GET['accelerate'] != "off" && $_GET['build-cpcss'] != "yes" && 
			((PegasaasAccelerator::$settings['settings']['minify_html']['status'] == 1 && $page_level_settings['minify_html'] != "0") 
			 ||
			 (PegasaasAccelerator::$settings['settings']['minify_html']['status'] == 0 && $page_level_settings['minify_html'] == "1"))
			 ) { 
			
			if ($_GET['pegasaas_disable_minify_html'] == 1) {
				return $buffer;
			}
		} else { 
			return $buffer;
		}
		
		$temporary_scripts = array();
		$script_count = 0;
		// temporarily change script comment tags to preserve theme
		$pattern 		= "/<script(.*?)>(.*?)<\/script>/si";
		preg_match_all($pattern, $buffer, $matches);
	
		foreach ($matches[0] as $script_html) {
		  $temporary_scripts["{$script_count}"] = $script_html;
		  $buffer = str_replace($script_html, "[temp_script_{$script_count}]", $buffer);
		  $script_count++;
		}
		$buffer = str_replace("--!>", "-->", $buffer); // antiquated ending comment tag

		$pattern 		= "/<script(.*?)script>/si";
		preg_match_all($pattern, $buffer, $matches);
	
		foreach ($matches[0] as $script_html) {

		  $temporary_scripts["{$script_count}"] = $script_html;
		  $buffer = str_replace($script_html, "[temp_script_{$script_count}]", $buffer);
		  $script_count++;
		}

		$search = array(
			 '/\/\/ slideshow on \/ off/',       // tempus
			// '/[\s]\/\/(.*)/',       // remove comments // this is removed March 19, due to a conflict in HTML comments
			'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
			'/[^\S ]+\</s',  // strip whitespaces before tags, except space
			'/(\s)+/s'       // shorten multiple whitespace sequences
		);
	
		$replace = array(
								 '',
			//					 '',
			'>',
			'<',
			'\\1'
		);
	
		$buffer = preg_replace($search, $replace, $buffer);
		
		// replace IE conditional statements with temporary code
		$search = array( 
			'/\<!--\>/s',
			'/\<!--\<!\[endif\]--\>/s',		 

						'/\<!--\[if/s',
						'/\<!\[endif/s',
						'/\<!--\[endif\]--\>/s'
							  );
			$replace = array(
			'[!--]',
			'[!--[![endif]--]',		 
	 
			'[!--[if',
			'[![endif',
			'[!--[endif]--]'
				);
			  
					
		$buffer = preg_replace($search, $replace, $buffer);
	
		// strip out comments
		$search = '/\<!--.*?--\>/s';
		$buffer = preg_replace($search, "", $buffer);
		
		
		// replace tempoary code and brinb back IE conditional statements
		$search = array( 
			'/\[!--\]/s',
			'/\[!--\[!\[endif\]--\]/s',		
			'/\[!--\[if/s',
						'/\[!\[endif/s',
						'/\[!--\[endif\]--\]/s'
							  );
			$replace = array(
			'<!-->',
			'<!--<![endif]-->',		 
			'<!--[if',
			'<![endif',
			'<!--[endif]-->'
				);
				
		$buffer = preg_replace($search, $replace, $buffer);
		
		$pattern 		= "/\[temp_script_(.*?)\]/si";
		preg_match_all($pattern, $buffer, $matches);
		
		foreach ($temporary_scripts as $i => $script_html) {
		 
		 $buffer = str_replace("[temp_script_{$i}]", $script_html, $buffer);
		}
		
		
		// when minified, this extra line causes js to break
		$buffer = str_replace("// jQuery('#slider').nivoSlider({ controlNavThumbs:true });", "", $buffer);
		

	  	$buffer = str_replace("wa=/["."script|<"."style|<"."link", "wa=/<"."script|<"."style|<"."link", $buffer);
		return $buffer;
		
		
	}	
	

	static function minify_css($css) {
		global $pegasaas;
		
		// hard code background urls appropriately
		$pattern = "/url\((.*?)\)/";
	
		$css = preg_replace_callback($pattern, 'PegasaasAccelerator::fix_url_pattern', $css);
		
		if (PegasaasAccelerator::$settings['settings']['remove_query_strings']['status'] != 0) {
			$css = PegasaasDeferment::strip_query_strings_from_css($css);
		}
		
		//$css = "NOPE".$css;
		$css = preg_replace('!/\*.*?\*/!s','', $css);
		$css = preg_replace('/\n\s*\n/',"\n", $css);
		
		// space
		$css = preg_replace('/[\n\r \t]/',' ', $css);
		$css = preg_replace('/ +/',' ', $css);
		$css = preg_replace('/ ?([,:;{}]) ?/','$1',$css);
		
		// trailing;
		$css = preg_replace('/;}/','}',$css);
		
		// fix unescaped slash
		$css = str_replace('content:"\\"}','content:"\\\\"}',$css);

		return $css;
		
	}		
	
	static function apply_css_minify_urls($page_html) { 
		global $pegasaas;
		
 		$pattern = "/<link(.*?)>/si";
		$matches = array();
    	preg_match_all($pattern, $page_html, $matches);
		$src_pattern = "/href=['\"](.*?)['\"]/si";
		$rel_pattern = "/rel=['\"](.*?)['\"]/si";
		$filename_pattern = '/post-([\d]*?)\.css/si';

		$cdn = PegasaasAccelerator::$settings['settings']['cdn']['status'] == 1;
		
		$cache_server = PegasaasCache::get_cache_server(false, "css");

		//$page_html .= "applying css minify urls";
		foreach ($matches[0] as $find) {  
	  		$match_src = array();
			$match_rel = array();
			$match_filename = array();
			
	  		preg_match($src_pattern, $find, $match_src);
			preg_match($rel_pattern, $find, $match_rel);
			
			$rel 	= $match_rel[1];
			if ($rel != "stylesheet") { 
				continue;
			} 
		
	  		$src = $match_src[1];
			preg_match($filename_pattern, $src, $match_filename);

			
			$file_extension = PegasaasUtils::get_file_extension($src);

			$replace = $find;
			
			// offsite resource
			if (strstr($src, "//") && !strstr($src, $_SERVER['HTTP_HOST'])) {
				continue;
				
			// not a css file
			} else if ($file_extension != "css") {	
				continue;
				
			// do not minify combined css
			} else if (strstr($src, "combined.css") && strstr($src, "wp-content/pegasaas-cache/")) {
				continue;
			
			// needed to not push through elementor as the local cache was not clearing on a regular basis, maybe to do with cloudflare
			} else if (strstr($src, "elementor/css/")) {	
				$replace = str_replace($src, $src."?".time(), $replace);
				$page_html = str_replace($find, $replace, $page_html);
				continue;
						
			} else if ( strstr($src, "_static/")) {
				continue;	
				
			} else if (strstr($src, "/mmr/")) {
				continue;

			} else if (strstr($src, "optimole")) {
				continue;	

			// resource on this server	
			} else if (strstr($src, $_SERVER['HTTP_HOST'])) {
				
				$domain_pattern = "/(http:\/\/|https:\/\/|\/\/)(.*?)\//";
				preg_match($domain_pattern, $src, $domain_match);
			 
				if ($cdn) {
					$replace 		= str_replace($domain_match[0], $cache_server, $replace);
				} else {
					// we are now processing .min.css as of 3.1.15 so as to strip CSS
					if (strstr($src, ".min.css") && false) {
						
					// only minify those resources which are not already previously minified 
					} else {
						$content_path 	= str_replace($pegasaas->get_home_url(), "", content_url());
						//$content_path 	= $this->utils->get_content_folder_path();
						$cache_folder 	= trim($content_path, '/')."/pegasaas-cache/";
						
						
						$replace 		= str_replace($domain_match[0], $domain_match[0].$cache_folder, $replace);

						
						$pegasaas->assert_local_minified_resource($src);
					}
				}
				if (strstr($replace, "pegasaas-cache/wp-content/uploads/elementor/css/")) {
					$replace = str_replace(".css", ".css?".time(), $replace);
				}
				
				$page_html = str_replace($find, $replace, $page_html);
			} else {
				if (substr($src, 0, 1) == "/") {  
					
			    	$src_replace = $cache_server.ltrim($src, "/");
					
				} else {
			    	$src_replace = $cache_server.$pegasaas->utils->map_resource_path($src, true);
				}
				$replace = str_replace($src, $src_replace, $replace);
				$page_html = str_replace($find, $replace, $page_html);
			}
		}
		return $page_html;
	}		
	
	static function apply_js_minify_urls($page_html) {
		global $pegasaas;
		
 		$pattern = "/<script(.*?)>/si";
		$matches = array();
    	preg_match_all($pattern, $page_html, $matches);
		$src_pattern = "/src=['\"](.*?)['\"]/si";
		$cdn = PegasaasAccelerator::$settings['settings']['cdn']['status'] == 1;

		$cache_server = PegasaasCache::get_cache_server(false, "js");

		
		foreach ($matches[0] as $find) {  
		
	  		$match_src = array();
	  		preg_match($src_pattern, $find, $match_src);
			$src = $match_src[1];
			$replace = $find;
			$file_extension = PegasaasUtils::get_file_extension($src);

			if (strstr($src, "/!/deferred-js") !== false && strstr($src, "/!djs")) {
				continue;
			
			// not a css file
			} else if ($file_extension != "js") {	
				
				continue;
			} else if ( strstr($src, "_static/")) {
				continue;
				
			} else if (strstr($src, "optimole")) {
				continue;	

			// do not minify merge, minify, refresh
			} else if (strstr($src, "/mmr/")) {
				continue;
			}
		
			if (strstr($src, $_SERVER['HTTP_HOST'])) {
				$domain_pattern = "/(http:\/\/|https:\/\/|\/\/)(.*?)\//";
				preg_match($domain_pattern, $src, $domain_match);

				
				//$replace = str_replace($domain_match[0], $cache_server, $replace);
			
				if ($cdn) {
					$src_replace = $pegasaas->utils->strip_query_string($src);
					$replace = str_replace($src, $src_replace, $replace);
					$replace 		= str_replace($domain_match[0], $cache_server, $replace);
				
					
					$page_html = str_replace($find, $replace, $page_html);
				} else {
					// we are now processing .min.css as of 3.1.15 so as to strip CSS
					if (strstr($src, ".min.js") && false) {
						
					// only minify those resources which are not already previously minified 
					} else {
					
						$content_path 	= trim(str_replace($pegasaas->get_home_url(), "", content_url()), '/');

						$cache_folder 	= PEGASAAS_CACHE_FOLDER;
						
						if (!strstr($src, $cache_folder)) {
							$replace 		= str_replace($domain_match[0], $domain_match[0].ltrim($cache_folder, "/")."/", $replace);
							
							$pegasaas->assert_local_minified_resource($src);
	
							$page_html = str_replace($find, $replace, $page_html);
						}
						
					}
				}	
				
				
				
			} else {
				if (substr($src, 0, 7) == "http://" || substr($src, 0, 8) == "https://" || substr($src, 0, 2) == "//") {
					continue;
				}  
				if (substr($src, 0, 1) == "/") {  
					
			    	$src_replace = $cache_server.ltrim($src, "/");
					
				} else {
			    	$src_replace = $cache_server.$pegasaas->utils->map_resource_path($src, true);
				}
				$replace = str_replace($src, $src_replace, $replace);
				$page_html = str_replace($find, $replace, $page_html);
			}

		}
		return $page_html;
	}	
	
	
	/* this is an expensive function call */
	/* during testing, given previous page excution being about 100ms, the call to JSMin::minify takes an additional 80ms */
	static function minify_js($js, $buffer = "", $compression_mode = 1) {
		global $pegasaas;
		$debug = $buffer != "";

		if ($debug) { $buffer .= "\n".$pegasaas->condition_comment_codes($pegasaas->utils->get_total_page_build_time(false, "minify_js:1")); }

		require_once PEGASAAS_ACCELERATOR_DIR."assets/jsmin/jsmin.php";
		if ($debug) { $buffer .= "\n".$pegasaas->condition_comment_codes($pegasaas->utils->get_total_page_build_time(false, "minify_js:2")); }
		
		if ($compression_mode == 1) {
			$js = str_replace("- ++", "-PEGASAASREQUIREDSPACE++", $js);
			$js = str_replace("- --", "-PEGASAASREQUIREDSPACE--", $js);
			$js = str_replace("+ --", "+PEGASAASREQUIREDSPACE--", $js);
			$js = str_replace("+ ++", "+PEGASAASREQUIREDSPACE++", $js);
			$js = str_replace("+ +", "+PEGASAASREQUIREDSPACE+", $js);
			$js = str_replace("+ -", "+PEGASAASREQUIREDSPACE-", $js);
			$js = str_replace("- -", "-PEGASAASREQUIREDSPACE-", $js);
			$js = str_replace("- +", "-PEGASAASREQUIREDSPACE+", $js);
			$js = str_replace('\/', "PEGASAAS_ESCAPED_SLASH", $js);

		
	
			if ($debug) { $buffer .= "\n".$pegasaas->condition_comment_codes($pegasaas->utils->get_total_page_build_time(false, "minify_js:3")); }
			try {
				$minified_js = JSMin_X::minify($js);
			} catch (JSMinException $e) {
				$minified_js = $js;
			}
			$minified_js = str_replace("PEGASAASREQUIREDSPACE", " ", $minified_js);
			$minified_js = str_replace('PEGASAAS_ESCAPED_SLASH', '\/', $minified_js);

		} else if ($compression_mode == 2) {
			$minified_js = $js;
			$minified_js = preg_replace('!/\*.*?\*/!s','', $minified_js);
			
			$minified_js = preg_replace('/\n\s*\n/',"\n", $minified_js);	
			$minified_js = str_replace("\n\n", "", $minified_js);
			$minified_js = trim($minified_js);
		} else {
			
		}
		

		if ($debug) { $buffer .= "\n".$pegasaas->condition_comment_codes($pegasaas->utils->get_total_page_build_time(false, "minify_js:4")); }

		if ($debug) {
			return array("minified_js" => $minified_js, "buffer" => $buffer);
		} else {
			return $minified_js;
		}
	}		
}
?>