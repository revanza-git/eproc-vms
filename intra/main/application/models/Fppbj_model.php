<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Fppbj_model extends MY_Model{
	public $table = 'ms_fppbj';
	function __construct(){
		parent::__construct();

	}
	function getData($form=array()){
		$query = "	SELECT  nama_pengadaan,
							idr_anggaran,
							year_anggaran,
							id
					FROM ".$this->table."";
		if($this->input->post('filter')){
			$query .= $this->filter($form, $this->input->post('filter'), true);
		}
		
		return $query;
	}

	function selectData($id){
		$query = "SELECT 	a.*, b.name division, c.value, d.id_fppbj swakelola
						FROM ".$this->table." a
						LEFT JOIN tb_division b ON b.id = a.id_division
						LEFT JOIN tb_csms_limit c ON c.id=a.csms
						LEFT JOIN tr_analisa_swakelola d ON d.id_fppbj=a.id
						WHERE a.id = ".$id;
		$query = $this->db->query($query);
		return $query->row_array();
	}

	function delete($id){

		$get_data = $this->db->where('id',$id)->get('ms_fppbj')->row_array();

		$activity = $this->session->userdata('admin')['name']." menghapus data : ".$get_data['nama_pengadaan'];

		$this->activity_log($this->session->userdata('admin')['id_user'],$activity,$id);

		$this->db->where('id_pengadaan', $id)->update('tr_email_blast',array('del'=>1));

		return $this->db->where('id', $id)
					->update($this->table, array(
											'del' => 1,
											'edit_stamp' => timestamp()
											)
					);
	}

	public function get_analisa_resiko($id_fppbj)
	{
		$query = "SELECT 	tr_analisa_risiko.*, ms_fppbj.id id_fppbj
						FROM tr_analisa_risiko
						LEFT JOIN ms_fppbj ON ms_fppbj.id = tr_analisa_risiko.id_fppbj
						WHERE tr_analisa_risiko.id_fppbj = ".$id_fppbj;
		$query = $this->db->query($query);
		return $query->row_array();	
	}

	public function get_data_analisa_risiko_detail($id)
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
					INNER JOIN
						ms_fppbj c ON c.id=b.id_fppbj
					WHERE c.tipe_pengadaan != 'barang' AND b.id_fppbj = ".$query1['id'];
		$sql .= " GROUP BY a.id ORDER BY a.id ASC";
		$query = $this->db->query($sql)->result_array();
		return $query;
		 // print_r($query);die;
		
		// echo $table;
	}

	public function get_analisa_swakelola($id_fppbj)
	{
		$query = "SELECT 	tr_analisa_swakelola.*, ms_fppbj.id id_fppbj
						FROM tr_analisa_swakelola
						LEFT JOIN ms_fppbj ON ms_fppbj.id = tr_analisa_swakelola.id_fppbj
						WHERE tr_analisa_swakelola.id_fppbj = ".$id_fppbj;
		$query = $this->db->query($query);
		return $query->row_array();
	}

	function selectDataFppbj($id){
		$query = "SELECT 	a.*,
							a.id id,
							b.id id_proc_method,
							b.name metode_pengadaan_name
						FROM ".$this->table." a
						JOIN
							tb_proc_method b ON b.id=a.metode_pengadaan
							WHERE a.id = ".$id;
		$query = $this->db->query($query, array($id));
		return $query->row_array();
	}

	public function getFPPBJ(){
		$this->db->select('ms_fppbj.nama_pengadaan nama_pengadaan, ms_fppbj.jenis_pengadaan jenis_pengadaan,ms_fppbj.penggolongan_penyedia penggolongan_penyedia, ms_fppbj.csms penggolongan_CSMS,ms_fppbj.jwpp jwpp, ms_fppbj.jwp jwp, ms_fppbj.hps hps, ms_fppbj.desc_metode_pembayaran desc_metode_pembayaran, ms_fppbj.jenis_kontrak jenis_kontrak, ms_fppbj.desc desc');
		$this->db->where('(ms_fppbj.del IS NULL OR ms_fppbj.del = 0)');
		$query= $this->db->get('ms_fppbj');

		return $query->result_array();

	}

	public function date_compare($a, $b){
		echo $a;
		echo $b;
		die;
		$t1 = strtotime($a['entry_stamp']);
		$t2 = strtotime($b['entry_stamp']);
		return $t1 - $t2;
	}

	public function getProc($id){
		$this->db->select('ms_fppbj.id, year_anggaran, tb_proc_method.name metode_pengadaan, ms_fppbj.nama_pengadaan, ms_fppbj.jenis_pengadaan,ms_fppbj.penggolongan_penyedia, ms_fppbj.csms,ms_fppbj.jwpp, ms_fppbj.jwp, ms_fppbj.hps, ms_fppbj.desc_metode_pembayaran, ms_fppbj.jenis_kontrak, ms_fppbj.desc');
		$this->db->where('(ms_fppbj.del IS NULL OR ms_fppbj.del = 0)');
		$this->db->where('ms_fppbj.id', $id);
		$this->db->join('tb_proc_method', 'tb_proc_method.id = metode_pengadaan');
		$query= $this->db->get('ms_fppbj');
		
		$data = $query->row_array();

		$data['fkpbj_detail'] 	= $this->db->select('id id_fkpbj, file, desc, is_status, entry_stamp')->where('del', 0)->where('id_fppbj', $data['id'])->get('ms_fkpbj')->result_array();
		$data['fp3_detail'] 	= $this->db->select('id id_fp3, no_pr, n_pengadaan, m_pengadaan, jadwal, desc, is_status, entry_stamp')->where('del', 0)->where('id_fppbj', $data['id'])->get('ms_fp3')->result_array();

		foreach ($data['fkpbj_detail'] as $key => $value)
			$data['fkpbj_detail'][$key]['type']	= "fkpbj";
		
		foreach ($data['fp3_detail'] as $key => $value)
			$data['fp3_detail'][$key]['type']	= "fp3";

		$data['detail']	= array_merge ($data['fkpbj_detail'], $data['fp3_detail']);

		return $data;

	}

	public function approve($id_fppbj, $status){
		return $this->db->where('id', $id_fppbj)->update('ms_fppbj', $status);
	}

	public function get_description()
	{
		$sql = "SELECT description FROM tb_comitment_desc";
		$query = $this->db->query($sql)->row_array();
		return $query;
	}
	public function get_email($id_fppbj)
	{
		$query = "SELECT 
					email
			   FROM 
			   		ms_user
			   JOIN ms_fppbj on ms_fppbj.id_division = ms_user.id_division
			   WHERE ms_fppbj.id = " .$id_fppbj;
		$query = $this->db->query($query)->result_array();
		// print_r($query);
		return $query;

	}

	public function insert_keterangan($keterangan)
	{
		$data = $this->db->where('id_fppbj',$keterangan['id_fppbj'])->where('type','reject')->get('tr_note')->result_array();
		if (count($data)>0) {
			$this->db->where('id_fppbj',$keterangan['id_fppbj'])->where('type','reject')->delete('tr_note');
			return $this->db->insert('tr_note',$keterangan);
		} else{
			return $this->db->insert('tr_note',$keterangan);
		}		
	}
	
	public function getValResiko($id)
	{
		$analisa_resiko =$this->pm->get_data_analisa($id);

		$getCat = array();
		if(count($analisa_resiko) > 0){
			//echo "Masuk kon 1";die;
			foreach ($analisa_resiko as $key => $value) {

				$manusia 	= $this->setCategory($value['manusia']);
				$asset 		= $this->setCategory($value['asset']);
				$lingkungan = $this->setCategory($value['lingkungan']);
				$hukum 		= $this->setCategory($value['hukum']);
				
				//echo $manusia.' - '.$asset.' - '.$lingkungan.' - '.$hukum;die;
				//SET CATEGORY PER QUESTION 
				if ($manusia == "extreme" || $asset == "extreme" || $lingkungan == "extreme" || $hukum == "extreme") {
					$category = '<span id="catatan" class="catatan"><span id="catatan" class="catatan red">E</span></span>';
				}else if ($manusia == "high" || $asset == "high" || $lingkungan == "high" || $hukum == "high") {
					$category = '<span id="catatan" class="catatan"><span id="catatan" class="catatan red">H</span></span>';
				}else  if ($manusia == "medium" || $asset == "medium" || $lingkungan == "medium" || $hukum == "medium") {
					$category = '<span id="catatan" class="catatan"><span id="catatan" class="catatan yellow">M</span></span>';
				}else if ($manusia == "low" || $asset == "low" || $lingkungan == "low" || $hukum == "low") {
					$category = '<span id="catatan" class="catatan"><span id="catatan" class="catatan green">L</span></span>';
				}else{
					$category = '<span id="catatan" class="catatan"><span id="catatan" class="catatan">?</span></span>';
				}
					
				array_push($getCat, $category);

				if (in_array('<span id="catatan" class="catatan"><span id="catatan" class="catatan red">E</span></span>', $getCat, TRUE)){
					$total = 'E';
				}else if (in_array('<span id="catatan" class="catatan"><span id="catatan" class="catatan red">H</span></span>', $getCat, TRUE)){
					$total = 'H';
				}else if (in_array('<span id="catatan" class="catatan"><span id="catatan" class="catatan yellow">M</span></span>', $getCat, TRUE)){
					$total = 'M';
				}else if (in_array('<span id="catatan" class="catatan"><span id="catatan" class="catatan green">L</span></span>', $getCat, TRUE)){
					$total = 'L';
				}else{
					$total = '-';
				}

				return $total;
			}
		}
	}

	public function setCategory($val){
    	if ($val >= 1 && $val <= 4) {
		return 'low';
		// return '<span id="catatan" class="catatan green">L</span>';
		}else if ($val > 4 && $val <= 9) {
			return 'medium';
			// return '<span id="catatan" class="catatan yellow">M</span>';		
		}else if ($val >= 10 && $val <= 14) {
			return 'high';
			// return '<span id="catatan" class="catatan red">H</span>';
		}else if ($val >= 15 && $val <= 25) {
			return 'extreme';
			// return '<span id="catatan" class="catatan red">E</span>';
		}else{
			return false;
		}
    }
	
	public function insert_tr_email_blast($id,$jwpp_start,$metode)
	{
			$metode_day	= 0;

			$get_metode = $this->db->where('id',$metode)->get('tb_proc_method')->row_array();

			$fppbj = $this->db->where('id', $id)->get('ms_fppbj')->row_array();

			$metode = trim($get_metode['name']);
			if ($metode == "Pelelangan") {
				$metode_day = 60; //60 hari
			}else if ($metode == "Pengadaan Langsung") {
				$metode_day = 10;// 10 hari
			}else if ($metode == "Pemilihan Langsung"){
				$metode_day = 45; //45 hari
			}else if ($metode == "Swakelola"){
				$metode_day = 0;
			}else if ($metode == "Penunjukan Langsung") {
				$metode_day = 20;// 20 hari
			}else{
				// $metode_day = 1;
			}
			$yellow = $jwpp_start;
	        // echo $value['metode_pengadaan'].'<br>';
	        $start_yellow 	= $metode_day+14;
	        $end_yellow 	= $metode_day+1;
			$yellow__ 		= date('Y-m-d', strtotime($yellow.'-'.$start_yellow.' days'));
			$yellow___ 		= date('Y-m-d', strtotime($yellow.'-'.$end_yellow.' days'));
			
			$prevDate 		= date('Y-m-d', strtotime($yellow__.'-3 days'));

			if ($fppbj['metode_pengadaan'] != 3) {
				if ($fppbj['is_perencanaan'] == 1) {
					$this->date_periode($id,$prevDate,$yellow__,1);
				} else {
					$this->date_periode($id,$yellow__,$yellow___,2);
				}
			}	
		
	}

	public function date_periode($id,$begin,$end,$type)
	{
		$begin = new DateTime($begin);
		$end = new DateTime($end);

		$interval = DateInterval::createFromDateString('1 day');
		$period = new DatePeriod($begin, $interval, $end);
		
		foreach ($period as $dt) {
			// echo $dt->format("Y-m-d").'<br>';die;
			$data = array(
				'id_pengadaan'	=> $id,
				'date_alert'	=> $dt->format("Y-m-d"),
				'type'			=> $type,
				'entry_stamp'	=> date('Y-m-d H:i:s')
			);
			$this->db->insert('tr_email_blast',$data);
		}
	}
	
	public function edit_tr_email_blast($id,$jwpp_start,$metode)
	{
			$metode_day	= 0;

			$get_metode = $this->db->where('id',$metode)->get('tb_proc_method')->row_array();

			$fppbj = $this->db->where('id', $id)->get('ms_fppbj')->row_array();

			$metode = trim($get_metode['name']);
			if ($metode == "Pelelangan") {
				$metode_day = 60; //60 hari
			}else if ($metode == "Pengadaan Langsung") {
				$metode_day = 10;// 10 hari
			}else if ($metode == "Pemilihan Langsung"){
				$metode_day = 45; //45 hari
			}else if ($metode == "Swakelola"){
				$metode_day = 0;
			}else if ($metode == "Penunjukan Langsung") {
				$metode_day = 20;// 20 hari
			}else{
				// $metode_day = 1;
			}
			$yellow = $jwpp_start;
	        // echo $value['metode_pengadaan'].'<br>';
	        $start_yellow 	= $metode_day+14;
	        $end_yellow 	= $metode_day+1;
			$yellow__ 		= date('Y-m-d', strtotime($yellow.'-'.$start_yellow.' days'));
			$yellow___ 		= date('Y-m-d', strtotime($yellow.'-'.$end_yellow.' days'));
			
			$prevDate 		= date('Y-m-d', strtotime($yellow__.'-3 days'));

			if ($fppbj['metode_pengadaan'] != 3) {
				if ($fppbj['is_perencanaan'] == 1) {
					$this->date_periode($id,$prevDate,$yellow__,1);
				} else {
					$this->date_periode($id,$yellow__,$yellow___,2);
				}
			}			
	}

	public function edit_date_periode($id,$begin,$end,$type)
	{
		$begin = new DateTime($begin);
		$end = new DateTime($end);

		$interval = DateInterval::createFromDateString('1 day');
		$period = new DatePeriod($begin, $interval, $end);
		
		$this->db->where('id_pengadaan',$id)->where('type',$type)->delete('tr_email_blast');
		
		foreach ($period as $dt) {
			// echo $dt->format("Y-m-d").'<br>';die;
			$data = array(
				'id_pengadaan'	=> $id,
				'date_alert'	=> $dt->format("Y-m-d"),
				'type'			=> $type,
				'edit_stamp'	=> date('Y-m-d H:i:s')
			);
			$this->db->insert('tr_email_blast',$data);
		}
	}
}