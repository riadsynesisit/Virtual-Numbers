<?php
    require "./includes/DRY_setup.inc.php";
    
    function get_page_content($this_page)
    {
		
	   global $action_message;
        global $my_total_call_count;

        $voice_mail   =   new VoiceMail();
        //Process if the enabled/disabled button is clicked
        if(isset($_POST["btnAction"]) || isset($_POST["btnEmail"]) || isset($_POST["selRings"])){
        	//Check whether or not the message belongs to the logged in user
			//Prevent unauthorized access to the voice messages
			$user_id = $_SESSION['user_logins_id'];
        	$is_mb_owner = $voice_mail->checkMailboxEntitlements($_POST["mailbox"],$user_id);
        	if($is_mb_owner==true){
		        if(isset($_POST["btnAction"])){
		        	if($_POST["btnAction"]=="Enabled"){
		        		$voice_mail->enable_disable_mailbox($_POST["mailbox"],0);
		        	}else{
		        		$voice_mail->enable_disable_mailbox($_POST["mailbox"],1);
		        	}
		        }
		        //Process Email of Voice Mails
        		if(isset($_POST["btnEmail"])){
		        	if($_POST["btnEmail"]=="Yes"){
		        		$status = $voice_mail->email_voice_mail($_POST["mailbox"],"no");
		        	}else{
		        		$status = $voice_mail->email_voice_mail($_POST["mailbox"],"yes");
		        	}
		        	if($status !==true){
		        		echo $status;exit;//Display error message here and then exit.
		        	}
		        }
		        //Process update to the "Rings before Voice mail activation
		        if(isset($_POST["selRings"])){
		        	$voice_mail->update_rings_before_vm($_POST["mailbox"],$_POST["selRings"]);
		        }
        	}
        }
        
        $page_vars  =   array();

        // sort out pagination
        // first, figure out the page selector data
        $records_per_page   =   RECORDS_PER_PAGE;
		$my_total_vm_users_count=$voice_mail->total_calls_for_user_id($_SESSION['user_logins_id']);
		$page_count         =   ceil($my_total_vm_users_count / $records_per_page);

        $page_deck  =   array();
        
        for($page=1;$page<=$page_count;$page++)
        {
            $first_record_in_range  =   $page * $records_per_page - ($records_per_page -1 );
            $last_record_in_range   =   $page * $records_per_page;
            if($last_record_in_range>   $my_total_vm_users_count)
            {
                $last_record_in_range   =   $my_total_vm_users_count;
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
        if ($page_current_range_end >   $my_total_vm_users_count)
        {
            $page_current_range_end =   $my_total_vm_users_count;
        }

        //now update the variable stack
        $page_vars['page_count']    =   $page_count;
        $page_vars['page_deck']     =   $page_deck;

        $page_vars['page_current']  =   $page_current;
        $page_vars['page_current_range_start']  =   $page_current_range_start;
        $page_vars['page_current_range_end']    =   $page_current_range_end;

        # => now, only get the records for this page
        
        $voice_set=   $voice_mail->for_page_range($_SESSION['user_logins_id'], $records_per_page, $page_current_range_start - 1);
        $my_voice_mail=   array();

        if(count($voice_set)>0)
        {
            foreach($voice_set as $one_voice)
            {
                $unread=   $voice_mail->get_unread_mail($one_voice["mailbox"]);
				$total_mail=   $voice_mail->get_total_mail($one_voice["mailbox"]);
				$enabled = $one_voice["enabled"];
				$attach = $one_voice["attach"];
                $temp_stack =   array("mailbox" => $one_voice["mailbox"],
										"unread" => $unread,
										"total_mail" => $total_mail,
                						"enabled" => $enabled, 
                						"attach" => $attach, 
                						"rings_before_vm" => $one_voice["rings_before_vm"], 		
                						"password" => $one_voice["password"]		
                ); // temp_stack
                $my_voice_mail[] = $temp_stack;
            } # voice_set.each
        }//end of if count of $voice_set > 0

        $page_vars['my_voice_mail']   =   $my_voice_mail;
        $page_vars['my_total_vm_users_count']   =   $my_total_vm_users_count;
        $page_vars['my_total_call_count']   =   $my_total_call_count;
        $page_vars['menu_item'] =   'voice_mail';
        $this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);

		$this_page->content_area.=  getVoiceMailsPage();//In pages/voice_mails.inc.php

        common_header($this_page,"'Voice Mail'");
    }

    require("includes/DRY_authentication.inc.php");
?>