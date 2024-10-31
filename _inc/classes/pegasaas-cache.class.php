<?php

class PegasaasCache {
	var $settings;
	var $site_cache_cleared = false;
	static $geo_modifier = "";
	static $is_remote_call = false;

	private $supported_caching_plugins;

	function __construct() {
		// no need to clear cache on wp-json endpoint -- can invoke long TTFB in admin area due to clearing multi-server-cache
		if (!strstr($_SERVER['REQUEST_URI'], "wp-json/")) {
			add_action( 'edit_post', array($this, 'handle_edit_post') );
		}

		if ($this->cloudflare_exists()) {
			$this->supported_caching_plugins =
			array('clear-cache-for-widgets',
					'sg-cachepress',
				 	'pantheon-advanced-page-cache',
				 	'redis-cache',
				    'wpe-advanced-cache-options',
				    'clear-w3tc-cache',
				    'ecwid-clear-cache-w3tc-k8s',
				 	'no-cache-ajax-widgets');
		} else {
			$this->supported_caching_plugins =
			array('clear-cache-for-widgets',

					 'wp-rocket',

					 'litespeed-cache',

					 'sg-cachepress',
				 	'pantheon-advanced-page-cache',
				 	'redis-cache',
				    'wpe-advanced-cache-options',
				    'clear-w3tc-cache',
				    'ecwid-clear-cache-w3tc-k8s',
				 	'no-cache-ajax-widgets');
		}
		$cloudflare_already_checked_this_instance = false;
	}

	static function disable_wp_optimize_cache() {
		WP_Optimize()->get_page_cache()->disable();
		WPO_Page_Cache::instance()->disable();

		if (!class_exists('WP_Optimize_Cache_Commands')) include_once(WPO_PLUGIN_MAIN_PATH . 'cache/class-cache-commands.php');

		$cache_commands = new WP_Optimize_Cache_Commands();
		$cache_commands->disable();
	}

	function handle_post_change_state($new_status, $old_status, $post) {
		global $pegasaas;
		global $test_debug;
		$ignore_post_types = array("shop_order", "cw_admin_audit");


		if ($test_debug) {
			print "<li>Executing transition_post_status/handle_post_change_state {$new_status} {$old_status} {$post} Hook</li>";
		}

		if (isset($post) && isset($post->post_type) && in_array($post->post_type, $ignore_post_types)) {
			if ($test_debug) {
				print "<li>Ignoring Cache Clear of {$post->post_type}</li>";
			}
			return;
		}

		$resource_id = PegasaasUtils::get_object_id($post->ID);

		$pegasaas->utils->log("Handle Post Change State: {$new_status} ({$resource_id} / {$post->post_type})", "caching");

		if ($new_status == "trash") {
			$resource_id = str_replace("__trashed", "", $resource_id);
			$pegasaas->utils->log("Trashing: $resource_id", "caching");

			$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides");
			
			if ($page_level_settings['accelerated']) {
				if ($test_debug) {
					print "<li>Disabling Premium Acceleration for {$post} </li>";
				}
				// disabling the page automatically clears the page cache and the performance scans for the resource
		    	$pegasaas->disable_accelerator_for_page($resource_id);
			} else {
				if ($test_debug) {
					print "<li>Skipping Disabling Premium Acceleration for {$post} (wasn't enabled in the first place) but still clearing cache</li>";
				}

				if (file_exists(PEGASAAS_CACHE_FOLDER_PATH."{$resource_id}index-temp.html")) { 
					$pegasaas->cache->clear_page_cache($resource_id);
				}
				
			}
		} else {
			$pegasaas->utils->log("Clearing Page Cache & Performance Scans: $resource_id", "caching");
			$pegasaas->cache->clear_page_cache($resource_id);
			$pegasaas->scanner->clear_performance_scans($resource_id);
		}
	}

	function clear_tags() {
		global $pegasaas;
		self::rm_dir_tree("tag/");
		global $wpdb;

		$query = "DELETE FROM {$wpdb->prefix}pegasaas_page_cache WHERE resource_id LIKE '/tag/%'";
		$wpdb->query($query);

	}

	function clear_categories() {
		global $pegasaas;
		self::rm_dir_tree("category/");
		global $wpdb;

		$query = "DELETE FROM {$wpdb->prefix}pegasaas_page_cache WHERE resource_id LIKE '/category/%'";
		$wpdb->query($query);

	}

	function clear_extended_coverage() {
		global $pegasaas;

		global $wpdb;
		$rows = $pegasaas->db->get_results("pegasaas_page_cache", array("data" => array("comparison" => " LIKE ", "value" => '%"is_extended":true%' )));

		foreach ($rows as $row) {

			$resource_id = $row->resource_id;
			$fields = array("time" => date("Y-m-d H:i:s"),
						   "resource_id" => $resource_id,
						   "task_type" => "clear_html_cache");
			$pegasaas->db->add_record("pegasaas_queued_task", $fields);
		}
	}

	static function get_cache_content_url($file_name, $file_type = "") {
		global $pegasaas;

		$cdn 				= PegasaasAccelerator::$settings['settings']['cdn']['status'] == 1;
		$cache_server 		= PegasaasCache::get_cache_server(false, $file_type);
		$resource_id 		= PegasaasUtils::get_object_id();
		list($resource_path) = explode("?", $resource_id);
		$content_url 		= content_url();

		print $content_url."<br>";

		$cache_content_url = str_replace("https://".$_SERVER['HTTP_HOST']."/", $cache_server, $content_url);
		$cache_content_url = str_replace("http://".$_SERVER['HTTP_HOST']."/", $cache_server, $cache_content_url);

		$resource_path = rtrim($resource_path, '/').'/'; // ensure there is a trailing slash

		if ($cdn) {
			$href 	= $cache_content_url;
		} else {
			$href 	= $content_url;
		}
		//$resource_path = $pegasaas->utils->strip_query_string($file_name);
		$href .= "/pegasaas-cache{$resource_path}{$file_name}";

		return $href;

	}


	static function write_cache_file($resource_id, $file_name, $file_contents) {
		global $pegasaas;
		$resource_path = $pegasaas->utils->strip_query_string($resource_id);
		$path =  WP_CONTENT_DIR."/pegasaas-cache{$resource_path}";

		if (!is_dir($path)) {
			$pegasaas->cache->mkdirtree(WP_CONTENT_DIR."/pegasaas-cache{$resource_path}", 0755, true);
		}

		$file_path = "{$path}{$file_name}";

		$fp = @fopen($file_path, "w");

		if ($fp) {
			@fwrite($fp, $file_contents);
			@fclose($fp);
		}

	}

	function clear_pegasaas_file_cache() {

		$this->rm_dir_tree();
		delete_option("pegasaas_cache_map");

	}

	private function rm_dir_tree($path = "") {
		if (strstr($path, "../") !== false) {
			return false;
		}
		$path 	= ltrim($path, "/");
		$dir  	= PEGASAAS_CACHE_FOLDER_PATH."/{$path}";
		$folder_files = @scandir($dir);

		if (is_array($folder_files)) {
			$files = array_diff($folder_files, array('.','..'));
			foreach ($files as $file) {
				(is_dir("$dir/$file")) ? $this->rm_dir_tree("$path/$file") : unlink("$dir/$file");
			}
		}

    	return @rmdir($dir);
	}


	function check_cache() {

		// BRANDON NEED TO WORK IN HERE, check of geot_country cache file exists
		global $pegasaas;
		$get_args = $_GET;

		//print "start";

		$pegasaas->utils->log("Check Cache Start", "caching");

		$debug 		= false;

		// if the page has been requested with rebuild-js=1 in the query string, then do not cache this page
		if (isset($get_args['rebuild-js']) && $get_args['rebuild-js'] == 1) {
			return;
		}

		// if this is a post submission, we do not want to show a cached page
		if ($_SERVER['REQUEST_METHOD'] != 'GET' && $_SERVER['REQUEST_METHOD'] != "HEAD") {
			$pegasaas->utils->log("Check Cache non-GET request method detected", "caching");

			return;
		}


		if (!PegasaasAccelerator::$settings) {
			PegasaasAccelerator::$settings = get_option("pegasaas_settings");
		}

		$settings 	= PegasaasAccelerator::$settings;

		if ($settings['status'] == 0) {
			if ($debug) { print "<!-- pegasaas accelerator://global status disabled -->\n"; }
			return false;
		} else {
			if ($settings['settings']['gzip_compression']['status'] == '1') {
				if (method_exists($pegasaas, "is_wordlift_api") && !$pegasaas->is_wordlift_api()) {
					if (false) {
						// this method was causing the 404 -> 301 redirection plugin to not work, as we've pushed the
						// contents of an existing buffer out
						// this was changed on 2020-03-06
						ob_flush();
						ob_start("ob_gzhandler");

					} else {
						// new functionality 2020-03-06
						// fetch any existing output buffer
						$contents = ob_get_clean();
						// enable gzip handling
						ob_start("ob_gzhandler");
						// dump any existing output buffer to the buffer
						print $contents;
					}

				}
			}
			if ($debug) { print "<!-- pegasaas accelerator://global status enabled ({$settings['status']}) -->\n"; }
		}

		$pegasaas->utils->require_wp_include("pluggable");

		$accelerate_explicitly_on = false;
		if (isset($_GET['accelerate']) && $_GET['accelerate'] == "on") {
			$accelerate_explicitly_on = true;
		}
	
		// if there is no function "is_user_logged_in" then likely it is a file that it outside of the full
		// scope of WordPress, just using a WPDB object, such as is used in some on-the-fly data files in some themes
		if (!function_exists("is_user_logged_in")) {
			return;

		// if the user is logged in, we do not want to cache any pages as the content may be dynamic for them
		} else if (is_user_logged_in() && !$accelerate_explicitly_on) {

			return;

		// if WooCommerce is installed, and the user has a WooCommerce cookie (meaning they have something in their cart)
		// then we should not show cached pages
		// "is_woocommerce() doesn't exist when check_cache() is called
		//} else if (function_exists("is_woocommerce") && $this->has_woocommerce_cookie()) {
		} else if ($this->has_woocommerce_cookie()) {
			return;


		} else if ($this->has_yith_wishlist()) {
			return;

		//} else if (class_exists("WP_eCommerce") && $this->has_wpecommerce_cookie()) {
		} else if ($this->has_wpecommerce_cookie()) {
			return;

		} else if ($this->has_edd_items_in_cart()) {
			return;

		} else if (PegasaasAccelerator::$settings['settings']['accelerated_mobile_pages']['status'] == 1 && $pegasaas->utils->is_mobile_user_agent() && !strstr($pegasaas->utils->get_uri_args(), "gpsi-call")) {
		
	
			return;
		}


		if (method_exists($pegasaas, "is_wordlift_api") && $pegasaas->is_wordlift_api()) {

			$pegasaas->utils->log("Check Cache: WordLift Request Detected", "caching");
			if ($debug) { print "<!-- pegasaas accelerator://is wordlift -->\n"; }

			$cache_this_page = true;
		} else {

			$object_id 			= PegasaasUtils::get_object_id();
			$cached_resource 	= PegasaasUtils::get_object_meta($object_id, "cached_html");
			if ($pegasaas->utils->does_plugin_exists_and_active("geotargetingwp")) {
				$geo_modifier 		= $this->get_cache_geo_modifier();
			} else {
				$geo_modifier = "";
			}

			$request_args 		= $pegasaas->utils->get_uri_args();
			$object_id_args 	= $pegasaas->utils->get_uri_args($object_id);
			$request_args		= $pegasaas->utils->strip_ignored_query_arguments($request_args);
			$object_id_args 	= $pegasaas->utils->strip_ignored_query_arguments($object_id_args);
			$args 				= trim(str_replace($object_id_args, "", $request_args), "&");

			if ($geo_modifier != "") {
  				$args = $geo_modifier;
			}




			if ($args == "" || strstr($request_args, "gpsi-call")) {
				$args = "NULL";
			}

			

			$cached_page 		= @$cached_resource["{$args}"];
			$pegasaas->utils->log("Check Cache args: {$args}", "caching");
			$pegasaas->utils->log("Check Cache object id: {$object_id}", "caching");


			if (@$cached_page['when_cached'] == "") {
				$adjusted_time = 0;
				$pegasaas->utils->log("Check Cache Cached Page When Cached: Is Blank", "caching");


			} else {
				$pegasaas->utils->log("Check Cache Cached Page When Cached: {$cached_page['when_cached']}", "caching");
				$pegasaas->utils->log("Check Cache Date Format: ".get_option( 'date_format' ), "caching");
				$pegasaas->utils->log("Check Cache Time Format: ".get_option( 'time_format' ), "caching");
				$date_reg = date('Y-m-d H:i:s', $cached_page['when_cached']);
				$date_from_gmt = get_date_from_gmt($date_reg,get_option( 'date_format' )." ".get_option('time_format') );
				$pegasaas->utils->log("Check Cache Regular Date: {$date_reg}", "caching");
				$pegasaas->utils->log("Check Cache GMT Date: {$date_from_gmt}", "caching");
				$adjusted_time = strtotime(get_date_from_gmt($date_reg ));
				$pegasaas->utils->log("Check Cache Adjusted Time: {$adjusted_time}", "caching");

			}



			$page_level_settings = PegasaasUtils::get_object_meta($object_id, "accelerator_overrides");



			$cache_this_page = false;
		}

		// note: the is_on_excluded_page does not return false if this is a 404, as this call to "check_cache" happens early in the lifecycle before the query is evaluated
		if ($settings['status'] == 1 && $_GET['accelerate'] != "off" && !$pegasaas->is_on_excluded_page()) {
			 if ($page_level_settings['accelerated'] == "1" &&
				 (($settings['settings']['page_caching']['status'] >= 1 && $page_level_settings['page_caching'] != "0")
					 ||
					($settings['settings']['page_caching']['status'] == "0" && $page_level_settings['page_caching'] >= 1))

				 ){

				 add_filter( 'do_rocket_generate_caching_files', '__return_false', 999 ); // Disable WP rocket caching.
				 define('DONOTCACHEPAGE', true); // disable W3 Total Cache from caching
				 $cache_this_page = true;

			 // if the page is not boosted, but caching is still enabled, then we will still cache the semi-optimized page
			 } else if ($page_level_settings['accelerated'] != "1" && $settings['settings']['page_caching']['status'] >= 1) {
				 define('DONOTCACHEPAGE', true); // disable W3 Total Cache from caching
				 $cache_this_page = true;
			 }
		 }

		if ($cache_this_page) {

		

			$pegasaas->utils->log("Check Cache -- cache this page true", "caching");

			 // if a cached page doesn't exist, or the cached_page is stale, provided the user is not logged in
			 // then save a copy at the end of the page call
			if (method_exists($pegasaas, "is_wordlift_api") && $pegasaas->is_wordlift_api()) {

			} else if ($object_id == "/") {
				$post_obj = get_page_by_path("", 'OBJECT', array("page","post"));
			} else {
				$post_obj = get_page_by_path($object_id, 'OBJECT', array("page","post"));
			}

			 if ($debug) {
				 print "\n<!--";
				 print "post_obj->ID: {$post_obj->ID} / {$object_id} \n";
				 print "cached_page['when_cached']: {$cached_page['when_cached']} / ".date("Y-m-d H:i:s", $cached_page['when_cached'])."\n";
				 print "post_modified: ".strtotime($post_obj->post_modified)." / ".date("Y-m-d H:i:s", strtotime($post_obj->post_modified))."\n";
				 print "cached_page['when_cached'] adjusted: {$adjusted_time} / ".date("Y-m-d H:i:s", $adjusted_time)."\n";
				 print "-->\n";
				// exit;

			 }

			 $file_based_caching_global = $settings['settings']['page_caching']['status'] == 1 || $settings['settings']['page_caching']['status'] == 2;
			 $db_based_caching_global   = $settings['settings']['page_caching']['status'] == 3;

			 if ($page_level_settings['page_caching'] == "") {
				 $page_level_settings['page_caching'] = 1;
			 }

			 $bypass_staging_mode = false;
			 if (isset($page_level_settings['staging_mode_page_is_live']) && $page_level_settings['staging_mode_page_is_live'] == 1) {
				 $bypass_staging_mode = true;
				// print "bypass staging mode";
				 $pegasaas->utils->log("Check Cache -- Bypass Staging Mode is 1", "caching");
			 }
		//	var_dump($page_level_settings);
			//print $pegasaas->in_development_mode();

			 if ($page_level_settings['page_caching'] == 1) {
				 if ($file_based_caching_global) {
					 $caching_type = "file";
				 } else {
					 $caching_type = "db";
				 }
			 } else if ($page_level_settings['page_caching'] == 2) {
				 $caching_type = "file";
			 } else if ($page_level_settings['page_caching'] == 3) {
				 $caching_type = "db";
			 } else {
				 $caching_type = "none";
			 }
			if ($debug) {
				print "<!-- caching type $caching_type -->\n";
			}

			// we should bypass the cache
			if ($_SERVER['HTTP_X_PEGASAAS_BYPASS_CACHE'] == 1) {
				$this->save_cached_copy = false;
				return;
			}

			$pegasaas->utils->log("Check Cache -- caching type {$caching_type}", "caching");

			 if ($caching_type == "file") {
				 // if this the cache filename is relative to the cahce folder path, then we
				 // should pre-pend the cache folder path so that the file name is compatible
				 // with legacy operations in this function
				 if (isset($cached_page['relative-to-cache-folder-path'])) {
					 $cached_page['file'] = PEGASAAS_CACHE_FOLDER_PATH.$cached_page['file'];
				 }
				 $explicitly_show_cached_page_if_exists = false;
				 if (isset($_GET['accelerate']) && $_GET['accelerate'] == "on") {
					 $explicitly_show_cached_page_if_exists = true;
				 } else if (!isset($_GET['accelerate']) && strstr($_SERVER['HTTP_USER_AGENT'], "Chrome-Lighthouse")) {
					 $explicitly_show_cached_page_if_exists = true;
				 }

				 // check to see if the cached page is a directory on the file system
				 // because if we check to see that the file exists, and we find it there, then we'll end
				 // up serving a blank response.  Example:<br>
				 // /pegasaas-cache/index.html/someotherfile.css
				 // if /pegasaas-cache/index.html (a cache file) doesn't actually exist
				 if (isset($cached_page['file']) && $cached_page['file'] != '' && file_exists($cached_page['file']) && is_dir($cached_page['file'])) {
				    $is_dir = true;
				 } else {
				    $is_dir = false;
				 }


				 // provided there are no record of a file cache, or the file cache deoesn't exist, and the user is not logged in
				 // then proceed with whatever setting has already been pre-determined above
				 if ((@$cached_page['file'] == "" || !@file_exists($cached_page['file']) || $is_dir) && !is_user_logged_in()) {
					$pegasaas->utils->log("Check Cache -- cache this page -- file doesn't exist or is stale and user is not logged in", "caching");
					$pegasaas->utils->log("Check Cache A {$cached_page['file']}", "caching");
					$pegasaas->utils->log("Check Cache B ".@file_exists($cached_page['file']), "caching");
					$pegasaas->utils->log("Check Cache C {$adjusted_time}", "caching");
					$pegasaas->utils->log("Check Cache D ".strtotime($post_obj->post_modified), "caching");
					$pegasaas->utils->log("Check Cache E ".is_user_logged_in(), "caching");

                    $pegasaas->utils->log("Check Cache -- Should save cached copy as: file ", "caching");

					// explicitly saving cache - June 7, 2021 -- otherwise, cache was not saving
					$this->save_cached_copy = true;

				// if we're in development mode, and we're NOT bypassing the staging mode because this page is set to live or
				// viewing it with a query string of "accelerate=on", then we should build save the cache
				} else if ($pegasaas->in_development_mode() && !$bypass_staging_mode && !$explicitly_show_cached_page_if_exists) {
					 $pegasaas->utils->log("Check Cache -- in development mode, is staged page, no cache dislayed: {$cached_page['file']}", "caching");
					 if (!file_exists($cached_page['file'])) {
						 $this->save_cached_copy = true;
					 } 


				// display the cache file provided the user is not logged in, or in the event that they are logged in, that the
				// querystring is accelerate=on
				} else if (!is_user_logged_in() || $accelerate_explicitly_on) {

					$this->save_cached_copy = true;
					if (!is_user_logged_in()) {
						$pegasaas->utils->log("Check Cache -- user is not logged in, display cached file: {$cached_page['file']}", "caching");
					} else if ($accelerate_explicitly_on) {
						$pegasaas->utils->log("Check Cache -- user is logged in but accelerate=on, display cached file: {$cached_page['file']}", "caching");
					}

					if (!file_exists($cached_page['file'])) {
						$pegasaas->utils->log("Check Cache -- cache this page -- file doesn't exist: {$cached_page['file']}", "caching");

						// we don't want to save this buffer, as it may include logged in elements
						if (is_user_logged_in()) {
							$this->save_cached_copy = false;
						}

						// no cache exists, so lets just exit
						return;
					} else {
						$pegasaas->utils->log("Check Cache -- cache this page -- file does exist: {$cached_page['file']}", "caching");

					}

					//print "YaaaaH";
					//	exit;
					if (!method_exists($pegasaas, "is_wordlift_api") && !$pegasaas->is_wordlift_api()) {
						
					} else {

						$pegasaas->utils->get_total_page_build_time(true);
						if (!headers_sent() || true) {
							//print "yyyyyy";
							header("X-Pegasaas-Cache: HIT-VIA-PLUGIN", true);
							header("X-Pegasaas-Cache-Message: displaying cached file resource ({$object_id}) // built {$when_created} // requested at {$request_date_time}");
						} else {
							//print "headers already sent";
						}
					}

					 $when_created 			= date("Y-m-d H:i:s (T)", $cached_page['when_cached']);
					 $cached_page['html'] 	= $pegasaas->apply_powered_by_message($cached_page['html']);
					 if ($_SERVER['REQUEST_METHOD'] == "HEAD") {
						//return;
					 } else {
						 readfile($cached_page['file']);
					 }

					 $request_date_time = date("Y-m-d H:i:s (T)");

					
					 die();
				 }

			 } 
		 }
		 $pegasaas->utils->log("$"."pegasas->cache->save_cached_copy: ".$pegasaas->cache->save_cached_copy, "caching");

	}

	function cloudflare_exists() {
		global $pegasaas;

		$last_exists_query_result	= get_option("pegasaas_cloudflare_last_exists_query_result", false);
		$last_exists_query_time 	= get_option("pegasaas_cloudflare_last_exists_query_time", 0);
		$ten_minutes 	= 60 * 10;
		$thirty_minutes = 60 * 30;
		$one_day		= 60 * 60 * 24;

		if (isset($_GET['check-cloudflare'])) {
			$ten_minutes = 0;
			$one_day = 0;
			$thirty_minutes = 0;
		}


		if ($this->cloudflare_already_checked_this_instance) {
			return $last_exists_query_result;
		} else if ($last_exists_query_time > time() - $thirty_minutes) {
			return $last_exists_query_result;
		} else if ($last_exists_query_result && $last_exists_query_time > time() - $one_day) {
			return $last_exists_query_result;
		}

		$headers['X-Auth-Key'] 	 = $api_key;
		$headers['X-Auth-Email'] = $account_email;
		$headers['Content-Type'] = "application/json";
		$url 	= "https://api.cloudflare.com/client/v4/user";

		$pegasaas->utils->log("Check Cloudflare via ".PEGASAAS_CLOUDFLARE_TEST_ENDPOINT, "cloudflare");
		$pegasaas->utils->log("Current Time: ".time(), "cloudflare");
		$pegasaas->utils->log("Last Exists Query Time: ".$last_exists_query_time, "cloudflare");
		$pegasaas->utils->log("Force Cloudflare Check: ".(isset($_GET['check-cloudflare']) ? "YES" : "no"), "cloudflare");

		$args 	= array(
					  "method" => "POST",
					   'sslverify' => 'false',
						"body" => array("url" => PEGASAAS_ACCELERATOR_URL."assets/css/admin.css",
									  "api_key" => PegasaasAccelerator::$settings['api_key']));

		// query the headers of a static file, rather than a page, as a page load may incur server resources
		$response = wp_remote_request(PEGASAAS_CLOUDFLARE_TEST_ENDPOINT, $args);
		$this->cloudflare_already_checked_this_instance = true;

		if (is_a($response, "WP_Error")) {
			$detected_cloudflare = false;

			$pegasaas->utils->log("Error Getting Results", "cloudflare");

		} else {

			$detected_cloudflare = $response['body'] == "1";
		}

		$pegasaas->utils->log("Cloudflare Exists ? ".($detected_cloudflare ? "YES" : "NO"), "cloudflare");



		update_option("pegasaas_cloudflare_last_exists_query_result", $detected_cloudflare);
		update_option("pegasaas_cloudflare_last_exists_query_time", time());

		return $detected_cloudflare;

	}

	function mod_pagespeed_exists($force = false) {
		global $pegasaas;
		$debug = false;

		$last_exists_query_result	= get_option("pegasaas_mod_pagespeed_last_exists_query_result", false);
		$last_exists_query_time 	= get_option("pegasaas_mod_pagespeed_last_exists_query_time", 0);

		if ($debug) {
		   print "last query time: ".$last_exists_query_time;
		   print "<br>force: $force<br>";
		}
		$ten_minutes 	= 60 * 10;
		$thirty_minutes = 60 * 30;
		$one_day		= 60 * 60 * 24;

		if (isset($_GET['check-mod-pagespeed']) || $force) {
			$ten_minutes = 0;
			$one_day = 0;
			$thirty_minutes = 0;
			$last_exists_query_time = 0;
		}


		if ($this->mod_pagespeed_already_checked_this_instance) {
			return $last_exists_query_result;
		} else if ($last_exists_query_time > 0) {
			return $last_exists_query_result;
		}





		// query the headers of a static file, rather than a page, as a page load may incur server resources
		$response = wp_remote_request($pegasaas->get_home_url());

		$this->mod_pagespeed_already_checked_this_instance = true;

		if (is_a($response, "WP_Error")) {
			$detected_varnish = false;

			$pegasaas->utils->log("Error Getting Results", "varnish");

		} else {
			$http_response = $response['http_response'];
			if ($debug) {
				print "<pre>";
				var_dump($response['headers']);
				print "</pre>";
			}

			//$response = $http_response->get_response_object();

			// this can result in a false positive
		//	if ($response->status_code == 200 || $response->status_code == 201) {



				if (isset($response['headers']['x-mod-pagespeed'])) {
					$detected_mod_pagespeed = true;
				}
		//	}
		}

		$pegasaas->utils->log("Mod_Pagespeed Exists ? ".($detected_mod_pagespeed ? "YES" : "NO"), "mod_pagespeed");



		update_option("pegasaas_mod_pagespeed_last_exists_query_result", $detected_mod_pagespeed);
		update_option("pegasaas_mod_pagespeed_last_exists_query_time", time());



		return $detected_mod_pagespeed;




	//	X-Mod-Pagespeed: 1.13.35.2-0
	}


	function varnish_exists($force = false) {
		global $pegasaas;

		$last_exists_query_result	= get_option("pegasaas_varnish_last_exists_query_result", false);
		$last_exists_query_time 	= get_option("pegasaas_varnish_last_exists_query_time", 0);
		$ten_minutes 	= 60 * 10;
		$thirty_minutes = 60 * 30;
		$one_day		= 60 * 60 * 24;

		if (isset($_GET['check-varnish']) || $force) {
			$ten_minutes = 0;
			$one_day = 0;
			$thirty_minutes = 0;
			$last_exists_query_time = 0;
		}


		if ($this->varnish_already_checked_this_instance) {
			return $last_exists_query_result;
		} else if ($last_exists_query_time > 0) {
			return $last_exists_query_result;
		}



		$args 	= array("method" => "PURGE",
					     "sslverify" => "false");

		// query the headers of a static file, rather than a page, as a page load may incur server resources
		$response = wp_remote_request($pegasaas->get_home_url(), $args);

		$this->varnish_already_checked_this_instance = true;

		if (is_a($response, "WP_Error")) {
			$detected_varnish = false;

			$pegasaas->utils->log("Error Getting Results", "varnish");

		} else {
			$http_response = $response['http_response'];
			$response_object = $http_response->get_response_object();

			// this can result in a false positive
			if ($response_object->status_code == 200 || $response_object->status_code == 201) {

				// so we should check for the x-varnish header
				// works for openresty server DreamHost DreamPress
				
				// disabling this next wp_remote_request as it is hitting the home page before the home page can be enabled for acceleration.
				// which is causing installation issues.  We should just check the existing response message.
				/*
				$response = wp_remote_request($pegasaas->get_home_url());
				if (is_a($response, "WP_Error")) {
					return false;
				}
				*/
				if (isset($response['headers']['x-varnish'])) {
					$detected_varnish = true;
				}
			}
		}

		$pegasaas->utils->log("Varnish Exists ? ".($detected_varnish ? "YES" : "NO"), "varnish");



		update_option("pegasaas_varnish_last_exists_query_result", $detected_varnish);
		update_option("pegasaas_varnish_last_exists_query_time", time());

		return $detected_varnish;

	}

	function purge_unoptimized_cached_images() {
		global $pegasaas;

		$cached_images = get_option("pegasaas_image_cache");
		foreach ($cached_images as $filename => $image) {
			if ($image['optimized'] === false) {
				$file_path = PEGASAAS_CACHE_FOLDER_PATH."/".$filename;
				@unlink($file_path);
				unset($cached_images["$filename"]);
			}
		}
		update_option("pegasaas_image_cache", $cached_images);

	}


	function purge_optimized_cached_images() {
		global $pegasaas;

		$cached_images = get_option("pegasaas_image_cache");
		foreach ($cached_images as $filename => $image) {
			if ($image['optimized'] === true) {
				$file_path = PEGASAAS_CACHE_FOLDER_PATH."/".$filename;
				@unlink($file_path);
				unset($cached_images["$filename"]);
			}
		}
		update_option("pegasaas_image_cache", $cached_images);

	}

	function purge_all_local_cached_images() {
		$this->purge_optimized_cached_images();
		$this->purge_unoptimized_cached_images();


	}


	function cloudflare_zone_id_valid($force_check = false) {
		global $pegasaas;
		$domain 		= $_SERVER['HTTP_HOST'];
		$account_email 	= get_option("pegasaas_cloudflare_account_email", "");
		$api_key 		= get_option("pegasaas_cloudflare_api_key", "");
		$zone_id 		= get_option("pegasaas_cloudflare_zone_id", "");
		$last_auth_query_result	= get_option("pegasaas_cloudflare_last_zone_id_query_result", false);

		$one_day = 86400; // seconds
		$authorization_type = get_option("pegasaas_cloudflare_authorization_type", 0);


		// if we are using a global api key, then we can query the zones, and go with the first one, although not necessarily recommended
		if ($zone_id == "" && $authorization_type == 0) {
			return true;
		}


		$last_cloudflare_auth_query = get_option("pegasaas_cloudflare_last_zone_id_query_time", 0);

		if (!$force_check && $last_cloudflare_auth_query > time() - $one_day) {
			return $last_auth_query_result;
		}

		if ($authorization_type == 1) {
			$headers['Authorization'] = "Bearer {$api_key}";
		} else {
			$headers['X-Auth-Key'] = $api_key;
			$headers['X-Auth-Email'] = $account_email;
		}

		$headers['Content-Type'] = "application/json";


		// get zones
		$url = "https://api.cloudflare.com/client/v4/zones/{$zone_id}/";
		$args = array("headers" => $headers,
					  "method" => "GET");


		$response = wp_remote_request($url, $args);
		$cf_response = json_decode($response['body'], "true");
	//	$pegasaas->utils->log("Confirm Cloudflare for {$domain}: {$response['body']}", "caching");

		if ($cf_response['success']) {
			$pegasaas->utils->log("Cloudflare zone id valid", "cloudflare");

			update_option("pegasaas_cloudflare_last_zone_id_query_result", $cf_response['success']);
			update_option("pegasaas_cloudflare_last_zone_id_query_time", time());

			return true;

		} else {
			$pegasaas->utils->log("Cloudflare zone id invalid", "cloudflare");

			return false;
		}
	}

	function cloudflare_condition_settings() {
		global $pegasaas;
		$critical_logging = false;
		$pegasaas->utils->log("Conditioning Cloudflare Settings", "cloudflare");

		// disable the html minification -- we already minify, but need to see the comments that are in the footer
		$request = json_encode(array("value" => array("css" => "on","html" => "off", "js" => "on")));
		$response = self::cloudflare_api("PATCH", "settings/minify", $request);
		if (is_a($response, "WP_Error")) {
			$pegasaas->utils->log("Conditioning Cloudflare Settings: Minify Update FAILED", "cloudflare");
		} else {
			$pegasaas->utils->log("Conditioning Cloudflare Settings: HTML Minify Successfully DISABLED", "cloudflare");
		}


		// disable rocketloader - rocketloader can interfere with the proper
		//                        operation of javascript and is redundant when
		//                        pegasaas is running
		$request = json_encode(array("value" => "off"));
		$response = self::cloudflare_api("PATCH", "settings/rocket_loader", $request);
		if (is_a($response, "WP_Error")) {
			$pegasaas->utils->log("Conditioning Cloudflare Settings: Rocket Loader Update FAILED", "cloudflare");
		} else {
			$pegasaas->utils->log("Conditioning Cloudflare Settings: Rocket Loader Successfully DISABLED", "cloudflare");
		}


		// turn caching to "basic" - so we can get a predictable response when passing query arguments
		$request = json_encode(array("value" => "basic"));
		$response = self::cloudflare_api("PATCH", "settings/cache_level", $request);
		if (is_a($response, "WP_Error")) {
			$pegasaas->utils->log("Conditioning Cloudflare Settings: Cache Level Update FAILED", "cloudflare");
		} else {
			$pegasaas->utils->log("Conditioning Cloudflare Settings: Cache Level Succesfully Set to BASIC", "cloudflare");
		}


	}

	function cloudflare_credentials_valid($force_check = false) {

		$account_email 	= get_option("pegasaas_cloudflare_account_email", "");
		$api_key 		= get_option("pegasaas_cloudflare_api_key", "");
		$last_auth_query_result	= get_option("pegasaas_cloudflare_last_auth_query_result", false);

		$ten_minutes = 600; // seconds

		$authorization_type = get_option("pegasaas_cloudflare_authorization_type", 0);

		//	print "checking credentials\n";

		if ($authorization_type == 1 && $api_key == "") {
		  	return false;
		} else if ($authorization_type == 0 && ($account_email == "" || $api_key == "")) {
			return false;
		} else {

			$last_cloudflare_auth_query = get_option("pegasaas_cloudflare_last_auth_query_time", 0);
			//print "force check: ".$force_check;

			if (!$force_check && $last_cloudflare_auth_query > time() - $ten_minutes) {
				return $last_auth_query_result;
			}
			//print "authorization type {$authorization_type}\n";

			if ($authorization_type == 1) {
				$headers['Authorization'] = "Bearer {$api_key}";
				$url 	= "https://api.cloudflare.com/client/v4/user/tokens/verify";
			} else {
				$headers['X-Auth-Key'] = $api_key;
				$headers['X-Auth-Email'] = $account_email;
				$url 	= "https://api.cloudflare.com/client/v4/user";
			}
			$headers['Content-Type'] = "application/json";

			$args 	= array("headers" => $headers,
					  "method" => "GET");
			$response 	 = wp_remote_request($url, $args);
			if (is_a($response, "WP_Error")) {
				return false;
			}
			$cf_response = json_decode($response['body'], "true");

			update_option("pegasaas_cloudflare_last_auth_query_result", $cf_response['success']);
			update_option("pegasaas_cloudflare_last_auth_query_time", time());

			if ($cf_response['success']) {
			  return true;
			} else {
			  return false;
			}
		}

	}

	function cloudflare_api($method, $command, $body = "") {
		global $pegasaas;
		$server_http_host = $_SERVER['HTTP_HOST'];
		$domain 		= preg_replace('/^www\./', '', $server_http_host);
		$account_email 	= get_option("pegasaas_cloudflare_account_email", "");
		$api_key 		= get_option("pegasaas_cloudflare_api_key", "");
		$zone_id 		= get_option("pegasaas_cloudflare_zone_id", "");

		$authorization_type = get_option("pegasaas_cloudflare_authorization_type", 0);

		if ($authorization_type == 1) {
			$headers['Authorization'] = "Bearer {$api_key}";
		} else {
			$headers['X-Auth-Key'] = $api_key;
			$headers['X-Auth-Email'] = $account_email;
		}

		$headers['Content-Type'] = "application/json";

		if ($zone_id == "" && $authorization_type == 0) {
			$url = "https://api.cloudflare.com/client/v4/zones?name={$domain}&status=active&page=1&per_page=20&order=status&direction=desc&match=all";
			$args = array("headers" => $headers,
					  "method" => "GET");


			$response = wp_remote_request($url, $args);
			if (is_a($response, "WP_Error")) {
				$pegasaas->utils->log("Cloudflare not purged -- could not get zone id -- response is an error", "cloudflare");

				return false;
			}

			$cf_response = json_decode($response['body'], "true");

			if ($cf_response['success']) {
				$zone_id 	= $cf_response['result'][0]['id'];
			} else {
				$error = $cf_response['errors']['0']['message'];
				$pegasaas->utils->log("Cloudflare not purged -- Could Not Get Zone ID -- {$error}", "cloudflare");
				return false;
			}
		}





		$url 		= "https://api.cloudflare.com/client/v4/zones/{$zone_id}/{$command}";
		//print $url;
		$args 		= array("headers" => $headers,
							"method" => $method);

		if ($body != "") {
			$args['body'] = $body;
		}





		$response 		= wp_remote_request($url, $args);


		return $response;
	}

	function purge_cloudflare($file = "") {
		global $pegasaas;
		if ($file == "") {
			$request ='{"purge_everything":true}';
		} else {
			$files = array();
			$files[] = "https://{$domain}{$file}";
			$files[] = "http://{$domain}{$file}";

			if ($server_http_host != $domain) {
				$files[] = "https://{$server_http_host}{$file}";
				$files[] = "http://{$server_http_host}{$file}";
			}
			$request = json_encode(array("files" => $files));
		}
		$response = self::cloudflare_api("POST", "purge_cache", $request);


		if (is_a($response, "WP_Error")) {
			$pegasaas->utils->log("Cloudflare not purged for {$file} -- response is an error", "cloudflare");
			return false;
		}

		$cf_response 	= json_decode($response['body'], "true");

		if ($cf_response['success']) {
			if ($file == "") {
							$pegasaas->utils->log("Purged ALL Cloudflare cache", "cloudflare");

			} else {
								$pegasaas->utils->log("Purged Cloudflare for {$file}", "cloudflare");

			}
			return true;
		} else {
			$error = $cf_response['errors']['0']['message'];
			$pegasaas->utils->log("Cloudflare not purged for {$file} -- {$error}", "cloudflare");
			return false;
		}


	}

	function get_supported_caching_plugins() {
		return $this->supported_caching_plugins;
	}

	function is_third_party_cache_active(){
		global $pegasaas;

		$supported_cache_plugins = $this->get_supported_caching_plugins();

		foreach($supported_cache_plugins as $plugin_name){
			if($pegasaas->utils->does_plugin_exists_and_active($plugin_name) ){
				print '<!-- pegasaas://accelerator - existing caching plugin "'.$plugin_name.'" detected -->'."\n";
				return true;
			}
		}
		return false;
	}

	function has_yith_wishlist() {
		global $pegasaas;

		if (is_array($_COOKIE)) {
			foreach ($_COOKIE as $key => $value) {
				if (strstr($key, "yith_wcwl_session") !== false) {
					$cookie = json_decode($value, true);
					$sid = addslashes($cookie['session_id']);
					global $wpdb;

					$table_prefix = $wpdb->prefix.$table;

					$query = "SELECT count(*) total_tiems FROM {$wpdb->prefix}yith_wcwl_lists l, {$wpdb->prefix}yith_wcwl li WHERE l.ID=li.wishlist_id AND l.session_id='{$sid}'";

					$results = $wpdb->get_results($query);

					if ($results[0]->total_items > 0) {
						return true;
					}
				}
			}
		}
		return false;
	}


	function has_woocommerce_cookie() {
		if (is_array($_COOKIE)) {
			foreach ($_COOKIE as $key => $value) {
				if (strstr($key, "wp_woocommerce_session") !== false) {
					return true;
				}
			}
		}
		return false;
	}

	function has_edd_items_in_cart() {
		if (is_array($_COOKIE)) {
			foreach ($_COOKIE as $key => $value) {
				if (strstr($key, "edd_items_in_cart") !== false) {
					return true;
				}
			}
		}
		return false;
	}

	function has_wpecommerce_cookie() {
		if (is_array($_COOKIE)) {
			foreach ($_COOKIE as $key => $value) {
				if (strstr($key, "wpsc_customer_cookie") !== false) {
					return true;
				}
			}
		}
		return false;
	}

	public static function del_tree($dir, $file_mask = "") {
		if ($dir == "" || $dir == "/") {
			return;
		}
	//	print "directory is $dir<br>"; 
		$files = @scandir($dir);
		if (is_array($files)) {
   			$files = array_diff($files, array('.','..'));
    		if (is_array($files)) {

				foreach ($files as $file) {
					if (is_dir("$dir/$file")) {
						self::del_tree("$dir/$file", $file_mask);
					} else {
						if ($file_mask != "") {
							$file_pattern = str_replace("*", "(.*)", str_replace(".", '\.', $file_mask));

							// if this does not match, then skip file
							if (!preg_match($file_pattern1, $file) && !preg_match($file_pattern2, $file)) {
								continue;
							}

							//print "match ($file_mask): $file\n";
						}

						$file_path = $dir."/".$file;

						unlink($file_path);
					}
				}
			}
		}
    	return @rmdir($dir);
  	}

	function clear_categories_cache() {
		global $pegasaas;
		$categories 		= $pegasaas->utils->get_all_categories();
		foreach ($categories as $category) {
			$this->clear_html_cache($category->resource_id);
		}
	}

	static function clear_paginated_pages_cache($object_id) {
		global $pegasaas;

		self::del_tree(PEGASAAS_CACHE_FOLDER_PATH."{$object_id}page/");
		//$categories 		= $pegasaas->utils->get_all_categories();
		//foreach ($categories as $category) {
		//	$this->clear_html_cache($category->resource_id);
		//}
	}



	function clear_woocommerce_product_categories_cache() {
		global $pegasaas;


		$categories 		= $pegasaas->utils->get_all_woocommerce_product_categories();
		foreach ($categories as $category) {
			$this->clear_html_cache($category->resource_id);
		}
	}

	function clear_woocommerce_product_tags_cache() {
		global $pegasaas;

		$tags 		= $pegasaas->utils->get_all_woocommerce_product_tags();
		foreach ($tags as $tag) {
			$this->clear_html_cache($tag->resource_id);
		}
	}

	function queue_clear_global_cache($cache_type) {
		global $pegasaas;

		$fields = array();


		if ($cache_type == "html") {
			$fields["task_type"] = "clear_html_cache";

		} else if ($cache_type == "deferred_js") {
			$fields["task_type"] = "clear_deferred_js_cache";

		} else if ($cache_type == "combine_css") {
			$fields["task_type"] = "clear_combine_css_cache";

		} else if ($cache_type == "all") {
			$fields["task_type"] = "clear_page_cache";

		}


		$cache_map = PegasaasAccelerator::$cache_map;
		if ($cache_type == "html" || $cache_type == "all") {
			//$this->clear_local_resource_cache("html");
			$all_files = $this->get_list_of_all_local_files("html");
			foreach ($all_files as $filename) {
				$resource_id = str_replace(PEGASAAS_CACHE_FOLDER_PATH, "", $filename);
				$resource_id = str_replace("index.html", "", $resource_id);
				$resource_id = str_replace("index-temp.html", "", $resource_id);
				$cache_map["$resource_id"] = true;
			}
		}


		foreach ($cache_map as $resource_id => $data) {

		//	//$resource_id = $post->slug;
			$fields["time"] 		= date("Y-m-d H:i:s");
			$fields["resource_id"]  = $resource_id;
			$pegasaas->db->add_record("pegasaas_queued_task", $fields);
		}




	}


	function queue_reoptimize_global_cache($cache_type) {
		global $pegasaas;



		$fields = array();


		if ($cache_type == "html") {
			$fields["task_type"] = "reoptimize_html_cache";

		} else if ($cache_type == "deferred_js") {
			$fields["task_type"] = "reoptimize_deferred_js_cache";

		} else if ($cache_type == "combine_css") {
			$fields["task_type"] = "reoptimize_combine_css_cache";

		} else if ($cache_type == "all") {
			$fields["task_type"] = "reoptimize_page_cache";

			// wipe out all existing requests
			$pegasaas->db->delete("pegasaas_queued_task", array("task_type" => "reoptimize_page_cache"));

		}


		$cache_map = PegasaasAccelerator::$cache_map;
		if ($cache_type == "html" || $cache_type == "all") {
			//$this->clear_local_resource_cache("html");
			$all_files = $this->get_list_of_all_local_files("html");
			foreach ($all_files as $filename) {
				$resource_id = str_replace(PEGASAAS_CACHE_FOLDER_PATH, "", $filename);
				$resource_id = str_replace("index.html", "", $resource_id);
				$resource_id = str_replace("index-temp.html", "", $resource_id);
				$cache_map["$resource_id"] = true;
			}
		}



		foreach ($cache_map as $resource_id => $data) {
			$cache_map["$resource_id"] = array();
			$cache_map["$resource_id"]['fields'] = array();
			$cache_map["$resource_id"]['fields']['resource_id'] = $resource_id;

			$cache_map["$resource_id"]['fields']["time"] 		= date("Y-m-d H:i:s");

			$page_settings = $pegasaas->utils->get_object_meta($resource_id, "accelerator_overrides");
			$cache_map["$resource_id"]["accelerated"] = $page_settings["accelerated"] == true || $page_settings['accelerated'] == 1  ? 1 : 0;
			$cache_map["$resource_id"]["prioritized"] = $page_settings["prioritized"] == true || $page_settings["prioritized"] == 1 ? 1 : 0;

		}


		// order
		uasort($cache_map, array($this, 'sort_cache_map'));


		foreach ($cache_map as $data) {
			$db_fields = $data['fields'];
			$db_fields['task_type'] = $fields['task_type'];

			$pegasaas->db->add_record("pegasaas_queued_task", $db_fields);
		}




	}

	static function sort_cache_map($a, $b) {

		if ($a['accelerated'] == $b['accelerated']) {
			if ($a['prioritized'] == $b['prioritized']) {

				return 0;
			} else {

				return ($a['prioritized'] > $b['prioritized']) ? -1 : 1;
			}
		} else if ($a['accelerated'] > $b['accelerated']) {

			return -1;

		} else {

			return 1;
		}
	}

	function pegasaas_clear_queued_cache_resources() {
		global $pegasaas;

		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {

			$total_remaining = $this->clear_queued_cache_resources();
			print json_encode(array("status" => $total_remaining));

		} else {
			print json_encode(array("status" => -1, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response

	}


	function clear_queued_cache_resources($debug = false) {
		global $pegasaas;



		$records = $pegasaas->db->get_results("pegasaas_queued_task", array(), "time ASC");
		$total_remaining = sizeof($records);
		$max_script_execution_time = 30; // seconds

		if ($this->kinsta_exists()) {
			$max_script_execution_time = 15;
			$last_cache_clear = get_option("pegasaas_last_cache_clear_queue_run", 0);
			// if the time since the last cache clear queue was within the last 15 seconds, then exit
			if (time() - $last_cache_clear < 15) {
				return $total_remaining;
			}
		}




		foreach ($records as $record) {
			if ($debug) {
				print "<pre class='admin'>";
				var_dump($record);
				print "</pre>";
			}
			$task_type = $record->task_type;
			$resource_id = $record->resource_id;

			if ($task_type == "clear_html_cache") {
				$this->clear_html_cache($resource_id);

			} else if ($task_type == "clear_deferred_js_cache") {
				$this->clear_deferred_js_cache($resource_id);

			} else if ($task_type == "clear_combine_css_cache") {
				$this->clear_combine_css_cache($resource_id);

			} else if ($task_type == "clear_page_cache") {
				$this->clear_html_cache($resource_id);
				$this->clear_deferred_js_cache($resource_id);
				$this->clear_combine_css_cache($resource_id);

			} else if ($task_type == "reoptimize_page_cache") {
				$page_settings = $pegasaas->utils->get_object_meta($resource_id, "accelerator_overrides");
				if (isset($page_settings['prioritized']) && $page_settings['prioritized'] == 1) {
					$max_in_queue = 10;
				} else {
					$max_in_queue = 5;
				}

			//	$records = $pegasaas->db->get_results("pegasaas_api_request", array("request_type" => "optimization-request"), "time ASC");
				$total_in_queue = $pegasaas->db->get_num_results("pegasaas_api_request", array("request_type" => "optimization-request"));

				if ($total_in_queue >= $max_in_queue) {
					continue;
				}

				// we do not pass along priority, because the priority is automatically elevated if the page is prioritized when the
				// submission is submitted to the api via PegasaasAccelerator::submit_optimization_Request
				$success = $pegasaas->utils->touch_url($resource_id,
													   array("headers" => array("X-Pegasaas-Priority-Optimization" => 2,
																				"X-Pegasaas-Bypass-Cache" => 1))
													   );
				// if blocking were false (non-blocking) then we could hit the  server 100 times in 5 seconds, overloading the PHP workers
				// when blocking is true then we are only tying up one PHP worker for a maximum of 5 seconds, plus the second request...


				if (!$success) {
					//							$pegasaas->utils->log("PegasaasCache::clear_queued_cache_resources 1297 error ($resource_id)", "caching");
					//print "Not successful  {$resource_id}\n";
					break;
				} else {
				//	print "succesful {$resource_id}\n";
					//		$pegasaas->utils->log("PegasaasCache::clear_queued_cache_resources 1299 success ($resource_id)", "caching");

				}
				//$this->clear_html_cache($resource_id);
				//$this->clear_deferred_js_cache($resource_id);
				//$this->clear_combine_css_cache($resource_id);

			} else {
				$total_remaining--;
				continue;
			}


			$pegasaas->db->delete("pegasaas_queued_task", array("task_id" => $record->task_id));
			$total_remaining--;
			$time_remaining = time() - $pegasaas->start_time;
			if (time() - $pegasaas->start_time > $max_script_execution_time) {

				$pegasaas->utils->log("PegasaasCache::clear_queued_cache_resources 1315 exiting early because {$time_remaining}", "caching");

				//print "<pre class='admin'>exiting early</pre>";
				break;
			} else {
								$pegasaas->utils->log("PegasaasCache::clear_queued_cache_resources 1322 continuing because {$time_remaining}", "caching");

				//print "<pre class='admin'>time is okay: ".(time() - $pegasaas->start_time)."</pre>";
			}
											$pegasaas->utils->log("PegasaasCache::clear_queued_cache_resources 1326 total remaining {$total_remaining}", "caching");

		}


		update_option("pegasaas_last_cache_clear_queue_run", time());
		return $total_remaining;
	}

	function has_cache_reoptimizing_queued() {
		global $pegasaas;


		return $pegasaas->db->has_record("pegasaas_queued_task", array("task_type" => array("value" => "reoptimize_%", "comparison" => " LIKE ")));

	}


	function has_cache_clearing_queued() {
		global $pegasaas;


		return $pegasaas->db->has_record("pegasaas_queued_task", array("task_type" => array("value" => "clear_%", "comparison" => " LIKE ")));

	}
	static function clear_et_cache($object_id) {
		global $pegasaas;
		$pegasaas->cache->clear_cache($object_id);
	}

	function clear_cache($object_id = "") {
	  	global $pegasaas;

		// if we are wiping out all the cache, then we should reset the post_types_pages register, so that it can be rebuilt as needed
		if ($object_id == "") {
			update_option("pegasaas_post_types_pages", array());
			if (@PegasaasAccelerator::$settings['settings']["multi_server"]['status'] > 0 && @sizeof(PegasaasAccelerator::$settings['settings']["multi_server"]['ips']) > 1 && !self::$is_remote_call) {
					$pegasaas->utils->log("PegasaasCache::about to invoke clear_multi_server_page_cache", "caching");
					$this->clear_multi_server_page_cache();
			}
		}

		$pegasaas->utils->log("PegasaasCache::clear_cache ($object_id)", "caching");

		$debug = false;


		if ($object_id == "") {
		//	$this->queue_clear_global_cache("deferred_js");
		//	$this->queue_clear_global_cache("combine_css");

		} else {
			if ($debug) { $pegasaas->utils->console_log("before clear deferred js"); }
			$this->clear_deferred_js_cache($object_id);
			$this->clear_combine_css_cache($object_id);
			if ($debug) { $pegasaas->utils->console_log("after clear deferred js"); }
		}




		if ($object_id == "") {
			$pegasaas->data_storage->unset_object("local_file_stats_html");
			$pegasaas->data_storage->unset_object("local_file_stats_combined.css");
			$pegasaas->data_storage->unset_object("local_file_stats_deferred-js.js");
			$this->queue_clear_global_cache("all");
		} else {
			$pegasaas->utils->log("About to CLEAR HTML CACHE: {$object_id}", "caching");
			$this->clear_html_cache($object_id);
			$pegasaas->utils->log("After CLEAR HTML CACHE", "caching");
			if ($debug) { $pegasaas->utils->console_log("after clear clear_html_cache"); }
		}






	  if ($this->is_themify()) {
		  $this->clear_themify_generated_css($object_id);
	  }

	  if ($object_id != "" && $this->has_beaver_builder()) {
		  $this->clear_beaver_builder_generated_css($object_id);
	  }


		if ($object_id == "") {
			$this->site_cache_cleared = true;
		}


		// only purge cloudflare here if this is a global cache clear,
		// as it has already been cleared via the previously executed clear_html_cache() function
		if ($this->cloudflare_exists() && $object_id == "") {
			//$url = $pegasaas->utils->get_object_id($object_id);
		//	$url = $object_id;
			$this->purge_cloudflare();
		}

	}


	function is_themify() {
		$theme_info = wp_get_theme();
		$theme_name = $theme_info->get("Name");
		$is_themify =  (strstr(strtolower($theme_name), "themify"));
		return $is_themify;
	}

	function has_beaver_builder() {
		global $pegasaas;

		$has_beaver_builder = $pegasaas->utils->does_plugin_exists_and_active("beaver-builder") ||  $pegasaas->utils->does_plugin_exists_and_active("beaver-builder-lite");

		return $has_beaver_builder;
	}

	function clear_themify_generated_css($object_id) {
		global $pegasaas;

		$cdn = PegasaasAccelerator::$settings['settings']['cdn']['status'] == 1;

		$cache_server = PegasaasCache::get_cache_server(false, "css");

		$post_id = url_to_postid($object_id);

		$content_url = str_replace(home_url("", "https"), "", content_url());
		$content_url = str_replace(home_url("", "http"), "",$content_url);

		$css_file 	= $content_url."/uploads/themify-css/themify-builder-{$post_id}-generated.css";

		$this->clear_remote_cache_file($css_file);
	}


	function clear_beaver_builder_generated_css($object_id) {
		global $pegasaas;

		$cache_server = PegasaasCache::get_cache_server(false, "css");

		$post_id = url_to_postid($object_id);

		$content_url = str_replace(home_url("", "https"), "", content_url());
		$content_url = str_replace(home_url("", "http"), "", $content_url);

		$beaver_builder_cache_folder 	= $content_url."/uploads/bb-plugin/cache/";
		$beaver_builder_cache_items = array();

		$the_post					= get_post($post_id);

		if ($the_post->post_type == "fl-builder-template") {
			$item = $beaver_builder_cache_folder.$post_id."-layout.css";
			$beaver_builder_cache_items["$item"] = $item;

			$item = $beaver_builder_cache_folder.$post_id."-layout.js";
			$beaver_builder_cache_items["$item"] = $item;

		} else {

			$url = get_permalink($post_id);
			$content	= $pegasaas->utils->fetch_page_html($url);
			$script_matches = array();
			$script_tag_pattern = "/<script(.*?)>(.*?)<\/script>/si";
			$src_pattern 		= "/\ssrc=['\"](.*?)['\"]/si";
			preg_match_all($script_tag_pattern, $content, $script_matches);

			// iterate through scripts to see if there are any that match the beaver builder cache folder
			foreach ($script_matches[0] as $index => $find) {
				$match_src = array();

				preg_match($src_pattern, $script_matches[1]["$index"], $match_src);
				$src = $match_src[1];

				if (strstr($src, $beaver_builder_cache_folder)) {
					$cleaned_url = $pegasaas->utils->strip_query_string($src);
					$beaver_builder_cache_items["{$cleaned_url}"] = $cleaned_url;
				}
			}

			$stylesheet_matches = array();
			$stylesheet_tag_pattern = "/<link(.*?)>/si";
			$href_pattern 			= "/\shref=['\"](.*?)['\"]/si";
			preg_match_all($stylesheet_tag_pattern, $content, $stylesheet_matches);

			// iterate through scripts to see if there are any that match the beaver builder cache folder
			foreach ($stylesheet_matches[0] as $index => $find) {
				$match_href = array();

				preg_match($href_pattern, $stylesheet_matches[1]["$index"], $match_href);
				$href = $match_href[1];

				if (strstr($href, $beaver_builder_cache_folder)) {
					$cleaned_url = $pegasaas->utils->strip_query_string($href);
					$beaver_builder_cache_items["{$cleaned_url}"] = $cleaned_url;
				}
			}
		}

		foreach ($beaver_builder_cache_items as $url) {
			$url = str_replace(PegasaasCache::get_cache_server(false, "css"), $pegasaas->get_home_url()."/", $url);
			$url = str_replace(home_url("", "https"), "", $url);
			$css_file = str_replace(home_url("", "http"), "", $url);
			$this->clear_remote_cache_file($css_file);

		}
		// we should really clear the specific layout, but beaver builder has partial layouts, layouts in layouts, that
		// are difficult to detect
		// we could get the post content, and search for the ####-layout-partial.css and ####-layout.css files and clear them each

	}

	static function get_cache_server($webp_alternate = false, $file_type = "") {
		global $pegaaas;
		$cdn = PegasaasAccelerator::$settings['settings']['cdn']['status'] == 1;

		if ($cdn == true) {
			if ($file_type != "") {
				$cache_server = "https://cdn1.".PEGASAAS_ACCELERATOR_CACHE_SERVER."/".PegasaasAccelerator::$settings['installation_id']."/{$file_type}/";

			} else if ($webp_alternate) {
				$cache_server = "https://img-cdn.".PEGASAAS_ACCELERATOR_CACHE_SERVER."/".PegasaasAccelerator::$settings['installation_id']."/img/";

			} else {
				$cache_server = "https://".PegasaasAccelerator::$settings['installation_id'].".cdn.".PEGASAAS_ACCELERATOR_CACHE_SERVER."/";

			}
		} else {
			$cache_server = PEGASAAS_CACHE_FOLDER_URL.'/';
		}

		return $cache_server;
	}



	function clear_deferred_js_cache($object_id = "") {
		global $pegasaas;
		$debug = false;

		if ($object_id == "") {
			$deferred_js_records = get_option('pegasaas_deferred_js', array());

			$all = PegasaasUtils::get_all_pages_and_posts();

			foreach ($all as $post) {
				//$path = PEGASAAS_CACHE_FOLDER_PATH."{$post->slug}djs-1.js";
				//$this->clear_cache_file($path);

				$path =  PEGASAAS_CACHE_FOLDER_PATH."{$post->resource_id}deferred-*.js";
				//print $path."<br>";
				$this->clear_cache_file($path);
				$pegasaas->utils->delete_object_meta($post->resource_id, "deferred_js");
			}

			foreach ($deferred_js_records as $resource_id => $active) {
				//$path =  PEGASAAS_CACHE_FOLDER_PATH."{$resource_id}djs-1.js";
				//$this->clear_cache_file($path);

				$path =  PEGASAAS_CACHE_FOLDER_PATH."{$resource_id}deferred-*.js";
				$this->clear_cache_file($path);
				$deferred_js = PegasaasUtils::get_object_meta($resource_id, "deferred_js");
				if(isset($deferred_js['deferred_js_post_id']) ) {
					$pegasaas->utils->log("Deferred JS has a postID of {$deferred_js['deferred_js_post_id']}", "caching");
				}else{
					$pegasaas->utils->log("Deferred JS has a postID of NOT DEFINED", "caching");
			  	}

				if (isset($deferred_js['deferred_js_post_id']) && $deferred_js['deferred_js_post_id'] != "") {
					$pegasaas->utils->log("Deleting Deferred JS Post ID {$deferred_js['deferred_js_post_id']}");
					wp_delete_post($deferred_js['deferred_js_post_id'], $force_delete = true);
				}

				$pegasaas->utils->delete_object_meta($resource_id, "deferred_js");


			}

			delete_option('pegasaas_deferred_js', array());
		} else {
			if ($debug) { $pegasaas->utils->console_log("Deleting Deferred JS for $object_id"); }

			$path =  PEGASAAS_CACHE_FOLDER_PATH."{$object_id}deferred-*.js";
			$this->clear_cache_file($path);




			// disabled April 16, 2020 as we no longer need to clear remote cache
			//if ($debug) { $pegasaas->utils->console_log("Before Clear Remote Cache File"); }
			//$this->clear_remote_cache_file(PEGASAAS_CACHE_FOLDER."{$object_id}deferred-*.js");
			//if ($debug) { $pegasaas->utils->console_log("After Clear Remote Cache File"); }

			$deferred_js = PegasaasUtils::get_object_meta($object_id, "deferred_js");
			$pegasaas->utils->log("Deferred JS has a postID of {$deferred_js['deferred_js_post_id']}", "caching");


					if ($deferred_js['deferred_js_post_id'] != "") {
						$pegasaas->utils->log("Deleting Deferred JS Post ID {$deferred_js['deferred_js_post_id']}");
						wp_delete_post($deferred_js['deferred_js_post_id'], $force_delete = true);
					}

			$pegasaas->utils->delete_object_meta($object_id, "deferred_js");
			$deferred_js_records = get_option('pegasaas_deferred_js', array());

			unset($deferred_js_records["$object_id"]);
			update_option('pegasaas_deferred_js', $deferred_js_records);

		}
		// $pegasaas->utils->optimize_database(false); // removed in 2.2.5

	}

	function clear_combine_css_cache($object_id = "") {
		global $pegasaas;
		$debug = false;

		if ($object_id == "") {
			$all = PegasaasUtils::get_all_pages_and_posts();

			foreach ($all as $post) {
				$path =  PEGASAAS_CACHE_FOLDER_PATH."{$post->resource_id}combined-*.css";
				$this->clear_cache_file($path);
			}
		} else {
			$path =  PEGASAAS_CACHE_FOLDER_PATH."{$object_id}combined-*.css";
			$this->clear_cache_file($path);


			// disabled April 16, 2020 as we no longer need to clear remote cache
			//if ($debug) { $pegasaas->utils->console_log("Before Clear Remote Cache File"); }
			//$this->clear_remote_cache_file(PEGASAAS_CACHE_FOLDER."{$object_id}combined*.css");
			//if ($debug) { $pegasaas->utils->console_log("After Clear Remote Cache File"); }





		}


	}

	function get_list_of_all_files_in_folder($file_type, $folder_path) {
		global $pegasaas; 
		global $test_debug_detailed;
		global $total_scandir_time;
		global $total_scandirs;

		$list_of_files = array();
		$start_scandir_time = $pegasaas->execution_time();
	    
		if ($test_debug_detailed) {
	    	
	    	PegasaasUtils::log_benchmark("Before Scandir: {$folder_path}", "debug-li", 1000);
	    }

		$folder_files = @scandir($folder_path);
		$finish_scandir_time = $pegasaas->execution_time();
		$scandir_time = $finish_scandir_time - $start_scandir_time;
		$total_scandir_time += $scandir_time;
		$total_scandirs++;
	
		
		if ($test_debug_detailed) {
		

	    	PegasaasUtils::log_benchmark("After Scandir: {$folder_path}", "debug-li", 1000);

	    	PegasaasUtils::log_benchmark("Scandir time: {$scandir_time}<br><br>", "debug-li", 1000);

	    }

		if (is_array($folder_files)) {
			if ($test_debug_detailed) {
			//	PegasaasUtils::log_benchmark("Before Array Diff", "debug-li", 1000);
			}
			$files = array_diff($folder_files, array('.','..'));
			if ($test_debug_detailed) {
			//	PegasaasUtils::log_benchmark("After Array Diff", "debug-li", 1000);
			}

			foreach ($files as $file) {
				if (is_dir("{$folder_path}/{$file}")) {
					$list_of_files = array_merge($list_of_files, $this->get_list_of_all_files_in_folder($file_type, $folder_path."/".$file));

				} else {


					$file_extension = PegasaasUtils::get_file_extension($file);
					if ($test_debug_detailed) {
					//	PegasaasUtils::log_benchmark("After get_file_extension", "debug-li", 1000);
					}

					if ($file_extension == "css" && ($file_type == "css" || $file_type == "")) {
						if ($file != "combined.css" && $file != "critical.css") {
							$list_of_files[] = $folder_path."/".$file;
						}


					} else if ($file_extension == "js" && ($file_type == "js" || $file_type == "")) {
						if ($file != "deferred-js.js") {
							$list_of_files[] = $folder_path."/".$file;
						}
					} else if ($file_extension == "jpg" && ($file_type == "jpg" || $file_type == "")) {

							$list_of_files[] = $folder_path."/".$file;

					} else if ($file_extension == "jpeg" && ($file_type == "png" || $file_type == "")) {

							$list_of_files[] = $folder_path."/".$file;

					} else if ($file_extension == "html" && ($file_type == "html")) {

							$list_of_files[] = $folder_path."/".$file;

					} else if ($file == "critical.css" && ($file_type == "critical.css")) {

							$list_of_files[] = $folder_path."/".$file;

					} else if ($file == "image-data.json" && ($file_type == "image-data.json")) {

							$list_of_files[] = $folder_path."/".$file;

					} else if ($file == "combined.css" && ($file_type == "combined.css")) {

							$list_of_files[] = $folder_path."/".$file;

					} else if ($file == "deferred-js.js" && ($file_type == "deferred-js.js")) {

							$list_of_files[] = $folder_path."/".$file;

					} else if ($file_extension == "png" && ($file_type == "png" || $file_type == "")) {

							$list_of_files[] = $folder_path."/".$file;

					}
					if ($test_debug_detailed) {
					//	PegasaasUtils::log_benchmark("Afer add to array", "debug-li", 1000);
					}
				}

			}

		}
		return $list_of_files;

	}

	static function get_scandir_time() {
		global $pegasaas;
		global $test_debug_detailed;
		$benchmark_before_test = $pegasaas->execution_time();
		@scandir(PEGASAAS_CACHE_FOLDER_PATH);
		$benchmark_after_test = $pegasaas->execution_time();
		$benchmark_total_time = $benchmark_after_test - $benchmark_before_test;



		return $benchmark_total_time;
	}

	static function get_cache_folder_size() {
		// return the size, in megabytes, of the cache folder
		$io = popen ( '/usr/bin/du -sm ' .PEGASAAS_CACHE_FOLDER_PATH, 'r' );
		$size = fgets ( $io, 4096);

		$size = substr ( $size, 0, strpos ( $size, "\t" ) );
		pclose ( $io );

		return $size;
	}

	static function get_estimated_cache_scan_time() {
		$cache_map = PegasaasAccelerator::$cache_map;

		//$benchmark_before_size_test = $pegasaas->execution_time();
	//	$cache_folder_size = PegasaasCache::get_cache_folder_size();


		//if ($cache_folder_size == "") {
			// factor is determined by the time it takes to scan an entire directory, on average, and all the files within it.
			// for example, if a scandir of a single folder takes 1 second, on average, it could take 15 seconds to process the entire folder
			// -- of course, file operations are measured in microseconds, but if there are thousands of files in the cache map, then this could 
			// take a significant time the file i/o for this file system is slow.
			$factor = 15;
			$scandir_time = PegasaasCache::get_scandir_time();
			$estimated_total_time = $scandir_time * sizeof($cache_map) * $factor;

	//	} else {

		//	$benchmark_after_size_test = $pegasaas->execution_time();
		//	$total_test_time = $benchmark_after_size_test - $benchmark_before_size_test;
		//	$time_per_mb = $total_test_time / $cache_folder_size;


		//}
		return $estimated_total_time;
	}

	function get_list_of_all_local_files($file_type, $force_full_scan = false) {
		global $pegasaas;
		global $test_debug_detailed;
		PegasaasUtils::log("get_list_of_all_local_files('$file_type', '{$force_full_scan}') start", "script_execution_benchmarks");

		$estimated_total_scan_time = PegasaasCache::get_estimated_cache_scan_time();

		//$benchmark_before_size_test = $pegasaas->execution_time();
		//$cache_folder_size = PegasaasCache::get_cache_folder_size();
		//if ($cache_folder_size == "") {
		//	$benchmark_before_size_test = $pegasaas->execution_time();

		//} else {

		//	$benchmark_after_size_test = $pegasaas->execution_time();
		//	$total_test_time = $benchmark_after_size_test - $benchmark_before_size_test;
		//	$time_per_mb = $total_test_time / $cache_folder_size;


		//}


		if ($test_debug_detailed) {
			PegasaasUtils::log_benchmark("Cache Map Size: ".sizeof(PegasaasAccelerator::$cache_map), "debug-li", 1000);
			PegasaasUtils::log_benchmark("Estimated Scan Time: {$estimated_total_scan_time}", "debug-li", 1000);

	//		PegasaasUtils::log_benchmark("Cache Folder Size: {$cache_folder_size}", "debug-li", 1000);
		//	PegasaasUtils::log_benchmark("Time Per MB: {$time_per_mb}", "debug-li", 1000);

		}
		$files = array();
		if ($estimated_total_scan_time > 10 && !$force_full_scan) {
		//	PegasaasUtils::log("get_list_of_all_local_files('$file_type', '{$force_full_scan}') start", "script_execution_benchmarks");

			if ($test_debug_detailed) {
				PegasaasUtils::log_benchmark("Cache Map Method Start", "debug-li", 1000);
		
			}
			
			foreach (PegasaasAccelerator::$cache_map as $resource_id => $resource) {
				if ($resource['is_temp']) {
					if ($file_type == "html") {
						$files[] = PEGASAAS_CACHE_FOLDER_PATH.$resource_id."index-temp.html";
					}
				} else {
					if ($file_type == "html") {
						$files[] = PEGASAAS_CACHE_FOLDER_PATH.$resource_id."index.html";
					}
				}
	
			}
			if ($test_debug_detailed) {
				PegasaasUtils::log_benchmark("Cache Map Method End", "debug-li", 1000);
		
			}
		} else {
		
			if ($test_debug_detailed) {
				PegasaasUtils::log_benchmark("Regular get-list-of-all-files-in-folder Method START", "debug-li", 1000);
		
			}		
			$files = array_merge($files, $this->get_list_of_all_files_in_folder($file_type, PEGASAAS_CACHE_FOLDER_PATH.""));

			if ($test_debug_detailed) {
				PegasaasUtils::log_benchmark("Regular get-list-of-all-files-in-folder Method START", "debug-li", 1000);
		
			}		
		}
		PegasaasUtils::log("get_list_of_all_local_files('$file_type', '{$force_full_scan}') end (".(sizeof($files))." files)", "script_execution_benchmarks");

		return $files;

	}


	function clear_local_resource_cache($file_type = "") {
		global $pegasaas;
		$force_full_scan = true;

		$all_files = $this->get_list_of_all_local_files($file_type, $force_full_scan);
		//var_dump($all_files);
		if (sizeof($all_files) > 0) {
			foreach ($all_files as $file_name) {

				$this->clear_cache_file($file_name);
			}
		}
	}

	function clear_elementor_page_local_css_cache($resource_id = "") {
		global $pegasaas;

		if ($resource_id == "") {
			$this->rm_dir_tree("wp-content/uploads/elementor/css/");

		} else {
			$post_id = url_to_postid($resource_id);
			$this->clear_cache_file(PEGASAAS_CACHE_FOLDER_PATH."/wp-content/uploads/elementor/css/post-{$post_id}.css");
			$this->clear_cache_file(PEGASAAS_CACHE_FOLDER_PATH."/wp-content/uploads/elementor/css/global.css");
			if ($this->cloudflare_exists()) {

				$this->purge_cloudflare("/wp-content/uploads/elementor/css/post-{$post_id}.css*");
				$this->purge_cloudflare("/wp-content/uploads/elementor/css/global.css*");
			}

		}
	}

	function clear_posts_elementor_page_local_css_cache($buffer) {
		$pattern = "/<link(.*?)>/si";
    	preg_match_all($pattern, $buffer, $matches);

		$href_pattern 	= "/href=['\"](.*?)['\"]/si";
		$filename_pattern = '/post-([\d]*?)\.css/si';




		foreach ($matches[0] as $css_link) {
			$match_href 	= array();
			$match_filename = array();

			preg_match($href_pattern, $css_link, $match_href);


			$href 	= $match_href[1];
		    if (strstr($href, "/pegasaas-cache/wp-content/uploads/elementor/css/post-")) {
				preg_match($filename_pattern, $href, $match_filename);
				$file_id = $match_filename[1];

				$this->clear_cache_file(PEGASAAS_CACHE_FOLDER_PATH."/wp-content/uploads/elementor/css/post-{$file_id}.css");

			}
		}

		$this->clear_cache_file(PEGASAAS_CACHE_FOLDER_PATH."/wp-content/uploads/elementor/css/global.css");


	}

	function clear_html_cache($object_id = "") {
		global $pegasaas;

		if ($pegasaas->utils->does_plugin_exists_and_active("elementor")) {
			$this->clear_elementor_page_local_css_cache($object_id);
		}

		if ($object_id === "") {

			update_option("pegasaas_post_types_pages", array());


			$pegasaas->utils->log("Clearing ALL HTML cache", "caching");
			$pegasaas_cache_map = PegasaasAccelerator::$cache_map;
			$objects 			= PegasaasUtils::get_all_pages_and_posts();

			foreach ($objects as $post) {
				$resource_id = $post->resource_id;
				$pegasaas->utils->log("About to try to clear for {$resource_id}", "caching");

				if (PegasaasAccelerator::$settings['settings']['varnish']['status'] == 1) {
					$this->purge_varnish($pegasaas->get_home_url().$resource_id);
				}
				$this->clear_cache_file(PEGASAAS_CACHE_FOLDER_PATH."{$resource_id}index*.html");

				if (isset(PegasaasAccelerator::$settings['settings']['accelerated_mobile_pages']) && PegasaasAccelerator::$settings['settings']['accelerated_mobile_pages']['status'] > 0) {
					$this->clear_cache_file(PEGASAAS_CACHE_FOLDER_PATH."{$resource_id}amp/index*.html");
				}

				$pegasaas->utils->delete_object_meta($resource_id, "cached_html");
			}

			// also go through the cache map and clear any pages from the map
			foreach ($pegasaas_cache_map as $resource_id => $active) {

				$pegasaas->utils->delete_object_meta($resource_id, "cached_html");
			}

			delete_option('pegasaas_cache_map', array());

			$this->clear_third_party_cache();
			if (PegasaasAccelerator::$settings['settings']['varnish_compatibility']['status'] == 1) {

			}
			if ($this->cloudflare_exists()) {
				$success = $this->purge_cloudflare();
			}
		} else {
			$pegasaas->utils->log("Clearing HTML cache for [{$object_id}] !", "caching");
			$resource_id = $object_id;

			//$cache_data_map = get_option("pegasaas_cache_map", array());
			//$cache_data_map = PegasaasAccelerator::$cache_map;
			//unset($cache_data_map["{$resource_id}"]);
			unset(PegasaasAccelerator::$cache_map["{$resource_id}"]);


			//update_option("pegasaas_cache_map", $cache_data_map);

			//$cached_htmls = PegasaasUtils::get_object_meta($resource_id, "cached_html");
			$pegasaas->utils->log("cabout to clear_cache_file {$resource_id}index*.html", "caching");
			$this->clear_cache_file(PEGASAAS_CACHE_FOLDER_PATH."{$resource_id}index*.html");

			if (isset(PegasaasAccelerator::$settings['settings']['accelerated_mobile_pages']) && PegasaasAccelerator::$settings['settings']['accelerated_mobile_pages']['status'] > 0) {
				$this->clear_cache_file(PEGASAAS_CACHE_FOLDER_PATH."{$resource_id}amp/index*.html");
			}
			$pegasaas->utils->log("clear_html_cache for $resource_id", "caching");

			// this will also delete it from the PegasaasAccelerator::$cache_map for the next invocation
			$pegasaas->utils->delete_object_meta($resource_id, "cached_html");

			if (@PegasaasAccelerator::$settings['settings']["multi_server"]['status'] > 0 && @sizeof(PegasaasAccelerator::$settings['settings']["multi_server"]['ips']) > 1 && !self::$is_remote_call) {
				$pegasaas->utils->log("PegasaasCache::about to invoke clear_multi_server_page_cache", "caching");
				$this->clear_multi_server_page_cache($resource_id);

			}

			$post_id = url_to_postid($object_id);

			if ($post_id == '0') {
				$this->clear_third_party_cache($post_id);
				//$this->clear_third_party_cache(get_option("page_on_front"));
			} else {
				$this->clear_third_party_cache($post_id);
			}

			if ($object_id != "" && $this->cloudflare_exists()) {
				$url = $object_id;
				$this->purge_cloudflare($url);
			}
		}


	}




	function clear_pantheon_cache($post_id = "") {
		if ($post_id === "") {
			Pantheon_Cache()->flush_site();
		} else {
			Pantheon_Cache()->clean_post_cache( $post_id );
		}
	}


	function clear_kinsta_cache($post_id = "") {
		global $KinstaCache;
		global $kinsta_cache;
		global $pegasaas;
		if ($post_id === "") {
			$pegasaas->utils->log("Third Party Cache (Kinsta) purge_complete_caches()", "caching");

			@$kinsta_cache->kinsta_cache_purge->purge_complete_caches();
		} else {
			$pegasaas->utils->log("Third Party Cache (Kinsta) initiate_puge($post_id)", "caching");

			@$kinsta_cache->kinsta_cache_purge->initiate_purge($post_id, "");
		}
	}

	function clear_pagely_cache($post_id = "") {
		global $pegasaas;
		if ($post_id === "") {
			$pegasaas->utils->log("Third Party Cache (Pagely) purgeAll()", "caching");
			if ( class_exists( 'PagelyCachePurge' ) ) {
        		$purger = new PagelyCachePurge();
				$purger->purgeAll();
			}

		} else {
			$pegasaas->utils->log("Third Party Cache (Pagely) initiate_puge($post_id)", "caching");

			if ( class_exists( 'PagelyCachePurge' ) ) {
				$resource_id = $pegasaas->utils->get_object_id($post_id);
        		$purger 	 = new PagelyCachePurge();
				$purger->purgePath($resource_id);
			}
		}
	}

	function clear_cache_file($filename) {
		global $pegasaas;

		if (@file_exists($filename)) {
			if (@unlink($filename)) {
			
				$pegasaas->utils->log("CLEAR CACHE FILE: {$filename} SUCCESS", "caching");
			} else {
			
				$pegasaas->utils->log("CLEAR CACHE FILE: {$filename} FAIL", "caching");
			}
		} else if (strstr($filename, "*")) {
			array_map('unlink', glob($filename));
			$pegasaas->utils->log("CLEAR CACHE FILE: {$filename} trying to clear bulk ", "caching");

		} else {
		
			$pegasaas->utils->log("CLEAR CACHE FILE: {$filename} does not exist", "caching");
		}

		 $pieces = explode("/", rtrim($filename, "/"));
		 array_pop($pieces);
		 $sub_path = implode("/", $pieces);
	
		if (is_dir($sub_path) && self::is_dir_empty($sub_path)) {
		  @rmdir($sub_path);
		}
	}

	static function is_dir_empty($dir) {
  		if (!is_readable($dir)) return NULL;
  		return (count(scandir($dir)) == 2);
	}


	function clear_multi_server_page_cache($resource_id = "") {
		global $pegasaas;

		$post_fields = array("command" 		=> "clear-multi-server-page-cache",
							 "resource_id" 	=> $resource_id,
							 "ignore_ip" 	=> $_SERVER['SERVER_ADDR']);

		$pegasaas->api->post($post_fields, array("blocking" => false));
	}

	function clear_remote_cache_file($file = "") {
		global $pegasaas;

		$pegasaas->utils->log("Attempting to clear remote cache for {$file}", "caching");

		if (!is_array($file) && $file != "") {
			$post = array("command" => "clear-cache-file", "file" => $file);
			$pegasaas->api->post($post, array("blocking" => false));
		}
	}

	function clear_remote_cache_files($files = array(), $clear_cdn_only = false) {
		global $pegasaas;

		$pegasaas->utils->log("Attempting to clear remote cache of multiple files {$file}", "caching");

		if (is_array($files) && sizeof($files) > 0) {
			$post = array("command" => "clear-cache-files", "files" => $files, "clear_cdn_only" => $clear_cdn_only);
			$output = $pegasaas->api->post($post, array("blocking" => false));

		}
	}


	function handle_save_post($post_id) {
		global $pegasaas;
			$pegasaas->utils->log("Begin Handle Save Post ($post_id), About To Clear Cache for $object_id", "caching");


		$object_id = $pegasaas->utils->get_object_id($post_id);


		$this->clear_html_cache($object_id);
		$pegasaas->utils->log("End of Handle Save Post ($post_id), About To Clear Cache for $object_id", "caching");
	}

	function handle_edit_post($post_id) {
		global $pegasaas;

		$post_type = get_post_type($post_id);

		if ($post_type == "nav_menu_item") {


			//var_dump($_POST);
			//exit;
			if ($_POST['_pegasaas_clear_cache'] == "1") {
				if (!$this->site_cache_cleared) {
					$this->clear_cache();
				}
				$pegasaas->utils->log("Menu Saved [{$post_type}], Clearing Site Cache", "caching");


			} else {
				$pegasaas->utils->log("Menu Saved [{$post_type}], Not Clearing Site Cache", "caching");

			}

		} else {
			$object_id = $pegasaas->utils->get_object_id($post_id);

			$pegasaas->utils->log("Edit Post ($post_id) [{$post_type}], About To Clear Cache for $object_id", "caching");

			$this->clear_cache($object_id);
			if (PegasaasAccelerator::$settings['settings']['wordlift']['status'] == 1) {
				if ($pegasaas->do_wordlift()) {
					$this->clear_wordlift_schema($post_id);
				}
			}
		}

	}

	function get_basic_image_cache_stats() {
		global $pegasaas;
		$stats = array();
		

		$image_cache = get_option("pegasaas_image_cache", array());
		$stats['unoptimized_images'] 	= 0;
		$stats['optimized_images'] 		= 0;
		$stats['savings'] 				= 0;
		$stats['images_over_quota'] 	= 0;
		$stats['total_filesize'] 		= 0;
		$stats['cache_filesize'] 		= 0;
		$stats['optimized_filesize'] 	= 0;
		$stats['optimizations_so_far_this_month'] = 0;
		$stats['percentage_of_use'] 	= 0;

		foreach ($image_cache as $filename => $data) {
				if ($data['optimized'] === true) {
					$stats['optimized_images']++;
					$stats['savings'] += $data['original_size'] - $data['optimized_size'];
					$stats['total_filesize'] += $data['original_size'];
					$stats['optimized_filesize'] += $data['optimized_size'];
					$stats['cache_filesize'] += $data['optimized_size'];
				} else {
					$stats['unoptimized_images']++;
					$stats['cache_filesize'] += $data['original_size'];
					if ($data['original_filesize'] > PegasaasAccelerator::$settings['settings']['basic_image_optimization']['max_image_size']) {
						$stats['images_over_quota']++;
					}
				}
			}

		$this_month = date("Y-m");
		$optimizations = get_option('pegasaas_image_optimizations', array());
		if (is_array($optimizations['so_far_this_month'])) {
			$stats['optimizations_so_far_this_month'] = count($optimizations["{$this_month}"]);
		} else {
			$stats['optimizations_so_far_this_month'] = 0;
		}

		if (PegasaasAccelerator::$settings['settings']['basic_image_optimization']['images_per_month'] == 0) {
			PegasaasAccelerator::$settings['settings']['basic_image_optimization']['images_per_month'] = 100;
		}

		$stats['percentage_of_use'] = 100 * ($stats['optimizations_so_far_this_month'] / PegasaasAccelerator::$settings['settings']['basic_image_optimization']['images_per_month']);

		return $stats;

	}

	function pegasaas_remote_clear_image_stats() {
		global $pegasaas;

		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$this_month = date("Y-m");
			$pegasaas->utils->semaphore("pegasaas_image_optimization");
			$optimizations = get_option('pegasaas_image_optimizations', array());

			unset($optimizations["$this_month"]);
			update_option('pegasaas_image_optimizations', $optimizations);
			$pegasaas->utils->release_semaphore("pegasaas_image_optimization");

			print json_encode(array("status" => 1));

		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response

	}

	function get_local_cache_stats($extension, $refresh = false) {
		global $pegasaas;

		global $test_debug;
		PegasaasUtils::log("get_local_cache_stats('$extension', '{$refresh}') start", "script_execution_benchmarks");

		if ($pegasaas->data_storage->is_valid("local_file_stats_{$extension}") && !$refresh) {

			return $pegasaas->data_storage->get("local_file_stats_{$extension}");
		}

		if ($test_debug) {
			PegasaasUtils::log_benchmark("Getting All Local Files for: $extension", "debug-li", 1);
		}

		$files = $this->get_list_of_all_local_files($extension);


		$stats = array();
		$stats['count'] = count($files);
		$stats['size']  = 0;

		foreach ($files as $filename) {


			$stats['size'] += @filesize($filename);
		}
		if ($test_debug) {
		  print "<Pre>";
		  var_dump($stats);
		  print "</pre>";
		}


		$pegasaas->data_storage->set("local_file_stats_$extension", $stats, 600);

		return $stats;
	}

	function clear_wordlift_schema($post_id) {
		global $pegasaas;
		$object_id 	= $pegasaas->utils->get_object_id($post_id);



		if (get_option("show_on_front") == "page" && $post_id == get_option("page_on_front")) {

				$filename = "{$post_id}-homepage.schema";

		} else if ($post_id == 0 || get_option("page_for_posts") == $post_id) {
				$filename = "homepage.schema";

		} else {
			$filename = "{$post_id}.schema";
		}

		$path 		=  PEGASAAS_CACHE_FOLDER_PATH."/wl-api/{$filename}";

		$this->clear_cache_file($path);
		/*
		if (!function_exists("get_home_path")) {
			//$root_path = str_replace("/wp-content", "/wp-admin", WP_CONTENT_DIR);
			$root_path = ABSPATH."wp-admin";

			require_once($root_path."/includes/file.php");
		}
		*/

		//$just_content_dir_folder_path = str_replace(get_home_path(), "/", WP_CONTENT_DIR);

		$this->clear_remote_cache_file(PEGASAAS_CACHE_FOLDER."/wl-api/{$filename}");

	}

	function save_page_cache($buffer, $object_id = "", $url = "", $temp_cache = false, $css_validation_issue = false, $extended_coverage = false) {
		global $pegasaas;
		$pegasaas->utils->log("Save Cache Start", "caching");

		$wl_api = method_exists($pegasaas, "is_wordlift_api") && $pegasaas->is_wordlift_api();

		if ($object_id == "") {
			$object_id 			= PegasaasUtils::get_object_id();
		}



		if (true) {
			$request_args = $pegasaas->utils->get_uri_args($url);
			$object_id_args = $pegasaas->utils->get_uri_args($object_id);
			$args = trim(str_replace($object_id_args, "", $request_args), "&");
			if ($args == "") {
				$args = "NULL";
			}

			if ($args != "NULL" && !$wl_api) {


				$pegasaas->utils->log("Found ARGS ($args) so Page ($object_id) not cached.", "caching");
				return $buffer;
			}

			$file_based_caching = PegasaasAccelerator::$settings['settings']['page_caching']['status'] == 2 ||
								  PegasaasAccelerator::$settings['settings']['page_caching']['status'] == 1 || $pegasaas->is_standard();
			if ($wl_api) {
				$pegasaas->utils->log("Save Cache WordLift API", "caching");


				$wl_homepage = $_GET['homepage'] == "true";
				$wl_id 		 = $_GET['id'];

				if ($wl_id != "" && $wl_homepage) {
					$filename = "{$wl_id}-homepage.schema";
				} else if ($wl_id != "") {
					$filename = "{$wl_id}.schema";
				} else if ($homepage) {
					$filename = "homepage.schema";
				} else {
				//	$pegasaas->utils->release_semaphore("pegasaas_cache_map");
					return $buffer;
				}

			// $args are query string arguments, such as ?name=value
			} else	if ($args == "NULL") {
				if ($pegasaas->utils->does_plugin_exists_and_active("geotargetingwp")) {
					$geo_modifier = $this->get_cache_geo_modifier();
				} else {
					$geo_modifier = "";
				}

				if ($geo_modifier != "") {
					$args = $geo_modifier;
				}


				// temp caching is when the page may not be optimized, however it makes sense to
				// store the page locally to speed up delivery of pages in the event that optimizations
				// may not be immediately performed
				if ($temp_cache) {
						$filename = "index-temp{$geo_modifier}.html";
						$temp_filename = ""; // we may need to clear the index.html, although by doing so we may create an inescapble loop
				} else {
						$temp_filename = "index-temp{$geo_modifier}.html";
						$filename = "index{$geo_modifier}.html";
				}
				$original_filename = "index-original{$geo_modifier}.html";


			} else {
					$filename = $pegasaas->instance.".html";
					$original_filename = $pegasaas->instance."-original.html";

			}
				if (!$wl_api) {

					$comment = "Cache Copy Saved ";


					$comment .= "@ ".date("Y-m-d H:i:s")." ";

					$comment .= "[{$object_id}] [".PegasaasUtils::get_permalink()."]";
					if (!PegasaasUtils::should_strip_footer_comments()) {
					$buffer .= PegasaasUtils::html_comment($comment);
					}
				}


		//	$pegasaas->utils->release_semaphore("pegasaas_cache_map");
			if ($file_based_caching) {

				$cache_data['cache_type'] 	= "file";
				if ($wl_api) {
					$file_folder_name = "/wl-api";
				} else if ($object_id == "/") {
					$file_folder_name = "/";
				} else {
					$file_folder_name = "/".trim($pegasaas->utils->strip_query_string($object_id), "/");
				}



				//if (PegasaasUtils::wpml_multi_domains_active()) {
				//	$file_folder_name = "/".$_SERVER['HTTP_HOST'].$file_folder_name;
				//}
				//

				$cache_folder_path = PEGASAAS_CACHE_FOLDER_PATH;
				$cache_data['relative-to-cache-folder-path'] = true;

				$cache_data['file'] 		= "{$file_folder_name}/{$filename}";
				$cache_data['original'] 	= "{$file_folder_name}/{$original_filename}";
				if ($temp_filename) {
					$cache_data['temp_file']  =	"{$file_folder_name}/{$temp_filename}";
				}

				$cache_data['file'] = str_replace("//", "/", $cache_data['file']);
				$cache_data['temp_file'] = str_replace("//", "/", $cache_data['temp_file']);
				$cache_data['original'] =  str_replace("//", "/", $cache_data['original']);


				if ($temp_cache) {
					$cache_data['is_temp'] = true;
				}
				if ($extended_coverage) {
					$cache_data['is_extended'] = true;
				}
				$cache_data['html'] 		= "";
				$pegasaas->utils->log("PEGASAAS_CACHE_FOLDER_PATH': ".PEGASAAS_CACHE_FOLDER_PATH, "caching");
				$pegasaas->utils->log("cache_data[file]': ".$cache_data['file'], "caching");
				$pegasaas->utils->log("file_folder_name': ".$file_folder_name, "caching");
				$pegasaas->utils->log("filename': ".$filename, "caching");

				$this->asset_folder_path(PEGASAAS_CACHE_FOLDER_PATH.$cache_data['file']);
				$pegasaas->utils->log("About To Check Semaphore 'html_write_cache_{$file_folder_name}'", "caching");

				//if ($pegasaas->utils->semaphore("html_write_cache_{$file_folder_name}")) {
				if (true) {
				//	$pegasaas->utils->log("Semaphore 'html_write_cache_{$file_folder_name}': OK", "caching");
					// remove possible previous copies
					if (file_exists(PEGASAAS_CACHE_FOLDER_PATH.$cache_data['file'])) {
						@unlink(PEGASAAS_CACHE_FOLDER_PATH.$cache_data['file']);
					}

					//if (file_exists(PEGASAAS_CACHE_FOLDER_PATH.$cache_data['original'])) {
					//	@unlink(PEGASAAS_CACHE_FOLDER_PATH.$cache_data['original']);
					//}

					if ($temp_filename && file_exists(PEGASAAS_CACHE_FOLDER_PATH.$cache_data['temp_file'])) {
							@unlink(PEGASAAS_CACHE_FOLDER_PATH.$cache_data['temp_file']);
					}

					// clear third party cache
					$post_id = url_to_postid($object_id);
					$this->clear_third_party_cache($post_id);
					if ($object_id != "" && $this->cloudflare_exists()) {
						//$url = $pegasaas->utils->get_object_id($object_id);
						$url = $object_id;
						$this->purge_cloudflare($url);
					}

					// save cache
				//	$pegasaas->utils->log("Semaphore 'html_write_cache_{$file_folder_name}': OK", "caching");
					$fp = @fopen(PEGASAAS_CACHE_FOLDER_PATH.$cache_data['file'] , "w");

					if ($fp) {
						$pegasaas->utils->log("Succefully opened file for writing: ".PEGASAAS_CACHE_FOLDER_PATH.$cache_data['file'], "caching");

						@fwrite($fp, $buffer);
						@fclose($fp);
					} else {
						$pegasaas->utils->log("Could not open file for writing: ".PEGASAAS_CACHE_FOLDER_PATH.$cache_data['file'], "caching");
					}


					if (strlen(PegasaasOptimize::$original_page_html) > 0 ) {
						$fp = @fopen(PEGASAAS_CACHE_FOLDER_PATH.$cache_data['original'] , "w");

						if ($fp) {
							$pegasaas->utils->log("Succefully opened file for writing: ".PEGASAAS_CACHE_FOLDER_PATH.$cache_data['original'], "caching");

							@fwrite($fp, PegasaasOptimize::$original_page_html);
							@fclose($fp);
						} else {
							$pegasaas->utils->log("Could not open file for writing: ".PEGASAAS_CACHE_FOLDER_PATH.$cache_data['original'], "caching");
						}
					} else {
						$pegasaas->utils->log("Original file contents not existing: ".PEGASAAS_CACHE_FOLDER_PATH.$cache_data['original'], "caching");

					}

					if (!$wl_api && file_exists(PEGASAAS_CACHE_FOLDER_PATH.$cache_data['file']) && filesize(PEGASAAS_CACHE_FOLDER_PATH.$cache_data['file']) < 1000) {
						$pegasaas->utils->log("File length of {$cache_data['file']} less than 1KB");
						if (@unlink(PEGASAAS_CACHE_FOLDER_PATH.$cache_data['file'])) {
							$pegasaas->utils->log("File {$cache_data['file']} was removed");
						} else {
							$pegasaas->utils->log("File {$cache_data['file']} could not be removed");
						}
					}



				//	$pegasaas->utils->release_semaphore("html_write_cache_{$file_folder_name}");
				} else {
			//		$pegasaas->utils->log("Semaphore 'html_write_cache_{$file_folder_name}': OK", "caching");

				}
			} else {
				$cache_data['cache_type'] 	= "db";
				$cache_data['html'] 		= $buffer;
				$cache_data['file'] 		= "";


			}

			$cache_data['hit'] 			= PegasaasUtils::get_permalink();
			$cache_data['hit-time'] 	= time();
			$cache_data['hit-time2'] 	= mktime(date("H"));
			$cache_data['hit-time3'] 	= get_option('gmt_offset');
			$cache_data['hit-time4'] 	= mktime(date("H")+get_option('gmt_offset'));
			$cache_data['when_cached'] 	= mktime(date("H")+get_option('gmt_offset'));
			$cache_data['built'] 		= time();
			$cache_data['css_validation_issue'] 		= $css_vaidation_issue;


			// update the global cache map
		//	$cache_data_map 				= get_option("pegasaas_cache_map", array());
			$cache_data_map	= PegasaasAccelerator::$cache_map;
			$cache_data_map["$object_id"] 	= array("id" => $object_id,
													"built" => time(),
													"css_validation_issue" => $css_validation_issue);
			if ($temp_cache) {
				$cache_data['is_temp'] = true;
				$cache_data_map["$object_id"]['is_temp'] = true;
			}
			if ($extended_coverage) {
					$cache_data['is_extended'] = true;
					$cache_data_map["$object_id"]['is_extended'] = true;
			}
			PegasaasAccelerator::$cache_map = $cache_data_map; // update the instance copy

			//update_option("pegasaas_cache_map", $cache_data_map);


			// update the cache record for this object - this is pulled into the PegasaasAccelerator::$cache_map on the next invocation
			$cached_resource 			= PegasaasUtils::get_object_meta($object_id, "cached_html", $use_cache = false);
			$cached_resource["{$args}"] = $cache_data;
			$pegasaas->utils->update_object_meta($object_id, "cached_html", $cached_resource);



		} else {
			$pegasaas->utils->log("Save Cache Semaphore Locked", "caching");

			if ($wl_api) {

			} else {
				if (!PegasaasUtils::should_strip_footer_comments()) {
				$buffer .= PegasaasUtils::html_comment("Cached copy not saved due to semaphore lock [{$object_id}] [".PegasaasUtils::get_permalink()."]");
				}
			}
		}

		return $buffer;
	}

	function set_cache_geo_modifier($geo_modifier) {
		self::$geo_modifier = $geo_modifier;
	}

	function get_cache_geo_modifier() {
		//print "getting geo modifier\n";
		// see if this setting is already set
		if (self::$geo_modifier != "") {
  			return self::$geo_modifier;
 		}

  		$geo_modifier = "";
  		$modifier_cookies = array("geot_country", "geot_rocket_state", "geot_rocket_city");
		//print "<pre>";
		//var_dump($_COOKIE);
		//print "</pre>";
  		foreach ($modifier_cookies as $cookie_name) {
    		$cookie_value = $_COOKIE["$cookie_name"];
			//print "have $cookie_name \n";
    		if ($cookie_value == "" || $cookie_value == "not-detected") {
      		//print "don't have a value\n";
    		} else {
      			$geo_modifier .= "-".$cookie_value;
				//print "geo modifier is $geo_modifier\n";
    		}
			//print "the cookie value is $cookie_value \n";
			// index.html
			// index-CA.html
			// index-victoria.html
			// index-CA-BC-victoria.html
  		}

  		self::$geo_modifier = $geo_modifier; // set it
  		//print "geo modifier X: $geo_modifier\n";
		return $geo_modifier;
	}


	function assemble_cache_map() {
		global $pegasaas;

		$all_cached_pages = $pegasaas->db->get_results("pegasaas_page_cache");
		$cache_map = array();

		foreach ($all_cached_pages as $row) {
			$data = json_decode($row->data, true);

			if (isset($data['NULL'])) {
				$data = $data["NULL"];
				$resource_id = $row->resource_id;

				$obj = array();
				$obj["id"] = $resource_id;
				$obj["built"] = (isset($data['built']) ? $data['built'] : $data['when_cached']);
				$obj["css_validation_issue"] = (isset($data['css_validation_issue']) ? $data['css_validation_issue'] : false);
				$obj["is_temp"] = (isset($data['is_temp']) ? $data['is_temp'] : false);
				$cache_map["{$resource_id}"] = $obj;
			}
		}



		return $cache_map;
	}

	function pegasaas_purge_image_cdn_edge_network_only() {
		global $pegasaas;

		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
				$this->clear_remote_cache("image", true);

				print json_encode(array("status" => 1));
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}


	function pegasaas_purge_image_resource_cache() {
		global $pegasaas;

		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
				$this->clear_remote_cache("image");

				print json_encode(array("status" => 1));
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}

	function pegasaas_purge_js_resource_cache() {
		global $pegasaas;

		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
				$this->clear_remote_cache("js");

				print json_encode(array("status" => 1));
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}

	function pegasaas_purge_js_cdn_edge_network_only() {
		global $pegasaas;

		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
				$this->clear_remote_cache("js", true);

				print json_encode(array("status" => 1));
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}

	function pegasaas_purge_indv_files_resource_cache() {
		global $pegasaas;

		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
				$files = explode("\n", $_POST['files']);

				if (sizeof($files) > 0) {
					$this->clear_remote_cache_files($files);
					print json_encode(array("status" => 1));
				} else {
					print json_encode(array("status" => 2, "message" => "No files specified"));
				}
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}

	function pegasaas_purge_indv_files_cdn_edge_network_only() {
		global $pegasaas;

		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
				$files = explode("\n", $_POST['files']);

				if (sizeof($files) > 0) {
					$this->clear_remote_cache_files($files, true);
					print json_encode(array("status" => 1));
				} else {
					print json_encode(array("status" => 2, "message" => "No files specified"));
				}
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}

	function pegasaas_purge_css_cdn_edge_network_only() {
		global $pegasaas;

		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
				$this->clear_remote_cache("css", true);

				print json_encode(array("status" => 1));
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}

	function pegasaas_purge_css_resource_cache() {
		global $pegasaas;

		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
				$this->clear_remote_cache("css");

				print json_encode(array("status" => 1));
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}



	function clear_remote_cache($resource_type = "", $clear_cdn_only = false) {
		global $pegasaas;

		$post_fields = array("command" => "clear-all-cache",
							 "resource_type" => $resource_type,
							 "clear_cdn_only" => $clear_cdn_only);

		$pegasaas->api->post($post_fields, array("blocking" => false));

	}

	function clear_godaddy_cache($post_id = "") {
		global $pegasaas;

		//From mu-plugins/gd-system-plugin.php
		//Call mu-plugins/gd-system-plugin/includes/class-cache.php
		$pegasaas->utils->log("GoDaddy Exists.  Attempting to clear $post_id");
		if ($post_id !== "") {
			//$post_id = url_to_postid($resource_id);
			if ($post_id == '0') {
				$page_for_posts 	= get_option( 'page_for_posts' );
				$post 				= get_post($page_for_posts);

				do_action( 'clean_post_cache',           [ $page_for_posts, $post ] );
				$pegasaas->utils->log("Godaddy clearing $page_for_posts");
				//$this->gd_request("BAN")
			}

			$url = $pegasaas->utils->get_object_id($post_id);
			$this->purge_varnish($url);
		} else {
			$post_id = "";
		}

		$post = get_post($post_id);
		do_action( 'clean_post_cache',           [ $post_id, $post ] );
		$pegasaas->utils->log("Godaddy clearing $post_id");
		$pegasaas->utils->log("Godaddy clearing $post->post_title");
	}

	static function purge_varnish($url = "") {
		global $pegasaas;


		$args 	= array("method" => "PURGE",
					   "sslverify" => "false");

		// query the headers of a static file, rather than a page, as a page load may incur server resources
		$response = wp_remote_request($url, $args);



		if (is_a($response, "WP_Error")) {
			$detected_varnish = false;
			$pegasaas->utils->log("Error purging Varnish for: $url", "varnish");
			return 0;
		} else {

			$http_response = $response['http_response'];
			$response = $http_response->get_response_object();

			$pegasaas->utils->log("Purged Varnish: $url", "varnish");
			return $response->status_code == 200;
		}






	}

	private static function gd_request( $method, $url = null ) {

		$url  = empty( $url ) ? $pegasaas->get_home_url() : $url;
		$host = parse_url( $url, PHP_URL_HOST );
		$url  = set_url_scheme( str_replace( $host, Plugin::vip(), $url ), 'http' );

		wp_cache_flush();

		// This forces the APC cache to flush across the server
		update_option( 'gd_system_last_cache_flush', time() );

		wp_remote_request(
			esc_url_raw( $url ),
			[
				'method'   => $method,
				'blocking' => false,
				'headers'  => [
					'Host' => $host,
				],
			]
		);

	}
	function clear_wpengine_cache($post_id = "") {
		global $pegasaas;

		WpeCommon::purge_memcached();
		WpeCommon::clear_maxcdn_cache();

		if ($post_id !== "") {
			$resource_id = $pegasaas->utils->get_object_id($post_id);
			//print "resource_id: $resource"
			/*$post_id= url_to_postid($resource_id);
			if ($post_id == 0) {
			}*/
			//WpeCommon::purge_varnish_cache($post_id, true);
			$url = parse_url( $resource_id, PHP_URL_PATH );
			$this->path_to_purge = $url;
			add_filter( 'wpe_purge_varnish_cache_paths', array( $this, 'set_wpengine_cache_resource_path' ) );
			WpeCommon::purge_varnish_cache(1);
			remove_filter( 'wpe_purge_varnish_cache_paths', array( $this, 'set_wpengine_cache_resource_path' ) );
			$pegasaas->utils->log("Purged WP Engine Cache: Post ID ".$post_id. " for ".$url." resource id ".$resource_id);

		} else {
			$post_id = "";
			WpeCommon::purge_varnish_cache();
			$pegasaas->utils->log("Purged WP Engine Cache: ALL");

		}


	}

	function set_wpengine_cache_resource_path() {
		$url = array( $this->path_to_purge );
		return $url;
	}


	/*
	in plugin.php
		$wpe_common = WpeCommon::instance();
		add_action( $hook, array( $wpe_common, 'purge_varnish_cache_all' ) );

	In Admin-ui.php:
		global $pegasaas;
		$pegasaas->pegasaas_purge_all_resource_cache();
		$pegasaas->utils->log('Deleted the Pegassaas-cache folder');


	*/


	function godaddy_exists() {
		global $pegasaas;

		if (!function_exists("get_mu_plugins")) {
			include(ABSPATH."wp-admin/includes/plugin.php");
		}

		if (function_exists("get_mu_plugins")){
			$plugins = get_mu_plugins();
			return array_key_exists("gd-system-plugin.php", $plugins);
		} else {
			$pegasaas->utils->log("Function gd-system-plugin.php() not found for request {$_SERVER['REQUEST_URI']}");
			return false;
		}
	}
	function wpengine_exists() {
		global $pegasaas;

		if (!function_exists("get_mu_plugins")) {
			include(ABSPATH."wp-admin/includes/plugin.php");
		}

		if (function_exists("get_mu_plugins")){
			$plugins = get_mu_plugins();
			if(isset($plugins['mu-plugin.php'])&&isset($plugins['mu-plugin.php']['Name'])&&$plugins['mu-plugin.php']['Name']=='WP Engine System' ){
				//$pegasaas->utils->log("WP Engine exists: " );
				return true;
			}else{
				//$pegasaas->utils->log("WP Engine DOES NOT exist: ");
			}
			return false;
		} else {
			$pegasaas->utils->log("Function gd-system-plugin.php() not found for request {$_SERVER['REQUEST_URI']}");
			return false;
		}
	}

	function kinsta_exists() {
		global $pegasaas;

		if (!function_exists("get_mu_plugins")) {
			include(ABSPATH."wp-admin/includes/plugin.php");
		}

		if (function_exists("get_mu_plugins")){
			$plugins = get_mu_plugins();
			return array_key_exists("kinsta-mu-plugins.php", $plugins);
		} else {
			$pegasaas->utils->log("Function get_mu_plugins() not found for request {$_SERVER['REQUEST_URI']}");
			return false;
		}
	}


	function pagely_exists() {
		global $pegasaas;
		return class_exists( 'PagelyCachePurge' );
	}

	// bluehost
	function endurance_exists() {
		global $pegasaas;

		if (!function_exists("get_mu_plugins")) {
			include(ABSPATH."wp-admin/includes/plugin.php");
		}

		if (function_exists("get_mu_plugins")){
			$plugins = get_mu_plugins();
			return array_key_exists("endurance-page-cache.php", $plugins);
		} else {
			$pegasaas->utils->log("Function get_mu_plugins() not found for request {$_SERVER['REQUEST_URI']}");
			return false;
		}
	}

	function endurance_active() {
		if ($this->endurance_exists()) {
			global $epc;
			if (isset($epc) && $epc->cache_level > 0) {
				return true;
			}
		}

		return false;

	}

	function disable_endurance() {
		update_option( 'endurance_cache_level', 0 );
	}


	function pantheon_exists() {
		if (!function_exists("get_mu_plugins")) {
			include(ABSPATH."wp-admin/includes/plugin.php");
		}

			return false;
		if (function_exists("get_mu_plugins")){
			$plugins = get_mu_plugins();
			return array_key_exists("kinsta-mu-plugins.php", $plugins);
		} else {
			$pegasaas->utils->log("Function get_mu_plugins() not found for request {$_SERVER['REQUEST_URI']}");
			return false;
		}
	}


	function pegasaas_purge_all_cloudflare_cache() {
		global $pegasaas;
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
				$success = $this->purge_cloudflare();

				print json_encode(array("status" => $success));
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}

	function pegasaas_purge_all_resource_cache() {
		global $pegasaas;
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
				$this->clear_remote_cache();

				print json_encode(array("status" => 1));
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}

	function pegasaas_purge_all_cdn_edge_network_only() {
		global $pegasaas;
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
				$this->clear_remote_cache("", true);

				print json_encode(array("status" => 1));
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}

	function pegasaas_purge_all_html_cache() {
		global $pegasaas;

		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$this->clear_cache();
			print json_encode(array("status" => 1));





		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response





	}



	function pegasaas_reoptimize_all_html_cache() {
		global $pegasaas;

		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			// brandon, here we need to get a list of all html cache elements
			// if the item is an index-temp.html then add it to the queue clear the cache
			// if the item is an index.html then add it to the queue to re-optimize

			$this->queue_reoptimize_global_cache("all");
			//$this->clear_cache();
			print json_encode(array("status" => 1));





		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response





	}




	function pegasaas_reoptimize_local_static_asset() {
		global $pegasaas;

		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$asset_id = $_POST['asset_id'];
			$asset = $pegasaas->db->get_single_record("pegasaas_static_asset", array("asset_id" => $asset_id));

			$file_name 	= str_replace("../", "", $asset->original_file_name);
			$path =  PEGASAAS_CACHE_FOLDER_PATH."{$file_name}";
			$url = PEGASAAS_CACHE_FOLDER_URL.$file_name;
			$this->clear_cache_file($path);
			//print json_encode(array("status" => 2));
			//wp_die();
			$args 	= array(
					   "method" => "GET",
					   'sslverify' => 'false',
						'timeout' => 15,
						'blocking' => true);


			$response = wp_remote_request($url, $args);

			$data = $pegasaas->db->get_single_record("pegasaas_static_asset", array("original_file_name" => $file_name));

			$return = array("status" => 1,
							"asset_id" => $data->asset_id,
							"asset_status" => $data->status,
							"optimized_file_size" => $data->optimized_file_size,
						   "original_file_size" => $data->original_file_size );

			print json_encode($return);

		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response


	}



	function pegasaas_remotely_delete_local_static_asset() {
		global $pegasaas;

		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {


			$file_name 	= str_replace("../", "", $_POST['file_name']);
			$path 		=  PEGASAAS_CACHE_FOLDER_PATH."{$file_name}";

			if (strstr($path, "index.html" || strstr($path, "index-temp.html"))) {
				$path = str_replace("index.html", "", $path);
				$path = str_replace("index-temp.html", "", $path);
				$this->clear_page_cache($path);
			} else {
				$this->clear_cache_file($path);
				if ($file_name != "" && $this->cloudflare_exists()) {
					$url = PEGASAAS_CACHE_FOLDER.$file_name;
					$this->purge_cloudflare($url);
				}
			}


			$return = array("status" => 1, "file_name" => $path);

			print json_encode($return);

		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response


	}


	function pegasaas_delete_local_static_asset() {
		global $pegasaas;

		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$asset_id = $_POST['asset_id'];
			$asset = $pegasaas->db->get_single_record("pegasaas_static_asset", array("asset_id" => $asset_id));

			$file_name 	= str_replace("../", "", $asset->original_file_name);
			$path 		=  PEGASAAS_CACHE_FOLDER_PATH."{$file_name}";

			$this->clear_cache_file($path);

			if ($file_name != "" && $this->cloudflare_exists()) {
				$url = PEGASAAS_CACHE_FOLDER.$file_name;
				$this->purge_cloudflare($url);
			}

			$pegasaas->db->delete("pegasaas_static_asset", array("asset_id" => $asset_id));
			$return = array("status" => 1, "asset_id" => $asset_id);

			print json_encode($return);

		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response


	}


	function pegasaas_purge_all_local_image_cache() {
		global $pegasaas;

		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {


			$this->clear_local_resource_cache("jpeg");
			$this->clear_local_resource_cache("jpg");
			$this->clear_local_resource_cache("png");
			$this->clear_remote_cache("image");
			$pegasaas->db->delete("pegasaas_static_asset", array("asset_type" => "image"));
			update_option("pegasaas_image_cache", array());
			print json_encode(array("status" => 1));

		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response

	}

	function pegasaas_purge_all_local_css_cache() {
		global $pegasaas;

		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$this->clear_local_resource_cache("css");
			print json_encode(array("status" => 1));

		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response

	}

	function pegasaas_purge_all_local_js_cache() {
		global $pegasaas;

		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$this->clear_local_resource_cache("js");
			print json_encode(array("status" => 1));

		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response

	}

	function clear_third_party_cache($id = "") {
		global $pegasaas;


		$pegasaas->utils->log("Clearing Third Party Cache of $id", "caching");

		$debug = false;

		if ($id === "") {
			$pegasaas->utils->log("Clearing ALL Third Party Cache");
			// HANDLE THIRD PARTY MU-PLUGINS
			if ($this->kinsta_exists()) {
				$pegasaas->utils->log("Third Party Caching Plugin Detected: Kinsta", "caching");

				$this->clear_kinsta_cache();
			}

			if ($this->pagely_exists()) {
				$pegasaas->utils->log("Third Party Caching Plugin Detected: Pagely", "caching");
				$this->clear_pagely_cache();
			}

			if ($this->godaddy_exists()) {
				$pegasaas->utils->log("Third Party Caching Plugin Detected: GoDaddy", "caching");
				$this->clear_godaddy_cache();
			}
			if ($this->wpengine_exists()) {
				$pegasaas->utils->log("WP Engine exists. Clearing All.", "caching" );
				$this->clear_wpengine_cache();
			}
			if ($this->pantheon_exists()) {
				$pegasaas->utils->log("Third Party Caching Plugin Detected: Pantheon", "caching");
				$this->clear_pantheon_cache();
			}

			// HANDLE THIRD PARTY PLUGINS
			if ($pegasaas->utils->does_plugin_exists_and_active("w3-total-cache")) {
				//in plugins/w3-total-cache/wp-content/object-cache.php
				//global $wp_object_cache;

				//$wp_object_cache->delete( $id );
				if (function_exists('w3tc_flush_all')){
					w3tc_flush_all();
				}
				$pegasaas->utils->debug($debug, '1. Clearing W3 Total Cache.'."\n");


			} else {
				if ($debug) { print '1. W3 Total Cache Inactive or Not Installed.'."\n";}
			}

			if ($pegasaas->utils->does_plugin_exists_and_active("wp-super-cache")) {
				// In plugins/wp-super-cache/wp-cache.php
				// Lists all cached Files: wp_cache_files();
				wp_cache_clean_cache('wp-cache-', $all = true);
				if ($debug) { print '2. Clearing WP Super Cache.'."\n"; }
			}else{
				if ($debug) { print '2. WP Super Cache Inactive or Not Installed.'."\n";}
			}
			if ($pegasaas->utils->does_plugin_exists_and_active("wp-rocket")) {
				//in plugins/wp-rocket/inc/common/purge.php
				//rocket_clean_post( $post_id );
				//rocket_clean_post( 77777 );						//Must itterate through all the posts!!!!!!!!!!!!!!!!
				if (function_exists('rocket_clean_domain')) {
					rocket_clean_domain();
				}
				if ($debug) {print '3. Clearing WP Rocket Cache.'."\n"; }
			}else{
				if ($debug) {print '3. WP Rocket Cache Inactive or Not Installed.'."\n";}
			}
			if ($pegasaas->utils->does_plugin_exists_and_active("wp-fastest-cache")) {
				//in plugins/wp-fastest-cache/wpFastestCache.php
				$wpfc = new WpFastestCache();
				$wpfc->deleteCache();
				if ($debug) {print '4. Clearing WP Fastest Cache.'."\n";}
			}else{
				if ($debug) {print '4. Fastest Cache Inactive or Not Installed.'."\n";}
			}
			if ($pegasaas->utils->does_plugin_exists_and_active("litespeed-cache")) {
				//in plugins/litespeed-cache/includes/litespeed-cache-purge.class.php
				//ID Method purge_post($value=$id)
				$purgeClass=new LiteSpeed_Cache_Purge();
				$purgeClass->purge_all();
				if ($debug) {print '5. Clearing LiteSpeed Cache.'."\n";}
			}else{
				if ($debug) {print '5. LiteSpeed Cache Inactive or Not Installed.'."\n";}
			}
			if ($pegasaas->utils->does_plugin_exists_and_active("hyper-cache")) {
				//in plugins/hyper-cache/plugin.php
				//ID Method clean_post($post_id, $clean_archives = true, $clean_home = true)
				$hyperCache=new HyperCache();
				$hyperCache->clean();
				if ($debug) {print '6. Clearing Hyper Cache.'."\n";}
			}else{
				if ($debug) {print '6. Hyper Cache Inactive or Not Installed.'."\n";}
			}
			if ($pegasaas->utils->does_plugin_exists_and_active("comet-cache")) {
				//in plugins/comet-cache/src/includes/classes/Actions.php
				//$myActions=new CometCache();
				//$counter = $this->wipeCache(true);
				//BROKEN.  Cannot Create Object!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
				if ($debug) {print '7. NOT WORKING. Clearing Comet Cache. Wiped: '.$counter.' Items.'."\n";}
			} else{
				if ($debug) {print '7. Comet Cache Inactive or Not Installed'."\n";}
			}

			if ($pegasaas->utils->does_plugin_exists_and_active("sg-cachepress")) {
				//in /sg-cachepress/class-sg-cachepress-supercacher.php


				////$SGOptions=new SG_CachePress_Options();
    			//global $sg_cachepress_environment;
				//$SGSuperCache=new SG_CachePress_Supercacher($SGOptions,$environment=null);
				//$SGSuperCache->purge_cache();


				if ($debug) {print '9. Site Ground Optimizer. Wiped.'."\n";}
			}else{
				if ($debug) {print '9. Site Ground Optimizer Inactive or Not Installed'."\n";}
			}


		} else {
			if ($id !== "" && !is_numeric($id)) {
				// if it is non-numeric, then this id is actually a resource id, and we need to change it to the post id
				$id = url_to_postid($id);
			}

			if ($debug) {print 'Purging INDIVIDUAL Object by POST_ID'."\n"; }
			if ($this->kinsta_exists()) {
				$pegasaas->utils->log("Third Party Caching Plugin Detected: Kinsta", "caching");

				if ($id == 0) {
					$this->clear_kinsta_cache($id);
					$page_on_front = get_option("page_on_front");
					if ($page_on_front != "") {
						$this->clear_kinsta_cache($page_on_front);
					}
				} else {
					$this->clear_kinsta_cache($id);
				}
			}

			if ($this->pagely_exists()) {
				$pegasaas->utils->log("Third Party Caching Plugin Detected: Pagely", "caching");

				if ($id == 0) {
					$this->clear_pagely_cache($id);
					$page_on_front = get_option("page_on_front");
					if ($page_on_front != "") {
						$this->clear_pagely_cache($page_on_front);
					}
				} else {
					$this->clear_pagely_cache($id);
				}
			}

			if ($this->wpengine_exists()) {
				$pegasaas->utils->log("WP Engine exists. Clearing Post: $id", "caching" );
				$this->clear_wpengine_cache($id);
			}

			if ($this->godaddy_exists()) {
				$pegasaas->utils->log("Third Party Caching Plugin Detected: GoDaddy", "caching");

				if ($id == '0') {
					$this->clear_godaddy_cache($id);
					$page_on_front = get_option("page_on_front");
					if ($page_on_front != "") {
						$this->clear_godaddy_cache($page_on_front);
					}
				} else {
					$this->clear_godaddy_cache($id);
				}
			}


			if ($this->pantheon_exists()) {
				$pegasaas->utils->log("Third Party Caching Plugin Detected: Pantheon", "caching");

				if ($id == 0) {
					$this->clear_pantheon_cache($id);
					$this->clear_pantheon_cache(get_option("page_on_front"));
				} else {
					$this->clear_pantheon_cache($id);
				}

			}

			if ($pegasaas->utils->does_plugin_exists_and_active("w3-total-cache")) {
				$pegasaas->utils->log("Third Party Caching Plugin Detected: W3 Total Cache.  Deleting post id: {$id}", "caching");

				//in plugins/w3-total-cache/wp-content/object-cache.php
				//wp_cache_delete($id);
				//apply_filters( 'w3tc_preflush_post', true, $extras );
				//do_action( 'w3tc_flush_post', $id, array('ui_action' => true) );
				//global $wp_object_cache;
				//$wp_object_cache->delete( $id );

				if (function_exists('w3tc_flush_post')){
					//print "flushing";
					w3tc_flush_post($id);
				}
				if ($debug) {print '1. Clearing Total Cache'."\n"; }
			}else{
				if ($debug) {print '1. Total Cache Inactive or Not Installed'."\n"; }
			}

			if ($pegasaas->utils->does_plugin_exists_and_active("wp-super-cache")) {
				//in plugins/wp-super-cache/wp-cache.php
				//Lists all cached Files: wp_cache_files();
				wp_cache_clean_cache('wp-cache-'.$id, $all = true);
				if ($debug) {print '2. Clearing WP Super Cache.'."\n";  }
			}else{
				if ($debug) {print '2. WP Super Cache Inactive or Not Installed.'."\n"; }
			}
			if ($pegasaas->utils->does_plugin_exists_and_active("wp-rocket")) {
				//in plugins/wp-rocket/inc/common/purge.php
				//rocket_clean_post( $post_id );
				rocket_clean_post( $id );
				if ($debug) {print '3. Clearing WP Rocket Cache.'."\n";  }
			}else{
				if ($debug) {print '3. WP Rocket Cache Inactive or Not Installed.'."\n"; }
			}
			if ($pegasaas->utils->does_plugin_exists_and_active("wp-fastest-cache")) {
				//in plugins/wp-fastest-cache/wpFastestCache.php
				$wpfc = new WpFastestCache();
				$wpfc->singleDeleteCache(false, $id);
				if ($debug) {print '4. Clearing Fastest Cache'."\n"; }
			}else{
				if ($debug) {print '4. Fastest Cache Inactive or Not Installed'."\n"; }
			}
			if ($pegasaas->utils->does_plugin_exists_and_active("litespeed-cache")) {
				//in plugins/litespeed-cache/includes/litespeed-cache-purge.class.php
				$purgeClass=new LiteSpeed_Cache_Purge();
				$purgeClass->purge_post($value=$id);
				if ($debug) {print '5. Clearing LiteSpeed Cache.'."\n"; }
			}else{
				if ($debug) {print '5. LiteSpeed Cache Inactive or Not Installed.'."\n"; }
			}
			if ($pegasaas->utils->does_plugin_exists_and_active("hyper-cache")) {
				//in plugins/hyper-cache/plugin.php
				$hyperCache=new HyperCache();
				$hyperCache->clean_post($id, $clean_archives = true, $clean_home = true );
				if ($debug) {print '6. Clearing Hyper Cache.'."\n"; }
			}else{
				if ($debug) {print '6. Hyper Cache Inactive or Not Installed.'."\n"; }
			}


			if ($pegasaas->utils->does_plugin_exists_and_active("sg-cachepress")) {
				//in /sg-cachepress/class-sg-cachepress-supercacher.php
				//$SGOptions = new SG_CachePress_Options();
    			//global $sg_cachepress_environment;
				//$SGSuperCache=new SG_CachePress_Supercacher($SGOptions,$sg_cachepress_environment);
				//$SGSuperCache->hook_add_post( $id );

				//die('Clearing77 Site Ground Optimizer');

				if ($debug) {print '9. Site Ground Optimizer. Wiped.'."\n";}
			}else{
				if ($debug) {print '9. Site Ground Optimizer Inactive or Not Installed'."\n";}
			}



		}


	}

	function clear_contact_form_7_pages_cache() {
		global $pegasaas;
		$pegasaas->utils->log("clearing pages with Contact Form 7 Form: START", "compatibility_contact_form_7");

		$post_type = "pages-containing-contact-form-7";
		if (array_key_exists($post_type, $pegasaas->post_type_pages)) {
			foreach ($pegasaas->post_type_pages["$post_type"] as $object_id => $found) {
				$pegasaas->utils->log("clearing page with Contact Form 7 Form: {$object_id}", "compatibility_contact_form_7");

				$this->clear_cache($object_id);
			}
		} else {
			$pegasaas->utils->log("clearing pages with Contact Form 7 Form: NO PAGES", "compatibility_contact_form_7");
		}
		$pegasaas->utils->log("clearing pages with Contact Form 7 Form: COMPLETE", "compatibility_contact_form_7");

	}


	function clear_post_types_page_cache($post_id) {
		global $pegasaas;
		global $test_debug;
		$ignore_post_types = array("shop_order", "cw_admin_audit");
		$pegasaas->utils->log("clear_post_types_page_cache for {$post_id}", "caching");
		
		$post = get_post($post_id);
		$post_type = $post->post_type;

		if (isset($post) && isset($post->post_type) && in_array($post->post_type, $ignore_post_types)) {
			if ($test_debug) {
				print "<li>Ignoring Cache Clear of {$post->post_type}</li>";
			}
			return;
		}

		if (array_key_exists($post_type, $pegasaas->post_type_pages) && $post_type != "page" && $post_type != "post") {
			foreach ($pegasaas->post_type_pages["$post_type"] as $object_id => $found) {
				if (!strstr($object_id, "tag")) {
					$this->clear_cache($object_id);
				}
			}
		} else {
			//$pegasaas->utils->log("clear_post_types_page_cache array key of {$post_type} does not exist in ".print_r($pegasaas->post_type_pages, true), "caching");

		}
	}



	function clear_blog_page_cache($post_id) {
		global $pegasaas;
		global $test_debug;
		$ignore_post_types = array("shop_order", "cw_admin_audit");
		
		$post = get_post($post_id);

		if (isset($post) && isset($post->post_type) && in_array($post->post_type, $ignore_post_types)) {
			if ($test_debug) {
				print "<li>Ignoring Archive Cache Clear of {$post->post_type}</li>";
			}
			return;
		}
	
		$pegasaas->utils->log("PegasaasCache::clear_blog_page_cache({$post_id})", "caching");


	

		$object_id = $pegasaas->utils->get_object_id($post_id);


		if (get_post_type($post_id) == "post") {
			$show_on_front 	= get_option( 'show_on_front' );
			$page_for_posts = get_option( 'page_for_posts' );
			$page_on_front 	= get_option( "page_on_front" );



			// if the blog is set up as the home page, set the object id as /
			if ($show_on_front == "posts" || $page_for_posts == 0 || ($page_for_posts == "" && $page_on_front == "")) {
				$object_id = "/";

			// otherwise grab the page for posts and use that as the object id, to be cleared
			} else {
			  	$object_id = $pegasaas->utils->get_object_id($page_for_posts);
			}

			$this->clear_cache($object_id);

			PegasaasCache::clear_paginated_pages_cache($object_id);

			// clear all category pages for the post
			$categories = wp_get_post_categories($post_id);
			$categories_cleared = array();
			foreach ($categories as $category_id) {
				$category_obj = get_category($category_id);


				$category_object_id = str_replace($pegasaas->get_home_url('', 'https'), "", get_category_link($category_id));
				$category_object_id = str_replace($pegasaas->get_home_url('', 'http'), "", $category_object_id);
				$this->clear_cache($category_object_id);
				PegasaasCache::clear_paginated_pages_cache($category_object_id);

				$categories_cleared["$category_id"] = true;
			}

			if (is_array($_POST['post_category'])) {
				foreach ($_POST['post_category'] as $category_id) {
					if ($category_id == 0) {
						continue;
					}
					if (array_key_exists($category_id, $categories_cleared)) {
						continue;

					}

					$category_object_id = str_replace($pegasaas->get_home_url('','https'), "", get_category_link($category_id));
					$category_object_id = str_replace($pegasaas->get_home_url('','http'), "", $category_object_id);



					$this->clear_cache($category_object_id);
					PegasaasCache::clear_paginated_pages_cache($category_object_id);

					$categories_cleared["$category_id"] = true;
				}
			}

		}

	}


	function asset_folder_path($file_name) {
		$local_path = explode("/",$file_name);
		array_pop($local_path);
		$local_path = implode("/", $local_path);
		if (!is_dir($local_path)) {
		  self::mkdirtree($local_path, 0755, true);
		}
	}






	public static function mkdirtree($path, $permission, $recurse) {
		   $success = @mkdir($path, $permission);
		   if (!$success) {
			   $pieces = explode("/", rtrim($path, "/"));
			   array_pop($pieces);
			   $sub_path = implode("/", $pieces);
			   self::mkdirtree($sub_path, $permission, $recurse);
			   @mkdir($path, $permission);
				//chmod($path, 0777);

		   } else {
			 // chmod($path, 0777);
		   }
	}




	/***************************************************
	 * REMOTE AJAX FUNCTIONS
	 **************************************************/
	function pegasaas_remote_clear_page_cache() {
		global $pegasaas;
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
				self::$is_remote_call = true;
			    $return_var = array();

				$resource_id = $_POST['resource_id'];

				if ($resource_id != "") {
					$return_var['method'] = "clear_page_cache";
					$return_var['resource_id'] = $resource_id;
					$pegasaas->utils->log("pegasaas_remote_clear_page_cache for $resource_id", "caching");

					$this->clear_page_cache($resource_id);
				} else {
					$return_var['method'] = "clear_cache";
					$this->clear_cache();
				}
				$return_var['status'] = 1;
				$return_var['server'] = $_SERVER['SERVER_ADDR'];


				print json_encode($return_var);

		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response

	}

	function clear_page_cache($resource_id, $output_json = false) {
		global $pegasaas;


			    unset(PegasaasAccelerator::$cache_map["{$resource_id}"]);


				
				//$cached_htmls = PegasaasUtils::get_object_meta($resource_id, "cached_html");
				$this->clear_cache_file(PEGASAAS_CACHE_FOLDER_PATH."{$resource_id}index*.html");

				if (isset(PegasaasAccelerator::$settings['settings']['accelerated_mobile_pages']) && PegasaasAccelerator::$settings['settings']['accelerated_mobile_pages']['status'] > 0) {
					$this->clear_cache_file(PEGASAAS_CACHE_FOLDER_PATH."{$resource_id}amp/index*.html");
				}

				$pegasaas->utils->log("clear_page_cache for $resource_id", "caching");


				// this will also delete it from the PegasaasAccelerator::$cache_map for the next invocation
				$pegasaas->utils->delete_object_meta($resource_id, "cached_html");


				// clear the combined css
				$this->clear_combine_css_cache($resource_id);

				// also clear the deferred JS
				$this->clear_deferred_js_cache($resource_id);

				$this->clear_cache_file(PEGASAAS_CACHE_FOLDER_PATH."{$resource_id}semi-critical-*.css");
				$this->clear_cache_file(PEGASAAS_CACHE_FOLDER_PATH."{$resource_id}head-js-*.js");
				$this->clear_cache_file(PEGASAAS_CACHE_FOLDER_PATH."{$resource_id}footer-js-*.js");
				$this->clear_cache_file(PEGASAAS_CACHE_FOLDER_PATH."{$resource_id}bundled-js-*.js");


				if (@PegasaasAccelerator::$settings['settings']["multi_server"]['status'] > 0 && @sizeof(PegasaasAccelerator::$settings['settings']["multi_server"]['ips']) > 1 && !self::$is_remote_call) {
					$pegasaas->utils->log("PegasaasCache::about to invoke clear_multi_server_page_cache", "caching");
					$this->clear_multi_server_page_cache($resource_id);

				} else {
				//$pegasaas->utils->log("PegasaasCache::skipping clear_multi_server_page_cache", "caching");
				//$pegasaas->utils->log("PegasaasCache::status: ".PegasaasAccelerator::$settings['settings']["multi_server"]['status'], "caching");
				//$pegasaas->utils->log("PegasaasCache::count: ".sizeof(PegasaasAccelerator::$settings['settings']["multi_server"]['ips']), "caching");
				//$pegasaas->utils->log("PegasaasCache::is_remote ".self::$is_remote_call, "caching");


				}

				if ($output_json) {
					print json_encode(array("status" => 1));
				}

				$pegasaas->utils->release_semaphore("pegasaas_deferred_js");
				$this->clear_third_party_cache($resource_id);

	}

	function pegasaas_clear_page_cache() {
		global $pegasaas;
		$pegasaas->utils->log("PegasaasCache::pegasaas_clear_page_cache()", "caching");

		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			if ($_POST['resource_id'] == "") {
				print json_encode(array("status" => 0, "message" => 'Error: Blank Resource ID'));
			} else {
				$this->clear_page_cache($_POST['resource_id'], true);

			}

		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}


	function pegasaas_build_page_cache() {
		global $pegasaas;


		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			if ($_POST['resource_id'] == "") {
				print json_encode(array("status" => 0, "message" => 'Error: Blank Resource ID'));
			} else {
				$resource_id = $_POST['resource_id'];



				$pegasaas->utils->touch_url($resource_id, array("headers" => array("X-Pegasaas-Priority-Optimization" => 2,
																				   "X-Pegasaas-Bypass-Cache" => 1,
																				   "X-Pegasaas-Manual-Request" => 1)));
				print json_encode(array("status" => 1));
			}

		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}




	function pegasaas_clear_js_cache() {
		global $pegasaas;

		// need to get the post id, and clear the cache

		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			if ($_POST['resource_id'] == "") {
				print json_encode(array("status" => 0, "message" => 'Error: Blank Resource ID'));
			} else {
				if ($pegasaas->utils->semaphore("pegasaas_deferred_js")) {
					$resource_id = $_POST['resource_id'];
					$deferred_js_records 	= get_option('pegasaas_deferred_js', array());
					unset($deferred_js_records[$resource_id]);

					update_option("pegasaas_deferred_js", $deferred_js_records);

					$pegasaas->utils->delete_object_meta($resource_id, "deferred_js");

					print json_encode(array("status" => 1));
					$pegasaas->utils->release_semaphore("pegasaas_deferred_js");
				} else {
					print json_encode(array("status" => 0, "message" => 'Error: Semaphore Lock - pegasaas_deferred_js'));
				}
			}

		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}



	function pegasaas_clear_css_cache() {
		global $pegasaas;



		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			if ($_POST['resource_id'] == "") {
				print json_encode(array("status" => 0, "message" => 'Error: Blank Resource ID'));
			} else {
				if ($pegasaas->utils->semaphore("pegasaas_critical_css", $wait = 0, $stale_time = 20)) {
					if ($pegasaas->utils->semaphore("pegasaas_pending_critical_css_request", $wait = 0, $stale_time = 30)) {
						$critical_css = get_option('pegasaas_critical_css', array());
						unset($critical_css["{$_POST['resource_id']}"]);
						update_option("pegasaas_critical_css", $critical_css);

						$existing_requests = get_option("pegasaas_pending_critical_css_request", array());
						unset($existing_requests["{$_POST['resource_id']}"]);
						update_option("pegasaas_pending_critical_css_request", $existing_requests);

						$pegasaas->utils->delete_object_meta($_POST['resource_id'], "critical_css");

						print json_encode(array("status" => 1));
						$pegasaas->utils->release_semaphore("pegasaas_pending_critical_css_request");
					} else {
						print json_encode(array("status" => 0, "message" => 'Error: Semaphore Lock - pegasaas_pending_critical_css_request'));
					}
					$pegasaas->utils->release_semaphore("pegasaas_critical_css");
				} else {
					print json_encode(array("status" => 0, "message" => 'Error: Semaphore Lock - pegasaas_critical_css'));
				}
			}

		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}


}
?>