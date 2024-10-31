<div class="row setup-content" id="step-4" style="display: none;">
      <div class="col-md-6 col-md-offset-3">
        <div class="col-md-12">
			<?php
			$site_composition = $pegasaas->utils->get_site_composition();
			?>
            <h3>Site Composition</h3>
			
			<ul class='site-composition'>   
				<?php foreach ($site_composition['post_types'] as $post_type => $post_type_info) {
				if ($post_type_info['count'] > 0) { ?>
			  <li><span><?php echo $post_type_info['count']; ?></span>    
				  <?php echo str_replace("Category", "Categorie", $post_type_info['name']); ?>s</li>
			    <?php 
										 }
										 } ?> 
			</ul>
			
			
			<h3>Configuration</h3>
			

		  <p>Choose how much of your site you wish to initially accelerate.  You can always accelerate individual or groups of pages/posts after the initial setup.</p>
          <!--Radio butons-->
		  <?php $total_columns = 2;
			if ($site_composition['post_types']['post']['count'] > 0) { $total_columns++; }
			if ($site_composition['post_types']['page']['count'] > 0) { $total_columns++; }
			
			?>

		
			  <ul class='auto-acceleration-options'>
			  		<li class='selected-option'>
						<div class='row'>
						<div class='col-sm-3'>
						<input id='acceleration-type-everything'  type="radio" checked autocomplete="off" name='acceleration-type' value='all' />
						
						<label for="acceleration-type-everything">Everything</label>
						</div>
						<div class='accelerate-type-options'>
						<div class='col-sm-6'>
			  			
			  
	
			<p>Accelerate
				<select class='form-control page-count-selector' name='all-count'>
					<?php if ($site_composition['summary']['count'] > 5) { ?><option value='5'>first 5</option><?php } ?>
					<?php if ($site_composition['summary']['count'] > 10) { ?><option value='10'>first 10</option><?php } ?>
					<?php if ($site_composition['summary']['count'] > 25) { ?><option value='25'>first 25</option><?php } ?>
					<?php if ($site_composition['summary']['count'] > 50) { ?><option value='50'>first 50</option><?php } ?>
					<?php if ($site_composition['summary']['count'] > 100) { ?><option value='100'>first 100</option><?php } ?>
					<?php if ($site_composition['summary']['count'] > 250) { ?><option value='250'>first 250</option><?php } ?>
					<?php if ($site_composition['summary']['count'] > 500) { ?><option value='500'>first 500</option><?php } ?>
					<option selected value="<?php echo $site_composition['summary']['count']; ?>">all <?php echo $site_composition['summary']['count']; ?></option>
				</select> pages/posts/other</p>
			<p class='estimated-time-to-complete'>Estimated Time To Complete: <span class='est-time' rel='<?php echo $site_composition['time_per_page']; ?>'><?php 
				if ($site_composition['summary']['time_to_complete'] > 60) {
					print ($site_composition['summary']['time_to_complete']/60)." hours";
				} else {
					echo $site_composition['summary']['time_to_complete']." minutes";
				} ?></span></p>
							</div>
							<div class='col-sm-3 text-right'><button class="btn btn-success btn-lg" type="submit">Accelerate!</button></div>
						</div>
						</div>
	
			  </li>
		
		
		<?php if ($site_composition['post_types']['page']['count'] > 0) { ?>
	    <li>
				<div class='row'>
					<div class='col-sm-4'>
						<input id='acceleration-type-pages'  type="radio"  autocomplete="off" name='acceleration-type' value='page' />
						<label for="acceleration-type-pages">Just Pages</label>
					</div>
					
			  			<div class='accelerate-type-options'>
			<div class='col-sm-5'>
		
			<p>Accelerate
				<select class='form-control page-count-selector' name='page-count'>
					<?php if ($site_composition['post_types']['page']['count'] > 5) { ?><option value='5'>first 5</option><?php } ?>
					<?php if ($site_composition['post_types']['page']['count'] > 10) { ?><option value='10'>first 10</option><?php } ?>
					<?php if ($site_composition['post_types']['page']['count'] > 25) { ?><option value='25'>first 25</option><?php } ?>
					<?php if ($site_composition['post_types']['page']['count'] > 50) { ?><option value='50'>first 50</option><?php } ?>
					<?php if ($site_composition['post_types']['page']['count'] > 100) { ?><option value='100'>first 100</option><?php } ?>
					<?php if ($site_composition['post_types']['page']['count'] > 250) { ?><option value='250'>first 250</option><?php } ?>
					<?php if ($site_composition['post_types']['page']['count'] > 500) { ?><option value='500'>first 500</option><?php } ?>
					<option  selected value="<?php echo $site_composition['post_types']['page']['count']; ?>">all <?php echo $site_composition['post_types']['page']['count']; ?></option>				
				</select> pages</p>
			<p class='estimated-time-to-complete'>Estimated Time To Complete: <span class='est-time' rel='<?php echo $site_composition['time_per_page']; ?>'><?php 
				if ($site_composition['post_types']['page']['time_to_complete'] > 60) {
					print ($site_composition['post_types']['page']['time_to_complete']/60)." hours";
				} else {
					echo $site_composition['post_types']['page']['time_to_complete']." minutes";
				} ?></span></p>
							</div>
							<div class='col-sm-3 text-right'><button class="btn btn-success btn-lg" type="submit">Accelerate!</button></div>
						</div>
						</div>
							</li>
		<?php } ?>
				  
		<?php if ($site_composition['post_types']['post']['count'] > 0) { ?>
			<li>
				<div class='row'>
					<div class='col-sm-4'>
					<input id='acceleration-type-posts'  type="radio"  autocomplete="off" name='acceleration-type' value='post' />
						<label for="acceleration-type-posts">Just Posts</label>
		</div>
					
			  			<div class='accelerate-type-options'>
			<div class='col-sm-5'>
			<p>Accelerate <select class='form-control page-count-selector'  name='post-count'>
					<?php if ($site_composition['post_types']['post']['count'] > 5) { ?><option value='5'>first 5</option><?php } ?>
					<?php if ($site_composition['post_types']['post']['count'] > 10) { ?><option value='10'>first 10</option><?php } ?>
					<?php if ($site_composition['post_types']['post']['count'] > 25) { ?><option value='25'>first 25</option><?php } ?>
					<?php if ($site_composition['post_types']['post']['count'] > 50) { ?><option value='50'>first 50</option><?php } ?>
					<?php if ($site_composition['post_types']['post']['count'] > 100) { ?><option value='100'>first 100</option><?php } ?>
					<?php if ($site_composition['post_types']['post']['count'] > 250) { ?><option value='250'>first 250</option><?php } ?>
					<?php if ($site_composition['post_types']['post']['count'] > 500) { ?><option value='500'>first 500</option><?php } ?>
									<option selected value="<?php echo $site_composition['post_types']['post']['count']; ?>">all <?php echo $site_composition['post_types']['post']['count']; ?></option>

				</select> posts</p>
			<p class='estimated-time-to-complete'>Estimated Time To Complete: <span class='est-time' rel='<?php echo $site_composition['time_per_page']; ?>'><?php 
				if ($site_composition['post_types']['post']['time_to_complete'] > 60) {
					print ($site_composition['post_types']['post']['time_to_complete']/60)." hours";
				} else {
					echo $site_composition['post_types']['post']['time_to_complete']." minutes";
				} ?></span></p>
						</div>
							<div class='col-sm-3 text-right'><button class="btn btn-success btn-lg" type="submit">Accelerate!</button></div>
						</div>
						</div>
			</li>
		<?php } ?>
			<li>
				<div class='row'>
					<div class='col-sm-4'>
					<input id='acceleration-type-home-page'  type="radio"  autocomplete="off" name='acceleration-type' value='home-page' />
						<label for="acceleration-type-home-page">Home Page only</label>
		</div>
					
			  			<div class='accelerate-type-options'>
			<div class='col-sm-5'>
			<p>Accelerate only the home page</p>
			<p class='estimated-time-to-complete'>Estimated Time To Complete: <span class='est-time' rel='<?php echo $site_composition['time_per_page']; ?>'>
				5 minutes</span></p>
						</div>
							<div class='col-sm-3 text-right'><button class="btn btn-success btn-lg" type="submit">Accelerate!</button></div>
						</div>
						</div>
			</li>				  

			  		<li>
						<div class='row'>
						<div class='col-sm-4'>
						<input id='acceleration-type-manual'  type="radio" autocomplete="off" name='acceleration-type' value='manual' />
						
						<label for="acceleration-type-manual">Manual Installation</label>
						</div>
						<div class='accelerate-type-options'>
			<div class='col-sm-5'>
			<p>With this option, you choose to manually enable acceleration on your pages and posts.</p>
			
						</div>
							<div class='col-sm-3 text-right'><button class="btn btn-success btn-lg" type="submit">Continue</button></div>
						</div>
							
						</div>
	
			  </li>
		</ul>
			  	</form>
		<script>
			  	jQuery(".auto-acceleration-options input[type=radio]").on("change", function() {
				  jQuery(this).parents("form").find("li").removeClass("selected-option");
				  jQuery(this).parents("li").addClass("selected-option");
			  	});
				</script>			

         
			  
        </div>
      </div>
    </div>