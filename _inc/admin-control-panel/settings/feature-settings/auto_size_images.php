			<!--	
<b>Exclude Pages:</b><br/>
										<form method="post" style="display: block">
				<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="auto_size_images_exclude_pages">
						<textarea placeholder="/path/to/exclude/" class='form-control form-control-full' name='s'><?php echo $feature_settings['exclude_pages']; ?></textarea>
					<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
			</form>
<br/>
-->
<b>Exclude Specific Images:</b><br/>
										<form method="post" style="display: block">
				<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="auto_size_images_exclude_images">
						<textarea placeholder="/path/to/image-file.jpg" class='form-control form-control-full' name='s'><?php echo $feature_settings['exclude_images']; ?></textarea>
					<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
			</form>						
