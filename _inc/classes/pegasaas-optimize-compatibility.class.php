<?php
class PegasaasCompatibility {
	static function apply_wordlift_conditioning($buffer) {
		global $pegasaas;
		
		// add a preload to the HTML to load the schema ASAP
		$script_matches	= array();
		$link_matches 	= array();
		$script_pattern = "/<script(.*?)>(.*?)<\/script>/si";
		$wlapi_pattern 	= "/\"apiUrl\":\"(.*?)\"/si";
		$settings_json_pattern 	= "/var wlSettings = (.*?);/";
		

		// we are currently not using this, as we need to add in functionality to the cdn
		// to handle an alternate path to the wl-api, as the cdn cannot accept query string arguments
		$script_matches	= array();
		$link_matches 	= array();
		$script_pattern = "/<script(.*?)>(.*?)<\/script>/si";
		$wlapi_pattern 	= "/\"apiUrl\":\"(.*?)\"/si";
		
		$cdn 			= PegasaasAccelerator::$settings['settings']['cdn']['status'] == 1;
		$cache_server 	= PegasaasCache::get_cache_server()."wl-api/";
		$cache_server_escaped = str_replace('/', '\\/', $cache_server); 
		
		
		
			preg_match_all($script_pattern, $buffer, $script_matches);

			// iterate through all of the script blocks
			foreach ($script_matches[0] as $index => $script_block) {
				$settings_matches = array();

				// if this is a themify code block, then extract the link
				if (strstr($script_block, "var wlSettings")) {
					// extract apiURL argument
					
					preg_match($settings_json_pattern, $script_block, $settings_matches);
					$wl_data = json_decode($settings_matches[1], true);
					$wl_api_url = $wl_data['apiUrl']."?action=wl_jsonld";
					$wl_api_url .= "&id=".$wl_data['postId'];
					if ($wl_data['isHome'] == "1") {
						$wl_api_url .= "&homepage=true";
					}
					$data = $pegasaas->utils->get_file($wl_api_url);
					// inject the schema directly into the page
					$buffer = str_replace("</head>", "<script type='application/ld+json'>{$data}</script></head>", $buffer);
					
					// remove the old data from the page
					$buffer = str_replace($script_block, "", $buffer);
					//$buffer = str_replace("</head>", "<link as='script' rel='preload' href='{$wl_api_url}'></head>", $buffer);
				} else if (strstr($script_block, "wordlift/js/dist/bundle.js") !== false) {
					$buffer = str_replace($script_block, "", $buffer);
				} else if (strstr($script_block, "?action=wl_jsonld") !== false) {
					// strip the api call from the page
					$buffer = str_replace($script_block, "", $buffer);
				}
			}
		
		return $buffer;
	}
	
	static function apply_thrive_compatibility($buffer) {
		$matches = array();
		// identify <div> with class "tcb-window-width" and strip out style width and style height
		$pattern = '/<div[^>]*?tcb-window-width[^>]*?style="(.*?)"/';
		preg_match_all($pattern, $buffer, $matches);
		foreach ($matches[0] as $index => $match_block) {
			
			
			$replacement = str_replace($matches[1][$index], "", $match_block);
		//	$replacement = str_replace("tcb-window-width", "", $replacement);
		
			$buffer = str_replace($match_block, $replacement, $buffer);
		}
		
		return $buffer;
	}
	
}