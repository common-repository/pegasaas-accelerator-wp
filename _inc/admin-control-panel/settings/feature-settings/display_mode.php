			<form method="post" style="display: block" target="hidden-frame" class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-status">
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
										<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>	
					
				
			<ul class='interface-theme'>
			  	<li class='<?php if ($feature_settings['status'] == "1") { print "selected_interface_theme "; } ?>interface_type_light'>
					<input onclick="toggle_display_mode(this)" type='radio' id="interface_type_light" name='s' value='1' <?php if ($feature_settings['status'] == "1") { print "checked "; } ?>> 
    				<label for="interface_type_light">LIGHT</label>
  				</li>
			  	<li class='<?php if ($feature_settings['status'] == "0") { print "selected_interface_theme "; } ?>interface_type_dark'>
					<input onclick="toggle_display_mode(this)" type='radio' id="interface_type_dark" name='s' value='0' <?php if ($feature_settings['status'] == "0") { print "checked "; } ?>> 
    				<label for="interface_type_dark">DARK</label>
  				</li>
			  	<li class='<?php if ($feature_settings['status'] == "2") { print "selected_interface_theme "; } ?>interface_type_plain'>
					<input onclick="toggle_display_mode(this)" type='radio' id="interface_type_plain" name='s' value='2' <?php if ($feature_settings['status'] == "2") { print "checked "; } ?>> 
    				<label for="interface_type_plain">PLAIN</label>
  				</li>	
			</ul>


			</form>