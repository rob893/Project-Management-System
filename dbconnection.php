<?php
    
    $servername="localhost";
    $user="SWE6633member";
    $password="KSUadmin1!";
    $database="SWE6633";
    $conn = new mysqli($servername, $user, $password, $database);
    if($conn->connect_error){
        die("Connection failed: " .$conn->connect_error);
    } /*else {
		echo 'Connection successful.';
	}*/
?>
