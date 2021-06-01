<?php  
	include "../Validations/login_input_validate.php";

    $instance = new LoginValidate();
?>

<h3>Find Account by Username or ID</h3>

<form id="find_user_form">
    <div class="form-group mt-2">
        <label for="username_find">Username</label>
        <input type="text" name="title" class="form-control c_username_find" id="username_find" placeholder="Username" <?php echo $instance->getValidations('username'); ?>>
    </div>
    <button type="submit" class="btn btn-info btn-block">
        Find Account
    </button>
</form>