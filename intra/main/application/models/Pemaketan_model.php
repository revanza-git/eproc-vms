<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pemaketan_model extends MY_Model{
	public $ms_user = 'ms_user';
	public $fppbj = 'ms_fppbj';
	public $eproc_db;
	function __construct(){
		parent::__construct();
		$this->eproc_db = $this->load->database('eproc',true);
	}

	function pejabatPengadaan()
	{
		$query = "SELECT id, name FROM " . $this->ms_user . " where id_role = 9 or id_role = 8 or id_role = 7 or id_role = 2";

		$data = $this->db->query($query)->result_array();

		$result = array();
		foreach($data as $value)
		{
			if ($value['name'] == "Haryo") {
                $value['name'] = "Kepala Procurement";
            }
			$result[$value['id']] = $value['name'];
		}

		return $result;
		
	}
	
	function getData($year){
		
		$admin = $this->session->userdata('admin');

		// Handle empty or null year parameter
		if (empty($year)) {
			$year = date('Y');
		}

		$years = explode(',', $year);

		// Use year_anggaran field with LIKE for flexibility (works for 2022 data)
		if (count($years) == 1) {
			$query_year = "ms_fppbj.year_anggaran LIKE '%".$years[0]."%'";
		} else {
			// For multiple years, check each year
			$year_conditions = array();
			foreach ($years as $y) {
				$year_conditions[] = "ms_fppbj.year_anggaran LIKE '%".$y."%'";
			}
			$query_year = "(" . implode(" OR ", $year_conditions) . ")";
		}

		
		// Superadmin (role 10) can see all data without restrictions
		if ($admin['id_role'] == 10) {
			$get = "WHERE ms_fppbj.del = 0 AND ".$query_year;
		} else if ($admin['id_role'] == 7) {
			$get = 'WHERE ms_fppbj.is_status = 0 AND ms_fppbj.del = 0 AND '.$query_year.' AND 
			        ms_fppbj.is_approved = 3 AND 
			        ms_fppbj.is_reject = 0 AND 
			        ms_fppbj.is_writeoff = 0 AND 
			        ((ms_fppbj.idr_anggaran > 100000000 AND ms_fppbj.idr_anggaran <= 1000000000) AND 
			        (ms_fppbj.metode_pengadaan = 4 OR 
			        ms_fppbj.metode_pengadaan = 2 OR 
			        ms_fppbj.metode_pengadaan = 1))';
		} else if ($admin['id_role'] == 8) {
			$get = 'WHERE ms_fppbj.is_status = 0 AND ms_fppbj.del = 0 AND '.$query_year.' AND 
			        ms_fppbj.is_approved = 3 AND 
			        ms_fppbj.is_reject = 0 AND 
			        ms_fppbj.is_writeoff = 0 AND 
			        (ms_fppbj.idr_anggaran > 1000000000 AND ms_fppbj.idr_anggaran <= 10000000000) AND 
			        (ms_fppbj.metode_pengadaan = 4 OR 
			        ms_fppbj.metode_pengadaan = 2 OR 
			        ms_fppbj.metode_pengadaan = 1)';
		} else if ($admin['id_role'] == 9) {
			$get = 'WHERE ms_fppbj.is_status = 0 AND ms_fppbj.del = 0 AND '.$query_year.' AND 
			        ms_fppbj.is_approved = 3 AND 
			        ms_fppbj.is_reject = 0 AND 
			        ms_fppbj.is_writeoff = 0 AND 
			        ms_fppbj.idr_anggaran >= 10000000000 AND 
			        (ms_fppbj.metode_pengadaan = 4 OR 
			        ms_fppbj.metode_pengadaan = 2 OR 
			        ms_fppbj.metode_pengadaan = 1)';
		} else {
			// Other roles have basic filtering with optional PIC restriction
			$pic = "";
			if ($admin['id_role'] == 6) {
				$pic = " AND ms_fppbj.id_pic = ".$admin['id_user'];
			}
			$get = "WHERE ms_fppbj.del = 0 AND ".$query_year." ".$pic;
		}
		$query = "	SELECT  MAX(name) as name,
							count(*) AS total,
							MAX(ms_fppbj.id) as id,
							tb_division.id id_division
					FROM ".$this->fppbj."
					JOIN tb_division ON ms_fppbj.id_division = tb_division.id 
					 ".$get."";
		if($this->input->post('filter')){
			$query .= $this->filter($form, $this->input->post('filter'), false);
		}
		
		$query .= " GROUP BY id_division ";
		return $query;
	}

	public function getDataByYear($year)
	{
		$yearInt = (int) $year;

		$this->db->select('ms_fppbj.nama_pengadaan AS name, COUNT(*) AS total, ya.year_anggaran AS year, ms_fppbj.id', false);
		$this->db->from($this->fppbj);
		$this->db->join('ms_fppbj_year_anggaran ya', 'ya.id_fppbj = ms_fppbj.id', 'inner');
		$this->db->where('ya.year_anggaran', $yearInt);
		$this->db->where('ms_fppbj.is_reject', 0);
		$this->db->where('ms_fppbj.is_approved_hse <', 2);

		$this->db->group_start();
			$this->db->group_start();
				$this->db->where('ms_fppbj.del', 0);
				$this->db->where('ms_fppbj.is_approved', 3);
				$this->db->group_start();
					$this->db->where('ms_fppbj.idr_anggaran <=', 100000000);
					$this->db->or_group_start();
						$this->db->where('ms_fppbj.idr_anggaran >', 100000000);
						$this->db->where('ms_fppbj.metode_pengadaan', 3);
					$this->db->group_end();
				$this->db->group_end();
			$this->db->group_end();

			$this->db->or_group_start();
				$this->db->where('ms_fppbj.del', 0);
				$this->db->where('ms_fppbj.is_approved', 4);
				$this->db->where('ms_fppbj.idr_anggaran >', 100000000);
			$this->db->group_end();

			$this->db->or_group_start();
				$this->db->where('ms_fppbj.is_status', 2);
				$this->db->where('ms_fppbj.is_approved', 3);
				$this->db->where('ms_fppbj.del', 0);
			$this->db->group_end();

			$this->db->or_group_start();
				$this->db->where('ms_fppbj.is_status', 1);
				$this->db->where('ms_fppbj.is_approved', 3);
				$this->db->where('ms_fppbj.del', 0);
			$this->db->group_end();
		$this->db->group_end();

		$this->db->group_by('ms_fppbj.id');

		return $this->db->get()->result_array();
	}
	
	function getDataRekap($form = "", $year = null)
	{
		if ($year > 0) {
			$query = "	SELECT  b.name AS division,
								ms_fppbj.nama_pengadaan AS name,
								YEAR(ms_fppbj.entry_stamp) as year,
								ms_fppbj.id
						FROM " . $this->fppbj . "
						LEFT JOIN
							tb_division b ON b.id=ms_fppbj.id_division
						LEFT JOIN
							ms_fp3 ON ms_fp3.id_fppbj = ms_fppbj.id
						WHERE
						(ms_fppbj.is_perencanaan = 1 AND ms_fppbj.entry_stamp LIKE '%" . $year . "%' AND ms_fppbj.del = 0 AND ms_fppbj.is_approved = 3 AND (ms_fppbj.idr_anggaran <= 100000000 OR (ms_fppbj.idr_anggaran > 100000000 AND ms_fppbj.metode_pengadaan = 3))
                            OR  
                            (ms_fppbj.is_perencanaan = 1 AND ms_fppbj.entry_stamp LIKE '%" . $year . "%' AND ms_fppbj.del = 0 AND ms_fppbj.is_approved = 4 AND ms_fppbj.idr_anggaran > 100000000))
                            OR
                            (ms_fppbj.is_perencanaan = 1 AND ms_fppbj.entry_stamp LIKE '%" . $year . "%' AND ms_fppbj.is_status = 2 AND ms_fppbj.del = 0)
                            OR
                            (ms_fppbj.is_perencanaan = 1 AND ms_fppbj.entry_stamp LIKE '%" . $year . "%' AND ms_fppbj.is_status = 1 AND ms_fp3.status != 'hapus' AND ms_fppbj.del = 0)";
			if ($this->input->post('filter')) {
				$query .= $this->filter($form, $this->input->post('filter'), true);
			}
			$query .= " GROUP BY id";
		} else {
			$query = "	SELECT  ms_fppbj.nama_pengadaan AS name,
								count(*) AS total,
								YEAR(ms_fppbj.entry_stamp) as year,
								ms_fppbj.id
						FROM " . $this->fppbj . "
						LEFT JOIN
							ms_fp3 ON ms_fp3.id_fppbj = ms_fppbj.id
						WHERE
						(ms_fppbj.is_perencanaan = 1 AND ms_fppbj.entry_stamp LIKE '%" . $year . "%' AND ms_fppbj.del = 0 AND ms_fppbj.is_approved = 3 AND (ms_fppbj.idr_anggaran <= 100000000 OR (ms_fppbj.idr_anggaran > 100000000 AND ms_fppbj.metode_pengadaan = 3))
                            OR  

                            (ms_fppbj.is_perencanaan = 1 AND ms_fppbj.entry_stamp LIKE '%" . $year . "%' AND ms_fppbj.del = 0 AND ms_fppbj.is_approved = 4 AND ms_fppbj.idr_anggaran > 100000000))

                            OR

                            (ms_fppbj.is_perencanaan = 1 AND ms_fppbj.entry_stamp LIKE '%" . $year . "%' AND ms_fppbj.is_status = 2 AND ms_fppbj.del = 0)

                            OR

                            (ms_fppbj.is_perencanaan = 1 AND ms_fppbj.entry_stamp LIKE '%" . $year . "%' AND ms_fppbj.is_status = 1 AND ms_fp3.status != 'hapus' AND ms_fppbj.del = 0)";
			if ($this->input->post('filter')) {
				$query .= $this->filter($form, $this->input->post('filter'), true);
			}
			$query .= " GROUP BY YEAR(ms_fppbj.entry_stamp)";
		}
		return $query;
	}

	function selectData($id){
		$query = "SELECT 	
						a.*,
						a.sistem_kontrak,
						b.name nama_pic
				  FROM ".$this->fppbj." a 
				  LEFT JOIN 
				  	   	ms_user b ON b.id=a.id_pic
				  WHERE a.id = ?";
		$query = $this->db->query($query, array($id));
		return $query->row_array();
	}

	public function get_multi_years($id)
	{
		return $this->db->select('*')->where('id_fppbj',$id)->get('tr_price')->result_array();
	}

	public function get_id_analisa_risiko($id)
	{
		return $this->db->select('*')->where('id_fppbj',$id)->get('tr_analisa_risiko')->row_array();
	}

	function getDataDivision($form=array(), $id_division="",$id_fppbj="0",$year = ""){
		$admin = $this->session->userdata('admin');

		// Handle empty or null year parameter
		if (empty($year)) {
			$year = date('Y');
		}

		// Superadmin (role 10) can see all data without division restrictions
		if ($admin['id_role'] == 10) {
			if ($year != '') {
				$years = explode(',', $year);

				if (count($years) == 1) {
					$year_anggaran = "ms_fppbj.year_anggaran LIKE '%".$years[0]."%' AND";
				} else {
					$year_conditions = array();
					foreach ($years as $y) {
						$year_conditions[] = "ms_fppbj.year_anggaran LIKE '%".$y."%'";
					}
					$year_anggaran = "(" . implode(" OR ", $year_conditions) . ") AND";
				}
			} else {
				$year_anggaran = " "; 
			}
			
			$where_id_division = "ms_fppbj.id_division = " . $id_division . " AND ";
			$where = " $year_anggaran  ms_fppbj.del=0 ";

		} else if (!in_array($admin['id_role'], array(7,8,9))) {

			if ($admin['id_role'] == 6) {
				$pic = " AND ms_fppbj.id_pic = ".$admin['id_user'];
			} else {
				$pic = " ";
			}			

			if ($year != '') {
				$years = explode(',', $year);

				if (count($years) == 1) {
					$year_anggaran = "ms_fppbj.year_anggaran LIKE '%".$years[0]."%' AND";
				} else {
					$year_conditions = array();
					foreach ($years as $y) {
						$year_conditions[] = "ms_fppbj.year_anggaran LIKE '%".$y."%'";
					}
					$year_anggaran = "(" . implode(" OR ", $year_conditions) . ") AND";
				}
			} else {
				$year_anggaran = " "; 
			}
					
			$where_id_division = "ms_fppbj.id_division = " . $id_division . " AND ";

			$where = " $year_anggaran  ms_fppbj.del=0 " . $pic;
		} else if ($admin['id_role'] == 7) {
			// Prepare year filter for role 7
			if ($year != '') {
				$years = explode(',', $year);
				if (count($years) == 1) {
					$year_anggaran = "ms_fppbj.year_anggaran LIKE '%".$years[0]."%' AND";
				} else {
					$year_conditions = array();
					foreach ($years as $y) {
						$year_conditions[] = "ms_fppbj.year_anggaran LIKE '%".$y."%'";
					}
					$year_anggaran = "(" . implode(" OR ", $year_conditions) . ") AND";
				}
			} else {
				$year_anggaran = "";
			}
			$where_id_division = "ms_fppbj.id_division = " . $id_division . " AND ";
			$where = $year_anggaran . ' ms_fppbj.is_status = 0 AND 
			        ms_fppbj.is_approved = 3 AND 
			        ms_fppbj.is_reject = 0 AND 
			        ms_fppbj.is_writeoff = 0 AND 
			        ((ms_fppbj.idr_anggaran > 100000000 AND ms_fppbj.idr_anggaran <= 1000000000) AND 
			        (ms_fppbj.metode_pengadaan = 4 OR 
			        ms_fppbj.metode_pengadaan = 2 OR 
			        ms_fppbj.metode_pengadaan = 1)) AND ms_fppbj.del = 0';
		} else if ($admin['id_role'] == 8) {
			// Prepare year filter for role 8
			if ($year != '') {
				$years = explode(',', $year);
				if (count($years) == 1) {
					$year_anggaran = "ms_fppbj.year_anggaran LIKE '%".$years[0]."%' AND";
				} else {
					$year_conditions = array();
					foreach ($years as $y) {
						$year_conditions[] = "ms_fppbj.year_anggaran LIKE '%".$y."%'";
					}
					$year_anggaran = "(" . implode(" OR ", $year_conditions) . ") AND";
				}
			} else {
				$year_anggaran = "";
			}
			$where_id_division = "ms_fppbj.id_division = " . $id_division . " AND ";
			$where = $year_anggaran . ' ms_fppbj.is_status = 0 AND ms_fppbj.is_approved = 3 AND ms_fppbj.is_reject = 0 AND ms_fppbj.is_writeoff = 0 AND (ms_fppbj.idr_anggaran > 1000000000 AND ms_fppbj.idr_anggaran <= 10000000000) AND (ms_fppbj.metode_pengadaan = 4 OR ms_fppbj.metode_pengadaan = 2 OR ms_fppbj.metode_pengadaan = 1) AND ms_fppbj.del = 0';
		} else if ($admin['id_role'] == 9) {
			// Prepare year filter for role 9
			if ($year != '') {
				$years = explode(',', $year);
				if (count($years) == 1) {
					$year_anggaran = "ms_fppbj.year_anggaran LIKE '%".$years[0]."%' AND";
				} else {
					$year_conditions = array();
					foreach ($years as $y) {
						$year_conditions[] = "ms_fppbj.year_anggaran LIKE '%".$y."%'";
					}
					$year_anggaran = "(" . implode(" OR ", $year_conditions) . ") AND";
				}
			} else {
				$year_anggaran = "";
			}
			$where_id_division = "ms_fppbj.id_division = " . $id_division . " AND ";
			$where = $year_anggaran . ' ms_fppbj.is_status = 0 AND ms_fppbj.is_approved = 3 AND ms_fppbj.is_reject = 0 AND ms_fppbj.is_writeoff = 0 AND ms_fppbj.idr_anggaran >= 10000000000 AND (ms_fppbj.metode_pengadaan = 4 OR ms_fppbj.metode_pengadaan = 2 OR ms_fppbj.metode_pengadaan = 1) AND ms_fppbj.del = 0';
		} 
		if ($id_fppbj == '0' || $id_fppbj == '') {
			$id_fppbj = '';
			if($this->session->userdata('admin')['id_division'] == 5 && $this->session->userdata('admin')['id_role'] != 5){
				$where = " ((ms_fppbj.tipe_pengadaan = 'jasa' AND ms_fppbj.is_approved = 1) OR (ms_fppbj.tipe_pengadaan = 'barang' AND ms_fppbj.is_approved = 0 AND ms_fppbj.id_division = 5) OR (ms_fppbj.del = 0 AND ms_fppbj.id_division = 5))";
			}
		} else {
			$id_fppbj = 'ms_fppbj.id = ' . $id_fppbj . ' AND ';
		}
		$query = "	SELECT  ms_fppbj.nama_pengadaan,
							tb_proc_method.name metode,
							year_anggaran,
							ms_fppbj.is_status,
							ms_fppbj.is_approved,
							ms_fppbj.lampiran_persetujuan,
							ms_fppbj.id_perencanaan_umum,
							ms_fppbj.is_reject,
							ms_fppbj.is_writeoff,
							ms_fppbj.id,
							ms_fppbj.tipe_pengadaan,
							ms_fppbj.metode_pengadaan,
							ms_fppbj.is_approved_hse,
							tr_note.value,
							ms_fppbj.is_planning,
							ms_fppbj.jenis_pengadaan,
							ms_fppbj.idr_anggaran,
							tr_note.type,
							ms_fppbj.del,
							fp3.nama_pengadaan nama_baru,
							ms_fppbj.is_cancelled,
							fp3.desc_batal
					FROM ".$this->fppbj."
					LEFT JOIN tb_proc_method ON ms_fppbj.metode_pengadaan = tb_proc_method.id
					LEFT JOIN tr_note ON tr_note.id_fppbj=ms_fppbj.id AND tr_note.type = 'reject'
					LEFT JOIN ms_fp3 fp3 ON fp3.id_fppbj=ms_fppbj.id
					WHERE " . $where_id_division . " " . $id_fppbj . " " . $where;
		
					if($this->input->post('filter')){
			$query .= $this->filter($form, $this->input->post('filter'), false);
		}

		$query .= " GROUP BY ms_fppbj.id ";
		return $query;
	}

	public function getProc($id){
		$this->db->select('ms_fppbj.id, year_anggaran, tb_proc_method.name metode_pengadaan, ms_fppbj.nama_pengadaan, ms_fppbj.jenis_pengadaan,ms_fppbj.penggolongan_penyedia, ms_fppbj.csms,ms_fppbj.jwpp_start,ms_fppbj.jwpp_end, ms_fppbj.jwp_start,ms_fppbj.jwp_end, ms_fppbj.hps, ms_fppbj.desc_metode_pembayaran, ms_fppbj.jenis_kontrak, ms_fppbj.desc');
		$this->db->where('(ms_fppbj.del IS NULL OR ms_fppbj.del = 0)');
		$this->db->where('ms_fppbj.id', $id);
		$this->db->join('tb_proc_method', 'tb_proc_method.id = metode_pengadaan', 'LEFT');
		$query= $this->db->get('ms_fppbj');
		
		$data = $query->row_array();

		$data['fkpbj_detail'] 	= $this->db->select('ms_fkpbj.id id_fkpbj, file, desc, is_status, ms_fkpbj.entry_stamp,ms_user.name user')->where('ms_fkpbj.del', 0)->where('id_fppbj', $data['id'])->join('ms_user','ms_user.id=ms_fkpbj.id_pic','LEFT')->group_by('ms_fkpbj.id_fppbj')->get('ms_fkpbj')->result_array();
		$data['fp3_detail'] 	= $this->db->select('ms_fp3.id id_fp3, status, nama_pengadaan, tb_proc_method.name metode_pengadaan, jadwal_pengadaan, ms_fp3.desc, ms_fp3.is_status, ms_fp3.entry_stamp')->where('ms_fp3.del', 0)->where('id_fppbj', $data['id'])->join('tb_proc_method', 'tb_proc_method.id = ms_fp3.metode_pengadaan')->get('ms_fp3')->result_array();

		foreach ($data['fkpbj_detail'] as $key => $value)
			$data['fkpbj_detail'][$key]['type']	= "fkpbj";
		
		
		foreach ($data['fp3_detail'] as $key => $value)
			$data['fp3_detail'][$key]['type']	= "fp3";
			

		$data['detail']	= array_merge ($data['fkpbj_detail'], $data['fp3_detail']);

		
		// print_r($data);
		return $data;

	}

	public function get_status($id){
		$query = "	SELECT  nama_pengadaan,
							tb_proc_method.name metode,
							year_anggaran,
							ms_fppbj.is_status,
							ms_fppbj.is_approved,
							ms_fppbj.id_perencanaan_umum,
							ms_fppbj.id,
							ms_fppbj.lampiran_persetujuan
					FROM ".$this->fppbj."
					LEFT JOIN tb_proc_method ON ms_fppbj.metode_pengadaan = tb_proc_method.id
					WHERE 	ms_fppbj.id = ".$id."
					AND		ms_fppbj.del=0 ";

		if($this->input->post('filter')){
			$query .= $this->filter($form, $this->input->post('filter'), true);
		}

		if($this->session->userdata('admin')['id_role']==3){
			$query .= 'AND is_approved >= 1';
		}else if($this->session->userdata('admin')['id_role']==2){
			$query .= 'AND is_approved >= 2';
		}

		// print_r($query);
		return $this->db->query($query)->row_array();
	}





	/*============================================================================

									REKAP SECTION

	==============================================================================*/

	function getDataYear($form = "", $year = null)
	{
		if ($year > 0) {
			$query = "	SELECT  b.name AS division,
								a.nama_pengadaan AS name,
								a.year_anggaran AS year,
								a.id
						FROM " . $this->fppbj . " a
						LEFT JOIN
							tb_division b ON b.id=a.id_division
						WHERE
						a.entry_stamp LIKE '%" . $year . "%' AND a.del = 0";
			if ($this->input->post('filter')) {
				$query .= $this->filter($form, $this->input->post('filter'), true);
			}
			$query .= " GROUP BY a.id";
		} else {
			$query = "	SELECT  nama_pengadaan AS name,
								count(*) AS total,
								year_anggaran AS year,
								ms_fppbj.id
						FROM " . $this->fppbj . "
						WHERE
						entry_stamp LIKE '%" . $year . "%' AND del = 0";
			if ($this->input->post('filter')) {
				$query .= $this->filter($form, $this->input->post('filter'), true);
			}
			$query .= " GROUP BY YEAR(entry_stamp)";
		}
		return $query;
	}

	function getDataYear_($form = "", $year = null)
	{
		if ($year > 0) {
			$query = "	SELECT  tb_division.name AS division,
								a.nama_pengadaan AS name,
								a.year_anggaran AS year,
								a.id
						FROM " . $this->fppbj . " a
						LEFT JOIN tb_division ON tb_division.id = a.id_division
						WHERE  a.is_status = 0 AND
						a.is_reject = 0 
				        AND a.is_approved_hse < 2
						AND 
						(
							(
								a.entry_stamp LIKE '%" . $year . "%' AND 
								a.del = 0 AND 
								a.is_approved = 3 AND 
								(
									a.idr_anggaran <= 100000000 OR 
									(
										a.idr_anggaran > 100000000 AND 
										a.metode_pengadaan = 3
									)
								)
							)
						)
						OR  
						(
							a.entry_stamp LIKE '%" . $year . "%' AND 
							a.del = 0 AND 
							a.is_approved = 4 AND 
							a.idr_anggaran > 100000000
						) 
						OR
                            (a.entry_stamp LIKE '%" . $year . "%' AND a.is_status = 2 AND a.del = 0)
						OR
                            (a.entry_stamp LIKE '%" . $year . "%' AND a.is_status = 1 AND a.del = 0)";
			if ($this->input->post('filter')) {
				$query .= $this->filter($form, $this->input->post('filter'), true);
			}
			$query .= " GROUP BY a.id";
		} else {
			$query = "	SELECT  nama_pengadaan AS name,
								count(*) AS total,
								year_anggaran AS year,
								ms_fppbj.id
						FROM " . $this->fppbj . "
						WHERE 	is_reject = 0 
				        AND is_approved_hse < 2
						AND 
						(
							(
								entry_stamp LIKE '%" . $year . "%' AND 
								del = 0 AND 
								is_approved = 3 AND 
								(
									idr_anggaran <= 100000000 OR 
									(
										idr_anggaran > 100000000 AND 
										metode_pengadaan = 3
									)
								)
							)
						)
						OR  
						(
							entry_stamp LIKE '%" . $year . "%' AND 
							del = 0 AND 
							is_approved = 4 AND 
							idr_anggaran > 100000000
						) 
						OR
                            (entry_stamp LIKE '%" . $year . "%' AND is_status = 2 AND del = 0)
						OR
                            (entry_stamp LIKE '%" . $year . "%' AND is_status = 1 AND del = 0)";
			if ($this->input->post('filter')) {
				$query .= $this->filter($form, $this->input->post('filter'), true);
			}
			$query .= " GROUP BY YEAR(entry_stamp)";
		}
		return $query;
	}

	function approve($data){
		// $data['tipe_pengadaan'] = $data['pengadaan'];
		$data['del'] = 0;
		unset($data['pengadaan']);
		
		$a 		=  $this->db->insert("ms_perencanaan_umum", $data);
		$a_id 	= $this->db->insert_id();

		// $query = " UPDATE ms_fppbj SET id_perencanaan_umum = $a_id WHERE entry_stamp LIKE '%".$data['year']."%' ";
		$query = " UPDATE ms_fppbj SET is_perencanaan = 2 WHERE del = 0 AND DATE(entry_stamp) > '".$data['date_close']."' ";
		$this->db->query($query);

		$out_perencanaan =  " SELECT * FROM ms_fppbj WHERE del = 0 AND DATE(entry_stamp) > '".$data['date_close']."' ";

		$exec = $this->db->query($out_perencanaan)->result_array();

		foreach ($exec as $key => $value) {
			$this->db->where('id_pengadaan',$value['id'])->update('tr_email_blast',array(
				'type' => 2
			));
		}
		// $this->db->where('year_anggaran', $data['year'])->update('ms_fppbj', array('id_perencanaan_umum' => $a_id));

		return $a;
	}

	public function is_close($year)
	{
		$data = $this->db->where('year', $year)->get('ms_perencanaan_umum')->row_array();
		return $data;
	}

	public function get_data_step($id)
	{
		$sql = "SELECT 
					a.*
				FROM 
					ms_fppbj a 
				WHERE a.id = ".$id;
		$query = $this->db->query($sql)->row_array();

		return $query;
	}

	public function get_data_fp3($id)
	{
		$sql = "SELECT 
					a.*
				FROM 
					ms_fp3 a 
				WHERE a.del = 0 AND a.id_fppbj = ".$id;
		$query = $this->db->query($sql)->row_array();
		return $query;
	}

	public function get_data_analisa($id)
	{
		if(empty($id) || !is_numeric($id)){
			return array();
		}
		$sql1 = "SELECT a.* FROM ms_fppbj a WHERE a.id = ".(int)$id;
		$query1 = $this->db->query($sql1)->row_array();
		$sql = "SELECT 
						a.* 
					FROM 
						tr_analisa_risiko_detail a 
					INNER JOIN
						tr_analisa_risiko b ON b.id=a.id_analisa_risiko
					WHERE b.id_fppbj = ".$query1['id'];
		$sql .= " GROUP BY a.id ORDER BY a.id ASC";
		$query = $this->db->query($sql)->result_array();
		return $query;
		 // print_r($query);die;
		
		// echo $table;
	}

	public function upload_lampiran_persetujuan($id, $save)
	{
		return $this->db->where('id',$id)->update('ms_fppbj',$save);
	}

	public function get_pic($id_fppbj){
		// if ($metode == 3) {
		// 	$query = "SELECT id, name FROM ms_user WHERE id_division = 1";
		// } else {
		$fppbj = $this->db->where('id',$id_fppbj)->get('ms_fppbj')->row_array();

		if ($fppbj['metode_pengadaan'] != 3) {
			$id_pic = " AND id != 110 ";
		}

		$query = "SELECT id, name FROM ms_admin WHERE id_role_app2 = 6 AND del = 0 ".$id_pic;
		// }
		
		$query = $this->eproc_db->query($query);
		// $data = array();
		// foreach ($query as $key => $value) {
		// 	$data[$value['id']] = $value['name'];
		// }
		// print_r($data);
		return $query;
	}

	public function selectDataPIC($id_fppbj)
	{
		$f = $this->db->where('del',0)->where('id',$id_fppbj)->get('ms_fppbj')->row_array();

		// print_r($f);die;

		$query = "SELECT id, name FROM ms_admin WHERE id = ?";
		
		$query = $this->eproc_db->query($query,array($f['id_pic']));

		return $query;
	}

	public function get_swakelola($id){
		$query = $this->db->where('id_fppbj',$id)->get('tr_analisa_swakelola');
		return $query->row_array();
	}

	public function update($id, $data){
		
		foreach ($data as $key => $value) {
			foreach ($value as $keys => $values) {
					if($values=='') unset($value[$keys]);
				}
			if(is_array($value)){
				$data[$key] = implode(',',$value);
			}
		}
		$a = $this->db->
				where('id', $id)->
				update('ms_fppbj', $data);
				return $a;
	}

	public function check_perencanaan_umum($year){
		$check =$this->db->where('year', $year)->where('del',0)->get('ms_perencanaan_umum')->result_array();
		$check = count($check);

		return $check;
	}

	
}
