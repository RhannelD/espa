<?php 
	include '../database/dbconnection.php';
	include '../Validations/department_input_validate.php';

	if(empty($_REQUEST['id'])) {
	    exit();
	}
	
	$id 			= $_REQUEST['id'];			
	$code 			= $_REQUEST['code'];
	$department_code= $code;		
	$title 			= $_REQUEST['title'];
	$selected_dean 	= $_REQUEST['selected_dean'];
	$selected_head 	= $_REQUEST['selected_head'];
	$dean_name 		= $_REQUEST['dean_name'];
	$dean_gender 	= $_REQUEST['dean_gender'];
	$head_name 		= $_REQUEST['head_name'];
	$head_gender 	= $_REQUEST['head_gender'];
	if($selected_dean!='New'){
		$dean_name 	= null;
		$dean_gender= null;
	}
	if($selected_head!='New') {
		$head_name 	= null;
		$head_gender= null;
	}

	// Validating Inputs
	$validate_inputs = new DepartmentValidate();
	$validate_inputs->setValues($code, $title, $dean_name, $dean_gender, $head_name, $head_gender, null);
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
	$dean_name 		= $mysqli->real_escape_string($_REQUEST['dean_name']);
	$dean_gender 	= $mysqli->real_escape_string($_REQUEST['dean_gender']);
	$head_name 		= $mysqli->real_escape_string($_REQUEST['head_name']);
	$head_gender 	= $mysqli->real_escape_string($_REQUEST['head_gender']);

	// Creation of New Dean
	if($selected_dean=='New'){
		$sql = $mysqli->query("
			INSERT INTO `department_dean` (`Dean_ID`, `Name`, `Gender`) 
			VALUES (NULL, '$dean_name', '$dean_gender')
		");
		if(!$sql) {
			showError();
			exit();
		}
		if($mysqli->affected_rows <= 0){
			showError();
			exit();
		}
		$selected_dean = $mysqli->insert_id;
	}

	// Creation of New Head
	if($selected_head=='New'){
		$sql = $mysqli->query("
			INSERT INTO `department_head` (`Head_ID`, `Name`, `Gender`) 
			VALUES (NULL, '$head_name', '$head_gender')
		");
		if(!$sql) {
			showError();
			exit();
		}
		if($mysqli->affected_rows <= 0){
			showError();
			exit();
		}
		$selected_head = $mysqli->insert_id;
	}

	// Creating Course info if there are changes
	$sql = $mysqli->query("
		UPDATE `department` 
		SET `Department_Code` = '$code', 
			`Department_Title` = '$title', 
			`Dean_ID` = '$selected_dean', 
			`DeptHead_ID` = '$selected_head' 
		WHERE `department`.`Department_ID` = $id
	");
	if($sql) {
		if ($mysqli->affected_rows > 0) {
			// Alert Success if updated
			echo json_encode(array(
				"alert" => "success",
				"title" => "Succesfully Updated!",
				"message" => "Department \"$department_code\" Succesfully Updated",
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
			"message" => "Department Updating has been Failed"
		));
	}
?>