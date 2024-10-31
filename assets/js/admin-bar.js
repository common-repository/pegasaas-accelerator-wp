jQuery(document).ready(function() {
	jQuery("#wp-admin-bar-pegasaas-clear-page-cache a, .pegasaas-mega-menu-current-page-cache button").click(function(e) {
		e.preventDefault();
	
		var resource_id = jQuery(this).find(".status").attr("rel");
		var $this = this;
	
		jQuery(this).find(".status").html("<i class='pegasaas-in-progress'></i>");
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_clear_page_cache', 
				  'api_key': pegasaas_ajax_object.api_key,  
				  'resource_id': resource_id,
				  'resource_id_type': "obj_id"}, 
				function(data) {
					if (data['status'] == "1") {
						jQuery($this).find(".status").html("<i class='pegasaas-progress-success'></i>");
						jQuery($this).parents(".pegasaas-mega-menu-item").find(".pegasaas-mega-menu-stat-number").html("0");
						jQuery($this).parents(".pegasaas-mega-menu-item").find(".pegasaas-mega-menu-stat-date").html("NOT");
						
					} else  { 
					  
						jQuery($this).find(".status").html("<i class='pegasaas-progress-error'></i>");
					}
					setTimeout("clear_pegasaas_progress()", 1500);
				}, 
				"json");																										
	});
	
	jQuery("#toplevel_page_pegasaas-accelerator a[href*='c=purge-page-cache']").click(function(e) {
		e.preventDefault();
	
		
		var $this = this;
	
		jQuery(this).find(".fa").removeClass("fa-trash");
		jQuery(this).find(".fa").addClass("fa-spin");
		jQuery(this).find(".fa").addClass("fa-spinner");
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_all_html_cache', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] == "1") {
						//jQuery($this).addClass("btn-cache-clearing");
						//jQuery($this).html("Clearing Cache <span class='status'><i class='pegasaas-in-progress'></i></span>");
						
						queue_cache_clearing();
					} else  { 
					  	jQuery($this).find(".fa").removeClass("fa-spin");
						jQuery($this).find(".fa").removeClass("fa-spinner");

						jQuery($this).find(".fa").addClass("fa-remove");
						setTimeout("clear_pegasaas_progress()", 1500);
					}
					
					
				}, 
				"json");																										
	});
	
	jQuery("#toplevel_page_pegasaas-accelerator a[href*='c=purge-all-local-js-cache']").click(function(e) {
		e.preventDefault();
		var $this = this;
		jQuery(this).find(".fa").removeClass("fa-trash");
		jQuery(this).find(".fa").addClass("fa-spin");
		jQuery(this).find(".fa").addClass("fa-spinner");
		
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_all_local_js_cache', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {	
					jQuery($this).find(".fa").removeClass("fa-spin");
					jQuery($this).find(".fa").removeClass("fa-spinner");

					if (data['status'] == "1") {
						jQuery($this).find(".fa").addClass("fa-trash");
					} else  { 
						jQuery($this).find(".fa").addClass("fa-remove");
					}
					//setTimeout("clear_pegasaas_progress()", 1500);
				}, 
				"json");																										
	});
	
	jQuery("#toplevel_page_pegasaas-accelerator a[href*='c=purge-all-local-css-cache']").click(function(e) {
		e.preventDefault();
		var $this = this;
		jQuery(this).find(".fa").removeClass("fa-trash");
		jQuery(this).find(".fa").addClass("fa-spin");
		jQuery(this).find(".fa").addClass("fa-spinner");
		
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_all_local_css_cache', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {	
					jQuery($this).find(".fa").removeClass("fa-spin");
					jQuery($this).find(".fa").removeClass("fa-spinner");

					if (data['status'] == "1") {
						jQuery($this).find(".fa").addClass("fa-trash");
					} else  { 
						jQuery($this).find(".fa").addClass("fa-remove");
					}
					//setTimeout("clear_pegasaas_progress()", 1500);
				}, 
				"json");																										
	});	

	jQuery("#toplevel_page_pegasaas-accelerator a[href*='c=purge-all-local-image-cache']").click(function(e) {
		e.preventDefault();
		var $this = this;
		jQuery(this).find(".fa").removeClass("fa-trash");
		jQuery(this).find(".fa").addClass("fa-spin");
		jQuery(this).find(".fa").addClass("fa-spinner");
		
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_all_local_image_cache', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {	
					jQuery($this).find(".fa").removeClass("fa-spin");
					jQuery($this).find(".fa").removeClass("fa-spinner");

					if (data['status'] == "1") {
						jQuery($this).find(".fa").addClass("fa-trash");
					} else  { 
						jQuery($this).find(".fa").addClass("fa-remove");
					}
					//setTimeout("clear_pegasaas_progress()", 1500);
				}, 
				"json");																										
	});		
	
});


jQuery(document).ready(function() {
	jQuery("#wp-admin-bar-pegasaas-rebuild-all-critical-css a").click(function(e) {
		e.preventDefault();
	
	
		var $this = this;

		jQuery(this).find(".status").html("<i class='pegasaas-in-progress'></i>");
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_rebuild_all_critical_css', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] == "1") {
						jQuery($this).find(".status").html("<i class='pegasaas-progress-success'></i>");
					} else  { 
					  	
						jQuery($this).find(".status").html("<i class='pegasaas-progress-error'></i>");
					}
					setTimeout("clear_pegasaas_progress()", 1500);
				}, 
				"json");																										
	});
});

jQuery(document).ready(function() {
	jQuery("#wp-admin-bar-pegasaas-purge-all-html-cache a, .pegasaas-mega-menu-page-cache button.btn-cache, .pegasaas-mega-menu-deferred-js-cache button").click(function(e) {
		e.preventDefault();
		var $this = this;

		jQuery(this).find(".status").html("<i class='pegasaas-in-progress'></i>");
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_all_html_cache', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] == "1") {
						jQuery($this).addClass("btn-cache-clearing");
						jQuery($this).html("Clearing Cache <span class='status'><i class='pegasaas-in-progress'></i></span>");
						//jQuery($this).find(".status").html("<i class='pegasaas-progress-success'></i>");
						
						//jQuery($this).parents(".pegasaas-mega-menu-item").find(".pegasaas-mega-menu-stat-number").html("0");
						queue_cache_clearing();
					} else  { 
					  	
						jQuery($this).find(".status").html("<i class='pegasaas-progress-error'></i>");
						setTimeout("clear_pegasaas_progress()", 1500);
					}
					
					
				}, 
				"json");																										
	});
});

jQuery(document).ready(function() {
	jQuery(".pegasaas-mega-menu-page-cache button.btn-reoptimize").click(function(e) {
		e.preventDefault();
		var $this = this;

		jQuery(this).find(".status").html("<i class='pegasaas-in-progress'></i>");
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_reoptimize_all_html_cache', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] == "1") {
						jQuery($this).addClass("btn-cache-reoptimizing");
						jQuery($this).html("Re-optimize <span class='status'><i class='pegasaas-in-progress'></i></span>");
						//jQuery($this).find(".status").html("<i class='pegasaas-progress-success'></i>");
						
						//jQuery($this).parents(".pegasaas-mega-menu-item").find(".pegasaas-mega-menu-stat-number").html("0");
							queue_cache_reoptimizing();
					} else  { 
					  	
						jQuery($this).find(".status").html("<i class='pegasaas-progress-error'></i>");
						setTimeout("clear_pegasaas_progress()", 1500);
					}
					
					
				}, 
				"json");																										
	});
});



jQuery(document).ready(function() {
	jQuery("#wp-admin-bar-pegasaas-purge-local-image-cache a, .pegasaas-mega-menu-basic-image-cache button.btn-cache").click(function(e) {
		e.preventDefault();
		var $this = this;

		jQuery(this).find(".status").html("<i class='pegasaas-in-progress'></i>");
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_all_local_image_cache', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] == "1") {
						jQuery($this).find(".status").html("<i class='pegasaas-progress-success'></i>");
						jQuery($this).parents(".pegasaas-mega-menu-item").find(".pegasaas-mega-menu-stat-number").html("0");
					} else  { 
					  	
						jQuery($this).find(".status").html("<i class='pegasaas-progress-error'></i>");
					}
					setTimeout("clear_pegasaas_progress()", 1500);
					
				}, 
				"json");																										
	});
});

jQuery(document).ready(function() {
	jQuery("#wp-admin-bar-pegasaas-purge-local-minified-css a, .pegasaas-mega-menu-minified-css-cache button").click(function(e) {
		e.preventDefault();
		var $this = this;

		jQuery(this).find(".status").html("<i class='pegasaas-in-progress'></i>");
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_all_local_css_cache', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] == "1") {
						jQuery($this).find(".status").html("<i class='pegasaas-progress-success'></i>");
						jQuery($this).parents(".pegasaas-mega-menu-item").find(".pegasaas-mega-menu-stat-number").html("0");

					} else  { 
					  	
						jQuery($this).find(".status").html("<i class='pegasaas-progress-error'></i>");
					}
					setTimeout("clear_pegasaas_progress()", 1500);
				}, 
				"json");																										
	});
});


jQuery(document).ready(function() {
	jQuery("#wp-admin-bar-pegasaas-purge-local-minified-js a, .pegasaas-mega-menu-minified-js-cache button").click(function(e) {
		e.preventDefault();
		var $this = this;

		jQuery(this).find(".status").html("<i class='pegasaas-in-progress'></i>");
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_all_local_js_cache', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] == "1") {
						jQuery($this).find(".status").html("<i class='pegasaas-progress-success'></i>");
						jQuery($this).parents(".pegasaas-mega-menu-item").find(".pegasaas-mega-menu-stat-number").html("0");

					} else  { 
					  	
						jQuery($this).find(".status").html("<i class='pegasaas-progress-error'></i>");
					}
					setTimeout("clear_pegasaas_progress()", 1500);
				}, 
				"json");																										
	});
});

jQuery(document).ready(function() {
	jQuery("#wp-admin-bar-pegasaas-purge-all-resource-cache a, .pegasaas-mega-menu-resource-cdn button.btn-purge-cdn-all").click(function(e) {
		e.preventDefault();
		var $this = this;

		jQuery(this).find(".status").html("<i class='pegasaas-in-progress'></i>");
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_all_resource_cache', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] == "1") {
						jQuery($this).find(".status").html("<i class='pegasaas-progress-success'></i>");
					} else  { 
					  	
						jQuery($this).find(".status").html("<i class='pegasaas-progress-error'></i>");
					}
					setTimeout("clear_pegasaas_progress()", 1500);
				}, 
				"json");																										
	});
});



jQuery(document).ready(function() {
	jQuery("#wp-admin-bar-pegasaas-purge-all-cloudflare-cache a, .pegasaas-mega-menu-cloudflare button.btn-purge-cloudflare").click(function(e) {
		e.preventDefault();
		var $this = this;

		jQuery(this).find(".status").html("<i class='pegasaas-in-progress'></i>");
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_all_cloudflare_cache', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] == "1") {
						jQuery($this).find(".status").html("<i class='pegasaas-progress-success'></i>");
					} else  { 
					  	
						jQuery($this).find(".status").html("<i class='pegasaas-progress-error'></i>");
					}
					setTimeout("clear_pegasaas_progress()", 1500);
				}, 
				"json");																										
	});
});

jQuery(document).ready(function() {
	jQuery("#wp-admin-bar-pegasaas-purge-image-resource-cache a, .pegasaas-mega-menu-resource-cdn button.btn-purge-cdn-images").click(function(e) {
		e.preventDefault();
		var $this = this;

		jQuery(this).find(".status").html("<i class='pegasaas-in-progress'></i>");
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_image_resource_cache', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] == "1") {
						jQuery($this).find(".status").html("<i class='pegasaas-progress-success'></i>");
					} else  { 
					  	
						jQuery($this).find(".status").html("<i class='pegasaas-progress-error'></i>");
					}
					setTimeout("clear_pegasaas_progress()", 1500);
				}, 
				"json");																										
	});
});

jQuery(document).ready(function() {
	jQuery("#wp-admin-bar-pegasaas-purge-js-resource-cache a, .pegasaas-mega-menu-resource-cdn button.btn-purge-cdn-js").click(function(e) {
		e.preventDefault();
		var $this = this;

		jQuery(this).find(".status").html("<i class='pegasaas-in-progress'></i>");
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_js_resource_cache', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] == "1") {
						jQuery($this).find(".status").html("<i class='pegasaas-progress-success'></i>");
					} else  { 
					  	
						jQuery($this).find(".status").html("<i class='pegasaas-progress-error'></i>");
					}
					setTimeout("clear_pegasaas_progress()", 1500);
				}, 
				"json");																										
	});
	
	
	
});

jQuery(document).ready(function() {
	jQuery("#wp-admin-bar-pegasaas-purge-css-resource-cache a, .pegasaas-mega-menu-resource-cdn button.btn-purge-cdn-css").click(function(e) {
		e.preventDefault();
		var $this = this;

		jQuery(this).find(".status").html("<i class='pegasaas-in-progress'></i>");
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_css_resource_cache', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] == "1") {
						jQuery($this).find(".status").html("<i class='pegasaas-progress-success'></i>");
					} else  { 
					  	
						jQuery($this).find(".status").html("<i class='pegasaas-progress-error'></i>");
					}
					setTimeout("clear_pegasaas_progress()", 1500);
				}, 
				"json");																										
	});
});






function clear_pegasaas_progress() {
	jQuery("#wp-admin-bar-pegasaas-menu a .status, #wp-admin-bar-pegasaas-menu button .status").html("");
	jQuery("#wp-admin-bar-pegasaas-menu a, #wp-admin-bar-pegasaas-menu button").removeClass("btn-cache-success");
//	jQuery("#wp-admin-bar-pegasaas-menu a, #wp-admin-bar-pegasaas-menu button").html("Cache Cleared <span class='status'><i class='pegasaas-progress-success'></i></span>");

}

function resize_toolbar_mega_menu(){
      var win = jQuery(this); //this = window
	  var accelerator_menu = jQuery("#wp-admin-bar-pegasaas-menu");
	  var menu_offset = accelerator_menu.offset();
	  var buffer = 20;
	
	  if (!menu_offset) {
		console.log("No longer have offset for accelerator menu");
		return;
	  }
      var side_menu_width = jQuery("#adminmenuback").width();
	 // console.log("window width: " + win.width());
	  var preferred_max_width = 40;
	  jQuery(".pegasaas-mega-menu-item").each(function() {
		 var element_width = jQuery(this).attr("data-width");
		  preferred_max_width = preferred_max_width + parseInt(element_width);
		 
	  });
	
	//	console.log("preferred max width: " + preferred_max_width);
	
	  
	  var max_menu_width = win.width() - menu_offset.left - buffer;
	  var max_menu_width = win.width() - side_menu_width - (buffer * 2);
	 	//  console.log(" max menu width: " + max_menu_width);

	  if (max_menu_width > preferred_max_width) {
		  max_menu_width = preferred_max_width;
		  
	  }
	
	//	 	  console.log("new max menu width: " + max_menu_width);

	  var menu_left_position = -(menu_offset.left) + side_menu_width + buffer;
	
	  accelerator_menu.find(".ab-sub-wrapper").css("left", menu_left_position + "px")
	  accelerator_menu.find(".ab-sub-wrapper").css("width", max_menu_width + "px");
	
	  if (max_menu_width < 600) {
		  
	  }
}

jQuery(document).ready(function() {
jQuery(".btn-toggler").click(function(e) {
	e.preventDefault();
	
	var data_target = jQuery(this).attr("data-toggler-target");
	
	if (jQuery(this).hasClass("btn-toggler-active")) {
	   	
	} else {
		jQuery(this).parents(".pegasaas-mega-menu-score-toggler-container").find(".pegasaas-mega-menu-score-toggler-item").addClass("hidden");
		jQuery(this).parents(".pegasaas-mega-menu-score-toggler-container").find(".pegasaas-mega-menu-score-toggler-item[data-toggler=" + data_target + "]").removeClass("hidden");
		jQuery(this).parents(".pegasaas-mega-menu-score-toggler-buttons").find(".btn").removeClass("btn-toggler-active");
		jQuery(this).addClass("btn-toggler-active");
	}

});
});
var 	pegasaas_button_target = "#wp-admin-bar-pegasaas-purge-all-html-cache a, .pegasaas-mega-menu-page-cache button.btn-cache, .pegasaas-mega-menu-deferred-js-cache button";

function queue_cache_clearing() {
	// delay the first run
	console.log("Queuing Cache Clearing");
	pegasaas_button_target = "#wp-admin-bar-pegasaas-purge-all-html-cache a, .pegasaas-mega-menu-page-cache button.btn-cache, .pegasaas-mega-menu-deferred-js-cache button";
	setTimeout('run_background_cache_clearing()', 0);
	
}


function queue_cache_reoptimizing() {
	// delay the first run
	console.log("Queuing Cache Reoptimizing");
	pegasaas_button_target = ".pegasaas-mega-menu-page-cache button.btn-reoptimize";

	setTimeout('run_background_cache_clearing()', 0);
	
}


function run_background_cache_clearing() {
	jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_clear_queued_cache_resources', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] > 0) {
						setTimeout("run_background_cache_clearing()", 0);
						console.log("Have " + data['status'] + " remaining items to clear from cache.");
					} else {
						jQuery(pegasaas_button_target).each(function(e) { 
							
							jQuery(this).removeClass("btn-cache-clearing");
							jQuery(this).addClass("btn-cache-success");
							
							if (!jQuery(this).hasClass("btn-reoptimize")) {
								jQuery(this).html("Delete <span class='status'><i class='pegasaas-progress-success'></i></span>");
								jQuery(this).parents(".pegasaas-mega-menu-item").find(".pegasaas-mega-menu-stat-number").html("0");
							} else {
								jQuery(this).html("Re-Optimize <span class='status'><i class='pegasaas-progress-success'></i></span>");

							}
							
						//jQuery($this).parents(".pegasaas-mega-menu-item").find(".pegasaas-mega-menu-stat-number").html("0");
						});
						jQuery("#toplevel_page_pegasaas-accelerator a[href='admin.php?page=pegasaas-accelerator&c=purge-page-cache']").each(function(e) {
							jQuery(this).find(".fa").removeClass("fa-spin");																												
							jQuery(this).find(".fa").removeClass("fa-spinner");																												
							jQuery(this).find(".fa").addClass("fa-trash");																												
						});
						setTimeout("clear_pegasaas_progress()", 1500);

						console.log("Queued cache items have been cleared");
					}
					
				}, 
				"json");	
	
}


function run_background_cache_reoptimize() {
	jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_clear_queued_reoptimize_resources', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] > 0) {
						setTimeout("run_background_cache_reoptimize()", 0);
						console.log("Have " + data['status'] + " remaining items to optimize");
					} else {
						jQuery(".pegasaas-mega-menu-page-cache button.btn-reoptimize").each(function(e) { 
							jQuery(this).removeClass("btn-cache-reoptimizing");
							jQuery(this).addClass("btn-cache-success");
							jQuery(this).html("Re-Optimize <span class='status'><i class='pegasaas-progress-success'></i></span>");
							//jQuery(this).parents(".pegasaas-mega-menu-item").find(".pegasaas-mega-menu-stat-number").html("0");
							
						//jQuery($this).parents(".pegasaas-mega-menu-item").find(".pegasaas-mega-menu-stat-number").html("0");
						});
					
						setTimeout("clear_pegasaas_progress()", 1500);

						console.log("Queued cache items have been reoptimized");
					}
					
				}, 
				"json");	
	
}


jQuery(window).on('load', resize_toolbar_mega_menu);
jQuery(window).on('resize', resize_toolbar_mega_menu);
