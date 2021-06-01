<?php  
	include "../database/dbconnection.php";
	
	function getUserDepartments($mysqli, $user_id){
		$sql = $mysqli->query("
			SELECT u.User_Type, ud.Department_ID 
			FROM user u 
				INNER JOIN user_department ud ON u.User_ID = ud.User_ID
			WHERE u.User_ID = '$user_id'
			UNION ALL 
			SELECT u.User_Type, (SELECT GROUP_CONCAT(d.Department_ID) FROM department d) AS Department_ID
			FROM user u
			WHERE u.User_ID = '$user_id'
				AND u.User_Type = 'ADM'
		");
		if (!$sql) {
			showError();
			exit();
		}
		while ($obj = $sql -> fetch_object()){
			return $obj->Department_ID;
		}
	}
?>