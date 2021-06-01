
<!-- Editing the Course -->
<div class="modal fade" id="edit_course" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  	<div class="modal-dialog modal-dialog-centered" role="document">
	    <div class="modal-content">
			<div class="modal-header bg-dark text-white">
				<h5 class="modal-title" id="exampleModalCenterTitle">Course Editing</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  	<span aria-hidden="true"><i class="fas fa-times-circle text-white"></i></span>
				</button>
			</div>
			<div class="modal-body" id="course_editing_modal">

			</div>
	    </div>
  	</div>
</div>

<!-- Adding Pre-Requisites -->
<div class="modal fade" id="add_prerequisite" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  	<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
	    <div class="modal-content">
			<div class="modal-header bg-dark text-white">
				<h5 class="modal-title" id="exampleModalCenterTitle">Pre-Requisite Adding</h5>
				<button type="button" class="close close_prereq_modal" data-dismiss="modal" aria-label="Close">
				  	<span aria-hidden="true"><i class="fas fa-times-circle text-white"></i></span>
				</button>
			</div>
			<div class="modal-body" id="modal-table-prereq">

				<div class="row">
				    <div class="input-group rounded col-lg-6">
				      	<input type="search" class="form-control rounded" placeholder="Search Courses" aria-label="Search" aria-describedby="course_search_icon" id="prereq_search_input" />
				      	<span class="input-group-text border-0" id="prereq_search_icon">
				        	<i class="fas fa-search"></i>
				      	</span>
				    </div>
				    <div class="col-lg-6" id="success_alert_add_prereq">
				    </div>
			    </div>

			    <div id="modal_table_prereq">
			    	
			    </div>
			</div>
	    </div>
  	</div>
</div>