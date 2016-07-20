<section class="top-footer">
    	<div class="container">
        <div class="row">
          <div class="col-md-3">
          	<h3>About Sticky Number</h3>
              <ul class="list-unstyled">
              	<li><a href="<?php echo SITE_URL;?>about-us/"><i class="fa fa-long-arrow-right"></i> About Us</a></li>
                <li><a href="<?php echo SITE_URL;?>contact-us/"><i class="fa fa-long-arrow-right"></i> Contact Us</a></li>
                <li><a href="<?php echo SITE_URL;?>feedback/"><i class="fa fa-long-arrow-right"></i> Site Suggestions</a></li>
              </ul>                             
          </div>
          <div class="col-md-3">  
          <h3>Products</h3>         
              <ul class="list-unstyled">              
              	<li><a href="<?php echo SITE_URL;?>pricing/"><i class="fa fa-long-arrow-right"></i> Pricing</a></li>              
				<li><a href="#"><i class="fa fa-long-arrow-right"></i> Login</a></li>              
                <li><a href="<?php echo SITE_URL;?>free-uk-number/"><i class="fa fa-long-arrow-right"></i> Free UK Number</a></li>
				<li><a href="<?php echo SITE_URL;?>features/"><i class="fa fa-long-arrow-right"></i> Features</a></li>                
              </ul>
          </div> 
          <div class="col-md-3">  
          <h3>Support</h3>         
              <ul class="list-unstyled">              
              	<li><a href="<?php echo SITE_URL;?>faq/"><i class="fa fa-long-arrow-right"></i> FAQ</a></li>              
				<!--li><a href="tutorials/"><i class="fa fa-long-arrow-right"></i> Tutorials</a></li-->              
                <li><a href="<?php echo SITE_URL;?>how/"><i class="fa fa-long-arrow-right"></i> How it works</a></li>
				<!--li><link rel="alternate" hreflang="x-default" href="http://www.stickynumber.com/"> 
				<link rel="alternate" hreflang="en-au" href="http://www.stickynumber.com.au/"> 
				<link rel="alternate" hreflang="en-gb" href="http://www.stickynumber.co.uk/"> </li-->
				<li><select onchange="location.href=this.value" class="sel_change_site">
						<option <?php if(SITE_COUNTRY == 'AU'){echo 'selected';}?> value="https://www.stickynumber.com.au">Australia</option>
						<option <?php if(SITE_COUNTRY == 'UK'){echo 'selected';}?> value="https://www.stickynumber.uk">United Kingdom</option>
						<option <?php if(SITE_COUNTRY == 'COM'){echo 'selected';}?> value="https://www.stickynumber.com">Global</option>
					</select>
				</li>
              </ul>
          </div>                      
          <div class="col-md-3">
          	<h3>Payment Methods</h3>
            <div class="row">
              <div class="col-md-6"><img width="160" height="90" src="<?php echo SITE_URL;?>images/worldpay-footer.png" alt="worldpay" class="img-responsive"/></div>
              <div class="col-md-6"><img width="216" height="90" src="<?php echo SITE_URL;?>images/paypal-footer.png" alt="paypal" class="img-responsive"/></div>
            </div>
            <p class="mtop15"><strong>Services Status</strong></p>
            <p><strong>Call Forwarding: <span class="text-success">ACTIVE</span></strong></p>
          </div>
        </div>
        </div>
    </section>    
    
	<section class="bottom-footer">
              <div class="container">
              <div class="row">
                  <div class="col-md-8 mtop5">&copy; 2014 Sticky Number - <?php echo COUNTRY_DEMONYM;?> Virtual Number All rights reserved - Sticky Number&reg;| <a href="<?php echo SITE_URL?>terms/" class="termslinks">Terms of Service</a> & <a href="<?php echo SITE_URL?>privacy-policy/" class="termslinks">Privacy Policy</a></div>
                  <div class="col-md-4">
                  	<div class="footer-social">
                       <div class="footer-social-items text-right"><a href="https://www.facebook.com/stickynumber" target="_blank"><img width="34" height="33" src="<?php echo SITE_URL;?>images/facebook.png" alt="facebook logo" /></a></div>
                       <div class="footer-social-items"><a href="https://plus.google.com/b/113258218112686629360/113258218112686629360/posts" target="_blank"><img src="<?php echo SITE_URL;?>images/googleplus.png" width="43" height="33" alt="google plus logo" /></a></div>
                       <div class="footer-social-items"><a href="http://www.linkedin.com/company/sticky-number" target="_blank"><img width="42" height="33" src="<?php echo SITE_URL;?>images/linkedin.png" alt="linkedin logo" /></a></div>
                       <div class="footer-social-items"><a href="https://twitter.com/stickynumber" target="_blank"><img src="<?php echo SITE_URL;?>images/twitter.png" width="43" height="33" alt="twitter logo" /></a></div>
                   </div>
            	</div>
               </div>    

              </div>
    </section>
<?php
	if($conn){
		//echo $conn;
		mysql_close($conn);
	}
?>