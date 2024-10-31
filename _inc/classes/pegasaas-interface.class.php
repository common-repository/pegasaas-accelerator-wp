<?php
class PegasaasInterface {
	var $current_results_page = 1;
	var $max_results_page = 0; // this is set in $pegasaas->scanner->get_scanned_objects
	var $results_per_page = 25;
	var $results_post_type = "";
	var $results_issue_filter = "";
	
	
	function __construct() {
		
		if (isset($_COOKIE['pegasaas_results_per_page']) && $_COOKIE['pegasaas_results_per_page'] == "all") {
			$this->results_per_page = "";
		} else if (isset($_COOKIE['pegasaas_results_per_page']) && $_COOKIE['pegasaas_results_per_page'] != "") {
			$this->results_per_page = $_COOKIE['pegasaas_results_per_page'] + 0;
		}
		
		if (isset($_COOKIE['pegasaas_current_results_page']) && $_COOKIE['pegasaas_current_results_page'] != "") {
			$this->current_results_page = $_COOKIE['pegasaas_current_results_page'] + 0;
		}	

		
		if (isset($_COOKIE['pegasaas_results_post_type']) && strtolower($_COOKIE['pegasaas_results_post_type']) == "all") {
			$this->results_post_type = "";
		} else if (isset($_COOKIE['pegasaas_results_post_type']) && $_COOKIE['pegasaas_results_post_type'] != "") {
			$this->results_post_type = $_COOKIE['pegasaas_results_post_type'];
		}
		
		if (isset($_COOKIE['pegasaas_results_issue_filter']) && $_COOKIE['pegasaas_results_issue_filter'] == "any") {
			$this->results_issue_filter = "";
		} else if (isset($_COOKIE['pegasaas_results_issue_filter']) && $_COOKIE['pegasaas_results_issue_filter'] != "") {
			$this->results_issue_filter = $_COOKIE['pegasaas_results_issue_filter'];
		}	
		//var_dump($_COOKIE);
		if (isset($_COOKIE['pegasaas_results_search_filter']) && $_COOKIE['pegasaas_results_search_filter'] == "") {
			$this->results_search_filter = "";
		} else if (isset($_COOKIE['pegasaas_results_search_filter']) && $_COOKIE['pegasaas_results_search_filter'] != "") {
			$this->results_search_filter = trim($_COOKIE['pegasaas_results_search_filter']);
		}	
		
		

		// web perf metrics
		if (isset($_COOKIE['pegasaas_web_perf_metrics_ttfb']) && $_COOKIE['pegasaas_web_perf_metrics_ttfb'] == "") {
			$this->web_perf_metrics_ttfb_class = "";
		} else if (isset($_COOKIE['pegasaas_web_perf_metrics_ttfb']) && $_COOKIE['pegasaas_web_perf_metrics_ttfb'] == "0") {
			$this->web_perf_metrics_ttfb_class = "hidden-advanced";
		} else if (isset($_COOKIE['pegasaas_web_perf_metrics_ttfb']) && $_COOKIE['pegasaas_web_perf_metrics_ttfb'] == "1") {
			$this->web_perf_metrics_ttfb_class = "";
		}else {
			$this->web_perf_metrics_ttfb_class = "";
		}			
		
		if (isset($_COOKIE['pegasaas_web_perf_metrics_fcp']) && $_COOKIE['pegasaas_web_perf_metrics_fcp'] == "") {
			$this->web_perf_metrics_fcp_class = "";
		} else if (isset($_COOKIE['pegasaas_web_perf_metrics_fcp']) && $_COOKIE['pegasaas_web_perf_metrics_fcp'] == "0") {
			$this->web_perf_metrics_fcp_class = "hidden-advanced";
		} else if (isset($_COOKIE['pegasaas_web_perf_metrics_fcp']) && $_COOKIE['pegasaas_web_perf_metrics_fcp'] == "1") {
			$this->web_perf_metrics_fcp_class = "";
		}else {
			$this->web_perf_metrics_fcp_class = "";
		}	
		
		if (isset($_COOKIE['pegasaas_web_perf_metrics_fmp']) && $_COOKIE['pegasaas_web_perf_metrics_fmp'] == "") {
			$this->web_perf_metrics_fmp_class = "hidden-advanced";
		} else if (isset($_COOKIE['pegasaas_web_perf_metrics_fmp']) && $_COOKIE['pegasaas_web_perf_metrics_fmp'] == "0") {
			$this->web_perf_metrics_fmp_class = "hidden-advanced";
		} else if (isset($_COOKIE['pegasaas_web_perf_metrics_fmp']) && $_COOKIE['pegasaas_web_perf_metrics_fmp'] == "1") {
			$this->web_perf_metrics_fmp_class = "";
		} else {
			$this->web_perf_metrics_fmp_class = "hidden-advanced";
		}			

		if (isset($_COOKIE['pegasaas_web_perf_metrics_si']) && $_COOKIE['pegasaas_web_perf_metrics_si'] == "") {
			$this->web_perf_metrics_si_class = "";
		} else if (isset($_COOKIE['pegasaas_web_perf_metrics_si']) && $_COOKIE['pegasaas_web_perf_metrics_si'] == "0") {
			$this->web_perf_metrics_si_class = "hidden-advanced";
		} else if (isset($_COOKIE['pegasaas_web_perf_metrics_si']) && $_COOKIE['pegasaas_web_perf_metrics_si'] == "1") {
			$this->web_perf_metrics_si_class = "";
		} else {
			$this->web_perf_metrics_si_class = "";
		}				

		if (isset($_COOKIE['pegasaas_web_perf_metrics_lcp']) && $_COOKIE['pegasaas_web_perf_metrics_lcp'] == "") {
			$this->web_perf_metrics_lcp_class = "";
		} else if (isset($_COOKIE['pegasaas_web_perf_metrics_lcp']) && $_COOKIE['pegasaas_web_perf_metrics_lcp'] == "0") {
			$this->web_perf_metrics_lcp_class = "hidden-advanced";
		} else if (isset($_COOKIE['pegasaas_web_perf_metrics_lcp']) && $_COOKIE['pegasaas_web_perf_metrics_lcp'] == "1") {
			$this->web_perf_metrics_lcp_class = "";
		} else {
			$this->web_perf_metrics_lcp_class = "";
		}	

		if (isset($_COOKIE['pegasaas_web_perf_metrics_tti']) && $_COOKIE['pegasaas_web_perf_metrics_tti'] == "") {
			$this->web_perf_metrics_tti_class = "";
		} else if (isset($_COOKIE['pegasaas_web_perf_metrics_tti']) && $_COOKIE['pegasaas_web_perf_metrics_tti'] == "0") {
			$this->web_perf_metrics_tti_class = "hidden-advanced";
		} else if (isset($_COOKIE['pegasaas_web_perf_metrics_tti']) && $_COOKIE['pegasaas_web_perf_metrics_tti'] == "1") {
			$this->web_perf_metrics_tti_class = "";
		} else {
			$this->web_perf_metrics_tti_class = "";
		}		

		if (isset($_COOKIE['pegasaas_web_perf_metrics_fci']) && $_COOKIE['pegasaas_web_perf_metrics_fci'] == "") {
			$this->web_perf_metrics_fci_class = "";
		} else if (isset($_COOKIE['pegasaas_web_perf_metrics_fci']) && $_COOKIE['pegasaas_web_perf_metrics_fci'] == "0") {
		
			$this->web_perf_metrics_fci_class = "hidden-advanced";
		} else  if (isset($_COOKIE['pegasaas_web_perf_metrics_fci']) && $_COOKIE['pegasaas_web_perf_metrics_fci'] == "1") {
			$this->web_perf_metrics_fci_class = "";
		} else {
			$this->web_perf_metrics_fci_class = "";
		}			
		
		if (isset($_COOKIE['pegasaas_web_perf_metrics_tbt']) && $_COOKIE['pegasaas_web_perf_metrics_tbt'] == "") {
			$this->web_perf_metrics_tbt_class = "hidden-advanced";
		} else if (isset($_COOKIE['pegasaas_web_perf_metrics_tbt']) && $_COOKIE['pegasaas_web_perf_metrics_tbt'] == "0") {
			$this->web_perf_metrics_tbt_class = "hidden-advanced";
		} else if (isset($_COOKIE['pegasaas_web_perf_metrics_tbt']) && $_COOKIE['pegasaas_web_perf_metrics_tbt'] == "1") {
			$this->web_perf_metrics_tbt_class = "";
		} else {
			$this->web_perf_metrics_tbt_class = "hidden-advanced";
		}			

		if (isset($_COOKIE['pegasaas_web_perf_metrics_cls']) && $_COOKIE['pegasaas_web_perf_metrics_cls'] == "") {
			$this->web_perf_metrics_cls_class = "hidden-advanced";
			
		} else if (isset($_COOKIE['pegasaas_web_perf_metrics_cls']) && $_COOKIE['pegasaas_web_perf_metrics_cls'] == "0") {
			$this->web_perf_metrics_cls_class = "hidden-advanced";
		} else if (isset($_COOKIE['pegasaas_web_perf_metrics_cls']) && $_COOKIE['pegasaas_web_perf_metrics_cls'] == "1") {
			$this->web_perf_metrics_cls_class = "";
		} else {
			$this->web_perf_metrics_cls_class = "hidden-advanced";
		}			
		//print $this->web_pef_petrics_cls_class."xyz";
		add_action( 'load-edit.php', array( $this, 'pages_posts__contextual_help' ) );
	}
	
	function get_advanced_visible_gauges_class() {
		
		$count = 7;
		$metrics = array ("ttfb", "fcp", "si", "lcp", "tti", "tbt", "cls");
		foreach ($metrics as $metric) {
			$variable = "web_perf_metrics_{$metric}_class";
			
			if ($this->{$variable} == "hidden-advanced") {
				$count--;
			}
		}
		if ($count == 8 || $count == 7  || $count == 6) {
			return "visible-gauges-{$count}";
		} else {
			return "";
		}
		
		
	}
	
	static function trial_bar() {
		global $pegasaas;
		
		if ($pegasaas->is_trial()) {
			$days_remaining = $pegasaas->trial_days_remaining();
			if ($days_remaining > 0) {
				include(PEGASAAS_ACCELERATOR_DIR."/_inc/admin-control-panel/warnings/trial-countdown.php");
		
			} else if ($days_remaining == 0) {			
				include(PEGASAAS_ACCELERATOR_DIR."/_inc/admin-control-panel/popovers/trial-expired.php");
			}
		} 
	}
	
	static function get_upgrade_link() {
		global $pegasaas;
		return "https://pegasaas.com/upgrade/?utm_source=pegasaas-accelerator-account-panel&upgrade_key=".PegasaasAccelerator::$settings['api_key']."-".PegasaasAccelerator::$settings['installation_id']."&site=".$pegasaas->utils->get_http_host()."&current=".PegasaasAccelerator::$settings['subscription'];
	}
	
	static function https_http_inconsistency_warning() {
		if (PegasaasUtils::https_http_inconsistency_exists()) {
			include(PEGASAAS_ACCELERATOR_DIR."/_inc/admin-control-panel/warnings/https-http-inconsistency.php");
  		}
	}
	
	static function write_permissions_warning() {
		global $pegasaas;
		if (PegasaasAccelerator::$settings['status'] == 1 && PegasaasAccelerator::are_write_permissions_insufficient()) {
			include(PEGASAAS_ACCELERATOR_DIR."/_inc/admin-control-panel/warnings/write-permissions-required-bar.php");
		}
		
	}
	
	function pages_posts__contextual_help() {
		global $pegasaas;
		$screen = get_current_screen();
		
		$plugin_name = "Pegasaas Accelerator";
		
		
		
		$build_cache_label = ($pegasaas->is_standard() ? 'Optimize Now' : 'Build Cache');
		$screen->add_help_tab( 
			array(
				/* translators: %s expands to Pegasaas */
				'title'    => sprintf( __( '%s Columns', 'pegasaas-accelerator' ), 'Web Performance' ),
				'id'       => 'pegasaas-columns',
				'content'  => sprintf(
					/* translators: %1$s: Yoast SEO, %2$s: Link to article about content analysis, %3$s: Anchor closing, %4$s: Link to article about text links, %5$s: Emphasis open tag, %6$s: Emphasis close tag */
					'<p>'.__('%1$s adds several columns and features to this page.', 'pegasaas-accelerator').'</p>'.
					'<p><b>'.__('Enabing/Disabling Acceleration', 'pegasaas-accelerator').'</b><br>'.
					__('To turn page acceleration ON or OFF, use the toggle button %2$s under the %3$s column.', 'pegasaas-accelerator').'</p>'.
					'<p><b>'.__('Clearing Cache', 'pegasaas-accelerator').'</b><br>'.
					__('You can clear the page cache by clicking the <b>Clear Cache</b> link that is visible under the page title when you hover over the Page Title.  When a cached file is cleared, the icon under the %4$s column will turn grey.', 'pegasaas-accelerator').
					'</p>'.
					'<p><b>'.__($build_cache_label, 'pegasaas-accelerator').'</b><br>'.
					__('You can instruct the system to build a cached page  by clicking the <b>'.$build_cache_label.'</b> link that is visible under the page title when you hover over the Page Title.  When a cached file exists, the icon under the %4$s column will turn from grey to green.', 'pegasaas-accelerator').
					'</p>'.
					'<p><b>'.__('PageSpeed Score', 'pegasaas-accelerator').'</b><br>'.
					__('Your mobile PageSpeed score for your page will be shown, if your page has acceleration enabled, under the %5$s column.  The mobile (rather thank desktop) PageSpeed score is displayed as it is a more relevant metric for being found in search as Google is a mobile first search index.', 'pegasaas-accelerator').

					'</p>'.
					'<p><b>'.__('Load Time', 'pegasaas-accelerator').'</b><br>'.
					__('Your mobile load time for your page, as is reported by Google Ligthouse as the Speed Index, will be shown if your page has acceleration enabled, under the %6$s column.  The mobile (rather thank desktop) Speed Index is displayed as it is a more relevant metric for being found in search as Google is a mobile first search index.', 'pegasaas-accelerator').
					'</p>'.
					'<p><b>'.__('Bulk Actions', 'pegasaas-accelerator').'</b><br>'.
					__('You can perform actions on multiple pages at once by checking the checkbox to the left of the page title, and then select one of the following options from the <b>Bulk Actions</b> select box at the top of the list of pages.', 'pegasaas-accelerator').

					'</p>'.
					'<ul>'.
					'<li>'.__('Enable Acceleration -- this is the same as using the toggle button to Enable Accleration', 'pegasaas-accelerator').'</li>'.
					'<li>'.__('Disable Acceleration -- this is the same as using the toggle button to Disable Accleration', 'pegasaas-accelerator').'</li>'.
					'<li>'.__($build_cache_label.' -- this is the same as using the '.$build_cache_label.' link to '.strtolower($build_cache_label), 'pegasaas-accelerator').'</li>'.
					'<li>'.__('Clear HTML Cache -- this is the same as using the Clear Cache link clear the page cache', 'pegasaas-accelerator').'</li>'.
					'</ul>',
					$plugin_name,
					'<img style="vertical-align: middle" src="'.PEGASAAS_ACCELERATOR_URL.'assets/images/toggle-off.png">',
					'<img style="vertical-align: middle" src="'.PEGASAAS_ACCELERATOR_URL.'assets/images/icon-dark.png">',
					'<img style="vertical-align: middle" src="'.PEGASAAS_ACCELERATOR_URL.'assets/images/icon-cache.png">',
					'<img style="vertical-align: middle" src="'.PEGASAAS_ACCELERATOR_URL.'assets/images/icon-score.png">',
					'<img style="vertical-align: middle" src="'.PEGASAAS_ACCELERATOR_URL.'assets/images/icon-speed.png">',
					'<a href="">',
					'</a>',
					'<a href="">',
					'<em>',
					'</em>'
				),
				'priority' => 15,
			)
		);
	}
	
	function render_page_metric($data, $metric, $view, $html = true) {
		
				$color['fast'] 		= "#64BC63";
		$color['average'] 	= "#e67700";
		$color['slow'] 		= "#cc0000";
		$color['pending'] 		= "#cccccc";
		
		if ($metric == "pagespeed-mobile" || $metric == "pagespeed-desktop") {
			$score = $score_data;
			if ($score >= 90) {
				$score_class = "fast";
			} else if ($score >= 50) {
				$score_class = "average";
			} else if ($score == "") {
				$score_class = "pending";
			} else {
				$score_class = "slow";
			} 
			
			if ($score_class == "pending") { 
				if ($score_percent == "") {
					if ($html) { 
						$gauge_content = "<i title='Pending' class='fa fa-hourglass-2 pegasaas-tooltip'></i>";
					} else {
						$gauge_content = "Pending";
					}
					$score_percent = 0;
				}
			} else {
				$score_percent = $score;
				$gauge_content = $score."%";
			}
			
			
		
		} else if ($metric == "si") {
			
			$score = $data["meta"]["$view"]["lab_data"]["speed-index"]["value"];
			$score_percent = $score_data['score'] * 100;
			
			 if ($score == "" || $score == 0.0) {
				$score_class = "pending";
			} else if ($score <= 3.387) {
				$score_class = "fast";
			} else if ($score <= 5.8) {
				$score_class = "average";
			} else {
				$score_class = "slow";
			}

			
			if ($score_class == "pending") { 
				$gauge_content = "";
				$score_percent = 0;
			
			} else {
				$gauge_content = $score."s";
			}			
		
		} else if ($metric == "tti") {
			
			
			
			$score = $data["meta"]["$view"]["lab_data"]["interactive"]["value"];
			$score_percent = $data["meta"]["$view"]["lab_data"]["interactive"]['score'] * 100;
			
			if ($score <= 3.785) {
				$score_class = "fast";
			} else if ($score <= 7.3) {
				$score_class = "average";
			} else {
				$score_class = "slow";
			}
			
			if ($score != "") {
				$gauge_content = $score."s";
			}

		} else if ($metric == "tbt") {
			
			
			
			$score = number_format($data["meta"]["$view"]["lab_data"]["total-blocking-time"]["value"]/100, 1);
			$score_percent = $data["meta"]["$view"]["lab_data"]["total-blocking-time"]['score'] * 100;
			
			if ($score <= 0.290) {
				$score_class = "fast";
			} else if ($score <= 0.6) {
				$score_class = "average";
			} else {
				$score_class = "slow";
			}
			
			if ($score != "") {
				$gauge_content = $score."s";
			}			
		} else if ($metric == "cls") {
			//var_dump($data["meta"]["$view"]["lab_data"]);
			
			
			$score = $data["meta"]["$view"]["lab_data"]["cumulative-layout-shift"]["value"];
			$score_percent = $data["meta"]["$view"]["lab_data"]["cumulative-layout-shift"]['score'];
			
			if ($score <= 0.1) {
				$score_class = "fast";
			} else if ($score <= 0.25) {
				$score_class = "average";
			} else {
				$score_class = "slow";
			}
			
			if ($score != "") {
				$gauge_content = $score."";
			}			
		} else if ($metric == "ttfb") {
	
		
			$score = $data["meta"]["$view"]["lab_data"]["time-to-first-byte"]["value"];
			$score_percent = $score_data['score'] * 100;
			
			if ($score <= 150) {
				$score_class = "fast";
			} else if ($score <= 300) {
				$score_class = "average";
			} else {
				$score_class = "slow";
			}
			
			if ($score != "") {
				$gauge_content = $score."ms";	
			}
		} else if ($metric == "fcp") {
			$score = $data["meta"]["$view"]["lab_data"]["first-contentful-paint"]["value"];
			$score_percent = $score_data['score'] * 100;			
			
			if ($score <= 2.336) {
				$score_class = "fast";
			} else if ($score <= 4.0) {
				$score_class = "average";
			} else {
				$score_class = "slow";
			}
			
			if ($score != "") {
				$gauge_content = $score."s";
			} 
		
		} else if ($metric == "fmp") {
			$score = $data["meta"]["$view"]["lab_data"]["first-meaningful-paint"]["value"];
			$score_percent = $score_data['score'] * 100;
			
			if ($score <= 2.336) {
				$score_class = "fast";
			} else if ($score <= 4.0) {
				$score_class = "average";
			} else {
				$score_class = "slow";
			}
			if ($score != "") {
				$gauge_content = $score."s";
			}
		} else if ($metric == "lcp") {
			$score = $data["meta"]["$view"]["lab_data"]["largest-contentful-paint"]["value"];
			$score_percent = $score_data['score'] * 100;
			
			if ($score <= 2.5) {
				$score_class = "fast";
			} else if ($score <= 4.0) {
				$score_class = "average";
			} else {
				$score_class = "slow";
			}
			if ($score != "") {
				$gauge_content = $score."s";
			}			
		} else if ($metric == "fci") {
			$score = $data["meta"]["$view"]["lab_data"]["first-cpu-idle"]["value"];

			$score_percent = $score_data['score'] * 100;			
			
			if ($score <= 3.387) {
				$score_class = "fast";
			} else if ($score <= 5.8) {
				$score_class = "average";
			} else {
				$score_class = "slow";
			}
			if ($score != "") {
				$gauge_content = $score."s";
			}
		} 
		
		$bar_color = $color["{$score_class}"];
		
		if ($html) {
		?><div class='pegasaas-accelerator-perfscore pegasaas-score-bar <?php echo $score_class; ?> <?php echo $view."-perf-score-".$metric; ?>' 
			   data-percent="<?php echo $score_percent; ?>" 
			   data-duration="0" 
			   data-color="#ccc,<?php echo $bar_color; ?>" 
			   data-content="<?php echo $gauge_content; ?>"></div>
		<?php
		} else {
			return array('value' => $gauge_content, 'class' => $score_class);
		}

		
		
	}

	
		
	function admin_notice__conflicting_plugin() {
		global $pegasaas;
		
		$active_conflicting_plugins = $pegasaas->get_active_conflicting_plugins();
		   ?>
    <div class="notice pegasaas-notice notice-error ">
		<h6><?php _e( 'Pegasaas Accelerator WP', 'pegasaas-accelerator'); ?></h6>
        <p><?php _e( 'The following conflicting plugin(s) appears to be active:  ', 'pegasaas-accelerator' ); ?>
		
		  <?php foreach ($active_conflicting_plugins as $plugin_file => $plugin_data) { ?>
			<?php echo $plugin_data['Name']; ?> 
		  <?php } ?>
		
		</p>
		<p>	
		<?php _e( 'Please disable the conflicting plugin(s) so that Pegasaas Accelerator WP can properly accelerate your website.', 'pegasaas-accelerator' ); ?>
		<a href="plugins.php"><?php _e( "Manage Plugins", 'pegasaas-accelerator' ); ?></a></p>
    
		</div>
    <?php
		
	}		

	function admin_notice__ask_for_review() {
		global $pegasaas;
		
		   ?>
<style>
.notice-form-right { 

display: inline-block;
	}
	.notice-form-right button { width: 150px; margin-right: 5px;}
	.notice-form-button-container { float: right; margin-top: 21px;}
	.five-star { vertical-align: top; 
	margin-left: -3px; }
	.dismiss-submit-a-review {
		cursor: pointer;
		float: right;
		margin-right: -5px;
		margin-left: 10px;
		margin-top: 5px;
		color: #ccc;
	}
	
</style>
    <div class="notice pegasaas-notice notice-ask-for-review">
		<i class='dismiss-submit-a-review fa fa-close'></i>
		<div class='notice-form-button-container'>
			<form class='notice-form-right' target="_blank" action="https://wordpress.org/support/plugin/pegasaas-accelerator-wp/reviews/#new-post"><button type='submit' class='button button-primary'>Submit A Review</button></form>
			<form class='notice-form-right' target="_blank" action="https://pegasaas.com/become-a-sponsor/"><button type='submit' class='button btn button-primary'>Become A Sponsor</button></form>
			<form class='notice-form-right' target="_blank" action="https://pegasaas.com/become-a-sponsor/#one-time-donation"><button type='submit' class='button btn button-primary'>Donate</button></form>
			<form class='notice-form-right'><button id="dismiss-submit-a-review" type='button' class='button'>Don't Ask Me Again</button></form>
		</div>
		<h6><?php _e( 'Pegasaas Accelerator WP', 'pegasaas-accelerator'); ?></h6>
        <p><?php _e( 'Hey!  It looks like you\'ve been using our plugin for a while now and your website is SO much faster now! We hope you\'re enjoying the plugin and finding it useful.  ', 'pegasaas-accelerator'); ?></p>
		<p><?php _e( 'Could you please do us a BIG favour and help support us by either <a target="_blank" href="https://wordpress.org/support/plugin/pegasaas-accelerator-wp/reviews/#new-post">submiting a <img class="five-star" src="'.plugin_dir_url($pegasaas->get_plugin_file()).'assets/images/five-stars.png" /> review on WordPress.org</a> or contribute to our development fund by <a href="https://pegasaas.com/become-a-sponsor/#one-time-donation">donating</a> or by <a href="https://pegasaas.com/become-a-sponsor/">becoming a sponsor</a>.', 'pegasaas-accelerator' ); ?></p>
	</div>
<script>
jQuery("#dismiss-submit-a-review,.dismiss-submit-a-review").bind("click", function() {
	
	jQuery(".notice-ask-for-review").css("display", "none");
	jQuery.post(ajaxurl,
				{ 'action': 'pegasaas_dismiss_review_prompt', 'api_key': jQuery("#pegasaas-api-key").val() },
				function(data) {

				}, "json");
	
});
</script>
   
    <?php
		
	}	
	
	function admin_notice__init_completed() {
		   ?>

    <div class="notice pegasaas-notice notice-success">
		<h6><?php _e( 'Pegasaas Accelerator WP', 'pegasaas-accelerator'); ?></h6>
        <p><?php _e( 'Initialization Is Complete! <a href="admin.php?page=pegasaas-accelerator">check score</a>', 'pegasaas-accelerator' ); ?></p>
	</div>

   
    <?php
		
	}
	
	function admin_notice__init_in_progress() {
		   ?>
    <div class="notice pegasaas-notice notice-info is-dismissible">
		<h6><?php _e( 'Pegasaas Accelerator WP', 'pegasaas-accelerator'); ?></h6>
        <p><?php _e( 'Initialization is in progress.  ', 'pegasaas-accelerator' ); ?></p>
    </div>
    <?php
		
	}
	function pegasaas_is_prepping_done() {
		global $pegasaas;
		
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$score_data 		= $pegasaas->scanner->get_site_score($refresh_score = true, $force_all = true);
			$benchmark_data 	= $pegasaas->scanner->get_site_benchmark_score($refresh_score = true, $force_all = true); 
			$cache_data_map 		   = PegasaasAccelerator::$cache_map;
			$number_of_optimized_pages = sizeof($cache_data_map);
			
			
			
			$queued_optimization_requests			= $pegasaas->db->get_results("pegasaas_api_request", array("request_type" => "optimization-request"));
			$number_of_requests 					= count($queued_optimization_requests);
			
			//var_dump($score_data);
			
			//var_dump($benchmark_data);
		
			if ($score_data['scanned_urls'] > 0 && $benchmark_data['scanned_urls'] > 0) {
				print 3;
			} else {
				if ($pegasaas->is_standard()) {
					// check to see if there are submitted optimizations
					if ($number_of_optimized_pages > 0) {
						print 2;
					} else {
						if ($number_of_requests > 0) {
							print 1;
						} else {
							// if number of requests == 0
							if ($number_of_requests == 0) {
								
								print -1;
							} else {
								print 0;
							}
						}
						
					}
				
				} else {
					return 0;
				}
			
			}
			
		} else {
			print -2;
		}
		wp_die();
		
	}
	
	function pegasaas_notify_hung_initialization() {
		global $pegasaas;
		
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$pegasaas->api->notify_of_possible_initialization_problem();
		}
	}
	
	function get_interface_load_time($page = "pegasaas-dashboard") {
		global $pegasaas;
		
		$time_tracking = get_option("pegasaas_accelerator_interface_load_times", array());
		if ($page == "pegasaas-dashboard") {
			if (PegasaasAccelerator::$settings['settings']['white_label']['status'] == 1) {
				$load_time = $time_tracking["toplevel_page_pa-web-perf"]["load_time"];
			} else {
				$load_time = $time_tracking["toplevel_page_pegasaas-accelerator"]["load_time"];
			}
		} else {
			$load_time = $time_tracking["{$page}"]["load_time"];
		}
		if ($load_time == "") {
			$load_time = -1;
		}
		
		return $load_time;
		
		
	}
	function pegasaas_admin_footer() {
		global $pegasaas;
		$screen = get_current_screen();
		
		$location = $screen->id;
		
		list($usec, $sec) = explode(" ", microtime());
		$time = time() + $usec;
		
		$total_time = $time - $pegasaas->start_time;
		$time_tracking = get_option("pegasaas_accelerator_interface_load_times", array());
		$time_tracking["$location"] = array("when_recorded" => time(), "load_time" => $total_time);
		update_option("pegasaas_accelerator_interface_load_times", $time_tracking);
		
		print "<input type='hidden' id='pegasaas--api-key' value='".PegasaasAccelerator::$settings['api_key']."' />";
		print "<script>console.log('Pegasas Footer: Server Build Time {$total_time}');</script>";
		
		//if ($location == "dashboard") {
			if (!$pegasaas->data_storage->is_valid("all_pages_and_posts")) {
				
				if (PegasaasAccelerator::$settings['api_key'] != "") {
					$this->inject_footer_ajax_request("backload_all_pages_and_posts");
				
				}
			//}

		}
	}

	function inject_footer_ajax_request__auto_accelerate_pages() {
		$this->inject_footer_ajax_request("auto_accelerate_pages");
	}	
	
	
	function inject_footer_ajax_request__submit_scan_request() {
		$this->inject_footer_ajax_request("submit_scan_request");
	}

	function inject_footer_ajax_request__submit_benchmark_requests() {
		$this->inject_footer_ajax_request("submit_benchmark_requests");
	}


	
	
	function inject_footer_ajax_request__assert_global_cpcss() {
		$this->inject_footer_ajax_request("assert_global_cpcss");
	}
	
	function inject_footer_ajax_request($action) {
		$do_action = false;
		if ($action == "assert_global_cpcss") {
			$do_action = PegasaasDeferment::needs_global_cpcss();
			
		} else if ($action == "submit_benchmark_requests") {
			$do_action = PegasaasScanner::needs_benchmark_scans();
		
		} else if ($action == "submit_scan_request") {
			$do_action = PegasaasScanner::needs_webperf_scans();
		} else if ($action == "auto_accelerate_pages") {
			$do_action = PegasaasAccelerator::needs_pages_auto_accelerated();
		} else if ($action == "backload_all_pages_and_posts") {
			$do_action = true;
		}
		
		if ($do_action) { 
		?>
		<script>
		jQuery(document).ready(function($) {

			var data = {
				'action': 'pegasaas_<?php echo $action; ?>',
				'api_key': '<?php echo PegasaasAccelerator::$settings['api_key']; ?>'
			};

		
			jQuery.post(ajaxurl, data, function(response) {});
		});
			</script>
<?php
		}
	}

	
   /**
     * Gets the number of milliseconds that should be used as the interval for the 
	 * dashboard interface refresh routine
     *
     * @return integer The number of milliseconds before the interface executes a refresh
     */	
	function get_interface_start_interval() {
		global $pegasaas;
		
		$delay_in_seconds = 30;

		// if we are running on a siteground server, be extremely cautious as the
		// siteground system is a resource monitored platform
		if ($pegasaas->utils->is_siteground_server()) {
			$delay_in_seconds = 60;
		
		} else if ($pegasaas->utils->is_kinsta_server()) {
			$delay_in_seconds = 120;
		
		} else if (PegasaasAccelerator::$settings['settings']['response_rate']['status'] == "maximum") { 
			$delay_in_seconds = 30; 
		//	$pegasaas->utils->console_log("maximum response rate");
		
		} else if (PegasaasAccelerator::$settings['settings']['response_rate']['status'] == "aggressive") {
			$delay_in_seconds = 30; 

			
		} else if (PegasaasAccelerator::$settings['settings']['response_rate']['status'] == "default") {
			$delay_in_seconds = 30;

		} else if (PegasaasAccelerator::$settings['settings']['response_rate']['status'] == "normal" ) {
			$delay_in_seconds = 30; 
		
		} else if (PegasaasAccelerator::$settings['settings']['response_rate']['status'] == "cautious" ) {
			$delay_in_seconds = 45; 
		
		} else if (PegasaasAccelerator::$settings['settings']['response_rate']['status'] == "slowest" || 
			PegasaasAccelerator::$settings['settings']['response_rate']['status'] == "siteground-normal") {
			$delay_in_seconds = 60; 
		} else if ( PegasaasAccelerator::$settings['settings']['response_rate']['status'] == "kinsta-normal") {
			$delay_in_seconds = 120;
		}
		
		$start_interval = $delay_in_seconds * 1000;
		return $start_interval;
	}

	
	function add_page_post_meta_boxes() {	
		global $pegasaas;
		
		$post_types = $pegasaas->utils->get_post_types();
		
		if (current_user_can("administrator")) {
			if (PegasaasAccelerator::$settings['settings']['white_label']['status'] == 1) {
				if (!$pegasaas->interface->page_settings_panel_restricted()) {
					add_meta_box('pegasaas_page_post_options_sidebar', 
						 PegasaasAccelerator::$settings['settings']['white_label']['menu_label'], 
						 array($this, 'display_page_post_options'), 
						 $post_types, 
						 'side', 
						 'high'); 
				}
			} else {
				add_meta_box('pegasaas_page_post_options_sidebar', 
						 '<i class="pegasaas-icon"></i> Pegasaas Accelerator', 
						 array($this, 'display_page_post_options'), 
						 $post_types, 
						 'side', 
						 'high'); 
			}
			add_action('pre_post_update', array($this, 'save_page_post_metadata'));
		}
	}	
	
	function save_page_post_metadata($post_id) {

		global $pegasaas;
		
		$post 			= get_post($post_id);
		$resource_id 	= $pegasaas->utils->get_object_id($post_id);

		$post_type 		= get_post_type($post_id);
					
		//$page_level_configurations = (array)get_post_meta($post_id, "pegasaas_accelerator_overrides", true); 
		$page_level_configurations = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides"); 
		
		foreach ($_POST as $key => $value) {
			$possible_key_data = explode("__", $key);
			
			if ($possible_key_data[0] == "pegasaas_accelerator") {
				$the_key = $possible_key_data[1];

							
				
				if ($the_key == "post_type_cpcss") {
					$cpcss = get_option('pegasaas_critical_css', array());
					if ($post->resource_id  == "/") {
						$post_type = "home_page";
					}
					
					if ($value == "") { 
						unset($cpcss["post_type__{$post_type}"]);
						$pegasaas->utils->delete_object_meta("post_type__{$post_type}", "critical_css");
					} else {
		

						$value = stripslashes($value);
						$cpcss["post_type__{$post_type}"] = true;
						$post_type_cpcss 		= PegasaasUtils::get_object_meta("post_type__{$post_type}", 'critical_css');
						$post_type_cpcss['css'] = $value;
						//	print "yes, have cpcss of strlen".strlen($value)."<br>";
						$pegasaas->utils->update_object_meta("post_type__{$post_type}", 'critical_css', $post_type_cpcss);
					}
					update_option("pegasaas_critical_css", $cpcss);
				} else if ($the_key == "page_level_cpcss") {
					$cpcss = get_option('pegasaas_critical_css', array());
					if ($value == "") {
						unset($cpcss["{$resource_id}"]);
						
						$pegasaas->utils->delete_object_meta($resource_id, 'critical_css');
					} else {
						$cpcss["{$resource_id}"] = true;
						$page_level_cpcss			= PegasaasUtils::get_object_meta($resource_id, 'critical_css');
						$value = stripslashes($value);
						$page_level_cpcss['css'] 	= $value;
						
						$pegasaas->utils->update_object_meta($resource_id, 'critical_css', $page_level_cpcss);
					}
					update_option("pegasaas_critical_css", $cpcss);
				} else {
					if ($value == "-1") {
						unset($page_level_configurations["{$the_key}"]);
					} else {
						$page_level_configurations["{$the_key}"] = $value;
					}
				}
			}
		}

		
		$pegasaas->utils->update_object_meta($resource_id, "accelerator_overrides", $page_level_configurations);
		
		
	}		
	
	function get_interface_refresh_interval() {
		global $pegasaas;
		
		$delay_in_seconds = 30;
		
		// if we are running on a siteground server, be extremely cautious as the
		// siteground system is a resource monitored platform
		if ($pegasaas->utils->is_siteground_server()) {
			$delay_in_seconds = 120;
		
		} else if ($pegasaas->utils->is_kinsta_server()) {
			$delay_in_seconds = 120;
		
		} else if (PegasaasAccelerator::$settings['settings']['response_rate']['status'] == "maximum") { 
			$delay_in_seconds = 30; 
		
		} else if (PegasaasAccelerator::$settings['settings']['response_rate']['status'] == "aggressive") {
			$delay_in_seconds = 30; 
		
		} else if (PegasaasAccelerator::$settings['settings']['response_rate']['status'] == "default") {
			$delay_in_seconds = 60; 

		} else if (PegasaasAccelerator::$settings['settings']['response_rate']['status'] == "normal") {
			$delay_in_seconds = 60; 
		
		} else if (PegasaasAccelerator::$settings['settings']['response_rate']['status'] == "cautious" ) {
			$delay_in_seconds = 90; 
		
		} else if (PegasaasAccelerator::$settings['settings']['response_rate']['status'] == "slowest") {
			$delay_in_seconds = 120;
			
		} else if (PegasaasAccelerator::$settings['settings']['response_rate']['status'] == "siteground-normal") {
			$delay_in_seconds = 120;
			
		} else if ( PegasaasAccelerator::$settings['settings']['response_rate']['status'] == "kinsta-normal") {
			$delay_in_seconds = 120;
		
		}
		
		$refresh_interval = $delay_in_seconds * 1000;
		return $refresh_interval;
	}	
	
	
	function get_faqs() {
		$one_month = 60*60*24*30;
		$faqs = get_option("pegasaas_faqs", array());
		
		if (isset($faqs['last_updated']) && $faqs['last_updated'] > time() - $one_month) {
			return $faqs['faqs'];
		}

		
		$args = array();
		$verify_peer = get_option("pegasaas_accelerator_curl_ssl_verify", 0) == 1;
		$args['ssl_verify'] = 1;
		$args['method'] = "GET";
		
	

		$response = wp_remote_request("https://pegasaas.com/category/faqs/feed/", $args);
		
		if (is_a($response, "WP_Error")) {
			$error = $response->get_error_message();
			//$pegasaas->utils->log("API Error: {$error}", "api");
			$response = "";
		} else {
			$http_response = $response['http_response'];
			$response =  $http_response->get_data();
			
			
		}
		
		$items = array();
		$pattern = '/<item>(.*?)<\/item>/si';
		$pattern_title = '/<title>(.*?)<\/title>/si';
		$pattern_link  = '/<link>(.*?)<\/link>/si';
		$pattern_content  = '/<content:encoded><!\[CDATA\[(.*)\]\]><\/content:encoded>/si';
		$matches = array();
		
		$matches_title = array();
		$matches_link = array();
			preg_match_all($pattern, $response, $matches);
		
		foreach ($matches[0] as $item) {
			preg_match($pattern_title, $item, $matches_title);
			
			$title = $matches_title[1];
			preg_match($pattern_link, $item, $matches_link);
			$link = $matches_link[1];
				preg_match($pattern_content, $item, $matches_content);
			$content = $matches_content[1];

			$items[] = array("title" => $title, "link" => $link, "content" => $content);
			
		}
		update_option("pegasaas_faqs", array("faqs" => $items, "last_updated" => time()));
		return $items;
	}


	function get_changelog() {
		global $pegasaas;
		$oldest_version_to_display = "2.0.0";

		
		$args = array();
		$verify_peer = get_option("pegasaas_accelerator_curl_ssl_verify", 0) == 1;
		$args['ssl_verify'] = 1;
		$args['method'] = "GET";
	

		$response = wp_remote_request("https://pegasaas.com/changelog.xml", $args); 
		
		if (is_a($response, "WP_Error")) {
			$error = $response->get_error_message();
			//$pegasaas->utils->log("API Error: {$error}", "api");
			$response = "";
		} else {
			$http_response = $response['http_response'];
			$response =  $http_response->get_data();
			
			
		}
		
		$items = array();
		$pattern = '/<item>(.*?)<\/item>/si';
		$pattern_title = '/<title>(.*?)<\/title>/si';
		$pattern_version = '/<version>(.*?)<\/version>/si';

		$pattern_video  = '/<video>(.*?)<\/video>/si';
		$pattern_release_date  = '/<pubDate>(.*?)<\/pubDate>/si';
		$pattern_content  = '/<content:encoded><!\[CDATA\[(.*)\]\]><\/content:encoded>/si';
		$matches = array();
		
		$matches_title = array();
		$matches_version= array();
		$matches_video  = array();
		$matches_release_date  = array();
		
		preg_match_all($pattern, $response, $matches);
		
		foreach ($matches[0] as $item) {
			preg_match($pattern_title, $item, $matches_title);
			preg_match($pattern_video, $item, $matches_video);
			
			
			preg_match($pattern_version, $item, $matches_version);
			preg_match($pattern_content, $item, $matches_content);
			preg_match($pattern_release_date, $item, $matches_release_date);
			
			$title = $matches_title[1];
			$video = $matches_video[1];
	
			$version = $matches_version[1];
			$content = $matches_content[1];
			$release_date = $matches_release_date[1];
			if (!$pegasaas->utils->newer_version($oldest_version_to_display, $version)) {
				$items[] = array("title" => $title, "video" => $video, "content" => $content, 'version' => $version, 'release_date' => $release_date);
			} 	
			
		}
		return $items;
	}
	
	function take_tour() {
		$max_tour_step = $this->get_tour_last_step();
		
		if (PegasaasAccelerator::$settings['settings']['white_label']['status'] == 1) {
			return false;
		}
		
		if ($_COOKIE['pegasaas_tour_steps_completed'] == "" || $_COOKIE['pegasaas_tour_steps_completed'] < $max_tour_step) {
			return true;
		} else {
			return false;
		}
		
	}
	
	function get_current_tour_step() {
		if ($_COOKIE['pegasaas_tour_steps_completed'] == 0) {
			return 0;
		} else {
			return $_COOKIE['pegasaas_tour_steps_completed'];
		}
	}
	
	function get_tour_last_step() {
		return 1;
	}
	
	function get_troubleshooting_articles() {
		$one_month = 60*60*24*30;
		$troubleshooting = get_option("pegasaas_troubleshooting", array());
		
		if (isset($troubleshooting['last_updated']) && $troubleshooting['last_updated'] > time() - $one_month) {
			return $troubleshooting['troubleshooting'];
		}

		
		$args = array();
		$verify_peer = get_option("pegasaas_accelerator_curl_ssl_verify", 0) == 1;
		$args['ssl_verify'] = 1;
		$args['method'] = "GET";
	

		$response = wp_remote_request("https://pegasaas.com/category/troubleshooting/feed/", $args);
		
		if (is_a($response, "WP_Error")) {
			$error = $response->get_error_message();
			//$pegasaas->utils->log("API Error: {$error}", "api");
			$response = "";
		} else {
			$http_response = $response['http_response'];
			$response =  $http_response->get_data();
			
			
		}
		
		$items = array();
		$pattern = '/<item>(.*?)<\/item>/si';
		$pattern_title = '/<title>(.*?)<\/title>/si';
		$pattern_link  = '/<link>(.*?)<\/link>/si';
		$pattern_content  = '/<content:encoded><!\[CDATA\[(.*)\]\]><\/content:encoded>/si';
		$matches = array();
		
		$matches_title = array();
		$matches_link = array();
		$matches_content = array();
			preg_match_all($pattern, $response, $matches);
		
		foreach ($matches[0] as $item) {
			preg_match($pattern_title, $item, $matches_title);
			
			$title = $matches_title[1];
			preg_match($pattern_link, $item, $matches_link);
			$link = $matches_link[1];

			preg_match($pattern_content, $item, $matches_content);
			$content = $matches_content[1];
			
			$items[] = array("title" => $title, "link" => $link, "content" => $content);
			
		}
		
				update_option("pegasaas_troubleshooting", array("troubleshooting" => $items, "last_updated" => time()));

		return $items;
	}	
	
	function admin_posts_column_sort($columns) {

		//$columns['pegasaas-accelerator-accelerator']    = 'pegasaas-accelerator-accelerator';

		return $columns;
	}		

	
	function register_bulk_actions($bulk_actions) { 
		global $pegasaas;
		$bulk_actions['accelerate'] 			= __('Enable Acceleration', 'accelerate');
		$bulk_actions['disable_acceleration'] 	= __('Disable Acceleration', 'disable_acceleration');
		
		if ($pegasaas->is_standard()) { 
			$bulk_actions['build_cache'] 			= __('Optimize Now', 'build_cache');
		} else { 
			$bulk_actions['build_cache'] 			= __('Build Cache', 'build_cache');
		
		}		
		$bulk_actions['clear_cache'] 			= __('Clear HTML Cache', 'clear_cache');

		
		return $bulk_actions;
		
	}
	
	function bulk_action_handler($redirect_to, $doaction = "", $post_ids = array()) {
		global $pegasaas;

				$redirect_to = remove_query_arg('bulk_clear_cache_post', $redirect_to);
				$redirect_to = remove_query_arg('bulk_accelerate_post', $redirect_to);
				$redirect_to = remove_query_arg('bulk_disable_acceleration_post', $redirect_to);
		$set_pages = false;

		
		if ($doaction == "accelerate") {

			$accelerated_pages = get_option("pegasaas_accelerated_pages", array());
			$accelerated_count = 0;
			
			
			foreach ($post_ids as $post_id) {
				// if the page is in "publish" state
				
				$post = get_post($post_id);
				
				if ($post->post_status == "publish") {
					if (sizeof($accelerated_pages)>= PegasaasAccelerator::$settings['limits']['maximum_page_accelerations_available']) {
						break;
					} else {
					
						$resource_id = $pegasaas->utils->get_object_id($post_id);
						// if the page isn't already accelerated
						if ($accelerated_pages["$resource_id"]  !== true ) {
					
							$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides", true);
							$page_level_settings['accelerated'] = true;
							$pegasaas->utils->update_object_meta($resource_id, "accelerator_overrides", $page_level_settings);

							$accelerated_pages["{$resource_id}"] = 1;
						} else {
							
						}
						$accelerated_count++;

					}
					$set_pages = true;
				}
			}
			if ($accelerated_count > 0) {
				$redirect_to = add_query_arg('bulk_accelerate_post', count($post_ids), $redirect_to );
			}
			
			if ($set_pages) {
				$end_size = sizeof($accelerated_pages);
				$pegasaas->utils->log("update_option('pegasaas_accelerated_pages', Array({$end_size}) -- PegasaasInterface::bulk_action_handler -- accelerate / ".sizeof($post_ids), "data_structures");

				update_option("pegasaas_accelerated_pages", $accelerated_pages, false);

				$pegasaas->set_accelerated_pages();
			}
			
		} else if ($doaction == "disable_acceleration") {
			$accelerated_pages = get_option("pegasaas_accelerated_pages", array());
			$accelerated_count = 0;
			
			foreach ($post_ids as $post_id) {
				
				$resource_id = $pegasaas->utils->get_object_id($post_id);
				
					if ($accelerated_pages["$resource_id"]) {
						
						$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides", true);
						$page_level_settings['accelerated'] = 0;
						$pegasaas->utils->update_object_meta($resource_id, "accelerator_overrides", $page_level_settings);
					

						unset($accelerated_pages["{$post_id}"]);
					
					} else {
						
					}
					
					
			
				$set_pages = true;
			}
			if ($set_pages) {
				$end_size = sizeof($accelerated_pages);
				$pegasaas->utils->log("update_option('pegasaas_accelerated_pages', Array({$end_size}) -- PegasaasInterface::bulk_action_handler -- disable / ".sizeof($post_ids), "data_structures");

				update_option("pegasaas_accelerated_pages", $accelerated_pages, false);
				$pegasaas->set_accelerated_pages();
			}
			
			$redirect_to = add_query_arg('bulk_disable_acceleration_post', count($post_ids), $redirect_to );

		} else if ($doaction == "clear_cache") {
			
			foreach ($post_ids as $post_id) {
				// do stuff here
				$object_id = $pegasaas->utils->get_object_id($post_id);
				$pegasaas->cache->clear_cache($object_id );
			}
			
			$redirect_to = add_query_arg('bulk_clear_cache_post', count($post_ids), $redirect_to );
		} else if ($doaction == "build_cache") {
			$pegasaas->max_execution_time = 20;
			
			foreach ($post_ids as $post_id) {
				// do stuff here
				$object_id = $pegasaas->utils->get_object_id($post_id);
				
				// disabled this as of August 19,2020 we don't want to clear cache while doing a bulk action
				//$pegasaas->cache->clear_cache($object_id);

				$pegasaas->utils->touch_url($object_id, array("timeout" => 3, "blocking" => false));
			}
	
			$redirect_to = add_query_arg('bulk_build_cache_post', count($post_ids), $redirect_to );
		} 
		
		return $redirect_to;
	}

	function bulk_action_admin_notice() {
		
	  if ( ! empty( $_REQUEST['bulk_accelerate_post'] ) ) {
		$count = intval( $_REQUEST['bulk_accelerate_post'] );
		printf( '<div id="message" class="updated fade"><p>' .
		  _n( 'Accelerated %s posts.',
			'Accelerated %s posts.',
			$count,
			'accelerate'
		  ) . '</p></div>', $count );
	  } else if ( ! empty( $_REQUEST['bulk_disable_acceleration_post'] ) ) {
		$count = intval( $_REQUEST['bulk_disable_acceleration_post'] );
		printf( '<div id="message" class="error fade"><p>' .
		  _n( 'Disabled Acceleration for %s posts.',
			'Disabled Acceleration for %s posts.',
			$count,
			'disable_acceleration'
		  ) . '</p></div>', $count );
	  } else if ( ! empty( $_REQUEST['bulk_clear_cache_post'] ) ) {
		$count = intval( $_REQUEST['bulk_clear_cache_post'] );
		printf( '<div id="message" class="updated fade"><p>' .
		  _n( 'Cleared HTML cache for %s posts.',
			'Cleared HTML cache for %s posts.',
			$count,
			'clear_cache'
		  ) . '</p></div>', $count );
	  } else if ( ! empty( $_REQUEST['bulk_build_cache_post'] ) ) {
		$count = intval( $_REQUEST['bulk_clear_cache_post'] );
		printf( '<div id="message" class="updated fade"><p>' .
		  _n( 'Building optimized cache for %s posts.',
			'Building optmimized cache for %s posts.',
			$count,
			'clear_cache'
		  ) . '</p></div>', $count );
	  } 
	}	
	
	function add_bulk_pagepost_functions() {
		if (current_user_can("administrator")) {
			global $pegasaas;

			$available_post_types = $pegasaas->utils->get_post_types(); 

			foreach ($available_post_types as $post_type_id) {
				add_filter( "bulk_actions-edit-{$post_type_id}", array($this, 'register_bulk_actions'));
				add_filter( "handle_bulk_actions-edit-{$post_type_id}", array($this, 'bulk_action_handler'), 10, 3);
			}

			add_action( 'admin_notices', array($this, 'bulk_action_admin_notice') );
		}
	}
	
	function admin_categories_column_content($content, $column_name, $term_id ) {
		 global $pegasaas;
		
		 $category 		= get_term($term_id);	
		 $resource_id 	= $pegasaas->utils->get_object_id("category__".$category->term_id);
		 
		 $this->add_admin_column_content($column_name, $resource_id);		 
	 }
	
	 function admin_posts_column_content($column_name, $post_id) {
		 global $pegasaas;
		
		 $post 			= get_post($post_id);
		 $resource_id 	= $pegasaas->utils->get_object_id($post->ID);
		 
		 $this->add_admin_column_content($column_name, $resource_id);
	 }
	
	function add_admin_column_content($column_name, $resource_id) {
		$resource_id = str_replace("http://", "/", $resource_id);
		$resource_id = str_replace("https://", "/", $resource_id);
		
		global $pegasaas;
		
		switch ($column_name) {
			case 'pegasaas-accelerator-status':
				$page_level_settings 	= PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides", $use_cache = true);
				//var_dump($page_level_settings['accelerated']); 
				
				?><input type='checkbox' 
						 <?php if ($pegasaas->is_excluded_url($resource_id)) { echo "disabled"; } ?> 
						 data-pegasaas-resource-id='<?php echo $resource_id; ?>' 
						 style='display:none;' 
						 class='js-switch js-switch-smallest indv-js-switch <?php if ($page_level_settings['accelerated'] === true || $page_level_settings['accelerated'] == 1) { ?>resource-accelerated<?php } ?> <?php if ($pegasaas->in_development_mode() && $page_level_settings['staging_mode_page_is_live'] != 1) { ?>staging-mode-active<?php } ?> <?php if ($pegasaas->is_excluded_url($resource_id)) { echo "excluded-resource"; } ?>' 
						 <?php if ($page_level_settings['accelerated'] === true || $page_level_settings['accelerated'] == 1) { ?>checked<?php } ?> /><?php 

				break; 
			
			case 'pegasaas-accelerator-perfscore' :
				if ($pegasaas->execution_time() > 10) { 
					break;
				} 
				
				$page_level_settings 	= PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides", $use_cache = true);
		    
				// do not display any scores if the acceleration is disabled
				if ($page_level_settings['accelerated'] == false) { break; }

				$page_score_data = $pegasaas->scanner->pegasaas_fetch_pagespeed_last_scan($resource_id, $use_cache = true);
				
				
				if (is_array($page_score_data)) {
					$page_score = $page_score_data['mobile_score'];
					
					if ($page_score == "") {
						break;
					}
					
					if ($page_score >= 90) {
						$page_score_class = "fast";
						$bar_color = "#64BC63";
					} else if ($page_score >= 50) {
						$page_score_class = "average";
						$bar_color = "#232C30";
						$bar_color = "#e67700";
					} else {
						$bar_color = "#cc0000";
						$page_score_class = "slow";
					} 
					
					 $url = $pegasaas->get_home_url().$resource_id;
					 ?><a rel='pegasaas-tooltip' title='PageSpeed Score (Mobile): Click To Verify with Google PageSpeed Insights' href='https://developers.google.com/speed/pagespeed/insights/?url=<?php echo urlencode($url); ?>' target='_blank' rel='noopener noreferrer'><div class='pegasaas-accelerator-perfscore pegasaas-score-bar <?php echo $page_score_class; ?>' data-percent="<?php echo $page_score; ?>" data-duration="2000" data-color="#ccc,<?php echo $bar_color; ?>" data-content='<?php echo $page_score; ?>'></div></a><?php
					
				} 
				break;
			
						
			case 'pegasaas-accelerator-cache':
				//print "resource id: $resource_id<br>";
			
			 	$cache_exists 		= PegasaasAccelerator::$cache_map["{$resource_id}"] != "";
				$temp_cache_exists 	= PegasaasAccelerator::$cache_map["{$resource_id}"]["is_temp"];
				
				$request_queued = $pegasaas->db->has_record("pegasaas_api_request", array("resource_id" => $resource_id, "request_type" => "optimization-request"));
			 	
				if ($request_queued) { 
					print '<div class="pegasaas-accelerator-cache-icon request-existing pegasaas-tooltip" title="Optimization Request Queued" rel="'.$resource_id.'"></div>';

				} else if ($temp_cache_exists) {
					print '<div class="pegasaas-accelerator-cache-icon temp-existing pegasaas-tooltip" title="Temporary Cache Exists" rel="'.$resource_id.'"></div>';

				} else if ($cache_exists) {
					print '<div class="pegasaas-accelerator-cache-icon existing pegasaas-tooltip" title="Optimized Cache Exists" rel="'.$resource_id.'"></div>';
				} else {
					print '<div class="pegasaas-accelerator-cache-icon pegasaas-tooltip" title="Cache Does Not Exist" rel="'.$resource_id.'"></div>';
					
				}
				break;
				
			case 'pegasaas-accelerator-mobile-speed':
				if ($pegasaas->execution_time() > 10) { 
					break;
				} 
				$page_level_settings 	= PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides", $use_cache = true);
			    
				// do not display any scores if the acceleration is disabled
				if ($page_level_settings['accelerated'] == false) {	break; }

				$page_score_data = $pegasaas->scanner->pegasaas_fetch_pagespeed_last_scan($resource_id, $use_cache = true);

				if (is_array($page_score_data)) {
					$interactive_time = $page_score_data['meta']['mobile']['lab_data']['speed-index']['displayValue'];
					$interactive_time_data = explode(" ", $interactive_time);
					$interactive_time = number_format($interactive_time * 1, 1, '.', ''); 
					
					if ($interactive_time == "" || $interactive_time == 0.0) {
						
						break;
					}
				
					
					if ($interactive_time <= 3.7) {
						$page_score_class = "fast";
						$bar_color = "#64BC63";
					} else if ($interactive_time <= 7.3) {
						$page_score_class = "average";
						$bar_color = "#232C30";
						$bar_color = "#e67700";
					} else {
						$bar_color = "#cc0000";
						$page_score_class = "slow";
					} 
					$max_time = 28;
					$percentage = 100 * $interactive_time / $max_time;
					
					 $url = $pegasaas->get_home_url().$resource_id;
					 ?><a rel='pegasaas-tooltip' title='Speed Index (Mobile): Click To Verify with Google PageSpeed Insights' href='https://developers.google.com/speed/pagespeed/insights/?url=<?php echo urlencode($url); ?>' target='_blank' rel='noopener noreferrer'><div class='pegasaas-accelerator-perfscore pegasaas-score-bar <?php echo $page_score_class; ?>' data-percent="<?php echo $percentage; ?>" data-duration="2000" data-color="#ccc,<?php echo $bar_color; ?>" data-content='<?php echo $interactive_time; ?>s'></div></a><?php
					
				} 
				break;
		
			
		}
	}
	
	
	function global_settings_page_restricted() {
		global $pegasaas;
		if (PegasaasAccelerator::$settings['settings']['white_label']['status'] == 1) {
			if (PegasaasAccelerator::$settings['settings']['white_label']['restrict_global_settings'] == "no") {
				return false;
			} else {
				$current_user = wp_get_current_user();	
				if ($current_user->user_email == PegasaasAccelerator::$settings['settings']['white_label']['global_admin_email']) {
					return false;
				} else {
					return true;
				}
			}
			
		} else {
			return false;
		}
		
	}
	
	function page_settings_panel_restricted() {
		global $pegasaas;
		if (PegasaasAccelerator::$settings['settings']['white_label']['status'] == 1) {
			if (PegasaasAccelerator::$settings['settings']['white_label']['restrict_page_settings'] == "no") {
				return false;
			} else {
				$current_user = wp_get_current_user();	
				if ($current_user->user_email == PegasaasAccelerator::$settings['settings']['white_label']['global_admin_email']) {
					return false;
				} else {
					return true;
				}
			}
			
		} else {
			return false;
		}
		
	}

	function dashboard_scores_restricted() {
		global $pegasaas;
		if (PegasaasAccelerator::$settings['settings']['white_label']['status'] == 1) {
			if (PegasaasAccelerator::$settings['settings']['white_label']['restrict_dashboard_page_scores'] == "no") {
				return false;
			} else {
				$current_user = wp_get_current_user();	
				if ($current_user->user_email == PegasaasAccelerator::$settings['settings']['white_label']['global_admin_email']) {
					return false;
				} else {
					return true;
				}
			}
			
		} else {
			return false;
		}
		
	}	

	function toolbar_scores_restricted() {
		global $pegasaas;
		if (PegasaasAccelerator::$settings['settings']['white_label']['status'] == 1) {
			if (PegasaasAccelerator::$settings['settings']['white_label']['restrict_toolbar_scores'] == "no") {
				return false;
			} else {
				$current_user = wp_get_current_user();
				if (PegasaasUtils::match_email(PegasaasAccelerator::$settings['settings']['white_label']['global_admin_email'], $current_user->user_email)) {
			//	if ($current_user->user_email == PegasaasAccelerator::$settings['settings']['white_label']['global_admin_email']) {
					return false;
				} else {
					return true;
				}
			}
			
		} else {
			return false;
		}
		
	}	
	
	function admin_enqueue($hook_suffix) {
		global $pegasaas;
		global $pagenow;
		global $wp_scripts;
		$has_bootstrap = false;

		$version = $pegasaas->get_current_version();
		

		$pegasaas->utils->console_log("Enqueue Scripts");
		
		// ali drop ship does not enqueue JS files in the typical manner, but they use bootstrap
		if ($pegasaas->utils->does_plugin_exists_and_active("alids") && isset($_GET['page']) && $_GET['page'] != "pegasaas-accelerator") {
			$has_bootstrap = true;
		}
	
		
		foreach ($wp_scripts->queue as $handle) {
			$script_name = $wp_scripts->registered["$handle"]->src;
			if (strstr($script_name, "bootstrap.js") !== false || strstr($script_name, "bootstrap.min.js") !== false) {
				$has_bootstrap = true; 
			
			}
		}
		
		if ($has_bootstrap) {
			
		} else { 
	 		wp_enqueue_script('pegasaas-admin-bootstrap-js',  PEGASAAS_ACCELERATOR_URL  . 'assets/bootstrap/3.3.7/js/bootstrap.min.js', array('jquery'), '3.3.5', true);
		}
		
		
	  	if ($hook_suffix == "toplevel_page_pegasaas-accelerator" || $hook_suffix == "tools_page_pa-web-perf"  || $hook_suffix == "toplevel_page_pa-web-perf") {
			// news ticker
			wp_enqueue_script('pegasaas-admin-newsticker-js',  PEGASAAS_ACCELERATOR_URL  . 'assets/js/jquery.bootstrap.newsbox.js', '', '3.3.5', false);
			wp_enqueue_script('pegasaas-admin-circular-progress',  PEGASAAS_ACCELERATOR_URL  . 'assets/js/jquery-plugin-progressbar.js', '', '3.3.5', false);

			// remove from NGG (Nextgen Gallery) on our dashboard due to conflict
			wp_deregister_script('jquery-modal');
			wp_deregister_style('jquery-modal');
			
			// bootstrap
			wp_enqueue_style('pegasaas-admin-bootstrap-css',   PEGASAAS_ACCELERATOR_URL  . 'assets/bootstrap/3.3.7/css/bootstrap.min.css');	
	  
	  		// switchery (ios style switch buttons)
			wp_enqueue_style('pegasaas-admin-switchery-css',  PEGASAAS_ACCELERATOR_URL  . 'assets/switchery/switchery.min.css?ver='.$version, $version );
			wp_enqueue_script('pegasaas-admin-switchery-js',  PEGASAAS_ACCELERATOR_URL  . 'assets/switchery/switchery.min.js', '', '3.3.5', false);

			//wp_enqueue_script('pegasaas-admin-jquery-guide',  PEGASAAS_ACCELERATOR_URL  . 'assets/js/jquery.guide.js', 'jquery', '1.0.0', false);

	  		// switchery (ios style range slider)
			//wp_enqueue_style('pegasaas-admin-powerange-css',  PEGASAAS_ACCELERATOR_URL  . 'assets/powerange/powerange.css?t='.time(), time() );
			//wp_enqueue_script('pegasaas-admin-powerange-js',  PEGASAAS_ACCELERATOR_URL  . 'assets/powerange/powerange.js', '', '3.3.5', false);
			
			
			// jquery chart.js
			wp_enqueue_script('pegasaas-admin-chart-js', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js', '', '2.7.2', false);
			
			wp_enqueue_style('pegasaas-admin-fonts', 'https://fonts.googleapis.com/css?family=Muli|Lato:300,400');
			wp_enqueue_style(  'pegasaas-admin-material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons');

			wp_enqueue_script('pegasaas-admin-js-cookie', PEGASAAS_ACCELERATOR_URL  . 'assets/js/js.cookie.js');
			wp_enqueue_script('pegasaas-admin-settings', PEGASAAS_ACCELERATOR_URL  . 'assets/js/settings.js', 'jquery', '', true);
	
			// morphext
			wp_enqueue_script('pegasaas-admin-morphtext-js',  PEGASAAS_ACCELERATOR_URL  . 'assets/morphext/morphext.js', '', '2.6.0', false);
			wp_enqueue_style('pegasaas-admin-morphtext-css',  PEGASAAS_ACCELERATOR_URL  . 'assets/morphext/morphext.css?t='.time(), time() );
			wp_enqueue_style('pegasaas-admin-animate-css',  PEGASAAS_ACCELERATOR_URL  . 'assets/morphext/animate.css?ver='.$version, $version );
			wp_enqueue_style('pegasaas-score-gauges-bootstrap-css',  PEGASAAS_ACCELERATOR_URL  . 'assets/css/score-gauges.css?ver='.$version, $version);	

			
			
		
			$last_checked = get_option("pegasaas_when_ob_environment_last_checked", 0);
			if (get_option("pegasaas_when_ob_environment_last_checked", 0) < time() - 86400) {
				 wp_add_inline_script( 'jquery-migrate', 'jQuery(document).ready(function(){jQuery.post(ajaxurl, {"action": "pegasaas_get_ob_environment", "api_key": jQuery("#pegasaas-api-key").val() });});' );
			}
			//wp_enqueue_script('pegasaas-admin-js-mustachejs', PEGASAAS_ACCELERATOR_URL  . 'assets/js/jquery.mustache.js?ver='.$version, array('jquery'), '3.0', false);
			wp_enqueue_script('pegasaas-admin-js-nunjucks', PEGASAAS_ACCELERATOR_URL  . 'assets/js/nunjucks.js?ver='.$version, array('jquery'), '3.0', false);
			wp_enqueue_script('pegasaas-admin-js-dashboard', PEGASAAS_ACCELERATOR_URL  . 'assets/js/dashboard.js?ver='.$version, array('jquery'), '3.0', true);
			
			
		} else if ($pagenow == "edit.php") {
	  		// switchery (ios style switch buttons)
			wp_enqueue_style('pegasaas-admin-switchery-css',  PEGASAAS_ACCELERATOR_URL  . 'assets/switchery/switchery.min.css?ver='.$version, $version );
			wp_enqueue_script('pegasaas-admin-switchery-js',  PEGASAAS_ACCELERATOR_URL  . 'assets/switchery/switchery.min.js', '', '3.3.5', false);
			wp_enqueue_script('pegasaas-admin-circular-progress',  PEGASAAS_ACCELERATOR_URL  . 'assets/js/jquery-plugin-progressbar.js', '', '3.3.5', false);
			wp_enqueue_style('pegasaas-admin-fonts', 'https://fonts.googleapis.com/css?family=Muli|Lato:300,400');

			wp_enqueue_script('pegasaas-admin-bootstrap-js',   PEGASAAS_ACCELERATOR_URL  . 'assets/bootstrap/3.3.7/js/bootstrap.min.js', array('jquery'), '3.3.5', true);
			wp_enqueue_style('pegasaas-posts-bootstrap-css',  PEGASAAS_ACCELERATOR_URL  . 'assets/css/posts.css?ver='.$version, $version);	
				wp_enqueue_style('pegasaas-score-gauges-bootstrap-css',  PEGASAAS_ACCELERATOR_URL  . 'assets/css/score-gauges.css?ver='.$version, $version);	
		  
	  	} else if ($pagenow == "post.php" || $pagenow == "edit-tags.php") {
			wp_enqueue_style('pegasaas-admin-switchery-css',  PEGASAAS_ACCELERATOR_URL  . 'assets/switchery/switchery.min.css?ver='.$version, $version );
			wp_enqueue_script('pegasaas-admin-switchery-js',  PEGASAAS_ACCELERATOR_URL  . 'assets/switchery/switchery.min.js', '', '3.3.5', false);
			wp_enqueue_script('pegasaas-admin-circular-progress',  PEGASAAS_ACCELERATOR_URL  . 'assets/js/jquery-plugin-progressbar.js', '', '3.3.5', false);

			wp_enqueue_script('pegasaas-admin-bootstrap-js',   PEGASAAS_ACCELERATOR_URL  . 'assets/bootstrap/3.3.7/js/bootstrap.min.js', array('jquery'), '3.3.5', true);
			wp_enqueue_style('pegasaas-page-post-options',  PEGASAAS_ACCELERATOR_URL  . 'assets/css/page-post-options.css?ver='.$version, $version);	
				wp_enqueue_style('pegasaas-posts-bootstrap-css',  PEGASAAS_ACCELERATOR_URL  . 'assets/css/posts.css?ver='.$version, $version);	
				wp_enqueue_style('pegasaas-score-gauges-bootstrap-css',  PEGASAAS_ACCELERATOR_URL  . 'assets/css/score-gauges.css?ver='.$version, $version);	
		
		}
 

		wp_register_script( 'pegasaas-accelerator-admin-bar-js',		 PEGASAAS_ACCELERATOR_URL  . 'assets/js/admin-bar.js?ver='.$version, array('jquery'), '1.0', true);
    	if ($pegasaas->cache->has_cache_clearing_queued() || $pegasaas->cache->has_cache_reoptimizing_queued()) {
			wp_add_inline_script ('pegasaas-accelerator-admin-bar-js', "queue_cache_clearing();", "after");	
		}
		wp_enqueue_script( 'pegasaas-accelerator-admin-bar-js');
		
		
		
		wp_enqueue_style( 'pegasaas-accelerator-admin-styles',  PEGASAAS_ACCELERATOR_URL  . 'assets/css/admin.css?ver='.$version, $version );
		
		if ($_POST['interface_type'] != "") {
			PegasaasAccelerator::$settings['settings']['display_mode']['status'] = $_POST['interface_type']; 
		}
		
		if (PegasaasAccelerator::$settings['settings']['display_mode']['status'] == 3) {
			wp_enqueue_style( 'pegasaas-accelerator-admin-styles-light',  PEGASAAS_ACCELERATOR_URL  . 'assets/css/blurred.css?ver='.$version, $version );
		}  else if (PegasaasAccelerator::$settings['settings']['display_mode']['status'] == 1 || PegasaasAccelerator::$settings['settings']['display_mode']['status'] == "") {
			wp_enqueue_style( 'pegasaas-accelerator-admin-styles-light',  PEGASAAS_ACCELERATOR_URL  . 'assets/css/light.css?ver='.$version, $version );
		}  else if (PegasaasAccelerator::$settings['settings']['display_mode']['status'] == 2) {
				wp_enqueue_style( 'pegasaas-accelerator-admin-styles-plain',  PEGASAAS_ACCELERATOR_URL  . 'assets/css/plain.css?ver='.$version, $version );
		
		}
		
		wp_enqueue_style( 'pegasaas-admin-fontawesome4',	 PEGASAAS_ACCELERATOR_URL  . "assets/font-awesome/4.7.0/css/font-awesome.min.css");		

		wp_enqueue_script('pegasaas-accelerator-admin-script',  PEGASAAS_ACCELERATOR_URL  . 'assets/js/pegasaas-accelerator.js?ver='.$version, array('jquery'), time(), true);
		
		wp_enqueue_style(  'pegasaas-accelerator-admin-bar-styles', PEGASAAS_ACCELERATOR_URL  . 'assets/css/admin-bar.css?ver='.$version, $version );
		wp_enqueue_script( 'pegasaas-accelerator-admin-bar-js',		PEGASAAS_ACCELERATOR_URL  . 'assets/js/admin-bar.js?ver='.$version, array('jquery'), '1.0', true);
		
	
		
	
			wp_localize_script( 'pegasaas-accelerator-admin-bar-js', 'pegasaas_ajax_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ),
				 'api_key' => PegasaasAccelerator::$settings['api_key']) );
		
		
		
	}	
	
	function render_recommended_actions_button($object_id, $post_id) {
		global $pegasaas;

		$the_post = get_post($post_id);
		$post = $pegasaas->scanner->get_scanned_object_post_data($the_post, $object_id);
		
		
		$post_average_score = ($post['mobile_score'] + $post['score'] ) / 2;
		if ($post_average_score)

				  if ($post_average_score >= 85) {
					  $status_both 		= "pass";
					  $status_icon_both = "fa-check";
				  } else if ($post_average_score > 75) {
					  $status_both 			= "warning";
					  $status_icon_both 		= "fa-exclamation-triangle";
				  } else {
					  $status_both 		= "danger";
				  	  $status_icon_both = "fa-exclamation-triangle"; 
				  }
				  
				  if ($post['mobile_score'] >= 85) {
					  $status_mobile = "pass";
					  $status_icon_mobile = "fa-check";
				  } else if ($post['mobile_score'] >= 75) {
					  $status_mobile = "warning";
					  $status_icon_mobile = "fa-exclamation-triangle";
				  } else {
					  $status_mobile = "danger";
					  $status_icon_mobile = "fa-exclamation-triangle";
				  }
				  
				  if ($post['score'] >= 85) {
					  $status_desktop = "pass";
					  $status_icon_desktop = "fa-check";
				  } else if ($post['score'] >= 75) {
					  $status_desktop = "warning";
					  $status_icon_desktop = "fa-exclamation-triangle";
				  } else {
					  $status_desktop = "danger";
					  $status_icon_desktop = "fa-exclamation-triangle";
				  }	
				  if ($post['score'] != "") {
				  ?>
					  <button class='btn btn-primary btn-accelerated-status btn-accelerated-status-both-<?php echo $status_both; ?> btn-accelerated-status-desktop-<?php echo $status_desktop; ?> btn-accelerated-status-mobile-<?php echo $status_mobile; ?>' data-toggle="modal" data-target="#recommendations_<?php echo str_replace("/", "--", $post['slug']); ?>"><i class='fa fa-status-icon-both <?php echo $status_icon_both; ?>'></i><i class='fa fa-status-icon-mobile <?php echo $status_icon_mobile; ?>'></i><i class='fa fa-status-icon-desktop <?php echo $status_icon_desktop; ?>'></i></button>
					  <!-- Modal -->
					<div class="modal fade recommendations-modal modal-accelerated-status-desktop-<?php echo $status_desktop; ?> modal-accelerated-status-mobile-<?php echo $status_mobile; ?>" id="recommendations_<?php echo str_replace("/", "--", $post['slug']); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					  <div class="modal-dialog" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">Recommendations</h4>
						  </div>
						  <div class="modal-body">
							  
							  <?php  ?>
							  <?php  $rule_data 			= array_shift($post['needs']); ?>
							  <?php $mobile_rule_data 	= array_shift($post['mobile_needs']); ?>
							  <?php array_unshift($post['needs'], $rule_data); ?>
							  <?php array_unshift($post['mobile_needs'], $mobile_rule_data); ?>

<div class='recommendations-container modal-mobile-recommendations-container'>
	 <div class='issue-summary'>PageSpeed Score Below Optimal</div>
							  <div class='issue-description'>Desktop PageSpeed Score: <?php echo $post['score'];?>
								  
Top Desktop Priority: <?php echo $rule_data['rule_name']; ?> (<?php echo $rule_data['rule_impact']; ?>/100)
	
Mobile PageSpeed Score: <?php echo $post['mobile_score'];?>
								  
Top Mobile Priority: <?php echo $mobile_rule_data['rule_name']; ?> (<?php echo $rule_data['mobile_rule_impact']; ?>/100)
	
Current Settings
----------------								  
<?php foreach (PegasaasAccelerator::$settings['settings'] as $setting => $data) { ?>
<?php echo $setting.": ".$data['status']; ?>
								  
<?php  } ?>
								</div>							  
							 
								<div class='issue-url'><?php echo $post['slug']; ?></div>
							<h4>
								
								<?php if ($post['mobile_score'] < 85 && PegasaasAccelerator::$settings['limits']['support'] == 1) { ?>
								<button class='btn btn-default pull-right btn-help-request'><i class='fa fa-life-ring'></i> Initiate Support Ticket</button>
								<?php } ?>
								
								<i class='fa fa-mobile'></i> Mobile <span class='badge badge-default'><?php echo $post['mobile_score'];?>/100</span></h4>
							
							  
							<?php if ($post['mobile_score'] >= 95) { ?>
							  <p class='summary'>Awesome, this page is well optimized.  There isn't much more we can do to make it any better!</p>
							<?php } else if ($post['mobile_score'] >= 90) { ?>
							  <p class='summary'>It looks like maybe we should look at a few things to see if we can bring this score up a bit more.</p>
							<?php } else if ($post['mobile_score'] >= 85) { ?>
							  <p class='summary'>Hmm, while <?php echo $post['mobile_score'];?>/100 is not terrible, we would like to see this above 90%. <?php if (PegasaasAccelerator::$settings['limits']['support'] == 0) { ?>Try the recommendations below.<?php } else { ?>  If the recommendations below don't help, then initiate a support ticket by going to the Support tab, our team can look into this.<?php } ?></p>
							<?php } else { ?>
							  <p class='summary'>Good grief!  It looks like there are some major obstacles in the way of getting you a decent score.    <?php if (PegasaasAccelerator::$settings['limits']['support'] == 0) { ?>Try the recommendations below.<?php } else { ?> If the recommendations below don't help, then initiate a support ticket by clicking the button above, our team can look into this.<?php } ?></p>
							<?php } ?>			
							  <?php if ($post['mobile_score'] < 100) {
	
	?>
							  <h5 class='top-focus'>Remaining Performance Opportunitites</h5> 
							<?php foreach ($post['mobile_needs'] as $mobile_rule_data) { 
										if ($mobile_rule_data['rule_impact'] < .95) {
	?><div class='rule-container'>
							   <h5 class='rule-name'><?php echo $mobile_rule_data['rule_name'];?> 
								   <span class='badge badge-default'><?php if ($post['version'] == 5) {  print number_format($mobile_rule_data['rule_score'] * 100, 1, '.', ''); 
								    } else { echo number_format($mobile_rule_data['rule_impact'], 1, '.', ''); 
										   } ?>/100</span></h5>
					 <p><?php echo $mobile_rule_data['rule_long_description']; ?></p> 
							  <?php } ?>
	</div>
	<?php
	} ?>
	<?php } ?>
							  </div>
										  
							<div class="recommendations-container modal-desktop-recommendations-container">
 <div class='issue-summary'>PageSpeed Score Below Optimal</div>
							  <div class='issue-description'>Desktop PageSpeed Score: <?php echo $post['score'];?>
								  
Top Desktop Priority: <?php echo $rule_data['rule_name']; ?> (<?php echo $rule_data['rule_impact']; ?>/100)
	
Mobile PageSpeed Score: <?php echo $post['mobile_score'];?>
								  
Top Mobile Priority: <?php echo $mobile_rule_data['rule_name']; ?> (<?php echo $rule_data['mobile_rule_impact']; ?>/100)
	
Current Settings
----------------								  
<?php foreach (PegasaasAccelerator::$settings['settings'] as $setting => $data) { ?>
<?php echo $setting.": ".$data['status']; ?>
								  
<?php } ?>	
</div>
<div class='issue-url'><?php echo $post['slug']; ?></div>
							<h4>
								<?php if ($post['score'] < 85 && PegasaasAccelerator::$settings['limits']['support'] == 1) { ?>
								<button class='btn btn-default pull-right btn-help-request'><i class='fa fa-life-ring'></i> Initiate Support Ticket</button>
								<?php } ?>
								<i class='fa fa-desktop'></i> Desktop <span class='badge badge-default'><?php echo $post['score'];?>/100</span> </h4>
							<?php if ($post['score'] >= 95) { ?>
							  <p class='summary'>Super fantastic!  This page is well optimized for desktop.</p>
							<?php } else if ($post['score'] >= 90) { ?>
							  <p class='summary'>It looks like maybe we should look at a few things to see if we can bring this score up a bit more.</p>
							<?php } else if ($post['score'] >= 85) { ?>
							  <p class='summary'>Hmm, while <?php echo $post['score'];?>/100 is not terrible, we would like to see this above 90%.  <?php if (PegasaasAccelerator::$settings['limits']['support'] == 0) { ?>Try the recommendations below.<?php } else { ?>  If the recommendations below don't help, then initiate a support ticket by going to the Support tab, our team can look into this.<?php } ?></p>
							<?php } else { ?>
							  <p class='summary'>Good grief!  It looks like there are some major obstacles in the way of getting you a decent score.     <?php if (PegasaasAccelerator::$settings['limits']['support'] == 0) { ?>Try the recommendations below.<?php } else { ?> If the recommendations below don't help, then initiate a support ticket by clicking the button above, our team can look into this.<?php } ?></p>
							<?php } ?>
							  <?php if ($post['score'] < 100) { ?>
							  <h5 class='top-focus'>Your Top Focus</h5>
							 

					 <h5 class='rule-name'><?php echo $rule_data['rule_name'];?> <span class='badge badge-default'>
						 <?php if ($post['version'] == 5) {  print number_format($mobile_rule_data['rule_score'] * 100, 1, '.', ''); 
								    } else { echo number_format($mobile_rule_data['rule_impact'], 1, '.', ''); 
								   } ?>/100</span></h5>
					 <p><?php echo $rule_data['rule_long_description']; ?></p> 
																
		
				<?php } ?>
					  
				 <?php // } ?>
							  </div>
							
						  </div>
						  <div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Got It!</button>
							
						  </div>
						</div>
					  </div>
					</div>

				<?php }
					  
	}
	
	
	

	 function setup_page_post_hooks() {
		 if (current_user_can("administrator")) {
			$post_types = get_post_types( array( 'public' => true ), 'names' );

			if (is_array($post_types) && $post_types !== array() ) {
				foreach ($post_types as $post_type) {
						add_filter("manage_{$post_type}_posts_columns", array($this, "admin_posts_column_headings" ), 10, 1);
						add_action("manage_{$post_type}_posts_custom_column", array($this, "admin_posts_column_content"), 10, 2);
						add_action("manage_edit-{$post_type}_sortable_columns", array($this, "admin_posts_column_sort"), 10, 2);
						add_filter("manage_edit-category_columns", array($this, "admin_posts_column_headings" ), 10, 1);
						add_action('manage_category_custom_column' , array($this, "admin_categories_column_content"), 10, 3);

					//$filter = sprintf("get_user_option_%s", sprintf("manage%scolumnshidden", "edit-{$post_type}") );
						//add_filter( $filter, array($this, "admin_posts_column_hidden" ), 10, 3 );

				}
			}
		 }
	}

	/**
     * Add link on archive
     *
     * @uses   get_post_type_object, get_archive_post_link, current_user_can, esc_attr
     * @access public
     * @since  0.0.1
     * @param  array string $actions
     * @param  integer $id
     * @return array $actions
     */
    function add_clear_cache_link( $actions, $object ) {
		global $pegasaas;
      
		if (is_object($object) && get_class($object) == "WP_Term") {
			
			$resource_id = $pegasaas->utils->get_object_id("category__".$object->term_id);
		} else if (is_object($object) && get_class($object) == "WP_Post") {
		
			$resource_id = $pegasaas->utils->get_object_id($object->ID);
			
		} else {
			return $actions;
		}
		
		$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides");
		if (isset($page_level_settings['accelerated']) && ($page_level_settings['accelerated'] == 1 || $page_level_settings['accelerated'] == true)) {
			$cache_state_links_class = "";
		} else{
			$cache_state_links_class = " hidden";
		}	
		
		$cache_path 			= PEGASAAS_CACHE_FOLDER_PATH."{$resource_id}";

		$html_filename = $cache_path.'index.html';
		$html_temp_filename = $cache_path.'index-temp.html';
		
        	$actions['pa-clear-cache-link'] = '<span class="pa-clear-cache-link "><a href="#" class="pegasaas-accelerator-clear-cache-link" rel="'.$resource_id.'"' 
            . ' title="'
            . esc_attr( __( 'Clear Cache', $pegasaas->textdomain  ) ) 
            . '">' . __( 'Clear Cache', $pegasaas->textdomain  ) . '</a></span>';
		
        return $actions;
    }
	
	
	
    function add_build_cache_link( $actions, $object ) {
		global $pegasaas;
	
		if (is_object($object) && get_class($object) == "WP_Term") {
			$resource_id = $pegasaas->utils->get_object_id("category__".$object->term_id);
		} else if (is_object($object) && get_class($object) == "WP_Post") {
			$resource_id = $pegasaas->utils->get_object_id($object->ID);
		} else {
			return $actions;
		}
	
		$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides");
		if (isset($page_level_settings['accelerated']) && ($page_level_settings['accelerated'] == 1 || $page_level_settings['accelerated'] == true)) {
			$cache_state_links_class = "";
		} else{
			$cache_state_links_class = " hidden";
		}	
		
		
        	$actions['pa-build-cache-link'.$cache_state_links_class] = '<span class="pa-build-cache-link '.$cache_state_links_class.'"><a href="#" class="pegasaas-accelerator-build-cache-link" rel="'.$resource_id.'"' 
            . ' title="'
            . esc_attr( __( ($pegasaas->is_standard() ? 'Optimize Now' : 'Build Cache'), $pegasaas->textdomain  ) ) 
            . '">' . __( ($pegasaas->is_standard() ? 'Optimize Now'  : 'Build Cache'), $pegasaas->textdomain  ) . '</a></span>';
			
		
        return $actions;
    }		
	

    function add_staging_mode_links( $actions, $object ) {
		global $pegasaas;
	
		if (is_object($object) && get_class($object) == "WP_Term") {
			$resource_id = $pegasaas->utils->get_object_id("category__".$object->term_id);
		} else if (is_object($object) && get_class($object) == "WP_Post") {
			$resource_id = $pegasaas->utils->get_object_id($object->ID);
		} else {
			return $actions;
		}
	
		$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides");
		
		if (isset($page_level_settings['accelerated']) && ($page_level_settings['accelerated'] == 1 || $page_level_settings['accelerated'] == true)) {
			$staging_mode_buttons_class = "";
		} else{
			$staging_mode_buttons_class = " hidden";
		}
		
		if (isset($page_level_settings['staging_mode_page_is_live']) && $page_level_settings['staging_mode_page_is_live'] == 1) {
			$staging_mode_page_is_live = true;
			$staging_mode_go_live_class = "hidden";
			$staging_mode_move_to_staging_class = "";
		} else {
			$staging_mode_page_is_live = false;
			$staging_mode_go_live_class = "";
			$staging_mode_move_to_staging_class = "hidden";
		}
		
		
        $actions['pa-staging-mode-links'.$staging_mode_buttons_class] = '<span class="pa-staging-mode-links '.$staging_mode_buttons_class.'"><a href="#" class="pa-staging-mode-go-live-link '.$staging_mode_go_live_class.'" rel="'.$resource_id.'"' 
            . ' title="'
            . esc_attr( __( 'Go Live', $pegasaas->textdomain  ) ) 
            . '">' . __( 'Go Live', $pegasaas->textdomain  ) . '</a><a href="#" class="pa-staging-mode-stage-page-link '.$staging_mode_move_to_staging_class.'" rel="'.$resource_id.'"' 
            . ' title="'
            . esc_attr( __( 'Move To Staging', $pegasaas->textdomain  ) ) 
            . '">' . __( 'Move To Staging', $pegasaas->textdomain  ) . '</a>';
			
		
        return $actions;
    }		
		
	
	function admin_posts_column_headings($columns) {
		global $pegasaas;    
 		
		if (PegasaasAccelerator::$settings['status'] == '1') { 
			if (PegasaasAccelerator::$settings['settings']['white_label']['status'] == 1) {
						$columns['pegasaas-accelerator-status'] 	= '<span class="pegasaas-accelerator-status-wl pegasaas-column-has-tooltip" title="Web Performance Enabled"><span class="screen-reader-text">'.__('Web Perf Enabled', 'pegasaas-accelerator').'</a></span>';
	
			} else {
						$columns['pegasaas-accelerator-status'] 	= '<span class="pegasaas-accelerator-status pegasaas-column-has-tooltip" title="Accelerated"><span class="screen-reader-text">'.__('Accelerated', 'pegasaas-accelerator').'</a></span>';
	
			}
			$columns['pegasaas-accelerator-perfscore'] 	= '<span class="pegasaas-accelerator-perfscore pegasaas-column-has-tooltip" rel="tooltip" title="'.__('PageSpeed Score', 'pegasaas-accelerator').'"><span class="screen-reader-text">'.__('Perf Score', 'pegasaas-accelerator').'</a></span>';
			$columns['pegasaas-accelerator-mobile-speed'] 		= '<span class="pegasaas-accelerator-speed pegasaas-column-has-tooltip" rel="tooltip" title="'.__('Mobile Load Time', 'pegasaas-accelerator').'"><span class="screen-reader-text">'.__('Mobile Speed Index', 'pegasaas-accelerator').'</a></span>';
			
			if ($pegasaas->is_standard()) {
				$columns['pegasaas-accelerator-cache'] 		= '<span class="pegasaas-accelerator-cache pegasaas-column-has-tooltip" rel="tooltip" title="'.__('Optimized', 'pegasaas-accelerator').'"><span class="screen-reader-text">'.__('Optimized', 'pegasaas-accelerator').'</a></span>';
			} else {
				$columns['pegasaas-accelerator-cache'] 		= '<span class="pegasaas-accelerator-cache pegasaas-column-has-tooltip" rel="tooltip" title="'.__('Page Cache', 'pegasaas-accelerator').'"><span class="screen-reader-text">'.__('Page Cache', 'pegasaas-accelerator').'</a></span>';

			}
		}
		
		return $columns;
	}

	function render_page_overview($post_id) {
		global $pegasaas;
		
		$post = get_post($post_id);
		
		$resource_id = $pegasaas->utils->get_object_id($post->ID);
		
		$page_score_data = PegasaasUtils::get_object_meta($resource_id, "accelerator_pagespeed_history");

		
			if (is_array($page_score_data)) {
					$page_score_data_record = array_pop($page_score_data);
					$page_score = $page_score_data_record['score'];
					
					//get_date_from_gmt( date( 'Y-m-d H:i:s', $my_unix_timestamp ), 'F j, Y H:i:s' );
					$last_scan = get_date_from_gmt(date('Y-m-d H:i:s', $page_score_data_record['when']),get_option( 'date_format' ) ).
					" (".get_date_from_gmt( date('Y-m-d H:i:s', $page_score_data_record['when']),get_option( 'time_format' )).")"; 
				} else {
					$page_score = "";
					$last_scan = "";
				}
				
				if ($page_score == "") {
					$page_score = "?";
				} 
				$page_level_settings = PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides");
		
			 	if ($page_level_settings['accelerated'] !== true) { ?>
                <div class='pegasaas-accelerator-pagespeed-data-overview-accelerator-off'>
                <i class='fa fa-warning'></i> Accelerator is <b>DISABLED</b> for this page.
                </div>

				<?php } else { ?>
                <div class='pegasaas-accelerator-pagespeed-data-overview-accelerator-on'>
                <i class='fa fa-check'></i> Accelerator is <b>ENABLED</b> for this page.
                </div>
               
                <?php } ?>
               <?php if ($last_scan != "") { ?>
                <div class='pegasaas-accelerator-last-scan'>
                <h4>PageSpeed Score <span class='<?php print $page_score_class; ?>'><?php echo $page_score; ?></span> <button class='btn btn-xs pull-right pegasaas-scan-now-button'>Scan Now</button></h4>
                <p>Last Scanned: <?php print $last_scan; ?></p>
                </div>
                <?php }
		
				$cached_page 	= PegasaasUtils::get_object_meta($resource_id, "cached_html");
				$has_cache 		= is_array($cached_page) && sizeof(@$cached_page) > 0;
		
				$total_cache 	= 0;
			
				if ($has_cache) {
					$when_built = $cached_page["NULL"]["when_cached"];
					foreach ($cached_page as $args => $data) {
						$total_cache += strlen($data['html']);
					}
					$cache_file = PEGASAAS_CACHE_FOLDER_PATH."".$resource_id."index.html";
				
					$total_cache += @filesize($cache_file);
				}
				?>
                <div class='pegasaas-accelerator-html-cache-info'>
                  <h4>HTML Cache<?php if ($has_cache) { ?><button class='btn btn-xs pull-right pegasaas-clear-html-cache-button'>Clear Cache (<?php echo number_format((@$total_cache)/1024); ?>KB)</button><?php 
				  } else { 
				  ?><button class='btn btn-xs disabled pull-right'>(<?php echo number_format(@($total_cache)/1024); ?>KB)</button>
                  <?php } ?>
                  </h4>
                  <?php if (@$has_cache != "") { ?><p>Built: <?php 
				  
				  echo get_date_from_gmt(date('Y-m-d H:i:s', $when_built),get_option( 'date_format' ) ).
					" (".get_date_from_gmt( date('Y-m-d H:i:s', $when_built),get_option( 'time_format' )).")"; 

				  
				  
				 // date_i18n( get_option( 'date_format' ), $cached_page['when_cached'] ) . " (".date_i18n( get_option( 'time_format' ), $cached_page['when_cached'] ).")";
				  
				  ?></p><?php } ?>

                </div>
                <?php
				$critical_css = PegasaasUtils::get_object_meta($resource_id, "critical_css");

				if ($critical_css['css'] > 0) { 
				?>
<div class='pegasaas-accelerator-css-cache-info'>
                  <h4>Critical CSS<?php if (@$critical_css['css'] != "") { ?><button class='btn btn-xs pull-right pegasaas-clear-css-cache-button'>Clear Cache (<?php echo number_format(strlen($critical_css['css'])/1024); ?>KB)</button><?php 
				  } else { 
				  ?><button class='btn btn-xs disabled pull-right'>(<?php echo number_format(strlen(@$critical_css['css'])/1024); ?>KB)</button>
                  <?php } ?>
                  </h4>
                  <?php if (@$critical_css['css'] != "") { ?><p>Built: <?php 
				  				  echo get_date_from_gmt(date('Y-m-d H:i:s', $critical_css['built']),get_option( 'date_format' ) ).
					" (".get_date_from_gmt( date('Y-m-d H:i:s', $critical_css['built']),get_option( 'time_format' )).")"; 

				//  echo date_i18n( get_option( 'date_format' ), $critical_css['built'] ) . " (".date_i18n( get_option( 'time_format' ), $critical_css['built'] ).")";  
				  
				  ?></p><?php } ?>

                </div>
                    <?php
				}
		
		$deferred_js = PegasaasUtils::get_object_meta($resource_id, "deferred_js");
		
		$total_deferred_js = strlen($deferred_js['deferred_js']);
					$deferred_js_file = PEGASAAS_CACHE_FOLDER_PATH."".$resource_id."deferred-js.js";
					$total_deferred_js += @filesize($deferred_js_file);				
				?>            
<div class='pegasaas-accelerator-js-cache-info'>
                  <h4>JS Cache<?php if (@$total_deferred_js > 0) { ?><button class='btn btn-xs pull-right disabled pegasaas-clear-js-cache-button'>Clear Cache (<?php echo number_format((@$total_deferred_js)/1024); ?>KB)</button><?php 
				  } else { 
				  ?><button class='btn btn-xs disabled pull-right'>(<?php echo number_format($total_deferred_js/1024); ?>KB)</button>
                  <?php } ?>
                  </h4>
                  <?php if (@$deferred_js['deferred_js'] != "") { ?><p>Built: <?php 
				  				  echo get_date_from_gmt(date('Y-m-d H:i:s', $deferred_js['when_updated']),get_option( 'date_format' ) ).
					" (".get_date_from_gmt( date('Y-m-d H:i:s', $deferred_js['when_updated']),get_option( 'time_format' )).")"; 
				  
	
				  ?></p><?php } ?>

                </div>                                

                <?php                
                
				$page_score_details = $pegasaas->scanner->get_page_speed_opportunities($post_id);
				if (sizeof($page_score_details) > 0) { ?>
                <p style='margin-top: 10px;'>The following items are the areas of opportunity that impact your PageSpeed score.</p>
                <?php
				print "<ul>";
					foreach ($page_score_details as $opportunity) {
						$points = number_format($opportunity['rule_impact'], 1);
						print "<li><span class='label label-default'>{$points}</span> {$opportunity['rule_name']}</li>";
	
					}
					print "</ul>";
				} else { 
				?><!--
                <p>Hey Sparky!  It looks like you've aced it.  There's nothing you need to work on with this page!</p>
-->
                <?php 
				}		
		
	}
	
	

	function update_administrative_menu() {
		global $pegasaas;
		PegasaasUtils::log("Start of update_administrative_menu", "script_execution_benchmarks");

		$svg_icon = PEGASAAS_ACCELERATOR_URL.'assets/images/icon.png';


		
		if (PegasaasAccelerator::$settings['settings']['white_label']['status'] == 1) {
			if (PegasaasAccelerator::$settings['settings']['white_label']['dashboard_menu_item_location'] == "main") {
				add_menu_page(PegasaasAccelerator::$settings['settings']['white_label']['menu_label']." ".__("Overview", "pegasaas-accelerator"), 
						  __(PegasaasAccelerator::$settings['settings']['white_label']['menu_label'], "pegasaas-accelerator"), 
						  "manage_options", 
						  "pa-web-perf", 
						  "pa_web_perf", 'dashicons-performance', 99);
			} else {
				add_management_page(PegasaasAccelerator::$settings['settings']['white_label']['menu_label']." ".__("Overview", "pegasaas-accelerator"), 
						  __(PegasaasAccelerator::$settings['settings']['white_label']['menu_label'], "pegasaas-accelerator"), 
						  "manage_options", 
						  "pa-web-perf", 
						  "pa_web_perf", 'dashicons-performance', 99);				
			}


		} else {
		
			add_menu_page("Pegasaas Accelerator: ".__("Overview", "pegasaas-accelerator"), 
						  __("Accelerator", "pegasaas-accelerator"), 
						  "manage_options", 
						  "pegasaas-accelerator", "pegasaas_accelerator_settings", $svg_icon, 99);

		}
		
		// if this is a site hosted at wordpress.com, where there is no top menu, then we need to provide sub-menu items so that cache can be cleared
		if (function_exists('posix_uname')) {
			$server_information = @posix_uname();

			if (is_array($server_information)) {
				if (strstr($server_information['nodename'], "atomicsites.net")) {
					add_submenu_page("pegasaas-accelerator", __("Clear Cache", "pegasaas-accelerator"), '<i class="fa fa-fw fa-trash"></i> Purge Page Cache', 'manage_options', "admin.php?page=pegasaas-accelerator&c=purge-page-cache");
					
					if (PegasaasAccelerator::$settings['settings']['minify_css']['status'] > 0 && PegasaasAccelerator::$settings['settings']['cdn']['status'] == 0) {			 
						add_submenu_page("pegasaas-accelerator", __("Clear Cache", "pegasaas-accelerator"), '<i class="fa fa-fw fa-trash"></i> Purge CSS Cache', 'manage_options', "admin.php?page=pegasaas-accelerator&c=purge-all-local-css-cache");
					}
						
					if (PegasaasAccelerator::$settings['settings']['basic_image_optimization']['status'] > 0 || (PegasaasAccelerator::$settings['settings']['image_optimization']['status'] > 0 && PegasaasAccelerator::$settings['settings']['cdn']['status'] == 0)) {			 
						add_submenu_page("pegasaas-accelerator", __("Clear Cache", "pegasaas-accelerator"), '<i class="fa fa-fw fa-trash"></i> Purge Image Cache', 'manage_options', "admin.php?page=pegasaas-accelerator&c=purge-all-local-image-cache");
					}
					if (PegasaasAccelerator::$settings['settings']['minify_js']['status'] > 0 && PegasaasAccelerator::$settings['settings']['cdn']['status'] == 0) {			 
						add_submenu_page("pegasaas-accelerator", __("Clear Cache", "pegasaas-accelerator"), '<i class="fa fa-fw fa-trash"></i> Purge JS Cache', 'manage_options', "admin.php?page=pegasaas-accelerator&c=purge-all-local-js-cache");
					}

				}
			}
		}		
		//add_submenu_page("pegasaas-accelerator", 'Accelerator', '<i class="'.PEGASAAS_DASHBOARD_ICON_CLASS.'"></i> Dashboard', 'manage_options', "pegasaas-accelerator");
		
		//add_submenu_page("pegasaas-accelerator", 'Accelerator', '<i class="fa fa-fw fa-history"></i> Scan History', 'manage_options', "admin.php?page=pegasaas-accelerator&tab=history");
		
		//add_submenu_page("pegasaas-accelerator", 'Accelerator', '<i class="'.PEGASAAS_CACHE_ICON_CLASS.'"></i> Local Cache', 'manage_options', "admin.php?page=pegasaas-accelerator&tab=cache");
		//add_submenu_page("pegasaas-accelerator", 'Accelerator', '<i class="'.PEGASAAS_SETTINGS_ICON_CLASS.'"></i> Settings', 'manage_options', "admin.php?page=pegasaas-accelerator&tab=settings");
		//add_submenu_page("pegasaas-accelerator", 'Accelerator', '<i class="'.PEGASAAS_USER_ACCOUNT_ICON_CLASS.'"></i> Account', 'manage_options', "admin.php?page=pegasaas-accelerator&tab=account");
	
		//add_submenu_page("pegasaas-accelerator", 'Accelerator', '<i class="'.PEGASAAS_FAQS_ICON_CLASS.'"></i> FAQs', 'manage_options', "admin.php?page=pegasaas-accelerator&tab=faqs");
		
		//add_submenu_page("pegasaas-accelerator", 'Accelerator', '<i class="'.PEGASAAS_SUPPORT_ICON_CLASS.'"></i> Support', 'manage_options', "admin.php?page=pegasaas-accelerator&tab=support");		
		PegasaasUtils::log("End of update_administrative_menu", "script_execution_benchmarks");

	}	
	
	function get_expires_in($output_days = true) {
		global $pegasaas;
		
		$seconds_in_day = 60*60*24;

		$time_to_expiry = strtotime(PegasaasAccelerator::$settings['renewal_date']) - time();
		
		$days_to_expiry = floor($time_to_expiry / $seconds_in_day);
		if ($output_days) {
			print $days_to_expiry." days";		
		} else {
			return $days_to_expiry;
		}
	}

	function pegasaas_fetch_page_metrics() { 
		global $pegasaas;
		global $test_debug;
		
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$data = array();
			$data['status'] = 1;

			
			$data['pages']  = $pegasaas->scanner->get_scanned_objects("page_importance");
			if ($test_debug) { 
				PegasaasUtils::log_benchmark("Pages Array Size: ".sizeof($data['pages']), "debug-li", 1);
			}
			$full_cache_map = PegasaasAccelerator::$cache_map;
			$data['cache_map'] = array();
	
			$data['settings'] 					= PegasaasAccelerator::$settings;
			$data['settings']['is_pro_edition'] = $pegasaas->is_pro_edition();
			
			$data['max_results_page'] 			= $pegasaas->interface->max_results_page; 
			$data['results_per_page'] 			= $pegasaas->interface->results_per_page; 
			$data['current_results_page']		= $pegasaas->interface->current_results_page;
			
			foreach ($data['pages'] as $index => $obj) {
				$last_scanned = date("Y-m-d H:i", $obj['last_scan']);
				$last_scanned_ago_difference = time() - $obj['last_scan'];
				if ($last_scanned_ago_difference > 86400) {
					$last_scanned_ago = floor($last_scanned_ago_difference / 86400)." days ago";
				} else if ($last_scanned_ago_difference > 7200) {
					$last_scanned_ago = floor($last_scanned_ago_difference / 3600)." hours ago";

				} else {
					$last_scanned_ago = floor($last_scanned_ago_difference / 60)." minutes ago";
				}
				$object_id = $obj['resource_id'];
				
				
				// copy records from the full cache map -- we don't want to send the full cache map as it
				// can be very large (only need to pass along the data relevant to the 'pages' array)
				if (isset($full_cache_map["{$object_id}"])) {
					$data['cache_map']["{$object_id}"] = $full_cache_map["{$object_id}"];
				}
				
				$last_cached 				= date("Y-m-d H:i", $data['cache_map']["$object_id"]['built']);
				$last_cached_ago_difference = time() - $data['cache_map']["$object_id"]['built'];
				
				if ($last_cached_ago_difference > 86400) {
					$last_cached_ago = floor($last_cached_ago_difference / 86400)." days ago";
				} else if ($last_cached_ago_difference > 7200) {
					$last_cached_ago = floor($last_cached_ago_difference / 3600)." hours ago";

				} else {
					$last_cached_ago = floor($last_cached_ago_difference / 60)." minutes ago";
				}
				
				$data['pages'][$index]['when_last_everything'] = "<div class='text-left when-optimized-tooltip'>Last Cached: {$last_cached_ago} ($last_cached)<br>Last Scanned: $last_scanned_ago ({$last_scanned})</div>";
			}
			print json_encode($data);
			
			
		} else {
			print json_encode(array("status" => -2));
		}
		if (!$test_debug) {
			wp_die();
		}
	}
	
	
	
	function pegasaas_fetch_site_metrics() {
		global $pegasaas;
		
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$data = array();
			$data['pagespeed'] = array();
			$data['pagespeed']['accelerated'] 	= $pegasaas->scanner->get_site_score();
			$data['pagespeed']['baseline'] 	  	= $pegasaas->scanner->get_site_benchmark_score(); 
			$data['performance_metrics'] 		= $pegasaas->scanner->get_site_performance_metrics();
			
			print json_encode($data);
			
			
		} else {
			print json_encode(array("status" => -2));
		}
		wp_die();
	}	
	
	
	function pegasaas_dashboard_settings_update() {
		global $pegasaas;
		
		if ($_POST['api_key'] == PegasaasAccelerator::$settings['api_key']) {
			$data = array();
			$data['status'] = -1;
			
			
			// handle a request to disable a feature
			if ($_POST['args']['c'] == "disable-feature") {
				$pegasaas->disable_feature($_POST['args']['f']);
				$data['status'] = 2;
				if (array_key_exists('prompt', $_POST['args']) && $_POST['args']['prompt'] == "clear_cache") {
					if (array_key_exists('cache', $_POST['args']) && $_POST['args']['cache'] == "do-not-clear") {
						$data['cache'] = "not-cleared";
					} else {
						$data['cache'] = "cleared";
						$pegasaas->cache->clear_html_cache();	
					}
				}
				
			// handle a request to enable a feature
			} else if ($_POST['args']['c'] == "enable-feature") {
				$pegasaas->enable_feature($_POST['args']['f']);
				$data['status'] = 3;
				if (array_key_exists('prompt', $_POST['args']) && $_POST['args']['prompt'] == "clear_cache") {
					if (array_key_exists('cache', $_POST['args']) && $_POST['args']['cache'] == "do-not-clear") {
						$data['cache'] = "not-cleared";
					} else {
						$data['cache'] = "cleared";
						$pegasaas->cache->clear_html_cache();	
					}
				}
				
				

			// handle a request to enable a local setting such as:
			// - exclude urls
			// - manually added scripts to be lazy loaded
			} else if ($_POST['args']['c'] == "change-local-setting") {
				$pegasaas->save_local_setting($_POST['args']['f'], $_POST['args']['s']);
				$data['status'] = 8;
				$data['feature'] = $_POST['args']['f'];
								
			// handle feature state which are different from a simple enable/disable, such as:
			// - defer unused css -- 0,1,1500,7500
			} else if ($_POST['args']['c'] == "change-feature-status") {
				$feature = $_POST['args']['f'];
				
				$pegasaas->set_feature($feature, $_POST['args']['s']);
				$data['status'] = 4;
				
				if ($feature == "api_submit_method") {
					update_option("pegasaas_accelerator_api_non_blocking_until", 0);
				}
				
			// handle feature attributes which are saved at locally such as:
			// - lazy loaded script
			} else if ($_POST['args']['c'] == "toggle-local-setting") {
				
				$pegasaas->toggle_local_setting($_POST['args']['f']);
				$data['status'] = 5;
				
			// handle complex feature attributes which are saved at locally such as:
			// - lazy load script attributes	
			} else if ($_POST['args']['c'] == "toggle-local-complex-setting"  || $_POST['args']['c'] == "change-local-complex-setting") {
				
				$pegasaas->toggle_local_complex_setting($_POST['args']);
				$data['status'] = 6;
				
			// handle feature attribute which is saved at the API such as:
			// - "retain_exif" for "image_optimization" 
			// - "css_cache_busting" and "js_cache_busting" for "cdn"	
			} else if ($_POST['args']['c'] == "change-feature-attribute") {
				$feature = $_POST['args']['f'];
				//var_dump($_POST);
				//var_dump(PegasaasAccelerator::$settings['settings']);
				$feature_status = PegasaasAccelerator::$settings['settings']["{$feature}"]['status'];
				if ($_POST['args']['s'] == "on") {
					$_POST['args']['s'] = "1";
				} else if ($_POST['args']['s'] == "") {
					$_POST['args']['s'] = 0;
				}
				//var_dump($_POST);
				$secondary_status = $pegasaas->set_feature($feature, $feature_status, array($_POST['args']['a'] => $_POST['args']['s']));
				$data['status'] = 7;
				$data['secondary_status'] = $secondary_status;
				if ($secondary_status == "extended-coverage-cache-clearing-queued") {
					$data['cache_clearing_queued'] = true;
				}
				
				if ($feature == "api_submit_method") {
					update_option("pegasaas_accelerator_api_non_blocking_until", 0);
				}

				
			// handle feature attribute removal, which is saved at the API such as:
			// - "ip" for "multi_server" 	
			} else if ($_POST['args']['c'] == "remove-item-complex-setting") {
				$data['status'] = 1;
				$success = $pegasaas->remove_item_complex_setting($_POST['args']);
				if (!$success) {
					$data['status'] = 0;
				}
			}
			
			// if the request is one which may have instructions about clearing cache, then 
			// handle the submitted data accordingly
			if ($data['status'] > 1) {
				if (array_key_exists('prompt', $_POST['args']) && $_POST['args']['prompt'] == "clear_cache") {
					if (array_key_exists('cache', $_POST['args']) && $_POST['args']['cache'] == "do-not-clear") {
						$data['cache'] = "not-cleared";
					} else {
						$data['cache'] = "cleared";
						$pegasaas->cache->clear_html_cache();	
					}
				}
			}
			
			print json_encode($data);
			
			
		} else {
			print json_encode(array("status" => -2));
		}
		wp_die();
		
	
	}
	function pegasaas_plugin_row_meta($links, $file) {
		
		if ($file != 'pegasaas-accelerator-wp/pegasaas-accelerator-wp.php' && 
		$file != 'pegasaas-accelerator-wp3/pegasaas-accelerator-wp.php') { return $links; }

		if (PegasaasAccelerator::$settings['settings']['white_label']['status'] == 1) {
			unset($links[2]);
		}
		return $links;
	}
	
	function pegasaas_plugin_action_links($links, $file) {
		global $pegasaas;
		
		// do not modify links if this is not our plugin
		if ($file != 'pegasaas-accelerator-wp/pegasaas-accelerator-wp.php' && 
		    $file != 'pegasaas-accelerator-wp3/pegasaas-accelerator-wp.php') { return $links; }
		
		// get the deactivate link and add a deactivation target so that we can do an exit survey
		foreach ($links as $key => $link) {
			if ('deactivate' === $key) {
				$links[$key] = $link . '<i class="pegasaas-accelerator-deactivation-target"></i>';
			}
		}
		
		// add a manage link
		if (PegasaasAccelerator::$settings['settings']['white_label']['status'] == 1) {	
			$pegasaas_url = admin_url( 'admin.php?page=pa-web-perf' );
		} else {
			$pegasaas_url = admin_url( 'admin.php?page=pegasaas-accelerator' );
		}
		
		$new_links['manage'] = '<a href="' . $pegasaas_url . '">'.__('Manage', "pegasaas-accelerator").'</a>';
		
		// add an upgrade link
		if ($pegasaas->is_free()) {
			$new_links['upgrade'] = "<a target='_blank' id='upgrade-to-premium' href='https://pegasaas.com/upgrade/?utm_source=pegasaas-accelerator-wp-plugins-panel&upgrade_key=".PegasaasAccelerator::$settings['api_key']."-".PegasaasAccelerator::$settings['installation_id']."&site=".$pegasaas->utils->get_http_host()."&current=".PegasaasAccelerator::$settings['subscription']."'>".__('Upgrade To Premium', "pegasaas-accelerator").'</a>';
		}
		
		$links = array_merge($new_links, $links);
		return $links;
	}	
	
	
	
	function frontend_enqueue() {
		global $pegasaas;
		
		if (current_user_can("administrator")) {

			wp_enqueue_style(  'pegasaas-accelerator-admin-bar-styles',  PEGASAAS_ACCELERATOR_URL  . 'assets/css/admin-bar.css?t='.time(), time() );
			wp_enqueue_script( 'pegasaas-accelerator-admin-bar-js',		 PEGASAAS_ACCELERATOR_URL  . 'assets/js/admin-bar.js?t='.time(), array('jquery'), '1.0', true);
			
			$localize_script_parameters = array();
			$localize_script_parameters['ajax_url'] = admin_url("admin-ajax.php");
			$localize_script_parameters['api_key'] = PegasaasAccelerator::$settings['api_key'];
			
			//if ($this->kinsta_exists()) {
				//$localize_script_parameters['kinsta_clear_cache_all_nonce'] = wp_create_nonce( 'kinsta-clear-cache-all' );

			//}
			
			
			
			wp_localize_script( 'pegasaas-accelerator-admin-bar-js', 'pegasaas_ajax_object', $localize_script_parameters );

		}
	}	
	

	
	
	function get_progress($metric) {
		global $pegasaas;
		
		$data = array(
			'percent' => 100,
			'pending_scans' => 0,
			'base' => 1,
			'estimated_time' => 'Completed!'
		);
		
		
		
	
		

	
	//$pagespeed_pending_requests 			= get_option('pegasaas_accelerator_pagespeed_score_request_tokens', array());
	
	//$pagespeed_pending_benchmark_requests 	= get_option('pegasaas_accelerator_pagespeed_benchmark_score_request_tokens', array());
	//$pagespeed_score_benchmark_requests 	= get_option('pegasaas_accelerator_pagespeed_benchmark_score_requests', array());		
		
		if ($metric == "benchmark") {
			$queued_records 				= (get_option('pegasaas_accelerator_pagespeed_benchmark_score_requests', array()));
			$accelerated_pages 				= 0;	
			$all_pages_and_posts = PegasaasUtils::get_all_pages_and_posts();
			foreach ($all_pages_and_posts as $obj) {
				if ($obj->accelerated) { $accelerated_pages++; }
			}
			$pages_with_scores				= get_option("pegasaas_accelerator_pagespeed_benchmark_scores", array());
	
			foreach ($queued_records as $resource_id => $info) {
				unset($pages_with_scores["{$resource_id}"]);
			}
			
			$data['base'] = $accelerated_pages;
			if (sizeof($queued_records) > 0) {
				$data['pending_scans'] = sizeof($queued_records);
				$base = $accelerated_pages;
				
				if ($base == 0) {
					$data['percent'] = 0;
				} else {
					$data['percent'] = number_format(($base-sizeof($queued_records))/$base*100, 0, '.', '');
				}
				if ($data['pending_scans'] > 75) {
					$data['estimated_time'] = "Estimated ".ceil($data['pending_scans']/60)." hours";
				} else {
					$data['estimated_time'] = "Estimated ".($data['pending_scans'])." minutes";
				}		
			}
			
		
			
		} else if ($metric == "pagespeed") {
			$queued_records 				= (get_option('pegasaas_accelerator_pagespeed_score_requests', array()));
			$accelerated_pages 				= 0;	
			$all_pages_and_posts = PegasaasUtils::get_all_pages_and_posts();
			
			
			
			
			$pages_with_scores				= get_option("pegasaas_accelerator_pagespeed_scores", array());
			
			
			foreach ($queued_records as $resource_id => $info) {
				unset($pages_with_scores["{$resource_id}"]);
			}
			
			
			$actual_pages_with_scores = array();
			foreach ($all_pages_and_posts as $obj) {
				if ($obj->accelerated) { 
					$accelerated_pages++; 
					$resource_id = $obj->resource_id;
					
					if (array_key_exists($resource_id, $pages_with_scores)) {
					
						$actual_pages_with_scores["{$resource_id}"] = true;
					}
					
									   
				}
				
			}		
		
			$data['base'] = $accelerated_pages;

			if (sizeof($queued_records) > 0) {
				$data['pending_scans'] = sizeof($queued_records);
				$base 			= $accelerated_pages;
				$data['percent'] = number_format(sizeof($actual_pages_with_scores)/$base*100, 0, '.', '');

				if ($data['pending_scans'] > 75) {
					$data['estimated_time'] = "Estimated ".ceil($data['pending_scans']/60)." Hours";
				} else {
					$data['estimated_time'] = "Estimated ".($data['pending_scans'])." Minutes";
				}		
			}
		
		 	
		} else if ($metric == "cpcss") {
			$existing_records = sizeof($pegasaas->get_critical_css_records("post_type")) + sizeof($pegasaas->get_critical_css_records("custom"));
			$queued_records	= sizeof(get_option('pegasaas_pending_critical_css_request', array()));
			
			$data['pending_scans'] = $queued_records;
			
			$base = $existing_records + $queued_records;
			if ($base == 0) {
				$data['percent'] = 0;
			} else {
				$data['percent'] = number_format(($base - $queued_records)/$base*100, 0, '.', '');
			}
	
		
				
		
			$data['base'] = $base;

			if ($data['pending_scans'] > 0) {
				if ($data['percent'] > 75) {
					$data['estimated_time'] = "Estimated ".ceil($data['pending_scans']/60)." hours";
				} else {
					$data['estimated_time'] = "Estimated ".($data['pending_scans'])." minutes";
				}
			} else {
				$data['percent'] = 100;
			}
			
		}
		return $data;
		
	}
	

			
	function render_circle_gauge($metric, $score_data) {
		$color['fast'] 		= "#64BC63";
		$color['average'] 	= "#e67700";
		$color['slow'] 		= "#cc0000";
		$color['pending'] 		= "#cccccc";
		
		if ($metric == "pagespeed-mobile" || $metric == "pagespeed-desktop") {
			$score = $score_data;
			if ($score >= 90) {
				$score_class = "fast";
			} else if ($score >= 50) {
				$score_class = "average";
			} else if ($score == "") {
				$score_class = "pending";
			} else {
				$score_class = "slow";
			} 
			
			if ($score_class == "pending") { 
				if ($score_percent == "") {
					$gauge_content = "<i title='Pending' class='fa fa-hourglass-2 pegasaas-tooltip'></i>";
					$score_percent = 0;
				}
			} else {
				$score_percent = $score;
				$gauge_content = $score."%";
			}
			
			
		
		} else if ($metric == "speedindex-mobile" || $metric == "speedindex-desktop") {
			
			$score = $score_data['value'];
			$score_percent = $score_data['score'] * 100;
			
			 if ($score == "" || $score == 0.0) {
				$score_class = "pending";
			} else if ($score <= 3.387) {
				$score_class = "fast";
			} else if ($score <= 5.8) {
				$score_class = "average";
			} else {
				$score_class = "slow";
			}

			
			if ($score_class == "pending") { 
				$gauge_content = "<i title='Pending' class='fa fa-hourglass-2 pegasaas-tooltip'></i>";
				$score_percent = 0;
			
			} else {
				$gauge_content = $score."s";
			}			
		
		} else if ($metric == "interactive-mobile" || $metric == "interactive-desktop") {
			$score = $score_data['value'];
			$score_percent = $score_data['score'] * 100;
			
			if ($score <= 3.785) {
				$score_class = "fast";
			} else if ($score <= 7.3) {
				$score_class = "average";
			} else {
				$score_class = "slow";
			}
			

			$gauge_content = $score."s";
			
		} else if ($metric == "fcp-mobile" || $metric == "fcp-desktop") {
			$score = $score_data['value'];
			$score_percent = $score_data['score'] * 100;			
			
			if ($score <= 2.336) {
				$score_class = "fast";
			} else if ($score <= 4.0) {
				$score_class = "average";
			} else {
				$score_class = "slow";
			}
			

			$gauge_content = $score."s";
		
		} else if ($metric == "fmp-mobile" || $metric == "fmp-desktop") {
			$score = $score_data['value'];
			$score_percent = $score_data['score'] * 100;
			
			if ($score <= 2.336) {
				$score_class = "fast";
			} else if ($score <= 4.0) {
				$score_class = "average";
			} else {
				$score_class = "slow";
			}
			
			$gauge_content = $score."s";
		} else if ($metric == "fci-mobile" || $metric == "fci-desktop") {
			$score = $score_data['value'];
			$score_percent = $score_data['score'] * 100;			
			
			if ($score <= 3.387) {
				$score_class = "fast";
			} else if ($score <= 5.8) {
				$score_class = "average";
			} else {
				$score_class = "slow";
			}
			
			$gauge_content = $score."s";
		} 
		
		$bar_color = $color["{$score_class}"];
		?><div class='pegasaas-accelerator-perfscore pegasaas-score-bar <?php echo $score_class; ?>' data-percent="<?php echo $score_percent; ?>" data-duration="2000" data-color="#ccc,<?php echo $bar_color; ?>" data-content="<?php echo $gauge_content; ?>"></div>
		<?php

		
	}
	
	function display_page_post_options( $object ) {
		

		global $pegasaas;
		
		$system_settings = get_option("pegasaas_settings", array());
		$resource_id 	 = $pegasaas->utils->get_object_id($object->ID);
		
	  	$page_level_configurations 	= PegasaasUtils::get_object_meta($resource_id, "accelerator_overrides"); 
		$page_level_settings 		= $page_level_configurations;
		$configurable_settings 		= array("page_caching",  
											"defer_render_blocking_js", "defer_render_blocking_css", 
											"defer_unused_css",
											"image_optimization", 
											"inject_critical_css", 
											"lazy_load_images", "lazy_load_background_images", "lazy_load_scripts", "lazy_load_youtube", "lazy_load", 
											"minify_html", "minify_css", "minify_js");
		
		$view_mode = $_COOKIE['pegasaas_display_mode'] == "" || $_COOKIE['pegasaas_display_mode'] == "multi-mode" ? "mobile-mode" : $_COOKIE['pegasaas_display_mode']; 
		$page_score_data = $pegasaas->scanner->pegasaas_fetch_pagespeed_last_scan($resource_id);
			
	?>

<div id="pegasaas-accelerator__page_post_options_sidebar" <?php if ($page_level_configurations['accelerated'] != true) { ?>class='pegasaas-disabled <?php echo $view_mode; ?> <?php if ($page_level_settings['prioritized'] != true) { ?>prioritization-disabled<?php } ?>'<?php } else { ?>class='<?php echo $view_mode; ?> <?php if ($page_level_settings['prioritized'] != true) { ?>prioritization-disabled<?php } ?>'<?php } ?>>
	<div class='pegasaas-feature-box-summary'>
		<?php _e('Web Performance Enabled', 'pegasaas-accelerator'); ?> 
		<div style='float: right;'><input type='checkbox' data-pegasaas-resource-id='<?php echo $resource_id; ?>' style='display:none;' class='js-switch js-switch-smallest indv-js-switch <?php if ($page_level_settings['accelerated'] == true) { ?>resource-accelerated<?php } ?>' <?php if ($page_level_settings['accelerated'] == true) { ?>checked<?php } ?> /></div>
	</div>

	<div class='pegasaas-feature-box-sidebar'>
		<?php if (isset(PegasaasAccelerator::$settings['limits']['page_prioritizations']) && PegasaasAccelerator::$settings['limits']['page_prioritizations'] > 0) { ?>
		<div class='pegasaas-feature-box-summary page-optimization-prioritized'>
			<?php _e('Prioritize Re-Optimization', 'pegasaas-accelerator'); ?> 
			<div style='float: right;'><input type='checkbox' data-pegasaas-resource-id='<?php echo $resource_id; ?>' style='display:none;' class='js-switch js-switch-smallest indv-js-switch <?php if ($page_level_settings['prioritized'] == true) { ?>resource-prioritized resource-prioritization<?php } else { ?>resource-prioritization<?php } ?>' <?php if ($page_level_settings['prioritized'] == true) { ?>checked<?php } ?> /></div>
		</div>
		
		<?php } ?>		
		<div class='pegasaas-subsystem-title mobile-only'>
			PageSpeed Score (Mobile)
			<?php self::render_circle_gauge("pagespeed-mobile", $page_score_data['mobile_score']); ?>
		</div>
		<div class='pegasaas-subsystem-title desktop-only'>
			PageSpeed Score (Desktop)
			<?php self::render_circle_gauge("pagespeed-desktop", $page_score_data['score']); ?>
		</div>
		<div class='pegasaas-subsystem-title mobile-only'>
			Load Time (Mobile)
			<?php self::render_circle_gauge("speedindex-mobile", $page_score_data['meta']['mobile']['lab_data']['speed-index']); ?>
		</div>
		<div class='pegasaas-subsystem-title desktop-only'>
			Load Time (Desktop)
			<?php self::render_circle_gauge("speedindex-desktop", $page_score_data['meta']['desktop']['lab_data']['speed-index']); ?>
		</div>		
		
			<?php if ($pegasaas->in_development_mode()) { ?>
		<div class='pegasaas-subsystem-title'>
			Staging Mode 
			<a class='button btn-inspect' target='_blank' href='<?php echo $resource_id; ?>?inspect-web-perf'>Inspect</a>
			
		</div>			
			<?php } ?>

		
	</div>
	<div class='pegasaas-feature-box-sidebar'>
		<div class='pegasaas-subsystem-title'>
			<?php echo __("Auto Clear Cache", "pegasaas-accelerator"); ?>
			<i class='fa fa-fw  fa-angle-down  feature-box-toggle'></i>
		</div>
		<div class='pegasaas-accelerator-subsystem-feature-description'>
			<p>If you have a page that contains dynamic parts, such as an e-commerce page or real estate listings page, you can use the page-level auto cache clearing feature. </p>
				<select class='form-control' name="pegasaas_accelerator__auto_clear_page_cache">
					<option <?php if (@$page_level_configurations['auto_clear_page_cache'] == "") { ?>selected<?php } ?> value=''><?php echo __("Disabled", "pegasaas-accelerator"); ?></option>
					<option <?php if (@$page_level_configurations['auto_clear_page_cache'] == 'monthly') { ?>selected<?php } ?> value='monthly'><?php echo __("Monthly", "pegasaas-accelerator"); ?></option>
					<option <?php if (@$page_level_configurations['auto_clear_page_cache'] == "biweekly") { ?>selected<?php } ?> value='biweekly'><?php echo __("Bi-Weekly", "pegasaas-accelerator"); ?></option>
					<option <?php if (@$page_level_configurations['auto_clear_page_cache'] == 'weekly') { ?>selected<?php } ?> value='weekly'><?php echo __("Weekly", "pegasaas-accelerator"); ?></option>
					<option <?php if (@$page_level_configurations['auto_clear_page_cache'] == 'daily') { ?>selected<?php } ?> value='daily'><?php echo __("Daily", "pegasaas-accelerator"); ?></option>
				</select>	
		</div>
	</div>
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
				<p>To initiate the page render as fast as possible, render-blocking CSS should be deferred.  You should have the "Inject Critical CSS" enabled also, if you have this feature enabled.  If you are having display issues, you can attempt to disable this feature.</p>
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
					$resource_id = $pegasaas->utils->get_object_id($object->ID);
				
			
					
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

</div>
<script>
		function resize_subsystem_feature_description() {
		var feature_description_size = 0;  
		jQuery(".pegasaas-accelerator-subsystem-feature-description").height("");
		
		jQuery(".pegasaas-accelerator-subsystem-feature-description").each(function() {
		  if (jQuery(this).height() > feature_description_size) {
			  feature_description_size = jQuery(this).height();
			

		  }	else {
			  console.log(jQuery(this).height());
		  }
		});
		if (feature_description_size > 0) {
			jQuery(".pegasaas-accelerator-subsystem-feature-description").css("height", (feature_description_size) + "px");
		}
		jQuery(".pegasaas-accelerator-subsystem-feature-description").removeClass("not-resized");
	}
	//jQuery(window).load(function() { resize_subsystem_feature_description(); });
	//jQuery(window).resize(function() { resize_subsystem_feature_description(); });
	jQuery("#settings-nav-button").on("shown.bs.tab", function() { resize_subsystem_feature_description(); });
	
</script>
<?php
	}	 	
	
	function get_human_readable_filesize_info($filesize) {
		$unit = "";
			if ($filesize / 1024 < 1000) {
				$filesize =  number_format($filesize/1024, 0, '.', ',');
				$unit = "KB";	
			} else if ($filesize / 1024 / 1024 < 1000) {
				$filesize =  number_format($filesize/1024/1024, 0, '.', ',');
				$unit = "MB";	
			} else { 
				$filesize =  number_format($filesize/1024/1024/1024, 0, '.', ',');
				$unit = "GB";	
				
			} 
		return array($filesize, $unit);
		
	}
	
	function generate_mega_menu_html($section) {
		global $pegasaas;
		
		$html = "";
		if ($section == "scores" && false) {
			
			$score_data = $pegasaas->scanner->get_site_score(); 
	
		 	
			$score_data['average_score'] = $score_data['score'];
			
		 	$score_class['merged'] = "unknown";
		 	$score_class['mobile'] = "unknown";
		 	$score_class['desktop'] = "unknown";
		 
		 	if ($score_data['average_score'] >= 85) {
			 	$score_class['average'] = "good";
		 	} else if ($score_data['average_score'] >= 75) {
			 	$score_class['average'] = "warning";
		 	} else if ($score_data['average_score'] == 0) {
			 	$score_class['average'] = "unknown";
		 	} else {
			 	$score_class['average'] = "danger";
		 	} 

		 	if ($score_data['mobile_score'] >= 85) {
			 	$score_class['mobile'] = "good";
		 	} else if ($score_data['mobile_score'] >= 75) {
			 	$score_class['mobile'] = "warning";
		 	} else if ($score_data['mobile_score'] == 0) {
			 	$score_class['mobile'] = "unknown";
		 	} else {
			 	$score_class['mobile'] = "danger";
		 	} 
			
		 	if ($score_data['desktop_score'] >= 85) {
			 	$score_class['desktop'] = "good";
		 	} else if ($score_data['desktop_score'] >= 75) {
			 	$score_class['desktop'] = "warning";
		 	} else if ($score_data['desktop_score'] == 0) {
			 	$score_class['desktop'] = "unknown";
		 	} else {
			 	$score_class['desktop'] = "danger";
		 	} 			
			
			$html = "<div data-width='250' class='pegasaas-mega-menu-item pegasaas-mega-menu-scores'>
			<h5>".(!is_admin() ? "Site ": "")."PageSpeed Score</h5>
			<div class='pegasaas-mega-menu-item-row'>
			<div class='pegasaas-pagespeed-gauge-mobile pegasaas-score-{$score_class['mobile']}'>
			  <div class='pegasaas-pagespeed-gauge-mobile-bottom'></div>
			  <div class='pegasaas-pagespeed-gauge-mobile-bottom2'></div>
			  <div class='pegasaas-pagespeed-gauge-mobile-center'>{$score_data['mobile_score']}<div class='score-label'>Mobile</div></div>			
			</div>
			<div class='pegasaas-pagespeed-gauge pegasaas-score-{$score_class['average']}'>
			  <div class='pegasaas-pagespeed-gauge-bottom'></div>
			  <div class='pegasaas-pagespeed-gauge-center'>{$score_data['average_score']}<div class='score-label'>Average</div></div>
			</div>
			<div class='pegasaas-pagespeed-gauge-desktop pegasaas-score-{$score_class['desktop']}'>
			  <div class='pegasaas-pagespeed-gauge-desktop-bottom'></div>
			  <div class='pegasaas-pagespeed-gauge-desktop-bottom2'></div>
			  <div class='pegasaas-pagespeed-gauge-desktop-center'>{$score_data['desktop_score']}<div class='score-label'>Desktop</div></div>			
			</div>
			</div>
			</div>";	
		} else if ($section == "page-scores" && false) {
			$resource_id = PegasaasUtils::get_object_id();
			
			$page_score_details = $pegasaas->scanner->pegasaas_fetch_pagespeed_last_scan($resource_id);

			
			if ($page_score_details) { 
				$score_data['average_score'] = number_format(($page_score_details['score'] + $page_score_details['mobile_score']) / 2, 0, '.', '');
				$score_data['mobile_score'] = number_format($page_score_details['mobile_score'], 0, '.', '');
				$score_data['desktop_score'] = number_format($page_score_details['score'], 0, '.', '');
			} else {
				$score_data['average_score'] = "?";
				$score_data['mobile_score'] = "?";
				$score_data['desktop_score'] = "?";
			}
			
		 	$score_class['merged'] = "unknown";
		 	$score_class['mobile'] = "unknown";
		 	$score_class['desktop'] = "unknown";
		 
		 	if ($score_data['average_score'] >= 85) {
			 	$score_class['average'] = "good";
		 	} else if ($score_data['average_score'] >= 75) {
			 	$score_class['average'] = "warning";
		 	} else if ($score_data['average_score'] == "?") {
			 	$score_class['average'] = "unknown";
		 	} else {
			 	$score_class['average'] = "danger";
		 	} 

		 	if ($score_data['mobile_score'] >= 85) {
			 	$score_class['mobile'] = "good";
		 	} else if ($score_data['mobile_score'] >= 75) {
			 	$score_class['mobile'] = "warning";
		 	} else if ($score_data['average_score'] == "?") {
			 	$score_class['mobile'] = "unknown";
		 	} else {
			 	$score_class['mobile'] = "danger";
		 	} 
			
		 	if ($score_data['desktop_score'] >= 85) {
			 	$score_class['desktop'] = "good";
		 	} else if ($score_data['desktop_score'] >= 75) {
			 	$score_class['desktop'] = "warning";
		 	} else if ($score_data['average_score'] == "?") {
			 	$score_class['desktop'] = "unknown";
		 	} else {
			 	$score_class['desktop'] = "danger";
		 	} 			
			$url = $pegasaas->get_home_url().$resource_id;
			$html = "<div data-width='0' class='pegasaas-mega-menu-item pegasaas-mega-menu-scores pegasaas-mega-menu-scores-page-level'>
			<h5>Page PageSpeed Score</h5>
			<div class='pegasaas-mega-menu-item-row'>
			<div class='pegasaas-pagespeed-gauge-mobile pegasaas-score-{$score_class['mobile']}'>
			  <div class='pegasaas-pagespeed-gauge-mobile-bottom'></div>
			  <div class='pegasaas-pagespeed-gauge-mobile-bottom2'></div>
			  <div class='pegasaas-pagespeed-gauge-mobile-center'>{$score_data['mobile_score']}<div class='score-label'>Mobile</div></div>			
			</div>
			<div class='pegasaas-pagespeed-gauge pegasaas-score-{$score_class['average']}'>
			  <div class='pegasaas-pagespeed-gauge-bottom'></div>
			  <div class='pegasaas-pagespeed-gauge-center'>{$score_data['average_score']}<div class='score-label'>Average</div>
			  <a target='_blank' class='btn btn-scan' href='https://developers.google.com/speed/pagespeed/insights/?url=".(urlencode($url))."'>Scan</a></div>
			</div>
			<div class='pegasaas-pagespeed-gauge-desktop pegasaas-score-{$score_class['desktop']}'>
			  <div class='pegasaas-pagespeed-gauge-desktop-bottom'></div>
			  <div class='pegasaas-pagespeed-gauge-desktop-bottom2'></div>
			  <div class='pegasaas-pagespeed-gauge-desktop-center'>{$score_data['desktop_score']}<div class='score-label'>Desktop</div></div>			
			</div>
			</div>
			</div>";
		} else if ($section == "page-cache") {
			$page_cache_stats = $pegasaas->cache->get_local_cache_stats("html");
			
			$combined_css_cache_stats = $pegasaas->cache->get_local_cache_stats("combined.css");
			$deferred_js_cache_stats = $pegasaas->cache->get_local_cache_stats("deferred-js.js");
			$total_pages = $page_cache_stats['count'];
			$total_page_cache_size = $page_cache_stats['size'] + $combined_css_cache_stats['size']  + $deferred_js_cache_stats['size'];
			$total_page_cache_unit = "";
			
			list($total_page_cache_size, $total_page_cache_unit) = $this->get_human_readable_filesize_info($total_page_cache_size);
			
			
			$html = "<div data-width='280' class='pegasaas-mega-menu-item pegasaas-mega-menu-page-cache'>
					 <h5>".($pegasaas->is_standard() == "lite" ? __("Page Optimizations") : __("Page Cache"))."</h5>
					 <div class='pegasaas-mega-menu-item-row'>
					  
						  <div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-number'>{$total_pages}</div>
						     <div class='pegasaas-mega-menu-stat-description'>Pages</div>
						  </div>
					      <div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-number'>{$total_page_cache_size}<span>{$total_page_cache_unit}</span></div>
						    <div class='pegasaas-mega-menu-stat-description'>Size</div>
						  </div>
					</div>
					<div class='pegasaas-mega-menu-item-row'>
						<button type='button' class='btn btn-cache btn-50'>Delete <span class='status'></span></button>
						<button type='button' class='btn btn-reoptimize btn-50'>Re-Optimize <span class='status'></span></button>
					</div>
			</div>";
		} else if ($section == "page-cached-resources") {
		
			$combined_css_cache_stats = $pegasaas->cache->get_local_cache_stats("combined.css");
			$deferred_js_cache_stats = $pegasaas->cache->get_local_cache_stats("deferred-js.js");
			$total_pages = $combined_css_cache_stats['count'] + $deferred_js_cache_stats['count'];
			$total_page_cache_size = $combined_css_cache_stats['size']  + $deferred_js_cache_stats['size'];
			$total_page_cache_unit = "";
			list($total_page_cache_size, $total_page_cache_unit) = $this->get_human_readable_filesize_info($total_page_cache_size);
			
			
			$html = "<div data-width='210' class='pegasaas-mega-menu-item pegasaas-mega-menu-page-cache'>
					 <h5>".(!is_admin() ? "All Local ": "Page")." Cached Resources</h5>
					 <div class='pegasaas-mega-menu-item-row'>
					  
						  <div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-number'>{$total_pages}</div>
						     <div class='pegasaas-mega-menu-stat-description'>Files</div>
						  </div>
					      <div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-number'>{$total_page_cache_size}<span>{$total_page_cache_unit}</span></div>
						    <div class='pegasaas-mega-menu-stat-description'>Size</div>
						  </div>
					</div>
					<div class='pegasaas-mega-menu-item-row'>
						<button type='button' class='btn btn-cache'>Delete Cache <span class='status'></span></button>
					</div>
			</div>";

	} else if ($section == "current-page-cached-resources") {
			$resource_id = PegasaasUtils::get_object_id();
		
			$cache_path 			= PEGASAAS_CACHE_FOLDER_PATH."{$resource_id}";
			$html_filename 			= $cache_path."index.html";
			$extra = $html_filename;
			$html_temp_filename 			= $cache_path."index-temp.html";
			$extra .= "<br>".$html_temp_filename;
			$deferred_js_filename 	= $cache_path."deferred-js.js";
			$combined_css_filename 	= $cache_path."combined.css";
			
			$when_cached = 0;
			$total_size = 0;
			if (file_exists($html_filename)) {
				$extra .= "<br>html file exists";
			   	$total_size += filesize($html_filename);
				$when_cached = filemtime($html_filename);
			} 
			
			if (file_exists($html_temp_filename)) {
				$extra .= "<br>html temp file exists";
			   	$total_size += filesize($html_temp_filename);
				$when_cached = filemtime($html_temp_filename);
			}
			
			if (file_exists($deferred_js_filename)) {
			   	$total_size += filesize($deferred_js_filename);
				if ((PegasaasAccelerator::$settings['settings']['page_caching']['status'] == 0 && 
				    PegasaasAccelerator::$settings['settings']['page_caching']['defer_render_blocking_js'] > 0) || when_cached == 0) {
					$when_cached = filemtime($deferred_js_filename);
				}
			}
			if (file_exists($combined_css_filename)) {
			   	$total_size += filesize($combined_css_filename);
				if ((PegasaasAccelerator::$settings['settings']['page_caching']['status'] == 0 && 
				    PegasaasAccelerator::$settings['settings']['page_caching']['combine_css'] > 0) || $when_cached == 0) {
					$when_cached = filemtime($combined_css_filename);
				}
			}	
			
			
			$timezone_offset = date("Z") + number_format(get_option('gmt_offset'), 0, '.', '');
			//print "server_timezone_offset".$server_timezone_offset."\n<br>";
		//	print "wp timezone_offset".$timezone_offset;
			
			
			if ($when_cached == 0) {
				$when_cached = "NOT";
			} else {
				
				$when_cached_time = $when_cached + ($timezone_offset * 60 * 60);
				$when_cached = "<div class='pegasaas-stat-month-day'>".date("M d", $when_cached_time)."</div>";
			//	$when_cached .="<div class='pegasaas-stat-year'>".date("Y", $when_cached_time)."</div>";
				$when_cached .="<div class='pegasaas-stat-time'>".date("H:i", $when_cached_time)."</div>";
			}
			

			list($total_page_cache_size, $total_page_cache_unit) = $this->get_human_readable_filesize_info($total_size);
			$html = "<div data-width='220' class='pegasaas-mega-menu-item pegasaas-mega-menu-current-page-cache'>
					 <h5>This Page Cached Resources</h5>
					 <div class='pegasaas-mega-menu-item-row'>
					  
						  <div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-date'>{$when_cached}</div>
						     <div class='pegasaas-mega-menu-stat-description'>Cached</div>
						  </div>
					      <div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-number'>{$total_page_cache_size}<span>{$total_page_cache_unit}</span></div>
						    <div class='pegasaas-mega-menu-stat-description'>Size</div>
						  </div>						  
					</div>
					<div class='pegasaas-mega-menu-item-row'>
						<button type='button' class='btn btn-cache'>Delete Cache <span class='status' rel='{$resource_id}'></span></button>
					</div>
			</div>";	
			
		} else if ($section == "current-page-cache") {
			$resource_id = PegasaasUtils::get_object_id();
		
			$cache_path 			= PEGASAAS_CACHE_FOLDER_PATH."{$resource_id}";
			$html_filename 			= $cache_path."index.html";
			$temp_html_filename 			= $cache_path."index-temp.html";
			$deferred_js_filename 	= $cache_path."deferred-js.js";
			$combined_css_filename 	= $cache_path."combined.css";
			
			$when_cached = 0;
			$total_size = 0;
			if (file_exists($html_filename)) {
			   	$total_size += filesize($html_filename);
				$when_cached = filemtime($html_filename);
			} 
			
			if (file_exists($temp_html_filename)) {
			   	$total_size += filesize($temp_html_filename);
				$when_cached = filemtime($temp_html_filename);				
			}
			if (file_exists($deferred_js_filename)) {
			   	$total_size += filesize($deferred_js_filename);
				if (PegasaasAccelerator::$settings['settings']['page_caching']['status'] == 0 && 
				    PegasaasAccelerator::$settings['settings']['page_caching']['defer_render_blocking_js'] > 0) {
					$when_cached = filemtime($deferred_js_filename);
				}
			}
			if (file_exists($combined_css_filename)) {
			   	$total_size += filesize($combined_css_filename);
				if (PegasaasAccelerator::$settings['settings']['page_caching']['status'] == 0 && 
				    PegasaasAccelerator::$settings['settings']['page_caching']['combine_css'] > 0) {
					$when_cached = filemtime($combined_css_filename);
				}
			}	
			
			
			$timezone_offset = date("Z") + number_format(get_option('gmt_offset'), 0, '.', '');
			//print "server_timezone_offset".$server_timezone_offset."\n<br>";
		//	print "wp timezone_offset".$timezone_offset;
			
			
			if ($when_cached == 0) {
				$when_cached = "NOT";
			} else {
				
				$when_cached_time = $when_cached + ($timezone_offset * 60 * 60);
				$when_cached = "<div class='pegasaas-stat-month-day'>".date("M d", $when_cached_time)."</div>";
				$when_cached .="<div class='pegasaas-stat-time'>".date("H:i", $when_cached_time)."</div>";
			}
			
			

			list($total_page_cache_size, $total_page_cache_unit) = $this->get_human_readable_filesize_info($total_size);
			$html = "<div data-width='205' class='pegasaas-mega-menu-item pegasaas-mega-menu-current-page-cache'>
					 <h5>Current Page's Cache</h5>
					 <div class='pegasaas-mega-menu-item-row'>
					  
						  <div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-date'>{$when_cached}</div>
						     <div class='pegasaas-mega-menu-stat-description'>Cached</div>
						  </div>
					      <div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-number'>{$total_page_cache_size}<span>{$total_page_cache_unit}</span></div>
						    <div class='pegasaas-mega-menu-stat-description'>Size</div>
						  </div>						  
					</div>
					<div class='pegasaas-mega-menu-item-row'>
						<button type='button' class='btn btn-cache'>Delete Cache <span class='status' rel='{$resource_id}'></span></button>
					</div>
			</div>";			
		} else if ($section == "basic-image-cache") {
			$stats = $pegasaas->cache->get_basic_image_cache_stats();	
			
			if ($stats['percentage_of_use'] > 85) {
				$image_stat_class = "pegasaas-mega-menu-stat-container-danger";
			} else if ($stats['percentage_of_use'] > 75) {
				$image_stat_class = "pegasaas-mega-menu-stat-container-warning";
			} else {
				$image_stat_class = "";
			}
			list($total_image_cache_size, $total_image_cache_unit) = $this->get_human_readable_filesize_info($stats['cache_filesize']);

			
			$html = "<div data-width='255' class='pegasaas-mega-menu-item pegasaas-mega-menu-basic-image-cache'>
					 <h5>IMAGE CACHE</h5>
					 <div class='pegasaas-mega-menu-item-row'>
					  
						  <div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-number'>".($stats['optimized_images'] + $stats['unoptimized_images'])."</div>
						     <div class='pegasaas-mega-menu-stat-description'>Images</div>
						  </div>
					      <div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-number'>{$total_image_cache_size}<span>{$total_image_cache_unit}</span></div>
						    <div class='pegasaas-mega-menu-stat-description'>Size</div>
						  </div>";
			if (PegasaasAccelerator::$settings['settings']['basic_image_optimization']['images_per_month'] != 9999) {
				$html .= "<div class='pegasaas-mega-menu-stat-container {$image_stat_class}'>
						    <div class='pegasaas-mega-menu-stat-number'>{$stats['optimizations_so_far_this_month']}<span>/".PegasaasAccelerator::$settings['settings']['basic_image_optimization']['images_per_month']."</span></div>
						    <div class='pegasaas-mega-menu-stat-description'>Credits Used</div>
						  </div>";
			}
			$html .="</div>
					<div class='pegasaas-mega-menu-item-row'>
						<button type='button' class='btn btn-cache'>Delete Cache <span class='status'></span></button>";
			if (PegasaasAccelerator::$settings['settings']['basic_image_optimization']['images_per_month'] != 9999) {
				$html .= "<a target='_blank' href='https://pegasaas.com/upgrade/?utm_source=pegasaas-accelerator-wp-mega-menu&upgrade_key=".PegasaasAccelerator::$settings['api_key']."-".PegasaasAccelerator::$settings['installation_id']."&site=".$pegasaas->utils->get_http_host()."&current=".PegasaasAccelerator::$settings['subscription']."' class='btn btn-upgrade'>Upgrade</a>";
			}
			$html .= "</div>
			</div>";			

			
		} else if ($section == "local-image-cache") {
			$stats = $pegasaas->cache->get_basic_image_cache_stats();	
			
			if ($stats['percentage_of_use'] > 85) {
				$image_stat_class = "pegasaas-mega-menu-stat-container-danger";
			} else if ($stats['percentage_of_use'] > 75) {
				$image_stat_class = "pegasaas-mega-menu-stat-container-warning";
			} else {
				$image_stat_class = "";
			}
			list($total_image_cache_size, $total_image_cache_unit) = $this->get_human_readable_filesize_info($stats['cache_filesize']);

			
			$html = "<div data-width='255' class='pegasaas-mega-menu-item pegasaas-mega-menu-basic-image-cache'>
					 <h5>IMAGE CACHE</h5>
					 <div class='pegasaas-mega-menu-item-row'>
					  
						  <div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-number'>".($stats['optimized_images'] + $stats['unoptimized_images'])."</div>
						     <div class='pegasaas-mega-menu-stat-description'>Images</div>
						  </div>
					      <div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-number'>{$total_image_cache_size}<span>{$total_image_cache_unit}</span></div>
						    <div class='pegasaas-mega-menu-stat-description'>Size</div>
						  </div>";
			
			$html .="</div>
					<div class='pegasaas-mega-menu-item-row'>
						<button type='button' class='btn btn-cache'>Delete Cache <span class='status'></span></button>";
			
			$html .= "</div>
			</div>";				
			
		} else if ($section == "minified-js-cache") {
			$js_cache_stats = $pegasaas->cache->get_local_cache_stats("js");
			$total_files = $js_cache_stats['count'];
			$total_js_cache_size = $js_cache_stats['size'];
			
			list($total_js_cache_size, $total_js_cache_unit) = $this->get_human_readable_filesize_info($total_js_cache_size);

			
			$html = "<div data-width='205' class='pegasaas-mega-menu-item pegasaas-mega-menu-minified-js-cache'>
					 <h5>Minified Javascript</h5>
					 <div class='pegasaas-mega-menu-item-row'>
					  
						  <div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-number'>{$total_files}</div>
						     <div class='pegasaas-mega-menu-stat-description'>JS Files</div>
						  </div>
					      <div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-number'>{$total_js_cache_size}<span>{$total_js_cache_unit}</span></div>
						    <div class='pegasaas-mega-menu-stat-description'>Size</div>
						  </div>
					</div>
					<div class='pegasaas-mega-menu-item-row'>
						<button type='button' class='btn btn-cache'>Delete Cache <span class='status'></span></button>
					</div>
			</div>";		
		
		} else if ($section == "deferred-js-cache") {
			$js_cache_stats = $pegasaas->cache->get_local_cache_stats("deferred-js.js");
			$total_files = $js_cache_stats['count'];
			$total_js_cache_size = $js_cache_stats['size'];
			
			list($total_js_cache_size, $total_js_cache_unit) = $this->get_human_readable_filesize_info($total_js_cache_size);

			
			$html = "<div data-width='205' class='pegasaas-mega-menu-item pegasaas-mega-menu-deferred-js-cache'>
					 <h5>Deferred Javascript</h5>
					 <div class='pegasaas-mega-menu-item-row'>
					  
						  <div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-number'>{$total_files}</div>
						     <div class='pegasaas-mega-menu-stat-description'>JS Files</div>
						  </div>
					      <div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-number'>{$total_js_cache_size}<span>{$total_js_cache_unit}</span></div>
						    <div class='pegasaas-mega-menu-stat-description'>Size</div>
						  </div>
					</div>
					<div class='pegasaas-mega-menu-item-row'>
						<button type='button' class='btn btn-cache'>Delete Cache <span class='status'></span></button>
					</div>
			</div>";		
		
			
		} else if ($section == "combined-css-cache") {
			$css_cache_stats = $pegasaas->cache->get_local_cache_stats("combined.css");
			$total_files = $css_cache_stats['count'];
			$total_css_cache_size = $css_cache_stats['size'];
			
			list($total_css_cache_size, $total_css_cache_unit) = $this->get_human_readable_filesize_info($total_css_cache_size);

			
			$html = "<div data-width='205' class='pegasaas-mega-menu-item pegasaas-mega-menu-deferred-js-cache'>
					 <h5>Combined CSS</h5>
					 <div class='pegasaas-mega-menu-item-row'>
					  
						  <div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-number'>{$total_files}</div>
						     <div class='pegasaas-mega-menu-stat-description'>CSS Files</div>
						  </div>
					      <div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-number'>{$total_css_cache_size}<span>{$total_css_cache_unit}</span></div>
						    <div class='pegasaas-mega-menu-stat-description'>Size</div>
						  </div>
					</div>
					<div class='pegasaas-mega-menu-item-row'>
						<button type='button' class='btn btn-cache'>Delete Cache <span class='status'></span></button>
					</div>
			</div>";		
		
						
		} else if ($section == "minified-css-cache") {
			$css_cache_stats = $pegasaas->cache->get_local_cache_stats("css");
			$total_files = $css_cache_stats['count'];
			$total_css_cache_size = $css_cache_stats['size'];
			
			list($total_css_cache_size, $total_css_cache_unit) = $this->get_human_readable_filesize_info($total_css_cache_size);

			
			$html = "<div data-width='200' class='pegasaas-mega-menu-item pegasaas-mega-menu-minified-css-cache'>
					 <h5>Minified CSS</h5>
					 <div class='pegasaas-mega-menu-item-row'>
					  
						  <div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-number'>{$total_files}</div>
						     <div class='pegasaas-mega-menu-stat-description'>CSS Files</div>
						  </div>
					      <div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-number'>{$total_css_cache_size}<span>{$total_css_cache_unit}</span></div>
						    <div class='pegasaas-mega-menu-stat-description'>Size</div>
						  </div>
					</div>
					<div class='pegasaas-mega-menu-item-row'>
						<button type='button' class='btn btn-cache'>Delete Cache <span class='status'></span></button>
					</div>
			</div>";		
		
		} else if ($section == "resource-cdn") {
		
			
			
			$html = "<div data-width='275' class='pegasaas-mega-menu-item pegasaas-mega-menu-resource-cdn'>
					 <h5>Content Delivery Network</h5>
					 <div class='pegasaas-mega-menu-item-row'>
					 	<div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-text'>Images</div>
						    <button type='button' class='btn btn-cache btn-purge-cdn-images'>Purge <span class='status'></span></button>
						</div>
					 	<div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-text'>CSS</div>
						    <button type='button' class='btn btn-cache btn-purge-cdn-css'>Purge <span class='status'></span></button>
						</div>
					 	<div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-text'>JS</div>
						    <button type='button' class='btn btn-cache btn-purge-cdn-js'>Purge <span class='status'></span></button>
						</div>								
					 </div>
								 
					 <div class='pegasaas-mega-menu-item-row'>
						<button type='button' class='btn btn-cache btn-purge-cdn-all'>Purge All <span class='status'></span></button>
					 </div>
			</div>";
		} else if ($section == "cloudflare-cdn") {
		
			
			
			$html = "<div data-width='200' class='pegasaas-mega-menu-item pegasaas-mega-menu-cloudflare'>
					 <h5>Cloudflare</h5>
					 <div class='pegasaas-mega-menu-item-row'>
					 	<div class='pegasaas-mega-menu-stat-container'>
						    <div class='pegasaas-mega-menu-stat-text'>JS/CSS/Images</div>
						   	<button type='button' class='btn btn-cache btn-purge-cloudflare'>Purge Cloudflare <span class='status'></span></button>

						</div>
										
					 </div>
								 
					 <div class='pegasaas-mega-menu-item-row'>
					 </div>
			</div>";
		}
		return $html;
	}
	
	
	function pegasaas_dismiss_review_prompt() {
		
		update_option("pegasaas_accelerator_dismiss_review_prompt", 1);
		print "1";
		wp_die();
	}
	
	function pegasaas_dismiss_upgrade_box() {
		
		update_option("pegasaas_accelerator_dismiss_upgrade_box", 1);
	}
	
 	function add_toolbar_items($admin_bar) {
		global $pegasaas;
		$debug = false;
		if ($this->toolbar_scores_restricted()) {
			//print "toolbar scores restricted";
			 return;
		}
		 if (current_user_can("administrator")) {
			if (PegasaasAccelerator::$settings['settings']['white_label']['status'] == 1) {
				
				$pegasaas_url = get_admin_url( null, 'admin.php?page=pa-web-perf' );
			 	$title = '<span class="ab-label">' . __( PegasaasAccelerator::$settings['settings']['white_label']['toolbar_label'], 'pegasaas-accelerator' ) . '</span>';

			} else {
				$pegasaas_url = get_admin_url( null, 'admin.php?page=pegasaas-accelerator' );

		 		$svg_icon 	= PEGASAAS_ACCELERATOR_URL. 'assets/images/icon.png';
			 $title = '<div id="pegasaas-ab-icon" class="ab-item pegasaas-logo svg"></div><span class="ab-label">' . __( 'Accelerator', 'pegasaas-accelerator' ) . '</span>';
		}
			 
		$admin_bar->add_menu( array(
			'id'    => 'pegasaas-menu',
			'title' => $title,
			'href'  => $pegasaas_url
		) );	 	
			 
		$menu_html = "<div class='pegasaas-mega-menu-item-row'>";	
			 
		 if ($this->toolbar_scores_restricted()) {
			//print "toolbar scores restricted";
			 return;
		 } else if (false) {
			// PageSpeed Scores
			if (!is_admin()) {
				$menu_html .= "<div class='pegasaas-mega-menu-score-toggler-container'>";
				$menu_html .= "<div class='pegasaas-mega-menu-score-toggler-item' data-toggler='1'>";
				$menu_html .= $this->generate_mega_menu_html("page-scores");
				$menu_html .= "</div>";
				$menu_html .= "<div class='pegasaas-mega-menu-score-toggler-item hidden' data-toggler='2'>";
			}
			 
			$menu_html .= $this->generate_mega_menu_html("scores");
			if (!is_admin()) {	
				$menu_html .= "</div>";
				$menu_html .= "<div class='pegasaas-mega-menu-score-toggler-buttons'>";
				$menu_html .= "<button data-toggler-target='1' class='btn btn-toggler btn-toggler-active'>Page Score</button>";
				$menu_html .= "<button data-toggler-target='2' class='btn btn-toggler'>Site Score</button>";
				$menu_html .= "</div>";
				$menu_html .= "</div>";	 
			}			 
		 }
		 
		 
	
	

	 

	$menu_html .= "<div class='pegasaas-mega-menu-item-row'>";
	
			 
			 
	// Page Cache
	if (PegasaasAccelerator::$settings['settings']['page_caching']['status'] > 0) { 
		if ( ! is_admin() && current_user_can("administrator")) {
			
			$menu_html .= $this->generate_mega_menu_html("current-page-cache");
		
		}		
		
		
		$menu_html .= $this->generate_mega_menu_html("page-cache");
	} else {
		/*
		if (PegasaasAccelerator::$settings['settings']['defer_render_blocking_js']['status'] > 0) { 
			$menu_html .= $this->generate_mega_menu_html("deferred-js-cache");
	
		}
		if (PegasaasAccelerator::$settings['settings']['combine_css']['status'] > 0) { 
			$menu_html .= $this->generate_mega_menu_html("combined-css-cache");
	
		}		
		*/
		if ( ! is_admin() && current_user_can("administrator")) {
			
			$menu_html .= $this->generate_mega_menu_html("current-page-cached-resources");
		
		}
		
		if (PegasaasAccelerator::$settings['settings']['defer_render_blocking_js']['status'] > 0 || PegasaasAccelerator::$settings['settings']['combine_css']['status'] > 0) { 
			$menu_html .= $this->generate_mega_menu_html("page-cached-resources");
	
		}
	}
			 

			 
	// Minified JS
	if (PegasaasAccelerator::$settings['settings']['minify_js']['status'] > 0 && PegasaasAccelerator::$settings['settings']['cdn']['status'] == 0) { 
		$menu_html .= $this->generate_mega_menu_html("minified-js-cache");
	}		

	// Minified CSS
	if (PegasaasAccelerator::$settings['settings']['minify_css']['status'] > 0 && PegasaasAccelerator::$settings['settings']['cdn']['status'] == 0) {			 
		$menu_html .= $this->generate_mega_menu_html("minified-css-cache");			 
	}
		

	// Image Cache
	if (PegasaasAccelerator::$settings['settings']['basic_image_optimization']['status'] > 0) { 		 
		$menu_html .= $this->generate_mega_menu_html("basic-image-cache");
	}
	// Image Cache
	if (PegasaasAccelerator::$settings['settings']['image_optimization']['status'] > 0 &&  PegasaasAccelerator::$settings['settings']['cdn']['status'] == 0) { 		 
		$menu_html .= $this->generate_mega_menu_html("local-image-cache");
	}
			 
			 

	if (PegasaasAccelerator::$settings['settings']['cdn']['status'] > 0) { 
		$menu_html .= $this->generate_mega_menu_html("resource-cdn");
	}	
			 
	if (($pegasaas->cache->cloudflare_exists() || PegasaasAccelerator::$settings['settings']['cloudflare']['status'] > 0) && $pegasaas->cache->cloudflare_credentials_valid()) {
			$menu_html .= $this->generate_mega_menu_html("cloudflare-cdn");
	}
			 
	$menu_html .= "</div>";
	$menu_html .= "</div>";
			 
			 
	$admin_bar->add_node( array(
		'id'     => 'pegasaas-mega-menu',
		'title'  => $menu_html,
		'parent' => 'pegasaas-menu'
	));

	

	
	
		 }

	}	
	
	
	function hide_diagnostics() {
		global $pegasaas;
		setcookie("view_diagnostics", false);	
		$pegasaas->view_diagnostics = false;
	}
	
	
}
?>