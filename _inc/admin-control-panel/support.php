<div style='max-width: 900px; margin: auto; padding-bottom: 50px; '>
<h3>Support</h3>
<p>Please provide a detailed account of what you are experiencing, including:</p>
<ol>
	<li>A short summary of the issue</li>
	<li>The specific URL(s) that the issue is experienced on</li>
	<li>What specifically at the URL is happening/not happening</li>
</ol>


<form id="support-form" style='margin-top: 40px;' method="post" action="https://pegasaas.com/submit-support/">
   <div class="form-group">
    <label for="subject">Your Name</label>

    <input required type="text" class="form-control" id="contact_name" name="contact_name" placeholder="Your Name" value="<?php if (!$pegasaas->is_free()) { echo PegasaasAccelerator::$settings['account']['first_name']." ".PegasaasAccelerator::$settings['account']['last_name']; } ?>">
  </div>
	
   <div class="form-group">
    <label for="subject">Email</label>
    <input required type="email" class="form-control" id="email_address" name="email_address" placeholder="Your Email Address" value="<?php echo PegasaasAccelerator::$settings['account']['email_address']; ?>">
  </div>
  <div class="form-group">
    <label for="description">URL(s)</label>
	  <textarea required style='height: 100px;' class="form-control" id="urls_with_issue" name="urls_with_issue" placeholder="Input one URL per line"></textarea>
  </div>
  <div class="form-group">
    <label for="description">Description of Problem</label>
	  <textarea required style='height: 100px;' class="form-control" id="description" name="description" 
				placeholder="What specifically, at the above URL(s), should be happening and what is happening instead."></textarea>
  </div>

  <div class="checkbox">
    <label>
      <input type="checkbox" name="agree" value="Yes"> I have read the <a href='https://pegasaas.com/support/'>documentation</a> and I agree to send diagnostic information (WordPress version, PHP version, list of enabled plugins) when I submit this form.
    </label>
  </div> 
  <input type="hidden" name="account_email_address" value="<?php echo PegasaasAccelerator::$settings['account']['email_address']; ?>" />
  <input type="hidden" name="account_contact_name" value="<?php echo PegasaasAccelerator::$settings['account']['first_name']." ".PegasaasAccelerator::$settings['account']['last_name']; ?>" />
  <input type="hidden" name="c" value="submit-support-issue" />
  <input type="hidden" name="diagnostic__api_key" value="<?php echo PegasaasAccelerator::$settings['api_key']; ?>" />  
  <input type="hidden" name="diagnostic__installation_id" value="<?php echo PegasaasAccelerator::$settings['installation_id']; ?>" />  
  <input type="hidden" name="diagnostic__wp_home_url" value="<?php echo $pegasaas->get_home_url(); ?>" />  
  <input type="hidden" name="diagnostic__wp_site_url" value="<?php echo get_site_url(); ?>" />  
  <input type="hidden" name="diagnostic__wp_version" value="<?php echo get_bloginfo("version"); ?>" />  
  <input type="hidden" name="diagnostic__php_version" value="<?php echo phpversion(); ?>" />
  <?php $theme = wp_get_theme(); ?>
  <input type="hidden" name="diagnostic__theme_name" value="<?php echo $theme->get('Name'); ?>" />
  <input type="hidden" name="diagnostic__theme_uri" value="<?php echo $theme->get('ThemeURI'); ?>" />
  <?php $plugins = get_plugins(); 
	$installed_plugins = array();
	foreach ($plugins as $path => $plugin_data) {
		?>
		<input type="hidden" name="diagnostic__installed_plugins[]" value="<?php 
		echo $plugin_data['Name']."\n"; 
		echo $path."\n"; 
		echo $plugin_data['PluginURI']."\n"; 
		echo $plugin_data['Version']."\n"; ?>" />
		<?php
	}
	?>
  
  <button type="submit" class="btn btn-default pull-right" disabled>Submit</button>
</form>

	</div>