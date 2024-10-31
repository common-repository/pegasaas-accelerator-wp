/* GLOBAL HELPER FUNCTIONS */
jQuery(".pegasaas-status-switch").each(function() { new Switchery(this,  {size: 'small'}); });
jQuery(".feature-switch .js-switch").each(function() { var sw = new Switchery(this, {size: 'small'} ); 
												   jQuery(this).data("sw", sw);
												   });
jQuery(".feature-toggle-switch .js-switch").each(function() { var sw = new Switchery(this, {size: 'small'} ); 
														  jQuery(this).data("sw", sw);
														  });


function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+ d.toUTCString();
	document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function check_for_control_commands(e) {
	
            var evtobj = window.event? event : e

            if (evtobj.keyCode == 192 && evtobj.ctrlKey) {
				jQuery("#advanced-tab").css("display", "");
				console.log("advanced-tab");
			}
}

document.onkeyup = check_for_control_commands;	
	
jQuery("#wpbody-content").on("click", function(e) {
	if (jQuery(this).hasClass("tooltip-visible")) {

		jQuery(".fa-tooltip-clicked").click();

	}
});


function dismiss_upgrade_box() {
	jQuery("#pegasaas-upgrade-box").css("display", "none");
	jQuery.post(ajaxurl,
				{ 'action': 'pegasaas_dismiss_upgrade_box', 'api_key': jQuery("#pegasaas-api-key").val() },
				function(data) {

				}, "json");
}



/* PAGE SCORES TAB jQuery */
jQuery("#results_per_page").bind("change", function(e) {
	var results_per_page = this.options[e.target.selectedIndex].value;
	var current_results_page = jQuery("#current_results_page").val();
	setCookie("pegasaas_results_per_page", results_per_page, 365);
	setCookie("pegasaas_current_results_page", current_results_page, 365);
	pegasaas_fetch_data();
	//document.location.href = document.location.href;
});

jQuery("#show_post_type").bind("change", function(e) {
	var results_post_type = this.options[e.target.selectedIndex].value;
	var current_results_page = jQuery("#current_results_page").val();
	setCookie("pegasaas_results_post_type", results_post_type, 365);
	setCookie("pegasaas_current_results_page", current_results_page, 365);
	pegasaas_fetch_data();
	//document.location.href = document.location.href;
});	


jQuery("#filter_results_issues").bind("change", function(e) {
	var results_filter = this.options[e.target.selectedIndex].value;
	var current_results_page = jQuery("#current_results_page").val();
	
	setCookie("pegasaas_results_issue_filter", results_filter, 365);
	setCookie("pegasaas_current_results_page", current_results_page, 365);
	pegasaas_fetch_data();
	//document.location.href = document.location.href; 
});	

jQuery("#filter_results_search").bind("click", function(e) {
	
	var results_filter  = jQuery("#results_search_filter").val();
	
	
	setCookie("pegasaas_results_search_filter", results_filter, 365);
	pegasaas_fetch_data();
	//document.location.href = document.location.href; 
});	

jQuery("#results_search_filter").bind("keypress", function(e) {
	if (e.which == 13) {
		e.preventDefault;
		
	
		var results_filter  = jQuery("#results_search_filter").val();


		setCookie("pegasaas_results_search_filter", results_filter, 365);
		pegasaas_fetch_data();
	}
	
	
	//document.location.href = document.location.href; 
});	

jQuery("#results_page_fast_backwards, #results_page_backward, #results_page_forward, #results_page_fast_forwards").bind("click", function(e) {
	var new_page = jQuery(this).val();
	setCookie("pegasaas_current_results_page", new_page, 365);
	console.log("setting current results page to: " + new_page);
	pegasaas_fetch_data();
	//document.location.href = document.location.href;
});	





/* SETTINGS TAB jQuery */
if (jQuery(".pegasaas-dashboard").hasClass("interface-novice")) {
	jQuery(".pegasaas-feature-box.locked-for-novice .js-switch").each(function() {
		jQuery(this).data("sw").disable();
	});

	jQuery(".feature-container.locked-for-novice .js-switch").each(function() {
		jQuery(this).data("sw").disable();
	});
}
	
jQuery(".pegasaas-feature-box.premium-feature .js-switch").each(function() {
	jQuery(this).data("sw").disable();
});
jQuery(".feature-container.premium-feature .js-switch").each(function() {
	jQuery(this).data("sw").disable();
});	
	
jQuery(".pegasaas-feature-box.locked-for-novice .switchery").click(function() {
	if (jQuery(".pegasaas-dashboard").hasClass("interface-novice")) {
		jQuery("#novice-mode-restriction").modal('show');
	}
	
});

jQuery(".feature-container.locked-for-novice .switchery").click(function() {
	if (jQuery(".pegasaas-dashboard").hasClass("interface-novice")) {
		jQuery("#novice-mode-restriction").modal('show');
	}
	
});

jQuery(".pegasaas-feature-box.premium-feature .switchery").click(function() {	
	jQuery("#upgrade-for-premium-feature").modal('show');
});
jQuery(".feature-container.premium-feature .switchery").click(function() {	
	jQuery("#upgrade-for-premium-feature").modal('show');
});


function resize_subsystem_feature_description() {

	jQuery(".pegasaas-accelerator-subsystem-feature-description").height("");
	jQuery(".pegasaas-feature-section-container").each(function() {
		var feature_description_size = 0; 

		jQuery(this).find(".pegasaas-accelerator-subsystem-feature-description").each(function() {

		  if (jQuery(this).height() > feature_description_size) {
			  feature_description_size = jQuery(this).height();
		  }	
		});

		if (feature_description_size > 0) {
			jQuery(this).find(".pegasaas-accelerator-subsystem-feature-description").css("height", (feature_description_size) + "px");
		}
		jQuery(this).find(".pegasaas-accelerator-subsystem-feature-description").removeClass("not-resized");
	});
}





/* MODE SWITCHER */
jQuery(".system-mode-switcher input[type='radio']").click(function(){
	var radio_value = jQuery("input[name='system_mode']:checked").val();
	var arguments = { 'action': 'pegasaas_change_system_mode', 
			 'api_key': jQuery("#pegasaas-api-key").val(), 
			 'mode': radio_value,
			 };
	
	if (radio_value == "development") {
		arguments['duration'] = jQuery("#development-mode-time").val();
	}
	
	jQuery.post(ajaxurl, arguments,
			function(data) {
				jQuery(".pegasaas-dashboard").removeClass("staging-mode");
				jQuery(".pegasaas-dashboard").removeClass("diagnostic-mode");

				if (radio_value == 'live') {
					jQuery("#confirm-live-mode").modal("show");
				} else if (radio_value == 'development') { 
					jQuery(".pegasaas-dashboard").addClass("staging-mode");
					jQuery("#confirm-development-mode").modal("show");
				} else if (radio_value == 'diagnostic') {
					jQuery(".pegasaas-dashboard").addClass("diagnostic-mode");
					jQuery("#confirm-diagnostic-mode").modal("show");
				}

			}, "json");

	// live = 1
	// diagnostic = 2 // this will disable caching and any on-the-fly optimizations, essentially disabling the plugin, without actually setting the status to 0
	// development == live but with a development_mode time limit
});

jQuery("#development-mode-time").bind("change", function() {
	var duration = jQuery(this).val();
		
	jQuery.post(ajaxurl,
				{ 'action': 'pegasaas_change_system_mode', 'api_key': jQuery("#pegasaas-api-key").val(), 'mode': 'development', 'duration': duration},
				function(data) {
				}, "json");
});



/* CHANGELOG TAB jQuery */
function pa_add_lazyload_youtube() {
		
		var containers = document.getElementsByClassName("pa-yt-player");
		
        for (var index = 0; index < containers.length; index++) {
            var temporary_div = document.createElement("div");
            temporary_div.setAttribute("data-id", containers[index].dataset.id);
            temporary_div.innerHTML = pegasaas_youtube_thumbnail(containers[index].dataset.id);
            temporary_div.onclick   = pegasaas_youtube_iframe;
            containers[index].appendChild(temporary_div);
         }
    }
	
 
    function pegasaas_youtube_thumbnail(id) {
		var youtube_thumbnail = "<img  src=\"https://i.ytimg.com/vi/ID/hqdefault.jpg\">";
		
		var youtube_play_icon = "<div class=\"pegasaas-yt-play\"></div>";
        return youtube_thumbnail.replace("ID", id) + youtube_play_icon;
    }
 
    function pegasaas_youtube_iframe() {
        var youtube_iframe    = document.createElement("iframe");
        var youtube_embed_url = "https://www.youtube.com/embed/ID?autoplay=1";
        youtube_iframe.setAttribute("src", youtube_embed_url.replace("ID", this.dataset.id));
        youtube_iframe.setAttribute("frameborder", "0");
        youtube_iframe.setAttribute("allowfullscreen", "1");
        youtube_iframe.setAttribute("allowfullscreen", "1");
        this.parentNode.replaceChild(youtube_iframe, this);
		
    }

 	pa_add_lazyload_youtube();	

