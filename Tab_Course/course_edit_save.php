<?php 
	include '../database/dbconnection.php';
	include '../Validations/course_input_validate.php';

	if(empty($_REQUEST['id'])) {
	    exit();
	}

	$id 			= $_REQUEST['id'];				
	$code 			= $_REQUEST['code'];		
	$course_code 	= $code;				
	$title 			= $_REQUEST['title'];
	$unit 			= $_REQUEST['unit'];
	$lec 			= $_REQUEST['lec'];
	$lab 			= $_REQUEST['lab'];
	$req_standing 	= $_REQUEST['req_standing']; 

	$prereq_ids = $_REQUEST['prereq_id'];	// Array of Pre-requisites
	asort($prereq_ids);
	$prereq_id = implode(",",$prereq_ids);	// String Concatinated of Pre-requisites

	$updated = false;

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

	if(!ifSamePrereqs($mysqli, $id, $prereq_id)){
		// Deleting all removed prereqs
		$sql = $mysqli->query("
			DELETE FROM pre_requisites 
			WHERE Course_ID = $id
				AND Pre_Requisite NOT IN ($prereq_id)
		");	
		if(!$sql) {
			showError();
			exit();
		}
		if($mysqli->affected_rows > 0)
			$updated = true;

		// Check if their are prereq to be add
		if($prereq_ids[0]!=0){
			// Adding the new prereqs
			foreach ($prereq_ids as $prereqID) {
				// Continue/Pass if course prereq is existing
				$sql = $mysqli->query("
					SELECT PreReq_ID FROM pre_requisites 
					WHERE Course_ID = $id
						AND Pre_Requisite = $prereqID
				");
				if(!$sql) {
					showError();
					exit();
				}
				if($sql->num_rows > 0)
					continue;
			
				// Adding the New Prereq
				$sql = $mysqli->query("
					INSERT INTO `pre_requisites` (`PreReq_ID`, `Course_ID`, `Pre_Requisite`) 
					VALUES (NULL, $id, $prereqID);
				");
				if(!$sql) {
					showError();
					exit();
				}
				if($mysqli->affected_rows > 0)
					$updated = true;
			}
		}
	}

	// Saving Course info if there are changes
	$sql = $mysqli->query("
		UPDATE `courses` 
		SET `Course_Code` = '$code', 
			`Course_Title` = '$title', 
			`Units` = $unit, 
			`Lecture` = $lec, 
			`Laboratory` = $lab, 
			`Req Standing` = '$req_standing' 
		WHERE `courses`.`Course_ID` = $id;
	");
	if(!$sql) {
		showError();
		exit();
	}
	if($mysqli->affected_rows > 0)
		$updated = true;

	// Alert Success if updated
	if($updated){
		echo json_encode(array(
			"alert" => "success",
			"title" => "Succesfully Updated!",
			"message" => "Course \"$course_code\" Succesfully Updated"
		));
		exit();
	}

	// Alert if Nothing Changed
	echo json_encode(array(
		"alert" => "info",
		"title" => "Nothing Changed!",
		"message" => "Nothing has been changed"
	));

	function showError(){
		// Alert if Failed to SQL Query
		echo json_encode(array(
			"alert" => "error",
			"title" => "Failed to Update!",
			"message" => "Program Updating has been Failed"
		));
	}

	// Checking if course prereqs has changed
	function ifSamePrereqs($mysqli, $id, $prereq_id){
		$sql = $mysqli->query("
			SELECT COALESCE(
						(
							SELECT GROUP_CONCAT(p.Pre_Requisite) 
							FROM pre_requisites p 
							WHERE p.Course_ID = $id 
							ORDER BY p.Pre_Requisite
						),'0'
					)='$prereq_id' AS 'ifSamePrereqs'
		");
		if ($result = $sql) {
			while ($obj = $result -> fetch_object()){
				return $obj->ifSamePrereqs;
			}
		}
		return false;
	}
?>