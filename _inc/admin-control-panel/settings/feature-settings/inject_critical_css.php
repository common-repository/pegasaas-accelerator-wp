<div class='hidden-novice hidden-intermediate'>
	<b>Where To Inject:</b><br/>
	<form method="post" style="display: inline-block" class='feature-setting-change'>
		<input type="hidden" name="c" value="change-feature-status">
		<input type="hidden" name="f" value="<?php echo $feature; ?>">
		<?php if (in_array($feature, $requires_cache_clearing )) { ?>
		<input type='hidden' name='prompt' value='clear_cache' />
		<?php } ?>							
	<select class='form-control' onchange="submit_form(this)" name="s">
		<option value="1">Default</option>
		<option value="2" <?php if ($feature_settings['status'] == "2") { ?>selected<?php } ?>>Before Closing &lt;/head&gt; Tag </option>
		<option value="3" <?php if ($feature_settings['status'] == "3") { ?>selected<?php } ?>>Before First &lt;link&gt; Tag (default) </option>
	</select>
</form>
<br/>
<br/>
			
			
			
<h4>Global Critical CSS Template</h4>
<p style='text-transform: none'>If you find that the global critical css that is used on non-premium optimized extended-coverage pages is not accurate, you can explicitly specify a different page to use as the "template".</p>
<?php
	$post_types = $pegasaas->utils->get_post_types();

	foreach ($post_types as $post_type) { 
		$post_type_object = get_post_type_object($post_type); 
					
		$args = array(
			'posts_per_page'	=> 10000,
			'offset'			=> 0,
			'category'			=> '',
			'category_name'		=> '',
			'orderby'			=> 'post_date',
			'order'				=> '',
			'include'			=> '',
			'exclude'			=> '',
			'meta_key'			=> '',
			'meta_value'		=> '',
			'post_type'			=> $post_type,
			'post_mime_type'	=> '',
			'post_parent'		=> '',
			'author'			=> '',
			'author_name'		=> '',
			'post_status'		=> 'publish',
			'suppress_filters'	=> true 
		);			
		?>
	<form method="post" style="display: block" class='feature-setting-change'>
		<input type="hidden" name="c" value="change-local-setting">
		<input type="hidden" name="f" value="inject_critical_css_global_cpcss_template_override__<?php echo $post_type; ?>">
		<b><?php echo $post_type_object->labels->name; ?></b>
		<select class='form-control' onchange="submit_form(this)" name="s">
			<option value="" selected="selected" >Default</option>
			<?php
						
			
			
			// get list of pages and posts
			$posts_array    = PegasaasUtils::get_posts($args);
			$selected_value = PegasaasAccelerator::$settings["settings"]["inject_critical_css"]["global_cpcss_template_override"]["$post_type"];		
					
			foreach ($posts_array as  $post_object) { 
				print "<option ";
				if ($selected_value == $post_object->ID) { print "selected "; }
				print " value='{$post_object->ID}'>{$post_object->post_title}</option>";	
			}

			?>
		</select>
	</form>
<br/>
<?php


				}
				
			
				
				
		
			?>
			


						</div>