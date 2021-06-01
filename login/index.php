<?php 
    include "../database/User_if_signed_out.php";
    include "../Validations/login_input_validate.php";

    $instance = new LoginValidate();
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Electronic Student Program Adviser</title>
    <link rel="icon" href="../img/icon/BSU_icon.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="../bootstrap-4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap-4.6.0/fontawesome/css/all.css">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="../css/login.css">

    <script src="../js/sweetalert.min.js"></script>
</head>
 
<body>
    <div class="container-fluid h-100 background">

        <div class="row justify-content-center h-100">
            <div class="col-xl-4 offset-xl-8 col-lg-5 offset-lg-7 col-md-7 offset-md-5 col-sm-8 offset-sm-4 h-75 my-auto px-4 py-4">
                <div class="col-12 shadow-lg bg-white h-100 border-rounded shadow py-5 px-4 form_bg">
                    <form id="login-user">
                        <div>
                            <img 
                                class="rounded mx-auto d-block rounded-circle bsu-logo"
                                src="../img/icon/BSU_icon.png" 
                            >
                        </div>
                        <div class="form-group mt-2">
                            <label for="username">Username</label>
                            <input type="text" name="title" class="form-control c_username" id="username" placeholder="Username" <?php echo $instance->getValidations('username'); ?>>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-group mb-3">
                                <input type="password" name="title" class="form-control c_password" id="password" placeholder="Password" <?php echo $instance->getValidations('password'); ?>>
                                <div class="input-group-append">
                                    <a class="input-group-text show_password" id="basic-addon2">
                                        <i class="fa fa-eye-slash" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info btn-block">
                            Sign-in
                        </button>
                        <hr class=" my-1">
                        <button type="button" class="btn btn-secondary btn-block btn_forgot_password" data-toggle="modal" data-target="#forgot_password">
                           Forgot Password
                        </button>
                        <hr class=" my-1">
                        <a href="../sign_up/"  class="btn btn-success btn-block btn_sign_up" data-toggle="modal" data-target="#sign_up">
                            Sign-up
                        </a>
                        <div class="alert alert-info signin_loading mt-2 collapse">
                            <i class="fad fa-spinner-third fa-spin" aria-hidden="true"></i>
                            Signing-in....
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="forgot_password_modal">
            <div class="modal fade" id="forgot_password" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title" id="exampleModalCenterTitle">Forgot Password</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><i class="fas fa-times-circle text-white"></i></span>
                            </button>
                        </div>
                        <div class="modal-body forgot_password_body">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="sign_up_modal">
            <div class="modal fade" id="sign_up" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title" id="exampleModalCenterTitle">Student Sign-up</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><i class="fas fa-times-circle text-white"></i></span>
                            </button>
                        </div>
                        <div class="modal-body sign_up_body">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/jquery-3.5.1.min.js"></script>
    <script src="../js/jquery.validate.min.js"></script>
    <script src="../bootstrap-4.6.0/js/bootstrap.bundle.min.js"></script>

    <script src="../js/login.js"></script>
    <script src="../js/sign_up.js"></script>
</body>
</html>