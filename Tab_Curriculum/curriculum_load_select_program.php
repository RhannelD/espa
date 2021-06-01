<?php  
	include "../database/dbconnection.php";

	if(!isset($_REQUEST['id']) && !isset($department)) {
		exit();
    }
    
    if(isset($_REQUEST['id'])) {
    	$id = $_REQUEST['id'];
        if(empty($id)) {
    		$id = "0";
        }
    }
    if(isset($department)) {
    	$id = $department;
        if(empty($id)) {
    		$id = "0";
        }
    }
    
	$sql = $mysqli->query("
		SELECT Program_ID, Program_Code, Program_Title 
		FROM program 
		WHERE Department_ID = $id
	");
	if(!$sql)
		exit();

	if($sql->num_rows <= 0){
		?>
		<option value="">Select Program</option>
		<?php  
	}
	while ($obj = $sql -> fetch_object()) {
		?>
		<option value="<?php echo $obj->Program_ID; ?>" id="<?php echo htmlspecialchars($obj->Program_Code); ?>"><?php echo htmlspecialchars($obj->Program_Title); ?></option>
		<?php
	}
	$sql->free_result();
?>