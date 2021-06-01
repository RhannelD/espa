<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include '../database/dbconnection.php';
	include '../Validations/curriculum_input_validate.php';

	$instance = new CurriculumValidate();

	if(empty($_REQUEST['id'])) {
		exit();
    }
	$id = $_REQUEST['id'];

	// Get Curriculum info
	$sql = $mysqli->query("
		SELECT c.Curriculum_ID, sd.Department_Title, p.Program_Code, p.Program_Title, c.Track, YEAR(Academic_Year) AS 'AcademicYear'
        FROM `curriculum` c
            INNER JOIN program p ON c.Program_ID=p.Program_ID
        	INNER JOIN department sd ON p.Department_ID=sd.Department_ID
        WHERE c.Curriculum_ID = $id
	");
	if(!$sql)
		exit();
  	if($sql->num_rows <= 0)
  		exit();
	while ($obj = $sql -> fetch_object()){
  		$Curriculum = $obj;
  		break;
  	}
  	$sql->free_result();

  	// Get Curriculum References
	$references = $mysqli->query("
		SELECT * FROM `curriculum_references` WHERE Curriculum_ID = $id
	");
	if(!$references)
		exit();
?>

<form id="formCurriculumEditing">
	<div class="row">
		<div class="col-lg-6">
			<div class="form-group">
			  	<label>Curriculum ID</label>
			  	<div class="form-control bg-light c_curriculum_id"><?php echo htmlspecialchars($Curriculum->Curriculum_ID); ?></div>
			</div>
			<div class="form-group">
			  	<label>Department</label>
			  	<div class="form-control bg-light c_curriculum_department"><?php echo htmlspecialchars($Curriculum->Department_Title); ?></div>
			</div>
			<div class="form-group">
			  	<label>Program</label>
			  	<div class="form-control bg-light c_curriculum_program" id="<?php echo htmlspecialchars($Curriculum->Program_Code); ?>"><?php echo htmlspecialchars($Curriculum->Program_Title); ?></div>
			</div>
			<div class="form-group">
	    		<label>Academic Year</label>
	    		<div class="form-control bg-light c_curriculum_academic_year"><?php echo htmlspecialchars($Curriculum->AcademicYear); ?></div>
	  		</div>
		</div>

		<div class="col-lg-6">
			<div class="form-group">
			  	<label for="c_curriculum_track">Track</label>
			  	<input type="text" class="form-control c_curriculum_track" placeholder="Track" value="<?php echo htmlspecialchars($Curriculum->Track); ?>">
			</div>
			<div class="form-row mb-2">
				<label class="col-6">Reference/s</label>
				<div class="col-6 d-flex flex-row-reverse">
			  		<button type="button"  class="float-right btn btn-info btn-sm add_reference">
			  			<i class="fas fa-plus-circle"></i>
			  		  	Add
			  		</button>
				</div>
				<div class="col-12 mt-1">
		    		<div class="card container-fluid">
		    			<div class="row c_curriculum_references">
		    				<?php  
		    				while ($obj = $references -> fetch_object()){
		    					?>
			    				<div class="input-group mx-1 my-1">
								  	<input type="text" class="form-control c_curriculum_reference" placeholder="Reference" <?php echo $instance->getValidations('reference'); ?> value="<?php echo htmlspecialchars($obj->Reference); ?>">
								  	<div class="input-group-append">
								    	<button class="btn btn-danger remove_reference" type="button">
								    		<i class="fas fa-trash"></i>
								    	</button>
								  	</div>
								</div>
								<?php  
						  	}
							?>
		    			</div>
		    		</div>
		  		</div>
			</div>
		</div>
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
