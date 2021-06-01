<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include '../database/dbconnection.php';
	include '../Validations/curriculum_input_validate.php';
	include "../Standard_Functions/user_departments.php";

	$UserAuth = unserialize($_SESSION['UserAuth']);
    $user_type = $UserAuth->get_UserType();
    $user_id = $UserAuth->get_UserID();
	$department = getUserDepartments($mysqli, $user_id);

	$query_dept = "";
    if ($user_type == 'CHP' || $user_type == 'EVL') {
    	$query_dept = " WHERE Department_ID = '$department'";
    }

    $departments = $mysqli->query("
		SELECT Department_ID, Department_Title FROM department $query_dept
	");
	
	$instance = new CurriculumValidate();
?>

<form id="formCurriculumCreation">
	<div class="row">
		<div class="col-lg-6">
			<div class="form-group">
				<label for="c_curriculum_department">Department</label>
				<select class="custom-select bg-light c_curriculum_department" id="c_curriculum_department" <?php echo $instance->getValidations('department'); echo (in_array($user_type, array('CHP', 'EVL')))? " DISABLED": ""; ?>>
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

			<div class="form-group">
				<label for="c_curriculum_program">Program</label>
				<select class="custom-select bg-light c_curriculum_program" id="c_curriculum_program" <?php echo $instance->getValidations('program'); ?>>
					<!-- <option value="">Select Program</option> -->
					<?php  
					if (in_array($user_type, array('CHP', 'EVL'))){
						include "curriculum_load_select_program.php";
					}
					?>
				</select>
			</div>

			<div class="form-group">
			  	<label for="c_curriculum_track">Track</label>
			  	<input type="text" class="form-control c_curriculum_track" id="c_curriculum_track" placeholder="Track">
			</div>

			<div class="form-group">
	    		<label for="c_curriculum_academic_year">Academic Year</label>
	    		<input type="number" class="form-control c_curriculum_academic_year" id="c_curriculum_academic_year" value="<?php echo date('Y'); ?>" <?php echo $instance->getValidations('academic_year'); ?>>
	  		</div>
		</div>

		<div class="col-lg-6">
			<div class="form-row mb-2">
				<label class="col-6">Reference/s</label>
				<div class="col-6 d-flex flex-row-reverse">
			  		<button type="button"  class="float-right btn btn-info btn-sm add_reference">
			  			<i class="fas fa-plus-circle"></i>
			  		  	Add
			  		</button>
				</div>
				<div class="col-12 mt-1">
		    		<div class="card container-fluid">
		    			<div class="row c_curriculum_references">

		    				<?php include_once 'curriculum_add_input_reference.php'; ?>

		    			</div>
		    		</div>
		  		</div>
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
