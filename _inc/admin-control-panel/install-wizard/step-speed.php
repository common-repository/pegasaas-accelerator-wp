	<div class="row setup-content" id="step-speed" style="display: none">
		      <div class="col-md-10 col-md-offset-1"> 
        <div class="col-md-12">
	  <h3>Auto Pilot Level</h3>
	

			<p class='text-center'>We recommend starting on the "HYPERSONIC" auto-pilot setting.  If you experience display issues with your site when using this setting, you can dial it back to "SUPERSONIC" or "BASIC".</p>
			<p class='text-center'>If you're into manually tweaking the settings, you can take the reins yourself and switch to "Manual". </p>
			<ul class='speed-configuration'>
			  	<li class='<?php if ($selected_speed_configuration == "1") { print "selected_speed_configuration "; } ?>speed_configuration_basic'><input type='radio' id="speed_configuration_basic" name='speed_configuration' value='1' <?php if ($selected_speed_configuration == "1") { print "checked "; } ?>> 
					<label for="speed_configuration_basic">Basic</label>
				</li>
				<li class='<?php if ($selected_speed_configuration == "2") { print "selected_speed_configuration "; } ?>speed_configuration_supersonic' ><input type='radio' id="speed_configuration_supersonic" name='speed_configuration' value='2' <?php if ($selected_speed_configuration == "2") { print "checked "; } ?>> 
					<label for="speed_configuration_supersonic">Supersonic</label>
				</li>
				<li class='<?php if ($selected_speed_configuration == "3") { print "selected_speed_configuration "; } ?>speed_configuration_hypersonic'><input type='radio' id="speed_configuration_hypersonic" name='speed_configuration' value='3' <?php if ($selected_speed_configuration == "3") { print "checked "; } ?>> 
					<label for="speed_configuration_hypersonic">Hypersonic</label>
				</li>
				<li class='<?php if ($selected_speed_configuration == "4") { print "selected_speed_configuration "; } ?>speed_configuration_beastmode'><input type='radio' id="speed_configuration_beastmode" name='speed_configuration' value='4' <?php if ($selected_speed_configuration == "4") { print "checked "; } ?>> 
					<label for="speed_configuration_beastmode">Beast Mode</label>
			  			
				</li>					
			</ul>	
			<div id="speed_configuration_basic_description" class="speed-configuration-description <?php if ($selected_speed_configuration == "1") { print "visible"; } ?>">
			  <h4>Basic</h4>
			  <p>This is the lowest of the auto-pilot settings and is best for the most stable performance.  In this mode standard features such as Page Caching, Image Optimization, Lazy Loading, and Minification are enabled.</p>
			</div>
			<div id="speed_configuration_supersonic_description" class="speed-configuration-description <?php if ($selected_speed_configuration == "2") { print "visible"; } ?>">
			  <h4>Supersonic</h4>
			  <p>In SUPERSONIC mode, Pegasaas is automatically boosting your site by enabling more advanced features such as render blocking resource deferral, and advanced page conditioning.</p>

			</div>
			<div id="speed_configuration_hypersonic_description" class="speed-configuration-description <?php if ($selected_speed_configuration == "3") { print "visible"; } ?>">
			    <h4>Hypersonic</h4>
			  <p>HYPERSONIC is the max-stable auto-pilot setting, where 90% of sites can operate without display issues. In this mode, many additional resources are lazy loaded and deferred.</p>
			</div>
			<div id="speed_configuration_beastmode_description" class="speed-configuration-description <?php if ($selected_speed_configuration == "4") { print "visible"; } ?>">
			    <h4>Beast Mode</h4>
			  <p>In BEAST MODE, the auto-pilot system activates NEXT GENERATION features only available to PREMIUM subscribers.  These features can result in much faster load times, but also may cause
				a some degree of instability.</p>
			</div>			
				  </div>
		</div>
		
				<div class='col-xs-12 btn-row'>
				  <button class="btn btn-primary nextBtn pull-right" type="button" >Continue <i class='fa fa-angle-right'></i></button>
				  <button class="btn btn-default backBtn pull-left" type="button" ><i class='fa fa-angle-left'></i> Back</button>
				</div>
									<script>
			  	jQuery(".speed-configuration input[type=radio]").on("change", function() {
					
				  jQuery(this).parents("ul").find("li").removeClass("selected_speed_configuration");
				  jQuery(this).parents("li").addClass("selected_speed_configuration");
				  jQuery(this).parents(".setup-content").find(".speed-configuration-description").removeClass("visible");
				
				  if (jQuery(this).attr("value") == "1") { 
					jQuery("#speed_configuration_basic_description").addClass("visible");
			  
				  } else if (jQuery(this).attr("value") == "2") { 
					jQuery("#speed_configuration_supersonic_description").addClass("visible");
			  	 
				  } else if (jQuery(this).attr("value") == "3") { 
					jQuery("#speed_configuration_hypersonic_description").addClass("visible");
				  } else if (jQuery(this).attr("value") == "4") { 
					jQuery("#speed_configuration_beastmode_description").addClass("visible");
				  }
			  	});
				</script>
	  </div>  