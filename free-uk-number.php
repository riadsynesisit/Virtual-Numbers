<!doctype html>
<?php
//This page is reachable from about-us URL
if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
	if(isset($_SERVER["REQUEST_URI"]) && ($_SERVER["REQUEST_URI"]=="/free-uk-number.php" || $_SERVER["REQUEST_URI"]== "/landing.php" )){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: free-uk-number/");
		exit;
	}
	
}

$this_page = "free-uk-number";

//require("./dashboard/includes/config.inc.php");
require("./dashboard/classes/Country.class.php");
require("./dashboard/classes/Unassigned_DID.class.php");

$country = new Country();
$countries = $country->getAllCountry();

$unassDids = new Unassigned_DID();
$uDids = $unassDids->retUnassigned_DIDs() ;

require "landing_header.php";

?>

		<!-- Banner -->
	    
	    <!-- Banner Closed -->
	    
	    <!-- Main Content starts here -->
    
	<div class="clearfix"></div>
	<section class="banner">
<div class="container">
<div class="row">
  <div class="col-md-8">
  <div class="banner-left mtop20">
  <h1 class="fadeInLeft">FREE UK Number</h1>
  <h1 class="fadeInUp">FREE International Forwarding</h1>
  <a href="#"><img src="<?php echo SITE_URL;?>images/register-now.png" width="270" height="72" alt="Register Now" class="mtop20 bounceIn"></a> </div>
  </div>
  <div class="col-md-4">
    <div class="signup-box-wrap fadeInDown">
      <div class="signup-box">
        <h1><img src="<?php echo SITE_URL;?>images/sign-up.png" width="32" height="32" alt="Sign Up">Sign Up for Free</h1>
        <div class="signup-box-form">
<form class="form-horizontal" role="form">

  <div class="form-group">
    <div class="col-sm-12">
		<select class="form-control" name="udids" id="udids">
			<!--option value="">Please Select a Free Number</option-->
			<!--option value="99999" selected="">Free UK Number 70 ($0.00/month)</option-->
			<?php foreach($uDids as $udid) { ?>
				<option value="<?php echo trim($udid['id']); ?>" ><?php echo '(+44) ' . $udid['did_number'] ; ?></option>
		<?php } ?>
		</select>
	</div>
  </div>
  
  <div class="form-group">
    <div class="col-sm-12 mtop10">
		<select class="form-control" name="fwd_country" id="fwd_country">
			<option value="">Please Select Forwarding Country</option>
		<?php foreach($countries as $country) { ?>
				<option value="<?php echo trim($country['country_code']); ?>" <?php if(trim($sel_fwd_cntry) != "" && trim($sel_fwd_cntry)==trim($country["country_code"])){echo " selected "; } ?>><?php echo $country['printable_name'] . '+' . $country['country_code'] ?></option>
		<?php } ?>
		</select>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-12 mtop10">
      <input type="text" class="form-control" placeholder="+" name="number" id="number" value="">
    </div>
  </div> 
  <div class="form-group">
    <div class="col-sm-12 mtop10">
      <input type="text" id="txtName" class="form-control" placeholder="Name" value="">
    </div>
  </div>  
  <div class="form-group">
    <div class="col-sm-12 mtop10">
      <input type="text" id="txtEmail" class="form-control" placeholder="Email" value="">
    </div>
  </div>
    <div class="form-group">
    <div class="col-sm-12 mtop10">
      <input type="password" id="pword" class="form-control" placeholder="Password" value="">
    </div>
  </div>  
	
  <div class="form-group" id="captcha" style="display:none">
    <input type="text" name="real_person" id="real_person" />
  </div>
  
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="button" class="signme-btn btnActivateFree"><strong>Activate</strong></button>
    </div>
  </div>
  
  
  
</form>        
        </div>
  
  </div>
  
      <div class="form-shadow"><img src="<?php echo SITE_URL;?>images/shadow.png" width="290" height="26" alt="Form register"></div>
    </div>
  </div>
</div>
</section>																						

<section class="main-content">
<div class="container">
<div class="clear_both">&nbsp;</div>
<div class="text-center mtop10"><br/><img src="<?php echo SITE_URL;?>images/whychoose-icon.jpg"></div>
<h1 class="text-center">Why Stickynumber</h1>
<img src="<?php echo SITE_URL;?>images/separator.png" width="106" height="4" class="mtop20 centerImage">
<div class="well well-lg mtop30">
<div class="countries">
<h2><img src="<?php echo SITE_URL;?>images/UK.png" width="21" height="15"> Free 4470 UK number forwarding</h2>
<table class="table">
      <thead>
        <tr>
          <th>Free UK Number</th>
          <th>Country</th>
          <th>Country Code</th>
          <th>Landline/Mobile</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>(+44)  70XXXXXXXXXX</td>
          <td><img src="<?php echo SITE_URL;?>images/Nigeria.png" width="21" height="15"> Nigeria</td>
          <td>+234</td>
          <td>Yes / Yes</td>
        </tr>
        <tr>
          <td>(+44)  70XXXXXXXXXX</td>
          <td><img src="<?php echo SITE_URL;?>images/Malaysia.png" width="21" height="15" alt="Malaysia"> Malaysia</td>
          <td>+60</td>
          <td>Yes / <a href="#">Enquire</a></td>
        </tr>
        <tr>
          <td>(+44)  70XXXXXXXXXX</td>
          <td><img src="<?php echo SITE_URL;?>images/SouthAfrica.png" width="21" height="15" alt="South Africa"> South Africa</td>
          <td>+27</td>
          <td>Yes / Yes</td>
        </tr>
        <tr>
          <td>(+44)  70XXXXXXXXXX</td>
          <td><img src="<?php echo SITE_URL;?>images/India.png" width="21" height="15" alt="India"> India</td>
          <td>+91</td>
          <td>Yes / Yes</td>
        </tr>
        <tr>
          <td>(+44)  70XXXXXXXXXX</td>
          <td><img src="<?php echo SITE_URL;?>images/Qatar.png" width="21" height="15" alt="Qatar"> Qatar</td>
          <td>+974</td>
          <td>Yes / Yes</td>
        </tr>
        <tr>
          <td>(+44)  70XXXXXXXXXX</td>
          <td><img src="<?php echo SITE_URL;?>images/TheNetherlands.png" width="21" height="15" alt="The Netherlands">The Netherlands</td>
          <td>+31</td>
          <td>Yes / Yes</td>
        </tr>
        <tr>
          <td>(+44)  70XXXXXXXXXX</td>
          <td><img src="<?php echo SITE_URL;?>images/Europe.png" width="21" height="15" alt="Europe"> Europe</td>
          <td>+</td>
          <td><a href="#">Enquire</a></td>
        </tr>
        <tr>
          <td>(+44)  70XXXXXXXXXX</td>
          <td><strong>Worldwide</strong></td>
          <td>+</td>
          <td><a href="#">Enquire</a></td>
        </tr>                                        
      </tbody>
    </table>
    </div>
</div>
<div class="desc-text mtop20">Sticky Number is a free UK number supplier, we guaranteed the best performance & reliability at our disposal. Our client base expands across the globe, reaching 100,000's of users daily. All 070 free UK numbers come with international forwarding.</div>
<h2 class="text-center mtop30">3 Easy Steps</h2>
<img src="<?php echo SITE_URL;?>images/3-steps.png" width="942" height="148" class="centerImage">
<a href="#"><img src="<?php echo SITE_URL;?>images/button-2.png" width="212" height="58" alt="Sign Up Now" class="centerImage mtop30"></a> </div>
</section>
<section class="second-footer">
<div class="container mtop30">
<div class="text-center mtop10"><img src="<?php echo SITE_URL;?>images/whatsnew-icon.jpg"></div>
<h1 class="text-center">What's New</h1>
<img src="<?php echo SITE_URL;?>images/separator.png" width="106" height="4" class="mtop20 centerImage">
<div class="row mtop30">
            <div class="col-md-4">
<div class="sf-one">
                           <div class="sf-head">
                              <div class="sf-icon"><img src="<?php echo SITE_URL;?>images/range.jpg"></div>
                              <div class="sf-header">No Setup Fees</div>
                           </div>
                           <div class="sf-body">
                          We improved our system to automate the online number setup letting us waive setup fees.
                           </div>
              </div>
      </div>
            <div class="col-md-4">
<div class="sf-one">
                           <div class="sf-head">
                              <div class="sf-icon"><img src="<?php echo SITE_URL;?>images/net-divert.jpg"></div>
                              <div class="sf-header">Free International Divert 24/7</div>
                           </div>
                           <div class="sf-body">All online numbers can be routed to thousands of destinations around the world.</div>
              </div>
            </div>
            <div class="col-md-4">
<div class="sf-one">
                           <div class="sf-head">
                              <div class="sf-icon"><img src="<?php echo SITE_URL;?>images/worldwide.jpg"></div>
                              <div class="sf-header">Worldwide Instant Activation</div>
                           </div>
                           <div class="sf-body">All accounts are now activated in real time giving you an instant online number.</div>
              </div>
            </div>
            <div class="clearfix"></div>  
    </div>      
</div>
</section>                     
<section class="footer">
<div class="container">
<div class="row">
  <div class="col-md-8">
	<p class="footer-credit">
		© 2011 Sticky Number - <a href="#">Free UK Number</a>, All rights reserved – Sticky Number® | <a href="#">Terms of Service</a> & <a href="#">Privacy Policy</a>
	</p>
  </div>
  <div class="col-md-4">
	<div class="footer-social">
	   <!--div class="footer-social-items text-right"><img src="<?php echo SITE_URL;?>images/facebook.png"></div>
	   <div class="footer-social-items"><img src="<?php echo SITE_URL;?>images/googleplus.png"></div>
	   <div class="footer-social-items"><img src="<?php echo SITE_URL;?>images/youtube.png"></div>
	   <div class="footer-social-items"><img src="<?php echo SITE_URL;?>images/twitter.png"></div-->
	   <div class="footer-social-items text-right"><a target="_new" href="https://www.facebook.com/stickynumber"><img alt="facebook" src="<?php echo SITE_URL; ?>images/facebook.png"/></a></div>
	   <div class="footer-social-items"><a target="_new" href="https://plus.google.com/b/113258218112686629360/113258218112686629360/posts"><img alt="google plus" src="<?php echo SITE_URL; ?>images/googleplus.png"/></a></div>
	   <div class="footer-social-items"><a target="_new" href="http://www.linkedin.com/company/sticky-number" ><img alt="Linked In" src="<?php echo SITE_URL; ?>images/linkedin.png" /></a></div>
	   <div class="footer-social-items"><a target="_new" href="https://twitter.com/stickynumber"><img alt="twitter" src="<?php echo SITE_URL; ?>images/twitter.png"/></a></div>
</div>  
  </div>
</div>
</div>
</section>	
<form method="post" action="<?php echo SITE_URL?>index.php?free_number" name="frm-free-num-070" id="frm-free-num-070">
	<input type="hidden" value="GB" name="did_country_code" id="free_number_did_country_code">
	<input type="hidden" value="99999" name="did_free_city_code" id="free_number_did_city_code">
	<input type="hidden" value="" name="fwd_cntry" id="free_number_fwd_cntry">
	<input type="hidden" value="1" name="forwarding_type" id="free_number_forwarding_type">
	<input type="hidden" value="" name="forwarding_number" id="free_number_forwarding_number">
	<input type="hidden" value="" name="free_number_id" id="free_number_id">
	<input type="hidden" value="United Kingdom 44" name="vnum_country" id="free_number_vnum_country">
	<input type="hidden" value="Free UK Number 70 (£0.00/month)" name="vnum_city" id="free_number_vnum_city">
	<input type="hidden" value="new" name="new_or_current" id="free_number_new_or_current">
	<input type="hidden" value="P" name="personal_or_business" id="free_number_personal_or_business">
	<input type="hidden" value="free_number_070_AU.php" name="next_page" id="free_number_next_page">
</form>																							
	