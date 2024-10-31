<?php	$stats = $pegasaas->cache->get_basic_image_cache_stats();	
															
										?>
					<div class='image-optimization-stats'>
						<?php if (PegasaasAccelerator::$settings['settings']['basic_image_optimization']['images_per_month'] != 9999) { ?>
					<p class='text-center'>Image Optimizations This Month</p>
					<div style='width: calc(100% - 90px); display: inline-block;'>
					<div class="progress">
  					<div class="progress-bar <?php if ($stats['percentage_of_use'] > 85) { print "progress-bar-danger"; } else if ($stats['percentage_of_use'] > 75) { print "progress-bar-warning"; } ?>" role="progressbar" aria-valuenow="<?php echo $stats['percentage_of_use']; ?>" aria-valuemin="0" aria-valuemax="100" style="min-width: 5em; width: <?php echo $stats['percentage_of_use']; ?>%;">
    <?php echo $stats['optimizations_so_far_this_month'] ?>/<?php echo PegasaasAccelerator::$settings['settings']['basic_image_optimization']['images_per_month']; ?>
  </div>
</div>
						</div>
					<div style='margin-top: -1px;text-align: right; vertical-align: top; width: 80px; display: inline-block;'>
					<a target="_blank" class='btn btn-xs btn-success' href='https://pegasaas.com/upgrade/?utm_source=pegasaas-accelerator-lite-image-panel&upgrade_key=<?php echo PegasaasAccelerator::$settings['api_key']; ?>-<?php echo PegasaasAccelerator::$settings['installation_id']; ?>&site=<?php echo $pegasaas->utils->get_http_host(); ?>&current=<?php echo PegasaasAccelerator::$settings['subscription']; ?>'>Upgrade</a>
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
						<div class='image-stat-description'>Images Over Max Filesize (<?php echo (PegasaasAccelerator::$settings['settings']['basic_image_optimization']['max_image_size'] / 1024 / 1024); ?>MB)</div>
						<a target="_blank" class='btn btn-xs btn-success' href='https://pegasaas.com/upgrade/?utm_source=pegasaas-accelerator-lite-image-panel&upgrade_key=<?php echo PegasaasAccelerator::$settings['api_key']; ?>-<?php echo PegasaasAccelerator::$settings['installation_id']; ?>&site=<?php echo $pegasaas->utils->get_http_host(); ?>&current=<?php echo PegasaasAccelerator::$settings['subscription']; ?>'>Upgrade</a>

						  </div>
					  </div>
					 
					  	
					</div>
					
				
					<?php } ?>
						</div>