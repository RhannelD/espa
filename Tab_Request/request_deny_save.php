<?php  
	include "../database/dbconnection.php";
	include "../Validations/request_input_validate.php";
	require '../library-phpMailer/PHPMailerAutoload.php';

	if(empty($_REQUEST['request_id'])) {
		exit();
    }
    $request_id 	= $mysqli->real_escape_string($_REQUEST['request_id']);
    $description 	= $mysqli->real_escape_string($_REQUEST['description']);

	$validate_inputs = new RequestValidate();
	$validate_inputs->setValues($description);
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

	// Verify if the request id is existing
	$sql = $mysqli->query("
		SELECT True FROM request WHERE Request_ID = '$request_id' 
		UNION ALL
		SELECT True FROM request_approve WHERE Request_ID = '$request_id'
		UNION ALL
		SELECT True FROM request_denied WHERE Request_ID = '$request_id' 
	");
	if (!$sql || $sql->num_rows == 0) {
		showError();
		exit();
	}
	if ($sql->num_rows != 1) {
		echo json_encode(array(
			// Alert Success if uploaded
			"alert" => "info",
			"title" => "Denying Request Failed!",
			"message" => "This Request has already been Responded"
		));
	}


	$sql = $mysqli->query("
		INSERT INTO `request_denied` (`Denied_ID`, `Request_ID`, `Message`) VALUES (NULL, '$request_id', '$description')
	");
	if (!$sql) {
		showError();
		exit();
	}


	// --------------------------------------
	// Sending Email Notification to the student
	$sql = $mysqli->query("
		SELECT CONCAT(u.Firstname, '', u.Lastname) AS Name, us.SR_Code, u.Email
		FROM user u 
			INNER JOIN user_student us ON u.User_ID=us.User_ID
            INNER JOIN request r ON r.Student_ID=us.Student_ID
		WHERE Request_ID = '$request_id'
	");
	if ($sql) {
		while ($obj = $sql -> fetch_object()) {
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
			$mail->addAddress($obj->Email);         // Name is optional
	
			$mail->isHTML(true);                                  	// Set email format to HTML
	
			$mail->Subject = 'Evalaution Notification';
			$mail->Body    = "
				<h2>".$obj->Name."</h2>
				Your request for evaluation have been denied.<br>
				Check your account at our website.<br>
				<a href='https://".$_SERVER['HTTP_HOST']."' target='_blank'>Electronic Student Program Adviser</a>
			";
			$mail->AltBody = $obj->Name.'You have been evaluated. Check your account at our website. https://'.$_SERVER['HTTP_HOST'];
			$mail->send();
			break;
		}
	}
	// --------------------------------------


	echo json_encode(array(
		// Alert Success if uploaded
		"alert" => "success",
		"title" => "Responded Successfully!",
		"message" => "You Denied the Student's Reuqest"
	));
	exit();


	function showError(){
		echo json_encode(array(
			// Alert Success if uploaded
			"alert" => "error",
			"title" => "Upload Failed!",
			"message" => "Student Failed to Upload Proposal Slip"
		));
	}
?>