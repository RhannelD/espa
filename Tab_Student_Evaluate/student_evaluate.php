<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include "../database/dbconnection.php";
	include "../Standard_Functions/year_sem.php";
	include "../Standard_Functions/grade_converter.php";
	include "../Standard_Functions/per_semester_units.php";
	include "../Validations/proposal_slip_input_validate.php";

	if(empty($_REQUEST['sr_code'])) {
		exit();
    }
	$sr_code 	= $mysqli->real_escape_string($_REQUEST['sr_code']);
	$request_id = $mysqli->real_escape_string($_REQUEST['request_id']);

	$sql = $mysqli->query("
		SELECT us.SR_Code, CONCAT(u.Firstname, ' ', u.Lastname) AS Name, c.Curriculum_ID, sd.Department_Title, p.Program_Code, p.Program_Title, c.Track, YEAR(Academic_Year) AS 'AcademicYear', 
            GROUP_CONCAT(cr.Reference SEPARATOR ', ') AS 'References' 
        FROM `curriculum` c
            INNER JOIN program p ON c.Program_ID=p.Program_ID
        	INNER JOIN department sd ON p.Department_ID=sd.Department_ID
            INNER JOIN curriculum_references cr ON c.Curriculum_ID=cr.Curriculum_ID 
            INNER JOIN user_student us ON c.Curriculum_ID = us.Curriculum_ID 
            INNER JOIN user u ON us.User_ID = u.User_ID
        WHERE us.SR_Code = '$sr_code'
	");
	if(!$sql)
		exit();
  	if($sql->num_rows <= 0)
  		exit();
	while ($obj = $sql -> fetch_object()){
  		$Student = $obj;
  		break;
  	}

	$instance = new ProposalSlipValidate();
?>

<div class="row mt-1 px-2">
	<div class="col-md-6">
		<div class="student_sr_code"><?php echo htmlspecialchars($Student->SR_Code); ?></div>
		<div class="student_name"><?php echo htmlspecialchars($Student->Name); ?></div>
		<div class="student_department"><?php echo htmlspecialchars($Student->Department_Title); ?></div>
		<div class="student_program" id="<?php echo htmlspecialchars($Student->Program_Code); ?>"><?php echo htmlspecialchars($Student->Program_Title); ?></div>
	</div>

	<div class="col-md-6">
		<div class="student_references"><?php echo htmlspecialchars($Student->References); ?></div>
		<div class="student_track"><?php echo htmlspecialchars($Student->Track); ?></div>
		<div class="student_academic_year"><?php echo htmlspecialchars($Student->AcademicYear.'-'.(intval($Student->AcademicYear)+1)); ?></div>
		<div class="d-flex justify-content-end row">	
			<button class="btn btn-dark mb-1 student_evaluate_back" id="<?php echo $Student->SR_Code; ?>">
				<i class="fas fa-arrow-circle-left"></i>
				Back
			</button>
		</div>
	</div>
</div>

<ul class="nav nav-tabs" id="myTab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="evaluation-tab" data-toggle="tab" href="#evaluation" role="tab" aria-controls="evaluation" aria-selected="true">
			Evaluation
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="courses-tab" data-toggle="tab" href="#courses" role="tab" aria-controls="courses" aria-selected="false">
			Courses
		</a>
	</li>
</ul>
<div class="tab-content" id="myTabContent">
	<div class="tab-pane fade show active pt-2" id="evaluation" role="tabpanel" aria-labelledby="evaluation-tab">	
		<div class="row">
			<div class="col-lg-3 order-lg-last">
				<?php  
				if ($request_id > 0) {
					?>
					<div class="alert alert-info m-x mb-1">
						Evaluation by Request
					</div>
					<?php
				}
				?>

				<div class="input-group mb-3">
					<div class="input-group-prepend">
						<span class="input-group-text bg-dark text-white">Units</span>
					</div>
					<input type="number" class="form-control text-right bg-white total_units" disabled value="0">
					<div class="input-group-append">
						<span class="input-group-text">/</span>
					</div>
					<div class="input-group-append maximum_units">
						<span class="input-group-text"><?php echo getSemesterUnits(1); ?></span>
					</div>
				</div>


				<div class="card border-secondary mb-1">
					<h5 class="card-header bg-secondary text-white text-center font-weight-bold">
						Actions
					</h5>
					<div class="card-body p-2">
						<button class="btn btn-primary mb-1 btn-block add_courses">
							<i class="fas fa-plus-circle"></i>
							Add Courses
						</button>
						<button class="btn btn-info mb-1 btn-block autoload_courses">
							<i class="fas fa-truck-loading"></i>
							Autoload Courses
						</button>
						<button class="btn btn-danger mb-1 btn-block remove_all_courses">
							<i class="fas fa-trash-alt"></i>
							Remove All Courses
						</button>
					</div>
				</div>

				<div class="card border-secondary mb-1">
					<div class="card-body p-2">
						<button class="btn btn-success mb-1 btn-block print_proposal">
							<i class="fas fa-print"></i>
							Print Proposal Slip
						</button>
						<button class="btn btn-success mb-1 btn-block upload_proposal">
							<i class="fas fa-file-upload"></i>
							Upload Proposal Slip
						</button>
					</div>
				</div>
			</div>

			<div class="col-lg-9">
				<div class="card border-dark">
					<h5 class="card-header bg-dark text-white font-weight-bold">
						Student Evaluation
					</h5>
				</div>

				<div class="table-responsive">

					<table class="table table-sm table-hover mt-2" id="course_table">
				        <thead class="bg-secondary text-white">
				            <tr>
				                <th>Code</th>
				                <th>Title</th>
				                <th class='text-nowrap text-center'>Units</th>
				                <th class='text-nowrap text-center'>Lec</th>
				                <th class='text-nowrap text-center'>Lab</th>
				                <th class="text-center">Action</th>
				            </tr>
				        </thead>
				        <tbody class="added_courses">

		                    <tr class="course_added_none">
		                        <td class="text-nowrap alert-info" colspan="6">None</td>
		                    </tr>

			        	</tbody>
	    			</table>

    			</div>
			</div>
		</div>
	</div>

	<div class="tab-pane fade" id="courses" role="tabpanel" aria-labelledby="courses-tab">
		<div class="row mb-1">
			<div class="input-group col-lg-6 mt-2">
				<div class="input-group rounded">
					<input type="search" class="form-control rounded course_search_input" placeholder="Search Courses" aria-label="Search" aria-describedby="course_search_icon"/>
					<span class="input-group-text border-0 course_search_icon">
						<i class="fas fa-search"></i>
					</span>
				</div>
			</div>
			<div class="col-lg-4 mt-2">
				<div class="input-group rounded">
				  	<div class="input-group-prepend">
				    	<label class="input-group-text" for="to_be_query">Show</label>
				  	</div>
					<select class="custom-select to_be_query" id='to_be_query'>
					    <option value="Unfinished" selected>Courses to be taken</option>
					    <option value="Curriculum">Curriculum Courses</option>
					    <option value="All">All Courses</option>
					</select>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="contents-container col-12 main-tablebar mb-2 table_courses">
				
			</div>
		</div>
	</div>
</div>


<div class="upload_proposal_modal">

	<div class="modal fade" id="upload_proposal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	    <div class="modal-dialog modal-dialog-centered" role="document">
	      	<form class="modal-content" id="formProposalUpload">
		        <div class="modal-header bg-dark text-white">
		          <h5 class="modal-title" id="exampleModalCenterTitle">Upload Proposal Slip</h5>
		          	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		              	<span aria-hidden="true"><i class="fas fa-times-circle text-white"></i></span>
		          	</button>
		        </div>
		        <div class="modal-body proposal_uploading">
					<div class="form-row">
						<div class="form-group col-6">
						  	<label for="c_student_sr_code">SR-Code</label>
						  	<input type="text" name="sr_code" class="form-control" value="<?php echo htmlspecialchars($Student->SR_Code); ?>" disabled>
						</div>

						<?php  
						if ($request_id > 0) {
							?>
							<div class="form-group col-6">
							  	<label for="c_student_sr_code">Request ID</label>
							  	<input type="text" name="request_id" class="form-control" value="<?php echo htmlspecialchars($request_id); ?>" disabled>
							</div>
							<?php
						}
						?>
					</div>

					<div class="form-row">
						<div class="form-group col-12">
						  	<label for="c_student_lastname">Student Name</label>
						  	<input type="text" name="lastname" class="form-control c_student_lastname" id="c_student_lastname" value="<?php echo htmlspecialchars($Student->Name); ?>" disabled>
						</div>
					</div>
					<div class="form-group">
					    <label for="description">Send Message</label>
					    <textarea class="form-control c_description" id="description" rows="3" <?php echo $instance->getValidations('description'); ?>></textarea>
					</div>

		        </div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-info">
						<i class="fas fa-file-upload"></i>
						Upload
					</button>
					<button type="button" data-dismiss="modal" class="btn btn-secondary" id="cancel_edit">
						<i class="fas fa-times"></i>
						Cancel
					</button>
				</div>	
	      	</form>
	    </div>
	</div>

</div>

