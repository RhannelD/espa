<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include '../Validations/course_input_validate.php';

	$instance = new CourseValidate();
?>

<form id="formCourseCreation">
	<div class="row">
		<div class="col-xl-6">
			<div class="form-group">
			  	<label for="c_course_code">Course Code</label>
			  	<input type="text" name="code" class="form-control c_course_code" id="c_course_code" placeholder="Course Code" <?php echo $instance->getValidations('code'); ?>>
			</div>
			<div class="form-group">
			  	<label for="c_course_title">Course Title</label>
			  	<input type="text" name="title" class="form-control c_course_title" id="c_course_title" placeholder="Course Title" <?php echo $instance->getValidations('title'); ?>>
			</div>
			<div class="form-row">
				<div class="form-group col-md-4">
		    		<label for="c_units">Units</label>
		    		<input type="number" name="units" class="form-control c_course_units" id="c_units" value="0" <?php echo $instance->getValidations('units'); ?>>
		  		</div>
				<div class="form-group col-md-4">
		    		<label for="c_lecture">Lecture</label>
		    		<input type="number" name="lecture" class="form-control c_course_lecture" id="c_lecture" value="0" <?php echo $instance->getValidations('lecture'); ?>>
		  		</div>
		  		<div class="form-group col-md-4">
		    		<label for="c_laboratory">Laboratory</label>
		    		<input type="number" name="laboratory" class="form-control c_course_laboratory" id="c_laboratory" value="0" <?php echo $instance->getValidations('laboratory'); ?>>
		  		</div>
			</div>
			<div class="form-group">
				  <label for="c_req_standing">Req Standing</label>
				  <input type="text" name="req_standing" class="form-control c_course_req_standing" id="c_req_standing" placeholder="Req Standing" <?php echo $instance->getValidations('req_standing'); ?>>
			</div>
		</div>


		<div class="col-xl-6">
			<div class="form-row mb-2">
				<label class="col-6">Pre-Requisite/s</label>
				<div class="col-6 d-flex flex-row-reverse">
		  		<button type="button"  class="float-right btn btn-info btn-sm add_prereq" data-toggle="modal" data-target="#add_prerequisite">
		  			<i class="fas fa-plus-circle"></i>
		  		  	Add
		  		</button>
				</div>
				<div class="col-12 mt-1">
		    		<div class="card table-responsive table-responsive-sm">
		    			<table class="table-sm table-hover">
		    				<tbody id="added_prereqs">
				              <tr class="item-prereq-none">
				                <td colspan="3" class="text-nowrap">None</td>
				              </tr>
		    				</tbody>
		    			</table>
		    		</div>
		  		</div>
			</div>  	
		</div>	
	</div>	
	<div class="modal-footer row">
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
