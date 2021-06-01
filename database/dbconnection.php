<?php

	$db_servername	= "localhost";
	$db_username 	= "id16369886_rhannel"; 
	$db_password 	= "o9NHYX>cn6{2!BgU";
	$db_name 	= "id16369886_electronic_student_program_adviser";
	$database 	= "electronic_student_program_adviser";

	if($_SERVER['HTTP_HOST'] == 'localhost'){	
		$db_servername	= "localhost";
		$db_username 	= "root";
		$db_password 	= "";
		$db_name 	= "electronic_student_program_adviser";
	}

	$mysqli = new mysqli($db_servername, $db_username, $db_password, $db_name);
	if ($mysqli->connect_errno) {
		die("Connection failed: " . $mysqli->connect_error);
	    exit();
	}

?>