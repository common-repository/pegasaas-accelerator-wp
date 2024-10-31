<?php  		

if (PegasaasAccelerator::$settings['limits']['monthly_optimizations'] == 0  ) {
	$deferred_js 							= get_option('pegasaas_deferred_js', array());
	$post_type_critical_css					= PegasaasDeferment::get_critical_css_records("post_type");
	//$page_critical_css						= PegasaasDeferment::get_critical_css_records("custom");
	$post_type_image_data					= PegasaasAccelerator::get_image_data_records("post_type");
	$page_image_data						= PegasaasAccelerator::get_image_data_records("custom");
} else {
		$deferred_js 							= array();
	$post_type_critical_css					= array();
	//$page_critical_css							= array();

	$post_type_image_data = array();
	$page_image_data = array();
}

	$page_critical_css = $pegasaas->cache->get_list_of_all_local_files("critical.css");	
	


$queued_requests 						= get_option('pegasaas_pending_critical_css_request', array());
$queued_optimization_requests			= $pegasaas->db->get_results("pegasaas_api_request", array("request_type" => "optimization-request"));
$queued_image_data_requests 			= get_option('pegasaas_pending_image_data_request', array());
$pagespeed_pending_requests = $pegasaas->db->get_results("pegasaas_api_request", array("request_type" => "pagespeed"));

$pagespeed_pending_benchmark_requests = $pegasaas->db->get_results("pegasaas_api_request", array("request_type" => "pagespeed-benchmark"));

$pagespeed_semaphore_locks 				= $pegasaas->utils->get_all_semaphore_locks();
$cache_map 								= PegasaasAccelerator::$cache_map;


$memory_limit = ini_get('memory_limit');
$current_usage = (memory_get_peak_usage() / 1024 / 1024);
$current_use_percentage = $pegasaas->utils->get_memory_use_percentage(); 

?>
<!-- WARNING BAR -->
<div class='pegasaas-warning'>
	<i class='<?php echo PEGASAAS_MEMORY_WARNING_ICON_CLASS; ?>'></i>
	It is not recommended that you clear any of the data on this page unless instructed by a Pegasaas technician as doing so may result in a slower website while critical aspects are re-built.
</div>

<!-- MEMORY USE -->
<div class='well'>
	<h3>Memory Use</h3>
	<p>Pegasaas Accelerator will warn you when your PHP memory usage reaches 90%. Current maxium PHP memory allocated: <?php print $memory_limit; ?></p>
	<div class="progress" style='max-width: 500px;'>
		<div style="min-width: 3.5em;" class="progress-bar" role="progressbar" aria-valuenow="<?php print number_format($current_use_percentage, 0, '.', ''); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php print number_format($current_use_percentage, 0, '.', ''); ?>%;">
		<?php print number_format($current_use_percentage, 1, '.', ''); ?>% /<?php print number_format($current_usage, 1, '.', ''); ?>MB
		</div>
	</div>	
</div>


<!-- CACHE -->
<div class='well'>
<h3>Cache</h3>
<p>Clearing cache can help to recover space, however it may negatively impact the load time of your site until new resources are loaded.  When clearing "Critical CSS Cache", it will take some time to re-calculate
the critical-above-the-fold CSS for each page/post in your site -- only clear this if there is an obvious global issue that clearing all CSS cache will resolve.</p>
<?php if (PegasaasAccelerator::$settings['limits']['monthly_optimizations'] == 0) { ?>
<form class='advanced-button-form' method="post" action="admin.php?page=pegasaas-accelerator" >
	<input type='hidden' name='c' value='clear-post-type-critical-css' />
    <button type='submit' class='btn btn-large btn-square-large'>
        <span class='badge label-info'><?php echo count($post_type_critical_css); ?></span>
<div class='fa-wrapper'>
    <i class='<?php echo PEGASAAS_CSS_ICON_CLASS; ?>'></i>
		</div>
   Clear Post Type Critical CSS Cache</button>
    
 <?php
if (! isset($pegasaas->view_diagnostics) ) {
	 $pegasaas->view_diagnostics=false;
}
if ($pegasaas->view_diagnostics) { ?>

	<div class='diagnostic-container'>
	<ul>
      <li>Resource ID / Built</li>
	<?php 
	foreach ($post_type_critical_css as $id => $data) {
		$pt_critical_css = PegasaasUtils::get_object_meta($id, "critical_css");
		?><li><?php echo $id; ?> / <?php echo date("Y-m-d H:i:s", $pt_critical_css['built']); ?></li>
	<?php }	?>
	</ul>
	</div>

<?php } ?>
  
   
</form>


<form class='advanced-button-form' method="post" action="admin.php?page=pegasaas-accelerator" >
	<input type='hidden' name='c' value='clear-critical-css' />
    <button type='submit' class='btn btn-large btn-square-large'>
        <span class='badge label-info'><?php echo count($page_critical_css); ?></span>
<div class='fa-wrapper'>
    <i class='<?php echo PEGASAAS_CSS_ICON_CLASS; ?>'></i>
		</div>
   Clear Page Level Critical CSS Cache</button>
    
 <?php
if ($pegasaas->view_diagnostics) { ?>

	<div class='diagnostic-container'>
	<ul>
      <li>Resource ID / Built</li>
	<?php 
	foreach ($page_critical_css as $id => $data) {
		$critical_css = PegasaasUtils::get_object_meta($id, "critical_css");
		?><li><?php echo $id; ?> / <?php echo date("Y-m-d H:i:s", $critical_css['built']); ?></li>
	<?php }	?>
	</ul>
	</div>

<?php } ?>
  
   
</form>


<form style='display:none' class='advanced-button-form' method="post" action="admin.php?page=pegasaas-accelerator" >
	<input type='hidden' name='c' value='clear-post-type-image-data' />
    <button type='submit' class='btn btn-large btn-square-large'>
        <span class='badge label-info'><?php echo count($post_type_image_data); ?></span>
<div class='fa-wrapper'>
    <i class='<?php echo PEGASAAS_IMAGE_DATA_ICON_CLASS; ?>'></i>
		</div>
   Clear Post Type Image Data Cache</button>
    
 <?php
if (! isset($pegasaas->view_diagnostics) ) {
	 $pegasaas->view_diagnostics=false;
}
if ($pegasaas->view_diagnostics) { ?>

	<div class='diagnostic-container'>
	<ul>
      <li>Resource ID / Built</li>
	<?php 
	foreach ($post_type_image_data as $id => $data) {
		$pt_image_data = PegasaasUtils::get_object_meta($id, "image_data");
		?><li><?php echo $id; ?> / <?php echo date("Y-m-d H:i:s", $pt_image_data['built']); ?></li>
	<?php }	?>
	</ul>
	</div>

<?php } ?>
  
   
</form>


<form  style='display:none' class='advanced-button-form' method="post" action="admin.php?page=pegasaas-accelerator" >
	<input type='hidden' name='c' value='clear-image-data' />
    <button type='submit' class='btn btn-large btn-square-large'>
        <span class='badge label-info'><?php echo count($page_image_data); ?></span>
<div class='fa-wrapper'>
    <i class='<?php echo PEGASAAS_IMAGE_DATA_ICON_CLASS; ?>'></i>
		</div>
   Clear Page Level Image Data Cache</button>
    
 <?php
if ($pegasaas->view_diagnostics) { ?>

	<div class='diagnostic-container'>
	<ul>
      <li>Resource ID / Built</li>
	<?php 
	foreach ($page_image_data as $id => $data) {
		$image_data = PegasaasUtils::get_object_meta($id, "image_data");
		?><li><?php echo $id; ?> / <?php echo date("Y-m-d H:i:s", $image_data['built']); ?></li>
	<?php }	?>
	</ul>
	</div>

<?php } ?>
  
   
</form>



<?php } ?>
  
	<form class='advanced-button-form' method="post" action="admin.php?page=pegasaas-accelerator" >
		<input type='hidden' name='c' value='clear-cache' />
		<button type='submit' class='btn btn-large btn-square-large'>
		<span class='badge label-info'><?php echo count($cache_map); ?></span>
		<div class='fa-wrapper'>
		<i class='<?php echo PEGASAAS_CODE_ICON_CLASS; ?>'></i>
			</div>
	   Clear HTML Page Cache</button>

	<?php if ($pegasaas->view_diagnostics) { ?>
		<div class='diagnostic-container diagnostic-container-wide'>
		<table class='table table-striped table-bordered'>
		  <tr>
			<td>Post ID</td>
			<td>Location</td>
			<td>Built</td>
		  </tr>
		<?php
		foreach ($cache_map as $id => $data) {
			?><tr>
			<td><?php echo $data['id']; ?></td>
			<td><a target="_blank" href='<?php echo $id;?>'><?php echo $id; ?></a></td>
			<td><?php  
					echo get_date_from_gmt(date('Y-m-d H:i:s', $data['built']),get_option( 'date_format' ) ).
						" (".get_date_from_gmt( date('Y-m-d H:i:s', $data['built']),get_option( 'time_format' )).")"; 

			//echo date("Y-m-d H:i:s", $data['built']); ?></td>
			</tr> 
		<?php }	?>
		</table>
		</div>
	<?php } ?>	
	</form>
 
	<form class='advanced-button-form' method="post" action="admin.php?page=pegasaas-accelerator" >
		<input type='hidden' name='c' value='clear-remote-cache' />
		<button type='submit' class='btn btn-large btn-square-large'>
		<div class='fa-wrapper'><i class='<?php echo PEGASAAS_RESOURCE_CACHE_ICON_CLASS; ?>'></i></div>
		Clear CDN Resource Cache (Image/CSS/JS)</button>
	</form>
  </div>


<div class='well'>
<h3>Scan</h3>
<p>You may clear your local PageSpeed scan history if you wish -- historical scans will still be available from your pegasaas.com account interface.</p>
<form class='advanced-button-form' method="post" action="admin.php?page=pegasaas-accelerator" ><input type='hidden' name='c' value='clear-pagespeed-scores' />
  <button type='submit' class='btn btn-large btn-square-large'>
     <span class='badge label-info'><?php echo $pegasaas->scanner->get_pagespeed_scores_count(); ?></span>
 <div class='fa-wrapper'>
  <i class='<?php echo PEGASAAS_CHART_LINE_ICON_CLASS; ?>'></i>
	  </div>
  Clear PageSpeed Scores</button>
	
<?php
if ($pegasaas->view_diagnostics) {
$pages_with_scores = get_option("pegasaas_accelerator_pagespeed_scores", array());
	 ?>
	<div class='diagnostic-container'>
	<ul>
      <li>Resource ID / Mobile Score / Desktop Score</li>
	<?php 
	foreach ($pages_with_scores as $id => $scores) {
	
		?><li><?php echo $id; ?> / <?php echo $scores['mobile']; ?> / <?php echo $scores['desktop']; ?></li>
	<?php }	?>
	</ul>
	</div>

<?php } ?>	
</form>


<form class='advanced-button-form' method="post" action="admin.php?page=pegasaas-accelerator" ><input type='hidden' name='c' value='clear-pagespeed-benchmark-scores' />
  <button type='submit' class='btn btn-large btn-square-large'>
     <span class='badge label-info'><?php echo $pegasaas->scanner->get_pagespeed_benchmark_scores_count(); ?></span>
 <div class='fa-wrapper'>
  <i class='<?php echo PEGASAAS_CHART_AREA_ICON_CLASS; ?>'></i>
	  </div>
  Clear PageSpeed Benchmark Scores</button>
	
<?php
if ($pegasaas->view_diagnostics) {
$pages_with_scores = get_option("pegasaas_accelerator_pagespeed_benchmark_scores", array());
	//var_dump($pages_with_scores);
	 ?>
	<div class='diagnostic-container'>
	<ul>
      <li>Resource ID / Built</li>
	<?php 
	foreach ($pages_with_scores as $id => $score) {
		
		?><li><?php echo $id; ?> / <?php echo $score; ?></li>
	<?php }	?>
	</ul>
	</div>

<?php } ?>		
	
</form>
	</div>
	
	<div class='well'>
<h3>Pending Requests</h3>
<p>If there are stale requests that did not get processed, you may clear those requests here.</p>
<?php if ($pegasaas->is_standard()) { ?>
<form class='advanced-button-form' method="post" action="admin.php?page=pegasaas-accelerator" <?php if ($pegasaas->view_diagnostics) {?>class='diagnostic-mode'<?php } ?>>
    <input type='hidden' name='c' value='clear-queued-optimization-requests' />
    <button type='submit' class='btn btn-large btn-square-large'>
        <span class='badge label-info'><?php echo count($queued_optimization_requests); ?></span>
<div class='fa-wrapper'>
    <i class='<?php echo PEGASAAS_PENDING_REQUEST_ICON_CLASS; ?>'></i>
</div>
    Clear Optimization Requests</button>

<?php
if ($pegasaas->view_diagnostics) { ?>

	<div class='diagnostic-container'>
	<ul>
    <li>Resource ID / Requested</li>
	<?php 
		foreach ($queued_optimization_requests as  $datax) {
		$data['requested'] = $datax->time;
		$id = $datax->resource_id;
		?><li><?php echo $id; ?>--<?php echo date("Y-m-d H:i:s", strtotime($data['requested'])); ?></li>
	<?php }	?>
	</ul>
	</div>

<?php } ?>	
</form>
<?php } ?>

<form class='advanced-button-form' method="post" action="admin.php?page=pegasaas-accelerator" <?php if ($pegasaas->view_diagnostics) {?>class='diagnostic-mode'<?php } ?>>
    <input type='hidden' name='c' value='clear-queued-requests' />
    <button type='submit' class='btn btn-large btn-square-large'>
        <span class='badge label-info'><?php echo count($queued_requests); ?></span>
<div class='fa-wrapper'>
    <i class='<?php echo PEGASAAS_PENDING_REQUEST_ICON_CLASS; ?>'></i>
</div>
    Clear Critical CSS Requests</button>

<?php
if ($pegasaas->view_diagnostics) { ?>

	<div class='diagnostic-container'>
	<ul>
    <li>Resource ID / Requested</li>
	<?php 
	foreach ($queued_requests as $id => $data) {
		?><li><?php echo $id; ?>--<?php echo date("Y-m-d H:i:s", $data['requested']); ?></li>
	<?php }	?>
	</ul>
	</div>

<?php } ?>	
</form>



<form class='advanced-button-form' method="post" action="admin.php?page=pegasaas-accelerator" <?php if ($pegasaas->view_diagnostics) {?>class='diagnostic-mode'<?php } ?>>
    <input type='hidden' name='c' value='clear-queued-image-data-requests' />
    <button type='submit' class='btn btn-large btn-square-large'>
        <span class='badge label-info'><?php echo count($queued_image_data_requests); ?></span>
<div class='fa-wrapper'>
    <i class='<?php echo PEGASAAS_PENDING_REQUEST_ICON_CLASS; ?>'></i>
</div>
    Clear Image Data Requests</button>

<?php
if ($pegasaas->view_diagnostics) { ?>

	<div class='diagnostic-container'>
	<ul>
    <li>Resource ID / Requested</li>
	<?php 
	foreach ($queued_requests as $id => $data) {
		?><li><?php echo $id; ?>--<?php echo date("Y-m-d H:i:s", $data['requested']); ?></li>
	<?php }	?>
	</ul>
	</div>

<?php } ?>	
</form>

<?php
//var_dump($pagespeed_pending_requests);
?>
<form class='advanced-button-form' method="post" action="admin.php?page=pegasaas-accelerator" <?php if ($pegasaas->view_diagnostics) {?>class='diagnostic-mode'<?php } ?>>
	<input type='hidden' name='c' value='clear-pagespeed-requests' />
    <button type='submit' class='btn btn-large btn-square-large'>
    <span class='badge label-info'><?php echo count($pagespeed_pending_requests); ?></span>
		<div class='fa-wrapper'><i class='<?php echo PEGASAAS_PENDING_REQUEST_ICON_CLASS; ?>'></i></div>Clear PageSpeed Requests</button>
<?php 
if ($pegasaas->view_diagnostics) { ?>
	<div class='diagnostic-container'>
		<table>
		  <tr>
	        <td>Resource ID</td>
			<td>Nonce</td>
			<td>Submitted</td>
			</tr>
	
	
	<?php 
	foreach ($pagespeed_pending_requests as $id => $individual_request) {
		
			
	
		?><tr>
			<td><?php echo $individual_request->resource_id; ?></td>
			<td><?php echo $individual_request->nonce;?></td>
			<td><?php echo date("Y-m-d H:i:s", $individual_request->time); ?></td>
		</tr> 
	<?php 
	}	?>
	</table>
	</div>
  <?php } ?>

</form>

<form class='advanced-button-form' method="post" action="admin.php?page=pegasaas-accelerator" <?php if ($pegasaas->view_diagnostics) {?>class='diagnostic-mode'<?php } ?> >
	<input type='hidden' name='c' value='clear-pagespeed-benchmark-requests' />
    <button type='submit' class='btn btn-large btn-square-large'>
    <span class='badge label-info'><?php echo count($pagespeed_pending_benchmark_requests); ?></span>
		<div class='fa-wrapper'><i class='<?php echo PEGASAAS_PENDING_REQUEST_ICON_CLASS; ?>'></i></div>
		Clear PageSpeed Benchmark Requests</button>
<?php 
if ($pegasaas->view_diagnostics) { ?>
	<div class='diagnostic-container'>
	<ul>
    <li>Resource ID / Nonce</li>
	<?php 
	foreach ($pagespeed_pending_benchmark_requests as $id => $data) {
		foreach ($data as $nonce => $request_data) {
	//	var_dump($data);

		?><li><?php echo $id; ?> | <?php echo $nonce;?> | <?php echo date("Y-m-d H:i:s", $request_data['when_submitted']); ?></li> 
	<?php }	?>
		<?php }	?>
	</ul>
	</div>
  <?php } ?>

</form>
  
 <form class='advanced-button-form' method="post" action="admin.php?page=pegasaas-accelerator"  <?php if ($pegasaas->view_diagnostics) {?>class='diagnostic-mode'<?php } ?> >
	<input type='hidden' name='c' value='clear-all-semaphore-locks' />
    <button type='submit' class='btn btn-large btn-square-large'>
    <span class='badge label-info'><?php echo count($pagespeed_semaphore_locks); ?></span>
    <div class='fa-wrapper'><i class='<?php echo PEGASAAS_PENDING_REQUEST_ICON_CLASS; ?>'></i></div>Clear Variable Locks</button>
<?php 
if ($pegasaas->view_diagnostics) { ?>
	<div class='diagnostic-container'>
	<ul>
    <li>Resource ID / Nonce</li>
	<?php 
	foreach ($pagespeed_semaphore_locks as $semaphore_name) {
		?><li><?php echo $semaphore_name; ?></li> 
	<?php }	?>
	</ul>
	</div>
  <?php } ?>
</form>

<?php if ($pegasaas->utils->is_kinsta_server() || $pegasaas->utils->is_siteground_server() || PegasaasAccelerator::$settings['settings']['response_rate']['status'] == "slowest") { ?>
<form class='advanced-button-form' method="post" action="admin.php?page=pegasaas-accelerator" >
	<input type='hidden' name='c' value='pickup-pending-requests' />
    <button type='submit' class='btn btn-large btn-square-large'>
    <div class='fa-wrapper'><i class='<?php echo PEGASAAS_PICKUP_PENDING_REQUEST_ICON_CLASS; ?>'></i></div>Pickup Pending Requests</button>
</form>
<?php } ?>
  </div>

<div class='well'>
 <h3>All</h3>
<form class='advanced-button-form' method="post" action="admin.php?page=pegasaas-accelerator">
	<input type='hidden' name='c' value='clear-all-data' />
    <button type='submit' class='btn btn-large btn-square-large'>
    <span class='badge label-info'><?php 
		$count = 0;
		if (is_array($pagespeed_pending_requests)) {
			$count += count($pagespeed_pending_requests);
		}
		if (is_array($queued_requests)) {
			$count += count($queued_requests);
		}
		if (is_array($cache_map)) {
			$count += count($cache_map);
		}
		if (is_array($deferred_js)) {
			$count += count($deferred_js);
		}
		if (is_array($critical_css)) {
			$count += count($critical_css);
		}
		$count += $pegasaas->scanner->get_pagespeed_benchmark_scores_count() + $pegasaas->scanner->get_pagespeed_scores_count();
		
		echo $count;
		?></span>
    <div class='fa-wrapper'><i class='<?php echo PEGASAAS_PURGE_ALL_ICON_CLASS; ?>'></i></div>
    Purge All Data</button>
</form>

<form class='advanced-button-form' method="post" action="admin.php?page=pegasaas-accelerator">
	<input type='hidden' name='c' value='clear-cache' />
    <button type='submit' class='btn btn-large btn-square-large'>
    <div class='fa-wrapper'><i class='<?php echo PEGASAAS_PURGE_ALL_ICON_CLASS; ?>'></i></div>
    Purge CDN Cache</button>
</form>
    
<form class='advanced-button-form' method="post" action="admin.php?page=pegasaas-accelerator">
	<input type='hidden' name='c' value='re-init-accelerated-pages' />
	<input type='hidden' name='acceleration-type' value='manual' />
    <button type='submit' class='btn btn-large btn-square-large'>
<div class='fa-wrapper'>
    <i class='<?php echo PEGASAAS_AUTO_ACCELERATE_ICON_CLASS; ?>'></i></div>
    Re-Auto Accelerate Pages</button>    
</form> 

<form class='advanced-button-form' method="post" action="admin.php?page=pegasaas-accelerator">
	<input type='hidden' name='c' value='re-check-http-auth' />
    <button type='submit' class='btn btn-large btn-square-large'>
<div class='fa-wrapper'>
    <i class='<?php echo PEGASAAS_AUTO_ACCELERATE_ICON_CLASS; ?>'></i></div>
    Re-Check HTTP Auth Status</button>    
</form> 
	
	</div>
<div class='well'>
 <?php if ($pegasaas->view_diagnostics) { ?>
  <h3>Administrative Functions</h3>
  <form class='advanced-button-form' method="post" action="admin.php?page=pegasaas-accelerator">
	<input type='hidden' name='c' value='hide-diagnostics' />
    <button type='submit' class='btn btn-large btn-square-large'>
	<div class='fa-wrapper'>
    <i class='<?php echo PEGASAAS_HIDE_DIAGNOSTICS_ICON_CLASS; ?>'></i>
		</div>
		Hide Diagnostics</button>
</form>

<?php } else { ?>

  <h3>Administrative Functions</h3>
  <form class='advanced-button-form' method="post" action="admin.php?page=<?php echo $_GET['page']; ?>">
	<input type='hidden' name='c' value='view-diagnostics' />
    <button type='submit' class='btn btn-large btn-square-large'>
	<div class='fa-wrapper'>
    <i class='<?php echo PEGASAAS_SHOW_DIAGNOSTICS_ICON_CLASS; ?>'></i>
		</div>
		Show Diagnostics</button>
</form>

<?php } ?>
</div>

<div class='well'>
<h3>.htaccess</h3>
<p>Just the contents of the htaccess control file.  We may look at this to see if there is a conflict with any instructions used by other plugins.</p>
<p>If the .htaccess file does not contain the plugin instructions, you can FORCE an update to the .htacces, bypassing any checks for conflicts.  It is recommended that you
ensure you have SFTP access to your server in the event that this update breaks your website.</p>
<?php
$htaccess_contents = implode("", file($pegasaas->get_home_path().".htaccess"));
?>
<textarea disabled readonly class='htaccess'><?php echo $htaccess_contents; ?></textarea>

<form class='advanced-button-form' method="post"  onsubmit="return confirm('By proceeding, understand that this could break your website.  Be sure you have SFTP access to your webspace and a backup of your current .htaccess file before proceeding.');">
	<input type='hidden' name='c' value='force-htaccess-update' />
    <button type='submit' class='btn btn-large btn-square-large'>
<div class='fa-wrapper'>
    <i class='<?php echo PEGASAAS_AUTO_ACCELERATE_ICON_CLASS; ?>'></i></div>
    Force .htaccess Update</button>    
</form> 
	
</div>
 