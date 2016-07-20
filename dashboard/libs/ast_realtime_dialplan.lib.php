<?php
function dialplan_rebuild_user_all(){
	
	//find all the assigned DID numbers of the currently logged in user and then
	//update the changes in the forwarding number in the ast_rt_extensions table
	//to make sure that they are all correct.
	
	$assigned_did = new Assigned_DID();
	$assigned_did->user_id = $_SESSION['user_logins_id'];
	
	$user_dids = $assigned_did->getUserDIDs();
	
	foreach($user_dids as $user_did){
		dialplan_did_route("vssp", $user_did['did_number'], $user_did['route_number'], $user_did['user_id']);
	}
	
}

function dialplan_did_route($context, $incoming, $route_to, $user_id){
	
	$ast_rt_extensions = new ASTRTExtensions();
	if(is_object($ast_rt_extensions)==false){
		die("Error: Failed to create ASTRTExtensions object.");
	}
	
	//set the properties
	$ast_rt_extensions->context = $context;
	$ast_rt_extensions->exten = $incoming;
	$ast_rt_extensions->priority = 1;
	$ast_rt_extensions->app="macro";
	$ast_rt_extensions->appdata="did_route,$route_to,$user_id,$incoming";
	
	$route = $ast_rt_extensions->getRoute();
	
	if($route==0){
		//No route found so create a new one
		$record_id = $ast_rt_extensions->createRoute();
		if($record_id==0){
			die("Error: Failed to create route.");
		}
	}else{
		$record_id = $route['id'];
	}
	$ast_rt_extensions->id = $record_id;
	
	//Now update this route
	return $ast_rt_extensions->updateRoute();
	
}
?>