<?php  
	include "../database/dbconnection.php";
	include "../database/UserAuth.php";
	include '../Validations/login_input_validate.php';
    include "../Standard_Functions/grade_converter.php";
    include "../Standard_Functions/user_departments.php";

    date_default_timezone_set('Asia/Singapore');

	if(empty($_REQUEST['grade_rec_id'])) {
		exit();
	}

	$password 		= $_REQUEST['password'];
	// Validating Inputs
	$validate_inputs = new LoginValidate();
	$validate_inputs->setPassword($password);
	if($validate_inputs->validate()){
		// Alert if Inputs are Invalid
		echo json_encode(array(
			"alert" => "error",
			"title" => "Input Error!",
			"message" => $validate_inputs->getErrorMessage(),
			'error' =>  ".c_password"
		));
		exit();
	}


	$grade_rec_id 	= $mysqli->real_escape_string($_REQUEST['grade_rec_id']);
	$password 		= $mysqli->real_escape_string($_REQUEST['password']);

	session_start();
	$UserAuth = unserialize($_SESSION['UserAuth']);
    $user_id = $UserAuth->get_UserID();
    $name = $UserAuth->get_UserName();
    $user_type = $UserAuth->get_UserType();


	// Validations (if correct password)
	$sql = $mysqli->query("
		SELECT True 
		FROM user u
		WHERE u.User_ID = '$user_id'
			AND u.Password = '$password'
		    AND u.User_Type <> 'STD'
	");
	if (!$sql) {
		showError();
		exit();
	}
	if ($sql->num_rows <= 0) {
		// alert password is incorrect
		$sql -> free_result();
		echo json_encode(array(
			"alert" => "error",
			"title" => "Incorrect Password!",
			"message" => "You entered an incorrect password"
		));
		exit();
	}

    // Get Info for History Insertion
	$sql = $mysqli->query("
		SELECT g.Course_ID, c.Course_Code, g.Grade, us.SR_Code, CONCAT(u.Firstname, ' ', u.Lastname) AS Name 
		FROM grades g 
			INNER JOIN user_student us ON us.Student_ID = g.Student_ID 
		    INNER JOIN user u ON us.User_ID = u.User_ID 
		    INNER JOIN courses c ON g.Course_ID = c.Course_ID
		WHERE g.Grade_Rec_ID = '$grade_rec_id'
	");
	if (!$sql) {
		showError();
		exit();
	}
	while ($obj = $sql -> fetch_object()){
		$sr_code 		= $obj->SR_Code;
		$student 		= $obj->Name;
		$course_id 		= $obj->Course_ID;
		$course_name 	= $obj->Course_Code;
		$grade_converted = convert_grade($obj->Grade);
	}


	// Deleting Grade
	$sql = $mysqli->query("
		DELETE FROM `grades` WHERE `grades`.`Grade_Rec_ID` = '$grade_rec_id'
	");
	if (!$sql) {
		showError();
		exit();
	}
	if ($mysqli->affected_rows <= 0) {
		showError();
		exit();
    }


    // Inserting Grade History
	$current_date =  date('Y-m-d');
	$current_time =  date('H:i:s');
	$department   =  'NULL';
	if ($user_type != 'ADM') {
		$department = getUserDepartments($mysqli, $user_id);
	}

	$sql = $mysqli->query("
		INSERT INTO `grading_history` (`Grading_History_ID`, `Date`, `Time`, `Department_ID`, `History`) 
		VALUES (NULL, '$current_date', '$current_time', $department,
			'[$user_id] $name deleted a grade for [$sr_code] $student on course [$course_id] $course_name - $grade_converted')
	");
	if (!$sql) {
		showError();
		exit();
	}
	if ($mysqli->affected_rows <= 0) {
		showError();
		exit();
	}	

	echo json_encode(array(
		// Alert Success if deleted
		"alert" => "success",
		"title" => "Deleted Successfully!",
		"message" => "Student Grade Deleted Successfully"
	));
	exit();

	function showError(){
		echo json_encode(array(
			"alert" => "error",
			"title" => "Deletion Failed!",
			"message" => "Student Grade Failed to Deleted"
		));
	}
?>