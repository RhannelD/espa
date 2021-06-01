<?php  

	function getYear($year){
		switch($year){
            case 1:
                return "First Year";
            case 2:
                return "Second Year";
            case 3:
                return "Third Year";
            case 4:
                return "Fourth Year";
            case 5:
                return "Firth Year";
        }
    }

	function getSem($sem){
        switch($sem){
            case 1:
                return "First Semester";
            case 2:
                return "Second Semester";
            case 3:
                return "Summer";
        }
	}

    function getYearSem_forPdf($year, $sem){
        if($year == 3 && $sem == 3)
            return "MIDTERM<br>";
        
        return strtoupper(getYear($year)).'<br>'.getSem($sem);
    }

    function getNumYearSem($num){
        $remainder = $num%10;
        switch ($remainder) {
            case 1:
                return $num.'st';
            case 2:
                return $num.'nd';
            case 3:
                return $num.'rd';
            default:
                return $num.'th';
        }
    }
?>