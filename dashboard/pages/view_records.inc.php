<?php
function getViewRecordsPage(){
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
        
        $get_CID    =   $_GET['id'];
        if(!empty($get_CID)){
            $get_cidURL =   "id=".$get_CID."&";
        }
        
        $mail_box=$page_vars['mailbox'];
        
        $page_html.='<script type="text/javascript">
                                        function setAsHeard(unid,heard){
                                                if(heard == 1 || heard == "1"){
                                                        return;//already set as heard nothing to do
                                                }
                                                //alert("Thanks for listening!");
                                                var xhrObject = new XMLHttpRequest();
                                                if (xhrObject) {
                                                        xhrObject.open("POST", "'.SERVER_WEB_NAME.'/dashboard/file.php", true);
                                                        xhrObject.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                                                        xhrObject.send("vm_id="+unid);  
                                                }
                                                
                                        }
                                        
                                        function readupdate(){
											document.readform.submit();
										}
                                        
                </script>
        ';
        
       /* $page_html = '<div class="title-bg"><h1>Voice Mail</h1></div><div class="mid-section" >';
	
	$page_html.=" <div class='date-bar'>
					<div class='left-info'><b>$mail_box </b>&nbsp; *Messages  are automatically deleted after 7 days unless you hit save</div>
										</div>
					<!-- Date section end here -->								
					
				";
	
	
	if(isset($page_vars['action_message']) && $page_vars['action_message'] != ""){
		$page_html .= '<div class="tip_box">';
		$page_html .= $page_vars['action_message'];
		$page_html .= '</div>';
	}
	
	$page_html .= '<table width="100%" border="0" cellspacing="0" cellpadding="2" class="my-call-recording">';
	
	if(count($page_vars['my_voice_mail'])>0){
		$page_html .= "<caption> </caption>";
	}else{
		$page_html .= "<caption>$mail_box  You have no Messages received </caption>";
	}
	
	$page_html .= '<tr class="title">
				<td width="10%" align="center" class="first">Call Date</td>
				<td width="10%" align="center">New / Heard?</td>
				<td width="10%" align="center">Call Duration</td>
				<td width="70%" align="center">Action</td>

			</tr>';
	
	if(count($page_vars['my_voice_mail'])>0){
		foreach($page_vars['my_voice_mail'] as $one_voice){
                   $unid=$one_voice["uniqueid"];
				   $min=gmdate("H:i:s", $one_voice["duration"]);
				   $mins=explode(':',$min);
				   $sec='';
				   if($mins[0]==0)
				   {
				     $sec.='';
				   }
				   else
				   {
				    $sec.=$mins[0].'h&nbsp;';
				   
				   }
				   if($mins[1]==0)
				   {
				     $sec.='0m&nbsp;';
				   }
				   else
				   {
				    $sec.=$mins[1].'m&nbsp;';
				   
				   }
				    if($mins[2]==0)
				   {
				     $sec.='0s&nbsp;';
				   }
				   else
				   {
				    $sec.=$mins[2].'s&nbsp;';
				   
				   }
				   $t=date("d M Y H:i:s",$one_voice["origtime"]);
				   if(intval($one_voice["read"])==1){
				   		$read = "<font color=\"green\">Heard</font>";
				   }else{
				   		$read = "<font color=\"red\">New</font>";
				   }
			$page_html .='<tr >
				<td align="center">'.$t.'</td>	
				<td align="center">'.$read.'</td>
				<td align="center">'.$sec.'</td>
				<td align="left">
				<form action="view_records.php" name="readform" method="post">
				<!-- <img src="images/player.png" alt="play" onclick="readupdate()">
				<embed  type="application/x-shockwave-flash" flashvars="audioUrl='.SERVER_WEB_NAME.'/dashboard/file.php?fileid='.$unid.'" src="http://www.google.com/reader/ui/3523697345-audio-player.swf"  width="320" height="27" quality="best"></embed> -->
				<audio controls preload="metadata" onended="javascript:setAsHeard('.$unid.','.intval($one_voice["read"]).')">
                <source src="'.SERVER_WEB_NAME.'/dashboard/file.php?fileid='.$unid.'&type=mp3" type="audio/mpeg" />
                <source src="'.SERVER_WEB_NAME.'/dashboard/file.php?fileid='.$unid.'&type=ogg" type="audio/ogg" />
                <source src="'.SERVER_WEB_NAME.'/dashboard/file.php?fileid='.$unid.'&type=wav" type="audio/wav" />
                Your browser does not support this audio format.
                </audio>
				<input type="hidden" name="unid" value="'.$unid.'">
				<input type="hidden" name="mailbox" value="'.$mail_box.'">
				<input type="hidden" name="paginate" value="'.$page_vars['page_current'].'">
				<input type="submit" name="Delete" value="Delete" alt="" onclick="javascript:return window.confirm(\'Are you sure you want to delete this message?\')" style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" title="click to delete" class="logged-button" id="Delete">&nbsp;&nbsp;';
				if($one_voice["savemsg"]=="0"){
					$page_html .='<input type="submit" name="Save" value="Save" alt="" style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" title="click to save" class="logged-button" id="Save">';
				}else{
					$page_html .='<input type="submit" name="Unsave" value="Unsave" alt="" style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" title="click to unsave" class="logged-button" id="Unsave">';
				}
				$page_html .='</form>
				</td>
				
			</tr>';
		}//end of foreach
	}//end of if count
	
	$page_html .= '</table>';
	
	if($page_vars['page_count'] > 1){	
		$page_html .= '<form name="frmPaginate" id="frmPaginate" action="view_records.php" method="post">';
		$page_html .= '<p align="right">Showing '.$page_vars['page_current_range_start'].'-'.$page_vars['page_current_range_end'].' of '.$page_vars['my_voice_mails_count']."\n"; 
		$page_html .= '<input type="hidden" name="mailbox" value="'.$mail_box.'">';
		$page_html .= '<select name="paginate"  onchange="javascript:document.frmPaginate.submit();">'."\n";
		$page_deck = $page_vars['page_deck'];
		$i=0;
		foreach($page_deck as $page_range){
			$i++;
			$page_html .= '<option value="'.$i.'">'.$page_range.'</option>'."\n";
		}			 
		$page_html .= '</select>
	</p></form>';
	}//end of if page count > 1
    $page_html.='</div> <!-- Mid section start here -->';
	*/
        
	$page_html .= '
    	<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Voice Mail</h1>
                    <div class="date-bar">
						<div class="left-info"><b>'.$mail_box.' </b> : *Messages  are automatically deleted after 7 days unless you hit save</div>
					</div>';
					
					if(isset($page_vars['action_message']) && $page_vars['action_message'] != ""){
						$page_html .= '<div class="tip_box">';
						$page_html .= $page_vars['action_message'];
						$page_html .= '</div>';
					}
	
					if(count($page_vars['my_voice_mail'])<=0){
						$page_html .= "<caption><b>$mail_box</b> : <font color='red'>You have no voice mail messages in this mailbox.</font> </caption>";
					}else{
						$page_html .= '<div class="table-responsive">
						<table class="table table-hover">';
	
						$page_html .= '<thead class="blue-bg"><tr class="title">
									<th>Call Date</th>
									<th>New / Heard?</th>
									<th>Call Duration</th>
									<th>Play Message</th>
									<th>Action</th>
								</tr></thead>';
		
						if(count($page_vars['my_voice_mail'])>0){
							foreach($page_vars['my_voice_mail'] as $one_voice){
					                   $unid=$one_voice["uniqueid"];
									   $min=gmdate("H:i:s", $one_voice["duration"]);
									   $mins=explode(':',$min);
									   $sec='';
									   if($mins[0]==0)
									   {
									     $sec.='';
									   }
									   else
									   {
									    $sec.=$mins[0].'h&nbsp;';
									   
									   }
									   if($mins[1]==0)
									   {
									     $sec.='0m&nbsp;';
									   }
									   else
									   {
									    $sec.=$mins[1].'m&nbsp;';
									   
									   }
									    if($mins[2]==0)
									   {
									     $sec.='0s&nbsp;';
									   }
									   else
									   {
									    $sec.=$mins[2].'s&nbsp;';
									   
									   }
									   $t=date("d M Y H:i:s",$one_voice["origtime"]);
									   if(intval($one_voice["read"])==1){
									   		$read = "<font color=\"green\">Heard</font>";
									   }else{
									   		$read = "<font color=\"red\">New</font>";
									   }
								$page_html .='<tr >
									<td align="center">'.$t.'</td>	
									<td align="center">'.$read.'</td>
									<td align="center">'.$sec.'</td>
									<td align="left" valign="middle">
									<form action="view_records.php" name="readform" method="post">
									<!-- <img src="images/player.png" alt="play" onclick="readupdate()">
									<embed  type="application/x-shockwave-flash" flashvars="audioUrl='.SERVER_WEB_NAME.'/dashboard/file.php?fileid='.$unid.'" src="http://www.google.com/reader/ui/3523697345-audio-player.swf"  width="320" height="27" quality="best"></embed> -->
									<audio controls preload="metadata" onended="javascript:setAsHeard('.$unid.','.intval($one_voice["read"]).')">
					                <source src="'.SERVER_WEB_NAME.'/dashboard/file.php?fileid='.$unid.'&type=mp3" type="audio/mpeg" />
					                <source src="'.SERVER_WEB_NAME.'/dashboard/file.php?fileid='.$unid.'&type=ogg" type="audio/ogg" />
					                <source src="'.SERVER_WEB_NAME.'/dashboard/file.php?fileid='.$unid.'&type=wav" type="audio/wav" />
					                Your browser does not support this audio format.
					                </audio>
					                </td>
					                <td align="center"  valign="middle">
									<input type="hidden" name="unid" value="'.$unid.'">
									<input type="hidden" name="mailbox" value="'.$mail_box.'">
									<input type="hidden" name="paginate" value="'.$page_vars['page_current'].'">
									<button type="submit" name="Delete" value="Delete" alt="" onclick="javascript:return window.confirm(\'Are you sure you want to delete this message?\')" title="click to delete" class="btn btn-primary addpads" id="Delete">Delete</button>&nbsp;&nbsp;';
									if($one_voice["savemsg"]=="0"){
										$page_html .='<button type="submit" name="Save" value="Save" title="click to save" class="btn btn-primary addpads" id="Save">Save</button>';
									}else{
										$page_html .='<button type="submit" name="Unsave" value="Unsave" title="click to unsave" class="btn btn-primary addpads" id="Unsave">Unsave</button>';
									}
									$page_html .='</form>
									</td>
									
								</tr>';
							}//end of foreach
						}//end of if count
						
						$page_html .= '</table><div>';
					}//End of if there are voicemail records
					
                $page_html .= '</div></div>
            </div>
        </div>';
        
	return $page_html;
}
?>