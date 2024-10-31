<tr class='page-data-item'>
	<!-- URL -->
	<td class='page-details'>
	  <span class='staging-mode-visible'>
		<?php include "staging-mode-status-template.php"; ?>
	  </span>
	  <span class='diagnostic-mode-visible'><span class='label label-danger'>Inactive</span></span>
	  <span class='{{ page_type_icon_class }}'>{{ page_type_icon_text }}</span>
	  {% if display_wpml_icon %}
		<span class='{{ wpml_icon_class }}'><img src='{{ wpml_icon }}'></span>
	  {% endif %}
	  <div class='page-title-slug-cell'>
		  <div class='page-title'>{{ name }}</div>
	  	  <a href='{{ slug }}' target='_blank' class='external-link-to-page'>{{ slug }}</a>
	  </div>

	  <!-- right floating icons -->
	  <div class='pull-right'>
		<!-- cache and webperf scan history -->
		<span class='when-optimized-icon-container'>
			<span title='{{ when_last_everything }}'  class='when-optimized-icon material-icons'>history</span>  
		</span>
		<!-- edit page level settings link -->
	  	<a data-toggle='tooltip' title='Edit {% if page_level_settings_icon_class == "visible" %}MODIFIED {% endif %}Page Level Settings ' class='external-link edit-page-settings-icon {{page_level_settings_icon_class}}' href='post.php?post={{ id }}&action=edit#pegasaas_page_post_options'><i class='material-icons'>{{ page_level_settings_icon }}</i></a>
	  	
		<!-- enable/disable page prioritization -->  
		<span class='{{ page_prioritizations_available_class }}'>
			<i rel='{{ pid }}' class='fa-page-prioritization fa {{ fa_page_prioritization_class }}'></i>
		</span>

		<!-- possible CSS validation issues detected -->
	  	<a class='pull-right css-validation-issue {{css_validation_issue_class}}' href='https://jigsaw.w3.org/css-validator/validator?uri={{ post_url_accelerate_off_encoded }}&profile=css3svg&usermedium=all&warning=no&vextwarning=&lang=en' target='_blank'>
			<i class='material-icons material-icon-red' rel='tooltip' title='Possible CSS Validation Issues Detected.  This may affect the ability for Pegasaas to fetch an accurate Critical CSS snapshot.'>report_problem</i>
	  	</a>
		
		<!-- webp image issue -->
		<a class='pull-right webp-image-issues {{webp_image_issue_class}}' href='https://pegasaas.com/upgrade/?utm_source=pegasaas-accelerator-webp-issues-icon&upgrade_key=<?php echo PegasaasAccelerator::$settings['api_key']; ?>-<?php echo PegasaasAccelerator::$settings['installation_id']; ?>&site=<?php echo $pegasaas->utils->get_http_host(); ?>&current=<?php echo PegasaasAccelerator::$settings['subscription']; ?>' target='_blank'>
			<i class='material-icons material-icon-red' rel='tooltip' title='This page could be faster if you used WebP images.  Click this icon to UPGRADE to the PREMIUM API to have Pegasaas automatically serve WebP images to your visitors for a faster page load.'>add_photo_alternate</i>
		</a>
		
		<!-- execessive DOM issue --> 
		<a class='pull-right dom-size-issues {{dom_size_issue_class}}' href='https://pegasaas.com/upgrade/?utm_source=pegasaas-accelerator-dom-size-issues-icon&upgrade_key=<?php echo PegasaasAccelerator::$settings['api_key']; ?>-<?php echo PegasaasAccelerator::$settings['installation_id']; ?>&site=<?php echo $pegasaas->utils->get_http_host(); ?>&current=<?php echo PegasaasAccelerator::$settings['subscription']; ?>' target='_blank'>
			<i class='material-icons material-icon-red' rel='tooltip' title='The size of this webpage is causing it to load slowly.  Click this icon to UPGRADE to the PREMIUM API now to take advantage of a page load with our advanced page loading features that address this issue.'>thumb_down_alt</i>
		</a>

		<!-- google ads issue -->
		<a class='pull-right google-ads-issues {{google_ads_issue_class}}' href='https://pegasaas.com/upgrade/?utm_source=pegasaas-accelerator-google-ads-issue-icon&upgrade_key=<?php echo PegasaasAccelerator::$settings['api_key']; ?>-<?php echo PegasaasAccelerator::$settings['installation_id']; ?>&site=<?php echo $pegasaas->utils->get_http_host(); ?>&current=<?php echo PegasaasAccelerator::$settings['subscription']; ?>' target='_blank'>
			<i class='material-icons material-icon-red' rel='tooltip' title='This page is loading slowly due to the Google Ads API.  Click this icon to UPGRADE to the PREMIUM API now to take advantage of a faster page load with our advanced page loading features that automatically address this issue.'>monetization_on</i>
		</a>
	  </div>
	</td>
	<!-- Optimized -->
{% if settings.status > 0 %}
	<td class='page-cache-button-container'>
	  <?php include "cache-dropdown-3-lite.php"; ?>
	</td>

{% endif %}

	
	<!-- Baseline Web Peformance Metrics -->
				  
	
				<td rel='{{ pid }}' class='unaccelerated-pagespeed original-score-container left-bg {{ benchmark_scan_in_progress_class }}'>
	 				<table>
					  	<tr>
						  	<td>
							  	<table>
									<tr class='mobile-score-row'>
						   				<td class='fa-container'><i class='fa fa-fw fa-mobile'></i></td>
										<td class='perf-metrics-container'>
										  <table class='perf-metrics-table'>
											  <tr>
											    <td data-display-element='ttfb' class='perf-metric hidden-novice text-center {{ web_perf_metrics_ttfb_class }}' style='font-size: 8pt;'>
													<div class='perf-metric-{{ baseline_mobile_ttfb.class }} mobile-perf-score-ttfb'>
													{{ baseline_mobile_ttfb.value }}
													</div>												
												</td>
												<td data-display-element='fcp' class='perf-metric hidden-novice {{ web_perf_metrics_fcp_class }}'>
													<div class='perf-metric-{{ baseline_mobile_fcp.class }} mobile-perf-score-fcp'>
													{{ baseline_mobile_fcp.value }}
													</div>										
												</td>	
												<td data-display-element='si' class='perf-metric hidden-novice {{ web_perf_metrics_si_class }}'>
													<div class='perf-metric-{{ baseline_mobile_si.class }} mobile-perf-score-si'>
													{{ baseline_mobile_si.value }}
													</div>												
												</td>												  
												<td data-display-element='lcp' class='perf-metric hidden-novice {{ web_perf_metrics_lcp_class }}'>
													<div class='perf-metric-{{ baseline_mobile_lcp.class }} mobile-perf-score-lcp'>
													{{ baseline_mobile_lcp.value }}
													</div>											
												</td>												  
												<td data-display-element='tti' class='perf-metric hidden-novice hidden-intermediate {{ web_perf_metrics_tti_class }}'>
													<div class='perf-metric-{{ baseline_mobile_tti.class }} mobile-perf-score-tti'>
													{{ baseline_mobile_tti.value }}
													</div>												
												</td>
												<!--
												<td data-display-element='fci' class='perf-metric hidden-novice hidden-intermediate {{ web_perf_metrics_fci_class }}'>
													<div class='perf-metric-{{ baseline_mobile_fci.class }} mobile-perf-score-fci'>
													{{ baseline_mobile_fci.value }}
													</div>												
												</td>
												-->	
												<td data-display-element='tbt' class='perf-metric hidden-novice hidden-intermediate {{ web_perf_metrics_tbt_class }}'>
													<div class='perf-metric-{{ baseline_mobile_tbt.class }} mobile-perf-score-tbt'>
													{{ baseline_mobile_tbt.value }}
													</div>												
												</td>	
												<td data-display-element='cls' class='perf-metric hidden-novice hidden-intermediate {{ web_perf_metrics_cls_class }}'>
													<div class='perf-metric-{{ baseline_mobile_cls.class }} mobile-perf-score-cls'>
													{{ baseline_mobile_cls.value }}
													</div>												
												</td>													  
												<td class='progress-v2_6'>	
													<div class="progress mobile-score-container">
														<div class="progress-bar {{ benchmark_mobile_progress_class }}" 
															 role="progressbar" 
															 aria-valuenow="{{ benchmark_mobile_score }}" 
															 aria-valuemin="0" aria-valuemax="100" 
															 style="width:100%;">
															{{ benchmark_mobile_progress_bar_message }}


														</div>
													</div>
												</td>
											  </tr>
											</table>
										</td>
																				
			  						</tr>

  									<tr class='desktop-score-row left-bg'>
						 				<td class='fa-container' style='width: 30px;'><i class='fa fa-fw fa-desktop'></i></td>
										<td class='perf-metrics-container'>
										  <table class='perf-metrics-table'>
											  <tr>
													<td data-display-element='ttfb' class='perf-metric hidden-novice text-center {{ web_perf_metrics_ttfb_class }}' style='font-size: 8pt;'>
														<div class='perf-metric-{{ baseline_desktop_ttfb.class }} desktop-perf-score-ttfb'>
														{{ baseline_desktop_ttfb.value }}
														</div>												
													</td>
													<td data-display-element='fcp' class='perf-metric hidden-novice {{ web_perf_metrics_fcp_class }}'>
														<div class='perf-metric-{{ baseline_desktop_fcp.class }} desktop-perf-score-fcp'>
														{{ baseline_desktop_fcp.value }}
														</div>											
													</td>
													<td data-display-element='si' class='perf-metric hidden-novice {{ web_perf_metrics_si_class }}'>
														<div class='perf-metric-{{ baseline_desktop_si.class }} desktop-perf-score-si'>
														{{ baseline_desktop_si.value }}
														</div>												
													</td>												  
													<td data-display-element='lcp' class='perf-metric hidden-novice {{ web_perf_metrics_lcp_class }}'>
														<div class='perf-metric-{{ baseline_desktop_lcp.class }} desktop-perf-score-lcp'>
														{{ baseline_desktop_lcp.value }}
														</div>												
													</td>	
													<td data-display-element='tti' class='perf-metric hidden-novice hidden-intermediate {{ web_perf_metrics_tti_class }}'>
														<div class='perf-metric-{{ baseline_desktop_tti.class }} desktop-perf-score-tti'>
														{{ baseline_desktop_tti.value }}
														</div>												
													</td>			
													<!--									  
													<td data-display-element='fci' class='perf-metric hidden-novice hidden-intermediate {{ web_perf_metrics_fci_class }}'>
														<div class='perf-metric-{{ baseline_desktop_fci.class }} desktop-perf-score-fci'>
														{{ baseline_desktop_fci.value }}
														</div>												
													</td>
													-->
													<td data-display-element='tbt' class='perf-metric hidden-novice hidden-intermediate {{ web_perf_metrics_tbt_class }}'>
														<div class='perf-metric-{{ baseline_desktop_tbt.class }} desktop-perf-score-tbt'>
														{{ baseline_desktop_tbt.value }}
														</div>												
													</td>
													<td data-display-element='cls' class='perf-metric hidden-novice hidden-intermediate  {{ web_perf_metrics_cls_class }}'>
														<div class='perf-metric-{{ baseline_desktop_cls.class }} desktop-perf-score-cls'>
														{{ baseline_desktop_cls.value }}
														</div>												
													</td>												  
													<td class='progress-v2_6'>
														<div class="progress desktop-score-container">
															<div class="progress-bar {{ benchmark_desktop_progress_class }}" 
																 role="progressbar" aria-valuenow="{{ benchmark_score }}" aria-valuemin="0" aria-valuemax="100" 
																 style="width: 100%;">
																{{ benchmark_desktop_progress_bar_message }}

															</div>
														</div>
													</td>
												</tr>
											</table>
										</td>
			  						</tr>		 
								</table>
						  	</td>
						  	<td class='benchmark-dropdown-menu-container'>
								<form class='gtmetrix-form' target="_blank" action='https://gtmetrix.com/analyze.html' method='post'>
						  			<input type='url' name='url' value='<?php echo $pegasaas->get_home_url(); ?>{{ slug }}?accelerate=off' />
					  			</form>
				  				<form class='pull-right rescan-benchmark' style='margin-bottom: 0px;' rel='{{ pid }}'>
				  					<input type='hidden' name='pid' value='{{ pid }}' />
									<input type='hidden' name='post_id' value='{{ id }}' />
				  					<input type='hidden' name='c' value='rescan-pagespeed' />
				  	
									<div class="btn-group">					
					  					<button type="button" class="btn btn-primary btn-caret btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<span class="fa fa-ellipsis-v"></span>
											<span class="sr-only">Toggle Dropdown</span>
					  					</button>		
										<ul class="dropdown-menu">
											<li><a href='#' class='initiate-rescan-pagespeed-benchmark'><i class='<?php echo PEGASAAS_RESCAN_ICON_CLASS; ?>'></i> Rescan Baseline Performance Metrics</a></li>
											<li><a href='https://developers.google.com/speed/pagespeed/insights/?url={{ post_url_accelerate_off_encoded }}' target='_blank'><i class='<?php echo PEGASAAS_GOOGLE_ICON_CLASS; ?>'></i> Verify Metrics with Google PageSpeed Insights</a></li>
											<li><a href='#' class='hidden-novice scan-with-gtmetrix'><i class='<?php echo PEGASAAS_GTMETRIX_ICON_CLASS; ?>'></i> Scan with GTMetrix</a></li>
										</ul>
									</div>
				  				</form>								
							</td>
						</tr>
					</table>
				</td>
				    
				<td class='accelerated-score-container right-bg {{ accelerated_scan_in_progress_class }}'>
					<table>
						<tr>
							<td>
				   				<table>					   
 									<tr class='mobile-score-row'>
					   					<td class='fa-container' style='width: 30px;'><i class='<?php echo PEGASAAS_MOBILE_ICON_CLASS; ?>'></i></td>
										<td class='perf-metrics-container'>
										  <table class='perf-metrics-table'>
											  <tr>
										<td data-display-element='ttfb' class='perf-metric hidden-novice text-center {{ web_perf_metrics_ttfb_class }}' style='font-size: 8pt;'>
											<div class='perf-metric-{{ accelerated_mobile_ttfb.class }} mobile-perf-score-ttfb'>
											{{ accelerated_mobile_ttfb.value }}
											</div>												
										</td>
										<td data-display-element='fcp' class='perf-metric hidden-novice {{ web_perf_metrics_fcp_class }}'>
											<div class='perf-metric-{{ accelerated_mobile_fcp.class }} mobile-perf-score-fcp'>
											{{ accelerated_mobile_fcp.value }}
											</div>											
										</td>	
										<td data-display-element='si' class='perf-metric hidden-novice {{ web_perf_metrics_si_class }}'>
											<div class='perf-metric-{{ accelerated_mobile_si.class }} mobile-perf-score-si'>
											{{ accelerated_mobile_si.value }}
											</div>												
										</td>												  
										<td data-display-element='lcp' class='perf-metric hidden-novice {{ web_perf_metrics_lcp_class }}'>
											<div class='perf-metric-{{ accelerated_mobile_lcp.class }} mobile-perf-score-lcp'>
											{{ accelerated_mobile_lcp.value }}
											</div>												
										</td>												  
		
										<td data-display-element='tti' class='perf-metric hidden-novice hidden-intermediate {{ web_perf_metrics_tti_class }}'>
											<div class='perf-metric-{{ accelerated_mobile_tti.class }} mobile-perf-score-tti'>
											{{ accelerated_mobile_tti.value }}
											</div>												
										</td>
										<!--
										<td data-display-element='fci' class='perf-metric hidden-novice hidden-intermediate {{ web_perf_metrics_fci_class }}'>
											<div class='perf-metric-{{ accelerated_mobile_fci.class }} mobile-perf-score-fci'>
											{{ accelerated_mobile_fci.value }}
											</div>												
										</td>												  
										-->
										<td data-display-element='tbt' class='perf-metric hidden-novice hidden-intermediate {{ web_perf_metrics_tbt_class }}'>
											<div class='perf-metric-{{ accelerated_mobile_tbt.class }} mobile-perf-score-tbt'>
											{{ accelerated_mobile_tbt.value }}
											</div>												
										</td> 		
										<td data-display-element='cls' class='perf-metric hidden-novice hidden-intermediate {{ web_perf_metrics_cls_class }}'>
											<div class='perf-metric-{{ accelerated_mobile_cls.class }} mobile-perf-score-cls'>
											{{ accelerated_mobile_cls.value }}
											</div>												
										</td> 													  
			   							<td class='progress-v2_6'>
											<div class="progress mobile-score-container" rel='{{ slug }}'>
  												<div class="progress-bar {{ accelerated_mobile_progress_class }}" 
													 role="progressbar" 
													 aria-valuenow="{{ mobile_score }}" aria-valuemin="0" aria-valuemax="100" 
													 style="width: 100%;">
    											{{ accelerated_mobile_progress_bar_message }}					
  												</div>
											</div>	
					   					</td>	
												</tr>
												</table>
												</td>
					   				</tr>					   
					   
				     				<tr class='desktop-score-row'>
						 				<td class='fa-container' style='width: 30px;'><i class='<?php echo PEGASAAS_DESKTOP_ICON_CLASS; ?>'></i></td>
										<td class='perf-metrics-container'>
										  <table class='perf-metrics-table'>
											  <tr>
										<td data-display-element='ttfb' class='perf-metric hidden-novice text-center {{ web_perf_metrics_ttfb_class }}' style='font-size: 8pt;'>
											<div class='perf-metric-{{ accelerated_desktop_ttfb.class }} desktop-perf-score-ttfb'>
											{{ accelerated_desktop_ttfb.value }}
											</div>												
										</td>
										<td data-display-element='fcp' class='perf-metric hidden-novice {{ web_perf_metrics_fcp_class }}'>
											<div class='perf-metric-{{ accelerated_desktop_fcp.class }} desktop-perf-score-fcp'>
											{{ accelerated_desktop_fcp.value }}
											</div>											
										</td>
										<td data-display-element='si' class='perf-metric hidden-novice {{ web_perf_metrics_si_class }}'>
											<div class='perf-metric-{{ accelerated_desktop_si.class }} desktop-perf-score-si'>
											{{ accelerated_desktop_si.value }}
											</div>												
										</td>												  
										<td data-display-element='lcp' class='perf-metric hidden-novice hidden-intermediate {{ web_perf_metrics_lcp_class }}'>
											<div class='perf-metric-{{ accelerated_desktop_lcp.class }} desktop-perf-score-lcp'>
											{{ accelerated_desktop_lcp.value }}
											</div>												
										</td>		  
										<td data-display-element='tti' class='perf-metric hidden-novice hidden-intermediate {{ web_perf_metrics_tti_class }}'>
											<div class='perf-metric-{{ accelerated_desktop_tti.class }} desktop-perf-score-tti'>
											{{ accelerated_desktop_tti.value }}
											</div>												
										</td> 
										<!--
										<td data-display-element='fci' class='perf-metric hidden-novice hidden-intermediate {{ web_perf_metrics_fci_class }}'>
											<div class='perf-metric-{{ accelerated_desktop_fci.class }} desktop-perf-score-fci'>
											{{ accelerated_desktop_fci.value }}
											</div>											
										</td>	
										-->
										<td data-display-element='tbt' class='perf-metric hidden-novice hidden-intermediate {{ web_perf_metrics_tbt_class }}'>
											<div class='perf-metric-{{ accelerated_desktop_tbt.class }} desktop-perf-score-tbt'>
											{{ accelerated_desktop_tbt.value }}
											</div>												
										</td>	
										<td data-display-element='cls' class='perf-metric hidden-novice hidden-intermediate {{ web_perf_metrics_cls_class }}'>
											<div class='perf-metric-{{ accelerated_desktop_cls.class }} desktop-perf-score-cls'>
											{{ accelerated_desktop_cls.value }}
											</div>												
										</td>												  
	 									<td class='progress-v2_6'>
											<div class="progress desktop-score-container" rel='{{ slug }}'>
 
											<div class="progress-bar {{ accelerated_desktop_progress_class }}" 
												 role="progressbar" 
												 aria-valuenow="{{ score }}" aria-valuemin="0" aria-valuemax="100" 
												 style="width: 100%;">
    											{{ accelerated_desktop_progress_bar_message }}	
  											</div>
										</td>
											</tr>
												</table>
												</td>
					   				</tr> 
								</table>					  
						  	</td>
						  	<td class='benchmark-dropdown-menu-container'>
								<form class='gtmetrix-form' target="_blank" action='https://gtmetrix.com/analyze.html' method='post'>
						  			<input type='url' name='url' value='<?php echo $pegasaas->get_home_url(); ?>{{ slug }}' />
					  			</form>
								<form class='pull-right rescan-pagespeed {% if this_one_scanning %}scanning{% endif %}' 
									  style='margin-bottom: 0px;' 
									  rel='{{ pid }}'>
				  					<input type='hidden' name='pid' value='{{ pid }}' />
				  					<input type='hidden' name='post_id' value='{{ id }}' />
				  					<input type='hidden' name='c' value='rescan-pagespeed' />
				  	
									<div class="btn-group">					
					  					<button type="button" class="btn btn-primary btn-caret btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<span class="fa fa-ellipsis-v"></span>
											<span class="sr-only">Toggle Dropdown</span>
					  					</button>		
										<ul class="dropdown-menu">
											<li><a href='#' class='initiate-rescan-pagespeed'><i class='<?php echo PEGASAAS_RESCAN_ICON_CLASS; ?>'></i> Rescan Accelerated Performance Metrics</a></li>
											<li><a href='https://developers.google.com/speed/pagespeed/insights/?url={{ post_url_encoded }}' target='_blank'><i class='<?php echo PEGASAAS_GOOGLE_ICON_CLASS; ?>'></i> Verify Metrics with Google PageSpeed Insights</a></li>
											<li class='hidden-novice'><a href='#' class='scan-with-gtmetrix'><i class='<?php echo PEGASAAS_GTMETRIX_ICON_CLASS; ?>'></i> Scan with GTMetrix</a></li>
										</ul>
									</div>
					  
					  	
				  	
				  </form>								
							
							</td>
						</tr>
					</table>
				</td>	
			</tr>