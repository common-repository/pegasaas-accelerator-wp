	<div class="row setup-content" id="step-go" style="display: none">
		      <div class="col-md-10 col-md-offset-1"> 
        <div class="col-md-12">
	  <h3>Let's Go!</h3>
	
			<p class='text-center'>Before we tell Pegasaas to start optimizing your pages, tell us if you want to go LIVE with the optimizations 
			immediately, or if you want to review your optimizations in a "Staging" mode first.</p>
		
			<ul class='mode-level'>
			  	<li class='<?php if ($selected_system_mode == "live") { print "selected_system_mode "; } ?>system_mode_live'><input type='radio' id="system_mode_live" name='system_mode' value='live' <?php if ($selected_system_mode == "live") { print "checked "; } ?>> 
					<label for="system_mode_live">LIVE</label>
				</li>
				<li class='<?php if ($selected_system_mode == "staging") { print "selected_system_mode "; } ?>system_mode_staging' ><input type='radio' id="system_mode_staging" name='system_mode' value='staging' <?php if ($selected_system_mode == "staging") { print "checked "; } ?>> 
					<label for="system_mode_staging">Staging</label>
				</li>			  
			</ul>	
			<div id="system_mode_live_description" class="mode-level-description <?php if ($selected_system_mode == "live") { print "visible"; } ?>">
			  <h4>LIVE</h4>
			  <p>Use this mode if you want Pegasaas to serve your optimized pages immediately.  You can always switch to Staging mode at a later time.</p>
			</div>
			<div id="system_mode_staging_description" class="mode-level-description <?php if ($selected_system_mode == "staging") { print "visible"; } ?>">
			  <h4>Staging</h4>
			  <p>If this is a production environment or you want to preview optimizations prior to going live, use this mode.  You can always switch to Live mode at a later time.</p>

			</div>

			
				  </div>
		</div>
		
				<div class='col-xs-12 btn-row'>
					<button class="pull-right btn btn-primary" type="submit">Complete Setup</button>
				

				  <button class="btn btn-default backBtn pull-left" type="button" ><i class='fa fa-angle-left'></i> Back</button>
				</div>
		
									<script>
			  	jQuery(".mode-level input[type=radio]").on("change", function() {
				  jQuery(this).parents("ul").find("li").removeClass("selected_system_mode");
				  jQuery(this).parents("li").addClass("selected_system_mode");
				  jQuery(this).parents(".setup-content").find(".mode-level-description").removeClass("visible");
				
				  if (jQuery(this).attr("value") == "live") { 
					jQuery("#system_mode_live_description").addClass("visible");
			  
				  } else if (jQuery(this).attr("value") == "staging") { 
					jQuery("#system_mode_staging_description").addClass("visible");
			  	 
				  } 
			  	});
				</script>
	  </div>  