<?php 
	include '../database/dbconnection.php';
	include '../Validations/student_input_validate.php';
	require '../library-phpMailer/PHPMailerAutoload.php';

	if(empty($_REQUEST['sr_code'])) {
		showError(1);
	    exit();
	}
	
	$id 			= null;
	$sr_code 		= $_REQUEST['sr_code'];
	$firstname 		= $_REQUEST['firstname'];
	$lastname 		= $_REQUEST['lastname'];
	$gender 		= $_REQUEST['gender'];
	$email 			= $_REQUEST['email'];
	$password 		= $_REQUEST['password'];
	$c_password 	= $_REQUEST['c_password'];
	$program 		= $_REQUEST['program'];
	$year 			= $_REQUEST['year'];
	$track 			= $_REQUEST['track'];

	if ($password != $c_password) {
		// Alert if Password are Invalid
		echo json_encode(array(
			"alert" => "error",
			"title" => "Passwords not match!",
			"message" => 'Password and Confirm Password does not match',
			'error' =>  'password'
		));
		exit();
	}

	// Validating Inputs
	$validate_inputs = new StudentValidate();
	$validate_inputs->setValues($sr_code, $firstname, $lastname, $gender, $email, $program, $year, $track);
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

	$sr_code 		= $mysqli->real_escape_string($_REQUEST['sr_code']);
	$firstname 		= $mysqli->real_escape_string($_REQUEST['firstname']);
	$lastname 		= $mysqli->real_escape_string($_REQUEST['lastname']);
	$gender 		= $mysqli->real_escape_string($_REQUEST['gender']);
	$email 			= $mysqli->real_escape_string(strtolower($_REQUEST['email']));
	$password 		= $mysqli->real_escape_string($_REQUEST['password']);
	$curriculum 	= $mysqli->real_escape_string($_REQUEST['track']);


	// Validate if SR code already exist
	$sql = $mysqli->query("
		SELECT 'SR-Code' AS Result, 'sr_code' AS Error FROM user_student WHERE SR_Code = '$sr_code' UNION ALL
		SELECT 'Email' AS Result, 'email' AS Error FROM user WHERE Email = '$email' LIMIT 1
	");
	if (!$sql) {
		showError(2);
		exit();
	}
	if ($sql->num_rows > 0) {
		// Alert if Sr code exist
		while ($obj = $sql -> fetch_object()) {
			echo json_encode(array(
				"alert" => "error",
				"title" => "$obj->Result already exist!",
				"message" => "Student's $obj->Result is already existing from the records",
				'error' =>  $obj->Error
			));
		}
		exit();
	}	
	$sql->free_result();


	// Creating Student User info
	$sql = $mysqli->query("
		INSERT INTO `user_signup` (`Signup_ID`, `Firstname`, `Lastname`, `Gender`, `Email`, `Password`, `Curriculum_ID`, `SR_Code`) 
		VALUES (NULL, '$firstname', '$lastname', '$gender', '$email', '$password', '$curriculum', '$sr_code')
	");
	if (!$sql) {
		showError(3);
		exit();
	}
	if ($mysqli->affected_rows <= 0) {
		showError(4);
		exit();
	}	

	$id = $mysqli->insert_id;

	// --------------------------------------
	$signupconfirmationlink = $_SERVER['HTTP_HOST']."/sign_up/?signupcode=$id";
	
	$mail = new PHPMailer;

	// $mail->SMTPDebug = 3;                            // Enable verbose debug output

	$mail->isSMTP();                                    // Set mailer to use SMTP
	$mail->Host = 'smtp.gmail.com';  					// Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                             // Enable SMTP authentication
	$mail->Username = 'es.programadviser@gmail.com';    // SMTP username
	$mail->Password = 'Rhannel 31';                     // SMTP password
	$mail->SMTPSecure = 'tls';                          // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 587;                                  // TCP port to connect to

	$mail->setFrom('es.programadviser@gmail.com', 'Electronic Student Program Adviser');
	// $mail->addAddress('joe@example.net', 'Joe User');    // Add a recipient
	$mail->addAddress($email);         // Name is optional

	$mail->isHTML(true);                                  	// Set email format to HTML

	$mail->Subject = 'Sign Up Confimation';
	$mail->Body    = "
		<h2>$firstname $lastname</h2>
		You just have signed up.<br>
		This is the confirmation link for you account:<br>
		<a href='https://$signupconfirmationlink' target='_blank'>$signupconfirmationlink</a>
	";
	$mail->AltBody = '$firstname $lastname You just have signed up. This is the confirmation link for you account: $signupconfirmationlink';


	if($_SERVER['HTTP_HOST'] == 'localhost'){	
		$mail->send();

		// Alert Success if updated
		echo json_encode(array(
			"alert" => "success",
			"title" => "Email Sent!",
			"message" => "Check your email for confirmation"
		));
		exit();
	}

	if(!$mail->send()) {
		echo json_encode(array(
			"alert" => "error",
			"title" => "Sending Email Failed!",
			"message" => 'Mailer Error: ' . $mail->ErrorInfo
		));
		exit();
	} 

	echo json_encode(array(
		"alert" => "success",
		"title" => "Email Sent!",
		"message" => "Check your email for confirmation"
	));
	exit();
	

	function showError($num){
		// Alert if Failed to Create
		echo json_encode(array(
			"alert" => "error",
			"title" => "Failed to Create!",
			"message" => "Student Profile Creation has been Failed"
		));
	}

?>

