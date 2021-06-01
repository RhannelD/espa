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
		SELECT u.User_ID, u.Firstname, u.Lastname, u.Gender, u.Email, u.User_Type, ud.Department_ID
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

<form id="formOfficerEditing">
	<div class="row">
		<div class="col-12">
			<div class="form-row">
				<div class="form-group col-6">
				  	<label for="c_user_id">User ID</label>
				  	<input type="text" name="sr_code" class="form-control c_student_sr_code" id="c_student_sr_code" value="<?php echo htmlspecialchars($officer->User_ID); ?>" disabled>
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-6">
				  	<label for="c_firstname">Firstname</label>
				  	<input type="text" name="firstname" class="form-control c_firstname" id="c_firstname" placeholder="Firstname" <?php echo $instance->getValidations('firstname'); ?> value="<?php echo htmlspecialchars($officer->Firstname); ?>">
				</div>
				<div class="form-group col-md-6">
				  	<label for="c_lastname">Lastname</label>
				  	<input type="text" name="lastname" class="form-control c_lastname" id="c_lastname" placeholder="Lastname" <?php echo $instance->getValidations('lastname'); ?> value="<?php echo htmlspecialchars($officer->Lastname); ?>">
				</div>
			</div>

			<div>
				<label>Gender:</label>
			  	<div class="form-check form-check-inline">
					<input class="form-check-input c_gender" type="radio" name="c_gender" id="male" value="male" <?php echo (($officer->Gender)=='male')? "checked": ""; ?>>
					<label class="form-check-label" for="male">Male</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input c_gender" type="radio" name="c_gender" id="female" value="female" <?php echo (($officer->Gender)=='female')? "checked": ""; ?>>
					<label class="form-check-label" for="female">Female</label>
				</div>
			</div>

			<div class="form-group">
			  	<label for="c_email">Email</label>
			  	<input type="email" name="email" class="form-control c_email" id="c_email" placeholder="juan.delacruz@g.batstate-u-edu.ph" <?php echo $instance->getValidations('email'); ?> value="<?php echo htmlspecialchars($officer->Email); ?>" disabled>
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
