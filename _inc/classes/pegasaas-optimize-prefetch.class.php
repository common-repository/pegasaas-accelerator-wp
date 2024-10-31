<?php
class PegasaasPrefetch {
	static function get_dns_prefetch_domains($page_html) {
		$prefetch_domain = array();

		$pattern = "/<script(.*?)<\/script>/si";
    	preg_match_all($pattern, $page_html, $matches);
		
		$src_pattern = "/src=['\"](.*?)['\"]/si";

		foreach ($matches[0] as $find) {
	  		$match_src = array();
	  		preg_match($src_pattern, $find, $match_src);
	  		$src = $match_src[1];
			if (strstr($src, "//") && !strstr($src, $_SERVER['HTTP_HOST'])) {
				$domain_pattern = "/(http:\/\/|https:\/\/|\/\/)(.*?)\//";
				preg_match($domain_pattern, $src, $domain_match);
			
				$domain = rtrim($domain_match[0],"/");
				$prefetch_domain["{$domain}"] = true;
			
			}
			
		}
	
		$pattern = "/<img(.*?)>/si";
		$matches = array();
    	preg_match_all($pattern, $page_html, $matches);
		$src_pattern = "/src=['\"](.*?)['\"]/si";

		foreach ($matches[0] as $find) {
	  
	  		$match_src = array();
	  		preg_match($src_pattern, $find, $match_src);
	  		$src = $match_src[1];
			if (strstr($src, "//") && !strstr($src, $_SERVER['HTTP_HOST'])) {
				$domain_pattern = "/(http:\/\/|https:\/\/|\/\/)(.*?)\//";
				preg_match($domain_pattern, $src, $domain_match);
			
			
				$domain = rtrim($domain_match[0],"/");
				$prefetch_domain["{$domain}"] = true;
			
			}


		}
 
 		$pattern = "/<link(.*?)>/si";
		$matches = array();
    	preg_match_all($pattern, $page_html, $matches);
		$src_pattern = "/href=['\"](.*?)['\"]/si";

		foreach ($matches[0] as $find) {  
	  		$match_src = array();
	  		preg_match($src_pattern, $find, $match_src);
	  		$src = $match_src[1];
			if (strstr($src, "//") && !strstr($src, $_SERVER['HTTP_HOST'])) {
				$domain_pattern = "/(http:\/\/|https:\/\/|\/\/)(.*?)\//";
				preg_match($domain_pattern, $src, $domain_match);
				$domain = rtrim($domain_match[0],"/");
				$prefetch_domain["{$domain}"] = true;
			}
		}
		return $prefetch_domain;
	}

	static function apply_dns_prefetch($page_html, $prefetch_domain) {
		global $pegasaas;
			
		$additional_domains = explode("\n", trim(str_replace("\r", "", PegasaasAccelerator::$settings['settings']['dns_prefetch']['additional_domains'])));
		foreach ($additional_domains as $domain) {
			if ($domain != "") { 
			$prefetch_domain["$domain"] = true;
			}
		}
		
		
		$page_html = preg_replace("/\<link rel=\"alternate\"/", "<!-- accelerator dns prefetch begin -->\n<!-- accelerator dns prefetch end -->\n<link rel=\"alternate\"", $page_html, 1);
		
		
		if (is_array($prefetch_domain)) {
			foreach ($prefetch_domain as $domain => $status) {
				
				if (PegasaasAccelerator::$settings['settings']['dns_prefetch']['status'] == 3) {
				  $dns_prefetch = "<link rel='preconnect' href='{$domain}' />";
				} else {
				   $dns_prefetch = "<link rel='dns-prefetch' href='{$domain}' />";
				}
				
				if ($domain != "" && strpos($page_html, $dns_prefetch) === false) { 
				  $page_html = str_replace("<!-- accelerator dns prefetch end -->", "{$dns_prefetch}\n<!-- accelerator dns prefetch end -->", $page_html);
				}
			}	
		}
				
		return $page_html;
		
	}
	

	
	
}