<div class='hidden-novice'>
					
					
					<br/><br/><b>Resources To Preload:</b><br/> 
										<form method="post" style="display: block" class='feature-setting-change'>
				<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="preload_user_files_resources">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>											
						<textarea placeholder="Example: https://www.google-analytics.com/analytics.js" class='form-control form-control-full' name='s'><?php echo $feature_settings['resources']; ?></textarea>
					<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
			</form>	
	</div>