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
						
						<button type='submit' class='btn btn-primary btn-xs btn-cpcss'>
							<span class='cpcss-icon'>{<i class='fa fa-bolt'></i>}</span>
						
						</button>
							<?php if ($page_level_cpcss_exists || $post_type_cpcss_exists)	{ ?>			
					  <button type="button" class="btn btn-primary btn-caret btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span class="caret"></span>
						<span class="sr-only">Toggle Dropdown</span>
					  </button>		
	<ul class="dropdown-menu">
		<?php if ($page_level_cpcss_exists) { ?>
										<li><a href="?page=pegasaas-accelerator&c=purge-cpcc&p=<?php echo $post['slug']; ?>"><i class='<?php echo PEGASAAS_REBUILD_CSS_ICON_CLASS; ?> fa-plcpcss'></i> Purge Page-Level Critical Path CSS</a></li>
										<li><a href="?page=pegasaas-accelerator&c=recalc-cpcc&p=<?php echo $post['slug']; ?>"><i class='<?php echo PEGASAAS_REBUILD_CSS_ICON_CLASS; ?> fa-plcpcss'></i> Re-Build Page-Level Critical Path CSS</a></li>
		<?php } else { ?>
										<li><a href="?page=pegasaas-accelerator&c=build-cpcc&p=<?php echo $post['slug']; ?>"><i class='<?php echo PEGASAAS_REBUILD_CSS_ICON_CLASS; ?> fa-plcpcss'></i> Build Page-Level Critical Path CSS</a></li>

		<?php } ?>
		<li><a href="?page=pegasaas-accelerator&c=recalc-cpcc&p=post_type__<?php echo ($post['post_type'] == "" ? "home_page" : $post['post_type']); ?>"><i class='<?php echo PEGASAAS_REBUILD_CSS_ICON_CLASS; ?> fa-ptcpcss'></i> Purge Post-Type Critical Path CSS</a></li>

	</ul>
						<?php } ?>
					</div>
					  
					  	
				  	
				  </form>					  
<?php } ?>