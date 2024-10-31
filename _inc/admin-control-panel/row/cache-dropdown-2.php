<?php 
if (PegasaasAccelerator::$settings['status'] > 0) { 
	$cache_exists = PegasaasAccelerator::$cache_map["{$post['slug']}"] != "";
	$auto_crawl_enabled = PegasaasAccelerator::$settings['settings']['auto_crawl']['status'] == 1;
?> 
<form class='page-cache-form <?php print ($cache_exists ? "cache-exists" : ""); ?>'>
	<input type='hidden' name='pid' value='<?php echo $post['pid']; ?>' />
	<input type='hidden' name='post_id' value='<?php echo $post['id']; ?>' />
	<input type='hidden' name='c' value='rescan-pagespeed' />
				  	
	<div class="btn-group">						
		<button type='button'  class='btn btn-primary btn-xs btn-cache dropdown-toggle <?php if (PegasaasAccelerator::$settings['settings']['auto_crawl']['status'] == 1 ) { ?>auto-crawl-available<?php } ?>' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<i class='fa fa-fw fa-check <?php 			if (!$cache_exists) { 							?>hidden<?php } ?>'></i> 
			<i class='fa fa-fw fa-hourglass-half <?php 	if ($cache_exists || !$auto_crawl_enabled) { 	?>hidden<?php } ?>' rel='tooltip' title='Pending - will be built automatically by the auto crawl'></i>	
			<i class='fa fa-fw fa-info <?php 			if ($cache_exists || $auto_crawl_enabled) { 	?>hidden<?php } ?>' rel='tooltip' title='The page has not yet been cached, and will be cached the next time a guest views the page.'></i>
			<i class='fa fa-fw fa-spinner fa-spin hidden' rel='tooltip' title='...'></i>
			<i class='fa fa-fw fa-exclamation-triangle hidden' rel='tooltip' title='There was a problem building the page cache.  Please try again later.'></i>
			<span class="fa fa-ellipsis-v"></span>
		</button>
														
		<ul class="dropdown-menu">
			<li data-state='cache-exists'  <?php if (!$cache_exists) { ?>class='hidden'<?php } ?>><a class='purge-page-cache' data-slug='<?php echo $post['slug']; ?>' href="?page=pegasaas-accelerator&c=rebuild-page-cache&p=<?php echo $post['slug']; ?>"><i class='fa fa-trash'></i> Purge Page Cache</a></li>
			<li data-state='cache-missing' <?php if ($cache_exists) { ?>class='hidden'<?php } ?>><a class='build-page-cache' data-slug='<?php echo $post['slug']; ?>' href="?page=pegasaas-accelerator&c=build-page-cache&p=<?php echo $post['slug']; ?>"><i class='fa fa-magic'></i> Build Page Cache</a></li>
		</ul>
	</div>
</form>					  
<?php 
}
?>