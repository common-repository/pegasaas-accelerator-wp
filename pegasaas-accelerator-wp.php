<?php
/*
Plugin Name: Pegasaas Accelerator WP
Plugin URI: http://pegasaas.com/
Description: The Pegasaas Accelerator WP plugin provides complete, automated, and simplified web performance optimization for a faster website and higher Google PageSpeed score.
Version: 3.8.15
Author: pegasaas.com
Author URI: https://pegasaas.com/
*/

// do not load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!defined('PEGASAAS_ACCELERATOR_VERSION')) {

	define('PEGASAAS_ACCELERATOR_TYPE', 'standard');
	require_once("_inc/functions.php");
	require_once("constants.php");
	
	require_once("_inc/plugin-pre-setup.php");
	
	$GLOBALS["pegasaas"] = new PegasaasAccelerator();	 

	function pegasaas_activation_redirect( $plugin ) {
		if( $plugin == plugin_basename( __FILE__ ) ) {
			
			if (isset(PegasaasAccelerator::$settings['settings']['white_label']['status']) && PegasaasAccelerator::$settings['settings']['white_label']['status'] == 1) {
				exit( wp_redirect( admin_url( 'admin.php?page=pa-web-perf&action=activate' ) ) );
			} else {
				exit( wp_redirect( admin_url( 'admin.php?page=pegasaas-accelerator&action=activate' ) ) );
			}

		} else if ($plugin == "wp-optimize/wp-optimize.php") {
			PegasaasCache::disable_wp_optimize_cache();
		 
		} else if ($plugin == "w3-total-cache/w3-total-cache.php") {
			
		} 
	}
	if (!defined('WP_CLI')) {

		add_action( 'activated_plugin', 'pegasaas_activation_redirect' );
	}
	
}

?>