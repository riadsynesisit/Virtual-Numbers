<html>
	<head>
		<title>StickyNumber API Tester</title>
		<script type="text/javascript" src="../js/jquery-1.9.1.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$("#api_method").change(function(){
					cleanInputs();
					switch($("#api_method").val()){
						case "changePassword":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("new_password");
							break;
						case "createAccount":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("name");
							break;
						case "deleteCreditCard":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("cc_id");
							break;
						case "deletePrimaryBackupSettingOnCC":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("cc_id");
							break;
						case "deleteVoiceMessage":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("voice_message_id");
							break;
						case "disableVoiceMail":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("virtual_number");
							break;
						case "enableVoiceMail":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("virtual_number");
							break;
						case "forwardingCountries":
							//Good to go
							break;
						case "getAccountCredit":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							break;
						case "getAccountDetails":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							break;
						case "getCallRecords":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("start_rec");
							$("#param_name2").val("records_per_page");
							break;
						case "getCallsCount":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							break;	
						case "getCreditCards":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							break;
						case "getCreditCardsCount":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							break;
						case "getCreditCardsDetails":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("start_rec");
							$("#param_name2").val("records_per_page");
							break;
						case "getDIDs":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("start_rec");
							$("#param_name2").val("records_per_page");
							break;
						case "getDIDsCount":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							break;
						case "getInvoices":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("start_rec");
							$("#param_name2").val("records_per_page");
							break;
						case "getInvoicesCount":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							break;
						case "getNewVoiceMailsCount":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							break;
						case "getNewVoiceMailsPerMailBoxCount":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("virtual_number");
							break;
						case "getPlanMinutesNewDID":
							$("#param_name1").val("country_iso");
							$("#param_name2").val("city_prefix");
							$("#param_name3").val("fwd_number");
							break;
						case "getPlans":
							//Good to go
							break;
						case "getTotalVoiceMailsPerMailBox":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("virtual_number");
							break;
						case "getVoiceMailBoxesCount":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							break;
						case "getVoiceMailBoxesInfo":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("start_rec");
							$("#param_name2").val("records_per_page");
							break;
						case "getVoiceMailsInfo":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("virtual_number");
							$("#param_name2").val("start_rec");
							$("#param_name3").val("records_per_page");
							break;
						case "getVoiceMessage":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("voice_message_id");
							break;
						case "makeBackupCC":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("cc_id");
							break;
						case "makePrimaryCC":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("cc_id");
							break;
						case "numberCities":
							$("#param_name1").val("country_iso");
							break;
						case "numberCountries":
							//Good to go
							break;
						case "payByAccountCreditNewDID":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("country_iso");
							$("#param_name2").val("city_prefix");
							$("#param_name3").val("fwd_number");
							$("#param_name4").val("choosen_plan");
							$("#param_name5").val("plan_period");//Accepted values are M - for monthly and Y for annual
							$("#param_name6").val("num_of_months");
							break;
						case "payByAccountCreditRenewDID":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("choosen_plan");
							$("#param_name2").val("plan_period");//Accepted values are M - for monthly and Y for annual
							$("#param_name3").val("num_of_months");
							$("#param_name4").val("did_id");
							break;
						case "payByAccountCreditRestoreDID":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("choosen_plan");
							$("#param_name2").val("plan_period");//Accepted values are M - for monthly and Y for annual
							$("#param_name3").val("num_of_months");
							$("#param_name4").val("did_id");
							break;
						case "payByCCAccountCredit":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("credit_amount");
							$("#param_name2").val("street");
							$("#param_name3").val("city");
							$("#param_name4").val("state");
							$("#param_name5").val("country");
							$("#param_name6").val("postal_code");
							$("#param_name7").val("pay_method");
							$("#param_name8").val("card_type");
							$("#param_name9").val("card_number");
							$("#param_name10").val("expiry_month");
							$("#param_name11").val("expiry_year");
							$("#param_name12").val("security_cvv2");
							break;
						case "payByCCNewDID":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("country_iso");
							$("#param_name2").val("city_prefix");
							$("#param_name3").val("fwd_number");
							$("#param_name4").val("choosen_plan");
							$("#param_name5").val("plan_period");//Accepted values are M - for monthly and Y for annual
							$("#param_name6").val("num_of_months");
							$("#param_name7").val("street");
							$("#param_name8").val("city");
							$("#param_name9").val("state");
							$("#param_name10").val("country");
							$("#param_name11").val("postal_code");
							$("#param_name12").val("pay_method");
							$("#param_name13").val("card_type");
							$("#param_name14").val("card_number");
							$("#param_name15").val("expiry_month");
							$("#param_name16").val("expiry_year");
							$("#param_name17").val("security_cvv2");
							break;
						case "payByCCRenewDID":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("choosen_plan");
							$("#param_name2").val("plan_period");//Accepted values are M - for monthly and Y for annual
							$("#param_name3").val("num_of_months");
							$("#param_name4").val("did_id");
							$("#param_name5").val("street");
							$("#param_name6").val("city");
							$("#param_name7").val("state");
							$("#param_name8").val("country");
							$("#param_name9").val("postal_code");
							$("#param_name10").val("pay_method");
							$("#param_name11").val("card_type");
							$("#param_name12").val("card_number");
							$("#param_name13").val("expiry_month");
							$("#param_name14").val("expiry_year");
							$("#param_name15").val("security_cvv2");
							break;
						case "payByCCRestoreDID":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("choosen_plan");
							$("#param_name2").val("plan_period");//Accepted values are M - for monthly and Y for annual
							$("#param_name3").val("num_of_months");
							$("#param_name4").val("did_id");
							$("#param_name5").val("street");
							$("#param_name6").val("city");
							$("#param_name7").val("state");
							$("#param_name8").val("country");
							$("#param_name9").val("postal_code");
							$("#param_name10").val("pay_method");
							$("#param_name11").val("card_type");
							$("#param_name12").val("card_number");
							$("#param_name13").val("expiry_month");
							$("#param_name14").val("expiry_year");
							$("#param_name15").val("security_cvv2");
							break;
						case "payByExistingCCAccountCredit":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("credit_amount");
							$("#param_name2").val("existing_cc_id");
							break;
						case "payByExistingCCNewDID":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("country_iso");
							$("#param_name2").val("city_prefix");
							$("#param_name3").val("fwd_number");
							$("#param_name4").val("choosen_plan");
							$("#param_name5").val("plan_period");//Accepted values are M - for monthly and Y for annual
							$("#param_name6").val("num_of_months");
							$("#param_name7").val("existing_cc_id");
							break;
						case "payByExistingCCRenewDID":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("choosen_plan");
							$("#param_name2").val("plan_period");//Accepted values are M - for monthly and Y for annual
							$("#param_name3").val("num_of_months");
							$("#param_name4").val("did_id");
							$("#param_name5").val("existing_cc_id");
							break;
						case "payByExistingCCRestoreDID":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("choosen_plan");
							$("#param_name2").val("plan_period");//Accepted values are M - for monthly and Y for annual
							$("#param_name3").val("num_of_months");
							$("#param_name4").val("did_id");
							$("#param_name5").val("existing_cc_id");
							break;
						case "payByPayPalAccountCredit":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("credit_amount");
							break;
						case "payByPayPalNewDID":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("country_iso");
							$("#param_name2").val("city_prefix");
							$("#param_name3").val("fwd_number");
							$("#param_name4").val("choosen_plan");
							$("#param_name5").val("plan_period");//Accepted values are M - for monthly and Y for annual
							$("#param_name6").val("num_of_months");
							break;
						case "payByPayPalRenewDID":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("choosen_plan");
							$("#param_name2").val("plan_period");//Accepted values are M - for monthly and Y for annual
							$("#param_name3").val("num_of_months");
							$("#param_name4").val("did_id");
							break;
						case "payByPayPalRestoreDID":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("choosen_plan");
							$("#param_name2").val("plan_period");//Accepted values are M - for monthly and Y for annual
							$("#param_name3").val("num_of_months");
							$("#param_name4").val("did_id");
							break;
						case "saveVoiceMessage":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("voice_message_id");
							break;
						case "setVoiceMessageAsRead":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("voice_message_id");
							break;
						case "unsaveVoiceMessage":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("voice_message_id");
							break;
						case "updateCreditCard":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("cc_id");
							$("#param_name2").val("primary_back_up");
							$("#param_name3").val("street");
							$("#param_name4").val("city");
							$("#param_name5").val("state");
							$("#param_name6").val("country");
							$("#param_name7").val("postal_code");
							$("#param_name8").val("pay_method");
							$("#param_name9").val("card_type");
							$("#param_name10").val("card_number");
							$("#param_name11").val("expiry_month");
							$("#param_name12").val("expiry_year");
							$("#param_name13").val("security_cvv2");
							break;
						case "updateVoiceMailRings":
							$("#username_hint").val("username");
							$("#password_hint").val("password");
							$("#param_name1").val("virtual_number");
							$("#param_name2").val("rings_before_vm");
							break;
						case "":
							//This is for the first item of "Please Select" in the drop-down
							//Good to go
							break;
						default:
							alert("API Method not in change switch case");
							break;
					}//end of switch
				});
			});

			function cleanInputs(){
				$("#username_hint").val("");
				$("#username").val("");
				$("#password_hint").val("");
				$("#password").val("");
				$("#param_name1").val("");
				$("#param_value1").val("");
				$("#param_name2").val("");
				$("#param_value2").val("");
				$("#param_name3").val("");
				$("#param_value3").val("");
				$("#param_name4").val("");
				$("#param_value4").val("");
				$("#param_name5").val("");
				$("#param_value5").val("");
				$("#param_name6").val("");
				$("#param_value6").val("");
				$("#param_name7").val("");
				$("#param_value7").val("");
				$("#param_name8").val("");
				$("#param_value8").val("");
				$("#param_name9").val("");
				$("#param_value9").val("");
				$("#param_name10").val("");
				$("#param_value10").val("");
				$("#param_name11").val("");
				$("#param_value11").val("");
				$("#param_name12").val("");
				$("#param_value12").val("");
				$("#param_name13").val("");
				$("#param_value13").val("");
				$("#param_name14").val("");
				$("#param_value14").val("");
				$("#param_name15").val("");
				$("#param_value15").val("");
				$("#param_name16").val("");
				$("#param_value16").val("");
				$("#param_name17").val("");
				$("#param_value17").val("");
				$("#api_response").val("");
			}
		
			function submit_request(){
				var api_method=$("#api_method").val();
				var url= "index.php";
				var postdata = { 'method' : $("#api_method").val() };
				if($("#username_hint").val() != ""){
					postdata['username'] = $("#username").val();
				}
				if($("#password_hint").val() != ""){
					postdata['password'] = $("#password").val();
				}
				if($("#param_name1").val() != ""){
					postdata[$("#param_name1").val()] = $("#param_value1").val();
				}
				if($("#param_name2").val() != ""){
					postdata[$("#param_name2").val()] = $("#param_value2").val();
				}
				if($("#param_name3").val() != ""){
					postdata[$("#param_name3").val()] = $("#param_value3").val();
				}
				if($("#param_name4").val() != ""){
					postdata[$("#param_name4").val()] = $("#param_value4").val();
				}
				if($("#param_name5").val() != ""){
					postdata[$("#param_name5").val()] = $("#param_value5").val();
				}
				if($("#param_name6").val() != ""){
					postdata[$("#param_name6").val()] = $("#param_value6").val();
				}
				if($("#param_name7").val() != ""){
					postdata[$("#param_name7").val()] = $("#param_value7").val();
				}
				if($("#param_name8").val() != ""){
					postdata[$("#param_name8").val()] = $("#param_value8").val();
				}
				if($("#param_name9").val() != ""){
					postdata[$("#param_name9").val()] = $("#param_value9").val();
				}
				if($("#param_name10").val() != ""){
					postdata[$("#param_name10").val()] = $("#param_value10").val();
				}
				if($("#param_name11").val() != ""){
					postdata[$("#param_name11").val()] = $("#param_value11").val();
				}
				if($("#param_name12").val() != ""){
					postdata[$("#param_name12").val()] = $("#param_value12").val();
				}
				if($("#param_name13").val() != ""){
					postdata[$("#param_name13").val()] = $("#param_value13").val();
				}
				if($("#param_name14").val() != ""){
					postdata[$("#param_name14").val()] = $("#param_value14").val();
				}
				if($("#param_name15").val() != ""){
					postdata[$("#param_name15").val()] = $("#param_value15").val();
				}
				if($("#param_name16").val() != ""){
					postdata[$("#param_name16").val()] = $("#param_value16").val();
				}
				if($("#param_name17").val() != ""){
					postdata[$("#param_name17").val()] = $("#param_value17").val();
				}
				$.ajaxSetup({async:false});
				$.post(url, postdata, function(response){
					$("#api_response").val(JSON.stringify(response));
				});
			}
		</script>
	</head>
	<form name="frm_api_tester" id="frm_api_tester">
		<table>
			<tr>
			<td>Method:</td><td><input disabled type="text" name="method_hint" id="method_hint" value="method">&nbsp;=&nbsp;
			<select name="api_method" id="api_method">
				<option value="">Please Select</option>
				<option value="changePassword">changePassword</option>
				<option value="createAccount">createAccount</option>
				<option value="deleteCreditCard">deleteCreditCard</option>
				<option value="deletePrimaryBackupSettingOnCC">deletePrimaryBackupSettingOnCC</option>
				<option value="deleteVoiceMessage">deleteVoiceMessage</option>
				<option value="disableVoiceMail">disableVoiceMail</option>
				<option value="enableVoiceMail">enableVoiceMail</option>
				<option value="forwardingCountries">forwardingCountries</option>
				<option value="getAccountCredit">getAccountCredit</option>
				<option value="getAccountDetails">getAccountDetails</option>
				<option value="getCallRecords">getCallRecords</option>
				<option value="getCallsCount">getCallsCount</option>
				<option value="getCreditCards">getCreditCards</option>
				<option value="getCreditCardsCount">getCreditCardsCount</option>
				<option value="getCreditCardsDetails">getCreditCardsDetails</option>
				<option value="getDIDs">getDIDs</option>
				<option value="getDIDsCount">getDIDsCount</option>
				<option value="getInvoices">getInvoices</option>
				<option value="getInvoicesCount">getInvoicesCount</option>
				<option value="getNewVoiceMailsCount">getNewVoiceMailsCount</option>
				<option value="getNewVoiceMailsPerMailBoxCount">getNewVoiceMailsPerMailBoxCount</option>
				<option value="getPlanMinutesNewDID">getPlanMinutesNewDID</option>
				<option value="getPlans">getPlans</option>
				<option value="getTotalVoiceMailsPerMailBox">getTotalVoiceMailsPerMailBox</option>
				<option value="getVoiceMailBoxesCount">getVoiceMailBoxesCount</option>
				<option value="getVoiceMailBoxesInfo">getVoiceMailBoxesInfo</option>
				<option value="getVoiceMailsInfo">getVoiceMailsInfo</option>
				<option value="getVoiceMessage">getVoiceMessage</option>
				<option value="makeBackupCC">makeBackupCC</option>
				<option value="makePrimaryCC">makePrimaryCC</option>
				<option value="numberCities">numberCities</option>
				<option value="numberCountries">numberCountries</option>
				<option value="payByAccountCreditNewDID">payByAccountCreditNewDID</option>
				<option value="payByAccountCreditRenewDID">payByAccountCreditRenewDID</option>
				<option value="payByAccountCreditRestoreDID">payByAccountCreditRestoreDID</option>
				<option value="payByCCAccountCredit">payByCCAccountCredit</option>
				<option value="payByCCNewDID">payByCCNewDID</option>
				<option value="payByCCRenewDID">payByCCRenewDID</option>
				<option value="payByCCRestoreDID">payByCCRestoreDID</option>
				<option value="payByExistingCCAccountCredit">payByExistingCCAccountCredit</option>
				<option value="payByExistingCCNewDID">payByExistingCCNewDID</option>
				<option value="payByExistingCCRenewDID">payByExistingCCRenewDID</option>
				<option value="payByExistingCCRestoreDID">payByExistingCCRestoreDID</option>
				<option value="payByPayPalAccountCredit">payByPayPalAccountCredit</option>
				<option value="payByPayPalNewDID">payByPayPalNewDID</option>
				<option value="payByPayPalRenewDID">payByPayPalRenewDID</option>
				<option value="payByPayPalRestoreDID">payByPayPalRestoreDID</option>
				<option value="saveVoiceMessage">saveVoiceMessage</option>
				<option value="setVoiceMessageAsRead">setVoiceMessageAsRead</option>
				<option value="unsaveVoiceMessage">unsaveVoiceMessage</option>
				<option value="updateCreditCard">updateCreditCard</option>
				<option value="updateVoiceMailRings">updateVoiceMailRings</option>
			</select>
			</td>
			</tr>
			<tr>
			<td>Login Email:</td><td><input disabled type="text" name="username_hint" id="username_hint" value="">&nbsp;=&nbsp;
				<input type="text" name="username" id="username"></td>
			</tr>
			<tr>
			<td>Password:</td><td><input disabled type="text" name="password_hint" id="password_hint" value="">&nbsp;=&nbsp;
				<input type="password" name="password" id="password"></td>
			</tr>
			<tr>
			<td>Parameter1 Name / value:</td><td><input disabled type="text" name="param_name1" id="param_name1" value="">&nbsp;=&nbsp;
				<input type="text" name="param_value1" id="param_value1" value=""></td>
			</tr>
			<tr>
			<td>Parameter2 Name / value:</td><td><input disabled type="text" name="param_name2" id="param_name2" value="">&nbsp;=&nbsp;
				<input type="text" name="param_value2" id="param_value2" value=""></td>
			</tr>
			<tr>
			<td>Parameter3 Name / value:</td><td><input disabled type="text" name="param_name3" id="param_name3" value="">&nbsp;=&nbsp;
				<input type="text" name="param_value3" id="param_value3" value=""></td>
			</tr>
			<tr>
			<td>Parameter4 Name / value:</td><td><input disabled type="text" name="param_name4" id="param_name4" value="">&nbsp;=&nbsp;
				<input type="text" name="param_value4" id="param_value4" value=""></td>
			</tr>
			<tr>
			<td>Parameter5 Name / value:</td><td><input disabled type="text" name="param_name5" id="param_name5" value="">&nbsp;=&nbsp;
				<input type="text" name="param_value5" id="param_value5" value=""></td>
			</tr>
			<tr>
			<td>Parameter6 Name / value:</td><td><input disabled type="text" name="param_name6" id="param_name6" value="">&nbsp;=&nbsp;
				<input type="text" name="param_value6" id="param_value6" value=""></td>
			</tr>
			<tr>
			<td>Parameter7 Name / value:</td><td><input disabled type="text" name="param_name7" id="param_name7" value="">&nbsp;=&nbsp;
				<input type="text" name="param_value7" id="param_value7" value=""></td>
			</tr>
			<tr>
			<td>Parameter8 Name / value:</td><td><input disabled type="text" name="param_name8" id="param_name8" value="">&nbsp;=&nbsp;
				<input type="text" name="param_value8" id="param_value8" value=""></td>
			</tr>
			<tr>
			<td>Parameter9 Name / value:</td><td><input disabled type="text" name="param_name9" id="param_name9" value="">&nbsp;=&nbsp;
				<input type="text" name="param_value9" id="param_value9" value=""></td>
			</tr>
			<tr>
			<td>Parameter10 Name / value:</td><td><input disabled type="text" name="param_name10" id="param_name10" value="">&nbsp;=&nbsp;
				<input type="text" name="param_value10" id="param_value10" value=""></td>
			</tr>
			<tr>
			<td>Parameter11 Name / value:</td><td><input disabled type="text" name="param_name11" id="param_name11" value="">&nbsp;=&nbsp;
				<input type="text" name="param_value11" id="param_value11" value=""></td>
			</tr>
			<tr>
			<td>Parameter12 Name / value:</td><td><input disabled type="text" name="param_name12" id="param_name12" value="">&nbsp;=&nbsp;
				<input type="text" name="param_value12" id="param_value12" value=""></td>
			</tr>
			<tr>
			<td>Parameter13 Name / value:</td><td><input disabled type="text" name="param_name13" id="param_name13" value="">&nbsp;=&nbsp;
				<input type="text" name="param_value13" id="param_value13" value=""></td>
			</tr>
			<tr>
			<td>Parameter14 Name / value:</td><td><input disabled type="text" name="param_name14" id="param_name14" value="">&nbsp;=&nbsp;
				<input type="text" name="param_value14" id="param_value14" value=""></td>
			</tr>
			<tr>
			<td>Parameter15 Name / value:</td><td><input disabled type="text" name="param_name15" id="param_name15" value="">&nbsp;=&nbsp;
				<input type="text" name="param_value15" id="param_value15" value=""></td>
			</tr>
			<tr>
			<td>Parameter16 Name / value:</td><td><input disabled type="text" name="param_name16" id="param_name16" value="">&nbsp;=&nbsp;
				<input type="text" name="param_value16" id="param_value16" value=""></td>
			</tr>
			<tr>
			<td>Parameter17 Name / value:</td><td><input disabled type="text" name="param_name17" id="param_name17" value="">&nbsp;=&nbsp;
				<input type="text" name="param_value17" id="param_value17" value=""></td>
			</tr>
			<tr>
			<td>&nbsp;</td><td><input type="button" value="Submit" onclick="javascript:submit_request();"></td>
			</tr>
			<tr><td>API Response:</td><td><textarea  rows="20" cols="100" name="api_response" id="api_response"></textarea></td></tr>
		</table>
	</form>
</html>