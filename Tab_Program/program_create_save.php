<?php 
	include '../database/dbconnection.php';
	include '../Validations/program_input_validate.php';

	if(empty($_REQUEST['code'])) {
	    exit();
	}
	
	$id 			= null;
	$code 			= $_REQUEST['code'];
	$program_code 	= $code;
	$title 			= $_REQUEST['title'];
	$department 	= $_REQUEST['department'];

	// Validating Inputs
	$validate_inputs = new ProgramValidate();
	$validate_inputs->setValues($code, $title, $department);
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

	$code 			= $mysqli->real_escape_string($_REQUEST['code']);			
	$title 			= $mysqli->real_escape_string($_REQUEST['title']);
	$department 	= $mysqli->real_escape_string($_REQUEST['department']);

	// Creating Program info if there are changes
	$sql = $mysqli->query("
		INSERT INTO `program` (`Program_ID`, `Program_Code`, `Program_Title`, `Department_ID`) 
		VALUES (NULL, '$code', '$title', '$department')
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

	// Alert Success if updated
	echo json_encode(array(
		"alert" => "success",
		"title" => "Succesfully Created!",
		"message" => "Program \"$program_code\" Succesfully Created",
		"id" => "$id"
	));
	exit();

	function showError(){
		// Alert if Failed to Create
		echo json_encode(array(
			"alert" => "error",
			"title" => "Failed to Create!",
			"message" => "Program Creation has been Failed"
		));
	}
?>