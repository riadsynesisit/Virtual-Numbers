    <!-- Secondary Footer -->
    <!--<div id="sub-content-wrapper">
    	<div id="sub-content">
        <h1 class="fltlft">Want to get Free Virtual Phone Number? Sign up Now</h1>
        <a class="btn-green-big fltrt" href="<?php echo SITE_URL; ?>pricing/">Get your FREE Number</a>
        <div class="clearfloat"></div>
        </div>
    </div>-->
      <?php if($this_page=="home"){ ?>
         <div class="second-footer">
           	<div class="wrapper">
              	<div class="container Mtop30">
                   <div class="row text-center"><img alt="whats new" src="<?php echo SITE_URL; ?>images/whatsnew-icon.jpg"/></div>
                   <div class="whychoose-text">What's New</div> 
                   <div class="row Mtop30">
                        <div class="sf-one">
                           <div class="sf-head">
                              <div class="sf-icon"><img alt="range" src="<?php echo SITE_URL; ?>images/range.jpg"/></div>
                              <div class="sf-header">No Setup Fees</div>
                           </div>
                           <div class="sf-body">
                          We improved our system to automate the virtual number setup letting us waive setup fees.
                           </div>
                        </div>
                        <div class="sf-one Mleft20">
                           <div class="sf-head">
                              <div class="sf-icon"><img alt="International Divert" src="<?php echo SITE_URL; ?>images/net-divert.jpg"/></div>
                              <div class="sf-header">Free International Divert 24/7</div>
                           </div>
                           <div class="sf-body">All virtual numbers can be routed to thousands of destinations around the world.</div>
                        </div>
                        <div class="sf-one Mleft20">
                           <div class="sf-head">
                              <div class="sf-icon"><img alt="World wide" src="<?php echo SITE_URL; ?>images/worldwide.jpg"/></div>
                              <div class="sf-header">Worldwide Instant Activation</div>
                           </div>
                           <div class="sf-body">All accounts are now activated in real time giving you an instant virtual number.</div>
                        </div>
                   </div> 
                   </div>
                   </div>
                   </div>
                 <?php }else{//if not home page?>
                 	<div class="clearfloat"></div>
                   <div id="sub-content-wrapper" >
				    	<div id="sub-content">
				        <span class="span_sign_up">Want to get Free Virtual Phone Number? Sign up Now</span>
				        <a class="btn-green-big fltrt" href="<?php echo SITE_URL; ?>free-uk-number/">Get your FREE Number</a>
				        <div class="clearfloat"></div>
				        </div>
				    </div>
                   
                <?php }//end of id home page ?>
    <!-- Secondary Footer closed -->
    
    
    <!-- Main footer -->
    <div class="footer">
         <div class="wrapper">
              <div class="container H325 relative">
                   <div class="row Mtop55">
                        <div class="footer-col1">
                             <div class="footer-header">Get in Touch</div>
                             <?php if(SITE_COUNTRY!="UK"){ ?>
                             <div class="row Mtop30">
                                  <div class="footer-intouch-icon"><img alt="Phone Number" src="<?php echo SITE_URL; ?>images/fax-icon.png"/></div>
                                  <div class="footer-intouch">Phone Number: 0291 192 986</div>
                             </div>
                             <div class="row Mtop20">
                                  <div class="footer-intouch-icon">&nbsp;</div>
                                  <div class="footer-intouch">International Number: +61 291 192 986</div>
                             </div>
                             <?php } ?>
                             <div class="row Mtop20">
                                  <div class="footer-intouch-icon"><img alt="email us" src="<?php echo SITE_URL; ?>images/mail-icon.png"/></div>
                                  <div class="footer-intouch"><?php echo ACCOUNT_CHANGE_EMAIL; ?></div>
                             </div>
                        </div>
                        <div class="footer-col2 Mleft25">
                             <div class="footer-header">The Sitemap</div>
                             <div class="row Mtop30">
                                  <div class="footer-arrow"><img alt="footer arrow" src="<?php echo SITE_URL; ?>images/footer-arrow.png"/></div>
                                  <a href="<?php echo SITE_URL; ?>index.php"><div class="footer-sitemap">Home</div></a>
                                  <div class="footer-arrow"><img alt="footer arrow" src="<?php echo SITE_URL; ?>images/footer-arrow.png"/></div>
                                  <a href="<?php echo SITE_URL; ?>faq/"><div class="footer-sitemap2">FAQ</div></a>
                             </div>
                             <div class="row Mtop20">
                                  <div class="footer-arrow"><img alt="footer arrow" src="<?php echo SITE_URL; ?>images/footer-arrow.png"/></div>
                                  <a href="<?php echo SITE_URL; ?>how-to-get-a-phone-number/"><div class="footer-sitemap">Virtual Number</div></a>
                                  <div class="footer-arrow"><img alt="footer arrow" src="<?php echo SITE_URL; ?>images/footer-arrow.png"/></div>
                                  <a href="<?php echo SITE_URL; ?>contact-us/"><div class="footer-sitemap2">Contact Us</div></a>
                             </div>
                             <!--<div class="row Mtop20">
                                  <div class="footer-arrow"><img alt="footer arrow" src="<?php echo SITE_URL; ?>images/footer-arrow.png"/></div>
                                  <a href="<?php echo SITE_URL; ?>index.php?page=features_AU"><div class="footer-sitemap">Make Calls Online</div></a>
                             </div> -->
                        </div>
                        <div class="footer-col3 Mleft25">
                             <div class="footer-header">Payment Methods</div>
                             <div class="row Mtop30">
                                  <div class="worldpay"><img alt="Worldpay" src="<?php echo SITE_URL; ?>images/worldpay-footer.png"/></div>
                                  <div class="paypal-payment"><img alt="PayPal" src="<?php echo SITE_URL; ?>images/paypal-footer.png"/></div>
                                  <div class="clearfloat"></div>
                                  <h3 class="title2">Services Status</h3>
        						  <h3 class="title2 green-txt2">Call Forwarding: <span class="status">ACTIVE</span></h3>
                             </div>
                        </div>
                   </div>
                   <!-- Go Up -->
                   <div class="goup"><a href="#top"><img alt="top" src="<?php echo SITE_URL; ?>images/top.png"/></a></div>
              </div>
         </div>
    </div>
    <!-- Main footer closed -->
    
    <!-- Last Footer -->
    <div class="footer-last">
         <div class="wrapper">
              <div class="container">
                   <div class="footer-last-fine"><span class="fine-print">&copy; 2014 Sticky Number - </span> <span><?php echo COUNTRY_DEMONYM; ?> Virtual Number</span> <span class="fine-print"> All rights reserved - Sticky Number&reg;</span> <span>| <a href="<?php echo SITE_URL; ?>terms/" class="termslinks">Terms of Service</a> & <a href="<?php echo SITE_URL; ?>privacy-policy/" class="termslinks">Privacy Policy</a></span></div>
                   <div class="footer-social">
                       <div class="footer-social-items text-right"><a target="_new" href="https://www.facebook.com/stickynumber"><img alt="facebook" src="<?php echo SITE_URL; ?>images/facebook.png"/></a></div>
                       <div class="footer-social-items"><a target="_new" href="https://plus.google.com/b/113258218112686629360/113258218112686629360/posts"><img alt="google plus" src="<?php echo SITE_URL; ?>images/googleplus.png"/></a></div>
                       <div class="footer-social-items"><a target="_new" href="http://www.linkedin.com/company/sticky-number" ><img alt="Linked In" src="<?php echo SITE_URL; ?>images/linkedin.png" /></a></div>
                       <div class="footer-social-items"><a target="_new" href="https://twitter.com/stickynumber"><img alt="twitter" src="<?php echo SITE_URL; ?>images/twitter.png"/></a></div>
                   </div>
              </div>
         </div>
    </div>
    <script src="<?php echo SITE_URL; ?>js/jquery.icheck.min.js"></script>
	
</body>

<!--script  src="<?php //echo SITE_URL; ?>js/jquery.icheck.min.js"></script-->

<!--link type="text/css" rel="stylesheet" href="<?php //echo SITE_URL; ?>skins/minimal/minimal.css" /-->

<?php if($this_page!="virtual_number"){ ?>
<script type="text/javascript">
$(document).ready(function(){
  $('input').iCheck({
    checkboxClass: 'icheckbox_minimal',
    radioClass: 'iradio_minimal',
    increaseArea: '20%' // optional
  });
  $('input').iCheck2({
	    checkboxClass: 'icheckbox_minimal',
	    radioClass: 'iradio_minimal',
	    increaseArea: '20%' // optional
	  });
});
</script>
<?php } ?>
<!-- Google Tag Manager -->
<noscript><iframe title="Google Data Manager" src="//www.googletagmanager.com/ns.html?id=GTM-MH5XZK"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-MH5XZK');</script>
<!-- End Google Tag Manager -->
</html>