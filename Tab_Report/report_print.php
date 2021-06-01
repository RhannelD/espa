<?php  
include "../database/User_if_signed_in.php";
include "../database/dbconnection.php";
include "../database/UserAuth.php";
include '../pdf-generator/generate_report.php';
include "../Standard_Functions/pagination.php";
include "../Standard_Functions/user_departments.php";
include "../Standard_Functions/semester_schedule.php";

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

if(!empty($_REQUEST['department'])) {
    $sql_usertype = "";
    if ($_REQUEST['department'] != 'all') {
        $sql_usertype = 'AND d.Department_ID = '.$_REQUEST['department'];
    }
}

if(!empty($_REQUEST['page'])) {
    $page = $_REQUEST['page']; 
}

if(!empty($_REQUEST['rows'])) {
    $per_page = $_REQUEST['rows']; 
}

if(!empty($_REQUEST['search'])) {
    $search = $_REQUEST['search'];
}
$search = $mysqli->real_escape_string(str_replace("\\", "\\\\", $search));

if ($user_type != 'ADM') {
    $sql_usertype = 'AND d.Department_ID = '.getUserDepartments($mysqli, $user_id);
}


switch ($_REQUEST['report']) {
    case 'inc':
        $query = "
            SELECT us.SR_Code, CONCAT(u.Firstname, ' ', u.Lastname) AS Name, d.Department_Code, p.Program_Code, c.Track, c.Academic_Year 
        FROM user u 
            INNER JOIN user_student us ON u.User_ID=us.User_ID
            INNER JOIN curriculum c ON us.Curriculum_ID=c.Curriculum_ID
            INNER JOIN program p ON c.Program_ID=p.Program_ID
            INNER JOIN department d ON p.Department_ID=d.Department_ID
        WHERE us.Student_ID IN (
                SELECT g2.Student_ID 
                FROM curriculum_courses cc2 
                    INNER JOIN grades g2 ON cc2.Course_ID=g2.Course_ID
                WHERE g2.Student_ID=us.Student_ID
                    AND cc2.Curriculum_ID=c.Curriculum_ID
                    AND g2.Grade = 4
            )
            AND (
                SELECT True
                FROM curriculum_courses cc3
                    LEFT JOIN grades g3 ON cc3.Course_ID=g3.Course_ID
                WHERE g3.Student_ID=us.Student_ID
                    AND cc3.Curriculum_ID=c.Curriculum_ID
                    AND g3.Grade IS NULL
            ) IS NULL
            AND (
                us.SR_Code LIKE '$search%'
                OR CONCAT(u.Firstname, ' ', u.Lastname) LIKE '%$search%'
            )
            $sql_usertype
            ORDER BY us.SR_Code $order
        ";
        break;
    case 'dropped':
        $query = "
            SELECT us.SR_Code, CONCAT(u.Firstname, ' ', u.Lastname) AS Name, d.Department_Code, p.Program_Code, c.Track, c.Academic_Year 
        FROM user u 
            INNER JOIN user_student us ON u.User_ID=us.User_ID
            INNER JOIN curriculum c ON us.Curriculum_ID=c.Curriculum_ID
            INNER JOIN program p ON c.Program_ID=p.Program_ID
            INNER JOIN department d ON p.Department_ID=d.Department_ID
        WHERE us.Student_ID IN (
                SELECT g2.Student_ID 
                FROM curriculum_courses cc2 
                    INNER JOIN grades g2 ON cc2.Course_ID=g2.Course_ID
                WHERE g2.Student_ID=us.Student_ID
                    AND cc2.Curriculum_ID=c.Curriculum_ID
                    AND g2.Grade = 6
            )
            AND (
                SELECT True
                FROM curriculum_courses cc3
                    LEFT JOIN grades g3 ON cc3.Course_ID=g3.Course_ID
                WHERE g3.Student_ID=us.Student_ID
                    AND cc3.Curriculum_ID=c.Curriculum_ID
                    AND g3.Grade IS NULL
            ) IS NULL
            AND (
                us.SR_Code LIKE '$search%'
                OR CONCAT(u.Firstname, ' ', u.Lastname) LIKE '%$search%'
            )
            $sql_usertype
            ORDER BY us.SR_Code $order
        ";
        break;
    case 'failed':
        $query = "
            SELECT us.SR_Code, CONCAT(u.Firstname, ' ', u.Lastname) AS Name, d.Department_Code, p.Program_Code, c.Track, c.Academic_Year 
        FROM user u 
            INNER JOIN user_student us ON u.User_ID=us.User_ID
            INNER JOIN curriculum c ON us.Curriculum_ID=c.Curriculum_ID
            INNER JOIN program p ON c.Program_ID=p.Program_ID
            INNER JOIN department d ON p.Department_ID=d.Department_ID
        WHERE us.Student_ID IN (
                SELECT g2.Student_ID 
                FROM curriculum_courses cc2 
                    INNER JOIN grades g2 ON cc2.Course_ID=g2.Course_ID
                WHERE g2.Student_ID=us.Student_ID
                    AND cc2.Curriculum_ID=c.Curriculum_ID
                    AND g2.Grade = 5
            )
            AND (
                SELECT True
                FROM curriculum_courses cc3
                    LEFT JOIN grades g3 ON cc3.Course_ID=g3.Course_ID
                WHERE g3.Student_ID=us.Student_ID
                    AND cc3.Curriculum_ID=c.Curriculum_ID
                    AND g3.Grade IS NULL
            ) IS NULL
            AND (
                us.SR_Code LIKE '$search%'
                OR CONCAT(u.Firstname, ' ', u.Lastname) LIKE '%$search%'
            )
            $sql_usertype
            ORDER BY us.SR_Code $order
        ";
        break;
    
    default:   
        $SemesterSchedule = new SemesterSchedule();
        $SchoolYear = $SemesterSchedule->getCurrentSchoolYear()-2;

        $query = "
            SELECT us.SR_Code, CONCAT(u.Firstname, ' ', u.Lastname) AS Name, d.Department_Code, p.Program_Code, c.Track, c.Academic_Year 
            FROM user u 
                INNER JOIN user_student us ON u.User_ID=us.User_ID
                INNER JOIN curriculum c ON us.Curriculum_ID=c.Curriculum_ID
                INNER JOIN program p ON c.Program_ID=p.Program_ID
                INNER JOIN department d ON p.Department_ID=d.Department_ID
            WHERE us.Student_ID NOT IN (
                    SELECT g2.Student_ID 
                    FROM curriculum_courses cc2 
                        INNER JOIN grades g2 ON cc2.Course_ID=g2.Course_ID
                    WHERE g2.Student_ID=us.Student_ID
                        AND cc2.Curriculum_ID=c.Curriculum_ID
                        AND g2.Grade > 2.50
                )
                AND (
                    SELECT True
                    FROM curriculum_courses cc3
                        LEFT JOIN grades g3 ON cc3.Course_ID=g3.Course_ID
                    WHERE g3.Student_ID=us.Student_ID
                        AND cc3.Curriculum_ID=c.Curriculum_ID
                        AND g3.Grade IS NULL
                ) IS NULL
                AND c.Academic_Year <= $SchoolYear
                AND (
                    us.SR_Code LIKE '$search%'
                    OR CONCAT(u.Firstname, ' ', u.Lastname) LIKE '%$search%'
                )
                $sql_usertype
            ORDER BY us.SR_Code $order
        ";
        break;
}


$sql = $mysqli->query($query);
$total_pages = ceil(mysqli_num_rows($sql)/$per_page);

$Pagination = new Pagination($page,$total_pages);

$start_page = ($Pagination->getPage() - 1) * $per_page;

$query = "$query LIMIT $start_page, $per_page ";



$pdf = new Report($mysqli, $query, $_REQUEST['department'], $_REQUEST['report']);
$pdf->Output('Report.pdf', 'I');

?>