		<!--	
					<div class='hidden-novice'>
				<b>Minification Level:</b>	
					<br/>
				<form method="post" style="display: inline-block" class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-status">
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
						
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="1">.js Files Only (Default)</option>  
				  <option value="2" <?php if ($feature_settings['status'] == "2") { ?>selected<?php } ?>>.js + .min.js Files</option>
				</select>
			</form>					
					
						
						<br/><br/> 
						<b>Exclude Javascript Files From Minification:</b><br/>
										<form method="post" style="display: block" class='feature-setting-change'>
				<input type="hidden" name="c" value="change-local-setting">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>													
					<input type="hidden" name="f" value="defer_unused_css_exclude_stylesheets">
						<textarea placeholder="/path/to/script.js" class='form-control form-control-full' name='s'><?php echo $feature_settings['exclude_scripts']; ?></textarea>
					<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
			</form>
						</div>
-->