<?php
if (isset($_GET['action']) && $_GET['action'] == "activate") {
	if (isset(PegasaasAccelerator::$settings['status']) && PegasaasAccelerator::$settings['status'] === 0) {
		$pegasaas->enable();

	}
	
}
if ($_POST['c'] == "disable-endurance") { 
  $pegasaas->cache->disable_endurance();
}

if ($_POST['c'] == "force-htaccess-update") {
	update_option("pegasaas_force_htaccess_write", true);
}

if ($_GET['c'] == "purge_wpengine") {
	$pegasaas->cache->clear_wpengine_cache(1);
	print "cleared wp-engine cache";
}

if ($_POST['c'] == "register-api-key") {
	$pegasaas->api->pegasaas_api_key_check($_POST['api_key'], false);

	if (PegasaasAccelerator::$settings['status'] > -1) {
		if (isset($_POST['system_mode'])) {
			if ($_POST['system_mode'] == "staging") {
				$pegasaas->enable_development_mode("indefinitely");
			} else {
				$pegasaas->enable();
			}
		} else {
			$pegasaas->enable();
		}
		if ($pegasaas->cache->varnish_exists(true)) { 
			$pegasaas->enable_feature("varnish");
		}
		if ($pegasaas->cache->cloudflare_exists() && !$pegasaas->cache->cloudflare_credentials_valid()) {
			$pegasaas->save_local_setting('cloudflare_account_email', $_POST['cloudflare_account_email']);
			$pegasaas->save_local_setting('cloudflare_api_key', $_POST['cloudflare_api_key']);
			$pegasaas->enable_feature("cloudflare");

		}


		$pegasaas->auto_accelerate_pages();
			
		// submit list to be scanned
		$pegasaas->scanner->submit_benchmark_requests();
		$pegasaas->scanner->submit_scan_request();
	}
	

}

if ($_GET['test'] == "api-key-check") {
	//var_dump(PegasaasAccelerator::$settings);
	$pegasaas->api->pegasaas_api_key_check(PegasaasAccelerator::$settings['api_key'], true);
}
if (isset($_GET['ignore-ssl-warning'])) {
	update_option("pegasaas_accelerator_ignore_ssl_warning", 1);
} else if ($_POST['c'] == 'remove-ssl-warning-override') {
	delete_option("pegasaas_accelerator_ignore_ssl_warning");
}

$page_view = '';// default

if ($_GET['tab'] == "history") {
	$page_view = "history";
} else if ($_GET['tab'] == "settings") {
	$page_view = "settings";
} else if ($_GET['tab'] == "cache") {
	$page_view = "cache";
} else if ($_GET['tab'] == "account") {
	$page_view = "account";
} else if ($_GET['tab'] == "faqs") {
	$page_view = "faqs";
} else if ($_GET['tab'] == "support") {
	$page_view = "support";
}


if ($_POST['c'] == "Disable") {
	$pegasaas->disable();
} else if ($_POST['c'] == "Enable") { 
	$pegasaas->enable();
} else if ($_POST['c'] == "re-accelerate-post-type") {
	//ob_end_clean();
	$pegasaas->enable_all_for_post_type($_POST['pt']);
	?>
<script>parent.set_accelerate_buttons('<?php echo $_POST['pt']; ?>', 'enabled');</script>
<?php
//	exit;
}else if ($_POST['c'] == "de-accelerate-post-type") {
	$pegasaas->disable_all_for_post_type($_POST['pt']);
	?>
<script>parent.set_accelerate_buttons('<?php echo $_POST['pt']; ?>', 'disabled');</script>
<?php
	exit;	
} else if ($_POST['c'] == "enable-feature") {
	$pegasaas->enable_feature($_POST['f']);

} else if ($_POST['c'] == "disable-feature") { 
	$pegasaas->disable_feature($_POST['f']);

} else if ($_POST['c'] == "change-feature-status") {
	$pegasaas->set_feature($_POST['f'], $_POST['s']);

} else if ($_POST['c'] == "change-feature-attribute") {
	ob_end_clean();
	print "Change Feature Attribute:";
	var_dump($_POST);
	$feature = $_POST['f'];
	if ($_POST['s'] == "on") { $_POST['s'] = 1; } else if ($_POST['s'] == "") { $_POST['s'] = 0; }
	$feature_status = PegasaasAccelerator::$settings['settings']["{$feature}"]['status'];
	$pegasaas->set_feature($feature, $feature_status, array($_POST['a'] => $_POST['s']));
	exit;
	
} else if ($_POST['c'] == "change-local-setting") {
	$pegasaas->save_local_setting($_POST['f'], $_POST['s']);

	
} else if ($_GET['c'] == "purge-page-cache") {
	$pegasaas->cache->clear_cache();	

} else if ($_POST['c'] == "set-cloudflare-credentials") {
	$pegasaas->save_local_setting('cloudflare_account_email', $_POST['cloudflare_account_email']);
	$pegasaas->save_local_setting('cloudflare_api_key', $_POST['cloudflare_api_key']);
	$pegasaas->cache->cloudflare_credentials_valid($force_check = true);

} else if ($_POST['c'] == "toggle-local-setting") {
	$pegasaas->toggle_local_setting($_POST['f']);

} else if ($_POST['c'] == "toggle-local-complex-setting" || $_POST['c'] == "change-local-complex-setting") {
	$pegasaas->toggle_local_complex_setting($_POST);

	
} else if ($_POST['c'] == "add-http-auth-instructions") {
	$pegasaas->utils->add_http_auth_instructions();

} else if ($_POST['c'] == "enable-wordfence-whitelisting") {
	$pegasaas->utils->whitelist_pegasaas_in_wordfence();
	
	
} else if ($_POST['c'] == "re-check-http-auth") {
	$pegasaas->utils->http_auth_active_and_blocking($force_check = true);

} else if ($_POST['c'] == "create-test-data") {
	$pegasaas->create_test_data();
	
} else if ($_POST['c'] == "purge-unoptimized-image-cache") {
	$pegasaas->cache->purge_unoptimized_cached_images();

} else if ($_POST['c'] == "purge-optimized-image-cache") {
	$pegasaas->cache->purge_optimized_cached_images();

} else if ($_POST['c'] == "purge-all-local-image-cache") {
	$pegasaas->cache->purge_all_local_cached_images();
	
} else if ($_POST['c'] == "clear-critical-css") {
	$page_view = "cache";
	
} else if ($_POST['c'] == "clear-deferred-js") {
	$page_view = "cache";
} else if ($_POST['c'] == "clear-queued-requests") {
	$page_view = "cache";
} else if ($_POST['c'] == "clear-queued-optimization-requests") {
	$page_view = "cache";
} else if ($_POST['c'] == "clear-cache") {
	$page_view = "cache";
} else if ($_POST['c'] == "clear-pagespeed-requests") {
	$page_view = "cache";
} else if ($_POST['c'] == "clear-pagespeed-scores") {
	$page_view = "cache";
} else if ($_POST['c'] == "clear-pagespeed-benchmark-scores") {
	$page_view = "cache";	
} else if ($_POST['c'] == "clear-remote-cache") {
	$page_view = "cache";
}  else if ($_POST['c'] == "hide-diagnostics") {
	$pegasaas->interface->hide_diagnostics();	
	$page_view = "cache";

} else if ($_POST['c'] == "update-plugin" || $_GET['c'] == "update-plugin") {
    // provided we get past the check, render the process page
    $pegasaas->download_and_update();
	return;	

} else if ($_POST['c'] == "render-benchmark-chart" || $_GET['c'] == "render-benchmark-chart") {
	ob_end_clean();
	ob_end_clean();
		
	$benchmark_data = $pegasaas->scanner->get_site_benchmark_score(); 
	include("charts/benchmark-chart.php");
	exit;
} else if ($_POST['c'] == "render-accelerated-chart" || $_GET['c'] == "render-accelerated-chart") {
	ob_end_clean();
	ob_end_clean();

	$score_data = $pegasaas->scanner->get_site_score(); 
	include("charts/accelerated-chart-2.php");
	
	exit;
} else if ($_POST['c'] == "render-page-recommendation-button") {
	ob_end_clean();
	ob_end_clean();
	
	$pegasaas->interface->render_recommended_actions_button($_POST['rid'], $_POST['pid']);
	exit;

} else if ($_POST['c'] == "push-scan") {
	ob_end_clean();
	ob_end_clean();
	exit;
	
}
if ($_POST['c'] == "disable-feature" || $_POST['c'] == "enable-feature") {
	print "Feature Saved";
	exit;
}

$pages_accelerated 	= $pegasaas->scanner->get_site_pages_accelerated_data();

$pegasaas->utils->console_log("Pegasaas Beginning of Admin Control Panel");
?>