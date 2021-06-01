<?php  
	session_start();

	if(!isset($_SESSION['UserAuth'])){
		header("Location: ../login/");
	}
?>