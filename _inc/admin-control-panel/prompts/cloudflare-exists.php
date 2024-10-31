<div class='row'>
<div  style='max-width: 450px; margin: auto;'>
	<h1 class='text-center' style='margin-top: 100px;'>Cloudflare Detected</h1>
	<p class='text-center'>Right on, it looks like your website is leveraging the Cloudflare network.</p>
	<p class='text-center'>To allow Pegasaas Accelerator to automatically clear related page and resource caches when you update content, 
		you will need to specify your Cloudflare settings below before continuing.</p>
	<p>Your API key is found by:</p>
	<ul>
	  <li><a href='https://dash.cloudflare.com/login' target="_blank">logging in to your cloudflare account</a></li>
	  <li>Choosing your site from those listed in your "Home" panel</li>
	  <li>Scroll down and select from the API panel, on the right of the Cloudflare page, "Get your API Key"</li>
	  <li>Scroll down, and VIEW your "Global API Key".</li>
	  <li>Copy the API key that is provided, into the API Key field below.</li>
	  <li>Click the "Save" button below.</li>

	</ul></p>
				<form method="post" class='form-inline <?php if (!$pegasaas->cache->cloudflare_credentials_valid()) { print "has-error"; } ?>' style="display: block; margin-bottom: 5px; ">
					<input type="hidden" name="c" value="set-cloudflare-credentials">
					
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-envelope fa-fw"></i></span>
						<input name='cloudflare_account_email' class="form-control" type="text" placeholder="Account Email" value="<?php echo PegasaasAccelerator::$settings['settings']['cloudflare']['account_email']; ?>">
					</div>
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
						<input name='cloudflare_api_key' class="form-control" type="text" placeholder="API Key" value="<?php echo  PegasaasAccelerator::$settings['settings']['cloudflare']['api_key']; ?>">
					</div>
					<p class='text-center' style='margin-top: 10px;'>
					<button type="submit" class="btn btn-success">Save</button></p>
				</form> 				
<p>	If the interface reloads without proceeding to the next step, then the API key or Account Email is incorrect.   If you are certain that the Account Email and 
	API key are correct, you may <a href='?page=pegasaas-accelerator&skip-cloudflare=1'>skip this step</a> however you may end up with pages that do not Acceler</p>
	
	

	</div>

	</div></div> 