<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	include "../database/dbconnection.php";
	include "../Standard_Functions/pagination.php";

	$search = "";
	$per_page = 15;
	$page = 1;
	$id = "0";

	if(!empty($_REQUEST['id'])) {
		$id = $_REQUEST['id']; 
	}

	if(!empty($_REQUEST['page'])) {
		$page = $_REQUEST['page']; 
	}

	if(!empty($_REQUEST['search'])) {
		$search = $_REQUEST['search'];
	}
    $search = $mysqli->real_escape_string(str_replace("\\", "\\\\", $search));

	$sql = $mysqli->query("
		SELECT c.Course_ID
		FROM courses c 
		WHERE c.Course_ID NOT IN (SELECT cc.Course_ID  FROM curriculum_courses cc WHERE cc.Curriculum_ID= $id)
			AND ((c.Course_Code LIKE '%$search%'
		    OR c.Course_ID IN (SELECT c2.Course_id FROM courses c2 WHERE c2.Course_Title LIKE '%$search%')))
	");
	$total_pages = ceil(mysqli_num_rows($sql)/$per_page);

	$Pagination = new Pagination($page,$total_pages);

	$start_page = ($Pagination->getPage() - 1) * $per_page;

	$sql = $mysqli->query("
		SELECT c.Course_ID, c.Course_Code, c.Course_Title, c.Units, c.Lecture, c.Laboratory, c.`Req Standing` AS ReqSta, 
			(SELECT GROUP_CONCAT(c1.Course_Code SEPARATOR ', ') AS 'Pre-requisite/s' 
		    	FROM pre_requisites p INNER JOIN courses c1 ON p.Pre_Requisite =c1.Course_ID
		     	WHERE p.Course_ID=c.Course_ID) as 'Prereq'
		FROM courses c 
		WHERE c.Course_ID NOT IN (SELECT cc.Course_ID  FROM curriculum_courses cc WHERE cc.Curriculum_ID= $id)
			AND ((c.Course_Code LIKE '%$search%'
		    OR c.Course_ID IN (SELECT c2.Course_id FROM courses c2 WHERE c2.Course_Title LIKE '%$search%')))
		LIMIT $start_page, $per_page
	");

?>

<div class="table-responsive">
	<ul class="pagination justify-content-end pagination-md add_course_pagination">

		<li class="page-item <?php echo ($Pagination->hasPrev())? "disabled": ""; ?>">
		  	<a class="page-link" id="<?php echo $Pagination->getPrevPage(); ?>">
		    	<i class="fas fa-caret-square-left"></i>
		  	</a>
		</li>

		<?php  
			for ($page_num = $Pagination->getStartPage(); $page_num <= $Pagination->getEndPage(); $page_num++) { 
				?>
				<li class="page-item <?php echo ($Pagination->getPage() == $page_num)? "active": ""; ?>">
			  		<a class="page-link"  id="<?php echo $page_num; ?>">
			    	<?php echo $page_num; ?>
			  		</a>
				</li>
			<?php
			}  
		?>

		<li class="page-item <?php echo ($Pagination->hasNext())? "disabled": ""; ?>">
		  	<a class="page-link" id="<?php echo $Pagination->getNextPage(); ?>">
		    	<i class="fas fa-caret-square-right"></i>
		 	</a>
		</li>

	</ul>
</div>

<div class="card table-responsive">

  	<table class="table table-sm table-hover card-header" id="course_table">
    
	    <thead class="thead-dark">
			<tr>
				<th>ID</th>
				<th>Code</th>
				<th>Title</th>
				<th>Units</th>
				<th>Lec</th>
				<th>Lab</th>
				<th>Pre-Requisite</th>
				<th>Action</th>
			</tr>
	    </thead>

	    <tbody>
		    <?php 
		        if($sql->num_rows==0){
			        ?>
			        <tr>
			            <td colspan="8" class="table-info">No Results Found!</td>
			        </tr>
			        <?php
		        }

		        while ($obj = $sql -> fetch_object()) {
		         	?>

		          	<tr id="rows">
			            <td class="text-nowrap"><?php echo htmlspecialchars($obj->Course_ID); ?></td>
			            <td class="text-nowrap add_course_code_<?php echo htmlspecialchars($obj->Course_ID); ?>"><?php echo htmlspecialchars($obj->Course_Code); ?></td>
			            <td class="text-nowrap add_course_title_<?php echo htmlspecialchars($obj->Course_ID); ?>"><?php echo htmlspecialchars($obj->Course_Title); ?></td>
			            <td class="text-nowrap"><?php echo htmlspecialchars($obj->Units); ?></td>
			            <td class="text-nowrap"><?php echo htmlspecialchars($obj->Lecture); ?></td>
			            <td class="text-nowrap"><?php echo htmlspecialchars($obj->Laboratory); ?></td>
			            <td class="text-nowrap"><?php echo htmlspecialchars($obj->Prereq); ?></td>
			            <td class="text-nowrap btn btn-info btn-sm data-fixed-columns btn_course_id_add" id="<?php echo htmlspecialchars($obj->Course_ID); ?>">
					  			<i class="fad fa-plus"></i>
					  			Add
			            </td>
		          	</tr>

		          <?php
		        }
		        $sql -> free_result();
		        
		    ?>
	    </tbody>

 	</table>
  
</div>