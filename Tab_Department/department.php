<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
?>
<div class="row mb-1">
	<div class="input-group col-md-6 mt-2">

		<div class="input-group rounded">
			<input type="search" class="form-control rounded department_search_input" placeholder="Search Departments" aria-label="Search" aria-describedby="department_search_icon"/>
			<span class="input-group-text border-0 department_search_icon">
				<i class="fas fa-search"></i>
			</span>
		</div>

	</div>

	<div class="col-md-6 mt-2">

		<div class="input-group rounded">
			<button class="btn btn-info ml-auto mr-0 create_department_open_modal" type="button" data-toggle="modal" data-target="#create_department">
				<i class="fas fa-plus"></i>
				Create Department
			</button>
		</div>

	</div>
</div>

<div class="row">

	<div class="contents-container col-md-6 main-tablebar mb-2 table_department collapse">

	</div>

	<div class="contents-container col-md-6 info_department collapse">

	</div>

	<div class="creating_department_modal">

		<div class="modal fade" id="create_department" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		      	<div class="modal-content">
			        <div class="modal-header bg-dark text-white">
			          <h5 class="modal-title" id="exampleModalCenterTitle">Department Creation</h5>
			          	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			              	<span aria-hidden="true"><i class="fas fa-times-circle text-white"></i></span>
			          	</button>
			        </div>
			        <div class="modal-body department_creating">
			          	
			        </div>
		      	</div>
		    </div>
		</div>

	</div>

  	<div class="editing_department_modal">
	    <div class="modal fade" id="edit_department" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
				<div class="modal-content">
					<div class="modal-header bg-dark text-white">
						<h5 class="modal-title" id="exampleModalCenterTitle">Department Editing</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    <span aria-hidden="true"><i class="fas fa-times-circle text-white"></i></span>
						</button>
					</div>
					<div class="modal-body department_editing">

					</div>
				</div>
	        </div>
	    </div>
  	</div>

  	<div class="editing_department_logo_modal">
	    <div class="modal fade" id="edit_department_logo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	        <div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header bg-dark text-white">
						<h5 class="modal-title" id="exampleModalCenterTitle">Department Logo Editing</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    <span aria-hidden="true"><i class="fas fa-times-circle text-white"></i></span>
						</button>
					</div>
					<div class="modal-body department_logo_editing">

					</div>
				</div>
	        </div>
	    </div>
  	</div>
</div>