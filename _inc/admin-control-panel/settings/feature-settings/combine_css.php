 <?php if ($pegasaas->is_pro()) { ?>
					<div class='hidden-novice'>
					<b>Combination Method:</b><br/>
					<form method="post" style="display: inline-block" class='feature-setting-change'>
<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
<?php } ?>										
					<input type="hidden" name="c" value="change-feature-status">
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="1">Auto Detect Best Method</option>
				  <option value="x1" <?php if ($feature_settings['status'] == "x1") { ?>selected<?php } ?>>Combine into 1 File</option>
				  <option value="x2" <?php if ($feature_settings['status'] == "x2") { ?>selected<?php } ?>>Combine into 2 Files</option>
				  <option value="x3" <?php if ($feature_settings['status'] == "x3") { ?>selected<?php } ?>>Combine into 3 Files</option>
				  <option value="x4" <?php if ($feature_settings['status'] == "x4") { ?>selected<?php } ?>>Combine into 4 Files</option>
				</select>
					</form>
				</div>
					
<?php } ?>