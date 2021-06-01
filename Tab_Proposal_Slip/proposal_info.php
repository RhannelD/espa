<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include "../database/dbconnection.php";

    $UserAuth = unserialize($_SESSION['UserAuth']);
    $UserType = $UserAuth->get_UserType();

	if(empty($_REQUEST['id'])) {
		exit();
    }
	$Slip_ID = $_REQUEST['id'];

	$sql = $mysqli->query("
        SELECT ps.Slip_ID, us.SR_Code, CONCAT(u.Firstname, ' ', u.Lastname) AS Name, ps.Description, ps.Date, ps.File_Name
        FROM proposal_slip ps 
            INNER JOIN user_student us ON ps.Student_ID = us.Student_ID 
            INNER JOIN user u ON us.User_ID = u.User_ID 
            INNER JOIN curriculum c ON us.Curriculum_ID = c.Curriculum_ID 
            INNER JOIN program p ON c.Program_ID = p.Program_ID
        WHERE ps.Slip_ID = $Slip_ID 
	");

  	if($sql->num_rows <= 0){
  		exit();
  	}

	while ($obj = $sql -> fetch_object()){
  		$Proposal = $obj;
  		break;
  	}
?>

<div class="card my-5 my">
	<h4 class="card-header bg-dark text-white">Proposal Info</h4>
	<div class="card-body">
		<table>
			<tbody>
				<tr>
					<td>ID:</td>
					<td class="pl-sm-1 pl-md-2 info_program_id"><?php echo htmlspecialchars($Proposal->Slip_ID); ?></td>
				</tr>
				<?php  
				if (in_array($UserType, array('ADM', 'CHP', 'EVL'))) {
					?>
					<tr>
						<td>SR-Code:</td>
						<td class="pl-sm-1 pl-md-2 info_program_code"><?php echo htmlspecialchars($Proposal->SR_Code); ?></td>
					</tr>
					<tr>
						<td>Name:</td>
						<td class="pl-sm-1 pl-md-2 info_program_code"><?php echo htmlspecialchars($Proposal->Name); ?></td>
					</tr>
					<?php
				}
				?>
				<tr>
					<td>Date:</td>
					<td class="pl-sm-1 pl-md-2 info_program_title"><?php echo date('F d, Y',strtotime(htmlspecialchars($obj->Date))); ?></td>
				</tr>
				<?php  
				$fullpath  = dirname(__FILE__, 2)."\\Proposal_Slip\\".$Proposal->File_Name;
				$fullpath  = str_replace("\\", "/", $fullpath);
		
				if(!file_exists($fullpath)){
				    ?>
					<tr>
						<td>File:</td>
						<td class="pl-sm-1 pl-md-2 text-danger">File does not exist</td>
					</tr>
				    <?php
				}
				?>
			</tbody>
		</table>
		<td colspan="2">
			<div class="form-group">
			    <label>Message:</label>
			    <textarea class="form-control bg-white" rows="3" disabled><?php echo htmlspecialchars($Proposal->Description); ?></textarea>
		    </div>
		</td>
		<?php  
		if(file_exists($fullpath)){
		    ?>
			<a href="../Proposal_Slip/<?php echo htmlspecialchars($Proposal->File_Name); ?>" target="_blank">
				<div class="input-group mb pb-1 added_file_display filetoopen"> 
					<div class="input-group-prepend filetoopen"> 
						<span class="input-group-text"> 
							<i class="fas fa-file-pdf"></i> 
						</span> 
					</div> 
					<input type="text" class="form-control bg-white" disabled value="<?php echo htmlspecialchars($Proposal->File_Name); ?>"> 
				</div>
			</a>
		    <?php
		}
		?>
	</div>

	<div class="card-footer">
		<button class="btn btn-danger proposal_delete mt-1" id="<?php echo htmlspecialchars($Proposal->Slip_ID); ?>">
			<i class="fas fa-trash"></i>
			Delete
		</button>
	</div>
</div>