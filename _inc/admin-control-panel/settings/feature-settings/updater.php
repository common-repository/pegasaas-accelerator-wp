	<b>Update To:</b><br/>
				<form method="post" style="display: inline-block" target="hidden-frame" class='feature-setting-change' >
					<input type="hidden" name="c" value="change-feature-status">
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>		
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="0">Public Release (Default)</option>
				  <option value="1" <?php if ($feature_settings['status'] == "1") { ?>selected<?php } ?>>Release Candidate (or better)</option>
				  <option value="2" <?php if ($feature_settings['status'] == "2") { ?>selected<?php } ?>>Beta Release (or better)</option>
				</select>
			</form>	