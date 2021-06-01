<?php 
	include '../database/dbconnection.php';
	include '../Validations/officer_input_validate.php';

	if(empty($_REQUEST['email'])) {
		showError();
	    exit();
	}
	
	$id 			= null;
	$firstname 		= $_REQUEST['firstname'];
	$lastname 		= $_REQUEST['lastname'];
	$gender 		= $_REQUEST['gender'];
	$email 			= $_REQUEST['email'];
	$department 	= $_REQUEST['department'];
	$officer 		= $_REQUEST['officer'];
	$password 		= $_REQUEST['password'];

	// Validating Inputs
	$validate_inputs = new OfficerValidate();
	$validate_inputs->setValues($firstname, $lastname, $gender, $email, $department, $officer, $password);
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

	$firstname 		= $mysqli->real_escape_string($_REQUEST['firstname']);
	$lastname 		= $mysqli->real_escape_string($_REQUEST['lastname']);
	$gender 		= $mysqli->real_escape_string($_REQUEST['gender']);
	$email 			= $mysqli->real_escape_string(strtolower($_REQUEST['email']));
	$department 	= $mysqli->real_escape_string($_REQUEST['department']);
	$officer 		= $mysqli->real_escape_string($_REQUEST['officer']);
	$password 		= $mysqli->real_escape_string($_REQUEST['password']);


	// Validate if SR code already exist
	$sql = $mysqli->query("
		SELECT 'Email' AS Result, 'email' AS Error FROM user WHERE Email = '$email' LIMIT 1
	");
	if (!$sql) {
		showError();
		exit();
	}
	if ($sql->num_rows > 0) {
		// Alert if Email exist
		while ($obj = $sql -> fetch_object()) {
			echo json_encode(array(
				"alert" => "error",
				"title" => "Email already exist!",
				"message" => "Entered Email is already existing from the records",
				'error' =>  $obj->Error
			));
		}
		exit();
	}	
	$sql->free_result();


	// Creating Officer User info
	$sql = $mysqli->query("
		INSERT INTO `user` (`User_ID`, `User_Type`, `Firstname`, `Lastname`, `Gender`, `Email`, `Password`) 
		VALUES (NULL, '$officer', '$firstname', '$lastname', '$gender', '$email', '$password')
	");
	if (!$sql) {
		showError();
		exit();
	}
	if ($mysqli->affected_rows <= 0) {
		showError();
		exit();
	}	

	$id = $mysqli->insert_id;

	// Creating Officer Department Info
	$sql = $mysqli->query("
		INSERT INTO `user_department` (`User_ID`, `Department_ID`) 
		VALUES ('$id', '$department')
	");
	if (!$sql) {
		showError();
		exit();
	}
	if ($mysqli->affected_rows <= 0) {
		showError();
		exit();
	}	

	// Alert Success if updated
	echo json_encode(array(
		"alert" => "success",
		"title" => "Succesfully Created!",
		"message" => "Department Officer Profile Succesfully Created",
		"id" => "$id"
	));
	exit();

	function showError(){
		// Alert if Failed to Create
		echo json_encode(array(
			"alert" => "error",
			"title" => "Failed to Create!",
			"message" => "Department Officer Profile Creation has been Failed"
		));
	}
?>