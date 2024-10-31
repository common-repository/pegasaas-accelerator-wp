<?php
$features = PegasaasAccelerator::$settings['settings'];
unset($features['response_rate']);
$feature_sections = array();

$feature_sections["General"] = array("speed_configuration",
									 "coverage",
									 "display_mode",
									 "display_level",
									 );

$feature_sections["Server Response Time"] = array(
												  "cdn",
												  "browser_caching",
												  );	



$feature_sections["HTML Resource Optimization"] = array("minify_html",
														"gzip_compression", 
														"auto_clear_page_cache"
												  );

$feature_sections["Image Resource Optimization"] = array(
												   "basic_image_optimization",
												   "image_optimization",
												   "external_image_optimization",
												   "webp_images",
												   "auto_size_images"

												  );


$feature_sections["Javascript Resource Delivery"] = array("defer_render_blocking_js", 
														  "combine_js",
														  "minify_js",
														  "preload_scripts");

$feature_sections["CSS Resource Delivery"] = array(
											   "defer_render_blocking_css",
											   "defer_unused_css",
											   "inject_critical_css",
											   "essential_css",
											   "combine_css", 
											   "minify_css" 
											   );

$feature_sections["Font Resource Delivery"] = array(
											  "preload_web_fonts",
											  "enable_default_font_display",
											  "google_fonts",
											  "strip_google_fonts_mobile",	
											  
											  );
$feature_sections["Misc Resource Delivery"] =  array(
												  "dns_prefetch",
												  "preload_user_files"
												);



/*************************** LAZY LOADING *********************************/
$feature_sections["Lazy Loading"] = array(
										  "lazy_load_images", 
										  "lazy_load_background_images", 
										  "lazy_load",
										  "lazy_load_google_ads",
										  "lazy_load_google_maps",
										  "lazy_load_youtube",
										  "lazy_load_vimeo",
										  "lazy_load_twitter_feed",
										  "lazy_load_facebook_feed",
										  "lazy_load_scripts",
										  "lazy_load_html",
											"lazy_load_plugin_scripts");

$third_party_vendor_scripts = get_option("pegasaas_third_party_vendor_scripts");

$lazy_load_third_party_vendor_scripts = array();

if (isset($third_party_vendor_scripts["ampproject"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_ampproject";
}	

if (isset($third_party_vendor_scripts["anyclip"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_anyclip";
}	

if (isset($third_party_vendor_scripts["bibblio"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_bibblio";
}			


if (isset($third_party_vendor_scripts["callrail"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_callrail";
}		

if (isset($third_party_vendor_scripts["capsumo"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_capsumo";
}	

if (isset($third_party_vendor_scripts["clicky_analytics"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_clicky_analytics";
}		
if (isset($third_party_vendor_scripts["clickcease"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_clickcease";
}	

if (isset($third_party_vendor_scripts["convertbox"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_convertbox";
}	

if (isset($third_party_vendor_scripts["crazyegg"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_crazyegg";
}						

if (isset($third_party_vendor_scripts["driftt"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_driftt";
}	

if (isset($third_party_vendor_scripts["facebook_pixel"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_facebook_pixel";
}				

if (isset($third_party_vendor_scripts["facebook_sdk"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_facebook_sdk";
}	

if (isset($third_party_vendor_scripts["geobid_pixel"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_geobid_pixel";
}				
if (isset($third_party_vendor_scripts["google_analytics"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_google_analytics";
}
if (isset($third_party_vendor_scripts["google_maps"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_google_maps";
}					
if (isset($third_party_vendor_scripts["google_tag_manager"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_google_tag_manager";
}

if (isset($third_party_vendor_scripts["hotjar"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_hotjar";
}
if (isset($third_party_vendor_scripts["hubspot"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_hubspot";
}				
if (isset($third_party_vendor_scripts["intercom"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_intercom";
}	
if (isset($third_party_vendor_scripts["live_chat"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_live_chat";
}					
if (isset($third_party_vendor_scripts["lucky_orange"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_lucky_orange";
}

if (isset($third_party_vendor_scripts["media_net"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_media_net";
}	

if (isset($third_party_vendor_scripts["ngageics"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_ngageics";
}			

if (isset($third_party_vendor_scripts["olark"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_olark";
}		

if (isset($third_party_vendor_scripts["oribi_analytics"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_oribi_analytics";
}	

if (isset($third_party_vendor_scripts["privy"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_privy";
}

if (isset($third_party_vendor_scripts["pubguru"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_pubguru";
}

if (isset($third_party_vendor_scripts["pushcrew"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_pushcrew";
}			
if (isset($third_party_vendor_scripts["segmentio"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_segmentio";
}		

if (isset($third_party_vendor_scripts["social_warfare"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_social_warfare";
}	

if (isset($third_party_vendor_scripts["subscribers"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_subscribers";
}	


if (isset($third_party_vendor_scripts["sumo"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_sumo";
}	

if (isset($third_party_vendor_scripts["tawk"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_tawk";
}	

if (isset($third_party_vendor_scripts["tidio_chat"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_tidio_chat";
}	


if (isset($third_party_vendor_scripts["trustarc"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_trustarc";
}	

if (isset($third_party_vendor_scripts["wordpress_emoji"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_wordpress_emoji";
}	
if (isset($third_party_vendor_scripts["wordpress_site_stats"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_wordpress_site_status";
}	

if (isset($third_party_vendor_scripts["yandex"])) {
	$lazy_load_third_party_vendor_scripts[] =  "lazy_load_yandex";
}		

if (sizeof($lazy_load_third_party_vendor_scripts) > 0) {
	$feature_sections["Lazy Loading"][] = "lazy_load_third_party_vendor_scripts";
	$features["lazy_load_third_party_vendor_scripts"] = array("name" => "Third Party Vendor Scripts", "since" => "3.0.0", "status" => 1);
}










/*************************** COMPATIBILITY *********************************/

$feature_sections["Compatibility"][] = "cloudflare";
$feature_sections["Compatibility"][] = "varnish";

if (isset($features["wordlift"])) {
	$feature_sections["Compatibility"][] = "wordlift";
}
if (isset($features["themify"])) {
	$feature_sections["Compatibility"][] = "themify";
}
if (isset($features["thrive"])) {
	$feature_sections["Compatibility"][] = "thrive";
}				
if (isset($features["avada"])) {
	$feature_sections["Compatibility"][] = "avada";
}
if (isset($features["woocommerce"])) {
	$feature_sections["Compatibility"][] = "woocommerce";
}
if (isset($features["wpx_cloud"])) {
	$feature_sections["Compatibility"][] = "wpx_cloud";
}

if (isset($features["elementor_compatibility"])) {
	$feature_sections["Compatibility"][] = "elementor_compatibility";
}



if ($pegasaas->utils->does_plugin_exists_and_active("accelerated-mobile-pages") || 
	$pegasaas->utils->does_plugin_exists_and_active("accelerated-moblie-pages")) {
	$feature_sections["Compatibility"][] = "accelerated_mobile_pages";
}

/*************************** MISCELLANEOUS *********************************/
$feature_sections["Miscellaneous"] = array("logging",  
										   "promote",
										   "exclude_urls", 
										   "auto_crawl",
										   "blog",
										   "dynamic_urls",
										   "multi_server",
										   "database_connection_refresh",
										   "api_submit_method"
										  );
if (get_option("pegasaas_accelerator_ignore_ssl_warning", 0) == 1) {
	$feature_sections["Miscellaneous"][] = "ssl_warning_override";
}

		





/*************************** REQUIRES CACHING *********************************/

$requires_cache_clearing = array("minify_html", 
								 "combine_css",
								 "combine_js",
								 "enable_default_font_display", 
								 "basic_image_optimization", 
								 "image_optimization",
								 "auto_size_images", 
								 "defer_render_blocking_js",
								 "proxy_external_js",
								 "defer_render_blocking_css",
								 "inject_critical_css",
								 "defer_unused_css",
								 "essential_css",
								 "proxy_external_css",
								"dns_prefetch", 
								"preload_user_files",
								"preload_fav_icons",
								"google_fonts", 
								"strip_google_fonts_mobile",
								"lazy_load_images", 
								"lazy_load_background_images", 
								"lazy_load",
								"lazy_load_youtube",
								"lazy_load_google_ads",
								"lazy_load_twitter_feed",
								"lazy_load_scripts",
								"thrive",
								"themify",
								"wordlift",
								"preload_web_fonts",
								"preload_scripts");



/*************************** LOCKED FOR NOVICE *********************************/
$locked_for_novice = array("gzip_compression", 
						   "browser_caching",
						   "minify_js", 
						   "combine_css",
						   "combine_js",
						   "enable_default_font_display", 
						   "minify_css", 
						   "defer_render_blocking_js", 
						   "defer_render_blocking_css",
						   "proxy_external_js",
						   "proxy_external_css",
						   "inject_critical_css",
						  "essential_css",
						  "defer_unused_css",
						  "dns_prefetch", 
						   "preload_individual_fils", 
						   "preload_fav_icons",
						  "google_fonts",
						  "strip_google_fonts_for_mobile",
						  "lazy_load_images", 
						  "lazy_load_background_images",
						  "lazy_load",
						   "lazy_load_youtube",
						   "lazy_load_twitter_feed",
						   "lazy_load_scripts",
						   "preload_user_files",
						   "strip_google_fonts_mobile"
						  );

$manual_only_configuration = array(
	"General Resource Optimization",
	"Image Resource Optimization",
	"Javascript Resource Delivery",
	"CSS Resource Delivery",
	"Misc Resource Delivery",
	"Server Response Time",
	"Lazy Loading",
	"minify_html", 
								   "image_optimization",
								   "basic_image_optimization",
								   "external_image_optimization",
								   "webp_images",
								   "auto_size_images",
									"cdn",
								   "gzip_compression", 
								   "lazy_load_google_ads",
								   "lazy_load_vimeo",
								   "lazy_load_facebook_feed",
								   "third_party_vendor_scripts",
						   "browser_caching",
						   "minify_js", 
						   "combine_css",
						   "combine_js",
						   "enable_default_font_display", 
						   "minify_css", 
						   "defer_render_blocking_js", 
						   "defer_render_blocking_css",
						   "proxy_external_js",
						   "proxy_external_css",
						   "inject_critical_css",
						  "essential_css",
						  "defer_unused_css",
						  "dns_prefetch", 
						   "preload_individual_fils", 
						   "preload_fav_icons",
						  "google_fonts",
						  "strip_google_fonts_for_mobile",
						  "lazy_load_images", 
						  "lazy_load_background_images",
						  "lazy_load",
						   "lazy_load_youtube",
						   "lazy_load_twitter_feed",
						   "lazy_load_scripts",
						   "preload_user_files",
						   "strip_google_fonts_mobile",
						   "preload_web_fonts",
						   "preload_scripts"
						  );



$foundation_features = array("preload_user_files",
							"google_fonts",
							
							
							"dns_prefetch",
							"minify_css",
							"minify_js",
							"dns_prefetch",
							"wordlift",
							"lazy_load",
							 "lazy_load_images",
							"essential_css",
							"inject_critical_css",
							"defer_render_blocking_js",
							"defer_render_blocking_css",
							"image_optimization",
							 "basic_image_optimization",
							 "minify_html",
							 "gzip_compression",
							 "enable_default_font_display",
							 
							 
							 "blog_settings",
							 "auto_clear_page_cache",
							 "browser_caching"
							);

$api_features = array("external_image_optimization",
						"webp_images",
					   	"cdn",
						  "auto_size_images",
						 "combine_js",
						 "combine_css",
						 "defer_unused_css",
						 "preload_fav_icons",
						 "strip_google_fonts_mobile",
					  	"lazy_load_google_ads",
						 "lazy_load_background_images",
						 "lazy_load_vimeo",
						 "lazy_load_youtube",
						 "lazy_load_twitter_feed",
						 "lazy_load_facebook_feed",
						 "lazy_load_scripts",
					    "lazy_load_google_maps",
						 "lazy_load_third_party_vendor_scripts", 
					 "lazy_load_html",
					 "lazy_load_plugin_scripts");



$no_toggle_features = array("blog",
							"display_mode",
							"display_level",
							"speed_configuration",
							"coverage",
							"blog",
							"updater",
							"ssl_warning_override",
							"api_submit_method",
							"lazy_load_third_party_vendor_scripts"
						   );
?>