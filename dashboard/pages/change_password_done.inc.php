<?php
function getChangePasswordDone($this_page){
	
	$page_vars = $this_page->variable_stack;
	
	$page_html = '<div class="title-bg"><h1>Password Change Results</h1></div>
	<div class="mid-section"><div class="date-bar"><div class="left-info"></div></div>';
	
	if($page_vars['action_message']!=""){
		$page_html .= '<div class="tip_box">'."\n";
		$page_html .= $page_vars['action_message']."\n";
		$page_html .= '</div>'."\n";
	}
	
	$page_html.='</div>';
	return $page_html;
	
}
?>
