<?php
class PegasaasOptimize {
	static $original_page_html = "";
	
	function __construct() { }
	
	
	
	/**
     * Parse the output buffer.  Is responsible for 
     * organization and optimization of the requested document.
     *
     * @param string $buffer
     *
     * @return string Updated output buffer
     */
	function sanitize_output($buffer = "", $test_submission = false) {
		global $pegasaas;
	
	

		$debug 		 = false;
		$resource_id = PegasaasUtils::get_object_id();
		$accelerated_pages = get_option("pegasaas_accelerated_pages", array());
		
		if ($_GET['pegasaas_debug'] == "test_error") {
			// This function does not exist, and will throw a fatal error.
			// The purpose of this call is to test the error handling capabilities
			$pegasaas->test_error_handling();
		}
	


		if (defined('WP_CLI') && WP_CLI === true) {
			return $buffer;
		}
		
		// if this is an amp page, do not condition
		if (function_exists("is_amp_endpoint") && is_amp_endpoint()) {
			
			
			if (isset(PegasaasAccelerator::$settings['settings']['accelerated_mobile_pages']) && PegasaasAccelerator::$settings['settings']['accelerated_mobile_pages']['status'] > 0) {
				$is_amp = true;
			} else { 
			
				$is_amp = true;
				return $buffer;
			}
		}
		
		// if this request is for the purpose of building critical css, then return the unconditioned buffer
		if (array_key_exists("build-cpcss", $_GET) && $_GET['build-cpcss'] == "yes") {
			return $buffer;
		}
		
		//$buffer .= "<!-- x y ".strlen($buffer)." -->\n";
		
		// if this request is the cron system, do not condition the buffer under any circumstances
		if ( defined( 'DOING_CRON' )) {
			$pegasaas->utils->log("Sanitize Output: Cron Detected, Exiting Early", "html_conditioning");
    		return $buffer;
		} else {
			$pegasaas->utils->log("Start Sanitizing Output:: {$_SERVER['REQUEST_URI']}", "html_conditioning");
			$pegasaas->utils->log("Size of Buffer ".strlen($buffer), "html_conditioning");
			$pegasaas->utils->log("Remote Address {$_SERVER['REMOTE_ADDR']}", "html_conditioning");
			$pegasaas->utils->log("User Agent {$_SERVER['HTTP_USER_AGENT']}", "html_conditioning");
			$pegasaas->utils->log("Script Filename {$_SERVER['SCRIPT_FILENAME']}", "html_conditioning");
		}
		
		// if the size of the buffer is zero, then this could be a cron job -- we should not continue attempting to condition the page
		if (strlen($buffer) == 0 && !array_key_exists('pegasaas_debug', $_GET)) {
			$pegasaas->utils->log("Sanitize Output: Detected Zero Length Buffer, Exiting Early", "html_conditioning");
			return $buffer;
		}
		
		// on the off chance that the function "is_user_logged_in" doesn't exist, then we should log this event
		if (!function_exists("is_user_logged_in")) {
			$pegasaas->utils->log("WordPress Core Function Does Not Exist: is_user_logged_in", "critical");
		}
		
		$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides");
		$bypass_staging_mode = false;
			
		// a situation where the plugin is not yet activated by way of activating a single page
		if (is_array($accelerated_pages) && sizeof($accelerated_pages) == 0 && !$test_submission) {
			header("X-Pegasaas-Message: Caching Disabled");
			header("X-Pegasaas-Reason: No Pages Accelerated");
			header("HTTP/1.0 201 No Pages Accelerated"); // required for Kinsta cache
			
			return $buffer;
		}
		
	 	if (isset($page_level_settings['staging_mode_page_is_live']) && $page_level_settings['staging_mode_page_is_live'] == 1) {
			$bypass_staging_mode = true;
		} 

		// check the content-type for this page -- if it is not an html response, then exit early and return un-optimized buffer
		if (function_exists('headers_list')) {
			$headers = headers_list();
			if (is_array($headers)) {
				foreach ($headers as $header) {
					$header = strtolower($header);
					if (strstr($header, "content-type:") && !strstr($header, "text/html")) {
						header("X-Pegasaas-Message: Bypass Optimization Non-HTML Content Type: ".$header);
						return $buffer;
					}
				}
			}
		}

		// if this page does not have an opening body or html tag, then we should exit, as it is possible this is 
		// a dynamically built JSON page (where the plugin developer did not specify JSON content-type), or a malformed page which shouldn't be transformed
		if (!strstr($buffer, "<body") || !strstr($buffer, "<html")) {
			header("X-Pegasaas-Message: Bypass Optimization No body or html tag found: ".$header);

			return $buffer;
		}

		if (strstr($_SERVER['REQUEST_URI'], "/!/external-css,") !== false) {
		  	return  $this->fetch_external_css();	
			
		} else if (strstr($_SERVER['REQUEST_URI'], "/!/external-js,") !== false) {
		  	return  $this->fetch_external_js();		

		
		} else if (strstr($resource_id, "/wp-json/") !== false ||
				   strstr($resource_id, "/wp-admin/") !== false ||
				   strstr($resource_id, "/wc-auth/") !== false ||
			       strstr($resource_id, "/xmlrpc.php") !== false ||
				   strstr($resource_id, "/feed/") !== false ||
				   strstr($resource_id, "/jm-ajax/") !== false
			   ) {
			return $buffer;	
			
		} else if ($pegasaas->is_on_excluded_page()) { 
			header("X-Pegasaas-Message: Optimization Not Performed");
			header("X-Pegasaas-Reason: On Excluded Page");
			header("HTTP/1.0 201 On Excluded Page"); // required for Kinsta cache
			return $buffer;

		} else if (!$test_submission && $pegasaas->query_args_exists() && !isset($_GET['pegasaas-debug']) ) { 
			
			
			header("X-Pegasaas-Message: Caching Disabled");
			header("X-Pegasaas-Reason: Query Args Existing");
			
			// if the crdbg=1 query argument exists, then call rail is trying to ping the script
			if (isset($_GET['crdbg'])) {
				// do not change the header message
			} else {
				header("HTTP/1.0 201 Query Args Exist"); // required for Kinsta cache otherwise Kinsta will cache the response
			}
			
			if ($pegasaas->in_development_mode() && !$bypass_staging_mode) {	
				header("X-Pegasaas-Reason-2: Staging Mode Enabled For This Page");
			} else {
	
				$save_cache = false;
				$buffer = $this->local_optimize($buffer, $save_cache);
				
			}
			
			return $buffer;
			
		} else if ($pegasaas->post_is_password_protected()) {
			header("X-Pegasaas-Message: Caching Disabled");
			header("X-Pegasaas-Reason: User On Password Protected Page");	
			header("HTTP/1.0 201 User On Password Protected Page"); // required for Kinsta cache
		
			//PegasaasAccelerator::$settings['settings']['defer_render_blocking_js']['override'] = 3;
			$save_cache = false;
			$buffer = $this->local_optimize($buffer, $save_cache);
			
			return $buffer;
	
		} else if ($pegasaas->is_on_ecommerce_page($buffer)) {
			header("X-Pegasaas-Message: Caching Disabled");
			header("X-Pegasaas-Reason: On E-commerce Page");
			header("HTTP/1.0 201 On E-commerce Page"); // required for Kinsta Cache
			
		//	PegasaasAccelerator::$settings['settings']['defer_render_blocking_js']['override'] = 3;
			$save_cache = false;
			$buffer = $this->local_optimize($buffer, $save_cache);
	
			return $buffer;
		
		} else if (class_exists("WP_eCommerce") && $pegasaas->cache->has_wpecommerce_cookie()) {
			header("X-Pegasaas-Message: Caching Disabled");
			header("X-Pegasaas-Reason: WP E-Commerce Session Exists");
			header("HTTP/1.0 201 WP E-Commerce Session Exists"); // required for Kinsta Cache
			
		//	PegasaasAccelerator::$settings['settings']['defer_render_blocking_js']['override'] = 3;
			$save_cache = false;
			$buffer = $this->local_optimize($buffer, $save_cache);

			return $buffer;
		
		} else if (function_exists("is_woocommerce") && $pegasaas->cache->has_woocommerce_cookie()) {
			header("X-Pegasaas-Message: Caching Disabled");
			header("X-Pegasaas-Reason: WooCommerce Session Exists");
			header("HTTP/1.0 201 WooCommerce Session Exists"); // required for Kinsta Cache
		
	//		PegasaasAccelerator::$settings['settings']['defer_render_blocking_js']['override'] = 3;
			$save_cache = false;
			$buffer = $this->local_optimize($buffer, $save_cache);
			
			return $buffer;
		
		} else if (function_exists("is_user_logged_in") && is_user_logged_in()) { 
			header("X-Pegasaas-Message: Caching Disabled");
			header("X-Pegasaas-Reason: User Logged In PO211");

			$save_cache = false;
			$buffer = $this->local_optimize($buffer, $save_cache);
			
			return $buffer;
			
		} else if (function_exists('is_404') && is_404() && !(PegasaasUtils::instapage_active() && PegasaasUtils::is_instapage_url())) {
			header("X-Pegasaas-Message: Caching Disabled");
			header("X-Pegasaas-Reason: 404 Response");
			return $buffer;	
		
		// if the page is the result of a POST/HEAD/PUT, then it could be dynamically generated and should not be cached	
		} else if ($_SERVER['REQUEST_METHOD'] != "GET") {
			header("X-Pegasaas-Message: Caching Disabled");
			header("X-Pegasaas-Reason: Non-GET Submission");
						
			$save_cache = false;
			$buffer = $this->local_optimize($buffer, $save_cache);
			
			return $buffer;
		} 
		
		if ($this->disable) {
			return $buffer;
		}
		
		
		$save_cache = true;
		$temp_cache = true;
		$extended_coverage = false;
		

		// if this is the home page, then record the in-page scripts, to be used with the lazy-loader
		if ($resource_id == "/") {
			$pegasaas->capture_in_page_scripts($buffer);
		}
		
		$original_page_html = $buffer;
		self::$original_page_html = $buffer."abc";
		$pegasaas->utils->log("strlen of origina page html: ".strlen(self::$original_page_html), "caching");
		$pegasaas->utils->log("$"."pegasas->cache->save_cached_copy: ".$pegasaas->cache->save_cached_copy, "caching");

		// do not apply optimizations to a temporary cache if we are in development mode, and are not bypassing the staging mode with
		// a page that is explicitly set as live
		if ($pegasaas->in_development_mode() && !$bypass_staging_mode) {
		
			// in development (staging) mode, if this page is set as STAGING, but the
			// request is to bypass the cache (meaning that we've attempted hit it with the 'optimize' mechanism),
			// then we should not "disable", but should also not save any temporary cache
			if ($_SERVER['HTTP_X_PEGASAAS_BYPASS_CACHE'] == 1) {
				
			
			// if this is a regular request, then we should simply just disable without applying any local transformations
			} else if (!$pegasaas->cache->save_cached_copy) {
				$this->disable = true; // so we don't request the optimization
			
			} else {
		
			}
		
		} else {
		
			// we cannot apply any HTML 4/5 optimizations to AMP HTML as it will break
			// validation.  All we can do is save the cache
			if ($test_submission) {
				$save_cache = false;
				
			} else if ($is_amp) {
				$save_cache = true;
				
			} else {
				
				if ($page_settings['accelerated'] == 0 || $page_settings['accelerated'] == "" || $page_settings['accelerated'] == false) {
					if (PegasaasAccelerator::$settings['settings']['coverage']['status'] == 1) {
						if (is_category() && (PegasaasAccelerator::$settings['settings']['coverage']['extended']['do_categories'] == 0 || 
											 PegasaasAccelerator::$settings['settings']['coverage']['extended']['do_categories'] == false)) {
							return $buffer;
						} else if (is_tag() && (PegasaasAccelerator::$settings['settings']['coverage']['extended']['do_tags'] == 0 || 
											 PegasaasAccelerator::$settings['settings']['coverage']['extended']['do_tags'] == false)) {
							return $buffer;
						} 
					}
				}
				
				$buffer = $this->local_optimize($buffer, $save_cache);
			}
			
			
		}
	

		$page_settings 		  = PegasaasUtils::get_object_meta(PegasaasUtils::get_object_id(), "accelerator_overrides");
		$paginated_page 	= is_paged();
		$do_paginated_pages	= PegasaasAccelerator::$settings['settings']['blog']['pagination_accelerated'] == 1;

		if ($test_submission) {
		
		} else if ($page_settings['accelerated'] == 1 || $page_settings['accelerated'] == true) {
			if (PegasaasAccelerator::$settings['settings']['coverage']['premium']['use_temporary_cache'] == 0) {
				$save_cache = false;
			}
			
		} else if ($paginated_page && $do_paginated_pages) {
			$this->disable = false;
			if (PegasaasAccelerator::$settings['settings']['coverage']['premium']['use_temporary_cache'] == 0) {
				$save_cache = false;
			}	
			
		} else if (PegasaasUtils::instapage_active() && PegasaasUtils::is_instapage_url()) {
			if (PegasaasAccelerator::$settings['settings']['coverage']['premium']['use_temporary_cache'] == 0) {
				$save_cache = false;
			}
						
		} else {
	
		
			
			$extended_coverage = true;
			if (PegasaasAccelerator::$settings['settings']['coverage']['status'] == 1 && PegasaasAccelerator::$settings['settings']['coverage']['extended']['use_cache'] == 0) {
				$save_cache = false;
				
			} else if (PegasaasAccelerator::$settings['settings']['coverage']['status'] == 0) {
				$save_cache = false;
		 		$this->disable = true; 
				
			}			
		}	
		/*
		$buffer .= PegasaasUtils::html_comment("REMOTE ADDR: " . $_SERVER['REMOTE_ADDR'] );
	
		$buffer .= PegasaasUtils::html_comment("LImits MOnthly Optimizzations Remaining: " . PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining'] );
		$buffer .= PegasaasUtils::html_comment("LImits MOnthly Optimizzations Remaining: " . PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining'] );
	
		$resource_id = PegasaasUtils::get_object_id();
		$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides", false);
		$buffer .= PegasaasUtils::html_comment("page level settings['accelerated']: " . $page_level_settings['accelerated'] );
		$buffer .= PegasaasUtils::html_comment("page level settings['accelerated']: " . $page_level_settings['accelerated'] );
	
		

		$buffer .= PegasaasUtils::html_comment("Can Submit: " . $this->can_request_api_optimization() );
		$buffer .= PegasaasUtils::html_comment("Disabled?: " . $this->disable );
		$buffer .= PegasaasUtils::html_comment("Test Submission: " . $test_submission );
		$buffer .= PegasaasUtils::html_comment("Is Amp: " . $is_amp );
		global $proxy_buffer;
		$buffer .= PegasaasUtils::html_comment("Proxy Buffer: " . $proxy_buffer );
		
*/
		if (($this->can_request_api_optimization() || $test_submission) && !$this->disable && !$is_amp) {
			$request_state = $this->optimize_via_api($original_page_html, $test_submission);
			

			if ($test_submission) {
				return $request_state;
			}
			
			if ($request_state == 1) { 
				
				header("X-Pegasaas-Message: Caching Disabled");
				header("X-Pegasaas-Reason: Pending Optimization");
				header("HTTP/1.0 201 Pending Optimization"); // required for Kinsta Cache
				if (!PegasaasUtils::should_strip_footer_comments()) {
					$buffer .= PegasaasUtils::html_comment("Page Optimization Queued");
				}

			} else if ($request_state == 2) { 
				header("X-Pegasaas-Message: Caching Disabled");
				header("X-Pegasaas-Reason: Optimization Already Queued");
				header("HTTP/1.0 201 Pending Optimization"); // required for Kinsta Cache
				if (!PegasaasUtils::should_strip_footer_comments()) {
					$buffer .= PegasaasUtils::html_comment("Page Optimization Already Queued");
				}
			
			} else if ($request_state == -1) { 
				header("X-Pegasaas-Message: Caching Disabled");
				header("X-Pegasaas-Reason: API Key Invalid");
				header("HTTP/1.0 201 Pending Optimization"); // required for Kinsta Cache
				if (!PegasaasUtils::should_strip_footer_comments()) {
					$buffer .= PegasaasUtils::html_comment("Page Optimization Not Submitted Due to Invalid API Key");		
				}	
			
			} else {
				header("X-Pegasaas-Message: Caching Disabled");
				header("X-Pegasaas-Reason: Pending Optimization/Failed");
				header("HTTP/1.0 201 Pending Optimization"); // required for Kinsta Cache
				if (!PegasaasUtils::should_strip_footer_comments()) {
					$buffer .= PegasaasUtils::html_comment("Page Optimization Submission Failed at API");
				}
				$save_cache = false;

			}
			
		} else {
			if (!PegasaasUtils::should_strip_footer_comments()) {
				if ($this->disable) {
					$buffer .= PegasaasUtils::html_comment("No Local Optimizations Performed");
				} else {
					$buffer .= PegasaasUtils::html_comment("Local Optimization Only");
				}
			}
			

		}
		
          
		
		// possibly due to caching being disabled
		// we should skip saving the page to the file system
		if (!$save_cache) {
	
			if (!PegasaasUtils::should_strip_footer_comments()) {
				$buffer .= PegasaasUtils::html_comment("Caching Disabled");
			}
			$pegasaas->utils->log("Caching Disabled PegasaasOptimize", "caching");

			
		// otherwise, we have a good signal, and the PegasaasCache::check_cache function indicated we should "save_cached_copy"
		// then save the cache
		} else if ($pegasaas->cache->save_cached_copy) { 
			$pegasaas->utils->log("Saving Cache PegasaasOptimize::358", "caching");

			$buffer = $pegasaas->cache->save_page_cache($buffer, "", "", $temp_cache, false, $extended_coverage);		
			
		// we have a "save cache" flag, but the PegasaasCache::check_cache indicated we should not save the cache
		} else {
			
			if ($pegasaas->in_diagnostic_mode()) { 
				header("X-Pegasaas-Message: Caching Disabled");
				header("X-Pegasaas-Reason: In Diagnostic Mode");	
			} else if (($pegasaas->in_development_mode() && !$bypass_staging_mode) || $pegasaas->is_development_mode_request()) { 
				header("X-Pegasaas-Message: Caching Disabled");
				header("X-Pegasaas-Reason: In Staging Mode");	
			} else {
				header("X-Pegasaas-Message: Caching Disabled");
				header("X-Pegasaas-Reason: None");	
			} 
			//print "DISABLED";
			//exit;
			if (!PegasaasUtils::should_strip_footer_comments()) {
				$buffer .= PegasaasUtils::html_comment("Caching Disabled");
			}
			$pegasaas->utils->log("Caching Disabled PegasaasOptimize", "caching");

		}
		
		if ($_GET['accelerate'] == "off") {
			// apply ?accelerator=off to all CSS/JS to disable GZIP 
			$buffer = $pegasaas->apply_benchmark_mode($buffer);
			header("X-Pegasaas-Message: Benchmark Mode");	
		}
		

		return $buffer;
	}
	
	
	/**
     * Parse the output buffer.  Is responsible for 
     * organization and optimization of the requested document.
     *
     * @param string $buffer
     *
     * @return string Updated output buffer
     */	
	function local_optimize($buffer = "", &$save_cache = false) {
		global $pegasaas;
		
		$are_we_saving_cache = $save_cache;
		$resource_id = PegasaasUtils::get_object_id();
	

		$page_settings 		  = PegasaasUtils::get_object_meta(PegasaasUtils::get_object_id(), "accelerator_overrides");
		
		$paginated_page 	= is_paged();
		$do_paginated_pages	= PegasaasAccelerator::$settings['settings']['blog']['pagination_accelerated'] == 1;

		if ($page_settings['accelerated'] == 1 || $page_settings['accelerated'] == true) {
		
			if (PegasaasAccelerator::$settings['settings']['coverage']['premium']['use_temporary_foundation_optimizations'] == 0) {
				
				return $buffer;
			}
		} else if ($paginated_page && $do_paginated_pages) {
		

			if (PegasaasAccelerator::$settings['settings']['coverage']['premium']['use_temporary_foundation_optimizations'] == 0) {
				
				return $buffer;
			}			
		} else {
			

			if (PegasaasAccelerator::$settings['settings']['coverage']['status'] == 1 && PegasaasAccelerator::$settings['settings']['coverage']['extended']['use_foundation_optimizations'] == 0) {
				return $buffer;
			} else if (PegasaasAccelerator::$settings['settings']['coverage']['status'] == 0) {
				return $buffer;
			}			
		}
	
		$start_time = array_sum(explode(' ', microtime()));
		
		$accelerator_disabled = array_key_exists("accelerate", $_GET) && $_GET['accelerate'] == "off";

		if (!$this->is_wordlift_api()) {	
			if (!$accelerator_disabled) { 
		
				// strip out rocket cpcss
				$buffer = $pegasaas->utils->strip_wprocket_critical_css($buffer);
				$buffer = PegasaasCompatibility::apply_thrive_compatibility($buffer);
				
				$buffer = $this->condition_script_tags($buffer);
				$buffer = $this->condition_document($buffer);
				
				if (PegasaasUtils::get_feature_status("preload_user_files") == 1) {	
					$buffer = $this->preload_user_files($buffer);
				}

				if (PegasaasUtils::get_feature_status("google_fonts") == 1) {
				  	$buffer = $this->optimize_google_fonts($buffer);	
				}		

				if (PegasaasUtils::get_feature_status("dns_prefetch") >= 1) {
					$prefetch_domains = PegasaasPrefetch::get_dns_prefetch_domains($buffer);
				}

				
				if (PegasaasUtils::get_feature_status("minify_css") == 1) { 		
					$buffer = PegasaasMinify::apply_css_minify_urls($buffer);	
					if ($save_cache && strstr($buffer, "/plugins/elementor/")) {
						$pegasaas->cache->clear_posts_elementor_page_local_css_cache($buffer);
					}
				}

				if (PegasaasUtils::get_feature_status("minify_js") == 1) { 
					$buffer = PegasaasMinify::apply_js_minify_urls($buffer);
				}

				if (PegasaasUtils::get_feature_status("dns_prefetch") >= 1) { 
					 $buffer = PegasaasPrefetch::apply_dns_prefetch($buffer, $prefetch_domains);
				}
		
				if (PegasaasUtils::get_feature_status("wordlift") >= 1) { 
					$buffer = PegasaasCompatibility::apply_wordlift_conditioning($buffer);
				}
			
				if (PegasaasUtils::get_feature_status("lazy_load") >= 1) { 
					$buffer = PegasaasLazyLoad::lazy_load_iframes($buffer);
				}
				
					
				if (PegasaasUtils::get_feature_status("essential_css") >= 1) { 
					$buffer = PegasaasDeferment::inject_essential_css($buffer); 
				}
				
			
				if (PegasaasUtils::get_feature_status("defer_render_blocking_css") >= 1) {
					$defer_css 	= true;
					$defer_js  	= false;
					$save_cache = false;
					
					if (PegasaasUtils::get_feature_status("inject_critical_css") >= 1) {
						$buffer_array 	= PegasaasDeferment::inject_critical_css($buffer);
						$buffer 		= $buffer_array['buffer'];
						$defer_css  	= $buffer_array['critical_css_available'];
					} 


					if ($defer_css) {
						$buffer   	= PegasaasDeferment::defer_css($buffer);
						$defer_js 	= true;
						
						$save_cache = true;
					} 
				} else {
					$defer_js = true;
				
				}
				
				
				if (PegasaasUtils::get_feature_status("image_optimization") >= 1 || PegasaasUtils::get_feature_status("basic_image_optimization") >= 1) {
					$buffer = PegasaasImages::apply_optimized_img_url($buffer);
					$buffer .= "<!-- applied cdn image optimization -->\n";
				}

				
				if (PegasaasUtils::get_feature_status("external_image_optimization") >= 1 || PegasaasUtils::get_feature_status("cdn") >= 1) {
					$buffer = PegasaasImages::apply_external_img_cdn_url($buffer);
					$buffer .= "<!-- applied external image optimization -->\n";
				}

		
				if (PegasaasUtils::get_feature_status("lazy_load_images") >= 1) {
					$buffer = PegasaasLazyLoad::lazy_load_images($buffer);
				} 
				
						
				if (PegasaasUtils::get_feature_status("minify_html") >= 1) {
					$buffer = PegasaasMinify::minify_html($buffer);
				}

				if (PegasaasUtils::get_feature_status("defer_render_blocking_js") >= 1 && $defer_js) {
					$buffer =  PegasaasDeferment::defer_js($buffer);
				}	

				

				

				$buffer .= "\n<!-- This page optimized with Pegasaas Accelerator WP - https://pegasaas.com/ -->";
			
				// determine website address
				$website_url_parts = parse_url($_SERVER['SCRIPT_URI']);
				if ($_SERVER['HTTPS'] == "on" || $_SERVER['REQUEST_SCHEME'] == "https" || $_SERVER['SERVER_PORT'] == 443) {
					$scheme = "https";
				} else {
					$scheme = "http";
				}
				$website_address = "{$scheme}://".$_SERVER['HTTP_HOST'];
				$domain_folder = "";
				
				// if WPML is active and it uses multiple domains, then we should
				// indicate that the cache is stored in a subfolder
				//if (PegasaasUtils::wpml_multi_domains_active()) {
				//	$domain_folder = "/".$_SERVER['HTTP_HOST'];
				//}
				
			//	$website_address = $pegasaas->get_home_url();
			
				$resource_path 	= 	$pegasaas->utils->get_object_id();

				$pegasaas->utils->log("Optimize Local -- Resource Path: $resource_path ", "caching");
				/*
				$pegasaas->utils->log("Optimize Local -- Resource Path: $resource_path ", "critical");
				$pegasaas->utils->log("Remote ADDR:  ".$_SERVER['REMOTE_ADDR'], "critical");
				$pegasaas->utils->log("_GET:  ".print_r($_GET, true), "critical");
				$pegasaas->utils->log("_POST:  ".print_r($_POST, true), "critical");
				$pegasaas->utils->log("_SERVER:  ".print_r($_SERVER, true), "critical");
				$pegasaas->utils->log("settings:  ".print_r(PegasaasAccelerator::$settings, true), "critical");
				$pegasaas->utils->log("settings:  ".print_r(PegasaasAccelerator::$settings, true), "critical");
				*/
				$plugin_version = $pegasaas->get_current_version();
				$date_generated   = gmdate("F jS, Y @ H:i:s", time())." UTC";
			
				$finish_time = array_sum(explode(' ', microtime()));
				
				$time_to_generate = array_sum(explode(' ', microtime()))-$GLOBALS['pegasaas_page_start_time'];
				$time_to_generate = number_format($time_to_generate, 3);
			
				$auto_pilot_level = PegasaasAccelerator::$settings['settings']['speed_configuration']['status'];
				
				$optimization_levels = array();
				$optimization_levels[0] = "Manual Configuration";
				$optimization_levels[1] = "Sonic (Caching, Lazy Loading, Image Optimization)";
				$optimization_levels[2] = "Supersonic (Sonic + Deferral of Render Blocking Resources)";
				$optimization_levels[3] = "Hypersonic (Supersonic + Deferral of Third-Party Scripts)";
				$optimization_levels[4] = "Beast Mode (Hypersonic + Experimental Features)";
			

			

				
				if ($are_we_saving_cache) {
					$buffer .= "\n<!-- Cache File URL: {$website_address}{$resource_path} -->";
					$buffer .= "\n<!-- Cache File Path: /wp-content/pegasaas-cache{$resource_path}index-temp.html -->";
					$buffer .= "\n<!-- Optimization Level: ".$optimization_levels["{$auto_pilot_level}"]." -->"; 
					$buffer .= "\n<!-- Cache File Generated Via: Plugin v{$plugin_version} -->";
					$buffer .= "\n<!-- Cache File Generated On: {$date_generated} -->";
					$buffer .= "\n<!-- Cache File Generated In: {$time_to_generate} seconds -->";
					$buffer .= "\n<!-- Object ID: ".$pegasaas->utils->get_object_id()." -->";
				} else {
					$buffer .= "\n<!-- Page Requested: {$website_address}{$resource_path} -->";
					$buffer .= "\n<!-- Dynamically Generated Via: Plugin v{$plugin_version} -->";
					$buffer .= "\n<!-- Optimization Level: ".$optimization_levels["{$auto_pilot_level}"]." -->"; 
					$buffer .= "\n<!-- Dynamically Generated On: {$date_generated} -->";
					$buffer .= "\n<!-- Dynamically Generated In: {$time_to_generate} seconds -->";
					$buffer .= "\n<!-- Object ID: ".$pegasaas->utils->get_object_id()." -->";
				}
			
				//$buffer .= "\n<!-- Critical CSS Size: {$size_of_critical_css} bytes -->";

				
				//$buffer .= $pegasaas->utils->get_total_page_build_time();
				
				$buffer = $pegasaas->re_condition_comment_codes($buffer);
			} else {
				header("X-Pegasaas-Message: Benchmark Mode");
				$buffer = $pegasaas->apply_benchmark_mode($buffer);
			}
		} else {
			$save_cache = $pegasaas->do_wordlift();
		}
	
		
		return $buffer;
	}		

	
	
	/**
     * Helper function that returns true if the current request is the WordLift API endpoint.
     *
     * @return boolean 
     */	
	function is_wordlift_api() {
		if (strstr($_SERVER['REQUEST_URI'], "wl-api/") !== false) {
			return true;
		}
		return false;
	}	


	/**
     * Helper function that conditions the html document ($buffer) to add in missing </body> and </html> tags if those tags are missing.
     *
     * @return boolean 
     */		
	function condition_document($buffer) {
		if (strstr($buffer, "<body") && !strstr($buffer, "</body>")) {
		  $buffer .= "</body>";
		}
		if (strstr($buffer, "<html") && !strstr($buffer, "</html>")) {
		  $buffer .= "</html>";
		}
  
		return $buffer;
	}
  


	function condition_script_tags($buffer) {
		$pattern 		= "/<\/script[\s]+>/si";

		$matches = array();
    	preg_match_all($pattern, $buffer, $matches);
		foreach ($matches[0] as $x => $find) {
			$replace = "</script>";
			$buffer = str_replace($find, $replace, $buffer);
		}
		
		
		
		
		
		$pattern 		= "/<script(.*?)>/si";
		$pattern2 		= "/<script(.*?)>(.*?)<\/script>/si";

		$matches = array();
		$matches2 = array();
    	preg_match_all($pattern, $buffer, $matches);
		foreach ($matches[1] as $x => $find) {
			if (substr($find, -1) == "/") {
				$search = str_replace("?", '\?', $find);
				$search = str_replace("*", '\*', $search);
				$search = str_replace(".", '\.', $search);
				$search = str_replace("/", '\/', $search);
				$second_pattern = "/<script{$search}>(.*?)?<\/script>/si";
				//$second_pattern = "/{$search}(.*?)?<\/script>/si";
				//print "second pattern: ".($second_pattern);
				// check to see if there is an existing </script> tag
				preg_match_all($second_pattern, $buffer, $matches2);
		
				if (sizeof($matches2) == 0 || $matches2[0][0] == "") {
					
					$replace = str_replace("/>", "></script>", $matches[0][$x]);
					$buffer = str_replace($matches[0][$x], $replace, $buffer);
				} else {
					// there is another script, but no trailing </script> tag
					if (strstr($matches2[1][0], "<script")) {
						
						$replace = str_replace("/>", "></script>", $matches[0][$x]);
						$buffer = str_replace($matches[0][$x], $replace, $buffer);
					} else {
					
						// otherwise, we assume that there is no opening <script tag but there is a closing </script> tag, so we should just
						// remove the / from the opeing <script /> block 
						$replace = str_replace("/>", ">", $matches[0][$x]);
											

						$buffer = str_replace($matches[0][$x], $replace, $buffer);
					}
				}
				
				
			}
			
		}
		$buffer = str_replace('<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=el" />', '<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=el"></script>', $buffer);
		
		
		return $buffer;
	}
	
	function pegasaas_check_queued_optimization_requests() {
		global $pegasaas;
		
		
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$optimization_requests_results = $pegasaas->db->get_results("pegasaas_api_request", array("request_type" => "optimization-request"));
			$optimization_requests = array();
			foreach ($optimization_requests_results as $request_record) {
				$resource_id = $request_record->resource_id;
				$optimization_requests["$resource_id"] = json_decode($request_record->data, true);
			}

			$pending_requests = $_POST['pending_requests'];

			$completed_requests = array();
			if (is_array($pending_requests)) {
				foreach ($pending_requests as $resource_id) {
					if (!array_key_exists($resource_id, $optimization_requests)) {
						

						if (isset(PegasaasAccelerator::$cache_map["$resource_id"])) {
							$cache_data = PegasaasAccelerator::$cache_map["$resource_id"];
							if ($cache_data['is_temp']) {
								$completed_requests["$resource_id"]['status']		= 1; // temporary cache, no api request
							} else {
								$completed_requests["$resource_id"]['status']		= 2; // completed cache
							}
						} else {
							$completed_requests["$resource_id"]['status']		= 0; // no request
						}
						
					} else {
						$completed_requests["$resource_id"]['status']		= 1; // temporary cache, api request
					}
				}
			}
			$completed_requests["#summary#"]["total_pending_requests"] = sizeof($optimization_requests);
			print json_encode($completed_requests);
		} else {
			print json_encode(array("status" => -1));
		}		
	 	wp_die();			
		
	 	wp_die();		
	}
	
	
	
	function combine_css($buffer) { 
		global $pegasaas;
		$cdn 				= PegasaasAccelerator::$settings['settings']['cdn']['status'] == 1;
		$cache_server 		= PegasaasCache::get_cache_server(false, "css");
		$resource_id 		= PegasaasUtils::get_object_id();
		$server_domain = str_replace("www.", "", $_SERVER['HTTP_HOST']);
		$now = time();
		
		$path =  WP_CONTENT_DIR."/pegasaas-cache{$resource_id}";
		if (!is_dir($path)) {
			$pegasaas->cache->mkdirtree(WP_CONTENT_DIR."/pegasaas-cache{$resource_id}", 0755, true);
		}

		$file = "{$path}combined-{$now}.css";
		
		if (@file_exists($file)) {
			$css_exists = true;
		}
		
		
		$stylesheet_count = 0; 

		if (strstr($_SERVER['HTTP_USER_AGENT'], "PegasaasAccelerator") > -1 || $_GET['build_css'] == 1) { 
			$critical_css_builder_request = true;
			PegasaasDeferment::$building_critical_css = true;
		}
		
	
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
		$injected_placeholder = false;
					
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
			
			
			if (!$css_exists) {
				$css = $pegasaas->utils->get_file($href);
			}
			
					
			$pattern = "/url\((.*?)\)/";
		
			global $source_file;
			$source_file = $href;
			
		
			
			$css = preg_replace_callback($pattern, 'PegasaasAccelerator::fix_url_pattern2', $css);		 
			

			$cache_server = PegasaasCache::get_cache_server(false, "css");
			
			$cdn = PegasaasAccelerator::$settings['settings']['cdn']['status'] == 1;

			if ($cdn) { 
				if (strstr($source_file, "https://maxcdn.bootstrapcdn.com/font-awesome/") || strstr($source_file, "//maxcdn.bootstrapcdn.com/font-awesome/")) {
					$css = str_replace("url('https://maxcdn.bootstrapcdn.com/font-awesome/", "url('{$cache_server}!/external-css,https:/maxcdn.bootstrapcdn.com/font-awesome/", $css);
					$css = str_replace("url('//maxcdn.bootstrapcdn.com/font-awesome/", "url('{$cache_server}!/external-css,https:/maxcdn.bootstrapcdn.com/font-awesome/", $css);
					$css = str_replace("?v=4.7.0')", "')", $css);
					$css = str_replace("?v=4.5.0')", "')", $css);
				} 
			}
			
			

			

			
			if (PegasaasAccelerator::$settings['settings']['image_optimization']['status'] != 0) {
				// condition the cpcss, by placing the appropriate CDN information
			  	/* Apply CDN URL to any background images within the HTML */
				$matches 				= array();
				$bg_image_tag_pattern 	= '/[\s]?url\([\'"]?(.*?)[\'"]?\)/si';
				preg_match_all($bg_image_tag_pattern, $css, $matches);
				
				// iterate through the matches
				foreach ($matches[0] as $find) {  
					//$page_html .= $find;

					$replace 	= $find;
					$domain_match = array();

					// do not attempt to optimize an inline encoded image
					if (strstr($find, "data:image/")) {
						continue;
					}		

					// if this is a local URI, then we would change it
					if (strstr($find, $server_domain)) {

						$domain_pattern = "/(http:\/\/|https:\/\/|\/\/)(.*?)\//";
						preg_match($domain_pattern, $find, $domain_match);
						
						//$replace 		= str_replace($domain_match[0], "https://".PegasaasAccelerator::$settings['installation_id'].".".PEGASAAS_ACCELERATOR_CACHE_SERVER."/", $replace);
						$replace 		= str_replace($domain_match[0], $cache_server, $replace);
						$css 		= str_replace($find, $replace, $css);
					}
				}
 		
			}
			
			
			$css = $pegasaas->proxy_external_url($css);
			$combine_css .= "/* $href */\n";
			if (PegasaasAccelerator::$settings['settings']['minify_css']['status'] > 0) { 
				$css = PegasaasMinify::minify_css($css);
			}
			$combine_css .= $css."\n\n";
			
			
			
			if (!$injected_placeholder) {
				$buffer = str_replace($css_link, "[combine-css-insert-placeholder]", $buffer);
				$injected_placeholder = true;
			} else {
				$buffer = str_replace($css_link, "", $buffer);
			}
		}

		if (!$css_exists) {
					
					$fp = @fopen($file, "w");
				
					
		  			@fwrite($fp, $combine_css);

					
					@fclose($fp);
		}
		
		// map the correct stylesheet href
		$href = PegasaasCache::get_cache_content_url("combined-{$now}.css", "css");		
	
		// restore IE conditional comment script to the HTML
		$buffer = str_replace("<ielink", "<link", $buffer);
	
		// inject into page at the point of the previous first stylesheet
		$buffer = str_replace("[combine-css-insert-placeholder]", "<link rel='stylesheet' type='text/css' href='{$href}' />", $buffer);

		return $buffer;
	}	
	
	
	
	function optimize_google_fonts($buffer) {
		global $pegasaas;
		
		$matches 		= array();
		$href_pattern = "/['\"](https:\/\/|http:\/\/|\/\/)fonts.googleapis.com\/css\?family=(.*?)['\"]/si";
		$link_pattern = "/<link(.*?)>/si";
		$url_pattern = "/url\(['\"]?(.*?)['\"]?\)/si";
		$font_face_pattern = "/@font-face\{(.*?)\}/";

		$preload_html = "";
		$do_direct_inject = false;
		if ($do_direct_inject) {
			// search for all link tags
			$matches = array();
    		preg_match_all($link_pattern, $buffer, $matches);
	    	foreach ($matches[0] as $index => $link_find) {
				$matches_href = array();
    			preg_match_all($href_pattern, $link_find, $matches_href);
				if (sizeof($matches_href[0]) > 0) {
					$href = trim($matches_href[0][0], "'\"");
					if (substr($href, 0, 2) == "//") {
						$href = "https:".$href;
					}
		
					$file_contents = $pegasaas->utils->get_file($href);
				
					
					// so long as there is no error, then inject the direct code and continue to the next import, otherwise, do not replace
					if (!strstr($file_contents, "The requested URL was not found on this server.")) {
						$url_matches = array();
						preg_match_all($url_pattern, $file_contents, $url_matches);
						
						foreach ($url_matches[1] as $index => $url_match) {
							$preload_html .= "<link rel='preload' as='font' href='{$url_match}' />";
						}
						if (PegasaasAccelerator::$settings['settings']['enable_default_font_display'] != 0 || PegasaasAccelerator::$settings['settings']['enable_default_font_display'] != "") { 
						  if (PegasaasAccelerator::$settings['settings']['enable_default_font_display']['status'] == 1) {
							  $font_display = "fallback";
						  } else {
							  $font_display = PegasaasAccelerator::$settings['settings']['enable_default_font_display']['status'];
						  }
						
							$file_contents = str_replace("font-family", "font-display: {$font_display}; font-family", $file_contents);
						}
						
						
						$file_contents = "<style type=\"text/css\">\n".$file_contents."</style>";
						//$file_contents = "<style type=\"text/css\">@media (min-width:768px) {\n".$file_contents."\n}</style>";
						//$file_contents = "";
						$buffer = str_replace($link_find, $file_contents, $buffer);
								
						
						
					} 
					
						
					
									
				}
				
			}
		
		} else {		
		    preg_match_all($href_pattern, $buffer, $matches);

			// get all of the google fonts familes used in this document
			$families = array();
			$subset = "";
			$display_swap = "";
			foreach ($matches[0] as $index => $find) {
				$data = $matches[2]["$index"];
				$data = explode("&#038;", $data);
		
				
				if (strstr($data[0], "&display=swap")) {
					$display_swap = "&display=swap";
					$data[0] = str_replace("&display=swap", "", $data[0]);
				}
				if (strstr($data[0], "&subset=latin")) {
					$subset = "&subset=latin";
					$data[0] = str_replace("&subset=latin", "", $data[0]);
				}
				
				$families[] = $data[0];	
			}
			

			
			$replacement_done = false;
			foreach ($matches[0] as $index => $find) {

				// if we've modified the first stylesheet link then go through and 
				// find the matching link tag for this font family and remove it
				if ($replacement_done) {
					$matches_gf = array();
					$pattern = "/<link(.*?)>/si";
					preg_match_all($pattern, $buffer, $matches_gf);
					foreach ($matches_gf[0] as $index => $gf_find) {
						if (strstr($gf_find, $find) !== false) {
							$buffer = str_replace($gf_find, "", $buffer);
						}
					}
					
					$matches_gf = array();
					$import_pattern = "/@import (.*?);/i";
					preg_match_all($import_pattern, $buffer, $matches_gf);
					foreach ($matches_gf[0] as $index => $gf_find) {
						if (strstr($gf_find, $find) !== false) {
							$buffer = str_replace($gf_find, "", $buffer);
						}
					}
					

				// if this is the first stylesheet link, then replace it with a modified call to google fonts
				} else {

					$new_families 		= implode("|", $families);
					$replace 			= str_replace($matches[2]["$index"], $new_families.$display_swap.$subset, $find);
					
					
					$buffer 			= str_replace($find, $replace, $buffer);
					$replacement_done 	= true;
					continue;
	
				}
			}
		}

		return $buffer;
	}	
	
	
	/**
     * Conditions the output buffer to include <link> tags that preload
     * user defined files.
     *
     * @param string $buffer
     *
     * @return string Updated output buffer
     */		
	function preload_user_files($buffer) {
		global $pegasaas;
		
		// get user defined_ob resources
		$resources =  explode("\n", trim(str_replace("\r", "", PegasaasAccelerator::$settings['settings']['preload_user_files']['resources'])));

		foreach ($resources as $resource) {
			if ($resource != "") {
				$file_extension = PegasaasUtils::get_file_extension($resource);
				$as = "";
				
				if ($file_extension == "js") {
					$as = "script";
				} else if ($file_extension == "css") {
					$as = "style";
				} else if ($file_extension == "png" || $file_extension == "jpg" || $file_extension == "gif" || $file_extension == "svg" || $file_extension == "ico") {
					$as = "image";
				} else if ($file_extension == "woff" || $file_extension == "woff2" || $file_extension == "ttf" || $file_extension == "eot") {
					$as = "font";
				}
				if ($as != "") {
				  $buffer = str_replace("</head>", "<link rel='preload' as='{$as}' href='{$resource}' /></head>", $buffer);	
				}
			}
		}
		return $buffer;
	}	
	
	function can_request_api_optimization() {
		global $pegasaas;
		global $proxy_buffer;
		$paginated_page 	= is_paged();
		$do_paginated_pages	= PegasaasAccelerator::$settings['settings']['blog']['pagination_accelerated'] == 1;
		

		// if this plan is a 2019 monthly-optimizations limited plan
		if (isset(PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining'])) {
			// signal that this installation can request further optimizations as they have credits remaining
			if (PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining'] > 0) {	
				$resource_id = PegasaasUtils::get_object_id();
				$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides", false);
			
				// if the page has full acceleration enabled then signal that they can request via the API
				if ($page_level_settings['accelerated'] != false) {
					return true;
                    
				// optimize paginated pages if paginated pages is enabled	
 				} else if ($paginated_page && $do_paginated_pages) {
                	return true;
				
				// always optimize instapages
				} else if (PegasaasUtils::instapage_active() && PegasaasUtils::is_instapage_url()) {
					return true;

				// otherwise, signal that the installation should not request from the API	
				} else {
					return false;
				}
			
			// signal that the installation cannot request further optimization through the API
			} else {
				return false;
			}
			
		// if this plan is not a monthly-optimizations or the limits are set to zero
		} else if (!isset(PegasaasAccelerator::$settings['limits']['monthly_optimizations']) || PegasaasAccelerator::$settings['limits']['monthly_optimizations'] == 0) {
			$resource_id = PegasaasUtils::get_object_id();
			$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides", false);
			
			// if the page has full acceleration enabled then signal that they can request via the API
			if ($page_level_settings['accelerated'] != false) {
				return true;

			// always optimize instapages
			} else if (PegasaasUtils::instapage_active() && PegasaasUtils::is_instapage_url()) {
				return true;

			// optimize paginated pages if paginated pages is enabled
			} else if ($paginated_page && $do_paginated_pages) {
                return true;	
                	
			// otherwise, signal that the installation should not request from the API	
			} else {
				return false;
			}
			
		}
		return false;
		
	}
	
	
	
	
	
	function optimize_via_api($buffer = "", $test_submission = false) {
		global $pegasaas;;
		$debug = false;
		
		if (!PegasaasUtils::memory_within_limits()) {
			return false;
		}
		
		$resource_id = PegasaasUtils::get_object_id();

		return $pegasaas->submit_optimization_request($resource_id, $buffer, $test_submission);
		
	}
	
}
?>