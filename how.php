<?php

if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
	if(isset($_SERVER["REQUEST_URI"]) && ($_SERVER["REQUEST_URI"]=="/index.php?page=how" || $_SERVER["REQUEST_URI"]=="/how-to-get-a-phone-number" || $_SERVER["REQUEST_URI"]=="/how")){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: how/");
		exit;
	}
	
}else if($_SERVER["HTTP_HOST"] == "localhost"){
	include 'inc_common_header.php';
}
?>
<?php include 'inc_header_page.php'; ?>
  </head>
  <body>
  
	<?php include 'login_top_page.php';?>
  
    <?php include 'top_nav_page.php';?>
	
    <div class="container">
    <img width="1170" height="230" class="img-responsive" alt="Our Pricing" src="<?php echo SITE_URL;?>images/SF-How.jpg">
    </div><!-- /.container -->
    
    <div class="container mtop50">
            <div class="row">
              <div class="col-md-8">
              <h1>How it Works</h1>
<h2>See the process step by step</h2>
<img width="577" height="357" src="<?php echo SITE_URL;?>images/htw-steps.png" class="img-responsive" alt="Step by Step Process">
<p>Sticky Number makes going global quicker, easier and simpler than you'd ever have thought. Check out how easy it is below.</p>
<div class="grayborder">
<p><span class="box-num">1</span>Pick your local or toll free number from just about any location globally and get activated instantly.</p>
<img width="478" height="217" src="<?php echo SITE_URL;?>images/worldmap.jpg" class="img-responsive centeredImage mtop30" alt="World Map">
<p class="mtop30"><span class="box-num">2</span>Enter the number you wish to forward your brand new Sticky Number to. (Forward to local and mobile numbers)</p>
<img width="668" height="313" src="<?php echo SITE_URL;?>images/add-new.jpg" class="img-responsive centeredImage mtop30" alt="Add New">
<p class="mtop30"><span class="box-num">3</span>Dial your brand new Sticky Number from any phone.</p>
<img width="158" height="310" src="<?php echo SITE_URL;?>images/iphone.jpg" class="img-responsive centeredImage mtop30" alt="Iphone">
<p class="mtop30"><span class="box-num">4</span>The call goes via the Sticky Number service where we redirect to your destination number</p>
<img width="600" height="314" src="<?php echo SITE_URL;?>images/team.jpg" class="img-responsive centeredImage mtop30" alt="Team">
<p class="mtop30"><span class="box-num">5</span>Your destination phone rings</p>
<img width="158" height="310" src="<?php echo SITE_URL;?>images/samsung.jpg" class="img-responsive centeredImage mtop30" alt="Phone">
</div>
</div>
              <div class="col-md-4">
              <img width="317" height="165" src="<?php echo SITE_URL;?>images/support.png" class="img-responsive" alt="Support">
                       <div class="support-list">
                            <ul>
                               <li>Instant Account Activation</li>
                               <li>24/7 Server Monitoring</li>
                               <li>Complete Easy Manangement Tools</li>
                               <li>Accreditied Australian Number Supplier</li>
                               <li>Wordwide Phone Number Routing</li>
                               <li>Free Voice Mail</li>                               
                            </ul>
                       </div>              
              </div>
            </div>    
    </div>
    
	<?php include 'inc_free_uk_page.php' ;?>
    <?php include 'inc_footer_page.php' ;?>
	
  </body>
</html>
