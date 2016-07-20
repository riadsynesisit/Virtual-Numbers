<?php
	require "./includes/DRY_setup.inc.php";
	
	function get_page_content($this_page)
    {
        global $action_message;
        
		$cc_count = 0; //Initialize the number of credit cards to zero
		
		$cc_details = new CC_Details();
		if(is_object($cc_details)){
			$cc_count = $cc_details->get_cc_count($_SESSION["user_logins_id"]);
		}else{
			die("Could not create CC_Details object.");
		}//end of if(is_onject($cc_details)
        
    	if(isset($_POST["primary_backup_cc_id"]) && trim($_POST["primary_backup_cc_id"])!=""){
    		if(isset($_POST["primary_backup_value"]) && trim($_POST["primary_backup_value"])!=""){
    			switch(intval($_POST["primary_backup_value"])){
    				case 0:
    					$status = $cc_details->removePrimaryBackupCC(trim($_POST["primary_backup_cc_id"]),$_SESSION["user_logins_id"]);
    					break;
    				case 1:
    					$status = $cc_details->makePrimaryCC(trim($_POST["primary_backup_cc_id"]),$_SESSION["user_logins_id"]);
    					break;
    				case 2:
    					$status = $cc_details->makeBackupCC(trim($_POST["primary_backup_cc_id"]),$_SESSION["user_logins_id"]);
    					break;
    			}
    		}
        	if($status!=true){
        		die("Error: Failed to change the Primary/Backup status of this credit card.");
        	}
        }
		
		// sort out pagination
        // first, figure out the page selector data
        $records_per_page   =   RECORDS_PER_PAGE;
        $page_count         =   ceil($cc_count / $records_per_page);
        
    	$page_deck  =   array();
        
        for($page=1;$page<=$page_count;$page++)
        {
            $first_record_in_range  =   $page * $records_per_page - ($records_per_page -1 );
            $last_record_in_range   =   $page * $records_per_page;
            if($last_record_in_range>   $cc_count)
            {
                $last_record_in_range   =   $cc_count;
            }
        
            $page_deck[$page]       =   $first_record_in_range ." - ".$last_record_in_range;
        }
        
    	//now figure out what page we are on
        $page_current       =   1;
        $safe_page_select   =   $_GET['page'];
        if($safe_page_select!=  "")
        {
            $safe_page_select   =   intval($safe_page_select);
        }

        if($safe_page_select > 0 && $safe_page_select != $page_current)
        {
            $page_current   =    $safe_page_select;
        }

        $page_current_range_start   =   $page_current * $records_per_page - ($records_per_page -1 );
        $page_current_range_end     =   $page_current * $records_per_page;
        if ($page_current_range_end >   $cc_count)
        {
            $page_current_range_end =   $cc_count;
        }
		
        //now update the variable stack
        $page_vars['page_count']    =   $page_count;
        $page_vars['page_deck']     =   $page_deck;

        $page_vars['page_current']  =   $page_current;
        $page_vars['page_current_range_start']  =   $page_current_range_start;
        $page_vars['page_current_range_end']    =   $page_current_range_end;
        
        # => now, only get the records for this page
        $cc_set = $cc_details->for_page_range($_SESSION['user_logins_id'], $records_per_page, $page_current_range_start - 1);
        $my_ccs = array();
        
        if(count($cc_set)>0)
        {
            foreach($cc_set as $one_cc)
            {
            	$temp_stack =   array("card_type" => $one_cc["card_logo"],
            							"card_number" => $one_cc["card_number"],
            							"expiry" => $one_cc["exp_month"]."/".$one_cc["exp_year"],
            							"date_added" => $one_cc["date_added"],
            							"date_updated" => $one_cc["date_updated"],
            							"cc_details_id" => $one_cc["id"],
            							"is_primary" => $one_cc["is_primary"]
            					);
            	$my_ccs[] = $temp_stack;
            }//end of foreach
        }//end of if 
        
        $page_vars['my_ccs']   =   $my_ccs;
        $page_vars['cc_count']   =   $cc_count;
        $page_vars['menu_item'] =   'payment_methods';
        $this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);

        $this_page->content_area.=  getPaymentMethodsPage();//In pages/payment_methods.inc.php

        common_header($this_page,"'My Payment Methods'");
        
    }
    
    require("includes/DRY_authentication.inc.php");