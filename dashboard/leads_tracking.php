<?php

require "./includes/DRY_setup.inc.php";
require "./classes/Lead_accounts.class.php";
require "./classes/Leads.class.php";
	
function get_page_content($this_page){
	
	if(isset($_SESSION['user_logins_id'])==false || trim($_SESSION['user_logins_id'])==""){
		echo "Error: No user session found. Please login again.";
		exit;
	}else{
		$user_id = trim($_SESSION['user_logins_id']);
	}
	
	$assigned_did = new Assigned_DID();
	if(is_object($assigned_did)==false){
		echo "Error: Failed to create Assigned_DID object.";
		exit;
	}
	
	$lead_accounts_obj = new Lead_accounts();	
	if(is_object($lead_accounts_obj)==false){
		echo "Error: Failed to create Lead_accounts object.";
		exit;
	}
	
	$leads_obj = new Leads();
	if(is_object($leads_obj)==false){
		echo "Error: Failed to create Leads object.";
		exit;
	}
	
	//Check if the form is submitted to go to the payment page
	if(isset($_POST["payment"])==true && trim($_POST["payment"])=="yes"){
		$this_page->content_area .= getLeadTrackingPaymentHTML();
		common_header($this_page,"'Leads Tracking'");
		return;
	}elseif(isset($_POST["cartId"])==true && trim($_POST["cartId"])!=""){//Check if the payment is received to activate leads tracking plan
		//Proces Purchase
		$status = process_purchase(trim($_POST["cartId"]));
		if($status!==true){
			echo $status;
			exit;
		}
	}elseif(isset($_POST["remove_did_id"])==true && trim($_POST["remove_did_id"])!=""){
		//Update the assigned_did record
		$did_id = trim($_POST["remove_did_id"]);
		$status = $assigned_did->remove_lead_tracking($did_id);
		if($status!==true && substr($status,0,6)=="Error:"){
			echo $status;//Echo the error message
			exit;
		}
		//Now delete the leads record
		$status = $leads_obj->delete_lead_by_lead_did_id($did_id);
		if($status!==true && substr($status,0,6)=="Error:"){
			echo $status;//Echo the error message
			exit;
		}
	}elseif(isset($_POST["lead_reporing_email"])==true && trim($_POST["lead_reporing_email"])!=""){
		//Update the lead accounts record
		$email_id = trim($_POST["lead_reporing_email"]);
		$status = $lead_accounts_obj->update_lead_reporting_email($user_id,$email_id);
		if($status!==true && substr($status,0,6)=="Error:"){
			echo $status;//Echo the error message
			exit;
		}
		$alert_message = "Success: Leads reporting email updated.";
		$this_page->content_area .= getLeadConfigureHTML($alert_message);
		common_header($this_page,"'Leads Tracking - Configure'");
		return;
	}elseif(isset($_POST["configure_plan"])==true && trim($_POST["configure_plan"])=="yes"){
		$this_page->content_area .= getLeadConfigureHTML();
		common_header($this_page,"'Leads Tracking - Configure'");
		return;
	}elseif(isset($_POST["view_completed_leads"])==true && trim($_POST["view_completed_leads"])=="yes"){
		$this_page->content_area .= getCompletedLeadsViewHTML();
		common_header($this_page,"'Leads Tracking - View Completed Leads'");
		return;
	}
	
	//Check if the leads tracking is activated in this users account
	$lead_accounts = $lead_accounts_obj->get_lead_accounts($user_id);
	if(is_array($lead_accounts)==false && substr($lead_accounts,0,6)=="Error:"){
		echo $lead_accounts;
		exit;
	}
	if(is_array($lead_accounts)==false && $lead_accounts==0){
		$this_page->content_area .= getActivateLeadTrackingHTML();
		common_header($this_page,"'Leads Tracking'");
	}elseif(is_array($lead_accounts)==true){
		$this_page->content_area .= getLeadTrackingHTML();
		common_header($this_page,"'Leads Tracking'");
	}else{
		echo "Error: Unexpected response from Lead_accounts : ".print_r($lead_accounts);
		exit;
	}
		
}

function process_purchase($cartId){
	
	$blnError=false;
	
	//Get shopping_cart details
	$shopping_cart = new Shopping_cart();
	if(is_object($shopping_cart)==false){
		$blnError = true;
		$errMsg = "Error: Failed to create Shopping_cart object";
	}
	
	$cart_details = "";
	if($blnError==false){
		$cart_details = $shopping_cart->get_cart_details($cartId);
	}
	
	if($blnError==false && is_array($cart_details)==false){
		$blnError = true;
		$errMsg = $cart_details;
	}
	
	if($blnError==false){
		if($cart_details["paid"]=="Y" && $cart_details["fulfilled"]!="Y"){//If paid but not yet fulfilled
			$lead_accounts = new Lead_accounts();
			if(is_object($lead_accounts)==false){
				$blnError = true;
				$errMsg = "Error: Failed to create Lead_accounts object.";
			}
			if($blnError==false){
				$status =$lead_accounts->insert_or_renew_lead_account($cartId);
				if(is_bool($status) && $status===true){
					//Update the shopping_cart as fulfilled
					$status = $shopping_cart->update_fulfilled($cartId, "");
					if($status!="success"){
						$blnError = true;
						$errMsg = $status;
					}
				}
			}
		}
	}
	if($blnError==false){
		return true;
	}else{
		return $errMsg;
	}
	
	
}



function getActivateLeadTrackingHTML(){
	
	global $this_page;
	
	$user_id = trim($_SESSION['user_logins_id']);
	
	//Now get the leads tracking price and apply the exchange rate
	$leads_plan_whole_dollars = round(get_leads_tracking_rate_in_user_currency($user_id));
	$leads_plan_cents = "00";
	
	$page_html = '';
	
	$page_html .="
	
	<script>
	
			$(document).ready(function(){
				
                $('#btn-choose-leads-plan').click(function(event) {
                	$('#frm-step1').submit();
                })
			
			});//End of $(document).ready(function()
			
	</script>
	
	";
	
	$page_html .= '
			<div id="page-wrapper">
            	<div class="row">
                	<div class="col-lg-12">
                    	<h1 class="page-header">Activate Leads Tracking</h1>
                    	<div class="row">
                    		<div class="col-lg-12">
                    			<div class="contentbox">
                    				<div class="contentbox-head">
                    					<div class="content-breadbox">
			                            	<ul class="breadcrumb2">
			                                	<li ><a class="breadcrumb-first breadcrumb-page" href="#">Choose Plan</a></li>
			                                    <li><a href="#" class="disable-breadcrumb">Payment</a></li>
			                                </ul>
			                            </div>
			                            <div class="Mtop20">
				                            <div class="row Mtop20">
				                            	<div class="col-sm-3 Mtop20">
				                            		<div class="panel panel-default text-center panelbox">
				                            			<div class="panel-heading">
											              	<h4 class="panel-title price text-success">'.CURRENCY_SYMBOL.$leads_plan_whole_dollars.'<span class="price-cents"></span><span class="price-month">Monthly</span></h4>
											            </div>
											            <ul class="list-group">
											            	<li class="list-group-item">Auto Assigns Numbers</li>
										              		<li class="list-group-item">Lead Tracking</li>
										             		<li class="list-group-item">Lead Recording</li>
										             		<li class="list-group-item"><a id="btn-choose-leads-plan" class="btn btn-success">Choose</a></li>
											            </ul>
				                            		</div>
				                            	</div>
				                            </div>
				                        </div>	
                    				</div>
                    			</div>
                    		</div>
                    	</div>
                    </div>
                </div>
            </div> 
            
            <form method="post" action="leads_tracking.php" name="frm-step1" id="frm-step1">
            	<input type="hidden" value="yes" name="payment" id="payment">
            </form>
	';//End of $page_html
	
	
	
	return $page_html;
	
}

function getLeadTrackingPaymentHTML(){
	
	global $this_page;
	
	$user_id = trim($_SESSION['user_logins_id']);
	
	$pay_amount = round(get_leads_tracking_rate_in_user_currency($user_id));
	
	//Get Account Credit
	$member_credits = new Member_credits();
  	if(is_object($member_credits)==false){
  		die("Error: Failed to create Member_credits object.");
  	}
  	$blnSufficientCredit = false;
  	$font_color = "red";
  	$acct_credit_radio = "&nbsp;";
  	$account_credit = $member_credits->getTotalCredits($_SESSION['user_logins_id']);
  	if(intval($account_credit*100) >= intval($pay_amount*100)){
  		$blnSufficientCredit = true;
  		$font_color = "green";
	}
	$acct_credit_radio = '<input type="radio" name="creditcard" checked="checked" id="radAcctCredit" />&nbsp;Account Credit (<font color="'.$font_color.'">Available: '.CURRENCY_SYMBOL.$account_credit.', Required: '.CURRENCY_SYMBOL.$pay_amount.'</font>)';
	
	$page_html = '';
	
	$page_html .="
	
	<script>
	
			var pymtType = 'AcctCredit';
			var blnSufficientCredit = '".$blnSufficientCredit."';		
	
			$(document).ready(function(){
				
                $('#btn-make-payment').click(function(event) {
                	if(pymtType == 'AcctCredit'){
						if($('#terms-chk-box').is(':checked') == false){
    						alert('You must agree to the terms and conditions to proceed');
    						$('#terms-chk-box').focus();
    						return false;
    					}
    				}
    				if(blnSufficientCredit==0){
    					alert('You do not have sufficient account credit (Required: ".html_entity_decode(CURRENCY_SYMBOL).$pay_amount.", Available: ".html_entity_decode(CURRENCY_SYMBOL).$account_credit.") to pay for this purchase.\\n\\nPlease buy more account credit and then try again.');
    					return false;
    				}
    				if(confirm('".html_entity_decode(CURRENCY_SYMBOL)."$pay_amount will be deducted from your account credit. Please confirm.')==true){
    					var cartId = get_cartId('AccountCredit');
						var url = '../ajax/ajax_charge_acct_credit.php';
						var choosen_plan = 0;
						var city_id = 0;
						var plan_period = 'M';
						var num_of_months = 1;//Hard coded until annual payment or multiple months is allowed on front end
						var did_id = 0;
						jQuery.ajaxSetup({async:false});
						$.post(url,{'cart_id': cartId, 
										'choosen_plan' : choosen_plan, 
										'city_id' : city_id, 
										'plan_period' : plan_period,
										'num_of_months' : num_of_months,
										'did_id' : did_id
										},
										function(response){
											if(response.substr(0,7)=='Success'){
												$('#cartId').val(cartId);
												$('#frm-step2').submit();
												return;
											}else{
												alert(response);
												return;
											}
							});
    				}else{
    					event.preventDefault();
    				}
                });
			
			});//End of $(document).ready(function()
			
			function get_cartId(pymt_gateway){
					
				jQuery.ajaxSetup({async:false});
				var url = '../ajax/ajax_add_to_cart.php';
				
				return_to = 'LeadsTracking';
				description = 'Leads Tracking Plan';
				purchase_type = 'leads_tracking';
				postdata = {'amount' : ".$pay_amount.", 
							'description' : description,
							'num_of_months' : 1,
							'return_to' : return_to,
							'purchase_type' : purchase_type,
							'did_id' : 0,
							'did_number' : 0,
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
	
	if(isset($_POST["renew"]) && trim($_POST["renew"])=="yes"){
		$action = "Renew";
	}else{
		$action = "Activate";
	}
	
	$page_html .= '
			<div id="page-wrapper">
            	<div class="row">
                	<div class="col-lg-12">
                    	<h1 class="page-header">'.$action.' Leads Tracking</h1>
                    	<div class="row">
                    		<div class="col-lg-12">
                    			<div class="contentbox">
                    				<div class="contentbox-head">
                    					<div class="content-breadbox">
			                            	<ul class="breadcrumb2">
			                                	<li ><a class="breadcrumb-first" href="leads_tracking.php">Choose Plan</a></li>
			                                    <li><a href="#" class="breadcrumb-page">Payment</a></li>
			                                </ul>
			                            </div>
			                            
			                            <div class="row">
										 	<div class="col-lg-12 Mtop20"> 
										    	<div class="panel panel-default">
										        	<div class="panel-body">
										            	<div class="col-md-6">
										            		<h4>Payment Options:</h4>';
								            				$page_html .= $acct_credit_radio;
								            				$page_html .= '<div class="clearfix Mtop10"></div>
								            				<input type="checkbox" id="terms-chk-box"><a href="../terms/" target="_blank"> I agree to the Terms and Conditions</a> <span class="txt-red">*</span>';
										  $page_html .= '</div>
			                            			</div>
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
            </div>
            
            <form method="post" action="leads_tracking.php" name="frm-step2" id="frm-step2">
            	<input type="hidden" value="" name="cartId" id="cartId">
            </form>
	';//End of $page_html
	
	return $page_html;
	
}

function getLeadTrackingHTML(){
	
	global $this_page;
	
	$user_id = trim($_SESSION['user_logins_id']);
	
	$leads_obj = new Leads();
	if(is_object($leads_obj)==false){
		echo "Error: Failed to create Leads object.";
		exit;
	}
	
	$assigned_did = new Assigned_DID();
	if(is_object($assigned_did)==false){
		echo "Error: Failed to create Assigned_DID object.";
		exit;
	}
	$lead_dids_count = $assigned_did->get_lead_dids_count($user_id);
	if(substr($lead_dids_count,0,6)=="Error:"){
		echo $lead_dids_count;
		exit;
	}
	
	$page_count = 1;//Initialize
	$records_per_page = RECORDS_PER_PAGE;
	if($lead_dids_count > $records_per_page){
		//Needs to do pagination here
		$page_count = ceil($lead_dids_count / $records_per_page);
		$page_deck = array();
		for($page=1;$page<=$page_count;$page++){
			$first_record_in_range = $page * $records_per_page - ($records_per_page -1 );
			$last_record_in_range = $page * $records_per_page;
			if($last_record_in_range > $lead_dids_count){
				$last_record_in_range = $lead_dids_count;
			}
			$page_deck[$page] = $first_record_in_range ." - ".$last_record_in_range;
		}
		//now figure out what page we are on
		$page_current = 1;
		$safe_page_select = $_POST['paginate'];
		if($safe_page_select!=""){
			$safe_page_select = intval($safe_page_select);
		}
		if($safe_page_select > 0 && $safe_page_select != $page_current){
			$page_current  = $safe_page_select;
		}
		$page_current_range_start = $page_current * $records_per_page - ($records_per_page -1 );
		$page_current_range_end = $page_current * $records_per_page;
		if ($page_current_range_end > $lead_dids_count){
			$page_current_range_end = $lead_dids_count;
		}
	}else{//End of pagination code
		$page_current_range_start = 1;//No multiple pages	
	}
	if($lead_dids_count>0){
		$lead_tracking_dids = $assigned_did->getLeadDIDsForPage($user_id,$page_current_range_start-1);
		if(is_array($lead_tracking_dids)==false && substr($lead_tracking_dids,0,6)=="Error:"){
			echo $lead_tracking_dids;
			exit;
		}
	}
	
	
	$lead_accounts_obj = new Lead_accounts();	
	if(is_object($lead_accounts_obj)==false){
		return "Error: Failed to create Lead_accounts object.";
	}
	$lead_accounts = $lead_accounts_obj->get_lead_accounts($user_id);
	if(is_array($lead_accounts)==false && substr($lead_accounts,0,6)=="Error:"){
		return $lead_accounts;
	}
	if(is_array($lead_accounts)==false){
		if($lead_accounts==0){
			return "Error: No leads accounts found for user id $user_id";
		}else{
			return "Error: Unexpected result while querying for leads account for user id $user_id. Result is : ".print_r($lead_accounts);
		}
	}
	
	if($lead_accounts["secs_to_expire"]>0){
		$status = "<font color=\"green\">Active</font>";
	}else{
		$status = "<font color=\"green\">Expired</font>";
	}
	
	
	$page_html = '';
	
	$page_html .="
		<script>
			
			function renew_leads_tracking(){
			
				$('#frm-step1').submit();
			
			}
			
			function add_new_number(){
			
				$('#frm-add-new-number').submit();
			
			}
			
			function remove_tracking(did_id){
			
				$('#remove_did_id').val(did_id);
				$('#frm-remove-number').submit();
			
			}
			
			function configure_tracking(did_id){
			
				$('#frm-configure-plan').submit();
			
			}
			
			function view_completed_leads(){
			
				$('#frm-view-completed-leads').submit();
			
			}
			
		</script>
	";
	
	$page_html .= '
			<div id="page-wrapper">
            	<div class="row">
                	<div class="col-lg-12">
                    	<h1 class="page-header">My Leads Plan</h1>
                    	<div class="table-responsive">
                    		<table class="table table-hover">
                    			<thead class="blue-bg">
                    				<tr>
                    					<th>Action</th>
										<th align="center">Start Date</th>
										<th>Renewal Due Date</th>
										<th>Renew</th>
										<th>DIDs</th>
										<th>Status</th>
									</tr>
								</thead>';
					$page_html .= '<tbody><tr>';
					$page_html .= '<td><button type="button" name="configure_tracking" class="btn btn-success addpads" onclick="configure_tracking();">Configure</button></td>';
					$page_html .= '<td>'.$lead_accounts["date_created"].'</td>';
					$page_html .= '<td>'.$lead_accounts["next_renewal_on"].'</td>';
					$page_html .= '<td><button name="renew_leads_tracking" class="btn btn-success addpads" onclick="renew_leads_tracking()">Renew</button>';
					$page_html .= '<td><button name="add_new_number" class="btn btn-success addpads" onclick="add_new_number()">Add DID</button>';
					$page_html .= '<td>'.$status.'</td>';
					$page_html .= '</tr>';				
                   $page_html .= '</tbody>
                    		</table>
                    	</div>';
                   $page_html .= '<button type="button" name="view_completed_leads" class="btn btn-success addpads" onclick="view_completed_leads();">View Completed Leads</button>';
                   if($lead_dids_count>0){
                   		$page_html .= '
                   			<h2>My Lead DIDs</h2>
                   			<div class="table-responsive">
                   				<table class="table table-hover">
                   					<thead class="blue-bg">
	                    				<tr>
	                    					<th>Tracking</th>
											<th>Virtual Number(Avbl. Bal)</th>
											<th>Lead Details</th>
											<th>Expiry(GMT)</th>
											<th align="middle">status</th>
										</tr>
									</thead>
									<tbody>';
                   					if($lead_dids_count>0 && $lead_accounts["secs_to_expire"]>0){
										foreach($lead_tracking_dids as $lead_did){
											$blnAssigned = false;
											if($lead_did['lead_did_in_use']=='Y'){
												$blnAssigned = true;
											}else{
												$blnAssigned = false;
											}
											if($blnAssigned==true){
												$lead_details = $leads_obj->get_lead_details($lead_did['id']);
												if(is_array($lead_details)==false && $lead_details==0){
													$lead_details_text = "<font color='red'>Not Assigned</font>";
												}elseif(is_array($lead_details)==false && substr($lead_details,0,6)=="Error:"){
													$lead_details_text = $lead_details;
												}elseif(is_array($lead_details)==true){
													$lead_details_text = $lead_details["name"].'|'.$lead_details["email"].'<br />Forwards to: '.$lead_details["phone1"].'<br />'.$lead_details['received_on'].' from IP: '.$lead_details["ipaddress"];
												}
											}else{
												$lead_details_text = "<font color='red'>Not Assigned</font>";
											}
											if($blnAssigned==true){
												$tracking_html = '<button type="button" name="remove_tracking" class="btn btn-success addpads" onclick="remove_tracking('.$lead_did["id"].');">Completed</button>';
											}else{
												$tracking_html = "<font color='red'>Not Assigned</font>";
											}
											if(substr($lead_did['did_number'],0,3)=="070" || substr($lead_did['did_number'],0,3)=="087" || substr($lead_did['did_number'],0,4)=="4470" || substr($lead_did['did_number'],0,4)=="4487"){
												$paid_did = false;
												$expiry_date = "N/A";
											}else{
												$paid_did = true;
												$expiry_date = $lead_did["did_expires_on"];
											}
											$status = "";
											if(intval($lead_did["secs_to_expire"]) > 0 || $paid_did==false){//Active
												$status = '<font color="green">Active</font>';
											}else{
												$status = '<font color="red">Expired</font>';
											}
											$page_html .= '
												<tr>
													<td>'.$tracking_html.'</td>
													<td>'.$lead_did["did_number"].'('.CURRENCY_SYMBOL.number_format($lead_did["balance"],2).')</td>
													<td>'.$lead_details_text.'</td>
													<td>'.$expiry_date.'</td>
													<td>'.$status.'</td>
												</tr>
											';
										}//end of foreach loop	
                   					}else{
                   						if($lead_accounts["secs_to_expire"]<=0){
                   							$page_html .= '<tr><td colspan="6"><font color="red">Please renew your leads tracking plan to view the lead DIDs.</font></td></tr>';
                   						}else{
                   							$page_html .= '<tr><td colspan="6"><font color="red">No lead tracking DIDs added. Please add them above.</font></td></tr>';
                   						}
                   					}
						$page_html .= '</tbody>
                   				</table>
                   			</div>
                   		';
                   }
                   if($page_count > 1){	
						$page_html .= '<form name="frmPaginate" id="frmPaginate" action="leads_tracking.php" method="post">';
						$page_html .= '<p align="right">Showing '.$page_current_range_start.'-'.$page_current_range_end.' of '.$lead_dids_count."\n"; 
						$page_html .= '<select name="paginate" onchange="javascript:document.frmPaginate.submit();">'."\n";
						$i=0;
						foreach($page_deck as $page_range){
							$i++;
							$page_html .= '<option value="'.$i.'">'.$page_range.'</option>'."\n";
						}			 
						$page_html .= '</select>
					</p></form>';
					}//end of if page count > 1
                   
                   $page_html .= '</div>
                </div>
            </div>
            <form method="post" action="leads_tracking.php" name="frm-step1" id="frm-step1">
            	<input type="hidden" value="yes" name="payment" id="payment">
            	<input type="hidden" value="yes" name="renew" id="renew">
            </form>
            <form method="post" action="add-number-1.php" name="frm-add-new-number" id="frm-add-new-number">
				<input type="hidden" value="yes" name="add_lead_did" id="add_lead_did">
            </form>
            <form method="post" action="leads_tracking.php" name="frm-remove-number" id="frm-remove-number">
				<input type="hidden" value="" name="remove_did_id" id="remove_did_id">
            </form>
            <form method="post" action="leads_tracking.php" name="frm-configure-plan" id="frm-configure-plan">
				<input type="hidden" value="yes" name="configure_plan" id="configure_plan">
            </form>
            <form method="post" action="leads_tracking.php" name="frm-view-completed-leads" id="frm-view-completed-leads">
				<input type="hidden" value="yes" name="view_completed_leads" id="view_completed_leads">
            </form>
	';//End of $page_html
	
	return $page_html;
	
}

function getLeadConfigureHTML($alert_message=""){
	
	global $this_page;
	
	$user_id = trim($_SESSION['user_logins_id']);
	
	$lead_accounts_obj = new Lead_accounts();
	if(is_object($lead_accounts_obj)==false){
		return "Error: Failed to create Lead Accounts object.";
	}
	$lead_account = $lead_accounts_obj->get_lead_accounts($user_id);
	if(is_array($lead_account)==false && substr($lead_account,0,6)=="Error:"){
		return $lead_account;
	}elseif(is_array($lead_account)==false && intval($lead_account)==0){
		return "Error: No leads tracking account found for user id $user_id";
	}elseif(is_array($lead_account)==false){
		return "Error: Unexpected result when getting lead account. Result is not an array.";
	}
	
	$page_html = '';
	
	$page_html .= '<link rel="stylesheet" href="../css/spectrum.css" />';
	$page_html .= '<script src="./js/spectrum.js"></script>';
	
	if($alert_message!=""){
		
		$page_html .= "<script>alert('".$alert_message."');</script>";
		
	}
	
	$page_html .= "
		<script>
			
			function update_lead_email(){
				
				//Validate if the email id entered is correct
				var email_id = $('#lead_reporing_email').val();
				if(validateEmail(email_id)==false){
					alert('Please enter a valid email id');
					$('#lead_reporing_email').focus();
					return false;
				}else{
					$('#frm_reporing_email').submit();
				}
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
		
			function insertAndExecute(id, text)
			  {
			    domelement = document.getElementById(id);
			    domelement.innerHTML = text;
			    var scripts = [];
			
			    ret = domelement.childNodes;
			    for ( var i = 0; ret[i]; i++ ) {
			      if ( scripts && domnodeName( ret[i], 'script' ) && (!ret[i].type || ret[i].type.toLowerCase() === 'text/javascript') ) {
			            scripts.push( ret[i].parentNode ? ret[i].parentNode.removeChild( ret[i] ) : ret[i] );
			        }
			    }
			
			    for(script in scripts)
			    {
			      evalScript(scripts[script]);
			    }
			  }
			  function domnodeName(elem, name){
			    return elem.nodeName && elem.nodeName.toUpperCase() === name.toUpperCase();
			  }
			  function evalScript( elem ) {
			    data = ( elem.text || elem.textContent || elem.innerHTML || '' );
			
			    var head = document.getElementsByTagName('head')[0] || document.documentElement,
			    script = document.createElement('script');
			    script.type = 'text/javascript';
			    script.appendChild( document.createTextNode( data ) );
			    head.insertBefore( script, head.firstChild );
			    head.removeChild( script );
			
			    if ( elem.parentNode ) {
			        elem.parentNode.removeChild( elem );
			    }
			  }	
		
			function get_code(){
				
				//Get the inputs
				var user_id = ".$user_id.";
				var bg_color = $('#bg_color').val();
				if(bg_color==''){
					alert('Please select a background color');
					$('#bg_color').focus();
					return;
				}
				var txt_color = $('#txt_color').val();
				if(txt_color==''){
					alert('Please select a text color');
					$('#txt_color').focus();
					return;
				}
				var fontfamily = $('#fontfamily').val();
				var fontsize = $('#fontsize').val();
				var borderwidth = $('#borderwidth').val();
				var form_width = $('#form_width').val();
				if(form_width==''){
					alert('Please enter form width');
					$('#txt_color').focus();
					return;
				}
				var form_height = $('#form_height').val();
				if(form_height==''){
					alert('Please enter form height');
					$('#txt_color').focus();
					return;
				}
			
				//alert(bg_color+','+txt_color+','+fontfamily+','+fontsize+','+borderwidth+','+form_width+','+form_height);
				
				//Get the code through AJAX
				var url='../ajax/ajax_leads_code.php';
				var postdata = {'user_id' : user_id, 
								'bg_color' : bg_color, 
								'txt_color' : txt_color, 
								'fontfamily' : fontfamily, 
								'fontsize' : fontsize, 
								'borderwidth' : borderwidth, 
								'form_width' : form_width, 
								'form_height' : form_height };
				jQuery.ajaxSetup({async:false});
				$.post(url, postdata, function(response){
					if(response.substr(0,6)!='Error:'){
						$('#source').val(response);
						//document.getElementById('div_form_display').innerHTML = response;
						insertAndExecute('div_form_display',response);
						return;
					}else{
						alert(response);
						return;
					}
				});//End of $.post
				
			}
		
		</script>
	";
	
	$page_html .= '
			<div id="page-wrapper">
            	<div class="row">
                	<div class="col-lg-12">
                    	<h1 class="page-header">Configure Leads Plan - Form Designer:</h1>
                    	<div class="row">                
                			<div class="col-lg-12">
                				<div class="contentbox">
                				
                					<div class="row Mtop15">
										<div class="col-md-2">Reporting Email: </div>
										<div class="col-md-10">
											<form name="frm_reporing_email" id="frm_reporing_email" method="post" action="leads_tracking.php">
												<input type="text" size="50" name="lead_reporing_email" id="lead_reporing_email" value="'.$lead_account["lead_reporting_email"].'" />
												<button type="button" name="btn_lead_reporing_email" class="btn btn-success addpads" onclick="update_lead_email();">Update</button>
											</form>
										</div>
									</div>
                				
                					<h3>Form required Details</h3>
									<div class="row Mtop15">
									  <div class="col-md-2">Background</div>
									  <div class="col-md-10"><input type="text" class="custom" name="bg_color" id="bg_color" /></div>
									</div>
									<div class="row Mtop15">
									  <div class="col-md-2">Text color</div>
									  <div class="col-md-10"><input type="text" class="custom" name="txt_color" id="txt_color" /></div>
									</div>
									<div class="row Mtop15">
									  	<div class="col-md-2">Text Font</div>
										  <div class="col-md-10">
										  	<select name="fontfamily" id="fontfamily">
												<option value="arial" style="font-family:arial"> Arial</option>
												<option value="helvetica" style="font-family:helvetica"> Helvetica</option>
												<option value="verdana" style="font-family:verdana"> Verdana</option>
												<option value="tahoma" style="font-family:tahoma"> Tahoma</option>
												<option value="times" style="font-family:times roman"> Times Roman</option>
												<option value="georgia" style="font-family:georgia"> Georgia</option>
												<option value="garamond" style="font-family:garamond"> Garamond</option>
												<option value="cursive" style="font-family:cursive"> Cursive</option>
											</select>
										</div>
									</div>
									<div class="row Mtop15">
									  <div class="col-md-2">Text size</div>
										 <div class="col-md-10">
										  	<select name="fontsize" id="fontsize">
												<option value="8pt"> 8pt</option>
												<option value="9pt"> 9pt</option>
												<option value="10pt"> 10pt</option>
												<option value="12pt"> 12pt</option>
												<option value="14pt"> 14pt</option>
												<option value="16pt"> 16pt</option>
												<option value="18pt"> 18pt</option>
											</select>
										</div>
									</div>
									<div class="row Mtop15">
									  <div class="col-md-2">Border Size</div>
										  <div class="col-md-10">
										  	<select name="borderwidth" id="borderwidth">
												<option value="0"> 0 pixels</option>
												<option value="1" selected="selected"> 1 pixel</option>
												<option value="2"> 2 pixels</option>
												<option value="3"> 3 pixels</option>
												<option value="4"> 4 pixels</option>
												<option value="5"> 5 pixels</option>
												<option value="6"> 6 pixels</option>
											</select>
										</div>
									</div>
									<div class="row Mtop15">
									  <div class="col-md-2">Form Size</div>
									  <div class="col-md-10"><input type="text" size="7" placeholder="Width" name="form_width" id="form_width" /> x <input type="text" size="7" placeholder="Height" name="form_height" id="form_height" /> in Pixels</div>
									</div>
									<div class="row Mtop15">
									  <div class="col-md-2">Code</div>
									  <div class="col-md-10"><textarea name="source" id="source" rows="4" cols="70" onclick="this.focus();this.select()"></textarea></div>
									</div>
									<div class="row Mtop15">
										<div class="col-md-2">&nbsp;</div>
										<div class="col-md-10"><button type="submit" class="btn btn-lg btn-primary Mtop20 pull-left" onclick="get_code()">Get Code</button></div>
									</div>
									<div class="row Mtop15">
										<div class="col-md-2">&nbsp;</div>
										<div class="col-md-10"><div id="div_form_display"></div></div>
									</div>
                				</div>
                			</div>
                		</div>
                    </div>
                </div>
            </div>        	
    ';
	
	$page_html .= '
		<script>
		$(".custom").spectrum({
		    color: "#f00", 
		    preferredFormat:"hex"
		});
		</script>
	';
	
	return $page_html;
	
}

function getCompletedLeadsViewHTML(){
	
	global $this_page;
	
	$user_id = trim($_SESSION['user_logins_id']);
	
	$leads_obj = new Leads();
	if(is_object($leads_obj)==false){
		return "Error: Failed to create Leads object.";
	}
	
	$completed_leads_count = $leads_obj->get_completed_leads_count($user_id);
	if(substr($completed_leads_count,0,6)=="Error:"){
		echo $completed_leads_count;
		exit;
	}
	
	$page_count = 1;//Initialize
	$records_per_page = RECORDS_PER_PAGE;
	if($completed_leads_count > $records_per_page){
		//Needs to do pagination here
		$page_count = ceil($completed_leads_count / $records_per_page);
		$page_deck = array();
		for($page=1;$page<=$page_count;$page++){
			$first_record_in_range = $page * $records_per_page - ($records_per_page -1 );
			$last_record_in_range = $page * $records_per_page;
			if($last_record_in_range > $completed_leads_count){
				$last_record_in_range = $completed_leads_count;
			}
			$page_deck[$page] = $first_record_in_range ." - ".$last_record_in_range;
		}
		//now figure out what page we are on
		$page_current = 1;
		$safe_page_select = $_POST['paginate'];
		if($safe_page_select!=""){
			$safe_page_select = intval($safe_page_select);
		}
		if($safe_page_select > 0 && $safe_page_select != $page_current){
			$page_current  = $safe_page_select;
		}
		$page_current_range_start = $page_current * $records_per_page - ($records_per_page -1 );
		$page_current_range_end = $page_current * $records_per_page;
		if ($page_current_range_end > $completed_leads_count){
			$page_current_range_end = $completed_leads_count;
		}
	}else{//End of pagination code
		$page_current_range_start = 1;//No multiple pages	
	}
	
	if($completed_leads_count>0){
		$leads = $leads_obj->getCompletedLeadsForPage($user_id,$page_current_range_start-1);
		if(is_array($leads)==false && substr($leads,0,6)=="Error:"){
			echo $leads;
			exit;
		}
	}
	
	$page_html = '';
	
	$page_html .= '
			<div id="page-wrapper">
            	<div class="row">
                	<div class="col-lg-12">
                    	<h1 class="page-header">Completed Leads</h1>
                    	<div class="table-responsive">
                    		<table class="table table-hover">
                    			<thead class="blue-bg">
                    				<tr>
										<th align="center">Name</th>
										<th>Email</th>
										<th>Phone 1</th>
										<th align="middle">Phone 2</th>
										<th>DID</th>
										<th>Received On</th>
										<th>Completed On</th>
										<th>IP Addr</th>
									</tr>
								</thead>
								<tbody>';
								if($completed_leads_count>0){
									foreach($leads as $lead){
										$page_html .= '<tr>';
										$page_html .= '<td>'.$lead["name"].'</td>';
										$page_html .= '<td>'.$lead["email"].'</td>';
										$page_html .= '<td>'.$lead["phone1"].'</td>';
										$page_html .= '<td>'.$lead["phone2"].'</td>';
										$page_html .= '<td>'.$lead["did_number"].'</td>';
										$page_html .= '<td>'.$lead["received_on"].'</td>';
										$page_html .= '<td>'.$lead["completed_on"].'</td>';
										$page_html .= '<td>'.$lead["ipaddress"].'</td>';
										$page_html .= '</tr>';
									}//end of foreach loop
								}else{
									$page_html .= '<tr><td colspan="8"><font color="red">No completed leads were found.</font></td></tr>';
								}
				 $page_html .= '</tbody>
							</table>
						</div>';
						if($page_count > 1){	
							$page_html .= '<form name="frmPaginate" id="frmPaginate" action="leads_tracking.php" method="post">';
							$page_html .= '<p align="right">Showing '.$page_current_range_start.'-'.$page_current_range_end.' of '.$completed_leads_count."\n"; 
							$page_html .= '<select name="paginate" onchange="javascript:document.frmPaginate.submit();">'."\n";
							$i=0;
							foreach($page_deck as $page_range){
								$i++;
								$page_html .= '<option value="'.$i.'">'.$page_range.'</option>'."\n";
							}			 
							$page_html .= '</select>
							<input type="hidden" value="yes" name="view_completed_leads" id="view_completed_leads">
						</p></form>';
						}//end of if page count > 1
	  $page_html .= '</div>
				</div>
			</div>';
	
	return $page_html;
	
}

require "./includes/DRY_authentication.inc.php";