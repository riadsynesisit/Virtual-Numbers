<?php
function getAccountDetails(){
	
        $get_CID    =   $_GET['id'];
        if(!empty($get_CID)){
            $get_cidURL =   "?id=".$get_CID;
        }
        
	$user = new User();
	if(is_object($user)==false){
		echo "Error: Failed to create User object.";
		exit;
	}
	
	$country = new Country();
	if(is_object($country)==false){
		echo "Error: Failed to create Country object.";
		exit;
	}
	
	$user_info = $user->getUserInfoById($_SESSION['user_logins_id']);
	$countries = $country->getAllCountry();
	
	$page_html = '
    	<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Account Details</h1>';
			if(isset($_POST["user_email"]) && trim($_POST["user_email"])!=""){
				$page_html .= '<p class="p_update_message">Account Info updated successfully!</p>';
			}
    $page_html .= '</div>
            </div>
            <div class="panel panel-default">
            	<div class="panel-heading">
                	<h3 class="panel-title"><i class="fa fa-list-ul fa-fw"></i> Modify Account Details</h3>
                </div>
                <div class="panel-body">
                	<div class="row">
                    	<div class="col-xs-12 col-md-8">
                        <form class="form-horizontal" role="form" method="post" action="account_details.php" name="frm_account_details" id="frm_account_details">
                        	<div class="form-group">
                        		<label class="col-sm-3 control-label">Full Name</label>
                        		<div class="col-xs-6">
									<input name="full_name" id="full_name" type="text" class="form-control" placeholder="Full Name" value="'.$_SESSION["user_name"].'">
                        		</div>
                      		</div>
                      		<div class="form-group">
                        		<label class="col-sm-3 control-label">Email</label>
                        		<div class="col-xs-6">
									<input name="user_email" id="user_email" type="text" class="form-control" placeholder="Email Address" value="'.$_SESSION["user_login"].'">
                        		</div>
                      		</div>
                      		<div class="form-group">
                        		<label for="inputPassword" class="col-sm-3 control-label">Password</label>
                        		<div class="col-xs-6">
                          			<input name="user_pword" id="user_pword" type="password" class="form-control" id="inputPassword" placeholder="*******">
                        		</div>
                      		</div>
                      		<div class="form-group">
                        		<label class="col-sm-3 control-label">Street Address 1</label>
                        		<div class="col-xs-8">
									<input name="address1" id="address1" type="text" class="form-control" placeholder="Address 1" value="'.$user_info["address1"].'">
                        		</div>
                      		</div>
                      		<div class="form-group">
                        		<label class="col-sm-3 control-label">Street Address 2</label>
                        		<div class="col-xs-8">
									<input name="address2" id="address2" type="text" class="form-control" placeholder="Address 2" value="'.$user_info["address2"].'">
                        		</div>
                      		</div>
                      		<div class="form-group">
                        		<label class="col-sm-3 control-label">City</label>
                        		<div class="col-xs-6">
									<input name="user_city" id="user_city" type="text" class="form-control" placeholder="City" value="'.$user_info["user_city"].'">
                        		</div>
                      		</div>
                      		<div class="form-group">
                        		<label class="col-sm-3 control-label">Country</label>
                        		<div class="col-xs-5">
									<select class="form-control" name="user_country" id="user_country">
										<option value="">Select Country</option>';
										foreach($countries as $country) {
											if(trim($user_info["user_country"])==trim($country["iso"])){
												$selected = " selected ";
											}else{
												$selected = "";
											}
											$page_html .= '<option value="'.trim($country["iso"]).'" '.$selected.'>'.trim($country["printable_name"]).'</option>';
										}
									$page_html.='</select>
								</div>
							</div>
							<div class="form-group">
                        		<label class="col-sm-3 control-label">State/Region</label>
                        		<div class="col-xs-6">
									<input name="user_state" id="user_state" type="text" class="form-control" placeholder="stateregion" value="'.$user_info["user_state"].'">
                        		</div>
                      		</div>
							<div class="form-group">
                        		<label class="col-sm-3 control-label">Postal Code</label>
                        		<div class="col-xs-4">
									<input name="postal_code" id="postal_code" type="text" class="form-control" placeholder="postal" value="'.$user_info["postal_code"].'">
                        		</div>
                      		</div>
                      	</form>
                        <a onclick="javascript:$(\'#frm_account_details\').submit();" class="btn blue btn-lg m-icon-big fltrt">
							Update Details <i class="m-icon-big-swapright m-icon-white"></i>
						</a>
					</div>
				</div>
			</div>	
        </div>
    </div>
    ';
	
	return $page_html;
	
}
function getAccountDetailsByadmin(){
	global $this_page;
	
	$get_CID    =   $_GET['id'];
    if(!empty($get_CID)){
        $get_cidURL =   "?id=".$get_CID;
    }
	
    $ppa = new Paypal_preapprovals();
    if(is_object($ppa)==false){
    	die("Failed to create Paypal_preapprovals object.");
    }
    
    $wpa = new Worldpay_preapprovals();
	if(is_object($wpa)==false){
    	die("Failed to create Worldpay_preapprovals object.");
    }
    
	$spa = new SimplePay_preapprovals();
	if(is_object($spa)==false){
    	die("Failed to create Worldpay_preapprovals object.");
    }
	
    $user = new User();
    if(is_object($user)==false){
    	die("Failed to created User object.");
    }
    $credit_values = $user->get_max_30days_credit($get_CID, "Y");
    
	$max_credits = $credit_values[0];
	$is_over_4_allowed = $credit_values[1];
	$page_vars = $this_page->variable_stack;
	
	$leads_tracking_yes = "";
	if($page_vars['users']['leads_tracking_menu']=="Y"){ 
		$leads_tracking_yes = " selected ";
	}
    $leads_tracking_no = "";
	if($page_vars['users']['leads_tracking_menu']=="N"){ 
		$leads_tracking_no = " selected ";
	}
	
	$show_wp_yes = "";
	if($page_vars['users']['is_over_4_allowed']=="Y"){ 
		$show_wp_yes = " selected ";
	}
    $show_wp_no = "";
	if($page_vars['users']['show_wp']=="N"){ 
		$show_wp_no = " selected ";
	}
	
	$allow_over_4_yes = "";
	if($page_vars['users']['allow_over_4_credits']=="Y"){ 
		$allow_over_4_yes = " selected ";
	}
    $allow_over_4_no = "";
	if($page_vars['users']['allow_over_4_credits']=="N"){ 
		$allow_over_4_no = " selected ";
	}
	
	$show_acct_crdt_yes = "";
	if($page_vars['users']['show_acct_crdt']=="Y"){ 
		$show_acct_crdt_yes = " selected ";
	}
    $show_acct_crdt_no = "";
	if($page_vars['users']['show_acct_crdt']=="N"){ 
		$show_acct_crdt_no = " selected ";
	}
	
	$page_html = '
		<div id="page-wrapper">
			<div class="row">
            	<div class="col-lg-12">
                	<h1 class="page-header">Member Account Details</h1>
	';

	$page_html .= '		
          <table width="100%" border="0" cellspacing="0" cellpadding="7" class="my-call-recording">
            <tbody>';
				if(isset($_REQUEST['message']))
				{
				 $page_html.='<tr><td><div class="tip_box" style="width:200px;">'.$_REQUEST['message'].'</div></td></tr>';
				
				}
              $page_html.='<tr class="title">
                <td class="first" width="170">Full Name:</td>
                <td width="40%">'.$page_vars['users']['name'].'</td>
              </tr>
              <tr>
                <td class="first">Email Address:</td>
                <td><a href="mailto:'.$page_vars['users']['email'].'" class="link_style1">'.$page_vars['users']['email'].'</a>&nbsp;</td>
			  </tr>
            </tbody>
          </table>
          <br />
	';
	$page_html.='<form name="update" action="update_account_details.php" method="post">
	<table width="100%" border="0" cellspacing="0" cellpadding="7" class="my-call-recording"><tr><td><b>Account Status:</b></td><td>
	<select name="status">
	<option value="Active"';
	if($page_vars['users']['status']=='Active')
	{
	 $page_html.=' selected ';
	}
	$page_html.='>Active</option>
	<option value="LoginRestricted"';
	if($page_vars['users']['status']=='LoginRestricted')
	{
	 $page_html.=' selected ';
	}
	$page_html.='>LoginRestricted</option>
	<option value="Suspended"';
	if($page_vars['users']['status']=='Suspended')
	{
	 $page_html.=' selected ';
	}
	$page_html.='>Suspended</option>
	<option value="Terminated"';
	if($page_vars['users']['status']=='Terminated')
	{
	 $page_html.=' selected ';
	}
	$page_html.='>Terminated</option>
	</select>
	</td>
	<td colspan="3">
	</tr>
	<tr>
	<td valign="bottom">';
	if($page_vars['users']['site_country']=="AU" || $page_vars['users']['site_country']=="UK" || $page_vars['users']['site_country']=="COM"){
		$page_html.='<label for="did_limit" class="dcm_label">DID 070/087 Limit: </label>';
		$showLimit = 1 ;
	}else{
		$page_html.='&nbsp;';
		$showLimit = 0 ;
	}
	$page_html.='</td>
	<td valign="top">';
	if($showLimit == 1){
		$page_html.='<input type="text" name="did_limit" class="dcm_input" value="'.$page_vars['users']['did_limit'].'">';
	}else{
		$page_html.='&nbsp;';
	}
	$page_html.='</td>
	
	<td valign="bottom">';
	if($showLimit == 1){
		$page_html.='<label for="did_limit_per_ip" class="dcm_label">DID 070/087 Limit/IP: </label>';
		$showLimit = 1 ;
	}else{
		$page_html.='&nbsp;';
		$showLimit = 0 ;
	}
	$page_html.='</td>
	<td valign="top">';
	if($showLimit == 1){
		$page_html.='<input type="text" name="did_limit_per_ip" class="dcm_input" value="'.$page_vars['users']['did_limit_per_ip'].'">';
	}else{
		$page_html.='&nbsp;';
	}
	$page_html.='</td>
	
	<td>&nbsp;&nbsp;</td>
	</tr>';
	//if($page_vars['users']['site_country']=="AU"){
		$page_html.='<tr>
		<td>Total Credits:</td>
		<td>'.$page_vars['total_credits'].'</td>
		<td valign="bottom"><label for="add_credits" class="dcm_label">Add Credits: </label></td>
		<td valign="top"><input type="text" name="add_credits" class="dcm_input" value=""></td>
		<td>&nbsp;&nbsp;</td>
		</tr>';
		$page_html.='<tr>
		<td>Leads Tracking Menu:</td>
		<td>
			<select name="leads_tracking_menu" id="leads_tracking_menu">
				<option value="Y"'.$leads_tracking_yes.'>Yes</option>
				<option value="N"'.$leads_tracking_no.'>No</option>
			</select>
		</td>
		<td valign="bottom"><label for="add_credits" class="dcm_label">Max 30day Credits: </label></td>
		<td valign="top"><input type="text" name="max_credits" class="dcm_input" value="'.$max_credits.'"></td>
		<td>&nbsp;&nbsp;</td>
		</tr>';
		$page_html.='<tr>
		<td>Show CC (Override site rules):</td>
		<td>
			<select name="show_wp" id="show_wp">
				<option value="Y"'.$show_wp_yes.'>Yes</option>
				<option value="N"'.$show_wp_no.'>No</option>
			</select>
		</td>
		<td valign="bottom"><label for="show_acct_crdt" class="dcm_label">Show Acct Credit: </label></td>
		<td valign="top">
			<select name="show_acct_crdt" id="show_acct_crdt">
				<option value="Y"'.$show_acct_crdt_yes.'>Yes</option>
				<option value="N"'.$show_acct_crdt_no.'>No</option>
			</select>
		</td>
		<td>&nbsp;&nbsp;</td>
		</tr>';
		
		$page_html.='<tr>
		<td>Allow over 4 credits:</td>
		<td>
			<select name="allow_over_4_credits" id="allow_over_4_credits">
				<option value="Y"'.$allow_over_4_yes.'>Yes</option>
				<option value="N"'.$allow_over_4_no.'>No</option>
			</select>
		</td>
		
		<td colspan="3">&nbsp;&nbsp;</td>
		</tr>';
	//}
	$page_html.='<tr>
	<td>
	Date Status Set:
	</td>
	<td>'.$page_vars['users']['date_status_set'].'</td>
	<td>DID Used:</td>
	<td>'.$page_vars['Did_Limit_Used'].'</td>
	<td>&nbsp;<input type="submit" name="submit" value="Submit"></td>
	</tr>
	<tr><td colspan=5>&nbsp;</td>
	</table>
	<input type="hidden" name="id" value="'.$page_vars['id'].'">
	<input type="hidden" name="old_status" value="'.$page_vars['users']['status'].'">
	<input type="hidden" name="old_did_limit" value="'.$page_vars['users']['did_limit'].'">
	<input type="hidden" name="old_did_limit_per_ip" value="'.$page_vars['users']['did_limit_per_ip'].'">
	</form>';
	
	$page_html .= '
	<script>
		function delete_agreement(pymt_gtwy, did_id, did_number){
			if(confirm("Are you sure you want to cancel the future pay agreement with "+pymt_gtwy+" of this Virtual Number: "+did_number+"?")==false){
				return;
			}
			jQuery.ajaxSetup({async:false});
			var url = "../ajax/ajax_cancel_futurepay.php";
            $.post(url, {"pymt_gtwy" : pymt_gtwy, "did_id" : did_id, "cancel_reason" : "Cancelled by admin from account details page"}, function(response){
				console.log(response);
            	if(response.substr(0,6)=="Error:"){
            		alert(response);
            		return;
            	}else{
            		alert("Success: Future Pay agreement with "+pymt_gtwy+" of virtual number: "+did_number+" successfully cancelled");
            		$("#div_delete_agreement_"+did_number).hide();
            	}
            });
		}
	</script>
	';
	
	//if($page_vars['users']['site_country']=="AU"){
		$page_html .= '<p>User DIDs:</p>';
		if($page_vars['my_DIDs_count']>0){
			$page_html .= '<table width="100%" border="1" cellspacing="2" cellpadding="3" class="my-call-recording">
				<tbody>';
			
			$page_html .= '<tr class="title tr-bg-title">
						<td width="10%" class="first td_order_DESC" align="center">Country Code</td>
						<td width="10%" class="td_order_ASC" align="center">Area Code</td>
						<td width="16%" class="td_order_ASC" align="center">Virtual Number</td>
						<td width="16%" class="td_order_ASC" align="center">Destination</td>	
						<td width="24%" class="td_order_ASC" align="center">Action</td>
						<td width="16%" class="td_order_ASC" align="center">Status</td>
						</tr>';
			foreach($page_vars['my_DIDs'] as $did_key_value => $my_DID){
				if($did_key_value%2 == 0 ){
					$class_tr_bg = ' class="tr_bg_even"';
				}else{
					$class_tr_bg = ' class="tr_bg_odd"';
				}
				if(substr($my_DID['did_number'],0,3)=="070" || substr($my_DID['did_number'],0,3)=="087" || substr($my_DID['did_number'],0,4)=="4470" || substr($my_DID['did_number'],0,4)=="4487"){
					$free_did = true;
				}else{
					$free_did = false;
				}
				//Check if a future pay agreement is in place either of PayPal or of WorldPay
				$del_agreement_html = "";
				switch(trim($my_DID['pymt_gateway'])){
					case "PayPal":
						$pa_exists = $ppa->does_preapproval_exist($my_DID['id']);
						if(is_bool($pa_exists)==false && substr($pa_exists, 0,6)=="Error:"){
							die($pa_exists);
						}elseif($pa_exists===true){
							$del_agreement_html = '<input type="button" name="del_agreement" value="Delete Agreement" class="logged-button"  style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;"  onclick="delete_agreement(\''.$my_DID['pymt_gateway'].'\',\''.$my_DID['id'].'\',\''.$my_DID['did_number'].'\')" >';
						}
						break;
					case "WorldPay":
						$pa_exists = $wpa->does_preapproval_exist($my_DID['id']);
						if(is_bool($pa_exists)==false && substr($pa_exists, 0,6)=="Error:"){
							die($pa_exists);
						}elseif($pa_exists===true){
							$del_agreement_html = '<input type="button" name="del_agreement" value="Delete Agreement" class="logged-button"  style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;"  onclick="delete_agreement(\''.$my_DID['pymt_gateway'].'\',\''.$my_DID['id'].'\',\''.$my_DID['did_number'].'\')" >';
						}
						break;
					case "Credit_Card":
						$pa_exists = $spa->does_preapproval_exist($my_DID['id']);
						if(is_bool($pa_exists)==false && substr($pa_exists, 0,6)=="Error:"){
							die($pa_exists);
						}elseif($pa_exists===true){
							$del_agreement_html = '<input type="button" name="del_agreement" value="Delete Agreement" class="logged-button"  style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;"  onclick="delete_agreement(\''.$my_DID['pymt_gateway'].'\',\''.$my_DID['id'].'\',\''.$my_DID['did_number'].'\')" >';
						}
						break;
					case "AccountCredit":
						$pa_exists = $wpa->does_preapproval_exist($my_DID['id']);
						if(is_bool($pa_exists)==false && substr($pa_exists, 0,6)=="Error:"){
							die($pa_exists);
						}elseif($pa_exists===true){
							$del_agreement_html = '<input type="button" name="del_agreement" value="Delete Agreement" class="logged-button"  style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;"  onclick="delete_agreement(\'WorldPay\',\''.$my_DID['transId'].'\',\''.$my_DID['did_number'].'\')" >';
						}else{
							$pa_exists = $ppa->does_preapproval_exist($my_DID['id']);
							if(is_bool($pa_exists)==false && substr($pa_exists, 0,6)=="Error:"){
								die($pa_exists);
							}elseif($pa_exists===true){
								$del_agreement_html = '<input type="button" name="del_agreement" value="Delete Agreement" class="logged-button"  style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;"  onclick="delete_agreement(\'PayPal\',\''.$my_DID['pp_receiver_trans_id'].'\',\''.$my_DID['did_number'].'\')" >';
							}
						}
						break;
					default:
						//Don't process anything else except for 070 or 087 numbers and deleted numbers (myorder for deleted numbers is 2) - these numbers do not have a payment gateway
						if($my_DID['myorder']==1 && substr($my_DID['did_number'],0,3)!="070" && substr($my_DID['did_number'],0,3)!="087" && substr($my_DID['did_number'],0,4)!="4470" && substr($my_DID['did_number'],0,4)!="4487"){
							die("Error: Unknown payment gateway: ".trim($my_DID['pymt_gateway'])." for DID Number : ".$my_DID['did_number']);
						}
						break;
				}
				$page_html .= '
					<tr '. $class_tr_bg .'>
						<td>'.$my_DID['did_country_code'].'</td>
						<td>'.$my_DID['did_area_code'].'</td>
						<td>'.$my_DID['did_number'].'</td>
						<td>'.$my_DID['route_number'].'</td>
						<td align="center">
						<form action="account_details.php'.$get_cidURL.'" method="post" accept-charset="utf-8">
						<input type="hidden" name="did_number" value="'.$my_DID['did_number'].'" id="did_number">
						<input type="hidden" name="city_id" value="'.$my_DID['city_id'].'" id="city_id">
						<input type="hidden" name="did_id" value="'.$my_DID['id'].'" id="did_id">';
						$delete_button_html = '<input type="submit" name="submit" value="Delete" class="logged-button"  style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;"  onclick="javascript:return confirm(\'Are you sure you want to delete virtual number:'.$my_DID['did_number'].' ?\')" >';
						if(intval($my_DID['secs_to_expire'])>0){
							$page_html .= $delete_button_html;
						}else{
							if(intval($my_DID["renew_declined_count"]) >= 3){
								$page_html .= '<input type="submit" name="submit" value="Show to member" class="logged-button"  style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" >';
							}else if($my_DID['myorder']==2){//This is a deleted number
								$page_html .= "Deleted: ".$my_DID['did_expires_on'];
							}else{
								$page_html .= $delete_button_html;
							}
						}
						$page_html .= '<div id="div_delete_agreement_'.$my_DID['did_number'].'">'.$del_agreement_html."</div>";
						$page_html .= '</form>
						</td>
						<td align="center">';
						if($my_DID['myorder']==2){//This is a deleted number
							$page_html .= '<font color="red">Deleted</font>';
						}elseif(intval($my_DID['secs_to_expire'])>0 || $free_did==true){
							$page_html .= '<font color="green">Active</font>';
						}else{
							if(intval($my_DID["renew_declined_count"]) >= 3){
								$page_html .= '<font color="red">Auto Billing Failed<br />'.$my_DID["renew_declined_count"].' x Attempts</font>';
							}else{
								$page_html .= '<font color="red">Expired</font>';
							}
						}
						$page_html .= '</td>
					</tr>';
			}//end of foreach loop
			$page_html .= '</table>'."\n";
		}//end of if did count is greater than zero
		
		if($page_vars['page_count_did'] > 1){	
			$page_html .= '<p align="right">Showing '.$page_vars['page_current_range_start_did'].'-'.$page_vars['page_current_range_end_did'].' of '.$page_vars['my_DIDs_count']."\n"; 
			$page_html .= '<select name="paginate_did" >'."\n";
			$page_deck_did = $page_vars['page_deck_did'];
			$i=0;
			foreach($page_deck_did as $page_range){
				$i++;
				$page_html .= '<option value="'.$i.'" onclick="window.location =\'account_details.php?page_did='.$i.'&id='.$page_vars['id'].'&type=admin\' " >'.$page_range.'</option>'."\n";
			}			 
			$page_html .= '</select>
		</p>';
		}//end of if page count > 1
		
	//}//end of id site country is AU
	
	$page_html .= '<table width="100%" border="0" cellspacing="0" cellpadding="7" class="my-call-recording">
		<tbody><tr><td><b>Access Logs</b></td></tr>';
	
	if(count($page_vars['access'])>0){
		$page_html .= '';
	}else{
		$page_html .= '<caption>Access Logs Not Found.</caption>';
	}
	
	$page_html .= '<tr class="title">
				<td width="12%" class="first td_order_DESC">Who? (IP)</td>
				<td width="24%" class="td_order_ASC">Date/Time</td>
				<td width="12%" class="td_order_ASC">User Type</td>
				<td width="36%" class="td_order_ASC">Item</td>				
				</tr>';
	
	if(count($page_vars['access'])>0){
		foreach($page_vars['access'] as $one_member){
			$page_html .='<tr class="tr_highlight">
				<td>'.$one_member['id_address'].'</td>  
				<td>'.$one_member["created"].'</td>
				<td>'.ucfirst($one_member["type"]).'</td>
				<td>'.$one_member["item"];
				if($one_member['did_number']!='')
				{
				 $page_html .= $one_member['did_number'];
				}
				if($one_member['old_value']!='')
				{
				 $page_html .= ' From:'.$one_member['old_value'].' To:'.$one_member['new_value'].'</td></tr>';
				}
				else if($one_member["new_value"]!='')
				{
				  $page_html .=' As '.$one_member["new_value"].'</td></tr>';
				
				}
				else
				{
				  $page_html .='</td></tr>';
				}
				
				
		}//end of foreach
	}//end of if count
	
	$page_html .= '</tbody>
	</table>';
	
	if($page_vars['page_count'] > 1){	
		$page_html .= '<p align="right">Showing '.$page_vars['page_current_range_start'].'-'.$page_vars['page_current_range_end'].' of '.$page_vars['my_total_access_count']."\n"; 
		$de=$page_vars['id'];
		$se="'admin'";
		$page_html .= '<select name="paginate" onchange="javascript:block_pagination(\'account_details\',this.value,'.$de.','.$se.')">'."\n";
		$page_deck = $page_vars['page_deck'];
		$i=0;
		foreach($page_deck as $page_range){
			$i++;
			$page_html .= '<option value="'.$i.'" '; if(isset($_GET['page']) && $_GET['page']==$i) 
			{ 
				$page_html .= '	selected >'.$page_range.'</option>'."\n";
			}
			else
			{			
			$page_html .= '	>'.$page_range.'</option>'."\n";
		
			}
		}			 
		$page_html .= '</select>
	</p>';
	}//end of if page count > 1
	
	$page_html.='<form name="update" action="update_account_details.php'.$get_cidURL.'" method="post"><table><tr><td ><b>Notes:</b></td><td><textarea name="notes" rows="10" cols="50">'.$page_vars['users']['notes'].'</textarea></td></tr>
	<tr><td><input type="hidden" name="id" value="'.$page_vars['id'].'"></td><td><input type="submit" name="submit" value="Save"></td></tr>
	</table></form>';
	$page_html.='</div>';
	$page_html.='</div>';
	$page_html.='</div>';
	return $page_html;
	
}
?>