<?php 
	include '../database/dbconnection.php';
	include '../Validations/curriculum_input_validate.php';

	if(empty($_REQUEST['id'])) {
	    exit();
	}

	$id 			= $_REQUEST['id'];	
	$track 			= $_REQUEST['track'];
	$references 	= $_REQUEST['references'];					// Array of references
	asort($references);
	$reference_c 	= implode(",",$references);					// Concatinated separeted by comma
	$reference_c 	= $mysqli->real_escape_string($reference_c);
	$reference_c_sq = [];
	foreach ($references as $key => $value) {
		$reference_c_sq[$key] = $mysqli->real_escape_string($value);
	}
	$reference_c_sq = "'" . implode("','",$reference_c_sq) . "'";	// Concatinated separeted by comma, and words enclosed by single quote
	$updated 		= false;

	// Get Curriculum info
	$sql = $mysqli->query("
		SELECT Program_ID, YEAR(Academic_Year) AS 'AcademicYear' FROM curriculum WHERE Curriculum_ID = $id
	");
	if (!$sql) {
		showError();
		exit();
	}
	while ($obj = $sql -> fetch_object()){
		$program = $obj->Program_ID;
		$academic_year = $obj->AcademicYear;
		break;
	}
	$sql->free_result();


	// Validating Inputs
	$validate_inputs = new CurriculumValidate();
	$validate_inputs->setValuesV2($track, $references);
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

	$track 			= $mysqli->real_escape_string($_REQUEST['track']);

	// if Track cannnot be empty
	if(empty($track)){
		$sql = $mysqli->query("
			SELECT Curriculum_ID FROM `curriculum` 
            WHERE Program_ID = $program
                AND Academic_Year LIKE '$academic_year' 
                AND Track NOT LIKE ''
                AND Curriculum_ID != $id
		");
		if (!$sql) {
			showError();
			exit();
		}
		while($sql->num_rows > 0){
			// Alert error if track cannot be empty
			echo json_encode(array(
				"alert" => "info",
				"title" => "Track is Empty!",
				"message" => "Please enter a track",
				'error' =>  'track'
			));
			exit();
		}
		$sql->free_result();
	}


	// if Curriculum already exist
	$sql = $mysqli->query("
		SELECT c.Curriculum_ID FROM curriculum c 
        WHERE c.Program_ID = $program
	        AND c.Academic_Year = $academic_year
	        AND c.Track LIKE '$track'
	        AND c.Curriculum_ID != $id
	    UNION ALL
	    SELECT c.Curriculum_ID FROM curriculum c 
        WHERE c.Program_ID = $program
	        AND c.Academic_Year = $academic_year
	        AND c.Track LIKE ''
	        AND c.Curriculum_ID != $id
	    LIMIT 1
	");
	if (!$sql) {
		showError();
		exit();
	}
	while($sql->num_rows > 0){
		// Alert Curriculum is Already Existing
		echo json_encode(array(
			"alert" => "info",
			"title" => "Already Exist!",
			"message" => "Curriculum is Already Existing"
		));
		exit();
	}
	$sql->free_result();


	// Update Curriculum
	$sql = $mysqli->query("
		UPDATE `curriculum` 
		SET `Track` = '$track' 
		WHERE `curriculum`.`Curriculum_ID` = $id
	");
	if (!$sql) {
		showError();
		exit();
	}
	if ($mysqli->affected_rows > 0) {
		$updated = true;
	}	

	// Check if Had the same References
	$sql = $mysqli->query("
		SELECT (
		    SELECT GROUP_CONCAT(Reference)
			FROM curriculum_references 
			WHERE Curriculum_ID = $id
		    ORDER BY Reference
		) = '$reference_c' AS Same
	");
	if (!$sql) {
		showError();
		exit();
	}
	while ($obj = $sql -> fetch_object()){
		$same_references = $obj->Same;
	}
	$sql->free_result();

	if(!$same_references){
		// Deleting all removed references
		$sql = $mysqli->query("
			DELETE FROM curriculum_references 
            WHERE Curriculum_ID = $id 
                AND Reference NOT IN ($reference_c_sq)
		");	
		if(!$sql) {
			showError();
			exit();
		}
		if($mysqli->affected_rows > 0)
			$updated = true;
	
		// Creating Curriculum References
		foreach ($references as $obj) {
			// Continue/Pass if reference is existing
			$obj =  $mysqli->real_escape_string($obj);
			$sql = $mysqli->query("
				SELECT Reference_ID FROM curriculum_references 
				WHERE Curriculum_ID = $id 
					AND Reference = '$obj'
			");
			if(!$sql) {
				showError();
				exit();
			}
			if($sql->num_rows > 0)
				continue;

			// Adding the Reference
			$sql = $mysqli->query("
				INSERT INTO `curriculum_references` (`Reference_ID`, `Curriculum_ID`, `Reference`) 
				VALUES (NULL, '$id', '$obj')
			");
			if (!$sql) {
				showError();
				exit();
			}
			if($mysqli->affected_rows > 0)
				$updated = true;
		}
	}
	if ($updated ) {
		// Alert Success if updated
		echo json_encode(array(
			"alert" => "success",
			"title" => "Succesfully Created!",
			"message" => "Curriculum \"$program\" Succesfully Created",
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


	function showError(){
		// Alert if Failed to Create
		echo json_encode(array(
			"alert" => "error",
			"title" => "Failed to Create!",
			"message" => "Program Creation has been Failed"
		));
	}
?>