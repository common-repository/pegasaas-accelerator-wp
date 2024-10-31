<?php $ips = $feature_settings["ips"]; ?>
<b>Detected Server IP Addresses:</b>

<ul style='text-transform: none'>
	<?php foreach ($ips as $ip) { ?>		
	<li>
		<form method="post" class='pull-left background-submit'>
			<button class='btn btn-xs btn-danger'>Delete</button>
			<input type="hidden" name="c" value="remove-item-complex-setting">
			<input type='hidden' name='f' value='multi_server_ip' />
			<input type='hidden' name='ip' value='<?php echo $ip; ?>' />
		</form> 
		 &nbsp; <?php echo $ip; ?>
	</li>
	<?php } ?>
</ul>
			