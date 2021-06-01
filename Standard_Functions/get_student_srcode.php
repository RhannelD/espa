<?php  
include "../database/dbconnection.php";
include "../database/UserAuth.php";

session_start();
$UserAuth = unserialize($_SESSION['UserAuth']);
$UserType = $UserAuth->get_UserType();
$UserID   = $UserAuth->get_UserID();

if ($UserType != 'STD') {
	exit();
}

$sql = $mysqli->query("
	SELECT SR_Code
	FROM user_student
	WHERE User_ID = '$UserID'
");
if(!$sql || $sql->num_rows <= 0)
	exit();
while ($obj = $sql -> fetch_object()){
	echo json_encode(array(
		"id" => $obj->SR_Code
	));
}
?>