<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Kurs_model extends MY_Model{
	public $table = 'tb_kurs';
	function __construct(){
		parent::__construct();

	}
	function getData($form=array()){
		$query = "	SELECT  name,
							symbol,
							id
					FROM ".$this->table."";
		if($this->input->post('filter')){
			$query .= $this->filter($form, $this->input->post('filter'), true);
		}
		
		return $query;
	}

	function selectData($id){
		$query = "SELECT 	name,
							symbol
							FROM ".$this->table."
							WHERE id = ".$id;
		$query = $this->db->query($query, array($id));
		return $query->row_array();
	}
}