<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
	date_default_timezone_set('Asia/Singapore');
?>

<div class="row">
	<div class="col-lg-8 offset-lg-2 col-xl-6 offset-xl-3">
		<div class="card mt-5">
			<h5 class="card-header bg-dark text-white">Backup Database</h5>
			<div class="card-body">
				<div class="form-group">
					<label>Database</label>
					<input type="text" class="form-control bg-white" disabled value="Electronic Student Program Adviser"></input>
				</div>
				<div class="form-group">
					<label>Date & Time</label>
					<input type="text" class="form-control bg-white" disabled value="<?php echo date('Y-m-d H:i:s') ?>"></input>
				</div>
			</div>
			<div class="card-footer">
				<a href="" class="btn btn-success btn_backup">Backup Database</a>
			</div>
		</div>
	</div>
</div>