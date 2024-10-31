
<script>
	jQuery("#desktop").click(function() {
		jQuery(".pagespeed-scores").removeClass("mobile-mode");
		jQuery(".pagespeed-scores").removeClass("multi-mode");
		jQuery(".pagespeed-scores").addClass("desktop-mode");
		jQuery("#pegasaas-scores-wrapper").removeClass("mobile-mode");
		jQuery("#pegasaas-scores-wrapper").removeClass("multi-mode");
		jQuery("#pegasaas-scores-wrapper").addClass("desktop-mode");
		Cookies.set("pegasaas_display_mode", "desktop-mode", { expires: 120 });
	});

	jQuery("#mobile").click(function() {
		jQuery(".pagespeed-scores").removeClass("desktop-mode");
		jQuery(".pagespeed-scores").removeClass("multi-mode");
		jQuery(".pagespeed-scores").addClass("mobile-mode");
		jQuery("#pegasaas-scores-wrapper").removeClass("desktop-mode");
		jQuery("#pegasaas-scores-wrapper").removeClass("multi-mode");
		jQuery("#pegasaas-scores-wrapper").addClass("mobile-mode");		
		Cookies.set("pegasaas_display_mode", "mobile-mode", { expires: 120 });

	});
	
	jQuery("#both").click(function() {
		jQuery(".pagespeed-scores").removeClass("mobile-mode");
		jQuery(".pagespeed-scores").removeClass("desktop-mode");
		jQuery(".pagespeed-scores").addClass("multi-mode");
		Cookies.set("pegasaas_display_mode", "multi-mode", { expires: 120 });

	});
</script>
	
 
<?php //if (!$pegasaas->is_free()) { ?>
<?php include("pro/overview-nav.php"); ?>
<?php // } ?>


		  <table class='table table-striped table-bordered pagespeed-scores <?php echo $view_mode; ?>'>
	 		
		  <tr class='header-row header-row-1'>
			  <th rowspan='1' class='double-row'>URL</th>
			  <?php if (PegasaasAccelerator::$settings['status'] > 0) { ?>
			  
		  
			  <th rowspan='1' class='double-row text-center'><?php _e("Optimized"); ?></th>
		  
			  <th colspan='1' class='text-center border-left unaccelerated-pagespeed'>Baseline Performance Metrics <i class='fa fa-info-circle' rel='tooltip' title='These performance metrics are for your site before any optimizations are performed.'></i>
			  <table style='width: 100%;'>
			    <tr>
				  <td style='min-width: 30px; width: 30px;'>&nbsp;</td>
			<td class='perf-metrics-container'>
									  <table class='perf-metrics-table'>
										  <tr>
					<td data-display-element='ttfb' class='perf-metric-header hidden-novice text-center <?php echo $pegasaas->interface->web_perf_metrics_ttfb_class; ?>' rel='tooltip' title='Time To First Byte'><div class='perf-metric-col-label'>TTFB</div></td>
					<td data-display-element='fcp' class='perf-metric-header hidden-novice text-center <?php echo $pegasaas->interface->web_perf_metrics_fcp_class; ?>' rel='tooltip' title='First Contentful Paint'><div class='perf-metric-col-label'>FCP</div></td>
					<td data-display-element='si' class='perf-metric-header hidden-novice text-center <?php echo $pegasaas->interface->web_perf_metrics_si_class; ?>' rel='tooltip' title='Speed Index'><div class='perf-metric-col-label'>SI</div></td>
					<td data-display-element='lcp' class='perf-metric-header hidden-novice text-center <?php echo $pegasaas->interface->web_perf_metrics_lcp_class; ?>' rel='tooltip' title='Largest Contentful Paint'><div class='perf-metric-col-label'>LCP</div></td>
					<td data-display-element='tti' class='perf-metric-header hidden-novice text-center hidden-intermediate <?php echo $pegasaas->interface->web_perf_metrics_tti_class; ?>' rel='tooltip' title='Time To Interactive'><div class='perf-metric-col-label'>TTI</div></td>
				<!--	<td data-display-element='fci' class='perf-metric-header hidden-novice text-center hidden-intermediate <?php echo $pegasaas->interface->web_perf_metrics_fci_class; ?>' rel='tooltip' title='First CPU Idle'><div class='perf-metric-col-label'>FCI</div></td>-->
					<td data-display-element='tbt' class='perf-metric-header hidden-novice text-center hidden-intermediate <?php echo $pegasaas->interface->web_perf_metrics_tbt_class; ?>' rel='tooltip' title='Total Blocking Time'><div class='perf-metric-col-label'>TBT</div></td>
					<td data-display-element='cls' class='perf-metric-header hidden-novice text-center hidden-intermediate <?php echo $pegasaas->interface->web_perf_metrics_cls_class; ?>' rel='tooltip' title='Cumulative Layout Shift'><div class='perf-metric-col-label'>CLS</div></td>
					<td class='perf-metric-header-ps progress-v2_6 text-center'  rel='tooltip' title='PageSpeed Score'><div class='visible-novice'>PageSpeed&nbsp;</div>Score</td>
					
										  </tr>
				</table>
					</td>
					<td class='perf-metric-header-tail'></td>
				  </tr>
				  </table>
			  </th>
			  <th colspan='1' class='text-center border-left accelerated-pagespeed'>Accelerated Performance Metrics
 <table style='width: 100%;'>
			    <tr>
				  <td style='min-width: 30px; width: 30px; '>&nbsp;</td>
					<td class='perf-metrics-container'>
									  <table class='perf-metrics-table'>
										  <tr>
					<td data-display-element='ttfb' class='perf-metric-header hidden-novice text-center <?php echo $pegasaas->interface->web_perf_metrics_ttfb_class; ?>' rel='tooltip' title='Time To First Byte'><div class='perf-metric-col-label'>TTFB</div></td>
					<td data-display-element='fcp' class='perf-metric-header hidden-novice text-center <?php echo $pegasaas->interface->web_perf_metrics_fcp_class; ?>' rel='tooltip' title='First Contentful Paint'><div class='perf-metric-col-label'>FCP</div></td>
					<td data-display-element='si' class='perf-metric-header hidden-novice text-center <?php echo $pegasaas->interface->web_perf_metrics_si_class; ?>' rel='tooltip'  title='Speed Index'><div class='perf-metric-col-label'>SI</div></td>
					<td data-display-element='lcp' class='perf-metric-header hidden-novice text-center <?php echo $pegasaas->interface->web_perf_metrics_lcp_class; ?>' rel='tooltip' title='Largest Contentful Paint'><div class='perf-metric-col-label'>LCP</div></td>
					<td data-display-element='tti' class='perf-metric-header hidden-novice text-center hidden-intermediate <?php echo $pegasaas->interface->web_perf_metrics_tti_class; ?>' rel='tooltip'  title='Time To Interactive'><div class='perf-metric-col-label'>TTI</div></td>
				<!--	<td data-display-element='fci' class='perf-metric-header hidden-novice text-center hidden-intermediate <?php echo $pegasaas->interface->web_perf_metrics_fci_class; ?>' rel='tooltip' title='First CPU Idle'><div class='perf-metric-col-label'>FCI</div></td>-->
					<td data-display-element='tbt' class='perf-metric-header hidden-novice text-center hidden-intermediate <?php echo $pegasaas->interface->web_perf_metrics_tbt_class; ?>' rel='tooltip'  title='Total Blocking Time'><div class='perf-metric-col-label'>TBT</div></td>
					<td data-display-element='cls' class='perf-metric-header hidden-novice text-center hidden-intermediate <?php echo $pegasaas->interface->web_perf_metrics_cls_class; ?>' rel='tooltip'  title='Cumulative Layout Shift'><div class='perf-metric-col-label'>CLS</div></td>
					<td class='perf-metric-header-ps progress-v2_6 text-center'  rel='tooltip' title='PageSpeed Score'><div class='visible-novice'>PageSpeed&nbsp;</div>Score</td>
											  </tr>
				</table>
					</td>
											  <td class='perf-metric-header-tail'></td>
				  </tr>
				  </table>				  
				  </th>
			  <!--<th rowspan='1' class='needs-attention-header' >&nbsp;</th> -->
			  <?php } else { ?> 
			  <th colspan='1' class='text-center'>Un-Accelerated</th>
			  <?php } ?>
			</tr>
		
			  

	
		  <?php 
		  $existing_requests 						= get_option("pegasaas_accelerator_pagespeed_score_requests", array());
		  if (!is_array($existing_requests)) {
			  $existing_requests = array();
		  }
		  $existing_benchmark_requests 				= get_option("pegasaas_accelerator_pagespeed_benchmark_score_requests", array());
		  if (!is_array($existing_benchmark_requests)) {
			  $existing_benchmark_requests = array();
		  }			  
		  if (!is_array($scanned_pages)) {
			  $scanned_pages = array();
		  }
			  $scanned_pages = array();
		  foreach ($scanned_pages as $post) {
			  if ($post['score'] == -2 || $post['benchmark_score'] == -2) {
			  ?>
				<tr class='limited'>
					<td>
						
					  <?php if ($post['type'] == "Page") { ?>
			  			<span class="material-icons">description</span>
					  <?php } else if ($post['type'] == "Category") { ?>
					  <span class="material-icons">folder</span>
					   <?php } else { ?>
					  <span class="dashicons dashicons-admin-post"></span>
					  <?php } ?>
					  
					  <?php echo $post['slug']; ?>
				 	</td>
				
					<td>&nbsp;</td>

					
					<td class='unaccelerated-pagespeed'>
						<div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">Upgrade</div></div>					
				  	</td>
				   <?php if (PegasaasAccelerator::$settings['status'] > 0) { ?>
				  <td><div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">Upgrade</div></div>		</td>
				  <!--<td class='needs-attention'>Upgrade</td>-->
				<?php } ?>
				</tr>				  
			  <?php
			  } else {
				
				  $pagespeed_scan_in_progress 			= @sizeof($existing_requests["{$post['pid']}"]) > 0;
				  $pagespeed_benchmark_scan_in_progress = @sizeof($existing_benchmark_requests["{$post['pid']}"]) > 0;
			  ?>
				<tr>
					<td>
						<?php if ($post['type'] == "Page") { ?>
			  			<span class="material-icons" title='<?php echo $post['type']; ?>'>description</span>
	  					<?php } else if ($post['type'] == "Category") { ?>
					  	<span class="material-icons">folder</span>					   
						<?php } else if ($post['type'] == "Woocommerce_product_category") { ?>
					  	<span class="material-icons">store</span>						  	
						<?php } else if ($post['type'] == "Product") { ?>
					  	<span class="material-icons">view_list</span>						  	
						<?php } else if ($post['type'] == "Woocommerce_product_tag") { ?>
					  	<span class="material-icons">loyalty</span>	
						<?php } else { ?>
					  	<span class="material-icons" title='<?php echo $post['type']; ?>'>notes</span>
					  	<?php } ?>
					  	<a href='<?php echo $pegasaas->get_home_url().$post['slug']; ?>' target='_blank' class='external-link-to-page'><?php echo $post['slug']; ?></a>
						
						
						<?php if (PegasaasAccelerator::$settings['status'] > 0) { ?>
				  		<a data-toggle='tooltip' title='Edit Page Level Settings' class='pull-right external-link' style='margin-left: 10px;' href='post.php?post=<?php echo $post['id'];?>&action=edit#pegasaas_page_post_options'><i class='material-icons'>edit</i></a>
						<?php } ?>
						
						<?php 
				  			$cache_exists = PegasaasAccelerator::$cache_map["{$post['slug']}"] != "";
				  if ($cache_exists && PegasaasAccelerator::$cache_map["{$post['slug']}"]["css_validation_issue"] == true) { ?>
						<a class='pull-right css-validation-issue' href='https://jigsaw.w3.org/css-validator/validator?uri=<?php echo urlencode($pegasaas->get_home_url().$post['slug']."?accelerate=off"); ?>&profile=css3svg&usermedium=all&warning=no&vextwarning=&lang=en' target='_blank'>
							<i class='fa fa-exclamation-triangle' rel='tooltip' title='Possible CSS Validation Issues Detected.  This may affect the ability for Pegasaas to fetch an accurate Critical CSS snapshot.'></i></a>
						<?php } ?>
				  
					</td>
				  
				<?php if (PegasaasAccelerator::$settings['status'] > 0) { ?>
					
					<td class='page-cache-button-container'><?php include("row/cache-dropdown-2-lite.php"); ?></td>

				<?php } ?>
					
				  <!-- Benchmark Score -->
				  <?php $benchmark_data = $pegasaas->scanner->pegasaas_fetch_pagespeed_benchmark_last_scan($post['pid']); ?>
					<td rel='<?php echo $post['pid']; ?>' class='unaccelerated-pagespeed original-score-container  left-bg 
					<?php if ( $post['benchmark_score'] == '' && $post['benchmark_score'] != '0') { ?> benchmark-scan-in-progress benchmark-pending-scan<?php } ?>'>
	 				<table>
					  	<tr>
						  	<td>
							  	<table>
									<tr class='mobile-score-row'>
						   				<td class='fa-container'><i class='<?php echo PEGASAAS_MOBILE_ICON_CLASS; ?>'></i></td>

	   	  
					   	  	  
					   	  	  	  	  
									
										<td class='perf-metric hidden-novice text-center' style='font-size: 8pt;'>
										  <?php $pegasaas->interface->render_page_metric($benchmark_data, "ttfb", "mobile"); ?>
										</td>
										<td class='perf-metric hidden-novice'>
										  <?php $pegasaas->interface->render_page_metric($benchmark_data, "fcp", "mobile"); ?>
										</td>
										<td class='perf-metric hidden-novice hidden-intermediate'>
										  <?php $pegasaas->interface->render_page_metric($benchmark_data, "fmp", "mobile"); ?>
										</td>	
										<td class='perf-metric hidden-novice hidden-intermediate'>
										  <?php $pegasaas->interface->render_page_metric($benchmark_data, "fci", "mobile"); ?>
										</td>	
										<td class='perf-metric hidden-novice'>
										  <?php $pegasaas->interface->render_page_metric($benchmark_data, "si", "mobile"); ?>
										</td>
										<td class='perf-metric hidden-novice hidden-intermediate'>
										  <?php $pegasaas->interface->render_page_metric($benchmark_data, "tti", "mobile"); ?>
										</td>	
					    				<td class='progress-v2_6'>	
											
<div class="progress mobile-score-container" >
  <div class="progress-bar progress-bar-stripedx <?php if (($post['benchmark_mobile_score'] == '' && $post['benchmark_mobile_score'] != '0') || $post['benchmark_mobile_score'] === 'NULL'|| $post['benchmark_mobile_score'] == -1) { print "active"; } else if ($post['benchmark_mobile_score'] >= 85) { print "progress-bar-success"; } else if ($post['benchmark_mobile_score'] >= 75) { print "progress-bar-warning"; } else { print "progress-bar-danger"; } ?>" role="progressbar" aria-valuenow="<?php echo $post['benchmark_mobile_score']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php if (($post['benchmark_mobile_score'] == '' && $post['benchmark_mobile_score'] != '0')  || $post['benchmark_mobile_score'] === 'NULL' || $post['benchmark_mobile_score'] == -1) { print "100"; } else { if ($post['benchmark_mobile_score'] < 10) { print "10"; } else { echo $post['benchmark_mobile_score']; } } ?>%;">
    <?php if ( ($post['benchmark_mobile_score'] == '' && $post['benchmark_mobile_score'] != '0') || $post['benchmark_mobile_score'] === 'NULL'|| $post['benchmark_mobile_score'] == -1) { 
				  if ($post['benchmark_mobile_score'] == '' && $post['benchmark_score'] > 0) {
					  $pegasaas->scanner->clear_last_benchmark_scan($post['pid']);
				  }
				  
			   if (!PegasaasUtils::memory_within_limits()) { echo "On Hold"; } else { echo "Queued"; }
			  } else { echo $post['benchmark_mobile_score']; } ?>
  </div>
</div>
										</td>										
										
			  						</tr>

		 
  <tr class='desktop-score-row left-bg'>
						 <td class='fa-container'><i class='<?php echo PEGASAAS_DESKTOP_ICON_CLASS; ?>'></i></td>

	  
										<td class='perf-metric hidden-novice'>
										  <?php $pegasaas->interface->render_page_metric($benchmark_data, "ttfb", "desktop"); ?>
										</td>
										<td class='perf-metric hidden-novice'>
										  <?php $pegasaas->interface->render_page_metric($benchmark_data, "fcp", "desktop"); ?>
										</td>
										<td class='perf-metric hidden-novice hidden-intermediate'>
										  <?php $pegasaas->interface->render_page_metric($benchmark_data, "fmp", "desktop"); ?>
										</td>	
										<td class='perf-metric hidden-novice'>
										  <?php $pegasaas->interface->render_page_metric($benchmark_data, "fci", "desktop"); ?>
										</td>	
										<td class='perf-metric hidden-novice hidden-intermediate'>
										  <?php $pegasaas->interface->render_page_metric($benchmark_data, "si", "desktop"); ?>
										</td>
										<td class='perf-metric hidden-novice hidden-intermediate'>
										  <?php $pegasaas->interface->render_page_metric($benchmark_data, "tti", "desktop"); ?>
										</td>	  
				 <td class='progress-v2_6'>
<div class="progress desktop-score-container">
  <div class="progress-bar progress-bar-stripedx <?php if (($post['benchmark_score'] == '' && $post['benchmark_score'] != '0') || $post['benchmark_score'] === 'NULL' || $post['benchmark_score'] == -1) { print "active"; } else if ($post['benchmark_score'] >= 90) { print "progress-bar-success"; } else if ($post['benchmark_score'] >= 50) { print "progress-bar-warning"; } else { print "progress-bar-danger"; } ?>" role="progressbar" aria-valuenow="<?php echo $post['benchmark_score']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php if (($post['benchmark_score'] == '' && $post['benchmark_score'] != '0')  || $post['benchmark_score'] === 'NULL'|| $post['benchmark_score'] == -1) { print "100"; } else { if ($post['benchmark_score'] < 10) { print "10"; } else { echo $post['benchmark_score']; } } ?>%;">
    <?php if ( ($post['benchmark_score'] == '' && $post['benchmark_score'] != '0') || $post['benchmark_score'] === 'NULL' || $post['benchmark_score'] == -1) { 
				 if (!PegasaasUtils::memory_within_limits()) { echo "On Hold"; } else { echo "Queued"; }
																							 
				} else { echo $post['benchmark_score']; } ?>
  </div>
</div>
		 </td>
	  
			  </tr>		 
		 
</table>
						  </td>
						  <td class='benchmark-dropdown-menu-container'><?php include("row/benchmark-dropdown.php"); ?></td>
						  </tr>
					  </table>
							  
			
					</td>
					
				  <?php if (PegasaasAccelerator::$settings['status'] > 0) { ?>	  

				  <td class='accelerated-score-container right-bg <?php if ( $post['score'] == '' && $post['score'] != '0') { ?> accelerated-scan-in-progress accelerated-pending-scan<?php } ?>'>
					  <table>
					  
					    <tr>
						  <td>
				   <table>
<?php $accelerated_data = $pegasaas->scanner->pegasaas_fetch_pagespeed_last_scan($post['pid']); ?>					   
 <tr  class='mobile-score-row'>
					   <td class='fa-container'><i class='<?php echo PEGASAAS_MOBILE_ICON_CLASS; ?>'></i></td>
		
	 
	 	<td class='perf-metric hidden-novice text-center' style='font-size: 8pt;'>
										  <?php $pegasaas->interface->render_page_metric($accelerated_data, "ttfb", "mobile"); ?>
										</td>
										<td class='perf-metric hidden-novice'>
										  <?php $pegasaas->interface->render_page_metric($accelerated_data, "fcp", "mobile"); ?>
										</td>
										<td class='perf-metric hidden-novice hidden-intermediate'>
										  <?php $pegasaas->interface->render_page_metric($accelerated_data, "fmp", "mobile"); ?>
										</td>	
										<td class='perf-metric hidden-novice hidden-intermediate'>
										  <?php $pegasaas->interface->render_page_metric($accelerated_data, "fci", "mobile"); ?>
										</td>	
										<td class='perf-metric hidden-novice'>
										  <?php $pegasaas->interface->render_page_metric($accelerated_data, "si", "mobile"); ?>
										</td>
										<td class='perf-metric hidden-novice hidden-intermediate'>
										  <?php $pegasaas->interface->render_page_metric($accelerated_data, "tti", "mobile"); ?>
										</td>
	 
	 
			   <td class='progress-v2_6'><div class="progress mobile-score-container" rel='<?php echo $post['slug']; ?>'>

  <div class="progress-bar progress-bar-stripedx <?php 
																		   $this_one_scanning = false;
				  if ($prepping || ($post['mobile_score'] == '' && $post['mobile_score'] != '0') || $pagespeed_scan_in_progress) { 				  
					  print "active";
					  $this_one_scanning = true;
				
				  
				  } else if ($post['mobile_score'] == -1) {
					   if (PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining'] == 0) {
					   print "progress-bar-paused"; 
						    } else {
					  print "";
				  }
				  } else if (!$pagespeed_scan_in_progress) { 
					  if ($post['mobile_score'] >= 90) { 
						  print "progress-bar-success"; 
					  } else if ($post['mobile_score'] >= "50") { 
						  print "progress-bar-warning"; 
					  } else { 
						  print "progress-bar-danger"; 
					  } 
				  } ?>" role="progressbar" aria-valuenow="<?php 
				  if ($prepping || ($post['mobile_score'] == '' && $post['mobile_score'] != '0')) {
					  print '100';
				  } else if ($post['mobile_score'] == -1) {
					  print '100';					  
				  } else if (!($post['mobile_score'] == '' && $post['mobile_score'] != '0') && $post['mobile_score'] < 40) { 
					  print "40"; 
					 // echo "100";
					  
				  } else { 
					//  print $post['mobile_score']; 
					  echo "100";
				  } ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php
				  
				   if ($prepping || ($post['mobile_score'] == '' && $post['mobile_score'] != '0')  || $pagespeed_scan_in_progress) { 
					   echo "100";
				  } else if ($post['mobile_score'] == -1) {
					  print '100';				   
				  } else if (!($post['mobile_score'] == '' && $post['mobile_score'] != '0') && $post['mobile_score'] < 40) { 
					  print "40"; 			
					 //  echo "100";
				   } else {
					   
					  echo $post['mobile_score'];
					 //  echo "100";
				   } ?>%;">
    <?php if (!($post['mobile_score'] == '' && $post['mobile_score'] != '0') && $post['mobile_score'] >= 0) { echo $post['mobile_score']; } else { 
			 if ($post['mobile_score'] == '' && false) {
				 print "Error -- Rescan Required";
			 } else if (!PegasaasUtils::memory_within_limits()) { 
				 echo "On Hold";
			 } else if ($pegasaas->is_standard() && PegasaasAccelerator::$settings['limits']['monthly_optimizations'] > 0 && PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining'] == 0) {
			   echo "Pending Optimization Credits";	 
			 
			 } else if ($post['mobile_score'] == -1) {
				 echo "Pending";
			 } else { 
				 echo "Queued";
			 }
																																				 
				 } ?>
  </div>
</div>	
					   </td>	 
					   </tr>					   
					   
				     <tr  class='desktop-score-row'>
						 <td class='fa-container'><i class='<?php echo PEGASAAS_DESKTOP_ICON_CLASS; ?>'></i></td>
					
							 	<td class='perf-metric hidden-novice text-center' style='font-size: 8pt;'>
										  <?php $pegasaas->interface->render_page_metric($accelerated_data, "ttfb", "desktop"); ?>
										</td>
										<td class='perf-metric hidden-novice'>
										  <?php $pegasaas->interface->render_page_metric($accelerated_data, "fcp", "desktop"); ?>
										</td>
										<td class='perf-metric hidden-novice hidden-intermediate'>
										  <?php $pegasaas->interface->render_page_metric($accelerated_data, "fmp", "desktop"); ?>
										</td>	
										<td class='perf-metric hidden-novice hidden-intermediate'>
										  <?php $pegasaas->interface->render_page_metric($accelerated_data, "fci", "desktop"); ?>
										</td>	
										<td class='perf-metric hidden-novice'>
										  <?php $pegasaas->interface->render_page_metric($accelerated_data, "si", "desktop"); ?>
										</td>
										<td class='perf-metric hidden-novice hidden-intermediate'>
										  <?php $pegasaas->interface->render_page_metric($accelerated_data, "tti", "desktop"); ?>
										</td>
	 <td class='progress-v2_6'><div class="progress desktop-score-container" rel='<?php echo $post['slug']; ?>'>
 
  			<div class="progress-bar progress-bar-stripedx <?php 
																		   $this_one_scanning = false;
				  if ($prepping || ($post['score'] == '' && $post['score'] != '0') || $pagespeed_scan_in_progress) { 				  
					  print "active";
					  $this_one_scanning = true;
				 } else if ($post['mobile_score'] == -1) {
				  echo "";  
				  } else if (!$pagespeed_scan_in_progress) { 
					  if ($post['score'] >= 90) { 
						  print "progress-bar-success"; 
					  } else if ($post['score'] >= "50") { 
						  print "progress-bar-warning"; 
					  } else { 
						  print "progress-bar-danger"; 
					  } 
				  } ?>" role="progressbar" aria-valuenow="<?php 
				  if ($prepping || ($post['score'] == '' && $post['score'] != '0')) {
					  print '100';
				  } else if ($post['mobile_score'] == -1) {
					  print '100';
				  
				  } else if (!($post['score'] == '' && $post['score'] != '0') && $post['score'] < 40) { 
					  print "40"; 
				  } else { 
					  print $post['score']; 
				  } ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php
				  
				   if ($prepping || ($post['score'] == '' && $post['score'] != '0')  || $pagespeed_scan_in_progress) { 
					   echo "100";
				  } else if ($post['mobile_score'] == -1) {
					  print '100';				  
				   } else if (!($post['score'] == '' && $post['score'] != '0') && $post['score'] < 40) { 
					  print "40"; 	
					 //  echo "100";
				   } else {
					  echo $post['score'];
					 //  echo "100";
				   } ?>%;">
    <?php if (!($post['score'] == '' && $post['score'] != '0') && $post['score'] >= 0) { 
			  echo $post['score']; 
		 } else { 
			 if ($post['score'] == '' && false) {
				 print "Error -- Rescan Required";
			 } else if (!PegasaasUtils::memory_within_limits()) { 
				 echo "On Hold";
			  } else if ($post['mobile_score'] == -1) {
				 echo "Pending";
			 } else { 
				 echo "Queued";
			 }
				   
				   } ?>
  </div>
</td>							 
					   </tr>
					  
</table>					  
						  </td>
						  <td class='benchmark-dropdown-menu-container'><?php include("row/accelerated-dropdown.php"); ?></td>
						  </tr>
					  </table>
				  </td>
					<?php /*
				  <td class='areas-needing-attention no-left-border' >
					  <?php if ($post['mobile_score'] > 0 && $post['score'] > 0) { ?>
					<?php $pegasaas->interface->render_recommended_actions_button($post['slug'], $post['id']); ?>
					  <?php } ?>
				  </td> 			
				-*/ ?>
				 
				  
				  <td class='last-scan' style='display: none;'><span class='when'><?php 
																		   if ($post['last_scan'] != "") {
																	   
																		   echo date_i18n( get_option( 'date_format' ), $post['last_scan'] );
																			//   echo " ".date_i18n( get_option( 'time_format' ), $post['last_scan']);
																		   }
																		   //var_dump($post);
					  ?>
					  </span> 
					
				  </td>
				 

				  <?php } else { ?>  
					<!--
				  <td class='areas-needing-attention'>
				  <?php foreach ($post['benchmark_needs'] as $rule_id => $rule_data) { ?>
					  <span class='label label-<?php if ($rule_data['rule_impact'] < 2) { print "default"; } else if ($rule_data['rule_impact'] < 6) { print "warning"; } else { print "danger"; } ?>'>
					  <a rel="<?php echo $rule_id; ?>" class='needs' title='<?php echo $rule_data['rule_name']; ?>'><i class='fa fa-<?php echo $rule_data['rule_icon']; ?>' title=': Worth <?php echo number_format($rule_data['rule_impact'], 2, '.', ''); ?>%'></i></a>
					  </span>&nbsp; 
					  <div style='display: none;' class='rule-details-<?php echo $rule_id; ?>'><?php echo $rule_data['rule_long_description']; ?></div>
				  <?php } ?>
					  
				  </td> 
-->
				  <?php } ?>
				</tr>
				<tr id="pegasaas-desktop-score-review-<?php echo $post['slug']; ?>" class='desktop-score-report-container non-visible'>
		
					<td colspan='7' class='desktop-score-report'>
						
						
					</td>
			  	</tr>
				<?php				  
			  }			  
		  }
		  ?>
		  </table>
<script type="text/template" id="page-score-row-template">
<?php include("row/page-score-row-template.php"); ?>
</script>
<?php 
			 
		

if (isset($rescan_benchmark) && $rescan_benchmark > 5) {
	$pegasaas->clear_pagespeed_benchmark_scores();
}
?>