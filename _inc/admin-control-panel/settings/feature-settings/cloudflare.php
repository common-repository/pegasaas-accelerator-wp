<?php if (!$pegasaas->cache->cloudflare_credentials_valid() && $feature_settings['api_key'] != "" && $feature_settings['account_email'] != "") { ?><p class='pegasaas-warning pegasaas-warning-settings-box'>It appears as though either the Account Email or API Key is invaild.</p> <?php } ?>
				<p style='text-transform: none'>** Please note that the API Token feature is currently in BETA at Cloudflare and may not be fully functional.  </p>
				<form method="post" class='feature-setting-change form-inline <?php if (!$pegasaas->cache->cloudflare_credentials_valid()) { print "has-error"; } ?>' style="display: block; margin-bottom: 5px; ">
					<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="cloudflare_authorization_type">					
					
						<h5>Authorization Type
							<i class='fa fa-info-circle' title='Choose the type of Cloudflare API key you are using.' rel='tooltip'></i>
				
					</h5>				
					<div class="input-group">
						<span class="input-group-addon" title='This is the type of API Access Being Granted' rel='tooltip'><i class="fa fa-handshake-o fa-fw"></i></span>

						<select name='s' class="form-control" onchange='set_visible_cloudflare_fields(this); submit_form(this)'>
						  <option value='0'>Global API Key</option>
						  <option value='1' <?php if ($feature_settings['authorization_type'] == '1') { ?>selected<?php } ?>>API Token</option>
						</select>
					</div>
					<!--<button type="button" onclick="submit_form(this)" class="btn btn-default">Save</button>-->
				</form> 
					<script>
					function set_visible_cloudflare_fields(select) {
						var value = jQuery(select).val();
						if (value == 0) {
							jQuery("#cloudflare-account-email-form").css("display", "block");
							jQuery("#cloudflare-global-api-key-title").css("display", "block");
							jQuery("#cloudflare-api-token-title").css("display", "none");
						} else if (value == 1) {
							jQuery("#cloudflare-account-email-form").css("display", "none");
							jQuery("#cloudflare-global-api-key-title").css("display", "none");
							jQuery("#cloudflare-api-token-title").css("display", "block");
							
						}
					}
					</script>
				<form id="cloudflare-account-email-form" method="post" class='feature-setting-change form-inline <?php if (!$pegasaas->cache->cloudflare_credentials_valid()) { print "has-error"; } ?>' style="display: <?php if ($feature_settings['authorization_type'] == '1') { print "none"; } else { print "block"; } ?>; margin-bottom: 5px; ">
					<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="cloudflare_account_email">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>					
					<h5>Cloudflare Account Email
							<i class='fa fa-info-circle' title='This is the email that is used by your Cloudflare account -- it is required in order to authenticate a "Global API Key".' rel='tooltip'></i>
				
					</h5>
					
					<div class="input-group">
						<span class="input-group-addon" title='Your Cloudflare Account Email Address' rel='tooltip'><i class="fa fa-envelope fa-fw"></i></span>
						<input name='s' class="form-control" type="text" placeholder="Account Email" value="<?php echo $feature_settings['account_email']; ?>">
					</div>
					<button type="button" onclick="submit_form(this)" class="btn btn-default">Save</button>
				</form> 				

					
				<form method="post" class='feature-setting-change form-inline <?php if (!$pegasaas->cache->cloudflare_credentials_valid()) { print "has-error"; } ?>' style="display: block">
					<h5 id='cloudflare-api-token-title' style="display: <?php if ($feature_settings['authorization_type'] == '0') { print "none"; } else { print "block"; } ?>;">
						API Token 
						<i class='fa fa-info-circle' title='An "API Token" can be created by going to your Cloudflare account profile page, and select he "API Tokens" tab, and then creating a new "API Token" within the "API Tokens" section.' rel='tooltip'></i>
					</h5>
					<h5 id='cloudflare-global-api-key-title'  style="display: <?php if ($feature_settings['authorization_type'] == '1') { print "none"; } else { print "block"; } ?>;">
						Global API Key
						<i class='fa fa-info-circle' title='Your global API key is found by going to your Cloudflare account profile page, and select he "API Tokens" tab.  The Global API key will be listed under the "API Keys" section.' rel='tooltip'></i>
					</h5>
					<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="cloudflare_api_key">
					<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>							
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
						<input name='s' class="form-control" type="text" placeholder="API Key" value="<?php echo $feature_settings['api_key']; ?>">
					</div>
					<button type="button" onclick="submit_form(this)" class="btn btn-default">Save</button>
				</form>  
				<form method="post" class='feature-setting-change form-inline <?php if (!$pegasaas->cache->cloudflare_credentials_valid() || !$pegasaas->cache->cloudflare_zone_id_valid()) { print "has-error"; } ?>' style="display: block; margin-bottom: 5px; ">
					<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="cloudflare_zone_id">
				
					
					<h5 for='cloudflare-zone-id'>Zone ID <i class='fa fa-info-circle' title='This is a 32 character alpha-numeric string found on the bottom right section of your Cloudlfare zone overview page' rel='tooltip'></i> </i></h5>
					
					
					<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-bullseye fa-fw"></i></span>
					<input id='cloudflare-zone-id' name='s' class="form-control" type="text" placeholder="Zone ID" value="<?php echo $feature_settings['zone_id']; ?>">
					</div>
					<button type="button" onclick="submit_form(this)" class="btn btn-default">Save</button>
				</form> 		