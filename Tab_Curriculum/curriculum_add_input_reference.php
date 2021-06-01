<?php  
	if (!class_exists('CurriculumValidate')){
		include '../Validations/curriculum_input_validate.php';
		$instance = new CurriculumValidate();
	}
?>

<div class="input-group mx-1 my-1">
  	<input type="text" class="form-control c_curriculum_reference" placeholder="Reference" <?php echo $instance->getValidations('reference'); ?>>
  	<div class="input-group-append">
    	<button class="btn btn-danger remove_reference" type="button">
    		<i class="fas fa-trash"></i>
    	</button>
  	</div>
</div>