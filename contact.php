<?php
if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
	if(isset($_SERVER["REQUEST_URI"]) && ($_SERVER["REQUEST_URI"]=="/index.php?page=contact"  || $_SERVER["REQUEST_URI"]=="/contact-us" || $_SERVER["REQUEST_URI"]=="/index.php?page=contact_us" || $_SERVER["REQUEST_URI"]=="/?page=contact_us")){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: contact-us/");
		exit;
	}
	
}else if($_SERVER["HTTP_HOST"]!="localhost"){
	include 'inc_common_header.php';
}
?>

<?php include 'inc_header_page.php'; ?>

	
  
  </head>

  <body>
	
	<?php include 'login_top_page.php';?>
  
    <?php include 'top_nav_page.php';?>

	<?php include 'get_number_sql_data.php'; ?>
	
    <div class="container">
    <img width="1170" height="230" class="img-responsive" alt="Our Pricing" src="<?php echo SITE_URL;?>images/SF-Contactus.jpg">
    </div><!-- /.container -->
    
    <div class="container mtop50">
    <div class="row">
      <div class="col-md-8">
      <h2 class="heading">Contact Us</h2>
      <p>Our friendly Customer Service Representatives are ready to help with any comments or queries you may have.</p>
     <div class="row mtop30">
      <div class="col-md-6">
      <h2 class="heading">Feedback</h2>
     <!--form-->
      <div class="form-group">
        <label for="sender_name">Name</label>
        <input type="text" name="sender_name" class="form-control border" id="sender_name">
      </div>
      <div class="form-group">
        <label for="email">Email address</label>
        <input type="email" class="form-control border" name="sender_email" id="sender_email" placeholder="Enter email">
      </div>
      <div class="form-group">
        <label for="sender_message">Message</label>
      <textarea id="sender_message" name="sender_message" class="form-control border" rows="3"></textarea>
      </div>      
      <button id="send_feedback" type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Send</button> <button type="cancel" onclick="location.reload();" class="btn btn-default">Cancel</button>
    <!--/form-->     
      </div>
      <div class="col-md-6">
      <h2 class="heading">Support</h2>
      <div class="supportbox">
      <p>Check out our FAQ to find answers to most questions, otherwise contact us via the details below at any time!</p>
      <hr class="hr-styled">
	  <?php if(SITE_COUNTRY=="AU"){ ?>
      <!--p>Phone Number : 0291 192 986</p>
      <p>International Number : +61 291 192 986</p-->
      <p>Email : <a href="mailto:<?php echo ACCOUNT_CHANGE_EMAIL;?>"><?php echo ACCOUNT_CHANGE_EMAIL;?></a></p>
	  <?php }elseif(SITE_COUNTRY=="UK"){ ?>
	  <!--p>Phone Number : 020 3734 2433</p>
      <p>International Number : +44 20 3734 2433</p-->
      <p>Email : <a href="mailto:<?php echo ACCOUNT_CHANGE_EMAIL;?>"><?php echo ACCOUNT_CHANGE_EMAIL;?></a></p>
	  <?php }elseif(SITE_COUNTRY=="COM"){ ?>
	  <p>Email : <a href="mailto:<?php echo ACCOUNT_CHANGE_EMAIL;?>"><?php echo ACCOUNT_CHANGE_EMAIL;?></a></p>
	  <?php } ?>
      <!--p class="mtop50">Connect with us</p>
      <div class="footer-social-items text-right"><img src="<?php echo SITE_URL;?>images/facebook.png"/></div>
                                       <div class="footer-social-items"><img src="<?php echo SITE_URL;?>images/googleplus.png"/></div>
                                       <div class="footer-social-items"><img src="<?php echo SITE_URL;?>images/youtube.png"/></div>
                                       <div class="footer-social-items"><img src="<?php echo SITE_URL;?>images/twitter.png"/></div-->
                                       <div class="clearfix"></div>
      </div>
      </div>
    </div>     
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
			<label for="city">Select City</label>
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
			<label for="number">Enter Number</label>
			<input type="text" value="" id="number" name="number" placeholder="+" class="input form-control mtop5">                                                 
		</div>
      <button id="btn-continue-step1" class="btn btn-lg btn-primary pull-right"><!--h3-->Continue <i class="fa fa-angle-right"></i><!--/h3--></button>
	
    </div><!-- End of getstarted-formbox -->
                                    
	<div class="clearfix"></div>
    </div><!-- End of get-started-white div tag -->
    <div class="clearfix"></div>                                  
                            </div>
                            </div>
    </div>
    
    </div>
    
	<?php include 'inc_free_uk_page.php' ;?>
    <?php include 'inc_footer_page.php' ;?>

	<?php include 'inc_hidden_forms.php';?>
	
	<?php include 'js/get_number_scripts.js.php';?>
	</body>
</html>
