#!/usr/bin/php -q
<?php

require("/var/www/html/stickynumber/dashboard/includes/config.inc.php");
require("/var/www/html/stickynumber/dashboard/includes/db.inc.php");
require("/var/www/html/stickynumber/dashboard/classes/UserMessages.class.php");
require("/var/www/html/stickynumber/dashboard/classes/Daily_Stats.class.php");
require("/var/www/html/stickynumber/dashboard/classes/Client_notifications.class.php");
require("/var/www/html/stickynumber/dashboard/libs/Magrathea_Telecom_NTS_API.lib.php");
require("/var/www/html/stickynumber/dashboard/libs/utilities.lib.php");

echo "\n\n\n".date(DATE_RSS)."\n\n";
set_time_limit(0);

$rows_ast_rt_extensions=0;
$rows_assigned_dids=0;
$rows_access_logs=0;
$num_emails_sent=0;

$loop_count=0;

//All the UK DIDs that were not called in the past 90 days
$query = "select ad.id as id, 
					ad.user_id as user_id,
					ul.site_country as site_country, 
					ul.uniquecode as uniquecode, 
					ul.email as email, 
					ul.name as name, 
					ul.unsubscribe as unsubscribe,
					ad.did_number as did_number, 
					ad.route_number as route_number, 
					ad.route_last_used as route_last_used 
			from assigned_dids ad LEFT JOIN user_logins as ul ON ad.user_id=ul.id  
			where 
					(SUBSTRING(ad.did_number,1,3)='070' OR SUBSTRING(did_number,1,3)='087') and 
					ad.did_activated_on is not null and 
					ad.route_last_used is not null and 
					datediff(CURDATE(),ad.route_last_used) > 90
			order by ad.user_id asc;";
$result = mysql_query($query);

while($row=mysql_fetch_array($result)){
	
	$fp = NTS_API_Connect();
	
	$loop_count = $loop_count + 1;
	echo "\n$loop_count. Processing ".$row['did_number'];
	echo "\nThe above DID was last called on: ".$row['route_last_used'];
	
	mysql_query("delete from ast_rt_extensions where exten=".$row['did_number']);
	if(mysql_affected_rows()>0){
		echo "\nDeleted did number ".$row['did_number']." from ast_rt_extensions last called on ".$row['route_last_used'];
		$rows_ast_rt_extensions = $rows_ast_rt_extensions + 1;
	}else{
		echo "\nNo rows found for the above DID in ast_rt_extensions.";
	}
	mysql_query("delete from assigned_dids where did_number=".$row['did_number'])." and user_id=".$row['user_id'];
	if(mysql_affected_rows()>0){
		echo "\nDeleted did number ".$row['did_number']." from assigned_dids.";
		//DEAC this did number
		$set_result = NTS_API_send_command($fp, "DEAC ".$row['did_number']);
		echo "\nResult of DEAC on DID: ".$row['did_number']." is : ".$set_result;
		$rows_assigned_dids = $rows_assigned_dids + 1;
		//Insert record in deleted_dids table
		$deleted_dids_ins_query = "insert into deleted_dids(
						user_id, 
						did_id, 
						did_number,  
						route_number, 
						did_deleted_on,
						delete_reason
					) values(
						'".$row['user_id']."', 
						".$row['id'].", 
						'".$row['did_number']."',
						'".$row["route_number"]."',
						NOW(),
						'Not called for 90 days'
					)";
		$deleted_dids_result = mysql_query($deleted_dids_ins_query);
		//Insert a record into access logs table
		$access_query = "insert into access_logs (user_id, type, type_id, id_address, item) values (".$row['user_id'].",'System',".$row['user_id'].",'178.79.166.188','Number ".$row['did_number']." expired. Last Called: ".$row['route_last_used']."');";
		$access_result = mysql_query($access_query);
		if(mysql_affected_rows()>0){
			echo "\nInserted record in access_logs table.";
			$rows_access_logs = $rows_access_logs + 1;
		}else{
            echo "\nFailed to insert record in access_logs tables.\nquery is :".$access_query."\nError is: ".mysql_error();
        }
		//Send E-mail to the user.
		//$user_query = "select uniquecode, email, name, unsubscribe from user_logins where id=".$row['user_id'];
		//$user_result = mysql_query($user_query);
		
			//$user_row = mysql_fetch_array($user_result);
			if($row['unsubscribe']=='N'){//Send emails only to those who did NOT unsubscribe
				$virt_num = "(+44) ".substr($row['did_number'],1);
				if(send_email($row['user_id'],$row['email'],$row['name'],$virt_num,$row['uniquecode'],$row['site_country'])==true){
					echo "\nEmail sent to ".$row['email'];
					$num_emails_sent = $num_emails_sent +1;
				}else{
					echo "\nFailed to send Email to ".$row['email'];
				}
			}else{
				echo "\nUser unsubscribed for emails. Email NOT SENT.";
			}
		
		//inform the user of the action taken by creating a new message
		$new_message = new UserMessages();
		$new_message->message_title = "DID Number Expired";
		$new_message->message_body = "Your registered DID ".$row['did_number']." has expired and was removed from your account on ".date("D, d M Y",time()).". It was not called for three months (Last Called on: ".$row['route_last_used'].").";
		$new_message->user_id = $row['user_id'];
		$six_years_from_now = time() + (6 * 365 * 24 * 60 * 60);//6 years, 365 days, 24 hours, 60 minutes, 60 seconds
		$new_message->valid_until = date('Y-m-d H:i:s',$six_years_from_now);
		$new_message->viewed = 0;
		$new_message->user_can_delete = 1;
		if($new_message->saveUserMessage()==true){
			echo "\nUserMessage created for user id: ".$row['user_id'];
		}else{
			echo "\nFAILED to create UserMessage for user id: ".$row['user_id'];
		}
		//Immediately reAllo the DID
		$reallo_result=NTS_API_request_specific_did($fp,$row['did_number'],false);
		if($reallo_result=="SUCCESS"){
			//Insert into unassigned DIDs table
			$ins_query = "insert into unassigned_dids (did_number) values ('".$row['did_number']."')";
            $result = mysql_query($ins_query);
            if($result!=false){
            	//Insert successful
                echo "\nSuccessfully inserted ".$row['did_number']." in the unassigned_dids table\n";
            }else{
            	 echo "\nSuccessfully inserted ".$row['did_number']." in the unassinged_dids table\n";
            }
		}else{
			echo "\nFailed to reALLO did : ".$row['did_number'];
		}
	}else{
		echo "\nNo rows found for the above DID in assigned_dids table.";
	}
	echo "\n";//Add a line space to the log file between each iteration.
	//set_time_limit(0);
	
	NTS_API_disconnect($fp);
}//end of while


//Save the number of DIDs removed in the daily_system_stats.90day_dids_removed table
$daily_stats = new daily_stats();
$daily_stats->updateActiveDIDRemoval($rows_assigned_dids);

echo "\n\nNumber of ast_rt_extensions rows deleted: ".$rows_ast_rt_extensions;
echo "\nNumber of assigned_dids rows deleted: ".$rows_assigned_dids;
echo "\nNumber of rows inserted into access_logs table: ".$rows_access_logs;
echo "\nNumber of emails sent: ".$num_emails_sent;
echo "\n\n\n".date(DATE_RSS)."\n\n";

if($conn){
	mysql_close($conn) ;
}

function send_email($user_id,$to_email,$cust_name,$did_number,$uniquecode, $site_country){
	
	switch($site_country){
		case "AU":
			$server_web_name = "https://www.stickynumber.com.au/";
			$server_email = "info@stickynumber.com.au";
			break;
		case "COM":
			$server_web_name = "https://www.stickynumber.com/";
			$server_email = "info@stickynumber.com";
			break;
		case "UK":
			$server_web_name = "https://www.stickynumber.uk/";
			$server_email = "info@stickynumber.uk";
			break;
		default:
			$server_web_name = "https://www.stickynumber.com.au/";
			$server_email = "info@stickynumber.com.au";
			break;
	}
	
	$from_email = ACCOUNT_CHANGE_EMAIL;
	if(trim($from_email)==""){
		echo "\nERROR: NO FROM EMAIL";
		return false;
	}
	$subject_line = $did_number." | Virtual Number Expired";
	$email_hmtl_start = '<html> 
  	<body style="padding: 10px 15px; background: #FDFFC0; border: 1px dotted #999; color: #666; margin-bottom: 15px;">'; 
	$email_top = "
	Hello $cust_name,<br />
	<br />
	Your virtual number $did_number has <font color='red'>EXPIRED</font>. 
	Due to inactivity your virtual number will no longer show within your account and calls can no longer be received. 
	<br /><br />
	-------------------------------------------------------------------------------------------------------
	<br /><br />
	<b>$did_number</b>
	<br /><br />
	Expiry Date:&nbsp;&nbsp;".date("d M Y")."
	<br />Status:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Expired
	<br /><br />
	-------------------------------------------------------------------------------------------------------
	<br /><br />";
	$email_body = '<br />
	If you have any questions or need assistance with your account management, please contact us.<br />
 	<br />
	Kind Regards,<br />
 	<br />
	Sticky Number <br />
	Customer Support<br />

	e: <a href="mailto:'.$server_email.'">'.$server_email.'</a><br />
	w: <a href="'.$server_web_name.'">'.$server_web_name.'</a><br />';
	$email_hmtl_end = '
	<br />You are receiving this email because you signed up on StickyNumber.com. Please click here to <a href="https://www.stickynumber.com/unsubscribe.php?id='.$uniquecode.'">unsubscribe</a>.
  	</body> 
	</html>';

	$email_message  = $email_hmtl_start . $email_top . $email_body . $email_hmtl_end;
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: Sticky Number <' . $from_email .'>' . "\r\n";
	$addl_params = '-f '. $from_email.' -r '.$from_email."\r\n";
	$status = mail($to_email, $subject_line, $email_message, $headers, $addl_params);
	if($status==true){
		$status = create_client_notification($user_id, $from_email, $to_email, $subject_line, $email_message);
		if(is_numeric($status)==false && substr($status,0,6)=="Error:"){
			echo $status;
			return false;
		}else{
			return true;
		}
	}
	
}