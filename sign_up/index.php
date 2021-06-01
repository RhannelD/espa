<?php  
	include '../database/dbconnection.php';

	if(!isset($_GET['signupcode'])){
		header("Location: login/");
		exit();
	}
	$signupcode = $_GET['signupcode'];

	// Validate if Sign up exist
	$sql = $mysqli->query("
		SELECT up.Signup_ID, CONCAT(up.Firstname, up.Lastname) AS Name, up.Gender, up.Email, up.SR_Code, (
			SELECT True 
		    FROM user u 
		    WHERE u.Email = up.Email
		    LIMIT 1
			) AS Email_Check , (
		    SELECT True
		    FROM user_student us 
		    WHERE us.SR_Code = up.SR_Code
		    LIMIT 1
		    ) AS SR_Code_Check 
		FROM user_signup up 
		WHERE up.Signup_ID = '$signupcode'
	");

	$num_rows = 0;
	if ($sql) {
		$num_rows = $sql->num_rows;
		while ($obj = $sql -> fetch_object()) {
			$result = $obj;

			if (!(isset($obj->Email_Check) || isset($obj->SR_Code_Check))) {
				$sql1 = $mysqli->query("
					INSERT INTO `user` (`User_ID`, `User_Type`, `Firstname`, `Lastname`, `Gender`, `Email`, `Password`) 
					SELECT null, 'STD', up.Firstname, up.Lastname, up.Gender, up.Email, up.Password FROM user_signup up WHERE up.Signup_ID = '$signupcode'
				");
				$id = $mysqli->insert_id;
				$sql1 = $mysqli->query("
					INSERT INTO `user_student` (`Student_ID`, `User_ID`, `Curriculum_ID`, `SR_Code`) 
					SELECT null, '$id', up.Curriculum_ID, up.SR_Code FROM user_signup up WHERE up.Signup_ID = '$signupcode'
				");
			}
			break;
		}
	}
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Electronic Student Program Adviser</title>
    <link rel="icon" href="../img/icon/BSU_icon.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="../bootstrap-4.6.0/css/bootstrap.min.css">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="../bootstrap-4.6.0/fontawesome/css/all.css">
    <link rel="stylesheet" href="../css/style.css">

    <!-- JavaScript -->
    <!-- <script src="../js/fontawesome.js"></script> -->
    <script src="../js/sweetalert.min.js"></script>

    <title>ESPA</title>
</head>

<body>

	<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom d-flex align-content-center">
        <div class="col-12 container-fluid">
            <div class="icons ml-auto mr-2">
                <img class="top-icon top-left-icon" src="../img/icon/BSU_icon.png">
            </div>

            <div>
                <div class="top-title title-batstateu">BATANGAS STATE UNIVERSITY</div>
            </div>

            <div class="icons ml-2 mr-auto">
                <img class="top-icon top-left-icon" src="../img/icon/Spartan_icon.png">
            </div>
        </div>
    </nav>

	<div class="row">
    	<div class="offset-md-3 col-md-6 my-5">
    		<div class='card'>
    			<h3 class="card-header bg-dark text-white">Email Confirmation</h3>
    			<div class="card-body">
				    <?php  
				    if ($num_rows == 0) {
						?>
			    		<div class="alert alert-danger my-1">
							Sign-up registration record not found
						</div>
						<?php
					} else {
						if (isset($result->Email_Check) || isset($result->SR_Code_Check)) {
							?>
						    <div class="alert alert-info my-1">Already Registered</div>
							<?php
						} else {
							?>
						    <div class="alert alert-success mt-1 mb-2">Account Successfully Registered</div>
						    <div class="row">
							    <div class="form-group col-md-6">
								  	<label for="name">Name</label>
								  	<input type="text" name="name" class="form-control bg-white" id="name" value="<?php echo htmlspecialchars($result->Name) ?>" disabled>
								</div>
								<div class="form-group col-md-6">
								  	<label for="sr_code">SR-Code</label>
								  	<input type="text" name="sr_code" class="form-control bg-white " id="sr_code" value="<?php echo htmlspecialchars($result->SR_Code) ?>" disabled>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-12">
								  	<label for="email">SR-Code</label>
								  	<input type="email" name="email" class="form-control bg-white " id="email" value="<?php echo htmlspecialchars($result->Email) ?>" disabled>
								</div>
							</div>
							<?php
						}
					}
				    ?>
    			</div>
    			<div class="card-footer">
    				<a class="btn btn-success float-right" href="../login/">
    					Continue on Login
    					<i class="fas fa-arrow-circle-right ml-1"></i>
    				</a>
    			</div>
    		</div>
    	</div>
    </div>

	    
    
    <script src="../js/jquery-3.5.1.min.js"></script>
    <script src="../js/jquery.validate.min.js"></script>
    <script src="../bootstrap-4.6.0/js/bootstrap.bundle.min.js"></script>
    <script src="../library-chartjs/Chart.min.js"></script>
</body>

</html>