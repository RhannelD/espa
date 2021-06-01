<?php  
	if (!isset($mysqli))
		include "../database/dbconnection.php";
	if (!isset($id))
		$id = $_REQUEST['id'];
	if (!isset($year))
		$year = $_REQUEST['year'];
	if (!isset($sem))
		$sem = $_REQUEST['sem'];

	$courses = $mysqli->query("
		SELECT cs.Course_ID, cs.Course_Code, cs.Course_Title, cs.Units, cs.Lecture, cs.Laboratory, cs.`Req Standing` AS req,
			(SELECT GROUP_CONCAT(cs1.Course_Code SEPARATOR ',') FROM courses cs1 INNER JOIN pre_requisites p1 
		     	ON cs1.Course_ID=p1.Pre_Requisite WHERE p1.Course_ID=cs.Course_ID)  AS PreReqs,
		    (SELECT GROUP_CONCAT(cs2.Course_Code SEPARATOR ',') 
             FROM courses cs2 
             	INNER JOIN pre_requisites p2 ON cs2.Course_ID=p2.Course_ID 
             	INNER JOIN curriculum_courses cc2 ON cc2.Course_ID=cs2.Course_ID
             WHERE p2.Pre_Requisite=cs.Course_ID 
            	AND cc2.Curriculum_ID=cc.Curriculum_ID) AS CoReqs
		FROM curriculum c 
			INNER JOIN curriculum_courses cc ON c.Curriculum_ID=c.Curriculum_ID
		    INNER JOIN courses cs ON cc.Course_ID=cs.Course_ID
		WHERE cc.Year_Level= $year 
			AND cc.Semester= $sem 
			AND cc.Curriculum_ID= $id
		GROUP BY cs.Course_Code ORDER BY cc.CuCo_Code
	");
	if(!$courses)
		exit();
	if($courses->num_rows <= 0) {
		?>
		<tr>
			<td colspan="8" class="text-nowrap">
				No Results
			</td>
		</tr>
		<?php
	}
	while ($course = $courses -> fetch_object()) {
	?>
	<tr>
		<?php  
		$lecture = (intval($course->Lecture)==0)? "-": $course->Lecture;
  		$laboratory = (intval($course->Laboratory)==0)? "-": $course->Laboratory;

  		$coreq = ($course->CoReqs=="")? "-": $course->CoReqs;
  		$prereq = ($course->PreReqs=="")? null: $course->PreReqs;
  		if ($course->PreReqs == ""){
  			if ($course->req == "") {
  				$prereq = "-";
  			} else {
  				$prereq = $course->req;
  			}
  		} else {
  			$prereq = $course->PreReqs;
  		}
		?>
		<td class="text-nowrap course_code_<?php echo htmlspecialchars($course->Course_ID); ?>"><?php echo htmlspecialchars($course->Course_Code); ?></td>
		<td class="text-nowrap"><?php echo htmlspecialchars($course->Course_Title); ?></td>
		<td class="text-nowrap"><?php echo htmlspecialchars($course->Units); ?></td>
		<td class="text-nowrap"><?php echo htmlspecialchars($lecture); ?></td>
		<td class="text-nowrap"><?php echo htmlspecialchars($laboratory); ?></td>
		<td class="text-nowrap"><?php echo htmlspecialchars($prereq); ?></td>
		<td class="text-nowrap"><?php echo htmlspecialchars($coreq); ?></td>
		<td class="text-nowrap py-0" width="20px">
			<button class="btn btn-sm btn-danger my-0 delete_course" id="<?php echo htmlspecialchars($course->Course_ID); ?>">
				<i class="fas fa-trash"></i>
				Remove
			</button>
		</td>
	</tr>
	<?php
	}
?>