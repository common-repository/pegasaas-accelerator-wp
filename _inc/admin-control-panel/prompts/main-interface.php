<?php $pegasaas->utils->console_log("Main Interface 1"); ?>
<?php if ($pegasaas->interface->take_tour()) { ?><script>
	var current_tour_step = <?php echo $pegasaas->interface->get_current_tour_step(); ?>;
	var max_tour_step = <?php echo $pegasaas->interface->get_tour_last_step(); ?>;
	jQuery(document).ready(function() {
		setTimeout('start_welcome_tour()', 500);
		//jQuery("#welcome-tour").modal("show");
	});
	function start_welcome_tour() {
		jQuery("#welcome-tour").modal("show");
	}
	jQuery("#welcome-clear").click(function() {
		setCookie("pegasaas_tour_steps_completed", max_tour_step, 365);
		jQuery("#welcome-tour").modal("hide");
	})
</script>
<?php } 



?>
<?php $pegasaas->utils->console_log("Main Interface 21"); ?>
<div style='clear:both;'></div>
 <div id="pegasaas-accelerator-main-controls" <?php if (PegasaasAccelerator::$settings['status'] < 1) { ?>class='pegasaas-disabled'<?php } ?>>

    <div id="pegasaas-accelerator-main-buttons-container" style='position: relative'>

	
		
	  <div id="pegasaas-accelerator-main-buttons">
	    <ul class="nav nav-tabs" role="tablist">
		<?php if (PegasaasAccelerator::$settings['status'] > 0 && !$pegasaas->interface->dashboard_scores_restricted() && !$skip_to_support) { ?>
			<li role="presentation"<?php if ($page_view == "") { ?> class="active"<?php } ?>><a aria-controls="dashboard" role="tab" data-toggle="tab" href="#pegasaas-accelerator-main-dashboard"><?php _e("Page Scores", "pegasaas-accelerator"); ?></a></li>
		<?php } ?>
			<?php if (!$pegasaas->interface->global_settings_page_restricted() && !$skip_to_support) { ?>
			<li role="presentation"<?php if ($page_view == "settings") { ?> class="active"<?php } ?> id='settings-nav-button'><a aria-controls="settings" role="tab" data-toggle="tab" href="#pegasaas-accelerator-main-settings"><?php _e("Settings", "pegasaas-accelerator"); ?></a></li>
			<?php } ?>
			<?php if (PegasaasAccelerator::$settings['settings']['white_label']['status'] != 1) { ?>
			  <li role="presentation"<?php if ($skip_to_support || $page_view == "support" || (PegasaasAccelerator::$settings['status'] == 0 && $page_view == "")) { ?> class="active"<?php } ?>><a  id="support-button" aria-controls="support" role="tab" data-toggle="tab" href="#pegasaas-accelerator-support"><?php _e("Support", "pegasaas-accelerator"); ?></a></li>	
			<?php } ?>
		<!--
			<li role="presentation"<?php if ($page_view == "account") { ?> class="active"<?php } ?>><a aria-controls="account" role="tab" data-toggle="tab" href="#pegasaas-accelerator-main-account">
			<?php if (PegasaasAccelerator::$settings['settings']['white_label'] == 1) { ?><?php _e("API Access", "pegasaas-accelerator"); ?><?php } else { ?><?php _e("Account", "pegasaas-accelerator"); ?><?php } ?></a></li>-->
			<?php if (!$skip_to_support && PegasaasAccelerator::$settings['settings']['white_label'] != 1) { ?>
			  <li role="presentation" <?php if ($page_view == "faqs") { ?> class="active"<?php } ?>><a  id="faqs-button" aria-controls="faqs" role="tab" data-toggle="tab" href="#pegasaas-accelerator-faqs"><?php _e("FAQs", "pegasaas-accelerator"); ?></a></li>	
			  <li role="presentation" <?php if ($page_view == "troubleshooting") { ?> class="active"<?php } ?>><a  id="troubleshooting-button" aria-controls="troubleshooting" role="tab" data-toggle="tab" href="#pegasaas-accelerator-troubleshooting"><?php _e("Troubleshooting", "pegasaas-accelerator"); ?></a></li>	
			  <li role="presentation" <?php if ($page_view == "tools") { ?> class="active"<?php } ?>><a  id="tools-button" aria-controls="tools" role="tab" data-toggle="tab" href="#pegasaas-accelerator-tools"><?php _e("Tools", "pegasaas-accelerator"); ?></a></li>	
			<!--
			<li role="presentation" <?php if ($page_view == "changelog") { ?> class="active"<?php } else { ?>class=' hidden-novice'<?php } ?>><a  id="changelog-button" aria-controls="changelog" role="tab" data-toggle="tab" href="#pegasaas-accelerator-changelog"><?php _e("Changelog", "pegasaas-accelerator"); ?></a></li>	
			-->
			<?php if (!isset(PegasaasAccelerator::$settings['settings']['cdn']) || PegasaasAccelerator::$settings['settings']['cdn']['status'] == 0) { ?>
			<li role="presentation" <?php if ($page_view == "cache") { ?> class="active"<?php } else { ?>class=' '<?php } ?>><a id="cache-button" aria-controls="cache" role="tab" data-toggle="tab" href="#pegasaas-accelerator-cache"><?php _e("Cache", "pegasaas-accelerator"); ?></a></li>	
			<?php } else { ?>
			<li role="presentation" <?php if ($page_view == "cache") { ?> class="active"<?php } else { ?>class=' '<?php } ?>><a id="cache-button" aria-controls="cache" role="tab" data-toggle="tab" href="#pegasaas-accelerator-cache"><?php _e("Cache", "pegasaas-accelerator"); ?></a></li>				
			<?php } ?>
			<li id="advanced-tab" class='hidden-novice hidden-intermediate' role="presentation"<?php if ($page_view == "advanced") { ?> class="active"<?php } ?>><a aria-controls="advanced" role="tab" data-toggle="tab" href="#pegasaas-accelerator-advanced"><?php _e("Advanced", "pegasaas-accelerator"); ?></a></li>
			<?php } ?>
		 </ul>
	  </div>
	</div>
<?php $pegasaas->utils->console_log("Main Interface 58"); ?>
	 
	<div id='pegasaas-accelerator-main-content-container' class='tab-content'>
		<?php if ($pegasaas->cache->cloudflare_exists() && !$pegasaas->cache->cloudflare_credentials_valid()) { ?>
		<div class='pegasaas-warning pegasaas-warning-cloudflare'>
			<i class='<?php echo PEGASAAS_CLOUDFLARE_ICON_CLASS; ?>'></i>
			To allow Pegasaas Accelerator to automatically clear related page and resource caches when you update content, 
			you will need to configure your Cloudflare settings on the settings tab.
		</div>
		<?php } ?>
<?php $pegasaas->utils->console_log("Main Interface 68"); ?>
		
		<?php if (!PegasaasUtils::memory_within_limits()) { ?>
		<div class='pegasaas-warning'>
			<i class='<?php echo PEGASAAS_MEMORY_WARNING_ICON_CLASS; ?>'></i>
			Your PHP memory usage is nearing the defined upper limit of <?php echo ini_get('memory_limit'); ?>.  Please increase the 'memory_limit' setting in your PHP settings to <?php echo PegasaasUtils::get_next_memory_limit(); ?>.  All further optimization tasks have been put on hold until this limit can be increased.
		</div>
		<?php } ?>
		
		<?php if ($pegasaas->cache->pantheon_exists()  && !$pegasaas->utils->does_plugin_exists_and_active("pantheon-advanced-page-cache")) { ?>
		<div class='pegasaas-warning'>
			<i class='<?php echo PEGASAAS_MEMORY_WARNING_ICON_CLASS; ?>'></i>
			To allow Pegasaas Accelerator to automatically clear related page caches when you update content, you will need to Install and Activate the <i>Pantheon Advanced Page Caching</i> plugin.  Install <a href='plugin-install.php?s=pantheon+advanced+page+cache&tab=search&type=term&action=pantheon-load-infobox'>Pantheon Advanced Page Cache</a>.
		</div>
		
		<?php } ?>
		
	<?php $pegasaas->utils->console_log("Main Interface 85"); ?>	
		
		<?php if (PegasaasAccelerator::$settings['status'] > 0 && !$pegasaas->interface->dashboard_scores_restricted() && !$skip_to_support) { ?>
		<div role="tabpanel" class="tab-pane fade  <?php if ($page_view == "") { ?> active<?php } ?>" id="pegasaas-accelerator-main-dashboard">
			<?php include PEGASAAS_ACCELERATOR_DIR."_inc/admin-control-panel/overview.php"; ?>	 
		</div>
		<?php } else { ?>
		<?php } ?>
		<?php $pegasaas->utils->console_log("Main Interface 96"); ?>	
		<div role="tabpanel" class="tab-pane fade <?php if ($page_view == "advanced") { ?> in active<?php } ?>" id="pegasaas-accelerator-advanced">
			<?php include PEGASAAS_ACCELERATOR_DIR."_inc/admin-control-panel/advanced.php"; ?>
		</div>
		<?php $pegasaas->utils->console_log("Main Interface 99"); ?>	
		<div role="tabpanel" class="tab-pane fade <?php if ($page_view == "settings") { ?> in active<?php } ?>" id="pegasaas-accelerator-main-settings">
			<?php include PEGASAAS_ACCELERATOR_DIR."_inc/admin-control-panel/settings.php"; ?>
		</div>
		<!--
		<div role="tabpanel" class="tab-pane fade <?php if ($page_view == "account") { ?> in active<?php } ?>" id="pegasaas-accelerator-main-account">
			<?php //include PEGASAAS_ACCELERATOR_DIR."_inc/admin-control-panel/account.php"; ?>
		</div>
-->
		<?php $pegasaas->utils->console_log("Main Interface 107"); ?>	
		<div role="tabpanel" class="tab-pane fade <?php if ($page_view == "faqs") { ?> in active<?php } ?>" id="pegasaas-accelerator-faqs">
			<?php include PEGASAAS_ACCELERATOR_DIR."_inc/admin-control-panel/faqs.php"; ?>
		</div>
		<?php $pegasaas->utils->console_log("Main Interface 110"); ?>	
		<div role="tabpanel" class="tab-pane fade <?php if ($page_view == "troubleshooting") { ?> in active<?php } ?>" id="pegasaas-accelerator-troubleshooting">
			<?php include PEGASAAS_ACCELERATOR_DIR."_inc/admin-control-panel/troubleshooting.php"; ?>
		</div>
		<?php $pegasaas->utils->console_log("Main Interface 114"); ?>	
		<div role="tabpanel" class="tab-pane fade <?php if ($page_view == "tools") { ?> in active<?php } ?>" id="pegasaas-accelerator-tools">
			<?php include PEGASAAS_ACCELERATOR_DIR."_inc/admin-control-panel/tools.php"; ?>
		</div>
		<?php $pegasaas->utils->console_log("Main Interface 118"); ?>	
		<div role="tabpanel" class="tab-pane fade <?php if ($page_view == "changelog") { ?> in active<?php } ?>" id="pegasaas-accelerator-changelog">
			<?php // include PEGASAAS_ACCELERATOR_DIR."_inc/admin-control-panel/changelog.php"; ?>
		</div>		
		<?php $pegasaas->utils->console_log("Main Interface 122"); ?>	
		<?php if (!isset(PegasaasAccelerator::$settings['settings']['cdn']) || PegasaasAccelerator::$settings['settings']['cdn']['status'] == 0) { ?>
		<div role="tabpanel" class="tab-pane fade <?php if ($page_view == "cache") { ?> in active<?php } ?>" id="pegasaas-accelerator-cache">
			<?php include PEGASAAS_ACCELERATOR_DIR."_inc/admin-control-panel/cache.php"; ?>
		</div>		
		<?php } else { ?>
		<div role="tabpanel" class="tab-pane fade <?php if ($page_view == "cache") { ?> in active<?php } ?>" id="pegasaas-accelerator-cache">
			<?php include PEGASAAS_ACCELERATOR_DIR."_inc/admin-control-panel/cdn.php"; ?>
		</div>	
		<?php } ?>
		<?php $pegasaas->utils->console_log("Main Interface 132"); ?>	
		<div role="tabpanel" class="tab-pane fade <?php if ($skip_to_support || $page_view == "support" || (PegasaasAccelerator::$settings['status'] == 0 && $page_view == "")) { ?> in active<?php } ?>" id="pegasaas-accelerator-support">
			<?php include PEGASAAS_ACCELERATOR_DIR."_inc/admin-control-panel/support.php"; ?>
		</div>	
		
	 </div>
</div>
	
  

	  
