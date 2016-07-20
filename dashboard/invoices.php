<?php
	require "./includes/DRY_setup.inc.php";
	
	function get_page_content($this_page)
    {
        global $action_message;
        
		$payments_count = 0; //Initialize the number of invoices to zero
		
		$payments_received = new Payments_received();
		if(is_object($payments_received)){
			$payments_count = $payments_received->get_payments_count($_SESSION["user_logins_id"]);
		}else{
			die("Could not create Payments_received object.");
		}//end of if(is_object($payments_received)
        
		// sort out pagination
        // first, figure out the page selector data
        $records_per_page   =   RECORDS_PER_PAGE;
        $page_count         =   ceil($payments_count / $records_per_page);
        
    	$page_deck  =   array();
        
        for($page=1;$page<=$page_count;$page++)
        {
            $first_record_in_range  =   $page * $records_per_page - ($records_per_page -1 );
            $last_record_in_range   =   $page * $records_per_page;
            if($last_record_in_range>   $payments_count)
            {
                $last_record_in_range   =   $payments_count;
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
        if ($page_current_range_end >   $payments_count)
        {
            $page_current_range_end =   $payments_count;
        }
		
        //now update the variable stack
        $page_vars['page_count']    =   $page_count;
        $page_vars['page_deck']     =   $page_deck;

        $page_vars['page_current']  =   $page_current;
        $page_vars['page_current_range_start']  =   $page_current_range_start;
        $page_vars['page_current_range_end']    =   $page_current_range_end;
        
        # => now, only get the records for this page
        $payments_set = $payments_received->for_page_range($_SESSION['user_logins_id'], $records_per_page, $page_current_range_start - 1);
        $my_payments = array();
        
        if(count($payments_set)>0)
        {
            foreach($payments_set as $one_payment)
            {
            	$temp_stack =   array("date_paid" => $one_payment["date_paid"],
            							"id" => $one_payment["id"], 
            							"pymt_gateway" => $one_payment["pymt_gateway"], 
            							"transId" => $one_payment["transId"], 
            							"pp_sender_trans_id" => $one_payment["pp_sender_trans_id"], 
            							"pp_receiver_trans_id" => $one_payment["pp_receiver_trans_id"], 
            							"chosen_plan" => $one_payment["chosen_plan"],
            							"amount" => $one_payment["amount"]
            					);
            	$my_payments[] = $temp_stack;
            }//end of foreach
        }//end of if 
        
        $page_vars['my_payments']   =   $my_payments;
        $page_vars['payments_count']   =   $payments_count;
        $page_vars['menu_item'] =   'invoices';
        $this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);

        $this_page->content_area.=  getInvoicesPage();//In pages/invoices.inc.php

        common_header($this_page,"'My Payments'");
        
    }
	
	require("includes/DRY_authentication.inc.php");