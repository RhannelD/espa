<?php 
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include '../database/dbconnection.php';
	include '../Validations/login_input_validate.php';
    include "../Standard_Functions/grade_converter.php";

	if(empty($_REQUEST['sr_code'])) {
        exit();
    }

  	$sr_code 		= $mysqli->real_escape_string($_REQUEST['sr_code']);
  	$grade_rec_id 	= $mysqli->real_escape_string($_REQUEST['grade_rec_id']);

	$sql = $mysqli->query("
		SELECT g.Grade_Rec_ID, g.Grade, c.Course_Title, us.SR_Code, CONCAT(u.Firstname, ' ', u.Lastname) AS Name
		FROM grades g 
			INNER JOIN user_student us ON g.Student_ID = us.Student_ID
		    INNER JOIN user u ON us.User_ID = u.User_ID 
		    INNER JOIN courses c ON c.Course_ID = g.Course_ID
		WHERE g.Grade_Rec_ID = '$grade_rec_id'
			AND us.SR_Code = '$sr_code'
	");

    while ($obj = $sql -> fetch_object()){
  		$info = $obj;
  		break;
  	}
  	$sql->free_result();

	$instance = new LoginValidate();
?>



<div class="row">
	<div class="form-group col-4">
	  	<label for="c_student_sr_code">SR-Code</label>
	  	<input type="text" name="code" class="form-control c_student_sr_code" id="c_student_sr_code" disabled value="<?php echo htmlspecialchars($info->SR_Code); ?>">
	</div>
	<div class="form-group col-8">
	  	<label for="c_student_name">Student Name</label>
	  	<input type="text" name="code" class="form-control c_student_name" id="c_student_name" disabled value="<?php echo htmlspecialchars($info->Name); ?>">
	</div>
</div>
<div class="form-group">
  	<label for="c_course_title">Course Title</label>
  	<input type="text" name="title" class="form-control c_course_title" id="c_course_title" disabled value="<?php echo htmlspecialchars($info->Course_Title); ?>">
</div>
<div class="row">
	<div class="form-group col-6">
	  	<label for="c_grade_rec_id">Grade Record ID</label>
	  	<input type="text" name="code" class="form-control c_grade_rec_id" id="c_grade_rec_id" disabled value="<?php echo htmlspecialchars($info->Grade_Rec_ID); ?>">
	</div>
	<div class="form-group col-6">
	  	<label for="c_student_grade">Student Grade</label>
	  	<input type="text" name="title" class="form-control c_student_grade" id="c_student_grade" disabled value="<?php echo htmlspecialchars(convert_grade($info->Grade)); ?>">
	</div>
</div>

<form id="formDeleteGradeConfirmation">
	
    <div class="form-group">
        <label for="password">Password</label>
        <div class="input-group mb-3">
            <input type="password" name="title" class="form-control c_password" id="password" placeholder="Password" <?php echo $instance->getValidations('password'); ?>>
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
			Delete
		</button>
		<button type="button" data-dismiss="modal" class="btn btn-secondary" id="cancel_edit">
			<i class="fas fa-times"></i>
			Cancel
		</button>
	</div>	  	
</form>