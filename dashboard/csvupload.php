<?php 
//check session and login before proceeding

$up_id = uniqid();
if(isset($_POST['submit']))
{    
	 $fname = $_FILES['dcm_csv_file']['name'];
     $chk_ext = explode(".",$fname);
     if(strtolower($chk_ext[1]) == "csv")
     {
         $filename = $_FILES['dcm_csv_file']['tmp_name'];
		 $target_path = "_uploads/";
		 $target_path = $target_path . basename( $_FILES['dcm_csv_file']['name']);
		 if(move_uploaded_file($_FILES['dcm_csv_file']['tmp_name'], $target_path))
		 {   
			$redirect = "success";
			echo "Your file was successfully uploaded.<br><br>Please wait... while you are being redirected...";
		 }
		 else
		 {
		    $redirect = "notsaved";
		    echo "ERROR: The file upload was NOT successful. Please try again.<br><br>If the problem persists please contact technical support.";
		 }
     }
     else
     {
         $redirect = "invaid";
         echo "ERROR: INVALID file Type. Only CSV files are accepted.";
		 
     }
}else{
	echo "Not posted!";
	exit;
}
?>
<html>
<head>
<script src="../scripts/jquery.js" type="text/javascript"></script>
<script type="text/javascript">
var su= '<?php if (isset($redirect) && $redirect=="success") {  echo "success";  } else { echo 'no';} ?>' ;
if(su=='success')
{
//Separation of file upload from saving data of the file in the database table. The user/admin do NOT have to wait
//while data is being parsed in the uploaded file and then saved in the database.
$.ajax({
  url: "csvsave.php?target=<?php echo $target_path;?>"
 });
setTimeout(delayRedirect,1000);
}

function delayRedirect(){
	window.location="admin_dest_costs_mgr.php?up_id=<?php echo $up_id;?>";
}
</script>
</head>
<body>
</body>
</html>
