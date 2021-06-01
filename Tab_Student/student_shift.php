<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include '../database/dbconnection.php';
	include '../Validations/student_input_validate.php';

	if(empty($_REQUEST['sr_code'])) {
	    exit();
	}
	$sr_code = $mysqli->real_escape_string($_REQUEST['sr_code']);

	$sql = $mysqli->query("
		SELECT us.SR_Code, CONCAT(u.Firstname, ' ', u.Lastname) AS Name, c.Program_ID, c.Academic_Year, c.Curriculum_ID 
		FROM user u 
			INNER JOIN user_student us ON u.User_ID = us.User_ID 
		    INNER JOIN curriculum c ON us.Curriculum_ID = c.Curriculum_ID
		WHERE us.SR_Code = '$sr_code'
	");
	if(!$sql){
		exit();
	}
	while ($obj = $sql -> fetch_object()){
  		$student = $obj;
  		break;
  	}
  	$sql->free_result();

	$programs = $mysqli->query("
		SELECT Program_ID, Program_Title FROM program
	");

	$years = $mysqli->query("
		SELECT DISTINCT(Academic_Year) AS 'AcademicYear'
        FROM curriculum WHERE Program_ID = $student->Program_ID
        ORDER BY Academic_Year ASC
	");

	$tracks = $mysqli->query("
		SELECT c.Curriculum_ID, c.Track FROM curriculum c  
		WHERE c.Academic_Year LIKE '$student->Academic_Year' 
			AND c.Program_ID = $student->Program_ID
	");

	$instance = new StudentValidate();
?>

<form id="formStudentShifting">
	<div class="form-row">
		<div class="form-group col-6">
		  	<label for="c_student_sr_code">SR-Code</label>
		  	<input type="text" name="sr_code" class="form-control c_student_sr_code" id="c_student_sr_code" value="<?php echo htmlspecialchars($student->SR_Code); ?>" disabled>
		</div>
	</div>

	<div class="form-group">
	  	<label for="c_student_name">Student Name</label>
	  	<input type="text" name="sr_code" class="form-control c_student_name" id="c_student_name" value="<?php echo htmlspecialchars($student->Name); ?>" disabled>
	</div>

	<div class="form-group">
		<label for="c_student_program">Program</label>
		<select class="custom-select bg-light c_student_program" id="c_student_program" <?php echo $instance->getValidations('program'); ?>>
			<option value="">Select Program</option>
		  	<?php  
		  		if ($result = $programs) {
		  			while ($obj = $result -> fetch_object()) {
		  				?>
		  				<option value="<?php echo htmlspecialchars($obj->Program_ID); ?>" <?php echo ($obj->Program_ID==$student->Program_ID)? "selected": ""; ?>><?php echo htmlspecialchars($obj->Program_Title); ?></option>
		  				<?php
				  	}
		  		}
		  		$programs->free_result();
		  	?>
		</select>
	</div>

	<div class="form-group">
		<label for="c_student_academic_year">Academic Year</label>
		<select class="custom-select bg-light c_student_academic_year" id="c_student_academic_year" <?php echo $instance->getValidations('academic_year'); ?>>
			<option value="">Select Academic Year</option>
			<?php  
		  		if ($result = $years) {
		  			while ($obj = $result -> fetch_object()) {
		  				?>
		  				<option value="<?php echo htmlspecialchars($obj->AcademicYear); ?>" id="<?php echo htmlspecialchars($obj->AcademicYear); ?>" <?php echo ($obj->AcademicYear==$student->Academic_Year)? "selected": ""; ?>><?php echo htmlspecialchars($obj->AcademicYear."-".(intval($obj->AcademicYear)+1)); ?></option>
		  				<?php
				  	}
		  		}
		  		$years->free_result();
		  	?>
		</select>
	</div>

	<div class="form-group">
		<label for="c_student_track">Track</label>
		<select class="custom-select bg-light c_student_track" id="c_student_track" <?php echo $instance->getValidations('track'); ?>>
			<?php  
		  		if($tracks->num_rows <= 0){
					?>
					<option value="">None</option>
					<?php  
				}
		  		if ($result = $tracks) {
		  			while ($obj = $result -> fetch_object()) {
		  				?>
		  				<option value="<?php echo htmlspecialchars($obj->Curriculum_ID); ?>" id="<?php echo htmlspecialchars($obj->Curriculum_ID); ?>" <?php echo ($obj->Curriculum_ID==$student->Curriculum_ID	)? "selected": ""; ?>><?php echo htmlspecialchars($obj->Track); ?></option>
		  				<?php
				  	}
		  		}
		  		$tracks->free_result();
		  	?>
		</select>
	</div>

	<div class="modal-footer">
		<button type="submit" class="btn btn-info">
			<i class="fad fa-save"></i>
			Update
		</button>
		<button type="button" data-dismiss="modal" class="btn btn-secondary" id="cancel_edit">
			<i class="fas fa-times"></i>
			Cancel
		</button>
	</div>	  	
</form>
