<?php defined('BASEPATH') OR exit('No direct script access allowed');

class admin_report_model extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	public function join_list(){

	}


	function get_name(){
		$query = $this->db 	->select('ms_vendor.name, ms_situ.address, ms_vendor_admistrasi.vendor_city city')
							->join('ms_situ', 'ms_situ.id_vendor = ms_vendor.id', 'left')
							->join('ms_vendor_admistrasi', 'ms_situ.id_vendor = ms_vendor.id', 'left')
							->group_by('ms_vendor.id')
							->get('ms_vendor');

		return $query->result_array();
	}

	function get_address(){
		$query = $this->db->select('address')->get('ms_situ');

		return $query->result_array();
	}

	function get_city(){
		$query = $this->db->select('vendor_city')->get('ms_vendor_admistrasi');

		return $query->result_array();
	}
}