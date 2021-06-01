<?php 
	include '../database/dbconnection.php';
	include '../Validations/student_input_validate.php';

	if(empty($_REQUEST['sr_code'])) {
	    exit();
	}

	$sr_code 	= $_REQUEST['sr_code'];
	$course_id 	= $_REQUEST['course_id'];

	// Validating Inputs
	$validate_inputs = new StudentValidate();
	$validate_inputs->setSRCodeInfo($sr_code);
	if($validate_inputs->validate()){
		// Alert if Inputs are Invalid
		echo json_encode(array(
			"alert" => "error",
			"title" => "Student SR-Code Error!",
			"message" => "SR-Code error, please try again"
		));
		exit();
	}
	
	$sr_code 		= $mysqli->real_escape_string($_REQUEST['sr_code']);
	$course_id 		= $mysqli->real_escape_string($_REQUEST['course_id']);

	

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


	// Verify if all course's prereqs are passed
	$sql = $mysqli->query("
		SELECT * 
		FROM user_student s 
			INNER JOIN courses c 
		    LEFT JOIN grades g ON s.Student_ID=g.Student_ID AND g.Course_ID=c.Course_ID 
		WHERE (g.Grade_Rec_ID IS NULL 
			OR (SELECT MIN(g2.Grade) 
		        FROM user_student s2 
		        INNER JOIN grades g2 ON s2.Student_ID=g2.Student_ID 
		        WHERE g2.Course_ID=c.Course_ID 
		        	AND s2.Student_ID=s.Student_ID)>3 ) 
			AND c.Course_ID NOT IN (SELECT pr3.Course_ID 
		                            FROM user_student s3 
		                            	INNER JOIN pre_requisites pr3 
		                            	LEFT JOIN grades g3 ON s3.Student_ID=g3.Student_ID AND g3.Course_ID=pr3.Pre_Requisite 
		                            WHERE pr3.Course_ID=c.Course_ID 
		                            	AND s3.Student_ID=s.Student_ID 
		                            	AND (g3.Grade IS NULL 
		                                OR (SELECT MIN(g4.Grade) 
		                                    FROM user_student s4 
		                                    	INNER JOIN grades g4 ON s4.Student_ID=g4.Student_ID 
		                                    WHERE g4.Course_ID=pr3.Pre_Requisite 
		                                    	AND s4.Student_ID=s.Student_ID)>3  ) ) 
		AND s.SR_Code = '$sr_code'
		AND c.Course_ID Like '$course_id' 
		GROUP BY c.Course_ID
		LIMIT 1
	");
	if (!$sql) {
		showError();
		exit();
	}
	while($sql->num_rows == 0){
		// Alert error if Course already passed
		echo json_encode(array(
			"alert" => "info",
			"title" => "Course Cannot be Taken!",
			"message" => "Course Pre-Requisite/s hasn't been passed yet"
		));
		exit();
	}


	// Adding
	$sql = $mysqli->query("
		SELECT Course_ID, Course_Code, Course_Title, Units, Lecture, Laboratory 
		FROM courses 
			WHERE Course_ID = '$course_id'
	");
	if (!$sql) {
		showError();
		exit();
	}
	while ($obj = $sql -> fetch_object()) {
		// Alert error if Course already passed
		echo json_encode(array(
			"alert" => "success",
			"course_data" => array(
				'Course_ID' 	=> $obj->Course_ID,
				'Course_Code'	=> $obj->Course_Code,
				'Course_Title'	=> $obj->Course_Title,
				'Units'			=> $obj->Units,
				'Lecture'		=> $obj->Lecture,
				'Laboratory'	=> $obj->Laboratory,
			)
		));
		exit();
	}

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
?>