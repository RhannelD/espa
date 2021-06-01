<?php 

	session_start();

	session_destroy();

	// Going to Main
	echo json_encode(array(
		"alert" => "success",
		"panel" => "../login/"
	));
	exit();

?>