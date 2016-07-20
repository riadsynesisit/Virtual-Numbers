<?php
function getSystemNoticesNew(){
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
	
	$page_html = '
		<div id="page-wrapper">
			<div class="row">
            	<div class="col-lg-12">
                	<h1 class="page-header">System Notices</h1>
	';

	if($page_vars['action_message'] != ""){
		$page_html .= '<div class="tip_box">';
		$page_html .= $page_vars['action_message'];
		$page_html .= '</div>';
	}
	
	$page_html .= '
	
	<form action="admin_system_notices.php" method="post" accept-charset="utf-8">
	<table width="100%" border="0" cellspacing="0" cellpadding="7" class="modify-details">
	 	<tbody>
	 		<caption>Complete the above form and click publish</caption>
			<tr><th class="first">New System Message</th></tr>
			<tr>
				<td style="padding-left:150px;">
					<input type="text" name="message_title" value="message title" id="system_notices_message_title" size="40"></br>
				
					<textarea name="message_body" rows="8" cols="40">message body here</textarea></br>
				
					<label for="severity">severity</label><select name="severity" id="severity" size="1">';
					$i = 0;
					foreach($page_vars['severity'] as $severity){
						$page_html .= '<option value="'.$i.'">'.$severity[1].'</option>';
						$i++;
					}
					$page_html .= '</select></br>
					<label for="message_type">message type</label><select name="message_type" id="message_type" size="1">';
					$i = 0;
					foreach($page_vars['message_type'] as $message_type){
						$page_html .= '<option value="'.$i.'">'.$message_type.'</option>';
						$i++;
					}
					$page_html .= '</select></br>
					<label for="valid_from_day">valid from</label>
					<select name="valid_from_day" id="valid_from_day" size="1">';
						for($i=1;$i<=31;$i++){
							$page_html .= '<option value="'.$i.'"';
							if($i==$page_vars['valid_from_day']){
								$page_html .= ' selected';
							}
							$page_html .= '>'.$i.'</option>';
						}
					$page_html .= '</select>
					<select name="valid_from_months" id="valid_from_months" size="1">';
						for($i=1;$i<=12;$i++){
							$page_html .= '<option value="'.$i.'"';
							if($i==$page_vars['valid_from_month_num']){
								$page_html .= ' selected';
							}
							$page_html .= '>'.$page_vars['months'][$i-1].'</option>';
						}
					$page_html .= '</select>
					<select name="valid_from_years" id="valid_from_years" size="1">';
						for($i=$page_vars['valid_from_year'];$i<=$page_vars['valid_from_year']+13;$i++){
							$page_html .= '<option value="'.$i.'"';
							if($i==$page_vars['valid_from_year']){
								$page_html .= ' selected';
							}
							$page_html .= '>'.$i.'</option>';
						}
					$page_html .= '</select></br>
					<label for="valid_until_day">valid until</label>
					<select name="valid_until_day" id="valid_until_day" size="1">';
						for($i=1;$i<=31;$i++){
							$page_html .= '<option value="'.$i.'"';
							if($i==$page_vars['valid_until_day']){
								$page_html .= ' selected';
							}
							$page_html .= '>'.$i.'</option>';
						}
					$page_html .= '</select>
					<select name="valid_until_months" id="valid_until_months" size="1">';
						for($i=1;$i<=12;$i++){
							$page_html .= '<option value="'.$i.'"';
							if($i==$page_vars['valid_until_month_num']){
								$page_html .= ' selected';
							}
							$page_html .= '>'.$page_vars['months'][$i-1].'</option>';
						}
					$page_html .= '</select>
					<select name="valid_until_years" id="valid_until_years" size="1">';
						for($i=$page_vars['valid_until_year'];$i<=$page_vars['valid_until_year']+13;$i++){
							$page_html .= '<option value="'.$i.'"';
							if($i==$page_vars['valid_until_year']){
								$page_html .= ' selected';
							}
							$page_html .= '>'.$i.'</option>';
						}
					$page_html .= '</select></br>
					<p align=right><input type="submit" name="publish" value="publish"></p>
				</td>
			</tr>
	 	</tbody>
	 </table>
	</form>
	<p>&nbsp;</p>
	';
	$page_html.='</div>';
	$page_html.='</div>';
	$page_html .= '</div>';
	
	return $page_html;
	
}
?>