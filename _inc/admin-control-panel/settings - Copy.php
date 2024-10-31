		<div class='subscribed-features'>
			<!--<h3>Settings</h3>-->
			<div class='row'>
				<?php
				
			$features = $pegasaas->settings['settings'];
		
		
				
			$icons = array(); 
			$icons["page_caching"] = PEGASAAS_SETTING_PAGE_CACHING;	
			$icons["auto_clear_page_cache"] = PEGASAAS_SETTING_AUTO_CLEAR_PAGE_CACHE;	
			$icons["cloudflare"] = PEGASAAS_SETTING_PAGE_CLOUDFLARE;	
			$icons['gzip_compression'] = PEGASAAS_SETTING_GZIP_COMPRESSION;
			$icons['browser_caching'] = PEGASAAS_SETTING_BROWSER_CACHING;
			$icons['dns_prefetch'] = PEGASAAS_SETTING_DNS_PREFETCH;
			$icons['minify_html'] = PEGASAAS_SETTING_MINIFY_HTML;
			$icons['minify_css'] = PEGASAAS_SETTING_MINIFY_CSS;
			$icons['minify_js'] = PEGASAAS_SETTING_MINIFY_JAVASCRIPT;
			$icons['basic_image_optimization'] = PEGASAAS_SETTING_IMAGE_OPTIMIZATION;
			$icons['image_optimization'] = PEGASAAS_SETTING_IMAGE_OPTIMIZATION;
			$icons['webp_images'] = PEGASAAS_SETTING_IMAGE_OPTIMIZATION;
			$icons['auto_size_images'] = PEGASAAS_SETTING_IMAGE_OPTIMIZATION;
			$icons['external_image_optimization'] = PEGASAAS_SETTING_EXTERNAL_IMAGE_OPTIMIZATION;
			$icons['defer_render_blocking_js'] = PEGASAAS_SETTING_DEFER_RENDER_BLOCKING_JS;
			$icons['defer_render_blocking_css'] = PEGASAAS_SETTING_DEFER_RENDER_BLOCKING_CSS;
			$icons['defer_unused_css'] = PEGASAAS_SETTING_DEFER_RENDER_BLOCKING_CSS;
			$icons['inject_critical_css'] = PEGASAAS_SETTING_INJECT_CRITICAL_CSS;
			$icons['proxy_external_js'] = PEGASAAS_SETTING_PROXY_EXTERNAL_JS;
			$icons['proxy_external_css'] = PEGASAAS_SETTING_PROXY_EXTERNAL_CSS;
			$icons['cdn'] = PEGASAAS_SETTING_CDN;
			$icons['disable_wp_emoji'] = PEGASAAS_SETTING_LAZY_LOAD_TWITTER;
			$icons['lazy_load'] = PEGASAAS_SETTING_LAZY_LOAD_TWITTER;
			$icons['lazy_load_images'] = PEGASAAS_SETTING_LAZY_LOAD_TWITTER;
			$icons['lazy_load_background_images'] = PEGASAAS_SETTING_LAZY_LOAD_TWITTER;
			$icons['lazy_load_twitter_feed'] = PEGASAAS_SETTING_LAZY_LOAD_YOUTUBE;
			$icons['lazy_load_youtube'] = PEGASAAS_SETTING_LAZY_LOAD_YOUTUBE;
			$icons['lazy_load_scripts'] = PEGASAAS_SETTING_LAZY_LOAD_YOUTUBE;
			$icons['promote'] = PEGASAAS_SETTING_PROMOTE;		
			$icons['google_fonts'] = PEGASAAS_SETTING_GOOGLE_FONTS;		
			$icons['strip_google_fonts_mobile'] = PEGASAAS_SETTING_GOOGLE_FONTS;		
			$icons['preload_fav_icons'] = PEGASAAS_SETTING_FAV_ICONS;		
			$icons['logging'] = PEGASAAS_SETTING_LOGGING;		
			$icons['auto_crawl'] = PEGASAAS_SETTING_AUTO_CRAWL;		
			$icons['exclude_urls'] = PEGASAAS_SETTING_EXCLUDE_URLS;		
			$icons['display_mode'] = PEGASAAS_SETTING_DISPLAY_MODE;		
			$icons['varnish'] = PEGASAAS_SETTING_VARNISH;		
			$icons['blog'] = PEGASAAS_SETTING_BLOG;
			$icons['updater'] = "fa fa-fw fa-cloud-download";
			$icons['dynamic_urls'] = "fa fa-fw fa-code-fork";
			$icons['display_level'] = "fa fa-fw fa-universal-access";
				
			unset($features['response_rate']);
			$feature_sections = array();
				
			$feature_sections["General"] = array("display_mode",
												 "display_level");
				
			$feature_sections["Server Response Time"] = array(
															  "auto_clear_page_cache",
															  "browser_caching",
															  "cdn");	
			/* <!-- developer-edition component begin --> */
			if ($pegasaas->is_pro_edition()) {
				$feature_sections["Server Response Time"] = array("page_caching", 
																  "auto_clear_page_cache",
															  	  "browser_caching",
															      "cdn");
			}
			/* <!-- developer-edition component end --> */	

			
			$feature_sections["General Resource Optimization"] = array("minify_html",
																	   "gzip_compression", 
															   "minify_css", 
															   "minify_js", 
															   "combine_css",
															   "enable_default_font_display"
															  );

			$feature_sections["Image Resource Optimization"] = array(
															   "basic_image_optimization",
															   "image_optimization",
															   "external_image_optimization",
															   "webp_images",
															   "auto_size_images"
															   
															  );
				
				
			$feature_sections["Javascript Resource Delivery"] = array("defer_render_blocking_js", 
														   			  "proxy_external_js");

			$feature_sections["CSS Resource Delivery"] = array(
														   "defer_render_blocking_css",
														   "inject_critical_css",
														   "defer_unused_css", 
															 "essential_css",
														   "proxy_external_css");
				
			$feature_sections["Misc Resource Delivery"] = array(
														  "dns_prefetch",
														  "preload_user_files",
														  "preload_fav_icons",
														  "google_fonts",
														  "strip_google_fonts_mobile");
				
			$feature_sections["Lazy Loading"] = array("lazy_load_images", 
													  "lazy_load_background_images", 
													  "lazy_load",
													  "lazy_load_youtube",
													  "lazy_load_twitter_feed");
			
			$feature_sections["Lazy Loading"][] = "lazy_load_scripts";
			

			
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
				
			/* developer-edition component begin */	
			if ($pegasaas->is_pro_edition()) {
				if ($pegasaas->utils->does_plugin_exists_and_active("accelerated-mobile-pages") || $pegasaas->utils->does_plugin_exists_and_active("accelerated-moblie-pages")) {
					$feature_sections["Compatibility"][] = "accelerated_mobile_pages";
				}
			}
			/* developer-edition component end */	
			
							
			$feature_sections["Miscellaneous"] = array("logging",  
													   "promote",
													   "exclude_urls", 
													   "auto_crawl",
													   "blog",
													   "dynamic_urls",
													   "test_setting"
													  );
				
			/* developer-edition component begin */	
			if ($pegasaas->is_pro_edition()) {
				$feature_sections["Miscellaneous"][] = "updater";
			}
			/* developer-edition component end */		
			
		
			$requires_cache_clearing = array("minify_html", 
											 "combine_css", 
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
													  "lazy_load_twitter_feed",
											"lazy_load_scripts",
											"thrive",
											"themify",
											"wordlift");
				
			$locked_for_novice = array("gzip_compression", 
									   "browser_caching",
									   "minify_js", 
									   "combine_css", 
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
				
			if (is_array($features)) {
			foreach ($feature_sections as $feature_title => $section_features) { 	
				?>
				<div class='pegasaas-feature-box pegasaas-feature-section-container' style='clear: both;'>
					<div class='pegasaas-subsystem-title'><?php echo $feature_title; ?></div>
						<div class='row'>
						
					<?php if ($feature_title == "Server Response Time") { ?> 
					<p class='section-description'>The first target when optimizing the performance of your website is the response time of your server.  If your server response time is too slow,
						your visitors will bounce.  Enabling Page Caching, Browser Caching, and using a CDN can help to speed up the response time of your web server.</p>
				<?php } else if ($feature_title == "General Resource Optimization") { ?>
						<p class='section-description'>Once server response time has been optimized, then the resources (CSS, Javascript, and HTML)
in your site should be optimized.</p>
				<?php } else if ($feature_title == "Image Resource Optimization") { ?>
						<p class='section-description'>Once server response time has been optimized, then the images resources in your site should be optimized.</p>						
					<?php } else if ($feature_title == "Misc Resource Delivery") { ?>
						<p class='section-description'>After the website resources are optimized, then how they are delivered by and to the web browser is the next task of optimization.  Some of the 
							features/settings in previous sections also help with resource delivery, such as CDN and Browser Caching.</p>
						<?php } else if ($feature_title == "Lazy Loading") { ?>
						<p class='section-description'>
						Lazy loading can be used to reduce the initial page load time dramatically.  Images, IFRAMES, and YouTube resources can take seconds to load.  By deferring their loading,
							your page's fully loaded time can be significantly improved. 
						</p>
						<?php } else if ($feature_title == "Compatibility") { ?>
						
						<?php }
						foreach ($section_features as $feature) { 
					
						$feature_settings = $features["$feature"];
						if (!isset($feature_settings) && !isset($features["premium"]["$feature"])) {
							continue;
						} else if (isset($features["premium"]["$feature"])) {
							$feature_settings = $features["premium"]["$feature"];
						}
						$is_new_feature = $pegasaas->is_recent_version($feature_settings['since'], "new");
						$is_recent_feature = $pegasaas->is_recent_version($feature_settings['since'], "recent");
						$is_premium_feature = isset($features["premium"]["$feature"]);
						?> 
				<div class='<?php if ($feature == "lazy_load_scripts") { ?>col-md-12 col-lg-8<?php } else { ?>col-md-6 col-lg-4<?php } ?>'>
					<div class='pegasaas-feature-box <?php if (isset($features['premium']["$feature"])) { ?>premium-feature <?php } ?><?php if (in_array($feature, $locked_for_novice )) { ?>locked-for-novice <?php } ?> <?php if ($feature_settings['status'] == "0" || $feature_settings['status'] == "" || $is_premium_feature) { print "feature-disabled"; } ?> <?php if ($is_new_feature) { ?>new-feature<?php } else if ($is_recent_feature) { ?>recent-feature<?php } ?>'>
						<div class='pegasaas-subsystem-title'>
							<div class='pegasaas-subsystem-title-icon hidden-xs'>
								<i id="pegasaas-setting-icon-<?php echo $feature; ?>" class='<?php echo $icons["{$feature}"]; ?>'></i>
							</div>
							<?php echo str_replace("Blocking JavaScript", "Blocking JS", $feature_settings['name']); ?>
							<?php if ($feature == "dynamic_urls") { ?><span class='label label-danger'>Advanced</span><?php } ?>
							<i class='fa fa-fw fa-angle-down feature-box-toggle'></i> 
						
						 <?php if ($pegasaas->settings['status'] > 0 && !isset($features["premium"]["$feature"]) && $feature != "display_mode" && $feature != "display_level" && $feature != "blog" && $feature != "updater") { ?>
				<form method="post" class='feature-switch pull-right' target="hidden-frame">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>
				<input type='hidden' name='f' value='<?php echo $feature; ?>' />
				<?php if (isset($features['upgradable']["$feature"])) { ?>
							<span class='label label-premium' rel='tt' 
								  title='A more advanced and faster feature is available by upgrading.'>UPGRADE AVAIL</span>
				<?php } ?>
					
					<?php 

				if ($feature_settings['status'] == "0" || $feature_settings['status'] == "") { 
					?><input type='hidden' name='c' value='enable-feature' />
					<input  type='checkbox' class='js-switch js-switch-small' />
					<?php 
				} else { 
					?><input type='hidden' name='c' value='disable-feature' />
					  <input type='checkbox' class='js-switch js-switch-small' checked />
					<?php 

				} ?>
				</form>	
		
			
				<?php } else if (isset($features['premium']["$feature"])) { ?>
							<form method="post" class='feature-switch pull-right' target="hidden-frame">
								<span class='label label-premium'>PREMIUM FEATURE</span>
								<input type='checkbox' class='js-switch js-switch-small js-premium-feature' />
							</form>
				<?php } ?>

						</div>
				<div class='pegasaas-accelerator-subsystem-feature-description'>	
<!-- display version since this feature has been included -->
<?php if ($feature_settings['since'] != "") { ?><p class='since-version'>Since: v<?php echo ($feature_settings['since']); ?></p><?php } ?>

<!-- explain what this feature does -->
<?php if ($feature == "page_caching") { ?>
<p>In order to reduce the time it takes to serve your web pages, have Page Caching enabled.</p>
<p>The fastest method is the File Based, served via .htaccess method, however for testing purposes, you may wish to go with the File Based served via WordPress method (slower), or turn the feature off altogether. </p>
<?php } else if ($feature == "dynamic_urls") { ?>
<p>If you would like to bypass the optimized page cache, when a user visits a page using a dynamic URL (example: https://www.yourwebsite.com/?product=3333&amp;c=add-to-cart), such as when a shopping cart, or dynamically generated forum or events calendar is used, then enable this feature.</p>
<p><b>PPC / UTM Tracking</b><br>If you have this feature enabled, you may wish to exclude certain "query string" parameters from triggering the bypass of optimized cache, such as those used with Pay-Per-Click advertising, or other campaign tracking, which does
	not require change to the HTML of your page. </p>

<?php } else if ($feature == "display_mode") { ?>
<p>Choose how you want the interface to be styled. </p>

<?php } else if ($feature == "display_level") { ?>
<p>Choose how the level of detail and configurability that is suitable for your experience level. </p>					
<ul>
  <li><b>Novice:</b> Show just your page speed scores -- some settings are locked for stability.</li>
  <li><b>Intermediate:</b> Show metrics such as Time To First Byte, First Contentful Paint, Time To Interactive, First CPU Idle -- configurability is expanded.</li>	
  <li><b>Advanced:</b> You're a GURU, so we'll let you have at it.  All the safeties are turned off, so you assume the risks involved with tinkering as there is a greater risk for turbulence when deviating from the defaults.</li>	
</ul> 
					
<?php } else if ($feature == "auto_clear_page_cache") { ?>
<p>You may choose to have your optimized pagels cleared on a regular basis.  This can be helpful if you find that your content dynamically
					changes in your site over time.</p>
<p>A word of caution -- by clearing your cache, your pages will be automatically re-optimized on demand, or via the Auto Crawl functionality only if you have sufficent 
	optimization credits. </p>

<?php } else if ($feature == "browser_caching") { ?>
<p>It is extremely important to leverage Browser Caching to prevent repeated loads of static resources by the web browser.</p>
<p>Enabling this feature can also cut down on bandwidth usage and un-needed strain on the web server.</p>
					
<?php } else if ($feature == "cdn") { ?>
<p>When CDN (Content Delivery Network) is enabled, Pegasaas will automatically push all of your cachable resources through a global CDN.</p>
<p>Your visitors will receive these resources from a data center closest to them, to ensure the fastest possible delivery of the resources, which will result in a faster page load time.</p>
<p><b>Please Note:</b> If you are testing with GTMetrix to evalulate your "fully loaded time", your load time may be higher than normal during the first 2-3 tests, until such time as your website resources are
fully cached within the CDN server closest to the GTMetrix testing server.  Once the resources are cached, however, the fully loaded time should be significantly faster for your
cachable resources.</p>
					
<?php } else if ($feature == "cloudflare") { 
		if ($pegasaas->cache->cloudflare_exists()) { ?>
<p>It appears as though you may have Cloudflare acting as a child and l. In order for Pegasaas to properly clear cache of resources on demand, please specify your Cloudflare API credentials.</p>
		<?php } else { ?>
<p>If you have Cloudflare active, be sure to specify your Cloudflare API key.</p>	
		<?php } ?>

<?php } else if ($feature == "varnish") { ?>	
	<?php if ($feature_settings['status'] == "1") { ?>
<p>If you feel that Varnish is acively used with your web hosting provider, you can leave this enabled, however it is recommended that you disable this feature if you're not sure.</p>
	<?php } else { ?>
<p>If you feel that Varnish is actively used with your web hosting provider, you can enable this feature.</p>
	<?php } ?> 
						
<?php } else if ($feature == "auto_crawl") { ?>
<p>To generate a cache of our web pages as fast as possible, Pegasaas can crawl your site automatically.</p>
<p>You may wish to turn this off if you have many (500+) pages in your site that see little traffic, and are on a shared hosting environment that is resource limited.</p>

<?php } else if ($feature == "woocommerce") { ?>
<p>By default, general features of WooCommerce are compatible with the Pegasaas Accelerator plugin.  However, if you need extended support for Product Tags and Product Categories, then you will need to enable this feature.</p>
<p>When enabled, this feature will allow you to add acceleration to WooCommerce Product Tags and Product Categories.</p>
					
					
<?php } else if ($feature == "wordlift") { ?>
<p>It appears as though you have the WordLift plugin active.  In order for to make your pages load faster when WordLift is active, Pegasaas can cache the schema for each page.  The schema is automatically cleared when a page is updated.</p>

<?php } else if ($feature == "preload_fav_icons") { ?>
<p>Fav icons are typically the last item that is requested by the browser.  Unfortunately, requesting these resources late in the render process can result in a longer load time than
	if they are requested with a 'preload'.  Because the Pegasaas CDN enables parallel resource requests, Pegasaas Accelerator can preload the fav icons early resulting in a faster page render.</p>

<?php } else if ($feature == "preload_user_files") { ?>
<p>While Pegasaas Accelerator automatically requests that the browser preloads CSS, javascript, and font files, there may be other files that you identify that you wish the browser to preload rather than load late.  An example of
	a file that you may wish to preload is something such as Google Analytics.</p>
					
<?php } else if ($feature == "enable_default_font_display") { ?>
<p>In order to present a page that renders as fast as possible for your visitors, you should specify a non-blocking font-display method for your web fonts.</p>
					
<?php } else if ($feature == "strip_google_fonts_mobile") { ?>
<p>Google Fonts are large and often require multiple round-trips based upon the different types of styles for the font your designer has requested be loaded.</p>
					<p>Pegasaas can either load just the most critical Google Font, or strip them altogether for the fastests possible load.</p>
										
<?php } else if ($feature == "exclude_urls") { ?>
					<p>Specify path to pages that you wish to exclude. If you specify a folder, such as "/catalog/", all pages in that folder will be excluded. </p>
<?php } else if ($feature == "gzip_compression") { ?>
				<p>By enabling GZIP Compression on the server, the size of your web pages can be reduced by 50% or more, cutting down the time it takes to transfer the files from your web server to your visitors web browser.</p><p>The visitors web browser then automatically "un-zips" the page.</p>
			  <?php } else if ($feature == "dns_prefetch") { ?>
				<p>Prefetching the DNS information, or prconnecting, for any external website, that is housing resources that your page uses, can save you a little bit of time when it comes to load those resources.</p>
			  <?php } else if ($feature == "minify_html") { ?>
				<p>Minification of HTML involves stripping out unrequired whitespace and comments from your web page.</p>
					<p>This can reduce the size of your web page, depending upon the amount of bloat in an un-optimized page, by up to 50%.</p>
			  <?php } else if ($feature == "minify_css") { ?>
				<p>Minification of CSS involves stripping out unrequired whitespace and comments from your CSS documents.</p>
					<p>While CSS documents do not normally have a lot of white-space or comments, by minifying anything possible, you can still save previous milliseconds in transfer time for each document.</p>
			  <?php } else if ($feature == "combine_css") { ?>
				<p>Combining CSS resources into a single file can reduce load time by eliminated the overhead incurred by loading resources separately.</p>
					
			  <?php } else if ($feature == "minify_js") { ?>
				<p>Minification of JS involves stripping out of unrequired whitespace and comments from your JavaScript files.</p>
					<p>This process can be more prolemmatic if the JavaScript was not written properly, so if you run into pages where functionality is lost, try disabling this feature.</p>
			  <?php } else if ($feature == "disable_wp_emoji") { ?>
				<p>Disabling the often unused WP Emoji library can improve page load time, if it is not required.</p>

			  <?php } else if ($feature == "themify") { ?>
				<p>When using the Themify Ultra theme, certain modifications must be made to the code.  If you find that you have reduced functionality, you may disable this feature however doing so may result in lower PageSpeed scores.</p>
		  <?php } else if ($feature == "essential_css") { ?>
				<p>Essential CSS is CSS that may be required to properly render your page when the typical Critical CSS is inadequate.<p>
				<p>This CSS is often required when components above the fold are rendered via javascript and cannot be detected by the Critical CSS detection engine.</p>

			  <?php } else if ($feature == "image_optimization") { ?>
				<p>Your biggest gains in page load time will come from optimizing images.<p>
				<p>Images are the largest resources that are loaded into your web page.</p>
			  <?php } else if ($feature == "basic_image_optimization") { ?>
				<p>Your biggest gains in page load time will come from optimizing images.<p>
				<p>Images are the largest resources that are loaded into your web page.</p>
			  <?php } else if ($feature == "auto_size_images") { ?>
				<p>Often, images are uploaded and used in places in your website where the WordPress core cannot provide appropriately sized images.  Pegasaas can
					size images that are too large, automatically for you.<p>
				
			  <?php } else if ($feature == "external_image_optimization") { ?>
						<p>Accelerator can also optimize external (off-site) images.</p>
		<?php } else if ($feature == "lazy_load_scripts") { ?>
						<p>Some scripts often do not need to be activated until action is taken on a page.  By lazy-loading some scripts, the fully loaded time of your web page can be improved dramatically.</p>
			  <?php } else if ($feature == "webp_images") { ?>
						<p>In order to have your images load as fast as possible for those web browsers that support the WebP image type, Pegasaas Accelerator can auto-deliver
					WebP versions of your JPEG/PNG images.</p>
			  <?php } else if ($feature == "animations_css") { ?>
						<p>The widely used Animations CSS framework, that is used to "animate in" page blocks, is not compatible with fast (non-render blocked) page loads.  Pegasaas disables above-the-fold animations, but leaves below-the-fold animations to activate on demand.</p>
			  <?php } else if ($feature == "promote") { ?>

			 <p>Help us spread the word by displaying a 'powered by' message at the bottom of your web page.  Sign up to become and affiliate (through your pegasaas.com account) to make
					$ from referals, and Pegasaas will automatically put your affiliate link in the 'powered by' message for you.</p>

					<?php } else if ($feature == "defer_render_blocking_js") { ?>
				<p>By deferring render-blocking JavaScript, your scripts will all be loaded and then executed after the page has been rendered.</p>
				<p>This can save you SECONDS worth of load time.</p>
			  <?php } else if ($feature == "defer_render_blocking_css") { ?>
				<p>By deferring render-blocking CSS, your stylesheets will all be loaded after the page has been initially loaded.  This can save you SECONDS worth of load time.</p>
				<p>To avoid the Flash of Unstyled Content, you should make sure the "Inject Critical CSS" feature is enabled.</p>
			  <?php } else if ($feature == "defer_unused_css") { ?>
				<p>By deferring unused CSS, your stylesheets will all be loaded after the page has loaded.  This can save you SECONDS worth of load time.</p>
				<p>This feature is an extension of the 'Defer Render Blocking CSS' mechanism.</p>
			  <?php } else if ($feature == "inject_critical_css") { ?>
				<p>To avoid the "Flash of Unstyled Content", you can have Accelerator automatically detect your critical path (above the fold) styles, and inject them into your pages.</p>
			  <?php } else if ($feature == "proxy_external_js") { ?>
					<i>This is an experimental feature and may be removed at any time.</i>
			  <?php } else if ($feature == "proxy_external_css") { ?>
					<i>This is an experimental feature and may be removed at any time.</i>

		  		<?php } else if ($feature == "lazy_load_youtube") { ?>
					<p>By lazy-loading YouTube, Pegasaas creates a container with the YouTube thumbnail and a play button.  This makes your page load faster as the many resources that YouTube loads (that causes the initial page to load) are only loaded on demand once your visitor clicks the play button.</p>

				<?php } else if ($feature == "google_fonts") { ?>
				<p>Enabling this feature will have Pegasaas optimize the delivery of Google Fonts for you by inlining the font requests directly in the page, rather than through a deferred stylesheet call.  Using this method also
					means that Pegasaas will load the Google Fonts via our super fast CDN.</p>

					<?php } else if ($feature == "blog") { ?>
					<p>Enable or disable acceleration for pages that are used by the WordPress blog.</p>
					
					<?php } else if ($feature == "logging") { ?>
					<p>Logging is available to troubleshoot issues.  This is typically only enabled by Pegasaas Tech Support. </p>
			
		
					

					<?php } else if ($feature == "lazy_load_images") { ?>
					<p>Lazy loading your foreground images can help to reduce the page load time.</p>
					<p>Lazy loading can also help with penalities of off-site image content that may not have appropriate browser caching enabled.</p>
					

					<?php } else if ($feature == "lazy_load_background_images") { ?>
					<p>Lazy loading your background images can help to reduce the page load time.</p>
					<p>Lazy loading can also help with penalities of off-site image content that may not have appropriate browser caching enabled.</p>
					
					
					<?php } else if ($feature == "lazy_load") { ?>
					<p>Lazy loading your iframes can help to reduce the load time of your web pages.</p>
					<p>Lazy loading can also help with penalities of off-site iframe content that may not have appropriate browser caching enabled.</p>

			  <?php } ?>


				<div class='pegasaas-accelerator-subsystem-status <?php if ($pegasaas->settings['status'] == 0) { print "pegasaas-accelerator-subsystem-status-bypassed"; } else if ($feature_settings['status'] == "0") { print "pegasaas-accelerator-subsystem-status-bypassed"; } else { print "pegasaas-accelerator-subsystem-status-onlinex"; } ?>'><?php 
													  if ($pegasaas->settings['status'] == 0) { 
														  print "Offline";
													  } else {
														if ($feature_settings['status'] == "0" && false) { 
															print "You have disabled this feature."; 
														} else { 
															if ($feature == "defer_render_blocking_js") {

					?>
					<div class='hidden-novice hidden-intermediate'>
					<b>How To Defer:</b><br/>
				<form method="post" style="display: inline-block" class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-status">
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>					
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="1">Default</option>  
<?php /* developer-edition component begin */	
			if ($pegasaas->is_pro_edition()) { ?>
				  <option value="3" <?php if ($feature_settings['status'] == "3") { ?>selected<?php } ?>>Internally embedded at end of page</option>
				  <option value="2" <?php if ($feature_settings['status'] == "2") { ?>selected<?php } ?>>Externally deferred at end of page</option>
<?php 		} 
	/* developer-edition component end */ ?>
					<option value="4" <?php if ($feature_settings['status'] == "4") { ?>selected<?php } ?>>Externally defer all files and inline blocks at original DOM location</option>
				  <option value="5" <?php if ($feature_settings['status'] == "5") { ?>selected<?php } ?>>Externally defer all files at original DOM location and assembled inline blocks at end of page (default)</option>
				
				</select>
			</form>
						<br/><br/>
					</div>
					
					
					<div class='hidden-novice'>
						<b>Exclude Scripts:</b><br/>
										<form method="post" style="display: block" class='feature-setting-change'>
				<input type="hidden" name="c" value="change-local-setting">
											
											<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>
					<input type="hidden" name="f" value="defer_render_blocking_js_exclude_scripts">
						<textarea placeholder="/path/to/javascript-file.js" class='form-control form-control-full' name='s'><?php echo $feature_settings['exclude_scripts']; ?></textarea>
					<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
			</form>
						</div>
					<?php } else if ($feature == "auto_size_images") { ?>
					<!--
<b>Exclude Pages:</b><br/>
										<form method="post" style="display: block">
				<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="auto_size_images_exclude_pages">
						<textarea placeholder="/path/to/exclude/" class='form-control form-control-full' name='s'><?php echo $feature_settings['exclude_pages']; ?></textarea>
					<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
			</form>
<br/><b>Exclude Specific Images:</b><br/>
										<form method="post" style="display: block">
				<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="auto_size_images_exclude_images">
						<textarea placeholder="/path/to/image-file.jpg" class='form-control form-control-full' name='s'><?php echo $feature_settings['exclude_images']; ?></textarea>
					<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
			</form>						
-->

				<?php } else if ($feature == "dns_prefetch") { ?>
					<div class='hidden-novice hidden-intermediate'>
					<b>Method:</b><br/>
									<form method="post" style="display: inline-block" class='feature-setting-change'
<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>										
					<input type="hidden" name="c" value="change-feature-status">
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="1">Default</option>
				  <option value="2" <?php if ($feature_settings['status'] == "2") { ?>selected<?php } ?>>DNS Prefetch (Default)</option>
				  <option value="3" <?php if ($feature_settings['status'] == "3") { ?>selected<?php } ?>>Preconnect</option>
				</select>
					</form>
					
					
					<br/><br/><b>Additional Domains:</b><br/>
										<form method="post" style="display: block" class='feature-setting-change'>
<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>											
				<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="dns_prefetch_additional_domains">
						<textarea placeholder="Example: //www.googletagmanager.com" class='form-control form-control-full' name='s'><?php echo $feature_settings['additional_domains']; ?></textarea>
					<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
			</form>					
</div>
				<?php } else if ($feature == "auto_clear_page_cache") { ?>
					<b>Frequency:</b><br/>
									<form method="post" style="display: inline-block" class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-status">
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="1">Monthly</option>
				  <option value="2" <?php if ($feature_settings['status'] == "2") { ?>selected<?php } ?>>Weekly</option>
				</select>
					</form>					
					<?php } else if ($feature == "preload_user_files") { ?>
<div class='hidden-novice'>
					
					
					<br/><br/><b>Resources To Preload:</b><br/> 
										<form method="post" style="display: block" class='feature-setting-change'>
				<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="preload_user_files_resources">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>											
						<textarea placeholder="Example: https://www.google-analytics.com/analytics.js" class='form-control form-control-full' name='s'><?php echo $feature_settings['resources']; ?></textarea>
					<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
			</form>	
	</div>
			<?php } else if ($feature == "defer_render_blocking_css") { ?>
					<div class='hidden-novice'>
					<b>Exclude CSS Files From Being Deferred:</b><br/> 
										<form method="post" style="display: block" class='feature-setting-change'>
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>													
				<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="defer_render_blocking_css_exclude_stylesheets">
						<textarea placeholder="/path/to/stylesheet.css" class='form-control form-control-full' name='s'><?php echo $feature_settings['exclude_stylesheets']; ?></textarea>
					<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
			</form>
					</div>
					<?php } else if ($feature == "defer_unused_css") { ?> 
<?php /* developer-edition component begin */	
			if ($pegasaas->is_pro_edition()) { ?>					
					<div class='hidden-novice hidden-intermediate'>
				<b>When To Load:</b>	
					<br/>
				<form method="post" style="display: inline-block" class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-status">
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>							
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="1">Auto Detect (Default)</option>  
				  <option value="3" <?php if ($feature_settings['status'] == "3") { ?>selected<?php } ?>>As Soon As Possible</option>
				  <option value="4" <?php if ($feature_settings['status'] == "4") { ?>selected<?php } ?>>After 1.5 Seconds</option>
				  <option value="5" <?php if ($feature_settings['status'] == "5") { ?>selected<?php } ?>>After 5.0 Seconds</option>
				  <option value="2" <?php if ($feature_settings['status'] == "2") { ?>selected<?php } ?>>On Action (Scroll/Click)</option>
				</select>
			</form>					
					<br/><br/>
						</div>
<?php } 
	/* developer-edition component end */ ?>																  
					<div class='hidden-novice'>
						<b>Exclude CSS Files:</b><br/>
										<form method="post" style="display: block" class='feature-setting-change'>
				<input type="hidden" name="c" value="change-local-setting">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>													
					<input type="hidden" name="f" value="defer_unused_css_exclude_stylesheets">
						<textarea placeholder="/path/to/stylesheet.css" class='form-control form-control-full' name='s'><?php echo $feature_settings['exclude_stylesheets']; ?></textarea>
					<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
			</form>
						</div>
					<?php } else if ($feature == "essential_css") { ?>
					<div class='hidden-novice'>
					<br/><br/><b>Essential CSS:</b><br/>
										<form method="post" style="display: block" class='feature-setting-change'>
				<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="essential_css_css">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>													
						<textarea placeholder=".your-css { font-color: red; content: 'Example'; }" class='form-control form-control-full' name='s'><?php echo $feature_settings['css']; ?></textarea>
					<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
			</form>
					</div>
					<?php } else if ($feature == "exclude_urls") {  ?>
					
					<form method="post" style="display: block" class='feature-setting-change'>
				<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="exclude_urls_urls">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>								
						<textarea class='form-control form-control-full' name='s'><?php echo $feature_settings['urls']; ?></textarea>
					<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
			</form>
					
					<?php } else if ($feature == "strip_google_fonts_mobile") {  ?>
					<div class='hidden-novice'>
					<form method="post" style="display: block" class='feature-setting-change'>
				<input type="hidden" name="c" value="change-feature-status">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>								
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="1">Strip All (for best performance)</option>
				  <option value="2" <?php if ($feature_settings['status'] == "2") { ?>selected<?php } ?>>Strip Non-Essential (for best display) </option>
				</select>
			</form>				
					</div>
<?php } else if ($feature == "page_caching") { ?>
					<div class='hidden-novice hidden-intermediate'>
<b>Caching Type:</b><br/>
<form method="post" style="display: inline-block" class='feature-setting-change'>
	<input type="hidden" name="c" value="change-feature-status">
	<input type="hidden" name="f" value="<?php echo $feature; ?>">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>			
	<select class='form-control' onchange="submit_form(this)" name="s">
		<option value="1">Default</option>
		<option value="2" <?php if ($feature_settings['status'] == "2") { ?>selected<?php } ?>>File Based [served by .htaccess] (default) </option>
		<option value="4" <?php if ($feature_settings['status'] == "4") { ?>selected<?php } ?>>File Based [served by WordPress]</option>
	</select>
</form>
</div>
<?php } else if ($feature == "display_mode") { ?>
					<b>Display Mode:</b><br/>
				<form method="post" style="display: inline-block" target="hidden-frame" class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-status">
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
										<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>		
				<select class='form-control' onchange="toggle_display_mode(this)" name="s">
					<option value="1" <?php if ($feature_settings['status'] == "1") { ?>selected<?php } ?>>Light</option>				  
					<option value="0"  <?php if ($feature_settings['status'] == "0") { ?>selected<?php } ?>>Dark</option>
				  <option value="2" <?php if ($feature_settings['status'] == "2") { ?>selected<?php } ?>>Plain</option>
				</select>
			</form>
<?php } else if ($feature == "display_level") { ?>
					<b>Experience Level:</b><br/>
				<form method="post" style="display: inline-block" target="hidden-frame" class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-status">
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>		
				<select class='form-control' onchange="toggle_display_level(this)" name="s">
					<option value="0" <?php if ($feature_settings['status'] == "0") { ?>selected<?php } ?>>Novice</option>				  
					<option value="1"  <?php if ($feature_settings['status'] == "1") { ?>selected<?php } ?>>Intermediate</option>
				  <option value="2" <?php if ($feature_settings['status'] == "2") { ?>selected<?php } ?>>Advanced</option>
				</select>
			</form>					
<?php } else if ($feature == "inject_critical_css") { ?>
					<div class='hidden-novice hidden-intermediate'>
					<b>Where To Inject:</b><br/>
				<form method="post" style="display: inline-block" class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-status">
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>							
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="1">Default</option>
				  <option value="2" <?php if ($feature_settings['status'] == "2") { ?>selected<?php } ?>>Before Closing &lt;/head&gt; Tag </option>
				  <option value="3" <?php if ($feature_settings['status'] == "3") { ?>selected<?php } ?>>Before First &lt;link&gt; Tag (default) </option>
				</select>
			</form>
						</div>
<?php } else if ($feature == "updater") { ?>
					<b>Update To:</b><br/>
				<form method="post" style="display: inline-block" target="hidden-frame" class='feature-setting-change' >
					<input type="hidden" name="c" value="change-feature-status">
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>		
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="0">Public Release (Default)</option>
				  <option value="1" <?php if ($feature_settings['status'] == "1") { ?>selected<?php } ?>>Release Candidate (or better)</option>
				  <option value="2" <?php if ($feature_settings['status'] == "2") { ?>selected<?php } ?>>Beta Release (or better)</option>
				</select>
			</form>	
	<?php } else if ($feature == "cloudflare") { ?>
	<?php if (!$pegasaas->cache->cloudflare_credentials_valid() && $feature_settings['api_key'] != "" && $feature_settings['account_email'] != "") { ?><p class='pegasaas-warning pegasaas-warning-settings-box'>It appears as though either the Account Email or API Key is invaild.</p> <?php } ?>
				<form method="post" class='feature-setting-change form-inline <?php if (!$pegasaas->cache->cloudflare_credentials_valid()) { print "has-error"; } ?>' style="display: block; margin-bottom: 5px; ">
					<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="cloudflare_account_email">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>							
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-envelope fa-fw"></i></span>
						<input name='s' class="form-control" type="text" placeholder="Account Email" value="<?php echo $feature_settings['account_email']; ?>">
					</div>
					<button type="button" onclick="submit_form(this)" class="btn btn-default">Save</button>
				</form> 				

				<form method="post" class='feature-setting-change form-inline <?php if (!$pegasaas->cache->cloudflare_credentials_valid()) { print "has-error"; } ?>' style="display: block">
					<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="cloudflare_api_key">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>							
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
						<input name='s' class="form-control" type="text" placeholder="API Key" value="<?php echo $feature_settings['api_key']; ?>">
					</div>
					<button type="button" onclick="submit_form(this)" class="btn btn-default">Save</button>
				</form>       	

<?php } else if ($feature == "image_optimization" || $feature == "external_image_optimization") {
  if ($pegasaas->is_pro_edition()) { ?>
	
					<div class='hidden-novice'>
					<b>Optimization Level:</b><br/>
				<form method="post" style="display: inline-block"  class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-status">
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>					
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="1">Default</option>
				  <option value="100" <?php if ($feature_settings['status'] == "100") { ?>selected<?php } ?>>Best Quality</option>
				  <option value="80"  <?php if ($feature_settings['status'] == "80")  { ?>selected<?php } ?>>Mid Quality</option>
				  <option value="65"  <?php if ($feature_settings['status'] == "65")  { ?>selected<?php } ?>>Economy Mode</option>
				  <option value="55"  <?php if ($feature_settings['status'] == "55")  { ?>selected<?php } ?>>Low Quality (default)</option>				

				</select>
			</form>  
					</div>
<div class='hidden-novice'>
<br/><b>Images From Being Optimized:</b><br/>
<form method="post" style="display: block" class='feature-setting-change'>
	<input type="hidden" name="c" value="change-local-setting">
	<input type="hidden" name="f" value="image_optimization_exclude_images">
<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>	
	<textarea placeholder="/path/to/image.jpg" class='form-control form-control-full' name='s'><?php echo $feature_settings['exclude_images']; ?></textarea>
	<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
</form>						
</div>					
					
<?php 
}
								   } else if ($feature == "dynamic_urls") { 
		$default_args = array("utm_source", "utm_medium", "utm_campaign", "utm_term", "utm_content", "gclid", "keyword");
?>

<b>Ignore When Any of the Following is  Present:</b>
<ul style='text-transform: none'>
	<?php foreach ($default_args as $arg) { ?>		
	<li <?php if ($feature_settings["{$arg}"] != 1) { print "class='feature-disabled'"; } ?>>
		<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
			<input type="hidden" name="c" value="toggle-local-setting">
			<input type='hidden' name='f' value='dynamic_urls_<?php echo $arg; ?>' />
			<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings["{$arg}"] == 1) { print "checked"; } ?> />
		</form> 
		 &nbsp; <?php echo $arg; ?>
	</li>
	<?php } ?>
					
					</ul>
	
<br/><b>Custom Parameters To Ignore:</b><br/>
	<small style='text-transform: none; font-weight: normal; '>				Example: https://<?php echo $pegasaas->utils->get_http_host(); ?>/?<b>your_key</b>=value_2<br/></small>
										<form method="post" style="display: block"  class='feature-setting-change'>
				<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="dynamic_urls_additional_args">
											<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>
						<textarea placeholder="your_key
another_key
something_else" class='form-control form-control-full' name='s'><?php echo $feature_settings['additional_args']; ?></textarea>
					<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
			</form>						
<?php } else if ($feature == "enable_default_font_display") {

					?>
<div class='hidden-novice'>
	<b>Optimization Level:</b><br/>
	<form method="post" style="display: inline-block" class='feature-setting-change'>
		<input type="hidden" name="c" value="change-feature-status">
		<input type="hidden" name="f" value="<?php echo $feature; ?>">
		<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>
		<select class='form-control' onchange="submit_form(this)" name="s">
			<option value="1">Fallback (default)</option>
			<option value="swap" <?php if ($feature_settings['status'] == "swap") { ?>selected<?php } ?>>Swap</option>
			<option value="optional"  <?php if ($feature_settings['status'] == "optional")  { ?>selected<?php } ?>>Optional</option>
		</select>
	</form>   	
</div>
<?php } else if ($feature == "lazy_load_scripts" && $pegasaas->is_pro()) { ?>
					<div class='hidden-novice'><!--
		<?php if (isset($pegasaas->settings['settings']['lazy_load_scripts']['instagram_feed'])) {
					?>
					<ul class='regular-case'>
					  <li <?php if ($feature_settings['instagram_feed'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left prompt-to-clear-cache' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='lazy_load_scripts_instagram_feed' />
							<input type='hidden' name='prompt' value='clear_cache' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['instagram_feed'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; Instagram Feed
					  </li>
					</ul>
					
			
			<?php } ?>
		<?php	if (isset($pegasaas->settings['settings']['lazy_load_scripts']['thirstyaffiliates'])) {
					?>
					<ul class='regular-case'>
					  <li <?php if ($feature_settings['thirstyaffiliates'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left prompt-to-clear-cache' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='lazy_load_scripts_thirstyaffiliates' />
							<input type='hidden' name='prompt' value='clear_cache' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['thirstyaffiliates'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; ThirstyAffiliates
					  </li>
					</ul>
			<?php } ?>		
<?php	if (isset($pegasaas->settings['settings']['lazy_load_scripts']['jetpack_twitter'])) {
					?>
					<ul class='regular-case'>
					  <li <?php if ($feature_settings['jetpack_twitter'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left prompt-to-clear-cache' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='lazy_load_scripts_jetpack_twitter' />
							<input type='hidden' name='prompt' value='clear_cache' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['jetpack_twitter'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; Jetpack Twitter Embed
					  </li>
					</ul>
			<?php } ?>							
<?php	if (isset($pegasaas->settings['settings']['lazy_load_scripts']['jetpack_facebook'])) {
					?>
					<ul class='regular-case'>
					  <li <?php if ($feature_settings['jetpack_facebook'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left prompt-to-clear-cache' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='lazy_load_scripts_jetpack_facebook' />
							<input type='hidden' name='prompt' value='clear_cache' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['jetpack_facebook'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; Jetpack Facebook Embed
					  </li>
					</ul>
			<?php } ?>
						
					<ul class='regular-case'>
					  <li <?php if ($feature_settings['wordpress'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left prompt-to-clear-cache' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='lazy_load_scripts_wordpress' />
							<input type='hidden' name='prompt' value='clear_cache' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['wordpress'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; WordPress Scripts
					  </li>
					</ul>
					<ul class='regular-case'>
					  <li <?php if ($feature_settings['google_recaptcha'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left prompt-to-clear-cache' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='lazy_load_scripts_google_recaptcha' />
							<input type='hidden' name='prompt' value='clear_cache' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['google_recaptcha'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; Google reCAPTCHA
					  </li>
					</ul>
-->
				<?php if (true) { ?>
						<p class='regular-case'><b>Please note</b> that care should be taken when lazy loading scripts.  If you lazy load a script that other scripts rely upon, which are not themselves lazy loaded, critical functionality in your page
						can be broken.  <b>It is strongly recommended that you test the functionality of your optimized pages after lazy loading.</b></p>
					<div class='table-complex-settings regular-case'>
					  <div class='row theader'>
					
						<div class='col-sm-6' style='padding-left: 30px; '>Script</div>
						<div class='col-sm-3'>Mobile</div>
						<div class='col-sm-3'>Desktop</div>
					  </div>	
						<?php $detected_lazy_loadable_scripts = $pegasaas->scanner->get_lazy_loadable_scripts();
							
							  foreach ($detected_lazy_loadable_scripts as $item) { 
								$url = $item['url'];

									
									
						?>
					
					<div class='row feature-row  <?php if ($feature_settings['custom_scripts']["{$url}"]['status'] != 1) { print "feature-disabled"; } ?>'>
						<form method="post" class='feature-toggle-switch prompt-to-clear-cache' target="hidden-frame">
						
						  
							<div class='col-sm-6' class='toggle-cell' > 
				
						
							<input type="hidden" name="c" value="toggle-local-complex-setting">
							<input type='hidden' name='f' value='lazy_load_scripts_custom_scripts' />
						    <input type='hidden' name='url' value='<?php echo $url; ?>' />
							<input type='hidden' name='prompt' value='clear_cache' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['custom_scripts']["{$url}"]['status'] == 1) { print "checked"; } ?> />
							&nbsp;
						  <?php if ($item['alias'] != "") { echo $item['alias']; } else { echo $item['url']; } ?>
					 	
							</div>
						<div class='col-sm-3'>
						  <select name='mobile_setting' class='form-control' >
							  <option value='0'    <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == '0') { ?>selected<?php } ?>>Do Not Lazy Load</option>  
							  <option value='500'  <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 500) { ?>selected<?php } ?>>After 0.5s</option>
							  <option value='1000' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 1000) { ?>selected<?php } ?>>After 1.0s</option>
							  <option value='1500' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 1500) { ?>selected<?php } ?>>After 1.5s</option>
							  <option value='2000' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 2000) { ?>selected<?php } ?>>After 2.0s</option>
							  <option value='2500' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 2500) { ?>selected<?php } ?>>After 2.5s</option>
							  <option value='3000' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 3000) { ?>selected<?php } ?>>After 3.0s</option>
							  <option value='3500' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 3500) { ?>selected<?php } ?>>After 3.5s</option>
							  <option value='4000' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 4000) { ?>selected<?php } ?>>After 4.0s</option>							  
							  <option value='4500' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 4500) { ?>selected<?php } ?> >After 4.5s</option>							  
							  <option value='5000' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 5000) { ?>selected<?php } ?>>After 5.0s</option>							  
							  <option value='5500' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 5500) { ?>selected<?php } ?>>After 5.5s</option>
							  <option value='6000' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 6000) { ?>selected<?php } ?>>After 6.0s</option>							  
							  <option value='6500' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 6500) { ?>selected<?php } ?>>After 6.5s</option>							  
							  <option value='7000' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 7000) { ?>selected<?php } ?>>After 7.0s</option>							  
							  <option value='7500' <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 7500) { ?>selected<?php } ?>>After 7.5s</option>	
							  <option value='1'    <?php if ($feature_settings['custom_scripts']["{$url}"]['mobile_status'] == 1) { ?>selected<?php } ?>>On Action</option>
						  </select>
						</div>
					<div class='col-sm-3'>
						  <select name='desktop_setting' class='form-control' >
							  <option value='0'    <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == '0') { ?>selected<?php } ?>>Do Not Lazy Load</option>  
							  <option value='500'  <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 500) { ?>selected<?php } ?>>After 0.5s</option>
							  <option value='1000' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 1000) { ?>selected<?php } ?>>After 1.0s</option>
							  <option value='1500' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 1500) { ?>selected<?php } ?>>After 1.5s</option>
							  <option value='2000' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 2000) { ?>selected<?php } ?>>After 2.0s</option>
							  <option value='2500' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 2500) { ?>selected<?php } ?>>After 2.5s</option>
							  <option value='3000' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 3000) { ?>selected<?php } ?>>After 3.0s</option>
							  <option value='3500' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 3500) { ?>selected<?php } ?>>After 3.5s</option>
							  <option value='4000' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 4000) { ?>selected<?php } ?>>After 4.0s</option>							  
							  <option value='4500' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 4500) { ?>selected<?php } ?> >After 4.5s</option>							  
							  <option value='5000' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 5000) { ?>selected<?php } ?>>After 5.0s</option>							  
							  <option value='5500' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 5500) { ?>selected<?php } ?>>After 5.5s</option>
							  <option value='6000' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 6000) { ?>selected<?php } ?>>After 6.0s</option>							  
							  <option value='6500' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 6500) { ?>selected<?php } ?>>After 6.5s</option>							  
							  <option value='7000' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 7000) { ?>selected<?php } ?>>After 7.0s</option>							  
							  <option value='7500' <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 7500) { ?>selected<?php } ?>>After 7.5s</option>	
							  <option value='1'    <?php if ($feature_settings['custom_scripts']["{$url}"]['desktop_status'] == 1) { ?>selected<?php } ?>>On Action</option>
						  </select>
						</div>						
						</form>
						</div>
						
						
						<?php } ?>	
							</div>
						<?php } ?>
	<br/><br/><b>Manually Add Scripts To Be Lazy Loaded:</b><br/>
	<form method="post" style="display: block" class='feature-setting-change'>
		<input type="hidden" name="c" value="change-local-setting">
<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>		
		<input type="hidden" name="f" value="lazy_load_scripts_additional_scripts">
		<textarea placeholder="/path/to/script.js" class='form-control form-control-full' name='s'><?php echo $feature_settings['additional_scripts']; ?></textarea>
		<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
	</form>			
</div>
					<?php } else if ($feature == "basic_image_optimization") { 
							$stats = $pegasaas->cache->get_basic_image_cache_stats();	
															
										?>
					<div class='image-optimization-stats'>
						<?php if ($pegasaas->settings['settings']['basic_image_optimization']['images_per_month'] != 9999) { ?>
					<p class='text-center'>Image Optimizations This Month</p>
					<div style='width: calc(100% - 90px); display: inline-block;'>
					<div class="progress">
  					<div class="progress-bar <?php if ($stats['percentage_of_use'] > 85) { print "progress-bar-danger"; } else if ($stats['percentage_of_use'] > 75) { print "progress-bar-warning"; } ?>" role="progressbar" aria-valuenow="<?php echo $stats['percentage_of_use']; ?>" aria-valuemin="0" aria-valuemax="100" style="min-width: 5em; width: <?php echo $stats['percentage_of_use']; ?>%;">
    <?php echo $stats['optimizations_so_far_this_month'] ?>/<?php echo $pegasaas->settings['settings']['basic_image_optimization']['images_per_month']; ?>
  </div>
</div>
						</div>
					<div style='margin-top: -1px;text-align: right; vertical-align: top; width: 80px; display: inline-block;'>
					<a target="_blank" class='btn btn-xs btn-success' href='https://pegasaas.com/upgrade/?utm_source=pegasaas-accelerator-lite-image-panel&upgrade_key=<?php echo $pegasaas->settings['api_key']; ?>-<?php echo $pegasaas->settings['installation_id']; ?>&site=<?php echo $pegasaas->utils->get_http_host(); ?>&current=<?php echo $pegasaas->settings['subscription']; ?>'>Upgrade</a>
					</div>
					<?php } ?>
					<p class='text-center' style='margin-top: 20px;'>Image Optimization Stats</p>
					
					<div class='row'>
					  <div class='col-sm-4'>
						  <div class='image-stat-container'>
						<div class='image-stat-number'><?php echo $stats['optimized_images']; ?></div>
						<div class='image-stat-description'>Images<span>&nbsp;</span></div>
						  </div>
					  </div>
					  <div class='col-sm-4'>
						  <div class='image-stat-container'>
						<div class='image-stat-number'><?php if ($stats['total_filesize'] / 1024 < 1000) {
																	print number_format($stats['total_filesize']/1024, 0, '.', ',')."<span>KB</span>";
																} else {
																	echo number_format($stats['total_filesize'] / 1024 / 1024, 1, '.', ',')."<span>MB</span>";
																} ?></div>
						<div class='image-stat-description'>Original</div>
						  </div>
					  </div>
					  <div class='col-sm-4'>
						  <div class='image-stat-container'>
						<div class='image-stat-number'><?php if ($stats['optimized_filesize'] / 1024 < 1000) {
																	print number_format($stats['optimized_filesize']/1024, 0, '.', ',')."<span>KB</span>";
																} else {
																	echo number_format($stats['optimized_filesize'] / 1024 / 1024, 1, '.', ',')."<span>MB</span>";
																} ?></div>
						<div class='image-stat-description'>Optimized</div>
						  </div>
					  </div>	
					</div>
					<div class='row'>
					  <div class='col-sm-offset-2 col-sm-4'>
						  <div class='image-stat-container'>
						<div class='image-stat-number'><?php 
																if ($stats['savings'] / 1024 < 1000) {
																	print number_format($stats['savings']/1024, 0, '.', ',')."<span>KB</span>";
																} else {
																	echo number_format($stats['savings'] / 1024 / 1024, 1, '.', ',')."<span>MB</span>";
																} ?>
						</div>
						<div class='image-stat-description'>Savings</div>
							  </div>
					  </div>
						<div class=' col-sm-4'>
						  <div class='image-stat-container'>
						<div class='image-stat-number'><?php if ($stats['total_filesize'] == 0) { echo "0"; } else { echo number_format(100*($stats['savings'] / $stats['total_filesize']), 0, '.', ''); } ?><span>%</span></div>
						<div class='image-stat-description'>Savings</div>
							  </div>
					  </div>	
					</div>
					
					<p class='text-center' style='margin-top: 20px;'>Image Cache</p>
					
					
					<div class='row'>
					  <div class='col-sm-4'>
						  <div class='image-stat-container'>
						<div class='image-stat-number'><?php echo $stats['optimized_images'] + $stats['unoptimized_images']; ?></div>
						<div class='image-stat-description'>Total Cached</div>
							  		  <form method="post" style="display: inline-block" target="hidden-frame">
						<input type="hidden" name="c" value="purge-all-local-image-cache">
						<button type='submit' class='btn btn-xs btn-primary'>Purge</button>
					</form>
						  </div>
					  </div>
					  <div class='col-sm-4'>
						  <div class='image-stat-container'>
						<div class='image-stat-number'><?php echo $stats['optimized_images']; ?></div>
						<div class='image-stat-description'>Optimized</div>
							  		  <form method="post" style="display: inline-block" target="hidden-frame">
						<input type="hidden" name="c" value="purge-optimized-image-cache">
						<button type='submit' class='btn btn-xs btn-primary'>Purge</button>
					</form>
						  </div>
					  </div>
					  <div class='col-sm-4'>
						  <div class='image-stat-container'>
						<div class='image-stat-number'><?php echo $stats['unoptimized_images'] ?></div>
						<div class='image-stat-description'>Unoptimized</div>
							  <form method="post" style="display: inline-block" target="hidden-frame">
						<input type="hidden" name="c" value="purge-unoptimized-image-cache">
						<button type='submit' class='btn btn-xs btn-primary'>Purge</button>
					</form> 
						  </div>
					  </div>	
					</div>
					

					  <?php if ($stats['images_over_quota'] > 0 || true) { ?>
					<p class='text-center' style='margin-top: 20px;'>Issues</p>
					
					
					<div class='row'>
					  <div class='col-sm-offset-4 col-sm-4'>
						  <div class='image-stat-container'>
						<div class='image-stat-number'><?php echo $stats['images_over_quota']; ?></div>
						<div class='image-stat-description'>Images Over Max Filesize (<?php echo ($pegasaas->settings['settings']['basic_image_optimization']['max_image_size'] / 1024 / 1024); ?>MB)</div>
						<a target="_blank" class='btn btn-xs btn-success' href='https://pegasaas.com/upgrade/?utm_source=pegasaas-accelerator-lite-image-panel&upgrade_key=<?php echo $pegasaas->settings['api_key']; ?>-<?php echo $pegasaas->settings['installation_id']; ?>&site=<?php echo $pegasaas->utils->get_http_host(); ?>&current=<?php echo $pegasaas->settings['subscription']; ?>'>Upgrade</a>

						  </div>
					  </div>
					 
					  	
					</div>
					
				
					<?php } ?>
						</div>
<?php } else if ($feature == "logging") { ?>
					<ul>
					  <li <?php if ($feature_settings['log_file_permissions'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='logging_log_file_permissions' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['log_file_permissions'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; File Permissions
					  </li>
					<li <?php if ($feature_settings['log_submit_scans'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='logging_log_submit_scans' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['log_submit_scans'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; Submit PageSpeed Scans
					  </li>
					  <li <?php if ($feature_settings['log_submit_benchmark_scans'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='logging_log_submit_benchmark_scans' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['log_submit_benchmark_scans'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; Submit Benchmark Scans
					  </li>	
					  <li <?php if ($feature_settings['log_process_pagespeed_scans'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='logging_log_process_pagespeed_scans' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['log_process_pagespeed_scans'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; Process PageSpeed Scans
					  </li>
					  <li <?php if ($feature_settings['log_process_benchmark_scans'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='logging_log_process_benchmark_scans' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['log_process_benchmark_scans'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; Process Benchmark Scans
					  </li>						
					  <li <?php if ($feature_settings['log_server_info'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='logging_log_server_info' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['log_server_info'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; Server Info
						</li>
					  <li <?php if ($feature_settings['log_semaphores'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='logging_log_semaphores' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['log_semaphores'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; Semaphores
						</li>
					  <li <?php if ($feature_settings['log_cloudflare'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='logging_log_cloudflare' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['log_cloudflare'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; Cloudflare
						</li>
 						<li <?php if ($feature_seattings['log_varnish'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='logging_log_varnish' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['log_varnish'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; Varnish
						</li>
					<li <?php if ($feature_settings['log_html_conditioning'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='logging_log_html_conditioning' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['log_html_conditioning'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; HTML Conditioning
						</li>
					  <li <?php if ($feature_settings['log_image_data'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='logging_log_image_data' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['image_data'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; CPCSS
						</li>	
						<li <?php if ($feature_settings['log_cpcss'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='logging_log_cpcss' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['log_cpcss'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; CPCSS
						</li>						
 							<li <?php if ($feature_settings['log_api'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='logging_log_api' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['log_api'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; API
						</li>
 						<li <?php if ($feature_settings['log_pickup_queued_requests'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='logging_log_pickup_queued_requests' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['log_pickup_queued_requests'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; CRON - Pickup Queued Requests
						</li>
 						<li <?php if ($feature_settings['log_auto_crawl'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='logging_log_auto_crawl' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['log_auto_crawl'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; CRON - Auto Crawl
						</li>
							<li <?php if ($feature_settings['log_auto_clear_page_cache'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='logging_log_auto_clear_page_cache' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['log_auto_clear_page_cache'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; CRON - Auto Clear Page Cache
						</li>						
 						<li <?php if ($feature_settings['log_script_execution_benchmarks'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='logging_log_script_execution_benchmarks' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['log_script_execution_benchmarks'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; Script Execution Benchmarks
						</li>	
						<li <?php if ($feature_settings['log_caching'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='logging_log_caching' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['log_caching'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; Caching
						</li>						
					</ul>
				<br/>
						
						
				<form method="post" style="display: inline-block" target="hidden-frame">
					<input type="hidden" name="c" value="reset-log-file">
					<button type='submit' class='btn btn-primary'>Clear Log File</button>
			</form> 

			  <form method="get" style="display: inline-block" target="_blank" action="<?php echo PEGASAAS_ACCELERATOR_URL."log.txt"; ?>">

					<button type='submit' class='btn btn-primary'>View Log File</button>
			</form>           							

<?php } else if ($feature == "blog") { ?>
					<ul> 
						<?php if (get_option("show_on_front") == "posts" || get_option("show_on_front") == "layout") { ?>
					  <li <?php if ($feature_settings['home_page_accelerated'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='blog_home_page_accelerated' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['home_page_accelerated'] == 1) { print "checked"; } ?> />
						</form>
						  &nbsp; Home Page Accelerated
					  </li>
						<?php } ?>
					  <li <?php if ($feature_settings['categories_accelerated'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='blog_categories_accelerated' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['categories_accelerated'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; Categories Accelerated
					  </li>										
					</ul>
				<br/>
						
<?php } else if ($feature == "woocommerce" && $pegasaas->is_pro()) { ?>
					<ul> 		
					  <li <?php if ($feature_settings['product_tags_accelerated'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='woocommerce_product_tags_accelerated' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['product_tags_accelerated'] == 1) { print "checked"; } ?> />
						</form>
						  &nbsp; Product Tags Accelerated
					  </li>
		
					  <li <?php if ($feature_settings['product_categories_accelerated'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='woocommerce_product_categories_accelerated' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['product_categories_accelerated'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; Product Categories Accelerated
					  </li>										
					</ul>
				<br/>
						
				    					    							
<?php } else if ($feature == "lazy_load_twitter_feed" ) { ?>
					<div class='hidden-novice'>
<b>Optimization Level:</b><br/>
				<form method="post" style="display: inline-block" class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-status">
<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>					
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="1">Default</option>
				  <option value="500" <?php if ($feature_settings['status'] == "500") { ?>selected<?php } ?>>0.5 second</option>
				  <option value="1000" <?php if ($feature_settings['status'] == "1000") { ?>selected<?php } ?>>1 second</option>
				  <option value="1500"  <?php if ($feature_settings['status'] == "1500")  { ?>selected<?php } ?>>1.5 seconds</option>
				  <option value="2000"  <?php if ($feature_settings['status'] == "2000")  { ?>selected<?php } ?>>2 seconds</option>				
				  <option value="3000"  <?php if ($feature_settings['status'] == "3000")  { ?>selected<?php } ?>>3 seconds</option>				
				  <option value="4000"  <?php if ($feature_settings['status'] == "4000")  { ?>selected<?php } ?>>4 seconds</option>				
				  <option value="5000"  <?php if ($feature_settings['status'] == "5000")  { ?>selected<?php } ?>>5 seconds</option>				
				</select>
			</form>           									
					</div>
<?php } else if ($feature == "lazy_load_images") { ?>
					<div class='hidden-novice'>
<br/><b>Exclude Foreground Images From Being Lazy Loaded:</b><br/>
<form method="post" style="display: block" class='feature-setting-change'>
	<input type="hidden" name="c" value="change-local-setting">
	<input type="hidden" name="f" value="lazy_load_images_exclude_images">
<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>	
	<textarea placeholder="/path/to/image.jpg" class='form-control form-control-full' name='s'><?php echo $feature_settings['exclude_images']; ?></textarea>
	<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
</form>						
					</div>
<?php } else if ($feature == "lazy_load_background_images") { ?>
					<div class='hidden-novice'>
<br/><b>Exclude Background Images From Being Lazy Loaded:</b><br/>
<form method="post" style="display: block" class='feature-setting-change'>
	<input type="hidden" name="c" value="change-local-setting">
<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>	
	<input type="hidden" name="f" value="lazy_load_background_images_exclude_images">
	<textarea placeholder="/path/to/image.jpg" class='form-control form-control-full' name='s'><?php echo $feature_settings['exclude_images']; ?></textarea>
	<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
</form>			
					</div>
<?php } 
	}	
}?>

						</div></div>
					</div>
					</div>

				<?php  } ?>
					</div>
				</div>
				<?php
				}
			}
				?>
				</div>
			<iframe name='hidden-frame' src="" style='display: none;'></iframe>
</div>
<script>
var pending_form = "";	
jQuery(".feature-switch,.feature-setting-change,.prompt-to-clear-cache").bind("submit", function(e) {

	if (jQuery(this).parents(".pegasaas-feature-box").hasClass("locked-for-novice") && jQuery(".pegasaas-dashboard").hasClass("interface-novice")) {
		e.preventDefault();
		return false;
	}
	
	if (jQuery(this).find("input[name='prompt']").length > 0) {
		if (jQuery(this).find("input[name='prompt']").val() == "clear_cache") {
			e.preventDefault();
			pending_form = jQuery(this);


			jQuery("#confirm-update-clear-cache").modal("show");
			jQuery("#update-setting-and-clear").attr("data-feature", jQuery(this).find("input[name='f']").val());


			jQuery("#update-setting-and-clear").bind("click", function() {
				//alert(jQuery(pending_form).find("input[name='c']").val());
				if (jQuery(pending_form).find("input[name='c']").val() == "change-local-setting" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-feature-status"  || 
					jQuery(pending_form).find("input[name='c']").val() == "toggle-local-setting" ||
				    jQuery(pending_form).find("input[name='c']").val() == "change-local-complex-setting" ||
				    jQuery(pending_form).find("input[name='c']").val() == "toggle-local-complex-setting"  ) {

					
					
				} else {
					if (jQuery(pending_form).find("input[name='c']").val() == "enable-feature") {
						jQuery(pending_form).find("input[name='c']").val("disable-feature");
					} else {
						jQuery(pending_form).find("input[name='c']").val("enable-feature");
					}
				}

				
				jQuery(pending_form).find("input[name='cache']").remove();
				jQuery(pending_form).find("input[name='prompt']").val("no-prompt");
				jQuery(this).unbind("click");

				jQuery(pending_form).submit();
				jQuery("#confirm-update-clear-cache").modal("hide");
				if (jQuery(pending_form).find("input[name='c']").val() == "change-local-setting" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-feature-status" || 
					jQuery(pending_form).find("input[name='c']").val() == "toggle-local-complex-setting" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-local-complex-setting" || 
					jQuery(pending_form).find("input[name='c']").val() == "toggle-local-setting") {
					if (jQuery(pending_form).parents(".feature-row").hasClass("feature-disabled")) {
						jQuery(pending_form).parents(".feature-row").removeClass("feature-disabled");
						//alert("class found 3");
					} else {
						//alert("class not found 3");
						jQuery(pending_form).parents(".feature-row").addClass("feature-disabled");
					}					
				} else {
					if (jQuery(pending_form).find("input[name='c']").val() == "enable-feature") {
						jQuery(pending_form).find("input[name='c']").val("disable-feature");
					} else {
						jQuery(pending_form).find("input[name='c']").val("enable-feature");
					}	
				}
			});

			
			jQuery("#update-setting-but-no-clear").bind("click", function() {
				//alert(jQuery(pending_form).find("input[name='c']").val());
				if (jQuery(pending_form).find("input[name='c']").val() == "change-local-setting"|| 
					jQuery(pending_form).find("input[name='c']").val() == "change-feature-status"  || 
					jQuery(pending_form).find("input[name='c']").val() == "toggle-local-setting" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-local-complex-setting"|| 
					jQuery(pending_form).find("input[name='c']").val() == "toggle-local-complex-setting") {
								
				} else {
					if (jQuery(pending_form).find("input[name='c']").val() == "enable-feature") {
						jQuery(pending_form).find("input[name='c']").val("disable-feature");
					} else {
						jQuery(pending_form).find("input[name='c']").val("enable-feature");
					}
				}
				
				if (jQuery(pending_form).find("input[name='cache']").length == 0) {
					jQuery(pending_form).append("<input type='hidden' name='cache' value='do-not-clear' />");

				}
				jQuery(pending_form).find("input[name='prompt']").val("no-prompt");

				
				jQuery(pending_form).submit();
				jQuery(this).unbind("click");
				jQuery("#confirm-update-clear-cache").modal("hide");
		
				if (jQuery(pending_form).find("input[name='c']").val() == "change-local-setting" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-feature-status" || 
					jQuery(pending_form).find("input[name='c']").val() == "toggle-local-setting" || 
					jQuery(pending_form).find("input[name='c']").val() == "change-local-complex-setting"|| 
					jQuery(pending_form).find("input[name='c']").val() == "toggle-local-complex-setting") {
					/*
					if (jQuery(pending_form).parents(".feature-row").hasClass("feature-disabled")) {
						jQuery(pending_form).parents(".feature-row").removeClass("feature-disabled");
//alert("class found 2");
					} else {
						//alert("class not found 2");
						jQuery(pending_form).parents(".feature-row").addClass("feature-disabled");
					}	
					*/
				} else {
					if (jQuery(pending_form).find("input[name='c']").val() == "enable-feature") {
						jQuery(pending_form).find("input[name='c']").val("disable-feature");
					} else {
						jQuery(pending_form).find("input[name='c']").val("enable-feature");
					}	
				}


			}	);	
		
		
		} else {
			
			jQuery(this).find("input[name='prompt']").val("clear_cache");
			return true;
		}
	} else {
		
		jQuery(this).find("input[name='prompt']").val("clear_cache");
		return true;
	}
});
function submit_form(element) {
	jQuery(element).parents('form').attr("target", "hidden-frame")
	jQuery(element).parents('form').submit();
}
	
jQuery(".table-complex-settings select").bind("change", function () {
	jQuery(this).parents("form.feature-toggle-switch").find("input[name=c]").val("change-local-complex-setting");
	jQuery(this).parents("form.feature-toggle-switch").submit();
});
jQuery(".table-complex-settings form.feature-toggle-switch .js-switch").bind("change", function(e) { 
		jQuery(this).parents("form.feature-toggle-switch").find("input[name=c]").val("toggle-local-complex-setting");

});
</script>