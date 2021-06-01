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

    if(!empty($_REQUEST['page'])) {
        $page = $_REQUEST['page']; 
    }

    if(!empty($_REQUEST['search'])) {
        $search = $_REQUEST['search'];
    }
    $search = $mysqli->real_escape_string(str_replace("\\", "\\\\", $search));

    $sql_usertype = '';
    if ($user_type == 'CHP' || $user_type == 'EVL') {
        $sql_usertype = 'AND p.Department_ID = '.getUserDepartments($mysqli, $user_id);
    }
    
    $sql = $mysqli->query("
        SELECT p.Program_ID, p.Program_Code, p.Program_Title, d.Department_Code
        FROM program p 
            INNER JOIN department d ON p.Department_ID=d.Department_ID 
        WHERE (p.Program_ID LIKE '$search%' 
            OR p.Program_Code LIKE '%$search%' 
            OR p.Program_Title LIKE '%$search%'
            OR d.Department_Code LIKE '%$search%')
            $sql_usertype
    ");
    $total_pages = ceil(mysqli_num_rows($sql)/$per_page);

    $Pagination = new Pagination($page,$total_pages);

    $start_page = ($Pagination->getPage() - 1) * $per_page;

    $sql = $mysqli->query("
        SELECT p.Program_ID, p.Program_Code, p.Program_Title, d.Department_Code
        FROM program p 
            INNER JOIN department d ON p.Department_ID=d.Department_ID 
        WHERE (p.Program_ID LIKE '$search%' 
            OR p.Program_Code LIKE '%$search%' 
            OR p.Program_Title LIKE '%$search%'
            OR d.Department_Code LIKE '%$search%')
            $sql_usertype
        LIMIT $start_page, $per_page
    ");
?>

<div class="table-responsive">
    <ul class="pagination justify-content-end pagination-md program_pagination">


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

    <table class="table table-sm table-hover card-header program_table">
    
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Title</th>
                <th>Dept.</th>
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
                    <tr id="<?php echo htmlspecialchars($obj->Program_ID); ?>" class="rows program_table_row_<?php echo htmlspecialchars($obj->Program_ID); ?>">
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Program_ID); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Program_Code); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Program_Title); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Department_Code); ?></td>
                    </tr>
                    <?php
                }
                $sql -> free_result();
            ?>
        </tbody>

     </table>
  
</div>

