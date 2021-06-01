<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include "../database/dbconnection.php";

	$CourseID = $_REQUEST['ID'];

	$sql = $mysqli->query("
		SELECT c.Course_ID, c.Course_Code, c.Course_Title, c.Units, c.Lecture, c.Laboratory, c.`Req Standing` AS 'Req', 
			(SELECT GROUP_CONCAT(c1.Course_Code SEPARATOR ', ') AS 'Pre-requisite/s' 
			FROM pre_requisites p 
			INNER JOIN courses c1 ON p.Pre_Requisite =c1.Course_ID WHERE p.Course_ID=c.Course_ID) as 'PreReq' 
		FROM courses c WHERE c.Course_ID Like '$CourseID'
	");

  	if($sql->num_rows <= 0){
  		exit();
  	}

	while ($obj = $sql -> fetch_object()){
  		$Course = $obj;
  		break;
  	}

?>

<div class="card my-5 my">
	<h4 class="card-header bg-dark text-white">Course Info</h4>
	<div class="card-body">
		<table>
			<tbody>
				<tr>
					<td>ID:</td>
					<td class="pl-sm-1 pl-md-2" id="info_course_id"><?php echo htmlspecialchars($Course->Course_ID); ?></td>
				</tr>
				<tr>
					<td>Code:</td>
					<td class="pl-sm-1 pl-md-2 info_course_code"><?php echo htmlspecialchars($Course->Course_Code); ?></td>
				</tr>
				<tr>
					<td>Title:</td>
					<td class="pl-sm-1 pl-md-2 info_course_title"><?php echo htmlspecialchars($Course->Course_Title); ?></td>
				</tr>
				<tr>
					<td>Unit:</td>
					<td class="pl-sm-1 pl-md-2 info_course_units"><?php echo htmlspecialchars($Course->Units); ?></td>
				</tr>
				<tr>
					<td>Lecture:</td>
					<td class="pl-sm-1 pl-md-2 info_course_lecture"><?php echo htmlspecialchars($Course->Lecture); ?></td>
				</tr>
				<tr>
					<td>Laboratory:</td>
					<td class="pl-sm-1 pl-md-2 info_course_laboratory"><?php echo htmlspecialchars($Course->Laboratory); ?></td>
				</tr>
				<tr>
					<td>Req Standing:</td>
					<td class="pl-sm-1 pl-md-2 info_course_reqstanding">
						<?php 
						if(empty($obj->Req)){
							echo "None"; 
						}
						echo $obj->Req; 
						?>
					</td>
				</tr>
				<tr>
					<td>Pre-Requisite/s:</td>
					<td class="pl-sm-1 pl-md-2 info_course_prereq">
						<?php
						if(empty($obj->PreReq)){
							echo "None"; 
						}
						echo $obj->PreReq; 
						?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="card-footer">
		<button type="button" class="btn btn-info edit_course_open_modal mt-1" data-toggle="modal" data-target="#edit_course">
			<i class="fas fa-edit"></i>
		  	Edit
		</button>
		<button class="btn btn-danger course_delete mt-1" id="<?php echo htmlspecialchars($Course->Course_ID); ?>">
			<i class="fas fa-trash"></i>
			Delete
		</button>
	</div>
</div>