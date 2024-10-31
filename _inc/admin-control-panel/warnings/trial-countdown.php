<div class='pegasaas-warning-top <?php if ($days_remaining > 10) { print "trial-grey"; } else if ($days_remaining > 5) { print "trial-orange"; } ?>'>
	<span class='material-icons' style='vertical-align: bottom'>timer</span>
	
	Your trial has <span class='days-remaining'><?php echo $days_remaining ?></span> days remaining <i class='fa fa-info-circle' data-toggle='popover' 
										  data-html='true' 
										  data-placement='bottom'
						   				  data-trigger='click'
										  title='Trial Period' 
										  data-content='During your trial period, enjoy our full optimization suite.  At the end of the trial, if you choose to not upgrade to a subscription plan, your website will revert to its original unoptimized speed.'></i>.  If you're excited about what a faster website can do for your ranking, conversion rate, and customer satisfaction, <a rel="noopener noreferrer" class='btn btn-default btn-xs' target="_blank" href='<?php echo PegasaasInterface::get_upgrade_link(); ?>'><?php echo __("Upgrade", "pegasaas-accelerator"); ?></a> to
	a full plan today.
</div>
