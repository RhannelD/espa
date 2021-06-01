<?php 
	include "../Standard_Functions/ValidationTemplate.php";
	/**
	 * 
	 */
	class AccountValidate extends ValidationTemplate
	{
		protected  $validations = array(
			'firstname' => array(
				'required' => 	true,
				'minlength' => 	1,
				'maxlength' => 	100,
				'name' => true
			),
			'lastname' => array(
				'required' => 	true,
				'minlength' => 	1,
				'maxlength' => 	100,
				'name' => true
			),
			'gender' => array(
				'gender' => 	true
			),
			'password' => array(
				'required' => 	true,
				'minlength' => 	3,
				'maxlength' => 	200
			)
		);

		protected  $messages = array(
			'firstname' => array(
				'required' 	=> 	"Please enter the Firstname",
				'minlength' => 	"Program Title should be atleast 1 characters",
				'maxlength' => 	"Program Title must be less than or equal to 150 characters",
				'name' 		=>  "Invalid Firstname"
			),
			'lastname' => array(
				'required' 	=> 	"Please enter the Lastname",
				'minlength' => 	"Program Title should be atleast 1 characters",
				'maxlength' => 	"Program Title must be less than or equal to 150 characters",
				'name' 		=>  "Invalid Lastname"
			),
			'gender' => array(
				'gender' => 	"Please select the Student's Gender"
			),
			'password' => array(
				'required' 	=> 	"Please enter a Password",
				'minlength' => 	"Password should be atleast 3 characters",
				'maxlength' => 	"Password must be less than or equal to 200 characters"
			)
		);
		
		function setValues($firstname, $lastname, $gender, $password){
			$this->values['firstname'] 		= $firstname;
			$this->values['lastname'] 		= $lastname;
			$this->values['gender'] 		= $gender;
			$this->values['password'] 		= $password;
		}

		function setAccountInfo($firstname, $lastname, $gender){
			$this->values['firstname'] 		= $firstname;
			$this->values['lastname'] 		= $lastname;
			$this->values['gender'] 		= $gender;
		}
	}
?>