<script>
var state = "<?php echo ($prepping ? "benchmarking" : "scanning"); ?>";		
</script>

<?php 	
	$view_mode = $_COOKIE['pegasaas_display_mode'] == "" || $_COOKIE['pegasaas_display_mode'] == "multi-mode" ? "mobile-mode" : $_COOKIE['pegasaas_display_mode']; 

  $installed_but_nothing_accelerated = $pages_accelerated['accelerated'] == 0 && $pages_accelerated['pending'] == 0; 


if ($prepping && $installed_but_nothing_accelerated) {
	//include("installed-but-nothing-accelerated.php") ;
}
?>

<div id="pegasaas-scores-wrapper" class='<?php echo $view_mode; ?> <?php if ($prepping) { ?>is-prepping<?php } ?>'>
	<div class='row' id="pegasaas-scores">
<?php if (false && $prepping) {
	if (!$installed_but_nothing_accelerated) {
		include("installed-but-nothing-accelerated.php") ;
	} 
} else {
	if (!$prepping) {
		$pegasaas->set_init_notification(0);
	} 
$pegasaas->utils->console_log("Dashboard Interface 2: 24");
	include("dashboard-display-2.php");

?>


<?php if (PegasaasAccelerator::$settings['status'] > 0) { ?>

	<?php if ($pegasaas->is_pro_edition()) { ?>

	<?php } else { ?>

	<?php if ($pegasaas->is_free() && get_option("pegasaas_accelerator_dismiss_upgrade_box", 0) == 0) { ?>
	<div id="pegasaas-upgrade-box" class='pegasaas-upgrade-box'>
		<button class='btn pull-right btn-close' onclick='dismiss_upgrade_box()'>X</button>
		<h5>Go PREMIUM with Pegasaas Accelerator WP Pro!</h5>
		<ul>
		  <li class='col-sm-12 col-md-6'><i class='fa-li fa fa-check'></i>Increased Coverage <i class='fa fa-question-circle-o' rel='tooltip' title='Get hundreds or thousands more pages optimized with our premium plans.'></i></li>
		  <li class='col-sm-12 col-md-6'><i class='fa-li fa fa-check'></i>Unlimited PageSpeed Scans <i class='fa fa-question-circle-o' rel='tooltip' title='Know which pages in your site -- your entire site --  need further attention.'></i></li>
		  <li class='col-sm-12 col-md-6'><i class='fa-li fa fa-check'></i>Page Level Configuration <i class='fa fa-question-circle-o' rel='tooltip' title='Turn features on and off, as you need, on a page-by-page basis.'></i></li>
		  <li class='col-sm-12 col-md-6'><i class='fa-li fa fa-check'></i>Premium Support <i class='fa fa-question-circle-o' rel='tooltip' title='Have a unique situation?  Our support team is standing by ready to investigate.'></i></li>
		  <li class='col-sm-12 col-md-6'><i class='fa-li fa fa-check'></i>Integrated Global CDN <i class='fa fa-question-circle-o' rel='tooltip' title='Get the benefit of an automatically integrated global content delivery network.  Have your images, css, and javascript files delivered to your global vistors faster.'></i></li>
		  <li class='col-sm-12 col-md-6'><i class='fa-li fa fa-check'></i>Advanced Image Optimization <i class='fa fa-question-circle-o' rel='tooltip' title='Larger images, auto image size detection and auto resizing.  Dramatically -- and automatically -- reduce the size of your page with our advanced image optimization features.'></i></li>
		  <li class='col-sm-12 col-md-6'><i class='fa-li fa fa-check'></i>More Monthly Image Optimizations <i class='fa fa-question-circle-o' rel='tooltip' title='Enjoy thousands more monthly image optimzations per month with the Pro plan!'></i></li>
		  <li class='col-sm-12 col-md-6'><i class='fa-li fa fa-check'></i>WebP Image Optimization <i class='fa fa-question-circle-o' rel='tooltip' title='Take advantage of next-generation image optimization with the automatic delivery of .webp encoded images for those browsers that support them.'></i></li>
		  <li class='col-sm-12 col-md-6'><i class='fa-li fa fa-plus'></i> Much More </li>
		</ul>
		
		<div class='text-center'>
		<a class='btn btn-primary' target='_blank' href='https://pegasaas.com/upgrade/?utm_source=pegasaas-accelerator-dashboard&upgrade_key=<?php echo PegasaasAccelerator::$settings['api_key']?>-<?php echo PegasaasAccelerator::$settings['installation_id']?>&site=<?php echo $pegasaas->utils->get_http_host(); ?>&current=<?php echo PegasaasAccelerator::$settings['subscription'];?>&v=3' class='btn btn-upgrade'>Learn more about what's included with with a Premium API Key</a>
		</div>
	</div>
	<?php } ?>
	
<?php } ?>
	
<?php } else { ?>
<style>
	#pegasaas-scores-wrapper { min-height: auto; } 
</style>
<?php } ?>
</div>
<?php
	//$scanned_pages = $pegasaas->scanner->get_scanned_objects("page_importance"); ?>


<script type="text/javascript">
	<?php //include PEGASAAS_ACCELERATOR_DIR."_inc/admin-control-panel/charts/options-2.php"; ?>
	<?php //include PEGASAAS_ACCELERATOR_DIR."_inc/admin-control-panel/charts/accelerated-chart-2.php"; ?>
</script>

<?php } ?>
