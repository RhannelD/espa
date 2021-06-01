<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include "../database/dbconnection.php";
    include "../Standard_Functions/user_departments.php";

    $UserAuth = unserialize($_SESSION['UserAuth']);
    $UserType = $UserAuth->get_UserType();
    $User_ID = $UserAuth->get_UserID();

    if (!in_array($UserType, array('CHP', 'EVL')))
    	exit();

    $Department_ID = getUserDepartments($mysqli, $User_ID);


	$sql = $mysqli->query("
		SELECT d.Department_ID, d.Department_Code, d.Department_Title, d.Logo, dd.Name AS 'Dean', dh.Name AS 'Head'
			FROM department d 
			INNER JOIN department_dean dd ON d.Dean_ID=dd.Dean_ID
			INNER JOIN department_head dh ON d.DeptHead_ID=dh.Head_ID
				WHERE Department_ID = $Department_ID
	");

  	if($sql->num_rows <= 0){
  		exit();
  	}

	while ($obj = $sql -> fetch_object()){
  		$Department = $obj;
  		break;
  	}
  	$sql->free_result();
?>
<div class="row">
	<div class=" col-lg-8 offset-lg-2 col-xl-6 offset-xl-3">
		<div class="card my-5 my">
			<h4 class="card-header bg-dark text-white">Department Info</h4>
			<div class="card-body">
				<table class="col-12">
					<tbody>
						<tr>
							<td colspan="2">
								<?php  
								if ($UserType == 'CHP') {
									?>
									<img src="../img/dept_logo/<?php echo $Department->Logo; ?>" alt="Logo" class="rounded mx-auto d-block logo info_department_logo rounded-circle" id="<?php echo $Department->Logo; ?>" data-toggle="modal" data-target="#edit_department_logo">
									<?php
								} else {
									?>
									<img src="../img/dept_logo/<?php echo $Department->Logo; ?>" alt="Logo" class="rounded mx-auto d-block logo rounded-circle">
									<?php
								}
								?>
							</td>
						</tr>
						<tr>
							<td style="width: 30px;">ID:</td>
							<td class="pl-sm-1 pl-md-2 info_department_id"><?php echo htmlspecialchars($Department->Department_ID); ?></td>
						</tr>
						<tr>
							<td>Code:</td>
							<td class="pl-sm-1 pl-md-2 info_department_code"><?php echo htmlspecialchars($Department->Department_Code); ?></td>
						</tr>
						<tr>
							<td>Department:</td>
							<td class="pl-sm-1 pl-md-2 info_department_title"><?php echo htmlspecialchars($Department->Department_Title); ?></td>
						</tr>
						<tr>
							<td>Dept. Dean:</td>
							<td class="pl-sm-1 pl-md-2 info_department_dean"><?php echo htmlspecialchars($Department->Dean); ?></td>
						</tr>
						<tr>
							<td>Dept. Head:</td>
							<td class="pl-sm-1 pl-md-2 info_department_head"><?php echo htmlspecialchars($Department->Head); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php  
			if ($UserType == 'CHP') {
				?>
				<div class="card-footer">
					<button type="button" class="btn btn-info edit_department_open_modal mt-1" data-toggle="modal" data-target="#edit_department">
						<i class="fas fa-edit"></i>
					  	Edit
					</button>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>

<?php  
if ($UserType == 'EVL') {
	exit();
}
?>
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