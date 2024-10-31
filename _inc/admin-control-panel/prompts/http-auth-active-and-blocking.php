<style>
	#wpcontent {
		background-color: #232c30;
	}
</style>
<div class='row'>
<div  class='hold-on-prompt'>
	<h1 class='text-center'><?php _e("Hold on, Boss!", "pegasaas-accelerator"); ?></h1>
	
	<p class='text-center'>
		<?php if ($_POST['c'] == "re-check-http-auth") {
		_e("It appears as though the website <b>still</b> has \"Basic HTTP Authentication\" active. ", "pegasaas-accelerator");
		_e("  Please confirm that the required instructions are in the .htaccess file and try again. ", "pegasaas-accelerator");
	    _e("If this problem persists, please <a href='?page=pegasaas-accelerator&skip=to-support'>contact the Pegasaas support team</a>.", "pegasaas-accelerator");
		} else { 
		_e("It appears as though the site has \"Basic HTTP Authentication\" active which will not allow our API to communicate with the plugin to deliver optimized content.", "pegasaas-accelerator");
		}
		?>
		</p>
	

	<div id="http-auth-instructions" style='display: none;'>
		<h4 id="pegasaas-ip-addresses" class='text-center'  style='margin-top: 30px;'><?php _e("Manually Add Instructions To Control File", "pegasaas-accelerator"); ?></h4>
			<?php 
		
			if ($pegasaas->utils->http_auth_blocking['global'] == 1) {
				$htaccess_contents = $pegasaas->get_htaccess_contents();
				
				if (stristr($htaccess_contents, "require valid-user")) {
				  print "<p>";
					_e("Please ensure that the following lines are added to the .htaccess in your main web folder, immediately after the line 'Require valid-user'.", "pegasaas-accelerator");
					print "</p>";
				} else { 
					// if the file doesn't contain 'require valid-user' then the http authentication instructions may be in a folder outside of the document root,
					// so instruct the user to put the instructions into the primary .htaccess file
				  	print "<p>";
				    _e("Please ensure that the following lines are added to the .htaccess in your main web folder, near the top of the file.", "pegasaas-accelerator");
					print "</p>";
				}
			} else {
				$htaccess_contents = $pegasaas->get_htaccess_contents("wp-admin/");
				if (stristr($htaccess_contents, "require valid-user")) {
				  	print "<p>";
					_e("Please ensure that the following lines are added to the .htaccess in your /wp-admin/ folder, immediately after the line 'Require valid-user'.", "pegasaas-accelerator");
					print "</p>";
				} else { 
					
					
					// if the file doesn't contain 'require valid-user' then the http authentication instructions may be in a folder outside of the document root,
					// so instruct the user to put the instructions into the primary .htaccess file
				  	print "<p>";
					_e("Please ensure that the following lines are added to the .htaccess in your /wp-admin/ folder, near the top of the file.", "pegasaas-accelerator");
					print "</p>";
					
					if ($htaccess_contents == "") {
						print "<p>";
					   _e("If no .htaccess file exists within the /wp-admin/ folder, you may need to create one.");
						print "</p>";
					}
				
				}			
			}
			?>
			
		<p class='text-center'>
			<textarea class='form-control' style='color: #333; width: 100%; height: 120px; font-size: 10px;'><?php
				echo $pegasaas->utils->get_http_auth_special_instructions(); ?>
			</textarea>
		</p>
		
		<button type='button' onclick='hide_http_auth_instructions()' class='btn btn-default'><?php _e("Uh, Maybe Not.", "pegasaas-accelerator"); ?></button>
		<form method="post" action="admin.php?page=pegasaas-accelerator" style='float: right'>
	<input type='hidden' name='c' value='re-check-http-auth' />
    <button type='submit' class='btn btn-primary pull-right'><?php _e("Okay, I've done that.  Let's see if this works!", "pegasaas-accelerator"); ?></button>
		</form>
	</div>
	<?php 
	global $pegasaas_form_error;
	if ($pegasaas_form_error != "") { ?>
	<div class='alert alert-danger text-center'><?php echo $pegasaas_form_error; ?></div>
	<?php } ?>

	<div class='text-center' id='http-auth-buttons' style='margin-top: 30px;'>
	<p class='text-center' style='margin-bottom: 30px; '>
		<?php _e("We can attempt to add special instructions to your .htaccess control file for you, or if you prefer, you can add the instructions yourself.", "pegasaas-accelerator"); ?>
	</p>		
	<p class='text-center' style='margin-bottom: 40px; font-weight: bold;'>
		<?php _e("Please note that PageSpeed scans (performed by external Google service) will unavailable while the site is protected with a password.", "pegasaas-accelerator"); ?>
	</p>		
		<button type='button' onclick='show_http_auth_instructions()' class='btn btn-default'><?php _e("Thanks, I'll Add Them Manually", "pegasaas-accelerator"); ?></button>
	 
	
			
		<form style='display: inline;' class='pull-right' method="post">
			<input type='hidden' name='c' value='add-http-auth-instructions'>
			<button class='btn btn-success'><?php _e("Yes Please, Do This For Me", "pegasaas-accelerator"); ?></button>
		</form>
	</div>

	</div></div>
<script>
function show_http_auth_instructions() {
	jQuery("#http-auth-instructions").css("display", "block");
	jQuery("#http-auth-buttons").css("display", "none");
}
	
	function hide_http_auth_instructions() {
	jQuery("#http-auth-instructions").css("display", "none");
	jQuery("#http-auth-buttons").css("display", "block");
}
</script>