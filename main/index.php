<?php 
    include "../database/dbconnection.php";
    include "../database/UserAuth.php";
    include "../database/User_if_signed_in.php";

    include "../Panel_Include/header.php"; 

    $UserAuth = unserialize($_SESSION['UserAuth']);
    $User_ID = $UserAuth->get_UserID();
    $UserType = $UserAuth->get_UserType();

    ?>
    <script type="text/javascript">
        var SignedIn_User_ID = <?php echo $UserAuth->get_UserID(); ?>;
    </script>
    <?php

    $id = $UserAuth->get_UserID();
    $sql = $mysqli->query("
        SELECT 'ADM' AS Logo, '' AS Department_Title
        FROM user u
        WHERE u.User_ID = $id
            AND u.User_Type = 'ADM' 
        UNION ALL 
        SELECT sd.Logo, sd.Department_Title 
        FROM user u
            INNER JOIN user_student us ON u.User_ID = us.User_ID 
            INNER JOIN curriculum c ON us.Curriculum_ID = c.Curriculum_ID 
            INNER JOIN program p ON c.Program_ID = p.Program_ID 
            INNER JOIN department sd ON p.Department_ID = sd.Department_ID
        WHERE u.User_ID = $id
        UNION ALL 
        SELECT sd.Logo, sd.Department_Title 
        FROM user u
            INNER JOIN user_department ud ON u.User_ID = ud.User_ID 
            INNER JOIN department sd ON ud.Department_ID = sd.Department_ID 
        WHERE u.User_ID = $id 
    ");
    if(!$sql) {
        // exit();
    }
    while ($obj = $sql -> fetch_object()) {
        $Department_Title = $obj->Department_Title;
        $logo = $obj->Logo;
    }
?>

    
<div class="d-flex" id="wrapper">

    <!-- Sidebar -->
    <div class="bg-light border-right" id="sidebar-wrapper">
        <div class="sidebar-heading" style="font-size: 18px;">
            <?php  
            switch ($UserType) {
                case 'ADM':
                    ?>
                    <i class="fas fa-user-cog"></i>
                    <?php
                    break;
                case 'STD':
                    ?>
                    <i class="fas fa-user-graduate"></i>
                    <?php
                    break;
                case 'CHP':
                    ?>
                    <i class="fas fa-user-tie"></i>
                    <?php
                    break;
                default:
                    ?>
                    <i class="fas fa-user"></i>
                    <?php
                    break;
            }
            ?>
            <span class="account_info_name">
                <?php  
                echo $UserAuth->get_UserName();
                ?>
            </span>
            <br>
            <button class="btn btn-sm btn-danger sign_out">
              <i class="fas fa-sign-out"></i>
              Signout
            </button>
        </div>

        <div class="list-group list-group-flush">        
            <?php  
            if (in_array($UserType, array('ADM', 'CHP', 'EVL'))) {
                ?>
                <a class="list-group-item list-group-item-action bg-light tabs" id="tab_dashboard">
                    <i class="fas fa-chart-line"></i>
                    Dashboard
                </a> 
                <?php
            }
            if (in_array($UserType, array('ADM', 'CHP', 'EVL'))) {
                ?>
                <a class="list-group-item list-group-item-action bg-light tabs" id="tab_student">
                    <i class="fas fa-user-graduate"></i>
                    Student
                </a>
                <?php
            }
            if (in_array($UserType, array('ADM', 'CHP', 'EVL'))) {
                ?>
                <a class="list-group-item list-group-item-action bg-light tabs" id="tab_report">
                    <i class="fas fa-print"></i>
                    Report
                </a>
                <?php
            }
            if (in_array($UserType, array('ADM', 'CHP', 'EVL', 'STD'))) {
                ?>
                <a class="list-group-item list-group-item-action bg-light tabs" id="tab_request">
                    <i class="fas fa-paper-plane"></i>
                    Request
                </a>
                <?php
            }
            if (in_array($UserType, array('ADM', 'CHP', 'EVL', 'STD'))) {
                ?>
                <a class="list-group-item list-group-item-action bg-light tabs" id="tab_proposal">
                    <i class="fas fa-file-alt"></i>
                    Proposal Slip
                </a>
                <?php
            }
            if (in_array($UserType, array('STD'))) {
                ?>
                <a class="list-group-item list-group-item-action bg-light tabs" id="tab_student_curriculum">
                    <i class="fas fa-file-certificate"></i>
                    Curriculum
                </a>
                <?php
            }
            if (in_array($UserType, array('STD'))) {
                ?>
                <a class="list-group-item list-group-item-action bg-light tabs" id="tab_student_predict">
                    <i class="fas fa-file-invoice"></i>
                    Predict Path
                </a>
                <?php
            }
            if (in_array($UserType, array('ADM'))) {
                ?>
                <a class="list-group-item list-group-item-action bg-light tabs" id="tab_department">
                    <i class="fas fa-building"></i>
                    Department
                </a>
                <?php
            }
            if (in_array($UserType, array('CHP', 'EVL'))) {
                ?>
                <a class="list-group-item list-group-item-action bg-light tabs" id="tab_department_chp_view">
                    <i class="fas fa-building"></i>
                    Department
                </a>
                <?php
            }
            if (in_array($UserType, array('ADM', 'CHP'))) {
                ?>
                <a class="list-group-item list-group-item-action bg-light tabs" id="tab_department_officer">
                    <i class="fas fa-user-tie"></i>
                    Department Officer
                </a>
                <?php
            }
            if (in_array($UserType, array('ADM', 'CHP', 'EVL'))) {
                ?>
                <a class="list-group-item list-group-item-action bg-light tabs" id="tab_curriculum">
                    <i class="fas fa-file-certificate"></i>
                    Curriculum
                </a>
                <?php
            }
            if (in_array($UserType, array('ADM', 'CHP', 'EVL'))) {
                ?>
                <a class="list-group-item list-group-item-action bg-light tabs" id="tab_program">
                    <i class="fas fa-books"></i>
                    Program
                </a>
                <?php
            }
            if (in_array($UserType, array('ADM', 'CHP', 'EVL'))) {
                ?>
                <a class="list-group-item list-group-item-action bg-light tabs" id="tab_course">
                    <i class="fad fa-book-open"></i>
                    Course
                </a>
                <?php
            }
            if (in_array($UserType, array('ADM', 'CHP', 'EVL'))) {
                ?>
                <a class="list-group-item list-group-item-action bg-light tabs" id="tab_history">
                    <i class="fas fa-list"></i>
                    History
                </a>
                <?php
            }
            if (in_array($UserType, array('ADM'))) {
                ?>
                <a class="list-group-item list-group-item-action bg-light tabs" id="tab_backup">
                    <i class="fas fa-database"></i>
                    Backup
                </a>
                <?php
            }
            ?>
            <a class="list-group-item list-group-item-action bg-light tabs" id="tab_account">
                <i class="fad fa-cogs"></i>
                Account
            </a>
        </div>
    </div>
    <!-- /#sidebar-wrapper -->


    <!-- Page Content -->
    <div id="page-content-wrapper">

        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom d-flex align-content-center">
            <div class="float-left">
                <button class="btn" id="menu-toggle">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="offset-md-1 col-md-8 offset-0 col-10 container-fluid">
                <div class="icons ml-auto mr-2">
                    <img class="top-icon top-left-icon" src="../img/icon/BSU_icon.png">
                </div>

                <div>
                    <div class="top-title title-batstateu">BATANGAS STATE UNIVERSITY</div>
                    <div class="top-title title-department"><?php echo htmlspecialchars($Department_Title); ?></div>
                </div>

                <div class="icons ml-2 mr-auto">
                    <?php  
                    if ($Department_Title == '') {
                        ?> 
                        <img class="top-icon top-left-icon" src="../img/icon/Spartan_icon.png">
                        <?php
                    } else {
                        ?> 
                        <img class="top-icon top-right-icon" src="../img/dept_logo/<?php echo $logo; ?>">
                        <?php
                    }
                    ?>
                </div>
            </div>
        </nav>

      <div class="container-fluid" id="mainpanel">

      </div>

    </div>

</div>

<?php 
  include "../Panel_Include/footer.php" 
?>