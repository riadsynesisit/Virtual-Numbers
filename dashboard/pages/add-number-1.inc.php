<?php 
//$user_detail    =   mysql_fetch_object(mysql_query("SELECT password,ip_address FROM user_logins WHERE email = '".$_SESSION['user_login']."'"));
//$user_country   =   $user_detail->password;   
//$ip_address     =   $user_detail->ip_address;

function add_number_1_Page(){
    
	$country_code   =   mysql_fetch_object(mysql_query("SELECT country_code FROM country WHERE printable_name LIKE '".$_SESSION['IP_Country']."'"))->country_code;
    if(empty($country_code)){
        $user_countrycode   =   44;
    }else{
        $user_countrycode   =   $country_code;   
    }

    global $this_page;
	
	$page_vars = $this_page->variable_stack;
	
	$page_html = "";
	$page_html .= '<div class="title-bg"><h1>Configure New Number</h1></div><div class="mid-section">
	               <div class="date-bar">
					<div class="left-info">Configure New Virtual Number </div>
										</div>
					<!-- Date section end here -->';				
	
	
	if($page_vars['action_message'] != ""){
		$page_html .= '<div class="tip_box">';
		$page_html .= $page_vars['action_message'];
		$page_html .= '</div>';
	}
	
	$page_html .= '
	<form name="input" action="add-number-1.php" method="POST" onsubmit="return validateForm();" >
	<table width="100%" border="0" cellspacing="0" cellpadding="7" class="configure-number">		
		
		<tr>
			<td colspan="2" >
				&nbsp;<font color="red"><b><div id="invalidDestination"></div></b><font>
			</td>
		</tr>
		<tr>
		<td colspan="2">
		<div class="tip_box">
		You Can Have Option To Change The Country Code
		</div>
		</td>
		</tr>
		<tr>
		<td colspan="2">
		
		
		<select name="country_code" id="country_code" class="select-country" onchange="country_code_change(this.value)">';
		$page_html .= '<option value="" selected>Select Country</option>';
		foreach($page_vars['country_codes'] as $countryone)
		{
                    $selected   =   "";
                    //if($countryone['country_code'] == $user_countrycode){$selected = "selected";}
                    $page_html .= '<option value="'.$countryone['country_code'].'" '.$selected.'>'.$countryone['printable_name'].'+'.$countryone['country_code'].' </option>';
		}
		$page_html .= '</select>
		
		  Country Code
		</td>
		
		</tr>
		<tr>
			<td width="25%" align="right" valign="top">
				
					<input name="destinationnumber" id="destinationnumber" value="+" size="25" type="text" 	class="destination-number" tabindex="2" onkeyup="dest_changed(event)" /><span class="madatory">*</span>
				
			</td>
			<td width="75%">Enter destination number.</td>
		</tr>';

		$page_html .='<tr>
						<td>&nbsp;</td>
						<td><input type="submit" name="Submit" id="continue" value="Continue" alt="Continue" title="Continue" class="continue-btn" tabindex="6" /></td>
					  </tr>
		
		</table>
					<div class="info">Add Number Wizard Step 1 of 3</div>
	</form>
	

<!-- Google Conversion Tracking  -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 996263337;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "1QT6CPfO9QEQqYuH2wM";
var google_conversion_value = 0;
if (1) {
  google_conversion_value = 1;
}
/* ]]> */
</script>
<script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/996263337/?value=1&amp;label=1QT6CPfO9QEQqYuH2wM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
	
	';
	$page_html.="
	<script type='text/javascript'>
	function country_code_change(destvalue)
	{
	document.getElementById('destinationnumber').value='+'+destvalue;

	}
	function dest_changed(e){
		dest = document.getElementById('destinationnumber').value;
		if(dest.length == 0){
			document.getElementById('destinationnumber').value='+';
			dest = document.getElementById('destinationnumber').value;
		}
		if(dest.charCodeAt(0) != 43){
			document.getElementById('destinationnumber').value='+'+dest;
			dest = document.getElementById('destinationnumber').value;
		}
		for(var i=1;i<=dest.length;i++){
			if(dest.charCodeAt(i)<48 || dest.charCodeAt(i)>57){
				document.getElementById('destinationnumber').value=dest.substr(0,i)+dest.substr(i+1);
			}
		}
		dest = document.getElementById('destinationnumber').value;
		if(dest.length>=5){
			cc = dest.substr(1,4);
		}else{
			cc = dest.substr(1);
		}
		ccset = setDropDownList(document.getElementById('country_code'),cc);
		if(cc.length <= 1 || ccset){return;}
		for(var i=1;i<=4;i++){
			cc = cc.substr(0,cc.length-1);
			ccset = setDropDownList(document.getElementById('country_code'),cc);
			if(cc.length <= 1 || ccset){return;}
		}
	}
	function setDropDownList(elementRef, valueToSetTo){
 		var isFound = false;

 		for (var i=0; i<elementRef.options.length; i++){
  			if ( elementRef.options[i].value == valueToSetTo ){
   				elementRef.options[i].selected = true;
   				isFound = true;
  			}
 		}

 		if ( isFound == false ){
  			elementRef.options[0].selected = true;
  		}
  		return isFound;
	}
	function validateForm(){
		//first clean up destination number
		var dest = document.getElementById('destinationnumber').value;
		if(dest.length == 0){
			document.getElementById('destinationnumber').value='+';
			dest = document.getElementById('destinationnumber').value;
		}
		if(dest.charCodeAt(0) != 43){
			document.getElementById('destinationnumber').value='+'+dest;
			dest = document.getElementById('destinationnumber').value;
		}
		var clean_dest = '+';
		for(var i=1;i<=dest.length;i++){
			if(dest.charCodeAt(i)<48 || dest.charCodeAt(i)>57){
				//dont'take this character.
			}else{
				clean_dest += dest.charAt(i);
			}
		}
		document.getElementById('destinationnumber').value = clean_dest;
		if(document.getElementById('destinationnumber').value=='+' || document.getElementById('destinationnumber').value==''){
			alert('Please enter destination number');
			document.getElementById('destinationnumber').focus();
			//The following three lines set cursor at end of existing input value
			var tmpstr = $('#destinationnumber').val();//Put val in a temp variable
            $('#destinationnumber').val('');//Blank out the input
            $('#destinationnumber').val(tmpstr);//Refill using temp variable value
			return false;
		}
		var destination = document.getElementById('destinationnumber').value;
		destination = destination.substr(1);
		if(checkDestinationValidity(destination)==false){
			return false;
		}
		return true;
	}
	function checkDestinationValidity(destination){
	
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.open('GET','validate_destination.php?dest='+destination+'&t='+Math.random(),false);
		xmlhttp.send();
		if(xmlhttp.responseText.indexOf('valid')>0){
			return true;
		}else{
			document.getElementById('invalidDestination').innerHTML=xmlhttp.responseText;
			return false;
		}
	}
	</script>
	";
	$page_html.='</div>';
	return $page_html;
	
}

function add_number_1_Page_AU(){

    global $this_page;
	
    $did_countries_obj = new Did_Countries();
    if(is_object($did_countries_obj)==false){
    	echo "Error: Failed to create Did_Countries object.";
    }
	$did_countries = $did_countries_obj->getAvailableCountries();
    
    $country = new Country();
	$countries = $country->getAllCountry();
    
	$page_vars = $this_page->variable_stack;
	
	$page_html = "";
	
	$page_html .="
	
	<script>
	
			$(document).ready(function(){
	
				$('#did_country').change(function() {
                    var country_iso = $(this).val();
                    
                    $('#city option').remove();
                    if (country_iso == '') {
                        $('#city').append('<option value=\"\">Please select a city</option');
                        return true;                        
                    }
                    
                    $('#city').append('<option value=\"\">Loading...</option');
                    $('#city').attr('disabled', 'disabled');
                    
                    var url = '../ajax/ajax_load_did_cities.php';
                	
                    $.get(url, {'country_iso' : country_iso}, function(response){
                        var data = jQuery.parseJSON(response);  
                        $('#city').removeAttr('disabled');
                        $('#city option').remove();
                        $('#city').append('<option value=\"\">Please select a city</option'); 
                        $(data).each(function(index, item) {
                            $('#city').append('<option value=\"'+$.trim(item['city_id'])+'\">'+item['city_name']+' '+item['city_prefix']+' (".CURRENCY_SYMBOL."'+item['sticky_monthly_plan1']+'/month)</option>')           
                        })
                        
                    })    
                    
                }) //on did_country change
			
				$('#country').change(function() {
                	$('#number').focus();
                    $('#number').val( '+' + $(this).val())
                }) //on country change
                
         		$('#number').keyup(function(event) {
                        var val = $(this).val();
                        if (val.length == 0) {
                            $(this).val('+')
                            return true;
                        }
                        
                        if (val[0] != '+') {
                            val = '+' + val;
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
                        
                        var option_value = tmp.replace('+','')
                        
                        if ($('#country option[value=\'' + option_value + '\']').length > 0) {
                            $('#country').val( option_value )
                        }  
                        
                })
                
                $('#btn-continue-step1').click(function(event) {
                    if(validate_step1()==true){
                    	$('#fwd_cntry').val($('#country').val());
                    	var au_area_code = $('#city').val();
                    	var country_iso = $('#did_country').val();
                    	$('#au_city_code').val(au_area_code);
                    	$('#country_iso').val(country_iso);
                    	if($('#city').val()=='99999'){//070 Free Number in AU site
                    		$('#forwarding_number').val($('#number').val());
                    		$('#frm-free-num-070').submit();
                    	}else{
                    		$('#frm-step1').submit();
                    	}
                    }
                })
	
			});//End of $(document).ready(function(){
			
			function validate_step1(){
				if($('#city').val() == ''){
					alert('Please select a city in the country selected by you.');
					$('#city').focus();
					return false;
				}
				if($('#country').val() == ''){
					alert('Please select call forwarding country');
					$('#country').focus();
					return false;
				}
				//first clean up destination number
				var dest = document.getElementById('number').value;
				if(dest.length == 0){
					document.getElementById('number').value='+';
					dest = document.getElementById('number').value;
				}
				if(dest.charCodeAt(0) != 43){
					document.getElementById('number').value='+'+dest;
					dest = document.getElementById('number').value;
				}
				var clean_dest = '+';
				for(var i=1;i<=dest.length;i++){
					if(dest.charCodeAt(i)<48 || dest.charCodeAt(i)>57){
						//dont'take this character.
					}else{
						clean_dest += dest.charAt(i);
					}
				}
				document.getElementById('number').value = clean_dest;
				if($('#number').val().length <= 10){
					alert('Please enter call forwarding number with area code (min 10 digits)');
					$('#number').focus();
					//The following three lines set cursor at end of existing input value
					var tmpstr = $('#number').val();//Put val in a temp variable
                    $('#number').val('');//Blank out the input
                    $('#number').val(tmpstr);//Refill using temp variable value
					return false;
				}
				return true;
			}
	
	</script>
	
	";
	
	$page_html .='
		<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Add New Number</h1></div>
                <div class="row">                
                <div class="col-lg-12">
            		<div class="contentbox">
                    	<div class="contentbox-head">
                        	<div class="content-breadbox">
                            	<ul class="breadcrumb2">
                                	<li ><a class="breadcrumb-first breadcrumb-page" href="#">Virtual Number</a></li>
                                    <li><a href="#" class="disable-breadcrumb">Choose Plan</a></li>
                                    <li><a href="#" class="disable-breadcrumb">Payment</a></li>
                                </ul>
                            </div>
                            <div class="row Mtop20">
                            	<div class="subsection">
                            		<div class="row">
                                    	<div class="col-lg-6 col-md-6">
                                    		<div class="choosenumber-left">
                                                        
                                                        <!-- First Box-->
                                                            <div class="chooseno-box">
                                                                <div class="chooseno-box-head">
                                                                     <div class="chooseno-box-title">Virtual Number Country/City</div>
                                                                </div>
                                                                <div class="section-pointer"><div class="pointer"></div></div>
                                                                <div class="choosebox-row">
                                                                     <select class="selectbox" name="did_country" id="did_country">
                                                                          <option value="">Please select a country</option>';
                                                                          foreach($did_countries as $did_country) {
                                                                          		$page_html.='<option value="'.$did_country["country_iso"].'">'.$did_country["country_name"].' '.$did_country["country_prefix"].'</option>';
                                           								  }
                                                   $page_html.='</select>
                                                                </div>
                                                                <div class="choosebox-row">
                                                                     <select class="selectbox" name="city" id="city">
                                                                          <option value="">Please select a city</option>
                                                        			</select>
                                                                </div>
                                                                
                                                                <div class="choosebox-row">
                                                                     &nbsp;
                                                                </div>                                                          
                                                                
                                                                
                                                            </div><!-- First Box ends-->  
                                                            <div class="clearfix"></div>
                                                            
                                                            <div class="txt-c Mtop20">Select one of our available Country/City/Area code numbers using the drop down menu above.</div>
                                                                                                                  
                                                   </div>
                                               </div>';   
                                               if(isset($_POST["add_lead_did"]) && trim($_POST["add_lead_did"])=="yes"){
                                                      $page_html .= '<form method="post" action="add-number-1.php" name="frm-step1" id="frm-step1">
                                                                	 <input type="hidden" value="'.trim($_POST['fwd_cntry']).'" name="fwd_cntry" id="fwd_cntry">
                                                                	 <input type="hidden" value="9999999999" name="number" id="number" />
                                                                     <input type="hidden" value="" name="au_city_code" id="au_city_code" />
                                                                     <input type="hidden" value="" name="country_iso" id="country_iso" />
                                                                     <input type="hidden" value="yes" name="add_lead_did" id="add_lead_did">
                                                                     </form>
                                                                     <form method="post" action="new_free_number_070_AU.php" name="frm-free-num-070" id="frm-free-num-070">
                                                                	 <input type="hidden" value="" name="forwarding_number" id="forwarding_number" />
                                                                	 <input type="hidden" value="yes" name="add_lead_did" id="add_lead_did">
                                                                     </form>';
                                               }else{     
                                $page_html .= '<div class="col-lg-6 col-md-6">
                                                   <div class="choosenumber-right">
                                                        
                                                        <!-- First Box-->
                                                            <div class="chooseno-box">
                                                                <div class="chooseno-box-head">
                                                                     <div class="chooseno-box-title">Enter Your Forwarding Number</div>
                                                                </div>
                                                                <div class="section-pointer"><div class="pointer"></div></div>
                                                                
                                                                 <div class="choosebox-row">
                                                                     <select class="selectbox" name="country" id="country">
                                                                          <option value="">Select Country</option>';
                                           									foreach($countries as $country) { 
                                           										$page_html .= "<option value='".$country['country_code']."'>".$country['printable_name'] . " +".$country['country_code']."</option>";
                                           									}
                                						$page_html .= '</select>
                                                                </div>
                                                                
                                                                <div class="choosebox-row">
                                                                	 <form method="post" action="add-number-1.php" name="frm-step1" id="frm-step1">
                                                                	 <input type="hidden" value="'.trim($_POST['fwd_cntry']).'" name="fwd_cntry" id="fwd_cntry">
                                                                	 <input type="text" class="textinput" placeholder="+" name="number" id="number" />
                                                                     <input type="hidden" value="" name="au_city_code" id="au_city_code" />
                                                                     <input type="hidden" value="" name="country_iso" id="country_iso" />
                                                                     </form>
                                                                     <form method="post" action="new_free_number_070_AU.php" name="frm-free-num-070" id="frm-free-num-070">
                                                                	 <input type="hidden" value="" name="forwarding_number" id="forwarding_number" />
                                                                     </form>
                                                                </div>                                                          
                                                                
                                                                
                                                            </div><!-- First Box ends--> 
                                                            <div class="clearfix"></div>
                                                            <div class="txt-c Mtop20">Enter your forwarding number. Then click "Continue" button to proceed to the choose plan page.</div>                                                       
                                                   </div>
                                               </div>';
                                               }//end of else part of add_lead_did                                                  
                         $page_html .= '</div>
                                    	<div class="centeredImage Mtop20">                                               
                                        	<a id="btn-continue-step1" class="btn blue btn-lg m-icon-big">
									 			Continue <i class="m-icon-big-swapright m-icon-white"></i>
											</a> 
		                                </div>
                                    </div>
                            	</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	';				
	
	/*$page_html.='
	<!-- Main Container -->
                               <div class="contentbox">
                                     <div class="contentbox-head">
                                          <div class="content-breadbox">
                                                <ul class="breadcrumb">
                                                    <li ><a class="breadcrumb-first breadcrumb-page" href="#">Forwarding Number</a></li>
                                                    <li><a href="#" class="disable-breadcrumb">Choose Plan</a></li>
                                                    <li><a href="#" class="disable-breadcrumb">Payment</a></li>
                                                </ul>
                                          </div>
                                         
                                          <div class="row Mtop20">
                                              <div class="subsection">
												  
                                              	  <div class="choosenumber-left">
                                                        
                                                        <!-- First Box-->
                                                            <div class="chooseno-box">
                                                                <div class="chooseno-box-head">
                                                                     <div class="chooseno-box-title">Virtual Number Country/City</div>
                                                                </div>
                                                                <div class="section-pointer"><div class="pointer"></div></div>
                                                                <div class="choosebox-row">
                                                                     <select class="selectbox" name="did_country" id="did_country">
                                                                          <option value="">Please select a country</option>';
                                                                          foreach($did_countries as $did_country) {
                                                                          		$page_html.='<option value="'.$did_country["country_iso"].'">'.$did_country["country_name"].' '.$did_country["country_prefix"].'</option>';
                                           								  }
                                                   $page_html.='</select>
                                                                </div>
                                                                <div class="choosebox-row">
                                                                     <select class="selectbox" name="city" id="city">
                                                                          <option value="">Please select a city</option>
                                                        			</select>
                                                                </div>
                                                                
                                                                <div class="choosebox-row">
                                                                     &nbsp;
                                                                </div>                                                          
                                                                
                                                                
                                                            </div><!-- First Box ends-->  
                                                            
                                                            
                                                            <div class="row Mtop20 middler">Select one of our available Australian City/Area code numbers using the drop down menu above.</div>
                                                                                                                  
                                                   </div>
                                              		
                                                   <div class="choosenumber-right">
                                                        
                                                        <!-- First Box-->
                                                            <div class="chooseno-box">
                                                                <div class="chooseno-box-head">
                                                                     <div class="chooseno-box-title">Enter Your Forwarding Number</div>
                                                                </div>
                                                                <div class="section-pointer"><div class="pointer"></div></div>
                                                                
                                                                 <div class="choosebox-row">
                                                                     <select class="selectbox" name="country" id="country">
                                                                          <option value="">Select Country</option>';
                                           									foreach($countries as $country) { 
                                           										$page_html .= "<option value='".$country['country_code']."'>".$country['printable_name'] . " +".$country['country_code']."</option>";
                                           									}
                                						$page_html .= '</select>
                                                                </div>
                                                                
                                                                <div class="choosebox-row">
                                                                	 <form method="post" action="add-number-1.php" name="frm-step1" id="frm-step1">
                                                                	 <input type="text" class="textinput" placeholder="+" name="number" id="number" />
                                                                     <input type="hidden" value="" name="au_city_code" id="au_city_code" />
                                                                     <input type="hidden" value="" name="country_iso" id="country_iso" />
                                                                     </form>
                                                                     <form method="post" action="new_free_number_070_AU.php" name="frm-free-num-070" id="frm-free-num-070">
                                                                	 <input type="hidden" value="" name="forwarding_number" id="forwarding_number" />
                                                                     </form>
                                                                </div>                                                          
                                                                
                                                                
                                                            </div><!-- First Box ends--> 
                                                            
                                                            <div class="row Mtop20 middler">Enter your forwarding number. Then click "Continue" button to proceed to the choose plan page.</div>                                                       
                                                   </div>
                                                   
                                                   
                                                   <div class="row middler Mtop50"><a class="greenmedbtn" id="btn-continue-step1">Continue</a></div>
                                                   
                                              </div>
                                          </div>                                          
                                           
                                 <!-- Personal Details closed -->         
                                          
                                          
                                     </div>
								</div>
                                <!-- Main Container closed -->
	';
	*/
	//$page_html.='</div>';//End of mid-section DIV
	return $page_html;
	
}//end of function add_number_1_Page_AU()

function add_number_1_Page_AU_choose_plan(){
	
	global $this_page;
    
	$page_vars = $this_page->variable_stack;
	
	$renew_period = 1;//Hard coded as 1 month until the Annual DID Payment option comes into affect.
	
	$unassigned_087did = new Unassigned_087DID();
	if(is_object($unassigned_087did)!=true){
		die("Error: Failed to create Unassigned_087DID object.");
	}
	$sco = new System_Config_Options();
	if(is_object($sco)!=true){
		die("Error: Failed to create System_Config_Options object.");
	}
	$did_cities = new Did_Cities();
  	if(is_object($did_cities)!=true){
		echo "Error: Failed to create DID_Cities object.";
		exit;
	}
	
	$gbp_to_aud_rate = $sco->getRateGBPToAUD();
	$usd_to_aud_rate = $sco->getRateUSDToAUD();
	if(SITE_CURRENCY=="AUD"){
		$exchange_rate = $gbp_to_aud_rate;
		$exchange_rate_tollfree = $usd_to_aud_rate;
	}else{
		$exchange_rate = 1;
		$exchange_rate_tollfree = 1;
	}
	
	$dest_costs = new DCMDestinationCosts();
    if(is_object($dest_costs)==false){
    	echo "Error: Failed to create DCMDestinationCosts object.";
    	exit;
    }
    
    $fwd_cntry = trim($_POST['fwd_cntry']);
	$country_iso = $_POST['country_iso'];
	$city_id = $_POST['au_city_code'];
	
	//$callCost = $unassigned_087did->callCost($_POST["number"],"peak",true)*$exchange_rate;
	$callCost = $dest_costs->get_cheapest_call_rate($fwd_cntry,true,SITE_CURRENCY);
	
	//If the number selected is a Toll-free number need to factor in the incoming call cost
	$callCostTF = $unassigned_087did->callCostTollFree($city_id,true)*$exchange_rate_tollfree;
	if(is_numeric($callCostTF)==false){
		echo "Error: Failed to get Toll free costs. ".$callCostTF;
		exit;
	}
	
 	//Check for country iso
    if(isset($_POST["country_iso"])==false || trim($_POST["country_iso"])==""){
    	echo "Error: DID Country ISO code not passed";
    	exit;
    }
     //Check for city prefix
    if(isset($_POST["au_city_code"])==false || trim($_POST["au_city_code"])==""){
    	echo "Error: DID City Prefix not passed";
    	exit;
    }
    
	$sticky_did_monthly_setup = $did_cities->getStickyDIDMonthlyAndSetupFromCityId($city_id);
	if(is_array($sticky_did_monthly_setup)==false && substr($sticky_did_monthly_setup,0,6)=="Error:"){
		echo $sticky_did_monthly_setup;
		exit;
	}
	$sticky_did_monthly = $sticky_did_monthly_setup["sticky_monthly"];
	$pay_amount_plan1 = number_format($sticky_did_monthly_setup["sticky_monthly_plan1"],2);
	$pay_amount_plan2 = number_format($sticky_did_monthly_setup["sticky_monthly_plan2"],2);
	$pay_amount_plan3 = number_format($sticky_did_monthly_setup["sticky_monthly_plan3"],2);
	$sticky_setup = $sticky_did_monthly_setup["sticky_setup"];
	
	// GET the yearly plan rates
	
	$plan0_yearly = number_format($sticky_did_monthly_setup["sticky_yearly"]/12,2);
	$plan1_yearly = number_format($sticky_did_monthly_setup["sticky_yearly_plan1"]/12,2);
	$plan2_yearly = number_format($sticky_did_monthly_setup["sticky_yearly_plan2"]/12,2);
	$plan3_yearly = number_format($sticky_did_monthly_setup["sticky_yearly_plan3"]/12,2);
	
	// added on 23rd Nov 2015
	
	if(trim($sticky_did_monthly)==""){
		echo "Error: No sticky did_monthly found in did_cities for country_iso: $country_iso and City ID $city_id.";
		exit;
	}
	
	//Plan 1 details
	$plan1_monthly = number_format($pay_amount_plan1,2);
	$plan1_whole_dollars = substr($plan1_monthly,0,strlen($plan1_monthly)-3);
	$plan1_cents = substr($plan1_monthly,-2,2);
	//Plan 2 details
	$plan2_monthly = number_format($pay_amount_plan2,2);
	$plan2_whole_dollars = substr($plan2_monthly,0,strlen($plan2_monthly)-3);
	$plan2_cents = substr($plan2_monthly,-2,2);
	//Plan 3 details
	$plan3_monthly = number_format($pay_amount_plan3,2);
	$plan3_whole_dollars = substr($plan3_monthly,0,strlen($plan3_monthly)-3);
	$plan3_cents = substr($plan3_monthly,-2,2);
	
	//Amount available for SIP Cost after paying for the DID Monthly fees and Set Up costs if any
	if((isset($_POST["did_id"]) && trim($_POST["did_id"])!="") || (isset($_POST["activate_did_id"]) && trim($_POST["activate_did_id"])!="")){
		$effective_amount_plan1 = $pay_amount_plan1 - ($sticky_did_monthly*$renew_period);
		$effective_amount_plan2 = $pay_amount_plan2 - ($sticky_did_monthly*$renew_period);
		$effective_amount_plan3 = $pay_amount_plan3 - ($sticky_did_monthly*$renew_period);
	}else{
		$effective_amount_plan1 = $pay_amount_plan1 - ($sticky_did_monthly*$renew_period) - $sticky_setup;
		$effective_amount_plan2 = $pay_amount_plan2 - ($sticky_did_monthly*$renew_period) - $sticky_setup;
		$effective_amount_plan3 = $pay_amount_plan3 - ($sticky_did_monthly*$renew_period) - $sticky_setup;
	}
		
	if($callCostTF!=0){
		$num_of_minutes_plan1 = floor($effective_amount_plan1/($callCost+$callCostTF));
		$num_of_minutes_plan2 = floor($effective_amount_plan2/($callCost+$callCostTF));
		$num_of_minutes_plan3 = floor($effective_amount_plan3/($callCost+$callCostTF));
	}else{
		$num_of_minutes_plan1 = $dest_costs->get_cheapest_call_minutes($fwd_cntry, $effective_amount_plan1);
		$num_of_minutes_plan2 = $dest_costs->get_cheapest_call_minutes($fwd_cntry, $effective_amount_plan2);
		$num_of_minutes_plan3 = $dest_costs->get_cheapest_call_minutes($fwd_cntry, $effective_amount_plan3);
	}
	
	$page_html = "";
	
	$page_html .="
	
	<script>
	
			$(document).ready(function(){
				
				var choosen_plan = 0;
                var annual_pay = 0;
                $('#btn-choose-plan1').click(function(event) {
					var plan_period = $('input[name=\"rad_plan1_period\"]:checked').val();
					console.log(plan_period);
					if(plan_period){
					
						choosen_plan = 1;
						$('#chosen_plan').val('1');
						$('#plan_period').val(plan_period);
						$('#frm-step1').submit();
					}else{
						clearAllCheckbox();
					}
                })
                $('#btn-choose-plan2').click(function(event) {
                	var plan_period = $('input[name=\"rad_plan2_period\"]:checked').val();
					console.log(plan_period);
					if(plan_period){
					
						choosen_plan = 2;
						$('#chosen_plan').val('2');
						$('#plan_period').val(plan_period);
						$('#frm-step1').submit();
					}else{
						clearAllCheckbox();
					}
                })
                $('#btn-choose-plan3').click(function(event) {
                	var plan_period = $('input[name=\"rad_plan3_period\"]:checked').val();
					console.log(plan_period);
					if(plan_period){
					
						choosen_plan = 3;
						$('#chosen_plan').val('3');
						$('#plan_period').val(plan_period);
						$('#frm-step1').submit();
					}else{
						clearAllCheckbox();
					}
                })
			
			});//End of $(document).ready(function()
			
			function clearAllCheckbox(){
				alert('Select a Plan Period');
				$('input[type=\"radio\"]').each(function(){
					$(this).removeAttr('checked');
				});
			}
	</script>
	
	";
	
	$page_html .= '
		<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Add New Number</h1></div>
                <div class="row">                
                	<div class="col-lg-12">
                		<div class="contentbox">
                                     <div class="contentbox-head">
                                          <div class="content-breadbox">
                                                <ul class="breadcrumb2">';
												if((isset($_POST["did_id"]) && trim($_POST["did_id"])!="") || 
													(isset($_POST["activate_did_id"]) && trim($_POST["activate_did_id"])!="")){
													$page_html.='<li><a class="breadcrumb-first  breadcrumb-page">Choose Plan</a></li>';
												}else{
                                                    $page_html.='<li ><a class="breadcrumb-first" href="add-number-1.php">Virtual Number</a></li>
                                                    <li><a href="#" class=" breadcrumb-page">Choose Plan</a></li>';
												}
                                       $page_html.='<li><a href="#" class="disable-breadcrumb">Payment</a></li>
                                                </ul>
                                          </div>
                                    <div class="Mtop20">
										<div class="row Mtop20">
											<div class="col-sm-3 Mtop20">
												<div class="panel panel-default text-center panelbox">
										            <div class="panel-heading">
										              <!--h3 class="panel-title price text-success">'.CURRENCY_SYMBOL.$plan1_whole_dollars.'<span class="price-cents">'.$plan1_cents.'</span><span class="price-month">Monthly</span></h3-->
													  <div class="plans-sked">
														<input title="Plan Monthly" name="rad_plan1_period" id="rad_plan1_monthly" type="radio" value="M" class="pull-left radio-btn"><span class="plan_span_css">'. CURRENCY_SYMBOL. $plan1_monthly . '</span>(Monthly)<br/> <input title="Annual Plan" name="rad_plan1_period" id="rad_plan1_annual" type="radio" value="Y" class="pull-left radio-btn"><span class="plan_span_css">' . CURRENCY_SYMBOL. $plan1_yearly . '</span>(Yearly)
													  </div>
										            </div>
										            <ul class="list-group">
										              <li class="list-group-item">'.$num_of_minutes_plan1.' Minutes*</li>
										              <li class="list-group-item">Call Diversion</li>
										              <li class="list-group-item">Online Call Statistics</li>
										              <li class="list-group-item">Call Queues</li>';
										              	if(isset($_POST["current_plan"]) && trim($_POST["current_plan"])!=""){
                                                      		if(intval(trim($_POST["current_plan"]))==1){
                                                            	$plan1_btn_val = "Extend";
                                                            }elseif(intval(trim($_POST["current_plan"]))>1){
                                                            	$plan1_btn_val = "Downgrade";
                                                            }
                                                        }else{
                                                        	$plan1_btn_val = "Choose";
                                                        }
										              $page_html.='<li class="list-group-item"><a id="btn-choose-plan1" class="btn btn-success">'.$plan1_btn_val.'</a></li>
										            </ul>
										          </div>          
										        </div>
										        
										        <div class="col-sm-3 Mtop20">
													<div class="panel panel-default text-center panelbox">
											            <div class="panel-heading">
											              <!--h3 class="panel-title price text-success">'.CURRENCY_SYMBOL.$plan2_whole_dollars.'<span class="price-cents">'.$plan2_cents.'</span><span class="price-month">Monthly</span></h3-->
														  <div class="plans-sked">
															<input title="Plan Monthly" name="rad_plan2_period" id="rad_plan2_monthly" type="radio" value="M" class="pull-left radio-btn"><span class="plan_span_css">'. CURRENCY_SYMBOL. $plan2_monthly . '</span>(Monthly)<br/> <input title="Annual Plan" name="rad_plan2_period" id="rad_plan2_annual" type="radio" value="Y" class="pull-left radio-btn"><span class="plan_span_css">' . CURRENCY_SYMBOL. $plan2_yearly . '</span>(Yearly)
														  </div>
											            </div>
											            <ul class="list-group">
											              <li class="list-group-item">'.$num_of_minutes_plan2.' Minutes*</li>
											              <li class="list-group-item">Call Diversion</li>
											              <li class="list-group-item">Online Call Statistics</li>
											              <li class="list-group-item">Call Queues</li>';
											              	if(isset($_POST["current_plan"]) && trim($_POST["current_plan"])!=""){
	                                                      		if(intval(trim($_POST["current_plan"]))==2){
	                                                            	$plan2_btn_val = "Extend";
	                                                            }elseif(intval(trim($_POST["current_plan"]))>2){
	                                                            	$plan2_btn_val = "Downgrade";
	                                                            }elseif(intval(trim($_POST["current_plan"]))<2){
	                                                            	$plan2_btn_val = "Upgrade";
	                                                            }
	                                                        }else{
	                                                        	$plan2_btn_val = "Choose";
	                                                        }
											              $page_html.='<li class="list-group-item"><a id="btn-choose-plan2" class="btn btn-success">'.$plan2_btn_val.'</a></li>
											            </ul>
											          </div>          
											        </div>
											        
											        <div class="col-sm-3 Mtop20">
														<div class="panel panel-default text-center panelbox">
												            <div class="panel-heading">
												              <!--h3 class="panel-title price text-success">'.CURRENCY_SYMBOL.$plan3_whole_dollars.'<span class="price-cents">'.$plan3_cents.'</span><span class="price-month">Monthly</span></h3-->
															  <div class="plans-sked">
																<input title="Plan Monthly" name="rad_plan3_period" id="rad_plan3_monthly" type="radio" value="M" class="pull-left radio-btn"><span class="plan_span_css">'. CURRENCY_SYMBOL. $plan3_monthly . '</span>(Monthly)<br/> <input title="Annual Plan" name="rad_plan3_period" id="rad_plan3_annual" type="radio" value="Y" class="pull-left radio-btn"><span class="plan_span_css">' . CURRENCY_SYMBOL. $plan3_yearly . '</span>(Yearly)
															  </div>
												            </div>
												            <ul class="list-group">
												              <li class="list-group-item">'.$num_of_minutes_plan3.' Minutes*</li>
												              <li class="list-group-item">Call Diversion</li>
												              <li class="list-group-item">Online Call Statistics</li>
												              <li class="list-group-item">Call Queues</li>';
												              	if(isset($_POST["current_plan"]) && trim($_POST["current_plan"])!=""){
		                                                      		if(intval(trim($_POST["current_plan"]))==3){
		                                                            	$plan3_btn_val = "Extend";
		                                                            }elseif(intval(trim($_POST["current_plan"]))<=2){
		                                                            	$plan3_btn_val = "Upgrade";
		                                                            }
		                                                        }else{
		                                                        	$plan3_btn_val = "Choose";
		                                                        }
												              	$page_html.='<li class="list-group-item"><a id="btn-choose-plan3" class="btn btn-success">'.$plan3_btn_val.'</a></li>
												            </ul>
												          </div>          
												        </div>
													</div>';
												if(isset($_POST["current_plan"]) && trim($_POST["current_plan"])!=""){
											        $page_html.='<!--<div class="planbox-c">
														<div class="chooseno-box">
															<div class="chooseno-box-head">
						                                    	<div class="chooseno-box-title">Enter Your Forwarding Number</div>
						                                    </div>
						                                    <div class="section-pointer"><div class="pointer"></div></div>
						                                    <div class="choosebox-row">
						                                    	<span class="memberradio"><input type="radio" name="iCheck" id="radNumberForward" checked /></span>
						                                    	<div class="hint">Enter the Number to forward to:</div><div class="hint-img">
						                                    	<div class="form_hint">Use this option to forward your Online Number to an existing mobile or landline number. For example +6141234567 or +61212345678.</div>
						                                    </div>
						                                    <div class="clearfloat"></div>
						                                </div>
						                                <div class="choosebox-row">
                                                        	<select class="selectbox" name="country" id="country">
                                                                <option value="">Select Country</option>     
                                                            </select>
                                                        </div>
														<div class="choosebox-row">
                                                                	 <form method="post" action="add-number-1.php" name="frm-step1" id="frm-step1">
                                                                	 <input type="text" class="textinput" placeholder="+" name="number" id="number">
                                                                     <input type="hidden" value="" name="au_city_code" id="au_city_code">
                                                                     <input type="hidden" value="" name="country_iso" id="country_iso">
                                                                     </form>
                                                                     <form method="post" action="new_free_number_070_AU.php" name="frm-free-num-070" id="frm-free-num-070">
                                                                	 <input type="hidden" value="" name="forwarding_number" id="forwarding_number">
                                                                     </form>
                                                        </div>
                                                        <div class="choosebox-row">
                                                        	<span class="memberradio"><input type="radio" name="iCheck" id="radSipForward" /></span>
                                                        	<div class="hint">Enter SIP Address:</div>
                                                        	<div class="hint-img"><div class="form_hint">SIP is for advanced users that wish to forward there number to a network and not a physical phone. SIP stands for Session Initiation Protocol</div></div>
                                                        	<input type="text" value="" id="sipAddress" name="sipAddress" placeholder="you@sip_provider.com" class="textinput" disabled >
															<div class="clearfloat"></div>
             											</div>
             											
													</div>-->';
													}
												$page_html.='</div>
										        
										        
											</div>
											
										</div>
									</div>
									
									
                	</div>
                </div>
            </div>
        </div>    
        
        <form method="post" action="add-number-1.php" name="frm-step1" id="frm-step1">
                                                    <input type="hidden" value="'.$_POST["country_iso"].'" name="country_iso" id="country_iso">
                                                    <input type="hidden" value="'.$_POST["au_city_code"].'" name="au_city_code" id="au_city_code">
                                                   	<input type="hidden" value="'.$_POST["number"].'" name="fwd_number" id="fwd_number">
                                                   	<input type="hidden" value="" name="chosen_plan" id="chosen_plan">
													<input type="hidden" value="" name="plan_period" id="plan_period">';
                                                   if(isset($_POST["did_id"]) && trim($_POST["did_id"])!=""){
                                                   	$page_html.='<input type="hidden" value="'.$_POST["did_id"].'" name="did_id" id="did_id">
                                                   				 <input type="hidden" value="'.$_POST["current_plan"].'" name="current_plan" id="current_plan">
                                                   				 <input type="hidden" value="'.$_POST["did_full_number"].'" name="did_full_number" id="did_full_number">';
                                                   }
                                                   if(isset($_POST["activate_did_id"]) && trim($_POST["activate_did_id"])!=""){
                                                   	$page_html.='<input type="hidden" value="'.$_POST["activate_did_id"].'" name="activate_did_id" id="activate_did_id">
                                                   				 <input type="hidden" value="'.$_POST["activate_current_plan"].'" name="activate_current_plan" id="activate_current_plan">
                                                   				 <input type="hidden" value="'.$_POST["did_full_number"].'" name="did_full_number" id="did_full_number">';
                                                   }
                                                   if(isset($_POST["add_lead_did"]) && trim($_POST["add_lead_did"])=="yes"){
                                                   	$page_html.='<input type="hidden" value="yes" name="add_lead_did" id="add_lead_did">';
                                                   }
                                                   $page_html.='</form>
	';

	return $page_html;
	
}//end of function add_number_1_Page_AU_choose_plan() 

function add_number_1_Page_AU_choose_pymt(){
	
	global $this_page;
	
	if(isset($_SESSION["user_logins_id"])==false || trim($_SESSION["user_logins_id"])==""){
		echo "Your login session has expired.<br />";
		echo "Please login back again.";
		exit;
	}
    
	if(isset($_POST['country_iso'])==false || trim($_POST['country_iso'])==""){
		echo "Error: country_iso code is not passed.";
		exit;
	}
	if(isset($_POST['au_city_code'])==false || trim($_POST['au_city_code'])==""){
		echo "Error: city prefix is not passed.";
		exit;
	}
	$country_iso = $_POST['country_iso'];
	$city_id = $_POST['au_city_code'];
	
	$country = new Country();
	if(is_object($country)!=true){
		die("Failed to create Country object.");
	}
	$countries = $country->getAllCountry();
	
	$page_vars = $this_page->variable_stack;
	
	$chosen_plan = intval($_POST["chosen_plan"]);

	$renew_period = 1;//Hard coded as 1 month until the Annual DID Payment option comes into affect.
	
	// added on 7th dec 2015
	
	if(isset($_POST['plan_period']) && $_POST['plan_period'] == 'Y'){
		$renew_period = 12;
	}
	//echo $renew_period.'<br/>';
	// ended
	
	$unassigned_087did = new Unassigned_087DID();
	if(is_object($unassigned_087did)!=true){
		die("Error: Failed to create Unassigned_087DID object.");
	}
	$sco = new System_Config_Options();
	if(is_object($sco)!=true){
		die("Error: Failed to create System_Config_Options object.");
	}
	$did_cities_obj = new Did_Cities();
    if(is_object($did_cities_obj)==false){
    	echo "Error: Failed to create Did_Cities object.";
    }
    $gbp_to_aud_rate = $sco->getRateGBPToAUD();
	if(SITE_CURRENCY=="AUD"){
		$exchange_rate = $gbp_to_aud_rate;
	}else{
		$exchange_rate = 1;
	}
	$callCost = $unassigned_087did->callCost($_POST["fwd_number"],"peak",true)*$exchange_rate;
	$did_country_city_names = $did_cities_obj->getCountryAndCityNameFromCityId($city_id);
	$sticky_did_monthly_setup = $did_cities_obj->getStickyDIDMonthlyAndSetupFromCityId($city_id);
	if(is_array($sticky_did_monthly_setup)==false && substr($sticky_did_monthly_setup,0,6)=="Error:"){
		echo $sticky_did_monthly_setup;
		exit;
	}
	$sticky_did_monthly = $sticky_did_monthly_setup["sticky_monthly"];
	//echo '<pre>';print_r($sticky_did_monthly_setup);echo '</pre>';
	switch(intval($chosen_plan)){
		case 1:
			$pay_amount = ($renew_period == 12) ? number_format($sticky_did_monthly_setup["sticky_yearly_plan1"],2) : number_format($sticky_did_monthly_setup["sticky_monthly_plan1"],2);
			
			break;
		case 2:
			$pay_amount = ($renew_period == 12) ? number_format($sticky_did_monthly_setup["sticky_yearly_plan2"],2) : number_format($sticky_did_monthly_setup["sticky_monthly_plan2"],2);
			break;
		case 3:
			$pay_amount = ($renew_period == 12) ? number_format($sticky_did_monthly_setup["sticky_yearly_plan3"],2) : number_format($sticky_did_monthly_setup["sticky_monthly_plan3"],2);
			break;
	}
	$sticky_setup = $sticky_did_monthly_setup["sticky_setup"];
	
	if(trim($sticky_did_monthly)==""){
		echo "Error: No sticky did_monthly found in did_cities for country_iso: $country_iso and City ID: $city_id.";
		exit;
	}
	//Amount available for SIP Cost after paying for the DID Monthly fees and Set Up costs if any
	if(isset($_POST["did_id"]) && trim($_POST["did_id"])!=""){//Upgrade the plan of an existing DID
		$effective_amount = $pay_amount - ($sticky_did_monthly*$renew_period);
	}elseif(isset($_POST["activate_did_id"]) && trim($_POST["activate_did_id"])!=""){
		//Renew an Expired DID if it is within the aging period of DIDWW
		$effective_amount = $pay_amount - ($sticky_did_monthly*$renew_period);
	}else{//New DID
		$effective_amount = $pay_amount - ($sticky_did_monthly*$renew_period) - $sticky_setup;
	}
	$num_of_minutes = floor($effective_amount/$callCost);
	
	//Get Account Credit
	$member_credits = new Member_credits();
	  if(is_object($member_credits)==false){
	  	die("Failed to create Member_credits object.");
	  }
	  $blnSufficientCredit = false;
	  $acct_credit_radio = "&nbsp;";
	  $account_credit = $member_credits->getTotalCredits($_SESSION['user_logins_id']);
	  if(intval($account_credit*100) >= intval($pay_amount*100)){
	  	$blnSufficientCredit = true;
	  	$acct_credit_radio = '<input type="radio" name="creditcard" id="radAcctCredit" />&nbsp;Account Credit ('.CURRENCY_SYMBOL.$account_credit.')';
	  }
	
	  //Get Existing Credit Cards
	  $cc_details = new CC_Details();
	  if(is_object($cc_details)==false){
	  	die("Failed to create CC_Details object.");
	  }
	  
  	$cc_details_array = $cc_details->getCCLastFourOfUser(trim($_SESSION['user_logins_id']));

    if($cc_details_array!=false){
    	$cc_data = json_encode($cc_details_array);
    }else{
    	//die("Error: Failed to CC Details.");
    }
	
	$_SESSION['pay_amount'] = $pay_amount;
    $cc_count = 0;//$cc_details->get_cc_count(trim($_SESSION['user_logins_id']));
    
    $ip_country = get_ip_location($_SERVER["REMOTE_ADDR"]);
    
 	$user = new User();
	if(is_object($user)==false){
		die("Failed to create User object.");
	}
	
	$user_address = $user->getUserAddress($_SESSION["user_logins_id"]);
	if(is_array($user_address)==false){
		if(substr($user_address,0,6)=="Error:"){
			echo $user_address;
			exit;
		}elseif($user_address==0){
			echo "No rows found for user_id : ".$_SESSION["user_logins_id"];
			exit;
		}
	}
	
	$user_country = trim($user_address["user_country"]);
    
    //Get Number of Card Declined attempts
	$card_declined_attempts = 0;//$user->getCardDeclinedAttempts($_SESSION["user_logins_id"]);
	
	//$blnShowWP = false;
	$blnShowCC = false;
	if(strstr($_SERVER["HTTP_HOST"], 'dev.') || strstr($_SERVER["HTTP_HOST"], 'localhost') || strstr($_SESSION["user_login"], "steve.s") ){
		//$blnShowCC = true;
	
	}
	if(SITE_COUNTRY=="AU" && ($ip_country=='AU' || $ip_country=="NZ") && ($user_country=='AU' || $user_country=="NZ") && $card_declined_attempts < 3){
		$blnShowCC = true;
	}elseif(SITE_COUNTRY=="UK" && $ip_country=='GB' && $user_country=='GB' && $card_declined_attempts < 3){
		$blnShowCC = true;
	}
	
	//Override show WP
	//Get Show WP (override) setting
	$show_wp = $user_address["show_wp"] ; //$user->get_show_wp($_SESSION["user_logins_id"]);
	if($show_wp=="Y"){
		$blnShowCC = true;
	}
	
	if(SITE_COUNTRY=="UK"){
		$blnShowCC = false; // added temporary
	}
	//If renewing an existing number - must set the WP Future Pay start date to be in sync with the current DID expiry date at DIDWW
	$wp_fp_start_date = date('Y-m-d',strtotime(date("Y-m-d", mktime())." + 28 days"));
	if((isset($_POST["did_id"]) && trim($_POST["did_id"])!="") || (isset($_POST["activate_did_id"]) && trim($_POST["activate_did_id"])!="")){
		/*$assigned_did = new Assigned_DID();
		if(is_object($assigned_did)==false){
			echo "Error: Failed to create Assigned_DID object.";
			exit;
		}
		if(isset($_POST["did_id"]) && trim($_POST["did_id"])!=""){
			$did_id = trim($_POST["did_id"]);
		}elseif(isset($_POST["activate_did_id"]) && trim($_POST["activate_did_id"])!=""){
			$did_id = trim($_POST["activate_did_id"]);
		}
		$did_expires_on = $assigned_did->getDIDExpiryFromID($did_id);
		$wp_fp_start_date = date('Y-m-d', strtotime($did_expires_on. ' + 28 days'));
		if(time() >= date_timestamp_get(date_create($wp_fp_start_date))){//Start Date cannot be today or in the past
			$wp_fp_start_date = date('Y-m-d',strtotime(date("Y-m-d", mktime())." + 1 days"));
		}*/
	}
	  
	$page_html = "";
	
	$page_html .="
	
	<script>	
			var purchase_type;
			var did_id;
			var did_number;
			var pymtType = '';	
			var site_country = '".SITE_COUNTRY."';
			var site_url = '".SITE_URL."';
			var ip_country = '".$ip_country."';
			var site_currency = '". SITE_CURRENCY ."';
			var card_declined_attempts = ".$card_declined_attempts.";\n";
			if($blnShowCC==true){ 
				$page_html .="var blnShowCC = true;\n"; 
			} else { 
				$page_html .= "var blnShowCC = false;\n"; 
			}
			if(isset($_POST["add_lead_did"]) && trim($_POST["add_lead_did"])=="yes"){
				$page_html .="var add_lead_did = 'yes';\n";
			}else{
				$page_html .="var add_lead_did = 'no';\n";
			}
	
			$page_html .="$(document).ready(function(){
			
				
                pymtType = 'PayPal';
                
			
				purchase_type = $('#purchase_type').val();
				if(purchase_type == 'new_did'){
					did_id = '';
					did_number = '';
				}else if(purchase_type == 'renew_did'){
					did_id = $('#renew_did_id').val();
					did_number =  $('#did_full_number').val();
				}else if(purchase_type == 'restore_did'){
					did_id =  $('#activate_did_id').val();
					did_number =  $('#did_full_number').val();
				}
			
				$('#radPayPal').click(function(event){
					pymtType = 'PayPal';
					
					return;
				});
				
				$('#radAcctCredit').click(function(event){
					pymtType = 'AcctCredit';
					
					return;
				});
				
				$('#radCredit_Card').click(function(event){
                	pymtType = 'Credit_Card';
                	return;
                });
				
				
				$('#btn-make-payment').click(function(event) {
					if($('#terms-chk-box').is(':checked') == false){
						event.preventDefault();
						alert('You must agree to the terms and conditions to proceed');
						$('#terms-chk-box').focus();
						return false;
					}
						
					if(pymtType == 'AcctCredit'){
						
						if(confirm('".html_entity_decode(CURRENCY_SYMBOL)."$pay_amount will be deducted from your account credit. Please confirm.')==true){
							//Hide the payment inputs area
							$('#div_payment_inputs').hide();
							$('#div_btn_make_payment').hide();
							$('#div_processing_image').show();
							var cartId = get_cartId('AccountCredit');
							var url = '../ajax/ajax_charge_acct_credit.php';
							var choosen_plan = $('#sel_plan').val();
							var fwd_number = $('#fwd_number').val();
	                    	var country_iso = $('#country_iso').val();
							var city_id = $('#au_city_code').val();
							var plan_period = $('#plan_period').val();
							var num_of_months = $renew_period;//1 -- Hard coded until annual payment or multiple months is allowed on front end
							jQuery.ajaxSetup({async:false});
							$.post(url,{'cart_id': cartId, 
										'choosen_plan' : choosen_plan, 
										'fwd_number' : fwd_number, 
										'country_iso' : country_iso, 
										'city_id' : city_id, 
										'plan_period' : plan_period,
										'num_of_months' : num_of_months,
										'purchase_type' : purchase_type,
										'did_id' : did_id,
										'did_number' : did_number
										},
										function(response){
											if(response.substr(0,7)=='Success'){
												location.href = 'edit_routes.php?cartId='+cartId+'&add_lead_did='+add_lead_did;
												return;
											}else{
												alert(response);
												$('#div_processing_image').hide();
												return;
											}
							});
						}else{
							event.preventDefault();
						}
						return;
					 }else if(pymtType == 'PayPal'){
					 	
						//Add the record to shopping_cart.
						var cartId = get_cartId('PayPal');
						//Proceed to PayPal page
						var url='../ajax/ajax_go_to_paypal.php';
						var postdata = {'cartId' : cartId};
						jQuery.ajaxSetup({async:false});
						$.post(url, postdata, function(response){
							if(response.substr(0,6)!='Error:'){
								//Now take them to the PayPal site
								location.href= response;
								return;
							}else{
								alert(response);
								return;
							}
						});//End of $.post
					 	return;
					 }else if(pymtType == 'Credit_Card'){
					 	
						var cartId = get_cartId('Credit_Card');
						var plan_period = $('#plan_period').val();
						//Proceed to PayPal page
						var url='../ajax/ajax_simplepay_generate_token.php';
						var postdata = {currency: site_currency, amount:$pay_amount,did_plan_period:plan_period};
						jQuery.ajaxSetup({async:false});
						$.post(url, postdata, function(response){
							
							response = $.parseJSON(response);
							console.log(response);
							if(response.id){
								var pay_url = site_url+'dashboard/simple_payment.php?id='+response.id+'&cart_id='+cartId;
								//console.log(pay_url);
								location.href = pay_url;
								//window.open(pay_url);
							}else{
								
								return false;
							}
							
						});//End of $.post
					 	return;
					 }
					
                })
			});//End of $(document).ready(function()
			
			
			function go_choose_plan(){
				$('#frm-choose-plan').submit();
			}
			
						
			function get_cartId(pymt_gateway){
				var return_to = '';
				var description = '';
				jQuery.ajaxSetup({async:false});
				var url = '../ajax/ajax_add_to_cart.php';
				if(purchase_type=='new_did'){
					return_to = 'MemberArea';
					description = 'Virtual Number: ".$_POST["did_full_number"]." ".$did_country_city_names["country_name"]." ".$did_country_city_names["city_name"]."(".$did_country_city_names["country_prefix"]."-".$did_country_city_names["city_prefix"].")';
				}else if(purchase_type=='renew_did'){
					return_to = 'NumberRoutes';
					description = 'Renew Virtual Number : '+did_number;
				}else if(purchase_type=='restore_did'){
					return_to = 'NumberRoutes';
					description = 'Restore Virtual Number : '+did_number;
				}
				if(pymt_gateway == 'AccountCredit'){
					if(purchase_type=='renew_did'){
						return_to = 'NumberRoutes';
						description = 'Renew Virtual Number : '+did_number;
					}else if(purchase_type=='restore_did'){
						return_to = 'NumberRoutes';
						description = 'Restore Virtual Number : '+did_number;
					}
				}
				postdata = {'amount' : ".$pay_amount.", 
							'did_plan' : $('#sel_plan').val(), 
							'description' : description,
							'plan_period' : $('#plan_period').val(),
							'num_of_months' : 1,
							'country_iso' : $('#country_iso').val(),
							'city_prefix' : $('#au_city_code').val(),
							'fwd_number' : $('#fwd_number').val(),
							'return_to' : return_to,
							'purchase_type' : purchase_type,
							'did_id' : did_id,
							'did_number' : did_number,
							'pymt_gateway': pymt_gateway
							}
				var cartId = '';
				$.post(url, postdata, function(response){
					if(response.substr(0,6)!='Error:'){
						cartId = response;
					}else{
						alert(response);
						return;
					}
				});
				
				return cartId;
			}
			
	</script>
	
	";
	
	$page_html .= '
		<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Add New Number</h1></div>
                <div class="row">                
                	<div class="col-lg-12 Mtop20">
						<div class="contentbox">
                        	<div class="contentbox-head">
                            	<div class="content-breadbox">
                                	<ul class="breadcrumb2">
                                    	<li><a class="disable-breadcrumb" href="#">Virtual Number</a></li>
                                        <li><a href="#" class="disable-breadcrumb">Choose Plan</a></li>
                                        <li><a href="#" class="breadcrumb-page">Payment</a></li>
                                        <li><a href="#" class="disable-breadcrumb">Finish</a></li>
                                    </ul>
                                </div>
                                
                                <div class="row">
								 	<div class="col-lg-12 Mtop20"> 
								    	<div class="panel panel-default">
								        	<div class="panel-body">
								            	<div class="col-md-6">
								            		<h4>Payment Options:</h4>';
								            		$page_html .= $acct_credit_radio;
								            		$page_html .= '<div class="clearfix Mtop10"></div>
            										<input type="radio" checked="checked" name="creditcard" id="radPayPal" /><span> <img src="../images/paypal.png"></span>';
            										$page_html .= '<div class="clearfix Mtop10"></div>';
								            		if($blnShowCC==true){
	            										$page_html .= '<input type="radio" name="creditcard" id="radCredit_Card" />
	            										<span><img src="../images/visa_curved.png"/></span>
	                                                    <span><img src="../images/mastercard_curved.png"/></span>
	                                                    
	                                                    <span><img src="../images/american_express_curved.png"/></span>';
								            		}
													
                                                    $page_html .= '<div class="clearfix Mtop10"></div>
                                                    <input type="checkbox" id="terms-chk-box"><a href="../terms/" target="_blank"> I agree to the Terms and Conditions</a> <span class="txt-red">*</span>
								            	</div>
								            	
								           	</div>
                                		</div>
                                		<a  id="btn-make-payment" class="btn blue btn-lg m-icon-big fltrt">
									 		Make Payment <i class="m-icon-big-swapright m-icon-white"></i>
										</a>
                                	</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	';

    $page_html .='<input type="hidden" value="'.$_POST["country_iso"].'" name="country_iso" id="country_iso">
                                                         <input type="hidden" value="'.$_POST["au_city_code"].'" name="au_city_code" id="au_city_code">
                                                         <input type="hidden" value="'.$_POST["chosen_plan"].'" name="sel_plan" id="sel_plan">';
														 if($renew_period == 12){
															 $plan_period_val = "Y";
														 }else{
															 $plan_period_val = "M";
														 }
											$page_html .='<input type="hidden" value="'. $plan_period_val.'" name="plan_period" id="plan_period">
                                                         <input type="hidden" value="'.$_POST["fwd_number"].'" name="fwd_number" id="fwd_number">
                                                         <input type="hidden" value="" name="pymt_type" id="pymt_type">
                                                         <input type="hidden" value="" name="existing_cc_id" id="existing_cc_id">';
														if(isset($_POST["did_id"]) && trim($_POST["did_id"])!=""){
                                                         	$page_html .= '<input type="hidden" value="'.$_POST["did_id"].'" name="did_id" id="renew_did_id">
                                                         					<input type="hidden" value="renew_did" name="purchase_type" id="purchase_type">
                                                         					<input type="hidden" value="'.$_POST["did_full_number"].'" name="did_full_number"  id="did_full_number">';
                                                         }elseif(isset($_POST["activate_did_id"]) && trim($_POST["activate_did_id"])!=""){
                                                   			$page_html.='<input type="hidden" value="'.$_POST["activate_did_id"].'" name="activate_did_id" id="activate_did_id">
                                                   							<input type="hidden" value="restore_did" name="purchase_type" id="purchase_type">
                                                   				 			<input type="hidden" value="'.$_POST["did_full_number"].'" name="did_full_number" id="did_full_number">';
                                                   		}else{
                                                   			$page_html.='<input type="hidden" value="new_did" name="purchase_type" id="purchase_type">';
                                                   		}
                                                                
    $page_html .= '
    	<!-- form to be posted to GO BACK to the choose plan tab -->
        <form method="post" action="add-number-1.php" name="frm-choose-plan" id="frm-choose-plan">
	    <input type="hidden" value="'.$_POST["country_iso"].'" name="country_iso" id="country_iso">
        <input type="hidden" value="'.$_POST["au_city_code"].'" name="au_city_code" id="au_city_code">
	    <input type="hidden" value="'.$_POST["fwd_number"].'" name="number" id="number">';
        if(isset($_POST["did_id"]) && trim($_POST["did_id"])!=""){
        	$page_html .= '<input type="hidden" value="'.$_POST["did_id"].'" name="did_id" id="did_id">
            <input type="hidden" value="'.$_POST["did_full_number"].'" name="did_full_number">
            <input type="hidden" value="'.$_POST["current_plan"].'" name="current_plan" id="current_plan">';
        }
		if(isset($_POST["activate_did_id"]) && trim($_POST["activate_did_id"])!=""){
        	$page_html.='<input type="hidden" value="'.$_POST["activate_did_id"].'" name="activate_did_id" id="activate_did_id">
            <input type="hidden" value="'.$_POST["activate_current_plan"].'" name="activate_current_plan" id="activate_current_plan">
            <input type="hidden" value="'.$_POST["did_full_number"].'" name="did_full_number" id="did_full_number">';
        }
	    $page_html .= '</form>';
	
	return $page_html;
	
}//end of function add_number_1_Page_AU_choose_pymt() 

function getNewLimitPage(){
	$page_html = '<div class="title-bg"><h1>Limit Reached</h1></div><div class="mid-section">
	               <div class="date-bar">
					<div class="left-info"></div>
										You have Reached Your DID Allowance.To Increase Limit Contact us on:support@stickynumber.com
										</div>
					<!-- Date section end here -->';
	$page_html .= '</div>';

	return $page_html;
}

?>