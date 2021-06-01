<?php  
	include "../database/dbconnection.php";

	if(empty($_REQUEST['id'])) {
		exit();
	}

	$id = $mysqli->real_escape_string($_REQUEST['id']);


	$deleting = deletingProgram($mysqli, $id);
	if($deleting) {	
		echo json_encode(array(
			"alert" => "success",
			"title" => "Deleted Successfully!",
			"message" => "Department Officer Profile Deleted Successfully"
		));
		exit();
	}

	if(is_null($deleting)){
		echo json_encode(array(
			"alert" => "error",
			"title" => "Deletion has been Failed!",
			"message" => "Department Officer Profile Can't Deleted"
		));
		exit();
	} 


	// Delete Function
	function deletingProgram($mysqli, $user_id){
		$sql = $mysqli->query("
			DELETE FROM recovery_code WHERE User_ID = '$user_id'
		");

		$sql = $mysqli->query("
			DELETE FROM user_student WHERE User_ID = '$user_id'
		");

		$sql = $mysqli->query("
			DELETE FROM user_department WHERE User_ID = '$user_id'
		");

		$sql = $mysqli->query("
			DELETE FROM user WHERE User_ID = '$user_id'
		");
		if (!$sql) {
			return null;
		}

		return $mysqli->affected_rows > 0;
	}
?>