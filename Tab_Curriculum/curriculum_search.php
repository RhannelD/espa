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
    $notIN = "";

    if(!empty($_REQUEST['page'])) {
        $page = $_REQUEST['page']; 
    }

    if(!empty($_REQUEST['notIN'])) {    // This is for the Curriculum Duplication "Tab_Curriculum_Open/curriculum_open_edit.php"
        $notIN = $_REQUEST['notIN'];
        $notIN = "AND c.Curriculum_ID != '$notIN'"; 
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
        SELECT c.Curriculum_ID, p.Program_Code, c.Track, YEAR(Academic_Year) AS 'AcademicYear', 
            GROUP_CONCAT(cr.Reference SEPARATOR ',') AS 'References' 
        FROM `curriculum` c
            INNER JOIN program p ON c.Program_ID=p.Program_ID
            INNER JOIN curriculum_references cr ON c.Curriculum_ID=cr.Curriculum_ID 
        WHERE (c.Curriculum_ID LIKE '$search%' 
            OR c.Program_ID IN (SELECT p.Program_ID FROM program p WHERE p.Program_Code LIKE '%$search%') 
            OR c.Track LIKE '%$search%'
            OR YEAR(Academic_Year) LIKE '$search%')
            $notIN 
            $sql_usertype
        GROUP BY c.Curriculum_ID
    ");
    $total_pages = ceil(mysqli_num_rows($sql)/$per_page);

    $Pagination = new Pagination($page,$total_pages);

    $start_page = ($Pagination->getPage() - 1) * $per_page;

    $sql = $mysqli->query("
        SELECT c.Curriculum_ID, p.Program_Code, c.Track, YEAR(Academic_Year) AS 'AcademicYear', 
            GROUP_CONCAT(cr.Reference SEPARATOR ',') AS 'References' 
        FROM `curriculum` c
            INNER JOIN program p ON c.Program_ID=p.Program_ID
            INNER JOIN curriculum_references cr ON c.Curriculum_ID=cr.Curriculum_ID 
        WHERE (c.Curriculum_ID LIKE '$search%' 
            OR c.Program_ID IN (SELECT p.Program_ID FROM program p WHERE p.Program_Code LIKE '%$search%') 
            OR c.Track LIKE '%$search%'
            OR YEAR(Academic_Year) LIKE '$search%')
            $notIN 
            $sql_usertype
        GROUP BY c.Curriculum_ID
        LIMIT $start_page, $per_page
    ");
?>

<div class="table-responsive">
    <ul class="pagination justify-content-end pagination-md curriculum_pagination">


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

    <table class="table table-sm table-hover card-header curriculum_table">
    
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Track</th>
                <th>Year</th>
                <th>References</th>
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
                    <tr id="<?php echo htmlspecialchars($obj->Curriculum_ID); ?>" class="rows curriculum_table_row_<?php echo htmlspecialchars($obj->Curriculum_ID); ?>">
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Curriculum_ID); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Program_Code); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Track); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->AcademicYear.'-'.(intval($obj->AcademicYear)+1)); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->References); ?></td>
                    </tr>
                    <?php
                }
                $sql -> free_result();
            ?>
        </tbody>

     </table>
  
</div>

