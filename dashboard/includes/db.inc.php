<?php 
$conn = mysql_connect(SQLHOST, SQLUSER, SQLPASS) or die("Database ".SQLHOST." Connection Error!!");
mysql_select_db(SQLDB, $conn) or die("Database not found!!");
?>