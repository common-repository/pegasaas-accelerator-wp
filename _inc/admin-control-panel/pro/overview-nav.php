
<div class='row'>
<div class='col-sm-4'>
<div class="info-switcher">
	<p class='fieldset'>
		  <label>Show
		  <select class='form-control' id='show_post_type'>
			 			<option value='all'>All</option>
			  			<?php $post_types = $pegasaas->utils->get_post_types(); ?>
					  <?php foreach ($post_types as $post_type_id) { 
					  $post_type = get_post_type_object( $post_type_id );
					  ?>
					 	<option <?php if ($post_type_id == $pegasaas->interface->results_post_type) { print "selected"; } ?> value='<?php echo $post_type_id; ?>'><?php echo $post_type->labels->name; ?></option>
					 <?php } ?>
		  			</select>	
			  	</label>
		<!--
		  		<label>Issues
		  			<select class='form-control' id='filter_results_issues'>
						<option value='any'>Any</option>
						<option <?php if ("minification" == $pegasaas->interface->results_issue_filter) { print "selected"; } ?> value='minification'>Minification</option>
						<option <?php if ("browser-caching" == $pegasaas->interface->results_issue_filter) { print "selected"; } ?> value='browser-caching'>Browser Caching</option>
						<option <?php if ("image-optimization" == $pegasaas->interface->results_issue_filter) { print "selected"; } ?> value='image-optimization'>Image Optimization</option>
						<option <?php if ("defer-render-blocking-resources" == $pegasaas->interface->results_issue_filter) { print "selected"; } ?> value='defer-render-blocking-resources'>Render Blocking Resources</option>
						<option <?php if ("visible-content-prioritization" == $pegasaas->interface->results_issue_filter) { print "selected"; } ?> value='visible-content-prioritization'>Visible Content Prioritization</option>
						<option <?php if ("server-response-time" == $pegasaas->interface->results_issue_filter) { print "selected"; } ?> value='server-response-time'>Server Response Time</option>
		  			</select>	
		  		</label>
-->
		  		<label>Results Per Page
		  			<select class='form-control' id='results_per_page'>
						<option <?php if ($pegasaas->interface->results_per_page == "5") { print "selected"; } ?>>5</option>
						<option <?php if ($pegasaas->interface->results_per_page == "10") { print "selected"; } ?>>10</option>
						<option <?php if ($pegasaas->interface->results_per_page == "25") { print "selected"; } ?>>25</option>
						<option <?php if ($pegasaas->interface->results_per_page == "50") { print "selected"; } ?>>50</option>
						<option <?php if ($pegasaas->interface->results_per_page == "100") { print "selected"; } ?>>100</option>
						<option <?php if ($pegasaas->interface->results_per_page == "150") { print "selected"; } ?>>150</option>
						<option <?php if ($pegasaas->interface->results_per_page == "250") { print "selected"; } ?>>250</option>
						<option <?php if ($pegasaas->interface->results_per_page == "all" || $pegasaas->interface->results_per_page == "") { print "selected"; } ?>>All</option>
		  			</select>		
				</label>
			</p>
		</div> 
	</div>	
	<div class='col-sm-4'>
		<div class="results-indicator-switcher-center">
			<div class='fieldset'>
				
					<div class="form-group">
						<input type='text' class='form-control' id="results_search_filter" value="<?php echo $pegasaas->interface->results_search_filter; ?>">
					</div>
				
			</div>
		</div> 
	</div>	
	
	<div class='col-sm-4'>
		<div class="results-indicator-switcher">
			<p class='fieldset'>
				<button class='btn btn-default btn-circle' id="results_page_fast_backwards" <?php if ($pegasaas->interface->current_results_page - 1 <= 0) { print "disabled"; } ?>  value="1"><i class='fa fa-fast-backward'></i></button>
				<button class='btn btn-default btn-circle' id="results_page_backward" <?php if ($pegasaas->interface->current_results_page - 1 <= 0) { print "disabled"; } ?>  value="<?php echo $pegasaas->interface->current_results_page - 1; ?>"><i class='fa fa-backward'></i></button>
				<span class='results-indicator-page'>Page <span id="current_results_page"><?php echo $pegasaas->interface->current_results_page; ?></span> of <span id="total_result_pages"><?php echo $pegasaas->interface->max_results_page; ?></span></span>
				<button class='btn btn-default btn-circle' id="results_page_forward" <?php if ($pegasaas->interface->current_results_page + 1 > $pegasaas->interface->max_results_page) { print "disabled"; } ?> value="<?php echo $pegasaas->interface->current_results_page + 1; ?>"><i class='fa fa-forward'></i></button>
				<button class='btn btn-default btn-circle' id="results_page_fast_forwards" <?php if ($pegasaas->interface->current_results_page + 1 > $pegasaas->interface->max_results_page) { print "disabled"; } ?> value="<?php echo $pegasaas->interface->max_results_page; ?>"><i class='fa fa-fast-forward'></i></button>
			</p>
		</div> 
	</div>
</div>