<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include '../database/dbconnection.php';
	include '../Validations/student_input_validate.php';
	include "../Standard_Functions/user_departments.php";

	$UserAuth = unserialize($_SESSION['UserAuth']);
    $user_type = $UserAuth->get_UserType();
    $user_id = $UserAuth->get_UserID();
	$department = getUserDepartments($mysqli, $user_id);

	$query_dept = "";
    if ($user_type == 'CHP' || $user_type == 'EVL') {
    	$query_dept = " WHERE Department_ID = '$department'";
    }

	$programs = $mysqli->query("
		SELECT Program_ID, Program_Title 
		FROM program
		$query_dept
	");

	$instance = new StudentValidate();
?>

<form id="formStudentCreation">
	<div class="row">
		<div class="col-lg-6">
			<div class="form-row">
				<div class="form-group col-6">
				  	<label for="c_student_sr_code">SR-Code</label>
				  	<input type="text" name="sr_code" class="form-control c_student_sr_code" id="c_student_sr_code" placeholder="SR-Code" <?php echo $instance->getValidations('sr_code'); ?>>
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-6">
				  	<label for="c_student_firstname">Firstname</label>
				  	<input type="text" name="firstname" class="form-control c_student_firstname" id="c_student_firstname" placeholder="Firstname" <?php echo $instance->getValidations('firstname'); ?>>
				</div>
				<div class="form-group col-md-6">
				  	<label for="c_student_lastname">Lastname</label>
				  	<input type="text" name="lastname" class="form-control c_student_lastname" id="c_student_lastname" placeholder="Lastname" <?php echo $instance->getValidations('lastname'); ?>>
				</div>
			</div>

			<div class="form-group">
			  	<label for="c_student_email">Email</label>
			  	<input type="email" name="email" class="form-control c_student_email" id="c_student_email" placeholder="juan.delacruz@g.batstate-u-edu.ph" <?php echo $instance->getValidations('email'); ?>>
			</div>

			<div>
				<label>Gender:</label>
			  	<div class="form-check form-check-inline">
					<input class="form-check-input c_student_gender" type="radio" name="c_student_gender" id="male" value="male" checked>
					<label class="form-check-label" for="male">Male</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input c_student_gender" type="radio" name="c_student_gender" id="female" value="female">
					<label class="form-check-label" for="female">Female</label>
				</div>
			</div>

			<div class="form-group">
			  	<label for="c_student_password">Password</label>
			  	<input type="password" name="password" class="form-control c_student_password" id="c_student_password" placeholder="Password">
			</div>
		</div>

		<div class="col-lg-6">
			<div class="form-group">
				<label for="c_student_program">Program</label>
				<select class="custom-select bg-light c_student_program" id="c_student_program" <?php echo $instance->getValidations('program'); ?>>
					<option value="">Select Program</option>
				  	<?php  
				  		if ($result = $programs) {
				  			while ($obj = $result -> fetch_object()) {
				  				?>
				  				<option value="<?php echo htmlspecialchars($obj->Program_ID); ?>"><?php echo htmlspecialchars($obj->Program_Title); ?></option>
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
					<!-- Other options to be loaded -->
				</select>
			</div>

			<div class="form-group">
				<label for="c_student_track">Track</label>
				<select class="custom-select bg-light c_student_track" id="c_student_track" <?php echo $instance->getValidations('track'); ?>>
					<option value="">None</option>
					<!-- Other options to be loaded -->
				</select>
			</div>
		</div>
	</div>

	<div class="modal-footer">
		<button type="submit" class="btn btn-info">
			<i class="fad fa-save"></i>
			Create
		</button>
		<button type="button" data-dismiss="modal" class="btn btn-secondary" id="cancel_edit">
			<i class="fas fa-times"></i>
			Cancel
		</button>
	</div>	  	
</form>
