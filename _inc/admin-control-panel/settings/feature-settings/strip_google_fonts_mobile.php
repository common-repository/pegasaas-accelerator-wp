<div class='hidden-novice'>
	<h4>Custom Configuration</h4>
	<p style='text-transform: none;'>Choose which fonts you wish to strip, for mobile browsers.   This option 
	is sometimes used if the "Strip Non-Essential" option strips a font-variant (weight or style) that you find necessary for your mobile display.
	It is recommended that you strip as many as possible to keep your mobile load time as fast as possible. </p>
	<form method="post" style="display: block" class='feature-setting-change'>
				<input type="hidden" name="c" value="change-feature-status">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>								
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
				<select class='form-control' onchange="toggle_strip_web_fonts_custom(this); submit_form(this)" name="s">
				  <option value="1">Strip All (for best performance)</option>
				  <option value="2" <?php if ($feature_settings['status'] == "2") { ?>selected<?php } ?>>Strip Non-Essential (for best display) </option>
				  <option value="3" <?php if ($feature_settings['status'] == "3") { ?>selected<?php } ?>>Custom</option>
				</select>
		</form>				
	<br/>
				<script>
				function toggle_strip_web_fonts_custom(select) {
					if (jQuery(select).val() == 3) {
						jQuery("#strip-web-fonts-custom").css("display", "block");
					} else {
						jQuery("#strip-web-fonts-custom").css("display", "none");

					}
				}
				</script>	
		
					<div class='table-complex-settings regular-case' id='strip-web-fonts-custom' <?php if ($feature_settings['status'] != 3) { print "style='display: none;'"; } ?>>
					  <div class='row theader'>
					
						<div class='col-sm-6' style='padding-left: 30px; '>Font</div>
						<div class='col-sm-3'>Weight</div>
						<div class='col-sm-3'>Style</div>
					  </div>	
						<?php $detected_fonts = $pegasaas->scanner->get_page_fonts();
						//	print "<pre>";
						//	var_dump($detected_fonts);
						//	print "</pre>";
							  foreach ($detected_fonts as $family => $variants) { 
								  foreach ($variants as $variant) {
									 $signature = $family.",".$variant['weight'].",".$variant['style'];
									
									
						?>
					
					<div class='row feature-row  <?php if ($feature_settings['custom_fonts']["{$signature}"]['status'] != 1) { print "feature-disabled"; } ?>'>
						<form method="post" class='feature-toggle-switch prompt-to-clear-cache' target="hidden-frame">
						
						  
							<div class='col-sm-6' class='toggle-cell' > 
				
						
							<input type="hidden" name="c" value="toggle-local-complex-setting">
							<input type='hidden' name='f' value='strip_google_fonts_mobile_custom_fonts' />
						    <input type='hidden' name='font' value='<?php echo $signature; ?>' />
							<input type='hidden' name='prompt' value='clear_cache' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['custom_fonts']["{$signature}"]['status'] == 1) { print "checked"; } ?> />
							&nbsp;
							<?php print $family; ?>

						
					 	
							</div>
						<div class='col-sm-3'>
						  <?php echo $variant['weight'] == NULL ? "Normal" : ucwords($variant['weight']); ?>
						</div>
						<div class='col-sm-3'>
							<?php echo $variant['style'] == NULL ? "Normal" : ucwords($variant['style']); ?>
						</div>						
					</form>
				</div>
						
					<?php } ?>	
				<?php } ?>	
			</div>
					
	
</div>