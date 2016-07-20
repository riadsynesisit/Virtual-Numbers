<?php
function getSearchPage(){
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
	
	$page_html = '
		<div id="page-wrapper">
			<div class="row">
            	<div class="col-lg-12">
                	<h1 class="page-header">Member Search</h1>
	';
	
	if($page_vars['action_message'] != ""){
		$page_html .= '<div class="tip_box">';
		$page_html .= $page_vars['action_message'];
		$page_html .= '</div>';
	}
	
	$page_html .= '
	<script>
		function show_didww_status(){
			var did_no = parseInt(document.getElementsByName("did")[0].value);
			var member_no = parseInt(document.getElementsByName("id")[0].value);
			console.log(did_no+" **** "+member_no);
			if( did_no && member_no ){
				var customer_id = member_no-10000;
				console.log(customer_id +" <==> "+did_no);
				
				jQuery.ajaxSetup({async:false});
				var url = "../ajax/ajax_show_didww_status.php";
				$.post(url, {"customer_id" : customer_id, "did_no" : did_no}, function(response){
						console.log(response);
						$("#div_show_didww td:nth-child(2)").html("");
						if(response.length == 4 ){
							alert("Number response returned : "+response);
							return false;
						}
						
						response = jQuery.parseJSON(response);
						
						$(".td_status").html("Status");
						$(".td_didww_cntry").html(response.country_name);
						$(".td_city_name").html(response.city_name);
						$(".td_did_name").html(response.did_number);
						$(".td_expiry").html(response.did_expire_date_gmt);
						$(".td_order").html(response.order_id);
						$(".td_auto_renew").html(response.autorenew_enable);
						$(".td_balance").html(response.prepaid_balance);
						$("#div_show_didww").show();
						return;
					
				});
			
			}else{
				alert("Enter the member ID and DID properly");
				$("#div_show_didww").hide();
			}
		}
		
		function cancel_futurepay(){
			var pymt_gtwy = document.getElementById("pymt_gtwy").value;
			var trans_id = document.getElementById("trans_id").value;
			if($.trim(pymt_gtwy)==""){
				alert("Please select the payment gateway");
				return;
			}
			if($.trim(trans_id)==""){
				alert("Please enter the transaction id");
				return;
			}
			jQuery.ajaxSetup({async:false});
			var url = "../ajax/ajax_cancel_futurepay.php";
            $.post(url, {"pymt_gtwy" : pymt_gtwy, "trans_id" : trans_id, "cancel_reason" : "Cancelled from member search page"}, function(response){
            	if(response.substr(0,6)=="Error:"){
            		alert(response);
            		return;
            	}else{
            		alert("Success: Future Pay agreement successfully cancelled");
            		//clear the inputs on successful return to cancel the next transaction
            		$("#pymt_gtwy").val("");
            		$("#trans_id").val("");
            	}
            });
		}
	</script>
	<style>
		#div_show_didww tr:nth-child(even) {background: #CCC}
	</style>
	';
	
	$page_html .= '<div style="float:left"><form name="input" action="search.php" method="post" >
	<table width="100%" border="0" cellspacing="0" cellpadding="7" class="update-details">
	<tr>
	<td idth="30%">
	<input name="ip_address" value=""  class="update-input" type="textbox">
	</td>
	<td width="70%">
	Ip Address
	</td>	
	</tr>
	<tr>
	<td width="30%">
	<input name="did" value="" class="update-input" type="textbox">
	</td>
	<td width="70%">
	DID
	</td>	
	</tr>
	<tr>
	<td width="30%">
	<input name="dest" value="" class="update-input" type="textbox">
	</td>
	<td width="70%">
	Destination No
	</td>	
	</tr>
	<tr>
	<td width="30%">
	<input name="email" value="" class="update-input" type="textbox">
	</td>
	<td width="70%">
	Email
	</td>	
	</tr>
	<tr>
	<td width="30%">
	<input name="id" value="" class="update-input" type="textbox">
	</td>
	<td width="70%">
	Member Id
	</td>	
	</tr>
	<tr>
	<td width="30%">
	<input name="transId" value="" class="update-input" type="textbox">
	</td>
	<td width="70%">
	Transaction ID
	</td>	
	</tr>
	<tr>
	<td>
	<input name="Submit" value="Search" type="submit" class="continue-btn"/>&nbsp;&nbsp;
	<input id="btnShowDidwwStatus" class="continue-btn" onclick="show_didww_status();" value="Show DIDWW" type="button">
	</td>
	</tr>
	</table>
	</form></div>
	
	<div id="div_show_didww" style="display:none">
		<table style="width:35%;text-align:center" cellspacing="5" cellpadding="7" class="update-details" border="1"> 	   
			<tr style="font-weight:bold"><td>Prameter</td><td class="td_status"> Status </td></tr>
			<tr><td>country_name</td><td class="td_didww_cntry">&nbsp;</td></tr>
			<tr><td>city_name</td><td class="td_city_name">&nbsp;</td></tr>
			<tr><td>did_number</td><td class="td_did_name">&nbsp;</td></tr>
			<tr><td>did_expire_date_gmt</td><td class="td_expiry"></td></tr>
			<tr><td>order_id</td><td class="td_order"></td></tr>
			<tr><td>autorenew_enable</td><td class="td_auto_renew"></td></tr>
			<tr><td>prepaid_balance</td><td class="td_balance"></td></tr>
		</table>
	</div>
	<div style="clear:both">&nbsp;</div>
	<h2>&nbsp;Cancel Future Pay Agreements</h2>
	&nbsp;&nbsp;&nbsp;Payment Gateway: <select name="pymt_gtwy" id="pymt_gtwy">
		<option value="">Please Select</option>
		<option value="PayPal">PayPal</option>
		<option value="WorldPay">WorldPay</option>
	</select>
	&nbsp;&nbsp;&nbsp;Transaction ID:<input name="trans_id" id="trans_id" />
	&nbsp;&nbsp;&nbsp;<input name="cancel_fp" value="Cancel" type="submit" class="continue-btn" onclick="cancel_futurepay();" />
	';
	$page_html .= '</div>';
	$page_html .= '</div>';
	$page_html .= '</div>';
	 return $page_html;
	}
function getSearchMembersList(){
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
	
	$page_html = '
		<div id="page-wrapper">
			<div class="row">
            	<div class="col-lg-12">
                	<h1 class="page-header">Member Search List</h1>
	';
	
	$page_html .= '<table width="100%" border="0" cellspacing="0" cellpadding="7" class="my-call-recording">
		<tbody>';
	
	if(count($page_vars['members'])>0){
		$page_html .= '';
	}else{
		$page_html .= '<caption>Members List Not Found.</caption>';
	}
	
	$page_html .= '<tr class="title">
				<td width="17%" class="first td_order_DESC">Member Id (Site)</td>
				<td width="17%" class="td_order_ASC">Email</td>';
				if($page_vars['did']!='')
				{
				 $page_html .= '<td width="17%" class="td_order_ASC">DID</td>';
				 $page_html .= '<td width="17%" class="td_order_ASC">DID Status</td>';
				}
				if($page_vars['dest']!='')
				{
				 $page_html .= '<td width="17%" class="td_order_ASC">Destination No</td>';
				}
				$page_html .='<td width="7%" class="td_order_ASC">Account Status</td>
				<td width="25%" class="td_order_ASC">Login</td>
				
			</tr>';
	
	if(count($page_vars['members'])>0){
		foreach($page_vars['members'] as $one_member){
			$page_html .='<tr class="tr_highlight">
				<td  class="link_style1"><a href="account_details.php?id='.$one_member['id'].'&type=admin"><span class="link_style1">M'.(intval($one_member['id'])+10000).' ('.$one_member['site_country'].')</span></td>  
				<td>'.$one_member["email"].'</td>';
				if($page_vars['did']!='')
				{
				 $page_html .= '<td>'.$one_member['did_number'].'</th>';
				 $page_html .= '<td>'.$one_member['did_status'].'</th>';
				}
				if($page_vars['dest']!='')
				{
				 $page_html .= '<td>'.$one_member['route_number'].'</th>';
				}
				$page_html .='<td>'.$one_member["status"].'</td>';
				if($one_member["status"]=='Active')
				{
					if($_SERVER["HTTP_HOST"]=="system.hawkingsoftware.ae"){
						if($one_member['site_country']=="AU"){
							$login_as_member_url = "https://www.stickynumber.com.au/dashboard/";
						}elseif($one_member['site_country']=="UK"){
							$login_as_member_url = "https://www.stickynumber.uk/dashboard/";
						}else{
							$login_as_member_url = "http://member.stickynumber.com/dashboard/";
						}
					}else{
						$login_as_member_url = "";
					}
					$page_html .='<td class="link_style1"><a href="'.$login_as_member_url.'admin_login.php?id='.$one_member["id"].'" target="blank">Login as Member</a></td>';
				}
				else
				{
                  $page_html .='<td>Login Restricted</td>';
				}	
			$page_html .='</tr>';
		}//end of foreach
	}//end of if count
	
	$page_html .= '</tbody>
	</table>';
	
	if($page_vars['page_count'] > 1){	
		$page_html .= '<form name="frmPaginate" id="frmPaginate" action="search.php" method="post">';
		$page_html .= '<input type="hidden" name="ip_address" value="'.$_POST["ip_address"].'">';
		$page_html .= '<input type="hidden" name="did" value="'.$_POST["did"].'">';
		$page_html .= '<input type="hidden" name="dest" value="'.$_POST["dest"].'">';
		$page_html .= '<input type="hidden" name="email" value="'.$_POST["email"].'">';
		$page_html .= '<input type="hidden" name="id" value="'.$_POST["id"].'">';
		$page_html .= '<input type="hidden" name="Submit" value="Submit">';
		$page_html .= '<p align="right">Showing '.$page_vars['page_current_range_start'].'-'.$page_vars['page_current_range_end'].' of '.$page_vars['my_total_members_count']."\n"; 
		$page_html .= '<select name="paginate" onchange="javascript:document.frmPaginate.submit();" >'."\n";
		$page_deck = $page_vars['page_deck'];
		$i=0;
		foreach($page_deck as $page_range){
			$i++;
			$page_html .= '<option value="'.$i.'" onclick="window.location =\'search.php?page='.$i.'&ip_address='.$page_vars['ip_address'].'&id='.$page_vars['id'].'&email='.$page_vars['email'].'&did='.$page_vars['did'].'&dest='.$page_vars['dest'].'&Submit='.$page_vars['Submit'].'\' " >'.$page_range.'</option>'."\n";
		}			 
		$page_html .= '</select>
	</p></form>';
	}//end of if page count > 1
	$page_html .= '</div>';
	$page_html .= '</div>';
	$page_html .= '</div>';
	return $page_html;
}

?>