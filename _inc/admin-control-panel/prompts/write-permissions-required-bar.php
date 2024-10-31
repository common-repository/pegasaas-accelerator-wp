<div class='pegasaas-warning-top'>
	<i class='<?php echo PEGASAAS_MEMORY_WARNING_ICON_CLASS; ?>'></i>
	It appears as though the there are resources <i class='fa fa-info-circle' data-toggle='popover' 
										  data-html='true' 
										  data-placement='bottom'
						   				  data-trigger='click'
										  title='Insufficient Permissions' 
										  data-content='The following files need to have their permissions set to allow writing:<br><?php 
		if (!$pegasaas->is_htaccess_writable()) { ?><h3>.htaccess</h3><p>Pegasaas needs to write to this file each time a setting that requires .htaccess rule changes is enabled/disabled, or if the plugin itself is disabled/enabled.</p><?php }
		if (!$pegasaas->is_cache_writable()) { ?><h3>wp-content/pegsaas-cache/</h3>p>Pegasaas automatically creates a folder that it uses for caching called "pegasaas-cache" in the "wp-content" folder.  Please ensure that either the "wp-content" folder is writable, or that you have created a folder called "pegasaas-cache" within the "wp-content" folder with write permissions.</p><?php }
		if (!$pegasaas->is_log_writable()) { ?><h3><?php echo str_replace($pegasaas->get_home_url(), "", PEGASAAS_ACCELERATOR_URL); ?>log.txt</h3><p>In order to troubleshoot any issues, the log.txt file should be writable.</p><?php }
		?>'></i>, which we need to write to, that we do not have access to. These issues must
	be resolved before Pegasaas can operate. <a rel="noopener noreferrer" target="_blank" href='https://codex.wordpress.org/Changing_File_Permissions'>click here to learn about setting permissions</a>
</div>
