<!doctype html>
<?php

require "common_header.php";

require("./dashboard/classes/Shopping_cart.class.php");
require("./dashboard/classes/Country.class.php");
require("./dashboard/classes/User.class.php");
require("./dashboard/libs/utilities.lib.php");

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

$shopping_cart = new Shopping_cart();
if(is_object($shopping_cart)==false){
	echo "Error: Failed to create Shopping_cart object.";
	exit;
}

$user = new User();
if(is_object($user)==false){
	echo "Error: Failed to create User object.";
	exit;
}

if(isset($_SESSION["user_logins_id"])==false || trim($_SESSION["user_logins_id"])==""){
	echo "Your login session has expired.<br />";
	echo "Please login back again.";
	exit;
}

$_SESSION["post_values"] = $_POST;
//echo "<pre>";print_r($_SESSION);echo '</pre>';
$user_address = $user->getUserAddress($_SESSION["user_logins_id"]);
if(is_array($user_address)==false){
	if(substr($user_address,0,6)=="Error:"){
		echo $user_address;
		exit;
	}elseif($user_address==0){
		echo "No rows found for user_id : ".$_SESSION["user_logins_id"];
		exit;
	}
}

$ip_country = get_ip_location($_SERVER["REMOTE_ADDR"]);
$user_country = trim($user_address["user_country"]);
//echo $_SERVER["REMOTE_ADDR"].'##===##'.$ip_country.'##===##'.$user_country.'<br/>';

//Get Number of Card Declined attempts :: ( Set to zero as steve advised @ 01th july, 2015 )
$card_declined_attempts = $user_address["card_declined_attempts"]; 

//$card_declined_attempts = $user->getCardDeclinedAttempts($_SESSION["user_logins_id"]);

$blnShowWP = false;
$blnShowCC = false;
$blnShowPP = true;
//Override show WP
//Get Show WP (override) setting

//$show_wp = $user->get_show_wp($_SESSION["user_logins_id"]); /**  No need to fetch again. **/

 $show_wp = $user_address['show_wp'] ;

if($show_wp=="Y"){
	$blnShowCC = true;
}
else if(SITE_COUNTRY=="AU" && ($ip_country=='AU' || $ip_country=="NZ") && $card_declined_attempts < 3){
	$blnShowCC = true; // skip condition  && ($user_country=='AU' || $user_country=="NZ") 
}elseif(SITE_COUNTRY=="UK" && $ip_country=='GB' && $card_declined_attempts < 3){
	$blnShowCC = true;
}
if(SITE_COUNTRY=="UK"){
	$blnShowCC = false; // added temporary
}
//$blnShowCC = true;
if(strstr($_SERVER["HTTP_HOST"], 'dev.') || strstr($_SERVER["HTTP_HOST"], 'localhost') || strstr($_SESSION["user_login"], "steve.s") ){
	//$blnShowCC = true;
	
} 
$years = array("2016","2017","2018");
$months = array("JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC");
//echo '<pre>';print_r($_SESSION);echo '</pre>';
?>

		<script>
		  var country_iso = "<?php echo trim($_POST["did_country_code"]); ?>";
		  var city_id = "<?php echo trim($_POST["did_city_code"]); ?>";
		  var plan_period = "<?php echo trim($_POST["plan_period"]); ?>";//M=Monthly, Y=Yearly
		  var choosen_plan = <?php echo trim($_POST["choosen_plan"]); ?>;
		  var pay_amount = <?php echo trim($_POST["plan_cost"]); ?>;
		  var purchase_type = "new_did";
		  var fwd_number = "<?php echo trim($_POST["forwarding_number"]); ?>";
		  var forwarding_type = "<?php echo trim($_POST["forwarding_type"]); ?>";
		  var new_or_current = "<?php echo trim($_POST["new_or_current"]); ?>";
		  var site_country = "<?php echo SITE_COUNTRY; ?>";
		  var ip_country = "<?php echo $ip_country; ?>";
		  var user_country = "<?php echo $user_country; ?>";
		  var card_declined_attempts = <?php echo $card_declined_attempts; ?>;
		  var site_currency = '<?php echo SITE_CURRENCY; ?>';
		  //var blnShowCC = <?php if($blnShowCC==true){ echo "true;"; } else { echo "false;"; } ?>;

            $(document).ready(function(){
            	
                var pymtType = <?php if($blnShowCC==true){ echo '"Credit_Card"'; } else { echo '"PayPal"'; } ?>;
                var mins_array = "";

                if(new_or_current=="current"){
					//Get Account Credit
					$.post('<?php echo SITE_URL; ?>ajax/ajax_get_account_credit.php',{},function(response){
						//console.log(response);
						if(response.substr(0,7)=="Success"){
							var account_credit_array = response.split(',');
							if(parseFloat(account_credit_array[1]) >= parseFloat(pay_amount)){
								$('#radio-account-credit').show();
								$('#account-credit-amount').text('$'+account_credit_array[1]);
							}
						}else{
							alert(response);
							return;
						}
					});
            	}

                $('#btn-make-payment').mousedown(function(event) {
                	event.preventDefault();
					
					if($('#terms-chk-box').is(':checked') == false){
						alert("You must agree to the terms and conditions to proceed");
						$('#terms-chk-box').focus();
						return false;
    				}
						
                    if(pymtType == 'AcctCredit'){
                    	
    					if(confirm('<?php echo CURRENCY_SYMBOL; ?>'+pay_amount+' will be deducted from your account credit. Please confirm.')==true){
    						showPaymentProgress();
    						charge_acct_credit();
    					}else{
    						event.preventDefault();
    					}
						return;
                    }else if(pymtType == 'PayPal'){
                    	/*if($('#terms-chk-box').is(':checked') == false){
    						event.preventDefault();
        					alert("You must agree to the terms and conditions to proceed");
        					$('#terms-chk-box').focus();
        					return false;
        				}*/
    					var cartId = get_cartId('PayPal');
    					//Proceed to PayPal page
    					var url='<?php echo SITE_URL; ?>ajax/ajax_go_to_paypal.php';
    					var postdata = {'cartId' : cartId};
    					jQuery.ajaxSetup({async:false});
    					$.post(url, postdata, function(response){
    						if(response.substr(0,6)!='Error:'){
    							//Now take them to the PayPal site
    							location.href= response;
    							return;
    						}else{
    							alert(response);
    							return;
    						}
    						});//End of $.post
    					return;
                    }
                	else if(pymtType == 'CreditCard'){
						if(validate_payment()==true){
							var cartId = get_cartId('WorldPay');
							gotoWorldPay(cartId);
							return;
						}
					}
					else if(pymtType == 'Credit_Card'){
						
						var is_recurrent_cc = true;//$('#cc_reccurent-chk-box').is(":checked");
						
						var cartId = get_cartId('Credit_Card');
						
						var url = '<?php echo SITE_URL; ?>ajax/ajax_simplepay_generate_token.php';
						$.post(url, {currency: site_currency, amount:pay_amount, did_plan_period:plan_period,is_recurrent:is_recurrent_cc}, function(response){
							//console.log(response);
							response = $.parseJSON(response);
							console.log(response);
							if(response.id){
								var pay_url = '<?php echo SITE_URL; ?>'+'?page=simple_payment&id='+response.id+'&cart_id='+cartId;
								//console.log(pay_url);
								location.href = pay_url;
								//window.open(pay_url);
							}else{
								//alert("An error occurred. Please try again.");
								console.log("error")
								return false;
							}
						
							
						});
						//send email...
						/*if(validate_payment_cc()==true){

							var cartId = get_cartId('Credit_Card');
							var url='<?php echo SITE_URL; ?>ajax/ajax_send_payment_info.php';
							var postdata = {
								t_cart_id : cartId,
								t_member_id : '<?php echo $_SESSION['user_logins_id'];?>',
								t_orderType : 'New',
								t_amount: pay_amount+'<?php echo SITE_CURRENCY; ?>',	
								t_city_id : city_id , 
								t_fwd_number : fwd_number,
								t_cc_type:   $('#payment-card-cc-type').val(),
								t_cc_number: $('#input-cc-number').val(),
								t_cc_cvv:    $('#input-cc-cvv-number').val(),
								t_cc_expiry: ($('#input-cc-expiry-month').val()+'-'+$('#input-cc-expiry-year').val())
							};
							//console.log(postdata);
							jQuery.ajaxSetup({async:false});
							$.post(url, postdata, function(response){
								if(response=='Sent'){
    							
									alert('Thanks, you will be notified soon by our customer support');
									return;
								}else{
									alert(response);
									return;
								}
    						});//End of $.post
							
							return;
						}*/
						
					}//end of validate_payment
                });

                $('#radPayPal').on('ifChecked', function(event){
					//console.log('pp clicked');
                	pymtType = 'PayPal';
					$('#radPayPal').click();
					/*$('#row_card_type').hide();
					$('#row_street_address').hide();
                	$('#row_city').hide();
                	$('#row_country').hide();
                	$('#row_state').hide();
                	$('#row_postal_code').hide();*/
					
					//$('.div_cc').children().hide();
					$('.div_wp').children().hide();
				});
				
				$('#radAcctCredit').on('ifChecked', function(event){
					pymtType = 'AcctCredit';
					$('#radAcctCredit').click();
					/*$('#row_card_type').hide();
					$('#row_street_address').hide();
                	$('#row_city').hide();
                	$('#row_country').hide();
                	$('#row_state').hide();
                	$('#row_postal_code').hide();*/
					
					//$('.div_cc').children().hide();
					$('.div_wp').children().hide();
					
				});
                
                $('#radCreditCard').on('ifChecked', function(event){
                	pymtType = 'CreditCard';
					//console.log('wp cc clicked');
                	/*$('#row_card_type').show();
                	$('#row_street_address').show();
                	$('#row_city').show();
                	$('#row_country').show();
                	$('#row_state').show();
                	$('#row_postal_code').show();*/
					$('.div_wp').children().show();
					//$('.div_cc').children().hide();
                });
				
				$('#radCCard').on('ifChecked', function(event){
                	pymtType = 'Credit_Card';
					//console.log('cc clicked');
                	
					//$('.div_cc').show().children().show();
					$('.div_wp').children().hide();
                });

                $('#btn-back-to-sel-plan').click(function(event){
                	$('#frm-sel-plan').submit();
                });
                
            });//end of $(document).ready

			function validate_payment() {
				if($.trim($('#payment-card-type').val()) == ""){
					alert("Please select your card type");
					$('#payment-card-type').focus();
					return false;
				}
				if($.trim($('#billing-street').val()) == ""){
					alert("Please enter your street address");
					$('#billing-street').focus();
					return false;
				}
				if($.trim($('#billing-city').val()) == ""){
					alert("Please enter your city");
					$('#billing-city').focus();
					return false;
				}
				if($.trim($('#billing-country').val()) == ""){
					alert("Please enter your country");
					$('#billing-country').focus();
					return false;
				}
				if($.trim($('#billing-postal-code').val()) == ""){
					alert("Please enter your postal code");
					$('#billing-postal-code').focus();
					return false;
				}
				if($('#terms-chk-box').is(':checked') == false){
					alert("You must agree to the terms and conditions to proceed");
					$('#terms-chk-box').focus();
					return false;
				}
				return true;
			}

			function validate_payment_cc() {
				if($.trim($('#payment-card-cc-type').val()) == ""){
					alert("Please select your card type");
					$('#payment-card-cc-type').focus();
					return false;
				}
				if($.trim($('#input-cc-number').val()) == ""){
					alert("Please enter your Card Number");
					$('#input-cc-number').focus();
					return false;
				}else{
					if( $.trim($('#input-cc-number').val()).length < 12 ){
						alert('Card number should be greater than 12 digits!');
						$('#input-cc-number').focus();
						return;
					}
				}
				if($.trim($('#input-cc-cvv-number').val()) == ""){
					alert("Please enter CVV numebr");
					$('#input-cc-cvv-number').focus();
					return false;
				}else{
					if( $.trim($('#input-cc-cvv-number').val()).length > 4 ){
						alert('CVV number not greater than 4 digits!');
						$('#input-cc-cvv-number').focus();
						return;
					}
				}
				if($.trim($('#input-cc-expiry-month').val()) == ""){
					alert("Please enter Expiry details");
					$('#input-cc-expiry-month').focus();
					return false;
				}
				if($.trim($('#input-cc-expiry-year').val()) == ""){
					alert("Please enter Expiry details");
					$('#input-cc-expiry-year').focus();
					return false;
				}
				if($('#terms-chk-box').is(':checked') == false){
					alert("You must agree to the terms and conditions to proceed");
					$('#terms-chk-box').focus();
					return false;
				}
				return true;
			}

			function showPaymentProgress(){
				$('#btn-make-payment').prop('value','Processing');
				$('#btn-make-payment').attr("disabled", true);
			}

			function hidePaymentProgress(){
				$('#btn-make-payment').prop('value','Make Payment');
				$('#btn-make-payment').removeAttr("disabled");
			}

			function gotoWorldPay(cartId){

				if(plan_period == "M"){
					var pay_interval = 3;
					var pay_start_date = "<?php echo date('Y-m-d',strtotime(date("Y-m-d", mktime())." + 28 day")); ?>";
				}else if(plan_period == "Y"){
					var pay_interval = 4;
					var pay_start_date = "<?php echo date('Y-m-d',strtotime(date("Y-m-d", mktime())." + 360 day")); ?>";
				}else{
					alert("Error: Unknown plan period value: "+plan_period);
					return false;
				}
				//Build the URL with querystring
				//cartId must be unique
				var url = '<?php echo WORLDPAY_URL; ?>&cartId='+cartId+'&amount='+pay_amount+
							'&currency=<?php echo SITE_CURRENCY; ?>&desc=Virtual Number of '+country_iso+' '+city_id+
							'&email=<?php echo $_SESSION["user_logins_id"]; ?>@stickynumber.net&name=<?php echo $_SESSION["user_name"]; ?>&address1='+
							$('#billing-street').val()+
							'&town='+$('#billing-city').val()+
							'&postcode='+$('#billing-postal-code').val()+
							'&country='+$('#billing-country').val()+
							'&hideContact=&noLanguageMenu=&hideCurrency=&compName=StickyNumber.com.au&paymentType='+$('#payment-card-type').val()+
							'&futurePayType=regular&option=0&normalAmount='+pay_amount+
							'&startDate='+pay_start_date+'&intervalUnit='+pay_interval+'&intervalMult=1';
				location.href = url;
				
			}

			function get_cartId(pymt_gateway){

				jQuery.ajaxSetup({async:false});
				var url = '<?php echo SITE_URL; ?>ajax/ajax_add_to_cart.php';
				postdata = {'amount' : pay_amount, 
						'did_plan' : choosen_plan, 
						'description' : 'Virtual Number: <?php echo trim($_POST['vnum_country']); ?> <?php echo trim($_POST['vnum_city']); ?>',
						'plan_period' : plan_period,
						'num_of_months' : 1,
						'country_iso' : country_iso,
						'city_prefix' : city_id,
						'fwd_number' : fwd_number, 
						'forwarding_type': forwarding_type, 
						'return_to' : 'Slider',
						'purchase_type' : 'new_did',
						'pymt_gateway': pymt_gateway
						};
				var cartId = '';
				$.post(url, postdata, function(response){
					if(response.substr(0,6)!='Error:'){
						cartId = response;
					}else{
						alert(response);
						return;
					}
				});

				return cartId;
				
			}

			function charge_acct_credit(){

				var cartId = get_cartId('AccountCredit');
				var url = '<?php echo SITE_URL; ?>ajax/ajax_charge_acct_credit.php';
            	var num_of_months = 1;//Hard coded until annual payment or multiple months is allowed on front end
            	var did_id = "";//No did_id yet for NEW numbers to be obtained.
				jQuery.ajaxSetup({async:false});
				$.post(url,{'cart_id': cartId, 
							'choosen_plan' : choosen_plan, 
							'forwarding_type' : forwarding_type, 
							'fwd_number' : fwd_number, 
							'country_iso' : country_iso, 
							'city_id' : city_id , 
							'plan_period' : plan_period, 
							'num_of_months' : num_of_months, 
							'purchase_type' : purchase_type, 
							'did_id' : did_id},
							function(response){
					if(response.substr(0,7)=="Success"){
						//Send them to the inc_new_did_slider.php
						$("#cartId").val(cartId);
						$("#frm-make-payment").submit();
						//location.href = '<?php echo SITE_URL?>confirm.php?cartId='+cartId;
					}else{
						hidePaymentProgress();
						alert(response);
						return;
					}
				});
				//return;	
			}
			
			function generate_token(){
				var url = '<?php echo SITE_URL; ?>ajax/ajax_simplepay_generate_token.php';
            	$.post(url, {}, function(response){
					console.log(response);
					if(response.id){
						
					}
				
					
				});
			}
         
          </script>

	<!-- Banner -->
    <div class="wrapper">	         
    	<div class="container"><img src="images/SF-pricing.jpg" width="1024" height="201" alt="Account Details"></div>
    </div>
    <!-- Banner Closed -->

    <!-- Main wrapper starts here -->
    <div class="wrapper">
         <div class="container"> 
           		<div class="stepNav-wrap">
                    <ul id="stepNav" class="fourStep">
                        <li class="done"><a title=""><em>Step 1: Select Plan</em></a></li>
                        <li class="lastDone"><a title=""><em>Step 2: Create Account</em></a></li>
                        <li class="current"><a title=""><em>Step 3: Make Payment</em></a></li>
                        <li class="stepNavNoBg"><a title=""><em>Step 4: Confirm/Finish</em></a></li>
                    </ul>
                    <div class="clearfloat">&nbsp;</div>
    			</div>
    		
    		<div class="plan-box">
           		<div class="payment-p-wrap Mtop40">
                <h2>Shopping Cart</h2>
                <div class="row Mtop15">
                <div class="payment-label-2">Plan</div>
               <div class="payment-value txt-b"><?php if(isset($_POST["personal_or_business"]) && trim($_POST["personal_or_business"])=="B"){ echo "Business"; }else{ echo "Personal";} ?></div>
               </div>
                <div class="row Mtop15">
                <div class="payment-label-2">Product</div>
               		<div class="payment-value txt-b">
               			<?php 
               				echo "Online Number - ".trim($_POST["vnum_country"])." ".trim($_POST["vnum_city"]);
               			?>
               		</div>
               </div>
                <div class="row Mtop15">
                <div class="payment-label-2">Minutes Included</div>
               <div class="payment-value txt-b"><?php echo $_POST["plan_minutes"]; ?></div>
               </div>  
                <div class="row Mtop15">
                <div class="payment-label-2">Plan Length</div>
               <div class="payment-value txt-b"><?php if(isset($_POST["plan_period"]) && trim($_POST["plan_period"])=="Y"){ echo "Yearly"; }else{ echo "Monthly";} ?></div>
               </div>  
                <div class="row Mtop15">
                <div class="payment-label-2">Plan Cost</div>
               <div class="payment-value txt-b"><?php echo CURRENCY_SYMBOL.$_POST["plan_cost"]; ?></div>
               </div>  
                <div class="row Mtop15">
                <div class="payment-label-2">Activation Fee</div>
               <div class="payment-value txt-b"><?php echo CURRENCY_SYMBOL; ?>00</div>
               </div>
                <div class="row Mtop15 payment-t">
                <div class="payment-label-2 font18">Total Cost</div>
               <div class="payment-value txt-b font18"><?php echo CURRENCY_SYMBOL." ".$_POST["plan_cost"]." ".SITE_CURRENCY; ?>  per <?php if(trim($_POST["plan_period"])=="M"){echo "Month";}elseif(trim($_POST["plan_period"])=="Y"){echo "Year";}?></div>
               </div>                                                                                            
               <div class="clearfloat"></div>
           </div>
           
           <div class="payment-p-wrap Mtop10">
           								<div class="row" id="radio-account-credit" style="display: none;">
                                            <div class="paymentoption-radio"><input type="radio" name="payment" id="radAcctCredit" /></div>
                                            <div class="paymentoption-account">Account Credit </div>
                                            <div class="paymentoption-money"><span id="account-credit-amount"></span></div> 
                                       </div>
									  <div class="row">
                                            <div class="paymentoption-radio"><input type="radio" <?php if( $blnShowCC == false ) {echo ' checked ';}?> name="payment" id="radPayPal" class=" Mtop7" <?php if($blnShowPP==false) echo 'disabled="disabled"'; ?> /></div><div class="paymentoption-merchant"><img alt="PayPal" src="images/paypal.png"/></div>
											<?php if( $blnShowCC == true ){?>
											<div class="paymentoption-radio"><input checked type="radio" name="payment" id="radCCard" class=" Mtop7"  /></div>
                                            <div class="paymentoption-merchant"><img alt="visa" src="images/visa.png"/></div>
                                            <div class="paymentoption-merchant"><img alt="mastercard" src="images/mastercard.png"/></div>
                                            <div class="paymentoption-merchant"><img alt="amex" src="images/amex.png"/></div>
											<?php }?>
                                            
                                       </div>
                                       									   
									   
									   <div class="row Mtop15">
                                            <div class="paymentoption-radio"><input type="checkbox" id="terms-chk-box" /></div>
                                            <div class="payment-agree"><a href="terms/" target="_blank">I agree to the Terms and Conditions</a> <span class="manditory">*</span></div> 
                                       </div>
                                       <div class="clearfloat"></div>           
           </div>
           <div class="row Mtop15">
                                       <a class="payment-p-wrap-btn" id="btn-make-payment">Continue</a>
                                       </div>
           <p>&nbsp;</p>
           <p>&nbsp;</p>
           </div><!-- .Plan Box -->
    		
    		</div>
    	</div>
    	
    	<form method="post" action="<?php echo SITE_URL?>index.php?confirm" name="frm-make-payment" id="frm-make-payment">
    		<input type="hidden" value="" name="cartId" id="cartId">
    		<input type="hidden" value="confirm.php" name="next_page" id="next_page">
    	</form>