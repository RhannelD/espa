<?php  
	include '../database/dbconnection.php';
	include "../Validations/login_input_validate.php";

    $instance = new LoginValidate();

    if(!isset($_REQUEST['id'])) {
	    exit();
	}
	
	$id = $_REQUEST['id'];

    // Verify Login
	$sql = $mysqli->query("
		SELECT CONCAT(Firstname, ' ', Lastname) AS Username 
		FROM user WHERE User_ID = $id
	");
	if (!$sql) {
		// Alert if Failed to Verify
		echo json_encode(array(
			"alert" => "error",
			"title" => "Couldn't Process!",
			"message" => "Please try again later"
		));
		exit();
	}

	while ($obj = $sql -> fetch_object()) {
		$username = $obj->Username;
	}
?>

<h3>Change Password</h3>

<form id="change_password_form">
	<div class="form-row">
	    <div class="form-group col-4">
	        <label for="forgot_password_id">User ID</label>
	        <input type="text" name="id" class="form-control forgot_password_id" id="forgot_password_id" disabled value="<?php echo htmlspecialchars($id); ?>">
	    </div>
	    <div class="form-group col-8">
	        <label for="forgot_password_username">Username</label>
	        <input type="text" name="username" class="form-control forgot_password_username" id="forgot_password_username" disabled value="<?php echo htmlspecialchars($username); ?>	">
	    </div>
	</div>
    <div class="form-group">
        <label for="change_password_new">New Password</label>
        <div class="input-group mb-3">
        	<input type="password" name="change_password_new" class="form-control change_password_new" id="change_password_new" placeholder="New Password" autocomplete="off" <?php echo $instance->getValidations('password'); ?>>
		  	<div class="input-group-append">
		    	<a class="input-group-text show_password" id="basic-addon2">
		    		<i class="fa fa-eye-slash" aria-hidden="true"></i>
		    	</a>
		  	</div>
		</div>
    </div>
    <div class="form-group">
        <label for="change_password_retype">Retype Password</label>
        <div class="input-group mb-3">
        	<input type="password" name="change_password_retype" class="form-control change_password_retype" id="change_password_retype" placeholder="Retype Password" autocomplete="off" <?php echo $instance->getValidations('password'); ?>>
		  	<div class="input-group-append">
		    	<a class="input-group-text show_password" id="basic-addon2">
		    		<i class="fa fa-eye-slash" aria-hidden="true"></i>
		    	</a>
		  	</div>
		</div>
    </div>
    <button type="submit" class="btn btn-info btn-block">
        Change Password
    </button>
</form>