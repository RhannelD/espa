<?php  
	include '../database/dbconnection.php';
	include '../Validations/request_input_validate.php';
    include "../database/UserAuth.php";

	if(!isset($_REQUEST['message']) && !isset($_FILES['files'])) {
		echo json_encode(array(
			"alert" => "error",
			"title" => "Request Failed",
			"message" => ""
		));
	    exit();
	}

    date_default_timezone_set('Asia/Singapore');
	$date_time 	= date('Y-m-d_H-i-s');
	$date 			= date('Y-m-d');

    session_start();
    $UserAuth = unserialize($_SESSION['UserAuth']);
    $user_id = $UserAuth->get_UserID();

	$message 		= $_REQUEST['message'];

	// Validating Inputs
	$validate_inputs = new RequestValidate();
	$validate_inputs->setValues($message);
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

	if (isset($_FILES['files'])) {
		$files 			= $_FILES['files'];
		$files_error	= $_FILES['files']['error'];
		$files_count 	= count($_FILES['files']['name']);
	
		// print_r( $_FILES['files']['error']);
	
		for ($index=0; $index < count($files_error); $index++) { 
			if ( 0 < $files_error[$index] ) {
				$message = '';
				switch( $files_error[$index] ) {
		            case UPLOAD_ERR_OK:
		                $message = false;;
		                break;
		            case UPLOAD_ERR_INI_SIZE:
		            case UPLOAD_ERR_FORM_SIZE:
		                $message .= ' - file too large (limit of 2mb).';
		                break;
		            case UPLOAD_ERR_PARTIAL:
		                $message .= ' - file upload was not completed.';
		                break;
		            case UPLOAD_ERR_NO_FILE:
		                $message .= ' - zero-length file uploaded.';
		                break;
		            default:
		                $message .= ' - internal error #'.$_FILES['files']['error'];
		                break;
		        }
			 	echo json_encode(array(
					"alert" => "error",
					"title" => "Failed to Upload!",
					"message" => "Error $message"
				));
			 	exit();
		    }
		}
	}

	$message 		= $mysqli->real_escape_string($_REQUEST['message']);	

	// Creating Request info
	$sql = $mysqli->query("
		INSERT INTO `request` (`Request_ID`, `Student_ID`, `Department_ID`, `Message`, `Date`) 
		VALUES (NULL
			, (SELECT us.Student_ID
				FROM user u 
					INNER JOIN user_student us ON u.User_ID=us.User_ID
				WHERE u.User_ID = '$user_id')
			, (SELECT p.Department_ID 
				FROM user u 
					INNER JOIN user_student us ON u.User_ID=us.User_ID
				    INNER JOIN curriculum c ON us.Curriculum_ID=c.Curriculum_ID
				    INNER JOIN program p ON c.Program_ID=p.Program_ID
				WHERE u.User_ID = '$user_id')
			, '$message'
			, '$date')
	");
	if ($mysqli->affected_rows <= 0) {
		showError();
		exit();
	}	

	$id = $mysqli->insert_id;

	if (isset($_FILES['files'])){
		for ($index=0; $index < $files_count; $index++) { 
			// print_r($files['name'][$index]);
			$name 		= $files['name'][$index];
			$type		= pathinfo($name, PATHINFO_EXTENSION);
			$file_name 	= "[$id]_[$index]_[$user_id]_[$date_time].$type";
	
			$sql = $mysqli->query("
				INSERT INTO `request_file` (`File_ID`, `Request_ID`, `Name`, `Filename`) 
				VALUES (NULL, '$id', '$name', '$file_name')
			"); 
			if ($mysqli->affected_rows <= 0) {
				showError();
				exit();
			}	
			$path = "../Request_Files/$file_name";
			if(file_exists($path)){
				unlink($path);
			}
			move_uploaded_file($files['tmp_name'][$index], $path );
		}
	}

	echo json_encode(array(
		"alert" => "success",
		"title" => "Request Send!",
		"message" => "Request Send Succesfully",
		"id" => $id
	));


	function showError(){
		echo json_encode(array(
			// Alert Success if uploaded
			"alert" => "error",
			"title" => "Upload Failed!",
			"message" => "Requesting Evaluation Failed"
		));
	}
?>