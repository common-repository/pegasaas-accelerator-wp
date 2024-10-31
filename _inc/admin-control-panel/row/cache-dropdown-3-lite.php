<form class='page-cache-form {% if cache_exists %}cache-exists{% endif %}'>
	<input type='hidden' name='pid' value='{{ pid }}' />
	<input type='hidden' name='post_id' value='{{ id }}' />
	<input type='hidden' name='c' value='rescan-pagespeed' />
				  	
	<div class="btn-group {% if settings.limits.monthly_optimizations > 0 %}cache-standard-edition{% endif %}">						
		<button type='button' class='btn btn-primary btn-xs btn-cache dropdown-toggle {% if settings.settings.auto_crawl.status > 0 %}auto-crawl-available{% endif %}' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

			<!-- optimization performed by API -->
			<i rel='{{ pid }}' class='material-icons material-check {% if not cache_exists or temp_cache_exists %}hidden{% endif %}' rel='tooltip' title='Fully Optimized'>done</i> 
			
			<!-- optimization performed by plugin -->
			{% if no_credits %}
			<i rel='{{ pid }}' class='material-icons material-local-cache-exists temp-cache-exists {% if not temp_cache_exists %}hidden{% endif %}' rel='tooltip' title='Un-optimized Page Cached.  Upgrade to a plan with more optimization credits to optimize this page.'>clear</i>	
			{% else %}
			<i rel='{{ pid }}' class='material-icons material-local-optimization-complete {% if not temp_cache_exists %}hidden{% endif %}' rel='tooltip' title='Partially Optimized - Full Optimization via API in progress.'>done</i>	
			{% endif %}
			
			<!-- optimization pending crawl -->
			<i rel='{{ pid }}' class='material-icons material-check-pending {% if cache_exists or settings.settings.auto_crawl.status == 0 %}hidden{% endif %}'
			   rel='tooltip' 
			   title='Pending Optimization - Will be optimized automatically by the auto crawl'>access_time</i>	
			
			<!-- optimization pending user visit -->
			<i rel='{{ pid }}' class='material-icons material-optimized-on-next-visit {% if cache_exists or settings.settings.auto_crawl.status > 0 %}hidden{% endif %}' 
			   rel='tooltip' 
			   title='The page has not yet been optimized.  It will be optimized and cached the next time a visitor views the page.'>snooze</i>
			
			<!-- spinner -->
			<i class='svg-icons svg-tail-spin hidden' rel='tooltip' title='...'></i>
			
			<!-- problem requesting optimization -->
			<i rel='{{ pid }}' class='material-icons material-problem hidden' rel='tooltip' title='There was a problem optimizing the page.  Please try again later.'>report_problem</i>
			
			<!-- prompt -->
			<span class="material-icons">more_vert</span>
		</button>
														
		<ul class="dropdown-menu">
			<li data-state='cache-exists'  {% if not cache_exists or temp_cache_exists %}class='hidden'{% endif %}><a class='purge-page-cache' data-slug='{{ slug }}' data-resource-id='{{ pid }}' href="#"><i class='fa fa-trash'></i> Purge Optimized Page Cache</a></li>
			<li data-state='cache-exists'  {% if not cache_exists or temp_cache_exists %}class='hidden'{% endif %}><a class='purge-page-cache-and-reoptimize' data-resource-id='{{ pid }}' data-reoptimize='1' data-slug='{{ slug }}' href="#"><i class='fa fa-magic'></i> Purge &amp; Re-Optimize</a></li>
			<li data-state='cache-exists'  {% if not cache_exists or temp_cache_exists %}class='hidden'{% endif %}><a class='reoptimize-without-cache-clear' data-resource-id='{{ pid }}' data-reoptimize='1' data-slug='{{ slug }}' href="#"><i class='fa fa-magic'></i> Re-Optimize </a></li>
			<li data-state='temp-cache-exists'  {% if not temp_cache_exists %}class='hidden'{% endif %}><a class='purge-page-cache' data-slug='{{ slug }}' data-resource-id='{{ pid }}' href="?page=pegasaas-accelerator&c=rebuild-page-cache&p={{ slug }}"><i class='fa fa-trash'></i> Purge Temporary Page Cache</a></li>
			<li data-state='cache-missing' {% if cache_exists %}class='hidden'{% endif %}><a class='build-page-cache' data-slug='{{ slug }}' data-resource-id='{{ pid }}' href="?page=pegasaas-accelerator&c=build-page-cache&p={{ slug }}"><i class='fa fa-magic'></i> Optimize Page</a></li>
		</ul>
	</div>
</form>
