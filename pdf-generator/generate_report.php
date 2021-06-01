<?php  
	include '../library-tcpdf/tcpdf.php';
	include "../Standard_Functions/year_sem.php";
	include "../Standard_Functions/grade_converter.php";

	/**
	 * 
	 */
	class Report extends TCPDF
	{
		protected $mysqli;	
		protected $query;
	    protected $dept;
	    protected $report;
		
		function __construct($mysqli, $query, $dept, $report)
		{	
			$custom_layout = array(215.9, 330.2);
			parent::__construct('P', 'mm', $custom_layout, true, 'UTF-8', false);
			$this->SetCreator(PDF_CREATOR);
			$this->SetAuthor('BatStateU');
			$this->SetTitle('Report');
			$this->SetSubject('Report');

			$this->SetMargins(6, 3, 6, true);

			$this->setPrintHeader(false);
			$this->setPrintFooter(false);

			$this->AddPage();

			$this->mysqli 	= $mysqli;
			$this->query 	= $query;
		    $this->dept 	= $dept;
		    $this->report 	= $report;

			$this->printHeader();
			$this->printSubHeader();
			$this->printStudents();
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

			$deptlogo = '../img/icon/Spartan_icon.png';


			$dept = $this->dept;

			if ($dept != 'all') {
				$mysqli = $this->mysqli;
				$sql = $mysqli->query("
					SELECT Logo FROM department WHERE Department_ID = $dept
				");
				if(!$sql)
					exit();
				while ($obj = $sql -> fetch_object()){
			  		$deptlogo = '../img/dept_logo/'.$obj->Logo;
			  		break;
			  	}
			}

			$this->Image('../img/icon/BSU_icon.png', 45, 4, 18, '', '', 'http://www.tcpdf.org', '', false, 300);
			$this->Image($deptlogo, 150, 4, 18, '', '', 'http://www.tcpdf.org', '', false, 300);
		}

		function printSubHeader(){
			$report = 'Possible Honorable Students';
			switch ($this->report) {
				case 'inc':
					$report = 'Students with Incomplete Grade Record';
					break;
				case 'dropped':
					$report = 'Students with Dropped Record';
					break;
				case 'failed':
					$report = 'Students with Failed Grade Record';
					break;
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
					    <td colspan="3"><b>'.$report.'</b></td>
				  	</tr>
				</table>
			';
			$this->writeHTML($html, true, false, true, false, '');
		}

		function printStudents(){
			$mysqli = $this->mysqli;
			$sql = $mysqli->query($this->query);
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
							<td style="width: 60px;">SR-Code</td>
							<td style="width: 100px;">Name</td>
							<td style="width: 77px;">Department</td>
							<td style="width: 60.3px;">Program</td>
							<td style="width: 170px;">Track</td>
							<td style="width: 100px;">Academic Year</td>
						</tr>';

						if ($sql->num_rows == 0) {
					  		$html .= 
							'<tr class="fz9">
								<td colspan="7">None</td>
							</tr>';
						}

						while ($obj = $sql -> fetch_object()){
					  		$html .= 
							'<tr class="fz9">
								<td class="center">'.$obj->SR_Code.'</td>
								<td>'.$obj->Name.'</td>
								<td class="center">'.$obj->Department_Code.'</td>
								<td class="center">'.$obj->Program_Code.'</td>
								<td class="center">'.$obj->Track.'</td>
								<td class="center">'.$obj->Academic_Year.'-'.(intval($obj->Academic_Year)+1).'</td>
							</tr>';
					  	}

				  		$html .= '
					</tbody>
				</table>
			';

			$this->writeHTML($html, false, false, true, false, '');
		}
	}
	
?>