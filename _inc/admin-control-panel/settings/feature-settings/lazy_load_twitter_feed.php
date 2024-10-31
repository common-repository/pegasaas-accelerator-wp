<div class='hidden-novice'>
<b>Optimization Level:</b><br/>
				<form method="post" style="display: inline-block" class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-status">
<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>					
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="1">Default</option>
				  <option value="500" <?php if ($feature_settings['status'] == "500") { ?>selected<?php } ?>>0.5 second</option>
				  <option value="1000" <?php if ($feature_settings['status'] == "1000") { ?>selected<?php } ?>>1 second</option>
				  <option value="1500"  <?php if ($feature_settings['status'] == "1500")  { ?>selected<?php } ?>>1.5 seconds</option>
				  <option value="2000"  <?php if ($feature_settings['status'] == "2000")  { ?>selected<?php } ?>>2 seconds</option>				
				  <option value="3000"  <?php if ($feature_settings['status'] == "3000")  { ?>selected<?php } ?>>3 seconds</option>				
				  <option value="4000"  <?php if ($feature_settings['status'] == "4000")  { ?>selected<?php } ?>>4 seconds</option>				
				  <option value="5000"  <?php if ($feature_settings['status'] == "5000")  { ?>selected<?php } ?>>5 seconds</option>				
				</select>
			</form>           									
					</div>