=== Pegasaas Accelerator WP ===
Contributors: pegasaas
Donate link: https://pegasaas.com/
Tags: pagespeed,web performance,image optimization,caching,defer css,webperf,web perf,critical css,minify,lazy load,minification,page caching,browser caching,lazy loading,lazy load images,gzip,css combine,css optimization,js optimization,page speed,pegasaas,speed up wordpress,google pagespeed,load time
Requires at least: 4.6
Tested up to: 5.8
Requires PHP: 5.6.0
Stable tag: trunk
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Web Performance, Simplified -- it's what we do.  If you have limited-to-no web performance optimization experience, or want to consoldate all of your site's web performance plugins into a single plugin, then Pegasaas Accelerator WP is the plugin for you.   Designed to automatically optimize your WordPress pages and posts for Web Performance and Google PageSpeed, Pegasaas Accelerator WP leverages the power of the Pegasaas web performance API to deliver cutting edge web performance optimizations, that work in harmony with one another, without requiring you to know a thing about web performance optimization.

== Description ==

We believe everyone should be able to have a fast website without having to spend countless hours learning how different web performance plugins operate.

Pegasaas Accelerator WP optimizes your WordPress web pages and posts for maximum speed via the Pegasaas API, applying over 30 different web performance transformations, in harmony.  Some of the optimizations automatically performed on your pages include:

* Automatic Detection and Injection of Critical CSS
* Deferral of Render Blocking CSS
* Deferral of Unused CSS (Basic) 
* Deferral of Render Blocking JavaScript
* Minification of CSS
* Minification of JavaScript
* Minification of HTML
* Automatic Image Optimization (Basic)
* Removal of Query Strings from Static Resources
* DNS Prefetching
* Page Caching
* Auto Clear Page Cache
* Automatic Pre-Caching (Optimizing) of your Pages
* Combine CSS (Basic)
* Combine JavaScript (Basic)
* Google Fonts Optimization
* Minimize Critical Requests Depth
* Default Web font-display
* Preload Resources
* Preload Web Fonts
* Preload Scripts
* Lazy Load Foreground Images
* Lazy Load Background Images
* Lazy Load IFRAMEs
* Lazy Load Vimeo
* Lazy Load YOUTUBE
* Lazy Load Scripts
* Lazy Load Third Party Vendor Scripts 
* Enabling of Browser Caching
* Enabling of GZIP Compression
* Disable WP Emoji
* Cache Favicons

**Additional Features**

* Reporting of PageSpeed Scores 
* Reporting of Load Time Metrics and Speed Scores
* Staging Mode
* Global Foundational Web Performance Coverage

**Compatibility**

* Page Builder Compatible: Divi, Elementor, Beaver Builder, X, 7, Thrive
* eCommerce Compatible: WooCommerce, WP eCommerce, Ecwid, Easy Digital Downloads 
* Varnish Compatible
* Redis Compatible
* Cloudflare Compatible

**Web Performance Benchmarking**

So that you know how your web pages are performing, the plugin displays the PageSpeed Score for your pages, posts, and custom enabled post types, as well as the newest web performance metrics available from Google PageSpeed Insights Version 5 and Google Lighthouse, including:

* Time To First Byte
* First Contentful Paint
* Largest Contentful Paint
* Speed Index 
* Time To Interactive 
* First CPU Idle 
* Total Blocking Time
* Cumulative Layout Shift 



**Premium Features Available**

* Additional Coverage (for sites that want more than 10 pages optimized with the premium API web performance optimizations)
* Automatic Global CDN Integration for Static Resources
* Automatic WebP Image Delivery
* External (off-site) Image Optimization
* Automatic Image Resizing
* Advanced Lazy Loading of Scripts
* Advanced Combine JS
* Advanced Combine CSS
* Advanced Deferral of Render Blocking Resources
* Advanced Deferral of Unused CSS
* Optimize on-the-fly for e-commerce pages
* Beast Mode (Autopilot)
* Lazy Loading HTML (for large web pages)
* Auto deferral of Plugin Scripts
* Google Ads Deferral

== Video Tour ==

[youtube https://www.youtube.com/watch?v=f90Kb1AJiIA]

== Installation ==

Use our GUEST (free) API access and start optimizing immediately.  GUEST API includes:

* Coverage of up to 10 pages using the premium API web performance optimizations
* Global coverage using basic web performance techniques
* 100 Monthly Image Transformations: Your images will automatically be optimized and cached to your WordPress website.
* 10 Pages Scanned for PageSpeed and Web Performance Metrics


1. Upload `pegasaas-accelerator-wp` folder to the `/wp-content/plugins/` directory, or upload the provided zip through your WordPress Dashboard "Plugin" panel.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Sign up for GUEST API Access, or if you feel you will need premium optimizations on more than 10 pages, sign up for a PREMIUM API key.
1. Agree to the Terms of Service
1. Choose the number of pages, posts, or post-type objects to accelerate
1. Sit back and wait for the initial optimizations and benchmark and accelerated PageSpeed scores to be retrieved -- this takes approximately 5 minutes


== Frequently Asked Questions ==

= Do I need another caching plugin other than Pegasaas Accelerator WP? =

No, we recommend you do not use other optimization plugins at the same time as Pegasaas Accelerator WP, as doing so may cause a conflict.

= Can I use other web performance optimization plugins while using Pegasaas Accelerator WP? 

We do not recommend you operate other optimization plugins as doing so could cause a conflict in operations, as well as cause your WordPress website to run unnecessarily slow due to a duplication of functionality.

== Screenshots ==

1. Interface
2. Page Scores
3. Settings Panel
4. Page/Posts Panel
5. Install: Start
6. Install: Choose Interface
7. Install: Choose Experience Level
8. Install: Get API Key
9. Install: Terms of Service
10. Install: Choose Pages
11. Install: Set LIVE/Staging Mode
12. Installing
13. Dark Mode
14. Light Mode

== Changelog ==
= 3.8.15 =
**WooCommerce Compatibility (December 29, 2021)**
* Added in functionality to auto-clear catalog on product content update/added to category

= 3.8.14 =
**Fatal error: Uncaught Error: Non-static method PegasaasAccelerator::execution_time() (December 6, 2021) **
* To resolve: Fatal error: Uncaught Error: Non-static method PegasaasAccelerator::execution_time() 

= 3.8.13 =
**WordPress 5.8 Compatibility Check (November 6, 2021)**
* Confirmed plugin is compatible with WP 5.8

= 3.8.12 =
**Bug Fixes (October 13, 2021)**
* Resolved CURL errors when SSL certificate expired.
* Patched issue with logging system that was causing fatal errors.
* Resolved issues that were causing some Divi functionality to break.

= 3.8.11 =
**API Communication Update (September 30, 2021)**
* Issue where optimizations were submitting in an non-blocking manner by default, when they should have been submitted via blocking method, is resolved.  This should provide better stability.

= 3.8.10 =
**InstaPages/Divi Compatibility (September 16, 2021)**
* Added additional support so that 'Instapages' managed through the WordPress system can be optimized with Pegasaas
* Added support for Divi scripts to use special CDN based event triggers

= 3.8.9 =
**InstaPages Compatibility (September 15, 2021)**
* Added support so that 'Instapages' managed through the WordPress system can be optimized with Pegasaas
* Updated javascript deferral handler to v22

= 3.8.8 =
**Division by Zero Error (August 24, 2021)**
* Resolved error in dashboard caused by division by zero issue

= 3.8.7 =
**Updated javascript deferral handler (August 23, 2021)**
* Updated javascript deferral handler to v20
* Resolved warning in dashboard when no third-party analytics are found

= 3.8.6 =
**Web Performance Scores/Nginx Compatibility (June 22, 2021)**
* Revised how cache is called when a web performance score is requested

= 3.8.5 =
**AMP Plugin Compatibility (June 22, 2021)**
* Added output buffering compatibility for the AMP plugin

= 3.8.4 =
**Image Optimization (June 21, 2021)**
* Resolved issue in plugin where the "auto resize images" setting was not being observed for local optimizations

= 3.8.3 =
**General Updates (June 17, 2021)**
* Updated user-agent string for background re-optimization fetches
* Added additional handling for building optimized images on-the-fly for installations not using the CDN (ie Guest API) 

= 3.8.2 =
**Document Conditioning (June 15, 2021)**
* Added document conditioning where html document is missing </body> and </html> tags 
* Added additional handling for building optimized images on-the-fly for installations not using the CDN (ie Guest API) 
* Added cron task to clear stale requests once daily
* Optimized database query while running clear_queued_cache_resources background task 
* Added further compatibility for PHP 8

= 3.8.1 =
**Logging Update (June 10, 2021)**
* Added additional detail for 'long process' events
* Added further compatibility for PHP 8
* Resolved issue with on-the-fly javascript deferral
* Updated javascript deferral handler to v17

= 3.8.0 =
**Strip Web Fonts for Mobile - Custom Setting (June 8, 2021)**
* Added ability to choose the fonts you wish to strip for mobile
* Added new "long process" logging
* Improved auto-cache clear mechanism for when posts are trashed
* Corrected an issue with inline-background-image styles.
* Added compatibility for PHP8
* Resolved issues with pages becoming automaticall un-accelerated 
* Improved the logging function for better readability

= 3.7.0 =
**Auto-Crawl Feature Updated / Updated jQuery Handler (May 26, 2021)**
* Added ability to set the Auto-Crawl 'maximum execution time', 'maximum pages per crawl' and 'frequency' that the auto-crawl occurs.
* Updated the auto-crawl so that requests to the origin site are non-blocking
* Upgraded from jQuery Handler from v8 to latest v16
* Added compatibility for Google Optimize
* Updated the process optimization mechanism so that the plugin fetches the optimization from the API node that processed the optimization rather than go through the primary API endpoint


= 3.6.11 =
**Feature Addition / Compatibility (May 18, 2021)**
* Fixed issue with Google Font imports.
* Added ability to bypass optimization if there is no <html> or <body> tag. This resolves issues with plugins that serve non HTML documents (JSON, PDF, XML)
* Added support for Google Optimize

= 3.6.10 =
**Feature Addition / Compatibility (April 30, 2021)**
* Added ability to strip footer comments from cached/optimized pages.
* Added ability to detect the "Content-Type" of the page/post being requested, and if it is not "text/html" then the system will automatically bypass performing any optimization.  This resolves issues with plugins that serve non HTML documents (JSON, PDF, XML)

= 3.6.9 =
**Bug Fix (April 22, 2021)**
* Resolved issue with error messages that preventing the update of pages/posts.

= 3.6.8 =
**Bug Fix (April 22, 2021)**
* Resolved issue with error messages that preventing the update of pages/posts.

= 3.6.7 =
**General Improvements (April 21, 2021)**
* Updated Whitelabel functionality to allow for plugin renaming and obfuscation
* Updated Elementor compatiblity systems to allow for enabling/disabling exclusion of post-###.css files from the defer-unused-css feature.
* Added support for the "preload web fonts" and "preload scripts" features, which can be enabled/disabled in Manual mode, but are included by default within the Hypersonic auto-pilot mode.
* Added fallback support for sites with large cache, to not scan the pegasaas-cache folder for exact file stats -- this means for an improvement in performance for larger sites on slower servers.

= 3.6.6 =
**Staging Mode Bug Fixes (March 26, 2021)**
* Resolved issue when previewing a page where cache did not exist, which was showing a blank page
* Resolved issue when existing cache was not existing, the system would not auto-build the cache
* Resolved issue when staging mode was active, when cache existed on a staged page, system would continue to re-request the page for every hit
* Added ability to specify a page to use as the template for global critical css for an associated post type, in the "Inject Critical CSS" settings.
* Interface update to show the "edit page level settings" icon, in orange, should page level settings having been modified from the default settings.
* Updates to WPML handling where the page slugs were not fetching properly in the Page Scores view.

= 3.6.5 = 
**Compatibility and Bug Fixes  (March 17, 2021)**
* Added better coverage for WPML by adjusting the PegasaasUtils::wpml_multi_domains_active() function
* Resolved an issue in the local optimization html minification routine which was causing a site to display a blank page.
* Added coverage for the paginated blog pages
* Fixed problem with log rotation

= 3.6.4 = 
**Compatibility (February 23, 2021)**
* Added output buffer compatiblity for the Cookie Law Info plugin 
* Resolved warnnings experienced on Siteground in the "installation wizard"

= 3.6.3 = 
**Compatibility (February 22, 2021)**
* Added dashboard compatibility for the Nextgen Gallery
* Added output buffer compatiblity for the Cookie Law Info plugin 

= 3.6.2 =
**Cloudflare Cache Clear / Updated Logging System**
* Resolved an improper cache clearing call to the Cloudflare API.
* Updated the new login system furhter to allow for searching.

= 3.6.1 =
**Broken Images/ Cloudflare Conditioning**
* Bug fix for broken images that appeared when logged in and viewing pages in the website that used images that had width and height defined with "px" in the attributes.
* Added bootup code to the plugin that communicates with Cloudflare API to adjust certain settings to make Cloudflare more compatible with Pegasaas

= 3.6.0 =
**Performance Improvements**
* Upgraded the semaphore system to prevent long blocking in situations where blocking was not necessary
* Added new database table for semaphores, rather than relying on internal WordPress data handling, for better reliability
* Corrected Minification Issue with non-standard closing comment tag
* Added updated logging system

= 3.5.3 =
**Performance Tweaks**
* Re-enabled new caching mechanism for the Pegasaas large data structure
* Reduced the maximum semphore timeout from 5000ms to 1000ms
* Added in Google Maps option to the Lazy Loading section

= 3.5.2 =
**Cloudflare Bug Fix / UX+UI Updates (Jaunary 6, 2021)**
* Fixed issue where the plugin was not fetching the default zone from Cloudflare on sites that used the www subdomain in their domain name.
* Improved UX of the Page Scores panel to show the Post Title, time of last cache and web performance scan, as well as hide the "edit page level settings" icon until the user hovers over the row
* Added ability to set the "Prioritize Re-Optimization" setting on a page-by-page basis, via the edit "page/post" page.

= 3.5.1 =
**Search Filter Bug Fix (December 31, 2020)**
* This patch includes a fix for the Pegasaas dashboard search filter that was brokein in 3.5.0, as well as a number of smaller code tweaks.

= 3.5.0 =
**Improved Installer, Prioritized Page Optimization, Performance Improvements (December 30, 2020)**
This minor release improves a number of UI and UX aspects of the plugin.

**Improved Installer**
* The installer now has a separate step for the compatibility checker, which is now more comprehensive.  
* In many cases, the compatibility step can now also be skipped.
* The installer will also jump immediately to the dashboard upon fetching/validating an API key, cutting the "initialization" time down considerably.

**Prioritized Page Optimization (Beta)**
* Premium API plans will now include the ability to mark pages to be prioritized above the rest in a site, so that those pages are optimized ahead of the rest of the site.

**Performance Improvements**
* Added a large data structure to the cached data storage system.  This speeds up the /wp-admin/ dashboard load by as much as 1 second/1000 pages.  This may only be noticable for sites with over 2500 pages.
* Changed some API communication methods to be non-blocking
* Made re-optimization requests less intense on the website's web server by maximizing the number of requests in the queue.

**Automatic Web Performance Rescans**
* Re-activated this feature from v3.1
* Re-scans now happen automatically on re-optimization (provided not a significant number of web performance scans haven't been submitted in the last 24 hours)

= 3.4.0 =
API Submit Method (December 11, 2020)
This minor release improves the mechanism that submits the optimization request to the Pegasaas API. 

Until 2020 (prior to v3.0) the default behaviour used non-blocking calls from the plugin to the API.  In 2020, this behaviour was switched to a blocking call in order to improve reliability. 

In version 3.4.0, the submission system will be a hybrid mechanism: blocking unless the plugin experiences a communication fault when submitting an optimization request.  If a timeout or error is encountered, the plugin will switch to a non-blocking submission method which includes a background check (run every 10 minutes) to ensure that any pending requests that were submitted via the non-blocking mechanism were indeed received by the plugin.

This new setting (found in Settings -> Miscellaneous -> API Submit Method) is configurable -- you may choose between the new "Auto (Hybrid)", "Blocking", or "Non-Blocking" methods.  You can also now set the timeout duration for blocking api requests, as well as the the time period that the system will enter into a "non-blocking" mode, if you're using the new default method.

= 3.3.12 =
Redirection Bug Fix (November 17, 2020)

= 3.3.11 =
Bug Fixes (November 16, 2020)
* Resolved issue where the number of pages accelerated sometimes showed as 0 in the dashboard

= 3.3.10 =
Elementor Compatibility (October 30, 2020)
* Resolved issue in some Elementor interfaces where the Elementor post/page editor would not fully load

= 3.3.9 =
WPX Cloud and jQuery Updater Compatibility (October 16, 2020)
* Adjusted the Cache-Control parameters for WPX Cloud so that the wp-admin and wp-login.php endpoints are not cached
* Added jQuery Updater script signatures for better reliability in jquery script execusion when deferring javascript

= 3.3.8 = 
Elementor Compatibility / Small Bug Fixes (October 6, 2020)
* Added new feature which provides a much needed boost for sites powered by Elementor.  This feature is automatically enabled, but can be disabled in the Settings->Compatibility section.
* Resolved issue where some sites were having "unoptimized" pages automatically optimized.
* Set the upper limit on the number of web performance scans, to return as a part of the score, to 1000 (as greater than 1000 can cause memory issues).

= 3.3.7 = 
Compatibility Improvements (September 18, 2020)
* Resolved issue where when trailing slashes were disabled in the site, the .htaccess instructions plugin would not pull from cache properly.
* Added better support for WPML and WooCommerce Multilingual plugin
* Corrected issue with persistant long-loading background AJAX request.
* Added support for WPX Cloud CDN -- you can now enable the super fast WPX Cloud CDN for ultra fast TTFB if you're hosted with WPX

= 3.3.6 =
Compatibility Improvements (September 11, 2020)
* Slow Server Compatibility: increased compatibility test timeout to allow for slower web servers
* Added compatibility for Really Simple SSL content filter

= 3.3.5 =
General Improvements (September 9, 2020)
* Added Largest Contentful Paint, Total Blocking Time, and Cumulative Layout Shift metrics to the interface
* Fixed issue with sites that use a trailing slash in their URLS that were not auto redirecting to the appropriate page when the trailing slash was left off
* Resolved issue which was causing WP Site Health to have warnings related to the JSON API.

= 3.3.4 =
Dashboard Improvements (August 28, 2020)
* Improved load time of interface by caching some complex data structures in WordPress database
* Added ability to re-optimize pages without clearing cache
* Added ability to log when certain critical data structures are updated/deleted
* Added better cache handling for LiteSpeed servers in .htaccess file
* Improved handling for output buffer fetch

= 3.3.3 =
Additional Compatibility for WooCommerce, WPML, Caldera Forms (August 3, 2020)
* WooCommerce: Cart, Checkout, and My Account  pages are now automatically not cached
* WooCommerce: Improved performance when upating orders
* WPML: Now supports multi-domains configuration
* Caldera Forms: Better compatbility for Caldera Forms API

= 3.3.2 =
Additional Compatibility for AMP (July 9, 2020)
* Prior to this update, some of the advanced features of AMP were not being applied to the page.

= 3.3.1 =
Interface Performance Improvements (July 9, 2020)
* Adjusted compatibility with the "redirection" plugin which improved interface load time.
* Added remote testing capability to determine server reponse time of the server.
* Adjusted description for compatibility check failure when a blank response is returned from the test submission.  Description now states that the server is too slow.

= 3.3.0 =
Auto Pilot Modes (June 26, 2020)
This minor release adds the new "Auto Pilot" setting to the plugin.  Upon installation, the user now has the option of chosing from "basic", "supersonic", "hypersonic" or "beast mode" auto pilot modes.  Should the user wish, after initialization, the user can put the system into "manual configuration" mode.

The new "Beast Mode" setting gets the fastest speeds available, but some of the features (HTML Lazy Loading, Deferring Unused Javascript) are considered highly experimental and may not work with every website, so this mode should be used with caution.

There have been a number of other updates to the plugin since the last release, including support for the "WebP Express", "BunnyCDN", and "WP Daddy Builder" plugins.

= 3.2.2 =
Added ability to handle very large web pages. (May 25, 2020)

= 3.2.1 =
Resolved bug that caused Elementor to not be able to edit pages. (May 15, 2020)

= 3.2.0 =
Upgraded Web Performance Data Fetch Method (May 14, 2020)
* This minor update added support for a "notify and fetch" method of submitting web performance data to the plugin.  In previous versions, the API would submit the web performance data back to the plugin directly once a scan was complete. 
* In this new version, the plugin is notified that the results are ready and then the plugin immediately fetches the data.  The reason that we moved to a "notification and fetc"h (vs "direct push") method was that some web servers were blocking the web performance data submissions, and thus the installation would never complete.
* While this will not resolve all hung installations, this should account for a large number of failed attempts to get the plugin initial scans completed.

= 3.1.14 =
Compatibility for Third Party Vendor Scripts, Plugin, and Hosting (May 7, 2020)
* Added support for third-party vendor scripts (Subscribers, Ngageics)
* Added support for Optimole Image CDN
* Resolved issue with Resty systems that block wp_remote_request requests using a stale user agent string -- this caused the compatibility checker to fail.

= 3.1.13 =
Third Party Vendor Scripts (April 23, 2020)
* Added support for third-party vendor scripts (Olark, Bibblio)

= 3.1.12 =
Third Party Vendor Scripts & Cache Clearing Issues (April 16, 2020)
* Added support for six new third-party vendor scripts (Facebook SDK, AnyClip, Social Warfare, ConvertBox, PubGuru, Media.net)
* Removed calls to API to clear API remote cache on Combined CSS and Deferred JS as those resources now exist with timestamped filenames

= 3.1.11 =
Compatibility (WP 5.4) and Third Party Vendor Scripts (April 13, 2020)
* Confirmed compatibility with WP 5.4
* Added Driftt as a third-party vendor script that can be lazy loaded
* Added TrustArc as a third-party vendor script that can be lazy loaded

= 3.1.10 =
Code Conditioning / Permalink Issue (April 7, 2020)
* Resolved incorrectly formatted <script> tags that were impacting the execution code that was responsible for the deferral javascript
* Resolved issue of sites with no trailing slash in the permalink not auto-accelerating pages 

= 3.1.9 =
Caching Issues (April 2, 2020)
* Resolved an issue in the dynamic extended-coverage page caching where sites that do not use a trailing slash could not clear cache on some pages.
* Added detection of Google Tag Manager script injected by Google Site Kit, for the Lazy Loading -> Third Party Vendor Scripts feature.
* Added conditioning for Thrive Architect pages that use the javascript resizing of regions in the page, which is not compatible with non-render-blocking javascript.
* Resolved an issue with Elementor built CSS files being cached with Cloudflare

= 3.1.8 =
Compatibility (March 24, 2020)
* Added the ability to auto clear locally optimized elementor CSS cache files
* Added browser cache busting on elementor CSS cache files (is arbitrarily enabled but will add it as an optional feature in the future)
* Added functionalty to allow the Pegasaas account dashboard to request aggregate site web performance data from the plugin

= 3.1.7 =
Compatibility (March 20, 2020)
* Resolved issue with Google web fonts not loading on dynamically generated pages
* Added compatibility for the Thrive Architect system, to allow rendering in "preview" mode as well as added conditioning to the HTML to allow for non-render-blocking javascript to display the page in a smoother fashion.
* Added compatibility with an "infinity scrolling" component that was loading HTML through a non-standard method.
* Added a feature to enable "database connection refreshing" for platforms that exhibit long timeouts possibly due to cloud database connections being hung

= 3.1.6 =
Compatibility (March 13, 2020)
* Added the ability to purge all local cache for those sites running on WordPress.com hosting
* Added url decoding of file paths on images that are optimized and stored locally (basic image optimization)

= 3.1.5 =
Compatibility (March 11, 2020)
* Added handling for the "schema-app-structured-data-for-schemaorg" plugin to support the "RemoveMicrodata" output buffer handling
* Resolved PHP "Notice" incurred by PegasaasUtils::get_permalink() line 643
* Corrected broken deferal of inline script blocks, on dynamically generated pages (or when logged in), when website is using Cloudflare
* Corrected broken deferral of script tags when website has Cloudflare, on dynamically generated pages, which was impacting the ability to edit pages with Elementor

= 3.1.4 =
General Bug Fixes (March 6, 2020)
* Patched conflict issue with "404 to 301 - Redirect, Log, and Notify 404 Errors" plugin
* Added a longer timeout to the "compatibility checker" in the installation wizard
* Patched issue with delivery of the JS deferral handling on dynamically rendered pages
* Removed text/html from browser caching .htaccess instructions as it may have been causing a conflict on some hosting platforms

= 3.1.3 =
This update improves the dynamically generated pages javascript deferral and adds ability to clear page-level critical-css:
* Latest version of JS deferral in the dynamically generated pages upgraded to revision 8
* Ability to clear all page-level critical css added to Advanced tab

= 3.1.2 =
* Compatibility with Rate My Post plugin added.
* Resource mapping of external JS files in the dynamically generated pages is resolved.
* Latest version of JS deferral in the dynamically generated pages has been upgraded.

= 3.1.1 =
Server Performance Related Fixes & Log Management Handling
* Temporarily rolled back changes that invoked auto-rescan of web performance scan as this feature was causing heavy load to some servers.  This feature will be re-introduced as an opt-in feature in v3.1.2
* The logging system now allows you to specify which level of PHP errors to optionally log (Deprecated/Warning/Notices).  Optional messages are disabled by default.  You can also now specify to only log Pegasaas related issues.
* Resolved an issue with a comment being displayed at the end of the WP-CLI output when there were warning/notices due to the level of logging set in PHP

= 3.1.0 =
This minor update adds stability improvements to the plugin:

**Installation Wizard Compatibility Check**
* First step on installation now does a quick check to ensure that there are no third party plugins that are known to cause conflicts, and that the plugin and API can communicate effectively

**Auto Web Performance Rescans*
* When a page is explicitly re-optimized, it will also auto-request a new web performance scan
* If a page is re-optimized through a cache-clear, if the last performance scan was over a month ago, a new web performance scan will be automatically requested

**Log File Rotation**
* The log.txt file will now be coped into the rotated into the "logs/" folder where 5 days of log files will be stored.  This will be performed at midnight, daily

**WP-JSON with WP-Nonce Compatibility**
* The system will now auto handle expired "wp_rest" wp-nonce for when making requests of the wp-json API

**General Bug Fixes**
* Resource mapping in the critical path CSS in the dynamically generated pages is resolved.

= 3.0.5 =
Divi Clear Cache Bug
* This patch addresses a bug that was introduced in 3.0.0 to handle the cache clearing in websites running Divi.

= 3.0.4 =
Deferral of inline script block bug fix.
* This patch addresses the situation where there are inline script blocks preceeding a script tag with src attribute.  In this situation, the inline script blocks, that preceeded the first external script reference, were not executing.

= 3.0.3 =
This update patches a few error messages that were being displayed in the dashboard and in the on-the-fly optimizations.
* Dashboard warning involving the CDN tab
* On-the-fly warning about lazy loading scripts

= 3.0.2 =
This update patches small issues introduced with version 3.0.0 including:
* Added .xsl files to the list of file types automatically excluded from being optimized through the extended coverage
* Added ability to exclude categories and tags from extended coverage
* Added auto-clearing of extended coverage cache when caching is disabled on extended coverage
* Small CSS changes to the interface to make links clearer in the Installation Wizard

= 3.0.1 =
This update patches small issues introduced with version 3.0.0 including:
* Incomplete cache clearing
* Broken SVG Images
* Broken JavaScript issues in performed foundational optimizations

= 3.0.0 =
Version 3.0 has been four months in the making, and as such the list of changes and improvements are vast.   Here are a summary of the updates:

* NEW *

* Staging Mode - for live production websites that want to test the plugin and how it performs prior to going live
* Global Basic Coverage - basic converage of foundation web performance features for those pages that are not explicitly declared with premium acceleration
* Generic CDN for Whitelabel Agency plans - automatically uses a generic domain name to obfuscate the fact that the CDN is run by Pegasaas
* Lazy Loading for 3rd Party Vendor Scripts - everything from Google Tag Manager to Google Maps, LiveChat to Tawk, some 25+ vendor scripts can now be briefly lazy loaded in order to shorten the initial load time of your web pages
* Multi-Server Support - if you run your website via multiple load-balanced AWS websites, the plugin will communicate with our API which will in turn communicate with each web server that you run, to make sure that the optimizations are deployed to each server, and that cache is consistent between the installations
* Clear CDN Cache Elements - if you're using the CDN, you can now clear individual files from the global CDN
* Projected Bounce Rate Reduction - we now estimate your bounce rate reduction based upon market research that for every second saved, bounce rate tends to decline by 7%
* Projected Conversion Rate Improvement - we now estimate your conversion rate improvement based upon market research that for every second saved, conversion rates tends to improve by 12%
* CDN Cache Busting - you can now enable this feature which will append a timestamp to the end of any CSS or JS file, if you are one to make regular updates to those types of files in your site
* Support for new inline-script-block deferral  - this replaces the previous method of externally compiled javascript files
* Support for Lazy Loading of dependent script blocks
* Caching Enabled for AMP pages
* Defer Unused CSS - you can now specify which "action" you want to trigger the loading of the deferred CSS files (click / scroll / click or scroll)
* Lazy Loading Iframe Exclusions - you can now specify URLs of iframes to exclude from lazy loading
* Retain Image Metadata - You can now opt to retain the image exif metadata in your pages when optimizing them through the CDN

* UPDATED *

* Pickup Stale Requests - the plugin will now check the API for requests that may have not been received
* Interface - major overhaul of the UI which includes faster load time of pages in the dashboard
* Mega Menu - global pagespeed scores are no longer displayed as calculation of the metrics was causing the dashboard to load slowly for those sites with 500+ pages
* Cache Clearing - this is now done as a background process so as not to incur timeouts on the dashboard.
* Settings Page - major overhaul of the UI and functionality which now uses background AJAX calls to speed up the saving of features
* Nginx Support for domain aliases - the plugin will now first check that the domain name that is being used to request the page is the primary domain name in the WordPress settings before serving the cache.  If it finds that the domain names do not match, the plugin will allow WordPress to do a redirect to the primary domain.

= 2.8.19 =
Compatibility (Yith Woocommerce Wishlist, CSS)
* This update adds compatibility for the Yith Wishlist plugin by detecting if Yith is in installed and if the current session's user has a wishlist.  If the user has a wishlist with at least one itme, then cache is bypassed and the page is dynamically generated.
* A minor change to the CSS for the post-pages panel was made to accomodate those plugins that use a "search" function (search field had white text on white background).

= 2.8.18 =
Output buffer handling error fixed (introduced in 2.8.16)

= 2.8.17 =
Output buffer handling error patched (introduced in 2.8.16)

= 2.8.16 =
Excluded Pages Warning Output Patch / Siteground HTTPS redirect bug fix 
* Resolved warning output on new pattern matching known urls that should be excluded that contained a reserved regular expression character
* Resolved an issue with the HTTP->HTTPS redirect for Siteground servers where the path to the requested URL was being excluded from the redirect

= 2.8.15 =
Excluded Pages and Interface Improvements 
* Added support for regular expression pattern matching for entries listed in Excluded URLS 
* Resolved dashboard conflict with the AliDropship plugin that also uses bootstrap

= 2.8.14 =
Improved Cache Clearing
* Resolved error for some sites clearing Cloudflare cache.
* Fixed issues with blank entries for Lazy Loaded JavaScript on the settings page.

= 2.8.13 =
Improved Cache Clearing
* Resolved cache clearing issue for the Pagely platform

= 2.8.12 =
Improved Cache Clearing
* Resolved lengthy cache clearing when saving pages

= 2.8.11 =
Improved Cache Clearing
* Resolved lengthy cache clearing when saving menus
* Added support to auto-clear Custom Post Type archive pages when a Custom Post Type is created/updated
* Added page-level ability to set automatic clearing of cache (Daily, Weekly, Bi-Weekly, Monthly)
* Added ability to clear global cache bi-weekly (in addition to Weekly, and Monthly)

= 2.8.10 =
Improved Cache Clearing and Data Handling
* Reduced cache clearing time on large sites by up to 80%
* Improved the data handling for object meta data to improve load time on the interface

= 2.8.9 =
Improved Wordfence Compatibility, Dashboard Improvements
* Developed and added Pegasaas Accelerator as a whitelisted service for Wordfence 
* Improved the priority of requests submitted from the plugin to the API
* Improved the ordering of pages/posts shown in the dashboard
* Fixed bug when clearing cache on Flywheel hosting

= 2.8.8 =
Added/fixed e-commerce compatibility
* Fixed broken support for  e-commerce plugins when serving cache via the fallback in-plugin method, where the existence of woo-commerce and wp-ecommerce was not being detected due to an early call to the PegasaasCache::check_cache().
* Added support for the Easy Digital Downloads ecommerce plugin -- plugin will now bypass cache if it detects there are items in the EDD cart.

= 2.8.7 =
Patched issue with POST submissions
* Submissions to pages with POST that had zero post arguments would bypass cache and invoke a rebuild of the already optimized page.

= 2.8.6 =
Patched issue with on-the-fly optimizations for sites hosted with WPMU DEV
* The typical 404 page served from nginx is different on WPMU, so the plugin was not detecting the ngnix environement and as such was not passing along the appropriate condtions for the API to format the code accordingly.

= 2.8.5 =
Patched issue with on-the-fly optimizations for sites in subfolders
* Handling was added for sites that exist within a subfolder (example: /blog/) -- previously, the CSS/JS/Images were unable to be fetched due to a filename mapping issue.

= 2.8.4 =
API IP Addresses for Wordfence Firewall Conditioning
* A recent update to the API IP list had been corrupted.  In the event to deal with this condition in the future, we have added two fields to the data that is deleted when the plugin is uninstalled.

= 2.8.3 =
WordPress.com Hosted Sites now Supported
* The plugin is now set up to install on WordPress.com hosted sites (requires the WordPress.com "Business" plan or higher to install plugins).

= 2.8.2 =
Resolved output issue with 404 handler
* This patch addresses an issue, introduced in v2.8.0, where the 404 handler was being interrupted.
* Included in this patch is beta functionality for the Cloudflare 'API Tokens' feature, which is currently in development at Cloudflare.

= 2.8.1 =
Fixed issue of admin bar being cached
* This patch resolves an issue existing in v2.7 where the admin bar was being cached and served if a logged in user should hit a page that had acceleration enabled but did not yet have a cached optimized version of the page.

= 2.8.0 =
**WordPress Multisite Network Compatibility (Using Subdirectories)**
* Pegasaas now supports the WordPress Multisite Network feature, where you have a network administrator that manages multiple websites, or multiple versions of the same website, for testing development purposes.  
* Patched issue where sub-categories were not properly mapped for cache clearing or for being included in the pages accelerated total
* Patched issue where total number of pages accelerated was not calculated correctly, which in some cases prevented some installations from accelerating new pages/posts

= 2.7.4 =
**Exposed PHP Notice**
* This patch supresses a PHP Notice message that was displaying on some servers.

= 2.7.3 =
**Microsoft Windows IIS Permalink Check / Exposed PHP Notice**
* This patch resolves an issue with the Permalink check on installation, when the website is served by a Microsoft IIS system.
* In a recent update, certainly handling for query string arguments was modified.  This in turn caused a PHP Notice to be exposed for those systems that display PHP Notices.  This has now been resolved.

= 2.7.2 =
**Debugging Improvements**
* This patch includes further updates to resolve cache folder mapping issues introduced in 2.7.0

= 2.7.1 =
**Cache Folder Bug Resolution For Installations that Reside in Subfolder**
* This patch resolves a bug introduced in 2.7.0 for installations that reside within a subfolder where the "content" folder was not mapped correctly.

= 2.7.0 =
**WordPress Multisite Network Compatibility (Using Subdomains)**
* Pegasaas now supports the WordPress Multisite Network feature when using subdomains, where you have a network administrator that manages multiple websites, or multiple versions of the same website, for testing development purposes.  This new level of compatibility required changes across the entire plugin to properly map the location of cache files which needed to exist in parallel for multiple sites.
* Alternate "wp-content" folder location is now supported.

= 2.6.0 =
**Advanced Performance Metrics / Combine CSS and Combine JS Upgrade **
* Advanced Performance Metrics (Time to First Byte, First Contentful Paint, First Meaningful Paint, First CPU Idle, Speed Index, and Time To Interactive) are now available for each page displayed in the Pegasaas Accelerator WP dashboard for those using the "Advanced" display level.  Intermediate will show a limited set of performance metrics.
* The Combine CSS feature was upgraded for Premium subscribers -- you now will have the ability to specify the number of stylesheets to serve -- by default, the system will build a single massive stylesheet for Guest API users. 
* A new feature, Combine JS, performs the same mechanism for JavaScript as the Combine CSS feature does for stylesheets.  This feature is currently tied to the "Deferral of Render Blocking Javascript", but will be expanded in the future should someone want to combine JS but not defer it.  By default, the system will build a single JS file, however Premium subscribers will have the ability to split it into up to 4 files.
* Additional support for a deprecated cURL error code has been added for those servers running older versions of cURL.
* Bug fixes to the dashboard that displayed "NAN" to the "PageSpeed Change" value once a PageSpeed scan was complete.

= 2.5.0 =
**Script Lazy Loading**
* For those users of the Guest API, we have opened the up basic script lazy loading feature.  If you have a script in your page that we've determined can be reliably lazy loaded until the user scrolls, then it will be.  You can, of course, disable this feature if you find it interferes with the operation of your page.
* If you have a premium subscription, you can take full advantage of the more robust capability of this advanced system.  For each script on your web page, you can specify if and when it is lazy loaded.  Be forewarned: lazy loading a script which has dependants can cause functionality in your page to break.
* This update also includes analytics opt-out code for pages that are requested via the ?accelerate=off query, which is typically used for Google PageSpeed Insights requests.
* Better support for sites protected with a password using HTTP AUTHENTICATION was included in this release.

= 2.4.2 =
**Benchmark PageSpeed Scan Bug**
* Resolved a bug introduced in version 2.4.0 where the system did not detect an existing Benchmark PageSpeed scan, which resulted with the interface indicating "Queued" scans, and then the request of unnecessary scans from the API.

= 2.4.1 =
**Lazy Loading Scripts added to Standard Edition for Premium API subscribers**
* Adding custom lazy loaded scripts was previously only available in the Pro Edition of the plugin.  It is now avalable in the Standard Edition for Premium API subscribers.

= 2.4.0 =
**Data Management Improvements**
* This update dramatically improves the load time of the Pegasaas Accelerator dashboard (and by proxy, the entire WordPress system when using Pegasaas)
* Bug fix that resolved warning messages generated by the PegasaasInterface::admin_posts_column_content() function.
* Bug fix that resolved the modal overlay on first load of the Pegasaas dashboard, if using a mobile device.

= 2.3.9 =
**Compatibility with WooCommerce API**
* Previously, Pegasaas would append some code to the response for wp-json/ and wc-auth/ requests, causing the wc-auth requests to fail.  This release fully resolves this issue.

= 2.3.8 =
**Cache and Data Management**
* This update includes a few small improvements to the cache handling system related to mapping of URLs to the appropriate cache resource.

= 2.3.7 =
**Compatibility (mod_pagespeed, Apache htaccess) Handling**
* Instructions are automatically added to the .htaccess, disabling mod_pagespeed.  This replaces the previous behaviour where the end user was required to manually disable the mod_pagespeed plugin themselves via their web hosting control panel.
* Added conditional satements to check for mod_header and mod_setenvif Apache modules to prevent a server error in the event that either of those Apache modules are not installed
* Created a mechanism to test whether the new .htaccess file will cause a 500 Internal Server Error, prior to saving the updated .htaccess file to the root.

= 2.3.6 =
**Caching Related Issues Resolved**
* This update removes the date/time check for cache, as this is no longer a relevant reason to invalidate cache as cache is already auto cleared through the cron system.

= 2.3.5 =
**Caching Issue Bug Fix**
* This update resolves a bug that was caused by an improperly assessed cache date for those installations which use a non-typical date format.

= 2.3.4 =
**Support for WP Dashboard when Port Explicitly Declared**
* This update adds support for a situation where the WP dashboard is accessed using a port #, such as https://somewebsite.com:443/wp-admin/

= 2.3.3 =
**WP Engine Compatibility Patch**
* Caching: Additional support has been added to clear invididual page level cache.
* Dynamic Resource Optimization: The WP Engine hosting platform was preventing the plugin from optimizing resources which were to be optimized on-the-fly.  Modifications had to be made to the file naming convention (.optimized-css rather than .css-optimized)

= 2.3.2 =
**Compatibility Patch**
* Avada: The plugin will now resolve an issue where some Avada code was executing in a cart-before-the-horse situation in the main.js file.  This resolves javascript errors resulting from incomplete rendering of the page.
* WooCommerce: Advanced support for WooCommerce Product Caterores/Tags has now been added to the Premium Edition.
* Flywheel: Initial support for the Flywheel hosting platform has been added.  Flywheel maps the /wp-admin/ folder from a symbolically linked /www/.wordpress/wp-admin/ folder 
* Cloudflare: Further support for the Cloudflare system was added to ensure that temporary cache is cleared when an optimization has been submitted to the plugin from the API.

= 2.3.1 =
**Simplified operation of some features**
* Deferral of Render Blocking JavaScript features have been simplified for the Standard edition.
* Deferral of Unused CSS feature has been simplified for the Standard edition. 
* Removed the "recommended improvements" icon / popup
* Simplified up the "more" (vertical ellipsis) button beside the PageSpeed scores

= 2.3.0 =
**Added suport for a new 'Development' mode in the Premium edition**
* The new premium edition Development mode is supported with updates to this release.
* Contextual Help added to the top of the pages/posts panel
* Clarified the LIVE/Diagnostic mode with a new switcher at the top of the dashboard interface
* Added improved compatibility with W3 Total Cache

= 2.2.19 =
**Database Performance Improvements**
* Reduced the load time of the dashboard by half by adjusting a number of database queries.

= 2.2.18 =
**Improved WP-CLI Compatibility**
* Resolved an issue with the plugin that was caching output run by WP-CLI in rare situations.

= 2.2.17 =
**Novice/Intermediate/Advanced Mode**
* Added a new step to the installation wizard so asking web performance experience level.  Interface is tailored to the level of experience -- streamlining it for novice users.

**Static Resources Cache Handling**
* There is now a new tab in the dashboard labelled "Cache", which provides the ability to view all of cached static resources, and their level of optimization.  User can also delete individual items, as well as re-optimize the resource.

**Improvements**
* Resolved issue with installations being deployed to Bluehost as there was a recent change to the Endurance caching plugin which prevented the installation wizard from starting.
* Added improved support for subdirectory installations.

= 2.2.16 =
**Custom Pages Installation Bug**
* Resolved a problem with the installation routine when a user selected a custom selection of pages, that included the home page.  In this scenario, all pages then had the acceleration enabled. 

= 2.2.15 =
**Head, Footer, and Post Injections Compatibility **
* Resolved an issue where the "hefo_callback" was being suppressed by the Pegasaas Accelerator WP buffer capture system. 

= 2.2.14 =
**Compatibility**
* When an image was being fetched for optimization, there was an issue that if the server did not respond with a source file, even though the file existed, a placeholder image of zero bytes was being stored.  This behaviour has now been updated so as to just return a 404 result and exit early with no optimization.
* We have changed how the plugin responds to conflicting plugins, so that third-party caching plugins are currently considered conflicting if Cloudflare is installed.  When more than one caching plugin is present (Pegasaas + one other) the ability for the system to succesfully fetch optimized pages is diminished.  We have plans to resolve this issue, but for the time being, it is best to consider other third party caching plugins not compatible in this scenario.
* At installation, we are now prompting for Cloudflare credentials if the system detects that Cloudflare is present.
* We have added compatibility with the "Accelerated Mobile Pages" plugin to allow mobile redirection.
* Additional support for Pagely hosting (through SSL detection) has been added.
* We are now detecting possible hung installations through the installation wizard.  If the system detects a possible hung installation, it will notify the Pegasaas team so that we can investigate, while attempting to retry.  If, after the typical initialization duration, the optimization request are not performed as anticipated, it will display a message indicating that the initialization did not proceed, that the Pegasaas team has been notifiede, and to initiate a support ticket.
* A new system to notify you when you are runing low on credits is now in place.

= 2.2.13 =
**Compatibility**
* Added support for permalinks that do not have a trailing slash /, and confirmed support for permalinks formatted in the /index.php/path/to/resource/ manner.
* Resolved the case of the system incurring a 500 server error when initialized on a site with a space in the document root folder (or parent folders).  The .htaccess contained two RewriteCond instructions which each contained a path that needed to be wrapped within quotes.

**Lazy Loading**
* Added ability for the user to define image paths to be excluded from lazy loading foreground and background images

**Static Resource Handling**
* Added handling for situations where static resources are not handled by the WordPress 404 system -- previously, the plugin could not optimize CSS/JS/images if static resources were excluded from being handled by the WordPress 404 system.

= 2.2.12 =
**Installation Routine**
* Added support form that is now accessible in the event that the installation hangs
* Test for invalid SSL certificate and warn that installation cannot be complete until the SSL certificate issues is resolved
* Added new test for a non-existant .htaccess file, which attempts to create a .htaccess file if the environment supports it, based upon the permalink settings of the site
* Added support for installations that allocate 1GB+ of memory to PHP

**Image Optimization**
* Added support for installations that migrate from a website that uses a /~userdir/ 
* Resolved possible conflict of the lazy loading images code with other scripts that use boilerplate IntersectionObserver code

**Caching**
* Resolved an issue where the plugin was requesting optimized versions of pages on a POST request, possibly storing submitted data to a form if the form utility injected submitted values into the form.
* Added a new feature (Dynamic URLs) where the user can specify if they want query string arguments to bypass the cached optimized page.  This may be required for those installations that use a plugin which passes query string arguments in order to display a dynamic page.  With this new feature, you can also specify arguments to ignore, such as those that are associated with pay-per-click or tracking systems, that do not require a dynamically generated page.

= 2.2.11 =
**Installation Routine**
* New Step #1 which explains what the installation wizard does, and allows the user to choose their interface color
* Existing "Light" interface syle is updated with cloud background
* New "Plain" interface is more netutral than the "Light" interface.
* Updated final step of Installation Wizard simplifies how you choose which pages/posts to optimize, with a new "Advanced" option where the user can choose individual posts, pages, or custom post types
* If a user does not have enough credits to complete the basic installation, it will warn them once the installation wizard has been submitted.

**Installation Bug Fixes**
* Resolved an issue when the dashboard is being referenced via HTTPS but the website is set as HTTP in the WordPress general settings page.  This was causing installations to hang at 93% as the optimized page was not being saved due to an inconsistent "resource id".
* Resolved an issue where a server side firewall existed, that was preventing the submission of large post data (the optimization request) by implementing a notify-then-fetch routine.

**Builder Compatibility**
* Added compatibility for the new Divi builder by excluding URLS from being optimized that contained the et_fb=1 or et_bfb=1 arguments
* Added compatibility for the Cornerstone (X) builder by excluding any URLS from being optimized that contained /x/, or were referred by /x/

= 2.2.10 =
**Cache Management**
* Added support for Pagely server side (Varnish based) caching system 
* Detect and alert user if "mod_pagespeed" installed on the server (as mod_pagespeed will conflict with Pegasaas functionality)
* Resolved isssue where incorrect subfolder paths were created in the wp-content/ folder, when optimized pages were returned from the API
* Added new feature where the user can choose to enable "auto clear" of page cache on a weekly or monthly basis

**Memory Management**
* Migrated the use of native page setting and page cache state variables stored in WordPress wp_options table to a pegasaas_page_config and pegasaas_page_cache tables, to avoid bloat to the wp_options table that can occur with very large installations

**Usability**
* Added a "Welcome Tour" video to the interface upon first installationAdded a check for a valid email on Guest API key request
* Added a new tab labelled "Tools" which now has options to "Enable All" or "Disable All" acceleration for each of the page, post, or custom post types
* Added post level configurations for all custom post types -- previously, this was available only for pages and posts
* Added a new tab labelled "Changelog" which includes a list of recent updates
* Added a check for plugin being installed to a installation on "localhost" -- system cannot be installed on "localhost" as there is no way to scan "localhost" or return optimizations or scans to "localhost" from the API
* Added ability for Developer Edition to 'optimize on the fly' if dynamically generated page (query string, logged in user, ecommerce) is detected


= 2.2.9 =
* Added server speed check warning into interface
* Added notification alert when tracking/analytic scripts possibly slowing site down into interface
* Added "upgrade" link into the plugin listing in plugins.php panel
* Added detection and handling for .htpasswd (Basic HTTP Authentication) protected /wp-admin/ 
* Added detection and handling for Wordfence block of API requests
* Added link to contact support on the installer
* Changed the API communication request timeout from 1 second to 5 seconds
* Added a "pickup optimizations" service for Siteground/Kinsta/slowest response rate installations
* Changed the "autoload" setting for any Pegasaas data stored via  WP native "update_option" to "no"
* Changed the "manage" link in the plugin panel, for white label subscriptions, to the pa-web-perf alias
* Improved handling for write-protected .htaccess file

= 2.2.8 =
* Removed debug output when unoptimized page is requested.

= 2.2.7 =
* Added support for non-latin characters used in page slugs (permalink)
* Resolved issue where permalinks that had not been recalculated since a site had been switched from http to https causing a redirect issue preventing the scanning of pages
* Added feature to inform the individual running the initialization sequence that they can navigate away and the installation routine will continue in the background.
* Added feature that notifies the website operator via email once the initialization sequence has gathered the initial optimizations and scans
* Resolved issue where PageSpeed scans were being requested on pages with an un-optimized cache
* Added system check to verify that a permalink structure is enabled
* Adjusted interface to show when pages that are cached are un-optimized are not pending optimization if no optimization credits are remaining

= 2.2.6 =
* Update to PegasaasUtils::has_acceleration_enabled to resolve issue of home page and categories not being identified as having acceleration enabled

= 2.2.5 =
* Added compatibility for DreamPress by DreamHost cloud hosting
* Added auto detection of Varnish server side caching
* Added a number of third-party caching and optimization plugins as non-comaptible with a prompt to disable incompatible plugins 
* Resolved dashboard display prompts
* Improvment to temporary cache handling when an optimization is completed
* Addition of an "Optimize Now" option for the "Bulk" options in the Pages and Posts panel
* Resolved JavaScript error on edit page/post page.
* Resolved issue with superfluous PageSpeed scans being requested
* Added caching of ttf/woff/woff2/eot/svg files to the pegasaas-cache folder 
* Removed DB optimization calls as they can slow down the response time of the application and are no longer required
* Added "X-Powered-By" header to the request headers

= 2.2.4 =
* Update to PegasaasAPI::post to resolve API timeout issue

= 2.2.3 =
* Updates to Interface and Addition of Temporary Cache

= 2.2.1 =
* Patch problem with initialization sequence.

= 2.2.0 =
* Initial WordPress Repository Version.

== Upgrade Notice ==
= 3.4.0 = 
This update adds a better API submission method.

= 3.3.12 =
This update resolves a bug with the auto-crawl mechanism that disabled acceleration on pages that did not use a trailing slash.

= 3.3.11 =
This update resolves a display issue in the Pegasaas Accelerator WP dashboard.

= 3.3.10 =
This update resolves a compatibility issue with Elementor.

= 3.3.9 =
This update adds compatibility improvements for WPX and jQuery Updater.

= 3.3.8 =
This update adds compatibility improvements for Elementor.

= 3.3.7 =
This update adds compatibility improvements.

= 3.3.6 =
This update adds compatibility improvements.

= 3.2.2 =
This update adds the ability to handle very large web pages.

= 3.2.1 =
This update fixes an issue where minification of .min.js assets was not being performed correctly for the Elementor page builder.

= 3.2.0 =
This update improves the reliability of data transfer from the API, which should improve the number of succesful installations.

= 3.1.14 =
This update adds compatibility for six new Third Party Vendor Scripts

= 3.1.13 =
This update adds compatibility for six new Third Party Vendor Scripts

= 3.1.12 =
This update adds compatibility for six new Third Party Vendor Scripts

= 3.1.11 =
This update confirms compatibility with WP 5.4 and adds "Third Party Vendor Scripts" compatibility for Driftt &  TrustArc

= 3.1.10 =
This patch addresses an issue where sites that did not have a trailing slash in their permalink could not auto-accelerate pages.

= 3.1.9 =
This patch addresses compatibility issues with Google Site Kit, Elementor, and Theme Architect, as well as adds handling for sites that do not use a trailing slash on their permalinks.

= 3.1.8 =
This patch addresses compatibility issues with sites built with Elementor.

= 3.1.7 =
This patch addresses compatibility issues with Thrive Architect, Google web fonts, and cloud database providers.

= 3.1.6 =
This patch addresses compatibility issues with WordPress.com hosting, as well as special non-english characters in images.

= 3.1.5 =
This patch addresses compatibility issues in two plugins as well as some features available with sites hosted WordPress.com

= 3.1.4 =
This patch includes general bug fixes.

= 3.1.3 =
This patch further upgraded the dynamically generated pages deferral of javascript.

= 3.1.2 =
This patch added compatibility with "Rate My Post" and improved the dynamically generated pages deferral of javascript.

= 3.1.1 =
This patch addresses server performance related issues introduced with the auto-rescan mechanism introduced in v3.1.0.

= 3.1.0 =
This minor update includes a new ompatibility check, automatic web performance rescans, and log file rotation.

= 3.0.5 =
This patch addresses a bug that was introduced in 3.0.0 to handle the cache clearing in websites running Divi.

= 3.0.4 =
This patch addresses the situation where there inline script blocks preceeding a script tag with src attribute were not executing.

= 3.0.3 =
This update patches a few error messages that were being displayed in the dashboard and in the on-the-fly optimizations.

= 3.0.2
This update patches some bugs introduced in 3.0.0 related to caching and the new foundational optimzations.

= 3.0.1
This update patches some bugs introduced in 3.0.0 related to caching and the new foundational optimzations.

= 3.0.0 =
Version 3.0 has been four months in the making, and as such the list of changes and improvements are vast.  

= 2.8.19 =
This update adds compatibility for the YITH Wishlist plugin, plus some minor CSS fixes for the dashboard

= 2.8.18 =
This update patches a bug introduced in 2.8.16.

= 2.8.17 =
This update patches a bug introduced in 2.8.16.

= 2.8.16 =
This update patches quirky functionality in the Exclude URLS feature (introduced in v2.8.15) and Siteground HTTP->HTTPS auto redirection 

= 2.8.15 =
This update improves the Excluded URLs functionality and adds compatibility for the AliDropship plugin interface

= 2.8.14 =
This update adds improved Cache Clearing for Cloudflare and improvements to the JavaScript Lazy Loading settings interface. 

= 2.8.13 =
This update adds improved Cache Clearing

= 2.8.12 =
This update adds improved Cache Clearing

= 2.8.11 =
This update adds improved Cache Clearing

= 2.8.10 =
This update adds improved Cache Clearing and Data Handling

= 2.8.9 =
This update adds improved Wordfence compatibility and dashboard tweaks

= 2.8.8 =
This update adds and fixes e-commerce compatiblity.

= 2.8.7 =
This update patches an issue POST submissions.

= 2.8.6 =
This update patches an issue with on-the-fly optimizations for sites hosted with WPMU DEV.

= 2.8.5 =
This update patches an issue with on-the-fly optimizations for sites in subfolders.

= 2.8.4 =
The update patches an issue with trailing whitespace in the list of Pegasaas API IP addresses.

= 2.8.3 =
The plugin is now set up to install on WordPress.com hosted sites (requires the WordPress.com "Business" plan or higher to install plugins).

= 2.8.2 =
This update resolved a problem with the 404 handler which was not serving dynamic assets or 404 pages correctly.

= 2.8.1 =
This issue resolved a problem of the admin bar being cached.

= 2.8.0 =
This update hads support for the WordPress Multisite Network (when using subdirectories), where an administator can manage multiple sites through a master installation of WordPress.

= 2.7.4 =
This update supresses a PHP Notice.

= 2.7.3 =
This update resolves permalink mapping on Microsoft IIS as well as supresses a PHP Notice.

= 2.7.2 =
This update resolves path mapping for installations that reside within a subfolder.

= 2.7.1 =
This update resolves path mapping for installations that reside within a subfolder.

= 2.7.0 =
This upgrade adds support for the WordPress Multisite Network feature (when using subdomains), where an administator can manage multiple sites through a master installation of WordPress.

= 2.6.0 =
This upgrade adds advanced performance metrics to the interface, and upgrades the Combine CSS feature, and adds a new feature called Combine JS.

= 2.5.0 =
This update upgrades the script lazy loading system, including new functionality for the Guest and Premium APIs.

= 2.4.2 =
This update resolves a bug that was introduced in 2.4.0 where the system did not detect an existing Benchmark PageSpeed scan.

= 2.4.1 =
This update adds the functionality of custom lazy loaded script definition for Premium API key users

= 2.4.0 =
This update included bug fixes and an overhaul of the data management system that resulted in dramatically improved the load time of the Pegasaas Accelerator dashboard

= 2.3.9 =
This update includes compatiblity for wp-json and wc-auth API extensions for WooCommerce. 

= 2.3.8 =
This update includes a few small improvements to the cache handling system related to mapping of URLs to the appropriate cache resource.

= 2.3.7 =
This update adds error handling for the Apache .htaccess file, used for fast caching.

= 2.3.6 =
This update resolves caching related issues.

= 2.3.5 =
This update adds a fix for the caching system which was not fetching optimized content in a small percentage of sites.

= 2.3.4 =
This update adds support for when a port # is specified in the URL used to access the dashboard

= 2.3.3 =
This upgrade is comprised primarily with compatibility resolutions for the WP Engine hosting platform.

= 2.3.2 =
This upgrade is comprised primarily with compatibility resolutions for the Avada theme, WooCommerce plugin, Flywheel hosting platform, and Cloudflare CDN.

= 2.3.1 =
This update included simplified operation of some deferral mechanisms.

= 2.3.0 =
This update included support for a new Development mode available with the premium edition, contextual help on the pages/posts page, as well as a number of compatibility improvements. 

= 2.2.19 =
This update included performance improvements of the dashboard, most noticably for installations with > 1000 pages. 

= 2.2.18 =
This update included improvements to compatibility for WP-CLI.

= 2.2.17 =
This update included improvements to the installation wizard, adding a new "web performance experience level" step, as well as new handling for managing static resource cache, and some minor compatibility improvements.

= 2.2.16 =
This update included a fix which caused the "custom" installation method to not operate as expected.

= 2.2.15 =
This update included a fix which was causing a compatibility issue with the "Head, Footer, and Post Injections" plugin by Stefano Lissa.

= 2.2.14 =
This update includes a number of compatibility updates, including a better support for websites using Cloudflare.

= 2.2.13 =
This update added handling for a couple of non-typical enironmental conflicts, as well as the addition of an extension of the Lazy Loading Images feature, where you can now exclude specified images from being lazy loaded.

= 2.2.12 =
This update focused on further improving the installation experience, as well as the addition of a new feature called Dynamic URLs which lets the adminstrator enable the bypass of cache when query string variables are present in the web page request.

= 2.2.11 =
This update focused on improving the installation experience.

= 2.2.10 =
This update focused on improvements to the cache management and data handling systems, as well as a number of usability updates including a new feature where you can schedule automatic clearing of your cached optimized pages on a monthly or weekly basis. 

= 2.2.9 =
This is a fairly substantial patch which addresses installation issues that resulted from timeouts and possible interruption in service from security plugins.  We've also added a couple of warning notifications to the interface for situations which could impact the performance of the plugin.

= 2.2.8 =
This patch disables the debug output was visible when an a page optimization was requested.

= 2.2.7 =
Resolved a few issues revolving around the requesting of PageSpeed scans on unoptimized pages, as well as added a feature to inform the person installing that they navigate away from the installation process.

= 2.2.6 =
A bug was introduced in 2.2.5 where which caused "installations" to appear hung.  This patch allows the home page to be auto-optimized upon initialization, allowing the initialization routine to complete.

= 2.2.5 =
A number of bug fixes and small improvements to the interface and caching system.

= 2.2.4 =
The API was returning a timeout error, which was preventing the plugin from saving records to the database, which in turn cause the installation routine to not complete.

= 2.2.3 =
The interface now displays a more meaningful icon when a page optimization is queued.  There is also now a temporary cache that is created to reduce the server response time, while the full optimization is performed by the API.

= 2.2.1 =
The database schema was incomplete, which caused a hung installation.  This version patches incomplete table structure for those existing installations, as well as resolves the original SQL that was incomplete.

= 2.2.0 =
* Initial WordPress Repository Version.
