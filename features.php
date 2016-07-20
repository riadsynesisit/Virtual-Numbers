<?php

if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
	if(isset($_SERVER["REQUEST_URI"]) && ($_SERVER["REQUEST_URI"]=="/features.php" || $_SERVER["REQUEST_URI"]=="/features" )){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: features/");
		exit;
	}
	
}else if($_SERVER["HTTP_HOST"]=="localhost"){
	include 'inc_common_header.php';
}
?>
<?php include 'inc_header_page.php'; ?>
</head>
  <body>
	
	<?php include 'login_top_page.php';?>
  
    <?php include 'top_nav_page.php';?>
	
    <div class="container">
    <img width="1170" height="230" class="img-responsive" alt="Our Pricing" src="<?php echo SITE_URL;?>images/SF-Features.jpg">
    </div><!-- /.container -->
    
    <div class="container">
    <div class="grayborder mtop50">
    <div class="row">
      <div class="col-md-8">
<h2 class="convert_h1_h2">Features</h2>
<p class="justify_text">Protect your identity online by using a virtual Australian number while advertising an ad online, in a newspaper, signing up to a website, selling a car, or making overseas business enquiries while maintaining a Australian presence without displaying your real phone number.</p>
<p class="justify_text">Furthermore to protecting your identity will not divulge any personal identifiable information with other company's or publically. Below is a list of features we include in our Australian numbering system.</p>
 <div class="f-list">
    <ul>
    	<li><img src="<?php echo SITE_URL;?>images/v-mes.jpg" alt="Voice Messages">
        <p class="txt-blue font18"><strong>Voice Messages</strong></p>
        <p>Too busy to talk? Allow Sticky Number to take a message for you.</p>
        </li>
    	<li><img src="<?php echo SITE_URL;?>images/cml.jpg" alt="Call on Mobile">
         <p><strong>Calls to mobiles and landlines</strong></p>
<p class="font16">Call mobiles and landlines worldwide at low rates</p>
        </li>  
        <li><img src="<?php echo SITE_URL;?>images/c-id.jpg" alt="Call ID">
 <p><strong>Caller ID</strong></p>
<p>Calls received show the callers number.</p>        
        </li>             
    </ul>
    
</div>
<div class="clearfix"></div>
<p class="mtop30 justify_text">Protect your identity online by using a virtual Australian number while advertising an ad online, in a newspaper, signing up to a website, selling a car, or making overseas business enquiries while maintaining a Australian presence without displaying your real phone number. Furthermore to protecting your identity will not divulge any personal identifiable information with other company's or publically. Below is a list of features we include in our Australian numbering system.</p>    
<div class="row">
  <div class="col-md-6">
    <div class="feature-list">
    <ul>
    <li>24/7 Server Monitoring</li>
    <li>Complete Easy Management Tools</li>
    <li>View Call Records</li>
    </ul>
    </div>  
  </div>
  <div class="col-md-6">
    <div class="feature-list">
    <ul>
    <li>24/7 access to your online control panel</li>
    <li>Unlimited virtual Australia number routing</li>
    <li>View call records</li>
    </ul>
    </div>  
  </div>
</div>

      </div>
      <div class="col-md-4">
      <img src="<?php echo SITE_URL;?>images/support.png" class="img-responsive" alt="Support">
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
      <img src="<?php echo SITE_URL;?>images/couldp.jpg" class="img-responsive" alt="Cloud"> </div>
    </div>
    </div>
 <img src="<?php echo SITE_URL;?>images/call-records.jpg" class="img-responsive pull-right mtop50" alt="Call Records">    
<h2 class="convert_h1_h2 mtop50">Call Records</h2>

<p class="justify_text">We give you unlimited access to historical call recors made to all your Online Numbers</p>

<p class="justify_text">View detaild information about every call that is made to Stickynumber.com.au Online Phone Number. Learn about your customers' call habits and high traffic call locations to help make decisions about outbound sales and follow-up. You can use the real-time call record for accounting purposes as well.</p>
<div class="clearfix"></div>
<img src="<?php echo SITE_URL;?>images/voice-mail.jpg" class="img-responsive pull-left mtop50" alt="Voice Mail">
<h2 class="convert_h1_h2 mtop50">Voice Mail</h2>

<p class="justify_text">If you're busy getting the kids ready for school or in back-to-back meetings, Stickynumber.com.au offers a free voicemail service that can take a message for you.</p>

<p class="justify_text">Listen to your messages and return any calls at your Convenience</p>

<a href="<?php echo SITE_URL ; ?>voicemail/">More Info </a>
<div class="clearfix"></div>
<img src="<?php echo SITE_URL;?>images/num-mngt.jpg" class="img-responsive pull-right mtop50" alt="Number Management">
<h2 class="convert_h1_h2 mtop50">Number Management</h2>

<p class="justify_text">Never miss a call thanks to our 24/7 call forwarding. Update your destination number instantly to any phone.</p>

<p class="justify_text"><strong>How it works</strong>: Somebody calls you on Sticky Number, the call gets forwarded to a mobile or landline of you choice. You pick up and there's no extra charge for the person calling.</p>

<p class="justify_text">Upgrade or downgrade your plan to anytime to meeting your usage needs.</p>
<div class="clearfix"></div>
<div class="grayborder mtop50">
      <img src="<?php echo SITE_URL; ?>images/cloudlinux.png" class="img-responsive pull-left" alt="CloudLinux">
      <img src="<?php echo SITE_URL; ?>images/ms-windows.png" class="img-responsive mlft20 pull-left" alt="MS Server">
      <img src="<?php echo SITE_URL; ?>images/w-server.png" class="img-responsive mlft20 pull-left" alt="Windows Server 2012">
            <div class="clearfix"></div>
      <img src="<?php echo SITE_URL; ?>images/pp-plesk.png" class="img-responsive pull-left" alt="Plesk">
      <img src="<?php echo SITE_URL; ?>images/dell-tm.png" class="img-responsive mlft20 pull-left" alt="Dell">
      <img src="<?php echo SITE_URL; ?>images/ms-exc.png" class="img-responsive mlft20 pull-left" alt="MS Echange">
      <img src="<?php echo SITE_URL; ?>images/asterisk.png" class="img-responsive mlft20 pull-left" alt="Asterisk">
            <div class="clearfix"></div>
      </div>
</div>
    
	<?php include 'inc_free_uk_page.php' ;?>
    <?php include 'inc_footer_page.php' ;?>
	
  </body>
</html>
