<?php
function getChangePasswordPage($this_page){
	
        $get_CID    =   $_GET['id'];
        if(!empty($get_CID)){
            $get_cidURL =   "?id=".$get_CID;
        }
        
	$page_vars = $this_page->variable_stack;
	
	$page_html = '';
	
	$page_html .= '<div class="title-bg"><h1>Changing Your Password</h1></div>
	<div class="mid-section">
	<div class="date-bar">
					<div class="left-info"><b>Enter your current and desired new password</b></div>
					
					</div>';
	if($page_vars['action_message'] != ""){
		$page_html .= '<div class="tip_box">';
		$page_html .= $page_vars['action_message'];
		$page_html .= '</div>';
	}

	
	$page_html .='<form name="input" action="change_password.php'.$get_cidURL.'" method="post" >
		<table width="100%" border="0" cellspacing="0" cellpadding="7" class="update-details">
			
			<tbody>
				
				<tr>
					<td class="first">Full Name:</td>
					<td width="77%"><a href="account_details.php" class="link_style1">'.$page_vars['user_name'].'</a></td>
				</tr>
				<tr>
					<td class="first">Username:</td>
					<td width="77%"><a href="mailto:'.$page_vars['user_login'].'" class="link_style1">'.$page_vars['user_login'].'</a>&nbsp;</td>
				</tr>
				
				<tr>
					<td class="first">New Password:</td>
					<td id="pass"><input name="new_password" value="" size="15" style="width: 150px;" type="password" /></td>
				</tr>
				<tr>
				<td class="border-none">&nbsp;</td>
				<td><input name="Submit" value="Continue" class="continue-btn"  type="submit"/></td>
				</tr>
			</tbody>
		</table>
		
	</form>
	</div>
	';
	
	return $page_html;
	
}
?>