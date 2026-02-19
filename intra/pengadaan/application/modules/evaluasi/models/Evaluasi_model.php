<?php defined('BASEPATH') or exit('No direct script access allowed');

class Evaluasi_model extends CI_Model
{

	function get_pengadaan_list($search = '', $sort = '', $page = '', $per_page = '', $is_page = FALSE, $filter = array())
	{
		$user = $this->session->userdata('user');
		$admin = $this->session->userdata('admin');

		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

		// print_r($admin);
		$this->db->select('*, ms_procurement.id id, ms_procurement.name name, tb_blacklist_limit.value category, ms_vendor.id id_vendor, ms_vendor.name pemenang, "-" as pemenang_kontrak , "-" as user_kontrak , proc_date, ms_procurement.del del, tr_feedback.remark');
		$this->db->group_by('ms_procurement.id');
		$this->db->where('ms_procurement.del', 0);
		$this->db->where('ms_procurement.id_mekanisme!=', 1);
		$this->db->where('ms_contract.id_vendor', $user['id_user']);
		$this->db->join('ms_contract', 'ms_contract.id_procurement=ms_procurement.id', 'LEFT');
		$this->db->join('ms_vendor', 'ms_vendor.id=ms_contract.id_vendor', 'LEFT');
		$this->db->join('tr_assessment', 'tr_assessment.id_vendor=ms_vendor.id AND tr_assessment.id_procurement = ms_procurement.id', 'LEFT');
		$this->db->join('tb_blacklist_limit', 'tb_blacklist_limit.id=tr_assessment.category', 'LEFT');
		$this->db->join('ms_procurement_bsb', 'ms_procurement_bsb.id_proc=ms_procurement.id', 'LEFT');
		$this->db->join('tr_feedback', 'tr_feedback.id_procurement=ms_procurement.id', 'LEFT');


		if ($this->input->get('sort') && $this->input->get('by')) {
			$this->db->order_by($this->input->get('by'), $this->input->get('sort'));
		}
		if ($is_page) {
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page * ($cur_page - 1));
		}

		$a = $this->filter->generate_query($this->db->group_by('ms_procurement.id'), $filter);

		$query = $a->get('ms_procurement');
		// echo $this->db->last_query();die;
		return $query->result_array();
	}

	public function save_feedback($save)
	{
		$a = $this->db->insert('tr_feedback', $save);
		return $a;
	}

	public function get_division($id_division)
	{
		$a = $this->db->where('del', 0)->where('id_division', $id_division)->get('ms_admin');
		return $a->result_array();
	}

	public function get_vendor($id_vendor)
	{
		$a = $this->db->select('ms_vendor.name, tb_legal.name legal')
			->join('ms_vendor_admistrasi', 'ms_vendor_admistrasi.id_vendor=ms_vendor.id', 'LEFT')
			->join('tb_legal', 'tb_legal.id=ms_vendor_admistrasi.id_legal', 'LEFT')
			->where('ms_vendor.id', $id_vendor)
			->get('ms_vendor');
		return $a->row_array();
	}

	public function get_pengadaan($id)
	{
		$a = $this->db->where('id', $id)->get('ms_procurement');
		return $a->row_array();
	}
}
