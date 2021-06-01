<?php 
	include "../Standard_Functions/ValidationTemplate.php";
	/**
	 * 
	 */
	class OfficerValidate extends ValidationTemplate
	{
		protected  $validations = array(
			'id' => array(
				'required' => 	true,
				'number' => 	true
			),
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
			'email' => array(
				'required' => 	true,
				'email'	=>		true
			),
			'department' => array(
				'required' => 	true,
				'number' => 	true
			),
			'officer' => array(
				'required' => 	true,
				'officer' => 	true
			),
			'password' => array(
				'required' => 	true,
				'minlength' => 	3,
				'maxlength' => 	200
			)
		);

		protected  $messages = array(
			'id' => array(
				'required' => 	"Invalid ID",
				'number' => 	"Invalid ID"
			),
			'firstname' => array(
				'required' 	=> 	"Please enter the Firstname",
				'minlength' => 	"Firstname should be atleast 1 characters",
				'maxlength' => 	"Firstname must be less than or equal to 150 characters",
				'name' 		=>  "Invalid Firstname"
			),
			'lastname' => array(
				'required' 	=> 	"Please enter the Lastname",
				'minlength' => 	"Lastname should be atleast 1 characters",
				'maxlength' => 	"Lastname must be less than or equal to 150 characters",
				'name' 		=>  "Invalid Lastname"
			),
			'gender' => array(
				'gender' => 	"Please select the Officer's Gender"
			),
			'email' => array(
				'required' => 	"Please enter the Email",
				'email'	=>		"Invalid Email"
			),
			'department' => array(
				'required' => 	"Please select a Department",
				'number' => 	"Please select a Department"
			),
			'officer' => array(
				'required' => 	"Please select a Offcier Type",
				'officer' => 	"Please select a Offcier Type"
			),
			'password' => array(
				'required' 	=> 	"Please enter a Password",
				'minlength' => 	"Password should be atleast 3 characters",
				'maxlength' => 	"Password must be less than or equal to 200 characters"
			)
		);
		
		function setValues($firstname, $lastname, $gender, $email, $department, $officer, $password){
			$this->values['firstname'] 		= $firstname;
			$this->values['lastname'] 		= $lastname;
			$this->values['gender'] 		= $gender;
			$this->values['email'] 			= $email;
			$this->values['department'] 	= $department;
			$this->values['officer'] 		= $officer;
			$this->values['password'] 		= $password;
		}

		function setOfficerInfo($id, $firstname, $lastname, $gender){
			$this->values['id'] 			= $id;
			$this->values['firstname'] 		= $firstname;
			$this->values['lastname'] 		= $lastname;
			$this->values['gender'] 		= $gender;
		}

		function setOfficerPosition($id, $department, $officer){
			$this->values['id'] 			= $id;
			$this->values['department'] 	= $department;
			$this->values['officer'] 		= $officer;
		}

		function setChangePasswordInfo($id, $password){
			$this->values['id'] 			= $id;
			$this->values['password'] 		= $password;
		}
	}
?>