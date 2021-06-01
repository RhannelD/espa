<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include "../database/dbconnection.php";

	if(empty($_REQUEST['id'])) {
		exit();
    }
	$id = $mysqli->real_escape_string($_REQUEST['id']);

	$sql = $mysqli->query("
		SELECT u.User_ID, u.User_Type, CONCAT(u.Lastname, ', ', u.Firstname) AS Name, u.Gender, u.Email, d.Department_Title
        FROM user u
            INNER JOIN user_department ud ON u.User_ID = ud.User_ID
            INNER JOIN department d ON ud.Department_ID = d.Department_ID
        WHERE u.User_ID LIKE '$id' 
	");

  	if($sql->num_rows <= 0){
  		exit();
  	}

	while ($obj = $sql -> fetch_object()){
  		$Officer = $obj;
  		break;
  	}
?>

<div class="card my-5 my">
	<h4 class="card-header bg-dark text-white">Department Officer Info</h4>
	<div class="card-body">
		<table>
			<tbody>
				<tr>
					<td>User ID:</td>
					<td class="pl-sm-1 pl-md-2 info_officer_user_id"><?php echo htmlspecialchars($Officer->User_ID); ?></td>
				</tr>
				<tr>
					<td>Name:</td>
					<td class="pl-sm-1 pl-md-2 info_officer_name"><?php echo htmlspecialchars($Officer->Name); ?></td>
				</tr>
				<tr>
					<td>Email:</td>
					<td class="pl-sm-1 pl-md-2 info_officer_gender"><?php echo htmlspecialchars($Officer->Email); ?></td>
				</tr>
				<tr>
					<td>Gender:</td>
					<td class="pl-sm-1 pl-md-2 info_officer_gender"><?php echo htmlspecialchars(ucfirst($Officer->Gender)); ?></td>
				</tr>
				<tr>
					<td>Department:</td>
					<td class="pl-sm-1 pl-md-2 info_officer_department"><?php echo htmlspecialchars($Officer->Department_Title); ?></td>
				</tr>
				<tr>
					<td>Position:</td>
					<td class="pl-sm-1 pl-md-2 info_officer_gender">
                        <?php 
                            switch (htmlspecialchars($obj->User_Type)) {
                                case 'CHP':
                                    echo "Department Admin";
                                    break;
                                case 'EVL':
                                    echo "Evaluator";
                                    break;
                            }
                        ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="card-footer">
		<button type="button" class="btn btn-info edit_officer_open_modal mt-1" data-toggle="modal" data-target="#edit_officer">
			<i class="fas fa-edit"></i>
		  	Edit Info
		</button>
		<button type="button" class="btn btn-info position_officer_open_modal mt-1" data-toggle="modal" data-target="#position_officer">
			<i class="fas fa-key"></i>
		  	Change Position
		</button>
		<button type="button" class="btn btn-info password_officer_open_modal mt-1" data-toggle="modal" data-target="#password_officer">
			<i class="fas fa-key"></i>
		  	Change Password
		</button>
		<button class="btn btn-danger officer_delete mt-1">
			<i class="fas fa-trash"></i>
			Delete
		</button>
	</div>
</div>