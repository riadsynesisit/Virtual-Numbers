<?php 	
	if(SITE_COUNTRY=="UK"){ 
		$logo_revision = "-uk"; 
	}elseif(SITE_COUNTRY=="AU"){
		$logo_revision = "";
	}else{
		$logo_revision = "-r1";
	}
?>
<nav class="navbar navbar-default">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo SITE_URL;?>index.php"><img src="<?php echo SITE_URL;?>images/main-logo<?php echo $logo_revision; ?>.png" height="100" width="212" class="img-responsive" alt="Stickynumber Logo"></a>
        </div>
		<?php $url = $_SERVER['REQUEST_URI']; $active_class = 'class="active"';?>
        <div id="navbar" class="collapse navbar-collapse navbar-right">
          <ul class="nav navbar-nav">
            <li <?php if(strstr($url, '/pricing')){ echo $active_class;}?>>
				<a href="<?php echo SITE_URL;?>pricing/">Pricing</a>
			</li>     
            <li <?php if(strstr($url, '/tutorial')){ echo $active_class;}?>>
				<a href="<?php echo SITE_URL;?>tutorial/">Tutorials</a>
			</li>
            <li <?php if(strstr($url, '/how/')){ echo $active_class;}?>>
				<a href="<?php echo SITE_URL;?>how/">How it Works</a>
			</li>
            <li <?php if(strstr($url, '/feature')){ echo $active_class;}?>>
				<a href="<?php echo SITE_URL;?>features/">Features</a>
			</li>
            <li <?php if(strstr($url, '/faq')){ echo $active_class;}?>>
				<a href="<?php echo SITE_URL;?>faq/">FAQ</a>
			</li>
            <li <?php if(strstr($url, '/contact')){ echo $active_class;}?>>
				<a href="<?php echo SITE_URL;?>contact-us/">Contact Us</a>
			</li> 
          </ul>
          <div class="clearfix"></div>
        </div><!--/.nav-collapse -->
      </div>
    </nav>