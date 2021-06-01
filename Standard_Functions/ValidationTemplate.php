<?php 

	/**
	 * 
	 */
	class ValidationTemplate
	{
		private $errormessage = '';
		private $errorkey = '';	

		protected  $values = array(			// Values to be validate
		);

		protected  $validations = array(	// Validation Values
		);

		protected  $messages = array( 		// Error Messsages
		);
		
		// Validation
		function validate(){
			$this->errormessage = '';
			foreach ($this->validations as $key => $value) {
				if(!array_key_exists($key, $this->values)){
					continue;
				}

				// For String Inputs
				if(array_key_exists('required', $value)){
					if($this->values[$key]==null || empty($this->values[$key])){
						$this->errormessage = $this->messages[$key]['required'];
						$this->errorkey = $key;
						return true;
					}
				}
				if(array_key_exists('minlength', $value)){
					if(strlen($this->values[$key]) < $value['minlength']){
						$this->errormessage = $this->messages[$key]['minlength'];
						$this->errorkey = $key;
						return true;
					}
				}
				if(array_key_exists('maxlength', $value)){
					if(strlen($this->values[$key]) > $value['maxlength']){
						$this->errormessage = $this->messages[$key]['maxlength'];
						$this->errorkey = $key;
						return true;
					}
				}

				// Name Validation
				if(array_key_exists('name', $value)){
					if(!preg_match("/[a-zA-Z]{1,30}$/", $this->values[$key])){
						$this->errormessage = $this->messages[$key]['name'];
						$this->errorkey = $key;
						return true;
					}
				}

				// SR-Code Validation
				if(array_key_exists('sr_code', $value)){
					if(!preg_match("/^[0-9]{2}[-]{1}[0-9]{5}$/", $this->values[$key])){
						$this->errormessage = $this->messages[$key]['sr_code'];
						$this->errorkey = $key;
						return true;
					}
				}

				// Institutional Email Validation
				if(array_key_exists('email', $value)){
					if(!preg_match("/^[a-zA-Z0-9._%+-]+\@g.batstate-u.edu.ph$/", $this->values[$key])){
						$this->errormessage = $this->messages[$key]['email'];
						$this->errorkey = $key;
						return true;
					}
				}

				// For Number Inputs
				if(array_key_exists('number', $value)){
					if(!is_numeric($this->values[$key])){
						$this->errormessage = $this->messages[$key]['number'];
						$this->errorkey = $key;
						return true;
					}
				}
				if(array_key_exists('min', $value)){
					if($this->values[$key] < $value['min']){
						$this->errormessage = $this->messages[$key]['min'];
						$this->errorkey = $key;
						return true;
					}
				}
				if(array_key_exists('max', $value)){
					if($this->values[$key] > $value['max']){
						$this->errormessage = $this->messages[$key]['max'];
						$this->errorkey = $key;
						return true;
					}
				}

				// For Gender Inputs
				if(array_key_exists('gender', $value)){
					$genders = array('male', 'female');
					if(!in_array($this->values[$key], $genders)){
						$this->errormessage = $this->messages[$key]['gender'];
						$this->errorkey = $key;
						return true;
					}
				}
				
				// For Array (has ducplicated values)
				if(array_key_exists('array', $value)){
					if(count($this->values[$key]) !== count(array_unique($this->values[$key]))){
						$this->errormessage = $this->messages[$key]['array'];
						$this->errorkey = $key;
						return true;
					}
				}

				// For Adding Department Officer
				if(array_key_exists('officer', $value)){
					$officer = array('CHP', 'EVL', 'NAN');
					if(!in_array($this->values[$key], $officer)){
						$this->errormessage = $this->messages[$key]['officer'];
						$this->errorkey = $key;
						return true;
					}
				}
			}
		}

		// Getting the error Message
		function getErrorMessage(){
			return $this->errormessage;
		}

		// Get the Error Key (for js and showing bootstrap input error)
		function getErrorKey(){
			return $this->errorkey;
		}

		// Get validations (for html input purposes)
		function getValidations($name){
			$validations = "";
			$allowed = array('required', 'minlength', 'maxlength', 'min', 'max');

			foreach ($this->validations[$name] as $key => $value) {
				if(!in_array($key, $allowed))
					continue;
				$validations .= " $key='$value' ";
			}
			return $validations;
		}
	}
?>