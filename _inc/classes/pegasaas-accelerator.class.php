<?php
if (!class_exists("PegasaasAccelerator")) :
class PegasaasAccelerator {
	static $cache_map = array();
	static $post_type_cpcss = array();
	static $page_level_cpcss = array();
	
	
	var $font_awesome_version;
	var $updater;
	var $instance;
	var $interface;
	var $api;
	var $utils;
	static $settings = array();	
	static $start_time = 0;
	var $buffer; 
	var $ob_buffer;
	var $max_execution_time;
	var $loaded_redirection_excluded_urls = false; // for use in the "is_excluded_url" function
	

	function __construct() {	
		
		
		if (isset($_GET['accelerate']) && $_GET['accelerate'] == "on") {
				add_filter('user_has_cap', '__return_false'); // disable query monitor on the front end if 'acceleration' is explicitly enabled
		}
		
		// gracefully quit, for plugin that use a lot of memory
		if (is_admin() && isset($_GET['page']) && $_GET['page'] == "amp_options") {	
			$this->utils 		= new PegasaasUtils();
			$this->interface 	= new PegasaasInterface();
			$this->instance 	= $this->utils->generate_random_string();
			add_action('admin_init', 	array($this,'admin_init'));
		}		
		
		// disable heartbeat on the pegasaas dashboard
		if (is_admin() && isset($_GET['page']) && $_GET['page'] == "pegasaas-accelerator") {
			    add_action( 'init', array($this, 'stop_heartbeat'), 1 );
		}
		global $pegasaas;
		global $wp_rewrite;
		$pegasaas = $this;
		$this->ob_buffer = "";	
		$this->output_buffer_mode = "functionless";
		$this->max_execution_time = 5; // seconds

		//$this->output_buffer_mode = ""; 

		
		set_error_handler(array($this, 'pegasaas_error_handler'));

		$time = $this->microtime();
		$this->start_time = $time;
		$this->start_memory =  memory_get_peak_usage();
		
		$debug 				= false;
		
		$this->font_awesome_version = 4;
		$this->utils 		= new PegasaasUtils();
	
		$this->interface 	= new PegasaasInterface();
		$this->cache 		= new PegasaasCache();
		$this->api 			= new PegasaasAPI();
		$this->scanner 		= new PegasaasScanner();
		$this->deferment	= new PegasaasDeferment();
		
		$this->data_storage		= new PegasaasDataStorage();

		$this->db			= new PegasaasDB();
		$this->hermes		= new PegasaasHermes();
		$this->instance 	= $this->utils->generate_random_string();
		$this->optimize 	= new PegasaasOptimize();
		$this->cron 		= new PegasaasCron();
 
		
		
		
		//	var_dump($_SERVER);
		//	exit;
		
		$this->init_settings();
		
		// set static data that can be globally accessed
		self::$cache_map 		= $this->cache->assemble_cache_map();
		// get_option("pegasaas_cache_map", array());
		
		if (isset($_GET['accelerate']) && $_GET['accelerate'] == "off" || isset($_GET['web-perf-baseline-scan'])) { 
		  	$this->disable = true;
		}
		
		if (defined('WP_CLI') && WP_CLI === true) {
			$this->disable = true;
			
		}
		
		
		if ($this->is_on_excluded_page()) {
			if (!headers_sent()) {
				header("X-Pegasaas-Message: On Excluded Page");
			}
			$this->disable = true;
		}
	
		$pegasaas->utils->require_wp_include("pluggable");

		if ($this->in_development_mode() && $this->is_development_mode_request() && current_user_can("edit_posts")) {

		} else if (!is_admin() && !$this->is_login() && !$this->disable) {

			$check_for_cache = true;
			if (!is_multisite()) {
				$home_domain = str_replace("https://", "", $this->utils->strip_url_port($this->get_home_url()));
				$home_domain = str_replace("http://", "", $home_domain);
				$home_domain = trim($home_domain, '/');
				$home_domain_data = explode("/", $home_domain);
				$home_domain = $home_domain_data[0];
				
				// if the host is not the registered primary domain, 
				// then we should bypass checking cache and let WordPress  
				// take over the auto-redirect to the primary domain
				if ($_SERVER['HTTP_HOST'] != $home_domain && !PegasaasUtils::wpml_multi_domains_active() ) {
					$check_for_cache = false;
				}
			
			}

			// check for missing trailing slash if necessary
			// this will effectively redirect a page such as /about-us  to /about-us/		
			$permalink_structure = get_option("permalink_structure");
			
			// special handling for instapages
			$instapage_active = PegasaasUtils::instapage_active();
			$is_instapage_url   = $instapage_active && PegasaasUtils::is_instapage_url();

			if (substr($permalink_structure, -1) == "/" && 
					   isset($_SERVER['REDIRECT_URL']) && 
					   strlen($_SERVER['REDIRECT_URL']) > 0 && 
					   substr($_SERVER['REDIRECT_URL'], -1) != '/' &&
					   $_SERVER['REDIRECT_URL'] != '/index.php' /* added 3.6.5 */
					   && (!$is_instapage_url)
					   ) {
				$check_for_cache = false;
				return; // this will bypass all pegasaas and let WordPress do it's redirect.  on some systems, if we don't do this, the page will not redirect and we'll get a blank page.
			
			// if permalink structure does not have a trailing slash then
			// this will effectively redirect a page such as /about-us/ to /about-us
			} else if (substr($permalink_structure, -1) != "/" && isset($_SERVER['REDIRECT_URL']) && strlen($_SERVER['REDIRECT_URL']) > 0 && substr($_SERVER['REDIRECT_URL'], -1) == '/') {
				$check_for_cache = false;
				return; // this will bypass all pegasaas and let WordPress do it's redirect.  on some systems, if we don't do this, the page will not redirect and we'll get a blank page.
			}

			if ($check_for_cache) { 
				

				$this->cache->check_cache();
			}
		
		
		
		} else {
			
			if (is_admin()) {
				$this->utils->log("Do not Check Cache because is_admin", "caching");
			}			
			if ($this->is_login()) {
				$this->utils->log("Do not Check Cache because is_login", "caching");
				
			}
			if (isset( $this->disable) && $this->disable) {
				$this->utils->log("Do not Check Cache because ->disable == true", "caching");			
			}
		}
	
		// disable batcache on wp.com sites as it is not something we can instruct the end user to do.
		global $batcache;
		if (is_object($batcache) && function_exists('batcache_cancel')) {
			batcache_cancel();
		}
		
		
		/** WORDPRESS HOOKS */
		add_action( 'wp',										array($this, 'initialize_registered_page_post_types'));
		PegasaasPluginCompatibility::condition_instapage_hooks();

		add_action( 'plugins_loaded', 							array($this, "load_textdomain"));
		add_action( 'init', 									array($this,'init'), 9999); // ensure that we init as the very last plugin
		add_action( 'template_redirect', 						array($this, 'handle_404'));
		add_action( 'upgrader_process_complete', 				array($this, 'upgrade_completed'));
		add_action( 'update_option_permalink_structure', 		array($this, 'permalink_structure_updated'), 10, 2);
		add_action( 'the_post', 								array($this, 'set_post'));
		add_action( 'admin_init', 								array($this,'admin_init')); 
		add_action( 'current_screen', 							array($this->interface, 'add_bulk_pagepost_functions') );
		
		if ($this->is_trial() && $this->trial_days_remaining() == 0) {
		} else {
			add_action( 'shutdown', array($this, 'shutdown'), -99999); // sure this is the very first shutdown register
		}
		
		/** PEGASAAS HOOKS */
		add_action( 'pegasaas_pickup_data', 					array($this->api, "pickup_queued_requests"));
		add_action( 'pegasaas_calculate_web_perf_metrics', 		array($this->scanner, "calculate_web_pef_metrics"));
		add_action(	'pegasaas_auto_crawl', 						array($this, "auto_crawl"));

		add_action( 'pegasaas_clear_stale_requests', 			array($this, "clear_stale_requests"));
		add_action( 'pegasaas_process_cache_clearing_queue', 	array($this->cache, "clear_queued_cache_resources"));
		add_action( 'pegasaas_check_for_changed_nonce', 		array($this, "has_wp_rest_nonce_changed"));
		add_action( 'pegasaas_auto_clear_page_cache_daily', 	array($this->cron, "auto_clear_page_cache_daily"));
		add_action( 'pegasaas_auto_clear_page_cache_biweekly', 	array($this->cron, "auto_clear_page_cache_biweekly"));
		add_action( 'pegasaas_auto_clear_page_cache_weekly', 	array($this->cron, "auto_clear_page_cache_weekly"));
		add_action( 'pegasaas_auto_clear_page_cache_monthly', 	array($this->cron, "auto_clear_page_cache_monthly"));
		add_action( 'pegasaas_rotate_log_files', 				array($this->utils, 'rotate_log_files'));
		add_action( 'pegasaas_purge_unoptimized_cached_images', array($this->cache, "purge_unoptimized_cached_images"));
		
		/** FILTERS **/
		add_filter('cron_schedules', 							array($this->cron, 'add_custom_cron_intervals'), 10, 1);

		$this->utils->log("Pegasaas Invoked for {$_SERVER['REQUEST_URI']} (".($this->execution_time()).")", "script_execution_benchmarks");
		//$this->utils->log("Pegasaas Invoked for {$_SERVER['REQUEST_URI']} (".($this->execution_time()).")");
		
	}

	function load_textdomain() {
		PegasaasUtils::log("load_textdomain ('plugins_loaded' fired)", "script_execution_benchmarks");

		if ( function_exists( 'get_user_locale' ) && is_admin() ) {
			$locale = get_user_locale();
		} else {
			$locale = get_locale();
		}

		$locale = apply_filters( 'plugin_locale', $locale, 'pegasaas-accelerator-wp' );

		load_textdomain( 'pegasaas-accelerator-wp', WP_LANG_DIR . "/plugins/pegasaas-accelerator-wp/pegasaas-accelerator-wp-$locale.mo" );
		load_plugin_textdomain( 'pegasaas-accelerator-wp' );
	}
	

    function stop_heartbeat() {
    	wp_deregister_script('heartbeat');
    }
	
	
	
	function init_settings() {

		PegasaasAccelerator::$settings = get_option("pegasaas_settings");

		PegasaasAccelerator::$settings['settings']['logging']['log_submit_scans'] 				= get_option("pegasaas_logging_log_submit_scans");
		PegasaasAccelerator::$settings['settings']['logging']['log_server_info'] 				= get_option("pegasaas_logging_log_server_info");
		PegasaasAccelerator::$settings['settings']['logging']['log_submit_benchmark_scans'] 	= get_option("pegasaas_logging_log_submit_benchmark_scans");
		PegasaasAccelerator::$settings['settings']['logging']['log_process_pagespeed_scans'] 	= get_option("pegasaas_logging_log_process_pagespeed_scans");
		PegasaasAccelerator::$settings['settings']['logging']['log_process_benchmark_scans'] 	= get_option("pegasaas_logging_log_process_benchmark_scans");
		PegasaasAccelerator::$settings['settings']['logging']['log_semaphores'] 				= get_option("pegasaas_logging_log_semaphores");
		PegasaasAccelerator::$settings['settings']['logging']['log_html_conditioning'] 		= get_option("pegasaas_logging_log_html_conditioning");
		PegasaasAccelerator::$settings['settings']['logging']['log_cpcss'] 					= get_option("pegasaas_logging_log_cpcss");
		PegasaasAccelerator::$settings['settings']['logging']['log_image_data'] 				= get_option("pegasaas_logging_log_image_data");
		PegasaasAccelerator::$settings['settings']['logging']['log_api'] 						= get_option("pegasaas_logging_log_api");
		PegasaasAccelerator::$settings['settings']['logging']['log_pickup_queued_requests'] 	= get_option("pegasaas_logging_log_pickup_queued_requests");
		PegasaasAccelerator::$settings['settings']['logging']['log_auto_crawl'] 				= get_option("pegasaas_logging_log_auto_crawl");
		PegasaasAccelerator::$settings['settings']['logging']['log_auto_clear_page_cache'] 	= get_option("pegasaas_logging_log_auto_clear_page_cache");
		PegasaasAccelerator::$settings['settings']['logging']['log_script_execution_benchmarks'] = get_option("pegasaas_logging_log_script_execution_benchmarks");
		PegasaasAccelerator::$settings['settings']['logging']['log_caching'] 					= get_option("pegasaas_logging_log_caching");
		PegasaasAccelerator::$settings['settings']['logging']['log_database'] 					= get_option("pegasaas_logging_log_database");
		PegasaasAccelerator::$settings['settings']['logging']['log_file_permissions'] 			= get_option("pegasaas_logging_log_file_permissions");
		PegasaasAccelerator::$settings['settings']['logging']['log_cloudflare'] 				= get_option("pegasaas_logging_log_cloudflare");
		PegasaasAccelerator::$settings['settings']['logging']['log_varnish'] 				= get_option("pegasaas_logging_log_varnish");
		PegasaasAccelerator::$settings['settings']['logging']['log_pegasaas_only'] 				= get_option("pegasaas_logging_log_pegasaas_only", 1);
		PegasaasAccelerator::$settings['settings']['logging']['log_data_structures'] 				= get_option("pegasaas_logging_log_data_structures");
		PegasaasAccelerator::$settings['settings']['logging']['log_E_DEPRECATED'] 				= get_option("pegasaas_logging_log_E_DEPRECATED");
		PegasaasAccelerator::$settings['settings']['logging']['log_E_WARNING'] 				= get_option("pegasaas_logging_log_E_WARNING");
		PegasaasAccelerator::$settings['settings']['logging']['log_E_NOTICE'] 				= get_option("pegasaas_logging_log_E_NOTICE");
		PegasaasAccelerator::$settings['settings']['logging']['compatibility_contact_form_7'] 				= get_option("pegasaas_logging_compatibility_contact_form_7");

		
		PegasaasAccelerator::$settings['settings']['inject_critical_css']['global_cpcss_template_override'] 	= get_option("pegasaas_inject_critical_css_global_cpcss_template_override", array());

		
		PegasaasAccelerator::$settings['settings']['cloudflare']['api_key'] 		= get_option("pegasaas_cloudflare_api_key");
		PegasaasAccelerator::$settings['settings']['cloudflare']['account_email'] 	= get_option("pegasaas_cloudflare_account_email");
		PegasaasAccelerator::$settings['settings']['cloudflare']['authorization_type'] 	= get_option("pegasaas_cloudflare_authorization_type");
		PegasaasAccelerator::$settings['settings']['cloudflare']['zone_id'] 	= get_option("pegasaas_cloudflare_zone_id");

		
		
		PegasaasAccelerator::$settings['settings']['blog']['status'] = 1;
		PegasaasAccelerator::$settings['settings']['blog']['name'] = "Blog Settings";
		PegasaasAccelerator::$settings['settings']['blog']['home_page_accelerated'] 	= get_option("pegasaas_blog_home_page_accelerated", 1);
		PegasaasAccelerator::$settings['settings']['blog']['categories_accelerated'] 	= get_option("pegasaas_blog_categories_accelerated", 1);
		PegasaasAccelerator::$settings['settings']['blog']['pagination_accelerated'] 	= get_option("pegasaas_blog_pagination_accelerated", 1);

		PegasaasAccelerator::$settings['settings']['ssl_warning_override']['status'] = 1;
		PegasaasAccelerator::$settings['settings']['ssl_warning_override']['name'] = "Ignore SSL Warning";
		
		
		PegasaasAccelerator::$settings['settings']['dynamic_urls']['utm_source'] 		= get_option("pegasaas_dynamic_urls_utm_source", 1);
		PegasaasAccelerator::$settings['settings']['dynamic_urls']['utm_medium'] 		= get_option("pegasaas_dynamic_urls_utm_medium", 1);
		PegasaasAccelerator::$settings['settings']['dynamic_urls']['utm_campaign'] 	= get_option("pegasaas_dynamic_urls_utm_campaign", 1);
		PegasaasAccelerator::$settings['settings']['dynamic_urls']['utm_term'] 		= get_option("pegasaas_dynamic_urls_utm_term", 1);
		PegasaasAccelerator::$settings['settings']['dynamic_urls']['utm_content'] 		= get_option("pegasaas_dynamic_urls_utm_content", 1);
		PegasaasAccelerator::$settings['settings']['dynamic_urls']['gclid'] 			= get_option("pegasaas_dynamic_urls_gclid", 1);
		PegasaasAccelerator::$settings['settings']['dynamic_urls']['keyword'] 			= get_option("pegasaas_dynamic_urls_keyword", 1);
		PegasaasAccelerator::$settings['settings']['dynamic_urls']['additional_args'] 	= get_option("pegasaas_dynamic_urls_additional_args");
	
		
		PegasaasAccelerator::$settings['settings']['exclude_urls']['urls'] = get_option("pegasaas_exclude_urls_urls");
			
		PegasaasAccelerator::$settings['settings']['defer_render_blocking_js']['exclude_scripts']	= get_option("pegasaas_defer_render_blocking_js_exclude_scripts");
		PegasaasAccelerator::$settings['settings']['lazy_load']['exclude_urls'] = get_option("pegasaas_lazy_load_exclude_urls");;

		PegasaasAccelerator::$settings['settings']['strip_google_fonts_mobile']['custom_fonts'] 			= get_option("pegasaas_strip_google_fonts_mobile_custom_fonts");



		PegasaasAccelerator::$settings['settings']['defer_render_blocking_css']['exclude_stylesheets'] = get_option("pegasaas_defer_render_blocking_css_exclude_stylesheets");
		PegasaasAccelerator::$settings['settings']['defer_unused_css']['exclude_stylesheets'] 		= get_option("pegasaas_defer_unused_css_exclude_stylesheets");
		PegasaasAccelerator::$settings['settings']['dns_prefetch']['additional_domains'] 			= get_option("pegasaas_dns_prefetch_additional_domains");
		PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['additional_scripts'] 		= get_option("pegasaas_lazy_load_scripts_additional_scripts");
		PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts'] 			= get_option("pegasaas_lazy_load_scripts_custom_scripts");
		PegasaasAccelerator::$settings['settings']['lazy_load_images']['exclude_images'] 			= get_option("pegasaas_lazy_load_images_exclude_images");
		PegasaasAccelerator::$settings['settings']['lazy_load_background_images']['exclude_images'] = get_option("pegasaas_lazy_load_background_images_exclude_images");
		if (isset(PegasaasAccelerator::$settings['settings']['image_optimization'])) {
			PegasaasAccelerator::$settings['settings']['image_optimization']['exclude_images'] 		= get_option("pegasaas_image_optimization_exclude_images");
			PegasaasAccelerator::$settings['settings']['image_optimization']['exclude_images_from_auto_sizing'] 		= get_option("pegasaas_image_optimization_exclude_images_from_auto_sizing");
			if (!isset(PegasaasAccelerator::$settings['settings']['image_optimization']['auto_size_images'] )) {
				PegasaasAccelerator::$settings['settings']['image_optimization']['auto_size_images'] = 1;
			}
		}
		PegasaasAccelerator::$settings['settings']['preload_user_files']['resources'] 		= get_option("pegasaas_preload_user_files_resources");
		PegasaasAccelerator::$settings['settings']['essential_css']['css'] 		= get_option("pegasaas_essential_css_css");
		
		PegasaasAccelerator::$settings['settings']['auto_size_images']['exclude_images'] 			= get_option("pegasaas_auto_size_images_exclude_images");
	
		
		if ($this->utils->does_plugin_exists_and_active("instagram-feed")) {
			PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['instagram_feed'] = get_option("pegasaas_lazy_load_scripts_instagram_feed", 1);
			
			if (!isset(PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']['/plugins/instagram-feed/js/sb-instagram.min.js'])) {
				PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']["/plugins/instagram-feed/js/sb-instagram.min.js"] = array(
					"status" => 1,
					"mobile_status" => 1,
					"desktop_status" => 1,
					"is_globally_available" => true);
			} else {
				PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']["/plugins/instagram-feed/js/sb-instagram.min.js"]['is_globally_available'] = 1;
			}

			PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['have_third_party_plugin'] = true;
		}

		if ($this->utils->does_plugin_exists_and_active("thirstyaffiliates")) {
			PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['thirstyaffiliates'] = get_option("pegasaas_lazy_load_scripts_thirstyaffiliates", 1);
			PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['have_third_party_plugin'] = true;
			
			if (!isset(PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']['/plugins/thirstyaffiliates/js/app/ta.js'])) {

				PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts'][ "/plugins/thirstyaffiliates/js/app/ta.js"] = array(
					"status" => 1,
					"mobile_status" => 1,
					"desktop_status" => 1,
				"is_globally_available" => true);			
			}else {
				PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']["/plugins/thirstyaffiliates/js/app/ta.js"]['is_globally_available'] = 1;
			}
		}
		
		
		
	
		if ($this->utils->does_plugin_exists_and_active("jetpack")) {
			PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['jetpack_twitter'] = get_option("pegasaas_lazy_load_scripts_jetpack_twitter", 1);
			PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['have_third_party_plugin'] = true;
			
			if (!isset(PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']['/plugins/jetpack/_inc/build/twitter-timeline.min.js'])) {
		
				PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']["/plugins/jetpack/_inc/build/twitter-timeline.min.js"] = array(
						"status" => 1,
						"mobile_status" => 1,
						"desktop_status" => 1,
						"is_globally_available" => true);	
			} else {
				PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']["/plugins/jetpack/_inc/build/twitter-timeline.min.js"]['is_globally_available'] = 1;
			}

			PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['jetpack_facebook'] = get_option("pegasaas_lazy_load_scripts_jetpack_facebook", 1);
			PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['have_third_party_plugin'] = true;
			
			if (!isset(PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']['/plugins/jetpack/_inc/build/facebook-embed.min.js'])) {

				PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']["/plugins/jetpack/_inc/build/facebook-embed.min.js"] = array(
						"status" => 1,
						"mobile_status" => 1,
						"desktop_status" => 1,
						"is_globally_available" => 1);	
			}else {
				PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']["/plugins/jetpack/_inc/build/facebook-embed.min.js"]['is_globally_available'] = 1;
			}
		}
		
		
		if ($this->utils->does_plugin_exists_and_active("woocommerce")) {
			PegasaasAccelerator::$settings['settings']['woocommerce']['product_tags_accelerated'] 			= get_option("pegasaas_woocommerce_product_tags_accelerated", 1);
			PegasaasAccelerator::$settings['settings']['woocommerce']['product_categories_accelerated'] 	= get_option("pegasaas_woocommerce_product_categories_accelerated", 1);
		} else {
			unset(PegasaasAccelerator::$settings['settings']['woocommerce']);
		}
		
		

		if (!isset(PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']['https://www.google.com/recaptcha/api.js'])) {
				PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']["https://www.google.com/recaptcha/api.js"] = array(
					"status" => 1,
					"mobile_status" => 1,
					"desktop_status" => 1,
					"is_globally_available" => true);
		} else {
			PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']["https://www.google.com/recaptcha/api.js"]['is_globally_available'] = 1;
		}
		
		// default wordpress scripts that are lazy-loadable
		if (!isset(PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']['/wp-includes/js/comment-reply.min.js'])) {
				PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']["/wp-includes/js/comment-reply.min.js"] = array(
					"status" => 1,
					"mobile_status" => 1,
					"desktop_status" => 1,
					"is_globally_available" => true);
		} else {
			PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']["/wp-includes/js/comment-reply.min.js"]['is_globally_available'] = 1;
		}		

		if (!isset(PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']['/wp-includes/js/wp-embed.min.js'])) {
				PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']["/wp-includes/js/wp-embed.min.js"] = array(
					"status" => 1,
					"mobile_status" => 1,
					"desktop_status" => 1,
					"is_globally_available" => true);
		} else {
			PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']["/wp-includes/js/wp-embed.min.js"]['is_globally_available'] = 1;
		}

		if (!isset(PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']['/wp-includes/js/wp-emoji-release.min.js'])) {
				PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']["/wp-includes/js/wp-emoji-release.min.js"] = array(
					"status" => 1,
					"mobile_status" => 1,
					"desktop_status" => 1,
					"is_globally_available" => true);
		} else {
				PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']["/wp-includes/js/wp-emoji-release.min.js"]['is_globally_available'] = 1;
		}		
		
		PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['have_third_party_plugin'] = true;
		
		
		
		if (!$this->utils->does_plugin_exists_and_active("accelerated-mobile-pages") && !$this->utils->does_plugin_exists_and_active("accelerated-moblie-pages")) {
			unset(PegasaasAccelerator::$settings['settings']['accelerated_mobile_pages']);
		}
		
		
		if (!$this->utils->is_themify_theme()) {
			unset(PegasaasAccelerator::$settings['settings']['themify']);
		}
		
		if (!$this->utils->is_thrive_theme()) {
			unset(PegasaasAccelerator::$settings['settings']['thrive']);
		}		
		
		if (!$this->utils->is_avada_theme()) {
			unset(PegasaasAccelerator::$settings['settings']['avada']);
		}								
		
		if (!$this->utils->does_plugin_exists_and_active("wordlift")) {
			unset(PegasaasAccelerator::$settings['settings']['wordlift']);
		}

		if (!$this->utils->is_wpx_server()) {
			unset(PegasaasAccelerator::$settings['settings']['wpx_cloud']);
		}		
		
		if (!($this->utils->does_plugin_exists_and_active("elementor") || $this->utils->does_plugin_exists_and_active("elementor-pro"))) {
			unset(PegasaasAccelerator::$settings['settings']['elementor_compatibility']);
		}
	}
	function init_get() {
		
		/* initialize _GET variables to avoid notices */
		if (!isset($_GET['accelerate'])) 				{	$_GET['accelerate']					= "";}
		if (!isset($_GET['build-cpcss'])) 				{	$_GET['build-cpcss']				= "";}
		if (!isset($_GET['debug'])) 					{	$_GET['debug'] 						= "";}
		if (!isset($_GET['c'])) 						{	$_GET['c'] 							= "";}
		if (!isset($_GET['pegasaas_debug'])) 			{	$_GET['pegasaas_debug'] 			= "";}
		if (!isset($_GET['tab'])) 						{	$_GET['tab'] 						= "";}
		if (!isset($_GET['retest_interface_load_time'])) {	$_GET['retest_interface_load_time'] = "";}


		
	}
	
	/* handle deconstruction */
	function __destruct() {
		$this->utils->log("Pegasaas End for {$_SERVER['REQUEST_URI']} (".$this->execution_time().")", "script_execution_benchmarks");
	//	$this->utils->log("Pegasaas End for {$_SERVER['REQUEST_URI']} (".$this->execution_time().")");
		
		PegasaasUtils::log_write();
	}	 
	
	/* handles logging of errors */
	function pegasaas_shutdown_handler() {
		global $pegasaas;

		$error = error_get_last();

		if($error['type'] != ""){
			$this->pegasaas_error_handler($error['type'], $error['message'], $error['file'], $error['line']);

		}

	}
	function auto_crawl() {
		$debug = false;
		global $test_debug;
		if ($test_debug) {
			$debug = $test_debug;
		}

		$this->max_execution_time = 15;
		
		
		if (PegasaasAccelerator::$settings['settings']['auto_crawl']['status'] == 0) {
			$this->utils->log("Auto Crawl Disabled", "auto_crawl");
		} else {
			if (isset(PegasaasAccelerator::$settings['settings']['auto_crawl']['max_execution_time'])) {
				$this->max_execution_time = PegasaasAccelerator::$settings['settings']['auto_crawl']['max_execution_time'];
			}
			
			$this->utils->log("Auto Crawl Max Execution Time: {$this->max_execution_time}", "auto_crawl");
			if ($debug) {
				print "<pre class='admin'>";
			}
			$this->utils->log("Starting Auto Crawl", "auto_crawl");
			if ($debug) {
				print "--Auto Crawl Max Execution Time: {$this->max_execution_time}\n\n";
			}			
			// if there are queued resources that are to be cached, then we should execute those requests first
			$this->cache->clear_queued_cache_resources();
			
			if ($this->execution_time() > $this->max_execution_time) {
				$this->utils->log("Ending Auto Crawl Early due to Execution Time Exceeding Max Execution Time", "auto_crawl");
			}

			/* DETERMINE THE NUMBER OF CACHE BUILDS (HITS) TO INVOKE */
			$throttle_rate = PegasaasAccelerator::$settings['settings']['response_rate']['status'];

			if ($throttle_rate == "maximum" || $throttle_rate == "aggressive") {
				$maximum_submits_per_invocation = 10;
			} else if ($throttle_rate == "normal") {
				$maximum_submits_per_invocation = 5;
			} else if ($throttle_rate == "kinsta-normal") {
				$maximum_submits_per_invocation = 5;
			} else {
				$maximum_submits_per_invocation = 3;
			}

			if (isset(PegasaasAccelerator::$settings['settings']['auto_crawl']['max_pages_to_crawl_per_invocation']) && PegasaasAccelerator::$settings['settings']['auto_crawl']['max_pages_to_crawl_per_invocation'] != 'default') {
			
				$maximum_submits_per_invocation = PegasaasAccelerator::$settings['settings']['auto_crawl']['max_pages_to_crawl_per_invocation'];
			}
			$this->utils->log("Auto Crawl Max Submits Per Invocation: {$maximum_submits_per_invocation}", "auto_crawl");
			if ($debug) {
				print "--Auto Crawl Max Submits Per Invocation: {$maximum_submits_per_invocation}\n\n";
			}
			// check if is page caching on
			if (PegasaasAccelerator::$settings['settings']['page_caching']['status'] != 0) { 
				// get all pages and posts
				$posts = $this->utils->get_all_pages_and_posts();

				// iterate through all pages and posts
				foreach ($posts as $post) {

					$resource_id 			= $post->resource_id;
					$page_level_settings 	= PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides");

					if (!$post->accelerated) {
						continue;
					}									

					$this_page_caching_status = 0;

						if ((PegasaasAccelerator::$settings['settings']['page_caching']['status'] == 1 && $page_level_settings['page_caching'] != "0") ||
							(PegasaasAccelerator::$settings['settings']['page_caching']['status'] == "0" && $page_level_settings['page_caching'] == "1")){
							$this_page_caching_status = 1;
						}


						if ($this_page_caching_status) {


							if ($debug) { print "Status ({$resource_id}): Page Caching Enabled\n"; }
							// check page cache
							//$cache_data_map = get_option("pegasaas_cache_map", array());
							$cache_data_map	= PegasaasAccelerator::$cache_map;
							
							$url = $resource_id;
							$url						= str_replace($this->get_home_url(), "", $url);
							if ($url == "") {
								$url = "/";
							}


							if (!$cache_data_map["{$url}"]) {							
								$this->cache->clear_third_party_cache($post->ID); 
								if ($this->cache->cloudflare_exists()) {
									$this->cache->purge_cloudflare($url);
								}
								
								$request_this_resource = false;
								if ($page_cache_request_count < $maximum_submits_per_invocation) {
		
									$page_cache_request_count++;
									if ($debug) {
										print "--Auto Crawl START For: {$url}\n";
									}
									$this->utils->log("Auto Crawl START For: {$url}", "auto_crawl");
									$this->utils->touch_url($url, array('blocking' => false));
									$this->utils->log("Auto Crawl COMPLETE For: {$url}", "auto_crawl");

									if ($debug) {
										print "--Auto Crawl COMPLETE For: {$url}\n\n";
									}

								} else {
									// $this->utils->log("Cache Limit Per Invocation Reached.  Did not Cache {$url}", "auto_crawl");

									if ($debug) {
										print "--Cache Limit Per Invocation Reached\n\n";
									}

									// exit out of function because we've created cache for maximum number of pages
									break;
								}
							} else {
								// $this->utils->log("Cache Already Exists for {$url}", "auto_crawl");
								if ($debug) { 

								  print "--Cache Already Exists\n\n";
								}
							}
						}		
				}
			}
			$this->utils->log("Ending Auto Crawl", "auto_crawl");

			if ($debug) { print "</pre>"; }
		}
	}	
	function pegasaas_error_handler($errno, $errstr, $errfile, $errline ) {
		global $pegasaas;
		$always_log = array("E_PARSE", "E_ERROR", "E_STRICT", "E_CORE_ERROR", "E_COMPILE_ERROR", "E_USER_ERROR");

		
		switch ($errno){

			case E_ERROR: // 1 //
				$typestr = 'E_ERROR'; break;
			case E_WARNING: // 2 //
				$typestr = 'E_WARNING'; break;
			case E_PARSE: // 4 //
				$typestr = 'E_PARSE'; break;
			case E_NOTICE: // 8 //
				$typestr = 'E_NOTICE'; break;
			case E_CORE_ERROR: // 16 //
				$typestr = 'E_CORE_ERROR'; break;
			case E_CORE_WARNING: // 32 //
				$typestr = 'E_CORE_WARNING'; break;
			case E_COMPILE_ERROR: // 64 //
				$typestr = 'E_COMPILE_ERROR'; break;
			case E_CORE_WARNING: // 128 //
				$typestr = 'E_COMPILE_WARNING'; break;
			case E_USER_ERROR: // 256 //
				$typestr = 'E_USER_ERROR'; break;
			case E_USER_WARNING: // 512 //
				$typestr = 'E_USER_WARNING'; break;
			case E_USER_NOTICE: // 1024 //
				$typestr = 'E_USER_NOTICE'; break;
			case E_STRICT: // 2048 //
				$typestr = 'E_STRICT'; break;
			case E_RECOVERABLE_ERROR: // 4096 //
				$typestr = 'E_RECOVERABLE_ERROR'; break;
			case E_DEPRECATED: // 8192 //
				$typestr = 'E_DEPRECATED'; break;
			case E_USER_DEPRECATED: // 16384 //
				$typestr = 'E_USER_DEPRECATED'; break;

		}
		
		if (isset(PegasaasAccelerator::$settings['settings']) && is_array(PegasaasAccelerator::$settings['settings']['logging']) && array_key_exists("log_pegasaas_only", PegasaasAccelerator::$settings['settings']['logging']) && PegasaasAccelerator::$settings['settings']['logging']["log_pegasaas_only"] == 1) {
			
			if (strlen($errfile) > 10 && !strstr($errfile, "pegasaas-accelerator-wp")) {
				return;
			}
		}
			
		if (isset(PegasaasAccelerator::$settings['settings']) && is_array(PegasaasAccelerator::$settings['settings']['logging']) && array_key_exists("log_{$typestr}", PegasaasAccelerator::$settings['settings']['logging']) && PegasaasAccelerator::$settings['settings']['logging']["log_{$typestr}"] == 1) {
				
		} else if (in_array($typestr, $always_log) || $typestr == "E_ERROR") {
				
		} else {
			return;
		}
		//print "[".in_array($typestr, $always_log)."]\n";
		$message = $typestr.': '.$errstr.' in '.$errfile.' on line #'.$errline;
		//print "ff".$message."\n";
		//exit;
		
		if ($errno != "" && $pegasaas->utils) { 
			$pegasaas->utils->log($message, "error");
		}
	}

	
	function get_image_data($post = "", $resource_id = "") {
		
		
		if ($resource_id == "") {
			$resource_id = $this->utils->get_object_id();
		}
		if ($post == "") {
			$post = $this->post;
		}
		
		
		$post_id = $post->ID;
		if (isset($_GET['pegasaas_debug']) && $_GET['pegasaas_debug'] == "1") {
			print "post_id={$post_id}\n";
			print "resource_id={$resource_id}\n";
		}
		
		if ($post->ID == "" && $resource_id != "") {
			$post = $this->utils->get_post_object($resource_id);
			if ($post->is_category) {
				if (isset($_GET['pegasaas_debug']) && $_GET['pegasaas_debug'] == "1") {
					print "YES is category of type {$post->category_post_type}\n";
				}
				$post->post_type = $post->category_post_type;
			}
			
			if ($post->ID == "") {
				@$post->ID = url_to_postid($resource_id);
			}
				//print sizeof($all_pages_and_posts);
			
		
				
			//need to add in handling here to check to see if it is a category
			
		} else if ($post->ID == "" && $resource_id == "") {
			return array("image_data" => "");
		}

		$this->utils->log("get_image_data resource_id {$resource_id}", "image_data");
		$this->utils->log("get_image_data post->slug {$post->slug}  -- id {$post->ID}", "image_data");
		
		$debug_backtrace = debug_backtrace();
		$calling_file = explode("/", $debug_backtrace[0]['file']);
		$calling_file = array_pop($calling_file);
		$calling_function = $debug_backtrace[1]['function'];
		$calling_class = $debug_backtrace[1]['class'];
		$calling_line = $debug_backtrace[1]['line'];

		$debug_backtrace_string = "{$calling_file} ".($calling_class != "" ? $calling_class."->" : "")."{$calling_function}() line #{$calling_line}";

		$this->utils->log("image_data calling function {$debug_backtrace_string}", "image_data");
		
		
		// check to see if page level critical path CSS exists and i
		// if it does, if it is not stale, then use it
		$critical_css 			= PegasaasUtils::get_object_meta($resource_id, 'image_data');
		
		if (is_array($critical_css) && array_key_exists("image_data", $critical_css) && strlen($critical_css['image_data']) > 0) {
			$page_level_css_exists = true;
			$critical_css['type'] = "page_level";
		}
		
		if (is_array($critical_css) && array_key_exists("built", $critical_css)) {
			$page_level_build_date = $critical_css['built']; 
		}
		
		if ($page_level_css_exists) {
			
			// if it is stale, request fresh cpcss for this page
			if ($page_level_build_date < strtotime($post->post_date)) {
				$existing_requests 		= get_option("pegasaas_pending_image_data_request", array());
				$existing_page_requests = array();
			
				// check to see if there is an existing queued request
				if (array_key_exists($resource_id, $existing_requests)) {
					$critical_css['queued'] = true;
				
				// if there is no existing queued request, then execute a request for critical path css
				} else {
					$critical_css['queued'] = $this->request_image_data("", $post, $resource_id);
				}			
				
				// rebuild
				$critical_css['status'] = 'Page Level Image Data Being Rebuilt';
				$critical_css['skip'] 	= true;
			} else {
				$critical_css['skip'] 	= false;
			}
		
			
		// if it does not exist, does the post type cpcss exist
		} else {
	
			if ($post->ID === 0 || $post->ID == get_option("page_on_front")) {
				$post_type = "home_page"; 
				$post_type_url = $this->get_home_url();
			} else {
				if ($post->post_type == "") {
					$post_type = get_post_type($post->ID);
				} else {
					$post_type = $post->post_type;
				}
				
				$post_type_obj = $this->utils->get_post_type_object($post_type);
				
				$post_type_url = get_permalink( $post_type_obj->ID );
				if (!strstr($post_type_url, $this->get_home_url())) {
					$post_type_url = $this->get_home_url()."/".$post_type_obj->post_name."/";
				}				
				//print "y".$post_type_url;
			}
			

		
			$critical_css 	= PegasaasUtils::get_object_meta("post_type__".$post_type, "image_data");

			if (is_array($critical_css) && array_key_exists("image_data", $critical_css) && strlen($critical_css['image_data']) > 0) {
				$post_type_css_exists = true;
				
				$critical_css['type'] = "post_type";
				$critical_css['post_type'] = $post_type;
				//print "yes, it is good";
			}

			if (is_array($critical_css) && array_key_exists("built", $critical_css)) {
				$post_type_build_date = $critical_css['built']; 
			}			
			if ($post_type_css_exists) {
				
				$critical_css['skip'] = false;
			} else {
				$existing_requests 		= get_option("pegasaas_pending_image_data_request", array());
				$existing_page_requests = array();
			
				// check to see if there is an existing queued request
				if (array_key_exists("post_type__".$post_type, $existing_requests)) {
					$critical_css['queued'] = true;
				
				// if there is no existing queued request, then execute a request for critical path css
				} else {
					$post_type_url = str_replace($this->get_home_url(), "", $post_type_url);
					$critical_css['queued'] = $this->request_image_data("", "", $post_type_url,  $post_type);
				}						
				
				
				$critical_css['skip'] = true;
				$critical_css['status'] = 'Post Type Image Data Being Built';
				// request post type critical css
			}
		}
		
		
	// if we are not skipping, then condition the CPCSS
		if (!$critical_css['skip']) {
			
			$critical_css['image_data'] = json_decode(stripslashes($critical_css['image_data']), true);
			//$critical_css['css'] = str_replace('\"', '"', $critical_css['css']);
		//	$critical_css['css'] = str_replace("content:'\\\\f", "content:'\\f", $critical_css['css']);
		//	$critical_css['css'] = str_replace("content:'\\\\e", "content:'\\e", $critical_css['css']);
			
		}
			
		
		
		return $critical_css;

	}		
	
	
	static function get_image_data_records($type) {	
		$all = get_option('pegasaas_image_data', array());
		$return = array();

		foreach ($all as $id => $item) {
			if (($type == "post_type" || $type == "all") && strstr($id, "post_type__")) {
			  $return["$id"] = $item;	
			
			}
			if (($type == "custom" || $type == "all") && !strstr($id, "post_type__")) {
			  $return["$id"] = $item;	
			
			}
			
		}
		return $return;
		
	}	
	
	
	function can_optimize() {
		return (PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining'] > 0);
	}
	
	function trial_days_remaining() {

		if (!$this->is_trial()) {
			return -1;
		} else {
			
		
			if (!isset(PegasaasAccelerator::$settings['trial_expires'])) {
				
				return -2; // trial has expired
			} else {
				$days = ceil(( number_format(PegasaasAccelerator::$settings['trial_expires'] - time(), 0, '.', '')) / 86400 );
				
				if ($days < 0) {
					return 0;
				} else {
					return $days;
				}
			}
		}
	}
	
	function is_trial() {
		return (PEGASAAS_ACCELERATOR_TYPE == "trial" && !isset(PegasaasAccelerator::$settings['subscription'])) ||
			 (isset(PegasaasAccelerator::$settings['subscription']) && strstr(PegasaasAccelerator::$settings['subscription'], "trial"));
	}
	
	function is_standard() {
		return PEGASAAS_ACCELERATOR_TYPE == "standard" || PEGASAAS_ACCELERATOR_TYPE == "trial";
	}

	function is_free() {
		return PegasaasAccelerator::$settings['subscription'] != "" && PegasaasAccelerator::$settings['settings']['cdn'] == "";
	}		

	function is_pro_edition() {
		return PEGASAAS_ACCELERATOR_TYPE == "pro";
	}	

	
	function is_pro() {
		return PegasaasAccelerator::$settings['subscription'] != "" && PegasaasAccelerator::$settings['settings']['cdn'] != "";
	}	
	

	
	function handle_404() {
		$debug = false;

		
		if (is_404()) {
			// exit out of doing anything special, if htis is an instapage
			if (PegasaasUtils::instapage_active() && PegasaasUtils::is_instapage_url()) {
				exit();
			}


			// signal to the cache handling system to not save this page, otherwise there could be
			// bad cache folders such as pegsasaas-cache/index.html/
			$this->cache->save_cached_copy = false;
			
			$requested_url = $_SERVER['REQUEST_URI'];
			$requested_url = $this->utils->strip_query_string($requested_url);
			
			if (strstr($requested_url, "~~~")) {
				$requested_url = str_replace("~~~", ".", $requested_url);
				$requested_url .= "-optimized";
			}
			

			$file_extension = PegasaasUtils::get_file_extension($requested_url);

			
			if (strstr($file_extension, "-optimized")) {
				$ngnix_file_mapping = true;
				
				$file_extension = str_replace("-optimized", "", $file_extension);
				$requested_url_ngnix_original = $requested_url;
				$requested_url = str_replace(".".$file_extension."-optimized", ".".$file_extension, $requested_url);
				
			} else if (strstr($file_extension, "optimized-")) {
				$ngnix_file_mapping = true;
				
				$file_extension = str_replace("optimized-", "", $file_extension);
				$requested_url_ngnix_original = $requested_url;
				$requested_url = str_replace(".optimized-".$file_extension, ".".$file_extension, $requested_url);				
			
			} else {
				$ngnix_file_mapping = false;
			}
		
			
	
			
			if (strstr($_SERVER['REQUEST_URI'], "/!/external-css,") !== false) {
				ob_end_clean();
				
		  		print $this->fetch_external_css();
				exit();									
				
			} else if ($file_extension == "css" || $file_extension == "js" ) {
				
				$headers = getallheaders();
				// if this is a request via the Pegasaas Fetch system then this truly is a file not found
				if (isset($_GET['test_404']) && $_GET['test_404'] == 1 || isset($headers["X-Pegasaas-Fetch-Request"])) {
					return;
				}
				
				// build path
				$href = $requested_url;
				
				
				$cache_folder = $this->utils->get_home_location().PEGASAAS_CACHE_FOLDER;
				//print "cache folder: $cache_folder<br>";
				//exit;
				
				// return a typical 404 if the file is not within the cache folder
				if (!strstr($href, $cache_folder)) {
					return;
				}
				$sanitized_href = str_replace($cache_folder, "", $href);
	
				$resource_built = $this->assert_local_minified_resource($this->get_home_url().$sanitized_href);

				if (!$resource_built) { 
					header("HTTP/1.1 404 Not Found", true);
					exit; // send 404 response
				} else {
					$file =  PEGASAAS_CACHE_FOLDER_PATH."{$sanitized_href}";
					
					sleep(1);
					if (@file_exists($file)) {
						header("Location: {$requested_url}");
					}					
				}
			

				
			} else if ($file_extension == "ttf" || $file_extension == "eot"  || $file_extension == "woff"  || $file_extension == "woff2"   || $file_extension == "svg" ) {
				
				$headers = getallheaders();
				// if this is a request via the Pegasaas Fetch system then this truly is a file not found
				if (isset($_GET['test_404']) && $_GET['test_404'] == 1 || isset($headers["X-Pegasaas-Fetch-Request"])) {
					return;
				}
				
				// build path
				$href = $requested_url;

				
				$cache_folder = $this->utils->get_home_location().PEGASAAS_CACHE_FOLDER;

				// return a typical 404 if the file is not within the cache folder
				if (!strstr($href, $cache_folder)) {
					return;
				}
				
				$sanitized_href = str_replace($cache_folder, "", $href);
				
				$resource_built = $this->assert_local_cache_resource($this->get_home_url().$sanitized_href);
				
				if (!$resource_built) { 
					//print "resource not built: $sanitized_href";
					
					return; // send 404 response
				} else {
				
				
					$file =  PEGASAAS_CACHE_FOLDER_PATH."{$sanitized_href}";
					
					sleep(1);
					if (@file_exists($file)) {
						header("Location: {$requested_url}");
					}					
				}
			

				
								
			} else if ($file_extension == "png" || $file_extension == "jpg" || $file_extension == "jpeg" || $file_extension == "gif" || $file_extension == "ico") {
			$debug = false;
				$headers = getallheaders();
				
				
			
				
				
				// if this is a request via the Pegasaas Fetch system then this truly is a file not found
				if (isset($_GET['test_404']) && $_GET['test_404'] == 1 || isset($headers["X-Pegasaas-Fetch-Request"])) {
					return;
				}
				
				$basic_image_optimization = PegasaasAccelerator::$settings['settings']['basic_image_optimization'];
				if ($basic_image_optimization) {

					$optimizations = get_option('pegasaas_image_optimizations', array());
					$this_month = date("Y-m");
					$this_months_optimizations = $optimizations["$this_month"];
					
					if ($basic_image_optimization['status'] == 1 && (count($this_months_optimizations) < $basic_image_optimization['images_per_month'] || $basic_image_optimization['images_per_month'] == 9999) ) {
						$optimizations_available = true;
						$max_image_size = $basic_image_optimization['max_image_size'];
						
					}
				} else {

					
					$optimizations_available = true;
					$max_image_size = PegasaasAccelerator::$settings['settings']['image_optimization']['max_image_size'];

				}				
			
				$debug = false;
				// build path
				$href = $requested_url;
				$href = urldecode($href); // needed to translate non-english characters to their equivilant, in order to find the original file name
				
				if ($debug) { echo "Requested URL: {$href}<br>"; }
				//$content_path = str_replace($this->get_home_url(), "", content_url());
				$cache_folder = $this->utils->get_home_location().PEGASAAS_CACHE_FOLDER;
				if ($debug) { echo "Content Path: {$content_path}<br>"; }
				if ($debug) { echo "Cache Folder: {$cache_folder}<br>"; }
				if ($debug) { echo "PEGASAAS_CACHE_FOLDER: ".PEGASAAS_CACHE_FOLDER."<br>"; }
				if ($debug) { echo "PEGASAAS_CACHE_FOLDER_PATH: ".PEGASAAS_CACHE_FOLDER_PATH."<br>"; }
				if ($debug) { echo "PEGASAAS_CACHE_FOLDER_URL: ".PEGASAAS_CACHE_FOLDER_URL."<br>"; }
				if ($debug) { echo "home_path: ".$this->get_home_path()."<br>"; }
				if ($debug) { echo "home_location: ".$this->utils->get_home_location()."<br>"; }

				$sanitized_href = str_replace($cache_folder, "", $href);
				
				
			    if ($debug) { echo "Sanitized HREF: {$sanitized_href}<br>"; }
				
				$home_path = $this->get_home_path();
				$root_path = substr($home_path, 0, strrpos($home_path, $this->utils->get_home_location()));
				if ($debug) { echo "home location strrpos: ".(strrpos($home_path, $this->utils->get_home_location()))."<br>"; }
				if ($debug) { echo "root path: ".$root_path."<br>"; }

				//$original_filename 	= rtrim($root_path ,"/").$sanitized_href;
				$original_filename 	= rtrim($this->get_home_path() ,"/").$sanitized_href;
				
				$file_exists = file_exists($original_filename);
				if ($file_exists) {
					$original_size 	= filesize($original_filename);
				} else {
					$original_size = 0;
				}
				
				

				if ($debug) { echo "Original Filename: {$original_filename}<br>"; }
				if ($debug) { echo "File Exists? : ".($file_exists ? "Yes" : "No")."<br>";}
				
							//	exit;

				
				// attempt to strip out user folders (/~username/) , if the file does not exist
				if (!$file_exists && strstr($original_filename, "/~")) {
					$original_filename = preg_replace('/\/~(.*?)\//', '/', $original_filename);

					$file_exists = file_exists($original_filename);
				}				
				
				
				// test for original file name stripping out width/height/args
				$cleaned_original_filename = $this->strip_image_dimensions_from_filename($original_filename);
				$cleaned_original_filename = str_replace("%20", " ", $cleaned_original_filename);
				
				if ($cleaned_original_filename != $original_filename) {
			
					$did_file_already_exist = $file_exists;
				//	print "did file already exist: $did_file_already_exist <br>";
				//	print "hmm6: $cleaned_original_filename <br>";
								
					$file_exists = file_exists($cleaned_original_filename);

					
					
					if ($file_exists) {
						$original_size 	= filesize($cleaned_original_filename);
					}	

					// we should just throw a 404, because this indicates that the file has width height attributes
					// but is in the same folder as the original					
					if (!$did_file_already_exist && $file_exists) {
					//	print "file didn't already exist, but the clearned original filename does exist so lets redirect to it<br>";
					//	exit;
						header("Location: ".$this->strip_image_dimensions_from_filename($sanitized_href));
						return;
						//return;
					}
				}	
			//	print "xxx";
			//	exit;
				if (!$file_exists) {
					if ($debug) { echo "File Not Found Locally"; exit; }
					return;
				} else {
					if ($debug) {
						echo "File Found<br>";
						exit;
					}
					
				}	
				
				if ($max_image_size <= $original_size) {
					$optimizations_available = false;
					$exceeds_maximum_image_size = true;
				}
				
				
				
				if ($ngnix_file_mapping) {
					
					
				}		
				
				// just store locally 
				if ($optimizations_available) {
					$resource_built = $this->assert_local_optimized_image($this->get_home_url().$sanitized_href);
//print "hmm4";exit;
					$this_month = date("Y-m");
					
					$original_filename 	= rtrim($this->get_home_path() ,"/").$sanitized_href;
					if (false && $ngnix_file_mapping) {
						$optimized_filename = rtrim($this->get_home_path() ,"/").$requested_url_ngnix_original;
						$optimized_filename_not_renamed = rtrim($this->get_home_path() ,"/").$requested_url;
						rename($optimized_filename_not_renamed, $optimized_filename); 
						
					} else {
						$optimized_filename = rtrim($this->get_home_path() ,"/").$requested_url;
					}
					
					
					$original_size 	= filesize($original_filename);
					$optimized_size = filesize($optimized_filename);
					
					// record image optimization
					$this->utils->semaphore("pegasaas_image_optimization");
					$optimizations = get_option('pegasaas_image_optimizations', array());
					$optimizations["$this_month"][] = array("file" => $sanitized_href, "original_size" => $original_size, "optimized_size" => $optimized_size); 
					update_option('pegasaas_image_optimizations', $optimizations);
					$this->utils->release_semaphore("pegasaas_image_optimization");
					
					// record cached image
					$this->utils->semaphore("pegasas_image_cache");
					$cached_images = get_option('pegasaas_image_cache', array());
					$cached_images["{$sanitized_href}"] = array("optimized" => true, "when_cached" => time(), "original_size" => $original_size, "optimized_size" => $optimized_size);
					update_option("pegasaas_image_cache", $cached_images);
					$this->utils->release_semaphore("pegasas_image_cache");
					
					
					
					
				} else {
				
					$resource_built = $this->assert_local_optimized_image($this->get_home_url().$sanitized_href, false, $exceeds_maximum_image_size);
					
					if (false && $ngnix_file_mapping) {
						$stored_filename = rtrim($this->get_home_path() ,"/").$requested_url_ngnix_original;
						$stored_filename_not_renamed = rtrim($this->get_home_path() ,"/").$requested_url;
						rename($stored_filename_not_renamed, $stored_filename); 
						
					} 
					
					// record cached image
					$this->utils->semaphore("pegasas_image_cache");
					$cached_images = get_option('pegasaas_image_cache', array());
					$cached_images["{$sanitized_href}"] = array("optimized" => false, "when_cached" => time(), "original_size" => $original_size, "optimized_size" => $optimized_size);
					update_option("pegasaas_image_cache", $cached_images);
					$this->utils->release_semaphore("pegasas_image_cache");				
					
				}
				
				if ($this->cache->cloudflare_exists()) {
					$this->cache->purge_cloudflare($requested_url);
				}
					
			

				if (!$resource_built) { 
					header("Location: ".$this->get_home_url().$sanitized_href);
					
					
					return; // send 404 response
				} else {
					if (false && $ngnix_file_mapping) {
						$sanitized_href = str_replace(".{$file_extension}", ".{$file_extension}-optimized", $sanitized_href);
					}
					$file =  PEGASAAS_CACHE_FOLDER_PATH."{$sanitized_href}";
					sleep(1);
					if (@file_exists($file)) {
						
						if (false && $ngnix_file_mapping) {
							header("Location: {$requested_url_ngnix_original}");
						} else {
						
							header("Location: {$requested_url}");
						}
					
						
					}					
				}
			

				
				
			} 
	
		}
	}

	function strip_image_dimensions_from_filename($filename) {
	
				if (strstr($filename, "---")) { 

					$pattern = '/\-\-\-([\d]*?)x([\d]*?)([^\d])/';
					$matches = array();
					preg_match($pattern, $filename, $matches);
					$width = $matches[1];
					$height = $matches[2];
					$last_char = $matches[3];
			
					if ($width != "" && $height != "") {

					
						$filename = str_replace($matches[0], $matches[3], $filename);
						$have_dimensions = true;

						$pattern = '/\-\-\-(.*?)\-\-(.*?)\./'; 

						//print $source_file;
						$matches = array();
						preg_match($pattern, $filename, $matches);
						$command = $matches[1];
						$command_arg = $matches[2];

						if ($command != "" && $command_arg != "") {
							$filename = str_replace($matches[0], ".", $filename);

						}
					}

				}	
		
		return $filename;
	}
	
	function assert_local_optimized_image($requested_url, $optimize = true, $exceeds_maximum_image_size = false) {
		global $pegasaas;
		$debug = false || (isset($_GET['api-key']) && isset($_GET['pegasaas-debug']) && $_GET['pegasaas-debug'] == "local-optimized" && PegasaasAccelerator::$settings['api_key'] == $_GET['api-key']);
		if ($debug) { echo "Requested URL: {$requested_url}<br>";  }
		$requested_url = $this->utils->strip_query_string($requested_url);
		
		// build path
		$href = $requested_url;
		$source_path = str_replace($this->get_home_url(), "", $href);
		$source_path = str_replace("%20", " ", $source_path);

		$stripped_filename   = $this->strip_image_dimensions_from_filename($requested_url);
		$stripped_filename = str_replace("%20", " ", $stripped_filename);
		
		$optimization_server = "https://".PegasaasAccelerator::$settings['installation_id'].".".PEGASAAS_ACCELERATOR_CACHE_SERVER;
		//$optimized_url 		 = $optimization_server.rtrim($this->utils->get_wp_location(), '/').$source_path;
		$url_parts = parse_url($this->get_home_url());
		//unset($url_parts['port']);
		
		
		$optimized_url 		 = $optimization_server.rtrim(@$url_parts['path'], '/').$source_path;
		
	
		if ($debug) { echo "Requesting: {$optimized_url}<br>"; }

		// get the original source file
		if ($optimize) {
			$file_contents = $this->utils->get_file($optimized_url, $status_code);
			$asset_status = $status_code;
		} else {
			$file_contents = $this->utils->get_file($stripped_filename, $status_code);
			if ($exceeds_maximum_image_size) {
				$asset_status = 310;
			} else {
				$asset_status = 311;
			}
		}
		$original_file = trailingslashit($this->get_home_path()).ltrim($this->strip_image_dimensions_from_filename($source_path), '/');
		if ($debug) { echo "Original File: ".$original_file."<br>";  }
		if ($debug) { echo "Size Of File: ".strlen($file_contents)."<br>";  }
		if ($debug) { echo "Size Of Original: ".filesize($original_file)."<br>";  }
		if ($debug && false) { echo "Contents: $file_contents <br>"; }
		if ($debug) { echo "Status: $asset_status <br>"; }

		$is_larger_than_original = strlen($file_contents) >= filesize($original_file);
	
		if ($debug) { echo "Optimized is larger: {$is_larger_than_original} <br>"; }

		if ($status_code == -1 || $is_larger_than_original) {

			// attempt to fetch local and store that as the cached file instead
			if (file_get_contents(__FILE__)) {
				if ($asset_status == 310 || $asset_status == 311) {
				} else if ($is_larger_than_original) {
					$asset_status = 309; 
				} else {
					$asset_status = 304;
				}
				$remapped_filename = $stripped_filename;
				$remapped_filename = str_replace($this->get_home_url("", "https")."/", $this->get_home_path(), $remapped_filename);
				$remapped_filename = str_replace($this->get_home_url("", "http")."/", $this->get_home_path(), $remapped_filename);
				
				$file_contents = file_get_contents($remapped_filename);

			
			} 
			
			// if we cannot fetch that file, then instead attempt to use one of the placeholder images
			if ($file_contents == "") {
				$asset_status = 404;
			   
				$file_contents = " ";
				$file_extension = PegasaasUtils::get_file_extension($requested_url);
				
				$file_extension = str_replace("-optimized", "", $file_extension);
				$file_extension = str_replace("optimized-", "", $file_extension);
				if ($file_extension == "jpg") {
					$file_contents = file_get_contents(PEGASAAS_ACCELERATOR_DIR."assets/images/placeholders/404.jpg");
				} else if ($file_extension == "png") {
					$file_contents = file_get_contents(PEGASAAS_ACCELERATOR_DIR."assets/images/placeholders/404.png");
				} else if ($file_extension == "gif") {
					$file_contents = file_get_contents(PEGASAAS_ACCELERATOR_DIR."assets/images/placeholders/404.gif");
		
				}
				
			}
			
		}

		
		if ($file_contents != "") {
			
			$file_extension = PegasaasUtils::get_file_extension($requested_url);	
			$file =  PEGASAAS_CACHE_FOLDER_PATH."{$source_path}";
			
			$path = substr($file, 0,strrpos($file, '/'));
			
			if (!is_dir($path)) {
				$this->cache->mkdirtree($path, 0755, true);
			}
			
			$destination_file_name 	=  PEGASAAS_CACHE_FOLDER_PATH."{$source_path}";	
			if ($debug) { echo "Storing to: {$destination_file_name}<br>"; exit; }
			$fp	= @fopen($destination_file_name, "w");
					
		  	if ($fp) { 
				fwrite($fp, $file_contents);
				
				fclose($fp);
				$optimized_path = PEGASAAS_CACHE_FOLDER_URL."/{$source_path}";
				$pegasaas->utils->log_local_asset($requested_url, $optimized_path, $asset_status);

				return true;
			} else {
				return false;
			}
		} else if (false) {
			// no longer store a blank file, because already pre-detect existing file
			
			
			// store a blank file
			$file_extension = PegasaasUtils::get_file_extension($requested_url);
			
			
			$file =  PEGASAAS_CACHE_FOLDER_PATH."{$source_path}";
			
			$path = substr($file, 0,strrpos($file, '/'));
			
			if (!is_dir($path)) {
				$this->cache->mkdirtree($path, 0755, true);
			}
			
			$destination_file_name 	=  PEGASAAS_CACHE_FOLDER_PATH."/{$source_path}";		
			$fp	= @fopen($destination_file_name, "w");
					
		  	if ($fp) { 
				fwrite($fp, $file_contents);
				fclose($fp);
				return false;
			} else {
				return false;
			}		
			
			return false;
		} else {
			return false;
		}
	}

	function assert_local_cache_resource($requested_url) {
		global $pegasaas;
		$requested_url = $this->utils->strip_query_string($requested_url);
	
		// build path
		$href = $requested_url;
		$source_path = str_replace($this->get_home_url(), "", $href);
		
		$file_extension = PegasaasUtils::get_file_extension($requested_url);
		if (strstr($file_extension, "-optimized")) {
			$ngnix_file_mapping = true;
			$file_extension = str_replace("-optimized", "", $file_extension);
			$href = str_replace(".".$file_extension."-optimized", ".".$file_extension, $href);
		} else if (strstr($file_extension, "optimized-")) {
			$ngnix_file_mapping = true;
			$file_extension = str_replace("optimized-", "", $file_extension);
			$href = str_replace(".optimized-".$file_extension, ".".$file_extension, $href);

		} else {
			$ngnix_file_mapping = false;
		}

		// get the original source file
		$file_contents = $this->utils->get_file($href);

		 
		if ($file_contents != "") {
			
			
			
			global $four_oh_four_source;
			// pass along via globa, the original source path,
			// in order to map the relative
			// location of any referenced resources
			$four_oh_four_source = $source_path;		
			
			$file =  PEGASAAS_CACHE_FOLDER_PATH."{$source_path}";
			
			//print "okay? $file\n";
			//return;
			$path = substr($file, 0,strrpos($file, '/'));
			
			if (!is_dir($path)) {
				$this->cache->mkdirtree($path, 0755, true);
			}
			
			$destination_file_name 	=  PEGASAAS_CACHE_FOLDER_PATH."{$source_path}";		
			$fp	= @fopen($destination_file_name, "w");
					
		  	if ($fp) { 
	
				fwrite($fp, $file_contents);
				fclose($fp);
				
				$optimized_path = PEGASAAS_CACHE_FOLDER_URL."{$source_path}";
				$asset_status = 200;
				$pegasaas->utils->log_local_asset($requested_url, $optimized_path, $asset_status);
				
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}	
	
	
	function assert_local_minified_resource($requested_url) {
		global $pegasaas;
		$debug = false;

		$requested_url = $this->utils->strip_query_string($requested_url);
		if ($debug) { echo "Requested URL: {$requested_url}<br>";  }

		// build path
		$href = $requested_url;
		$source_path = str_replace($this->get_home_url(), "", $href);
		
		if ($debug) { echo "Requesting: {$href}<br>"; }

		// get the original source file
		
		$file_contents = $this->utils->get_file($href, $asset_status);
		
		if ($asset_status == 404) { 
		   $file_contents = "/* the original source file ({$requested_url}) was not found */";
		} 
		if ($file_contents == "") {
			$file_contents = "/* empty */";
		}
		
		if ($debug && false) { echo "File Contents: {$file_contents}<br>"; }
		if ($debug) { echo "File Contents size: ".strlen($file_contents)."<br>"; }
		if ($file_contents != "") {
			
			$file_extension = PegasaasUtils::get_file_extension($requested_url);
			
			global $four_oh_four_source;
			// pass along via global, the original source path,
			// in order to map the relative
			// location of any referenced resources
			$four_oh_four_source = $source_path;
			
			if ($file_extension == "css") {
				$file_contents = PegasaasMinify::minify_css($file_contents);
			
			
			} else if ($file_extension == "js") {
				$compression_mode = 1;
				if (strstr($requested_url, ".min.js")) {
					$compression_mode = 2;
				}
				$file_contents = PegasaasMinify::minify_js($file_contents, "", $compression_mode);
				
				
				
				if (PegasaasAccelerator::$settings['settings']['avada']['status'] == 1) {
					$file_contents = str_replace("jQuery(document).ready(", "jQuery(window).load(", $file_contents);
				} 
			}
					
			
			$file =  PEGASAAS_CACHE_FOLDER_PATH."{$source_path}";
			
			  
			$path = substr($file, 0,strrpos($file, '/'));
			
			if (!is_dir($path)) {
				$this->cache->mkdirtree($path, 0755, true);
			}
			
			$destination_file_name 	=  PEGASAAS_CACHE_FOLDER_PATH."{$source_path}";		
			$fp	= @fopen($destination_file_name, "w");
			if ($debug) { echo "Storing to: {$destination_file_name}<br>";}
		
		  	if ($fp) { 
				fwrite($fp, $file_contents);
				fclose($fp);

				$optimized_path = PEGASAAS_CACHE_FOLDER_URL."{$source_path}";
				
				$pegasaas->utils->log_local_asset($requested_url, $optimized_path, $asset_status);
				if ($debug) { 
				  exit;
				}
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
		
	
	

	
	function deactivate() {
		// remove cron variables
		PegasaasCron::clear_cron_events();

		// clear local cache
		$this->cache->clear_pegasaas_file_cache();
		
		// communicate to the api that we are disabling
		$this->disable();
		
		// clear local variables
		$this->clear_pegasaas_variables();
		
		// retain last api key in the event that the user uninstalls so that we can close the subscription
		update_option("pegasaas_deactivated_api_key", PegasaasAccelerator::$settings['api_key']);
		
	}

	
	
	function clear_pegasaas_variables() {
		
		
		// clear all page-level settings 
		$all_pages = $this->utils->get_all_pages_and_posts();
		foreach ($all_pages as $page) {
			$this->utils->delete_object_meta($page->slug, "accelerator_overrides");
		}
		
		$this->utils->clear_all_semaphores();
		$this->clear_all_data();
		$this->cache->clear_remote_cache();
		
		delete_option("pegasaas_settings");
		delete_option("pegasaas_when_db_last_optimzed");
		delete_option("pegasaas_limits_check_time");
		delete_option("pegasaas_cache_map");
	
		delete_option("pegasaas_accelerator_curl_ssl_verify");
		delete_option("pegasaas_accelerator_ignore_ssl_warning");
		delete_option("pegasaas_accelerator_last_curl_error");
		$this->utils->log("delete_option('pegasaas_accelerated_pages') -- PegasaasAccelerator::clear_pegasaas_variables()", "data_structures");
		
		delete_option("pegasaas_accelerated_pages");
		delete_option("pegasaas_prioritized_pages");
		delete_option("pegasaas_accelerator_update_version");
		delete_option("pegasaas_accelerator_when_last_checked_for_update");
		delete_option("pegasaas_image_cache");
		delete_option("pegasaas_image_optimizations");
		delete_option("pegasaas_accelerator_dismiss_upgrade_box");
		delete_option("pegasaas_accelerator_dismiss_review_prompt");
		delete_option("pegasaas_blog_home_page_accelerated");
		delete_option("pegasaas_blog_categories_accelerated");
		delete_option("pegasaas_blog_pagination_accelerated");
		delete_option("pegasaas_woocommerce_product_tags_accelerated");
		delete_option("pegasaas_woocommerce_product_categories_accelerated");

		
		
		delete_option("pegasaas_logging_log_submit_scans");
		delete_option("pegasaas_logging_log_server_info");
		delete_option("pegasaas_logging_log_submit_benchmark_scans");
		delete_option("pegasaas_logging_log_process_pagespeed_scans");
		delete_option("pegasaas_logging_log_process_benchmark_scans");
		delete_option("pegasaas_logging_log_semaphores");
		delete_option("pegasaas_logging_log_html_conditioning");
		delete_option("pegasaas_logging_log_cpcss");
		delete_option("pegasaas_logging_log_image_data");
		delete_option("pegasaas_logging_log_api");
		delete_option("pegasaas_logging_log_pickup_queued_requests");
		delete_option("pegasaas_logging_log_auto_crawl");
		delete_option("pegasaas_logging_log_auto_clear_page_cache");
		delete_option("pegasaas_logging_log_script_execution_benchmarks");
		delete_option("pegasaas_logging_log_caching");
		delete_option("pegasaas_logging_log_database");
		delete_option("pegasaas_logging_log_file_permissions");
		delete_option("pegasaas_logging_log_cloudflare");
		delete_option("pegasaas_logging_log_varnish");
		delete_option("pegasaas_logging_log_pegasaas_only");
		delete_option("pegasaas_logging_log_E_DEPRECATED");
		delete_option("pegasaas_logging_log_E_WARNING");
		delete_option("pegasaas_logging_log_E_NOTICE");

		delete_option("pegasaas_cloudflare_api_key");
		delete_option("pegasaas_cloudflare_account_email");
		delete_option("pegasaas_cloudflare_authorization_type");
		delete_option("pegasaas_cloudflare_zone_id");
		delete_option("pegasaas_cloudflare_last_exists_query_result");
		delete_option("pegasaas_cloudflare_last_exists_query_time");
		
		delete_option("pegasaas_when_http_auth_last_checked");
		delete_option("pegasaas_http_auth_status");
		delete_option("pegasaas_server_ip_via_remote_endpoint");
		
		delete_option("pegasaas_when_ips_last_checked");
		delete_option("pegasaas_api_ips");
		delete_option("pegasaas_post_types_pages");
		delete_option("pegasaas_web_perf_score_data");
		delete_option("pegasaas_web_perf_benchmark_score_data");
		
 		delete_option("pegasaas_dynamic_urls_utm_source");
		delete_option("pegasaas_dynamic_urls_utm_medium");
		delete_option("pegasaas_dynamic_urls_utm_campaign");
		delete_option("pegasaas_dynamic_urls_utm_term");
		delete_option("pegasaas_dynamic_urls_utm_content");
		delete_option("pegasaas_dynamic_urls_gclid");
		delete_option("pegasaas_dynamic_urls_keyword");
		delete_option("pegasaas_dynamic_urls_additional_args");
	
		
		delete_option("pegasaas_exclude_urls_urls");
		
		delete_option("pegasaas_defer_render_blocking_js_exclude_scripts");
	
		delete_option("pegasaas_defer_render_blocking_css_exclude_stylesheets");
		delete_option("pegasaas_defer_unused_css_exclude_stylesheets");
		delete_option("pegasaas_dns_prefetch_additional_domains");
		delete_option("pegasaas_lazy_load_exclude_urls");
		delete_option("pegasaas_lazy_load_scripts_additional_scripts");
		delete_option("pegasaas_lazy_load_scripts_custom_scripts");
		delete_option("pegasaas_lazy_load_images_exclude_images");
		delete_option("pegasaas_lazy_load_background_images_exclude_images");
		
		delete_option("pegasaas_image_optimization_exclude_images");
		delete_option("pegasaas_image_optimization_exclude_images_from_auto_sizing");
		
		delete_option("pegasaas_preload_user_files_resources");
		delete_option("pegasaas_essential_css_css");

		delete_option("pegasaas_wp_rest_nonce");

	
		$this->db->delete("pegasaas_performance_scan", array());
		$this->db->delete("pegasaas_api_request", array());
		$this->db->delete("pegasaas_page_config", array());
		$this->db->delete("pegasaas_static_asset", array());
		$this->db->delete("pegasaas_page_cache", array()); 

		$this->data_storage->unset_object("all_pages_and_posts");
		$this->data_storage->unset_object("local_file_stats_html");
		$this->data_storage->unset_object("local_file_stats_combined.css");
		$this->data_storage->unset_object("local_file_stats_deferred-js.js");
		
	}
	
	
	

	
	function get_current_version() {
		if (!function_exists("get_plugin_data")) {
			include("includes/plugin.php");
		}		
		$plugin = get_plugin_data(PEGASAAS_ACCELERATOR_DIR."pegasaas-accelerator-wp.php");
		return $plugin['Version'];
	}
	
	function is_development_mode_request() {
		return isset($_GET['inspect-web-perf']);
	}
	
	function in_development_mode() {
		$development_mode_expiry = get_option("pegasaas_development_mode", 0); // will be a time-date stamp, or -1
		
		if (PegasaasAccelerator::$settings['status'] == 1) {
			// if -1, then this is the "indefinitely in development mode" setting
			if ($development_mode_expiry == -1) {
				return true;

			} else {
				// return whether if the time is currently earlier than the expiry time-date stamp
				return time() < $development_mode_expiry;
			}
		} else {
			return false;
		}
		
	}
	
	function in_live_mode() {
		$development_mode_expiry = get_option("pegasaas_development_mode", 0); // will be a time-date stamp, or -1
		
		if (PegasaasAccelerator::$settings['status'] == 1) {
			// if -1, then this is the "indefinitely in development mode" setting
			if ($development_mode_expiry == -1) {
				return false;

			} else {
				// return whether if the time is currently earlier than the expiry time-date stamp
				return time() > $development_mode_expiry;
			}
		} else {
			return false;
		}
		
	}
	
	function in_diagnostic_mode() {

		if (PegasaasAccelerator::$settings['status'] == 2 || PegasaasAccelerator::$settings['status'] == 0) {
			return true;
			
		} 
		
		return false;
		
	}	

	function shutdown() { 
		
		if (!is_admin() && $this->output_buffer_mode == "functionless") {

			if ($this->disabled) {
				
				return;
			}
			
			
			$handlers = ob_list_handlers();
			if ($_SERVER['HTTP_X_PEGASAAS_DEBUG'] == "show-handlers") {
				ob_end_clean();
				var_dump($handlers);
				exit;
			}
			$buffer = "";  
			
			global $xyzinfo;
		
			$output_levels = ob_get_level(); 
			global $output_buffer_level;
			
			$order = 0;
			
			//for ($i = 0; $i < $output_levels; $i++) { 
			for ($i = $output_levels-1; $i >= 0; $i--) {
				if ($handlers["$i"] != "default output handler" && $handlers["$i"] != "ob_gzhandler") {
				  if ($buffer == "") {
				     $buffer = ob_get_clean();
				  }
				}
						
				
				$output_buffer_level = $i;
				if ($_GET['pegasaas-test'] == "output-buffer-handlers") {
					$buffer .= "<!-- handler: ".$handlers[$i]." -->y";
				}
 
				
				if ($handlers[$i] === 'N2WordpressAssetInjector::output_callback') {
					$buffer = N2WordpressAssetInjector::platformRenderEnd($buffer);
				  
				} else if ($handlers[$i] === 'Nextend\WordPress\OutputBuffer::outputCallback') {
					$buffer = Nextend\WordPress\OutputBuffer::prepareOutput($buffer);
					
				} else if ($handlers[$i] == "C_Photocrati_Resource_Manager::output_buffer_handler") {
					 C_Photocrati_Resource_Manager::$instance->buffer = $buffer;
					 $buffer = C_Photocrati_Resource_Manager::$instance->output_buffer();
					
				} else if ($handlers[$i] == "Optml_Manager::replace_content") {
					
					$buffer = Optml_Main::instance()->manager->replace_content($buffer);
				
				// for the "really-simple-ssl" plugin
				} else if ($handlers[$i] == "rsssl_mixed_content_fixer::filter_buffer") {
					
					$buffer = RSSSL()->rsssl_mixed_content_fixer->filter_buffer($buffer);
					
				} else if ($handlers[$i] == "hefo_callback") {
					$buffer = hefo_callback($buffer);
					
				// for the "schema-app-structured-data-for-schemaorg" plugin
				} else if ($handlers[$i] == "SchemaFront::RemoveMicrodata") {
					 $buffer = SchemaFront::RemoveMicrodata($buffer);		
				
				// for wp.com hosted sites	peg
				} else if ($handlers[$i] == "wpcom_better_footer_links_buffer") {
					
					$buffer = wpcom_better_footer_links_buffer($buffer);

					
				// for WPDaddy Builder Plugin	
				} else if ($handlers[$i] == "WPDaddy\Builder\Buffer::ob_finish") {

					
					WPDaddy\Builder\Buffer::instance()->wp_head();
					$buffer_temp = WPDaddy\Builder\Buffer::instance()->ob_finish($buffer);
					
					// buffer can be false, if there is no header, so we should always check and if it is false
					// then just use the existing buffer
					if ($buffer_temp) {
						$buffer = $buffer_temp;
					}
					
				} else if ($handlers[$i] == "BunnyCDNFilter::rewrite") {
					$buffer = PegasaasPluginCompatibility::bunny_cdn_ob_finish($buffer);
						
				} else if ($handlers[$i] == "\WebPExpress\AlterHtmlInit::alterHtml") {
					$buffer = \WebPExpress\AlterHtmlInit::alterHtml($buffer);
					
				} else if ($handlers[$i] == "ampforwp_the_content_filter_full") {
					$buffer = ampforwp_the_content_filter_full($buffer);
					
				} else if ($handlers[$i] == "TRP_Translation_Render::translate_page") {
					$buffer =  TRP_Translate_Press::get_trp_instance()->get_component('translation_render')->translate_page($buffer);

				} else if ($handlers[$i] == "TRP_Translation_Render::render_default_language") {
					$buffer =  TRP_Translate_Press::get_trp_instance()->get_component('translation_render')->render_default_language($buffer);

				} else if ($handlers[$i] == "TRP_Translation_Render::render_default_language") {
					$buffer =  TRP_Translate_Press::get_trp_instance()->get_component('translation_render')->render_default_language($buffer);

				// Hide MY WP - Ghost (hide-my-wp)
				} else if ($handlers[$i] == "HMW_Models_Rewrite::getBuffer") {
					$buffer =  HMW_Classes_ObjController::getClass( 'HMW_Models_Rewrite' )->getBuffer($buffer);
	
					
				} else if ($handlers[$i] == "Cookie_Law_Info_Script_Blocker::init") {
					$cookie_law_info_script_blocker = new Cookie_Law_Info_Script_Blocker();
					$buffer =  $cookie_law_info_script_blocker->replace_scripts($buffer);

				} else if ($handlers[$i] == "Cookie_Law_Info_Script_Blocker_Frontend::wt_end_custom_buffer") {
					$cookie_law_info_script_blocker_frontend = new Cookie_Law_Info_Script_Blocker_Frontend();

					$buffer =  $cookie_law_info_script_blocker_frontend->wt_end_custom_buffer($buffer);
				
				} else if ($handlers[$i] == "AMP_Theme_Support::finish_output_buffering") {
					$buffer = AMP_Theme_Support::finish_output_buffering($buffer);
					
				} else {

					$buffer_temp = ob_get_clean();
					$buffer_temp = trim($buffer_temp);
					$buffer = $buffer_temp.$buffer; 
				}
				
				$order++;
	
			}
			
			
			

			
			$page_settings 		  = PegasaasUtils::get_object_meta(PegasaasUtils::get_object_id(), "accelerator_overrides");
			$explicitly_show_accelerated_page = false;
			if (isset($_GET['accelerate']) && $_GET['accelerate'] == "on") {
				$explicitly_show_accelerated_page = true;
			} else if (isset($page_settings['staging_mode_page_is_live']) && $page_settings['staging_mode_page_is_live'] == 1) {
				
				$explicitly_show_accelerated_page = true;
			}

			
			$this->output_buffer = $buffer;
			
			$headers = getallheaders();
			$test_submission = false;
			
			foreach ($headers as $n => $v) {
				if (strtolower($n) == "x-pegasaas-optimization-test") {
					$test_submission = true;
					if (!headers_sent()) {
						header("HTTP/1.0 201 No Pages Accelerated"); // required for Kinsta cache
						header("Cache-Control: private, max-age=0, no-cache"); // required for Kinsta Cache
					}
				}
			}
			
			
			if ($this->is_on_excluded_page()) {
				
				// cannot output headers as data has already been started by wp-includes/formatting.php
				if (!headers_sent()) {
				
					header("X-Pegasaas-Message: No Acceleration Performed");
					header("X-Pegasaas-Reason: On Excluded Page");
				}
				
				
				print $buffer;
				
			} else if ((PegasaasAccelerator::$settings['status'] == 0 || PegasaasAccelerator::$settings['status'] == 2) && $_GET['accelerate'] != "on" && !$test_submission ) {
				if (!headers_sent()) {
					header("X-Pegasaas-Message: No Acceleration Performed");
					header("X-Pegasaas-Reason: In Diagnostic Mode");
				}
				print $buffer;
			
			
			} else {
			
				ob_start(array($this,'fatal_error_handler'));

				try {
				
					if (sizeof($handlers) > 1) {
						ob_start();
					}

					if (PegasaasAccelerator::$settings['settings']['gzip_compression']['status'] == '1') {
						$handlers = ob_list_handlers();
						//var_dump($handlers);
						if (!in_array("ob_gzhandler", $handlers)) {
							ob_start("ob_gzhandler"); 

						}	
					}					
					print $this->optimize->sanitize_output($buffer, $test_submission);

				// try/catch of (Error $error) only supported in PHP 7+
				// The fallback method is to wrap the sanitize output with a "fatal_error_handler" output buffer trap function
				} catch (Error $error) {
					
					$this->pegasaas_error_handler(E_ERROR, $error->getMessage(), $error->getFile(), $error->getLine());
					
					print $buffer;
					if (!defined('WP_CLI')) {
						print "<!-- ".$error->getMessage()." -->\n";
							print "<!-- ".$error->getFile()." -->\n";
							print "<!-- ".$error->getLine()." -->\n";
						print "<!-- pegasaas://accelerator -- fatal error detected, sanitize output aborted 2 -->";
					}
				}
			}
			
		} 
		
		//PegasaasUtils::log_write();
	}
	
	function fatal_error_handler($buffer) {
		$error = error_get_last();
    	if (isset($error['type']) && $error['type'] == 1){
			$message = 'E_ERROR: '.$error['message'].' in '.$error['file'].' on line #'.$error['line']."--".print_r($error, true);
			$this->utils->log($message, "error");
			$buffer = $this->output_buffer;
			if (!defined('WP_CLI')) {
				$buffer .= "<!-- pegasaas://accelerator -- fatal error detected, sanitize output aborted -->";
			}
			return $buffer;
		} else {
			return $buffer;
		}
	}
	
	function set_post(){     
		global $post;
		global $wp_query;

		if (!isset($post) || $post == NULL) {
			// save the current post to this object
			$this->post = $wp_query->get_queried_object();
			
			if ($this->post->ID == '0') {
				if (get_option("show_on_front") == "posts") {
				} else if (get_option("show_on_front") == "page") {
					$this->post = get_post(get_option("page_on_front"));
				}					
			}
			if ($this->post != NULL) {
				$slug = "/";
				if ($this->post->post_name != "/") {
					$permalink = get_permalink($this->post->ID);
					$permalink = str_replace($this->get_home_url(), "", $permalink);
					$slug = $permalink;
				} 
				$this->post->slug = $slug;
			}
		}		
	}
	
	function admin_init() {
		parse_str($_SERVER['QUERY_STRING'], $params);
		if (array_key_exists("page", $params) && ($params["page"] == "rlrsssl_really_simple_ssl")) {
            $this->resolve_conflict("rlrsssl_really_simple_ssl");
        }
		
	}
	
	static function is_amp_endpoint() {
		
		if (function_exists("is_amp_endpoint") && is_amp_endpoint()) {
		  return true;	
		} else if (strstr($_SERVER['REQUEST_URI'], "/amp/")) {
			return true;
		}
		return false;
		
	}
	
	function init() {

		global $pagenow;
		global $post;

		PegasaasUtils::log("Begin Init", "script_execution_benchmarks");

		if (isset($_GET['pegasasas-debug']) && $_GET['pegasaas-debug'] == "console") {
			$this->utils->console_log('Init 1');
		}
		// log request data
		$this->utils->log_request_data();
		
		if ('plugins.php' === $pagenow) {
			add_action('admin_footer', array($this, 'pegasaas_deactivation_exit_survey'));
		}		
		
		load_plugin_textdomain( 'pegasaas-accelerator', PEGASAAS_ACCELERATOR_DIR . 'lang', 
							   basename( PEGASAAS_ACCELERATOR_DIR ) . '/lang' );
		
		

		// set up cron for pickup
		PegasaasCron::register_cron_events();
		
		$this->db->assert_structure();
				
		add_action( 'admin_init', array( $this->interface, 'setup_page_post_hooks' ) );
	
		// we do not want to execute anything else if we are doing a cron job
		if ( defined( 'DOING_CRON' ) ) {
    		return;
		}
		if (isset($_GET['pegasaas-debug']) && $_GET['pegasaas-debug'] == "console") {
			$this->utils->console_log('Init 2');
		}		
		if (is_admin()) {
			
			if ( (isset($_POST['output']) && $_POST['output'] == "raw") || (isset($_GET['output']) && $_GET['output'] == "raw") ) {
				
				
				ob_start();
			}
			if (isset($_POST['c']) && $_POST['c'] == 'view-diagnostics') { 
				setcookie("view_diagnostics", true);
				$this->view_diagnostics = true;

			} else if(isset($_COOKIE['view_diagnostics']) ){
				$this->view_diagnostics = $_COOKIE['view_diagnostics'];
			}

			add_action('admin_enqueue_scripts', array($this->interface, 'admin_enqueue'));
			add_action('admin_footer', 			array($this->interface, 'pegasaas_admin_footer'));
			
		} else {
			add_action('wp_enqueue_scripts', 	array($this->interface, 'frontend_enqueue'));
		}
		
	

		// set up admin menus
		add_action( 'admin_menu', 				array($this->interface, 'update_administrative_menu'));
		
	
			add_action( 'admin_bar_menu', 			array($this, 'admin_post_commands'), 99);
		if ($this->is_trial() && $this->trial_days_remaining() == 0) {
		} else if (!PegasaasAccelerator::is_amp_endpoint()) {
			add_action( 'admin_bar_menu', 			array($this->interface, 'add_toolbar_items'), 100);
		}
	

		// add links under plugin name on plugin page
		add_filter('plugin_action_links', 					array($this->interface,'pegasaas_plugin_action_links'), 10, 2);
		add_filter('plugin_row_meta', 						array($this->interface,'pegasaas_plugin_row_meta'), 10, 2);

		// deactivation exit survey
		add_action( 'wp_ajax_pegasaas_send_exit_survey', 	array($this, 'pegasaas_send_exit_survey'));
		 
		// core enable/disable/feature ajax calls
		add_action( 'wp_ajax_pegasaas_api_key_check',  					array($this->api, 'pegasaas_api_key_check') );
		add_action( 'wp_ajax_pegasaas_enable_feature',  				array($this, 'pegasaas_enable_feature') );
		add_action( 'wp_ajax_nopriv_pegasaas_get_settings',  				array($this->api, 'pegasaas_get_settings') );
		add_action( 'wp_ajax_nopriv_pegasaas_sync_settings',  			array($this->api, 'pegasaas_sync_settings') );
		add_action( 'wp_ajax_pegasaas_change_system_mode',  					array($this, 'pegasaas_change_system_mode') );

		add_action( 'wp_ajax_pegasaas_disable_accelerator_for_page',  	array($this, 'pegasaas_disable_accelerator_for_page') );
		add_action( 'wp_ajax_pegasaas_enable_accelerator_for_page',  	array($this, 'pegasaas_enable_accelerator_for_page') );

		add_action( 'wp_ajax_pegasaas_disable_prioritization_for_page', 	array($this, 'pegasaas_disable_prioritization_for_page') );
		add_action( 'wp_ajax_pegasaas_enable_prioritization_for_page', 		array($this, 'pegasaas_enable_prioritization_for_page') );
		
		add_action( 'wp_ajax_pegasaas_disable_staging_for_page',  		array($this, 'pegasaas_disable_staging_for_page') );
		add_action( 'wp_ajax_pegasaas_enable_staging_for_page',  		array($this, 'pegasaas_enable_staging_for_page') );
		
		add_action( 'wp_ajax_nopriv_pegasaas_fetch_accelerated_pages_list',  	 array($this, 'pegasaas_fetch_accelerated_pages_list') );
		add_action( 'wp_ajax_pegasaas_get_ob_environment',  	 		array($this->api, 'pegasaas_get_ob_environment') );

		
		// caching 
		add_action('wp_ajax_pegasaas_clear_page_cache',  					array($this->cache, 'pegasaas_clear_page_cache') );
		add_action('wp_ajax_pegasaas_build_page_cache',  					array($this->cache, 'pegasaas_build_page_cache') );
		add_action('wp_ajax_pegasaas_clear_js_cache',  						array($this->cache, 'pegasaas_clear_js_cache') );
		add_action('wp_ajax_pegasaas_clear_css_cache',  					array($this->cache, 'pegasaas_clear_css_cache') );
		add_action('wp_ajax_nopriv_pegasaas_remote_clear_page_cache', 		array($this->cache, 'pegasaas_remote_clear_page_cache'));
		add_action('wp_ajax_nopriv_pegasaas_remote_clear_cloudflare_cache', 	array($this->cache, 'pegasaas_purge_all_cloudflare_cache'));
		add_action('wp_ajax_pegasaas_reoptimize_all_html_cache', 				array($this->cache, 'pegasaas_reoptimize_all_html_cache'));
		add_action('wp_ajax_pegasaas_purge_all_local_image_cache', 			array($this->cache, 'pegasaas_purge_all_local_image_cache'));
		add_action('wp_ajax_pegasaas_purge_all_local_css_cache', 			array($this->cache, 'pegasaas_purge_all_local_css_cache'));
		add_action('wp_ajax_pegasaas_purge_all_local_js_cache', 			array($this->cache, 'pegasaas_purge_all_local_js_cache'));
		add_action('wp_ajax_pegasaas_clear_queued_cache_resources', 		array($this->cache, 'pegasaas_clear_queued_cache_resources'));
		add_action('wp_ajax_pegasaas_purge_all_html_cache', 				array($this->cache, 'pegasaas_purge_all_html_cache'));
		
		add_action('wp_ajax_pegasaas_backload_all_pages_and_posts', array($this->utils, 'pegasaas_backload_all_pages_and_posts'));
				
		// pure cloudfront
		add_action ('wp_ajax_pegasaas_purge_all_cloudflare_cache', 			array($this->cache, 'pegasaas_purge_all_cloudflare_cache'));

		// purge pegasaas api + cdn
		add_action ('wp_ajax_pegasaas_purge_all_resource_cache', 			array($this->cache, 'pegasaas_purge_all_resource_cache'));
		add_action ('wp_ajax_pegasaas_purge_image_resource_cache', 			array($this->cache, 'pegasaas_purge_image_resource_cache'));
		add_action ('wp_ajax_pegasaas_purge_js_resource_cache', 			array($this->cache, 'pegasaas_purge_js_resource_cache'));
		add_action ('wp_ajax_pegasaas_purge_css_resource_cache', 			array($this->cache, 'pegasaas_purge_css_resource_cache'));	
		add_action ('wp_ajax_pegasaas_purge_indv_files_resource_cache',		array($this->cache, 'pegasaas_purge_indv_files_resource_cache'));
		
		// purge cdn only
		add_action ('wp_ajax_pegasaas_purge_all_cdn_edge_network_only', 			array($this->cache, 'pegasaas_purge_all_cdn_edge_network_only'));
		add_action ('wp_ajax_pegasaas_purge_image_cdn_edge_network_only', 			array($this->cache, 'pegasaas_purge_image_cdn_edge_network_only'));
		add_action ('wp_ajax_pegasaas_purge_js_cdn_edge_network_only', 				array($this->cache, 'pegasaas_purge_js_cdn_edge_network_only'));
		add_action ('wp_ajax_pegasaas_purge_css_cdn_edge_network_only', 			array($this->cache, 'pegasaas_purge_css_cdn_edge_network_only'));	
		add_action ('wp_ajax_pegasaas_purge_indv_files_cdn_edge_network_only',		array($this->cache, 'pegasaas_purge_indv_files_cdn_edge_network_only'));
		
		// local static resource management
		add_action ('wp_ajax_pegasaas_reoptimize_local_static_asset', 		array($this->cache, 'pegasaas_reoptimize_local_static_asset'));
		add_action ('wp_ajax_pegasaas_delete_local_static_asset', 			array($this->cache, 'pegasaas_delete_local_static_asset'));
		add_action ('wp_ajax_nopriv_pegasaas_remotely_delete_local_static_asset', 		array($this->cache, 'pegasaas_remotely_delete_local_static_asset'));
		add_action ('wp_ajax_nopriv_pegasaas_remote_clear_image_stats', 	array($this->cache, 'pegasaas_remote_clear_image_stats'));
		
					
		// gpsi score scans
		add_action( 'wp_ajax_pegasaas_request_pagespeed_score',  			array($this->scanner, 'pegasaas_request_pagespeed_score') );
		add_action( 'wp_ajax_pegasaas_request_pagespeed_benchmark_score',  	array($this->scanner, 'pegasaas_request_pagespeed_benchmark_score') );
		add_action( 'wp_ajax_pegasaas_request_image_data',  				array($this->scanner, 'pegasaas_request_image_data') );
		add_action( 'wp_ajax_pegasaas_prerequest_pagespeed_score',  		array($this->scanner, 'pegasaas_prerequest_pagespeed_score') );
		add_action( 'wp_ajax_pegasaas_cancel_pagespeed_score_request',  	array($this->scanner, 'pegasaas_cancel_pagespeed_score_request') );
		add_action( 'wp_ajax_pegasaas_check_queued_pagespeed_score_requests',  			array($this->scanner, 'pegasaas_check_queued_pagespeed_score_requests') );
		add_action( 'wp_ajax_pegasaas_check_queued_pagespeed_benchmark_score_requests', array($this->scanner, 'pegasaas_check_queued_pagespeed_benchmark_score_requests') );
		add_action( 'wp_ajax_pegasaas_fetch_pagespeed_opportunities_html',  			array($this->scanner, 'pegasaas_fetch_pagespeed_opportunities_html') );
		add_action( 'wp_ajax_nopriv_pegasaas_process_pagespeed_score_request',  			array($this->scanner, 'pegasaas_process_pagespeed_score_request') );
		add_action( 'wp_ajax_nopriv_pegasaas_process_pagespeed_benchmark_score_request',  	array($this->scanner, 'pegasaas_process_pagespeed_benchmark_score_request') );
		
		
		// critical path css
		add_action( 'wp_ajax_nopriv_pegasaas_process_critical_css_request', array($this, 'pegasaas_process_critical_css_request') );
		add_action ('wp_ajax_pegasaas_rebuild_all_critical_css', 			array($this, 'pegasaas_rebuild_all_critical_css'));
		add_action ('wp_ajax_pegasaas_recalculate_cpcss', 					array($this, 'pegasaas_recalculate_cpcss'));
		add_action ('wp_ajax_pegasaas_purge_cpcss', 						array($this, 'pegasaas_purge_cpcss'));
		
		// optimization requests
		add_action( 'wp_ajax_pegasaas_clear_optimization_request',  		array($this, 'pegasaas_clear_optimization_request') );
		add_action( 'wp_ajax_nopriv_pegasaas_clear_optimization_request',  	array($this, 'pegasaas_clear_optimization_request') );
		add_action( 'wp_ajax_pegasaas_check_queued_optimization_requests',  array($this->optimize, 'pegasaas_check_queued_optimization_requests') );
		add_action( 'wp_ajax_nopriv_pegasaas_clear_queued_optimization_requests', array($this, 'pegasaas_clear_queued_optimization_requests'));
		
		// image data request
		add_action( 'wp_ajax_nopriv_pegasaas_process_image_data_request',  array($this, 'pegasaas_process_image_data_request') );

		// image data request
		add_action( 'wp_ajax_nopriv_pegasaas_process_optimization_request',  array($this, 'pegasaas_process_optimization_request') );
		
		// interface requests
		add_action('wp_ajax_pegasaas_is_prepping_done', 			array($this->interface, 'pegasaas_is_prepping_done'));
		add_action('wp_ajax_nopriv_pegasaas_is_prepping_done',  	array($this->interface, 'pegasaas_is_prepping_done')); // allow API to query state of initialization
		add_action('wp_ajax_pegasaas_notify_hung_initialization', 	array($this->interface, 'pegasaas_notify_hung_initialization'));
		add_action('wp_ajax_pegasaas_dismiss_upgrade_box', 			array($this->interface, 'pegasaas_dismiss_upgrade_box'));
		add_action('wp_ajax_pegasaas_dismiss_review_prompt', 		array($this->interface, 'pegasaas_dismiss_review_prompt'));
		add_action('wp_ajax_nopriv_pegasaas_fetch_page_metrics', 	array($this->interface, 'pegasaas_fetch_page_metrics')); // allow API to query the stored data 
		add_action('wp_ajax_pegasaas_fetch_page_metrics', 			array($this->interface, 'pegasaas_fetch_page_metrics'));
		add_action('wp_ajax_nopriv_pegasaas_fetch_site_metrics', 	array($this->interface, 'pegasaas_fetch_site_metrics'));
		add_action('wp_ajax_pegasaas_dashboard_settings_update', 	array($this->interface, 'pegasaas_dashboard_settings_update')); 
		
		add_action('wp_ajax_pegasaas_check_compatibility',  		array($this, 'pegasaas_check_compatibility') );
		add_action('wp_ajax_nopriv_pegasaas_check_compatibility',  	array($this, 'pegasaas_check_compatibility') );

		add_action('wp_ajax_pegasaas_check_server_response_time',  			array($this, 'pegasaas_check_server_response_time') );
		add_action('wp_ajax_nopriv_pegasaas_check_server_response_time',  	array($this, 'pegasaas_check_server_response_time') );
		
		add_action('wp_ajax_pegasaas_check_api_reachable',  		array($this, 'pegasaas_check_api_reachable') );
		add_action('wp_ajax_nopriv_pegasaas_check_api_reachable',  	array($this, 'pegasaas_check_api_reachable') );
		
		add_action('wp_ajax_pegasaas_check_test_optimization',  		array($this, 'pegasaas_check_test_optimization') );
		add_action('wp_ajax_nopriv_pegasaas_check_test_optimization',  	array($this, 'pegasaas_check_test_optimization') );

		add_action('wp_ajax_pegasaas_check_push_fetch_test',  		array($this, 'pegasaas_check_push_fetch_test') );
		add_action('wp_ajax_nopriv_pegasaas_check_push_fetch_test',  	array($this, 'pegasaas_check_push_fetch_test') );

		add_action('wp_ajax_pegasaas_check_webperf_data_fetch_test',  		array($this, 'pegasaas_check_webperf_data_fetch_test') );
		add_action('wp_ajax_nopriv_pegasaas_check_webperf_data_fetch_test',  	array($this, 'pegasaas_check_webperf_data_fetch_test') );

		
		
		add_action('wp_ajax_nopriv_pegasaas_check_ability_to_submit_to_ajax',  	array($this, 'pegasaas_check_ability_to_submit_to_ajax') );

		add_action('wp_ajax_pegasaas_assert_global_cpcss', 			array($this, 'pegasaas_assert_global_cpcss'));
		add_action('wp_ajax_pegasaas_submit_scan_request', 			array($this, 'pegasaas_submit_scan_request'));
		add_action('wp_ajax_pegasaas_submit_benchmark_requests', 	array($this, 'pegasaas_submit_benchmark_requests'));
		add_action('wp_ajax_pegasaas_auto_accelerate_pages', 		array($this, 'pegasaas_auto_accelerate_pages'));
	
		// no need to clear cache on wp-json endpoint -- can invoke long TTFB in admin area due to clearing multi-server-cache
		if (!strstr($_SERVER['REQUEST_URI'], "wp-json/")) { 
			// clear cache upon post update
			add_action('pre_post_update', 								array($this->cache, 'clear_blog_page_cache'));
			add_action('pre_post_update', 								array($this->cache, 'clear_post_types_page_cache'));
			add_action('transition_post_status', 						array($this->cache, 'handle_post_change_state'), 10, 3);
		}
			
		// clear all data when the theme is changed
		add_action('after_switch_theme', 							array($this, 'clear_all_data'));
		

		add_filter( 'post_row_actions', array( $this->interface, 'add_clear_cache_link' ), 10, 2 );
		add_filter( 'page_row_actions', array( $this->interface, 'add_clear_cache_link' ), 10, 2 );
		add_filter( 'category_row_actions', array( $this->interface, 'add_clear_cache_link' ), 15, 2 );
		
		add_filter( 'post_row_actions', array( $this->interface, 'add_build_cache_link' ), 10, 2 );
		add_filter( 'page_row_actions', array( $this->interface, 'add_build_cache_link' ), 10, 2 );
		add_filter( 'category_row_actions', array( $this->interface, 'add_build_cache_link' ), 10, 2 );



		$headers = getallheaders();
		foreach ($headers as $n => $v) {
				if (strtolower($n) == "x-pegasaas-optimization-test") {
					$test_submission = true;
				}
				
			}
		
		if ($this->in_development_mode()) {
			add_filter( 'post_row_actions', array( $this->interface, 'add_staging_mode_links' ), 10, 2 );
			add_filter( 'page_row_actions', array( $this->interface, 'add_staging_mode_links' ), 10, 2 );
			add_filter( 'category_row_actions', array( $this->interface, 'add_staging_mode_links' ), 10, 2 );

		}
		

		PegasaasUtils::log("After Init/add_filters", "script_execution_benchmarks");

		$page_level_settings = PegasaasUtils::get_object_meta($this->utils->get_object_id(), "accelerator_overrides");
		
		add_action( 'admin_menu',  		array( $this->interface, 'add_page_post_meta_boxes'));
		add_action("wp_ajax_pegasaas_save_page_configurations", array( $this->interface, 'pegasaas_save_page_configurations'));
	
		
		/* HANDLE expired wp-nonce values for wp-json  API */
		$send_no_cache_headers = apply_filters('rest_send_nocache_headers', is_user_logged_in());
		if (!$send_no_cache_headers && !is_admin() && $_SERVER['REQUEST_METHOD'] == 'GET') {
    		$nonce = wp_create_nonce('wp_rest');
    		$_SERVER['HTTP_X_WP_NONCE'] = $nonce;
		}
		
		
		/* HANDLE expired wp-nonce value for user-registration login form */
		if (PegasaasUtils::does_plugin_exists_and_active("user-registration")) {
			if (!is_admin() && $_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['user-registration-login-nonce'])) {
				$nonce = wp_create_nonce("user-registration-login");
				
				$_POST['user-registration-login-nonce'] = $nonce;
			}
		}

		if (isset($_GET['pegasaas-debug']) && $_GET['pegasaas-debug'] == "console") {
			$this->utils->console_log('Init 1');
		}

		/* HANDLE expired wp-nonce value for rate-my-post admin-ajax request */
		if (PegasaasUtils::does_plugin_exists_and_active("rate-my-post")) {
			if (is_admin() && defined('DOING_AJAX') && DOING_AJAX && $_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action']) && $_POST['action'] == "process_rating") {
				$nonce = wp_create_nonce("rmp_public_nonce");
				$_POST['nonce'] = $nonce;
			}
		}

		/* HANDLE expired wp-nonce value for contact-form-7 forms */
		if (PegasaasUtils::does_plugin_exists_and_active("contact-form-7")) {
			
			if (!$send_no_cache_headers && !is_admin() && $_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['_wpcf7'])) {
				$nonce = wp_create_nonce("wp_rest");
		
				$_SERVER['HTTP_X_WP_NONCE'] = $nonce;
			} 
		}		


		if (isset($_GET['accelerate']) &&  $_GET['accelerate'] == "on") {
			
			add_filter('show_admin_bar', '__return_false');
		}
		
		if ($this->is_development_mode_request() && current_user_can("edit_posts")) {
			
			add_action('wp_loaded', 			array($this, 'render_developer_inspect_interface'));
			
			
		} else
		
		// do not do anything to this page if this is a wordfence scan
		if (isset( $_GET['wordfence_lh']) && $_GET['wordfence_lh'] != "") { 
		
		} else if (isset($_GET['accelerate']) && $_GET['accelerate'] == "off") {
		
			$this->disabled = true;
			header("X-Pegasaas-Message: Accelerator Disabled");
			header("X-Pegasaas-Reason: Page Acceleration Explicitly Disabled via ?accelerate=off");
			add_action('wp_print_scripts', array($this, "add_analytics_opt_out"));
			
			// this is so that the plugin can evaluate if there are non-typical output buffer handlers in operation
			if (isset($_POST['get_ob_environment']) && $_POST['get_ob_environment'] == "json" && 
			    isset($_POST['api_key']) == PegasaasAccelerator::$settings['api_key']) {
				$handlers = ob_list_handlers();
				print json_encode($handlers);
				exit;
			}
		/*
		} else if (!$page_level_settings['accelerated']) {
		
		
			$this->disabled = true;
			header("X-Pegasaas-Message: Accelerator Disabled");
			header("X-Pegasaas-Reason: Page Acceleration Not Enabled");
		*/
		} else 	if (!is_admin() && isset($_GET['pegasaas_debug']) && $_GET['pegasaas_debug'] == "1") {
			
			$this->utils->log("Calling \$this->optimize->sanitize_output() no buffer", "html_conditioning");
			$this->optimize->sanitize_output();
			
		} else if (!is_admin() && !$this->is_on_excluded_page() && (PegasaasAccelerator::$settings['status'] == 1 || (isset($_GET['accelerate']) && $_GET['accelerate'] != "on") || $test_submission)) {
	

			// If the output buffer mode is the legacy, then handle it via a function call.
			// - this was deprecated because we could not trap errors effectively with it,
			//   nor output testing data using functions that modify output buffer such as var_dump
			if ($this->output_buffer_mode == "") { 
			

				$this->utils->log("Output Buffer function set", "html_conditioning");
				ob_start(array($this->optimize, "sanitize_output"));
				

			// If the output buffer mode is the new "functionless" mode
			// - this mode allows us to trap fatal errors as well as output testing data via
	
			} else if ($this->output_buffer_mode == "functionless") {
				
			
				// declare this shutdown handler IFF we are going to be handling the output buffer
				if ($this->is_trial() && $this->trial_days_remaining() == 0) {
				} else {
					register_shutdown_function(array($this, 'pegasaas_shutdown_handler'));
					ini_set("display_errors", 1);

					ob_start();
				}
			
			}
	
			
			if (PegasaasAccelerator::$settings['settings']['gzip_compression']['status'] == '1') {
				$handlers = ob_list_handlers();
				//var_dump($handlers);
				if (!in_array("ob_gzhandler", $handlers)) {
					//ob_start("ob_gzhandler"); 
					
				}	
			}		
		} 
			if (isset($_GET['pegasaas-debug']) && $_GET['pegasaas-debug'] == "console") {
			$this->utils->console_log('Init 2522');
		}
		PegasaasUtils::log("Before Check Curl SSL", "script_execution_benchmarks");
	
		$this->utils->check_curl_ssl();
		if (isset($_GET['pegasaas-debug']) && $_GET['pegasaas-debug'] == "console") {
			$this->utils->console_log('Init 2528');
		}
		PegasaasUtils::log("After Check Curl SSL", "script_execution_benchmarks");

		if (is_admin() && PegasaasAccelerator::$settings['status'] == 1 && PegasaasAccelerator::$settings['last_api_check'] < time() - PEGASAAS_API_KEY_VALIDITY) {
			$this->api->pegasaas_api_key_check(PegasaasAccelerator::$settings['api_key'], false);
		}
		PegasaasUtils::log("After Pegasaas API Key Check", "script_execution_benchmarks");

		
		if (isset($_GET['pegasaas-debug']) && $_GET['pegasaas-debug'] == "console") {
			$this->utils->console_log('Init 2539');
		}
		
		if (is_admin()) {
			// do not apttempt to push scan if we're just requesting an accelerated chart.
			if (isset($_POST['c']) &&$_POST['c'] == "render-accelerated-chart") {
				return;
			}
			
			if ($this->init_in_progress()) {
				$score_data 		= $this->scanner->get_site_score();
				$benchmark_data 	= $this->scanner->get_site_benchmark_score(); 
	

				if ($score_data['scanned_urls'] == 0 || $benchmark_data['scanned_urls'] == 0) {
					add_action( 'admin_notices', array($this->interface,'admin_notice__init_in_progress') );
				} else {
					$this->set_init_notification(2);
					$this->hermes->send_init_complete_notification();
					add_action( 'admin_notices', array($this->interface,'admin_notice__init_completed') );
				}
				
				
			} else if ($this->init_completed()) {
				add_action( 'admin_notices', array($this->interface,'admin_notice__init_completed') );
			}
			$active_conflicting_plugins = $this->get_active_conflicting_plugins();
			if (sizeof($active_conflicting_plugins) > 0) {
				add_action( 'admin_notices', array($this->interface,'admin_notice__conflicting_plugin') );
			}
			
		// disabled because the scanner->get_site_score takes a long time on very large installations (>1000 posts)
			// as it invokes the GetAllPagesAndPosts function which takes 3s/100pages every time (does not use cache)
			if (false && get_option("pegasaas_accelerator_dismiss_review_prompt", 0) == 0) { 
				$time_of_install = strtotime(self::$settings['subscription_signup_date']);
				$two_weeks_ago = mktime(0,0,0,date("m"),date("d")-14,date("Y"));
				$site_scores = $this->scanner->get_site_score();
				if ($site_scores['mobile_score'] > 80) {
					$site_baseline_scores = $this->scanner->get_site_benchmark_score();
					$site_pagespeed_score = $site_scores['mobile_score'];
					$site_baseline_pagespeed_score = $site_baseline_scores['mobile_score'];
					$improvement = $site_pagespeed_score -  $site_baseline_pagespeed_score; 
					
					if ($time_of_install < $two_weeks_ago && $site_pagespeed_score > 85 && $improvement > 10) {
						add_action('admin_notices', array($this->interface, 'admin_notice__ask_for_review') );
					}
				}
			}
		} 
		
		$this->post_type_pages = get_option("pegasaas_post_types_pages", array());

		
		
		
		if ($this->font_awesome_version == "4") {
			// side bar and side menu
			define("PEGASAAS_DASHBOARD_ICON_CLASS", "fa fa-fw fa-dashboard");
			//define("PEGASAAS_CACHE_ICON_CLASS", "fa fa-fw fa-bolt");
			define("PEGASAAS_MEMORY_WARNING_ICON_CLASS", "fa fa-lg fa-hand-stop-o");
			define("PEGASAAS_CLOUDFLARE_ICON_CLASS", "fa fa-lg fa-cloud");
			
			
			define("PEGASAAS_SETTINGS_ICON_CLASS", "fa fa-fw fa-cog");
			define("PEGASAAS_USER_ACCOUNT_ICON_CLASS", "fa fa-fw fa-user-circle");
			define("PEGASAAS_FAQS_ICON_CLASS", "fa fa-fw fa-question-circle");
			define("PEGASAAS_SUPPORT_ICON_CLASS", "fa fa-fw fa-universal-access");
			define("PEGASAAS_CACHE_ICON_CLASS", "fa fa-fw fa-shield");
			define("PEGASAAS_REBUILD_CSS_ICON_CLASS", "fa fa-fw fa-bolt");

			// top dashboard
			define("PEGASAAS_MOBILE_ICON_CLASS", "fa fa-mobile");
			define("PEGASAAS_DESKTOP_ICON_CLASS", "fa fa-desktop");

			// each page
			define("PEGASAAS_GOOGLE_ICON_CLASS", "fa fa-fw fa-google");
			define("PEGASAAS_GTMETRIX_ICON_CLASS", "fa fa-fw fa-line-chart");
			define("PEGASAAS_EXTERNAL_LINK_ICON_CLASS", "fa fa-fw fa-external-link");
			define("PEGASAAS_RESCAN_ICON_CLASS", "fa fa-fw fa-refresh");
			
			// cache panel
			define("PEGASAAS_CSS_ICON_CLASS", "fa fa-pa-css");
			define("PEGASAAS_IMAGE_DATA_ICON_CLASS", "fa fa-pa-image-data");
			define("PEGASAAS_JS_ICON_CLASS", "fa fa-pa-js");
			define("PEGASAAS_CODE_ICON_CLASS", "fa fa-code");
			define("PEGASAAS_HTML_ICON_CLASS", "fa fa-html5");
			define("PEGASAAS_RESOURCE_CACHE_ICON_CLASS", "fa fa-file-image-o");
			define("PEGASAAS_CHART_AREA_ICON_CLASS", "fa fa-area-chart");
			define("PEGASAAS_CHART_LINE_ICON_CLASS", "fa fa-line-chart");
			define("PEGASAAS_PENDING_REQUEST_ICON_CLASS", "fa fa-question-circle");
			define("PEGASAAS_PICKUP_PENDING_REQUEST_ICON_CLASS", "fa fa-cloud-download");

			define("PEGASAAS_PURGE_ALL_ICON_CLASS", "fa fa-bomb");
			define("PEGASAAS_AUTO_ACCELERATE_ICON_CLASS", "fa fa-eraser");
			define("PEGASAAS_HIDE_DIAGNOSTICS_ICON_CLASS", "fa fa-eye-slash");
			define("PEGASAAS_SHOW_DIAGNOSTICS_ICON_CLASS", "fa fa-eye");

			// settings panel
			define("PEGASAAS_SETTING_PAGE_CACHING", "fa fa-fw fa-archive");
			define("PEGASAAS_SETTING_AUTO_CLEAR_PAGE_CACHE", "fa fa-fw fa-clock-o");
			define("PEGASAAS_SETTING_PAGE_CLOUDFLARE", "fa fa-fw fa-cloud");
			define("PEGASAAS_SETTING_GZIP_COMPRESSION", "fa fa-fw fa-file-archive-o");
			define("PEGASAAS_SETTING_BROWSER_CACHING", "fa fa-fw fa-chrome");
			define("PEGASAAS_SETTING_DNS_PREFETCH", "fa fa-fw fa-spinner");
			define("PEGASAAS_SETTING_MINIFY_HTML", "fa fa-fw fa-compress");
			define("PEGASAAS_SETTING_MINIFY_CSS", "fa fa-fw fa-compress");
			define("PEGASAAS_SETTING_MINIFY_JAVASCRIPT", "fa fa-fw fa-compress");
			define("PEGASAAS_SETTING_IMAGE_OPTIMIZATION", "fa fa-fw fa-image");
			define("PEGASAAS_SETTING_EXTERNAL_IMAGE_OPTIMIZATION", "fa fa-fw fa-image");
			define("PEGASAAS_SETTING_DEFER_RENDER_BLOCKING_JS", "fa fa-fw fa-hourglass-2");
			define("PEGASAAS_SETTING_DEFER_RENDER_BLOCKING_CSS", "fa fa-fw fa-hourglass-2");
			define("PEGASAAS_SETTING_INJECT_CRITICAL_CSS", "fa fa-fw fa-asterisk");
			define("PEGASAAS_SETTING_PROXY_EXTERNAL_JS", "fa fa-fw fa-exchange");
			define("PEGASAAS_SETTING_PROXY_EXTERNAL_CSS", "fa fa-fw fa-exchange");
			define("PEGASAAS_SETTING_CDN", "fa fa-fw fa-shield");
			define("PEGASAAS_SETTING_DISABLE_WP_EMOJI", "fa fa-fw fa-smile-o");
			define("PEGASAAS_SETTING_LAZY_LOAD", "fa fa-fw fa-spinner");
			define("PEGASAAS_SETTING_LAZY_IMAGES", "fa fa-fw fa-spinner");
			define("PEGASAAS_SETTING_LAZY_LOAD_TWITTER", "fa fa-fw fa-spinner");
			define("PEGASAAS_SETTING_LAZY_LOAD_YOUTUBE", "fa fa-fw fa-spinner");
			define("PEGASAAS_SETTING_PROMOTE", "fa fa-fw fa-bullhorn");
			define("PEGASAAS_SETTING_GOOGLE_FONTS", "fa fa-fw fa-google");
			define("PEGASAAS_SETTING_LOGGING", "fa fa-fw fa-file-text-o");
			define("PEGASAAS_SETTING_AUTO_CRAWL", "fa fa-fw fa-android");
			define("PEGASAAS_SETTING_EXCLUDE_URLS", "fa fa-fw fa-ban");
			define("PEGASAAS_SETTING_BLOG", "fa fa-fw fa-rss");
			define("PEGASAAS_SETTING_VARNISH", "fa fa-fw fa-shield");
			define("PEGASAAS_SETTING_FAV_ICONS", "fa fa-fw fa-paw");
			
			if (PegasaasAccelerator::$settings['settings']['display_mode']['status'] == 1) {
				define("PEGASAAS_SETTING_DISPLAY_MODE", "fa fa-fw fa-sun-o");
			} else if (PegasaasAccelerator::$settings['settings']['display_mode']['status'] == 2) {
				define("PEGASAAS_SETTING_DISPLAY_MODE", "fa fa-fw fa-circle-thin");

			} else {
				define("PEGASAAS_SETTING_DISPLAY_MODE", "fa fa-fw fa-moon-o");

			}
		} else {
			define("PEGASAAS_DASHBOARD_ICON_CLASS", "fas fa-fw fa-tachometer-alt");
			define("PEGASAAS_CACHE_ICON_CLASS", "fas fa-fw fa-bolt");
			define("PEGASAAS_MEMORY_WARNING_ICON_CLASS", "fa fa-lg fa-hand-stop-o");
			define("PEGASAAS_SETTINGS_ICON_CLASS", "fa fa-fw fa-cog");
			define("PEGASAAS_USER_ACCOUNT_ICON_CLASS", "fas fa-fw fa-user-circle");
			define("PEGASAAS_FAQS_ICON_CLASS", "far fa-fw fa-question-circle");
			define("PEGASAAS_SUPPORT_ICON_CLASS", "fa fa-fw fa-universal-access");
			define("PEGASAAS_MOBILE_ICON_CLASS", "fas fa-mobile-alt");
			define("PEGASAAS_DESKTOP_ICON_CLASS", "fas fa-desktop");
			define("PEGASAAS_GOOGLE_ICON_CLASS", "fab fa-google");
			define("PEGASAAS_EXTERNAL_LINK_ICON_CLASS", "fas fa-external-link-alt");
			define("PEGASAAS_CSS_ICON_CLASS", "fab fa-css3");
			define("PEGASAAS_CODE_ICON_CLASS", "fas fa-code");
			define("PEGASAAS_HTML_ICON_CLASS", "fab fa-html5");
			define("PEGASAAS_RESOURCE_CACHE_ICON_CLASS", "far fa-file-image");
			define("PEGASAAS_CHART_AREA_ICON_CLASS", "fas fa-chart-area");
			define("PEGASAAS_CHART_LINE_ICON_CLASS", "fas fa-chart-line");
			define("PEGASAAS_PENDING_REQUEST_ICON_CLASS", "far fa-question-circle");
			define("PEGASAAS_PICKUP_PENDING_REQUEST_ICON_CLASS", "far fa-cloud-download");
			define("PEGASAAS_PURGE_ALL_ICON_CLASS", "fa fa-bomb");

			define("PEGASAAS_AUTO_ACCELERATE_ICON_CLASS", "fa fa-erase");
			define("PEGASAAS_HIDE_DIAGNOSTICS_ICON_CLASS", "far fa-eye-slash");

			// settings panel
			define("PEGASAAS_SETTING_PAGE_CACHING", "fas fa-fw fa-archive");
			define("PEGASAAS_SETTING_GZIP_COMPRESSION", "fas fa-fw fa-file-archive");
			define("PEGASAAS_SETTING_BROWSER_CACHING", "fab fa-fw fa-chrome");
			define("PEGASAAS_SETTING_DNS_PREFETCH", "fas fa-fw fa-spinner");
			define("PEGASAAS_SETTING_MINIFY_HTML", "fas fa-fw fa-compress");
			define("PEGASAAS_SETTING_MINIFY_CSS", "fas fa-fw fa-compress");
			define("PEGASAAS_SETTING_MINIFY_JAVASCRIPT", "fas fa-fw fa-compress");
			define("PEGASAAS_SETTING_IMAGE_OPTIMIZATION", "far fa-fw fa-images");
			define("PEGASAAS_SETTING_EXTERNAL_IMAGE_OPTIMIZATION", "far fa-fw fa-images");
			define("PEGASAAS_SETTING_DEFER_RENDER_BLOCKING_JS", "fas fa-fw fa-stopwatch");
			define("PEGASAAS_SETTING_DEFER_RENDER_BLOCKING_CSS", "fas fa-fw fa-stopwatch");
			define("PEGASAAS_SETTING_INJECT_CRITICAL_CSS", "fas fa-fw fa-asterisk");
			define("PEGASAAS_SETTING_PROXY_EXTERNAL_JS", "fas fa-fw fa-exchange-alt");
			define("PEGASAAS_SETTING_PROXY_EXTERNAL_CSS", "fas fa-fw fa-exchange-alt");
			define("PEGASAAS_SETTING_CDN", "fab fa-fw fa-keycdn");
			define("PEGASAAS_SETTING_LAZY_LOAD", "fas fa-fw fa-spinner");
			define("PEGASAAS_SETTING_LAZY_LOAD_TWITTER", "fas fa-fw fa-spinner");
			define("PEGASAAS_SETTING_LAZY_LOAD_YOUTUBE", "fas fa-fw fa-spinner");
			define("PEGASAAS_SETTING_PROMOTE", "fas fa-fw fa-bullhorn");
			define("PEGASAAS_SETTING_GOOGLE_FONTS", "fa fa-fw fa-google");
			define("func", "fa fa-fw fa-file-text-o");

		}
	

		if (is_admin()) {
			$this->assert_white_label_info();
		}
		PegasaasUtils::log("End of Init", "script_execution_benchmarks");

	}

	function assert_white_label_info() {
		$plugin_location = PEGASAAS_ACCELERATOR_DIR."pegasaas-accelerator-wp.php";

		// condition plugin info
		$plugin = get_plugin_data($plugin_location);
		
		$plugin_setting = array_merge($plugin); 

		if (PegasaasAccelerator::$settings['settings']['white_label']['status'] == 1) {
			$plugin_setting['Name'] 	   = PegasaasAccelerator::$settings['settings']['white_label']['plugin_name'];
			$plugin_setting['PluginURI']   = PegasaasAccelerator::$settings['settings']['white_label']['plugin_website'];
			$plugin_setting['Description'] = PegasaasAccelerator::$settings['settings']['white_label']['plugin_description'];
			$plugin_setting['Author']      = PegasaasAccelerator::$settings['settings']['white_label']['plugin_author'];
			$plugin_setting['AuthorURI']   = PegasaasAccelerator::$settings['settings']['white_label']['plugin_website'];
		} else {
			$plugin_setting['Name'] 	   = "Pegasaas Accelerator WP";
			$plugin_setting['PluginURI']   = "https://pegasaas.com/";
			$plugin_setting['Description'] = "The Pegasaas Accelerator WP plugin provides complete, automated, and simplified web performance optimization for a faster website and higher Google PageSpeed score.";
			$plugin_setting['Author']      = "pegasaas.com";
			$plugin_setting['AuthorURI']   = "https://pegasaas.com/";

		}
		
		if ($plugin['Name'] != $plugin_setting['Name']) { 
			
			$plugin_file = file($plugin_location);
			$plugin_file = implode("", $plugin_file);
			$plugin_original = $plugin_file;

			$plugin_file = preg_replace("/Plugin Name\:(.*)/", "Plugin Name: {$plugin_setting['Name']}", $plugin_file);
			$plugin_file = preg_replace("/Plugin URI\:(.*)/", "Plugin URI: {$plugin_setting['PluginURI']}", $plugin_file);
			$plugin_file = preg_replace("/Description\:(.*)/", "Description: {$plugin_setting['Description']}", $plugin_file);
			$plugin_file = preg_replace("/Author\:(.*)/", "Author: {$plugin_setting['Author']}", $plugin_file);
			$plugin_file = preg_replace("/Author URI\:(.*)/", "Author URI: {$plugin_setting['AuthorURI']}", $plugin_file);
			
			
				
			if ($plugin_file != $plugin_original) {
				$fp = fopen($plugin_location, "w+");
				if ($fp) {
					$this->utils->log("SUCCESS writing PLUGIN INFO to {$plugin_file}", "file_permissions");
					fwrite($fp, $plugin_file);
					fclose($fp);
				}
			}  else {
					$this->utils->log("Unable to write PLUGIN INFO to {$plugin_file}", "file_permissions");

			}
		}	
	}


	function initialize_registered_page_post_types() {
		PegasaasUtils::log("initialize_registered_page_post_types ('wp' fired)", "script_execution_benchmarks");

		if (!is_admin()) {
			$this->clear_registered_page_post_types();
			if (is_archive()) {
				$this->this_post_type = "archive";
			} else {
				$this->this_post_type = get_post_type();
			}
			
			add_filter( 'woocommerce_before_shop_loop', array($this, 'register_woocommerce_categories'));
			add_filter( 'the_content', array($this, 'register_post_types' ));
			add_action( 'wp_footer', array($this, 'save_registered_page_post_types'));
		}
		
	}

	function pegasaas_check_ability_to_submit_to_ajax() {
		print "Success [".$this->execution_time()."]";
		wp_die();
	}
	
	function pegasaas_check_server_response_time() {
		$this->max_execution_time = 15;
		$response = array();
		$response['execution_time'] = number_format($this->execution_time() * 1000, 0, '.', ',');
		
		
		
		if ($response['execution_time'] > 3000) {
			$response['advice'] = "<h4>Slow Server Response</h4> It looks like your WordPress wp-admin/admin-ajax.php is extremely slow to load.  You can <button class='btn btn-xs btn-default' onclick='check_compatibility()'>check again</button>, <a href='https://pegasaas.com/knowledge-base/what-if-the-installation-wizard-detects-that-my-web-server-is-slow-to-respond/' target='_blank'>learn more</a>, or continue anyway.";
		} else if ($response['execution_time'] > 2000) {
			$response['advice'] = "<h4>Slow Server Response</h4> It looks like your WordPress wp-admin/admin-ajax.php is very slow to load.  You can <button class='btn btn-xs btn-default' onclick='check_compatibility()'>check again</button>, <a href='https://pegasaas.com/knowledge-base/what-if-the-installation-wizard-detects-that-my-web-server-is-slow-to-respond/' target='_blank'>learn more</a>, or continue anyway.";
		}
		header("Content-type: application/json");
		print json_encode($response);
		wp_die();
	}
	
	function pegasaas_check_api_reachable() {
		$this->max_execution_time = 35;
		header("Content-type: application/json");
		print json_encode(PegasaasPluginCompatibility::is_api_reachable());
		wp_die();
		
	}

	function pegasaas_check_test_optimization() {
		$this->max_execution_time = 15;
		header("Content-type: application/json");
		print json_encode(PegasaasPluginCompatibility::submit_test_optimization());
		wp_die();
		
	}	

	function pegasaas_check_push_fetch_test() {
		
		$this->max_execution_time = 30;
		header("Content-type: application/json");
		print json_encode(PegasaasPluginCompatibility::submit_push_fetch_test());
		wp_die();
		
	}		

	
	function pegasaas_check_webperf_data_fetch_test() {
		$this->max_execution_time = 30;
		header("Content-type: application/json");
		print json_encode(PegasaasPluginCompatibility::submit_webperf_data_fetch_test());
		wp_die();
		
	}		
	
	
	
	function pegasaas_check_compatibility() {
		$this->max_execution_time = 30;
		
		PegasaasCache::purge_varnish($this->get_home_url()."/");
		if ($this->cache->kinsta_exists()) {
			$this->cache->clear_kinsta_cache();
		}

		$plugin_issues = PegasaasPluginCompatibility::get_caching_plugins_issues(); 
		$system_issues = PegasaasPluginCompatibility::get_system_issues();
		$api_issues    = PegasaasPluginCompatibility::get_api_issues();
		
		
		
		$total_critical_issues = sizeof($plugin_issues['critical']) + sizeof($system_issues['critical']) + sizeof($api_issues['critical']);
		$total_warning_issues  = sizeof($plugin_issues['warning'])  + sizeof($system_issues['warning'])  + sizeof($api_issues['warning']);
	
		$response = array();
		if ($total_critical_issues > 0) {
			$response['status'] = -1;
			$response['html'] = "<h3>Compatibility Issues Detected</h3>";
			$response['html'] .= "<p>We've detected the following issues that need to be resolved before we can proceed with the installation.</p>";
		} else if ($total_warning_issues > 0) {
			$response['status'] = 0;
			$response['html'] = "<h3>Compatibility Issues Detected</h3>";
			$response['html'] .= "<p>We've detected the following issues that should be resolved before we proceed with the installation however you can try installing without addressing them.</p>";
			
		} else {
			$response['status'] = 1;
			$response['html'] = "<h3>Compatibility Check Passed!</h3>
			<div class='text-center compatibility-passed' ><i class='material-icons'>check</i></div>";
		//	$response['html'] .= "<p>We've detected the following issues that should be resolved before we proceed with the installation however you can try installing without addressing them.</p>";
	
		}
		$response['html'] .= "<ul>";
		if (sizeof($plugin_issues['critical']) > 0) {
		  foreach ($plugin_issues['critical'] as $issue) { 
			  $response['html'] .= "<li class='conflict'><i class='material-icons'>error</i> {$issue['title']} <a href='#' class='btn btn-xs btn-info more-link'>help</a>";
			  $response['html'] .= "<div>";
			  if (is_array($issue['advice'])) { foreach ($issue['advice'] as $advice) { $response['html'] .= "<p>{$advice}</p>"; } } else { $response['html'] .= "<p>".$issue['advice']."</p>"; }   
			  $response['html'] .= "</div></li>";
		   } 
		
		} else {
			if (sizeof($plugin_issues['warning']) > 0) {
				foreach ($plugin_issues['warning'] as $issue) { 
					  $response['html'] .= "<li class='conflict'><i class='material-icons'>error</i> {$issue['title']} <a href='#' class='btn btn-xs btn-info more-link'>help</a>";
					  $response['html'] .= "<div>";
					  if (is_array($issue['advice'])) { foreach ($issue['advice'] as $advice) { $response['html'] .= "<p>{$advice}</p>"; } } else { $response['html'] .= "<p>".$issue['advice']."</p>"; }   
					  $response['html'] .= "</div></li>";
				   }				
			}
			
			$reponse['passed_html'] .= "<li class='pass'><i class='material-icons'>done</i> No Plugin Conflicts Detected</li>";
		}
		
		if (sizeof($system_issues['critical']) > 0) {
		   foreach ($system_issues['critical'] as $issue) { 
			  $response['html'] .= "<li class='conflict'><i class='material-icons'>error</i> {$issue['title']} <a href='#' class='btn btn-xs btn-info more-link'>help</a>";
			  $response['html'] .= "<div>";
			  if (is_array($issue['advice'])) { foreach ($issue['advice'] as $advice) { $response['html'] .= "<p>{$advice}</p>"; } } else { $response['html'] .= "<p>".$issue['advice']."</p>"; }   
			  $response['html'] .= "</div></li>";
		   } 		
		} 
		if (sizeof($system_issues['warning']) > 0) {
			foreach ($system_issues['warning'] as $issue) { 
			  $response['html'] .= "<li class='pass'><i class='material-icons'>error</i> {$issue['title']} </li>";
		   } 		
		}		
		
		if (sizeof($system_issues['passed']) > 0) {
			foreach ($system_issues['passed'] as $issue) { 
			  $response['passed_html'] .= "<li class='pass'><i class='material-icons'>done</i> {$issue['title']} </li>";
		   } 		
		}
		
		if (sizeof($api_issues['critical']) > 0) {
		   foreach ($api_issues['critical'] as $issue) { 
			   $response['data'] = $issue['data'];
			  $response['html'] .= "<li class='conflict'><i class='material-icons'>error</i> {$issue['title']} <a href='#' class='btn btn-xs btn-info more-link'>help</a>";
			  $response['html'] .= "<div>";
			  if (is_array($issue['advice'])) { foreach ($issue['advice'] as $advice) { $response['html'] .= "<p>{$advice}</p>"; } } else { $response['html'] .= "<p>".$issue['advice']."</p>"; }   
			  $response['html'] .= "</div></li>";
		   } 		
		} 
		
		if (sizeof($api_issues['warning']) > 0) {
			foreach ($api_issues['warning'] as $issue) { 
			  $response['html'] .= "<li class='pass'><i class='material-icons'>error</i> {$issue['title']} </li>";
		   } 		
		}	
		
		if (sizeof($api_issues['passed']) > 0) {
			foreach ($api_issues['passed'] as $issue) { 
			  $response['passed_html'] .= "<li class='pass'><i class='material-icons'>done</i> {$issue['title']} </li>";
		   } 		
		}			
		$response['html'] .= "</ul>";
		print json_encode($response);
		
	
				
		
	 	wp_die();
		
		
	}
	
	function pegasaas_assert_global_cpcss() {
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			PegasaasDeferment::assert_global_cpcss();
			print 1;
		} else {
			print -1;
		}		
		
	 	wp_die();
	}
	
	function pegasaas_submit_scan_request() {
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$this->scanner->submit_scan_request();
			print 1;
		} else {
			print -1;
		}		
		
	 	wp_die();
	}	
	
	
	function pegasaas_auto_accelerate_pages() {
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$this->auto_accelerate_pages();
			print 1;
		} else {
			print -1;
		}		
		
	 	wp_die();		
	}
	
	function pegasaas_submit_benchmark_requests() {
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$this->scanner->submit_benchmark_requests();
			print 1;
		} else {
			print -1;
		}		
		
	 	wp_die();		
	}
	
	function pegasaas_process_critical_css_request() {
			
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
		
			$api_key 		= PegasaasAccelerator::$settings['api_key'];
			$request_id 	= $_POST['request_id'];
			$requested_url 	= $_POST['requested_url'];
			$verify_peer 	= get_option("pegasaas_accelerator_curl_ssl_verify", 0) == 1;
			
			$post_fields = array();
			$post_fields['api_key'] = $api_key;
			$post_fields['requested_url'] = $requested_url;
			$post_fields['request_id'] = $request_id;
			$post_fields['command'] = "FETCH";
			
			$args = array();
			$args['method'] 	= "POST";
			$args['sslverify'] = $verify_peer;
			$args['timeout']   = 10;
			$args['body']	   = $post_fields;
			
			$response = wp_remote_request("https://".PEGASAAS_ACCELERATOR_CACHE_SERVER."/critical-css/", $args);
		
			if (is_a($response, "WP_Error")) {
				$error = $response->get_error_message();
				$pegasaas->utils->log("API Error: {$error}", "cpcss");
				print -3;
				wp_die();
			} else {			
				$http_response 	= $response['http_response'];
				$response 		=  $http_response->get_data();
			
				//print "<br>response: $response";
				if ($response == "Invalid Request URL") {
					print -2;
					wp_die();
				} 

			
			
				$critical_css 	=  html_entity_decode($response);
				$resource_id 	= $_GET['wp_rid'];		
				if ($resource_id == "" && $_POST['wp_rid'] != "") {
					$resource_id = urldecode($_POST['wp_rid']);
				}
				
				if ($resource_id != "") {
					PegasaasDeferment::process_critical_css($critical_css, $resource_id);
				}

				print "1-".$resource_id;
			}
		} else {
			print -1;
		}		
		
	 	wp_die();		
	}
	
	

	static function has_wp_rest_nonce_changed() {
		global $pegasaas;

		$last_wp_rest_nonce = get_option("pegasaas_wp_rest_nonce", "");

		$current_wp_rest_nonce = wp_create_nonce("wp_rest");

		if ($last_wp_rest_nonce != $current_wp_rest_nonce) {
			$this->utils->log("wp_rest nonce CHANGED", "compatibility_contact_form_7");

			update_option("pegasaas_wp_rest_nonce", $current_wp_rest_nonce);
		
			if ($last_wp_rest_nonce != "") {
				if (PegasaasUtils::does_plugin_exists_and_active("contact-form-7")) {
					$pegasaas->cache->clear_contact_form_7_pages_cache();
				}
			}
		} else {
			$this->utils->log("wp_rest nonce NO CHANGE", "compatibility_contact_form_7");
		}

	}
	
	
	/* at the beginning of each page request, the registered entries for this page should be cleared */
	function clear_registered_page_post_types() {
		$this_object_id = $this->utils->get_object_id();
		foreach ($this->post_type_pages as $post_type => $objects) {
			if (array_key_exists($this_object_id, $this->post_type_pages["$post_type"])) {
				unset($this->post_type_pages["$post_type"]["$this_object_id"]);	
			}
		}
	}
	
	/* record the post types included in ths page */
	function register_post_types($content) {
		global $pegasaas;
		global $post;
		$object_id = $this->utils->get_object_id();
		$pegasaas->utils->log("register_post_types {$object_id} ", "caching");

	
		if (in_the_loop()) {
			$post_type = get_post_type($post);
			
			
			$pegasaas->utils->log("register_post_types {$object_id} for {$post_type} ", "caching");

			if ($this->this_post_type != $post_type && $post_type != "page" && $post_type != "post" && !strstr($object_id, "tag")) {
			    $page_level_settings = PegasaasUtils::get_object_meta($object_id, "accelerator_overrides", true);
				
				if (array_key_exists("accelerated", $page_level_settings) && $page_level_settings['accelerated'] > 0) {
					$this->post_type_pages["$post_type"]["$object_id"] = true;
				}

				/*
				// does this page have contact 7 nonce handling
				if (strstr($buffer, "wp-api-fetch-js-after") && strstr($buffer, 'name="_wpcf7"')) {
					// if so, then register this page for pages containing contact form 7 forms
					$this->post_type_pages["pages-containing-contact-form-7"]["$object_id"] = true;
				}
				*/
			}
		}
		
		return $content;
	}


	/* record the post types included in ths page */
	function register_woocommerce_categories($content) {
		global $pegasaas;
		global $post;
		$object_id = $this->utils->get_object_id();
	
	
		
			$post_type = get_post_type($post);

	//	print "this_post_type = ".$this->this_post_type." !=? $post_type<br>";
	//	exit;

			if ($this->this_post_type != $post_type && $post_type != "page" && $post_type != "post" && !strstr($object_id, "tag")) {
			    $page_level_settings = PegasaasUtils::get_object_meta($object_id, "accelerator_overrides", true);
				
				// because woo commerce category pages are not explicitly enabled, we assume we should clear them anyway
				//if (array_key_exists("accelerated", $page_level_settings) && $page_level_settings['accelerated'] > 0) {
					$this->post_type_pages["$post_type"]["$object_id"] = true;
				//}

				/*
				// does this page have contact 7 nonce handling
				if (strstr($buffer, "wp-api-fetch-js-after") && strstr($buffer, 'name="_wpcf7"')) {
					// if so, then register this page for pages containing contact form 7 forms
					$this->post_type_pages["pages-containing-contact-form-7"]["$object_id"] = true;
				}
				*/
			}
		
		
		return $content;
	}
	
	/* upon the completion of the page request, we should re-save the registered post types for this page */
	function save_registered_page_post_types() {
		//print "yeah";
		
		update_option("pegasaas_post_types_pages", $this->post_type_pages);
	}
	
	

	function init_in_progress() {
		return get_option("pegasaas_init_notification", 0) == 1;
	}

	function init_completed() {
		return get_option("pegasaas_init_notification", 0) == 2;
	}	

	

	
	
	function is_recent_version($version, $scope = "recent") {
			$new_minor_gap = 1;
			$recent_minor_gap = 2;
		$version_data = explode(".", $version);
		$major = $version_data[0];
		$minor = $version_data[1];
		$patch = $version_data[2];
		
		$current_version = $this->get_current_version();
		
		if (false && $this->is_pro_edition()) {
			$current_version = $this->get_update_version();
			$new_minor_gap = 0;
			$recent_minor_gap = 1;
		}
		
		if ($current_version == "wp-dev") {
			$current_version = "3.0.0";
		}
		$current_version_data = explode(".", $current_version);
		$current_major = $current_version_data[0];
		$current_minor = $current_version_data[1];
		$current_patch = $current_version_data[2];
		

		if ($scope == "new") {
			if (($current_major == $major && $current_minor - $new_minor_gap <= $minor)) {
				return true;
			} else {
				return false;
			}
		} else {
			if (($current_major == $major && $current_minor - $recent_minor_gap <= $minor)) {
			
				return true;
			} else {
				return false;
			}
		}
	}
	
	function ob_capture($buffer = "") {
		return $buffer;
		global $pegasaas;
		$pegasaas->ob_buffer = "UU3".$buffer."AAA3";
		return "Y5".$buffer."Z5";
	}
	
	function admin_post_commands() {
		if (is_admin()) {

			if (isset($_GET['c']) && $_GET['c'] == "rebuild-page-cache") {
				$this->cache->clear_cache($_GET['p']);
			}
			
			if (isset($_GET['c']) && $_GET['c'] == "purge-cpcc") {
				PegasaasDeferment::clear_critical_css_cache($_GET['p']);
			}			
			if (isset($_GET['c']) && $_GET['c'] == "recalc-cpcc") {
				PegasaasDeferment::clear_critical_css_cache($_GET['p']);
				PegasaasDeferment::request_critical_css("", "", $_GET['p'], "", "request_gpsi_score", true);

			}
			if (isset($_GET['c']) && $_GET['c'] == "build-cpcc") {
				PegasaasDeferment::request_critical_css("", "", $_GET['p'], "", "request_gpsi_score", true);
			}
			
			if ( (isset($_POST['c']) && $_POST['c'] == "clear-critical-css") 			|| (isset($_POST['c']) && $_POST['c'] == "clear-all-data" ) ||( isset($_GET['c']) &&$_GET['c'] == "rebuild-critical-css") ) {
				
				$this->cache->clear_local_resource_cache("critical.css");
				$this->cache->clear_local_resource_cache("image-data.json");

			} 
			if ( (isset($_POST['c']) &&$_POST['c'] == "clear-post-type-critical-css")	|| (isset($_POST['c']) && $_POST['c'] == "clear-all-data" )  || (isset($_GET['c']) &&$_GET['c'] == "rebuild-critical-css") ) {
				PegasaasDeferment::clear_critical_css_cache("post_type");	
			} 
			
			
			
			
			if ( (isset($_POST['c']) &&$_POST['c'] == "clear-deferred-js") 				|| (isset($_POST['c']) && $_POST['c'] == "clear-all-data" )  || (isset($_GET['c']) &&$_GET['c'] == "purge-html-cache") ) {
				PegasaasDeferment::clear_deferred_js();	
			} 
			
			if ( (isset($_POST['c']) &&$_POST['c'] == "clear-queued-requests") 			|| (isset($_POST['c']) && $_POST['c'] == "clear-all-data" ) ) {
				$this->clear_queued_requests();	
			} 
			if ( (isset($_POST['c']) &&$_POST['c'] == "clear-queued-optimization-requests") 			|| (isset($_POST['c']) && $_POST['c'] == "clear-all-data" ) ) {
				$this->clear_queued_optimization_requests();	
			} 			

			
			if ( (isset($_POST['c']) &&$_POST['c'] == "clear-cache") 					|| (isset($_POST['c']) && $_POST['c'] == "clear-all-data" ) || (isset($_GET['c']) &&$_GET['c'] == "purge-html-cache") ) {
				$this->cache->clear_cache();	
			} 

		
			
			if (isset($_GET['c']) &&$_GET['c'] == "purge-local-css-cache") {
				$this->cache->clear_local_resource_cache("css");	
			}

			if ( (isset($_POST['c']) &&$_POST['c'] == "clear-pagespeed-requests")			|| (isset($_POST['c']) && $_POST['c'] == "clear-all-data" ) ) {
				$this->scanner->clear_pagespeed_requests();	
			} 
			
			
			if ( (isset($_POST['c']) && $_POST['c'] == "clear-pagespeed-benchmark-requests") || (isset($_POST['c']) && $_POST['c'] == "clear-all-data" ) ) {
				
				$this->scanner->clear_pagespeed_benchmark_requests();	
			} 
			if ( (isset($_POST['c']) &&$_POST['c'] == "clear-pagespeed-scores") 			|| (isset($_POST['c']) && $_POST['c'] == "clear-all-data") ) {
				$this->scanner->clear_pagespeed_requests();
				$this->scanner->clear_pagespeed_scores();	
			} 
			if ( (isset($_POST['c']) &&$_POST['c'] == "clear-pagespeed-benchmark-scores") 	|| (isset($_POST['c']) && $_POST['c'] == "clear-all-data") ) {
				$this->scanner->clear_pagespeed_benchmark_requests();
				$this->scanner->clear_pagespeed_benchmark_scores();	
			}
			if ( (isset($_POST['c']) &&$_POST['c'] == "clear-all-data") 					|| (isset($_POST['c']) && $_POST['c'] == "clear-all-semaphore-locks") ) {
				$this->utils->clear_all_semaphores();
			}
			// do not apttempt to push scan if we're just requesting an accelerated chart.
			if (isset($_POST['c']) &&$_POST['c'] == "render-accelerated-chart") {
				return;
			}
			if (isset($_POST['c']) &&$_POST['c'] == "reset-log-file") {
				$this->utils->reset_log_file();
			}		
			$this->utils->log_load_milestone("Init 423");
			
		}
		
	}

	function pegasaas_clear_queued_optimization_requests() {
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$this->clear_queued_optimization_requests();
			
			print 1;
		} else {
			print -1;
		}		
		
	 	wp_die();
	}
	
	function pegasaas_clear_optimization_request() {
	
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$resource_id = $_POST['resource_id'];
			$this->db->delete("pegasaas_api_request", array("resource_id" => $resource_id, 
															"request_type" => "optimization-request"));
				
			$url = $this->get_home_url().str_replace("?accelerate=off", "", $resource_id);
			
			$response = $this->api->post(array("command" => "cancel-optimization-request", 
											   "url" => $url), 
										 array('timeout' => $this->api->get_general_api_request_timeout(), 
											   'blocking' => false)); 
			print 1;
		} else {
			print -1;
		}		
		
		
	 	wp_die();

	}
	
	
	function pegasaas_process_optimization_request() {
		global $pegasaas;
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$this->utils->log("Pegasaas Process Optimization Request Invoked (".($this->execution_time()).")", "script_execution_benchmarks");
			
			
	
			$api_key 		= PegasaasAccelerator::$settings['api_key'];
			$url			= "https://".PEGASAAS_ACCELERATOR_CACHE_SERVER."/!/fetch-critical-css";
			$request_id 	= $_POST['request_id'];
			$requested_url 	= $_POST['requested_url'];
			$geo_modifier   = $_SERVER['HTTP_X_PEGASAAS_GEO_MODIFIER'];
			
			if ($geo_modifier != "") {
				if ($pegasaas->utils->does_plugin_exists_and_active("geotargetingwp")) {
					$pegasaas->cache->set_cache_geo_modifier($geo_modifier);
				}
			}
			print "Pegasaas Accelerator WP<br>";

			if (array_key_exists("method", $_POST) && $_POST['method'] == "fetch") { 
				
				if ($_POST['api_node'] != "") {
					PegasaasAccelerator::$settings['settings']['api_node']['status'] = $_POST['api_node'];
				}
				$this->utils->log("Pegasaas Process Optimization Before Fetch (".($this->execution_time()).")", "script_execution_benchmarks");

				print "Fetching from API Endpoint: ".$this->api->get_api_node_url()."<br>";
				$start_fetch = $this->execution_time();
				$response = $this->api->fetch_finished_optimization($_POST['optimization_id']);

				$end_fetch = $this->execution_time();
				$this->utils->log("Pegasaas Process Optimization After Fetch (".($this->execution_time()).")", "script_execution_benchmarks");

				$total_fetch_time = number_format($end_fetch - $start_fetch, 3, '.', '');
				print "Fetch Time: {$total_fetch_time}s<br>";
				$html = $response['optimization_request']['data'];
				$css_validation_issue = $response['optimization_request']['css_validation_issue'];
				$secondary_files = $response['optimization_request']['secondary'];
				
				$method = "pull";
				
				$this->utils->log("Pegasaas Process Optimization Fetch Time: $total_fetch_time", "script_execution_benchmarks");

			} else {
	
				$secondary_files 	= json_decode(stripslashes($_POST['secondary']), true);
				$html 	= stripslashes($_POST['data']);
				$method = "push";
				$css_validation_issue = $response['optimization_request']['css_validation_issue'];


			}
			
			

			
			$resource_id 	= $_GET['wp_rid'];	
		

			if ($resource_id == "" && $_POST['wp_rid'] != "") {
				$resource_id = urldecode($_POST['wp_rid']); // changed Jan 29, 2018 as the post resource id is not url encoded
			}
			$priority_flag = urldecode($_POST['priority']);
			
			
			print "Plugin Address: {$_SERVER['SERVER_ADDR']}<br>";
			print "Resource ID: $resource_id<br>";
			$status_code = 1;
			if ($resource_id != "") { 
				if (strlen($html) == 0) {
					$status_code = -2;
				}
				print " -Size: ".strlen($html)."<br>";
				print " -CSS Validation Issue: ".$css_validation_issue."<br><br>";
				//print "sizeof secondary files: ".sizeof($secondary_files)."<br>";
				if (is_array($secondary_files) && sizeof($secondary_files) > 0) {
					print "Secondary Files:<br>";
					foreach ($secondary_files as $secondary_file) { 
						$filename = $secondary_file['filename'];
						print " -Filename: $filename<br>";
						$data = $secondary_file['data'];
						print " -Size: ".strlen($data)."<br>";

						$resource_path = $this->utils->strip_query_string($resource_id);

						// added Jan 29, 2018 to handle non latin characters
						$resource_path = urlencode($this->utils->strip_query_string($resource_id));
						$resource_path = str_replace("%2F", "/", $resource_path);
						

						$path =  PEGASAAS_CACHE_FOLDER_PATH."{$resource_path}";
					//	print "Path is: $path <br>";
						//$pegasaas->utils->log("Process Optimization Request, creating {$path}", "caching");

						if (!is_dir($path)) { 
							$this->cache->mkdirtree($path, 0755, true);
						}
						if ($fp = fopen(PEGASAAS_CACHE_FOLDER_PATH."".$filename, "w")) {
							fwrite($fp, $data);
							fclose($fp);
							print "File saved to: ".PEGASAAS_CACHE_FOLDER_PATH."".$filename."<br>";

						} else {
							print "Unable to write to: ".PEGASAAS_CACHE_FOLDER_PATH."".$filename."<br>";
						}


						


							
						print "<br>";
					}
				}
		
				$api_request = $this->db->get_single_record("pegasaas_api_request", array("request_type" => "optimization-request", "request_id" => $request_id));
				$is_manual_request = $api_request->advisory == "manual";
				
				$this->process_optimization($html, $resource_id, $request_id, $requested_url, $method, $css_validation_issue); 
				$this->utils->log("Pegasaas Process Optimization Request Finished Succesfully (".($this->execution_time()).")", "script_execution_benchmarks");

				$this->scanner->maybe_request_page_scan($resource_id, $priority_flag, $is_manual_request);
				
				print "Priority Flag: {$priority_flag}<br>";
				print "Status: {$status_code}";
				$this->utils->log("Pegasaas Process Optimization Request Finished Succesfully (".($this->execution_time()).")", "script_execution_benchmarks");

			
			} else {
				print "Status: 0";
				$this->utils->log("Pegasaas Process Optimization Request Finished - no resource id (".($this->execution_time()).")", "script_execution_benchmarks");

			}


			
		} else if ($_POST['optimization_id'] == "test-optimization-submission") {
			print 1;
		
		} else {
			var_dump($_POST);
			print -1;
		}		
		
	
	 	wp_die();

	}

	
	function process_optimization($html, $resource_id, $request_id, $url, $method = "push", $css_validation_issue = false) { 
			if ($this->db->has_record("pegasaas_api_request", array("request_id" => $request_id, "resource_id" => $resource_id, "request_type" => "optimization-request")) || $method == "pull") {
				$request_id = $this->db->delete("pegasaas_api_request", array("resource_id" => $resource_id, "request_type" => "optimization-request"));
				
				// if we receive a blank optimization back, it means that there was a problem either with the API or the API
				// is informing us that the optimization does not exist, in which case we should clear the cache and allow the system to re-submit
				// optimization as required on next page view
				if ($html == "") {
					if ($_GET['pegasaas-debug'] == "pickup-optimization-requests") {
						print "have a blank one {$resource_id} / {$request_id}<br>";
					}
					$this->utils->log("Optimization HTML Was Blank, Clearing Cache (".($this->execution_time()).")", "script_execution_benchmarks");
					
					$this->cache->clear_page_cache($resource_id);
					$this->utils->log("Optimization HTML Was Blank, Cache Cleared (".($this->execution_time()).")", "script_execution_benchmarks");
	
				} else {
					$is_temp_cache = false;
					$this->utils->log("Optimization HTML Was Sufficient, Saving Cache (".($this->execution_time()).")", "script_execution_benchmarks");

					$this->cache->save_page_cache($html, $resource_id, $url, $is_temp_cache, $css_validation_issue);
					$this->utils->log("Optimization HTML Was Sufficient, Cache Saved (".($this->execution_time()).")", "script_execution_benchmarks");

				}
				
				
			} else {
				//print "no existingg record {$request_id}<br>\n";
			}
		
		
		
	}	
	
	function resolve_conflict($plugin) {
		if ($plugin == "rlrsssl_really_simple_ssl") {
			
			$htaccess_location = $this->get_home_path().".htaccess";
			if (file_exists($htaccess_location)) {
				$htaccess_file = file($htaccess_location);
				$htaccess_file = implode("", $htaccess_file);
				$htaccess_original = $htaccess_file;
			
			
				$matches = array();
				preg_match("/# BEGIN rlrssslReallySimpleSSL(.*)# END rlrssslReallySimpleSSL\n/si", $htaccess_file, $matches);
				$rlrsssl_really_simple_ssl_code = $matches[0];
				if ($rlrsssl_really_simple_ssl_code != "") {
					
				    $htaccess_file = str_replace($rlrsssl_really_simple_ssl_code, "", $htaccess_file);
					$htaccess_file = $rlrsssl_really_simple_ssl_code.$htaccess_file;

					if ($htaccess_file != $htaccess_original) {
						$state = "moving";
						
						if ($this->utils->is_htaccess_safe($htaccess_file, "THIRD PARTY CONFLICT RESOLUTION ({$state})")) {
							$this->utils->write_file($htaccess_location.'--backup', $htaccess_original);
							$this->utils->write_file($htaccess_location, $htaccess_file);
						}						
						
						//$this->utils->write_file($htaccess_location, $htaccess_file);
					}
				}
			}
		}
	}
	
	
	
	

	
	
	function pegasaas_send_exit_survey() {  
		
		if (!isset($_POST['reason_id'])) {
			exit;
		}
		
		// if this is a temporary installation, and the user indicates that they want to
		// retain the settings and data, then do nothing
		if ($_POST['reason_id'] == "reason-1" && $_POST['reason_details'] == "1") {
			$this->disable();
			
			// retain api key in the event the user uninstalls the plugin, 
			// so that we can notify the API that the subscription has been
			// terminated
			update_option("pegasaas_deactivated_api_key", PegasaasAccelerator::$settings['api_key']);

		// otherwise, nuke it all
		} else {

			$this->deactivate();
			

		}
	

		
		$post_fields = array();
		$post_fields["command"] 			= "submit-exit-survey";
		$post_fields["reason_id"] 	= intval(str_replace("reason-", "", $_POST['reason_id']));
		$post_fields["reason"] 		= sanitize_text_field($_POST['reason']);
		$post_fields["reason_details"] = sanitize_text_field($_POST['reason_details']);
		$post_fields["installation_id"] = PegasaasAccelerator::$settings['installation_id'];
		
		$args = array(
			'body' => $post_fields,
			'sslverify' => false,
			'timeout' => 15,
		);
		
		$resp = wp_remote_post(PEGASAAS_API_KEY_SERVER, $args);
		
		echo 1;
		exit;		
	}

	
	
	
	function pegasaas_deactivation_exit_survey() {
		wp_enqueue_style("pegasaas-plugin-modal", PEGASAAS_ACCELERATOR_URL . 'assets/css/plugin-exit-survey.css');
		wp_enqueue_script('pegasaas-plugin-exit-survey', PEGASAAS_ACCELERATOR_URL . 'assets/js/plugin-exit-survey.js');
	}
	


	
	
	
	function pre_condition_admin_page() {
		$debug = true;
		if ($debug) {
			$this->utils->console_log('beginning of pre_condition_admin_page');
		}	
		if ( $_GET['page'] == "pegasaas-accelerator" || $_GET['page'] == "pa-web-perf") {	
			
			if (PegasaasUtils::does_plugin_exists_and_active("wp-optimize")) {
				PegasaasCache::disable_wp_optimize_cache();
			}

			
			// submit list to be scanned
			if ($this->utils->memory_within_limits()) {
				
			
	
				$this->utils->console_log('Before Clear Stale Requests');
				$this->clear_stale_requests();
				

				$this->utils->console_log('Before Auto Accelerate Requests');
				// possibly a bottleneck here
				
				
				
				
				if (isset($_GET['action']) && $_GET['action'] == "nudge") {
					$this->auto_submit_requests_to_api();
				} else {
					$this->auto_submit_requests_to_api("ajax");
				}
				
			
			
		}		
	}
	
		
		
		}
	
	function auto_submit_requests_to_api($method = "direct") {
			if (isset($_GET['test'])) {
				return;
			}
			if ($this->utils->memory_within_limits()) {
			
				
			if (isset($_POST['acceleration-type']) || $method == "direct") {
					
				$this->auto_accelerate_pages();
				
			} else {
				add_action("admin_footer", array($this->interface, "inject_footer_ajax_request__auto_accelerate_pages"));
			}
				
			$global_cpcss_requests = 0;	
				
				
			if (PegasaasAccelerator::$settings['limits']['monthly_optimizations'] == 0) { 
				if ($method == "direct" || true) { 
					$global_cpcss_requests = PegasaasDeferment::assert_global_cpcss();
				} else {
					add_action("admin_footer", array($this->interface, "inject_footer_ajax_request__assert_global_cpcss"));
				}
			}
				
				

				$this->utils->console_log('Before Submit Scan Requests');
				if ($method == "direct") { 
					$this->scanner->submit_scan_request();
				} else {
					add_action("admin_footer", array($this->interface, "inject_footer_ajax_request__submit_scan_request"));
				}
				
				$this->utils->console_log('After Submit Scan Requests');
					
				if (($this->utils->is_kinsta_server() || $this->utils->is_siteground_server()) && $global_cpcss_requests > 0) {
					
				} else {
					
					$this->utils->console_log('Before Submit Benchmark Requests');
					
					if ($method == "direct") { 
						$this->scanner->submit_benchmark_requests();
					} else {
						add_action("admin_footer", array($this->interface, "inject_footer_ajax_request__submit_benchmark_requests"));
					}
					

				}
				
				
			} 
			
			$force_pickup = $_POST['c'] == "pickup-pending-requests" || $_GET['c'] == 'pickup-pending-requests';
		
			 if ($force_pickup || (!$this->utils->is_kinsta_server() && !$this->utils->is_siteground_server())) {
				if ($debug) { $this->utils->console_log('Before Pickup Queued Requests'); }	
				 	if ($method == "direct") { 
						$this->api->pickup_queued_requests($force_pickup); 
				 	} 
				
				 
				if ($debug) { $this->utils->console_log('After Pickup Queued Requests'); }	
			 }
	}
	
	function pegasaas_recalculate_cpcss() {
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$this->clear_critical_css_cache($_POST['resource_id']);
			$this->request_critical_css("", "", $_POST['resource_id'], "", "", false);
			print json_encode(array("status" => 1));
		
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));	
		}
		
		wp_die(); // required in order to not have a trailing 0 in the json response		
	}
	
	function pegasaas_purge_cpcss() {
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$this->clear_critical_css_cache($_POST['resource_id']);
			print json_encode(array("status" => 1));
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));	
		}
		
		wp_die(); // required in order to not have a trailing 0 in the json response			
	}
	
	
	function pegasaas_rebuild_all_critical_css() {
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$this->clear_critical_css_cache();
			print json_encode(array("status" => 1));
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response		
	}	
	
	function clear_all_data() {
		if (false && $this->is_pro_edition()) {
			$this->clear_critical_css_cache();	
		}
	
		PegasaasDeferment::clear_deferred_js();	
		$this->clear_queued_requests();	
		$this->clear_queued_optimization_requests();	
		$this->cache->clear_cache();	
		$this->scanner->clear_pagespeed_requests();	
		$this->scanner->clear_pagespeed_benchmark_requests();	
		$this->scanner->clear_pagespeed_scores();	
		$this->scanner->clear_pagespeed_benchmark_scores();	
		$this->clear_stale_requests();
		$this->auto_accelerate_pages();
		
	}


	
	function set_accelerated_pages() {
		$accelerated_pages = get_option("pegasaas_accelerated_pages", array());
		$acceleration_data = $this->scanner->get_site_pages_accelerated_data();

		$post_fields = array();
		$post_fields['version']	= 2;
		$post_fields['status']	= 1;
		$post_fields['command']	= "set-accelerated-pages";
		$post_fields['accelerated_pages'] = $acceleration_data['accelerated'] + $acceleration_data['pending'];	

		$this->api->post($post_fields, array("blocking" => false));
	}
	
	
	
	
	function enable_all_for_post_type($post_type) {
		$debug = true; 
		//$acceleration_data = $this->scanner->get_site_pages_accelerated_data();
				
		$accelerated_pages = get_option("pegasaas_accelerated_pages", array());
		$start_size 	   = sizeof($accelerated_pages);

		$total_accelerated = 0;
		foreach ($accelerated_pages as $post_id => $page_info) {
			$page_level_settings = PegasaasUtils::get_object_meta($post_id, "accelerator_overrides");
			if ($page_level_settings['accelerated'] == 1) {
				$total_accelerated++;
			}
		}
		
		$this->utils->debug($debug, "Status: # of accelerated pages $total_accelerated\n");
		
			
		if ($total_accelerated < PegasaasAccelerator::$settings['limits']['maximum_page_accelerations_available']) {
			$this->utils->debug($debug, "Fewer Accelerator Pages than Limit \n");
			$this->utils->debug($debug, "POST['acceleration-type'] == {$_POST['acceleration-type']} \n");

			
			
			$posts_array = $this->utils->get_all_pages_and_posts();
			$accelerate_limit = $_POST['page-count'];
			$disable_acceleration_on_remaining = true;
			$accelerate_limit = PegasaasAccelerator::$settings['limits']['maximum_page_accelerations_available'];
				
			foreach ($posts_array as $pid => $post) {
				
				$resource_id = $post->resource_id;
				print "<br>slug: ".$resource_id."<br>";
				print "pid: ".$pid."<br>";
				
				
				$post_acceleration_status = true;
				

				if ($disable_acceleration_on_remaining) {
					if ($post->post_type != $post_type) {
								$post_acceleration_status = false;
						continue;
					} 
					
						
					// if we have exceeded the allowable accelerations for this subscription, set the acceleration status to 0
					if ($total_accelerated >= $accelerate_limit && $disable_acceleration_on_remaining) {
						$post_acceleration_status = false;
					}

					if ($this->is_excluded_url($post->slug)) {
						$post_acceleration_status = false;
					}

					if (array_key_exists($resource_id, $accelerated_pages)) {
						$page_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides");
						if ($page_settings['accelerated'] == true) {
							$this->utils->debug($debug, "Status ({$resource_id}): Page Already Accelerated {$resource_id}\n");
							continue; 
						}
						

					} 
					
					
					
						if ($post_acceleration_status == true) {
								$total_accelerated++;
								$accelerated_pages["{$resource_id}"] = 1;
								$this->utils->all_pages_and_posts["$pid"]->accelerated = true;
								
						} else if ($disable_acceleration_on_remaining) {
								$accelerated_pages["{$resource_id}"] = 0;
								$this->utils->all_pages_and_posts["$pid"]->accelerated = true;
						}
							
						if (!$post_acceleration_status) {
								unset($this->utils->all_pages_and_posts["$pid"]);
							
						}


						$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides");
						
						if (!$post_acceleration_status) { 
							$post_acceleration_status = 0;
						}
						if (!array_key_exists("accelerated", $page_level_settings) || $page_level_settings['accelerated'] == false) {
								$page_level_settings['accelerated'] = $post_acceleration_status;
								$this->utils->update_object_meta($resource_id, "accelerator_overrides", $page_level_settings);
								$this->utils->debug($debug, "Status: Setting Acceler Status {$resource_id} -- {$post_acceleration_status}\n");
						} else {
							$this->utils->debug($debug, "Status: post may already be accelerated {$resource_id} \n");

						}

					

					if ($total_accelerated >= $accelerate_limit && !$disable_acceleration_on_remaining ) {
						$this->utils->debug($debug, "Status: LIMIT of Accelerated pages (".PegasaasAccelerator::$settings['limits']['maximum_page_accelerations_available'].") < ".sizeof($accelerated_pages)."\n");
						break;
					}
					
				}
			}
				
			$end_size = sizeof($accelerated_pages);
			$this->utils->log("update_option('pegasaas_accelerated_pages', Array({$end_size})) -- PegasaasAccelerator::enable_all_for_post_type('{$post_type}')", "data_structures");

			update_option("pegasaas_accelerated_pages", $accelerated_pages, false);
				
			if ($start_size != $end_size) { 
				$this->set_accelerated_pages();
			}
		}
	}
	
	
	
	
	function disable_all_for_post_type($post_type) {
		$debug = false;
		
		$accelerated_pages = get_option("pegasaas_accelerated_pages", array());
		$start_size 	   = sizeof($accelerated_pages);

		$total_accelerated = 0;
		foreach ($accelerated_pages as $post_id => $page_info) {
			$page_level_settings = PegasaasUtils::get_object_meta($post_id, "accelerator_overrides");
			if ($page_level_settings['accelerated'] == 1) {
				$total_accelerated++;
			}
		}
		
		$this->utils->debug($debug, "Status: # of accelerated pages $total_accelerated\n");
		
			
		//if ($total_accelerated < PegasaasAccelerator::$settings['limits']['maximum_page_accelerations_available']) {
			//$this->utils->debug($debug, "Fewer Accelerator Pages than Limit \n");
		//	$this->utils->debug($debug, "POST['acceleration-type'] == {$_POST['acceleration-type']} \n");

			
			
			$posts_array = $this->utils->get_all_pages_and_posts();
		//	$accelerate_limit = $_POST['page-count'];
			$disable_acceleration_on_remaining = true;
		//	$accelerate_limit = PegasaasAccelerator::$settings['limits']['maximum_page_accelerations_available'];
				
			foreach ($posts_array as $pid => $post) {
				
				$post_id = $post->resource_id;
				$post_acceleration_status = false;
				

				if ($disable_acceleration_on_remaining) {
					if ($post->post_type != $post_type) {
								
						continue;
					} 
					
					

					
					
					
					    if ($disable_acceleration_on_remaining) {
								$accelerated_pages["{$post_id}"] = 0;
								$this->utils->all_pages_and_posts["$pid"]->accelerated = true;
						}
							
						if (!$post_acceleration_status) {
								unset($this->utils->all_pages_and_posts["$pid"]);
							
						}


						$page_level_settings = PegasaasUtils::get_object_meta($post_id, "accelerator_overrides");
						
						if (!$post_acceleration_status) { 
							$post_acceleration_status = 0;
						}
					
						if (array_key_exists("accelerated", $page_level_settings) && $page_level_settings['accelerated'] == true) {
							$this->cache->clear_html_cache($post->ID);
						}
						//if (!array_key_exists("accelerated", $page_level_settings) || $page_level_settings['accelerated'] == false) {
								$page_level_settings['accelerated'] = $post_acceleration_status;
								$this->utils->update_object_meta($post_id, "accelerator_overrides", $page_level_settings);
								$this->utils->debug($debug, "Status: Setting Acceler Status {$post_id} -- {$post_acceleration_status}\n");
						//} 

					

					
					
				
			}
				
			$end_size = sizeof($accelerated_pages);
			$this->utils->log("update_option('pegasaas_accelerated_pages', Array({$end_size})) -- PegasaasAccelerator::disable_all_for_post_type('{$post_type}')", "data_structures");

			update_option("pegasaas_accelerated_pages", $accelerated_pages, false);
				
			if ($start_size != $end_size) { 
				$this->set_accelerated_pages();
			}
		}
	}	
	
	function auto_accelerate_pages($reset = false) {
		$debug = false;
		global $test_debug;
		$critical_logging = false; 
		if ($test_debug) {
			$debug = true;
		}
		$debug = false;
		if (PegasaasAccelerator::$settings['status'] != 1) {
			if ($debug) {
				print "<pre>Not Enabled Yet</pre>"; 
			}
			return;
		}
		if ($critical_logging) {
			$this->utils->log("AUTO ACCELERATE_PAGES START ({$_POST['acceleration-type']})", "critical");
		}

		if (isset($_GET['pegasaas_debug']) && strstr($_GET['pegasaas_debug'],"auto_accelerate_pages")) { 
			$debug = true;
		}
	
		if (is_array($_POST) && isset($_POST['acceleration-type'])) {
			$wait_time = 10000;
		} else {
			$wait_time = 0;
		}

		// auto accelerate pages will not execute if there is a locking semphore existing that is less than 20 seconds old
		if ($this->utils->semaphore("auto_accelerate_pages", $wait_time, $stale_if_this_many_seconds_old = 20)) {
			$this->utils->debug($debug, "<pre class='admin'>");
			$this->api->pegasaas_limits_check();

			$accelerated_pages = get_option("pegasaas_accelerated_pages", array());
			if ($critical_logging) {
				$this->utils->log("PegasaasAccelerator::auto_accelerate_pages size of accelerated_pages: ".sizeof($accelerated_pages). " -- ".print_r($accelerated_pages, true), "critical");
			}		
		
			
			// this is where we unaccelerate all pages

			// no longer checking howmany pages accelerated vs limits, in this mechanism, as it can un-accelerate pages if we go over the limit by one or two due to 
			// the auto-accelerating of categories through the "auto_accelerate()" method.
			//	if (sizeof($accelerated_pages) > PegasaasAccelerator::$settings['limits']['maximum_page_accelerations_available'] || (isset( $_GET['reset'])&&$_GET['reset'] == 1) || $_POST['c'] == "re-init-accelerated-pages" || $reset) {
			if ((isset( $_GET['reset'])&&$_GET['reset'] == 1) || $_POST['c'] == "re-init-accelerated-pages" || $reset) {
					if ($critical_logging) {
					$this->utils->log("Sizeof(accelerated_pages): ".sizeof($accelerated_pages), "critical");
					$this->utils->log("limits[max_page_accelerations_available]: ". PegasaasAccelerator::$settings['limits']['maximum_page_accelerations_available'], "critical");
					$this->utils->log("GET[reset]: ". $_GET['reset'], "critical");
					$this->utils->log("POST[c]: ". $_POST['c'], "critical");
					$this->utils->log("reset: ". $reset, "critical");
				}
				$reload_accelerator_overrides = true;
				foreach ($accelerated_pages as $post_id => $page_info) {
					unset($accelerated_pages["$post_id"]);
					$this->utils->debug($debug, "Removing $post_id\n");
				
					$page_level_settings = PegasaasUtils::get_object_meta($post_id, "accelerator_overrides");
					unset($page_level_settings['accelerated']); // we unset it, so that it can be auto accelerated in the next foreach block
					$this->utils->update_object_meta($post_id, "accelerator_overrides", $page_level_settings);
				}
				$this->utils->log("delete_option('pegasaas_accelerated_pages') -- PegasaasAccelerator::auto_accelerate_pages('{$reset}')", "data_structures");
				$this->utils->log("delete_option('pegasaas_accelerated_pages') -- ".sizeof($accelerated_pages)."/".$_POST['c']."/".$_GET['reset'], "data_structures");

				delete_option("pegasaas_accelerated_pages");
			}
			
			$accelerated_pages = get_option("pegasaas_accelerated_pages", array());
			$start_size 	   = sizeof($accelerated_pages);

			$total_accelerated = 0;
			foreach ($accelerated_pages as $post_id => $page_info) {
				$page_level_settings = PegasaasUtils::get_object_meta($post_id, "accelerator_overrides");
				if ($page_level_settings['accelerated'] == 1) {
					$total_accelerated++;
				}
			}
			
			$this->utils->debug($debug, "Status: # of accelerated pages $total_accelerated\n");
		
			
			if ($total_accelerated < PegasaasAccelerator::$settings['limits']['maximum_page_accelerations_available']) {
			
				$this->utils->debug($debug, "Fewer Accelerator Pages than Limit \n");
				$this->utils->debug($debug, "POST['acceleration-type'] == {$_POST['acceleration-type']} \n");

				// get list of pages and/or posts
				// $_POST['acceleration-type'] is specified within the setup wizard
				
				if ($_POST['acceleration-type'] == "page") {
					PegasaasUtils::$all_pages_and_posts = array();
					$this->data_storage->unset_object("all_pages_and_posts");

					$this->set_home_page_accelerated(1, true);
					$accelerated_pages["/"] = 1;
					$total_accelerated++;
					
					// ensure categories are disabled
					$this->set_categories_accelerated(0, true);
				
					$posts_array = $this->utils->get_all_pages_and_posts();
					$accelerate_limit = $_POST['page-count'];
					$accelerate_limit = PegasaasAccelerator::$settings['limits']['maximum_page_accelerations_available'];
					$disable_acceleration_on_remaining = true;
				
				} else if ($_POST['acceleration-type'] == "post") {
					$this->set_home_page_accelerated(1, true);
					$accelerated_pages["/"] = 1;
					$total_accelerated++;

					// ensure categories are disabled
					$this->set_categories_accelerated(0, true);
					PegasaasUtils::$all_pages_and_posts = array();
					$this->data_storage->unset_object("all_pages_and_posts");

					$posts_array = $this->utils->get_all_pages_and_posts();
					$accelerate_limit = $_POST['post-count'];
					$accelerate_limit = PegasaasAccelerator::$settings['limits']['maximum_page_accelerations_available'];
					$disable_acceleration_on_remaining = true;
					
				} else if ($_POST['acceleration-type'] == "advanced") {
				
					$this->set_home_page_accelerated(1, true);
					$accelerated_pages["/"] = 1;
					$total_accelerated++;

					// ensure categories are disabled
					$this->set_categories_accelerated(0, true);
					PegasaasUtils::$all_pages_and_posts = array();
					$this->data_storage->unset_object("all_pages_and_posts");

					$posts_array = $this->utils->get_all_pages_and_posts();
					$accelerate_limit = PegasaasAccelerator::$settings['limits']['maximum_page_accelerations_available'];
					$disable_acceleration_on_remaining = true;		

					
					
				} else if ($_POST['acceleration-type'] == "all") {
					PegasaasUtils::$all_pages_and_posts = array();
					$this->data_storage->unset_object("all_pages_and_posts");
					$this->set_home_page_accelerated(1, true);
					$accelerated_pages["/"] = 1;
					$total_accelerated++;

					$category_resource_ids = $this->set_categories_accelerated(1, true);
					
					foreach ($category_resource_ids as $rid) {
						$accelerated_pages["{$rid}"] = 1;
						$total_accelerated++;
					}

					$posts_array 		= PegasaasUtils::get_all_pages_and_posts();
					
					$accelerate_limit 	= $_POST['all-count'];
					$accelerate_limit = PegasaasAccelerator::$settings['limits']['maximum_page_accelerations_available'];

					
					// if the submitted accelerated # is the number of pages in the site, then allow auto acceleration
					// on any new pages
					if ($accelerate_limit >= sizeof($posts_array)) {
						$disable_acceleration_on_remaining = false;
					} else {
						$disable_acceleration_on_remaining = true;
					}
					
				} else if ($_POST['acceleration-type'] == "home-page") {
					$this->set_home_page_accelerated(1, true);
					$accelerated_pages["/"] = 1;
					$total_accelerated++;

					$this->set_categories_accelerated(0, true);
					PegasaasUtils::$all_pages_and_posts = array();
					
					$posts_array 		= $this->utils->get_all_pages_and_posts();
					$accelerate_limit 	= 1;
					$disable_acceleration_on_remaining = true;

					
				} else if ($_POST['acceleration-type'] == "manual") {
					$this->set_home_page_accelerated(0, true);
					// ensure categories are disabled
					$this->set_categories_accelerated(0, true);
					PegasaasUtils::$all_pages_and_posts = array();
					
					$posts_array 		= $this->utils->get_all_pages_and_posts();
					$accelerate_limit 	= 0;
					$disable_acceleration_on_remaining = true;

				} else {
					$accelerate_limit = PegasaasAccelerator::$settings['limits']['maximum_page_accelerations_available'];
					$posts_array = $this->utils->get_all_pages_and_posts();
					$disable_acceleration_on_remaining = false;
				}
				
				
				
				$this->utils->debug($debug, "Size of posts array: ".(sizeof($posts_array))."\n");
				$this->utils->debug($debug, "disable_acceleration_on_remaining == {$disable_acceleration_on_remaining} \n");
				$this->utils->debug($debug, "accelerate_limit == {$accelerate_limit} \n");

				if ($debug) {
					print "<pre>AcceleratedPages\n";
					var_dump($accelerated_pages);
					print "</pre>";

					print "<pre>Posts\n";
					var_dump($posts_array);
					print "</pre>";

				}

				// while the size of accelerated_pages < the limit, and there are more pages yet unaccelerated
				foreach ($posts_array as $pid => $post) {

						$resource_id = $post->resource_id; 
						
						$this->utils->debug($debug, "check to see if we should accelerate this item: {$post_id}\n\n");
						$post_acceleration_status = true;

						if ($disable_acceleration_on_remaining) {
							if ($_POST['acceleration-type'] == "home-page" && $post->resource_id != "/") {
								
								$post_acceleration_status = false;
								$this->utils->debug($debug, "Home page is not '/' because it is {$post->resource_id}\n\n");

							
							} else if ($_POST['acceleration-type'] == "page" && $post->post_type != "page") {
								$post_acceleration_status = false;
							} else if ($_POST['acceleration-type'] == "post" && $post->post_type != "post") {
								$post_acceleration_status = false;
							} else if ($_POST['acceleration-type'] == "advanced") {
								// test if master checkbox is checked
								
								if (!PegasaasUtils::is_in_list($post->slug, $_POST['enable_acceleration_on'], true)){
								 	$post_acceleration_status = false;
									
	
								} 

								
							} 
						}

						// if we have exceeded the allowable accelerations for this subscription, set the acceleration status to 0
						if ($total_accelerated >= $accelerate_limit && $disable_acceleration_on_remaining) {
							$post_acceleration_status = false;
							
						}
						if ($critical_logging) {
							$this->utils->log("Total Accelerated So Far: $total_accelerated", "critical" );
						}	
						if ($this->is_excluded_url($post->slug)) {
							$post_acceleration_status = false;
							
						}


						if (array_key_exists($resource_id, $accelerated_pages)) {
							
							$this->utils->debug($debug, "Status ({$resource_id}): Page Already Accelerated {$resource_id}\n");
							continue; 

						} else {
							
							$this->utils->debug($debug, "Status ({$resource_id}): Page Not Yet Accelerated {$resource_id}\n");
							if ($critical_logging) {
								$this->utils->log("PegasaasAccelerator::auto_accelerate_pages()[3986] '{$resource_id}' page not yet accelerated: ".sizeof($accelerated_pages), "critical");
							}	
							if ($post_acceleration_status == true) {
								$this->utils->debug($debug, "Post Acceleration Status is TRUE\n");
								if ($critical_logging) {
									$this->utils->log("PegasaasAccelerator::auto_accelerate_pages()[3986] '{$resource_id}'/'$pid' Post Acceleration Status is TRUE");
								}	
								$total_accelerated++;
								$accelerated_pages["{$resource_id}"] = 1;
								if (!is_object(PegasaasUtils::$all_pages_and_posts["$pid"])) {
									PegasaasUtils::$all_pages_and_posts["$pid"] = new stdClass();
								}
								PegasaasUtils::$all_pages_and_posts["$pid"]->accelerated = true;
							
								
							} else if ($disable_acceleration_on_remaining) {
								$this->utils->debug($debug, "Post Acceleration Status is FALSE and DISABLE_ACCELERATION_ON_REMAINING is TRUE\n");
								if ($critical_logging) {
							//		$this->utils->log("PegasaasAccelerator::auto_accelerate_pages()[3986] '{$resource_id}'/'$pid'' Post Acceleration Status is FALSE", "critical");
								}	
								// disabled this line on June 8, 2021
								$accelerated_pages["{$resource_id}"] = 0;
								
								PegasaasUtils::$all_pages_and_posts["$pid"]->accelerated = false;
							}
							/*
							if (!$post_acceleration_status) {
								$this->utils->debug($debug, "Clearing {$pid} from all_pages_and_posts array\n");

								//$reset_all_pages_and_posts = true;
								
								unset($this->utils->all_pages_and_posts["$pid"]);
							
							}
							*/


							$page_level_settings 			 = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides");
							if (!$post_acceleration_status) { 
								$post_acceleration_status = 0;
							}
							
							if (!array_key_exists("accelerated", $page_level_settings)) {
								//print "accelerating $post_acceleration_status\n";
								$page_level_settings['accelerated'] = $post_acceleration_status;
								$this->utils->update_object_meta($resource_id, "accelerator_overrides", $page_level_settings);
								$this->utils->debug($debug, "Status: Setting Acceler Status {$resource_id} -- {$post_acceleration_status}\n");
								$reload_accelerator_overrides = true;
								if ($critical_logging) {
							//		$this->utils->log("PegasaasAccelerator::auto_accelerate_pages()[4032] '{$resource_id}'/'$pid'' page level settings SAVED", "critical");
								}
							} else {
								$this->utils->debug($debug, "Status: Apparently 'accelerated' status already exists ".print_r($page_level_settings, true)."\n");
								if ($critical_logging) {
								//	$this->utils->log("PegasaasAccelerator::auto_accelerate_pages()[4032] '{$resource_id}'/'$pid'' page level settings NOT SAVED", $critical);
								}
							}

						}

						if ($total_accelerated >= $accelerate_limit && !$disable_acceleration_on_remaining ) {
							$this->utils->debug($debug, "Status: LIMIT of Accelerated pages (".PegasaasAccelerator::$settings['limits']['maximum_page_accelerations_available'].") < ".sizeof($accelerated_pages)."\n");
							break;
						}
					
				}
		
				
						if (sizeof($this->utils->all_pages_and_posts) > 1000) {
		    $lifetime = 600; // 10 minutes
		
		} else if (sizeof($this->utils->all_pages_and_posts) > 500) {
			$lifetime = 300; // 5 minutes			
		
		} else if (sizeof($this->utils->all_pages_and_posts) > 250) {
			$lifetime = 120; // 2 minutes
		} else {
			$lifetime = 60; // 1 minute
		}
				
		$this->data_storage->set("all_pages_and_posts", PegasaasUtils::$all_pages_and_posts, $lifetime);
		
				
				$end_size = sizeof($accelerated_pages);
			//	$this->utils->log("Updated Pegasaas 'accelerated_pages' with array of size {$end_size} -- auto_accelerate_pages");
				$this->utils->log("update_option('pegasaas_accelerated_pages', Array({$end_size}) -- auto_accelerate_pages", "data_structures");

				update_option("pegasaas_accelerated_pages", $accelerated_pages, false);
				if ($critical_logging) {
						$this->utils->log("update_option('pegasaas_accelerated_pages', Array({$end_size}) -- auto_accelerate_pages", "critical");

						$this->utils->log("PegasaasAccelerator::auto_accelerate_pages SET accelerated_pages: ".sizeof($accelerated_pages), "critical");
				}				
				$this->utils->debug($debug, "Status: Start Size {$start_size} and end size {$end_size}");

				if ($start_size != $end_size) { 
					$this->set_accelerated_pages();
				}
			} else {
				if ($critical_logging) {
						$this->utils->log("PegasaasAccelerator::auto_accelerate_pages NOT SET accelerated_pages (".PegasaasAccelerator::$settings['limits']['maximum_page_accelerations_available'].") < ".sizeof($accelerated_pages), "critical");
				}			
				
				$this->utils->debug($debug, "Status: LIMIT of Accelerated pages (".PegasaasAccelerator::$settings['limits']['maximum_page_accelerations_available'].") < ".sizeof($accelerated_pages)."\n", "critical");
			}
			
			$this->utils->debug($debug, "Status: # of accelerated pages ".sizeof($accelerated_pages)."\n");
			$this->utils->debug($debug, "</pre>");
			
		 	$this->utils->release_semaphore("auto_accelerate_pages");
		}
		
		if ($critical_logging) {
				$this->utils->log("PegasaasAccelerator::auto_accelerate_pages size of accelerated_pages: ".sizeof($accelerated_pages), "critical");
		}
		if ($critical_logging) {
			$this->utils->log("AUTO ACCELERATE_PAGES END", "critical");
		}

	} 
	
	
	
	static function needs_pages_auto_accelerated() {
		if (isset($_GET['page']) && $_GET['page'] == "pegasaas-accelerator") {
			return true;
		} else {
			return false;
		}
	}
	
	
	function in_debug_mode($mode) {
		if (isset($_GET['pegasaas_debug']) && strstr($_GET['pegasaas_debug'], $mode) !== false) {
			return true;
		}
		return false;
	}
	



	
	function clear_stale_requests() {
		global $pegasaas;
		global $test_debug;
		$debug = false;
		if (isset($test_debug) && $test_debug == true) {
			$debug = true;
		}
		if ($debug) {
			PegasaasUtils::log_benchmark("Clear Stale Request START", "debug-li", 1);
		}
		
		$stale_time = mktime(date("H") - 1);
		
		$all_pagespeed_benchmark_requests = $pegasaas->db->get_results("pegasaas_api_request", array("request_type" => "pagespeed-benchmark"));
		if (is_array($all_pagespeed_bechmark_requests)) {
			$stale_time = $stale_time - sizeof($all_pagespeed_bechmark_requests); // an hour less one request/minute
		}
		

		
		$possible_stale_time_date = date("Y-m-d H:i:s", $stale_time);
		
		if ($debug) {
			PegasaasUtils::log_benchmark("Stale Time: {$possible_stale_time_date}", "debug-li", 1);
		}		
		
		$pegasaas->db->delete_comparison("pegasaas_api_request", array("request_type" => "pagespeed-benchmark", "time" => array("value" => $possible_stale_time_date, "comparison" => "<")));

		$stale_time = mktime(date("H") - 1);
		$all_pagespeed_requests = $pegasaas->db->get_results("pegasaas_api_request", array("request_type" => "pagespeed"));
		if (is_array($all_pagespeed_requests)) {
			$stale_time = $stale_time - sizeof($all_pagespeed_requests); // an hour less one request/minute	 
		}
		
		$possible_stale_time_date = date("Y-m-d H:i:s", $stale_time);
		$pegasaas->db->delete_comparison("pegasaas_api_request", array("request_type" => "pagespeed", "time" => array("value" => $possible_stale_time_date, "comparison" => "<")));

		$stale_time = mktime(date("H") - 1);
		$all_optimization_requests = $pegasaas->db->get_results("pegasaas_api_request", array("request_type" => "optimization-request"));
		if (is_array($all_optimization_requests)) {
			$stale_time = $stale_time - sizeof($all_optimization_requests); // an hour less one request/minute
		}
		
		$possible_stale_time_date = date("Y-m-d H:i:s", $stale_time);
		$pegasaas->db->delete_comparison("pegasaas_api_request", array("request_type" => "optimization-request", "time" => array("value" => $possible_stale_time_date, "comparison" => "<")));
		

		$stale_time = mktime(date("H") - 6);
		$possible_stale_time_date = date("Y-m-d H:i:s", $stale_time);
		$pegasaas->db->delete_comparison("pegasaas_queued_task", array("time" => array("value" => $possible_stale_time_date, "comparison" => "<")));
	
		
		
		if ($debug) {
			print "</pre>";
		}
	}

	
	function get_home_path() {
		
		
        $home    = set_url_scheme( get_option( 'home' ), 'http' );
		
        $siteurl = set_url_scheme( get_option( 'siteurl' ), 'http' );
		

        if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {
			
				$home_path_info = parse_url($home);
				$site_path_info = parse_url($siteurl);
		

				$wp_path_rel_to_home = str_ireplace(@$home_path_info['path'], '', @$site_path_info['path']);
	
			
			
				$pos = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );
				
			// if we are not in the folder that contains the wordpress files (wp-admin, wp-includes)
			// then we should assume the current folder is the home folder
			if (!$pos) {
				$pos = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), "/" );
			} 
			
			$home_path = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );
				$home_path = str_replace("/wp-admin", "/", $home_path);
				$home_path = trailingslashit( $home_path );

	        } else {
		
	              $home_path = ABSPATH;
			}
		
		
	        return str_replace( '\\', '/', $home_path );	
			
	}
	function get_home_dir() {
		$home = get_option( 'home' );
		//$home = "https://testing.pegasaas.com/subfolder";
		$home_data = parse_url($home);
		return $home_data['path'];
	}
	
	static function get_home_url($path = '', $scheme = null, $protocol = false) {
        $home    = set_url_scheme( get_option( 'home' ), $scheme );
		list( $home ) = explode( '?', $home );
		
		$home = rtrim($home, "/");
		
	    return $home;	
			
	}
	
	function get_content_url($path = '', $scheme = null, $protocol = false) {
		$home = trim($this->get_home_url($path, $scheme, $protocol), '/');
		//$home_path = $this->get_home_path();

	//	$content_folder = str_replace($home_path, '', WP_CONTENT_DIR);
		$content_folder = pegasaas_get_content_folder();
		$content_folder = trim($content_folder, '/');
	
		return trailingslashit($home).$content_folder;
	}
	
	
	function disable($uninstall = false) {
	
	  	if (PegasaasAccelerator::$settings['api_key'] != "") {
			if ($uninstall) {
				$response = $this->api->post(array('change_status' => -1));
			} else {
				$response = $this->api->post(array('change_status' => 0));
			}
			$api_key = PegasaasAccelerator::$settings['api_key'];
			
			$data = json_decode($response, true);

			if ($data['api_error'] != "") {
				PegasaasAccelerator::$settings['last_api_check'] = time();
				PegasaasAccelerator::$settings['error'] = "Unable to connect to pegasaas API Key Server";
				$response = json_encode(array("status" => -2, "reason" => "Unable to connect to API Key Server"));
			} else {

				if ($data['status'] == 1) {
					
					PegasaasAccelerator::$settings = $data;
					PegasaasAccelerator::$settings['api_key'] = $api_key;
					PegasaasAccelerator::$settings['last_api_check'] = time();
								
				} else if ($data['status'] == 0) {
					PegasaasAccelerator::$settings = $data;
				  PegasaasAccelerator::$settings['status'] = 0;	
				  PegasaasAccelerator::$settings['api_key'] = $api_key;
				  PegasaasAccelerator::$settings['last_api_check'] = time();
			 
					
				} else {
					PegasaasAccelerator::$settings['status'] = -1;
					PegasaasAccelerator::$settings['api_key'] = "";
		
				}
			} 
			
			$this->set_gzip(false);
			$this->set_browser_caching(false);
			$this->set_benchmarker(false);
			$this->set_caching(false);
			$this->set_mod_pagespeed(false);			
			
			update_option("pegasaas_settings", PegasaasAccelerator::$settings);
			delete_option("pegasaas_development_mode");		
		

		}
	}
	
	function uninstall() {
		$api_key = get_option("pegasaas_deactivated_api_key", "");
		if ($api_key != "") {
			PegasaasAccelerator::$settings['api_key'] = $api_key;
		}
		
		// remove cron variables
		PegasaasCron::clear_cron_events();

		// clear local cache
		$this->cache->clear_pegasaas_file_cache();
		
		// communicate to the api that we are disabling
		$this->disable($uninstall = true);
		
		// clear local variables
		$this->clear_pegasaas_variables();	
		
		delete_option("pegasaas_deactivated_api_key");
		
	}
	

	function upgrade_completed($upgrader_object, $options = array()) {

		
		$upgraded_plugin = $upgrader_object->result['destination_name']; // the plugin being updated
		
		$current_plugin_path_name = plugin_basename($this->get_plugin_file()); // our plugin name
		if ($upgrader_plugin != "" && strstr($current_plugin_path_name, $upgrader_plugin)) { 

			// assert caching instructions
			$this->set_caching(PegasaasAccelerator::$settings['settings']['page_caching']['status'], $force_write = true);
			
			$plugin_version = $this->get_current_version();
			// record when updated
			$update_history = get_option("pegasaas_plugin_update_history", array());
			$update_history[] = array('when_updated' => time(),
									  'version' => $plugin_version);
			update_option("pegasaas_plugin_update_history", $update_history);
		} 
	
	}
	
	
	function submit_optimization_request($resource_id, $buffer = "", $test_submission = false) {
		global $pegasaas;
		global $test_debug;
		

		if ($test_debug) {
			PegasaasUtils::log_benchmark("Submit Optimization Request: Start ($resource_id) ($test_submission)", "debug-li", 1); 

		}

		if ($test_submission && isset($_SERVER['HTTP_X_PEGASAAS_DEBUG'])) {
			if (PegasaasAccelerator::$settings['api_key'] == "" || $_SERVER['HTTP_X_PEGASAAS_DEBUG'] == PegasaasAccelerator::$settings['api_key']) {
				$debug = true;
			}
		}
		$debug = false;
	
		
		if ($url == "") {
			$wordpress_location = $this->utils->get_wp_location();
			
			$url = $_SERVER['REQUEST_URI'];
			$url = preg_replace('/'.preg_quote($wordpress_location, "/").'/', "", $url, 1);
		}
		
		if ($url == $this->get_home_url()) {
			$url = "/";
		} 
		
		
		// required in order to properly map URL for non-latin characters so that
		// caching read/write can occur - January 29, 2019
		
		
		if (strstr($url, "wp-admin") && !$test_submission) { 

			$this->utils->log("Request optimize html url wp-admin detected -- existing");
			return false;
		}		
		
	
  		if (PegasaasAccelerator::$settings['api_key'] != "" || $test_submission) {
			  
			if ($test_debug) {
				PegasaasUtils::log_benchmark("Submit Optimization Request: API Key Not Blank or is Test Submission", "debug-li", 1); 

			}
			
			$api_key = PegasaasAccelerator::$settings['api_key'];
			
			$url = str_replace("?accelerator=off", "", $url);
			
			// strip wp rocket critical css 
			$buffer = $this->utils->strip_wprocket_critical_css($buffer);
			
		 
			//return false;
			$location = $this->utils->get_wp_location();

			if (!$this->db->has_record("pegasaas_api_request", array("resource_id" => $resource_id, "request_type" => "optimization-request"))) {
				
				
				$page_settings 		  = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides");
				if ($pegasaas->utils->does_plugin_exists_and_active("geotargetingwp")) {
					$page_settings['geo_modifier'] = $this->cache->get_cache_geo_modifier();
				}
				$content_url 		  = $this->get_content_url();
				$content_url_alt 	  = content_url(); // in multi-site, this can be the non aliased url
				$home_url 			  = home_url();
				$home_url_conditioned = $this->get_home_url();
				$theme_info 		  = wp_get_theme();
				$plugin_uri 		  = plugin_dir_url($this->get_plugin_file());
				$nginx_404_handling   = $this->utils->nginx_404_handling();
				
				if ($_SERVER['HTTP_X_PEGASAAS_PRIORITY_OPTIMIZATION'] == 3) {
					$request_priority = 3;
				} else if ($_SERVER['HTTP_X_PEGASAAS_PRIORITY_OPTIMIZATION'] == 2) {
					$request_priority = 2;
				} else if ($_SERVER['HTTP_X_PEGASAAS_PRIORITY_OPTIMIZATION'] == 1) {
					$request_priority = 1;
				} else {
					
					
					$request_priority = 0;
				}
				
				if ($page_settings['prioritized'] == 1) {
					$request_priority++; // elevate request priority
				}
				
				//print "request_priority: {$request_priority}";
				//exit;
				
				global $wp_scripts;
				$scripts = array();
				foreach ($wp_scripts->queue as $script_handle) {
					
					$script 	= $wp_scripts->registered["$script_handle"];
					
					$script_src = $script->src;
					$scripts["$script_handle"] = array("before" => $script->extra['before'],
													"after" => $script->extra['after'],
													'handle' => $script_handle,
													'src' => $script_src,
													'deps' => $scriptj->deps);
				}
	
				if (defined("SUBDOMAIN_INSTALL")) {
					$multisite_variation = "subdomain";
				} else {
					$multisite_variation = "subdirectory";
				}
				 
				
				$site_configurations = array("content_url" => $content_url, 
											 "content_url_alt" => $content_url_alt, 
											 "content_dir" => WP_CONTENT_DIR,
											 "cache_folder" => PEGASAAS_CACHE_FOLDER,
											 "home_url" => $home_url, 
											 "theme_name" => $theme_info->get("Name"),
											 "home_url_conditioned" => $home_url_conditioned,
											 "http_host" 		=> $this->utils->get_http_host(), 
											 "http_host_alt" 	=> $this->utils->get_http_host_alt(),
											 "https" 			=> $_SERVER['HTTPS'],
											 "request_uri" 		=> $_SERVER['REQUEST_URI'],
											 "document_root" 	=> $_SERVER['DOCUMENT_ROOT'],
											 "home_path" 		=> $this->get_home_path(),
											 "abspath" 			=> ABSPATH,
											 "server_ip" 		=> $_SERVER['SERVER_ADDR'],
											 "plugin_uri" 		=> $plugin_uri,
											 "script_metadata"	=> $scripts,
											 "is_multisite" 	=> is_multisite(),
											 "multisite_variation" => $multisite_variation,
											 "nginx_404_handling" => ($nginx_404_handling ? 2 : 0),
											 "preload_user_files" => array("resources" => PegasaasAccelerator::$settings['settings']['preload_user_files']['resources']),
											 "dns_prefetch" => array("additional_domains" => PegasaasAccelerator::$settings['settings']['dns_prefetch']['additional_domains']),
											 "essential_css" => array("css" => PegasaasAccelerator::$settings['settings']['essential_css']['css']),
											 "strip_google_fonts_mobile" => array("custom_fonts" => PegasaasAccelerator::$settings['settings']['strip_google_fonts_mobile']['custom_fonts']),
											 "lazy_load_scripts" => array("additional_scripts" => PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['additional_scripts'],
																		  "custom_scripts" => PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']
																		  ),
											 "lazy_load" => array("exclude_urls" => PegasaasAccelerator::$settings['settings']['lazy_load']['exclude_urls']),
											 "auto_size_images" => array("exclude_images" => PegasaasAccelerator::$settings['settings']['auto_size_images']['exclude_images']),
											 
											 "defer_unused_css" => array("exclude_stylesheets" => PegasaasAccelerator::$settings['settings']['defer_unused_css']['exclude_stylesheets']),
											 "defer_render_blocking_js" => array("exclude_scripts" => PegasaasAccelerator::$settings['settings']['defer_render_blocking_js']['exclude_scripts']),
											 "defer_render_blocking_css" => array("exclude_stylesheets" => PegasaasAccelerator::$settings['settings']['defer_render_blocking_css']['exclude_stylesheets']),
											 "image_optimization" => array("exclude_images" => PegasaasAccelerator::$settings['settings']['image_optimization']['exclude_images'],
																		   "auto_size_images" => PegasaasAccelerator::$settings['settings']['image_optimization']['auto_size_images'],
																		   "exclude_images_from_auto_sizing" => PegasaasAccelerator::$settings['settings']['image_optimization']['exclude_images_from_auto_sizing']),
											 "lazy_load_images" => array("exclude_images" => PegasaasAccelerator::$settings['settings']['lazy_load_images']['exclude_images']),
											 "lazy_load_background_images" => array("exclude_images" => PegasaasAccelerator::$settings['settings']['lazy_load_background_images']['exclude_images'])
				
											);
				if (!$test_submission) { 
					// assert the server IP, locally, so that it is synchronized with the API
					$this->api->assert_server_ip($_SERVER['SERVER_ADDR']);
				}
				if ($debug || $test_debug) {
					print "\nSite Configurations\n";
					var_dump($site_configurations);
					print "</pre>";
				}
				if (!$test_submission) {
					$request_id = $this->record_optimization_request($resource_id);
				}
				
				$post_fields = array();
				$post_fields['api_key'] 			= $api_key;
				$post_fields['domain'] 				= $_SERVER["HTTP_HOST"];
				if (PegasaasUtils::wpml_multi_domains_active()) {
					$website_address = $pegasaas->get_home_url();
					$address_details = parse_url($website_address);
					$website_address = $address_details['scheme']."://".$_SERVER['HTTP_HOST'];
				} else {
					$website_address = $pegasaas->get_home_url();
				}
			
			
	
				$post_fields['url']					= $website_address.$url;
				$post_fields['version']				= 1;
				$post_fields['command']				= "submit-optimization-request";
				if ($test_submission) {
					$post_fields['test_submission'] = true;
				}				
				
				
				$post_fields['page_settings'] 		= json_encode($page_settings);
				$post_fields['site_configurations'] = json_encode($site_configurations);
				$post_fields['request_priority'] 	= $request_priority;
				//$post_fields['callback_url'] 		= ($_SERVER['HTTPS'] == "on" ? "https://" : "http://").$this->utils->get_http_host().$location."/wp-admin/admin-ajax.php?wp_rid=".$resource_id."&request_id={$request_id}"; 
				$post_fields['callback_url'] 		= admin_url( 'admin-ajax.php' )."?wp_rid=".$resource_id."&request_id={$request_id}"; 
				
				// if the buffer is large than 4MB, then this request is unusually large and should be truncated
				if (strlen($buffer) > 4194304) {
					$buffer = "";
					$post_fields['buffer_truncated'] = true;
				}
				
				$post_fields['buffer']				= $buffer; 
				
				
				$blocking = $this->api->is_submission_method_blocking();
				$timeout  = $this->api->get_optimization_timeout();

				$window = PegasaasAccelerator::$settings['settings']['api_submit_method']['non_blocking_window']  * 60;
				/* 
				// for testing blocking/non-blocking system
				if (!$blocking) {
					$post_fields['do_not_store_submission'] = true;
				}
				*/
				$response = json_decode($this->api->post($post_fields, 
														 array('timeout' => $timeout, 
															   'blocking' => $blocking)),
									   true);

				if ($debug) { 
					print "\nPost Fields x\n";
					var_dump($post_fields);
					print "\n";
					
					print "\nResponse\n";
					var_dump($response);
					print "\n";
					
					print "Blocking: ".$blocking."\n";
					print "Submission Method Variable: ".($pegasaas->api->is_submission_method_variable())."\n";
					print "Timeout: $timeout \n";
					print "Non Blocking Window: $window \n";
					$window_ends = date("Y-m-d H:i:s", get_option("pegasaas_accelerator_api_non_blocking_until"));
					print "Window ends: ".$window_ends." \n"; 
					print "Current Time: ".date("Y-m-d H:i:s")." \n";
				} 
				if ($test_submission) {
					header("Content-type: text/plain", true); // to ensure services like Ezoic don't append code to the request
				}		
				
				// optimization request submitted with confirmation
				if (isset($response['optimization_request_status']) && $response['optimization_request_status'] == 1) {
					return 1;
				
				// optimization request was submitted without confirmation
				} else if (isset($response['optimization_request_status']) && $response['optimization_request_status'] == 2) {
					return 1;
					
				// optimization request submission failed
				} else {
					// delete out the pending api request, if the submission to the API failed.
					$this->db->delete("pegasaas_api_request", array("resource_id" => $resource_id, "request_type" => "optimization-request"));
					return 0;
				}
			} else { 
				
				if ($test_debug) {
					PegasaasUtils::log_benchmark("Submission Already Exists", "debug-li", 1); 

				}
				return 2;
			}			
		} 
		
		return -1;
				
		
	}


	
	
	function record_optimization_request($resource_id) {
		global $pegasaas;
		
		PegasaasAccelerator::$settings["limits"]['monthly_optimizations_remaining']--;
		update_option("pegasaas_settings", PegasaasAccelerator::$settings);
		
		if ($pegasaas->hermes->should_send_limit_notification()) {
			$pegasaas->hermes->send_approaching_limit_notification();
		}
		
		if ($_SERVER['HTTP_X_PEGASAAS_MANUAL_REQUEST'] == 1) {
			$advisory = "manual";
		} else {
			$advisory = "";
		}
		
		$request_id = $pegasaas->db->add_record("pegasaas_api_request", array("resource_id" => $resource_id, 
																			  "request_type" => "optimization-request", 
																			  "time" => date("Y-m-d H:i:s"),
																			  "advisory" => $advisory));
		return $request_id;
	}
	

	
	function set_init_notification($stage) {
		update_option("pegasaas_init_notification", $stage);
	}
	
	function enable($in_staging_mode = false) {
		
		PegasaasHtaccess::assert_htaccess_conditioned();
		
	  	if (PegasaasAccelerator::$settings['api_key'] != "") {
			$api_key = PegasaasAccelerator::$settings['api_key'];
			$post_fields = array("change_status" => 1);
			$response = $this->api->post($post_fields);
			
			if ($response == "") {
				PegasaasAccelerator::$settings['last_api_check'] = time();
				PegasaasAccelerator::$settings['error'] = "Unable to connect to pegasaas API Key Server";
				$response = json_encode(array("status" => -2, "reason" => "Unable to connect to API Key Server"));
			} else {
				$data = json_decode($response, true);

				if ($data['status'] == 1) {
					PegasaasAccelerator::$settings = $data;
					PegasaasAccelerator::$settings['api_key'] = $api_key;
					PegasaasAccelerator::$settings['last_api_check'] = time();
								
				} else if ($data['status'] == 0) {
				  PegasaasAccelerator::$settings = $data;
				  PegasaasAccelerator::$settings['status'] = 0;	
				  PegasaasAccelerator::$settings['api_key'] = $api_key;
				  PegasaasAccelerator::$settings['last_api_check'] = time();
			  
				} else {
					PegasaasAccelerator::$settings = $data;
					PegasaasAccelerator::$settings['status'] = -1;
					PegasaasAccelerator::$settings['api_key'] = "";		
				}
			}

			$this->set_gzip(PegasaasAccelerator::$settings['settings']['gzip_compression']['status']);
			$this->set_browser_caching(PegasaasAccelerator::$settings['settings']['browser_caching']['status']);
			$this->set_benchmarker(PegasaasAccelerator::$settings['status']);
			$this->set_caching(PegasaasAccelerator::$settings['settings']['page_caching']['status'], $force_write = $in_staging_mode);
			$this->set_mod_pagespeed(PegasaasAccelerator::$settings['status']);
			
			update_option("pegasaas_settings", PegasaasAccelerator::$settings);		
			if (!$in_staging_mode) {
				delete_option("pegasaas_development_mode");			
			}

		} 
		
		
	}
	
	function enable_development_mode($duration = 'one-hour') {
		
	  	if (PegasaasAccelerator::$settings['api_key'] != "") {
			
			$expiration = "";
			if ($duration == '') {
				$duration = 'one-hour';
			}
			
			if ($duration == 'indefinitely') {
				$expiration = -1; // 5 years
				
			} else if ($duration == 'one-hour') {
				$expiration = time() + 3600;
			} else if ($duration == 'two-hours') {
				$expiration = time() + 7200;
			} else if ($duration == 'four-hours') {
				$expiration = time() + 14400;
			} else if ($duration == 'eight-hours') {
				$expiration = time() + 28800;
			} else if ($duration == 'one-day') {
				$expiration = time() + 86400;
			} else if ($duration == 'one-week') {
				$expiration = time() + 604800;
			} else if ($duration == 'one-month') {
				$expiration = time() + 2592000;
			}
			
			if ($expiration != "") {
				update_option('pegasaas_development_mode', $expiration);
			}			
			//print "setting expiration to $expiration";
			$this->enable($in_staging_mode = true);
		}
	}
	

	function enable_diagnostic_mode() {
	  	if (PegasaasAccelerator::$settings['api_key'] != "") {
			$api_key = PegasaasAccelerator::$settings['api_key'];
			$post_fields = array("change_status" => 2);
			$response = $this->api->post($post_fields);
			
			if ($response == "") {
				PegasaasAccelerator::$settings['last_api_check'] = time();
				PegasaasAccelerator::$settings['error'] = "Unable to connect to pegasaas API Key Server";
				$response = json_encode(array("status" => -2, "reason" => "Unable to connect to API Key Server"));
			} else {
				$data = json_decode($response, true);

				if ($data['status'] == 1 || $data['status'] == 0 || $data['status'] == 2) {
					PegasaasAccelerator::$settings = $data;
					PegasaasAccelerator::$settings['api_key'] = $api_key;
					PegasaasAccelerator::$settings['last_api_check'] = time();
								
				} else {
					PegasaasAccelerator::$settings = $data;
					PegasaasAccelerator::$settings['status'] = -1;
					PegasaasAccelerator::$settings['api_key'] = "";		
				}
			}

			$this->set_gzip(PegasaasAccelerator::$settings['settings']['gzip_compression']['status']);
			$this->set_browser_caching(PegasaasAccelerator::$settings['settings']['browser_caching']['status']);
			$this->set_benchmarker(PegasaasAccelerator::$settings['status']);
			$this->set_caching(PegasaasAccelerator::$settings['settings']['page_caching']['status']);
			$this->set_mod_pagespeed(PegasaasAccelerator::$settings['status']);
			
			update_option("pegasaas_settings", PegasaasAccelerator::$settings);		
			delete_option("pegasaas_development_mode");				

		}
	}	
	
	
	
	/*
		Functions that edit the .htaccess: 
			set_caching				get_caching
			set_gzip				get_mod_gzip
			set_benchmarker			get_benchmarker
									get_mod_deflate
									get_mod_expires
			set_browser_caching
	*/
	
	function get_caching() {
		// this will provide a relative path from the root to the content folder (without trailing slash)
		$content_folder = $this->utils->get_content_folder_path();
		$redirect_to_primary_domain = "";
		
		$home_domain = str_replace("https://", "", $this->utils->strip_url_port($this->get_home_url()));
		$home_domain = str_replace("http://", "", $home_domain);
		$home_domain = trim($home_domain, '/');
		$home_domain_data = explode("/", $home_domain);
		$home_domain = $home_domain_data[0];
		
		$https_site = false;
		$wp_location = $this->utils->get_wp_location();
		$rewrite_base = rtrim($wp_location)."/";
		$wpml_multi_domains_active = PegasaasUtils::wpml_multi_domains_active();
		
		// do not push to HTTPS if the home url is not https
		// also do not assert to the home domain if this is multi-site, as it may not correspond to other installations
		if (strstr($this->get_home_url(), "https://") && !is_multisite() && !$wpml_multi_domains_active) {
			$https_site = true;
			
			// handle servers that use %{HTTP:X-Forwarded-Proto} instead of %{HTTPS}
			if ($_SERVER['HTTP_X_FORWARDED_PROTO'] != "") {
				$redirect_to_primary_domain .= "  RewriteCond %{HTTP:X-Forwarded-Proto} !https\n".
							"  RewriteRule (.*) https://%{SERVER_NAME}%{REQUEST_URI} [L,R=301]\n";		
		
			
			// handle servers that use PAGELY hosting
			} else if ($_SERVER['HTTP_X_PAGELY_SSL'] != "") {
				$redirect_to_primary_domain .= "  RewriteCond %{HTTP:X-Pagely-SSL} \"off\"\n".
					"  RewriteRule (.*) https://{$home_domain}{$rewrite_base}\$1 [L,R=301]\n";		
			} else {
				$redirect_to_primary_domain .= "  RewriteCond %{HTTPS} !on\n".
					"  RewriteRule (.*) https://{$home_domain}{$rewrite_base}\$1 [L,R=301]\n";				
			}
		
		}
		
		// do not assert to the home domain if this is multi-site, as it may not correspond to other installations
		if (!is_multisite() && !$wpml_multi_domains_active) {
			$redirect_to_primary_domain .= "  RewriteCond %{HTTP_HOST} !^{$home_domain}$\n".
			"  RewriteRule (.*) ".($https_site ? "https" : "http")."://{$home_domain}/\$1 [L,R=301]\n\n";
		}
		
		$version = $this->get_current_version();
		
		if ($this->in_development_mode()) {
			$development_mode_expiry = get_option("pegasaas_development_mode", 0); // will be a time-date stamp, or -1
			if ($development_mode_expiry == -1) {
				return "# PEGASAAS ACCELERATOR PAGE CACHING START\n".
					"# In Infinite Staging Mode\n".
					"# PEGASAAS ACCELERATOR PAGE CACHING END\n\n"; // we are indefinitely in staging mode, so we will not use the quick file-based caching
			} else {
				$time_string = date("YmdHis", $development_mode_expiry);
				$time_rewrite = "  RewriteCond %{TIME} >{$time_string} \n";
			}

		} else {
			$time_rewrite = "# not in development mode\n";
		}

		$code_block =  "# PEGASAAS ACCELERATOR PAGE CACHING START\n".
			"<IfModule mod_rewrite.c>\n".
			"  RewriteEngine On\n". 
			"  RewriteBase {$rewrite_base}\n".
			"{$redirect_to_primary_domain}";
		
		
		$code_block .= 		
			"  # temp cache handling\n".
			"  RewriteCond %{HTTP:Cookie} !wordpress_logged_in_\n".
			"  RewriteCond %{REQUEST_METHOD} !POST\n".
			"  RewriteCond %{QUERY_STRING} !.+ [OR]\n".
			"  RewriteCond %{QUERY_STRING} gpsi-call\n".
			"  RewriteCond %{HTTP:Cookie} !comment_author_\n".
			"  RewriteCond %{HTTP:Cookie} !wp-postpass\n".
			"  RewriteCond %{HTTP:Cookie} !wp_woocommerce_session\n".
			"  RewriteCond %{HTTP:Cookie} !wpsc_customer_cookie\n".
			"  RewriteCond %{HTTP:Cookie} !edd_items_in_cart\n";
		
		if ($wpml_multi_domains_active) {
			$code_block .= 	"	RewriteCond %{HTTP_HOST} ^{$home_domain}$\n";
		}		
		
		$code_block .= $time_rewrite;

		
		if (PegasaasAccelerator::$settings['settings']['accelerated_mobile_pages']['status'] === 1) {
			$code_block .= "  RewriteCond %{HTTP_USER_AGENT} '!(android|blackberry|googlebot-mobile|iemobile|ipad|iphone|ipod|opera mobile|palmos|webos)' [NC]\n".
				"  RewriteCond %{REQUEST_URI} !amp/$\n";
		}
		
		if ($wpml_multi_domains_active) {
			$code_block .= "  RewriteCond %{DOCUMENT_ROOT}{$content_folder}/pegasaas-cache/{$_SERVER['HTTP_HOST']}/\$1/index-temp.html -f [or]\n".
				"  RewriteCond \"".PEGASAAS_CACHE_FOLDER_PATH."/{$_SERVER['HTTP_HOST']}/\$1/index-temp.html\" -f\n".
				"  RewriteRule ^(.*?)/?$ \"{$content_folder}/pegasaas-cache/{$_SERVER['HTTP_HOST']}/\$1/index-temp.html\" [L]\n\n";			
		
		} else {
			$code_block .=	"  RewriteCond %{DOCUMENT_ROOT}{$content_folder}/pegasaas-cache/\$1/index-temp.html -f [or]\n".
				"  RewriteCond \"".PEGASAAS_CACHE_FOLDER_PATH."/\$1/index-temp.html\" -f\n".
				"  RewriteRule ^(.*?)/?$ \"{$content_folder}/pegasaas-cache/\$1/index-temp.html\" [L]\n\n";
		}
		
		$code_block .=	"  # optimized cache handling\n".
			"  RewriteCond %{HTTP:Cookie} !wordpress_logged_in_\n".
			"  RewriteCond %{REQUEST_METHOD} !POST\n".
			"  RewriteCond %{QUERY_STRING} !.+ [OR]\n".
			"  RewriteCond %{QUERY_STRING} gpsi-call\n".
			"  RewriteCond %{HTTP:Cookie} !comment_author_\n".
			"  RewriteCond %{HTTP:Cookie} !wp-postpass\n".
			"  RewriteCond %{HTTP:Cookie} !wp_woocommerce_session\n".
			"  RewriteCond %{HTTP:Cookie} !wpsc_customer_cookie\n".
			"  RewriteCond %{HTTP:Cookie} !edd_items_in_cart\n";
		
		$code_block .= $time_rewrite;
		
		if (PegasaasAccelerator::$settings['settings']['accelerated_mobile_pages']['status'] === 1) {
			$code_block .= "  RewriteCond %{HTTP_USER_AGENT} '!(android|blackberry|googlebot-mobile|iemobile|ipad|iphone|ipod|opera mobile|palmos|webos)' [NC]\n".
				"  RewriteCond %{REQUEST_URI} !amp/$\n";
		}
		
		if ($wpml_multi_domains_active) {
			$code_block .= "  RewriteCond %{DOCUMENT_ROOT}{$content_folder}/pegasaas-cache/{$_SERVER['HTTP_HOST']}/\$1/index.html -f [or]\n".
				"  RewriteCond \"".PEGASAAS_CACHE_FOLDER_PATH."/{$_SERVER['HTTP_HOST']}/\$1/index.html\" -f\n".
				"  RewriteRule ^(.*?)/?$ \"{$content_folder}/pegasaas-cache/{$_SERVER['HTTP_HOST']}/\$1/index.html\" [L]\n\n";			
		} else {
			$code_block .= "  RewriteCond %{DOCUMENT_ROOT}{$content_folder}/pegasaas-cache/\$1/index.html -f [or]\n".
				"  RewriteCond \"".PEGASAAS_CACHE_FOLDER_PATH."/\$1/index.html\" -f\n".
				"  RewriteRule ^(.*?)/?$ \"{$content_folder}/pegasaas-cache/\$1/index.html\" [L]\n\n";			
		}

			
		$code_block .= "  <IfModule mod_setenvif.c>\n".
			"    # set an environment variable that says this is a cache hit\n".
			"    SetEnvIf Request_URI pegasaas-cache PEGASAAS_CACHE_HIT=true\n".
			"    SetEnvIf Request_URI \"index\\.html\$\" CONTENT_TYPE_HTML=true\n".
			"    SetEnvIf Request_URI \"index-temp\\.html\$\" PEGASAAS_TEMP_CACHE_HIT=true\n".
			"    SetEnvIf PEGASAAS_CACHE_HIT false CONTENT_TYPE_HTML=false\n".		
			
			"    <IfModule mod_headers.c>\n".
			"      # set the header to indicate whether this is a cache hit\n".
			"      Header set \"X-Pegasaas-Cache\" \"MISS\" env=!PEGASAAS_CACHE_HIT&&!PEGASAAS_TEMP_CACHE_HIT\n".
			"      Header set \"X-Pegasaas-Cache\" \"HIT\" env=PEGASAAS_CACHE_HIT\n".
			"      Header set \"X-Pegasaas-Cache\" \"TEMPORARY\" env=PEGASAAS_TEMP_CACHE_HIT\n".
			"    </IfModule>\n".
			"  </IfModule>\n\n".
			"  <IfModule mod_headers.c>\n".
			"    # ensure some server level caching is disabled\n".
			"    Header set \"Cache-Control\" \"private, max-age=0, no-cache\"\n\n".

			"    # set the powered-by header\n".
			"    Header set \"X-Powered-By\" \"Pegasaas Accelerator WP {$version}\"\n\n".
			
			"    # set the header to indicate whether this is a cache hit\n".
			"    Header set \"Content-Type\" \"text/html; charset=UTF-8\" env=CONTENT_TYPE_HTML\n".
			"  </IfModule>\n\n".
			
			"  # instructions to redirect from viewing the HTMLcache file directly\n".
			"  RewriteCond %{THE_REQUEST} pegasaas-cache\n".
			"  RewriteCond %{THE_REQUEST} index.html\n".
			"  RewriteCond %{QUERY_STRING} !.+\n".
			"  RewriteCond %{HTTPS} on\n".
			"  RewriteRule (.*?)wp-content/pegasaas-cache/(.*?)index.html https://%{HTTP_HOST}/$1$2 [L,R=301]\n\n".
			"  RewriteCond %{THE_REQUEST} pegasaas-cache\n".
			"  RewriteCond %{THE_REQUEST} index.html\n".
			"  RewriteCond %{QUERY_STRING} !.+\n".
			"  RewriteCond %{HTTPS} !on\n".
			"  RewriteRule (.*?)wp-content/pegasaas-cache/(.*?)index.html http://%{HTTP_HOST}/$1$2 [L,R=301]\n".
			"</IfModule>\n".
			"# PEGASAAS ACCELERATOR PAGE CACHING END\n\n";
		
		return $code_block;
	}
	


	function get_wp_content_caching() {		
		$rewrite_base = rtrim($this->utils->get_wp_location())."/";
		$code_block =  "# PEGASAAS ACCELERATOR CACHE FOLDER INSTRUCTIONS START\n".
			"# This .htaccess is required for installations that have 404 handling disabled\n".
			"<IfModule mod_rewrite.c>\n".
			"RewriteEngine On\n". 
			"RewriteBase {$rewrite_base}\n".
			"RewriteRule ^index\\.php\$ - [L]\n".
			"RewriteCond %{REQUEST_FILENAME} !-f\n".
			"RewriteCond %{REQUEST_FILENAME} !-d\n".
			"RewriteRule . {$rewrite_base}index.php [L]\n".
			"</IfModule>\n".
			"# PEGASAAS ACCELERATOR CACHE FOLDER INSTRUCTIONS END\n\n";
		
		return $code_block;
	}	
	
	function get_mod_gzip() {
		return "# PEGASAAS ACCELERATOR GZIP START\n".
			"<IfModule mod_gzip.c>\n".
				"  mod_gzip_on Yes\n".
				"  mod_gzip_dechunk Yes\n".
				"  mod_gzip_item_include file .(html?|txt|css|js|php|pl)$\n".
				"  mod_gzip_item_include handler ^cgi-script$\n".
				"  mod_gzip_item_include mime ^text/.*\n".
				"  mod_gzip_item_include mime ^application/x-javascript.*\n".
				"  mod_gzip_item_include mime ^application/ld+json\n".
				"  mod_gzip_item_exclude mime ^image/.*\n".
				"  mod_gzip_item_exclude rspheader ^Content-Encoding:.*gzip.*\n".
			"</IfModule>\n".
			"# PEGASAAS ACCELERATOR GZIP END\n\n";	
	}
	
	function get_mod_pagespeed() {
		return "# PEGASAAS ACCELERATOR MOD_PAGESPEED START\n".
			"<IfModule pagespeed_module>\n".
				"  ModPagespeed off\n".
			"</IfModule>\n".
			"# PEGASAAS ACCELERATOR MOD_PAGESPEED END\n\n";	
	}	
	
	function get_mod_deflate() {
		return "# PEGASAAS ACCELERATOR DEFLATE START\n".
			"<IfModule mod_deflate.c>\n".
  "  AddOutputFilterByType DEFLATE text/html\n".
  "  AddOutputFilterByType DEFLATE text/css\n".
  "  AddOutputFilterByType DEFLATE text/javascript\n".
  "  AddOutputFilterByType DEFLATE text/xml\n".
  "  AddOutputFilterByType DEFLATE text/plain\n".
  "  AddOutputFilterByType DEFLATE image/x-icon\n".
  "  AddOutputFilterByType DEFLATE image/svg+xml\n".
  "  AddOutputFilterByType DEFLATE application/ld+json\n".			
  "  AddOutputFilterByType DEFLATE application/rss+xml\n".
  "  AddOutputFilterByType DEFLATE application/javascript\n".
  "  AddOutputFilterByType DEFLATE application/x-javascript\n".
  "  AddOutputFilterByType DEFLATE application/xml\n".
  "  AddOutputFilterByType DEFLATE application/xhtml+xml\n".
  "  AddOutputFilterByType DEFLATE application/x-font\n".
  "  AddOutputFilterByType DEFLATE application/x-font-truetype\n".
  "  AddOutputFilterByType DEFLATE application/x-font-ttf\n".
  "  AddOutputFilterByType DEFLATE application/x-font-otf\n".
  "  AddOutputFilterByType DEFLATE application/x-font-opentype\n".
  "  AddOutputFilterByType DEFLATE application/vnd.ms-fontobject\n".
  "  AddOutputFilterByType DEFLATE font/ttf\n".
  "  AddOutputFilterByType DEFLATE font/otf\n".
  "  AddOutputFilterByType DEFLATE font/opentype\n\n".
				
  "  # For Olders Browsers Which Can't Handle Compression\n".
  "  BrowserMatch ^Mozilla/4 gzip-only-text/html\n".
  "  BrowserMatch ^Mozilla/4\.0[678] no-gzip\n".
  "  BrowserMatch \bMSIE !no-gzip !gzip-only-text/html\n".
			"</IfModule>\n".
			"# PEGASAAS ACCELERATOR DEFLATE END\n\n";
	}
	function get_benchmarker() {
		$rewrite_base = rtrim($this->utils->get_wp_location())."/";
		return  "# PEGASAAS ACCELERATOR BENCHMARKER START\n".
			"<IfModule mod_rewrite.c>\n".
				"  RewriteEngine On\n".
				"  RewriteBase {$rewrite_base}\n".
				"  RewriteCond %{QUERY_STRING} accelerator=off\n".
				"  RewriteRule ^(.*)$ $1?accelerate=off&%{QUERY_STRING} [NS,E=no-gzip:1,E=dont-vary:1,E=no-cache]\n".
			"</IfModule>\n".
		"# PEGASAAS ACCELERATOR BENCHMARKER END\n\n";
	}
	function get_mod_expires() {
		return  "# PEGASAAS ACCELERATOR EXPIRES START\n".
			"<IfModule mod_expires.c>\n".
				"  ExpiresActive On\n".
				"  ExpiresDefault \"access 14 days\"\n".
				"  ExpiresByType text/css \"access 1 year\"\n".
				"  ExpiresByType image/png \"access 1 year\"\n".
				"  ExpiresByType image/gif \"access 1 year\"\n".
				"  ExpiresByType text/html \"access 0 seconds\"\n".
				"  ExpiresByType image/jpg \"access 1 year\"\n".
				"  ExpiresByType image/jpeg \"access 1 year\"\n".
				"  ExpiresByType text/x-javascript \"access 1 year\"\n".
				"  ExpiresByType application/pdf \"access 1 year\"\n".
				"  ExpiresByType image/x-icon \"access 1 year\"\n".
				"  ExpiresByType application/x-shockwave-flash \"access 7 months\"\n".
			"</IfModule>\n".
			"# PEGASAAS ACCELERATOR EXPIRES END\n\n";
	}
	

	function get_htaccess_contents($folder = "") {
		if ($this->is_apache() || $this->is_litespeed()) {
			$htaccess_location = $this->get_home_path()."{$folder}.htaccess";
			$htaccess_file = file($htaccess_location);
			return implode("", $htaccess_file);
		} else {
			return "";
		}
	}
	
	function set_caching($enable, $force_write = false) {
		$debug = false;	
		// if plugin is remotely disabled or is in diagnostic mode, then disable caching
		if (PegasaasAccelerator::$settings['status'] == 0 || PegasaasAccelerator::$settings['status'] == 2) {
		// only have file based caching if not in staging mode -- we may build a system that uses the existence of a 
		// .tmp file in the future, to make it possible for staging mode to use file caching
		
			$enable = false;
		}
		
		if ($debug) { print "status: ".PegasaasAccelerator::$settings['status']; }
		if ($debug) { print "<pre class='admin'>"; }
		if ($debug) { print "force-write $force_write"; }

		if ($this->is_apache() || $this->is_litespeed()) {
			

	  		$htaccess_location = $this->get_home_path().".htaccess";
			$htaccess_file = file($htaccess_location);
			$htaccess_file = implode("", $htaccess_file);
			$htaccess_original = $htaccess_file;
			
			if ($enable) {
				if (!is_multisite()) {
					$state = "adding";
					// if we are using file based caching served via .htaccess, then add it
					if (PegasaasAccelerator::$settings['settings']['page_caching']['status'] == 1 || PegasaasAccelerator::$settings['settings']['page_caching']['status'] == 2 ) {
						if (strpos($htaccess_file, "ACCELERATOR PAGE CACHING START") === false || $force_write) {
							$htaccess_file = preg_replace("/(# (PEGASAAS )?ACCELERATOR PAGE CACHING START)(.*)# (PEGASAAS )?ACCELERATOR PAGE CACHING END\n\n/si", "", $htaccess_file);

							//$htaccess_file = $this->get_caching().$htaccess_file;
							$htaccess_file = PegasaasHtaccess::get_caching_instructions().$htaccess_file;
						} 

					// if we are not using file based caching via .htaccess, then make sure the instructions are not in the .htaccess control file
					} else {					
						if (strpos($htaccess_file, "ACCELERATOR PAGE CACHING START") !== false || $force_write) {
							//$htaccess_file = str_replace($this->get_caching(), "", $htaccess_file);

							// use fallback method
							if (strpos($htaccess_file, "ACCELERATOR PAGE CACHING START") !== false) {
								$htaccess_file = preg_replace("/(# (PEGASAAS )?ACCELERATOR PAGE CACHING START)(.*)# (PEGASAAS )?ACCELERATOR PAGE CACHING END\n\n/si", "", $htaccess_file);
							}
						}
					}
				}
				
				
				// apply new .htaccess file to pegasas-caching folder
				$this->cache->asset_folder_path(PEGASAAS_CACHE_FOLDER_PATH."/");
				//print "trying to write file : ".PEGASAAS_CACHE_FOLDER_PATH."/.htaccess";
				$this->utils->write_file(PEGASAAS_CACHE_FOLDER_PATH."/.htaccess", $this->get_wp_content_caching());
				
				
			} else {
				$state = "removing";
	
				$htaccess_file = preg_replace("/(# (PEGASAAS )?ACCELERATOR PAGE CACHING START)(.*)# (PEGASAAS )?ACCELERATOR PAGE CACHING END\r\n\r\n/si", "", $htaccess_file);
				$htaccess_file = preg_replace("/(# (PEGASAAS )?ACCELERATOR PAGE CACHING START)(.*)# (PEGASAAS )?ACCELERATOR PAGE CACHING END\r\n/si", "", $htaccess_file);

				$htaccess_file = preg_replace("/(# (PEGASAAS )?ACCELERATOR PAGE CACHING START)(.*)# (PEGASAAS )?ACCELERATOR PAGE CACHING END\n\n/si", "", $htaccess_file);
				$htaccess_file = preg_replace("/(# (PEGASAAS )?ACCELERATOR PAGE CACHING START)(.*)# (PEGASAAS )?ACCELERATOR PAGE CACHING END\n/si", "", $htaccess_file);
				$htaccess_file = preg_replace("/(# (PEGASAAS )?ACCELERATOR PAGE CACHING START)(.*)# (PEGASAAS )?ACCELERATOR PAGE CACHING END/si", "", $htaccess_file);
			
			}
			if ($debug) { print "state {$state}"; }
				
			if ($htaccess_file != $htaccess_original) {
				// copy to a test folder
				if ($this->utils->is_htaccess_safe($htaccess_file, "PAGE CACHING ({$state})")) {
					$this->utils->write_file($htaccess_location.'--backup', $htaccess_original);
					$this->utils->write_file($htaccess_location, $htaccess_file);
				}
				
	
				
				
			
				
			} else {
					$this->utils->log("Unable to write CACHING instructions to {$htaccess_location}", "file_permissions");

			}
	  } else {
	  }
			if ($debug) { print "</pre>"; }
	}
	
	
	function set_gzip($enable) {
		if (PegasaasAccelerator::$settings['status'] == 0) {
			$enable = false;
		}
		
		if ($this->is_apache() || $this->is_litespeed()) {
	  		$htaccess_location = $this->get_home_path().".htaccess";
		
			$htaccess_file 		= file($htaccess_location);
			$htaccess_file 		= implode("", $htaccess_file);
			$htaccess_original 	= $htaccess_file;
			
			if ($enable) {
				$state = "adding";
				if (strpos($htaccess_file, "mod_deflate") === false) {
					
					$htaccess_file = $this->get_mod_deflate().$htaccess_file;
				}
				
				if (strpos($htaccess_file, "mod_gzip") === false) {
					$htaccess_file = $this->get_mod_gzip().$htaccess_file;
				}
			} else {
				$state = "removing";
				if (strpos($htaccess_file, "mod_deflate") !== false) {
					$htaccess_file = preg_replace("/(# (PEGASAAS )?ACCELERATOR DEFLATE START)(.*)# (PEGASAAS )?ACCELERATOR DEFLATE END\n\n/si", "", $htaccess_file);
				}
				
				if (strpos($htaccess_file, "mod_gzip") !== false) {
					$htaccess_file = preg_replace("/(# (PEGASAAS )?ACCELERATOR GZIP START)(.*)# (PEGASAAS )?ACCELERATOR GZIP END\n\n/si", "", $htaccess_file);

				}
			}

			if ($htaccess_file != $htaccess_original) {
					if ($this->utils->is_htaccess_safe($htaccess_file, "MOD_GZIP ({$state})")) {
					$this->utils->write_file($htaccess_location.'--backup', $htaccess_original);
					$this->utils->write_file($htaccess_location, $htaccess_file);
				}
				//$this->utils->write_file($htaccess_location, $htaccess_file);
				
			}
	  } else {
	  }
	}	
	
	
	function set_benchmarker($enable) {
		if (PegasaasAccelerator::$settings['status'] == 0) {
			$enable = false;
		}

		if ($this->is_apache() || $this->is_litespeed()) {

	  		$htaccess_location = $this->get_home_path().".htaccess";
			$htaccess_file = file($htaccess_location);
			$htaccess_file = implode("", $htaccess_file);
			$htaccess_original = $htaccess_file;
			
			if ($enable) {
				$state = "adding";
				if (strpos($htaccess_file, "ACCELERATOR BENCHMARKER START") === false) {
					$htaccess_file = $this->get_benchmarker().$htaccess_file;
				}
			} else {
				$state = "removing";
				if (strpos($htaccess_file, "ACCELERATOR BENCHMARKER START") !== false) {
					$htaccess_file = preg_replace("/(# (PEGASAAS )?ACCELERATOR BENCHMARKER START)(.*)# (PEGASAAS )?ACCELERATOR BENCHMARKER END\n\n/si", "", $htaccess_file);
				}			
			}
			if ($htaccess_file != $htaccess_original) {
				if ($this->utils->is_htaccess_safe($htaccess_file, "BENCHMARKER ({$state})")) {
					$this->utils->write_file($htaccess_location.'--backup', $htaccess_original);
					$this->utils->write_file($htaccess_location, $htaccess_file);
				}				
				
			}	  
	  	} else {
	  	}
	}
	
	function set_mod_pagespeed($enable) {
		if (PegasaasAccelerator::$settings['status'] == 0) {
			$enable = false;
		}

		if ($this->is_apache() || $this->is_litespeed()) {

	  		$htaccess_location = $this->get_home_path().".htaccess";
			$htaccess_file = file($htaccess_location);
			$htaccess_file = implode("", $htaccess_file);
			$htaccess_original = $htaccess_file;
			
			if ($enable) {
				$state = "adding";
				if (strpos($htaccess_file, "PEGASAAS ACCELERATOR MOD_PAGESPEED START") === false) {
					$htaccess_file = $this->get_mod_pagespeed().$htaccess_file;
				}
			} else {
				$state = "removing";
				if (strpos($htaccess_file, "PEGASAAS ACCELERATOR MOD_PAGESPEED START") !== false) {
					$htaccess_file = preg_replace("/(# PEGASAAS ACCELERATOR MOD_PAGESPEED START)(.*)# PEGASAAS ACCELERATOR MOD_PAGESPEED END\n\n/si", "", $htaccess_file);
				}			
			}
			if ($htaccess_file != $htaccess_original) {
				if ($this->utils->is_htaccess_safe($htaccess_file, "MOD_PAGESPEED ({$state})")) {
					$this->utils->write_file($htaccess_location.'--backup', $htaccess_original);
					$this->utils->write_file($htaccess_location, $htaccess_file);
				}				
				
			}	  
	  	} else {
	  	}
	}	
	
	function set_browser_caching($enable) {
		
		if (PegasaasAccelerator::$settings['status'] == 0) {
			$enable = false;
		}
		
		
		if ($this->is_apache() || $this->is_litespeed()) {
	  		$htaccess_location = $this->get_home_path().".htaccess";
		
			$htaccess_file = file($htaccess_location);
			$htaccess_file = implode("", $htaccess_file);
			$htaccess_original = $htaccess_file;
			
			if ($enable) {
				$state = "adding";
				if (strpos($htaccess_file, "mod_expires") === false) {
					$htaccess_file = $this->get_mod_expires().$htaccess_file;
				}
				
			} else {
				$state = "removing";
				if (strpos($htaccess_file, "mod_expires") !== false) {
					$htaccess_file = preg_replace("/(# (PEGASAAS )?ACCELERATOR CACHING START)(.*)# (PEGASAAS )?ACCELERATOR CACHING END\n\n/si", "", $htaccess_file);
					$htaccess_file = preg_replace("/(# (PEGASAAS )?ACCELERATOR EXPIRES START)(.*)# (PEGASAAS )?ACCELERATOR EXPIRES END\n\n/si", "", $htaccess_file);
				}			
				
			}

			if ($htaccess_file != $htaccess_original) {
				if ($this->utils->is_htaccess_safe($htaccess_file, "BROWSER CACHING ({$state})")) {
					$this->utils->write_file($htaccess_location.'--backup', $htaccess_original);
					$this->utils->write_file($htaccess_location, $htaccess_file);
				}				
				
				//$this->utils->write_file($htaccess_location, $htaccess_file);
			}
	  	
		  
	  } else {
		
		
	  }
		
	}	
	
	function pegasaas_enable_feature() {
		$feature = $_POST['feature'];
		$this->enable_feature($feature);
	}
	
	function enable_feature($feature) {
		global $pegasaas;
		
		$this->set_feature($feature, 1);

		// whenever this feature is enabled, if the credentials are valid, then condition the cloudflare API settings
		if ($feature == "cloudflare") {
			if ($pegasaas->cache->cloudflare_credentials_valid($force_check = true)) {
				$pegasaas->cache->cloudflare_condition_settings();
			}
		}
	}
	
	
	function disable_feature($feature) {
		$this->set_feature($feature, 0);
	}
	
	function save_local_setting($setting, $setting_data) {
		global $pegasaas;
		
		$setting_name = "pegasaas_{$setting}";
		$setting_data = stripslashes($setting_data);
		$check_for_cache_clear = false;


		update_option($setting_name, $setting_data);
		
		if (strstr($setting, "inject_critical_css_global_cpcss_template_override")) {
			$post_type = str_replace("inject_critical_css_global_cpcss_template_override__", "", $setting);
			PegasaasAccelerator::$settings['settings']['inject_critical_css']['global_cpcss_template_override']["$post_type"] = $setting_data;
			delete_option($setting_name);
			
			update_option("pegasaas_inject_critical_css_global_cpcss_template_override", PegasaasAccelerator::$settings['settings']['inject_critical_css']['global_cpcss_template_override']);
		}
		
		if ($setting == "cloudflare_zone_id") {
			update_option("pegasaas_cloudflare_last_zone_id_query_time", 0);
			PegasaasAccelerator::$settings['settings']['cloudflare']['zone_id'] = $setting_data;
		}
		
		if ($setting == "cloudflare_api_key") {
			PegasaasAccelerator::$settings['settings']['cloudflare']['api_key'] = $setting_data;
		}
		if ($setting == "cloudflare_account_email") {
			PegasaasAccelerator::$settings['settings']['cloudflare']['account_email'] = $setting_data;
		}
		if ($setting == "cloudflare_zone_id") {
			PegasaasAccelerator::$settings['settings']['cloudflare']['zone_id'] = $setting_data;
		}		
		
		if ($setting == "cloudflare_authorization_type") {
			PegasaasAccelerator::$settings['settings']['cloudflare']['authorization_type'] = $setting_data;
		}		

		if ($setting == "cloudflare_api_key" || $setting=="cloudflare_account_email"  || $setting == "cloudflare_authorization_type") {
			update_option("pegasaas_cloudflare_last_auth_query_time", 0);
			update_option("pegasaas_cloudflare_last_exists_query_time", 0);
			
			// provided the credentials are valid, ensure we are conditioning the cloudflare API with the required setings
			if ($pegasaas->cache->cloudflare_credentials_valid($force_check = true)) {
				$pegasaas->cache->cloudflare_condition_settings();
			}
		}		
		
		
		
		if ($setting == "dynamic_urls_additional_args") {
			PegasaasAccelerator::$settings['settings']['dynamic_urls']['additional_args'] = $setting_data;
		}		
		
		if ($setting == "exclude_urls_urls") {
			
			PegasaasAccelerator::$settings['settings']['exclude_urls']['urls'] = $setting_data;
			
			
		}		
		
		if ($setting == "defer_render_blocking_js_exclude_scripts") {
			$check_for_cache_clear = true;


			PegasaasAccelerator::$settings['settings']['defer_render_blocking_js']['exclude_scripts'] = $setting_data;
		}
		
		if ($setting == "lazy_load_scripts_additional_scripts") {
			$check_for_cache_clear = true;
			$passed_in_setting_data = $setting_data;
			$setting_data = get_option("pegasaas_lazy_load_scripts_custom_scripts", array());
			
			$urls_to_add = explode("\n", $passed_in_setting_data);
			
			foreach ($urls_to_add as $url) {
				
				if (trim($url) != "") {
					$url = trim($url);
					
					$setting_data["{$url}"] = array("status" => 1,
												"mobile_status" => 1,
												"desktop_status" => 1);

					// update it in the settings (that may be visible during this page load)
					PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']["{$url}"] = $setting_data["{$url}"];
				}
				
				
				
			}

			// store the updated custom scripts
			update_option("pegasaas_lazy_load_scripts_custom_scripts", $setting_data);
		
			// we no longer store this data in this setting
			delete_option($setting_name);
		}


		
		
		
		
		if ($setting == "lazy_load_images_exclude_images") {
			$check_for_cache_clear = true;


			PegasaasAccelerator::$settings['settings']['lazy_load_images']['exclude_images'] = $setting_data;
		}
		
		if ($setting == "image_optimization_exclude_images") {
			$check_for_cache_clear = true;


			PegasaasAccelerator::$settings['settings']['image_optimization']['exclude_images'] = $setting_data;
		}		

		
		if ($setting == "image_optimization_exclude_images_from_auto_sizing") {
			$check_for_cache_clear = true;

			PegasaasAccelerator::$settings['settings']['image_optimization']['exclude_images_from_auto_sizing'] = $setting_data;
		}			
		
		if ($setting == "lazy_load_background_images_exclude_images") {
			$check_for_cache_clear = true;


			PegasaasAccelerator::$settings['settings']['lazy_load_background_images']['exclude_images'] = $setting_data;
		}
		
		if ($setting == "lazy_load_exclude_urls") {
			
			$check_for_cache_clear = true;


			PegasaasAccelerator::$settings['settings']['lazy_load']['exclude_urls'] = $setting_data;
		}

		if ($setting == "defer_unused_css_exclude_stylesheets") {
			$check_for_cache_clear = true;


			PegasaasAccelerator::$settings['settings']['defer_unused_css']['exclude_stylesheets'] = $setting_data;
		}
		
		if ($setting == "defer_render_blocking_css_exclude_stylesheets") {
			$check_for_cache_clear = true;


			PegasaasAccelerator::$settings['settings']['defer_render_blocking_css']['exclude_stylesheets'] = $setting_data;
		}	
		
		if ($setting == "dns_prefetch_additional_domains") {
			$check_for_cache_clear = true;


			PegasaasAccelerator::$settings['settings']['dns_prefetch']['additional_domains'] = $setting_data;
		}
		
		if ($setting == "preload_user_files_resources") {
			$check_for_cache_clear = true;

			PegasaasAccelerator::$settings['settings']['preload_user_files']['resources'] = $setting_data;
		}
		if ($setting == "essential_css_css") {
			$check_for_cache_clear = true;

			PegasaasAccelerator::$settings['settings']['essential_css']['css'] = $setting_data;
		}		
		
		

		if ($check_for_cache_clear) {
			if (!isset($_POST['action']) || $_POST['action'] != "pegasaas_dashboard_settings_update") {
				if (array_key_exists('cache', $_POST) && $_POST['cache'] == "do-not-clear") {
				} else {	
					$this->cache->clear_html_cache();	
				}
			}
		}

		
	}

	
	function set_woocommerce_product_categories_accelerated($enabled, $save_setting = false) {
		if ($save_setting) {
			update_option("pegasaas_woocommerce_product_categories_accelerated", $enabled);
		}
		
		$categories = $this->utils->get_all_woocommerce_product_categories();
			
			foreach ($categories as $category) {
				$resource_id = $category->resource_id;
				$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides");
			
				$page_level_settings['accelerated'] = $enabled;
				$this->utils->update_object_meta($resource_id, "accelerator_overrides", $page_level_settings);
			}
		
	}	

	function set_woocommerce_product_tags_accelerated($enabled, $save_setting = false) {
		if ($save_setting) {
			update_option("pegasaas_woocommerce_product_tags_accelerated", $enabled);
		}
		
		$categories = $this->utils->get_all_woocommerce_product_tags();
			
			foreach ($categories as $category) {
				$resource_id = $category->resource_id;
				$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides");
			
				$page_level_settings['accelerated'] = $enabled;
				$this->utils->update_object_meta($resource_id, "accelerator_overrides", $page_level_settings);
			}
		
	}		
	
	function set_categories_accelerated($enabled, $save_setting = false) {
		$resource_ids = array();
		

		if ($save_setting) {
			update_option("pegasaas_blog_categories_accelerated", $enabled);
		}
		if ($enabled == '1' || $enabled == 1) {
			$enabled = true;
		} 

		$categories = $this->utils->get_all_categories();
			
			foreach ($categories as $category) {
				$resource_id = $category->resource_id;
				$resource_ids[] = $resource_id;
				$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides");
				
				$page_level_settings['accelerated'] = $enabled;
				$this->utils->update_object_meta($resource_id, "accelerator_overrides", $page_level_settings);

			}
		return $resource_ids;
	}
	
	function set_home_page_accelerated($enabled, $save_setting = false) {
		if ($save_setting) {
			update_option("pegasaas_blog_home_page_accelerated", $enabled);
		}		

		$resource_id = "/";
			
		$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides");
		if ($enabled == '1' || $enabled == 1) {
			$enabled = true;
		} 

		$page_level_settings['accelerated'] = $enabled;
		$this->utils->update_object_meta($resource_id, "accelerator_overrides", $page_level_settings);
		
	}
	
	function toggle_local_setting($setting) {
		$setting_name = "pegasaas_{$setting}";
		$default_setting = 0;
		if ($setting == "lazy_load_scripts_instagram_feed") {
			$default_setting = 1;
			if (array_key_exists('cache', $_POST) && $_POST['cache'] == "do-not-clear") {
			} else {
				$this->cache->clear_html_cache();	
			}

		}
		if ($setting == "lazy_load_scripts_thirstyaffiliates") {
			$default_setting = 1;
			if (array_key_exists('cache', $_POST) && $_POST['cache'] == "do-not-clear") {
			} else {
				$this->cache->clear_html_cache();	
			}
		}
		if ($setting == "lazy_load_scripts_jetpack_twitter") {
			$default_setting = 1;
			if (array_key_exists('cache', $_POST) && $_POST['cache'] == "do-not-clear") {
			} else {
				$this->cache->clear_html_cache();
			}
		}	
		
		if ($setting == "lazy_load_scripts_google_recaptcha") {
			$default_setting = 1;
			if (array_key_exists('cache', $_POST) && $_POST['cache'] == "do-not-clear") {
			} else {
				$this->cache->clear_html_cache();
			}
		}	
		
		if ($setting == "lazy_load_scripts_wordpress") {
			$default_setting = 1;
			if (array_key_exists('cache', $_POST) && $_POST['cache'] == "do-not-clear") {
			} else {
				$this->cache->clear_html_cache();	
			}
		}			
		
		if ($setting == "blog_home_page_accelerated") {
			
			$this->cache->clear_html_cache("/");
			$default_setting = 1;
		}
		if ($setting == "blog_categories_accelerated") {		
			$default_setting = 1;
			$this->cache->clear_categories_cache();
		}	

		if ($setting == "woocommerce_product_categories_accelerated") {		
			$default_setting = 1;
			$this->cache->clear_woocommerce_product_categories_cache();
		}	
		if ($setting == "woocommerce_product_tags_accelerated") {		
			$default_setting = 1;
			$this->cache->clear_woocommerce_product_tags_cache();
		}		
		
		if ($setting == "dynamic_urls_utm_source" ||
		   $setting == "dynamic_urls_utm_campaign" ||
		   $setting == "dynamic_urls_utm_medium" ||
		   $setting == "dynamic_urls_utm_term" ||
		   $setting == "dynamic_urls_utm_content" ||
		   $setting == "dynamic_urls_gclid" ||
		   $setting == "dynamic_urls_keyword" ) {
			
			$default_setting = 1;
		}	
		$setting_data = get_option($setting_name, $default_setting);
		if ($setting_data == 0) {
			$setting_data = 1;
		} else {
			$setting_data = 0;
		}
		if ($setting == "blog_home_page_accelerated") {
			$this->set_home_page_accelerated($setting_data);
		}	
		if ($setting == "blog_categories_accelerated") {
			$this->set_categories_accelerated($setting_data);
		}	
		if ($setting == "woocommerce_product_categories_accelerated") {
			$this->set_woocommerce_product_categories_accelerated($setting_data);
		}			
		if ($setting == "woocommerce_product_tags_accelerated") {
			$this->set_woocommerce_product_tags_accelerated($setting_data);
		}		
		
		//print "savign $setting_name as $setting_data";
		
		
		update_option($setting_name, $setting_data);
		
	
	}

	
	function toggle_local_complex_setting($post) {

		$setting = $post['f'];
		$setting_name = "pegasaas_{$setting}"; 
		$default_setting = 0;


		$setting_data = get_option($setting_name, array());

		if ($setting == "lazy_load_scripts_custom_scripts") {
			$url = trim($post['url']);
			$url_notrim = $post['url'];
			$mobile_setting = $post['mobile_setting'];
			$desktop_setting = $post['desktop_setting'];
			$detected_lazy_loadable_scripts = $this->scanner->get_lazy_loadable_scripts();
			
			
			if ($setting_data["{$url}"]['status'] === "" || $setting_data["{$url}"] == NULL) {
			   $current_setting = $detected_lazy_loadable_scripts["$url"]["default"];	
				
			} else {
				$current_setting = $setting_data["{$url}"]['status'];
			}
			if ($post['c'] == "toggle-local-complex-setting") {
				if ($current_setting == 0) {
					$status = 1;
				} else {
					$status = 0;
				}
			} else {
				$status = $current_setting;
			}
			
			PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']["{$url}"]["status"] = $status;
			
			$is_globally_available_script = PegasaasAccelerator::$settings['settings']['lazy_load_scripts']['custom_scripts']["{$url}"]["is_globally_available"];
				
			$setting_data["{$url}"] = array("status" => $status,
												"mobile_status" => $mobile_setting,
												"desktop_status" => $desktop_setting);
			
			$site_resources = get_option("pegasaas_in_page_scripts", array());

			$is_in_page_script = isset($site_resources["$url"]);
			
			
			
			// remove this setting entirely if the script is a manually added script
			// or one that may be left over from an uninstalled theme or plugin
			if ($status == 0 && (!$is_in_page_script && !$is_globally_available_script)) {
				unset($setting_data["{$url}"]);
				unset($setting_data["{$url}\n"]);
				unset($setting_data["{$url}\r"]);
				unset($setting_data["{$url}\r\n"]);
				unset($setting_data["{$url_notrim}"]);
			}
			
			// do not clear if this request has been submitted through background AJAX invocation as
			// the cache has been cleared in the calling routine PegasaasInterface::pegasaas_dashboard_settings_update
			if (!isset($_POST['action']) || $_POST['action'] != "pegasaas_dashboard_settings") {
				if (array_key_exists('cache', $post) && $post['cache'] == "do-not-clear") {
				} else {
					$this->cache->clear_html_cache();	
				}
			}
			
			
			
		} else if ($setting == "strip_google_fonts_mobile_custom_fonts") {
			$signature = trim($post['font']);
		//	print "signature: $signature";
			$detected_lazy_loadable_scripts = $this->scanner->get_page_fonts();
			
			
			if ($setting_data["{$signature}"]['status'] === "" || $setting_data["{$signature}"] == NULL) {
			   $current_setting = 0;	
				
			} else {
				$current_setting = $setting_data["{$signature}"]['status'];
			}

			if ($post['c'] == "toggle-local-complex-setting") {
				if ($current_setting == 0) {
					$status = 1;
				} else {
					$status = 0;
				}
			} else {
				$status = $current_setting;
			}
			
			PegasaasAccelerator::$settings['settings']['strip_google_fonts_mobile']['custom_fonts']["{$signature}"]["status"] = $status;
			
		//	$is_globally_available_script = PegasaasAccelerator::$settings['settings']['strip_google_fonts_mobile']['custom_scripts']["{$url}"]["is_globally_available"];
		

		
			
			$setting_data["{$signature}"] = array("status" => $status);
				// may need to put this code back in, iterating through $site_resources, to remove junk data
			/*
			$site_resources = get_option("pegasaas_in_page_fonts", array());

			$is_in_page_font = isset($site_resources["$url"]);
			
			
			
			// remove this setting entirely if the script is a manually added script
			// or one that may be left over from an uninstalled theme or plugin
			if ($status == 0 && (!$is_in_page_script && !$is_globally_available_script)) {
				unset($setting_data["{$url}"]);
				unset($setting_data["{$url}\n"]);
				unset($setting_data["{$url}\r"]);
				unset($setting_data["{$url}\r\n"]);
				unset($setting_data["{$url_notrim}"]);
			}
			*/
			// do not clear if this request has been submitted through background AJAX invocation as
			// the cache has been cleared in the calling routine PegasaasInterface::pegasaas_dashboard_settings_update
			if (!isset($_POST['action']) || $_POST['action'] != "pegasaas_dashboard_settings") {
				if (array_key_exists('cache', $post) && $post['cache'] == "do-not-clear") {
				} else {
					$this->cache->clear_html_cache();	
				}
			}
			
	//		print "update";
			
		}
		
	//	print "hmm";
		
		
		
	
	//	print "setting_name: $setting_name \n\n";
	//	var_dump($setting_data);
		
		update_option($setting_name, $setting_data);
		
	
	}	
	
	

	
	function remove_item_complex_setting($settings) {

		$setting = $settings['f'];
	
		$default_setting = 0;

 
		if ($setting == "multi_server_ip") {
			$ip_to_remove = trim($settings['ip']);
		
			
			if (filter_var($ip_to_remove, FILTER_VALIDATE_IP)) {
				
				foreach (PegasaasAccelerator::$settings['settings']['multi_server']['ips'] as $i => $ip) {
					if ($ip == $ip_to_remove) {
						unset(PegasaasAccelerator::$settings['settings']['multi_server']['ips']["{$i}"]);
					}
				}
				
				PegasaasAccelerator::set_feature("multi_server", 1, array("ips" => PegasaasAccelerator::$settings['settings']['multi_server']['ips'] ));
				return true;
				//
			} else {
				
				return false;
			}
		} else {
			return false;
			
		}
		

		
		
		
	
		
		
		
		
	
	}		
	
	function set_feature($feature, $status, $optional_parameters = array()) { 
		$debug = false;
		
		
	  	if (PegasaasAccelerator::$settings['api_key'] != "") {
			$api_key = PegasaasAccelerator::$settings['api_key'];
			if ($debug) { 
				print "<pre class='admin'>";
				print $feature."=".$status;
				print "<br>";
				var_dump($optional_parameters);
				print "</pre>";
			}
		//	exit;
			
			$post_fields = array();
			$post_fields['change_feature_status'] 	= $status;
			$post_fields['feature'] 				= $feature;
			$post_fields['options'] 				= $optional_parameters;
			if ($debug) { 
				print "<pre class='admin'>Post Fields\n";
				
				var_dump($post_fields);
				print "</pre>";
			}
			$response = $this->api->post($post_fields);
			if ($debug) {
				print "<pre class='admin'>";
				var_dump($response);
				print "</pre>";
			}
			
			if ($response == "") {
				PegasaasAccelerator::$settings['last_api_check'] = time();
				PegasaasAccelerator::$settings['error'] = "Unable to connect to pegasaas API Key Server";
				$response = json_encode(array("status" => -2, "reason" => "Unable to connect to API Key Server"));
			
			} else {
			
				$data = json_decode($response, true);
				
			//	var_dump($data);
				
				
				if ($debug) {
					print "<pre class='admin'>";
					var_dump($data);
					print "</pre>";
				}
				
				
				if ($data['api_error'] != "") {
				
					PegasaasAccelerator::$settings['last_api_check'] = time();
					PegasaasAccelerator::$settings['error'] 	= "Unable to connect to pegasaas API Key Server";
					PegasaasAccelerator::$settings['reason'] 	= "Unable to connect to pegasaas API Key Server -- please try again.";
					PegasaasAccelerator::$settings['reason_short'] = "timeout";
					$response = json_encode(array("status" => -2, "reason" => "Unable to connect to API Key Server"));
				
				} else if ($data['status'] == '1') {
					PegasaasAccelerator::$settings = $data;
					PegasaasAccelerator::$settings['last_api_check'] = time(); 
								
				} else if ($data['status'] == '0') {
				  	PegasaasAccelerator::$settings = $data;
				  	PegasaasAccelerator::$settings['api_key'] = $api_key;
				  	PegasaasAccelerator::$settings['last_api_check'] = time();
			  
				} else {
					// possible error condition, lets not change the status
					$response = json_encode(array("status" => -1, "reason" => "Unknown")); 
					PegasaasAccelerator::$settings['reason'] = $data['reason'];
			
					PegasaasAccelerator::$settings['last_api_check'] = time();
				}
			}
			
			
			update_option("pegasaas_settings", PegasaasAccelerator::$settings);			
		
			if ($feature == "gzip_compression") {
				$this->set_gzip($status);
			} else if ($feature == "browser_caching") {
				$this->set_browser_caching($status);
			} else if ($feature == "wordlift") {
				$this->set_wordlift($status);
			} else if ($feature == "wpx_cloud") {
				$this->set_caching(true, $force_write = true);	
			
			} else if ($feature == "dns_prefetch") {
				if (array_key_exists('cache', $_POST) && $_POST['cache'] == "do-not-clear") {
				
				} else {
				
					$this->cache->clear_html_cache();
				}
			} else if ($feature == "minify_html") {
				if (array_key_exists('cache', $_POST) && $_POST['cache'] == "do-not-clear") {
			
				} else {
	
					$this->cache->clear_html_cache();
				}				
			} else if ($feature == "lazy_load_scripts") {
				if (array_key_exists('cache', $_POST) && $_POST['cache'] == "do-not-clear") {
				} else {
					$this->cache->clear_html_cache();
				}
			} else if ($feature == "lazy_load") {
				if (array_key_exists('cache', $_POST) && $_POST['cache'] == "do-not-clear") {
				
				} else {
					$this->cache->clear_html_cache();
				}
			} else if ($feature == "lazy_load_images") {
				if (array_key_exists('cache', $_POST) && $_POST['cache'] == "do-not-clear") {
				
				} else {
					$this->cache->clear_html_cache();
				}			
			} else if ($feature == "lazy_load_background_images") {
				if (array_key_exists('cache', $_POST) && $_POST['cache'] == "do-not-clear") {
				
				} else {
					$this->cache->clear_html_cache();
				}
			} else if ($feature == "lazy_load_youtube") {
				if (array_key_exists('cache', $_POST) && $_POST['cache'] == "do-not-clear") {
				
				} else {
					$this->cache->clear_html_cache();
				}				
			} else if ($feature == "minify_css") {

			} else if ($feature == "combine_css") {
				if (array_key_exists('cache', $_POST) && $_POST['cache'] == "do-not-clear") {
				
				} else {
					$this->cache->clear_html_cache();
				}				
				
			} else if ($feature == "disable_wp_emoji") {
				if (array_key_exists('cache', $_POST) && $_POST['cache'] == "do-not-clear") {
				
				} else {
					$this->cache->clear_html_cache();
				}
				
			} else if ($feature == "defer_render_blocking_js") {
				if (array_key_exists('cache', $_POST) && $_POST['cache'] == "do-not-clear") {
				
				} else {
					$this->cache->clear_html_cache();
				}				
			} else if ($feature == "defer_render_blocking_css") {
				if (array_key_exists('cache', $_POST) && $_POST['cache'] == "do-not-clear") {
				
				} else {
					$this->cache->clear_html_cache();
				}				
			} else if ($feature == "defer_unused_css") {
				if (array_key_exists('cache', $_POST) && $_POST['cache'] == "do-not-clear") {
				
				} else {
					$this->cache->clear_html_cache();
				}				
			} else if ($feature == "inject_critical_css") {
				if (array_key_exists('cache', $_POST) && $_POST['cache'] == "do-not-clear") {
				
				} else {
					$this->cache->clear_html_cache();
				}				
			} else if ($feature == "page_caching") {
				$this->set_caching($status);
				

				
			} else if ($feature == "image_optimization") {
			
				if ($optional_parameters['quality'] != "" || $optional_parameters['retain_exif'] != "") {

				
					if (array_key_exists('image_cache', $_POST) && $_POST['image_cache'] == "do-not-clear") {

					} else {

						$this->cache->clear_remote_cache("image");	
						
					}
				}
				
			} else if ($feature == "coverage") {
				if (isset($optional_parameters['extended.do_tags']) && $optional_parameters['extended.do_tags'] == 0) {
					$this->cache->clear_tags();
					return "tags-cleared";
				} else if (isset($optional_parameters['extended.do_categories']) && $optional_parameters['extended.do_categories'] == 0) {
					$this->cache->clear_categories();
					return "categories-cleared";
				} else if (isset($optional_parameters['extended.use_cache']) && $optional_parameters['extended.use_cache'] == 0) {
					$this->cache->clear_extended_coverage();	
					return "extended-coverage-cache-clearing-queued";
				}
			}
		}
		
	}


	function is_apache() {
		$this->utils->log("Server Software: {$_SERVER['SERVER_SOFTWARE']}", "server_info");

		return (strpos( $_SERVER['SERVER_SOFTWARE'], 'Apache') !== false) ;
	}

	
	function is_litespeed() {
		return (strpos( $_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') !== false) ;
	}	
	
	

	
	
	

	
		
	
	function is_login() {
		return false;
		return in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) );
	}

	


	


	
	

	
	public static function fix_url_pattern2($matches) {
		global $source_file;
		
		
		$path 	= str_replace("'", "", $matches[1]);
		$path   = str_replace('"', "", $path);
		$path   = str_replace('&quot;', "", $path);
		
		// if the path to the CSS url() is an absolute URL, return it
		if (substr($path, 0, 7) == "http://" || substr($path, 0, 8) == "https://" || substr($path, 0, 2) == "//") {
		  return "".$matches[0];
			
		// if the format is woff
		 } else if (substr($path, 0, 26) == "data:application/font-woff") {
			return "".$matches[0];
		} else if (substr($path, 0, 29) == "data:application/octet-stream") {
			return "".$matches[0]; 
		} else if (substr($path, 0, 18) == "data:image/svg+xml") {
			return "".$matches[0];
		} else if (substr($path, 0, 21) == "data:image/gif;base64") {
			return "".$matches[0];
		

		// the format is an open type font
		// } else if (substr($path, -4) == ".otf") {
		//	return $matches[0];
			
		// if the path contains a relative path somewhere above the CSS folder path, then map that (relative 2 test)
		} else if (strstr($path, "../")) {
		//	//print "dotdot";
			$count 	= substr_count($path, "../");
						
			for ($i = 0; $i < $count; $i++) {
				$find_string .= "../";
			}
			
			if (substr($source_file, 0, 2) == "//") {
				$file_name_path = explode("/", "https:".$source_file);
			} else { 
				$file_name_path = explode("/", $source_file);
			}
			
			
			// pop the script name out of the array
			array_pop($file_name_path);
	
			// get the number of folders
			$full_path_count = count($file_name_path);
			
			for ($i = $full_path_count - $count; $i < $full_path_count; $i++) {
			  array_pop($file_name_path);	
			}
			
			$file_name_path = implode("/", $file_name_path);
			
			$full_path = ltrim($file_name_path, "/")."/";
			$path = str_replace($find_string, $full_path, $path);
			
		// if the path is an relative based on the root folder, then just append the root
		} else if (substr($path, 0, 1) == "/") {
			//print "Just root\n";
		  $path  =ltrim( $path, "/");
		  
		// if the path is a relative somewhere below the CSS folder path, then map that (relative test)
		} else {
		//	print "Maybe";
			$file_name_path = explode("/", $source_file);
			
			// pop the script name out of the array
			array_pop($file_name_path);
			$file_name_path = implode("/", $file_name_path);
			//$path = $referer_base.ltrim($file_name_path, "/")."/".$path;
			$path = ltrim($file_name_path, "/")."/".$path;
			
		}
	
		return " url('{$path}')";
	}
	
	
	
	
	public static function fix_url_pattern($matches) {
		global $four_oh_four_source;
		
		//return "okay";
		$referer_base = "//".$_SERVER['HTTP_HOST']."/";
		
		$path 	= str_replace("'", "", $matches[1]);
		$path 	= str_replace('"', "", $path);
		$path 	= str_replace('&quot;', "", $path);
		
		if ($path == "") {
			return $matches[0];
			
		// if the URL has a + in it, it likely is javascript, so we should ignore as doing so could break the javascript
		} else if (strstr($path, "+") !== false) {
			return $matches[0];
			
		} else if (strstr($path, "data:image") !== false) {
		    return $matches[0];	
		}
		
		// strip $quot; from strings such as url(&quot;https://something.com/something.jpg&quot;)
	    $matches[0] = str_replace("&quot;", "", $matches[0]);
		
		// added to remove preceeding space on some urls.  Should note that this may cause issue
		// with urls that contain encoded image data 
		$original_path = $path;
		$path = trim($path);
		 
		if (substr($matches[0], 0,1) == " ") {
			$first_character = " ";
		} else {
			$first_character = "";
		}
		//print "path is [$path]\n";
		//print "matches is [{$matches[0]}]\n";
		// if the path to the CSS url() is an absolute URL, return it
		if (substr($path, 0, 7) == "http://" || substr($path, 0, 8) == "https://" || substr($path, 0, 2) == "//") {
			if ($original_path != $path) {
				$matches[0] = str_replace($original_path, $path, $matches[0]);
			}
		  return $first_character.trim($matches[0]);
	 	} else if (substr($path, 0, 26) == "data:application/font-woff") {
			return "".$matches[0];
	 	} else if (substr($path, 0, 29) == "data:application/octet-stream") {
			return "".$matches[0]; 
	 
		// if the path contains a relative path somewhere above the CSS folder path, then map that (relative 2 test)
		} else if (strstr($path, "../")) {
			$count 	= substr_count($path, "../");
						
			for ($i = 0; $i < $count; $i++) {
				$find_string .= "../";
			}
			
			if ($four_oh_four_source != "") {
				$file_name_path = explode("/", $four_oh_four_source);
			} else {
				$file_name_path = explode("/", $_SERVER['REQUEST_URI']);
			}
			
			// pop the script name out of the array
			array_pop($file_name_path);
	
			// get the number of folders
			$full_path_count = count($file_name_path);
			
			for ($i = $full_path_count - $count; $i < $full_path_count; $i++) {
			  array_pop($file_name_path);	
			}
			
			$file_name_path = implode("/", $file_name_path);
			if ($four_oh_four_source == "") {
				$full_path = $referer_base.ltrim($file_name_path, "/");
			} else {
				$full_path = $file_name_path."/";
			}
			$path = str_replace($find_string, $full_path, $path);
			
		// if the path is an relative based on the root folder, then just append the root
		} else if (substr($path, 0, 1) == "/") {
		  $path  = $referer_base.ltrim( $path, "/");
		  
		// if the path is a relative somewhere below the CSS folder path, then map that (relative test)
		} else {
			if ($four_oh_four_source != "") {
				$file_name_path = explode("/", $four_oh_four_source);
			} else {
				$file_name_path = explode("/", $_SERVER['REQUEST_URI']);
			}
			
			
			// pop the script name out of the array
			array_pop($file_name_path);
		
			// reassemble file name path
			$file_name_path = implode("/", $file_name_path);
		
			
			if ($four_oh_four_source == "" || 
				PegasaasUtils::get_file_extension($path) == "eot" || 
				PegasaasUtils::get_file_extension($path) == "ttf" ||
			    PegasaasUtils::get_file_extension($path) == "svg" ||
			    PegasaasUtils::get_file_extension($path) == "woff" ||
				PegasaasUtils::get_file_extension($path) == "woff2") {
				$path = $referer_base.ltrim($file_name_path, "/")."/".$path;
			} 
			
		}
		

		
		
		return " url('{$path}')";
	}


	
	function fetch_external_css() {
		header_remove("Set-Cookie");
		$external_css_data = explode("/!/external-css,", $_SERVER['REQUEST_URI']);
		
		$external_css = $external_css_data[1];
		
		if (substr($external_css,0,2) == "//") {
			if ($_SERVER['HTTPS'] == "on") {
					$external_css = "https:".$external_css;
				} else {
					$external_css = "http:".$external_css;
					
				}
		} else if (substr($external_css,0,1) == "/") {
			if ($_SERVER['HTTPS'] == "on") {
					$external_css = "https:/".$external_css;
				} else {
					$external_css = "http:/".$external_css;
					
				}
		} else {
			$external_css = str_replace("https:/", "https://", $external_css);
			$external_css = str_replace("http:/", "http://", $external_css);
		}
			
		$sapi_type = php_sapi_name();
		header("HTTP/1.1 200 OK");
		if (substr($sapi_type, 0, 3) == 'cgi' || substr($sapi_type, 0, 3) == 'fpm') {
			header("Status: 200 OK");
		} else {
			
		}
		if (strstr($external_css, ".ttf")) {
						header("Content-Type: application/font-sfnt", true);

			
		} else if (strstr($external_css, ".woff2")) {
						header("Content-Type:font/woff2", true);

			
		} else if (strstr($external_css, ".woff")) {
						header("Content-Type: application/font-woff", true);

		} else if (strstr($external_css, ".eot")) {
			header("Content-Type: application/vnd.ms-fontobject", true);
		
		} else if (strstr($external_css, ".svg")) {
			header("Content-Type: image/svg+xml", true);
		} else if (strstr($external_css, ".otf")) {
			header("Content-Type: application/font-sfnt", true);

		} else {
			header("Content-Type: text/css", true);
		}
		
		
			$file_data = file($external_css);
		
			$file_data = implode("", $file_data);
		
		
		
		if (PegasaasAccelerator::$settings['settings']['minify_css']['status'] == 1) {
			$file_data = preg_replace('!/\*.*?\*/!s','', $file_data);
			$file_data = preg_replace('/\n\s*\n/',"\n", $file_data);

			// space
			$file_data = preg_replace('/[\n\r \t]/',' ', $file_data);
			$file_data = preg_replace('/ +/',' ', $file_data);
			$file_data = preg_replace('/ ?([,:;{}]) ?/','$1',$file_data);

			// trailing;
			$file_data = preg_replace('/;}/','}',$file_data);
			//$file_data = "/*minified*/".$file_data;
		} else {
			$file_data = "/*nope*/".$file_data;
		}
	
		if ($file_data == "") {
			return "/* source is blank */";
		}
				// if this is a request to font awesome, then we should attempt to map the paths to the fonts
			if (strstr($external_js, "font-awesome") !== false) {
			
				$fontPathPieces = explode("/", $external_css);
				array_pop($fontPathPieces);
				array_pop($fontPathPieces);
				$fontPath = implode("/", $fontPathPieces)."/fonts/";
				$file_data = str_replace("../fonts/", $fontPath, $file_data);
			}		
			return $file_data;
			
		
	}
	
	
	
	
	
	function is_on_ecommerce_page($buffer) {
		/*
		global $pagenow;
		
		$woo_commerce_installed = function_exists("is_woocommerce");
		if ($woo_commerce_installed) {
			if (strstr($buffer, "woocommerce-page")) {
				return true;
			} 
		}
		*/
		
		
		return false;
		
	}
	
	
	
	
	function is_on_excluded_page() {
		global $pegasaas;
		global $pagenow;
		$is_excluded_url = $this->is_excluded_url($_SERVER['REQUEST_URI']);
		
		global $wp_query;
		
		
		 // AMP pages are excluded from being cached
		 if ((strstr($_SERVER['REQUEST_URI'], "/amp/") || (isset($_GET['amp']) && $_GET['amp'] == 1)) && (!isset(PegasaasAccelerator::$settings['settings']['accelerated_mobile_pages']) && PegasaasAccelerator::$settings['settings']['accelerated_mobile_pages']['status'] != 1)) {
			 return true;
			 
		 // cornerstone builder
		 } else if (strstr($_SERVER['REQUEST_URI'], "/x/#/") || strstr($_SERVER['REQUEST_URI'], "/x/") || strstr($_SERVER['HTTP_REFERER'], "/x/")) {
			 return true;

		 // on an admin page, or a user defined excluded url
		 } else if ($is_excluded_url) {
			 return true;
		
		// Because instapages bypass most of the wordpress system, we cannot explicitly enable or disable individual pages.  This means
		// that we have to indicates that it is enabled by default.  The previous ifelse will catch if the page is explicitly excluded through the
		// Excluded URLS setting.
		} else if (PegasaasUtils::instapage_active() && PegasaasUtils::is_instapage_url()) {
			return false;
				
		 // this will not evaluate as true if the is_on_excluded_page is called early in the page lifecycle (before parse_query), when we might expect is_404 to return true
		 } else if (isset( $wp_query ) && is_404()) {
			return true;
		 } 

		return false;
	}
	
	
	function post_is_password_protected() {
		global $post;
		return !empty($post->post_password);
		
	}
	
	function is_excluded_url($url) {
		global $pegasaas;
	
		
		 $excluded_pages = array();
		 $excluded_pages[] = "pagely/status/";
		
		 $excluded_pages[] = 'wp-cron\.php';
		 //$excluded_pages[] = 'wp-login\.php';
		 $excluded_pages[] = 'wp-register\.php';
		// $excluded_pages[] = 'admin-ajax\.php';
		 $excluded_pages[] = '\?fl_builder';
		 $excluded_pages[] = '\?kinsta-monitor';
		 $excluded_pages[] = '&et_fb=1'; // divi
		 $excluded_pages[] = '&et_bfb=1'; // divi
		 $excluded_pages[] = '\?tve=true'; // thrive visual editor
		 $excluded_pages[] = '&PageSpeed=off'; // mod pagespeed bypass		
		 $excluded_pages[] = 'robots\.txt';
		 $excluded_pages[] = '\?wc-ajax';
		 $excluded_pages[] = '\.xml';
		 $excluded_pages[] = '\.kml';
		 $excluded_pages[] = '\.xsl';		
		 $excluded_pages[] = '\.zip';
		 $excluded_pages[] = '\.js';
		 $excluded_pages[] = '\.css';
		 $excluded_pages[] = '\.php';
		 $excluded_pages[] = '\.png';
		 $excluded_pages[] = '\.jpg';
		 $excluded_pages[] = '_ubhc/';
		 $excluded_pages[] = 'wp-content/';
		 $excluded_pages[] = '\?wp_service_worker';
		 $excluded_pages[] = '\?sucurianticache';
		 $excluded_pages[] = 'wc-auth/';
	//	 $excluded_pages[] = 'wp-admin/';
	//	 $excluded_pages[] = 'wp-json/';
		$excluded_pages[] = 'cf-api/';
		$excluded_pages[] = 'w3tc_rewrite_test';
	 	$excluded_pages[] = '\?infinity=scrolling';
		$excluded_pages[] = '\?preview=true';
		$excluded_pages[] = 'post_type=shop_order'; // woo-commerce order
		 $excluded_pages[] = 'elementor-preview';    // elementor preview
		 $excluded_pages[] = '\?custom-css'; 


	 	 $excluded_pages[] 	= str_replace(".", '\.', admin_url( 'admin-ajax.php' )); 
	 	 $excluded_pages[] 	= str_replace(".", '\.', admin_url()); 
	 	 $excluded_pages[] 	= str_replace(".", '\.', wp_login_url()); 
	 	 $excluded_pages[] 	= str_replace(".", '\.', content_url()); 
		
		
		
		 /*******************************
		  * 'REDIRECTION' Plugin handling
		  *******************************/
	 	 if ($this->utils->does_plugin_exists_and_active("redirection") && !$this->loaded_redirection_excluded_urls) {
			 global $wpdb;
			 $results = $wpdb->get_results("SELECT url FROM {$wpdb->prefix}redirection_items WHERE status='enabled'");
			 foreach($results as $row) {
				 $excluded_pages[] = $results['url'];
			 }
			 $this->loaded_redirection_excluded_urls = true;
		 }	
		
		
		/*******************************
		 * 'WOOCOMMERCE' Plugin handling
		 *******************************/
	
		
		if (function_exists("wc_get_page_id")) {
			$woocommerce_pages = array("cart", "checkout", "myaccount");
			foreach ($woocommerce_pages as $page_name) {
				$page_id =  wc_get_page_id( $page_name );
				if ($page_id) {
					$post_name = get_post_field( 'post_name', $page_id);
					if ($post_name) {
			 			$excluded_pages[] = $post_name;
					}
				}	
			}
		}

		
		
		
		
		 if (PegasaasAccelerator::$settings['settings']['exclude_urls']['status'] == 1) {
		
		 	$user_defined_excluded_paths = explode("\n", trim(str_replace("\r", "",PegasaasAccelerator::$settings['settings']['exclude_urls']['urls'])));
			$excluded_pages = array_merge($excluded_pages, $user_defined_excluded_paths);
		 }

		
		if ($pegasaas->utils->does_plugin_exists_and_active('buddypress') ) {
			$excluded_pages[] = buddypress()->Members->root_slug;
			$excluded_pages[] = buddypress()->activity->root_slug;
		}
		
		
		foreach ($excluded_pages as $excluded_page) {
			if (trim($excluded_page) != "") {	
				$excluded_page = str_replace("#", "\#", $excluded_page);
				$pattern = "#{$excluded_page}#";
		

				if ($excluded_page != "" && preg_match($pattern, $url)) {
					$pegasaas->utils->log("Excluded because is excluded url.  found '$url' in '$excluded_page'", "caching");

					return true;
				}
			}
			
		}	
	
		
		return false;
	}
	

	

	
	function fetch_external_js() {
		 header_remove("Set-Cookie");
		$external_js_data = explode("/!/external-js,", $_SERVER['REQUEST_URI']);
		
		
		$external_js = $external_js_data[1];
		
		if (substr($external_js,0,2) == "//") {
			if ($_SERVER['HTTPS'] == "on") {
					$external_js = "https:".$external_js;
				} else {
					$external_js = "http:".$external_js;
					
				}
		} else if (substr($external_js,0,1) == "/") {
			if ($_SERVER['HTTPS'] == "on") {
					$external_js = "https:/".$external_js;
				} else {
					$external_js = "http:/".$external_js;
					
				}
		} else {
			$external_js = str_replace("https:/", "https://", $external_js);
			$external_js = str_replace("http:/", "http://", $external_js);
		}
			
		$sapi_type = php_sapi_name();
		header("HTTP/1.1 200 OK");
		if (substr($sapi_type, 0, 3) == 'cgi' || substr($sapi_type, 0, 3) == 'fpm') {
			header("Status: 200 OK");
		} else {
			
		}
	
			header("Content-type: text/javascript", true);
		
			$file_data = file($external_js);
			$file_data = implode("", $file_data);
		//return "x".$file_data;
		//return $external_css;
		if ($file_data == "") {
			return "/* source is blank */";
		}
	
		return $file_data;
			
		
	}	

	
	


	function add_analytics_opt_out() {
		?><script id='pegasaas-analytics-opt-out'>window['_gaUserPrefs'] = { ioo : function() { return true; } };</script>
<?php
	}
	
	
  
	
	
	/* this functionality not included in the LITE version */
	function is_wordlift_api() {
		return false;
	}
	

	function capture_in_page_scripts($buffer) {
		global $pegasaas;
		
		$matches = array();
		$pattern = '/\<'.'!--\[if(.*?)\<'.'!\[endif/s';
		preg_match_all($pattern, $buffer, $matches);
	
		foreach ($matches[0] as $match_data) {
		  $find 		= $match_data;
		  $replace 		= str_replace("<script", "<iescript", $find);
		  $buffer = str_replace($find, $replace, $buffer);  
		}
		$matches 		= array();
		$pattern 		= "/<script(.*?)>(.*?)<\/script>/si";
    	preg_match_all($pattern, $buffer, $matches);
	
		$src_pattern 	= "/\ssrc=['\"](.*?)['\"]/si";	
		
		$site_resources = array();
		$third_party_vendor_scripts = array();
		
		foreach ($matches[0] as $index => $find) {
			$match_src = array();
	  		preg_match($src_pattern, $matches[1]["$index"], $match_src);
	  		$src 	= $pegasaas->utils->strip_query_string($match_src[1]);
			$src = str_replace(PegasaasCache::get_cache_server(false, "js"), "", $src);
			$src = str_replace(PegasaasCache::get_cache_server(false, "css"), "", $src);
			$src = str_replace(PegasaasCache::get_cache_server(false, "img"), "", $src);
			$src = str_replace(PegasaasCache::get_cache_server(false), "", $src);
			$src = str_replace($pegasaas->get_home_url(), "", $src);
			$src = $pegasaas->utils->strip_query_string($src);
					
			if (strstr($find, "https://www.google.com/maps/embed") || strstr($find, "maps.googleapis.com/maps/api/js")) {
				$third_party_vendors['google_maps'] = true;				
			} else if (strstr($find, "h,o,t,j,a,r") && strstr($find, "static.hotjar.com")) {
				$third_party_vendors['hotjar'] = true;
			} else if ((strstr($find, "w,d,s,l,i") || strstr($find, "w, d, s, l, i")) && strstr($find, "googletagmanager.com")) {
				$third_party_vendors['google_tag_manager'] = true;
			
			} else if (strstr($find, "var Tawk_API=Tawk_API")) {
				$third_party_vendors['tawk'] = true;


			} else if (strstr($find, "//code.tidio.co/")) {
				$third_party_vendors['tidio_chat'] = true;

			} else if (strstr($find, "https://widget.privy.com/assets/widget.js")) {
				$third_party_vendors['privy'] = true;
				
			} else if (strstr($find, "https://cdn.ampproject.org/v0.js")) {
				$third_party_vendors['ampproject'] = true;
				
			} else if (strstr($find, "app.capsumo.com")) {
				$third_party_vendors['capsumo'] = true;
				
				
				
			} else if (strstr($find, "cdn.subscribers.com/assets/subscribers.js")) {
				$third_party_vendors['subscribers'] = true;

			} else if (strstr($find, 'script.src = "https://messenger.ngageics.com/ilnksrvr.aspx"')) {
				$third_party_vendors['ngageics'] = true;
				
				
			} else if (strstr($find, "var t = window.driftt = window.drift = window.driftt || []")) {
				$third_party_vendors['driftt'] = true;
			
			} else if (strstr($find, "consent.trustarc.com")) {
				$third_party_vendors['trustarc'] = true;
				
			} else if (strstr($find, "cdn.segment.com/analytics.js/v1")) {
				$third_party_vendors['segmentio'] = true;

		
			} else if (strstr($find, "(function(d, s, id)") && strstr($find, "/connect.facebook.net/")) {
				$third_party_vendors['facebook_sdk'] = true;
				
			} else if (strstr($find, "player.anyclip.com")) {
				$third_party_vendors['anyclip'] = true;
				
			} else if (strstr($find, "m2.ai/pghb")) {
				$third_party_vendors['pub_guru'] = true;
				
			} else if (strstr($find, "social-warfare/assets/js/script.min.js")) {
				$third_party_vendors['social_warfare'] = true;
				
			} else if (strstr($find, "contextual.media.net/dmedianet.js")) {
				$third_party_vendors['media_net'] = true;
				
			} else if (strstr($find, "cdn.convertbox.com/convertbox/js/embed.js")) {
				$third_party_vendors['convertbox'] = true;				
				
				
			} else if (strstr($find, "cdn.livechatinc.com/tracking.js")) {
				$third_party_vendors['live_chat'] = true;
			
			} else if (strstr($find, "https://d10lpsik1i8c69.cloudfront.net/w.js")) {
				$third_party_vendors['lucky_orange'] = true;
			} else if (strstr($find, "//pixel.geobid.com/")) {
				$third_party_vendors['geobid_pixel'] = true;
			} else if (strstr($find, "f,b,e,v,n,t,s")) {
				$third_party_vendors['facebook_pixel'] = true;
			} else if (strstr($find, "i,s,o,g,r,a,m") && strstr($find, "google-analytics.com")) {
				$third_party_vendors['google_analytics'] = true;
			} else if (strstr($find, "gtag('config'") && strstr($find, "function gtag()") && strstr($find, "gtag('js'") == false) {
				$third_party_vendors['google_analytics'] = true;
			} else if (strstr($find, 'src="https://www.googletagmanager.com/gtag/js') && $matches[2][$index] == "") {
				$third_party_vendors['google_analytics'] = true;
			} else if (strstr($find, "m,e,t,r,i,k,a")) {
				$third_party_vendors['yandex'] = true;
			} else if (strstr($find, "b,o,n,g,s,r,c")) {
				$third_party_vendors['oribi_analytics'] = true;
			} else if (strstr($find, "https://www.clickcease.com/monitor/stat.js")) {
				$third_party_vendors['clickcease'] = true;
			} else if (strstr($find, 'src="//cdn.callrail.com') && $matches[2][$i] == "") {
				$third_party_vendors['callrail'] = true;
			} else if (strstr($find, 'src="//js.hs-scripts.com') && $matches[2][$i] == "") {
				$third_party_vendors['hubspot'] = true;
			} else if (strstr($find, '//stats.wp.com') && $matches[2][$i] == "") {
				$third_party_vendors['wordpress_site_stats'] = true;
			} else if (strstr($find, '//script.crazyegg.com') && $matches[2][$i] == "") {
				$third_party_vendors['crazyegg'] = true;
			} else if (strstr($find, '//static.getclicky.com/js') && $matches[2][$i] == "") {
				$third_party_vendors['clicky_analytics'] = true;
			} else if (strstr($find, "function(e,a,t){var r,n,o,i,p") && strstr($find, "window._wpemojiSettings")) {
				$third_party_vendors['wordpress_emoji'] = true;
			} else if (strstr($find, "s,u,m,o,j,v") && strstr($find, "load.sumo.com")) {
				$third_party_vendors['sumo'] = true;
			} else if (strstr($find, "var ic=w.Intercom;")) {
				$third_party_vendors['intercom'] = true;
			} else if (strstr($find, "p,u,s,h") && strstr($find, "pushcrew.com")) {
				$third_party_vendors['pushcrew'] = true;
			}
			
			if ($src != "" && !array_key_exists($src, $site_resources)) {
				$site_resources["$src"] = array("url" => $src);
			}
		}	
		update_option("pegasaas_third_party_vendor_scripts", $third_party_vendors);
		update_option("pegasaas_in_page_scripts", $site_resources);
		return $site_resources;
	}
	


	function capture_in_page_fonts($buffer) {
		global $pegasaas;
		//print "<Pre>";
		//print htmlentities($buffer);
	//	print "</pre>";
		
		// get existing critical css fonts
		$font_face_pattern = '/@font-face{(.*?)?}/si';
		$font_face_matches = array();
		preg_match_all($font_face_pattern, $buffer, $font_face_matches);
			
		$all_fonts = array();
		$font_family_pattern = '/font-family:(.*?);/i';
		$font_weight_pattern = '/font-weight:(.*?);/i';
		$font_style_pattern = '/font-style:(.*?);/i';

	
		foreach ($font_face_matches[0] as $x => $font_code_block) {
			
			if (strstr($font_code_block, "fonts.gstatic.com") || strstr($font_code_block, "cdn1.pegasaas.io/fonts/s/")  || strstr($font_code_block, "sfgcdn.pegasaas.io/s/")) {
				
					
				$font_family_matches = array();
				preg_match($font_family_pattern, $font_code_block, $font_family_matches);

				$font_weight_matches = array();
				preg_match($font_weight_pattern, $font_code_block, $font_weight_matches);

				$font_style_matches = array();
				preg_match($font_style_pattern, $font_code_block, $font_style_matches);
				//print "font-familyx: {$font_family_matches[1]}<br>";
				$font_family = str_replace(" ", "+", trim($font_family_matches[1],"'"));
			
				$font_weight = $font_weight_matches[1];
				$font_style  = str_replace("'", "", $font_style_matches[1]);

				//print "font family: {$font_family}<br>";
				$all_fonts["$font_family"][] = array("weight" => $font_weight, "style" => $font_style, );
			}
		}		
	
		update_option("pegasaas_in_page_fonts", $all_fonts);
		return $all_fonts;
	}

	function condition_comment_codes($html) {
		$html = str_replace("<!--", "[PEGASAASCOMMENT!--", $html);
		$html = str_replace("-->", "--PEGASAASCOMMENT]", $html);
		return $html;
		
	}
	
	function re_condition_comment_codes($buffer) {
		$buffer = str_replace("[PEGASAASCOMMENT!--", "<!--", $buffer);
		$buffer = str_replace("--PEGASAASCOMMENT]", "-->", $buffer);
		return $buffer;
	}
	
	
	

	
	function apply_powered_by_message($buffer) {
		if (PegasaasAccelerator::$settings['settings']['promote']['status'] != "0") {
			$messages = array();
			$messages[] = "Website Supercharged by <a href='https://pegasaas.com/[affid]'>Pegasaas</a>";
			$messages[] = "<a href='https://pegasaas.com/[affid]'>PageSpeed Optimization</a> by <a href='https://pegasaas.com/[affid]'>Pegasaas</a>";
			$messages[] = "Website Supercharged by <a href='https://pegasaas.com/[affid]'>Pegasaas</a>";
			$messages[] = "<a href='https://pegasaas.com/[affid]'>PageSpeed Optimization</a> by <a href='https://pegasaas.com/[affid]'>Pegasaas</a>";
			$messages[] = "Website Supercharged by <a href='https://pegasaas.com/[affid]'>Pegasaas</a>";
			$messages[] = "<a href='https://pegasaas.com/[affid]'>PageSpeed Optimization</a> by <a href='https://pegasaas.com/[affid]'>Pegasaas</a>";
			$messages[] = "Website Supercharged by <a href='https://pegasaas.com/[affid]'>Pegasaas</a>";
			$messages[] = "<a href='https://pegasaas.com/[affid]'>PageSpeed Optimization</a> by <a href='https://pegasaas.com/[affid]'>Pegasaas</a>";
			$messages[] = "Website Supercharged by <a href='https://pegasaas.com/[affid]'>Pegasaas</a>";
			$messages[] = "<a href='https://pegasaas.com/[affid]'>PageSpeed Optimization</a> by <a href='https://pegasaas.com/[affid]'>Pegasaas</a>";

			
			$index = substr(hexdec(PegasaasAccelerator::$settings['installation_id']), -1);

			$message = $messages["{$index}"];
			$message = "<div style='text-align: center; font-size: 10pt;'>{$message}</div>";
			$affid = "";
			
			if (PegasaasAccelerator::$settings['settings']['promote']['status'] == 2 && PegasaasAccelerator::$settings['account']['affiliate_id'] != "") {
				$affid = "?aid=".PegasaasAccelerator::$settings['account']['affiliate_id'];
			} else if (PegasaasAccelerator::$settings['settings']['promote']['status'] == 3 && PegasaasAccelerator::$settings['account']['affiliate_id'] != "") {
				$affid = "?aid=".PegasaasAccelerator::$settings['account']['affiliate_id'];
			}
			$message = str_replace("[affid]", $affid, $message);
			$buffer = str_replace("</body>", "{$message}</body>", $buffer);
		}
		return $buffer;
	}
	
	
	
	

	function query_args_exists() {
		global $pegasaas;
		
		$object_id 			= PegasaasUtils::get_object_id();
		$request_args = $pegasaas->utils->get_uri_args();
		$object_id_args = $pegasaas->utils->get_uri_args($object_id);
		$args = trim(str_replace($object_id_args, "", $request_args), "&");
		
		if ($args == "") {
			return false;
		}
		
		return true;
	}
	
	
	
	
	function apply_benchmark_mode($buffer) {
		$stylesheet_count = 0;
		

		// fetch all link references
		$matches = array();
		$pattern = "/<link(.*?)>/si";
    	preg_match_all($pattern, $buffer, $matches);
	    
		$href_pattern = "/href=['\"](.*?)['\"]/si";
		$media_pattern = "/media=['\"](.*?)['\"]/si";
		$rel_pattern = "/rel=['\"](.*?)['\"]/si";
	
		
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
			if ($rel == "stylesheet") {
				$new_href = $href;
				$new_css_link = $css_link;

				if (strstr($new_href, "?") !== false) {
					$new_href .= "&accelerator=off";

				} else {
					$new_href .= "?accelerator=off";
				}

				$new_css_link   = str_replace($href, $new_href, $css_link);
				$buffer 		= str_replace($css_link, $new_css_link, $buffer);	
			}
			
		

		}
	
		
	  
		return $buffer;
		
	}
	
	
	 
	/* this functionality not available in the lite version */
	function proxy_external_url($buffer) {
	
		

		
		return $buffer;
		
		
	}	

	
	


	
	
	
	
	function clear_queued_requests() {
		$response = $this->api->post(array("command" => "cancel-all-cpcss-requests"), array("blocking" => false)); 
		delete_option('pegasaas_pending_critical_css_request');
	}

	function clear_queued_optimization_requests() {
		$response = $this->api->post(array("command" => "cancel-all-optimization-requests"), 
									 array("timeout" => $this->api->get_general_api_request_timeout(), 
										   "blocking" => false)); 
		//delete_option('pegasaas_pending_critical_css_request');
		$this->db->delete("pegasaas_api_request", array("request_type" => "optimization-request"));
	}



	
	static function get_global_cpcss_types() {
		$post_types = get_post_types(array("public" => true ));
		
		unset($post_types['attachment']);
		unset($post_types['pegasaas-deferred-js']);
		unset($post_types['elementor_library']);
		
		// thrive post types
		unset($post_types['tcb_symbol']);
		unset($post_types['tcb_lightbox']); 
		
		if (class_exists("Tribe__Main")) {
			$post_types["tribe_events"] = "tribe_events";
		}
		
		if (PegasaasAccelerator::$settings['settings']['woocommerce']) {
			//$post_types['woocommerce_product_categories'] = "woocommerce_product_categories";
			//$post_types['woocommerce_product_categories'] = "woocommerce_product_categories";
		}
		$post_types['home_page'] = "home_page";
		$post_types['page'] 	 = "page";
		
		return $post_types;
	}
	
	
	


	
	
	
	

	
  
	function fix_external_url_pattern($matches) {
		global $pegasaas;
		
		$cache_server = PegasaasCache::get_cache_server(PegasaasAccelerator::$settings['settings']['webp_images']['status'] == 1, "img");
		
		$space = $matches[1];
		$quote = $matches[2];
		$url = $matches[3];
		$file_extension = strtolower(PegasaasUtils::get_file_extension($url));
		if (!strstr($url, "pegasaas.io") && ($file_extension == "jpg" || $file_extension == "png" || $file_extension == "webp")) {
			$url = $cache_server."/external-image,".$url;
		}
		
		$quote2 = $matches[3];
		return "{$space}url({$quote}{$url}{$quote2})";
		
	}

	static function execution_time_static() { 
		if (self::$start_time == 0) {
			self::$start_time = self::microtime();
		}
		return self::microtime() - self::$start_time;
	}


	function execution_time() { 
		if (self::$start_time == 0) {
			self::$start_time = self::microtime();
		}
		return self::microtime() - self::$start_time;
	}
	
	static function microtime() {
		return microtime(true);
		list($usec, $sec) = explode(" ", microtime());
		$time = time() + $usec;
		return $time;
	}


	
		
	function disable_prioritization_for_page($resource_id) {
		global $pegasaas;
	
	
		$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides");
		$page_level_settings['prioritized'] = 0;
	

		$prioritized_pages = get_option("pegasaas_prioritized_pages");
		unset($prioritized_pages["{$resource_id}"]);

		update_option("pegasaas_prioritized_pages", $prioritized_pages);	
		
		// update session object
		$index_position = PegasaasUtils::get_pages_and_posts_array_position($resource_id);
		if ($index_position != NULL) {
			PegasaasUtils::$all_pages_and_posts["$index_position"]->prioritized = false;
			$pegasaas->data_storage->set("all_pages_and_posts", PegasaasUtils::$all_pages_and_posts);
		}

		$this->utils->update_object_meta($resource_id, 'accelerator_overrides', $page_level_settings);
		
	}
	
	
	function disable_accelerator_for_page($resource_id, $set_accelerated_pages = true) {
		global $pegasaas;
	
	
		$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides");
		$page_level_settings['accelerated'] = 0;
	

		$accelerated_pages = get_option("pegasaas_accelerated_pages", array());
		unset($accelerated_pages["{$resource_id}"]);

		$end_size = sizeof($accelerated_pages);
		$this->utils->log("update_option('pegasaas_accelerated_pages', Array({$end_size}) -- disable_accelerator_for_page", "data_structures");
		update_option("pegasaas_accelerated_pages", $accelerated_pages, false);	
		
		// update session object
		$index_position = PegasaasUtils::get_pages_and_posts_array_position($resource_id);
		if ($index_position != NULL) {
			PegasaasUtils::$all_pages_and_posts["$index_position"]->accelerated = false;
			$pegasaas->data_storage->set("all_pages_and_posts", PegasaasUtils::$all_pages_and_posts);
		}

		$this->utils->update_object_meta($resource_id, 'accelerator_overrides', $page_level_settings);
		
		$this->cache->clear_page_cache($resource_id);
		$this->scanner->clear_performance_scans($resource_id);
		if ($set_accelerated_pages) {
			$this->set_accelerated_pages();
		}
	}

	function pegasaas_change_system_mode() {
		
		
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			if ($_POST['mode'] == 'live') {
				$this->enable();
				print json_encode(array("status" => 1));
			} else if ($_POST['mode'] == 'diagnostic') {
				$this->enable_diagnostic_mode();
				print json_encode(array("status" => 1));
			} else if ($_POST['mode'] == 'development') {
				$this->enable_development_mode($_POST['duration']);
				$expiry = get_option("pegasaas_development_mode", 0);
				print json_encode(array("status" => 1, 'enabled-until' => $expiry));
			} else {
				print json_encode(array("status" => 0, 'message' => "Error: Invalid Mode: {$_POST['mode']}"));
			}
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}



	function pegasaas_disable_accelerator_for_page() {
		// need to get the post id, and clear the cache
		
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			if ($_POST['resource_id'] == "") {
				print json_encode(array("status" => 0, "message" => 'Error: Blank Resource ID'));
			} else {
				$this->disable_accelerator_for_page($_POST['resource_id']);
				
				print json_encode(array("status" => 1));
				
			}

		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}

	function pegasaas_disable_prioritization_for_page() {
		// need to get the post id, and clear the cache
		
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			if ($_POST['resource_id'] == "") {
				print json_encode(array("status" => 0, "message" => 'Error: Blank Resource ID'));
			} else {
				$this->disable_prioritization_for_page($_POST['resource_id']);
				
				print json_encode(array("status" => 1));
				
			}

		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}	
	
	function pegasaas_disable_staging_for_page() {


		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			if ($_POST['resource_id'] == "") {
				print json_encode(array("status" => 0, "message" => 'Error: Blank Resource ID'));
			} else {
				$page_level_settings = PegasaasUtils::get_object_meta($_POST['resource_id'], "accelerator_overrides", true);
				$page_level_settings['staging_mode_page_is_live'] = 1;
				$this->utils->update_object_meta($_POST['resource_id'], "accelerator_overrides", $page_level_settings);
				print json_encode(array("status" => 1, "message" => "Staging Mode Disabled For {$_POST['resource_id']}"));

			}
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}	
	
	function pegasaas_enable_staging_for_page() {


		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			if ($_POST['resource_id'] == "") {
				print json_encode(array("status" => 0, "message" => 'Error: Blank Resource ID'));
			} else {
				$page_level_settings = PegasaasUtils::get_object_meta($_POST['resource_id'], "accelerator_overrides", true);
				$page_level_settings['staging_mode_page_is_live'] = 0;
				$this->utils->update_object_meta($_POST['resource_id'], "accelerator_overrides", $page_level_settings);
				print json_encode(array("status" => 1, "message" => "Staging Mode Enabled For {$_POST['resource_id']}"));

			}
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}

	function pegasaas_enable_accelerator_for_page() {
		global $pegasaas;


		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			if ($_POST['resource_id'] == "") {
				print json_encode(array("status" => 0, "message" => 'Error: Blank Resource ID'));
			} else {
				$acceleration_data = $this->scanner->get_site_pages_accelerated_data();
				$accelerated_pages = get_option("pegasaas_accelerated_pages", array());
	
				$pages_accelerated = sizeof($accelerated_pages);

				if ($pages_accelerated >= PegasaasAccelerator::$settings['limits']['maximum_page_accelerations_available']) {
					print json_encode(array("status" => 0, 
											"pages_accelerated" => $pages_accelerated, 
											"accelerated_pages" => $accelerated_pages, 
											"acceleration_data" => $acceleration_data, 
											"message" => "You have already accelerated the maximum number of pages available in your plan (".PegasaasAccelerator::$settings['limits']['accelerated_pages'].").\n\nEither turn off acceleration for an already accelerated page/post or upgrade your plan via the Accelerator dashboard to accelerate more pages."));
				} else {
					$page_level_settings = PegasaasUtils::get_object_meta($_POST['resource_id'], "accelerator_overrides", true);
					$page_level_settings['accelerated'] = 1;
					
					$index_position = PegasaasUtils::get_pages_and_posts_array_position($_POST['resource_id']);
					if ($index_position != NULL) {
						PegasaasUtils::$all_pages_and_posts["$index_position"]->accelerated = true;
						$pegasaas->data_storage->set("all_pages_and_posts", PegasaasUtils::$all_pages_and_posts);
					}
					
					
					$this->utils->update_object_meta($_POST['resource_id'], "accelerator_overrides", $page_level_settings);
					$this->cache->clear_page_cache($_POST['resource_id']);

					$accelerated_pages["{$_POST['resource_id']}"] = 1;
					$acceleration_data['accelerated']++;
					$acceleration_data['not_accelerated']--;
					
					$end_size = sizeof($accelerated_pages);
					$this->utils->log("update_option('pegasaas_accelerated_pages', Array({$end_size}) -- pegasaas_enable_accelerator_for_page", "data_structures");
					update_option("pegasaas_accelerated_pages", $accelerated_pages, false);
					
					//var_dump(get_option("pegasaas_accelerated_pages"));
					print json_encode(array("status" => 1, 
											"acceleration_pages" => $accelerated_pages, 
											"pages_accelerated" => $acceleration_data['accelerated'], 
											"acceleration_data" => $acceleration_data));
					$this->set_accelerated_pages();
				}
			}
			
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}
	
	function pegasaas_enable_prioritization_for_page() {
		global $pegasaas;


		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			if ($_POST['resource_id'] == "") {
				print json_encode(array("status" => 0, "message" => 'Error: Blank Resource ID'));
			} else {
				$prioritized_pages = get_option("pegasaas_prioritized_pages");
	
				$pages_prioritzed = sizeof($prioritized_pages);

				if ($pages_prioritzed >= PegasaasAccelerator::$settings['limits']['page_prioritizations']) {
					print json_encode(array("status" => 0, 
											"pages_prioritized" => $pages_prioritzed, 
											"message" => "You have already prioritized the maximum number of pages available in your plan (".PegasaasAccelerator::$settings['limits']['page_prioritizations'].").\n\nEither turn off prioritization for an already prioritized page/post or upgrade your plan via the Accelerator dashboard to prioritize more pages."));
				} else {
					$page_level_settings = PegasaasUtils::get_object_meta($_POST['resource_id'], "accelerator_overrides", true);
					$page_level_settings['prioritized'] = 1;
					
					$index_position = PegasaasUtils::get_pages_and_posts_array_position($_POST['resource_id']);
					if ($index_position != NULL) {
						PegasaasUtils::$all_pages_and_posts["$index_position"]->prioritized = true;
						$pegasaas->data_storage->set("all_pages_and_posts", PegasaasUtils::$all_pages_and_posts);
					}
					
					
					$this->utils->update_object_meta($_POST['resource_id'], "accelerator_overrides", $page_level_settings);

					$prioritized_pages["{$_POST['resource_id']}"] = 1;
		
					update_option("pegasaas_prioritized_pages", $prioritized_pages);
					
					//var_dump(get_option("pegasaas_accelerated_pages"));
					print json_encode(array("status" => 1, 
											"prioritized_pages" => $prioritized_pages));
					//$this->set_prioritized_pages();
				}
			}
			
		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response
	}	
	
	
	
	function get_active_conflicting_plugins() {
		
		$all_plugins = get_plugins();
		$known_conflicting_plugins 	= array("async-javascript/async-javascript.php", 				/* redundant functionality */
										    "piio-image-optimization/piio-image-optimization.php", 	/* redundant/conflicting functionality */
										    "autoptimize/autoptimize.php", 							/* redundant/conflicting functionality */
										    "wp-fastest-cache/wpFastestCache.php", 					/* redundant/conflicting functionality */
											"wp-super-cache/wp-cache.php",							/* redundant/conflicting functionality */
										   "wp-rocket/wp-rocket.php", 								/* redundant/conflicting functionality */
										   "wp-speed-of-light/wp-speed-of-light.php", 				/* redundant/conflicting functionality */
										   "a3-lazy-load/a3-lazy-load.php", 						/* redundant/conflicting functionality */
										   "w3-total-cache/w3-total-cache.php", 					/* redundant/conflicting functionality */
										   "hyper-cache/plugin.php", 								/* redundant/conflicting functionality */
									  	 	"hummingbird-performance/wp-hummingbird.php", 			/* redundant/conflicting functionality */
									  	 	"wp-hummingbird/wp-hummingbird.php", 				    /* redundant/conflicting functionality */
									  	 	"wp-smush-pro/wp-smush.php", 				    	    /* redundant/conflicting functionality */
									  	 	"wp-smush/wp-smush.php", 				    	    /* redundant/conflicting functionality */
										   "comet-cache/comet-cache.php", 							/* redundant/conflicting functionality */
										   "psn-pagespeed-ninja/pagespeedninja.php", 				/* redundant/conflicting functionality */
										   "fast-velocity-minify/fvm.php", 							/* redundant functionality */
										   "speed-booster-pack/speed-booster-pack.php", 				/* redundant functionality */
										   "litespeed-cache/litespeed-cache.php", 				/* redundant/conflicting functionality */
											"wp-fastest-cache-premium/wpFastestCachePremium.php", /* redundant/conflicting functionality */
												"wp-asset-clean-up/wpacu.php", /* redundant/conflicting functionality */
											"page-optimize/page-optimize.php",
											
											'swift-performance-lite/performance.php'
										   );
		$supported_caching_plugins 	= $this->cache->get_supported_caching_plugins();

		$active_conflicting_plugins = array();
		foreach ($all_plugins as $plugin_file => $plugin_info) {
			if (is_plugin_active($plugin_file)) {
				
				if (strstr($plugin_file, "cache") ||
					in_array($plugin_file, $known_conflicting_plugins)
				   ) {
					
					$supported = false;
					foreach ($supported_caching_plugins as $supported_plugin) {
						if (strstr($plugin_file, $supported_plugin)) {
							$supported = true;
							break;
						}
					}
					if (!$supported) {
						$active_conflicting_plugins["$plugin_file"] = $plugin_info;
					}
				} 
			}
		}	
		 
		foreach ($active_conflicting_plugins as $plugin_file => $plugin_info) {
			
		}
		return $active_conflicting_plugins;
		
	}
	
	
	
	static function are_write_permissions_insufficient() {
		global $pegasaas;
		if (!$pegasaas->is_htaccess_writable() || !$pegasaas->is_cache_writable() || !$pegasaas->is_log_writable()) {
			return true;
		} else {
			return false;
		}
	}
	
	function is_htaccess_writable() {
	
		if ($this->is_apache() || $this->is_litespeed()) {
			
			$htaccess_file_location = $this->get_home_path().".htaccess";

			$writable = is_writable($htaccess_file_location);
	
			// if the file is not initially writable then attempt to change the permissions
			if (!$writable) {
				chmod($htaccess_file_location, 0644);
				$writable = is_writable($htaccess_file_location);
				chmod($htaccess_file_location, 0444); 
			}
			return $writable;
		} else {

			return true;
		}
	
	}
	
	function is_cache_writable() {
		$cache_folder =  PEGASAAS_CACHE_FOLDER_PATH."/";
		if (is_dir($cache_folder) && is_writable($cache_folder)) {
			return true;
		} else if (is_writable(WP_CONTENT_DIR)) {
			if (!is_dir($cache_folder)) {
				$this->cache->mkdirtree($cache_folder, 0755, true);
			}
			return is_dir($cache_folder) && is_writable($cache_folder);
			
		} else {
			return false;
		}
	}
	
	function do_wordlift() {
		return false;
	}
	
	function pegasaas_fetch_accelerated_pages_list() {
	
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$accelerated_pages = get_option("pegasaas_accelerated_pages", array());

			print json_encode($accelerated_pages);

		} else {
			print json_encode(array("status" => 0, "message" => 'Error: Invalid API Key'));
		}
		wp_die(); // required in order to not have a trailing 0 in the json response			
		
	}
	
	function is_log_writable() {
		$log_file_location 	= PEGASAAS_ACCELERATOR_DIR."/log.txt";
		if (!file_exists($log_file_location) && is_writable(PEGASAAS_ACCELERATOR_DIR)) {
			$fp 		= fopen($log_file_location, "a+");
			fwrite($fp, $message);
			fclose($fp);
			
		}
		return is_writable($log_file_location);
	}
	
	function get_plugin_file() {
		return (PEGASAAS_ACCELERATOR_DIR."pegasaas-accelerator-wp.php");
	}
	
	function permalink_structure_updated($old_value, $new_value) {
		
		$this->set_caching(PegasaasAccelerator::$settings['settings']['page_caching']['status'], true);

	}
	
	
	function render_developer_inspect_interface() {
		global $pegasaas; 
		global $wp_query;
		ob_end_clean();
		$resource_id 				= PegasaasUtils::get_object_id();
		

		$post_id = url_to_postid($resource_id);
	
		$object = get_post($post_id);
		
		$page_score_data 			= $pegasaas->scanner->pegasaas_fetch_pagespeed_last_scan($resource_id);
		$page_benchmark_score_data 	= $pegasaas->scanner->pegasaas_fetch_pagespeed_benchmark_last_scan($resource_id);

		$view_mode = $_COOKIE['pegasaas_display_mode'] == "" || $_COOKIE['pegasaas_display_mode'] == "multi-mode" ? "mobile-mode" : $_COOKIE['pegasaas_display_mode']; 
		$configurable_settings 		= array("page_caching",  
											"defer_render_blocking_js", "defer_render_blocking_css", 
											"defer_unused_css",
											"image_optimization", 
											"inject_critical_css", 
											"lazy_load_images", "lazy_load_background_images", "lazy_load_scripts", "lazy_load_youtube", "lazy_load", 
											"minify_html", "minify_css", "minify_js");
		?>
<html>
	<head>
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="<?php echo PEGASAAS_ACCELERATOR_URL; ?>assets/css/admin.css">
		<link rel="stylesheet" href="<?php echo PEGASAAS_ACCELERATOR_URL; ?>assets/css/posts.css">
		<link rel="stylesheet" href="<?php echo PEGASAAS_ACCELERATOR_URL; ?>assets/css/page-post-options.css">
		<link rel="stylesheet" href="<?php echo PEGASAAS_ACCELERATOR_URL; ?>assets/css/inspector.css">
		<script type='text/javasript' href="<?php echo PEGASAAS_ACCELERATOR_URL; ?>assets/js/pegasaas-accelerator.js"></script>


	
	</head>
	<body>
	  <div id="developer-inspector">
		<div class='admin-bar-container'>
		  <div id='pegasaas-branding'>
			  <div id="pegasaas-icon"></div>
			  <span id="tool-title"><?php _e( 'Accelerator Development Mode Inspector', 'pegasaas-accelerator' ); ?></span>	
		  </div>
		
		  <div id="view-chooser">
			 <div id="tab-accelerated" class='tab tab-selected' rel='accelerated-page-container'>Optimized</div><div id="tab-original" class='tab' rel='original-page-container'>Original</div>
		  </div>
		  <div id="mode-chooser">
			 <div id="mode-desktop" class='mode <?php if ($view_mode == "desktop-mode") { print "mode-selected"; } ?>' rel='desktop-mode'><i class='fa fa-desktop'></i> Desktop</div><div id="tab-mobile" class='mode <?php if ($view_mode == "mobile-mode") { print "mode-selected"; } ?>' rel='mobile-mode'><i class='fa fa-mobile'></i> Mobile</div>
		  </div>				
		</div>
		<div id="preview-container" class='<?php echo $view_mode; ?>'>
			<div id='original-page-container' class='preview-container'>
				<div class='page-admin-bar'>
					<div class='page-url'>
					<?php echo $resource_id; ?> (Original)
					</div>
					<span class='mobile-only'>
						
						<div class='metric metric-pagespeed' title='PageSpeed Score'><?php echo $page_benchmark_score_data['mobile_score']; ?></div>
						<div class='metric metric-fcp' title='First Contentful Paint'><?php echo $page_benchmark_score_data['meta']['mobile']['lab_data']['first-contentful-paint']['displayValue']; ?></div>
						<div class='metric metric-fmp' title='First Meaningful Paint'><?php echo $page_benchmark_score_data['meta']['mobile']['lab_data']['first-meaningful-paint']['displayValue']; ?></div>
						<div class='metric metric-si' title='Speed Index'><?php echo $page_benchmark_score_data['meta']['mobile']['lab_data']['speed-index']['displayValue']; ?></div>
				<!--		<div class='metric metric-fci' title='First CPU Idle'><?php echo $page_benchmark_score_data['meta']['mobile']['lab_data']['first-cpu-idle']['displayValue']; ?></div>-->
						<div class='metric metric-tti' title='Time To Interactive'><?php echo $page_benchmark_score_data['meta']['mobile']['lab_data']['interactive']['displayValue']; ?></div>
					</span>
					<span class='desktop-only'>
						
						<div class='metric metric-pagespeed' title='PageSpeed Score'><?php echo $page_benchmark_score_data['score']; ?></div>
						<div class='metric metric-fcp' title='First Contentful Paint'><?php echo $page_benchmark_score_data['meta']['desktop']['lab_data']['first-contentful-paint']['displayValue']; ?></div>
						<div class='metric metric-fmp' title='First Meaningful Paint'><?php echo $page_benchmark_score_data['meta']['desktop']['lab_data']['first-meaningful-paint']['displayValue']; ?></div>
						<div class='metric metric-si' title='Speed Index'><?php echo $page_benchmark_score_data['meta']['desktop']['lab_data']['speed-index']['displayValue']; ?></div>
					<!--	<div class='metric metric-fci' title='First CPU Idle'><?php echo $page_benchmark_score_data['meta']['desktop']['lab_data']['first-cpu-idle']['displayValue']; ?></div>-->
						<div class='metric metric-tti' title='Time To Interactive'><?php echo $page_benchmark_score_data['meta']['desktop']['lab_data']['interactive']['displayValue']; ?></div>
					</span>
				</div>
				<div class='iframe-container'>
				  <div class='inner'>
					<iframe src="<?php echo $resource_id; ?>?accelerate=off"></iframe>
					</div>
				</div>
			</div>
			<div id='accelerated-page-container' class='preview-container container-active'>
				<div class='page-admin-bar'>
					<div class='page-url'>
					<?php echo $resource_id; ?> (Optimized)
					</div>
					<span class='mobile-only'>
						
						<div class='metric metric-pagespeed' title='PageSpeed Score'><?php echo $page_score_data['mobile_score']; ?></div>
						<div class='metric metric-fcp' title='First Contentful Paint'><?php echo $page_score_data['meta']['mobile']['lab_data']['first-contentful-paint']['displayValue']; ?></div>
						<div class='metric metric-fmp' title='First Meaningful Paint'><?php echo $page_score_data['meta']['mobile']['lab_data']['first-meaningful-paint']['displayValue']; ?></div>
						<div class='metric metric-si' title='Speed Index'><?php echo $page_score_data['meta']['mobile']['lab_data']['speed-index']['displayValue']; ?></div>
						<div class='metric metric-fci' title='First CPU Idle'><?php echo $page_score_data['meta']['mobile']['lab_data']['first-cpu-idle']['displayValue']; ?></div>
						<div class='metric metric-tti' title='Time To Interactive'><?php echo $page_score_data['meta']['mobile']['lab_data']['interactive']['displayValue']; ?></div>
					</span>
					<span class='desktop-only'>
						
						<div class='metric metric-pagespeed' title='PageSpeed Score'><?php echo $page_score_data['score']; ?></div>
						<div class='metric metric-fcp' title='First Contentful Paint'><?php echo $page_score_data['meta']['desktop']['lab_data']['first-contentful-paint']['displayValue']; ?></div>
						<div class='metric metric-fmp' title='First Meaningful Paint'><?php echo $page_score_data['meta']['desktop']['lab_data']['first-meaningful-paint']['displayValue']; ?></div>
						<div class='metric metric-si' title='Speed Index'><?php echo $page_score_data['meta']['desktop']['lab_data']['speed-index']['displayValue']; ?></div>
						<div class='metric metric-fci' title='First CPU Idle'><?php echo $page_score_data['meta']['desktop']['lab_data']['first-cpu-idle']['displayValue']; ?></div>
						<div class='metric metric-tti' title='Time To Interactive'><?php echo $page_score_data['meta']['desktop']['lab_data']['interactive']['displayValue']; ?></div>
					</span>	
					
					<div class='configure-button-container'><a id="config-button"><i class='fa fa-cog'></i></a></div>
					<button id="save-cache-button" class='button' <?php if ($this->in_development_mode()) { print "disabled title='Disabled while in development mode.'"; } ?>>Save Page</button>
					<button id="reload-page-button" class='button'>Reload Preview</button>
					<button id="save-page-button" disabled class='button'>Save Settings<i class='fa fa-spin fa-spinner'></i></button>
				</div>
				<div id="configuration-settings" class="hidden">
					<div class='inner'>
						<form>
							<input type='hidden' name='action'  value='pegasaas_save_page_configurations' />
							<input type='hidden' name='api_key' value='<?php echo PegasaasAccelerator::$settings['api_key']; ?>' />
							<input type='hidden' name='resource_id' value='<?php echo $resource_id; ?>' />
			<?php
		foreach ($configurable_settings as $feature) { 
		$feature_settings = PegasaasAccelerator::$settings['settings']["$feature"];
		if (isset($feature_settings['status'])) {
			?>
	<div class='pegasaas-feature-box-sidebar'>
		<div class='pegasaas-subsystem-title'>
			<?php echo str_replace("Optimize Images", "Image Optimization", str_replace("Blocking JavaScript", "Blocking JS", $feature_settings['name'])); ?>
			<i class='fa fa-fw fa-angle-down feature-box-toggle'></i>
		</div>
					
				<div class='pegasaas-accelerator-subsystem-feature-description'>
				<?php if ($feature == "page_caching") { ?>
				<p>If you have a page that contains dynamic parts, such as an e-commerce page that has cart information, you can DISABLE page caching here.  This may cause the 'server response time' to be a be quite a bit higher.</p>
				<select class='form-control' name="pegasaas_accelerator__page_caching">
					<option value='-1'>Default (<?php if ($system_settings['settings']['page_caching']['status'] == 0) { ?>Disabled<?php } else { ?>Enabled<?php } ?>)</option>
					<?php if ($system_settings['settings']['page_caching']['status'] == 1) { ?>
					<option <?php if (@$page_level_configurations['page_caching'] == "0") { ?>selected<?php } ?> value='0'>Explicitly Disabled</option>
					<!--<option <?php if (@$page_level_configurations['page_caching'] == "4") { ?>selected<?php } ?> value='4'>Explicitly Enabled (File Based served by WP)</option>
					<option <?php if (@$page_level_configurations['page_caching'] == "2") { ?>selected<?php } ?> value='2'>Explicitly Enabled (Stored in Database)</option> -->
					<?php } ?>
				</select>
				<?php } else if ($feature == "minify_html") { ?>
				<p>Minifying your Pages or Posts HTML can reduce the time-to-transfer by as much as 50%.  You can disable HTML if you find you want to visually debug your HTML code.</p>
				<select class='form-control' name="pegasaas_accelerator__minify_html">
					<option value='-1'>Default (<?php if ($system_settings['settings']['minify_html']['status'] == 0) { ?>Disabled<?php } else { ?>Enabled<?php } ?>)</option>
					<option <?php if (@$page_level_configurations['minify_html'] == "0") { ?>selected<?php } ?> value='0'>Explicitly Disabled</option>
					<option <?php if (@$page_level_configurations['minify_html'] == "1") { ?>selected<?php } ?> value='1'>Explicitly Enabled</option>
				</select>
				<?php } else if ($feature == "minify_css") { ?>
					<p>By minifying any inline CSS, you can further reduce the size and load time of your page.</p>
				<select class='form-control' name="pegasaas_accelerator__minify_css">
					<option value='-1'>Default (<?php if ($system_settings['settings']['minify_css']['status'] == 0) { ?>Disabled<?php } else { ?>Enabled<?php } ?>)</option>
					<option <?php if (@$page_level_configurations['minify_css'] == "0") { ?>selected<?php } ?> value='0'>Explicitly Disabled</option>
					<option <?php if (@$page_level_configurations['minify_css'] == "1") { ?>selected<?php } ?> value='1'>Explicitly Enabled</option>
				</select>
				<?php } else if ($feature == "minify_js") { ?>
				<p>By minifying any inline JavaScript, you can further reduce the size and load time of your page.</p>
				<select class='form-control' name="pegasaas_accelerator__minify_js">
					<option value='-1'>Default (<?php if ($system_settings['settings']['minify_js']['status'] == 0) { ?>Disabled<?php } else { ?>Enabled<?php } ?>)</option>
					<option <?php if (@$page_level_configurations['minify_js'] == "0") { ?>selected<?php } ?> value='0'>Explicitly Disabled</option>
					<option <?php if (@$page_level_configurations['minify_js'] == "1") { ?>selected<?php } ?> value='1'>Explicitly Enabled</option>
				</select> 
				<?php } else if ($feature == "image_optimization") { ?>
				<p>If you require your images to not be optimized on this page, you can change the image optimization settings for this page here.</p>
				<select class='form-control' name="pegasaas_accelerator__image_optimization">
					<option value='-1'>Default (<?php if ($system_settings['settings']['image_optimization']['status'] == 0) { ?>Disabled<?php } else { ?>Enabled<?php } ?>)</option>
					<option <?php if (@$page_level_configurations['image_optimization'] == "0") { ?>selected<?php } ?> value='0'>Explicitly Disabled</option>
					<option <?php if (@$page_level_configurations['image_optimization'] == "1") { ?>selected<?php } ?> value='1'>Explicitly Enabled</option>
				</select>
				<?php } else if ($feature == "defer_render_blocking_js") { ?>
				<p>To make the page begin its render as fast as possible, render-blocking javascript should be deferred.  In the event that javascript functionality ont his page is broken due to the deferral, you can try disabling this feature.</p>
				
				<select class='form-control' name="pegasaas_accelerator__defer_render_blocking_js">
					<option value='-1'>Default (<?php 
		if ($system_settings['settings']['defer_render_blocking_js']['status'] == 0) { 
		    print __("Disabled");
		} else if ($system_settings['settings']['defer_render_blocking_js']['status'] == "2") { 
			print __("Deferred externally at end of page");
		} else if ($system_settings['settings']['defer_render_blocking_js']['status'] == "3") { 
			print __("Deferred internally at end of page");
		} else if ($system_settings['settings']['defer_render_blocking_js']['status'] == "4") { 
			print __("Defer all files and inline blocks at original DOM location");
		} else { 
			print __("Defer all files at original DOM location and externally defer assembled inline blocks at end of page");
		} ?>)
					</option>
					<option <?php if (@$page_level_configurations['defer_render_blocking_js'] == "0") { ?>selected<?php } ?> value='0'>Explicitly Disabled</option>
					
					<option <?php if (@$page_level_configurations['defer_render_blocking_js'] == "2") { ?>selected<?php } ?> value='2'>Explicitly deferred externally at end of page</option>
					<option <?php if (@$page_level_configurations['defer_render_blocking_js'] == "3") { ?>selected<?php } ?> value='3'>Explicitly deferred inline at end of page</option>
					<option <?php if (@$page_level_configurations['defer_render_blocking_js'] == "4") { ?>selected<?php } ?> value='4'>Externally defer all files and inline blocks at original DOM locatione</option>
					<option <?php if (@$page_level_configurations['defer_render_blocking_js'] == "5") { ?>selected<?php } ?> value='5'>Externally defer all files at original DOM location and defer assembled inline blocks at end of page (default)</option>
				</select>
					
					
				<?php } else if ($feature == "defer_render_blocking_css") { ?>
				<select class='form-control' name="pegasaas_accelerator__defer_render_blocking_css">
					<option value='-1'>Default (<?php if ($system_settings['settings']['defer_render_blocking_css']['status'] == 0) { ?>Disabled<?php } else { ?>Enabled<?php } ?>)</option>
					<option <?php if (@$page_level_configurations['defer_render_blocking_css'] == "0") { ?>selected<?php } ?> value='0'>Explicitly Disabled</option>
					<option <?php if (@$page_level_configurations['defer_render_blocking_css'] == "1") { ?>selected<?php } ?> value='1'>Explicitly Enabled</option>
				</select>
					
				<?php } else if ($feature == "defer_unused_css") { ?>
				<p>To initiate the page render as fast as possible, unused CSS should be deferred.  In order to use this feature, render-blocking css needs to be deferred.</p>
				<select class='form-control' name="pegasaas_accelerator__defer_unused_css">
					<option value='-1'>Default (<?php if ($system_settings['settings']['defer_unused_css']['status'] == 0) { ?>Disabled<?php } else { ?>Enabled<?php } ?>)</option>
					<option <?php if (@$page_level_configurations['defer_unused_css'] == "0") { ?>selected<?php } ?> value='0'>Explicitly Disabled</option>
					<option <?php if (@$page_level_configurations['defer_unused_css'] == "1") { ?>selected<?php } ?> value='1'>Explicitly Enabled (Default)</option>
					<option <?php if (@$page_level_configurations['defer_unused_css'] == "3") { ?>selected<?php } ?> value='3'>Explicitly Enabled - As Soon as Possible</option>
					<option <?php if (@$page_level_configurations['defer_unused_css'] == "4") { ?>selected<?php } ?> value='4'>Explicitly Enabled - After 1.5 Seconds</option>
					<option <?php if (@$page_level_configurations['defer_unused_css'] == "5") { ?>selected<?php } ?> value='5'>Explicitly Enabled - After 5 Seconds</option>
					<option <?php if (@$page_level_configurations['defer_unused_css'] == "2") { ?>selected<?php } ?> value='2'>Explicitly Enabled - On Scroll/Click</option>
				</select>
					
					
					
				<?php } else if ($feature == "inject_critical_css") { ?>
				<p>By injecting Critical Path CSS into your page, you can avoid the Flash of Unstyled Content.</p>
				<select class='form-control' name="pegasaas_accelerator__inject_critical_css">
					<option value='-1'>Default (<?php 
		if ($system_settings['settings']['inject_critical_css']['status'] == 0) { 
						
						?>Disabled<?php 
		} else if ($system_settings['settings']['inject_critical_css']['status'] == "3" || $system_settings['settings']['inject_critical_css']['status'] == "1") { 
						?>Before first &lt;link&gt; tag<?php
		} else { ?>Before closing &lt;/head&gt; tag<?php } ?>)
					</option>
					<option <?php if (@$page_level_configurations['inject_critical_css'] == "0") { ?>selected<?php } ?> value='0'>Explicitly Disabled</option>
					
					<option <?php if (@$page_level_configurations['inject_critical_css'] == "2") { ?>selected<?php } ?> value='2'>Explicitly before closing &lt;/head&gt; tag</option>
					<option <?php if (@$page_level_configurations['inject_critical_css'] == "3") { ?>selected<?php } ?> value='3'>Explicitly before first &lt;style&gt; tag</option>
				</select>
					
				<div>

				  <!-- Nav tabs -->
				  <ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#post-type-cpcss" aria-controls="home" role="tab" data-toggle="tab">Post Type</a></li>
					<li role="presentation"><a href="#page-level-cpcss" aria-controls="profile" role="tab" data-toggle="tab">Override</a></li>
				  </ul>
					<?php	
					$post_type = get_post_type($object->ID);			
					$page_level_cpcss	= PegasaasUtils::get_object_meta($resource_id, 'critical_css');
					$post_type_cpcss 	= PegasaasUtils::get_object_meta("post_type__{$post_type}", 'critical_css');

					if ($slug  == "/") {
						$post_type_cpcss_exists = PegasaasUtils::get_object_meta("post_type__home_page", 'critical_css');
					}
				?>
	
				  <!-- Tab panes -->
				  <div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="post-type-cpcss">
					  <textarea class='pegasaas-feature-critical-css-textarea' name="pegasaas_accelerator__post_type_cpcss"><?php echo $post_type_cpcss['css']; ?></textarea>
					  
					</div>
					<div role="tabpanel" class="tab-pane" id="page-level-cpcss">
					  <textarea class='pegasaas-feature-critical-css-textarea' name="pegasaas_accelerator__page_level_cpcss"><?php echo $page_level_cpcss['css']; ?></textarea>
					  
					</div>
				  </div>

				</div>
					
					
				<?php } else if ($feature == "lazy_load") { ?>
				<p>Occassionally, lazy loading iframes can cause regions of your pages to not render properly.  If this happens with your iframes, try disabling the lazy load function.</p>
				<select class='form-control' name="pegasaas_accelerator__lazy_load">
					<option value='-1'>Default (<?php 
		if ($system_settings['settings']['lazy_load']['status'] == 0) { 
						
						?>Disabled<?php 		
		} else { ?>Enabled<?php } ?>)
					</option>
					<option <?php if (@$page_level_configurations['lazy_load'] == "0") { ?>selected<?php } ?> value='0'>Explicitly Disabled</option>
					<option <?php if (@$page_level_configurations['lazy_load'] == "1") { ?>selected<?php } ?> value='1'>Explicitly Enabled</option>				
					</select>
				<?php } else if ($feature == "lazy_load_scripts") { ?>
				<p>If there is a global lazy loaded script is not firing, you can disable script lazy loading for your page here.</p>
				<select class='form-control' name="pegasaas_accelerator__lazy_load_scripts">
					<option value='-1'>Default (<?php 
		if ($system_settings['settings']['lazy_load_scripts']['status'] == 0) { 
						
						?>Disabled<?php 		
		} else { ?>Enabled<?php } ?>)
					</option>
					<option <?php if (@$page_level_configurations['lazy_load_scripts'] == "0") { ?>selected<?php } ?> value='0'>Explicitly Disabled</option>
					<option <?php if (@$page_level_configurations['lazy_load_scripts'] == "1") { ?>selected<?php } ?> value='1'>Explicitly Enabled</option>				
					</select>
				<?php } else if ($feature == "lazy_load_background_images") { ?>
				<p>If you find that the background images of your page are not loading, you can try disabling the lazy loading of background images here.</p>
				<select class='form-control' name="pegasaas_accelerator__lazy_load_background_images">
					<option value='-1'>Default (<?php 
		if ($system_settings['settings']['lazy_load_background_images']['status'] == 0) { 
						
						?>Disabled<?php 		
		} else { ?>Enabled<?php } ?>)
					</option>
					<option <?php if (@$page_level_configurations['lazy_load_background_images'] == "0") { ?>selected<?php } ?> value='0'>Explicitly Disabled</option>
					<option <?php if (@$page_level_configurations['lazy_load_background_images'] == "1") { ?>selected<?php } ?> value='1'>Explicitly Enabled</option>				
					</select>
				<?php } else if ($feature == "lazy_load_images") { ?>
				<p>Occassionally, lazy loading images can cause a regions of your pages to not render properly.  If this happens with your page, try disabling the lazy load function.</p>
				<select class='form-control' name="pegasaas_accelerator__lazy_load_images">
					<option value='-1'>Default (<?php 
		if ($system_settings['settings']['lazy_load_images']['status'] == 0) { 
						
						?>Disabled<?php 		
		} else { ?>Enabled<?php } ?>)
					</option>
					<option <?php if (@$page_level_configurations['lazy_load_images'] == "0") { ?>selected<?php } ?> value='0'>Explicitly Disabled</option>
					<option <?php if (@$page_level_configurations['lazy_load_images'] == "1") { ?>selected<?php } ?> value='1'>Explicitly Enabled</option>				
					</select>
				<?php } else if ($feature == "lazy_load_youtube") { ?>
				<p>Change the page level setting for lazy loading youtube, here, if you want this page to lazy load youtube differently from the global setting for the site.</p>
				<select class='form-control' name="pegasaas_accelerator__lazy_load_youtube">
					<option value='-1'>Default (<?php 
		if ($system_settings['settings']['lazy_load_youtube']['status'] == 0) { 
						
						?>Disabled<?php 		
		} else { ?>Enabled<?php } ?>)
					</option>
					<option <?php if (@$page_level_configurations['lazy_load_youtube'] == "0") { ?>selected<?php } ?> value='0'>Explicitly Disabled</option>
					<option <?php if (@$page_level_configurations['lazy_load_youtube'] == "1") { ?>selected<?php } ?> value='1'>Explicitly Enabled</option>				
					</select>
					<?php } ?>
					</div>
	</div>
		<?php } 
	}?>
					
					
					
						</form>
					</div>
				</div>
				<div class='iframe-container'>
				  <div class='inner'>
					<iframe src="<?php echo $resource_id; ?>?accelerate=on"></iframe>
				  </div>
				</div>
			</div>	
		</div>
	</div>
		<script src='<?php echo get_home_url(); ?>/wp-admin/load-scripts.php?c=0&amp;load%5B%5D=jquery-core,jquery-migrate,utils' type='text/javascript'></script>
		<script type='text/javascript' src="<?php echo PEGASAAS_ACCELERATOR_URL; ?>assets/js/pegasaas-accelerator.js"></script>
		<script>
		  jQuery(document).ready(function() {
			 jQuery(".tab").bind("click", function() {
				 jQuery("#view-chooser .tab").removeClass("tab-selected"); 
				 jQuery(this).addClass("tab-selected");
				 jQuery("#preview-container .preview-container").removeClass("container-active");
				 var target_container = jQuery(this).attr("rel");
				 jQuery("#" + target_container).addClass("container-active");
				 
			 });
			  jQuery(".mode").bind("click", function() {
				 jQuery("#mode-chooser .mode").removeClass("mode-selected"); 
				 jQuery(this).addClass("mode-selected");
				 jQuery("#preview-container").removeClass("desktop-mode");
				 jQuery("#preview-container").removeClass("mobile-mode");
				 var new_mode = jQuery(this).attr("rel");
				 jQuery("#preview-container").addClass(new_mode);
				 
			 });
			  
			jQuery("#config-button").bind("click", function() {
			
				if (jQuery("#configuration-settings").hasClass("hidden")) {
					show_configurations_container();
				} else {
					hide_configurations_container();
				}
				
			});			  
			  
			  
			jQuery("#configuration-settings .form-control").bind("change", function() {
				
				jQuery("#save-page-button").removeAttr("disabled");
				jQuery("#reload-page-button").attr("disabled", true);
				
			});
			  
			jQuery("#save-page-button").bind("click", function() {
			    jQuery(this).attr("disabled", true);
				jQuery(this).addClass("working");
				var arguments = jQuery("#configuration-settings form").serializeArray();
				/*
				  arguments['action'] = 'pegasaas_save_page_configurations';
				  arguments['action'] = api_key;
				  arguments.resource_id = resource_id;
				
				  */
				  jQuery.post(ajaxurl, 
						arguments, 
						function(data) {
							jQuery("#save-page-button").removeClass("working");
							jQuery("#reload-page-button").removeAttr("disabled");
					  	//	jQuery("#save-cache-button").removeAttr("disabled");
						}, 
						
						"json");				  
			  });
		  });
			
			jQuery("#reload-page-button").bind("click", function() {
				var src = jQuery("#accelerated-page-container iframe").attr("src");
				jQuery("#accelerated-page-container iframe").attr("src", src);
			});
			jQuery("#save-cache-button").bind("click", function() {
				
				//jQuery(this).attr("disabled", true);
				jQuery(this).addClass("working");
				var arguments = { action: 'pegasaas_build_page_cache', api_key: api_key, resource_id: resource_id };
				jQuery.post(ajaxurl, 
						arguments, 
						function(data) {
							jQuery("#save-cache-button").removeClass("working");
							//jQuery("#reload-page-button").removeAttr("disabled");
						}, 
						
						"json");
				
			});			
		
			function hide_configurations_container() {
				jQuery("#configuration-settings").addClass("hidden");
			}
			function show_configurations_container() {
				jQuery("#configuration-settings").removeClass("hidden");
			}	
			
			function save_settings() {
				
				
			}
			var ajaxurl = "<?php echo admin_url("admin-ajax.php"); ?>";
			var api_key =  "<?php echo PegasaasAccelerator::$settings['api_key']; ?>";
			var resource_id =  "<?php echo $resource_id; ?>";
		

		</script>
	</body>
</html>
		<?php
		exit;
	}
	
	function is_multi_server_installation() {

		$ips = PegasaasAccelerator::$settings['settings']['multi_server']['ips'];
		
		if (is_array($ips) && sizeof($ips) > 1) {
			return true;
		}
		return false;
	}
} // end class
endif;

?>