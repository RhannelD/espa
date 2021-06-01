<?php  
include '../database/dbconnection.php';
include "../database/UserAuth.php";
include "../Standard_Functions/user_departments.php";
include '../Standard_Functions/semester_schedule.php';
include '../Standard_Functions/year_sem.php';

session_start();
$UserAuth = unserialize($_SESSION['UserAuth']);
$user_id = $UserAuth->get_UserID();
$user_type = $UserAuth->get_UserType();

$indexes = array();
$xValues = array();
$yValues = array(
	'Clean' => array(),
	'Inc' => array(),
	'Failed' => array(),
	'Dropped' => array()
);

$SemesterSchedule = new SemesterSchedule();
$current_year_sem = $SemesterSchedule->getCurrentSchoolYearAndSem();

$iterate 	= 8;
$year 		= $current_year_sem['year'];
$sem 		= $current_year_sem['sem'];

$sql_usertype = '';
if ($user_type == 'CHP' || $user_type == 'EVL') {
    $sql_usertype = 'AND p.Department_ID = '.getUserDepartments($mysqli, $user_id);
}

$array_year_sem = [];

for ($iterate = 8; $iterate > 0; $iterate --) { 
	array_push($array_year_sem, $year."_".$sem);
	array_push($xValues, $year."-".($year+1)." ".getNumYearSem($sem)." Sem");
	$indexes[$year.'_'.$sem] = $iterate-1;

	foreach ($yValues as $key => $val) {
    	array_push($yValues[$key], 0);
	}

	if ($iterate == 1) {
		break;
	}

	if ($sem > 1) {
		$sem--;
		continue;
	}
	$sem = 2;
	$year--;
}

$sql_year_sem = "'".implode("', '",$array_year_sem)."'";

$sql = $mysqli->query("
    SELECT 'Clean' AS Result, (c.Academic_Year+cc.Year_Level-1) AS Year, cc.Semester, COUNT(DISTINCT(us.Student_ID)) AS Count
	FROM user_student us
		INNER JOIN curriculum c ON us.Curriculum_ID=c.Curriculum_ID
	    INNER JOIN curriculum_courses cc ON c.Curriculum_ID=cc.Curriculum_ID
	    INNER JOIN program p ON c.Program_ID=p.Program_ID
		LEFT JOIN grades g ON us.Student_ID=g.Student_ID AND cc.Course_ID=g.Course_ID
	WHERE cc.Semester BETWEEN 1 AND 2
		AND CONCAT((c.Academic_Year+cc.Year_Level-1),'_',cc.Semester) IN ($sql_year_sem)
		$sql_usertype
	    AND us.Student_ID NOT IN (
	        SELECT us2.Student_ID
	        FROM user_student us2
	            INNER JOIN curriculum c2 ON us2.Curriculum_ID=c2.Curriculum_ID
	            INNER JOIN curriculum_courses cc2 ON c2.Curriculum_ID=cc2.Curriculum_ID 
	            LEFT JOIN grades g2 ON us2.Student_ID=g2.Student_ID AND cc2.Course_ID=g2.Course_ID
	        WHERE us2.Student_ID=us.Student_ID
	            AND (c2.Academic_Year+cc2.Year_Level-1) = (c.Academic_Year+cc.Year_Level-1)
	            AND cc2.Semester=cc.Semester
	            AND g2.Grade IS NOT NULL
	            AND g2.Grade IN (4,5,6) 
		)
	GROUP BY (c.Academic_Year+cc.Year_Level-1), cc.Semester
	UNION ALL
	SELECT 'Inc' AS Result, (c.Academic_Year+cc.Year_Level-1) AS Year, cc.Semester, COUNT(DISTINCT(us.Student_ID)) AS Count
	FROM user_student us
		INNER JOIN curriculum c ON us.Curriculum_ID=c.Curriculum_ID
	    INNER JOIN curriculum_courses cc ON c.Curriculum_ID=cc.Curriculum_ID
	    INNER JOIN program p ON c.Program_ID=p.Program_ID
		LEFT JOIN grades g ON us.Student_ID=g.Student_ID AND cc.Course_ID=g.Course_ID
	WHERE cc.Semester BETWEEN 1 AND 2
		AND CONCAT((c.Academic_Year+cc.Year_Level-1),'_',cc.Semester) IN ($sql_year_sem)
		$sql_usertype
		AND g.Grade IS NOT NULL
	    AND us.Student_ID IN (
	        SELECT us2.Student_ID
	        FROM user_student us2
	            INNER JOIN curriculum c2 ON us2.Curriculum_ID=c2.Curriculum_ID
	            INNER JOIN curriculum_courses cc2 ON c2.Curriculum_ID=cc2.Curriculum_ID 
	            LEFT JOIN grades g2 ON us2.Student_ID=g2.Student_ID AND cc2.Course_ID=g2.Course_ID
	        WHERE us2.Student_ID=us.Student_ID
	            AND (c2.Academic_Year+cc2.Year_Level-1) = (c.Academic_Year+cc.Year_Level-1)
	            AND cc2.Semester=cc.Semester
	            AND g2.Grade IS NOT NULL
	            AND g2.Grade IN (4) 
		)
	GROUP BY (c.Academic_Year+cc.Year_Level-1), cc.Semester
	UNION ALL
	SELECT 'Failed' AS Result, (c.Academic_Year+cc.Year_Level-1) AS Year, cc.Semester, COUNT(DISTINCT(us.Student_ID)) AS Count
	FROM user_student us
		INNER JOIN curriculum c ON us.Curriculum_ID=c.Curriculum_ID
	    INNER JOIN curriculum_courses cc ON c.Curriculum_ID=cc.Curriculum_ID
	    INNER JOIN program p ON c.Program_ID=p.Program_ID
		LEFT JOIN grades g ON us.Student_ID=g.Student_ID AND cc.Course_ID=g.Course_ID
	WHERE cc.Semester BETWEEN 1 AND 2
		AND CONCAT((c.Academic_Year+cc.Year_Level-1),'_',cc.Semester) IN ($sql_year_sem)
		$sql_usertype
		AND g.Grade IS NOT NULL
	    AND us.Student_ID IN (
	        SELECT us2.Student_ID
	        FROM user_student us2
	            INNER JOIN curriculum c2 ON us2.Curriculum_ID=c2.Curriculum_ID
	            INNER JOIN curriculum_courses cc2 ON c2.Curriculum_ID=cc2.Curriculum_ID 
	            LEFT JOIN grades g2 ON us2.Student_ID=g2.Student_ID AND cc2.Course_ID=g2.Course_ID
	        WHERE us2.Student_ID=us.Student_ID
	            AND (c2.Academic_Year+cc2.Year_Level-1) = (c.Academic_Year+cc.Year_Level-1)
	            AND cc2.Semester=cc.Semester
	            AND g2.Grade IS NOT NULL
	            AND g2.Grade IN (5) 
		)
	GROUP BY (c.Academic_Year+cc.Year_Level-1), cc.Semester
	UNION ALL
	SELECT 'Dropped' AS Result, (c.Academic_Year+cc.Year_Level-1) AS Year, cc.Semester, COUNT(DISTINCT(us.Student_ID)) AS Count
	FROM user_student us
		INNER JOIN curriculum c ON us.Curriculum_ID=c.Curriculum_ID
	    INNER JOIN curriculum_courses cc ON c.Curriculum_ID=cc.Curriculum_ID
	    INNER JOIN program p ON c.Program_ID=p.Program_ID
		LEFT JOIN grades g ON us.Student_ID=g.Student_ID AND cc.Course_ID=g.Course_ID
	WHERE cc.Semester BETWEEN 1 AND 2
		AND CONCAT((c.Academic_Year+cc.Year_Level-1),'_',cc.Semester) IN ($sql_year_sem)
		$sql_usertype
		AND g.Grade IS NOT NULL
	    AND us.Student_ID IN (
	        SELECT us2.Student_ID
	        FROM user_student us2
	            INNER JOIN curriculum c2 ON us2.Curriculum_ID=c2.Curriculum_ID
	            INNER JOIN curriculum_courses cc2 ON c2.Curriculum_ID=cc2.Curriculum_ID 
	            LEFT JOIN grades g2 ON us2.Student_ID=g2.Student_ID AND cc2.Course_ID=g2.Course_ID
	        WHERE us2.Student_ID=us.Student_ID
	            AND (c2.Academic_Year+cc2.Year_Level-1) = (c.Academic_Year+cc.Year_Level-1)
	            AND cc2.Semester=cc.Semester
	            AND g2.Grade IS NOT NULL
	            AND g2.Grade IN (6) 
		)
	GROUP BY (c.Academic_Year+cc.Year_Level-1), cc.Semester
");

if(!$sql || $sql->num_rows <= 0){
	echo json_encode(array(
		"xValues" => array_reverse($xValues),
		"yValues" => $yValues
	));
	exit();
}

while($obj = $sql->fetch_object()) {
	$yValues[$obj->Result][$indexes[$obj->Year.'_'.$obj->Semester]] = intval($obj->Count);
}

echo json_encode(array(
	"xValues" => array_reverse($xValues),
	"yValues" => $yValues
));
?>