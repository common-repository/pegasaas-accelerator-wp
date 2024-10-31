<h3>FAQs</h3>

<?php 
$faqs = $pegasaas->interface->get_faqs();

?>
<div class="panel-group accordion" id="faq-accordion" role="tablist" aria-multiselectable="true">
<?php 
	$count = 0;
	foreach ($faqs as $article) { 
	$count++; ?>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingOne">
      <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#faq-accordion" href="#collapse_faq_<?php echo $count; ?>" aria-expanded="true" aria-controls="collapseOne">
          <i class='fa fa-angle-right'></i> <?php echo $article['title']; ?>
        </a>
      </h4>
    </div>
    <div id="collapse_faq_<?php echo $count; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
        <?php echo $article['content']; ?>
      </div>
    </div>
  </div>
<?php } ?>
  
</div>