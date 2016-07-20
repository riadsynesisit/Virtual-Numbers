<?php
function getUpdateAccountDetailsPage(){
    
        $get_CID    =   $_GET['id'];
        if(!empty($get_CID)){
            $get_cidURL =   "?id=".$get_CID;
        }
        
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
	
	$page_html = '';
	
	$page_html.='<div class="title-bg"><h1>Account Details</h1></div>
				<div class="mid-section">
				
				<!-- Date section start here -->
					<div class="date-bar">
					<div class="left-info"><b>Member - Contact Information</b></div>
					<ul>
						<li><a href="#"><img src="images/print-icon.jpg" alt="Print" title="Print" /></a></li>
						<li><a href="#"><img src="images/mail-icon.jpg" alt="E-Mail" title="E-Mail" /></a></li>
					</ul>
					</div>
					<!-- Date section end here -->';
	if($page_vars['action_message'] != ""){
		$page_html .= '<div class="tip_box">';
		$page_html .= $page_vars['action_message'];
		$page_html .= '</div>';
	}
	
	/*$page_html .= '

            <form name="input" method="POST" action="update_account_details.php" >
            <table class="table_style2" align="center" border="0" cellspacing="0" width="100%">
              <tbody>
                <tr>
                  <th colspan="2" class="first"><span class="first td_order_DESC">Member - Contact Information</span></th>
                </tr>
                <tr>
                  <td width="104" height="33" class="first">Full Name:</td>
                  <td width="517"><span class="first">
                    <input name="name" value="'.$page_vars['user_name'].'" size="25" type="text" />
                    <span class="text_style1">*</span></span></td>
                </tr>
                <tr>
                  <td class="first">Email Address:</td>
                  <td><span class="first">
                    <input name="email" value="'.$page_vars['email'].'" size="25" type="text" />
                    <span class="text_style1">*</span></span></td>
                </tr>
                </tbody>
              <tbody id="business_details" style="">
              </tbody>
            </table>
            <br />
            <p align="right">
				<input type="submit" alt="Continue" name="submit" value="Continue &rarr;" id="update_contact_submit">
            </p>
        </form>
	
	';*/
	
	
$page_html.='<form name="input" method="POST" action="update_account_details.php'.$get_cidURL.'" >
					<table width="100%" border="0" cellspacing="0" cellpadding="7" class="update-details">
					  <tr>
						<td width="30%">Full Name:</td>
						<td width="70%"><input type="text" name="name" id="name" value="'.$page_vars['user_name'].'" class="update-input" /> <span>*</span></td>
					  </tr>
					  <tr>
						<td>Email Address:</td>
						<td><input type="text" name="email" id="email" value="'.$page_vars['email'].'" class="update-input" /> <span>*</span></td>
					  </tr>					  
					  <tr>
						<td class="border-none">&nbsp;</td>
						<td><input type="submit" name="submit" id="Continue-update" value="Continue" alt="Continue" title="Continue" class="continue-btn" /></td>
					  </tr>
					</table>
                     </form>
					
				</div> <!-- Mid section start here -->';
	
	return $page_html;
	
}
?>