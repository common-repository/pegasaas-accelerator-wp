<?php if ($pegasaas->is_pro()) { ?>					
					<div class='hidden-novice hidden-intermediate'>
				<b>When To Load:</b>	
					<br/>
				<form method="post" style="display: inline-block" class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-status">
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>							
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="1">Auto Detect (Default)</option>  
				  <option value="3" <?php if ($feature_settings['status'] == "3") { ?>selected<?php } ?>>As Soon As Possible</option>
				  <option value="4" <?php if ($feature_settings['status'] == "4") { ?>selected<?php } ?>>After 1.5 Seconds</option>
				  <option value="5" <?php if ($feature_settings['status'] == "5") { ?>selected<?php } ?>>After 5.0 Seconds</option>
				  <option value="2" <?php if ($feature_settings['status'] == "2") { ?>selected<?php } ?>>On Action (Scroll/Click)</option>
				</select>
			</form>					
					<br/><br/>
	<b>Action Type:</b>	
		 <ul>
 
			<li>
				<div>	
					<form method="post" style="display: inline-block"  class='feature-setting-change defer-unused-css-form'>
						<input type="hidden" name="c" value="change-feature-attribute">
						<input type="hidden" name="a" value="on_action.click">
						<input type="hidden" name="f" value="<?php echo $feature; ?>">
						<input type="hidden" name="s" value="<?php echo $feature_settings['on_action']['click']; ?>">

						<input onchange="if (toggle_action_type_checkbox(this)) { submit_form(this); }" class='material-selector' id='defer_unused_css_action_click'  type="checkbox"  autocomplete="off" <?php if ($feature_settings['on_action']['click'] == "1") { ?>checked<?php } ?> />
						<label class='material-selector' for="defer_unused_css_action_click">On Click
							<small>If the user clicks their mouse, then the deferred CSS will load</small>
						</label>	
					</form>
				</div>
			</li>					  
			  
			<li>
				<div>
					<form method="post" style="display: inline-block"  class='feature-setting-change defer-unused-css-form'>
						<input type="hidden" name="c" value="change-feature-attribute">
						<input type="hidden" name="a" value="on_action.scroll">
						<input type="hidden" name="f" value="<?php echo $feature; ?>">
						<input type="hidden" name="s" value="<?php echo $feature_settings['on_action']['scroll']; ?>">

						<input onchange="if (toggle_action_type_checkbox(this)) { submit_form(this); }" class='material-selector' id='defer_unused_css_action_scroll'  type="checkbox"  autocomplete="off" <?php if ($feature_settings['on_action']['scroll'] == "1") { ?>checked<?php } ?>/>
						<label class='material-selector' for="defer_unused_css_action_scroll">On Scroll
							<small>Once the user scrolls the page, the CSS files will load</small>
						</label>
					</form>
				</div>
			</li>		
	
	</ul> 	
								
						
						</div>


<?php } ?>																  
					<div class='hidden-novice'>
						<b>Exclude CSS Files:</b><br/>
										<form method="post" style="display: block" class='feature-setting-change'>
				<input type="hidden" name="c" value="change-local-setting">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>													
					<input type="hidden" name="f" value="defer_unused_css_exclude_stylesheets">
						<textarea placeholder="/path/to/stylesheet.css" class='form-control form-control-full' name='s'><?php echo $feature_settings['exclude_stylesheets']; ?></textarea>
					<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
			</form>
						</div>