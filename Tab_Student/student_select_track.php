<?php  
	include "../database/dbconnection.php";

	if(!isset($_REQUEST['id']) && !isset($_REQUEST['year'])) {
		exit();
    }
    
	$id = $_REQUEST['id'];
    if(empty($id)) {
		$id = "0";
    }
    $year = $_REQUEST['year'];
    if(empty($year)) {
		$year = "0";
    }

	$sql = $mysqli->query("
		SELECT c.Curriculum_ID, c.Track FROM curriculum c  
		WHERE c.Academic_Year LIKE '$year' 
			AND c.Program_ID = '$id'
	");
	if(!$sql)
		exit();

	if($sql->num_rows <= 0){
		?>
		<option value="">None</option>
		<?php  
		exit();
	}
	while ($obj = $sql -> fetch_object()) {
		?>
		<option value="<?php echo htmlspecialchars($obj->Curriculum_ID); ?>" id="<?php echo htmlspecialchars($obj->Curriculum_ID); ?>"><?php echo htmlspecialchars($obj->Track); ?></option>
		<?php
	}
	$sql->free_result();
?>