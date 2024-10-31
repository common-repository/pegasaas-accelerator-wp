<?php
class PegasaasHermes {
	function __construct() {
	
	}
	
	function send_init_complete_notification() {
		global $pegasaas;
		
		$to = PegasaasAccelerator::$settings['account']['email_address'];
		$subject = "[Pegasaas Accelerator WP] Initialization Complete";
		//$message = "<img src='".PEGASAAS_ACCELERATOR_URL."assets/images/pegasaas-accelerator-horizontal-logo-dark.png'>";
		$message = $this->get_email_header();
		$message .= "<p><b>Greetings!</b></p>";
		$message .= "<p>We are pleased to report that the initial optimizations and web performance scans, for ".get_home_url().", have just completed.  You may ";
		$message .= "view the initial scores on the <a href='".get_dashboard_url()."admin.php?page=pegasaas-accelerator'>Pegasaas Accelerator WP dashboard</a>.</p>";
		
		
		$message .= "<p style='margin-top: 40px;'>Thank you!</p>\n";
		$message .= "<p>The Pegasaas Team </p>\n";		
		
		$message = "<html><body>$message</body></html>";
		$headers = array("Content-Type: text/html");
		
		wp_mail($to, $subject, $message, $headers);
		
	}	
	
	function should_send_limit_notification() {
		global $pegasaas;
		if ($pegasaas->is_free() && PegasaasAccelerator::$settings['limits']['monthly_optimizations'] > 0 ) {
			$percent_used = 100 * (PegasaasAccelerator::$settings['limits']['monthly_optimizations']- PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining']) / PegasaasAccelerator::$settings['limits']['monthly_optimizations'];

			if ($percent_used >= 90) {
				$notification_sent = get_option("pegasaas_limit_notification_sent", false);
				if (!$notification_sent) {
				
					return true;
				
				} 
			}
		} 
	}
	
	function send_approaching_limit_notification() {
		global $pegasaas;
		
		$percent_used = 100 * (PegasaasAccelerator::$settings['limits']['monthly_optimizations']- PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining']) / PegasaasAccelerator::$settings['limits']['monthly_optimizations'];

		$to = PegasaasAccelerator::$settings['account']['email_address'];

		$subject = "[Pegasaas Accelerator WP] ".PegasaasAccelerator::$settings['subscription_name']." Account Nearing Plan Limits";
		$message = $this->get_email_header();
		//$message = "<img src='".PEGASAAS_ACCELERATOR_URL."assets/images/pegasaas-accelerator-horizontal-logo-dark.png'>";
		$message .= "<div style='text-align:center'><img width='33px' alt='Warning' src='https://pegasaas.com/Site/graphics/warning-icon.png'>";
		$message .= "<h3>Pegasaas Accelerator WP Account Nearing Plan Limits</h3></div>";
		$message .= "<p><b>Greetings ".PegasaasAccelerator::$settings['account']['first_name'].",</b></p>";
		$message .= "<p>Thank you for using Pegasaas Accelerator WP!</p>";
	
		$message .= "<p style='borde-radius: 6px; text-align: center; background-color: #B1E6FD; padding:40px;'>";
		$message .= "We noticed that your Pegasaas Accelerator WP '".PegasaasAccelerator::$settings['subscription_name']."' plan <b>has reached {$percent_used}% of its monthly ";
		$message .= "optimizations quota</b> for ".$pegasaas->utils->get_http_host()."</p>";
		$upgrade_link = "https://pegasaas.com/upgrade/?utm_source=nearing-plan-limit-email&upgrade_key=".PegasaasAccelerator::$settings['api_key']."-".PegasaasAccelerator::$settings['installation_id']."&site=".$pegasaas->utils->get_http_host()."&current=".PegasaasAccelerator::$settings['subscription'];

		$message .= "<p>If you wish to upgrade your plan - ";
		$message .= "<a href='{$upgrade_link}'>please follow this link.</a></p>\n\n";  
	 
		

		//$message .= "<p> If you have any questions, please feel free to <a href='https://pegasaas.com/contact/'>contact us via our website</a> or call us at 1-866-943-5733 </p>\n";
		$message .= "<p style='margin-top: 40px;'>Thank you,</p>\n";
		$message .= "<p>The Pegasaas Team </p>\n";		
		
		$message = "<html><body>$message</body></html>";
		
		$headers = array("Content-Type: text/html");
		update_option("pegasaas_limit_notification_sent", true);
		
		wp_mail($to, $subject, $message, $headers);
		$pegasaas->api->notify_limit_approaching();
		
	}	
	
	function get_email_header($background = "light") {

				
		if ($background == "dark") {
			$backgroundColor = "background-color: #3A444C;";
			$backgroundHeight = "height: 40px";
			$headerMargin = "height: 20px;";
			$imageHeight = "100";
			$email_logo = "https://pegasaas.com/Site/graphics/pegasaas-email-logo.png";
		} else if ($background == "light") {
			$backgroundColor = "background-color: #46AFEA; background-size: cover; background-image: url(https://pegasaas.com/Site/graphics/pegasaas-light-header.jpg); background-repeat: no-repeat; background-position: bottom right; ";
			$backgroundHeight = "height: 50px";
			$headerMargin = "height: 20px;";
			$imageHeight = "100"; 		
			$email_logo = "https://pegasaas.com/Site/graphics/pegasaas-email-logo-light.png";
			$inner_table = "min-height: 250px; -webkit-border-horizontal-spacing: 0px;"; 
			$inner_cell = "background-color: #fff;";
			$inner_style = "padding: 0px; background-color: #fff; border-radius: 6px; -webkit-border-horizontal-spacing: 0px;  ";
			$inner_style2 = "border: 1px solid #B1E6FD; border-radius: 6px;";
			$div_styles = "border-radius: 6px; box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.10);  ";
		} else { 
			$backgroundHeight = "height: 0px;";
			$headerMargin = "height: 0px;";
			$imageHeight = "75";
			$email_logo = "https://pegasaas.com/Site/graphics/pegasaas-email-logo.png";
		}
		
		$header = "<table style='{$backgroundColor} text-align: center;' width='100%'>";
		$header .= "<tr><td style='{$backgroundHeight}'>&nbsp;</td></tr>";
		$header .= "<tr><td style='text-align: center'>";
		
		$header .= "<a href='https://pegasaas.com/'><img src='{$email_logo}' alt='Pegasaas - Web Performance, Made Simple' width='{$imageHeight}' height='{$imageHeight}' style='width: {$imageHeight}px; margin: auto;'></a>";
		$header .= "</td>";
		$header .= "<tr><td style='{$backgroundHeight}'>&nbsp;</td></tr>";
		if ($background != "light") { 
			$header .= "</table>";
			$header .= "<table>";
			$header .= "<tr><td style='{$headerMargin}'>&nbsp;</td></tr>";
			$header .= "</table>"; 
			$header .= "<table width='100%' style='color: #3A444C; font-size: 14px; font-weight: normal; line-height: 17px ; font-family: \"Helvetica Neue\",Helvetica,Arial,sans-serif;'>";
		}
		
		$header .= "<tr><td align='center' style='text-align: center;'><div style='max-width: 600px; margin: auto; {$div_styles}'>";
		$header .= "<table width='100%' align='center' style='{$inner_table}'><tr><td style='{$inner_style}'><div style='border-radius: 6px; border: 1px solid #B1E6FD;'>";
			$header .= "<table width='100%' align='center'><tr><td style='height: 20px' height='20'>&nbsp;</td></table>";
		// for main content
		$header .= "<table width='100%' align='center'><tr><td style='width: 20px' width='20'>&nbsp;</td>";
		$header .= "<td align='left' style='text-align: left; max-width: 560px; margin: auto; color: #3A444C; font-size: 14px; font-weight: normal; line-height: 17px ; font-family: \"Helvetica Neue\",Helvetica,Arial,sans-serif;'>";	
		
		return $header;
	
	}
	
	function get_email_footer($style = "dark", $include_social = false, $include_support = false) {

		$support_table = "background-color: #B1E6FD;";
		$social_table = "";
		$padding_cell = "";
		$h4_style = "font-size: 18px; margin-top: 18px; margin-bottom: 0px; padding-bottom: 0px; "; 
		
		// close main content
		$footer = "<td style='width: 20px' width='20'>&nbsp;</td></tr></table>";
		$footer .= "<table><tr><td style='height: 20px;'>&nbsp;</td></tr></table>";
		if ($include_support) {
			
			$footer .= "<table width='100%' align='center' style='{$support_table}'><tr><td style='width: 20px' width='20'>&nbsp;</td>";
			$footer .= "<td style='width:50px;' width='50'><img src='https://pegasaas.com/Site/graphics/support-icon.png' width='40px' alt='Get Help'></td>";
			$footer .= "<td align='left' style='text-align: left; max-width: 560px; margin: auto; font-size: 14px; font-weight: normal; line-height: 17px ; font-family: \"Helvetica Neue\",Helvetica,Arial,sans-serif;'>";
			$footer .= "<table><tr><td style='height: 20px;'>&nbsp;</td></tr></table>";
			$footer .= "<h4 style='{$h4_style}'>Need Help?</h4><p style='margin-top: 5px;'>For any additional help, please feel free to contact our <a href='https://pegasaas.com/contact/'>support team</a>.</p>";
			$footer .= "<table><tr><td style='height: 20px;'>&nbsp;</td></tr></table>";
			$footer .= "</td>";
			$footer .= "<td style='width: 20px' width='20'>&nbsp;</td></tr></table>";
			
		}
		if ($include_social) {
			
			$footer .= "<table width='100%' align='center' style='{$social_table}'><tr><td style='width: 20px' width='20'>&nbsp;</td>";
			$footer .= "<td align='left' style='text-align: left; max-width: 560px; margin: auto; font-size: 14px; font-weight: normal; line-height: 17px ; font-family: \"Helvetica Neue\",Helvetica,Arial,sans-serif;'>";
			$footer .= "<table><tr><td style='height: 10px; line-height: 10px;'>&nbsp;</td></tr></table>";
			$footer .= "<table><tr>";
			$footer .= "<td style='width:40px;' width='40'><a href='https://facebook.com/pegasaas'><img style='width: 33px; height: 33px;' width='33' height='33' src='https://pegasaas.com/Site/graphics/facebook-icon.png' alt='Facebook'></a></td>";
			$footer .= "<td style='width:40px;' width='40'><a href='https://twitter.com/pegasaas'><img style='width: 33px; height: 33px;' width='33' height='33' src='https://pegasaas.com/Site/graphics/twitter-icon.png' alt='Twitter'></a></td>";
		//	$footer .= "<td style='width:40px;' width='40'><a href='https://linkedin.com/pegasaas'><img style='width: 33px; height: 33px;' width='33' height='33' src='https://pegasaas.com/Site/graphics/facebook-icon.png' alt='Facebook'></a></td>";
			$footer .= "<td style='width:40px;' width='40'><a href='https://www.youtube.com/channel/UCAnn6YuahIbGgiD_NGR1eZA'><img style='width: 33px; height: 33px;' width='33' height='33' src='https://pegasaas.com/Site/graphics/youtube-icon.png' alt='YouTube'></a></td>";
			$footer .= "</tr></table>";
			//$footer .= "<h4 style='{$h4_style}'>Need Help?</h4><p style='margin-top: 5px;'>For any additional help, please feel free to contact our <a href='https://pegasaas.com/contact/'>support team</a>.</p>";
			$footer .= "<table><tr><td style='height: 10px; line-height: 10px;'>&nbsp;</td></tr></table>";
			
			$footer .= "</td>"; 
			$footer .= "<td style='width: 20px' width='20'>&nbsp;</td></tr></table>";
			
		}		
		
		if (!$include_support && !$include_social) {
			$footer .= "<table><tr><td style='height: 20px;'>&nbsp;</td></tr></table>";
		}
		$footer .= "</div></td>";
		$footer .= "{$padding_cell}";
				
		$footer .= "</tr></table>";
		

		$footer .= "</td></tr></table>";	

		$footer .= "</div></td></tr></table>";
		
		return $footer;
	}		
}
?>