 <?php if ($pegasaas->is_pro()) { ?>
					<div class='hidden-novice'>
						<b>Employ  Cache Busting:</b><br/>
						<div style='padding: 0px 15px;'>
					<div class='row'>
						<div class='col-sm-6'>
							<b>CSS</b><br/>
							<form method="post" style="display: inline-block"  class='feature-setting-change'>
								<input type="hidden" name="c" value="change-feature-attribute">
								<input type="hidden" name="a" value="css_cache_busting">
								<input type='hidden' name='prompt' value='clear_cache' />
								<input type="hidden" name="f" value="<?php echo $feature; ?>">

								<select class='form-control' onchange="submit_form(this)" name="s">
								  <option value="0">No</option>
								  <option value="1" <?php if ($feature_settings['css_cache_busting'] == "1") { ?>selected<?php } ?>>Yes</option>
								</select>
							</form>  
						</div>
						<div class='col-sm-6'>
							<b>Javascript</b><br/>
							<form method="post" style="display: inline-block"  class='feature-setting-change'>
								<input type="hidden" name="c" value="change-feature-attribute">
								<input type="hidden" name="a" value="js_cache_busting">
								<input type='hidden' name='prompt' value='clear_cache' />
								<input type="hidden" name="f" value="<?php echo $feature; ?>">

								<select class='form-control' onchange="submit_form(this)" name="s">
								  <option value="0">No</option>
								  <option value="1" <?php if ($feature_settings['js_cache_busting'] == "1") { ?>selected<?php } ?>>Yes</option>
								</select>
							</form>  
						</div>	
						</div>
					</div>
					
						<br>
						<b>About Cache Busting:</b>
						<p class='normal-case'>Cache busting adds a timestamp to the end of the CDN served asset.  This helps when you are making changes to stylesheets but the changes
							are not reflected in your web browser.</p>
						<p class='normal-case'>You will still need to clear the  CDN asset cache (either CSS, JS) after you update your assets, and clear your page cache, so that the new CDN cache busting timestamp can
							be used.</p>
						<p class='normal-case'>By enabling this, GTMetrix, Pingdom, and other testing tools may warn you to "remove query string arguments" from your static resources.  Those warnings can be ignored.</p>
					</div>
<?php } ?>