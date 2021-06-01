<?php  
	include "../database/dbconnection.php";

	if(empty($_REQUEST['id']) || empty($_REQUEST['code'])){	
		exit();
	}

	$CourseID = $_REQUEST['id'];
	$CourseCode = $_REQUEST['code'];

	$validate = ifConnectedToOtherRecords($mysqli, $CourseID);

	if(is_string($validate)){
		echo json_encode(array(
			"alert" => "info",
			"title" => "Course cannot be Deleted!",
			"message" => "Course \"$CourseCode\" is Connected to a Curriculum"
		));
		exit();
	}

	if(deletingCourse($mysqli, $CourseID)) {	
		echo json_encode(array(
			"alert" => "success",
			"title" => "Course Deleted!",
			"message" => "Course \"$CourseCode\" Deleted Successfully"
		));
		exit();
	}

	echo json_encode(array(
		"alert" => "error",
		"title" => "Course cannot be Deleted!",
		"message" => "Course \"$CourseCode\" Can't Deleted"
	));
	
	function deletingCourse($mysqli, $CourseID){
		$sql = $mysqli->query("
			DELETE FROM `pre_requisites` WHERE `pre_requisites`.`Course_ID` = $CourseID OR `pre_requisites`.`Pre_Requisite`=$CourseID
		");
		$sql = $mysqli->query("
			DELETE FROM `courses` WHERE `courses`.`Course_ID` = $CourseID
		");
		return $mysqli->affected_rows >= 1;
	}

	// Validations
	function ifConnectedToOtherRecords($mysqli, $CourseID){
		$sql = $mysqli->query("
			SELECT 'Curriculum' AS Result FROM `curriculum_courses` WHERE Course_ID = $CourseID UNION ALL
			SELECT 'Grades' AS Result FROM `grades` WHERE Course_ID = $CourseID LIMIT 1
		");
		if ($result = $sql) {
			while ($obj = $result -> fetch_object()){
	  			return $obj->Result;
	  		}
	  		return false;
		}
		return null;
	}
?>