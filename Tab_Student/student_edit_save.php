<?php 
	include '../database/dbconnection.php';
	include '../Validations/student_input_validate.php';

	if(empty($_REQUEST['sr_code'])) {
		showError();
	    exit();
	}
	
	$sr_code 		= $_REQUEST['sr_code'];
	$firstname 		= $_REQUEST['firstname'];
	$lastname 		= $_REQUEST['lastname'];
	$gender 		= $_REQUEST['gender'];

	// Validating Inputs
	$validate_inputs = new StudentValidate();
	$validate_inputs->setStudentInfo($sr_code, $firstname, $lastname, $gender);
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
	$firstname 		= $mysqli->real_escape_string($_REQUEST['firstname']);
	$lastname 		= $mysqli->real_escape_string($_REQUEST['lastname']);
	$email 			= $mysqli->real_escape_string($_REQUEST['email']);
	$gender 		= $mysqli->real_escape_string($_REQUEST['gender']);


	// Creating Program info if there are changes
	$sql = $mysqli->query("
		UPDATE `user` 
		SET `Firstname` = '$firstname', 
			`Lastname` = '$lastname', 
		    `Gender` = '$gender',
		    `email` = '$email'  
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
				"title" => "Succesfully Updated!",
				"message" => "Student Profile \"$sr_code\" Succesfully Updated",
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
		// Alert if Failed to Create
		echo json_encode(array(
			"alert" => "error",
			"title" => "Failed to Update!",
			"message" => "Student Profile Updating has been Failed"
		));
	}
?>