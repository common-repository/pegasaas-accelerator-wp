<?php if ($feature == "page_caching") { ?>
<p>In order to reduce the time it takes to serve your web pages, have Page Caching enabled.</p>
<p>The fastest method is the File Based, served via .htaccess method, however for testing purposes, you may wish to go with the File Based served via WordPress method (slower), or turn the feature off altogether. </p>
<?php } else if ($feature == "coverage") { ?>
<p>While full optimization is available for those pages that have the premium boost applied, you can also have extended coverage of foundation optimizations to the remainder of the site
as well as to intermediate (temporary) cache that exists while during the time that an optimization is being processed by the API.</p>
<?php } else if ($feature == "elementor_compatibility") { ?>
<p>This compatibility setting enables features which allow for significantly better mobile page load speed.  There may be websites that are not compatible
	with these settings.  If your Elementor widgets or menus are broken, disable this feature.</p>


<?php } else if ($feature == "speed_configuration") { ?>
<p>Every website is designed differently, and will respond differently to the automated optimzations that Pegasaas provides.</p>
<p>If some functionality in your site becomes broken after enabling Pegasaas at a particular speed setting, try adjusting the speed setting to a lower setting.
<p>If you have some understanding of web performance, you may enable the "Manual Settings" so that you can take the reigns yourself.</p>

<?php } else if ($feature == "api_submit_method") { ?>
<p>By default, the Pegasaas plugin will communicate with the API in a blocking fashion.  This means that the plugin will wait for an "SUCCESS" or "FAILED" response from the API when submitting an optimization.If the plugin detects that the connection takes a long time to complete, or that the connection fails, the plugin will go into a "non-blocking" mode for 
	a period of time.  By shifting to a non-blocking method, it will mean that your web server is not tied up trying to submit to our API if it is overloaded. </p>
<p>You can optionally choose to explicitly use the Blocking method -- this will mean that the submission of optimization requests will wait for the full timeout period -- this may cause your server to experience some load if the API is under load.  This method is considered the most reliable for quick return of optimizations.
<p>You may also explicitly choose the Non-Blocking method if your server is sensitive to resource usage.  With the non-blocking method, background processes will occassionally check to ensure incomplete optimizations are still queued, and if not (meaning the request never reached the API), then the request is re-submitted.</p>



<?php } else if ($feature == "lazy_load_third_party_vendor_scripts") { ?>

<p>Third party vendor scripts can slow down the main thread that renders your web page.  In addition, those same scripts an inject additional scripts
					and resources that can draw out the page load time, and cause your Speed Index, Time To Interactive, and First CPU Idle to be inflated.</p>
<p>When you have lazy loading enabled for these third-party vendor scripts, the script is lazy loaded for a very brief window, to allow the main thread rendering to complete, and then the script is immediately loaded.</p>
					<p>If the script is "analytic" in nature, we ensure that the critical aspects that measure the page start time are not lazy loaded, so that you get the most
					accurate analytics as possible while still experiencing a fast page load.</p>
					<p>If you experience issues with your analytics not recording data as required, disable this feature for the script in question.</p>

<?php } else if ($feature == "lazy_load_google_maps") { ?>

<p>The scripts and iframes used for Google Maps can slow down the main thread that renders your web page.</p>
<p>When you have lazy loading enabled for Google Maps, the scripts are lazy loaded for a very brief window and the iframes are lazy loaded until the user scrolls near them.
	This  allow the main thread rendering to complete quickly, and then the Google Maps resources are loaded.</p>
				

<?php } else if ($feature == "accelerated_mobile_pages") { ?>

<p>By enabling AMP compatibility, we add instructions to the system to auto direct the user, on a mobile device, to AMP pages.</p>
	 
<?php } else if ($feature == "database_connection_refresh") { ?>

<p>On some cloud providers, the databaser cloud connection can hang without terminating the PHP session.  This can result in long timeouts on the server.  To mitigate this issue,
you can enable the "Database Connection Refresh" option which will refresh the database connection if the PHP request takes longer than normal.</p>
<p>Please note that that this is an experimental feature.  If, after enabling this feature, you experience further and/or more pronounced connection problems, please contact the Pegasaas support team.</p>
					
<?php } else if ($feature == "dynamic_urls") { ?>
<p>If you would like to bypass the optimized page cache, when a user visits a page using a dynamic URL (example: https://www.yourwebsite.com/?product=3333&amp;c=add-to-cart), such as when a shopping cart, or dynamically generated forum or events calendar is used, then enable this feature.</p>
<p><b>PPC / UTM Tracking</b><br>If you have this feature enabled, you may wish to exclude certain "query string" parameters from triggering the bypass of optimized cache, such as those used with Pay-Per-Click advertising, or other campaign tracking, which does
	not require change to the HTML of your page. </p>
<?php } else if ($feature == "multi_server") { ?>
<p>If Pegasaas detects that your website runs on multiple servers, then this feature will be enabled.  Delivery of premium optimized content will be delivered to each endpoint via the API.</p>
<?php if (isset($feature_settings["ips"]) && is_array($feature_settings["ips"]) && sizeof($feature_settings["ips"]) > 1) { ?>
					<p>You may remove IP addresses from the list of those detected in the event that one of your servers is removed.</p>
<?php } ?>
<?php } else if ($feature == "display_mode") { ?>
<p>Choose how you want the interface to be styled. </p>

<?php } else if ($feature == "ssl_warning_override") { ?>
<p>If you wish to remove the SSL verification override, toggle this feature off.</p>					
					
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
<p>It appears as though you may have Cloudflare acting as a content delivery network.  In order for Pegasaas to properly clear cache of resources on demand, please specify your Cloudflare API credentials.</p>
		<?php } else { ?>
<p>If you have Cloudflare active, be sure to specify your Cloudflare API key.</p>	
		<?php } ?>

<?php } else if ($feature == "varnish") { ?>	
	<?php if ($feature_settings['status'] == "1") { ?>
<p>If you feel that Varnish is acively used with your web hosting provider, you can leave this enabled, however it is recommended that you disable this feature if you're not sure.</p>
	<?php } else { ?>
<p>If you feel that Varnish is actively used with your web hosting provider, you can enable this feature.</p>
	<?php } ?> 

<?php } else if ($feature == "wpx_cloud") { ?>	
<p>We have detected that you use the WPX platform with their WPX Cloud CDN capabilities... AWESOME!</p>
<p>You can enable the full use of the WPX Cloud CDN with Pegasaas to make your Time-To-First-Byte incredibly fast.</p>
<p><b>PLEASE NOTE!!!</b>  There is one drawback to enabling this feature, and that is that the WPX CDN cache cannot be cleared through
Pegasaas.  You can clear the Pegasaas cache, and then within 10 minutes your updated page should become available.  Optimizations may take up to 30 minutes to perform, when using enabling this feature as there may exist an unoptimized
version of the page in the CDN cache.  The optimization can only be performed if cache doesn't exist in the CDN.  It is recommended that if you are actively clearing cache and optimizing pages, to leave this feature disabled and only re-enable 
	it once you are no longer actively working on your site.</p>
	

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

	<?php } else if ($feature == "preload_web_fonts") { ?>
<p>This feature will instruct the web browser to preload the web font as soon as possible, so that the First Contentful Paint can occur quickly.  Sometimes, if there are a lot
of web fonts used in a web page, preloading all of them can cause the FCP to be slower than if this feature were not enabled, due to the render cycle putting a priority on 
fonts over other resources (such as images) that may be needed for the Above The Fold rendering.</p>


	<?php } else if ($feature == "preload_scripts") { ?>
<p>Normally, when Javascript is "deferred" (to allow for a fast initial render), external script resource loading is a "low" priority in the web browser resource request pipeline.  This can mean 
that the javascript, that should be executed as soon as the initial Document Ready has fired, is delayed.  By enabling preloading of all scripts, the loading of those external 
script resources is started as soon as possible, while still allowing for a fast initial First Contentful Paint.</p>

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
				<p>This feature is typically only used if you are not on a web server that supports the HTTP2 or HTTP3 protocols that allows multi-plex downloads in parallel.</p>
				<p>If combining css is used on a server that supports HTTP2, you can make your web page slower to load and increase your web traffic as combined assets are not
					as cachable as the individual files css which are called upon from every page.</p>

  <?php } else if ($feature == "combine_js") { ?>
				<p>Combining JavaScript resources into a single file can reduce load time by eliminated the overhead incurred by loading resources separately.</p>
				<p>This feature is typically only used if you are not on a web server that supports the HTTP2 or HTTP3 protocols that allows multi-plex downloads in parallel.</p>
				<p>If combining css is used on a server that supports HTTP2, you can make your web page slower to load and increase your web traffic as combined assets are not
					as cachable as the individual files css which are called upon from every page.</p>					
			  <?php } else if ($feature == "minify_js") { ?>
				<p>Minification of JS involves stripping out of unrequired whitespace and comments from your JavaScript files.</p>
					<p>This process can be more problemmatic if the JavaScript was not written properly, so if you run into pages where functionality is lost, try disabling this feature.</p>
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
					<p>Option #1: By default, we use the "Lite" loader, which places the YouTube video screenshot in the page.  This provides for a faster page scroll, however the display of the screenshot may not be compatible with some frameworks.</p>
					<p>Option #2: Alternately, you can choose to use the "IFRAME" lazy load method.  This will provide a slightly faster initial page load (as no resources are loaded on page load), however as the user scrolls down the page, the youtube iframe will load all of its resources regardless of whether the visitor
						clicks "play". This method is not preferrable if you have a lot of YouTube videos on a single page, although it is better than not using any YouTube lazy loading at all.</p>
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