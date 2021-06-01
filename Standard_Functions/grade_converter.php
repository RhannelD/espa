<?php  
	function convert_grade($num){
		switch ($num) {
			case 4:
				return 'INC';
			case 6:
				return 'Dropped';
		}
		return number_format($num,2);
	}
?>