<?php

require "./includes/DRY_setup.inc.php";

function get_page_content($this_page){
	
	$user_id = trim($_SESSION['user_logins_id']);
	if(isset($user_id)==false || trim($user_id)==""){
		echo "Error: No user login session found. Please login again.";
		exit;
	}
	
	//Check if the form is submitted to go to the view notifications page
	if(isset($_POST["notification_id"])==true && trim($_POST["notification_id"])!=""){
		$this_page->content_area .= getNotificationHTML(trim($_POST["notification_id"]));
		common_header($this_page,"User Notification");
		return;
	}
	
	$this_page->content_area = "";
	
	$this_page->content_area .= getNotificationsHTML();
	
	common_header($this_page,"User Notifications");
	
}

function getNotificationsHTML(){
	
	global $this_page;
	
	$user_id = trim($_SESSION['user_logins_id']);
	
	$client_notifications = new Client_notifications();
	if(is_object($client_notifications)==false){
		echo "Error: Failed to create Client notifications object.";
		exit;
	}
	
	$notifications_count = $client_notifications->get_client_notifications_count($user_id);
	if(is_int($notifications_count)==false && substr($notifications_count,0,6)=="Error:"){
		echo $notifications_count;
		exit;
	}
	
	$page_html = '';
	$page_count = 1;//Initialize
	if($notifications_count > 0){
		$records_per_page = RECORDS_PER_PAGE;
		if($notifications_count > $records_per_page){
			//Needs to do pagination here
			$page_count = ceil($notifications_count / $records_per_page);
			$page_deck = array();
			for($page=1;$page<=$page_count;$page++){
				$first_record_in_range = $page * $records_per_page - ($records_per_page -1 );
				$last_record_in_range = $page * $records_per_page;
				if($last_record_in_range > $notifications_count){
					$last_record_in_range = $notifications_count;
				}
				$page_deck[$page] = $first_record_in_range ." - ".$last_record_in_range;
			}
			//now figure out what page we are on
			$page_current = 1;
			$safe_page_select = $_POST['paginate'];
			if($safe_page_select!=""){
				$safe_page_select = intval($safe_page_select);
			}
			if($safe_page_select > 0 && $safe_page_select != $page_current){
				$page_current  = $safe_page_select;
			}
			$page_current_range_start = $page_current * $records_per_page - ($records_per_page -1 );
			$page_current_range_end = $page_current * $records_per_page;
			if ($page_current_range_end > $notifications_count){
				$page_current_range_end = $notifications_count;
			}
		}else{//End of pagination code
			$page_current_range_start = 1;//No multiple pages	
		}
		
		$notifications = $client_notifications->get_client_notifications_for_page($user_id,$page_current_range_start-1);
		if(is_array($notifications)==false && substr($notifications,0,6)=="Error:"){
			echo $notifications;
			exit;
		}
	}//End of if notifications count is > 0
	
	$page_html .='
		<script>
			function view_notification(id){
				$("#notification_id").val(id);
				$("#frm-view-notification").submit();
			}
		</script>
	';
	
	$page_html .= '
		<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">View Notifications</h1>
                </div>
            </div>
            <div class="table-responsive">
				<table class="table table-hover">
					<thead class="blue-bg">
						<tr>
							<th>Date</th>
							<th>Subject</th>
							<th>Status</th>
							<th>Email</th>
						</tr>
					</thead>
					<tbody>';
					if($notifications_count >0){
						foreach($notifications as $notfication){
			 $page_html .= '<tr>
								<td><a href="#" onclick="view_notification('.$notfication["id"].');">'.$notfication["date_sent"].'</a></td>
								<td><a href="#" onclick="view_notification('.$notfication["id"].');">'.$notfication["subject"].'</a></td>
								<td><a href="#" onclick="view_notification('.$notfication["id"].');"><font color="green">Sent</font></a></td>
								<td><a href="#" onclick="view_notification('.$notfication["id"].');"><img src="../images/mail.png"/></a></td>
							</tr>';
						}//End of foreach
					}else{
		  $page_html .= '<tr>
							<td colspan="4"><font color="red">No notifications found.</font></td>
						</tr>';
					}
	$page_html .= '</tbody>
				</table>
			</div>';
			if($page_count > 1){	
						$page_html .= '<form name="frmPaginate" id="frmPaginate" action="view-notifications.php" method="post">';
						$page_html .= '<p align="right">Showing '.$page_current_range_start.'-'.$page_current_range_end.' of '.$lead_dids_count."\n"; 
						$page_html .= '<select name="paginate" onchange="javascript:document.frmPaginate.submit();">'."\n";
						$i=0;
						foreach($page_deck as $page_range){
							$i++;
							$page_html .= '<option value="'.$i.'">'.$page_range.'</option>'."\n";
						}			 
						$page_html .= '</select>
					</p></form>';
					}//end of if page count > 1
        $page_html .= '</div>
        	<form method="post" action="view-notifications.php" name="frm-view-notification" id="frm-view-notification">
            	<input type="hidden" value="" name="notification_id" id="notification_id">
            </form>
	';
	
	return $page_html;
	
}

function getNotificationHTML($id){
	
	global $this_page;
	
	$user_id = trim($_SESSION['user_logins_id']);
	
	$client_notifications = new Client_notifications();
	if(is_object($client_notifications)==false){
		echo "Error: Failed to create Client notifications object.";
		exit;
	}
	
	$notification = $client_notifications->get_client_notification($id,$user_id);
	if(is_array($notification)==false && substr($notification,0,6)=="Error:"){
		echo $notification;
		exit;
	}
	
	$email_contents = str_replace(PHP_EOL,"",$notification["contents"]);
	$email_contents = str_replace("\n","",$email_contents);
	$email_contents = str_replace("\r","",$email_contents);
	$email_contents = str_replace("\r\n","",$email_contents);
	$email_contents = str_replace("\n\r","",$email_contents);
	
	$page_html = '';
	
	$page_html .= '
		<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">View Notification</h1>
                    <div class="panel panel-default Mtop20">
  						<div class="panel-heading">Notification Details</div>
  						<div class="panel-body">
  							<p>Date : <strong class="text-warning">'.$notification["date_sent"].'</strong></p>
    						<p>Status : <strong class="text-warning">Sent</strong></p>
    						<p>Sender : <strong class="text-warning">'.$notification["sender"].'</strong></p>
    						<p>Recipient : <strong class="text-warning">'.$notification["recipient"].'</strong></p>
    						<p>Subject : <strong class="text-warning">'.$notification["subject"].'</strong></p>
    						<p><div id="div-email-contents">'.$email_contents.'</div></p>
  						</div>
					</div>
                </div>
            </div>
        </div>
    ';
	
	return $page_html;
	
}

require("includes/DRY_authentication.inc.php");

?>