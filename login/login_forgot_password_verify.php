<?php  
	include '../database/dbconnection.php';

	if(!isset($_REQUEST['id']) || !isset($_REQUEST['code'])) {
	    exit();
	}

	$id = $_REQUEST['id'];
	$code = $_REQUEST['code'];

	// Verify Answer
	$sql = $mysqli->query("
		SELECT True
		FROM recovery_code rc
		WHERE rc.User_ID = '$id'
			AND rc.Code_ID = (
		    	SELECT MAX(rc2.Code_ID)
		        FROM recovery_code rc2 
		        WHERE rc2.User_ID = rc.User_ID
		        LIMIT 1
		    )
		    AND rc.Code = '$code'
		LIMIT 1
	");
	if (!$sql) {
		// Alert if Failed to Verify
		echo json_encode(array(
			"alert" => "error",
			"title" => "Couldn't Process!",
			"message" => "Connection/Query Error"
		));
		exit();
	}

	if ($sql->num_rows > 0) {
		// Going to Main
		echo json_encode(array(
			"alert" => "success"
		));
		exit();
	}

	// Alert if Failed to Verify
	echo json_encode(array(
		"alert" => "error",
		"title" => "Wrong Answer",
		"message" => "Account verification failed!"
	));
	exit();
?>