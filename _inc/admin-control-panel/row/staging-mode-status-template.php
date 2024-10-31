<form class='staging-mode-status {{ staging_mode_status }}'>
	<input type='hidden' name='pid' value='{{ pid }}' />
	<input type='hidden' name='post_id' value='{{ id }}' />
				  	
	<div class="btn-group staging-mode-button-active">						
		<button type='button' class='btn btn-staging dropdown-toggle ' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			STAGING		
			<!-- prompt -->
			<span class="caret"></span>
		</button>
														
		<ul class="dropdown-menu">
			<li><a class='move-to-live-mode' data-slug='{{ slug }}' data-resource-id='{{ pid }}' href="?page=pegasaas-accelerator">Make LIVE</a></li>
			<li><a class='preview-page' href="{{ slug }}?accelerate=on" target="_blank">Preview</a></li>
		</ul>
	</div>
	<div class="btn-group staging-mode-button-disabled">						
		<button type='button' class='btn btn-live dropdown-toggle ' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			LIVE		
			<!-- prompt -->
			<span class="caret"></span>
		</button>
														
		<ul class="dropdown-menu">
			<li><a class='move-to-staging-mode' data-slug='{{ slug }}' data-resource-id='{{ pid }}' href="?page=pegasaas-accelerator">Move to STAGING</a></li>
			<li><a class='preview-page' href="{{ slug }}?accelerate=on" target="_blank">Preview</a></li>
		</ul>
	</div>	
</form>	