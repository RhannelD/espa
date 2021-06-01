<?php  
	include "../database/dbconnection.php";

	if(empty($_REQUEST['id'])) {
		exit();
	}

	$SR_Code = $mysqli->real_escape_string($_REQUEST['id']);

	$validate = ifConnectedToOtherRecords($mysqli, $SR_Code);

	if(is_string($validate)){
		echo json_encode(array(
			"alert" => "info",
			"title" => "Student Profile cannot be Deleted!",
			"message" => "Student \"$SR_Code\" has $validate"
		));
		exit();
	}

	$deleting = deletingProgram($mysqli, $SR_Code);
	if($deleting) {	
		echo json_encode(array(
			"alert" => "success",
			"title" => "Deleted Successfully!",
			"message" => "Program \"$SR_Code\" Deleted Successfully"
		));
		exit();
	}

	if(is_null($validate) || ($deleting == 0)){
		echo json_encode(array(
			"alert" => "error",
			"title" => "Student Profile Deletion has been Failed!",
			"message" => "Student \"$SR_Code\" Can't Deleted"
		));
		exit();
	} 


	// Delete Function
	function deletingProgram($mysqli, $SR_Code){
		$sql = $mysqli->query("
			SELECT User_ID FROM user_student WHERE SR_Code = '$SR_Code'
		");
		if(!$sql){
			return null;
		}
		while ($obj = $sql -> fetch_object()){
	  		$User_ID = $obj->User_ID;
	  		break;
	  	}

		$sql = $mysqli->multi_query("
			DELETE FROM recovery_code WHERE User_ID = '$User_ID';
			DELETE FROM user_student WHERE User_ID = '$User_ID';
			DELETE FROM user WHERE User_ID = '$User_ID';
		");
		if (!$sql) {
			return null;
		}

		$affected_row = 0;
		while ($mysqli->next_result()) {
			$affected_row += $mysqli->affected_rows;
		}
		return $affected_row > 0;
	}

	// Validations
	function ifConnectedToOtherRecords($mysqli, $SR_Code){
		$sql = $mysqli->query("
			SELECT 'Grades' AS Result  
				FROM user_student us 
					INNER JOIN grades g ON us.Student_ID = g.Student_ID
				WHERE us.SR_Code = '$SR_Code'
			UNION ALL
			SELECT 'Proposal Slip' AS Result   
				FROM proposal_slip ps
					INNER JOIN user_student us ON ps.Student_ID = us.Student_ID 
				WHERE us.SR_Code = '$SR_Code'
			LIMIT 1
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