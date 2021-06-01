<?php 
	include '../database/dbconnection.php';
	include '../Validations/student_input_validate.php';

	if(empty($_REQUEST['sr_code'])) {
		showError();
	    exit();
	}
	
	$sr_code 		= $_REQUEST['sr_code'];
	$password 		= $_REQUEST['password'];

	// Validating Inputs
	$validate_inputs = new StudentValidate();
	$validate_inputs->setChangePasswordInfo($sr_code, $password);
	if($validate_inputs->validate()){
		// Alert if Inputs are Invalid
		echo json_encode(array(
			"alert" => "error",
			"title" => "Input Error!",
			"message" => $validate_inputs->getErrorMessage(),
			'error' =>  $validate_inputs->getErrorKey()
		));
		exit();
	}

	$sr_code 		= $mysqli->real_escape_string($_REQUEST['sr_code']);
	$password 		= $mysqli->real_escape_string($_REQUEST['password']);


	// Changing Student Password
	$sql = $mysqli->query("
		UPDATE `user` 
		SET `password` = '$password'
		WHERE `user`.`User_ID` = (
				SELECT us.User_ID 
				FROM user_student us 
				WHERE us.SR_Code = '$sr_code'
			)
	");
	
	if($sql){
		if ($mysqli->affected_rows > 0) {
			// Alert Success if updated
			echo json_encode(array(
				"alert" => "success",
				"title" => "Password Succesfully Updated!",
				"message" => "Student \"$sr_code\" Password Succesfully Updated",
				"id" => "$sr_code"
			));
			exit();
		}
		// Alert if Nothing Changed
		echo json_encode(array(
			"alert" => "info",
			"title" => "Nothing Changed!",
			"message" => "Nothing has been changed"
		));
		exit();
	}

	showError();
	exit();

	function showError(){
		// Alert if Failed to Change
		echo json_encode(array(
			"alert" => "error",
			"title" => "Failed to Update!",
			"message" => "Student Password Updating has been Failed"
		));
	}
?>