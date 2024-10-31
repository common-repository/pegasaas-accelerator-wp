				<form method="post" style="display: block" target="hidden-frame" class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-status">
					<input type='hidden' name='prompt' value='clear_cache' />
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
					 
			<ul class='speed-configuration'>
			  	<li class='<?php if ($feature_settings['status'] == "1") { print "selected_speed_configuration "; } ?>speed_configuration_basic'>
					<input onclick="toggle_speed_configuration(this)" type='radio' id="speed_level_basic" name='s' value='1' <?php if ($feature_settings['status'] == "1") { print "checked "; } ?>> 
    				<label for="speed_level_basic">Basic</label> 
  				</li>
			  	<li class='<?php if ($feature_settings['status'] == "2") { print "selected_speed_configuration "; } ?>speed_configuration_supersonic'>
					<input onclick="toggle_speed_configuration(this)" type='radio' id="speed_level_supersonic" name='s' value='2' <?php if ($feature_settings['status'] == "2") { print "checked "; } ?>> 
    				<label for="speed_level_supersonic">Supersonic</label>
  				</li>
			  	<li class='<?php if ($feature_settings['status'] == "3") { print "selected_speed_configuration "; } ?>speed_configuration_hypersonic'>
					<input onclick="toggle_speed_configuration(this)" type='radio' id="speed_level_hypersonic" name='s' value='3' <?php if ($feature_settings['status'] == "3") { print "checked "; } ?>> 
    				<label for="speed_level_hypersonic">Hypersonic</label>
  				</li>	
			  	<li class='<?php if ($feature_settings['status'] == "4") { print "selected_speed_configuration "; } ?>speed_configuration_beastmode'>
					<input onclick="return toggle_speed_configuration(this)" type='radio' id="speed_level_beastmode" name='s' value='4' <?php if ($feature_settings['status'] == "4") { print "checked "; } ?>> 
    				<label for="speed_level_beastmode">Beast Mode</label>
  				</li>				
				<li class='<?php if ($feature_settings['status'] == "0") { print "selected_speed_configuration "; } ?>speed_configuration_manual'>
					<input onclick="toggle_speed_configuration(this)" type='radio' id="speed_level_manual" name='s' value='0' <?php if ($feature_settings['status'] == "0") { print "checked "; } ?>> 
    				<label for="speed_level_manual">Manual</label>
  				</li>
			</ul>
	
					
					
<div id="speed_configuration_basic_description" class="speed-configuration-description <?php if ($feature_settings['status'] == "1") { print "visible"; } ?>">
			  <h4>Basic</h4>
			  <p>This is the lowest of the auto-pilot settings and is best for the most stable performance.  In this mode standard features such as Page Caching, Image Optimization, Lazy Loading, and Minification are enabled.</p>
			</div>
			<div id="speed_configuration_supersonic_description" class="speed-configuration-description <?php if ($feature_settings['status'] == "2") { print "visible"; } ?>">
			  <h4>Supersonic</h4>
			  <p>In SUPERSONIC mode, Pegasaas is automatically boosting your site by enabling more advanced features such as render blocking resource deferral, and advanced page conditioning.</p>

			</div>
			<div id="speed_configuration_hypersonic_description" class="speed-configuration-description <?php if ($feature_settings['status'] == "3") { print "visible"; } ?>">
			    <h4>Hypersonic</h4>
			  <p>HYPERSONIC is the max-stable auto-pilot setting, where 90% of sites can operate without display issues. In this mode, many additional resources are lazy loaded and deferred.</p>
			</div>
			<div id="speed_configuration_beastmode_description" class="speed-configuration-description <?php if ($feature_settings['status'] == "4") { print "visible"; } ?>">
			    <h4>Beast Mode</h4>
			  <p>In BEAST MODE, the auto-pilot system activates NEXT GENERATION features only available to PREMIUM subscribers.  These features can result in much faster load times, but also may cause
				a some degree of instability.</p>
			</div>	
			<div id="speed_configuration_manual_description" class="speed-configuration-description <?php if ($feature_settings['status'] == "0") { print "visible"; } ?>">
			    <h4>Manual</h4>
			  <p>In MANUAL mode, you take the reins and configure the plugin as you see fit.  By default, the system is configured with "HYPERSONIC"
				  presets -- you can enable or disable each feature as you wish.</p>
			</div>					
					<script>
			  	jQuery(".speed-configuration input[type=radio]").on("change", function() {
					
				  jQuery(this).parents("ul").find("li").removeClass("selected_speed_configuration");
				  jQuery(this).parents("li").addClass("selected_speed_configuration");
				  jQuery(this).parents(".feature-setting-change").find(".speed-configuration-description").removeClass("visible");
				
				  if (jQuery(this).attr("value") == "1") { 
					jQuery("#speed_configuration_basic_description").addClass("visible");
			  
				  } else if (jQuery(this).attr("value") == "2") { 
					jQuery("#speed_configuration_supersonic_description").addClass("visible");
			  	 
				  } else if (jQuery(this).attr("value") == "3") { 
					jQuery("#speed_configuration_hypersonic_description").addClass("visible");
				  } else if (jQuery(this).attr("value") == "4") { 
					jQuery("#speed_configuration_beastmode_description").addClass("visible");
				  } else if (jQuery(this).attr("value") == "0") { 
					jQuery("#speed_configuration_manual_description").addClass("visible");
				  }
			  	});
				</script>
			</form>	