<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Fkpbj_model extends MY_Model{
	public $table = 'ms_fkpbj';
	function __construct(){
		parent::__construct();

	}
	function getFppbj($form){
		$query = "	SELECT
						a.*
						FROM ms_fppbj a WHERE a.del=0";

		$query = $this->db->query($query)->result_array();
		$data = array();
		foreach ($query as $key => $value) {
			$data[$value['id']] = $value['nama_pengadaan'];
		}
		return $data;
	}

	function getData($form=array()){
		$query = "	SELECT
						b.nama_pengadaan,
						a.desc,
						a.file,
						a.is_status,
						a.id,
						a.is_approved

						FROM ".$this->table." a

						LEFT JOIN ms_fppbj b ON b.id = a.id_fppbj

					WHERE a.del = 0";
		if($this->input->post('filter')){
			$query .= $this->filter($form, $this->input->post('filter'), true);
		}
		
		return $query;
	}

	// function selectData($id){
	// 	$query = "SELECT 
	// 					b.nama_pengadaan,
	// 					a.desc,
	// 					a.file,
	// 					a.is_status,
	// 					a.id

	// 					FROM ".$this->table." a
	// 					LEFT JOIN ms_fppbj b ON b.id = a.id_fppbj
	
	// 				WHERE a.del = 0 and a.id = ?";
	// 	$query = $this->db->query($query, array($id));
	// 	return $query->row_array();
	// }

	function selectData($id){
		$query = "SELECT 	ms_fkpbj.*, tb_division.name division
						FROM ".$this->table."
						LEFT JOIN tb_division ON tb_division.id = ms_fkpbj.id_division
						WHERE ms_fkpbj.id = ".$id."";
		$query = $this->db->query($query, array($id));
		return $query->row_array();
	}

	function selectDataFKPBJ($id){
		$query = "SELECT 	ms_fkpbj.*, tb_division.name division
						FROM ".$this->table."
						LEFT JOIN tb_division ON tb_division.id = ms_fkpbj.id_division
						WHERE ms_fkpbj.id = ".$id."";
		$query = $this->db->query($query);
		return $query;
	}

	function get_fkpbj($id){
		$query = "SELECT 	ms_fppbj.*, tb_division.name division
						FROM ".$this->table."
						LEFT JOIN tb_division ON tb_division.id = ms_fppbj.id_division
						WHERE ms_fppbj.id = ".$id."";
		$query = $this->db->query($query, array($id));
		return $query->row_array();
	}

	function get_fkpbj_by_id_fppbj($id){
		$query = "SELECT 	ms_fkpbj.*, 
							tb_division.name division,
							b.desc_dokumen desc_dokumen_fppbj,
							b.tipe_pengadaan,
							c.name metode_name
						FROM ".$this->table."
						LEFT JOIN tb_division ON tb_division.id = ms_fkpbj.id_division
						LEFT JOIN ms_fppbj b ON b.id=ms_fkpbj.id_fppbj
						LEFT JOIN tb_proc_method c ON c.id=ms_fkpbj.metode_pengadaan
						WHERE ms_fkpbj.id_fppbj = ".$id."";
		$query = $this->db->query($query);
		return $query->row_array();
	}

	public function insert($id,$save){
		//print_r($save);die;
		$update_fppbj = array(
			'nama_pengadaan'		 => $save['nama_pengadaan'],
			'metode_pengadaan'		 => $save['metode_pengadaan'],
			'pr_lampiran'			 => $save['pr_lampiran'],
			'kak_lampiran' 			 => $save['kak_lampiran'],
			'idr_anggaran' 			 => $save['idr_anggaran'],
			'year_anggaran' 		 => $save['year_anggaran'],
			'hps' 					 => $save['hps'],
			'lingkup_kerja' 		 => $save['lingkup_kerja'],
			'penggolongan_penyedia'  => $save['penggolongan_penyedia'],
			'desc_metode_pembayaran' => $save['desc_metode_pembayaran'],
			'jenis_kontrak' 		 => $save['jenis_kontrak'],
			'sistem_kontrak' 		 => $save['sistem_kontrak'],
			'is_status' 			 => $save['is_status'],
			'is_approved' 			 => $save['is_approved'],
			'edit_stamp' 			 => $save['edit_stamp'],
			'no_pr'					 => $save['no_pr'],
			'desc_dokumen'			 => $save['desc_dokumen'],
			'jwpp_start'			 => $save['jwpp_start'],
			'jwpp_end'			 	 => $save['jwpp_end']
		);

		$this->db->where('id',$id)->update('ms_fppbj',$update_fppbj);

		$this->db->where('id_fppbj', $id)->update('ms_fp3', array('edit_stamp' => date('Y-m-d H:i:s'), 'del' => 1));

		$getDpt = $this->db->where('id_fppbj',$id)->get('tr_analisa_risiko')->row_array();
		if (count($getDpt) > 0) {
			$this->db->where('id_fppbj',$id)->update('tr_analisa_risiko',array('dpt_list'=>$save['dpt'],'edit_stamp'=>date('Y-m-d H:i:s'),));
		} else {
			$this->db->insert('tr_analisa_risiko',array(
				'dpt_list'=>$save['dpt'],
				'id_fppbj'=>$id,
				'entry_stamp'=>date('Y-m-d H:i:s'),
				'del'=>0
			));
		}
		
		return $this->db->insert('ms_fkpbj',$save);
	}

	public function statusApprove($status,$year)
	{
		$admin = $this->session->userdata('admin');

		if ($status == '4') {
			$s = " AND is_reject = 1 ";
		} elseif ($status == '5') {
			$s = ""; 
		} else {
			$s = " AND is_reject = 0 AND is_approved = ".$status;
		}

		if($year != ''){
			$q = ' AND entry_stamp LIKE "%'.$year.'%"';
		} else{
			$q = '';
		}

		$query = " 	SELECT
						*
					FROM
						ms_fkpbj
					WHERE
						del = 0 $s $q";

		if ($admin['id_division'] != 1 && $admin['id_division'] != 5) {
			$query .= " AND id_division = ".$admin['id_division'];
		}

		$query = $this->db->query($query);

		// echo $this->db->last_query();die;

		return $query;
	}

	public function getDataFp3($id_fppbj)
	{
		$query = $this->db->where('del', 0)->where('id_fppbj', $id_fppbj)->get('ms_fp3');
		return $query->row_array();
	}
}
