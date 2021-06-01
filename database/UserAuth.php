<?php  
	/**
	 * 
	 */
	class UserAuth
	{
		private $UserID = null;
		private $UserName = null;
		private $UserType = null;
		
		function __construct($UserID, $UserName, $UserType)
		{
			$this->set_UserID($UserID);
			$this->set_UserName($UserName);
			$this->set_UserType($UserType);
		}

		function set_UserID($UserID){
			$this->UserID = $UserID;
		}

		function set_UserName($UserName){
			$this->UserName = $UserName;
		}

		function set_UserType($UserType){
			$this->UserType = $UserType;
		}

		function get_UserID(){
			return $this->UserID;
		}

		function get_UserName(){
			return $this->UserName;
		}

		function get_UserType(){
			return $this->UserType;
		}
	}
?>