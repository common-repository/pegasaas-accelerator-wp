<b>Connection Duration:</b><br/>
			
	<form method="post" style="display: inline-block"  class='feature-setting-change'>
			<input type="hidden" name="c" value="change-feature-attribute">
			<input type="hidden" name="a" value="interval">
			<input type="hidden" name="f" value="<?php echo $feature; ?>">
				
			<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="15">Every 15 Seconds</option>
				  <option value="30" <?php if ($feature_settings['interval'] == "30") { ?>selected<?php } ?>>30</option>
				  <option value="45" <?php if ($feature_settings['interval'] == "45") { ?>selected<?php } ?>>45</option>
			</select>
	</form>  