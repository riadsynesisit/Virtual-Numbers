<?php 
	if(isset($_SERVER["HTTP_REFERER"]) && substr($_SERVER["HTTP_HOST"],-19)!="stickynumber.com.au" && trim($_SERVER["HTTP_REFERER"])!="" && strpos($_SERVER["HTTP_REFERER"],"google.com.au")!==false && strpos($_SERVER["HTTP_REFERER"],"rd=no")===false){
		if(strpos($_SERVER['QUERY_STRING'],"rd=no")===false){
			header("Location: https://www.stickynumber.com.au");
			exit;
		}
	}
	if(isset($_SERVER["HTTP_REFERER"]) && substr($_SERVER["HTTP_HOST"],-15)!="stickynumber.uk" && trim($_SERVER["HTTP_REFERER"])!="" && (strpos($_SERVER["HTTP_REFERER"],"google.co.uk")!==false || strpos($_SERVER["HTTP_REFERER"],"google.uk")!==false) && strpos($_SERVER["HTTP_REFERER"],"rd=no")===false){
		if(strpos($_SERVER['QUERY_STRING'],"rd=no")===false){
			header("Location: https://www.stickynumber.uk");
			exit;
		}
	}
?>
<?php //echo '<pre>';print_r($_SERVER); 
include 'inc_header_page.php'; ?>
</head>
 <body>
	<?php include 'login_top_page.php';?>
  
    <?php include 'top_nav_page.php';?>
    
	<?php include 'get_number_sql_data.php'; ?>
    <div class="bannerwrapper">   
    <div class="container">
    <div class="row">
    <div class="col-md-8">
		<div class="banner-slide-wrap">
			<div class="free-trials-badge"><img width="150" height="151" src="images/badge.png" class="img-responsive" alt="Free UK number"></div>
			<div class="banner-slide">
                       <h1 class="text-center">Virtual Numbers in Over 1200 Cities</h1>
                    <ul>
                        <li><img src="images/pick-number.png" width="601" height="354" alt="Pick Number" class="centeredImage"></li>
                        <li><img src="images/slide-2.png" width="601" height="353" alt="Slide 2" class="centeredImage"></li>
                        <li><img src="images/dial-brand.png" width="600" height="354" alt="Dial Brand" class="centeredImage"></li>
                        <li><img src="images/phone-rings.png" width="600" height="354" alt="Destination Phone Rings"></li>
                    </ul>
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
    <!--form-->
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
	<!--/form-->
                                	</div><!-- End of getstarted-formbox -->
                                    <div class="clearfix"></div>
                                </div><!-- End of get-started-white div tag -->
    <div class="clearfix"></div>                                  
                            </div>             
      </div>
    </div>                        
    </div>  
    </div>
    <div class="container">
    <div class="row">
      <div class="col-md-2">
    <div class="icon-services">
    <img src="images/voicetoemail.png" width="105" height="52" alt="Voice to Mail" class="centeredImage">
	<p class="text-center"><strong>Voice Mail to Email</strong></p>
    <p class="text-center">Our voice mail records your messages and emails them to you</p>
    </div>      
      </div>
      <div class="col-md-2">
<div class="icon-services">
        <img width="105" height="52" class="centeredImage" alt="Call Overflow" src="images/calloverflow.png">
<p class="text-center"><strong>Sticky Call Notice</strong></p>
    <p class="text-center">Get a beep before you talk so you know its a business call</p>
    </div>      
      </div>
      <div class="col-md-2">
         <div class="icon-services">
        <img src="images/callforwarding.png" width="105" height="52" alt="Call Forwarding" class="centeredImage">
<p class="text-center"><strong>Call Forwarding</strong></p>
    <p class="text-center">Receive your calls on your landline or mobile</p>
    </div>     
      </div>
      <div class="col-md-2">
        <div class="icon-services">
        <img src="images/livedata.png" width="105" height="52" alt="Live Data" class="centeredImage">
<p class="text-center"><strong>View Call Records</strong></p>
    <p class="text-center">Real time call records including date, time and duration</p>
    </div>      
      </div>
      <div class="col-md-2">
        <div class="icon-services">
    <img src="images/dayrouting.png" width="105" height="52" alt="Day Routing" class="centeredImage">
<p class="text-center"><strong>Day Forwarding</strong></p>
    <p class="text-center">Change where your call redirects to depending on your schedule</p>
    </div>      
      </div>
      <div class="col-md-2">
        <div class="icon-services">
     <img src="images/timerouting.png" width="105" height="52" alt="Time Routing" class="centeredImage">
<p class="text-center"><strong>Time Forwarding</strong></p>
    <p class="text-center">Set times when call goes to different forwarding numbers</p>
    </div>      
      </div>
    </div>    
    </div>
 <div id="choose-us-wrap">
    	<div class="choose-us-box">
        <img width="100" height="100" alt="Contact Us" src="images/contact-mid.png"> <h2>Signup with us</h2> 
		
        </div>
        <img width="61" height="14" class="curved-sep" alt="Curve" src="images/curve-separator.png">
<div class="choose-us-box">
        <img width="100" height="100" alt="Contact Us" src="images/free-num.png"> <h2>Select Phone Number City</h2>      
      </div>
              <img width="61" height="14" class="curved-sep" alt="Curve" src="images/curve-separator-2.png">
    	<div class="choose-us-box">
        <img width="100" height="100" alt="Contact Us" src="images/divert.png"> <h2>Forward Phone Number</h2>      
        </div>                
        <div class="clearfix"></div>
		
		
    </div>   
	
    <div class="container mtop50">
	<div class="row">
		<div class="choose-us-box-details">Signup with us and choose an exclusive local phone number in your country. Flexible plans with no cancellation fees. Stay in touch 24/7.</div>
		
		<div class="choose-us-box-details margin-left-6">When someone calls your number they just make a normal local call. You pay a monthly plan fee for your number and callers pay normal call rates.</div>
		
		<div class="choose-us-box-details margin-left-6 max-width-35">Now you have your sticky number. Share it with customers, friends and family. The number works just like any local number but reaches you anywhere.</div>
	</div>
	
	<div class="clearfix"><br/></div>
	
    <div class="row">
      <div class="col-md-7">
      <h2 class="convert_h1_h2">Why choose Sticky Number</h2>
<p class="text-justify">With your Sticky Number you can receive, record, manage and view call records from the comfort of any computer, tablet or smart phone in the world! You can get a local virtual phone number from over 1220 cities in over 60 countries and have it forward to just about any phone in the world. We aim to provide high performance and reliability using our global network. We also provide unlimited minutes with our <a href="free-uk-number/">free UK numbers</a>.</p>

<p class="text-justify">Here at Sticky Number we continue to add features to our systems so we can provide clients with all their virtual number needs. Our client base expands across the globe. All <a href="pricing/"><?php echo COUNTRY_DEMONYM;?>  virtual phone numbers</a> come with international forwarding, voice mail and IVR secure access. The Signup process is simple, choose a country and city then divert phone number free to most destinations around the world.</p>
      
	  <h2 class="convert_h1_h2">Top Local & International Call Rates</h2>
	  <p class="justify_text">Choose a country from the drop down box below and view rates from many locations around the world. Our phone number plans start at a low cost with low calling rates.</p>
	  <table cellspacing="2" class="tbl_home_pricing" border="0.4" >
		<tr>
			<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
		</tr>
		<tr>
			<td class="td_width_35">&nbsp;</td>
			<td>
				<select onchange="get_cheapest_rate_country(this.value);" id="cheap_did_country" name="cheap_did_country" class="form-control">
	            <option value="">Please select a country</option>
				<?php foreach($did_countries as $did_country) { ?>
				<option value="<?php echo trim($did_country["country_prefix"]); ?>" ><?php echo $did_country["country_name"] . ' ' . $did_country["country_prefix"] ?>
				</option>
				<?php } ?>
				</select>
			</td>
			<td>&nbsp;</td>
		</tr> 
		<tr>
			<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
		</tr>
		<tr class="tr_home_bg">
			<td>Country</td><td>Call Type</td> <td>Rate(per min)</td>
		</tr> 
		<tr>
			<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
		</tr>
		<tr class="tr_set_country_data">
			<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
		</tr>
	  </table>
      </div>
      <div class="col-md-5">
		<img width="493" height="348" src="images/why-c.jpg" class="pull-right img-responsive" alt="Why choose Us">
		<p class="justify_text">-Our number works on any normal phone, mobile and landline. Your callers wont need to remember any confusing codes or numbers to call you. Its the same as calling a local number.</p>
		<p class="justify_text">-The lowest pricing starts from $3.94. When you select your local country and are assigned an Australian phone number you upgrade or downgrade your subscription, even add one off credits. View our <a href="faq/">FAQ</a> for further information</p>
		<p class="justify_text">-No hidden charges and we provide short term monthly subscriptions. Alternatively for bigger discounts you can select yearly plan</p>
	  </div>
	    
	  
    </div>

<div class="alert alert-success mtop30 text-center" role="alert"><strong>24/7 Monitoring | Complete Easy Manangement Tools | <?php echo COUNTRY_DEMONYM;?> Number Supplier | Worldwide Phone Number Routing</strong></div>
<div class="panel-group" id="accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
        <span class="glyphicon glyphicon-plus"></span>
          How to forward virtual numbers?
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse">
      <div class="panel-body">
Diverting or forwarding a virtual number is a standard feature with most phone number suppliers. Where you are wanting to divert the number depends on the plan you should be choosing. With Sticky Number we have two types of forwarding, to either a physical mobile or landline number where we include calling minutes per month, the other option is SIP forwarding where you use another service to handle the outbound calling. The advantage with StickyNumber is we allow 24/7 access to updating the forwarding destination which isapplied immediately upon saving your new settings.
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
          <span class="glyphicon glyphicon-plus"></span>
          How to get a virtual phone number in <?php echo COUNTRY_DEMONYM;?>
        </a>
      </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse">
      <div class="panel-body">
When finding a virtual telephone number supplier make sure they are able to port numbers. Porting a number is the ability to move your new number between phone suppliers, if they are not able to port numbers you may have to sacrifice the number and have to choose a new number when you move to a new company. You should also see how long the company has been around and the features they provide, for example some suppliers require manual editing of the forwarding number or may not provide voice mail features. The process of buying a number is easy for most countries, all you have to do is signup as a member and choose a plan, there are however some destinations that require documentation, which includes having a local presence and identity proof.
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
          <span class="glyphicon glyphicon-plus"></span>
          What are virtual phone numbers?
        </a>
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse">
      <div class="panel-body">
A virtual number is a number not directly linked to physical telephone line, which means you can have several numbers from different locations all going to the same telephone line. Virtual numbers allow you to purchase a number from another city or country outside of your business location, if you live in Australia but have clients from USA you can buy a virtual number and forward the call to your Australian landline number. The benefit is reducing your clients costs to call your company from overseas. More technically speaking a virtual number can link to a PSTN or SIP network. Note that virtual number usage is subject to that countries regulations.
      </div>
    </div>
  </div>
<div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
          <span class="glyphicon glyphicon-plus"></span>
          Why get a 1300 or 1800 phone number?
        </a>
      </h4>
    </div>
    <div id="collapseFour" class="panel-collapse collapse">
      <div class="panel-body">
1800 numbers are normally referred to as toll free numbers (free phone) for the caller, however the business who
operates the number pays higher cost. 1300 numbers are normally referred to as a landline number due to clients calling 1300 numbers pay a local rate. Choosing
between the 1300 & 1800 numbers depends on your business model, whether you want to absorb more of the clients calling costs.      </div>
    </div>
  </div>  
</div>
	<div class="row">
		<h4 class="cus_comments">Customer Comments</h4>
		<div class="choose-us-box-details">"I must mention the customer service from sticky number has made the purchase process straight forward and easy to understand. Support has been extremely helpful. keep up the good work".<br/><br/> Philip C.<br/>Chief Executive Officer</div>
		
		<div class="choose-us-box-details margin-left-6">"Very impressed with the support and indepth features. They answered my calls within minutes and provided awesome customer service. You guys has high-tech system. I would recommend you".<br/><br/>Jane G.<br/>General Manager</div>
		
		<div class="choose-us-box-details margin-left-6 max-width-35">"I have found sticky number to be the best integrated telephone number service providing features to track calls and over the 2 years i have seen many constant updates and new tools being added".<br/><br/><br/>Oliver D.<br/>Marketing Director</div>
	</div>
</div><!-- /.container -->
    
<section class="gray-area">
  	<div class="container">
		<img src="images/whatsnew-icon.jpg" alt="What's New" class="centeredImage" height="30" width="50" />
		<h2 class="convert_h1_h2 text-center">What's New</h2>
            <div class="row mtop20">
              <div class="col-md-4">
              <h3><i class="fa fa-money"></i> No Setup Fees</h3>
              <p>We improved our system to automate the virtual number setup letting us waive setup fees.</p>
              </div>
              <div class="col-md-4">
              <h3><i class="fa fa-globe"></i> Free International Divert 24/7</h3>
              <p>All virtual numbers can be routed to thousands of destinations around the world.</p>
              </div>
              <div class="col-md-4">
              <h3><i class="fa fa-cogs"></i> Worldwide Instant Activation</h3>
              <p>All accounts are now activated in real time giving you an instant virtual number.</p>
              </div>
            </div>        
        </div>
    </section>
    
    <?php //include 'inc_footer_page.php' ;?>
    
    <script src="js/unslider.js"></script>
             <!-- SlidesJS Required: Initialize SlidesJS with a jQuery doc ready -->
	<?php include 'js/get_number_scripts.js.php';?>
		  <!-- End SlidesJS Required -->
          
          
	<?php include 'inc_hidden_forms.php';?>
	
	<?php include 'inc_footer_page.php';?>
	
  </body>
</html>
