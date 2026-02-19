<?php defined('BASEPATH') or exit('No direct script access allowed');
class Dashboard_model extends MY_Model
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('Main_model', 'mm');
		$this->load->driver('cache', array('adapter' => 'file'));
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
		$admin = $this->session->userdata('admin');
		$id_role = (is_array($admin) && isset($admin['id_role'])) ? (int) $admin['id_role'] : 0;
		$id_division = (is_array($admin) && isset($admin['id_division'])) ? (int) $admin['id_division'] : 0;
		$cacheKey = 'dash_rekapPerencanaanGraph_' . (int) $year . '_r' . $id_role . '_d' . $id_division;
		$cached = $this->cache->get($cacheKey);
		if ($cached !== false && is_array($cached)) {
			return $cached;
		}

		$this->load->model('Export_test_model', 'ext');

		$fppbj_baru = count($this->rekap_department($year, 2));
		$fkpbj_baru = count($this->rekap_department_fkpbj($year, 2));
		$fp3_baru = count($this->rekap_department_fp3($year, 2));

		// Initialize all variables to prevent undefined variable errors
		$fppbj_pelelangan = 0;
		$fppbj_pemilihan_langsung = 0;
		$fppbj_swakelola = 0;
		$fppbj_penunjukan_langsung = 0;
		$fppbj_pengadaan_langsung = 0;
		
		$fppbj_baru_pelelangan = 0;
		$fppbj_baru_pemilihan_langsung = 0;
		$fppbj_baru_swakelola = 0;
		$fppbj_baru_penunjukan_langsung = 0;
		$fppbj_baru_pengadaan_langsung = 0;
		
		$fkpbj_pelelangan = 0;
		$fkpbj_pemilihan_langsung = 0;
		$fkpbj_swakelola = 0;
		$fkpbj_penunjukan_langsung = 0;
		$fkpbj_pengadaan_langsung = 0;
		
		$fkpbj_baru_pelelangan = 0;
		$fkpbj_baru_pemilihan_langsung = 0;
		$fkpbj_baru_swakelola = 0;
		$fkpbj_baru_penunjukan_langsung = 0;
		$fkpbj_baru_pengadaan_langsung = 0;
		
		$fp3_baru_pelelangan = 0;
		$fp3_baru_pemilihan_langsung = 0;
		$fp3_baru_swakelola = 0;
		$fp3_baru_penunjukan_langsung = 0;
		$fp3_baru_pengadaan_langsung = 0;
		
		$fp3_lama_pelelangan = 0;
		$fp3_lama_pemilihan_langsung = 0;
		$fp3_lama_swakelola = 0;
		$fp3_lama_penunjukan_langsung = 0;
		$fp3_lama_pengadaan_langsung = 0;

		$data_fppbj = $this->ext->rekap_total_department_fppbj($year, 1);
		$data_fppbj_baru = $this->ext->rekap_total_department_fppbj($year, 2);
		$data_fkpbj_baru = $this->ext->rekap_total_department_fkpbj_baru($year);
		$data_fkpbj = $this->ext->count_total_department_fkpbj($year);
		$data_fp3_baru = $this->ext->rekap_total_department_fp3_by_type($year);
		$data_fp3_lama = $this->ext->rekap_total_department_fp3_by_type($year, 1);

		$metodes = [
			1 => 'Pelelangan',
			2 => 'Pemilihan Langsung',
			3 => 'Swakelola',
			4 => 'Penunjukan Langsung',
			5 => 'Pengadaan Langsung'
		];

		foreach ($metodes as $key_metode => $metode) {
			$fppbj = (int) $data_fppbj[0]['metode_' . $key_metode];
			$fppbj_baru = (int) $data_fppbj_baru[0]['metode_' . $key_metode];
			$fkpbj = (int) $data_fkpbj[0]['metode_' . $key_metode];
			$fkpbj_baru = (int) $data_fkpbj_baru[0]['metode_' . $key_metode];
			$fp3_baru = (int) $data_fp3_baru[0]['metode_' . $key_metode];
			$fp3_lama = (int) $data_fp3_lama[0]['metode_' . $key_metode];

			${'fppbj_baru_' . (str_replace(" ", "_", strtolower($metode)))} = $fppbj_baru;
			${'fppbj_' . (str_replace(" ", "_", strtolower($metode)))} = $fppbj;
			${'fkpbj_' . (str_replace(" ", "_", strtolower($metode)))} = $fkpbj;
			${'fkpbj_baru_' . (str_replace(" ", "_", strtolower($metode)))} = $fkpbj_baru;
			${'fp3_baru_' . (str_replace(" ", "_", strtolower($metode)))} = $fp3_baru;
			${'fp3_lama_' . (str_replace(" ", "_", strtolower($metode)))} = $fp3_lama;
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

		$data['plan']   				= count($this->mm->get_fppbj_selesai($year)->result());
		$data['act']    				= count($this->rekap_department_fkpbj($year));
		$data['act_out']				= count($this->rekap_department($year, 2)) + count($this->rekap_department_fkpbj($year, 2)) + count($this->rekap_department_fp3($year, 2)); //+ count($this->rekap_department($year,2))

		$data['pelelangan']             = $total_fppbj_pelelangan + $total_fkpbj_pelelangan + $total_fp3_pelelangan; //$total_fppbj_pelelangan + $total_fkpbj_pelelangan + $total_fp3_pelelangan;
        $data['pemilihan_langsung']     = $total_fppbj_pemilihan_langsung + $total_fkpbj_pemilihan_langsung + $total_fp3_pemilihan_langsung; //$total_fppbj_pemilihan_langsung + $total_fkpbj_pemilihan_langsung + $total_fp3_pemilihan_langsung;
        $data['swakelola']              = $total_fppbj_swakelola + $total_fkpbj_swakelola + $total_fp3_swakelola; //$total_fppbj_swakelola + $total_fkpbj_swakelola + $total_fp3_swakelola;
        $data['penunjukan_langsung']    = $total_fppbj_penunjukan_langsung + $total_fkpbj_penunjukan_langsung + $total_fp3_penunjukan_langsung; //$total_fppbj_penunjukan_langsung + $total_fkpbj_penunjukan_langsung + $total_fp3_penunjukan_langsung;
        $data['pengadaan_langsung']     = $total_fppbj_pengadaan_langsung + $total_fkpbj_pengadaan_langsung + $total_fp3_pengadaan_langsung; //$total_fppbj_pengadaan_langsung + $total_fkpbj_pengadaan_langsung + $total_fp3_pengadaan_langsung;
        
        // Add protection against division by zero
        if ($data['plan'] > 0) {
            $data['percent_act'] = round(($data['act'] / $data['plan']) * 100);
            $data['percent_act_out'] = round(($data['act_out'] / $data['plan']) * 100);
        } else {
            $data['percent_act'] = 0;
            $data['percent_act_out'] = 0;
        }

		$this->cache->save($cacheKey, $data, 300);
		return $data;
	}

	public function getTotalMethodPlan($method, $year)
	{
		$admin = $this->session->userdata('admin');
		$id_division = (is_array($admin) && isset($admin['id_division'])) ? (int) $admin['id_division'] : null;
		$method = (int) $method;
		$year = (int) $year;

		$this->db->from('ms_fppbj a');
		$this->db->join('ms_fppbj_year_anggaran ya', 'ya.id_fppbj = a.id AND ya.year_anggaran = ' . $year, 'inner', false);
		$this->db->where('a.is_status', 0);
		$this->db->where('a.is_reject', 0);
		$this->db->where('a.del', 0);
		$this->db->where('a.is_approved_hse <', 2);
		$this->db->where('a.metode_pengadaan', $method);
		if ($id_division !== null && $id_division !== 1 && $id_division !== 5) {
			$this->db->where('a.id_division', $id_division);
		}
		$this->db->group_start();
			$this->db->group_start();
				$this->db->where('a.is_approved', 3);
				$this->db->group_start();
					$this->db->where('a.idr_anggaran <=', 100000000);
					$this->db->or_group_start();
						$this->db->where('a.idr_anggaran >', 100000000);
						$this->db->where('a.metode_pengadaan', 3);
					$this->db->group_end();
				$this->db->group_end();
			$this->db->group_end();
			$this->db->or_group_start();
				$this->db->where('a.is_approved', 4);
				$this->db->where('a.idr_anggaran >', 100000000);
			$this->db->group_end();
		$this->db->group_end();

		return (int) $this->db->count_all_results();
	}

	function getTotalMethodActualFKPBJ($method, $year)
	{
		$admin = $this->session->userdata('admin');
		$id_division = (is_array($admin) && isset($admin['id_division'])) ? (int) $admin['id_division'] : null;
		$method = (int) $method;
		$year = (int) $year;

		$this->db->from('ms_fppbj a');
		$this->db->join('ms_fppbj_year_anggaran ya', 'ya.id_fppbj = a.id AND ya.year_anggaran = ' . $year, 'inner', false);
		$this->db->where('a.is_status', 2);
		$this->db->where('a.del', 0);
		$this->db->where('a.metode_pengadaan', $method);
		if ($id_division !== null && $id_division !== 1 && $id_division !== 5) {
			$this->db->where('a.id_division', $id_division);
		}

		return (int) $this->db->count_all_results();
	}

	function getTotalMethodActualFP3($method, $year)
	{
		$admin = $this->session->userdata('admin');
		$id_division = (is_array($admin) && isset($admin['id_division'])) ? (int) $admin['id_division'] : null;
		$method = (int) $method;
		$year = (int) $year;

		$this->db->from('ms_fppbj a');
		$this->db->join('ms_fppbj_year_anggaran ya', 'ya.id_fppbj = a.id AND ya.year_anggaran = ' . $year, 'inner', false);
		$this->db->where('a.is_status', 1);
		$this->db->where('a.del', 0);
		$this->db->where('a.metode_pengadaan', $method);
		if ($id_division !== null && $id_division !== 1 && $id_division !== 5) {
			$this->db->where('a.id_division', $id_division);
		}

		return (int) $this->db->count_all_results();
	}

	function rekap_department($year = null, $type = 1)
	{
		$admin = $this->session->userdata('admin');
		$id_division = (is_array($admin) && isset($admin['id_division'])) ? (int) $admin['id_division'] : null;
		$type = (int) $type;
		$year = (int) $year;

		$this->db->from('ms_fppbj a');
		$this->db->join('ms_fppbj_year_anggaran ya', 'ya.id_fppbj = a.id AND ya.year_anggaran = ' . $year, 'inner', false);
		$this->db->where('a.is_reject', 0);
		$this->db->where('a.del', 0);
		$this->db->where('a.is_status', 0);
		$this->db->where('a.is_perencanaan', $type);
		if ($id_division !== null && $id_division !== 1 && $id_division !== 5) {
			$this->db->where('a.id_division', $id_division);
		}
		$this->db->group_start();
			$this->db->group_start();
				$this->db->where('a.is_approved', 3);
				$this->db->group_start();
					$this->db->where('a.idr_anggaran <=', 100000000);
					$this->db->or_group_start();
						$this->db->where('a.idr_anggaran >', 100000000);
						$this->db->where('a.metode_pengadaan', 3);
					$this->db->group_end();
				$this->db->group_end();
			$this->db->group_end();
			$this->db->or_group_start();
				$this->db->where('a.is_approved', 4);
				$this->db->where('a.idr_anggaran >', 100000000);
			$this->db->group_end();
		$this->db->group_end();

		return $this->db->get()->result_array();
	}

	function rekap_department_fkpbj($year = null, $type = 1)
	{
		$admin = $this->session->userdata('admin');
		$id_division = (is_array($admin) && isset($admin['id_division'])) ? (int) $admin['id_division'] : null;
		$type = (int) $type;
		$year = (int) $year;

		$this->db->from('ms_fppbj a');
		$this->db->join('ms_fppbj_year_anggaran ya', 'ya.id_fppbj = a.id AND ya.year_anggaran = ' . $year, 'inner', false);
		$this->db->where('a.is_perencanaan', $type);
		$this->db->where('a.is_status', 2);
		$this->db->where('a.is_approved', 3);
		$this->db->where('a.del', 0);
		if ($id_division !== null && $id_division !== 1 && $id_division !== 5) {
			$this->db->where('a.id_division', $id_division);
		}

		return $this->db->get()->result_array();
	}

	function rekap_department_fp3($year = null, $type = 1)
	{
		$admin = $this->session->userdata('admin');
		$id_division = (is_array($admin) && isset($admin['id_division'])) ? (int) $admin['id_division'] : null;
		$type = (int) $type;
		$year = (int) $year;

		$this->db->select('a.*');
		$this->db->from('ms_fp3 a');
		$this->db->join('ms_fppbj b', 'a.id_fppbj = b.id', 'inner');
		$this->db->join('ms_fppbj_year_anggaran ya', 'ya.id_fppbj = b.id AND ya.year_anggaran = ' . $year, 'inner', false);
		$this->db->where('b.is_status', 1);
		$this->db->where('b.is_perencanaan', $type);
		$this->db->where('b.del', 0);
		if ($id_division !== null && $id_division !== 1 && $id_division !== 5) {
			$this->db->where('b.id_division', $id_division);
		}
		$this->db->group_by('b.id');

		return $this->db->get()->result_array();
	}
	
	public function getDetailGraph($method, $year)
	{
		$method = (int) $method;
		$year = (int) $year;

		$this->db->select('a.nama_pengadaan, b.name, ya.year_anggaran, a.is_status, a.id', false);
		$this->db->from('ms_fppbj a');
		$this->db->join('tb_division b', 'a.id_division = b.id', 'left');
		$this->db->join('ms_fppbj_year_anggaran ya', 'ya.id_fppbj = a.id AND ya.year_anggaran = ' . $year, 'inner', false);

		$this->db->group_start();
			$this->db->group_start();
				$this->db->where('a.is_perencanaan', 2);
				$this->db->where('a.is_status', 0);
				$this->db->where('a.is_reject', 0);
				$this->db->where('a.metode_pengadaan', $method);
				$this->db->where('a.del', 0);
				$this->db->where('a.is_approved', 3);
				$this->db->group_start();
					$this->db->where('a.idr_anggaran <=', 100000000);
					$this->db->or_group_start();
						$this->db->where('a.idr_anggaran >', 100000000);
						$this->db->where('a.metode_pengadaan', 3);
					$this->db->group_end();
				$this->db->group_end();
			$this->db->group_end();

			$this->db->or_group_start();
				$this->db->where('a.is_perencanaan', 2);
				$this->db->where('a.is_status', 0);
				$this->db->where('a.is_reject', 0);
				$this->db->where('a.metode_pengadaan', $method);
				$this->db->where('a.del', 0);
				$this->db->where('a.is_approved', 4);
				$this->db->where('a.idr_anggaran >', 100000000);
			$this->db->group_end();

			$this->db->or_group_start();
				$this->db->where('a.is_reject', 0);
				$this->db->where('a.metode_pengadaan', $method);
				$this->db->where('a.is_status', 2);
				$this->db->where('a.is_approved', 3);
				$this->db->where('a.del', 0);
			$this->db->group_end();

			$this->db->or_group_start();
				$this->db->group_start();
					$this->db->where('a.is_reject', 0);
					$this->db->where('a.is_status', 1);
					$this->db->where('a.metode_pengadaan', $method);
					$this->db->where('a.del', 0);
					$this->db->where('a.is_approved', 3);
					$this->db->group_start();
						$this->db->where('a.idr_anggaran <=', 100000000);
						$this->db->or_group_start();
							$this->db->where('a.idr_anggaran >', 100000000);
							$this->db->where('a.metode_pengadaan', 3);
						$this->db->group_end();
					$this->db->group_end();
				$this->db->group_end();
				$this->db->or_group_start();
					$this->db->where('a.is_reject', 0);
					$this->db->where('a.is_status', 1);
					$this->db->where('a.metode_pengadaan', $method);
					$this->db->where('a.del', 0);
					$this->db->where('a.is_approved', 4);
					$this->db->where('a.idr_anggaran >', 100000000);
				$this->db->group_end();
			$this->db->group_end();
		$this->db->group_end();

		$this->db->order_by('b.id', 'DESC');
		return $this->db->get_compiled_select();
	}
	
	public function rekapAllFPPBJFinish($year, $type = 1)
    {
		$admin = $this->session->userdata('admin');
		$id_division = (is_array($admin) && isset($admin['id_division']) && $admin['id_division'] !== '') ? (int) $admin['id_division'] : null;
		$year = (int) $year;
		$type = (int) $type;

		$this->db->from('ms_fppbj fppbj');
		$this->db->join('ms_fppbj_year_anggaran ya', 'ya.id_fppbj = fppbj.id AND ya.year_anggaran = ' . $year, 'inner', false);
		$this->db->where('fppbj.is_status <=', 3);
		$this->db->where('fppbj.is_perencanaan', $type);
		$this->db->where('fppbj.del', 0);
		if ($id_division !== null && $id_division != 1 && $id_division != 5) {
			$this->db->where('fppbj.id_division', $id_division);
		}

        return $this->db->get();
    }
}
