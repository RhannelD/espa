<?php 
	include "../Standard_Functions/ValidationTemplate.php";
	/**
	 * 
	 */
	class StudentValidate extends ValidationTemplate
	{
		protected  $validations = array(
			'sr_code' => array(
				'required' => 	true,
				'minlength' => 	8,
				'maxlength' => 	8,
				'sr_code' => 	true
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
			'program' => array(
				'required' => 	true,
				'number' => 	true
			),
			'gender' => array(
				'gender' => 	true
			),
			'email' => array(
				'required' => 	true,
				'email'	=>		true
			),
			'academic_year' => array(
				'required' => 	true,
				'number' => 	true
			),
			'track' => array(
				'required' => 	true,
				'number' => 	true
			),
			'password' => array(
				'required' => 	true,
				'minlength' => 	3,
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
			'program' => array(
				'required' => 	"Please select a Program",
				'number' => 	"Please select a Program"
			),
			'gender' => array(
				'gender' => 	"Please select the Student's Gender"
			),
			'email' => array(
				'required' => 	"Please enter the Email",
				'email'	=>		"Invalid Email"
			),
			'academic_year' => array(
				'required' => 	"Please select Academic Year",
				'number' => 	"Please select Academic Year"
			),
			'track' => array(
				'required' => 	"Please select a Track",
				'number' => 	"Please select a Track"
			),
			'password' => array(
				'required' 	=> 	"Please enter a Password",
				'minlength' => 	"Password should be atleast 3 characters",
				'maxlength' => 	"Password must be less than or equal to 200 characters"
			)
		);
		
		function setValues($sr_code, $firstname, $lastname, $gender, $email, $program, $year, $track){
			$this->values['sr_code'] 		= $sr_code;
			$this->values['firstname'] 		= $firstname;
			$this->values['lastname'] 		= $lastname;
			$this->values['gender'] 		= $gender;
			$this->values['email'] 			= $email;
			$this->values['program'] 		= $program;
			$this->values['academic_year'] 	= $year;
			$this->values['track'] 			= $track;
		}

		function setStudentInfo($sr_code, $firstname, $lastname, $gender){
			$this->values['sr_code'] 		= $sr_code;
			$this->values['firstname'] 		= $firstname;
			$this->values['lastname'] 		= $lastname;
			$this->values['gender'] 		= $gender;
		}

		function setShiftingInfo($sr_code, $program, $year, $track){
			$this->values['sr_code'] 		= $sr_code;
			$this->values['program'] 		= $program;
			$this->values['academic_year'] 	= $year;
			$this->values['track'] 			= $track;
		}

		function setChangePasswordInfo($sr_code, $password){
			$this->values['sr_code'] 		= $sr_code;
			$this->values['password'] 		= $password;
		}

		function setSRCodeInfo($sr_code){
			$this->values['sr_code'] 		= $sr_code;
		}
	}
?>