<?php
    require "./includes/DRY_setup.inc.php";
    
    function get_page_content($this_page)
    {
        global $action_message;
        global $my_total_call_count;

        $page_vars  =   array();

        // sort out pagination
        // first, figure out the page selector data
        $records_per_page   =   RECORDS_PER_PAGE;
        $page_count         =   ceil($my_total_call_count / $records_per_page);

        $page_deck  =   array();
        
        for($page=1;$page<=$page_count;$page++)
        {
            $first_record_in_range  =   $page * $records_per_page - ($records_per_page -1 );
            $last_record_in_range   =   $page * $records_per_page;
            if($last_record_in_range>   $my_total_call_count)
            {
                $last_record_in_range   =   $my_total_call_count;
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
        if ($page_current_range_end >   $my_total_call_count)
        {
            $page_current_range_end =   $my_total_call_count;
        }

        //now update the variable stack
        $page_vars['page_count']    =   $page_count;
        $page_vars['page_deck']     =   $page_deck;

        $page_vars['page_current']  =   $page_current;
        $page_vars['page_current_range_start']  =   $page_current_range_start;
        $page_vars['page_current_range_end']    =   $page_current_range_end;

        # => now, only get the records for this page
        $cdrs   =   new AsteriskSQLiteCDR();
        $cdr_set=   $cdrs->for_page_range($_SESSION['user_logins_id'], $records_per_page, $page_current_range_start - 1);
        $my_cdrs=   array();

        if(count($cdr_set)>0)
        {
            foreach($cdr_set as $one_cdr)
            {
                $vmreason = "";
            	if($one_cdr["voicemail"]=="yes"){
                	$call_status="VOICE MAIL";
                	$vmreason = $one_cdr["vmreason"];
                }else{
            		$call_status=$one_cdr["disposition"];
                }
            	$temp_sip   =   explode(",",$one_cdr["lastdata"]);
                $temp_stack =   array("calldate" => $one_cdr["calldate"],
                "clid"      =>  $one_cdr["clid"],
                "did"       =>  $one_cdr["accountcode"],
                "dest_number"   =>  $one_cdr["fwd_num"],
                "duration"  =>  seconds_humanized($one_cdr["duration"]),
                "disposition"   =>  $call_status, 
                "vmreason"  => $vmreason 
                ); // temp_stack
                $my_cdrs[] = $temp_stack;
            } # cdr_set.each
			
			// GET DIDs with timezone
			if(!isset($_SESSION['user_did_timezone'])){
				$assigned_did = new Assigned_DID();
	
				$did_data = $assigned_did->getUserDIDsByUserId($_SESSION['user_logins_id']);
				foreach($did_data as $did_row){
					$tmp_data[] = array("did" => $did_row['did_number'], "tz" => $did_row['user_timezone']);
				}
				$_SESSION['user_did_timezone'] = $tmp_data;
			}
			// EDITED @ 27th May 2016 by RIAD
        }//end of if count of $cdr_set > 0

        $page_vars['my_cdrs']   =   $my_cdrs;
        $page_vars['my_total_call_count']   =   $my_total_call_count;
        $page_vars['menu_item'] =   'call_records';
        $this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);

        $this_page->content_area.=  getCallRecordsPage();//In pages/call_records.inc.php

        common_header($this_page,"'My CDRs'");
    }

    require("includes/DRY_authentication.inc.php");
?>