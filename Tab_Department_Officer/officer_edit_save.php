<?php 
	include '../database/dbconnection.php';
	include '../Validations/officer_input_validate.php';

	if(empty($_REQUEST['id'])) {
		showError();
	    exit();
	}
	
	$id 			= $_REQUEST['id'];
	$firstname 		= $_REQUEST['firstname'];
	$lastname 		= $_REQUEST['lastname'];
	$gender 		= $_REQUEST['gender'];


	// Validating Inputs
	$validate_inputs = new OfficerValidate();
	$validate_inputs->setOfficerInfo($id, $firstname, $lastname, $gender);
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
	$firstname 		= $mysqli->real_escape_string($_REQUEST['firstname']);
	$lastname 		= $mysqli->real_escape_string($_REQUEST['lastname']);
	$gender 		= $mysqli->real_escape_string($_REQUEST['gender']);


	// Creating Program info if there are changes
	$sql = $mysqli->query("
		UPDATE `user` 
		SET `Firstname` = '$firstname', 
			`Lastname` 	= '$lastname', 
			`Gender` 	= '$gender' 
		WHERE `user`.`User_ID` = '$id'
	");
	
	if($sql){
		if ($mysqli->affected_rows > 0) {
			// Alert Success if updated
			echo json_encode(array(
				"alert" => "success",
				"title" => "Succesfully Updated!",
				"message" => "Department Officer Profile Succesfully Updated",
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
		// Alert if Failed to Create
		echo json_encode(array(
			"alert" => "error",
			"title" => "Failed to Update!",
			"message" => "Department Officer Profile Updating has been Failed"
		));
	}
?>