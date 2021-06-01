<?php  
	include '../database/dbconnection.php';
	include '../database/UserAuth.php';
	include '../Validations/login_input_validate.php';

	if(!isset($_REQUEST['id']) || !isset($_REQUEST['password_new'])) {
	    exit();
	}

	$password 			= $_REQUEST['password'];
	$new_password 		= $_REQUEST['password_new'];
	$retype_password 	= $_REQUEST['password_retype'];

	// Verify if new and retype password is the same
	if ($new_password != $retype_password){
		echo json_encode(array(
			"alert" => "error",
			"title" => "Input error!",
			"message" => "New and Retype Password is not the same",
			"error" => ".change_password_new, .change_password_retype"
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

	$id 				= $mysqli->real_escape_string($_REQUEST['id']);
	$password 			= $mysqli->real_escape_string($_REQUEST['password']);
	$new_password 		= $mysqli->real_escape_string($_REQUEST['password_new']);

	// Verify if the password
	$sql = $mysqli->query("
		SELECT True FROM user 
		WHERE User_ID = $id 
			AND Password = '$password'
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
	if ($sql->num_rows == 0) {
		// Incorrect Password
		echo json_encode(array(
			"alert" => "error",
			"title" => "Password error!",
			"message" => "You entered a wrong Password",
			"error" => ".change_password"
		));
		exit();
	}
	$sql -> free_result();


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
		echo json_encode(array(
			"alert" => "success",
			"title" => "Password Updated!",
			"message" => "Updating password complete"
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