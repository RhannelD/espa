<?php  
	include '../database/dbconnection.php';
	include '../database/UserAuth.php';
	include '../Validations/login_input_validate.php';

	if(!isset($_REQUEST['id']) || !isset($_REQUEST['new_password'])) {
	    exit();
	}

	$id = $_REQUEST['id'];
	$new_password 		= $_REQUEST['new_password'];
	$retype_password 	= $_REQUEST['retype_password'];

	// Verify if new and retype password is the same
	if ($new_password != $retype_password){
		echo json_encode(array(
			"alert" => "error",
			"title" => "Input error!",
			"message" => "New and Retype Password is not the same"
		));
		exit();
	}

	// Validating Inputs
	$validate_inputs = new LoginValidate();
	$validate_inputs->setPassword($new_password);
	if($validate_inputs->validate()){
		// Alert if Inputs are Invalid
		echo json_encode(array(
			"alert" => "error",
			"title" => "Input Error!",
			"message" => $validate_inputs->getErrorMessage(),
			'error' =>  ".change_password_new, .change_password_retype"
		));
		exit();
	}

	$new_password 		= $mysqli->real_escape_string($_REQUEST['new_password']);
	$retype_password 	= $mysqli->real_escape_string($_REQUEST['retype_password']);

	// Verify if the same password
	$sql = $mysqli->query("
		SELECT True FROM user 
		WHERE User_ID = $id 
			AND Password = '$new_password'
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
			"alert" => "error",
			"title" => "Password error!",
			"message" => "You entered your current Password\nPlease enter a new password.",
			"error" => ".change_password_new, .change_password_retype"
		));
		exit();
	}
	$sql -> free_result();

	// Changing you current password
	$sql = $mysqli->query("
		UPDATE `user` 
		SET `Password` = '$new_password' 
		WHERE `user`.`User_ID` = $id
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

	if ($mysqli->affected_rows > 0) {
		// Changing Password complete
		// Getting the User info
		$user = $mysqli->query("
			SELECT User_ID, User_Type, CONCAT(Firstname,' ',Lastname) AS Username
			FROM user 
	        WHERE User_ID = $id
		");
		while ($obj = $user -> fetch_object()) {
			session_start();
			$UserAuth = new UserAuth($obj->User_ID, $obj->Username, $obj->User_Type);

			$_SESSION['UserAuth'] = serialize($UserAuth);
			$user -> free_result();

			// Going to Main
			echo json_encode(array(
				"alert" => "success",
				"panel" => "../main/"
			));
			exit();
		}
	}

	// Alert if Failed to Verify
	echo json_encode(array(
		"alert" => "error",
		"title" => "Wrong Answer",
		"message" => "Account verification failed!"
	));
	exit();
?>