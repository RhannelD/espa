<?php  
	include "../database/dbconnection.php";
	include "proposal_slip.php";

	if(empty($_REQUEST['added_courses'])&&empty($_REQUEST['sr_code'])) {
		exit();
    }

    $pdf = new ProposalSlip($mysqli, $_REQUEST['sr_code'], $_REQUEST['added_courses']);
    $pdf->Output($_REQUEST['sr_code'].'-ProposalSlip.pdf', 'I');
?>