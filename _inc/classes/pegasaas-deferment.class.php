<?php
if (!class_exists("PegasaasDeferment")) :
class PegasaasDeferment {
	
	function __construct() {
		
	}
	
	
	function clear_deferred_js() {
		global $pegasaas;
		
		$deferred_js_records = get_option('pegasaas_deferred_js', array());
		
		foreach ($deferred_js_records as $resource_id => $info) {	
			$resource_path = $pegasaas->utils->strip_query_string($resource_id);
			$path =  PEGASAAS_CACHE_FOLDER_PATH."{$resource_path}deferred-*.js";
			
			$pegasaas->cache->clear_cache_file($path);
					
			$pegasaas->utils->delete_object_meta($resource_id, "deferred_js");
		}
		
		delete_option("pegasaas_deferred_js");
	}	

	
}
endif;

?>