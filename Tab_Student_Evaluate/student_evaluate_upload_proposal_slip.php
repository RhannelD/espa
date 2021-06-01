<?php  
	include "../database/dbconnection.php";
	include "../pdf-generator/proposal_slip.php";
	include "../Validations/proposal_slip_input_validate.php";
	require '../library-phpMailer/PHPMailerAutoload.php';

	if(empty($_REQUEST['added_courses'])&&empty($_REQUEST['sr_code'])) {
		exit();
    }
    $sr_code 		= $_REQUEST['sr_code'];
    $description 	= $_REQUEST['description'];

    // Validating Inputs
	$validate_inputs = new ProposalSlipValidate();
	$validate_inputs->setValues($sr_code, $description);
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
    $description 	= $mysqli->real_escape_string($_REQUEST['description']);

    date_default_timezone_set('Asia/Singapore');
	$current_date 	= date('Y-m-d_H-i-s');
	$date 			= date('Y-m-d');

	$file_name = "ProposalSlip_".$sr_code."_$current_date.pdf";
	$file_path = dirname(__FILE__, 2)."\\Proposal_Slip\\";

	$fullpath  = $file_path.$file_name;
	$fullpath  = str_replace("\\", "/", $fullpath);

    $pdf = new ProposalSlip($mysqli, $_REQUEST['sr_code'], $_REQUEST['added_courses']);
    $pdf->Output($fullpath, 'F');

    if(!file_exists($fullpath)){
	    showError();
		exit();
	}

	// Inserting Proposal Slip info to Database
	$sql = $mysqli->query("
		INSERT INTO `proposal_slip` (`Slip_ID`, `Student_ID`, `Description`, `File_Name`, `Date`) 
		VALUES (NULL, (SELECT us.Student_ID FROM user_student us WHERE us.SR_Code = '$sr_code'), 
			'$description', '$file_name', '$date')
	");
	if (!$sql || $mysqli->affected_rows <= 0) {
		unlink($fullpath);

		showError();
		exit();
	}
	$proposal_id = $mysqli->insert_id;

	// Inserting respond to the Request if theres any
	if (!(empty($_REQUEST['request_id']) || $_REQUEST['request_id'] <= 0)) {
		$request_id = $_REQUEST['request_id'];

		// Verify if the request id is existing
		$sql = $mysqli->query("
			SELECT True FROM request WHERE Request_ID = '$request_id' 
			UNION ALL
			SELECT True FROM request_approve WHERE Request_ID = '$request_id'
			UNION ALL
			SELECT True FROM request_denied WHERE Request_ID = '$request_id' 
		");
		if ($sql && $sql->num_rows == 1) {
			$sql = $mysqli->query("
				INSERT INTO `request_approve` (`Approve_ID`, `Request_ID`, `Slip_ID`) VALUES (NULL, '$request_id', '$proposal_id')
			");
		}
	}


	// --------------------------------------
	// Sending Email Notification to the student
	$sql = $mysqli->query("
		SELECT CONCAT(u.Firstname, '', u.Lastname) AS Name, us.SR_Code, u.Email
		FROM user u 
			INNER JOIN user_student us ON u.User_ID=us.User_ID
		WHERE us.SR_Code = '$sr_code'
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
				You have been evaluated.<br>
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
		"title" => "Uploaded Successfully!",
		"message" => "Student Proposal Slip Uploaded Successfully"
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