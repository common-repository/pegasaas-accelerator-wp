<div class="row setup-content" id="step-2" style="display: none;">
      		<div class="col-md-6 col-md-offset-3">
        		<div class="col-md-12">
          		<h3><?php _e("API Key", "pegasaas-accelerator"); ?></h3>
		   		<?php if ($pegasaas->is_standard()) { ?>
				<style>#pro-api-key-container { display: none;}</style>
		   		<p><?php _e("An API key is required to access the Pegasaas API to optimize your pages, fetch PageSpeed scores, and perform image optimization.", "pegasaas-accelerator"); ?></p>
			
				<ul class='api-key-type'>
			  		<li class='selected_key_type'><input type='radio' id="api_key_type_quick" name='api_key_type' value='quick' checked> <label for="api_key_type_quick">Request Guest API Key (100 Monthly Optimization Credits)</label>
			  			<div id="api-request-email">
				  			<div class="form-group <?php if ($pegasaas->settings['reason'] != "" && $_POST['c'] == "register-api-key") { ?>has-error<?php } ?>">
								<?php if ($pegasaas->settings['reason'] != "" && $_POST['c'] == "register-api-key") { ?>
								<p><?php echo $pegasaas->settings['reason']; ?></p>
								<?php } ?>
								<div class="input-group">
  									<span class="input-group-addon"><i class="fa fa-envelope  fa-fw"></i></span>
				  					<input class='form-control' placeholder="E-Mail Address" type='email' value="<?php echo get_option("admin_email"); ?>" name="api_request_email"/>
								</div>
								<p class='email-address-description'><b><?php echo get_option("admin_email"); ?></b> is the e-mail address in your WordPress settings.  You may use this e-mail address, or change it to any valid email address that you own.  A guest API account will be created for you.</p>
							</div>
						</div>
					</li>
			  		<li><input type='radio' id="api_key_type_pro"  name='api_key_type' value='pro'> <label for="api_key_type_pro">Use Premium API Key</label> <a style='margin-left: 10px;' target='_blank' class='btn btn-xs btn-info' href='https://pegasaas.com/compare-products/?utm_source=pegasaas-accelerator-install' target='_blank'>compare plans <i class='fa fa-external-link'></i></a>
			  
		   		<?php } ?>
						<div id="pro-api-key-container">
		  					<p>The API Key is available from your account at <a href='https://pegasaas.com/manage/?utm_source=pegasaas-accelerator-install' target='_blank'>pegasaas.com</a>.  If you have not
							yet signed up for an account, you can <a href='https://pegasaas.com/pricing/?utm_source=pegasaas-accelerator-install' target='_blank'>get one here</a>.</p>
			
						
		
          
							<div class="form-group <?php if ($pegasaas->settings['reason'] != "" && $_POST['c'] == "register-api-key") { ?>has-error<?php } ?>">
								<div class="input-group">
  									<span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
  									<input class="form-control" type="text" placeholder='API Key' name='api_key' value='<?php echo $_POST['api_key']; ?>'>
								</div>
			
				 				<?php if ($pegasaas->settings['reason'] != "" && $_POST['api_key'] != "") { ?>
								<div class='help-block' style='margin-left: 50px; font-size: 10px; font-weight: bold; '><?php echo $pegasaas->settings['reason']; ?></div>
								<?php } ?>
          					</div>
						</div>
					<?php if ($pegasaas->is_standard()) { ?>
					</li>
				</ul>
				<script>
			  	jQuery(".api-key-type input[type=radio]").on("change", function() {
				  jQuery(this).parents("ul").find("li").removeClass("selected_key_type");
				  jQuery(this).parents("li").addClass("selected_key_type");
				  if (jQuery(this).attr("value") == "pro") { 
				    jQuery("#pro-api-key-container").css("display", "block");
				    jQuery("#api-request-email").css("display", "none");
				  } else {
					 jQuery("#pro-api-key-container").css("display", "none"); 
					  jQuery("#api-request-email").css("display", "block");
				  }
			  	});
				</script>		
					<?php } ?>        
          		<button class="btn btn-primary nextBtn pull-right" type="button" >Continue <i class='fa fa-angle-right'></i></button>
        	</div>
      	</div>
    </div>