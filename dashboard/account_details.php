<?php

require "./includes/DRY_setup.inc.php";

function get_page_content($this_page){

	
	
	//check if the user is an admin, otherwise redirect to index page
	if($_SESSION['admin_flag']==1 && isset($_REQUEST['id'])){
		$id=$_REQUEST['id'];
		process_admin($this_page,$id);	
	}else{
        //If the update form is submitted process it first
        if(isset($_POST["user_email"]) && trim($_POST["user_email"])!=""){//account update form submitted
        	$user=new User();
        	$user->name = $_POST["full_name"];
        	$user->password = $_POST["user_pword"];
        	$user->address1 = $_POST["address1"];
        	$user->address2 = $_POST["address2"];
        	$user->user_city = $_POST["user_city"];
        	$user->user_state = $_POST["user_state"];
        	$user->user_country = $_POST["user_country"];
        	$user->postal_code = $_POST["postal_code"];
        	$user->email = $_POST["user_email"];
        	$status = $user->updateUserAccount(SITE_COUNTRY);
        	if(is_bool($status)==false && substr($status,0,6)=="Error:"){
        		die($status);
        	}else{
				$this_access = new Access();
				$this_access->updateAccessLogs('Personal Details Updated by User');
			}
        }    
		show_index_page($this_page);
	}
	
}
function show_index_page($this_page){

	$this_page->content_area = "";
	$this_page->content_area .= getAccountDetails();
	//@this_user.update_last_login!
	$page_vars['menu_item'] = 'account_details';
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	common_header($this_page, "Main Page");
	
}
function process_admin($this_page,$id){

	$page_vars = array();
	
	$this_user=new User();
	$users = $this_user->getUserInfoById($id);
	
	$records_per_page = RECORDS_PER_PAGE;
	
	//if($users["site_country"]=="AU"){
		
		$assigned_dids = new Assigned_DID();
		
		//check and process if there is DID delete request
		if(isset($_POST['submit']) && $_POST['submit']=='Delete'){
			if(intval($_POST['city_id'])==99999){//This is a MagTel Number
				//Should cancel with MagTel to ensure that the number no longer works
				$assigned_dids->DEAC_did_number(trim($_POST['did_number']));
				$del_status = $assigned_dids->deleteAUDIDData($id,$_POST['did_number'],"Deleted by admin user_id ".trim($_SESSION['user_logins_id']));
				if($del_status!=true){
					die("ERROR: Failed to delete the DID: ".trim($_POST['did_number'])." record from the database.");
				}
			}else{
				//Should cancel order on DIDWW to ensure that the number no longer works
				$client = new DidwwApi(DIDWW_API_WSDL, DIDWW_USER, DIDWW_PASS);
				if(is_object($client)==false){
				  	die("Error: Failed to create DidwwApi object.");
				}
				$did_order_cancel = $client->cancelOrder($id,$_POST['did_number']);
				if(is_object($did_order_cancel) && $did_order_cancel->result==0){//success
					//Now delete the DID from our database records
					$del_status = $assigned_dids->deleteAUDIDData($id,$_POST['did_number'],"Deleted by admin user_id ".trim($_SESSION['user_logins_id']));
					if($del_status!=true){
						die("ERROR: Failed to delete the DID record from the database.");
					}
				}else{
					echo "\nFailed to cancelOrder.";
					if(is_object($did_order_cancel)==true){
						echo "\nResult is: ".$did_order_cancel->result;
						echo "\nError Code is: ".$client->getErrorCode();
						echo "\nError is: ".$client->getErrorString();
					}else{
						echo "\n DIDWW Response is:".$did_order_cancel;
					}
					
					$del_status = $assigned_dids->deleteAUDIDData($id,$_POST['did_number'],"Deleted by admin user_id ".trim($_SESSION['user_logins_id']));
					if($del_status!=true){
						die("ERROR: Failed to delete the DID record from the database.");
					}else{
						echo '\n<h3 style="color:green;text-align:center">DID deleted successfully.</h3>';
					}
					//exit;
				}
			}
		}elseif(isset($_POST['submit']) &&  substr($_POST['submit'],0,4)=='Show'){
			$assigned_dids->showDIDToMember($_POST["did_id"]);//Reduce the renew_declined_count to 1
		}
		
		//Get member DIDs count
		$users_dids = $assigned_dids->getUserDIDsCount($id);
		
		// sort out pagination
		// first, figure out the page selector data
		$page_count = ceil($users_dids / $records_per_page);
	
		$page_deck = array();
		for($page=1;$page<=$page_count;$page++){
			$first_record_in_range = $page * $records_per_page - ($records_per_page -1 );
			$last_record_in_range = $page * $records_per_page;
			if($last_record_in_range > $users_dids){
				$last_record_in_range = $users_dids;
			}
			$page_deck[$page] = $first_record_in_range ." - ".$last_record_in_range;
		}
		
		//now figure out what page we are on
		$page_current = 1;
		$safe_page_select = $_GET['page_did'];
		if($safe_page_select!=""){
			$safe_page_select = intval($safe_page_select);
		}
		
		if($safe_page_select > 0 && $safe_page_select != $page_current){
			$page_current  = $safe_page_select;
		}
	
		$page_current_range_start = $page_current * $records_per_page - ($records_per_page -1 );
		$page_current_range_end = $page_current * $records_per_page;
		if ($page_current_range_end > $users_dids){
			$page_current_range_end = $users_dids;
		}
		
		//now update the variable stack
		$page_vars['page_count_did'] = $page_count;
		$page_vars['page_deck_did'] = $page_deck;
		
		$page_vars['page_current_did'] = $page_current;
		$page_vars['page_current_range_start_did'] = $page_current_range_start;
		$page_vars['page_current_range_end_did'] = $page_current_range_end;
		
		$page_vars['my_DIDs_count'] = $users_dids;
		
		$page_vars['my_DIDs'] = $assigned_dids->getAUUserDIDsForPage($id,$page_current_range_start-1);
	//}//end of id site_country is AU
    
	$this_access = new Access();
	
	//get memebrs count
	$my_total_call_count = $this_access->getCountaccess($id);		
			
	// sort out pagination
	// first, figure out the page selector data
	$page_count = ceil($my_total_call_count / $records_per_page);

	$page_deck = array();
	for($page=1;$page<=$page_count;$page++){
		$first_record_in_range = $page * $records_per_page - ($records_per_page -1 );
		$last_record_in_range = $page * $records_per_page;
		if($last_record_in_range > $my_total_call_count){
			$last_record_in_range = $my_total_call_count;
		}
		$page_deck[$page] = $first_record_in_range ." - ".$last_record_in_range;
	}
	
	//now figure out what page we are on
	$page_current = 1;
	if(isset($_REQUEST['page'])){
	 $safe_page_select = $_GET['page'];
	}
	else
	{
	$safe_page_select='';
	
	}
	
	if($safe_page_select!=""){
		$safe_page_select = intval($safe_page_select);
	}
	
	if($safe_page_select > 0 && $safe_page_select != $page_current){
		$page_current  = $safe_page_select;
	}

	$page_current_range_start = $page_current * $records_per_page - ($records_per_page -1 );
	$page_current_range_end = $page_current * $records_per_page;
	if ($page_current_range_end > $my_total_call_count){
		$page_current_range_end = $my_total_call_count;
	}
	
	//now update the variable stack
	$page_vars['page_count'] = $page_count;
	$page_vars['page_deck'] = $page_deck;
	$page_vars['menu_item'] = 'account_details';
	
	$page_vars['page_current'] = $page_current;
	$page_vars['page_current_range_start'] = $page_current_range_start;
	$page_vars['page_current_range_end'] = $page_current_range_end;
	$assigned_dids = new Assigned_DID();
	$users_dids = $assigned_dids->getUserDIDsCount($id);
	
	$page_vars['Did_Limit_Used'] = $users_dids;
	
	$member_credits = new Member_credits();
	$total_credits = $member_credits->getTotalCredits($id);
	$page_vars['total_credits'] = $total_credits;
	
	# => now, only get the records for this page
	
	$access = $this_access->for_page_range($records_per_page, $page_current_range_start - 1);
     
     if($access=='')
	 {
	    $page_vars['access'] = array();
	 }
	 else
	 {
	   $page_vars['access'] = $access;
	 
	 }
	
	
	
	$page_vars['my_total_access_count'] = $my_total_call_count;
	$page_vars['id'] = $_REQUEST['id'];
	$page_vars['users'] = $users;
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	 
	$this_page->content_area = "";
	$this_page->content_area .= getAccountDetailsByAdmin();
	common_header($this_page, "Main Page");
	
}

require("includes/DRY_authentication.inc.php");

?>