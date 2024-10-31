<?php
class PegasaasPluginCompatibility {
	
	
	function __construct() {
	
	}

	static function condition_instapage_hooks() {
		if (PegasaasUtils::instapage_active()) {
			foreach ($GLOBALS['wp_filter']['wp']->callbacks as $index => $priority) {
				foreach ($priority as $id => $callback) {
					if ($callback['function'][1] == "checkCustomUrl") {
						// remove the standard action
						$class = InstapageCmsPluginConnector::getSelectedConnector();
						remove_action('wp', array($class, "checkCustomUrl"), 1);

						// add in the compatibile action
						$class = new PegasaasInstapageCmsPluginWpConnector();
						add_action('wp', array($class, 'checkCustomUrl'), 1);
					}
				}
			}
		} 

	}

	static function get_caching_plugins_issues() {
		global $pegasaas;
		$issues = array();
		$issues['critical'] = array();
		$issues['warning']  = array();
		
		if ($pegasaas->cache->pantheon_exists()  && !$pegasaas->utils->does_plugin_exists_and_active("pantheon-advanced-page-cache")) { 
			$issues['critical'][] = array("title" => "'Pantheon Advanced Page Cache' Detected",
										  "advice" => "To allow Pegasaas Accelerator to automatically clear related page caches when you update content, you will need to Install and Activate the <i>Pantheon Advanced Page Caching</i> plugin.  Install <a href='plugin-install.php?s=pantheon+advanced+page+cache&tab=search&type=term&action=pantheon-load-infobox'>Pantheon Advanced Page Cache</a>.");
		}
		
		if ($pegasaas->utils->does_plugin_exists_and_active("wp-rocket")) {
			$issues['warning'][] = array("title" => "'WP Rocket' Caching Plugin Detected",
										 "advice" => "We recommend that you disable 'WP Rocket' as you should not operate more than one caching plugin at a time.");
		}
		if ($pegasaas->utils->does_plugin_exists_and_active("wp-super-cache")) {
			$issues['critical'][] = array("title" => "'WP Super Cache' Caching Plugin Detected",
										 "advice" => "Please disable 'WP Super Cache' as you should not operate more than one caching plugin at a time.");
		}		

		if ($pegasaas->utils->does_plugin_exists_and_active("wp-fastest-cache")) {
			$issues['critical'][] = array("title" => "'WP Fastest Cache' Caching Plugin Detected",
										 "advice" => "Please disable 'WP Fastest Cache' as you should not operate more than one caching plugin at a time.");
		}
		
		if ($pegasaas->utils->does_plugin_exists_and_active("litespeed-cache")) {
			$issues['warning'][] = array("title" => "'Litespeed Cache' Caching Plugin Detected",
										 "advice" => "Please disable 'Litespeed Cache' as you should not operate more than one caching plugin at a time.");
		}

		if ($pegasaas->utils->does_plugin_exists_and_active("hyper-cache")) {
			$issues['critical'][] = array("title" => "'Hyper Cache' Caching Plugin Detected",
										 "advice" => "Please disable 'Hyper Cache' as you should not operate more than one caching plugin at a time.");
		}

		if ($pegasaas->utils->does_plugin_exists_and_active("comet-cache")) {
			$issues['critical'][] = array("title" => "'Comet Cache' Caching Plugin Deteced",
										 "advice" => "Please disable 'Comet Cache' as you should not operate more than one caching plugin at a time.");
		}	
		
		if ($pegasaas->utils->does_plugin_exists_and_active("comet-cache")) {
			$issues['critical'][] = array("title" => "'W3 Total Cache' Caching Plugin Detected",
										 "advice" => "Please disable 'W3 Total Cache' as you should not operate more than one caching plugin at a time.");
		}
		
		if ($pegasaas->utils->does_plugin_exists_and_active("page-optimize")) {
			$issues['critical'][] = array("title" => "'Page Optimize' Plugin Detected",
										 "advice" => "Please disable 'Page Optimize' as you should not operate more than one minification/concatination plugin at a time.");
		}		
		
		return $issues;
	}
	
	
	static function get_plugin_compatibility() {
		global $pegasaas;
		$plugins = get_plugins();
		
		$active_conflicting_plugins = $pegasaas->get_active_conflicting_plugins();
		
		// initialize array
		$issues = array();
		$issues['critical'] = array();
		$issues['all'] = array();
		$issues['warning'] = array();
		
		foreach ($plugins as $plugin_path => $plugin) {
			if (in_array($plugin_path, $active_conflicting_plugins)) {
				print "conflicting: $plugin_path <br>";
				$issues['all'][] = array("title" => "<i class='fa fa-remove'></i> ".$plugin['Name'],
										"advice" => "Please disable this plugin as it contains functionality that conflicts with the operation of Pegasaas",
										"state" => "critical");
			} else if (is_plugin_active($plugin_path) && !strstr($plugin_path, "pegasaas")) {
				//var_dump($plugin);
				$issues['all'][] = array("title" => "<i class='fa fa-check'></i> ".$plugin['Name'], "state" => "passed");
				
			}
		}
		return $issues;
		
	}
	
	static function get_api_compatibility() {
		$issues = array();
		$issues['all'][] = array("title" => "<i id='api-reachable' class='fa fa-spin fa-spinner'></i> Checking API Reachable",
								 "advice" => "We're testing to see if we can contact the API.",
								"state" => "checking");
		
		$issues['all'][] = array("title" => "<i id='api-response-time' class='fa fa-spin fa-spinner'></i> Checking API Response Time",
								 "advice" => "We're testing to see how long it takes to communicate with the API.",
								"state" => "checking");
		
		$issues['all'][] = array("title" => "<i id='api-test-optimization' class='fa fa-spin fa-spinner'></i> Submitting Test Optimization",
								 "advice" => "We're testing to see if there are any third-party firewalls or caching systems blocking the system from submitting an optimization to the API.",
								"state" => "checking");		

		$issues['all'][] = array("title" => "<i id='api-test-fetch' class='fa fa-spin fa-spinner'></i> Checking Optimization Data Fetch",
								 "advice" => "We're testing to see if the API can submit to the plugin.",
								"state" => "checking");		
		
		$issues['all'][] = array("title" => "<i id='api-test-webperf-fetch' class='fa fa-spin fa-spinner'></i> Checking Web Performance Data Fetch",
								 "advice" => "We're testing to see if the API can submit to the plugin.",
								"state" => "checking");		
		
		return $issues;
	}
	
	static function is_api_reachable() {
		global $pegasaas;
		
		// test api reachable
		$start_time = $pegasaas->microtime();
		$response = $pegasaas->api->post(array("command" => "test-api-response"), array('timeout' => 30, 'blocking' => true));
		$response = array();
		$response['response_time'] = number_format(($pegasaas->microtime() - $start_time) * 1000, 0, '.', ',');
		
		if ($response == "") {
			$response['reachable'] = false;
			$response['advice'] = "<h4>API Unreachable</h4> It looks like our API is not currently reachable.  You can <button class='btn btn-xs btn-default' onclick='check_compatibility()'>try again</button>, <a href='https://pegasaas.com/knowledge-base/why-is-the-api-unreachable-when-i-try-to-install-the-pegasaas-accelerator-wp-plugin/' target='_blank'>learn more</a>, or continue anyway.";
		} else { 
			$response['reachable'] = true;
		}
		return $response;
		
	}
	
	static function submit_test_optimization() {
		global $pegasaas;
		
		// test api reachable
		$start_time = $pegasaas->microtime();
		$response = array();
		$site_response = $pegasaas->utils->touch_url("/", array("return-data" => true, 
														   "optimization-test" => true, 
														   "blocking" => true, 
														   "timeout" => 30)); 
			
		//unset ($site_response);
		if ($site_response) {
				if ($site_response == "1") {
					$response['status'] = 1;
					$response['title'] = "Test Optimization Succeeded";
				} else {
				
					if (substr($response, 0,1) == 1) {
						$response['status'] = 0;
						$response['title'] = "Test Optimization Failed";
						$response['data'] = $site_response;
						$response['advice'] = "<h4>Bad Output Buffer</h4> It seems as though your web server was able to submit 
						the test submission, however there appears to be something interfering with the response.  
						Please <button class='btn btn-xs btn-default' onclick='check_compatibility()'>try again</button>, 
						<a href='https://pegasaas.com/knowledge-base/i-am-getting-a-test-optimization-failed-bad-output-buffer-message-when-installing-the-plugin/' target='_blank'>learn more</a>, 
						or <a class='support-link' href='?page=pegasaas-accelerator&skip=to-support'>contact support</a>.  We do not recommend you proceed with the installation.
						";
					
					} else {
						$response['status'] = -1;
						$response['title'] = "Test Optimization Blocked";
						$response['data'] = $site_response;
						$response['advice'] = "<h4>Blocked Submission</h4> It seems as though your web server has blocked 
						our optimization from submitting. 
						Please <button class='btn btn-xs btn-default' onclick='check_compatibility()'>try again</button>, 
						<a href='https://pegasaas.com/knowledge-base/i-am-getting-a-test-optimization-blocked-blocked-submission-message-when-trying-to-install-the-plugin/' target='_blank'>learn more</a>, 
						or <a class='support-link' href='?page=pegasaas-accelerator&skip=to-support'>contact support</a> so that we may investigate.";
						
					}
				}
			   
		} else { 
						$response['status'] = -2;
						$response['title'] = "Test Optimization Not Completed - Web Server Timeout";
						$response['data'] = $site_response;
			
						$response['advice'] = "<h4>Submission Timeout</h4> It seems as though your web server failed to complete the test submission in under five seconds.
						Please <button class='btn btn-xs btn-default' onclick='check_compatibility()'>try again</button>,  
						<a href='https://pegasaas.com/knowledge-base/i-am-getting-a-test-optimization-not-complete-web-server-timeout-message-when-installing-the-plugin/' target='_blank'>learn more</a>, or continue anyway.";
			
						//$response['advice'] = "  ";
			
				
				
		}		
		
	//	$response = array();
		$response['response_time'] = number_format(($pegasaas->microtime() - $start_time) * 1000, 0, '.', ',');
		
		
		return $response;
		
	}	
	

	static function submit_push_fetch_test() {
		global $pegasaas;
		
		// test api reachable
		$start_time = $pegasaas->microtime();
		$response = array();
		
		
		$location = $pegasaas->utils->get_wp_location();
		$post_fields = array();
		$post_fields['command'] = "test-optimization-push-fetch";
		$post_fields['callback_url']   		= admin_url( 'admin-ajax.php' )."?wp_rid=".$resource_id."&request_id={$request_id}"; 

			
		$test_communication_passed = false;
			
		// test optimization push fetch
		$site_response = $pegasaas->api->post($post_fields, array('timeout' => 30, 'blocking' => true));
		
			
		if ($site_response == "") {
				$response['status'] = -1;
				$response['title'] = "<i id='api-test-fetch' class='fa fa-remove'></i> WordPress Plugin Unreachable By API";
				$response['data'] = $site_response;
			//	$response['advice'] = "It appears as though our API cannot communicate with the 'wp-admin/admin-ajax.php' endpoint.   Please <a href='?page=pegasaas-accelerator&skip=to-support'>contact support</a> so that we can investigate.";
				$response['advice'] = "<h4>WordPress Plugin Unreachable</h4> It appears as though our API cannot communicate with the 'wp-admin/admin-ajax.php' endpoint when submitting data. 
				Please <button class='btn btn-xs btn-default' onclick='check_compatibility()'>try again</button>,  
				<a href='https://pegasaas.com/knowledge-base/i-am-getting-a-wordpress-plugin-unreachable-message-when-trying-to-install-the-plugin/' target='_blank'>learn more</a>, <a class='support-link' href='?page=pegasaas-accelerator&skip=to-support'>contact support</a>, or continue anyway.";
			

		

		} else { 
				$data = json_decode($site_response, true);
				
				if ($data['status'] == 1) {
					$response['status'] = 1;
					$response['title'] = "<i id='api-test-fetch' class='fa fa-check'></i> API Communication Test Passed";
					$response['data'] = $site_response;
					$response['advice'] = "";
				
					
				} else {
					$response['status'] = 0;
					$response['title'] = "<i id='api-test-fetch' class='fa fa-remove'></i> API Communication Test Failed";
					$response['data'] = $site_response;
				//	$response['advice'] = "It seems as though there is something block our API from returning optimizations to the plugin.  Please <a href='?page=pegasaas-accelerator&skip=to-support'>contact support</a> and report that the system returned status '{$data['status']}'.";
					
					$response['advice'] = "<h4>Communication Test Failed</h4> It appears as though our API failed to communicate correctly with the WordPress plugin when submitting data. 
				Please <button class='btn btn-xs btn-default' onclick='check_compatibility()'>try again</button>,  
				<a href='https://pegasaas.com/knowledge-base/i-am-getting-a-communication-test-failed-message-when-trying-to-install-the-plugin/' target='_blank'>learn more</a>, <a class='support-link' href='?page=pegasaas-accelerator&skip=to-support'>contact support</a>, or continue anyway.";


				}

		}
		
		
		$response['response_time'] = number_format(($pegasaas->microtime() - $start_time) * 1000, 0, '.', ',');
		
		
		return $response;
		
	}		
	
	static function submit_webperf_data_fetch_test() {
		global $pegasaas;
		
		// test api reachable
		$start_time = $pegasaas->microtime();
		$response = array();
		
		
		$location = $pegasaas->utils->get_wp_location();
		$post_fields = array();
		$post_fields['command'] 	 = "test-webperf-push";
	//	$post_fields['callback_url'] = ($_SERVER['HTTPS'] == "on" ? "https://" : "http://").$pegasaas->utils->get_http_host().$location."/wp-admin/admin-ajax.php?wp_rid=".$resource_id."&request_id={$request_id}"; 
		$post_fields['callback_url'] 		= admin_url( 'admin-ajax.php' )."?wp_rid=".$resource_id."&request_id={$request_id}"; 


		// test optimization push fetch
		$site_response = $pegasaas->api->post($post_fields, array('timeout' => 45, 'blocking' => true)); 

			
			
		if ($site_response == "") {
				$response['status'] = -1;
				$response['title'] = "<i id='api-test-webperf-fetch' class='fa fa-remove'></i> WordPress Plugin Unreachable By API (Fetch Test #2)";
				$response['data'] = $site_response;
				//$response['advice'] = "It appears as though our API cannot communicate with the 'wp-admin/admin-ajax.php' endpoint.   Please <a href='?page=pegasaas-accelerator&skip=to-support'>contact support</a> so that we can investigate.";
				
				$response['advice'] = "<h4>WordPress Plugin Unreachable</h4> It appears as though our API cannot communicate with the 'wp-admin/admin-ajax.php' endpoint when submitting data. 
				Please <button class='btn btn-xs btn-default' onclick='check_compatibility()'>try again</button>,  
				<a href='https://pegasaas.com/knowledge-base/i-am-getting-a-wordpress-plugin-unreachable-message-when-trying-to-install-the-plugin/' target='_blank'>learn more</a>, <a class='support-link' href='?page=pegasaas-accelerator&skip=to-support'>contact support</a>, or continue anyway.";

		

		} else { 
				$data = json_decode($site_response, true);
				
				if ($data['status'] == 1) {
					$response['status'] = 1;
					$response['title'] = "<i id='api-test-webperf-fetch' class='fa fa-check'></i> API Communication Test #2 Passed";
					$response['data'] = $site_response;
					$response['advice'] = "";
				
					
				} else {
					$response['status'] = 0;
					$response['title'] = "<i id='api-test-webperf-fetch' class='fa fa-remove'></i> API Communication Test #2 Failed";
					$response['data'] = $site_response;
				//	$response['advice'] = "It seems as though there is something blocking our API from returning optimizations to the plugin.  Please <a href='?page=pegasaas-accelerator&skip=to-support'>contact support</a> and report that the system returned status '{$data['status']}'.";

					$response['advice'] = "<h4>Communication Test Failed</h4> It appears as though our API failed to communicate correctly with the WordPress plugin when submitting data. 
				Please <button class='btn btn-xs btn-default' onclick='check_compatibility()'>try again</button>,  
				<a href='https://pegasaas.com/knowledge-base/i-am-getting-a-communication-test-failed-message-when-trying-to-install-the-plugin/' target='_blank'>learn more</a>, <a class='support-link' href='?page=pegasaas-accelerator&skip=to-support'>contact support</a>, or continue anyway.";

				}

		}
		
		
		$response['response_time'] = number_format(($pegasaas->microtime() - $start_time) * 1000, 0, '.', ',');
		
		
		return $response;
		
	}		
	
	static function get_api_issues() {
		global $pegasaas;
		$issues = array();
		$issues['critical'] = array();
		$issues['warning']  = array();	
		$issues['passed'] = array();
		
		$api_connection_ok = false;
		
		// test api reachable
		$start_time = time();
		$response = $pegasaas->api->post(array("command" => "test-api-response"), array('timeout' => 30, 'blocking' => true));
		if ($response == "") {
			$issues['critical'][] = array("title" => "API Unrechable",
										 "advice" => "It may be that our API servers are busy.  Please try again.  If the problem persists, please <a href='?page=pegasaas-accelerator&skip=to-support'>contact support</a>.");

		} else { 
			
			$data = json_decode($response, true);
			
			if ($data['status'] == 1) {
				$end_time = time();
				if ($end_time - $start_time > 30) {
					$issues['warning'][] = array("title" => "API Connection Slow",
										 "advice" => "It may be that our API servers are busy.  You can try again, however if the problem persists, please <a href='?page=pegasaas-accelerator&skip=to-support'>contact support</a>.");
				} else {
					$api_connection_ok = true;
					$issues['pass'][] = array('title' => 'API Connection OK');
				}
			} else {
				$issues['critical'][] = array("title" => "API Response Not Correct",
										 "advice" => "It may be that our API servers are busy.  Please try again.  If the problem persists, please <a href='?page=pegasaas-accelerator&skip=to-support'>contact support</a>.");
				
			}
			
		}
		
		if ($api_connection_ok) {
			// test optimization test
			$response = $pegasaas->utils->touch_url("/", array("return-data" => true, "optimization-test" => true, "blocking" => true, "timeout" => 30)); 
			
			
			if ($response) {
				if ($response == "1") {
					$issues['pass'][] = array('title' => 'Test Submission OK');
				} else {
					if (substr($response, 0,1) == 1) {
						$issues['critical'][] = array("title" => "Test Submission Failed",
												  "data" => $response,
										 "advice" => "It seems as though your web server was able to submit the test submission, however there appears to be something interfering with the response.  This could be due to a firewall, or a server side caching system.  Please <a href='?page=pegasaas-accelerator&skip=to-support'>contact support</a> so that we may investigate.");
					
					} else {
						$issues['critical'][] = array("title" => "Test Submission Blocked",
												  "data" => $response,
										 		  "advice" => "It seems as though your web server has blocked our optimization from submitting.  This could be due to a firewall, or a server side caching system.  Please <a href='?page=pegasaas-accelerator&skip=to-support'>contact support</a> so that we may investigate.");
					}
				}
			   
			} else {
				$issues['critical'][] = array("title" => "Test Submission Not Completed - Web Server Too Slow",
											  "data" => $response,
											  
										 "advice" => "It seems as though your web server failed to complete the test submission in under five seconds.  This indicates that your web server, given the mix of plugins installed in your site, would be unsuited to communicating with an outside API.  If you believe this diagnosis is in error, and you woud like us to investigate further, please <a href='?page=pegasaas-accelerator&skip=to-support'>contact support</a> so that we may investigate.");
				
			}
			
			$location = $pegasaas->utils->get_wp_location();
			$post_fields = array();
			$post_fields['command'] = "test-optimization-push-fetch";
			//$post_fields['callback_url'] 		= ($_SERVER['HTTPS'] == "on" ? "https://" : "http://").$pegasaas->utils->get_http_host().$location."/wp-admin/admin-ajax.php?wp_rid=".$resource_id."&request_id={$request_id}"; 
			$post_fields['callback_url']   		= admin_url( 'admin-ajax.php' )."?wp_rid=".$resource_id."&request_id={$request_id}"; 

			
			$test_communication_passed = false;
			
			// test optimization push fetch
			$response = $pegasaas->api->post($post_fields, array('timeout' => 45, 'blocking' => true));
			
			if ($response == "") {
				$issues['critical'][] = array("title" => "WordPress Plugin Unreachable By API",
											 "advice" => "It appears as though our API cannot communicate with the 'wp-admin/admin-ajax.php' endpoint.   Please <a href='?page=pegasaas-accelerator&skip=to-support'>contact support</a> so that we can investigate.");

			} else { 
				$data = json_decode($response, true);
				
				if ($data['status'] == 1) {
					
					$api_connection_ok = true;
					$issues['pass'][] = array('title' => 'API Communication Test Pass');
		 			$test_communication_passed = true;
				} else {
					$issues['critical'][] = array("title" => "API Communication Test Failed",
											 "advice" => "It seems as though there is something block our API from returning optimizations to the plugin.  Please <a href='?page=pegasaas-accelerator&skip=to-support'>contact support</a> and report that the system returned status '{$data['status']}'.");

				}

			}
			if ($test_communication_passed) {
				$location = $pegasaas->utils->get_wp_location();
				$post_fields = array();
				$post_fields['command'] 	 = "test-webperf-push";
			//	$post_fields['callback_url'] = ($_SERVER['HTTPS'] == "on" ? "https://" : "http://").$pegasaas->utils->get_http_host().$location."/wp-admin/admin-ajax.php?wp_rid=".$resource_id."&request_id={$request_id}"; 
				$post_fields['callback_url'] 		= admin_url( 'admin-ajax.php' )."?wp_rid=".$resource_id."&request_id={$request_id}"; 


				// test optimization push fetch
				$response = $pegasaas->api->post($post_fields, array('timeout' => 45, 'blocking' => true)); 

				if ($response == "") {
					$issues['critical'][] = array("title" => "WordPress Plugin Unreachable By API (Test #2)",
												 "advice" => "It appears as though our API cannot communicate with the 'wp-admin/admin-ajax.php' endpoint.   Please <a href='?page=pegasaas-accelerator&skip=to-support'>contact support</a> so that we can investigate.");

				} else { 
					$data = json_decode($response, true);

					if ($data['status'] == 1) { 

						$api_connection_ok = true;
						$issues['pass'][] = array('title' => 'API Communication Test #2 Pass');

					} else {
						$issues['critical'][] = array("title" => "API Communication Test #2 Failed",
												 "advice" => "It seems as though there is something block our API from returning optimizations to the plugin.  Please <a href='?page=pegasaas-accelerator&skip=to-support'>contact support</a> and report that the system returned status '{$data['status']}'.");

					}

				}	
			}
			
		}
		
		
		
		return $issues;
		
	}
	
	static function get_system_issues() {
		global $pegasaas;
		$issues = array();
		$issues['critical'] = array();
		$issues['warning']  = array();	
		$issues['passed']   = array();	
		$issues['all'] = array();
		
		// test memory
		if (!PegasaasUtils::memory_within_limits()) {
			$issue =  array("title" => "<i class='fa fa-remove'></i> PHP Memory Limit ",
										 "advice" => "Your PHP memory usage is nearing the defined upper limit of ".ini_get('memory_limit').".  Please increase the 'memory_limit' setting in your PHP settings to ".PegasaasUtils::get_next_memory_limit().".");
			$issues['critical'][] = $issue;
			$issue['state'] = "critical";
			
		} else {
			 $issue = array("title" => "<i class='fa fa-check'></i> PHP Memory Limit Good");
			 $issues['passed'][] = $issue;
			 $issue['state'] = "passed";
		}
		$issues['all'][] = $issue;
		
		
		
		// test htaccess writable
		if (!$pegasaas->is_htaccess_writable()) {
			$issue =  array("title" => "<i class='fa fa-remove'></i> .htaccess File is <b>Not</b> Writable",
										 "advice" => array("Please ensure the .htaccess file, found in the website root directory, is writable (at least until installation is complete).",
														   "Pegasaas needs to write to this file each time a setting that requires .htaccess rule changes is enabled/disabled, or if the plugin itself is disabled/enabled.",
														   "If you do not know how to change the permissions on the .htaccess file, <a rel='noopener noreferrer' target='_blank' href='https://pegasaas.com/knowledge-base/htaccess-file-is-not-writable/'>click here</a>."));
			$issues['critical'][] = $issue;
			$issue['state'] = "critical";
			

		} else {
			$issue =  array("title" => "<i class='fa fa-check'></i> .htaccess File Writable");
		}
		
		$issues['all'][] = $issue;
		
		
		
		if (!$pegasaas->is_cache_writable()) {
			$issue = array("title" => "<i class='fa fa-remove'></i> 'wp-content/pegasaas-cache/' Folder Is Not Writable",
										 "advice" => "Pegasaas automatically creates a folder that it uses for caching called 'pegasaas-cache' in the 'wp-content' folder.  Please ensure that either the 'wp-content' folder is writable, or that you have
			created a folder called 'pegasaas-cache' within the 'wp-content' folder with write permissions.");
				
			$issues['critical'][] = $issue;
			$issue['state'] = "critical";
			

		} else {
			 $issue = array("title" => "<i class='fa fa-check'></i> Cache Folder Writable");
			$issues['passed'][] = $issue;
			$issue['state'] = "passed";
		 }

		$issues['all'][] = $issue;
		
		if (!$pegasaas->is_log_writable()) {
			$issue =  array("title" => "<i class='fa fa-remove'></i> ".str_replace($pegasaas->get_home_url(), "", PEGASAAS_ACCELERATOR_URL)."log.txt is not writable",
										 "advice" => "In order to troubleshoot any issues, the log.txt file should be writable.");
														  
			$issues['critical'][] = $issue;
			$issue['state'] = "critical";
			
		} else {
			$issue = array("title" => "<i class='fa fa-check'></i> Log File Writable");
			$issues['passed'][] = $issue;
			$issue['state'] = "passed";
		}	
		
		if ($pegasaas->utils->is_siteground_server()) {
			$issue = array("title" => "<i class='fa fa-warning'></i> Hosting: Siteground");
			$issues['passed'][] = $issue;
			$issue['state'] = "passed";					
		} else if ($pegasaas->utils->is_kinsta_server()){
			$issue = array("title" => "<i class='fa fa-warning'></i> Hosting: Kinsta");
			$issues['passed'][] = $issue;
			$issue['state'] = "passed";					
		} else if ($pegasaas->utils->is_windows_iis()) {
			$issue = array("title" => "<i class='fa fa-danger'></i> Hosting: Windows IIS");
			$issues['passed'][] = $issue;
			$issue['state'] = "passed";					
		} else if ($pegasaas->utils->is_flywheel_server()) {
			$issue = array("title" => "<i class='fa fa-check'></i> Hosting: Flywheel");
			$issues['passed'][] = $issue;
			$issue['state'] = "passed";					
		} else if ($pegasaas->utils->is_wpx_server()) {
			$issue = array("title" => "<i class='fa fa-check'></i> Hosting: WPX");
			$issues['passed'][] = $issue;
			$issue['state'] = "passed";						
		} else if ($pegasaas->cache->godaddy_exists()) {
			$issue = array("title" => "<i class='fa fa-warning'></i> Hosting: GoDaddy");
			$issues['passed'][] = $issue;
			$issue['state'] = "passed";			
		}  
		
		$issues['all'][] = $issue;

		if ($pegasaas->cache->cloudflare_exists()) {
			// cloudflare
			$issue = array("title" => "<i id='cloudflare-detected' class='fa fa-warning'></i> Cloudflare");
			$issues['all'][] = $issue;
		}		
		
		// server response time
		$issue = array("title" => "<i id='server-response-time' class='fa fa-spin fa-spinner'></i> Server Response Time");
		$issues['all'][] = $issue;
		
		
		return $issues;
		
	}	
	
	
	static function bunny_cdn_ob_finish($buffer) {
		
		$options = BunnyCDN::getOptions();
		if(strlen(trim($options["cdn_domain_name"])) > 0) {
			$rewriter = new PegasaasBunnyCDNFilter($options["site_url"], (is_ssl() ? 'https://' : 'http://') . $options["cdn_domain_name"], $options["directories"], $options["excluded"], $options["disable_admin"]);
			return $rewriter->rewrite($buffer);
		} else {
			return $buffer;
		}
	}
}
if (class_exists("BunnyCDNFilter")) {
	

	class PegasaasBunnyCDNFilter extends BunnyCDNFilter {
		public function pegasaas_rewrite($buffer) {
			return $this->rewrite($buffer);
		}
	}
}

/****************************************************************************
 * 
 *  instapage
 * 
 *  These extensions are added so that we can override the default functionality of the instapages
 *  plugin.  The only difference between this code and the boilerplate instapages code, is that the wp_die() has been
 *  commented out.
 * 
 *  */
if (class_exists("InstapageCmsPluginWPConnector")) {
	class PegasaasInstapageCmsPluginPageModel extends InstapageCmsPluginPageModel {
		/**
		 * Displays the landing page.
		 *
		 * @param object $page Landing page to display.
		 * @param int $forcedStatus Status to be set as a header. Default: null.
		 */
		public function display($page, $forcedStatus = null)
		{
			require_once(__DIR__ . '/../modules/lpAjaxLoader/InstapageCmsPluginLPAjaxLoaderController.php');
			$lpAjaxLoaderController = new InstapageCmsPluginLPAjaxLoaderController();
			$instapageId = $page->instapage_id;
			$slug = $page->slug;
			$host = parse_url($page->enterprise_url, PHP_URL_HOST);
			InstapageCmsPluginHelper::writeDiagnostics($slug . ' : ' . $instapageId, 'slug : instapage_id');

			$api = InstapageCmsPluginAPIModel::getInstance();
			$querySufix = '';
			$cookies = $_COOKIE;

			if (!empty($_GET)) {
				if ($lpAjaxLoaderController->shouldDecodeQuery()) {
					$querySufix = '?' . base64_decode($_GET['b64']);
				} else {
					$querySufix = '?' . http_build_query($_GET);
				}
			} elseif (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
				$querySufix = '?' . $_SERVER['QUERY_STRING'];
			}

			if (is_array($cookies) && count($cookies)) {
				$cookiesWeNeed = array("instapage-variant-{$instapageId}");

				foreach ($cookies as $key => $value) {
					if (!in_array($key, $cookiesWeNeed)) {
						unset($cookies[$key]);
					}
				}
			}

			$url = preg_replace('/https?:\/\/' . $host . '/', INSTAPAGE_ENTERPRISE_ENDPOINT, $page->enterprise_url);
			$url .= $querySufix;
			$url = InstapageCmsPluginConnector::getURLWithSelectedProtocol($url);

			$enterpriseCallResult = $api->enterpriseCall($url, $host, $cookies);
			$html = $this->getLandingPageHTMLFromTheApp($enterpriseCallResult);
			$this->setVariantCookie($enterpriseCallResult, $instapageId);

			if ($lpAjaxLoaderController->shouldBeUsed($url)) {
				$html = $lpAjaxLoaderController->injectScript($html);
				$html = $lpAjaxLoaderController->addDisplayNoneOnBody($html);
			}

			if ($forcedStatus) {
				$status = $forcedStatus;
			} else {
				$status = isset($enterpriseCallResult['code']) ? $enterpriseCallResult['code'] : 200;
			}

			if ($html) {
				ob_start();
				InstapageCmsPluginHelper::disableCaching();
				InstapageCmsPluginHelper::httpResponseCode($status);
				print $html;
				ob_end_flush();
			//  die();
			} else {
				return false;
			}
		}
	}

	class PegasaasInstapageCmsPluginWPConnector extends InstapageCmsPluginWPConnector {
		public function checkPage($type, $slug = '') {

			$page = InstapageCmsPluginPageModel::getInstance();
			$result = $page->check($type, $slug);
			$supportLegacy = InstapageCmsPluginHelper::getMetadata('supportLegacy', true);
		
			if (!$result && $supportLegacy && $this->legacyArePagesPresent()) {
			  $result = $this->legacyGetPage($slug);
			}
		
			if (isset($result->instapage_id) && $result->instapage_id) {
			  // we have landing page for given slug,
			  // but if in url there are duplicated slashes show 404 page instead
			  if (InstaPageCmsPluginHelper::checkIfRequestUriHasDuplicatedSlashes()) {
				self::return404();
				return false;
			  }

			  if ($type == '404') {
				$page->display($result, 404);
			  } else {
				$page->display($result);
			  }
			}
		  }


		  public function checkCustomUrl() {
			$slug = InstapageCmsPluginHelper::extractSlug($this->getHomeURL());
		
			if ($slug) {
			  $this->checkPage('page', $slug);
			}
		
			return true;
		  }
	}
}
