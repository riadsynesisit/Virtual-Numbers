<?php
function getLogoutPage(){
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
	
	$page_html = '';
	
	if($page_vars['action_message'] != ""){
		$page_html .= '<div class="tip_box">';
		$page_html .= $page_vars['action_message'];
		$page_html .= '</div>';
	}
	
	$page_html .= '
	
	<h3 class="page_header">Log Out</h3>
<div class="tip_box"> 
	<strong style="display: block; margin-bottom: 5px;">Pending...</strong>Please wait while we process your request.
</div>
	
	';
	
	return $page_html;
	
}