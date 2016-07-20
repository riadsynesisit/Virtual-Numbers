<?php

        if($_POST['action'] ==  'delete')
        {
            $deleteID   =   mysql_real_escape_string(trim($_POST['deleteID']));
            
            mysql_query("DELETE FROM cdr_archieve WHERE AcctId = '$deleteID'");
        }
        
function getAsteriskArchieveErrorPage(){
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
	
	$page_html = '<div class="title-bg"><h1>Asterisk Errors</h1></div><div class="mid-section">';
	
	$page_html.=' <div class="date-bar">
					<div class="left-info" style="float:right;"><b>
                                        <!--<input id="logged-btn" class="logged-button" type="submit" title="" style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" alt="" value="View Archieve" name="logged-btn">-->
                                        &nbsp;
                                        </b></div>
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
				<td width="15%" class="first td_order_DESC">Call Date</td>
				<td width="15%" class="td_order_ASC">Caller ID</td>
				<td width="15%" class="td_order_DESC">Virtual Number</td>
				<td width="15%" class="td_order_DESC">Routes To</td>
				<td width="15%" class="td_order_DESC">Error</td>
				<td width="10%" class="td_order_DESC">Member ID</td>
                                <td width="15%" class="td_order_DESC">Action</td>
			</tr>';
	
	if(count($page_vars['my_cdrs'])>0){
            
		foreach($page_vars['my_cdrs'] as $one_cdr){
                $memberID   =   'M'.(intval($one_cdr["userfield"])+10000);
			$page_html .='<tr >
				<td>'.$one_cdr["calldate"].'</td>
				<td>'.$one_cdr["clid"].'</td>
				<td>'.$one_cdr["did"].'</td>
				<td>'.$one_cdr["dest_number"].'</td>
				<td>'.$one_cdr["disposition"].'</td>
                                <td>'.$memberID.'</td>
				<td><input id="logged-btn" class="logged-button" type="submit" value="Permanently Delete" name="logged-btn" onclick="deleteRecord('.$one_cdr["callID"].')" style="background:#359DD1;border-radius:4px; width:auto;" ></td>    
			</tr>';
		}//end of foreach
	}//end of if count
	
	$page_html .= '</table>';
	
	if($page_vars['page_count'] > 1){	
		$page_html .= '<p align="right">Showing '.$page_vars['page_current_range_start'].'-'.$page_vars['page_current_range_end'].' of '.$page_vars['my_total_call_count']."\n"; 
		$page_html .= '<select name="paginate" onchange="window.location = \'admin_asterisk_archieve_errors.php?page=\'+this.value">'."\n";
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
    <script type="text/javascript">
    function deleteRecord(id)
    {
        var agree   =   confirm("Are you sure you want to permanently delete this record?");
        if(agree){
            document.deleteForm.deleteID.value = id;
            document.deleteForm.submit();
        }else{
            return false;
        }
    }
    </script>
    ';
	return $page_html;
}
?>