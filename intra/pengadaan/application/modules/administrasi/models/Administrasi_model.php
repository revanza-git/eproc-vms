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

	public function get_pic($id_vendor)
	{
		$a = $this->db->select('ms_vendor_pic.*, ms_vendor.name as vendor, tb_legal.name legal')
			->where('ms_vendor_pic.id_vendor', $id_vendor)
			->join('ms_vendor', 'ms_vendor.id=ms_vendor_pic.id_vendor', 'LEFT')
			->join('ms_vendor_admistrasi', 'ms_vendor.id=ms_vendor_admistrasi.id_vendor', 'LEFT')
			->join('tb_legal', 'tb_legal.id=ms_vendor_admistrasi.id_legal', 'LEFT')
			->get('ms_vendor_pic');
		return $a->row_array();
	}
}