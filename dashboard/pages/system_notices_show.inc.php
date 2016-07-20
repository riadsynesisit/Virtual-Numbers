<?php
function getSystemNotices(){
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
	
	$page_html = "";
	
	$page_html .= '
		<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">View Notifications</h1>
                </div>
            </div>
            <div class="table-responsive">
				<table class="table table-hover">
					<thead class="blue-bg">
						<tr>
							<th>Date</th>
							<th>Subject</th>
							<th>Status</th>
							<th>Email</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							
						</tr>
					</tbody>
				</table>
			</div>
			
        </div>
	';
	
	/*$page_html .= '<div class="title-bg"><h1>System Notices</h1></div><div class="mid-section">
	               <div class="date-bar">
					<div class="left-info"></div>
										</div>
					<!-- Date section end here -->';				
	
	
	if($page_vars['action_message'] != "" && !isset($_GET['id'])){
		$page_html .= '<div class="tip_box">';
		$page_html .= $page_vars['action_message'];
		$page_html .= '</div>';
	}
	
	$severity = array();
	$severity[0] = array('Blue','Info');
	$severity[1] = array('Green','Non-Service Impacting');
	$severity[2] = array('Yellow','Service Impacting');
	$severity[3] = array('Red','Severely Service Impacting');
	
	$message_type = array('Planned','Current','Recent');
	
  	if(count($page_vars['my_messages']) > 0 ){
		$page_html .= '<li style="padding:0px 15px 6px 21px;margin:10px 21px 0px 20px;">Your Messages:
		<div class="tip_box" style="background:#facacc">
			<form action="index.php" method="post" accept-charset="utf-8">'."\n";
				foreach($page_vars['my_messages'] as $this_message){
					$page_html .= '<p>'."\n";
						if($this_message['viewed']){
							$page_html .= $this_message['message_title'];
						}else{
							$page_html .= '<strong>'.$this_message['message_title'].'</strong>'."\n";
						}
						$page_html .= ' ('.$this_message['message_creation'].')'."\n";
						//check for ::keyword:: subsitutions
						$replace = ceil(abs(strtotime($this_message['valid_until']) - mktime())/(24*60*60));
						$page_html .= '<br />'.str_replace("::expire_days::", $replace, $this_message['message_body'])."\n";
					$page_html .= '</p>'."\n";
					if($this_message['user_can_delete']){
						$page_html .= '<input type="submit" value="Delete" name="'.$this_message['id'].'">'."\n";
					}
				}//end of foreach loop
			$page_html .= '</form>
		</div>';
  	}

	if(count($page_vars['old_message_list']) == 0){
		$page_html .= '<li style="padding:0px 15px 6px 21px;margin:10px 21px 0px 20px;">There are no old system notices.<br />'."\n";
	}else{
		$page_html .= '<li style="padding:0px 15px 6px 21px;margin:10px 21px 0px 20px;">Old System Notices:'."\n";
	 	foreach($page_vars['old_message_list'] as $this_message){
			$page_html .= '<div class="tip_box">'."\n";
			$page_html .= $severity[$this_message['severity']][1].': '.$this_message['message_title'].' ('.$message_type[$this_message['message_type']].') <br />'."\n";
			$page_html .= $this_message['message_body'].'<br />'."\n";
			$page_html .= $this_message['message_creation'].' to '.$this_message['valid_until'].'<br />'."\n";
			$page_html .= '<br /><a href="./admin_system_notices.php?id='.$this_message['id'].'">Click here to delete this message</a><br />'."\n";
			$page_html .= '</div>'."\n";
	 	}
	}

	if(count($page_vars['current_message_list']) == 0){
		$page_html .= '<li style="padding:0px 15px 6px 21px;margin:10px 21px 0px 20px;">There are no current system notices.<br />'."\n";
	}else{
		$page_html .= '<li style="padding:0px 15px 6px 21px;margin:10px 21px 0px 20px;">Current System Notices:'."\n";
	 	foreach($page_vars['current_message_list'] as $this_message){
			$page_html .= '<div class="tip_box">'."\n";
			$page_html .= $severity[$this_message['severity']][1].': '.$this_message['message_title'].' ('.$message_type[$this_message['message_type']].') <br />'."\n";
			$page_html .= $this_message['message_body'].'<br />'."\n";
			$page_html .= $this_message['message_creation'].' to '.$this_message['valid_until'].'<br />'."\n";
			$page_html .= '<br /><a href="./admin_system_notices.php?id='.$this_message['id'].'">Click here to delete this message</a><br />'."\n";
			$page_html .= '</div>'."\n";
	 	}
	}

	if(count($page_vars['future_message_list']) == 0){
		$page_html .= '<li style="padding:0px 15px 6px 21px;margin:10px 21px 0px 20px;">There are no future system notices.<br />'."\n";
	}else{
		$page_html .= '<li style="padding:0px 15px 6px 21px;margin:10px 21px 0px 20px;">Future System Notices:'."\n";
	 	foreach($page_vars['future_message_list'] as $this_message){
			$page_html .= '<div class="tip_box">'."\n";
			$page_html .= $severity[$this_message['severity']][1].': '.$this_message['message_title'].' ('.$message_type[$this_message['message_type']].') <br />'."\n";
			$page_html .= $this_message['message_body'].'<br />'."\n";
			$page_html .= $this_message['message_creation'].' to '.$this_message['valid_until'].'<br />'."\n";	
			$page_html .= '<br /><a href="./admin_system_notices.php?id='.$this_message['id'].'">Click here to delete this message</a><br />'."\n";
			$page_html .= '</div>'."\n";
	 	}
	}
	$page_html .= '</div>';*/
	return $page_html;
	
}
?>