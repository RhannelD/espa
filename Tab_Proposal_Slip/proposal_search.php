<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
    include "../database/dbconnection.php";
    include "../Standard_Functions/pagination.php";
    include "../Standard_Functions/user_departments.php";

    $UserAuth = unserialize($_SESSION['UserAuth']);
    $user_id = $UserAuth->get_UserID();
    $user_type = $UserAuth->get_UserType();

    $search = "";
    $per_page = 15;
    $page = 1;
    $order = 'ASC';
    $show = 'all';
    $date = date('Y-m-d');

    if(!empty($_REQUEST['order'])) {
        $order = $_REQUEST['order']; 
    }

    if(!empty($_REQUEST['show'])) {
        $show = $_REQUEST['show']; 
    }

    if(!empty($_REQUEST['page'])) {
        $page = $_REQUEST['page']; 
    }

    if(!empty($_REQUEST['date'])) {
        $date = $_REQUEST['date']; 
    }

    if(!empty($_REQUEST['search'])) {
        $search = $_REQUEST['search'];
    }
    $search = $mysqli->real_escape_string(str_replace("\\", "\\\\", $search));

    $sql_usertype = '';
    if ($user_type == 'CHP' || $user_type == 'EVL') {
        $sql_usertype = 'AND Department_ID = '.getUserDepartments($mysqli, $user_id);
    }
    
    $sql_student = '';
    if ($user_type == 'STD') {
        $sql_student = "AND u.User_ID = '$user_id'";
    }

    switch ($show) {
        case 'today':
            // Display History created today
            $date = date('Y-m');
            $query = "
                SELECT ps.Slip_ID, us.SR_Code, CONCAT(u.Firstname, ' ', u.Lastname) AS Name, ps.Description, ps.Date
                FROM proposal_slip ps 
                    INNER JOIN user_student us ON ps.Student_ID = us.Student_ID 
                    INNER JOIN user u ON us.User_ID = u.User_ID 
                    INNER JOIN curriculum c ON us.Curriculum_ID = c.Curriculum_ID 
                    INNER JOIN program p ON c.Program_ID = p.Program_ID
                WHERE (us.SR_Code LIKE '$search%' 
                    OR CONCAT(u.Firstname, ' ', u.Lastname) LIKE '%$search%' 
                    OR ps.Description LIKE '%$search%')
                    AND DATE_FORMAT(ps.Date,'%Y-%m') = '$date' 
                    $sql_usertype
                    $sql_student
                ORDER BY ps.Date $order, ps.Slip_ID $order
            ";
            break;

        case 'date':
            // Display History created on a set year and month
            $query = "
                SELECT ps.Slip_ID, us.SR_Code, CONCAT(u.Firstname, ' ', u.Lastname) AS Name, ps.Description, ps.Date
                FROM proposal_slip ps 
                    INNER JOIN user_student us ON ps.Student_ID = us.Student_ID 
                    INNER JOIN user u ON us.User_ID = u.User_ID 
                    INNER JOIN curriculum c ON us.Curriculum_ID = c.Curriculum_ID 
                    INNER JOIN program p ON c.Program_ID = p.Program_ID
                WHERE (us.SR_Code LIKE '$search%' 
                    OR CONCAT(u.Firstname, ' ', u.Lastname) LIKE '%$search%' 
                    OR ps.Description LIKE '%$search%')
                    AND DATE_FORMAT(ps.Date,'%Y-%m') = '$date' 
                    $sql_usertype
                    $sql_student
                ORDER BY ps.Date $order, ps.Slip_ID $order
            ";
            break;

        default:
            // Display all of the history
            $query = "
                SELECT ps.Slip_ID, us.SR_Code, CONCAT(u.Firstname, ' ', u.Lastname) AS Name, ps.Description, ps.Date
                FROM proposal_slip ps 
                    INNER JOIN user_student us ON ps.Student_ID = us.Student_ID 
                    INNER JOIN user u ON us.User_ID = u.User_ID 
                    INNER JOIN curriculum c ON us.Curriculum_ID = c.Curriculum_ID 
                    INNER JOIN program p ON c.Program_ID = p.Program_ID
                WHERE (us.SR_Code LIKE '$search%' 
                    OR CONCAT(u.Firstname, ' ', u.Lastname) LIKE '%$search%' 
                    OR ps.Description LIKE '%$search%')
                    $sql_usertype
                    $sql_student
                ORDER BY ps.Date $order, ps.Slip_ID $order
            ";
            break;
    }

    $sql = $mysqli->query($query);
    $total_pages = ceil(mysqli_num_rows($sql)/$per_page);

    $Pagination = new Pagination($page,$total_pages);

    $start_page = ($Pagination->getPage() - 1) * $per_page;

    $sql = $mysqli->query("$query LIMIT $start_page, $per_page ");
?>

<div class="table-responsive">
    <ul class="pagination justify-content-end pagination-md proposal_pagination">


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

    <table class="table table-sm table-hover card-header proposal_table">
    
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <?php  
                if ($user_type != 'STD') {
                    ?>
                    <th>SR Code</th>
                    <th>Name</th>
                    <?php
                }
                ?>
                <th>Date</th>
                <th>Description</th>
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
                    <tr class="rows proposal_table_row_<?php echo htmlspecialchars($obj->Slip_ID); ?>" id="<?php echo htmlspecialchars($obj->Slip_ID); ?>">
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Slip_ID); ?></td>
                        <?php  
                        if ($user_type != 'STD') {
                            ?>
                            <td class="text-nowrap"><?php echo htmlspecialchars($obj->SR_Code); ?></td>
                            <td class="text-nowrap"><?php echo htmlspecialchars($obj->Name); ?></td>
                            <?php
                        }
                        ?>
                        <td class="text-nowrap"><?php echo date('d M, Y',strtotime(htmlspecialchars($obj->Date))); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Description); ?></td>
                    </tr>
                    <?php
                }
                $sql -> free_result();
            ?>
        </tbody>

     </table>
  
</div>

