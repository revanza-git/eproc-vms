<?php defined('BASEPATH') OR exit('No direct script access allowed');

class pernyataan_model extends CI_Model{

	function __construct(){
		parent::__construct();



		$this->pernyataan = array(
							'value',
							'entry_stamp');
	}

	function get_pernyataan_list(){
		$query = $this->db 	->select('*')
							->get('tb_pernyataan');

		return $query->result_array();
	}

	function edit_pernyataan($id, $data){
		$param = array();
		$this->db->where('id',$id);
		$res = $this->db->update('tb_pernyataan',$data);
		// echo print_r($data);
		return $res;
	}

	function save_pernyataan($data){
   		
   		$_param = array();
		$sql = "INSERT INTO tb_pernyataan (
								`value`,
								`entry_stamp`
								) 
				VALUES (?,?) ";

		foreach($this->pernyataan as $_param) $param[$_param] = $data[$_param];

		$this->db->query($sql, $param);
		$id = $this->db->insert_id();
		
		return $id;
   	}
}