<?php 
	include '../database/dbconnection.php';
	include '../Validations/student_input_validate.php';

	if(empty($_REQUEST['sr_code'])) {
		showError();
	    exit();
	}
	
	$sr_code 		= $_REQUEST['sr_code'];
	$program 		= $_REQUEST['program'];
	$year 			= $_REQUEST['year'];
	$track 			= $_REQUEST['track'];

	// Validating Inputs
	$validate_inputs = new StudentValidate();
	$validate_inputs->setShiftingInfo($sr_code, $program, $year, $track);
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
	$curriculum 	= $mysqli->real_escape_string($_REQUEST['track']);


	// Creating Program info if there are changes
	$sql = $mysqli->query("
		UPDATE user_student 
		SET Curriculum_ID = '$curriculum' 
		WHERE SR_Code = '$sr_code'
	");
	
	if($sql){
		if ($mysqli->affected_rows > 0) {
			// Alert Success if Shifted
			echo json_encode(array(
				"alert" => "success",
				"title" => "Succesfully Shifted!",
				"message" => "Student \"$sr_code\" Program Succesfully Shifted",
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
		// Alert if Failed to Shift
		echo json_encode(array(
			"alert" => "error",
			"title" => "Failed to Shift Program!",
			"message" => "Student Program Shifting has been Failed"
		));
	}
?>