<?php  
	include '../database/dbconnection.php';

	if(!isset($_REQUEST['username'])) {
	    exit();
	}

	$username = $_REQUEST['username'];

	// Verify Login
	$sql = $mysqli->query("
		SELECT u.User_ID, CONCAT(u.Firstname,' ',u.Lastname) AS Username
		FROM user u
        	LEFT JOIN user_student us ON u.User_ID=us.User_ID
		WHERE u.User_ID LIKE '$username' 
		    OR CONCAT(u.Firstname,' ',u.Lastname) LIKE '$username'
		    OR u.Email = '$username'
            OR us.SR_Code = '$username'
	");
	if (!$sql) {
		// Alert if Failed to Verify
		echo json_encode(array(
			"alert" => "error",
			"title" => "Couldn't Process!",
			"message" => "Please try again later"
		));
		exit();
	}

	while ($obj = $sql -> fetch_object()) {
		// Going to Main
		echo json_encode(array(
			"alert" => "success",
			"id" => $obj->User_ID,
			"username" => $obj->Username
		));
		exit();
	}

	// Alert if Failed to Verify
	echo json_encode(array(
		"alert" => "error",
		"title" => "Account not found!",
		"message" => "Couldn't find the Username/ID"
	));
	exit();
?>