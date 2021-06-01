<?php  
	include "../database/dbconnection.php";

	if(empty($_REQUEST['id'])) {
		exit();
	}

	$Program_ID = $_REQUEST['id'];
	$Program_Code = $_REQUEST['code'];

	$validate = ifConnectedToOtherRecords($mysqli, $Program_ID);

	if(is_string($validate)){
		echo json_encode(array(
			"alert" => "info",
			"title" => "Program cannot be Deleted!",
			"message" => "Program \"$Program_Code\" is Connected to a $validate"
		));
		exit();
	}

	$deleting = deletingProgram($mysqli, $Program_ID);
	if($deleting) {	
		echo json_encode(array(
			"alert" => "success",
			"title" => "Deleted Successfully!",
			"message" => "Program \"$Program_Code\" Deleted Successfully"
		));
		exit();
	}

	if(is_null($validate) || is_null($deleting)){
		echo json_encode(array(
			"alert" => "error",
			"title" => "Program Deletion has been Failed!",
			"message" => "Program \"$Program_Code\" Can't Deleted"
		));
		exit();
	} 


	// Delete Function
	function deletingProgram($mysqli, $Program_ID){
		$sql = $mysqli->query("
			DELETE FROM `program` WHERE `program`.`Program_ID` = $Program_ID
		");
		if($sql){
			return $mysqli->affected_rows > 0;
		}
		return null;
	}

	// Validations
	function ifConnectedToOtherRecords($mysqli, $Program_ID){
		$sql = $mysqli->query("
			SELECT 'Curriculum' AS Result 
			FROM `curriculum` 
			WHERE Program_ID = $Program_ID  
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