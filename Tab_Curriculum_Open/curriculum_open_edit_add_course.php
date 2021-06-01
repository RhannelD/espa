<?php  
	include '../database/dbconnection.php';

	if(empty($_REQUEST['id'])) {
		exit();
	}
	$id 			= $_REQUEST['id']; 
	$code 			= $_REQUEST['code']; 
	$curriculum_id 	= $_REQUEST['curriculum_id']; 
	$year 			= $_REQUEST['year']; 
	$sem 			= $_REQUEST['sem']; 


	// check if Course is already added
	$sql = $mysqli->query("
		SELECT cc.Curriculum_ID FROM curriculum_courses cc WHERE cc.Curriculum_ID = $curriculum_id AND cc.Course_ID = $id
	");
	if (!$sql) {
		showError();
		exit();
	}
	if ($sql->num_rows > 0){
		echo json_encode(array(
			"alert" => "info",
			"title" => "Already Exist!",
			"message" => "Course $code Already exist on Curriculum"
		));
		exit();
	}


	// check if Course has prereq/s (take another validation if true)
	$sql = $mysqli->query("
		SELECT PreReq_ID FROM `pre_requisites` WHERE Course_ID = $id
	");
	if (!$sql) {
		showError();
		exit();
	}
	if ($sql->num_rows > 0){
		// verify if Course pre-requisite/s has not yet added
		$sql = $mysqli->query("
			SELECT c.Curriculum_ID FROM curriculum c
                INNER JOIN curriculum_courses cc ON c.Curriculum_ID=cc.Curriculum_ID
        	WHERE c.Curriculum_ID = $curriculum_id 
                AND (SELECT ps.PreReq_ID FROM pre_requisites ps 
                        INNER JOIN curriculum c1 
                        LEFT JOIN curriculum_courses cc1 
                            ON ps.Pre_Requisite=cc1.Course_ID AND c1.Curriculum_ID=cc1.Curriculum_ID 
                    WHERE ps.Course_ID = $id
                        AND c1.Curriculum_ID = $curriculum_id 
                        AND cc1.CuCo_Code IS null 
                    GROUP BY ps.PreReq_ID
                    LIMIT 1) IS null 
                OR (SELECT ps.Pre_Requisite FROM courses cs 
                        INNER JOIN pre_requisites ps ON cs.Course_ID=ps.Course_ID 
                    WHERE cs.Course_ID = $id 
                    GROUP BY cs.Course_ID ) is null 
            GROUP BY c.Curriculum_ID 
		");
		if (!$sql) {
			showError();
			exit();
		}
		if ($sql->num_rows <= 0){
			// Alert Course is Already Existing 
			echo json_encode(array(
				"alert" => "info",
				"title" => "Unadded Pre-Requisite!",
				"message" => "Course $code cant be added!\nTake the Pre-Requisite First"
			));
			exit();
		}


		// Check if Course Pre-requisite is on the previous semesters
		$sql = $mysqli->query(" 
			SELECT p.Pre_Requisite FROM pre_requisites p 
            WHERE p.Course_ID = $id
                AND p.Pre_Requisite 
                NOT IN (SELECT cc.Course_ID FROM curriculum_courses cc 
                    WHERE cc.Curriculum_ID = $curriculum_id 
                        AND CONCAT(cc.Year_Level,cc.Semester)
                            <'".$year.$sem."') 
		");
		if (!$sql) {
			showError();
			exit();
		}
		if ($sql->num_rows > 0){
			// Alert All Course Pre-Requisite/s is not previous tables
			echo json_encode(array(
				"alert" => "error",
				"title" => "$code cant be added!",
				"message" => "Course Pre-Requisite/s doesn't exist on previous semesters table.\nTry Adding to the next semester table."
			));
			exit();
		}
	}

	$sql = $mysqli->query("
		INSERT INTO `curriculum_courses` (`CuCo_Code`, `Curriculum_ID`, `Course_ID`, `Year_Level`, `Semester`) 
        VALUES (NULL, '$curriculum_id', '$id', '$year', '$sem')
	");
	if (!$sql) {
		showError();
		exit();
	}
	if ($mysqli->affected_rows > 0){
		// Alert Success if updated
		echo json_encode(array(
			"alert" => "success",
			"title" => "Course Succesfully Added!",
			"message" => "Course \"$code\" Succesfully Added to the Curriculum",
			"id" => "$id"
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