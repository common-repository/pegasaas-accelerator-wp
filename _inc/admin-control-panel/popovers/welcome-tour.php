<?php if ($pegasaas->interface->take_tour() ) { ?>
<div class="modal fade" id="welcome-tour" tabindex="-1" role="dialog" aria-labelledby="WelcomeModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
      <div class="modal-body">
		  <h1>Welcome!</h1>
		 
		  <p>We'd like to take a minute to walk you through the Pegasaas Accelerator WP Dashboard, so you can take
		  full advantage of all of the available features.</p>
		  <div class="embed-responsive embed-responsive-16by9">
		  <iframe class="embed-responsive-item" width="560" height="315" src="https://www.youtube.com/embed/f90Kb1AJiIA" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		  </div>
		  <br>
		  <button class='btn btn-default' id="welcome-clear" data-feature=''>Close</button>
      </div>
    </div>
  </div>
</div>

<?php } ?>