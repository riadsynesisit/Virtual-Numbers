<!doctype html>
<?php

require "inc_header_page.php";
//require "common_header.php";

require("./dashboard/classes/Block_Destination.class.php");
require("./dashboard/classes/Unassigned_087DID.class.php");
require("./dashboard/classes/Unassigned_DID.class.php");
require("./dashboard/classes/Assigned_DID.class.php");
require("./dashboard/classes/VoiceMail.class.php");
require("./dashboard/classes/Access.class.php");
require("./dashboard/classes/AST_RT_Extensions.class.php");
require("./dashboard/classes/SystemConfigOptions.class.php");
require("./dashboard/libs/ast_realtime_dialplan.lib.php");

$blnError = false;
$errMsg = "";

//Check for user session
if($blnError == false && (isset($_SESSION['user_logins_id'])==false || trim($_SESSION['user_logins_id'])=="")){
	$blnError = true;
	$errMsg = "Error: Session expired. Please login again.";
}

if($blnError == false && isset($_POST['forwarding_number'])==false || trim($_POST['forwarding_number'])==""){
	$blnError = true;
	$errMsg = "Error: Required parameter forwarding_number not passed.";
}

$block_destination = new Block_Destination();
if($blnError == false && is_object($block_destination)==false){
	$blnError = true;
	$errMsg = "Error: Failed to create Block_Destination object.";
}

$unassigned_087did = new Unassigned_087DID();
if($blnError == false && is_object($unassigned_087did)==false){
	$blnError = true;
	$errMsg = "Error: Failed to create Unassigned_087DID object.";
}

$unassigned_did = new Unassigned_DID();
if($blnError == false && is_object($unassigned_did)==false){
	$blnError = true;
	$errMsg = "Error: Failed to create Unassigned_DID object.";
}

$assigned_did = new Assigned_DID();
if($blnError == false && is_object($assigned_did)==false){
	$blnError = true;
	$errMsg = "Error: Failed to create Assigned_DID object.";
}

$voice_mail = new VoiceMail();
if($blnError == false && is_object($voice_mail)==false){
	$blnError = true;
	$errMsg = "Error: Failed to create VoiceMail object.";
}

$access = new Access();
if($blnError == false && is_object($access)==false){
	$blnError = true;
	$errMsg = "Error: Failed to create Access object.";
}

if($blnError == false && $block_destination->isDestinationBlocked($_POST['forwarding_number'])==true){
	//Number blocked
	$blnError = true;
	$errMsg = "Error: The forwarding number selected by you is currently blocked.";
}

$fwd_number = str_replace('+','',$_POST['forwarding_number']);
if($blnError == false && $unassigned_087did->isCallCostOK($fwd_number)!=true){
	$blnError = true;
	$errMsg = "Error: Your forwarding number is too expensive and not currently allowed..";
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: Sticky Number Server <'.ACCOUNT_CHANGE_EMAIL.'>' . "\r\n";
	mail("info@stickynumber.com.au","Over the cost limit forwarding number $fwd_number by User ID: ".$_SESSION['user_logins_id'],"User ID: ".$_SESSION['user_logins_id']." wanted to select over the cost limit forwarding number: ".$fwd_number. " and was denied the DID Number.",$headers);
}

$did_id = "";
$did = "";
if(isset($_POST["free_number_id"])==true && trim($_POST["free_number_id"])!=""){
	if($blnError == false){
		$did_id = trim($_POST["free_number_id"]);
		$did = $unassigned_did->getUnassigned_DIDbyID($did_id);
	}
}else{
	//Get an available a 070 Number
	if($blnError == false){
		//Check for maximum number of DIDs allowed in an account
		$users_dids = $assigned_did->getUserDIDsCount($_SESSION['user_logins_id'],true);
		$admin_limit=$assigned_did->getUserDIDsLimit($_SESSION['user_logins_id']);
		if($users_dids>=$admin_limit){
			$blnError = true;
			$errMsg = "Error: The Number of DIDs in your account is $users_dids and it exceeds/meets your allowed limit of $admin_limit. Please contact support.";
		}
		if($blnError == false){
			$arr_did = $unassigned_did->retUnassigned_DID();
			if(is_array($arr_did)==false){
				$blnError = true;
				$errMsg = $arr_did;
			}else{
				$did_id = $arr_id[0];
				$did = $arr_did[1];
			}
		}
	}
}

//Now reserve this did_number - if still available
if($blnError == false){
	$status = $unassigned_did->setReservedByDID($did);
	
	if($status){//If reservation is successful
		$assigned_did->user_id 		= $_SESSION['user_logins_id'];
		$assigned_did->did_number	= $did;
		$assigned_did->did_country_code = 44;
		$assigned_did->did_area_code = 70;
		$assigned_did->city_id		= 99999;
		$assigned_did->callerid_on	= 1;
		$assigned_did->ringtone		= -9;
		$assigned_did->route_number	= $fwd_number;
		$assigned_did->callerid_option = 1;
		$assigned_did->total_minutes	= 0;
		$assigned_did->balance = 0;//Free number no balance required
		//Now insert this Assigned DID record in the table
		$status = $assigned_did->insertDIDData();
		if(is_int($status)==false && substr($status,0,6)=="Error:"){
				$blnError = true;
				$errMsg = $status;//Set the error message from here
			}
		if($blnError==false && $status <= 0 ){
			$blnError = true;
			$errMsg = "Error: A DID Number could not be reserved for you. Please try again.";
		}
	}else{
		$blnError = true;
		$errMsg = "Error: A DID Number could not be reserved for you. Please try again.";
	}
}

//Now add a record to voicemail_users table
if($blnError == false){
	$status = $voice_mail->add_voicemail_user($did,$_SESSION['user_logins_id']);
}

//Now update access logs
if($blnError == false){
	$access->updateAccessLogs('Added DID:',false,$did);
}

//Now take the DID out of the unassigned list permanently
if($blnError == false){
	$unassigned_did->did_number = $did;
	$unassigned_did->destroyByDID_Number();
}

$status = dialplan_did_route("vssp", $did, $fwd_number, $_SESSION['user_logins_id']);
if($status!=true){
	$blnError = true;
	$errMsg = "Error: Failed to create DID Route. Please try again.";
}

?>      
		  <script>
		  
            $(document).ready(function(){
            	$("#submit_form_news").click(function(){
            		var emailid = document.getElementById("email_news").value;
            		var submit_url = "./mcapi/inc/store-address.php?ajax=1&email="+emailid;
            		  $.ajax({url:submit_url, success:function(result){
            		    alert(result);
            		  }});
            		});
            });
			           
          </script>
          <!-- End SlidesJS Required -->
        <?php if(SITE_COUNTRY!="COM"){?>
        <!--Start of Zopim Live Chat Script-->
		<script type="text/javascript">
		window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
		d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
		_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
		$.src='//v2.zopim.com/?1piIDUavtFoOMB89h2pj9OK8cT8CswZK';z.t=+new Date;$.
		type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
		</script>
		<!--End of Zopim Live Chat Script-->
		<?php } ?>
		
		
</head>
<body>
	<?php 
	include 'login_top_page.php';
  
	include 'top_nav_page.php';?>

	<div class="container">
    <img width="1170" height="230" class="img-responsive" alt="Our Pricing" src="<?php echo SITE_URL; ?>images/SF-pricing.jpg">
    </div><!-- /.container -->
	
    
    <!-- Main wrapper starts here -->
    <div class="wrapper">
         <div class="container"> 
           		
    		<div class="plan-box">
           		<div class="alert-box alert-box-g centeredImage">
           			
					<?php session_destroy();?>
           			<?php if($blnError==false){//When there is no error ?>
						<h1 style="color:red">Verify Email Address</h1><br>
           				<h3>We have sent an email to your inbox, once you click on the link and confirm you email address your number will be added to your account</h3>
                        	<br/>
                        	
                        	 
                        	<meta http-equiv="refresh" content="6;URL=index.php" />
           			<?php }else{ //If there is any error?>
	         		<?php 	echo "<font color='red'>".$errMsg."</font><script>alert('".$errMsg."')</script>"; ?>
	         		<?php }//End of else part of error check?>

					<p>Contact us 24/7 About your order or if you have any other enquires.
					Please Check your email for order confirmation and Invoice</p>
				</div>
           </div><!-- .Plan Box -->
    		</div>
    	</div>
		
		<?php include('inc_footer_page.php');?>
		
    </body>
</html>
	