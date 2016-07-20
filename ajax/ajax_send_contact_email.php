<?php

if(isset($_POST["sender_name"])==false || trim($_POST["sender_name"])==""){
    echo "Error: Sender Name not entered.";
    exit;
}else{
	$sender_name = trim($_POST["sender_name"]);
}

if(isset($_POST["sender_email"])==false || trim($_POST["sender_email"])==""){
    echo "Error: Sender Email not entered.";
    exit;
}else{
	$sender_email = trim($_POST["sender_email"]);
}

if(isset($_POST["sender_message"])==false || trim($_POST["sender_message"])==""){
    echo "Error: Message not entered.";
    exit;
}else{
	$sender_message = trim($_POST["sender_message"]);
}

$to = "support@stickynumber.com.au";
//$to = "steve.s@stickynumber.com";
$subject = "Message received on contact us page";
$message ="Sender Name: ".$sender_name;
$message .="\n\nSender Email: ".$sender_email;
$message .="\n\nSender Message: ".$sender_message;

$headers = "From:info@stickynumber.com.au"."\r\n";
$headers .= "Reply-To:" . $sender_email."\r\n";
$headers .= "CC:steve.s@stickynumber.com, riadul.i@stickynumber.com \r\n";

$status = mail($to,$subject,$message,$headers);
if($status==true){
	echo "success";
}else{
	echo "Error: Failed to send your message. Please try again.";
}
exit;
?>