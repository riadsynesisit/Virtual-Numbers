<?php


	function getIndexPage($this_page){
            
        if($_POST['action'] ==  'verifyEmail')
        {
            $email      =   $_SESSION['user_login'];
            $userInfo   =   mysql_fetch_object(mysql_query("SELECT uniquecode,name FROM user_logins WHERE email LIKE '".$_SESSION['user_login']."'"));
            $uniquecode =   $userInfo->uniquecode;
            $user_name  =   $userInfo->name;
            account_verify_email($_SESSION['user_logins_id'],$email,$uniquecode,$user_name);
        }
        
        $get_CID    =   $_GET['id'];
        if(!empty($get_CID)){
            $get_cidURL =   "?id=".$get_CID;
        }
        
        $page_vars = array();
	
	$page_vars = $this_page->variable_stack;
        
        
        
        $currentdate    =   date('Y-m-d');
        $last7days      =   date('Y-m-d',strtotime(date("Y-m-d",strtotime($currentdate)) . " -7 days"));
        $last60days     =   date('Y-m-d',strtotime(date("Y-m-d",strtotime($currentdate)) . " -60 days"));
        $last90days     =   date('Y-m-d',strtotime(date("Y-m-d",strtotime($currentdate)) . " -90 days"));
        $errorCount     =   0;
        
        if($page_vars['user_last_login']==""){
        	$last_login = "First time user";
        }else{
        	$last_login = $page_vars['user_last_login'];
        }
	
        $page_html = '
        	<div id="page-wrapper">
            	<div class="row">
                	<div class="col-lg-12">
                    	<h1 class="page-header">Dashboard</h1>
                    	<div class="panel panel-default Mtop20">
  							<div class="panel-heading">Login Tracker</div>
  							<div class="panel-body">
    							<p><i class="glyphicon glyphicon-eye-open"></i> Last Login : <strong class="text-warning">'.$last_login.'</strong></p>
  							</div>
						</div>
                	</div>
            	</div>
            <!-- /.row -->
            	
            <!-- /.row -->
        	</div>
        	<!-- /#page-wrapper -->
        ';
        
	$page_html.='
				<div class="mid-section">
				
				';
                                        $chkQry =   mysql_query("SELECT id,email FROM user_logins WHERE verify_email LIKE 'N' AND id = '".$_SESSION['user_logins_id']."'");
                                        if(mysql_num_rows($chkQry) > 0){
                                        $errorCount =   $errorCount + 1;   
	                                        if(SITE_COUNTRY=="UK"){ 
		                                        $page_html.='<table width="95%" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#facacc">
		                                            <tr>
		                                                <td height="5px" colspan="2"></td>
		                                            </tr>
		                                            <tr>
		                                                <td colspan="2" class="index_alerts">Please click on the link sent to you to verify your email id. <a href="update_account_details.php'.$get_cidURL.'">Update email</a> or <a href="javascript:void(0)" onclick="manualVerify()">resend verification email</a>.</td>
		                                            </tr>
		                                            <tr>
		                                                <td height="5px" colspan="2"></td>
		                                            </tr>
		                                        </table>
		                                        <table width="95%" border="0" cellspacing="0" cellpadding="0" align="center">
		                                            <tr>
		                                                <td>&nbsp;</td>
		                                            </tr>
		                                        </table>';
	                                        }
                                        }
                                        
                                        $chkdid =   mysql_query("SELECT did_number,did_assigned_on FROM assigned_dids WHERE did_assigned_on BETWEEN '".$last7days."' AND '".$currentdate."' AND user_id LIKE '".$_SESSION['user_logins_id']."'");
                                        if(mysql_num_rows($chkdid) > 0)
                                        {
                                            while($didRow     =   mysql_fetch_object($chkdid))
                                            {
                                            $assigndate =   $didRow->did_assigned_on;
                                            $did_number =   $didRow->did_number;
                                            $callCount  =   mysql_fetch_object(mysql_query("SELECT count(*) as total FROM cdr WHERE accountcode LIKE '$did_number'",$conn))->total;
                                            
                                            if(empty($callCount)){
                                            $next7days  =   date('Y-m-d',strtotime(date("Y-m-d",strtotime($assigndate)) . " + 7 days"));
                                            $expiredays =   (strtotime($next7days) - strtotime($currentdate))/86400;
                                            $expiredays1=   7 - $expiredays;
                                            $noticedate =   date('D M d, Y',strtotime($next7days));
                                            $errorCount =   $errorCount + 1;
                                        }}}
                                        
                                        $chkdid =   mysql_query("SELECT did_number FROM assigned_dids WHERE user_id LIKE '".$_SESSION['user_logins_id']."'");
                                        if(mysql_num_rows($chkdid) > 0)
                                        {
                                            while($didRow     =   mysql_fetch_object($chkdid))
                                            {
                                            $did_number =   $didRow->did_number;
                                            $calldate   =   mysql_fetch_object(mysql_query("SELECT date_format(calldate,'%Y-%m-%d') as call_date FROM cdr WHERE accountcode LIKE '$did_number' AND calldate BETWEEN '".$last90days." 00:00:00' AND '".$last60days." 23:59:59' ORDER BY calldate DESC LIMIT 1"))->call_date;
                                            if(!empty($calldate)){
                                            $next90days =   date('Y-m-d',strtotime(date("Y-m-d",strtotime($calldate)) . " + 90 days"));
                                            $expiredays =   (strtotime($currentdate) - strtotime($calldate))/86400;
                                            $expiredays1=   90 - $expiredays;
                                            $noticedate =   date('D M d, Y',strtotime($next90days));
                                            if($expiredays1 < 30){
                                            $errorCount =   $errorCount + 1;    
                                        }}}}
                                       
				$page_html.='</div> <!-- Mid section start here -- -->

<form name="errorForm" action="" method="post">
<input type="hidden" name="action" value="submitError"/>
</form>
<form name="verifyForm" action="" method="post">
<input type="hidden" name="action" value="verifyEmail"/>
</form>
<script type="text/javascript">
function check_error()
{
document.errorForm.submit();
}

function manualVerify()
{
    document.verifyForm.submit();
}
</script>
';
	
	return $page_html;
	
}