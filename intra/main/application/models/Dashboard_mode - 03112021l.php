<?php defined('BASEPATH') or exit('No direct script access allowed');
class Dashboard_model extends MY_Model
{

	function __construct()
	{
		parent::__construct();
	}
	function total_rencana_baseline($form)
	{
		$admin = $this->session->userdata('admin');
		$query = "SELECT COUNT(*) ct FROM ms_baseline WHERE del = 0 AND status = 1";
		if ($admin['id_role'] == 2) {
			$query .= " AND id_pengguna = " . $admin['id_division'];
		}
		$query = $this->db->query($query)->row_array();
		return $query['ct'];
	}
	function total_realisasi_baseline($form)
	{
		$admin = $this->session->userdata('admin');
		$query = "SELECT COUNT(*) ct FROM ms_procurement a JOIN ms_baseline b ON a.id_baseline = b.id WHERE a.del = 0";
		if ($admin['id_role'] == 2) {
			$query .= " AND a.id_pengguna = " . $admin['id_division'];
		}
		$query = $this->db->query($query)->row_array();
		return $query['ct'];
	}
	function total_realisasi_non_baseline($form)
	{
		$admin = $this->session->userdata('admin');
		$query = "SELECT COUNT(*) ct FROM ms_procurement a WHERE a.id_baseline IS NULL AND a.del = 0";
		if ($admin['id_role'] == 2) {
			$query .= " AND a.id_pengguna = " . $admin['id_division'];
		}
		$query = $this->db->query($query)->row_array();
		return $query['ct'];
	}
	function total_nilai_baseline($form)
	{
		$admin = $this->session->userdata('admin');
		$query = "SELECT (SUM(idr_budget_investasi) + SUM(idr_budget_operasi)) ct FROM ms_baseline a WHERE del = 0 AND status = 1";
		if ($admin['id_role'] == 2) {
			$query .= " AND a.id_pengguna = " . $admin['id_division'];
		}
		$query = $this->db->query($query)->row_array();

		return $query['ct'];
	}

	function total_nilai_terkontrak_baseline($form)
	{
		$admin = $this->session->userdata('admin');
		$query = "SELECT SUM(c.contract_price) ct FROM ms_procurement a JOIN ms_baseline b ON a.id_baseline = b.id JOIN ms_contract c ON a.id = c.id_procurement WHERE a.del = 0";
		if ($admin['id_role'] == 2) {
			$query .= " AND a.id_pengguna = " . $admin['id_division'];
		}
		$query = $this->db->query($query)->row_array();

		return $query['ct'];
	}

	function total_nilai_terkontrak_non_baseline($form)
	{
		$admin = $this->session->userdata('admin');
		$ct = 0;
		$query = "SELECT SUM(c.contract_price) ct FROM ms_procurement a JOIN ms_contract c ON a.id = c.id_procurement WHERE a.del = 0 AND a.id_baseline IS NULL";
		if ($admin['id_role'] == 2) {
			$query .= " AND a.id_pengguna = " . $admin['id_division'];
		}
		$query = $this->db->query($query)->row_array();
		if ($query['ct'] != null) {
			$ct = $query['ct'];
		}

		return $ct;
	}
	function total_nilai_terbayar_baseline($form)
	{
		$admin = $this->session->userdata('admin');
		$ct = 0;
		$query = "SELECT SUM(a.value) ct FROM ms_invoice a JOIN ms_procurement b ON a.id_procurement = b.id  WHERE a.del = 0 AND id_baseline IS NOT NULL";
		if ($admin['id_role'] == 2) {
			$query .= " AND a.id_pengguna = " . $admin['id_division'];
		}
		$query = $this->db->query($query)->row_array();
		if ($query['ct'] != null) {
			$ct = $query['ct'];
		}

		return $ct;
	}
	function total_nilai_terbayar_non_baseline($form)
	{
		$admin = $this->session->userdata('admin');
		$ct = 0;
		$query = "SELECT SUM(a.value) ct FROM ms_invoice a JOIN ms_procurement b ON a.id_procurement = b.id  WHERE a.del = 0 AND id_baseline IS NULL";
		if ($admin['id_role'] == 2) {
			$query .= " AND a.id_pengguna = " . $admin['id_division'];
		}
		$query = $this->db->query($query)->row_array();
		if ($query['ct'] != null) {
			$ct = $query['ct'];
		}

		return $ct;
	}

	function rekapPerencanaanGraph($year)
	{
		$this->load->model('Export_test_model', 'ext');

		$fppbj_baru = count($this->rekap_department($year, 2));
		$fkpbj_baru = count($this->rekap_department_fkpbj($year, 2));
		$fp3_baru = count($this->rekap_department_fp3($year, 2));

		$a = $this->ext->rekap_department($year);

		foreach ($a as $key => $value) {
			$data_fppbj 	 = $this->ext->rekap_department_fppbj($year, $value['id_division'], '1');
			$data_fppbj_baru = $this->ext->rekap_department_fppbj($year, $value['id_division']);
			$data_fkpbj_baru = $this->ext->rekap_department_fkpbj_baru($year, $value['id_division']);
			$data_fp3_baru = $this->ext->rekap_department_fp3_by_type($year, $value['id_division']);
			$data_fp3_lama = $this->ext->rekap_department_fp3_by_type($year, $value['id_division'], 1);

			$metodes = [
				1 => 'Pelelangan',
				2 => 'Pemilihan Langsung',
				3 => 'Swakelola',
				4 => 'Penunjukan Langsung',
				5 => 'Pengadaan Langsung'
			];

			foreach ($metodes as $key_metode => $metode) {
				$data_fkpbj = $this->ext->count_rekap_department_fkpbj($year, $value['id_division'], $metode);

				$fppbj 	 = $data_fppbj[0]['metode_' . $key_metode];
				$fppbj_baru = $data_fppbj_baru[0]['metode_' . $key_metode];
				$fkpbj 	 = $data_fkpbj[0]['metode_' . $key_metode];
				$fkpbj_baru = $data_fkpbj_baru[0]['metode_' . $key_metode];
				$fp3_baru = $data_fp3_baru[0]['metode_' . $key_metode];
				$fp3_lama = $data_fp3_lama[0]['metode_' . $key_metode];

				${'fppbj_baru_' . (str_replace(" ", _, strtolower($metode)))} += $fppbj_baru;
				${'fppbj_' . (str_replace(" ", _, strtolower($metode)))} += $fppbj;
				${'fkpbj_' . (str_replace(" ", _, strtolower($metode)))} += $fkpbj;
				${'fkpbj_baru_' . (str_replace(" ", _, strtolower($metode)))} += $fkpbj_baru;
				${'fp3_baru_' . (str_replace(" ", _, strtolower($metode)))} += $fp3_baru;
				${'fp3_lama_' . (str_replace(" ", _, strtolower($metode)))} += $fp3_lama;
			}
		}

		$total_fppbj_pelelangan = $fppbj_pelelangan + $fppbj_baru_pelelangan;
		$total_fppbj_pemilihan_langsung = $fppbj_pemilihan_langsung + $fppbj_baru_pemilihan_langsung;
		$total_fppbj_swakelola = $fppbj_swakelola + $fppbj_baru_swakelola;
		$total_fppbj_penunjukan_langsung = $fppbj_penunjukan_langsung + $fppbj_baru_penunjukan_langsung;
		$total_fppbj_pengadaan_langsung = $fppbj_pengadaan_langsung + $fppbj_baru_pengadaan_langsung;

		$total_fkpbj_pelelangan = $fkpbj_pelelangan + $fkpbj_baru_pelelangan;
		$total_fkpbj_pemilihan_langsung = $fkpbj_pemilihan_langsung + $fkpbj_baru_pemilihan_langsung;
		$total_fkpbj_swakelola = $fkpbj_swakelola + $fkpbj_baru_swakelola;
		$total_fkpbj_penunjukan_langsung = $fkpbj_penunjukan_langsung + $fkpbj_baru_penunjukan_langsung;
		$total_fkpbj_pengadaan_langsung = $fkpbj_pengadaan_langsung + $fkpbj_baru_pengadaan_langsung;

		$total_fp3_pelelangan = $fp3_lama_pelelangan + $fp3_baru_pelelangan;
		$total_fp3_pemilihan_langsung = $fp3_lama_pemilihan_langsung + $fp3_baru_pemilihan_langsung;
		$total_fp3_swakelola = $fp3_lama_swakelola + $fp3_baru_swakelola;
		$total_fp3_penunjukan_langsung = $fp3_lama_penunjukan_langsung + $fp3_baru_penunjukan_langsung;
		$total_fp3_pengadaan_langsung = $fp3_lama_pengadaan_langsung + $fp3_baru_pengadaan_langsung;

		// echo $fppbj_pelelangan . $fppbj_pemilihan_langsung . $fppbj_swakelola . $fppbj_penunjukan_langsung . $fppbj_pengadaan_langsung;
		// die;
		// echo count($this->rekap_department($year)) . "-" . count($this->rekap_department_fkpbj($year)) . " - " . count($this->rekap_department_fp3($year));
		// die;
		// echo $fppbj_baru_pemilihan_langsung . '-' . $this->getTotalMethodActualFKPBJ(2, $year) . '-' . $this->getTotalMethodActualFP3(2, $year);;
		// die;
		$data['plan']   				= count($this->rekap_department($year)) + count($this->rekap_department_fkpbj($year)) + count($this->rekap_department_fp3($year));
		$data['act']    				= count($this->rekap_department_fkpbj($year)) + count($this->rekap_department_fp3($year));
		$data['act_out']				= count($this->rekap_department($year, 2)) + count($this->rekap_department_fkpbj($year, 2)) + count($this->rekap_department_fp3($year, 2)); //+ count($this->rekap_department($year,2))
		$data['pelelangan'] 			= $fppbj_baru_pelelangan + $this->getTotalMethodActualFKPBJ(1, $year) + $this->getTotalMethodActualFP3(1, $year); //$total_fppbj_pelelangan + $total_fkpbj_pelelangan + $total_fp3_pelelangan;
		$data['pemilihan_langsung'] 	= $fppbj_baru_pemilihan_langsung + $this->getTotalMethodActualFKPBJ(2, $year) + $this->getTotalMethodActualFP3(2, $year); //$total_fppbj_pemilihan_langsung + $total_fkpbj_pemilihan_langsung + $total_fp3_pemilihan_langsung;
		$data['swakelola'] 				= $fppbj_baru_swakelola + $this->getTotalMethodActualFKPBJ(3, $year) + $this->getTotalMethodActualFP3(3, $year); //$total_fppbj_swakelola + $total_fkpbj_swakelola + $total_fp3_swakelola;
		$data['penunjukan_langsung'] 	= $fppbj_baru_penunjukan_langsung + $this->getTotalMethodActualFKPBJ(4, $year) + $this->getTotalMethodActualFP3(4, $year); //$total_fppbj_penunjukan_langsung + $total_fkpbj_penunjukan_langsung + $total_fp3_penunjukan_langsung;
		$data['pengadaan_langsung'] 	= $fppbj_baru_pengadaan_langsung + $this->getTotalMethodActualFKPBJ(5, $year) + $this->getTotalMethodActualFP3(5, $year); //$total_fppbj_pengadaan_langsung + $total_fkpbj_pengadaan_langsung + $total_fp3_pengadaan_langsung;
		$data['percent_act'] = round(($data['act'] / $data['plan']) * 100);
		$data['percent_act_out'] = round(($data['act_out'] / $data['plan']) * 100);
		// $data['total']  = count($this->db->select('id')->where('year_anggaran', $year)->where('del', 0)->where('is_reject', 0)->get('ms_fppbj')->result_array());
		// $data['plan']   = count($this->db->select('id')->where('year_anggaran', $year)->where('del', 0)->where('is_status < 2')->where('is_reject', 0)->get('ms_fppbj')->result_array());
		// $data['act']    = count($this->db->select('id')->where('year_anggaran', $year)->where('del', 0)->where('is_status', 2)->where('is_reject', 0)->get('ms_fppbj')->result_array());

		// $data['plan']   = $data['plan'] / $data['total'] * 100;
		// $data['act']    = $data['act'] / $data['total'] * 100;

		return $data;
	}

	public function getTotalMethodPlan($method, $year)
	{
		$id_division = $this->session->userdata('admin')['id_division'];
		if ($id_division != 1 && $id_division != 5) {
			$divisi = "id_division = " . $id_division . " AND ";
		} else {
			$divisi = '';
		}
		$sql = "SELECT
						*
				  FROM
				  		ms_fppbj
				   WHERE 
				  		is_status = 0 AND 
				        is_reject = 0 
				        AND del = 0
				        AND is_approved_hse < 2
						AND 
						(
							(
								" . $divisi . " 
								metode_pengadaan = " . $method . " AND 
								del = 0 AND 
								entry_stamp LIKE '%" . $year . "%' AND 
								is_approved = 3 AND 
								(
									idr_anggaran <= 100000000 OR 
									(
										idr_anggaran > 100000000 AND metode_pengadaan = 3
									)
								)
							)
						)
						OR  
						(
							" . $divisi . " 
							metode_pengadaan = " . $method . " AND 
							del = 0 AND 
							entry_stamp LIKE '%" . $year . "%' AND 
							is_approved = 4 AND 
							idr_anggaran > 100000000
						) ";
		$query = $this->db->query($sql);
		// print_r($query);die;
		// echo $this->db->last_query();die;
		return count($query->result_array());
	}

	function getTotalMethodActualFKPBJ($method, $year)
	{
		$id_division = $this->session->userdata('admin')['id_division'];
		if ($id_division != 1 && $id_division != 5) {
			$divisi = "id_division = " . $id_division . " AND ";
		} else {
			$divisi = '';
		}
		$sql = "SELECT 
				    *
				FROM
				    ms_fppbj
				WHERE
						$divisi
						-- is_perencanaan = 1 AND
						metode_pengadaan = $method AND
				    	is_status = 2
						AND is_approved = 3
				        AND del = 0
						AND entry_stamp LIKE '%" . $year . "%'";
		$query = $this->db->query($sql);
		// print_r($query);die;
		// echo $this->db->last_query();die;
		return count($query->result_array());
	}

	function getTotalMethodActualFP3($method, $year)
	{
		$id_division = $this->session->userdata('admin')['id_division'];
		if ($id_division != 1 && $id_division != 5) {
			$divisi = "id_division = " . $id_division . " AND ";
		} else {
			$divisi = '';
		}
		$sql = "SELECT 
				    *
				FROM
				    ms_fppbj
				WHERE
					(
						$divisi
						is_status = 1 AND 
						entry_stamp LIKE '%" . $year . "%' AND metode_pengadaan = $method AND 
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
					OR
					(
						$divisi
						is_status = 1 AND 
						entry_stamp LIKE '%" . $year . "%' AND metode_pengadaan = $method AND 
						del = 0 AND 
						is_approved = 4 AND 
						idr_anggaran > 100000000
					)";
		$query = $this->db->query($sql);
		// print_r($query);die;
		// echo $this->db->last_query();die;
		return count($query->result_array());
	}

	function rekap_department($year = null, $type = 1)
	{
		$id_division = $this->session->userdata('admin')['id_division'];
		if ($id_division != 1 && $id_division != 5) {
			$divisi = "id_division = " . $id_division . " AND ";
		} else {
			$divisi = '';
		}
		$sql = "SELECT
						*
				  FROM
				  		ms_fppbj
				   WHERE 
				        is_reject = 0 
				        AND del = 0
				        AND is_approved_hse < 2
						AND 
						(
							(
								is_status = 0 AND 
								is_perencanaan = " . $type . " AND 
								" . $divisi . " 
								del = 0 AND 
								entry_stamp LIKE'%" . $year . "%' AND 
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
							is_status = 0 AND 
							is_perencanaan = " . $type . " AND 
							" . $divisi . " 
							del = 0 AND 
							entry_stamp LIKE'%" . $year . "%' AND 
							is_approved = 4 AND 
							idr_anggaran > 100000000
						) ";
		$query = $this->db->query($sql);
		// print_r($query);die;
		// echo $this->db->last_query();die;
		return $query->result_array();
	}

	function rekap_department_fkpbj($year = null, $type = 1)
	{
		$id_division = $this->session->userdata('admin')['id_division'];
		if ($id_division != 1 && $id_division != 5) {
			$divisi = "id_division = " . $id_division . " AND ";
		} else {
			$divisi = '';
		}
		$sql = "SELECT 
				    *
				FROM
				    ms_fppbj
				WHERE
						$divisi
						is_perencanaan = " . $type . " AND
				    	is_status = 2
				        AND del = 0
						AND is_approved = 3
						AND entry_stamp LIKE '%" . $year . "%'";
		$query = $this->db->query($sql);
		// print_r($query);die;
		// echo $this->db->last_query();die;
		return $query->result_array();
	}

	function rekap_department_fp3($year = null, $type = 1)
	{
		$id_division = $this->session->userdata('admin')['id_division'];
		if ($id_division != 1 && $id_division != 5) {
			$divisi = "b.id_division = " . $id_division . " AND ";
		} else {
			$divisi = '';
		}
		$sql = "SELECT 
				    a.*
				FROM
				    ms_fp3 a
				JOIN
					ms_fppbj b
				WHERE
					(
						b.entry_stamp LIKE '%" . $year . "%' AND 
						$divisi 
						b.is_perencanaan = " . $type . " AND 
						b.is_status = 1 AND 
						b.del = 0 AND 
						b.is_approved = 3 AND 
						(
							b.idr_anggaran <= 100000000 OR 
							(
								b.idr_anggaran > 100000000 AND 
								b.metode_pengadaan = 3
							)
						)
                        
						OR  

						(
							b.entry_stamp LIKE '%" . $year . "%' AND 
							$divisi 
							b.is_perencanaan = " . $type . " AND 
							b.is_status = 1 AND 
							b.del = 0 AND 
							b.is_approved = 4 AND 
							b.idr_anggaran > 100000000
						)
					)
				GROUP by b.id";
		$query = $this->db->query($sql);
		// print_r($query);die;
		// echo $this->db->last_query();
		// die;
		return $query->result_array();
	}

	public function getDetailGraph($method, $year)
	{
		$q = " 	SELECT 
					a.nama_pengadaan, b.name, a.year_anggaran, a.is_status, a.id
				FROM
					ms_fppbj a
						LEFT JOIN
					tb_division b ON a.id_division = b.id
				WHERE
					(is_perencanaan = 2 AND is_status = 0 AND is_reject = 0 AND a.entry_stamp LIKE '%" . $year . "%' AND a.metode_pengadaan = $method AND a.del = 0 AND is_approved = 3 AND (idr_anggaran <= 100000000 OR (idr_anggaran > 100000000 AND metode_pengadaan = 3))
					OR  
				
					(is_perencanaan = 2 AND is_status = 0 AND is_reject = 0 AND a.entry_stamp LIKE '%" . $year . "%' AND a.metode_pengadaan = $method AND a.del = 0 AND is_approved = 4 AND idr_anggaran > 100000000))
				
					OR
				
					(is_reject = 0 AND a.entry_stamp LIKE '%" . $year . "%' AND a.metode_pengadaan = $method AND a.is_status = 2 AND is_approved = 3 AND a.del = 0)
				
					OR
				
					(
						(
							is_reject = 0 AND
							is_status = 1 AND 
							a.entry_stamp LIKE '%" . $year . "%' AND a.metode_pengadaan = $method AND 
							a.del = 0 AND 
							is_approved = 3 AND 
							(
								idr_anggaran <= 100000000 OR 
								(
									idr_anggaran > 100000000 AND 
									metode_pengadaan = 3
								)
							)
						)
						OR
						(
							is_reject = 0 AND
							is_status = 1 AND 
							a.entry_stamp LIKE '%" . $year . "%' AND a.metode_pengadaan = $method AND 
							a.del = 0 AND 
							is_approved = 4 AND 
							idr_anggaran > 100000000
						)
					)
				
				order by b.id DESC";
		return $q;
	}
}
