<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';

    $UserAuth = unserialize($_SESSION['UserAuth']);
    $user_type = $UserAuth->get_UserType();
    
	date_default_timezone_set('Asia/Singapore');
	$date = date('Y-m');
?>



<ul class="nav nav-tabs mt-2" id="myTab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="request-tab" data-toggle="tab" href="#request" role="tab" aria-controls="request" aria-selected="true">
			Search Requests
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link disabled" id="request_info-tab" data-toggle="tab" href="#request_info" role="tab" aria-controls="request_info" aria-selected="false">
			Request Info
		</a>
	</li>
</ul>
<div class="tab-content" id="myTabContent">
	<div class="tab-pane fade show active pt-2" id="request" role="tabpanel" aria-labelledby="request-tab">	
			
		<div class="row mb-1">
			<div class="col-xl-6 mt-2 order-xl-last">
				<div class="row">
					<div class="col-lg-8">
						<div class="row">
							<div class="input-group col-6 mt-1">
								<select class="form-control input_order">
								  	<option value="ASC">Ascending</option>
								  	<option value="DESC" selected>Descending</option>
								</select>
							</div>
							<div class="input-group col-6 mt-1">
								<select class="form-control input_show">
								  	<option value="ALL" selected>Show All</option>
								  	<option value="EVD">Evaluated</option>
								  	<option value="DND">Denied</option>
								  	<option value="PND">Pending</option>
								</select>
							</div>
						</div>
					</div>

					<div class="col-lg-4 mt-1 order-first order-lg-last">
						<?php  
						if (in_array($user_type, array('STD'))){
							?> 
							<div class="input-group rounded float-right">
								<button class="btn btn-info ml-auto mr-0 create_request_open_modal" type="button" data-toggle="modal" data-target="#create_request">
									<i class="fas fa-plus"></i>
									Request
								</button>
							</div>
							<?php
						}
						?>
					</div>
				</div>
			</div>

			<div class="input-group col-xl-6 mt-2">

				<div class="input-group rounded mt-1">
					<input type="search" class="form-control rounded request_search_input" placeholder="Search Request" aria-label="Search" aria-describedby="request_search_icon"/>
					<span class="input-group-text border-0 request_search_icon">
						<i class="fas fa-search"></i>
					</span>
				</div>

			</div>
		</div>

		<div class="contents-container col-12 main-tablebar mb-2 table_request collapse">

		</div>

		<div class="creating_request_modal">
			<div class="modal fade" id="create_request" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			    <div class="modal-dialog modal-dialog-centered" role="document">
			      	<div class="modal-content">
				        <div class="modal-header bg-dark text-white">
				          <h5 class="modal-title" id="exampleModalCenterTitle">Request Evaluation</h5>
				          	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				              	<span aria-hidden="true"><i class="fas fa-times-circle text-white"></i></span>
				          	</button>
				        </div>
				        <div class="request_creating">
				          	
				        </div>
			      	</div>
			    </div>
			</div>
		</div>

	</div>

	<div class="tab-pane fade" id="request_info" role="tabpanel" aria-labelledby="request_info-tab">
		
		<div class="row">
			<div class="contents-container col-12 info_request collapse">

			</div>
		</div>

	</div>
</div>
