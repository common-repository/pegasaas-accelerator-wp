<?php if (PegasaasAccelerator::$settings['status'] > 0 ) {
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
			
	var accelerated_data = {
    datasets: [{
        data: [<?php echo $score_data['score']; ?><?php foreach ($score_data['opportunities'] as $rule) { ?>,<?php echo ($rule['impact']); } ?>],
 		backgroundColor:[<?php if ($score_data['score'] >= 85) { print "'#5cb85c'"; } else if ($score_data['score'] >= 75) {
	print "'#f0ad4e'"; } else { print "'#cc0000'"; } ?><?php 
		foreach ($score_data['opportunities'] as $rule) { 
			print ","; 
			if ($rule['impact'] < 2 || true) { 
				print "'{$grey_color}'"; 
			} else if ($rule['impact'] < 6) { 
				print "'#f0ad4e'"; 
			} else { print "'#cc0000'"; }  } ?>],
		 borderColor: ['<?php print $border_color; ?>'<?php foreach ($score_data['opportunities'] as $rule) { ?>,'<?php print $border_color; ?>'<?php } ?>],
		borderWidth: ['0'<?php foreach ($score_data['opportunities'] as $rule) { ?>,'0'<?php } ?>],
   }],

    // These labels appear in the legend and in the tooltips when hovering different arcs
    labels: ['Current Site Score'
<?php foreach ($score_data['opportunities'] as $rule) { ?>,'<?php echo ($rule['name']); ?>'<?php } ?>
        
    ],
};

var accelerated_score = "<?php echo $score_data['score']; ?>";
var accelerated_desktop_score = "<?php echo $score_data['desktop_score']; ?>";
var accelerated_mobile_score = "<?php echo $score_data['mobile_score']; ?>";
				
<?php if ($_GET['c'] != "render-accelerated-chart" && $_POST['c'] != "render-accelerated-chart") { ?>			
var ctx = jQuery("#pegasaas-site-speed-chart canvas");		
var accelerated_pagespeed_chart = new Chart(ctx, {
    type: 'doughnut',
    data: accelerated_data,
    options: accelerated_options
});
<?php } ?>
<?php } ?>	