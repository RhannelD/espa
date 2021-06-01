<?php 
	if (!class_exists('ValidationTemplate')) {
	    include "../Standard_Functions/ValidationTemplate.php";
	}
	
	/**
	 * 
	 */
	class RecoveryAnswerValidate extends ValidationTemplate
	{
		protected  $validations = array(
			'question' => array(
				'required' 		=> true,
				'number' 		=> true
			),
			'answer' => array(
				'required' => 	true,
				'minlength' => 	3,
				'maxlength' => 	100
			)
		);

		protected  $messages = array(
			'question' => array(
				'required' 		=> "Please select a Question",
				'number' 		=> "Please select a Question"
			),
			'answer' => array(
				'required' 	=> 	"Please enter an Answer",
				'minlength' => 	"Answer should be atleast 3 characters",
				'maxlength' => 	"Answer must be less than or equal to 100 characters"
			)
		);
		
		function setValues($question, $answer){
			$this->values['question'] 	= $question;
			$this->values['answer'] 	= $answer;
		}
		
	}

?>