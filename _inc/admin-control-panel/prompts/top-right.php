<?php 

$show_monthly_boost_optimizations_remaining = isset(PegasaasAccelerator::$settings['limits']['monthly_optimizations']) ;
$show_monthly_image_optimizations_remaining = isset(PegasaasAccelerator::$settings['settings']['basic_image_optimization']);
if ( $show_monthly_boost_optimizations_remaining || $show_monthly_image_optimizations_remaining ) {  
if ($show_monthly_boost_optimizations_remaining) {
	
					$percent_used = ((PegasaasAccelerator::$settings['limits']['monthly_optimizations'] - PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining']) / PegasaasAccelerator::$settings['limits']['monthly_optimizations']) * 100;
					$days_left_in_month = date("t") - date("j") + 1;
					$percent_used = 90;
					if ($percent_used > 90) {
						$boost_gauge_class = "stats-danger";
				
					} else if ($percent_used > 75) {
						$boost_gauge_class = "stats-warning";
					} else {
						$boost_gauge_class = "";
					}	
}
	if ($show_monthly_image_optimizations_remaining) {
		$stats = $pegasaas->cache->get_basic_image_cache_stats();	
					$image_optimizations_remaining = PegasaasAccelerator::$settings['settings']['basic_image_optimization']['images_per_month'] - $stats['optimizations_so_far_this_month']	;
					
					$stats['percentage_of_use'] = 95;
					if ($stats['percentage_of_use'] > 90) {
						$image_gauge_class = "stats-danger";
					} else if ($stats['percentage_of_use'] > 75) {
						$image_gauge_class = "stats-warning";
					} else {
						$image_gauge_class = "";
					}
	}
																								   
?>
<div class='top-corner-stats <?php if ($show_monthly_boost_optimizations_remaining && $show_monthly_image_optimizations_remaining) { ?>top-corner-stats-double<?php } else { ?>top-corner-stats-single<?php } ?> <?php if ($show_monthly_boost_optimizations_remaining) { print $image_gauge_class; } else { print $boost_gauge_class." b"; } ?>'>
	<div class='top-corner-status-inner <?php if ($show_monthly_image_optimizations_remaining) {  print $boost_gauge_class; } ?>'>
	<?php if ($show_monthly_boost_optimizations_remaining) { ?>	

			<div class='top-right-gauge'>

					<div class='small-gauge-title'><?php echo __("Optimization Boosts Remaining", "pegasaas-accelerator"); ?></div>
					<div class='small-gauge-value'>
						<span id='credits-remaining'><?php echo PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining']; ?></span><span class='of-total-credits'>/<?php echo PegasaasAccelerator::$settings['limits']['monthly_optimizations']; ?></span>
					</div>
					
				
					<a rel="opopener noreferrer" target="_blank" class="btn btn-lg btn-success" href='https://pegasaas.com/upgrade/?utm_source=pegasaas-accelerator-lite-dashboard-optimization-stat&upgrade_key=<?php echo PegasaasAccelerator::$settings['api_key']; ?>-<?php echo PegasaasAccelerator::$settings['installation_id']; ?>&site=<?php echo $pegasaas->utils->get_http_host(); ?>&current=<?php echo PegasaasAccelerator::$settings['subscription']; ?>'><?php echo __("Upgrade To Pro", "pegasaas-accelerator"); ?></a>					

  				
			</div>
		
	
	<?php } ?>
	<?php if ($show_monthly_image_optimizations_remaining && PegasaasAccelerator::$settings['settings']['basic_image_optimization']['images_per_month'] != 9999) {
					
					
	?>	
	
			
		
				<div class='top-right-gauge'> 
	
					
					<div class='small-gauge-title'><?php echo __("Image Optimizations Remaining", "pegasaas-accelerator"); ?></div>

					<div class='small-gauge-value'>
					<?php echo $image_optimizations_remaining; ?><span>/<?php echo PegasaasAccelerator::$settings['settings']['basic_image_optimization']['images_per_month']; ?></span>
					</div>
														 <?php if (!$show_monthly_boost_optimizations_remaining) { ?>
						<a rel="opopener noreferrer" target="_blank" class="btn btn-lg btn-success" href='https://pegasaas.com/upgrade/?utm_source=pegasaas-accelerator-lite-dashboard-image-stat&upgrade_key=<?php echo PegasaasAccelerator::$settings['api_key']; ?>-<?php echo PegasaasAccelerator::$settings['installation_id']; ?>&site=<?php echo $pegasaas->utils->get_http_host(); ?>&current=<?php echo PegasaasAccelerator::$settings['subscription']; ?>'><?php echo __("Upgrade", "pegasaas-accelerator"); ?></a>					
					 <?php } ?>

  				</div>

		
	 
	<?php } ?>
		</div>
	</div>

<?php } ?>
