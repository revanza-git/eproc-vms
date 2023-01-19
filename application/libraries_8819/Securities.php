<?php
class Securities{
	
	public function __construct(){
		$this->CI =& get_instance(); 
	}

	function clean_input($data = array(), $action = ''){
		$count = count($data);
		$return = array(); 
		
		foreach($data as $key => $value){
			$return[$key] = strip_tags($value);
		}
			

		return $return;
	}
}