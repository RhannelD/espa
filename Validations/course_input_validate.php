<?php 
	include "../Standard_Functions/ValidationTemplate.php";
	/**
	 * 
	 */
	class CourseValidate extends ValidationTemplate
	{
		protected  $validations = array(
			'code' => array(
				'required' => 	true,
				'minlength' => 	3,
				'maxlength' => 	20
			),
			'title' => array(
				'required' => 	true,
				'minlength' => 	3,
				'maxlength' => 	150
			),
			'req_standing' => array(
				'maxlength' => 	60
			),
			'units' => array(
				'number' => 	true,
				'min' => 		1,
				'max' => 		100
			),
			'lecture' => array(
				'number' => 	true,
				'min' => 		0,
				'max' => 		100
			),
			'laboratory' => array(
				'number' => 	true,
				'min' => 		0,
				'max' => 		1000
			)
		);

		protected  $messages = array(
			'code' => array(
				'required' 	=> 	"Please enter a Course Code",
				'minlength' => 	"Course Code should be atleast 3 characters",
				'maxlength' => 	"Course Code must be less than or equal to 20 characters"
			),
			'title' => array(
				'required' 	=> 	"Please enter a Course Title",
				'minlength' => 	"Course Title should be atleast 3 characters",
				'maxlength' => 	"Course Title must be less than or equal to 150 characters"
			),
			'req_standing' => array(
				'maxlength' => 	"Course Req-Standing must be less than or equal to 60 characters"
			),
			'units' => array(
				'number' 	=> 	"Please enter a Course Units",
				'min' 		=> 	"Course Units value must be greater than or equal to 1",
				'max' 		=> 	"Course Units value must be less than or equal to 100"
			),
			'lecture' => array(
				'number' 	=> 	"Please enter a Course Lecture",
				'min' 		=> 	"Course Lecture value must be greater than or equal to 0",
				'max' 		=> 	"Course Lecture value must be less than or equal to 100"
			),
			'laboratory' => array(
				'number' 	=> 	"Please enter a Course Laboratory",
				'min' 		=> 	"Course Laboratory value must be greater than or equal to 0",
				'max' 		=> 	"Course Laboratory value must be less than or equal to 1000"
			)
		);
		
		function setValues($code, $title, $units, $lecture, $laboratory, $req_standing){
			$this->values['code'] 			= $code;
			$this->values['title'] 			= $title;
			$this->values['units'] 			= $units;
			$this->values['lecture'] 		= $lecture;
			$this->values['laboratory'] 	= $laboratory;
			$this->values['req_standing'] 	= $req_standing;
		}
		
	}

?>