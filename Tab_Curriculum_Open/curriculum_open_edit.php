<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include "../database/dbconnection.php";
	include "../Standard_Functions/year_sem.php";

	if(empty($_REQUEST['id'])) {
		exit();
    }
	$id = $_REQUEST['id'];

	$sql = $mysqli->query("
		SELECT c.Curriculum_ID, sd.Department_Title, p.Program_Code, p.Program_Title, c.Track, YEAR(Academic_Year) AS 'AcademicYear', 
            GROUP_CONCAT(cr.Reference SEPARATOR ', ') AS 'References' 
        FROM `curriculum` c
            INNER JOIN program p ON c.Program_ID=p.Program_ID
        	INNER JOIN department sd ON p.Department_ID=sd.Department_ID
            INNER JOIN curriculum_references cr ON c.Curriculum_ID=cr.Curriculum_ID 
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
?>

<div class="row mt-1 p-2">
	<div class="col-md-6">
		<div class="curriculum_department"><?php echo htmlspecialchars($Curriculum->Department_Title); ?></div>
		<div class="curriculum_program" id="<?php echo htmlspecialchars($Curriculum->Program_Code); ?>"><?php echo htmlspecialchars($Curriculum->Program_Title); ?></div>
		<div class="curriculum_references"><?php echo htmlspecialchars($Curriculum->References); ?></div>
	</div>

	<div class="col-md-6">
		<div class="curriculum_track"><?php echo htmlspecialchars($Curriculum->Track); ?></div>
		<div class="curriculum_academic_year"><?php echo htmlspecialchars($Curriculum->AcademicYear); ?></div>
		<div class="d-flex justify-content-end mt-1">	
			<button class="btn btn-info ml-1 btn_curriculum_duplicate" type="button" data-toggle="modal" data-target="#duplicate_curriculum">
				<i class="fas fa-copy"></i>
				Duplicate other Curriculum
			</button>
			<button class="btn btn-dark ml-1 curriculum_open_back" id="<?php echo $Curriculum->Curriculum_ID; ?>">
				<i class="fas fa-arrow-circle-left"></i>
				Back
			</button>
		</div>
	</div>
</div>

<?php  
	for ($year=1; $year <= 5; $year++) { 
		for ($sem=1; $sem <= 3; $sem++) {
		    ?>
		    <div class="my-4">
				<div class="card border-dark">
					<div class="card-header bg-dark text-white py-1">
						<h5 class="font-weight-bold float-left my-2 curriculum_title_year_and_sem_<?php echo $year.'_'.$sem; ?>">
							<?php echo getYear($year); ?> / <?php echo getSem($sem); ?>
						</h5>
						<button class="btn btn-info float-right my-0 btn_adding_curriculum_course" id="<?php echo $year.'_'.$sem; ?>" data-toggle="modal" data-target="#curriculum_adding_course">
							Add Course
						</button>
					</div>
					<div class="table-responsive">
						<table class="table card-body table-sm table-hover my-0 table-borderless">
							<thead class="bg-secondary text-white">
								<tr>
									<th class="text-nowrap">Code</th>
									<th class="text-nowrap">Course</th>
									<th class="text-nowrap">Unit/s</th>
									<th class="text-nowrap">Lec</th>
									<th class="text-nowrap">Lab</th>
									<th class="text-nowrap">Pre-Requisite/s</th>
									<th class="text-nowrap">Co-Requisite/s</th>
									<th class="text-nowrap"></th>
								</tr>
							</thead>
					  		<tbody class="tbody_courses_<?php echo $year."_".$sem ; ?>" id="<?php echo $year.'_'.$sem; ?>">

					  			<?php  
					  			include "curriculum_open_edit_persem.php";
					  			?>

							</tbody>
						</table>
					</div>
				</div>
			</div>
		    <?php
		}
	}
	$sql -> free_result();
?>


<div class="curriculum_add_course_modal">

	<div class="modal fade" id="curriculum_adding_course" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
	      	<div class="modal-content">
		        <div class="modal-header bg-dark text-white">
		          <h5 class="modal-title add_course_modal_title" id="exampleModalCenterTitle">Curriculum Adding Course</h5>
		          	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		              	<span aria-hidden="true"><i class="fas fa-times-circle text-white"></i></span>
		          	</button>
		        </div>
		        <div class="modal-body curriculum_add_course"  id="<?php echo $Curriculum->Curriculum_ID; ?>">
		          	<div class="row">
                        <div class="input-group rounded col-lg-6">
                            <input type="search" class="form-control rounded add_course_search_input" placeholder="Search Courses" aria-label="Search" aria-describedby="course_search_icon"/>
                            <span class="input-group-text border-0 add_course_search_icon">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                        <div class="col-lg-6" id="success_alert_add_course">

                        </div>
                    </div>

                    <div class="modal_table_add_course"  style="min-height: 400px;">
                      	
                    </div>
		        </div>
	      	</div>
	    </div>
	</div>

</div>

<div class="curriculum_duplicate_course_modal">

	<div class="modal fade" id="duplicate_curriculum" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
	      	<div class="modal-content">
		        <div class="modal-header bg-dark text-white">
		          <h5 class="modal-title add_course_modal_title" id="exampleModalCenterTitle">Curriculum Duplication</h5>
		          	<button type="button" class="close close_curriculum_duplicate" data-dismiss="modal" aria-label="Close">
		              	<span aria-hidden="true"><i class="fas fa-times-circle text-white"></i></span>
		          	</button>
		        </div>
		        <div class="modal-body curriculum_add_course"  id="<?php echo $Curriculum->Curriculum_ID; ?>" style="min-height: 600px;">
		        	<div class="alert alert-danger">
                		<strong>Warning:</strong> Curriculum Courses Duplication will remove any pre-added courses at the current Curriculum
                	</div>
		          	<div class="row">
                        <div class="input-group rounded col-lg-6">
                            <input type="search" class="form-control rounded duplicate_curriculum_search_input" placeholder="Search Curriculum" aria-label="Search" aria-describedby="course_search_icon"/>
                            <span class="input-group-text border-0 duplicate_curriculum_search_icon">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>

                    <div class="row">
	                    <div class="contents-container col-md-6 modal_table_duplicate_curriculum">
	                      	
	                    </div>

	                    <div class="contents-container col-md-6 modal_info_duplicate_curriculum">
	                      	
	                    </div>
                    </div>
		        </div>
	      	</div>
	    </div>
	</div>

</div>