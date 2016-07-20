<?php
    require "./includes/DRY_setup.inc.php";
    
    function get_page_content($this_page)
    {
		
	   global $action_message;
        global $my_total_call_count;

    	$voice_mail   =   new VoiceMail();
        //Process if the delete/save button is clicked
        if(isset($_POST["Delete"]) || isset($_POST["Save"]) || isset($_POST["Unsave"])){
        	//Check whether or not the message belongs to the logged in user
			//Prevent unauthorized access to the voice messages
			$user_id = $_SESSION['user_logins_id'];
        	$is_vm_owner = $voice_mail->checkMsgEntitlements($_POST["unid"],$user_id);
        	if($is_vm_owner==true){
	        	if(isset($_POST["Delete"]) && $_POST["Delete"]=="Delete"){
	        		$voice_mail->delete_voice_message($_POST["unid"]);
	        	}
	        	if(isset($_POST["Save"]) && $_POST["Save"]=="Save"){
	        		$voice_mail->save_voice_message($_POST["unid"]);
	        	}
	        	if(isset($_POST["Unsave"]) && $_POST["Unsave"]=="Unsave"){
	        		$voice_mail->unsave_voice_message($_POST["unid"]);
	        	}
        	}
        }
        
        $page_vars  =   array();

        // sort out pagination
        // first, figure out the page selector data
        $records_per_page   =   RECORDS_PER_PAGE;
		$my_voice_mails_count=$voice_mail->total_mails_for_mailbox($_POST['mailbox']);
		$page_count         =   ceil($my_voice_mails_count / $records_per_page);

        $page_deck  =   array();
        
        for($page=1;$page<=$page_count;$page++)
        {
            $first_record_in_range  =   $page * $records_per_page - ($records_per_page -1 );
            $last_record_in_range   =   $page * $records_per_page;
            if($last_record_in_range>   $my_voice_mails_count)
            {
                $last_record_in_range   =   $my_voice_mails_count;
            }
        
            $page_deck[$page]       =   $first_record_in_range ." - ".$last_record_in_range;
        }

        //now figure out what page we are on
        $page_current       =   1;
        $safe_page_select   =   $_POST['paginate'];
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
        if ($page_current_range_end >   $my_voice_mails_count)
        {
            $page_current_range_end =   $my_voice_mails_count;
        }

        //now update the variable stack
        $page_vars['page_count']    =   $page_count;
        $page_vars['page_deck']     =   $page_deck;

        $page_vars['page_current']  =   $page_current;
        $page_vars['page_current_range_start']  =   $page_current_range_start;
        $page_vars['page_current_range_end']    =   $page_current_range_end;

        # => now, only get the records for this page
        
        $voice_set=   $voice_mail->total_mails($_POST['mailbox'], $records_per_page, $page_current_range_start - 1);
        $my_voice_mail=   array();

        if(count($voice_set)>0)
        {
            foreach($voice_set as $one_voice)
            {
                $temp_stack =   array(
				"uniqueid" => $one_voice["uniqueid"],
				"origtime" => $one_voice["origtime"],
				"read" => $one_voice["read"],
										"duration" => $one_voice["duration"],
										"tape" => $one_voice["recording"],
                						"savemsg" => $one_voice["savemsg"]										
                ); // temp_stack
                $my_voice_mail[] = $temp_stack;
            } # voice_set.each
        }//end of if count of $voice_set > 0

        $page_vars['my_voice_mail']   =   $my_voice_mail;
        $page_vars['my_voice_mails_count']   =   $my_voice_mails_count;
        $page_vars['my_total_call_count']   =   $my_total_call_count;
		$page_vars['mailbox']   =   $_POST['mailbox'];
        $page_vars['menu_item'] =   'voice_mail';
        $this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);

		$this_page->content_area.=  getViewRecordsPage();//In pages/view_records.inc.php

        common_header($this_page,"'Voice Mail'");
    }

    require("includes/DRY_authentication.inc.php");
?>