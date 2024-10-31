<?php if (PegasaasAccelerator::$settings['status'] > 0) { 
	//var_dump(PegasaasAccelerator::$post_type_cpcss); 
	$page_level_cpcss_exists 	= isset(PegasaasAccelerator::$page_level_cpcss["{$post['slug']}"]) 					&& PegasaasAccelerator::$page_level_cpcss["{$post['slug']}"] != "";

	if ($post['post_type'] == "category") {
		$post_type_cpcss_exists 	= isset(PegasaasAccelerator::$post_type_cpcss["post_type__{$post['category_post_type']}"]) 	&& PegasaasAccelerator::$post_type_cpcss["post_type__{$post['category_post_type']}"] != "";
	} else {
		$post_type_cpcss_exists 	= isset(PegasaasAccelerator::$post_type_cpcss["post_type__{$post['post_type']}"]) 	&& PegasaasAccelerator::$post_type_cpcss["post_type__{$post['post_type']}"] != "";
	//	print $post['post_type'];
		if ($post['slug'] == "/") {
			$post_type_cpcss_exists = PegasaasAccelerator::$post_type_cpcss["post_type__home_page"] != "";
		}
	}

?> 

<form class='cpcss-form <?php if ($post_type_cpcss_exists) { print "post-type-cpcss-exists"; } ?> <?php if ($page_level_cpcss_exists) { print "page-level-cpcss-exists"; } ?>'>
	<input type='hidden' name='pid' value='<?php echo $post['pid']; ?>' />
	<input type='hidden' name='post_id' value='<?php echo $post['id']; ?>' />
	<input type='hidden' name='c' value='rescan-pagespeed' />
				  	
	<div class="btn-group">	
		<button type='button' class='btn btn-primary btn-xs btn-cpcss dropdown-toggle' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<i class='fa fa-fw fa-check <?php 			if (!$page_level_cpcss_exists && !$post_type_cpcss_exists) { ?>hidden<?php } ?>'></i> 
			<i class='fa fa-fw fa-hourglass-half <?php 	if ($page_level_cpcss_exists || $post_type_cpcss_exists) { 	?>hidden<?php } ?>' rel='tooltip' title='Pending - will be built automatically by the auto crawl'></i>	
			<i class='fa fa-fw fa-spinner fa-spin hidden' rel='tooltip' title='...'></i>
			<i class='fa fa-fw fa-exclamation-triangle hidden' rel='tooltip' title='There was a problem building the page cache.  Please try again later.'></i>
			
			<?php if ($page_level_cpcss_exists || $post_type_cpcss_exists)	{ ?>	
			<span class="fa fa-ellipsis-v"></span>
			<?php } ?>
		</button>
					
		<ul class="dropdown-menu">
			<li data-state='page-level-cpcss-exists'  <?php if (!$page_level_cpcss_exists) { ?>class='hidden'<?php } ?>><a class='purge-page-level-cpcss' 	data-slug='<?php echo $post['slug']; ?>' href="?page=pegasaas-accelerator&c=purge-cpcc&p=<?php echo $post['slug']; ?>"><i class='fa fa-trash'></i> Purge Page-Level CPCSS</a></li>
			<li data-state='page-level-cpcss-exists'  <?php if (!$page_level_cpcss_exists) { ?>class='hidden'<?php } ?>><a class='refresh-page-level-cpcss' 	data-slug='<?php echo $post['slug']; ?>' href="?page=pegasaas-accelerator&c=recalc-cpcc&p=<?php echo $post['slug']; ?>"><i class='fa fa-refresh'></i> Refresh Page-Level CPCSS</a></li>
			<li data-state='page-level-cpcss-missing' <?php if ($page_level_cpcss_exists) {  ?>class='hidden'<?php } ?>><a class='build-page-level-cpcss' 	data-slug='<?php echo $post['slug']; ?>' href="?page=pegasaas-accelerator&c=build-cpcc&p=<?php echo $post['slug']; ?>"><i class='fa fa-magic'></i> Build Page-Level CPCSS</a></li>
			<li data-state='always'><a class='refresh-post-type-cpcss' 	data-type='<?php echo ($post['post_type'] == "" ? "home_page" : $post['post_type']); ?>' href="?page=pegasaas-accelerator&c=recalc-cpcc&p=post_type__<?php echo ($post['post_type'] == "" ? "home_page" : $post['post_type']); ?>"><i class='fa fa-refresh'></i> Refresh Post-Type CPCSS</a></li>
		</ul>	
	</div>
</form>					  
<?php } ?>