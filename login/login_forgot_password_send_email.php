<?php  
	include '../database/dbconnection.php';
	require '../library-phpMailer/PHPMailerAutoload.php';

	if(!isset($_REQUEST['id'])){
		exit();
	}
	$id = $_REQUEST['id'];

	$sql = $mysqli->query("
		SELECT rc.Code_ID, rc.Code, CONCAT(u.Firstname, ' ', u.Lastname) AS Name, u.Email
		FROM recovery_code rc
			INNER JOIN user u ON rc.User_ID=u.User_ID
		WHERE rc.User_ID = '$id'
		ORDER BY Code_ID DESC
        LIMIT 1
	");
	if (!$sql || $sql->num_rows==0) {
		exit();
	}
	while ($obj = $sql->fetch_object()) {
		$result = $obj;
		break;
	}


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
	$mail->addAddress($result->Email);         // Name is optional

	$mail->isHTML(true);                                  	// Set email format to HTML

	$mail->Subject = 'Forgot Password';
	$mail->Body    = "
		<h2>".$result->Name."</h2>
		This is your verification code: <strong>".$result->Code."</strong>
	";
	$mail->AltBody = $result->Name.' This is your verification code: '.$result->Code;
	
	if(!$mail->send()) {
		echo $mail->ErrorInfo;
	} 
?>