<?php  
	session_start();

	if(!isset($_SESSION['UserAuth'])){
		?>
		<script type="text/javascript">
			$(document).ready(function(){
				$('.sign_out').click();
			})
		</script>
		<?php
		exit();
	}

    $UserAuth = unserialize($_SESSION['UserAuth']);

    ?>
	<script type="text/javascript">
		if (SignedIn_User_ID != <?php echo $UserAuth->get_UserID(); ?>) {
			location.reload();
		}
	</script>
    <?php
?>