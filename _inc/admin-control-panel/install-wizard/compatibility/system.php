<div class="panel panel-default" id="system-compatibility">
    <div class="panel-heading" role="tab" id="headingOne">
		<h4 class="panel-title">
			  <a role="button" data-toggle="collapse" data-parent="#compatibility-accordion" href="#collapse_compatiblity_environment" aria-expanded="true" aria-controls="collapseOne">
				  
				  System <span class='status-icon pull-right'><i class='fa fa-spin fa-spinner'></i></span>
			</a>
		</h4>
	</div>
	<div id="collapse_compatiblity_environment" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
    	<div class="panel-body">
			<ul>
				<?php $system_issues = PegasaasPluginCompatibility::get_system_issues();
					foreach ($system_issues['all'] as $issue) { ?>
					<li><?php echo $issue['title']; ?></li>
					<?php } ?>
			</ul>
		</div>
	</div>
</div>