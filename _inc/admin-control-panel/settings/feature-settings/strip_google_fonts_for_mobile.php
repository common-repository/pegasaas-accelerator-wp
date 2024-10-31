<div class='hidden-novice'>
					<form method="post" style="display: block" class='feature-setting-change'>
				<input type="hidden" name="c" value="change-feature-status">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>								
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="1">Strip All (for best performance)</option>
				  <option value="2" <?php if ($feature_settings['status'] == "2") { ?>selected<?php } ?>>Strip Non-Essential (for best display) </option>
				</select>
			</form>				
					</div>