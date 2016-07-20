<?php
function update_card(){
	
	global $this_page;
    
	$country = new Country();
	$countries = $country->getAllCountry();
	
	$page_vars = $this_page->variable_stack;
	
	$page_html = "";
	
	$page_html .="
	
	<script>
	
			$(document).ready(function(){
			
				//Allow only numbers in Card Number field
                $('#card-number').keyup(function(event) {
                        var val = $(this).val();
                        if (val.length == 0) {
                            return true;
                        }
                        
                        var tmp = val;
                        
                        var last_char = val[ val.length -1 ]
                        var char_code = val.charCodeAt(val.length - 1) 
                        if ((char_code < 48 || char_code > 57)) {
                            tmp = '';
                            for(var i=0; i < val.length - 1; i++) {
                                tmp += val[i]         
                            }
                                 
                        } 
                        
                        $(this).val( tmp )
                })
                
                $('#billing-security-cvv2').keyup(function(event) {
                        var val = $(this).val();
                        if (val.length == 0) {
                            return true;
                        }
                        
                        var tmp = val;
                        
                        var last_char = val[ val.length -1 ]
                        var char_code = val.charCodeAt(val.length - 1) 
                        if ((char_code < 48 || char_code > 57)) {
                            tmp = '';
                            for(var i=0; i < val.length - 1; i++) {
                                tmp += val[i]         
                            }
                                 
                        } 
                        
                        $(this).val( tmp )
                })
			
				$('#btn-add-card').click(function(event) {
                	if(validate_payment()==true){
						$('#frm-step1').submit();
                	}//end of validate_payment
                })
			});//End of $(document).ready(function()
			
			function validate_payment() {

				if($.trim($('#billing-street').val()) == ''){
					alert('Please enter your street address');
					$('#billing-street').focus();
					return false;
				}
				if($.trim($('#billing-city').val()) == ''){
					alert('Please enter your city');
					$('#billing-city').focus();
					return false;
				}
				if($.trim($('#billing-country').val()) == ''){
					alert('Please enter your country');
					$('#billing-country').focus();
					return false;
				}
				/*if($.trim($('#billing-state').val()) == ''){
					alert('Please enter your state/region');
					$('#billing-state').focus();
					return false;
				}*/
				if($.trim($('#billing-postal-code').val()) == ''){
					alert('Please enter your postal code');
					$('#billing-postal-code').focus();
					return false;
				}
				if($.trim($('#payment-method').val()) == ''){
					alert('Please select your payment method');
					$('#payment-method').focus();
					return false;
				}
				if($.trim($('#payment-card-type').val()) == ''){
					alert('Please select your card type');
					$('#payment-card-type').focus();
					return false;
				}
				if($.trim($('#card-number').val()) == ''){
					alert('Please enter your card number');
					$('#card-number').focus();
					return false;
				}
				if($('#card-number').val().length < 12){
					alert('Please enter card number (min 12 digits)');
					$('#card-number').focus();
					//The following three lines set cursor at end of existing input value
					var tmpstr = $('#card-number').val();//Put val in a temp variable
                    $('#card-number').val('');//Blank out the input
                    $('#card-number').val(tmpstr);//Refill using temp variable value
					return false;
				}
				if(isNaN($.trim($('#card-number').val()))){
					alert('Please enter a numeric Card Number');
					$('#card-number').focus();
					return false;
				}
				if($.trim($('#card-expiry-month').val()) == ''){
					alert('Please select your card expiry month');
					$('#card-expiry-month').focus();
					return false;
				}
				if($.trim($('#card-expiry-year').val()) == ''){
					alert('Please select your card expiry year');
					$('#card-expiry-year').focus();
					return false;
				}
				if($.trim($('#billing-security-cvv2').val()) == ''){
					alert('Please enter your card Security / CVV2 number');
					$('#billing-security-cvv2').focus();
					return false;
				}
				if($('#billing-security-cvv2').val().length < 3 || $('#billing-security-cvv2').val().length > 4){
					alert('Please enter card Security / CVV2 (3 or 4 digits)');
					$('#billing-security-cvv2').focus();
					//The following three lines set cursor at end of existing input value
					var tmpstr = $('#billing-security-cvv2').val();//Put val in a temp variable
                    $('#billing-security-cvv2').val('');//Blank out the input
                    $('#billing-security-cvv2').val(tmpstr);//Refill using temp variable value
					return false;
				}
				if(isNaN($.trim($('#billing-security-cvv2').val()))){
					alert('Please enter a numeric Security/CVV2 Number');
					$('#billing-security-cvv2').focus();
					return false;
				}
				
				return true;
			}
			
	</script>
	
	";
	
	//$page_html .= '<div class="mid-section">';
	
	$cc_data = $page_vars["cc_data"];
	
	switch($cc_data["card_type"]){
		case "CC":
			$cc_selected = " selected ";
			break;
		case "DC":
			$dc_selected = " selected ";
			break;
		case "PC":
			$pc_selected = " selected ";
			break;
	}
	
	switch($cc_data["card_logo"]){
		case "Visa":
			$visa_selected = " selected ";
			break;
		case "Mastercard":
			$mastercard_selected = " selected ";
			break;
		case "Amex":
			$amex_selected = " selected ";
			break;
	}
	
	switch($cc_data["exp_month"]){
		case "01":
			$jan_selected = " selected ";
			break;
		case "02":
			$feb_selected = " selected ";
			break;
		case "03":
			$mar_selected = " selected ";
			break;
		case "04":
			$apr_selected = " selected ";
			break;
		case "05":
			$may_selected = " selected ";
			break;
		case "06":
			$jun_selected = " selected ";
			break;
		case "07":
			$jul_selected = " selected ";
			break;
		case "08":
			$aug_selected = " selected ";
			break;
		case "09":
			$sep_selected = " selected ";
			break;
		case "10":
			$oct_selected = " selected ";
			break;
		case "11":
			$nov_selected = " selected ";
			break;
		case "12":
			$dec_selected = " selected ";
			break;
	}
	
	switch($cc_data["is_primary"]){
		case "0":
			$primary_backup_none = " selected ";
			break;
		case "1":
			$primary_backup_primary = " selected ";
			break;
		case "2":
			$primary_backup_backup = " selected ";
			break;
	}
	
	$page_html.='
	
		<!-- Main Container -->
                                <div class="contentbox">
                                     <div class="contentbox-head">
                                         <div class="contentbox-head-title">Update Card Details</div>
                                          <div class="row Mtop20">
                                              <div class="subsection">   
                                                    <div class="row Mtop10">
                                                    	<form method="post" action="update_card.php?id='.$_GET['id'].'" name="frm-step1" id="frm-step1">
                                                         <table cellpadding="0" cellspacing="0" class="paymenttable2">
                                                                <tr>
                                                                   <td class="borderleft">Primary/Back-up?</td>
                                                                   <td>
                                                                   	<select class="selectbox" name="primary-backup" id="primary-backup">
                                                                   		<option value="0" '.$primary_backup_none.'>None</option>
                                                                   		<option value="1" '.$primary_backup_primary.'>Primary Card</option>
                                                                   		<option value="2" '.$primary_backup_backup.'>Back-up Card</option>
                                                                   	</select>
                                                                   </td>
                                                                   <td>&nbsp;</td>
                                                                   <td>
                                                                      <span><img src="img/american_express_curved.png"/></span>
                                                                      <span><img src="img/mastercard_curved.png"/></span>
                                                                      <span><img src="img/visa_curved.png"/></span>
                                                                   </td>                                                                   
                                                                </tr>
                                                                
                                                                 <tr>
                                                                   <td class="borderleft" nowrap>Street Address <span class="redfont">*</span></td>
                                                                   <td><input type="text" class="textinput" name="billing-street" id="billing-street" placeholder="Your Address" value="'.$cc_data["street"].'" /></td>
                                                                   <td nowrap>Payment Method <span class="redfont">*</span></td>
                                                                   <td>
                                                                      <select class="selectbox" name="payment-method" id="payment-method" >
                                                                            <option value="">Please Select...</option>
                                                 							<option value="CC" '.$cc_selected.'>Credit Card</option>
                                                 							<option value="DC" '.$dc_selected.'>Debit Card</option>
                                                 							<option value="PC" '.$pc_selected.'>Prepaid Card</option>
                                                                      </select>
                                                                   </td>                                                                   
                                                                </tr>
                                                                
                                                                <tr>
                                                                   <td class="borderleft" nowrap>City <span class="redfont">*</span></td>
                                                                   <td><input type="text" class="textinput" placeholder="City" name="billing-city" id="billing-city" value="'.$cc_data["city"].'" /></td>
                                                                   <td nowrap>Card Type <span class="redfont">*</span></td>
                                                                   <td>
                                                                      <select class="selectbox" name="payment-card-type" id="payment-card-type" >
                                                                            <option value="">Please select...</option>
                                                 							<option value="Visa" '.$visa_selected.'>Visa</option>
                                                 							<option value="Mastercard" '.$mastercard_selected.'>Mastercard</option> 
                                                 							<option value="Amex" '.$amex_selected.'>Amex</option>
                                                                      </select>
                                                                   </td>                                                                   
                                                                </tr>
                                                                
                                                                <tr>
                                                                   <td class="borderleft" nowrap>Country <span class="redfont">*</span></td>
                                                                   <td>
                                                                   <select class="selectbox" name="billing-country" id="billing-country">
                                                                          <option value="">Select Country</option>';
                                           									foreach($countries as $country) { 
                                           										$page_html .= "<option value='".$country['iso']."'";
                                           										if($country['iso'] == $cc_data["country"]){
                                           											$page_html .= " selected ";
                                           										}
                                           										$page_html .= ">".$country['printable_name'] . "</option>";
                                           									}
                                						$page_html .= '</select>
                                                                   </td>
                                                                   <td nowrap>Card Number <span class="redfont">*</span></td>
                                                                   <td><input type="text" class="textinput" placeholder="Card Number" name="card-number" id="card-number" value="'.$cc_data["card_number"].'" /></td>                  
                                                                </tr>
                                                                
                                                                <tr>
                                                                   <td class="borderleft" nowrap>State/Region</td>
                                                                   <td>
                                                                   <input type="text" class="textinput" placeholder="State/Region" name="billing-state" id="billing-state" value="'.$cc_data["state"].'" />
                                                                   </td>
                                                                   <td nowrap>Expiry date: <span class="redfont">*</span></td>
                                                                   <td>
                                                                   	<select class="selectbox-reg inputhalf" name="card-expiry-month" id="card-expiry-month" >
							                                                 <option value="">Month</option>
							                                                 <option value="1" '.$jan_selected.'>January</option>
							                                                 <option value="2" '.$feb_selected.'>February</option>
							                                                 <option value="3" '.$mar_selected.'>March</option>
							                                                 <option value="4" '.$apr_selected.'>April</option>
							                                                 <option value="5" '.$may_selected.'>May</option>
							                                                 <option value="6" '.$jun_selected.'>June</option>
							                                                 <option value="7" '.$jul_selected.'>July</option>
							                                                 <option value="8" '.$aug_selected.'>August</option>
							                                                 <option value="9" '.$sep_selected.'>September</option>
							                                                 <option value="10" '.$oct_selected.'>October</option>
							                                                 <option value="11" '.$nov_selected.'>November</option>
							                                                 <option value="12" '.$dec_selected.'>December</option>                                                 
							                                          </select>
							                                          <select class="selectbox-reg inputhalf" name="card-expiry-year" id="card-expiry-year" >
							                                                 <option value="">Year</option>';
							                                                 $curr_year = intval(date("Y"));
							                                                 for($i=0;$i<14;$i++){
							                                                 	$page_html .= '<option value="'.($curr_year+$i).'"';
							                                                 	if(($curr_year+$i)==intval($cc_data["exp_year"])){
							                                                 		$page_html .= " selected ";
							                                                 	}
							                                                 	$page_html .= '>'.($curr_year+$i).'</option>';
							                                                 }                     
							                            $page_html .= '</select>
                                                                   </td>                  
                                                                </tr>
                                                                
                                                                <tr class="borderbot">
                                                                   <td class="borderleft" nowrap>Postal Code <span class="redfont">*</span></td>
                                                                   <td><input type="text" class="textinput" placeholder="Your Postal Code" name="billing-postal-code" id="billing-postal-code" value="'.$cc_data["postal_code"].'" /></td>
                                                                   <td nowrap>Security/CVV2 <span class="redfont">*</span></td>
                                                                   <td><input type="text" class="textinput" placeholder="Security/CVV2" name="billing-security-cvv2" id="billing-security-cvv2" value="'.$cc_data["security_code"].'" /></td>                  
                                                                </tr>                                                                
                                                         </table>
                                                         </form>                                                  
                                                    </div>
                                                   
                                                   <div class="row middler Mtop15"><a id="btn-add-card" class="greenmedbtn">Update Card</a></div>
                                                   
                                              </div>
                                          </div>                                          
                                           
                                 <!-- Personal Details closed -->         
                                          
                                          
                                     </div>
                                </div>
                                <!-- Main Container closed -->
	
	';
	
	//$page_html.='</div>';//End of mid-section DIV
	return $page_html;
	
}