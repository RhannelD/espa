<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include "../database/dbconnection.php";

	if(empty($_REQUEST['id'])) {
		exit();
    }
	$Program_ID = $_REQUEST['id'];

	$sql = $mysqli->query("
        SELECT p.Program_ID, p.Program_Code, p.Program_Title, d.Department_Title
        FROM program p 
        	INNER JOIN department d ON p.Department_ID=d.Department_ID 
        WHERE p.Program_ID = $Program_ID 
	");

  	if($sql->num_rows <= 0){
  		exit();
  	}

	while ($obj = $sql -> fetch_object()){
  		$Program = $obj;
  		break;
  	}
?>

<div class="card my-5 my">
	<h4 class="card-header bg-dark text-white">Program Info</h4>
	<div class="card-body">
		<table>
			<tbody>
				<tr>
					<td>ID:</td>
					<td class="pl-sm-1 pl-md-2 info_program_id"><?php echo htmlspecialchars($Program->Program_ID); ?></td>
				</tr>
				<tr>
					<td>Code:</td>
					<td class="pl-sm-1 pl-md-2 info_program_code"><?php echo htmlspecialchars($Program->Program_Code); ?></td>
				</tr>
				<tr>
					<td>Program:</td>
					<td class="pl-sm-1 pl-md-2 info_program_title"><?php echo htmlspecialchars($Program->Program_Title); ?></td>
				</tr>
				<tr>
					<td>Department:</td>
					<td class="pl-sm-1 pl-md-2 info_program_units"><?php echo htmlspecialchars(ucwords(strtolower($Program->Department_Title))); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="card-footer">
		<button type="button" class="btn btn-info edit_program_open_modal mt-1" data-toggle="modal" data-target="#edit_program">
			<i class="fas fa-edit"></i>
		  	Edit
		</button>
		<button class="btn btn-danger program_delete mt-1" id="<?php echo htmlspecialchars($Program->Program_ID); ?>">
			<i class="fas fa-trash"></i>
			Delete
		</button>
	</div>
</div>