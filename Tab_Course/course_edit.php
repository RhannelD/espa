<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
  	include "../database/dbconnection.php";
    include '../Validations/course_input_validate.php';

    $instance = new CourseValidate();

    if(empty($_REQUEST['id'])) {
        exit();
    }
  	$CourseID = $mysqli->real_escape_string($_REQUEST['id']);

    // If course can be edited
    $sql = $mysqli->query("
      SELECT True 
      FROM grades 
      WHERE Course_ID = '$CourseID'
      LIMIT 1
    ");
    if ($sql) {
      if ($sql->num_rows > 0) {
        ?>
        <div class="alert alert-info">Course can't be edited. Student's has grades already in this course.</div>
        <?php
        exit();
      }
    }

    // Course query
  	$sql = $mysqli->query("
    	SELECT c.Course_ID, c.Course_Code, c.Course_Title, c.Units, c.Lecture, c.Laboratory, c.`Req Standing` AS 'Req', 
      	(SELECT GROUP_CONCAT(c1.Course_Code SEPARATOR ', ') AS 'Pre-requisite/s' 
     	 	FROM pre_requisites p 
      		INNER JOIN courses c1 ON p.Pre_Requisite =c1.Course_ID WHERE p.Course_ID=c.Course_ID) as 'PreReq' 
    	FROM courses c WHERE c.Course_ID Like '$CourseID'
  	");

    while ($obj = $sql -> fetch_object()){
  		$Course = $obj;
  		break;
  	}


  	$sql = $mysqli->query("
    	SELECT c.Course_ID, c.Course_Code, c.Course_Title 
    	FROM pre_requisites p INNER JOIN courses c ON p.Pre_Requisite=c.Course_ID 
    	WHERE p.Course_ID = $CourseID
  	");

?>


<form id="formCourseEditing">
    <div class="row">
        <div class="col-xl-6">
        	<div class="form-row">
        		<div class="input-group input-group-md mb-3">
        		  	<div class="input-group-prepend">
        		    	<span class="input-group-text" id="inputGroup-sizing-sm">Course ID</span>
        		  	</div>
        		  	<div class="form-control" id="e_course_id">
        		  		<?php echo htmlspecialchars($Course->Course_ID); ?>
        		  	</div>
        		</div>
        	</div>
        	<div class="form-group">
        	  	<label for="e_course_code">Course Code</label>
        	  	<input type="text" class="form-control e_course_code" id="e_course_code" placeholder="Course Code" value="<?php echo htmlspecialchars($Course->Course_Code); ?>" <?php echo $instance->getValidations('code'); ?>>
        	</div>
        	<div class="form-group">
        	  	<label for="e_course_title">Course Title</label>
        	  	<input type="text" class="form-control e_course_title" id="e_course_title" placeholder="Course Title" value="<?php echo htmlspecialchars($Course->Course_Title); ?>" <?php echo $instance->getValidations('title'); ?>>
        	</div>
        	<div class="form-row">
        		<div class="form-group col-md-4">
            		<label for="e_units">Units</label>
            		<input type="number" class="form-control e_course_units" id="e_units" value="<?php echo htmlspecialchars($Course->Units); ?>" <?php echo $instance->getValidations('units'); ?>>
              	</div>
            	<div class="form-group col-md-4">
                	<label for="e_lecture">Lecture</label>
                	<input type="number" class="form-control e_course_lecture" id="e_lecture" value="<?php echo htmlspecialchars($Course->Lecture); ?>" <?php echo $instance->getValidations('lecture'); ?>>
              	</div>
              	<div class="form-group col-md-4">
                	<label for="e_laboratory">Laboratory</label>
                	<input type="number" class="form-control e_course_laboratory" id="e_laboratory" value="<?php echo htmlspecialchars($Course->Laboratory); ?>" <?php echo $instance->getValidations('laboratory'); ?>>
              	</div>
        	</div>
            <div class="form-group">
                <label for="e_req_standing">Req Standing</label>
                <input type="text" class="form-control e_course_req_standing" id="e_req_standing" placeholder="Req Standing" value="<?php echo htmlspecialchars($Course->Req); ?>" <?php echo $instance->getValidations('req_standing'); ?>>
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
            					<?php 
                                if ($sql -> num_rows == 0) {
                                    ?>
                                    <tr class="item-prereq-none">
                                        <td colspan="3" class="text-nowrap">None</td>
                                    </tr>
                                    <?php
                                }
          						while ($result = $sql -> fetch_object()){
          							?>
          							<tr class="item-prereq-<?php echo htmlspecialchars($result->Course_ID); ?>" id="<?php echo htmlspecialchars($result->Course_ID); ?>">
          								<td class="text-nowrap"><?php echo htmlspecialchars($result->Course_Code); ?></td>
          								<td class="text-nowrap"><?php echo htmlspecialchars($result->Course_Title); ?></td>
          								<td class="text-nowrap text-danger delete-prereq" id="<?php echo htmlspecialchars($result->Course_ID); ?>"><i class="fas fa-trash"></i></td>
          							</tr>
          							<?php	
          		                }
            					?>
            				</tbody>
            			</table>
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
