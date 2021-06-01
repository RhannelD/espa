<?php 
	include '../database/dbconnection.php';
	include '../Validations/curriculum_input_validate.php';

	if(empty($_REQUEST['department'])) {
	    exit();
	}

	$id 			= null;			
	$department 	= $_REQUEST['department'];			
	$program 		= $_REQUEST['program'];
	$code 			= $_REQUEST['code'];
	$track 			= $_REQUEST['track'];
	$academic_year 	= $_REQUEST['academic_year'];
	$references 	= $_REQUEST['references'];

	// Validating Inputs
	$validate_inputs = new CurriculumValidate();
	$validate_inputs->setValues($department, $program, $track, $academic_year, $references);
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
		
	$program 		= $mysqli->real_escape_string($_REQUEST['program']);
	$track 			= $mysqli->real_escape_string($_REQUEST['track']);
	$academic_year 	= $mysqli->real_escape_string($_REQUEST['academic_year']);

	// if Track cannnot be empty
	if(empty($track)){
		$sql = $mysqli->query("
			SELECT * FROM `curriculum` 
            WHERE Program_ID = $program
                AND Academic_Year LIKE '$academic_year' 
                AND Track NOT LIKE ''
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
	}

	// if Curriculum already exist
	$sql = $mysqli->query("
		SELECT c.Curriculum_ID FROM curriculum c 
        WHERE c.Program_ID = $program
	        AND c.Academic_Year = $academic_year
	        AND Track LIKE '$track'
	    UNION ALL
	    SELECT c.Curriculum_ID FROM curriculum c 
        WHERE c.Program_ID = $program
	        AND c.Academic_Year = $academic_year
	        AND Track LIKE ''
	    LIMIT 1
	");
	if (!$sql) {
		showError();
		exit();
	}
	if($sql->num_rows > 0){
		// Alert Curriculum is Already Existing
		echo json_encode(array(
			"alert" => "info",
			"title" => "Already Exist!",
			"message" => "Curriculum is Already Existing"
		));
		exit();
	}

	// Creating Curriculum
	$sql = $mysqli->query("
		INSERT INTO `curriculum` (`Curriculum_ID`, `Program_ID`, `Track`, `Academic_Year`) 
		VALUES (NULL, '$program', '$track', '$academic_year')
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

	// Creating Curriculum References
	foreach ($references as $reference) {
		// Adding the Reference
		$ref = $mysqli->real_escape_string($reference);
		$sql = $mysqli->query("
			INSERT INTO `curriculum_references` (`Reference_ID`, `Curriculum_ID`, `Reference`) 
			VALUES (NULL, '$id', '$ref')
		");
		if (!$sql) {
			showError();
			exit();
		}
	}

	// Alert Success if updated
	echo json_encode(array(
		"alert" => "success",
		"title" => "Succesfully Created!",
		"message" => "Curriculum \"$code\" Succesfully Created",
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