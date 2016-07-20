<?php 	
	if(SITE_COUNTRY=="UK"){ 
		$logo_revision = "-uk"; 
	}elseif(SITE_COUNTRY=="AU"){
		$logo_revision = "";
	}else{
		$logo_revision = "-r1";
	}
?>
	<!-- Menu and Logo -->
	    <div class="wrapper">
	         <div class="container">
	              <div class="menucontain">
	                   <div class="logo"><a href="<?php echo SITE_URL; ?>index.php"><img alt="Main Logo" src="<?php echo SITE_URL; ?>images/main-logo<?php echo $logo_revision; ?>.png"/></a></div>
	                   <div id="top-nav">
	                   		<ul>
	                        	<li><a href="<?php echo SITE_URL; ?>index.php">Home</a></li>
	                        	<li><a href="<?php echo SITE_URL; ?>about-us/">About Us</a></li>
	                        	<li><a href="<?php echo SITE_URL; ?>how-to-get-a-phone-number/">How it Works</a></li>
	                        	<li><a href="<?php echo SITE_URL; ?>features/">Features</a></li>
	                        	<li><a href="<?php echo SITE_URL; ?>pricing/">Pricing</a></li>
	                        	<li><a href="<?php echo SITE_URL; ?>faq/">FAQ's</a></li>
	                        	<li><a href="<?php echo SITE_URL; ?>contact-us/">Contact Us</a>
	                        </ul>
	                        <div class="clearfloat"></div>
	                   </div>
	              </div>
	         </div>
	    </div>
	    <!-- Menu and Logo Closed-->