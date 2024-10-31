	
					<form method="post" style="display: block" class='feature-setting-change'>
				<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="exclude_urls_urls">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>								
						<textarea class='form-control form-control-full' name='s'><?php echo $feature_settings['urls']; ?></textarea>
					<button class='btn btn-success btn-save' onclick='submit_form(this)'>Save <span class='status'></span></button>
			</form>