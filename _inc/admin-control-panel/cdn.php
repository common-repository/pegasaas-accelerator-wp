
<div class='cdn-cache-assets'>
 <h3 class='text-center'>How The CDN Cache and Optimization System Works</h3>
	<style>

	</style>
	
	<div class='row'>
	  <div class='col end-user-outer'>
		 <div class='end-user'>
			 <div class='text-center'>
				 <i class="material-icons">computer</i>
		     	 <h4>Internet User</h4>
			 </div>
	  	<p>Your visitor's web browser has a temporary cache.</p>
	  	<p>If the browser does not have a cached copy, then it requests a copy from the CDN network.</p>

		 </div>
	  </div>
	  <div class='col cdn-edge-network-outer'>
		 <div class='cdn-edge-network'>
		    <div class='text-center'>
				 <i class="material-icons">cloud</i>
		     	 <h4>CDN Edge Servers</h4>
			 </div>
					<p>The Content Delivery Network edge servers each have their own copy of your optimized resources, such as .png, .css, and .js files.</p>
		     <p>Because the network is global, optimized files can be quickly transferred to your website visitors, no matter where they're browsing from.</p>
		  			<div class='text-center'>
			 <div class="btn-group">
			  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Clear CDN Cache <span class='status'></span><span class="caret"></span></button>
				
			  <ul class="dropdown-menu">
				<li><a href="#" class='cdn-purge-everything'>Purge Everything</a></li>
				<li><a href="#" class='cdn-purge-images'>Clear Images Only</a></li>
				<li><a href="#" class='cdn-purge-js'>Clear Javascript Only</a></li>
				<li><a href="#" class='cdn-purge-css'>Clear CSS Only</a></li>
				<li role="separator" class="divider"></li>
				<li><a href="#" data-toggle="modal" data-target="#clear-cdn-file-modal">Clear Specific Files</a></li>				  
			  </ul>
			</div>		
		  </div>
	  </div>
		</div>
		
	  <div class='col reverse-proxy-outer'>
		 <div class='reverse-proxy'>
		    <div class='text-center'>
				 <i class="material-icons">layers</i>
		     	 <h4>Optimization Servers</h4>
			 </div>			 
		<p>Acting as a "reverse proxy", the <?php if (PegasaasAccelerator::$settings['settings']['white_label']['status'] != 1) { ?>Pegasaas<?php } ?> optimization servers fetch a copy of the original resource from the "origin" server
				 and then saves a copy the optimized version of your JS, CSS, and images so that the global CDN can fetch, as needed, the optimized resource.</p>
			<div class='text-center'>
			 <div class="btn-group">
			  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Purge Opt System + CDN <span class='status'></span><span class="caret"></span></button>

			  <ul class="dropdown-menu">
				<li><a href="#" class='reverse-proxy-purge-everything'>Purge Everything</a></li>
				<li><a href="#" class='reverse-proxy-purge-images'>Purge Images Only</a></li>
				<li><a href="#" class='reverse-proxy-purge-js'>Purge Javascript Only</a></li>
				<li><a href="#" class='reverse-proxy-purge-css'>Purge CSS Only</a></li>
				<li role="separator" class="divider"></li>
				<li><a href="#" data-toggle="modal" data-target="#clear-revproxy-file-modal">Purge Specific Files</a></li>				  
			  </ul>
			</div>		 
		 
		</div>
		 </div>
	  </div>
	  <div class='col'>
		 <div class='origin-server'>
	    	<div class='text-center'>
				 <i class="material-icons">storefront</i>
		     	 <h4>Origin Server</h4>
			 </div>	
			 <p>Your website acts as the source for the original version of your resources, serving JS, CSS, and images to the Optimization Servers.</p>
		 </div>
	  </div>			
		
	</div><div class='container'>
	<div class='row'>
		
  	<h3>Troubleshooting</h3>
	
<div class="panel-group accordion" id="cdn-accordion" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="cdn-troubleshooting-1-heading">
      <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#cdn-accordion" href="#cdn-troubleshooting-1" aria-expanded="true" aria-controls="cdn-troubleshooting-1">
          An image (or two) appears broken, when viewing an optimized page.
        </a>
      </h4>
    </div>
    <div id="cdn-troubleshooting-1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="cdn-troubleshooting-1-heading">
      <div class="panel-body">
		<p>This situation is most likely caused by a brief communication issue between the CDN edge server that your browser requested the files from.  It is possible that there was an issue
		with the optimization process with the image(s), so if clearing the CDN endpoints of your image does not resolve the problem, then you can clear the optimized resource from the reverse proxy and the CDN.</p>

		  <b>First:</b>
         <ol>
			<li>Try clearing just the <a href="#" data-toggle="modal" data-target="#clear-cdn-file-modal"><b>individual file(s)</b></a> from the Content Delivery Network edge servers.</li>
			<li>Clear your browser cache.</li>
			 <li>Reload the page with the broken image.</li>
		 </ol>
		  <b>If the above did not resolve the problem:</b>
		  <ol>
		    <li>Clear just the <a href="#" data-toggle="modal" data-target="#clear-revproxy-file-modal"><b>individual file(s)</b></a> from the Optimization Server system.  This will also clear those files from the CDN.</li>
			<li>Clear your browser cache.</li>
			<li>Reload the page with the broken image.</li>
			  
		  </ol>
		  <b>If the above did not resolve the problem:</b>
		  <ol>
		    <li>Initiate a support incident via the support form.</li>
			<li>Provide as much detail about the image, the URL of the page it is on, and what browser is does and does not work in.</li>
		  </ol>
      </div>
    </div>
  </div>
  
 
 <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="cdn-troubleshooting-2-heading">
      <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#cdn-accordion" href="#cdn-troubleshooting-2" aria-expanded="true" aria-controls="cdn-troubleshooting-2">
          All image appears broken, when viewing an optimized page.
        </a>
      </h4>
    </div>
    <div id="cdn-troubleshooting-2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="cdn-troubleshooting-2-heading">
      <div class="panel-body">
		  <p>This is likely caused by an issue between the optimization server and the origin server.  Possibly the origin server is preventing the reverse proxy system
		  from fetching the original copy.  But before you send off a support request, try the first two suggestions below.</p>
		  <b>First:</b>
         <ol>
			<li>Try clearing just <a href='#' class='cdn-purge-images'><b>all the images from the Content Delivery Network</b></a> edge servers.</li>
			<li>Clear your browser cache.</li>
			 <li>Reload the page with the broken image.</li>
		 </ol>
		  <b>If the above did not resolve the problem:</b>
		  <ol>
		    <li>Try clearing <a href='#' class='reverse-proxy-purge-images'><b>all the images from the Optimization Server system</b></a>.  This will also clear those files from the CDN.</li>
			<li>Clear your browser cache.</li>
			<li>Reload the page with the broken image.</li>
			  
		  </ol>
		  <b>If the above did not resolve the problem:</b>
		  <ol>
		    <li>Put the plugin into diagnostic mode.</li>
		    <li>Initiate a support incident via the support form.</li>
			<li>Provide as much detail about the image, the URL of the page it is on, and what browser is does and does not work in.</li>
		  </ol>
      </div>
    </div>
  </div>
	
<div class="panel panel-default">
    <div class="panel-heading" role="tab" id="cdn-troubleshooting-3-heading">
      <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#cdn-accordion" href="#cdn-troubleshooting-3" aria-expanded="true" aria-controls="cdn-troubleshooting-3">
          Browser console reports 404 errors for Javascript or CSS resources.
        </a>
      </h4>
    </div>
    <div id="cdn-troubleshooting-3" class="panel-collapse collapse" role="tabpanel" aria-labelledby="cdn-troubleshooting-3-heading">
      <div class="panel-body">
		<p>When the JS/CSS resources report 404, then this is likely caused by the optimization servers having a brief communication interruption when the initial optimization is performed.  If it is one or two resources, then we recommend 
		  trying to clear the CDN of those resources first.  If it is all resources, then skip to step #2, to clear the optimization system (and CDN) of all Javascript/CSS resources.</p>
		  <b>First:</b>
         <ol>
			 <li>Try clearing just <a href='#' class='cdn-purge-js'><b>all the JS</b></a> <a href='#' class='cdn-purge-css'><b>and CSS</b></a> from the Content Delivery Network edge servers.</li>
			<li>Clear your browser cache.</li>
			 <li>Reload the page with the broken image.</li>
		 </ol>
		  <b>If the above did not resolve the problem:</b>
		  <ol>
		    <li>Try clearing all the <a href='#' class='reverse-proxy-purge-js'><b>all the JS</b></a> <a href='#' class='reverse-proxy-purge-css'><b>and CSS</b></a> from the Optimization Server system.  This will also clear those files from the CDN.</li>
			<li>Clear your browser cache.</li>
			<li>Reload the page with the broken image.</li>
			  
		  </ol>
		  <b>If the above did not resolve the problem:</b>
		  <ol>
		    <li>Put the plugin into diagnostic mode.</li>
		    <li>Initiate a support incident via the support form.</li>
			<li>Provide as much detail about the image, the URL of the page it is on, and what browser is does and does not work in.</li>
		  </ol>
      </div>
    </div>
  </div>	

	
	<div class="panel panel-default">
    <div class="panel-heading" role="tab" id="cdn-troubleshooting-4-heading">
      <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#cdn-accordion" href="#cdn-troubleshooting-4" aria-expanded="true" aria-controls="cdn-troubleshooting-4">
          Browser console reports Javascript error with one of the CDN resources.
        </a>
      </h4>
    </div>
    <div id="cdn-troubleshooting-4" class="panel-collapse collapse" role="tabpanel" aria-labelledby="cdn-troubleshooting-4-heading">
      <div class="panel-body">
		<p>This means that our minification system did not handle a special situation in your code.</p>
		  <ol>
		    <li>Put the plugin into diagnostic mode.</li>
		    <li>Initiate a support incident via the support form.</li>
			<li>Provide as much detail about the image, the URL of the page it is on, and what browser is does and does not work in.</li>
		  </ol>
      </div>
    </div>
  </div>	
	
	<div class="panel panel-default">
    <div class="panel-heading" role="tab" id="cdn-troubleshooting-5-heading">
      <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#cdn-accordion" href="#cdn-troubleshooting-5" aria-expanded="true" aria-controls="cdn-troubleshooting-5">
          Browser console reports Javascript error with the Deferred JS file for your page.  
        </a>
      </h4>
    </div>
    <div id="cdn-troubleshooting-2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="cdn-troubleshooting-5-heading">
      <div class="panel-body">
		<p>This means that our API optimization system system did not handle a special situation in your Javascript code.  Note: this is not a CDN related issue.</p>
		  <ol>
		    <li>Put the plugin into diagnostic mode.</li>
		    <li>Initiate a support incident via the support form.</li>
			<li>Provide as much detail about the image, the URL of the page it is on, and what browser is does and does not work in.</li>
		  </ol>
      </div>
    </div>
  </div>	
	
	<div class="panel panel-default">
    <div class="panel-heading" role="tab" id="cdn-troubleshooting-6-heading">
      <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#cdn-accordion" href="#cdn-troubleshooting-6" aria-expanded="true" aria-controls="cdn-troubleshooting-6">
          Part of the page did not display as intended (either an animated slider, menu, or other content section).  
        </a>
      </h4>
    </div>
    <div id="cdn-troubleshooting-6" class="panel-collapse collapse" role="tabpanel" aria-labelledby="cdn-troubleshooting-6-heading">
      <div class="panel-body">
		<p>This means that our Critical CSS Generator did not capture a perfect snapshot of your page. Note: this is not a CDN related issue.</p>
 		<b>First:</b>
         <ol>
			<li>Try re-optimizing the page.</li>
			<li>Clear your browser cache.</li>
			 <li>Reload the affected page in your browser.</li>
		 </ol>
		  <b>If the above did not resolve the problem:</b>		  
		  <ol>
		    <li>Leave the plugin in live mode, if at all possible (so that we can see the issue first hand).</li>
		    <li>Initiate a support incident via the support form.</li>
			<li>Provide as much detail about the image, the URL of the page it is on, and what browser is does and does not work in.</li>
		  </ol>
      </div>
    </div>
  </div>
	
	
	
</div>
		</div>
		</div>
	
<div class="modal fade" id="clear-cdn-file-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Clear CDN Endpoints</h4>
      </div>
      <div class="modal-body">
		  <label>File Path(s)</label>
		  <textarea id="cdn_file_names" class='form-control' placeholder="Examples: (wildcards will not recurse into subfolders)
/wp-content/uploads/2016/10/my-image.jpg
/wp-content/uploads/2016/10/my-image.*
/wp-content/* 
/wp-content/uploads/2016/*"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="clear-cdn-cache-individual-files">Clear Cache</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

	
<div class="modal fade" id="clear-revproxy-file-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Purge Optimization System + CDN Endpoints</h4>
      </div>
      <div class="modal-body">
		  <label>File Path(s)</label>
		  <textarea id="reverse_proxy_file_names" class='form-control' placeholder="Examples: (wildcards will not recurse into subfolders)
/wp-content/uploads/2016/10/my-image.jpg
/wp-content/uploads/2016/10/my-image.*
/wp-content/* 
/wp-content/uploads/2016/*" ></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="reverse-proxy-purge-individual-files">Purge Cache</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</div>	
	
<script>
jQuery(document).ready(function() {
	jQuery(".reverse-proxy-purge-everything").click(function(e) {
		console.log("should clear all");
		e.preventDefault();
		var $this = this;
		jQuery('.cdn-cache-assets .reverse-proxy .caret').addClass("hidden");
		jQuery('.cdn-cache-assets .reverse-proxy .status').html("<i class='svg-icons svg-tail-spin svg-15-15' rel='tooltip' title='...'></i>");
		
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_all_resource_cache', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] == "1") {
						jQuery('.cdn-cache-assets .reverse-proxy .status').html("<i class='material-icons'>done</i>");
						jQuery('.cdn-cache-assets .reverse-proxy button.dropdown-toggle').addClass("btn-success").removeClass("btn-primary");
					} else  { 
					  	jQuery('.cdn-cache-assets .reverse-proxy button.dropdown-toggle').addClass("btn-danger").removeClass("btn-primary");

						jQuery('.cdn-cache-assets .reverse-proxy .status').html("<i class='material-icons'>close</i>");
					}
					setTimeout("clear_reverse_proxy_cache_progress()", 1500);
				}, 
				"json");	
				
	});
	
	
	jQuery(".reverse-proxy-purge-images").click(function(e) {
		e.preventDefault();
		var $this = this;
		jQuery('.cdn-cache-assets .reverse-proxy .caret').addClass("hidden");
		jQuery('.cdn-cache-assets .reverse-proxy .status').html("<i class='svg-icons svg-tail-spin svg-15-15' rel='tooltip' title='...'></i>");
		
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_image_resource_cache', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] == "1") {
						jQuery('.cdn-cache-assets .reverse-proxy .status').html("<i class='material-icons'>done</i>");
						jQuery('.cdn-cache-assets .reverse-proxy button.dropdown-toggle').addClass("btn-success").removeClass("btn-primary");
					} else  { 
					  	jQuery('.cdn-cache-assets .reverse-proxy button.dropdown-toggle').addClass("btn-danger").removeClass("btn-primary");

						jQuery('.cdn-cache-assets .reverse-proxy .status').html("<i class='material-icons'>close</i>");
					}
					setTimeout("clear_reverse_proxy_cache_progress()", 1500);
				}, 
				"json");	
				
	});
	
	jQuery(".reverse-proxy-purge-js").click(function(e) {
		e.preventDefault();
		var $this = this;
		jQuery('.cdn-cache-assets .reverse-proxy .caret').addClass("hidden");
		jQuery('.cdn-cache-assets .reverse-proxy .status').html("<i class='svg-icons svg-tail-spin svg-15-15' rel='tooltip' title='...'></i>");
		
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_js_resource_cache', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] == "1") {
						jQuery('.cdn-cache-assets .reverse-proxy .status').html("<i class='material-icons'>done</i>");
						jQuery('.cdn-cache-assets .reverse-proxy button.dropdown-toggle').addClass("btn-success").removeClass("btn-primary");
					} else  { 
					  	jQuery('.cdn-cache-assets .reverse-proxy button.dropdown-toggle').addClass("btn-danger").removeClass("btn-primary");

						jQuery('.cdn-cache-assets .reverse-proxy .status').html("<i class='material-icons'>close</i>");
					}
					setTimeout("clear_reverse_proxy_cache_progress()", 1500);
				}, 
				"json");	
				
	});	
	
	jQuery(".reverse-proxy-purge-css").click(function(e) {
		e.preventDefault();
		var $this = this;
		jQuery('.cdn-cache-assets .reverse-proxy .caret').addClass("hidden");
		jQuery('.cdn-cache-assets .reverse-proxy .status').html("<i class='svg-icons svg-tail-spin svg-15-15' rel='tooltip' title='...'></i>");
		
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_css_resource_cache', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] == "1") {
						jQuery('.cdn-cache-assets .reverse-proxy .status').html("<i class='material-icons'>done</i>");
						jQuery('.cdn-cache-assets .reverse-proxy button.dropdown-toggle').addClass("btn-success").removeClass("btn-primary");
					} else  { 
					  	jQuery('.cdn-cache-assets .reverse-proxy button.dropdown-toggle').addClass("btn-danger").removeClass("btn-primary");

						jQuery('.cdn-cache-assets .reverse-proxy .status').html("<i class='material-icons'>close</i>");
					}
					setTimeout("clear_reverse_proxy_cache_progress()", 1500);
				}, 
				"json");	
				
	});	
	
	jQuery("#reverse-proxy-purge-individual-files").click(function(e) {
		e.preventDefault();
		var $this = this;
		jQuery('#clear-revproxy-file-modal').modal('hide');
		
		jQuery('.cdn-cache-assets .reverse-proxy .caret').addClass("hidden");

		jQuery('.cdn-cache-assets .reverse-proxy .status').html("<i class='svg-icons svg-tail-spin svg-15-15' rel='tooltip' title='...'></i>");
		
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_indv_files_resource_cache', 
				  'api_key': pegasaas_ajax_object.api_key,
				  'files': jQuery("#reverse_proxy_file_names").val()}, 
				function(data) {
					if (data['status'] == "1") {
						jQuery('.cdn-cache-assets .reverse-proxy .status').html("<i class='material-icons'>done</i>");
						jQuery('.cdn-cache-assets .reverse-proxy button.dropdown-toggle').addClass("btn-success").removeClass("btn-primary");
					} else  { 
					  	jQuery('.cdn-cache-assets .reverse-proxy button.dropdown-toggle').addClass("btn-danger").removeClass("btn-primary");

						jQuery('.cdn-cache-assets .reverse-proxy .status').html("<i class='material-icons'>close</i>");
					}
					setTimeout("clear_reverse_proxy_cache_progress()", 1500);
				}, 
				"json");	
				
	});		
		
	
	
	
	
jQuery(".cdn-purge-everything").click(function(e) {
		e.preventDefault();
		var $this = this;
		jQuery('.cdn-cache-assets .cdn-edge-network .caret').addClass("hidden");
		jQuery('.cdn-cache-assets .cdn-edge-network .status').html("<i class='svg-icons svg-tail-spin svg-15-15' rel='tooltip' title='...'></i>");
		
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_all_cdn_edge_network_only', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] == "1") {
						jQuery('.cdn-cache-assets .cdn-edge-network .status').html("<i class='material-icons'>done</i>");
						jQuery('.cdn-cache-assets .cdn-edge-network button.dropdown-toggle').addClass("btn-success").removeClass("btn-primary");
					} else  { 
					  	jQuery('.cdn-cache-assets .cdn-edge-network button.dropdown-toggle').addClass("btn-danger").removeClass("btn-primary");

						jQuery('.cdn-cache-assets .cdn-edge-network .status').html("<i class='material-icons'>close</i>");
					}
					setTimeout("clear_cdn_edge_network_progress()", 1500);
				}, 
				"json");	
				
	});
	
	
	jQuery(".cdn-purge-images").click(function(e) {
		e.preventDefault();
		var $this = this;
		jQuery('.cdn-cache-assets .cdn-edge-network .caret').addClass("hidden");
		jQuery('.cdn-cache-assets .cdn-edge-network .status').html("<i class='svg-icons svg-tail-spin svg-15-15' rel='tooltip' title='...'></i>");
		
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_image_cdn_edge_network_only', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] == "1") {
						jQuery('.cdn-cache-assets .cdn-edge-network .status').html("<i class='material-icons'>done</i>");
						jQuery('.cdn-cache-assets .cdn-edge-network button.dropdown-toggle').addClass("btn-success").removeClass("btn-primary");
					} else  { 
					  	jQuery('.cdn-cache-assets .cdn-edge-network button.dropdown-toggle').addClass("btn-danger").removeClass("btn-primary");

						jQuery('.cdn-cache-assets .cdn-edge-network .status').html("<i class='material-icons'>close</i>");
					}
					setTimeout("clear_cdn_edge_network_progress()", 1500);
				}, 
				"json");	
				
	});
	
	jQuery(".cdn-purge-js").click(function(e) {
		e.preventDefault();
		var $this = this;
		jQuery('.cdn-cache-assets .cdn-edge-network .caret').addClass("hidden");
		jQuery('.cdn-cache-assets .cdn-edge-network .status').html("<i class='svg-icons svg-tail-spin svg-15-15' rel='tooltip' title='...'></i>");
		
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_js_cdn_edge_network_only', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] == "1") {
						jQuery('.cdn-cache-assets .cdn-edge-network .status').html("<i class='material-icons'>done</i>");
						jQuery('.cdn-cache-assets .cdn-edge-network button.dropdown-toggle').addClass("btn-success").removeClass("btn-primary");
					} else  { 
					  	jQuery('.cdn-cache-assets .cdn-edge-network button.dropdown-toggle').addClass("btn-danger").removeClass("btn-primary");

						jQuery('.cdn-cache-assets .cdn-edge-network .status').html("<i class='material-icons'>close</i>");
					}
					setTimeout("clear_cdn_edge_network_progress()", 1500);
				}, 
				"json");	
				
	});	
	
	jQuery(".cdn-purge-css").click(function(e) {
		e.preventDefault();
		var $this = this;
		jQuery('.cdn-cache-assets .reverse-proxy .caret').addClass("hidden");
		jQuery('.cdn-cache-assets .reverse-proxy .status').html("<i class='svg-icons svg-tail-spin svg-15-15' rel='tooltip' title='...'></i>");
		
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_css_cdn_edge_network_only', 
				  'api_key': pegasaas_ajax_object.api_key }, 
				function(data) {
					if (data['status'] == "1") {
						jQuery('.cdn-cache-assets .cdn-edge-network .status').html("<i class='material-icons'>done</i>");
						jQuery('.cdn-cache-assets .cdn-edge-network button.dropdown-toggle').addClass("btn-success").removeClass("btn-primary");
					} else  { 
					  	jQuery('.cdn-cache-assets .cdn-edge-network button.dropdown-toggle').addClass("btn-danger").removeClass("btn-primary");

						jQuery('.cdn-cache-assets .cdn-edge-network .status').html("<i class='material-icons'>close</i>");
					}
					setTimeout("clear_cdn_edge_network_progress()", 1500);
				}, 
				"json");	
				
	});	
	
	jQuery("#clear-cdn-cache-individual-files").click(function(e) {
		e.preventDefault();
		var $this = this;
		jQuery('#clear-cdn-file-modal').modal('hide');
		
		jQuery('.cdn-cache-assets .cdn-edge-network .caret').addClass("hidden");

		jQuery('.cdn-cache-assets .cdn-edge-network .status').html("<i class='svg-icons svg-tail-spin svg-15-15' rel='tooltip' title='...'></i>");
		
		jQuery.post(pegasaas_ajax_object.ajax_url, 
				{ 'action': 'pegasaas_purge_indv_files_cdn_edge_network_only', 
				  'api_key': pegasaas_ajax_object.api_key,
				  'files': jQuery("#cdn_file_names").val()}, 
				function(data) {
					if (data['status'] == "1") {
						jQuery('.cdn-cache-assets .cdn-edge-network .status').html("<i class='material-icons'>done</i>");
						jQuery('.cdn-cache-assets .cdn-edge-network button.dropdown-toggle').addClass("btn-success").removeClass("btn-primary");
					} else  { 
					  	jQuery('.cdn-cache-assets .cdn-edge-network button.dropdown-toggle').addClass("btn-danger").removeClass("btn-primary");

						jQuery('.cdn-cache-assets .cdn-edge-network .status').html("<i class='material-icons'>close</i>");
					}
					setTimeout("clear_cdn_edge_network_progress()", 1500);
				}, 
				"json");	
				
	});		
			
});

function clear_reverse_proxy_cache_progress() {
	jQuery(".cdn-cache-assets .reverse-proxy .status").html("");
	jQuery('.cdn-cache-assets .reverse-proxy .caret').removeClass("hidden");
	jQuery('.cdn-cache-assets .reverse-proxy button.dropdown-toggle').addClass("btn-primary").removeClass("btn-success").removeClass("btn-danger");

	
}
	
	function clear_cdn_edge_network_progress() {
	jQuery(".cdn-cache-assets .cdn-edge-network .status").html("");
	jQuery('.cdn-cache-assets .cdn-edge-network .caret').removeClass("hidden");
	jQuery('.cdn-cache-assets .cdn-edge-network button.dropdown-toggle').addClass("btn-primary").removeClass("btn-success").removeClass("btn-danger");

	
}
</script>
