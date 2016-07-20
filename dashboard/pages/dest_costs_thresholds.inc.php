<?php
function getDestCostsThresholdsPage(){
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
	
	$origin_set_uk = array();
	$origin_set_intl = array();
	
	$origin_set_uk = $page_vars['origin_set_uk'];
	$origin_set_intl = $page_vars['origin_set_intl'];
	$origin087_set_uk = $page_vars['origin087_set_uk'];
	$origin087_set_intl = $page_vars['origin087_set_intl'];
	
	$page_html .= '<h1 class="page-header">Destination Costs - Edit Dial-OK Thresholds</h1>';
	
	$page_html .= '
	
	<table width="100%" border="0" cellspacing="0" cellpadding="7" >
		<tr class="title">
			<td><b><font color="red">070 Range</font></b> Calls Originated in UK</td>
			<td><b><font color="red">070 Range</font></b> Calls Originated Internationally</td>
		</tr>
		<tr>
			<td>
				<span id="dcm_text_left_col">
					<form action="admin_dest_costs_mgr.php" method="post" accept-charset="utf-8">
						
						<label for="peak_limit" class="dcm_label" style="padding-right:25px;">Peak limit: </label>
						<input type="text" name="peak_limit" value="'.$origin_set_uk['peak_limit'].'" id="peak_limit" class="dcm_input" size="6" > 
						[From 08:00 monday to 17:59 friday] <br />
						
						<label for="evening_limit" class="dcm_label" style="padding-right:7px;">Evening limit: </label>
						<input type="text" name="evening_limit" value="'.$origin_set_uk['evening_limit'].'" id="evening_limit" class="dcm_input" size="6">
						[From 18:00 monday to 23:59 friday]<br />
						
						<label for="weekend_limit" class="dcm_label">Weekend limit: </label>
						<input type="text" name="weekend_limit" value="'.$origin_set_uk['weekend_limit'].'" id="weekend_limit" class="dcm_input" size="6">
						[From 00:00 saturday to 07:59 monday]<br />
						
						<label for="flag_call_limit" class="dcm_label" style="padding-right:7px;">Flag call limit: </label>
						<input type="text" name="flag_call_limit" value="'.$origin_set_uk['flag_call_limit'].'" id="flag_call_limit" class="dcm_input" size="6">
						 [All time]<br />
                                                 
                                                 <label for="flag_call_cost" class="dcm_label" style="padding-right:7px;">Flag call Cost: </label>
						 <input type="text" name="flag_call_cost" value="'.$origin_set_uk['flag_call_cost'].'" id="flag_call_cost" class="dcm_input" size="6">
						 [All time]<br />
						
						<p><input type="submit" value="OK" name="origin_set_uk"></p>
					</form>
				</span>
			</td>
			<td>
				
				<span id="dcm_text_right_col">

					<form action="admin_dest_costs_mgr.php" method="post" accept-charset="utf-8">
						<label for="peak_limit" class="dcm_label" style="padding-right:25px;">Peak limit: </label>
						<input type="text" name="peak_limit" value="'.$origin_set_intl['peak_limit'].'" id="peak_limit" class="dcm_input" size="6">
						 [From 08:00 monday to 17:59 friday] <br />

						<label for="evening_limit" class="dcm_label" style="padding-right:7px;">Evening limit: </label>
						<input type="text" name="evening_limit" value="'.$origin_set_intl['evening_limit'].'" id="evening_limit" class="dcm_input" size="6">
						 [From 18:00 monday to 23:59 friday]<br />

						<label for="weekend_limit" class="dcm_label">Weekend limit: </label>
						<input type="text" name="weekend_limit" value="'.$origin_set_intl['weekend_limit'].'" id="weekend_limit" class="dcm_input" size="6">
						 [From 00:00 saturday to 07:59 monday]<br />

						<label for="flag_call_limit" class="dcm_label" style="padding-right:7px;">Flag call limit: </label>
						<input type="text" name="flag_call_limit" value="'.$origin_set_intl['flag_call_limit'].'" id="flag_call_limit" class="dcm_input" size="6">
						 [All time]<br />
                                                 
                                                <label for="flag_call_cost" class="dcm_label" style="padding-right:7px;">Flag call Cost: </label>
						<input type="text" name="flag_call_cost" value="'.$origin_set_intl['flag_call_cost'].'" id="flag_call_cost" class="dcm_input" size="6">
                                                [All time]<br />

						<p><input type="submit" value="OK" name="origin_set_intl"></p>
					</form>
				</span>

			</td>
		</tr>
		<tr class="title">
			<td><b><font color="red">087 Range</font></b> Calls Originated in UK</td>
			<td><b><font color="red">087 Range</font></b> Calls Originated Internationally</td>
		</tr>
		<tr>
			<td>
				<span id="dcm_text_left_col">
					<form action="admin_dest_costs_mgr.php" method="post" accept-charset="utf-8">
						
						<label for="peak_limit" class="dcm_label" style="padding-right:25px;">Peak limit: </label>
						<input type="text" name="peak_limit" value="'.$origin087_set_uk['peak_limit'].'" id="peak_limit" class="dcm_input" size="6" > 
						[From 08:00 monday to 17:59 friday] <br />
						
						<label for="evening_limit" class="dcm_label" style="padding-right:7px;">Evening limit: </label>
						<input type="text" name="evening_limit" value="'.$origin087_set_uk['evening_limit'].'" id="evening_limit" class="dcm_input" size="6">
						[From 18:00 monday to 23:59 friday]<br />
						
						<label for="weekend_limit" class="dcm_label">Weekend limit: </label>
						<input type="text" name="weekend_limit" value="'.$origin087_set_uk['weekend_limit'].'" id="weekend_limit" class="dcm_input" size="6">
						[From 00:00 saturday to 07:59 monday]<br />
						
						<label for="flag_call_limit" class="dcm_label" style="padding-right:7px;">Flag call limit: </label>
						<input type="text" name="flag_call_limit" value="'.$origin087_set_uk['flag_call_limit'].'" id="flag_call_limit" class="dcm_input" size="6">
						 [All time]<br />
                                                 
                                                 <label for="flag_call_cost" class="dcm_label" style="padding-right:7px;">Flag call Cost: </label>
						 <input type="text" name="flag_call_cost" value="'.$origin087_set_uk['flag_call_cost'].'" id="flag_call_cost" class="dcm_input" size="6">
						 [All time]<br />
						
						<p><input type="submit" value="OK" name="origin087_set_uk"></p>
					</form>
				</span>
			</td>
			<td>
				
				<span id="dcm_text_right_col">

					<form action="admin_dest_costs_mgr.php" method="post" accept-charset="utf-8">
						<label for="peak_limit" class="dcm_label" style="padding-right:25px;">Peak limit: </label>
						<input type="text" name="peak_limit" value="'.$origin087_set_intl['peak_limit'].'" id="peak_limit" class="dcm_input" size="6">
						 [From 08:00 monday to 17:59 friday] <br />

						<label for="evening_limit" class="dcm_label" style="padding-right:7px;">Evening limit: </label>
						<input type="text" name="evening_limit" value="'.$origin087_set_intl['evening_limit'].'" id="evening_limit" class="dcm_input" size="6">
						 [From 18:00 monday to 23:59 friday]<br />

						<label for="weekend_limit" class="dcm_label">Weekend limit: </label>
						<input type="text" name="weekend_limit" value="'.$origin087_set_intl['weekend_limit'].'" id="weekend_limit" class="dcm_input" size="6">
						 [From 00:00 saturday to 07:59 monday]<br />

						<label for="flag_call_limit" class="dcm_label" style="padding-right:7px;">Flag call limit: </label>
						<input type="text" name="flag_call_limit" value="'.$origin087_set_intl['flag_call_limit'].'" id="flag_call_limit" class="dcm_input" size="6">
						 [All time]<br />
                                                 
                                                <label for="flag_call_cost" class="dcm_label" style="padding-right:7px;">Flag call Cost: </label>
						<input type="text" name="flag_call_cost" value="'.$origin087_set_intl['flag_call_cost'].'" id="flag_call_cost" class="dcm_input" size="6">
                                                [All time]<br />

						<p><input type="submit" value="OK" name="origin087_set_intl"></p>
					</form>
				</span>

			</td>
		</tr>
		<tr>
			<td colspan="2">
				<form action="admin_dest_costs_mgr.php" method="post" accept-charset="utf-8">
					<label for="call087_cost_limit" class="dcm_label" style="padding-right:7px;">Call Cost Limit to offer 087 Range: </label>
					<input type="text" name="call087_cost_limit" value="'.$page_vars['call087_cost_limit'].'" id="call087_cost_limit" class="dcm_input" size="6">
					<input type="submit" value="OK" name="submit087_cost_limit">
				</form>
			</td>
		</tr>
	</table>
	<hr size="3">
	<h3>&nbsp;<u>SIP Cost</u></h3>
	<form name="frm_sip_cost" id="frm_sip_cost" enctype="multipart/form-data" action="admin_dest_costs_mgr.php" method="post">
	&nbsp;SIP Mark Up Cost (%)
	<input type="text" name="sip_mark_up" id="sip_mark_up" value="'.$page_vars['sip_mark_up_cost'].'">
	&nbsp;Currency Rate GBP to AUD
	<input type="text" name="rate_gbp_aud" id="rate_gbp_aud" value="'.$page_vars['rate_gbp_aud'].'">
	<input type="button" name="sip_cost_submit" id="sip_cost_submit" value="Submit">
	</form>
	
	<hr size="3">
	<h3>&nbsp;<u>Toll-Free Cost</u></h3>
	<form name="frm_tf_cost" id="frm_tf_cost" enctype="multipart/form-data" action="admin_dest_costs_mgr.php" method="post">
	&nbsp;Toll-free Mark Up Cost (margin) (%)
	<input type="text" name="tf_mark_up" id="tf_mark_up" value="'.$page_vars['tf_mark_up_cost'].'">
	&nbsp;Currency Rate USD to AUD
	<input type="text" name="rate_usd_aud" id="rate_usd_aud" value="'.$page_vars['rate_usd_aud'].'">
	<input type="button" name="tf_cost_submit" id="tf_cost_submit" value="Submit">
	</form>
	
	<hr size="3">
	<h3>&nbsp;<u>DID Cost</u></h3>
	&nbsp;<a href="didww_costs_csv.php">Download DID Costs CSV template file</a> 
	<form name="fileupload_did" id="fileupload_did" enctype="multipart/form-data" action="didww_costs_csv.php" method="post">
	<br>&nbsp;DID Prices CSV
	&nbsp; <input type="file" name="did_csv_file" id="did_csv_file" size="30" /><input type="button" name="did_csv_upload" id="did_csv_upload" value="Upload"/>
	<input type="hidden" name="APC_UPLOAD_PROGRESS" id="progress_key" value="4e78d40e25711" />
	<iframe id="upload_frame_did" name="upload_frame_did" frameborder="0" border="0" src="" scrolling="no" scrollbar="no" > </iframe>
	</form>
	';
	$page_html .= '</div>';
	
	return $page_html;
	
}