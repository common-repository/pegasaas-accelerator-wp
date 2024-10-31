<div class='hidden-novice hidden-intermediate'>
	<b>When To Initialize Elementor Frontend:</b><br/>
	<form method="post" style="display: inline-block" class='feature-setting-change'>
		<input type="hidden" name="c" value="change-feature-attribute">
		<input type="hidden" name="a" value="lazy_load_delay">

		<input type="hidden" name="f" value="<?php echo $feature; ?>">
		<?php if (in_array($feature, $requires_cache_clearing )) { ?>
		<input type='hidden' name='prompt' value='clear_cache' />
		<?php } ?>							
		<select class='form-control' onchange="submit_form(this)" name="s">
			<option value="500" <?php if ($feature_settings['lazy_load_delay'] == "500") { ?>selected<?php } ?>>500ms (mobile) - Smallest Delay</option>  
			<option value="1500" <?php if ($feature_settings['lazy_load_delay'] == "1500") { ?>selected<?php } ?>>1500ms (mobile) - Small Delay (Default - Recommended)</option>  
			<option value="2500" <?php if ($feature_settings['lazy_load_delay'] == "2500") { ?>selected<?php } ?>>2500ms (mobile) - Longer Delay</option>
			<option value="3500" <?php if ($feature_settings['lazy_load_delay'] == "3500") { ?>selected<?php } ?>>3500ms (mobile) - Longest Delay</option>
		</select>
	</form>									
</div>

<div class='hidden-novice hidden-intermediate'>
	<b>Template CSS Handling:</b><br/>
	<form method="post" style="display: inline-block" class='feature-setting-change'>
		<input type="hidden" name="c" value="change-feature-attribute">
		<input type="hidden" name="a" value="post_css_compatibility">

		<input type="hidden" name="f" value="<?php echo $feature; ?>">
		<?php if (in_array($feature, $requires_cache_clearing )) { ?>
		<input type='hidden' name='prompt' value='clear_cache' />
		<?php } ?>							
		<select class='form-control' onchange="submit_form(this)" name="s">
			<option value="0" <?php if ($feature_settings['post_css_compatibility'] == "0") { ?>selected<?php } ?>>Disabled</option>  
			<option value="1" <?php if ($feature_settings['post_css_compatibility'] == "1") { ?>selected<?php } ?>>Excluded post-###.css from full deferral</option>  
			<option value="2" <?php if ($feature_settings['post_css_compatibility'] == "2") { ?>selected<?php } ?>>Direct inject post-###.css into HTML</option>
		</select>
	</form>									
</div>


