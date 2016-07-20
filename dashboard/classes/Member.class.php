<?php
    class Member
    {
        function getCountMembers()
        {
            $where      =   '';
            $didquery   =   0;

            if($_REQUEST['did']!='' || $_REQUEST['dest']!='')
            {
                $didquery   =   1;
                $where      .=  ' where u.id=ad.user_id ';
                $where_del      .=  ' where u.id=dd.user_id ';
            }
        
            if($_REQUEST['ip_address']!='')
            {
                $ip_address =   $_REQUEST['ip_address'];
                if($where   ==  ''){
                    $where  .=  "where ip_address like '%$ip_address%' ";
                }else{
                    $where  .=  "and ip_address like '%$ip_address%' ";
                }	
            }
            
            if($_REQUEST['id']!='')
            {
                $id =   $_REQUEST['id'];
                $se =   0;
                $gid=   str_ireplace("m","",$id);
                $sid=   $gid-10000;
                if($sid >   0){
                    $id =   $sid;
                    $se =   1;
                }else{
                    $id =   $gid;
                }
                
                if($se  ==  0){
                    if($where   ==  ''){
                        $where  .=  "where id like '%$id%' ";
                    }else{
                        $where  .=  "and id like '%$id%' ";
                    }
                }else{
                    if($where   ==  ''){
                        $where  .=  "where id=$id ";
                    }else{
                        $where  .=  "and id=$id ";
                    }
                }	
            }
        
            if($_REQUEST['email']!='')
            {
                $email  =   $_REQUEST['email'];
                if($where   ==  ''){
                    $where  .=  "where email like '%$email%' ";
                }else{
                    $where  .=  "and email like '%$email%' ";
                }		  
            }
            
            if($_REQUEST['did']!='')
            {
                $did    =   $_REQUEST['did'];
                $where  .=  " and ad.did_number like '%$did%' ";
                $where_del  .=  " and dd.did_number like '%$did%' ";
            }
            
            if($_REQUEST['dest']!='')
            {
                $dest   =   $_REQUEST['dest'];
                $where  .=  " and ad.route_number like '%$dest%' ";
            }

            if($didquery    ==  1){
                $query  =   "select count(*) as count from user_logins u,assigned_dids ad $where ";
                $query_del  =   "select count(*) as count from user_logins u,deleted_dids dd $where_del ";
            }else{
                $query  =   "select count(*) as count from user_logins $where ";
            }

            if($_REQUEST['transId']!=''){
                $transId   =   $_REQUEST['transId'];
            	$query  =   "select count(*) as count from user_logins u, payments_received p where p.user_id=u.id and p.wp_transId='$transId'";
			 	$result =   mysql_query($query);
			 	$row    =   mysql_fetch_array($result);
			 	if($row['count']!=0){
				 	return  $row['count'];
				 }else{
				 	$query  =   "select count(*) as count from user_logins u, payments_received p where p.user_id=u.id and p.pp_receiver_trans_id='$transId'";
			 		$result =   mysql_query($query);
			 		$row    =   mysql_fetch_array($result);
			 		return  $row['count'];
				 }
            }elseif($_REQUEST['did']!=''){
            	$result =   mysql_query($query);
	            $row    =   mysql_fetch_array($result);
	            //Now Deleted DIDs
	            $result_del =   mysql_query($query_del);
	            $row_del    =   mysql_fetch_array($result_del);
	            return  intval($row['count']) + intval($row_del['count']);
            }else{
				$result =   mysql_query($query);
	            $row    =   mysql_fetch_array($result);
	            return  $row['count'];
			}
        }

        function for_page_range($limit,$offset)
        {
            $where      =   '';
            $didquery   =   0;

            if($_REQUEST['did']!='' || $_REQUEST['dest']!='')
            {
                $didquery   =   1;
                $where      .=  ' where u.id=ad.user_id ';
                $where_del      .=  ' where u.id=dd.user_id ';
            }
        
            if($_REQUEST['ip_address']!='')
            {
                $ip_address=$_REQUEST['ip_address'];
                if($where   ==  ''){
                    $where  .=  "where ip_address like '%$ip_address%' ";
                }else{
                    $where  .=  "and ip_address like '%$ip_address%' ";
                }	
            }
            
            if($_REQUEST['id']!='')
            {
                $id =   $_REQUEST['id'];
                $se =   0;
                $gid=   str_ireplace("m","",$id);
                $sid=   $gid-10000;
            
                if($sid>0){
                    $id =   $sid;
                    $se =   1;
                }else{
                    $id =   $gid;
                }
            
                if($se  ==  0){
                    if($where   ==  ''){
                        $where  .=  "where id like '%$id%' ";
                    }else{
                        $where  .=  "and id like '%$id%' ";
                    }
                }else{
                    if($where   ==  ''){
                        $where  .=  "where id=$id ";
                    }else{
                        $where  .=  "and id=$id ";
                    }
                }	  
            }
        
            if($_REQUEST['email']!='')
            {
                $email  =   $_REQUEST['email'];
                if($where   ==  ''){
                    $where  .=  "where email like '%$email%' ";
                }else{
                    $where  .=  "and email like '%$email%' ";
                }		  
            }
            
            if($_REQUEST['did']!='')
            {
                $did    =   $_REQUEST['did'];
                $where  .=  " and ad.did_number like '%$did%' ";
                $where_del  .=  " and dd.did_number like '%$did%' ";
            }
            
            if($_REQUEST['dest']!='')
            {
                $dest   =   $_REQUEST['dest'];
                $where  .=  " and ad.route_number like '%$dest%' ";
            }

            if($didquery    ==  1){
                //$query  =   "select u.*,ad.route_number as route_number,ad.did_number as did_number FROM  user_logins u,assigned_dids ad $where LIMIT $limit OFFSET $offset  ";
                $query_current  =   "select u.*,ad.route_number as route_number,ad.did_number as did_number, 'Current' as did_status, 1 as myorder, ad.did_expires_on as expiry_order FROM  user_logins u,assigned_dids ad $where ";
                $query_deleted  =   "select u.*,dd.route_number as route_number,dd.did_number as did_number, dd.delete_reason as did_status, 2 as myorder, dd.did_deleted_on as expiry_order FROM  user_logins u,deleted_dids dd $where_del ";
                $query = "select * from (".$query_current." union ".$query_deleted.")  as t order by t.myorder asc, t.expiry_order asc LIMIT $limit OFFSET $offset ";
            }else{
                $query  =   "SELECT * FROM user_logins $where LIMIT $limit OFFSET $offset;";
            }
            
			 if($_REQUEST['transId']!='')
            {
                $transId   =   $_REQUEST['transId'];
				$query  =   "select u.* from user_logins u, payments_received p where p.user_id=u.id and p.wp_transId='$transId'";
				$result =   mysql_query($query);
            	$row    =   mysql_num_rows($result);
				 if($row==0)
				 {
				  $query  =   "select u.* from user_logins u, payments_received p where p.user_id=u.id and p.pp_receiver_trans_id='$transId'";
				   $result =   mysql_query($query);
				 }
            }
			else
			{
            $result =   mysql_query($query);
			}

            if(mysql_error()    ==  ""){
                if(mysql_num_rows($result)  !=  0){
                    while($row  =   mysql_fetch_assoc($result)){
                        $ret_arr[]  =   $row;
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
    }
?>	