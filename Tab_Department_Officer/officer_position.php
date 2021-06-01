<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include '../database/dbconnection.php';
	include '../Validations/officer_input_validate.php';
	include "../Standard_Functions/user_departments.php";

	if(empty($_REQUEST['id'])) {
	    exit();
	}
	$id = $mysqli->real_escape_string($_REQUEST['id']);

	$sql = $mysqli->query("
		SELECT u.User_ID, CONCAT(u.Firstname, ' ', u.Lastname) AS Name, u.User_Type, ud.Department_ID
		FROM user u 
        	LEFT JOIN user_department ud ON u.User_ID = ud.User_ID
		WHERE u.User_ID = '$id'
	");
	if(!$sql){
		exit();
	}
	while ($obj = $sql -> fetch_object()){
  		$officer = $obj;
  		break;
  	}
  	$sql->free_result();


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

<form id="formOfficerPosition">
	<div class="row">
		<div class="col-12">
			<div class="form-row">
				<div class="form-group col-6">
				  	<label for="c_user_id">User ID</label>
				  	<input type="text" name="sr_code" class="form-control c_user_id" id="c_user_id" value="<?php echo htmlspecialchars($officer->User_ID); ?>" disabled>
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-12">
				  	<label for="c_name">Name</label>
				  	<input type="text" name="sr_code" class="form-control c_name" id="c_name" value="<?php echo htmlspecialchars($officer->Name); ?>" disabled>
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
				  				<option value="<?php echo htmlspecialchars($obj->Department_ID); ?>" <?php echo ($user_type=='CHP' || $obj->Department_ID == $officer->Department_ID)? " SELECTED": ""; ?> >
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
				<label for="c_officer">User Type</label>
				<select class="custom-select bg-light c_officer" id="c_officer">
					<option value="">Select Officer Type</option>
					<?php 
					if ($user_type=='ADM') {
						?>
					  	<option value="CHP" <?php echo ($officer->User_Type=='CHP')? " SELECTED": ""; ?> >
					  		Department Admin
					  	</option>
						<?php
					}
					?>
				  	<option value="EVL" <?php echo ($officer->User_Type=='EVL')? " SELECTED": ""; ?> >
				  		Evaluator
				  	</option>
				  	<option value="NAN" <?php echo ($officer->User_Type=='NAN')? " SELECTED": ""; ?> >
				  		None
				  	</option>
				</select>
			</div>
		</div>
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
