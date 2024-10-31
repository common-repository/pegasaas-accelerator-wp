<?php
class PegasaasScanner {
	var $data_cache;
	
	function __construct() {
		$this->data_cache = array();	
	}
	
	
	/*********************************************************************
	 * BENCHMARK SCANS 
	 *********************************************************************/
	
	function clear_last_benchmark_scan($resource_id) {
		global $pegasaas;
		
		$pegasaas->db->delete_last("pegasaas_performance_scan", array("resource_id" => $resource_id), 'scan_id');
	}

	function maybe_request_page_scan($resource_id, $priority_flag, $is_manual_request = false) {
		global $pegasaas;
		
		$request = false;
		$one_month = 60*60*24*30;
		$pegasaas->utils->log("Maybe Request Page Scan {$resource_id} / {$priority_flag}", "submit_scans");
		$maximum_queued_request_to_allow = 5; // this prevents hammering a server
		$periods = array();
		$periods[0] = 0;
		$reason = "None";
		if ($priority_flag == "") { 
		   $priority_flag = 0;
		}
		
		$periods["2-weeks"] = 60*60*24*14;
		$periods["1-month"] = 60*60*24*30;
		$periods["2-months"] = 60*60*24*60;
		
		
		$one_day = date("Y-m-d H:i:s", time() - 86400);
		$one_week = date("Y-m-d H:i:s", time() - (86400 * 7));

		
		if (isset(PegasaasAccelerator::$settings['settings']['auto_rescan_webperf']) && PegasaasAccelerator::$settings['settings']['auto_rescan_webperf']['status'] == 1) {
			$existing_queued_requests = $pegasaas->db->get_results("pegasaas_api_request", 
																   array("request_type" => "pagespeed", 
																		 "resource_id" => $resource_id));
			$total_existing_queued_requests = $pegasaas->db->get_results("pegasaas_api_request", 
																		 array("request_type" => "pagespeed"));
		
			$all_existing_scans_in_last_day = $pegasaas->db->get_results("pegasaas_performance_scan", 
																		 array("request_type" => "pagespeed",
																			  "time" => array("comparison" => ">",
																							  "value" => $one_day)));
			
			//print sizeof($all_existing_scans_in_last_day);
			// if this is a manually re-optimized page, then we will rescan
			// if there have not been more scans, than the total number of webperf scanned pages scanned, in the last day
			if ($is_manual_request) {
				if (sizeof($all_existing_scans_in_last_day) < PegasaasAccelerator::$settings['limits']['pagespeed_scanned_pages']) {
					$request = true;
				} else {
					$reason = "Already more than max number of monthly scans in the last 24 hours (".sizeof($all_existing_scans_in_last_day)." > ".PegasaasAccelerator::$settings['limits']['pagespeed_scanned_pages'].")";
				}
				
			// if this is a general re-optimizztion or optimization (not manually requested), then allow for rescans if
			// there haven't been more scans than the web perf scanned pages / 30
			} else {
				if (sizeof($all_existing_scans_in_last_day) < PegasaasAccelerator::$settings['limits']['pagespeed_scanned_pages'] / 30) {
					$last_scan_of_this_resource  = $pegasaas->db->get_single_record("pegasaas_performance_scan", 
																		 array("request_type" => "pagespeed",
																			   "resource_id" => $resource_id),
																			 array("time" => "DESC"));
					$last_scan_time = strtotime($last_scan_of_this_resource->time);
					
					if ($last_scan < strtotime($one_week)) {
						$request = true;
					} else {
						$reason = "Scan already performed in the last week";
					}
				} else {
					$reason = "Already more than 1/30th of monthly scans in the last 24 hours";
				}
				
			}
		}
			/*
			// if the priority is 1 or 2, then submit scan request
			if ($priority_flag == 1 || $priority_flag == 2) {
				$rescan_period = PegasaasAccelerator::$settings['settings']['auto_rescan_webperf']['rescan_priority_after'];

				if ($rescan_period == "") {
					$rescan_period = 0;
				}
				
				
				if (sizeof($existing_queued_requests) == 0 && $rescan_period > 0) {
					$request = true;
				} else {
					$reason = "Rescan Period Disabled";
				}
			} else if ($total_existing_queued_requests < 5) {
				$rescan_period = PegasaasAccelerator::$settings['settings']['auto_rescan_webperf']['rescan_non_priority_after'];
				if ($rescan_period == "") {
					$rescan_period = 0;
				}
				
				 if ($rescan_period > 0) {
					 $timeframe = $periods["{$rescan_period}"];
					 if ($timeframe < $periods["2-weeks"]) {
					   $timeframe = $periods["2-week"];
					 }
					

					// if there is no existing scan   
					$existing_scans = $pegasaas->db->get_results("pegasaas_performance_scan", array("scan_type" => "pagespeed", "resource_id" => $resource_id), "time DESC", "", 1);
					if (sizeof($existing_scans) == 0) {
						$request = true;
					} else {
						$scan_date = $existing_scans[0]->time;
						if (strtotime($scan_date) < time() - $timeframe) {
							$request = true;
						} else {
							$reason = "Not Stale Enough";
						}

					}
				 } else {
					 $reason = "Rescan Period Disabled";

				 }
			} else {
				$reason = "{$total_existing_queued_requests} existing queued requests";
			}
		} else {
			$reason = "Auto Rescan Webperf Disabled";
		}
		*/ 
		if ($request) {
			$this->request_pagespeed_score($resource_id, false, false);
		} else {
			$pegasaas->utils->log("Not Requesting Page Scan [{$resource_id}] / [{$priority_flag}] ({$reason})", "submit_scans");

		}
		
	}
	
	
	function calculate_web_pef_metrics() {
		$this->get_site_score($force_recalculation = true);
		
	}
	
	static function needs_webperf_scans() {
		global $pegasaas;
		
		if (isset($_GET['page']) && $_GET['page'] == "pegasaas-accelerator") {
			return true;
		} else {
			return false;
		}		
		
	}
	static function needs_benchmark_scans() {
		global $pegasaas;
		if (isset($_GET['page']) && $_GET['page'] == "pegasaas-accelerator") {
			return $pegasaas->scanner->submit_benchmark_requests(false) > 0;
		} else {
			return false;
		}
		
	}
	function submit_benchmark_requests($submit_scans = true) {

		global $pegasaas;
		$pages_to_submit = array();
		if (PegasaasAccelerator::$settings['status'] != 1) {
			return;
		}
		$debug = false; 
	
		
		if ($pegasaas->utils->semaphore("submit_benchmark_requests", $wait_time = 0, $stale_if_this_many_seconds_old = 30)) {
			if (strstr($_GET['pegasaas_debug'], "submit_benchmark_requests") !== false) {
				$debug = true;
			}
			
			$debug_show_existing_benchmark_requests = true;
			$debug_show_benchmark_pagepeed_history 	= false;
			
			// it is okay to submit as many benchmark requests as we like as it will not
			// impact the server, as they will be throttled by the API based upon the 
			// specified response rate
			$maximum_submits_per_invocation 		= 250; 
			$maximum_benchmark_scans 				= PegasaasAccelerator::$settings['limits']['pagespeed_scanned_pages'];
			
			// kinsta servers simply cannot handle sustained load, so we will only take a 
			// small sample of the baseline performance metrics.  We also only request only 10 at a time
			// as requesting a whole bunch may result in some of the requests becoming stale
			if ($pegasaas->utils->is_kinsta_server()) {
				$maximum_submits_per_invocation = 10;
				$maximum_benchmark_scans = 100;
			}
			
			$posts_array = PegasaasUtils::get_all_pages_and_posts();
		
			$pages_to_submit = array();
		
			// strip the http:// or https:// from the site and home urls to condition the urls
			// as some users may inadvertantly not set the protocols to the same, which will cause the callback url for the 
			// benchmark requests to end up looking like https://yourwebsite.com/https://yourwebsite.com/wp-admin/...
		
			$find_protocols 	= array("http://", "https://");
			$replace_protocols 	= array("", "");
			$home_url = str_replace($find_protocols, $replace_protocols, $pegasaas->get_home_url());
			$site_url = str_replace($find_protocols, $replace_protocols, get_site_url());
			
			$location = $pegasaas->utils->get_wp_location();
		
			if ($debug) {
				print "<pre class='admin'>home_url: ".$pegasaas->get_home_url()."\n";
				print "site url: ".get_site_url()."\n";
				print "</pre>";		
			}
		
			//$existing_benchmark_requests = get_option("pegasaas_accelerator_pagespeed_benchmark_score_requests", array());
			$existing_benchmark_request_records = $pegasaas->db->get_results("pegasaas_api_request", 
																			 array("request_type" => "pagespeed-benchmark"));
			foreach ($existing_benchmark_request_records as $record) {
				$resource_id = $record->resource_id;
				$existing_benchmark_requests["$resource_id"] = $record;
			}
			
			if (is_array($existing_benchmark_requests)) {
				$total_existing_scans = sizeof($existing_benchmark_requests);
			}
			$total_existing_scans += $pegasaas->scanner->get_pagespeed_benchmark_scores_count();
			
			if ($debug && $debug_show_existing_benchmark_requests) {
				print "<pre class='admin'>Existing Benchmark Requests\n";
				var_dump($existing_benchmark_requests);
				print "</pre>";
			}
		
			// get all posts that have acceleration enabled -- these are the only pages that we will request GPSI B for.
			$enabled_posts = array();
			foreach ($posts_array as $post) {
				if ($pegasaas->utils->has_acceleration_enabled($post->resource_id, $post->is_category)) {
					$enabled_posts[] = $post;
				}
			}

			$enabled_posts = array_slice($enabled_posts,0,$maximum_benchmark_scans);
	

			// iterate through list and checked to see if there are any that do not have a recent scan date
			foreach ($enabled_posts as $post) {		
				$resource_id = $post->ID;
				$resource_id = $post->resource_id;
				$request_this_resource = false;

				$post_pagespeed_history = $this->get_pages_with_scores("pagespeed-benchmark", $resource_id);
			
			if ($post_pagespeed_history != "") {
				if ($debug) {
					print "<pre class='admin'>";
					print "have some post_pagespeed history\n";
					var_dump($post_pagespeed_history);
					print "</pre>";
				}
				
				$post_data = json_decode($post_pagespeed_history->data, true);
				
				if (strtotime($post_pagespeed_history->time) < mktime(0,0,0, date("m"),date("d") - 730) && !$existing_benchmark_requests["$resource_id"]) {
					$request_this_resource = true;
				} else if ($post_data['version'] < 5 && !$existing_benchmark_requests["$resource_id"]) {
					$request_this_resource = true;	
				} else if ($post_data['score'] == '' && $post_data['score'] != '0') {
					$request_this_resource = true;
					
				} else {
					continue;
				}
			} else {
				// do not request benchmark score if the acceleration status is disabled
				$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides");
				if ($debug) {
					print "<pre class='admin'>";
					print "Page level settings for $resource_id is {$page_level_settings['accelerated']}";
					print "</pre>";
				}
				if (isset($page_level_settings['accelerated']) && ($page_level_settings['accelerated'] == true || $page_level_settings['accelerated'] == 1)) {
				
					$request_this_resource = true;
				} else {
				
					$request_this_resource = false;
				}
				if ($debug) {
					print "<pre class='admin'>";
					print "Request This Resource $resource_id : $request_this_resource";
					print "</pre>";
				}				
			
			}
			if (is_array($existing_benchmark_requests) && array_key_exists($resource_id, $existing_benchmark_requests )){
				$request_this_resource = false;
				if ($debug && false) {
					print "<pre class='admin'>We have one already! {$resource_id}\n";
					var_dump($post_pagespeed_history);
					print "</pre>";
				}	
			} else {
				if ($debug) {
					print "<pre class='admin'>We DO NOT have one already! {$resource_id}\n";
					var_dump($post_pagespeed_history);
					print "</pre>";
				}
			}
			if ($request_this_resource) {
				if (strstr($post->slug, "http://") || strstr($post->slug, "https://")) {
					$the_url = $post->slug;
				} else {
					$the_url = $pegasaas->get_home_url().$post->slug;
				}
			
				
				if ($the_url == "") {
					$the_url = $pegasaas->get_home_url()."/";
				}
				if (strstr($the_url, "?") === false) {
					$the_url .= "?";
				} else {
					$the_url .= "&";
				}
				$the_url .= "accelerate=off";
				//$the_url .= "&web-perf-baseline-scan"; // disabled, as this modification cause the TTFB to be artifically fast.
				
				$nonce = $resource_id."-".$pegasaas->utils->microtime_float();
				if ($resource_id == "/") {
					$priority = true;
				} else {
					$priority = false;
				}
				if (PegasaasAccelerator::$settings['limits']['pagespeed_scanned_pages'] > $total_existing_scans) {
				/*
					$pages_to_submit[] = array(		
						"url" => $the_url,
						"nonce" => $nonce,
						"resource_id" => $resource_id,
						"priority" => $priority,
						"callback_url" => ($_SERVER['HTTPS'] == "on" ? "https://" : "http://").$_SERVER['HTTP_HOST'].$location."/wp-admin/admin-ajax.php?wp_rid=".$resource_id."&wp_n=".$nonce."&v=3"
					);
					*/
					$pages_to_submit[] = array(		
						"url" => $the_url,
						"nonce" => $nonce,
						"resource_id" => $resource_id,
						"priority" => $priority,
						"callback_url" =>  admin_url( 'admin-ajax.php' )."?wp_rid=".$resource_id."&wp_n=".$nonce."&v=3"
					);					
							

					$total_existing_scans++;
				}
				
				if ($debug && false) {
					print "<pre class='admin'>location\n";
					print $location;
					print "</pre>";
				}
			}
		}
			
		$pages_to_submit = array_slice($pages_to_submit,0,$maximum_submits_per_invocation);
		if (!$submit_scans) {
			return count($pages_to_submit);
		}
		if ($debug) {
			print "<pre class='admin'>Submits Per Invocation: ".$maximum_submits_per_invocation."</pre>";
			print "<pre class='admin'>Total Existing Scans Limit: ".PegasaasAccelerator::$settings['limits']['pagespeed_scanned_pages']."</pre>";
			print "<pre class='admin'>Total Existing Scans: {$total_existing_scans}</pre>";
			print "<pre class='admin'>Pages To Submit\n";
			var_dump($pages_to_submit); 
			print "</pre>";
		} 
			
		// assemble and bundle the payload and submit it to pegasaas
		if( sizeof($pages_to_submit) > 0) { 
		
			$post_fields = array();
			$post_fields['api_key'] = PegasaasAccelerator::$settings['api_key'];
			$post_fields['domain'] 	= $_SERVER["HTTP_HOST"];
			$post_fields['version']	= 5;
			$post_fields['status']	= 1;
			$post_fields['command']	= "submit-pagespeed-benchmark-queries";
			$post_fields['queries'] = json_encode($pages_to_submit);
			if ($debug) {
				print "<pre class='admin'>";
				var_dump($post_fields);
				print "</pre>";
			}
			
			
		
			$response = $pegasaas->api->post($post_fields, array("blocking" => false));
			
			$data = json_decode($response, true);
			
			if ($data['api_error'] != "") {
				 
				return json_encode(array("status" => 0, "message" => 'Error: Problem communicating with pegasaas.io'));
			} else {	

					
				foreach ($pages_to_submit as $request) { 
					$pegasaas->db->add_record("pegasaas_api_request", array("time" => date("Y-m-d H:i:s", time()), "request_type" => "pagespeed-benchmark", "resource_id" => $request['resource_id'], "nonce" => $request['nonce']));
				}

				}
				
			}
			$pegasaas->utils->release_semaphore("submit_benchmark_requests");
		}
		return sizeof($pages_to_submit);
	}
	
	function fetch_page_scripts() {
		global $pegasaas; 
		$save_custom_scripts = false;
		
		
		$site_resources = get_option("pegasaas_in_page_scripts", array());
	
		if ($site_resources == "" || sizeof($site_resources) == 0) {	
			$home_page_html = $pegasaas->utils->fetch_page_html($pegasaas->get_home_url()."/?accelerate=off");
			$site_resources = $pegasaas->capture_in_page_scripts($home_page_html);		
		}

		if (is_array(PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts'])) {
			foreach (PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts'] as $url => $item) {
			
				
			
				if (trim($url) != "") {
					$url = trim($url);
					$site_resources["$url"] = $item;
					$site_resources["$url"]['url'] = $url;
				
					// correct blank entries
				} else {
					$save_custom_scripts = true;
					unset($pegasaas->settings['settings']['lazy_load_scripts']['custom_scripts']["{$url}"]);
				}
				
			}
		}
		if ($save_custom_scripts) { 
			update_option("pegasaas_lazy_load_scripts_custom_scripts", PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']);
		}
	
				
	
	
		return $site_resources;
	}
	
	function fetch_page_fonts() {
		global $pegasaas; 
		$save_custom_scripts = false;
		
		
		$site_resources = get_option("pegasaas_in_page_fonts", array());
	
		if ($site_resources == "" || sizeof($site_resources) == 0) {	
			$home_page_html = $pegasaas->utils->fetch_page_html($pegasaas->get_home_url()."/");
			
			$site_resources = $pegasaas->capture_in_page_fonts($home_page_html);		
		}
/*
		if (is_array(PegasaasAccelerator::$settings['settings']['strip_google_fonts_mobile']['custom_fonts'])) {
			foreach (PegasaasAccelerator::$settings['settings']['strip_google_fonts_mobile']['custom_fonts'] as $url => $item) {
			
				
			
				if (trim($url) != "") {
					$url = trim($url);
					$site_resources["$url"] = $item;
					$site_resources["$url"]['url'] = $url;
				
					// correct blank entries
				} else {
					$save_custom_fonts = true;
					unset($pegasaas->settings['settings']['strip_google_fonts_mobile']['custom_fonts']["{$url}"]);
				}
				
			}
		}
		
		if ($save_custom_scripts) { 
			update_option("pegasaas_strip_google_fonts_mobile_custom_fonts", PegasaasAccelerator::$settings['settings']['strip_google_fonts_mobile']['custom_fonts']);
		}
	
		*/		
	
	
		return $site_resources;
	}

	function get_lazy_loadable_scripts() {
		global $pegasaas;
		
		
		$resources = $this->fetch_page_scripts();
		foreach ($resources as $i => $item) {
			//if ($item['resourceType'] == "Script") {
				$item['url'] = str_replace(PegasaasCache::get_cache_server(false, "js"), "", $item['url']);
				$item['url'] = str_replace(PegasaasCache::get_cache_server(false, "css"), "", $item['url']);
				$item['url'] = str_replace(PegasaasCache::get_cache_server(false, "img"), "", $item['url']);
				$item['url'] = str_replace(PegasaasCache::get_cache_server(false), "", $item['url']);
				$item['url'] = str_replace($pegasaas->get_home_url(), "", $item['url']);
				$item['url'] = $pegasaas->utils->strip_query_string($item['url']);
				$item['default'] = 0; // by default, if no settings, then lazy loading of this script is off
				$item['default_mobile'] = 0;
				$item['default_desktop'] = 0;
			
				if (strstr($item['url'], "googletagmanager.com")) {
					if (PegasaasUtils::get_feature_status('lazy_load_google_tag_manager') == 1) {
						unset($resources[$i]);	
						continue;
					}							
					$item['alias'] = "[Google] Tag Manager";
				} else if (strstr($item['url'], "recaptcha/api.js")) {
					$item['alias'] = "[Google] reCAPTCHA";
					$item['default'] = 1;
					$item['default_mobile'] 	= 1;
					$item['default_desktop'] 	= 1;
				} else if (strstr($item['url'], "jquery.js") || strstr($item['url'], "jquery.min.js" )) {
					// removing jQuery from the list of lazy loadable scripts -- there are no scenarios where 
					// jquery can be reliably lazy loaded as nearly all JS aspects of a modern WP website require jQuery
					$item['alias'] = "[Shared] jQuery";
					unset($resources[$i]);	
					continue;
				} else if (strstr($item['url'], "jquery-migrate.min.js")) {
					// removing jQuery from the list of lazy loadable scripts -- there are no scenarios where 
					// jquery can be reliably lazy loaded as nearly all JS aspects of a modern WP website require jQuery
					
					$item['alias'] = "[Shared] jQuery Migrate";
					unset($resources[$i]);	
					continue;
					
				} else if (strstr($item['url'], "google-analytics.com/analytics.js")) {
					if (PegasaasUtils::get_feature_status('lazy_load_google_analytics') == 1) {
						unset($resources[$i]);	
						continue;
					}					
					$item['alias'] = "[Google] Analytics";
				} else if (strstr($item['url'], "wp-embed.min.js")) {
					$item['alias'] = "[WP Core] Embeds";
				} else if (strstr($item['url'], "comment-reply.min.js")) {
					$item['alias'] = "[WP Core] Comment Replies";
				} else if (strstr($item['url'], "emoji-release.min.js")) {
					if (PegasaasUtils::get_feature_status('lazy_load_wordpress_emoji') == 1) {
						unset($resources[$i]);	
						continue;
					}
					$item['alias'] = "[WP Core] Emoji";
				} else if (strstr($item['url'], "bootstrap.min.js") && !strstr($item['url'], "wp-content")) {
					$item['alias'] = "[Common] Bootstrap Framework JS";
				
				} else if (strstr($item['url'], "www.gstatic.com/recaptcha/api2")) {
					unset($resources[$i]);	
					continue;
				} else if (strstr($item['url'], "/instagram-feed/js/sb-instagram.min.js")) {
					$item['alias'] = "[Plugin] Instagram Feed";
					$item['default']			= 1;
					$item['default_mobile'] 	= 1;
					$item['default_desktop'] 	= 1;
				} else if (strstr($item['url'], "/wp-content/plugins/thirstyaffiliates/js/app/ta.js")) {
					$item['alias'] = "[Plugin] Thirsty Affiliates";
					$item['default']			= 1;
					$item['default_mobile'] 	= 1;
					$item['default_desktop'] 	= 1;	
				} else if (strstr($item['url'], "/wp-content/plugins/jetpack/_inc/build/twitter-timeline.min.js")) {
					$item['alias'] = "[Plugin] Jetpack Twitter Timeline";
					$item['default']			= 1;
					$item['default_mobile'] 	= 1;
					$item['default_desktop'] 	= 1;			
				} else if (strstr($item['url'], "/wp-content/plugins/jetpack/_inc/build/facebook-embed.min.js")) {
					$item['alias'] = "[Plugin] Jetpack Facebook Embed";
					$item['default']			= 1;
					$item['default_mobile'] 	= 1;
					$item['default_desktop'] 	= 1;	 							

				} else if (strstr($item['url'], "platform.twitter.com/widgets.js")) {
					if (PegasaasUtils::get_feature_status('lazy_load_twitter_feed') == 1) {
						unset($resources[$i]);	
						continue;
					} 
					
					$item['alias'] = "[Twitter] Feed";
				} else if (strstr($item['url'], "maps.googleapis.com/maps/api/js")) {
					if (PegasaasUtils::get_feature_status('lazy_load_google_maps') == 1) {
						unset($resources[$i]);	
						continue;
					} 
					
					$item['alias'] = "[Google] Maps API";				
					
				} else if (strstr($item['url'], "cdn.callrail.com")) {
					if (PegasaasUtils::get_feature_status('lazy_load_callrail') == 1) {
						unset($resources[$i]);	
						continue;
					} 
					
					$item['alias'] = "CallRail Script";								
			
				} else if (strstr($item['url'], "js.hs-scripts.com")) {
					if (PegasaasUtils::get_feature_status('lazy_load_hubspot') == 1) {
						unset($resources[$i]);	
						continue;
					} 
					
					$item['alias'] = "Hubspot Scripts Loader";			
				
				} else if (strstr($item['url'], "stats.wp.com")) {
					if (PegasaasUtils::get_feature_status('lazy_load_wordpress_site_stats') == 1) {
						unset($resources[$i]);	
						continue;
					} 
					
					$item['alias'] = "[WordPress] Site Stats";			
				
				} else if (strstr($item['url'], "script.crazyegg.com")) {
					if (PegasaasUtils::get_feature_status('lazy_load_crazyegg') == 1) {
						unset($resources[$i]);	
						continue;
					} 
					
					$item['alias'] = "CrazyEgg Script";			
				
			
				} else if (strstr($item['url'], "static.getclicky.com/js")) {
					if (PegasaasUtils::get_feature_status('lazy_load_clicky_analytics') == 1) {
						unset($resources[$i]);	
						continue;
					} 
					
					$item['alias'] = "Clicky Analytics Script";		
	
		
				
				
				
				} else if (strstr($item['ur'], "platform.twitter.com/widgets.js")) {
					if (PegasaasUtils::get_feature_status('lazy_load_twitter_feed') == 1) {
						unset($resources[$i]);	
						continue;
					} 
					
					$item['alias'] = "Twitter Feed";
				
		
				} else { 
					if (strstr($item['url'], "wp-content/plugins")) {
						
						$path = str_replace("/wp-content/plugins/", "", $item['url']);
					
						$path_data = explode("/", $path);
						
						$plugin_name = array_shift($path_data);
						$plugin_name = ucwords($plugin_name);
						$stripped_path = array_pop($path_data);
						$item['alias'] = "[Plugin] {$plugin_name}: {$stripped_path}";
					} else if (strstr($item['url'], "wp-content/themes")) {
						$path = str_replace("/wp-content/themes/", "", $item['url']);
						$path_data = explode("/", $path);
						$theme_name = ucwords(array_shift($path_data));
						
						$stripped_path = array_pop($path_data);
						$item['alias'] = "[Theme] {$theme_name}: {$stripped_path}";
					} else {
						$item['alias'] = "";
					}
				}
				$resources["$i"] = $item;
		//	} else {
			
			//	unset($resources[$i]);
			//}
		} 
		
		//usort($resources, array($this, "sort_resources_by_load_order"));
		
		
		
		$found = array();
		$found = $resources;
		
	
		
		return $found;
	
		
	}

	

	function get_page_fonts() {
		global $pegasaas;
		
		
		$resources = $this->fetch_page_fonts();
		
		$found = array();
		$found = $resources;
		
	
		
		return $found;
	
		
	}

	function sort_resources_by_load_order($a, $b) {
		if ($a['startTime'] == $b['startTime']) {
			return 0;
		}
		return ($a['startTime'] < $b['startTime']) ? -1 : 1;
	}
	
	
	function get_page_benchmark_speed_opportunities($resource_id, $context = "desktop") {
		global $pegasaas;
	
		$opportunities = array();

		$post_data = $this->pegasaas_fetch_pagespeed_benchmark_last_scan($resource_id);
	
		if (is_array($post_data)) {		
		

			
			if (is_array($post_data['meta']) && isset($post_data['meta']['formattedResults']) && is_array($post_data['meta']['formattedResults']) && is_array($post_data['meta']['formattedResults']['ruleResults']) ) { 
				$metaArrayToLoopThrough=$post_data['meta']['formattedResults']['ruleResults'];
				$metaMode='';	
			} else if ($context == "desktop" && is_array($post_data['meta']['desktop']) && is_array($post_data['meta']['desktop']['formattedResults']) && is_array($post_data['meta']['desktop']['formattedResults']['ruleResults']) ) {
				$metaArrayToLoopThrough=$post_data['meta']['desktop']['formattedResults']['ruleResults'];
				$metaMode='desktop';	
			} else if ($context == "mobile" && is_array($post_data['meta']['mobile']) && is_array($post_data['meta']['mobile']['formattedResults']) && is_array($post_data['meta']['mobile']['formattedResults']['ruleResults']) ) {
				$metaArrayToLoopThrough=$post_data['meta']['mobile']['formattedResults']['ruleResults'];
				$metaMode='mobile';	
			}				

			if(isset($metaArrayToLoopThrough ) ){
				foreach ($metaArrayToLoopThrough as $rule => $data) {
					if ($data['ruleImpact'] > 0) {
						if ($data['ruleImpact'] < 2) {
							$rule_label = "";
						} else if ($data['ruleImpact'] < 6) {
							$rule_label = "<span class='label label-warning label-small pull-right'>Needs Attention</span>";
						} else {
							$rule_label = "<span class='label label-danger label-small pull-right'>Critical Issue</span>";
						}
						$opportunities[] = array(
							"rule" => $rule, 
							"rule_name"				=> $this->get_rule_name($rule), 
							"rule_description"		=> $data['localizedRuleName'], 
							"rule_icon"				=> $this->get_rule_icon($rule), 
							"rule_impact" 			=> $data['ruleImpact'],
							"rule_long_description" => "<div class='score-worth'>Worth ".number_format($data['ruleImpact'], 2, '.', '')." points</div>{$rule_label}<br/><br/>".$this->get_rule_description($rule, "benchmarked", $data)
						);	
					}
				}
			}
		}
		uasort($opportunities, array($this, "sort_page_speed_opportunities"));
		return $opportunities;
	}	
	
	function clear_pagespeed_benchmark_scores() {
		global $pegasaas;
		

		
		if ($debug) {
				$debug_backtrace = debug_backtrace();
			$calling_file = explode("/", $debug_backtrace[0]['file']);
			$calling_file = array_pop($calling_file);
			$calling_function = $debug_backtrace[1]['function'];
			$calling_class = $debug_backtrace[1]['class'];
			$calling_line = $debug_backtrace[0]['line'];
			
			$debug_backtrace_string = "{$calling_file} ".($calling_class != "" ? $calling_class."->" : "")."{$calling_function}() line #{$calling_line}";
		
	print "<pre class='admin'>";
		print $debug_backtrace_string;
		print "</pre>";
			
		}
		
		$pages_with_scores = get_option("pegasaas_accelerator_pagespeed_benchmark_scores", array());
		
		$all = PegasaasUtils::get_all_pages_and_posts();
		
		foreach ($all as $post) {
			$pegasaas->utils->delete_object_meta($post->resource_id, "accelerator_pagespeed_benchmark_history");
		}			
		foreach ($pages_with_scores as $resource_id => $value) {
			$pegasaas->utils->delete_object_meta($resource_id, "accelerator_pagespeed_benchmark_history");
		}
		delete_option("pegasaas_accelerator_pagespeed_benchmark_scores");
		delete_option("pegasaas_accelerator_pagespeed_benchmark_score_request_tokens");
		delete_option("pegasaas_accelerator_pagespeed_benchmark_score_requests");
		$pegasaas->db->delete("pegasaas_performance_scan", array("scan_type" => "pagespeed-benchmark"));
		$pegasaas->db->delete("pegasaas_api_request", array("request_type" => "pagespeed-benchmark"));

	}
	
	
	function clear_performance_scans($resource_id) {
		global $pegasaas;
		$pegasaas->db->delete("pegasaas_performance_scan", array("resource_id" => $resource_id, "scan_type" => "pagespeed-benchmark"));
		$pegasaas->db->delete("pegasaas_api_request", array("resource_id" => $resource_id, "request_type" => "pagespeed-benchmark"));

		$pegasaas->db->delete("pegasaas_performance_scan", array("resource_id" => $resource_id, "scan_type" => "pagespeed"));
		$pegasaas->db->delete("pegasaas_api_request", array("resource_id" => $resource_id, "request_type" => "pagespeed"));
		
	}
	
	/******************************************************************
	 * ACCELERATED PAGE SCANS
	 *****************************************************************/

	function clear_pagespeed_scores() {
		global $pegasaas;
		
		$pages_with_scores = get_option("pegasaas_accelerator_pagespeed_scores", array());
		
		$all = PegasaasUtils::get_all_pages_and_posts();
		
		foreach ($all as $post) {
			$pegasaas->utils->delete_object_meta($post->resource_id, "accelerator_pagespeed_history");
		}		
		
		foreach ($pages_with_scores as $resource_id => $value) {
			$pegasaas->utils->delete_object_meta($resource_id, "accelerator_pagespeed_history");
			//delete_post_meta($resource_id, "pegasaas_accelerator_pagespeed_history");
		}
		//delete_option("pegasaas_accelerator_pagespeed_history_0"); // for the blog front page
		delete_option("pegasaas_accelerator_pagespeed_scores");
		delete_option("pegasaas_accelerator_pagespeed_score_request_tokens");
		delete_option("pegasaas_accelerator_pagespeed_score_requests");		
		$pegasaas->db->delete("pegasaas_performance_scan", array("scan_type" => "pagespeed"));
		$pegasaas->db->delete("pegasaas_api_request", array("request_type" => "pagespeed"));

	}

	function pegasaas_fetch_pagespeed_opportunities_html() {
		global $pegasaas;
		
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$completed_request = array();
			$completed_request['resource_id'] = $_POST['resource_id'];
			
			$page_score_data = PegasaasUtils::get_object_meta($_POST['resource_id'], "accelerator_pagespeed_history");

			if (is_array($page_score_data)) {
				$page_score_data_record = array_pop($page_score_data);
				$last_scan = date_i18n( get_option( 'date_format' ), $page_score_data_record['when'] ) . " (".date_i18n( get_option( 'time_format' ), $page_score_data_record['when'] ).")"; 
			} else {
				$last_scan = "Unknown";
			}
			
			$page_score_details = $this->get_page_speed_opportunities($_POST['resource_id']);
			ob_start();
			$pegasaas->interface->render_page_overview($_POST['resource_id']); 
			
			$completed_request['html'] = ob_get_clean();

			print json_encode($completed_request);

		} else {
			print json_encode(array("status" => -1));
		}		
		wp_die();
	}

	function pegasaas_check_queued_pagespeed_score_requests() {
		global $pegasaas;
		PegasaasUtils::log("pegasaas_check_queued_pagespeed_score_requests START", "script_execution_benchmarks");

		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$pagespeed_score_requests_results = $pegasaas->db->get_results("pegasaas_api_request", array("request_type" => "pagespeed"));
			$pagespeed_score_requests = array();
			foreach ($pagespeed_score_requests_results as $request_record) {
				$resource_id = $request_record->resource_id;
				$pagespeed_score_requests["$resource_id"] = json_decode($request_record->data, true);
			}
			
		
			$pending_requests = $_POST['pending_requests'];
			
			$completed_requests = array();
			if (is_array($pending_requests)) {
				foreach ($pending_requests as $resource_id) {
					if (!array_key_exists($resource_id, $pagespeed_score_requests)) {
						
						$post_pagespeed = 	$this->pegasaas_fetch_pagespeed_last_scan($resource_id);
						
						
						// do not return anything if the time-to-first-byte is NULL, otherwise the result will end up showing a "Rescan Required" message which 
						// in most cases is not correct.  A NULL value simply means that a scan has never been completed
						
						if ( $post_pagespeed['meta']['mobile']['lab_data']['time-to-first-byte']['value'] == NULL) {
							
						} else {
							$completed_requests["$resource_id"]['mobile_score']		= ceil($post_pagespeed['mobile_score']);

							$completed_requests["$resource_id"]['mobile_ttfb']		= $post_pagespeed['meta']['mobile']['lab_data']['time-to-first-byte']['value'];
							$completed_requests["$resource_id"]['mobile_fcp']		= $post_pagespeed['meta']['mobile']['lab_data']['first-contentful-paint']['value'];
							$completed_requests["$resource_id"]['mobile_fmp']		= $post_pagespeed['meta']['mobile']['lab_data']['first-meaningful-paint']['value'];
							$completed_requests["$resource_id"]['mobile_tbt']		= $post_pagespeed['meta']['mobile']['lab_data']['total-blocking-time']['value'];
							$completed_requests["$resource_id"]['mobile_cls']		= $post_pagespeed['meta']['mobile']['lab_data']['cumulative-layout-shift']['value'];
							$completed_requests["$resource_id"]['mobile_lcp']		= $post_pagespeed['meta']['mobile']['lab_data']['largest-contentful-paint']['value'];
							$completed_requests["$resource_id"]['mobile_si']		= $post_pagespeed['meta']['mobile']['lab_data']['speed-index']['value'];
							$completed_requests["$resource_id"]['mobile_fci']		= $post_pagespeed['meta']['mobile']['lab_data']['first-cpu-idle']['value'];
							$completed_requests["$resource_id"]['mobile_tti']		= $post_pagespeed['meta']['mobile']['lab_data']['interactive']['value'];

							$completed_requests["$resource_id"]['desktop_score']	= ceil($post_pagespeed['score']);
							$completed_requests["$resource_id"]['desktop_ttfb']		= $post_pagespeed['meta']['desktop']['lab_data']['time-to-first-byte']['value'];
							$completed_requests["$resource_id"]['desktop_fcp']		= $post_pagespeed['meta']['desktop']['lab_data']['first-contentful-paint']['value'];
							$completed_requests["$resource_id"]['desktop_fmp']		= $post_pagespeed['meta']['desktop']['lab_data']['first-meaningful-paint']['value'];
							$completed_requests["$resource_id"]['desktop_tbt']		= $post_pagespeed['meta']['desktop']['lab_data']['total-blocking-time']['value'];
							$completed_requests["$resource_id"]['desktop_cls']		= $post_pagespeed['meta']['desktop']['lab_data']['cumulative-layout-shift']['value'];							$completed_requests["$resource_id"]['desktop_lcp']		= $post_pagespeed['meta']['desktop']['lab_data']['largest-contentful-paint']['value'];
							$completed_requests["$resource_id"]['desktop_si']		= $post_pagespeed['meta']['desktop']['lab_data']['speed-index']['value'];
							$completed_requests["$resource_id"]['desktop_fci']		= $post_pagespeed['meta']['desktop']['lab_data']['first-cpu-idle']['value'];
							$completed_requests["$resource_id"]['desktop_tti']		= $post_pagespeed['meta']['desktop']['lab_data']['interactive']['value'];		
						}
					}
				}
			}
			$completed_requests["#summary#"]["total_pending_requests"] = sizeof($pagespeed_score_requests);
			print json_encode($completed_requests);
		} else {
			print json_encode(array("status" => -1));
		}		
		PegasaasUtils::log("pegasaas_check_queued_pagespeed_score_requests END", "script_execution_benchmarks");

	 	wp_die();		
	}

	
	function pegasaas_request_image_data($die = true) { 
		global $pegasaas;

		
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			if ($_POST['resource_id'] == "") {
				print json_encode(array("status" => 0, "message" => 'Error: Blank Resource ID'));
			} else {
				$resource_id = $_POST['resource_id'];

				$nonce 		 = $resource_id."-".$pegasaas->utils->microtime_float();
				$location 	 = $pegasaas->utils->get_wp_location();
				$post_fields = array();
		
				$post_fields['url']		= $pegasaas->home_url().$_POST['resource_id'];
				$post_fields['version']	= 5;
				$post_fields['status']	= 1;
				$post_fields['command']	= "submit-image-data-request";
				//$post_fields['callback_url'] = ($_SERVER['HTTPS'] == "on" ? "https://" : "http://").$_SERVER['HTTP_HOST'].$location."/wp-admin/admin-ajax.php?wp_rid=".$_POST['resource_id']."&wp_n=".$nonce."&v=3";
				$post_fields['callback_url'] = admin_url( 'admin-ajax.php' )."?wp_rid=".$_POST['resource_id']."&wp_n=".$nonce."&v=3";

			
				
				
				$response = $this->api->post($post_fields, array("timeout" => $pegasaas->api->get_general_api_request_timeout(),
																 "blocking" => true));
				
				$data = json_decode($response, true);

				if ($data['api_error'] != "") {
					if ($die) { 
						print json_encode(array("status" => 0, "message" => 'Error: Problem communicating with pegasaas.io'));
					}
				} else {		
			
					
					// request failed for some reason
					 if ($data['image_data_request_status'] == 0) {
						print json_encode(array("status" => 0, "message" => $data['message']));	
					 } else if ($data['image_data_request_status'] == 1) {
						if ($pegasaas->utils->semaphore("pegasaas_accelerator_image_data_requests", $wait_time = 0, $stale_if_this_many_seconds_old = 20)) { 
							if ($die) { print json_encode(array("status" => 1, "message" => 'Request Submitted'));	}

							$image_data_requests_tokens = get_option("pegasaas_accelerator_image_data_request_tokens", array());
							$image_data_requests_tokens["$nonce"] = $_POST['resource_id'];
							update_option("pegasaas_accelerator_image_data_request_tokens", $image_data_requests_tokens);

							$image_data_requests = get_option("pegasaas_accelerator_image_data_requests", array());
							$image_data_requests["{$_POST['resource_id']}"]["$nonce"] = array("when_submitted" => time(), "nonce" => $nonce);
							update_option("pegasaas_accelerator_image_data_requests", $image_data_requests);
							$pegasaas->utils->release_semaphore("pegasaas_accelerator_image_data_requests");
						} 
					
					// recent request already exists, no score
					} else if ($data['image_data_request_status'] == 2) {
						if ($die) {	print json_encode(array("status" => 2, "message" => $data['image_data_status_message'])); }
					
					// recent request already exists, with a score	
					} else if ($data['image_data_request_status'] == 3) {
						$image_data_requests_tokens = get_option("pegasaas_accelerator_image_data_request_tokens", array());
						$image_data_requests = get_option("pegasaas_accelerator_image_data_requests", array());
						
						if ($image_data_requests_tokens["{$data['nonce']}"] == $_POST['resource_id']) {
							// remove the request token from the pending requests list
							unset($image_data_requests_tokens["{$data['nonce']}"]);
							set_option("pegasaas_accelerator_image_data_request_tokens", $image_data_requests_tokens);
							
							// remove the page/post from the pending requests list
							unset($image_data_requests["{$_POST['resource_id']}"]);
							set_option("pegasaas_accelerator_image_data_requests", $image_data_requests);
							
							// store data
							$time = time();
	
							
							//$post_pagespeed_history["{$time}"] = array("when" => $time, "score" => $data['score'], "meta" => $data['meta']);
							$pegasaas->utils->update_object_meta($_POST['resource_id'], "image_data", $data['meta']);

							if ($die) {
								print json_encode(array("status" => 3, "message" => $data['image_data_status_message']));
							}

						// invalid nonce returned
						} else {
							if ($die) { 
								print json_encode(array("status" => 0, "message" => $data['image_data_status_message']));
							}
						}
					// invalid payload
					} else {
						 if ($die) {
							print json_encode(array("status" => 0, "message" => "Unknown error. Trace: #3491"));
						 }
					}
				}
			}
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		if ($die) {
			wp_die(); // required in order to not have a trailing 0 in the json response
		}	
	}
	
	
	function request_pagespeed_score($resource_id, $is_rescan = false, $die = false) {
		global $pegasaas;

		
		
				$nonce = $resource_id."-".$pegasaas->utils->microtime_float();
				
				$location = $pegasaas->utils->get_wp_location();
				
			
				$post_fields = array();

				$slug = $pegasaas->utils->get_post_slug_from_resource_id($resource_id);
				
				if (strstr($slug, "http://") || strstr($slug, "https://")) {
						$the_url = $slug;
				} else {
						$the_url = $pegasaas->get_home_url().$slug;
				}		
		
				$post_fields['url']		= $the_url;
				$allows_gpsi_calls = $this->caching_allows_gpsi_calls();
				if ($allows_gpsi_calls) {
					if (strstr($post_fields['url'], "?")) {
						$post_fields['url'] .= "&";
					} else {
						$post_fields['url']	 .= "?";
					}
					
					$post_fields['url']	 .= "gpsi-call-".$pegasaas->utils->microtime_float();

				}				
				
				$post_fields['version']	= 5;
				$post_fields['status']	= 1;
				$post_fields['command']	= "submit-pagespeed-query";
				//$post_fields['callback_url'] = ($_SERVER['HTTPS'] == "on" ? "https://" : "http://").$_SERVER['HTTP_HOST'].$location."/wp-admin/admin-ajax.php?wp_rid=".$resource_id."&wp_n=".$nonce."&v=3";
				$post_fields['callback_url'] = admin_url( 'admin-ajax.php' )."?wp_rid=".$resource_id."&wp_n=".$nonce."&v=3";

				
		
				// send request and save response to variable
				$response = $pegasaas->api->post($post_fields, array("timeout"  => $pegasaas->api->get_general_api_request_timeout(), 
																	 "blocking" => false));
				
				$data = json_decode($response, true);

				if ($data['api_error'] != "") {
					if ($die) { 
						print json_encode(array("status" => 0, "message" => 'Error: Problem communicating with pegasaas.io'));
					}
				} else {		
				
					$fields = array("time" 			=> date("Y-m-d H:i:s", time()), 
									"request_type"	=> "pagespeed", 
									"resource_id"	=> $resource_id, 
									"nonce" 		=> $nonce);
					
					if ($is_rescan) {
						$fields['advisory'] = "is_rescan";
					}
					
					$pegasaas->db->add_record("pegasaas_api_request", $fields);
					
				}
			
		
	}
	
	
	function pegasaas_request_pagespeed_score($die = true, $is_rescan = false) {
		global $pegasaas;

		
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			if ($_POST['resource_id'] == "") {
				print json_encode(array("status" => 0, "message" => 'Error: Blank Resource ID'));
			} else {
				$resource_id = $_POST['resource_id'];
				$this->request_pagespeed_score($resource_id, $is_rescan, $die);
			}
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		if ($die) {
			wp_die(); // required in order to not have a trailing 0 in the json response
		}
	}

	
	
	function pegasaas_request_pagespeed_benchmark_score($die = true) {
		global $pegasaas;
		
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			if ($_POST['resource_id'] == "") {
				print json_encode(array("status" => 0, "message" => 'Error: Blank Resource ID'));
			} else {
				$resource_id = $_POST['resource_id'];

				$nonce = $resource_id."-".$pegasaas->utils->microtime_float();
				
				$location = $pegasaas->utils->get_wp_location();
				
		
				$post_fields = array();
				$post_fields['api_key'] = $_POST['api_key'];
				$post_fields['domain'] 	= $_SERVER["HTTP_HOST"];
				
				$slug = $pegasaas->utils->get_post_slug_from_resource_id($resource_id);
				
				if (strstr($slug, "http://") || strstr($slug, "https://")) {
						$the_url = $slug;
				} else {
						$the_url = $pegasaas->get_home_url().$slug;
				}	
				
				$post_fields['url']		= $the_url."?accelerate=off";			
				
				$post_fields['version']	= 5;
				$post_fields['status']	= 1;
				$post_fields['command']	= "submit-pagespeed-benchmark-query";
				//$post_fields['callback_url'] = ($_SERVER['HTTPS'] == "on" ? "https://" : "http://").$_SERVER['HTTP_HOST'].$location."/wp-admin/admin-ajax.php?wp_rid=".$_POST['resource_id']."&wp_n=".$nonce."&v=3";
				$post_fields['callback_url'] = admin_url( 'admin-ajax.php' )."?wp_rid=".$_POST['resource_id']."&wp_n=".$nonce."&v=3";


		
				// send request and save response to variable
				$response = $pegasaas->api->post($post_fields, 
												 array("timeout" => $pegasaas->api->get_general_api_request_timeout()));
				
				$data = json_decode($response, true);

				if ($data['api_error'] != "") {
					if ($die) { 
						print json_encode(array("status" => 0, "message" => 'Error: Problem communicating with pegasaas.io'));
					}
				} else {		
					$data = json_decode($response, true);
					
					
											$pegasaas->db->add_record("pegasaas_api_request", array("time" => date("Y-m-d H:i:s", time()), 
																				"request_type" => "pagespeed-benchmark", 
																				"resource_id" => $_POST['resource_id'], 
																				"nonce" => $nonce));
				
				}
			}
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		if ($die) {
			wp_die(); // required in order to not have a trailing 0 in the json response
		}
	}	 
	
	function clear_pagespeed_requests() {
		global $pegasaas;
		
		$pegasaas->utils->log("About to instruct API to dump all pagespeed-requests", "api");
		$response = $pegasaas->api->post(array("command" => "cancel-all-pagespeed-requests"), array("blocking" => false));
		$pegasaas->utils->log("PageSpeed requests cleared", "api");
			
		$pegasaas->db->delete("pegasaas_api_request", array("request_type" => "pagespeed"));
	}
	
	function clear_pagespeed_benchmark_requests() {
		global $pegasaas;

		$pegasaas->utils->log("About to instruct API to dump all benchmark pagespeed-requests", "api");
		$pegasaas->api->post(array("command" => "cancel-all-benchmark-pagespeed-requests"), array("blocking" => false));
		$pegasaas->utils->log("Benchmark PageSpeed requests cleared", "api");
		
		$pegasaas->db->delete("pegasaas_api_request", array("request_type" => "pagespeed-benchmark"));
	}	

	/*********************************************************************************
	 * HELPER
	 *********************************************************************************/

	function get_analytics_loaded($resources) {
		$known_analytics = array("Google Tag Manager" 	=> "www.googletagmanager.com", 
								 "Google Analytics" 	=> "www.google-analytics.com",
								 "Hotjar" 				=> "script.hotjar.com",
								 "Facebook" 			=> "connect.facebook.net",
								 "Kissmetrics" 			=> "kissmetrics.com",
								 "Outbrain" 			=> "outbrain.com",
								 "Quora" 				=> "quora.com",
								 "Bing Analytics" 		=> "bat.bing.com");
		
		$found = array();
		if (is_array($resources)) {
			foreach ($resources as $loaded_resource) {

				foreach ($known_analytics as $provider => $signature) {

					if (strstr($loaded_resource['url'], $signature)) {
						$found["$provider"] = $provider;

					}
				}
			}
		}
	
		
		return $found;
	}
	
	function get_adnetwork_loaded($resources) {
		$known_adnetwork = array("Google AdSense" => "googleads.g.doubleclick.net");
		$found = array();
		if (is_array($resources)) { 
			foreach ($resources as $loaded_resource) {
				foreach ($known_adnetwork as $provider => $signature) {
					if (strstr($loaded_resource['url'], $signature)) {
						$found[] = $provider;
					}
				}
			}
		}
		
		return $found;		
	}
	
	
	
	function condition_scan_data($data) {
		//var_dump 
		$conditioned_data = array();
		$conditioned_data["meta"]["mobile"]["lab_data"]["speed-index"]["value"] = $data["meta"]["mobile"]["lab_data"]["speed-index"]["value"];
		$conditioned_data["meta"]["mobile"]["lab_data"]["interactive"]["value"] = $data["meta"]["mobile"]["lab_data"]["interactive"]["value"];
		$conditioned_data["meta"]["mobile"]["lab_data"]["interactive"]["score"] = $data["meta"]["mobile"]["lab_data"]["interactive"]["score"];
		if ($data["meta"]["mobile"]["lab_data"]["time-to-first-byte"]["value"]) {
			$conditioned_data["meta"]["mobile"]["lab_data"]["time-to-first-byte"]["value"] = $data["meta"]["mobile"]["lab_data"]["time-to-first-byte"]["value"];	
		} else {
			$conditioned_data["meta"]["mobile"]["lab_data"]["time-to-first-byte"]["value"] = $data["meta"]["mobile"]["lab_data"]["server-response-time"]["value"];
		}
		$conditioned_data["meta"]["mobile"]["lab_data"]["first-contentful-paint"]["value"] 		= $data["meta"]["mobile"]["lab_data"]["first-contentful-paint"]["value"];
		$conditioned_data["meta"]["mobile"]["lab_data"]["first-meaningful-paint"]["value"] 		= $data["meta"]["mobile"]["lab_data"]["first-meaningful-paint"]["value"];
		$conditioned_data["meta"]["mobile"]["lab_data"]["first-cpu-idle"]["value"] 				= $data["meta"]["mobile"]["lab_data"]["first-cpu-idle"]["value"];
		$conditioned_data["meta"]["mobile"]["lab_data"]["largest-contentful-paint"]["value"] 	= $data["meta"]["mobile"]["lab_data"]["largest-contentful-paint"]["value"];
		$conditioned_data["meta"]["mobile"]["lab_data"]["total-blocking-time"]["value"] 		= $data["meta"]["mobile"]["lab_data"]["total-blocking-time"]["value"];
		$conditioned_data["meta"]["mobile"]["lab_data"]["cumulative-layout-shift"]["value"] 	= $data["meta"]["mobile"]["lab_data"]["cumulative-layout-shift"]["value"];
			
		$conditioned_data["meta"]["desktop"]["lab_data"]["speed-index"]["value"] = $data["meta"]["desktop"]["lab_data"]["speed-index"]["value"];
		$conditioned_data["meta"]["desktop"]["lab_data"]["interactive"]["value"] = $data["meta"]["desktop"]["lab_data"]["interactive"]["value"];
		$conditioned_data["meta"]["desktop"]["lab_data"]["interactive"]["score"] = $data["meta"]["desktop"]["lab_data"]["interactive"]["score"];
		if ($data["meta"]["mobile"]["lab_data"]["time-to-first-byte"]["value"]) {
			$conditioned_data["meta"]["desktop"]["lab_data"]["time-to-first-byte"]["value"] = $data["meta"]["desktop"]["lab_data"]["time-to-first-byte"]["value"];
		} else {
			$conditioned_data["meta"]["desktop"]["lab_data"]["time-to-first-byte"]["value"] = $data["meta"]["desktop"]["lab_data"]["server-response-time"]["value"];

		}
		$conditioned_data["meta"]["desktop"]["lab_data"]["first-contentful-paint"]["value"] 	= $data["meta"]["desktop"]["lab_data"]["first-contentful-paint"]["value"];
		$conditioned_data["meta"]["desktop"]["lab_data"]["first-meaningful-paint"]["value"] 	= $data["meta"]["desktop"]["lab_data"]["first-meaningful-paint"]["value"];
		$conditioned_data["meta"]["desktop"]["lab_data"]["first-cpu-idle"]["value"] 			= $data["meta"]["desktop"]["lab_data"]["first-cpu-idle"]["value"];
		$conditioned_data["meta"]["desktop"]["lab_data"]["largest-contentful-paint"]["value"] 	= $data["meta"]["desktop"]["lab_data"]["largest-contentful-paint"]["value"];
		$conditioned_data["meta"]["desktop"]["lab_data"]["total-blocking-time"]["value"] 		= $data["meta"]["desktop"]["lab_data"]["total-blocking-time"]["value"];
		$conditioned_data["meta"]["desktop"]["lab_data"]["cumulative-layout-shift"]["value"] 	= $data["meta"]["desktop"]["lab_data"]["cumulative-layout-shift"]["value"];


		
			return $conditioned_data;
	}
	
	function get_scanned_object_post_data($post, $resource_id, $object_type = "") {
	//	var_dump($post);
		global $pegasaas;
		$resource_id = rtrim($resource_id, "/")."/";	
		if ($post->is_category) {
			$permalink = get_category_link($post->cat_ID);
		} else {
			$permalink = $post->slug;
			if ($permalink == "") {
				$permalink = $resource_id;
			}
			
		
		}
		//print "permalink: $permalink\n";
	//$permalink = rtrim($resource_id, "/")."/";	
		
		//print "resource id (3): {$resource_id}\n";
		$post_pagespeed_history = $this->pegasaas_fetch_pagespeed_last_scan($resource_id, true);
		/*
		$post_pagespeed_history = $pegasaas->db->get_results("pegasaas_performance_scan", array("resource_id" => $resource_id,
																								 "scan_type" => "pagespeed"
																				  ), "time DESC", "resource_id", 1);
*/
		$post_pagespeed_benchmark_history = $this->pegasaas_fetch_pagespeed_benchmark_last_scan($resource_id, true);
/*
		$post_pagespeed_benchmark_history = $pegasaas->db->get_results("pegasaas_performance_scan", array("resource_id" => $resource_id,
																								 "scan_type" => "pagespeed-benchmark"
																				  
																				  ), "time DESC", "resource_id", 1);
																				  */
		$accelerated_pages = get_option("pegasaas_accelerated_pages", array());

	
		if ($post_pagespeed_history) {
			//$post_data = json_decode($post_pagespeed_history[0]->data, true);
			$post_data = $post_pagespeed_history;
			$post_data['meta']['mobile']['opportunities']['total-byte-weight']['details']['items'] = "v";
			$post_data['analytics_detected']  = $this->get_analytics_loaded($post_data['meta']['mobile']['opportunities']['total-byte-weight']['details']['items']);
			$post_data['adnetworks_detected'] = $this->get_adnetwork_loaded($post_data['meta']['mobile']['opportunities']['total-byte-weight']['details']['items']);
			$post_data['accelerated_scan'] = $post_pagespeed_history;
			
		} else {
			// check to see if there is a requested queued
			$has_existing_request = $pegasaas->db->has_record("pegasaas_api_request", array("request_type" => "pagespeed", "resource_id" => $resource_id));
			//$existing_requests 		= get_option("pegasaas_accelerator_pagespeed_score_requests", array());
			
			//if (array_key_exists($resource_id, $existing_requests)) {
			if ($has_existing_request) {
				$post_data['score'] = -1;
				$post_data['mobile_score'] = -1;
			} else {
				$post_data['score'] = -2;
				$post_data['mobile_score'] = -2;
			}
			
		}
	
		if ($post_pagespeed_benchmark_history) {
			
			
			$post_data['benchmark_score'] = $post_pagespeed_benchmark_history['score'];
			$post_data['benchmark_mobile_score'] = $post_pagespeed_benchmark_history['mobile_score'];
			$post_data['benchmark_desktop_load_time'] = $post_pagespeed_benchmark_history['meta']['load_times']['desktop'];
			$post_data['baseline_scan'] = $post_pagespeed_benchmark_history;
			//var_dump($post_pagespeed_benchmark_history); 
		} else {
		
			$has_existing_request = $pegasaas->db->has_record("pegasaas_api_request", array("request_type" => "pagespeed-benchmark", "resource_id" => $resource_id));

			// check to see if there is a requested queued
			if ($has_existing_request) {

				$post_data['benchmark_score'] = -1;
				$post_data['benchmark_mobile_score'] = -1;
			} else {

				$post_data['benchmark_score'] = -2;
				$post_data['benchmark_mobile_score'] = -2;
			
			}
			
		}
		
		
		$permalink = str_replace($pegasaas->get_home_url('', 'http'), "", $permalink);
		$permalink = str_replace($pegasaas->get_home_url('', 'https'), "", $permalink);
		
		if ($permalink == "") {
			$permalink = "/";
		}	
		$accelerated = 1;

		$pid = rtrim($permalink, '/').'/';
		
		if (!array_key_exists($pid, $accelerated_pages) && $accelerated_pages["{$pid}"] != 1){
			$accelerated = 0;	
		}
						
		
		if(!isset($post_data['when']) ){						$post_data['when']								=NULL;}
		if(!isset($post_data['score']) ){						$post_data['score']								=NULL;}
		if(!isset($post_data['mobile_score']) ){				$post_data['mobile_score']						=NULL;}
		if(!isset($post_data['benchmark_score']) ){				$post_data['benchmark_score']					=NULL;}
		if(!isset($post_data['benchmark_mobile_score']) ){		$post_data['benchmark_mobile_score']			=NULL;}
		if(!isset($post_data['benchmark_desktop_load_time']) ){	$post_data['benchmark_desktop_load_time']		=NULL;}
		if(!isset($post_data['meta']) ){						$post_data['meta']['load_times']['desktop']		=NULL;}
		if(!isset($post_data['meta']) ){						$post_data['meta']['desktop_load_time']			=NULL;}
		
		//$resource_id = $permalink;
		//$resource_id = rtrim($resource_id, "/")."/";
		//$resource_id = str_replace(array("https://", "http://"), array("/", "/"), $resource_id);
		$content_url = content_url();

		if (false && strstr($post->slug, "broken") ) {
			print "<pre>";
			var_dump($post);
			print "</pre>";
		}
		$object = array(
			"id"						=> $post->ID,
			//"pid"						Set Below //
			"name"						=> $post->post_title, 
			"slug"						=> $post->slug, 
				
			"resource_id"				=> $post->resource_id, 
			"type"						=> ucwords($post->post_type),
			"post_type"					=> $post->post_type,
			"is_category"				=> $post->is_category,
			"category_post_type"		=> $post->category_post_type,
			"last_scan" 				=> $post_data['when'], 
			"score"						=> $post_data['score'], 
			"version"					=> $post_data['version'], 
			"mobile_score"				=> $post_data['mobile_score'], 
			//"load_time"				Set Below //
			"benchmark_score"			=> $post_data['benchmark_score'],
			"benchmark_mobile_score"	=> $post_data['benchmark_mobile_score'],
			"benchmark_load_time"		=> $post_data['benchmark_desktop_load_time'],
			"baseline_scan"				=> $this->condition_scan_data($post_data['baseline_scan']),
			"accelerated_scan"			=> $this->condition_scan_data($post_data['accelerated_scan']),
			/* "needs"						=> $this->get_page_speed_opportunities($resource_id, "desktop"), 
			"mobile_needs"				=> $this->get_page_speed_opportunities($resource_id, "mobile"), 
			*/
			"needs" => array(),
			"mobile_needs" => array(),
			"last_scanned"				=> $post_data['when'],
			"accelerated"				=> $accelerated,
			"staging_mode_disabled"     => $post->staging_mode_disabled,
		/*	"benchmark_needs" 			=> $this->get_page_benchmark_speed_opportunities($resource_id), "last_scanned" => $post_data['when']*/
			"benchmark_needs" 			=> array(),
			"display_wpml_icon"			=> $post->language == "" ? false : true,
			"wpml_icon_class"			=> $post->language == "" ? "hidden" : "",
			"wpml_icon"					=> $content_url."/plugins/sitepress-multilingual-cms/res/flags/{$post->language}.png",
			"webp_image_issue_class"	=> "hidden",	
			"dom_size_issue_class"		=> "hidden",	
			"google_ads_issue_class"		=> "hidden"	
		);	
		
		
		
		
		if ($pegasaas->is_free() && isset($post_data['meta']['mobile']['opportunities'])) {
		   $object['webp_image_issue_class'] = isset($post_data['meta']['mobile']['opportunities']['uses-webp-images']['score']) && ($post_data['meta']['mobile']['opportunities']['uses-webp-images']['score'] < 1 || $post_data['meta']['desktop']['opportunities']['uses-webp-images']['score'] < 1) ? "" : "hidden";	
		   $object['dom_size_issue_class']   = isset($post_data['meta']['mobile']['opportunities']['dom-size']['score']) && ($post_data['meta']['mobile']['opportunities']['dom-size']['score'] < 0.75 || $post_data['meta']['desktop']['opportunities']['dom-size']['score'] < 0.75) ? "" : "hidden";	
		   $object['webp_image_issue_class'] = "";
			$has_google_ads = false;
			$third_party_script_objects = $post_data['meta']['mobile']['lab_data']['third-party-summary']['details']['items'];
			foreach ($third_party_script_objects as $item) {
				if (strstr($item['entity']['url'], "doubleclickbygoogle.com")){
					$has_google_ads = true;
					break;
				}
			}
		
			$object['google_ads_issue_class']   = ($has_google_ads ? "" : "hidden");	
		}
	
		if (($post->post_type == "page" && $object_type == "pages") || $object_type == "") {
			$object["pid"]				= $resource_id;
			$object["load_time"]		= $post_data['meta']['load_times']['desktop'];
		} else if (($post->post_type == "post" && $object_type == "posts") || $object_type == "") {
			$object["pid"]				= $resource_id;
			$object["load_time"]		= $post_data['desktop_load_time'];
		} else if ($object_type == "") {
			$object["pid"]				= $resource_id;
			$object["load_time"]		=$post_data['desktop_load_time'];
		}
		
		
		
		return $object;
	}
	
	function get_scanned_objects($order_by = "score_desc", $object_type = "") {
		global $pegasaas;
		global $test_debug;
		$critical_logging = false;
		
		
		$maximum_scanned_objects_to_display = 250;
		
		$objects			= array();
		
		
		if ($pegasaas->interface->results_post_type == "") {
			if ($pegasaas->interface->results_search_filter != "") { 
				$posts_array		= PegasaasUtils::get_all_pages_and_posts(true);
			} else {
				$posts_array		= PegasaasUtils::get_all_pages_and_posts();	
			}
		} else {
			$posts_array		= $pegasaas->utils->get_all_posts($pegasaas->interface->results_post_type, 
																  $pegasaas->interface->results_search_filter);
		}
		
		if ($test_debug) {
			PegasaasUtils::log_benchmark("All Pages And Posts size: ".sizeof($posts_array), "debug-li", 1);
		}
		if ($critical_logging) {
			PegasaasUtils::log("PegasaasScanner::get_scanned_objects(): APAP Size: ".sizeof($posts_array));
		}		
		
		$count				= 0;
		$accelerated_pages	= get_option("pegasaas_accelerated_pages", array());

		
		if ($critical_logging) {
			PegasaasUtils::log("PegasaasScanner::get_scanned_objects(): accelerated_pages size: ".sizeof($accelerated_pages));
		}		
		
		// if there are no acelerated pages, then either we're fetching the page metrics too early, or the user has no pages enabled
		// so we should at least attempt to refresh the all-pages-and-posts object
		if (sizeof($accelerated_pages) == 0) {
			if ($test_debug) {
				PegasaasUtils::log_benchmark("Accelerated Pages: ".sizeof($accelerated_pages), "debug-li", 1);
			}	
			sleep(1);
			$accelerated_pages	= get_option("pegasaas_accelerated_pages", array());
			PegasaasUtils::refresh_all_pages_and_posts();
			if ($pegasaas->interface->results_post_type == "") {
				if ($pegasaas->interface->results_search_filter != "" || true) { 
				//	print "BBB";
					$posts_array		= PegasaasUtils::get_all_pages_and_posts(true);
				} else {
					$posts_array		= PegasaasUtils::get_all_pages_and_posts();	
				}
			} else {
				//print "AAA";
				$posts_array		= $pegasaas->utils->get_all_posts($pegasaas->interface->results_post_type, 
																	  $pegasaas->interface->results_search_filter);
			}
		}
		//var_dump($posts_array);
		
		  $existing_requests 						= get_option("pegasaas_accelerator_pagespeed_score_requests", array());
		  if (!is_array($existing_requests)) {
			  $existing_requests = array();
		  }
		  $existing_benchmark_requests 				= get_option("pegasaas_accelerator_pagespeed_benchmark_score_requests", array());
		  if (!is_array($existing_benchmark_requests)) {
			  $existing_benchmark_requests = array();
		  }			
		
	
	//	print "accelerated pages: ".sizeof($accelerated_pages);
		
		foreach ($posts_array as $post) {
			if ($count >= $maximum_scanned_objects_to_display) {
				continue;
			}
		
		
			$post_data	= array();
			$resource_id = $post->resource_id;

			
			if ($post->accelerated) {
				$post_data	= $this->get_scanned_object_post_data($post, $resource_id);

				
				$pagespeed_scan_in_progress 			= is_array($existing_requests["{$resource_id}"]) && sizeof($existing_requests["{$resource_id}"]) > 0;
				$pagespeed_benchmark_scan_in_progress = is_array($existing_benchmark_requests["{$resource_id}"]) && sizeof($existing_benchmark_requests["{$resource_id}"]) > 0;
		
				if (PegasaasAccelerator::$settings['limits']['pagespeed_scanned_pages'] > sizeof($objects)) {
					if ($post_data['score'] == -2) {
						$post_data['score'] = NULL;
					}
					if ($post_data['mobile_score'] == -2) {
						$post_data['mobile_score'] = NULL;
					}
					
					if ($post_data['benchmark_score'] == -2) {
						$post_data['benchmark_score'] = NULL;
					}
					if ($post_data['benchmark_mobile_score'] == -2) {
						$post_data['benchmark_mobile_score'] = NULL;
					}					
				}
				
				//for the interface, the baseline row
				if (($post_data['benchmark_score'] == -1 || $post_data['benchmark_score'] == NULL || $post_data['benchmark_score'] == '') && $post_data['benchmark_score'] != '0') { 
					$post_data['benchmark_scan_in_progress_class'] = 'benchmark-scan-in-progress benchmark-pending-scan';
				} else {
					$post_data['benchmark_scan_in_progress_class'] = '';
				}
				
				if (($post_data['benchmark_mobile_score'] == '' && $post_data['benchmark_mobile_score'] != '0') || $post_data['benchmark_mobile_score'] === 'NULL'|| $post_data['benchmark_mobile_score'] == -1) { 
					$post_data['benchmark_mobile_progress_class'] = "active"; 
				} else if ($post_data['benchmark_mobile_score'] >= 90) { 
					$post_data['benchmark_mobile_progress_class'] = "progress-bar-success"; 
				} else if ($post_data['benchmark_mobile_score'] >= 50) { 
					$post_data['benchmark_mobile_progress_class'] = "progress-bar-warning"; 
				} else { 
					$post_data['benchmark_mobile_progress_class'] = "progress-bar-danger"; 
				} 
				if (($post_data['benchmark_mobile_score'] == '' && $post_data['benchmark_mobile_score'] != '0')  || $post_data['benchmark_mobile_score'] === 'NULL' || $post_data['benchmark_mobile_score'] == -1) { 
					$post_data['benchmark_mobile_progress_bar_width'] =  "100"; 
				} else { 
					if ($post_data['benchmark_mobile_score'] < 10) { 
						$post_data['benchmark_mobile_progress_bar_width'] = "10"; 
					} else { 
						$post_data['benchmark_mobile_progress_bar_width'] = $post_data['benchmark_mobile_score']; 
					} 
				} 
				if ($post_data['benchmark_mobile_score'] > 0) {
					$post_data['benchmark_mobile_progress_bar_message'] = $post_data['benchmark_mobile_score']."%";
				} else {
					$post_data['benchmark_mobile_progress_bar_message'] = "Queued";
				}
							
				
				
				if (($post_data['benchmark_score'] == '' && $post_data['benchmark_score'] != '0') || $post_data['benchmark_score'] === 'NULL'|| $post_data['benchmark_score'] == -1) { 
					$post_data['benchmark_desktop_progress_class'] = "active"; 
				} else if ($post_data['benchmark_score'] >= 90) { 
					$post_data['benchmark_desktop_progress_class'] = "progress-bar-success"; 
				} else if ($post_data['benchmark_score'] >= 50) { 
					$post_data['benchmark_desktop_progress_class'] = "progress-bar-warning"; 
				} else { 
					$post_data['benchmark_desktop_progress_class'] = "progress-bar-danger"; 
				} 
				if (($post_data['benchmark_score'] == '' && $post_data['benchmark_score'] != '0')  || $post_data['benchmark_score'] === 'NULL' || $post_data['benchmark_score'] == -1) { 
					$post_data['benchmark_desktop_progress_bar_width'] =  "100"; 
				} else { 
					if ($post_data['benchmark_score'] < 10) { 
						$post_data['benchmark_desktop_progress_bar_width'] = "10"; 
					} else { 
						$post_data['benchmark_desktop_progress_bar_width'] = $post_data['benchmark_score']; 
					} 
				} 
				if ($post_data['benchmark_score'] > 0) {
					$post_data['benchmark_desktop_progress_bar_message'] = $post_data['benchmark_score']."%";
				} else {
					$post_data['benchmark_desktop_progress_bar_message'] = "Queued";
				}				
	
				
				
				//for the interface, the accelerated metrics section
				if ( $post_data['score'] == '' && $post_data['score'] != '0') { 
					$post_data['accelerated_scan_in_progress_class'] = 'accelerated-scan-in-progress accelerated-pending-scan';
				} else {
					$post_data['accelerated_scan_in_progress_class'] = '';
				}
				
				
				$post_data['this_one_scanning'] = true;
				if (($post_data['mobile_score'] == '' && $post_data['mobile_score'] != '0') || $pagespeed_scan_in_progress) { 				  
					
					  $post_data['accelerated_mobile_progress_class'] = "active"; 
					  $post_data['this_one_scanning'] = true;
					  
				
				  
				} else if ($post_data['mobile_score'] == -1) {
					if (PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining'] == 0) {
						$post_data['accelerated_mobile_progress_class'] = "progress-bar-paused"; 
					} else {
						$post_data['accelerated_mobile_progress_class'] = ""; 
				  	}
				} else if (!$pagespeed_scan_in_progress) { 
					if ($post_data['mobile_score'] >= 90) { 
						  $post_data['accelerated_mobile_progress_class'] = "progress-bar-success"; 
					  } else if ($post_data['mobile_score'] >= "50") { 
						  $post_data['accelerated_mobile_progress_class'] = "progress-bar-warning"; 
					  } else { 
						  $post_data['accelerated_mobile_progress_class'] = "progress-bar-danger"; 
					  } 
				} 	
				
				
				  
	
				
			
				
				if (($post_data['mobile_score'] == '' && $post_data['mobile_score'] != '0')  || $pagespeed_scan_in_progress) { 
					$post_data['accelerated_mobile_progress_bar_width'] =  "100"; 
				} else { 
					if ($post_data['mobile_score'] == -1) { 
						$post_data['accelerated_mobile_progress_bar_width'] = "100"; 
					} else if (!($post_data['mobile_score'] == '' && $post_data['mobile_score'] != '0') && $post_data['mobile_score'] < 40) { 
					  	$post_data['accelerated_mobile_progress_bar_width'] = "40"; 			
					} else { 
						$post_data['accelerated_mobile_progress_bar_width'] = $post_data['mobile_score']; 
					} 
				} 

				if ($post_data['mobile_score'] >= 0 && $post_data['mobile_score'] != NULL) {
					$post_data['accelerated_mobile_progress_bar_message'] = $post_data['mobile_score']."%";
				} else {
					if ($pegasaas->is_standard() && PegasaasAccelerator::$settings['limits']['monthly_optimizations'] > 0 && PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining'] == 0) {
						$post_data['accelerated_mobile_progress_bar_message'] = "Pending Optimization Credits";
					} else if ($post_data['mobile_score'] == -1) {
						$post_data['accelerated_mobile_progress_bar_message'] = "Pending";
					} else {
						$post_data['accelerated_mobile_progress_bar_message'] = "Queued";
					}
				}
				
				
				
				
				if (($post_data['score'] == '' && $post_data['score'] != '0') || $pagespeed_scan_in_progress) { 				 			
					  $post_data['accelerated_desktop_progress_class'] = "active"; 
				} else if ($post_data['score'] == -1) {
					if (PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining'] == 0) {
						$post_data['accelerated_desktop_progress_class'] = "progress-bar-paused"; 
					} else {
						$post_data['accelerated_desktop_progress_class'] = ""; 
				  	}
				} else if (!$pagespeed_scan_in_progress) { 
					if ($post_data['score'] >= 90) { 
						  $post_data['accelerated_desktop_progress_class'] = "progress-bar-success"; 
					  } else if ($post_data['score'] >= 50) { 
						  $post_data['accelerated_desktop_progress_class'] = "progress-bar-warning"; 
					  } else { 
						  $post_data['accelerated_desktop_progress_class'] = "progress-bar-danger"; 
					  } 
				} 
				
				if (($post_data['score'] == '' && $post_data['score'] != '0')  || $pagespeed_scan_in_progress) { 
					$post_data['accelerated_desktop_progress_bar_width'] =  "100"; 
				} else { 
					if ($post_data['score'] == -1) { 
						$post_data['accelerated_desktop_progress_bar_width'] = "100"; 
					} else if (!($post_data['score'] == '' && $post_data['score'] != '0') && $post_data['score'] < 40) { 
					  	$post_data['accelerated_desktop_progress_bar_width'] = "40"; 			
					} else { 
						$post_data['accelerated_desktop_progress_bar_width'] = $post_data['score']; 
					} 
				} 

				if ($post_data['score'] >= 0) {
					$post_data['accelerated_desktop_progress_bar_message'] = $post_data['score']."%";
				} else {
					if ($pegasaas->is_standard() && PegasaasAccelerator::$settings['limits']['monthly_optimizations'] > 0 && PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining'] == 0) {
						$post_data['accelerated_desktop_progress_bar_message'] = "Pending Optimization Credits";
					} else if ($post_data['score'] == -1) {
							$post_data['accelerated_desktop_progress_bar_message'] = "Pending";
					} else {
						$post_data['accelerated_desktop_progress_bar_message'] = "Queued";
					}
				}
	 				
				$post_data["page_type_icon_class"] = "material-icons";	
				if ($post_data['type'] == "Page") { 
					$post_data["page_type_icon_text"] = "description";
				} else if ($post_data['type'] == "Category") {
					$post_data["page_type_icon_text"] = "folder";
				} else if ($post_data['type'] == "Woocommerce_product_category") {
					$post_data["page_type_icon_text"] = "store";
				} else if ($post_data['type'] == "Product") {
					$post_data["page_type_icon_text"] = "view_list";
				} else if ($post_data['type'] == "Woocommerce_product_tag") {
					$post_data["page_type_icon_text"] = "loyalty";
				} else {
					$post_data["page_type_icon_text"] = "notes";
				}
				
				$page_overrides = $pegasaas->utils->get_object_meta($resource_id, "accelerator_overrides");

				if (isset(PegasaasAccelerator::$settings['limits']['page_prioritizations']) && PegasaasAccelerator::$settings['limits']['page_prioritizations'] > 0) {
					$post_data["page_prioritizations_available_class"] = "page-prioritization-container";
					$post_data["fa_page_prioritization_class"] = "fa-flag";
					
					if ($page_overrides['prioritized'] == 1) {
						$post_data["fa_page_prioritization_class"] .= " prioritization-enabled";
					}
					
					
				} else {
					$post_data["page_prioritizations_available_class"] = "hidden";
				}

				$page_level_settings_only = array_merge($page_overrides);
				if (isset($page_level_settings_only['accelerated'])) {
					unset($page_level_settings_only['accelerated']);
				}
				if (isset($page_level_settings_only['prioritized'])) {
					unset($page_level_settings_only['prioritized']);
				}	
				if (isset($page_level_settings_only['staging_mode_page_is_live'])) {
					unset($page_level_settings_only['staging_mode_page_is_live']);
				}
				
				if (isset($page_level_settings_only['auto_clear_page_cache']) && $page_level_settings_only['auto_clear_page_cache'] == "") {
					unset($page_level_settings_only['auto_clear_page_cache']);
				}
				
				if (sizeof($page_level_settings_only) == 0) {
					$post_data['page_level_settings_icon'] = "settings";
					$post_data['page_level_settings_icon_class'] = "";
				} else {
					$post_data['page_level_settings_icon'] = "settings_suggest";
					$post_data['page_level_settings_icon_class'] = "visible";
				//	var_dump($page_level_settings_only);

				}
				
				
				
				//var_dump($post_data['baseline_scan']);
				$post_data["baseline_mobile_ttfb"] = $pegasaas->interface->render_page_metric($post_data['baseline_scan'], "ttfb", "mobile", false);
				$post_data["baseline_mobile_fcp"]  = $pegasaas->interface->render_page_metric($post_data['baseline_scan'], "fcp", "mobile", false);
				$post_data["baseline_mobile_fmp"] = $pegasaas->interface->render_page_metric($post_data['baseline_scan'], "fmp", "mobile", false);
				$post_data["baseline_mobile_lcp"] = $pegasaas->interface->render_page_metric($post_data['baseline_scan'], "lcp", "mobile", false);
				$post_data["baseline_mobile_fci"] = $pegasaas->interface->render_page_metric($post_data['baseline_scan'], "fci", "mobile", false);
				$post_data["baseline_mobile_si"]  = $pegasaas->interface->render_page_metric($post_data['baseline_scan'], "si", "mobile", false);
				$post_data["baseline_mobile_tti"] = $pegasaas->interface->render_page_metric($post_data['baseline_scan'], "tti", "mobile", false);
				$post_data["baseline_mobile_tbt"] = $pegasaas->interface->render_page_metric($post_data['baseline_scan'], "tbt", "mobile", false);
				$post_data["baseline_mobile_cls"] = $pegasaas->interface->render_page_metric($post_data['baseline_scan'], "cls", "mobile", false);
				
				$post_data["baseline_desktop_ttfb"] = $pegasaas->interface->render_page_metric($post_data['baseline_scan'], "ttfb", "desktop", false);
				$post_data["baseline_desktop_fcp"] = $pegasaas->interface->render_page_metric($post_data['baseline_scan'], "fcp", "desktop", false);
				$post_data["baseline_desktop_fmp"] = $pegasaas->interface->render_page_metric($post_data['baseline_scan'], "fmp", "desktop", false);
				$post_data["baseline_desktop_lcp"] = $pegasaas->interface->render_page_metric($post_data['baseline_scan'], "lcp", "desktop", false);
				$post_data["baseline_desktop_fci"] = $pegasaas->interface->render_page_metric($post_data['baseline_scan'], "fci", "desktop", false);
				$post_data["baseline_desktop_si"] = $pegasaas->interface->render_page_metric($post_data['baseline_scan'], "si", "desktop", false);
				$post_data["baseline_desktop_tti"] = $pegasaas->interface->render_page_metric($post_data['baseline_scan'], "tti", "desktop", false);
				$post_data["baseline_desktop_tbt"] = $pegasaas->interface->render_page_metric($post_data['baseline_scan'], "tbt", "desktop", false);
				$post_data["baseline_desktop_cls"] = $pegasaas->interface->render_page_metric($post_data['baseline_scan'], "cls", "desktop", false);

				$post_data["accelerated_mobile_ttfb"] = $pegasaas->interface->render_page_metric($post_data['accelerated_scan'], "ttfb", "mobile", false);
				$post_data["accelerated_mobile_fcp"] = $pegasaas->interface->render_page_metric($post_data['accelerated_scan'], "fcp", "mobile", false);
				$post_data["accelerated_mobile_fmp"] = $pegasaas->interface->render_page_metric($post_data['accelerated_scan'], "fmp", "mobile", false);
				$post_data["accelerated_mobile_lcp"] = $pegasaas->interface->render_page_metric($post_data['accelerated_scan'], "lcp", "mobile", false);
				$post_data["accelerated_mobile_fci"] = $pegasaas->interface->render_page_metric($post_data['accelerated_scan'], "fci", "mobile", false);
				$post_data["accelerated_mobile_si"] = $pegasaas->interface->render_page_metric($post_data['accelerated_scan'], "si", "mobile", false);
				$post_data["accelerated_mobile_tti"] = $pegasaas->interface->render_page_metric($post_data['accelerated_scan'], "tti", "mobile", false);
				$post_data["accelerated_mobile_tbt"] = $pegasaas->interface->render_page_metric($post_data['accelerated_scan'], "tbt", "mobile", false);
				$post_data["accelerated_mobile_cls"] = $pegasaas->interface->render_page_metric($post_data['accelerated_scan'], "cls", "mobile", false);
				
				$post_data["accelerated_desktop_ttfb"] = $pegasaas->interface->render_page_metric($post_data['accelerated_scan'], "ttfb", "desktop", false);
				$post_data["accelerated_desktop_fcp"] = $pegasaas->interface->render_page_metric($post_data['accelerated_scan'], "fcp", "desktop", false);
				$post_data["accelerated_desktop_fmp"] = $pegasaas->interface->render_page_metric($post_data['accelerated_scan'], "fmp", "desktop", false);
				$post_data["accelerated_desktop_lcp"] = $pegasaas->interface->render_page_metric($post_data['accelerated_scan'], "lcp", "desktop", false);
				$post_data["accelerated_desktop_fci"] = $pegasaas->interface->render_page_metric($post_data['accelerated_scan'], "fci", "desktop", false);
				$post_data["accelerated_desktop_si"] = $pegasaas->interface->render_page_metric($post_data['accelerated_scan'], "si", "desktop", false);
				$post_data["accelerated_desktop_tti"] = $pegasaas->interface->render_page_metric($post_data['accelerated_scan'], "tti", "desktop", false);
				$post_data["accelerated_desktop_tbt"] = $pegasaas->interface->render_page_metric($post_data['accelerated_scan'], "tbt", "desktop", false);
				$post_data["accelerated_desktop_cls"] = $pegasaas->interface->render_page_metric($post_data['accelerated_scan'], "cls", "desktop", false);
				
				
				$cache_exists = PegasaasAccelerator::$cache_map["{$post_data['slug']}"] != "";
				
				
				if ($cache_exists && PegasaasAccelerator::$cache_map["{$post_data['slug']}"]["css_validation_issue"] == true) {			
					$post_data['css_validation_issue_class'] = "";
				} else {
					$post_data['css_validation_issue_class'] = "hidden";
				}
				
				if (!$webp_image_issue) {			
					$post_data['web_image_issue_class'] = "";
				} else {
					$post_data['web_image_issue_class'] = "hidden";
				}
				
				$post_data['home_url'] = $pegasaas->get_home_url();
				
				$post_data['post_url_accelerate_off_encoded'] = urlencode($pegasaas->get_home_url().$post_data['slug']."?accelerate=off");
				$post_data['post_url_encoded'] = urlencode($pegasaas->get_home_url().$post_data['slug']);
 					  	
				if ($pegasaas->interface->results_issue_filter != "") {
					if ($this->has_need($pegasaas->interface->results_issue_filter, $post_data['needs'])) {
						$objects[]	= $post_data;
					}
			
				} else {
					$objects[]	= $post_data;
				}
				
				
				
				$count++;
			} else {
			   // if (sizeof($accelerated_pages) == 0 && $count++ < 10) {
				//	$objects[] = $this->get_scanned_object_post_data($post, $resource_id);
				//}
			//	print "<Pre clas='admin'>post not accelerated {$resource_id}</pre>";
			}
		}	
			//print "c: ".sizeof($objects)."\n";
		if ($order_by == "score_desc") {
			uasort($objects, array($this, 'sort_scan_items_by_score_desc'));
		} else if ($order_by == "page_importance") {
			uasort($objects, array($this, 'sort_scan_items_by_page_importance'));
		}
		
		$count = 0;
		// $pegasaas->utils->console_log("GET SCANNED OBJECTS 2");

		foreach ($objects as $key => $value) {
			if ($count++ >= PegasaasAccelerator::$settings['limits']['pagespeed_scanned_pages'] ) {
				$objects["$key"]["benchmark_score"] = -1;	
				
			} 
			$pid = $value['resource_id'];
			
		
			if (!array_key_exists($pid, $accelerated_pages) && $accelerated_pages["{$pid}"] != 1){

				$objects["$key"]["score"] = -1;	
				
			}
		  
		}
		if ($order_by == "score_desc") {
			 uasort($objects, array($this, 'sort_scan_items_by_score_desc'));			
		} else if ($order_by == "page_importance") {
			uasort($objects, array($this, 'sort_scan_items_by_page_importance'));
		}
		
		$results_per_page = $pegasaas->interface->results_per_page;
		$offset 		  = ($pegasaas->interface->current_results_page - 1) * $results_per_page;

			//	 $pegasaas->utils->console_log("GET SCANNED OBJECTS 3");

		$pegasaas->interface->max_results_page = ceil(sizeof($objects) / $results_per_page);
		
		
		
		
		if ($results_per_page != "") {
			$objects = array_slice($objects, $offset, $results_per_page);
		}
		
		
		//print "a: ".sizeof($objects)."\n";
		
		$wpml_multi_domains_active = PegasaasUtils::wpml_multi_domains_active();
 
		if ($wpml_multi_domains_active) {
			global $sitepress;
			
			$current_language = $sitepress->get_current_language();
			$default_language = $sitepress->get_default_language();
			$wpml_domains = wpml_get_setting( 'language_domains' );
			$wpml_domains["$default_language"] = PegasaasUtils::get_home_domain();
		
			
			if ($current_language != $default_language) {
				$disable_filters = true;
			}

		//	print "Current Language ($current_language) // Default Language ($default_language)\n";
			//$disable_filters = true;
	//	} else if (PegasaasUtils::does_plugin_exists_and_active("wpml")){
	//		global $sitepress;
	//		$current_language = $sitepress->get_current_language();
	//		$default_language = $sitepress->get_default_language();
		//	print "Current Language ($current_language) // Default Language ($default_language)\n";

		} else {
		//	print "wmpl_multi_domains not active\n";
		}
		
		if ($disable_filters) {
			$page_link_filter = PegasaasUtils::get_filter("page_link");
		//	print "<br>page link filter is ".$page_link_filter."<br>";
			$post_link_filter = PegasaasUtils::get_filter("post_link");
		//	print "post link filter is ".$post_link_filter."<br>";
			remove_all_filters("page_link");
			remove_all_filters("post_link");
		}
		
		foreach ($objects as $x => $result) {
	//		var_dump($objects[$x]);
			if ($objects[$x]['slug'] == NULL) {
				
					$objects[$x]['slug'] = PegasaasUtils::get_resource_slug($result['id']);
				
			} 
			if ($test_debug) {
				PegasaasUtils::log_benchmark("Post Name {$result['id']} is {$result['name']}", "debug-li", 1);
			}
		}
		
		if ($disable_filters) {
			PegasaasUtils::set_filter("page_link", $page_link_filter);
			PegasaasUtils::set_filter("post_link", $post_link_filter);	
		}
			//	 $pegasaas->utils->console_log("GET SCANNED OBJECTS 4");
		if ($test_debug) {
			PegasaasUtils::log_benchmark("Objects size: ".sizeof($objects), "debug-li", 1);
		}
		//print "<pre>";
		//var_dump($objects);
		//print "</pre>";
		return $objects;
	}
	
	function has_need($need, $opportunities) {
		foreach ($opportunities as $opportunity) {
			if ($need == "minification" && $opportunity['rule'] == "Minification") {
				return true;
			} else if ($need == "browser-caching" && $opportunity['rule'] == "LeverageBrowserCaching") {
				return true;
			} else if ($need == "image-optimization" && $opportunity['rule'] == "OptimizeImages") {
				return true;
			} else if ($need == "server-response-time" && $opportunity['rule'] == "ServerResponseTime") {
				return true;
			} else if ($need == "defer-render-blocking-resources" && $opportunity['rule'] == "MinimizeRenderBlockingResources") {
				return true;
			} else if ($need == "visible-content-prioritization" && $opportunity['rule'] == "PrioritizeVisibleContent") {
				return true;
			}
		}
	
		return false;

		
	}

	function get_page_speed_opportunities($resource_id, $context = "desktop") {
		global $pegasaas;
		
		$opportunities 			= array();
		
		//$post_pagespeed_history = PegasaasUtils::get_object_meta($resource_id, "accelerator_pagespeed_history");
		
		$post_data = $this->pegasaas_fetch_pagespeed_last_scan($resource_id, true);
		
	//	if ($post_pagespeed_history != "") {
		if (is_array($post_data)) {
		//	$post_data = array_pop($post_pagespeed_history);
			
			//Adam: Simplified all the logic.  Original comment below.
			//The only difference wass in the "rule_long_description" and foreach loop

			$metaArrayToLoopThrough = array();
			if ($post_data['version'] == 5) {
			
				if ($context == "desktop" && is_array($post_data['meta']['desktop']) && is_array($post_data['meta']['desktop']['opportunities'])) {
					$metaArrayToLoopThrough=$post_data['meta']['desktop']['opportunities'];
					$metaMode='desktop';	
				} else if ($context == "mobile" && is_array($post_data['meta']['mobile']) && is_array($post_data['meta']['mobile']['opportunities'])) {
					$metaArrayToLoopThrough=$post_data['meta']['mobile']['opportunities'];
					$metaMode='mobile';	
				}						
			} else {
				if (is_array($post_data['meta']) && isset($post_data['meta']['formattedResults']) && is_array($post_data['meta']['formattedResults']) && is_array($post_data['meta']['formattedResults']['ruleResults']) ) { 
					$metaArrayToLoopThrough=$post_data['meta']['formattedResults']['ruleResults'];
					$metaMode='';	
				} else if ($context == "desktop" && is_array($post_data['meta']['desktop']) && is_array($post_data['meta']['desktop']['formattedResults']) && is_array($post_data['meta']['desktop']['formattedResults']['ruleResults']) ) {
					$metaArrayToLoopThrough=$post_data['meta']['desktop']['formattedResults']['ruleResults'];
					$metaMode='desktop';	
				} else if ($context == "mobile" && is_array($post_data['meta']['mobile']) && is_array($post_data['meta']['mobile']['formattedResults']) && is_array($post_data['meta']['mobile']['formattedResults']['ruleResults']) ) {
					$metaArrayToLoopThrough=$post_data['meta']['mobile']['formattedResults']['ruleResults'];
					$metaMode='mobile';	
				}		
			}

			if(isset($metaArrayToLoopThrough ) ){
			foreach ($metaArrayToLoopThrough as $rule => $data) {
				if ($post_data['version'] == 5) {
					if ($data['score'] < 1 && $data['score'] != NULL) {
						$opportunityToAdd = array(
							"rule" 					=> $rule, 
							"rule_name" 			=> $this->get_rule_name($rule), 
							"rule_description" 		=> $this->get_rule_description($rule, "accelerated", $data), 
							"rule_icon" 			=> $this->get_rule_icon($rule), 
							"rule_score" 			=> $data['score'],
							"rule_raw_data" 		=> $data,  
						);
						switch($metaMode){
							case 'desktop': $opportunityToAdd["rule_long_description"]=$this->get_rule_description($rule, "accelerated", $data);break;
							case 'mobile':	$opportunityToAdd["rule_long_description"]=$this->get_rule_description($rule, "accelerated", $data);break;
						}	
						$opportunities[]=$opportunityToAdd; 
					}
				} else {
					if ($data['ruleImpact'] > 0) {
						$opportunityToAdd= array(
							"rule" 					=> $rule, 
							"rule_name" 			=> $this->get_rule_name($rule), 
							"rule_description" 		=> $data['localizedRuleName'], 
							"rule_icon" 			=> $this->get_rule_icon($rule), 
							"rule_impact" 			=> $data['ruleImpact'],
							"rule_raw_data" 		=> $data,
						);
						switch($metaMode){
							case '':		$opportunityToAdd["rule_long_description"]="<div class='score-worth'>Worth ".number_format($data['ruleImpact'], 2, '.', '')." points</div>{$rule_label}<br/><br/>".$this->get_rule_description($rule, "accelerated", $data);break;
							case 'desktop': $opportunityToAdd["rule_long_description"]=$this->get_rule_description($rule, "accelerated", $data);break;
							case 'mobile':	$opportunityToAdd["rule_long_description"]=$this->get_rule_description($rule, "accelerated", $data);break;
						}	
						$opportunities[]=$opportunityToAdd;
					}	
				}
			}
			}
		
		}
		if ($post_data['version'] == 5) {
			uasort($opportunities, array($this, "sort_page_speed_opportunities_v5"));
		} else {
			uasort($opportunities, array($this, "sort_page_speed_opportunities"));
		}
		return $opportunities;
	}
	
	function get_rule_description($id, $condition, $raw_data) {
		//Adam:  Why do some of these have new lines in their return messages, when others do not?
		global $pegasaas;

		if ($id == "LeverageBrowserCaching") {
			if ($condition == "benchmarked") {
				return "
				
				Load time can be dramatically reduced on secondary calls to your website resources (CSS, JavaScript, &amp; images) by instructing your web browser to 'cache' those resources for a particular length of time.<br/><br/>Accelerator automatically enables this for you.
				";
			} else {
				$return = "It looks like there are some resources that need to have browser caching enabled.";
				$return .= "<ul>";
				foreach ($raw_data['urlBlocks'][0]['urls'] as $url) {
					if(isset($url['results']) ){
						$return .= "<li>".$url['results']['args'][0]['value']."<li>";
					}
				}
				$return .= "</ul>";
				if ($raw_data['ruleImpact'] < 3) {
					$non_cachable_resources = $this->number_of_non_cachable_resources($raw_data['urlBlocks'][0]['urls']);
					if ($non_cachable_resources > 0) {
						$return = "It looks like there are {$non_cachable_resources} non-cachable resources that are triggering a warning by Google PageSpeed Insights.  Because we can't cache them (because doing so would break their functionality), <b>you can ignore this issue.</b>";
					} else {
					}
				}
				return $return;
			}
		} else if ($id == "MainResourceServerResponseTime") {
			if ($condition == "benchmarked") {
				$return = "If your server is overloaded, or your page is very large, this can cause your server to respond slowly.<br/><br/>Accelerator has built in <b>page caching</b> which helps to reduce the server response time.";
			} else {
				$return = "If your server is overloaded, or your page is very large, this can cause your server to respond slowly.<br/><br/>";
				if (PegasaasAccelerator::$settings['settings']['page_caching']['status'] == 1) {
					$return = "Accelerator already attempts to speed up the server response time with page caching.  It may be that this score was the result of a page not yet being fully cached in which case you can just re-scan this page for a fresh score.  It may be also be that this page is a non-cachable page (such as a dynamic ecommerce page).   If neither of these situations are the case, then you should look at investing in faster web hosting. <br/><br/> <button class='btn btn-default btn-rescan-page'>Try Re-scanning Page<span></span></button> <button class='btn btn-default btn-rescan-all-pages'>Re-scan ALL Pages<span></span></button>";
				} else {
					$return = "The Page Caching feature of Pegasaas Accelerator is not currently enabled.  We recommend you enable this feature to speed up the response time of your web server.  Once enabled, you can initiate a re-scan of this page.<br/><br/><button class='btn btn-default btn-enable-caching'>Enable Page Caching<span></span></button> <button class='btn btn-default btn-rescan-page'>Re-scan Page<span></span></button> <button class='btn btn-default btn-rescan-all-pages'>Re-scan ALL Pages<span></span></button>";
				}
			}
			return $return;
		} else if ($id == "MinimizeRenderBlockingResources") {
			if ($condition == "benchmarked") {
				$return = "Render blocking resources are JavasScript and CSS files that must be loaded <b>before</b> your HTML can be rendered.<br/><br/>  For best possible render times, those resources should be 'deferred' to the end of the page.<br/><br/>  Accelerator does this for you. ";
			} else {
				if (PegasaasAccelerator::$settings['settings']['cdn']['status'] == 1) {
					$return = "The 'Deferral of Render Blocking Resources' feature of Pegasaas Accelerator is enabled, however the scan reports that some resources are not deferred.  We recommend you first try rescanning this page.  If that does not remvoe the error, please initiate a ticket.<br><br><button class='btn btn-default btn-rescan-page'>Re-scan Page<span></span></button> <button class='btn btn-default btn-rescan-all-pages'>Re-scan ALL Pages<span></span></button>";
				} else {
					$return = "The 'Deferral of Render Blocking Resources' feature of Pegasaas Accelerator is not currently enabled.  We recommend you enable this feature to speed up the render time of your web page.  Once enabled, you can initiate a re-scan of this page.<br><br><button class='btn btn-default btn-enable-deferral-rbr'>Enable this Feature<span></span></button> <button class='btn btn-default btn-rescan-page'>Re-scan Page<span></span></button> <button class='btn btn-default btn-rescan-all-pages'>Re-scan ALL Pages<span></span></button>";
				}
			}
			return $return;
		} else if ($id == "OptimizeImages") {
			if ($condition == "benchmarked") {
				$return = "Images are often uploaded in their raw form.  Even if pre-optimized before uploading them to WordPress, WordPress often (unintentionally) add bloats to images.  In addition, many images are not sized appropriately for the region they are to be displayed in.<br/><br/>  Accelerator auto-optimzes your images for you.";
			} else {
			if (PegasaasAccelerator::$settings['settings']['image_optimization']['status'] == 0) {
				$return = "It looks like you have the Image Optimization feature of Pegasaas disabled.  Enable this feature to auto-accelerate your images.";
			} else {
					$return_data		= "";
					$url_data			= "<ul>";
					$just_original_urls	= "";
					foreach ($raw_data['urlBlocks'][0]['urls'] as $url) {
						
						$just_original_urls .= $url['result']['args'][0]['value'];
						$theurl = $url['result']['args'][0]['value'];
						$theurl = str_replace("http://", "", $theurl);
						$theurl = str_replace("https://", "", $theurl);
						$theurl = str_replace($_SERVER['HTTP_HOST'], "", $theurl);
						$url_data .= "<li>".$theurl."</li>";
					}
					$url_data .= "</ul>";
					if (PegasaasAccelerator::$settings['settings']['cdn']['status'] == 1 && strstr($just_original_urls, $_SERVER['HTTP_HOST'])) {
						$return .= "First, try <a href='javascript:purge_and_scan_page()'>purging your HTML page cache and then re-scanning the page</a>.  If that does not work, then";
						$return .= " try watching our video on setting widths and heights for your website images for the following images:";
						$return .= $url_data;
					} else {
						$return = "It looks like possibly the following images could have the width and height attributes set more accurately.";
						$return .= $url_data;
					}
				}
			}
			return $return;
		} else if ($id == "EnableGzipCompression") {
			return "
			Enabling server-side compression is usually the first step that should be taken to optimize the load time for your web pages.  Enabling compression allows the server to reduce the size of your web page (much like 'zipping' or 'compressing' a file) on-the-fly.  Your web browser automatically will 'uncompress' the web page, and then display it to you.<br/><br/>Using server-side compression can reduce the file-size (and transfer time) by up to 75%.<br/><br/>Accelerator automatically enables this for you.
			
			";
		} else if ($id == "LeverageBrowserCaching") {
			return "chrome";
		} else if ($id == "MinifyCss") {
			return "
			Minifying CSS can reduce the file size (and transfer time) by up to 50% for your cascasing style sheets, by stripping out comments and unnecessary white space.<br/><br/>Accelerator automatically does this for you.
			";
		} else if ($id == "MinifyJavaScript") {
			return "
			
			Minifying JavaScript can reduce the file size (and transfer time) by up to 50% for your JavaScript files, by stripping out comments and unnecessary white space.<br/><br/>Accelerator automatically does this for you.
			";
		} else if ($id == "PrioritizeVisibleContent") {
			if ($condition == "benchmarked") {
				$return = "When deferring render blocking resources, your page may display a 'Flash of Unstyled Content' (aka FOUC).  By pre-loading the 'critical path CSS' into the beginning of your HTML page, this can be avoided.<br/><br/>Accelerator automatically detects and pre-loads the critical CSS for you.";
			} else {
				if (PegasaasAccelerator::$settings['settings']['inject_critical_css']['status'] == 1) {
					$return = "When deferring render blocking resources, your page may display a 'Flash of Unstyled Content' (aka FOUC).  By pre-loading the 'critical path CSS' into the beginning of your HTML page, this can be avoided.  This feature is already enabled, however.  We recommend first rebuilding the critical path CSS for this page, and then rescanning.  If that does not solve the error, please initiate a ticket.<br><br><button class='btn btn-default btn-rebuild-cpcss'>Re-Build Critical Path CSS<span></span></button>  <button class='btn btn-default btn-rescan-page'>Re-scan Page<span></span></button> ";
				} else {
					$return = "When deferring render blocking resources, your page may display a 'Flash of Unstyled Content' (aka FOUC).  By pre-loading the 'critical path CSS' into the beginning of your HTML page, this can be avoided.  You should first enablet this feature, and then re-scan the page.   If that does not solve the error, please initiate a ticket.<br><br><button class='btn btn-default btn-enable-cpcss'>Enable this Feature<span></span></button> <button class='btn btn-default btn-rescan-page'>Re-scan Page<span></span></button> ";
				}
			}
			return $return;
			//Adam: Overriden return param ???
			return "
			
			";
		} else if ($id == "metrics") {
			return "Colects all available metrics.";
		} else if ($id == "dom-size") {
			return "Browser engineers recommend pages contain fewer than ~1,500 DOM nodes. The sweet spot is a tree depth < 32 elements and fewer than 60 children/parent element. A large DOM can increase memory usage, cause longer <a href='https://developers.google.com/web/fundamentals/performance/rendering/reduce-the-scope-and-complexity-of-style-calculations' target='_blank'>style calculations</a>, and produce costly <a target='_blank' href='https://developers.google.com/speed/articles/reflow'>layout reflows</a>. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/tools/lighthouse/audits/dom-size'>Learn more</a>";

		} else if ($id == "uses-rel-preload") {
			if ($condition == "benchmarked") {
				$return = "It is recommend that you prioritize fetching resources that are currently requested later in page load. <a class='btn btn-info' href='https://developers.google.com/web/tools/lighthouse/audits/preload' target='_blank'>Learn more</a>";
			} else {
				$return = "It is recommend that you prioritize fetching resources that are currently requested later in page load. <a class='btn btn-info' href='https://developers.google.com/web/tools/lighthouse/audits/preload' target='_blank'>Learn more</a>";

			}
		} else if ($id == "unminified-javascript") {
			return "Minifying JavaScript files can reduce payload sizes and script parse time. <a class='btn btn-info' href='https://developers.google.com/speed/docs/insights/MinifyResources' target='_blank'>Learn more</a>";
		} else if ($id == "redirects") {
			return "Redirects introduce additional delays before the page can be loaded. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/tools/lighthouse/audits/redirects'>Learn more</a>";

		} else if ($id == "user-timings") {
			return "Consider instrumenting your app with the User Timing API to measure your app's real-world performance during key user experiences. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/tools/lighthouse/audits/user-timing'>Learn more</a>";

		} else if ($id == "first-meaningful-paint") {
			return "First Meaningful Paint measures when the primary content of a page is visible. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/tools/lighthouse/audits/first-meaningful-paint'>Learn more</a>";

			
		} else if ($id == "efficient-animated-content") {
			return "Large GIFs are inefficient for delivering animated content. Consider using MPEG4/WebM videos for animations and PNG/WebP for static images instead of GIF to save network bytes. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/fundamentals/performance/optimizing-content-efficiency/replace-animated-gifs-with-video/'>Learn more</a>";

		} else if ($id == "time-to-first-byte") {
			return "Time To First Byte identifies the time at which your server sends a response. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/tools/lighthouse/audits/ttfb'>Learn more</a>";

		} else if ($id == "render-blocking-resources") {
			return "Resources are blocking the first paint of your page. Consider delivering critical JS/CSS inline and deferring all non-critical JS/styles. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/tools/lighthouse/audits/blocking-resources'>Learn more</a>";

		} else if ($id == "uses-optimized-images") {
			return "Optimized images load faster and consume less cellular data. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/tools/lighthouse/audits/optimize-images'>Learn more</a>";

		} else if ($id == "uses-text-compression") {
			return "Text-based resources should be served with compression (gzip, deflate or brotli) to minimize total network bytes. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/tools/lighthouse/audits/text-compression'>Learn more</a>";

		} else if ($id == "uses-long-cache-ttl") {
			return "A long cache lifetime can speed up repeat visits to your page. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/tools/lighthouse/audits/cache-policy'>Learn more</a>";

		} else if ($id == "interactive") {
			return "Interactive marks the time at which the page is fully interactive. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/tools/lighthouse/audits/consistently-interactive'>Learn more</a>";

		} else if ($id == "font-display") {
			return "Leverage the font-display CSS feature to ensure text is user-visible while webfonts are loading. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/updates/2016/02/font-display'>Learn more</a>";

		} else if ($id == "estimated-input-latency") {
			return "The score above is an estimate of how long your app takes to respond to user input, in milliseconds, during the busiest 5s window of page load. If your latency is higher than 50 ms, users may perceive your app as laggy. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/tools/lighthouse/audits/estimated-input-latency'>Learn more</a>";

		} else if ($id == "uses-rel-preconnect") {
			return "Consider adding preconnect or dns-prefetch resource hints to establish early connections to important third-party origins. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/fundamentals/performance/resource-prioritization#preconnect'>Learn more</a>";

		} else if ($id == "bootup-time") {
			return "Consider reducing the time spent parsing, compiling, and executing JS. You may find delivering smaller JS payloads helps with this. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/tools/lighthouse/audits/bootup'>Learn more</a>";

		} else if ($id == "unminified-css") {
			return "Minifying CSS files can reduce network payload sizes. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/tools/lighthouse/audits/minify-css'>Learn more</a>";

		} else if ($id == "offscreen-images") {
			return "Consider lazy-loading offscreen and hidden images after all critical resources have finished loading to lower time to interactive. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/tools/lighthouse/audits/offscreen-images'>Learn more</a>";

		} else if ($id == "uses-responsive-images") {
			return "Serve images that are appropriately-sized to save cellular data and improve load time. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/tools/lighthouse/audits/oversized-images'>Learn more</a>";

		} else if ($id == "unused-css-rules") {
			return "Remove unused rules from stylesheets to reduce unnecessary bytes consumed by network activity. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/tools/lighthouse/audits/unused-css'>Learn more</a>";

		} else if ($id == "speed-index") {
			return "Speed Index shows how quickly the contents of a page are visibly populated. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/tools/lighthouse/audits/speed-index'>Learn more</a>";

		} else if ($id == "first-cpu-idle") {
			return "First CPU Idle marks the first time at which the page's main thread is quiet enough to handle input. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/tools/lighthouse/audits/first-interactive'>Learn more</a>";

		} else if ($id == "total-byte-weight") {
			return "Large network payloads cost users real money and are highly correlated with long load times. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/tools/lighthouse/audits/network-payloads'>Learn more</a>";

		} else if ($id == "mainthread-work-breakdown") {
			return "Consider reducing the time spent parsing, compiling and executing JS. You may find delivering smaller JS payloads helps with this.";

		} else if ($id == "first-contentful-paint") {
			return "First Contentful Paint marks the time at which the first text or image is painted. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/tools/lighthouse/audits/first-contentful-paint'>Learn more</a>";

		} else if ($id == "uses-webp-images") {
			return "Image formats like JPEG 2000, JPEG XR, and WebP often provide better compression than PNG or JPEG, which means faster downloads and less data consumption. <a class='btn btn-info' target='_blank' href='https://developers.google.com/web/tools/lighthouse/audits/webp'>Learn more</a>";

		} else if ($id == "critical-request-chains") {
			return "The Critical Request Chains below show you what resources are loaded with a high priority. Consider reducing the length of chains, reducing the download size of resources, or deferring the download of unnecessary resources to improve page load. <a class='btn btn-info' href='https://developers.google.com/web/tools/lighthouse/audits/critical-request-chains' target='_blank'>Learn more</a>";

		}

	
		
	}	

	function number_of_non_cachable_resources($urls) {
		$number_of_non_cachable_resources = 0;
		$non_cachable = array("googletagmanager", "google-analytics", "maps.google.com");
		
		
		foreach ($urls as $url) {
		
			if ($url['result']['args'][0]['type'] == "URL") {
				
				foreach ($non_cachable as $find) {
					if (strstr($url['result']['args'][0]['value'], $find)) {
						$number_of_non_cachable_resources++;
					}
				}
			}
		}
		return $number_of_non_cachable_resources;
	}	
	
	function get_rule_icon($id) {
		
		switch($id){
			case "LeverageBrowserCaching";			return "chrome";	break;
			case "MainResourceServerResponseTime";	return "database";	break;
			case "MinimizeRenderBlockingResources";	return "clock-o";	break;
			case "OptimizeImages";					return "image";		break;
			case "EnableGzipCompression";			return "compress";	break;
			case "LeverageBrowserCaching";			return "chrome";	break;
			case "MinifyCss";						return "css3";		break;
			case "MinifyJavaScript";				return "terminal";	break;
			case "PrioritizeVisibleContent";		return "eye";		break;
		}
		return $id;
	}		

	/*******************************************************************************
	 * SORTING
	 *******************************************************************************/
	function sort_page_speed_opportunities($a, $b) {
		if ($a['rule_impact'] == $b['rule_impact']) {
			return 0;
		}
		return ($a['rule_impact'] > $b['rule_impact']) ? -1 : 1;
	}
	function sort_page_speed_opportunities_v5($a, $b) {
		if ($a['rule_score'] == $b['rule_score']) {
			return 0;
		}
		return ($a['rule_score'] < $b['rule_score']) ? -1 : 1;
	}	

	function sort_site_speed_opportunities($a, $b) {
		if ($a['impact'] == $b['impact']) {
			return 0;
		}
		return ($a['impact'] > $b['impact']) ? -1 : 1;
	}	

	
	
	
	
	function sort_page_items_by_accelerated_status($a, $b) {
		if ($a->accelerated == $b->accelerated) {
			return 0;
			
		}
		
		return ($a->accelerated > $b->accelerated) ? -1 : 1;
		
	}		
	
	
	
	function get_scanned_pages() {
		return $this->get_scanned_objects("pages"); 	
	}
	
	function get_scanned_posts() {
		return $this->get_scanned_objects("posts"); 	
	}
	
	function pegasaas_fetch_pagespeed_last_scan($resource_id, $use_cache = false) {
		global $pegasaas;
	
		$post_data 					= array();
		
		PegasaasUtils::log("pegasaas_check_queued_pagespeed_score_requests START", "script_execution_benchmarks");

		$post_pagespeed_history = $this->get_pages_with_scores("pagespeed", $resource_id);
		PegasaasUtils::log("pegasaas_check_queued_pagespeed_score_requests AFTER get_pages_with_scores", "script_execution_benchmarks");

		$pagespeed_scan_record = $post_pagespeed_history->data;
			

	
		
		
		if ($pagespeed_scan_record) {
		
			$post_data = json_decode($pagespeed_scan_record, true);
			
			// condition speed-index
			$speed_index_mobile = number_format($post_data['meta']['mobile']['lab_data']['speed-index']['displayValue'] * 1, 1, '.', '');
			$speed_index_desktop = number_format($post_data['meta']['desktop']['lab_data']['speed-index']['displayValue'] * 1, 1, '.', '');
			if ($speed_index_mobile != 0.0 && $speed_index_mobile != "") {
				$post_data['meta']['mobile']['lab_data']['speed-index']['value'] = $speed_index_mobile;
				$post_data['meta']['desktop']['lab_data']['speed-index']['value'] = $speed_index_desktop;
			}

			// condition interactive
			$interactive_mobile = number_format($post_data['meta']['mobile']['lab_data']['interactive']['displayValue'] * 1, 1, '.', '');
			$interactive_desktop = number_format($post_data['meta']['desktop']['lab_data']['interactive']['displayValue'] * 1, 1, '.', '');
			if ($interactive_mobile != 0.0 && $interactive_mobile != "") {
				$post_data['meta']['mobile']['lab_data']['interactive']['value'] = $interactive_mobile;
				$post_data['meta']['desktop']['lab_data']['interactive']['value'] = $interactive_desktop;
			}
				
			// condition fmp
			$fmp_mobile = number_format($post_data['meta']['mobile']['lab_data']['first-meaningful-paint']['displayValue'] * 1, 1, '.', '');
			$fmp_desktop = number_format($post_data['meta']['desktop']['lab_data']['first-meaningful-paint']['displayValue'] * 1, 1, '.', '');
			if ($fmp_mobile != 0.0 && $fmp_mobile != "") {
				$post_data['meta']['mobile']['lab_data']['first-meaningful-paint']['value'] = $fmp_mobile;
				$post_data['meta']['desktop']['lab_data']['first-meaningful-paint']['value'] = $fmp_desktop;
			}
			
			// condition fcp
			$fcp_mobile = number_format($post_data['meta']['mobile']['lab_data']['first-contentful-paint']['displayValue'] * 1, 1, '.', '');
			$fcp_desktop = number_format($post_data['meta']['desktop']['lab_data']['first-contentful-paint']['displayValue'] * 1, 1, '.', '');
			if ($fcp_mobile != 0.0 && $fcp_mobile != "") {
				$post_data['meta']['mobile']['lab_data']['first-contentful-paint']['value'] = $fcp_mobile;
				$post_data['meta']['desktop']['lab_data']['first-contentful-paint']['value'] = $fcp_desktop;
			}
			
			// condition lcp
			$lcp_mobile = number_format($post_data['meta']['mobile']['lab_data']['largest-contentful-paint']['displayValue'] * 1, 1, '.', '');
			$lcp_desktop = number_format($post_data['meta']['desktop']['lab_data']['largest-contentful-paint']['displayValue'] * 1, 1, '.', '');
			if ($fcp_mobile != 0.0 && $fcp_mobile != "") {
				$post_data['meta']['mobile']['lab_data']['largest-contentful-paint']['value'] = $lcp_mobile;
				$post_data['meta']['desktop']['lab_data']['largest-contentful-paint']['value'] = $lcp_desktop;
			}			

			
			// condition tbt
			$tbt_mobile = number_format($post_data['meta']['mobile']['lab_data']['total-blocking-time']['displayValue'] * 1, 1, '.', '');
			$tbt_desktop = number_format($post_data['meta']['desktop']['lab_data']['total-blocking-time']['displayValue'] * 1, 1, '.', '');
			if ($fcp_mobile != 0.0 && $fcp_mobile != "") {
				$post_data['meta']['mobile']['lab_data']['total-blocking-time']['value'] = $tbt_mobile;
				$post_data['meta']['desktop']['lab_data']['total-blocking-time']['value'] = $tbt_desktop;
			}			


			// condition cls
			$cls_mobile = number_format($post_data['meta']['mobile']['lab_data']['cumulative-layout-shift']['displayValue'] * 1, 1, '.', '');
			$cls_desktop = number_format($post_data['meta']['desktop']['lab_data']['cumulative-layout-shift']['displayValue'] * 1, 1, '.', '');
			if ($fcp_mobile != 0.0 && $fcp_mobile != "") {
				$post_data['meta']['mobile']['lab_data']['cumulative-layout-shift']['value'] = $cls_mobile;
				$post_data['meta']['desktop']['lab_data']['cumulative-layout-shift']['value'] = $cls_desktop;
			}			
			//var_dump($post_data['meta']['mobile']['lab_data']['cumulative-layout-shift']);

			
			/*
			// condition fci
			$fci_mobile = number_format($post_data['meta']['mobile']['lab_data']['first-cpu-idle']['displayValue'] , 1, '.', '');
			$fci_desktop = number_format($post_data['meta']['desktop']['lab_data']['first-cpu-idle']['displayValue'] , 1, '.', '');
			if ($fci_mobile != 0.0 && $fci_mobile != "") {
				$post_data['meta']['mobile']['lab_data']['first-cpu-idle']['value'] = $fci_mobile;
				$post_data['meta']['desktop']['lab_data']['first-cpu-idle']['value'] = $fci_desktop;
			}	
			*/		
			
			if (isset($post_data['meta']['mobile']['opportunities']['time-to-first-byte']['displayValue'])) {
				$ttfb_mobile = $post_data['meta']['mobile']['opportunities']['time-to-first-byte']['displayValue'];
			} else {
				$ttfb_mobile = $post_data['meta']['mobile']['opportunities']['server-response-time']['displayValue'];
			}
			
			// condition ttfb
			$ttfb_mobile = str_replace("Root document took ", "", $ttfb_mobile);
			$ttfb_mobile = str_replace("ms", "", $ttfb_mobile);
			
			$ttfb_mobile = str_replace(",", "", $ttfb_mobile);
			$ttfb_mobile = @number_format($ttfb_mobile * 1, 0);
			
			
			if (isset($post_data['meta']['desktop']['opportunities']['time-to-first-byte']['displayValue'])) {
				$ttfb_desktop = $post_data['meta']['desktop']['opportunities']['time-to-first-byte']['displayValue'];
			} else {
				$ttfb_desktop = $post_data['meta']['desktop']['opportunities']['server-response-time']['displayValue'];
			}
			
			$ttfb_desktop = str_replace("Root document took ", "", $ttfb_desktop);
			$ttfb_desktop = str_replace("ms", "", $ttfb_desktop);
			$ttfb_desktop = str_replace(",", "", $ttfb_desktop);
			$ttfb_desktop = number_format($ttfb_mobile * 1, 0);
			if ($ttfb_mobile != 0.0 && $ttfb_mobile != "") {
				$post_data['meta']['mobile']['lab_data']['time-to-first-byte']['value'] = $ttfb_mobile;
				$post_data['meta']['desktop']['lab_data']['time-to-first-byte']['value'] = $ttfb_desktop;
			}			
			
			
			
		}
		//if ($use_cache) {
		//	$this->data_cache["pagespeed_last_scan"]["{$resource_id}"] = $post_data;
		//}
		PegasaasUtils::log("pegasaas_check_queued_pagespeed_score_requests END", "script_execution_benchmarks");

		return $post_data;

	}
	
	function pegasaas_fetch_pagespeed_benchmark_last_scan($resource_id, $use_cache = false) {
		global $pegasaas;
		
		$post_data 					= array();
		

			$pagespeed_score_requests = $this->get_pages_with_scores("pagespeed-benchmark", $resource_id);
		
			//$pagespeed_score_requests 	= $pegasaas->db->get_results("pegasaas_performance_scan", array("resource_id" => $resource_id, "scan_type" => "pagespeed-benchmark"), "time DESC", "resource_id", 1);
			//$post_pagespeed_history = $pagespeed_score_requests[0];
			$pagespeed_scan_record = $pagespeed_score_requests->data;

		
		
	

	
		
		
		if ($pagespeed_scan_record) {
		
			$post_data = json_decode($pagespeed_scan_record, true);
			
			// condition speed-index
			$speed_index_mobile = number_format($post_data['meta']['mobile']['lab_data']['speed-index']['displayValue'] * 1, 1, '.', '');
			$speed_index_desktop = number_format($post_data['meta']['desktop']['lab_data']['speed-index']['displayValue'] * 1, 1, '.', '');
			if ($speed_index_mobile != 0.0 && $speed_index_mobile != "") {
				$post_data['meta']['mobile']['lab_data']['speed-index']['value'] = $speed_index_mobile;
				$post_data['meta']['desktop']['lab_data']['speed-index']['value'] = $speed_index_desktop;
			}

			// condition interactive
			$interactive_mobile = number_format($post_data['meta']['mobile']['lab_data']['interactive']['displayValue'] * 1, 1, '.', '');
			$interactive_desktop = number_format($post_data['meta']['desktop']['lab_data']['interactive']['displayValue'] * 1, 1, '.', '');
			if ($interactive_mobile != 0.0 && $interactive_mobile != "") {
				$post_data['meta']['mobile']['lab_data']['interactive']['value'] = $interactive_mobile;
				$post_data['meta']['desktop']['lab_data']['interactive']['value'] = $interactive_desktop;
			}
				
			// condition fmp
			$fmp_mobile = number_format($post_data['meta']['mobile']['lab_data']['first-meaningful-paint']['displayValue'] * 1, 1, '.', '');
			$fmp_desktop = number_format($post_data['meta']['desktop']['lab_data']['first-meaningful-paint']['displayValue'] * 1, 1, '.', '');
			if ($fmp_mobile != 0.0 && $fmp_mobile != "") {
				$post_data['meta']['mobile']['lab_data']['first-meaningful-paint']['value'] = $fmp_mobile;
				$post_data['meta']['desktop']['lab_data']['first-meaningful-paint']['value'] = $fmp_desktop;
			}
			
			// condition fcp
			$fcp_mobile = number_format($post_data['meta']['mobile']['lab_data']['first-contentful-paint']['displayValue'] * 1, 1, '.', '');
			$fcp_desktop = number_format($post_data['meta']['desktop']['lab_data']['first-contentful-paint']['displayValue'] * 1, 1, '.', '');
			if ($fcp_mobile != 0.0 && $fcp_mobile != "") {
				$post_data['meta']['mobile']['lab_data']['first-contentful-paint']['value'] = $fcp_mobile;
				$post_data['meta']['desktop']['lab_data']['first-contentful-paint']['value'] = $fcp_desktop;
			}
			
			// condition lcp
			$lcp_mobile = number_format($post_data['meta']['mobile']['lab_data']['largest-contentful-paint']['displayValue'] * 1, 1, '.', '');
			$lcp_desktop = number_format($post_data['meta']['desktop']['lab_data']['largest-contentful-paint']['displayValue'] * 1, 1, '.', '');
			if ($lcp_mobile != 0.0 && $lcp_mobile != "") {
				$post_data['meta']['mobile']['lab_data']['largest-contentful-paint']['value'] = $lcp_mobile;
				$post_data['meta']['desktop']['lab_data']['largest-contentful-paint']['value'] = $lcp_desktop;
			}			

			// condition tbt
			$tbt_mobile = number_format($post_data['meta']['mobile']['lab_data']['total-blocking-time']['displayValue'] * 1, 1, '.', '');
			$tbt_desktop = number_format($post_data['meta']['desktop']['lab_data']['total-blocking-time']['displayValue'] * 1, 1, '.', '');
			if ($fcp_mobile != 0.0 && $fcp_mobile != "") {
				$post_data['meta']['mobile']['lab_data']['total-blocking-time']['value'] = $tbt_mobile;
				$post_data['meta']['desktop']['lab_data']['total-blocking-time']['value'] = $tbt_desktop;
			}
			
			// condition cls
			$cls_mobile = number_format($post_data['meta']['mobile']['lab_data']['cumulative-layout-shift']['displayValue'] * 1, 1, '.', '');
			$cls_desktop = number_format($post_data['meta']['desktop']['lab_data']['cumulative-layout-shift']['displayValue'] * 1, 1, '.', '');
			if ($fcp_mobile != 0.0 && $fcp_mobile != "") {
				$post_data['meta']['mobile']['lab_data']['cumulative-layout-shift']['value'] = $cls_mobile;
				$post_data['meta']['desktop']['lab_data']['cumulative-layout-shift']['value'] = $cls_desktop;
			}				
			
			// condition fci
			/*
			$fci_mobile = number_format($post_data['meta']['mobile']['lab_data']['first-cpu-idle']['displayValue'] * 1 , 1, '.', '');
			$fci_desktop = number_format($post_data['meta']['desktop']['lab_data']['first-cpu-idle']['displayValue'] * 1, 1, '.', '');
			if ($fci_mobile != 0.0 && $fci_mobile != "") {
				$post_data['meta']['mobile']['lab_data']['first-cpu-idle']['value'] = $fci_mobile;
				$post_data['meta']['desktop']['lab_data']['first-cpu-idle']['value'] = $fci_desktop;
			}
			*/
			
			if (isset($post_data['meta']['mobile']['opportunities']['time-to-first-byte']['displayValue'])) {
				$ttfb_mobile = $post_data['meta']['mobile']['opportunities']['time-to-first-byte']['displayValue'];
			} else {
				$ttfb_mobile = $post_data['meta']['mobile']['opportunities']['server-response-time']['displayValue'];
			}
			
			
			// condition ttfb
			$ttfb_mobile = str_replace("Root document took ", "", $ttfb_mobile);
			$ttfb_mobile = str_replace("ms", "", $ttfb_mobile);
			
			$ttfb_mobile = str_replace(",", "", $ttfb_mobile);
			$ttfb_mobile = @number_format($ttfb_mobile * 1, 0);
			
			
			if (isset($post_data['meta']['mobile']['opportunities']['time-to-first-byte']['displayValue'])) {
				$ttfb_desktop = $post_data['meta']['desktop']['opportunities']['time-to-first-byte']['displayValue'];
			} else {
				$ttfb_desktop = $post_data['meta']['desktop']['opportunities']['server-response-time']['displayValue'];
			}
			
			$ttfb_desktop = str_replace("Root document took ", "", $ttfb_desktop);
			$ttfb_desktop = str_replace("ms", "", $ttfb_desktop);
			$ttfb_desktop = str_replace(",", "", $ttfb_desktop);
			$ttfb_desktop = number_format($ttfb_mobile * 1, 0);

			
		//	$ttfb_desktop = number_format($post_data['meta']['desktop']['opportunities']['time-to-first-byte']['displayValue'], 1, '.', '');
			if ($ttfb_mobile != 0.0 && $ttfb_mobile != "") {
				$post_data['meta']['mobile']['lab_data']['time-to-first-byte']['value'] = $ttfb_mobile;
				$post_data['meta']['desktop']['lab_data']['time-to-first-byte']['value'] = $ttfb_desktop;
			}			
		}
		
		

		
		return $post_data;

	}	
	

	function pegasaas_check_queued_pagespeed_benchmark_score_requests() {
		global $pegasaas;
		
		
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$pagespeed_score_requests_results = $pegasaas->db->get_results("pegasaas_api_request", array("request_type" => "pagespeed-benchmark"));
			$pagespeed_score_requests = array();
			foreach ($pagespeed_score_requests_results as $request_record) {
				$resource_id = $request_record->resource_id;
				$pagespeed_score_requests["$resource_id"] = json_decode($request_record->data, true);
			}

			$pending_requests = $_POST['pending_requests'];

			$completed_requests = array();
			if (is_array($pending_requests)) {
				foreach ($pending_requests as $resource_id) {
					if (!array_key_exists($resource_id, $pagespeed_score_requests)) {
						$post_pagespeed = $this->pegasaas_fetch_pagespeed_benchmark_last_scan($resource_id);

						
						$completed_requests["$resource_id"]['mobile_score']		= $post_pagespeed['mobile_score'];
						$completed_requests["$resource_id"]['mobile_ttfb']		= $post_pagespeed['meta']['mobile']['lab_data']['time-to-first-byte']['value'];
						$completed_requests["$resource_id"]['mobile_fcp']		= $post_pagespeed['meta']['mobile']['lab_data']['first-contentful-paint']['value'];
						$completed_requests["$resource_id"]['mobile_fmp']		= $post_pagespeed['meta']['mobile']['lab_data']['first-meaningful-paint']['value'];
						$completed_requests["$resource_id"]['mobile_lcp']		= $post_pagespeed['meta']['mobile']['lab_data']['largest-meaningful-paint']['value'];
						$completed_requests["$resource_id"]['mobile_tbt']		= $post_pagespeed['meta']['mobile']['lab_data']['total-blocking-time']['value'];
						$completed_requests["$resource_id"]['mobile_cls']		= $post_pagespeed['meta']['mobile']['lab_data']['cumulative-layout-shift']['value'];
						$completed_requests["$resource_id"]['mobile_si']		= $post_pagespeed['meta']['mobile']['lab_data']['speed-index']['value'];
						$completed_requests["$resource_id"]['mobile_fci']		= $post_pagespeed['meta']['mobile']['lab_data']['first-cpu-idle']['value'];
						$completed_requests["$resource_id"]['mobile_tti']		= $post_pagespeed['meta']['mobile']['lab_data']['interactive']['value'];
						
						$completed_requests["$resource_id"]['desktop_score']	= $post_pagespeed['score'];
						$completed_requests["$resource_id"]['desktop_ttfb']		= $post_pagespeed['meta']['desktop']['lab_data']['time-to-first-byte']['value'];
						$completed_requests["$resource_id"]['desktop_fcp']		= $post_pagespeed['meta']['desktop']['lab_data']['first-contentful-paint']['value'];
						$completed_requests["$resource_id"]['desktop_fmp']		= $post_pagespeed['meta']['desktop']['lab_data']['first-meaningful-paint']['value'];
						$completed_requests["$resource_id"]['desktop_lcp']		= $post_pagespeed['meta']['desktop']['lab_data']['largest-meaningful-paint']['value'];
						$completed_requests["$resource_id"]['desktop_tbt']		= $post_pagespeed['meta']['desktop']['lab_data']['total-blocking-time']['value'];
						$completed_requests["$resource_id"]['desktop_cls']		= $post_pagespeed['meta']['desktop']['lab_data']['cumulative-layout-shift']['value'];
						$completed_requests["$resource_id"]['desktop_si']		= $post_pagespeed['meta']['desktop']['lab_data']['speed-index']['value'];
						$completed_requests["$resource_id"]['desktop_fci']		= $post_pagespeed['meta']['desktop']['lab_data']['first-cpu-idle']['value'];
						$completed_requests["$resource_id"]['desktop_tti']		= $post_pagespeed['meta']['desktop']['lab_data']['interactive']['value'];						
					}
				}
			}
			$completed_requests["#summary#"]["total_pending_requests"] = sizeof($pagespeed_score_requests);
			print json_encode($completed_requests);
		} else {
			print json_encode(array("status" => -1));
		}		
	 	wp_die();			
		
	 	wp_die();		
	}
	
	function process_pagespeed_score_request($resource_id, $nonce, $data) {
		global $pegasaas; 
		$debug = false;
		
		//var_dump($data);
	//	$valid_request = $pegasaas->db->has_record("pegasaas_api_request", array("resource_id" => $resource_id, "nonce" => $nonce, "request_type" => "pagespeed"));
		$valid_request = $pegasaas->db->has_record("pegasaas_api_request", array("resource_id" => $resource_id, "request_type" => "pagespeed"));

		if ($valid_request || $_POST['debug'] == 1) { 
		
			
			
			$request_record  = $pegasaas->db->get_single_record("pegasaas_api_request", array("resource_id" => $resource_id, "request_type" => "pagespeed"));
			
			$pegasaas->utils->log("Processing PageSpeed Scores for $resource_id with Nonce $nonce", "process_pagespeed_scans");
			$pegasaas->db->delete("pegasaas_api_request", array("resource_id" => $resource_id, "request_type" => "pagespeed"));

			//print json_encode($data);
			//print "\n";
							
			// does the data contain a bad score due to deferral of render blocking resources or prioritization of visible content?
			// if yes, then do not store data, and instead queue cpcss for this page
			// by not storing, but deleting the request and queing cpcss for this page, the request for further gpsi data will not happen until the cpcss
			// has been completed
			$store_data = true;
			
			// if this is not a rescan, then we should check the score against a benchmark
			if ($request_record->advisory != "is_rescan") {
			
				$benchmark_data = $this->pegasaas_fetch_pagespeed_benchmark_last_scan($resource_id);
		
				if (is_array($benchmark_data)) {
					if ($_POST['debug'] == 1) {
						print "Have Benchmark Data\n";
					}
					
					$score 			= $data['desktop']['performance']['score'] * 100;
					$mobile_score 	= $data['mobile']['performance']['score'] * 100;
					
								//		var_dump($data['mobile']['performance']);
					
					if ($_POST['debug'] == 1) {
						print "Desktop Score: {$score}\n";
						print "Mobile Score: {$mobile_score}\n";
						print "Benchmark Desktop Score: {$benchmark_data['score']}\n";
						print "Benchmark Mobile Score: {$benchmark_data['mobile_score']}\n";
						
					}
					
					if ($benchmark_data['score'] > $score || $benchmark_data['mobile_score'] > $mobile_score) {
						$store_data = false;
						
					//		print "benchmark desktop score {$benchmark_data['score']} vs {$score}\n";
					//	print "benchmark mobile score {$benchmark_data['mobile_score']} vs {$mobile_score}\n";
						if ($_POST['debug'] == 1) {
						  	print "Benchmark Score is higher than Desktop Score -- Initiating Rescan\n";	
						}
						
						//  re-request the score and tag on "is_rescan"
						$this->request_pagespeed_score($resource_id, $is_rescan = true, $die = false);
					} else {
						
					}
				}
				
			} 
			

			
			// store data
			if ($store_data) {
				if ($_POST['debug'] == 1) {
					print "Storing Data\n";
				}
				$time = time();
				//$post_pagespeed_history = PegasaasUtils::get_object_meta($resource_id, "accelerator_pagespeed_history");
				$post_pagespeed_history = array(); // no longer retain the speed data
				if ($data['version'] == 5) {
					$score 			= ceil($data['desktop']['performance']['score'] * 100);
					$mobile_score 	= ceil($data['mobile']['performance']['score'] * 100);
					
	
				} else {
					$score 			= $data['desktop']['ruleGroups']['SPEED']['score'];
					$mobile_score 	= $data['mobile']['ruleGroups']['SPEED']['score'];
	
				}
				
				// lab_data->main-thread-tasks
				// lab_data->layout-shift-elements
				// lab_data ->resource-summary
				// lab_data->third-party-summary
				// total-byte-weight
				
					$record_data = array( "when" => $time, 
										"score" => $score, 
										"mobile_score" => $mobile_score, 
										"version" => $data['version'],
										"meta" => $data);

					$pegasaas->db->delete("pegasaas_performance_scan", array("scan_type" => "pagespeed", 
																			 "resource_id" => $resource_id));				
					$insert_id = $pegasaas->db->add_record("pegasaas_performance_scan", array("time" => date("Y-m-d H:i:s", time()), 
																				 "scan_type" => "pagespeed", 
																				 "resource_id" => $resource_id, 
																				 "data" => json_encode($record_data)));
				
				// update global index (so that we can easily iterate through scores for a summary, as well as quickly wipe out all scores)
				$pages_with_scores = get_option("pegasaas_accelerator_pagespeed_scores", array());
				$pages_with_scores["$resource_id"] = array("mobile" => $mobile_score, "desktop" => $score);
				update_option("pegasaas_accelerator_pagespeed_scores", $pages_with_scores);		
				$score_summary = get_option("", array());
				$score_data = get_option("pegasaas_web_perf_score_data", array());
				$performance_data = get_option("pegasaas_performance_data", array());
				if (isset($score_data['stale_records']) && isset($score_data['score_data'])) {
					$score_data['stale_records']++;
					
					update_option("pegasaas_web_perf_score_data", $score_data);
				} 
				
				if (isset($performance_data['stale_records']) && isset($performance_data['performance_data'])) {
					$performance_data['stale_records']++;
					
					update_option("pegasaas_performance_data", $performance_data);
				} 
				
			} 
			
			
				
			$success = true;
				
		// invalid nonce returned
		} else {
			
		
			$success = false; 
			$pegasaas->utils->log("No -- have PageSpeed Scores for $resource_id with Nonce $nonce"); 
			if (is_array($pagespeed_score_requests)) {
				$pegasaas->utils->log("Existing Keys: ".implode(",", array_keys($pagespeed_score_requests)));
			} else {
				$pegasaas->utils->log("Existing Keys: there are none");
			}
			if (is_array($pagespeed_score_requests[$resource_id])) {
				$pegasaas->utils->log("Existing Keys[{$resource_id}]: ".implode(",", array_keys($pagespeed_score_requests[$resource_id])));
			} else {
								$pegasaas->utils->log("Existing Keys[{$resource_id}]: none");

			}
			
		
		}	
		
		$pegasaas->utils->release_semaphore("pegasaas_process_pagespeed_score_request");
		
		return $success;
	}
	
	
		
		
		
	function pegasaas_process_pagespeed_score_request() {
		global $pegasaas;
		
		$pegasaas->utils->log("----- pegasaas_process_pagespeed_score_request: BEGIN -----", "process_pagespeed_scans");
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$resource_id 	= $_GET['wp_rid'];		
			if ($resource_id == "" && $_POST['wp_rid'] != "") {
				$resource_id = urldecode($_POST['wp_rid']);
			}	
			
			$nonce 			= $_GET['wp_n'];	
			if ($nonce == "" && $_POST['wp_n'] != "") {
				$nonce = urldecode($_POST['wp_n']);
			}			
			
			if (array_key_exists("method", $_POST) && $_POST['method'] == "fetch") { 
				$request_id = $_POST['request_id'];

				$response = $pegasaas->api->fetch_finished_pagespeed_scan($request_id);
				$data = $response['pagespeed_scan']['data'];
				

			} else {
				$data = json_decode(stripslashes($_POST['data']), true);
			}
			
			
			
			//var_dump($data); 
		

			
	
			$pegasaas->utils->log("About To Process with $resource_id / $nonce", "process_pagespeed_scans");

			$success = $this->process_pagespeed_score_request($resource_id, $nonce, $data);
			
			print $success ? 1 : 0; // either a 1 or 0
		
		} else if ($_POST['request_id'] == "test-web-perf-submission") {
			print 1;
		} else {
			print -1;
		}		
		
		$pegasaas->utils->log("----- pegasaas_process_pagespeed_score_request: END-----", "process_pagespeed_scans");
	 	wp_die();		
	}

	function pegasaas_process_pagespeed_benchmark_score_request() {
		global $pegasaas;
		
	
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
				$data = json_decode(stripslashes($_POST['data']), true);

				$resource_id 	= $_GET['wp_rid'];		
				if ($resource_id == "" && $_POST['wp_rid'] != "") {
					$resource_id = urldecode($_POST['wp_rid']);
				}	
				
				$nonce 			= $_GET['wp_n'];	
				if ($nonce == "" && $_POST['wp_n'] != "") {
					$nonce = urldecode($_POST['wp_n']);
				}	
				
			
				if (array_key_exists("method", $_POST) && $_POST['method'] == "fetch") { 
					$request_id = $_POST['request_id'];
					$response = $pegasaas->api->fetch_finished_pagespeed_baseline_scan($request_id);
						$data = $response['pagespeed_baseline_scan']['data'];
				} else {
					$data = json_decode(stripslashes($_POST['data']), true);
				}			
			
				$success = $this->process_pagespeed_benchmark_score_request($resource_id, $nonce, $data);
				
				if (!$success) {
					$pegasaas->api->pickup_queued_pagespeed_scores();
				}
				print $success ? 1 : 0;
				
				
		
		} else {
			print -1;
		}		
	 	wp_die();		
	}	

	function process_pagespeed_benchmark_score_request($resource_id, $nonce, $data) {
		global $pegasaas;
		

				$pegasaas->utils->log("Processing Benchmark GPSI score for {$resource_id} / {$nonce}");
 //print "Resource ID: {$resource_id}\n";
			//	$resource_id = urlencode($resource_id);
		// print "Resource ID2: {$resource_id}\n";
	//	$resource_id = str_replace("%2F", "/", $resource_id);

				$valid_request = $pegasaas->db->has_record("pegasaas_api_request", array("resource_id" => $resource_id,  "request_type" => "pagespeed-benchmark"));
				//var_dump($data);
				if ($valid_request) { 
					$pegasaas->utils->log("Processing Benchmark GPSI score {$resource_id} / {$nonce} / YES");
					$pegasaas->db->delete("pegasaas_api_request", array("resource_id" => $resource_id, "request_type" => "pagespeed-benchmark"));
								
					// store data
					$time = time();
					 
					if ($data['version'] == 5) {
						$score 			= $data['desktop']['performance']['score'] * 100;
						$mobile_score 	= $data['mobile']['performance']['score'] * 100;
					
					} else {
						$score 			= $data['desktop']['ruleGroups']['SPEED']['score'];
						$mobile_score 	= $data['mobile']['ruleGroups']['SPEED']['score'];
					}
					
					
					$record_data = array( "when" => $time, 
										"score" => $score, 
										"mobile_score" => $mobile_score, 
										"version" => $data['version'],
										"meta" => $data);

					$pegasaas->db->delete("pegasaas_performance_scan", array("scan_type" => "pagespeed-benchmark", 
																			 "resource_id" => $resource_id));		
					$insert_id = $pegasaas->db->add_record("pegasaas_performance_scan", array("time" => date("Y-m-d H:i:s", time()), 
																				 "scan_type" => "pagespeed-benchmark", 
																				 "resource_id" => $resource_id, 
																				 "data" => json_encode($record_data)));
					 			
					$success = true;
					$pages_with_scores = get_option("pegasaas_accelerator_pagespeed_benchmark_scores", array());
					$pages_with_scores["$resource_id"] = array("mobile" => $mobile_score, "desktop" => $score);
				
					update_option("pegasaas_accelerator_pagespeed_benchmark_scores", $pages_with_scores);		
					
					$score_data = get_option("pegasaas_web_perf_benchmark_score_data", array());
					$performance_data = get_option("pegasaas_performance_data", array());
					if (isset($score_data['stale_records']) && isset($score_data['score_data'])) {
						$score_data['stale_records']++;
					
						update_option("pegasaas_web_perf_benchmark_score_data", $score_data);
					} 
				
					if (isset($performance_data['stale_records']) && isset($performance_data['performance_data'])) {
						$performance_data['stale_records']++;
						update_option("pegasaas_performance_data", $performance_data);
					} 					
				// invalid nonce returned
				} else {
					$pegasaas->utils->log("Processing Benchmark GPSI score {$resource_id} / {$nonce} / NO MATCHING RECORD");

				
					// make sure to clear any pagespeed request that may have been accidentally de-coupled
					//unset($pagespeed_score_requests["{$resource_id}"]);<br>
					$success = false;

					
				}

		
		return $success;
	
	}

	function get_site_pages_accelerated_data() {
		global $pegasaas;
		$pegasaas->utils->log("get_site_pages_accelerated_data() start", "script_execution_benchmarks");

				
		$critical_logging = false;
		
		$pages_accelerated = array(
			"accelerated" => 0,
			"pages_in_site" => 0,
			"pending" => 0,
			"not_accelerated" => 0,
		);
		
		
		
		// clear search filter so that the summary of accelerated data includes all site data
		//$temporary_search_filter_storage = $pegasaas->interface->results_search_filter;
		//$pegasaas->interface->results_search_filter = ""; 
		//print $pegasaas->execution_time()."<br>";
		$pages_and_posts = PegasaasUtils::get_all_pages_and_posts($use_filter = false);
		//		print $pegasaas->execution_time()."<br>";
//return;
		
		$pages_accelerated['pages_in_site'] = count($pages_and_posts);
		$accelerated_pages = array();
		foreach ($pages_and_posts as $post) {
			$resource_id = $post->resource_id;
			$last_time_adjusted = $post->last_time_adjusted;
			
		//	$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides", false);

    	
			//if ($page_level_settings['accelerated'] > 0) {
			if ($post->accelerated) {
				
				$accelerated_pages[$resource_id] = 1;

				if (PegasaasAccelerator::$settings['settings']['page_caching']['status'] > 0) {
					$cache_exists = PegasaasAccelerator::$cache_map["{$resource_id}"] != "" && PegasaasAccelerator::$cache_map["{$resource_id}"] != NULL;
					
					if ($cache_exists) {
						$pages_accelerated['accelerated']++;
					} else {
						$pages_accelerated['pending']++;
					}
				} else {
					$pages_accelerated['accelerated']++;
				}
				if ($critical_logging) {
					PegasaasUtils::log("PegasaasScanner::get_site_pages_accelerated_data()[3021] {$resource_id} ACCELERATED  {$last_time_adjusted}:".sizeof($accelerated_pages));
				}					
				
			} else {
				if ($critical_logging) {
					PegasaasUtils::log("PegasaasScanner::get_site_pages_accelerated_data()[3026] {$resource_id} NOT ACCELERATED {$last_time_adjusted}:".sizeof($accelerated_pages));
				}	
				$pages_accelerated["not_accelerated"] ++;
			}
		}
		$end_size = sizeof($accelerated_pages);
		$pegasaas->utils->log("update_option('pegasaas_accelerated_pages', Array({$end_size}) -- PegasaasScanner::get_site_pages_accelerated_data", "data_structures");

		if ($end_size == 0) {
			$pegasaas->utils->log("size of pages and posts ".sizeof($pages_and_posts)." -- PegasaasScanner::get_site_pages_accelerated_data", "data_structures");
			
		}
		
		if ($critical_logging) {
			PegasaasUtils::log("PegasaasScanner::get_site_pages_accelerated_data() sizeof accelerated pages:".sizeof($accelerated_pages));
		}
		update_option("pegasaas_accelerated_pages", $accelerated_pages, false);
		$pegasaas->utils->log("get_site_pages_accelerated_data() end", "script_execution_benchmarks");
		
		return $pages_accelerated;
		
	}
	
	function &get_pages_with_scores($type, $resource_id = "") { 
		global $pegasaas;
 
		
		
		if ($this->data_cache["pegasaas_performance_scan_{$type}"]) {
			
			$pages_with_scores = $this->data_cache["pegasaas_performance_scan_{$type}"];	
		} else { 
			if (sizeof($this->data_cache) == 0) {
				$memory_limit = $pegasaas->utils->get_memory_limit();
				
				
				$memory_usage = memory_get_usage();
				
				$memory_remaining = $memory_limit - $memory_usage;
			
				$safe_memory = $memory_remaining * .75; // 75%
				$size_of_typical_scan = 100000 * 2; // scan + benchmark
				if ($this->maximum_scans_to_store == "") {
					$this->maximum_scans_to_store = floor($safe_memory / $size_of_typical_scan);
				}
			}
						

			if ($this->maximum_scans_to_store < 1) {
				$this->maximum_scans_to_store = 1;
			}
			if ($this->maximum_scans_to_store > 500) {
				$this->maximum_scans_to_store = 500;
			}

			$pages_with_scores = array();
			$pages = $pegasaas->db->get_results("pegasaas_performance_scan", array("scan_type" => $type), "time DESC", "", $this->maximum_scans_to_store);
			foreach ($pages as $page) {
				$r_id = $page->resource_id;
				if (!array_key_exists($r_id, $pages_with_scores)) {
					$pages_with_scores["$r_id"] = $page;
				}
			}
			
			
			$this->data_cache["pegasaas_performance_scan_{$type}"] = $pages_with_scores;
			
			
			
		}

		
		if ($resource_id != "") {

			return $pages_with_scores["{$resource_id}"];
		}
		
		
		return $pages_with_scores;
	}
	
	function get_site_performance_metrics() {
		global $pegasaas;
		global $test_debug;
		
		
		if (isset($_GET['force_recalculation'])) {
			$force_recalculation = true;
			$force_all = true;
		}
		
		
		if ($force_recalculation && $force_all) {
			if ($test_debug) {
				PegasaasUtils::log_benchmark("Forcing Recalculation - Force All", "debug-li", 1);
			}			
		// bypass
		
		// first attempt to see if there is an object in the class
		} else if (isset($this->performance_data)) {
			if ($debug) { $pegasaas->utils->console_log("Have Existing CLASS Score Data"); }
			if ($test_debug) {
				PegasaasUtils::log_benchmark("Using Class Variable", "debug-li", 1);
			}	
			
			return $this->performance_data;
		
			
			
		} else if ($force_recalculation) {
			if ($test_debug) {
				PegasaasUtils::log_benchmark("Force Recalculation", "debug-li", 1);
			}	
			$performance_data = get_option("pegasaas_performance_data", array());
			if (isset($performance_data['stale_records']) && $performance_data['stale_records'] == 0) {
				if ($test_debug) {
					PegasaasUtils::log_benchmark("No Recalculation Required", "debug-li", 1);
				}					
				
				$this->performance_data = $performance_data['performance_data'];
				return $this->performance_data;
			}

		
		} else if ($debug && $debug_rebuild) {
			// skip the next elseif
		
		} else {
			$performance_data = get_option("pegasaas_performance_data", array());
			$fifteen_minutes = 60 * 15;
			$one_hour = 60 * 60;
			
			if (isset($performance_data['stale_records']) && isset($performance_data['performance_data']) && $performance_data['stale_records'] < 15) {
			
				if ($performance_data['stale_records'] > 0 && $performance_data['last_updated'] < time() - $fifteen_minutes) {
					//print "stale data";
					if ($test_debug) {
						PegasaasUtils::log_benchmark("New Data Existing, Should Rebuild", "debug-li", 1);
					}
				} else if ($performance_data['last_updated'] < time() - $one_hour) {
					// stale data
					if ($test_debug) {
						PegasaasUtils::log_benchmark("Data from the WP options table is STALE", "debug-li", 1);
					}
				} else {
					if ($test_debug) {
						PegasaasUtils::log_benchmark("Using Existing Data from the WP options table", "debug-li", 1);
					}
					$this->performance_data = $performance_data['performance_data'];
					
					return $this->performance_data;
				}
			}
	
									 
		
			if ($debug) { $pegasaas->utils->console_log("Score Data Does Not Yet Exist"); }

		}
		
		if ($test_debug) {
			PegasaasUtils::log_benchmark("Fetching New Score Data", "debug-li", 1);
		}
	//	print "Okay";
		/*
		// first attempt to see if there is an object in the class
		if (isset($this->performance_data)) {
			if ($debug) { $pegasaas->utils->console_log("Have Existing CLASS Score Data"); }
			return $this->performance_data;
		
		}		
		*/
		$performance_data_temp = array(
			"time-to-first-byte"		=> array("title" => "Time To First Byte", 		"unit" => "ms", "abbr" => "TTFB", 	"percent" => 0, "value" => 0, "before" => 0, "change" => 0, "description" => "TTFB measures the duration from the user or client making an HTTP request to the first byte of the page being received by the client's browser."),
			"first-contentful-paint"   	=> array("title" => "First Contentful Paint", 	"unit" => "s",	"abbr" => "FCP", 	"percent" => 0, "value" => 0, "before" => 0, "change" => 0, "description" => "FCP is triggered when any content is painted -- this could be text, an image, or canvas render.  The time of the FCP is reduced by injecting critical CSS into the page, and deferring render blocking resources."),
			"first-meaningful-paint"	=> array("title" => "First Meaningful Paint", 	"unit" => "s",	"abbr" => "FMP", 	"percent" => 0, "value" => 0, "before" => 0, "change" => 0, "description" => "First Meaningful Paint is the paint after which the largest above-the-fold layout change has happened.  FMP is reduced by injecting critical CSS into the page, and deferring render blocking resources."),
			"speed-index"				=> array("title" => "Speed Index", 				"unit" => "s",	"abbr" => "SI", 	"percent" => 0, "value" => 0, "before" => 0, "change" => 0, "description" => "Speed Index is a metric that shows you how quickly the contents of the page are visibly populated.  Speed Index can be impacted by the size of the DOM and how much of the page is rendered with JavaScript."),
			"largest-contentful-paint"		=> array("title" => "Largest Contentful Paint", "unit" => "s",	"abbr" => "LCP", 	"percent" => 0, "value" => 0, "before" => 0, "change" => 0, "description" => "Largest Contentful Paint (LCP) is a Core Web Vitals metric and measures when the largest content element in the viewport becomes visible.  LCP reduced by improving server response time."),
			"interactive"				=> array("title" => "Time To Interactive", 		"unit" => "s",	"abbr" => "TTI", 	"percent" => 0, "value" => 0, "before" => 0, "change" => 0, "description" => "TTI is a metric that measures how long it takes a page to become interactive, where interactive is identified as the point where the page has displayed useful content and the page responds to user interactions within 50 milliseconds.  This is impacted negatively by heavy work done by JavaScript."),
		/*	"first-cpu-idle" 			=> array("title" => "First CPU Idle", 			"unit" => "s",	"abbr" => "FCI", 	"percent" => 0, "value" => 0, "before" => 0, "change" => 0, "description" => "First CPU Idle is a metrict that measures when a page is minimally interactive.  The FCI time is shortened by reducing the number of resources loaded, Javascript executed, and styles rendered."),*/
			"total-blocking-time"	=> array("title" => "Total Blocking Time", 		"unit" => "s",	"abbr" => "TBT", 	"percent" => 0, "value" => 0, "before" => 0, "change" => 0, "description" => "The Total Blocking Time (TBT) metric measures the total amount of time between First Contentful Paint (FCP) and Time to Interactive (TTI) where the main thread was blocked for long enough to prevent input responsiveness."),
			"cumulative-layout-shift"	=> array("title" => "Cumulative Layout Shift", 	"unit" => "",	
												 "abbr" => "CLS", 	
												 "percent" => 0, "value" => 0, "before" => 0, "change" => 0, 
												 "description" => "CLS is the unexpected shifting of web page elements while the page is still downloading. The kinds of elements that tend to cause shift are fonts, images, videos, contact forms, buttons and other kinds of content. CLS is often caused by regions of the page being rendered on-page-load by JavaScript."),
			);

	
		$performance_data['mobile'] 	= $performance_data_temp;
		$performance_data['desktop'] 	= $performance_data_temp;
		
			
		if ($test_debug) {
			$current_usage = number_format((memory_get_peak_usage() / 1024 / 1024), 1, '.', '');
			$current_use_percentage = number_format($pegasaas->utils->get_memory_use_percentage(), 0, '.', ''); 	
			PegasaasUtils::log_benchmark("Memory Usage Before PageSpeed DB Query: {$current_usage}M ({$current_use_percentage}%) ", "debug-li", 1);
		}	
		
		//$pages_with_scores				= get_option("pegasaas_accelerator_pagespeed_scores", array());
		$pages_with_scores 	= $this->get_pages_with_scores("pagespeed");

		
		foreach ($pages_with_scores as $scan_record) {
			
			
			$resource_id = $scan_record->resource_id;
			
			$page_score_details = json_decode($scan_record->data, true);
			
			$benchmark_score_details = $this->pegasaas_fetch_pagespeed_benchmark_last_scan($resource_id, true);

			if ($page_score_details == NULL || $benchmark_score_details == NULL ) {
				continue;
			}

			if (!array_key_exists('lab_data', $page_score_details['meta']['mobile']) || !array_key_exists('lab_data', $benchmark_score_details['meta']['mobile'])) {
				continue;

			}
			$count++;
			
			foreach ($performance_data['mobile'] as $index => $obj) {
				
				
				if ($index == "time-to-first-byte") {
					
					if (isset($page_score_details['meta']['mobile']['opportunities']['time-to-first-byte']['displayValue'])) {
						$data = $page_score_details['meta']['mobile']['opportunities']['time-to-first-byte']['displayValue'];
					} else {
						$data = $page_score_details['meta']['mobile']['opportunities']['server-response-time']['displayValue'];
					}
					
					
					$data = str_replace("Root document took ", "", $data);
					$data = str_replace(" ms", "", $data);
					$data = str_replace(",", "", $data);
					if ($data == "") { $data = 0; } 
					$performance_data['mobile']["{$index}"]["value"] += $data;

					
					
					if (isset($page_score_details['meta']['desktop']['opportunities']['time-to-first-byte']['displayValue'])) {
						$data = $page_score_details['meta']['desktop']['opportunities']['time-to-first-byte']['displayValue'];
					} else {
						$data = $page_score_details['meta']['desktop']['opportunities']['server-response-time']['displayValue'];
					}					
					
					
					$data = str_replace("Root document took ", "", $data);
					$data = str_replace(" ms", "", $data);
					$data = str_replace(",", "", $data);
					if ($data == "") { $data = 0; } 
					$performance_data['desktop']["{$index}"]["value"] += $data;

					
					if (isset($benchmark_score_details['meta']['mobile']['opportunities']['time-to-first-byte']['displayValue'])) {
						$data = $benchmark_score_details['meta']['mobile']['opportunities']['time-to-first-byte']['displayValue'];
					} else {
						$data = $benchmark_score_details['meta']['mobile']['opportunities']['server-response-time']['displayValue'];
					}			
					
				
					$data = str_replace("Root document took ", "", $data);
					$data = str_replace(" ms", "", $data);
					$data = str_replace(",", "", $data);
					if ($data == "") { $data = 0; } 
					$performance_data['mobile']["{$index}"]["before"] += $data;

					
					if (isset($benchmark_score_details['meta']['desktop']['opportunities']['time-to-first-byte']['displayValue'])) {
						$data = $benchmark_score_details['meta']['desktop']['opportunities']['time-to-first-byte']['displayValue'];
					} else {
						$data = $benchmark_score_details['meta']['desktop']['opportunities']['server-response-time']['displayValue'];
					}					
					
					//$data = $benchmark_score_details['meta']['desktop']['opportunities']['time-to-first-byte']['displayValue'];
					$data = str_replace("Root document took ", "", $data);
					$data = str_replace(" ms", "", $data);
					$data = str_replace(",", "", $data);
					if ($data == "") { $data = 0; } 
					$performance_data['desktop']["{$index}"]["before"] += $data;
					
					
					
				} else {
					
					// current value mobile
					$data = $page_score_details['meta']['mobile']['lab_data']["{$index}"]['displayValue'];
					$data = str_replace(" s", "", $data);
					$data = str_replace(" ms", "", $data);
					if ($data == "") { $data = 0; } 
					
					if ($index == "total-blocking-time") {
						$data = $data / 1000; // because the data is in miliseconds
					}
					$performance_data['mobile']["{$index}"]["value"] += $data;

					// current value desktop
					$data = $page_score_details['meta']['desktop']['lab_data']["{$index}"]['displayValue'];
					$data = str_replace(" s", "", $data);
					$data = str_replace(" ms", "", $data);
					if ($data == "") { $data = 0; } 
					if ($index == "total-blocking-time") {
						$data = $data / 1000; // because the data is in miliseconds
					}					
					$performance_data['desktop']["{$index}"]["value"] += $data;

					// benchmark value mobile
					$data = $benchmark_score_details['meta']['mobile']['lab_data']["{$index}"]['displayValue'];
					$data = str_replace(" s", "", $data);
					$data = str_replace(" ms", "", $data);
					if ($data == "") { $data = 0; } 
					if ($index == "total-blocking-time") {
						$data = $data / 1000; // because the data is in miliseconds
					}					
					$performance_data['mobile']["{$index}"]["before"] += $data;
					
					// benchmark value desktop
					$data = $benchmark_score_details['meta']['desktop']['lab_data']["{$index}"]['displayValue'];
					$data = str_replace(" s", "", $data);
					$data = str_replace(" ms", "", $data);
					if ($data == "") { $data = 0; } 
					if ($index == "total-blocking-time") {
						$data = $data / 1000; // because the data is in miliseconds
					}					
					$performance_data['desktop']["{$index}"]["before"] += $data;
				
				}
			}
			
		}	
		
		if ($test_debug) {
			$current_usage = number_format((memory_get_peak_usage() / 1024 / 1024), 1, '.', '');
			$current_use_percentage = number_format($pegasaas->utils->get_memory_use_percentage(), 1, '.', ''); 	
			PegasaasUtils::log_benchmark("Memory Usage After PageSpeed DB Query: {$current_usage}M ({$current_use_percentage}%) ", "debug-li", 1);
		}	
	
		$performance_metrics['time-to-first-byte'] 		= array("fast" => 200, "slow" => 350, 'median' => 700, 'falloff' => 150 );
		$performance_metrics['first-contentful-paint'] 	= array("fast" => 2.35, "slow" => 4.0, 'median' => 4, 'falloff' => 2);
		$performance_metrics['first-meaningful-paint'] 	= array("fast" => 2.35, "slow" => 4.0, 'median' => 4, 'falloff' => 2);
		$performance_metrics['largest-contentful-paint'] = array("fast" => 2.48, "slow" => 4.0, 'median' => 4, 'falloff' => 2);
		$performance_metrics['speed-index'] 			= array("fast" => 3.38, "slow" => 5.8, 'median' => 6.5, 'falloff' => 2.9);
		$performance_metrics['total-blocking-time'] 	= array("fast" => 0.290, "slow" => 0.600, 'median' => 6.5, 'falloff' => 2.9);
		$performance_metrics['cumulative-layout-shift'] = array("fast" => 0.1, "slow" => 0.25, 'median' => 0.1, 'falloff' => 0.075);
		$performance_metrics['interactive'] 			= array("fast" => 3.8, "slow" => 7.3, 'median' => 7.3, 'falloff' => 2.9);
		$performance_metrics['first-cpu-idle'] 			= array("fast" => 3.4, "slow" => 5.8, 'median' => 5.8, 'falloff' => 2.9);
		
		if ($count == 0) {
			$count = 1;
		}
		foreach ($performance_data['mobile'] as $index => $obj) {
		//	print "XX $index <br>";
			$precision = 1;
			if ($index == "time-to-first-byte") {
				$precision = 0;
			} else if ($index == "cumulative-layout-shift") {
				$precision = 2;
			} 
			
			
			$performance_data['metrics']["{$index}"] = $performance_metrics["$index"];
			
			if (false &&  $index == "total-blocking-time") {
				print "XXXXX";
				var_dump($performance_data['mobile']["{$index}"]["value"]);
				print "\n";
				print $count;
			}
			// settle the averages current values
			$performance_data['mobile']["{$index}"]["value"] = number_format($performance_data['mobile']["{$index}"]["value"]/$count, $precision, '.', '');
			$performance_data['desktop']["{$index}"]["value"] = number_format($performance_data['desktop']["{$index}"]["value"]/$count, $precision, '.', '');

			// settle the averages current values
			$performance_data['mobile']["{$index}"]["percentile"] = PegasaasUtils::quantile_at_value($performance_metrics["$index"]['median'], $performance_metrics["$index"]['falloff'], $performance_data['mobile']["{$index}"]["value"]);
			$performance_data['desktop']["{$index}"]["percentile"] = PegasaasUtils::quantile_at_value($performance_metrics["$index"]['median'], $performance_metrics["$index"]['falloff'], $performance_data['desktop']["{$index}"]["value"]);
			
			// settle the averages unaccelerated values
			$performance_data['mobile']["{$index}"]["before"] = number_format($performance_data['mobile']["{$index}"]["before"]/$count, $precision, '.', '');
			$performance_data['desktop']["{$index}"]["before"] = number_format($performance_data['desktop']["{$index}"]["before"]/$count, $precision, '.', '');

			// set the change values
			$performance_data['mobile']["{$index}"]['change'] = number_format($performance_data['mobile']["{$index}"]["value"] - $performance_data['mobile']["{$index}"]["before"], $precision, '.', '');
			$performance_data['desktop']["{$index}"]['change'] = number_format($performance_data['desktop']["{$index}"]["value"] - $performance_data['desktop']["{$index}"]["before"], $precision, '.', '');
			
			// evaluate rating mobile
			if ($performance_data['mobile']["{$index}"]["value"] <= $performance_metrics["{$index}"]["fast"]) {
				$performance_data['mobile']["{$index}"]["rating"] = "fast";
			} else if ($performance_data['mobile']["{$index}"]["value"] <= $performance_metrics["{$index}"]["slow"]) {
				$performance_data['mobile']["{$index}"]["rating"] = "average";
			} else {
				$performance_data['mobile']["{$index}"]["rating"] = "slow";
			}

			// evaluate rating desktop
			if ($performance_data['desktop']["{$index}"]["value"] <= $performance_metrics["{$index}"]["fast"]) {
				$performance_data['desktop']["{$index}"]["rating"] = "fast";
			} else if ($performance_data['desktop']["{$index}"]["value"] <= $performance_metrics["{$index}"]["slow"]) {
				$performance_data['desktop']["{$index}"]["rating"] = "average";
			} else {
				$performance_data['desktop']["{$index}"]["rating"] = "slow";
			}			
		}
	

		unset($performance_data['desktop']['first-meaningful-paint']);
		unset($performance_data['mobile']['first-meaningful-paint']);
		
		$this->performance_data = $performance_data;
		update_option("pegasaas_performance_data", array("performance_data" => $performance_data, "last_updated" => time(), "stale_records" => 0));

		
		return $performance_data;
	}	

	function get_site_score($force_recalculation = false, $force_all = false) {
		global $pegasaas;
		$debug = false;
		$debug_rebuild = false;
		if (isset($_GET['c']) && $_GET['c'] == "force-recalculation") {
			$force_recalculation = true;
		}
		
			
		$five_minutes = 60 * 5;
		$fifteen_minutes = 60 * 15;
		//$fifteen_minutes = 60;
		// first attempt to see if there is an object in the class
		if ($force_recalculation && $force_all) {
			// bypass checks
		} else if (isset($this->score_data)) {
			if ($debug) { $pegasaas->utils->console_log("Have Existing CLASS Score Data"); }
			//print "<pre>";
			//var_dump($this->score_data);
			//print "</pre>";
			return $this->score_data;
	
		} else if ($force_recalculation) {
			$score_data = get_option("pegasaas_web_perf_score_data", array());
			
			if (isset($score_data['stale_records']) && $score_data['stale_records'] == 0) {
				$this->score_data = $score_data['score_data'];
				return $this->score_data;
			}
		
		} else if ($debug && $debug_rebuild) {
			// skip the next elseif
		} else if (false && isset($_COOKIE['pegasaas_score_data'])) {
			$pegasaas_score_data = json_decode(stripslashes($_COOKIE['pegasaas_score_data']), true);
			
			
			if ($debug) { $pegasaas->utils->console_log("Maybe Have Existing COOKIE Score Data"); }
			
			// check to see if score data date is fresh, and if so, apply it to the class data and return
			$last_updated = (isset($pegasaas_score_data['last_updated']) ? $pegasaas_score_data['last_updated'] : 0);
			if ($last_updated > time() - $five_minutes) {
				if ($debug) { $pegasaas->utils->console_log("YES Have Existing COOKIE Score Data"); }

				$this->score_data = $pegasaas_score_data['data'];
				return $this->score_data;
			}
		} else {
			$score_data = get_option("pegasaas_web_perf_score_data", array());
			
			//var_dump($score_data);
			if (isset($score_data['stale_records']) && isset($score_data['score_data']) && $score_data['stale_records'] < 15) {
				
				// if the last recalculation happened over 15 minutes ago
				if ($score_data['stale_records'] > 0 && $score_data['last_updated'] < time() - $fifteen_minutes) {
			
				
				// always recalculate if score is zero
				} else if ($score['score_data']['score'] == 0) {
					
				// otherwise, use the cached data
				} else {
					$this->score_data = $score_data['score_data'];
					return $this->score_data;
				}
			}
									 
		
			if ($debug) { $pegasaas->utils->console_log("Score Data Does Not Yet Exist"); }

		}
		//print "rebuilding site score";
		
		
		$score_data = array(
			"score"			=> 0,
			"desktop_score" => 0,
			"mobile_score"	=> 0,
			"scanned_urls"	=> 0,
			"total_urls"	=> 0,
			"opportunities" => array()
		);
		
		
		
		$pages_with_scores = $this->get_pages_with_scores("pagespeed");
		
		
		
		
		
		$total_scores		= 0;
		$mobile_scores		= 0;
		if ($debug) {
			print "<pre class='admin'>";
		}
		$count = 0;
		$modified_pages_with_scores = false;
		foreach ($pages_with_scores as $scan_record) {

			$resource_id = $scan_record->resource_id;
			$count++;
			
		
			$page_score_details = json_decode($scan_record->data, true);

			if (!isset($score_data['analytics_detected'])) {
				$score_data['analytics_detected'] = array();
				$score_data['adnetworks_detected'] = array();
			}
						
			
			$score_data['analytics_detected']  = array_merge($score_data['analytics_detected'], $this->get_analytics_loaded($page_score_details['meta']['mobile']['opportunities']['total-byte-weight']['details']['items']));
			$score_data['adnetworks_detected'] = array_merge($score_data['adnetworks_detected'], $this->get_adnetwork_loaded($page_score_details['meta']['mobile']['opportunities']['total-byte-weight']['details']['items']));

		
			if ($page_score_details['score'] == 0 && isset($page_score_details['meta']['desktop']['error'])) {
				
			} else {
				$score_data['score'] 			+= $page_score_details['score'];
				$score_data['desktop_score'] 	+= $page_score_details['score'];
				$total_scores++;
				$desktop_scores++;
			}
			
			
			
			
			
			if ($page_score_details['score'] == 0 && isset($page_score_details['meta']['desktop']['error'])) {
				
			} else {
				$score_data['score']		+= $page_score_details['mobile_score'];
				$score_data['mobile_score'] += $page_score_details['mobile_score'];
				$total_scores++;

				$mobile_scores++;
			}
			
			$score_data['scanned_urls']++;		


			
			
			
		}
		
	
		
		if ($debug) {
			print "</pre>";
		}
		//$temporary_search_filter_storage = $pegasaas->interface->results_search_filter;
	//	$pegasaas->interface->results_search_filter = ""; 
	   PegasaasUtils::log("get_site_score -- before get_all_pages_and_posts", "script_execution_benchmarks");

		$all_pages_and_posts = PegasaasUtils::get_all_pages_and_posts($use_filter = false);
		PegasaasUtils::log("get_site_score -- after get_all_pages_and_posts", "script_execution_benchmarks");

		// restore the search filter so that it shows in the "search bar"
	//	$pegasaas->interface->results_search_filter = $temporary_search_filter_storage; 
		
		//$all_pages_and_posts = PegasaasUtils::get_all_pages_and_posts();
		
		$score_data['total_urls'] = count($all_pages_and_posts);
		
		if ($score_data['score'] > 0 && $total_scores>0) {		//Adam: To Prevent a divide by zero error
			$average_score 	= $score_data['score']/$total_scores;
			$decimal_places = 0;
			
			if ($average_score > 95 && $average_score < 100) {
				
				$decimal_places = 1;
			}
			
			$score_data['score'] = number_format($average_score, $decimal_places, '.', ',');
			if ($decimal_places == 1 && fmod($score_data['score'], 1) == 0) {
				$score_data['score'] = number_format($average_score, 0, '.', ',');
			}
			
			$average_score 	= $score_data['desktop_score']/$desktop_scores;
			$decimal_places = 0;
			
			if ($average_score > 95 && $average_score < 100) {
				$decimal_places = 1;
			}
			
			$score_data['desktop_score'] = number_format($average_score, $decimal_places, '.', ',');
			if ($decimal_places == 1 && fmod($score_data['desktop_'], 1) == 0) {
				$score_data['desktop_score'] = number_format($average_score, 0, '.', ',');
			}
			
			if ($score_data['mobile_score'] > 0) {
				$average_score 	= $score_data['mobile_score']/$mobile_scores;
				$decimal_places = 0;
				if ($average_score > 95 && $average_score < 100) {
					$decimal_places = 1;
				}
				
				$score_data['mobile_score'] = number_format($average_score, $decimal_places, '.', ',');
				if ($decimal_places == 1 && fmod($score_data['mobile_score'], 1) == 0) {
						$score_data['mobile_score'] = number_format($average_score, 0, '.', ',');
				}
			}
		}
		
		
		$total_impact = 0;
		
		foreach ($score_data['opportunities'] as $rule_name => $rule) {
			$score_data['opportunities']["$rule_name"]["impact"] = $score_data['opportunities']["$rule_name"]["impact"]/$score_data['scanned_urls']; 
			$total_impact += $score_data['opportunities']["$rule_name"]["impact"];
		}
		
		$total_possible_score = 100 - $score_data['score'];
		
		if ($total_impact > 0) {
			foreach ($score_data['opportunities'] as $rule_name => $rule) {
				$impact_ratio = $score_data['opportunities']["$rule_name"]["impact"] / $total_impact;
				$score_data['opportunities']["$rule_name"]["impact"] = number_format($impact_ratio * $total_possible_score, 1, '.', ','); 
			}
		}
		PegasaasUtils::log("get_site_score before sort", "script_execution_benchmarks");

		uasort($score_data['opportunities'], array($this, "sort_site_speed_opportunities"));
		
		$psd['data'] = $score_data;
		$psd['last_updated'] = time();
		
		// do not store this data -- too big
		//@setcookie("pegasaas_score_data", json_encode($psd), 0);
		PegasaasUtils::log("get_site_score before update_option", "script_execution_benchmarks");

		
		$this->score_data = $score_data;
		
		update_option("pegasaas_web_perf_score_data", array("score_data" => $score_data, "last_updated" => time(), "stale_records" => 0));
		PegasaasUtils::log("get_site_score END", "script_execution_benchmarks");

		return $score_data;
	}
	
	function get_site_benchmark_score($force_recalculation = false, $force_all = true) {
		global $pegasaas;
		$debug = false;
		$debug_rebuild = false;
		
		if (isset($_GET['c']) && $_GET['c'] == "force-recalculation") {
			$force_recalculation = true;
		}			
		$five_minutes = 60 * 5;
		$fifteen_minutes = 60 * 15;
		
		 if ($force_recalculation && $force_all) {
			
		// bypass
		
		// first attempt to see if there is an object in the class
		} else if (isset($this->benchmark_score_data)) {
			if ($debug) { $pegasaas->utils->console_log("Have Existing CLASS Score Data"); }
			return $this->benchmark_score_data;
		
			
			
		} else if ($force_recalculation) {
			 	if ($debug) { $pegasaas->utils->console_log("Forcing Recalculation of Benchmark Data"); }
			$score_data = get_option("pegasaas_web_perf_benchmark_score_data", array());
			if (isset($score_data['stale_records']) && $score_data['stale_records'] == 0) {
				$this->benchmark_score_data = $score_data['score_data'];
				return $this->benchmark_score_data;
			}
		
		} else if ($debug && $debug_rebuild) {
			// skip the next elseif
					 	if ($debug) { $pegasaas->utils->console_log("Skipping Recalc"); }

		} else {
			 
			$score_data = get_option("pegasaas_web_perf_benchmark_score_data", array());
						 	if ($debug) { $pegasaas->utils->console_log("Possibly Recalculating"); }
			// print "<pre class='admin'>";
			//var_dump($score_data);
			// print "</pre>";

			//var_dump($score_data);
			if (isset($score_data['stale_records']) && isset($score_data['score_data']) && $score_data['stale_records'] < 15) {
				//print "less than 15";
				if ($score_data['stale_records'] > 0 && $score_data['last_updated'] < time() - $fifteen_minutes) {
				//	print "stale data";
				} else {
					$this->benchmark_score_data = $score_data['score_data'];
					
					return $this->benchmark_score_data;
				}
			}
		//	print "recalculating";
									 
		
			if ($debug) { $pegasaas->utils->console_log("Score Data Does Not Yet Exist"); }

		}
		global $pegasaas;

		$score_data = array(
			"score" => 0,
			"desktop_score" => 0,
			"mobile_score" => 0,							
			"scanned_urls" => 0,
			"total_urls" => 0,
			"scanning_urls" => 0,
			"opportunities" => array()
		);
		
		$pages_with_scores = $this->get_pages_with_scores("pagespeed-benchmark");

		//$pages_with_scores 	= $pegasaas->db->get_results("pegasaas_performance_scan", array("scan_type" => "pagespeed-benchmark"), "time DESC", "resource_id");
		
		
		
			
		
			
		//$pages_with_scores 			 = get_option("pegasaas_accelerator_pagespeed_benchmark_scores", array());
		//$pagespeed_score_requests 	 = get_option("pegasaas_accelerator_pagespeed_benchmark_score_requests", array());
		$pagespeed_score_requests 	= $pegasaas->db->get_results("pegasaas_api_request", array("request_type" => "pagespeed-benchmark"), "", "resource_id");

		$score_data['scanning_urls'] = sizeof($pagespeed_score_requests);
		
		$total_scores  = 0;
		$mobile_scores = 0;
	
		foreach ($pages_with_scores as $scan_record) {
			$resource_id = $scan_record->resource_id;

			$page_score_details = json_decode($scan_record->data, true);
		
			$score_data['scanned_urls']++;
			
			if ($page_score_details['score'] == 0 && isset($page_score_details['meta']['desktop']['error'])) {
				
			} else {
				$score_data['score'] 			+= $page_score_details['score'];
				$score_data['desktop_score'] 	+= $page_score_details['score'];
				$total_scores++;
				$desktop_scores++;
			}
			
			
			
			
			
			if ($page_score_details['score'] == 0 && isset($page_score_details['meta']['desktop']['error'])) {
				
			} else {
				$score_data['score']		+= $page_score_details['mobile_score'];
				$score_data['mobile_score'] += $page_score_details['mobile_score'];
				$total_scores++;

				$mobile_scores++;
			}
						
	
			/*
			$page_score_details = $this->get_page_benchmark_speed_opportunities($resource_id);
			
			foreach ($page_score_details as $rule) {
				$rule_id 			= $rule['rule'];
				$rule_name 			= $rule['rule_name'];
				$rule_description	= $rule['rule_description'];
				$rule_impact 		= $rule['rule_impact'];
				
				$score_data['opportunities']["{$rule_id}"] = array(
					"description" => $rule_description, 
					"name" => $rule_name, 
					"icon" => $rule['rule_icon'],
					"impact" => $score_data["opportunities"]["{$rule_id}"]["impact"] + $rule_impact
				);
			}
			*/
		}
		
		if ($score_data['scanned_urls'] == 0) {
			//return $score_data;
		} 
		$all_pages_and_posts = PegasaasUtils::get_all_pages_and_posts();
		
		$score_data['total_urls'] = count($all_pages_and_posts);

		if ($score_data['score'] > 0 && $total_scores>0) {				//Adam: To Prevent a divide by zero error
			$score_data['score'] = number_format($score_data['score']/$total_scores, 0, '.', ',');
			$score_data['desktop_score'] = number_format($score_data['desktop_score']/$desktop_scores, 0, '.', ',');

			if ($score_data['mobile_score'] > 0  && $mobile_scores>0) {//Adam: To Prevent a divide by zero error		
				$score_data['mobile_score'] = number_format($score_data['mobile_score']/$mobile_scores, 0, '.', ',');
			}
		}
		$total_impact = 0;
		foreach ($score_data['opportunities'] as $rule_name => $rule) {
			$score_data['opportunities']["$rule_name"]["impact"] = $score_data['opportunities']["$rule_name"]["impact"]/$score_data['scanned_urls']; 
			$total_impact += $score_data['opportunities']["$rule_name"]["impact"];
		}
		$total_possible_score = 100 - $score_data['score'];
		
		foreach ($score_data['opportunities'] as $rule_name => $rule) {
			$impact_ratio = $score_data['opportunities']["$rule_name"]["impact"] / $total_impact;
			$score_data['opportunities']["$rule_name"]["impact"] = number_format($impact_ratio * $total_possible_score, 1, '.', ','); 
		}

		
		uasort($score_data['opportunities'], array($this, "sort_site_speed_opportunities"));
		
		update_option("pegasaas_web_perf_benchmark_score_data", array("score_data" => $score_data, "last_updated" => time(), "stale_records" => 0));

		return $score_data;
	}	
	
	function get_pagespeed_scores_count() {
		global $pegasaas;
		$results = $this->get_pages_with_scores("pagespeed");

		return sizeof($results);
		
	}
	
	function get_pagespeed_benchmark_scores_count() {
		global $pegasaas;
		$results = $this->get_pages_with_scores("pagespeed-benchmark");

		return sizeof($results);
	}

	function get_page_score_history($resource_id) {
		global $pegasaas;
		// determine the average
		$cut_off = mktime(0,0,0,date("m") - 3);
		$score_history = array();
		
		$pagespeed_history = PegasaasUtils::get_object_meta($resource_id, "accelerator_pagespeed_history");
		$scan_records = $pegasaas->db->get_results("pegasaas_performance_scans", array("resource_id" => $resource_id, 
																				  "time" => array("comparison" => "<",
																								  "value" => $cut_off)
																				  ), "time ASC");
	
		
		// initialize date array
		for ($i = 3; $i >= 0; $i--) {
		    $year_month = date("Y-m", mktime(0,0,0,date("m") - $i));
			$score_history["{$year_month}"]['score'] = 0;
			$score_history["{$year_month}"]['scans'] = 0;																	
			$score_history["{$year_month}"]['display_date'] = date("M", mktime(0,0,0,date("m") - $i));																	
		}
		
		
		// interate through scores
		
		// determine which month they were for
		
		// get the sum
		
		
		/*
		foreach ($post_pagespeed_history as $time => $data) {
		
			if ($cut_off <= $time) {
				$year_month = date("Y-m", $time);
				$score_history["{$year_month}"]['score'] += $data['score'];
				$score_history["{$year_month}"]['scans']++;
			}
		}		
		*/
		foreach ($scan_records as $record) {
			$data = json_decode($record['data'], true);
			$year_month = date("Y-m", strtotime($data['time']));
			$score_history["{$year_month}"]["score"] += $data['score'];
		}

		foreach ($score_history as $year_month => $data) {
		  if ($data['scans'] > 0) {
		  	$score_history["{$year_month}"]['score'] = number_format($score_history["{$year_month}"]['score']/$score_history["{$year_month}"]['scans'], 1, '.', ',');	
		  }
		}
		
		return $score_history;
	}	

	/*********************************************************
	 * SORTING FUNCTIONS
	 *********************************************************/
	
	function sort_scan_items_by_score_desc($a, $b) {
		
		$ascore = $a['score'];
		$bscore = $b['score'];
		
		if ($ascore == "") {
			$ascore = 0;
			
		}
		if ($bscore == "") {
			$bscore = 0;
		}
		
		if ($a['benchmark_score'] == -2 && $b['benchmark_score'] >=0) {
			return 1;
		} else if ($b['benchmark_score'] == -2 && $a['benchmark_score'] >0) {
			return -1;
		}
		if ($ascore == $bscore) {
			return 0;
		}
		return ($ascore > $bscore) ? -1 : 1;
		
	}
	
	function sort_scan_items_by_page_importance($a, $b) {
		
		$ascore = substr_count($a['resource_id'], '/');
		$bscore = substr_count($b['resource_id'], '/');
		
		if ($ascore == 0) {
			$ascore = 1;
		}
		if ($bscore == 0) {
			$bscore = 1;
		}
		
		
		
		if ($ascore == $bscore) {
			return strnatcmp($a['slug'], $b['slug']);
			
		}
		return ($ascore < $bscore) ? -1 : 1;
		
	}	

	function caching_allows_gpsi_calls() {
		global $pegasaas;
		
		if ($pegasaas->is_apache() || $pegasaas->is_litespeed()) {
		
	  		$htaccess_location = $pegasaas->get_home_path().".htaccess";
		
			$htaccess_file = file($htaccess_location);
			$htaccess_file = implode("", $htaccess_file);
			if (strstr($htaccess_file, "RewriteCond %{QUERY_STRING} gpsi-call")) {
				return true;
			}	
		}
		
		return false;
	}
	
	function submit_scan_request() {
		global $pegasaas;
		global $test_debug;
		$debug = false;
		if ($pegasaas->in_debug_mode("submit_scan_request")) {
			$debug = true;
		}
		
		if ($pegasaas->utils->semaphore("submit_scan_requests", $wait_time = 0, $stale_if_this_many_seconds_old = 20)) {
			$allows_gpsi_calls = $this->caching_allows_gpsi_calls();
			
			/* DETERMINE THE MAXIMUM NUMBER OF CACHE BUILDS (HITS) TO INVOKE */
			$throttle_rate = PegasaasAccelerator::$settings['settings']['response_rate']['status'];
			
			if ($throttle_rate == "maximum" || $throttle_rate == "aggressive") {
				$maximum_submits_per_invocation = 5;
			
			} else if ( $throttle_rate == "normal") {
				$maximum_submits_per_invocation = 2;
			
			} else {
				$maximum_submits_per_invocation = 1;
			}
			
			$css_request_count 				= 0;
			$page_cache_request_count 		= 0;
		
		
			if ($debug) { print "<pre class='admin'>"; }
			$pegasaas->utils->console_log("before get-alll-pages-posts");
			$posts_array 	 = PegasaasUtils::get_all_pages_and_posts();
			

			
			// should sort based on accelerated status
			uasort($posts_array, array($this, 'sort_page_items_by_accelerated_status'));			

			if ($debug) { print "Size of Posts/Pages Array: ".sizeof($posts_array)."\n"; }
			
		// we need to disable filters if this is a wpml_multi_domains system, as the filters change
		// the root domain and the language specific slug in some situations when the current language is not the default language
		$wpml_multi_domains_active = PegasaasUtils::wpml_multi_domains_active();
 
		if ($wpml_multi_domains_active) {
			global $sitepress;
			
			$current_language = $sitepress->get_current_language();
			$default_language = $sitepress->get_default_language();
			$wpml_domains = wpml_get_setting( 'language_domains' );
			$wpml_domains["$default_language"] = PegasaasUtils::get_home_domain();
		
			
			if ($current_language != $default_language) {
				$disable_filters = true;
			} 
		}	
		
		if ($disable_filters) {
			$page_link_filter = PegasaasUtils::get_filter("page_link");
			$post_link_filter = PegasaasUtils::get_filter("post_link");
			remove_all_filters("page_link");
			remove_all_filters("post_link");
		}		
			foreach ($posts_array as $index => $post) {
				if ($post->slug == "") {
					//if ($test_debug) {
					//	PegasaasUtils::log_benchmark("submit_scan_request()[3896] - {$post->ID}", "debug-li", 1);
					//}
					$post->slug = PegasaasUtils::get_resource_slug($post->ID);
				}
				
				if (!$post->accelerated) {
					unset($posts_array["$index"]);
					if ($debug) { print "Post NOT ACCELERATED: {$post->slug}\n"; }

				} else {
					if ($debug) { print "Post ACCELERATED: {$post->slug}\n"; }
				}  
			}	
			
			if ($disable_filters) {
				PegasaasUtils::set_filter("page_link", $page_link_filter);
				PegasaasUtils::set_filter("post_link", $post_link_filter);	
			}		
			
			$posts_array 	 	= array_slice($posts_array, 0, PegasaasAccelerator::$settings['limits']['maximum_page_accelerations_available']);
			
			uasort($posts_array, array($pegasaas->utils, 'sort_pages_home_page_first'));
			if ($debug) { var_dump($posts_array); } 
			$pages_to_submit 	= array();
		
			$location 		 		= $pegasaas->utils->get_wp_location();
			
			$existing_requests = array();
			$existing_request_records = $pegasaas->db->get_results("pegasaas_api_request", array("request_type" => "pagespeed"));
			
			foreach ($existing_request_records as $record) {
				$resource_id = $record->resource_id;
				$existing_requests["$resource_id"] = $record;
			}
			
			$total_existing_scans 	= sizeof($existing_requests) + $pegasaas->scanner->get_pagespeed_scores_count();
			
			if ($debug) {
				print "Existing PagesSpeed Score Requests\n";
				var_dump($existing_requests);
				
				print "Existing Scans\n";
				$existing_scans = $this->get_pages_with_scores("pagespeed");
				foreach ($existing_scans as $record) {
					print "{$record->resource_id}\n";
				}
			
			}
			if ($test_debug) {
				print "<li>Existing Scans<br><ul>";
				
				$existing_scans = $this->get_pages_with_scores("pagespeed");
				foreach ($existing_scans as $record) {
					print "<li>{$record->resource_id}</li>";
				}				
				print "</ul></li>";
			}
			if (!is_array($posts_array)) {
				if ($debug) { print "Ajusted Size of Posts/Pages Array: 0\n"; }
				if ($test_debug) {
					PegasaasUtils::log_benchmark("Ajusted Size of Posts/Pages Array: 0", "debug-li", 1);

				}
			} else {
				if ($debug) { print "Adjusted Size of Posts/Pages Array: ".sizeof($posts_array)."\n"; }
				if ($test_debug) {
					PegasaasUtils::log_benchmark("Adjusted Size of Posts/Pages Array: ".sizeof($posts_array), "debug-li", 1);

				}
			}

			// get all posts that have acceleration enabled -- these are the only pages that we will request GPSI B for.
			$enabled_posts = array();
			if (is_array($posts_array)) {
				foreach ($posts_array as $post) {
				//	$resource_id = $post->resource_id;
					$resource_id = $pegasaas->utils->get_object_id($post->ID);
					if ($pegasaas->utils->has_acceleration_enabled($resource_id, $post->is_category)) {
						if ($debug) { print "Post Has Acceleration Enabled: {$resource_id}\n"; }
						if ($test_debug) { PegasaasUtils::log_benchmark("Post Has Acceleration Enabled: {$resource_id}", "debug-li", 1); }
						$enabled_posts[] = $post;
					} else { 
						if ($debug) { print "Post Has Acceleration Disabled: {$resource_id}\n"; }
						if ($test_debug) { PegasaasUtils::log_benchmark("Post Has Acceleration Disabled: {$resource_id}", "debug-li", 1); }

					}
				}
			}

			$enabled_posts = array_slice($enabled_posts,0,PegasaasAccelerator::$settings['limits']['pagespeed_scanned_pages']);
				
		
			
			// iterate through list and checked to see if there are any that do not have a recent scan date
			foreach ($enabled_posts as $post) {	
				

				$request_this_resource	= false;	
				$resource_id = $pegasaas->utils->get_object_id($post->ID);
				
				$page_level_settings 	= PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides");
				
				$post_pagespeed_history = $this->get_pages_with_scores("pagespeed", $resource_id);
			
				if ($post_pagespeed_history) {
					if ($debug) { echo "Existing Record found in History\n"; }  
					if ($test_debug) { PegasaasUtils::log_benchmark("Existing Record Found In History: {$resource_id}", "debug-li", 1); }

					$request_this_resource = true;
					
					$post_data = json_decode($post_pagespeed_history->data, true);	
			
					// if the score that we have is stale (6 months) then re-request
					if ($post_data['when'] < mktime(0,0,0,date("m"),date("d")-180) && !$existing_requests["$resource_id"]) {
						$request_this_resource = true;
						if ($debug) { echo "Existing Record is stale ({$post_data['when']}), and no existing request in queue\n"; }
						if ($test_debug) { PegasaasUtils::log_benchmark("Existing Record is stale ({$post_data['when']}), and no existing request in queue", "debug-li", 1); }
					
					// upgrade from GPSIv4 to GPSIv5 scores
					} else if ($post_data['version'] < 5 && !$existing_requests["$resource_id"]) {
						$request_this_resource = true;
					
					// if the scan returned a bad score or is blank then request a new score
					} else if ($post_data['score'] == '' && $post_data['score'] != '0') {
						if ($debug) {
							print "Existing score is blank or zero\n";
						}
						if ($test_debug) { PegasaasUtils::log_benchmark("Existing score is blank or zero", "debug-li", 1); }


						$request_this_resource = true; 
		
					} else {
					  continue;	
					}
				} else {
					if ($debug) { echo "Existing Record NOT found in History\n"; }
					if ($test_debug) { PegasaasUtils::log_benchmark("Existing Record NOT found in History {$resource_id}", "debug-li", 1); }

					if (!$existing_requests["$resource_id"] ) {	
						if ($debug) { 
						  print "Request Does Not Exist for {$resource_id}\n";
						}
					    if ($test_debug) { PegasaasUtils::log_benchmark("Request Does Not Exist for {$resource_id}", "debug-li", 1); }

						$request_this_resource = true;
					} else {
						if ($debug) {
							 print "Request Already Exists for {$resource_id}\n";
						}
						 if ($test_debug) { PegasaasUtils::log_benchmark("Request Already Exists for {$resource_id}", "debug-li", 1); }

					}
				}

			
				
			
				// if we have a stale or non-existing scan of this page, then check first to see that we have a cached page available
				if ($request_this_resource) {
					
					
					if ($debug) { print "Status ({$resource_id}): should maybe build\n"; }
					if ($test_debug) { PegasaasUtils::log_benchmark("Status ({$resource_id}): should maybe build", "debug-li", 1); }

					$this_page_caching_status = 0;
				
			 		if ((PegasaasAccelerator::$settings['settings']['page_caching']['status'] == 1 && $page_level_settings['page_caching'] != "0") ||
						(PegasaasAccelerator::$settings['settings']['page_caching']['status'] == "0" && $page_level_settings['page_caching'] == "1")){
				 		$this_page_caching_status = 1;
			 		}
		 		
				
					if ($this_page_caching_status) {
						if ($debug) { print "Status ({$resource_id}): Page Caching Enabled\n"; }
					    if ($test_debug) { PegasaasUtils::log_benchmark("Status ({$resource_id}): Page Caching Enabled", "debug-li", 1); }
						
						// check page cache
						$cache_data_map 			= PegasaasAccelerator::$cache_map;
						//get_option("pegasaas_cache_map", array());
						$url = $post->slug;
						$url						= str_replace($pegasaas->get_home_url('', 'http'), "", $url);
						$url						= str_replace($pegasaas->get_home_url('', 'https'), "", $url);
						if ($url == "") {
							$url = "/";
						}
						if ($url == "/") {
							$request_priority = 3;
						} else {
							$request_priority = 1;
						}
		 
						
						if (!$cache_data_map["{$resource_id}"]) {
							if ($debug) { print "Status ({$resource_id}): Page Cache Not Yet Built ({$url})\n"; }
					        if ($test_debug) { PegasaasUtils::log_benchmark("Status ({$resource_id}): Page Cache Not Yet Built ({$url})", "debug-li", 1); }
							
							
														

							// check to see if we maybe have a situation where there is html cache, but not in the data map, then clear it out
							$html_cache = PegasaasUtils::get_object_meta($resource_id, "cached_html");
							$pegasaas->utils->delete_object_meta($resource_id, "cached_html");
							
							

							$request_this_resource = false;
							if ($page_cache_request_count < $maximum_submits_per_invocation) {
								if (PegasaasAccelerator::$settings['limits']['monthly_optimizations'] > 0 && PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining'] == 0) {
									
									if ($debug) { print "Status ({$resource_id}): No credits -- cache not requested ({$url})\n"; }	
									if ($test_debug) { PegasaasUtils::log_benchmark("Status ({$resource_id}): No credits -- cache not requested ({$url})", "debug-li", 1); }

								} else {
									$page_cache_request_count++;
									if ($test_debug) { PegasaasUtils::log_benchmark("Status ({$resource_id}): Invoking URL ({$url})", "debug-li", 1); }

									$response = $pegasaas->utils->touch_url($url, array("headers" => array("X-Pegasaas-Priority-Optimization" => $request_priority)));
									
									if ($test_debug) { PegasaasUtils::log_benchmark("Status ({$resource_id}): Page Touched ({$url})", "debug-li", 1); }

									if ($debug) { print "Status ({$resource_id}): Page Cache Not Yet Built - Touch ({$url})\n"; }	
								}
								
							} else {
								if ($debug) { print "Status ({$resource_id}): Page Cache Not Yet Built - Exceeded Max Submits Per Invocation ({$url})\n"; }
								if ($test_debug) { PegasaasUtils::log_benchmark("Status ({$resource_id}): Page Cache Not Yet Built - Exceeded Max Submits Per Invocation ({$url})", "debug-li", 1); }

							}
						} else {
							if ($debug) { print "Status ({$resource_id}): Is Temp Page Cache  ({$url})\n"; }
							if ($test_debug) { PegasaasUtils::log_benchmark("Status ({$resource_id}):  Is Temp Page Cache ({$url})", "debug-li", 1); }

							// check to see if page cache is a temp url
							if ($cache_data_map["$url"]["is_temp"]) {
								// if there is no optimization request in the queue and there are optimizations credits remaining
								if (!$pegasaas->db->has_record("pegasaas_api_request", array("resource_id" => $resource_id, "request_type" => "optimization-request")) &&
								   PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining'] > 0) {
									
									if ($test_debug) { PegasaasUtils::log_benchmark("Status ({$resource_id}): Invoking URL ({$url})", "debug-li", 1); }

									// re-touch this url -- this will clear the cache and request a new optimization
									$response = $pegasaas->utils->touch_url($url, array("headers" => array("X-Pegasaas-Priority-Optimization" => $request_priority)));
									
									if ($test_debug) { PegasaasUtils::log_benchmark("Status ({$resource_id}): Page Touched ({$url})", "debug-li", 1); }

									// decrement the optimizations remaining here as the optimization just invoked decrements it in another process 
									PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining']--; // this is only used locally
									
									// ensure that we do not request too many pages in one hit
									$page_cache_request_count++;
								}
								// we will not request this resource while there is a temp cache available
								$request_this_resource = false;
							}
							
							
							
							
							
							
						}
					}	else {
						// we should check to make sure that CPCSS / image data exists
						// NEEDS IMPLEMENTATION
						if ($debug) {
							if ($debug) { print "Status ({$resource_id}): NOT BUILDING PAGE CACHE ({$url})\n"; }
						}
					}			
				}
 
				if ($request_this_resource) {	
					if ($debug) { print "Status ({$resource_id}): YES \n"; }
					if ($test_debug) { PegasaasUtils::log_benchmark("Status ({$resource_id}): YES", "debug-li", 1); }

					if (strstr($post->slug, "http://") || strstr($post->slug, "https://")) {
						$the_url = $post->slug;
					} else {
						$the_url = $pegasaas->get_home_url().$post->slug;
					}
					
					//print "the url: $the_url <br>";
					$nonce = $resource_id."-".$pegasaas->utils->microtime_float();
					if ($allows_gpsi_calls) {
						if (strstr($the_url, "?")) {
							$the_url .= "&";
						} else {
							$the_url .= "?";
						}
					
						
						
						
						$the_url .= "gpsi-call-".$pegasaas->utils->microtime_float();
					}
					if ($resource_id == "/") {
						$priority = true;
					} else {
						$priority = false;
					}
					if (PegasaasAccelerator::$settings['limits']['pagespeed_scanned_pages'] > $total_existing_scans) {
		
						$pages_to_submit[] = array(
							"url" => $the_url,
							"nonce" => $nonce,
							"resource_id" => $resource_id,
							"priority" => $priority,
							"callback_url" => admin_url( 'admin-ajax.php' )."?wp_rid=".$resource_id."&wp_n=".$nonce."&v=3"
						);						

						$total_existing_scans++;
					}
				} else { 
					if ($debug) { print "Status ({$resource_id}): NO \n"; }
					if ($test_debug) { PegasaasUtils::log_benchmark("Status ({$resource_id}): NO", "debug-li", 1); }

				}
			}

			//if ($pegasaas->is_standard()) {
			//	$pages_to_submit = array_slice($pages_to_submit,0,PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining']);
			//}
			
			if ($debug) {	
				var_dump(PegasaasAccelerator::$settings['limits']['pagespeed_scanned_pages']);
				var_dump($total_existing_scans);
				var_dump($pages_to_submit);
			}	
			

			if ($debug) { $pegasaas->utils->console_log("before submitting to api"); }

			if (sizeof($pages_to_submit) > 0) {
				if ($test_debug) { PegasaasUtils::log_benchmark("Have ".sizeof($pages_to_submit)." request to submit to API", "debug-li", 1); }

				$post_fields = array();
				$post_fields['version']	= 5;
				$post_fields['status']	= 1;
				$post_fields['command']	= "submit-pagespeed-queries";
				$post_fields['queries'] = json_encode($pages_to_submit);
				
				$response = $pegasaas->api->post($post_fields, array( 
																	 "blocking" => false));

				$data = json_decode($response, true);
				if ($test_debug) { PegasaasUtils::log_benchmark("API Submit Complete", "debug-li", 1); }

			if ($data['api_error'] != "") {
				 $pegasaas->utils->release_semaphore("submit_scan_requests");
				 return json_encode(array("status" => 0, "message" => 'Error: Problem communicating with pegasaas.io'));
			} else {	
	
					if ($debug) {
						var_dump($data);
					}
				
					foreach ($pages_to_submit as $request) { 
						$pegasaas->db->add_record("pegasaas_api_request", array("time" => date("Y-m-d H:i:s", time()), "request_type" => "pagespeed", "resource_id" => $request['resource_id'], "nonce" => $request['nonce']));
					}
				
					$pegasaas->utils->log("----- submit_scan_request end -----", "submit_scan_request" );
				}
			} else {
				if ($test_debug) { PegasaasUtils::log_benchmark("Have ZERO requests to submit to API", "debug-li", 1); }

			}
			if ($debug) { $pegasaas->utils->console_log("after submitting to api"); }
			if ($debug) { print "</pre>"; } 
			
			$pegasaas->utils->release_semaphore("submit_scan_requests");
		}
		
	}

	function get_rule_name($id) {
		if ($id == "LeverageBrowserCaching") {
			return "Leverage Browser Caching";
		} else if ($id == "MainResourceServerResponseTime") {
			return "Reduce Server Response Time";
		} else if ($id == "MinimizeRenderBlockingResources") {
			return "Minimize Render Blocking Resources";
		} else if ($id == "OptimizeImages") {
			return "Optimize Images";
		} else if ($id == "EnableGzipCompression") {
			return "Enable Server-Side GZIP Compression";
		} else if ($id == "MinifyCss") {
			return "Minify CSS";
		} else if ($id == "MinifyJavaScript") {
			return "Minify JavaScript";
		} else if ($id == "dom-size") {
			return "Avoid an Excessive DOM Size";
		} else if ($id == "uses-rel-preload") {
			return "Preload Key Requests";
		} else if ($id == "unminified-javascript") {
			return "Minify JavasScript";
		} else if ($id == "redirects") {
			return "Avoid Multiple Page Redirects";
		} else if ($id == "first-meaningful-paint") {
			return "First Meaningful Paint";
		} else if ($id == "efficient-animated-content") {
			return "Use Video Frmats for Animated Content";
		} else if ($id == "time-to-first-byte") {
			return "Keep Server Response Times Low (TTFB)";
		} else if ($id == "render-blocking-resources") {
			return "Eliminate Render-Blocking Resources";
		} else if ($id == "uses-optimized-images") {
			return "Efficiently Encode Images";
		} else if ($id == "uses-text-compression") {
			return "Enable Text Compression";
		} else if ($id == "uses-long-cache-ttl") {
			return "Uses Efficient Cache Policy on Static Assets";
		} else if ($id == "interactive") {
			return "Time to Interactive";
		} else if ($id == "font-display") {
			return "Ensure Text Remains Visible While Webfonts Load";
		} else if ($id == "estimated-input-latency") {
			return "Estimated Input Latency";
		} else if ($id == "uses-rel-preconnect") {
			return "Preconnect to Required Origins";
		} else if ($id == "bootup-time") {
			return "JavaScript Execution Time";
		} else if ($id == "unminified-css") {
			return "Minify CSS";
		} else if ($id == "offscreen-images") {
			return "Defer (Lazy Load) Offscreen Images";
		} else if ($id == "uses-responsive-images") {
			return "Properly Size Images";
		} else if ($id == "unused-css-rules") {
			return "Defer Unused CSS";
		} else if ($id == "speed-index") {
			return "Speed Index";
		} else if ($id == "first-cpu-idle") {
			return "First CPU Idle";
		} else if ($id == "total-byte-weight") {
			return "Avoid Enormous Network Payloads";
		} else if ($id == "mainthread-work-breakdown") {
			return "Minimize Main-Thread Work";
		} else if ($id == "first-contentful-paint") {
			return "First Contentful Paint";
		} else if ($id == "uses-webp-images") {
			return "Serve Images in Next-Generation Formats";
		} else if ($id == "critical-request-chains") {
			return "Minimize Critical Requests Depth";
		}
		
		return $id;
	}		
	
	function pegasaas_prerequest_pagespeed_score() {
		global $pegasaas;
	
		$pre_request_status = 1;
		
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			if ($_POST['resource_id'] == "") {
				print json_encode(array("status" => 0, "message" => 'Error: Blank Resource ID'));
			} else {
				$resource_id = $_POST['resource_id'];
				
				if (PegasaasAccelerator::$settings['settings']['defer_render_blocking_css']['status'] == 1) {
					//print "yes";
					$url 		= $resource_id;
					$post_id 	= url_to_postid($url);
					//$post 		= get_post($post_id);
					$post 		= $pegasaas->utils->get_post_object($resource_id);
					if ($post->is_category) {
						$post->post_type = $post->category_post_type;
					}					
					$slug = $pegasaas->utils->get_post_slug_from_resource_id($resource_id);
				
					if (strstr($slug, "http://") || strstr($slug, "https://")) {
							$the_url = $slug;
					} else {
							$the_url = $pegasaas->get_home_url().$slug;
					}	
					
					if ($post->slug == "") {
		  				$post->slug = $pegasaas->get_home_url().$resource_id;
					} else {
						$post->slug = $the_url;
						
					}
					if (false && $pegasaas->is_pro_edition()) {
						$critical_css = $pegasaas->get_critical_css($post, $resource_id);
					}
					if ($critical_css['skip']) {
						$pre_request_status = 0;
					}

				}				
				
				
				if ($pre_request_status == 0) {		
					// if it doesn't, or is queued, then return false
					print json_encode(array("status" => 0, "message" => "Critical CSS Queued"));

				} else { 	
					// if it exists, 
					// then if there is not a cached version of the page then touch it
					if (PegasaasAccelerator::$settings['settings']['page_caching']['status'] == 1) {
						// check page cache
						//$cache_data_map 			= get_option("pegasaas_cache_map", array());
						$cache_data_map 			= PegasaasAccelerator::$cache_map;
						$pattern = '/\<'.'!-- pegasaas:\/\/accelerator - cached copy saved to filesystem @\s(.*?\s.*?)\s(.*?)--\>/';
						$matches = array();

						if (!$cache_data_map["{$resource_id}"]) {
	
						
							$response = $pegasaas->utils->touch_url($the_url, array("headers" => array("X-Pegasaas-Priority-Optimization" => 3)));

	

							preg_match($pattern, $response, $matches);
	
							$time_date = $matches[1];
							
							$time_date_stamp 	= strtotime($time_date);
							$current_time 		= time();
							$buffer_seconds 	= 60;
							// if time date stamp was recent, then we assume this is a fresh copy, and we're good to go
							if ($time_date_stamp + $buffer_seconds > $current_time) {
								$status = 1;
								$message = "OK To Scan";
								// otherwise, if thirdparty cache exists, clear it for this resource and return 0
							} else if ($pegasaas->cache->godaddy_exists()) {
								$post_id = url_to_postid($the_url);
								
								$status = 0;
								$message = "Likely Third Party Cache Existing ($time_date).  Purging Third Party Cache of Resource {$resource_id} / {$post_id}";
								$pegasaas->utils->log($message);
								
								$pegasaas->cache->clear_third_party_cache($post_id);
							} else {
								$status = 0;
								$message = "Stale Cache ($time_date) for Non-Existant Resource ({$resource_id}).";
								$pegasaas->utils->log($message);
							}
							
							
							
						// if page caching is disabled, we should probably check for stale third party cache here
						} else {
							$status = "1";
							$message = "OK To Scan";
						}
						
					}	else {
						$status = "1";
						$message = "Page Caching Disabled";
						//print "page caching off";
					}		
				//print "status: $status";
				print json_encode(array("status" => $status, "message" => $message));
			}
				
			
			
		
			

			}

		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response

	}	
	
	function pegasaas_cancel_request_pagespeed_score() {
		
		
	}
	
	
}
?>