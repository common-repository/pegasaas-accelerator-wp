<div class='hidden-novice'>
					<b>Exclude CSS Files From Being Deferred:</b><br/> 
										<form method="post" style="display: block" class='feature-setting-change'>
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>													
				<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="defer_render_blocking_css_exclude_stylesheets">
						<textarea placeholder="/path/to/stylesheet.css" class='form-control form-control-full' name='s'><?php echo $feature_settings['exclude_stylesheets']; ?></textarea>
					<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
			</form>
					</div>