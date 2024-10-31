<?php if ( $pegasaas->is_pro()) { ?>
					<div class='hidden-novice'>
					<?php if ($pegasaas->is_pro()) { ?>		
						<p class='regular-case'><b>Please note</b> that care should be taken when lazy loading scripts.  If you lazy load a script that other scripts rely upon, which are not themselves lazy loaded, critical functionality in your page
						can be broken.  <b>It is strongly recommended that you test the functionality of your optimized pages after lazy loading.</b></p>
					<div class='table-complex-settings regular-case'>
					  <div class='row theader'>
					
						<div class='col-sm-6' style='padding-left: 30px; '>Script</div>
						<div class='col-sm-3'>Mobile</div>
						<div class='col-sm-3'>Desktop</div>
					  </div>	
						<?php $detected_lazy_loadable_scripts = $pegasaas->scanner->get_lazy_loadable_scripts();
							
							  foreach ($detected_lazy_loadable_scripts as $item) { 
								$url = $item['url'];

									
									
						?>
					
					<div class='row feature-row  <?php if ($feature_settings['custom_scripts']["{$url}"]['status'] != 1) { print "feature-disabled"; } ?>'>
						<form method="post" class='feature-toggle-switch prompt-to-clear-cache' target="hidden-frame">
						
						  
							<div class='col-sm-6' class='toggle-cell' > 
				
						
							<input type="hidden" name="c" value="toggle-local-complex-setting">
							<input type='hidden' name='f' value='lazy_load_scripts_custom_scripts' />
						    <input type='hidden' name='url' value='<?php echo $url; ?>' />
							<input type='hidden' name='prompt' value='clear_cache' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['custom_scripts']["{$url}"]['status'] == 1) { print "checked"; } ?> />
							&nbsp;
						  <?php if ($item['alias'] != "") { echo $item['alias']; } else { echo $item['url']; } ?>
					 	
							</div>
						<div class='col-sm-3'>
						  <select name='mobile_setting' class='form-control' >
							  <option value='0'    <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == '0') { ?>selected<?php } ?>>Do Not Lazy Load</option>  
							  <option value='500'  <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 500) { ?>selected<?php } ?>>After 0.5s</option>
							  <option value='1000' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 1000) { ?>selected<?php } ?>>After 1.0s</option>
							  <option value='1500' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 1500) { ?>selected<?php } ?>>After 1.5s</option>
							  <option value='2000' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 2000) { ?>selected<?php } ?>>After 2.0s</option>
							  <option value='2500' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 2500) { ?>selected<?php } ?>>After 2.5s</option>
							  <option value='3000' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 3000) { ?>selected<?php } ?>>After 3.0s</option>
							  <option value='3500' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 3500) { ?>selected<?php } ?>>After 3.5s</option>
							  <option value='4000' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 4000) { ?>selected<?php } ?>>After 4.0s</option>							  
							  <option value='4500' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 4500) { ?>selected<?php } ?> >After 4.5s</option>							  
							  <option value='5000' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 5000) { ?>selected<?php } ?>>After 5.0s</option>							  
							  <option value='5500' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 5500) { ?>selected<?php } ?>>After 5.5s</option>
							  <option value='6000' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 6000) { ?>selected<?php } ?>>After 6.0s</option>							  
							  <option value='6500' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 6500) { ?>selected<?php } ?>>After 6.5s</option>							  
							  <option value='7000' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 7000) { ?>selected<?php } ?>>After 7.0s</option>							  
							  <option value='7500' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 7500) { ?>selected<?php } ?>>After 7.5s</option>	
							  <option value='1'    <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 1) { ?>selected<?php } ?>>On Action</option>
						  </select>
						</div>
					<div class='col-sm-3'>
						  <select name='desktop_setting' class='form-control' >
							  <option value='0'    <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == '0') { ?>selected<?php } ?>>Do Not Lazy Load</option>  
							  <option value='500'  <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 500) { ?>selected<?php } ?>>After 0.5s</option>
							  <option value='1000' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 1000) { ?>selected<?php } ?>>After 1.0s</option>
							  <option value='1500' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 1500) { ?>selected<?php } ?>>After 1.5s</option>
							  <option value='2000' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 2000) { ?>selected<?php } ?>>After 2.0s</option>
							  <option value='2500' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 2500) { ?>selected<?php } ?>>After 2.5s</option>
							  <option value='3000' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 3000) { ?>selected<?php } ?>>After 3.0s</option>
							  <option value='3500' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 3500) { ?>selected<?php } ?>>After 3.5s</option>
							  <option value='4000' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 4000) { ?>selected<?php } ?>>After 4.0s</option>							  
							  <option value='4500' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 4500) { ?>selected<?php } ?> >After 4.5s</option>							  
							  <option value='5000' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 5000) { ?>selected<?php } ?>>After 5.0s</option>							  
							  <option value='5500' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 5500) { ?>selected<?php } ?>>After 5.5s</option>
							  <option value='6000' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 6000) { ?>selected<?php } ?>>After 6.0s</option>							  
							  <option value='6500' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 6500) { ?>selected<?php } ?>>After 6.5s</option>							  
							  <option value='7000' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 7000) { ?>selected<?php } ?>>After 7.0s</option>							  
							  <option value='7500' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 7500) { ?>selected<?php } ?>>After 7.5s</option>	
							  <option value='1'    <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 1) { ?>selected<?php } ?>>On Action</option>
						  </select>
						</div>						
						</form>
						</div>
						
						
						<?php } ?>	
							</div>
						<?php } else { ?>
						<p>Upgrading to premium will provide full control over which scripts are lazy loaded, and when.  If you want to fine tune the loading
						of your scripts, consider upgrading today.</p>
						<?php } ?>
	<br/><br/><b>Manually Add Scripts To Be Lazy Loaded:</b><br/>
	<form method="post" style="display: block" class='feature-setting-change'>
		<input type="hidden" name="c" value="change-local-setting">
<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>		
		<input type="hidden" name="f" value="lazy_load_scripts_additional_scripts">
		<textarea placeholder="/path/to/script.js" class='form-control form-control-full' name='s'><?php echo $feature_settings['additional_scripts']; ?></textarea>
		<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
	</form>			
</div>
<?php } ?>