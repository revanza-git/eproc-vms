<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Riwayat_model extends MY_Model {

	public $eproc_db;

	function __construct()
	{
		parent::__construct();

		$this->eproc_db = $this->load->database('eproc',true);
	}

	public function getRiwayatPengadaan($id,$status="")
	{
		if ($status != 'approval') {
			$con = " AND c.is_status = ".$status;
		} else {
			$con = ""; // Initialize con variable for else case
			//$con = " AND (c.status = 'approval' OR c.status = 'reject')";
		}

		$query = "	SELECT
						c.*,
					    b.name approve_by,
					    c.date date_approval,
					    c.desc_reject,
					    c.dpt_list,
					    d.name method_name,
					    c.status,
					    a.tipe_pr pr_type,
						a.tipe_pengadaan pengadaan_type,
						a.jenis_pengadaan j_pengadaan,
						e.name pic_name,
						a.nama_pengadaan nama_tetap,
						c.approved_by
					FROM 
						tr_history_pengadaan c
					LEFT JOIN
						ms_fppbj a ON a.id=c.id_pengadaan
					LEFT JOIN
						ms_user b ON b.id=c.approved_by
					LEFT JOIN
						ms_user e ON e.id=c.id_pic
					LEFT JOIN
						tb_proc_method d ON d.id=a.metode_pengadaan
					WHERE
						c.id_pengadaan = ? AND c.del = 0 $con ORDER BY c.entry_stamp ASC";

		//print_r($query);exit;

		$query = $this->db->query($query,array($id));

		

		$_status = array(
			0 => 'FPPBJ',
			2 => 'FKPBJ',
			1 => 'FP3'
		);
		// print_r($data); // Commented out debug code - undefined variable
		if ($status != 'approval') {
			return $query->result_array();
		} else {
			// foreach ($query->result_array() as $key => $value) {
			// 	$data[$_status[$value['is_status']]][] = $value;
			// }
			// return $data;
			return $query->result_array();
		}
	}

	function riwayatFp3($id_fppbj)
	{
		$query = "	SELECT
						c.status,
						c.nama_pengadaan,
						b.nama_pengadaan nama_lama,
						d.name metode_pengadaan,
                        e.name metode_lama,
                        a.jwpp_start,
                        a.jwpp_end,
                        b.jwpp_start jwpp_start_lama,
                        b.jwpp_end jwpp_end_lama,
                        a.desc,
                        c.entry_stamp

					FROM ms_fp3 a

					LEFT JOIN ms_fppbj b ON b.id = a.id_fppbj						
					LEFT JOIN tr_history_pengadaan c ON c.id_pengadaan=a.id_fppbj
                    LEFT JOIN tb_proc_method d ON d.id=a.metode_pengadaan
                    LEFT JOIN tb_proc_method e ON e.id=b.metode_pengadaan
					WHERE 
						a.del = 0 AND c.id_pengadaan = ? AND c.is_status = 1";

		$query = $this->db->query($query,array($id_fppbj));

		return $query->result_array();
	}

	public function getPengadaanById($id)
	{
		// $query = $this->db->where('id',$id)->where('del',0)->get('ms_fppbj');
		$query = "	SELECT
						a.*,
						b.name divisi_name,
						c.name metode_name
					FROM
						ms_fppbj a
					LEFT JOIN
						tb_division b ON b.id=a.id_division
					LEFT JOIN
						tb_proc_method c ON c.id=a.metode_pengadaan
					WHERE
						a.id = ? ";
		$query = $this->db->query($query,array($id));
		return $query->row_array();
	}

	function getDptList($id_fppbj)
	{
		$query = $this->db->where('id_fppbj', $id_fppbj)->get('tr_analisa_risiko');
		return $query->row_array();
	}

	function getDptById($id)
	{
		$query = $this->eproc_db->where('id',$id)->get('ms_vendor');
		return $query->row_array();
	}

	public function getDataAdmin($id_user)
	{
		$query = $this->eproc_db->where('id',$id_user)->get('ms_admin');
		return $query->row_array();
	}
	
	public function getDataApproval($id)
	{
		$query = "	SELECT 
						a.is_status,
						a.status,
						a.date,
						a.id,
						b.name
					FROM
						tr_history_pengadaan a
					LEFT JOIN
						eproc.ms_admin b ON b.id=a.approved_by
					WHERE
						(a.status = 'approval' OR a.status = 'reject') AND a.del = 0 AND a.id_pengadaan = " . $id;
		return $query;
	}

	public function getDetailDateApproval($id)
	{
		$q = $this->db->where('id', $id)->get('tr_history_pengadaan');
		return $q->row_array();
	}

	public function save_updated_date($id, $save)
	{
		$q = $this->db->where('id', $id)->update('tr_history_pengadaan', array(
			// 'entry_stamp' => $save['date'],
			'date' => $save['date']
		));

		return $q;
	}

}

/* End of file Riwayat_model.php */
/* Location: ./application/models/Riwayat_model.php */