<?php
function getDeletedDIDsPage(){
	
	 $get_CID    =   $_GET['id'];
     if(!empty($get_CID)){
         $get_cidURL =   "?id=".$get_CID;
     }
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
	
	$page_html = '';
	$tip_box='';
	if($page_vars['action_message'] != ""){
		$tip_box.=$page_vars['action_message'];
	}
	
	$page_html .= '<div class="title-bg"><h1>&nbsp;Deleted or Expired Numbers</h1></div>
				<div class="mid-section">
				<div class="date-bar">
					<div class="left-info"><b>'.$tip_box.'</b></div>
										</div>';
	if($page_vars['deleted_DIDs_count'] > 0){
		$page_html .= '
			<table width="100%" border="0" cellspacing="0" cellpadding="4" class="edit-number-routes">
			<tr class="title">
						<td width="22%">Virtual Numbers</td>
						<td width="22%">Routes To</td>
						<td width="18%">Date Deleted</td>
						<td width="18%">Delete Reason</td>
						<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Action</td>
			</tr>';
		foreach($page_vars['deleted_DIDs'] as $deleted_DID){
			$page_html .= '
				<tr>
					<td>'.$deleted_DID['did_number'].'</td>
					<td>'.$deleted_DID['route_number'].'</td>
					<td>'.$deleted_DID['did_deleted_on'].'</td>
					<td>'.$deleted_DID['delete_reason'].'</td>
					<td align="center">
					<form action="deleted_numbers.php'.$get_cidURL.'" method="post" accept-charset="utf-8">
					<input type="hidden" name="did_number" value="'.$deleted_DID['did_number'].'" id="did_number">
					<input type="hidden" name="route_number" value="'.$deleted_DID['route_number'].'" id="route_number">
					<input type="submit" name="submit"   value="activate" class="logged-button" style="font-size:12px;" >
					</form></td>
				</tr>';
		}//end of foreach loop
		$page_html .= '</table>'."\n";
	
		if($page_vars['page_count'] > 1){	
			$page_html .= '<form name="frmPaginate" id="frmPaginate" action="deleted_numbers.php" method="post">';
			$page_html .= '<p align="right">Showing '.$page_vars['page_current_range_start'].'-'.$page_vars['page_current_range_end'].' of '.$page_vars['my_DID_count']."\n"; 
			$page_html .= '<select name="paginate" onchange="javascript:document.frmPaginate.submit();">'."\n";
			$page_deck = $page_vars['page_deck'];
			$i=0;
			foreach($page_deck as $page_range){
				$i++;
				$page_html .= '<option value="'.$i.'">'.$page_range.'</option>'."\n";
			}			 
			$page_html .= '</select>
		</p></form>';
		}//end of if page count > 1
	}else{
		$page_html .= '&nbsp;<em>You have no deleted or expired virtual numbers.</em>';
	}
	 $page_html .='</div>';
	return $page_html;
	
}