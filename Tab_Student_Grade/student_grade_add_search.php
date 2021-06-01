<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
    include "../database/dbconnection.php";
    include "../Standard_Functions/grade_converter.php";
    include "../Standard_Functions/pagination.php";

    $search = "";
    $per_page = 15;
    $page = 1;

    if(!empty($_REQUEST['page'])) {
        $page = $_REQUEST['page']; 
    }

    if(!empty($_REQUEST['number_of_rows'])) {
        $per_page = $_REQUEST['number_of_rows']; 
    }

    if(empty($_REQUEST['sr_code'])) {
        exit();
    }

    $sr_code = $mysqli->real_escape_string($_REQUEST['sr_code']);

    if(!empty($_REQUEST['search'])) {
        $search = ($_REQUEST['search']);
    }
    $search = $mysqli->real_escape_string(str_replace("\\", "\\\\", $search));

    $to_be_query = $_REQUEST['to_be_query'];

    switch ($to_be_query) {
        case 'Unfinished':
            // Query Unfinished or not yet passed courses from the curriculum
            $query = "
                SELECT g.Grade_Rec_ID, g.Grade, c.Course_ID, c.Course_Code, c.Course_Title, c.Units, c.Lecture, c.Laboratory
                FROM user_student us 
                    INNER JOIN curriculum_courses cc ON us.Curriculum_ID = cc.Curriculum_ID 
                    INNER JOIN courses c ON cc.Course_ID = c.Course_ID
                    INNER JOIN grades g ON us.Student_ID = g.Student_ID AND cc.Course_ID = g.Course_ID
                WHERE us.SR_Code = '$sr_code'
                    AND (SELECT MIN(g2.Grade) FROM grades g2 WHERE g2.Student_ID = us.Student_ID AND g2.Course_ID = cc.Course_ID
                         ) > 3
                    AND (c.Course_ID LIKE '$search%'
                    OR c.Course_Code LIKE '%$search%'
                    OR c.Course_Title LIKE '%$search%')
                GROUP BY cc.Course_ID
                UNION ALL 
                SELECT NULL AS Grade_Rec_ID, NULL AS Grade, c.Course_ID, c.Course_Code, c.Course_Title, c.Units, c.Lecture, c.Laboratory
                FROM user_student us 
                    INNER JOIN curriculum_courses cc ON us.Curriculum_ID = cc.Curriculum_ID 
                    INNER JOIN courses c ON cc.Course_ID = c.Course_ID
                    LEFT JOIN grades g ON us.Student_ID = g.Student_ID AND cc.Course_ID = g.Course_ID
                WHERE us.SR_Code = '$sr_code' 
                    AND (g.Grade IS NULL 
                         OR (
                         SELECT MIN(g2.Grade) FROM grades g2 WHERE g2.Student_ID = us.Student_ID AND g2.Course_ID = cc.Course_ID
                         ) > 3)
                    AND (c.Course_ID LIKE '$search%'
                    OR c.Course_Code LIKE '%$search%'
                    OR c.Course_Title LIKE '%$search%')
                GROUP BY cc.Course_ID
                ORDER BY COurse_ID ASC, -Grade_Rec_ID DESC
            ";
            break;
        
        case 'All':
            // Query all of the courses from the database
            $query = "
                SELECT g.Grade_Rec_ID, g.Grade, c.Course_ID, c.Course_Code, c.Course_Title, c.Units, c.Lecture, c.Laboratory
                FROM user_student us 
                    INNER JOIN courses c
                    INNER JOIN grades g ON us.Student_ID = g.Student_ID AND c.Course_ID = g.Course_ID
                WHERE us.SR_Code = '$sr_code'
                    AND (c.Course_ID LIKE '$search%'
                    OR c.Course_Code LIKE '%$search%'
                    OR c.Course_Title LIKE '%$search%')
                UNION ALL 
                SELECT NULL AS Grade_Rec_ID, NULL AS Grade, c.Course_ID, c.Course_Code, c.Course_Title, c.Units, c.Lecture, c.Laboratory
                FROM user_student us 
                    INNER JOIN courses c
                    LEFT JOIN grades g ON us.Student_ID = g.Student_ID AND c.Course_ID = g.Course_ID
                WHERE us.SR_Code = '$sr_code' 
                    AND (g.Grade IS NULL 
                         OR (
                         SELECT MIN(g2.Grade) FROM grades g2 WHERE g2.Student_ID = us.Student_ID AND g2.Course_ID = c.Course_ID
                         ) > 3)
                    AND (c.Course_ID LIKE '$search%'
                    OR c.Course_Code LIKE '%$search%'
                    OR c.Course_Title LIKE '%$search%')
                GROUP BY c.Course_ID
                ORDER BY COurse_ID ASC, -Grade_Rec_ID DESC
            ";
            break;

        default:
            // Query all courses from the curriculum
            $query = "
                SELECT g.Grade_Rec_ID, g.Grade, c.Course_ID, c.Course_Code, c.Course_Title, c.Units, c.Lecture, c.Laboratory, Year_Level, Semester, CuCo_Code   
                FROM user_student us 
                    INNER JOIN curriculum_courses cc ON us.Curriculum_ID = cc.Curriculum_ID 
                    INNER JOIN courses c ON cc.Course_ID = c.Course_ID
                    INNER JOIN grades g ON us.Student_ID = g.Student_ID AND cc.Course_ID = g.Course_ID
                WHERE us.SR_Code = '$sr_code'
                    AND (c.Course_ID LIKE '$search%'
                    OR c.Course_Code LIKE '%$search%'
                    OR c.Course_Title LIKE '%$search%')
                UNION ALL 
                SELECT NULL AS Grade_Rec_ID, NULL AS Grade, c.Course_ID, c.Course_Code, c.Course_Title, c.Units, c.Lecture, c.Laboratory, Year_Level, Semester, CuCo_Code
                FROM user_student us 
                    INNER JOIN curriculum_courses cc ON us.Curriculum_ID = cc.Curriculum_ID 
                    INNER JOIN courses c ON cc.Course_ID = c.Course_ID
                    LEFT JOIN grades g ON us.Student_ID = g.Student_ID AND cc.Course_ID = g.Course_ID
                WHERE us.SR_Code = '$sr_code' 
                    AND (g.Grade IS NULL 
                         OR (
                         SELECT MIN(g2.Grade) FROM grades g2 WHERE g2.Student_ID = us.Student_ID AND g2.Course_ID = cc.Course_ID
                         ) > 3)
                    AND (c.Course_ID LIKE '$search%'
                    OR c.Course_Code LIKE '%$search%'
                    OR c.Course_Title LIKE '%$search%')
                GROUP BY cc.Course_ID
                ORDER BY Year_Level, Semester, CuCo_Code, Course_ID ASC, -Grade_Rec_ID DESC
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
    <ul class="pagination justify-content-end pagination-md courses_pagination">


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

    <table class="table table-sm table-hover card-header student_table">
    
        <thead class="thead-dark">
            <tr>
                <th class="text-nowrap text-center">Grade</th>
                <th class="text-nowrap">ID</th>
                <th class="text-nowrap">Code</th>
                <th class="text-nowrap">Title</th>
                <th class="text-nowrap">Units</th>
                <th class="text-nowrap">Lec</th>
                <th class="text-nowrap">Lab</th>
                <th class="text-nowrap text-center">
                    <button type="button" class="btn btn-success btn-sm save_all_grade">
                        <i class="fas fa-save"></i>
                        Save All
                    </button>
                </th>
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
                    <tr class="rows course_grade_row_<?php echo htmlspecialchars($obj->Grade_Rec_ID); ?>">
                        <td class="text-nowrap text-center <?php echo ($obj->Grade > 3)? 'text-danger': ''; ?>">
                            <?php 
                            if($obj->Grade > 0) {
                                echo htmlspecialchars(convert_grade($obj->Grade));
                            }
                            else{
                                ?>
                                <select class="form-control form-control-sm select_course select_course_add_grade_<?php echo htmlspecialchars($obj->Course_ID); ?>" id="<?php echo htmlspecialchars($obj->Course_ID); ?>">
                                    <option value=""></option>
                                    <option value="1">1.00</option>
                                    <option value="1.25">1.25</option>
                                    <option value="1.50">1.50</option>
                                    <option value="1.75">1.75</option>
                                    <option value="2">2.00</option>
                                    <option value="2.25">2.25</option>
                                    <option value="2.50">2.50</option>
                                    <option value="2.75">2.75</option>
                                    <option value="3">3.00</option>
                                    <option value="4" class="text-danger">INC</option>
                                    <option value="5" class="text-danger">5.00</option>
                                    <option value="6" class="text-danger">Dropped</option>
                                </select>
                                <?php
                            }
                            ?>
                        </td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Course_ID); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Course_Code); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Course_Title); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Units); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Lecture); ?></td>
                        <td class="text-nowrap"><?php echo htmlspecialchars($obj->Laboratory); ?></td>
                        <td class="text-nowrap text-center">
                            <?php  
                            if (!empty($obj->Grade_Rec_ID)){
                                ?>
                                    <button type="button" class="btn btn-danger btn-sm delete_grade"  id="<?php echo htmlspecialchars($obj->Grade_Rec_ID); ?>" data-toggle="modal" data-target="#confirm_delete_grade">
                                        <i class="fas fa-trash"></i>
                                        Delete
                                    </button>
                                <?php
                            } else {
                                ?>
                                    <button type="button" class="btn btn-success btn-sm d-none save_grade" id="<?php echo htmlspecialchars($obj->Course_ID); ?>">
                                        <i class="fas fa-save"></i>
                                        Save
                                    </button>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
                $sql -> free_result();
            ?>
        </tbody>

     </table>
  
</div>