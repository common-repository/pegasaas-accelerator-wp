<?php /*
<div id="pegasaas-site-speed-chart-history"><canvas width="300" height="150"></canvas></div>

	<script type="text/javascript">
	var ctx = jQuery("#pegasaas-site-speed-chart-history canvas");
	var data = {
    datasets: [{
        data: [<?php foreach ($score_data['opportunities'] as $rule) { ?><?php echo ($rule['impact']); ?>,<?php } ?><?php echo $score_data['score']; ?>],
 		backgroundColor:[<?php foreach ($score_data['opportunities'] as $rule) { if ($rule['impact'] < 2) { print "'#aaa'"; } else if ($rule['impact'] < 6) { print "'#f0ad4e'"; } else { print "'#cc0000'"; } ?>,<?php } ?><?php if ($score_data['score'] >= 85) { print "'#5cb85c'"; } else if ($score_data['score'] >= 75) { print "'#f0ad4e'"; } else { print "'#cc0000'"; } ?>]	
   }],

    // These labels appear in the legend and in the tooltips when hovering different arcs
    labels: [
<?php foreach ($score_data['opportunities'] as $rule) { ?>'<?php echo ($rule['name']); ?>',<?php } ?>
        'Current Site Score'
    ],
};

var options =  {
        cutoutPercentage: 90,
		tooltipCaretSize: 0,
		maintainAspectRatio: true,
		legend: { display: false },
		tooltips: { enabled: false, 
		xPadding: 6,
		yPadding: 3,
		custom: function(tooltipModel) {
                // Tooltip Element
                var tooltipEl = document.getElementById('chartjs-tooltip');

                // Create element on first render
                if (!tooltipEl) {
					
                    tooltipEl = document.createElement('div');
                    tooltipEl.id = 'chartjs-tooltip';
                    tooltipEl.innerHTML = "<table></table>"
                    document.body.appendChild(tooltipEl);
                }

                // Hide if no tooltip
                if (tooltipModel.opacity === 0) {
                    tooltipEl.style.opacity = 0;
                    return;
                }

                // Set caret Position
                tooltipEl.classList.remove('above', 'below', 'no-transform');
                if (tooltipModel.yAlign) {
                    tooltipEl.classList.add(tooltipModel.yAlign);
                } else {
                    tooltipEl.classList.add('no-transform');
                }

                function getBody(bodyItem) {
                    return bodyItem.lines;
                }

                // Set Text
                if (tooltipModel.body) {
                    var titleLines = tooltipModel.title || [];
                    var bodyLines = tooltipModel.body.map(getBody);

                    var innerHtml = '<thead>';

                    titleLines.forEach(function(title) {
                        innerHtml += '<tr><th>' + title + '</th></tr>';
                    });
                    innerHtml += '</thead><tbody>';

                    bodyLines.forEach(function(body, i) {
                        var colors = tooltipModel.labelColors[i];
                        var style = 'background:' + colors.backgroundColor;
                        style += '; border-color:' + colors.borderColor;
                        style += '; border-width: 2px';
                        var span = '<span class="chartjs-tooltip-key" style="' + style + '"></span>';
                        innerHtml += '<tr><td>' + span + body + '</td></tr>';
                    });
                    innerHtml += '</tbody>';

                    var tableRoot = tooltipEl.querySelector('table');
                    tableRoot.innerHTML = innerHtml;
                }

                // `this` will be the overall tooltip
                var position = this._chart.canvas.getBoundingClientRect();

                // Display, position, and set styles for font
                tooltipEl.style.opacity = 1;
                tooltipEl.style.left = position.left + tooltipModel.caretX + 'px';
                tooltipEl.style.top = position.top + tooltipModel.caretY + 'px';
                tooltipEl.style.fontFamily = tooltipModel._fontFamily;
                tooltipEl.style.fontSize = tooltipModel.fontSize;
                tooltipEl.style.fontStyle = tooltipModel._fontStyle;
                tooltipEl.style.padding = tooltipModel.yPadding + 'px ' + tooltipModel.xPadding + 'px';
            }
		},
        };
 
var myDoughnutChart = new Chart(ctx, {
    type: 'doughnut',
    data: data,
    options: options
});
	</script>		  
	*/ ?>
	
<script>
var options =  {
		legend: { display: false },
		tooltips: { enabled: true },
		scales: { yAxes: [{  ticks: { stepSize: 25, min: 0, max: 100}}]}
        };
</script>
		  <table class='table table-striped table-bordered'>
		    <tr>
			  <th>URL</th>
	
			  <th>Score</th>
			</tr>
		  <?php 
		  $scanned_pages = $pegasaas->get_scanned_objects();
			  $scanned_pages = array_splice($scanned_pages, 0, 5);
		  foreach ($scanned_pages as $post) {
			  if ($post['score'] == 0) {
				  ?>
				<tr>
				  <td><?php echo $post['slug']; ?></td>
				  <td>Scanning</td>
				</tr>
				<?php
			  } else if ($post['score'] == -1) { ?>
				<tr class='limited'>
				  <td>Remove Limits</td>
				  <td>Remove Limits</td>
				</tr>				  
			  <?php
			  } else {
  ?>
				<tr>
				  <td><a href='<?php echo get_the_permalink($post['pid']); ?>' target='_blank'><?php echo $post['slug']; ?></a></td>
				  <td>
				  <?php $page_score_history = $pegasaas->get_page_score_history($post['pid']); ?>
				  
<div id="pegasaas-site-speed-chart-history-<?php echo $post['pid']; ?>" class='pegasaas-site-speed-chart-history'><canvas width="300" height="40"></canvas></div>

	<script type="text/javascript">
	var ctx = jQuery("#pegasaas-site-speed-chart-history-<?php echo $post['pid']; ?> canvas");
	var data = {
    datasets: [{
        data: [<?php
			   	$score_count = 0; 
			    foreach ($page_score_history as $year_month => $data) { 
					if ($score_count++ > 0) { print ","; } 
					if ($data['scans'] == 0) {
						echo 'NaN';
					} else {
						echo $data['score'];
					}
				}
				?>],
		borderColor: "#0073aa",
		backgroundColor: "#0073aa",
		fill: false
   }],

    // These labels appear in the legend and in the tooltips when hovering different arcs
    labels: [
<?php 
			   	$score_count = 0; 
			    foreach ($page_score_history as $year_month => $data) { 
					if ($score_count++ > 0) { print ","; } 
					echo "'".$data['display_date']."'"; 
				}

?>
    ],
};


 
var myLineChart = new Chart(ctx, {
    type: 'line',
    data: data,
    options: options
});
	</script>						  
				  
				  </td>
				</tr>
				<?php				  
			  }			  
		  }
		  ?>
		  </table>