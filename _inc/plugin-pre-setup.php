<?php

if ( isset($_GET['accelerate']) && $_GET['accelerate']=="off" ) {
	header_remove("Content-Encoding");
} 


$GLOBALS['pegasaas_page_start_time'] = array_sum(explode(' ', microtime()));


require_once(PEGASAAS_ACCELERATOR_DIR."_inc/classes/pegasaas-accelerator.class.php");
require_once(PEGASAAS_ACCELERATOR_DIR."_inc/classes/pegasaas-interface.class.php");
require_once(PEGASAAS_ACCELERATOR_DIR."_inc/classes/pegasaas-cache.class.php");
require_once(PEGASAAS_ACCELERATOR_DIR."_inc/classes/pegasaas-api.class.php");
require_once(PEGASAAS_ACCELERATOR_DIR."_inc/classes/pegasaas-utils.class.php");
require_once(PEGASAAS_ACCELERATOR_DIR."_inc/classes/pegasaas-scanner.class.php");
require_once(PEGASAAS_ACCELERATOR_DIR."_inc/classes/pegasaas-db.class.php");
require_once(PEGASAAS_ACCELERATOR_DIR."_inc/classes/pegasaas-compatibility.class.php");
require_once(PEGASAAS_ACCELERATOR_DIR."_inc/classes/pegasaas-htaccess.class.php");
require_once(PEGASAAS_ACCELERATOR_DIR."_inc/classes/pegasaas-data-storage.class.php");
require_once(PEGASAAS_ACCELERATOR_DIR."_inc/classes/pegasaas-cron.class.php");


// mail sending
require_once(PEGASAAS_ACCELERATOR_DIR."_inc/classes/pegasaas-hermes.class.php");

require_once(PEGASAAS_ACCELERATOR_DIR."_inc/classes/pegasaas-optimize.class.php");
require_once(PEGASAAS_ACCELERATOR_DIR."_inc/classes/pegasaas-optimize-compatibility.class.php");
require_once(PEGASAAS_ACCELERATOR_DIR."_inc/classes/pegasaas-optimize-deferment.class.php");
require_once(PEGASAAS_ACCELERATOR_DIR."_inc/classes/pegasaas-optimize-images.class.php");
require_once(PEGASAAS_ACCELERATOR_DIR."_inc/classes/pegasaas-optimize-lazyload.class.php");
require_once(PEGASAAS_ACCELERATOR_DIR."_inc/classes/pegasaas-optimize-minify.class.php");
require_once(PEGASAAS_ACCELERATOR_DIR."_inc/classes/pegasaas-optimize-prefetch.class.php");

require_once(PEGASAAS_ACCELERATOR_DIR."_inc/classes/pegasaas-test.class.php");

?>