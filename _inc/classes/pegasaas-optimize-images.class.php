<?php
class PegasaasImages {
	static function apply_optimized_img_url($page_html) { 
		global $pegasaas;
		
		$page_level_settings = PegasaasUtils::get_object_meta(PegasaasUtils::get_object_id(), "accelerator_overrides");


		$excluded_images = explode("\n", trim(str_replace("\r", "", PegasaasAccelerator::$settings['settings']['image_optimization']['exclude_images'])));

		
		$server_domain 	= str_replace("www.", "", $_SERVER['HTTP_HOST']);
		$cdn 			= PegasaasAccelerator::$settings['settings']['cdn']['status'] == 1;
		
		if (PegasaasAccelerator::$settings['settings']['basic_image_optimization']['status'] == 1) {
			$cache_server 	= $pegasaas->get_home_url()."/wp-content/pegasaas-cache/";
			$cache_server = content_url()."/pegasaas-cache/";
		} else {
			if (!$cdn) {
				$cache_server = $pegasaas->get_home_url()."/wp-content/pegasaas-cache/";
				$cache_server = content_url()."/pegasaas-cache/";
			} else {
				$cache_server 	= PegasaasCache::get_cache_server(PegasaasAccelerator::$settings['settings']['webp_images']['status'] == 1, "img");
			}
		}
		
		/* Apply CDN URL to any foreground images within the HTML */
 		$image_tag_pattern 	= "/<(img|input|link)(.*?)>/si";
		$input_tag_pattern 	= "/<input(.*?)>/si";
		$src_pattern 		= "/src=['\"](.*?)['\"]/si";
		$srcset_pattern 	= "/srcset=['\"](.*?)['\"]/si";
			$width_pattern 		= "/width=['\"](.*?)['\"]/si";
		$height_pattern 	= "/height=['\"](.*?)['\"]/si";

		$rel_pattern 		= "/rel=['\"](.*?)['\"]/si";
		$href_pattern 		= "/href=['\"](.*?)['\"]/si";
		$matches 			= array();

		$auto_size_images = isset(PegasaasAccelerator::$settings['settings']['auto_size_images']) && PegasaasAccelerator::$settings['settings']['auto_size_images'] == "" ? 1 : PegasaasAccelerator::$settings['settings']['image_optimization']['auto_size_images']; // enabled by default
	//	$auto_size_images = true; 
		
    	preg_match_all($image_tag_pattern, $page_html, $matches);
		
		// iterate through the matches
		foreach ($matches[0] as $index => $find) {  
	  		$replace 	= $find;
			$match_src = array();
			$match_srcset = array();
			
			
			$match_rel = array();
			preg_match($rel_pattern, $find, $match_rel);
	    	$rel = $match_rel[1];					
			
			$tag_type = $matches[1]["$index"];
			if ($tag_type == "link" && ($rel == "icon" || $rel == "apple-touch-icon-precomposed" || $rel == "shortcut icon")) {
				
				preg_match($href_pattern, $find, $match_src);
				
			} else {
				preg_match($src_pattern, $find, $match_src);
				
			}
			
	  		
			$domain_match = array();
	  		$src 		= $match_src[1];
			$src_name_value = $match_src[0];
			$src_name_value_replace = $src_name_value;
			
		  	preg_match($srcset_pattern, $find, $match_srcset);
	  		$srcset 		= $match_srcset[1];
			
		
			$match_width = array();
	    	preg_match($width_pattern, $find, $match_width);
	    	$width = $match_width[1];

			if (strstr($width, "%")) {
				$width = "";
			}
			$width = str_replace("px", "", $width);
			if ($width == "0") {
				$width = "";
			}
			
			if (!is_numeric($width)) {
				$width = "";
			} else if ($width != "") {
				$width = floor($width);
			}			
			
			
			
			$match_height = array();
			preg_match($height_pattern, $find, $match_height);
	    	$height = $match_height[1];		
			if (strstr($height, "%")) {
				$height = ""; 
			}
			$height = str_replace("px", "", $height);
			if ($height == "0") {
				$height = "";
			}
			if (!is_numeric($height)) {
				$height = "";
			} else if ($height != "") {
				$height = floor($height);
			} 
		

			if (strstr($src, "thumb.php") !== false) {
				$src_data = explode("thumb.php?", $src);
				$query_string = $src_data[1];
				$query_string = str_replace("&amp;", "&", $query_string);
				$thumbnail_data = array();
				parse_str($query_string, $thumbnail_data);

				$width = $thumbnail_data['w'];
				$height = $thumbnail_data['h'];

				$original_src = $src;
			
				$src 	 = str_replace("|", "/", $thumbnail_data['src']);
				$find 	 = str_replace($original_src, $src, $find);
				$replace = $find;
				$src_name_value = str_replace($original_src, $src, $src_name_value);
				$src_name_value_replace = $src_name_value;
				
				// replace the original full path of the thumbnail request to the updated src
				$page_html = str_replace($original_src, $src, $page_html);

			}
	
			// do not attempt to optimize an inline encoded image
			if (strstr($src, "data:image/")) {
				continue;
			}
			
			$file_extension = PegasaasUtils::get_file_extension($src);
			
			
			if ($file_extension == "gif" || $file_extension == "bmp") {
				
				continue;
			} else if (PegasaasUtils::is_in_list($src, $excluded_images)) {
					continue;	
			} else if ($file_extension == "svg") {
				
				continue;	

			// do not optimized external images
			} else if (strstr($src, "wp.com")) {
				continue;
				
			// optimole uses their own CDN, but they also have the host name of the origin server in the url,
			// so we must exclude optimole served images here.
			} else if (strstr($src, "optimole.com")) {
				continue;
	 		
			} else if (strstr($src, "facebook.com")) {
				   continue;
			} else if (substr($src, 0, 1) == "#") {
				//  print "have a # url<br>";
				  continue;
			  
			 } else if (strstr($src, "pegasaas.io")) {
				   continue;	
				
				
				
			// if this is a local URI, then we would change it
			} else if (strstr($src, $_SERVER['HTTP_HOST'])) {
			
				if (!strstr($src, $cache_server)) {
					
					$domain_pattern = "/(http:\/\/|https:\/\/|\/\/)(.*?)\//";
					preg_match($domain_pattern, $src, $domain_match);
			
					$src_name_value_replace 		= str_replace($domain_match[0], $cache_server, $src_name_value_replace);

					if ($file_extension != "gif" && $width != "" && $height != "" && $file_extension != "svg" && 
					!isset(PegasaasAccelerator::$settings['settings']['basic_image_optimization']['status']) &&
					$auto_size_images 
					) {
						$src_name_value_replace = str_replace(".{$file_extension}", "---{$width}x{$height}.{$file_extension}", $src_name_value_replace);
					
						if (strstr($rel, "crop-height-middle--") !== false) {
							$src_name_value_replace = str_replace(".{$file_extension}", "---{$rel}.{$file_extension}", $src_name_value_replace);
						}
					}					
				
					$replace = str_replace($src_name_value, $src_name_value_replace, $replace);
			
					$srcset_replace = preg_replace($domain_pattern, $cache_server, $srcset);
					
					if (!$cdn) {
						$inject_pegasaas_resolve_broken_image_js = true;
						
						if (strstr($replace, "onerror")) {
							$replace = str_replace("onerror='", "onerror='pegasaas_resolve_broken_image(this);", $replace);
							$replace = str_replace('onerror="', 'onerror="pegasaas_resolve_broken_image(this);', $replace);

						} else {
							$replace = str_replace("<img ", "<img onerror='pegasaas_resolve_broken_image(this);' ", $replace);
						}
					}	
				
					$replace = str_replace($srcset, $srcset_replace, $replace);
				
					$page_html 		= str_replace($find, $replace, $page_html);
				}
			
			} else if (substr($src, 0, 7) == "http://" || substr($src, 0, 8) == "https://" || substr($src, 0, 2) == "//") {
			  // skip, as this would be an external image
				
			} else {
				
				$path 	= $match_src[1];

				if (strstr($path, "../")) {
					$count 	= substr_count($path, "../");
								
					for ($i = 0; $i < $count; $i++) {
						$find_string .= "../";
					}
					
					$file_name_path = explode("/", $_SERVER['REQUEST_URI']);
					
					// pop the script name out of the array
					array_pop($file_name_path);
			
					// get the number of folders
					$full_path_count = count($file_name_path);
					
					for ($i = $full_path_count - $count; $i < $full_path_count; $i++) {
					  array_pop($file_name_path);	
					}
					
					$file_name_path = implode("/", $file_name_path);
					
					$full_path = $referer_base.ltrim($file_name_path, "/");
					$path = str_replace($find_string, $full_path, $path);
					
				// if the path is an relative based on the root folder, then just append the root
				} else if (substr($path, 0, 1) == "/") {
				  $path  = $referer_base.ltrim( $path, "/");
				  
				// if the path is a relative somewhere below the CSS folder path, then map that (relative test)
				} else {
					$file_name_path = explode("/", $_SERVER['REQUEST_URI']);
					
					// pop the script name out of the array
					array_pop($file_name_path);
					$file_name_path = implode("/", $file_name_path);
					$path = $referer_base.ltrim($file_name_path, "/")."/".$path;
					
				}			  	

				
				$src_name_value_replace 		= str_replace($match_src[1], $cache_server.$path, $src_name_value_replace);

				if ($width != "" && $height != "" && !isset(PegasaasAccelerator::$settings['settings']['basic_image_optimization']['status']) && $auto_size_images ) {
					$src_name_value_replace = str_replace(".{$file_extension}", "---{$width}x{$height}.{$file_extension}", $src_name_value_replace);

				}					
				$domain_pattern = "/(http:\/\/|https:\/\/|\/\/)(.*?)\//";
				$replace = str_replace($src_name_value, $src_name_value_replace, $replace);
				$srcset_replace = preg_replace($domain_pattern, $cache_server, $srcset);
				
				if (!$cdn) {
					$inject_pegasaas_resolve_broken_image_js = true;
					
					if (strstr($replace, "onerror")) {
						$replace = str_replace("onerror='", "onerror='pegasaas_resolve_broken_image(this);", $replace);
						$replace = str_replace('onerror="', 'onerror="pegasaas_resolve_broken_image(this);', $replace);

					} else {
						$replace = str_replace("<img ", "<img onerror='pegasaas_resolve_broken_image(this);' ", $replace);
					}
				}

				$replace = str_replace($srcset, $srcset_replace, $replace);
				$page_html 		= str_replace($find, $replace, $page_html);

			}
		}
		
		
	    /* Apply CDN URL to any background images within the HTML */
		$bg_image_tag_pattern = "/[^\w]url\((.*?)\)/si";
 		
		$matches = array();
		$page_html = preg_replace_callback($bg_image_tag_pattern, 'PegasaasAccelerator::fix_url_pattern', $page_html);		 

 		

		$matches 				= array();
		$bg_image_tag_pattern 	= '/[\s]?url\([\'"]?(.*?)[\'"]?\)/si';
		preg_match_all($bg_image_tag_pattern, $page_html, $matches);
		
		// iterate through the matches
		foreach ($matches[0] as $i => $find) {  
		$src = $matches[1][$i];
			$src_name_value = $find;
			$src_name_value_replace = $find;
			
			$replace 	= $find;
			$domain_match = array();
			
			$file_extension = PegasaasUtils::get_file_extension($src);
			
		  if (strstr($src, "wp.com") !== false) {
			   continue;
			 // do not attempt to optimize images that are from facebook.com
		   } else if (strstr($src, "facebook.com")) {
			   continue;
		   } else if (strstr($src, "pegasaas.io")) {
			   continue;
			  
		   } else if (strstr($src, "optimole.com")) {
			  continue;	  
		   } else if ($file_extension != "png" && $file_extension != "jpg" && $file_extension != "jpeg") { 
			   continue;
		   } else if ($file_extension == "gif" || $file_extension == "bmp") {
				continue;
			  
			// do not attempt to optimize an inline encoded image
		  } else if (strstr($find, "data:image/")) {
				continue;
			}	else if (strstr($find, "pegasaas-cache/")) {
			  continue;
		  }		
			
			
			// if this is a local URI, then we would change it
			if (strstr($find, $server_domain)) {
				$domain_pattern = "/(http:\/\/|https:\/\/|\/\/)(.*?)\//";
				preg_match($domain_pattern, $find, $domain_match);
				$replace 		= str_replace($domain_match[0], $cache_server, $replace);
				$page_html 		= str_replace($find, $replace, $page_html);
			}
		}

		if ($inject_pegasaas_resolve_broken_image_js) {
			$pegasaas_resolve_broken_image_js = "function pegasaas_resolve_broken_image(image) {

                          console.log('[Pegasaas Accelerator WP] Missing optimized image (' + image.src + ' -- requesting on-the-fly optimization.');

                          var optimized_image = document.createElement('img');";
				if ($site_settings['ngnix_404_handling'] == 2 || $site_settings['nginx_404_handling'] == 2) {
					$pegasaas_resolve_broken_image_js .= "
					var image_file_extension = image.src.split('.').pop();
					optimized_image.setAttribute('src', image.src.replace('.'+image_file_extension, '.optimized-' + image_file_extension));";

				} else { 
					$pegasaas_resolve_broken_image_js .= "
					optimized_image.setAttribute('src', image.src + '-optimized');";
				}		

			$pegasaas_resolve_broken_image_js .= "
						 
						  optimized_image.setAttribute('height', '1');
						  optimized_image.setAttribute('width', '1');
						  document.body.appendChild(optimized_image);

						  image.onerror = '';
						  var original_src = image.src;
						  original_src = original_src.replace('/pegasaas-cache/wp-content/', '/');
						  console.log('[Pegasaas Accelerator WP] Swapping missing optimized image with original (' + original_src + ')');

						  image.src = original_src + '?' + Date.now();
						  
						  image.srcset = '';

                        }";
		//	$pegasaas_resolve_broken_image_js = \pegasaas\Minify::js($pegasaas_resolve_broken_image_js);
			$pegasaas_resolve_broken_image_js = "<script>/* pegasaas-inline */ {$pegasaas_resolve_broken_image_js}</script>";
			$page_html = str_replace("</head>", "{$pegasaas_resolve_broken_image_js}</head>", $page_html);
		}
		return $page_html;
	}

	static function apply_external_img_cdn_url($page_html) { 
		global $pegasaas;
	
		$settings = PegasaasAccelerator::$settings['settings'];
		$installation_id = $settings['installation_id'];

		$http_host =  $_SERVER['HTTP_HOST'];
		
		$server_domain 	= str_replace("www.", "", $_SERVER['HTTP_HOST']);
		$cdn 			= PegasaasAccelerator::$settings['settings']['cdn']['status'] == 1;
	
	
		$page_level_settings = PegasaasUtils::get_object_meta(PegasaasUtils::get_object_id(), "accelerator_overrides");


		
		
	
		

		
		if (!$cdn) {
			return $page_html;
		}
		
		
		if (PegasaasAccelerator::$settings['settings']['basic_image_optimization']['status'] == 1) {
			$cache_server 	= $pegasaas->get_home_url()."/wp-content/pegasaas-cache/";
			$cache_server = content_url()."/pegasaas-cache/";
		} else {
			if (!$cdn) {
				$cache_server = $pegasaas->get_home_url()."/wp-content/pegasaas-cache/";
				$cache_server = content_url()."/pegasaas-cache/";
			} else {
				$cache_server 	= PegasaasCache::get_cache_server(PegasaasAccelerator::$settings['settings']['webp_images']['status'] == 1, "img");
			}
		}
		

	//$buffer = "extneral".$buffer;
		/* Apply CDN URL to any foreground images within the HTML */
 		$image_tag_pattern 	= "/<(img|input)(.*?)>/si";
		$src_pattern 		= "/src=['\"](.*?)['\"]/si";
		$srcset_pattern 	= "/srcset=['\"](.*?)['\"]/si";
		$data_srcset_pattern 	= "/data-srcset=['\"](.*?)['\"]/si";
		$data_oi_pattern 	= "/data-oi=['\"](.*?)['\"]/si";
		$width_pattern 		= "/width=['\"](.*?)['\"]/si";
		$height_pattern 	= "/height=['\"](.*?)['\"]/si";
		$rel_pattern 		= "/rel=['\"](.*?)['\"]/si";
		$matches 			= array();
		
    	preg_match_all($image_tag_pattern, $page_html, $matches);
		//print "host is: $http_host<br>";
		
		// iterate through the matches
		foreach ($matches[0] as $find) { 
			
			
	  		$replace 	= $find;
			$match_src = array();
			$match_srcset = array(); 
			
	  		preg_match($src_pattern, $find, $match_src);
			$domain_match = array();
	  		$src 		= $match_src[1];
			$src_name_value = $match_src[0];
			$src_name_value_replace = $src_name_value;
			
		  	preg_match($srcset_pattern, $find, $match_srcset);
	  		$srcset 		= $match_srcset[1];
			
		
			$match_width = array();
	    	preg_match($width_pattern, $find, $match_width);
	    	$width = $match_width[1];

			$match_height = array();
			preg_match($height_pattern, $find, $match_height);
	    	$height = $match_height[1];		
			
			$match_rel = array();
			preg_match($rel_pattern, $find, $match_rel);
	    	$rel = $match_rel[1];		
			
			
			//print "src is: $src<br>";
	
			$file_extension = PegasaasUtils::get_file_extension($src);
			//$page_html .= "file extension: $file_extension <br>";
			 // do not attempt to optimize images that are optimized via wordpress.com by the Jetpack plugin
		   if (strstr($src, "wp.com") !== false) {
			  // $buffer .= "x";
			   continue;
			   
		   // do not attempt to optimize images that are from facebook.com
		   } else if (strstr($src, "facebook.com")) {
			   continue;
			   
		   } else if (strstr($src, "optimole.com")) {
			   continue;	
			   
	   		} else if (strstr($src, "pegasaas.io")) {
			   continue;				   
		   } else if (strstr($src, "exactdn.com")) {
			   continue;
		   
		   } else if ($file_extension != "png" && $file_extension != "jpg" && $file_extension != "jpeg") { 
			   continue;
		   } else if ($file_extension == "gif" || $file_extension == "bmp") {
				continue;
				
			// if this is a not a local URI, then we would change it
			} else if (strstr($src, $http_host)) {
				
				
			} else if (substr($src, 0, 7) == "http://" || substr($src, 0, 8) == "https://" || substr($src, 0, 2) == "//") {
			  
			   // skip, as this would be an external image
				$domain_pattern = "/(http:\/\/|https:\/\/|\/\/)(.*?)\//";
				preg_match($domain_pattern, $src, $domain_match);
			    if (substr($src, 0, 2) == "//") {
					$http_prefix = "https:";
				} else {
					$http_prefix = "";
				}
				$src_name_value_replace 		= str_replace($domain_match[0], $cache_server."!/external-img,{$http_prefix}".$domain_match[0], $src_name_value_replace);
			//	print "External Image Replace = {$src_name_value_replace}<br>";
				//$src_name_value_replace 		= "https://".$this->settings['installation_id'].".".PEGASAAS_ACCELERATOR_CACHE_SERVER."//!/external-img,".$src_name_value_replace;
				
				if ($width != "" && $height != "") {
					$src_name_value_replace = str_replace(".{$file_extension}", "---{$width}x{$height}.{$file_extension}", $src_name_value_replace);
					
					if (strstr($rel, "crop-height-middle--") !== false) {
						$src_name_value_replace = str_replace(".{$file_extension}", "---{$rel}.{$file_extension}", $src_name_value_replace);
						
					}
				}
				
				$replace = str_replace($src_name_value, $src_name_value_replace, $replace);
			
			
				
				$page_html 		= str_replace($find, $replace, $page_html);
			   
			} else {
				

			}
		}
		return $page_html;
	    /* Apply CDN URL to any background images within the HTML */
	//	$bg_image_tag_pattern = "/[\s]?url\((.*?)\)/si";
		$bg_image_tag_pattern = "/[^\w]url\((.*?)\)/si";
 		//$bg_image_tag_pattern   = '/url\([\'"]?(.*?)[\'"]?\)/si';
		$matches = array();
		if (true || $installation_id == 1800) {
			$bg_image_tag_pattern = "/[^\w](url\((.*?)\))/si";
			$page_html = preg_replace_callback($bg_image_tag_pattern, '\pegasaas\Utils::fix_url_pattern3', $page_html);	
		} else {
			$bg_image_tag_pattern = "/[^\w]url\((.*?)\)/si";
			$page_html = preg_replace_callback($bg_image_tag_pattern, '\pegasaas\Utils::fix_url_pattern', $page_html);		 
		}
		
		//print "<pre>";
		//print htmlentities($page_html);
		//print "</pre>";	
		
		if ($installation_id == 2167) {
			//return $page_html;
		}
 		$bg_image_tag_pattern 	= '/[^\w]url\([\'"]?(.*?)[\'"]?\)/si';

		$matches 				= array();
		
				$stripped_buffer = preg_replace("/<script(.*?)<\/script>/si", "[shouldhavebeenscript]", $page_html);

		preg_match_all($bg_image_tag_pattern, $stripped_buffer, $matches);
	//	preg_match_all($bg_image_tag_pattern, $page_html, $matches);

		// iterate through the matches
		foreach ($matches[0] as $i => $find) {
			$src = $matches[1][$i];
			$src_name_value = $find;
			$src_name_value_replace = $find;
			//$page_html .= "ss: $src\n";
			$file_extension = \pegasaas\Utils::get_file_extension($src);
		  if (strstr($src, "wp.com") !== false) {
			   continue;
			 // do not attempt to optimize images that are from facebook.com
		   } else if (strstr($src, "facebook.com")) {
			   continue;
		   } else if (strstr($src, "pegasaas.io")) {
			   continue;
		   } else if ($file_extension != "png" && $file_extension != "jpg" && $file_extension != "jpeg") { 
			   continue;
		   } else if ($file_extension == "gif" || $file_extension == "bmp") {
				continue;
				
			// if this is a not a local URI, then we would change it
			} else if (strstr($src, $http_host)) {
				
				
			} else if (substr($src, 0, 7) == "http://" || substr($src, 0, 8) == "https://" || substr($src, 0, 2) == "//") {
				$replace 	= $find;
				$domain_match = array();
				
 				// skip, as this would be an external image
				$domain_pattern = "/(http:\/\/|https:\/\/|\/\/)(.*?)\//";
				preg_match($domain_pattern, $src, $domain_match);
			
				$src_name_value_replace 		= str_replace($domain_match[0], $cache_server."!/external-img,".$domain_match[0], $src_name_value_replace);
			  $replace = str_replace($src_name_value, $src_name_value_replace, $replace);
				$page_html 		= str_replace($find, $replace, $page_html);
			}
		}

		return $page_html;
	}		
	
}
?>