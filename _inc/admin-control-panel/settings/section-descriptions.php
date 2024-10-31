<p class='section-description'>
<?php
switch ($feature_title) {
		case "Server Response Time":
		  	_e("The first target when optimizing the performance of your website is the response time of your server.  If your server response time is too slow, your visitors will bounce.  Enabling Page Caching, Browser Caching, and using a CDN can help to speed up the response time of your web server.", "pegasaas-accelerator");
			break;
		case "General":
			_e("Choose how you want the dashboard to display by configuring the graphic display mode, and the amount of detail shown in the interfaces which suites your experience level.", "pegasaas-accelerator");
			break;
		case "HTML Resource Optimization":
			_e("While the entire web page is transformed through the optimization of HTML, CSS, and Javascript, once those changes are completed, these settings are applied.", "pegasaas-accelerator");
			break;
		case "Image Resource Optimization":
			_e("Once server response time has been optimized, then the images resources in your site should be optimized.", "pegasaas-accelerator");
			break;
		case "CSS Resource Optimization":
			_e("To make your web page load fast, your CSS files should be deferred and critical CSS injected directly into your web page.", "pegasaas-accelerator");
			break;
		case "Javascript Resource Optimization":
			_e("To make your web page load fast, your Javascript files should be deferred, preloaded, and minified.", "pegasaas-accelerator");
			break;
		case "Misc Resource Delivery":
			_e("Fonts are one of the biggest culprits in creating a slow page load.  The settings in this section target the loading font resource so that your page renders as fast as possible.", "pegasaas-accelerator");
			break;
		case "Misc Resource Delivery":
			_e("After the other resources are optimized, ", "pegasaas-accelerator");
			break;
		case "Lazy Loading":
			_e("Lazy loading can be used to reduce the initial page load time dramatically.  Images, IFRAMES, and YouTube resources can take seconds to load.  By deferring their loading, your page's fully loaded time can be significantly improved. ", "pegasaas-accelerator");
			break;
		case "Compatibility":
			_e("If your website leverages third-party technology that require some compatibility settings, those settings are managed here.", "pegasaas-accelerator");
			break;
		
}
?>
</p>