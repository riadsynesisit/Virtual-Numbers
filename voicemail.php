<?php
//This page is reachable from about-us URL
if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
	if(isset($_SERVER["REQUEST_URI"]) && ($_SERVER["REQUEST_URI"]=="/voicemail.php" || $_SERVER["REQUEST_URI"]=="/voicemail")){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: voicemail/");
		exit;
	}
	
}else if($_SERVER["HTTP_HOST"]=="localhost"){
	include 'inc_common_header.php';
}

$this_page = "voicemail";
?>

<?php include 'inc_header_page.php'; ?>
  <body>
  
	<?php include 'login_top_page.php';?>
  
    <?php include 'top_nav_page.php';?>
	
    <div class="container">
    <img width="1170" height="230" class="img-responsive" alt="Our Pricing" src="<?php echo SITE_URL; ?>images/SF-pricing.jpg">
    </div><!-- /.container -->
    
    <div class="container mtop50">
       <div class="container">       
              <div class="whychoose">
                   <div class="row text-center">
					<img width="50" height="30" alt="why choose us" src="<?php echo SITE_URL; ?>images/whychoose-icon.jpg"/>
				   </div>
                   <h1 class="whychoose-text">Sticky Voicemail</div>  
              </div>             
                    <div class="row Mtop20 font16 grey">
<p>Voicemail is a computer based system that allows users and subscribers to exchange personal voice messages to select and deliver voice information; and to process transactions relating to individuals, organizations, products and services, using an ordinary telephone.</p>

<p class="Mtop15">Voice-mail systems are designed to convey a caller's recorded audio message to a recipient. To do so they contain a user interface to select, play, and manage messages a delivery method to either play or otherwise deliver the message and a notification ability to inform the user of a waiting message. Most systems use phone-networks, either cellular or land-line based, as the conduit for all of these functions. Some systems may use multiple telecommunications methods, permitting recipients and callers to retrieve or leave messages through multiple devices.</p>

<h2 class="Mtop15">Features</h2>
<div class="feature-list">
<ul>
<li>Out-of-Office Greeting</li>
<li>Reply or Call Back</li>
<li>Urgent Message Retrieval</li>
<li>Message Identifier</li>
</ul>
</div>               
                    </div>
                    <div class="clearfloat"></div>
             <p>&nbsp;</p>      
             <p>&nbsp;</p>  
             <p>&nbsp;</p>      
         </div>
     
  </div><!-- /.container -->    
    
  
	<?php include 'inc_free_uk_page.php' ;?>
    <?php include 'inc_footer_page.php' ;?>

  </body>
</html>
