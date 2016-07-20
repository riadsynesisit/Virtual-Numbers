<?php
class Access{

  function updateAccessLogs($item,$old=false,$new=false,$did=false,$user_id=false)
  {
  
    if(isset($_SESSION['admin_access_login']) && $_SESSION['admin_access_login']==true)
	{
	  $type='admin';
	  $type_id=$_SESSION['admin_access_logins_id'];
	}
	else
	{
	 $type='user';
	 
	 if(isset($_SESSION['user_logins_id'])){
	 	$type_id=$_SESSION['user_logins_id'];
	 }else{
	 	$type_id=0;
	 }
	
	}
	if($user_id==false)
	{
		if(isset($_SESSION['user_logins_id'])){
			$user_id=$_SESSION['user_logins_id'];
		}else{
			$user_id=0;
		}
	}
	if(isset($_SERVER["REMOTE_ADDR"]) && trim($_SERVER["REMOTE_ADDR"])!=""){
		$ip_address=$_SERVER["REMOTE_ADDR"];
	}else{
		$ip_address="0.0.0.0";
	}
	if($old==false)
	{
	  $old='';
	
	}
	if($new==false)
	{
	 $new='';
	
	}
	if($did==false)
	{
	 $did='';
	
	}
	
	$query = "insert into access_logs (
							user_id, 
							type, 
							type_id, 
							id_address, 
							item,
							old_value,
							new_value,
							did_number
							) values (
							$user_id,
							'$type',
							$type_id,
							'$ip_address',
							'".mysql_real_escape_string($item)."',
							'".$old."',
							'".$new."',
							'".$did."'
							)";
		
		mysql_query($query);
		
  
  
  
  }
  
  function getCountaccess(){
		
		$id=$_REQUEST['id'];
		$where='';
		$where.="where user_id=$id ";
		$query = "select count(*) as count from access_logs $where ";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		return $row['count'];
		
	}
	
	function for_page_range($limit,$offset){
	   $id=$_REQUEST['id'];
	   $where='';
		$where.="where user_id=$id ";
	
	    $query = "SELECT log_id,user_id,type,type_id,id_address,item,old_value,new_value,did_number,date_format(created,'%d/%m/%y @ %h:%i') as created FROM access_logs $where order by log_id desc LIMIT $limit OFFSET $offset;";
		
		$result = mysql_query($query);
		
		if(mysql_error()==""){
			if(mysql_num_rows($result)!=0){
				while($row=mysql_fetch_assoc($result)){
			$ret_arr[]=$row;
		}
		    return $ret_arr;
			}else{
				return '';
			}
		}else{
			echo "An error occurred while getting data.<br>
					Error is: ".mysql_error()."<br>";
			return false;
		}
	
	}


}?>