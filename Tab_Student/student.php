<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
?>

<div class="row mb-1">
	<div class="input-group col-md-6 mt-2">

		<div class="input-group rounded">
			<input type="search" class="form-control rounded student_search_input" placeholder="Search Students" aria-label="Search" aria-describedby="student_search_icon"/>
			<span class="input-group-text border-0 student_search_icon">
				<i class="fas fa-search"></i>
			</span>
		</div>

	</div>

	<div class="col-md-6 mt-2">

		<div class="input-group rounded">
			<button class="btn btn-info ml-auto mr-0 create_student_open_modal" type="button" data-toggle="modal" data-target="#create_student">
				<i class="fas fa-plus"></i>
				Create Student
			</button>
		</div>

	</div>
</div>

<div class="row">

	<div class="contents-container col-md-6 main-tablebar mb-2 table_student collapse">

	</div>

	<div class="contents-container col-md-6 info_student collapse">

	</div>

	<div class="creating_student_modal">

		<div class="modal fade" id="create_student" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		      	<div class="modal-content">
			        <div class="modal-header bg-dark text-white">
			          <h5 class="modal-title" id="exampleModalCenterTitle">Student Creation</h5>
			          	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			              	<span aria-hidden="true"><i class="fas fa-times-circle text-white"></i></span>
			          	</button>
			        </div>
			        <div class="modal-body student_creating">
			          	
			        </div>
		      	</div>
		    </div>
		</div>

	</div>

  	<div class="editing_student_modal">
	    <div class="modal fade" id="edit_student" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	        <div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header bg-dark text-white">
						<h5 class="modal-title" id="exampleModalCenterTitle">Student Editing</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    <span aria-hidden="true"><i class="fas fa-times-circle text-white"></i></span>
						</button>
					</div>
					<div class="modal-body student_editing">

					</div>
				</div>
	        </div>
	    </div>
  	</div>

  	<div class="shifting_student_modal">
	    <div class="modal fade" id="shift_student" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	        <div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header bg-dark text-white">
						<h5 class="modal-title" id="exampleModalCenterTitle">Program Shifting</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    <span aria-hidden="true"><i class="fas fa-times-circle text-white"></i></span>
						</button>
					</div>
					<div class="modal-body student_shifting">

					</div>
				</div>
	        </div>
	    </div>
  	</div>

  	<div class="password_student_modal">
	    <div class="modal fade" id="password_student" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	        <div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header bg-dark text-white">
						<h5 class="modal-title" id="exampleModalCenterTitle">Change Student Password</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    <span aria-hidden="true"><i class="fas fa-times-circle text-white"></i></span>
						</button>
					</div>
					<div class="modal-body student_password">

					</div>
				</div>
	        </div>
	    </div>
  	</div>
</div>