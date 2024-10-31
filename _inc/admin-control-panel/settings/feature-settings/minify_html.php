
					<div class='hidden-novice'>
				<b>Strip Footer Comments:</b>
				<p style='text-transform: none'><?php echo $feature_settings['meta']['strip_footer_comments']['description']; ?></p>	
					<br/>
					<form method="post" style="display: inline-block" class='feature-setting-change'>
				<input type="hidden" name="c" value="change-feature-attribute">
				<input type="hidden" name="a" value="strip_footer_comments">

				<input type="hidden" name="f" value="<?php echo $feature; ?>">
					
						
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="0">No</option>  
				  <option value="1" <?php if ($feature_settings['strip_footer_comments'] == "1") { ?>selected<?php } ?>>Yes</option>
				</select>
			</form>					
					
				
						</div>
