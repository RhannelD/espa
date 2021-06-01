<?php 
	include "../Standard_Functions/ValidationTemplate.php";
	/**
	 * 
	 */
	class RequestValidate extends ValidationTemplate
	{
		protected  $validations = array(
			'message' => array(
				'maxlength' => 	600
			)
		);

		protected  $messages = array(
			'message' => array(
				'maxlength' =>  "Message must be less than or equal to 150 characters"
			)
		);
		
		function setValues($message){
			$this->values['message'] 	= $message;
		}
	}
?>