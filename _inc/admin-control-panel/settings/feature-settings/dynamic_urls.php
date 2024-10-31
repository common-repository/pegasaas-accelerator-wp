<?php $default_args = array("utm_source", "utm_medium", "utm_campaign", "utm_term", "utm_content", "gclid", "keyword"); ?>

<b>Ignore When Any of the Following Are Present:</b>
<ul style='text-transform: none'>
	<?php foreach ($default_args as $arg) { ?>		
	<li <?php if ($feature_settings["{$arg}"] != 1) { print "class='feature-disabled'"; } ?>>
		<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
			<input type="hidden" name="c" value="toggle-local-setting">
			<input type='hidden' name='f' value='dynamic_urls_<?php echo $arg; ?>' />
			<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings["{$arg}"] == 1) { print "checked"; } ?> />
		</form> 
		 &nbsp; <?php echo $arg; ?>
	</li>
	<?php } ?>
					
					</ul>
	
<br/><b>Custom Parameters To Ignore:</b><br/>
	<small style='text-transform: none; font-weight: normal; '>				Example: https://<?php echo $pegasaas->utils->get_http_host(); ?>/?<b>your_key</b>=value_2<br/></small>
										<form method="post" style="display: block"  class='feature-setting-change'>
				<input type="hidden" name="c" value="change-local-setting">
					<input type="hidden" name="f" value="dynamic_urls_additional_args">
											<?php if (in_array($feature, $requires_cache_clearing )) { ?>
					<input type='hidden' name='prompt' value='clear_cache' />
					<?php } ?>
						<textarea placeholder="your_key
another_key
something_else" class='form-control form-control-full' name='s'><?php echo $feature_settings['additional_args']; ?></textarea>
					<input type='button' class='btn btn-success' onclick='submit_form(this)' value='Save' />
			</form>		