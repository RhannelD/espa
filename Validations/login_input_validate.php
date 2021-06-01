<?php 
	include "../Standard_Functions/ValidationTemplate.php";
	/**
	 * 
	 */
	class LoginValidate extends ValidationTemplate
	{
		protected  $validations = array(
			'username' => array(
				'required' => 	true,
				'minlength' => 	1,
				'maxlength' => 	200
			),
			'password' => array(
				'required' => 	true,
				'minlength' => 	3,
				'maxlength' => 	200
			)
		);

		protected  $messages = array(
			'username' => array(
				'required' 	=> 	"Please enter a Username",
				'minlength' => 	"Username should be atleast 1 characters",
				'maxlength' => 	"Username must be less than or equal to 200 characters"
			),
			'password' => array(
				'required' 	=> 	"Please enter a Password",
				'minlength' => 	"Password should be atleast 3 characters",
				'maxlength' => 	"Password must be less than or equal to 200 characters"
			)
		);
		
		function setValues($username, $password){
			$this->values['username'] 	= $username;
			$this->values['password'] 	= $password;
		}

		function setPassword($password){
			$this->values['password'] 	= $password;
		}
	}
?>