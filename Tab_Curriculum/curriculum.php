<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
?>
<div class="row mb-1">
	<div class="input-group col-md-6 mt-2">

		<div class="input-group rounded">
			<input type="search" class="form-control rounded curriculum_search_input" placeholder="Search Curriculums" aria-label="Search" aria-describedby="curriculum_search_icon"/>
			<span class="input-group-text border-0 curriculum_search_icon">
				<i class="fas fa-search"></i>
			</span>
		</div>

	</div>

	<div class="col-md-6 mt-2">

		<div class="input-group rounded">
			<button class="btn btn-info ml-auto mr-0 create_curriculum_open_modal" type="button" data-toggle="modal" data-target="#create_curriculum">
				<i class="fas fa-plus"></i>
				Create Curriculum
			</button>
		</div>

	</div>
</div>

<div class="row">

	<div class="contents-container col-md-6 main-tablebar mb-2 table_curriculum collapse">

	</div>

	<div class="contents-container col-md-6 info_curriculum collapse">

	</div>

	<div class="creating_curriculum_modal">

		<div class="modal fade" id="create_curriculum" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		      	<div class="modal-content">
			        <div class="modal-header bg-dark text-white">
			          <h5 class="modal-title" id="exampleModalCenterTitle">Curriculum Creation</h5>
			          	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			              	<span aria-hidden="true"><i class="fas fa-times-circle text-white"></i></span>
			          	</button>
			        </div>
			        <div class="modal-body curriculum_creating">
			          	
			        </div>
		      	</div>
		    </div>
		</div>

	</div>

  	<div class="editing_curriculum_modal">
	    <div class="modal fade" id="edit_curriculum" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
				<div class="modal-content">
					<div class="modal-header bg-dark text-white">
						<h5 class="modal-title" id="exampleModalCenterTitle">Curriculum Editing</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    <span aria-hidden="true"><i class="fas fa-times-circle text-white"></i></span>
						</button>
					</div>
					<div class="modal-body curriculum_editing">

					</div>
				</div>
	        </div>
	    </div>
  	</div>
</div>