<?php

require "./includes/DRY_setup.inc.php";

function get_page_content($this_page){
	
        global $action_message;
	
	$page_vars = array();
	
	$action_message = "";
	$this_page->content_area = "";
	if(isset($_POST['submit']) && $_POST['submit']=='Submit'){
		process_update_status();
	}
	else if(isset($_POST['submit']) && $_POST['submit']=='Save'){
		process_update_notes();
	}
	else if(isset($_POST['submit'])){
		process_update_contact();
	}else{
		build_the_page();
	}
	
}

function build_the_page(){
	
	global $this_page;
	global $this_user;
	global $action_message;
	
	$page_vars = array();
	
	$user_info = $this_user->getUserInfo($_SESSION['user_login'],SITE_COUNTRY);
	//user_name is loaded in DRY_authentication
	//over-writing it here to make sure last changes are live
	$page_vars['user_name'] = $user_info['name'];
	$page_vars['email'] = $user_info['email'];
	$page_vars['action_message'] = $action_message;
	$page_vars['menu_item'] = 'account_details';
	
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);

	$this_page->content_area .= getUpdateAccountDetailsPage();
	
	common_header($this_page,"Update Account Details.");
}

function process_update_contact(){
	
	global $this_user;
	global $action_message;
	
	$action_message = "";
	$errors_count = 0;
	$errors = array();

	# => first, make the safe changes;  first name and email
	
	if(trim($_POST["name"])==""){
		$errors_count++;
		$errors[] = "Please enter your full name.";
	}
	
	if(trim($_POST["email"])==""){
		$errors_count++;
		$errors[] = "Please enter your Email.";
	}
	
	$this_user->name    =   $_POST["name"];
	$this_user->email   =   $_POST["email"];
        
        //$uniquecode         =   mysql_fetch_object(mysql_query("SELECT uniquecode FROM user_logins WHERE email LIKE '".$_SESSION['user_login']."'"))->uniquecode;
        
	if($errors_count == 0){
		$status = $this_user->saveUser(SITE_COUNTRY);
		if($status==false){
			$errors_count++;
			$errors[] = "An error occured while saving your details. Please try again. Please contact us if the problem persists.";
		}
	}
	
	// deal wtih any errors we've accumulated 
	if($errors_count > 0){
		$action_message .= "The following problems were detected!<br />Please review the following list \n";
		$action_message .= "<ul>\n";
		foreach($errors as $error){
			$action_message .= "<li>$error</li>\n";
		}
		$action_message .= "</ul>\n";
		
		$action_message .= "<br />Please correct the indicated errors.\n";
	}else{
		// update access logs
			if($_SESSION['user_login']!=$_POST['email'])
			{
			$this_access = new Access();
			$this_access->updateAccessLogs('Account Details Updated:Email',$_SESSION['user_login'],$_POST['email']);
                        
                        mysql_query("UPDATE user_logins SET verify_email = 'N',display_error = 'Y' WHERE email LIKE '".$_POST['email']."'");
                        //account_verify_email($this_user->email,$uniquecode);
		    }
			if($_SESSION['user_name']!=$_POST['name'])
			{
			$this_access = new Access();
			$this_access->updateAccessLogs('Account Details Updated:Name',$_SESSION['user_name'],$_POST['name']);
		    }
		//Must update the session variables
		$_SESSION['user_name'] = $this_user->name;
		$_SESSION['user_login'] = $this_user->email;
                
                //Send an E-mail to the user
		account_change_mailer($_SESSION['user_logins_id'], $this_user->email, $this_user->name, "not changed", 'update');

		$action_message .= "<h3>Changes Made Successfully!</h3>\n";
		$action_message .= "<p>Your changes to your contact information have been saved successfully.  You'll get emails shortly about the changes.</p>\n"; 

	}

	# => build the page
	build_the_page();
	
}

function process_update_notes()
{
  global $this_user;
  $id=$_POST["id"];
  $this_user->id = $_POST["id"];
  $this_user->notes = $_POST["notes"];
  $status = $this_user->saveUserNotes();
		if($status==false){
			 header("Location: account_details.php?id=$id&type=admin&message=Failed Please Try Again");
		}
		else
		{
		  header("Location: account_details.php?id=$id&type=admin&message=Successfully Updated Notes");
		
		}



}
function process_update_status()
{
  global $this_user;
  $id=$_POST["id"];//echo '<pre>';print_r($_POST);
  $old_status=$_POST["old_status"];
  $old_did_limit=$_POST["old_did_limit"];
  $old_did_limit_per_ip=$_POST["old_did_limit_per_ip"];
  $this_user->id = $_POST["id"];
  $this_user->status = $_POST["status"];
  $max_credit = trim($_POST["max_credits"]);
  $this_user->leads_tracking_menu = trim($_POST["leads_tracking_menu"]);
  $this_user->show_wp = trim($_POST["show_wp"]);
  $this_user->allow_over_4_credits = trim($_POST["allow_over_4_credits"]);
  $this_user->show_acct_crdt = trim($_POST["show_acct_crdt"]);
  if(is_numeric($max_credit)==false || $max_credit==""){
  	$max_credit = 200;//Default value
  }
  $this_user->max_30days_acct_credit = $max_credit;
  if(isset($_POST["did_limit"]) && trim($_POST["did_limit"])!=""){
  	$this_user->did_limit = $_POST["did_limit"];
  }else{
  	$this_user->did_limit = $_POST["old_did_limit"];
  }
  
  if(isset($_POST["did_limit_per_ip"]) && trim($_POST["did_limit_per_ip"])!=""){
  	$this_user->did_limit_per_ip = $_POST["did_limit_per_ip"];
  }else{
  	$this_user->did_limit_per_ip = $_POST["old_did_limit_per_ip"];
  }
  
  if( $old_status!=$_POST["status"] || intval($old_did_limit)!=intval($this_user->did_limit) || trim($_POST["add_credits"])!="" || trim($max_credit)!="" || intval($old_did_limit_per_ip)!=intval($this_user->did_limit_per_ip ) )
  {
	$status = $this_user->saveUserStatus();
	if($status==false){
		 header("Location: account_details.php?id=$id&type=admin&message=Failed Please Try Again");
	}
	else
	{
		//Add account credit to the user
		$member_credits = new Member_credits();
		if(is_object($member_credits)==false){
		header("Location: account_details.php?id=$id&type=admin&message=Failed to create Member_credits object. Please Try Again");
		 exit;
		}
		if(isset($_POST["add_credits"]) && trim($_POST["add_credits"])!=""){
		  $status = $member_credits->addMemberCredits($id,$_POST["add_credits"],$_SESSION['user_logins_id'], $_POST['allow_over_4_credits']);
		  if($status!==true){
			 header("Location: account_details.php?id=$id&type=admin&message=Failed to add Credits. $status Please Try Again");
			 exit;
		  }
		}
		  
		  // update access logs
		  $this_access = new Access();
		  if($old_status!=$_POST["status"]){
		  	$this_access->updateAccessLogs('Account Status Changed:',$old_status,$_POST["status"],false,$id);
		  }
		  if(trim($_POST["status"])=="Terminated"){
		  	//Release the DIDs from assigned_dids and also from the ast_rt_extensions
		  	$assigned_dids = new Assigned_DID();
		  	$assigned_dids->terminate_dids($this_user->id,"User account terminated");
		  	//$ast_rt_extensions = new ASTRTExtensions();
		  	//$ast_rt_extensions->terminate_routes($this_user->id);
		  }
		  if(intval($old_did_limit)!=intval($this_user->did_limit)){
		  	$this_access->updateAccessLogs('DID Limit Changed:',$old_did_limit,$this_user->did_limit,false,$id);
		  }
		  header("Location: account_details.php?id=$id&type=admin&message=Successfully Updated Status and/or DID Limit and/or Account Credit.");
		
		}

    }else
	{
      header("Location: account_details.php?id=$id&type=admin");
	
	}

}

require("includes/DRY_authentication.inc.php");

?>