<?php if (PegasaasAccelerator::$settings['status'] > 0) { ?> 
<form class='gtmetrix-form' target="_blank" action='https://gtmetrix.com/analyze.html' method='post'>
						  <input type='url' name='url' value='<?php echo ($pegasaas->get_home_url().$post['slug']); ?>' />
					  </form>
				  <form class='pull-right rescan-pagespeed <?php if ($this_one_scanning) { print "scanning"; } ?> ' style='margin-bottom: 0px;' rel='<?php echo $post['pid']; ?>'>
				  	<input type='hidden' name='pid' value='<?php echo $post['pid']; ?>' />
				  	<input type='hidden' name='post_id' value='<?php echo $post['id']; ?>' />
				  	<input type='hidden' name='c' value='rescan-pagespeed' />
				  	
					<div class="btn-group">					
					  <button type="button" class="btn btn-primary btn-caret btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span class="fa fa-ellipsis-v"></span>
						<span class="sr-only">Toggle Dropdown</span>
					  </button>		
	<ul class="dropdown-menu">
		<li><a href='#' class='initiate-rescan-pagespeed'><i class='<?php echo PEGASAAS_RESCAN_ICON_CLASS; ?>'></i> Rescan with Pegasaas</a></li>
	
		<li><a href='https://developers.google.com/speed/pagespeed/insights/?url=<?php echo urlencode($pegasaas->get_home_url().$post['slug']); ?>' target='_blank'><i class='<?php echo PEGASAAS_GOOGLE_ICON_CLASS; ?>'></i> Scan with Google PageSpeed Insights</a></li>
		<li class='hidden-novice'><a href='#' class='scan-with-gtmetrix'><i class='<?php echo PEGASAAS_GTMETRIX_ICON_CLASS; ?>'></i> Scan with GTMetrix</a></li>

</ul>
					</div>
					  
					  	
				  	
				  </form>					  
<?php } ?>