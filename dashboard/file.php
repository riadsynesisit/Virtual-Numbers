<?php

require "./includes/DRY_setup.inc.php";	
		
if($_SESSION['is_authenticated']==true){

	$user_id = $_SESSION['user_logins_id'];
	
	$voice_mail   	=   new VoiceMail();
	
	//Check whether or not the message belongs to the logged in user
	//Prevent unauthorized access to the voice messages
	
	if(isset($_POST["vm_id"])){
		$is_vm_owner = $voice_mail->checkMsgEntitlements($_POST["vm_id"],$user_id);
		if($is_vm_owner==true){
			$result = $voice_mail->setAsRead($_POST["vm_id"],$user_id);
			echo $result;
		}
	}else{
	
		$is_vm_owner = $voice_mail->checkMsgEntitlements($_GET['fileid'],$user_id);
		if($is_vm_owner==true){
			$file			=	$voice_mail->get_file($_GET['fileid'],$_GET['type']);
			$filesize		=	$voice_mail->get_filesize($_GET['fileid'],$_GET['type']);
					
			if($_GET['type']=="mp3"){
				header("Content-type: audio/mpeg");
			}else{
				header("Content-type: audio/".$_GET['type']);
			}
			//header("Content-Disposition: attachment; filename=msg0000.".$_GET['type']);
			header("Content-Length: $filesize");
			        
			echo $file;
		}//end of if is_vm_owner
	}

}
		
?>