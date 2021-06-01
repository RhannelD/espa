<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	date_default_timezone_set('Asia/Singapore');
	$date = date('Y-m-d');
?>

<div class="row mb-1">
	<div class="input-group col-lg-4 mt-2">

		<div class="input-group rounded mt-1">
			<input type="search" class="form-control rounded history_search_input" placeholder="Search History" aria-label="Search" aria-describedby="history_search_icon"/>
			<span class="input-group-text border-0 history_search_icon">
				<i class="fas fa-search"></i>
			</span>
		</div>

	</div>

	<div class="col-lg-8 mt-2">

		<div class="row">
			<div class="input-group col-md-3 mt-1">
				<select class="form-control input_order">
				  	<option value="ASC">Ascending</option>
				  	<option value="DESC" selected>Descending</option>
				</select>
			</div>

			<div class="input-group col-md-4 mt-1">
				<div class="input-group-prepend">
					<label class="input-group-text" for="Show">Show</label>
				</div>
				<select class="custom-select input_show" id="Show">
					<option value="all" selected>All</option>
					<option value="today">Today</option>
					<option value="after">After Date</option>
					<option value="before">Before Date</option>
				</select>
			</div>

			<div class="input-group col-md-5 mt-1">
				<div class="input-group-prepend">
					<span class="input-group-text">Date</span>
				</div>
				<input type="date" class="date form-control input_date" name="date" value="<?php echo $date; ?>" disabled>	
			</div>
		</div>

	</div>
</div>

<div class="row">

	<div class="contents-container col-12 main-tablebar mb-2 table_history collapse">

	</div>

</div>