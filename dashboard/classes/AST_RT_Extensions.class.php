<?php
class ASTRTExtensions{
	
	public $id;
	public $context;
	public $exten;
	public $priority;
	public $app;
	public $appdata;
	public $peak_allowed;
	public $offpeak_allowed;
	public $last_checked;
	
	function getRoute(){
		
		$query = "select * from ast_rt_extensions 
					where context='$this->context' and
							exten='$this->exten' and
							priority='$this->priority'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("ERROR in getRoute: ".mysql_errno()."<br>Description: ".mysql_error());
		}
		if(mysql_num_rows($result)==0){
			return 0;
		}else{
			return mysql_fetch_array($result);//Only one row is expected
		}
		
	}
	
	function createRoute(){
		
		//Set the last_checked to a old date so that the check happens again
		//This is to prevent entering a low cost route first, get it validated and then update
		//the route to a high cost one.
		
		$query = "insert into ast_rt_extensions (context, exten, priority, app, appdata,last_checked) 
					values ('$this->context', 
							'$this->exten', 
							$this->priority, 
							'$this->app', 
							'".mysql_real_escape_string($this->appdata)."',
							'2011-09-19 23:07:27')";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("ERROR in createRoute: ".mysql_errno()."<br>Description: ".mysql_error());
		}
		if($result){
			return mysql_insert_id();
		}else{
			return 0;
		}
		
	}
	
	function updateRoute(){
		
		//Set the last_checked to a old date so that the check happens again
		//This is to prevent entering a low cost route first, get it validated and then update
		//the route to a high cost one.
		
		$query = "update ast_rt_extensions 
					set context='$this->context', 
						exten='$this->exten', 
						priority=$this->priority, 
						app='$this->app', 
						appdata='".mysql_real_escape_string($this->appdata)."',
						last_checked = '2011-09-19 23:07:27'
					where id=$this->id";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("ERROR in updateRoute: ".mysql_errno()."<br>Description: ".mysql_error()."<br>$query");
		}
		return $result;
		
	}
	
	//function terminate_routes($user_id){
	//	$query = "delete from ast_rt_extensions where appdata like '%,$user_id,%'";
	//	mysql_query($query);
	//}
	
}