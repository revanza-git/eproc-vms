<?php
class json_provider_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
	}
	
	function get_barang($id_lelang = ''){
		$sql = "SELECT * FROM ms_procurement_barang WHERE id_procurement = ? ORDER BY id ASC"; 
		return $this->db->query($sql, $id_lelang);
	} 
	
	function select_barang($id_barang = ''){
		$sql = "SELECT a.nama_barang, a.nilai_hps, b.symbol, a.id_kurs FROM ms_procurement_barang a LEFT JOIN tb_kurs b ON a.id_kurs = b.id WHERE a.id = ?";
		$sql = $this->db->query($sql, $id_barang);

		return $sql->row_array();
	}
	
	function select_lelang($id_lelang = ''){
		$sql = "SELECT * FROM ms_procurement WHERE id = ?";
		$sql = $this->db->query($sql, $id_lelang);

		return $sql->row_array();
	}
	
	function get_peserta($id_lelang = ''){
		$sql = "SELECT a.id_vendor id_vendor, 
					   b.name AS name
				
				FROM ms_procurement_peserta a 
				LEFT JOIN ms_vendor b ON a.id_vendor = b.id 
				WHERE a.id_proc = ? ORDER BY a.id ASC"; 
		
		return $this->db->query($sql, $id_lelang);
	}
	
	function get_total_penawaran($id_barang = '', $id_vendor = ''){
		$sql = "SELECT COUNT(id) AS nilai FROM ms_penawaran WHERE id_barang = ? AND id_vendor = ?";	
		$sql = $this->db->query($sql, array($id_barang, $id_vendor));
		$sql = $sql->row_array(); 
		
		return $sql['nilai'];
	}
	
	function get_initial_data($id_lelang = '', $id_barang = '', $is_update = false){
		$peserta = $this->get_peserta($id_lelang);
		$return = array();
		
		foreach($peserta->result() as $_peserta){
			$_return = array();
			
			$sql = "SELECT in_rate AS nilai, entry_stamp FROM ms_penawaran WHERE id_barang = ? AND id_vendor = ? ORDER BY entry_stamp "; 
			if($is_update) $sql .= " DESC LIMIT 0,1";
			else $sql .= " ASC";
			
			$sql = $this->db->query($sql, array($id_barang, $_peserta->id_vendor));
			// echo $this->db->last_query();
			foreach($sql->result() as $data) 
				array_push($_return, array('x' => $data->entry_stamp, 'y' => $data->nilai));
				
			array_push($return, array('name' => $_peserta->name, 'data' => $_return));
		}
		
		return $return;
	}
	
	function get_chart_update($id_lelang = ''){
		$sql = "SELECT id FROM ms_procurement_barang WHERE id_procurement = ?";
		$sql = $this->db->query($sql, $id_lelang);
		$return = array();
		
		foreach($sql->result() as $data)
			array_push($return, array('id' => $data->id, 'data' => $this->get_initial_data($id_lelang, $data->id, true)));
		
		return $return;
	}
	
	function cek_hps($id_lelang = '', $id_barang = ''){
		$sql = "SELECT a.hps, 
					   a.id_kurs,
					   (SELECT rate FROM ms_procurement_barang WHERE id_kurs = a.id_kurs AND id_procurement = a.id_procurement) as rate 
					   
					   FROM ms_barang_lelang a 
					   WHERE a.id_procurement = ?"; 
		
		if($id_barang) $sql .= " AND a.id = ? ";
		
		$sql = $this->db->query($sql, array($id_lelang, $id_barang));
		$sql = $sql->row_array();
		
		return $sql;
	}
	
	function convert_to_idr($nilai = '', $id_kurs = '', $id_lelang = ''){
		$sql = "SELECT * 

					FROM ms_procurement_barang a

					JOIN ms_procurement_kurs b

					ON b.id_procurement = a.id_procurement

					WHERE a.id_kurs = ? 
					AND a.id_procurement = ? 


		";
		$sql = $this->db->query($sql, array($id_kurs, $id_lelang));
		$sql = $sql->row_array();
		
		if($sql['id_kurs'] == 1) $sql['rate'] = 1;
		
		return ($nilai * $sql['rate']);
	}
	
	function get_same_offers($id_barang = '', $value = '', $id_vendor = ''){
		$ord = '';
		$is_latest = $this->get_latest_pos($id_barang, $ord);
		
		$sql = "SELECT a.in_rate
						   
					   FROM ms_penawaran a
					   
					   WHERE a.id_barang = ? 
					   AND a.in_rate = ?
					   AND a.id_vendor <> ?";
		
		$sql = $this->db->query($sql, array($id_barang, $value, $id_vendor));
		$sql = $sql->num_rows();
		
		if(($is_latest == $value) and $sql) return 1;
		else return 0;
	}
	
	function get_latest_pos($id_barang = '', $ord = ''){
		$sql = "SELECT in_rate FROM ms_penawaran WHERE id_barang = ? ORDER BY in_rate ".$ord;
		$sql = $this->db->query($sql, $id_barang);
		$sql = $sql->row_array();
		
		return $sql['in_rate'];
	}
	
	function get_list($symbol = '', $ord = '', $id_barang = '', $id_vendor = '', $is_latest = false, $is_same = false){

		$arr = array();
		$sql = "SELECT a.id,
					   a.in_rate
						   
					   FROM ms_penawaran a
					   WHERE a.id_barang = ? ";
		$arr[] = $id_barang;
		if(!$is_latest){ 
			$sql .= "AND(a.in_rate ".$symbol."= (SELECT in_rate FROM ms_penawaran WHERE id_barang = a.id_barang AND id_vendor = ? ORDER BY in_rate ".$ord." LIMIT 0,1)) "; 
			$arr[] = $id_vendor;
			if($is_same){
				$sql .= " AND (a.entry_stamp < (SELECT entry_stamp FROM ms_penawaran WHERE id_barang = a.id_barang AND id_vendor = ? ORDER BY in_rate ".$ord." LIMIT 0,1)) ";
				$arr[] = $id_vendor;
			}
			$sql .= " GROUP BY id_vendor ";
		}
		else {
			$sql .= ' AND a.id_vendor = ?';
			$arr[] = $id_vendor;
		}
			
		$sql .= " ORDER BY in_rate ".$ord."";
		if($is_latest) $sql .= " LIMIT 0,1";

		$res = $this->db->query($sql, $arr);
		// print_r($res)

			
		return $res;
	}
	
	function get_rank($id_lelang = '', $id_user = ''){	
		$query = $this->get_barang($id_lelang);
		$fill = $this->select_lelang($id_lelang);
		$return = array();
						
		if($fill['auction_type'] == "forward_auction"){ $ord = "DESC"; $symbol = ">"; } 
		else if($fill['auction_type'] == "reverse_auction"){ $ord = "ASC"; $symbol = "<"; } 
		
		foreach($query->result() as $data){
			$rank = 1;
			
			$latest = $this->get_list($symbol, $ord, $data->id, $id_user, true);
			
			$latest = $latest->row_array();

			$latest = $latest['in_rate'];

			$hps = $this->convert_to_idr($data->nilai_hps, $data->id_kurs, $id_lelang);
			
			if($fill['auction_type'] == "forward_auction")		{ if($hps > $latest) $rank = 0; } 
			else if($fill['auction_type'] == "reverse_auction")	{ if($hps < $latest) $rank = 0; } 
						
			if($rank){
				$is_same = $this->get_same_offers($data->id, $latest, $id_user);
				$rank = $this->get_list($symbol, $ord, $data->id, $id_user, false, $is_same);

				$rank = $rank->num_rows();
				
				if($is_same) $rank++;
			}
		
			array_push($return, array('id' => $data->id, 'rank' => $rank));
		}
		
		return $return;
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
	
	function cek_posisi_penawaran($id_lelang = '', $id_barang = '', $type = ''){
		$hps = $this->cek_hps($id_lelang, $id_barang);
		$hps = $this->convert_to_idr($hps['hps'], $hps['id_kurs'], $id_lelang);
				
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
}