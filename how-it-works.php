<!doctype html>
<?php 
//This page is reachable from how-to-get-a-phone-number URL
if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
	if(isset($_SERVER["REQUEST_URI"]) && ($_SERVER["REQUEST_URI"]=="/index.php?page=how-it-works" || $_SERVER["REQUEST_URI"]=="/how-to-get-a-phone-number")){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: how-to-get-a-phone-number/");
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
<html lang="en">
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="rating" content="general" />
        <!-- meta name="Language" content="en" /-->
        <meta name="robots" content="all" />
        <meta name="robots" content="index,follow" />
        <!-- meta name="re-visit" content="7 days" /-->
        <!-- meta name="distribution" content="global" /-->    
        <?php if(SITE_COUNTRY == "AU"){?>    
        <title>Virtual Phone Number Australia, Virtual Phone Numbers </title>
        <meta name="description" content="Sticky Number provides Australian virtual phone numbers. International virtual phone number forwarding to landline and mobile telephones. Over 1200 cities."/>
		<?php }elseif(SITE_COUNTRY == "UK"){ ?>
		<title><?php echo COUNTRY_DEMONYM; ?> Number, <?php echo COUNTRY_DEMONYM; ?> Virtual Phone Number | Sticky Numbers <?php echo COUNTRY_NAME; ?> </title>
		<meta name="description" content="Sticky Number is a leading company provides United Kingdom numbers, UK Virtual numbers with guaranteed performance and reliability. For more:visit us online!"/>
        <?php }else{ ?>
		<title><?php echo COUNTRY_DEMONYM; ?> Number, <?php echo COUNTRY_DEMONYM; ?> Virtual Phone Number | Sticky Numbers <?php echo COUNTRY_NAME; ?> </title>
        <meta name="description" content="Step by step guide on how to sign-up and setup your virtual number. Providing screen shots with each virtual number setup process."/>
		<?php } ?>
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->		
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<!-- Adding "maximum-scale=1" fixes the Mobile Safari auto-zoom bug: http://filamentgroup.com/examples/iosScaleBug/ -->
        
        <!-- CSS STYLING -->        
        <link rel="stylesheet" media="all" href="<?php echo SITE_URL; ?>css/core.css"/>
        <link rel="stylesheet" media="all" href="<?php echo SITE_URL; ?>css/misc.css"/>
        <link rel="shortcut icon" href="<?php echo SITE_URL; ?>favicon.ico" type="image/x-icon" />
        <link type="text/css" rel="stylesheet" href="<?php echo SITE_URL; ?>skins/minimal/minimal.css" />
		
        <!-- JS -->
        <script type="text/javascript" src="<?php echo SITE_URL; ?>js/jquery-1.9.1.min.js"></script>
        <script src="<?php echo SITE_URL; ?>js/jquery.slides.min.js"></script>
        <script src="<?php echo SITE_URL; ?>js/scrolltopcontrol.js"></script>
        <script src="<?php echo SITE_URL; ?>js/jquery-ui-1.10.3.custom.min.js"></script>
        
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
                    
                    var url = '<?php echo SITE_URL; ?>ajax/ajax_load_did_cities.php';
                
                    $.get(url, {'country_iso' : country_iso}, function(response){
                        var data = jQuery.parseJSON(response);  
                        $('#city').removeAttr('disabled');
                        $('#city option').remove();
                        $('#city').append('<option value="">Please select a city</option'); 
                        $(data).each(function(index, item) {
                            $('#city').append('<option value="'+$.trim(item['city_id'])+'">'+item['city_name']+' '+item['city_prefix']+' (<?php echo CURRENCY_SYMBOL; ?>'+item['sticky_monthly_plan1']+'/month)</option>');
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
	                  <div class="info"><span class="Mtop2"><img alt="email us" src="<?php echo SITE_URL; ?>images/mail.png"/></span>&nbsp;<span class="Mleft5"><?php echo ACCOUNT_CHANGE_EMAIL; ?></span></div>
	                  <div class="logintop">
	                  		<?php if(isset($_SESSION['user_logins_id']) && trim($_SESSION['user_logins_id'])!=""){ ?>
	                  			Hi <?php echo $_SESSION['user_name']; ?>&nbsp;<a href="<?php echo SITE_URL; ?>dashboard/index.php">My Account</a>
                                		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo SITE_URL; ?>dashboard/logout.php">Logout</a>
	                  		<?php }else{ ?>
	                  			<?php if(isset($_GET['error']) && $_GET['error']=='failed' ) { ?> <div style='color:red;'>Member details incorrect,try again.</div><?php } ?>
		                       	<form name="toplogin" id="toplogin" action="<?php echo SITE_URL; ?>dashboard/login.php" method="post">
								
								<?php require "login_top.php"; ?>
								
		                       	<input type="hidden" name="site_country" value="AU" />
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

	<!-- Banner -->
    <div class="wrapper">
         <div class="container Mtop40">
              <img src="<?php echo SITE_URL; ?>images/SN-banner.jpg" width="1024" height="201" alt="Banner">
         </div>
    </div>
    <!-- Banner Closed -->

    <!-- Main wrapper starts here -->
    <div class="wrapper">
        <div class="container">  
        <p>&nbsp;</p>
        <div class="htw">
         	<h1 class="txt-green">How it Works</h1>
  			<p class="font18 txt-blue">See the process step by step</p>                   
            <div class="row Mtop20 f14"> 
            	<br>
                <img width="577" height="357" alt="How it works" src="<?php echo SITE_URL; ?>images/htw-steps.png">
				<p>&nbsp;</p>
                <p>&nbsp;</p>
                <p class="font18">Sticky Number makes going global quicker, easier and simpler than you'd ever have thought. Check out how easy it is below.</p>
                
                <p>&nbsp;</p>                   
    			<div class="clearfloat"></div>
                <div class="plan-box htw-2">
						<p class="font18"><span class="box-num">1</span>Pick your local or toll free number from just about any location globally and get activated instantly.</p><br>
                        <img src="<?php echo SITE_URL; ?>images/worldmap.jpg" alt="World map" width="478" height="217" class="centeredImage">
						<p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p class="font18"><span class="box-num">2</span>Enter the number you wish to forward your brand new Sticky Number to. (Forward to local and mobile numbers)</p><br>
                   		<img src="<?php echo SITE_URL; ?>images/add-new<?php if(SITE_COUNTRY == "UK"){echo "-uk"; }?>.jpg" alt="Add new" width="668" height="314" class="centeredImage">                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p class="font18"><span class="box-num">3</span>Dial your brand new Sticky Number from any phone.</p>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <div class="centeredImage">
                        <img src="<?php echo SITE_URL; ?>images/iphone.jpg" width="158" height="310" alt="Iphone"></div>
                                                <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p class="font18"><span class="box-num">4</span>The call goes via the Sticky Number service where we redirect to your destination number</p>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>                        
                        <img src="<?php echo SITE_URL; ?>images/team<?php if(SITE_COUNTRY == "UK"){echo "-uk"; }?>.jpg" width="600" height="314" alt="Team">
                                                <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p class="font18"><span class="box-num">5</span>Your destination phone rings</p>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>                        
                        <div class="centeredImage"><img src="<?php echo SITE_URL; ?>images/samsung.jpg" width="158" height="310" alt="Samsung"></div>
                                                <p>&nbsp;</p>
                        <p>&nbsp;</p>
				</div>
                
            </div>
		</div><!-- Left Box end-->

                   <!-- Right -->
                   <div class="contact-rightbox Mtop25">
                   		
                        <div class="row">
                             <div class="get-started-box">
                            	<div class="get-started-white">
                                	
									<?php require 'get_started_div.php';?>  <!-- INCLUDING Common DIV for all page -->
								
								</div>
                            </div>
                        </div>
                        
                         
                       <!--
                       <div class="row relative">
                            <div class="heading">24/7 Support</div>                            
                            <div class="support-help<?php if(SITE_COUNTRY!="AU"){ echo "1"; } ?>"></div>                            
                       </div>
                       
                       <div class="row Mtop150">
                       
                       <div class="support-list">
                            <ul>
                               <li>Instant Account Activation</li>
                               <li>24/7 Server Monitoring</li>
                               <li>Complete Easy Manangement Tools</li>
                               <li>Accreditied <?php echo COUNTRY_DEMONYM; ?> Number Supplier</li>
                               <li>Wordwide Phone Number Routing</li>
                               <li>Free Voice Mail</li>                               
                            </ul>
                       </div>
                       </div>
                        -->
                   </div>
               <div class="clearfloat"></div>                     
    			
    			
         </div>
    </div> 
    <!-- Main wrapper starts here Closed -->
       
       <form method="post" action="<?php echo SITE_URL?>index.php?choose_plan" name="frm-step1" id="frm-step1">
            <input type="hidden" value="" name="did_country_code" id="did_country_code">
            <input type="hidden" value="" name="did_city_code" id="did_city_code">
            <input type="hidden" value="" name="fwd_cntry" id="fwd_cntry">
			<input type="hidden" value="1" name="forwarding_type" id="forwarding_type">
            <input type="hidden" value="" name="forwarding_number" id="forwarding_number">
            <input type="hidden" value="" name="vnum_country" id="vnum_country">
            <input type="hidden" value="" name="vnum_city" id="vnum_city">
            <input type="hidden" value="choose_plan.php" name="next_page" id="next_page">
       </form>