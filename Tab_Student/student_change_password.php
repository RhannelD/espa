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
		SELECT us.SR_Code, CONCAT(u.Firstname, ' ', u.Lastname) AS Name  
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

<form id="formStudentChangePassword">
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
        <label for="c_student_password">Password</label>
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control c_student_password" id="c_student_password" placeholder="Password">
            <div class="input-group-append">
                <a class="input-group-text show_password" id="basic-addon2">
                    <i class="fa fa-eye-slash" aria-hidden="true"></i>
                </a>
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
