<?php
//This page is reachable from about-us URL
if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
	if(isset($_SERVER["REQUEST_URI"]) && ($_SERVER["REQUEST_URI"]=="/index.php?page=aboutus" || $_SERVER["REQUEST_URI"]=="/aboutus.php" || $_SERVER["REQUEST_URI"]=="/about-us")){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: about-us/");
		exit;
	}
	
}else if($_SERVER["HTTP_HOST"]=="localhost"){
	include 'inc_common_header.php';
}

$this_page = "aboutus";
?>

<?php include 'inc_header_page.php'; ?>
  </head>
  <body>
  
	<?php include 'login_top_page.php';?>
  
    <?php include 'top_nav_page.php';?>
	
    <div class="container">
    <img width="1170" height="230" class="img-responsive" alt="Our Pricing" src="<?php echo SITE_URL; ?>images/SF-pricing.jpg">
    </div><!-- /.container -->
    
    <div class="container mtop50">
        <img width="50" height="30" src="<?php echo SITE_URL; ?>images/whychoose-icon.jpg" alt="What's New" class="centeredImage">
<h1 class="text-center">Why choose Sticky Number</h1>
<p>&nbsp;</p>
<p>Sticky Number has provided Virtual Numbers and phone numbers from 1220 cities to 1,000,000's of individuals and businesses worldwide since 2011. Sticky Number is a privately-owned virtual number company. Our offices and data centres are located throughout UK, USA, UAE, SIN and NZ.</p>
<p>We have more than 50 full-time developers, engineers, service and support specialists whose mission is to serve you. We've made our name by offering great service. But that doesn't mean that you get less. We want you to have it all - best technology and best service.</p>
<h2>The Technology</h2>
<p>We use only the latest, cutting edge hardware and software to give you superior performance with minimal downtime. Sticky Numbers robust, reliable network features Dell PowerEdge high speed servers, Cisco network hardware, and multiple fibre optic connections to the Internet backbone.</p>
<p>We also use load balancing technology to distribute web traffic between multiple servers, further increasing availability, performance and security.</p>
<h2>The Speed</h2>
<p>The Sticky Number gigabit (or better) network enables the fastest possible data transmission rates within our network. High capacity uplinks to both Tier 1 and Tier 2 bandwidth providers offers the best possible domestic and international performance.</p>
<h2>Reliability</h2>
<p>Our network is built with reliability in mind, with redundancy at every possible point. We utilise the latest in Cisco and Juniper routers and switches for our network and industry leading DELL Blade servers for content and service provision. Running both Linux (Unix) and Microsoft Windows server software, reliability and performance is achieved through the use of industry standard load balancing technologies.</p>
<h2>Service</h2>
<p>Rely on strong, dedicated tech support and customer service from our experienced consultants.</p>
<div id="choose-us-wrap">
    	<div class="choose-us-box">
        <img width="100" height="100" alt="Contact Us" src="<?php echo SITE_URL; ?>images/contact-mid.png"> <h2>Signup with us</h2>      
        </div>
        <img width="61" height="14" class="curved-sep" alt="Curve" src="<?php echo SITE_URL; ?>images/curve-separator.png">
<div class="choose-us-box">
        <img width="100" height="100" alt="Contact Us" src="<?php echo SITE_URL; ?>images/free-num.png"> <h2>Select Phone Number City</h2>      
    </div>
              <img width="61" height="14" class="curved-sep" alt="Curve" src="<?php echo SITE_URL; ?>images/curve-separator-2.png">
    	<div class="choose-us-box">
        <img width="100" height="100" alt="Contact Us" src="<?php echo SITE_URL; ?>images/divert.png"> <h2>Forward Phone Number</h2>      
        </div>                
        <div class="clearfix"></div>
    </div>
  </div><!-- /.container -->    
    
  
	<?php include 'inc_free_uk_page.php' ;?>
    <?php include 'inc_footer_page.php' ;?>

  </body>
</html>
