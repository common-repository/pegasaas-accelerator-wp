<style>
	#wpcontent {
		background-color: #232c30;
	}
</style>
<div class='row'>
<div class='hold-on-prompt'>
	<h1 class='text-center'>Hold on, Boss!</h1>
	<p class='text-center'>It appears as though the Wordfence Web Application Firewall is active and not yet set up to allow our API to communicate with the plugin.</p>
	<p class='text-center'>
		Please ensure that the following IP addresses are in place within the Wordfence
		"<a href='admin.php?page=WordfenceWAF&subpage=waf_options&source=dashboard' target='_blank'>Firewall Options</a>" -> "Advanced Firewall Options" -> "Whitelisted IP Addresses that bypass all rules" area. 
	</p>
	<h4 id="pegasaas-ip-addresses" class='text-center'>Pegasaas IP Addresses</h4>
	<p class='text-center'>
	<textarea class='form-control' style='color: #333; width: 100%; height: 50px;'><?php print implode(",", $pegasaas->api->get_api_ips()); ?></textarea>
	<a href='?page=pegasaas-accelerator&fetch-api-list'>Refresh API IP List</a>
	</p>
	<p class='text-center'>Remember to click the "Save Changes" button in the Wordfence interface.</p>	
	    <div class='text-center' style='margin-top: 30px;'>
			
	<a target="_blank" class='btn btn-primary' href='admin.php?page=WordfenceWAF&subpage=waf_options&source=dashboard'>Open Wordfence</a> 
	<a class='btn btn-success' href='admin.php?page=<?php echo $_GET['page'];?>'>Okay, I've done that.  Let's try this again!</a>
	</div>

	</div></div> 