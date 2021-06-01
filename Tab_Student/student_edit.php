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
		SELECT us.SR_Code, u.Firstname, u.Lastname, u.Gender, u.Email, u.Password  
		FROM user u 
			INNER JOIN user_student us ON u.User_ID = us.User_ID 
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

	$instance = new StudentValidate();
?>

<form id="formStudentEditing">
	<div class="form-row">
		<div class="form-group col-6">
		  	<label for="c_student_sr_code">SR-Code</label>
		  	<input type="text" name="sr_code" class="form-control c_student_sr_code" id="c_student_sr_code" value="<?php echo htmlspecialchars($student->SR_Code); ?>" disabled>
		</div>
	</div>

	<div class="form-row">
		<div class="form-group col-md-6">
		  	<label for="c_student_firstname">Firstname</label>
		  	<input type="text" name="firstname" class="form-control c_student_firstname" id="c_student_firstname" placeholder="Firstname" <?php echo $instance->getValidations('firstname'); ?> value="<?php echo htmlspecialchars($student->Firstname); ?>">
		</div>
		<div class="form-group col-md-6">
		  	<label for="c_student_lastname">Lastname</label>
		  	<input type="text" name="lastname" class="form-control c_student_lastname" id="c_student_lastname" placeholder="Lastname" <?php echo $instance->getValidations('lastname'); ?> value="<?php echo htmlspecialchars($student->Lastname); ?>">
		</div>
	</div>

	<div class="form-group">
	  	<label for="c_student_email">Email</label>
	  	<input type="email" name="email" class="form-control c_student_email" id="c_student_email" placeholder="juan.delacruz.@g.batstate-u-edu.ph" value="<?php echo htmlspecialchars($student->Email); ?>">
	</div>

	<div>
		<label>Gender:</label>
	  	<div class="form-check form-check-inline">
			<input class="form-check-input c_student_gender" type="radio" name="c_student_gender" id="male" value="male" <?php echo (($student->Gender)=='male')? "checked": ""; ?>>
			<label class="form-check-label" for="male">Male</label>
		</div>
		<div class="form-check form-check-inline">
			<input class="form-check-input c_student_gender" type="radio" name="c_student_gender" id="female" value="female"<?php echo (($student->Gender)=='female')? "checked": ""; ?>>
			<label class="form-check-label" for="female">Female</label>
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
