<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include '../database/dbconnection.php';
	include '../Validations/department_input_validate.php';

	$instance = new DepartmentValidate();

	$deans = $mysqli->query("
		SELECT Dean_ID, Name FROM department_dean
	");

	$heads = $mysqli->query("
		SELECT Head_ID, Name FROM department_head
	");

?>

<form id="formDepartmentCreation">

	<div class="row">
		<div class="col-lg-6">
			<div class="row">
			  	<div class="col-3">
			  		<img 
			  			class="rounded mx-auto d-block rounded-circle border border-dark dept_add_logo"
			  			src="../img/icon/Department.png" 
			  		>
			  	</div>
			  	<div class="form-group col-9">
				    <label for="add_dept_logo">Department Logo</label>
				    <div class="form-group col-12">
					  	<input type="file" class="form-control-file form-control-file c_department_logo" id="add_dept_logo" <?php echo $instance->getValidations('logo'); ?> accept='.jpeg,.png'>
					  	<label class="custom-file-label dept_logo" for="add_dept_logo">Choose file</label>
					</div>
			  	</div>
		  	</div>

			<div class="form-group">
			  	<label for="c_department_code">Department Code</label>
			  	<input type="text" name="code" class="form-control c_department_code" id="c_department_code" placeholder="Department Code" <?php echo $instance->getValidations('code'); ?>>
			</div>
			<div class="form-group">
			  	<label for="c_department_title">Department Title</label>
			  	<input type="text" name="title" class="form-control c_department_title" id="c_department_title" placeholder="Department Title" <?php echo $instance->getValidations('title'); ?>>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="form-group">
				<label for="c_department_dean">Department Dean</label>
				<select class="custom-select bg-light c_department_dean" id="c_department_dean" <?php echo $instance->getValidations('dean'); ?>>
					<option value="">Select Dean</option>
				  	<option value="New">Create new</option>
				  	<?php  
				  		if ($result = $deans) {
				  			while ($obj = $result -> fetch_object()) {
				  				?>
				  				<option value="<?php echo htmlspecialchars($obj->Dean_ID); ?>"><?php echo htmlspecialchars($obj->Name); ?></option>
				  				<?php
						  	}
				  		}
				  		$deans->free_result();
				  	?>
				</select>
			</div>
			<div class="card px-1 mb-1 create_new_dean collapse">
				<div class="form-group">
				  	<label for="c_new_dean_name">Dean's Name</label>
				  	<input type="text" name="title" class="form-control c_department_dean_name" id="c_new_dean_name" placeholder="Name">
				</div>
				<div>
					<label>Gender:</label>
				  	<div class="form-check form-check-inline">
						<input class="form-check-input c_department_dean_gender" type="radio" name="c_new_dean_gender" id="dean_male" value="male" checked>
						<label class="form-check-label" for="dean_male">Male</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input c_department_dean_gender" type="radio" name="c_new_dean_gender" id="dean_female" value="female">
						<label class="form-check-label" for="dean_female">Female</label>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="c_department_head">Department Head</label>
				<select class="custom-select bg-light c_department_head" id="c_department_head" <?php echo $instance->getValidations('head'); ?>>
					<option value="">Select Head</option>
				  	<option value="New">Create new</option>
				  	<?php  
				  		if ($result = $heads) {
				  			while ($obj = $result -> fetch_object()) {
				  				?>
				  				<option value="<?php echo htmlspecialchars($obj->Head_ID); ?>"><?php echo htmlspecialchars($obj->Name); ?></option>
				  				<?php
						  	}
				  		}
				  		$heads->free_result();
				  	?>
				</select>
			</div>
			<div class="card px-1 mb-1 create_new_head collapse">
				<div class="form-group">
				  	<label for="c_new_head_name">Head's Name</label>
				  	<input type="text" name="title" class="form-control c_department_head_name" id="c_new_head_name" placeholder="Name">
				</div>
				<div>
					<label>Gender:</label>
				  	<div class="form-check form-check-inline">
						<input class="form-check-input c_department_head_gender" type="radio" name="c_new_head_gender" id="head_male" value="male" checked>
						<label class="form-check-label" for="head_male">Male</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input c_department_head_gender" type="radio" name="c_new_head_gender" id="head_female" value="female">
						<label class="form-check-label" for="head_female">Female</label>
					</div>
				</div>
			</div>
		</div>
	</div>

	</div>
	<div class="modal-footer">
		<button type="submit" class="btn btn-info">
			<i class="fad fa-save"></i>
			Create
		</button>
		<button type="button" data-dismiss="modal" class="btn btn-secondary" id="cancel_edit">
			<i class="fas fa-times"></i>
			Cancel
		</button>
	</div>	  	
</form>
