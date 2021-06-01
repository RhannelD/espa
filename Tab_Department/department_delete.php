<?php  
	include "../database/dbconnection.php";

	if(!empty($_REQUEST['id']) && !empty($_REQUEST['code'])){	
		$Department_ID = $_REQUEST['id'];
		$Department_Code = $_REQUEST['code'];
		$Department_Logo = $_REQUEST['logo'];

		$validate = ifConnectedToOtherRecords($mysqli, $Department_ID);

		if(is_string($validate)){
			echo json_encode(array(
				"alert" => "info",
				"title" => "Department cannot be Deleted!",
				"message" => "Department \"$Department_Code\" is Connected to a $validate"
			));
			exit();
		}

		$deleting = deletingDepartment($mysqli, $Department_ID, $Department_Logo);
		if($deleting) {	
			echo json_encode(array(
				"alert" => "success",
				"title" => "Deleted Successfully!",
				"message" => "Department \"$Department_Code\" Deleted Successfully"
			));
			exit();
		}

		if(is_null($validate) || is_null($deleting)){
			echo json_encode(array(
				"alert" => "info",
				"title" => "Department Can't Deleted!",
				"message" => "Department \"$Department_Code\" Can't Deleted"
			));
			exit();
		} 
	}


	// Delete Function
	function deletingDepartment($mysqli, $Department_ID, $Department_Logo){
		$sql = $mysqli->query("
			DELETE FROM `department` WHERE `department`.`Department_ID` = $Department_ID
		");
		if($sql){
			$path = "../img/dept_logo/$Department_Logo";
			if(file_exists($path)){
				unlink($path);
			}
			return $mysqli->affected_rows > 0;
		}
		return null;
	}

	// Validations
	function ifConnectedToOtherRecords($mysqli, $Department_ID){
		$sql = $mysqli->query("
			SELECT 'Program' AS Result FROM `program` WHERE Department_ID = $Department_ID UNION ALL
			SELECT 'User' AS Result FROM `user_department` WHERE Department_ID = $Department_ID UNION ALL
			SELECT 'Request' AS Result FROM `request` WHERE Department_ID = $Department_ID
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