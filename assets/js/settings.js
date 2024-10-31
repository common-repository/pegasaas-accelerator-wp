var pending_form = "";	
jQuery(".feature-switch,.feature-setting-change,.prompt-to-clear-cache").bind("submit", function(e) {
	
	if (jQuery(this).parents(".pegasaas-feature-box").hasClass("locked-for-novice") && jQuery(".pegasaas-dashboard").hasClass("interface-novice")) {
		e.preventDefault();
		return false;
	} else if (jQuery(this).parents(".feature-container").hasClass("locked-for-novice") && jQuery(".pegasaas-dashboard").hasClass("interface-novice")) {
		e.preventDefault();
		return false;
	}
	
	// change the parent option to show ENABLED or DISABLED state
	var parent_id = jQuery(this).parents(".settings-panel").attr("id");
	var current_state = jQuery(this).find("input[name='c']").val();
	var span = jQuery("[data-target='#" + parent_id + "']").find("span.label-state");
	if (current_state == "enable-feature") {
		span.html("ENABLED");
		span.removeClass("label-disabled");
		span.addClass("label-enabled");
		
	} else if (current_state == "disable-feature") {
		span.html("DISABLED");
		span.removeClass("label-enabled");
		span.addClass("label-disabled");
	}

	
	
	if (jQuery(this).find("input[name='prompt']").length > 0) {
		
		if (jQuery(this).find("input[name='prompt']").val() == "clear_cache") {
			e.preventDefault();
			pending_form = jQuery(this);


			jQuery("#confirm-update-clear-cache").modal("show");
			jQuery("#update-setting-and-clear").attr("data-feature", jQuery(this).find("input[name='f']").val());


			jQuery("#update-setting-and-clear").bind("click", function() {
				//alert(jQuery(pending_form).find("input[name='c']").val());
				if (jQuery(pending_form).find("input[name='c']").val() == "change-local-setting" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-feature-status"  || 
					jQuery(pending_form).find("input[name='c']").val() == "change-feature-attribute" || 
					jQuery(pending_form).find("input[name='c']").val() == "toggle-local-setting" ||
				    jQuery(pending_form).find("input[name='c']").val() == "change-local-complex-setting" ||
				    jQuery(pending_form).find("input[name='c']").val() == "toggle-local-complex-setting"  ) {

					
					
				} else {
					if (jQuery(pending_form).find("input[name='c']").val() == "enable-feature") {
						jQuery(pending_form).find("input[name='c']").val("disable-feature");
					} else {
						jQuery(pending_form).find("input[name='c']").val("enable-feature");
					}
				}
				

				jQuery(pending_form).find("input[name='cache']").remove();
				jQuery(this).unbind("click");

				submit_via_ajax(pending_form, "pegasaas_dashboard_settings_update");
				
				//jQuery(pending_form).submit();
				jQuery("#confirm-update-clear-cache").modal("hide");
				if (jQuery(pending_form).find("input[name='c']").val() == "change-local-setting" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-feature-status" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-feature-attribute" || 
					jQuery(pending_form).find("input[name='c']").val() == "toggle-local-complex-setting" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-local-complex-setting" || 
					jQuery(pending_form).find("input[name='c']").val() == "toggle-local-setting") {
					if (jQuery(pending_form).parents(".feature-row").hasClass("feature-disabled")) {
						jQuery(pending_form).parents(".feature-row").removeClass("feature-disabled");
					} else {
						jQuery(pending_form).parents(".feature-row").addClass("feature-disabled");
					}					
				} else {
					if (jQuery(pending_form).find("input[name='c']").val() == "enable-feature") {
						jQuery(pending_form).find("input[name='c']").val("disable-feature");
					} else {
						jQuery(pending_form).find("input[name='c']").val("enable-feature");
					}	
				}
			});

			
			jQuery("#update-setting-but-no-clear").bind("click", function() {
				
				if (jQuery(pending_form).find("input[name='c']").val() == "change-local-setting"|| 
					jQuery(pending_form).find("input[name='c']").val() == "change-feature-status"  ||
					jQuery(pending_form).find("input[name='c']").val() == "change-feature-attribute" || 
					jQuery(pending_form).find("input[name='c']").val() == "toggle-local-setting" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-local-complex-setting"||
					jQuery(pending_form).find("input[name='c']").val() == "change-local-complex-setting"|| 
					jQuery(pending_form).find("input[name='c']").val() == "toggle-local-complex-setting") {
								
				} else {
					if (jQuery(pending_form).find("input[name='c']").val() == "enable-feature") {
						jQuery(pending_form).find("input[name='c']").val("disable-feature");
					} else {
						jQuery(pending_form).find("input[name='c']").val("enable-feature");
					}
				}
				
				
				
				if (jQuery(pending_form).find("input[name='cache']").length == 0) {
					jQuery(pending_form).append("<input type='hidden' name='cache' value='do-not-clear' />");

				}

				submit_via_ajax(pending_form, "pegasaas_dashboard_settings_update");
				//jQuery(pending_form).submit();
				jQuery(this).unbind("click");
				jQuery("#confirm-update-clear-cache").modal("hide");
				
				
				if (jQuery(pending_form).find("input[name='c']").val() == "change-local-setting" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-feature-status" || 
					jQuery(pending_form).find("input[name='c']").val() == "toggle-local-setting" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-feature-attribute" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-local-complex-setting"|| 
					jQuery(pending_form).find("input[name='c']").val() == "toggle-local-complex-setting") {

				} else {
					if (jQuery(pending_form).find("input[name='c']").val() == "enable-feature") {
						jQuery(pending_form).find("input[name='c']").val("disable-feature");
					} else {
						jQuery(pending_form).find("input[name='c']").val("enable-feature");
					}	
				}
				
				
				console.log("after save 2, state is now: " + jQuery(pending_form).find("input[name='c']").val());


			});	
		
		
		} else if (jQuery(this).find("input[name='prompt']").val() == "clear_image_cache") {
			e.preventDefault();
			pending_form = jQuery(this);
			


			jQuery("#confirm-update-clear-image-cache").modal("show");
			jQuery("#update-setting-and-clear-images").attr("data-feature", jQuery(this).find("input[name='f']").val());


			jQuery("#update-setting-and-clear-images").bind("click", function() {
				
				if (jQuery(pending_form).find("input[name='c']").val() == "change-local-setting" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-feature-status"  || 
					jQuery(pending_form).find("input[name='c']").val() == "toggle-local-setting" ||
					jQuery(pending_form).find("input[name='c']").val() == "change-feature-attribute" || 
				    jQuery(pending_form).find("input[name='c']").val() == "change-local-complex-setting" ||
				    jQuery(pending_form).find("input[name='c']").val() == "toggle-local-complex-setting"  ) {

					
				} else {
					if (jQuery(pending_form).find("input[name='c']").val() == "enable-feature") {
						jQuery(pending_form).find("input[name='c']").val("disable-feature");
					} else {
						jQuery(pending_form).find("input[name='c']").val("enable-feature");
					}
				}
				

				
				jQuery(pending_form).find("input[name='image_cache']").remove();
				jQuery(this).unbind("click");

				//jQuery(pending_form).submit();
				submit_via_ajax(pending_form, "pegasaas_dashboard_settings_update");
				jQuery("#confirm-update-clear-image-cache").modal("hide");
				
				if (jQuery(pending_form).find("input[name='c']").val() == "change-local-setting" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-feature-status" || 
					jQuery(pending_form).find("input[name='c']").val() == "toggle-local-complex-setting" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-local-complex-setting" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-feature-attribute" || 
					jQuery(pending_form).find("input[name='c']").val() == "toggle-local-setting") {
					
					if (jQuery(pending_form).parents(".feature-row").hasClass("feature-disabled")) {
						jQuery(pending_form).parents(".feature-row").removeClass("feature-disabled");
					} else {
						jQuery(pending_form).parents(".feature-row").addClass("feature-disabled");
					}	
					
				} else {
					if (jQuery(pending_form).find("input[name='c']").val() == "enable-feature") {
						jQuery(pending_form).find("input[name='c']").val("disable-feature");
					} else {
						jQuery(pending_form).find("input[name='c']").val("enable-feature");
					}	
				}
			});

			
			jQuery("#update-setting-but-no-clear-images").bind("click", function() {
				if (jQuery(pending_form).find("input[name='c']").val() == "change-local-setting"|| 
					jQuery(pending_form).find("input[name='c']").val() == "change-feature-status"  ||
					jQuery(pending_form).find("input[name='c']").val() == "change-feature-attribute" || 
					jQuery(pending_form).find("input[name='c']").val() == "toggle-local-setting" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-local-complex-setting"||
					jQuery(pending_form).find("input[name='c']").val() == "toggle-local-complex-setting") {
									
				} else {
					if (jQuery(pending_form).find("input[name='c']").val() == "enable-feature") {
						jQuery(pending_form).find("input[name='c']").val("disable-feature");
					} else {
						jQuery(pending_form).find("input[name='c']").val("enable-feature");
					}
				}
				
				
				if (jQuery(pending_form).find("input[name='image_cache']").length == 0) {
					jQuery(pending_form).append("<input type='hidden' name='image_cache' value='do-not-clear' />");

				}
				jQuery(this).unbind("click");
				//jQuery(pending_form).submit();
				submit_via_ajax(pending_form, "pegasaas_dashboard_settings_update");
		jQuery("#confirm-update-clear-image-cache").modal("hide");
				if (jQuery(pending_form).find("input[name='c']").val() == "change-local-setting" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-feature-status" || 
					jQuery(pending_form).find("input[name='c']").val() == "toggle-local-setting" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-feature-attribute" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-local-complex-setting"|| 
					jQuery(pending_form).find("input[name='c']").val() == "toggle-local-complex-setting") {

						
				} else {
					if (jQuery(pending_form).find("input[name='c']").val() == "enable-feature") {
						jQuery(pending_form).find("input[name='c']").val("disable-feature");
					} else {
						jQuery(pending_form).find("input[name='c']").val("enable-feature");
					}	
				}


			}	);	
		
		} else if (jQuery(this).find("input[name='prompt']").val() == "no-prompt-image-cache") {
			
			jQuery(this).find("input[name='prompt']").val("clear_image_cache");
			return true;
		} else {
			
			jQuery(this).find("input[name='prompt']").val("clear_cache");
			return true;
		}

	} else {
		e.preventDefault();
		// there is no prompt, so just submit the form
		
		jQuery(this).find("input[name='prompt']").val("clear_cache");
	
		submit_via_ajax(this, "pegasaas_dashboard_settings_update");
		
		
		return true;
	}
});


function submit_form(element) {
	jQuery(element).parents('form').attr("target", "hidden-frame")
	jQuery(element).parents('form').submit();
}
	
jQuery(".table-complex-settings select").bind("change", function () {
	jQuery(this).parents("form.feature-toggle-switch").find("input[name=c]").val("change-local-complex-setting");
	jQuery(this).parents("form.feature-toggle-switch").submit();
});
jQuery(".table-complex-settings form.feature-toggle-switch .js-switch").bind("change", function(e) { 
	
		jQuery(this).parents("form.feature-toggle-switch").find("input[name=c]").val("toggle-local-complex-setting");

});

function submit_via_ajax(the_form, action) {
	var the_args = get_form_data(jQuery(the_form));
		jQuery(the_form).find("button").attr("disabled", true);
	
		var save_button = jQuery(the_form).find(".btn-save");
		if (save_button) {
			save_button.attr("disabled", true);
			save_button.find("span.status").html("<i class='svg-icons svg-icon-14 svg-tail-spin-white'></i>");
		}
		
		jQuery.post({url: ajaxurl, 
				 type: "POST",
				 dataType: "json",
				 data: { 'action': action, 'api_key': jQuery("#pegasaas-api-key").val(), 'args': the_args},
				 
				 success: function(data) {
					 if (save_button) {
						save_button.removeAttr("disabled");
						save_button.find("span.status").html("");
					}
					
					 if (the_args['c'] == 'change-feature-attribute' && the_args['f'] == 'coverage' && data['cache_clearing_queued']) {
						
						 run_background_cache_clearing();
					 }
					//if (the_args['c'] == "remove-item-complex-setting" && the_args['f'] == 'multi_server_ip') {
					//	jQuery($this).parents("li").remove();
					//}
					 
				},
				 error: function(jqXHR, textStatus, errorThrown) {
				  alert("Error, status = " + textStatus + ", " +
              "error thrown: " + errorThrown);
			}
				 });	
	
}
	
	jQuery(".background-submit").submit(function(event) {
		event.preventDefault();
	
		
		var $this = this;
		var the_args = get_form_data(jQuery(this));
		jQuery(this).find("button").attr("disabled", true);
		
		jQuery.post({url: ajaxurl, 
				 type: "POST",
				 dataType: "json",
				 data: { 'action': 'pegasaas_dashboard_settings_update', 'api_key': jQuery("#pegasaas-api-key").val(), 'args': the_args},
				 
				 success: function(data) {
					if (the_args['c'] == "remove-item-complex-setting" && the_args['f'] == 'multi_server_ip') {
						jQuery($this).parents("li").remove();
					}
					 
				},
				 error: function(jqXHR, textStatus, errorThrown) {
				  alert("Error, status = " + textStatus + ", " +
              "error thrown: " + errorThrown
        );
				 }
				 });
		
	});
	
	function get_form_data(form){
		var unindexed_array = form.serializeArray();
		var indexed_array = {};

		jQuery.map(unindexed_array, function(n, i){
			indexed_array[n['name']] = n['value'];
		});

		return indexed_array;
	}	

// side menu click action
jQuery(".quicklinks-column ul a").bind("click", function(e) {
	e.preventDefault();
	
	var target_id = jQuery(this).attr("href");
	
	jQuery(".settings-panel").css("display", "none");
	jQuery(".pegasaas-feature-section-container").fadeIn();
	
	jQuery('body,html').animate({
		scrollTop: jQuery(target_id).offset().top - 50
		}, 250
	);
});


// section feature click action						
jQuery(".settings-container a.list-group-item").click(function(e) {
	e.preventDefault();
	var data_target = jQuery(this).attr("data-target");
	jQuery("#pegasaas-accelerator-main-settings .pegasaas-feature-section-container").css("display", "none");
	
	//jQuery('body').scrollspy({ target: '#quicklinks', offset: 60 });
	//
	
	jQuery(data_target).fadeIn();
	

	jQuery("#quicklinks").addClass("in-feature");
});




// back to main section click action
jQuery(".back-to-main-settings").click(function(e) {
	e.preventDefault();
	var data_target = jQuery(this).parents(".settings-panel").attr("id");
	var target = jQuery("[data-target='#" + data_target + "']").parents(".pegasaas-feature-section-container");
	jQuery(".settings-panel").css("display", "none");
	
	jQuery("#pegasaas-accelerator-main-settings .pegasaas-feature-section-container").fadeIn();
	jQuery('[data-spy="scroll"]').each(function () {
  	var $spy = jQuery(this).scrollspy('refresh')
});
	
	jQuery('body,html').animate({ scrollTop: jQuery(target).offset().top - 50 }, 1000);
	//jQuery('body').scrollspy({ target: '#quicklinks', offset: 60 });
	jQuery("#quicklinks").removeClass("in-feature");
	
	
});


// back to main section click action
jQuery(".list-group-item a[data-toggle='collapse']").click(function(e) {
	
	var parent = jQuery(this).parents(".list-group-item");
	
	if (parent.hasClass("open")) {
		parent.removeClass("open");
		jQuery(this).find(".fa").removeClass("fa-angle-up").addClass("fa-angle-down");
	} else {
		parent.addClass("open");
				jQuery(this).find(".fa").removeClass("fa-angle-down").addClass("fa-angle-up");

	}


});

jQuery("#settings-nav-button").on('shown.bs.tab', function(e) {
	 
	jQuery('body').scrollspy({ target: '#quicklinks', offset: 60 });

});