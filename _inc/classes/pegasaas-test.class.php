<?php
class PegasaasTest {
	function __construct() {}

	static function semaphore_lock() {
		global $pegasaas;
		$start = $pegasaas->execution_time();
		print "<ul>";
		PegasaasUtils::log_benchmark("Locking Semaphore", "debug-li", 1);
		print "<li><ul>";
		$pegasaas->utils->semaphore("testing_semaphore");
		print "</ul></li>";
		PegasaasUtils::log_benchmark("Semaphore Should Be Now Locked", "debug-li", 1);
		print "<li><ul>";
		if ($pegasaas->utils->semaphore("testing_semaphore")) {
			print "</ul></li>";
			PegasaasUtils::log_benchmark("Semaphore Available.  Did Not Lock In The First Place [FAIL]", "debug-li", 1);
		} else {
			print "</ul></li>";
			PegasaasUtils::log_benchmark("Semaphore Locked [PASS]", "debug-li", 1);

		}
		//return;
		PegasaasUtils::log_benchmark("Sleeping for 2 seconds", "debug-li", 1);

		sleep(2);
		PegasaasUtils::log_benchmark("Sleep Completed", "debug-li", 1);
	print "<li><ul>";
		if ($pegasaas->utils->semaphore("testing_semaphore", $wait_time = 250, $stale_time = 1)) {
			print "</ul></li>";
			PegasaasUtils::log_benchmark("Semaphore Auto Released [PASS]", "debug-li", 1);
		} else {
			print "</ul></li>";
			PegasaasUtils::log_benchmark("Semaphore Did Not Auto Release [FAIL]", "debug-li", 1);

		}print "<li><ul>";
		$pegasaas->utils->release_semaphore("testing_semaphore");
		print "</ul></li>";
	$total_elapsed = $pegasaas->execution_time() - $start;
		PegasaasUtils::log_benchmark("Total Elapsed: {$total_elapsed}", "debug-li", 1);
		print "</ul>";

	}

	static function refresh_all_pages_and_posts() {
		global $pegasaas;
		global $test_debug;
		$test_debug = true;
		PegasaasUtils::load_all_pages_and_posts();

	}

	static function semaphore_quick_fail($pass = true) {
		global $pegasaas;
		$start = $pegasaas->execution_time();
		print "<ul>";
		PegasaasUtils::log_benchmark("Locking Semaphore", "debug-li", 1);
		print "<li><ul>";
		$pegasaas->utils->semaphore("testing_semaphore");
		print "</ul></li>";
		PegasaasUtils::log_benchmark("Semaphore Should Be Now Locked", "debug-li", 1);
		$benchmark = $pegasaas->execution_time();
		print "<li><ul>";
		if ($pegasaas->utils->semaphore("testing_semaphore", $pass ? 0 : 1000)) {
			print "</ul></li>";
			PegasaasUtils::log_benchmark("Semaphore Available.  Did Not Lock In The First Place [FAIL]", "debug-li", 1);
		} else {
		print "</ul></li>";
			$elapsed = $pegasaas->execution_time() - $benchmark;
			if ($elapsed < 1) {
				PegasaasUtils::log_benchmark("Semaphore Failed Quickly [PASS] {$elapsed}", "debug-li", 1);

			} else {
				PegasaasUtils::log_benchmark("Semaphore Failed Slowly [FAIL] {$elapsed}", "debug-li", 1);

			}

		}
		print "<li><ul>";
		$pegasaas->utils->release_semaphore("testing_semaphore");
		print "</ul></li>";
		$total_elapsed = $pegasaas->execution_time() - $start;
		PegasaasUtils::log_benchmark("Total Elapsed: {$total_elapsed}", "debug-li", 1);

		print "</ul>";

	}

	static function clear_stale_requests() {
		global $pegasaas;
		global $test_debug;
		$test_debug = true;
		$pegasaas->clear_stale_requests();

	}


	function auto_accelerate_pages() {
		global $pegasaas;
		global $test_debug;
		$test_debug = true;
		$_POST['acceleration-type'] = "all";
		$start = $pegasaas->execution_time();
		print "<ul>";
		PegasaasUtils::log_benchmark("Resetting All Pages And Posts", "debug-li", 1);
		PegasaasUtils::$all_pages_and_posts = array();
		$pegasaas->data_storage->unset_object("all_pages_and_posts");

		$pegasaas->auto_accelerate_pages($reset = true);

		$all_pages_and_posts = PegasaasUtils::get_all_pages_and_posts();

		PegasaasUtils::log_benchmark("All Pages And Posts size: ".sizeof($all_pages_and_posts), "debug-li", 1);

		print "<li><pre>";
		var_dump($all_pages_and_posts);
		print "</pre></li>";

		print "</ul>";
	}

	function get_scanned_objects() {
		global $pegasaas;
		global $test_debug;
		$test_debug = true;

		$start = $pegasaas->execution_time();
		print "<ul>";
		PegasaasUtils::log_benchmark("Resetting All Pages And Posts", "debug-li", 1);
		PegasaasUtils::$all_pages_and_posts = array();
		$pegasaas->data_storage->unset_object("all_pages_and_posts");

		$pegasaas->scanner->get_scanned_objects();
		print "</ul>";

	}


	function get_scanned_objects_on_first_init() {
		global $pegasaas;
		global $test_debug;
		$test_debug = true;
		self::auto_accelerate_pages();
		$start = $pegasaas->execution_time();
		print "<ul>";
		PegasaasUtils::log_benchmark("Resetting All Pages And Posts", "debug-li", 1);
		PegasaasUtils::$all_pages_and_posts = array();
		$pegasaas->data_storage->unset_object("all_pages_and_posts");

		$pegasaas->scanner->get_scanned_objects();
		print "</ul>";

	}

	function fetch_page_metrics() {
		global $pegasaas;
		global $test_debug;
		$test_debug = true;


		$start = $pegasaas->execution_time();
		$_POST['api_key'] = PegasaasAccelerator::$settings['api_key'];

		print "<ul>";
		PegasaasUtils::log_benchmark("Deleting 'pegasaas_accelerated_pages", "debug-li", 1);

		delete_option("pegasaas_accelerated_pages");

		PegasaasUtils::log_benchmark("Resetting All Pages And Posts", "debug-li", 1);
		PegasaasUtils::$all_pages_and_posts = array();
		$pegasaas->data_storage->unset_object("all_pages_and_posts");

		$pegasaas->interface->pegasaas_fetch_page_metrics();
		print "</ul>";

	}

		function submit_scan_request() {
			global $pegasaas;
			global $test_debug;
			$test_debug = true;


			$start = $pegasaas->execution_time();
			$_POST['api_key'] = PegasaasAccelerator::$settings['api_key'];

			print "<ul>";
			//PegasaasUtils::log_benchmark("Deleting 'pegasaas_accelerated_pages", "debug-li", 1);

			//delete_option("pegasaas_accelerated_pages");

			PegasaasUtils::log_benchmark("Resetting All Pages And Posts", "debug-li", 1);
			PegasaasUtils::$all_pages_and_posts = array();
			$pegasaas->data_storage->unset_object("all_pages_and_posts");

			$pegasaas->scanner->submit_scan_request();
			print "</ul>";

	}

	function clear_pagespeed_scans() {
			global $pegasaas;
			global $test_debug;
			$test_debug = true;
			$pegasaas->scanner->clear_pagespeed_requests();
			$pegasaas->scanner->clear_pagespeed_scores();

	}

	function clear_and_submit_pagespeed_scans() {
		global $test_debug;
		$test_debug = true;

		self::clear_pagespeed_scans();
		//sleep(4);
		self::submit_scan_request();


	}

	function get_all_categories() {
		global $test_debug;
		$test_debug = true;
		print "<ul>";
		PegasaasUtils::get_all_categories();

		print "</ul>";

	}

	function rotate_logs() {
		global $test_debug;
		$test_debug = true;
		print "<ul>";
		PegasaasUtils::rotate_log_files();

		print "</ul>";

	}

	function test_wp_delete_post() {
		global $test_debug;
		$test_debug = true;
		print "<ul>";
		PegasaasUtils::log_benchmark("Start", "debug-li", 1);
		add_action('pre_post_update', 								array(self, 'clear_blog_page_cache'));
		add_action('pre_post_update', 								array(self, 'clear_post_types_page_cache'));
		add_action('transition_post_status', 						array(self, 'handle_post_change_state'), 10, 3);
		PegasaasUtils::log_benchmark("Inserting Post", "debug-li", 1);
		$post_id = wp_insert_post(array('post_title' => 'test_wp_delete_post'));
		PegasaasUtils::log_benchmark("Deleting Post {$post_id}", "debug-li", 1);

		wp_delete_post($post_id);
		PegasaasUtils::log_benchmark("Post Deleted", "debug-li", 1);

		//PegasaasUtils::log_benchmark("Deleting Post 11290", "debug-li", 1);

		////wp_delete_post(11290);
		//PegasaasUtils::log_benchmark("Post Deleted", "debug-li", 1);

		print "</ul>";

	}

	function test_change_post_status() {
		global $test_debug;
		$test_debug = true;
		print "<ul>";
		PegasaasUtils::log_benchmark("Start", "debug-li", 1);
		add_action('pre_post_update', 								array(self, 'clear_blog_page_cache'));
		add_action('pre_post_update', 								array(self, 'clear_post_types_page_cache'));
		add_action('transition_post_status', 						array(self, 'handle_post_change_state'), 10, 3);
		PegasaasUtils::log_benchmark("Inserting Post", "debug-li", 1);
		$post_id = wp_insert_post(array('post_title' => 'test_wp_delete_post'));
		PegasaasUtils::log_benchmark("Update Post {$post_id}", "debug-li", 1);
		wp_update_post(array("ID" => $post_id, "post_status" => "publish", "post_content" => "content updated"));
		PegasaasUtils::log_benchmark("Update Post Status {$post_id}", "debug-li", 1);
		wp_update_post(array("ID" => $post_id, "post_status" => "trash"));
		PegasaasUtils::log_benchmark("Deleting Post {$post_id}", "debug-li", 1);
		wp_delete_post($post_id);
		PegasaasUtils::log_benchmark("Post Deleted", "debug-li", 1);
		print "</ul>";

	}
	static function clear_blog_page_cache() {
		print "<li>Executing pre_post_update/clear_blog_page_cache Hook</li>";
	} 
	static function clear_post_types_page_cache() {
		print "<li>Executing pre_post_update/clear_post_types_page_cache Hook</li>";
	} 
	static function handle_post_change_state($new_status, $old_status, $post) {
		print "<li>Executing transition_post_status/handle_post_change_state {$new_status} {$old_status} {$post} Hook</li>";
	} 	

	function get_site_performance_metrics() {
		global $test_debug;
		global $pegasaas;
		$test_debug = true;
	//	$_GET['force_recalculation'] = true;
		print "<ul>";
		PegasaasUtils::log_benchmark("Start", "debug-li", 1);
		$metrics = $pegasaas->scanner->get_site_performance_metrics();
		PegasaasUtils::log_benchmark("Finish", "debug-li", 1);
		$str = json_encode($metrics);
		print "<li>JSON Error: ".json_last_error_msg()."</li>";
	//	print $str;
		$total_levels = substr_count($str, "performance_data");

		print "<li>Object Size: ".number_format(strlen($str), 0, '.', ',')."</li>";
		print "<li>Total Levels: ".number_format($total_levels, 0, '.', ',')."</li>";
		print "<li>Object Dump<pre>";
		var_dump($metrics);
		print "</pre></li>";
		print "</ul>";
	}

	function fetch_page_fonts() {
		global $pegasaas;
		global $test_debug;
		$test_debug = true;
		$pegasaas->scanner->fetch_page_fonts();
	}

	function get_site_performance_metrics_footprint() {
		global $test_debug;
		global $pegasaas;
		$test_debug = true;

		print "<ul>";
		PegasaasUtils::log_benchmark("Start", "debug-li", 1);
		$data_footprint_total = 0;
		$data_footprint_baseline = 0;
		$data_footprint_accelerated = 0;

		$pages_with_scores 	= $pegasaas->scanner->get_pages_with_scores("pagespeed");

		foreach ($pages_with_scores as $scan_record) {
			
			$page_score_details = $scan_record->data;

			$data_footprint_total += strlen($page_score_details);
			$data_footprint_accelerated += strlen($page_score_details);
		}

		$pages_with_scores 	= $pegasaas->scanner->get_pages_with_scores("pagespeed-benchmark");

		foreach ($pages_with_scores as $scan_record) {
			$page_score_details = $scan_record->data;
			$data_footprint_total += strlen($page_score_details);
			$data_footprint_baseline += strlen($page_score_details);
		}
		PegasaasUtils::log_benchmark("Baseline Web Perf Scans Footprint: ".(number_format($data_footprint_baseline/1024/1024, 1, '.', ''))."M", "debug-li", 1);
		PegasaasUtils::log_benchmark("Accelerated Web Perf Scans Footprint: ".(number_format($data_footprint_accelerated/1024/1024, 1, '.', ''))."M", "debug-li", 1);
		PegasaasUtils::log_benchmark("Total Web Perf Scans Footprint: ".(number_format($data_footprint_total/1024/1024, 1, '.', ''))."M", "debug-li", 1);


		PegasaasUtils::log_benchmark("Finish", "debug-li", 1);

	}


	function get_list_of_all_files_in_folder() {
		global $test_debug;
		global $pegasaas;
		$test_debug = true;
		PegasaasUtils::log_benchmark("Start", "debug-li", 1);

		$all_files = $pegasaas->cache->get_list_of_all_local_files("html");
		PegasaasUtils::log_benchmark("Total Files: ".sizeof($all_files), "debug-li", 1);

		PegasaasUtils::log_benchmark("Finish", "debug-li", 1);

	}

	function auto_crawl() {
		global $test_debug;
		global $pegasaas;
		$test_debug = true;
		PegasaasUtils::log_benchmark("Start", "debug-li", 1);

		$pegasaas->auto_crawl();


		PegasaasUtils::log_benchmark("Finish", "debug-li", 1);

	}


	function get_list_of_all_files_in_folder_detailed() {
		global $test_debug;
		global $pegasaas;
		global $test_debug_detailed;
		global $total_scandir_time;
		global $total_scandirs;

		$test_debug_detailed = true;
		$test_debug = true;
		PegasaasUtils::log_benchmark("Start", "debug-li", 1);


		$all_files = $pegasaas->cache->get_list_of_all_local_files("html");
		//print "<Pre>";
		//var_dump($all_files);
	//	print "</pre>";
		//print "<h3>cache map</h3>";
		//print "<pre>";
		//var_dump(PegasaasAccelerator::$cache_map);
		//print "</pre>";
		
		$average_scandir_time = $total_scandir_time / $total_scandirs;
		PegasaasUtils::log_benchmark("Total Files: ".sizeof($all_files), "debug-li", 1);
		PegasaasUtils::log_benchmark("Total Scandir Time: {$total_scandir_time}", "debug-li", 1);
		PegasaasUtils::log_benchmark("Total Directories: {$total_scandirs}", "debug-li", 1);
		PegasaasUtils::log_benchmark("Average Scandir Time: {$average_scandir_time}", "debug-li", 1);

		PegasaasUtils::log_benchmark("Finish", "debug-li", 1);

	}


	function api_accessibile() {
		global $test_debug;
		global $pegasaas;

		$test_debug 	= true;
		$post_fields 	= array("command" => "test-api-response");
		$args 			= array('timeout' => 30, 'blocking' => true);

		print "<ul>";
		PegasaasUtils::log_benchmark("Start", "debug-li", 1000);

		$pegasaas->api->post($post_fields, $args);

		PegasaasUtils::log_benchmark("Done", "debug-li", 1000);
		print "</ul>";
	}


	function api_submit_optimization_request() {
		global $test_debug;
		global $pegasaas;

		$test_debug 	= true;
		
		print "<ul>";
		PegasaasUtils::log_benchmark("Start", "debug-li", 1000);

		$pegasaas->submit_optimization_request("/", $buffer = "", $test_submission = true);

		PegasaasUtils::log_benchmark("Done", "debug-li", 1000);
		print "</ul>";
	}

	function get_local_cache_stats_all() {
		global $test_debug;
		global $pegasaas;
		global $test_debug_detailed;
		$test_debug = true;
		$test_debug_detailed = true;


		PegasaasUtils::log_benchmark("Start", "debug-li", 1000);

		$page_cache_stats = $pegasaas->cache->get_local_cache_stats("html", $refresh = true);
		PegasaasUtils::log_benchmark("HTML: Done", "debug-li", 1000);

		$combined_css_cache_stats = $pegasaas->cache->get_local_cache_stats("combined.css", $refresh = true);
		PegasaasUtils::log_benchmark("Combined CSS: Done", "debug-li", 1000);

		$deferred_js_cache_stats = $pegasaas->cache->get_local_cache_stats("deferred-js.js", $refresh = true);
		PegasaasUtils::log_benchmark("Deferred JS: Done", "debug-li", 1000);




	}

	static function contact_form_7_compatibility() {
		global $test_debug;
		global $pegasaas;
		$test_debug = true;

		PegasaasUtils::log_benchmark("Start", "debug-li", 1);
		print "Contact Form 7 Active: ";
		if (PegasaasUtils::does_plugin_exists_and_active("contact-form-7")) {
			print "YES<br><br>";
		} else {
			print "NO<br><br>";
		}
		print "Nonce: ".get_option("pegasaas_wp_rest_nonce")."<br><br>";
		print "Pages Containing Forms:<br>";
		print "<pre>";
		var_dump ($pegasaas->post_type_pages["pages-containing-contact-form-7"]);
		print "</pre>";
		PegasaasUtils::log_benchmark("Finish", "debug-li", 1);
	}

	static function contact_form_7_check_rest_nonce() {
		global $test_debug;
		global $pegasaas;
		$test_debug = true;

		PegasaasUtils::log_benchmark("Start", "debug-li", 1);
		print "Contact Form 7 Active: ";
		if (PegasaasUtils::does_plugin_exists_and_active("contact-form-7")) {
			print "YES<br><br>";
		} else {
			print "NO<br><br>";
		}

		PegasaasUtils::log_benchmark("Finish", "debug-li", 1);
	}
}
?>