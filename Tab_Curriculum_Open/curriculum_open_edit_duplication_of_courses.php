<?php  
	include '../database/dbconnection.php';

	if(empty($_REQUEST['id'])) {
		exit();
	}
	$id 			= $_REQUEST['id'];  
	$curriculum_id 	= $_REQUEST['curriculum_id']; 	


	// check if Curriculums are existing
	$sql = $mysqli->query("
		SELECT True 
		FROM curriculum
		WHERE Curriculum_ID IN ('$id','$curriculum_id')
	");
	if (!$sql) {
		showError();
		exit();
	}
	if ($sql->num_rows != 2){
		echo json_encode(array(
			"alert" => "error",
			"title" => "Request error!",
			"message" => ""
		));
		exit();
	}


	// Check is there are any courses to be duplicated
	$sql = $mysqli->query("
		SELECT True FROM curriculum_courses WHERE Curriculum_ID = '$curriculum_id'
	");
	if (!$sql) {
		showError();
		exit();
	}
	if ($sql->num_rows == 0){
		echo json_encode(array(
			"alert" => "info",
			"title" => "Nothing to be Duplicate!",
			"message" => "Theres nothing to be duplicated from the selected Curriculum"
		));
		exit();
	}
	$num_of_courses = $sql->num_rows;
	$sql->free_result();


	// Deleting the Courses of the Current A
	$sql = $mysqli->query("
		DELETE FROM curriculum_courses WHERE Curriculum_ID = '$id'
	");
	if (!$sql) {
		echo json_encode(array(
			"alert" => "error",
			"title" => "Deleting Courses Failed!",
			"message" => "Deleting the current Curriculum courses has been failed"
		));
		exit();
	}


	// Duplicating the Selected Curriculum's Courses to the Current Curriculum
	$sql = $mysqli->query("
		INSERT INTO curriculum_courses (`CuCo_Code`, `Curriculum_ID`, `Course_ID`, `Year_Level`, `Semester`) 
		SELECT NULL, '$id', Course_ID, Year_Level, Semester 
			FROM curriculum_courses 
			WHERE Curriculum_ID = '$curriculum_id'
	");
	if (!$sql) {
		showError();
		exit();
	}
	if ($mysqli->affected_rows > 0){
		// Alert Success if updated
		echo json_encode(array(
			"alert" => "success",
			"title" => "Duplication executed Succesfully !",
			"message" => $mysqli->affected_rows."/$num_of_courses Courses has been duplicated to Curriculum"
		));
		exit();
	}
	

	function showError(){
		// Alert if Failed to Create
		echo json_encode(array(
			"alert" => "error",
			"title" => "Failed to Add!",
			"message" => "Adding Course has been Failed"
		));
	}
?>