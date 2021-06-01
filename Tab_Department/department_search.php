<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
    include "../database/dbconnection.php";
    include "../Standard_Functions/pagination.php";

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
        SELECT Department_ID 
        FROM department 
        WHERE Department_ID Like '$search%' 
            OR Department_Code Like '%$search%' 
            OR Department_Title Like '%$search%'
    ");
    $total_pages = ceil(mysqli_num_rows($sql)/$per_page);

    $Pagination = new Pagination($page,$total_pages);

    $start_page = ($Pagination->getPage() - 1) * $per_page;

    $sql = $mysqli->query("
        SELECT Department_ID, Department_Code, Department_Title 
        FROM department 
        WHERE Department_ID Like '$search%' 
            OR Department_Code Like '%$search%' 
            OR Department_Title Like '%$search%'
        LIMIT $start_page, $per_page
    ");
?>

<div class="table-responsive">
    <ul class="pagination justify-content-end pagination-md department_pagination">


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

    <table class="table table-sm table-hover card-header department_table">
    
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Title</th>
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
                    <tr id="<?php echo htmlspecialchars($obj->Department_ID); ?>" class="rows department_table_row_<?php echo htmlspecialchars($obj->Department_ID); ?>">
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Department_ID); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Department_Code); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Department_Title); ?></td>
                    </tr>
                    <?php
                }
                $sql -> free_result();
            ?>
        </tbody>

     </table>
  
</div>

