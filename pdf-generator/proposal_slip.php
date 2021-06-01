<?php  
	include '../library-tcpdf/tcpdf.php';
	include "../Standard_Functions/year_sem.php";
	include "../Standard_Functions/semester_schedule.php";

	/**
	 * 
	 */
	class ProposalSlip extends TCPDF
	{
		protected $sr_code;
		protected $added_courses;

		protected $mysqli;
		
		function __construct($mysqli, $sr_code, $added_courses)
		{	
			$custom_layout = array(215.9, 330.2);
			parent::__construct('P', 'mm', $custom_layout, true, 'UTF-8', false);
			$this->SetCreator(PDF_CREATOR);
			$this->SetAuthor('BatStateU');
			$this->SetTitle($sr_code.'_Proposal_Slip');
			$this->SetSubject('Proposal Slip');

			$this->SetMargins(7, 6, 7, true);

			$this->setPrintHeader(false);
			$this->setPrintFooter(false);

			$this->AddPage();

			$this->sr_code 			= $sr_code;
			$this->added_courses 	= $added_courses;
			$this->mysqli 			= $mysqli;

			$this->printPart1();
			$this->printPart2StudentInfo($sr_code);
			$this->printPart3Courses($sr_code, $added_courses);
			$this->printPart4Footer($sr_code);
		}

		function printPart1(){
			$date = date('M d Y');

			$html = '
				<style>
					* {
						font-family: "Times New Roman";
					}
					td {
						border: 1px solid black;
					}
				</style>
				<table>
					<tbody>
						<tr style="font-size: 10px;">
							<td style="line-height: 38px; width: 65px; height: 40px;"></td>
							<td style="line-height: 38px; width: 245px;">Reference No:</td>
							<td style="line-height: 38px; width: 175px;">Effective Date: '.$date.' </td>
							<td style="line-height: 38px; width: 87.3px;">Revision No: 00</td>
						</tr>
					</tbody>
				</table>
				<table>
					<tbody>
						<tr>
							<td style="height: 26px; font-weight: bold; text-align: center; line-height: 25px; font-size: 13px;">
								PROPOSAL SLIP
							</td>
						</tr>
					</tbody>
				</table>
				<table>
					<tbody>
						<tr>
							<td style="height: 20px; border: none; border-bottom: 1px solid black; border-left: 1px solid black; ">
								<input type="checkbox" name="agree" value="1" readonly="true"/> 
								<label style="font-weight: bold; font-size: 10px;">Undergraduate </label>
							</td>
							<td style="border: none; border-bottom: 1px solid black; border-right: 1px solid black;">
								<input type="checkbox" name="agree" value="1" readonly="true"/> 
								<label style="font-weight: bold; font-size: 10px;">Graduate</label>
							</td>
						</tr>
					</tbody>
				</table>
			';

			$this->Image('../img/icon/BSU_icon.png', 12, 7, 12, '', '', 'http://www.tcpdf.org', '', false, 300);

			$this->writeHTML($html, false, false, true, false, '');
		}

		function printPart2StudentInfo($sr_code){
			$sem_sched =  new SemesterSchedule();
			$semester = getSem($sem_sched->getCurrentSemeter());

			$mysqli = $this->mysqli;
			$sql = $mysqli->query("
				SELECT us.SR_Code, CONCAT(u.Firstname, ' ', u.Lastname) AS Name, c.Curriculum_ID, sd.Department_Title, p.Program_Code, p.Program_Title, c.Track, YEAR(Academic_Year) AS 'AcademicYear'
		        FROM `curriculum` c
		            INNER JOIN program p ON c.Program_ID=p.Program_ID
		        	INNER JOIN department sd ON p.Department_ID=sd.Department_ID
		            INNER JOIN user_student us ON c.Curriculum_ID = us.Curriculum_ID 
		            INNER JOIN user u ON us.User_ID = u.User_ID
		        WHERE us.SR_Code = '$sr_code'
			");
			if(!$sql)
				exit();
			while ($obj = $sql -> fetch_object()){
		  		$student = $obj;
		  		break;
		  	}

			$html = '
				<style>
					* {
						font-family: "Times New Roman";
					}
					td {
						border: 1px solid black;
					}
					td.height20 {
						height: 20px; 
						line-height: 20px;
					}
					tr.fz9 {
						font-size: 9px;
					}
					.bg-grey {
						font-weight: bold; 
						background-color: #CCCCCC;	
					}
				</style>
				<table>
					<tbody>
						<tr class="fz9">
							<td class="height20 bg-grey" style="width: 51px; ">Name</td>
							<td class="height20" style="width: 303px;">'.$student->Name.'</td>
							<td class="height20 bg-grey" style="width: 70px;">Semester</td>
							<td class="height20" style="width: 148.3px;">'.$semester.'</td>
						</tr>
						<tr class="fz9">
							<td class="height20 bg-grey" style="width: 51px;">College</td>
							<td class="height20" style="width: 303px;">'.$student->Department_Title.'</td>
							<td class="height20 bg-grey" style="width: 70px;">Academic Year</td>
							<td class="height20" style="width: 148.3px;">'.$student->AcademicYear.'-'.(intval($student->AcademicYear)+1).'</td>
						</tr>
						<tr class="fz9">
							<td class="height20 bg-grey" style="width: 51px;">Program</td>
							<td class="height20" style="width: 303px;">'.$student->Program_Title.'</td>
							<td class="height20 bg-grey" style="width: 70px;">SR Code</td>
							<td class="height20" style="width: 148.3px;">'.$student->SR_Code.'</td>
						</tr>
						<tr class="fz9">
							<td class="height20 bg-grey" style="width: 51px;">Major</td>
							<td class="height20" style="width: 303px;">'.$student->Track.'</td>
							<td class="height20 bg-grey" style="width: 70px;">Section</td>
							<td class="height20" style="width: 148.3px;"></td>
						</tr>
					</tbody>
				</table>
				<table>
					<tbody>
						<tr>
							<td style="height: 20px; border: none; border-bottom: 1px solid black; border-left: 1px solid black; ">
								<input type="checkbox" name="agree" value="1" readonly="true"/> 
								<label style="font-weight: bold; font-size: 10px;">New Student </label>
							</td>
							<td style="border: none; border-bottom: 1px solid black; border-right: 1px solid black;">
								<input type="checkbox" name="agree" value="1" readonly="true"/> 
								<label style="font-weight: bold; font-size: 10px;">Old Student</label>
							</td>
							<td style="height: 20px; border: none; border-bottom: 1px solid black; border-left: 1px solid black; ">
								<input type="checkbox" name="agree" value="1" readonly="true"/> 
								<label style="font-weight: bold; font-size: 10px;">Regular </label>
							</td>
							<td style="border: none; border-bottom: 1px solid black; border-right: 1px solid black;">
								<input type="checkbox" name="agree" value="1" readonly="true"/> 
								<label style="font-weight: bold; font-size: 10px;">Irregular</label>
							</td>
						</tr>
					</tbody>
				</table>
				<table>
					<tbody>
						<tr class="fz9">
							<td class="height20 bg-grey" style="width: 90px;"> Scholarship (if any)</td>
							<td class="height20" style="width: 482.3px;"></td>
						</tr>
					</tbody>
				</table>
			';

			$this->writeHTML($html, false, false, true, false, '');
		}

		function printPart3Courses($sr_code, $added_courses){
			$mysqli = $this->mysqli;
			if (is_array($added_courses))
				$added_courses = implode(", ", $added_courses);
			$sql = $mysqli->query("
				SELECT  c.Course_ID, c.Course_Code, c.Course_Title, c.Units, c.Lecture, c.Laboratory
			    FROM user_student us 
			        INNER JOIN curriculum_courses cc ON us.Curriculum_ID = cc.Curriculum_ID
			        INNER JOIN courses c ON cc.Course_ID = c.Course_ID
			        LEFT JOIN grades g ON us.Student_ID = g.Student_ID AND g.Course_ID = c.Course_ID
			    WHERE us.SR_Code = '$sr_code'
			        AND (g.Grade IS NULL 
			        OR (SELECT MIN(g2.Grade) FROM grades g2 WHERE g2.Course_ID = c.Course_ID AND g2.Student_ID = us.Student_ID GROUP BY g2.Course_ID) > 3)
			        AND c.Course_ID NOT IN 
			            (SELECT pr3.Course_ID 
			             FROM user_student s3 
			                INNER JOIN pre_requisites pr3 
			                LEFT JOIN grades g3 ON s3.Student_ID=g3.Student_ID AND g3.Course_ID=pr3.Pre_Requisite 
			             WHERE pr3.Course_ID=c.Course_ID 
			                AND s3.Student_ID=us.Student_ID 
			                AND (g3.Grade IS NULL OR 
			                    (SELECT MIN(g4.Grade) FROM user_student s4 
			                        INNER JOIN grades g4 ON s4.Student_ID=g4.Student_ID 
			                    WHERE g4.Course_ID=pr3.Pre_Requisite AND s4.Student_ID=us.Student_ID)>3  ) ) 
			        AND c.Course_ID IN ($added_courses)
			    GROUP BY cc.Course_ID
			");
			if(!$sql)
				exit();

			$html = '
				<style>
					* {
						font-family: "Times New Roman";
					}
					td {
						border: 1px solid black;
					}
					td.height20 {
						height: 20px; 
						line-height: 20px;
					}
					tr.fz9 {
						font-size: 9px;
					}
					tr.fz10 {
						font-size: 10px;
						text-align: center;
					}
					.fz10 td {
						height: 18px;
						line-height: 18px;
					}
					.fz9 td {
						height: 18px;
						line-height: 18px;
					}
					.bg-grey {
						font-weight: bold; 
						background-color: #CCCCCC;	
					}
					.bold td{
						font-weight: bold; 
					}
					.center {
						text-align: center;
					}
					.right {
						text-align: right;
					}
				</style>
				<table>
					<tbody>
						<tr class="bg-grey fz10">
							<td style="width: 60px;">Day</td>
							<td style="width: 60px;">Time</td>
							<td style="width: 77px;">Course Code</td>
							<td style="width: 262.3px;">Course Title</td>
							<td style="width: 39px;">Unit(s)</td>
							<td style="width: 37px;">Lec</td>
							<td style="width: 37px;">Lab</td>
						</tr>';

						$row = 0;
						$units = 0;
						$lec = 0;
						$lab = 0;

						while ($courses = $sql -> fetch_object()){
					  		$html .= 
							'<tr class="fz9">
								<td></td>
								<td></td>
								<td class="center">'.$courses->Course_Code.'</td>
								<td>'.$courses->Course_Title.'</td>
								<td class="center">'.$courses->Units.'</td>
								<td class="center">'.$courses->Lecture.'</td>
								<td class="center">'.$courses->Laboratory.'</td>
							</tr>';
							$row++;
							$units += $courses->Units;
							$lec += $courses->Lecture;
							$lab += $courses->Laboratory;
					  	}
					  	for ($count=$row; $count < 8 ; $count++) { 
					  		$html .= 
							'<tr class="fz9">
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>';
					  	}

				  		$html .= 
						'<tr class="fz9 bold">
							<td colspan="4" class="right">Total:</td>
							<td class="center">'.$units.'</td>
							<td class="center">'.$lec.'</td>
							<td class="center">'.$lab.'</td>
						</tr>
					</tbody>
				</table>
			';

			$this->writeHTML($html, false, false, true, false, '');
		}

		function printPart4Footer($sr_code){
			$mysqli = $this->mysqli;
			$sql = $mysqli->query("
				SELECT dh.Name AS Head, dd.Name AS Dean 
				FROM user_student us 
					INNER JOIN curriculum c ON us.Curriculum_ID = c.Curriculum_ID 
				    INNER JOIN program p ON c.Program_ID = p.Program_ID 
				    INNER JOIN department d ON p.Department_ID = d.Department_ID 
				    INNER JOIN department_head dh ON d.DeptHead_ID = dh.Head_ID
				    INNER JOIN department_dean dd ON d.Dean_ID = dd.Dean_ID
		        WHERE us.SR_Code = '$sr_code'
			");
			if(!$sql)
				exit();
			while ($obj = $sql -> fetch_object()){
		  		$profs = $obj;
		  		break;
		  	}

			$html = '
				<style>
					* {
						font-family: "Times New Roman";
					}
					td.height20 {
						height: 20px; 
						line-height: 20px;
					}
					tr.fz9 {
						font-size: 9px;
					}
					.bg-grey {
						font-weight: bold; 
						background-color: #CCCCCC;	
					}
					.center {
						text-align: center;
					}
					.border-bottom-none td{
						border-top: 1px solid black; 
						border-left: 1px solid black; 
						border-right: 1px solid black;
					}
					.border-top-none td{
						border-bottom: 1px solid black; 
						border-left: 1px solid black; 
						border-right: 1px solid black;
						height: 50px; 
					}
					.bold td{
						font-weight: bold; 
					}
					.date {
						font-weight: normal; 
						font-size: 8px;
					}
				</style>
				<table>
					<tbody>
						<tr class="fz9 border-bottom-none">
							<td>Evaluated By:</td>
							<td>Approved By:</td>
						</tr>
						<tr class="fz9 border-top-none bold">
							<td class="center"><br><br>
								_________________________________<br>
								'.$profs->Head.'<br>
								<span class="date">Date</span> 
							</td>
							<td class="center"><br><br>
								_________________________________<br>
								'.$profs->Dean.'<br>
								<span class="date">Date</span> 
							</td>
						</tr>
					</tbody>
				</table>
			';

			$this->writeHTML($html, false, false, true, false, '');
		}
	}
?>