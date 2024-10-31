<?php
define( 'PEGASAAS_ACCELERATOR_VERSION', '3.8.15' );
define( 'PEGASAAS_ACCELERATOR_REQUIRED_PHP_VERSION', '5.4.0' );
define( 'PEGASAAS_ACCELERATOR_RELEASE_DATE', date_i18n( 'F j, Y', '1407877048' ) );
define( 'PEGASAAS_ACCELERATOR_DIR', plugin_dir_path( __FILE__ ) );
define( 'PEGASAAS_ACCELERATOR_URL', plugin_dir_url( __FILE__ ) );
define( 'PEGASAAS_API_KEY_SERVER', "https://api.pegasaas.io/"); 
define( 'PEGASAAS_CLOUDFLARE_TEST_ENDPOINT', "https://pegasaas.io/cloudflare-test/");
define( 'PEGASAAS_REMOTE_IP_TEST_ENDPOINT', "https://pegasaas.io/get-server-ip/");
define( 'PEGASAAS_API_KEY_VALIDITY', 86400); //86400
define( 'PEGASAAS_ACCELERATOR_CACHE_SERVER', 'pegasaas.io');
define( 'PEGASAAS_ACCELERATOR_API_TIMEOUT', 20);
define( 'PEGASAAS_ACCELERATOR_API_OPTIMIZATION_TIMEOUT', 10);
define( 'PEGASAAS_ACCELERATOR_SOURCE_TYPE', 'normal');

// to disable w3tc
if (!defined('W3TC_IN_MINIFY')) {
	//define('W3TC_IN_MINIFY', true);
}
 


if (is_multisite()) {
	define( 'PEGASAAS_CACHE_FOLDER_PATH', WP_CONTENT_DIR.'/pegasaas-cache/sites/'.get_current_blog_id());
	define( 'PEGASAAS_CACHE_FOLDER', pegasaas_get_content_folder().'/pegasaas-cache/sites/'.get_current_blog_id());
	define( 'PEGASAAS_CACHE_FOLDER_URL', content_url().'/pegasaas-cache/sites/'.get_current_blog_id());
} else {
	define( 'PEGASAAS_CACHE_FOLDER_PATH', WP_CONTENT_DIR.'/pegasaas-cache');
	define( 'PEGASAAS_CACHE_FOLDER', pegasaas_get_content_folder().'/pegasaas-cache');
	define( 'PEGASAAS_CACHE_FOLDER_URL', content_url().'/pegasaas-cache');

}
//print PEGASAAS_CACHE_FOLDER_PATH;
?>