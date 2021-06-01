<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
    include "../database/dbconnection.php";
    include "../Standard_Functions/pagination.php";
    include "../Standard_Functions/user_departments.php";

    $UserAuth = unserialize($_SESSION['UserAuth']);
    $user_type = $UserAuth->get_UserType();
    $user_id = $UserAuth->get_UserID();
    $department = getUserDepartments($mysqli, $user_id);

    $query_dept = "";
    if ($user_type == 'CHP') {
        $query_dept = " 
            AND d.Department_ID = '$department'
            AND u.User_Type IN ('EVL', 'NAN')
        ";
    }


    $search = "";
    $per_page = 15;
    $page = 1;

    if(!empty($_REQUEST['page'])) {
        $page = $_REQUEST['page']; 
    }

    if(!empty($_REQUEST['search'])) {
        $search = $_REQUEST['search'];
    }
    $search = $mysqli->real_escape_string(str_replace("\\", "\\\\", $search));

    $sql = $mysqli->query("
        SELECT u.User_ID
        FROM user u
            INNER JOIN user_department ud ON u.User_ID = ud.User_ID
            INNER JOIN department d ON ud.Department_ID = d.Department_ID
        WHERE (u.User_ID LIKE '$search%' 
            OR CONCAT(u.Lastname, ' ', u.Firstname) LIKE '%$search%'
            OR d.Department_Code LIKE '%$search%')
            $query_dept
        ORDER BY u.User_ID
    ");
    $total_pages = ceil(mysqli_num_rows($sql)/$per_page);

    $Pagination = new Pagination($page,$total_pages);

    $start_page = ($Pagination->getPage() - 1) * $per_page;

    $sql = $mysqli->query("
        SELECT u.User_ID, u.User_Type, CONCAT(u.Lastname, ', ', u.Firstname) AS Name, u.Gender, d.Department_Code
        FROM user u
            INNER JOIN user_department ud ON u.User_ID = ud.User_ID
            INNER JOIN department d ON ud.Department_ID = d.Department_ID
        WHERE (u.User_ID LIKE '$search%' 
            OR CONCAT(u.Lastname, ', ', u.Firstname) LIKE '%$search%'
            OR d.Department_Code LIKE '%$search%')
            $query_dept
        ORDER BY u.User_ID
        LIMIT $start_page, $per_page
    ");
?>

<div class="table-responsive">
    <ul class="pagination justify-content-end pagination-md officer_pagination">


        <li class="page-item <?php echo ($Pagination->hasPrev())? "disabled": ""; ?>">
            <a class="page-link" id="<?php echo $Pagination->getPrevPage(); ?>">
                <i class="fas fa-caret-square-left"></i>
            </a>
        </li>

        <?php  
            if (!$Pagination->noPages()){
                for ($page_num = $Pagination->getStartPage(); $page_num <= $Pagination->getEndPage(); $page_num++) { 
                ?>
                    <li class="page-item <?php echo ($page == $page_num)? "active": ""; ?>">
                        <a class="page-link"  id="<?php echo $page_num; ?>">
                            <?php echo $page_num; ?>
                        </a>
                    </li>
                <?php
                }  
            }
        ?>

        <li class="page-item <?php echo ($Pagination->hasNext())? "disabled": ""; ?>">
            <a class="page-link" id="<?php echo $Pagination->getNextPage(); ?>">
                <i class="fas fa-caret-square-right"></i>
            </a>
        </li>

    </ul>
</div>

<div class="card table-responsive">

    <table class="table table-sm table-hover card-header officer_table">
    
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Position</th>
                <th>Dept.</th>
                <th>Gender</th>
            </tr>
        </thead>

        <tbody>
            <?php 
                if(!$sql || mysqli_num_rows($sql)==0){
                    ?>
                    <tr>
                        <td colspan="6" class="table-info">No Results Found!</td>
                    </tr>
                    <?php
                }

                while ($obj = $sql -> fetch_object()) {
                    ?>
                    <tr id="<?php echo htmlspecialchars($obj->User_ID); ?>" class="rows officer_table_row_<?php echo htmlspecialchars($obj->User_ID); ?>">
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->User_ID); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Name); ?></td>
                        <td class="text-nowrap">
                            <?php 
                                switch (htmlspecialchars($obj->User_Type)) {
                                    case 'CHP':
                                        echo "Dept. Admin";
                                        break;
                                    case 'EVL':
                                        echo "Evaluator";
                                        break;
                                }
                            ?>
                        </td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Department_Code); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars(ucfirst($obj->Gender)); ?></td>
                    </tr>
                    <?php
                }
                $sql -> free_result();
            ?>
        </tbody>
     </table>
</div>

