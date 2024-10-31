<?php
class PegasaasAPI {
	
	function __construct() { }

   /**
     * Check or set the validity of an API key
     *
     * @param string $api_key -- an alpha-numeric string
     * @param boolean $die -- whether to exit with a response code
     *
     * @return nil
     */		
	function pegasaas_api_key_check($api_key = "", $die = true) {
		global $pegasaas;

		if ($_POST['api_key_type'] == "trial") {
			$api_key = "request-new-trial-key";
			$request_key = true;
			if (strlen($_POST['api_request_first_name']) < 3 || strlen($_POST['api_request_last_name']) < 3) {
				PegasaasAccelerator::$settings['reason'] = "Your Name Is Required For The Free Trial";
				return;
			}
			
			$email = filter_var($_POST['api_request_email'], FILTER_SANITIZE_EMAIL);
			if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $email == "") {
				PegasaasAccelerator::$settings['reason'] = "Invalid Email Address Provided";
				return;
			}
			
		} else if ($_POST['api_key_type'] == "quick") {
			
		  	$api_key = "request-new-quick-key";
			$request_key = true;
			$email = filter_var($_POST['api_request_email'], FILTER_SANITIZE_EMAIL);
			if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $email == "") {
				PegasaasAccelerator::$settings['reason'] = "Invalid Email Address Provided";
				return;
			}
		} else {
			if ($api_key == "") {
				$api_key = $_POST['api_key'];
			}

			if ($api_key == "") {
				return;
			}
		} 
		if ($request_key) {
			
			$response = $this->post(array("api_key" => $api_key, 
										  "email" => $_POST['api_request_email'],
										  "promo_code" => $_POST['api_promo_code'],
										  "plugin_version" => $pegasaas->get_current_version(),
										  "speed_configuration" => $_POST['speed_configuration'],
										  "display_mode" => $_POST['interface_type'],
										  "display_level" => $_POST['interface_level']), array('timeout' => 30));
		} else {
			
			$response = $this->post(array("api_key" => $api_key,
										  "speed_configuration" => $_POST['speed_configuration'],
										  "display_mode" => $_POST['interface_type'],
										  "display_level" => $_POST['interface_level']), array('timeout' => 30)); 
		}
		// $response = '{"api_error":"cURL error 28: Operation timed out after 2001 milliseconds with 0 out of -1 bytes received"}';
		
		if ($_GET['pegasaas-debug'] == "api-key-check") { 
		    print "<pre>";
			var_dump($response);
			print "</pre>";
		}
		if ($response == "") {
			PegasaasAccelerator::$settings['last_api_check'] = time();
			PegasaasAccelerator::$settings['error'] = "Unable to connect to pegasaas API Key Server";
			if (!$request_key) {
				PegasaasAccelerator::$settings['status'] = 1;
			} 	
			$response = json_encode(array("status" => -2, "reason" => "Unable to connect to API Key Server"));
		} else { 
		
			$data = json_decode($response, true);
		
			if ($data['api_error'] != "") {
				
				PegasaasAccelerator::$settings['last_api_check'] = time();
				PegasaasAccelerator::$settings['error'] 	= "Unable to connect to pegasaas API Key Server";
				PegasaasAccelerator::$settings['reason'] 	= "Unable to connect to pegasaas API Key Server -- please try again.";
				PegasaasAccelerator::$settings['reason_short'] = "timeout";
				$response = json_encode(array("status" => -2, "reason" => "Unable to connect to API Key Server"));
			} else if ($data['status'] == '1') {
				
				PegasaasAccelerator::$settings = $data;
				PegasaasAccelerator::$settings['last_api_check'] = time();
				$response = json_encode(array("status" => 1, "reason" => "Successful Sync - Enabled"));
							
			} else if ($data['status'] == '0') {
				PegasaasAccelerator::$settings = $data;
			  	PegasaasAccelerator::$settings['status'] = 0;	
			  	PegasaasAccelerator::$settings['installation_id'] = $data['installation_id'];	
			  	PegasaasAccelerator::$settings['api_key'] = $api_key;
			  	PegasaasAccelerator::$settings['last_api_check'] = time();
				$response = json_encode(array("status" => 0, "reason" => "Successful Sync - Disabled"));
		   		
			} else {
				// possible error condition, lets not change the status
				$response = json_encode(array("status" => -1, "reason" => "Unknown"));
				PegasaasAccelerator::$settings['reason'] = $data['reason'];
			
				PegasaasAccelerator::$settings['last_api_check'] = time();
			}
		}
		$pegasaas->set_gzip(PegasaasAccelerator::$settings['settings']['gzip_compression']['status']);
		$pegasaas->set_browser_caching(PegasaasAccelerator::$settings['settings']['browser_caching']['status']);

		update_option("pegasaas_settings", PegasaasAccelerator::$settings);

		//if ($_POST['interface_type'] != "") {
		//	$pegasaas->set_feature("display_mode", $_POST['interface_type']);
		//}
	
		if ($die) { 
			print $response; 
			wp_die();
		}
	}
	
	
	function is_submission_method_blocking() {
		$method = PegasaasAccelerator::$settings['settings']['api_submit_method']['status'];
		
		if ($method == "" || $method == 0) {
			$non_blocking_until = get_option("pegasaas_accelerator_api_non_blocking_until", 0);
			if ($non_blocking_until < time()) {
				return true; // do a blocking request if we've passed by any non-blocking window
			} else {
				return false; // do non-blocking until we've reached the window
			}
	
		} else if ($method == 1) {
			return true;
		} else {
			return false;
		}
	}
	
	function is_submission_method_variable() {
		$method = PegasaasAccelerator::$settings['settings']['api_submit_method']['status'];
		
		if ($method == "" || $method == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	function get_optimization_timeout() {
		if (isset(PegasaasAccelerator::$settings['settings']['api_submit_method']['optimization_request_timeout'] ) && 
			PegasaasAccelerator::$settings['settings']['api_submit_method']['optimization_request_timeout'] > 0 &&
			PegasaasAccelerator::$settings['settings']['api_submit_method']['optimization_request_timeout'] < 20) {
			return PegasaasAccelerator::$settings['settings']['api_submit_method']['optimization_request_timeout'] * 1;
		} else {
			return PEGASAAS_ACCELERATOR_API_OPTIMIZATION_TIMEOUT;
		}	
	}
	
	function get_general_api_request_timeout() {
		if (isset(PegasaasAccelerator::$settings['settings']['api_submit_method']['general_request_timeout'] ) && 
			PegasaasAccelerator::$settings['settings']['api_submit_method']['general_request_timeout'] > 0 &&
			PegasaasAccelerator::$settings['settings']['api_submit_method']['general_request_timeout'] < 60) {
			return PegasaasAccelerator::$settings['settings']['api_submit_method']['general_request_timeout'] * 1;
		} else {
			return PEGASAAS_ACCELERATOR_API_TIMEOUT;
		}	
	}	
	
	function test_http_auth($test_url, $force_check = false) {
		$one_day = 60 * 60 * 24;
		
		$when_last_checked = get_option("pegasaas_when_http_auth_last_checked", array());
		$last_check_status = get_option("pegasaas_http_auth_status", array());

		if ($force_check || $when_last_checked["{$test_url}"] < time() - $one_day) {
			$response = wp_remote_request($test_url);
			$last_check_status["$test_url"] = 0;
			if (is_a($response, "WP_Error")) {
					$last_check_status["$test_url"] -1;
			} else {
					$http_response = $response['http_response'];
					$response_obj = $http_response->get_response_object();
					$httpcode = $response_obj->status_code;
					if ($httpcode == 401) {
						$last_check_status["$test_url"] = 1;
					}
			}	
			$when_last_checked["{$test_url}"] = time();
			update_option("pegasaas_http_auth_status", $last_check_status);
			update_option("pegasaas_when_http_auth_last_checked", $when_last_checked);
				
		} 
		
		//delete_option("pegasaas_http_auth_status");
		//delete_option("pegasaas_when_http_auth_last_checked");
		
		return $last_check_status["{$test_url}"];
	}
	
	
	function assert_server_ip($ip) {
		global $pegasaas;
		if ($ip == "") {
			$pegasaas->utils->log("Blank Server IP", "api");
			return;
		} else if (!filter_var($ip, FILTER_VALIDATE_IP)) {
			$pegasaas->utils->log("Invalid Server IP: {$ip}", "api");
			return;			
		}
		
		$ip_list = PegasaasAccelerator::$settings['settings']['multi_server']['ips'];
		
		if (!in_array($ip, $ip_list)) {
			array_push($ip_list, $ip);
			PegasaasAccelerator::$settings['settings']['multi_server']['ips'] = $ip_list;
			update_option("pegasaas_settings", PegasaasAccelerator::$settings);
		}
		
	}
	
	function get_api_ips() {
		$when_last_fetched = get_option("pegasaas_when_ips_last_checked", 0);
		$ip_list 			= get_option("pegasaas_api_ips", array());

		$one_month = 604800;

		
		if ($when_last_fetched < time() - $one_month || sizeof($ip_list) == 0 || (isset($_GET['fetch-api-list']))) {
			
			$ip_list_url = "https://ip.pegasaas.io/";
			$response = wp_remote_request($ip_list_url, array("method" => "GET"));
			
			if (is_a($response, "WP_Error")) {
				$ip_list = array(gethostbyname("api.pegasaas.io"));
			} else {
				$http_response = $response['http_response'];
				if ($http_response) { 
					$ip_list =  explode("\n", str_replace("\r", "", trim($http_response->get_data())));
				} else {
					$ip_list = array(gethostbyname("api.pegasaas.io"));
				}
			}
			
			
			update_option("pegasaas_api_ips", $ip_list);
			update_option("pegasaas_when_ips_last_checked", time());
			
		}
		
		return $ip_list;
	}
	
	function notify_of_possible_initialization_problem() {
		$last_notify = get_option("pegasaas_last_issue_notify_time", 0);
		// only notify if we haven't notified recently (within the last hour)
		if ($last_notify < time() - 3600) {
			update_option("pegasaas_last_issue_notify_time", time());
			$response = $this->post(array("command" => "notify-possible-initialization-issue"), 
									array("timeout" => 10, "blocking" => true));
			
		}
	}

	
	function notify_of_bad_htaccess($htaccess_instructions, $error_code) {
		
			
			$response = $this->post(array("command" => "notify-of-bad-htaccess", "htaccess_instructions" => $htaccess_instructions, "error_code" => $error_code), 
									array("timeout" => 10, "blocking" => false));
			
		
	}	
	
	function notify_limit_approaching() { 
		$last_notify = get_option("pegasaas_last_limit_notify_time", 0);
		// only notify if we haven't notified recently (within the last hour)
		if ($last_notify == 0) {
			update_option("pegasaas_last_limit_notify_time", time());
			$response = $this->post(array("command" => "plan-limit-approaching"), 
									array("timeout" => 10, "blocking" => true));
			
		}		
		
	}
	
	function pickup_queued_image_data() {
		global $pegasaas;
		
		$queued_requests 						= get_option('pegasaas_pending_image_data_request', array());
		if ($_POST['c'] == "pickup-pending-requests") {
			$force_pickup = true;
		}

		if (sizeof($queued_requests) > 0 || $force_pickup) {
		
			$response = $this->post(array("command" => "pickup-image-data"));
			update_option("pegasaas_last_pickup", time());
			if ($response == "") {

			} else {
				$data = json_decode($response, true);

				if (is_array($data['image_data_requests'])) {
					$pegasaas->utils->log("----- pickup_queued_image_data start -----", "pickup_queued_requests");

					foreach ($data['image_data_requests'] as $request_id => $request) { 
						$resource_id 	= $request['resource_id'];
						$image_data 	= $request['image_data'];

						$pegasaas->process_image_data($image_data, $resource_id);
					}
				}				
			}
		} 
	}
	function pickup_queued_optimizations() {
		global $pegasaas;
		$debug = false;
		
		$window = 60 * 10;
	
		$has_queued_requests = 	$pegasaas->db->has_record("pegasaas_api_request", array("request_type" => "optimization-request",
																						    "time" => array("comparison" => "<", 
																											"value" => date("Y-m-d H:i:s", time() - $window))));

		if ($_POST['c'] == "pickup-pending-requests" || $_GET['c'] == "pickup-pending-requests") {
			$force_pickup = true;
		}
		if ($_GET['pegasaas-debug'] == 'pickup-optimization-requests') {
			$debug = true;
		}

		if ($has_queued_requests || $force_pickup) {
			$post_args = array("command" => "pickup-optimization-requests");
		
			if ($has_queued_requests) {
			
				$queued_requests = $pegasaas->db->get_results("pegasaas_api_request", array("request_type" => "optimization-request",
																						    "time" => array("comparison" => "<", 
																											"value" => date("Y-m-d H:i:s", time() - $window))));
				if (is_array($queued_requests)) {
					foreach ($queued_requests as $row) {
						$request_id = $row->request_id;
					   $post_args["pending"]["{$request_id}"] = array("request_id" => $row->request_id, "wp_rid" => $row->resource_id);
					}
					
				}
			}
			
			
			$response = $this->post($post_args, array("timeout" => 20));
			
			update_option("pegasaas_last_pickup", time());
			
			if ($response == "") {

			} else {
				$data = json_decode($response, true);

	
				if (is_array($data['optimization_requests'])) {
					$pegasaas->utils->log("----- pickup_queued_optimization_requests start -----", "pickup_queued_requests");
					
					foreach ($data['optimization_requests'] as $request) { 
						$pegasaas->utils->log("have resource id {$resource_id}", "pickup_queued_requests");
						$resource_id 	= $request['resource_id'];
						$html 			= $request['data'];
						$secondary_files = $request['secondary'];
						$requested_url 	= $request['requested_url'];
						$request_id = $request['request_id'];
						//$pegasaas->utils->log("have resource id {$resource_id}", "pickup_queued_requests");
						//$pegasaas->utils->log("with request id {$request_id}", "pickup_queued_requests");

						if ($debug) {
							print "request id: ".$request_id."<br>";
							print "resource id: ".$resource_id."<br>";
							print "html length: ".strlen($html)."<br><br>";
						}
						
						// save secondary files
						if (sizeof($secondary_files) > 0) {
							foreach ($secondary_files as $secondary_file) {
								$filename = $secondary_file['filename'];
								//print "filename: $filename<br>";
								$data = $secondary_file['data'];
								$resource_path = $pegasaas->utils->strip_query_string($resource_id);

								// added Jan 29, 2018 to handle non latin characters
								$resource_path = urlencode($pegasaas->utils->strip_query_string($resource_id));

								$path =  PEGASAAS_CACHE_FOLDER_PATH."{$resource_path}";
								if (!is_dir($path)) { 
									$pegasaas->cache->mkdirtree($path, 0755, true);
								}
								$fp = @fopen(PEGASAAS_CACHE_FOLDER_PATH."".$filename, "w");


								@fwrite($fp, $data);


								@fclose($fp);				
							}
						}
						
						$pegasaas->process_optimization($html, $resource_id, $request_id, $requested_url);
						
					}
				}				
			}
		} 
	}
	
	function fetch_finished_optimization($request_id) {
		global $pegasaas;
		
		$response = $this->post(array("command" => "fetch-optimization-request",
									  "request_id" => $request_id
										 ), 
								array("timeout" => 20,
									  "blocking" => true,
									  "headers" => array("x-pegasaas-endpoint" => 'api')));
									 
		return json_decode($response, true);
		
	}
	
	function fetch_finished_pagespeed_scan($request_id) {
		global $pegasaas;
		
		$response = $this->post(array("command" => "fetch-pagespeed-request",
									  "request_id" => $request_id
										 ), 
								array("timeout" => 20,
									 "blocking" => true));
		return json_decode($response, true);
		
	}	

	function fetch_finished_pagespeed_baseline_scan($request_id) {
		global $pegasaas; 
		
		$response = $this->post(array("command" => "fetch-pagespeed-baseline-request",
									  "request_id" => $request_id
										 ), 
								array("timeout" => 20,
									 "blocking" => true));
		//var_dump($response);
		return json_decode($response, true);
		
	}	
	
	function pickup_queued_critical_path_css() {
		global $pegasaas;
		
		$queued_requests 						= get_option('pegasaas_pending_critical_css_request', array());
		if ($_POST['c'] == "pickup-pending-requests") {
			$force_pickup = true;
		}

		if (sizeof($queued_requests) > 0 || $force_pickup) {
		
			$response = $this->post(array("command" => "pickup-cpcss"));
			update_option("pegasaas_last_pickup", time());
			if ($response == "") {

			} else {
				$data = json_decode($response, true);

				if (is_array($data['cpcss_requests'])) {
					$pegasaas->utils->log("----- pickup_queued_critical_path_css start -----", "pickup_queued_requests");

					foreach ($data['cpcss_requests'] as $request_id => $request) { 
						$resource_id = $request['resource_id'];
						$css = $request['css'];

						$pegasaas->process_critical_css($css, $resource_id);
					}
				}				
			}
		} 
	}
	
	function pickup_queued_pagespeed_scores() {
		global $pegasaas;
		
		$queued_requests 			= $pegasaas->db->get_results("pegasaas_api_request", array("request_type" => "pagespeed"));
		$queued_benchmark_requests 	= $pegasaas->db->get_results("pegasaas_api_request", array("request_type" => "pagespeed-benchmark"));
		
		if (sizeof($queued_requests) + sizeof($queued_benchmark_requests) > 0) {		
			$response = $this->post(array("command" => "pickup-gpsi"), array("timeout", 20));
			update_option("pegasaas_last_pickup", time()); 

			if ($response == "") {
				return json_encode(array("status" => 0, "message" => 'Error: Problem communicating with pegasaas.io'));

			} else {
				$data = json_decode($response, true);

				if (is_array($data['pagespeed_requests'])) {
					$pegasaas->utils->log("----- pickup_queued_pagespeed_scores start -----", "pickup_queued_requests");
 
					foreach ($data['pagespeed_requests'] as $request_id => $request) { 
						$nonce 		 	= $request['nonce'];
						$resource_id 	= $request['resource_id'];
						$meta 	= $request['data'];
			
						$pegasaas->scanner->process_pagespeed_score_request($resource_id, $nonce, $meta);
					} 
				} else {
					
				}
				
				if (is_array($data['pagespeed_benchmark_requests'])) {
					foreach ($data['pagespeed_benchmark_requests'] as $request_id => $request) { 
						$nonce 		 	= $request['nonce'];
						$resource_id 	= $request['resource_id'];
						$meta 	= $request['data'];

						$pegasaas->scanner->process_pagespeed_benchmark_score_request($resource_id, $nonce, $meta);
						
					}					
				}				
			}
		}
	}
	
	function pickup_queued_requests($force_pickup = false ) {
		global $pegasaas;

		$pegasaas->utils->log("Starting Pickup Queued Requests", "pickup_queued_requests");

		$last_pickup 		 = get_option("pegasaas_last_pickup", 0);
		$eligible_for_pickup = time() - (MINUTE_IN_SECONDS * 5) > $last_pickup;
		
		if ($_GET['c'] == "pickup_queued_requests" || $force_pickup) {
			$eligible_for_pickup = true;
		}
		
		if (PegasaasAccelerator::$settings['settings']['response_rate']['status'] == "slowest" || 
			PegasaasAccelerator::$settings['settings']['response_rate']['status'] == "siteground-normal" || 
		    PegasaasAccelerator::$settings['settings']['response_rate']['status'] == "kinsta-normal" || true) {
			
			if ($eligible_for_pickup) {
				//print "is eligible";
					
				$pegasaas->utils->log("Starting Pickup Optimization Requests", "pickup_queued_requests");
				$this->pickup_queued_optimizations();
				$pegasaas->utils->log("Finished Pickup Optimization Requests", "pickup_queued_requests");

				if (PegasaasAccelerator::$settings['settings']['inject_critical_css']['status'] > 0 ) {
					$pegasaas->utils->log("Starting Pickup CPCSS Requests", "pickup_queued_requests");
					$this->pickup_queued_critical_path_css();
					$pegasaas->utils->log("Finished Pickup CPCSS Requests", "pickup_queued_requests");
				}

				if (PegasaasAccelerator::$settings['settings']['auto_size_images']['status'] > 0 ) {
					$pegasaas->utils->log("Starting Pickup Image Data Requests", "pickup_queued_requests");
					$this->pickup_queued_image_data();
					$pegasaas->utils->log("Finished Pickup Image Data Requests", "pickup_queued_requests");
				}
				
				$pegasaas->utils->log("Starting Pickup PageSpeed Scores", "pickup_queued_requests");
				$this->pickup_queued_pagespeed_scores();
				$pegasaas->utils->log("Finished Pickup PageSpeed Scores", "pickup_queued_requests");
			}

		}
		
		$pegasaas->utils->log("Finished Pickup Queued Requests", "pickup_queued_requests");
	}
	
	function pegasaas_limits_check() {
		global $pegasaas;
		
		$api_key = PegasaasAccelerator::$settings['api_key'];
		if ($api_key == "") {
			return;
		}
		
		$last_check = get_option("pegasaas_limits_check_time", 0);
		$check_duration = 3600; // one hour
		
		if ($last_check + $check_duration < time()) {
			$location = $pegasaas->utils->get_wp_location();
			
			$response = $this->post(array("c" => "limits-check", "installation_location" => $location));

			if ($response == "") {
				PegasaasAccelerator::$settings['last_api_check'] = time();
				PegasaasAccelerator::$settings['error'] = "Unable to connect to pegasaas API Key Server";
				$response = json_encode(array("status" => -2, "reason" => "Unable to connect to API Key Server"));
			} else {
				$data = json_decode($response, true);
				if (isset($data['settings']['limits']) && $data['settings']['limits'] != "") {
					PegasaasAccelerator::$settings['limits'] = $data['limits'];
				}
			}
			
			update_option("pegasaas_limits_check_time", time());
		}
	}		

	
	
	function pegasaas_sync_settings() {
		global $pegasaas;
		
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
		  	if ($_POST['status'] == 1) {
			  	PegasaasAccelerator::$settings['status'] = 1;
		  
		  	} else if ($_POST['status'] == 0) {
			  	PegasaasAccelerator::$settings['status'] = 0;
		  
		  	} else if ($_POST['status'] == -1) {
			  	PegasaasAccelerator::$settings['status'] = 0;
		  
		  	}
		  
		  	$_POST['settings'] 	= stripslashes($_POST['settings']);
		  	$_POST['limits'] 	= stripslashes($_POST['limits']);
		  	$_POST['account'] 	= stripslashes($_POST['account']);
			
		  	$posted_settings 	= json_decode($_POST['settings'], true);
		  
			if (is_array($posted_settings)) {
				PegasaasAccelerator::$settings['settings'] = $posted_settings;   
		  	}


		  	PegasaasAccelerator::$settings["limits"] 	= json_decode($_POST['limits'], true);
		  	PegasaasAccelerator::$settings["account"] = json_decode($_POST['account'], true);
		  	PegasaasAccelerator::$settings['installation_id'] = $_POST['installation_id'];
			PegasaasAccelerator::$settings['subscription'] = $_POST['subscription'];
			PegasaasAccelerator::$settings['subscription_name'] = $_POST['subscription_name'];
			
			if (isset($_POST['trial_expires'])) {
				PegasaasAccelerator::$settings['trial_expires'] = $_POST['trial_expires'];
			} 
			
		  	$pegasaas->set_gzip(PegasaasAccelerator::$settings['settings']['gzip_compression']['status']);
		  	$pegasaas->set_browser_caching(PegasaasAccelerator::$settings['settings']['browser_caching']['status']);
		
			update_option("pegasaas_settings", PegasaasAccelerator::$settings);
			//var_dump(PegasaasAccelerator::$settings);
			print 1;
		} else {
			print -1;
		}		

	 	wp_die();
	}	

	

	function pegasaas_get_settings() {
		global $pegasaas;
		
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			header("Content-type: application/json");
			$settings = json_encode(PegasaasAccelerator::$settings);

			print $settings;
		} else {
			print -1;
		}		

	 	wp_die();
	}	

	function assemble_plugin_data() {
		$plugins 		= get_plugins(); 
		$active_plugins = array();
		foreach ($plugins as $plugin_file => $plugin_data) {
			if (is_plugin_active($plugin_file)) {
				$active_plugins[] = array($plugin_file => $plugin_data['Version']);
			}
		}
		return $active_plugins;
	}
	
	function pegasaas_get_ob_environment() {
		global $pegasaas;
		
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			print $this->get_ob_environment();
		    
		} else {
			print "-1";
			
		}
		wp_die();
		
	}
	
	function get_ob_environment($force_check = false) {
		global $pegasaas;
		$one_day = 60 * 60 * 24;
		
		$when_last_checked = get_option("pegasaas_when_ob_environment_last_checked", 0);
		$ob_environment_data = get_option("pegasaas_ob_environment_data", array());

		if ($force_check || $when_last_checked < time() - $one_day) {
			$args['method'] 	= "POST";
			$args['timeout'] 	= 20;

			$args['sslverify']  = get_option("pegasaas_accelerator_curl_ssl_verify", 0) == 1;
			$args['body'] = array('get_ob_environment' => 'json', 'api_key' => PegasaasAccelerator::$settings['api_key']); 
			
			$response = wp_remote_request($pegasaas->get_home_url()."?accelerate=off", $args );
		
			$ob_environment_data = "";
			if (is_a($response, "WP_Error")) {
					$ob_environment_data = json_encode(array("Error Fetching Environment Data"));
			} else {
					$http_response = $response['http_response'];
					$response_obj = $http_response->get_response_object();
					$httpcode = $response_obj->status_code;
					$response_data = $http_response->get_data();
					if (strstr($response_data, "default output handler")) {
						$ob_environment_data = $response_data;
					} else {
						$ob_environment_data = json_encode(array("Nothing Returned"));
						
					}
			}	
			$when_last_checked = time();
			update_option("pegasaas_when_ob_environment_last_checked", $when_last_checked);
			update_option("pegasaas_ob_environment_data", $ob_environment_data);
				
		} 
		
		return $ob_environment_data;
	}
	
	function get_api_node_url() {
		
		$api_node_override = PegasaasAccelerator::$settings['settings']['api_node']['status'];
		
		if ($api_node_override == "" || $api_node_override === '0') {
	
			return PEGASAAS_API_KEY_SERVER;
		} else {
		
			return str_replace("api.", $api_node_override.".", PEGASAAS_API_KEY_SERVER);
		}
			
	}
	
	function post($post_fields, $args = array()) {
		global $pegasaas; 
		global $test_debug;
	//	$test_debug = true;


		
		$verify_peer = get_option("pegasaas_accelerator_curl_ssl_verify", 0) == 1;
		 
		
		$delay_until = get_option("pegasaas_accelerator_api_delay_until", 0);
		
		if (false && $delay_until > time()) {
			$date_time = date("H:i:s", $delay_until);
			$pegasaas->utils->log("Delaying API Call until {$date_time}", "api");
			
			return json_encode(array('api_error' => "Delay in place until {$date_time}"));
		}
		
		
		// setup standard post fields to go into post body
		$post_fields['installation_type'] 		= 1;
		$post_fields['installation_location'] 	= $pegasaas->utils->get_wp_location();
		$post_fields['installation_version'] 	= $pegasaas->get_current_version();
		
		// relay the source type
		if (defined('PEGASAAS_ACCELERATOR_SOURCE_TYPE')) {
			$post_fields['source_type'] 	= PEGASAAS_ACCELERATOR_SOURCE_TYPE;
		} else {
			$post_fields['source_type'] 	= 'undefined';
		}

		if (!array_key_exists("api_key", $post_fields)) {
			$post_fields['api_key'] 				= PegasaasAccelerator::$settings['api_key'];
		}
		
		// pass environment data
		// this data is used to resolve conflicts and determine causes of possible faults
		$post_fields['domain'] 					= $pegasaas->utils->get_http_host();
		$post_fields['php_version'] 			= phpversion();
		$post_fields['plugin_data'] 			= json_encode($this->assemble_plugin_data());
		$post_fields['ob_handler_data'] 			= $this->get_ob_environment();
		
		if (PegasaasUtils::wpml_multi_domains_active()) {
			$post_fields['wpml_multi_domains_active'] = true;
		}

		if ($pegasaas->utils->is_siteground_server()) {
			$post_fields['siteground_server'] 	= true;
		} else if ($pegasaas->utils->is_kinsta_server()) {
			$post_fields['kinsta_server'] 		= true;
		} 
		
		
		
		if ($_GET['pegasaas-debug'] == "post-fields" ) {
			print "<pre>";
			var_dump($post_fields);
			print "</pre>";
		}
		
		if ($test_debug) { 
			PegasaasUtils::log_benchmark("API Post Fields", "debug-li", 1); 
			print "<li><ul>";
			foreach ($post_fields as $field_name => $value) {
				print "<li>{$field_name} = {$value}</li>";
			}
			print "</ul></li>";
		
		}

		
		// update arguments to send to wp_remote_request
		$args['method'] 	= "POST";
		$args['sslverify']  = $verify_peer;
		$args['body'] 		= $post_fields; 
		
	
		if (!isset($args['blocking']) && $args['blocking'] == true) {
			$args['blocking'] = true;
		}
		
		if ($args['blocking'] === false) {
			$args['timeout'] = '1';
		//	$args['transport'] = "Requests_Transport_fsockopen";
		}
		if ($args['timeout'] === '') {
			$args['timeout'] = $this->get_general_api_request_timeout();
		}
		
	
		
		if ($test_debug) { 
			PegasaasUtils::log_benchmark("API Post Args", "debug-li", 1); 
			print "<li><ul>";
			foreach ($args as $field_name => $value) {
				print "<li>{$field_name} = {$value}</li>";
			}
			print "</ul></li>";
			//var_dump($args);
		}
		
		$pegasaas->utils->log("API Start Remote Request to ".$this->get_api_node_url(), "api");
		$response = wp_remote_request($this->get_api_node_url(), $args);
		$pegasaas->utils->log("API End Remote Request to ".$this->get_api_node_url(), "api");
		if ($test_debug) { 
			PegasaasUtils::log_benchmark("API Post Complete", "debug-li", 1); 
		}

		if ($_GET['pegasaas-debug'] == "post-response") { 
			print "<pre>";
			print "POSTING TO: ".$this->get_api_node_url()."\n";
			print "RESPONSE\n";
			var_dump($response);
			print "</pre>";
		}	
		
		// need to handle the following error with better message
		// cURL error 7: Failed to connect to api.pegasaas.io port 443: Connection refused
		// which is indicative of a firewall block (both to and from) of the API by the origin server
		
		if (is_a($response, "WP_Error")) {
			if ($test_debug) { 
				PegasaasUtils::log_benchmark("Have Error: ".$response->get_error_message(), "debug-li", 1); 
			}		
			if ($args['blocking'] == true) {
				$error = $response->get_error_message();
				$pegasaas->utils->log("API Error: {$error}", "api");
				$ten_minutes = 60 * 10; 

				update_option("pegasaas_accelerator_api_delay_until", time() + $ten_minutes);
				if ($this->is_submission_method_variable()) {
					if (isset(PegasaasAccelerator::$settings['settings']['api_submit_method']['non_blocking_window'] ) && 
						PegasaasAccelerator::$settings['settings']['api_submit_method']['non_blocking_window'] > 0) {
						$window = PegasaasAccelerator::$settings['settings']['api_submit_method']['non_blocking_window']  * 60; 
					} else {
						$window = $ten_minutes;
					}

					update_option("pegasaas_accelerator_api_non_blocking_until", time() + $window);
				}
				$args['body']['x-command'] = "api-connection-timeout";
				$args['timeout'] = $this->get_general_api_request_timeout();
				$args['blocking'] = false;
				wp_remote_request($this->get_api_node_url(), $args);
				return json_encode(array('api_error' => $error));
			} else {
				return json_encode(array('api_call' => 'complete'));
			}
		} else {

			
			
			
			$http_response = $response['http_response'];
			
			
			
			if ($http_response) { 
				$json_string =  $http_response->get_data();
				$response_obj = $http_response->get_response_object();
				$httpcode = $response_obj->status_code;
			} else {
				$json_string = "";
				$httpcode = 0;
			}
			if ($test_debug) { 
				PegasaasUtils::log_benchmark("API Response: $json_string", "debug-li", 1); 
				PegasaasUtils::log_benchmark("API Response Status: $httpcode", "debug-li", 1); 
			}
			
			$pegasaas->utils->log("API Response: $json_string", "api");
			$debug_backtrace = debug_backtrace();
			$calling_file = explode("/", $debug_backtrace[0]['file']);
			$calling_file = array_pop($calling_file);
			$calling_function = $debug_backtrace[1]['function'];
			$calling_class = $debug_backtrace[1]['class'];
			$calling_line = $debug_backtrace[1]['line'];
			
			$debug_backtrace_string = "{$calling_file} ".($calling_class != "" ? $calling_class."->" : "")."{$calling_function}() line #{$calling_line}";
			$pegasaas->utils->log("API Response: $debug_backtrace_string", "api");
			
			
			$pegasaas->utils->log("API Response: Status $httpcode", "api");
			 
			if ($args['blocking'] == true && $this->is_submission_method_variable()) {
				$response_obj = $http_response->get_response_object();
				$httpcode = $response_obj->status_code;
				if ($httpcode != 200) {
					$ten_minutes = 60 * 10;
					if (isset(PegasaasAccelerator::$settings['settings']['api_submit_method']['non_blocking_window'] ) && 
						PegasaasAccelerator::$settings['settings']['api_submit_method']['non_blocking_window'] > 0) {
						$window = PegasaasAccelerator::$settings['settings']['api_submit_method']['non_blocking_window']  * 60; 
					} else {
						$window = $ten_minutes; 
					}

					update_option("pegasaas_accelerator_api_non_blocking_until", time() + $window);
				}
			}
			
			if ($post_fields['command'] == "submit-optimization-request" && $args['blocking'] == false) {
				$response = array('optimization_request_status' => "2");
				$json_string = json_encode($response);
			}
			
			return $json_string;
		}
		
	

	

	}

		
	
}