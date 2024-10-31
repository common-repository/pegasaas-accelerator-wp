<?php 

$conversion_rate_change_mobile = number_format($performance_gauges['mobile']["speed-index"]['change'] * 12, 0, '.', '');
$mobile_unit = '%';

if ($conversion_rate_change_mobile > 0) {
	$conversion_rate_change_mobile = "N/C";
	$conversion_unit = "";
}
$conversion_rate_change_mobile_formatted = str_replace("-", "<i class='fa fa-long-arrow-up fa-small'></i>", $conversion_rate_change_mobile);


$conversion_rate_change_desktop = number_format($performance_gauges['desktop']["speed-index"]['change'] * 12, 0, '.', '');
$desktop_unit = '%';
if ($conversion_rate_change_desktop < -100) {
	$conversion_rate_change_desktop = -100;
	
} else if ($conversion_rate_change_desktop > 0) {
	$conversion_rate_change_desktop = "N/C";
	$desktop_unit = "";
}
$conversion_rate_change_desktop_formatted = str_replace("-", "<i class='fa fa-long-arrow-up fa-small'></i>", $conversion_rate_change_desktop);

$mobile_gauge 		= array("formatted" => $conversion_rate_change_mobile_formatted, "value" => $conversion_rate_change_mobile, "unit" => $mobile_unit);
$desktop_gauge 		= array("formatted" => $conversion_rate_change_desktop_formatted, 	 "value" => $conversion_rate_change_desktop, "unit" => $desktop_unit);


?>
<div class='performance-metric-change right-gauge'>
		
							
	<div class='small-gauge-container'>		
		<i class='fa fa-question-circle' 
										  data-toggle='popover' 
										  data-html='true' 
										  data-placement='bottom' 
										  data-trigger='click hover'
										  title='Projected Conversion Rate Change' 
										  data-content="Studies conducted by Google/SOASTA found that for every second saved of page load time that conversion rates improve by 12%.  Multiply the change in the Speed Index by 12% to get the percentage that your conversion rate is projected to improve."></i>

		<!--<svg style='max-width: 200px; margin: auto;' width="100%" height="100%" viewBox="0 0 42 42" class="donut">
			  <defs>
    				<clipPath id="right-small-gauge-bite">
      						<polygon points="0 24, 14,14 26 0, 42 0, 42 42, 0 42" />
    				</clipPath>

			  </defs>
			
			<circle clip-path="url(#right-small-gauge-bite)" class="inner-donut-small-gauge <?php if ($mobile_gauge['value'] < 0) { print "mobile-improved"; } ?> <?php if ($desktop_gauge['value'] < 0) { print "desktop-improved"; } ?>" cx="21" cy="21" r="14.95"  stroke="rgba(0,0,0,0.5)" stroke-width="0.75"></circle>
			<circle clip-path="url(#right-small-gauge-bite)" class="outer-donut" cx="21" cy="21" r="18" fill="transparent" stroke="rgba(0,0,0,0.75)" stroke-width="4.5"></circle>

			<path id="curve3" d="M3,27 C7,46 47,46 38.5,13 M38.5,13 C35,7 29,4 24,3"  fill="none" stroke="ff0000" stroke-width="2px" />

			<text width="35" height="35" style='color:#fff; font-size: 3.5px; text-transform: uppercase' fill="#fff">
				<textPath alignment-baseline="top" xlink:href="#curve3" text-anchor="right" startOffset="0%">projected conversion rate change</textPath>
	  		</text>
		</svg>
			-->		
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
		<div class='metric-description'>Projected Conversion Rate Change</div>
  	</div>
</div>	