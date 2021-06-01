<?php  
	include "../database/dbconnection.php";

	if(empty($_REQUEST['id'])) {
		exit();
	}

	$Request_ID = $mysqli->real_escape_string($_REQUEST['id']);
	$files 		= array();;

	// Check request if existing
	$sql = $mysqli->query("
		SELECT rf.Filename 
		FROM request r
			LEFT JOIN request_file rf ON r.Request_ID=rf.Request_ID
		WHERE r.Request_ID = '$Request_ID'
	");
	if(!$sql) {
		showError();
		exit();
	}
	if ($sql->num_rows <= 0) {
		// exit if not exist
		echo json_encode(array(
			"alert" => "error",
			"title" => "Deletion Failed!",
			"message" => "Proposal Slip is not existing"
		));
		exit();
	}
	while ($obj = $sql->fetch_object()) {
		if (!empty($obj->Filename))
			array_push($files,$obj->Filename);
	}
	$sql->free_result();

	// Deleting Request data on Database
	$sql = $mysqli->multi_query("
		DELETE FROM request_file WHERE Request_ID = '$Request_ID';
		DELETE FROM request_denied WHERE Request_ID = '$Request_ID';
		DELETE FROM request_approve WHERE Request_ID = '$Request_ID'; 
		DELETE FROM `request` WHERE `request`.`Request_ID` = '$Request_ID';
	");
	if(!$sql){
		showError();
		exit();
	}
	if ($result = $mysqli -> store_result()) {
  		if ($result->affected_rows <= 0) {
			showError();
			exit();
  		}
     	$result -> free_result();
    }


    // Deleting the files
    if (count($files) > 0){
    	$path = dirname(__FILE__, 2)."\\Request_Files\\";
    	foreach ($files as $file_name) {
    		if(file_exists($path.$file_name)) {
    			unlink($path.$file_name);
    		}
    	}
    }
	

	// exit on success
	echo json_encode(array(
		"alert" => "success",
		"title" => "Deleted Successfully!",
		"message" => "Proposal Slip Deleted Successfully"
	));
	exit();


	function showError() {
		echo json_encode(array(
			"alert" => "error",
			"title" => "Deletion Failed!",
			"message" => "Proposal Slip Can't Deleted"
		));
		exit();
	} 
?>