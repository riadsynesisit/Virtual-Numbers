<?php
//echo"<pre>";
//print_r($_SESSION);

        if($_POST['action'] ==  'delete')
        {
            $deleteID   =   mysql_real_escape_string(trim($_POST['deleteID']));
            
            $chkQry     =   mysql_query("SELECT * FROM assigned_dids WHERE id LIKE '$deleteID'");
            if(mysql_num_rows($chkQry) > 0)
            {
                $didInfo    =   mysql_fetch_object($chkQry);
            
                $insQry         =   "INSERT INTO assigned_dids_archieve SET
                user_id         =   '$didInfo->user_id',
                did_number      =   '$didInfo->did_number',
                route_number    =   '$didInfo->route_number',
                callerid_on     =   '$didInfo->callerid_on',
                ringtone        =   '$didInfo->ringtone',
                total_minutes   =   '$didInfo->total_minutes',
                did_assigned_on =   '$didInfo->did_assigned_on',
                route_last_used =   '$didInfo->route_last_used',
                route_last_changed  =   '$didInfo->route_last_changed',
                did_block_status=   '$didInfo->did_block_status',
                did_block_reason=   '$didInfo->did_block_reason'";

                mysql_query($insQry);

                mysql_query("DELETE FROM assigned_dids WHERE id = '$deleteID'");
            }
        }
        
        if($_POST['action'] ==  'remove')
        {
            $removeID   =   mysql_real_escape_string(trim($_POST['removeID']));
        
            mysql_query("UPDATE assigned_dids SET did_block_reason = '',did_block_status = 'N' WHERE id = '$removeID'");
        }
        
function getFraudDetectionNoticesPage(){
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
        
	$page_html = '<div class="title-bg"><h1>Fraud Detection Notices</h1></div><div class="mid-section">';
	
	$page_html.=' <div class="date-bar">
                        <div class="left-info" style="float:right;">
                        <b><a href="admin_fraud_detection_archieve_notices.php"><input id="logged-btn" class="logged-button" type="submit" title="" style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" alt="" value="View Archieve" name="logged-btn"></a></b>
                        </div>
                      </div>
        <!-- Date section end here -->				
	';
        
	if(isset($page_vars['action_message']) && $page_vars['action_message'] != ""){
		$page_html .= '<div class="tip_box">';
		$page_html .= $page_vars['action_message'];
		$page_html .= '</div>';
	}
	
	$page_html .= '<table width="100%" border="0" cellspacing="0" cellpadding="7" class="my-call-recording">';
	
//	if(count($page_vars['my_cdrs'])>0){
//		$page_html .= '<caption>Your call records for your Virtual Numbers</caption>';
//	}else{
//		$page_html .= '<caption>You have no calls received for your Virtual Numbers</caption>';
//	}
	
	$page_html .= '<tr class="title">
				<td width="50%" class="first td_order_DESC">Reason of Block</td>
				<td width="13%" class="td_order_ASC">Member ID</td>
				<td width="10%" class="td_order_DESC">DID</td>
				<td width="27%" class="td_order_DESC">Action</td>
                        </tr>';
	
	if($page_vars['my_DIDs_count'] > 0){
		foreach($page_vars['my_DIDs'] as $my_DID){
                if($my_DID["did_block_reason"] == 'Time'){$block_reason = "DID was called 100 times within 300 seconds.";}
                if($my_DID["did_block_reason"] == 'Cost'){$block_reason = "DID used &pound;30 within 24 hours.";}
                if($my_DID["did_block_reason"] == 'ALERT90'){$block_reason = "DID didn't called within last 90 days.";}
                if($my_DID["did_block_reason"] == 'ALERT7'){$block_reason = "New DID didn't called within 7 days.";}
                $memberID   =   'M'.(intval($my_DID["user_id"])+10000);
			$page_html .='<tr >
				<td>'.$block_reason.'</td>
				<td>'.$memberID.'</td>
				<td>'.$my_DID["did_number"].'</td>
				<td>
                                <input id="logged-btn" class="logged-button" onclick="return deleteRecord('.$my_DID["id"].')" type="submit" style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" alt="" value="Archieve" name="logged-btn">
                                <input id="logged-btn" class="logged-button" onclick="return removeBlock('.$my_DID["id"].')" type="submit" style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" alt="" value="Remove Block" name="logged-btn">
                                </td>            
                        </tr>';
		}//end of foreach
	}//end of if count
	
	$page_html .= '</table>';
	
	if($page_vars['page_count'] > 1){	
		$page_html .= '<p align="right">Showing '.$page_vars['page_current_range_start'].'-'.$page_vars['page_current_range_end'].' of '.$page_vars['my_total_call_count']."\n"; 
		$page_html .= '<select name="paginate" onchange="window.location = \'admin_fraud_detection_notices.php?page=\'+this.value">'."\n";
		$page_deck = $page_vars['page_deck'];
		$i=0;
		foreach($page_deck as $page_range){
			$i++;
			$page_html .= '<option value="'.$i.'">'.$page_range.'</option>'."\n";
		}			 
		$page_html .= '</select>
	</p>';
	}//end of if page count > 1
    $page_html.='</div> <!-- Mid section start here -->
    
    <form name="deleteForm" action="" method="post">
    <input type="hidden" name="action" value="delete"/>
    <input type="hidden" name="deleteID" value=""/>
    </form>
    
    <form name="removeForm" action="" method="post">
    <input type="hidden" name="action" value="remove"/>
    <input type="hidden" name="removeID" value=""/>
    </form>
    <script type="text/javascript">
    function deleteRecord(id)
    {
        var agree   =   confirm("Are you sure you want to archieve this record?");
        if(agree){
            document.deleteForm.deleteID.value = id;
            document.deleteForm.submit();
        }else{
            return false;
        }
    }
    
    function removeBlock(id)
    {
        var agree   =   confirm("Are you sure you want to remove block from this record?");
        if(agree){
            document.removeForm.removeID.value = id;
            document.removeForm.submit();
        }else{
            return false;
        }
    }
    </script>
    ';
	return $page_html;
}
?>