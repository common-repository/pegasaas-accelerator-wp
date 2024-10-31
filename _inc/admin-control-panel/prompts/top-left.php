
<div class='top-left-corner-stats top-left-corner-stats-double'>
	<div class='top-left-corner-status-inner'>

			<div class='top-left-gauge'>
				<div class='small-gauge-title'>
				  <?php echo __("Premium Acceleration Enabled", "pegasaas-accelerator"); ?>	

				</div>
				<div class='small-gauge-value'>
					<?php echo ($pages_accelerated['accelerated']+$pages_accelerated['pending']); ?><span>/<?php echo ($pages_accelerated['accelerated'] + $pages_accelerated['pending'] + $pages_accelerated['not_accelerated']);  ?></span>
				</div>	
				
				<a rel="opopener noreferrer" id="boost-more" class="btn btn-lg btn-default"><?php echo __("Boost More", "pegasaas-accelerator"); ?></a>					

			</div>

		
			<div class='top-left-gauge'>
				<div class='small-gauge-title'>
				  <?php echo __("Premium Acceleration Applied", "pegasaas-accelerator"); ?></div>
				<div class='small-gauge-value'>
					<?php echo ($pages_accelerated['accelerated']); ?><span>/<?php echo ($pages_accelerated['accelerated'] + $pages_accelerated['pending']);  ?></span>
				</div>		
			</div>		
  		</div>						
</div>		
			
