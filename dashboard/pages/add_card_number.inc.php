<?php
function add_card_number(){
	
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
				
				return true;
			}
			
	</script>
	
	";
	
	$primary_backup_options = "";
	switch(intval($page_vars['cc_count'])){
		case 0://No existing CCs - Adding their first CC - only option is primary
			$primary_backup_options = "<option value=\"1\">Primary Card</option>";
			break;
		case 1://One Primary card already exists - ONLY TWO options Primary and Back-up
			$primary_backup_options = "<option value=\"1\">Primary Card</option>
										<option value=\"2\" selected>Back-up Card</option>";
			break;
		default://Two or more cards already present - ALL THREE options None, Primary and Back-up
			$primary_backup_options = "<option value=\"0\" selected>None</option>
										<option value=\"1\">Primary Card</option>
										<option value=\"2\">Back-up Card</option>";
			break;
	}
	
	//$page_html .= '<div class="mid-section">';
	
	$page_html.='
	
		<!-- Main Container -->
                                <div class="contentbox">
                                     <div class="contentbox-head">
                                         <div class="contentbox-head-title">Add New Card</div>
                                          <div class="row Mtop20">
                                              <div class="subsection">
                                                    <div class="row Mtop10">
                                                    	<form method="post" action="add_new_card.php" name="frm-step1" id="frm-step1">
                                                         <table cellpadding="0" cellspacing="0" class="paymenttable2">
                                                                <tr>
                                                                   <td class="borderleft">Primary/Back-up?</td>
                                                                   <td>
                                                                   	<select class="selectbox" name="primary-backup" id="primary-backup">';
                                                                   	$page_html .= $primary_backup_options;
                                                     $page_html .= '</select>
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
                                                                   <td><input type="text" class="textinput" name="billing-street" id="billing-street" placeholder="Your Address" /></td>
                                                                   <td nowrap>Payment Method <span class="redfont">*</span></td>
                                                                   <td>
                                                                      <select class="selectbox" name="payment-method" id="payment-method" >
                                                                            <option value="">Please Select...</option>
                                                 							<option value="CC">Credit Card</option>
                                                 							<option value="DC">Debit Card</option>
                                                 							<option value="PC">Prepaid Card</option>
                                                                      </select>
                                                                   </td>                                                                   
                                                                </tr>
                                                                
                                                                <tr>
                                                                   <td class="borderleft" nowrap>City <span class="redfont">*</span></td>
                                                                   <td><input type="text" class="textinput" placeholder="City" name="billing-city" id="billing-city" /></td>
                                                                   <td nowrap>Card Type <span class="redfont">*</span></td>
                                                                   <td>
                                                                      <select class="selectbox" name="payment-card-type" id="payment-card-type" >
                                                                            <option value="">Please select...</option>
                                                 							<option value="Visa">Visa</option>
                                                 							<option value="Mastercard">Mastercard</option> 
                                                 							<option value="Amex">Amex</option>
                                                                      </select>
                                                                   </td>                                                                   
                                                                </tr>
                                                                
                                                                <tr>
                                                                   <td class="borderleft" nowrap>Country <span class="redfont">*</span></td>
                                                                   <td>
                                                                   <select class="selectbox" name="billing-country" id="billing-country">
                                                                          <option value="">Select Country</option>';
                                           									foreach($countries as $country) { 
                                           										$page_html .= "<option value='".$country['iso']."'>".$country['printable_name'] . "</option>";
                                           									}
                                						$page_html .= '</select>
                                                                   </td>
                                                                   <td nowrap>Card Number <span class="redfont">*</span></td>
                                                                   <td><input type="text" class="textinput" placeholder="Card Number" name="card-number" id="card-number" /></td>                  
                                                                </tr>
                                                                
                                                                <tr>
                                                                   <td class="borderleft" nowrap>State/Region</td>
                                                                   <td>
                                                                   <input type="text" class="textinput" placeholder="State/Region" name="billing-state" id="billing-state" />
                                                                   </td>
                                                                   <td nowrap>Expiry date: <span class="redfont">*</span></td>
                                                                   <td>
                                                                   	<select class="selectbox-reg inputhalf" name="card-expiry-month" id="card-expiry-month" >
							                                                 <option value="">Month</option>
							                                                 <option value="1">January</option>
							                                                 <option value="2">February</option>
							                                                 <option value="3">March</option>
							                                                 <option value="4">April</option>
							                                                 <option value="5">May</option>
							                                                 <option value="6">June</option>
							                                                 <option value="7">July</option>
							                                                 <option value="8">August</option>
							                                                 <option value="9">September</option>
							                                                 <option value="10">October</option>
							                                                 <option value="11">November</option>
							                                                 <option value="12">December</option>                                                 
							                                          </select>
							                                          <select class="selectbox-reg inputhalf" name="card-expiry-year" id="card-expiry-year" >
							                                                 <option value="">Year</option>';
							                                                 $curr_year = intval(date("Y"));
							                                                 for($i=0;$i<14;$i++){
							                                                 	$page_html .= '<option value="'.($curr_year+$i).'">'.($curr_year+$i).'</option>';
							                                                 }                     
							                            $page_html .= '</select>
                                                                   </td>                  
                                                                </tr>
                                                                
                                                                <tr class="borderbot">
                                                                   <td class="borderleft" nowrap>Postal Code <span class="redfont">*</span></td>
                                                                   <td><input type="text" class="textinput" placeholder="Your Postal Code" name="billing-postal-code" id="billing-postal-code" /></td>
                                                                   <td nowrap>Security/CVV2 <span class="redfont">*</span></td>
                                                                   <td><input type="text" class="textinput" placeholder="Security/CVV2" name="billing-security-cvv2" id="billing-security-cvv2" /></td>                  
                                                                </tr>                                                                
                                                         </table>
                                                         </form>                                                  
                                                    </div>
                                                   
                                                   <div class="row middler Mtop15"><a id="btn-add-card" class="greenmedbtn">Add Card</a></div>
                                                   
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