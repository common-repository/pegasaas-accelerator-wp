<?php if (PegasaasAccelerator::$settings['status'] > 0 ) { 
	if (PegasaasAccelerator::$settings['settings']['display_mode']['status'] == 1) { 
		$border_color = "#fff"; 
	} else { 
		$border_color = "#2C343B"; 
	}

?>		
	var merged_data = {
    datasets: [{
        data: [<?php echo $score_data['score']; ?><?php foreach ($score_data['opportunities'] as $rule) { ?>,<?php echo ($rule['impact']); } ?>],
 		backgroundColor:[<?php if ($score_data['score'] >= 85) { print "'#5cb85c'"; } else if ($score_data['score'] >= 75) { print "'#f0ad4e'"; } else { print "'#cc0000'"; } ?><?php foreach ($score_data['opportunities'] as $rule) { print ","; if ($rule['impact'] < 2) { print "'#aaa'"; } else if ($rule['impact'] < 6) { print "'#f0ad4e'"; } else { print "'#cc0000'"; }  } ?>],
		borderColor: ['<?php echo $border_color; ?>'<?php foreach ($score_data['opportunities'] as $rule) { ?>,'<?php echo $border_color; ?>'<?php } ?>],
		borderWidth: ['0'<?php foreach ($score_data['opportunities'] as $rule) { ?>,'0'<?php } ?>],
   }],

    // These labels appear in the legend and in the tooltips when hovering different arcs
    labels: ['Current Site Score'
<?php foreach ($score_data['opportunities'] as $rule) { ?>,'<?php echo ($rule['name']); ?>'<?php } ?>
        
    ],
};

var merged_score 			= "<?php echo $score_data['score']; ?>";
var merged_desktop_score 	= "<?php echo $score_data['desktop_score']; ?>";
var merged_mobile_score 	= "<?php echo $score_data['mobile_score']; ?>";
				
<?php if ($_GET['c'] != "render-merged-chart" && $_POST['c'] != "render-merged-chart") { ?>			
var ctx = jQuery("#pegasaas-merged-site-speed-chart canvas");		
var merged_pagespeed_chart = new Chart(ctx, {
    type: 'doughnut',
    data: merged_data,
    options: merged_options
});
<?php } ?>
<?php } ?>	