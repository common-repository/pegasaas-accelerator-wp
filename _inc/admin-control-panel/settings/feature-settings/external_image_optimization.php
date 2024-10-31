
					<div class='hidden-novice'>
					<b>Optimization Level:</b><br/>
				<form method="post" style="display: inline-block" class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-attribute">
					<input type="hidden" name="a" value="quality">
					<input type="hidden" name="f" value="<?php echo $feature; ?>">

					<input type='hidden' name='prompt' value='clear_image_cache' />				
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="1">Default</option>
				  <option value="100" <?php if ($feature_settings['quality'] == "100") { ?>selected<?php } ?>>Best Quality</option>
				  <option value="80"  <?php if ($feature_settings['quality'] == "80")  { ?>selected<?php } ?>>Mid Quality</option>
				  <option value="65"  <?php if ($feature_settings['quality'] == "65")  { ?>selected<?php } ?>>Economy Mode</option>
				  <option value="55"  <?php if ($feature_settings['quality'] == "55")  { ?>selected<?php } ?>>Low Quality (default)</option>				

				</select>
			</form>  
					</div>
					<?php if ($feature == "image_optimization") { ?>
					<div class='hidden-novice'>
					<br/><b>Retain Exif Metadata:</b>
					<br/>
				<form method="post" style="display: inline-block"  class='feature-setting-change'>
					<input type="hidden" name="c" value="change-feature-attribute">
					<input type="hidden" name="a" value="retain_exif">
					<input type='hidden' name='prompt' value='clear_image_cache' />
					<input type="hidden" name="f" value="<?php echo $feature; ?>">
				
				<select class='form-control' onchange="submit_form(this)" name="s">
				  <option value="0">No</option>
				  <option value="1" <?php if ($feature_settings['retain_exif'] == "1") { ?>selected<?php } ?>>Yes</option>
				</select>
			</form>  
					</div>
					<?php } ?>
					
<div class='hidden-novice'>
<br/><b>Exclude Images From Being Optimized:</b><br/>
<form method="post" style="display: block" class='feature-setting-change'>
	<input type="hidden" name="c" value="change-local-setting">
	<input type="hidden" name="f" value="image_optimization_exclude_images">
<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>	
	<textarea placeholder="/path/to/image.jpg" class='form-control form-control-full' name='s'><?php echo $feature_settings['exclude_images']; ?></textarea>
	<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
</form>						
</div>					
		