<div class='hidden-novice'>
<br/><b>Exclude Foreground Images From Being Lazy Loaded:</b><br/>
<form method="post" style="display: block" class='feature-setting-change'>
	<input type="hidden" name="c" value="change-local-setting">
	<input type="hidden" name="f" value="lazy_load_images_exclude_images">
<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>	
	<textarea placeholder="/path/to/image.jpg" class='form-control form-control-full' name='s'><?php echo $feature_settings['exclude_images']; ?></textarea>
	<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
</form>						
					</div>