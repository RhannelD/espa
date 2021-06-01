<?php  
	function has_duplicated_values($array) {
	    return count($array) !== count(array_unique($array));
	}
?>