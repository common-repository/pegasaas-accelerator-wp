<?php
class PegasaasLazyLoad {
	
	
	static function lazy_load_iframes($buffer) {
		$debug = $_GET['pegasaas_debug'] == "lazy-load";
		
		$pattern 		= "/<iframe(.*?)>(.*?)<\/iframe>/si";
		$matches 		= array();
    	preg_match_all($pattern, $buffer, $matches);
		foreach ($matches[0] as $find) {
			$replace = str_replace(" src", " data-src", $find);
			if (strstr($replace, "class=")) {
				$replace = str_replace("class='", "class='pegasaas-lazy-loaded-iframe ", $replace);
				$replace = str_replace('class="', 'class="pegasaas-lazy-loaded-iframe ', $replace);
			} else {
				$replace = str_replace(" data-src", " class='pegasaas-lazy-loaded-iframe' data-src", $replace);
			}
			$buffer = str_replace($find, $replace, $buffer);
		}
	
		
		
		$lazy_load_iframe_js = "'use strict';
		
		function pegasaas_preload_iframe(iframe) {
			
					if(iframe.getAttribute('data-src')) {
						iframe.setAttribute('src', iframe.getAttribute('data-src'));
						iframe.removeAttribute('data-src' );
					}
						
		
		}
		
		const pegasaas_lazy_loaded_iframes = document.querySelectorAll('.pegasaas-lazy-loaded-iframe');
		const config_iframe = {
  			// If the image gets within 50px in the Y axis, start the download.
  			rootMargin: '50px 0px',
  			threshold: 0.01
			};

		
		if (!('IntersectionObserver' in window)) {
			for (var index = 0; index < pegasaas_lazy_loaded_iframes.length; index++) {
				pegasaas_preload_iframe(pegasaas_lazy_loaded_iframes[index]);
			}

		} else {
		// Get all of the images that are marked up to lazy load
		
		function onIFrameIntersection(entries) {
  			// Loop through the entries
			for (var index = 0; index < entries.length; index++) {
			var entry = entries[index];
			
				//pegasaas_preload_image(pegasaas_lazy_loaded_iframes[index]);
				if (entry.intersectionRatio > 0) {

      				// Stop watching and load the iframe
      				iframe_observer.unobserve(entry.target);
					pegasaas_preload_iframe(entry.target);				

    			}
			}

		}
		
		// The observer for the iframe on the page
		let iframe_observer = new IntersectionObserver(onIFrameIntersection, config_iframe);
		
  		pegasaas_lazy_loaded_iframes.forEach(function(iframe) {
    			iframe_observer.observe(iframe);
  			});

		}
		
		";
		$lazy_load_iframe_js = PegasaasMinify::minify_js($lazy_load_iframe_js);
		$lazy_load_iframe_js = "<script>/* pegasaas-inline */ {$lazy_load_iframe_js}</script>";
	
		$buffer = str_replace("</body>", $lazy_load_iframe_js."</body>", $buffer);
		return $buffer;
	}

	static function lazy_load_images($buffer) {
		$debug = $_GET['pegasaas_debug'] == "lazy-load";
		
		$excluded_images = explode("\n", trim(str_replace("\r", "", PegasaasAccelerator::$settings['settings']['lazy_load_images']['exclude_images'])));
		$src_pattern 		= "/src=['\"](.*?)['\"]/si";
		$pattern 		= "/<img(.*?)>/si";
		$matches 		= array();
    	preg_match_all($pattern, $buffer, $matches);
		foreach ($matches[0] as $find) {
			$match_src = array();
			preg_match($src_pattern, $find, $match_src);
			$src 		= $match_src[1];

			if (strstr($find, " data-src")) {
				continue;
			}
			if (strstr($find, " loading=")) {
				continue;
			}


			if (PegasaasUtils::is_in_list($src, $excluded_images)) {
				continue;	
			}

			$replace = str_replace("<img ", "<img loading='lazy' ", $find);

			$buffer = str_replace($find, $replace, $buffer);
		}
	
	
	
		$buffer = str_replace("</body>", $lazy_load_iframe_js."</body>", $buffer);
		return $buffer;
	}	
}