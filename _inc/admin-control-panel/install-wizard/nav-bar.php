<?php $installation_step = 1; ?>
<div class="stepwizard">
    		<div class="stepwizard-row setup-panel <?php if ($pegasaas->cache->cloudflare_exists() && !$pegasaas->cache->cloudflare_credentials_valid()) { ?>stepwizard-row-10<?php } ?>">
      			<div class="stepwizard-step">
        			<a href="#step-start" type="button" class="btn <?php if ($api_connection_error) { ?>btn-default<?php } else { ?>btn-primary<?php } ?> btn-circle" <?php if ($api_connection_error) { ?>disabled='disabled']<?php } ?>><?php echo $installation_step++; ?></a>
        			<p><?php _e("Start", "pegasaas-accelerator"); ?></p>
      			</div>
				
      			<div class="stepwizard-step">
        			<a href="#step-compatibility" type="button" class="btn btn-default btn-circle"  disabled="disabled"><?php echo $installation_step++; ?></a>
        			<p><?php _e("Compatibility", "pegasaas-accelerator"); ?></p>
      			</div>		
				
      			<div class="stepwizard-step">
        			<a href="#step-ui" type="button" class="btn btn-default btn-circle"  disabled="disabled"><?php echo $installation_step++; ?></a>
        			<p><?php _e("Interface", "pegasaas-accelerator"); ?></p>
      			</div>	
      			<div class="stepwizard-step">
        			<a href="#step-ux" type="button" class="btn btn-default btn-circle"  disabled="disabled"><?php echo $installation_step++; ?></a>
        			<p><?php _e("Experience", "pegasaas-accelerator"); ?></p>
      			</div>		
					
      			<div class="stepwizard-step">
        			<a href="#step-api" type="button" class="btn btn-default btn-circle"  disabled="disabled"><?php echo $installation_step++; ?></a>
        			<p><?php _e("API Key", "pegasaas-accelerator"); ?></p>
      			</div>
      			<div class="stepwizard-step">
					<a href="#step-terms" type="button" class="btn btn-default btn-circle"  disabled="disabled"><?php echo $installation_step++; ?></a>
        			<p><?php _e("Terms of Service", "pegasaas-accelerator"); ?></p>
      			</div>
			
				<?php if ($pegasaas->cache->cloudflare_exists() && !$pegasaas->cache->cloudflare_credentials_valid()) { ?>
      			<div class="stepwizard-step">
					<a href="#step-cloudflare" type="button" class="btn btn-default btn-circle"  disabled="disabled"><?php echo $installation_step++; ?></a>
        			<p><?php _e("Cloudflare", "pegasaas-accelerator"); ?></p>
      			</div>	
				<div class="stepwizard-step">
					<a href="#step-config" type="button" class="btn btn-default btn-circle" disabled="disabled"><?php echo $installation_step++; ?></a>
        			<p><?php _e("Pages", "pegasaas-accelerator"); ?></p>
      			</div>
      			<div class="stepwizard-step">
        			<a href="#step-speed" type="button" class="btn btn-default btn-circle"  disabled="disabled"><?php echo $installation_step++; ?></a>
        			<p><?php _e("Speed", "pegasaas-accelerator"); ?></p>
      			</div>					
				<div class="stepwizard-step">
					<a href="#step-go" type="button" class="btn btn-default btn-circle" disabled="disabled"><?php echo $installation_step++; ?></a>
        			<p><?php _e("Finish", "pegasaas-accelerator"); ?></p>
      			</div>				
				<?php } else { ?>
				<div class="stepwizard-step">
					<a href="#step-config" type="button" class="btn btn-default btn-circle"  <?php if (!$api_connection_error) { ?>disabled="disabled"<?php } ?>><?php echo $installation_step++; ?></a>
        			<p><?php _e("Pages", "pegasaas-accelerator"); ?></p>
      			</div>
				<div class="stepwizard-step">
        			<a href="#step-speed" type="button" class="btn btn-default btn-circle"  disabled="disabled"><?php echo $installation_step++; ?></a>
        			<p><?php _e("Speed", "pegasaas-accelerator"); ?></p>
      			</div>	
				<div class="stepwizard-step">
					<a href="#step-go" type="button" class="btn <?php if (!$api_connection_error) { ?>btn-default<?php } else { ?>btn-primary<?php } ?> btn-circle" disabled="disabled"><?php echo $installation_step++; ?></a>
        			<p><?php _e("Finish", "pegasaas-accelerator"); ?></p>
      			</div>						
				<?php } ?>
      		</div>
    	</div>