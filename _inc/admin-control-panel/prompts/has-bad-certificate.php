<div class='row'>
<div class='hold-on-prompt'>
	<h1 class='text-center'>Uh oh.</h1>
	<p class='text-center'>We've detected that there may be something wrong with your HTTPS security certificate.</p>
	<p class='text-center'>Please see the reports from <a rel='noopener noreferer' href='https://www.sslshopper.com/ssl-checker.html#hostname=<?php echo $pegasaas->utils->get_http_host(); ?>'>SSL Shopper</a> or
	<a target='_blank' rel='noopener noreferer' href='https://www.ssllabs.com/ssltest/analyze.html?d=<?php echo $pegasaas->utils->get_http_host(); ?>&hideResults=on'>SSL Labs</a>  </p>
	<p class='text-center' >Once you have resolved the issue with your SSL certificate, you can reload this interface, or if you believe your certificate is fine you may ignore this warning.</p>
	
	<p class='text-center'><a class='btn btn-danger' href='?page=pegasaas-accelerator&ignore-ssl-warning'>Ignore Warning</a> <a class='btn btn-success' href='?page=pegasaas-accelerator&recheck-ssl'>Reload and Re-Check Certificate</a></p>

	</div>

	</div></div>