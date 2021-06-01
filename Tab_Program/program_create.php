<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include '../database/dbconnection.php';
	include '../Validations/program_input_validate.php';
	include "../Standard_Functions/user_departments.php";

	$UserAuth = unserialize($_SESSION['UserAuth']);
    $user_type = $UserAuth->get_UserType();
    $user_id = $UserAuth->get_UserID();
	$department = getUserDepartments($mysqli, $user_id);

	$query_dept = "";
    if (in_array($user_type, array('CHP', 'EVL'))) {
    	$query_dept = " WHERE Department_ID = '$department'";
    }

	$departments = $mysqli->query("
		SELECT Department_ID, Department_Title FROM department $query_dept
	");

	$instance = new ProgramValidate();
?>

<form id="formProgramCreation">
	<div class="form-group">
	  	<label for="c_program_code">Program Code</label>
	  	<input type="text" name="code" class="form-control c_program_code" id="c_program_code" placeholder="Program Code" <?php echo $instance->getValidations('code'); ?>>
	</div>
	<div class="form-group">
	  	<label for="c_program_title">Program Title</label>
	  	<input type="text" name="title" class="form-control c_program_title" id="c_program_title" placeholder="Program Title" <?php echo $instance->getValidations('title'); ?>>
	</div>
	<div class="form-group">
		<label for="c_program_department">Department</label>
		<select class="custom-select bg-light c_program_department" id="c_program_department" <?php echo $instance->getValidations('department'); echo (in_array($user_type, array('CHP', 'EVL')))? " DISABLED": "";?>>
			<option value="">Select Department</option>
		  	<?php  
		  		if ($result = $departments) {
		  			while ($obj = $result -> fetch_object()) {
		  				?>
		  				<option value="<?php echo htmlspecialchars($obj->Department_ID); ?>" <?php echo (in_array($user_type, array('CHP', 'EVL')))? " SELECTED": ""; ?>>
		  					<?php echo htmlspecialchars($obj->Department_Title); ?>
		  				</option>
		  				<?php
				  	}
		  		}
		  		$departments->free_result();
		  	?>
		</select>
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
