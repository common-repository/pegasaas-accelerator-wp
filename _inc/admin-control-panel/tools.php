<div style=' margin: auto;'>

<div class='pegasaas-feature-box pegasaas-feature-section-container' style='clear: both;'>
						<div class='pegasaas-subsystem-title'>Bulk Accelerate Pages</div>
					<div class='row'>
						
					
					<p class='section-description'>You can use these options to re-accelerate all post of a particular post types at once.</p>
						
				<?php $post_types = $pegasaas->utils->get_post_types(); ?>
			<?php foreach ($post_types as $post_type) { 
						$post_type_object = get_post_type_object($post_type); 
						$count = wp_count_posts($post_type);
						$total_posts = $count->publish;
	
						$total_accelerated_posts = $pegasaas->utils->get_accelerated_posts_count($post_type);
						
						?>
						<div class='col-md-6 col-lg-4'>
				<div class='pegasaas-feature-box tools-box'>
						<div class='pegasaas-subsystem-title'>
							<?php echo $post_type_object->labels->name; ?>
							<span id="accelerated-post-type-count-<?php echo $post_type; ?>" class='badge badge-default' data-total='<?php echo $total_posts; ?>'><?php echo $total_accelerated_posts; ?>/<?php echo $total_posts; ?></span>
							
							<form method="post" target='hidden-frame' class='pull-right bulk-enable-post-type '>
							    <input type='hidden' name='c' value='de-accelerate-post-type'>
								<input type='hidden' name='pt' value='<?php echo $post_type; ?>' />
								<button id="bulk-disable-post-type-<?php echo $post_type; ?>" type='submit' class='btn btn-danger' <?php if ($total_accelerated_posts == 0) { ?>disabled<?php } ?>>Disable All</button>
							</form>
						
							
							
							<form method="post" target='hidden-frame' class='pull-right bulk-enable-post-type'>
							    <input type='hidden' name='c' value='re-accelerate-post-type'>
								<input type='hidden' name='pt' value='<?php echo $post_type; ?>' />
								<button id="bulk-enable-post-type-<?php echo $post_type; ?>" type='submit' class='btn btn-success' <?php if ($total_posts <= $total_accelerated_posts) { ?>disabled<?php } ?>>Enable All</button>
							</form>
							
							
						</div>
						</div>
						</div>
			<?php } ?>
	</div>
	</div>
	
	<script>
	jQuery(".bulk-enable-post-type").submit(function() {
		jQuery(this).find("button").attr("disabled", true);
	});
		
		function set_accelerate_buttons(post_type, state) {
			var count = jQuery("#accelerated-post-type-count-" + post_type).attr("data-total");
			if (state == "enabled") {
				jQuery("#accelerated-post-type-count-" + post_type).html(count + "/" + count);
				jQuery("#bulk-disable-post-type-" + post_type).removeAttr("disabled");
			} else if (state == "disabled") {
				jQuery("#accelerated-post-type-count-" + post_type).html("0/" + count);
				jQuery("#bulk-enable-post-type-" + post_type).removeAttr("disabled");
			}
			
		}
	</script>
</div>