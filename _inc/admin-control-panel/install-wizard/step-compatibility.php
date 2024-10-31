<div class="row setup-content" id="step-compatibility" style="display: none;">
		      <div class="col-md-10 col-md-offset-1">
        <div class="col-md-12">
	  <h3>Compatibility Check</h3>

			<div id="compatibility-checker-progress">
			<div class="panel-group accordion" id="compatibility-accordion" role="tablist" aria-multiselectable="true">
			   <?php include "compatibility/system.php"; ?>
			   <?php include "compatibility/plugins.php"; ?> 
			   <?php include "compatibility/api.php"; ?> 
				
				
				
				</div>
				
		
		
				
	
			</div>
				
				
			</div>
			
		</div>
			<div class='col-xs-12 btn-row'>
				 
				  <button id="start-button" class="btn btn-primary nextBtn pull-right hiddeen" type="button" >Continue <i class='fa fa-angle-right'></i></button>
				  <button class="btn btn-default backBtn pull-left" type="button" ><i class='fa fa-angle-left'></i> Back</button>
			</div>

<script>
	
function toggle_info(link) {
		 
		  var div = jQuery(link).parents("li").find("div");
		  if (jQuery(div).hasClass("more")) {
			  jQuery(div).removeClass("more");
		  } else {
			jQuery(div).addClass("more");
		  }
	  }
		
	
	
	  function evaluate_compatibility(section) {
		  var critical_icons = jQuery("#" + section + "-compatibility .panel-body .fa-remove");
		  var warning_icons = jQuery("#" + section + "-compatibility .panel-body .fa-warning");
		  var passed_icons = jQuery("#" + section + "-compatibility .panel-body .fa-check");
		  var in_progress_icons = jQuery("#" + section + "-compatibility .panel-body .fa-spinner");
		  
		  if (in_progress_icons.length > 0) {
			  jQuery("#" + section + "-compatibility .status-icon").html("<i class='fa fa-spin fa-spinner'></i>");

			  
		  } else if (critical_icons.length > 0) {
			  jQuery("#" + section + "-compatibility .status-icon").html("<i class='fa fa-remove'></i>");

		  } else if (warning_icons.length > 0) {
			jQuery("#" + section + "-compatibility .status-icon").html("<i class='fa fa-warning'></i>");
  
		  } else  {
			  jQuery("#" + section + "-compatibility .status-icon").html("<i class='fa fa-check'></i>");
		  }
		  
		  
		  var global_icon_passed = jQuery(".status-icon .fa-check");
		  
		  if (global_icon_passed.length < 3) {
			  jQuery("#start-button").html("Continue Anyway <i class='fa fa-angle-right'></i>");
		  } else {
			  jQuery("#start-button").html("Continue <i class='fa fa-angle-right'></i>");
		  }
	  }
	
	
	  function check_compatibility() {
		  //console.log("checking compatibility");
		  //jQuery("#checking-compatibility").html("");
		 // jQuery("#checking-compatibility").addClass("checking");
		 // jQuery("#check-button").html("Checking Compatibility <i class='svg-icons svg-icon-14 svg-puff'></i>");
		 // jQuery("#check-button").attr("disabled", "disabled");
		  
		  jQuery("#api-compatibility .fa").removeClass("fa-warning");
		  jQuery("#api-compatibility .fa").removeClass("fa-check");
		  jQuery("#api-compatibility .fa").removeClass("fa-remove");
		  jQuery("#api-compatibility .fa").removeClass("fa-times-circle");
		  jQuery("#api-compatibility .fa").addClass("fa-spin");
		  jQuery("#api-compatibility .fa").addClass("fa-spinner");
		  console.log("checking compatibility");
		  console.log(jQuery("#api-compatiblity .fa").length);


		  
		/* SYSTEM */
		jQuery.post(ajaxurl,
				   { 'action': 'pegasaas_check_server_response_time' },
					function(data) {
						
						if (data['execution_time'] > 3000) {
							jQuery("#server-response-time").parent("li").html("<i id='server-response-time' class='fa fa-warning fa-red'></i> Extremely Slow Server Response Time (" + data['execution_time'] + " ms) <a href='#' class='btn btn-xs btn-info more-link'>help</a> <div> " + data['advice'] + "</div>");
	
						} else
						if (data['execution_time'] > 2000) {
							jQuery("#server-response-time").parent("li").html("<i id='server-response-time' class='fa fa-warning '></i> Slow Server Response Time (" + data['execution_time'] + " ms) <a href='#' class='btn btn-xs btn-info more-link'>help</a> <div> " + data['advice'] + "</div>");
	
						} else if (data['execution_time'] > 1000) {
							jQuery("#server-response-time").parent("li").html("<i id='server-response-time' class='fa fa-check'></i> OK Server Response Time (" + data['execution_time'] + " ms)");
	
						} else if (data['execution_time'] >= 0) {
							jQuery("#server-response-time").parent("li").html("<i id='server-response-time' class='fa fa-check'></i> Fast Server Response Time (" + data['execution_time'] + " ms)");
						} else {
								jQuery("#server-response-time").parent("li").html("<i id='server-response-time' class='fa fa-warning fa-red'></i> Server Response Time Unknown - Internal Error  <button class='btn btn-xs btn-default' onclick='check_compatibility()'>try again</button>");
						
						}
			
				   jQuery("#server-response-time").parent("li").find(".more-link").on("click", function(e) {
				
			e.preventDefault();
			toggle_info(this);
		});
			
			evaluate_compatibility("system");
		}, "json");
		  
		  
		/* API */
		  
		  jQuery.post(ajaxurl,
				   { 'action': 'pegasaas_check_api_reachable' },
					function(data) {
					
						
						if (!data['reachable']) {
							jQuery("#api-reachable").parent("li").html("<i id='api-reachable' class='fa fa-remove'></i> API Unreachable <a href='#' class='btn btn-xs btn-info more-link'>help</a> <div>"+data['advice']+"</div>");

							jQuery("#api-response-time").parent("li").html("<i id='api-response-time' class='fa fa-remove'></i> API Response Time (Timeout)");

							jQuery("#api-test-optimization").parent("li").html("<i id='api-test-optimization' class='fa fa-remove'></i> Test Optimization - Not Performed");
							jQuery("#api-test-fetch").parent("li").html("<i id='api-test-fetch' class='fa fa-remove'></i> Test Fetch Method - Not Performed");
							jQuery("#api-test-optimization-fetch").parent("li").html("<i id='api-test-optimization-fetch' class='fa fa-remove'></i> Test Optimization Data Fetch - Not Performed");
							jQuery("#api-test-webperf-fetch").parent("li").html("<i id='api-test-webperf-fetch' class='fa fa-remove'></i> Test Web Performance Data Fetch  - Not Performed");

							
							
						} else if (data['response_time'] > 2000) {
							jQuery("#api-reachable").parent("li").html("<i id='api-reachable' class='fa fa-check'></i> API Reachable");

							jQuery("#api-response-time").parent("li").html("<i id='api-response-time' class='fa fa-warning'></i> API Response Time (" + data['response_time'] + " ms)");
								
							check_api_step_2();
						} else if (data['response_time'] >= 0) {
							jQuery("#api-reachable").parent("li").html("<i id='api-reachable' class='fa fa-check'></i> API Reachable");

							jQuery("#api-response-time").parent("li").html("<i id='api-response-time' class='fa fa-check'></i> API Response Time (" + data['response_time'] + " ms)");
						console.log("check api step 2a");
						    check_api_step_2();
							
						} else {
							jQuery("#api-reachable").parent("li").html("<i id='api-reachable' class='fa fa-remove'></i> API Unreachable - Internal Error <button class='btn btn-xs btn-default' onclick='check_compatibility()'>try again</button>");

							jQuery("#api-response-time").parent("li").html("<i id='api-response-time' class='fa fa-remove'></i> API Response Time (Timeout)");

							jQuery("#api-test-optimization").parent("li").html("<i id='api-test-optimization' class='fa fa-remove'></i> Test Optimization - Not Performed");
							jQuery("#api-test-fetch").parent("li").html("<i id='api-test-fetch' class='fa fa-remove'></i> Test Fetch Method - Not Performed");
							jQuery("#api-test-optimization-fetch").parent("li").html("<i id='api-test-optimization-fetch' class='fa fa-remove'></i> Test Optimization Data Fetch - Not Performed");
							jQuery("#api-test-webperf-fetch").parent("li").html("<i id='api-test-webperf-fetch' class='fa fa-remove'></i> Test Web Performance Data Fetch  - Not Performed");
							
						}
			  
			   jQuery("#api-reachable").parent("li").find(".more-link").on("click", function(e) {
				
			e.preventDefault();
			toggle_info(this);
		});
			evaluate_compatibility("api");
			  
			  
		}, "json");		  
		 
	  }	
	
	function check_api_step_2() {
		
			  jQuery.post(ajaxurl,
				   { 'action': 'pegasaas_check_test_optimization' },
					function(data) {
					
						
						if (data['status'] == 0) {

							jQuery("#api-test-optimization").parent("li").html("<i id='api-test-optimization' class='fa fa-remove'></i> Test Optimization - Failed <a href='#' class='btn btn-xs btn-info more-link'>help</a> <div> " + data['advice'] + "</div>");
				
							
							
						} else if (data['status'] == -1) {
									jQuery("#api-test-optimization").parent("li").html("<i id='api-test-optimization' class='fa fa-remove'></i> Test Optimization - Blocked <a href='#' class='btn btn-xs btn-info more-link'>help</a> <div> " + data['advice'] + "</div>");
							
						
						} else if (data['status'] == -2) {
							jQuery("#api-test-optimization").parent("li").html("<i id='api-test-optimization' class='fa fa-remove'></i> Test Optimization - Timeout <a href='#' class='btn btn-xs btn-info more-link'>help</a> <div> " + data['advice'] + "</div>");
						
						    
						} else if (data['status'] == 1) {
							
								jQuery("#api-test-optimization").parent("li").html("<i id='api-test-optimization' class='fa fa-check'></i> Test Optimization Passed");
						
							
						} else {
							jQuery("#api-test-optimization").parent("li").html("<i id='api-test-optimization' class='fa fa-remove'></i> Internal Error <button class='btn btn-xs btn-default' onclick='check_compatibility()'>try again</button>");
						}
				  
				  
				   jQuery("#api-test-optimization").parent("li").find(".more-link").on("click", function(e) {
			//	 console.log("click");
			e.preventDefault();
			toggle_info(this);
		});
				  
			evaluate_compatibility("api");
			  
			  
		}, "json");
		
		jQuery.post(ajaxurl,
				   { 'action': 'pegasaas_check_push_fetch_test' }, 
					function(data) {
					
						
						if (data['status'] == 0) {

							jQuery("#api-test-fetch").parent("li").html(data['title'] + " <a href='#' class='btn btn-xs btn-info more-link'>help</a> <div>" + data['advice'] + "</div>");
				
							
							
						} else if (data['status'] == -1) {
									jQuery("#api-test-fetch").parent("li").html(data['title']+ " <a href='#' class='btn btn-xs btn-info more-link'>help</a> <div>" + data['advice'] + "</div>");
							
							
						
						
						    
						} else if (data['status'] == 1) {
							
								jQuery("#api-test-fetch").parent("li").html(data['title']);
						
							
						} else {
							jQuery("#api-test-fetch").parent("li").html("<i id='api-test-fetch' class='fa fa-remove'></i> Internal Error <button class='btn btn-xs btn-default' onclick='check_compatibility()'>try again</button>");
						}
			
				   jQuery("#api-test-fetch").parent("li").find(".more-link").on("click", function(e) {
				
			e.preventDefault();
			toggle_info(this);
		});
			evaluate_compatibility("api");
			  
			  
		}, "json");	
		
		
				
		
		jQuery.post(ajaxurl,
				   { 'action': 'pegasaas_check_webperf_data_fetch_test' },  
					function(data) {
					
						
						if (data['status'] == 0) {

							jQuery("#api-test-webperf-fetch").parent("li").html(data['title'] + " <a href='#' class='btn btn-xs btn-info more-link'>help</a> <div>" + data['advice'] + "</div>");
				
							
							
						} else if (data['status'] == -1) {
									jQuery("#api-test-webperf-fetch").parent("li").html(data['title'] + " <a href='#' class='btn btn-xs btn-info more-link'>help</a> <div>" + data['advice'] + "</div>");
							
							
						
						
						    
						} else if (data['status'] == 1) {
							
								jQuery("#api-test-webperf-fetch").parent("li").html(data['title']);
						
							
						} else {
							jQuery("#api-test-webperf-fetch").parent("li").html("<i id='api-test-webperf-fetch' class='fa fa-remove'></i> Internal Error <button class='btn btn-xs btn-default' onclick='check_compatibility()'>try again</button>");
						}
			
			   jQuery("#api-test-webperf-fetch").parent("li").find(".more-link").on("click", function(e) {
				 console.log("click");
			e.preventDefault();
			toggle_info(this);
		});
			
			evaluate_compatibility("api");
			  
			
			
		}, "json");			
	}
	
	jQuery("#check-button").click(function() { check_compatibility() });
	
	check_compatibility();
	console.log("hmm");
	
	function compatibility_fade_out() {
		jQuery("#checking-compatibility").fadeOut(1000,function() {
			jQuery(".compatibility-good").fadeIn();
		});
	}
	
</script>
</div>