	<div class="row setup-content" id="step-ui" style="display: none">
		      <div class="col-md-6 col-md-offset-3">
        <div class="col-md-12">
	  <h3>Interface Preference</h3>
			<p class='text-center'>Please tell us how you would like the user interface set up.  Which theme would you like to use?</p>
	
			
		
			<ul class='interface-theme'>
			  	<li class='<?php if ($selected_interface_type == "1") { print "selected_interface_theme "; } ?>interface_type_light'><input type='radio' id="interface_type_light" name='interface_type' value='1' <?php if ($selected_interface_type == "1") { print "checked "; } ?>> 
					<label for="interface_type_light">DEFAULT</label>
			  			
				</li>
				<li class='<?php if ($selected_interface_type == "0") { print "selected_interface_theme "; } ?>interface_type_dark' ><input type='radio' id="interface_type_dark" name='interface_type' value='0' <?php if ($selected_interface_type == "0") { print "checked "; } ?>> 
					<label for="interface_type_dark">DARK</label>
			  			
				</li>
				<li class='<?php if ($selected_interface_type == "2") { print "selected_interface_theme "; } ?>interface_type_plain' ><input type='radio' id="interface_type_plain" name='interface_type' value='2' <?php if ($selected_interface_type == "2") { print "checked "; } ?>> 
					<label for="interface_type_plain">LIGHT</label>
			  			
				</li>				  
			</ul>
			
		
		

				  </div>
				  
		</div>
			<div class='col-xs-12 btn-row'>			
				<button class="btn btn-primary nextBtn pull-right" type="button" >Continue <i class='fa fa-angle-right'></i></button>
				  <button class="btn btn-default backBtn pull-left" type="button" ><i class='fa fa-angle-left'></i> Back</button>
				</div>
		
									<script>
			  	jQuery(".interface-theme input[type=radio]").on("change", function() {
				  jQuery(this).parents("ul").find("li").removeClass("selected_interface_theme");
				  jQuery(this).parents("li").addClass("selected_interface_theme");
				  
				  if (jQuery(this).attr("value") == "0") { 
					  jQuery("#pegasaas-accelerator-admin-styles-light-css").remove();
					  jQuery("#pegasaas-accelerator-admin-styles-plain-css").remove();
					  jQuery("#plugin-logo").attr("src", "<?php echo PEGASAAS_ACCELERATOR_URL; ?>/assets/images/pegasaas-accelerator-horizontal-logo-light.png");
			  
				  } else if (jQuery(this).attr("value") == "1") { 
					
					  jQuery("#plugin-logo").attr("src", "<?php echo PEGASAAS_ACCELERATOR_URL; ?>/assets/images/pegasaas-accelerator-horizontal-logo-blue.png");

					  jQuery("head").append("<link id='pegasaas-accelerator-admin-styles-light-css' rel='stylesheet' href='<?php echo PEGASAAS_ACCELERATOR_URL; ?>/assets/css/light.css' type='text/css'>");
  jQuery("#pegasaas-accelerator-admin-styles-plain-css").remove();
					 
				  } else if (jQuery(this).attr("value") == "2") { 
					
					  jQuery("#plugin-logo").attr("src", "<?php echo PEGASAAS_ACCELERATOR_URL; ?>/assets/images/pegasaas-accelerator-horizontal-logo-dark.png");

					  jQuery("head").append("<link id='pegasaas-accelerator-admin-styles-plain-css' rel='stylesheet' href='<?php echo PEGASAAS_ACCELERATOR_URL; ?>/assets/css/plain.css' type='text/css'>");
  	jQuery("#pegasaas-accelerator-admin-styles-light-css").remove();
					 
				  }
			  	});
				</script>
	  </div>  