
				<form method="post" style="display: block" target="hidden-frame" class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-status">
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
			<ul class='coverage-level'>
			  	<li class='<?php if ($feature_settings['status'] == "0") { print "selected_coverage_level "; } ?>coverage_level_premium'>
					<input onclick="toggle_coverage_level(this)" type='radio' id="coverage_level_premium" name='s' value='0' <?php if ($feature_settings['status'] == "0") { print "checked "; } ?>> 
    				<label for="coverage_level_premium">Premium Coverage Only</label>
  				</li>
			  	<li class='<?php if ($feature_settings['status'] == "1") { print "selected_coverage_level "; } ?>coverage_level_extended'>
					<input onclick="toggle_coverage_level(this)" type='radio' id="coverage_level_extended" name='s' value='1' <?php if ($feature_settings['status'] == "1") { print "checked "; } ?>> 
    				<label for="coverage_level_extended">Premium + Extended</label>
  				</li>
			</ul>
	
			</form>

<div class='optimization-coverage-config-box <?php if ($feature_settings['status'] == "1") {?>has-extended<?php } ?>'>
  <div class='premium-coverage-config'>
  <h3>Premium Coverage Configurations</h3>
	
	  
					
				
		
	  <ul>	
			<li <?php if ($feature_settings['premium']['use_temporary_foundation_optimizations'] == "1") { ?>class='selected-option'<?php } ?>>
				<div>
					<form method="post" style="display: inline-block"  class='feature-setting-change'>
						<input type="hidden" name="c" value="change-feature-attribute">
						<input type="hidden" name="a" value="premium.use_temporary_foundation_optimizations">
						<input type="hidden" name="f" value="<?php echo $feature; ?>">
						<input type="hidden" name="s" value="<?php echo $feature_settings['premium']['use_temporary_foundation_optimizations']; ?>">
						<input onchange="toggle_coverage_checkbox(this); submit_form(this)" class='material-selector' id='coverage-premium-use-intermediate-foundation-optimizations'  type="checkbox"  autocomplete="off"  <?php if ($feature_settings['premium']['use_temporary_foundation_optimizations'] == "1") { ?>checked<?php } ?> />
						<label class='material-selector' for="coverage-premium-use-intermediate-foundation-optimizations">Use Intermediate Foundation Optimizations
							<small>As the API is processing your optimization request, your page can be partially optimized using our foundation web performance features. </small>
							<small>While these
							optimizations won't make your page as fast as the full suite of premium transformations that the API performs, it can mean your page is partially covered should
							anyone visit your page while the optimization request is queued in the API.</small>
							<small>We recommend this feature to be enabled.</small>
						</label>	
					</form>
				</div>
			</li>					  
			  
			<li <?php if ($feature_settings['premium']['use_temporary_cache'] == "1") { ?>class='selected-option'<?php } ?>>
				<div>
					<form method="post" style="display: inline-block"  class='feature-setting-change'>
						<input type="hidden" name="c" value="change-feature-attribute">
						<input type="hidden" name="a" value="premium.use_temporary_cache">
						<input type="hidden" name="f" value="<?php echo $feature; ?>">
						<input type="hidden" name="s" value="<?php echo $feature_settings['premium']['use_temporary_cache']; ?>">
						<input onchange="toggle_coverage_checkbox(this); submit_form(this)" class='material-selector' id='coverage-premium-use-temporary-cache'  type="checkbox"  autocomplete="off"  <?php if ($feature_settings['premium']['use_temporary_cache'] == "1") { ?>checked<?php } ?>/>
						<label  class='material-selector' for="coverage-premium-use-temporary-cache">Cache Temporary Page
							<small>Caching enables the server to serve a temporary copy of your web page faster than if it had to dynamically build it.</small>
							<small>We recommend this feature to be enabled.</small>
						</label>
					</form>
				</div>
			</li>		
	
	</ul>	  
  </div>
  <div class='extended-coverage-config'>
	<h3>Extended Coverage Configurations</h3>
	 <ul>
 
			<li <?php if ($feature_settings['extended']['use_foundation_optimizations'] == "1") { ?>class='selected-option'<?php } ?>>
				<div>	
					<form method="post" style="display: inline-block"  class='feature-setting-change'>
						<input type="hidden" name="c" value="change-feature-attribute">
						<input type="hidden" name="a" value="extended.use_foundation_optimizations">
						<input type="hidden" name="f" value="<?php echo $feature; ?>">
						<input type="hidden" name="s" value="<?php echo $feature_settings['extended']['use_foundation_optimizations']; ?>">

						<input onchange="toggle_coverage_checkbox(this); submit_form(this)" class='material-selector' id='coverage-extended-use-foundation-optimizations'  type="checkbox"  autocomplete="off" <?php if ($feature_settings['extended']['use_foundation_optimizations'] == "1") { ?>checked<?php } ?> />
						<label class='material-selector' for="coverage-extended-use-foundation-optimizations">Apply Foundation Optimizations
							<small>While these
							optimizations won't make your page as fast as the full suite of premium transformations that the API performs, it can mean your page is somewhat faster than if no optimizations were performed at all.</small>
							<small>We recommend this feature to be enabled.</small>
						</label>	
					</form>
				</div>
			</li>					  
			  
			<li <?php if ($feature_settings['extended']['use_cache'] == "1") { ?>class='selected-option'<?php } ?>>
				<div>
					<form method="post" style="display: inline-block"  class='feature-setting-change'>
						<input type="hidden" name="c" value="change-feature-attribute">
						<input type="hidden" name="a" value="extended.use_cache">
						<input type="hidden" name="f" value="<?php echo $feature; ?>">
						<input type="hidden" name="s" value="<?php echo $feature_settings['extended']['use_cache']; ?>">

						<input onchange="toggle_coverage_checkbox(this); submit_form(this)" class='material-selector' id='coverage-extended-use-cache'  type="checkbox"  autocomplete="off" <?php if ($feature_settings['extended']['use_cache'] == "1") { ?>checked<?php } ?>/>
						<label class='material-selector' for="coverage-extended-use-cache">Use Page Caching
							<small>Caching enables the server to serve a copy of your web page faster than if the website had to dynamically build it each time.</small>
							<small>We recommend this feature to be <b>enabled</b> unless you are using ecommerce or forums.</small>
						</label>
					</form>
				</div>
			</li>
		 	<li <?php if ($feature_settings['extended']['do_categories'] == "1") { ?>class='selected-option'<?php } ?>>
				<div>
					<form method="post" style="display: inline-block"  class='feature-setting-change'>
						<input type="hidden" name="c" value="change-feature-attribute">
						<input type="hidden" name="a" value="extended.do_categories">
						<input type="hidden" name="f" value="<?php echo $feature; ?>">
						<input type="hidden" name="s" value="<?php echo $feature_settings['extended']['do_categories']; ?>">

						<input onchange="toggle_coverage_checkbox(this); submit_form(this)" class='material-selector' id='coverage-extended-do-categories'  type="checkbox"  autocomplete="off" <?php if ($feature_settings['extended']['do_categories'] == "1") { ?>checked<?php } ?>/>
						<label class='material-selector' for="coverage-extended-do-categories">Extend To WordPress Categories
							<small>Extend the coverage to categories, such as '<?php echo get_home_url(); ?>/category/some_category/'.</small>
							<small>We recommend this feature to be <b>enabled</b> as cateories are often spidered and sometimes browsed.</small>
						</label>
					</form>
				</div>
			</li>
			<li <?php if ($feature_settings['extended']['do_tags'] == "1") { ?>class='selected-option'<?php } ?>>
				<div>
					<form method="post" style="display: inline-block"  class='feature-setting-change'>
						<input type="hidden" name="c" value="change-feature-attribute">
						<input type="hidden" name="a" value="extended.do_tags">
						<input type="hidden" name="f" value="<?php echo $feature; ?>">
						<input type="hidden" name="s" value="<?php echo $feature_settings['extended']['do_tags']; ?>">

						<input onchange="toggle_coverage_checkbox(this); submit_form(this)" class='material-selector' id='coverage-extended-do-tags'  type="checkbox"  autocomplete="off" <?php if ($feature_settings['extended']['do_tags'] == "1") { ?>checked<?php } ?>/>
						<label class='material-selector' for="coverage-extended-do-tags">Extend To WordPress Tags
							<small>Extend the coverage to tags, such as '<?php echo get_home_url(); ?>/tags/some_tag/'.</small>
							<small>We recommend this feature to be <b>disabled</b> as tags are normally not spidered or browsed.</small>
						</label>
					</form>
				</div>
			</li>
					 
	
	</ul> 
	  
  </div>
</div>