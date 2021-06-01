<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include '../database/dbconnection.php';
	include '../Validations/officer_input_validate.php';

	if(empty($_REQUEST['id'])) {
	    exit();
	}
	$id = $mysqli->real_escape_string($_REQUEST['id']);

	$sql = $mysqli->query("
		SELECT u.User_ID, CONCAT(u.Firstname, ' ', u.Lastname) AS Name   
		FROM user u 
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

	$instance = new OfficerValidate();
?>

<form id="formOfficerChangePassword">
	<div class="form-row">
		<div class="form-group col-6">
		  	<label for="c_id">User ID</label>
		  	<input type="text" name="sr_code" class="form-control c_id" id="c_id" value="<?php echo htmlspecialchars($officer->User_ID); ?>" disabled>
		</div>
	</div>

	<div class="form-group">
	  	<label for="c_name">Student Name</label>
	  	<input type="text" name="sr_code" class="form-control c_name" id="c_name" value="<?php echo htmlspecialchars($officer->Name); ?>" disabled>
	</div>

    <div class="form-group">
        <label for="password">Password</label>
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control c_password" id="password" placeholder="Password" <?php echo $instance->getValidations('password'); ?>>
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
