<?php
class PegasaasUtils {
	var $data_cache = array();
	var $data_cache_last_updated = array();
	var $http_auth_blocking = array();
	static $all_pages_and_posts;
	static $all_pages_and_posts_use_filter = false;
	var $use_domain_specific_handling; 
	static $last_execution_time = 0;
	static $last_benchmark_execution_time = 0;
	static $last_detailed_execution_time = 0;
	static $log_buffer = "";
	static $log_buffer_detailed = "";
	
	function __construct() {
	
	}
	
    /**
     * Generate a random string that is typically used to define a name for
     * the instance, which is used when logging information.
     *
     * @param integer $length -- length of the return variable
     *
     * @return string Alpha-numeric string, with the length of the $length variable
     */	
	function generate_random_string($length = 16) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		
		// generate the random string here
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
	static function wpml_active() {
		if (self::does_plugin_exists_and_active("wpml")) {
			//print "Yes is active";
			if (function_exists("wpml_get_setting")) {
				return true;
			}
		}
		return false;
	}
	static function wpml_multi_domains_active() {
		if (self::does_plugin_exists_and_active("wpml")) {
			//print "Yes is active";
			if (function_exists("wpml_get_setting")) {
			//	print "yes have the function";
				$negotiation_type = wpml_get_setting( 'language_negotiation_type');
				// negotiation_type 1 - standard, folder method, such as https://www.yoursite.com/fr/
				// negotiation_type 2 - multi-domain, such as https://fr.yoursite.com/ 
				// negotiation_type 3 - query string, such as https://www.yoursite.com/?lang=fr 
			//	print "negotaation type is {$negotation_type}\n";

				$wpml_domains = wpml_get_setting( 'language_domains' );
			//	var_dump($wpml_domains);
				if ($negotiation_type == 2) {
					$wpml_domains = wpml_get_setting( 'language_domains' );
					if (sizeof($wpml_domains) > 0) {

						return true;
					}
				} 
			}
		}
		//print "returning FALSE";
		return false;
	}
	
	static function get_wpml_domains() {
		$domains = wpml_get_setting( 'language_domains' );
		return $domains;
	}

	static function should_strip_footer_comments() {
		if (isset(PegasaasAccelerator::$settings['settings']['minify_html']) && PegasaasAccelerator::$settings['settings']['minify_html']['status'] == 1 && 
		isset(PegasaasAccelerator::$settings['settings']['minify_html']['strip_footer_comments']) && PegasaasAccelerator::$settings['settings']['minify_html']['strip_footer_comments'] == 1) {
			return true;
		}
		return false;
	}
	
	static function html_comment($comment) {
		
		if (isset(PegasaasAccelerator::$settings['settings']['white_label']) && PegasaasAccelerator::$settings['settings']['white_label']['status'] == 1) {
				return "\n<!-- Web Performance: {$comment} -->";
			} else {
				return "\n<!-- Pegasaas Accelerator WP: {$comment} -->";
			}		
		
	}
	function strip_wprocket_critical_css($buffer) {
		$pattern = '/<style id="rocket-critical-css">.*?<\/style>/s';
		$buffer = preg_replace($pattern, "<!-- wp rocket critical css stripped -->\n", $buffer);
		
		return $buffer;
	}

	
	static function strip_url_port($url) { 
		$url_parts = parse_url($url);
		unset($url_parts['port']);

		if (!isset($url_parts['path'])) {
			$url_parts['path'] = ""; // initialize
		}
		$portless = $url_parts['scheme']."://".$url_parts['host'].$url_parts['path'];

		if (isset($url_parts['query'])) {
			$portless .= "?".$url_parts['query'];
		}
		
		return $portless;
	}

	function get_server_address_via_api() {
		$ip_via_remote_endpoint = get_option("pegasaas_server_ip_via_remote_endpoint");
		
		if ($ip_via_remote_endpoint != NULL) {
			return $ip_via_remote_endpoint;
		} else {
			$response = wp_remote_request(PEGASAAS_REMOTE_IP_TEST_ENDPOINT);
		
			if (is_a($response, "WP_Error")) {
					$ip_via_remote_endpoint = $_SERVER["REMOTE_ADDR"]; 
			} else {
					$http_response = $response['http_response'];
					if ($http_response) { 
						$ip_via_remote_endpoint =  $http_response->get_data();
					}
			}
			
			if (!filter_var($ip_via_remote_endpoint, FILTER_VALIDATE_IP)) {
    			$ip_via_remote_endpoint = $_SERVER["REMOTE_ADDR"];
			} 
			update_option("pegasaas_server_ip_via_remote_endpoint", $ip_via_remote_endpoint);
			return $ip_via_remote_endpoint;
		}
		
	}
	
	function get_http_auth_special_instructions() {
		global $pegasaas; 
		
		// mark the beginning of the special instructions
		$instructions = "# PEGASAAS ACCELERATOR WP: ALLOW API ACCESS START\n";
		$instructions.= "Order allow,deny\n";
		
		foreach ($pegasaas->api->get_api_ips() as $ip) { 
			$instructions .= "Allow from {$ip}\n"; 
		}
		
		// add server IP address
		$instructions .= "# allow the server to access the website\n";
		$server_address = $_SERVER['SERVER_ADDR'];
		$server_address_2 = $this->get_server_address_via_api();
		
		if ($server_address != "") {
			$instructions .= "Allow from {$server_address}\n";
		}
		if ($server_address != $server_address_2 && $server_address_2 != "") {
			$instructions .= "Allow from {$server_address_2}\n";
		}
		

		$instructions .= "Satisfy any\n";
		$instructions .= "# PEGASAAS ACCELERATOR WP: ALLOW API ACCESS END\n\n";
		
		return $instructions;
		
	}
	
	
	static function get_home_domain() {
		global $pegasaas;
		
		$home_domain = str_replace("https://", "", PegasaasUtils::strip_url_port($pegasaas->get_home_url()));
		$home_domain = str_replace("http://", "", $home_domain);
		$home_domain = trim($home_domain, '/');
		$home_domain_data = explode("/", $home_domain);
		$home_domain = $home_domain_data[0];
		
		return $home_domain;
	}
	
	function add_http_auth_instructions() {
		global $pegasaas;
		
		if ($this->http_auth_active_and_blocking($force_check = true)) { 
			
			if ($this->http_auth_blocking['global'] == 1) {
				$htaccess_file_location = $pegasaas->get_home_path().".htaccess";

				// get the file contents
				$htaccess_file = file($htaccess_file_location);
				$htaccess_file = implode("", $htaccess_file);
				$htaccess_original = $htaccess_file;

				// strip out any of our existing instructions
				$htaccess_file = preg_replace("/(# PEGASAAS ACCELERATOR WP: SPECIAL INSTRUCTIONS START)(.*)# PEGASAAS ACCELERATOR WP: SPECIAL INSTRUCTIONS END\n\n/si", "", $htaccess_file);
				$htaccess_file = preg_replace("/(# PEGASAAS ACCELERATOR WP: ALLOW API ACCESS START)(.*)# PEGASAAS ACCELERATOR WP: ALLOW API ACCESS END\n\n/si", "", $htaccess_file);

				if (stristr($htaccess_file, "require valid-user")) {
					// attempt to add just after the "require valid user" line
					$htaccess_file = trim(preg_replace("/(require valid-user)/i", "$1\n".$this->get_http_auth_special_instructions(), $htaccess_file));
				} else {
					// otherwise prepend them to the htaccess file
					$htaccess_file = $this->get_http_auth_special_instructions().$htaccess_file;
				}
				//print "attempting to write $htaccess_file";
				// copy to a test folder
				if ($this->is_htaccess_safe($htaccess_file, "API ACCESS for / (adding)")) {
					$this->write_file($htaccess_file_location.'--backup', $htaccess_original);
					$this->write_file($htaccess_file_location, $htaccess_file);
				}

				
			} else {
				$wp_admin_htaccess_file_location = $pegasaas->get_home_path()."wp-admin/.htaccess";

				// get the file contents
				$htaccess_file = file($wp_admin_htaccess_file_location);
				$htaccess_file = implode("", $htaccess_file);
				$htaccess_original = $htaccess_file;

				// strip out any of our existing instructions
				$htaccess_file = preg_replace("/(# PEGASAAS ACCELERATOR WP: SPECIAL INSTRUCTIONS START)(.*)# PEGASAAS ACCELERATOR WP: SPECIAL INSTRUCTIONS END\n/si", "", $htaccess_file);
				$htaccess_file = preg_replace("/(# PEGASAAS ACCELERATOR WP: ALLOW API ACCESS START)(.*)# PEGASAAS ACCELERATOR WP: ALLOW API ACCESS END\n/si", "", $htaccess_file);

				if (stristr($htaccess_file, "require valid-user")) {
					// attempt to add just after the "require valid user" line
					$htaccess_file = trim(preg_replace("/(require valid-user)/i", "$1\n".$this->get_http_auth_special_instructions(), $htaccess_file));
				} else {
					// otherwise prepend them to the htaccess file
					$htaccess_file = $this->get_http_auth_special_instructions().$htaccess_file;
				}

				// copy to a test folder
				if ($this->is_htaccess_safe($htaccess_file, "API ACCESS for /wp-admin/ (adding)")) {
					$this->write_file($wp_admin_htaccess_file_location.'--backup', $htaccess_original);
					$this->write_file($wp_admin_htaccess_file_location, $htaccess_file);
				} 

			}
			// re-check the status
			$this->http_auth_active_and_blocking($force_check = true);
			
		} 
		
	}
	
	function http_auth_active_and_blocking($force_check = false) {
		global $pegasaas;
		if (sizeof($this->http_auth_blocking) > 0 && !$force_check) {
			$blocking = $this->http_auth_blocking;
		} else {
			$blocking = array("global" => 0, "admin" => 0);
			

			$blocking['global'] = $pegasaas->api->test_http_auth($pegasaas->get_home_url()."/license.txt", $force_check);
			if ($blocking['global'] == 0) {
				$blocking['admin'] = $pegasaas->api->test_http_auth(PEGASAAS_ACCELERATOR_URL."assets/css/admin.css", $force_check);
			}
			$this->http_auth_blocking = $blocking;
		}
		
		return ($blocking['admin'] == 1 || $blocking['global'] == 1); 
		

	}
	
	
	function wordfence_active_and_blocking() {
		global $pegasaas;
	
		if (class_exists('wfConfig')) {
			$wordfence_whitelist_settings = wfConfig::getJSON('whitelistPresets', array(), false);
			$ip_list 					  = $pegasaas->api->get_api_ips();
			
			if (!isset($wordfence_whitelist_settings['pegasaas'])) {
				$wordfence_whitelist_settings['pegasaas'] = array("n" => "Pegasaas Accelerator", "d" => true, "r" => $ip_list);
			} else {
				$wordfence_whitelist_settings['pegasaas']['r'] = $ip_list;
			}
			//var_dump($wordfence_whitelist_settings['pegasaas']);
			wfConfig::setJSON('whitelistPresets', $wordfence_whitelist_settings);
			 
			$wordfence_whitelisted_services = wfConfig::getJSON('whitelistedServices', array(), false);
			
			if (isset($wordfence_whitelisted_services['pegasaas'])) {
				// check legacy method
				$ips_included_in_list = true;
				foreach ($ip_list as $ip) {
				
					if (!strstr(wfConfig::get('whitelisted'), $ip)) {
						$ips_included_in_list = false;
					}
			
				}	
				if ($ips_included_in_list) {
					return false;
				}
				return !$wordfence_whitelisted_services['pegasaas'];
			} else {
				return !$wordfence_whitelist_settings['pegasaas']['d'];
			}		
		}
		
		return false;
		
	}
	
	
	
	function whitelist_pegasaas_in_wordfence() {
		global $pegasaas;
	
		if (class_exists('wfConfig')) {
			
			$wordfence_whitelisted_services = wfConfig::getJSON('whitelistedServices', array());
			$wordfence_whitelisted_services['pegasaas'] = true;
			
			wfConfig::setJSON('whitelistedServices', $wordfence_whitelisted_services);
			
		}
		
		return false;
		
	}	
	
	function is_valid_url($url) {
		
		return filter_var($url, FILTER_VALIDATE_URL);
		
	}	
	
	function get_content_folder_path() {
		// old < 1.12.9 
		//$content_folder = str_replace(home_url("", "https"), "", content_url());
		//$content_folder = str_replace(home_url("", "http"), "", $content_folder);
		
		// old < 2.3.4
		//$content_folder = str_replace("https://".$_SERVER['HTTP_HOST'], "", content_url());
		//$content_folder = str_replace("http://".$_SERVER['HTTP_HOST'], "", $content_folder);

		// new
		$url_parts = parse_url(content_url());
		$content_folder = @$url_parts['path'];
	
		
		return $content_folder;
	}
	
   /**
     * Return true/false if it is determined that this script
     * is running on a SiteGround web hosting evironment.
     *
     * @return boolean
     */		
	function is_siteground_server() {
		// this function may be disabled on some systems, which is why we suppress errors with the @
		if (function_exists('posix_uname')) {
			$server_information = @posix_uname();

			if (is_array($server_information)) {
				if (strstr($server_information['nodename'], "siteground")) {
					return true;
				}
			}
		}
		return false;
	}
	
   /**
     * Return true/false if it is determined that this script
     * is running on a WPX web hosting evironment.
     *
     * @return boolean
     */		
	static function is_wpx_server() {
		// this function may be disabled on some systems, which is why we suppress errors with the @
		if (function_exists('posix_uname')) {
			$server_information = @posix_uname();
			//var_dump($server_information);

			if (is_array($server_information)) {
				if (strstr($server_information['nodename'], "wpxhosting")) {
					return true;
				}
			}
		}
		return false;
	}	
	
	
	function is_flywheel_server() {
		return strstr($_SERVER['SERVER_SOFTWARE'], "Flywheel");
	}
	
	function is_kinsta_server() {
		return (isset($_SERVER['HTTP_X_KINSTA_EDGE_INCOMINGIP']) && $_SERVER['HTTP_X_KINSTA_EDGE_INCOMINGIP'] != "");

	}	
	
	function is_windows_iis() {
		return (strpos( $_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false);
	}
	
	function nginx_404_handling() {
		$nginx_404_handling = get_option("pegasaas_nginx_404_handling", -1);

		if ($nginx_404_handling == -1) {
			$verify_peer = get_option("pegasaas_accelerator_curl_ssl_verify", 0) == 1;
			$args['ssl_verify'] = $verify_peer;
			$args['sslverify'] = $verify_peer;
			$args['method'] = "GET";
			$args['timeout'] = 10;
			$args['blocking'] = true;


			$response = wp_remote_request(get_home_url()."/wp-content/test-for-nginx-404.css", $args);

			if (is_a($response, "WP_Error")) {
				$error = $response->get_error_message();


			} else {
				$http_response = $response['http_response'];
				$response =  $http_response->get_data();
				$response_obj = $http_response->get_response_object();
				$httpcode = $response_obj->status_code;
			}


			if ($httpcode == 404 && (strstr($response, "nginx") || strstr($_SERVER['SERVER_SOFTWARE'], "nginx"))) {
				update_option("pegasaas_nginx_404_handling", true);
				return true;
			} else {
				update_option("pegasaas_nginx_404_handling", false);
				return false;
			}
		} else {
			return $nginx_404_handling;
		}
	}
	
	
	static function get_object_meta($object_path, $meta_type, $use_cache = true) {
		global $pegasaas;
		global $proxy_buffer;
		
		$proxy_buffer .= "<!-- LOADING $object_path for $meta_type -->\n";

		if ($meta_type == "accelerator_overrides") {
			if ($use_cache == true) {
				if (array_key_exists("accelerator_overrides", $pegasaas->utils->data_cache) && array_key_exists($object_path, $pegasaas->utils->data_cache["accelerator_overrides"]) && $pegasaas->utils->data_cache["accelerator_overrides"]["{$object_path}"]) {
					$proxy_buffer .= "<!-- using cache via $object_path for $meta_type -->\n";
					$proxy_buffer .= "<!--  $object_path for $meta_type  is ".print_r($pegasaas->utils->data_cache["accelerator_overrides"]["{$object_path}"], true) ." -->\n";

					return $pegasaas->utils->data_cache["accelerator_overrides"]["{$object_path}"];
				}
			} 

			if (!array_key_exists("accelerator_overrides", $pegasaas->utils->data_cache) || sizeof($pegasaas->utils->data_cache["accelerator_overrides"]) == 0 || !$use_cache) {
				// preload all
				$proxy_buffer .= "<!-- loading cache via $object_path for $meta_type -->\n";
				$results = $pegasaas->db->get_results("pegasaas_page_config");

				foreach ($results as $record) {

				  $value = $record->settings;
				  $object_path_rel = $record->resource_id;



				  $pegasaas->utils->data_cache["accelerator_overrides"]["{$object_path_rel}"] = json_decode($value, true);
				  $proxy_buffer .= "<!--  $object_path_rel for $meta_type  is ".print_r($pegasaas->utils->data_cache["accelerator_overrides"]["{$object_path_rel}"], true) ." -->\n";

				}

			} else {
				$proxy_buffer .= "<!-- setting cache element to blank because use_cache = {$use_cache} -->\n";
				$pegasaas->utils->data_cache["accelerator_overrides"]["{$object_path}"] = array();
			}
		
			if (isset($pegasaas->utils->data_cache["accelerator_overrides"]["{$object_path}"])) {
				return $pegasaas->utils->data_cache["accelerator_overrides"]["{$object_path}"];
			} else {
				return NULL;
			}


			
			
			// otherwise, attempt to see if there is data in the wp_options table
		} else if ($meta_type == "cached_html") {
			
			// attempt to fetch a record from the new database table
			$record = $pegasaas->db->get_single_record("pegasaas_page_cache", array("resource_id" => $object_path));
			$value = $record->data;
			
			// if we got a record, then return the value
			if ($value != "") {
				return json_decode($value, true);
			}
			
			
		} 
		
		// otherwise, attempt to see if there is data in the wp_options table
		$field_name = "pegasaas_{$meta_type}_{$object_path}";
		return get_option($field_name, array());
	}
	
	function update_object_meta($object_path, $meta_type, $meta_data) {
		global $pegasaas;
		global $proxy_buffer;
		
		$field_name = "pegasaas_{$meta_type}_{$object_path}";
		
		// brandon it seems as though the settings in the db are not updating correctly upon first initialization
		
		if ($meta_type == "accelerator_overrides") {
		
			$debug_backtrace = debug_backtrace();

				$calling_file = explode("/", $debug_backtrace[0]['file']);
				$calling_file = array_pop($calling_file);
				$calling_function = $debug_backtrace[1]['function'];
				$calling_class = $debug_backtrace[1]['class'];
				$calling_line = $debug_backtrace[0]['line'];
			

			
			$debug_backtrace_string = "{$calling_file} <span class='trace-arrow'>&raquo;</span> ".($calling_class != "" ? $calling_class."->" : "")."{$calling_function}() line #{$calling_line}";
		


			$data = json_encode($meta_data);
			$pegasaas->db->update_record("pegasaas_page_config", array("resource_id" => $object_path, "settings" => $data, "last_updated" => date("Y-m-d H:i:s")));
		//	$proxy_buffer .= "<!-- just updated {$object_path} with settings: ".print_r($data, true)." -->\n";
		//	$proxy_buffer .= "<!-- backtrace  {$debug_backtrace_string} -->\n";
		//	$pegasaas->utils->debug(true, "Backtrce {$debug_backtrace_string}\n");
			$pegasaas->utils->log("Updating Record 'pegasaas_page_config' {$object_path}' with data: $data via {$debug_backtrace_string}", 'critical');

			delete_option($field_name); // remove the old native option
		
		} else if ($meta_type == "cached_html") {
			$data = json_encode($meta_data);
			$pegasaas->db->update_record("pegasaas_page_cache", array("resource_id" => $object_path, "data" => $data));
			delete_option($field_name); // remove the old native option
			
		} else {
			$field_name = "pegasaas_{$meta_type}_{$object_path}";
			// set autoload to 'no' (false) Feb 7, 2019, to reduce memory overhead
			// may cause execution time, although nothing significant found when benchmarking
			update_option($field_name, $meta_data, false);
		}
	}

	
	function get_accelerated_posts_count($post_type = "") {
		global $pegasaas;
		$count = 0;
		$all_pages_and_posts = PegasaasUtils::get_all_pages_and_posts();
		foreach ($all_pages_and_posts as $post) {
			if ($post->post_type == $post_type && $post->accelerated) {
				$count++;
			}
		}
		return $count;
	}
	
	
	function get_post_slug_from_resource_id($resource_id) {
		$all_pages_and_posts = self::get_all_pages_and_posts();
		foreach ($all_pages_and_posts as $post) {
			if ($post->resource_id == $resource_id) {
				return $post->slug;
			}
		}
	}

	function get_post_obj_from_resource_id($resource_id) {
		$all_pages_and_posts = self::get_all_pages_and_posts();
		foreach ($all_pages_and_posts as $post) {
			if ($post->resource_id == $resource_id) {
				return $post;
			}
		}
	}	
	
	static function get_post_type_object($post_type) {
		global $wpdb;
		$sql = "SELECT MAX(ID) as ID, post_type, post_name FROM {$wpdb->posts} WHERE post_type='$post_type' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 1";
		$row = $wpdb->get_results($sql);	
	
		return $row[0];
	}
	
	function get_wp_location() {
		$url_parts = parse_url(get_site_url());
		//unset($url_parts['port']);
		return @$url_parts['path'];

		/*
		$portless = $url_parts['scheme']."://".$url_parts['host'].$url_parts['path'];

		$location = str_replace(array("https://", "http://"), array("",""), get_site_url());
		$location = str_replace($_SERVER['HTTP_HOST'], "", $location);
		list( $location ) = explode( '?', $location );
		return $location;  
		*/
	}
	
	
    /**
     * Gets the folder/directory that the website is loaded in, relative to
     * the domain name
     *
     * @return string A string containing the path to the home folder, relative to the root, without any trailing slash
     */	
	function get_home_location() {
		global $pegasaas;
		$url_parts = parse_url($pegasaas->get_home_url());
		//unset($url_parts['port']);
		return @$url_parts['path'];
		
		
		$location = str_replace(array("https://", "http://"), array("",""), $pegasaas->get_home_url());
		list( $location ) = explode( '?', $location );
		
		$location = rtrim($location, "/");
		
		$location = str_replace($_SERVER['HTTP_HOST'], "", $location);

		return $location;  		
		
	}
	
	
	function is_htaccess_safe($htaccess_instructions, $section = "") {
		global $pegasaas;
		$passed = false;
		$override_test = get_option("pegasaas_force_htaccess_write", false);
		if ($override_test) {
			return true;
		}
	
		$test_location = PEGASAAS_ACCELERATOR_DIR."htaccess-test/";
		if (!is_dir($test_location)) {
			$pegasaas->cache->mkdirtree($test_location, 0755, true);
		}
	
		//$htaccess_instructions = "f\n".$htaccess_instructions;
		// write a test file
		$this->write_file($test_location."index.html", "PASSED");

		// write the .htaccess
		
		$this->write_file($test_location.".htaccess", $htaccess_instructions);

		$response = wp_remote_request(PEGASAAS_ACCELERATOR_URL."htaccess-test/");
		
		// test for error
		
		if (is_a($response, "WP_Error")) {
			
			$passed = false;
			$pegasaas->utils->log("htaccess Test ({$section}): have a WP_Error -- ".$response->get_error_message() , "critical");
			$pegasaas->api->notify_of_bad_htaccess($htaccess_instructions, $response->get_error_message());
		
		} else {
			$http_response = $response['http_response'];
			$response_obj = $http_response->get_response_object();
			$httpcode = $response_obj->status_code;
			$pegasaas->utils->log("htaccess Test ({$section}): $httpcode", "critical");
			
			if ($httpcode == 200 || $httpcode == 201  || $httpcode == 401) {
				$passed = true;
			} else {
				$pegasaas->api->notify_of_bad_htaccess($htaccess_instructions, $httpcode);

			}
		
		
		}	
	
		
		return $passed;

		
	}
	
	function permalinks_active() { 
		global $pegasaas;
		
		global $wp_rewrite;
		
		
		if ( get_option('permalink_structure') ) {
			$permalink_structure = get_option('permalink_structure');
			
			// bypass the .htaccess check if the server is informing us that it is nginx.
			// note: this may be an nginx server which proxies requests to an Apache server, in which case
			// we may need to test for the existance of an .htaccess file, in the future, however for now this test should resolve
			// the majority of issues with ngnix systems failing this step.
			if (strstr($_SERVER['SERVER_SOFTWARE'], "nginx")) {
				return true;
			}
			
			if ($pegasaas->utils->is_kinsta_server()) {
				return true;
			}
			if ($pegasaas->utils->is_windows_iis()) {
				return true;
			}
			
			$htaccess_file_location = $pegasaas->get_home_path().".htaccess";
		
			
			
			$exists = file_exists($htaccess_file_location);
			
			// attempt to create it
			if (!$exists) {
			
				
				//$wp_rewrite->set_permalink_structure( $permalink_structure );
				flush_rewrite_rules();
				
				if (PegasaasAccelerator::$settings['status'] == 1) {
					$pegasaas->set_gzip(PegasaasAccelerator::$settings['settings']['gzip_compression']['status']);
					$pegasaas->set_browser_caching(PegasaasAccelerator::$settings['settings']['browser_caching']['status']);
					$pegasaas->set_benchmarker(PegasaasAccelerator::$settings['status']);
					$pegasaas->set_caching(PegasaasAccelerator::$settings['settings']['page_caching']['status']);					
				}
				
			}
			$exists = file_exists($htaccess_file_location);
			
			
			return $exists;
		} else {
			return false;
		}
	}
	
	function get_http_host() {
		$host = explode(":", $_SERVER['HTTP_HOST']);
		return $host[0];
	}
	function get_http_host_alt() {
		$home = content_url();
		$home_data = parse_url($home);
		
		return $home_data['host'];
	}
	
	static function get_permalink() {		
		global $pegasaas;
	
		
		$request_uri = $_SERVER['REQUEST_URI'];
		
		// convert to hex otherwise the parse_url will result in null
		$request_uri = str_replace(":", "%3A", $request_uri); 
		
		// parse the url so we can grab the path portion only
		$request_data = parse_url($request_uri);
		
		// convert url_encoded characters to native characters
		// this is required in order to have a resource_id that can be mapped
		// on the file system
		$req_uri = urldecode(@$request_data['path']);
		
		if ($req_uri == "") {
			
			$req_uri = "/";
		}
		
		// support for WPML
		if (self::wpml_multi_domains_active()) {
		
			$website_address = $pegasaas->get_home_url();
			$address_details = parse_url($website_address);
			$this_address = $address_details['scheme']."://".$_SERVER['HTTP_HOST'];
			
			if ($website_address != $this_address || self::uses_domain_specific_handling()){
				$req_uri = $this_address.$req_uri;
			}
		} else if (isset($request_data['query'])) {
		
			$query_string = $request_data['query'];
			parse_str($query_string, $query_args);

			if (isset($query_args['lang']) && $query_args['lang'] != "") {
			   $req_uri .= "?lang=".$query_args['lang']; 
			}
		} 
		
		
		return $req_uri;
	}
	
	
	
	function get_site_path() {
		$home 		= set_url_scheme( get_option( 'home' ), 'http' );
	    $siteurl 	= set_url_scheme( get_option( 'siteurl' ), 'http' );

		return str_replace($home, "", $siteurl);
	}	
	
	

	function strip_domain($url) {
		$path = str_replace( set_url_scheme( get_option( 'home' ), 'http' ), "", $url);
		$path = str_replace( set_url_scheme( get_option( 'home' ), 'https' ), "", $path);
		return $path;
		
	}
	
	function log_local_asset($original_file_name, $optimized_file_name, $status = 0) {
		global $pegasaas;
		
		$original_file_name = $this->strip_domain($original_file_name);
		$optimized_file_name = $this->strip_domain($optimized_file_name);
		if (file_exists($pegasaas->get_home_path().ltrim($original_file_name, '/'))) {
			$original_file_size = filesize($pegasaas->get_home_path().ltrim($original_file_name, '/'));
		} else {
			$original_file_size = 0;
		}
		if (file_exists($pegasaas->get_home_path().ltrim($optimized_file_name, '/'))) {
			$optimized_file_size = filesize($pegasaas->get_home_path().ltrim($optimized_file_name, '/'));
		} else {
			$optimized_file_size = 0;
		}
		
		
		$asset_type_image = array("jpg", "jpeg", "png", "tiff", "svg", "ico", "gif");
		$asset_type_stylesheet   = array("css");
		$asset_type_font  = array("woff", "woff2", "eot", "ttf");
		$asset_type_script = array("js");
		
		$existing_record = $pegasaas->db->get_single_record("pegasaas_static_asset", array("original_file_name" => $original_file_name));
	
		$file_extension = PegasaasUtils::get_file_extension($original_file_name);
		
		if (in_array($file_extension, $asset_type_image)) {
			$asset_type = "image";
		} else if (in_array($file_extension, $asset_type_stylesheet)) {
			$asset_type = "stylesheet";
		} else if (in_array($file_extension, $asset_type_font)) {
			$asset_type = "font";
		} else if (in_array($file_extension, $asset_type_script)) {
			$asset_type = "script";
		} else {
			$asset_type = "misc";
		}
		
		
		$fields = array("original_file_name" => $original_file_name, 
						
						"optimized_file_name" => $optimized_file_name, 
						"asset_type" => $asset_type, 
						"when_created" => date("Y-m-d H:i:s"), 
						"original_file_size" => $original_file_size, 
						"optimized_file_size" => $optimized_file_size, 
						"status" => $status );
		
		if ($existing_record->asset_id != "") {
			$fields['asset_id'] = $existing_record->asset_id;
		} 
		$pegasaas->db->update_record("pegasaas_static_asset", $fields);
		
	}
	
	
	function get_file($file, &$response_code = "") {
		if (strpos($file, "//") === 0) {
			if ($_SERVER['HTTPS'] == "on") {
				$file = "https:".$file;
			} else {
				$file = "http:".$file;	
			}	
		} else if (strpos($file, "/") === 0) {
			$file 	= $_SERVER['DOCUMENT_ROOT'].$file;
			$is_local_file = true;
		} else if (strpos($file, "http://") === false && strpos($file, "https://") === false) {
			// first condition the url to map back any ../ references
			$filePath = ltrim($file, "/");
			$filePath = explode("?", $filePath);
			$filePath = $filePath[0];
			$filePathPieces = explode("/", $filePath);
			$fPathBreak = false;
			$fPathCount = 0;
			foreach ($filePathPieces as $fPath) {
				if ($fPathBreak) {
					break;
				}
				if ($fPath == "..") {
					$fPathCount++;
				  
				} else {
					$fPathBreak = true;
				}
			}
			$file = str_replace("../", "", $file);
			
			// determine path to file
			$folderPath = explode("/", $_SERVER['REQUEST_URI']);
			array_pop($folderPath);
			$folderPath = implode("/", $folderPath);
			$folderPath = explode("?", $folderPath);
			$folderPath = ltrim($folderPath[0], "/");
			$folderPathPieces = explode("/", $folderPath);
			$folderDepth = count($folderPathPieces);
			$folderPrepend = $_SERVER['DOCUMENT_ROOT']."/";
			for ($depth = 0; $depth + $fPathCount < $folderDepth; $depth++) {
				if ($folderPathPieces["$depth"] != "") { 
					$folderPrepend .= $folderPathPieces["$depth"]."/";
				}
			}		
			$file = $folderPrepend.$file;  
			$is_local_file = true;
		}

		if ($is_local_file && file_get_contents(__FILE__)) {

			$output = @file_get_contents($file);
			
		} else if (file_get_contents(__FILE__) && ini_get('allow_url_fopen') && false) {


			$output = @file_get_contents($file);
			
		} else {
	
			$args = array();
			$verify_peer = get_option("pegasaas_accelerator_curl_ssl_verify", 0) == 1;
			// the lets encrypt certificate is failing on some servers due to the version of openssl that is installed 
			$verify_peer = false; 

			// will have to revert this in time, for security
			$args['ssl_verify'] = $verify_peer;
			$args['sslverify'] = $verify_peer; // new setting
	
			$args['method'] = "GET";  
	
			 
			if (isset($_GET['test-args']) && $_GET['test-args'] == 1) {
				print "<pre>";  
				var_dump($args);
				print "</pre>";
				exit;
			}
		
			$response = wp_remote_request($file, $args);
		
			if (is_a($response, "WP_Error")) {
			//	print "<pre>";
				//var_dump($response);
				//print "</pre>";  
				$error = $response->get_error_message();
				$this->log("Error fetching file ($file): $error", "html_conditioning");
				print "<!-- Got an error fetching \"$file\": {$error} -->";
				
				$response_code = -1;
				return "";
			} else {
				$http_response 	= $response['http_response'];

				$response 		=  $http_response->get_data();
				
				$response_obj 	= $http_response->get_response_object();
				$httpcode 		= $response_obj->status_code;
				$headers 		= $response_obj->headers;
				if ($headers['x-pegasaas-error-code'] != "") {
					$response_code = $headers['x-pegasaas-error-code'];
				} else {
					$response_code = $httpcode;
				}
			}
			$output = $response;
	
			if ($httpcode == 404 || $httpcode == 403 || $httpcode == 500) {
			  //	$output = "";  // disabled this April 1, 2019 
			}

		}
		
		return $output;
	}
	
	
	static 	function get_file_extension($file_name) {
		if (strpos($file_name, "?") !== false) {
			$file_name = substr($file_name, 0, strpos($file_name, "?"));
		}
		
		
		return substr(strrchr($file_name,'.'),1);
	}
	
	
	// method to get a file contents if the server does not allow_url_fopen by using curl instead
	static function get_file_contents($file) {
		global $pegasaas;
		

		if (strpos($file, "//") === 0) {
			if ($_SERVER['HTTPS'] == "on") {
				$file = "https:".$file;
			} else {
				$file = "http:".$file;	
			}	
		} else if (strpos($file, "/") === 0) {
			$file 	= $_SERVER['DOCUMENT_ROOT'].$file;
			$is_local_file = true;
		} else if (strpos($file, "http://") === false && strpos($file, "https://") === false) {
			// first condition the url to map back any ../ references
			$filePath = ltrim($file, "/");
			$filePath = explode("?", $filePath);
			$filePath = $filePath[0];
			$filePathPieces = explode("/", $filePath);
			$fPathBreak = false;
			$fPathCount = 0;
			foreach ($filePathPieces as $fPath) {
				if ($fPathBreak) {
					break;
				}
				if ($fPath == "..") {
					$fPathCount++;
				  
				} else {
					$fPathBreak = true;
				}
			}
			$file = str_replace("../", "", $file);
			
			// determine path to file
			$folderPath = explode("/", $_SERVER['REQUEST_URI']);
			array_pop($folderPath);
			$folderPath = implode("/", $folderPath);
			$folderPath = explode("?", $folderPath);
			$folderPath = ltrim($folderPath[0], "/");
			$folderPathPieces = explode("/", $folderPath);
			$folderDepth = count($folderPathPieces);
			$folderPrepend = $_SERVER['DOCUMENT_ROOT']."/";
			for ($depth = 0; $depth + $fPathCount < $folderDepth; $depth++) {
				if ($folderPathPieces["$depth"] != "") { 
					$folderPrepend .= $folderPathPieces["$depth"]."/";
				}
			}		
			$file = $folderPrepend.$file;  
			$is_local_file = true;
		}

		if ($is_local_file && file_get_contents(__FILE__)) {
			$output = @file_get_contents($file);
			if ($output == "") {
				$output = "/* '$file' Not Found */";
			}
		} else if (file_get_contents(__FILE__) && ini_get('allow_url_fopen') && false) {


			$output = @file_get_contents($file);
			
			if ($output == "") {
				$output = "/* '$file' Not Found */";
			} else {
				$output = "/* via fopen */ ".$output;

			}
		} else {
			$args = array();
			$verify_peer = get_option("pegasaas_accelerator_curl_ssl_verify", 0) == 1;
			$verify_peer = false; // had to be overridden due to Let's Encrypt sunsetting one of their certificates
			$args['ssl_verify'] = $verify_peer;
			$args['sslverify'] = $verify_peer;
			$args['method'] = "GET";
			$args['timeout'] = 10;
	

			$response = wp_remote_request($file, $args);
		
			if (is_a($response, "WP_Error")) {
				$error = $response->get_error_message();
				$output = "";
			} else {
				$http_response = $response['http_response'];
				$output =  $http_response->get_data();
				$response_obj = $http_response->get_response_object();
				$httpcode = $response->status_code;
			}
			
		
			
			if ($httpcode == 404 || $httpcode == 403 || $httpcode == 500) {
				$output = "";
			}
			
			$output =	"/* file: $file */\n"."/* via wp_remote_request */ ".$output;

		}

		if (strstr($output, 'var ThriveApp={}')) {
		
			$output .= "function pegasaas_all_styles_loaded() {
			var stylesheets_loaded = true;
				jQuery('link[rel=stylesheet][title=pegasaas-deferred-css]').each(function (i, ele) {
 					if (jQuery(this).attr('data-loaded') != 'loaded') {
						stylesheets_loaded = false;
					}
 				});
				return stylesheets_loaded;
			}
			
			ThriveApp.grid_layout_original=ThriveApp.grid_layout;
			ThriveApp.grid_layout=function(a,b){
				if (pegasaas_all_styles_loaded()) {  
					setTimeout('ThriveApp.grid_layout_original(\"'+a+'\",\"'+b+'\")', 1000 );
				} else {
					setTimeout('ThriveApp.grid_layout(\"'+a+'\",\"'+b+'\")', 1000 );

				}
			
			};\n";
		}		
		
		
		$output = "try {\n {$output} \n} catch (err) { console.log('Caught error: ' + err); }";
	
		return $output;		
	}	
	
	
	function strip_query_string($url) {
		if (strstr($url, "?")) {
			$url = substr($url, 0, strrpos($url, "?"));
		}
		return $url;
	}
	
	function map_resource_path($file, $web_root = false) {
		// first condition the url to map back any ../ references
		$filePath = ltrim($file, "/");
		$filePath = explode("?", $filePath);
		$filePath = $filePath[0];
		$filePathPieces = explode("/", $filePath);
		$fPathBreak = false;
		$fPathCount = 0;
		foreach ($filePathPieces as $fPath) {
			if ($fPathBreak) {
				break;
			}
			if ($fPath == "..") {
				$fPathCount++;
		  
			} else {
				$fPathBreak = true;
			}
		}
		
		$file = str_replace("../", "", $file);
		
		// determine path to file
		$folderPath = explode("/", $_SERVER['REQUEST_URI']);
		array_pop($folderPath);
			 
		$folderPath 		= implode("/", $folderPath);
		$folderPath 		= explode("?", $folderPath);
		$folderPath 		= ltrim($folderPath[0], "/");
		$folderPathPieces 	= explode("/", $folderPath);
		$folderDepth 		= count($folderPathPieces);  	
		$folderPrepend 		= $_SERVER['DOCUMENT_ROOT']."/";
		
		for ($depth = 0 ; $depth + $fPathCount < $folderDepth; $depth++) {
			if ($folderPathPieces["$depth"] != "") { 
				$folderPrepend .= $folderPathPieces["$depth"]."/";
			}
		}		
		
		$file = $folderPrepend.$file;  
		if ($web_root) {
			$file = str_replace($_SERVER['DOCUMENT_ROOT'], "", $file);
		}
		
		return $file;
	}
	
	function has_acceleration_enabled($resource_id, $is_cat = false) {
		global $pegasaas;
		
		if ($resource_id == "/" && (get_option("show_on_front") == "posts" || get_option("show_on_front") == "layout")) {
			return PegasaasAccelerator::$settings['settings']['blog']['home_page_accelerated'] == 1;
		} else if ($is_cat) {
			return PegasaasAccelerator::$settings['settings']['blog']['categories_accelerated'] == 1;
		}  else {
			$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides");
	
			if (isset($page_level_settings['accelerated']) && ($page_level_settings['accelerated'] === true || $page_level_settings['accelerated'] == 1)) {
				return true;	
			} else {
				return false;
			}
		}
		
	}
	
	function write_file($file_name, $contents) { 
		// obtain the write state, so that we can return it back if it was set to not writable
		$writable 	  = is_writable($file_name);
		$file_existed = file_exists($file_name);
		
		// if the file is not initially writable then attempt to change the permissions
		if (!$writable && $file_existed) {
			@chmod($file_name, 0644);
		}
		
		$fp = fopen($file_name, "w+");
		if ($fp) {
			fwrite($fp, $contents);
			fclose($fp);
		}

		// if the file was not initially writable, then change the permissions back to not-writable
		if (!$writable && $file_existed) {
			chmod($file_name, 0444);
		}		
	}
	static function uses_domain_specific_handling() {
		if (!isset(self::$use_domain_specific_handling)) {
			if (PegasaasUtils::does_plugin_exists_and_active("woocommerce-multilingual") && PegasaasUtils::does_plugin_exists_and_active("wpml")) {
				//print "it exists";
				return true;
			} else {
				return false;
				
			}
		} else {
			return self::$use_domain_specific_handling;
		}
		
	}
	static function get_object_id($id = NULL) {
		global $pegasaas;
		
		
		$wpml_multi_domains_active = PegasaasUtils::wpml_multi_domains_active();

		if ($wpml_multi_domains_active) {
			
			global $sitepress;
			$current_language = $sitepress->get_current_language();
			$default_language = $sitepress->get_default_language();
			$wpml_domains = wpml_get_setting( 'language_domains' );
			$wpml_domains["$default_language"] = PegasaasUtils::get_home_domain();
		}
		
		$use_domain_specific_handling = PegasaasUtils::uses_domain_specific_handling();

		if ($use_domain_specific_handling) {
			//print "domain speciic handling enabled";
		}
		
		
		if ($id == NULL && $id != '0') {

			$location = $pegasaas->utils->get_home_location();

			// get the permalink from the WP system
			$object_id = PegasaasUtils::get_permalink();
			$object_url_data = parse_url($object_id);
			
			if (!$use_domain_specific_handling) {
				$object_id = str_replace($object_url_data['scheme']."://".$object_url_data['host'], "", $object_id);
				$object_id = str_replace($pegasaas->get_home_url('', 'https')."/".$location, "/", $object_id);
				$object_id = str_replace($pegasaas->get_home_url('', 'http')."/".$location, "/", $object_id);
			} 
			
			// condition the object id in the event that the URL has a ' or " character that is not automatically transformed by the server
			$object_id = str_replace("'", "%27", $object_id);
			$object_id = str_replace('"', "%22", $object_id);
			
			
			// strip the protocol and domain so that all we are left with is the path to the file
			// $object_id = str_replace($location, "", $object_id);

			// always strip the protocol out -- in the event that the domain is not the home-domain (as is used in wpml plugin)
			// object id will end up looking like: /www.anotherdomain.com/a-page-name/
			$object_id = str_replace("http://", "/", $object_id);
			$object_id = str_replace("https://", "/", $object_id);
			
			// assign the resource_id as /mydomain.com/ in the event that this is a multi-domain installation
			if ($object_id == "/" && PegasaasUtils::get_home_domain() != $object_url_data['host']) {
				$object_id = "/".$object_url_data['host']."/";
			}
			
			$object_id = rtrim($object_id, '/').'/'; // ensure there is always a trailing slash otherwise the file mapping can be messed up
			
			return $object_id;
		} else if (strpos($id, "woocommerce_product_category__") !== false) {
			
			$id = str_replace("woocommerce_product_category__", "", $id);
			
			$category_link = get_category_link($id);
			$slug = str_replace($pegasaas->get_home_url('', 'https'), "", $category_link);
			$slug = str_replace($pegasaas->get_home_url('', 'http'), "", $slug);
			
			
			$object_id = urldecode($slug);
			$object_id = rtrim($object_id, '/').'/'; // ensure there is always a trailing slash otherwise the file mapping can be messed up

			// always strip the protocol out -- in the event that the domain is not the home-domain (as is used in wpml plugin)
			// object id will end up looking like: /www.anotherdomain.com/a-page-name/
			$object_id = str_replace("http://", "/", $object_id);
			$object_id = str_replace("https://", "/", $object_id);
			
			
			
			return $object_id;		
		} else if (strpos($id, "category__") !== false) {
		
			$id = str_replace("category__", "", $id);
			
			$category_link = get_category_link($id);
			$slug = str_replace($pegasaas->get_home_url('', 'https'), "", $category_link);
			$slug = str_replace($pegasaas->get_home_url('', 'http'), "", $slug);
			
			
			$object_id = urldecode($slug);
			$object_id = rtrim($object_id, '/').'/'; // ensure there is always a trailing slash otherwise the file mapping can be messed up

			
			// always strip the protocol out -- in the event that the domain is not the home-domain (as is used in wpml plugin)
			// object id will end up looking like: /www.anotherdomain.com/a-page-name/
			$object_id = str_replace("http://", "/", $object_id);
			$object_id = str_replace("https://", "/", $object_id);
			
			
			return $object_id; 
			
		
		} else if (strpos($id, "woocommerce_product_tag__") !== false) {
			$id = str_replace("woocommerce_product_tag__", "", $id);
			
			$category_link = get_category_link($id);
			$slug = str_replace($pegasaas->get_home_url('', 'https'), "", $category_link);
			$slug = str_replace($pegasaas->get_home_url('', 'http'), "", $slug);
			
			
			$object_id = urldecode($slug);
			$object_id = rtrim($object_id, '/').'/'; // ensure there is always a trailing slash otherwise the file mapping can be messed up

			// always strip the protocol out -- in the event that the domain is not the home-domain (as is used in wpml plugin)
			// object id will end up looking like: /www.anotherdomain.com/a-page-name/
			$object_id = str_replace("http://", "/", $object_id);
			$object_id = str_replace("https://", "/", $object_id);
			
			
			return $object_id;			
		} else {
			
			
			$permalink = get_permalink($id);

			if ($wpml_multi_domains_active) {
				$post_type     = get_post_type( $id );
				$post_language = $sitepress->get_language_for_element( $id, 'post_' . $post_type );

				if ($post_language != $current_language) {
					$permalink_url_data = parse_url($permalink);
					$permalink_scheme = $permalink_url_data['scheme'];
					if ($permalink_scheme) {
						$permalink = str_replace($permalink_scheme."://".$wpml_domains["{$current_language}"], $permalink_scheme."://".$wpml_domains["{$post_language}"], $permalink);
					}
				}
			}
			
			$url_data = parse_url($permalink);
			$path 	  = $url_data['path'];
			
			// then this is probably a preview url, we should manually build the resource id
			if ($path == "/" && strstr($url_data['query'], "page_id=")) {
				
				$post = get_post($id);
				$home_dir = $pegasaas->get_home_dir();
				
				
				$object_id = "{$home_dir}/{$post->post_name}/";
				
			
			} else {
				
				$slug = $permalink;
				if (!$use_domain_specific_handling) {
					$slug = str_replace($pegasaas->get_home_url('', 'https'), "", $slug);
					$slug = str_replace($pegasaas->get_home_url('', 'http'), "", $slug);
				}

				if ($slug == "") {
					$slug = "/";
				}
				
				$object_id = urldecode($slug);
				
				$object_id = rtrim($slug, '/').'/'; // ensure there is always a trailing slash otherwise the file mapping can be messed up
			}

			
			if (!$use_domain_specific_handling) {
				$object_url_data = parse_url($object_id);
				
				$object_id = str_replace($object_url_data['scheme']."://".$object_url_data['host'], "", $object_id);
				$object_id = urldecode($object_id);
			}
			
		
			// condition the object id in the event that the URL has a ' or " character that is not automatically transformed by the server
			$object_id = str_replace("'", "%27", $object_id);
			$object_id = str_replace('"', "%22", $object_id);
			
			// always strip the protocol out -- in the event that the domain is not the home-domain (as is used in wpml plugin)
			// object id will end up looking like: /www.anotherdomain.com/a-page-name/
			$object_id = str_replace("http://", "/", $object_id);
			$object_id = str_replace("https://", "/", $object_id);
			
			return $object_id;
		}
	}
	
	
	function delete_object_meta($object_path, $meta_type) {
		global $pegasaas;
		
		$field_name = "pegasaas_{$meta_type}_{$object_path}";
		
		if ($meta_type == "accelerator_overrides") {
			
			$pegasaas->db->delete("pegasaas_page_config", array("resource_id" => $object_path));
			delete_option($field_name); // remove the old native option
		
		} else if ($meta_type == "cached_html") {
			
			$pegasaas->db->delete("pegasaas_page_cache", array("resource_id" => $object_path));
			delete_option($field_name); // remove the old native option
			
		} else {		
		
			delete_option($field_name);		
		}
	}
	
	

	
	
	function require_wp_include($include) {
		$site_path	= ltrim($this->get_site_path(), '/');
		if ($include == "" || $include == "pluggable") {
			// load the functions found in "pluggable.php" --- required for "is_user_logged_in()"
			if (file_exists($site_path."/wp-includes/pluggable.php")) {
				require_once($site_path."/wp-includes/pluggable.php");
			} else	if (file_exists("wp-includes/pluggable.php")) {
				require_once("wp-includes/pluggable.php");
			}
		} else if ($include == "rewrite") {
			if (file_exists($site_path."/wp-includes/rewrite.php")) {
				require_once($site_path."/wp-includes/rewrite.php");
			} else	if (file_exists("wp-includes/rewrite.php")) {
				require_once("wp-includes/rewrite.php");
			}	
		}
	}	
	
	static function rotate_log_files() {
		global $test_debug;
		
		$log_files_folder = PEGASAAS_ACCELERATOR_DIR."/logs/";
		
		if (!is_dir($log_files_folder)) {
			@mkdir($log_files_folder, 0755);
			
		}
		
		if (file_exists($log_files_folder."log.5.html")) {
			@unlink($log_files_folder."log.5.html");
		}

		if (file_exists($log_files_folder."log-detailed.5.html")) {
			@unlink($log_files_folder."log-detailed.5.html");
		}		
		
		for ($log_number = 5; $log_number--; $log_number > 0) {
			$new_number = $log_number+1;
			if (file_exists($log_files_folder."log.{$log_number}.html")) {
				if ($test_debug) {
					print "<li>Renaming log.{$log_number}.html to log.{$new_number}.html</li>";
				}
			
				rename($log_files_folder."log.{$log_number}.html", $log_files_folder."log.{$new_number}.html");
			}
			if (file_exists($log_files_folder."log-detailed.{$log_number}.html")) {
				if ($test_debug) {
					print "<li>Renaming log-detailed.{$log_number}.html to log-detailed.{$new_number}.html</li>";
				}
			
				rename($log_files_folder."log-detailed.{$log_number}.html", $log_files_folder."log-detailed.{$new_number}.html");
			}			
		}
		if ($test_debug) {
			print "<li>Renaming index.html to log.1.html</li>";
		}
		
		@rename(PEGASAAS_ACCELERATOR_DIR."/logs/index.html", $log_files_folder."log.1.html");	
		if ($test_debug) {
			print "<li>Renaming log-critical.html to log-critical.1.html</li>";
		}
		
		@rename(PEGASAAS_ACCELERATOR_DIR."/logs/log-detailed.html", $log_files_folder."log-detailed.1.html");	

	}
	
	
	static function log($message, $subsystem = "", $detailed = false) {
		global $pegasaas;
		
		
		$log_this = false; 
		$log_buffer = "regular"; 
		if ($detailed) {
			$log_buffer = "detailed";
			$log_this = true;
		}

		if ($subsystem == "") {
			$log_this = true;
			$subsystem = "GLOBAL";
		} else if ($subsystem == "error") {
			$log_this = true;
			$subsystem = "ERROR";

		} else if ($subsystem == "critical") {
			$log_this = true;
			$subsystem = "CRITICAL";

	
		} else if (is_array(PegasaasAccelerator::$settings['settings']['logging']) && array_key_exists("log_{$subsystem}", PegasaasAccelerator::$settings['settings']['logging']) && PegasaasAccelerator::$settings['settings']['logging']["log_{$subsystem}"] == 1) {
			$log_this = true;
		}

		$url = $_SERVER['REQUEST_URI'];
		if (strstr($url, "/wp-cron.php")) {
			$page_title = "WP Cron Task";
		} else if (strstr($url, "/admin-ajax.php")) {
			$page_title = "WP Admin Ajax Execution";
			
			if ($_POST['action'] == "pegasaas_process_pagespeed_score_request") {
				$page_title .= ": Process PageSpeed Score Request";
			} else if ($_POST['action'] == "pegasaas_process_pagespeed_benchmark_score_request") {
				$page_title .= ": Process Baseline PageSpeed Score Request";
			} else if ($_POST['action'] == "pegasaas_process_critical_css_request") {
				$page_title .= ": Process Critical CSS Request";
			} else if ($_POST['action'] == "pegasaas_process_optimization_request") {
				$page_title .= ": Process Optimization Request";
			} else {
				$page_title .= ": ".$_POST['action'];
			}

		} else if (strstr($url, "/wp-admin/admin.php?page=pegasaas-accelerator")) {
			$page_title = "Pegasaas Dashboard";
		} else if (strstr($url, "/wp-admin/")) {
			$page_title = "WP Dashboard";
		} else if (strstr($url, "/wp-json/")) {
			$page_title = "WP JSON Endpoint";			
		} else {
			$page_title = "Page View";
		}

		if ($log_buffer == "regular") {
			$last_execution_time = self::$last_execution_time;
		} else {
			$last_execution_time = self::$last_detailed_execution_time;
		}

		$precision = 1000;

		if ($last_execution_time == 0) {
		    $last_execution_time = $pegasaas->start_time;
		}

		
		$elapsed_time = PegasaasAccelerator::execution_time_static() - $last_execution_time;
		$elapsed_time = $elapsed_time * $precision;
		$elapsed_time = number_format($elapsed_time, 3, '.', '');
		if ($elapsed_time < 0){
			$elapsed_time = 0;
		}
		
		if ($subsystem == "ERROR" || 
		    $subsystem == "CRITICAL" || 
			$detailed ||
			(isset(PegasaasAccelerator::$settings['settings']['logging']['status']) && PegasaasAccelerator::$settings['settings']['logging']['status'] == 1 && $log_this)) {
			
			$time 		= date("D M d Y H:i:");
			$seconds =  date("s");
			
			list($usec, $sec) = explode(" ", microtime());
			if ($seconds < 10) {
				$time .= "0";
			}
			$time .= number_format($seconds + $usec, 4, '.', '');

			$message = htmlentities($message);
		
				
			$log_message ="
				<tr class='subsystem--{$subsystem} instance mb-1' rel='{$pegasaas->instance}'>
					<td class='timeStamp time-stamp-cell'>{$time}</td>
					<td class='serverIp ip-cell' >{$_SERVER['REMOTE_ADDR']}</td>
					<td class='timeToComplete ttc-cell'>[SESSIONDURATION]s</td>
					<td class='url'>{$page_title}<div class='url-cell'>{$url}</div></td>
					<td class='expand toggler-cell'>+</td>
					
				</tr>
			
				
				
				<tr id='{$pegasaas->instance}' class='hidden process-entries'>
					
					<td colspan='5'>
						<div class='scroll'>
								<table class='table table-hover'>
								<thead class='thead-light'>
									<tr>
								<!--		<th class='col-2'>Time Stamp</th> -->
										
										<th class='subsystem-cell' scope='col'>Subsystem</th>
										<th class='time-elapsed-cell' scope='col'>Elapsed</th>
										<th scope='col'>Message</th>
										
									</tr>
								</thead> 
									
										[LOGROW]
									
								</table>
						</div>
					</td>
				</tr>";

			if ($log_buffer == "regular" &&  self::$log_buffer == "") {
				self::$log_buffer = $log_message;
			} else if ($log_buffer == "detailed" && self::$log_buffer_detailed == "") {
				self::$log_buffer_detailed = $log_message;
			}

			$subsystem_pretty = str_replace("_", " ", $subsystem);
			$subsystem_pretty = ucwords($subsystem_pretty);

			$debug_backtrace = debug_backtrace();
			if ($log_buffer == "regular") {
				$calling_file = explode("/", $debug_backtrace[0]['file']);
				$calling_file = array_pop($calling_file);
				$calling_function = $debug_backtrace[1]['function'];
				$calling_class = $debug_backtrace[1]['class'];
				$calling_line = $debug_backtrace[0]['line'];
			
			} else {
				
	
				$detailed_debug_backtrace_string = "";

				for ($backtrace = 1; $backtrace < sizeof($debug_backtrace); $backtrace++) {
					if ($calling_line = $debug_backtrace["{$backtrace}"]['line'] == "") {
						continue;
					}
					$backtrace_caller = $backtrace+1;
					$calling_file = explode("/", $debug_backtrace["{$backtrace}"]['file']);
					$calling_file = array_pop($calling_file);
					$calling_function = $debug_backtrace["{$backtrace_caller}"]['function'];
					$calling_class = $debug_backtrace["{$backtrace_caller}"]['class'];
					$calling_line = $debug_backtrace["{$backtrace}"]['line'];
					if ($detailed_debug_backtrace_string != "") {
						$detailed_debug_backtrace_string .= "<br>";
					}
					$detailed_debug_backtrace_string .= "{$calling_file} <span class='trace-arrow'>&raquo;</span> ".($calling_class != "" ? $calling_class."->" : "")."{$calling_function}() line #{$calling_line}";
				}		
			}
		

			
			$debug_backtrace_string = "{$calling_file} <span class='trace-arrow'>&raquo;</span> ".($calling_class != "" ? $calling_class."->" : "")."{$calling_function}() line #{$calling_line}";
			
			
	

			$log_message = "
					<tr class='subsystem--{$subsystem}'>
						<td class='subsystem subsystem-cell'>{$subsystem_pretty}</td>
						<td class='time-elapsed-cell'>{$elapsed_time}ms</td>
						<td class='message'>{$message}
						  <div class='trace'>{$debug_backtrace_string}</div>
						</td>
					</tr>
				";

				$detailed_log_message = "
				<tr class='subsystem--{$subsystem}'>
					<td class='subsystem subsystem-cell'>{$subsystem_pretty}</td>
					<td class='time-elapsed-cell'>{$elapsed_time}ms</td>
					<td class='message'>{$message}
					  <div class='trace'>{$detailed_debug_backtrace_string}</div>
					</td>
				</tr>
			";
			if ($log_buffer == "regular") {
				self::$log_buffer = str_replace("[LOGROW]", $log_message. "[LOGROW]", self::$log_buffer);
			} else if ($log_buffer == "detailed") {
				self::$log_buffer_detailed = str_replace("[LOGROW]", $detailed_log_message. "[LOGROW]", self::$log_buffer_detailed);
			}
			
			if ($log_buffer == "regular") {
				self::$last_execution_time = $pegasaas->execution_time();
			} else {
				self::$last_detailed_execution_time = $pegasaas->execution_time();
			}
				

		}

		if ($log_buffer == "regular") {
			// always add this element to the detailed log, which is only logged to a file if the process takes a long time to complete

			self::log($message, $subsystem, $detailed = true);
		}
	}

	static function log_request_data() {
		$actions_to_log = array("pegasaas_process_pagespeed_score_request", 
								"pegasaas_process_pagespeed_benchmark_score_request",
								"pegasaas_process_critical_css_request",
								"pegasaas_process_optimization_request");
		$post_data = json_encode($_POST);
		$get_data = json_encode($_GET);
		if (strstr($_SERVER['REQUEST_URI'], "/wp-admin/admin-ajax.php") && in_array($_POST['action'], $actions_to_log)) {
			self::log("POST DATA: {$post_data}", "critical");
			self::log("GET DATA: {$get_data}", "critical");
		} else if (strstr($_SERVER['REQUEST_URI'], "/wp-json/")) {
			self::log("POST DATA: {$post_data}", "critical");
			self::log("GET DATA: {$get_data}", "critical");
		}
	}
	

	static function log_write() {
		global $pegasaas;
		$log_files_folder 	= PEGASAAS_ACCELERATOR_DIR."/logs/";

		$logfile 			= $log_files_folder."index.html";
		$long_process_log_file = $log_files_folder."log-detailed.html";
		
		if (!is_dir($log_files_folder)) {
			@mkdir($log_files_folder, 0755);
		}
			
		if (PegasaasAccelerator::$settings['settings']['logging']['log_long_processes'] == 1 && $pegasaas->execution_time() >= PegasaasAccelerator::$settings['settings']['logging']['long_process_threshold'] ) {
			$log_file_exists = is_file($long_process_log_file);
			$fp = fopen($long_process_log_file, "a+");

	
		
			if ($fp) {
				if (!$log_file_exists) {
					$header_code = file_get_contents(PEGASAAS_ACCELERATOR_DIR."/assets/templates/log.html");
					fwrite($fp, $header_code);
				}
				$log_buffer = self::$log_buffer_detailed;

				$log_buffer  = str_replace("[SESSIONDURATION]", number_format($pegasaas->execution_time(), 2), $log_buffer);
				$log_buffer  = str_replace("[LOGROW]", "",$log_buffer);

				fwrite($fp, $log_buffer);
				fclose($fp);
			}			
		}
		if (self::$log_buffer != "") {
			$log_file_exists = is_file($logfile);

			$fp = fopen($logfile, "a+");
		
			if ($fp) {
				if (!$log_file_exists) {
					$header_code = file_get_contents(PEGASAAS_ACCELERATOR_DIR."/assets/templates/log.html");
					fwrite($fp, $header_code);
				}
				$log_buffer  = self::$log_buffer;

				$log_buffer = str_replace("[SESSIONDURATION]", number_format($pegasaas->execution_time(), 2), $log_buffer);
				$log_buffer = str_replace("[LOGROW]", "", $log_buffer);
				fwrite($fp, $log_buffer);
				fclose($fp);
			}
		}
	}

	
    /**
     * Logs a message to the browser javascript console or log file with a time since last
     * benchmark position.
     *
     * @param string $message -- the message to log
	 * @param string $log_type -- either "console" or "file" (where to log the message)
     *
     */		
	static function log_benchmark($message, $log_type = "console", $precision = 100000) {
		global $pegasaas;

		$last_execution_time = self::$last_benchmark_execution_time;
	

		if ($last_execution_time == 0) {
		//	if (self::$last_execution_time == 0) {
				$last_execution_time = $pegasaas->start_time;
		//	} else {
		//		$last_benchmark_execution_time = self::$last_execution_time;
		//	}
			//print "last execution time zero {$pegasaas->start_time}";
		  //  $last_execution_time = time() - $pegasaas->start_time;
		}

		
		$elapsed_time = $pegasaas->execution_time() - $last_execution_time;
		$elapsed_time = $elapsed_time * $precision;
		//print $elapsed_time."<br>";
		if ($log_type == "console") {
			self::console_log($message." -- Elapsed Timed: {$elapsed_time}");

		} else if ($log_type == "file") {
			self::log($message." -- Elapsed Timed: {$elapsed_time}", "script_execution_benchmarks");
		
		} else if ($log_type == "debug-li") {
			$elapsed_time = number_format($elapsed_time, 2, '.', ',');
			if ($elapsed_time > 0) { $elapsed_time = "+".$elapsed_time; }
			print "<li>[$elapsed_time] {$message}</li>";
		}

		self::$last_benchmark_execution_time = $pegasaas->execution_time();
	}	
	
	
	
	
	
	function reset_log_file() {
		$logfile = PEGASAAS_ACCELERATOR_DIR."/log.txt";
		@unlink($logfile);

		$logfile = PEGASAAS_ACCELERATOR_DIR."/logs/index.html";
		@unlink($logfile);
		
		$logfile = PEGASAAS_ACCELERATOR_DIR."/logs/log-detailed.html";
		@unlink($logfile);			
	}
	
	function get_microtime() {
		list($usec, $sec) = explode(" ", microtime());
		$time = time() + $usec;
		return $time;
	}
	

	static function instapage_active() {
		if (class_exists("InstapageCmsPluginHelper")) {
			return true;
		} else {
			return false;
		}
	}

	static function is_instapage_url() {
		$slug = InstapageCmsPluginHelper::extractSlug(InstapageCmsPluginConnector::getHomeURL());

		$type = "page";

		$page = InstapageCmsPluginPageModel::getInstance();
		$result = $page->check($type, $slug);

		if (isset($result->instapage_id) && $result->instapage_id) {
	
			// we have landing page for given slug,
			// but if in url there are duplicated slashes show 404 page instead
			if (InstaPageCmsPluginHelper::checkIfRequestUriHasDuplicatedSlashes()) {
			  return false;
			}
	  
			if ($type == '404') {
				return false;
			} else {
			  return true;
			}
		}

		return false;
	}

	
	function get_semaphore($var) {
		global $pegasaas;
		
		$semaphore = $pegasaas->db->get_single_record("pegasaas_semaphore", array("semaphore_name" => $var));
		if (false && $var == "testing_semaphore") {
			print "<li><pre>Semaphore Data:\n";
			var_dump($semaphore);
			print "</pre></li>";
			print "<li>".$semaphore->semaphore_name."</li>";
		} 
		if ($semaphore->semaphore_name == $var) {

			return $semaphore;
		} else {
		
			return false;
		}
	}
	
	function semaphore($var, $maximum_wait = 1000, $maximum_lock_time = 10) {
		global $pegasaas;
		global $test_debug;
		
		if ($var == "testing_semaphore" || $test_debug) {
			$debug = true;
			$start_time = time();
		}
		
		if ($debug) {
			PegasaasUtils::log_benchmark("---------------- function semaphore() --------------", "debug-li", 1);
		}
		
		// do not allow sempahores locks to exist for longer than 10 seconds
		//$semaphores = get_option("pegasaas_semaphores", array());
		$semaphore = $this->get_semaphore($var);
		if (false && $debug) {
			print "<li>Returned Semaphore:<pre>";
			var_dump($semaphore);
			print "</pre>";
			print "</li>";
		}
		
		if (is_object($semaphore) && isset($semaphore->semaphore_name) && $semaphore->semaphore_name == $var) {
			if ($debug) {
				PegasaasUtils::log_benchmark("Existing Semaphore '{$var}' Detected", "debug-li", 1);
			}
			if (time() - strtotime($semaphore->when_created) > $maximum_lock_time) {
				
				$this->release_semaphore($var);
			}
		} else {
			
			if ($debug) {
				if (is_object($semaphore)) {
					PegasaasUtils::log_benchmark("semaphore is object '{$var}'", "debug-li", 1);
					if (isset($semaphore->semaphore_name)) {
								PegasaasUtils::log_benchmark("semaphore->semaphore_name isset '{$var}' with value of {$semaphore->semaphore_name}", "debug-li", 1);

					}

				} else {

					PegasaasUtils::log_benchmark("Semaphore does not yet exist '{$var}'", "debug-li", 1);
				}
			}
		}

			$debug_backtrace = debug_backtrace();
			$calling_file = explode("/", $debug_backtrace[0]['file']);
			$calling_file = array_pop($calling_file);
			$calling_function = $debug_backtrace[1]['function'];
			$calling_class = $debug_backtrace[1]['class'];
			$calling_line = $debug_backtrace[0]['line'];
			
			$debug_backtrace_string = "{$calling_file} ".($calling_class != "" ? $calling_class."->" : "")."{$calling_function}() line #{$calling_line}";
	
		$interval = 0;
		
		// if this semaphore is a "bail if it is in use" semaphore (wait time == 0) then we will only 
		// execute the first interation of the while() loop
		if ($maximum_wait <= 0) {
			$ms_interval = 0;
			$max_intervals = 1; 
		
		// otherwise, the script will pause for 250ms at a time, checking to see if the semaphore has been released
		} else {
			$ms_interval 			= 250; 
			$max_intervals 			= $maximum_wait / $ms_interval;
		}
		
		if ($debug) {
			PegasaasUtils::log_benchmark("Maximum Wait Time (ms): {$maximum_wait}", "debug-li", 1);
			PegasaasUtils::log_benchmark("Interval (ms): {$ms_interval}", "debug-li", 1);
			PegasaasUtils::log_benchmark("Max Intervals: {$max_intervals}", "debug-li", 1);
		}
		
		while($semaphore = $this->get_semaphore($var) && $interval++ < $max_intervals) {
			$sleep_time = $ms_interval * 1000;
			usleep($sleep_time); // usleep is in microseconds, 
		}
		if ($debug) {
			PegasaasUtils::log_benchmark("Total Executed Interval: {$interval}", "debug-li", 1);
		}	
		// if we reached the limit that of time requested to wait, then return a failure status
		if ($interval >= $max_intervals) {
			
			// quick fetch and set of existing semaphores, so we can release them in the future
			//$semaphores = get_option("pegasaas_semaphores", array());
			//if (!array_key_exists($var, $semaphores)) {
			//	$semaphores["$var"] = time();
			//	update_option("pegasaas_semaphores", $semaphores, false);
			//}
			if ($debug) {
				PegasaasUtils::log_benchmark("Lock Encountered", "debug-li", 1);
			}				
			$this->log("Semaphore ($var) LOCK ENCOUNTERED by {$debug_backtrace_string}", "semaphores");
			return false;
		} else {
			if ($debug) {
				PegasaasUtils::log_benchmark("Lock Not Encountered", "debug-li", 1);
			}		
		}
		
		$now = date("Y-m-d H:i:s");
		// quick fetch and set of existing semaphores, so we can release them if need be in the future
		$pegasaas->db->update_record("pegasaas_semaphore", array("semaphore_name" => $var, "when_created" => $now));
		//$semaphores = get_option("pegasaas_semaphores", array());
		//$semaphores["$var"] = time();
		//update_option("pegasaas_semaphores", $semaphores, false);
		
		$this->log("Semaphore ($var) LOCKED by {$debug_backtrace_string}", "semaphores");

		// otherwise, we can will lock this sempahore
		//update_option("pegasaas_semaphore_lock__{$var}", time(), false);
		return true;
	}
	
	
	function log_load_milestone($milestone) {
		if (isset($_GET['debug']) && $_GET['debug'] == "load") {
			$current_usage = (memory_get_peak_usage() / 1024 / 1024);
			$this->log("$milestone using ".$current_usage);
		}
		
	}
	
	function release_semaphore($var) {
		global $pegasaas; 
		global $test_debug;
		
		if ($var == "testing_semaphore" || $test_debug) {
			$debug = true;
		}
		
		
		$debug_backtrace = debug_backtrace();
			$calling_file = explode("/", $debug_backtrace[0]['file']);
			$calling_file = array_pop($calling_file);
			$calling_function = $debug_backtrace[1]['function'];
			$calling_class = $debug_backtrace[1]['class'];
			$calling_line = $debug_backtrace[0]['line'];
			
			$debug_backtrace_string = "{$calling_file} ".($calling_class != "" ? $calling_class."->" : "")."{$calling_function}() line #{$calling_line}";
	
		
		if ($debug) {
			PegasaasUtils::log_benchmark("Releasing Semaphore '{$var}'", "debug-li", 1);
		}
		//update_option("pegasaas_semaphores", $semaphores, false);
		$pegasaas->db->delete("pegasaas_semaphore", array("semaphore_name" => $var));
		
		// we can remove this next line after 3.6.x
		delete_option("pegasaas_semaphore_lock__{$var}");
		
		$this->log("Semaphore ($var) RELEASED by {$debug_backtrace_string}", "semaphores");
		return true;
	}
	
	
	
	function fetch_page_html($url) {
		$headers = array("user-agent" => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13'); 

		// keep the timeout low -- if the system isn't responding until after, then we definitely should not be pushing the server to respond
		if ($url != "") {
			$args = array("headers" => $headers); // short timeout -- this allows this non-blocking request to quit early
			
			$output = wp_remote_request($url, $args);
			if (is_a($output, "WP_Error")) {

				$this->log("Fetch Page HTML Failed: {$url}", "caching");
				return "";
			} else {
				return $output['body'];
			}
		} else {
			return "";
		}
		
	}
	
	static function https_http_inconsistency_exists() {
		return is_ssl() && strstr(get_option("home"), "http://") || is_ssl() && strstr(get_option("siteurl"), "http://") ;
	}
	
	static function permalinks_use_trailing_slashes() {
		
		$permalink_structure = get_option("permalink_structure");
		
		if (substr($permalink_structure, -1) == "/") {
			return true;
		} else {
			return false;
		}
	}
	
	
	function touch_url($url, $args = array(), $proxy_this_request = false) {
		global $pegasaas;

		$original_url 	= $url;
		$debug		 	= false;
		
		if (PegasaasUtils::uses_domain_specific_handling()) {
			if (!strstr($url, "http://") && !strstr($url, "https://")) {
				$url = "https:/".$url;
			} 
		}

		$bypass_cache_clear = array_key_exists('headers', $args) && array_key_exists('X-Pegasaas-Bypass-Cache', $args['headers']);
		
		if ($debug) { $pegasaas->utils->console_log("In Touch_URL, before clear cache {$url}"); }
		
		if (!$this->execution_time_ok()) {
			if ($debug) { $pegasaas->utils->console_log("In Touch_URL, Execution Time Too High -- Exiting Early"); }
			return false;
		}

		if (PegasaasUtils::wpml_multi_domains_active()) {
			$url_data = parse_url($url);
			if ($url_data["host"] != "") {
				$resource_id = str_replace($url_data['scheme']."://".$url_data['host'], "", $url);
				if ($resource_id == "/") {
					$resource_id = "/".$url_data['host']."/";
				}
				if ($bypass_cache_clear) {
				 
				} else {
					$pegasaas->cache->clear_cache($resource_id);
				}
				// url is already a fully resolved url
			} else {
				if (array_key_exists('headers', $args['headers']) && in_array($args['headers'], "X-Pegasaas-Bypass-Cache: 1")) {
				 
				} else {
					$pegasaas->cache->clear_cache($url);
				}
				$post_obj = PegasaasUtils::get_post_obj_from_resource_id($url);
				$post_type     = get_post_type( $post_obj->ID );
				global $sitepress;
				$post_language = $sitepress->get_language_for_element( $post_obj->ID, 'post_' . $post_type );
				
				$default_language = $sitepress->get_default_language();
				$wpml_domains = wpml_get_setting( 'language_domains' );
				$home_url_data = parse_url($pegasaas->get_home_url());
				if ($post_language != $default_language && $permalink != "/".$wpml_domains["{$post_language}"]."/") {
					$url	   = $home_url_data['scheme']."://".$wpml_domains["{$post_language}"].$url;
				} else {		
					$url = $pegasaas->get_home_url().$url;
				}
			}
		} else {
			if ($bypass_cache_clear) {
				
			} else {
			
				$pegasaas->cache->clear_cache($url);
			}
			$url = $pegasaas->get_home_url().$url;
		}
		

		
		
		
		$post_fields = array();
		
		$location = $pegasaas->utils->get_wp_location();

		// force https (by using this method) even if site is using http.
		// the only way that this request could be https, is if a valid certificate exists.
		// we should then assume that the rest of the site can be viewed as ssl
		
		
		
		if ($debug) { 
			list($usec, $sec) = explode(" ", microtime());
			$time = time() + $usec;

			$total_time = $time - $pegasaas->start_time;

			print "<script>console.log('{$pegasaas->instance} Pegasas Before touch of ($url): Server Build Time {$total_time}');</script>";
		}	

		$this->log("Attempting to touch: {$url}", "caching");

		if (!isset($args['headers'])) {
			$args['headers'] = array();
		}
		if (!isset($args['headers']['user-agent'])) {
			$user_agent = rand(1,4);
			if ($user_agent == 1) {
				$args['user-agent'] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36";
			} else if ($user_agent == 2) {
				$args['user-agent'] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:75.0) Gecko/20100101 Firefox/75.0";
			} else if ($user_agent == 3) {
				$args['user-agent'] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.63";
			} else {
		//		$args['user-agent'] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 Safari/537.36 Edge/18.17763";
				$args['user-agent'] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36 Edg/91.0.864.48";
			}
		}
		if (isset($args['optimization-test'])) {
			unset($args['optimization-test']);
			$args['headers']['x-pegasaas-optimization-test'] = "1";
		}

		if (!isset($args['blocking'])) {
			$args['blocking'] = true;
		}
		
		if (!isset($args['method'])) {
			$args['method'] = "GET";
		}		

		if (!isset($args['sslverify'])) {
			$args['sslverify'] = false;
		}			
		
		if (!isset($args['timeout'])) {
			$args['timeout'] = 5;
		}	
		
		if ($args['blocking'] === false) {
			$args['timeout'] = '1';
		}
		
		// this is necessary for some servers which use a local linux-base cron system
		if ($_SERVER['REMOTE_ADDR'] == "127.0.0.1") {
			$args['blocking'] = true;
		}

		if ($proxy_this_request) {
			$args["method"] = "POST";
			$args["body"] = array("url" => $url);

			$url = "https://".PEGASAAS_ACCELERATOR_CACHE_SERVER."/proxy-hit/";

			$this->log("Attempting to touch via proxy: {$url}");
		}
		//$output = wp_remote_post($url, $args);
		if (!strstr($url, "?")) {
			$url .= "";
		}
		//	$this->log("Attempting to touch (args2): ".print_r($args, true), "critical");
	
		$output = wp_remote_request($url, $args);

		if (is_a($output, "WP_Error")) {

			$this->log("Touch Failed: {$url}", "caching");
			$response_data = "Touch Failed because ".$output->get_error_message();
		} else {

			$http_response = $output['http_response'];
			if ($http_response) { 
				$response = $http_response->get_response_object();
				$response_data = $http_response->get_data();
				if ($_POST['pegasaas_debug'] == "show-headers") {
					var_dump($response->headers);
				}
				$response_url_data = parse_url($response->url);
				$requested_url_data = parse_url($url);
				
				$response_url_path = @$response_url_data['path'];
				$requested_url_path = @$requested_url_data['path'];
				
				//if ($response->redirects > 0 && $response->url != $url) {
				if ($response->redirects > 0 && trailingslashit($response_url_path) != trailingslashit($requested_url_path) ) {
					
					
					// only auto-disable acceleration for this page, when a redirection occurs, IFF wpml multi domains is not active
					if (!PegasaasUtils::wpml_multi_domains_active()) {
						$this->log("Detecting Redirect of $url to {$response->url}", "caching");
					
						// disable acceleration for this page
						$this->log("Disabling Accelerator for {$original_url}", "caching");
						$pegasaas->disable_accelerator_for_page($original_url);
					}
				}
			} else {
				$response = NULL;
			}
		}
			
		$this->log("Finished touch: {$url}", "caching");

		if ($debug) { 
			list($usec, $sec) = explode(" ", microtime());
			$time = time() + $usec;

			$total_time = $time - $pegasaas->start_time;

			print "<script>console.log('{$pegasaas->instance} Pegasas After touch of ($url): Server Build Time {$total_time}');</script>";
		}
		
		if (isset($args['return-data'])) {
					//	$this->log("Touch Response: ".$response_data, "critical");

			return $response_data;	
		} else {
			return $response;
		}
	}

	function get_all_semaphore_locks() {
		$semaphores = array("accelerator_overrides",
							"auto_accelerate_pages",
							"pegasaas_accelerator_pagespeed_benchmark_score_requests",
							"pegasaas_accelerator_pagespeed_score_requests",
							"pegasaas_accelerator_pagespeed_score_request_tokens",
							"pegasaas_cache_map",
							"pegasaas_critical_css",
							"pegasaas_deferred_js",
							"pegasaas_process_pagespeed_benchmark_score_request",
							"pegasaas_process_pagespeed_score_request",
							"pegasaas_pending_critical_css_request",
							"submit_benchmark_requests",
							"submit_scan_requests"); 
		
		
		
		$locked_semaphores = get_option("pegasaas_semaphores", array());
		$semaphores = array_merge($semaphores, $locked_semaphores);
		
		foreach ($semaphores as $semaphore_name) {
			$locked = get_option("pegasaas_semaphore_lock__{$semaphore_name}", false);
			if ($locked) {
				$locked_semaphores[] = $semaphore_name;
			}
		}
		
		return $locked_semaphores;
	}

	
	function debug($debug, $output) {
		if ($debug) {
			print $output;
		}
	}
	
	
	
	function microtime_float() {
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
	
	function check_curl_ssl($force_retest = false) {
		global $pegasaas;
		
		$curl_test = get_option("pegasaas_accelerator_curl_ssl_verify", -1);
		
		if ($curl_test == -1 || (isset($_GET['retest_curl_ssl']) && $_GET['retest_curl_ssl'] == 1) || $force_retest) {
			$error = 0;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, PEGASAAS_API_KEY_SERVER); //setup request to website to check license key
			curl_setopt($ch, CURLOPT_URL, PEGASAAS_ACCELERATOR_URL."assets/css/admin.css");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);			// return the response
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); 
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);					//timeout in seconds
			$response = @curl_exec($ch);

			if ($_GET['pegasaas-curl-error-test']) {
				$error = $_GET['pegasaas-curl-error-test'];
				if (in_array($error, array(77,83,82,66,64,60,59,53,54,5,35,51)) ) {
					update_option("pegasaas_accelerator_curl_ssl_verify", 0);
				} else {
					update_option("pegasaas_accelerator_curl_ssl_verify", 1);
				}
				update_option("pegasaas_accelerator_last_curl_error", $error );
				
			} else	if (curl_error($ch)) {
				
				$error = curl_errno($ch);
				if (in_array($error, array(77,83,82,66,64,60,59,53,54,5,35,51)) ) {
					update_option("pegasaas_accelerator_curl_ssl_verify", 0);
				} else {
					update_option("pegasaas_accelerator_curl_ssl_verify", 1);
				}
				update_option("pegasaas_accelerator_last_curl_error", $error );
			} else {
				
				update_option("pegasaas_accelerator_curl_ssl_verify", 1);
				update_option("pegasaas_accelerator_last_curl_error", $error );
			}
			update_option("pegasaas_accelerator_last_curl_ssl_test", time() );

			
		} 
	
	
	}
	

	

		
	function has_bad_certificate() {
		$ssl_errors = array(77,83,82,66,64,60,59,53,54,5,35);
		if ($_SERVER['HTTPS'] == "on") {
			

			
			$last_cert_check = get_option("pegasaas_accelerator_last_curl_ssl_test", -1);
			if ($last_cert_check == -1 || time() - $last_cert_check > 86400 || isset($_GET['recheck-ssl']) ) {
				
				$this->check_curl_ssl(true);
			}
			
			$verify_peer 	 = get_option("pegasaas_accelerator_curl_ssl_verify");
			
			if (!$verify_peer && time() - $last_cert_check > 60) {
				$this->check_curl_ssl(true);
			}
			
			$has_good_certificate = get_option("pegasaas_accelerator_curl_ssl_verify");
			
			
			
			return !$has_good_certificate;
			
			
		} 
		return false;
	}	
	
	function check_curl_load_time() {
		global $pegasaas;
		
		$curl_test = get_option("pegasaas_accelerator_interface_load_time", -1);
		
		if ($curl_test == -1 || $_GET['retest_interface_load_time'] == 1) {
			
			$url = get_admin_url();
			print $url;
 
			$args = array();
			$verify_peer = get_option("pegasaas_accelerator_curl_ssl_verify", 0) == 1;
			$verify_peer = false; // lets encrypt certificate now invalid
			$args['ssl_verify'] = $verify_peer;
			$args['sslverify'] = $verify_peer;
			$args['method'] 	= "GET";
			$args['timeout'] 	= "15";
	
			$start = $pegasaas->microtime();
			$response = wp_remote_request($url, $args);
			$load_time = $pegasaas->microtime() - $start;
		
			if (is_a($response, "WP_Error")) {
				$error = $response->get_error_message();
				update_option("pegasaas_accelerator_interface_load_time", 15);
			} else {
				update_option("pegasaas_accelerator_interface_load_time", $load_time);

			}
			return $load_time;
		} else {
			return $curl_test;
		}
	
	}
	
	function strip_ignored_query_arguments($query_string) {
		global $pegasaas;
		if ($query_string == "NULL") {
			return $query_string;
		}
		$args = array();
		$default_args = array("utm_source", "utm_medium", "utm_campaign", "utm_term", "utm_content", "gclid", "keyword");

		$ignored_query_arguments_str = PegasaasAccelerator::$settings['settings']['dynamic_urls']['additional_args'];
		$ignored_query_arguments 	 = explode("\n", $ignored_query_arguments_str); 
		foreach ($ignored_query_arguments as $i => $value) {
			$value = trim($value);
			$ignored_query_arguments["$i"] = $value; 
		}
		
		foreach ($default_args as $arg) { 
			if (PegasaasAccelerator::$settings['settings']['dynamic_urls']["{$arg}"] == 1) {
				$ignored_query_arguments[] = $arg;
			}
		}
			
		parse_str($query_string, $args);
		
		foreach ($args as $name => $value) {
			if (in_array($name, $ignored_query_arguments) || strstr($name, "gpsi-call")) {
				unset($args["$name"]);
			}
		}
			
		return http_build_query($args);
		
		
	}
	
	
	
	function get_uri_args($request_uri = ""){
		if ($request_uri == "") {
			$request_uri = $_SERVER['REQUEST_URI'];
		}
		$request_uri = str_replace("accelerate=on", "", $request_uri);
		
		$data = explode("?", $request_uri);
		
		if (strlen($data[1]) == 0) {
			return "NULL";
		} else {
			return $data[1];
		}
		
		
	}

	
	function sort_page_types($a, $b) {
		if ($a == $b) { return 0; }
		
		if ($a == "page") { return -1; }
		if ($b == "page") { return 1; }
		
		if ($a == "post") { return -1; }
		if ($b == "post") { return 1; }
		
		return strcmp($a, $b);
	}	

	function sort_pages_home_page_first($a, $b) {
				
		if ($a->slug == "/") { return -1; }
		if ($b->slug == "/") { return 1;  }
		
		if ($a->post_type == $b->post_type) {
			return strcmp($a->slug, $b->slug);
		}
		if ($a->post_type == "page") { return -1; }
		if ($b->post_type == "page") { return 1; }
		
		if ($a->post_type == "post") { return -1; }
		if ($b->post_type == "post") { return 1; }	
		
		return strcmp($a->post_type, $b->post_type);
	}	
	
	function get_all_pages() {
		return $this->get_all_posts("page");
		
	}
	
	
	function get_all_posts($post_type = "post", $search_filter = "") {
		global $pegasaas;

		
		
		if ($post_type == "page") {
			$order = "ASC";
			$order_by = "title";
		} else {
			$order = "DESC"; 
			$order_by = "date";
		}	

		$args = array(
			'posts_per_page'	=> 10000,
			'offset'			=> 0,
			'category'			=> '',
			'category_name'		=> '',
			'orderby'			=> $order_by,
			'order'				=> $order,
			'include'			=> '',
			'exclude'			=> '',
			'meta_key'			=> '',
			'meta_value'		=> '',
			'post_type'			=> $post_type,
			'post_mime_type'	=> '',
			'post_parent'		=> '',
			'author'			=> '',
			'author_name'		=> '',
			'post_status'		=> 'publish',
			'suppress_filters'	=> true 
		);
		if ($post_type == "") {
			unset($args['post_type']);
		}
		
		// get list of pages and posts
		if ($search_filter != "" && false) {
			if ($search_filter == "/") {
				if (get_option("show_on_front") == "posts" || get_option("show_on_front") == "layout") {
					$args['include'] = array(0);
				} else if (get_option("show_on_front") == "page") {
			
		
				
					$args['include'] = array( get_option("page_on_front") );
				}
			} else {
				$args['name'] = $search_filter;
			}
		    
			$args['suppress_filters'] = false;
		}
		//var_dump($args);
			
		$posts_array = get_posts($args);
		//print sizeof ($posts_array);
		
		$accelerated_pages 	= get_option("pegasaas_accelerated_pages", array());

		foreach ($posts_array as $index => $object) {
			$object->post_content = "";
			
			if ($object->post_name != "/") {
				$permalink = get_permalink($object->ID);
				if (!strstr($permalink, $pegasaas->get_home_url())) {
					$permalink = $object->post_name;
				}
				$permalink = str_replace($pegasaas->get_home_url('', 'http'), "", $permalink);
				$permalink = str_replace($pegasaas->get_home_url('', 'https'), "", $permalink);
				
				$slug = $permalink;
				$slug = urldecode($permalink);
				
				$resource_id = rtrim($permalink, "/")."/";
				$posts_array["$index"]->slug = $slug;
				$posts_array["$index"]->resource_id = $resource_id;
			}
			
			$accelerated = 1;

			if ($object->post_type == "tribe_events") {
				$pid = $posts_array["$index"]->resource_id;
			
			} else {
				$pid = $posts_array["$index"]->resource_id;
			}
			
			
			
			if (!array_key_exists($pid, $accelerated_pages) && $accelerated_pages["{$pid}"] != 1){
				$accelerated = 0;	
	
			}
			$posts_array["$index"]->accelerated = $accelerated;
		}		
		return $posts_array;
		
	}
	
	function get_post_id_from_url($resource_id) {
		
	}
	
	static function get_post_types($include_pegasaas_deferred_js = false) {
		global $pegasaas;
		$post_types = get_post_types(array("public" => true ));
		
		$debug = false;
		if ($debug) { 
		  print "<pre class='admin'>";
			var_dump($post_types);
			print "</pre>";
		}
		
		unset($post_types['attachment']);
		unset($post_types['elementor_library']);
		unset($post_types['tcb_symbol']);
		unset($post_types['tcb_lightbox']);
		unset($post_types['cms_block']);
		unset($post_types['woodmart_sidebar']);
		unset($post_types['woodmart_slide']);
		unset($post_types['ct_template']);
		unset($post_types['uncode_gallery']);
		unset($post_types['uncodeblock']);

		if (!$include_pegasaas_deferred_js) {
			unset($post_types['pegasaas-deferred-js']);
		}
	
		
		if (class_exists("Tribe__Main")) {
			$post_types["tribe_events"] = "tribe_events";
		}

		// sort post types
		uasort($post_types, array($pegasaas->utils, 'sort_page_types'));
		
		return $post_types;
		
	}
	
	function get_site_composition() {
		
		$composition = array(						//Brandon Confirm
			'time_per_page'		=> 0,
			'summary'			=> array(
				'count'				=>0,
				'time_to_complete'	=>-1,
			),
		);
		$composition['post_types'] = array();
	
		$post_types = PegasaasUtils::get_post_types();
		$speed_factor = 2;
		
		$all_posts_array = array();
		$composition['time_per_page'] += $speed_factor ;
		
		foreach ($post_types as $post_type) {
			$count_posts = wp_count_posts($post_type, 'readable');
			$post_type_object = get_post_type_object($post_type); 
			$composition['post_types']["$post_type"]["name"] = $post_type_object->labels->name;
			$composition['post_types']["$post_type"]["count"] = $count_posts->publish;
			$composition['post_types']["$post_type"]["time_to_complete"] = ($speed_factor * $composition['post_types']["$post_type"]["count"]);
		
			$composition['summary']['count'] += $composition['post_types']["$post_type"]["count"];
			$composition['summary']['time_to_complete'] += $composition['post_types']["$post_type"]["time_to_complete"];
			
		}
		
	/*
		$post_types 		= $this->get_post_types();
		$category_objects 	= get_categories();
		
		$composition['post_types']["post_categories"]["name"] = "Post Category";
		$composition['post_types']["post_categories"]["count"] = sizeof($category_objects);
		$composition['post_types']["post_categories"]["time_to_complete"] = ($speed_factor * $composition['post_types']["post_category"]["count"]);
		
		

		foreach ($post_types as $post_type_id => $post_type) {
			$post_type = get_post_type_object($post_type_id);
			$post_type_name = $post_type_id."_category";
			
			if (sizeof($post_type->taxonomies) > 0) {
				$composition['post_types']["{$post_type_name}"]["name"] = $post_type_object->labels->name." Category";
				$composition['post_types']["{$post_type_name}"]["count"] = sizeof($post_type->taxonomies);
				$composition['post_types']["{$post_type_name}"]["time_to_complete"] = ($speed_factor * $composition['post_types']["post_categories"]["count"]);
			}
			
			
		}		

		$categories = $this->get_all_categories();
		
		$composition['summary']['count'] += sizeof($categories);
		$composition['summary']['time_to_complete'] += sizeof($categories);
*/
		
		
		return $composition;
		
	}
	static function get_filter($tag) {
		  # Returns the current state of the given WordPress filter.
  		global $wp_filter;
  		return $wp_filter[$tag];
	}
	
	static function set_filter($tag, $saved) {
	  # Sets the given WordPress filter to a state saved by get_filter.
	  remove_all_filters($tag);
	  foreach ($saved as $priority => $func_list) {
		foreach ($func_list as $func_name => $func_args) {
		  add_filter($tag,$func_args['function'], $priority, $func_args['accepted_args']);
		}
	  }		
		
	}
	static function get_posts($args = array()) {
		global $wpdb;
		global $pegasaas;
		$debug = false;
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
			
			//$disable_filters = true;
			 
		}
	//	$disable_filters = true;

		
		if ($debug && false) { 
			list($usec, $sec) = explode(" ", microtime());
			$time = time() + $usec;

			$total_time = $time - $pegasaas->start_time; 

			print "<script>console.log('{$pegasaas->instance} Pegasas Before get all {$args['post_type']}: Server Build Time {$total_time}');</script>";
		}
			
		$post_table = $wpdb->prefix."posts";
		$query = "SELECT ID, post_title, post_name, post_type FROM {$post_table} WHERE post_type='{$args['post_type']}' AND post_status='{$args['post_status']}' ORDER BY `{$args['orderby']}` {$args['order']}";
		if ($args['limit'] != "") {
			$query .= " LIMIT {$args['limit']}";
		}
		if ($debug) {
			print "<script>console.log('{$pegasaas->instance} Pegasaas Before Query {$args['post_type']}: Server Build Time ".$pegasaas->execution_time()."');</script>";
			$last_execution_time = $pegasaas->execution_time();
		}
		$results = $wpdb->get_results($query);
		if ($debug) {
			$elapsed_time = $pegasaas->execution_time() - $last_execution_time;
			$elapsed_time = $elapsed_time * 100000;
			print "<script>console.log('{$pegasaas->instance} Pegasaas After Query {$args['post_type']}: Server Build Time ".$elapsed_time."');</script>";
			$last_execution_time = $pegasaas->execution_time();
		}
		if ($disable_filters) {
			$page_link_filter = self::get_filter("page_link");
			print "<br>page link filter is ".$page_link_filter."<br>";
			$post_link_filter = self::get_filter("post_link");
			print "<br>post_link_filter   is ".$post_link_filter."<br>";
			remove_all_filters("page_link");
			remove_all_filters("post_link");
		}
		
		foreach ($results as $r => $result) {
			if ($debug) {
				$elapsed_time = $pegasaas->execution_time() - $last_execution_time;
				$elapsed_time = $elapsed_time * 100000;
				print "<script>console.log('{$pegasaas->instance} Pegasaas Before get_permalink {$result->ID}: Server Build Time ".$elapsed_time."');</script>";
				$last_execution_time = $pegasaas->execution_time();
			}
			/*
			$permalink = get_permalink($result->ID);
			if ($debug) {
				print "original permalink: ".$permalink."<br>\n";
				
			}
			if ($debug) {
				$elapsed_time = $pegasaas->execution_time() - $last_execution_time;
				$elapsed_time = $elapsed_time * 100000;
				print "<script>console.log('{$pegasaas->instance} Pegasaas After get_permalink {$result->ID}: Server Build Time ".$elapsed_time."');</script>";
				$last_execution_time = $pegasaas->execution_time();
			}
			$permalink_url_data = parse_url($permalink);
			$permalink_scheme = $permalink_url_data['scheme'];
			$permalink = str_replace($pegasaas->get_home_url('', 'http'), "", $permalink);
			$permalink = str_replace($pegasaas->get_home_url('', 'https'), "", $permalink);
			if ($debug) {
				$elapsed_time = $pegasaas->execution_time() - $last_execution_time;
				$elapsed_time = $elapsed_time * 100000;
				print "<script>console.log('{$pegasaas->instance} Pegasaas After parse_url {$result->ID}: Server Build Time ".$elapsed_time."');</script>";
				$last_execution_time = $pegasaas->execution_time();
			}
			
			if ($wpml_multi_domains_active) {
				$post_type     = get_post_type( $result->ID );
				$post_language = $sitepress->get_language_for_element( $result->ID, 'post_' . $post_type );
				
				if ($post_language != $default_language && 
					$permalink != "/".$wpml_domains["{$post_language}"]."/"
				   	&& !strstr($permalink, "://") ) {
					//print "Not default<br>";
					$permalink	   = $permalink_scheme."://".$wpml_domains["{$post_language}"].$permalink;

				} else if ($post_language == $default_language && !strstr($permalink, "://") ) {
					//print "is default domain/languge<br>";
					$permalink	   = $permalink_scheme."://".$wpml_domains["{$post_language}"].$permalink;
				} else {
					if (strstr($permalink, "://") && $post_language != $current_language && !strstr($permalink, $wpml_domains["$post_language"])) {
					 // print "yup $post_language $current_language<br>";
						$permalink = str_replace($permalink_scheme."://".$wpml_domains["{$current_language}"], $permalink_scheme."://".$wpml_domains["{$post_language}"], $permalink);
					} 
					//print "no modification<br>";
				}
			}
			*/
			
			if ($debug) {
				print "modified permalink: ".$permalink."<br>\n";
				print "post language: ".$post_language."<br>\n";
				print "current language: ".$current_language."<br>\n";

			}

			//$slug = $permalink;
			//$slug = urldecode($slug);
			if ($debug) {
				$elapsed_time = $pegasaas->execution_time() - $last_execution_time;
				$elapsed_time = $elapsed_time * 100000;
				print "<script>console.log('{$pegasaas->instance} Pegasaas Before get_object_id {$result->ID}: Server Build Time ".$elapsed_time."');</script>";
				$last_execution_time = $pegasaas->execution_time();
			}
			$resource_id = PegasaasUtils::get_object_id($result->ID);
			
			
			if (strstr($resource_id, "?")) {
				unset($results["{$r}"]);
				continue;
			}			
			
			
			if ($debug) {
				$elapsed_time = $pegasaas->execution_time() - $last_execution_time;
				$elapsed_time = $elapsed_time * 100000;
				print "<script>console.log('{$pegasaas->instance} Pegasaas After get_object_id {$result->ID}: Server Build Time ".$elapsed_time."');</script>";
				$last_execution_time = $pegasaas->execution_time();
			}
			if ($debug) {
				print "object id: ".$resource_id."<br>\n";
			}

			
			//$results["{$r}"]->slug = $slug;
			$results["{$r}"]->resource_id = $resource_id;
			if ($debug) {
				print "permalink: ".$permalink."<br>\n";

				print "slug: ".$slug."<br>\n";
				print "resource id: ".$resource_id."<br><br>\n\n";
			}
			
			if ($wpml_multi_domains_active && $post_language == "") {
				unset($results["{$r}"]);
			} else if ($wpml_multi_domains_active) {
				$results["{$r}"]->language = $post_language;
			} else {
				$results["{$r}"]->language = "";
			}
			//print "<script>console.log('--');</script>";

		}
		//print "</pre>";
		if ($disable_filters) {
			self::set_filter("page_link", $page_link_filter);
			self::set_filter("post_link", $post_link_filter);	
		}
		
		
		if ($debug) {
			list($usec, $sec) = explode(" ", microtime());
			$time = time() + $usec;

			$total_time = $time - $pegasaas->start_time;

			print "<script>console.log('{$pegasaas->instance} Pegasas After get all {$args['post_type']}: Server Build Time {$total_time}');</script>";
		}
		//print "<pre>";
		//print "in get_posts\n";
		//var_dump($results);
		//print "</pre>";
		return $results;
		
	}
	
	function execution_time_ok() {
		global $pegasaas;
		
		list($usec, $sec) = explode(" ", microtime());
		$time = time() + $usec;

		$total_time = $time - $pegasaas->start_time;

		return ($total_time < $pegasaas->max_execution_time);
		
	}
	
	function console_log($output) {
		global $pegasaas;
	
		
		list($usec, $sec) = explode(" ", microtime());
		$time = time() + $usec;

		$total_time = $time - $pegasaas->start_time;
		if (isset($_GET['console-log'])) {
			print "<script>console.log('{$pegasaas->instance} {$output} -- Execution Time {$total_time}');</script>";
		}
		
	}
	
	static function get_all_categories() {
		global $pegasaas;
		global $test_debug;
		
		// add in the categories
		$post_types 		= PegasaasUtils::get_post_types();
		$category_objects 	= get_categories();
		$categories 		= array();
		foreach ($category_objects as $category_obj) {
			$category_id = $category_obj->category_nicename;
			$category_obj->category_post_type = "post";
			$category_obj->post_title = $category_obj->name;
			
			$categories["$category_id"] = $category_obj;
			if ($test_debug) { 
					PegasaasUtils::log_benchmark("Category {$category_id}", "debug-li", 1);
				print "<li><pre>";
				var_dump($category_obj);
				print "</pre>";
				print "</li>";
			}		
		}

		foreach ($post_types as $post_type) {
			$ptobj = get_post_type_object($post_type);
			
			
			foreach ($ptobj->taxonomies as $taxonomy) {
				if ($taxonomy == "post_tag") {
					continue;
				}

				$post_type_category_objects = get_categories(array("taxonomy" => $taxonomy));
				foreach ($post_type_category_objects as $post_type_category_obj) {
					$category_id = $post_type_category_obj->category_nicename;
					$post_type_category_obj->category_post_type = $post_type;
					

					$categories["$category_id"] = $post_type_category_obj;		

				}
			}
		}
		
		foreach ($categories as $category_id => $category) {
			$category->ID = "category__".$category->cat_ID;
			$category->is_category = true;
			$category->is_woocommerce_product_tag = false;
			$category->is_woocommerce_product_category = false;
			
			
			$permalink = get_category_link($category->cat_ID);
			

			$permalink = str_replace($pegasaas->get_home_url('', 'https'), "", $permalink);
			$permalink = str_replace($pegasaas->get_home_url('', 'http'), "", $permalink);
			$slug = urldecode($permalink);
			$resource_id = rtrim($slug,"/")."/"; 
			
			$category->slug = $slug;
			$category->resource_id = $resource_id;
			
			$category->post_type = "category";
			if ($category->post_name != "") {
			} else if ($category->cat_nicename != "") {
				$category->post_name = $category->cat_nicename;
			} else {
				$category->post_name = $category->category_nicename;
			}
			if ($test_debug) { 
				//	PegasaasUtils::log_benchmark("Category name for  {$category_id} is {$category->post_name}", "debug-li", 1);
			
			}	
			
			$categories["$category_id"] = $category;
		}		

		
		return $categories;
		
	}

	
	static function get_all_woocommerce_product_tags() {
		global $pegasaas;
		
		// add in the categories
		$post_types 		= PegasaasUtils::get_post_types();
		$args = array(
				 'taxonomy'     => 'product_tag',
				 'orderby'      => 'name',
				 'show_count'   => 0,
				 'pad_counts'   => 0,
				 'hierarchical' => 0,
				 'title_li'     => '',
				 'hide_empty'   => 0
		  );		
		
		$category_objects 	= get_categories($args);
		$categories 		= array();
		foreach ($category_objects as $category_obj) {
			$category_id = $category_obj->category_nicename;
			$category_obj->category_post_type = "product";
			$categories["$category_id"] = $category_obj;
		}

		
		foreach ($categories as $category_id => $category) {
			$category->ID = "woocommerce_product_tag__".$category->cat_ID;
			$category->is_woocommerce_product_tag = true;
			
			$permalink = get_category_link($category->cat_ID);

			$permalink = str_replace($pegasaas->get_home_url('', 'https'), "", $permalink);
			$permalink = str_replace($pegasaas->get_home_url('', 'http'), "", $permalink);
			$category->slug = $permalink;
			$category->resource_id = rtrim($permalink, "/")."/";
			
			$category->post_type = "woocommerce_product_tag";
			$category->post_name = $category->cat_niceame;
			$categories["$category_id"] = $category;
			
		}		

		
		return $categories;
		
	}		
	
	static function get_all_woocommerce_product_categories() {
		global $pegasaas;
		
		// add in the categories
		$post_types 		= PegasaasUtils::get_post_types();
		$args = array(
				 'taxonomy'     => 'product_cat',
				 'orderby'      => 'name',
				 'show_count'   => 0,
				 'pad_counts'   => 0,
				 'hierarchical' => 0,
				 'title_li'     => '',
				 'hide_empty'   => 0
		  );		
		
		$category_objects 	= get_categories($args);
		$categories 		= array();
		foreach ($category_objects as $category_obj) {
			$category_id = $category_obj->category_nicename;
			$category_obj->category_post_type = "product";
			$categories["$category_id"] = $category_obj;
		}


		
		foreach ($categories as $category_id => $category) {
			$category->ID = "woocommerce_product_category__".$category->cat_ID;
			$category->is_woocommerce_product_category = true;
			
			$permalink = get_category_link($category->cat_ID);

			$permalink = str_replace($pegasaas->get_home_url('', 'https'), "", $permalink);
			$permalink = str_replace($pegasaas->get_home_url('', 'http'), "", $permalink);
			$category->slug = $permalink;
			$category->resource_id = rtrim($permalink, "/")."/";
			
			$category->post_type = "woocommerce_product_category";
			$category->post_name = $category->cat_niceame;
			$categories["$category_id"] = $category;
			
		}		

		
		return $categories;
		
	}	
	
	static function refresh_all_pages_and_posts() {
		global $pegasaas;
		self::$all_pages_and_posts = array();
		$pegasaas->data_storage->unset_object("all_pages_and_posts");
		PegasaasUtils::load_all_pages_and_posts();
	}
	
	static function get_all_pages_and_posts($use_filter = true) {
		global $pegasaas;
		global $test_debug;
		$start_memory_usage = memory_get_usage();
		$critical_logging = false;
		
		
		$debug 	= false;
		if ($_GET['pegasaas_debug'] == "get_all_pages_and_posts") {
			$debug = true;		
		}
	
		// attempt to grab this data from the object if it already exists
		if (isset(PegasaasUtils::$all_pages_and_posts) && PegasaasUtils::$all_pages_and_posts != NULL && sizeof(PegasaasUtils::$all_pages_and_posts) > 0) {
			if ($use_filter && $pegasaas->interface->results_search_filter != "") {
				if ($test_debug) { 
					PegasaasUtils::log_benchmark("Class Variable \$all_pages_and_posts Exists. Using filtered version.", "debug-li", 1);
				}
				if ($critical_logging) {
					PegasaasUtils::log("APAP: Class Variable Existing and Filtered");
				}
				return PegasaasUtils::filter_all_pages_and_posts();
			} else {
				if ($test_debug) { 
					PegasaasUtils::log_benchmark("Class Variable \$all_pages_and_posts Exists. Using UNfiltered version.", "debug-li", 1);
				}			
				if ($critical_logging) {
					PegasaasUtils::log("APAP: Class Variable Existing and Not Filtered");
				}			
				return PegasaasUtils::$all_pages_and_posts;
			}
		
		// check to see if the array exists within the data storage structure	
		} else if ($pegasaas->data_storage->is_valid("all_pages_and_posts")) {
				if ($test_debug) { 
					PegasaasUtils::log_benchmark("Using Data object for '\$all_pages_and_posts'.", "debug-li", 1);
				}		
				if ($critical_logging) {
					PegasaasUtils::log("APAP: Using Data object", "CRITICAL");
				}				
		 	PegasaasUtils::$all_pages_and_posts = $pegasaas->data_storage->get("all_pages_and_posts");
		}	

		if (isset(PegasaasUtils::$all_pages_and_posts) && PegasaasUtils::$all_pages_and_posts != NULL && sizeof(PegasaasUtils::$all_pages_and_posts) > 0) {
			if ($use_filter && $pegasaas->interface->results_search_filter != "") {
				if ($test_debug) { 
					PegasaasUtils::log_benchmark("Using filtered version of '\$all_pages_and_posts'", "debug-li", 1);
				}			
				if ($critical_logging) {
					PegasaasUtils::log("APAP: Using Filtered Copy");
				}					
				return PegasaasUtils::filter_all_pages_and_posts();
			} else {
				if ($test_debug) { 
					PegasaasUtils::log_benchmark("Using unfiltered version of '\$all_pages_and_posts'", "debug-li", 1);
				}		
				if ($critical_logging) {
					PegasaasUtils::log("APAP: Using Data object");
				}	
				return PegasaasUtils::$all_pages_and_posts;
			}
		}
		
		PegasaasUtils::load_all_pages_and_posts();
		
		
		if ($use_filter && $pegasaas->interface->results_search_filter != "") {
			if ($test_debug) { 
				PegasaasUtils::log_benchmark("Using filtered version of 'all-pages-and-posts'", "debug-li", 1);
			}				
			if ($critical_logging) {
				PegasaasUtils::log("APAP: Using Filtered Copy");
			}				
				return PegasaasUtils::filter_all_pages_and_posts();
		} else {
			if ($test_debug) { 
				PegasaasUtils::log_benchmark("Using unfiltered version of 'all-pages-and-posts'", "debug-li", 1);
			}
			if ($critical_logging) {
				PegasaasUtils::log("APAP: Using unfiltered Copy");
			}	
				return PegasaasUtils::$all_pages_and_posts;
		}
		return PegasaasUtils::$all_pages_and_posts;
	}
	
	static function filter_all_pages_and_posts() {
		global $pegasaas;
		$search_filter = $pegasaas->interface->results_search_filter;
		if ($search_filter == "") {
		  	return PegasaasUtils::$all_pages_and_posts;	
		}
		
		$all_pages_and_posts_copy = PegasaasUtils::$all_pages_and_posts;
		
		foreach ($all_pages_and_posts_copy as $index => $object) {
			if (!strstr($object->post_name, $search_filter)) {
				unset($all_pages_and_posts_copy["$index"]);
			} 
		}
		
		return $all_pages_and_posts_copy;
	}
	
	static function load_all_pages_and_posts() {
		global $pegasaas;
		global $test_debug;
		$critical_logging = true;
		
		$post_types = PegasaasUtils::get_post_types();
		if ($test_debug) { 
			PegasaasUtils::log_benchmark("Populating 'all-pages-and-posts' variable", "debug-li", 1);
		}		
		
		if ($critical_logging) {
			PegasaasUtils::log("APAP: Loading APAP Object");
		}	
		if ($debug) {
			print "<pre class='admin'>";
			var_dump($post_types);
			print "</pre>";		
		}
		
		$all_posts_array = array();
		//PegasaasUtils::$all_pages_and_posts_use_filter = $use_filter;

		
		foreach ($post_types as $post_type) {
			if ($post_type == "page") {
				$order = "ASC";
			} else {
				$order = "DESC";
			}
			$args = array(
				'posts_per_page'	=> 10000,
				'offset'			=> 0,
				'category'			=> '',
				'category_name'		=> '',
				'orderby'			=> 'post_date',
				'order'				=> $order,
				'include'			=> '',
				'exclude'			=> '',
				'meta_key'			=> '',
				'meta_value'		=> '',
				'post_type'			=> $post_type,
				'post_mime_type'	=> '',
				'post_parent'		=> '',
				'author'			=> '',
				'author_name'		=> '',
				'post_status'		=> 'publish',
				'suppress_filters'	=> true 
			);
			
			// get list of pages and posts
			$posts_array = PegasaasUtils::get_posts($args);
			

			if ($post_type == "post") {
				$last_post_date = $posts_array[0]->post_modified;
			}
			if ($debug) {
				print "<pre class='admin'>";
				print "this {$post_type} pages\n";
				var_dump($posts_array);
				print "</pre>";
			}
			
			$all_posts_array = array_merge($all_posts_array, $posts_array);
			
			if ($post_type == "page") {
				$pages_array = $posts_array;
			}
		}

		$home = array();

		//print "<pre class='admin'>showing on front: ".get_option("show_on_front")."</pre>>";
		if (get_option("show_on_front") == "posts" || get_option("show_on_front") == "layout") {
			
			$blog_info = get_bloginfo();
			
			$home[0] = new stdClass();
			$home[0]->ID 		= 0;
			$home[0]->post_date = $last_post_date;
			$home[0]->post_title = $blog_info->name;
			$home[0]->post_name = "/";
			$home[0]->permalink = "/";
			$home[0]->slug 		= "/";
			$home[0]->resource_id = "/";
			$home[0]->is_category = false;
			if ($home[0]->post_type == "") {
			  $home[0]->post_type = "post";	
			}

			
			$all_posts_array 	= array_merge($home, $all_posts_array);
		} else if (get_option("show_on_front") == "page") {
			if (PegasaasUtils::wpml_multi_domains_active()) {
				//print "<pre class='admin'>YY</pre>";
				global $sitepress;
				$wpml_domains = wpml_get_setting( 'language_domains' );
				$default_language = $sitepress->get_default_language();
				$wpml_domains[$lang] = "default";
				//print "<pre class='admin'>";
				//var_dump($wpml_domains);
				//print "</pre>"; 
				$domain_language_count = 0;
				$total_domain_languages = sizeof($wpml_domains);
				
				foreach ($all_posts_array as $x => $page) {
					foreach ($wpml_domains as $lang => $domain) {
						if ($domain == "default") {
							
							$pre_option_page = new WPML_Pre_Option_Page( $sitepress->wpdb, $sitepress, false, $sitepress->get_default_language());
							$page_on_front_id = $pre_option_page->get( 'page_on_front', $sitepress->get_default_language() );
							
						} else {
							$pre_option_page = new WPML_Pre_Option_Page( $sitepress->wpdb, $sitepress, true, $lang );
							$page_on_front_id = $pre_option_page->get( 'page_on_front', $lang );
						
						}
						
						if ($page->ID == $page_on_front_id) { 
							if ($domain == "default") {
							
								$all_posts_array["$x"]->post_name 	= "https://".PegasaasUtils::get_home_domain()."/";
								$all_posts_array["$x"]->permalink 	= "https://".PegasaasUtils::get_home_domain()."/";
								$all_posts_array["$x"]->slug 		= "https://".PegasaasUtils::get_home_domain()."/";
								$all_posts_array["$x"]->resource_id = "/";
								$all_posts_array["$x"]->is_category = false;
								if ($all_posts_array["$x"]->post_type == "") {
									$all_posts_array["$x"]->post_type = "page";	
								}
								
							} else {
								//print "have secondary lang page $lang $page->ID \n";
								//$host = str_replace(array("http://", "https://"), array("", ""), $domain);
								$all_posts_array["$x"]->post_name 	= "https://{$domain}/";
								$all_posts_array["$x"]->permalink 	= "https://{$domain}/";
								$all_posts_array["$x"]->slug 		= "https://{$domain}/";
								$all_posts_array["$x"]->resource_id = "/{$domain}/";
								$all_posts_array["$x"]->is_category = false;
								if ($all_posts_array["$x"]->post_type == "") {
									$all_posts_array["$x"]->post_type = "page";	
								}
								
								//var_dump($pages_array["$x"]);
							}
							unset($wpml_domains[$lang]);
							$domain_lanaguage_count++;
							if ($domain_language_count == $total_domain_languages) {
								break; // break early so as not to go through each 
							}
							//break;

						} 

					}
					
				}
			} else {
			//	print "<pre class='admin'>XX</pre>";
				foreach ($all_posts_array as $x => $page) {
					if ($page->ID == get_option("page_on_front")) {
						$all_posts_array["$x"]->post_name 	= "/";
						$all_posts_array["$x"]->permalink 	= "/";
						$all_posts_array["$x"]->slug 		= "/";
						$all_posts_array["$x"]->resource_id = "/";
						$all_posts_array["$x"]->is_category = false;
						if ($all_posts_array["$x"]->post_type == "") {
							$all_posts_array["$x"]->post_type = "page";	
						}
						break;
					
					} 
				}
			} 
				//var_dump($pages_array);
			
		} else {
			
		}
		
		
		// get categories
		$categories 		= PegasaasUtils::get_all_categories();
	
		// merge the categories into the list of pages/posts
		$all_posts_array	 = array_merge($all_posts_array, $categories);		
 	//print "<pre class='admin'>";
		//var_dump($all_posts_array);
		//print "</pre>";

		if (PegasaasAccelerator::$settings['settings']['woocommerce']) {
			$woocommerce_product_categories = PegasaasUtils::get_all_woocommerce_product_categories();
			$all_posts_array = array_merge($all_posts_array, $woocommerce_product_categories);

			$woocommerce_product_tags = PegasaasUtils::get_all_woocommerce_product_tags();
			$all_posts_array = array_merge($all_posts_array, $woocommerce_product_tags);
		
		}

	//	print "<pre class='admin'>";
		foreach ($all_posts_array as $index => $object) {
			
			unset($object->post_content);
			

			$object->post_name = urldecode($object->post_name);

			
			if ($object->post_name != "/") {
				
				if (@$object->is_category || @$object->is_woocommerce_product_category || @$object->is_woocommerce_product_tag) {

				} else {
					/*
					$permalink = get_permalink($object->ID);
					//var_dump($object);
							//	print "permalink $permalink\n";	

					// in the event that the permalink cannot be mapped, use the post name
					
					//if (!strstr($permalink, $pegasaas->get_home_url('', 'http')) && !strstr($permalink, $pegasaas->get_home_url('', 'https'))) {
					if ($permalink == "") {	
						$permalink = '/'.$object->post_name.'/';
					}
					

					$permalink = str_replace($pegasaas->get_home_url('', 'http'), "", $permalink);
					$permalink = str_replace($pegasaas->get_home_url('', 'https'), "", $permalink);
					//print "<br>permalink: $permalink";
					// needed so that we can store the resource_id is in such a form that 
					// the cache can be both written and read in order to serve the cached page.
					// if this is not done, the .htaccess instructions cannot find the path to the cache
					$slug = urldecode($permalink);
					$resource_id = rtrim($slug,"/")."/"; 
					$resource_id = str_replace("https://", "/", $resource_id);
					$resource_id = str_replace("http://", "/", $resource_id);
					 
				//	print "resource_id = ".$resource_id."\n";
				//	print "slug = ".$slug."\n";
					
					$all_posts_array["$index"]->permalink 	= $permalink;
					$all_posts_array["$index"]->slug 		= $slug;
					$all_posts_array["$index"]->resource_id = $resource_id;
					*/
					$all_posts_array["$index"]->is_category = false;
					$all_posts_array["$index"]->is_woocommerce_product_category = false;
					$all_posts_array["$index"]->is_woocommerce_product_tag = false;
				}
			}
			
			$accelerated = true;

			if ($object->post_type == "tribe_events") {
				$pid = $all_posts_array["$index"]->slug;
			
			} else {
				$pid = $all_posts_array["$index"]->slug;
			}
			

			//$resource_id = PegasaasUtils::get_object_id($object->ID);
			$resource_id = $object->resource_id;
		//	print "resource_id = $resource_id\n";
			$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides");
			//if ($resource_id == "/testing-a.pegasaas.com/" || $resource_id == "/la-home-page/about-nous/") {
			//	print $resource_id."\n";
			//	var_dump($page_level_settings);
			//}			
			// if the object is the default landing page when as a blog (/ == object->ID is zero) then we assume it is accelerated, as
			// there is no way to override the page level settings, so we must go with global settings for that page
			$prioritized = $page_level_settings['prioritized'] == true;
			if ($pegasaas->is_excluded_url($resource_id)) {
			  	$accelerated = false;
			} else	if (!isset($page_level_settings['accelerated']) || $page_level_settings['accelerated'] == false) {
				
				if ($object->post_name == "/" && $object->ID == 0) {
				

					if (PegasaasAccelerator::$settings['settings']['blog']['home_page_accelerated'] == 1) {
						$accelerated = true;
					} else {
						$accelerated = false;
					}
				/*
				} else if ($object->is_category) {
					
					if (PegasaasAccelerator::$settings['settings']['blog']['categories_accelerated'] == 1) {
						$accelerated = true;
					//	print "y acc {$object->ID}<br>";
					
					} else {
					//	print "n acc {$object->ID}<br>";
						$accelerated = false;
					}
				*/
				} else if ($object->is_woocommerce_product_category) {
					
					if (PegasaasAccelerator::$settings['settings']['woocommerce']['status'] == 1 && PegasaasAccelerator::$settings['settings']['woocommerce']['product_categories_accelerated'] == 1) {	
						$accelerated = true;
					} else {
						$accelerated = false;
					}	
				} else if ($object->is_woocommerce_product_tag) {
					if (PegasaasAccelerator::$settings['settings']['woocommerce']['status'] == 1 && PegasaasAccelerator::$settings['settings']['woocommerce']['product_tags_accelerated'] == 1) {
						$accelerated = true;
					} else {
						$accelerated = false;
					}				
				} else {
					$accelerated = false;
				}
			} else {
			
				
			}
		
			
			$all_posts_array["$index"]->staging_mode_disabled = $page_level_settings['staging_mode_page_is_live'] == 1;
			
			$all_posts_array["$index"]->accelerated = $accelerated;
			$all_posts_array["$index"]->prioritized = $prioritized;
			//if ($search_filter != "" && !strstr($object->post_name, $search_filter)) {
			//	unset($all_posts_array["$index"]);
			//   // print "unsetting {$object->post_name}\n";
			//} else {
				//print "leaving  {$object->post_name}\n";
			//}
		}
		
		//print "</pre>";
		//print sizeof($object->post_name);

	
		// sort the pages so that home page is first listed item
		if ($debug) {
			print "<pre class='admin'>";
			print "all posts array1\n";
			foreach ($all_posts_array as $x => $y) {
				print $y->slug." (rid: {$y->resource_id})-- ".$y->ID." -- {$y->accelerated}<br>\n";
			}
			print "</pre>";
		}
		// sort post types
		uasort($all_posts_array, array($pegasaas->utils, 'sort_pages_home_page_first'));
		
		PegasaasUtils::$all_pages_and_posts = $all_posts_array;
		if ($debug) {
			print "<pre class='admin'>";
			print "all posts array2\n";
			foreach ($all_posts_array as $x => $y) {
				print $y->slug." (rid: {$y->resource_id}) -- ".$y->ID." -- {$y->accelerated}<br>\n";
			}
			print "</pre>";
		}
		
		if (sizeof($all_posts_array) > 1000) {
		    $lifetime = 600; // 10 minutes
		
		} else if (sizeof($all_posts_array) > 500) {
			$lifetime = 300; // 5 minutes			
		
		} else if (sizeof($all_posts_array) > 250) {
			$lifetime = 120; // 2 minutes
		} else {
			$lifetime = 60; // 1 minute
		}
		$pegasaas->data_storage->set("all_pages_and_posts", $all_posts_array, $lifetime);
		
		/*
				if (false && $debug) { 
					print "<pre class='admin'>";
					$current_memory_usage = memory_get_usage();
					print "start_memory: ".$start_memory_usage."<br>\n";
					print "current_memory: ".$current_memory_usage."<br>\n";
					print "memory usage:".($current_memory_usage - $start_memory_usage)."<br>";
					print "</pre>";
				}
				*/

		if ($test_debug) {
			print "<pre>";
			var_dump($all_posts_array);
			print "</pre>";
		}
		return $all_posts_array;
	}	

	/* post_id is a numerical post id
	   $should_disable_filters_if_necessary - should only be true if this function is called on single basis rather than in a loop
	*/
	static function get_resource_slug($post_id, $should_disable_filters_if_necessary = false) {
		global $pegasaas;
	
	//	print "get_Resource_Slug ($post_id) ($should_disable_filters_if_necessary)\n";
		
		$wpml_multi_domains_active = PegasaasUtils::wpml_multi_domains_active();
		$wpml_active = PegasaasUtils::wpml_active();

		if ($wpml_multi_domains_active) {
			global $sitepress;
			
			$current_language = $sitepress->get_current_language();
			$default_language = $sitepress->get_default_language();
			$wpml_domains = wpml_get_setting( 'language_domains' );
			$wpml_domains["$default_language"] = PegasaasUtils::get_home_domain();
		
			
			if ($current_language != $default_language) {
				$disable_filters = true;
			}
			
			//$disable_filters = true;
			 
		}	else if ($wpml_active) {
			global $sitepress;
			
			$current_language = $sitepress->get_current_language();
			$default_language = $sitepress->get_default_language();

		}

		
		if ($disable_filters && $should_disable_filters_if_necessary) {
			$page_link_filter = self::get_filter("page_link");
			$post_link_filter = self::get_filter("post_link");

			remove_all_filters("page_link");
			remove_all_filters("post_link");
		}		
		
		$permalink = get_permalink($post_id);
			
		$permalink_url_data = parse_url($permalink);
		$permalink_scheme = $permalink_url_data['scheme'];
		$permalink = str_replace($pegasaas->get_home_url('', 'http'), "", $permalink);
		$permalink = str_replace($pegasaas->get_home_url('', 'https'), "", $permalink);
		
		if ($wpml_multi_domains_active) {
			global $sitepress;
			$post_type     = get_post_type( $post_id );
			$post_language = $sitepress->get_language_for_element( $post_id, 'post_' . $post_type );

			if ($post_language != $default_language && 
				$permalink != "/".$wpml_domains["{$post_language}"]."/"
				&& !strstr($permalink, "://") ) {

				$permalink	   = $permalink_scheme."://".$wpml_domains["{$post_language}"].$permalink;

			} else if ($post_language == $default_language && !strstr($permalink, "://") ) {

				$permalink	   = $permalink_scheme."://".$wpml_domains["{$post_language}"].$permalink;
			} else {
				if (strstr($permalink, "://") && $post_language != $current_language && !strstr($permalink, $wpml_domains["$post_language"])) {

					$permalink = str_replace($permalink_scheme."://".$wpml_domains["{$current_language}"], $permalink_scheme."://".$wpml_domains["{$post_language}"], $permalink);
				} 

			}

		// handles conditions of subdirectory handling of languages (example /fr/my-page/)
		} else if ($wpml_active) {

			$post_type     = get_post_type( $post_id );
			$post_language = $sitepress->get_language_for_element( $post_id, 'post_' . $post_type );
			if ($post_language != $current_language) {
				$sitepress->switch_lang( $post_language );
				$permalink = get_permalink($post_id);
				$sitepress->switch_lang( $current_language );
				//print "post type language for post ($post_language) and current language is {$current_language} and new permlink = $new_permalink <br>";
				$permalink_url_data = parse_url($permalink);
				$permalink_scheme = $permalink_url_data['scheme'];
				$permalink = str_replace($pegasaas->get_home_url('', 'http'), "", $permalink);
				$permalink = str_replace($pegasaas->get_home_url('', 'https'), "", $permalink);			
			}
			
		}
		
		if ($disable_filters && $should_disable_filters_if_necessary) {
			self::set_filter("page_link", $page_link_filter);
			self::set_filter("post_link", $post_link_filter);	
		}
		$slug = $permalink;
		$slug = urldecode($slug);
		return $slug;
	}
	
	
	function get_post_object($resource_id) {
		$all_pages_and_posts = PegasaasUtils::get_all_pages_and_posts();
		
		foreach ($all_pages_and_posts as $object) {
			if ($object->resource_id == $resource_id) {
				return $object;
			}
		}
		
	}
	
	static function get_pages_and_posts_array_position($resource_id) {
		$all_pages_and_posts = PegasaasUtils::get_all_pages_and_posts();
		
		foreach ($all_pages_and_posts as $index => $object) {
			if ($object->resource_id == $resource_id) {
				return $index;
			}
		}
		return NULL;
	}
	
	function pegasaas_backload_all_pages_and_posts() {
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			PegasaasUtils::get_all_pages_and_posts();
			print 1;
		} else {
			print -1;
		}		
		
	 	wp_die();
		
	}
	
	static function match_email($string, $email) {
		$emails = explode(",", $string);
		foreach ($emails as $find) {
			if (strstr($find, "*")) {
				$pattern = "/".str_replace("*", ".*?", $find)."\$/i";
				$found = preg_match($pattern, $email);
				
			} else {
				$pattern = "/^{$find}\$/i";
				$found = preg_match($pattern, $email);
				

			}
			if ($found) {
				
				return true;
			}
		}
		return false;
	}
	
	static function get_auto_pilot_feature_status($feature) {
		$auto_pilot_setting = PegasaasAccelerator::$settings['settings']['speed_configuration']['status'];
		
		// google font optimization (combining fonts into single executable, is available in all but the lowest setting)
		if ($feature == "google_fonts") {
			if ($auto_pilot_setting >= 2) {
				return 1;
			} else {
				return 0;
			}
			
		// deferral of render blocking css is only enabled in supersonic, hypersonic, and beast mode
		} else if ($feature == "defer_render_blocking_css") {
			if ($auto_pilot_setting >= 2) {
				return 1;
			} else {
				return 0;
			}			
		
		// deferral of render blocking js is only enabled in supersonic, hypersonic, and beast mode
		} else if ($feature == "defer_render_blocking_js") {
			if ($auto_pilot_setting >= 2) {
				return 1;
			} else {
				return 0;
			}			

		// inject critical css is only enabled in supersonic, hypersonic, and beast mode
		} else if ($feature == "inject_critical_css") {
			if ($auto_pilot_setting >= 2) {
				return 1;
			} else {
				return 0;
			}			
			
		// essential css is a part of the critical css suite of features, and is only enabled in supersonic, hypersonic, and beast mode
		} else if ($feature == "essential_css") {
			if ($auto_pilot_setting >= 2) {
				return 1;
			} else {
				return 0;
			}			
		
		
		} else {
			return 1;
		}
	}
	
	static function get_feature_status($feature) {
		global $pegasaas;
		
		if (PegasaasAccelerator::$settings['settings']['speed_configuration']['status'] == 0) {
			$page_level_settings = PegasaasUtils::get_object_meta(PegasaasUtils::get_object_id(), "accelerator_overrides");

			$global_setting 	= PegasaasAccelerator::$settings['settings']["{$feature}"]['status'];
			$page_level_setting = $page_level_settings["{$feature}"];
			$override = PegasaasAccelerator::$settings['settings']["{$feature}"]['override'];
		} else {
			$global_setting = self::get_auto_pilot_feature_status($feature);
		}
		
		if ($override != "") {
			return $override;
		} else 	if ($page_level_setting == "") {
			return $global_setting;
		} else {
			return $page_level_setting;
		}
	}
	
	
	function optimize_database($force_optimize = false) {
		$one_day 					= 86400;
		$when_db_last_optimzed 		= get_option("pegasaas_when_db_last_optimzed", 0);
		$time_since_last_optimized 	= time() - $when_db_last_optimzed;
		
		if ($_POST['c'] == 'optimze_db' || $time_since_last_optimized > $one_day || $force_optimize) {
			global $wpdb;
			update_option("pegasaas_when_db_last_optimzed", time());
			
			$wpdb->query("OPTIMIZE TABLE `{$wpdb->prefix}options`");
			$wpdb->query("OPTIMIZE TABLE `{$wpdb->prefix}postmeta`");

			$wpdb->query("REPAIR TABLE `{$wpdb->prefix}options`");
			$wpdb->query("REPAIR TABLE `{$wpdb->prefix}postmeta`");
	
			
		} 
	}

	
	function is_avada_theme() {
		$theme_info = wp_get_theme();
	
		return strstr($theme_info->get("Name"), "Avada");
	}	
	
	function is_themify_theme() {
		$theme_info = wp_get_theme();
	
		return $theme_info->get("Name") == "Themify Ultra";
	}

	function is_thrive_theme() {
		$theme_info = wp_get_theme();
		// thrive-visual-editor
		// thrive-clever-widgets
		// thrive-ovation
		// thrive-leads
		return $this->does_plugin_exists_and_active("thrive-visual-editor");
	}

	static function does_plugin_exists_and_active($pluginName='') {
		if (!function_exists("get_plugin_data")) {
			include(ABSPATH."wp-admin/includes/plugin.php");
		}			
		
		$debug=false;
		$plugins= get_plugins();
		switch($pluginName){
			case 'pegasaas-accelerator':	$pluginPath='pegasaas-accelerator/pegasaas-accelerator.php';		break;
			case 'buddypress': 				$pluginPath='buddypress/class-buddypress.php'; 						break;
			case 'wp-jobs-manager': 		$pluginPath='wp-job-manager/wp-job-manager.php'; 					break;
			case 'clear-cache-for-widgets': $pluginPath='clear-cache-for-widgets/clear-cache-for-widgets.php'; 	break;
			case 'w3-total-cache': 			$pluginPath='w3-total-cache/w3-total-cache.php'; 					break;
			case 'wp-super-cache': 			$pluginPath='wp-super-cache/wp-cache.php'; 							break;
			case 'wp-rocket': 				$pluginPath='wp-rocket/wp-rocket.php'; 								break;
			case 'wp-fastest-cache': 		$pluginPath='wp-fastest-cache/wpFastestCache.php'; 					break;
			case 'lightspeed-cache': 		$pluginPath='litespeed-cache/litespeed-cache.php'; 					break;
			case 'hyper-cache': 			$pluginPath='hyper-cache/plugin.php'; 								break;
			case 'comet-cache': 			$pluginPath='comet-cache/comet-cache.php'; 							break;
			case 'sg-cachepress': 			$pluginPath='sg-cachepress/sg-cachepress.php'; 						break;
			case 'beaver-builder': 			$pluginPath='bb-plugin/fl-builder.php'; 							break;
			case 'beaver-builder-lite': 	$pluginPath='beaver-builder-lite-version/fl-builder.php'; 			break;
			case 'pantheon-advanced-page-cache' : $pluginPath = 'pantheon-advanced-page-cache/pantheon-advanced-page-cache.php'; break;
			case 'wordlift' : 				$pluginPath = 'wordlift/wordlift.php'; break;
			case 'instagram-feed' : 		$pluginPath = 'instagram-feed/instagram-feed.php'; break;
			case 'thirstyaffiliates' : 		$pluginPath = 'thirstyaffiliates/thirstyaffiliates.php'; break;
			case 'jetpack' : 				$pluginPath = 'jetpack/jetpack.php'; break;
			case 'thrive-visual-editor' : 	$pluginPath = 'thrive-visual-editor/thrive-visual-editor.php'; break;
			case 'thrive-clever-widgets' : 	$pluginPath = 'thrive-clever-widgets/thrive-clever-widgets.php'; break;
			case 'async-javascript' : 		$pluginPath = 'async-javascript/async-javascript.php'; break;
			case 'piio-image-optimization' : 		$pluginPath = 'piio-image-optimization/piio-image-optimization.php'; break;
			case 'accelerated-moblie-pages' : 		$pluginPath = 'accelerated-mobile-pages/accelerated-moblie-pages.php'; break;
			case 'accelerated-mobile-pages' : 		$pluginPath = 'accelerated-mobile-pages/accelerated-mobile-pages.php'; break;
			case 'woocommerce' : 		$pluginPath = 'woocommerce/woocommerce.php'; break;
			case 'wpe-advanced-cache-options' : 		$pluginPath = 'wpe-advanced-cache-options/wpe-advanced-cache.php'; break;
			case 'clear-w3tc-cache' : 		$pluginPath = 'clear_w3tc_cache/clear_w3tc_cache.php'; break;
			case 'ecwid-clear-cache-w3tc-k8s' : 		$pluginPath = 'ecwid-clear-cache-w3tc-k8s/index.php'; break;
			case 'redirection' : 			$pluginPath = 'redirection/redirection.php'; break;
			case 'alids' : 					$pluginPath = 'alids/alids.php'; break;
			case 'rate-my-post': 			$pluginPath = 'rate-my-post/rate-my-post.php'; break;
			case 'user-registration': 		$pluginPath = 'user-registration/user-registration.php'; break;
			case 'elementor': 				$pluginPath = 'elementor/elementor.php'; break;
			case 'elementor-pro': 				$pluginPath = 'elementor-pro/elementor.php'; break;
			case 'wpml': 					$pluginPath = 'sitepress-multilingual-cms/sitepress.php'; break;
			case 'wp-optimize': 			$pluginPath = 'wp-optimize/wp-optimize.php'; break;
			case 'woocommerce-multilingual':$pluginPath = 'woocommerce-multilingual/wpml-woocommerce.php'; break;
			case 'geotargetingwp':			$pluginPath = 'geotargetingwp/geotargetingwp.php'; break;
			case 'contact-form-7':			$pluginPath = 'contact-form-7/wp-contact-form-7.php'; break;
				
			default: 'Error: Plugin: '.		$pluginName.' Not Found'.var_export(get_plugins(),true ); 			return false;
		}
		if( array_key_exists($pluginPath, $plugins) ){
			return is_plugin_active($pluginPath);
		} else {
			if($debug){print 'Plugin: '.$pluginName.' is not Installed'."\n";}
			//var_export(get_plugins() );
		}
	}	
		
	function is_mobile_user_agent() {
		$user_agent = $_SERVER['HTTP_USER_AGENT'];

		if (preg_match('/(android|blackberry|googlebot-mobile|iemobile|ipad|iphone|ipod|opera mobile|palmos|webos)/i',$user_agent)) {
			return true;
		}
		return false;
		
	}
	
	
	function clear_all_semaphores() {
		$this->release_semaphore("accelerator_overrides");
		$this->release_semaphore("auto_accelerate_pages");
		$this->release_semaphore("pegasaas_accelerator_pagespeed_benchmark_score_requests");
		$this->release_semaphore("pegasaas_accelerator_pagespeed_score_requests");
		$this->release_semaphore("pegasaas_accelerator_pagespeed_score_request_tokens");
		$this->release_semaphore("pegasaas_cache_map");
		$this->release_semaphore("pegasaas_critical_css");
		$this->release_semaphore("pegasaas_deferred_js");
		$this->release_semaphore("pegasaas_process_pagespeed_benchmark_score_request");
		$this->release_semaphore("pegasaas_process_pagespeed_score_request");
		$this->release_semaphore("pegasaas_pending_critical_css_request");
		$this->release_semaphore("submit_benchmark_requests");
		$this->release_semaphore("submit_scan_requests");
		
		
		$locked_semaphores = get_option("pegasaas_semaphores", array());
		foreach ($locked_semaphores as $semaphore_name => $when) {
			$this->release_semaphore($semaphore_name);
		}
		
		
		$locked_semaphores = get_option("pegasaas_semaphores", array());
		foreach ($locked_semaphores as $semaphore_name => $when) {
			$this->log("Exising Semphore after clear: $semaphore_name");
		}
	}
	
	static function memory_within_limits() {
	 	$debug = false;
		$memory_limit 			= self::get_memory_limit();
		$memory_peak_usage 		= memory_get_peak_usage();
		$memory_usage 			= memory_get_usage();
		$difference 			= $memory_limit - $memory_peak_usage;
		$maximum_usage			= $memory_limit * .90; // set the maximum to 90% of full
		
		$memory_within_limits	= $maximum_usage > $memory_peak_usage;
		
		return $memory_limit == -1 || $memory_within_limits;	
	}
	
	static function get_next_memory_limit() {
		$memory_limit = self::get_memory_limit();
		$memory_limit = $memory_limit * 2;
		
		$memory_limit = $memory_limit / 1024 / 1024;
		
		$memory_limit = $memory_limit."M";
		
		return $memory_limit;
		
	} 
	
	static function get_memory_limit() {
		$memory_limit = ini_get('memory_limit');
		if ($memory_limit == -1) {
			$memory_limit = "128M"; // set a safe limit as "unlimited" can crash a server
		}
		
		if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
			if ($matches[2] == 'T') {
				$memory_limit = $matches[1] * 1024 * 1024 * 1024 * 1024;
			} else if ($matches[2] == 'G') {
				$memory_limit = $matches[1] * 1024 * 1024 * 1024;
			} else if ($matches[2] == 'M') {
				$memory_limit = $matches[1] * 1024 * 1024;
			} else if ($matches[2] == 'K') {
				$memory_limit = $matches[1] * 1024; 
			}
		}
		
		return $memory_limit;
		
	}

	static function is_in_list($subject, $list_items, $exact = false) {
		$is_in_list = false;
		foreach ($list_items as $item) {
			if ($exact) { 
				if ($item != "" && $subject == $item) {
					$is_in_list = true;
					return $is_in_list;
				}		
			} else {
				if ($item != "" && strstr($subject, $item)) {
					$is_in_list = true;
					return $is_in_list;
				}	
			}

		}
		return $is_in_list;
		
	}	
	
	function newer_version($version1, $version2) {
		$version1Data = explode(".", $version1);
		$version2Data = explode(".", $version2);
	  
		if (!isset($version1Data[1])) {
			$version1Data[1] = 0;
		}
		if (!isset($version2Data[1])) {
			$version2Data[1] = 0;
		}
		if (!isset($version1Data[2])) {
			$version1Data[2] = 0;
		}
		if (!isset($version2Data[2])) {
			$version2Data[2] = 0;
		}

		if (!isset($version1Data[3])) {
			$version1Data[3] = 0;
		}
		if (!isset($version2Data[3])) {
			$version2Data[3] = 0;
		}
	  
		if ($version1Data[0] < $version2Data[0]) {
			return false;
		} else if ($version1Data[0] > $version2Data[0]) {
			return true;
		} else {
			if ($version1Data[1] < $version2Data[1]) {
				return false;
			} else if ($version1Data[1] > $version2Data[1]) {
				return true;
			} else {
				if ($version1Data[2] < $version2Data[2]) {
					return false;
				} else if ($version1Data[2] > $version2Data[2]) {
			 
					return true;
				} else {
					if ($version1Data[3] > $version2Data[3]) {
				
					return true;  
			  
					}
				}
			}
		}
		return false;
	}		
	
	static function sort_font_families($a, $b) {
		$style['normal'] = 0;
		$style['italic'] = 1;
		
		$weight['400'] = 0;
		$weight['300'] = 1;
		$weight['200'] = 2;
		$weight['100'] = 3;
		$weight['500'] = 4;
		$weight['600'] = 5;
		$weight['700'] = 8;
		
		$a_style = $a['style'];
		$b_style = $b['style'];
		
		$a_style_value = $style["{$a_style}"];
		$b_style_value = $style["{$b_style}"];
		
		$a_weight = $a['weight'];
		$b_weight = $b['weight'];
		
		$a_weight_value = $weight["{$a_weight}"];
		$b_weight_value = $weight["{$b_weight}"];
		
		if ($a_style == $b_style) {
			if ($a_weight_value == $b_weight_value) {
				return 0;
			} else {
				return ($a_weight_value < $b_weight_value ? -1 : 1);
			}
		} else {
			return ($a_style < $b_style ? -1 : 1);
		}
		
		
	}
	
	
	function get_total_page_build_time($output = false, $marker = "total build") {
		$total_build_time = array_sum(explode(' ', microtime()))-$GLOBALS['pegasaas_page_start_time'];
		
		$total_build_time = number_format($total_build_time, 3);
		
		if ($total_build_time < 1) {
			$total_build_time = $total_build_time * 1000;
			$total_build_time .= "ms"; 
		} else {
			$total_build_time .= "s";
		}
		
		$comment = "\n<!-- pegasaas://accelerator - {$marker} time {$total_build_time} -->";			
		if (!$output) { 
			if (!headers_sent()) {
				header("X-Pegasaas-Build-Time: {$marker}, {$total_build_time}");
			}
		} else {
			return $comment;
		}
	
	}	
	
	function get_memory_use_percentage() {
		$memory_limit 		= $this->get_memory_limit();
		$memory_peak_usage 	= memory_get_peak_usage();
		
		return $memory_peak_usage / $memory_limit * 100;
	}
	
	/**
	 * Creates a log-normal distribution and finds the complementary
	 * quantile (1-percentile) of that distribution at value. All
	 * arguments should be in the same units (e.g. milliseconds).
	 * as provided in the google spreadsheet to calculate GPSI v5
	 *
	 * @param {number} median
	 * @param {number} falloff
	 * @param {number} value
	 * @return The complement of the quantile at value.
	 * @customfunction
	 */
	
	static function quantile_at_value($median, $falloff, $value) {  
	  $location = log($median);
      
		
	  $log_ratio = log($falloff / $median);
	  $shape = sqrt(1 - 3 * $log_ratio - sqrt(($log_ratio - 3) * ($log_ratio - 3) - 8)) / 2;

	  $standardized_x = (log($value) - $location) / (sqrt(2) * $shape);
		
		
	  return (1 - self::internal_erf_($standardized_x)) / 2;
	}
	
	/**
	 * Approximates the Gauss error function, the probability that a random variable
	 * from the standard normal distribution lies within [-x, x]. Moved from
	 * traceviewer.b.math.erf, based on Abramowitz and Stegun, formula 7.1.26.
	 * as found in the Google spreadsheet for calculating GPSI v5
	 * @param {number} x
	 * @return {number}
	 */
	static function internal_erf_($x) {

	  $sign = $x < 0 ? -1 : 1;
	  $x = abs($x);

	  $a1 = 0.254829592;
	  $a2 = -0.284496736;
	  $a3 = 1.421413741;
	  $a4 = -1.453152027;
	  $a5 = 1.061405429;
	  $p = 0.3275911;
	  $t = 1 / (1 + $p * $x);
	  $y = $t * ($a1 + $t * ($a2 + $t * ($a3 + $t * ($a4 + $t * $a5))));
  		return $sign * (1 - $y * exp(-$x * $x));
	}
}