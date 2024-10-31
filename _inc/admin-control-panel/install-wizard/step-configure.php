<div class="row setup-content" id="step-config" style="display: none;">
      <div class="col-md-10 col-md-offset-1">
        <div class="col-md-12">
			<?php
			$site_composition = $pegasaas->utils->get_site_composition();
			?>
           
			<h3>Configuration</h3>
			
		<?php if (PegasaasAccelerator::$settings['reason'] != "" && $_POST['api_key'] != "") { ?>
			<div class='api-connection-issue'><?php echo PegasaasAccelerator::$settings['reason']; ?></div>
		<?php } ?>
		  <p>Choose how much of your site you wish to initially accelerate.  You can always accelerate individual or groups of pages/posts after the initial setup.</p>
          <!--Radio butons-->
		  <?php $total_columns = 2;
			if ($site_composition['post_types']['post']['count'] > 0) { $total_columns++; }
			if ($site_composition['post_types']['page']['count'] > 0) { $total_columns++; }
			
			?>

		
		<ul class='auto-acceleration-options'>
			<!-- home page -->	  
			<li <?php if ($acceleration_type == "home-page") { ?>class='selected-option'<?php } ?>>
				<div>		
					<input class='material-selector' id='acceleration-type-home-page'  type="radio"  autocomplete="off" name='acceleration-type' value='home-page' <?php if ($acceleration_type == "home-page") { ?>checked<?php } ?> />
					<label  class='ptoi-type material-selector' for="acceleration-type-home-page">Home Page
						<small>Just your Home page.  You can always accelerate other pages later.</small>
					</label>	
				</div>
			</li>					  
			<!-- everything -->   
			<li <?php if ($acceleration_type == "all") { ?>class='selected-option'<?php } ?>>
				<div>
					<input class='material-selector' id='acceleration-type-everything'  type="radio"  autocomplete="off" name='acceleration-type' value='all' <?php if ($acceleration_type == "all") { ?>checked<?php } ?>/>
					<label  class='ptoi-type material-selector' for="acceleration-type-everything">Everything
						<small>Enable acceleration on all of your pages.  You can always disable acceleration on any page or post afterwards.</small>
					</label>
				</div>
			</li>

				  
				  
			<!-- Custom -->  
			<li <?php if ($acceleration_type == "advanced") { ?>class='selected-option'<?php } ?>>
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
		$posts = $pegasaas->utils->get_posts(array("post_type" => $pt_id, "post_status" => "publish", "limit" => 100, "orderby" => "post_title", "order" => "asc"));

		if ($post_type['count'] > 0 && sizeof($posts) > 0) { ?>
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
			<!-- Manual -->
			<li <?php if ($acceleration_type == "manual") { ?>class='selected-option'<?php } ?>>
				<div>
					<input class='material-selector' id='acceleration-type-manual'  type="radio" autocomplete="off" name='acceleration-type' value='manual' <?php if ($acceleration_type == "manual") { ?>checked<?php } ?> />

					<label  class='ptoi-type material-selector' for="acceleration-type-manual">Manual Installation
						<small>With this option, nothing is accelerated at this step, leaving you to manually enable acceleration on your pages and posts.</small>
					</label>
				</div> 
			</li>				  
				
				  
		</ul>

		
		<script>
			  	jQuery(".auto-acceleration-options input[type=radio]").on("change", function() {
				  jQuery(this).parents("form").find("li").removeClass("selected-option");
				  jQuery(this).parents("li").addClass("selected-option");
			  	});
				</script>			

         
			  
        </div>

      </div>
			  <div class='col-xs-12 btn-row'>
				 <button class="btn btn-primary nextBtn pull-right" type="button">Continue <i class='fa fa-angle-right'></i></button>

				  <button class="btn btn-default backBtn pull-left" type="button" ><i class='fa fa-angle-left'></i> Back</button>
				</div>

    </div>