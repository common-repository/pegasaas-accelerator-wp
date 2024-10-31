<?php 

if (PegasaasAccelerator::$settings['status'] > 0 ) {  ?>

	


	<div class='col-xs-12 col-sm-6 col-sm-offset-3 col-lg-6 col-lg-offset-3' > 
	    <div class='primary-gauge-3 text-center'>
		  <svg style='max-width: 400px; margin: auto;' width="100%" height="100%" viewBox="0 0 42 42" class="donut">
			    <defs>
    				<clipPath id="mode-switcher-bg-left">
      						<rect x="0" y="27" width="20.85" height="15" />
    				</clipPath>
     				<clipPath id="mode-switcher-bg-right">
      						<rect x="21.15" y="27" width="21" height="15" />
    				</clipPath>
			  </defs>
			<circle class="inner-donut" cx="21" cy="21" r="14.5" fill="transparent" stroke="rgba(0,0,0,0.25)" stroke-width="2"></circle>
			<circle class="outer-donut" cx="21" cy="21" r="17.5" fill="transparent" stroke="rgba(0,0,0,0.5)" stroke-width="3"></circle>
			<circle id="inner-gauge-line" class="inner-gauge-line" cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="#fff" stroke-width=".25" stroke-dasharray="34.75 65.25" stroke-dashoffset="92.75"></circle>

			<circle id="gauge-stat-bg-left" clip-path="url(#mode-switcher-bg-left)"  cx="21" cy="21" r="9.75" stroke="rgba(0,0,0,0.15)" fill="transparent" stroke-width='7'></circle>
			<circle id="gauge-stat-bg-right" clip-path="url(#mode-switcher-bg-right)" cx="21" cy="21" r="9.75" stroke="rgba(0,0,0,0.15)" fill="transparent" stroke-width='7'></circle>

			<circle id="inner-inner-donut"  cx="21" cy="21" r="9.75" stroke="rgba(0,0,0,0.15)" fill="transparent" stroke-width='7'></circle>
			  
			  
			<circle id="ms-bg-left" cx="21" cy="21" r="21.7" fill="transparent" stroke="rgba(0,0,0,0.50)" stroke-width="5" stroke-dasharray="10 135" stroke-dashoffset="52.75"></circle>											
			<circle id="ms-bg-right" cx="21" cy="21" r="21.7" fill="transparent" stroke="rgba(0,0,0,0.50)" stroke-width="5" stroke-dasharray="10 135" stroke-dashoffset="42.5"></circle>											
	
			<circle id="projected-conversion-rate-change-bg" cx="21" cy="21" r="23.2" fill="transparent" stroke="rgba(0,0,0,0.50)" stroke-width="8" stroke-dasharray="20.75 135" stroke-dashoffset="-15.5"></circle>											
			<circle id="projected-bounce-rate-change-bg" cx="21" cy="21" r="23.2" fill="transparent" stroke="rgba(0,0,0,0.50)" stroke-width="8" stroke-dasharray="20.75 135" stroke-dashoffset="119.25"></circle>											

			  
	<?php
	$radius = 15.91549430918954;
	// score of 0 = 210
	// 360 * .6525 = 2.349 degrees per pagespeed point


	$degrees_mobile = 208 - 2.344 * $score_data['mobile_score'];
	$cx_mobile = 21+$radius * cos( deg2rad($degrees_mobile) );
	$cy_mobile = 21-$radius * sin(  deg2rad($degrees_mobile) );
	
	$stroke_length_mobile = 65.25 * $score_data['mobile_score']/100;
	$stroke_length_mobile_gap = 100 - $stroke_length_mobile;

	
	$degrees_desktop = 208 - 2.344 * $score_data['desktop_score'];
	$cx_desktop = 21+$radius * cos( deg2rad($degrees_desktop) );
	$cy_desktop = 21-$radius * sin(  deg2rad($degrees_desktop) );
	
	$stroke_length_desktop = 65.25 * $score_data['desktop_score']/100;
	$stroke_length_desktop_gap = 100 - $stroke_length_desktop;
	
	if ($score_data['desktop_score'] >= 90) {
		$desktop_score_color = "#5cb85c";
	} else if ($score_data['desktop_score'] >= 50) {
		$desktop_score_color = "rgb(240,173,78)";
	} else {
		$desktop_score_color = "#cc0000";
	}
	if ($score_data['mobile_score'] >= 90) {
		$mobile_score_color = "#5cb85c";
	} else if ($score_data['mobile_score'] >= 50) {
		$mobile_score_color = "rgb(240,173,78)";
	} else {
		$mobile_score_color = "#cc0000";
	}	
	?>
	<circle id="score-gauge-bg"   cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="rgba(0,0,0,0.25)" stroke-width=".5"></circle>											
			  
	<circle id="mobile-score-gauge"     class="mobile-only"  cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="<?php echo $mobile_score_color; ?>" stroke-width=".25" stroke-dasharray="<?php echo $stroke_length_mobile; ?> <?php echo $stroke_length_mobile_gap; ?>" stroke-dashoffset="58"></circle>											
	<circle id="mobile-score-endpoint"  class='mobile-only'  cx="<?php echo $cx_mobile; ?>" cy="<?php echo $cy_mobile; ?>" r=".40" fill="<?php echo $mobile_score_color; ?>" stroke="transparent" stroke-width=".25" ></circle>
	
	<circle id="desktop-score-gauge"     class="desktop-only"  cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="<?php echo $desktop_score_color; ?>" stroke-width=".25" stroke-dasharray="<?php echo $stroke_length_desktop; ?> <?php echo $stroke_length_desktop_gap; ?>" stroke-dashoffset="58"></circle>											
	<circle id="desktop-score-endpoint" class='desktop-only' cx="<?php echo $cx_desktop; ?>" cy="<?php echo $cy_desktop; ?>" r=".40" fill="<?php echo $desktop_score_color; ?>" stroke="transparent" stroke-width=".25" ></circle>

	
	<path id="curve" d="M11,35 C-4.5,20.5  9.5,3.5 21,4.5 M21,4.5 C32.5,3.5 46.5,20.5 31,35"  fill="none" stroke="ff0000" stroke-width="2px" />
	  <text id="gauge-score-numbers" width="34" height="34" style='color:#fff; font-size: 1.45px' fill="#fff">
		<textPath alignment-baseline="top" xlink:href="#curve" text-anchor="middle" startOffset="50%">
0&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
10 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
20 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
30 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
40 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;    
50 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
60 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
70 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
80 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
90 &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;
100
		</textPath>
	  </text>
</svg>
			<?php 
	$conditioned_mobile_score = str_replace(".", "<span class='super'>.", $score_data['mobile_score']);
	if (strstr($conditioned_mobile_score, "super")) {
		$conditioned_mobile_score .= "</span>";
	}
	
	$conditioned_desktop_score = str_replace(".", "<span class='super'>.", $score_data['desktop_score']);
	if (strstr($conditioned_desktop_score, "super")) {
		$conditioned_desktop_score .= "</span>";
	}	
	?>
			<div id="pegasaas-site-speed-display">
					<span class='mobile-only'><?php echo $conditioned_mobile_score;  ?></span>
					<span class='desktop-only'><?php echo $conditioned_desktop_score;  ?></span>
					<?php if ($prepping) { ?>
				<i class='svg-icon svg-tail-spin-white svg-icon-inner'></i>
				<i class='svg-icon svg-tail-spin-white svg-icon-outer'></i>
				<i class='svg-icon svg-loading'></i>
				<span class="js-rotating">Scanning,Optimizing</span> 
				<?php } ?>	
				</div>
			<?php if ($prepping) { ?>
			<script>
			  
				  jQuery("#pegasaas-site-speed-display .mobile-only").html("");
				  jQuery("#pegasaas-site-speed-display .desktop-only").html("");
			 
			</script>
			<?php } ?>
				<div class='primary-gauge-title'>Average <span class='mobile-only'>Mobile</span><span class='desktop-only'>Desktop</span> PageSpeed Score
				<span class='mobile-only'>
				<i class=' fa fa-question-circle' 
										  data-toggle='popover' 
										  data-html='true' 
										  data-placement='bottom'
										  data-trigger='click hover'
										  title='Average Mobile PageSpeed Score' 
										  data-content='This score represents the average mobile PageSpeed score for all pages in your site that have been scanned. <br><br>It is more difficult to achieve a fast mobile PageSpeed score because mobile devices are slower and tend to be connected to the Internet via celluar connection.  Mobile PageSpeed scores are scored based upon a device connecting at 3G speeds.'></i>
							</span>
							<span>
				
								<i class='desktop-only fa fa-question-circle' 
										  data-toggle='popover' 
										  data-html='true' 
										  data-placement='bottom'
						   				  data-trigger='click hover'
										  title='Average Desktop PageSpeed Score' 
										  data-content='This score represents the average desktop PageSpeed score for all pages in your site that have been scanned. <br><br>It is easer to achieve a higher desktop PageSpeed score as desktop computers have faster CPUs and have faster Internet connections.'></i>
					</span>
				</div>
			

			
			<div class="mode-switcher">
		  		<p class="fieldset">
					<input type="radio" name="mode" value="mobile" id="mobile" <?php if ($view_mode == "mobile-mode") { print "checked"; } ?>>
					<label for="mobile"><i class='<?php echo PEGASAAS_MOBILE_ICON_CLASS; ?>'></i></label>

					<input type="radio" name="mode" value="desktop" id="desktop" <?php if ($view_mode == "desktop-mode") { print "checked"; } ?>>
					<label for="desktop"><i class='<?php echo PEGASAAS_DESKTOP_ICON_CLASS; ?>'></i></label>

					<span class="mode-switch"></span>
				</p>
			</div> 
			
			<?php
				
							
				$benchmark_data['mobile_score_change'] = $benchmark_data['mobile_score'] - $score_data['mobile_score'];
				$benchmark_data['desktop_score_change'] = $benchmark_data['desktop_score'] - $score_data['desktop_score'];
				if ($benchmark_data['mobile_score_change'] > 0) {
					$primary_change_icon_mobile = 'fa-long-arrow-down';
					$primary_change_icon_mobile = 'fa-minus';
				} else {
					$primary_change_icon_mobile = 'fa-long-arrow-up'; 
					$primary_change_icon_mobile = 'fa-plus'; 

					$benchmark_data['mobile_score_change'] = number_format($benchmark_data['mobile_score_change'] * -1, 1, '.', '');
				}
				
				if ($benchmark_data['desktop_score_change'] > 0) {
					$primary_change_icon_desktop = 'fa-long-arrow-down';
					$primary_change_icon_desktop = 'fa-minus';
				} else {
					$primary_change_icon_desktop = 'fa-long-arrow-up'; 
					$primary_change_icon_desktop = 'fa-plus'; 
					$benchmark_data['desktop_score_change'] = number_format($benchmark_data['desktop_score_change'] * -1, 1, '.', '');
				}				
				?>
				
  				<div class='primary-gauge-reference mobile-only'>
					<div class='pgr-l'><span class='pgrlscore'><?php echo $benchmark_data['mobile_score']; ?></span></div>
					<div class='pgr-r'><i class='fa <?php echo $primary_change_icon_mobile; ?>'></i> <?php echo number_format($benchmark_data['mobile_score_change'], 0, '.', ','); ?></div>
				</div>


  				<div class='primary-gauge-reference desktop-only'>
					<div class='pgr-l'><span class='pgrlscore'><?php echo $benchmark_data['desktop_score']; ?></span></div>

<div class='pgr-r'><i class='fa <?php echo $primary_change_icon_desktop; ?>'></i> <?php echo number_format($benchmark_data['desktop_score_change'], 0, '.', ','); ?></div>
				</div>
			
			<?php 

	$show_analytics_warning  = isset($score_data['analytics_detected']) && is_array($score_data['analytics_detected']) ? sizeof($score_data['analytics_detected']) > 0 : false;
	$show_slow_sever_warning = $pegasaas->interface->get_interface_load_time('dashboard') > 3.5;
	$show_multi_server_icon  = $pegasaas->is_multi_server_installation();
	$number_of_icons = 0;
	if ($show_analytics_warning) { $number_of_icons++; }
	if ($show_slow_sever_warning) { $number_of_icons++; }
	if ($show_multi_server_icon) { $number_of_icons++; }
	?>
				<?php if ($show_analytics_warning) { ?>
				<span class='analytics-detected-container fade'><i class="material-icons analytics-detected <?php if ($number_of_icons == 2) { ?>dashboard-icon-left<?php } ?>"
				     data-toggle='popover' 
										  data-html='true' 
										  data-placement='bottom'
						   				  data-trigger='click'
										  title='Slow Loading Analytics Scripts Detected' 
										  data-content='It appears that you have analytics <?php if (in_array("Google Tag Manager", $score_data['analytics_detected'])) { print "and tag manager"; } ?> scripts that are included in your page, which could be causing your pages to load slowly and give you a poor PageSpeed score..<br/><br/>You should consider use of these services carefuly and remove any that are not considered critical to the operation of your business.<br><br>To learn more, please read our <a href="https://pegasaas.com/analytics-and-tag-managers-and-their-impact-on-web-performance/" target="_blank">article about "Analytics and Tag Managers and Their Impact on Web Performance"</a>.<br/><br/>Services Detected:<br/><br/><?php foreach ($score_data['analytics_detected'] as $platform) { echo $platform."<br>"; } ?>'
				  >notification_important</i>
				</span>	
				<script>jQuery(document).ready(function() { jQuery(".analytics-detected-container").addClass("in") });</script>
				<?php } ?>
				<?php if ($show_slow_sever_warning) { ?>
				<span class='slow-server-resources-detected-container fade'><i class="material-icons slow-server-resources-detected  <?php if ($number_of_icons == 2) { ?>dashboard-icon-right<?php } else if ($number_of_icons == 3) { ?>dashboard-icon-far-right<?php } ?>"
				     data-toggle='popover' 
										  data-html='true' 
										  data-placement='bottom'
						   				  data-trigger='click'
										  title='Slow Server Detected' 
										  data-content='Your WordPress dashboard recently took <?php 
											echo number_format($pegasaas->interface->get_interface_load_time('dashboard'), 1, '.', ''); 
					?> seconds to load.  This may impact the ability for the plugin to optimize your website.<br><br>If you continue to see this warning, consider taking the following possible solutions:<br/><br/><ul><li>Reduce the number of installed and active plugins</li><li>Move to faster web hosting</li></ul>'
									  >build</i>
				</span>	
				<script>jQuery(document).ready(function() { jQuery(".slow-server-resources-detected-container").addClass("in") });</script>
				<?php } ?>	
			
				<?php if ($show_multi_server_icon) { ?>
				<span class='multi-server-installation-detected-container fade'><i class="material-icons multi-server-installation-detected <?php
	if ($number_of_icons == 2 && $show_analytics_warning) { 
					?>multi-server-installation-detected-right<?php 
	} else if ($number_of_icons == 2) {
					?>dashboard-icon-left<?php 
	} else if ($number_of_icons == 3) { ?>dashboard-icon-far-left<?php }  ?>"
				     data-toggle='popover' 
										  data-html='true' 
										  data-placement='bottom'
						   				  data-trigger='click'
										  title='Multi-Server Installation Detected' 
										  data-content="We have detected that you are running an installation on multiple servers -- that's awesome!<br><br>We've got you covered -- all premium optimizations will be deployed to all of your servers, and when you clear cache, all servers will be instructed accordingly."
									  >group_work</i>
				</span>	
				<script>jQuery(document).ready(function() { jQuery(".multi-server-installation-detected-container").addClass("in") });</script>
				<?php } ?>
			
			
				<span class='advanced-display-settings fade'><i class="material-icons"
				      data-toggle='popover' 
					  data-html='true' 
					  data-placement='top'
					  data-trigger='click'
					  title='Web Performance Metrics' 
					  data-content='Choose the web performance metrics that you wish to show in the interface.<br>
					<input rel="ttfb" onchange="toggle_advanced_web_perf_display_change(this)" type="checkbox" class="material-selector" id="advanced_web_perf_ttfb" <?php if ($pegasaas->interface->web_perf_metrics_ttfb_class == "") { print "checked"; }?>><label for="advanced_web_perf_ttfb" class="material-selector" >Time To First Byte</label><br>
					<input rel="fcp" onchange="toggle_advanced_web_perf_display_change(this)" type="checkbox" class="material-selector" id="advanced_web_perf_fcp" <?php if ($pegasaas->interface->web_perf_metrics_fcp_class == "") { print "checked"; }?>><label for="advanced_web_perf_fcp" class="material-selector">First Contentful Paint</label><br>
					<input rel="si" onchange="toggle_advanced_web_perf_display_change(this)" type="checkbox" class="material-selector" id="advanced_web_perf_si" <?php if ($pegasaas->interface->web_perf_metrics_si_class == "") { print "checked"; }?>><label for="advanced_web_perf_si" class="material-selector">Speed Index</label><br>
					<input rel="lcp" onchange="toggle_advanced_web_perf_display_change(this)" type="checkbox" class="material-selector" id="advanced_web_perf_lcp" <?php if ($pegasaas->interface->web_perf_metrics_lcp_class == "") { print "checked"; }?>><label for="advanced_web_perf_lcp" class="material-selector">Largest Contentful Paint</label><br>
					<input rel="tti" onchange="toggle_advanced_web_perf_display_change(this)" type="checkbox" class="material-selector" id="advanced_web_perf_tti" <?php if ($pegasaas->interface->web_perf_metrics_tti_class == "") { print "checked"; }?>><label for="advanced_web_perf_tti" class="material-selector">Time To Interactive</label><br>
					<input rel="tbt" onchange="toggle_advanced_web_perf_display_change(this)" type="checkbox" class="material-selector" id="advanced_web_perf_tbt" <?php if ($pegasaas->interface->web_perf_metrics_tbt_class == "") { print "checked"; }?>><label for="advanced_web_perf_tbt" class="material-selector">Total Blocking Time</label><br>
					<input rel="cls" onchange="toggle_advanced_web_perf_display_change(this)" type="checkbox" class="material-selector" id="advanced_web_perf_cls" <?php if ($pegasaas->interface->web_perf_metrics_cls_class == "") { print "checked"; }?>><label for="advanced_web_perf_cls" class="material-selector">Cumulative Layout Shift</label>'
				  >settings</i>
				</span>	
				<script>jQuery(document).ready(function() { jQuery(".advanced-display-settings").addClass("in") });</script>
				<span class='prepping-mode-instructions fade'><i class="material-icons"
				      data-toggle='popover' 
					  data-html='true' 
					  data-placement='top'
					  data-trigger='click'
					  title='Initialization' 
					  data-content='The system is currently performing the first few optimizations, and scanning those pages for their web performance metrics.  If the interface does not
					update with a score within 10 minutes, you can instruct the plugin to <a href="?page=pegasaas-accelerator&c=force-recalculation" class="btn btn-xs btn-default">recalculate the scores</a>.  If that does not help, please <a  href="?page=pegasaas-accelerator&skip=to-support">contact support</a>'
				  >help</i>
				</span>	
			<script>jQuery(document).ready(function() { jQuery(".prepping-mode-instructions").addClass("in") });</script>
			
		</div>

</div>
<?php
	$pegasaas->utils->console_log("Dashboard Display 2: 245");
	$performance_gauges = $pegasaas->scanner->get_site_performance_metrics();
$pegasaas->utils->console_log("Dashboard Display 2: 247");
include("bounce-rate-change.php"); 
include("conversion-rate-change.php"); 
	$pegasaas->utils->console_log("Dashboard Display 2: 250");
?>

</div> <!-- close row that was opened in parent -->
<style></style>



<?php
  include "top-right.php";
	include "top-left.php";
	
		 				   								 
 
  $rating_color["fast"] = "#5cb85c";
  $rating_color["average"] = "#101516";
  $rating_color["average"] = "rgb(240,173,78)";
  $rating_color["slow"] = "#c00";
  $rating_bg_color["fast"] = "rgba(92,184,92,0.25)";
  $rating_bg_color["average"] = "rgba(240,173,78,0.25)";	
  $rating_bg_color["slow"] = "rgba(204,0,0,0.25)";	
?>
<div class='row <?php echo $pegasaas->interface->get_advanced_visible_gauges_class(); ?>' id="web-performance-metrics">
	<div class='col-sm-gauge-gutter'></div>
	<div class='col-sm-gauge-container'>
		<div class='row' style='display: flex'> 
		<?php foreach ($performance_gauges['mobile'] as $index => $gauge) {
	
	
	  		if ($gauge['value'] == 'nan') {
				continue;
			}
			if ($index == "time-to-first-byte") { 
				$precision = 0;
			} else {
				$precision = 1;
			}
			if ($gauge['change'] > 0) {
				$gauge['icon'] = 'fa-long-arrow-up';
			} else {
				$gauge['icon'] = 'fa-long-arrow-down'; 
				$gauge['change'] = number_format($gauge['change'] * -1, $precision, '.', '');
			}
	  
			$desktop_gauge = $performance_gauges['desktop']["$index"];
			if ($desktop_gauge['change'] > 0) {
				$desktop_gauge['icon'] = 'fa-long-arrow-up';
			} else {
				$desktop_gauge['icon'] = 'fa-long-arrow-down'; 
				$desktop_gauge['change'] = number_format($desktop_gauge['change'] * -1, $precision, '.', '');
			}	
	  		$web_perf_class = "web_perf_metrics_".strtolower($gauge['abbr'])."_class";
	 		$web_perf_data  = "data-display-element='".strtolower($gauge['abbr'])."'";
			?>
			<div class='col-sm-gauge <?php if ($index == "first-meaingful-paint" || $index == "interactive"    || $index == "total-blocking-time"  || $index == "cumulative-layout-shift"  || $index == "first-cpu-idle") { ?>hidden-intermediate<?php } ?> <?php
	  
	  echo $pegasaas->interface->{$web_perf_class}; ?>' <?php echo $web_perf_data; ?>>
			
				<div class='small-gauge-container'>
 				
	  <svg style='max-width: 200px; margin: auto;' width="100%" height="100%" viewBox="0 0 42 42" class="donut">
			    <defs>
    				<clipPath id="mode-switcher-bg-left">
      						<rect x="0" y="27" width="20.85" height="15" />
    				</clipPath>
     				<clipPath id="mode-switcher-bg-right">
      						<rect x="21.15" y="27" width="21" height="15" />
    				</clipPath>
			  </defs>
			  
 
  
	<circle class="inner-donut-small-gauge" cx="21" cy="21" r="14.95"  stroke="rgba(0,0,0,0.5)" stroke-width="1.5"></circle>
	<circle class="outer-donut" cx="21" cy="21" r="17.5" fill="transparent" stroke="rgba(0,0,0,0.75)" stroke-width="3.75"></circle>
	<!--<circle id="inner-gauge-line" class="inner-gauge-line" cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="#fff" stroke-width=".25"></circle>-->

	<circle id="gauge-stat-bg-left"  clip-path="url(#mode-switcher-bg-left)"  cx="21" cy="21" r="10.5" stroke="rgba(0,0,0,0.3)" fill="transparent" stroke-width='7'></circle>
	<circle id="gauge-stat-bg-right" clip-path="url(#mode-switcher-bg-right)" cx="21" cy="21" r="10.5" stroke="rgba(0,0,0,0.3)" fill="transparent" stroke-width='7'></circle>
  			  
	<?php
	$radius = 15.91549430918954;
	// score of 0 = 210
	// 360 * .65 = 2.34 degrees per pagespeed point
	
		
	$degrees_mobile = 208 - 2.344 * ($gauge["percentile"] * 100);
	//  print "X:".$degrees_mobile."<br>";
	$cx_mobile = 21+$radius * cos( deg2rad($degrees_mobile) );
	$cy_mobile = 21-$radius * sin(  deg2rad($degrees_mobile) );
	
	$stroke_length_mobile = 65.25 *  $gauge["percentile"];
	$stroke_length_mobile_gap = 100 - $stroke_length_mobile;

	
	$degrees_desktop = 208 - 2.344 *  ($desktop_gauge["percentile"] * 100);
	$cx_desktop = 21 + $radius * cos( deg2rad($degrees_desktop) );
	$cy_desktop = 21 - $radius * sin(  deg2rad($degrees_desktop) );
	
	$stroke_length_desktop = 65.25 *  $desktop_gauge["percentile"];
	$stroke_length_desktop_gap = 100 - $stroke_length_desktop;
	
	?>
	<circle id="mobile-score-gauge"     class="mobile-only"  cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="<?php $rating = $gauge['rating']; echo $rating_color["{$rating}"]; ?>" stroke-width=".5" stroke-dasharray="<?php echo $stroke_length_mobile; ?> <?php echo $stroke_length_mobile_gap; ?>" stroke-dashoffset="58"></circle>											
	<circle id="mobile-score-endpoint"  class='mobile-only'  cx="<?php echo $cx_mobile; ?>" cy="<?php echo $cy_mobile; ?>" r=".40" fill="<?php $rating = $gauge['rating']; echo $rating_color["{$rating}"]; ?>" stroke="transparent" stroke-width="1" ></circle>
	
	<circle id="desktop-score-gauge"     class="desktop-only"  cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="<?php $rating = $desktop_gauge['rating']; echo $rating_color["{$rating}"]; ?>" stroke-width=".5" stroke-dasharray="<?php echo $stroke_length_desktop; ?> <?php echo $stroke_length_desktop_gap; ?>" stroke-dashoffset="58"></circle>											
	<circle id="desktop-score-endpoint"  class='desktop-only'  cx="<?php echo $cx_desktop; ?>" cy="<?php echo $cy_desktop; ?>" r=".40" fill="<?php $rating = $desktop_gauge['rating']; echo $rating_color["{$rating}"]; ?>" stroke="transparent" stroke-width="1" ></circle>

	
	<path id="curve" d="M11,35 C-4.5,20.5  9.5,3.5 21,4.5 M21,4.5 C32.5,3.5 46.5,20.5 31,35"  fill="none" stroke="ff0000" stroke-width="2px" />
	  <text width="34" height="34" style='color:#fff; font-size: 2px; text-transform: uppercase' fill="#fff">
		<textPath alignment-baseline="top" xlink:href="#curve" text-anchor="middle" startOffset="50%">
slow&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
&nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
&nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;

 
average  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;

  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
fast
		</textPath>
	  </text>

</svg>
					
				<div class='small-gauge mobile-only'> 	
					<div class='small-gauge-value'>
					<?php echo $gauge['value']; ?><span><?php echo $gauge['unit']; ?></span>
					</div>
					<div class='small-gauge-title'><?php echo $gauge['title']; ?></div>
					<div class='small-gauge-reference'>
					  <div class='sgr-l'><?php echo $gauge['before']; ?></div>
						<div class='sgr-c'>
														<i class='fa fa-question' 
										  data-toggle='popover' 
										  data-html='true' 
										  data-placement='bottom' 
										  data-trigger='click hover'
										  title='<?php _e($gauge['title']); ?>' 
										  data-content="<?php echo htmlentities($gauge['description']); ?><br/><br/>To get a FAST <?php _e($gauge['title']); ?> score, keep this metric to under <?php echo $performance_gauges['metrics']["$index"]['fast'].$gauge['unit']; ?>.<br><br>The number to the left shows the average <?php _e($gauge['title']); ?> prior to optimization.<br/><br/>The number to the right shows the average change in time."></i>

							
							</div>
						<div class='sgr-r'><i class='fa <?php echo $gauge['icon']; ?>'></i> <?php echo $gauge['change']; ?></div>
					</div>
					
  				</div>
				<div class='small-gauge desktop-only'> 
					<div class='small-gauge-value'>
					<?php echo $desktop_gauge['value']; ?><span><?php echo $desktop_gauge['unit']; ?></span>
					</div>
					<div class='small-gauge-reference'>
					  <div class='sgr-l'><?php echo $desktop_gauge['before']; ?></div>
						<div class='sgr-c'>
																				<i class='fa fa-question' 
										  data-toggle='popover' 
										  data-html='true' 
										  data-placement='bottom' 
										  data-trigger='click hover'
										  title='<?php _e($gauge['title']); ?>' 
										  data-content="<?php echo htmlentities($gauge['description']); ?><br/><br/>To get a FAST <?php _e($gauge['title']); ?> score, keep this metric to under <?php echo $performance_gauges['metrics']["$index"]['fast'].$gauge['unit']; ?>.<br><br>The number to the left shows the average <?php _e($gauge['title']); ?> prior to optimization.<br/><br/>The number to the right shows the average change in time."></i>

							
							</div>
						<div class='sgr-r'><i class='fa <?php echo $desktop_gauge['icon']; ?>'></i> <?php echo $desktop_gauge['change']; ?></div>
					</div>
					<div class='small-gauge-title'><?php echo $desktop_gauge['title']; ?></div>
  				</div>				
  			</div>
				</div>
			
		<?php } ?>
		</div>
	</div>
	<div class='col-sm-gauge-gutter'></div>
</div>


<?php }  else { ?>
<div class='text-center diagnostic-mode-container'>
<h3>Diagnostic Mode</h3>
<p>Pegasaas is currently 'disabled' and is in 'diagnostic mode'.  This means that your pages will not be accelerated if requested by a visitor.</p>
<p>Diagnostic mode may be requested during troubleshooting by Pegasaas support.</p>
	</div>
<?php } 

		
?>
