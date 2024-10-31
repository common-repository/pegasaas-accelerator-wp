<div id="pegasaas-dashboard-container">
<?php
$update_history = get_option("pegasaas_plugin_update_history");


// logic
include("admin-control-panel/interface-handling.php");

// popovers
include("admin-control-panel/popovers/welcome-tour.php");
include("admin-control-panel/popovers/confirm-update-clear-cache.php");
include("admin-control-panel/popovers/confirm-update-clear-image-cache.php");
include("admin-control-panel/popovers/development-mode.php");
include("admin-control-panel/popovers/live-mode.php");
include("admin-control-panel/popovers/diagnostic-mode.php");
include("admin-control-panel/popovers/time-is-up.php");
include("admin-control-panel/popovers/novice-mode-lock.php");
include("admin-control-panel/popovers/upgrade-for-premium-feature.php"); 

PegasaasInterface::trial_bar();
PegasaasInterface::https_http_inconsistency_warning();
PegasaasInterface::write_permissions_warning();	

	if ($_GET['test'] == "rotate-log") {
		$pegasaas->util->rotate();
	}
	
		if ($_GET['debug'] == "wpml") {
	$domains = PegasaasUtils::get_wpml_domains(); 
	print "<pre>";
	var_dump($domains);
			print "\nfy";
			
			$info = wpml_get_setting( 'language_negotiation_type');
		//	$info = wpml_get_setting_filter( false, 'language_negotiation_type' );
			var_dump($info);
	print "</pre>";
			
			
	}
?>
<div class='pegasaas-header <?php if (PegasaasAccelerator::$settings['status'] < 1) { ?>pegasaas-disabled<?php } ?>'>
	 
	  
	 <?php if (PegasaasAccelerator::$settings['settings']['white_label']['status'] == 1 && isset(PegasaasAccelerator::$settings['settings']['white_label']['dashboard_logo']) && PegasaasAccelerator::$settings['settings']['white_label']['dashboard_logo'] != "") { 
	    if ($pegasaas->utils->is_valid_url(PegasaasAccelerator::$settings['settings']['white_label']['dashboard_logo'])) { ?>
	      <img id="plugin-logo" src='<?php echo PegasaasAccelerator::$settings['settings']['white_label']['dashboard_logo']; ?>' style='margin-left: 22px; padding-left: 75px;'/>
	 <?php } else { ?>
	  		<h1><?php echo PegasaasAccelerator::$settings['settings']['white_label']['dashboard_logo']; ?></h1>
		<?php } ?>
	<?php } else {
	  if (PegasaasAccelerator::$settings['settings']['display_mode']['status'] == 1 || PegasaasAccelerator::$settings['settings']['display_mode']['status'] == "") {
		  $logo_modifier = "-light";
	  } else if (PegasaasAccelerator::$settings['settings']['display_mode']['status'] == 2 ) {
		  $logo_modifier = "-dark";
	  } else {
		  $logo_modifier = "-light";
	  }
	  ?>
    <img id="plugin-logo" src='<?php echo PEGASAAS_ACCELERATOR_URL; ?>assets/images/pegasaas-accelerator-horizontal-logo<?php echo $logo_modifier; ?><?php echo (false && $pegasaas->is_pro_edition() ? "-dev" :  ""); ?>.png' style='margin-left: 00px;'/>
	<!-- if the system is initialized -->
		<?php if (PegasaasAccelerator::$settings) { ?>
			<?php if (PegasaasAccelerator::$settings['status'] == 1) { ?>
	  	<div id="pegasaas-accelerator-key-wrapper" style='display: inline-block;' class='hidden'>
	   		<form id="pegasaas-status-form" method="post" action="admin.php?page=pegasaas-accelerator" >
				<input type='hidden' name='c' value='Disable' /></form>
		
			<div id='protected-status-container-2'  
				 	class='protected'
				 	 data-toggle='popover' 
					 data-html='true' 
					 data-placement='bottom' 
					 data-trigger='click hover'
					 title='What is this?' 
					 data-content='This toggle switches Pegasaas Accelerator WP beween LIVE and DIAGNOSTIC mode.<br><br>If you are experiencing display issues with your website, enable DIAGNOSTIC mode and then contact the support team so that we may investigate.'
				 
				 >
				<input style='display:none;' onchange='jQuery("#pegasaas-status-form").submit()' type='checkbox' class='pegasaas-status-switch js-switch js-switch-small' checked />
			</div>
	  	</div>
	  			
	  				<div class='upgrade-placeholder'>
					<?php if ($pegasaas->is_free()) { ?><div class='upgrade-link'><a href='https://pegasaas.com/upgrade/?utm_source=pegasaas-accelerator-lite-logo&upgrade_key=<?php echo PegasaasAccelerator::$settings['api_key']; ?>-<?php echo PegasaasAccelerator::$settings['installation_id']; ?>&site=<?php echo $pegasaas->utils->get_http_host(); ?>&current=<?php echo PegasaasAccelerator::$settings['subscription']; ?>' target="_blank" title='Upgrade'>Upgrade</a></div><?php } else { print "&nbsp;"; } ?>
	  				</div>
				
	  		<?php } else if (PegasaasAccelerator::$settings['api_key'] != "") { ?>
	 			<?php if ($pegasaas->is_standard() || true) { ?>
	  				<div class='upgrade-placeholder'>
					<?php if ($pegasaas->is_free()) { ?><div class='upgrade-link'><a href='https://pegasaas.com/upgrade/?utm_source=pegasaas-accelerator-lite-logo&upgrade_key=<?php echo PegasaasAccelerator::$settings['api_key']; ?>-<?php echo PegasaasAccelerator::$settings['installation_id']; ?>&site=<?php echo $pegasaas->utils->get_http_host(); ?>&current=<?php echo PegasaasAccelerator::$settings['subscription']; ?>' target="_blank" title='Upgrade'>Upgrade</a></div><?php } else { print "&nbsp;"; } ?>
	  				</div>
				
	  			<?php } ?>
	  
	  			<div id="pegasaas-accelerator-key-wrapper" 
					 style='display: inline-block;'
					 class='hidden'
					 data-toggle='popover' 
					 data-html='true' 
					 data-placement='bottom' 
					 data-trigger='click hover'
					 title='What is this?' 
					 data-content='This toggle switches Pegasaas Accelerator WP beween LIVE and DIAGNOSTIC mode.<br><br>If you are experiencing display issues with your website, enable DIAGNOSTIC mode and then contact the support team so that we may investigate.'
					 >
					
 					<form id="pegasaas-status-form" method="post" action="admin.php?page=pegasaas-accelerator"><input type='hidden' name='c' value='Enable' /></form>		
		  				<div id='protected-status-container-2' class='pegasaas-disabled'>
												
										  

	  						<input 
								   onchange='jQuery("#pegasaas-status-form").submit()' 
								   type='checkbox' 
								   class='pegasaas-status-switch js-switch js-switch-small' />   
			  				<div class='disabled-label'>DIAGNOSTIC MODE</div>
						</div>
	  				</div>
					
	  		<?php } ?>
		<?php } ?>
	<?php } ?>
	  <?php if (isset(PegasaasAccelerator::$settings['status']) && PegasaasAccelerator::$settings['status'] >= 0) { ?>
		<div class="system-mode-switcher" id="system-mode">
			
		  <p class="fieldset">
				<input type="radio" name="system_mode" value="live" id="live" <?php if ($pegasaas->in_live_mode()) { print "checked"; } ?>>
			  	<label for="live" rel='tooltip-html' title='<div class="live-mode-tooltip-label">LIVE Mode</div>Pegasaas Accelerator is fully operational and on auto-pilot in this mode.'>LIVE</label>

				<input type="radio" name="system_mode" value="development" id="development" <?php if ($pegasaas->in_development_mode()) { print "checked"; } ?>>
			    <label for="development" rel='tooltip-html' title='<div class="development-mode-tooltip-label">Staging Mode</div>Use this if you are actively working on your site, or want to test the results of different Pegasaas Accelerator settings.'>Staging</label>

				<input type="radio" name="system_mode" value="diagnostic" id="diagnostic" <?php if ($pegasaas->in_diagnostic_mode()) { print "checked"; } ?>>
				<label for="diagnostic" rel='tooltip-html' title='<div class="diagnostic-mode-tooltip-label">Diagnostic Mode</div>Use this if you need to troubleshoot an issue with support.'>Diagnostic</label>
			  
				<span class="mode-switch"></span>
			</p>
		</div> 
	  <?php } ?>
</div>


<?php 

	
	
if ($pegasaas->is_standard() && $pegasaas->utils->does_plugin_exists_and_active("pegasaas-accelerator")) {
	include "admin-control-panel/prompts/disable-standard.php";


} else if (version_compare(PHP_VERSION, PEGASAAS_ACCELERATOR_REQUIRED_PHP_VERSION, '<')) {
	include "admin-control-panel/prompts/php-version.php";


} else { 
	
	
	$pegasaas->utils->console_log("Before Get Scores");

	$score_data 		= $pegasaas->scanner->get_site_score();
	$pegasaas->utils->console_log("After Get Scores!");
	$benchmark_data 	= $pegasaas->scanner->get_site_benchmark_score(); 
		$pegasaas->utils->console_log("After Get Benchmark Scores!");

//print "<Pre>";
	//var_dump($score_data);
	
	//	var_dump($benchmark_data);
	//print "</pre>";
	if ($score_data['scanned_urls'] == 0 || $benchmark_data['scanned_urls'] == 0) {
		$prepping = true;
	} else {
		$prepping = false;
	}
	
	if (PegasaasAccelerator::$settings['status'] == 0) {
		$prepping = false;
	}
	
	$skip_to_support = false;
	if ($_GET['skip_prep'] == 2) {
		$prepping = 1;
	} else if ($_GET['skip_prep'] == 1) { 
		$prepping = 0;
	} else if ($_GET['skip'] == "to-support") {
		$prepping = 0;
		$skip_to_support = true;
	}
	
 	include ("admin-control-panel/dashboard.php"); 

    
} // end of php version detect ?>

</div><!-- pegasaas-dashboard-container -->


  

<script>
var PEGASAAS_ACCELERATOR_URL 		= "<?php echo PEGASAAS_ACCELERATOR_URL; ?>";
var total_requests_this_page_load 	= 0;
var start_interval 					= <?php echo $pegasaas->interface->get_interface_start_interval(); ?>;
var current_interval 				= <?php echo $pegasaas->interface->get_interface_refresh_interval(); ?>;
var current_short_interval 			= 15000;
var throttle_rate 					= "<?php echo PegasaasAccelerator::$settings['settings']['response_rate']['status']; ?>";

	
	//jQuery(window).load(function() { resize_subsystem_feature_description(); });
	//jQuery(window).resize(function() { resize_subsystem_feature_description(); });
//	jQuery("#settings-nav-button").on("shown.bs.tab", function() { resize_subsystem_feature_description(); });

	
function manage_interval_time() {
	<?php if (isset($_GET['console-log'])) { ?>
	console.log("Current interval: " + current_interval);
	console.log("Current short interval: " + current_short_interval);
	console.log("Current requests: " + total_requests_this_page_load);
	console.log("Throttling rate: " + throttle_rate);
	<?php } ?>
	
	if (total_requests_this_page_load == 10) {
		current_interval = current_interval * 2;
	} else if (total_requests_this_page_load == 25) {
		current_interval = current_interval * 2;
	} else if (total_requests_this_page_load >= 50) {
		<?php if (isset($_GET['console-log'])) { ?>
		console.log("Shutting Down Interface Refreshing");
		<?php } ?>
		clearInterval(accelerated_pagespeed_interval_timer);
		clearInterval(benchmark_interval_timer);
		clearInterval(background_interval_timer);
	}
		
	
}
	
function prompt_user() {
	jQuery("#time_is_up").modal("show");
}
	
	

jQuery("#pegasaas-api-key-form").bind("submit", function(e) {
	
	e.preventDefault();
	if (jQuery("#pegasaas-api-key").val() == "") { 
	  alert("Please enter a valid API Key.  To get your API Key, log in to your account at pegasaas.com");
	  return false;
	}
	 jQuery(this).find("input[type=submit]").attr("disabled", "disabled");
	manage_interval_time();
	jQuery.post({url: ajaxurl, 
				 type: "POST",
				 dataType: "json",
				 data: { 'action': 'pegasaas_api_key_check', 'api_key': jQuery("#pegasaas-api-key").val()},
				 
				 success: 				function(data) {
					
					 total_requests_this_page_load++;
				//	console.log(data);
					if (data['status'] == "1") {
					  jQuery("#api-key-status").html('<span class="dashicons dashicons-yes"></span>');
					  document.location.href = document.location.href;
					} else if (data['status'] == "-2") { 
					  jQuery("#api-key-status").html('<span class="dashicons dashicons-welcome-comments"></span>');
					  alert(data['reason']);
					} else if (data['status'] == "0") { 
					  jQuery("#api-key-status").html('<span class="dashicons dashicons-yes"></span>');
						document.location.href = document.location.href;
					} else {
						  jQuery("#api-key-status").html('<span class="dashicons dashicons-no"></span>');
						  alert(data['reason']);
					}
				},
				 error: function(jqXHR, textStatus, errorThrown) {
				  alert("Error, status = " + textStatus + ", " +
              "error thrown: " + errorThrown
        );
				 }
				 });
	
});
function init_page_prioritization_bindings() {
	
	jQuery(".fa-page-prioritization").on("click", function() {
		if (jQuery(this).hasClass("prioritization-enabled")) {
			jQuery(this).removeClass("prioritization-enabled");
			//jQuery(this).removeClass("fa-star");
			var resource_id = jQuery(this).parents("tr").find("input[name='pid']").val();
var $this = this;
			//alert(resource_id);
			jQuery.post(ajaxurl,
					{ 'action': 'pegasaas_disable_prioritization_for_page', 'api_key': jQuery("#pegasaas-api-key").val(), 
					 'resource_id': resource_id},
			
					function(data) {
						if (data['status'] == 1) {
							jQuery($this).removeClass("prioritization-enabled");
							//alert("Page Prioritization Disabled");
						} else {
							alert("Page Prioritization Not Disabled -- problem");
						}
					}, 'json');

		} else {
			//jQuery(this).parents("table").find(".default-subscription-toggler.fa-star").addClass("fa-star-o");
			//jQuery(this).parents("table").find(".default-subscription-toggler.fa-star").removeClass("fa-star");

			
			//jQuery(this).removeClass("fa-star-o");
			var resource_id = jQuery(this).parents("tr").find("input[name='pid']").val();
			var $this = this;
			
			jQuery.post(ajaxurl,
					{ 'action': 'pegasaas_enable_prioritization_for_page', 'api_key': jQuery("#pegasaas-api-key").val(), 
					 'resource_id': resource_id},					function(data) {
				
						if (data['status'] == 1) {
							jQuery($this).addClass("prioritization-enabled");
						
						} else {
							alert("You have reached your limit of page prioritizations.  Please un-prioritize another page before marking this page a priority for optimizations.")
						}
					}, 'json');
			//jQuery(this).parents("table").find(".default-subscription-toggler").removeClass("fa-star-o");
		}
	});
	jQuery('.fa-page-prioritization').tooltip({placement: 'right', title: function() {
		if (jQuery(this).hasClass("prioritization-enabled")) {
			return "Unselect this icon to remove this item from the list of prioritized pages that are to be optimized ahead of others during a site-wide cache purge.";
		} else {
			return "Select this icon to add this item to the list of prioritized pages that are to be optimized ahead of others during a site-wide cache purge";
		}
	}});
	
	jQuery(".when-optimized-icon").tooltip({placement: 'top', html: true});
}
	
function init_bindings() {
	
	
	
	if (interface_loaded) {
		jQuery("[rel='tooltip']").tooltip({container: 'body'});
		init_page_prioritization_bindings();
		//console.log("interface loaded");
	} else {
		//console.log("interface not loaded");
	}
	
	
	jQuery("a.scan-with-gtmetrix").bind("click", function(e) {
		e.preventDefault();
		jQuery(this).parents("tr").find("form.gtmetrix-form").submit();
		
	});
	
	jQuery("a.initiate-rescan-pagespeed").bind("click", function(e) {
		e.preventDefault();
		jQuery(this).parents("tr").find("form.rescan-pagespeed").submit();
	});
	
	

	
	jQuery("a.initiate-rescan-pagespeed-benchmark").bind("click", function(e) {
		e.preventDefault();
		
		jQuery(this).parents("tr").find(".original-score-container .progress-bar").addClass("active");
		jQuery(this).parents("tr").find(".original-score-container .progress-bar").removeClass("progress-bar-success");
		jQuery(this).parents("tr").find(".original-score-container .progress-bar").removeClass("progress-bar-warning");
		jQuery(this).parents("tr").find(".original-score-container .progress-bar").removeClass("progress-bar-danger");
		jQuery(this).parents("tr").find(".original-score-container .progress-bar").html("Scan Queued...");
		jQuery(this).parents("tr").find(".original-score-container .progress-bar").css("width", "100%");
		
		jQuery(this).parents("tr").find(".original-score-container").addClass("benchmark-scan-in-progress");
		
		var resource_id = jQuery(this).parents("tr").find("input[name='pid']").val();
		total_requests_this_page_load = 0; // reset the clock
		//console.log("resource id = "+ resource_id);
		manage_interval_time();
		
		jQuery.post(ajaxurl,
					{ 'action': 'pegasaas_request_pagespeed_benchmark_score', 'api_key': jQuery("#pegasaas-api-key").val(), 
					 'resource_id': resource_id},
					function(data) {
						total_requests_this_page_load++;
						
						
						pegasaas_accelerator_increment_pending_benchmark_scans_chart();

					}, "json");
	
		clearInterval(benchmark_interval_timer);
		benchmark_interval_timer =  setInterval(function() { pegasaas_accelerator_check_benchmark_scores(); }, current_interval);
		//alert(benchmark_interval_timer);
		
	});
	
	
	jQuery(".build-page-cache").click(function(e) {
		e.preventDefault();
		build_page_cache(this);
	});
	
	function build_page_cache($this) {
		
		var resource_id 		= jQuery($this).attr("data-resource-id");

		jQuery($this).parents(".btn-group").find(".material-local-optimization-complete").addClass("hidden");
		jQuery($this).parents(".btn-group").find(".material-optimized-on-next-visit").addClass("hidden");
		jQuery($this).parents(".btn-group").find(".material-local-cache-exists").addClass("hidden");
		jQuery($this).parents(".btn-group").find(".material-check-pending").addClass("hidden");
		jQuery($this).parents(".btn-group").find(".material-problem").addClass("hidden");
		jQuery($this).parents(".btn-group").find(".svg-tail-spin").removeClass("hidden");
		
	
		total_requests_this_page_load = 0; // reset the clock
		manage_interval_time();
		
		jQuery.post(ajaxurl,
					{ 'action': 'pegasaas_build_page_cache', 
					  'api_key': jQuery("#pegasaas-api-key").val(), 
					  'priority': '1', 
					  'resource_id': resource_id},
					function(data) {
						if (data['status'] == 1) {
							
							// remove spinner
							jQuery($this).parents(".btn-group").find(".svg-tail-spin").addClass("hidden");
							
							if (jQuery($this).parents(".btn-group").hasClass("cache-standard-edition")) {
								jQuery($this).parents(".btn-group").find(".material-local-optimization-complete").removeClass("hidden");
								jQuery($this).parents("ul").find("li").addClass("hidden");
								jQuery($this).parents("ul").find("li[data-state='cache-exists']").removeClass("hidden");
							} else {
								jQuery($this).parents(".btn-group").find(".material-local-optimization-complete").removeClass("hidden");
								jQuery($this).parents("ul").find("li").addClass("hidden");
								jQuery($this).parents("ul").find("li[data-state='cache-exists']").removeClass("hidden");	
							}
							
							if (background_interval_timer == null) {
								clearInterval(background_interval_timer);
								background_interval_timer =  setInterval(function() { pegasaas_accelerator_check_optimization_status(); }, current_short_interval);
							}
						} else {
							jQuery($this).parents(".btn-group").find(".svg-tail-spin").addClass("hidden");
							jQuery($this).parents(".btn-group").find(".material-problem").removeClass("hidden");
							jQuery($this).parents("ul").find("li").addClass("hidden");
							jQuery($this).parents("ul").find("li[data-state='cache-missing']").removeClass("hidden");
						}
						
					}, "json");
		
	}

	jQuery(".reoptimize-without-cache-clear").click(function(e) {
		e.preventDefault();
		
		re_optimize_without_clearing_cache(this);
		
	
		
		
	});	
	
	
	jQuery(".purge-page-cache-and-reoptimize").click(function(e) {
		e.preventDefault();
		
		purge_page_cache(this);
		
		//jQuery(this).parents(".btn-group").find(".material-optimized-on-next-visit").addClass("hidden");
	//	jQuery(this).parents(".btn-group").find(".material-check-pending").addClass("hidden");
			
		
		
	});
	
	jQuery(".purge-page-cache").click(function(e) {
		e.preventDefault();
		
		purge_page_cache(this);
	});
	
	function re_optimize_without_clearing_cache($this) {
									  
		var resource_id 		= jQuery($this).attr("data-resource-id");
		var is_reoptimize 		= jQuery($this).attr("data-reoptimize") == 1;

		
		var auto_crawl_enabled  = jQuery($this).parents(".btn-group").find(".btn-cache").hasClass("auto-crawl-available");
		var is_standard_edition  = jQuery($this).parents(".btn-group").hasClass("cache-standard-edition");

		if (is_standard_edition) {
			var resource_default_state = ".material-check-pending";
		} else {
			var resource_default_state = ".material-check-pending";

		}
	
		
		var temp_cache_exists  = jQuery($this).parents(".btn-group").find(".temp-cache-exists");
		
		if (temp_cache_exists) {
			temp_cache_exists = !(jQuery($this).parents(".btn-group").find(".temp-cache-exists").hasClass("hidden"));
		}
		
		var optimization_credits_remaining = jQuery("#credits-remaining").html();
		
		if (optimization_credits_remaining == 0) {
			
			jQuery($this).parents(".btn-group").find(".material-local-cache-exists").addClass("fa-no-credits");
		}
		
		
		// hide icons
		jQuery($this).parents(".btn-group").find(".material-local-optimization-complete").addClass("hidden");
		jQuery($this).parents(".btn-group").find(".material-check").addClass("hidden");
		jQuery($this).parents(".btn-group").find(".material-local-cache-exists").addClass("hidden");
	
		jQuery($this).parents(".btn-group").find(".svg-tail-spin").removeClass("hidden");
		
		if (temp_cache_exists) {
		
			jQuery.post(ajaxurl,
					{ 'action': 'pegasaas_clear_optimization_request', 
					  'api_key': jQuery("#pegasaas-api-key").val(), 
					  'resource_id': resource_id},
					function(data) {
						
					}, "json");			
			
		} 
		
		
		jQuery($this).parents("ul").find("li").addClass("hidden");
		jQuery($this).parents("ul").find("li[data-state='cache-missing']").removeClass("hidden");
		jQuery($this).parents(".btn-group").find(".svg-tail-spin").addClass("hidden");
			
		build_page_cache($this);	
		
	}	
	
	
	
	
	function purge_page_cache($this) {
									  
		var resource_id 		= jQuery($this).attr("data-resource-id");
		var is_reoptimize 		= jQuery($this).attr("data-reoptimize") == 1;

		
		var auto_crawl_enabled  = jQuery($this).parents(".btn-group").find(".btn-cache").hasClass("auto-crawl-available");
		var is_standard_edition  = jQuery($this).parents(".btn-group").hasClass("cache-standard-edition");

		if (is_standard_edition) {
			if (auto_crawl_enabled) {
				var resource_default_state = ".material-check-pending";
			} else {
				var resource_default_state = ".material-optimized-on-next-visit";
			}
		} else {
			if (auto_crawl_enabled) {
				var resource_default_state = ".material-check-pending";
			} else {
				var resource_default_state = ".material-optimized-on-next-visit";
			}
		}
	
		
		var temp_cache_exists  = jQuery($this).parents(".btn-group").find(".temp-cache-exists");
		if (temp_cache_exists) {
			temp_cache_exists = !(jQuery($this).parents(".btn-group").find(".temp-cache-exists").hasClass("hidden"));
		}
		
		var optimization_credits_remaining = jQuery("#credits-remaining").html();
		
		if (optimization_credits_remaining == 0) {
			
			jQuery($this).parents(".btn-group").find(".material-local-cache-exists").addClass("fa-no-credits");
		}
		
		
		// hide icons
		jQuery($this).parents(".btn-group").find(".material-local-optimization-complete").addClass("hidden");
		jQuery($this).parents(".btn-group").find(".material-check").addClass("hidden");
		jQuery($this).parents(".btn-group").find(".material-local-cache-exists").addClass("hidden");
	
		jQuery($this).parents(".btn-group").find(".svg-tail-spin").removeClass("hidden");
		
		if (temp_cache_exists) {
		
			jQuery.post(ajaxurl,
					{ 'action': 'pegasaas_clear_optimization_request', 
					  'api_key': jQuery("#pegasaas-api-key").val(), 
					  'resource_id': resource_id},
					function(data) {
						
					}, "json");			
			
		} 
		
		jQuery.post(ajaxurl,
					{ 'action': 'pegasaas_clear_page_cache', 
					  'api_key': jQuery("#pegasaas-api-key").val(), 
					  'resource_id': resource_id},
					function(data) {
						jQuery($this).parents("ul").find("li").addClass("hidden");
						jQuery($this).parents("ul").find("li[data-state='cache-missing']").removeClass("hidden");
						jQuery($this).parents(".btn-group").find(".svg-tail-spin").addClass("hidden");
			if (is_reoptimize) { 
				build_page_cache($this);	
			} else {
					jQuery($this).parents(".btn-group").find(resource_default_state).removeClass("hidden");
				
			}
		
					}, "json");
	}	
	
	jQuery(".move-to-live-mode").bind("click", function(e) {
		e.preventDefault();
		var resource_id 		= jQuery(this).attr("data-resource-id");
		var $this = this;
		jQuery.post(ajaxurl,
					{ 'action': 'pegasaas_disable_staging_for_page', 
					  'api_key': jQuery("#pegasaas-api-key").val(), 
					  'resource_id': resource_id},
					function(data) {
						jQuery($this).parents(".staging-mode-status").removeClass("staging-mode-active");
						jQuery($this).parents(".staging-mode-status").addClass("staging-mode-disabled");
					}, "json");
		
	});
	jQuery(".move-to-staging-mode").bind("click", function(e) {
		e.preventDefault();
		var resource_id 		= jQuery(this).attr("data-resource-id");
		var $this = this;
		jQuery.post(ajaxurl,
					{ 'action': 'pegasaas_enable_staging_for_page', 
					  'api_key': jQuery("#pegasaas-api-key").val(), 
					  'resource_id': resource_id},
					function(data) {
						jQuery($this).parents(".staging-mode-status").removeClass("staging-mode-disabled");
						jQuery($this).parents(".staging-mode-status").addClass("staging-mode-active");
					}, "json");
		
	});
	
	jQuery("form.rescan-pagespeed").bind("submit", function(e) {
		e.preventDefault();
		<?php if (!PegasaasUtils::memory_within_limits()) { ?>
		alert("Uh oh, it looks like your PHP memory limit may just about be reached.  In order to safely operate, we ask that you increase your memory limit before attempting to optimize or scan any further items.");
		return;
		<?php } ?>
		
		if  (jQuery("#credits-remaining") && jQuery("#credits-remaining").html() < 5) {
			
			if (jQuery(this).parents("tr").find(".page-cache-form button i.fa-check").hasClass("hidden")) {
				alert("We are unable to request a new PageSpeed scan until an optimized page has been built.");
				return;
			}
		}
	
		jQuery(this).parents("tr").find(".accelerated-score-container").addClass("accelerated-scan-in-progress");
		jQuery(this).addClass("maybe-scanning");
		jQuery(this).parents("tr").find(".accelerated-score-container .progress-bar").addClass("active");
		jQuery(this).parents("tr").find(".accelerated-score-container .progress-bar").removeClass("progress-bar-success");
		jQuery(this).parents("tr").find(".accelerated-score-container .progress-bar").removeClass("progress-bar-warning");
		jQuery(this).parents("tr").find(".accelerated-score-container .progress-bar").removeClass("progress-bar-danger");
		jQuery(this).parents("tr").find(".accelerated-score-container .progress-bar").html("Scan Queued...");
		jQuery(this).parents("tr").find(".accelerated-score-container .progress-bar").css("width", "100%");
		
		//jQuery(this).parents("tr").find(".boosted-by").html("");
		var resource_id = jQuery(this).find("input[name='pid']").val();
	//	alert(resource_id);
		total_requests_this_page_load = 0; // reset the clock
		//console.log("resource id = "+ resource_id);
		manage_interval_time();
		// first validate that critical CSS is not required
		jQuery.post(ajaxurl,
					{ 'action': 'pegasaas_prerequest_pagespeed_score', 'api_key': jQuery("#pegasaas-api-key").val(), 'resource_id': resource_id},
					function(data) {
						total_requests_this_page_load++;
						
						console.log(data);
	
						if (data['status'] == 1) {
							
							jQuery.post(ajaxurl,
										{ 'action': 'pegasaas_request_pagespeed_score', 
										  'api_key': jQuery("#pegasaas-api-key").val(), 
										  'resource_id': resource_id },
										function(data) {
								//alert(resource_id);
							//	console.log(data);
							}, "json");
							
							jQuery(".rescan-pagespeed[rel='" + resource_id + "']").removeClass("maybe-scanning");
							jQuery(".rescan-pagespeed[rel='" + resource_id + "']").addClass("scanning");
							
							pegasaas_accelerator_increment_pending_pagespeed_scans_chart();
						} else {
							
							
						}
						
						
					}, "json");
	
					clearInterval(accelerated_pagespeed_interval_timer);
					accelerated_pagespeed_interval_timer =  setInterval(function() { pegasaas_accelerator_check_scores_2(); }, current_interval);									
	
		
		
	});
	
	
}

	function pegasaas_accelerator_update_pending_pagespeed_scans_chart(total_pending) {
		var pending_pagespeed_scans_container = jQuery("#pegasaas-pending-pagespeed-scans-chart");
		var base = pending_pagespeed_scans_container.attr("data-base");
		pending_pagespeed_scans_container.attr("data-scans-in-progress", total_pending);
		var percent = parseInt((base - total_pending)/base * 100);
		
		if (total_pending == 0) {
			pending_pagespeed_scans_container.find(".morphext-container").html("");
			pending_pagespeed_scans_container.find(".progress-bar").html("100%");
			pending_pagespeed_scans_container.find(".progress-bar").width("100%");
			pending_pagespeed_scans_container.find(".js-rotating").unbind().removeData();

			pending_pagespeed_scans_container.find(".progress-bar").addClass("progress-bar-success");
			pending_pagespeed_scans_container.find(".progress-bar").removeClass("active");
			pending_pagespeed_scans_container.find(".progress-bar").removeClass("progress-bar-pulse");
			
		} else {
			
			pending_pagespeed_scans_container.find(".progress-bar").html(percent + "%");
			var morphext = percent + "% Complete,"+total_pending + " Scans Queued,Estimated ";
			if (total_pending > 75) {
				var time_amount = parseInt(total_pending)/60;
				morphext = morphext + time_amount.toFixed(1) + " Hours"; 
			} else {
				var time_amount = parseInt(total_pending);
				morphext = morphext + time_amount + " Minutes"; 

			}
			pending_pagespeed_scans_container.find(".progress-bar").removeClass("progress-bar-success");
			pending_pagespeed_scans_container.find(".progress-bar").addClass("active");
			pending_pagespeed_scans_container.find(".progress-bar").addClass("progress-bar-pulse");

			pending_pagespeed_scans_container.find(".progress-bar").width(percent + "%");
			pending_pagespeed_scans_container.find(".js-rotating").removeClass("morphext");
			pending_pagespeed_scans_container.find(".js-rotating").unbind().removeData();
			pending_pagespeed_scans_container.find(".morphext-container").html("<span class='js-rotating'>" + morphext +"</span>");
			pending_pagespeed_scans_container.find(".js-rotating").Morphext({ animation: "fadeIn",speed: "2500"});

		}
		
		
		
	}
	
	function pegasaas_accelerator_increment_pending_pagespeed_scans_chart() {
		var pending_pagespeed_scans_container = jQuery("#pegasaas-pending-pagespeed-scans-chart");
		var scans_in_progress = parseInt(pending_pagespeed_scans_container.attr("data-scans-in-progress")) + 1;
		
		pegasaas_accelerator_update_pending_pagespeed_scans_chart(scans_in_progress);
	}
	
	function pegasaas_accelerator_update_pending_benchmark_scans_chart(total_pending) {
		var pending_pagespeed_scans_container = jQuery("#pegasaas-pending-benchmark-scans-chart");
		var base = pending_pagespeed_scans_container.attr("data-base");
		pending_pagespeed_scans_container.attr("data-scans-in-progress", total_pending);
		var percent = parseInt((base - total_pending)/base * 100);
		
		if (total_pending == 0) {
			pending_pagespeed_scans_container.find(".morphext-container").html("");
			pending_pagespeed_scans_container.find(".progress-bar").html("100%");
			pending_pagespeed_scans_container.find(".progress-bar").width("100%");
			pending_pagespeed_scans_container.find(".js-rotating").unbind().removeData();

			pending_pagespeed_scans_container.find(".progress-bar").addClass("progress-bar-success");
			pending_pagespeed_scans_container.find(".progress-bar").removeClass("active");
			pending_pagespeed_scans_container.find(".progress-bar").removeClass("progress-bar-pulse");
		} else {
			
			pending_pagespeed_scans_container.find(".progress-bar").html(percent + "%");
			var morphext = percent + "% Complete,"+total_pending + " Scans Queued,Estimated ";
			if (total_pending > 75) {
				var time_amount = parseInt(total_pending)/60;
				morphext = morphext + time_amount.toFixed(1) + " Hours"; 
			} else {
				var time_amount = parseInt(total_pending);
				morphext = morphext + time_amount + " Minutes"; 

			}
			
			pending_pagespeed_scans_container.find(".progress-bar").addClass("active");
			pending_pagespeed_scans_container.find(".progress-bar").addClass("progress-bar-pulse");
		
			pending_pagespeed_scans_container.find(".progress-bar").removeClass("progress-bar-success");
			pending_pagespeed_scans_container.find(".progress-bar").width(percent + "%");
			pending_pagespeed_scans_container.find(".js-rotating").removeClass("morphext");
			pending_pagespeed_scans_container.find(".js-rotating").unbind().removeData();
			pending_pagespeed_scans_container.find(".morphext-container").html("<span class='js-rotating'>" + morphext +"</span>");
			pending_pagespeed_scans_container.find(".js-rotating").Morphext({ animation: "fadeIn",speed: "2500"});

		}
		
	}
	function pegasaas_accelerator_increment_pending_benchmark_scans_chart() {
		var pending_pagespeed_scans_container = jQuery("#pegasaas-pending-benchmark-scans-chart");
		var scans_in_progress = parseInt(pending_pagespeed_scans_container.attr("data-scans-in-progress")) + 1;
		
		pegasaas_accelerator_update_pending_benchmark_scans_chart(scans_in_progress);
	}	
	
	
	
	function pegasaas_get_perf_speed(metric, speed) {
		if (metric == "ttfb") {
			if (speed <= 150) {
				return "fast";
			} else if (speed <= 300) {
				return "average";
			} else {
				return "slow";
			}
		} else if (metric == "fcp") {
			if (speed <= 2.336) {
				return "fast";
			} else if (speed <= 4.0) {
				return "average";
			} else {
				return "slow";
			}			
			
		} else if (metric == "fmp") {
			if (speed <= 2.336) {
				return "fast";
			} else if (speed <= 4.0) {
				return "average";
			} else {
				return "slow";
			}			
		} else if (metric == "fci") {
			if (speed <= 3.387) {
				return "fast";
			} else if (speed <= 5.8) {
				return "average";
			} else {
				return "slow";
			}			
		} else if (metric == "si") {
			if (speed <= 3.387) {
				return "fast";
			} else if (speed <= 5.8) {
				return "average";
			} else {
				return "slow";
			}			
		} else if (metric == "tti") {
			if (speed <= 3.785) {
				return "fast";
			} else if (speed <= 7.3) {
				return "average";
			} else {
				return "slow";
			}			
		}
		
	}
	
	function pegasaas_get_perf_color(rating) {
		if (rating == "fast") {
			return "#64BC63";
		} else if (rating == "average") {
			return "#e67700";
		} else if (rating == "slow") {
			return "#cc0000";
		} else {
			return "#cccccc";
		}

	}
	
	function pegasaas_update_perf_score(container, view, metric, value) {
	//	var perf_score = jQuery(container).closest("tr").find("." + view + "-perf-score-" + metric);
		var perf_score = jQuery(container).find("." + view + "-perf-score-" + metric);
		if (metric == "ttfb") {
			var suffix = "ms";
		} else {
			var suffix = "s";
		}
		if (value == 0.0 || value == 0) {
			return;
		}
		var perf_rating = pegasaas_get_perf_speed(metric, value);
		jQuery(perf_score).removeClass("perf-metric-slow");
		jQuery(perf_score).removeClass("perf-metric-average");
		jQuery(perf_score).removeClass("perf-metric-fast");
		jQuery(perf_score).addClass("perf-metric-" + perf_rating);
		//var perf_color  = pegasaas_get_perf_color(perf_rating);
		//jQuery(perf_score).data("color", "#cccccc,"+perf_color);
		jQuery(perf_score).data("content", value + suffix);
		
		jQuery(perf_score).loading();
	}	
var fetch_attempts = 0;
	
function pegasaas_fetch_data() {
	jQuery("table.pagespeed-scores > tbody > tr:not(.header-row)").addClass("deleting");
	jQuery.post(ajaxurl, 
					{ 'action': 'pegasaas_fetch_page_metrics', 
					 'time' : Date.now(),
					 'api_key': jQuery("#pegasaas-api-key").val(),
					 'lang': 'en'
					
					}, 
					 
					function(data) {
						if (data['status'] == 1) {
							if (data['pages'].length == 0 && fetch_attempts++ < 2) {
								console.log("Refetching Data");
								pegasaas_fetch_data();
							
							} else {
								pegasaas_pages = data['pages'];
								pegasaas_settings = data['settings'];
								pegasaas_cache_map = data['cache_map'];
								max_results_page = data['max_results_page'];
								results_per_page = data['results_per_page'];
								current_results_page = data['current_results_page'];  
								render_pages();
							}
						}
					}, 'json');
}
	
function pegasaas_render_properties(props) {
	  return function(tok, i) {
    return (i % 2) ? props[tok] : tok;
  };
}

	
var pegasaas_pages = {};
var pegasaas_settings = {};
var pegasaas_cache_map = {};
var max_results_page = 0;
var results_per_page = 0;
var current_results_page = 0;
var web_perf_metrics_ttfb_class = "<?php echo $pegasaas->interface->web_perf_metrics_ttfb_class; ?>";
var web_perf_metrics_fcp_class = "<?php echo $pegasaas->interface->web_perf_metrics_fcp_class; ?>";
var web_perf_metrics_si_class  = "<?php echo $pegasaas->interface->web_perf_metrics_si_class; ?>";
var web_perf_metrics_lcp_class = "<?php echo $pegasaas->interface->web_perf_metrics_lcp_class; ?>";
var web_perf_metrics_tti_class = "<?php echo $pegasaas->interface->web_perf_metrics_tti_class; ?>";
var web_perf_metrics_fci_class = "<?php echo $pegasaas->interface->web_perf_metrics_fci_class; ?>";
var web_perf_metrics_tbt_class = "<?php echo $pegasaas->interface->web_perf_metrics_tbt_class; ?>";
var web_perf_metrics_cls_class = "<?php echo $pegasaas->interface->web_perf_metrics_cls_class; ?>";
	
function render_pages() {
	jQuery("table.pagespeed-scores > tbody > tr:not(.header-row)").remove();

	var items = [{ url: 'http://foo.com'}];
//	var row_template = jQuery("#page-score-row-template").text().split(/\$\{(.+?)\}/g);
	var row_template = jQuery("#page-score-row-template").text();
	
   
	nunjucks.configure({ autoescape: true });
	
	var no_credits = pegasaas_settings.limits.monthly_optimizations > 0 && pegasaas_settings.limits.monthly_optimizations_remaining == 0;
    var auto_crawl_enabled = pegasaas_settings.settings.auto_crawl.status == 1;
	

	for (var page_record of pegasaas_pages) {
		var slug = page_record.slug;
		var resource_id = page_record.resource_id;
		page_record['settings'] 	= pegasaas_settings;
		page_record['no_credits'] 	= no_credits;
		page_record['auto_crawl_enabled'] = auto_crawl_enabled;
		page_record['temp_cache_exists'] 	= pegasaas_cache_map[resource_id] != undefined && pegasaas_cache_map[resource_id] != undefined && pegasaas_cache_map[resource_id].is_temp == true;
		page_record['cache_exists'] 		= pegasaas_cache_map[resource_id] != undefined && pegasaas_cache_map[resource_id] != "";
		page_record['staging_mode_status'] 	= page_record['staging_mode_disabled'] == true ? "staging-mode-disabled" : "staging-mode-active";
		
		page_record['web_perf_metrics_ttfb_class'] = web_perf_metrics_ttfb_class;
		page_record['web_perf_metrics_fcp_class'] = web_perf_metrics_fcp_class;
		page_record['web_perf_metrics_si_class'] = web_perf_metrics_si_class;
		page_record['web_perf_metrics_lcp_class'] = web_perf_metrics_lcp_class;
		page_record['web_perf_metrics_tti_class'] = web_perf_metrics_tti_class;
		page_record['web_perf_metrics_fci_class'] = web_perf_metrics_fci_class;
		page_record['web_perf_metrics_tbt_class'] = web_perf_metrics_tbt_class;
		page_record['web_perf_metrics_cls_class'] = web_perf_metrics_cls_class;
		//console.log("page");


		//var this_row = jQuery.mustache(row_template, page_record);;
		//document.getElementById('content').innerHTML = ;
		var this_row = nunjucks.renderString(row_template, page_record);
		
		//var this_row = row_template;
		//this_row = page_record_item.map(function(item) {
		//	return row_template.map(pegasaas_render_properties(item)).join('');
	  //	});
		
	  jQuery('table.pagespeed-scores > tbody').append(this_row);
	}
	
	if (current_results_page == 1) {
		jQuery("#results_page_fast_backwards").prop("disabled", true);
		jQuery("#results_page_backward").prop("disabled", true);
	} else { 
		jQuery("#results_page_fast_backwards").prop("disabled", false);
		jQuery("#results_page_backward").prop("disabled", false);
	}
	
	if (current_results_page < max_results_page) {
		jQuery("#results_page_fast_forwards").prop("disabled", false);
		jQuery("#results_page_forward").prop("disabled", false);
	} else {
		jQuery("#results_page_fast_forwards").prop("disabled", true);
		jQuery("#results_page_forward").prop("disabled", true);
	}
	
	jQuery("#results_page_forward").val(current_results_page + 1);
	jQuery("#results_page_backward").val(current_results_page - 1);
	jQuery("#results_page_fast_forwards").val(max_results_page);
	
	
	jQuery("#total_result_pages").html(max_results_page);
	jQuery("#current_results_page").html(current_results_page);
	init_bindings();
	
	jQuery("#pegasaas-accelerator-main-dashboard").addClass("in");
	
	start_interface_checks();
		
  }	
	

<?php if (isset(PegasaasAccelerator::$settings['status'])) { ?>	
pegasaas_fetch_data();
<?php } ?>
	
function pegasaas_check_for_data() {
	
	
	var pending_requests = new Array();
	var maybe_scanning = 0;
	
	// do pagespeed
	// do baseline
	// do accelerated pages queue
	
	jQuery(".rescan-pagespeed.scanning").each(
		function() { 
			pending_requests.push(jQuery(this).find("input[name='pid']").val());
		}
	);
	
	jQuery(".rescan-pagespeed.maybe-scanning").each(
		function() {
			maybe_scanning++;
			jQuery(this).submit();
		}
	);	
	

	
	console.log("Pending Scan Requests Length: " + pending_requests.length);
	console.log("Pending CSS Requests Length: " + maybe_scanning);
	if (pending_requests.length == 0 && maybe_scanning == 0) {
			console.log("Clearing accelerated_pagespeed_interval_timer because zero and zero.");
			clearInterval(accelerated_pagespeed_interval_timer);
		return;
	}
	manage_interval_time();
	

	
	jQuery.post(ajaxurl, 
					{ 'action': 'pegasaas_check_queued_pagespeed_score_requests', 'pending_requests': pending_requests, 'api_key': jQuery("#pegasaas-api-key").val()}, 
											function(data) {
												total_requests_this_page_load++;
												if (data['status'] == -1) {
													console.log("API Key Error in fetching new scores");
												} else {
													//console.log(data.length);
													//console.log(data);
													for (var resource_id in data) {
													  if (resource_id != "#summary#") {
													 
													    var desktop_score = data[resource_id]['desktop_score'];
													    var mobile_score = data[resource_id]['desktop_mobile'];
														//console.log("the score is " + the_score);
													  if (desktop_score == null && mobile_score == null) {
	
													  } else { 
														  
														var the_container = jQuery(".rescan-pagespeed.scanning[rel='"+resource_id+"']");
														jQuery(the_container).removeClass("scanning");
														jQuery(the_container).parents("tr").find(".accelerated-score-container").removeClass("accelerated-scan-in-progress");
													  	jQuery(the_container).find("button[type='submit']").removeAttr("disabled");
													  	jQuery(the_container).find("button[type='submit']").html("Scan Now");	
														
													  	//var the_progress_bar_container = jQuery(the_container).parents("tr").find(".accelerated-score-container .progress-bar");
													  	
													  	
											
														
														

													  	
													  	
														//var load_time = jQuery(the_container).parents("tr").find(".accelerated-load-time");
														//jQuery(load_time).html(data[resource_id]['load_time'] + "s");
														
														pegasaas_update_perf_score(the_container, "desktop", "ttfb", data[resource_id]['desktop_ttfb']);
														pegasaas_update_perf_score(the_container, "desktop", "fcp", data[resource_id]['desktop_fcp']);
														pegasaas_update_perf_score(the_container, "desktop", "fmp", data[resource_id]['desktop_fmp']);
														pegasaas_update_perf_score(the_container, "desktop", "si", data[resource_id]['desktop_si']);
														pegasaas_update_perf_score(the_container, "desktop", "fci", data[resource_id]['desktop_fci']);
														pegasaas_update_perf_score(the_container, "desktop", "tti", data[resource_id]['desktop_tti']);
												
														  
														// update desktop pagespeed score  
														var desktop_progress_bar_container = jQuery(the_container).parents("tr").find(".accelerated-score-container .desktop-score-container .progress-bar");

														jQuery(desktop_progress_bar_container).removeClass("active");  
														jQuery(desktop_progress_bar_container).removeClass('progress-bar-paused');
														jQuery(desktop_progress_bar_container).html(data[resource_id]['desktop_score']);
														  
														if (data[resource_id]['desktop_score'] >= "90") {
														  	jQuery(desktop_progress_bar_container).addClass("progress-bar-success");
													  	} else if (data[resource_id]['desktop_score'] >= "50") {
															jQuery(desktop_progress_bar_container).addClass('progress-bar-warning');
														} else {
															jQuery(desktop_progress_bar_container).addClass('progress-bar-danger');
													  	}
														jQuery(desktop_progress_bar_container).css('width', data[resource_id]['desktop_score'] + "%");

														  

														pegasaas_update_perf_score(the_container, "mobile", "ttfb", data[resource_id]['mobile_ttfb']);
														pegasaas_update_perf_score(the_container, "mobile", "fcp", data[resource_id]['mobile_fcp']);
														pegasaas_update_perf_score(the_container, "mobile", "fmp", data[resource_id]['mobile_fmp']);
														pegasaas_update_perf_score(the_container, "mobile", "si", data[resource_id]['mobile_si']);
														pegasaas_update_perf_score(the_container, "mobile", "fci", data[resource_id]['mobile_fci']);
														pegasaas_update_perf_score(the_container, "mobile", "tti", data[resource_id]['mobile_tti']);
													  
														  
														// update mobile pagespeed score
														var mobile_progress_bar_container = jQuery(the_container).parents("tr").find(".accelerated-score-container .mobile-score-container .progress-bar");
														jQuery(mobile_progress_bar_container).removeClass("active");
														  jQuery(mobile_progress_bar_container).removeClass('progress-bar-paused');
														jQuery(mobile_progress_bar_container).html(data[resource_id]['mobile_score']);
														
														if (data[resource_id]['mobile_score'] >= "90") {
														  	jQuery(mobile_progress_bar_container).addClass("progress-bar-success");
													  	} else if (data[resource_id]['mobile_score'] >= "50") {
															jQuery(mobile_progress_bar_container).addClass('progress-bar-warning');
														} else if (data[resource_id]['mobile_score'] == "" || data[resource_id]['mobile_score'] == null) {
															jQuery(mobile_progress_bar_container).html("Rescan Required");
														} else {
															jQuery(mobile_progress_bar_container).addClass('progress-bar-danger');
														}
														jQuery(mobile_progress_bar_container).css('width', data[resource_id]['mobile_score'] + "%");
													
														  
														  
														
														
														  
														  
													
													  }
														
																										
														
														
												
													}
													
													var pending_requests = jQuery(".rescan-pagespeed.scanning").length + jQuery(".rescan-pagespeed.maybe-scanning").length;
													
													var requests_scanned = jQuery(".accelerated-score-container").length - pending_requests;
													
													if (requests_scanned < 0) {
														requests_scanned = 0;
													}
													
													jQuery(".pegasaas-requests-scanned").html(requests_scanned);
													
														if (pending_requests == 0 || jQuery("#accelerated-prepping-container").css("display") == "inline-block") {
															clearInterval(accelerated_pagespeed_interval_timer);
															
												
														jQuery("#accelerated-prepping-container").css("display", "none");
															jQuery("#accelerated-chart-container").css("display", "inline-block");
	
													
														render_accelerated_chart();
															
														
															
											

														}	
														var summary = data['#summary#'];
														pegasaas_accelerator_update_pending_pagespeed_scans_chart(summary['total_pending_requests']);

													}
												}
												
											}, 
											"json");
jQuery.post("admin.php?page=pegasaas-accelerator", 
																{ 'c': 'push-scan',
																 'via': 'pegasaas_check_for_data',
																 'output': 'raw'}, 
																		function(data) {});
	

}	
	
	function render_accelerated_chart() {
		jQuery.post("admin.php?page=pegasaas-accelerator", 
																{ 'c': 'render-accelerated-chart', 'output': 'raw'}, 
																		function(data) {
																eval(data);
															
																accelerated_mobile_score_display = accelerated_mobile_score.replace(".", "<span class='super'>.");
																if (accelerated_mobile_score_display.search("super") > 0) {
																	accelerated_mobile_score_display = accelerated_mobile_score_display + "</span>";
																}
																accelerated_desktop_score_display = accelerated_desktop_score.replace(".", "<span class='super'>.");
																if (accelerated_desktop_score_display.search("super") > 0) {
																	accelerated_desktop_score_display = accelerated_desktop_score_display + "</span>";
																}															
												
															
															
															
															var new_html = "<span class='mobile-only'>"+ accelerated_mobile_score + "</span>" +
																           "<span class='desktop-only'>"+ accelerated_desktop_score + "</span>";
															jQuery("#pegasaas-site-speed-display").html(new_html);
															jQuery("#pegasaas-scores-wrapper").removeClass("is-prepping");
															
															var benchmark_mobile_score = parseInt(jQuery(".primary-gauge-reference.mobile-only .pgr-l").text());
															var benchmark_desktop_score = parseInt(jQuery(".primary-gauge-reference.desktop-only .pgr-l").text());
															var mobile_difference_icon = "";
															var desktop_difference_icon = "";
	
															var primary_gauge_difference_mobile = (accelerated_mobile_score - benchmark_mobile_score).toFixed(1);
															var primary_gauge_difference_desktop = (accelerated_desktop_score - benchmark_desktop_score).toFixed(1);
		

															if (primary_gauge_difference_mobile > 0) {
																mobile_difference_icon = '<i class="fa fa-long-arrow-up"></i> ';
															} else if (primary_gauge_difference_mobile < 0) {
																mobile_difference_icon = '<i class="fa fa-long-arrow-down"></i> ';
															} 
															
															if (accelerated_desktop_score > 0) {
																desktop_difference_icon = '<i class="fa fa-long-arrow-up"></i> ';
															} else if (accelerated_desktop_score < 0) {
																desktop_difference_icon = '<i class="fa fa-long-arrow-down"></i> ';
															} 															
															
															jQuery(".primary-gauge-reference.mobile-only .pgr-r").html(mobile_difference_icon  + primary_gauge_difference_mobile);
															jQuery(".primary-gauge-reference.desktop-only .pgr-r").html(desktop_difference_icon + primary_gauge_difference_desktop);
														
															
															
															
															
															
															
															// accelerated score is defined in the code fetched and evaluated, above
															//animate_dashboard_indicator(0);
														//	setTimeout('animate_dashboard_indicator(' + accelerated_score + ')', 1500);
			 
																
															jQuery("#pegasaas-accelerated-desktop-score-container .the-score").html(accelerated_desktop_score);
															jQuery("#pegasaas-accelerated-desktop-score-container").removeClass("score-success");
															jQuery("#pegasaas-accelerated-desktop-score-container").removeClass("score-warning");
															jQuery("#pegasaas-accelerated-desktop-score-container").removeClass("score-danger");
															jQuery("#pegasaas-accelerated-desktop-score-container").removeClass("score-success");
															jQuery("#pegasaas-accelerated-desktop-score-container").removeClass("score-warning");
															jQuery("#pegasaas-accelerated-desktop-score-container").removeClass("score-danger");
															if (accelerated_desktop_score >= 85) {
																jQuery("#pegasaas-accelerated-desktop-score-container").addClass("score-success");

															} else if (accelerated_desktop_score >= 75) {
																jQuery("#pegasaas-accelerated-desktop-score-container").addClass("score-warning");

															} else if (accelerated_desktop_score >= 0) {
																jQuery("#pegasaas-accelerated-desktop-score-container").addClass("score-danger");

															}  

															
															
															if (accelerated_mobile_score >= 85) {
																jQuery("#pegasaas-accelerated-mobile-score-container").addClass("score-success");

															} else if (accelerated_mobile_score >= 75) {
																jQuery("#pegasaas-accelerated-mobile-score-container").addClass("score-warning");

															} else if (accelerated_mobile_score >= 0) {
																jQuery("#pegasaas-accelerated-mobile-score-container").addClass("score-danger");
															}  															
															jQuery("#pegasaas-accelerated-mobile-score-container .the-score").html(accelerated_mobile_score);
															//console.log("okay done"+ accelerated_mobile_score);
															});
		
	}
	
function pegasaas_accelerator_check_scores_2() {
	
	
	var pending_requests = new Array();
	var maybe_scanning = 0;
	
	jQuery(".rescan-pagespeed.scanning").each(
		function() { 
			pending_requests.push(jQuery(this).find("input[name='pid']").val());
		});
	jQuery(".rescan-pagespeed.maybe-scanning").each(
		function() {
			maybe_scanning++;
			jQuery(this).submit();
		});	
	

	
	console.log("Pending Scan Requests Length: " + pending_requests.length);
	console.log("Pending CSS Requests Length: " + maybe_scanning);
	if (pending_requests.length == 0 && maybe_scanning == 0) {
			console.log("Clearing accelerated_pagespeed_interval_timer because zero and zero.");
			clearInterval(accelerated_pagespeed_interval_timer);
		return;
	}
	manage_interval_time();
	

	
	jQuery.post(ajaxurl, 
					{ 'action': 'pegasaas_check_queued_pagespeed_score_requests', 'pending_requests': pending_requests, 'api_key': jQuery("#pegasaas-api-key").val()}, 
											function(data) {
												total_requests_this_page_load++;
												if (data['status'] == -1) {
													console.log("API Key Error in fetching new scores");
												} else {
													//console.log(data.length);
													//console.log(data);
													for (var resource_id in data) {
													  if (resource_id != "#summary#") {
													 
													    var desktop_score = data[resource_id]['desktop_score'];
													    var mobile_score = data[resource_id]['desktop_mobile'];
														//console.log("the score is " + the_score);
													  if (desktop_score == null && mobile_score == null) {
	
													  } else { 
														  
														var the_container = jQuery(".rescan-pagespeed.scanning[rel='"+resource_id+"']");
														jQuery(the_container).removeClass("scanning");
														jQuery(the_container).parents("tr").find(".accelerated-score-container").removeClass("accelerated-scan-in-progress");
													  	jQuery(the_container).find("button[type='submit']").removeAttr("disabled");
													  	jQuery(the_container).find("button[type='submit']").html("Scan Now");	
														
													  	//var the_progress_bar_container = jQuery(the_container).parents("tr").find(".accelerated-score-container .progress-bar");
													  	
													  	
											
														
														

													  	
													  	
														//var load_time = jQuery(the_container).parents("tr").find(".accelerated-load-time");
														//jQuery(load_time).html(data[resource_id]['load_time'] + "s");
														
														pegasaas_update_perf_score(the_container, "desktop", "ttfb", data[resource_id]['desktop_ttfb']);
														pegasaas_update_perf_score(the_container, "desktop", "fcp", data[resource_id]['desktop_fcp']);
														pegasaas_update_perf_score(the_container, "desktop", "fmp", data[resource_id]['desktop_fmp']);
														pegasaas_update_perf_score(the_container, "desktop", "si", data[resource_id]['desktop_si']);
														pegasaas_update_perf_score(the_container, "desktop", "fci", data[resource_id]['desktop_fci']);
														pegasaas_update_perf_score(the_container, "desktop", "tti", data[resource_id]['desktop_tti']);
												
														  
														// update desktop pagespeed score  
														var desktop_progress_bar_container = jQuery(the_container).parents("tr").find(".accelerated-score-container .desktop-score-container .progress-bar");

														jQuery(desktop_progress_bar_container).removeClass("active");  
														jQuery(desktop_progress_bar_container).removeClass("progress-bar-paused");  
														jQuery(desktop_progress_bar_container).html(data[resource_id]['desktop_score'] + "%");
														  
														if (data[resource_id]['desktop_score'] >= "90") {
														  	jQuery(desktop_progress_bar_container).addClass("progress-bar-success");
													  	} else if (data[resource_id]['desktop_score'] >= "50") {
															jQuery(desktop_progress_bar_container).addClass('progress-bar-warning');
														} else {
															jQuery(desktop_progress_bar_container).addClass('progress-bar-danger');
													  	}
														//jQuery(desktop_progress_bar_container).css('width', data[resource_id]['desktop_score'] + "%");
														jQuery(desktop_progress_bar_container).css('width',  "100%");

														  

														pegasaas_update_perf_score(the_container, "mobile", "ttfb", data[resource_id]['mobile_ttfb']);
														pegasaas_update_perf_score(the_container, "mobile", "fcp", data[resource_id]['mobile_fcp']);
														pegasaas_update_perf_score(the_container, "mobile", "fmp", data[resource_id]['mobile_fmp']);
														pegasaas_update_perf_score(the_container, "mobile", "si", data[resource_id]['mobile_si']);
														pegasaas_update_perf_score(the_container, "mobile", "fci", data[resource_id]['mobile_fci']);
														pegasaas_update_perf_score(the_container, "mobile", "tti", data[resource_id]['mobile_tti']);
													  
														  
														// update mobile pagespeed score
														var mobile_progress_bar_container = jQuery(the_container).parents("tr").find(".accelerated-score-container .mobile-score-container .progress-bar");
														jQuery(mobile_progress_bar_container).removeClass("active");
														  jQuery(mobile_progress_bar_container).removeClass("progress-bar-paused");
														jQuery(mobile_progress_bar_container).html(data[resource_id]['mobile_score'] + "%");
														
														if (data[resource_id]['mobile_score'] >= "90") {
														  	jQuery(mobile_progress_bar_container).addClass("progress-bar-success");
													  	} else if (data[resource_id]['mobile_score'] >= "50") {
															jQuery(mobile_progress_bar_container).addClass('progress-bar-warning');
														} else if (data[resource_id]['mobile_score'] == "" || data[resource_id]['mobile_score'] == null) {
															jQuery(mobile_progress_bar_container).html("Rescan Required");
														} else {
															jQuery(mobile_progress_bar_container).addClass('progress-bar-danger');
														}
														//jQuery(mobile_progress_bar_container).css('width', data[resource_id]['mobile_score'] + "%");
														jQuery(mobile_progress_bar_container).css('width', "100%");
													
														  
														  
														
														
														  
													
													  }
														
																										
														
														
												
													}
													
													var pending_requests = jQuery(".rescan-pagespeed.scanning").length + jQuery(".rescan-pagespeed.maybe-scanning").length;
													
													var requests_scanned = jQuery(".accelerated-score-container").length - pending_requests;
													
													if (requests_scanned < 0) {
														requests_scanned = 0;
													}
													
													jQuery(".pegasaas-requests-scanned").html(requests_scanned);
													
													console.log("" + pending_requests + " pending requests");
														if (pending_requests == 0 || jQuery("#accelerated-prepping-container").css("display") == "inline-block") {
															clearInterval(accelerated_pagespeed_interval_timer);
															
												
														jQuery("#accelerated-prepping-container").css("display", "none");
															jQuery("#accelerated-chart-container").css("display", "inline-block");
	
													
														
														jQuery.post("admin.php?page=pegasaas-accelerator", 
																{ 'c': 'render-accelerated-chart', 'output': 'raw'}, 
																		function(data) {
															eval(data);
															
															accelerated_mobile_score_display = accelerated_mobile_score.replace(".", "<span class='super'>.");
																if (accelerated_mobile_score_display.search("super") > 0) {
																	accelerated_mobile_score_display = accelerated_mobile_score_display + "</span>";
																}
																accelerated_desktop_score_display = accelerated_desktop_score.replace(".", "<span class='super'>.");
																if (accelerated_desktop_score_display.search("super") > 0) {
																	accelerated_desktop_score_display = accelerated_desktop_score_display + "</span>";
																}	
															
															var new_html = "<span class='mobile-only'>"+ accelerated_mobile_score_display + "</span>" +
																           "<span class='desktop-only'>"+ accelerated_desktop_score_display + "</span>";
															jQuery("#pegasaas-site-speed-display").html(new_html);
																														jQuery("#pegasaas-scores-wrapper").removeClass("is-prepping");

															var benchmark_mobile_score = parseInt(jQuery(".primary-gauge-reference.mobile-only .pgr-l").text());
															var benchmark_desktop_score = parseInt(jQuery(".primary-gauge-reference.desktop-only .pgr-l").text());
															var mobile_difference_icon = "";
															var desktop_difference_icon = "";
	
															var primary_gauge_difference_mobile = (accelerated_mobile_score - benchmark_mobile_score).toFixed(1);
															var primary_gauge_difference_desktop = (accelerated_desktop_score - benchmark_desktop_score).toFixed(1);
		

															if (primary_gauge_difference_mobile > 0) {
																mobile_difference_icon = '<i class="fa fa-long-arrow-up"></i> ';
															} else if (primary_gauge_difference_mobile < 0) {
																mobile_difference_icon = '<i class="fa fa-long-arrow-down"></i> ';
															} 
															
															if (accelerated_desktop_score > 0) {
																desktop_difference_icon = '<i class="fa fa-long-arrow-up"></i> ';
															} else if (accelerated_desktop_score < 0) {
																desktop_difference_icon = '<i class="fa fa-long-arrow-down"></i> ';
															} 															
															
															jQuery(".primary-gauge-reference.mobile-only .pgr-r").html(mobile_difference_icon  + primary_gauge_difference_mobile);
															jQuery(".primary-gauge-reference.desktop-only .pgr-r").html(desktop_difference_icon + primary_gauge_difference_desktop);
														
															
															
															/*
															var ctx = jQuery("#pegasaas-site-speed-chart canvas#desktop-chart");	
															var accelerated_pagespeed_chart = new Chart(ctx, {
																type: 'doughnut',
																data: desktop_accelerated_data,
																options: accelerated_options
															});	
															
															var ctx = jQuery("#pegasaas-site-speed-chart canvas#mobile-chart");	
															var accelerated_pagespeed_chart = new Chart(ctx, {
																type: 'doughnut',
																data: mobile_accelerated_data,
																options: accelerated_options
															});	
															*/
															
															// accelerated score is defined in the code fetched and evaluated, above
															//animate_dashboard_indicator(0);
														//	setTimeout('animate_dashboard_indicator(' + accelerated_score + ')', 1500);
			 
																
															jQuery("#pegasaas-accelerated-desktop-score-container .the-score").html(accelerated_desktop_score);
															jQuery("#pegasaas-accelerated-desktop-score-container").removeClass("score-success");
															jQuery("#pegasaas-accelerated-desktop-score-container").removeClass("score-warning");
															jQuery("#pegasaas-accelerated-desktop-score-container").removeClass("score-danger");
															jQuery("#pegasaas-accelerated-desktop-score-container").removeClass("score-success");
															jQuery("#pegasaas-accelerated-desktop-score-container").removeClass("score-warning");
															jQuery("#pegasaas-accelerated-desktop-score-container").removeClass("score-danger");
															if (accelerated_desktop_score >= 85) {
																jQuery("#pegasaas-accelerated-desktop-score-container").addClass("score-success");

															} else if (accelerated_desktop_score >= 75) {
																jQuery("#pegasaas-accelerated-desktop-score-container").addClass("score-warning");

															} else if (accelerated_desktop_score >= 0) {
																jQuery("#pegasaas-accelerated-desktop-score-container").addClass("score-danger");

															}  
															
															
															if (accelerated_mobile_score >= 85) {
																jQuery("#pegasaas-accelerated-mobile-score-container").addClass("score-success");

															} else if (accelerated_mobile_score >= 75) {
																jQuery("#pegasaas-accelerated-mobile-score-container").addClass("score-warning");

															} else if (accelerated_mobile_score >= 0) {
																jQuery("#pegasaas-accelerated-mobile-score-container").addClass("score-danger");
															}  															
															jQuery("#pegasaas-accelerated-mobile-score-container .the-score").html(accelerated_mobile_score);
															//console.log("okay done"+ accelerated_mobile_score);
															});
															
											

														}	
														var summary = data['#summary#'];
														pegasaas_accelerator_update_pending_pagespeed_scans_chart(summary['total_pending_requests']);

													}
												}
												
											}, 
											"json");
jQuery.post("admin.php?page=pegasaas-accelerator", 
																{ 'c': 'push-scan',
																 'via': 'pegasaas_accelerator_check_scores_2', 
																 'output': 'raw'}, 
																		function(data) {});
	

}
	
	
function pegasaas_accelerator_check_benchmark_scores() {
	
	console.log("Checking Benchmark Scores...");
	var pending_requests = new Array();
	jQuery(".benchmark-scan-in-progress").each(function() {
		pending_requests.push(jQuery(this).attr("rel"));
		//console.log("x" + jQuery(this).attr("rel"));
	});
	
	
	if (pending_requests.length == 0) {
		clearInterval(benchmark_interval_timer);
		clearInterval(accelerated_pagespeed_interval_timer);
		accelerated_pagespeed_interval_timer =  setInterval(function() { pegasaas_accelerator_check_scores_2(); }, current_interval);	
		
		jQuery("#non-accelerated-prepping-container").css("display", "none");
		jQuery("#non-accelerated-chart-container").css("display", "inline-block");
		return;
		
		
	}
	manage_interval_time();
	//alert(bctx);
	jQuery.post(ajaxurl, 
											{ 'action': 'pegasaas_check_queued_pagespeed_benchmark_score_requests', 'pending_requests': pending_requests, 'api_key': jQuery("#pegasaas-api-key").val()}, 
											function(data) {
												total_requests_this_page_load++;
												if (data['status'] == -1) {
													console.log("API Key Error in fetching new scores");
												} else {
													//console.log(data);
													for (var resource_id in data) {
														if (resource_id != "#summary") {
														//console.log("benchmark data");
													   // console.log(resource_id);
														
														//console.log(data[resource_id]);
														
														if (data[resource_id]['desktop_score'] != null && data[resource_id]['mobile_score'] != null) {
													  		var the_container = jQuery(".benchmark-scan-in-progress[rel='"+resource_id+"']");

													  	jQuery(the_container).removeClass("benchmark-scan-in-progress");
													  //jQuery(the_container).find("button[type='submit']").removeAttr("disabled");
													  //jQuery(the_container).find("button[type='submit']").html("Scan Now");	
														
														pegasaas_update_perf_score(the_container, "desktop", "ttfb", data[resource_id]['desktop_ttfb']);
														pegasaas_update_perf_score(the_container, "desktop", "fcp", data[resource_id]['desktop_fcp']);
														pegasaas_update_perf_score(the_container, "desktop", "fmp", data[resource_id]['desktop_fmp']);
														pegasaas_update_perf_score(the_container, "desktop", "si", data[resource_id]['desktop_si']);
														pegasaas_update_perf_score(the_container, "desktop", "fci", data[resource_id]['desktop_fci']);
														pegasaas_update_perf_score(the_container, "desktop", "tti", data[resource_id]['desktop_tti']);

															
														pegasaas_update_perf_score(the_container, "mobile", "ttfb", data[resource_id]['mobile_ttfb']);
														pegasaas_update_perf_score(the_container, "mobile", "fcp", data[resource_id]['mobile_fcp']);
														pegasaas_update_perf_score(the_container, "mobile", "fmp", data[resource_id]['mobile_fmp']);
														pegasaas_update_perf_score(the_container, "mobile", "si", data[resource_id]['mobile_si']);
														pegasaas_update_perf_score(the_container, "mobile", "fci", data[resource_id]['mobile_fci']);
														pegasaas_update_perf_score(the_container, "mobile", "tti", data[resource_id]['mobile_tti']);

														var desktop_progress_bar_container = jQuery(the_container).find(".desktop-score-container .progress-bar");
														//var load_time = jQuery(the_container).parents("tr").find(".benchmark-load-time");
															
													  	var mobile_progress_bar_container = jQuery(the_container).find(".mobile-score-container .progress-bar");
											
													
													  	jQuery(desktop_progress_bar_container).html(data[resource_id]['desktop_score'] + "%");
													  	jQuery(mobile_progress_bar_container).html(data[resource_id]['mobile_score'] + "%");
														jQuery(desktop_progress_bar_container).removeClass("active");
														jQuery(mobile_progress_bar_container).removeClass("active");
															
														jQuery(desktop_progress_bar_container).removeClass('progress-bar-paused');
														jQuery(mobile_progress_bar_container).removeClass("progress-bar-paused");
															
														//jQuery(load_time).html(data[resource_id]['load_time'] + "s");
															
														if (data[resource_id]['desktop_score'] >= "90") {
														  	jQuery(desktop_progress_bar_container).addClass("progress-bar-success");
													  	} else if (data[resource_id]['desktop_score'] >= "50") {
															jQuery(desktop_progress_bar_container).addClass('progress-bar-warning');
													  	} else {
															jQuery(desktop_progress_bar_container).addClass('progress-bar-danger');
													  	}
														
														if (data[resource_id]['mobile_score'] >= "90") {
														  	jQuery(mobile_progress_bar_container).addClass("progress-bar-success");
													  	} else if (data[resource_id]['mobile_score'] >= "50") {
															jQuery(mobile_progress_bar_container).addClass('progress-bar-warning');
														} else if (data[resource_id]['mobile_score'] == "" || data[resource_id]['mobile_score'] == null) {
															//jQuery(mobile_progress_bar_container).addClass('progress-bar-danger');
															jQuery(mobile_progress_bar_container).html("Rescan Required");

														} else {
															jQuery(mobile_progress_bar_container).addClass('progress-bar-danger');
														}
														  
														  
														  
														//jQuery(desktop_progress_bar_container).css('width', data[resource_id]['desktop_score'] + "%");
														//jQuery(mobile_progress_bar_container).css('width', data[resource_id]['mobile_score'] + "%");
														jQuery(desktop_progress_bar_container).css('width', "100%");
														jQuery(mobile_progress_bar_container).css('width', "100%");
															
															
															
															
													  var the_progress_bar_container = jQuery(the_container).find(".progress-bar");
											
													}
													
												
												
													}
													
												}
													
														var summary = data['#summary#'];
														pegasaas_accelerator_update_pending_benchmark_scans_chart(summary['total_pending_requests']);
													
													
													var pending_requests = jQuery(".benchmark-scan-in-progress").length;
													var requests_scanned = jQuery(".original-score-container").length - pending_requests;
													jQuery(".pegasaas-benchmark-requests-scanned").html(requests_scanned);
													
													
													
													
													if (pending_requests == 0) {
															clearInterval(benchmark_interval_timer);
															clearInterval(accelerated_pagespeed_interval_timer);
															accelerated_pagespeed_interval_timer =  setInterval(function() { pegasaas_accelerator_check_scores_2(); }, current_interval);	
												
															jQuery("#non-accelerated-prepping-container").css("display", "none");
															jQuery("#non-accelerated-chart-container").css("display", "inline-block");
														if (!jQuery("#pegasaas-accelerator-main-controls").hasClass("disabled")){
															//conlole.log("not disabled");
														
															jQuery(".benchmark-score-container").addClass("col-sm-5");
														jQuery(".benchmark-score-container").removeClass("text-center");
														jQuery(".benchmark-score-container").addClass("text-right");
														}
														
														//jQuery(".benchmark-score-container").removeClass("col-sm-5");
														//jQuery(".benchmark-score-container").addClass("col-sm-5");
														var score_pending_requests = jQuery(".rescan-pagespeed.scanning").length;
														if (score_pending_requests > 0) {
															jQuery("#accelerated-prepping-container").css("display", "inline-block");
														} 
														jQuery.post("admin.php?page=pegasaas-accelerator", 
																{ 'c': 'render-benchmark-chart', 'output': 'raw'}, 
																		function(data) {
															eval(data);
															
															//jQuery("#pegasaas-site-benchmark-speed-display").html(benchmark_score);
															jQuery(".primary-gauge-reference.mobile-only .pgr-l").html(benchmark_mobile_score);
															jQuery(".primary-gauge-reference.desktop-only .pgr-l").html(benchmark_desktop_score);
															
															var accelerated_mobile_score = parseInt(jQuery("#pegasaas-site-speed-display .mobile-only").html());
															var accelerated_desktop_score = parseInt(jQuery("#pegasaas-site-speed-display .desktop-only").html());
															var mobile_difference_icon = "";
															var desktop_difference_icon = "";
															
															var primary_gauge_difference_mobile = (accelerated_mobile_score - benchmark_mobile_score).toFixed(1);
															var primary_gauge_difference_desktop = (accelerated_desktop_score - benchmark_desktop_score).toFixed(1);
															
															if (primary_gauge_difference_mobile > 0) {
																mobile_difference_icon = '<i class="fa fa-long-arrow-up"></i> ';
															} else if (primary_gauge_difference_mobile < 0) {
																mobile_difference_icon = '<i class="fa fa-long-arrow-down"></i> ' ;
															} 
															
															if (accelerated_desktop_score > 0) {
																desktop_difference_icon = '<i class="fa fa-long-arrow-up"></i> ';
															} else if (accelerated_desktop_score < 0) {
																desktop_difference_icon = '<i class="fa fa-long-arrow-down"></i> ';
															} 															
															
															
															jQuery(".primary-gauge-reference.mobile-only .pgr-r").html(mobile_difference_icon + primary_gauge_difference_mobile);
															jQuery(".primary-gauge-reference.desktop-only .pgr-r").html(desktop_difference_icon + primary_gauge_difference_desktop);
														
															/*
															var non_accelerated_pagespeed_chart = new Chart(bctx, {
																type: 'doughnut',
																data: benchmark_data,
																options: benchmark_options
															});	
															*/
																
															
															jQuery("#pegasaas-benchmark-desktop-score-container .the-score").html(benchmark_desktop_score);
															
															jQuery("#pegasaas-benchmark-desktop-score-container").removeClass("score-success");
															jQuery("#pegasaas-benchmark-desktop-score-container").removeClass("score-warning");
															jQuery("#pegasaas-benchmark-desktop-score-container").removeClass("score-danger");
															jQuery("#pegasaas-benchmark-desktop-score-container").removeClass("score-success");
															jQuery("#pegasaas-benchmark-desktop-score-container").removeClass("score-warning");
															jQuery("#pegasaas-benchmark-desktop-score-container").removeClass("score-danger");
															if (benchmark_desktop_score >= 85) {
																jQuery("#pegasaas-benchmark-desktop-score-container").addClass("score-success");

															} else if (benchmark_desktop_score >= 75) {
																jQuery("#pegasaas-benchmark-desktop-score-container").addClass("score-warning");

															} else if (benchmark_desktop_score >= 0) {
																jQuery("#pegasaas-benchmark-desktop-score-container").addClass("score-danger");

															}  
															//console.log("benchmark mobile score: " + benchmark_mobile_score);
															
															if (benchmark_mobile_score >= 85) {
																jQuery("#pegasaas-benchmark-mobile-score-container").addClass("score-success");

															} else if (benchmark_mobile_score >= 75) {
																jQuery("#pegasaas-benchmark-mobile-score-container").addClass("score-warning");

															} else if (benchmark_mobile_score >= 0) {
																jQuery("#pegasaas-benchmark-mobile-score-container").addClass("score-danger");
															}  					
															
															jQuery("#pegasaas-benchmark-mobile-score-container .the-score").html(benchmark_mobile_score);															
															
															});
															
											

													} else {
														if (true) {
															jQuery.post("admin.php?page=pegasaas-accelerator", 
																{ 'c': 'push-scan', 
																  'via': 'pegasaas_accelerator_check_benchmark_scores',
																 'output': 'raw'}, 
																		function(data) {});
														}
													}
												
												}
												
											}, 
											"json");
}	
	
	
/* background interval timer routine */
		
function pegasaas_accelerator_check_optimization_status() {
	
	console.log("Checking Optimization Status...");
	var pending_requests = new Array();
	jQuery(".material-local-optimization-complete:not(.hidden)").each(function() {
		pending_requests.push(jQuery(this).attr("rel"));
		//console.log("x" + jQuery(this).attr("rel"));
	});
	jQuery(".material-check-pending:not(.hidden)").each(function() {
		pending_requests.push(jQuery(this).attr("rel"));
		//console.log("x" + jQuery(this).attr("rel"));
	});	
	
	
	
	
	if (pending_requests.length == 0) {
		clearInterval(background_interval_timer);	
		background_interval_timer = null;
	}
	manage_interval_time();

	jQuery.post(ajaxurl, 
											{ 'action': 'pegasaas_check_queued_optimization_requests', 'pending_requests': pending_requests, 'api_key': jQuery("#pegasaas-api-key").val()}, 
											function(data) {
												total_requests_this_page_load++;
												if (data['status'] == -1) {
													console.log("API Key Error in fetching new scores");
												} else {
													
													for (var resource_id in data) {
														if (resource_id != "#summary") {
														
														
															if (data[resource_id]['status'] != null) {
																var the_container = jQuery(".material-local-optimization-complete[rel='"+resource_id+"']");

																if (data[resource_id]['status'] == 0) {
																	jQuery(the_container).addClass("hidden");
																	jQuery(the_container).parents("button").find(".material-check-pending").removeClass("hidden");
																	jQuery(the_container).parents("button").find(".material-check").addClass("hidden");

																} else if (data[resource_id]['status'] == 1) {
																	jQuery(the_container).removeClass("hidden");
																	jQuery(the_container).parents("button").find(".material-check-pending").addClass("hidden");
																	jQuery(the_container).parents("button").find(".material-check").addClass("hidden");
																} else if (data[resource_id]['status'] == 2) {
																	jQuery(the_container).addClass("hidden");
																	jQuery(the_container).parents("button").find(".material-check-pending").addClass("hidden");
																	jQuery(the_container).parents("button").find(".material-check").removeClass("hidden");
																} 
																

																
																


															}
													
												
												
														}
													
													}
												}
		
												var pending_requests = jQuery(".material-local-optimization-complete:not(.hidden)").length +  jQuery(".material-check-pending:not(.hidden)").length;

												if (pending_requests == 0) {
													clearInterval(background_interval_timer);
													background_interval_timer = null;
													console.log("No more pending optimization requests.  Clearing interval timer.");
												}
												
												
											}, 
											"json");
}	
	
	
	
	
	
	
var accelerated_pagespeed_interval_timer = null;
var benchmark_interval_timer = null;
var background_interval_timer = null;		  

function start_interface_checks() {
	console.log("Interface checks starting.  Next check in " + current_interval/1000 + " seconds.");
	
	clearInterval(background_interval_timer);
	background_interval_timer = setInterval(function() {
		pegasaas_accelerator_check_optimization_status();
	}, current_short_interval);
	
	
	accelerated_pagespeed_interval_timer = setInterval(function() { pegasaas_accelerator_check_scores_2(); }, current_interval);	

	//if (state == "benchmarking") {
		//console.log("Starting Benchmarking Checker");
		 benchmark_interval_timer = setInterval(function() { pegasaas_accelerator_check_benchmark_scores(); }, current_interval);		
	//}
	
	
}
var interface_loaded = false;
jQuery(window).bind("load", function() {
	interface_loaded = true;
	<?php if (!PegasaasUtils::memory_within_limits()) {?>
	return;
	<?php } ?>
	
	var when_to_start_first_check = start_interval - current_interval;
	
	setTimeout(function() { start_interface_checks(); }, when_to_start_first_check);
	console.log("Interface checks start in " + when_to_start_first_check/1000 + " seconds.");
	
	
 	
	jQuery("[data-toggle='tooltip']").tooltip();
	jQuery("[rel='tooltip']").tooltip({container: 'body'});
	
	jQuery("[rel='tooltip-html']").tooltip({html: true, placement: 'bottom'});
	
	init_page_prioritization_bindings();
	/*
	jQuery(".needs").popover({placement: 'top', html: true, content: function() {
			var rule_id = jQuery(this).attr("rel");
		
			var html = jQuery(this).parents('td').find(".rule-details-" + rule_id).html();
	  		return html;	
		}
	});
	*/
});
	            
jQuery(".js-rotating").Morphext({
	animation: "fadeIn",
	speed: "2500",
    complete: function () {
                
    }
});
	
	
jQuery("#support-form input[name=agree]").click(function() {
		if (jQuery(this).is(":checked")) {
			jQuery(this).parents("form").find("button[type=submit]").removeAttr("disabled");
		} else {
			jQuery(this).parents("form").find("button[type=submit]").attr("disabled", "disabled");
			
		}
		
});
	

	
	jQuery(".purge-page-level-cpcss").click(function(e) {
		e.preventDefault();
		
		var resource_id 		= jQuery(this).attr("data-resource-id");
		var $this = this;

		jQuery($this).parents(".btn-group").find(".fa-check").addClass("hidden");
		jQuery($this).parents(".btn-group").find(".fa-hourglas-half").addClass("hidden");
		jQuery($this).parents(".btn-group").find(".fa-spinner").removeClass("hidden");

		jQuery.post(ajaxurl,
					{ 'action': 'pegasaas_purge_cpcss', 
					  'api_key': jQuery("#pegasaas-api-key").val(), 
					  'resource_id': resource_id},
					function(data) {
						jQuery($this).parents("ul").find("li").addClass("hidden");
						jQuery($this).parents("ul").find("li[data-state='always']").removeClass("hidden");
						jQuery($this).parents("ul").find("li[data-state='page-level-cpcss-missing']").removeClass("hidden");
			
						jQuery($this).parents(".btn-group").find(".fa-spinner").addClass("hidden");
						jQuery($this).parents(".btn-group").find(".fa-check").removeClass("hidden");
						
					}, "json");
	});	

	
	jQuery(".refresh-post-type-cpcss").click(function(e) {
		e.preventDefault();
		
		var resource_id 		= jQuery(this).attr("data-resource-id");
		var $this = this;

		jQuery($this).parents(".btn-group").find(".fa-check").addClass("hidden");
		jQuery($this).parents(".btn-group").find(".fa-hourglas-half").addClass("hidden");
		jQuery($this).parents(".btn-group").find(".fa-spinner").removeClass("hidden");

		jQuery.post(ajaxurl,
					{ 'action': 'pegasaas_recalculate_cpcss', 
					  'api_key': jQuery("#pegasaas-api-key").val(), 
					  'resource_id': resource_id},
					function(data) {
						//jQuery($this).parents("ul").find("li").addClass("hidden");
						//jQuery($this).parents("ul").find("li[data-state='always']").removeClass("hidden");
						jQuery($this).parents(".btn-group").find(".fa-spinner").addClass("hidden");
						jQuery($this).parents(".btn-group").find(".fa-check").removeClass("hidden");
					}, "json");
	});	
	
	jQuery(".refresh-page-level-cpcss, .build-page-level-cpcss").click(function(e) {
		e.preventDefault();
		
		var resource_id 		= jQuery(this).attr("data-resource-id");
		var $this = this;

		jQuery($this).parents(".btn-group").find(".fa-check").addClass("hidden");
		jQuery($this).parents(".btn-group").find(".fa-hourglas-half").addClass("hidden");
		jQuery($this).parents(".btn-group").find(".fa-spinner").removeClass("hidden");

		jQuery.post(ajaxurl,
					{ 'action': 'pegasaas_recalculate_cpcss', 
					  'api_key': jQuery("#pegasaas-api-key").val(), 
					  'resource_id': resource_id},
					function(data) {
						//jQuery($this).parents("ul").find("li").addClass("hidden");
						//jQuery($this).parents("ul").find("li[data-state='cache-missing']").removeClass("hidden");
						jQuery($this).parents(".btn-group").find(".fa-spinner").addClass("hidden");
						jQuery($this).parents(".btn-group").find(".fa-check").removeClass("hidden");
					}, "json");
	});	
	
	
	function re_hook_jquery_listeners_to_recommendations() {
	jQuery(".btn-help-request").click(function() {
		var this_page_issue_summary = jQuery(this).parents(".recommendations-container").find(".issue-summary").html();
		var this_page_url 			= jQuery(this).parents(".recommendations-container").find(".issue-url").html();
		var this_page_issue_description = jQuery(this).parents(".recommendations-container").find(".issue-description").html();
		
		jQuery("#support-form").find("#subject").val(this_page_issue_summary);
		jQuery("#support-form").find("#urls_with_issue").val(this_page_url);
		jQuery("#support-form").find("#description").val(this_page_issue_description);
		jQuery(this).parents("#pegasaas-accelerator-main-controls").find("#support-button").tab("show");
		jQuery(this).parents(".modal").modal("hide");
	});
		
		
		
	jQuery(".btn-enable-caching").click(function() {
		var active_button = this;
		
		jQuery.post(ajaxurl, 
																{ 'action': 'pegasaas_enable_feature', 'feature': 'page_caching'}, 
																		function(data) {
																			jQuery(active_button).find("span").html(" <i class='fa fa-check fa-green'></i>");
																			//alert(data);
		});
	});
	
	
	jQuery(".btn-enable-deferral-rbr").click(function() {
		var active_button = this;
		
		jQuery.post(ajaxurl, 
																{ 'action': 'pegasaas_enable_feature', 'feature': 'defer_render_blocking_js'}, 
																		function(data) {
			//alert(data);
																			jQuery(active_button).find("span").html(" <i class='fa fa-check fa-green'></i>");
																			//alert(data);
		});
		
		jQuery.post(ajaxurl, 
																{ 'action': 'pegasaas_enable_feature', 'feature': 'defer_render_blocking_css'}, 
																		function(data) {
																			jQuery(active_button).find("span").html(" <i class='fa fa-check fa-green'></i>");
																			//alert(data);
		});
	});	
	jQuery(".btn-cache").click(function(e) {e.preventDefault();});
	jQuery(".btn-cpcss").click(function(e) {e.preventDefault();});
	jQuery(".btn-enable-cpcss").click(function() {
		var active_button = this;
		
		jQuery.post(ajaxurl, 
																{ 'action': 'pegasaas_enable_feature', 'feature': 'inject_critical_css'}, 
																		function(data) {
			//alert(data);
																			jQuery(active_button).find("span").html(" <i class='fa fa-check fa-green'></i>");
																			//alert(data);
		});
		

	});	
	
	jQuery(".btn-rebuild-cpcss").click(function() {
		var active_button = this;
		var resource_id = jQuery(this).parents("td.areas-needing-attention").attr("rel");
		jQuery.post(ajaxurl, 
																{ 'action': 'pegasaas_recalculate_cpcss', 'resource_id': resource_id,
																'api_key': jQuery("#pegasaas-api-key").val()
																}, 
																		function(data) {
		//	alert(data);
																			jQuery(active_button).find("span").html(" <i class='fa fa-check fa-green'></i>");
																			//alert(data);
		});
		

	});	
	
	
	jQuery(".btn-rescan-all-pages").click(function() {
		if (confirm("This action will take some time as all pages will need to be re-built.  Are you sure you wish to proceed?")) {
			jQuery(this).find("span").html("<i class='fa fa-spinner fa-spin fa-green'></i>");
			jQuery(this).parents(".pagespeed-scores").find("form.rescan-pagespeed").submit();
			jQuery(this).parents(".modal").modal("hide");
		}
	});
	
	jQuery(".btn-rescan-page").click(function() {
			jQuery(this).find("span").html("<i class='fa fa-spinner fa-spin fa-green'></i>");
			jQuery(this).parents("tr").find("form.rescan-pagespeed").submit();
			jQuery(this).parents(".modal").modal("hide");
		});	
		
		
	}
	
	re_hook_jquery_listeners_to_recommendations();
	
	jQuery(document).ready(function() {
		
		var popover_options = { 'title': 'What Is This?', 
						    'trigger': 'click', 
						    'placement': 'top', 
						    'html': true, 
						    'template': '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
						    };
	
		
		jQuery('[data-toggle="popover"]').popover();
		
	});
	
	

	
	jQuery("form.feature-switch .js-switch").bind("change", function(e) {
		
		if (jQuery(this).parents(".pegasaas-feature-box").hasClass("locked-for-novice") && jQuery(".pegasaas-dashboard").hasClass("interface-novice")) {
			e.preventDefault();
			jQuery("#novice-mode-restriction").modal('show');
			//alert("For your safety, the ability to change this feature is disabled in novice mode.");
		
			return false;
		} else if (jQuery(this).parents(".feature-container").hasClass("locked-for-novice") && jQuery(".pegasaas-dashboard").hasClass("interface-novice")) {
			e.preventDefault();
			jQuery("#novice-mode-restriction").modal('show');
			//alert("For your safety, the ability to change this feature is disabled in novice mode.");
		
			return false;
		
		}
		
		if (jQuery(this).parents(".pegasaas-feature-box").hasClass("premium-feature")) {
			e.preventDefault();
			
			jQuery("#upgrade-for-premium-feature").modal('show');
			//alert("For your safety, the ability to change this feature is disabled in novice mode.");
		
			return false;
		} else if (jQuery(this).parents(".feature-container").hasClass("premium-feature")) {
			e.preventDefault();
			
			jQuery("#upgrade-for-premium-feature").modal('show');
			//alert("For your safety, the ability to change this feature is disabled in novice mode.");
		
			return false;
		}
		
		if (jQuery(this).parents(".pegasaas-feature-box:not(.pegasaas-feature-section-container)").hasClass("feature-disabled")) {
			jQuery(this).parents(".pegasaas-feature-box:not(.pegasaas-feature-section-container)").removeClass("feature-disabled");
		} else {
			jQuery(this).parents(".pegasaas-feature-box:not(.pegasaas-feature-section-container)").addClass("feature-disabled");
		}
		
		if (jQuery(this).parents(".feature-container").hasClass("feature-disabled")) {
			jQuery(this).parents(".feature-container").removeClass("feature-disabled");
		} else {
			jQuery(this).parents(".feature-container").addClass("feature-disabled");
		}


		
		jQuery(this).parents("form.feature-switch").submit();
		console.log("Switch State: " + jQuery(this).parents("form.feature-switch").find("input[name='c']").val());
		if (jQuery(this).parents(".pegasaas-feature-box:not(.pegasaas-feature-section-container)").hasClass("feature-disabled")) {
			console.log("Setting State To enabled");
			jQuery(this).parents("form.feature-switch").find("input[name='c']").val("enable-feature");
		} else if (jQuery(this).parents(".feature-container").hasClass("feature-disabled")) {
			console.log("Setting State To enabled");
			jQuery(this).parents("form.feature-switch").find("input[name='c']").val("enable-feature");
		} else {
			console.log("Setting State To disabled");
			jQuery(this).parents("form.feature-switch").find("input[name='c']").val("disable-feature");

		}
	});
	
	jQuery("form.feature-toggle-switch .js-switch").bind("change", function(e) {
		
		if (jQuery(this).parents("li").hasClass("feature-disabled")) {
			jQuery(this).parents("li").removeClass("feature-disabled");
	
		} else {
			jQuery(this).parents("li").addClass("feature-disabled");
		}

		if (jQuery(this).parents(".feature-row").hasClass("feature-disabled")) {
				
			jQuery(this).parents(".feature-row").removeClass("feature-disabled");

		} else {
				
			jQuery(this).parents(".feature-row").addClass("feature-disabled");
		}	
		var the_form = jQuery(this).parents("form.feature-toggle-switch");
		submit_via_ajax(the_form, "pegasaas_dashboard_settings_update");
		
		//the_form.submit();
		
	});	
	

	

	

		  
		  
		  
	jQuery("#pegasaas-accelerator-main-buttons a[data-toggle='tab']").on("click", function(e) {
		jQuery('body,html').animate({
			scrollTop: jQuery(this).offset().top - 50
		 	}, 1000
		);
	}	);
	
	jQuery("#boost-more").on("click", function(e) {
		console.log("yes");
		jQuery("a#tools-button").trigger("click");
	}	);
	
	

	
	jQuery(".initialization-sequence .vertical-progress").click(function() {
		document.location.href=document.location.href + "&skip_prep=1";

	});
	
	
	
	
	
function toggle_display_mode(element) {
	jQuery(element).parents("ul").find("li").removeClass("selected_interface_theme");
	jQuery(element).parents("li").addClass("selected_interface_theme");
	if (jQuery(element).val() == 0) {
		jQuery("#pegasaas-accelerator-admin-styles-light-css").remove();
		jQuery("#pegasaas-accelerator-admin-styles-plain-css").remove();
		jQuery("#plugin-logo").attr("src", "<?php echo PEGASAAS_ACCELERATOR_URL ?>assets/images/pegasaas-accelerator-horizontal-logo-light.png");
		jQuery("#pegasaas-setting-icon-display_mode").removeClass("fa-sun-o");
		jQuery("#pegasaas-setting-icon-display_mode").removeClass("fa-circle-thin");
		jQuery("#pegasaas-setting-icon-display_mode").addClass("fa-moon-o");
		

	} else if (jQuery(element).val() == 1) {
		jQuery("#pegasaas-accelerator-admin-styles-plain-css").remove();

		jQuery("head").append("<link id='pegasaas-accelerator-admin-styles-light-css' rel='stylesheet' href='<?php echo PEGASAAS_ACCELERATOR_URL ?>/assets/css/light.css' type='text/css'>");
		jQuery("#plugin-logo").attr("src",  "<?php echo PEGASAAS_ACCELERATOR_URL ?>assets/images/pegasaas-accelerator-horizontal-logo-light.png");
		jQuery("#pegasaas-setting-icon-display_mode").addClass("fa-sun-o");
		jQuery("#pegasaas-setting-icon-display_mode").removeClass("fa-moon-o");
		jQuery("#pegasaas-setting-icon-display_mode").removeClass("fa-circle-thin");
	} else {
		jQuery("#pegasaas-accelerator-admin-styles-light-css").remove();

		jQuery("head").append("<link id='pegasaas-accelerator-admin-styles-plain-css' rel='stylesheet' href='<?php echo PEGASAAS_ACCELERATOR_URL ?>/assets/css/plain.css' type='text/css'>");
		jQuery("#plugin-logo").attr("src", "<?php echo PEGASAAS_ACCELERATOR_URL ?>assets/images/pegasaas-accelerator-horizontal-logo-dark.png");
		jQuery("#pegasaas-setting-icon-display_mode").addClass("fa-circle-thin");
		jQuery("#pegasaas-setting-icon-display_mode").removeClass("fa-moon-o");
		jQuery("#pegasaas-setting-icon-display_mode").removeClass("fa-sun-o");
	}
	element.form.submit();
}	
	
	
function toggle_speed_configuration(element) {
	
	<?php if (strstr(PegasaasAccelerator::$settings['subscription'], "guest") ) { ?>
	if (jQuery(element).val() == 4) {
		
		alert("BEAST MODE is only available to PREMIUM subscriptions.  Please choose a different 'auto-pilot' setting or go to pegasaas.com/signup to get a PREMIUM api key.");
		return false;
	}
	<?php } ?> 
	
	jQuery(element).parents("ul").find("li").removeClass("selected_speed_configuration");
	jQuery(element).parents("li").addClass("selected_speed_configuration");

	
	jQuery(".pegasaas-dashboard").removeClass("speed-configuration-auto");
	jQuery(".pegasaas-dashboard").removeClass("speed-configuration-manual");
	jQuery(".pegasaas-dashboard").removeClass("speed-configuration-basic");
	jQuery(".pegasaas-dashboard").removeClass("speed-configuration-supersonic");
	jQuery(".pegasaas-dashboard").removeClass("speed-configuration-hypersonic");
	jQuery(".pegasaas-dashboard").removeClass("speed-configuration-beastmode");
	
	

		
	
	if (jQuery(element).val() == 0) {
		jQuery(".pegasaas-dashboard").addClass("speed-configuration-manual");
			
	} else if (jQuery(element).val() == 1) {
		jQuery(".pegasaas-dashboard").addClass("speed-configuration-auto");
		jQuery(".pegasaas-dashboard").addClass("speed-configuration-basic");

	} else if (jQuery(element).val() == 2) {
		jQuery(".pegasaas-dashboard").addClass("speed-configuration-auto");
		jQuery(".pegasaas-dashboard").addClass("speed-configuration-supersonic");

	} else if (jQuery(element).val() == 3) {
		jQuery(".pegasaas-dashboard").addClass("speed-configuration-auto");
		jQuery(".pegasaas-dashboard").addClass("speed-configuration-hypersonic");

	} else if (jQuery(element).val() == 4) {
		<?php if (strstr($pegasaas->settings['subscription'], "guest") ) { ?>
		alert("BEAST MODE is only available to PREMIUM subscriptions.  Please choose a different 'auto-pilot' setting or go to pegasaas.com/signup to get a PREMIUM api key.");
		return;
	<?php } else { ?>
		jQuery(".pegasaas-dashboard").addClass("speed-configuration-auto");
		jQuery(".pegasaas-dashboard").addClass("speed-configuration-beastmode");

	<?php } ?>
	} 
	
	jQuery(element.form).submit();
	
	return true;
}		
	
function toggle_display_level(element) {
	jQuery(element).parents("ul").find("li").removeClass("selected_interface_experience");
	jQuery(element).parents("li").addClass("selected_interface_experience");
	jQuery(element).parents(".setup-content").find(".experience-level-description").removeClass("visible");

			jQuery(".pegasaas-dashboard").removeClass("interface-advanced");
		jQuery(".pegasaas-dashboard").removeClass("interface-intermediate");
				jQuery(".pegasaas-dashboard").removeClass("interface-novice");

	if (jQuery(element).val() == 0) {

		jQuery(".pegasaas-dashboard").addClass("interface-novice");

		jQuery(".pegasaas-feature-box.locked-for-novice .js-switch, .feature-container.locked-for-novice .js-switch").each(function() {
			
			jQuery(this).data("sw").disable();

		});

				
	} else if (jQuery(element).val() == 1) {
		jQuery(".pegasaas-dashboard").addClass("interface-intermediate");
		jQuery(".pegasaas-feature-box.locked-for-novice .js-switch, .feature-container.locked-for-novice .js-switch").each(function() {
			jQuery(this).data("sw").enable();

		});

	} else {
	
		jQuery(".pegasaas-dashboard").addClass("interface-advanced");
		jQuery(".pegasaas-feature-box.locked-for-novice .js-switch, .pegasaas-feature-box.locked-for-novice .js-switch").each(function() {
			jQuery(this).data("sw").enable();

		});		
	}
	element.form.submit();
}
	
function toggle_coverage_level(element) {

	jQuery(element).parents("ul").find("li").removeClass("selected_coverage_level");
	jQuery(element).parents("li").addClass("selected_coverage_level");
	jQuery(element).parents(".pegasaas-accelerator-subsystem-feature-description").find(".optimization-coverage-config-box").removeClass("has-extended");

	if (jQuery(element).val() == 1) {
		jQuery(element).parents(".pegasaas-accelerator-subsystem-feature-description").find(".optimization-coverage-config-box").addClass("has-extended");


	} 
	
	element.form.submit();
}	
	
function toggle_coverage_checkbox(element) {
	if (jQuery(element).is(":checked")) {
		jQuery(element).parents("form").find("input[name='s']").val("1");
	} else {
		jQuery(element).parents("form").find("input[name='s']").val("0");
	}
}
	

function toggle_action_type_checkbox(element) {
	if (jQuery(element).is(":checked")) {
		jQuery(element).parents("form").find("input[name='s']").val("1");
	} else {
		var checked_elements = jQuery(element).parents("ul").find("input[type=checkbox]:checked");
		if (checked_elements.length == 0) {
			alert("You must choose at least one action");
			jQuery(element).attr("checked", true);
			return false;
		}
		
		jQuery(element).parents("form").find("input[name='s']").val("0");
		
	}
	return true;
}
 

	
	
	
	
jQuery(window).on("load", function() {

	jQuery("#pegasaas-dashboard-container").addClass("material-icons-loaded");
});	

function toggle_advanced_web_perf_display_change(element) {
	var rel = element.getAttribute("rel");
	if (jQuery(element).is(":checked")) {
		jQuery("[data-display-element='" + rel +"']").removeClass("hidden-advanced");
		setCookie("pegasaas_web_perf_metrics_" + rel, 1, 365);
		window["web_perf_metrics_" + rel + "_class"] = 'hidden-advanced';

	} else {
		jQuery("[data-display-element='" + rel +"']").addClass("hidden-advanced");
		setCookie("pegasaas_web_perf_metrics_" + rel, 0, 365);
				window["web_perf_metrics_" + rel + "_class"] = '';


	}
	set_visible_advanced_gauges();
}
	function set_visible_advanced_gauges() {
		var gauges = jQuery("#web-performance-metrics .col-sm-gauge:visible").length;
		if (gauges == 8) {
		  	jQuery("#web-performance-metrics").addClass("visible-gauges-8");
			jQuery("#web-performance-metrics").removeClass("visible-gauges-7");
		} else if (gauges == 7) {
			
		  	jQuery("#web-performance-metrics").addClass("visible-gauges-7");
			jQuery("#web-performance-metrics").removeClass("visible-gauges-8");

		} else {
			jQuery("#web-performance-metrics").removeClass("visible-gauges-7");
			jQuery("#web-performance-metrics").removeClass("visible-gauges-8");
		}
		
	}
	
	function is_prepping_done() {
			if (time_since_last_prepping_check >= 15) {
				time_since_last_prepping_check = 0;
				jQuery.post(ajaxurl, 
						{ 'action': 'pegasaas_is_prepping_done', 
						  'api_key': '<?php echo PegasaasAccelerator::$settings['api_key']; ?>'
						}, 
						function(data) {
					console.log("checking prepping: " + data);
							installation_status = data;
							api_key_valid = true;
							if (data == 3) {
								prepping_done = true;
							installation_hung = false;
							} else if (data == -1) {
								prepping_done = false;
								installation_hung = true;
							} else if (data == -2) {
								prepping_done = false;
								api_key_valid = false;
							} else {
								if (data == 2) {
									installation_hung = false;
									optimizations_complete = true;
								} else if (data == 1) {
									installation_hung = false;
									optimizations_queued = true;
								}
								prepping_done = false;
					 			if (time_since_last_nudge >= 30) {
					 				nudge_server();
								} 
					
					
							}
						});
			}
		}
		function nudge_server() {
			jQuery.post("admin.php?page=pegasaas-accelerator&action=nudge", 
						{ 'output': 'raw'}, 
						function(data) { console.log("Pegasaas: Nudged Server"); });
			
			time_since_last_nudge = 0;
		}
	<?php if ($prepping) { ?>
		var prepping_done = false;
	    var installation_hung = false;
	    var optimizations_queued = false;
	    var optimizations_complete = false;
		var time_since_last_nudge = 60;
		var time_since_last_prepping_check = 30;
		var api_key_valid = true;
		var installation_status = true;
	var delay = 5000;
	
	function update_initialization_sequence() {
		
		if (!prepping_done) { 
			is_prepping_done();
		}
		
		
		
					

		if (prepping_done) { 
		

		}
		
	
		time_since_last_nudge = time_since_last_nudge + (delay / 1000);
		time_since_last_prepping_check = time_since_last_prepping_check + (delay / 1000);
		
		if (prepping_done) {
			
			console.log("Pegasaas: Prepping Complete");
			document.location.href=document.location.href + "";
			//render_accelerated_chart();

		} else if (installation_hung) {
			console.log("Pegasaas: Installation Possibly Hung");
		} else {
			console.log("Pegasaas: Prepping Still In Progress");
			setTimeout("update_initialization_sequence()", delay);
		}
		
		
		
		
		
	}
	
	
	
		nudge_server();
		setTimeout("update_initialization_sequence()", 5000);

	<?php } ?>
	

	
	
</script>
