<?php  
	include '../library-tcpdf/tcpdf.php';
	include "../database/dbconnection.php";
	include "../Standard_Functions/year_sem.php";
	include "../Standard_Functions/grade_converter.php";

	if(empty($_REQUEST['id'])&&empty($_REQUEST['sr_code'])) {
		exit();
    }


	/**
	 * 
	 */
	class Curriculum extends TCPDF
	{
		protected $id;
		protected $sr_code;
		protected $name;
		protected $grades;
		protected $Curriculum;

		protected $mysqli;
		protected $pdf2;
		
		function __construct($mysqli, $id, $sr_code=null, $grades = false)
		{	
			$custom_layout = array(215.9, 330.2);
			parent::__construct('P', 'mm', $custom_layout, true, 'UTF-8', false);
			$this->SetCreator(PDF_CREATOR);
			$this->SetAuthor('BatStateU');
			$this->SetTitle('Curriculum');
			$this->SetSubject('Curriculum');

			$this->SetMargins(6, 3, 6, true);

			$this->setPrintHeader(false);
			$this->setPrintFooter(false);

			$this->pdf2 = clone $this;

			$this->AddPage();

			$this->id = $id;
			$this->sr_code = $sr_code;
			$this->grades = $grades;
			$this->mysqli = $mysqli;

			$this->setCurriculumID();

			$this->setCurriculum();

			$this->printHeader();
			$this->printSubHeader();
			$this->printNameAndTableHeader();

			$this->printPerSem($mysqli);

			$this->Output('Curriculum.pdf', 'I');
		}

		function setCurriculumID(){
			if($this->id != null) {
				$this->grades = false;
				return;
			}

			$mysqli = $this->mysqli;
			$sql = $mysqli->query("
				SELECT Curriculum_ID, CONCAT(u.Firstname, ' ', u.Lastname) AS Name
				FROM user_student us 
					INNER JOIN user u ON us.User_ID = u.User_ID
				WHERE SR_Code = '$this->sr_code'
			");
			if(!$sql)
				exit();
		  	if($sql->num_rows <= 0)
		  		exit();
			while ($obj = $sql -> fetch_object()){
		  		$this->id = $obj->Curriculum_ID;
		  		$this->name = $obj->Name;
		  		break;
		  	}
		}

		function setCurriculum(){
			$mysqli = $this->mysqli;
			$sql = $mysqli->query("
				SELECT c.Curriculum_ID, sd.Department_Title, p.Program_Code, p.Program_Title, c.Track, YEAR(Academic_Year) AS 'AcademicYear', 
		            GROUP_CONCAT(cr.Reference SEPARATOR ', ') AS 'References', sd.Logo 
		        FROM `curriculum` c
		            INNER JOIN program p ON c.Program_ID=p.Program_ID
		        	INNER JOIN department sd ON p.Department_ID=sd.Department_ID
		            INNER JOIN curriculum_references cr ON c.Curriculum_ID=cr.Curriculum_ID 
		        WHERE c.Curriculum_ID = $this->id
			");
			if(!$sql)
				exit();
		  	if($sql->num_rows <= 0)
		  		exit();
			while ($obj = $sql -> fetch_object()){
		  		$this->Curriculum = $obj;
		  		break;
		  	}
		}

		function printHeader(){
			$html = '
				<style> 
					*{
						font-family: "Times New Roman", Times;
						text-align: center;
						font-size: 12px;
					}
				</style>

				<table>
				  	<tr>
					    <td rowspan="3" colspan="3"></td>
					    <td colspan="4">Republic of the Philippines</td>
					    <td rowspan="3" colspan="3"></td>
				  	</tr>
				 	<tr>
					    <td colspan="4"><b>BATANGAS STATE UNIVERSITY ARASOF-NASUGBU</b></td>
				  	</tr>
				  	<tr>
					    <td colspan="4">Nasugbu, Batangas</td>
				  	</tr>
				</table>
			';

			$this->writeHTML($html, true, false, true, false, '');

			$this->Image('../img/icon/BSU_icon.png', 45, 4, 18, '', '', 'http://www.tcpdf.org', '', false, 300);
			$this->Image('../img/dept_logo/'.$this->Curriculum->Logo, 150, 4, 18, '', '', 'http://www.tcpdf.org', '', false, 300);
		}

		function printSubHeader(){
			$track = '';
			$Curriculum = $this->Curriculum;

			if(!empty($Curriculum->Track)){
				$track = '
					<tr>
					    <td colspan="3"><b><u>'.$Curriculum->Track.'</u></b></td>
				  	</tr>
				';
			}

			$html = '
				<style> 
					*{
						font-family: "Times New Roman", Times;
						text-align: center;
						font-size: 11px;
					}
				</style>

				<table>
				  	<tr>
					    <td rowspan="5"></td>
					    <td colspan="3"><b>'.$Curriculum->Department_Title.'</b></td>
					    <td rowspan="5"></td>
				  	</tr>
				 	<tr>
					    <td colspan="3"><b>'.$Curriculum->Program_Title.'</b></td>
				  	</tr>
				  	'.$track.'
				  	<tr>
					    <td colspan="3">Academic Year '.$Curriculum->AcademicYear.'-'.(intval($Curriculum->AcademicYear)+1).'</td>
				  	</tr>
				  	<tr>
					    <td colspan="3">Reference: <b>'.$Curriculum->References.'</b></td>
				  	</tr>
				</table>
			';
			$this->writeHTML($html, true, false, true, false, '');
		}

		function printNameAndTableHeader(){
			$this->name_srcode();
			$this->table_header();
			$this->MultiCell(0, 0, "", 0);
		}

		function name_srcode(){
			$sr_code = ($this->sr_code != null)? '<u>'.$this->sr_code.'</u>': '___________________________________________________';
			$name = ($this->name != null)? '<u>'.$this->name.'</u>': '__________________________________________';

			$html = '
				<style> 
					*{
						font-family: "Times New Roman", Times;
						font-size: 11px;
					}
				</style>

				<table>
				  	<tr>
					    <td colspan="3">
					    	Name: '.$name.'
					    	<br>
					    	Student Number: '.$sr_code.'
					    </td>
				  	</tr>
				</table>
			';
			$this->writeHTML($html, true, false, true, false, '');
		}

		function table_header(){
			$this->SetFont ('times', 'B', 10 , '', 'default', true );
			$this->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
			// $this->Cell(18, 14, 'Final Grade', 1, 0, 'C', 0, '', 0); 
			$this->MultiCell(17.6, 14, "Final\nGrade", 1, 'C', 0, 0, '', '', true, 0, false, true, 13,'M');
			$this->MultiCell(23.1, 14, "Course Code", 1, 'C', 0, 0, '', '', true, 0, false, true, 13,'M');
			$this->MultiCell(79.4, 14, "Course Title", 1, 'C', 0, 0, '', '', true, 0, false, true, 13,'M');
			$complex_cell_border = array(
			   'T' => array('width' => 0.5, 'color' => array(0,0,0), 'dash' => 0),
			   // 'R' => array('width' => 0.5, 'color' => array(0,0,0), 'dash' => 0),
			   'B' => array('width' => 0.5, 'color' => array(0,0,0), 'dash' => 0),
			   'L' => array('width' => 0.5, 'color' => array(0,0,0), 'dash' => 0),
			);
			$this->MultiCell(14.5, 14, "Unit/s", $complex_cell_border, 'C', 0, 0, '', '', true, 0, false, true, 13,'M');
			$this->MultiCell(23, 0, "", 0, 'C', 0, 0, '', '', true, 0, false, true, 0,'M');
			$this->MultiCell(22.8, 14, "Pre-\nRequisite/s", 1, 'C', 0, 0, '', '', true, 0, false, true, 13,'M');
			$this->MultiCell(22.8, 14, "Co-\nRequisite/s", 1, 'C', 0, 0, '', '', true, 0, false, true, 13,'M');
			$complex_cell_border = array(
			   'T' => array('width' => 0.5, 'color' => array(0,0,0), 'dash' => 0),
			   'R' => array('width' => 0.5, 'color' => array(0,0,0), 'dash' => 0),
			   'B' => array('width' => 0.5, 'color' => array(0,0,0), 'dash' => 0),
			   'L' => array('width' => 0.5, 'color' => array(0,0,0), 'dash' => 0),
			);
			$this->MultiCell(23.5, 7, "Hours", $complex_cell_border, 'C', 0, 1, 140, '', true, 0, false, true, 7,'M');
			$complex_cell_border = array(
			   // 'T' => array('width' => 0.5, 'color' => array(0,0,0), 'dash' => 0),
			   'R' => array('width' => 0.5, 'color' => array(0,0,0), 'dash' => 0),
			   'B' => array('width' => 0.5, 'color' => array(0,0,0), 'dash' => 0),
			   'L' => array('width' => 0.5, 'color' => array(0,0,0), 'dash' => 0),
			);
			$this->SetFont ('times', 'B', 11 , '', 'default', true );
			$this->MultiCell(11.9, 7, "Lec", $complex_cell_border, 'C', 0, 0, 140, '', true, 0, false, true, 7,'M');
			$this->MultiCell(11.6, 7, "Lab", $complex_cell_border, 'C', 0, 1, '', '', true, 0, false, true, 7,'M');
		}

		function printPerSem(){
			$mysqli = $this->mysqli;
			$sql = $mysqli->query("
				SELECT DISTINCT Year_Level, Semester
				FROM curriculum_courses
				WHERE Curriculum_ID = $this->id
			");
			if(!$sql)
				exit();
			if($sql->num_rows > 0){
				while ($obj = $sql -> fetch_object()) {
					$this->prinPertYearSem($obj->Year_Level, $obj->Semester);
				}
			}
		}

		function prinPertYearSem($year, $sem){
			$mysqli = $this->mysqli;
			$courses = $mysqli->query("
				SELECT cs.Course_ID, cs.Course_Code, cs.Course_Title, cs.Units, cs.Lecture, cs.Laboratory, cs.`Req Standing` AS req,
					(SELECT GROUP_CONCAT(cs1.Course_Code SEPARATOR ', ') FROM courses cs1 INNER JOIN pre_requisites p1 
				     	ON cs1.Course_ID=p1.Pre_Requisite WHERE p1.Course_ID=cs.Course_ID)  AS PreReqs,
				    (SELECT GROUP_CONCAT(cs2.Course_Code SEPARATOR ',') 
	                 FROM courses cs2 
	                 	INNER JOIN pre_requisites p2 ON cs2.Course_ID=p2.Course_ID 
	                 	INNER JOIN curriculum_courses cc2 ON cc2.Course_ID=cs2.Course_ID
	                 WHERE p2.Pre_Requisite=cs.Course_ID 
	                	AND cc2.Curriculum_ID=cc.Curriculum_ID) AS CoReqs
				FROM curriculum c 
					INNER JOIN curriculum_courses cc ON c.Curriculum_ID=c.Curriculum_ID
				    INNER JOIN courses cs ON cc.Course_ID=cs.Course_ID
				WHERE cc.Year_Level= $year
					AND cc.Semester= $sem
					AND cc.Curriculum_ID= $this->id
				GROUP BY cs.Course_Code ORDER BY cc.CuCo_Code
			");
			if(!$courses)
				exit();

			$style = '
				<style> 
					*{
						font-family: "Times New Roman", Times;
						text-align: center;
					}
					td.content{
						font-size: 11px;
						font-weight: normal;
					}
					td.foot{
						font-size: 11px;
						font-weight: bold;
					}
					td.year{
						font-size: 12px;
					}
					td.left{
						text-align: left;
					}
					td.center{
						text-align: center;
					}
					.red{
						color: red;
					}
					.underline{
						border-bottom: 0.1px solid black;
					}
					hr{
						height: 0.1px;
					}
				</style>
			';
			// span{
			// 	font-size: 10px;
			// }

			$html = $style.'
				<table nobr="true">
				  	<tr nobr="true">
				  		<td nobr="true" colspan="8" class="year">'.getYearSem_forPdf($year, $sem).'</td>
				  	</tr>
			';
			$totalUnits = 0;
			$totalLecture = 0;
			$totalLaboratory = 0;

		  	while ($course = $courses -> fetch_object()) {
		  		$totalUnits += intval($course->Units);
		  		$totalLecture += intval($course->Lecture);
		  		$totalLaboratory += intval($course->Laboratory);

		  		$lecture = (intval($course->Lecture)==0)? "-": $course->Lecture;
		  		$laboratory = (intval($course->Laboratory)==0)? "-": $course->Laboratory;

		  		$coreq = ($course->CoReqs=="")? "-": $course->CoReqs;
		  		$prereq = ($course->PreReqs=="")? null: $course->PreReqs;
		  		if ($course->PreReqs == ""){
		  			if ($course->req == "") {
		  				$prereq = "-";
		  			} else {
		  				$prereq = $course->req;
		  			}
		  		} else {
		  			$prereq = $course->PreReqs;
		  		}

		  		$grade = $this->getStudentCourseGrades($course->Course_ID);

		  		$html .= '
				  	<tr nobr="true">
					    <td nobr="true" width="50px"  height="2px" class="content center underline" style="font-size: '.$this->getAdjustedFontSize(10,$grade,50,$style).'px;">'.$grade.'</td>
					    <td nobr="true" width="66px"  height="2px" class="content left" style="font-size: '.$this->getAdjustedFontSize(11,$course->Course_Code,66,$style).'px;">'.$course->Course_Code.'</td>
					    <td nobr="true" width="225px" height="2px" class="content left" style="font-size: '.$this->getAdjustedFontSize(11,$course->Course_Title,225,$style).'px;">'.$course->Course_Title.'</td>
					    <td nobr="true" width="40px"  height="2px" class="content center">'.$course->Units.'</td>
					    <td nobr="true" width="32px"  height="2px" class="content center">'.$lecture.'</td>
					    <td nobr="true" width="32px"  height="2px" class="content center">'.$laboratory.'</td>
					    <td nobr="true" width="65px"  height="2px" class="content center" style="font-size: '.$this->getAdjustedFontSize(11,$prereq,65,$style).'px;">'.$prereq.'</td>
					    <td nobr="true" width="65px"  height="2px" class="content center" style="font-size: '.$this->getAdjustedFontSize(11,$coreq,65,$style).'px;">'.$coreq.'</td>
				  	</tr>
				';
			}
			$totalUnits = (intval($totalUnits)==0)? "-": $totalUnits;
			$totalLecture = (intval($totalLecture)==0)? "-": $totalLecture;
			$totalLaboratory = (intval($totalLaboratory)==0)? "-": $totalLaboratory;

			$html .= '
					<tr nobr="true">
					    <td nobr="true" width="341px" height="2px" class="foot center" colspan="3">TOTAL</td>
					    <td nobr="true" width="40px"  height="2px" class="foot center">'.$totalUnits.'</td>
					    <td nobr="true" width="32px"  height="2px" class="foot center">'.$totalLecture.'</td>
					    <td nobr="true" width="32px"  height="2px" class="foot center">'.$totalLaboratory.'</td>
					    <td nobr="true" width="130px"  height="2px" class="foot center" colspan="3"></td>
				  	</tr>
				</table>
			';

			$this->ifPageBreak($html);

			$this->writeHTML($html, true, false, true, false, '');
		}

		function getStudentCourseGrades($Course_ID){
			if (!$this->grades){
				return '';
			}

			//Grades
			$mysqli = $this->mysqli;
			
			$grades = $mysqli->query("
				SELECT g.Grade 
				FROM grades g 
					INNER JOIN user_student us ON g.Student_ID = us.Student_ID 
				WHERE us.SR_Code = '$this->sr_code'
					AND g.Course_ID = '$Course_ID'
			");
			if (!$grades) {
				exit();
			}
			if ($grades->num_rows == 0) {
				return '';
			}

			$concatinated_grades= '';
			$rows = $grades->num_rows;
			$iteration = 1;
			while ($grade = $grades -> fetch_object()){
				$concatinated_grades .= '
					<span class="'.(($grade->Grade > 3)? 'red': '').'">'
					.convert_grade($grade->Grade).(($iteration++<$rows)?',':'').
					'</span>
				';
			}
			// return $concatinated_grades.'<hr>';
			return $concatinated_grades;
		}

		function ifPageBreak($html){
			$this->pdf2->AddPage();
			$this->pdf2->writeHTML($html, true, false, true, false, '');
			$height = $this->pdf2->getY();
			$this->pdf2->deletePage($this->pdf2->getPage());
			$pagebreak = $this->checkPageBreak($height);
			if($pagebreak){
				$this->MultiCell(0, 0, "", 0);
				$this->printNameAndTableHeader();
			}
		}

		function getAdjustedFontSize($font_size, $text, $width, $style){
			$font_size+=0.05;
			do{
	  			$font_size-=0.05;
				$html = $style.'
					<table nobr="true">
						<tr nobr="true">
							<td nobr="true" width="'.$width.'px"  height="2px" class="content center" style="font-size: '.$font_size.'px;">'.$text.'</td>
						</tr>
					</table>
				';		
	  			$this->pdf2->AddPage();
				$this->pdf2->writeHTML($html, true, false, true, false, '');
				$height = $this->pdf2->getY();
				$this->pdf2->deletePage($this->pdf2->getPage());
	  		} while($height>13.15 && $font_size>1);
	  		return $font_size;
		}
	}


    if (!empty($_REQUEST['id'])) {
    	new Curriculum($mysqli, $id = $_REQUEST['id']);
    } else if (!empty($_REQUEST['sr_code'])) {
    	$grades = (!empty($_REQUEST['grade']));
    	new Curriculum($mysqli, null, $sr_code = $_REQUEST['sr_code'], $grades = $grades);
    }
	
	
?>