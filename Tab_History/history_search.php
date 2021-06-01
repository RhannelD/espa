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
    if ($user_type != 'ADM') {
        $sql_usertype = 'AND Department_ID = '.getUserDepartments($mysqli, $user_id);
    }

    switch ($show) {
        case 'today':
            // Display History created today
            $date = date('Y-m-d');
            $query = "
                SELECT dh.Grading_History_ID, dh.Date, dh.Time, dh.History 
                FROM grading_history dh
                WHERE (dh.Grading_History_ID LIKE '$search%'
                    OR History LIKE '%$search%')
                    AND Date = '$date'
                    $sql_usertype
                ORDER BY dh.Date, dh.Time $order
            ";
            break;
        
        case 'after':
            // 
            $query = "
                SELECT dh.Grading_History_ID, dh.Date, dh.Time, dh.History 
                FROM grading_history dh
                WHERE (dh.Grading_History_ID LIKE '$search%'
                    OR History LIKE '%$search%')
                    AND Date >= '$date'
                    $sql_usertype
                ORDER BY dh.Date, dh.Time $order
            ";
            break;

        case 'before':
            // 
            $query = "
                SELECT dh.Grading_History_ID, dh.Date, dh.Time, dh.History 
                FROM grading_history dh
                WHERE (dh.Grading_History_ID LIKE '$search%'
                    OR History LIKE '%$search%')
                    AND Date <= '$date'
                    $sql_usertype
                ORDER BY dh.Date, dh.Time $order
            ";
            break;

        default:
            // Display all of the history
            $query = "
                SELECT dh.Grading_History_ID, dh.Date, dh.Time, dh.History 
                FROM grading_history dh
                WHERE (dh.Grading_History_ID LIKE '$search%'
                    OR History LIKE '%$search%')
                    $sql_usertype
                ORDER BY dh.Date $order, dh.Time $order
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
    <ul class="pagination justify-content-end pagination-md history_pagination">


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

    <table class="table table-sm table-hover card-header history_table">
    
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Time</th>
                <th>History</th>
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
                    <tr class="rows">
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Grading_History_ID); ?></td>
                        <td class="text-nowrap"><?php echo date('M-d-y',strtotime(htmlspecialchars($obj->Date))); ?></td>
                        <td class="text-nowrap"><?php echo date('H:i:s',strtotime(htmlspecialchars($obj->Time))); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->History); ?></td>
                    </tr>
                    <?php
                }
                $sql -> free_result();
            ?>
        </tbody>

     </table>
  
</div>

