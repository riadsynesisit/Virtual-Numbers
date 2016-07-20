<?php
    class AsteriskSQLiteCDR
    {
	function total_calls_for_user_id($user_id)
        {
            $query  =   "SELECT count(*) as tot_calls FROM cdr WHERE userfield='$user_id';";
            $result =   mysql_query($query);
            $row    =   mysql_fetch_array($result);
            return $row['tot_calls'];
        }
	
	function for_page_range($this_user_id, $limit, $offset)
        {
            $retArr =   array();
            $query  =   "SELECT * FROM cdr WHERE userfield = '$this_user_id' ORDER BY calldate DESC LIMIT $limit OFFSET $offset;";
            $result =   mysql_query($query);
            if(mysql_num_rows($result)!=0)
            {
                while($row  =   mysql_fetch_array($result))
                {
                    $retArr[]   =   $row;
                }
                return $retArr;
            }else{
                return 0;
            }
	}
        
        function asterisk_errors($this_user_id, $limit, $offset)
        {
            
            $retArr =   array();
            $query  =   "SELECT * FROM cdr WHERE disposition NOT LIKE 'ANSWERED' ORDER BY calldate DESC LIMIT $limit OFFSET $offset;";
            $result =   mysql_query($query);
            if(mysql_num_rows($result)!=0)
            {
                while($row  =   mysql_fetch_array($result))
                {
                    $retArr[]   =   $row;
                }
                return $retArr;
            }else{
                return 0;
            }
	}
        
        function asterisk_archieve_errors($this_user_id, $limit, $offset)
        {
            
            $retArr =   array();
            $query  =   "SELECT * FROM cdr_archieve ORDER BY calldate DESC LIMIT $limit OFFSET $offset;";
            $result =   mysql_query($query);
            if(mysql_num_rows($result)!=0)
            {
                while($row  =   mysql_fetch_array($result))
                {
                    $retArr[]   =   $row;
                }
                return $retArr;
            }else{
                return 0;
            }
	}
	
	function number_of_calls_today()
        {
            $number_of_calls_today = 0;
            $day_start  =   date('Y-m-d 00:00:01');
            $query      =   "SELECT count(*) FROM cdr WHERE calldate > '$day_start' AND substr(accountcode,1,3)='070' AND lastapp = 'Dial'";
            $result     =   mysql_query($query);
            $row        =   mysql_fetch_array($result);
            return $row['count(*)'];		
		
	}//number_of_calls_today
	
	function number_of_087calls_today()
        {
            $number_of_calls_today = 0;
            $day_start  =   date('Y-m-d 00:00:01');
            $query      =   "SELECT count(*) FROM cdr WHERE calldate > '$day_start' AND substr(accountcode,1,3)='087' AND lastapp = 'Dial'";
            $result     =   mysql_query($query);
            $row        =   mysql_fetch_array($result);
            return $row['count(*)'];		
		
	}//number_of_calls_today
	
    }
?>