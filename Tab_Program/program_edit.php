<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include '../database/dbconnection.php';
	include '../Validations/program_input_validate.php';
    include "../Standard_Functions/user_departments.php";

    $UserAuth = unserialize($_SESSION['UserAuth']);
    $user_id = $UserAuth->get_UserID();
    $user_type = $UserAuth->get_UserType();

	if(empty($_REQUEST['id'])) {
        exit();
    }
  	$Program_ID = $_REQUEST['id'];

    $sql_usertype = '';
    if ($user_type == 'CHP' || $user_type == 'EVL') {
        $sql_usertype = 'WHERE Department_ID = '.getUserDepartments($mysqli, $user_id);
    }
    
	$departments = $mysqli->query("
		SELECT Department_ID, Department_Title FROM department
			$sql_usertype
	");

	$sql = $mysqli->query("
        SELECT p.Program_ID, p.Program_Code, p.Program_Title, d.Department_ID 
        FROM program p 
        	INNER JOIN department d ON p.Department_ID=d.Department_ID 
        WHERE p.Program_ID = $Program_ID 
	");

    while ($obj = $sql -> fetch_object()){
  		$Program = $obj;
  		break;
  	}
  	$sql->free_result();

	$instance = new ProgramValidate();
?>

<form id="formProgramEditing">
	<div class="form-row">
		<div class="input-group input-group-md mb-3 col-md-12">
		  	<div class="input-group-prepend">
		    	<span class="input-group-text" id="inputGroup-sizing-sm">Program ID</span>
		  	</div>
		  	<div class="form-control c_program_id">
		  		<?php echo htmlspecialchars($Program->Program_ID); ?>
		  	</div>
		</div>
	</div>
	<div class="form-group">
	  	<label for="c_program_code">Program Code</label>
	  	<input type="text" name="code" class="form-control c_program_code" id="c_program_code" placeholder="Program Code" value="<?php echo htmlspecialchars($Program->Program_Code); ?>" <?php echo $instance->getValidations('code'); ?>>
	</div>
	<div class="form-group">
	  	<label for="c_program_title">Program Title</label>
	  	<input type="text" name="title" class="form-control c_program_title" id="c_program_title" placeholder="Program Title" value="<?php echo htmlspecialchars($Program->Program_Title); ?>" <?php echo $instance->getValidations('title'); ?>>
	</div>
	<div class="form-group">
		<label for="c_program_department">Department</label>
		<select class="custom-select bg-light c_program_department" id="c_program_department" <?php echo $instance->getValidations('department'); echo (in_array($user_type, array('CHP', 'EVL')))? " DISABLED": "";?>>
			<option value="">Select Department</option>
		  	<?php  
		  		if ($result = $departments) {
		  			while ($obj = $result -> fetch_object()) {
		  				?>
		  				<option value="<?php echo htmlspecialchars($obj->Department_ID); ?>" <?php echo ($obj->Department_ID==$Program->Department_ID)?"selected":""; ?>><?php echo htmlspecialchars($obj->Department_Title); ?></option>
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
			Save
		</button>
		<button type="button" data-dismiss="modal" class="btn btn-secondary" id="cancel_edit">
			<i class="fas fa-times"></i>
			Cancel
		</button>
	</div>	  	
</form>
