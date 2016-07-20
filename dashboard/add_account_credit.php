<?php
	require "./includes/DRY_setup.inc.php";
	
	function get_page_content($this_page)
    {
        global $action_message;
        
        if(isset($_SESSION["user_logins_id"])==false || trim($_SESSION["user_logins_id"])==""){
        	echo "Error: No user login session found. Please login again.";
        	exit;
        }
        
        $user = new User();
		if(is_object($user)==false){
			die("Failed to create User object.");
		}
	    
	    //Get Age of account in days
		$age_in_days = 1;//$user->get_user_age_in_days($_SESSION["user_logins_id"]);
		if($age_in_days <= 7){
			//Check if the 7 days age requirement is overridden by admin 
			$show_acct_crdt = $user->get_show_acct_crdt($_SESSION["user_logins_id"]);
			if($show_acct_crdt!="Y"){
				echo "Your account is not yet activated for adding account credit.";
				exit;
			}
		}
        
	    if(isset($_SESSION['user_logins_id']) && trim($_SESSION['user_logins_id'])!=""){
			//if(isset($_POST['billing-security-cvv2'])){
			if(isset($_GET["cartId"]) && trim($_GET["cartId"])!=""){
				process_add_credit($this_page,trim($_GET["cartId"]));
			}else{
				show_setup_page($this_page);
			}
		}else{
	        //Jump to login screen
	        $this_page->meta_refresh_sec = READ_SCREEN_BEFORE_REDIRECT_SECONDS+12;
	        $this_page->meta_refresh_page = "login.php";
	
	        $this_page->content_area = "";
	        $this_page->content_area .= "We're sorry, you must be logged in as a verified user to access this service. </ br>\n";
	        $this_page->content_area .= "We will now  try to automatically take you to the login page.  If nothing seems to happen in a few seconds, just click <a href='login.php'>this link</a> and we will take you there. </ br>\n";
	
	        common_header($this_page, "Not Logged In - Login Required", false, $meta_refresh_sec, $meta_refresh_page);
	   }
        
    }
    
    function show_setup_page($this_page){
		
		$page_vars = array();
		
		$page_vars['menu_item'] = 'account_credit';
			
		$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	
		$this_page->content_area .= add_credit_html();
		
		common_header($this_page,"Account Credit");
	
	}
	
	function show_thank_you_page($this_page, $payment_status){
		
		$page_vars = array();
		
		$page_vars['menu_item'] = 'account_credit';
			
		$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	
		$this_page->content_area .= add_thank_you_html($payment_status);
		
		common_header($this_page,"Account Credit");
	
	}
	
	function process_add_credit($this_page, $cartId){
	
		$blnError=false;
		
		//Check if there is any error from WorldPay
		if(isset($_GET["errMsg"]) && trim($_GET["errMsg"])!=""){
			$blnError = true;
			$errMsg = trim($_GET["errMsg"]);
		}
		
		$risk_score = new Risk_score();
		if(is_object($risk_score)==false){
			$blnError = true;
			$errMsg = "Error: Failed to create Risk_score object.";
		}
		
		//Get shopping_cart details
		$shopping_cart = new Shopping_cart();
		if($blnError==false && is_object($shopping_cart)==false){
			$blnError = true;
			$errMsg = "Error: Failed to create Shopping_cart object";
		}
		
		$cart_details = "";
		$cartId = $_GET["cartId"];
		if($blnError==false){
			$cart_details = $shopping_cart->get_cart_details($cartId);
		}
		
		if($blnError==false && is_array($cart_details)==false){
			$blnError = true;
			$errMsg = $cart_details;
		}
		
		//process the account credit purchase
		
		
		//Check if this cartId is paid
		if($blnError==false && $cart_details["paid"]=="N"){
			//Check if the order is queued up for processing
			$status = $risk_score->get_order_status($cartId);
			if($status==5){//Order is in process queue
				show_thank_you_page($this_page, false);
				return;
			}else{
				$blnError = true;
				$errMsg = "Error: This order was not paid.";
			}
		}
		
		if($blnError==false && is_array($cart_details)==false){
			$blnError = true;
			$errMsg = $cart_details;
		}
		
		$page_vars = array();

		//Add account credit to the user
		$member_credits = new Member_credits();
		if($blnError==false && is_object($member_credits)==false){
			$blnError = true;
			$errMsg = "Error: Failed to create Member_credits object.";
		}
		
		if($blnError==false){
			if($cart_details["fulfilled"]!="Y"){
				//Need to check if the payment is captured and proceed only if the payment is captured
				if($blnError==false){
					$payment_status = $risk_score->is_payment_completed($cartId);
					if(is_bool($payment_status)==false && trim($payment_status)==""){
						$blnError = true;
						$errMsg = "Error: No payment_status returned for cart_id $cartId";
					}
					if($blnError==false && is_bool($payment_status)==false && substr($payment_status,0,6)=="Error:"){
						$blnError = true;
						$errMsg = $payment_status;//set the error message here
					}
					if($blnError==false){
						if($payment_status!==true){
							$payment_status = false;
						}else{
							$status = $member_credits->addMemberCredits($_SESSION['user_logins_id'],$cart_details["amount"],$_SESSION['user_logins_id']);
						}
					}
				}
			}else{
				$status = true;
			}
		}
		
		if($blnError==false && ($payment_status==true && $status!==true)){
			$blnError = true;
			$errMsg = $status;
		}
		
		//Now Update the fulfilled status
		if($blnError==false){
			if($cart_details["fulfilled"]!="Y" && $payment_status===true){
				$status = $shopping_cart->update_fulfilled($cartId,NULL);
			}else{
				$status = "success";
			}
		}
		
		if($blnError==false && $status != "success"){
			$blnError = true;
			$errMsg = $status;
		}
		
		if($blnError==false){
			show_thank_you_page($this_page, $payment_status);
		}else{
			die($errMsg);
		}
	}
	
	function add_credit_html(){

		global $this_page;
		
		$page_vars = $this_page->variable_stack;
		
		//Get Account Credit
		$member_credits = new Member_credits();
		if(is_object($member_credits)==false){
	  		die("Error: Failed to create Member_credits object.");
	  	}
	  	
	  	$account_credits_array = $member_credits->getTotalCredits($_SESSION['user_logins_id'], true);
		$account_credit = $account_credits_array[0];
		$allow_over_4_credits = $account_credits_array[1];
	  	$credit_added_in_30days = $member_credits->get_credit_added_in_30days($_SESSION['user_logins_id']);
	  	$credit_lots_in_30days = $member_credits->get_lots_added_in_30days($_SESSION['user_logins_id']);
	  	
	  	$cc_details = new CC_Details();
		if(is_object($cc_details)==false){
	  		die("Error: Failed to create CC_Details object.");
	  	}
	  	
		$cc_details_array = $cc_details->getCCLastFourOfUser(trim($_SESSION['user_logins_id']));
	
	    if($cc_details_array!=false){
	    	$cc_data = json_encode($cc_details_array);
	    }else{
	    	//die("Error: Failed to CC Details.");
	    }
	    $cc_count = $cc_details->get_cc_count(trim($_SESSION['user_logins_id']));
	    
	    $country = new Country();
		if(is_object($country)!=true){
			die("Error: Failed to create Country object.");
		}
		
		//$countries = $country->getAllCountry();
		
		$ip_country = get_ip_location($_SERVER["REMOTE_ADDR"]);
		
		$user = new User();
		if(is_object($user)==false){
			die("Failed to create User object.");
		}
		
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
		$auto_refill = $user_address['auto_refill'];//user->get_user_auto_refill($_SESSION["user_logins_id"]);
		$refill_amount = $user_address['auto_refill_amount'];//$user->get_user_refill_amount($_SESSION["user_logins_id"]);
		$max_30days_credit = $user_address['max_30days_acct_credit']; 
		
		if($auto_refill=='Y'){
			$refill_checked = " checked ";
		}else{
			$refill_checked = "";
		}
	
		$user_country = trim($user_address["user_country"]);
	    
	    //Get Number of Card Declined attempts
		$card_declined_attempts = $user_address['card_declined_attempts'];//$user->getCardDeclinedAttempts($_SESSION["user_logins_id"]);
		
		$blnShowCC = false;$blnShowWP = false;
		if(SITE_COUNTRY=="AU" && ($ip_country=='AU' || $ip_country=="NZ") && ($user_country=='AU' || $user_country=="NZ") && $card_declined_attempts < 3){
			$blnShowCC = true;
		}elseif(SITE_COUNTRY=="UK" && $ip_country=='GB' && $user_country=='GB' && $card_declined_attempts < 3){
			$blnShowCC = true;
		}
		
		//Override show WP
		//Get Show WP (override) setting
		$show_wp = $user_address['show_wp'];//$user->get_show_wp($_SESSION["user_logins_id"]);
		if($show_wp=="Y"){
			$blnShowCC = true;
		}
	  	
	  	$page_html = "";
	  	
	  	$page_html .="
	  	<script>
	  	
	  		//var cc_data = jQuery.parseJSON('$cc_data');
	  		var pymtType = '';
	  		var site_country = '".SITE_COUNTRY."';
	  		var ip_country = '".$ip_country."';
			var is_allow_over_4_credits = '".$allow_over_4_credits."';
	  		var credit_added_in_30days = ".$credit_added_in_30days.";
	  		var credit_lots_in_30days = ".intval($credit_lots_in_30days).";
	  		var card_declined_attempts = ".$card_declined_attempts.";
	  		var max_30days_credit = ".round($max_30days_credit,2).";\n";
	  	
	  		if($blnShowCC==true){ 
				$page_html .="var blnShowCC = true;\n"; 
			} else { 
				$page_html .= "var blnShowCC = false;\n"; 
			} 
	  		
	  		$page_html .= "$(document).ready(function(){
	  		
	  			if(blnShowCC==false){
                	pymtType = 'PayPal';
                }else{
					pymtType = 'Credit_Card';
				}
	  		
	  			$('#radPayPal').mousedown(function(event){
	  				pymtType = 'PayPal';
	  				$('#row_card_type').hide();
					$('#row_street_address').hide();
					$('#row_city').hide();
                	$('#row_country').hide();
                	$('#row_state').hide();
                	$('#row_postal_code').hide();
                	$('#radPayPal').click();
					return;
				});
				
				$('#radCreditCard').click(function(event){
                	pymtType = 'Credit_Card';
                	$('#row_card_type').show();
                	$('#row_street_address').show();
                	$('#row_city').show();
                	$('#row_country').show();
                	$('#row_state').show();
                	$('#row_postal_code').show();
                });
	  		
				$('#cc-list').change(function() {
                    var cc_id = $(this).val();
                    
                    if(cc_id==''){
                    	$('#row_street_address').show();
                    	$('#row_city').show();
                    	$('#row_country').show();
                    	$('#row_state').show();
                    	$('#row_postal_code').show();
                    }else{
                    	$('#row_street_address').hide();
                    	$('#row_city').hide();
                    	$('#row_country').hide();
                    	$('#row_state').hide();
                    	$('#row_postal_code').hide();
                    }  
                    
                }) //on cc-list change
				
                //Allow only numbers and decimal in Credit Amount field
                $('#credit-amount').keyup(function(event) {
                        var val = $(this).val();
                        if (val.length == 0) {
                            return true;
                        }
                        
                        var tmp = val;
                        
                        var last_char = val[ val.length -1 ]
                        var char_code = val.charCodeAt(val.length - 1) 
                        if (char_code!=46 && (char_code < 48 || char_code > 57)) {
                            tmp = '';
                            for(var i=0; i < val.length - 1; i++) {
                                tmp += val[i]         
                            }
                                 
                        } 
                        //Round the number to two decimals
                        
                        $(this).val( tmp )
                });
                
                //Allow only numbers and decimal in Auto Refill Amount field
                $('#refill-amount').keyup(function(event) {
                        var val = $(this).val();
                        if (val.length == 0) {
                            return true;
                        }
                        
                        var tmp = val;
                        
                        var last_char = val[ val.length -1 ]
                        var char_code = val.charCodeAt(val.length - 1) 
                        if (char_code!=46 && (char_code < 48 || char_code > 57)) {
                            tmp = '';
                            for(var i=0; i < val.length - 1; i++) {
                                tmp += val[i]         
                            }
                                 
                        } 
                        //Round the number to two decimals
                        
                        $(this).val( tmp )
                });
                
	  			                
                $('#btn-make-payment').mousedown(function(event) {
					
                	if($.trim($('#credit-amount').val()) == ''){
						event.preventDefault();
						alert('Please enter your Credit Amount');
						$('#credit-amount').focus();
						return false;
					}
                	if(credit_lots_in_30days >= 4 && is_allow_over_4_credits  == 'N'){
                		alert('Error: Maximum number of allowed credit lots in 30 days exceeded.');
                		return false;
                	}
                	if(credit_added_in_30days+parseFloat($('#credit-amount').val()) > max_30days_credit){
                		var max_can_be_added = max_30days_credit - credit_added_in_30days;
                		if(max_can_be_added <= 0){
                			max_can_be_added = 0;
                		}
                		alert('Error: Exceeds maximum account credit allowed in 30 days. Limit: max_30days_credit, Already added:'+credit_added_in_30days+',Max more that can be added:'+max_can_be_added);
                		return false;
                	}
					if($('#terms-chk-box').is(':checked') == false){
						event.preventDefault();
						alert('You must agree to the terms and conditions to proceed');
						$('#terms-chk-box').focus();
						return false;
					}
                
                	if(pymtType == 'PayPal'){
                		if($.trim($('#credit-amount').val()) == ''){
							event.preventDefault();
							alert('Please enter your Credit Amount');
							$('#credit-amount').focus();
							return false;
						}
						if(isNaN($.trim($('#credit-amount').val()))){
							event.preventDefault();
							alert('Please enter a numeric Credit Amount');
							$('#credit-amount').focus();
							return false;
						}
		  				
						//Add the record to shopping_cart.
						var cartId = get_cartId('PayPal');
						//Proceed to PayPal page
						var url='../ajax/ajax_go_to_paypal.php';
						var postdata = {'cartId' : cartId, 'allow_over_4_credit':is_allow_over_4_credits};
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
                	else if(pymtType == 'Credit_Card'){
                		var cartId = get_cartId('Credit_Card');
						
						var url = '../ajax/ajax_simplepay_generate_token.php';
						jQuery.ajaxSetup({async:false});
						$.post(url, {currency: '".SITE_CURRENCY."', amount:$.trim($('#credit-amount').val()), did_plan_period:'Y',is_recurrent:false}, function(response){
							console.log(response);
							response = $.parseJSON(response);
							console.log(response);
							if(response.id){
								var pay_url = '".SITE_URL."'+'dashboard/simple_payment.php?id='+response.id+'&cart_id='+cartId;
								//console.log(pay_url);
								location.href = pay_url;
								
							}else{
								
								console.log('error')
								return false;
							}
						
							
						});
                	}//end if validate_payment is true
                });//end of make payment mouse-down event
                
                $('#btnAutoRefill').mousedown(function(event) {
                	if($('#refill-chk-box').is(':checked') == true){
                		var auto_refill = 'Y';
                		//Validate amount field is not empty and not less than 10
                		if($.trim($('#refill-amount').val()) == ''){
							alert('Please enter your Refill Amount');
							$('#refill-amount').focus();
							return false;
						}
						if(isNaN($.trim($('#refill-amount').val()))){
							alert('Please enter a numeric Refill Amount');
							$('#refill-amount').focus();
							return false;
						}
						if(parseInt($('#refill-amount').val())<10){
							alert('Please enter a minimum value of 10 in Refill Amount');
							$('#refill-amount').focus();
							return false;
						}
						var auto_refill_amount = $('#refill-amount').val();
                	}else{
                		var auto_refill = 'N';
                		var auto_refill_amount = 0;
                	}
                	//Post these values to the ajax page to save in database
                	var url='../ajax/ajax_update_auto_refill.php';
					var postdata = {'auto_refill' : auto_refill, 'auto_refill_amount' : auto_refill_amount};
					jQuery.ajaxSetup({async:false});
					$.post(url, postdata, function(response){
						if(response.substr(0,6)!='Error:'){
							alert('Your Auto Refill updates are successful.');
							return;
						}else{
							alert(response);
							return;
						}
					});//End of $.post
                });
              
	  		});//End of $(document).ready
	  		
			
			function gotoWorldPay(){
				//Build the URL with querystring
				//cartId must be unique
				var cartId = get_cartId('WorldPay');
				var url = '".WORLDPAY_URL."&cartId='+cartId+'&amount='+$.trim($('#credit-amount').val())+'&currency=".SITE_CURRENCY."&desc=Account Credit of AUD'+$.trim($('#credit-amount').val())+'&email=".$_SESSION["user_logins_id"]."@stickynumber.net&name=".$_SESSION['user_name']."&address1='+$('#billing-street').val()+'&town='+$('#billing-city').val()+'&postcode='+$('#billing-postal-code').val()+'&country='+$('#billing-country').val()+'&hideContact=&noLanguageMenu=&hideCurrency=&compName=StickyNumber.com.au&paymentType='+$('#payment-card-type').val();
				location.href = url;
				/*jQuery.ajaxSetup({async:false});
				var posturl = 'card_details.php';
				postdata = {'wp_url' : url}
				$.post(posturl, postdata, function(response){
					if(response.substr(0,6)=='Error:'){
						alert(response);
					}else{
						$('body').html(response);
						return;
					}
				});*/
			}
			
			
			
			function get_cartId(pymt_gateway){
			
				jQuery.ajaxSetup({async:false});
				var url = '../ajax/ajax_add_to_cart.php';
				postdata = {'amount' : $.trim($('#credit-amount').val()), 
							'description' : 'Account Credit of ".SITE_CURRENCY."'+$.trim($('#credit-amount').val()),
							'return_to' : 'AccountCredit',
							'purchase_type' : 'account_credit',
							'pymt_gateway': pymt_gateway
							}
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
	  		
	  	</script>
	  	";
	  	
	  	$page_html .= '
	  		<div id="page-wrapper">
            	<div class="row">
                	<div class="col-lg-12">
                    	<h1 class="page-header">Account Credit</h1>
						<div class="row">
  							<div class="col-lg-12">
						        <div class="alert alert-info">
						          <p>Account Credit Available <strong>'.CURRENCY_SYMBOL.number_format($account_credit,2).'</strong></p>
						        </div>
						        <div class="panel panel-default">
						        	<div class="panel-heading">
						            	<h3 class="panel-title"><i class="fa fa-money fa-fw"></i>Add Account Credit</h3>
						          	</div>
						          	<div class="panel-body">
						            	<div class="col-md-6">
						            		<div class="clearfix Mtop10"></div>
						            		<input type="checkbox" id="refill-chk-box" '.$refill_checked.'>&nbsp;<b>Auto Refill Virtual Number Balance</b>
						            		<h4>Payment Options:</h4>';
						            		$page_html .= '<div class="clearfix Mtop10"></div>
            										<input type="radio" name="creditcard" id="radPayPal"';
													if($blnShowCC==false){
								            			$page_html .= ' checked=""';
								            		}
						            				$page_html .= '><span> <img src="../images/paypal.png"></span>
            										<div class="clearfix Mtop10"></div>';
						            				if($blnShowCC==true){
	            										$page_html .= '<input type="radio" name="creditcard" id="radCreditCard" checked="">
	            										<span><img src="../images/visa_curved.png"/></span>
	                                                    <span><img src="../images/mastercard_curved.png"/></span>
	                                                    <!--<span><img src="../images/maestro_curved.png"/></span>
	                                                    <span><img src="../images/jcb_curved.png"/></span>
	                                                    <span><img src="../images/diners_curved.png"/></span>-->
	                                                    <span><img src="../images/american_express_curved.png"/></span>';
						            				}else{
						            					$page_html .= '<input type="radio" name="creditcard" id="radCreditCardDisabled" disabled>
	            										<span><img src="../images/visa_curved.png"/></span>
	                                                    <span><img src="../images/mastercard_curved.png"/></span>
	                                                    <!--<span><img src="../images/maestro_curved.png"/></span>
	                                                    <span><img src="../images/jcb_curved.png"/></span>
	                                                    <span><img src="../images/diners_curved.png"/></span>-->
	                                                    <span><img src="../images/american_express_curved.png"/></span>Disabled. Contact support.';
						            				}
                                                    $page_html .= '<div class="clearfix Mtop10"></div>
                                                    <input type="checkbox" id="terms-chk-box"><a href="../terms/" target="_blank"> I agree to the Terms and Conditions</a> <span class="txt-red">*</span>
								            	</div>
						            			<div class="col-md-6">
						            				<form class="form-horizontal" role="form">
            											<div class="form-group">
									                        <label class="col-sm-4 control-label">Refill Amount '.CURRENCY_SYMBOL.'<span class="txt-red">*</span></label>
									                        <div class="col-xs-5">
																<input name="refill-amount" id="refill-amount" type="text" class="form-control" placeholder="Auto Refill Amount" value='.$refill_amount.'>
									                        </div>
									                        <button type="button" name="btnAutoRefill" value="Update" alt="Update" title="click to update" class="btn btn-primary" id="btnAutoRefill">Update</button>
									                    </div>
									                </form>
            										<form class="form-horizontal" role="form">
            											<div class="form-group">
									                        <label class="col-sm-4 control-label">Credit Amount '.CURRENCY_SYMBOL.'<span class="txt-red">*</span></label>
									                        <div class="col-xs-5">
																<input name="credit-amount" id="credit-amount" type="text" class="form-control" placeholder="Credit Amount">
									                        </div>
									                    </div>';
									                    
            										$page_html.='</form>
            									</div>
						            	</div>
						            	<a  id="btn-make-payment" class="btn blue btn-lg m-icon-big fltrt">
									 		Make Payment <i class="m-icon-big-swapright m-icon-white"></i>
										</a>
						            </div>
						        </div>
						    </div>
						</div>
					</div>
				</div>
			</div>
	  	';
	  	
		return $page_html;
	}
	
	function add_thank_you_html($payment_status){

		global $this_page;
		
		$page_vars = $this_page->variable_stack;
		
		//Get Account Credit
		$member_credits = new Member_credits();
		if(is_object($member_credits)==false){
	  		die("Error: Failed to create Member_credits object.");
	  	}
	  	
	  	$account_credit = $member_credits->getTotalCredits($_SESSION['user_logins_id']);
	  	
	  	$page_html = "";
	  	
	  	/*$page_html.='
	
		<!-- Main Container -->
                                <div class="contentbox">
                                     <div class="contentbox-head">
                                     	<div class="contentbox-head-title">Account Credit Available <font color="green">$<span id="avbl-acct-credit">'.number_format($account_credit,2).'</span></font></div>
                                          <div class="row Mtop20">
                                              <div class="subsection">
                                                    <div class="row Mtop10">
                                                    Thank you! <br/><br>';
	  													if($payment_status===true){
	  														$page_html.='Your account credit is successfully added.';
	  													}else{
	  														$page_html.='Your account credit order is under process now.';
	  													}
                                        $page_html.='</div>
                                              </div>
                                          </div>                                          
                                           
                                 <!-- Personal Details closed -->         
                                          
                                          
                                     </div>
                                </div>
                                <!-- Main Container closed -->
	
		';*/
	  	
	  		$page_html .= '
		<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Add Account Credit</h1></div>
                	<div class="row">                
                		<div class="col-lg-12">
							<div class="contentbox">
                            	<div class="contentbox-head">
                                	<div class="row">
	                                	<div class="col-md-6 col-md-offset-3">
	                                    	<div class="alert alert-success  Mtop20">';
	  											$page_html .= '<h1 class="txt-c">Thank You!</h1><br>';
												if($payment_status!==true){
													$page_html .= 'Your account credit order is under process now.';
												}else{
													$page_html .= 'Your account credit is successfully added.';
													$page_html .= '<p>Account Credit Available <font color="green">'.CURRENCY_SYMBOL.'<span id="avbl-acct-credit">'.number_format($account_credit,2).'</span></font></p>';
												}
												$page_html.='<p>Contact us 24/7 About your order or if you have any other enquires.
													Please Check your email for order confirmation and Invoice</p>';
	$page_html.='</div>                                        
	   </div>  </div>                                                                                
	                                 <!-- Personal Details closed -->         
	                                          
	                                          
	                                     </div>
									</div>                    
                    </div>
                    <!-- /.col-12 -->                 
                </div><!-- /.row -->
            </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->
	';
	  	
		return $page_html;
	}
    
    require("includes/DRY_authentication.inc.php");