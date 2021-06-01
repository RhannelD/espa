<?php 
	include '../database/dbconnection.php';
	include '../Validations/officer_input_validate.php';

	if(empty($_REQUEST['id'])) {
		showError();
	    exit();
	}
	
	$id 			= $_REQUEST['id'];
	$department 	= $_REQUEST['department'];
	$officer 		= $_REQUEST['officer'];


	// Validating Inputs
	$validate_inputs = new OfficerValidate();
	$validate_inputs->setOfficerPosition($id, $department, $officer);
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
	$department 	= $mysqli->real_escape_string($_REQUEST['department']);
	$officer 		= $mysqli->real_escape_string($_REQUEST['officer']);


	// Creating Program info if there are changes
	$sql = $mysqli->query("
		UPDATE `user` 
		SET `User_Type` = '$officer' 
		WHERE `user`.`User_ID` = '$id'
	");
	if (!$sql) {
		showError();
		exit();
	}
	$affected_rows = 0;
	if ($mysqli->affected_rows > 0) {
		$affected_rows = $mysqli->affected_rows;
	}
	
	$sql = $mysqli->query("
		UPDATE `user_department` 
		SET `Department_ID` = '$department' 
		WHERE `user_department`.`User_ID` = '$id'
	");
	if($sql){
		if (($mysqli->affected_rows+$affected_rows) > 0) {
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