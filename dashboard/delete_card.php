<?php
	require "./includes/DRY_setup.inc.php";
	
	function get_page_content($this_page)
    {
        global $action_message;
        
	    if(isset($_SESSION['user_logins_id']) && trim($_SESSION['user_logins_id'])!=""){
			if(isset($_GET['id']) && trim($_GET['id']!="")){
				process_delete_card($this_page);
			}else{
				die("Required parameter id was not passed in.");
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
    
    function process_delete_card($this_page){
    	
    	$cc_details = new CC_Details();
    	if(is_object($cc_details)){
	    	if($cc_details->deleteCCDetails($_GET['id'],$_SESSION['user_logins_id'])==true){
	    		header("Location:payment_methods.php");
	    	}
    	}else{
    		die("Failed to create CC_Details object.");
    	}
    	
    }
	
	require("includes/DRY_authentication.inc.php");