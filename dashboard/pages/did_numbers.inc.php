<?php
function getAllAssigned_DIDs(){
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
	
	$page_html='<div class="title-bg"><h1>DID Numbers</h1></div>
				<div class="mid-section">
				
				<!-- Date section start here -->
					<div class="date-bar">
					<div class="left-info"></div>
										</div>
					<!-- Date section end here -->';
	
	
	if($page_vars['action_message'] != ""){
		$page_html .= '<div class="tip_box">';
		$page_html .= $page_vars['action_message'];
		$page_html .= '</div>';
	}
	
	$last_user_id = '';
	$close_table = false;
	
	foreach($page_vars['grouped_by_user'] as $user_did){
		
		$curr_user_id = $user_did['user_id'];
	
		if($last_user_id != $curr_user_id){
			if($last_user_id != ''){//close the previous table
				$page_html .= '<tbody>
				</table>';
			}
			$close_table = true;
			$page_html .= '
			<table width="100%" border="0" cellspacing="0" cellpadding="7" class="modify-details">
				<tbody>
					<tr>
						<td class="first" colspan=3><strong>'.$user_did['name'].'</strong></td>
					</tr>
					<tr class="title">
					<td>DID</td>
					<td>Total Minutes</td>
					<td>Last Used</td>
				</tr>';
		}
		
			$page_html .= '<tr>
						<td> '.$user_did['did_number'].' </td>
						<td> '.$user_did['total_minutes'].' </td>
						<td> '.$user_did['route_last_used'].' </td>
					</tr>';
		$last_user_id = $curr_user_id;
	}//end of foreach
	
	if($close_table){
		$close_table = false;
		$page_html .= '<tbody>
		</table>';
	}
	$page_html.='</div>';
	return $page_html;
	
}
?>