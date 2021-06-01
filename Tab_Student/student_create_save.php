<?php 
	include '../database/dbconnection.php';
	include '../Validations/student_input_validate.php';

	if(empty($_REQUEST['sr_code'])) {
		showError();
	    exit();
	}
	
	$id 			= null;
	$sr_code 		= $_REQUEST['sr_code'];
	$firstname 		= $_REQUEST['firstname'];
	$lastname 		= $_REQUEST['lastname'];
	$gender 		= $_REQUEST['gender'];
	$email 			= $_REQUEST['email'];
	$password 		= $_REQUEST['password'];
	$program 		= $_REQUEST['program'];
	$year 			= $_REQUEST['year'];
	$track 			= $_REQUEST['track'];

	// Validating Inputs
	$validate_inputs = new StudentValidate();
	$validate_inputs->setValues($sr_code, $firstname, $lastname, $gender, $email, $program, $year, $track);
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
	$gender 		= $mysqli->real_escape_string($_REQUEST['gender']);
	$email 			= $mysqli->real_escape_string(strtolower($_REQUEST['email']));
	$password 		= $mysqli->real_escape_string($_REQUEST['password']);
	$curriculum 	= $mysqli->real_escape_string($_REQUEST['track']);


	// Validate if SR code already exist
	$sql = $mysqli->query("
		SELECT 'SR-Code' AS Result, 'sr_code' AS Error FROM user_student WHERE SR_Code = '$sr_code' UNION ALL
		SELECT 'Email' AS Result, 'email' AS Error FROM user WHERE Email = '$email' LIMIT 1
	");
	if (!$sql) {
		showError();
		exit();
	}
	if ($sql->num_rows > 0) {
		// Alert if Sr code exist
		while ($obj = $sql -> fetch_object()) {
			echo json_encode(array(
				"alert" => "error",
				"title" => "$obj->Result already exist!",
				"message" => "Student's $obj->Result is already existing from the records",
				'error' =>  $obj->Error
			));
		}
		exit();
	}	
	$sql->free_result();


	// Creating Student User info
	$sql = $mysqli->query("
		INSERT INTO `user` (`User_ID`, `User_Type`, `Firstname`, `Lastname`, `Gender`, `Email`, `Password`) 
		VALUES (NULL, 'STD', '$firstname', '$lastname', '$gender', '$email', '$password')
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

	// Creating Student Curriculum Info
	$sql = $mysqli->query("
		INSERT INTO `user_student` (`Student_ID`, `User_ID`, `Curriculum_ID`, `SR_Code`) 
		VALUES (NULL, '$id', '$curriculum', '$sr_code')
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
		"message" => "Student Profile \"$sr_code\" Succesfully Created",
		"id" => "$sr_code"
	));
	exit();

	function showError(){
		// Alert if Failed to Create
		echo json_encode(array(
			"alert" => "error",
			"title" => "Failed to Create!",
			"message" => "Student Profile Creation has been Failed"
		));
	}
?>