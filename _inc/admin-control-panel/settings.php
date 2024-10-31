<?php include "settings/icon-config.php"; ?>
<?php include "settings/feature-config.php"; ?>

<div class='subscribed-features settings-container'>
			<div class='quicklinks-column' id="quicklinks" >
			<h4 style='margin-top: 0px;'>Quicklinks</h4>	
				
	<ul  class='nav'>
	    <li class='active'><a href='#settings-section-account'><i class='material-icons'>account_circle</i> Account</a></li>
		<li><a href='#settings-section-general'><i class='material-icons'>explore</i> General</a></li>
		<li class='manual-only-configuration'><a href='#settings-section-server-response-time'><i class='material-icons'>access_time</i> Server Response Time</a></li>
		<li class='manual-only-configuration'><a href='#settings-section-html-resource-optimization'><i class='material-icons'>task</i> HTML Resource Optimization</a></li>
		<li class='manual-only-configuration'><a href='#settings-section-image-resource-optimization'><i class='material-icons'>crop_original</i> Image Resource Optimization</a></li>
		<li class='manual-only-configuration'><a href='#settings-section-javascript-resource-delivery'><i class='material-icons'>receipt</i> Javascript Resource Delivery</a></li>
		<li class='manual-only-configuration'><a href='#settings-section-css-resource-delivery'><i class='material-icons'>style</i> CSS Resource Delivery</a></li>
		<li class='manual-only-configuration'><a href='#settings-section-font-resource-delivery'><i class='material-icons'>font_download</i> Font Resource Delivery</a></li>
		<li class='manual-only-configuration'><a href='#settings-section-misc-resource-delivery'><i class='material-icons'>downloading</i> Misc Resource Delivery</a></li>
		<li class='manual-only-configuration'><a href='#settings-section-lazy-loading'><i class='material-icons'>timelapse</i> Lazy Loading</a></li>
		<li><a href='#settings-section-compatibility'><i class='material-icons'>assistant</i> Compatibility</a></li>
		<li><a href='#settings-section-miscellaneous'><i class='material-icons'>tune</i> Miscellaneous</a></li>
				</ul>

	  
				<div class='pegasaas-feature-section-container'>
					<h4>Legend</h4>
			<div class="list-group">
						  <div class="list-group-item foundation-feature">Global Feature</div>
						  <div class="list-group-item api-feature">API Only Feature</div>
						  <div class="list-group-item new-feature">New Feature</div>
						  <div class="list-group-item recent-feature">Recent Feature</div>
				</div>	
					</div>
	</div>

			<div class='main-column'>
				<?php
				
			if (is_array($features)) {
				$tab_active = true; 
				?>
				<style>
	
				

				</style>
	
				<div class="tab-content">
					<div class='pegasaas-feature-section-container'>
						<h3 id='settings-section-account' class='pegasaas-subsystem-title'>Account</h3>
						<p class='section-description'>Your Pegasaas account is your key to a personalized experience with all Pegasaas products and services.</p>
						<div class="list-group">
						  <div  class="list-group-item">
							<div class='gravatar-container'>
			<?php 
			$email = PegasaasAccelerator::$settings['account']['email_address'];
			$default = "mm";
			$size = 45;

			$grav_url = "https://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;
			$plugin_info = get_plugin_data($pegasaas->get_plugin_file());

	 		
			?>
			<img src="<?php echo $grav_url; ?>" alt="" />
			</div>
							  <div class='account-info'>
			  <ul>
				<?php if (!$pegasaas->is_free()) { ?>
			    <li><?php echo PegasaasAccelerator::$settings['account']['first_name']." ".PegasaasAccelerator::$settings['account']['last_name']; ?></li>
			    <?php } else { ?>
				  <li> <a style='float: right; margin-top: 5px; ' class='btn btn-success btn-sm' target="_blank" href='https://pegasaas.com/upgrade/?utm_source=pegasaas-accelerator-account-panel&upgrade_key=<?php echo PegasaasAccelerator::$settings['api_key']; ?>-<?php echo PegasaasAccelerator::$settings['installation_id']; ?>&site=<?php echo $pegasaas->utils->get_http_host(); ?>&current=<?php echo PegasaasAccelerator::$settings['subscription']; ?>'><?php echo __("Upgrade", "pegasaas-accelerator"); ?></a>	
					  Guest API Account</li>
				  <?php } ?>
				 <li><?php echo PegasaasAccelerator::$settings['account']['email_address']; ?></li>
								  </ul>
							  </div>
							  
							  
						  </div>
						  <a href="https://pegasaas.com/manage/" target="_blank" class="list-group-item">Manage your Pegasaas Account <i class='fa fa-external-link fa-right'></i></a>
						  <div  class="list-group-item">API Key  
							  <form class='settings-api-key-form' id="pegasaas-api-key-form" action="admin.php?page=pegasaas-accelerator">
								  <input id="pegasaas-api-key" type="text" value="<?php echo PegasaasAccelerator::$settings['api_key']; ?>" placeholder="API Key"> 
								  <div id="api-key-status" class='api-key-status'><?php if (PegasaasAccelerator::$settings['status'] == "2" || PegasaasAccelerator::$settings['status'] == "1" || PegasaasAccelerator::$settings['status'] == "0") { ?><span class="dashicons dashicons-yes"></span><?php } ?></div>
								  <input class='update-button btn btn-primary btn-sm' type='submit' value='Update' /></form>
							</div>
						  <div class='list-group-item'><a href="#feature-box-subscription-info" data-toggle="collapse" >Subscription
							<i class='fa fa-fw fa-angle-down fa-right'></i>
							</a>
							  
							  <div class='collapse' id='feature-box-subscription-info'>
								  
							    <ul>
											    <li><label>Installation ID:</label><?php echo PegasaasAccelerator::$settings['installation_id']; ?></li>
				<li><label>Version:</label><?php echo $plugin_info['Version']; ?></li>
				<li><label>Response Rate:</label><?php echo ucwords(str_replace("-normal", "", PegasaasAccelerator::$settings['settings']['response_rate']['status'])); ?></li>
				<li class='divider'></li>
				  <li><label>Subscription:</label><?php echo PegasaasAccelerator::$settings['subscription_name']; ?> 
					 
					 &nbsp; <a style='margin-top: -5px; margin-bottom:- 5px;' class='btn btn-default btn-xs' target="_blank" href='https://pegasaas.com/upgrade/?utm_source=pegasaas-accelerator-account-panel&upgrade_key=<?php echo PegasaasAccelerator::$settings['api_key']; ?>-<?php echo PegasaasAccelerator::$settings['installation_id']; ?>&site=<?php echo $pegasaas->utils->get_http_host(); ?>&current=<?php echo PegasaasAccelerator::$settings['subscription']; ?>'><?php echo __("Upgrade", "pegasaas-accelerator"); ?></a>	
			    </li>
				<li><label>Perf Monitored Pages:</label><?php echo PegasaasAccelerator::$settings['limits']['pagespeed_scanned_pages']; ?></li>  
				<?php if ($pegasaas->is_standard() && PegasaasAccelerator::$settings['limits']['monthly_optimizations'] != "") { ?>
					<li><label>Monthly Optimizations:</label><?php echo PegasaasAccelerator::$settings['limits']['monthly_optimizations']; ?></li>
  
				<?php } else { ?>
									
				  <li><label>Max Pages Optimized:</label><?php echo PegasaasAccelerator::$settings['limits']['accelerated_pages']; ?></li>
				<?php } ?>  
				<?php if (isset(PegasaasAccelerator::$settings['limits']['page_prioritizations']) && PegasaasAccelerator::$settings['limits']['page_prioritizations'] > 0) { ?>
				<li><label>Page Prioritizations:</label><?php echo PegasaasAccelerator::$settings['limits']['page_prioritizations']; ?></li>  
									
				<?php } ?>
									
				
					<li><label>API Node:</label><?php echo $pegasaas->api->get_api_node_url(); ?></li>
								</ul>
							  </div>
							  </div>

						</div>
						
					</div>
					
					<div id="api-key-panel" class='panel settings-panel' style='display: none'>
					  <div class="panel-body">
						<a href="#" class='back-to-main-settings'><i class='fa fa-chevron-left'></i> API Key</a>
						<p>Your API key is used to communicate with the Pegasaas optimization servers</p>
						<ul>
						  <li>
						  <label>API Key:</label></li>
						  </ul>
						</div>
					</div>
					
				<?php
				$section_active = true;
				
			foreach ($feature_sections as $feature_title => $section_features) { 	
				
				?>
				<div class='pegasaas-feature-section-container <?php if (in_array($feature_title, $manual_only_configuration )) { ?>manual-only-configuration <?php } ?> ' id="settings__<?php echo strtolower(str_replace(" ", "_", $feature_title)); ?>">
					<h3 id='settings-section-<?php echo strtolower(str_replace(" ", "-", $feature_title)); ?>' class='pegasaas-subsystem-title'><?php echo $feature_title; ?></h3>	
					<?php include "settings/section-descriptions.php"; ?>
					<div class="list-group">
					<?php foreach ($section_features as $feature) { 
		
						$feature_settings = $features["$feature"];
							
						if (!isset($feature_settings) && !isset($features["premium"]["$feature"])) {
							continue;
						} else if (isset($features["premium"]["$feature"])) {
							$feature_settings = $features["premium"]["$feature"];
						}
						$is_new_feature = $pegasaas->is_recent_version($feature_settings['since'], "new");
						$is_recent_feature = $pegasaas->is_recent_version($feature_settings['since'], "recent");
						$is_premium_feature = isset($features["premium"]["$feature"]);
						?> 
					
						  <a href="#" data-target="#<?php echo $feature; ?>-panel" class="list-group-item feature-container 
																						  <?php if (in_array($feature, $foundation_features)) {?>foundation-feature <?php } ?>
																						  <?php if (in_array($feature, $api_features)) {?>api-feature <?php } ?>
																						  <?php if (isset($features['premium']["$feature"])) { ?>premium-feature <?php } ?>
																						  <?php if (in_array($feature, $locked_for_novice )) { ?>locked-for-novice <?php } ?> 
																						  <?php if (in_array($feature, $manual_only_configuration )) { ?>manual-only-configuration <?php } ?> 
																						  <?php if (($feature_settings['status'] == "0" || $feature_settings['status'] == "" || $is_premium_feature)  && !in_array($feature, $no_toggle_features)) { print "feature-disabled"; } ?> 
																						  <?php if ($is_new_feature) { ?>new-feature<?php } else if ($is_recent_feature) { ?>recent-feature<?php } ?>">
							  <span class='pegasaas-subsystem-title-icon hidden-xs'>
								<i id="pegasaas-setting-icon-<?php echo $feature; ?>" class='<?php echo $icons["{$feature}"]; ?>'><?php if (isset($icons_inner["{$feature}"])) { print $icons_inner["{$feature}"]; } ?></i>
								</span>
							  <?php 
					$display_name = str_replace("Blocking JavaScript", "Blocking JS", $feature_settings['name']);
					$display_name = str_replace("Lazy Load", "", $display_name);
					echo $display_name; ?> 
							  <i class='fa fa-caret-right fa-right'></i>
							  
							   <?php if (PegasaasAccelerator::$settings['status'] > 0 && 
										 !isset($features["premium"]["$feature"]) && 
										
										  !in_array($feature, $no_toggle_features)
										 ) { ?>
			
				<?php if (isset($features['upgradable']["$feature"])) { ?>
							<span class='label label-premium' rel='tt' 
								  title='A more advanced and faster feature is available by upgrading.'>UPGRADE AVAILABLE</span>
				<?php } ?>
					
					<?php 
				
				if ($feature == "speed_configuration") {
							?><span class='label label-state label-enabled basic-only-configuration'>BASIC</span>
							  <span class='label label-state label-enabled supersonic-only-configuration'>SUPERSONIC</span>
							  <span class='label label-state label-enabled hypersonic-only-configuration'>HYPERSONIC</span>
							  <span class='label label-state label-enabled beastmode-only-configuration'>BEAST MODE</span>
							  <span class='label label-state label-enabled manual-only-configuration'>MANUAL</span>
							  <?php
				} else if ($feature == "display_level") {
							?><span class='label label-state label-enabled novice-only-level'>NOVICE</span>
							  <span class='label label-state label-enabled intermediate-only-level'>INTERMEDIATE</span>
							  <span class='label label-state label-enabled advanced-only-level'>ADVANCED</span>
			
							  <?php
				
				} else if ($feature == "display_mode") {
							?><span class='label label-state label-enabled light-only-mode'>LIGHT</span>
							  <span class='label label-state label-enabled dark-only-mode'>DARK</span>
							  <span class='label label-state label-enabled plain-only-mode'>PLAIN</span>
			
							  <?php
					
				} else if ($feature == "coverage") {
							?><span class='label label-state label-enabled standard-only-coverage'>STANDARD</span>
							  <span class='label label-state label-enabled extended-only-coverage'>EXTENDED</span>
			
							  <?php					
			} else {
					
				if (isset($feature_settings['experimental'])) { 
					?><span class='label label-state label-experimental'>EXPERIMENTAL</span>
					<?php 
				}																							 
																							 
				if ($feature_settings['status'] == "0" || $feature_settings['status'] == "") { 
					?><span class='label label-state label-disabled'>DISABLED</span>
					<?php 
				} else { 
					?><span class='label label-state label-enabled'>ENABLED</span>
					<?php 

				} 
				}
							  ?>
				
		
			
				<?php } else if (isset($features['premium']["$feature"])) { ?>
							
								<span class='label label-premium'>PREMIUM FEATURE</span>
								
				<?php } ?>
						  </a>
					
					<?php } ?>
					</div>
				</div> <!-- end of pegasaas-feature-section-container -->
					
					
	<?php foreach ($section_features as $feature) { 
		
						$feature_settings = $features["$feature"];
							
						if (!isset($feature_settings) && !isset($features["premium"]["$feature"])) {
							continue;
						} else if (isset($features["premium"]["$feature"])) {
							$feature_settings = $features["premium"]["$feature"];
						}
						$is_new_feature = $pegasaas->is_recent_version($feature_settings['since'], "new");
						$is_recent_feature = $pegasaas->is_recent_version($feature_settings['since'], "recent");
						$is_premium_feature = isset($features["premium"]["$feature"]);
					?>
					<div id="<?php echo $feature; ?>-panel" class='panel settings-panel' style='display: none'>
						
	
					  <div class="panel-body <?php if ($feature != "lazy_load_third_party_vendor_scripts") { ?>feature-container<?php } ?> <?php if (isset($features['premium']["$feature"])) { ?>premium-feature <?php } ?><?php if (in_array($feature, $manual_only_configuration )) { ?>manual-only-configuration <?php } ?> <?php if (in_array($feature, $locked_for_novice )) { ?>locked-for-novice <?php } ?> <?php if (($feature_settings['status'] == "0" || $feature_settings['status'] == "" || $is_premium_feature)  &&
					  ($feature != "display_mode" &&
					   $feature != "display_level" && 
					   $feature != "coverage" && 
					   $feature != "blog" && 
					   $feature != "updater" && 
					   $feature != "ssl_warning_override")) { print "feature-disabled"; } ?> <?php if ($is_new_feature) { ?>new-feature<?php } else if ($is_recent_feature) { ?>recent-feature<?php } ?>">
						 <?php if (PegasaasAccelerator::$settings['status'] > 0 && 
								   !isset($features["premium"]["$feature"]) && 
										
										  !in_array($feature, $no_toggle_features)) { ?>
				<form method="post" class='feature-switch pull-right' target="hidden-frame">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>
				<input type='hidden' name='f' value='<?php echo $feature; ?>' />
				<?php if (isset($features['upgradable']["$feature"])) { ?>
							<span class='label label-premium' rel='tt' 
								  title='A more advanced and faster feature is available by upgrading.'>UPGRADE AVAIL</span>
				<?php } ?>
					
				<?php 

				if ($feature_settings['status'] == "0" || $feature_settings['status'] == "") { 
					?><input type='hidden' name='c' value='enable-feature' />
					<input  type='checkbox' class='js-switch js-switch-small' />
					<?php 
				} else { 
					?><input type='hidden' name='c' value='disable-feature' />
					  <input type='checkbox' class='js-switch js-switch-small' checked />
					<?php 

				} ?>
				</form>	
		
			
				<?php } else if (isset($features['premium']["$feature"])) { ?>
							<form method="post" class='feature-switch pull-right' target="hidden-frame">
								<span class='label label-premium'>PREMIUM FEATURE</span>
								<input type='checkbox' class='js-switch js-switch-small js-premium-feature' />
							</form>
				<?php } ?>
						  
						  <a href="#" class='back-to-main-settings'><i class='fa fa-chevron-left'></i> <?php echo $feature_settings["name"]; ?></a>
							
	
				<div class='pegasaas-accelerator-subsystem-feature-description'>	
<!-- display version since this feature has been included -->
<?php if ($feature_settings['since'] != "") { ?><p class='since-version'>Since: v<?php echo ($feature_settings['since']); ?></p><?php } ?>

<!-- explain what this feature does -->
<?php include "settings/feature-descriptions.php"; ?>


				<div class='pegasaas-accelerator-subsystem-status <?php if (PegasaasAccelerator::$settings['status'] == 0 ) { print "pegasaas-accelerator-subsystem-status-bypassed"; } else if ($feature_settings['status'] == "0") { print "pegasaas-accelerator-subsystem-status-bypassed"; } else { print "pegasaas-accelerator-subsystem-status-onlinex"; } ?>'><?php 
													  if (PegasaasAccelerator::$settings['status'] == 0) { 
														  print "Offline";
													  } else {
														if ($feature_settings['status'] == "0" && false) { 
															print "You have disabled this feature."; 
														} else { 
															include "settings/feature-settings.php"; 
															}	
														}?>

						</div></div>
					</div>
					</div>
					

				<?php  } ?>
			
				<?php
					$section_active = false;
				}
				?></div><!-- end tab content --><?php
			}
				?>
				</div>

	<div class='extra-column'></div>
			<iframe name='hidden-frame' src="" style='display: none;'></iframe>
</div>

					
