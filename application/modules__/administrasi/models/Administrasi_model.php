<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Administrasi_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->field_master = array(
								'id_vendor',
								'type',
								'no',
								'notaris',
								'issue_date',
								'akta_file',
								'authorize_by',
								'authorize_no',
								'authorize_file',
								'authorize_date',
								'entry_stamp',
								'edit_stamp'
							);
	}

	function save_data($data){
		$_param = array();
		$sql = "INSERT INTO ms_akta (
							id_vendor,
							type,
							no,
							notaris,
							issue_date,
							akta_file,
							authorize_by,
							authorize_no,
							authorize_file,
							authorize_date,
							entry_stamp,
							edit_stamp) 
				VALUES (?,?,?,?,?,?,?,?,?,?,?,?) ";
		
		
		foreach($this->field_master as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);
		$id = $this->db->insert_id();
		
		return $id;
	}

	

}