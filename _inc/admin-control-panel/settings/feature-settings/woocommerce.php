<?php if ($pegasaas->is_pro()) { ?>

<ul> 		
					  <li <?php if ($feature_settings['product_tags_accelerated'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='woocommerce_product_tags_accelerated' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['product_tags_accelerated'] == 1) { print "checked"; } ?> />
						</form>
						  &nbsp; Product Tags Accelerated
					  </li>
		
					  <li <?php if ($feature_settings['product_categories_accelerated'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='woocommerce_product_categories_accelerated' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['product_categories_accelerated'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; Product Categories Accelerated
					  </li>										
					</ul>
<?php } ?>