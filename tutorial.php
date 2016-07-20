<?php
	if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
		if(isset($_SERVER["REQUEST_URI"]) && ($_SERVER["REQUEST_URI"]=="/index.php?page=tutorial"  || $_SERVER["REQUEST_URI"]=="/tutorial" || $_SERVER["REQUEST_URI"]=="/?page=tutorial")){
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: tutorial/");
			exit;
		}
	
	}

	include 'inc_header_page.php';
?>
  </head>
  <body>
	<?php include 'login_top_page.php';?>
  
    <?php include 'top_nav_page.php';?>
	
	<?php include 'get_number_sql_data.php'; ?>
	
    <div class="container">
    <img width="1170" height="230" class="img-responsive" alt="Our Pricing" src="<?php echo SITE_URL ;?>images/inner-banner.jpg">
    </div><!-- /.container -->
<div class="container mtop50">
    <div class="row">
      <div class="col-md-8">
<h1>Tutorial</h1>
<h3 class="mtop30">Getting Started</h3>
<h4><a href="<?php echo SITE_URL ;?>tutorials/what-services-do-you-offer.php">What services do you offer?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/how-can-StickyNumber-help-my-business.php">How can Sticky Number help my business?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/what-types-of-phone-numbers-do-you-offer.php">What types of phone numbers do you offer?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/can-callers-tell-the-location-theyre-calling.php">Can callers tell that I'm not physically in the location they're calling?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/Can-I-transfer-existing-toll-free-phone-numbers-StickyNumber.php">Can I transfer my existing toll-free phone numbers to Sticky Number?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/can-I-transfer-StickyNumber-numbers-another-carrier.php">Can I transfer my Sticky Number numbers to another carrier?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/do-I-have-to-agree-to-long-term-commitment.php">Do I have to agree to a long-term commitment?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/how-many-phone-numbers-can-I-have-on-my-account.php">How many phone numbers can I have on my account?</a><h4>


<h3 class="mtop30">Technical</h3>
<h4><a href="<?php echo SITE_URL ;?>tutorials/how-do-I-delete-a-number.php">How do I delete a number?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/how-to-add-another-phone-number.php">How to add another phone number?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/how-do-I-change-the-forwarding-number.php">How do I change the forwarding number?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/how-do-I-sign-up-for-a-virtual-phone-number.php">How do I sign up for a virtual phone number?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/how-do-I-reset-the-password-on-my-account.php">How do I reset the password on my account?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/how-can-I-enable-disable-sticky-number-voicemail.php">How can I enable or disable Sticky Number voice mail?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/can-I-choose-a-specific-number.php">Can I choose a specific number that I would like to have?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/how-do-I-setup-voicemail.php">How do I setup voice mail on my number?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/how-do-I-change-the-number-of-rings.php">How do I change the number of rings before voice mail is activated?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/what-kind-call-volume-can-I-get.php">What kind of call volume can I get?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/what-safeguards-do-you-have-to-ensure-quality-service.php">What safeguards do you have in place to ensure quality service?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/can-you-forward-calls-to-my-SIP-device.php">Can you forward calls to my SIP device?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/is-StickyNumber-similar-to-Skype-or-Vonage.php">Is Sticky Number similar to Skype or Vonage?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/is-there-someone-I-can-contact-for-technical-support.php">Is there someone I can contact for technical support?</a><h4>


<h3 class="mtop30">Billing</h3>
<h4><a href="<?php echo SITE_URL ;?>tutorials/how-do-I-change-the-auto-refill-on-my-number.php">How do I change the auto refill on my number?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/how-to-add-credit-to-my-account.php">How to add credit to my account?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/how-do-I-stop-automatic-billing-on-my-card.php">How do I stop automatic billing on my card or PayPal account?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/how-to-upgrade-or-downgrade-my-account.php">How to upgrade or downgrade my plan?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/my-number-has-expired-how-do-I-renew.php">My number has expired how do I renew?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/how-do-I-change-paypal-creditcard-auto-billed-on.php">How do I change the PayPal or credit card I am auto billed on?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/how-is-monthly-pricing-determined.php">How is monthly pricing determined?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/what-if-I-use-more-than-my-monthly-minutes.php">What if I use more than my monthly minutes?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/what-if-I-want-a-discount.php">What if I want a discount?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/what-payment-methods-do-you-accept.php">What payment methods do you accept?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/are-there-taxes-fees-surcharges-added-my-monthly-rate.php">Are there taxes, fees, or surcharges added to my monthly rate?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/how-do-I-review-my-billing-activity.php">How do I review my billing activity?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/wiill-I-receive-a-bill-in-mail.php">Will I receive a bill in the mail?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/is-there-someone-I-can-contact-for-questions-about-my-bill.php">Is there someone I can contact for questions about my bill?</a><h4>


<h3 class="mtop30">Account</h3>
<h4><a href="<?php echo SITE_URL ;?>tutorials/how-do-I-change-my-contact-information.php">How do I change my contact information?</a><h4>
<h4><a href="<?php echo SITE_URL ;?>tutorials/can-I-change-my-registered-email.php">Can I change my registered email address; if so, how?</a><h4>



      </div>
      <div class="col-md-4">
      <div class="get-started-box">
    <div class="get-started-white">
    <div class="getstarted-btn-box">
    	<div class="getstarted-btn">Get your Number</div>
    </div>
    <div class="getstarted-formbox">
    
		<div class="form-group">
			<label for="did_country">Select the Number you would like:</label>
			<select id="did_country" name="did_country" class="form-control">
	            <option value="">Please select a country</option>
				<?php foreach($did_countries as $did_country) { ?>
				<option value="<?php echo trim($did_country["country_iso"]); ?>" <?php if(trim($sel_did_country_code)!="" && trim($sel_did_country_code)==trim($did_country["country_iso"])){echo " selected "; } ?>><?php echo $did_country["country_name"] . ' ' . $did_country["country_prefix"] ?>
				</option>
				<?php } ?>
			</select> 
			<label for="city">Select City:</label>
			<select id="city" name="city" class="form-control mtop5" disabled="disabled">
				<option value="" selected="selected">Loading...</option>
			</select>                                                                                                 
		</div>
		<div class="form-group">
			<label for="country">Enter the Number to forward to:</label>
			<select id="country" name="country" class="form-control">
	            <option value="">Please Select Forwarding Country</option>
				<?php foreach($countries as $country) { ?>
				<option value="<?php echo trim($country['country_code']); ?>" <?php if(trim($sel_fwd_cntry) != "" && trim($sel_fwd_cntry)==trim($country["country_code"])){echo " selected "; } ?>><?php echo $country['printable_name'] . '+' . $country['country_code'] ?></option>
				<?php } ?>
			</select> 
			<label for="number">Enter Number:</label>
			<input type="text" value="" id="number" name="number" placeholder="+" class="input form-control mtop5">                                                 
		</div>
      <button id="btn-continue-step1" class="btn btn-lg btn-primary pull-right">Continue <i class="fa fa-angle-right"></i></button>
	
    </div><!-- End of getstarted-formbox -->
                                    
								<div class="clearfix"></div>
                                </div><!-- End of get-started-white div tag -->
    <div class="clearfix"></div>                                  
                            </div>
                            </div>
    </div>
    
    </div>    
	<?php include 'js/get_number_scripts.js.php';?>
	<?php include 'inc_free_uk_page.php' ;?>
    <?php include 'inc_footer_page.php' ;?>

   <?php include 'inc_hidden_forms.php';?>
   
  </body>
</html>
