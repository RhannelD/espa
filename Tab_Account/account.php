<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include "../database/dbconnection.php";
	include '../Validations/account_input_validate.php';
	include '../Validations/recovery_answer_input_validate.php';

	$instance = new AccountValidate();
	$recovery = new RecoveryAnswerValidate();

	$UserAuth = unserialize($_SESSION['UserAuth']);
    $user_id = $UserAuth->get_UserID();


	$sql = $mysqli->query("
		SELECT u.User_ID, u.User_Type, u.Firstname, u.Lastname, u.Gender, u.Email, us.SR_Code  
		FROM user u 
			LEFT JOIN user_department ud ON u.User_ID = ud.User_ID 
		    LEFT JOIN department d ON ud.Department_ID = d.Department_ID
		    LEFT JOIN user_student us ON u.User_ID = us.User_ID 
		    LEFT JOIN curriculum c ON us.Curriculum_ID = c.Curriculum_ID 
		    LEFT JOIN program p ON c.Program_ID = p.Program_ID
		WHERE u.User_ID = '$user_id'
	");
	if(!$sql)
		exit();
  	if($sql->num_rows <= 0)
  		exit();
	while ($obj = $sql -> fetch_object()){
  		$account = $obj;
  		break;
  	}
?>

<div class="row">

	<div class="contents-container col-md-6 main-tablebar mb-2 card_account ">
		<div class="card my-5 my">
			<h4 class="card-header bg-dark text-white">Account Info</h4>
			<div class="card-body">

				<form id="formAccountEditing">
					<div class="form-row">
						<div class="form-group col-6">
						  	<label for="c_account_id">Account ID</label>
						  	<input type="text" name="sr_code" class="form-control c_account_id" id="c_account_id" value="<?php echo htmlspecialchars($account->User_ID); ?>" disabled>
						</div>
						<?php  
						if (!empty($account->SR_Code)){
							?>
							<div class="form-group col-6">
							  	<label for="c_account_sr_code">SR-Code</label>
							  	<input type="text" name="sr_code" class="form-control c_account_sr_code" id="c_account_sr_code" value="<?php echo htmlspecialchars($account->SR_Code); ?>" disabled>
							</div>
							<?php
						}
						?>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
						  	<label for="c_account_firstname">Firstname</label>
						  	<input type="text" name="firstname" class="form-control c_account_firstname" id="c_account_firstname" placeholder="Firstname" <?php echo $instance->getValidations('firstname'); ?> value="<?php echo htmlspecialchars($account->Firstname); ?>" disabled>
						</div>
						<div class="form-group col-md-6">
						  	<label for="c_account_lastname">Lastname</label>
						  	<input type="text" name="lastname" class="form-control c_account_lastname" id="c_account_lastname" placeholder="Lastname" <?php echo $instance->getValidations('lastname'); ?> value="<?php echo htmlspecialchars($account->Lastname); ?>" disabled>
						</div>
					</div>

					<div>
						<label>Gender:</label>
					  	<div class="form-check form-check-inline">
							<input class="form-check-input c_account_gender" type="radio" name="c_account_gender" id="male" value="male" <?php echo (($account->Gender)=='male')? "checked": ""; ?> disabled>
							<label class="form-check-label" for="male">Male</label>
						</div>
						<div class="form-check form-check-inline">
							<input class="form-check-input c_account_gender" type="radio" name="c_account_gender" id="female" value="female"<?php echo (($account->Gender)=='female')? "checked": ""; ?> disabled>
							<label class="form-check-label" for="female">Female</label>
						</div>
					</div>
					
					<div class="form-group">
					  	<label for="c_account_email">Email</label>
					  	<input type="email" name="email" class="form-control c_account_email" id="c_account_email" value="<?php echo htmlspecialchars($account->Email); ?>" disabled>
					</div>

					<div class="modal-footer">
						<button class="btn btn-info btn_change_password" type="button" data-toggle="collapse" data-target="#collapseChangePassword" aria-expanded="false" aria-controls="collapseChangePassword">
							<i class="fas fa-pen-square"></i>
						    Change Password
						</button>
						<button type="button" class="btn btn-info btn_edit">
							<i class="fas fa-pen-square"></i>
							Edit
						</button>
						<button type="submit" class="btn btn-info btn_save">
							<i class="fad fa-save"></i>
							Save
						</button>
						<button type="button" class="btn btn-secondary btn_cancel">
							<i class="fas fa-times"></i>
							Cancel
						</button>
					</div>	  	
				</form>

			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="contents-container col-12 form_change_password collapse" id="collapseChangePassword">
			<div class="card my-5 my">
				<h4 class="card-header bg-dark text-white">Change Password</h4>
				<div class="card-body">

					<form id="change_password_form">
					    <div class="form-group">
					        <label for="change_password">Password</label>
					        <div class="input-group mb-3">
					        	<input type="password" name="change_password" class="form-control change_password" id="change_password" placeholder="New Password" autocomplete="off" <?php echo $instance->getValidations('password'); ?>>
							  	<div class="input-group-append">
							    	<a class="input-group-text show_password" id="basic-addon2">
							    		<i class="fa fa-eye-slash" aria-hidden="true"></i>
							    	</a>
							  	</div>
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

				</div>
			</div>
		</div>
	</div>
</div>