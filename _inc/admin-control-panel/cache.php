<div class='local-cache-assets'>

  <!-- Nav tabs -->
  <ul class="nav nav-pills" role="tablist">
    <li role="presentation" class="active"><a href="#assets-images" aria-controls="images" role="tab" data-toggle="tab">Images</a></li>
    <li role="presentation"><a href="#assets-stylesheets" aria-controls="stylesheets" role="tab" data-toggle="tab">Stylesheets</a></li>
    <li role="presentation"><a href="#assets-scripts" aria-controls="scripts" role="tab" data-toggle="tab">Scripts</a></li>
    <!--<li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Other</a></li>-->
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="assets-images">
		<table class='table table-bordered table-striped'>
		<tr class='header-row'>
		  <th>File</th>
		  <th class='text-center'>Original <i class='fa fa-question-circle' title='Original file size in Kilobytes' rel='tooltip'></i></th>
		  <th class='text-center'>Compressed <i class='fa fa-question-circle' title='Compressed file size in Kilobytes' rel='tooltip'></i></th>
		  <th class='text-center'>Savings <i class='fa fa-question-circle' title='Percentage file size and download time saved' rel='tooltip'></i></th>
		  <th class='text-center'>Status</th>
		  <th>&nbsp;</th>
		</tr>
	<?php  		
	$assets = $pegasaas->db->get_results("pegasaas_static_asset", array("asset_type" => "image"), "original_file_name ASC");
	foreach ($assets as $asset) {
			
			?>
<tr rel='asset-<?php echo $asset->asset_id; ?>'>
  <td><?php echo $asset->original_file_name; ?></td>
  <td class='text-center'><?php if ($asset->status == 404) { echo "N/A"; } else { 
				?><a target='_blank' href='<?php echo $pegasaas->get_home_url().$asset->original_file_name; ?>'><?php
		echo number_format($asset->original_file_size/1024, 2, '.', ',')."KB"; ?></a><?php } ?></td>
  <td class='optimized-file-size text-center'><?php if ($asset->status == 404) { echo "N/A"; } else { 
?><a target='_blank' href='<?php echo $pegasaas->get_home_url().$asset->optimized_file_name; ?>'><?php
		echo number_format($asset->optimized_file_size/1024, 2, '.', ',')."KB"; ?></a><?php } ?></td>
    <td class='compressed-savings text-center'><?php if ($asset->status == 404) { echo "0%"; } else { 
		if ($asset->original_file_size == 0) {
			$percentage = 0;
		} else {
			$percentage = (1-$asset->optimized_file_size/$asset->original_file_size) * 100;
		}
		echo number_format($percentage, 0, '.', ',')."%";  } ?>
		</td>
  <td class='optimized-file-status text-center'><?php
	if ($asset->status == 400) { print "Could Not Optimize - Server Error"; }
	else if ($asset->status == 304) { print "Could Not Optimize"; }
	else if ($asset->status == 309) { print "Already Optimized"; }
	else if ($asset->status == 310) { print "Exceeds Maximum Size <i class='fa fa-question-circle' rel='tooltip' title='The original filesize exceeds the maximum allowable file size for optimizations for your plan.'></i>"; }
	else if ($asset->status == 311) { print "Optimization Not Performed  <i class='fa fa-question-circle' rel='tooltip' title='The optimization was not performed due to there not being any more monthly image optimization credits.'></i>"; }
	else if ($asset->status == 200) { print "Optimized"; }
	else { print "Unknown"; }
						?>		 
								 
								 
								 </td>
  <td class='text-right'>
	<button class='reoptimize-resource btn btn-primary btn-sm' data-asset-id='<?php echo $asset->asset_id; ?>'>Re-Optimize</button>
	<button class='delete-resource btn btn-danger btn-sm' data-asset-id='<?php echo $asset->asset_id; ?>'>Delete</button>
	
	</td>
	</tr>	
			<tr class='non-visible'><td></td></tr>
	<?php
	} 
?>
</table>	
	  
	</div>
    <div role="tabpanel" class="tab-pane" id="assets-stylesheets">
		<table class='table table-bordered table-striped'>
		<tr class='header-row'>
		  <th>File</th>
		  <th class='text-center'>Original <i class='fa fa-question-circle' title='Original file size in Kilobytes' rel='tooltip'></i></th>
		  <th class='text-center'>Compressed <i class='fa fa-question-circle' title='Compressed file size in Kilobytes' rel='tooltip'></i></th>
		  <th class='text-center'>Savings <i class='fa fa-question-circle' title='Percentage file size and download time saved' rel='tooltip'></i></th>
		  <th class='text-center'>Status</th>
		  <th>&nbsp;</th>
		</tr>	<?php  		
	$assets = $pegasaas->db->get_results("pegasaas_static_asset", array("asset_type" => "stylesheet"), "original_file_name ASC");
	foreach ($assets as $asset) { 
		//	/var_dump($asset);
			?>
<tr rel='asset-<?php echo $asset->asset_id; ?>'>
  <td><?php echo $asset->original_file_name; ?></td>
  <td class='text-center'><?php if ($asset->status == 404) { echo "N/A"; } else { 
				?><a target='_blank' href='<?php echo $pegasaas->get_home_url().$asset->original_file_name; ?>'><?php
		echo number_format($asset->original_file_size/1024, 2, '.', ',')."KB"; ?></a><?php } ?></td>
  <td class='optimized-file-size text-center'><?php if ($asset->status == 404) { echo "N/A"; } else { 
?><a target='_blank' href='<?php echo $pegasaas->get_home_url().$asset->optimized_file_name; ?>'><?php
		echo number_format($asset->optimized_file_size/1024, 2, '.', ',')."KB"; ?></a><?php } ?></td>
    <td class='compressed-savings text-center'><?php if ($asset->status == 404) { echo "0%"; } else { 
		if ($asset->original_file_size == 0) {
			$percentage = 0;
		} else {
			$percentage = (1-$asset->optimized_file_size/$asset->original_file_size) * 100;
		}
		echo number_format($percentage, 0, '.', ',')."%";  
		
		
	
			} ?>
		</td>
	
	<td class='optimized-file-status text-center'><?php
	if ($asset->status == 400) { print "Could Not Optimize - Server Error"; }
	else if ($asset->status == 304) { print "Could Not Optimize"; }
	else if ($asset->status == 309) { print "Already Optimized"; }
	else if ($asset->status == 200) { print "Optimized"; }
	else { print "Unknown"; }
						?>		 
								 
								 
								 </td>
  <td class='text-right'>
	<button class='reoptimize-resource btn btn-primary btn-sm' data-asset-id='<?php echo $asset->asset_id; ?>'>Re-Optimize</button>
	<button class='delete-resource btn btn-danger btn-sm' data-asset-id='<?php echo $asset->asset_id; ?>'>Delete</button>
	
	</td>
	</tr>
						<tr class='non-visible'><td></td></tr>

	<?php
	} 
?>
</table>	
	  	  
	  
	  
	  
	</div>
    <div role="tabpanel" class="tab-pane" id="assets-scripts">
		<table class='table table-bordered table-striped'>
		<tr class='header-row'>
		  <th>File</th>
		  <th class='text-center'>Original <i class='fa fa-question-circle' title='Original file size in Kilobytes' rel='tooltip'></i></th>
		  <th class='text-center'>Compressed <i class='fa fa-question-circle' title='Compressed file size in Kilobytes' rel='tooltip'></i></th>
		  <th class='text-center'>Savings <i class='fa fa-question-circle' title='Percentage file size and download time saved' rel='tooltip'></i></th>
		  <th class='text-center'>Status</th>
		  <th>&nbsp;</th>
		</tr>	<?php  		
	$assets = $pegasaas->db->get_results("pegasaas_static_asset", array("asset_type" => "script"), "original_file_name ASC");
	foreach ($assets as $asset) { 
			
			?>
<tr rel='asset-<?php echo $asset->asset_id; ?>'>
  <td><?php echo $asset->original_file_name; ?></td>
  <td class='text-center'><?php if ($asset->status == 404) { echo "N/A"; } else { 
				?><a target='_blank' href='<?php echo $pegasaas->get_home_url().$asset->original_file_name; ?>'><?php
		echo number_format($asset->original_file_size/1024, 2, '.', ',')."KB"; ?></a><?php } ?></td>
  <td class='optimized-file-size text-center'><?php if ($asset->status == 404) { echo "N/A"; } else { 
?><a target='_blank' href='<?php echo $pegasaas->get_home_url().$asset->optimized_file_name; ?>'><?php
		echo number_format($asset->optimized_file_size/1024, 2, '.', ',')."KB"; ?></a><?php } ?></td>
    <td class='compressed-savings text-center'><?php if ($asset->status == 404) { echo "0%"; } else { 
		if ($asset->original_file_size == 0) {
			print "?";
		} else {
		echo number_format((1-$asset->optimized_file_size/$asset->original_file_size) * 100, 0, '.', ',')."%";  
		}
			}?>
		</td>

	<td class='optimized-file-status text-center'><?php
	if ($asset->status == 400) { print "Could Not Optimize - Server Error"; }
	else if ($asset->status == 304) { print "Could Not Optimize"; }
	else if ($asset->status == 309) { print "Already Optimized"; }
	else if ($asset->status == 200) { print "Optimized"; }
	else if ($asset->status == 404) { print "File Not Found"; }
	else { print "Unknown"; }
						?>		 
								 
								 
								 </td>
	
  <td class='text-right'>
	<button class='reoptimize-resource btn btn-primary btn-sm' data-asset-id='<?php echo $asset->asset_id; ?>'>Re-Optimize</button>
	<button class='delete-resource btn btn-danger btn-sm' data-asset-id='<?php echo $asset->asset_id; ?>'>Delete</button>
	
	</td>
	</tr>
						<tr class='non-visible'><td></td></tr>

	<?php
	} 
?>
</table>	
	  	  
	</div>

  </div>

</div>



<script>
jQuery(".reoptimize-resource").bind("click", function() {
	var asset_id = jQuery(this).attr("data-asset-id");
	jQuery(this).attr("disabled", true);
	jQuery(this).html("Optimizing <i class='fa fa-spin fa-spinner'></i>");
		jQuery(this).parents("tr").find("td.optimized-file-status").html("<i class='fa fa-spin fa-spinner'></i>");

		jQuery.post(ajaxurl,
					{ 'action': 'pegasaas_reoptimize_local_static_asset', 'api_key': jQuery("#pegasaas-api-key").val(), 'asset_id': asset_id},
					function(data) {
						
						
						console.log(data);
						jQuery("tr[rel=asset-"+asset_id+"] button.reoptimize-resource").html("Re-Optimize");
						jQuery("tr[rel=asset-"+asset_id+"] button.reoptimize-resource").removeAttr("disabled");

	
						if (data['status'] == 1) {
								var optimized_file_size = data['optimized_file_size']/1024;
								var optimized_file_savings = 100 * (1 - data['optimized_file_size']/data['original_file_size']);
								
								jQuery("tr[rel=asset-"+asset_id+"] td.optimized-file-size a").html(optimized_file_size.toFixed(2) + "KB");
								jQuery("tr[rel=asset-"+asset_id+"] td.compressed-savings").html(optimized_file_savings.toFixed(0) + "%");
								
								if (data['asset_status'] == "400") { 
									jQuery("tr[rel=asset-"+asset_id+"] td.optimized-file-status").html("Could Not Optimize - Server Error");
								} else if (data['asset_status'] == "304") {
									jQuery("tr[rel=asset-"+asset_id+"] td.optimized-file-status").html("Could Not Optimize");
								
								} else if (data['asset_status'] == "309") {
									jQuery("tr[rel=asset-"+asset_id+"] td.optimized-file-status").html("Already Optimized");
								} else if (data['asset_status'] == "310") {
									jQuery("tr[rel=asset-"+asset_id+"] td.optimized-file-status").html("Exceeds Maximum Size");
								} else if (data['asset_status'] == "311") {
									jQuery("tr[rel=asset-"+asset_id+"] td.optimized-file-status").html("Optimization Not Performed");

								} else if (data['asset_status'] == "200") {
									jQuery("tr[rel=asset-"+asset_id+"] td.optimized-file-status").html("Optimized");

								} else {
									
								}
							
						} else {
							
							
						}
						
						
					}, "json");
});
jQuery(".delete-resource").bind("click", function() {
	var asset_id = jQuery(this).attr("data-asset-id");
	jQuery("tr[rel=asset-"+asset_id+"]").remove();
	
		jQuery.post(ajaxurl,
					{ 'action': 'pegasaas_delete_local_static_asset', 'api_key': jQuery("#pegasaas-api-key").val(), 'asset_id': asset_id},
					function(data) {
						
						asset_id = data['asset_id'];
						
	
						if (data['status'] == 1) {
							jQuery("tr[rel="+asset_id+"]").remove();
							
							
						} else {
							
							
						}
						
						
					}, "json");
});	
</script>