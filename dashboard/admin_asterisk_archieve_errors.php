<?php
    require "./includes/DRY_setup.inc.php";
    
    function get_page_content($this_page)
    {
        global $action_message;
        global $my_total_call_count;

        $page_vars  =   array();

        // sort out pagination
        // first, figure out the page selector data
        $my_total_call_count    =   mysql_fetch_object(mysql_query("SELECT count(*) as total FROM cdr_archieve"))->total;
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
        $cdr_set=   $cdrs->asterisk_archieve_errors($_SESSION['user_logins_id'], $records_per_page, $page_current_range_start - 1);
        $my_cdrs=   array();

        if(count($cdr_set)>0)
        {
            foreach($cdr_set as $one_cdr)
            {
                $temp_sip   =   explode(",",$one_cdr["lastdata"]);
                $temp_stack =   array("calldate" => $one_cdr["calldate"],
                "clid"      =>  $one_cdr["clid"],
                "did"       =>  $one_cdr["accountcode"],
                "dest_number"   =>  sip_url_to_phone($temp_sip[0]),
                "duration"  =>  seconds_humanized($one_cdr["duration"]),
                "disposition"   =>  $one_cdr["disposition"],
                "userfield" =>  $one_cdr["userfield"],
                "callID"    =>  $one_cdr["AcctId"],  
                ); // temp_stack
                $my_cdrs[] = $temp_stack;
            } # cdr_set.each
        }//end of if count of $cdr_set > 0

        $page_vars['my_cdrs']   =   $my_cdrs;
        $page_vars['my_total_call_count']   =   $my_total_call_count;
        $page_vars['menu_item'] =   'call_records';
        $this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);

        $this_page->content_area.=  getAsteriskArchieveErrorPage();//In pages/call_records.inc.php

        common_header($this_page,"'My CDRs'");
    }

    require("includes/DRY_authentication.inc.php");
?>