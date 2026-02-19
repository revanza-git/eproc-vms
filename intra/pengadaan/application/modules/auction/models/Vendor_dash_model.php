<?php
class Vendor_dash_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
	}

	function get_auction_list_vendor($search='', $sort='', $page='', $per_page='',$is_page=FALSE){
    	$user = $this->session->userdata('user');

		$this->db->select('*, ms_procurement.id id, ms_procurement.name name, ms_procurement.work_area work_area, ms_procurement.auction_date auction_date, ms_vendor.name pemenang, "-" as pemenang_kontrak , "-" as user_kontrak , proc_date, ms_procurement.del del');
		$this->db->group_by('ms_procurement.id');
		$this->db->where('ms_procurement.del',0);
		$this->db->where('ms_procurement.id_mekanisme',1);
		$this->db->where('ms_procurement_peserta.id_vendor',$this->session->userdata('user')['id_user']);
		$this->db->where('ms_procurement.auction_date=',date('Y-m-d'));
		$this->db->join('ms_procurement_peserta','ms_procurement_peserta.id_proc=ms_procurement.id','LEFT');
		$this->db->join('ms_vendor','ms_procurement_peserta.is_winner=ms_vendor.id','LEFT');
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){

			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
		
		$query = $this->db->get('ms_procurement');

		return $query->result_array();
		
    }

	function get_data($id_lelang = ''){
		$sql = "SELECT a.*,
					   (SELECT GROUP_CONCAT(symbol) FROM tb_kurs WHERE id IN (SELECT id_kurs FROM ms_procurement_kurs WHERE id_procurement = a.id)) AS rate, 
					   b.metode_auction,
					   b.hps, 
					   b.metode_penawaran 
					   
				FROM ms_procurement a 
				LEFT JOIN ms_procurement_tatacara b ON a.id = b.id_procurement 
				
				WHERE a.id = ?";
		
		$sql = $this->db->query($sql, array($id_lelang));
		return $sql->row_array();
	}
	
	function kurs_info($id_lelang = ""){
		$sql = "SELECT a.*, b.name FROM ms_procurement_kurs a LEFT JOIN tb_kurs b ON a.id_kurs = b. id WHERE a.id_procurement = ? AND a.id_kurs <> 1"; 
		return $this->db->query($sql, $id_lelang);
	}
	
	function select_tata_cara($id_lelang = ''){
		$sql = "SELECT metode_penawaran FROM ms_procurement_tatacara WHERE id_procurement = ?";
		$sql = $this->db->query($sql, $id_lelang);
		$sql = $sql->row_array();
		
		return $sql['metode_penawaran'];
	}
	
	function get_syarat($id_lelang = ''){
		$sql = "SELECT description FROM ms_procurement_persyaratan WHERE id_proc = ?";
		$sql = $this->db->query($sql, $id_lelang);
		$sql = $sql->row_array();
		
		return $sql['description'];
	}

	function get_kurs($id_lelang = ''){
		$sql = "SELECT b.* 
				FROM tb_kurs b 
				LEFT JOIN ms_procurement_kurs a ON a.id_kurs = b.id 
				WHERE id_procurement = ?";
		
		return $this->db->query($sql, $id_lelang);
	}
	
	function get_penawaran($id_lelang = ''){
		$sql = "SELECT * FROM ms_penawaran WHERE id_procurement = ? AND id_vendor = ?";
		return $this->db->query($sql, array($id_lelang, $this->session->userdata('user')['id_user']));
	}
	
	function get_barang($id_lelang = ''){
		$sql = "SELECT a.*,
					   b.symbol,
					   b.name AS kurs 
					   
					   FROM ms_procurement_barang a 
					   LEFT JOIN tb_kurs b ON a.id_kurs = b.id 
					   
					   WHERE a.id_procurement = ?";
		return $this->db->query($sql, $id_lelang);
	}
	
	function get_nama_barang($id = ''){
		$sql = "SELECT * FROM ms_procurement_barang WHERE id = ?";
		$sql = $this->db->query($sql, $id);
		$sql = $sql->row_array();
		
		return $sql['nama_barang'];
	}
	
	function cek_rate_info($id_lelang = '', $id_kurs = ''){
		$sql = "SELECT * FROM ms_procurement_kurs WHERE id_kurs = ? AND id_procurement = ?";
		$sql = $this->db->query($sql, array($id_kurs, $id_lelang));
		
		return $sql->row_array();
	}
	
	function cek_hps($id_lelang = '', $id_barang = ''){
		$sql = "SELECT a.nilai_hps, 
					   a.id_kurs,
					   (SELECT rate FROM ms_procurement_kurs WHERE id_kurs = a.id_kurs AND id_procurement = a.id_procurement) as rate 
					   
					   FROM ms_procurement_barang a 
					   WHERE a.id_procurement = ?"; 
		
		if($id_barang) $sql .= " AND a.id = ? ";
		
		$sql = $this->db->query($sql, array($id_lelang, $id_barang));
		$sql = $sql->row_array();
		// echo $this->db->last_query();
		return $sql;
	}
	
	function cek_percentage($id_lelang = '', $id_barang = '', $nilai = '', $id_before = ''){
		$percent = 0;
		$input = array($id_lelang, $id_barang, $this->session->userdata('user')['id_user']);
		$sql = "SELECT in_rate AS nilai, id_kurs FROM ms_penawaran WHERE id_procurement = ? AND id_barang = ? AND id_vendor = ? ";
		
		if($id_before) {

			$sql .= " AND id < ? ";
			$input[] = $id_before;

		}

		$sql .= " ORDER BY id DESC LIMIT 0,1";
		
		$sql = $this->db->query($sql, $input);
		$sql = $sql->row_array();
		
		// if($sql['nilai'] / 100 > 0)
			$percent = number_format(($sql['nilai'] - $nilai) / ($sql['nilai'] / 100), 1);

		$data = $this->get_data($id_lelang);
		$percent = ($data['auction_type']=='forward_auction') ? abs($percent) : $percent;
		return $percent.'%';
	}
	
	function cek_lowest($id_lelang = '', $id_barang = ''){
		$sql = "SELECT MIN(in_rate) AS nilai FROM ms_penawaran WHERE id_procurement = ? AND id_barang = ? AND id_vendor = ?";
		$sql = $this->db->query($sql, array($id_lelang, $id_barang, $this->session->userdata('user')['id_user']));
		$sql = $sql->row_array();
		
		return $sql;
	}
	
	function cek_highest($id_lelang = '', $id_barang = ''){
		$sql = "SELECT MAX(in_rate) AS nilai FROM ms_penawaran WHERE id_procurement = ? AND id_barang = ? AND id_vendor = ?";
		$sql = $this->db->query($sql, array($id_lelang, $id_barang, $this->session->userdata('user')['id_user']));
		$sql = $sql->row_array();
		
		return $sql;
	}
	
	function convert_to_idr($nilai = '', $id_kurs = '', $id_lelang = ''){
		$sql = "SELECT * FROM ms_procurement_kurs WHERE id_kurs = ? AND id_procurement= ?";
		$sql = $this->db->query($sql, array($id_kurs, $id_lelang));
		$sql = $sql->row_array();
		// print_r($sql);
		if($sql['id_kurs'] == 1) $sql['rate'] = 1;
		
		return ($nilai * $sql['rate']);
	}
	
	function cek_posisi_penawaran($id_lelang = '', $id_barang = '', $type = ''){
		$hps = $this->cek_hps($id_lelang, $id_barang);
		$hps = $this->convert_to_idr($hps['nilai_hps'], $hps['id_kurs'], $id_lelang);
				
		if($type == "forward_auction")		$ord = "DESC";
		else if($type == "reverse_auction")	$ord = "ASC";
		
		$sql = "SELECT in_rate FROM ms_penawaran WHERE id_procurement = ? AND id_barang = ? ORDER BY in_rate ".$ord." LIMIT 0,1";
		$sql = $this->db->query($sql, array($id_lelang, $id_barang));
	 	$sql = $sql->row_array();
	 		 	
	 	if($type == "forward_auction"){
	 		if($sql['in_rate'] < $hps) return true;
	 	}
		else if($type == "reverse_auction"){
	 		if($sql['in_rate'] > $hps) return true;
	 	}
	 	else 
	 		return false;
	}
	
	function get_highest($id_lelang = '', $id_barang = '', $id_user = ''){		
		$sql = "SELECT id 
					   FROM ms_penawaran 
					   WHERE 
					   		in_rate > (SELECT in_rate FROM ms_penawaran WHERE id_procurement = ? AND id_barang = ? AND id_vendor = ? ORDER BY nilai DESC LIMIT 0,1) AND  
					    	id_barang = ? 
					   
					   GROUP BY id_vendor ORDER BY nilai DESC";

		$sql = $this->db->query($sql, array($id_lelang, $id_barang, $id_user, $id_barang));
		$data = $sql->num_rows();
		$data++;
				
		return $data;
	}
		
	function get_lowest($id_lelang = '', $id_barang = '', $id_user = ''){
		$sql = "SELECT id 
					   FROM ms_penawaran 
					   WHERE 
					   		in_rate < (SELECT in_rate FROM ms_penawaran WHERE id_procurement = ? AND id_barang = ? AND id_vendor = ? ORDER BY nilai ASC LIMIT 0,1) AND  
					    	id_barang = ? 
					   
					   GROUP BY id_vendor ORDER BY nilai ASC";

		$sql = $this->db->query($sql, array($id_lelang, $id_barang, $id_user, $id_barang));
		$data = $sql->num_rows();
		$data++;
				
		return $data;
	}
	
	function get_default_currency($id_barang = ''){
		$sql = "SELECT b.id, 
					   b.symbol 
					   
				FROM ms_procurement_barang a
				LEFT JOIN tb_kurs b ON b.id = a.id_kurs
				
				WHERE a.id = ?";
				
		$sql = $this->db->query($sql, $id_barang);
		return $sql->row_array();
	}
	
	function get_user_currency($id_lelang = '', $position = '', $id_barang = ''){
		$sql = "SELECT a.id_kurs, 
					   b.symbol 
					   
				FROM ms_penawaran a
				-- LEFT JOIN ms_procurement_kurs c ON b.id = a.id_kurs
				LEFT JOIN tb_kurs b ON b.id = a.id_kurs
				
				WHERE a.id_procurement = ? AND a.id_vendor = ? AND a.id_barang = ? ORDER BY a.id ".$position." LIMIT 0,1";
				
		$sql = $this->db->query($sql, array($id_lelang,  $this->session->userdata('user')['id_user'], $id_barang));
		// echo $this->db->last_query();
		return $sql->row_array();
	}
	
	function get_last_offer($id_barang = '', $id_vendor = '', $id_lelang = ''){
		$sql = "SELECT in_rate AS nilai FROM ms_penawaran WHERE id_barang = ? AND id_vendor = ? AND id_procurement = ? ORDER BY id DESC LIMIT 0,1 ";
		$sql = $this->db->query($sql, array($id_barang, $this->session->userdata('user')['id_user'], $id_lelang));
		$sql = $sql->row_array();
		
		return $sql['nilai']; 
	}
	
	function save_penawaran($param = array()){
		$sql = "INSERT INTO ms_penawaran (`id_procurement`,
													  `id_vendor`,
													  `id_barang`,
													  `id_kurs`,
													  `nilai`,
													  `in_rate`,
													  `down_percent`,
													  `entry_stamp`) VALUES (?,?,?,?,?,?,?,?)";
		
		$this->db->query($sql, $param);
		
		return $param['nilai'];
		
	}
}