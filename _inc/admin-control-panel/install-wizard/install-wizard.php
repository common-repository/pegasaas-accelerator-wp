<?php
$api_connection_error 		= (PegasaasAccelerator::$settings['reason_short'] == "timeout" && $_POST['c'] == "register-api-key");
$selected_interface_type 	= (!isset($_POST['interface_type']) ||$_POST['interface_type'] == "") ? 1 : $_POST['interface_type'] * 1;
$selected_interface_level 	= (!isset($_POST['interface_level']) || $_POST['interface_level'] == "") ? 0 : $_POST['interface_level'] * 1;
$selected_system_mode 		= (!isset($_POST['system_mode']) || $_POST['system_mode'] == "") ? "live" : $_POST['system_mode'] * 1;
$selected_speed_configuration 	= (!isset($_POST['speed_configuration']) || $_POST['speed_configuration'] == "") ? "3" : $_POST['speed_configuration'] * 1;
$api_request_email 			= (!isset($_POST['api_request_email']) || $_POST['api_request_email'] == "") ? get_option("admin_email") : $_POST['api_request_email'];
$user_data = wp_get_current_user();

$user_full_name = $user_data->data->display_name;
$user_full_name_data = explode(" ", $user_full_name);

$user_first_name = array_shift($user_full_name_data);
$user_last_name = implode(" ", $user_full_name_data);
$api_request_first_name		= (!isset($_POST['api_request_first_name']) || $_POST['api_request_first_name'] == "") ? $user_first_name : $_POST['api_request_first_name'];
$api_request_last_name		= (!isset($_POST['api_request_last_name']) || $_POST['api_request_last_name'] == "") ? $user_last_name : $_POST['api_request_last_name'];
$api_promo_code 			= (!isset($_POST['api_promo_code']) || $_POST['api_promo_code'] == "") ? "" : $_POST['api_promo_code'];
$api_key_type 				= (!isset($_POST['api_key_type']) || $_POST['api_key_type'] == "") ? "quick" : $_POST['api_key_type'];
$api_key 					= (!isset($_POST['api_key']) || $_POST['api_key'] == "") ? "" : $_POST['api_key'];
$agree_to_terms_of_service	= (isset($_POST['agree_to_terms_of_service']) && $_POST['agree_to_terms_of_service'] == "Yes") ? true : false;
$acceleration_type			= (!isset($_POST['acceleration-type']) || $_POST['acceleration-type'] == "") ? "all" : $_POST['acceleration-type'];
$enable_pages= array();
if (is_array($_POST['enable_acceleration_on'])) {
	foreach ($_POST['enable_acceleration_on'] as $slug) {
		$enable_pages["{$slug}"] = true;
	}
}

?>
<div class='install-wizard-container'>

	<div class='row'>
		<h1 class='text-center setup-wizard-title'><?php _e("Setup Wizard", "pegasaas-accelerator"); ?></h1>
		<p class='text-center setup-wizard-tagline'><?php _e("You are just moments away from SUPER charging your website!", "pegasaas-accelerator"); ?></p>
		<?php include "nav-bar.php"; ?>
	</div>

  	<form role="form" action="?page=<?php if (PegasaasAccelerator::$settings['settings']['white_label']['status'] == 1) { ?>pa-web-perf<?php } else { print "pegasaas-accelerator"; }?>" method="post" class='setup-wizard-form'>
		<input type='hidden' name='c' value='register-api-key' />
		<?php include "step-start.php"; ?>
    	<?php include "step-compatibility.php"; ?>
    	<?php include "step-ui.php"; ?>
    	<?php include "step-ux.php"; ?>
		<?php include "step-api.php"; ?>
		
		<?php include "step-terms.php"; ?>
		<?php if ($pegasaas->cache->cloudflare_exists() && !$pegasaas->cache->cloudflare_credentials_valid()) { ?>
		<?php include "step-cloudflare.php"; ?>
		<?php } ?>
		<?php include "step-configure.php"; ?>
		<?php include "step-speed.php"; ?> 
		<?php include "step-mode.php"; ?>
  	</form>
  
</div>
</div>
</div>
<script>
	jQuery(".config-acceleration-type-label select").bind("change", function(e) {
		var span = jQuery(this).parents("label").find("span.est-time");
		var number_of_pages = this.options[e.target.selectedIndex].value;
		var speed_factor = span.attr("rel");

		var minutes = parseInt(number_of_pages * speed_factor);
		var message = minutes + " minutes";
		if (minutes > 60) {
			message = minutes/60 + " hours";;
		}
		span.html(message);
		
	});
	
	function adjust_acceleration_type_label_height() {
		jQuery(".config-acceleration-type-label").height("auto");
		var label_height = 0;
		jQuery(".config-acceleration-type-label").each(function() {
			if (jQuery(this).height() > label_height) {
				label_height = jQuery(this).height();
			}
			console.log(jQuery(this).height());
		
		});
		jQuery(".config-acceleration-type-label").height(label_height);
	}
	
	
	jQuery(window).on("load", function () { adjust_acceleration_type_label_height(); });
	jQuery(window).on("resize", function () { adjust_acceleration_type_label_height(); });
						   
	
//jQuery(document).ready(function () {
  var navListItems = jQuery('div.setup-panel div a'),
          allWells = jQuery('.setup-content'),
          allNextBtn = jQuery('.nextBtn'),
          allBackBtn = jQuery('.backBtn');

  allWells.hide();

  navListItems.click(function (e) {
      e.preventDefault();
      var $target = jQuery(jQuery(this).attr('href')),
              $item = jQuery(this);

      if (!$item.hasClass('disabled')) {
          navListItems.removeClass('btn-primary').addClass('btn-default');
          $item.addClass('btn-primary');
          allWells.hide();
          $target.show();
          $target.find('input:eq(0)').focus();
      }
	  adjust_acceleration_type_label_height();
  });

  allNextBtn.click(function(){
      var curStep = jQuery(this).closest(".setup-content"),
          curStepBtn = curStep.attr("id"),
          nextStepWizard = jQuery('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
          curInputs = curStep.find("input[type='text'],input[type='url'],input[type='checkbox'],input[type='email']"),
          isValid = true;

      jQuery(".form-group").removeClass("has-error");
      for(var i=0; i<curInputs.length; i++){
		  console.log("current " + i);
          if (!curInputs[i].validity.valid){
              isValid = false;
              jQuery(curInputs[i]).closest(".form-group").addClass("has-error");
		 
		  
		  } else if (curInputs[i].type == "checkbox") {
			
		  } else {
			  
		  }
      }
	  
	  if (curStepBtn == "step-speed") {
		  var api_key_type = jQuery("input[name='api_key_type']:checked").val();
		  var speed_mode = jQuery("input[name='speed_configuration']:checked").val();
		  if (api_key_type == "quick" && speed_mode == 4) {
			  isValid = false;
			  alert("Beast Mode is only available to PREMIUM subscribers.  Please select a different mode, or go back to step 4 to sign up for a PREMIUM API KEY.");
		  }
	  }

      if (isValid) {
          nextStepWizard.removeAttr('disabled').trigger('click');
	  }
	  
	  adjust_acceleration_type_label_height()
  });

allBackBtn.click(function(){
      var curStep = jQuery(this).closest(".setup-content"),
          curStepBtn = curStep.attr("id"),
          prevStepWizard = jQuery('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().prev().children("a");
         

     
     

     
          prevStepWizard.removeAttr('disabled').trigger('click');
	  
	  
	  
  });	
	
  jQuery('div.setup-panel div a.btn-primary').trigger('click');
  jQuery('form.setup-wizard-form').submit(function(e) {
	  
	  jQuery(this).find('button[type=submit]').attr("disabled", true);
	  jQuery(this).find('button[type=submit]').html("Completing Setup <i class='svg-icon-14 svg-tail-spin-white'></i>");
  })
// });
</script>