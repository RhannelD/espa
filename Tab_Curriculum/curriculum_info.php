<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include "../database/dbconnection.php";

	if(empty($_REQUEST['id'])) {
		exit();
    }
	$id = $_REQUEST['id'];

	$sql = $mysqli->query("
		SELECT c.Curriculum_ID, sd.Department_Title, p.Program_Code, p.Program_Title, c.Track, YEAR(Academic_Year) AS 'AcademicYear', 
            GROUP_CONCAT(cr.Reference SEPARATOR ', ') AS 'References' 
        FROM `curriculum` c
            INNER JOIN program p ON c.Program_ID=p.Program_ID
        	INNER JOIN department sd ON p.Department_ID=sd.Department_ID
            INNER JOIN curriculum_references cr ON c.Curriculum_ID=cr.Curriculum_ID 
        WHERE c.Curriculum_ID = $id
	");
	if(!$sql)
		exit();
  	if($sql->num_rows <= 0)
  		exit();
	while ($obj = $sql -> fetch_object()){
  		$Curriculum = $obj;
  		break;
  	}
?>

<div class="card my-5 my">
	<h4 class="card-header bg-dark text-white">Curriculum Info</h4>
	<div class="card-body">
		<table>
			<tbody>
				<tr>
					<td>ID:</td>
					<td class="pl-sm-1 pl-md-2 info_curriculum_id"><?php echo htmlspecialchars($Curriculum->Curriculum_ID); ?></td>
				</tr>
				<tr>
					<td>Department:</td>
					<td class="pl-sm-1 pl-md-2 info_curriculum_department"><?php echo htmlspecialchars($Curriculum->Department_Title); ?></td>
				</tr>
				<tr>
					<td>Program:</td>
					<td class="pl-sm-1 pl-md-2 info_curriculum_program" id="<?php echo htmlspecialchars($Curriculum->Program_Code); ?>"><?php echo htmlspecialchars($Curriculum->Program_Title); ?></td>
				</tr>
				<tr>
					<td>Track:</td>
					<td class="pl-sm-1 pl-md-2 info_curriculum_track"><?php echo (empty($Curriculum->Track))? "None": htmlspecialchars($Curriculum->Track); ?></td>
				</tr>
				<tr>
					<td>Academic Year:</td>
					<td class="pl-sm-1 pl-md-2 info_curriculum_academic_year"><?php echo htmlspecialchars($Curriculum->AcademicYear."-".(intval($Curriculum->AcademicYear)+1)); ?></td>
				</tr>
				<tr>
					<td>References:</td>
					<td class="pl-sm-1 pl-md-2 info_curriculum_references"><?php echo htmlspecialchars($Curriculum->References); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="card-footer">
		<?php  
		if (!isset($_REQUEST['duplicate'])) {		// Default Curriculum Info
			?>
			<button class="btn btn-info curriculum_open mt-1" id="<?php echo htmlspecialchars($Curriculum->Curriculum_ID); ?>">
				<i class="far fa-file-alt"></i>
			  	Open Curriculum
			</button>
			<button class="btn btn-info curriculum_edit_courses mt-1" id="<?php echo htmlspecialchars($Curriculum->Curriculum_ID); ?>">
				<i class="fas fa-edit"></i>
			  	Edit Courses
			</button>
			<button type="button" class="btn btn-info edit_curriculum_open_modal mt-1" data-toggle="modal" data-target="#edit_curriculum">
				<i class="fas fa-edit"></i>
			  	Edit
			</button>
			<button class="btn btn-danger curriculum_delete mt-1" id="<?php echo htmlspecialchars($Curriculum->Curriculum_ID); ?>">
				<i class="fas fa-trash"></i>
				Delete
			</button>
			<?php
		} else { 							// For Curriculum Duplication
			?>
			<button class="btn btn-info confirm_curriculum_ducplicate mt-1" id="<?php echo htmlspecialchars($Curriculum->Curriculum_ID); ?>">
				<i class="fas fa-copy"></i>
			  	Duplicate Curriculum Courses
			</button>
			<?php
		}
		?>
	</div>
</div>