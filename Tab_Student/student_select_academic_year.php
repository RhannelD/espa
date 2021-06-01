<?php  
	include "../database/dbconnection.php";

	if(!isset($_REQUEST['id'])) {
		exit();
    }
    
	$id = $_REQUEST['id'];
    if(empty($id)) {
		$id = "0";
    }

	?>
	<option value="">Select Academic Year</option>
	<?php 

	$sql = $mysqli->query("
		SELECT DISTINCT(Academic_Year) AS 'AcademicYear'
        FROM curriculum WHERE Program_ID = $id
        ORDER BY Academic_Year ASC
	");
	if(!$sql)
		exit();

	if($sql->num_rows <= 0){
		exit();
	}
	 
	while ($obj = $sql -> fetch_object()) {
		?>
		<option value="<?php echo htmlspecialchars($obj->AcademicYear); ?>" id="<?php echo htmlspecialchars($obj->AcademicYear); ?>"><?php echo htmlspecialchars($obj->AcademicYear."-".(intval($obj->AcademicYear)+1)); ?></option>
		<?php
	}
	$sql->free_result();
?>