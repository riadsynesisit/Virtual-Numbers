<?php
	require "./includes/DRY_setup.inc.php";
	
	function get_page_content($this_page)
    {
        global $action_message;
        
	    if(isset($_SESSION['user_logins_id']) && trim($_SESSION['user_logins_id'])!=""){
			if(isset($_POST['billing-security-cvv2'])){
				process_update_card($this_page);
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
		
    	$get_CID    =   $_GET['id'];
        if(!empty($get_CID)){
            $get_cidURL =   "id=".$get_CID."&";
        }else{
        	die("Required parameter id not passed in.");
        }
        
        $cc_details = new CC_Details();
        $cc_data = $cc_details->getCCDetails($get_CID,$_SESSION['user_logins_id']);
        
        $page_vars["cc_data"] = $cc_data;
		
		$page_vars['menu_item'] = 'add_new_card';
			
		$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	
		$this_page->content_area .= update_card();//This function is in dashboard/pages/update_card.inc.php page
		
		common_header($this_page,"Update Card Details");
	
	}
	
	function process_update_card($this_page){
	
		$get_CID    =   $_GET['id'];
        if(!empty($get_CID)){
            $get_cidURL =   "id=".$get_CID."&";
        }else{
        	die("Required parameter id not passed in.");
        }
        
		$page_vars = array();

		$cc_details = new CC_Details();
		$status = $cc_details->updateCCDetails($get_CID,
										$_SESSION['user_logins_id'],
										$_POST["payment-card-type"],
										$_POST["payment-method"],
										$_POST["card-number"],
										$_POST["card-expiry-month"],
										$_POST["card-expiry-year"],
										$_POST["billing-security-cvv2"],
										$_POST["billing-street"],
										$_POST["billing-city"],
										$_POST["billing-state"],
										$_POST["billing-country"],
										$_POST["billing-postal-code"],
										$_POST["primary-backup"]
									);
		if($status==true){
			header("Location: payment_methods.php");
		}else{
			die("Failed to add payment method.");
		}
	}
    
    require("includes/DRY_authentication.inc.php");