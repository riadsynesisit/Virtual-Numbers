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
<!doctype html>
<?php 

$this_page = "home";

$sel_did_country_code = "";
$sel_did_city_code = "";
$sel_fwd_cntry = "";
$forwarding_number = "";
                	
if(isset($_POST["fwd_cntry"]) && trim($_POST["fwd_cntry"])!=""){
	$sel_fwd_cntry = trim($_POST["fwd_cntry"]);
}

if(isset($_POST["did_country_code"]) && trim($_POST["did_country_code"])!=""){
	$sel_did_country_code = trim($_POST["did_country_code"]);
}

if(isset($_POST["did_city_code"]) && trim($_POST["did_city_code"])!=""){
	$sel_did_city_code = trim($_POST["did_city_code"]);
}

if(isset($_POST["forwarding_number"]) && trim($_POST["forwarding_number"])!=""){
	$forwarding_number = trim($_POST["forwarding_number"]);
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
//$cheapest_city_au = $sco->getConfigOption('cheapest_did_city_au');
//$cheapest_city_gb = $sco->getConfigOption('cheapest_did_city_gb');
//$cheapest_city_us = $sco->getConfigOption('cheapest_did_city_us');

if($sel_did_country_code==""){
	$sel_did_country_code = $did_cities->getCheapestCountry($cheapest_city);
}

?>
<html lang="en">
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="rating" content="general" />
        <!--meta name="Lang" content="en" /-->
        <meta name="robots" content="all" />
        <meta name="robots" content="index,follow" />
        <!--meta name="re-visit" content="7 days" /-->
        <!--meta name="distribution" content="global" /-->
        <?php if(SITE_COUNTRY == "AU"){?> 
        <title>Virtual Phone Number Australia, Virtual Phone Numbers</title>   
        <meta name="description" content="Sticky Number provides Australian virtual phone numbers. International virtual phone number forwarding to landline and mobile telephones. Over 1200 cities."/>
		<?php }elseif(SITE_COUNTRY == "UK"){ ?>
		<title><?php echo COUNTRY_DEMONYM; ?> Number, <?php echo COUNTRY_DEMONYM; ?> Virtual Phone Number | Sticky Numbers <?php echo COUNTRY_NAME; ?> </title>
		<meta name="description" content="Sticky Number is a leading company provides United Kingdom numbers, United Kingdom Virtual phone numbers with guarantee of best performance and reliability. For more information visit us online!"/>
        <?php }else{ ?>
		<title><?php echo COUNTRY_DEMONYM; ?> Number, <?php echo COUNTRY_DEMONYM; ?> Virtual Phone Number | Sticky Numbers <?php echo COUNTRY_NAME; ?> </title>
        <meta name="description" content="Sticky Number is a leading company provides <?php echo COUNTRY_DEMONYM; ?> number, <?php echo COUNTRY_DEMONYM; ?> Virtual phone numbers with guarantee of best performance and reliability. For more information visit us online!"/>
		<?php } ?>
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->		
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<!-- Adding "maximum-scale=1" fixes the Mobile Safari auto-zoom bug: http://filamentgroup.com/examples/iosScaleBug/ -->
        
        <!-- CSS STYLING -->        
        <link rel="stylesheet" media="all" href="css/core.css"/>
        <link rel="stylesheet" media="all" href="css/misc.css"/>
        <link rel="shortcut icon" href="<?php echo SITE_URL; ?>favicon.ico" type="image/x-icon" />
		<link type="text/css" rel="stylesheet" href="<?php echo SITE_URL; ?>skins/minimal/minimal.css" />
        <!-- JS -->
        <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
        <script src="js/jquery.slides.min.js"></script>
        <script async src="js/scrolltopcontrol.js"></script>
        <script async src="js/jquery-ui-1.10.3.custom.min.js"></script>
        <script src="js/unslider.js"></script>
        
         <!-- SlidesJS Required: Initialize SlidesJS with a jQuery doc ready -->
		  <script>

		  var initial_load=false;
	      var city_selected = "";
		  
		  var country_iso = "";
		  var plan_period = 'M';//Hard coded until the annual payment is enabled in the system
		  var choosen_plan = 0;
		  var pay_amount = 0;
		  var purchase_type = 'new_did';

            $(document).ready(function(){

            	$('.banner-slide').unslider({
            		dots: true               //  Display dot navigation            //  
            	});
                
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
                    
                    var url = '<?php echo SITE_URL; ?>ajax/ajax_load_did_cities.php';
                
                    $.get(url, {'country_iso' : country_iso}, function(response){
                        var data = jQuery.parseJSON(response);  
                        $('#city').removeAttr('disabled');
                        $('#city option').remove();
                        $('#city').append('<option value="">Please select a city</option'); 
                        $(data).each(function(index, item) {
                        	city_selected = "";//reset
							if($.trim(item['city_id'])=="99999" && initial_load==true){
								initial_load = false;
								city_selected = " selected ";
							}
                            $('#city').append('<option value="'+$.trim(item['city_id'])+'" '+city_selected+' >'+item['city_name']+' '+item['city_prefix']+' (<?php echo CURRENCY_SYMBOL; ?>'+item['sticky_monthly_plan1']+'/month)</option>');
                        });
                    });    
                    
                }); //on did_country change

                <?php 
                	if(trim($sel_did_country_code)!=""){
                ?>
                	initial_load = true;
       		 		$('#did_country').change();
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
						if($("#city").val()==99999 || $("#city").val()=="99999"){
							<?php if(isset($_SESSION['user_logins_id']) && trim($_SESSION['user_logins_id']) != ""){?>
							$('#next_page').val('free_number_070_AU.php');
							$('#frm-step1').attr("action","<?php echo SITE_URL?>index.php?free_number");
							<?php }else{?>
							$('#next_page').val('create_account.php');
							$('#frm-step1').attr("action","<?php echo SITE_URL?>index.php?create_account");
							<?php } ?>
						}else{
							$('#next_page').val('choose_plan.php');
							$('#frm-step1').attr("action","<?php echo SITE_URL?>index.php?choose_plan");
						}
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
        <?php if(SITE_COUNTRY!="COM"){?>  
        <!--Start of Zopim Live Chat Script-->
		<script type="text/javascript">
		window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
		d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
		_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
		$.src='//v2.zopim.com/?1piIDUavtFoOMB89h2pj9OK8cT8CswZK';z.t=+new Date;$.
		type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
		</script>
		<!--End of Zopim Live Chat Script-->
		<?php } ?>

	</head>
	
	<body lang="en">
        
        <!-- Top Bar -->
	    <div class="topbar">
	         <!-- content -->
	         <div class="wrapper">
	              <div class="container">
	                  <div class="info"><span class="Mtop2"><img alt="email us" src="images/mail.png"/></span>&nbsp;<span class="Mleft5"><?php echo ACCOUNT_CHANGE_EMAIL; ?></span></div>
	                  <div class="logintop">
	                  		<?php if(isset($_SESSION['user_logins_id']) && trim($_SESSION['user_logins_id'])!=""){ ?>
	                  			Hi <?php echo $_SESSION['user_name']; ?>&nbsp;<a href="dashboard/index.php">My Account</a>
                                		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="dashboard/logout.php">Logout</a>
	                  		<?php }else{ 
									if(isset($_GET['error'])) {
										if($_GET['error']=='faileds'){
											echo "<div style='color:red;'>Your account has been suspended. Please contact support.</div>";
										}else if($_GET['error']=='failedv'){
											echo "<div style='color:red;'>Please verify your email to login.</div>";
										}else if( $_GET['error'] == 'failedl' ){
											echo "<div style='color:red;'>Login Restricted. Please contact support.</div>";
										}else if( $_GET['error'] == 'failed' ){
											echo "<div style='color:red;'>Member details incorrect,try again.</div>";
										}
									}
								 ?>
		                       	<form name="toplogin" id="toplogin" action="dashboard/login.php" method="post">
								
									<?php require "login_top.php"; ?>
								
									<input type="hidden" name="site_country" value="<?php echo SITE_COUNTRY; ?>" />
									<input type="hidden" name="front_page_login" value="2926354" id="front_page_login">
		                       	</form>
		                    <?php } //End of else part of no login session?>
	                  </div>
	              </div>
	         </div>
	         <!-- content closed -->         
	    </div>
	    <!-- Top Bar Closed -->
	    
	    <?php require "menu_and_logo.php"; ?>

<?php

require("./dashboard/classes/Did_Countries.class.php");
require("./dashboard/classes/Country.class.php");
$did_countries_obj = new Did_Countries();
$did_countries = $did_countries_obj->getAvailableCountries();

$country = new Country();
$countries = $country->getAllCountry();

?>       
       <!-- Banner Wrapper -->
       <div class="bannerwrapper">
       	<div class="banner-slide-wrap">
       	<div class="free-trials-badge"><img src="images/badge.png" width="160" height="160" alt="Free Trials"></div>
       	<div class="free-trials" id="div-free-trials"><img src="images/free-trials.png" width="175" height="97" alt="Free Trials No Restiction"></div>
       	<div class="banner-slide">
                   <h1 class="txt-c Mtop15"><font color="darkslategray">Over 1200 Virtual Numbers</font></h1>
                <ul>
                    <li><img src="images/pick-number.png" width="601" height="354" alt="Pick Number" class="centeredImage"></li>
                    <li><img src="images/slide-2.png" width="601" height="353" alt="Slide 2" class="centeredImage"></li>
                    <li><img src="images/dial-brand.png" width="600" height="354" alt="Dial Brand" class="centeredImage"></li>
                    <li><img src="images/phone-rings.png" width="600" height="354" alt="Destination Phone Rings"></li>
                </ul>
            </div>
                            <div class="get-started-box">
                            	<div class="get-started-white">
                                	
									<?php require 'get_started_div.php';?>
									
								</div><!-- End of get-started-white div tag -->
                            </div><!-- End of get-started-box div tag -->
                            <div class="clearfloat"></div> 
         </div><!-- End of banner-slide-wrap div tag -->
       </div><!-- End of bannerwrapper div tag -->
       <div class="clearfloat"></div>
       
       <!-- Main Content starts here -->
       <div class="icon-services-wrap Mtop40">
    <div class="icon-services">
    <img src="images/voicetoemail.png" width="105" height="52" alt="Voice to Mail" class="centeredImage">
<p class="txt-blue text-center"><strong>Voice to Email</strong></p>
    <p class="txt-c">Respond quickly to key calls with voicemails recorded and emailed to you</p>
    </div>
        <div class="icon-services">
        <img src="images/calloverflow.png" width="105" height="52" alt="Call Overflow" class="centeredImage">
<p class="txt-blue text-center"><strong>Sticky Call Alert</strong></p>
    <p class="txt-c">Know who is calling you with an alert prior to the call connecting</p>
    </div>
        <div class="icon-services">
        <img src="images/callforwarding.png" width="105" height="52" alt="Call Forwarding" class="centeredImage">
<p class="txt-blue text-center"><strong>Call Forwarding</strong></p>
    <p class="txt-c">Answer your business calls on your mobile or landline wherever you are</p>
    </div>
        <div class="icon-services">
        <img src="images/livedata.png" width="105" height="52" alt="Live Data" class="centeredImage">
<p class="txt-blue text-center"><strong>Live Data</strong></p>
    <p class="txt-c">View call activity in real time including call date, time and duration</p>
    </div>
        <div class="icon-services">
    <img src="images/dayrouting.png" width="105" height="52" alt="Day Routing" class="centeredImage">
<p class="txt-blue text-center"><strong>Day Routing</strong></p>
    <p class="txt-c">Maximize your resources by directing calls based on day of week</p>
    </div>
        <div class="icon-services">
     <img src="images/timerouting.png" width="105" height="52" alt="Time Routing" class="centeredImage">
<p class="txt-blue text-center"><strong>Time Routing</strong></p>
    <p class="txt-c">Maximize your resources by directing calls based on time of day</p>
    </div>
    <div class="clearfloat"></div>
    </div> <!-- icon-services-wrap -->
    
    <div class="wrapper">
	<div id="choose-us-wrap" class="Mtop40">
    	<div class="choose-us-box">
        <img width="100" height="100" alt="Contact Us" src="images/contact-mid.png"> <h2>Signup with us</h2>      
        </div>
        <img width="61" height="14" class="curved-sep" alt="Curve" src="images/curve-separator.png">
		<div class="choose-us-box">
        <img width="100" height="100" alt="Contact Us" src="images/free-num.png"> <h2>Virtual Number</h2>      
      	</div>
        <img width="61" height="14" class="curved-sep" alt="Curve" src="images/curve-separator-2.png">
    	<div class="choose-us-box">
        <img width="100" height="100" alt="Contact Us" src="images/divert.png"> <h2>Divert Phone<br> Number</h2>      
        </div>                
        <div class="clearfloat"></div>
    </div>
    
         <div class="container">         
				<h2 class="convert_h1_h2">Why choose Sticky Number</h2>
				<p class="font16 Mtop20">With your Sticky Number you can receive, record, manage and view call records from the comfort of any computer, tablet or smart phone in the world! You can get a local virtual phone number from over 1220 cities in over 60 countries and have it forward to just about any phone in the world. We aim to provide high performance and reliability using our global network. We also provide unlimited minutes with our free uk numbers.</p><br>
				<!--<p class="font16"><strong>Instantly receive a FREE number with unlimited minutes!</strong></p><br/>-->
				<img src="images/why-c<?php if(SITE_COUNTRY=="COM"){ echo "1"; }elseif(SITE_COUNTRY=="UK"){ echo "-uk";} ?>.jpg" width="493" height="348" alt="Why Choose Us" class="fltrt">
				<p class="font16">Here at Sticky Number we continue to add features to our systems so we can provide clients with all their virtual number needs. Our client base expands across the globe. All <?php echo COUNTRY_DEMONYM; ?> virtual phone numbers come with international forwarding, voice mail and IVR secure access. The Signup process is simple, choose a country and city then divert phone number free to most destinations around the world.</p><br>
					<!--<p class="font16">1. Signup<br>
					2. Choose phone number country and city<br>
					3. Divert Phone Number Free<br><br> -->
					<p class="font16">
					24/7 Server Monitoring<br>
					Complete Easy Management Tools<br>
					24/7 access to your online control panel<br>
					Unlimited virtual <?php echo COUNTRY_NAME; ?> number routing<br>
					View call records<br>
					</p> <br>
				<div class="blue-btn fltlft Mtop25" tabindex="0" onclick="location.href='?page=virtual_number';" onkeypress="location.href='?page=virtual_number';">Sign Up Now</div>
         </div>
    </div> 
    <!-- Main Content starts here Closed -->
       
       <form method="post" action="<?php echo SITE_URL?>index.php?choose_plan" name="frm-step1" id="frm-step1">
            <input type="hidden" value="" name="did_country_code" id="did_country_code">
            <input type="hidden" value="" name="did_city_code" id="did_city_code">
            <input type="hidden" value="" name="fwd_cntry" id="fwd_cntry">
            <input type="hidden" value="1" name="forwarding_type" id="forwarding_type">
            <input type="hidden" value="" name="forwarding_number" id="forwarding_number">
            <input type="hidden" value="" name="vnum_country" id="vnum_country">
            <input type="hidden" value="" name="vnum_city" id="vnum_city">
            <input type="hidden" value="" name="next_page" id="next_page">
       </form>