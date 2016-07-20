<?php
function getCallRecordsPage(){
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
        
        $get_CID    =   $_GET['id'];
        if(!empty($get_CID)){
            $get_cidURL =   "id=".$get_CID."&";
        }
        
     /*   $page_html = '<div class="title-bg"><h1>My Call Records</h1></div><div class="mid-section">';
	
	$page_html.=' <div class="date-bar">
					<div class="left-info"><b>Call Records</b></div>
										</div>
					<!-- Date section end here -->				
					
					
				';
	if(isset($page_vars['action_message']) && $page_vars['action_message'] != ""){
		$page_html .= '<div class="tip_box">';
		$page_html .= $page_vars['action_message'];
		$page_html .= '</div>';
	}
	
	$page_html .= '<table width="100%" border="0" cellspacing="0" cellpadding="7" class="my-call-recording">';
	
	if(count($page_vars['my_cdrs'])>0){
		$page_html .= '<caption>Your call records for your Virtual Numbers</caption>';
	}else{
		$page_html .= '<caption>You have no calls received for your Virtual Numbers</caption>';
	}
	
	$page_html .= '<tr class="title">
				<td width="21%" class="first td_order_DESC">Call Date</td>
				<td width="16%" class="td_order_ASC">Caller ID</td>
				<td width="17%" class="td_order_DESC">Virtual Number</td>
				<td width="16%" class="td_order_DESC">Routes To</td>
				<td width="16%" class="td_order_DESC">Call Duration</td>
				<td width="14%" class="td_order_DESC">Status</td>
			</tr>';
	
	if(count($page_vars['my_cdrs'])>0){
		foreach($page_vars['my_cdrs'] as $one_cdr){
                   
			$page_html .='<tr >
				<td >'.$one_cdr["calldate"].'</td>
				<td>'.$one_cdr["clid"].'</td>
				<td>'.$one_cdr["did"].'</td>
				<td>'.$one_cdr["dest_number"].'</td>
				<td>'.$one_cdr["duration"].'</td>
				<td title="'.$one_cdr["vmreason"].'">'.$one_cdr["disposition"].'</td>
			</tr>';
		}//end of foreach
	}//end of if count
	
	$page_html .= '</table>';
	
	if($page_vars['page_count'] > 1){	
		$page_html .= '<p align="right">Showing '.$page_vars['page_current_range_start'].'-'.$page_vars['page_current_range_end'].' of '.$page_vars['my_total_call_count']."\n"; 
		$page_html .= '<select name="paginate" onchange="window.location = \'call_records.php?'.$get_cidURL.'page=\'+this.value">'."\n";
		$page_deck = $page_vars['page_deck'];
		$i=0;
		foreach($page_deck as $page_range){
			$i++;
			$page_html .= '<option value="'.$i.'">'.$page_range.'</option>'."\n";
		}			 
		$page_html .= '</select>
	</p>';
	}//end of if page count > 1
    $page_html.='</div> <!-- Mid section start here -->';*/
        
    $page_html .='
    	<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">My Call Records</h1>
                </div>
                <div class="col-lg-12">';
    			if(count($page_vars['my_cdrs'])<=0){
                	$page_html.='<div class="alert alert-danger"><p>You have no calls received for your Virtual Numbers</p></div>';
    			}else{	
                $page_html.='<div class="table-responsive">
	                <table class="table">
	                <thead class="blue-bg"><tr><th>Call Date</th><th>Caller ID</th><th>Virtual Number(Timezone) </th><th>Routes To</th><th>Call Duration</th><th>Status</th></tr></thead>
	                <tbody>';
                		foreach($page_vars['my_cdrs'] as $one_cdr){
							$tz = "--";
							foreach($_SESSION['user_did_timezone'] as $user_did_tz ){
								if($one_cdr["did"] == $user_did_tz['did']){
									$tz = $user_did_tz['tz'];
									break;
								}
							}
			                $page_html.='<tr>
			                <td>'.$one_cdr["calldate"].'</td>
			                <td>'.$one_cdr["clid"].'</td>
			                <td>'.$one_cdr["did"].'(GMT'.$tz.')</td>
			                <td>'.$one_cdr["dest_number"].'</td>
			                <td>'.$one_cdr["duration"].'</td>
			                <td title="'.$one_cdr["vmreason"].'">'.$one_cdr["disposition"].'</td>
			                </tr>';
                		}//end of foreach
	                $page_html.='</tbody>
	                </table>';
	            }
                $page_html.='</div>
                </div>
            </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->
    ';
    
    if($page_vars['page_count'] > 1){	
		$page_html .= '<p align="right">Showing '.$page_vars['page_current_range_start'].'-'.$page_vars['page_current_range_end'].' of '.$page_vars['my_total_call_count']."\n"; 
		$page_html .= '<select name="paginate" onchange="window.location = \'call_records.php?'.$get_cidURL.'page=\'+this.value">'."\n";
		$page_deck = $page_vars['page_deck'];
		$i=0;
		foreach($page_deck as $page_range){
			$i++;
			$page_html .= '<option value="'.$i.'">'.$page_range.'</option>'."\n";
		}			 
		$page_html .= '</select>
	</p>';
	}//end of if page count > 1
                
	return $page_html;
}
?>