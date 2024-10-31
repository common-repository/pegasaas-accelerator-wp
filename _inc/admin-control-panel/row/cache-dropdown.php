<?php 
if (PegasaasAccelerator::$settings['status'] > 0) { 
	$cache_exists = PegasaasAccelerator::$cache_map["{$post['slug']}"] != "";
?> 
				  <form class='page-cache-form <?php print ($cache_exists ? "cache-exists" : ""); ?>'>
				  	<input type='hidden' name='pid' value='<?php echo $post['pid']; ?>' />
				  	<input type='hidden' name='post_id' value='<?php echo $post['id']; ?>' />
				  	<input type='hidden' name='c' value='rescan-pagespeed' />
				  	
					<div class="btn-group">	
						
						<button type='submit' class='btn btn-primary btn-xs btn-cache'>
							<span class='page-cache-icon'>&lt;<i class='fa fa-shield'></i>&gt;</span>
						
						</button>
												
					  <button type="button" class="btn btn-primary btn-caret btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span class="caret"></span>
						<span class="sr-only">Toggle Dropdown</span>
					  </button>		
						<ul class="dropdown-menu">
							<li><a href="?page=pegasaas-accelerator&c=rebuild-page-cache&p=<?php echo $post['slug']; ?>"><i class='<?php echo PEGASAAS_CACHE_ICON_CLASS; ?>'></i> Purge Page Cache</a></li>
						</ul>
					</div>
					  
					  	
				  	
				  </form>					  
<?php 
}
?>