<?php 
	include '../database/dbconnection.php';
	include '../Validations/student_grade_input_validate.php';
    include "../database/UserAuth.php";
    include "../Standard_Functions/grade_converter.php";
    include "../Standard_Functions/user_departments.php";

    date_default_timezone_set('Asia/Singapore');

	if(empty($_REQUEST['sr_code'])) {
	    exit();
	}

	$id 			= null;			
	$sr_code 		= $_REQUEST['sr_code'];
	$grades_added 	= $_REQUEST['grades_added'];
	$student 		= $_REQUEST['student'];


	// Validating Inputs
	$validate_inputs = new StudentGradeValidate();

	foreach ($grades_added as $course_id => $grade) {
		$validate_inputs->setValues($sr_code, $course_id, $grade);
		if($validate_inputs->validate()){
			// Alert if Inputs are Invalid
			echo json_encode(array(
				"alert" => "error",
				"title" => "Input Error!",
				"message" => $validate_inputs->getErrorMessage()
			));
			exit();
		}
	}
	
	$sr_code 		= $mysqli->real_escape_string($_REQUEST['sr_code']);

	// -----------------------------------------------------
	// Grade saving for single value or grade
	
	if (count($grades_added) ==  1) {
		foreach ($grades_added as $id => $course_grade) {
			$course_id = $id;
			$grade = $course_grade;
			$grade_converted = convert_grade($course_grade);
		}

		// If Course already passed
		$sql = $mysqli->query("
			SELECT True 
			FROM grades g 
				INNER JOIN user_student us ON g.Student_ID = us.Student_ID 
			WHERE us.SR_Code = '$sr_code'
				AND g.Course_ID = '$course_id'
			    AND g.Grade < 4
			LIMIT 1
		");
		if (!$sql) {
			showError();
			exit();
		}
		while($sql->num_rows > 0){
			// Alert error if Course already passed
			echo json_encode(array(
				"alert" => "info",
				"title" => "Course Already Passed!",
				"message" => "This course has been already passed"
			));
			exit();
		}


		// Creating Curriculum
		$sql = $mysqli->query("
			INSERT INTO `grades` (`Grade_Rec_ID`, `Course_ID`, `Student_ID`, `Grade`) 
			VALUES (NULL, '$course_id', (SELECT Student_ID FROM user_student WHERE SR_Code = '$sr_code'), '$grade')
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


		// Inserting Grade History
		$current_date =  date('Y-m-d');
		$current_time =  date('H:i:s');
		$course_name  =  getCourseName($mysqli, $course_id);

		session_start();
	    $UserAuth = unserialize($_SESSION['UserAuth']);
	    $user_id = $UserAuth->get_UserID();
	    $name = $UserAuth->get_UserName();
    	$user_type = $UserAuth->get_UserType();
		$department   =  'NULL';
		if ($user_type != 'ADM') {
			$department = getUserDepartments($mysqli, $user_id);
		}

		$sql = $mysqli->query("
			INSERT INTO `grading_history` (`Grading_History_ID`, `Date`, `Time`, `Department_ID`, `History`) 
			VALUES (NULL, '$current_date', '$current_time', $department,
				'[$user_id] $name add a grade for [$sr_code] $student on course [$course_id] $course_name - $grade_converted')
		");
		if (!$sql) {
			showError();
			exit();
		}
		if ($mysqli->affected_rows <= 0) {
			showError();
			exit();
		}	


		// Alert Success if updated
		echo json_encode(array(
			"alert" => "success",
			"title" => "Grades Succesfully Added!",
			"message" => "Student Grade Succesfully Added",
			"id" => "$id"
		));
		exit();
	}

	// -----------------------------------------------------
	// Grade saving for multiple value or grade
	
	$errors = 0;
	$passed = 0;
	$added = 0;
	
	session_start();
	foreach ($grades_added as $course_id => $grade) {
		$grade_converted = convert_grade($grade);

		// If Course already passed
		$sql = $mysqli->query("
			SELECT True 
			FROM grades g 
				INNER JOIN user_student us ON g.Student_ID = us.Student_ID 
			WHERE us.SR_Code = '$sr_code'
				AND g.Course_ID = '$course_id'
			    AND g.Grade < 4
			LIMIT 1
		");
		if (!$sql) {
			$errors++;
			continue;
		}
		while($sql->num_rows > 0){
			// Alert error if Course already passed
			$passed++;
			continue;
		}


		// Inserting Grade
		$sql = $mysqli->query("
			INSERT INTO `grades` (`Grade_Rec_ID`, `Course_ID`, `Student_ID`, `Grade`) 
			VALUES (NULL, '$course_id', (SELECT Student_ID FROM user_student WHERE SR_Code = '$sr_code'), '$grade')
		");
		if (!$sql) {
			$errors++;
			continue;
		}
		if ($mysqli->affected_rows <= 0) {
			$errors++;
			continue;
		}	

		// Inserting Grade History
		$current_date =  date('Y-m-d');
		$current_time =  date('H:i:s');
		$course_name  =  getCourseName($mysqli, $course_id);

	    $UserAuth = unserialize($_SESSION['UserAuth']);
	    $user_id = $UserAuth->get_UserID();
	    $name = $UserAuth->get_UserName();
    	$user_type = $UserAuth->get_UserType();
		$department   =  'NULL';
		if ($user_type != 'ADM') {
			$department = getUserDepartments($mysqli, $user_id);
		}

		$sql = $mysqli->query("
			INSERT INTO `grading_history` (`Grading_History_ID`, `Date`, `Time`, `Department_ID`, `History`) 
			VALUES (NULL, '$current_date', '$current_time', $department,
				'[$user_id] $name add a grade for [$sr_code] $student on course [$course_id] $course_name - $grade_converted')
		");
		if (!$sql) {
			continue;
		}
		if ($mysqli->affected_rows <= 0) {
			continue;
		}	

		$added++;
	}

	if ($errors == 0 && $passed == 0 && $added > 1){
		// Alert Success if all grade is added
		echo json_encode(array(
			"alert" => "success",
			"title" => "All Grades Succesfully Added!",
			"message" => "Student Grades Succesfully Added",
			"id" => "$id"
		));
		exit();
	}

	if ($added > 1) {
		// Alert if their some errors and an already passed course
		echo json_encode(array(
			"alert" => "success",
			"title" => "Some Grades Succesfully Added!",
			"message" => "$added of Student Grade/s are added.\n$passed of Student Course/s are already passed.\n$errors of error/s has been occured."
		));
		exit();
	}

	// Failed to add any grades
	showError();
	exit();

	function showError(){
		// Alert if Failed to Create
		echo json_encode(array(
			"alert" => "error",
			"title" => "Failed to Add Grades!",
			"message" => "Adding Grades has been Failed"
		));
	}

	function getCourseName($mysqli, $id) {
		// Get Course name
		$sql = $mysqli->query("
			SELECT Course_Code FROM courses WHERE Course_ID = '$id'
		");
		if (!$sql) {
			exit();
		}
		while ($obj = $sql -> fetch_object()){
			return $obj->Course_Code;
		}
		exit();
	}
?>