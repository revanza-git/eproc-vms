<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard_model extends MY_Model{
	
	function __construct(){
		parent::__construct();
	}
	function total_rencana_baseline($form){
		$admin = $this->session->userdata('admin');
		$query = "SELECT COUNT(*) ct FROM ms_baseline WHERE del = 0 AND status = 1";
		if($admin['id_role']==2){
			$query .= " AND id_pengguna = ".$admin['id_division'];
		}
		$query = $this->db->query($query)->row_array();
		return $query['ct'];
	}
	function total_realisasi_baseline($form){
		$admin = $this->session->userdata('admin');
		$query = "SELECT COUNT(*) ct FROM ms_procurement a JOIN ms_baseline b ON a.id_baseline = b.id WHERE a.del = 0";
		if($admin['id_role']==2){
			$query .= " AND a.id_pengguna = ".$admin['id_division'];
		}
		$query = $this->db->query($query)->row_array();
		return $query['ct'];
	}
	function total_realisasi_non_baseline($form){
		$admin = $this->session->userdata('admin');
		$query = "SELECT COUNT(*) ct FROM ms_procurement a WHERE a.id_baseline IS NULL AND a.del = 0";
		if($admin['id_role']==2){
			$query .= " AND a.id_pengguna = ".$admin['id_division'];
		}
		$query = $this->db->query($query)->row_array();
		return $query['ct'];
	}
	function total_nilai_baseline($form){
		$admin = $this->session->userdata('admin');
		$query = "SELECT (SUM(idr_budget_investasi) + SUM(idr_budget_operasi)) ct FROM ms_baseline a WHERE del = 0 AND status = 1";
		if($admin['id_role']==2){
			$query .= " AND a.id_pengguna = ".$admin['id_division'];
		}
		$query = $this->db->query($query)->row_array();
		
		return $query['ct'];
	}

	function total_nilai_terkontrak_baseline($form){
		$admin = $this->session->userdata('admin');
		$query = "SELECT SUM(c.contract_price) ct FROM ms_procurement a JOIN ms_baseline b ON a.id_baseline = b.id JOIN ms_contract c ON a.id = c.id_procurement WHERE a.del = 0";
		if($admin['id_role']==2){
			$query .= " AND a.id_pengguna = ".$admin['id_division'];
		}
		$query = $this->db->query($query)->row_array();
		
		return $query['ct'];
	}

	function total_nilai_terkontrak_non_baseline($form){
		$admin = $this->session->userdata('admin');
		$ct = 0;
		$query = "SELECT SUM(c.contract_price) ct FROM ms_procurement a JOIN ms_contract c ON a.id = c.id_procurement WHERE a.del = 0 AND a.id_baseline IS NULL";
		if($admin['id_role']==2){
			$query .= " AND a.id_pengguna = ".$admin['id_division'];
		}
		$query = $this->db->query($query)->row_array();
		if($query['ct']!=null){
			$ct = $query['ct'];
		}
		
		return $ct;
	}
	function total_nilai_terbayar_baseline($form){
		$admin = $this->session->userdata('admin');
		$ct = 0;
		$query = "SELECT SUM(a.value) ct FROM ms_invoice a JOIN ms_procurement b ON a.id_procurement = b.id  WHERE a.del = 0 AND id_baseline IS NOT NULL";
		if($admin['id_role']==2){
			$query .= " AND a.id_pengguna = ".$admin['id_division'];
		}
		$query = $this->db->query($query)->row_array();
		if($query['ct']!=null){
			$ct = $query['ct'];
		}
		
		return $ct;
	}
	function total_nilai_terbayar_non_baseline($form){
		$admin = $this->session->userdata('admin');
		$ct = 0;
		$query = "SELECT SUM(a.value) ct FROM ms_invoice a JOIN ms_procurement b ON a.id_procurement = b.id  WHERE a.del = 0 AND id_baseline IS NULL";
		if($admin['id_role']==2){
			$query .= " AND a.id_pengguna = ".$admin['id_division'];
		}
		$query = $this->db->query($query)->row_array();
		if($query['ct']!=null){
			$ct = $query['ct'];
		}
		
		return $ct;
	}
	
	function rekapPerencanaanGraph($year){

        $data['plan']   				= count($this->rekap_department($year));
        $data['act']    				= count($this->rekap_department_fkpbj($year)) + count($this->rekap_department_fp3($year));
        $data['pelelangan'] 			= $this->getTotalMethodPlan(1,$year) + $this->getTotalMethodActualFKPBJ(1,$year) + $this->getTotalMethodActualFP3(1,$year);
        $data['pemilihan_langsung'] 	= $this->getTotalMethodPlan(2,$year) + $this->getTotalMethodActualFKPBJ(2,$year) + $this->getTotalMethodActualFP3(2,$year);
        $data['swakelola'] 				= $this->getTotalMethodPlan(3,$year) + $this->getTotalMethodActualFKPBJ(3,$year) + $this->getTotalMethodActualFP3(3,$year);
        $data['penunjukan_langsung'] 	= $this->getTotalMethodPlan(4,$year) + $this->getTotalMethodActualFKPBJ(4,$year) + $this->getTotalMethodActualFP3(4,$year);
        $data['pengadaan_langsung'] 	= $this->getTotalMethodPlan(5,$year) + $this->getTotalMethodActualFKPBJ(5,$year) + $this->getTotalMethodActualFP3(5,$year);
        // $data['total']  = count($this->db->select('id')->where('year_anggaran', $year)->where('del', 0)->where('is_reject', 0)->get('ms_fppbj')->result_array());
        // $data['plan']   = count($this->db->select('id')->where('year_anggaran', $year)->where('del', 0)->where('is_status < 2')->where('is_reject', 0)->get('ms_fppbj')->result_array());
        // $data['act']    = count($this->db->select('id')->where('year_anggaran', $year)->where('del', 0)->where('is_status', 2)->where('is_reject', 0)->get('ms_fppbj')->result_array());

        // $data['plan']   = $data['plan'] / $data['total'] * 100;
        // $data['act']    = $data['act'] / $data['total'] * 100;
        
        return $data;
    }

    public function getTotalMethodPlan($method,$year)
    {
    	$id_division = $this->session->userdata('admin')['id_division'];
		if ($id_division != 1 && $id_division != 5) {
			$divisi = "id_division = ".$id_division." AND ";
		}else{
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
						AND ((".$divisi." metode_pengadaan = $method AND del = 0 AND entry_stamp LIKE '%".$year."%' AND is_approved = 3 AND (idr_anggaran <= 100000000 OR (idr_anggaran > 100000000 AND metode_pengadaan = 3))))
						OR  (".$divisi." metode_pengadaan = $method AND del = 0 AND entry_stamp LIKE '%".$year."%' AND is_approved = 4 AND idr_anggaran > 100000000) ";
            $query = $this->db->query($sql);
        // print_r($query);die;
            // echo $this->db->last_query();die;
        return count($query->result_array());
    }

    function getTotalMethodActualFKPBJ($method,$year){
       $id_division = $this->session->userdata('admin')['id_division'];
		if ($id_division != 1 && $id_division != 5) {
			$divisi = "id_division = ".$id_division." AND ";
		}else{
			$divisi = '';
		}
		$sql = "SELECT 
				    *
				FROM
				    ms_fkpbj
				WHERE
						$divisi
						metode_pengadaan = $method AND
				    	is_status = 2
				        AND del = 0
						AND entry_stamp LIKE '%".$year."%'";
            $query = $this->db->query($sql);
        // print_r($query);die;
            // echo $this->db->last_query();die;
        return count($query->result_array());
        
    }

    function getTotalMethodActualFP3($method,$year){
       $id_division = $this->session->userdata('admin')['id_division'];
		if ($id_division != 1 && $id_division != 5) {
			$divisi = "id_division = ".$id_division." AND ";
		}else{
			$divisi = '';
		}
		$sql = "SELECT 
				    *
				FROM
				    ms_fppbj
				WHERE
						$divisi
						metode_pengadaan = $method AND
				    	is_status = 1
				        AND del = 0
						AND entry_stamp LIKE '%".$year."%'";
            $query = $this->db->query($sql);
        // print_r($query);die;
            // echo $this->db->last_query();die;
        return count($query->result_array());
        
    }

    function rekap_department($year = null){
        $id_division = $this->session->userdata('admin')['id_division'];
		if ($id_division != 1 && $id_division != 5) {
			$divisi = "id_division = ".$id_division." AND ";
		}else{
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
						AND ((is_status = 0 AND ".$divisi." del = 0 AND entry_stamp LIKE'%".$year."%' AND is_approved = 3 AND (idr_anggaran <= 100000000 OR (idr_anggaran > 100000000 AND metode_pengadaan = 3))))
						OR  (is_status = 0 AND ".$divisi." del = 0 AND entry_stamp LIKE'%".$year."%' AND is_approved = 4 AND idr_anggaran > 100000000) ";
            $query = $this->db->query($sql);
        // print_r($query);die;
            // echo $this->db->last_query();die;
        return $query->result_array();
    }

    function rekap_department_fkpbj($year = null){
       $id_division = $this->session->userdata('admin')['id_division'];
		if ($id_division != 1 && $id_division != 5) {
			$divisi = "id_division = ".$id_division." AND ";
		}else{
			$divisi = '';
		}
		$sql = "SELECT 
				    *
				FROM
				    ms_fkpbj
				WHERE
						$divisi
				    	is_status = 2
				        AND del = 0
						AND entry_stamp LIKE '%".$year."%'";
            $query = $this->db->query($sql);
        // print_r($query);die;
            // echo $this->db->last_query();die;
        return $query->result_array();
        
    }

    function rekap_department_fp3($year = null){
       $id_division = $this->session->userdata('admin')['id_division'];
		if ($id_division != 1 && $id_division != 5) {
			$divisi = "b.id_division = ".$id_division." AND ";
		}else{
			$divisi = '';
		}
		$sql = "SELECT 
				    a.*
				FROM
				    ms_fp3 a
				JOIN
					ms_fppbj b
				WHERE
						$divisi
				    	b.is_status = 1
				        AND b.del = 0
						AND b.entry_stamp LIKE '%".$year."%'
				GROUP by b.id";
            $query = $this->db->query($sql);
        // print_r($query);die;
            // echo $this->db->last_query();die;
        return $query->result_array();
        
    }
}
