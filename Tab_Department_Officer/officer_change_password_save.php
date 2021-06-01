<?php 
	include '../database/dbconnection.php';
	include '../Validations/officer_input_validate.php';

	if(empty($_REQUEST['id'])) {
		showError();
	    exit();
	}
	
	$id 			= $_REQUEST['id'];
	$password 		= $_REQUEST['password'];

	// Validating Inputs
	$validate_inputs = new OfficerValidate();
	$validate_inputs->setChangePasswordInfo($id, $password);
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

	$id 			= $mysqli->real_escape_string($_REQUEST['id']);
	$password 		= $mysqli->real_escape_string($_REQUEST['password']);


	// Changing Officer Password
	$sql = $mysqli->query("
		UPDATE `user` SET `Password` = '$password' 
		WHERE `user`.`User_ID` = '$id'
	");
	
	if($sql){
		if ($mysqli->affected_rows > 0) {
			// Alert Success if updated
			echo json_encode(array(
				"alert" => "success",
				"title" => "Password Succesfully Updated!",
				"message" => "Department Officer Password Succesfully Updated",
				"id" => "$id"
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
			"message" => "Department Officer Password Updating has been Failed"
		));
	}
?>