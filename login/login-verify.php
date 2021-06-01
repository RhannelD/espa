<?php 
	include '../database/dbconnection.php';
	include '../database/UserAuth.php';
	include '../Validations/login_input_validate.php';

	session_start();

	$attempt = 0;
	if ( isset($_SESSION["attempt"]) && $_SESSION["attempt"] < 3 ){
		$attempt = $_SESSION["attempt"];
	}
	$attempt++;
	$_SESSION["attempt"] = $attempt;	

	if(!isset($_REQUEST['username']) || !isset($_REQUEST['password'])) {
	    exit();
	}
	
	$username = $_REQUEST['username'];
	$password = $_REQUEST['password'];

	// Validating Inputs
	$validate_inputs = new LoginValidate();
	$validate_inputs->setValues($username, $password);
	if($validate_inputs->validate()){
		// Alert if Inputs are Invalid
		echo json_encode(array(
			"alert" => "error",
			"title" => "Input Error!",
			"message" => $validate_inputs->getErrorMessage(),
			'error' =>  $validate_inputs->getErrorKey(),
			'attempt' => $attempt
		));
		exit();
	}

	$username 	= $mysqli->real_escape_string($_REQUEST['username']);			
	$password 	= $mysqli->real_escape_string($_REQUEST['password']);

	// Verify Login
	$sql = $mysqli->query("
		SELECT user.User_ID, User_Type, CONCAT(Firstname,' ',Lastname) AS Username
		FROM user 
			LEFT JOIN user_student ON user.User_ID = user_student.User_ID
		WHERE (user.User_ID LIKE '$username' 
		    OR CONCAT(Firstname,' ',Lastname) LIKE '$username'
		    OR user_student.SR_Code = '$username'
		    OR user.Email = '$username') 
			AND Password LIKE '$password'
	");
	if (!$sql) {
		// Alert if Failed to Verify
		echo json_encode(array(
			"alert" => "error",
			"title" => "Couldn't Sign-up!",
			"message" => "Please try again later",
			'attempt' => $attempt
		));
		exit();
	}

	while ($obj = $sql -> fetch_object()) {
		if (!in_array($obj->User_Type, array('ADM', 'CHP', 'EVL', 'STD'))) {
			// Alert if User dont have any position
			echo json_encode(array(
				"alert" => "info",
				"title" => "Sign-up Failed!",
				"message" => $obj->Username." don't have any position"
			));
			exit();
		}

		$UserAuth = new UserAuth($obj->User_ID, $obj->Username, $obj->User_Type);

		$_SESSION['UserAuth'] = serialize($UserAuth);

		// Going to Main
		echo json_encode(array(
			"alert" => "success",
			"panel" => "../main/"
		));
		exit();
	}

	// Alert if Failed to Verify
	echo json_encode(array(
		"alert" => "error",
		"title" => "Sign-up Failed!",
		"message" => "Username and Password doesn't match",
		'error' =>  "password",
		'attempt' => $attempt
	));
	exit();
?>