<!doctype html>
<?php

if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
	if(isset($_SERVER["REQUEST_URI"]) && $_SERVER["REQUEST_URI"]=="/features_new.php"){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: features/");
		exit;
	}
	
}

$this_page = "features_new";

require("./dashboard/includes/config.inc.php");
require "common_header.php";

?>

	<!-- Banner -->
    <div class="wrapper">	         <div class="container">
         <img src="<?php echo SITE_URL; ?>images/SF-Features.jpg" width="1024" height="201" alt="Features"> </div>
    </div>
    <!-- Banner Closed -->

    <!-- Main wrapper starts here -->
    <div class="wrapper">
         <div class="container">        
           <div id="tab3" style="display: block;">
<div class="plan-box">
<div class="htw">
<p>&nbsp;</p>
<h1>Features</h1><br>
<p class="font16">Protect your identity online by using a virtual <?php echo COUNTRY_DEMONYM; ?> number while advertising an ad online, in a newspaper, signing up to a website, selling a car, or making overseas business enquiries while maintaining a <?php echo COUNTRY_DEMONYM; ?> presence without displaying your real phone number.</p>
<p>&nbsp;</p>
<p class="font16">Furthermore to protecting your identity will not divulge any personal identifiable information with other company's or publically. Below is a list of features we include in our <?php echo COUNTRY_DEMONYM; ?> numbering system.</p><br>
<p>&nbsp;</p>
<div class="f-list fltlft">
    <ul>
    	<li><img src="<?php echo SITE_URL; ?>images/v-mes.jpg" width="44" height="40" alt="Voice Messages">
        <p class="txt-blue font18">Voice Messages</p>
        <p class="font16">Too busy to talk? Allow Sticky Number to take a message for you.</p>
        </li>
    	<li><img src="<?php echo SITE_URL; ?>images/cml.jpg" width="44" height="40" alt="Call on Mobile">
         <p class="txt-blue font18">Calls to mobiles and landlines</p>
<p class="font16">Call mobiles and landlines worldwide at low rates</p>
        </li>  
        <li><img src="<?php echo SITE_URL; ?>images/c-id.jpg" width="44" height="40" alt="Call ID">
 <p class="txt-blue font18">Caller ID</p>
<p class="font16">Calls received show the callers number.</p>        
        </li>             
    </ul>
</div><br>
<div class="clearfloat"></div><br />
<p class="font16">Protect your identity online by using a virtual <?php echo COUNTRY_DEMONYM; ?> number while advertising an ad online, in a newspaper, signing up to a website, selling a car, or making overseas business enquiries while maintaining a <?php echo COUNTRY_DEMONYM; ?> presence without displaying your real phone number. Furthermore to protecting your identity will not divulge any personal identifiable information with other company's or publically. Below is a list of features we include in our <?php echo COUNTRY_DEMONYM; ?> numbering system.</p><br><br>
<div class="fltlft">
<div class="feature-list font16">
<ul>
<li>24/7 Server Monitoring</li>
<li>Complete Easy Management Tools</li>
<li>View Call Records</li>
</ul>
</div>
</div>
<div class="fltrt">
<div class="feature-list font16">
<ul>
<li>24/7 access to your online control panel</li>
<li>Unlimited virtual <?php echo COUNTRY_NAME; ?> number routing</li>
<li>View call records</li>
</ul>
</div>
</div>
</div><!-- End of htw -->
<div class="contact-rightbox Mtop25">
                       <!-- Support -->
                       <div class="row relative">
                            <h2 class="heading h2_normal">24/7 Support</h2>
                            <div class="support-help<?php if(SITE_COUNTRY!="AU"){ echo "1"; } ?>"></div>                            
                       </div>
                       <!-- Support end-->
                       <div class="row Mtop150">
                       <!-- List -->
                       <div class="support-list">
                            <ul>
                               <li>Instant Account Activation</li>
                               <li>24/7 Server Monitoring</li>
                               <li>Complete Easy Manangement Tools</li>
                               <li>Accreditied <?php echo COUNTRY_DEMONYM; ?> Number Supplier</li>
                               <li>Wordwide Phone Number Routing</li>
                               <li>Free Voice Mail</li>                               
                            </ul><br>
                         <img src="<?php echo SITE_URL; ?>images/couldp.jpg" width="313" height="271" alt="Cloud"> </div>
                       </div>
                        
</div><!-- End of contact-rightbox -->
<div class="clearfloat"></div>
<p>&nbsp;</p>
</div>
<p>&nbsp;</p>
<p>&nbsp;</p>
<div class="col-2 fltlft">
<h2>Call Records</h2><br>
<p class="font16">We give you unlimited access to historical call recors made to all your Online Numbers</p><br>
<p class="font16">View detaild information about every call that is made to <?php echo SITE_DOMAIN; ?> Online Phone Number. Learn about your customers' call habits and high traffic call locations to help make decisions about outbound sales and follow-up. You can use the real-time call record for accounting purposes as well</p>
</div>
<img src="<?php echo SITE_URL; ?>images/call-records<?php if(SITE_COUNTRY == "UK"){echo "-uk"; }?>.jpg" alt="Call Records" width="607" height="536" class="fltrt">
<div class="clearfloat"></div>
<p>&nbsp;</p>
<p>&nbsp;</p>
<div class="col-2 fltrt">
<h2>Voice Mail</h2><br>
<p class="font16">If you're busy getting the kids ready for school or in back-to-back meetings, <?php echo SITE_DOMAIN; ?> offers a free voicemail service that can take a message for you.</p><br>
<p class="font16">Listen to your messages and return any calls at your Convenience</p><br>
<p class="font16"><a href="<?php echo SITE_URL; ?>voicemail.php">More Info</a></p>
</div>
  <img src="<?php echo SITE_URL; ?>images/voice-mail<?php if(SITE_COUNTRY == "UK"){echo "-uk"; }?>.jpg" width="607" height="536" alt="Voice Mail" class="fltlft">
<div class="clearfloat"></div>
<p>&nbsp;</p>
<p>&nbsp;</p>
<div class="col-2 fltlft">
<h2>Number Management</h2><br>
<p class="font16">Never miss a call thanks to our 24/7 call forwarding. Update your destination number instantly to any phone.</p><br>
<p class="font16"><strong>How it works:</strong>
Somebody calls you on Sticky Number, the call gets forwarded to a mobile or landline of you choice. You pick up and there's no extra charge for the person calling.</p><br>
<p class="font16">Upgrade or downgrade your plan to anytime to meeting your usage needs.</p>
</div>
<img src="<?php echo SITE_URL; ?>images/num-mngt<?php if(SITE_COUNTRY == "UK"){echo "-uk"; }?>.jpg" alt="Number Management" width="607" height="536" class="fltrt">
<div class="clearfloat"></div>
</div><!-- .Plan Box -->
         </div><!-- .Container -->
    </div> 
<div class="clearfloat"></div>   
    <!-- Main wrapper starts here Closed -->
    <p>&nbsp;</p><p>&nbsp;</p>
    <div class="logo-wrapper">
    <div class="logo-wrap">
      <img src="<?php echo SITE_URL; ?>images/cloudlinux.png" width="252" height="119" alt="CloudLinux">
      <img src="<?php echo SITE_URL; ?>images/ms-windows.png" width="200" height="119" alt="MS Server">
      <img src="<?php echo SITE_URL; ?>images/w-server.png" width="466" height="118" alt="Windows Server 2012">
      <img src="<?php echo SITE_URL; ?>images/pp-plesk.png" width="253" height="118" alt="Plesk">
      <img src="<?php echo SITE_URL; ?>images/dell-tm.png" width="243" height="118" alt="Dell">
      <img src="<?php echo SITE_URL; ?>images/ms-exc.png" width="245" height="119" alt="MS Echange">
      <img src="<?php echo SITE_URL; ?>images/asterisk.png" width="205" height="119" alt="Asterisk">
      </div>
      <div class="clearfloat"></div>
</div>

<?php 
    include('inc_footer_AU.php');
?>