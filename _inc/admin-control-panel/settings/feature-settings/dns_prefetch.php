<div class='hidden-novice hidden-intermediate'>
					<b>Method:</b><br/>
									<form method="post" style="display: inline-block" class='feature-setting-change'>
<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>										
					<input type="hidden" name="c" value="change-feature-status">
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="1">Default</option>
				  <option value="2" <?php if ($feature_settings['status'] == "2") { ?>selected<?php } ?>>DNS Prefetch (Default)</option>
				  <option value="3" <?php if ($feature_settings['status'] == "3") { ?>selected<?php } ?>>Preconnect</option>
				</select>
					</form>
					
					
					<br/><br/><b>Additional Domains:</b><br/>
										<form method="post" style="display: block" class='feature-setting-change'>
<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>											
				<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="dns_prefetch_additional_domains">
						<textarea placeholder="Example: //www.googletagmanager.com" class='form-control form-control-full' name='s'><?php echo $feature_settings['additional_domains']; ?></textarea>
					<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
			</form>					
</div>