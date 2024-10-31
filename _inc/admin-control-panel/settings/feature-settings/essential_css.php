<div class='hidden-novice'>
					<br/><br/><b>Essential CSS:</b><br/>
										<form method="post" style="display: block" class='feature-setting-change'>
				<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="essential_css_css">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>													
						<textarea placeholder=".your-css { font-color: red; content: 'Example'; }" class='form-control form-control-full' name='s'><?php echo $feature_settings['css']; ?></textarea>
					<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
			</form>
					</div>