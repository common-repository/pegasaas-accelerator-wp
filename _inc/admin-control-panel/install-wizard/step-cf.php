<div class="row setup-content" id="step-cf" style="display: none;">
		      <div class="col-md-6 col-md-offset-3">
        <div class="col-md-12">
	  <h3>Cloudflare Configuration</h3>
<p class='text-left'>It looks like your website is leveraging the Cloudflare network. To allow Pegasaas Accelerator to automatically clear related page and resource caches when you update content, 
		you will need to specify your Cloudflare settings below before continuing.  To learn where to find your Cloudflare API key, follow our tutorial <a target="_blank" href='https://pegasaas.com/where-can-you-find-your-cloudflare-api-key/'>here</a>. </p>

		
	
						<div class="input-group form-group">
							<span class="input-group-addon"><i class="fa fa-envelope fa-fw"></i></span>
							<input required name='cloudflare_account_email' class="form-control" type="email" placeholder="Cloudflare Account Email" value="<?php echo $pegasaas->settings['settings']['cloudflare']['account_email']; ?>">
						</div>
			

					<div class="input-group form-group">
						<span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
						<input required  name='cloudflare_api_key' class="form-control" type="text" placeholder="Cloudflare API Key" value="<?php echo  $pegasaas->settings['settings']['cloudflare']['api_key']; ?>">
				  	</div>
					
						
					
	
				   <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" >Next</button>
				  </div>
		</div>

</div>