<?php
class Auction_report_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
	}
	
	function get_header($id_lelang = ''){
		$sql = "SELECT a.*,
					   b.metode_penawaran,
					   b.metode_auction,
					   c.name budget_spender,
					   d.name AS nama_pejabat,
					   e.name budget_holder
					   -- e.name AS nama_lokasi,
					   -- f.symbol AS kurs
					   
				FROM ms_procurement a 
				
				LEFT JOIN ms_procurement_tatacara b ON a.id = b.id_procurement 
				LEFT JOIN tb_budget_spender c ON a.budget_spender = c.id
				LEFT JOIN tb_pejabat_pengadaan d ON a.id_pejabat_pengadaan = d.id
				LEFT JOIN tb_budget_holder e ON a.budget_holder = e.id
				-- LEFT JOIN ms_area_sbu e ON a.id_lokasi = e.id
				-- LEFT JOIN tb_kurs f ON b.id_kurs = f.id
				
				WHERE a.id = ? ";
		
		$sql = $this->db->query($sql, $id_lelang);
		return $sql->row_array();
	}
	
	function get_pengguna($id_lelang = ''){
		$sql = "SELECT b.name FROM ms_sbu_lelang a LEFT JOIN tb_sbu_lokasi b ON a.id_sbu = b.id WHERE a.id_lelang = ?";
		return $this->db->query($sql, $id_lelang);
	}
	
	function get_barang($id_lelang = ''){
		$sql = "SELECT 	a.* ,
						b.symbol

				FROM ms_procurement_barang a 

				LEFT JOIN tb_kurs b ON a.id_kurs = b.id

				WHERE id_procurement = ?";
		return $sql = $this->db->query($sql, $id_lelang);
	}
	
	function get_kurs($id_lelang = ''){
		$sql = "SELECT b.name FROM ms_procurement_kurs a LEFT JOIN tb_kurs b ON a.id_kurs = b.id WHERE a.id_procurement = ?";
		return $sql = $this->db->query($sql, $id_lelang);
	}
	
	function get_peserta($id_lelang = ''){
		$sql = "SELECT a.*,
					   b.name,
					   d.name legal
					   
				FROM ms_procurement_peserta a
				LEFT JOIN ms_vendor b ON a.id_vendor = b.id 
				LEFT JOIN ms_vendor_admistrasi c ON a.id_vendor = c.id_vendor 
				LEFT JOIN tb_legal d ON c.id_legal = d.id 
				
				WHERE id_proc = ?";
		
		return $sql = $this->db->query($sql, $id_lelang);
	}
	
	function get_count_penawaran($id_barang = ""){
		$sql = "SELECT COUNT(id) FROM ms_penawaran WHERE id_barang = ? GROUP BY id_vendor";	
		$sql = $this->db->query($sql, $id_barang);
		$sql = $sql->num_rows();
		
		return $sql;
	}
	
	function get_vendor_ranking($id_lelang = '', $id_barang = '', $type_lelang = ''){
		$ord = '';
		if($type_lelang == "forward_auction"){ $sel = "MAX"; $ord = "DESC"; }
		else if($type_lelang == "reverse_auction"){ $sel = "MIN"; $ord = "ASC"; }
		
		$sql = "SELECT a.id_vendor AS id_peserta, b.name AS nama_vendor,
					   (SELECT id FROM ms_penawaran WHERE id_vendor = a.id_vendor AND id_barang = ? ORDER BY in_rate ".$ord." LIMIT 0,1) AS id_penawaran 
					   
				FROM ms_procurement_peserta a
				LEFT JOIN ms_vendor b ON a.id_vendor = b.id 
				
				WHERE a.id_proc = ? ORDER BY (SELECT ".$sel."(in_rate) FROM ms_penawaran WHERE id_vendor = a.id_vendor AND id_barang = ?) ".$ord.", (SELECT id FROM ms_penawaran WHERE id_vendor = a.id_vendor AND id_barang = ? ORDER BY in_rate ".$ord." LIMIT 0,1) ASC";
		
		return $this->db->query($sql, array($id_barang, $id_lelang, $id_barang, $id_barang));
	}
	
	function get_penawaran($id = ''){
		$sql = "SELECT a.*,
					   b.nama_barang, 
					   c.symbol
					   
					   FROM ms_penawaran a 
					   LEFT JOIN ms_procurement_barang b ON a.id_barang = b.id 
					   LEFT JOIN tb_kurs c ON a.id_kurs = c.id 
					   
					   WHERE a.id = ?";
		
		$sql = $this->db->query($sql, array($id));	
		
		return $sql->row_array();
	}
	
	function get_history($id_lelang = '', $id_vendor = ''){
		$arr = array($id_lelang);
		$sql = "SELECT a.*, 
					   b.nama_barang AS nama_barang,
					   c.name AS nama_vendor,
					   d.symbol
						
				FROM ms_penawaran a
				
				LEFT JOIN ms_procurement_barang b ON a.id_barang = b.id
				LEFT JOIN ms_vendor c ON a.id_vendor = c.id 
				LEFT JOIN tb_kurs d ON a.id_kurs = d.id 
				
				WHERE a.id_procurement = ?"; 
		
		if($id_vendor) {$sql .= " AND a.id_vendor = ?";$arr[]=$id_vendor;}
		
		$sql .= " ORDER BY a.id ASC";
		
		return $this->db->query($sql, $arr);
	}
}