<?php

function getStandardPage($this_page){
    
	global $getID;

    if(!empty($getID)){
        $appendUrl="?id=$getID";
    }
    
    $user = new User();
	if(is_object($user)==false){
		die("Failed to create User object.");
	}
    
    //Get Age of account in days
	$age_in_days = 1;//$user->get_user_age_in_days($_SESSION["user_logins_id"]);
	//Check if the 7 days age requirement is overridden by admin 
	
	$user_details = $user->getUserInfoById($_SESSION["user_logins_id"]);
	
	$show_acct_crdt = $user_details['show_acct_crdt'];//$user->get_show_acct_crdt($_SESSION["user_logins_id"]);
	
	$leads_tracking_menu = $user_details["leads_tracking_menu"];
    
	$total_did_count    =   $this_page->variable_stack['my_DID_count'];
	
	if($total_did_count == 0){
	    $style_top  =   'style="width:1000px; border-radius:0px 8px 0px 0px;"';
	    $style_bottom  =   'style="width:1000px; border-radius:0px 0px 8px 0px;"';
	    $style              =   'style="width:1000px"';
	}
    
	$page_html="<!DOCTYPE html>\n";
	$page_html .="<html xmlns='http://www.w3.org/1999/xhtml'>\n";
	
	$page_html .="<head>\n";
	$page_html .="<title>$this_page->title</title>\n";
	
	$page_html .='<link rel="stylesheet" type="text/css" href="./css/bootstrap.css" />'."\n";
	$page_html .='<link rel="stylesheet" type="text/css" href="./font-awesome/css/font-awesome.css" />'."\n";
	$page_html .='<link rel="stylesheet" type="text/css" href="./css/sb-admin.css" />'."\n";
	
	$page_html .='<!--[if lte IE 9]><link rel="stylesheet" type="text/css" href="./css/ie9.css"><![endif]-->
	<!--[if lte IE 8]><link rel="stylesheet" type="text/css" href="./css/ie8.css"><![endif]-->
	<!--[if lte IE 7]><link rel="stylesheet" type="text/css" href="./css/ie7.css"><![endif]-->
	';
	foreach ($this_page->scripts as $script){
		$page_html .='<script src="../js/'.$script.'" type="text/javascript"></script>'."\n";
	}
	$page_html .='<script src="./js/bootstrap.min.js" type="text/javascript"></script>'."\n";
	$page_html .='<script src="./js/plugins/morris/raphael-2.1.0.min.js" type="text/javascript"></script>'."\n";
	$page_html .='<script src="./js/plugins/morris/morris.js" type="text/javascript"></script>'."\n";
	
	foreach ($this_page->meta_tags as $meta_tag){
		$page_html .= '<meta name="'.$meta_tag[0].'" content="'.$meta_tag[1].'" />'."\n";
	}
	
	if($this_page->meta_refresh_sec>0 && $this_page->meta_refresh_page!=""){
		$page_html .= '<meta http-equiv="refresh" content="'.$this_page->meta_refresh_sec.';url='.$this_page->meta_refresh_page.'" />'."\n";
	}
	
	if(SITE_COUNTRY!="COM"){
        $page_html .="<!--Start of Zopim Live Chat Script-->\n";
		$page_html .="<script type=\"text/javascript\">\n";
		$page_html .="window.\$zopim||(function(d,s){var z=\$zopim=function(c){z._.push(c)},$=z.s=\n";
		$page_html .="d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.\n";
		$page_html .="_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');\n";
		$page_html .="$.src='//v2.zopim.com/?1piIDUavtFoOMB89h2pj9OK8cT8CswZK';z.t=+new Date;$.\n";
		$page_html .="type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');\n";
		$page_html .="</script>\n";
		$page_html .="<!--End of Zopim Live Chat Script-->\n";
	}
	
	$page_html .="</head>\n";
	
	$page_html .="<body>\n";
	
	$page_html .="<div id='wrapper'>";
	
	if(SITE_COUNTRY=="UK"){ 
		$logo_revision = "-uk"; 
	}elseif(SITE_COUNTRY=="AU"){
		$logo_revision = "";
	}else{
		$logo_revision = "-r1";
	}
	
	$page_html .='
		<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <div class="logo"><a href="index.php"><img src="../images/main-logo'.$logo_revision.'.png" width="234" height="100" alt="Logo"></a>
                  <div class="clearfix"></div>
              </div>
            </div>';
           if(!$this_page->is_border_page){
           	$page_html.='
           	<div class="access">            
            	<ul class="nav navbar-top-links">
					<span class="username">
					Welcome, <strong class="txt-blue">'.$_SESSION['user_name'].'</strong> Member<strong class="txt-blue"> ID: M'.(intval($_SESSION['user_logins_id'])+10000).'</strong></span>
                        <li><a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
            	</ul>
			</div>';
           }
        $page_html.='</nav>';
        
        if(!$this_page->is_border_page){
        	if($_SESSION['admin_flag']==0||$_SESSION['admin_flag']==1){//Not for Basic Support Persons
        		$page_html .='
	        	<nav class="navbar-default navbar-static-side sidebar-bg" role="navigation">
	        		<div class="sidebar-collapse">
	        			<ul class="nav" id="side-menu">
	        				<li>
	        					<a href="javascript:void(0)" class="cur-ptr"><i class="fa fa-users fa-fw"></i> My Account<span class="fa arrow"></span></a>
	        					<ul class="nav nav-second-level collapse in">
	        						<li>
		                                <a href="index.php">My Home Page</a>
		                            </li>
	        						<li>
		                                <a href="account_details.php">Account Details</a>
		                            </li>
		                            <li>
		                                <a href="view-notifications.php">View Notifications</a>
		                            </li>
	        					</ul>
	        				</li>
	        				<li>
	        					<a href="javascript:void(0)" class="cur-ptr"><i class="fa fa-phone fa-fw"></i> Number Manager<span class="fa arrow"></span></a>
	        						<ul class="nav nav-second-level collapse in">
		        						<li>
			                                <a href="add-number-1.php">Add New Number</a>
			                            </li>
		        						<li>
			                                <a href="edit_routes.php">My Online Numbers</a>
			                            </li>
			                            <li>
			                                <a href="call_records.php">My Call Records</a>
			                            </li>
			                            <li>
			                                <a href="voice_mail.php">Voice Mail <span class="b-rounded fltrt"><strong>'.$this_page->variable_stack['my_unread_voicemails_count'].' New</strong></span></a>
			                            </li>';
        				if($leads_tracking_menu=="Y"){
			              $page_html .= '<li>
			                                <a href="leads_tracking.php">Leads Tracking</a>
			                            </li>';
        				}//end of if leads tracking menu
		                 $page_html .= '</ul>
	        				</li>';
        					//if(SITE_COUNTRY=='AU'){//Billing is only for AU Site or for admins
	        					$page_html .= '<li>
	        					<a href="javascript:void(0)" class="cur-ptr"><i class="fa fa-money fa-fw"></i> Billing<span class="fa arrow"></span></a>
	        						<ul class="nav nav-second-level collapse in">';
	        					if($age_in_days>7 || $show_acct_crdt=='Y'){//Show account credit link only after seven days of sign-up
	        							$page_html .= '<li>
			                                <a href="add_account_credit.php">Account Credit</a>
			                            </li>';
	        					}
		        						$page_html .= '<li>
			                                <a href="invoices.php">Invoices</a>
			                            </li>
	        						</ul>
	        					</li>';
        					//}//End of if site country is AU - Billing is only for AU site
				        	if($_SESSION['admin_flag']==1){//Extra menu items only for admin login
				        		$page_html .= '<li>
				        		<a href="javascript:void(0)" class="cur-ptr"><i class="fa fa-money fa-fw"></i> System Manager<span class="fa arrow"></span></a>
	        						<ul class="nav nav-second-level collapse in">
	        							<li>
			                                <a href="admin_system_status.php">Current System Status</a>
			                            </li>
		        						<li>
			                                <a href="admin_system_notices.php">Current System Notices</a>
			                            </li>
			                            <li>
			                                <a href="pending_orders.php">Pending Orders</a>
			                            </li>
										<li>
			                                <a href="renewal_errors.php">Renewal Errors</a>
			                            </li>
			                            <li>
			                                <a href="active_paid_dids.php">Acitve Paid DID Numbers</a>
			                            </li>
			                            <li>
			                                <a href="monthly_summary.php">Monthly Summary</a>
			                            </li>
			                            <li>
			                                <a href="admin_dest_costs_mgr.php">Call Destination Costs</a>
			                            </li>
			                            <!--<li>
			                                <a href="admin_asterisk_errors_mgr.php">Asterisk Errors</a>
			                            </li>-->
			                            <li>
			                                <a href="admin_fraud_detection_mgr.php">Fraud Detection Data</a>
			                            </li>
			                            <!--<li>
			                                <a href="admin_fraud_detection_notices.php">Fraud Detection Notices</a>
			                            </li>-->
			                            <li>
			                                <a href="search.php">Member Search</a>
			                            </li>
			                            <li>
			                                <a href="blocked_destinations.php">Outbound Number Blocker</a>
			                            </li>
			                            <li>
			                                <a href="inblocked_destinations.php">Inbound Number Blocker</a>
			                            </li>
	        						</ul>
				        		
				        		
				        		</li>';
				        	}
	        			$page_html .= '</ul>
	        		</div>
	        	</nav>
        	';
        	}//End of not for Basic Support persons
        }//end of if not border page
        
        $page_html .= $this_page->content_area;

$page_html .= '</div>';


return $page_html;

}
?>