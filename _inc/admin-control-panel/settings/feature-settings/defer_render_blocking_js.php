<div class='hidden-novice hidden-intermediate'>
					<b>How To Defer:</b><br/>
				<form method="post" style="display: inline-block" class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-status">
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>					
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="1">Default</option>  
<?php  ?>
					<option value="4" <?php if ($feature_settings['status'] == "4") { ?>selected<?php } ?>>Externally defer all files and inline blocks at original DOM location</option>
				  <option value="5" <?php if ($feature_settings['status'] == "5") { ?>selected<?php } ?>>Externally defer all files at original DOM location and assembled inline blocks at end of page (default)</option>
				
				</select>
			</form>
						<br/><br/>
					</div>
					
					
					<div class='hidden-novice'>
						<b>Exclude Scripts:</b><br/>
										<form method="post" style="display: block" class='feature-setting-change'>
				<input type="hidden" name="c" value="change-local-setting">
											
											<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>
					<input type="hidden" name="f" value="defer_render_blocking_js_exclude_scripts">
						<textarea placeholder="/path/to/javascript-file.js" class='form-control form-control-full' name='s'><?php echo $feature_settings['exclude_scripts']; ?></textarea>
					<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
			</form>
</div>