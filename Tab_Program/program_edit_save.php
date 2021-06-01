<?php 
	include '../database/dbconnection.php';
	include '../Validations/program_input_validate.php';

	if(empty($_REQUEST['id'])) {
	    exit();
	}
	
	$id 			= $_REQUEST['id'];			
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
	
	// Editing Program info if there are changes
	$sql = $mysqli->query("
		UPDATE `program` 
		SET `Program_Code` = '$code', 
			`Program_Title` = '$title', 
			`Department_ID` = '$department' 
		WHERE `program`.`Program_ID` = $id
	");

	if($sql){
		if ($mysqli->affected_rows > 0) {
			// Alert Success if updated
			echo json_encode(array(
				"alert" => "success",
				"title" => "Succesfully Updated!",
				"message" => "Program \"$program_code\" Succesfully Updated"
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
	// Alert if Failed to SQL Update
	echo json_encode(array(
		"alert" => "error",
		"title" => "Failed to Update!",
		"message" => "Program Updating has been Failed"
	));
?>