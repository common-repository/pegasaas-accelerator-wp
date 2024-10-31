
jQuery(document).ready(function() {
	
	jQuery(".pa-staging-mode-go-live-link").bind("click", function(e) {
		e.preventDefault();
		var resource_id = jQuery(this).attr("rel");

		var $this 		= this;
		
		jQuery(this).addClass("hidden");
		jQuery(this).parents("span").find(".pa-staging-mode-stage-page-link").removeClass("hidden");
	
		
								
		var action = "pegasaas_disable_staging_for_page";

	
									
			jQuery.post(ajaxurl, 
						{ 'action'      : action, 
						  'api_key'     : jQuery("#pegasaas--api-key").val(), 
						  'resource_id' : resource_id }, 
						function(data) {
							
			
							// if the status returned is "success"
							if (data['status'] == "1") {
								jQuery($this).parents("tr").find("input.js-switch").prop("indeterminate", false);
								jQuery($this).parents("tr").find("input.js-switch").data("sw").colorize();
								
													
							} else  { 
							
							}
						}, 
						"json");
		
		});
	
	jQuery(".pa-staging-mode-stage-page-link").bind("click", function(e) {
		e.preventDefault();
		var resource_id = jQuery(this).attr("rel");

		var $this 		= this;
		
		jQuery(this).addClass("hidden");
		jQuery(this).parents("span").find(".pa-staging-mode-go-live-link").removeClass("hidden");
	
		
								
		var action = "pegasaas_enable_staging_for_page";

	
									
			jQuery.post(ajaxurl, 
						{ 'action'      : action, 
						  'api_key'     : jQuery("#pegasaas--api-key").val(), 
						  'resource_id' : resource_id }, 
						function(data) {
							
			
							// if the status returned is "success"
							if (data['status'] == "1") {
								jQuery($this).parents("tr").find("input.js-switch").prop("indeterminate", true);
								jQuery($this).parents("tr").find("input.js-switch").data("sw").colorize();
								
													
							} else  { 
							
							}
						}, 
						"json");
		
		});
	
	
	jQuery(".pegasaas-accelerator-clear-cache-link").click(
		function(e) {
			e.preventDefault();
			// grab the page/post id that is stored in the 'rel' attribute
			var resource_id = jQuery(this).attr("rel");
			var $this 		= this;
									
			jQuery.post(ajaxurl, 
						{ 'action'     : 'pegasaas_clear_page_cache', 
					  	  'api_key'    : jQuery("#pegasaas--api-key").val(), 
					  	  'resource_id': resource_id }, 
						function(data) {
							if (data['status'] == "1") {
								jQuery($this).parents("tr").find(".pegasaas-accelerator-cache-icon").removeClass('existing');
								jQuery($this).parents("tr").find(".pegasaas-accelerator-cache-icon").removeClass('temp-existing');
								 
							} else  { 
								alert(data['message']);
							}
						}, 
						"json");
	});	
	

	jQuery(".pegasaas-accelerator-build-cache-link").click(
	
		function(e) {
			e.preventDefault();
			// grab the page/post id that is stored in the 'rel' attribute
			var resource_id = jQuery(this).attr("rel");
			var $this 		= this;
									
			jQuery.post(ajaxurl, 
						{ 'action'     : 'pegasaas_build_page_cache', 
					  	  'api_key'    : jQuery("#pegasaas--api-key").val(), 
					  	  'resource_id': resource_id }, 
						function(data) {
							if (data['status'] == "1") {
								jQuery($this).parents("tr").find(".pegasaas-accelerator-cache-icon").addClass('temp-existing');
								 
							} else  { 
								alert(data['message']);
							}
						}, 
						"json");
	});		
	
	
	
	/* this listener is currently not used */
	jQuery(".pegasaas-accelerator-html-cache-icon").click(
		function() {
			// grab the page/post id that is stored in the 'rel' attribute
			var resource_id = jQuery(this).attr("rel");
			var $this 		= this;
									
			jQuery.post(ajaxurl, 
						{ 'action'     : 'pegasaas_clear_page_cache', 
					  	  'api_key'    : jQuery("#pegasaas--api-key").val(), 
					  	  'resource_id': resource_id }, 
						function(data) {
							if (data['status'] == "1") {
								jQuery($this).removeClass('existing');
								 
							} else  { 
								alert(data['message']);
							}
						}, 
						"json");
	});

	/* this listener is currently not used */
	jQuery(".pegasaas-accelerator-js-cache-icon").click(
		function() {
			var resource_id = jQuery(this).attr("rel");
			var $this 		= this;
			
			jQuery.post(ajaxurl, 
						{ 'action'       : 'pegasaas_clear_js_cache', 
						  'api_key'      : jQuery("#pegasaas--api-key").val(), 
						   'resource_id' : resource_id}, 
						function(data) {
							if (data['status'] == "1") {
								jQuery($this).removeClass('existing');
							} else  { 
								alert(data['message']);
							}
						}, 
						"json");
		});
								
	/* this listener is currently not used */
	jQuery(".pegasaas-accelerator-css-cache-icon").click(
		function() {
			var resource_id = jQuery(this).attr("rel");
			var $this 		= this;
									
			jQuery.post(ajaxurl, 
						{ 'action'      : 'pegasaas_clear_css_cache', 
						  'api_key'     : jQuery("#pegasaas--api-key").val(), 
						  'resource_id' : resource_id}, 
						function(data) {
							if (data['status'] == "1") {
								jQuery($this).removeClass('existing');
							} else  { 
								alert(data['message']);
							}
						}, 
						"json");
		});


	/* used on the pages/posts page to change the accelerated status of a page/post */
	/* Summary: on click, submit to the ajax handler to change the "accelerated" status for this page/post */
	jQuery(".pegasaas-accelerator-accelerated-icon").click(
		function() {
			// get the page/post id that is stored in the 'rel' attribute
			var resource_id = jQuery(this).attr("rel");
			var $this 		= this;
								
			// "existing" indicates that acceleration for the page/post is "enabled"
			if (jQuery(this).hasClass("existing")) {
				var action = "pegasaas_disable_accelerator_for_page";
			} else {
				var action = "pegasaas_enable_accelerator_for_page";
			}
			
									
			jQuery.post(ajaxurl, 
						{ 'action'      : action, 
						  'api_key'     : jQuery("#pegasaas--api-key").val(), 
						  'resource_id' : resource_id }, 
						function(data) {
							// if the status returned is "success"
							if (data['status'] == "1") {
								// if the page is currently marked as having "acceleration" enabled, then remove the status 
								if (jQuery($this).hasClass("existing")) {
								 	jQuery($this).removeClass('existing');
									jQuery($this).parents("tr").find(".pegasaas-accelerator-pagespeed-score-container").click();
								} else {
									jQuery($this).addClass('existing');
								}
								
								jQuery($this).parents("tr").find(".pegasaas-accelerator-pagespeed-score-container").click();
													
							} else  { 
								alert(data['message']);
							}
						}, 
						"json");
			});

/*
								jQuery(".pegasaas-accelerator-pagespeed-score-containerx").click(function() {
																									
									var resource_id = jQuery(this).attr("rel");
									var $this = this;
									if (jQuery(this).hasClass("unknown")) {
									  
									  if (jQuery(this).hasClass("scanning")) {
										  var action = "pegasaas_cancel_pagespeed_score_request";
										  jQuery(this).removeClass("scanning");
										  
										  return;
									  } else {
										  var action = "pegasaas_request_pagespeed_score";
									  	 //alert("The page speed score takes a few minutes to generate.  You may continue to go about your work while the system determines the score for this page.");
									  	 jQuery(this).addClass("scanning");
									  
									 // return;
									  }
									} else {
									  var action = "pegasaas_request_pagespeed_score";
									  	 //alert("The page speed score takes a few minutes to generate.  You may continue to go about your work while the system determines the score for this page.");
									  jQuery(this).addClass("scanning");
									  
									 // alert("There is already a score available for this page.")
									 // return;
									}
									jQuery(this).find(".pegasaas-accelerator-pagespeed-data-overview").html("<center><i class='fa fa-circle-o-notch fa-spin'></i></center>");

									jQuery.post(ajaxurl, 
											{ 'action': action, 'api_key': jQuery("#pegasaas--api-key").val(), 'resource_id': resource_id}, 
											function(data) {
												jQuery($this).removeClass('scanning');
												jQuery($this).removeClass('unknown');
												jQuery($this).removeClass('excellent');
												jQuery($this).removeClass('good');
												jQuery($this).removeClass('needs-improvement');
												jQuery($this).removeClass('bad');
												
												if (data['score'] != "") { 
													//jQuery($this).remo('unknown');
													if (data['score'] >= "95") {
														jQuery($this).addClass('excellent');
													} else if (data['score'] >= "85") {
														jQuery($this).addClass('good');
													} else if (data['score'] >= "75") {
														jQuery($this).addClass('needs-improvement');
													} else {
														jQuery($this).addClass('bad');
													}
													
												
													//alert(jQuery($this).hasClass("existing"));
												} else  { 
												 // alert(data['message']);
												}
											}, 
											"json");


								});
								
		*/

/* If on the pages/posts page */
if (jQuery("body").find(".pegasaas-accelerator-pagespeed-score-container").length > 0) {
//	var intervalTimer = setInterval('pegasaas_accelerator_check_scores()', 120000);
	
	var popover_options = { 'title': 'Optimization Overview', 
						    'trigger': 'click', 
						    'placement': 'left', 
						    'html': true, 
						    'template': '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>', 
						    'content': function() { return jQuery(this).find(".pegasaas-accelerator-pagespeed-data-overview").html(); } };
		
		
	jQuery('[data-toggle="popover"]').popover(popover_options);
	 
	var count = 0;
	 
	jQuery('[data-toggle="popover"]').on('show.bs.popover', 
										 function() { 
	
											var currently_selected_popover = this;						
		  									jQuery('[data-toggle="popover"]').each(
												function() {
													if (this != currently_selected_popover) {
														jQuery(this).popover("hide");
													}
												});
										  });
		  
		  
	jQuery('[data-toggle="popover"]').on('inserted.bs.popover', 
										 function() { 
	
		  									var popover = jQuery(this).find("[role='tooltip']");
		  									count++;
		  
		  									var data = jQuery(this).data("bs.popover");
		  									var data_container = this;

		  									var tip = data.tip();

		  // set behaviour of SCAN NOW button
		  var scan_now_button = tip.find(".pegasaas-scan-now-button");
		  scan_now_button.click(function(e) {
			e.preventDefault();
			var resource_id = jQuery(this).parents(".pegasaas-accelerator-pagespeed-data-container").attr("rel");
			var $this = jQuery(".pegasaas-accelerator-pagespeed-score-container[rel='"+resource_id+"']");
			jQuery(this).attr("disabled", "disabled");
			
			if (jQuery($this).hasClass("unknown")) {
									  
			  if (jQuery($this).hasClass("scanning")) {
				  var action = "pegasaas_cancel_pagespeed_score_request";
				  jQuery($this).removeClass("scanning");				  
				  return;
			  } else {
				 var action = "pegasaas_request_pagespeed_score";
				 jQuery($this).addClass("scanning");
			  
			  }
			} else {
			  var action = "pegasaas_request_pagespeed_score";
			  jQuery($this).addClass("scanning");
			}
			
			  clearInterval(intervalTimer);
			 // console.log("reset interval timer");
		//	  intervalTimer = setInterval('pegasaas_accelerator_check_scores()', 120000);

// set the overview container to a loading icon		
			jQuery(".pegasaas-accelerator-pagespeed-score-container[rel='"+resource_id+"'] .pegasaas-accelerator-pagespeed-data-overview").html("<center><i class='fa fa-circle-o-notch fa-spin'></i></center>");
			
			// close the overview popup
			jQuery(data_container).popover("hide");
			
			// submit the request

			jQuery.post(ajaxurl, 
				{ 'action': action, 'api_key': jQuery("#pegasaas--api-key").val(), 'resource_id': resource_id}, 
				function(data) {
				//console.log("alpha");
					// set the button of the source container
					jQuery(".pegasaas-accelerator-pagespeed-score-container[rel='"+resource_id+"'] .pegasaas-scan-now-button").attr("disabled", "disabled");
					jQuery(".pegasaas-accelerator-pagespeed-score-container[rel='"+resource_id+"'] .pegasaas-accelerator-last-scan p").remove();
					
					var pa_psc = jQuery(".pegasaas-accelerator-pagespeed-score-container[rel='"+resource_id+"']");
						jQuery(pa_psc).removeClass('unknown');
						jQuery(pa_psc).removeClass('excellent');
						jQuery(pa_psc).removeClass('good');
						jQuery(pa_psc).removeClass('needs-improvement');
						jQuery(pa_psc).removeClass('bad');	
						jQuery(pa_psc).find(".pegasaas-accelerator-pagespeed-score-icon").removeClass("unknown");
						jQuery(pa_psc).find(".pegasaas-accelerator-pagespeed-score-icon").removeClass("excellent");
						jQuery(pa_psc).find(".pegasaas-accelerator-pagespeed-score-icon").removeClass("good");
						jQuery(pa_psc).find(".pegasaas-accelerator-pagespeed-score-icon").removeClass("needs-improvement");
						jQuery(pa_psc).find(".pegasaas-accelerator-pagespeed-score-icon").removeClass("bad");
					
					if (data['score'] != "" && data['score'] != 'undefined' && data['score'] != undefined) { 
						jQuery(pa_psc).removeClass('scanning');

					//	console.log("score returned is " + data['score']);
						//jQuery($this).remo('unknown');
						if (data['score'] >= "95") {
							jQuery(pa_psc).addClass('excellent');
						} else if (data['score'] >= "85") {
							jQuery(pa_psc).addClass('good');
						} else if (data['score'] >= "75") {
							jQuery(pa_psc).addClass('needs-improvement');
						} else {
							jQuery(pa_psc).addClass('bad');
						}
						
					
						//alert(jQuery($this).hasClass("existing"));
					} else  { 
						//console.log("no score returned " + data['score']);
					 // alert(data['message']);
					}
				}, 
				"json");
		  	});		  
		 
		  
		  
		  // set behaviour of CLEAR HTML CACHE button
		  var clear_html_cache_button = tip.find(".pegasaas-clear-html-cache-button");
	
		clear_html_cache_button.click(function(e) {
			e.preventDefault();
			//alert("yeah");
			  
			var resource_id = jQuery(this).parents(".pegasaas-accelerator-pagespeed-data-container").attr("rel");
			var $this = this;
			jQuery(this).attr("disabled", "disabled");
			//alert(jQuery("#pegasaas--api-key").val());
			jQuery.post(ajaxurl, 
				{ 'action': 'pegasaas_clear_page_cache', 'api_key': jQuery("#pegasaas--api-key").val(), 'resource_id': resource_id}, 
				function(data) {
					//alert(data);
					if (data['status'] == "1") {
						//alert(jQuery($this).hasClass("existing"));
					  jQuery($this).html("(0KB)");
					  jQuery($this).parents(".pegasaas-accelerator-html-cache-info").find("p").remove();
					  jQuery(".pegasaas-accelerator-pagespeed-score-container[rel='"+resource_id+"'] .pegasaas-clear-html-cache-button").html("(0KB)");
					  jQuery(".pegasaas-accelerator-pagespeed-score-container[rel='"+resource_id+"'] .pegasaas-accelerator-html-cache-info p").remove();
					  jQuery(".pegasaas-accelerator-pagespeed-score-container[rel='"+resource_id+"'] .pegasaas-clear-html-cache-button").attr("disabled", "disabled");
						//alert(jQuery($this).hasClass("existing"));
					} else  { 
					  alert("" + data['message']);
					}
				}, 
				"json");																										
		  	});
		  
		  // set behaviour of CLEAR CSS CACHE button
		  var clear_css_cache_button = tip.find(".pegasaas-clear-css-cache-button");
		  clear_css_cache_button.click(function(e) {
			e.preventDefault();
			var resource_id = jQuery(this).parents(".pegasaas-accelerator-pagespeed-data-container").attr("rel");
			var $this = this;
			jQuery(this).attr("disabled", "disabled");
			jQuery.post(ajaxurl, 
				{ 'action': 'pegasaas_clear_css_cache', 'api_key': jQuery("#pegasaas--api-key").val(), 'resource_id': resource_id}, 
				function(data) {
					if (data['status'] == "1") {
					  jQuery($this).html("(0KB)");
					  jQuery($this).parents(".pegasaas-accelerator-css-cache-info").find("p").remove();
					  jQuery(".pegasaas-accelerator-pagespeed-score-container[rel='"+resource_id+"'] .pegasaas-clear-css-cache-button").html("(0KB)");
					  jQuery(".pegasaas-accelerator-pagespeed-score-container[rel='"+resource_id+"'] .pegasaas-accelerator-css-cache-info p").remove();
					  jQuery(".pegasaas-accelerator-pagespeed-score-container[rel='"+resource_id+"'] .pegasaas-clear-css-cache-button").attr("disabled", "disabled");
					} else  { 
					  alert(data['message']);
					}
				}, 
				"json");																										
		  	});



		  // set behaviour of CLEAR JS CACHE button
		  var clear_js_cache_button = tip.find(".pegasaas-clear-js-cache-button");
		  clear_js_cache_button.click(function(e) {
			e.preventDefault();
			var resource_id = jQuery(this).parents(".pegasaas-accelerator-pagespeed-data-container").attr("rel");
			var $this = this;
			jQuery(this).attr("disabled", "disabled");
			jQuery.post(ajaxurl, 
				{ 'action': 'pegasaas_clear_js_cache', 'api_key': jQuery("#pegasaas--api-key").val(), 'resource_id': resource_id}, 
				function(data) {
					if (data['status'] == "1") {
					  jQuery($this).html("(0KB)");
					  jQuery($this).parents(".pegasaas-accelerator-css-cache-info").find("p").remove();
					  jQuery(".pegasaas-accelerator-pagespeed-score-container[rel='"+resource_id+"'] .pegasaas-clear-js-cache-button").html("(0KB)");
					  jQuery(".pegasaas-accelerator-pagespeed-score-container[rel='"+resource_id+"'] .pegasaas-accelerator-js-cache-info p").remove();
					  jQuery(".pegasaas-accelerator-pagespeed-score-container[rel='"+resource_id+"'] .pegasaas-clear-js-cache-button").attr("disabled", "disabled");
					} else  { 
					  alert(data['message']);
					}
				}, 
				"json");																										
		  	});

	});

}


});

function pegasaas_accelerator_fetch_opportunities_html(resource_id) {

	jQuery.post(ajaxurl, 
				{ 'action': 'pegasaas_fetch_pagespeed_opportunities_html', 'resource_id': resource_id, 'api_key': jQuery("#pegasaas--api-key").val()}, 
				function (data) {
					console.log("got data");
					//console.log(data);
					var resource_id = data['resource_id'];
					var html = data['html'];
					jQuery(".pegasaas-accelerator-pagespeed-score-container[rel='"+resource_id+"'] .pegasaas-accelerator-pagespeed-data-overview").html(html);	
					
				}, "json");
	
}

function pegasaas_accelerator_check_scores() {
	//alert("starting");
	//console.log("tick");
	var pending_requests = new Array();
	jQuery(".pegasaas-accelerator-pagespeed-score-container.scanning").each(function() {
																					 pending_requests.push(jQuery(this).attr("rel"));
																					 });
	//alert(pending_requests.length);
	
	jQuery.post(ajaxurl, 
											{ 'action': 'pegasaas_check_queued_pagespeed_score_requests', 'pending_requests': pending_requests, 'api_key': jQuery("#pegasaas--api-key").val()}, 
											function(data) {
												if (data['status'] == -1) {
													console.log("API Key Error in fetching new scores");
												} else {
													for (var resource_id in data) {
													//  alert(resource_id);	
													  var the_container = jQuery(".pegasaas-accelerator-pagespeed-score-container.scanning[rel='"+resource_id+"']");
													  jQuery(the_container).removeClass("scanning");
													  var the_icon =  jQuery(the_container).find(".pegasaas-accelerator-pagespeed-score-icon");
													  if (data[resource_id] != "") { 
														pegasaas_accelerator_fetch_opportunities_html(resource_id);
														
														jQuery(the_icon).html(data[resource_id]);
														  
														if (data[resource_id] >= "95") {
															jQuery(the_icon).addClass('excellent');
													} else if (data[resource_id] >= "85") {
														jQuery(the_icon).addClass('good');
													} else if (data[resource_id] >= "75") {
														jQuery(the_icon).addClass('needs-improvement');
													} else {
														jQuery(the_icon).addClass('bad');
													}
													
												
												
												} else  { 
												 // alert(data['message']);
												}
												
												}
												}
											}, 
											"json");

	//setTimeout('pegasaas_accelerator_check_scores', 5000);

}

jQuery(".indv-js-switch.staging-mode-active").prop("indeterminate", true);

jQuery(".indv-js-switch ").each(function() { 
	
	var sw = new Switchery(this, {size: 'small'} ); 
	jQuery(this).data("sw", sw);
});



jQuery(".indv-js-switch.excluded-resource + .switchery.switchery-small").bind("click", function() {
	
		alert("This post has been excluded from being optimized.  If you want to enable this page/post, please refine the settings under the Settings -> Misc -> Excluded URLs panel.");
	
	
});

jQuery(".indv-js-switch ").bind("change", function(e) {
		var resource_id = jQuery(this).attr("data-pegasaas-resource-id");
		var $this 		= this;
	
		if (jQuery(this).hasClass("revert")){
			jQuery(this).removeClass("revert");
			return;
		}
				
		if (jQuery(this).hasClass("resource-prioritization")) {
			if (jQuery(this).hasClass("resource-prioritized")) {
				var action = "pegasaas_disable_prioritization_for_page";
			} else {
				var action = "pegasaas_enable_prioritization_for_page";
			}
		} else {
			// "existing" indicates that acceleration for the page/post is "enabled"
			if (jQuery(this).hasClass("resource-accelerated")) {
				var action = "pegasaas_disable_accelerator_for_page";
			} else {
				var action = "pegasaas_enable_accelerator_for_page";
			}
		}
		
									
			jQuery.post(ajaxurl, 
						{ 'action'      : action, 
						  'api_key'     : jQuery("#pegasaas--api-key").val(), 
						  'resource_id' : resource_id }, 
						function(data) {
			
							// if the status returned is "success"
							if (data['status'] == "1") {
								if (jQuery($this).hasClass("resource-prioritization")) {
									// if the page is currently marked as having "acceleration" enabled, then remove the status 
									if (jQuery($this).hasClass("resource-prioritized")) {
										jQuery($this).removeClass('resource-prioritized');
										//jQuery($this).parents("tr").find(".pa-staging-mode-links").addClass('hidden');
										//jQuery($this).parents("tr").find(".pa-build-cache-link").addClass('hidden');
										//jQuery($this).parents("tr").find(".pa-claer-cache-link").addClass('hidden');

										jQuery($this).parents("#pegasaas-accelerator__page_post_options_sidebar").addClass('prioritization-disabled');

										//jQuery($this).parents("tr").find(".pegasaas-accelerator-cache-icon").removeClass("existing");
										//jQuery($this).parents("tr").find(".pegasaas-accelerator-cache-icon").removeClass("request-existing");

									} else {
										//jQuery($this).parents("tr").find(".pa-staging-mode-links").removeClass('hidden');
										//jQuery($this).parents("tr").find(".pa-build-cache-link").removeClass('hidden');
										//jQuery($this).parents("tr").find(".pa-clear-cache-link").removeClass('hidden');
										jQuery($this).addClass('resource-prioritized');
										jQuery($this).parents("#pegasaas-accelerator__page_post_options_sidebar").removeClass('prioritization-disabled');

									}
								} else {
								
									// if the page is currently marked as having "acceleration" enabled, then remove the status 
									if (jQuery($this).hasClass("resource-accelerated")) {
										jQuery($this).removeClass('resource-accelerated');
										jQuery($this).parents("tr").find(".pa-staging-mode-links").addClass('hidden');
										jQuery($this).parents("tr").find(".pa-build-cache-link").addClass('hidden');
										jQuery($this).parents("tr").find(".pa-claer-cache-link").addClass('hidden');

										jQuery($this).parents("#pegasaas-accelerator__page_post_options_sidebar").addClass('pegasaas-disabled');

										jQuery($this).parents("tr").find(".pegasaas-accelerator-cache-icon").removeClass("existing");
										jQuery($this).parents("tr").find(".pegasaas-accelerator-cache-icon").removeClass("request-existing");

									} else {
										jQuery($this).parents("tr").find(".pa-staging-mode-links").removeClass('hidden');
										jQuery($this).parents("tr").find(".pa-build-cache-link").removeClass('hidden');
										jQuery($this).parents("tr").find(".pa-clear-cache-link").removeClass('hidden');
										jQuery($this).addClass('resource-accelerated');
										jQuery($this).parents("#pegasaas-accelerator__page_post_options_sidebar").removeClass('pegasaas-disabled');

									}
								}
								
													
							} else  { 
							//	
								alert(data['message']);
								jQuery($this).addClass("revert");
								jQuery($this).click();
							}
						}, 
						"json");
		
});

jQuery(".accelerated-pages-tooltip").click(function(e) {
		e.stopPropagation();
	if (jQuery(this).hasClass("fa-tooltip-clicked")) {
		jQuery(this).removeClass("fa-tooltip-clicked");
		jQuery("#non-accelerated-chart-container").removeClass("pegasaas-muted-region");
		jQuery("#accelerated-chart-container").removeClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-benchmark-scans-chart").removeClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-pagespeed-scans-chart").removeClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-cpcss-scans-chart").removeClass("pegasaas-muted-region");
		jQuery("#wpbody-content.tooltip-visible").removeClass("tooltip-visible");
		
	} else {
		jQuery(this).addClass("fa-tooltip-clicked");
		jQuery("#wpbody-content").addClass("tooltip-visible");

		jQuery("#non-accelerated-chart-container").addClass("pegasaas-muted-region");
		jQuery("#accelerated-chart-container").addClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-benchmark-scans-chart").addClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-pagespeed-scans-chart").addClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-cpcss-scans-chart").addClass("pegasaas-muted-region");
		
	}
});


jQuery(".un-accelerated-pagespeed-scans-tooltip").click(function(e) {
		e.stopPropagation();
	if (jQuery(this).hasClass("fa-tooltip-clicked")) {
		jQuery(this).removeClass("fa-tooltip-clicked");
		jQuery("#pages-accelerated-chart-container").removeClass("pegasaas-muted-region");
		jQuery("#accelerated-chart-container").removeClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-benchmark-scans-chart").removeClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-pagespeed-scans-chart").removeClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-cpcss-scans-chart").removeClass("pegasaas-muted-region");
		jQuery("#wpbody-content.tooltip-visible").removeClass("tooltip-visible");
		
	} else {
		jQuery(this).addClass("fa-tooltip-clicked");
				jQuery("#wpbody-content").addClass("tooltip-visible");

		jQuery("#pages-accelerated-chart-container").addClass("pegasaas-muted-region");
		jQuery("#accelerated-chart-container").addClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-benchmark-scans-chart").addClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-pagespeed-scans-chart").addClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-cpcss-scans-chart").addClass("pegasaas-muted-region");
		
	}
});

jQuery(".accelerated-pagespeed-scans-tooltip").click(function(e) {
		e.stopPropagation();
	if (jQuery(this).hasClass("fa-tooltip-clicked")) {
		jQuery(this).removeClass("fa-tooltip-clicked");
		jQuery("#pages-accelerated-chart-container").removeClass("pegasaas-muted-region");
		jQuery("#non-accelerated-chart-container").removeClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-benchmark-scans-chart").removeClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-pagespeed-scans-chart").removeClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-cpcss-scans-chart").removeClass("pegasaas-muted-region");
		jQuery("#wpbody-content.tooltip-visible").removeClass("tooltip-visible");
		
	} else {
		jQuery(this).addClass("fa-tooltip-clicked");
				jQuery("#wpbody-content").addClass("tooltip-visible");

		jQuery("#pages-accelerated-chart-container").addClass("pegasaas-muted-region");
		jQuery("#non-accelerated-chart-container").addClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-benchmark-scans-chart").addClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-pagespeed-scans-chart").addClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-cpcss-scans-chart").addClass("pegasaas-muted-region");
		
	}
});

jQuery(".pending-benchmark-scans-chart-tooltip").click(function(e) {
		e.stopPropagation();
if (jQuery(this).hasClass("fa-tooltip-clicked")) {
		jQuery(this).removeClass("fa-tooltip-clicked");
		jQuery("#pages-accelerated-chart-container").removeClass("pegasaas-muted-region");
		jQuery("#non-accelerated-chart-container").removeClass("pegasaas-muted-region");
		jQuery("#accelerated-chart-container").removeClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-pagespeed-scans-chart").removeClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-cpcss-scans-chart").removeClass("pegasaas-muted-region");
		jQuery("#wpbody-content.tooltip-visible").removeClass("tooltip-visible");
		
	} else {
		jQuery(this).addClass("fa-tooltip-clicked");
				jQuery("#wpbody-content").addClass("tooltip-visible");

		jQuery("#pages-accelerated-chart-container").addClass("pegasaas-muted-region");
		jQuery("#non-accelerated-chart-container").addClass("pegasaas-muted-region");
		jQuery("#accelerated-chart-container").addClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-pagespeed-scans-chart").addClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-cpcss-scans-chart").addClass("pegasaas-muted-region");
		
	}
});

jQuery(".pending-pagespeed-scans-chart-tooltip").click(function(e) {
		e.stopPropagation();
if (jQuery(this).hasClass("fa-tooltip-clicked")) {
		jQuery(this).removeClass("fa-tooltip-clicked");
		jQuery("#pages-accelerated-chart-container").removeClass("pegasaas-muted-region");
		jQuery("#non-accelerated-chart-container").removeClass("pegasaas-muted-region");
		jQuery("#accelerated-chart-container").removeClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-benchmark-scans-chart").removeClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-cpcss-scans-chart").removeClass("pegasaas-muted-region");
		jQuery("#wpbody-content.tooltip-visible").removeClass("tooltip-visible");
		
	} else {
		jQuery(this).addClass("fa-tooltip-clicked");
				jQuery("#wpbody-content").addClass("tooltip-visible");

		jQuery("#pages-accelerated-chart-container").addClass("pegasaas-muted-region");
		jQuery("#non-accelerated-chart-container").addClass("pegasaas-muted-region");
		jQuery("#accelerated-chart-container").addClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-benchmark-scans-chart").addClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-cpcss-scans-chart").addClass("pegasaas-muted-region");
		
	}
});

jQuery(".pending-cpcss-scans-chart-tooltip").click(function(e) {
	e.stopPropagation();
if (jQuery(this).hasClass("fa-tooltip-clicked")) {
		jQuery(this).removeClass("fa-tooltip-clicked");
		jQuery("#pages-accelerated-chart-container").removeClass("pegasaas-muted-region");
		jQuery("#non-accelerated-chart-container").removeClass("pegasaas-muted-region");
		jQuery("#accelerated-chart-container").removeClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-pagespeed-scans-chart").removeClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-benchmark-scans-chart").removeClass("pegasaas-muted-region");
		jQuery("#wpbody-content.tooltip-visible").removeClass("tooltip-visible");
		
	} else {
		jQuery(this).addClass("fa-tooltip-clicked");
				jQuery("#wpbody-content").addClass("tooltip-visible");

		jQuery("#pages-accelerated-chart-container").addClass("pegasaas-muted-region");
		jQuery("#non-accelerated-chart-container").addClass("pegasaas-muted-region");
		jQuery("#accelerated-chart-container").addClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-pagespeed-scans-chart").addClass("pegasaas-muted-region");
		jQuery("#pegasaas-pending-benchmark-scans-chart").addClass("pegasaas-muted-region");
		
	}
});
// initialize tooltips
if (jQuery.tooltip) {
	jQuery(".accelerated-pages-tooltip").tooltip({ 
		title: "&nbsp;",
		html: true,
		trigger: 'click', 
		
		container: '#pages-accelerated-chart-container',
		template: '<div class="tooltip" role="tooltip"><div class="tooltip-inner"></div></div>',
		placement: 'bottom'
												  
		
	}
	);


jQuery(".un-accelerated-pagespeed-scans-tooltip").tooltip({ 
		title: "&nbsp;",
		html: true,
		trigger: 'click', 
		
		container: '#non-accelerated-chart-container',
		template: '<div class="tooltip" role="tooltip"><div class="tooltip-inner"></div></div>',
		placement: 'bottom'
												  
		
	}
	);

jQuery(".accelerated-pagespeed-scans-tooltip").tooltip({ 
		title: "&nbsp;",
		html: true,
		trigger: 'click', 
		
		container: '#pegasaas-site-speed-chart',
		template: '<div class="tooltip" role="tooltip"><div class="tooltip-inner"></div></div>',
		placement: 'bottom'
												  
		
	}
	);
jQuery(".pending-benchmark-scans-chart-tooltip").tooltip({ 
		title: "<img src='../wp-content/plugins/pegasaas-accelerator/assets/images/tooltips/benchmark-scans.png'>",
		html: true,
		trigger: 'click', 
		
		container: '#pegasaas-pending-benchmark-scans-chart',
		template: '<div class="tooltip" role="tooltip"><div class="tooltip-inner"></div></div>',
		placement: 'bottom'
												  
		
	}
	);

jQuery(".pending-pagespeed-scans-chart-tooltip").tooltip({ 
		title: "<img src='../wp-content/plugins/pegasaas-accelerator/assets/images/tooltips/pagespeed-scans.png'>",
		html: true,
		trigger: 'click', 
		
		container: '#pegasaas-pending-pagespeed-scans-chart',
		template: '<div class="tooltip" role="tooltip"><div class="tooltip-inner"></div></div>',
		placement: 'bottom'
												  
		
	}
	);

jQuery(".pending-cpcss-scans-chart-tooltip").tooltip({ 
		title: "<img src='../wp-content/plugins/pegasaas-accelerator/assets/images/tooltips/cpcss-scans.png'>",
		html: true,
		trigger: 'click', 
		
		container: '#pegasaas-pending-cpcss-scans-chart',
		template: '<div class="tooltip" role="tooltip"><div class="tooltip-inner"></div></div>',
		placement: 'bottom'
												  
		
	}
	);

 
	jQuery("[data-toggle='tooltip']").tooltip();
	jQuery(".pegasaas-column-has-tooltip").tooltip();
	jQuery("[rel='pegasaas-tooltip']").tooltip();
	jQuery(".pegasaas-tooltip").tooltip();
}

jQuery(".pegasaas-feature-box .feature-box-toggle").click(function() {
		if (jQuery(this).hasClass("fa-angle-down")){
			jQuery(this).addClass("fa-angle-up");
			jQuery(this).removeClass("fa-angle-down");
			jQuery(this).parents(".pegasaas-feature-box:not(.pegasaas-feature-section-container)").addClass("pegasaas-feature-box-expanded");
		} else {
			jQuery(this).addClass("fa-angle-down");
			jQuery(this).removeClass("fa-angle-up");	
			
			jQuery(this).parents(".pegasaas-feature-box:not(.pegasaas-feature-section-container)").removeClass("pegasaas-feature-box-expanded");

		}
		
	});


	jQuery(".pegasaas-feature-box-sidebar .pegasaas-subsystem-title").click(function() {
		
	var toggler = jQuery(this).find(".feature-box-toggle");
	
		if (jQuery(toggler).hasClass("fa-angle-down")){
			jQuery(toggler).addClass("fa-angle-up");
			jQuery(toggler).removeClass("fa-angle-down");
			jQuery(toggler).parents(".pegasaas-feature-box-sidebar").addClass("pegasaas-feature-box-expanded");
		} else {
			jQuery(toggler).addClass("fa-angle-down");
			jQuery(toggler).removeClass("fa-angle-up");	
			
			jQuery(toggler).parents(".pegasaas-feature-box-sidebar").removeClass("pegasaas-feature-box-expanded");

		}
		
	});


	jQuery(document).ready(function () { 
		if (jQuery().loading) {
			jQuery(".pegasaas-score-bar").loading();
		} else {
			console.log("Loading Extension Not Found");
		}
	});
jQuery(document).ready(function() {
	jQuery("#update-nav-menu").bind("submit", function(e) {
		
		if (confirm("Do you wish to clear your site cache now?")) {
			jQuery("#update-nav-menu").append("<input type='hidden' name='_pegasaas_clear_cache' value='1'>");
		} else {
			
			jQuery("#update-nav-menu").append("<input type='hidden' name='_pegasaas_clear_cache' value='0'>");
		}
		//jQuery(this).submit();
		
	});
	
});



/****************************************************************************
 * 
 *  instapage
 * 
 *  */
MutationObserver = window.MutationObserver || window.WebKitMutationObserver;
var pegasaas_instapages_observer = new MutationObserver(function(mutations, observer) {
	if (jQuery("#instapage-container .pegasaas-accelerator-clear-cache-link").length == 0) {
		
		jQuery("#instapage-container .before-delete").each(function() {

			// build the resource id which will be applie to the "rel" attribute
			var href = jQuery(this).parents(".c-table__cell").find(".page-url a").attr("href");
			href = href.replace("http://", "");
			href = href.replace("https://", "");
			var href_parts = href.split("/");
			href_parts.shift();
			var rel = href_parts.join("/");
			rel = "/" + rel;
			rel = rel.trimRight("/");
			rel = rel + "/";
	

			jQuery(this).append("<button rel='" + rel + "' class='pegasaas-accelerator-clear-cache-link'>Clear Cache <span class='pegasaas-accelerator-cache-icon'></span></button>");
		});
		
		jQuery(".pegasaas-accelerator-clear-cache-link").click(
			function(e) {
				e.preventDefault();
				// grab the page/post id that is stored in the 'rel' attribute
				var resource_id = jQuery(this).attr("rel");
				var $this 		= this;
										
				jQuery.post(ajaxurl, 
							{ 'action'     : 'pegasaas_clear_page_cache', 
								'api_key'    : jQuery("#pegasaas--api-key").val(), 
								'resource_id': resource_id }, 
							function(data) {
								if (data['status'] == "1") {
									jQuery($this).find(".pegasaas-accelerator-cache-icon").removeClass('existing');
									jQuery($this).find(".pegasaas-accelerator-cache-icon").removeClass('temp-existing');
									 
								} else  { 
									alert(data['message']);
								}
							}, 
							"json");
		});	
	} 
});

// define what element should be observed by the observer
// and what types of mutations trigger the callback
var instapage_container = document.getElementById('instapage-container');
if (instapage_container) {
	pegasaas_instapages_observer.observe(instapage_container, {
	subtree: true,
	attributes: true
	});
}

