<?php $progress = $pegasaas->interface->get_progress("benchmark"); ?>
<div class='speed-info-box' id="pegasaas-pending-benchmark-scans-chart" data-scans-in-progress='<?php echo $progress['pending_scans']; ?>' data-base='<?php echo $progress['base']; ?>'>
	<h3>Benchmark Scans <i class='fa fa-question-circle-o pending-benchmark-scans-chart-tooltip'></i></h3>
	<div class="progress">
  		<div class="progress-bar  <?php if ($progress['percent'] == 100) { print "progress-bar-success"; } else { print "progress-bar-pulse active"; } ?>" role="progressbar" aria-valuenow="<?php echo $progress['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $progress['percent']; ?>%;">
    		<?php if ($progress['percent'] == 100) { ?>
			100%
			<?php } else { ?>
			<?php echo $progress['percent']; ?>%
			<?php } ?>
  		</div>
	</div> 
	<span class='morphext-container text-center'><span class="js-rotating"><?php if ($progress['percent'] < 100) { ?><?php echo $progress['percent']; ?>% Complete,<?php echo $progress['pending_scans']; ?> Scans Queued,<?php echo $progress['estimated_time']; ?><?php } ?></span></span>
</div>