<?php

require "./includes/DRY_setup.inc.php";

function get_page_content($this_page){

	global $action_message;
	global $this_user;
	
	//check if the user is an admin or basicsupport, otherwise redirect to index page
	if($_SESSION['admin_flag']==1||$_SESSION['admin_flag']==2){
		if(isset($_REQUEST['Submit']) ){
			process_search();
		}else{
			show_index_page();
		}
	}else{
		not_admin_user();
	}
	
}

function show_index_page(){
	
	global $action_message;
	global $this_page;
	$page_vars = array();
	
	$page_vars['action_message'] = $action_message;
	$page_vars['menu_item'] = 'deleted_dids';
	
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	
	# => build the page
	$this_page->content_area .= getDeletedDIDsSearchPage();
	
	common_header($this_page,"Deleted DID Numbers");
	
}

function process_search(){

    global $action_message;
	
	global $this_page;
	
	$page_vars = array();
	
	$deleted_dids = new Deleted_DID();
	
	if(is_object($deleted_dids)==false){
		echo "Error: Failed to create Deleted_DID object.";
		exit;
	}
	
	//Inputs validation
	if(isset($_POST["did_number"])==false || trim($_POST["did_number"])==""){
		echo "Error: Required parameter did_number not posted";
		exit;
	}
	
	$did_number = trim($_POST["did_number"]);
	
	$did_deletions_count = $deleted_dids->getDIDDeletionsCount($did_number);
	
	$page_html = '
		<div id="page-wrapper">
			<div class="row">
            	<div class="col-lg-12">
                	<h1 class="page-header">Deleted DID Search results for DID Number: '.$did_number.'</h1>
	';
	
	$page_html .= '<table width="100%" border="0" cellspacing="0" cellpadding="7" class="my-call-recording">
		<tbody>';
	
	if($did_deletions_count==0){
		$page_html .= '<caption>No DID Deletions records found for DID Number: '.$did_number.'.</caption>';
	}else{
		//Table header html		
		$page_html .= "<thead class='blue-bg'><tr><th>Member ID</th><th>Forwarding To</th><th>Deleted On</th><th>Deleted Reason</th></tr></thead>";
		$deleted_did_details = $deleted_dids->getDeletedDIDDataByDID($did_number);
		foreach($deleted_did_details as $deleted_did){
			$member_num = intval($deleted_did["user_id"]) + 10000;
			$member_id= "M".$member_num;
			$page_html .= "<tr>
			<td><a href='account_details.php?id=".$deleted_did["user_id"]."&type=admin'>".$member_id."</a></td>
			<td>".$deleted_did["route_number"]."</td>
			<td>".$deleted_did["did_deleted_on"]."</td>
			<td>".$deleted_did["delete_reason"]."</td>
			</tr>";
		}
	}
	
	$page_html .= '</tbody>
	</table>';
	
	$page_html .= '<form action="deleted_dids.php">';
	$page_html .= '<button type="submit" name="btnSearchDelDIDs" value="Search Again" alt="Search Again" title="click to search again" class="btn btn-primary" id="btnSearchAgain">Search Again</button>';
	$page_html .= '</form>';
	
	$page_html .= '</div>';
	$page_html .= '</div>';
	$page_html .= '</div>';
	
	//build the page
	$this_page->content_area .= $page_html;
	
	common_header($this_page,"Deleted DID Numbers - Search Results");
	
}

function getDeletedDIDsSearchPage(){
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
	
	$page_html = '
		<div id="page-wrapper">
			<div class="row">
            	<div class="col-lg-12">
                	<h1 class="page-header">Deleted DID Numbers Search</h1>
	';
	
	if($page_vars['action_message'] != ""){
		$page_html .= '<div class="tip_box">';
		$page_html .= $page_vars['action_message'];
		$page_html .= '</div>';
	}
	
	$page_html .= '
		<form name="input" action="deleted_dids.php" method="post" >
			<table width="100%" border="0" cellspacing="0" cellpadding="7" class="update-details">
				<tr>
					<td idth="30%">
					<input name="did_number" value=""  class="update-input" type="textbox">
					</td>
					<td width="70%">
					Deleted DID Number
					</td>	
				</tr>
				<tr>
					<td>
						<input name="Submit" value="Search" type="submit" class="continue-btn"/>
					</td>
				</tr>
			</table>
		</form>
	';
	
	$page_html .= '</div>';
	$page_html .= '</div>';
	$page_html .= '</div>';
	
	return $page_html;
	
}

function not_admin_user(){
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
	
	$page_html = '
		<div id="page-wrapper">
			<div class="row">
            	<div class="col-lg-12">
                	<h1 class="page-header">Deleted DID Numbers Search</h1>
	';
	
	if($page_vars['action_message'] != ""){
		$page_html .= '<div class="tip_box">';
		$page_html .= $page_vars['action_message'];
		$page_html .= '</div>';
	}
	
	$page_html .= '
		<font color="red">You must be an admin user to view this page</font>
	';
	$page_html .= '</div>';
	$page_html .= '</div>';
	$page_html .= '</div>';
	
	//build the page
	$this_page->content_area .= $page_html;
	
	common_header($this_page,"Deleted DID Numbers - Search Results");
	
}

require("./includes/DRY_authentication.inc.php");
