<style>
	#wpcontent {
		background-color: #232c30;
	}
</style>
<div class='row'>
<div class='hold-on-prompt'>
	<h1 class='text-center'>Hold on, Boss!</h1>
	<p class='text-center'>There appears to be <?php 
		if (sizeof($active_conflicting_plugins) == 1) { 
		?>a plugin<?php 
		} else if (sizeof($active_conflicting_plugins) == 2) { 
		?>a couple of plugins<?php 
		} else if (sizeof($active_conflicting_plugins) > 3) { 
		?>a few plugins<?php
		}?>
		that, <i><a target="_blank" href='https://pegasaas.com/knowledge-base/are-there-plugins-that-need-to-be-disabled-in-order-for-pegasaas-accelerator-to-operate'>contain<?php if (sizeof($active_conflicting_plugins) == 1) { print "s"; } ?> functionality that conflicts with critical components of Pegasaas Accelerator</a>,</i> that <b>must</b> be deactivated before we can continue. </p>
		<p><b>Please ensure that the following plugin<?php if (sizeof($active_conflicting_plugins) == 1) {?> is<?php } else { ?>s are<?php } ?> deactivated:</b></p>
		<ul class='conflicting-plugin-list fa-ul'>
		  <?php foreach ($active_conflicting_plugins as $plugin_file => $plugin_data) { ?>
			<li><i class='fa-li fa fa-caret-right'></i><?php echo $plugin_data['Name']; ?></li>
		  <?php } ?>
	    </ul>
	    <div class='text-center' style='margin-top: 30px;'>
	<a class='btn btn-primary' href='plugins.php'>Manage Plugins</a>
	</div>

	</div></div>