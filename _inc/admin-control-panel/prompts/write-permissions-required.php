<style>
	#wpcontent {
		background-color: #232c30;
	}
</style>
<div class='row'>
<div  style='max-width: 450px; margin: auto;' class='write-permissions-required'>
	<h1 class='text-center' style='margin-top: 100px;'>Hold on, Sparky!</h1>
	<p class='text-center'>It appears as though there are resources, which we need to write to, that we do not have access to. These issues must
	be resolved before Pegasaas can operate.</p>
	<?php if (!$pegasaas->is_htaccess_writable()) { ?>
	<div>
	  <h3>.htaccess</h3>
		<p>Please ensure the .htaccess file, found in the website root directory, is writable (at least until installation is complete).</p>
		<p>Pegasaas needs to write to this file each time a setting that requires .htaccess rule changes is enabled/disabled, or if the plugin itself is disabled/enabled.</p>
		<p>If you do not know how to change the permissions on the .htaccess file, <a rel="noopener noreferrer" target="_blank" href='https://pegasaas.com/knowledge-base/htaccess-file-is-not-writable/'>click here</a>.</p>
	</div>
	<?php } ?>
	<?php if (!$pegasaas->is_cache_writable()) { ?>
	<div>
	  <h3>wp-content/pegsaas-cache/</h3>
		<p>Pegasaas automatically creates a folder that it uses for caching called "pegasaas-cache" in the "wp-content" folder.  Please ensure that either the "wp-content" folder is writable, or that you have
			created a folder called "pegasaas-cache" within the "wp-content" folder with write permissions.</p>
	</div>
	<?php } ?>
	<?php if (!$pegasaas->is_log_writable()) { ?>
	<div>
	  <h3><?php echo str_replace($pegasaas->get_home_url(), "", PEGASAAS_ACCELERATOR_URL); ?>log.txt</h3>
		<p>In order to troubleshoot any issues, the log.txt file should be writable.</p>
	</div>
	<?php } ?>	
	
	    <div class='text-center' style='margin-top: 30px;'>
	<a class='btn btn-primary' href='admin.php?page=pegasaas-accelerator'>Ok, done.  Lets try this again!</a>
	</div>

	</div></div>