<?php  
include "../database/UserAuth.php";
include '../database/verify_if_user_has_logout.php';
include "../database/dbconnection.php";
include "../Standard_Functions/user_departments.php";


$UserAuth = unserialize($_SESSION['UserAuth']);
$user_id = $UserAuth->get_UserID();
$user_type = $UserAuth->get_UserType();

$sql_usertype = "";
if ($user_type != 'ADM') {
    $sql_usertype = 'WHERE c.Department_ID = '.getUserDepartments($mysqli, $user_id);
}


$sql = $mysqli->query("
	SELECT c.Department_ID, c.Department_Code
	FROM department c
	$sql_usertype
");



?>

<div class="row mb-1">
	<div class="input-group col-lg-4 mt-2">

		<div class="input-group rounded mt-1">
			<input type="search" class="form-control rounded report_search_input" placeholder="Search Student" aria-label="Search" aria-describedby="report_search_icon"/>
			<span class="input-group-text border-0 report_search_icon">
				<i class="fas fa-search"></i>
			</span>
		</div>

	</div>

	<div class="col-lg-8 mt-2">
		<div class="row">
			<div class="order-md-last col-md-4 mt-1 print_report">
				<button class="btn btn-success btn-block">Print Report</button>
			</div>

			<div class="input-group col-md-3 mt-1">
				<select class="form-control input_order">
				  	<option value="ASC" selected>Ascending</option>
				  	<option value="DESC">Descending</option>
				</select>
			</div>

			<div class="input-group col-md-5 mt-1">
				<div class="input-group-prepend">
					<label class="input-group-text" for="Show">Department</label>
				</div>
				<select class="custom-select input_department" id="Show" <?php echo ($user_type != 'ADM')?"DISABLED":""; ?>>
					<?php  
					if ($user_type == 'ADM') {
						?>
						<option value="all" selected>All</option>
						<?php
					} 
					if ($sql) {
						while ($obj = $sql->fetch_object()) {
							?>
							<option value="<?php echo $obj->Department_ID; ?>">
								<?php echo htmlspecialchars($obj->Department_Code); ?>
							</option>
							<?php
						}
					}
					?>
				</select>
			</div>
		</div>
	</div>
</div>
<div class="row">	
	<div class="input-group col-md-5 col-lg-4 mt-1">
		<div class="input-group-prepend">
			<label class="input-group-text" for="report_type">Report</label>
		</div>
		<select class="custom-select input_report_type" id="report_type">
			<option value="honor" selected>Possible Honorable Students</option>
			<option value="inc">With Inc Record</option>
			<option value="dropped">With Dropped Record</option>
			<option value="failed">With Failed Record</option>
		</select>
	</div>

	<div class="input-group col-md-5 col-lg-3 mt-1">
		<div class="input-group-prepend">
			<label class="input-group-text" for="Row">No. of Rows</label>
		</div>
		<select class="custom-select input_rows" id="Row">
			<option value="15" selected>15</option>
			<option value="30">30</option>
			<option value="50">50</option>
			<option value="80">80</option>
			<option value="100">100</option>
			<option value="150">150</option>
			<option value="200">200</option>
			<option value="300">300</option>
			<option value="400">400</option>
			<option value="500">500</option>
		</select>
	</div>
</div>

<div class="row">

	<div class="contents-container col-12 main-tablebar mb-2 table_report collapse">

	</div>

</div>