<?php
function getUpdateRoutePage(){
	
	$get_CID    =   $_GET['id'];
     if(!empty($get_CID)){
         $get_cidURL =   "?id=".$get_CID;
     }
	 
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
	
	$did_data = array();
	$did_data = $page_vars['did_data'];
	/*$page_html = '<div class="title-bg"><h1>Update Number</h1></div><div class="mid-section">
	               <div class="date-bar">
					<div class="left-info">Update Virtual Number '.$did_data['did_number'].'</div>
										</div>
					<!-- Date section end here -->';				
	
	if($page_vars['action_message'] != ""){
		$page_html .= '<div class="tip_box">';
		$page_html .= $page_vars['action_message'];
		$page_html .= '</div>';
	}*/
	
	if($did_data['forwarding_type']==2){
		$page_html .= "<script>var forwarding_type = 2;</script>";
	}else{
		$page_html .= "<script>var forwarding_type = 1;</script>";
	}
	
	/*$page_html .= '
	
	<form name="input" action="update_route.php'.$get_cidURL.'" method="POST" >
	<table width="100%" border="0" cellspacing="0" cellpadding="7" class="configure-number">		
		<tbody>		
		<tr>
			<td colspan="2" >
				<input type="hidden" name="configuring_did" value="'.$did_data['did_number'].'" >
				<input type="hidden" name="did_number" value="'.$did_data['did_number'].'" >
			</td>
		</tr>
		<tr>
			<td width="25%" align="right" valign="top">
				
					<input name="destinationnumber" id="destinationnumber" value="'.$did_data['route_number'].'" size="25" type="text" class="destination-number" />
					<span class="madatory">*</span>
				
			</td>
			<td width="75%">Enter destination ';
			if($did_data['forwarding_type']==2){
				$page_html .= "SIP address.";
			}else{
				$page_html .= "number.";
			}
			$page_html .='</td>
		</tr>
		<tr>
		<td colspan="2" align="left">
		<select name="caller_id" id="caller_id" class="select-country" onchange="javascript:setOwnCallerid();">
		<option value="1" '.$page_vars['cid_option1'].'>See Whos Calling Me</option>
		<option value="2" '.$page_vars['cid_option2'].'>Send 44'.substr($did_data['did_number'],1).'</option>
		<option value="3" '.$page_vars['cid_option3'].'>Block Caller ID (Unknown)</option>
		<option value="4" '.$page_vars['cid_option4'].'>Set Your Own Caller ID (enter below)</option>
		</select>
		  Select Caller ID
		</td>
		</tr>
		<tr>
			<td width="100%" align="left" valign="top" colspan="2">
				<div id="divowncallerid" style="visibility:hidden">
					<input name="owncallerid" id="owncallerid" value="'.$page_vars['cid_custom'].'" size="25" type="text" 	class="destination-number" tabindex="4" />
					Enter your own caller id number
				</div>
			</td>
		</tr>
		<!--<tr>
			<td halign="center" class="first"><div align="right">
			<input name="callerid" type="radio" value="callerid_on" '.$page_vars['callerid_on'].' />
			Yes
			<input name="callerid" type="radio" value="callerid_off" '.$page_vars['callerid_off'].' />
			No <span class="text_style1">*</span></div></td>
			<td>&nbsp;</td>
		</tr>-->';
		
		$page_html .= '<tr>
						<td>&nbsp;</td>
						<td><input type="submit" name="Submit" id="continue" value="Continue" alt="Continue" title="Continue" class="continue-btn"  /></td>
					  </tr>
		
		</tbody>
	</table>
	</form>
	
	
	';*/
		
	$page_html.="
	<script type='text/javascript'>
	
		$(document).ready(function(){
		
			$('#DropDownTimezone').val('".$did_data['user_timezone']."');
			console.log(".$did_data['did_number'].");
			$('#destinationnumber').change(function() {
				validateInputs();
			});
			
				$('#destinationnumber').keyup(function(event) {
					if(forwarding_type==2){//This validation is applicable only for PSTN number forwarding
						return;
					}
                        var val = $(this).val();
                        
                        var tmp = val;
                        
                        var last_char = val[ val.length -1 ]
                        var char_code = val.charCodeAt(val.length - 1) 
                        if ((char_code < 48 || char_code > 57)) {
                            tmp = '';
                            for(var i=0; i < val.length - 1; i++) {
                                tmp += val[i]         
                            }
                                 
                        } 
                        
                        $(this).val( tmp );
                        
                });//End of destinationnumber key press event
        
        	$('#continue').click(function(){
        		return validateInputs();
        	});
			
		});//End of document ready function

		if(document.getElementById('caller_id').value=='4'){
			document.getElementById('divowncallerid').style.visibility = 'visible';
		}

		function setOwnCallerid(){
			if(document.getElementById('caller_id').value=='4'){
				document.getElementById('divowncallerid').style.visibility = 'visible';
			}else{
				document.getElementById('divowncallerid').text = '';
				document.getElementById('divowncallerid').style.visibility = 'hidden';
			}
		}
	
		function validateInputs(){
			if(forwarding_type==2){//Only if the forwarding type is SIP address
					if(validateEmail($('#destinationnumber').val())==false){
						alert(\"Please enter a valid destination SIP Address.\");
						return false;
					}
				}else{
					//first clean up destination number
					var dest = $('#destinationnumber').val();
					var clean_dest = '';
					for(var i=0;i<=dest.length;i++){
						if(dest.charCodeAt(i)<48 || dest.charCodeAt(i)>57){
							//dont'take this character.
						}else{
							clean_dest += dest.charAt(i);
						}
					}
					$('#destinationnumber').val(clean_dest);
					if($.trim($('#destinationnumber').val()).length <= 10){
						alert(\"Please enter call forwarding number with country code and area code (min 10 digits)\");
						$('#destinationnumber').focus();
						//The following three lines set cursor at end of existing input value
						var tmpstr = $('#destinationnumber').val();//Put val in a temp variable
	                    $('#destinationnumber').val('');//Blank out the input
	                    $('#destinationnumber').val(tmpstr);//Refill using temp variable value
						return false;
					}
				}
				return true;
		}
		
		function validateEmail(sEmail) {
		    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
		    if (filter.test(sEmail)) {
		        return true;
		    }
		    else {
		        return false;
		    }
		}
	</script>
	";
		
/*$page_html .= '</div>';*/
	
	$page_html .= '
		<form name="input" id="frm_inputs" action="update_route.php'.$get_cidURL.'" method="POST" >
		<div id="page-wrapper">
        	<div class="row">
            	<div class="col-lg-12">
                	<h1 class="page-header">Edit Number Routes</h1>
                    	<div class="row">
                    		<div class="col-xs-3">
                    			<h3>Phone Number</h3>
                    			<div class="alert alert-info"><strong>'.$did_data['did_number'].'</strong></div>
                    		</div>
                    	</div>                                    
                </div>
            </div>
            <div class="row">
				<div class="col-lg-12">    
                    <p>Timezone</p>
                    <div class="col-xs-5">
                    	<select class="form-control" name="DropDownTimezone" id="DropDownTimezone">
					      <option value="-12.0">(GMT -12:00) Eniwetok, Kwajalein</option>
					      <option value="-11.0">(GMT -11:00) Midway Island, Samoa</option>
					      <option value="-10.0">(GMT -10:00) Hawaii</option>
					      <option value="-9.0">(GMT -9:00) Alaska</option>
					      <option value="-8.0">(GMT -8:00) Pacific Time (US &amp; Canada)</option>
					      <option value="-7.0">(GMT -7:00) Mountain Time (US &amp; Canada)</option>
					      <option value="-6.0">(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
					      <option value="-5.0">(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
					      <option value="-4.0">(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
					      <option value="-3.5">(GMT -3:30) Newfoundland</option>
					      <option value="-3.0">(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
					      <option value="-2.0">(GMT -2:00) Mid-Atlantic</option>
					      <option value="-1.0">(GMT -1:00 hour) Azores, Cape Verde Islands</option>
					      <option value="0.0">(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
					      <option value="1.0">(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris</option>
					      <option value="2.0">(GMT +2:00) Kaliningrad, South Africa</option>
					      <option value="3.0">(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
					      <option value="3.5">(GMT +3:30) Tehran</option>
					      <option value="4.0">(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
					      <option value="4.5">(GMT +4:30) Kabul</option>
					      <option value="5.0">(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
					      <option value="5.5">(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
					      <option value="5.75">(GMT +5:45) Kathmandu</option>
					      <option value="6.0">(GMT +6:00) Almaty, Dhaka, Colombo</option>
					      <option value="7.0">(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
					      <option value="8.0">(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
					      <option value="9.0">(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
					      <option value="9.5">(GMT +9:30) Adelaide, Darwin</option>
					      <option value="10.0">(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
					      <option value="11.0">(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
					      <option value="12.0">(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
						</select>
					</div>
					<div class="col-lg-3">
					<p class="Mtop5">01 Mar 2013 | 10:29:17</p>
					</div>
	            </div> 
	        </div>
	        
	        <div class="row">
<div class="col-lg-12 Mtop20">
<p><strong>Select Caller ID</strong></p>
<div class="col-xs-5">
<select name="caller_id" id="caller_id" class="form-control" onchange="javascript:setOwnCallerid();">
		<option value="1" '.$page_vars['cid_option1'].'>See Whos Calling Me</option>
		<option value="2" '.$page_vars['cid_option2'].'>Send '.$did_data['did_number'].'</option>
		<option value="3" '.$page_vars['cid_option3'].'>Block Caller ID (Unknown)</option>
		<option value="4" '.$page_vars['cid_option4'].'>Set Your Own Caller ID (enter below)</option>
		</select>
</div>
<div class="col-xs-3">
<input type="text" class="form-control" name="owncallerid" id="owncallerid">
</div>
</div>
</div>
<div class="row">
<div class="col-lg-12 Mtop20">
<p><strong>Call Identifier Beep</strong></p>
<div class="col-xs-5">
<select class="form-control" name="callerid">
  <option>Set number of beeps</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select>
</div>
</div>
</div>                    
<div class="row">
<div class="col-lg-12 Mtop20">
<div class="table-responsive table-hover">
<table class="table">
<thead>
<tr>
<th width="45%">Schedule</th>
<th width="23%">Forward To</th>
<!--<th width="17%">Voice Mail Activision</th>
<th width="15%">Voice Mail Message</th>-->
</tr>
</thead>
<tbody>
<tr>
<td>
<div class="radio">
  <label>
    <input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked>
     All Times
  </label>
</div></td>
<td><input class="form-control" type="text" name="destinationnumber" id="destinationnumber" value="'.$did_data['route_number'].'"></td>
<!--<td><select class="form-control">
  <option>0 Rings</option>
  <option>1 Rings</option>
  <option>2 Rings</option>
  <option>3 Rings</option>
  <option>4 Rings</option>
  <option>5 Rings</option>
  <option>6 Rings</option>
  <option>7 Rings</option>
</select></td>
<td><select class="form-control">
  <option>Default Message</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>-->
</tr>
<tr>
<td colspan="4"><div class="radio">
  <label>
    <input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">
    Time Schedule
    </label>
</div></td>
</tr>
<tr>
<td>
<table width="100%">
<tbody>
<tr>
<td><select class="form-control">
  <option>Monday</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>
<td>&nbsp;</td>
<td><select class="form-control">
  <option>08:15am</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>
<td>&nbsp;To&nbsp;</td>
<td><select class="form-control">
  <option>08:15am</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>
</tr>
</tbody>
</table>
</td>
<td><input class="form-control" type="text"></td>
<!--<td><select class="form-control">
  <option>0 Rings</option>
  <option>1 Rings</option>
  <option>2 Rings</option>
  <option>3 Rings</option>
  <option>4 Rings</option>
  <option>5 Rings</option>
  <option>6 Rings</option>
  <option>7 Rings</option>
</select></td>
<td><select class="form-control">
  <option>Default Message</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>-->
</tr>
<tr>
<td>
<table width="100%">
<tbody>
<tr>
<td><select class="form-control">
  <option>Tuesday</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>
<td>&nbsp;</td>
<td><select class="form-control">
  <option>08:15am</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>
<td>&nbsp;To&nbsp;</td>
<td><select class="form-control">
  <option>08:15am</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>
</tr>
</tbody>
</table>
</td>
<td><input class="form-control" type="text"></td>
<!--<td><select class="form-control">
  <option>0 Rings</option>
  <option>1 Rings</option>
  <option>2 Rings</option>
  <option>3 Rings</option>
  <option>4 Rings</option>
  <option>5 Rings</option>
  <option>6 Rings</option>
  <option>7 Rings</option>
</select></td>
<td><select class="form-control">
  <option>Default Message</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>-->
</tr>
<tr>
<td>
<table width="100%">
<tbody>
<tr>
<td><select class="form-control">
  <option>Wednesday</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>
<td>&nbsp;</td>
<td><select class="form-control">
  <option>08:15am</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>
<td>&nbsp;To&nbsp;</td>
<td><select class="form-control">
  <option>08:15am</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>
</tr>
</tbody>
</table>
</td>
<td><input class="form-control" type="text"></td>
<!--<td><select class="form-control">
  <option>0 Rings</option>
  <option>1 Rings</option>
  <option>2 Rings</option>
  <option>3 Rings</option>
  <option>4 Rings</option>
  <option>5 Rings</option>
  <option>6 Rings</option>
  <option>7 Rings</option>
</select></td>
<td><select class="form-control">
  <option>Default Message</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>-->
</tr>
<tr>
<td>
<table width="100%">
<tbody>
<tr>
<td><select class="form-control">
  <option>Thursday</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>
<td>&nbsp;</td>
<td><select class="form-control">
  <option>08:15am</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>
<td>&nbsp;To&nbsp;</td>
<td><select class="form-control">
  <option>08:15am</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>
</tr>
</tbody>
</table>
</td>
<td><input class="form-control" type="text"></td>
<!--<td><select class="form-control">
  <option>0 Rings</option>
  <option>1 Rings</option>
  <option>2 Rings</option>
  <option>3 Rings</option>
  <option>4 Rings</option>
  <option>5 Rings</option>
  <option>6 Rings</option>
  <option>7 Rings</option>
</select></td>
<td><select class="form-control">
  <option>Default Message</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>-->
</tr>
<tr>
<td>
<table width="100%">
<tbody>
<tr>
<td><select class="form-control">
  <option>Friday</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>
<td>&nbsp;</td>
<td><select class="form-control">
  <option>08:15am</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>
<td>&nbsp;To&nbsp;</td>
<td><select class="form-control">
  <option>08:15am</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>
</tr>
</tbody>
</table>
</td>
<td><input class="form-control" type="text"></td>
<!--<td><select class="form-control">
  <option>0 Rings</option>
  <option>1 Rings</option>
  <option>2 Rings</option>
  <option>3 Rings</option>
  <option>4 Rings</option>
  <option>5 Rings</option>
  <option>6 Rings</option>
  <option>7 Rings</option>
</select></td>
<td><select class="form-control">
  <option>Default Message</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>-->
</tr>
<tr>
<td>
<table width="100%">
<tbody>
<tr>
<td><select class="form-control">
  <option>Saturday</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>
<td>&nbsp;</td>
<td><select class="form-control">
  <option>08:15am</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>
<td>&nbsp;To&nbsp;</td>
<td><select class="form-control">
  <option>08:15am</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>
</tr>
</tbody>
</table>
</td>
<td><input class="form-control" type="text"></td>
<!--<td><select class="form-control">
  <option>0 Rings</option>
  <option>1 Rings</option>
  <option>2 Rings</option>
  <option>3 Rings</option>
  <option>4 Rings</option>
  <option>5 Rings</option>
  <option>6 Rings</option>
  <option>7 Rings</option>
</select></td>
<td><select class="form-control">
  <option>Default Message</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>-->
</tr>
<tr>
<td>
<table width="100%">
<tbody>
<tr>
<td><select class="form-control">
  <option>Sunday</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>
<td>&nbsp;</td>
<td><select class="form-control">
  <option>08:15am</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>
<td>&nbsp;To&nbsp;</td>
<td><select class="form-control">
  <option>08:15am</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>
</tr>
</tbody>
</table>
</td>
<td><input class="form-control" type="text"></td>
<!--<td><select class="form-control">
  <option>0 Rings</option>
  <option>1 Rings</option>
  <option>2 Rings</option>
  <option>3 Rings</option>
  <option>4 Rings</option>
  <option>5 Rings</option>
  <option>6 Rings</option>
  <option>7 Rings</option>
</select></td>
<td><select class="form-control">
  <option>Default Message</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
</select></td>-->
</tr>
</tbody>
</table>
<a onclick="javascript:$(\'#frm_inputs\').submit();" class="btn blue btn-lg m-icon-big fltrt">
							Update Details <i class="m-icon-big-swapright m-icon-white"></i>
						</a>
</div>
</div>
</div>                                
            </div>
	        
        </div>
	';

	$page_html .= '
			<input type="hidden" name="configuring_did" value="'.$did_data['did_number'].'" >
			<input type="hidden" name="did_number" value="'.$did_data['did_number'].'" >
		</form>
	';
	
	return $page_html;
	
}