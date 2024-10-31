<div>
<b>API Optimization Request Submit Method:</b><br/>
				<form method="post" style="display: inline-block" class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-status">
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
			
				<select class='form-control' onchange="set_visible_api_submit_method_options(this);  submit_form(this)" name="s">
				  <option value="0">Auto</option>  
				  <option value="1" <?php if ($feature_settings['status'] == "1") { ?>selected<?php } ?>>Blocking</option>
				  <option value="2" <?php if ($feature_settings['status'] == "2") { ?>selected<?php } ?>>Non-Blocking</option>
				</select>
			</form>
</div>


<div id="api-submit-method-optimization-request-timeout" <?php if ($feature_settings['status'] == 2) { ?>style='display: none;'<?php } ?>>
	<br>
<b>Optimization Request Timeout:</b><br>
<p style='text-transform: none;'>This is the maximum duration of the optimization submission requests to the API.</p>
<form method="post" style="display: inline-block" class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-attribute">
					<input type="hidden" name="a" value="optimization_request_timeout">

					<input type="hidden" name="f" value="<?php echo $feature; ?>">
						
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="2.5" <?php if ($feature_settings['optimization_request_timeout'] == "2.5") { ?>selected<?php } ?>>2.5 seconds</option>  
				  <option value="5" <?php if ($feature_settings['optimization_request_timeout'] == "5") { ?>selected<?php } ?>>5 seconds</option>
				  <option value="10" <?php if ($feature_settings['optimization_request_timeout'] == "10" || $feature_settings['optimization_request_timeout'] == "") { ?>selected<?php } ?>>10 seconds (default)</option>
				  <option value="15" <?php if ($feature_settings['optimization_request_timeout'] == "15") { ?>selected<?php } ?>>15 seconds</option>
				  <option value="20" <?php if ($feature_settings['optimization_request_timeout'] == "20") { ?>selected<?php } ?>>20 seconds</option>
				</select>
			</form>			
					</div>
					
<div id="api-submit-method-non-blocking-window" <?php if ($feature_settings['status'] > 0) { ?>style='display: none;'<?php } ?>>
	<br>
<b>Non-Blocking Window:</b><br>
	<p style='text-transform: none;'>This is the period that the system shifts to the fallback non-blocking requests, when encountering a communication issue with the API.</p>
<form method="post" style="display: inline-block" class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-attribute">
					<input type="hidden" name="a" value="non_blocking_window">

					<input type="hidden" name="f" value="<?php echo $feature; ?>">
						
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="5" <?php if ($feature_settings['non_blocking_window'] == "5") { ?>selected<?php } ?>>5 minutes</option>
				  <option value="10" <?php if ($feature_settings['non_blocking_window'] == "10"  || $feature_settings['general_request_timeout'] == "") { ?>selected<?php } ?>>10 minutes (default)</option>
				  <option value="15" <?php if ($feature_settings['non_blocking_window'] == "15") { ?>selected<?php } ?>>15 minutes</option>
				  <option value="20" <?php if ($feature_settings['non_blocking_window'] == "20") { ?>selected<?php } ?>>20 minutes</option>
				  <option value="30" <?php if ($feature_settings['non_blocking_window'] == "30") { ?>selected<?php } ?>>30 minutes</option>
				  <option value="45" <?php if ($feature_settings['non_blocking_window'] == "45") { ?>selected<?php } ?>>45 minutes</option>
				  <option value="60" <?php if ($feature_settings['non_blocking_window'] == "60") { ?>selected<?php } ?>>60 minutes</option>
				</select>
			</form>			
					</div>	

	<div>
	<br>
<b>General API Request Timeout:</b><br>
<p style='text-transform: none;'>This is the maximum duration of general API request or data fetches.</p>
<form method="post" style="display: inline-block" class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-attribute">
					<input type="hidden" name="a" value="general_request_timeout">

					<input type="hidden" name="f" value="<?php echo $feature; ?>">
						
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="10" <?php if ($feature_settings['general_request_timeout'] == "10") { ?>selected<?php } ?>>10 seconds</option>
				  <option value="20" <?php if ($feature_settings['general_request_timeout'] == "20" || $feature_settings['general_request_timeout'] == "") { ?>selected<?php } ?>>20 seconds (default)</option>
				  <option value="30" <?php if ($feature_settings['general_request_timeout'] == "30") { ?>selected<?php } ?>>30 seconds</option>
				  <option value="45" <?php if ($feature_settings['general_request_timeout'] == "45") { ?>selected<?php } ?>>45 seconds</option>
				</select>
			</form>			
					</div>


<script>
					function set_visible_api_submit_method_options(select) {
						var value = jQuery(select).val();
						if (value == 0) {
							jQuery("#api-submit-method-optimization-request-timeout").css("display", "block");
							jQuery("#api-submit-method-non-blocking-window").css("display", "block");
						
						} else if (value == 1) {
							jQuery("#api-submit-method-optimization-request-timeout").css("display", "block");
							jQuery("#api-submit-method-non-blocking-window").css("display", "none");
							
							
						} else if (value == 2) {
							jQuery("#api-submit-method-optimization-request-timeout").css("display", "none");
							jQuery("#api-submit-method-non-blocking-window").css("display", "none");
							
							
						}
					}
					</script>