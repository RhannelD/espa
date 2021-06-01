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
    $sql_usertype = 'AND r.Department_ID = '.getUserDepartments($mysqli, $user_id);
}

$sql = $mysqli->query("
    SELECT 'Pending' AS Result, COUNT(r.Request_ID) AS Count
	FROM request r 
		LEFT JOIN request_denied rd ON r.Request_ID=rd.Request_ID 
	    LEFT JOIN request_approve ra ON r.Request_ID=ra.Request_ID
	WHERE rd.Denied_ID IS Null
		AND ra.Approve_ID IS NULL
		$sql_usertype
	UNION ALL
	SELECT 'Denied' AS Result, COUNT(r.Request_ID) AS Count
	FROM request r  
		INNER JOIN request_denied rd ON r.Request_ID=rd.Request_ID
	WHERE True
		$sql_usertype
	UNION ALL
	SELECT 'Evaluated' AS Result, COUNT(r.Request_ID) AS Count
	FROM request r 
	    INNER JOIN request_approve ra ON r.Request_ID=ra.Request_ID
	WHERE True
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