<div class="modal fade" id="confirm-update-clear-cache" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
      <div class="modal-body">
		  <h1>Hold on!</h1>
		 
		  <p>This feature requires page optimization cache to cleared and re-built.</p>
			  <?php if ($pegasaas->is_standard() && PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining'] != "") { ?>
			  <p>You have <?php echo PegasaasAccelerator::$settings['limits']['monthly_optimizations_remaining']; ?> monthly page optimizations remaining, with <?php echo ($pages_accelerated['accelerated']+$pages_accelerated['pending']); ?> pages set as optimizable.</p>
		  		<?php } ?>
		  <p>Do you wish to <b>clear the entire cache</b> or <b>just change the setting but not clear existing optimizations</b>?</p>
		  <br/>
		  <button class='btn btn-success' id="update-setting-and-clear" data-feature=''>Update Setting and Clear All Cache</button>
		  <br/>
		  <br/>
		  <button class='btn btn-warning' id="update-setting-but-no-clear"  data-feature=''>Update Setting but Do Not Clear Cache</button>
      </div>
    </div>
  </div>
</div>