<?php 
	include "../Standard_Functions/ValidationTemplate.php";
	/**
	 * 
	 */
	class StudentGradeValidate extends ValidationTemplate
	{
		protected  $validations = array(
			'sr_code' => array(
				'required' => 	true,
				'minlength' => 	8,
				'maxlength' => 	8,
				'sr_code' => 	true
			),
			'course_id' => array(
				'number' => 	true
			),
			'grade' => array(
				'number' => 	true,
				'min' => 		1,
				'max' => 		6
			)
		);

		protected  $messages = array(
			'sr_code' => array(
				'required' 	=> 	"Please enter the SR-Code",
				'minlength' => 	"SR-Code must be 8 characters",
				'maxlength' => 	"SR-Code must be 8 characters",
				'sr_code' => 	"Invalid SR-Code"
			),
			'course_id' => array(
				'number' => 	"Input Error",
			),
			'grade' => array(
				'number' 	=> 	"Please select a Grade",
				'min' => 		"Invalid Grade",
				'max' => 		"Invalid Grade"
			)
		);
		
		function setValues($sr_code, $course_id, $grade){
			$this->values['sr_code'] 		= $sr_code;
			$this->values['course_id'] 		= $course_id;
			$this->values['grade'] 			= $grade;
		}
	}
?>