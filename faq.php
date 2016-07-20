<?php

if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
	if(isset($_SERVER["REQUEST_URI"]) && ($_SERVER["REQUEST_URI"]=="/index.php?page=faq" || $_SERVER["REQUEST_URI"]=="/faq")){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: faq/");
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
	
	<?php include 'get_number_sql_data.php'; ?>
	
    <div class="container">
    <img width="1170" height="230" class="img-responsive" alt="Our Pricing" src="<?php echo SITE_URL;?>images/SF-FAQs.jpg">
    </div><!-- /.container -->
<div class="container mtop50">
    <div class="row">
      <div class="col-md-8">
<h2 class="convert_h1_h2">Frequently asked questions</h2>
  Below are FAQ's about Sticky Number, our system and the services we provide.
If you do not find the answer you are looking for, then please do not hesitate to contact us 24/7 via email: <a href="mailto:support@stickynumber.com">support@stickynumber.com.au</a> <br><br>

                        <strong class="text-primary">Who is Sticky Number?</strong><br><br>
                        
                        Sticky Number is a leading global online phone forwarding service provider. Using our vast network of servers and systems we are able to offer the best numbers, running on the best, most reliable and feature packed service available.<br><br>
We enable you to get hold of your very own local or toll free phone number and have it forward to any phone, practically anywhere in the world. This means you can take your business, service or product global in just a matter of minutes. It's never been this easy to set up a global virtual office.<br><br>
The team at sticky number know it doesn't just stop at easy to use, low cost, feature full  phone forwarding service. Our entire team is dedicated to you and ensuring you have the simplest, smoothest business experience available.<br><br>

                        <strong class="text-primary">What Numbers can I get?</strong><br><br>
Sticky Number has one of the biggest range of worldwide local and toll free numbers available. We currently provide numbers and call forwarding for and to over 1220 cities and 60 countries globally.<br><br>
Check out the sign up form to the right of screen. If you don't see the country or city you're looking for, email us at support@stickynumber.com.au and we'll explore our network in effort to fill your needs.<br><br>

                        <strong class="text-primary">How do I sign up?</strong><br><br>
It's never been easier, simply fill out the registration form to the right and you'll have your brand new phone number in just minutes.<br><br>
Not convinced that Sticky Number is the best choice for your new phone number? Sign up for a FREE unrestricted trial now!<br><br>
                        
          <strong class="text-primary">How do I login?</strong><br><br>
                        
                       To manage your Online Number, log in to your account:<br><br>
                        
                        1. Go to the top of any page<br>
                        2. Type your email and password into the fields provided<br>
                        2. Click Login and you'll be managing your numbers in seconds.<br><br>
                        
                        <strong class="text-primary">How do I add another virtual UK number?</strong><br><br>
                        
                        To add another number, log in to your account:<br><br>
                        
1. Simply log in to your account<br>
2. Click on "Add New Number"<br>
3. Fill out the form selecting your desired number and your destination number.<br>
4. Click Continue<br>
5. Follow the remainder of the form and you'll be up and running with your additional numbers is just minutes.<br><br>

<strong class="text-primary">What is call forwarding?</strong><br><br>
Ans: This is a process that enables you to have a phone number that is local to your customers, and one by which they can call you as if they were calling a friend at their location rather than dialing internationally.<br><br>

<strong class="text-primary">Can my callers determine if I am in the location they are calling?</strong><br><br>
Ans:  Your callers will only know if you tell them.<br><br>

<strong class="text-primary">Do I get charged if I use more than my monthly minutes?</strong><br><br>

Ans: When you minutes run low we will notify you. Once your monthly minutes are exhausted, the number will stop working. At this point you can top up your credit. You will find the cost per minute in your control panel/Member Account.<br><br>

<strong class="text-primary">Do you accept payment via wire transfer?</strong><br><br>
Ans: Not at this time. We accept Visa, MasterCard, AMEX and PayPal payments.<br><br>

<strong class="text-primary">How many phone numbers can I have for my account?</strong><br><br>
Ans:  You can have as many numbers as you want. Simply log into your Member Account and click “Add New Number”.<br><br>

<strong class="text-primary">How long before I can start using my phone number?</strong><br><br>
Ans:  Your number is instantly activated and you can start using it straight away.

<strong class="text-primary">How do I know my monthly pricing?</strong><br><br>
Ans: You can see your monthly charges by logging into your online control panel or Member Account, under the heading “Billing/Invoices”.<br><br>

<strong class="text-primary">Who do I contact directly regarding technical support?</strong><br><br>

Ans: You may ring us on 0291 192 986 if you are in Australia or +61 291 192 986 if you are outside of Australia. You may also email support@stickynumber.com.au<br><br>

<strong class="text-primary">Are your services for anyone you wants it or just for some types of businesses?</strong><br><br>
Ans: We serve anyone wanting our service.<br><br>

<strong class="text-primary">Is the contract monthly or yearly or do I have a choice between both?</strong><br><br>
Ans:  You have the option to sign up for a Month or a Year.<br><br>
<h2 class="convert_h1_h2">Free UK Number FAQ</h2>
<p>&nbsp;</p>
Below are FAQ's about our system and the services we provide. If you do not find the answer you are looking for, then please do not hesitate to contact us 24/7 via email: <a href="mailto:support@stickynumber.com.au">support@stickynumber.com.au</a>
<br><br>
<strong class="text-primary">How do I add another virtual UK number?</strong><br><br>
To add another number, log in to your account:<br><br>

1. Log in to your Sticky Number account<br />
2. Click on “Add New Number”<br />
3. Enter your destination number<br />
4. Click “Continue”<br />
5. Done<br><br>
<strong class="text-primary">Why do I have to call my number every 3 months?</strong><br><br>

We added a policy to remove out dated or unused phone numbers giving you the latest in the UK numbering service. We also added an additional 7 day policy to confirm you are wishing to use the 070 number, if either policy is not met the virtual UK number is released back into the UK telecommunications network. <br><br>

<strong class="text-primary">Do I have to pay any fee’s?</strong><br><br>
Sticky Number do not charge any fee’s to use our 070 free UK numbers. We are here to push forward the progression of international telecommunications and make every effort to provide high quality service.<br><br>

<strong class="text-primary">Do you have an example of the virtual number use?</strong><br><br>
You have the ability to provide a free uk number that forwards to yourself, once the business or personal transaction is complete you can easily re-route the number 24/7 through our control panel or add a brand new 070 number to your account.<br><br>
<p>&nbsp;</p>

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
			<label for="city">Select Your City:</label>
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
      <button id="btn-continue-step1" class="btn btn-lg btn-primary pull-right">Continue <i class="fa fa-angle-right"></i></button>
	<!--/form-->
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
