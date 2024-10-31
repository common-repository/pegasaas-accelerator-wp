
	<?php 


if ($pages_accelerated['accelerated'] == 0 && $pages_accelerated['pending'] == 0) { ?>
<style>
	#system-mode { display: none; }
	#pegasaas-scores-wrapper { display: none; }
	
</style>
	<div class='no-pages-accelerated-welcome'>
		<div class='install-wizard-container'>
	<h1 class='setup-wizard-title'>Welcome</h2>
	<p class='setup-wizard-tagline text-center'>It looks like you're all set up!  All you need to do is turn on acceleration for your pages.</p>
	<p class='setup-wizard-tagline text-center'>Use the options below, or watch our tutorial
	that shows to manually turn on acceleration for indidvidual posts.</p>
	<h3>Auto Acceleration Options</h3>
			<p>Choose how much of your site you wish to initially accelerate. You can always accelerate individual or groups of pages/posts after the initial setup.
			</p>
<?php 
													 $site_composition = $pegasaas->utils->get_site_composition();
													 $total_columns = 1;
			if ($site_composition['post_types']['post']['count'] > 0) { $total_columns++; }
			if ($site_composition['post_types']['page']['count'] > 0) { $total_columns++; }
			$acceleration_type = "manual";
			?>
		  <form role="form" action="" method="post" style="position: relative">
			  <input type='hidden' value='re-init-accelerated-pages' name='c' />
			  <ul class='auto-acceleration-options'>
	<ul class='auto-acceleration-options'>
			<!-- home page -->	  
			<li <?php if ($acceleration_type == "home-page") { ?>class='selected-option'<?php } ?>>
				<div class='pull-right'><button class="btn btn-success btn-lg pull-right" type="submit">Accelerate!</button></div>

				<div>		
					<input class='material-selector' id='acceleration-type-home-page'  type="radio"  autocomplete="off" name='acceleration-type' value='home-page' <?php if ($acceleration_type == "home-page") { ?>checked<?php } ?> />
					<label  class='ptoi-type material-selector' for="acceleration-type-home-page">Home Page
						<small>Just your Home page.  You can always accelerate other pages later.</small>
					</label>	
				</div>
			</li>					  
			<!-- everything -->   
			<li <?php if ($acceleration_type == "all") { ?>class='selected-option'<?php } ?>>
					<div class=' pull-right'><button class="btn btn-success btn-lg pull-right" type="submit">Accelerate!</button></div>
			<div>
					<input class='material-selector' id='acceleration-type-everything'  type="radio"  autocomplete="off" name='acceleration-type' value='all' <?php if ($acceleration_type == "all") { ?>checked<?php } ?>/>
					<label  class='ptoi-type material-selector' for="acceleration-type-everything">Everything
						<small>Enable acceleration on all of your pages.  You can always disable acceleration on any page or post afterwards.</small>
					</label>
				</div>
			</li>

				  
				  
			<!-- Custom -->  
			<li <?php if ($acceleration_type == "advanced") { ?>class='selected-option'<?php } ?>>
				<div class='pull-right'><button class="btn btn-success btn-lg pull-right" type="submit">Accelerate!</button></div>
				<div>
				<input class='material-selector' id='acceleration-type-advanced'  type="radio"  autocomplete="off" name='acceleration-type' value='advanced'  <?php if ($acceleration_type == "advanced") { ?>checked<?php } ?> />
				<label  class='ptoi-type material-selector' for="acceleration-type-advanced">Custom
					<small>Choose only the pages or posts you want to accelerate at this time.  Click the post type to expand the list of posts.</small>
				</label>
					
						
	<div class='row'>
	  <div class='accelerate-type-options'>
		<div class='col-sm-12'>
			
			
		<div class="panel-group accordion" id="post-type-options-accordion" role="tablist" aria-multiselectable="true">
<?php
	$post_type_increment = 0;
			$post_type_item_increment = 0;
	foreach ($site_composition['post_types'] as $pt_id => $post_type) {  
		$post_type_increment++; 
		
		if ($post_type['count'] > 0) { ?>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingOne">
      <h4 class="panel-title">
        <input type='checkbox' rel='<?php echo $pt_id; ?>' id='pto-group-<?php echo $pt_id; ?>' class='material-selector post-type-option' value='1'>
		  <label class='material-selector' for='pto-group-<?php echo $pt_id; ?>'>&nbsp;</label>
		  <a role="button" data-toggle="collapse" data-parent="#post-type-options-accordion" href="#collapse_pto_<?php echo $post_type_increment; ?>" aria-expanded="true" aria-controls="collapseOne">
           <?php echo $post_type['name']; ?>
		  </a>  
			<span class='badge badge-default pull-right'>0 of <?php echo $post_type['count']; ?></span>
        
      </h4>
    </div>
    <div id="collapse_pto_<?php echo $post_type_increment; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
		  
          <ul class='ptoi-items'>
		    <?php 
				$posts = $pegasaas->utils->get_posts(array("post_type" => $pt_id, "post_status" => "publish", "limit" => 100, "orderby" => "post_title", "order" => "asc"));
				foreach ($posts as $post) { 
					$post_type_item_increment++;
//var_dump($post);?>
			  <li class='ptoi-item'><input id='ptoi-<?php echo $post_type_item_increment; ?>' type='checkbox' rel='item_<?php echo $pt_id; ?>' class='pto-checkbox material-selector' name='enable_acceleration_on[]' <?php if ($enable_pages["{$post->slug}"]) { print 'checked'; } ?> value="<?php echo $post->slug;?>" /><label for='ptoi-<?php echo $post_type_item_increment; ?>' class='ptoi-label material-selector'><?php echo $post->post_title; ?></label></li>
			  <?php } ?>
		  </ul>
      </div>
		<script>
		jQuery(document).ready(function() {
			
		  set_checkboxes(jQuery("#collapse_pto_<?php echo $post_type_increment; ?> ul li:first-of-type input"));
		});
		</script>
    </div>
  </div>
			<?php } ?>
				<?php } ?>
  
	
</div>	

			<script>
				function set_checkboxes(tthis) {
		
				
					var selectable_options = jQuery("input.pto-checkbox[rel='" + jQuery(tthis).attr("rel") + "']");
					
					var selected_count = 0;
					
					selectable_options.each(function() {
						var checkbox = jQuery(this);
						if (checkbox.prop("checked")) {
						  selected_count++;
						}
					});
					
					var master_checkbox = jQuery("input.post-type-option[rel='"+  jQuery(tthis).attr("rel").replace("item_", "") + "']")
					var master_badge = master_checkbox.siblings(".badge");
					if (selected_count == 0) {
						
						master_checkbox.prop("checked", false);
						master_checkbox.attr("data-state", 0);
						master_checkbox.prop('indeterminate', false);
						
						master_badge.removeClass("badge-success");
						master_badge.removeClass("badge-indeterminate");
						master_badge.addClass("badge-default");
						master_badge.html("0 of " + selectable_options.length);						
						
					} else if (selected_count == selectable_options.length) {
						master_checkbox.prop("checked", true);
						master_checkbox.attr("data-state", 1);
						master_checkbox.prop('indeterminate', false);	
						
						master_badge.removeClass("badge-default");
						master_badge.removeClass("badge-indeterminate");
						master_badge.addClass("badge-success");
						master_badge.html("All " + selectable_options.length);					
						
						
					} else {
						master_checkbox.prop("checked", true);
						master_checkbox.attr("data-state", 2);
						master_checkbox.prop('indeterminate', true);
						master_badge.removeClass("badge-success");
						master_badge.removeClass("badge-default");
						master_badge.addClass("badge-indeterminate");
						master_badge.html(selected_count + " of " + selectable_options.length);
					}
					
				
				}
				
				jQuery('.pto-checkbox').click(function() {
					set_checkboxes(this);
				});
	
				jQuery('.post-type-option').click(function(e) {
					//e.preventDefault();
					
					var checkbox = jQuery(this);
					var master_badge = checkbox.siblings(".badge");
					var selectable_options = jQuery("input.pto-checkbox[rel='item_" + jQuery(this).attr("rel") + "']");

					
					if (checkbox.prop("checked")) {
					 	jQuery("input.pto-checkbox[rel='item_" + checkbox.attr("rel") + "']").prop("checked", true);
						checkbox.attr("data-state", 1);
						checkbox.prop('indeterminate', false);
						master_badge.removeClass("badge-default");
						master_badge.removeClass("badge-indeterminate");
						master_badge.addClass("badge-success");
						master_badge.html("All " + selectable_options.length);							
					} else {
						jQuery("input.pto-checkbox[rel='item_" + checkbox.attr("rel") + "']").prop("checked", false);
						checkbox.attr("data-state", 0);
						checkbox.prop('indeterminate', false);
						
						master_badge.removeClass("badge-success");
						master_badge.removeClass("badge-indeterminate");
						master_badge.addClass("badge-default");
						master_badge.html("0 of " + selectable_options.length);							
					}

					
				});
				

			</script>
				
						</div>
		</div>
	</div>
				</div>
			</li>	
		
		
				  
		
						  
		</ul>
 

	

		
	<h3>Manually Accelerate Your Pages</h3>
<ul class='auto-acceleration-options'>
	<li <?php if ($acceleration_type == "manual") { ?>class='selected-option'<?php } ?>>

				<div>		
					<input class='material-selector' id='acceleration-type-home-page'  type="radio"  autocomplete="off" name='acceleration-type' value='manual' <?php if ($acceleration_type == "manual") { ?>checked<?php } ?> />
					<label  class='ptoi-type material-selector' for="acceleration-type-home-page">Individual Pages
						<iframe width="100%" height="315" src="https://www.youtube.com/embed/Yia285nDKKI" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>

					</label>	
				</div>
			</li>
			  	
		</ul>
			  	</form>
		<script>
			  	jQuery(".auto-acceleration-options input[type=radio]").on("change", function() {
				  jQuery(this).parents("form").find("li").removeClass("selected-option");
				  jQuery(this).parents("li").addClass("selected-option");
			  	});
				</script>
	<br/ ><Br/>
		<style>#wpbody-content { padding-bottom: 0px; } </style>
	</div>
</div>
	<?php } else { 
		$pegasaas->set_init_notification(1);
?>
	<style>
		#system-mode { display: none; }
		#pegasaas-scores-wrapper { min-height: calc(100vh - 225px); }
		.pegasaas-dashboard { min-height: auto; }
		#wpbody-content { padding-bottom: 0px; }
</style>
	<div class='initialization-sequence text-center'>
		<?php if (isset(PegasaasAccelerator::$settings['limits']['monthly_optimizations']) && PegasaasAccelerator::$settings['limits']['monthly_optimizations'] > 0  && PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining'] == 0) { ?>
			<div class='stay-or-go-container no-border'><h3>Pegasaas Is Hungry!</h3>
			<p>It looks like you've run out of credits.  In order to optimize further pages, you will need to upgrade your account.</p>
			<a rel="noopener noreferrer" target="_blank" class="btn btn-success" href='https://pegasaas.com/upgrade/?utm_source=pegasaas-accelerator-lite-installation-no-credits&upgrade_key=<?php echo PegasaasAccelerator::$settings['api_key']; ?>-<?php echo PegasaasAccelerator::$settings['installation_id']; ?>&site=<?php echo $pegasaas->utils->get_http_host(); ?>&current=<?php echo PegasaasAccelerator::$settings['subscription']; ?>'><?php echo __("Upgrade Now", "pegasaas-accelerator"); ?></a>	
		</div>			

		
		<?php } else { ?>
		<div class='stay-or-go-container'><h3>Do I Stay or Do I Go?</h3>
			<p>You can sit here waiting for the initial optimizations and scans to complete, or you can go about your business and let the 
				initializtion sequence  
			complete in the background.  If you decide to continue with other work, we will alert you via a dashboard notification that the initial optimization and scans
			have been completed.</p>
			<a href='./' class='btn btn-primary'>Let Pegasaas Fly In The Background</a>
		</div>
		<div id="install-progress-box">
		
		<h3 id="initialization-title"><span class="js-rotating">Initializing,Initializing</span></h3>
		<h4  id="initialization-percent">0%</h4>
		
		<div class='row'>
			
		  <div class='col-sm-6 text-right'> 
			  <div class='vertical-progress'><div class='progress'></div></div>

			  <ul class='text-left' id="initialization-sequence-items"> 
				  
				  <li rel="init-1" class='pending' data-pegasaas-time='15'><i class='fa fa-fw'></i> Waking Up Pegasaas</li>
				  <li rel="init-2" class='pending-next' data-pegasaas-time='20'><i class='fa fa-fw'></i> Asserting GZIP</li>
				  <li rel="init-3" class='queued' data-pegasaas-time='20'><i class='fa fa-fw'></i> Turning On Browser Caching</li>
				  <li rel="init-4" class='queued' data-pegasaas-time='20'><i class='fa fa-fw'></i> Setting Up Page Caching</li>
				  <li rel="init-5" class='queued' data-pegasaas-time='20'><i class='fa fa-fw'></i> Submitting Optimzation Requests</li>
				  <li rel="init-6" class='queued' data-pegasaas-time='20'><i class='fa fa-fw'></i> Initiating Page Caching Auto-Crawl</li>
				  <li rel="init-7" class='queued' data-pegasaas-time='20'><i class='fa fa-fw'></i> Optimizing Images</li>
				  <li rel="init-8" class='queued' data-pegasaas-time='15'><i class='fa fa-fw'></i> Fetching Completed Optimizations</li>
				  <li rel="init-9" class='queued' data-pegasaas-time='15'><i class='fa fa-fw'></i> Initiating Performance Scans</li>
				  <li rel="init-10" class='queued' data-pegasaas-time='15'><i class='fa fa-fw'></i> Initiating Baseline Scans</li>
				  <li rel="init-11" class='queued' data-pegasaas-time='10' data-pegasaas-skippable='1'><i class='fa fa-fw'></i> Waiting For Initial Scores</li>
				  <li rel="init-12" class='queued' data-pegasaas-time='10'><i class='fa fa-fw'></i> Preparing to Launch!</li>				  
			  				  
				  
			</ul>
		  </div>

		  
		  <div class='col-sm-5 text-left'>
			<ul class='text-left' id="initialization-sequence-item-descriptions"> 
				
			<li id="init-1" class='visible-description' style="opacity: 1;">
				  	<h3>Waking Up Pegasaas</h3>
					<p>We're sending word to Olympus that we need the help of a winged horse!</p>
					<p>That, and we're validating your API key.</p>
				</li>
				<li id="init-2">
					<h3>Every Second Is Precious</h3>
					<p>For every second that we can make your website faster, your online revenue is predicted to improve by as much as 12%. *</p>
					<p class='smaller-text'>* Source: Google/SOASTA Research, 2017</p>
				</li>
				<li id="init-3" class='large'>
					<h3>The Average Mobile Web Page Takes 15<span style='text-transform: lowercase'>s</span> To Load</h3>
					<p>After 10 seconds, the probability of the visitor bouncing increases to 106%. *</p>
					<p>Yet, the average Pegasaas fully optimized mobile web page takes under 3 seconds to load.</p>
					<p class='smaller-text'>* Source: Google/SOASTA Research, 2017</p>

				</li>
				<li id="init-4">
					<h3>A Faster Website Mean Lower Bounce Rates</h3>
					<p>Studies have found that for every second that you can improve your website speed, your bounce rate can be reduced by
					  7%.</p>
				
				</li>
				  <li id="init-5">
					<h3>Optimization In Harmony</h3>
					<p>Pegasaas Accelerator WP applies over 20 different web performance optimization 
						concepts to your web pages to shave as much time off your load time as possible.</p>
				
				</li>
				  <li id="init-6" class='large'>
					<h3>Performance First</h3>
					<p>Pegasaas is first and foremost a performance-first plugin.  This means we make every effort to make your website faster, automatically, 
					  first.
					  </p>
					  
					  <p>If something about your website does not display exactly as you like, then you can always disable the automated features, or request
					  the support team investigate. *</p>
					  <p class='small-text'>* Available on Premium Subscriptions</p>

				</li>
				  	
			  
  				<li id="init-7">
			  			<h3>Real World Statistics</h3>
					<p>"COOK" increased conversion rate by 7% after cutting average page load time by 0.85 seconds. Bounce rate also fell by 7% and pages per session increased by 10%. *</p>
					<p class='smaller-text'>* Source: <a href='https://wpostats.com/'>https://wpostats.com/</a></p>
			  		</li>
				  <li id="init-8">
					  <h3>Real World Statistics</h3>
					<p>Furniture retailer "Zitmaxx Wonen" reduced their typical load time to 3 seconds and saw conversions jump 50.2%. Overall revenue from the mobile site also increased by 98.7%. *</p>
					<p class='smaller-text'>* Source: <a href='https://wpostats.com/'>https://wpostats.com/</a></p>

		</li>
				  <li id="init-9">
			  	<h3>Real World Statistics</h3>
					<p>"Carousell" reduced page load time by 65% and saw a 63% increase in organic traffic, a 3x increase in advertising click-thru rate and a 46% increase in first-time chatters. *</p>
					<p class='smaller-text'>* Source: <a href='https://wpostats.com/'>https://wpostats.com/</a></p>

			  </li>			
				<li id="init-10" clss='large'>
			
					<h3>Passionate About Performance</h3>
					  <p>Every single day, we work at making websites go from loading in 10 seconds to loading in under 3 seconds.</p>
					  <p>We hope we can be a part of making your website
						super fast, and help you make more conversions as a result.
					</p>

				</li>	
				<li id="init-11"  class='large'>
					<span id="waiting-to-complete">
			     	<h3>Coffee Time!</h3>
			  		<p>This is where we wait for the first couple of scans to finish completing.</p>
				 	<p>This could take a few minutes, or it may be done very quickly -- it all depends on how busy our API is at the moment.</p>
					<p>If this step takes longer than 15 minutes, please  <a href='?page=pegasaas-accelerator&skip=to-support'>initiate a support ticket</a> so that we may investigate.</p>
			  		</span>
					<span id="installation-hung" class="hidden">
			     	<h3>Initialization Problem</h3>
			  		<p>Our apologies, it looks like we had a problem with the initialization.</p>
				 	<p>Olympus has already been notified, and will be investigating the issue shortly.</p>
					<p>To launch an official investigation and get notified when a resolution is in place, please <a href='?page=pegasaas-accelerator&skip=to-support'>initiate a support ticket</a> so that we may investigate.</p>
					<p>Or, you can <a href='?page=pegasaas-accelerator'>give the install another try </a>. </p>
					</span>
				</li>
				<li id="init-12">
			     	<h3>Time to grab the reigns!</h3>
			  		<p>Here we go!  Just initializing the interface now...!</p>
			  	</li>			 
			  
			</ul>	  
			
		  </div>

		</div>
		</div>
<script>

		var total_of_percent = 0;
		var prepping_done = false;
	    var installation_hung = false;
	    var optimizations_queued = false;
	    var optimizations_complete = false;
		var time_since_last_nudge = 60;
		var time_since_last_prepping_check = 30;
		var api_key_valid = true;
		var installation_status = true;
	function update_initialization_sequence() {
		
		if (!prepping_done) { 
			is_prepping_done();
		}
		
		if (total_of_percent == 0) {
			jQuery("#initialization-sequence-items li").each(function() { total_of_percent = total_of_percent + parseInt(jQuery(this).attr("data-pegasaas-time")); });
		} 
		
		var total_of_percent_remaining = 0;
		jQuery("#initialization-sequence-items li.queued").each(function() { total_of_percent_remaining = total_of_percent_remaining + parseInt(jQuery(this).attr("data-pegasaas-time")); });

		var percent_remaining = Math.round(100*(total_of_percent - total_of_percent_remaining)/total_of_percent) ;
		var queued_items_remaining = jQuery("#initialization-sequence-items li.queued").length;
				

		if (queued_items_remaining > 0 || prepping_done) { 
		
			jQuery(".vertical-progress .progress").height(percent_remaining + "%");

			jQuery("#initialization-sequence-items li.done-done").css("display", "none");
			jQuery(".initialization-sequence h4").html(percent_remaining + "%");
			jQuery("#initialization-sequence-items li.done").removeClass("done").addClass("done-done");


			jQuery("#initialization-sequence-items li.pending").removeClass("pending").addClass("done");

			jQuery("#initialization-sequence-items li.pending-next").first().addClass("pending").removeClass("pending-next");
			jQuery("#initialization-sequence-items li.queued").first().addClass("pending-next").removeClass("queued");






			jQuery("#initialization-sequence-items li.done-done").animate({opacity: 0, height: 0}, delay * .5 );
			jQuery("#initialization-sequence-items li.done").animate({opacity: 0.5}, delay  );

		}
		
		var items_remaining = jQuery("#initialization-sequence-items li.pending-next").length;
		var delay = jQuery("#initialization-sequence-items li.pending").first().attr("data-pegasaas-time") * 1000;
		var can_skip = jQuery("#initialization-sequence-items li.pending").first().attr("data-pegasaas-skippable")  == 1;		
		
		
		time_since_last_nudge = time_since_last_nudge + (delay / 1000);
		time_since_last_prepping_check = time_since_last_prepping_check + (delay / 1000);
		
		if (prepping_done) {
			
			console.log("Pegasaas: Prepping Complete");
			if (can_skip) {
				delay = 1000;
			}
		} else if (installation_hung) {
			console.log("Pegasaas: Installation Possibly Hung");
		} else {
			console.log("Pegasaas: Prepping Still In Progress");

		}
		
		if (items_remaining > 0) {
		
			
			jQuery("#initialization-sequence-item-descriptions li").animate({opacity: 0}, 500  );
			jQuery("#initialization-sequence-item-descriptions li").removeClass("visible-description");
			
			var item_description_id = jQuery("#initialization-sequence-items li.pending").attr("rel");
			jQuery("#" + item_description_id).addClass("visible-description");
			jQuery("#" + item_description_id).animate({opacity: 1}, 500  );
			
			
			
			
			if (installation_hung && items_remaining == 1 && queued_items_remaining == 0) {
				jQuery.post(ajaxurl, 
						{ 'action': 'pegasaas_notify_hung_initialization', 
						  'api_key': '<?php echo PegasaasAccelerator::$settings['api_key']; ?>'
						}, function() { console.log("Pegasaas: Notified Olympus Of Hung Initialization");});
	
				jQuery("#initialization-sequence-item-descriptions li").last().prev().animate({opacity: 1}, 0  ).addClass("visible-description");
				jQuery("#waiting-to-complete").addClass("hidden");
				jQuery("#installation-hung").removeClass("hidden");
				jQuery("#initialization-percent").html("ERROR").addClass("hidden");
				jQuery(".js-rotating").unbind().removeData();
				jQuery(".js-rotating").removeClass("morphext");
				jQuery("#initialization-title").html("INITIALIZATION").addClass("hidden");
				jQuery(".initialization-sequence .col-sm-6.text-right").addClass("hidden");
				jQuery(".initialization-sequence .col-sm-5").addClass("col-sm-8").addClass("col-sm-offset-2").removeClass("col-sm-5");
				jQuery("#initialization-sequence-item-descriptions").removeClass("text-left").addClass("text-center");
				jQuery(".stay-or-go-container").addClass("hidden");
				
				
			} else {
				setTimeout("update_initialization_sequence()", delay);
			}
		} else {
			if (!prepping_done) {
				//setTimeout("update_initialization_sequence()", delay);
			} else {
				document.location.href=document.location.href + "";
			}
		}
	}
		
		
		function is_prepping_done() {
			if (time_since_last_prepping_check >= 15) {
				time_since_last_prepping_check = 0;
				jQuery.post(ajaxurl, 
						{ 'action': 'pegasaas_is_prepping_done', 
						  'api_key': '<?php echo PegasaasAccelerator::$settings['api_key']; ?>'
						}, 
						function(data) {
							installation_status = data;
							api_key_valid = true;
							if (data == 3) {
								prepping_done = true;
							installation_hung = false;
							} else if (data == -1) {
								prepping_done = false;
								installation_hung = true;
							} else if (data == -2) {
								prepping_done = false;
								api_key_valid = false;
							} else {
								if (data == 2) {
									installation_hung = false;
									optimizations_complete = true;
								} else if (data == 1) {
									installation_hung = false;
									optimizations_queued = true;
								}
								prepping_done = false;
					 			if (time_since_last_nudge >= 30) {
					 				nudge_server();
								} 
					
					
							}
						});
			}
		}
		function nudge_server() {
			jQuery.post("admin.php?page=pegasaas-accelerator&action=nudge", 
						{ 'output': 'raw'}, 
						function(data) { console.log("Pegasaas: Nudged Server"); });
			
			time_since_last_nudge = 0;
		}
		nudge_server();
	
		setTimeout("update_initialization_sequence()", 10000);

		
		
	</script>
		<?php } ?>
	</div>
<?php } ?>