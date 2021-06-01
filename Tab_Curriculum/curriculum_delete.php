<?php  
	include "../database/dbconnection.php";

	if(empty($_REQUEST['id'])) {
		exit();
	}

	$id = $_REQUEST['id'];
	$code = $_REQUEST['code'];

	// Validations (Connected to any other records)
	$sql = $mysqli->query("
		SELECT 'Student' AS Result FROM user_student WHERE Curriculum_ID = $id 
		LIMIT 1
	");
	if (!$sql) {
		showError();
		exit();
	}
	while ($obj = $sql -> fetch_object()){
		// alert curriculum is connected to other records
		$sql -> free_result();
		echo json_encode(array(
			"alert" => "info",
			"title" => "Curriculum cannot be Deleted!",
			"message" => "Curriculum \"$code\" is Connected to a $obj->Result"
		));
		exit();
	}

	// Deleting Curriculum
	$count = 0;
	$sql = $mysqli->query("
		DELETE FROM `curriculum_courses` WHERE `curriculum_courses`.`Curriculum_ID`= $id
	");
	if (!$sql) {
		showError();
		exit();
	}

	$sql = $mysqli->query("
		DELETE FROM `curriculum_references` WHERE `curriculum_references`.`Curriculum_ID` = $id
	");
	if (!$sql) {
		showError();
		exit();
	}

	$sql = $mysqli->query("
		DELETE FROM `curriculum` WHERE `curriculum`.`Curriculum_ID` = $id
	");
	if (!$sql) {
		showError();
		exit();
	}
	if($mysqli->affected_rows <= 0){
		showError();
		exit();
	}


	echo json_encode(array(
		"alert" => "success",
		"title" => "Deleted Successfully!",
		"message" => "Curriculum \"$code\" Deleted Successfully"
	));
	exit();

	function showError(){
		echo json_encode(array(
			"alert" => "error",
			"title" => "Deletion Failed!",
			"message" => "Curriculum Failed to Deleted"
		));
	}
?>