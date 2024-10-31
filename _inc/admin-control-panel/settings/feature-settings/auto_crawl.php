<br><div>
<b>Auto Crawl Frequency:</b><br/>
<p style='text-transform:none'>Here you can set how often you want the auto-crawl to occur.</p>
			<form method="post" style="display: inline-block" class='feature-setting-change'>
				<input type="hidden" name="c" value="change-feature-attribute">
				<input type="hidden" name="a" value="frequency">
				<input type="hidden" name="f" value="<?php echo $feature; ?>">
			
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="five_minutes">Every 5 Minutes (Default)</option>  
				  <option value="ten_minutes" <?php if ($feature_settings['frequency'] == "ten_minutes") { ?>selected<?php } ?>>Every 10 Minutes</option>
				  <option value="fifteen_minutes" <?php if ($feature_settings['frequency'] == "fifteen_minutes") { ?>selected<?php } ?>>Every 15 Minutes</option>
				  <option value="thirty_minutes" <?php if ($feature_settings['frequency'] == "thirty_minutes") { ?>selected<?php } ?>>Every 30 Minutes</option>
				</select>
			</form>
</div>

<br>
<div>
<b>Max Execution Time:</b><br>
<p style='text-transform: none;'>This is the maximum script execution time allowed when the auto-crawl is executing.  If your server allows for higher CPU usage, you can set this to a higher number.  However, if your
server is sensitive to CPU loads over 15 seconds, you can also lower this number.</p>
<form method="post" style="display: inline-block" class='feature-setting-change'>
				<input type="hidden" name="c" value="change-feature-attribute">
				<input type="hidden" name="a" value="max_execution_time">
				<input type="hidden" name="f" value="<?php echo $feature; ?>">
			
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="3">3 seconds</option>  
				  <option value="5" <?php if ($feature_settings['max_execution_time'] == "5") { ?>selected<?php } ?>>5 seconds</option>
				  <option value="10" <?php if ($feature_settings['max_execution_time'] == "10") { ?>selected<?php } ?>>10 seconds</option>
				  <option value="15" <?php if ($feature_settings['max_execution_time'] == "15") { ?>selected<?php } ?>>15 seconds</option>
				  <option value="30" <?php if ($feature_settings['max_execution_time'] == "30") { ?>selected<?php } ?>>30 seconds</option>
				</select>
			</form>
</div>
<br>			
<div>
<b>Max Pages per Crawl:</b><br>
<p style='text-transform: none;'>This is the maximum number of pages that the auto-crawl routine will crawl, per invocation.  If you're worried about overwhelming your web server, you can lower 
this value from the default.</p>
<form method="post" style="display: inline-block" class='feature-setting-change'>
				<input type="hidden" name="c" value="change-feature-attribute">
				<input type="hidden" name="a" value="max_pages_to_crawl_per_invocation">
				<input type="hidden" name="f" value="<?php echo $feature; ?>">
			
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="default">Default (Based Upon Server Response Rate) </option>  
				  <option value="1" <?php if ($feature_settings['max_pages_to_crawl_per_invocation'] == "1") { ?>selected<?php } ?>>1 page/crawl</option>
				  <option value="2" <?php if ($feature_settings['max_pages_to_crawl_per_invocation'] == "2") { ?>selected<?php } ?>>2 pages/crawl</option>
				  <option value="3" <?php if ($feature_settings['max_pages_to_crawl_per_invocation'] == "3") { ?>selected<?php } ?>>3 pages/crawl</option>
				  <option value="4" <?php if ($feature_settings['max_pages_to_crawl_per_invocation'] == "4") { ?>selected<?php } ?>>4 pages/crawl</option>
				  <option value="5" <?php if ($feature_settings['max_pages_to_crawl_per_invocation'] == "5") { ?>selected<?php } ?>>5 pages/crawl</option>
				  <option value="10" <?php if ($feature_settings['max_pages_to_crawl_per_invocation'] == "10") { ?>selected<?php } ?>>10 pages/crawl</option>
				  <option value="15" <?php if ($feature_settings['max_pages_to_crawl_per_invocation'] == "15") { ?>selected<?php } ?>>15 pages/crawl</option>
				  <option value="30" <?php if ($feature_settings['max_pages_to_crawl_per_invocation'] == "30") { ?>selected<?php } ?>>30 pages/crawl</option>
				</select>
			</form>
</div>