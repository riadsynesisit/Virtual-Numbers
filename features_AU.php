<!doctype html>
<?php 
if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
	if(isset($_SERVER["REQUEST_URI"]) && ($_SERVER["REQUEST_URI"]=="/features_AU.php" || $_SERVER["REQUEST_URI"]=="/index.php?page=features_AU" || $_SERVER["REQUEST_URI"]=="/features_AU")){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: features/");
		exit;
	}
	
}
require("./dashboard/classes/Did_Cities.class.php");
$did_cities = new Did_Cities();
if(is_object($did_cities)==false){
  	die("Failed to create Did_Cities object.");
}

require("./dashboard/classes/SystemConfigOptions.class.php");
$sco = new System_Config_Options();
if(is_object($sco)==false){
  	die("Failed to create System_Config_Options object.");
}
$cheapest_city = $sco->getConfigOption('cheapest_did_city');

$sel_did_country_code = $did_cities->getCheapestCountry($cheapest_city);

?>
<html>
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="rating" content="general" />
        <meta name="Language" content="en" />
        <meta name="robots" content="all" />
        <meta name="robots" content="index,follow" />
        <meta name="re-visit" content="7 days" />
        <meta name="distribution" content="global" />    
		<title>Free Australian Number, Free Australian Virtual Phone Number | Sticky Numbers Australia </title>
        <meta name="description" content="Stickynumber.com.au is a leading company provides free Australian number, Free Australian Virtual phone numbers with guarantee of best performance and reliability our free range disposal. For more information visit us online!"/>
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->		
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<!-- Adding "maximum-scale=1" fixes the Mobile Safari auto-zoom bug: http://filamentgroup.com/examples/iosScaleBug/ -->
        
        <!-- CSS STYLING -->        
        <link rel="stylesheet" media="all" href="css/core.css"/>
        <link rel="stylesheet" media="all" href="css/misc.css"/>
        
        <!-- JS -->
        <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
        <script src="js/jquery.slides.min.js"></script>
        <script src="js/scrolltopcontrol.js"></script>
        <script src="js/jquery-ui-1.10.3.custom.min.js"></script>
        
         <!-- SlidesJS Required: Initialize SlidesJS with a jQuery doc ready -->
		  <script>
		  var country_iso = "";
		  var plan_period = 'M';//Hard coded until the annual payment is enabled in the system
		  var choosen_plan = 0;
		  var pay_amount = 0;
		  var purchase_type = 'new_did';

            $(document).ready(function(){
                
            	$("#submit_form_news").click(function(){
            		var emailid = document.getElementById("email_news").value;
            		var submit_url = "./mcapi/inc/store-address.php?ajax=1&email="+emailid;
            		  $.ajax({url:submit_url, success:function(result){
            		    alert(result);
            		  }});
            	});
                    
                $('#did_country').change(function() {
                    country_iso = $(this).val();

                    $('#div-free-trials').hide();
                    
                    $('#city option').remove();
                    if (country_iso == '') {
                        $('#city').append('<option value="">Please select a city</option');
                        return true;                        
                    }
                    
                    $('#city').append('<option value="">Loading...</option');
                    $('#city').attr('disabled', 'disabled');
                    
                    var url = './ajax/ajax_load_did_cities.php';
                
                    $.get(url, {'country_iso' : country_iso}, function(response){
                        var data = jQuery.parseJSON(response);  
                        $('#city').removeAttr('disabled');
                        $('#city option').remove();
                        $('#city').append('<option value="">Please select a city</option'); 
                        $(data).each(function(index, item) {
                            $('#city').append('<option value="'+$.trim(item['city_id'])+'">'+item['city_name']+' '+item['city_prefix']+'</option>');
                        });
                    });    
                    
                }); //on did_country change

                <?php 
                	if(trim($sel_did_country_code)!=""){
                ?>
                		 $('#did_country').change();
                		 setTimeout(function(){//$('select[name="city"]').find('option[value="<?php echo $sel_did_city_code; ?>"]').attr("selected",true);
                		 						$('#city option[value=<?php echo $cheapest_city; ?>]').attr('selected', true);$('#div-free-trials').show();},1800);
                <?php		
                	}//end of if(trim($sel_did_country_code)!=""){
                ?>                

                $('#city').change(function() {
					if($('#city').val()==99999){
						$('#div-free-trials').show();
					}else{
						$('#div-free-trials').hide();
					}
				});
                
                $('#country').change(function() {
                	$('#number').focus();
                    $('#number').val( '+' + $(this).val())
                }); //on country change
                
                $('#number').keyup(function(event) {
                        var val = $(this).val();
                        if (val.length == 0) {
                            $(this).val('+')
                            return true;
                        }
                        
                        if (val[0] != '+') {
                            val = '+' + val;
                        }
                        
                        var tmp = val;
                        
                        var last_char = val[ val.length -1 ]
                        var char_code = val.charCodeAt(val.length - 1) 
                        if ((char_code < 48 || char_code > 57)) {
                            tmp = '';
                            for(var i=0; i < val.length - 1; i++) {
                                tmp += val[i]         
                            }
                                 
                        } 
                        
                        $(this).val( tmp )
                        
                        var option_value = tmp.replace('+','')
                        
                        if ($("#country option[value='" + option_value + "']").length > 0) {
                            $('#country').val( option_value )
                        }  
                        
                });

                $('#btn-continue-step1').mousedown(function(event) {
                    if(validate_step1()==true){
                    	$('#fwd_cntry').val($('#country').val());
						$('#forwarding_number').val($('#number').val());
						$('#did_country_code').val($("#did_country").val());
						$('#did_city_code').val($("#city").val());
						$('#vnum_country').val($("#did_country option:selected").text());
						$('#vnum_city').val($("#city option:selected").text());
						$('#frm-step1').submit();
                    }
                });
            });

			function validate_step1(){
				if($('#did_country').val() == ""){
					alert("Please select a country for your virtual number.");
					$('#did_country').focus();
					return false;
				}
				if($('#city').val() == ""){
					alert("Please select a city for your virtual number.");
					$('#city').focus();
					return false;
				}
				if($('#country').val() == ""){
					alert("Please select call forwarding country.");
					return false;
				}
				//first clean up destination number
				var dest = $('#number').val();
				if(dest.length == 0){
					$('#number').val('+');
					dest = $('#number').val();
				}
				if(dest.charCodeAt(0) != 43){
					$('#number').val('+'+dest);
					dest = $('#number').val();
				}
				var clean_dest = '+';
				for(var i=1;i<=dest.length;i++){
					if(dest.charCodeAt(i)<48 || dest.charCodeAt(i)>57){
						//dont'take this character.
					}else{
						clean_dest += dest.charAt(i);
					}
				}
				$('#number').val(clean_dest);
				//end of clenup destination number
				if($('#number').val().length <= 10){
					alert("Please enter call forwarding number with area code (min 10 digits)");
					$('#number').focus();
					//The following three lines set cursor at end of existing input value
					var tmpstr = $('#number').val();//Put val in a temp variable
                    $('#number').val('');//Blank out the input
                    $('#number').val(tmpstr);//Refill using temp variable value
					return false;
				}
				return true;
			}//end of function validate_step1

          </script>
          <!-- End SlidesJS Required -->
        
        <!--Start of Zopim Live Chat Script-->
		<script type="text/javascript">
		window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
		d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
		_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
		$.src='//v2.zopim.com/?1piIDUavtFoOMB89h2pj9OK8cT8CswZK';z.t=+new Date;$.
		type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
		</script>
		<!--End of Zopim Live Chat Script-->
        
	</head>
	
	<body lang="en">
        
        <!-- Top Bar --->
	    <div class="topbar">
	         <!-- content -->
	         <div class="wrapper">
	              <div class="container">
	                  <div class="info"><span class="Mtop2"><img src="images/mail.png"/></span>&nbsp;<span class="Mleft5">support@stickynumber.com.au</span></div>
	                  <div class="logintop">
	                  		<?php if(isset($_SESSION['user_logins_id']) && trim($_SESSION['user_logins_id'])!=""){ ?>
	                  			Hi <?php echo $_SESSION['user_name']; ?>&nbsp;<a href="dashboard/index.php">My Account</a>
                                		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="dashboard/logout.php">Logout</a>
	                  		<?php }else{ ?>
	                  			<?php if(isset($_GET['error']) && $_GET['error']=='failed' ) { ?> <div style='color:red;'>Member details incorrect,try again.</div><?php } ?>
		                       	<form name="toplogin" id="toplogin" action="dashboard/login.php" method="post">
		                       	<div class="login-input"><input name="email" type="text" class="input" placeholder="Username"/></div>
		                       	<div class="login-input"><input name="password" type="password" class="input" placeholder="Password"/><a href="index.php?page=forgot_password_AU">Forgot Password</a></div>
		                       	<div class="loginbtn-box"><input onclick="javascript:document.getElementById('toplogin').submit();" type="button" class="loginbtn" value="Login"/></div>
		                       	<input type="hidden" name="site_country" value="AU" />
                                <input type="hidden" name="front_page_login" value="2926354" id="front_page_login">
		                       	</form>
		                    <?php } //End of else part of no login session?>
	                  </div>
	              </div>
	         </div>
	         <!-- content closed -->         
	    </div>
	    <!-- Top Bar Closed --->
	    
	    <?php require "menu_and_logo.php"; ?>

<?php

require("./dashboard/classes/Did_Countries.class.php");
require("./dashboard/classes/Country.class.php");
$did_countries_obj = new Did_Countries();
$did_countries = $did_countries_obj->getAvailableCountries();

$country = new Country();
$countries = $country->getAllCountry();

?>       

	<!-- Banner -->
    <div class="wrapper">
         <div class="container Mtop40">
              <img src="images/SF-Features.jpg" width="1024" height="201" alt="Features">
         </div>
    </div>
    <!-- Banner Closed -->

    <!-- Main wrapper starts here -->
    <div class="wrapper">
         <div class="container">         
              <div class="row Mtop20">
                   <!-- Left -->
                   <div class="contact-leftbox">
                     <div class="heading">Features</div>
                     
                        <div class="row Mtop20 f14"> 
                        Protect your identity online by using a virtual Australian number while advertising an ad online, in a newspaper, signing up to a website, selling a car, or making overseas business enquiries while maintaining a Australian presence without displaying your real phone number. Furthermore to protecting your identity will not divulge any personal identifiable information with other company's or publically. Below is a list of features we include in our Australian numbering system.<br/><br/>
                        </div>
                        
                        <div class="feature-list Mtop20">
                            <ul>
                               <li>24/7 access to your online control panel</li>
                               <li>Unlimited phone number routing updates</li>
                               <li>Cancel Anytime</li>
                               <li>View call records</li>
                               <li>View voicemail notification</li>
                            </ul>
                        </div>
                   </div><!-- Left box end --.
                   
                   <!-- Right -->
                   <div class="contact-rightbox">
                        <div class="row">
                             <div class="get-started-box">
                            	<div class="get-started-white">
                            		<!--<div class="free-trials-2" id="div-free-trials"><img src="images/free-trials.png" width="175" height="97" alt="Free Trials No Restiction"></div>-->
                                	<div class="getstarted-btn-box"><div class="getstarted-btn">Get Started</div></div>
                                	<div class="getstarted-formbox">
                                		<div class="row">Please Select A Number :</div>
                                		<div class="row">
                                			<select class="selectP5" name="did_country" id="did_country">
	                                           <option value="">Please select a country</option>
	                                           <?php foreach($did_countries as $did_country) { ?>
	                                            <option value="<?php echo trim($did_country["country_iso"]); ?>" <?php if(trim($sel_did_country_code)!="" && trim($sel_did_country_code)==trim($did_country["country_iso"])){echo " selected "; } ?>><?php echo $did_country["country_name"] . ' ' . $did_country["country_prefix"] ?></option>
	                                           <?php } ?>
	                                      </select>
                                		</div>
                                		<div class="row">
                                			<select class="selectP5" name="city" id="city">
	                                           <option value="">Please select a city</option>
	                                      </select>
                                		</div>
                                		<div class="row">Please Enter Your Forwarding Number :</div>
                                		<div class="row">
                                			<select class="selectP5" name="country" id="country">
	                                           <option value="">Please Select Forwarding Country</option>
	                                           <?php foreach($countries as $country) { ?>
	                                                <option value="<?php echo trim($country['country_code']); ?>" <?php if(trim($sel_fwd_cntry) != "" && trim($sel_fwd_cntry)==trim($country["country_code"])){echo " selected "; } ?>><?php echo $country['printable_name'] . '+' . $country['country_code'] ?></option>
	                                           <?php } ?>
	                                           
	                                      </select>
                                		</div>
                                		<div class="row">
                                			<input type="text" class="input" placeholder="+" name="number" id="number" value="<?php echo $forwarding_number; ?>" />
                                		</div>
                                		<div class="row"><a class="slidesjs-navigation continue-btn" id="btn-continue-step1">Continue</a></div>
                                	</div><!-- End of getstarted-formbox -->
                                </div><!-- End of get-started-white div tag -->
                            </div><!-- End of get-started-box div tag -->
                        </div>
                        
                         
                       <!-- Support -->
                       <div class="row relative">
                            <div class="heading">24/7 Support</div>                            
                            <div class="support-help"></div>                            
                       </div>
                       <!-- Support end-->
                       <div class="row Mtop150">
                       <!-- List -->
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
                       </div>
                        
                   </div>
                   
              </div>
         </div>
    </div> 
    <!-- Main wrapper starts here Closed -->
       
       <!-- Main Content starts here -->
    <div class="wrapper">
         <div class="container">         
              <div class="whychoose">
                   <div class="row text-center"><img src="images/whychoose-icon.jpg"/></div>
                   <div class="whychoose-text">Why Choose Sticky Number</div>  
              </div>              
              <div class="mid-content">
                    <div class="row Mtop20 font16 text-center grey">
                    	With your FREE Sticky Number you can receive, record, manage and view call records from the comfort of any computer, tablet or smart phone in the world! You can get a FREE local or Toll Free online phone number from over 1220 cities in over 60 countries and have it forward to just about any phone in the world. We guarantee best performance and reliability using our global VOIP network all at industry best pricing.
                    	<br><br>
						Sign up now to instantly receive a FREE trial number with no limitations!
                    </div>
                    <div class="row Mtop65">
                         <div class="mid-contentbox">
                             <div class="row text-center"><img src="images/signupwithus.jpg"/></div>
                             <div class="mid-contentbox-title">Sign up with us</div>
                         </div>
                         <div class="mid-contentarrow"><img src="images/arrow-down.jpg"/></div>
                         <div class="mid-contentbox">
                             <div class="row text-center"><img src="images/choose-aus-no.jpg"/></div>
                             <div class="mid-contentbox-title">Choose Online Number</div>
                         </div>
                         <div class="mid-contentarrow"><img src="images/arrow-up.jpg"/></div>
                         <div class="mid-contentbox">
                             <div class="row text-center"><img src="images/divert-phone.jpg"/></div>
                             <div class="mid-contentbox-title">Divert Phone Number</div>
                         </div>
                    </div>
              
              </div>
         </div>
    </div> 
    <!-- Main Content starts here Closed -->
       
       <form method="post" action="<?php echo SITE_URL?>index.php?choose_plan" name="frm-step1" id="frm-step1">
            <input type="hidden" value="" name="did_country_code" id="did_country_code">
            <input type="hidden" value="" name="did_city_code" id="did_city_code">
            <input type="hidden" value="" name="fwd_cntry" id="fwd_cntry">
            <input type="hidden" value="" name="forwarding_number" id="forwarding_number">
            <input type="hidden" value="" name="vnum_country" id="vnum_country">
            <input type="hidden" value="" name="vnum_city" id="vnum_city">
            <input type="hidden" value="choose_plan.php" name="next_page" id="next_page">
       </form>