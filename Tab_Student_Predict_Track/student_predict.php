<?php  
	include "../database/dbconnection.php";
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include "../Standard_Functions/year_sem.php";
	include "../Standard_Functions/grade_converter.php";
	include "../Standard_Functions/semester_schedule.php";
	include "../Standard_Functions/per_semester_units.php";

    $UserAuth = unserialize($_SESSION['UserAuth']);
    $UserType = $UserAuth->get_UserType();

	if(empty($_REQUEST['sr_code'])) {
		exit();
    }
	$sr_code = $mysqli->real_escape_string($_REQUEST['sr_code']);

	$sql = $mysqli->query("
		SELECT us.SR_Code, CONCAT(u.Firstname, ' ', u.Lastname) AS Name, c.Curriculum_ID, sd.Department_Title, p.Program_Code, p.Program_Title, c.Track, YEAR(Academic_Year) AS 'AcademicYear', 
            GROUP_CONCAT(cr.Reference SEPARATOR ', ') AS 'References' 
        FROM `curriculum` c
            INNER JOIN program p ON c.Program_ID=p.Program_ID
        	INNER JOIN department sd ON p.Department_ID=sd.Department_ID
            INNER JOIN curriculum_references cr ON c.Curriculum_ID=cr.Curriculum_ID 
            INNER JOIN user_student us ON c.Curriculum_ID = us.Curriculum_ID 
            INNER JOIN user u ON us.User_ID = u.User_ID
        WHERE us.SR_Code = '$sr_code'
	");
	if(!$sql)
		exit();
  	if($sql->num_rows <= 0)
  		exit();
	while ($obj = $sql -> fetch_object()){
  		$Student = $obj;
  		break;
  	}

  	$course_array = array(0);
?>

<hr>
<div class="row mt-1 px-2">
	<div class="col-md-6">
		<div class="student_sr_code"><?php echo htmlspecialchars($Student->SR_Code); ?></div>
		<div class="student_name"><?php echo htmlspecialchars($Student->Name); ?></div>
		<div class="student_department"><?php echo htmlspecialchars($Student->Department_Title); ?></div>
		<div class="student_program" id="<?php echo htmlspecialchars($Student->Program_Code); ?>"><?php echo htmlspecialchars($Student->Program_Title); ?></div>
	</div>

	<div class="col-md-6">
		<div class="student_references"><?php echo htmlspecialchars($Student->References); ?></div>
		<div class="student_track"><?php echo htmlspecialchars($Student->Track); ?></div>
		<div class="student_academic_year"><?php echo htmlspecialchars($Student->AcademicYear.'-'.(intval($Student->AcademicYear)+1)); ?></div>
		<?php  
		if ($UserType != 'STD'){
			?>
			<div class="d-flex justify-content-end row">	
				<button class="btn btn-dark mb-1 student_open_back" id="<?php echo $Student->SR_Code; ?>">
					<i class="fas fa-arrow-circle-left"></i>
					Back
				</button>
			</div>
			<?php
		}
		?>
	</div>
</div>

<hr>
<div class="alert alert-success font-weight-bold my-4 border-success">
	Course Already Taken
</div>

<?php  
	$sql = $mysqli->query("
		SELECT DISTINCT Year_Level, Semester
		FROM curriculum_courses cc
			INNER JOIN user_student us ON cc.Curriculum_ID = us.Curriculum_ID 
            INNER JOIN grades g ON us.Student_ID=g.Student_ID AND cc.Course_ID=g.Course_ID
        WHERE us.SR_Code = '$sr_code'
	");
	if(!$sql)
		exit();
	if($sql->num_rows > 0){
		while ($obj = $sql -> fetch_object()) {
		    ?>
		    <div class="my-3">
				<div class="card border-dark">
					<h5 class="card-header bg-dark text-white font-weight-bold">
						<?php echo getYear($obj->Year_Level); ?> / <?php echo getSem($obj->Semester); ?>
					</h5>
					<div class="table-responsive">
						<table class="table card-body table-sm table-hover my-0 table-borderless">
							<thead class="bg-secondary text-white">
								<tr>
									<th class="text-nowrap text-center">Grade</th>
									<th class="text-nowrap">Code</th>
									<th class="text-nowrap">Course</th>
									<th class="text-nowrap">Unit/s</th>
									<th class="text-nowrap">Lec</th>
									<th class="text-nowrap">Lab</th>
									<th class="text-nowrap">Pre-Requisite/s</th>
									<th class="text-nowrap">Co-Requisite/s</th>
								</tr>
							</thead>
							<?php  
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
                                    INNER JOIN user_student us ON c.Curriculum_ID = us.Curriculum_ID
                                    INNER JOIN grades g ON us.Student_ID=g.Student_ID AND cc.Course_ID=g.Course_ID
								WHERE cc.Year_Level= $obj->Year_Level 
									AND cc.Semester= $obj->Semester 
									AND us.SR_Code = '$sr_code'
								GROUP BY cs.Course_Code ORDER BY cc.CuCo_Code
							");
							if(!$courses)
								exit();
						  	while ($course = $courses -> fetch_object()) {
						  		?>
						  		<tbody>
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

										<td class="text-center">
											<?php  
											//Grades
											$grades = $mysqli->query("
												SELECT g.Grade 
												FROM grades g 
													INNER JOIN user_student us ON g.Student_ID = us.Student_ID 
												WHERE us.SR_Code = '$sr_code'
													AND g.Course_ID = '$course->Course_ID'
											");
											if (!$grades) {
												exit();
											}
											if ($grades->num_rows == 0) {
												?>
												<span>-</span>
												<?php
											}

											$rows = $grades->num_rows;
											$iteration = 1;
											while ($grade = $grades -> fetch_object()){
												// Adding Course to exclude on to be course to be taken
												if ($grade->Grade <= 3)
													array_push($course_array, $course->Course_ID);

												// Displaying record
												?>
												<span class="<?php echo ($grade->Grade > 3)? 'text-danger': ''; ?>">
													<?php echo convert_grade($grade->Grade).(($iteration++<$rows)?',':''); ?>
												</span>
												<?php
											}
											?>
										</td>
										<td class="text-nowrap"><?php echo htmlspecialchars($course->Course_Code); ?></td>
										<td class="text-nowrap"><?php echo htmlspecialchars($course->Course_Title); ?></td>
										<td class="text-nowrap"><?php echo htmlspecialchars($course->Units); ?></td>
										<td class="text-nowrap"><?php echo htmlspecialchars($lecture); ?></td>
										<td class="text-nowrap"><?php echo htmlspecialchars($laboratory); ?></td>
										<td class="text-nowrap"><?php echo htmlspecialchars($prereq); ?></td>
										<td class="text-nowrap"><?php echo htmlspecialchars($coreq); ?></td>
									</tr>
								</tbody>
						  		<?php
							}
							$courses->free_result();
							?>
						</table>
					</div>
				</div>
			</div>
		    <?php
		}
	} else {
	  	?>
	  	<div class="alert alert-info mt-2">
	  		No Results
	  	</div>
	  	<?php
	}
	$sql -> free_result();
?>

<hr>
<div class="alert alert-info font-weight-bold my-4 border-info">
	Course To be Taken
</div>

<?php
	// Getting the Units per Year and Sem base on database records
	$UnitsPerYearSemOnRecords = array();	// [Array] Variable holder for units per year and sem
	$sql = $mysqli->query("
		SELECT (c.Academic_Year+cc.Year_Level-1) AS Year, cc.Semester, SUM(cs.Units) AS Total_Units
		FROM user_student us 
			INNER JOIN curriculum c ON us.Curriculum_ID=c.Curriculum_ID 
		    INNER JOIN curriculum_courses cc ON c.Curriculum_ID=cc.Curriculum_ID
		    INNER JOIN courses cs ON cc.Course_ID = cs.Course_ID
		WHERE us.SR_Code = '$sr_code'
		GROUP BY cc.Year_Level, cc.Semester
	");
	if ($sql) {
		while ($obj = $sql->fetch_object()) {
			$UnitsPerYearSemOnRecords[strval($obj->Year)][strval($obj->Semester)] = $obj->Total_Units;
		}
	}

	// Get the current year and semester
	$temp 			= new SemesterSchedule();
	$CurrentYearSem = $temp->getCurrentSchoolYearAndTriSem();
	$CurrentYear 	= $CurrentYearSem['year'];
	$CurrentSem 	= $CurrentYearSem['sem'];
	$YearCount		= count($UnitsPerYearSemOnRecords);

	for ($YearLoop=$CurrentYear; $YearLoop <= ($CurrentYear+$YearCount); $YearLoop++) {
		for ($SemLoop=$CurrentSem; $SemLoop <= 3; $SemLoop++) { 
			if($SemLoop == 3 && !(isset($UnitsPerYearSemOnRecords[$YearLoop][$SemLoop]))) {
				continue;
			}
			$courses = $mysqli->query("
				SELECT  c.Course_ID, c.Course_Code, c.Course_Title, c.Units, c.Lecture, c.Laboratory, c.`Req Standing` AS req,
                    (SELECT GROUP_CONCAT(c1.Course_Code SEPARATOR ', ')
                     FROM pre_requisites p INNER JOIN courses c1 ON p.Pre_Requisite =c1.Course_ID
                     WHERE p.Course_ID=c.Course_ID) as 'PreReqs' ,
				    (SELECT GROUP_CONCAT(cs2.Course_Code SEPARATOR ',') 
	                 FROM courses cs2 
	                 	INNER JOIN pre_requisites p2 ON cs2.Course_ID=p2.Course_ID 
	                 	INNER JOIN curriculum_courses cc2 ON cc2.Course_ID=cs2.Course_ID
	                 WHERE p2.Pre_Requisite=cc.Course_ID 
	                	AND cc2.Curriculum_ID=cc.Curriculum_ID) AS CoReqs
                FROM user_student us 
                    INNER JOIN curriculum_courses cc ON us.Curriculum_ID = cc.Curriculum_ID
                    INNER JOIN courses c ON cc.Course_ID = c.Course_ID
                    LEFT JOIN grades g ON us.Student_ID = g.Student_ID AND g.Course_ID = c.Course_ID
                WHERE us.SR_Code = '$sr_code'
                    AND (g.Grade IS NULL 
                    OR (SELECT MIN(g2.Grade) FROM grades g2 WHERE g2.Course_ID = c.Course_ID AND g2.Student_ID = us.Student_ID GROUP BY g2.Course_ID) > 3)
                    AND c.Course_ID NOT IN (
                        SELECT pr3.Course_ID 
                        FROM pre_requisites pr3  
                        WHERE pr3.Course_ID=c.Course_ID 
                            AND pr3.Pre_Requisite NOT IN (0,".implode(',',$course_array).")
                    ) 
                    AND c.Course_ID NOT IN (0,".implode(',',$course_array).")
                GROUP BY cc.Course_ID
                ORDER BY cc.Year_Level, cc.Semester, cc.CuCo_Code
			");
			if(!$courses)
				continue;
			if($courses->num_rows==0){
				break;
			}
			?>
		    <div class="my-3">
				<div class="card border-dark">
					<h5 class="card-header bg-dark text-white font-weight-bold">
						<?php echo $YearLoop.'-'.($YearLoop+1); ?> / <?php echo getSem($SemLoop); ?>
					</h5>
					<div class="table-responsive">
						<table class="table card-body table-sm table-hover my-0 table-borderless">
							<thead class="bg-secondary text-white">
								<tr>
									<th class="text-nowrap">Code</th>
									<th class="text-nowrap">Course</th>
									<th class="text-nowrap">Unit/s</th>
									<th class="text-nowrap">Lec</th>
									<th class="text-nowrap">Lab</th>
									<th class="text-nowrap">Pre-Requisite/s</th>
									<th class="text-nowrap">Co-Requisite/s</th>
								</tr>
							</thead>
							<?php  
							$CourseUnitsSum	= 0;
							$CourseUnitsLimit = (isset($UnitsPerYearSemOnRecords[$YearLoop][$SemLoop]))? ($UnitsPerYearSemOnRecords[$YearLoop][$SemLoop]): getSemesterUnits($SemLoop);
							if(($SemLoop==1||$SemLoop==2)&&$CourseUnitsLimit<getSemesterUnits($SemLoop)) {
								$CourseUnitsLimit=getSemesterUnits($SemLoop);
							}

						  	while ($course = $courses -> fetch_object()) {
						  		if ($CourseUnitsSum>$CourseUnitsLimit) {
						  			break;
						  		}

						  		array_push($course_array, $course->Course_ID);
						  		$CourseUnitsSum += intval($course->Units);
						  		?>
						  		<tbody>
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

										<td class="text-nowrap"><?php echo htmlspecialchars($course->Course_Code); ?></td>
										<td class="text-nowrap"><?php echo htmlspecialchars($course->Course_Title); ?></td>
										<td class="text-nowrap"><?php echo htmlspecialchars($course->Units); ?></td>
										<td class="text-nowrap"><?php echo htmlspecialchars($lecture); ?></td>
										<td class="text-nowrap"><?php echo htmlspecialchars($laboratory); ?></td>
										<td class="text-nowrap"><?php echo htmlspecialchars($prereq); ?></td>
										<td class="text-nowrap"><?php echo htmlspecialchars($coreq); ?></td>
									</tr>
								</tbody>
						  		<?php
							}
							$courses->free_result();
							?>
						</table>
					</div>
				</div>
			</div>
			<?php
		}
		$CurrentSem = 1;
	}
?>
