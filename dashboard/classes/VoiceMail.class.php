<?php
    class VoiceMail
    {
	function total_calls_for_user_id($user_id)
        {
            $query  =   "SELECT count(*) as tot_calls 
            			FROM voicemail_users vu, assigned_dids ad 
            			WHERE vu.customer_id = ad.user_id and 
            				vu.mailbox=ad.did_number and 
            				vu.customer_id='$user_id';";
            $result =   mysql_query($query);
            $row    =   mysql_fetch_array($result);
            return $row['tot_calls'];
        }
	
	function for_page_range($this_user_id, $limit, $offset)
        {
            $retArr =   array();
            $query  =   "SELECT vu.uniqueid as uniqueid,
								vu.customer_id as customer_id, 
								vu.context as context, 
								vu.mailbox as mailbox, 
								vu.password as password, 
								vu.fullname as fullname, 
								vu.email as email, 
								vu.pager as pager, 
								vu.tz as tz, 
								vu.attach as attach, 
								vu.saycid as saycid, 
								vu.dialout as dialout, 
								vu.callback as callback, 
								vu.review as review, 
								vu.operator as operator, 
								vu.envelope as envelope, 
								vu.sayduration as sayduration, 
								vu.saydurationm as saydurationm, 
								vu.sendvoicemail as sendvoicemail, 
								vu.delete as `delete`, 
								vu.nextaftercmd as nextaftercmd, 
								vu.forcename as forcename, 
								vu.forcegreetings as forcegreetings, 
								vu.hidefromdir as hidefromdir, 
								vu.stamp as stamp, 
								vu.attachfmt as attachfmt, 
								vu.searchcontexts as searchcontexts, 
								vu.cidinternalcontexts as cidinternalcontexts, 
								vu.exitcontext as exitcontext, 
								vu.volgain as volgain, 
								vu.tempgreetwarn as tempgreetwarn, 
								vu.messagewrap as messagewrap, 
								vu.minpassword as minpassword,
								vu.backupdeleted as backupdeleted, 
								vu.enabled as enabled, 
								vu.rings_before_vm as rings_before_vm 
            				FROM voicemail_users vu, assigned_dids ad WHERE
            					vu.customer_id = ad.user_id and 
            					vu.mailbox=ad.did_number and 
            					vu.customer_id = '$this_user_id'  LIMIT $limit OFFSET $offset;";
            $result =   mysql_query($query);
            if(mysql_num_rows($result)!=0)
            {
                while($row  =   mysql_fetch_assoc($result))
                {
                    $retArr[]   =   $row;
                }
                return $retArr;
            }else{
                return 0;
            }
	}
	function get_unread_mail($virtual_number)
        {
            $retArr =   array();
            $query  =   "SELECT count(*) as count FROM voicemessages v WHERE v.dir not like '%unavail%' and v.dir not like '%busy%' and v.mailboxuser = '$virtual_number'  and  v.read=0 group by v.mailboxuser;";
            $result =   mysql_query($query);
            if(mysql_num_rows($result)!=0)
            {
               
			    $row  =   mysql_fetch_array($result);
			    return $row['count'];
                
                
            }else{
                return 0;
            }
	}
    function get_total_unread_mails_for_user($userid)
        {
            $retArr =   array();
            $query  =   "SELECT count(*) as count 
            				FROM voicemessages v, voicemail_users vu 
            				WHERE v.dir not like '%unavail%' and 
            					v.dir not like '%busy%' and 
            					v.mailboxuser = vu.mailbox and 
            				v.read=0 and 
            				vu.customer_id='".$userid."';";
            $result =   mysql_query($query);
            if(mysql_num_rows($result)!=0)
            {
               
			    $row  =   mysql_fetch_array($result);
			    return $row['count'];
                
                
            }else{
                return 0;
            }
	}
	function get_total_mail($virtual_number)
        {
            $retArr =   array();
            $query  =   "SELECT count(*) as count FROM voicemessages WHERE dir not like '%unavail%' and dir not like '%busy%' and mailboxuser = '$virtual_number'  group by mailboxuser;";
            $result =   mysql_query($query);
            if(mysql_num_rows($result)!=0)
            {
               
			    $row  =   mysql_fetch_array($result);
			    return $row['count'];
                
                
            }else{
                return 0;
            }
	}
	function total_mails_for_mailbox($virtualnumber)
        {
            $query  =   "SELECT count(*) as tot_calls FROM voicemessages WHERE dir not like '%unavail%' and dir not like '%busy%' and mailboxuser='$virtualnumber';";
            $result =   mysql_query($query);
            $row    =   mysql_fetch_array($result);
            return $row['tot_calls'];
        }
	
	function total_mails($virtualnumber, $limit, $offset)
        {
            $retArr =   array();
            $query  =   "SELECT * FROM voicemessages WHERE dir not like '%unavail%' and dir not like '%busy%' and mailboxuser = '$virtualnumber' order by origtime desc LIMIT $limit OFFSET $offset;";
            $result =   mysql_query($query);
            if(mysql_num_rows($result)!=0)
            {
                while($row  =   mysql_fetch_array($result))
                {
                    $retArr[]   =   $row;
                }
                return $retArr;
            }else{
                return 0;
            }
	}
	function get_mail($unid)
        {
            $retArr =   array();
            $query  =   "SELECT * FROM voicemessages WHERE uniqueid = $unid  ;";
            $result =   mysql_query($query);
            if(mysql_num_rows($result)!=0)
            {
                while($row  =   mysql_fetch_array($result))
                {
                    $retArr[]   =   $row;
                }
                return $retArr;
            }else{
                return 0;
            }
	}
	function get_route_mail($virtualnumber, $user_id)
        {
            $retArr =   array();
            $query  =   "SELECT route_number FROM assigned_dids WHERE did_number = '$virtualnumber' and user_id='$user_id' ;";
            $result =   mysql_query($query);
            if(mysql_num_rows($result)!=0)
            {
               $row    =   mysql_fetch_array($result);
            return $row['route_number'];
            }else{
                return '';
            }
	}
	function get_file($id,$type){
		
            $retArr =   array();
            
            $query = "select if(recording_mp3 is null,'no','yes') as mp3exists from voicemessages where uniqueid=$id;";
            $result =   mysql_query($query);
        	if(mysql_num_rows($result)!=0)
            {
               	$row    =   mysql_fetch_array($result);
            	if($row['mp3exists']=='no'){
            		//No mp3 file exists yet. so it needs to be created
            		$query  =   "SELECT recording FROM voicemessages WHERE uniqueid =$id  ;";
            		$result =   mysql_query($query);
            		$row    =   mysql_fetch_array($result);
            		//Write the wav file to the /tmp location on disk
            		$wavfile = TMP_DIR.$id.".wav";
            		$mp3file = TMP_DIR.$id.".mp3";
            		$oggfile = TMP_DIR.$id.".ogg";
            		$f1 = fopen($wavfile,'a');
            		fwrite($f1,$row['recording']);
            		$wavfilesize = filesize($wavfile);
            		//Now convert the file using lame
            		exec("/usr/local/bin/lame -h --cbr $wavfile $mp3file");
            		$mp3filesize = filesize($mp3file);
            		//Now convert the file to ogg format using vorbis oggenc
            		exec("/usr/bin/oggenc -q 3 -o $oggfile $wavfile");
            		$oggfilesize = filesize($oggfile);
            		//Now save the mp3 file in the database table
            		$query = "update voicemessages set wavfilesize=".$wavfilesize.", 
            							mp3filesize=".$mp3filesize.", 
            							recording_mp3='".mysql_real_escape_string(file_get_contents($mp3file))."', 
            							oggfilesize=".$oggfilesize.",
            							recording_ogg='".mysql_real_escape_string(file_get_contents($oggfile))."' 
            						where uniqueid=".$id;
            		mysql_query($query);
            		fclose($f1);
            		unlink($wavfile);
            		unlink($mp3file);
            		unlink($oggfile);
            	}
            }else{
                return '';
            }
            
            if($type=="wav"){
            	$query  =   "SELECT recording FROM voicemessages WHERE uniqueid =$id  ;";
            }else{
            	$query  =   "SELECT recording_".$type." as recording FROM voicemessages WHERE uniqueid =$id  ;";
            }
            $result =   mysql_query($query);
            if(mysql_num_rows($result)!=0)
            {
               	$row    =   mysql_fetch_array($result);
            	return $row['recording'];
            }else{
                return '';
            }
	}
	
	function get_filesize($id,$type){
		
		$query  =   "SELECT ".$type."filesize as filesize FROM voicemessages WHERE uniqueid =$id  ;";
		$result =   mysql_query($query);
        if(mysql_num_rows($result)!=0){
        	$row    =   mysql_fetch_array($result);
            return $row['filesize'];
        }else{
        	return 0;
        }
	}
	
	function setAsRead($id){
		//Must mark this record as read
        $update_query = "update voicemessages set `read`=1 where uniqueid=$id;";
        mysql_query($update_query);
        return 1;
	}
	
	function enable_disable_mailbox($virtualnumber,$value){

		$query = "update voicemail_users set enabled=$value where mailbox='$virtualnumber';";
		mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			return "Error: An error occurred while enabling or disabling voice mail box. ".mysql_error();
		}
		
	}
	
    function email_voice_mail($virtualnumber,$value){

		$query = "update voicemail_users set attach='$value' where mailbox='$virtualnumber';";
		mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			return "Error: An error occurred while setting email of voice mails for voice mail box $virtualnumber. Query is $query. Error is ".mysql_error();
		}
		
	}
	
	function delete_voice_message($uniqueid){
		
		$query = "delete from voicemessages where uniqueid=$uniqueid;";
		mysql_query($query);
		return true;
		
	}
	
    function save_voice_message($uniqueid){
		
    	//Set a save indicator in the voicemessages tabls
    	$query = "update voicemessages set savemsg=1 where uniqueid=$uniqueid;";
		mysql_query($query);
		return true;
    	
	}
	
    function unsave_voice_message($uniqueid){
		
    	//Set a save indicator in the voicemessages tabls
    	$query = "update voicemessages set savemsg=0 where uniqueid=$uniqueid;";
		mysql_query($query);
		return true;
    	
	}
	
	function add_voicemail_user($did_number,$userid){
		
		//Before adding a new user ensure that no old VoiceMail user and Voicemessages exist for this DID Number (This same DID Number could be with another user in the past)
		$query = "delete from voicemessages where mailboxuser='$did_number'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: A error occurred while trying to delete voicemessage if any on this DID: $did_number from any old users of this DID if any.");
			return false;
		}
		$query = "delete from voicemail_users where mailbox='$did_number'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: A error occurred while trying to delete voicemail_users if any on this DID: $did_number from any old users of this DID if any.");
			return false;
		}
		
		$query = "select name, email from user_logins where id=$userid";
		$result =   mysql_query($query);
		if(mysql_num_rows($result)==0){
			die("Error: A records found for user id: $userid in user_logins in voiceMail.add_voicemail_user.");
			return false;
		}
		$row    =   mysql_fetch_array($result);
		$user_full_name = $row["name"];
		$user_email = $row["email"];
		
		$query = "insert into voicemail_users (
						customer_id,
						context,
						mailbox,
						password, 
						fullname, 
						email, 
						attachfmt, 
						enabled
						) values (
						'".$userid."', 
						'stickyvm',
						'".$did_number."',
						".rand(1000,9999).", 
						'".mysql_real_escape_string($user_full_name)."',
						'".mysql_real_escape_string($user_email)."', 
						'wav', 
						1
						)";
		mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			die("Error: An error occurred while adding voice mail user.<br><br>".mysql_error()."<br><br>Query is: $query");
			return false;
		}
		
	}
	
    function update_rings_before_vm($mailbox,$rings){
		
    	$query = "update voicemail_users set rings_before_vm=$rings where mailbox=$mailbox;";
		mysql_query($query);
    	return true;
	}
	
	function checkMsgEntitlements($uniqueid,$user_id){
		
		$query = "select vm.uniqueid 
						from voicemessages vm, 
								voicemail_users vmu 
						where vm.mailboxuser=vmu.mailbox and 
								vm.uniqueid='$uniqueid' and 
								vmu.customer_id='$user_id'";
		$result =   mysql_query($query);
        if(mysql_num_rows($result)!=0){
            return true;
        }else{
        	return false;
        }
		
	}
	
	function checkMailboxEntitlements($mailbox,$user_id){
		
		$query = "select uniqueid 
						from voicemail_users
						where mailbox='$mailbox' and 
								customer_id='$user_id'";
		$result =   mysql_query($query);
        if(mysql_num_rows($result)!=0){
            return true;
        }else{
        	return false;
        }
		
	}

}
?>