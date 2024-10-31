<?php
	if (PegasaasAccelerator::$settings['settings']['display_mode']['status'] == 1) { 
		$border_color = "#fff"; 
		$grey_color = "#333C42";
				$border_color = "rgba(0,0,0,0)"; 

	} else {
		$grey_color = "#444";
		$border_color = "#2C343B"; 
		$border_color = "rgba(0,0,0,0)"; 
	}
?>
   var benchmark_data = {
    datasets: [{
        data: [<?php echo $benchmark_data['score']; ?><?php foreach ($benchmark_data['opportunities'] as $rule) { ?>,<?php echo ($rule['impact']); } ?>],
 		backgroundColor:[<?php if ($benchmark_data['score'] >= 85) { print "'#5cb85c'"; } else if ($benchmark_data['score'] >= 75) { 
	print "'#f0ad4e'"; } else { print "'#cc0000'"; } ?><?php 
foreach ($benchmark_data['opportunities'] as $rule) { print ","; if ($rule['impact'] < 2 || true) { print "'{$grey_color}'"; } else if ($rule['impact'] < 6) { print "'rgba(240,173,78,0.90)'"; } else { print "'rgba(203,0,0,0.90)'"; } ?><?php } ?>],
		<?php if (PegasaasAccelerator::$settings['status'] < 1) { ?>
		borderColor: ['rgba(255,255,255,0.0)'<?php foreach ($benchmark_data['opportunities'] as $rule) { ?>,'rgba(255,255,255,0.0)'<?php } ?>],
	<?php } else { ?>
				 borderColor: ['<?php echo $border_color; ?>'<?php foreach ($benchmark_data['opportunities'] as $rule) { ?>,'<?php echo $border_color; ?>'<?php } ?>],
				<?php } ?>
				 borderWidth: ['0'<?php foreach ($benchmark_data['opportunities'] as $rule) { ?>,'0'<?php } ?>],

   }],

    // These labels appear in the legend and in the tooltips when hovering different arcs
    labels: ['PageSpeed score without Accelerator'
<?php foreach ($benchmark_data['opportunities'] as $rule) { ?>,'<?php echo ($rule['name']); ?>'<?php } ?>
        
    ],
};
var benchmark_score = "<?php echo $benchmark_data['score']; ?>";
var benchmark_desktop_score = "<?php echo $benchmark_data['desktop_score']; ?>";
var benchmark_mobile_score = "<?php echo $benchmark_data['mobile_score']; ?>";				
<?php if ($_GET['c'] != "render-benchmark-chart" && $_POST['c'] != "render-benchmark-chart") { ?>	
var bctx = jQuery("#pegasaas-site-benchmark-speed-chart canvas");	
var non_accelerated_pagespeed_chart = new Chart(bctx, {
    type: 'doughnut',
    data: benchmark_data,
    options: benchmark_options
});
<?php } ?>