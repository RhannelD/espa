<?php 
	include '../database/dbconnection.php';
	include '../Validations/department_input_validate.php';

	if(empty($_REQUEST['code'])) {
	    exit();
	}
	
	$id 			= null;	
	$logo 			= $_REQUEST['logo'];
	$code 			= $_REQUEST['code'];
	$department_code= $code;
	$title 			= $_REQUEST['title'];
	$selected_dean 	= $_REQUEST['selected_dean'];
	$selected_head 	= $_REQUEST['selected_head'];
	$dean_name 		= $_REQUEST['dean_name'];
	$dean_gender 	= $_REQUEST['dean_gender'];
	$head_name 		= $_REQUEST['head_name'];
	$head_gender 	= $_REQUEST['head_gender'];
	$file_type		= $_REQUEST['file_type'];
	if($selected_dean!='New'){
		$dean_name 	= null;
		$dean_gender= null;
	}
	if($selected_head!='New') {
		$head_name 	= null;
		$head_gender= null;
	}

	$created = false;

	// Validating Inputs
	$validate_inputs = new DepartmentValidate();
	$validate_inputs->setValues($code, $title, $dean_name, $dean_gender, $head_name, $head_gender, $logo);
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

	$code 			= $mysqli->real_escape_string($_REQUEST['code']);			
	$title 			= $mysqli->real_escape_string($_REQUEST['title']);
	$dean_name 		= $mysqli->real_escape_string($_REQUEST['dean_name']);
	$dean_gender 	= $mysqli->real_escape_string($_REQUEST['dean_gender']);
	$head_name 		= $mysqli->real_escape_string($_REQUEST['head_name']);
	$head_gender 	= $mysqli->real_escape_string($_REQUEST['head_gender']);

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

	if($selected_dean=='New'){
		$sql = $mysqli->query("
			INSERT INTO `department_dean` (`Dean_ID`, `Name`, `Gender`) 
			VALUES (NULL, '$dean_name', '$dean_gender')
		");
		if($mysqli->affected_rows <= 0){
			showError();
			exit();
		}
		$selected_dean = $mysqli->insert_id;
	}
	if($selected_head=='New'){
		$sql = $mysqli->query("
			INSERT INTO `department_head` (`Head_ID`, `Name`, `Gender`) 
			VALUES (NULL, '$head_name', '$head_gender')
		");
		if($mysqli->affected_rows <= 0){
			showError();
			exit();
		}
		$selected_head = $mysqli->insert_id;
	}

	// Creating Course info if there are changes
	$sql = $mysqli->query("
		INSERT INTO `department` (`Department_ID`, `Department_Code`, `Department_Title`, `Dean_ID`, `DeptHead_ID`, `Logo`) 
		VALUES (NULL, '$code', '$title', '$selected_dean', '$selected_head', 'temp')
	");
	if ($mysqli->affected_rows <= 0) {
		showError();
		exit();
	}	

	$id = $mysqli->insert_id;

	$sql = $mysqli->query("
		UPDATE `department` 
		SET `Logo` = 'logo-$id.$file_type' 
		WHERE `department`.`Department_ID` = $id
	"); 
	if ($mysqli->affected_rows <= 0) {
		showError();
		exit();
	}	
	
    move_uploaded_file($_FILES['file']['tmp_name'], "../img/dept_logo/logo-$id.$file_type" );

	// Alert Success if updated
	echo json_encode(array(
		"alert" => "success",
		"title" => "Succesfully Created!",
		"message" => "Course \"$department_code\" Succesfully Created",
		"id" => "$id"
	));
	exit();

	function showError(){
		// Alert if Failed to Create
		echo json_encode(array(
			"alert" => "error",
			"title" => "Failed to Create!",
			"message" => "Department Creation has been Failed"
		));
	}
?>