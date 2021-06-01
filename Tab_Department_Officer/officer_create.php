<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include '../database/dbconnection.php';
	include '../Validations/officer_input_validate.php';
	include "../Standard_Functions/user_departments.php";

	$UserAuth = unserialize($_SESSION['UserAuth']);
    $user_type = $UserAuth->get_UserType();
    $user_id = $UserAuth->get_UserID();
	$department = getUserDepartments($mysqli, $user_id);

	$query_dept = "";
    if ($user_type == 'CHP') {
    	$query_dept = " WHERE Department_ID = '$department'";
    }

	$departments = $mysqli->query("
		SELECT Department_ID, Department_Title FROM department $query_dept
	");

	$instance = new OfficerValidate();

?>

<form id="formOfficerCreation">
	<div class="row">
		<div class="col-12">
			<div class="form-row">
				<div class="form-group col-md-6">
				  	<label for="c_firstname">Firstname</label>
				  	<input type="text" name="firstname" class="form-control c_firstname" id="c_firstname" placeholder="Firstname" <?php echo $instance->getValidations('firstname'); ?>>
				</div>
				<div class="form-group col-md-6">
				  	<label for="c_lastname">Lastname</label>
				  	<input type="text" name="lastname" class="form-control c_lastname" id="c_lastname" placeholder="Lastname" <?php echo $instance->getValidations('lastname'); ?>>
				</div>
			</div>

			<div class="form-group">
			  	<label for="c_email">Email</label>
			  	<input type="email" name="email" class="form-control c_email" id="c_email" placeholder="juan.delacruz@g.batstate-u-edu.ph" <?php echo $instance->getValidations('email'); ?>>
			</div>

			<div>
				<label>Gender:</label>
			  	<div class="form-check form-check-inline">
					<input class="form-check-input c_gender" type="radio" name="c_gender" id="male" value="male" checked>
					<label class="form-check-label" for="male">Male</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input c_gender" type="radio" name="c_gender" id="female" value="female">
					<label class="form-check-label" for="female">Female</label>
				</div>
			</div>

			<div class="form-group">
				<label for="c_department">Department</label>
				<select class="custom-select bg-light c_department" id="c_department" <?php echo $instance->getValidations('department');  echo ($user_type=='CHP')? " DISABLED": "";  ?>>
					<option value="">Select Department</option>
				  	<?php  
				  		if ($result = $departments) {
				  			while ($obj = $result -> fetch_object()) {
				  				?>
				  				<option value="<?php echo htmlspecialchars($obj->Department_ID); ?>" <?php echo ($user_type=='CHP')? " SELECTED": ""; ?> ><?php echo htmlspecialchars($obj->Department_Title); ?></option>
				  				<?php
						  	}
				  		}
				  		$departments->free_result();
				  	?>
				</select>
			</div>

			<div class="form-group">
				<label for="c_officer">User Type</label>
				<select class="custom-select bg-light c_officer" id="c_officer" <?php echo $instance->getValidations('officer'); echo ($user_type=='CHP')? " DISABLED": ""; ?>>
					<option value="">Select Officer Type</option>
				  	<option value="CHP">Department Admin</option>
				  	<option value="EVL" <?php echo ($user_type=='CHP')? " SELECTED": ""; ?> >Evaluator</option>
				</select>
			</div>
			
		    <div class="form-group">
		        <label for="c_password">Password</label>
		        <div class="input-group mb-3">
		        	<input type="password" name="c_password" class="form-control c_password" id="c_password" placeholder="Password" autocomplete="off" <?php echo $instance->getValidations('password'); ?>>
				  	<div class="input-group-append">
				    	<a class="input-group-text show_password" id="basic-addon2">
				    		<i class="fa fa-eye-slash" aria-hidden="true"></i>
				    	</a>
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
