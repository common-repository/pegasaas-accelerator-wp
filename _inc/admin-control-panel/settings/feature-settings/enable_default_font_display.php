<div class='hidden-novice'>
	<b>Optimization Level:</b><br/>
	<form method="post" style="display: inline-block" class='feature-setting-change'>
		<input type="hidden" name="c" value="change-feature-status">
		<input type="hidden" name="f" value="<?php echo $feature; ?>">
		<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>
		<select class='form-control' onchange="submit_form(this)" name="s">
			<option value="1">Fallback (default)</option>
			<option value="swap" <?php if ($feature_settings['status'] == "swap") { ?>selected<?php } ?>>Swap</option>
			<option value="optional"  <?php if ($feature_settings['status'] == "optional")  { ?>selected<?php } ?>>Optional</option>
		</select>
	</form>   	
</div>