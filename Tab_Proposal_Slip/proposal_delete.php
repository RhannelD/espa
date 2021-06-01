<?php  
	include "../database/dbconnection.php";

	if(empty($_REQUEST['id'])) {
		exit();
	}

	$Proposal_ID = $mysqli->real_escape_string($_REQUEST['id']);

	// Check proposal slip if existing
	$sql = $mysqli->query("
		SELECT File_Name FROM proposal_slip WHERE Slip_ID = '$Proposal_ID'
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
		$file_name = $obj->File_Name;
	}
	$sql->free_result();

	// Validatin if can be deleted
	$validate = ifConnectedToOtherRecords($mysqli, $Proposal_ID);

	if(is_string($validate)){
		echo json_encode(array(
			"alert" => "info",
			"title" => "Proposal Slip cannot be Deleted!",
			"message" => "Proposal Slip is connected to a $validate"
		));
		exit();
	}


	// Deleting the proposal slip record
	$sql = $mysqli->query("
		DELETE FROM `proposal_slip` WHERE `proposal_slip`.`Slip_ID` = '$Proposal_ID'
	");
	if(!$sql) {
		showError();
		exit();
	}
	if ($mysqli->affected_rows > 0) {	
		// Deleting the file if existing
		$path = dirname(__FILE__, 2)."\\Proposal_Slip\\";
		if(file_exists($path.$file_name)) {
			unlink($path.$file_name);
		}

		// exit on success
		echo json_encode(array(
			"alert" => "success",
			"title" => "Deleted Successfully!",
			"message" => "Proposal Slip Deleted Successfully"
		));
		exit();
	}
	showError();

	function showError() {
		echo json_encode(array(
			"alert" => "error",
			"title" => "Deletion Failed!",
			"message" => "Proposal Slip Can't Deleted"
		));
		exit();
	} 

	// Validations
	function ifConnectedToOtherRecords($mysqli, $Proposal_ID){
		$sql = $mysqli->query("
			SELECT 'Request' AS Result FROM request_approve WHERE Slip_ID = '$Proposal_ID'
		");
		if ($result = $sql) {
			while ($obj = $result -> fetch_object()){
	  			return $obj->Result;
	  		}
	  		return false;
		}
		return null;
	}

?>