<?php
require "./includes/DRY_setup.inc.php";

function get_page_content($this_page){
	
	if(isset($_SESSION['user_logins_id']) && trim($_SESSION['user_logins_id'])!=""){
		new_did_success_page($this_page);
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

function new_did_success_page($this_page){
	
	$blnError = false;
	$errMsg = "";
	
	//Check for user session
	if($blnError == false && (isset($_SESSION['user_logins_id'])==false || trim($_SESSION['user_logins_id'])=="")){
		$blnError = true;
		$errMsg = "Error: Session expired. Please login again.";
	}
	
	if($blnError == false && isset($_POST['forwarding_number'])==false || trim($_POST['forwarding_number'])==""){
		$blnError = true;
		$errMsg = "Error: Required parameter forwarding_number not passed.";
	}
	
	$block_destination = new Block_Destination();
	if($blnError == false && is_object($block_destination)==false){
		$blnError = true;
		$errMsg = "Error: Failed to create Block_Destination object.";
	}
	
	$unassigned_087did = new Unassigned_087DID();
	if($blnError == false && is_object($unassigned_087did)==false){
		$blnError = true;
		$errMsg = "Error: Failed to create Unassigned_087DID object.";
	}
	
	$unassigned_did = new Unassigned_DID();
	if($blnError == false && is_object($unassigned_did)==false){
		$blnError = true;
		$errMsg = "Error: Failed to create Unassigned_DID object.";
	}
	
	$assigned_did = new Assigned_DID();
	if($blnError == false && is_object($assigned_did)==false){
		$blnError = true;
		$errMsg = "Error: Failed to create Assigned_DID object.";
	}
	
	$voice_mail = new VoiceMail();
	if($blnError == false && is_object($voice_mail)==false){
		$blnError = true;
		$errMsg = "Error: Failed to create VoiceMail object.";
	}
	
	$access = new Access();
	if($blnError == false && is_object($access)==false){
		$blnError = true;
		$errMsg = "Error: Failed to create Access object.";
	}
	
	if($blnError == false && $block_destination->isDestinationBlocked($_POST['forwarding_number'])==true){
		//Number blocked
		$blnError = true;
		$errMsg = "Error: The forwarding number selected by you is currently blocked.";
	}
	
	$fwd_number = str_replace('+','',$_POST['forwarding_number']);
	if($blnError == false && $unassigned_087did->isCallCostOK($fwd_number)!=true){
		$blnError = true;
		$errMsg = "Error: Your forwarding number is too expensive and not currently allowed..";
	}
	
	$did_id = "";
	$did = "";
	//Get an available a 070 Number
	if($blnError == false){
		//Check for maximum number of DIDs allowed in an account
		$users_dids = $assigned_did->getUserDIDsCount($_SESSION['user_logins_id'],true);
		$admin_limit=$assigned_did->getUserDIDsLimit($_SESSION['user_logins_id']);
		if($users_dids>=$admin_limit){
			$blnError = true;
			$errMsg = "Error: The Number of free DIDs in your account is $users_dids and it exceeds/meets your allowed limit of $admin_limit. Please contact support.";
		}
		if($blnError == false){
			$arr_did = $unassigned_did->retUnassigned_DID();
			if(is_array($arr_did)==false){
				$blnError = true;
				$errMsg = $arr_did;
			}else{
				$did_id = $arr_id[0];
				$did = $arr_did[1];
			}
		}
	}
	
	//Now reserve this did_number - if still available
	if($blnError == false){
		$status = $unassigned_did->setReservedByDID($did);
		
		if($status){//If reservation is successful
			$assigned_did->user_id 		= $_SESSION['user_logins_id'];
			$assigned_did->did_number	= $did;
			$assigned_did->callerid_on	= 1;
			$assigned_did->ringtone		= -9;
			$assigned_did->route_number	= $fwd_number;
			$assigned_did->callerid_option = 1;
			$assigned_did->total_minutes	= 0;
			$assigned_did->balance = 0; //Free number no balance required
			//Now insert this Assigned DID record in the table
			$status = $assigned_did->insertDIDData();
			if(is_int($status)==false && substr($status,0,6)=="Error:"){
				$blnError = true;
				$errMsg = $status;//Set the error message from here
			}
			if($blnError==false && $status <= 0 ){
				$blnError = true;
				$errMsg = "Error: A DID Number could not be reserved for you. Please try again.";
			}
		}else{
			$blnError = true;
			$errMsg = "Error: A DID Number could not be reserved for you. Please try again.";
		}
	}
	
	//Now add a record to voicemail_users table
	if($blnError == false){
		$status = $voice_mail->add_voicemail_user($did,$_SESSION['user_logins_id']);
	}
	
	//Now update access logs
	if($blnError == false){
		$access->updateAccessLogs('Added DID:',false,$did);
	}
	
	//Now take the DID out of the unassigned list permanently
	if($blnError == false){
		$unassigned_did->did_number = $did;
		$unassigned_did->destroyByDID_Number();
	}
	
	$status = dialplan_did_route("vssp", $did, $fwd_number, $_SESSION['user_logins_id']);
	if($status!=true){
		$blnError = true;
		$errMsg = "Error: Failed to create DID Route. Please try again.";
	}
	
	$page_vars = array();
		  
	$page_html = "";
	
	//$page_html .= '<div class="mid-section">';
	
	/*$page_html.='
	
		<!-- Main Container -->
                                <div class="contentbox">
                                     <div class="contentbox-head">
                                          <div class="content-breadbox">
                                                <ul class="breadcrumb">';
                                                    $page_html.='<li ><a class="breadcrumb-first" href="add-number-1.php">Forwarding Number</a></li>
                                                    <li><a class="" id="choose_plan" onclick="go_choose_plan();">Choose Plan</a></li>';  
                                                    $page_html.='<li><a href="#" class="breadcrumb-page">Payment</a></li>
                                                </ul>
                                          </div>
                                         
                                          <div class="row Mtop20">
                                              <div class="subsection">';
                                                    if($blnError==true && trim($errMsg)!=""){
                                                    	$page_html .= 'An error occurred while processing your request.<br /><br /><font color="red">'.trim($errMsg).'</font>';
                                                    }else{
			                                                    $page_html .= '<div class="row thankyoupay Mtop30">
			                                                    Thank you for your order. Your account is now setup with the following virtual number.<br/>
			                                                    If you have any questions please contact us on <a href="mailto:'.ACCOUNT_CHANGE_EMAIL.'">'.ACCOUNT_CHANGE_EMAIL.'</a>
			                                                    </div>
			                                                    <div class="thankyouinfo">
			                                                        <span>Your Virtual Number : <font color="red"><b><u>+'.$did.'</u></b></font><br />
			                                                        </span>
			                                                    </div>';
                                                    }
                                              $page_html .= '</div>
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
                    <h1 class="page-header">Add New Number</h1></div>
                	<div class="row">                
                		<div class="col-lg-12">
							<div class="contentbox">
                            	<div class="contentbox-head">
                                <div class="clearfix"></div>
                                <div class="row">
                                	<div class="col-md-6 col-md-offset-3">
                                    	<div class="alert alert-success  Mtop20">';
											if($blnError==true && trim($errMsg)!=""){
												$page_html .= 'An error occurred while processing your request.<br /><br /><font color="red">'.trim($errMsg).'</font>';
											}else{
												$page_html .= '<h1 class="txt-c">Thank You!</h1><br>';
												$page_html.='<p class="txt-red"><strong>Thank you for your order. Your account is now setup with the following virtual number.</strong></p><br>';
												$page_html.='<span>Your Virtual Number : <font color="red"><b><u>+'.$did.'</u></b></font><br /></span>';
												$page_html.='<p>Contact us 24/7 About your order or if you have any other enquires.</p>';
											}
							$page_html.='</div>                                        
   									</div>  
   								</div>                                                                                
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
	
    $this_page->content_area .= $page_html;
	$page_vars['menu_item'] = 'add_new_number';
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	common_header($this_page,"New DID Success");
	
}//end of function new_did_success_page

require("includes/DRY_authentication.inc.php");

?>