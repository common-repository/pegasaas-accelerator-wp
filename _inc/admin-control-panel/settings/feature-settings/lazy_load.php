<!-- lazy load iframes -->
					<div class='hidden-novice'>
					<b>Exclude Iframe URLS from being lazy loaded:</b><br/> 
					<form method="post" style="display: block" class='feature-setting-change'>
					
					<input type='hidden' name='prompt' value='clear_cache' />
																	
				<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="lazy_load_exclude_urls">
						<textarea placeholder="https://somewebsite.com/some-path/" class='form-control form-control-full' name='s'><?php echo $feature_settings['exclude_urls']; ?></textarea>
					<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
			</form>
					</div>