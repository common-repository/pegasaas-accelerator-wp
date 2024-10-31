<style>
	#wpcontent {
		background-color: #232c30;
	}
</style>
<div class='row'>
<div class='hold-on-prompt'>
	<h1 class='text-center'>Hold on, Boss!</h1>
	<p class='text-center'>It appears as though the Wordfence Web Application Firewall is active but has the "whitelist" feature disabled for Pegasaas Accelerator.  This setting needs to be enabled to allow the Pegasaas API to communicate with the plugin.</p>
<center>
	<form style='display: block;' class='text-center' method="post">
			<input type='hidden' name='c' value='enable-wordfence-whitelisting'>
			<button class='btn btn-success'><?php _e("Okay, whitelist Pegasaas in WordFence for me", "pegasaas-accelerator"); ?></button>
	</form>
	 </center>   

	</div></div> 