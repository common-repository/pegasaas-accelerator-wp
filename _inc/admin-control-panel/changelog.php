

<?php 
$changelog = $pegasaas->interface->get_changelog();

?>
<div class="panel-group accordion" id="changelog-accordion" role="tablist" aria-multiselectable="true">
<?php 
	$count = 0;
	foreach ($changelog as $version_info) { 
	$count++; 
	
	?>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingOne">
      <h4 class="panel-title">
        <a role="button" data-toggle="collapse" <?php if ($version_info['version'] != $pegasaas->get_current_version()) { ?>class='collapsed'<?php } ?> data-parent="#changelog-accordion" href="#collapse_changelog_<?php echo $count; ?>" aria-expanded="true" aria-controls="collapseOne">
          <i class='fa fa-plus-circle'></i> 
		  <div class='version-block'><?php echo $version_info['version']; ?></div><?php
		?><div class='release-date-block'><?php echo date("Y-m-d", strtotime( $version_info['release_date'] )); ?> </div><?php
		?><div class='title-block'><?php echo $version_info['title']; ?>
			<?php if ($version_info['version'] == $pegasaas->get_current_version()) { ?>
			<span class='label label-success'>Currently Installed</span>
			<?php } ?>
		  </div>
        </a>
      </h4>
    </div>
    <div id="collapse_changelog_<?php echo $count; ?>" class="panel-collapse <?php if ($version_info['version'] != $pegasaas->get_current_version()) { ?>collapse<?php } ?>" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
        <?php if ($version_info['video'] != "" && false) { ?>
		  <div class='pull-right'><?php echo lazy_load_youtube(apply_filters( 'the_content', $version_info['video'])); ?></div>
		<?php } ?>
		  <?php echo $version_info['content']; ?>
      </div>
    </div>
  </div>
<?php } ?>
  
</div>
<?php
function lazy_load_youtube($buffer) {
		$matches = array();
		$pattern = "/<iframe(.*?)<\/iframe>/si";
    	preg_match_all($pattern, $buffer, $matches);
		$src_pattern = "/\s?src=['\"](.*?)\/embed\/(.*?)['\"]/si";
		$width_pattern = "/\s?width=['\"](.*?)['\"]/si";
		$height_pattern = "/\s?height=['\"](.*?)['\"]/si";
    	$changes = 0;
		
		foreach ($matches[0] as $find) {
			$match_src = array();
			$match_width = array();
			$match_height = array();
			preg_match($src_pattern, $find, $match_src);
			preg_match($width_pattern, $find, $match_width);
			preg_match($height_pattern, $find, $match_height);
			if (!strstr($match_src[1], "youtube.com")) {
				continue;
			}
			$video_id 	= $match_src[2];
			$width 		= $match_width[1];
			$height 	= $match_height[1];
			$iframe_html = $find;
			
			if (strstr($video_id, "?")) {
				$video_id = substr($video_id, 0, strpos($video_id, "?"));
			}
			if ($video_id != "") {
				$changes++;
				$youtube_replace = "<div class='pa-yt-player' data-id='{$video_id}'";
				$youtube_replace .= " style='width: {$width}px; height: {$height}px' ";
				if ($width != "") {
				  $youtube_replace .= " width='{$width}'";	
				}
				
				if ($height != "") {
				  $youtube_replace .= " height='{$height}'";	
				}
				$youtube_replace .= "></div>";
				$buffer = str_replace($find, $youtube_replace, $buffer);
			} else {
				
			}	
		}
		
		// strip out jetpack wrapper
		$matches = array();
		$pattern = '/<span class=["\']embed-youtube["\'](.*?)>(<div class=\'pa-yt-player\'(.*?)<\/div>)<\/span>/si';
		$replace = '${2}';
    	$buffer = preg_replace($pattern, $replace, $buffer);
	return $buffer;
} ?>

		
	
		<script type='text/javascript'>

   
 
</script>
