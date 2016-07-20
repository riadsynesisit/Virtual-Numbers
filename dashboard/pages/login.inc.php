<?php
function getLoginPage($this_page){
	
	
	
	$page_html='<div class="title-bg2"><h1>Members Sign In</h1></div>
				<div class="mid-section">
				
				<!-- Date section start here -->
					<div class="date-bar2">
					<div class="left-info2">';
					if($this_page->variable_stack['action_message']!=""){
		$page_html .= '<div class="tip_box">';
		$page_html .= $this_page->variable_stack['action_message'];
		$page_html .= '</div>';
	}
	$page_html .= '</div>
										</div>
					<!-- Date section end here -->';
					
	
	
	$page_html .= '
		<div class="members_sign_in">
			
			
			<form class="prefill" action="login.php" method="post">
				<table class="table_style2" cellspacing="0" width="100%">
				<tr>
				<td style="width:10px;text-align:right;">
				Email:
				</td>
				<td >
				<input name="email" type="text" class="email" title="email" value="'.$this_page->variable_stack['user_params']['email'].'" /> 
				</td>
				</tr>
				<tr>
				<td>
				Password:
				</td>
				<td >
				<input name="password" type="password" class="email" title="password" value="'.$this_page->variable_stack['user_params']['password'].'" />
				</td>
				</tr>
				<tr>
				<td>
				</td>
				<td >
				<input name="submit" type="submit" class="buttonStd" value="Login"/>
				</td>
				</tr>
				<tr>
				<td colspan="2">
				<br /><br /><em>... or choose one of the following options:</em>
			<br /><a href="'.SERVER_WEB_NAME.'/sticky_reset.php">Forgot Password</a> | <a href="'.SERVER_WEB_NAME.'">First Time User</a>
				</td>
				</tr>
				</table>
				
				
				
			</form>
			
		</div>
	';
	$page_html .= '</div>';
	return $page_html;
	
}