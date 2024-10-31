
<div class='test-suite'>
	<h1>Test Suite</h1>
	<table width='100%'>
	  <tr>
		 <td width='50%' style='vertical-align: top'>
				  <h2>Semaphore System</h2>
			<ul>
				<li><a href='?page=pegasaas-accelerator&test=semaphore-lock'>Semaphore Lock</a></li>
				<li><a href='?page=pegasaas-accelerator&test=semaphore-quick-fail-pass'>Semaphore Quick-Fail (Pass)</a></li>
				<li><a href='?page=pegasaas-accelerator&test=semaphore-quick-fail-fail'>Semaphore Quick-Fail (Fail)</a>
				</li>
			</ul>

			 <h2>Submit Scans</h2>
			<ul>
				<li><a href='?page=pegasaas-accelerator&test=clear-stale-requests'>Clear Stale Requests</a></li>
				<li><a href='?page=pegasaas-accelerator&test=auto-accelerate-pages'>Auto Accelerate Pages</a></li>
				<li><a href='?page=pegasaas-accelerator&test=submit-scan-request'>Submit Scan Requests</a></li>
				<li><a href='?page=pegasaas-accelerator&test=clear-and-submit-pagespeed-requests'>Clear &amp; Submit Scan Requests</a></li>
				<li><a href='?page=pegasaas-accelerator&test=submit-benchmark-requests'>Submit Benchmark Requests</a></li>
			</ul>

			 <h2>Data Objects</h2>
			<ul>
			<li><a href='?page=pegasaas-accelerator&test=refresh-all-pages-and-posts'>Refresh All Pages and Posts Object</a></li>
			<li><a href='?page=pegasaas-accelerator&test=get-scanned-objects'>Get Scanned Objects</a></li>
				<li><a href='?page=pegasaas-accelerator&test=get-scanned-objects-on-first-init'>Get Scanned Objects (On First Init)</a></li>
				<li><a href='?page=pegasaas-accelerator&test=fetch-page-metrics'>Fetch Page Metrics</a></li>
				<li><a href='?page=pegasaas-accelerator&test=clear-pagespeed-scans'>Clear Pagespeed Scans</a></li>
				<li><a href='?page=pegasaas-accelerator&test=get-all-categories'>Get All Categories</a></li>
				<li><a href='?page=pegasaas-accelerator&test=get-site-performance-metrics'>Get Site Performance Metrics</a></li>
				<li><a href='?page=pegasaas-accelerator&test=get-site-performance-metrics&force_recalculation=1'>Get Site Performance Metrics (force recalc)</a></li>
				<li><a href='?page=pegasaas-accelerator&test=get-site-performance-metrics-footprint'>Get All Site Performance Metrics Footprint</a></li>
				<li><a href='?page=pegasaas-accelerator&test=fetch-page-fonts'>Fetch Page Fonts</a></li>
				
			</ul>


			<h2>File Handling</h2>
            <ul>
			<li><a href='?page=pegasaas-accelerator&test=get-list-of-all-files-in-folder'>Get List of All Files</a></li>
			<li><a href='?page=pegasaas-accelerator&test=get-list-of-all-files-in-folder-detailed'>Get List of All Files - DETAILED</a></li>
			<li><a href='?page=pegasaas-accelerator&test=get-local-cache-stats-all'>Get Local Cache Stats (all)</a></li>

			</ul>

			<h2>Communication</h2>
            <ul>
			<li><a href='?page=pegasaas-accelerator&test=api-accessible'>Test API Accessible</a></li>
			<li><a href='?page=pegasaas-accelerator&test=api-submit-optimization-request'>Test API Submit Test Optimization Request</a></li>
			</ul>

            <h2>Misc</h2>
            <ul>
			<li><a href='?page=pegasaas-accelerator&test=rotate-logs'>Rotate Logs</a></li>
			<li><a href='?page=pegasaas-accelerator&test=contact-form-7-compatibility'>Contact Form 7 Compatibility</a></li>
			<li><a href='?page=pegasaas-accelerator&test=settings-dump'>Settings Dump</a></li>
			<li><a href='?page=pegasaas-accelerator&test=auto-crawl'>Auto Crawl</a></li>
			<li><a href='?page=pegasaas-accelerator&test=test-wp-delete-post'>Test wp_delete_post</a></li>
			<li><a href='?page=pegasaas-accelerator&test=test-change-post-status'>Test change post status</a></li>
			</ul>



		  </td>

		 <td style='vertical-align: top'>
		   <?php
			 if ($_GET['test'] == 'semaphore-lock') {
				 ?>
				 <h4>Semaphore Lock</h4>
				 <p>This expects the result to PASS at the end, with a with a fast failure.   This test
					 should take aproximately 3 seconds to complete.</p>
			 <?php
			 	PegasaasTest::semaphore_lock();
			 } else if ($_GET['test'] == 'semaphore-quick-fail-pass') {
				 ?>
				 <h4>Semaphore Quick Fail</h4>
				 <p>This expects the result to PASS at the end, with a with a fast failure.  This is used
					 in functions (such as submit_benchmark_scans) where we don't want to hold the system up waitin for a previously
					 existing lock to complete.</p>
			 <?php
				 PegasaasTest::semaphore_quick_fail($pass = true);
			} else if ($_GET['test'] == 'auto-crawl') {
					?>
					<h4>Auto Crawl</h4>
					<p>Run the Auto Crawl mechanism</p>
				<?php
					PegasaasTest::auto_crawl();
				} else if ($_GET['test'] == 'refresh-all-pages-and-posts') {
					?>
					<h4>Refresh All Pages and Posts Object</h4>
					<p>This operation will bypass any cached all pages and posts object, and refresh it directly.</p>
				<?php
					PegasaasTest::refresh_all_pages_and_posts();


				} else if ($_GET['test'] == 'settings-dump') {
					?>
					<h4>Settings Dump</h4>
					<p>A raw view of the intsallation settings</p>
					<pre>
				<?php
					var_dump(PegasaasAccelerator::$settings['settings']);
					?>
					</pre>
					<?php				
			 } else if ($_GET['test'] == 'semaphore-quick-fail-fail') {
				?>
				 <h4>Semaphore Quick Fail</h4>
				 <p>This expects the result to FAIL at the end.  This purpose of this test
					 is to ensure that if bad data were passed to the Semaphore Quick-Fail (Pass) test, this should not
					 return a PASS.</p>
			 <?php

				 PegasaasTest::semaphore_quick_fail($pass = false);


				} else if ($_GET['test'] == 'api-accessible') {
					?>
					 <h4>API Accessible</h4>
					 <p>This test communicates (and dumps any response) with the API.</p>
				 <?php
	
					 PegasaasTest::api_accessibile();
					} else if ($_GET['test'] == 'api-submit-optimization-request') {
						?>
						 <h4>API Submit Test Optimization Request</h4>
						 <p>This test communicates (and dumps any response) with the API, while doing a test submission.</p>
					 <?php
		
						 PegasaasTest::api_submit_optimization_request();
			} else if ($_GET['test'] == 'get-list-of-all-files-in-folder') {
					?>
					 <h4>Get List of All Files In Folder</h4>
					 <p>Time how long it takes to iterate through entire cache folder searching for HTML files.</p>
				 <?php

					 PegasaasTest::get_list_of_all_files_in_folder();
			} else if ($_GET['test'] == 'get-list-of-all-files-in-folder-detailed') {
					?>
					 <h4>Get List of All Files In Folder (DETAILED)</h4>
					 <p>Time how long it takes to iterate through entire cache folder searching for HTML files.</p>
				 <?php

					 PegasaasTest::get_list_of_all_files_in_folder_detailed();
			} else if ($_GET['test'] == 'get-local-cache-stats-all') {
						?>
						 <h4>Get Local Cache Stats (ALL)</h4>
						 <p>Time how long it takes to iterate through entire cache folder searching for HTML, combined.css, deferred.js files.</p>
					 <?php

						 PegasaasTest::get_local_cache_stats_all();
			 } else if ($_GET['test'] == 'clear-stale-requests') {
				?>
				 <h4>Clear Stale Requets</h4>
				 <p></p>
			 <?php

				 PegasaasTest::clear_stale_requests();


				} else if ($_GET['test'] == 'contact-form-7-compatibility') {
					?>
					 <h4>Contact Form 7 Compatibility</h4>
					 <p></p>
				 <?php

					 PegasaasTest::contact_form_7_compatibility();
			 } else if ($_GET['test'] == "auto-accelerate-pages") {
				 ?>
				 <h4>Auto Accelerate Pages</h4>
				 <p></p>
			 <?php

				 PegasaasTest::auto_accelerate_pages();
			 } else if ($_GET['test'] == "get-scanned-objects") {
				 ?>
				 <h4>Get Scanned Objects</h4>
				 <p></p>
			 <?php

				 PegasaasTest::get_scanned_objects();
			 } else if ($_GET['test'] == "get-scanned-objects-on-first-init") {
				 ?>
				 <h4>Get Scanned Objects (On First Init)</h4>
				 <p>This test simulates a site with no pages at first accelerated, and then accelerating all pages.</p>
			 <?php

				 PegasaasTest::get_scanned_objects_on_first_init();
			 } else if ($_GET['test'] == "fetch-page-metrics") {
				 ?>
				 <h4>Fetch Page Metrics</h4>
				 <p></p>
			 <?php

				 PegasaasTest::fetch_page_metrics();
			 } else if ($_GET['test'] == "submit-scan-request") {
				 ?>
				 <h4>Submit Scan Request</h4>
				 <p></p>
			 <?php

				 PegasaasTest::submit_scan_request();

				} else if ($_GET['test'] == "fetch-page-fonts") {
					?>
					<h4>Fetch Page Fonts</h4>
					<p></p>
				<?php
   
					PegasaasTest::fetch_page_fonts();

			 } else if ($_GET['test'] == "clear-pagespeed-scans") {
				 ?>
				 <h4>Clear Pagespeed Scans</h4>
				 <p></p>
			 <?php

				 PegasaasTest::clear_pagespeed_scans();
			 }  else if ($_GET['test'] == "clear-and-submit-pagespeed-requests") {
				 ?>
				 <h4>Clear Pagespeed Scans</h4>
				 <p></p>
			 <?php

				 PegasaasTest::clear_and_submit_pagespeed_scans();
			 } else if ($_GET['test'] == "test-wp-delete-post") {
				 PegasaasTest::test_wp_delete_post();
			} else if ($_GET['test'] == "test-change-post-status") {
				 PegasaasTest::test_change_post_status();
				 
			} else if ($_GET['test'] == "get-all-categories") {
					PegasaasTest::get_all_categories();				 
			 } else if ($_GET['test'] == "get-site-performance-metrics") {
				 ?>
			 				 <h4>Get Site Performance Metrics</h4>

			 <?php
				 PegasaasTest::get_site_performance_metrics();

				} else if ($_GET['test'] == "get-site-performance-metrics-footprint") { ?>
								 <h4>Get Site Performance Metrics Footprint</h4>
   
				<?php
					PegasaasTest::get_site_performance_metrics_footprint();
   
			 } else if ($_GET['test'] == "rotate-logs") {
				 PegasaasTest::rotate_logs();
			 }
			 ?>

		 </td>
	  </tr>
	</table>


</div>