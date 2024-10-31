	<div class="row setup-content" id="step-1" >
		      <div class="col-md-6 col-md-offset-3">
        <div class="col-md-12">
	  <h3>Welcome!</h3>
			<p>This setup wizard is designed to get the Pegasaas Accelerator WP plugin initialized and automatically optimizing your pages for you in just a few short steps.</p>
			<p>Optimizations happen in the background, while you continue working with the rest of your site.  If at any time you want to revert back to your original un-optimized site, simply
			enable "diagnostic" mode, or disable the plugin.</p>
			<h4>Let's get Started!</h4>
			<p class='text-center'>Which theme would you like for the interface?</p>
		
			<ul class='interface-theme'>
			  	<li class='selected_interface_theme interface_type_light'><input type='radio' id="interface_type_light" name='interface_type' value='1' checked> 
					<label for="interface_type_light">LIGHT</label>
			  			
				</li>
				<li class='interface_type_dark' ><input type='radio' id="interface_type_dark" name='interface_type' value='0'> 
					<label for="interface_type_dark">DARK</label>
			  			
				</li>
				<li class='interface_type_plain' ><input type='radio' id="interface_type_plain" name='interface_type' value='2'> 
					<label for="interface_type_plain">PLAIN</label>
			  			
				</li>				  
			</ul>
				  <button class="btn btn-primary nextBtn pull-right" type="button" >Continue <i class='fa fa-angle-right'></i></button>
				  </div>
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