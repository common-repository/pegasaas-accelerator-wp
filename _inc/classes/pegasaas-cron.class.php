<?php
class PegasaasCron {

	
	function __construct() {
	
	}
	
	
	static function register_cron_events() {
		
		// schedule periodical auto crawl of the website to build cache
		if (!wp_next_scheduled('pegasaas_auto_crawl')) {
			if (!isset(PegasaasAccelerator::$settings['settings']['auto_crawl']['frequency'])) {
				$auto_crawl_frequency = 'five_minutes';
			} else {
				$auto_crawl_frequency = PegasaasAccelerator::$settings['settings']['auto_crawl']['frequency'];
			}
			
			wp_schedule_event(time() + 120, $auto_crawl_frequency, "pegasaas_auto_crawl");
		} else {

			
			// get scheduled timeframe for pegasaas_auto_crawl
			$frequency = wp_get_schedule( 'pegasaas_auto_crawl' );
		
			
			if (!isset(PegasaasAccelerator::$settings['settings']['auto_crawl']['frequency'])) {
				$auto_crawl_frequency = 'five_minutes';
			} else {
				$auto_crawl_frequency = PegasaasAccelerator::$settings['settings']['auto_crawl']['frequency'];
			}
			
		
			
		
			// if it is different from settings, and auto-crawl setting isn't blank, then change it
			if ($frequency != $auto_crawl_frequency) {
				if ($auto_crawl_frequency == "five_minutes" || $auto_crawl_frequency == "ten_minutes" || $auto_crawl_frequency == "fifteen_minutes" || $auto_crawl_frequency == "thirty_minutes") {
					wp_clear_scheduled_hook("pegasaas_auto_crawl");
				
					$next_scheduled_time = wp_next_scheduled('pegasaas_auto_crawl');
					wp_schedule_event($next_scheduled_time, 'five_minutes', "pegasaas_auto_crawl");
				}
			}

		}
		
		// schedule periodical auto crawl of the website to build cache
		if (!wp_next_scheduled('pegasaas_process_cache_clearing_queue')) {
			wp_schedule_event(time(), 'five_minutes', "pegasaas_process_cache_clearing_queue");
		}	
		
		/*	
		if (PegasaasUtils::does_plugin_exists_and_active("contact-form-7")) {
			// schedule periodical check for a changed wp_nonce
			if (!wp_next_scheduled('pegasaas_check_for_changed_nonce')) {
				wp_schedule_event(time(), 'five_minutes', "pegasaas_check_for_changed_nonce");
			}	
		}
		*/

		// schedule periodical pickup of completed optimizations from the api
		if (!wp_next_scheduled('pegasaas_clear_stale_requests')) {
			wp_schedule_event(time(), 'daily', "pegasaas_clear_stale_requests");
		}

		// schedule periodical pickup of completed optimizations from the api
		if (!wp_next_scheduled('pegasaas_pickup_data')) {
			wp_schedule_event(time(), 'ten_minutes', "pegasaas_pickup_data");
		}

		// schedule periodical pickup of completed optimizations from the api
		if (!wp_next_scheduled('pegasaas_calculate_web_perf_metrics')) {
			wp_schedule_event(time(), 'ten_minutes', "pegasaas_calculate_web_perf_metrics");
		}		
		
		// schedule monthly cache clearing of unoptimized cached image 
		if (!wp_next_scheduled('pegasaas_purge_unoptimized_cached_images')) {
			wp_schedule_event(mktime(0,0,0,date("m"), 1), 'monthly', "pegasaas_purge_unoptimized_cached_images");
		}	
		
		// schedule monthly cache clearing cron job
		if (!wp_next_scheduled('pegasaas_auto_clear_page_cache_monthly')) {
			wp_schedule_event(mktime(0,0,0,date("m"), 1), 'monthly', "pegasaas_auto_clear_page_cache_monthly");
		}
		
		// schedule bi-weekly cache clearing cron job
		if (!wp_next_scheduled('pegasaas_auto_clear_page_cache_biweekly')) {
			$time = mktime(0,0,0,date("m"), 1);
			$dow_first_of_month = date("w", $time);
			$dow_offset = 7 - $dow_first_of_month;
			if ($dow_offset == 7 ) {
				$dow_offset = 0;
			}
			wp_schedule_event(mktime(0,0,0,date("m"), 1 + $dow_offset), 'biweekly',  "pegasaas_auto_clear_page_cache_biweekly");
		}		
		
		// sechdule weekly cache clearing cron job
		if (!wp_next_scheduled('pegasaas_auto_clear_page_cache_weekly')) {
			$time = mktime(0,0,0,date("m"), 1);
			$dow_first_of_month = date("w", $time);
			$dow_offset = 7 - $dow_first_of_month;
			if ($dow_offset == 7 ) {
				$dow_offset = 0;
			}
			wp_schedule_event(mktime(0,0,0,date("m"), 1 + $dow_offset), 'weekly',  "pegasaas_auto_clear_page_cache_weekly");
		}
		
		// schedule daily cache clearing cron job -- this is only executed on a page-by-page basis
		if (!wp_next_scheduled('pegasaas_auto_clear_page_cache_daily')) {
			wp_schedule_event(mktime(0,0,0,date("m"), date("d"), date("Y")), 'daily',  "pegasaas_auto_clear_page_cache_daily");
		}		
		
		// schedule daily log rotation cron job 
		if (!wp_next_scheduled('pegasaas_rotate_log_files')) {
			wp_schedule_event(mktime(0,0,0,date("m"), date("d"), date("Y")), 'daily',  "pegasaas_rotate_log_files");
		}		
		
	}
	
	
	static function clear_cron_events() {
		wp_clear_scheduled_hook("pegasaas_auto_crawl");
		wp_clear_scheduled_hook("pegasaas_pickup_data");
		wp_clear_scheduled_hook("pegasaas_process_cache_clearing_queue");
		wp_clear_scheduled_hook("pegasaas_calculate_web_perf_metrics");
		wp_clear_scheduled_hook("pegasaas_purge_unoptimized_cached_images");
		wp_clear_scheduled_hook("pegasaas_clear_stale_requests");
		wp_clear_scheduled_hook("pegasaas_auto_clear_page_cache_daily");
		wp_clear_scheduled_hook("pegasaas_auto_clear_page_cache_biweekly");
		wp_clear_scheduled_hook("pegasaas_auto_clear_page_cache_weekly");
		wp_clear_scheduled_hook("pegasaas_auto_clear_page_cache_monthly");
		wp_clear_scheduled_hook("pegasaas_rotate_log_files");
		wp_clear_scheduled_hook("pegasaas_check_for_changed_nonce");
	}
	
	
	function auto_clear_page_cache_daily() {
		global $pegasaas;

		if ($pegasaas->is_trial() && $pegasaas->trial_days_remaining() == 0) {
			$pegasaas->cache->clear_pegasaas_file_cache();
			$pegasaas->utils->log("CRON: Auto Clear Cache DAILY (trial expired)", "critical");
			return;
		}
		
		$pegasaas->utils->log("CRON: Auto Clear Cache DAILY (start)", "auto_clear_page_cache");
		PegasaasUtils::get_object_meta("/", "accelerator_overrides", true); // just initialize the object if it isn't initialized aleady
	
		foreach ($pegasaas->utils->data_cache["accelerator_overrides"] as $resource_id => $page_level_settings) {
			if ($page_level_settings["accelerated"] == true || $page_level_settings["accelerated"] > 0) {
				if ($page_level_settings["auto_clear_page_cache"] == "daily") {

					$pegasaas->cache->clear_cache($resource_id);
				}
			}
		}
		$pegasaas->utils->log("CRON: Auto Clear Cache DAILY (end)", "auto_clear_page_cache");
	}	
	

	function auto_clear_page_cache_biweekly() {
		global $pegasaas;

		if (PegasaasAccelerator::$settings['settings']['auto_clear_page_cache']['status'] == 3) {
			$pegasaas->utils->log("CRON: Auto Clear Cache BI-WEEKLY", "auto_clear_page_cache");
			$pegasaas->cache->clear_cache();
			
		}
	}	
	
	function auto_clear_page_cache_weekly() {
		global $pegasaas;
		
		if (PegasaasAccelerator::$settings['settings']['auto_clear_page_cache']['status'] == 2) {
			$pegasaas->utils->log("CRON: Auto Clear Cache WEEKLY", "auto_clear_page_cache");
			$pegasaas->cache->clear_cache();
			
		}
	}
	
	function auto_clear_page_cache_monthly() {
		global $pegasaas;
		
		if (PegasaasAccelerator::$settings['settings']['auto_clear_page_cache']['status'] == 1) {
			$pegasaas->utils->log("CRON: Auto Clear Cache MONTHLY", "auto_clear_page_cache");
			$pegasaas->cache->clear_cache();
		}
	}	
	
	function add_custom_cron_intervals($schedules) {
		// schedules stores all recurrence schedules within WordPress
		$schedules['thirty_minutes'] = array(
			'interval'	=> 1800,	// Number of seconds, 1800 is 30 minutes
			'display'	=> __('Once Every 30 Minutes', 'pegasaas-accelerator')
		);

		$schedules['fifteen_minutes'] = array(
			'interval'	=> 900,	// Number of seconds, 900 is 15 minutes
			'display'	=> __('Once Every 15 Minutes', 'pegasaas-accelerator')
		);

		$schedules['ten_minutes'] = array(
			'interval'	=> 600,	// Number of seconds, 600 is 10 minutes
			'display'	=> __('Once Every 10 Minutes', 'pegasaas-accelerator')
		);

		$schedules['five_minutes'] = array(
			'interval'	=> 300,	// Number of seconds, 300 is 5 minutes
			'display'	=> __('Once Every 5 Minutes', 'pegasaas-accelerator')
		);		

		$schedules['one_minute'] = array(
			'interval'	=> 60,	// Number of seconds, 60 is 1 minute
			'display'	=> __('Once Every Minutes', 'pegasaas-accelerator')
		);
		
		$schedules['daily'] = array(
			'interval'	=> 86400,	
			'display'	=> __('Once Daily', 'pegasaas-accelerator')
		);			
		
		$schedules['weekly'] = array(
			'interval'	=> 604800,	
			'display'	=> __('Once Weekly', 'pegasaas-accelerator')
		);	

		$schedules['biweekly'] = array(
			'interval'	=> 1209600,	
			'display'	=> __('Once Every Two Weeks', 'pegasaas-accelerator')
		);	
		
		$schedules['monthly'] = array(
			'interval'	=> 2635200,	
			'display'	=> __('Once Per Month', 'pegasaas-accelerator')
		);			
		// Return our newly added schedule to be merged into the others
		return (array)$schedules; 		
		
	}
	
}