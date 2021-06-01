<?php 
	include '../database/dbconnection.php';

	if(empty($_REQUEST['id'])) {
	    exit();
	}
	
	$id 		= $_REQUEST['id'];		
	$Logo 		= $_REQUEST['logo'];
	$code 		= $_REQUEST['code'];
	$file_type 	= $_REQUEST['file_type'];

	if ( 0 < $_FILES['file']['error'] ) {
		$message = '';
		switch( $_FILES['file']['error'] ) {
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
                $message .= ' - internal error #'.$_FILES['newfile']['error'];
                break;
        }
	 	echo json_encode(array(
			"alert" => "error",
			"title" => "Failed to Upload!",
			"message" => "Error $message"
		));
	 	exit();
    }

	// Updating Logo info if there are changes
	$sql = $mysqli->query("
		UPDATE `department` 
		SET `Logo` = 'logo-$id.$file_type' 
		WHERE `department`.`Department_ID` = $id 
	");
	if(!$sql){
		showError();
		exit();
	}
	if ($mysqli->affected_rows > 0) {
		$path = "../img/dept_logo/$Logo";
		if(file_exists($path)){
			unlink($path);
		}
	}	
	
    move_uploaded_file($_FILES['file']['tmp_name'], "../img/dept_logo/logo-$id.$file_type" );

	// Alert Success if updated
	echo json_encode(array(
		"alert" => "success",
		"title" => "Succesfully Updated!",
		"message" => "Department \"$code\" Logo Succesfully Updated",
		"id" => "$id"
	));
	exit();

	function showError(){
		// Alert if Failed to Create
		echo json_encode(array(
			"alert" => "error",
			"title" => "Failed to Create!",
			"message" => "Course Creation has been Failed"
		));
	}
?>