<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include '../database/dbconnection.php';
	include '../Validations/request_input_validate.php';

	$instance = new RequestValidate();
?>

<form id="formRequestCreation">
	<div class="modal-body">
		<div class="form-group">
		    <label>Message:</label>
		    <textarea class="form-control bg-white c_message" rows="3" <?php echo $instance->getValidations('message'); ?>></textarea>
	    </div>

	    <div class="form-group col-12">
		  	<input type="file" name="file[]" multiple class="form-control-file form-control-file c_file" id="add_file" accept='.jpg,.jpeg,.png,.pdf'>
		  	<label class="custom-file-label dept_logo" for="add_file">Upload Files</label>
		</div>

		<div class="card px-1 pt-1 mb-1 selected_file_preview collapse">
			
		</div>
	</div>

	<div class="modal-footer">
		<button type="submit" class="btn btn-info">
			<i class="fas fa-paper-plane"></i>
			Send Request
		</button>
		<button type="button" data-dismiss="modal" class="btn btn-secondary" id="cancel_edit">
			<i class="fas fa-times"></i>
			Cancel
		</button>
	</div>	  	
</form>
