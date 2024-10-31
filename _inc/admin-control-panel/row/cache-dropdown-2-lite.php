<?php 
if (PegasaasAccelerator::$settings['status'] > 0) { 
	$cache_exists = PegasaasAccelerator::$cache_map["{$post['slug']}"] != "";
	$temp_cache_exists = PegasaasAccelerator::$cache_map["{$post['slug']}"]["is_temp"];
	
	$auto_crawl_enabled = PegasaasAccelerator::$settings['settings']['auto_crawl']['status'] == 1;
	$no_credits = PegasaasAccelerator::$settings['limits']['monthly_optimizations'] > 0  && PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining'] == 0;

?> 
<form class='page-cache-form <?php print ($cache_exists ? "cache-exists" : ""); ?>'>
	<input type='hidden' name='pid' value='<?php echo $post['pid']; ?>' />
	<input type='hidden' name='post_id' value='<?php echo $post['id']; ?>' />
	<input type='hidden' name='c' value='rescan-pagespeed' />
				  	
	<div class="btn-group<?php if (PegasaasAccelerator::$settings['limits']['monthly_optimizations'] > 0) { ?> cache-standard-edition<?php } ?>">						
		<button type='button' class='btn btn-primary btn-xs btn-cache dropdown-toggle <?php if (PegasaasAccelerator::$settings['settings']['auto_crawl']['status'] == 1 ) { ?>auto-crawl-available<?php } ?>' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		

			<!-- optimization performed by API -->
			<i class='material-icons material-check 
					  <?php if (!$cache_exists || $temp_cache_exists) { ?>hidden<?php } ?>'
			   rel='tooltip' 
			   title='Fully Optimized'
			   >assignment_turned_in</i> 
			
			
			<!-- optimization performed by plugin -->
			<?php if ($no_credits) { ?>
			<i class='material-icons material-local-cache-exists temp-cache-exists <?php if (!$temp_cache_exists) { 	?>hidden<?php } ?>' rel='tooltip' title='Un-optimized Page Cached.  Upgrade to a plan with more optimization credits to optimize this page.'>clear</i>	
			<?php } else { ?>
			<i class='material-icons material-local-optimization-complete <?php if (!$temp_cache_exists) { 	?>hidden<?php } ?>' rel='tooltip' title='Partially Optimized - Full Optimization via API in progress.'>done</i>	
			<?php } ?>
			
			<!-- optimization pending crawl -->
			<i class='material-icons material-check-pending  	
					  <?php if ($cache_exists || !$auto_crawl_enabled) { 	?>hidden<?php } ?>'
			   rel='tooltip' 
			   title='Pending Optimization - Will be optimized automatically by the auto crawl'>access_time</i>	
			
			<!-- optimization pending user visit -->
			<i class='material-icons material-optimized-on-next-visit				
					  <?php if ($cache_exists || $auto_crawl_enabled) { ?>hidden<?php } ?>' 
			   rel='tooltip' 
			   title='The page has not yet been optimized.  It will be optimized and cached the next time a visitor views the page.'>snooze</i>
			
			<!-- spinner -->
			<i class='svg-icons svg-tail-spin hidden' rel='tooltip' title='...'></i>
			
			<!-- problem requesting optimization -->
			<i class='material-icons material-problem hidden' rel='tooltip' title='There was a problem optimizing the page.  Please try again later.'>report_problem</i>
			
			<!-- prompt -->
			<span class="material-icons">more_vert</span>
		</button>
														
		<ul class="dropdown-menu">
			<li data-state='cache-exists'  <?php if (!$cache_exists || $temp_cache_exists) { ?>class='hidden'<?php } ?>><a class='purge-page-cache' data-slug='<?php echo $post['slug']; ?>' data-resource_id='<?php echo $post['pid']; ?>' href="?page=pegasaas-accelerator&c=rebuild-page-cache&p=<?php echo $post['slug']; ?>"><i class='fa fa-trash'></i> Purge Optimized Page Cache</a></li>
			<li data-state='temp-cache-exists'  <?php if (!$temp_cache_exists) { ?>class='hidden'<?php } ?>><a class='purge-page-cache' data-slug='<?php echo $post['slug']; ?>' data-resource_id='<?php echo $post['pid']; ?>' href="?page=pegasaas-accelerator&c=rebuild-page-cache&p=<?php echo $post['slug']; ?>"><i class='fa fa-trash'></i> Purge Temporary Page Cache</a></li>
			<li data-state='cache-missing' <?php if ($cache_exists) { ?>class='hidden'<?php } ?>><a class='build-page-cache' data-slug='<?php echo $post['slug']; ?>' data-resource_id='<?php echo $post['pid']; ?>' href="?page=pegasaas-accelerator&c=build-page-cache&p=<?php echo $post['slug']; ?>"><i class='fa fa-magic'></i> Optimize Page</a></li>
		</ul>
	</div>
</form>					  
<?php 
}
?>