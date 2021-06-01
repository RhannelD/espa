<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include "../database/dbconnection.php";
	include "../Validations/request_input_validate.php";
    include "../Standard_Functions/user_departments.php";

	$instance = new RequestValidate();

    $UserAuth = unserialize($_SESSION['UserAuth']);
    $user_type = $UserAuth->get_UserType();


	if(empty($_REQUEST['id'])) {
		exit();
    }
	$Request_ID = $_REQUEST['id'];

	$sql = $mysqli->query("
        SELECT r.Request_ID, r.Message, r.Date, us.SR_Code, CONCAT(u.Firstname, ' ', u.Lastname) AS Name 
        FROM request r 
            INNER JOIN user_student us ON r.Student_ID=us.Student_ID
            INNER JOIN user u ON us.User_ID=u.User_ID
        WHERE r.Request_ID = $Request_ID 
	");

  	if($sql->num_rows <= 0){
  		exit();
  	}

	while ($obj = $sql -> fetch_object()){
  		$Request = $obj;
  		break;
  	}

  	$sql = $mysqli->query("
        SELECT rf.File_ID, rf.Request_ID, rf.Name, rf.Filename
		FROM request_file rf
		WHERE rf.Request_ID = '$Request_ID'
	");
	$Files = $sql;

  	$sql = $mysqli->query("
  		SELECT 'Approved' AS Type, ps.File_Name, ps.Description AS Message
		FROM request_approve ra 
			INNER JOIN proposal_slip ps ON ra.Slip_ID=ps.Slip_ID
		WHERE ra.Request_ID = '$Request_ID'
		UNION ALL
		SELECT 'Denied' AS Type, null AS File_Name, rd.Message
		FROM request_denied rd 
		WHERE rd.Request_ID = '$Request_ID'
		LIMIT 1
	");
	$Responds = $sql;
?>

<div class="row">
	<div class="col-lg-6">
		<div class="card my-2 my">
			<h4 class="card-header bg-dark text-white">Request Info</h4>
			<div class="card-body">
				<table>
					<tbody>
						<tr>
							<td>ID:</td>
							<td class="pl-sm-1 pl-md-2 info_program_id"><?php echo htmlspecialchars($Request->Request_ID); ?></td>
						</tr>
						<?php  
						if ($user_type != 'STD') {
							?>
							<tr>
								<td>SR-Code:</td>
								<td class="pl-sm-1 pl-md-2 info_program_code"><?php echo htmlspecialchars($Request->SR_Code); ?></td>
							</tr>
							<tr>
								<td>Name:</td>
								<td class="pl-sm-1 pl-md-2 info_program_code"><?php echo htmlspecialchars($Request->Name); ?></td>
							</tr>
							<?php
						}
						?>
						<tr>
							<td>Date:</td>
							<td class="pl-sm-1 pl-md-2 info_program_title"><?php echo date('F d, Y',strtotime(htmlspecialchars($obj->Date))); ?></td>
						</tr>
					</tbody>
				</table>
				<?php  
				// Links for the upload request's files
				if ($Files->num_rows > 0 ){
					?> 
					<label class="mb-0">Files Uploaded:</label>
					<div class="card px-1 pt-1">
						<?php
						while ($File = $Files -> fetch_object()){
					  		$fullpath  = dirname(__FILE__, 2)."\\Request_Files\\".$File->Filename;
							$fullpath  = str_replace("\\", "/", $fullpath);
				
							$type	= pathinfo($File->Filename, PATHINFO_EXTENSION);
							$icon	= "fa-exclamation-circle";
							$missing = false;
							switch ($type) {
								case 'pdf':
									$icon = "fa-file-pdf";
									break;
								case 'png':
								case 'gif':
								case 'jpg':
								case 'jpeg':
									$icon = "fa-file-image";
									break;
							}
							if(!file_exists($fullpath)){
								$missing= true;
								$icon	= "fa-exclamation-circle";
							}
						    ?>
							<a <?php echo ($missing)? "": "href='../Request_Files/$File->Filename' target='_blank'"; ?> >
								<div class="input-group mb pb-1 added_file_display filetoopen"> 
									<div class="input-group-prepend filetoopen"> 
										<span class="input-group-text"> 
											<i class="fas <?php echo $icon; ?>"></i> 
										</span> 
									</div> 
									<input type="text" class="form-control bg-white <?php echo ($missing)? "text-danger": ""; ?>" disabled value="<?php echo htmlspecialchars($File->Name); ?>"> 
								</div>
							</a>
						    <?php
					  	}
						?> 
					</div>
					<?php
				}
				?>
				<?php  
		  		if (!empty($Request->Message)) {
		  			?>
			  		<div class="form-group mb-1">
					    <label class="mb-0">Message:</label>
					    <textarea class="form-control bg-white" rows="3" disabled><?php echo htmlspecialchars($Request->Message); ?></textarea>
				    </div>
			  		<?php
		  		}
				?>
			</div>
			<?php  
			// Shows the modal footer if theres any action can be triggered
			if ($Responds->num_rows == 0 || in_array($user_type, array('ADM', 'STD'))) {
				?> 
				<div class="card-footer">	
					<?php 
					if (in_array($user_type, array('ADM', 'STD'))) {
					    ?>
					    <button class="btn btn-danger request_delete mt-1">
							<i class="fas fa-trash"></i>
							Delete
						</button>
					    <?php
					}
					if ($Responds->num_rows == 0 && in_array($user_type, array('ADM', 'CHP', 'EVL'))) {
					    ?>
					    <button class="btn btn-dark btn_request_denying mt-1" id="<?php echo htmlspecialchars($Request->SR_Code); ?>"  type="button" data-toggle="modal" data-target="#deny_request">
							<i class="fas fa-reply"></i>
							Deny Request
						</button>
					    <button class="btn btn-success request_evaluate mt-1" id="<?php echo htmlspecialchars($Request->SR_Code); ?>">
							<i class="fas fa-reply"></i>
							Evaluate
						</button>
					    <?php
					}
					?>
				</div>
				<?php 
			}
			?>
		</div>
	</div>


	<div class="col-lg-6">
		<div class="card my-2 my">
			<h4 class="card-header bg-dark text-white">State</h4>
			<div class="card-body">
				<?php  
				if ($Responds->num_rows == 0) {
					?> 
					<div class="alert alert-info"> Pending... </div>
					<?php
				} 
				while ($Respond = $Responds -> fetch_object()) {
					if ($Respond->Type == 'Denied') {
			  			?> 
			  			<div class="alert alert-danger mb-1">Denied!</div>
			  			<?php
					}
			  		if ($Respond->Type == 'Approved') {
			  			?> 
			  			<div class="alert alert-success mb-1">Evaluated!</div>
			  			<label class="mt-2">Proposal Slip:</label>
			  			<?php 

				  		$fullpath  = dirname(__FILE__, 2)."\\Proposal_Slip\\".$Respond->File_Name;
						$fullpath  = str_replace("\\", "/", $fullpath);
			  			$icon = "fa-file-pdf";
			  			$missing = false;

			  			if(!file_exists($fullpath)){
							$missing= true;
							$icon	= "fa-exclamation-circle";
						}
			  			?> 
			  			<a <?php echo ($missing)? "": "href='../Proposal_Slip/$Respond->File_Name' target='_blank'"; ?> >
							<div class="input-group mb-0 pb-1 added_file_display filetoopen"> 
								<div class="input-group-prepend filetoopen"> 
									<span class="input-group-text"> 
										<i class="fas <?php echo $icon; ?>"></i> 
									</span> 
								</div> 
								<input type="text" class="form-control bg-white <?php echo ($missing)? "text-danger": ""; ?>" disabled value="<?php echo htmlspecialchars($Respond->File_Name); ?>"> 
							</div>
						</a>
			  			<?php
			  		}

			  		if (!empty($Respond->Message)) {
			  			?>
				  		<div class="form-group mb-1">
						    <label class="mx-1 mb-0">Message:</label>
						    <textarea class="form-control bg-white" rows="3" disabled><?php echo htmlspecialchars($Respond->Message); ?></textarea>
					    </div>
				  		<?php
			  		}
				}
				?>
			</div>
		</div>
	</div>
</div>


<div class="denying_request_modal">
	<div class="modal fade" id="deny_request" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	    <div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header bg-dark text-white">
					<h5 class="modal-title" id="exampleModalCenterTitle">Denying Student Reqest</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					    <span aria-hidden="true"><i class="fas fa-times-circle text-white"></i></span>
					</button>
				</div>
				<form id="formDenyRequest">
					<div class="modal-body denying_request">
						<div class="form-group">
						    <label for="message">Send Message</label>
						    <textarea class="form-control c_message" id="message" rows="3" <?php echo $instance->getValidations('message'); ?>></textarea>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-dark">
							<i class="fas fa-reply"></i>
							Deny Request
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
</div>