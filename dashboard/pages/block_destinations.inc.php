<?php

function getBlockDestinations(){
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
	
	$page_html = '
		<div id="page-wrapper">
			<div class="row">
            	<div class="col-lg-12">
                	<h1 class="page-header">Outbound Number Manager</h1>
                </div>
            </div>
	';
	
	if(isset($page_vars['action_message']))
	{
	if($page_vars['action_message'] != ""){
		$page_html .= '<div class="tip_box">';
		$page_html .= $page_vars['action_message'];
		$page_html .= '</div>';
	}
	}
	
	$page_html .= '<div class="table-responsive">';
	$page_html .= '<form name="input" action="blocked_destinations.php" method="post" >
	<table width="100%" border="0" cellspacing="0" cellpadding="7" class="update-details">
	<tr>
	<td idth="30%">
	<input name="destination" value="'.$page_vars['destination'].'"  class="update-input" type="textbox">
	</td>
	<td width="70%">
	Outbound Number
	</td>	
	</tr>	
	<tr>
	<td>
	<input name="Submit" value="Search" type="submit" class="continue-btn"/>
	<input name="Add" value="Add" type="submit" class="continue-btn"/>
	</td>
	
	</tr>
	</table>
	</form>
	</div>
	
	';
	
	
	$page_html .= '<div class="table-responsive">';
	$page_html .= '<table class="table table-hover">
		<tbody>';
	
	if(count($page_vars['members'])>0){
		$page_html .= '';
	}else{
		$page_html .= '<caption>Outbound Numbers Not Found.</caption>';
	}
	
	$page_html .= '<thead class="blue-bg">
				<tr>
				<th>Outbound Number</th>
				<th>Created</th>	
				<th>Action</th>
				</tr>
			</thead>';
	
	if(count($page_vars['members'])>0){
		foreach($page_vars['members'] as $one_member){
			$page_html .='
			<form action="blocked_destinations.php" method="post" accept-charset="utf-8">
			<tr class="tr_highlight">				
				<td>'.$one_member["destination"].'</td>
				<td>'.$one_member["datetime"].'</td>
				<td>
				<input type="hidden" name="destiantion_number" value="'.$one_member['destination'].'" id="destiantion_number">
				<input type="hidden" name="destiantion_id" value="'.$one_member['id'].'" id="destiantion_id">
				<input type="submit" name="Edit"   value="Edit" class="logged-button" style="font-size:12px;" >
					<input type="submit" name="Delete"   value="Delete" class="logged-button" style="font-size:12px;"  onclick="javascript:return confirm(\'Are you sure you want to delete Outbound Number :'.$one_member['destination'].' ?\')" >
				</td>';
					
			$page_html .='</tr></form>';
		}//end of foreach
	}//end of if count
	
	$page_html .= '</tbody>
	</table>
	</div>';
	echo $_GET['page'];
	if($page_vars['page_count'] > 1){	
		$page_html .= '<p align="right">Showing '.$page_vars['page_current_range_start'].'-'.$page_vars['page_current_range_end'].' of '.$page_vars['my_total_members_count']."\n"; 
		$de="'".$page_vars['destination']."'";
		$se="'".$page_vars['Submit']."'";
		$page_html .= '<select name="paginate" onchange="javascript:block_pagination(\'blocked_destinations\',this.value,'.$de.','.$se.')">'."\n";
		$page_deck = $page_vars['page_deck'];
		$i=0;
		foreach($page_deck as $page_range){
			$i++;
			$page_html .= '<option value="'.$i.'" '; if(isset($_GET['page']) && $_GET['page']==$i) 
			{ 
				$page_html .= '	selected >'.$page_range.'</option>'."\n";
			}
			else
			{			
			$page_html .= '	>'.$page_range.'</option>'."\n";
		
			}
		}			 
		$page_html .= '</select>
	</p>';
	}//end of if page count > 1
	$page_html .= '</div>';
	$page_html .= '</div>';
	$page_html .= '</div>';
	return $page_html;
}

function getUpdateDestinationPage(){
	
	
	 
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
	
	
	$destiantion_number = $page_vars['destiantion_number'];
	$destiantion_id = $page_vars['destiantion_id'];
	
	$page_html = '
		<div id="page-wrapper">
	';
	
	$page_html .= '<div class="title-bg"><h1>Update Outbound Number</h1></div><div class="mid-section">
	               <div class="date-bar">
					<div class="left-info">Update Outbound Number '.$destiantion_number.'</div>
										</div>
					<!-- Date section end here -->';				
	
	if($page_vars['action_message'] != ""){
		$page_html .= '<div class="tip_box">';
		$page_html .= $page_vars['action_message'];
		$page_html .= '</div>';
	}
	
	
	
	$page_html .= '
	
	<form name="input" action="blocked_destinations.php" method="POST" >
	<table width="100%" border="0" cellspacing="0" cellpadding="7" class="configure-number">		
		<tbody>		
		<tr>
			<td colspan="2" >
				<input type="hidden" name="destiantion_id" value="'.$destiantion_id.'" >
				
			</td>
		</tr>
		<tr>
			<td width="25%" align="right" valign="top">
				
					<input name="destiantion_number" value="'.$destiantion_number.'" size="25" type="text" class="destination-number" />
					<span class="madatory">*</span>
				
			</td>
			<td width="75%">Enter Outbound Number.</td>
		</tr>
		';
		
		
		$page_html .= '<tr>
						<td>&nbsp;</td>
						<td><input type="submit" name="Continue" id="continue" value="Continue" alt="Continue" title="Continue" class="continue-btn"  /></td>
					  </tr>
		
		</tbody>
	</table>
	</form>
	
	
	';
$page_html .= '</div>';
$page_html .= '</div>';
	return $page_html;
	
}

?>