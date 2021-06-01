<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include "../database/dbconnection.php";

	if(empty($_REQUEST['id'])) {
		exit();
    }
	$SR_Code = $mysqli->real_escape_string($_REQUEST['id']);

	$sql = $mysqli->query("
		SELECT us.SR_Code, CONCAT(u.Lastname, ' ', u.Firstname) AS Name, u.Gender, u.Email, p.Program_Title, YEAR(c.Academic_Year) AS Year, c.Track
        FROM user u
            INNER JOIN user_student us ON u.User_ID = us.User_ID 
            INNER JOIN curriculum c ON us.Curriculum_ID = c.Curriculum_ID 
            INNER JOIN program p ON c.Program_ID = p.Program_ID 
            INNER JOIN department sd ON p.Department_ID = sd.Department_ID
        WHERE u.User_Type = 'STD'
            AND us.SR_Code = '$SR_Code'
	");

  	if($sql->num_rows <= 0){
  		exit();
  	}

	while ($obj = $sql -> fetch_object()){
  		$Student = $obj;
  		break;
  	}
?>

<div class="card my-5 my">
	<h4 class="card-header bg-dark text-white">Student Info</h4>
	<div class="card-body">
		<table>
			<tbody>
				<tr>
					<td>SR Code:</td>
					<td class="pl-sm-1 pl-md-2 info_student_sr_code"><?php echo htmlspecialchars($Student->SR_Code); ?></td>
				</tr>
				<tr>
					<td>Name:</td>
					<td class="pl-sm-1 pl-md-2 info_student_name"><?php echo htmlspecialchars($Student->Name); ?></td>
				</tr>
				<tr>
					<td>Email:</td>
					<td class="pl-sm-1 pl-md-2 info_student_gender"><?php echo htmlspecialchars(strtolower($Student->Email)); ?></td>
				</tr>
				<tr>
					<td>Gender:</td>
					<td class="pl-sm-1 pl-md-2 info_student_gender"><?php echo htmlspecialchars(ucfirst($Student->Gender)); ?></td>
				</tr>
				<tr>
					<td>Program:</td>
					<td class="pl-sm-1 pl-md-2 info_student_program"><?php echo htmlspecialchars($Student->Program_Title); ?></td>
				</tr>
				<tr>
					<td>Year:</td>
					<td class="pl-sm-1 pl-md-2 info_student_year"><?php echo htmlspecialchars($Student->Year."-".(intval($Student->Year)+1)); ?></td>
				</tr>
				<?php  
				if($Student->Track != ""){
					?>
					<tr>
						<td>Track:</td>
						<td class="pl-sm-1 pl-md-2 info_student_year"><?php echo htmlspecialchars($Student->Track); ?></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</div>
	<div class="card-footer">
		<button class="btn btn-info student_evaluate_open mt-1" id="<?php echo htmlspecialchars($Student->SR_Code); ?>">
			<i class="fas fa-address-card"></i>
		  	Evaluate
		</button>
		<button class="btn btn-info student_curriculum_open mt-1" id="<?php echo htmlspecialchars($Student->SR_Code); ?>">
			<i class="far fa-file-alt"></i>
		  	Open Curriculum
		</button>
		<button type="button" class="btn btn-info student_predict_open mt-1" id="<?php echo htmlspecialchars($Student->SR_Code); ?>">
			<i class="fas fa-file-invoice"></i>
		  	Predict Path
		</button>
		<button type="button" class="btn btn-info shift_student_open_modal mt-1" data-toggle="modal" data-target="#shift_student">
			<i class="fas fa-exchange-alt"></i>
		  	Shift Program
		</button>
		<button type="button" class="btn btn-info edit_student_open_modal mt-1" data-toggle="modal" data-target="#edit_student">
			<i class="fas fa-edit"></i>
		  	Edit Info
		</button>
		<button type="button" class="btn btn-info password_student_open_modal mt-1" data-toggle="modal" data-target="#password_student">
			<i class="fas fa-key"></i>
		  	Change Password
		</button>
		<button class="btn btn-danger student_delete mt-1" id="<?php echo htmlspecialchars($Student->SR_Code); ?>">
			<i class="fas fa-trash"></i>
			Delete
		</button>
	</div>
</div>