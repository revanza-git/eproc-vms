<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bast_model extends CI_Model{

	function __construct(){
		parent::__construct();



		$this->pernyataan = array(
							'value',
							'entry_stamp');
	}

	function get_bast_format(){
		$query = $this->db 	->select('*')
							->where('del',0)
							->get('tb_bast_print');
		
		return $query->row_array();
	}

	function edit_bast($data){
		$this->db 	->where('del',0)->update('tb_bast_print',array('del'=>1,'edit_stamp'=>date('Y-m-d H:i:s')));

		$res = $this->db->insert('tb_bast_print',$data);
		return $res;
	}
}