<?php $pegasaas->utils->console_log("Dashboard 1"); ?>
<div class='pegasaas-dashboard <?php if (PegasaasAccelerator::$settings['settings']['white_label']['status'] == 1) { print "pa-wl"; } ?> 
<?php if (PegasaasAccelerator::$settings['settings']['display_level']['status'] == 0) { 
	print " interface-novice"; 
} else if (PegasaasAccelerator::$settings['settings']['display_level']['status'] == 1) { 
	print " interface-intermediate";
} else if (PegasaasAccelerator::$settings['settings']['display_level']['status'] == 2) { 
	print " interface-advanced";
} 
if ($pegasaas->in_development_mode()) { 
	print " staging-mode"; 
}
if ($pegasaas->in_diagnostic_mode()) { 
	print " diagnostic-mode"; 
} 
	 
if (PegasaasAccelerator::$settings['settings']['display_mode']['status'] == 1) { 
	print " display-mode-light";
} else if (PegasaasAccelerator::$settings['settings']['display_mode']['status'] == 2) {  
	print " display-mode-plain";
} else {
	print " display-mode-dark";
}	
	 
if (PegasaasAccelerator::$settings['settings']['coverage']['status'] == 0) { 
	print " coverage-level-standard";
} else if (PegasaasAccelerator::$settings['settings']['coverage']['status'] == 1) {  
	print " coverage-level-extended";
} 	 
	 
if (PegasaasAccelerator::$settings['settings']['speed_configuration']['status'] == 1) { 
	print " speed-configuration-basic speed-configuration-auto";
} else if (PegasaasAccelerator::$settings['settings']['speed_configuration']['status'] == 2) {  
	print " speed-configuration-supersonic speed-configuration-auto";
} else if (PegasaasAccelerator::$settings['settings']['speed_configuration']['status'] == 3) { 
	print " speed-configuration-hypersonic speed-configuration-auto";
} else if (PegasaasAccelerator::$settings['settings']['speed_configuration']['status'] == 4) {  
	print " speed-configuration-beastmode speed-configuration-auto";

} else { 
	print " speed-configuration-manual"; 
} ?>
	 
'>
<?php 

 
 
/***** do requirement checks ******/
$active_conflicting_plugins = $pegasaas->get_active_conflicting_plugins();

if ($pegasaas->cache->endurance_active()) {
	include "prompts/endurance-active.php";
	
} else if (!$pegasaas->utils->permalinks_active()) {
	include "prompts/permalinks-not-active.php";

} else if (false && $pegasaas->cache->mod_pagespeed_exists(get_option("pegasaas_mod_pagespeed_last_exists_query_result", false))) { 
	include "prompts/mod-pagespeed-exists.php";

} else if (false && $pegasaas->cache->cloudflare_exists() && !$pegasaas->cache->cloudflare_credentials_valid()) { 
	include "prompts/cloudflare-exists.php";

} else if (strstr($pegasaas->utils->get_http_host(), "localhost") || strstr($pegasaas->utils->get_http_host(), ".local")) {
	include "prompts/localhost.php";

} else if (get_option("pegasaas_accelerator_ignore_ssl_warning", 0) == 0 && $pegasaas->utils->has_bad_certificate()) {
	include "prompts/has-bad-certificate.php";
	
} else if ($pegasaas->utils->wordfence_active_and_blocking()) {
	include "prompts/wordfence-active-and-not-whitelisting.php";
	//include "prompts/wordfence-active-and-blocking.php";
	
} else if ($pegasaas->utils->http_auth_active_and_blocking()) {
	include "prompts/http-auth-active-and-blocking.php";
	
} else if (sizeof($active_conflicting_plugins) > 0) { 
	include "prompts/conflicting-plugins.php";
	
} else if (PegasaasAccelerator::$settings['status'] == -1 || !PegasaasAccelerator::$settings  || PegasaasAccelerator::$settings['api_key'] == "") {
	if (PegasaasAccelerator::are_write_permissions_insufficient()) {
		include "prompts/write-permissions-required.php";
	} else {
		include "install-wizard/install-wizard.php";
	}
} else { 
	//$pegasaas->utils->console_log("Dashboard 85"); 
	
	 
	
	if (!$skip_to_support) {
		$pegasaas->utils->console_log("Dashboard 90");
		include "prompts/dashboard-interface-2.php";
		$pegasaas->utils->console_log("Dashboard 92");
	}
//$pegasaas->utils->console_log("Dashboard 94");
	if (true ||  !$prepping) { 
	//$pegasaas->utils->console_log("Dashboard 96");
		include "prompts/main-interface.php";
	}
	//$pegasaas->utils->console_log("Dashboard 99");
} 
?>
</div>