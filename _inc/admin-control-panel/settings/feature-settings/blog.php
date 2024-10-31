	<ul> 
						<?php if (get_option("show_on_front") == "posts" || get_option("show_on_front") == "layout") { ?>
					  <li <?php if ($feature_settings['home_page_accelerated'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='blog_home_page_accelerated' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['home_page_accelerated'] == 1) { print "checked"; } ?> />
						</form>
						  &nbsp; Home Page Accelerated
					  </li>
						<?php } ?>
					  <li <?php if ($feature_settings['categories_accelerated'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='blog_categories_accelerated' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['categories_accelerated'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; Categories Accelerated
					  </li>	
					  <li <?php if ($feature_settings['pagination_accelerated'] != 1) { print "class='feature-disabled'"; } ?>>
						<form method="post" class='feature-toggle-switch pull-left' target="hidden-frame">
							<input type="hidden" name="c" value="toggle-local-setting">
							<input type='hidden' name='f' value='blog_pagination_accelerated' />
							<input type='checkbox' class='js-switch js-switch-small' <?php if ($feature_settings['pagination_accelerated'] == 1) { print "checked"; } ?> />
						</form> 
						  &nbsp; Pagination Accelerated
					  </li>	                      									
					</ul>
				