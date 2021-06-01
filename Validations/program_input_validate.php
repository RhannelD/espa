<?php 
	include "../Standard_Functions/ValidationTemplate.php";
	/**
	 * 
	 */
	class ProgramValidate extends ValidationTemplate
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
			'department' => array(
				'required' => 	true,
				'number' => 	true
			)
		);

		protected  $messages = array(
			'code' => array(
				'required' 	=> 	"Please enter a Program Code",
				'minlength' => 	"Program Code should be atleast 3 characters",
				'maxlength' => 	"Program Code must be less than or equal to 20 characters"
			),
			'title' => array(
				'required' 	=> 	"Please enter a Program Title",
				'minlength' => 	"Program Title should be atleast 3 characters",
				'maxlength' => 	"Program Title must be less than or equal to 150 characters"
			),
			'department' => array(
				'required' => 	"Please select a Department",
				'number' => 	"Please select a Department"
			)
		);
		
		function setValues($code, $title, $department){
			$this->values['code'] 			= $code;
			$this->values['title'] 			= $title;
			$this->values['department'] 	= $department;
		}
	}
?>