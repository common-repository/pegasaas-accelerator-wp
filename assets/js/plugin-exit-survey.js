jQuery(document).ready(function() {
	var modal_title 		= "Before you go, please let us know why you are deactivating";
	var modal_disclaimer 	= "Thank for your feedback.  Every piece of information submitted helps us to build a better plugin.";
	
	var reasons = ["This is a temporary deactivation while I debug an issue",
				   "I found a better plugin",
				   "The plugin did not work as expected",
				   "I no longer need the plugin",
				   "Other"];
	

 	var modal_html = "<div id='pegasaas-exit-survey-modal' class='pegasaas-modal'>" +
		"<div class='pegasaas-modal-dialog'>" +
		  "<div class='pegasaas-modal-header'>" +
		  "<h4>[modal_title]</h4>" +
		  "</div>" +
		  "<div class='pegasaas-modal-body'>" +
		    "<ul>" +
		      "<li class='reason'>" +
		 		"<input id='reason-1' type='radio' name='reason' value='This is a temporary deactivation while I debug an issue'>" +
		        "<label for='reason-1'>This is a temporary deactivation while I debug an issue</label>" +
				"<div class='secondary-questions'>" + 
		           "<p>Do you wish to retain your settings and data?</p>" +
				   "<ul>" +
						"<li><input id='retain-data-1' type='radio' name='reason-1' value='1' checked> <label for='retain-data-1'>Yes</label></li>" +
						"<li><input id='retain-data-0' type='radio' name='reason-1' value='0'> <label for='retain-data-0'>No</label></li>" + 
					"</ul>" +
		        "</div>" +
		      "</li>" + 
		      "<li class='reason'>" +
		 		"<input id='reason-2' type='radio' name='reason' value='I found a better plugin'>" +
		        "<label for='reason-2'>I found a better plugin</label>" +
				"<div class='secondary-questions'>" + 
		           "<input type='text' name='reason-2' placeholder='Please share the plugin name'>" +
		        "</div>" +
			  "</li>" + 
		      "<li class='reason'>" +
		 		"<input id='reason-3' type='radio' name='reason' value='The plugin did not work as expected'>" +
		        "<label for='reason-3'>The plugin did not work as expected</label>" +
				"<div class='secondary-questions'>" + 
				"<p>Please accept our apologies!  If you experienced a problem during installation, please <a href='https://pegasaas.com/support/' target='_blank'>contact us</a> so that our support team can resolve the issue for you.</p>"+
				   "<textarea name='reason-3' placeholder='Please share what happened.'></textarea>" +
				"</div>" +	
		      "</li>" + 
		      "<li class='reason'>" +
		 		"<input id='reason-4' type='radio' name='reason' value='I no longer need the plugin'>" +
		        "<label for='reason-4'>I no longer need the plugin</label>" +
		      "</li>" + 
		      "<li class='reason'>" +
		 		"<input id='reason-5' type='radio' name='reason' value='I was just test driving to see what it would do'>" +
		        "<label for='reason-5'>I was just test driving to see what it would do</label>" +
		      "</li>" + 
		      "<li class='reason'>" +
		 		"<input id='reason-6' type='radio' name='reason' value='Other'>" +
		        "<label for='reason-6'>Other</label>" +
				"<div class='secondary-questions'>" + 
				   "<textarea name='reason-6' placeholder='Please share the reason so that we can improve our plugin.'></textarea>" +
				"</div>" +			
		      "</li>" + 

		 	"</ul>" + 
		  "</div>" +
		  "<div class='pegasaas-modal-footer'>" +
		    "<div class='pegasaas-modal-disclaimer'>" +
		      "[disclaimer]" +
		    "</div>" +
		    "<a href='#' class='button button-secondary disabled'>Submit &amp; Deactivate</a> " +
		    "<a href='#' class='button button-primary button-close'>Cancel</a>" +
		  "</div>" + 
		"</div>";
	
	modal_html = modal_html.replace("[modal_title]", modal_title);
	modal_html = modal_html.replace("[disclaimer]", modal_disclaimer);

	
	var deactivate_plugin_link = jQuery('i.pegasaas-accelerator-deactivation-target').parent().find('a');
	
	

	deactivate_plugin_link.click(function (e) {
		e.preventDefault();
		jQuery("#pegasaas-exit-survey-modal").addClass("active");
	});
	jQuery(modal_html).appendTo(jQuery('body'));
	
	var pegasaas_exit_survey_modal = jQuery("#pegasaas-exit-survey-modal");
	
	pegasaas_exit_survey_modal.find("input[name='reason']").on("click", function() {
		pegasaas_exit_survey_modal.find("li.reason").removeClass("selected");
		if (jQuery(this).is(":checked")) {
	
			jQuery(this).parent("li.reason").addClass("selected");
		
		} 
		pegasaas_exit_survey_modal.find(".button-secondary").removeClass("disabled");
		
	});
	
	pegasaas_exit_survey_modal.on('click', '.button.button-secondary', function (e) {
			e.preventDefault();
			if(jQuery(this).hasClass('disabled')){
	
				return;
			}
			var reason = pegasaas_exit_survey_modal.find("input[name='reason']:checked");
		var reason_id = reason.attr("id");
		
		if (reason_id == "reason-1") {
			var reason_details = jQuery(pegasaas_exit_survey_modal).find("*[name='" + reason_id + "']:checked").val();

			
		} else {
			var reason_details = jQuery(pegasaas_exit_survey_modal).find("*[name='" + reason_id + "']").val();
 
		}
		
		if (reason_id == "reason-2" && reason_details == "") {
			alert("Please share the name of the plugin.");
		} else if (reason_id == "reason-3" && reason_details == "") {
			alert("Please share what happened.");
		} else if (reason_id == "reason-6" && reason_details == "") { 
			alert("Please share the reason so that we can improve our plugin.");
		} else {
			var deactivation_link = jQuery('i.pegasaas-accelerator-deactivation-target').parent().find('a').attr('href');
			 
			jQuery.ajax({
					url       : ajaxurl,
					method    : 'POST',
					data      : {
						'action'     : 'pegasaas_send_exit_survey',
						'reason_id'  : reason_id,
						'reason'  : reason.val(),
						'reason_details' : reason_details
					}, 
					beforeSend: function () {
						jQuery("#pegasaas-exit-survey-modal .button").addClass("disabled");
						jQuery("#pegasaas-exit-survey-modal .button-secondary").text("Deactivating...");
						
						
					},
					complete  : function () {
						if (reason_id !== "reason-1") {
							alert("If you are permanently deactivating this plugin, please remember to cancel your subscription via your account at pegasaas.com");
						}
						
						window.location.href = deactivation_link;
					}
				});			
			
		}
		
		
	});
	
	// handle click outside of dialog, and the close button
	pegasaas_exit_survey_modal.on('click', function (e) {
			var target = jQuery(e.target);
			
			if (target.hasClass('pegasaas-modal-body') || target.hasClass('pegasaas-modal-footer')) {
				return;
			}
		
			// if clicked element is not the close button and is inside the modal dialog, just exit
			if (!target.hasClass('button-close') && (target.parents('.pegasaas-modal-body').length > 0 || target.parents('.pegasaas-modal-footer').length > 0 )) {
				return;
			}
		
			pegasaas_exit_survey_modal.removeClass('active');
		});	
	
});