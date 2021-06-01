<?php 
	include "../Standard_Functions/ValidationTemplate.php";
	/**
	 * 
	 */
	class ProposalSlipValidate extends ValidationTemplate
	{
		protected  $validations = array(
			'sr_code' => array(
				'required' => 	true,
				'minlength' => 	8,
				'maxlength' => 	8,
				'sr_code' => 	true
			),
			'description' => array(
				'maxlength' => 	200
			)
		);

		protected  $messages = array(
			'sr_code' => array(
				'required' 	=> 	"Please enter the SR-Code",
				'minlength' => 	"SR-Code must be 8 characters",
				'maxlength' => 	"SR-Code must be 8 characters",
				'sr_code' => 	"Invalid SR-Code"
			),
			'description' => array(
				'maxlength' => 	"Description must be less than or equal to 200 characters"
			)
		);
		
		function setValues($sr_code, $description){
			$this->values['sr_code'] 		= $sr_code;
			$this->values['description'] 	= $description;
		}
	}
?>