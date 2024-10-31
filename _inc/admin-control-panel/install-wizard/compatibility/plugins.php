<div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingOne">
		<h4 class="panel-title">
			  <a role="button" data-toggle="collapse" data-parent="#compatibility-accordion" href="#collapse_compatiblity_plugins" aria-expanded="true" aria-controls="collapseOne">
				  <?php $plugin_issues = PegasaasPluginCompatibility::get_plugin_compatibility();
				  if (sizeof($plugin_issues['critical']) > 0) {
				  	$plugin_compatibility_icon_class = "remove";
				  } else if (sizeof($plugin_issues['warning']) > 0) {
					  $plugin_compatibility_icon_class = "warning";
				  } else {
					  $plugin_compatibility_icon_class = "check";
				  }
				  ?>
				  Plugin Compatibility <span class='status-icon pull-right'><i class='fa fa-<?php echo $plugin_compatibility_icon_class; ?>'></i></span>
			</a>
		</h4>
	</div>
	<div id="collapse_compatiblity_plugins" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
    	<div class="panel-body">
			<ul>
				<?php 
					foreach ($plugin_issues['all'] as $issue) { ?>
					<li><?php echo $issue['title']; ?></li>
					<?php } ?>
			</ul>
		</div>
	</div>
</div>