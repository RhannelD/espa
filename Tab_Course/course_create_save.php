<?php 
	include '../database/dbconnection.php';
	include '../Validations/course_input_validate.php';

	if(empty($_REQUEST['code'])) {
	    exit();
	}
	
	$id 			= null;		
	$code 			= $_REQUEST['code'];	
	$course_code 	= $code;			
	$title 			= $_REQUEST['title'];
	$unit 			= $_REQUEST['unit'];
	$lec 			= $_REQUEST['lec'];
	$lab 			= $_REQUEST['lab'];
	$req_standing 	= $_REQUEST['req_standing']; 
	$prereq_ids 	= $_REQUEST['prereq_id'];	// Array of Pre-requisites
	asort($prereq_ids);
	$created 		= false;

	// Validating Inputs
	$validate_inputs = new CourseValidate();
	$validate_inputs->setValues($code, $title, $unit, $lec, $lab, $req_standing);
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
	$unit 			= $mysqli->real_escape_string($_REQUEST['unit']);
	$lec 			= $mysqli->real_escape_string($_REQUEST['lec']);
	$lab 			= $mysqli->real_escape_string($_REQUEST['lab']);
	$req_standing 	= $mysqli->real_escape_string($_REQUEST['req_standing']); 

	// Creating Course info if there are changes
	$sql = $mysqli->query("
		INSERT INTO `courses` (`Course_ID`, `Course_Code`, `Course_Title`, `Units`, `Lecture`, `Laboratory`, `Req Standing`) 
		VALUES (NULL, '$code', '$title', '$unit', '$lec', '$lab', '$req_standing');
	");
	if ($mysqli->affected_rows <= 0) {
		exit();
	}	

	$id = $mysqli->insert_id;
	$created = true;

	// Check if their are prereq to be add
	if($prereq_ids[0]!=0){
		// Adding the new prereqs
		foreach ($prereq_ids as $prereqID) {
			// Adding the New Prereq
			$sql = $mysqli->query("
				INSERT INTO `pre_requisites` (`PreReq_ID`, `Course_ID`, `Pre_Requisite`) 
				VALUES (NULL, $id, $prereqID);
			");
		}
	}
	
	// Alert Success if updated
	if($created){
		$code = htmlspecialchars($code);
		echo json_encode(array(
			"alert" => "success",
			"title" => "Succesfully Created!",
			"message" => "Course \"$course_code\" Succesfully Created",
			"id" => "$id"
		));
		exit();
	}

	// Alert if Failed to Create
	echo json_encode(array(
		"alert" => "error",
		"title" => "Failed to Create!",
		"message" => "Course Creation has been Failed"
	));
?>