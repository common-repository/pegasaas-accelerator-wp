	<!--	<b>Experience Level:</b><br/> -->
				<form method="post" style="display: block" target="hidden-frame" class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-status">
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
			<ul class='interface-level'>
			  	<li class='<?php if ($feature_settings['status'] == "0") { print "selected_interface_experience "; } ?>interface_level_novice'>
					<input onclick="toggle_display_level(this)" type='radio' id="interface_level_novice" name='s' value='0' <?php if ($feature_settings['status'] == "0") { print "checked "; } ?>> 
    				<label for="interface_level_novice">Novice</label>
  				</li>
			  	<li class='<?php if ($feature_settings['status'] == "1") { print "selected_interface_experience "; } ?>interface_level_intermediate'>
					<input onclick="toggle_display_level(this)" type='radio' id="interface_level_intermediate" name='s' value='1' <?php if ($feature_settings['status'] == "1") { print "checked "; } ?>> 
    				<label for="interface_level_intermediate">Intermediate</label>
  				</li>
			  	<li class='<?php if ($feature_settings['status'] == "2") { print "selected_interface_experience "; } ?>interface_level_advanced'>
					<input onclick="toggle_display_level(this)" type='radio' id="interface_level_advanced" name='s' value='2' <?php if ($feature_settings['status'] == "2") { print "checked "; } ?>> 
    				<label for="interface_level_advanced">Advanced</label>
  				</li>	
			</ul>
	
			</form>			