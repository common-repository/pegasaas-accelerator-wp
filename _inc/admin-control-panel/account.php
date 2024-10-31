<div style='max-width: 650px; margin: auto;'>
<h3>Account Info</h3>
		  <p>Your Pegasaas account is your key to a personalized experience with all Pegasaas products and services.</p>
		  <div class='pegasaas-account-info-container'>
		    <div class='gravatar-container'>
			<?php 
			$email = PegasaasAccelerator::$settings['account']['email_address'];
			$default = "mm";
			$size = 150;

			$grav_url = "https://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;
			$plugin_info = get_plugin_data($pegasaas->get_plugin_file());

	 		
			?>
			<img src="<?php echo $grav_url; ?>" alt="" />
			</div>
			<div class='account-info'>
			  <ul>
				<?php if (!$pegasaas->is_free()) { ?>
			    <li><label>Name:</label><?php echo PegasaasAccelerator::$settings['account']['first_name']." ".PegasaasAccelerator::$settings['account']['last_name']; ?></li>
			    <?php } ?>
				 <li><label>Email:</label><?php echo PegasaasAccelerator::$settings['account']['email_address']; ?></li>
			    <li><label>Website:</label><?php echo PegasaasAccelerator::$settings['licensed_domain']; ?></li>
				<li class='divider'></li>
			    <li><label>Installation ID:</label><?php echo PegasaasAccelerator::$settings['installation_id']; ?></li>
			    <li><label>API Key:</label><form id="pegasaas-api-key-form" action="admin.php?page=pegasaas-accelerator"><input id="pegasaas-api-key" type="text" value="<?php echo PegasaasAccelerator::$settings['api_key']; ?>" placeholder="API Key"> <div id="api-key-status" class='api-key-status'><?php if (PegasaasAccelerator::$settings['status'] == "1" || PegasaasAccelerator::$settings['status'] == "0") { ?><span class="dashicons dashicons-yes"></span><?php } ?></div><input class='update-button btn btn-default btn-xs' type='submit' value='Update' /></form></li>
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
<!--
			    <?php if ($pegasaas->interface->get_expires_in(false) < 0) { ?>
		    	<li><label>Status:</label>Inactive</li>
				<?php } else { ?>
		    	<li><label>Status:</label>Active</li>

			    <li><label><?php if (PegasaasAccelerator::$settings['auto_renew'] == 1) { print "Renews"; } else { "Expires"; } ?> In:</label><?php echo $pegasaas->interface->get_expires_in(); ?></li>
			    <?php } ?>
				
			    <li><label>Auto Renew:</label><?php if (PegasaasAccelerator::$settings['auto_renew'] == 1) { print "On"; } else { "Off"; } ?></li>
  -->			  
</ul>
			  
			</div>
			  <?php if ($pegasaas->in_debug_mode("show_settings"))  {
	print "<pre>";
	var_dump(PegasaasAccelerator::$settings);
	print "</pre>";

			} ?>

		  </div>
	<?php if (!$pegasaas->is_free()) { ?>
		  	 <p>To manage your service subscription, API key, and licensed domain, <a href='https://pegasaas.com/manage' target='_blank'>log in to your Pegasaas Account</a>.</p>
	<?php } ?>
</div>