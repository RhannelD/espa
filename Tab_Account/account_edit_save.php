<?php 
	include '../database/dbconnection.php';
	include '../Validations/account_input_validate.php';

	if(empty($_REQUEST['id'])) {
	    exit();
	}
				
	$firstname 	= $_REQUEST['firstname'];
	$lastname 	= $_REQUEST['lastname'];
	$gender 	= $_REQUEST['gender'];

	// Validating Inputs
	$validate_inputs = new AccountValidate();
	$validate_inputs->setAccountInfo($firstname, $lastname, $gender);
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
			
	$id 		= $mysqli->real_escape_string($_REQUEST['id']);	
	$firstname 	= $mysqli->real_escape_string($_REQUEST['firstname']);
	$lastname 	= $mysqli->real_escape_string($_REQUEST['lastname']);
	$gender 	= $mysqli->real_escape_string($_REQUEST['gender']);
	
	// Editing Account info if there are changes
	$sql = $mysqli->query("
		UPDATE `user` 
		SET `Firstname` = '$firstname', 
			`Lastname` = '$lastname', 
			`Gender` = '$gender' 
		WHERE `user`.`User_ID` = '$id'
	");

	if($sql){
		if ($mysqli->affected_rows > 0) {
			// Alert Success if updated
			echo json_encode(array(
				"alert" => "success",
				"title" => "Succesfully Updated!",
				"message" => "Account has been Succesfully Updated",
				"data" => array(
					'firstname' => $firstname,
					'lastname' => $lastname,
					'gender' => $gender
				)
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
		"message" => "Account Updating has been Failed"
	));
?>