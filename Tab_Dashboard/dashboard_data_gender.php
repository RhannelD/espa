<?php  
include '../database/dbconnection.php';
include "../database/UserAuth.php";
include "../Standard_Functions/user_departments.php";

session_start();
$UserAuth = unserialize($_SESSION['UserAuth']);
$user_id = $UserAuth->get_UserID();
$user_type = $UserAuth->get_UserType();

$sql_usertype = '';
if ($user_type == 'CHP' || $user_type == 'EVL') {
    $sql_usertype = 'AND p.Department_ID = '.getUserDepartments($mysqli, $user_id);
}


$sql = $mysqli->query("
    SELECT u.Gender, COUNT(u.User_ID) AS Count
	FROM user u 
		INNER JOIN user_student us ON u.User_ID=us.User_ID
    	INNER JOIN curriculum c ON us.Curriculum_ID=c.Curriculum_ID
        INNER JOIN program p ON c.Program_ID=p.Program_ID
	WHERE True
		$sql_usertype
	GROUP BY u.Gender
	ORDER BY u.Gender DESC
");

if(!$sql || $sql->num_rows <= 0){
	echo json_encode(array(
		'none' => true
	));
	exit();
}

$xValues = [];
$yValues = [];
while($obj = $sql->fetch_object()) {
	array_push($xValues, ucfirst(strtolower($obj->Gender)));
	array_push($yValues, intval($obj->Count));
}

echo json_encode(array(
	"xValues" => $xValues,
	"yValues" => $yValues
));
?>