<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include "../database/dbconnection.php";
	include "../Standard_Functions/year_sem.php";

	if(empty($_REQUEST['id'])) {
		exit();
    }
	$id = $_REQUEST['id'];

	$sql = $mysqli->query("
		SELECT c.Curriculum_ID, sd.Department_Title, p.Program_Code, p.Program_Title, c.Track, YEAR(Academic_Year) AS 'AcademicYear', 
            GROUP_CONCAT(cr.Reference SEPARATOR ', ') AS 'References' 
        FROM `curriculum` c
            INNER JOIN program p ON c.Program_ID=p.Program_ID
        	INNER JOIN department sd ON p.Department_ID=sd.Department_ID
            INNER JOIN curriculum_references cr ON c.Curriculum_ID=cr.Curriculum_ID 
        WHERE c.Curriculum_ID = $id
	");
	if(!$sql)
		exit();
  	if($sql->num_rows <= 0)
  		exit();
	while ($obj = $sql -> fetch_object()){
  		$Curriculum = $obj;
  		break;
  	}
?>

<div class="row mt-1 p-2">
	<div class="col-md-6">
		<div class="curriculum_department"><?php echo htmlspecialchars($Curriculum->Department_Title); ?></div>
		<div class="curriculum_program" id="<?php echo htmlspecialchars($Curriculum->Program_Code); ?>"><?php echo htmlspecialchars($Curriculum->Program_Title); ?></div>
		<div class="curriculum_references"><?php echo htmlspecialchars($Curriculum->References); ?></div>
	</div>

	<div class="col-md-6">
		<div class="curriculum_track"><?php echo htmlspecialchars($Curriculum->Track); ?></div>
		<div class="curriculum_academic_year"><?php echo htmlspecialchars($Curriculum->AcademicYear.'-'.(intval($Curriculum->AcademicYear)+1)); ?></div>
		<div class="d-flex justify-content-end mt-1">	
			<button class="btn btn-info curriculum_print" id="<?php echo htmlspecialchars($Curriculum->Curriculum_ID); ?>">
				<i class="far fa-file-alt"></i>
			  	Print Curriculum
			</button>
			<button class="btn btn-dark ml-1 curriculum_open_back" id="<?php echo $Curriculum->Curriculum_ID; ?>">
				<i class="fas fa-arrow-circle-left"></i>
				Back
			</button>
		</div>
	</div>
</div>

<?php  
	$sql = $mysqli->query("
		SELECT DISTINCT Year_Level, Semester
		FROM curriculum_courses
		WHERE Curriculum_ID = $id
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
								WHERE cc.Year_Level= $obj->Year_Level 
									AND cc.Semester= $obj->Semester 
									AND cc.Curriculum_ID= $id
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