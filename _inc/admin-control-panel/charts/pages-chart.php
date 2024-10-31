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
   var pages_accelerated_data = {
    datasets: [{
        data: [<?php echo $pages_accelerated['accelerated']; 
		?>,<?php echo $pages_accelerated['pending']; 
		?>,<?php echo $pages_accelerated['not_accelerated']; 
		?>],
 		backgroundColor:[<?php if (PegasaasAccelerator::$settings['settings']['page_caching']['status'] == 1) { 
?>'#5cb85c', 'rgb(92, 184, 92, 0.50)',<?php 
} else { ?>'#f0ad4e', 'rgb(240, 173, 78, 0.90)',<?php } ?>'<?php echo $grey_color; ?>'],
	borderColor: ['<?php echo $border_color; ?>','<?php echo $border_color; ?>','<?php echo $border_color; ?>'],
	borderWidth: ['0','0','0'],

   }],

    // These labels appear in the legend and in the tooltips when hovering different arcs
    labels: ['Pages Accelerated <?php if (PegasaasAccelerator::$settings['settings']['page_caching']['status'] == 0) { ?>(Caching Disabled)<?php } ?>','Pages Being Accelerated','Pages Not Accelerated']
};
var pages_accelerated_count = "<?php echo $pages_accelerated['accelerated']; ?>";
			
<?php if ($_GET['c'] != "render-pages-accelerated-chart" && $_POST['c'] != "render-pages-accelerated-chart") { ?>	
var pactx = jQuery("#pegasaas-site-pages-accelerated-chart canvas");	
var pages_accelerated_pagespeed_chart = new Chart(pactx, {
    type: 'doughnut',
    data: pages_accelerated_data,
    options: pages_accelerated_options
});
<?php } ?>