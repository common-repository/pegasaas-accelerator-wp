<?php
class PegasaasHtaccess {
	function __construct() {}
	
	static function get_caching_instructions() {
		global $pegasaas;
		
		// this will provide a relative path from the root to the content folder (without trailing slash)
		$content_folder = $pegasaas->utils->get_content_folder_path();
		$home_domain 	= $pegasaas->utils->get_home_domain();

		$redirect_to_primary_domain = "";
		$https_site 				= false;
		$wp_location				 = $pegasaas->utils->get_wp_location();
		$rewrite_base = rtrim($wp_location)."/";
		$wpml_multi_domains_active = PegasaasUtils::wpml_multi_domains_active();
		
	/* 
	RewriteCond %{HTTPS} =on
   
    RewriteCond %{SERVER_PORT} =443
    
    RewriteCond %{HTTP:X-Forwarded-Proto} =https [NC]
    
	*/
	
			
		// do not push to HTTPS if the home url is not https
		// also do not assert to the home domain if this is multi-site, as it may not correspond to other installations
		if (strstr($pegasaas->get_home_url(), "https://") && !is_multisite() && !$wpml_multi_domains_active) {
			$https_site = true;
			
			// handle servers that use %{HTTP:X-Forwarded-Proto} instead of %{HTTPS}
			if ($_SERVER['HTTP_X_FORWARDED_PROTO'] != "") {
				$redirect_to_primary_domain .= "  RewriteCond %{HTTP:X-Forwarded-Proto} !https\n".
							"  RewriteRule (.*) https://%{SERVER_NAME}%{REQUEST_URI} [L,R=301]\n";		
		
			
			// handle servers that use PAGELY hosting
			} else if ($_SERVER['HTTP_X_PAGELY_SSL'] != "") {
				$redirect_to_primary_domain .= "  RewriteCond %{HTTP:X-Pagely-SSL} \"off\"\n".
					"  RewriteRule (.*) https://{$home_domain}{$rewrite_base}\$1 [L,R=301]\n";		
			
			} else {
				$redirect_to_primary_domain .= "  RewriteCond %{HTTPS} !on\n".
					"  RewriteRule (.*) https://{$home_domain}{$rewrite_base}\$1 [L,R=301]\n";				
			}
		
		}
		
		// do not assert to the home domain if this is multi-site, as it may not correspond to other installations
		if (!is_multisite() && !$wpml_multi_domains_active) {
			$redirect_to_primary_domain .= "  RewriteCond %{HTTP_HOST} !^{$home_domain}$\n".
			"  RewriteRule (.*) ".($https_site ? "https" : "http")."://{$home_domain}/\$1 [L,R=301]\n\n";
		}
		
		$version = $pegasaas->get_current_version();
		
		
		

		
		$code_block =  "# PEGASAAS ACCELERATOR PAGE CACHING START\n".
			"<IfModule mod_rewrite.c>\n".
			"  RewriteEngine On\n". 
			"  RewriteBase {$rewrite_base}\n".
			"{$redirect_to_primary_domain}";
		
		
		if (PegasaasAccelerator::$settings['settings']['wpx_cloud']['status'] == 1 && PegasaasUtils::is_wpx_server()) {
			$code_block .= "\n  # Detect whether WPX Cloud should be enabled\n".
			"  RewriteCond %{HTTP:COOKIE} !wordpress_logged_in_\n".
			"  RewriteCond %{REQUEST_URI} !wp-admin\n".
			"  RewriteCond %{REQUEST_URI} !wp-login\n".
			"  RewriteRule ^.*$ - [E=PEGASAAS_ALLOW_CACHE_CONTROL:true]\n\n";
		}
		
		if ($wpml_multi_domains_active) {
			$code_block .= self::build_caching_instructions("webperf", true, $home_domain, true); // webperf baseline scan cache file, primary domain
			$code_block .= self::build_caching_instructions("temp", true, $home_domain, true); // temp caching file, primary domain
			$code_block .= self::build_caching_instructions("", true, $home_domain, true); // standard caching file, primary domain
			$domains = PegasaasUtils::get_wpml_domains();
			foreach ($domains as $lang => $domain) {
				$code_block .= self::build_caching_instructions("webperf", true, $domain, false); // webperf baseline scan cache file, for specified domain
				$code_block .= self::build_caching_instructions("temp", true, $domain, false); // temp caching file, for specified domain
				$code_block .= self::build_caching_instructions("", true, $domain, false); // standard caching file, for specified domain
			}
			
		} else {
			$code_block .= self::build_caching_instructions("webperf");  // webperf baseline scan cache file
			$code_block .= self::build_caching_instructions("temp");  // temp caching file
			$code_block .= self::build_caching_instructions(""); // standard caching file
		}

			
		$code_block .= "  <IfModule mod_setenvif.c>\n".
			"    # set an environment variable that says this is a cache hit\n".
			"    SetEnvIf Request_URI pegasaas-cache PEGASAAS_CACHE_HIT=true\n".
			"    SetEnvIf Request_URI \"index\\.html\$\" CONTENT_TYPE_HTML=true\n".
			"    SetEnvIf Request_URI \"index-temp\\.html\$\" PEGASAAS_TEMP_CACHE_HIT=true\n".
			"    SetEnvIf Request_URI \"index-original\\.html\$\" PEGASAAS_ORIGINAL_CACHE_HIT=true\n".
			"    SetEnvIf PEGASAAS_CACHE_HIT false CONTENT_TYPE_HTML=false\n\n".		
			"    <IfModule mod_headers.c>\n".
			"      # set the header to indicate whether this is a cache hit\n".
			"      Header set \"X-Pegasaas-Cache\" \"MISS\" env=!PEGASAAS_CACHE_HIT&&!PEGASAAS_TEMP_CACHE_HIT\n".
			"      Header set \"X-Pegasaas-Cache\" \"HIT\" env=PEGASAAS_CACHE_HIT\n".
			"      Header set \"X-Pegasaas-Cache\" \"TEMPORARY\" env=PEGASAAS_TEMP_CACHE_HIT\n".
			"      Header set \"X-Pegasaas-Cache\" \"ORIGINAL\" env=PEGASAAS_ORIGINAL_CACHE_HIT\n".
			"    </IfModule>\n".
			"  </IfModule>\n\n".
			"  <IfModule mod_headers.c>\n";
		
		if (PegasaasAccelerator::$settings['settings']['wpx_cloud']['status'] == 1 && PegasaasUtils::is_wpx_server()) {
			$code_block .= "    # ensure WPX Cloud caching is enable\n".
			"    Header set \"Cache-Control\" \"max-age=300, public\" env=PEGASAAS_ALLOW_CACHE_CONTROL\n\n";
		} else {
			$code_block .= "    # ensure some server level caching is disabled\n".
			"    Header set \"Cache-Control\" \"private, max-age=0, no-cache\"\n\n";

		}
		
		
			$code_block .= 

			"    # set the powered-by header\n".
			"    Header set \"X-Powered-By\" \"Pegasaas Accelerator WP {$version}\"\n\n".
			
			"    # set the header to indicate whether this is a cache hit\n".
			"    Header set \"Content-Type\" \"text/html; charset=UTF-8\" env=CONTENT_TYPE_HTML\n".
			"  </IfModule>\n\n".
			
			"  # instructions to redirect from viewing the HTML cache file directly\n".
			"  RewriteCond %{REQUEST_FILENAME} pegasaas-cache\n".
			"  RewriteCond %{REQUEST_FILENAME} index.html\n".
			"  RewriteCond %{QUERY_STRING} !.+\n".
			"  RewriteCond %{HTTPS} on\n".
			"  RewriteRule (.*?)wp-content/pegasaas-cache/(.*?)index.html https://%{HTTP_HOST}/$1$2 [L,R=301]\n\n".
			"  RewriteCond %{REQUEST_FILENAME} pegasaas-cache\n".
			"  RewriteCond %{REQUEST_FILENAME} index.html\n".
			"  RewriteCond %{QUERY_STRING} !.+\n".
			"  RewriteCond %{HTTPS} !on\n".
			"  RewriteRule (.*?)wp-content/pegasaas-cache/(.*?)index.html http://%{HTTP_HOST}/$1$2 [L,R=301]\n".
			"</IfModule>\n".
			"# PEGASAAS ACCELERATOR PAGE CACHING END\n\n";
		
		return $code_block;
	}
	
	
	function build_time_rewrite_instruction() {
		global $pegasaas;
		
		if ($pegasaas->in_development_mode()) {
			$development_mode_expiry = get_option("pegasaas_development_mode", 0); // will be a time-date stamp, or -1
			if ($development_mode_expiry == -1) {
				return "# PEGASAAS ACCELERATOR PAGE CACHING START\n".
					"  # In Infinite Staging Mode\n".
					"# PEGASAAS ACCELERATOR PAGE CACHING END\n\n"; // we are indefinitely in staging mode, so we will not use the quick file-based caching
			} else {
				$time_string = date("YmdHis", $development_mode_expiry);
				$time_rewrite = "  RewriteCond %{TIME} >{$time_string} \n";
			}

		} else {
			$time_rewrite = "  # not in development mode\n";
		}
		
		return $time_rewrite;
	}
	
	function build_caching_instructions($file_type = "", $multi_domains_active = false, $domain = "", $is_primary_domain = false) {
		global $pegasaas;
		
		$is_temp_file = $file_type == "temp";
		$is_webperf_file = $file_type == "webperf";
		if ($is_temp_file) {
			$file_modifier = "-temp";
			$environment_cache_variable = "PEGASAAS_TEMP_CACHE_HIT";
		} else if ($is_webperf_file) {
			$file_modifier = "-original";
			$environment_cache_variable = "PEGASAAS_ORIGINAL_CACHE_HIT";
		} else {
			$file_modifier = "";
			$environment_cache_variable = "PEGASAAS_CACHE_HIT";
		}
		
		
		$content_folder = $pegasaas->utils->get_content_folder_path();

		$instructions = "";
		
		// inject instructions for new web-perf-scan target
		if ($is_webperf_file) {
			$instructions .= 		
			"  # WebPerf Baseline Scan Cache\n";
				

			
		} else if ($is_temp_file) {
				$instructions .= "  # Temporary Cache Handling\n";
		}

		if ($is_webperf_file) {
			$instructions .= 
			"  RewriteCond %{QUERY_STRING} web-perf-baseline-scan\n";
		} else {
			$instructions .= 		
		
			"  RewriteCond %{HTTP:Cookie} !wordpress_logged_in_\n".
			"  RewriteCond %{REQUEST_METHOD} !POST\n".
			"  RewriteCond %{QUERY_STRING} !.+ [OR]\n".
			"  RewriteCond %{QUERY_STRING} gpsi-call\n".
			"  RewriteCond %{HTTP:X-Pegasaas-Bypass-Cache} !1\n".
			"  RewriteCond %{HTTP:Cookie} !comment_author_\n".
			"  RewriteCond %{HTTP:Cookie} !wp-postpass\n".
			"  RewriteCond %{HTTP:Cookie} !wp_woocommerce_session\n".
			"  RewriteCond %{HTTP:Cookie} !wpsc_customer_cookie\n".
			"  RewriteCond %{HTTP:Cookie} !edd_items_in_cart\n";
		}
		
		if ($multi_domains_active) {
			$instructions .= 	"  RewriteCond %{HTTP_HOST} ^{$domain}$\n";
		}		
		
		if (!$is_webperf_file) {
			$instructions .= self::build_time_rewrite_instruction();
		
			if (PegasaasAccelerator::$settings['settings']['accelerated_mobile_pages']['status'] === 1) {
				$instructions .= "  RewriteCond %{HTTP_USER_AGENT} '!(android|blackberry|googlebot-mobile|iemobile|ipad|iphone|ipod|opera mobile|palmos|webos)' [NC]\n".
					"  RewriteCond %{REQUEST_URI} !amp/$\n";
			}
		}
		
		
		
		// when using trailing slashes, the path must be /$1index.html as the $1 will already include a slash
		if (PegasaasUtils::permalinks_use_trailing_slashes()) {
			if ($multi_domains_active && (!$is_primary_domain || PegasaasUtils::uses_domain_specific_handling())) {
				$instructions .= "  RewriteCond \"%{DOCUMENT_ROOT}{$content_folder}/pegasaas-cache/{$domain}/\$1index{$file_modifier}.html\" -f [or]\n".
					"  RewriteCond \"".PEGASAAS_CACHE_FOLDER_PATH."/{$domain}/\$1index{$file_modifier}.html\" -f\n".
					"  RewriteRule ^(.*?)$ \"{$content_folder}/pegasaas-cache/{$domain}/\$1index{$file_modifier}.html\" [L]\n\n";			

			} else {
				$instructions .=	"  RewriteCond \"%{DOCUMENT_ROOT}{$content_folder}/pegasaas-cache/\$1index{$file_modifier}.html\" -f [or]\n".
					"  RewriteCond \"".PEGASAAS_CACHE_FOLDER_PATH."/\$1index{$file_modifier}.html\" -f\n".
					"  RewriteRule ^(.*?)$ \"{$content_folder}/pegasaas-cache/\$1index{$file_modifier}.html\" [L,E={$environment_cache_variable}:true]\n\n";
			}				
			
		// when not using trailing slashes, the path must be /$1/index.html as the $1 will not include a trailing slash
		} else {
			if ($multi_domains_active && (!$is_primary_domain || PegasaasUtils::uses_domain_specific_handling())) {
				$instructions .= "  RewriteCond \"%{DOCUMENT_ROOT}{$content_folder}/pegasaas-cache/{$domain}/\$1/index{$file_modifier}.html\" -f [or]\n".
					"  RewriteCond \"".PEGASAAS_CACHE_FOLDER_PATH."/{$domain}/\$1/index{$file_modifier}.html\" -f\n".
					"  RewriteRule ^(.*?[^/])$ \"{$content_folder}/pegasaas-cache/{$domain}/\$1/index{$file_modifier}.html\" [L]\n\n";			

			} else {
				$instructions .=	"  RewriteCond \"%{DOCUMENT_ROOT}{$content_folder}/pegasaas-cache/\$1/index{$file_modifier}.html\" -f [or]\n".
					"  RewriteCond \"".PEGASAAS_CACHE_FOLDER_PATH."/\$1/index{$file_modifier}.html\" -f\n".
					"  RewriteRule ^(.*?[^/])$ \"{$content_folder}/pegasaas-cache/\$1/index{$file_modifier}.html\" [L,E={$environment_cache_variable}:true]\n\n";
			}				
		}
		
	
		return $instructions;
	}
	
	
	
	static function assert_htaccess_conditioned() {
		global $pegasaas;
		
		
		// get .htaccess file
		$htaccess_location = $pegasaas->get_home_path().".htaccess";
		$htaccess_file = file($htaccess_location);
		$htaccess_file = implode("", $htaccess_file);
		$htaccess_original = $htaccess_file;
		
		// strip W3TC instructions
		if (strstr($htaccess_file, "# BEGIN W3TC") && strstr($htaccess_file, "# END W3TC")) {
			$pattern = "/# BEGIN W3TC(.*?)# END W3TC(.*?)\n/si";
			$htaccess_file = preg_replace($pattern, "", $htaccess_file);
			
		}
		
		// strip LBROWSERCACHE instructions
		if (strstr($htaccess_file, "# LBROWSERCSTART Browser Caching") && strstr($htaccess_file, "# END Caching LBROWSERCEND")) {
			$pattern = "/# LBROWSERCSTART Browser Caching(.*?)# END Caching LBROWSERCEND(.*?)\n/si";
			$htaccess_file = preg_replace($pattern, "", $htaccess_file);
			
		}	
		
		if ($htaccess_file != $htaccess_original) {
			if ($pegasaas->utils->is_htaccess_safe($htaccess_file, "PAGE CACHING ({$state})")) {
				$pegasaas->utils->write_file($htaccess_location.'--backup', $htaccess_original);
				$pegasaas->utils->write_file($htaccess_location, $htaccess_file);
			}
		} 
		
	}
	
}
//print "yes";
?>