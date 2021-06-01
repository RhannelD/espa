<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include "../database/dbconnection.php";
	include "../Standard_Functions/year_sem.php";
	include "../Standard_Functions/grade_converter.php";

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
		if ($UserType != 'STD') {
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

			
<div class="row px-2">
	<div class="col-12">
		<div class="d-flex justify-content-end row">
			<?php  
			if ($UserType != 'STD') {
				?>
				<button class="btn btn-info mb-1 student_add_grade" id="<?php echo htmlspecialchars($Student->SR_Code); ?>">
					<i class="far fa-file-alt"></i>
				  	Add Grade
				</button>
				<?php
			}
			?>
			<button class="btn btn-info ml-1 mb-1 student_curriculum_grade_print" id="<?php echo htmlspecialchars($Student->SR_Code); ?>">
				<i class="fas fa-print"></i>
			  	Print Curriculum w/ Grade
			</button>
			<button class="btn btn-info ml-1 mb-1 student_curriculum_print" id="<?php echo htmlspecialchars($Student->SR_Code); ?>">
				<i class="fas fa-print"></i>
			  	Print Blank Curriculum
			</button>

			<button class="btn btn-primary  ml-1 mb-1 " type="button" data-toggle="collapse" data-target="#collapseGWA" aria-expanded="false" aria-controls="collapseGWA">
			    Show GWA
			</button>
		</div>
	</div>
</div>
			
	
<hr>
<div class="my-1 collapse" id="collapseGWA">
	<div class="card border-dark">
		<h5 class="card-header bg-dark text-white font-weight-bold">
			General Weighted Average
		</h5>
		<div class="row px-3 py-2">
			<div class="table-responsive col-md-4">
				<table class="table card-body table-sm table-hover my-0 table-borderless">
					<thead>
						<tr>
							<th colspan="2">GWA By Sem</th>
						</tr>
					</thead>
					<tbody>
						<?php  
						$sql = $mysqli->query("
							SELECT cc.Year_Level, cc.Semester, ((SUM(g.Grade*c.Units))/SUM(c.Units*IF(g.Grade is NULL, 0, 1))) As GWA
							FROM user_student us
								INNER JOIN curriculum_courses cc ON us.Curriculum_ID = cc.Curriculum_ID
							    INNER JOIN courses c ON cc.Course_ID = c.Course_ID
							    LEFT JOIN grades g ON cc.Course_ID = g.Course_ID AND us.Student_ID = g.Student_ID
							WHERE us.SR_Code = '$sr_code'
								AND (g.Grade <= 3
									OR g.Grade is NULL)
							GROUP BY cc.Year_Level, cc.Semester
						");
						if(!$sql)
							exit();
						if($sql->num_rows > 0){
							while ($obj = $sql -> fetch_object()) {
								?>
								<tr>
									<td width="160px">
										<?php echo getNumYearSem($obj->Year_Level); ?> Year/<?php echo getNumYearSem($obj->Semester); ?> Sem
									</td>
									<td>
										<?php echo ($obj->GWA != NULL)? number_format($obj->GWA,2): 0; ?>
									</td>
								</tr>
								<?php
							}
						}
						?>
					</tbody>
				</table>
			</div>
			<div class="table-responsive col-md-4">
				<table class="table card-body table-sm table-hover my-0 table-borderless">
					<thead>
						<tr>
							<th colspan="2">GWA By Year</th>
						</tr>
					</thead>
					<tbody>
						<?php  
						$sql = $mysqli->query("
							SELECT cc.Year_Level, cc.Semester, ((SUM(g.Grade*c.Units))/SUM(c.Units*IF(g.Grade is NULL, 0, 1))) As GWA
							FROM user_student us
								INNER JOIN curriculum_courses cc ON us.Curriculum_ID = cc.Curriculum_ID
							    INNER JOIN courses c ON cc.Course_ID = c.Course_ID
							    LEFT JOIN grades g ON cc.Course_ID = g.Course_ID AND us.Student_ID = g.Student_ID
							WHERE us.SR_Code = '$sr_code'
								AND (g.Grade <= 3
									OR g.Grade is NULL)
							GROUP BY cc.Year_Level
						");
						if(!$sql)
							exit();
						if($sql->num_rows > 0){
							while ($obj = $sql -> fetch_object()) {
								?>
								<tr>
									<td width="160px">
										<?php echo getNumYearSem($obj->Year_Level); ?> Year/<?php echo getNumYearSem($obj->Semester); ?> Sem
									</td>
									<td>
										<?php echo ($obj->GWA != NULL)? number_format($obj->GWA,2): 0; ?>
									</td>
								</tr>
								<?php
							}
						}
						?>
					</tbody>
				</table>
			</div>
			<div class="table-responsive col-md-4">
				<table class="table card-body table-sm table-hover my-0 table-borderless">
					<thead>
						<tr>
							<th colspan="2">Grand GWA</th>
						</tr>
					</thead>
					<tbody>
						<?php  
						$sql = $mysqli->query("
							SELECT cc.Year_Level, cc.Semester, ((SUM(g.Grade*c.Units))/SUM(c.Units*IF(g.Grade is NULL, 0, 1))) As GWA
							FROM user_student us
								INNER JOIN curriculum_courses cc ON us.Curriculum_ID = cc.Curriculum_ID
							    INNER JOIN courses c ON cc.Course_ID = c.Course_ID
							    LEFT JOIN grades g ON cc.Course_ID = g.Course_ID AND us.Student_ID = g.Student_ID
							WHERE us.SR_Code = '$sr_code'
								AND (g.Grade <= 3
									OR g.Grade is NULL)
							GROUP BY us.SR_Code
						");
						if(!$sql)
							exit();
						if($sql->num_rows > 0){
							while ($obj = $sql -> fetch_object()) {
								?>
								<tr>
									<td width="160px">
										GRAND GWA
									</td>
									<td>
										<?php echo ($obj->GWA != NULL)? number_format($obj->GWA,2): 0; ?>
									</td>
								</tr>
								<?php
							}
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php  
	$sql = $mysqli->query("
		SELECT DISTINCT Year_Level, Semester
		FROM curriculum_courses cc
			INNER JOIN user_student us ON cc.Curriculum_ID = us.Curriculum_ID 
        WHERE us.SR_Code = '$sr_code'
	");
	if(!$sql)
		exit();
	if($sql->num_rows > 0){
		while ($obj = $sql -> fetch_object()) {
		    ?>
		    <div class="my-4">
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
								    (SELECT GROUP_CONCAT(cs2.Course_Code SEPARATOR ',') FROM courses cs2 INNER JOIN pre_requisites p2
								    	ON cs2.Course_ID=p2.Course_ID WHERE p2.Pre_Requisite=cs.Course_ID ) AS CoReqs
								FROM user_student us 
									INNER JOIN curriculum c ON c.Curriculum_ID = us.Curriculum_ID
									INNER JOIN curriculum_courses cc ON c.Curriculum_ID=cc.Curriculum_ID
								    INNER JOIN courses cs ON cc.Course_ID=cs.Course_ID
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