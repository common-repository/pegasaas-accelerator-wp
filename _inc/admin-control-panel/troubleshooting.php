<h3>Troubleshooting</h3>
<?php $troubleshooting_articles = $pegasaas->interface->get_troubleshooting_articles(); ?>
<div class="panel-group accordion" id="troubleshooting-accordion" role="tablist" aria-multiselectable="true">
<?php 
	$count = 0;
	foreach ($troubleshooting_articles as $article) { 
	$count++;
	?>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingOne">
      <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#troubleshooting-accordion" href="#collapse_troubleshooting_<?php echo $count; ?>" aria-expanded="true" aria-controls="collapseOne">
          <i class='fa fa-angle-right'></i> <?php echo $article['title']; ?>
        </a>
      </h4>
    </div>
    <div id="collapse_troubleshooting_<?php echo $count; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
        <?php echo $article['content']; ?>
      </div>
    </div>
  </div>
<?php } ?>
  
</div>