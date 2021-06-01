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
    SELECT 'None' AS Result, COUNT(us.Student_ID) AS Count
	FROM user_student us
    	INNER JOIN curriculum c ON us.Curriculum_ID=c.Curriculum_ID
        INNER JOIN program p ON c.Program_ID=p.Program_ID
	WHERE (SELECT g2.Student_ID FROM grades g2 WHERE g2.Grade = 6 AND g2.Student_ID=us.Student_ID LIMIT 1) IS Null
    	$sql_usertype
	UNION ALL
	SELECT 'Dropped' AS Result, COUNT(us.Student_ID) AS Count
	FROM user_student us
    	INNER JOIN curriculum c ON us.Curriculum_ID=c.Curriculum_ID
        INNER JOIN program p ON c.Program_ID=p.Program_ID
	WHERE (SELECT g2.Student_ID FROM grades g2 WHERE g2.Grade = 6 AND g2.Student_ID=us.Student_ID LIMIT 1)
    	$sql_usertype
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
	array_push($xValues, ucfirst(strtolower($obj->Result)));
	array_push($yValues, intval($obj->Count));
}

echo json_encode(array(
	"xValues" => $xValues,
	"yValues" => $yValues
));
?>