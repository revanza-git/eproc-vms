<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Main_model extends CI_Model{

	function __construct(){
		parent::__construct();

	}

	public function dt($st)
	{
		if ($st == '0') {
			$query = "SELECT * FROM ms_vendor WHERE del = 0 AND vendor_status = 1 AND (need_approve = 0 OR need_approve IS NULL)";
		} else {
			$query = "SELECT * FROM ms_vendor WHERE del = 0 AND vendor_status = 1 AND need_approve = ".$st;
		}
		$query = $this->db->query($query);

		return $query;
	}

	public function search_bar($q){
		// $result = array();

		$query = " SELECT * FROM ms_vendor WHERE del = 0 AND name LIKE '%".$q."%' LIMIT 5";

		$query = $this->db->query($query)->result_array();
		// $query = $this->db->query($query, array('%'.$_POST['search'].'%','%'.$_POST['search'].'%'))->result_array();
		// $result = array();
	
		// foreach($query as $key => $q){
		// 	// if ($value['is_status'] == 0) {
		// 		$class = 'fppbj';
		// 	// } elseif ($value['is_status'] == 1) {
		// 	// 	$class = 'fp3';
		// 	// } else {
		// 	// 	$class = 'fkpbj';
		// 	// }
		// 	$result[$value['id']] = 
		// 		'<div class="search-result"><div class="sr-logo '.$class.'">
		// 			<span class="icon"><i class="fas fa-file-alt"></i></span>
		// 		</div>
		// 		<div class="sr-item">
		// 			<div class="sr-name"><span class="sr-no">1.</span><a href="'.base_url('pemaketan/division/'.$value['id_division'].'/'.$value['id'].'/'.date('Y',strtotime($value['entry_stamp']))).'">'.$value['nama_pengadaan'].'</a></div>
		// 			<div class="sr-keterangan">
		// 				<span class="status">Aktif</span>
		// 				<span class="divisi"'.$value['nama_divisi'].'</span>
		// 			</div>
		// 			<div class="sr-icon">
		// 				<span class="icon ar">
		// 					<i class="fas fa-radiation"></i>
		// 				</span>
		// 				<span class="icon sw" style="font-size: 13px">
		// 					<i class="fas fa-luggage-cart"></i>
		// 				</span>
		// 			</div>
		// 			</div>
		// 		</div>';
		// }
		return $query;
		// return $result;
	}

	function search_data($value){
		$result = array();
		$admin = $this->session->userdata('admin');		
		// $query = "	SELECT
		//                 a.id,
		//                 a.nama_pengadaan,
		//                 b.name nama_divisi,
		//                 a.is_status,
		//                 a.is_approved,
		//                 b.id id_division,
		//                 a.entry_stamp
		// 			FROM ms_fppbj a
		// 			LEFT JOIN tb_division b ON b.id=a.id_division
		// 			WHERE a.del = 0 AND a.nama_pengadaan LIKE ? OR b.name LIKE ?
		// 			LIMIT 5";
		$query = " SELECT * FROM ms_vendor WHERE del = 0 AND name LIKE ? LIMIT 5 ";
	    $query = $this->db->query($query, array('%'.$_POST['search'].'%','%'.$_POST['search'].'%'))->result_array();		
		$result = array();
		// foreach($query as $key => $value){
		// 	if ($value['is_status'] == 0) {
		// 		$class = 'fppbj';
		// 	} elseif ($value['is_status'] == 1) {
		// 		$class = 'fp3';
		// 	} else {
		// 		$class = 'fkpbj';
		// 	}
		// 	$result[$value['id']] = 
		// 		'<div class="search-result"><div class="sr-logo '.$class.'">
		// 			<span class="icon"><i class="fas fa-file-alt"></i></span>
		// 		</div>
		// 		<div class="sr-item">
		// 			<div class="sr-name"><span class="sr-no">1.</span><a href="'.base_url('pemaketan/division/'.$value['id_division'].'/'.$value['id'].'/'.date('Y',strtotime($value['entry_stamp']))).'">'.$value['nama_pengadaan'].'</a></div>
		// 			<div class="sr-keterangan">
		// 				<span class="status">Aktif</span>
		// 				<span class="divisi"'.$value['nama_divisi'].'</span>
		// 			</div>
		// 			<div class="sr-icon">
		// 				<span class="icon ar">
		// 					<i class="fas fa-radiation"></i>
		// 				</span>
		// 				<span class="icon sw" style="font-size: 13px">
		// 					<i class="fas fa-luggage-cart"></i>
		// 				</span>
		// 			</div>
		// 			</div>
		// 		</div>';
		// }
		
		return $result;
	}

	public function to_app($id)
	{
		$query = "	SELECT
						a.*
						-- b.name role_name
						-- c.name division
					FROM
						ms_admin a
					-- JOIN
					-- 	tb_role b ON b.id=a.id_role
					-- JOIN
					-- 	tb_division c ON c.id=a.id_division
					WHERE
						a.id = ?
		"; 

		$query = $this->db->query($query,array($id))->row_array();
		// echo $this->eproc_db->last_query();die;
		return $query;
	}

	function get_daftar_tunggu_chart(){

		$query = " 	SELECT 
						*

					FROM 
						ms_vendor a

					WHERE 
						a.vendor_status = 1
						AND a.is_active = 1
					";
		// if($this->session->userdata('admin')['id_role']==8){
			$query .= " AND a.need_approve = 1 ";
		// }
		$query .=	" ORDER BY 
						a.edit_stamp DESC
						
					";
		$result = $this->db->query($query);
		return $result;

	}

	function daftar_hitam_chart(){

		$query = " 	SELECT 
						*

					FROM 
						ms_vendor a

					LEFT JOIN 
						tr_blacklist b ON b.id_vendor = a.id

					WHERE 
						a.is_active = 0 
						AND b.del = 0
						AND b.id_blacklist = 2
						AND a.del = 0";
		//if($this->session->userdata('admin')['id_role']==8){
			//$query .= " AND b.need_approve = 1 ";
		//}
		$query .=	" ORDER BY 
						b.start_date DESC
					";
		$result = $this->db->query($query);
		return $result;

	}

	function daftar_merah_chart(){

		$query = " 	SELECT 
						*

					FROM 
						ms_vendor a

					LEFT JOIN 
						tr_blacklist b ON b.id_vendor = a.id

					WHERE 
						a.is_active = 0 
						AND b.del = 0
						AND b.id_blacklist = 1
						AND a.del = 0";
		//if($this->session->userdata('admin')['id_role']==8){
			//$query .= " AND b.need_approve = 1 ";
		//}
		$query .=	" ORDER BY 
						b.start_date DESC
					";
		$result = $this->db->query($query);
		return $result;

	}

	function dpt_chart(){
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
		
		$query = " 	SELECT 
						*

					FROM 
						ms_vendor a

					LEFT JOIN 
						tr_dpt b ON b.id_vendor = a.id 

					WHERE 
						a.is_active = 1
						AND a.vendor_status = 2
						AND a.del = 0
					
					GROUP BY
						a.id 

					ORDER BY 
						b.start_date DESC


					";
		$result = $this->db->query($query);
		return $result;

	}
}