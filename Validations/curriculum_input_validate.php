<?php 
	include "../Standard_Functions/ValidationTemplate.php";
	/**
	 * 
	 */
	class CurriculumValidate extends ValidationTemplate
	{
		protected  $validations = array(
			'department' => array(
				'required' 		=> true,
				'number' 		=> true
			),
			'program' => array(
				'required' 		=> true,
				'number' 		=> true
			),
			'track' => array(
				'maxlength' 	=> 100
			),
			'academic_year' => array(
				'number' 		=> true,
				'min' 			=> 0,
				'max' 			=> 0
			),
			'reference' => array(
				'required' 		=> true,
				'array'			=> true
			)
		);

		protected  $messages = array(
			'department' => array(
				'required' 		=> "Please select a Department",
				'number' 		=> "Please select a Department"
			),
			'program' => array(
				'required' 		=> "Please select a Program",
				'number' 		=> "Please select a Program"
			),
			'track' => array(
				'maxlength' 	=> "Curriculum Track must be less than or equal to 100 characters"
			),
			'academic_year' => array(
				'number' 		=> "Please enter a Year",
				'min' 			=> "Academic Year value must be greater than or equal to ",
				'max' 			=> "Academic Year value must be less than or equal to "
			),
			'reference' => array(
				'required' 		=> "Please enter a Year",
				'array'	=> "References has duclicated inputs"
			)
		);
		
		function setValues($department, $program, $track, $academic_year, $references){
			$this->values['department'] 	= $department;
			$this->values['program'] 		= $program;
			$this->values['track'] 			= $track;
			$this->values['academic_year'] 	= $academic_year;
			$this->values['reference'] 	= $references;
		}

		function setValuesV2($track, $references){
			$this->values['track'] 			= $track;
			$this->values['reference'] 	= $references;
		}

		function __construct() {
			$this->validations['academic_year']['min'] = (date('Y')-20);
			$this->validations['academic_year']['max'] = date('Y');

			$this->messages['academic_year']['min'] = $this->messages['academic_year']['min'] . strval(date('Y')-20);
			$this->messages['academic_year']['max'] = $this->messages['academic_year']['max'] . strval(date('Y'));
		}
	}
?>