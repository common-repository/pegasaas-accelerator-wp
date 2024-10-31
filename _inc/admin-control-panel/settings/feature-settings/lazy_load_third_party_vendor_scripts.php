<div class='panel-inner-body'>
		
			<div class="list-group">
					<?php foreach ($lazy_load_third_party_vendor_scripts as $vendor) { 
		
						$feature_settings = $features["$vendor"];
															//	var_dump($feature_settings);
							$is_recent_feature = false;
							$is_new_feature = false;
						?> 
					
						  <div class="list-group-item  feature-container 
									  <?php if (isset($features['premium']["$vendor"])) { ?>premium-feature <?php } ?>
									  <?php if (in_array($vendor, $locked_for_novice )) { ?>locked-for-novice <?php } ?> 
									  <?php if (($feature_settings['status'] == "0" || $feature_settings['status'] == "" || $is_premium_feature)) { print "feature-disabled"; } ?> <?php if ($is_new_feature) { ?>new-feature<?php } else if ($is_recent_feature) { ?>recent-feature<?php } ?> ">
							  <span class='pegasaas-subsystem-title-icon hidden-xs'>
								<i id="pegasaas-setting-icon-<?php echo $vendor; ?>" class='<?php echo $icons["{$vendor}"]; ?>'><?php if (isset($icons_inner["{$vendor}"])) { print $icons_inner["{$vendor}"]; } ?></i>
								</span>
							  <?php 
					$display_name = str_replace("Lazy Load", "", $feature_settings['name']);
					echo $display_name; ?>
						
				
				<form method="post" class='feature-switch pull-right' target="hidden-frame">
					<input type='hidden' name='prompt' value='clear_cache' />
					<input type='hidden' name='f' value='<?php echo $vendor; ?>' />

					
					<?php 

				if ($feature_settings['status'] == "0" || $feature_settings['status'] == "") { 
					?><input type='hidden' name='c' value='enable-feature' />
					<input  type='checkbox' class='js-switch js-switch-small' />
					<?php 
				} else { 
					?><input type='hidden' name='c' value='disable-feature' />
					  <input type='checkbox' class='js-switch js-switch-small' checked />
					<?php 

				} ?>
				</form>	
			
			
						  </div>
					
					<?php } ?>
					</div>		
					
		</div>
				