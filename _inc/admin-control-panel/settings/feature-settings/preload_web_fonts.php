<div class='hidden-novice'>
	<br/><br/><b>Preload Method:</b><br/> 
		<form method="post" style="display: block" class='feature-setting-change'>
				<input type="hidden" name="c" value="change-feature-status">
				<input type="hidden" name="f" value="preload_web_fonts">
				<?php if (in_array($feature, $requires_cache_clearing )) { ?>
				<input type='hidden' name='prompt' value='clear_cache' />
				<?php } ?>											
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="1">Default</option>
				  <option value="2" <?php if ($feature_settings['status'] == "2") { ?>selected<?php } ?>>Advanced (Experimental)</option>
				</select>
		</form>	
	</div>


