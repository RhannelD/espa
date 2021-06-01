<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include "../database/dbconnection.php";
	include "../Standard_Functions/year_sem.php";
	include "../Standard_Functions/grade_converter.php";

	if(empty($_REQUEST['sr_code'])) {
		exit();
    }
	$sr_code = $mysqli->real_escape_string($_REQUEST['sr_code']);

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
?>

<div class="row mt-1 p-2">
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
	</div>
</div>

<div class="row mb-1">

	<div class="col-lg-7 mt-2 order-lg-last">
		<div class="row">
			<div class="input-group mb-1 rounded col-sm-2 justify-content-end order-sm-last">
				<button class="btn btn-dark student_open_back" type="button" style="min-width: 90px;" id="<?php echo $Student->SR_Code; ?>">
					<i class="fas fa-arrow-circle-left"></i>
					Back
				</button>
			</div>
			<div class="input-group mb-1 rounded col-sm-6">
			  	<div class="input-group-prepend">
			    	<label class="input-group-text" for="to_be_query">Show</label>
			  	</div>
				<select class="custom-select to_be_query" id='to_be_query'>
				    <option value="Curriculum" selected>Curriculum Courses</option>
				    <option value="Unfinished">Unfinished Courses</option>
				    <option value="All">All Courses</option>
				</select>
			</div>
			<div class="input-group mb-1 rounded col-sm-4">
			  	<div class="input-group-prepend">
			    	<label class="input-group-text" for="number_of_rows">Rows</label>
			  	</div>
				<select class="custom-select number_of_rows" id='number_of_rows'>
				    <option value="15" selected>15</option>
				    <option value="25">25</option>
				    <option value="50">50</option>
				    <option value="100">100</option>
				    <option value="200">200</option>
				</select>
			</div>
		</div>
	</div>

	<div class="input-group col-lg-5 mt-2">
		<div class="input-group mb-1 rounded">
			<input type="search" class="form-control rounded course_search_input" placeholder="Search Courses" aria-label="Search" aria-describedby="course_search_icon"/>
			<span class="input-group-text border-0 course_search_icon">
				<i class="fas fa-search"></i>
			</span>
		</div>
	</div>

</div>

<div class="row">
	<div class="contents-container col-12 main-tablebar mb-2 table_courses">

	</div>
</div>


<div class="confirm_delete_grade_modal">

	<div class="modal fade" id="confirm_delete_grade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	    <div class="modal-dialog modal-dialog-centered" role="document">
	      	<div class="modal-content">
		        <div class="modal-header bg-dark text-white">
		          <h5 class="modal-title" id="exampleModalCenterTitle">Program Creation</h5>
		          	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		              	<span aria-hidden="true"><i class="fas fa-times-circle text-white"></i></span>
		          	</button>
		        </div>
		        <div class="modal-body confirming_delete_grade">
		          	
					

		        </div>
	      	</div>
	    </div>
	</div>

</div>
