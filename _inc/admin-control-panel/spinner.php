<div class="lds-css">
  <div style="width:100%;height:100%" class="lds-ripple">
    <div></div>
    <div></div>
  </div>
<style type="text/css">@-webkit-keyframes lds-ripple {
  0% {
	<?php if ($spinner_size == "large") { ?>
    top: 90px;
    left: 90px;
	  <?php } else { ?>
	top: 40px;
    left: 40px;
	<?php } ?>
    width: 0;
    height: 0;
    opacity: 1;
  }
  100% {
    top: 15px;
    left: 15px;
	<?php if ($spinner_size == "large") { ?>
    width: 150px;
    height: 150px;
	  <?php } else { ?>
	width: 50px;
    height: 50px;
	<?php } ?>
    opacity: 0;
  }
}
@keyframes lds-ripple {
  0% {
 	<?php if ($spinner_size == "large") { ?>
    top: 90px;
    left: 90px;
	  <?php } else { ?>
	top: 40px;
    left: 40px;
	<?php } ?>
    width: 0;
    height: 0;
    opacity: 1;
  }
  100% {
    top: 15px;
    left: 15px;
	<?php if ($spinner_size == "large") { ?>
    width: 150px;
    height: 150px;
	  <?php } else { ?>
	width: 50px;
    height: 50px;
	<?php } ?>
    opacity: 0;
  }
}
.lds-ripple {
  position: relative;
}
.lds-ripple div {
  box-sizing: content-box;
  position: absolute;
  border-width: 2px;
  border-style: solid;
  opacity: 1;
  border-radius: 50%;
  -webkit-animation: lds-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite;
  animation: lds-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite;
}
.lds-ripple div:nth-child(1) {
  border-color: #2c343b;
}
.lds-ripple div:nth-child(2) {
  border-color:#16C5FF;
  -webkit-animation-delay: -0.5s;
  animation-delay: -0.5s;
}
	.lds-css { 
		
		width: <?php if ($spinner_size == "large") { print "180px"; } else { print "90px"; } ?>;
		height: <?php if ($spinner_size == "large") { print "180px"; } else { print "90px"; } ?>;
		margin: auto;
	<?php if ($spinner_size == "large") { ?>
	margin-top: -15px; 
		<?php } ?>
	}
	#js-rotating { font-weight: bold; color: #16C5FF }
</style></div>