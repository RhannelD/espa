<?php  
	include "../database/dbconnection.php";

	if(!empty($_REQUEST['sr_code'])) {
		$sr_code = $_REQUEST['sr_code']; 
	}

	if(!empty($_REQUEST['max_units'])) {
		$max_units = $_REQUEST['max_units']; 
	}

	$added_courses = "0";
	if(!empty($_REQUEST['added_courses'])) {
		$added_courses = implode(",",$_REQUEST['added_courses']);
	}

	$units = 0;
	if(!empty($_REQUEST['units'])) {
		$units = $_REQUEST['units'];
	}

	$sql = $mysqli->query("
		SELECT  c.Course_ID, c.Course_Code, c.Course_Title, c.Units, c.Lecture, c.Laboratory,
            (SELECT GROUP_CONCAT(c1.Course_Code SEPARATOR ', ')
            FROM pre_requisites p INNER JOIN courses c1 ON p.Pre_Requisite =c1.Course_ID
            WHERE p.Course_ID=c.Course_ID) as 'Prereq' 
        FROM user_student us 
            INNER JOIN curriculum_courses cc ON us.Curriculum_ID = cc.Curriculum_ID
            INNER JOIN courses c ON cc.Course_ID = c.Course_ID
            LEFT JOIN grades g ON us.Student_ID = g.Student_ID AND g.Course_ID = c.Course_ID
        WHERE us.SR_Code = '$sr_code'
            AND (g.Grade IS NULL 
            OR (SELECT MIN(g2.Grade) FROM grades g2 WHERE g2.Course_ID = c.Course_ID AND g2.Student_ID = us.Student_ID GROUP BY g2.Course_ID) > 3)
            AND c.Course_ID NOT IN 
                (SELECT pr3.Course_ID 
                 FROM user_student s3 
                    INNER JOIN pre_requisites pr3 
                    LEFT JOIN grades g3 ON s3.Student_ID=g3.Student_ID AND g3.Course_ID=pr3.Pre_Requisite 
                 WHERE pr3.Course_ID=c.Course_ID 
                    AND s3.Student_ID=us.Student_ID 
                    AND (g3.Grade IS NULL OR 
                        (SELECT MIN(g4.Grade) FROM user_student s4 
                            INNER JOIN grades g4 ON s4.Student_ID=g4.Student_ID 
                        WHERE g4.Course_ID=pr3.Pre_Requisite AND s4.Student_ID=us.Student_ID)>3  ) ) 
            AND c.Course_ID NOT IN ($added_courses)
        GROUP BY cc.Course_ID
        ORDER BY cc.Year_Level, cc.Semester, cc.CuCo_Code
	");

    if (!$sql) {
    	exit();
    }
    while ($course_data = $sql -> fetch_object()) {
    	$units += $course_data->Units;
    	if ($units > $max_units)
    		break;
    	?>
    	<tr class='course_added' id='<?php echo $course_data->Course_ID; ?>'>
		    <td class='text-nowrap'><?php echo $course_data->Course_Code; ?></td>
		    <td class='text-nowrap'><?php echo $course_data->Course_Title; ?></td>
		    <td class='text-nowrap text-center course_added_units'><?php echo $course_data->Units; ?></td>
		    <td class='text-nowrap text-center'><?php echo $course_data->Lecture; ?></td>
		    <td class='text-nowrap text-center'><?php echo $course_data->Laboratory; ?></td>
		    <td class='text-nowrap text-center'>
		        <button type='button' class='btn btn-danger btn-sm remove_course_added'>
		            <i class='fas fa-trash'></i>
		            Remove
		        </button>
		    </td>
		</tr>
    	<?php
    }
?>