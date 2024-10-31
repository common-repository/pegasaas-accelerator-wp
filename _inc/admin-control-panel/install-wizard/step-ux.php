	<div class="row setup-content" id="step-ux" style="display: none">
		      <div class="col-md-10 col-md-offset-1"> 
        <div class="col-md-12">
	  <h3>Experience Level</h3>
	
			<p class='text-center'>To give you the best user experience, we will tailor the interface to your experience level.</p>
			<p class='text-center'>How would you describe your level of experience in the field of Web Performance Optimization?</p>
		
			<ul class='interface-level'>
			  	<li class='<?php if ($selected_interface_level == "0") { print "selected_interface_experience "; } ?>interface_level_novice'><input type='radio' id="interface_level_novice" name='interface_level' value='0' <?php if ($selected_interface_level == "0") { print "checked "; } ?>> 
					<label for="interface_level_novice">Novice</label>
				</li>
				<li class='<?php if ($selected_interface_level == "1") { print "selected_interface_experience "; } ?>interface_level_intermediate' ><input type='radio' id="interface_level_intermediate" name='interface_level' value='1' <?php if ($selected_interface_level == "1") { print "checked "; } ?>> 
					<label for="interface_level_intermediate">Intermediate</label>
				</li>
				<li class='<?php if ($selected_interface_level == "2") { print "selected_interface_experience "; } ?>interface_level_advanced'><input type='radio' id="interface_level_advanced" name='interface_level' value='2' <?php if ($selected_interface_level == "2") { print "checked "; } ?>> 
					<label for="interface_level_advanced">Advanced</label>
			  			
				</li>				  
			</ul>	
			<div id="interface_level_novice_description" class="experience-level-description <?php if ($selected_interface_level == "0") { print "visible"; } ?>">
			  <h4>Novice</h4>
			  <p>If you don't want all the techno-mumbo-jumbo, that's cool with us.  We'll show you how much faster your site is, and automate all the settings.  This mode is best for the most stable performance.</p>
			</div>
			<div id="interface_level_intermediate_description" class="experience-level-description <?php if ($selected_interface_level == "1") { print "visible"; } ?>">
			  <h4>Intermediate</h4>
			  <p>If you want to know your site metrics, such as First Contentful Paint or Time to Interactive, this is the setting for you. We'll automate most settings, but will give you the ability
				  to change a few more options.</p>

			</div>
			<div id="interface_level_advanced_description" class="experience-level-description <?php if ($selected_interface_level == "2") { print "visible"; } ?>">
			    <h4>Advanced</h4>
			  <p>This setting is reserved for GURUs -- if this is you, awesome!  With this level, all the "safeties" are off. This means you can do more, but that the risk of hitting turbulence
				is increased.  </p>
			</div>
			
				  </div>
		</div>
		
				<div class='col-xs-12 btn-row'>
				  <button class="btn btn-primary nextBtn pull-right" type="button" >Continue <i class='fa fa-angle-right'></i></button>
				  <button class="btn btn-default backBtn pull-left" type="button" ><i class='fa fa-angle-left'></i> Back</button>
				</div>
									<script>
			  	jQuery(".interface-level input[type=radio]").on("change", function() {
				  jQuery(this).parents("ul").find("li").removeClass("selected_interface_experience");
				  jQuery(this).parents("li").addClass("selected_interface_experience");
				  jQuery(this).parents(".setup-content").find(".experience-level-description").removeClass("visible");
				
				  if (jQuery(this).attr("value") == "0") { 
					jQuery("#interface_level_novice_description").addClass("visible");
			  
				  } else if (jQuery(this).attr("value") == "1") { 
					jQuery("#interface_level_intermediate_description").addClass("visible");
			  	 
				  } else if (jQuery(this).attr("value") == "2") { 
					jQuery("#interface_level_advanced_description").addClass("visible");
				  }
			  	});
				</script>
	  </div>  