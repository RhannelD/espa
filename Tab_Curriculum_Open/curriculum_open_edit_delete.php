<?php  
	include "../database/dbconnection.php";

	if(empty($_REQUEST['id']) || empty($_REQUEST['code'])){	
		exit();
	}

	$CurriculumID 	= $_REQUEST['curriculum_id'];
	$CourseID 		= $_REQUEST['id'];
	$CourseCode 	= $_REQUEST['code'];
	$year 			= $_REQUEST['year'];
	$sem 			= $_REQUEST['sem'];

	$validate = ifCourseCoRequisiteIsExisting($mysqli, $CourseID, $CurriculumID, $year, $sem);

	if(is_string($validate)){
		echo json_encode(array(
			"alert" => "info",
			"title" => "Course cannot be Removed!",
			"message" => "Remove Course \"$CourseCode\" Co-Requisite/s First"
		));
		exit();
	}

	if(deletingCurriculumCourse($mysqli, $CourseID, $CurriculumID)) {	
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
	
	function deletingCurriculumCourse($mysqli, $CourseID, $CurriculumID){
		$sql = $mysqli->query("
			DELETE FROM curriculum_courses WHERE Course_ID = $CourseID  AND Curriculum_ID= $CurriculumID
		");
		return $mysqli->affected_rows >= 1;
	}

	// Validations
	function ifCourseCoRequisiteIsExisting($mysqli, $CourseID, $CurriculumID, $year, $sem){
		$sql = $mysqli->query("
			SELECT 'Curriculum' AS Result FROM curriculum_courses cc 
                INNER JOIN pre_requisites p ON cc.Course_ID = p.Course_ID 
            WHERE cc.Curriculum_ID = $CurriculumID 
                AND CONCAT(cc.Year_Level,cc.Semester)>'$year$sem' 
                AND p.Pre_Requisite = $CourseID 
            LIMIT 1
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