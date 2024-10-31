<?php 

$bounce_rate_change_mobile = number_format($performance_gauges['mobile']["speed-index"]['change'] * 7, 0, '.', '');
$mobile_unit = '%';

if ($bounce_rate_change_mobile < -100) {
	$bounce_rate_change_mobile = -100;
	

} else if ($bounce_rate_change_mobile > 0) {
	$bounce_rate_change_mobile = "N/C";
	$mobile_unit = "";
}
$bounce_rate_change_mobile_formatted = str_replace("-", "<i class='fa fa-long-arrow-down fa-small'></i>", $bounce_rate_change_mobile);


$bounce_rate_change_desktop = number_format($performance_gauges['desktop']["speed-index"]['change'] * 7, 0, '.', '');
$desktop_unit = '%';
if ($bounce_rate_change_desktop < -100) {
	$bounce_rate_change_desktop = -100;
	
} else if ($bounce_rate_change_desktop > 0) {
	$bounce_rate_change_desktop = "N/C";
	$desktop_unit = "";
}
$bounce_rate_change_desktop_formatted = str_replace("-", "<i class='fa fa-long-arrow-down fa-small'></i>", $bounce_rate_change_desktop);

$mobile_gauge 		= array("formatted" => $bounce_rate_change_mobile_formatted, "value" => $bounce_rate_change_mobile, "unit" => $mobile_unit);
$desktop_gauge 	= array("formatted" => $bounce_rate_change_desktop_formatted, "value" => $bounce_rate_change_desktop, "unit" => $desktop_unit);
$bounce_rate_change_desktop = "N/C";


?>
<div class='performance-metric-change left-gauge'>
	<div class='small-gauge-container'>	
		
		<i class='fa fa-question-circle' 
										  data-toggle='popover' 
										  data-html='true' 
										  data-placement='bottom' 
										  data-trigger='click hover'
										  title='Projected Bounce Rate Change' 
										  data-content="Studies conducted by Google/SOASTA found that for every second saved, of page load time, that the bounce rates will be reduced by 7%.  Multiply the change in the Speed Index by 7% to get the percentage that your bounce rate is projected to be reduced by."></i>

					
		<div class='small-gauge mobile-only'> 	
			<div class='small-gauge-value <?php if ($mobile_gauge['value'] < 0) { print "improved"; } ?>'>
				<?php echo $mobile_gauge['formatted']; ?><span><?php echo $mobile_gauge['unit']; ?></span>
			</div>
  		</div>
		<div class='small-gauge desktop-only'> 
			<div class='small-gauge-value <?php if ($desktop_gauge['value'] < 0) { print "improved"; } ?>'>
				<?php echo $desktop_gauge['formatted']; ?><span><?php echo $desktop_gauge['unit']; ?></span>
			</div>			
  		</div>	
		<div class='metric-description'>Projected Bounce Rate Change</div>
  	</div>
</div>	