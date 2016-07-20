<?php
function getVoiceMailsPage(){
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
        
        $get_CID    =   $_GET['id'];
        if(!empty($get_CID)){
            $get_cidURL =   "id=".$get_CID."&";
        }
        
       /* $page_html = '<div class="title-bg"><h1>Voice Mail</h1></div><div class="mid-section" >';
	
	$page_html.=' <div class="date-bar">
					<div class="left-info"><b></b></div>
										</div>
					<!-- Date section end here -->				
					
					
				';
	if(isset($page_vars['action_message']) && $page_vars['action_message'] != ""){
		$page_html .= '<div class="tip_box">';
		$page_html .= $page_vars['action_message'];
		$page_html .= '</div>';
	}
	
	$page_html .= '<table width="100%" border="0" cellspacing="0" cellpadding="7" class="my-call-recording">';
	
	if(count($page_vars['my_voice_mail'])>0){
		$page_html .= '<caption>*click "Enabled" or "Disabled" to change the status of voice mail</caption>';
	}else{
		$page_html .= '<caption>You have no voice mails received for your Virtual Numbers</caption>';
	}
	
	$page_html .= '<tr class="title">
				<td width="17%" align="center" class="first td_order_DESC">Virtual Number</td>
				<td width="16%" align="center" class="td_order_ASC">Active</td>
				<td width="24%" align="center" class="td_order_DESC">Rings Before Activation</td>
				<td width="14%" align="center" class="td_order_DESC">Not Heard</td>
				<td width="13%" align="center" class="td_order_DESC">Total</td>
				<td width="16%" align="center" class="td_order_DESC">View Records</td>				
			</tr>';
	
	if(count($page_vars['my_voice_mail'])>0){
		foreach($page_vars['my_voice_mail'] as $one_voice){
                   
			$page_html .='<tr >
				<td align="center">'.$one_voice["mailbox"].'</td>	
				<td align="center"><form action="voice_mail.php" name="records" method="post">
				<input type="hidden" name="paginate" value="'.$page_vars['page_current'].'">
				<input type="hidden" name="mailbox" id="mailbox" value="'.$one_voice["mailbox"].'">';
				if($one_voice["enabled"]=="1"){
					$page_html .='<input type="submit" name="btnAction" value="Enabled" alt="Enabled" style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" title="click to disable" class="logged-button" id="Enabled">';
				}else{
					$page_html .='<input type="submit" name="btnAction" value="Disabled" alt="Disabled" style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" title="click to enable" class="logged-button" id="Disabled">';
				}
				$page_html .='</form></td>
				<td align="center"><form action="voice_mail.php" name="rings_before_vm'.$one_voice["mailbox"].'" method="post">
				<input type="hidden" name="paginate" value="'.$page_vars['page_current'].'">
				<input type="hidden" name="mailbox" id="mailbox" value="'.$one_voice["mailbox"].'">
				<select name="selRings" onchange="javascript:document.rings_before_vm'.$one_voice["mailbox"].'.submit();">';
				for($i=1;$i<=10;$i++){
					$page_html .='<option value="'.$i.'"';
					if($i==$one_voice["rings_before_vm"]){
						$page_html .=' selected';
					}
					$page_html .='>'.$i.'</option>';
				}
				$page_html .='</select></form></td>
				<td align="center"><font color="red">'.$one_voice["unread"].'</font></td>
				<td align="center">'.$one_voice["total_mail"].'</td>
				<td align="center"><form action="view_records.php" name="records" method="post">
				<input type="hidden" name="mailbox" id="mailbox" value="'.$one_voice["mailbox"].'">
				<input type="submit" name="viewrecords" value="View Records" alt="" style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" title="" class="logged-button" id="viewrecords">
				</form>
				</td>				 
			</tr>';
		}//end of foreach
	}//end of if count
	
	$page_html .= '</table>';
	
	if($page_vars['page_count'] > 1){	
		$page_html .= '<form name="frmPaginate" id="frmPaginate" action="voice_mail.php" method="post">';
		$page_html .= '<p align="right">Showing '.$page_vars['page_current_range_start'].'-'.$page_vars['page_current_range_end'].' of '.$page_vars['my_total_vm_users_count']."\n"; 
		$page_html .= '<select name="paginate" onchange="javascript:document.frmPaginate.submit();">'."\n";
		$page_deck = $page_vars['page_deck'];
		$i=0;
		foreach($page_deck as $page_range){
			$i++;
			$page_html .= '<option value="'.$i.'">'.$page_range.'</option>'."\n";
		}			 
		$page_html .= '</select>
	</p></form>';
	}//end of if page count > 1
    $page_html.='</div> <!-- Mid section start here -->';*/
        
        
    $page_html .= '
    	<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Voice Mail</h1>
                    <h6>1. Enable voice mail on your number.</h6>
                    <h6>2. Call your number and when it says "person at extension &lt;your sticky number&gt; is unavavalible" push "*" before the beep.</h6>
                    <h6>3. Please enter your 4 digit password (password below).</h6>
                    <h6>4. Select option "0" (Mailbox options).</h6>
                    <h6>5. Select option "1" (Record unavailable message).</h6>
                    <h6>6. Record your new greeting message and push "#".</h6>
                    <h6>7. When prompted accept and save your new unavailable greeting message by pushing "1".</h6>
<!--<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><i class="glyphicon glyphicon-wrench"></i> Settings</h3>
  </div>
  <div class="panel-body">
   <div class="row">
   	<div class="col-xs-6">
<form class="form-horizontal Mtop20" role="form">
  <div class="form-group">
  <label for="voiceemail" class="col-sm-4 control-label">Email Voice Mail</label>
  <div class="col-sm-8">
  <div class="radio-inline">
        <label for="radio">
            <input type="radio" value="1"/>
            Yes
        </label>
    </div>    
	<div class="radio-inline">       
        <label for="radio">
            <input type="radio" value="2"/>
            No
         </label>
    </div>   
	</div>  
		<div class="col-sm-offset-4 col-sm-8">
      		<button type="submit" class="btn btn-primary">Save</button>
    	</div>    
  </div>
</form>
    </div>
   </div>
  </div>
</div>    -->

<div class="table-responsive">
                <table class="table">
                <thead class="blue-bg">
                	<tr>
	                	<th>Virtual Number</th>
	                	<th>Password</th>
	                	<th>Enabled?</th>
	                	<th>Email Voice Mail?</th>
	                	<th>Rings Before Voice Mail</th>
	                	<th>Not Heard</th>
	                	<th>Total</th>
	                	<th>View Records</th>
                	</tr>
                </thead>
                <tbody>';
    if(count($page_vars['my_voice_mail'])>0){
    	foreach($page_vars['my_voice_mail'] as $one_voice){
                $page_html.='<tr>
                <td valign="middle">'.$one_voice["mailbox"].'</td>
                 <td align="center" valign="middle">'.$one_voice["password"].'</td>
                 <td align="center" valign="middle">
                 	<form action="voice_mail.php" name="records'.$one_voice["mailbox"].'" method="post">';
                		$page_html.='<input type="hidden" name="mailbox" id="mailbox" value="'.$one_voice["mailbox"].'">';
                 		if($one_voice["enabled"]=="1"){
							$page_html .='<button type="submit" name="btnAction" value="Enabled" alt="Enabled" title="click to disable" class="btn btn-primary" id="Enabled">Enabled</button>';
						}else{
							$page_html .='<button type="submit" name="btnAction" value="Disabled" alt="Disabled" title="click to enable" class="btn btn-primary" id="Disabled">Disabled</button>';
						}
                 	$page_html .='</form>
                 </td>
                 <td align="center" valign="middle">
                 	<form action="voice_mail.php" name="emailvoicemail'.$one_voice["mailbox"].'" method="post">';
                		$page_html.='<input type="hidden" name="mailbox" id="mailbox" value="'.$one_voice["mailbox"].'">';
                 		if($one_voice["attach"]=="yes"){
							$page_html .='<button align="center" type="submit" name="btnEmail" value="Yes" alt="Yes. Email voice mails." title="click to stop emailing of voicemails." class="btn btn-primary" id="EmailYes">Yes</button>';
						}else{
							$page_html .='<button align="center" type="submit" name="btnEmail" value="No" alt="No. Do not email voice mails." title="click to start emailing of voicemails." class="btn btn-primary" id="EmailNo">No</button>';
						}
                 	$page_html .='</form>
                 </td>
                 <td align="center" valign="middle">
                 	<form action="voice_mail.php" name="rings_before_vm'.$one_voice["mailbox"].'" method="post">
						<input type="hidden" name="mailbox" id="mailbox" value="'.$one_voice["mailbox"].'">
						<select name="selRings" onchange="javascript:document.rings_before_vm'.$one_voice["mailbox"].'.submit();">';
						for($i=0;$i<=10;$i++){
							$page_html .='<option value="'.$i.'"';
							if($i==$one_voice["rings_before_vm"]){
								$page_html .=' selected';
							}
							$page_html .='>'.$i.'</option>';
						}
						$page_html .='</select>
					</form>
                 </td>
<td align="center" valign="middle"><strong class="text-danger">'.$one_voice["unread"].'</strong></td>
                <td align="center" valign="middle">'.$one_voice["total_mail"].'</td>
                <td valign="middle">
                	<form action="view_records.php" name="records" method="post">
                	<input type="hidden" name="mailbox" id="mailbox" value="'.$one_voice["mailbox"].'">
                	<button class="btn btn-primary" type="submit">View Records</button>
                	</form></td>
                </tr>';
    	}//end of foreach loop
    }//end of if(count...            
                
                $page_html.='</tbody>
                </table>
                </div>
                </div>
            </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    ';
                
    if($page_vars['page_count'] > 1){	
		$page_html .= '<form name="frmPaginate" id="frmPaginate" action="voice_mail.php" method="post">';
		$page_html .= '<p align="right">Showing '.$page_vars['page_current_range_start'].'-'.$page_vars['page_current_range_end'].' of '.$page_vars['my_total_vm_users_count']."\n"; 
		$page_html .= '<select name="paginate" onchange="javascript:document.frmPaginate.submit();">'."\n";
		$page_deck = $page_vars['page_deck'];
		$i=0;
		foreach($page_deck as $page_range){
			$i++;
			$page_html .= '<option value="'.$i.'">'.$page_range.'</option>'."\n";
		}			 
		$page_html .= '</select>
	</p></form>';
	}//end of if page count > 1
        
	return $page_html;
}
?>