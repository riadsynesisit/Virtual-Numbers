<!doctype html>
<?php

require "inc_header_page.php";

require("./dashboard/classes/Country.class.php");

$country = new Country();
if(is_object($country)==false){
	echo "Failed to create Country object";
	exit;
}

$countries = $country->getAllCountry();
if(is_array($countries)==false){
	echo "Failed to get countries list.";
	exit;
}

?>

	<script>
            
            $(document).ready(function(){

            	$('#radNewAccount').on('ifChecked', function(event){
            		$('#div_user_details').show();
            		$('#new_or_current').val("new");
            	});

            	$('#radCurrentAccount').on('ifChecked', function(event){
            		$('#div_user_details').hide();
            		$('#new_or_current').val("current");
            	});

            	$('#radPersonal').on('ifChecked', function(event){
            		$('#div_company_name').hide();
            		$('#personal_or_business').val("P");
            	});

            	$('#radBusiness').on('ifChecked', function(event){
            		$('#div_company_name').show();
            		$('#personal_or_business').val("B");
            	});

				var blnNewAccountSuccess = false;
            	
                $('#btn-continue-step3').mousedown(function(event) {
                	event.preventDefault();
                    if(validate_step2()==true){
                    	jQuery.ajaxSetup({async:false});
                        //Create account ajax call
                    	var posturl = '<?php echo SITE_URL; ?>ajax/ajax_create_au_acct.php';
                    	var fname = $.trim($('#fname').val());
                    	var lname = $.trim($('#lname').val());
                    	var email = $.trim($('#email').val());
                    	var pword = $('#pword').val();
                    	var cname = $.trim($('#cname').val());
                    	var country = $.trim($('#country').val());
                    	var address = $.trim($('#address').val());
                    	var address2 = $.trim($('#address2').val());
                    	var postal_code = $.trim($('#postal_code').val());
                    	var city = $.trim($('#city').val());
                    	var state = $.trim($('#state').val());
                    	var site_country = "<?php echo SITE_COUNTRY; ?>";
						var is_free_num = 'N';
						if($("#free_number_did_city_code").val()=="99999"){//070 Free Number
							is_free_num = 'Y';
						}
                    	var postdata = {'fname' : fname, 
                				'lname' : lname, 
                				'email' : email, 
                				'pword' : pword, 
                				'new_or_current' : new_or_current,
								'user_type' : personal_or_business,
								'company' : cname,
								'user_country' : country,
								'address1' : address,
								'address2' : address2,
								'postal_code' : postal_code,
								'user_city' : city, 
								'user_state': state, 
								'site_country': site_country,
								'free_number' : is_free_num
                				};
        				$.ajax({type:"POST",
            					url: "<?php echo SITE_URL; ?>ajax/ajax_create_au_acct.php",
            					data: postdata, 
            					dataType:"text", 
            					async: false,
            					success: function(response){
                            		var response_array = response.split(",");
        							if(response_array[0]=="OK"){
        								//code to go to the next page
        								blnNewAccountSuccess = true;
        							}else{
        								alert(response);
        								return;
        							}
                            	},
                            	error: function(jqxhr, textStatus, errorThrown){
									alert("Error in ajax_create_au_acct.php: Status: "+textStatus+", Error Thrown: "+errorThrown);
                            	},
                            	complete: function(jqxhr, textStatus){
									//alert("Completed :"+textStatus);
									if(blnNewAccountSuccess==true){
                            			go_to_next_page();
									}else{
										return;
									}
                            	}
                    	});
                    }//End of validate_step2
                });//End of btn-continue-step3

            });

            function go_to_next_page(){
            	if($("#free_number_did_city_code").val()=="99999"){//070 Free Number
					$("#frm-free-num-070").submit();
				}else{
					if($('#next_page').val()=="make_payment.php" || $('#next_page').val()=="confirm.php"){
						$('#frm-step3').submit();
					}else{
						alert("Error: Unknow next_page - "+$('#next_page').val());
					}
				}
            }

			var new_or_current = "new";
			var personal_or_business = "P";
			function validate_step2(){
				
				if(validateEmail($('#email').val())==false){
					alert("Please enter your valid email id");
					$('#email').focus();
					return false;
				}
				if($('#pword').val().length < 6){
					alert("Please enter a password (min 6 characters)");
					$('#pword').focus();
					return false;
				}
				if($('#radNewAccount').is(':checked')==true){
					new_or_current = "new";
				}else{
					new_or_current = "current";
				}
				if($('#radBusiness').is(':checked')==true){
					personal_or_business = "B";
				}else{
					personal_or_business = "P";
				}
				if($('#radNewAccount').is(':checked')){
					if($.trim($('#fname').val()) == ""){
						alert("Please enter your first name");
						$('#fname').focus();
						return false;
					}
					if($.trim($('#lname').val()) == ""){
						alert("Please enter your last name");
						$('#lname').focus();
						return false;
					}
					<?php 
			        if(trim($_POST['did_city_code'])!="99999"){//No address required for Free UK 070 Numbers
			        ?>
					if($('#radBusiness').is(':checked') && $.trim($('#cname').val()) == ""){
						alert("Please enter your company name");
						$('#cname').focus();
						return false;
					}
					if($.trim($('#country').val()) == ""){
						alert("Please select your country");
						$('#country').focus();
						return false;
					}
					if($.trim($('#address').val()) == ""){
						alert("Please enter your address");
						$('#address').focus();
						return false;
					}
					if($.trim($('#postal_code').val()) == ""){
						alert("Please enter your postal code");
						$('#postal_code').focus();
						return false;
					}
					if($.trim($('#city').val()) == ""){
						alert("Please enter your city");
						$('#city').focus();
						return false;
					}
					<?php 
					}//End of if no address required for UK Free 070 Numbers
					?>
				}
				return true;
			}

			function validateEmail(sEmail) {
			    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
				
			    if (filter.test(sEmail)) {
			        return true;
			    }
			    else {
			        return false;
			    }
			}
         
          </script>

		  <link rel="stylesheet" media="all" href="<?php echo SITE_URL; ?>css/misc.css"/>
		  <link rel="stylesheet" media="all" href="<?php echo SITE_URL; ?>skins/minimal/minimal.css"/>
	<link rel="stylesheet" media="all" href="<?php echo SITE_URL; ?>css/override_bootstrap.css"/>
</head>
	<body>
  
  <?php include 'login_top_page.php';?>
  
  <?php include 'top_nav_page.php';?>
   
	<!-- Banner -->
    <!--div class="wrapper">	         
    	<div class="container"><img src="<?php echo SITE_URL?>images/SF-pricing.jpg" width="1024" height="201" alt="Account Details"></div>
    </div-->
    <!-- Banner Closed -->

	<div class="container">
    <img class="img-responsive" alt="Our Pricing" src="<?php echo SITE_URL;?>images/SF-pricing.jpg">
    </div><!-- /.container -->
    
    <!-- Main wrapper starts here -->
    <div class="wrapper">
         <div class="container"> 
           		<div class="stepNav-wrap">
                    <ul id="stepNav" class="fourStep">
                        <li class="lastDone"><a title=""><em>Step 1: Select Plan</em></a></li>
                        <li class="current"><a title=""><em>Step 2: Create Account</em></a></li>
                        <li><a title=""><em>Step 3: Make Payment</em></a></li>
                        <li class="stepNavNoBg"><a title=""><em>Step 4: Confirm/Finish</em></a></li>
                    </ul>
                    <div class="clearfloat">&nbsp;</div>
    			</div>
    			<div class="plan-box">
	           		<div class="payment-p-wrap Mtop40">
	                <h2>User Information</h2>
	                <div class="row Mtop5">
	                	<span class="rd-label">New Member</span>
	                	<span class="rd-radio"><div class="iradio_minimal checked" style="position: relative;"><input type="radio" checked id="radNewAccount" name="iCheck"></div></span>
	                	<span class="rd-label">Current Member</span>
	                	<span class="rd-radio"><div class="iradio_minimal" style="position: relative;"><input type="radio" id="radCurrentAccount" name="iCheck"></div></span>
	                </div>
	                <div class="row Mtop5">
	                <div class="payment-label">Email</div>
	                <div class="payment-value"><input type="text" name="email" id="email" class="input-s-wd" placeholder="Your Email"></div>
	                </div>
	                <div class="row Mtop5">
	                <div class="payment-label">Password</div>
	                 <div class="payment-value"><input type="password" name="pword" id="pword" class="input-s-wd" placeholder="Password"></div>
	                  </div>
	                <div class="clearfloat"></div>
	                 </div>
	                 <div class="payment-p-wrap Mtop10" id="div_user_details">
                <h2>Member Details</h2>
                <div class="row Mtop5">
                <div class="payment-label">First Name</div>
                <div class="payment-value"><input type="text" name="fname" id="fname" class="input-s-wd"></div>
                </div>
                <div class="row Mtop5">
                <div class="payment-label">Last Name</div>
                 <div class="payment-value"><input type="text" name="lname" id="lname" class="input-s-wd"></div>
                  </div>
                                  <div class="clearfloat"></div>
                
                <?php 
                if(trim($_POST['did_city_code'])!="99999"){//No address required for Free UK 070 Numbers
                ?>
                
                <div class="row Mtop5">
                 	<span class="rd-label">Personal</span>
                 	<span class="rd-radio"><div class="iradio_minimal checked" style="position: relative;"><input type="radio" checked id="radPersonal" name="iCheck2"></div></span>
                    <span class="rd-label">Business</span>
                    <span class="rd-radio"><div class="iradio_minimal" style="position: relative;"><input type="radio" id="radBusiness" name="iCheck2"></div></span>
                </div>
                <div class="clearfloat"></div>
                <div class="row Mtop5" id="div_company_name" style="display:none;">
                <div class="payment-label">Company Name</div>
                <div class="payment-value"><input type="text" name="cname" id="cname" class="input-s-wd"></div>
                </div> 
                  <div class="row Mtop5">
                 <div class="payment-label">Country / Region</div>
                 <div class="payment-value">
					<select id="country" name="country" class="selectP5 input-s-wd">
	                                           <option value="">Please Select Country</option>
	                                           <?php foreach($countries as $country) { ?>
	                                           <option value="<?php echo trim($country["iso"]); ?>"><?php echo $country["printable_name"]; ?></option>
	                                           <?php } ?>
	                 </select>
                                            </div>
                 </div>  
				<div class="row Mtop5">
                                            <div class="payment-label">Address</div>
                                            <div class="payment-value"><input type="text" class="input-s-wd" name="address" id="address" /></div>
                                       </div>  
				<div class="row Mtop5">
                                            <div class="payment-label">Address 2 (optional)</div>
                                            <div class="payment-value"><input type="text" class="input-s-wd" name="address2" id="address2" /></div>
                                       </div>                                                                                 
                   
                 <div class="row Mtop5">
                                            <div class="payment-label">Postal Code</div>
                                            <div class="payment-value"><input type="text" class="input-s-wd" name="postal_code" id="postal_code" /></div>
                                       </div>
                 <div class="row Mtop5">
                                            <div class="payment-label">City</div>
                                            <div class="payment-value"><input type="text" class="input-s-wd" name="city" id="city" /></div>
                                       </div>
                <div class="row Mtop5">
                                            <div class="payment-label">County / State</div>
                                            <div class="payment-value"><input type="text" class="input-s-wd" name="state" id="state" /></div>
                                       </div>  
                <?php 
                }//End of if for No address required for Free UK 070 numbers
                 ?>                                 
                 <div class="clearfloat"></div>
                 </div><!-- End of user details div tag -->
                 
                                       <div class="row Mtop15">
                                       <a class="payment-p-wrap-btn" id="btn-continue-step3">Continue</a>
                                       </div>
                                       <div class="clearfloat"></div>
                                 <p>&nbsp;</p>     
                                 <p>&nbsp;</p>             
           </div><!-- .Plan Box -->
          
    </div> 
	
    <!-- Main wrapper starts here Closed -->
	
	<?php include 'inc_free_uk_page.php';?>
	<?php include 'inc_footer_page.php';?>
	
    <?php 
       	if(isset($_POST['choosen_plan']) && trim($_POST['choosen_plan'])!=""){
       		$next_page_id = "make_payment";
       		$next_page = "make_payment.php";
       	}else{
       		$next_page_id = "confirm";
       		$next_page = "confirm.php";
       	}
    ?>
    <form method="post" action="<?php echo SITE_URL?>index.php?<?php echo $next_page_id; ?>" name="frm-step3" id="frm-step3">
            <input type="hidden" value="<?php echo trim($_POST['did_country_code']); ?>" name="did_country_code" id="did_country_code">
            <input type="hidden" value="<?php echo trim($_POST['did_city_code']); ?>" name="did_city_code" id="did_city_code">
            <input type="hidden" value="<?php echo trim($_POST['fwd_cntry']); ?>" name="fwd_cntry" id="fwd_cntry">
            <input type="hidden" value="<?php echo trim($_POST['forwarding_type']); ?>" name="forwarding_type" id="forwarding_type">
            <input type="hidden" value="<?php echo trim($_POST['forwarding_number']); ?>" name="forwarding_number" id="forwarding_number">
            <input type="hidden" value="<?php echo trim($_POST['vnum_country']); ?>" name="vnum_country" id="vnum_country">
            <input type="hidden" value="<?php echo trim($_POST['vnum_city']); ?>" name="vnum_city" id="vnum_city">
            <input type="hidden" value="" name="new_or_current" id="new_or_current">
            <input type="hidden" value="" name="personal_or_business" id="personal_or_business">
            <input type="hidden" value="<?php echo trim($_POST['choosen_plan']); ?>" name="choosen_plan" id="choosen_plan">
            <input type="hidden" value="<?php echo trim($_POST['plan_period']); ?>" name="plan_period" id="plan_period">
            <input type="hidden" value="<?php echo trim($_POST['plan_minutes']); ?>" name="plan_minutes" id="plan_minutes">
            <input type="hidden" value="<?php echo trim($_POST['plan_cost']); ?>" name="plan_cost" id="plan_cost">
            <input type="hidden" value="<?php echo $next_page; ?>" name="next_page" id="next_page">
       </form>
       <form method="post" action="<?php echo SITE_URL?>index.php?free_number" name="frm-free-num-070" id="frm-free-num-070">
            <input type="hidden" value="<?php echo trim($_POST['did_country_code']); ?>" name="did_country_code" id="free_number_did_country_code">
            <input type="hidden" value="<?php echo trim($_POST['did_city_code']); ?>" name="did_free_city_code" id="free_number_did_city_code">
            <input type="hidden" value="<?php echo trim($_POST['fwd_cntry']); ?>" name="fwd_cntry" id="free_number_fwd_cntry">
            <input type="hidden" value="<?php echo trim($_POST['forwarding_type']); ?>" name="forwarding_type" id="free_number_forwarding_type">
            <input type="hidden" value="<?php echo trim($_POST['forwarding_number']); ?>" name="forwarding_number" id="free_number_forwarding_number">
            <input type="hidden" value="<?php echo trim($_POST['free_number_id']); ?>" name="free_number_id" id="free_number_id">
            <input type="hidden" value="<?php echo trim($_POST['vnum_country']); ?>" name="vnum_country" id="free_number_vnum_country">
            <input type="hidden" value="<?php echo trim($_POST['vnum_city']); ?>" name="vnum_city" id="free_number_vnum_city">
            <input type="hidden" value="" name="new_or_current" id="free_number_new_or_current">
            <input type="hidden" value="" name="personal_or_business" id="free_number_personal_or_business">
            <input type="hidden" value="free_number_070_AU.php" name="next_page" id="free_number_next_page">
       </form>
    </div>