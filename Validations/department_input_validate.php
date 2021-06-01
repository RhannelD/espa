<?php 
	include "../Standard_Functions/ValidationTemplate.php";
	/**
	 * 
	 */
	class DepartmentValidate extends ValidationTemplate
	{
		protected  $validations = array(
			'logo' => array(
				'required' => 	true,
			),
			'code' => array(
				'required' => 	true,
				'minlength' => 	2,
				'maxlength' => 	12
			),
			'title' => array(
				'required' => 	true,
				'minlength' => 	3,
				'maxlength' => 	200
			),
			'dean' => array(
				'required' => 	true
			),
			'dean_name' => array(
				'required' => 	true,
				'minlength' => 	3,
				'maxlength' => 	200
			),
			'dean_gender' => array(
				'gender' => 	true
			),
			'head' => array(
				'required' => 	true
			),
			'head_name' => array(
				'required' => 	true,
				'minlength' => 	3,
				'maxlength' => 	200
			),
			'head_gender' => array(
				'gender' => 	true
			)
		);

		protected  $messages = array(
			'logo' => array(
				'required' => 	"Please select a Department Logo",
			),
			'code' => array(
				'required' 	=> 	"Please enter a Department Code",
				'minlength' => 	"Department Code should be atleast 2 characters",
				'maxlength' => 	"Department Code must be less than or equal to 12 characters"
			),
			'title' => array(
				'required' 	=> 	"Please enter a Department Title",
				'minlength' => 	"Department Title should be atleast 3 characters",
				'maxlength' => 	"Department Title must be less than or equal to 200 characters"
			),
			'dean' => array(
				'required' => 	"Please select a Dept. Dean"
			),
			'dean_name' => array(
				'required' => 	"Please enter the Dept. Dean's Name",
				'minlength' => 	"Dept. Dean's Name should be atleast 3 characters",
				'maxlength' => 	"Dept. Dean's Name must be less than or equal to 200 characters"
			),
			'dean_gender' => array(
				'gender' => 	"Please select the Dept. Dean's Gender"
			),
			'head' => array(
				'required' => 	"Please select a Dept. Head"
			),
			'head_name' => array(
				'required' => 	"Please enter the Dept. Head's Name",
				'minlength' => 	"Dept. Head's Name should be atleast 3 characters",
				'maxlength' => 	"Dept. Head's Name must be less than or equal to 200 characters"
			),
			'head_gender' => array(
				'gender' => 	"Please select the Dept. Head's Gender"
			)
		);
		
		function setValues($code, $title, $dean_name=null, $dean_gender=null, $head_name=null, $head_gender=null, $logo=null){
			if(!is_null($logo)){
				$this->values['logo'] 		= $logo;
			}
			$this->values['code'] 			= $code;
			$this->values['title'] 			= $title;
			if(!is_null($dean_name)){
				$this->values['dean_name'] 	= $dean_name;
				$this->values['dean_gender']= $dean_gender;
			}
			if(!is_null($head_name)){
				$this->values['head_name'] 	= $head_name;
				$this->values['head_gender']= $head_gender;
			}
		}
	}
?>