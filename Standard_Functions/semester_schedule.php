<?php 
	
	/**
	 * 
	 */
	class SemesterSchedule
	{
		public $FirstSem	= "Aug 01";
		public $SecondSem	= "Jan 01";
		public $Summer		= "Jun 01";

		function __construct()
		{
			
		}

		function getCurrentSemeter(){
			$FirstSem		= strtotime("Aug 01");
			$Summer			= strtotime("Jun 01");

			$currentDate 	= strtotime(date('M d'));
			
			if ($FirstSem <= $currentDate) {
				return 1;
			}
			if ($Summer <= $currentDate) {
				return 3;
			}
			return 2;
		}

		function getCurrentSchoolYearAndSem(){
			$FirstSem		= strtotime($this->FirstSem);

			$currentDate 	= strtotime(date('M d'));
			$currentYear 	= date('Y');

			$year_sem		= [];

			if ($FirstSem <= $currentDate) {
				$year_sem['year']	= $currentYear;
				$year_sem['sem']	= 1;
				return $year_sem;
			}

			$year_sem['year']	= $currentYear-1;
			$year_sem['sem']	= 2;
			return $year_sem;
		}

		function getCurrentSchoolYear(){
			$FirstSem		= strtotime($this->FirstSem);

			$currentDate 	= strtotime(date('M d'));
			$currentYear 	= date('Y');

			if ($FirstSem <= $currentDate) {
				return $currentYear;
			}

			return $currentYear-1;
		}

		function getCurrentSchoolYearAndTriSem(){
			$FirstSem		= strtotime($this->FirstSem);
			$SummerSem		= strtotime($this->Summer);

			$currentDate 	= strtotime(date('M d'));
			$currentYear 	= date('Y');

			$year_sem		= [];

			if ($FirstSem <= $currentDate) {
				$year_sem['year']	= $currentYear;
				$year_sem['sem']	= 1;
				return $year_sem;
			}
			if ($SummerSem <= $currentDate) {
				$year_sem['year']	= $currentYear-1;
				$year_sem['sem']	= 3;
				return $year_sem;
			}

			$year_sem['year']	= $currentYear-1;
			$year_sem['sem']	= 2;
			return $year_sem;
		}
	}
?>