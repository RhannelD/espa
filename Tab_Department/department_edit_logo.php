<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include '../database/dbconnection.php';

	if(empty($_REQUEST['id'])) {
        exit();
    }
  	$Department_ID = $_REQUEST['id'];
  	$Logo = null;

	$sql = $mysqli->query("
		SELECT Logo
			FROM department
				WHERE Department_ID = $Department_ID
	");

  	if($sql->num_rows <= 0){
  		exit();
  	}

	while ($obj = $sql -> fetch_object()){
  		$Logo = $obj->Logo;
  		break;
  	}
  	$sql->free_result();
?>

<form id="formDepartmentLogoEditing">

	<div class="row">
		<div class="col-lg-12">
			<div class="row">
			  	<div class="col-3">
			  		<img 
			  			class="rounded mx-auto d-block rounded-circle border border-dark dept_add_logo"
			  			id="<?php echo $Logo; ?>"
			  			src="../img/dept_logo/<?php echo $Logo; ?>" 
			  		>
			  	</div>
			  	<div class="form-group col-9">
				    <label for="add_dept_logo">Department Logo</label>
				    <div class="form-group col-12">
					  	<input type="file" class="form-control-file form-control-file c_department_logo" id="add_dept_logo" required>
					  	<label class="custom-file-label dept_logo" for="add_dept_logo">Choose file</label>
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
