<?php  
	include '../database/dbconnection.php';
	include "../Validations/login_input_validate.php";

    $instance = new LoginValidate();

    if(!isset($_REQUEST['id'])) {
	    exit();
	}
	
	$id 		= $_REQUEST['id'];
	$username 	= $_REQUEST['username'];
	$random_code= rand(100000,999999); 

    // Verify Login
	$sql = $mysqli->query("
		INSERT INTO `recovery_code` (`Code_ID`, `User_ID`, `Code`) 
		VALUES (NULL, '$id', '$random_code')
	");
	if (!$sql) {
		exit();
	}
?>

<h3>Email Verification</h3>

<form id="forgot_password_form">
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

    <div class="form-group mt-2">
        <div class="card py-2 px-2">
        	An email has been sent with a verification code.
        </div>
    </div>

    <div class="form-group">
        <label for="forgot_password_code">Code</label>
        <input type="text" name="answer" class="form-control forgot_password_code" id="forgot_password_code" placeholder="" autocomplete="off">
    </div>

    <div class="input-group mb-3">
	  	<input type="text" class="form-control bg-white" disabled value="Didn't receive any email?">
	  	<div class="input-group-append">
	    	<button class="btn btn-success btn_resend_code" type="button">Resend Code <i class="fas fa-envelope"></i></button>
	  	</div>
	</div>

    <button type="submit" class="btn btn-info btn-block">
        Submit
    </button>
</form>