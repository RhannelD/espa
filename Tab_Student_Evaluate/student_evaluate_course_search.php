<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
    include "../database/dbconnection.php";
    include "../Standard_Functions/pagination.php";

    $search = "";
    $per_page = 15;
    $page = 1;
    $added_courses = "0";

    if(!empty($_REQUEST['sr_code'])) {
        $sr_code = $_REQUEST['sr_code']; 
    }

    if(!empty($_REQUEST['page'])) {
        $page = $_REQUEST['page']; 
    }

    if(!empty($_REQUEST['search'])) {
        $search = $_REQUEST['search'];
    }
    $search = $mysqli->real_escape_string(str_replace("\\", "\\\\", $search));

    if (!empty($_REQUEST['added_courses'])) {
        $added_courses = implode(",",$_REQUEST['added_courses']);
    }

    $to_be_query = $_REQUEST['to_be_query'];

    switch ($to_be_query) {
        case 'Curriculum':
            // Query all courses from the curriculum
            $query = "
                SELECT c.Course_ID, c.Course_Code, c.Course_Title, c.Units, c.Lecture, c.Laboratory,
                    (SELECT GROUP_CONCAT(c1.Course_Code SEPARATOR ', ')
                     FROM pre_requisites p INNER JOIN courses c1 ON p.Pre_Requisite =c1.Course_ID
                     WHERE p.Course_ID=c.Course_ID) as 'Prereq' 
                FROM user_student us
                    INNER JOIN curriculum_courses cc ON us.Curriculum_ID = cc.Curriculum_ID
                    INNER JOIN courses c ON cc.Course_ID = c.Course_ID
                WHERE us.SR_Code = '$sr_code'
                    AND (c.Course_ID LIKE '$search%'
                    OR c.Course_Code LIKE '%$search%'
                    OR c.Course_Title LIKE '%$search%')
                    AND c.Course_ID NOT IN ($added_courses)
                GROUP BY c.Course_ID
                ORDER BY c.Course_ID
            ";
            break;

        case 'All':
            // Query all of the courses from the database
            $query = "
                SELECT c.Course_ID, c.Course_Code, c.Course_Title, c.Units, c.Lecture, c.Laboratory,
                    (SELECT GROUP_CONCAT(c1.Course_Code SEPARATOR ', ')
                     FROM pre_requisites p INNER JOIN courses c1 ON p.Pre_Requisite =c1.Course_ID
                     WHERE p.Course_ID=c.Course_ID) as 'Prereq' 
                FROM courses c
                WHERE (c.Course_ID LIKE '$search%'
                    OR c.Course_Code LIKE '%$search%'
                    OR c.Course_Title LIKE '%$search%')
                    AND c.Course_ID NOT IN ($added_courses)
                ORDER BY Course_ID
            ";
            break;

        default:
            // Query all courses from the curriculum
            $query = "
                SELECT  c.Course_ID, c.Course_Code, c.Course_Title, c.Units, c.Lecture, c.Laboratory,
                    (SELECT GROUP_CONCAT(c1.Course_Code SEPARATOR ', ')
                    FROM pre_requisites p INNER JOIN courses c1 ON p.Pre_Requisite =c1.Course_ID
                    WHERE p.Course_ID=c.Course_ID) as 'Prereq' 
                FROM user_student us 
                    INNER JOIN curriculum_courses cc ON us.Curriculum_ID = cc.Curriculum_ID
                    INNER JOIN courses c ON cc.Course_ID = c.Course_ID
                    LEFT JOIN grades g ON us.Student_ID = g.Student_ID AND g.Course_ID = c.Course_ID
                WHERE us.SR_Code = '$sr_code'
                    AND (g.Grade IS NULL 
                    OR (SELECT MIN(g2.Grade) FROM grades g2 WHERE g2.Course_ID = c.Course_ID AND g2.Student_ID = us.Student_ID GROUP BY g2.Course_ID) > 3)
                    AND c.Course_ID NOT IN (
                        SELECT pr3.Course_ID 
                        FROM user_student s3 
                            INNER JOIN pre_requisites pr3 
                            LEFT JOIN grades g3 ON s3.Student_ID=g3.Student_ID AND g3.Course_ID=pr3.Pre_Requisite 
                        WHERE pr3.Course_ID=c.Course_ID 
                            AND s3.Student_ID=us.Student_ID 
                            AND (g3.Grade IS NULL OR 
                                (SELECT MIN(g4.Grade) FROM user_student s4 
                                    INNER JOIN grades g4 ON s4.Student_ID=g4.Student_ID 
                                WHERE g4.Course_ID=pr3.Pre_Requisite AND s4.Student_ID=us.Student_ID)>3  ) 
                    ) 
                    AND (c.Course_ID LIKE '$search%'
                        OR c.Course_Code LIKE '%$search%'
                        OR c.Course_Title LIKE '%$search%')
                    AND c.Course_ID NOT IN ($added_courses)
                GROUP BY cc.Course_ID
                ORDER BY cc.Year_Level, cc.Semester, cc.CuCo_Code
            ";
            break;
    }


    $sql = $mysqli->query($query);
    $total_pages = ceil(mysqli_num_rows($sql)/$per_page);

    $Pagination = new Pagination($page,$total_pages);

    $start_page = ($Pagination->getPage() - 1) * $per_page;

    $sql = $mysqli->query($query." LIMIT $start_page, $per_page");
?>

<div class="table-responsive">
    <ul class="pagination justify-content-end pagination-md course_pagination">


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
                <th>Units</th>
                <th>Lec</th>
                <th>Lab</th>
                <th>Pre-Requisite/s</th>
                <th class="text-center">Action</th>
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
                    <tr id="<?php echo htmlspecialchars($obj->Course_ID); ?>" class="rows course_table_row_<?php echo htmlspecialchars($obj->Course_ID); ?>">
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Course_ID); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Course_Code); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Course_Title); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Units); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Lecture); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Laboratory); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Prereq); ?></td>
                        <td class="text-nowrap text-center">
                            <button type="button" class="btn btn-success btn-sm add_selected_course">
                                <i class="fas fa-plus-circle"></i>
                                Add Course
                            </button>
                        </td>
                    </tr>
                    <?php
                }
                $sql -> free_result();
            ?>
        </tbody>

     </table>
  
</div>

