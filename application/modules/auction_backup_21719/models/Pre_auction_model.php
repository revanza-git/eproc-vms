<?php
class Pre_auction_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
	}
	
	function get_status($id_lelang = ''){
		$sql = "SELECT is_started, is_suspended, is_finished, time_limit FROM ms_procurement WHERE id = ?";
		$sql = $this->db->query($sql, $id_lelang);
		$sql = $sql->row_array();
		
		$time_limit = date("M j, Y H:i:s O", strtotime($sql['time_limit']));
		
		return array('is_started' => $sql['is_started'], 'is_finished' => $sql['is_finished'], 'is_suspended' => $sql['is_suspended'], 'time' => $time_limit);
	}
	
	function select_data($id_lelang = ''){
		$sql = "SELECT a.*,
					   (SELECT GROUP_CONCAT(symbol) FROM tb_kurs WHERE id IN (SELECT id_kurs FROM ms_procurement_kurs WHERE id_procurement = a.id)) AS rate, 
					   b.metode_auction,
					   b.hps, 
					   b.metode_penawaran 
					   
				FROM ms_procurement a 
				LEFT JOIN ms_procurement_tatacara b ON a.id = b.id_procurement 
				
				WHERE a.id = ?";
		
		$sql = $this->db->query($sql, $id_lelang);
		
		return $sql->row_array();
	}
	
	function start_auction($id_lelang = ''){
		$duration = $this->select_data($id_lelang);
		$duration = $duration['auction_duration'];
		 
		$time_limit = strtotime(date("Y-m-d H:i:s")." + ".$duration." minutes");
		$time_limit = date("Y-m-d H:i:s", $time_limit);
		
		$sql = "UPDATE ms_procurement SET is_started = ?, is_finished = ?, start_time = ?, time_limit = ?  WHERE id = ?";
		$this->db->query($sql, array(1, 0, date("Y-m-d H:i:s"), $time_limit, $id_lelang));
		
		return $time_limit;
	}
	
	function extend_lelang($id_lelang = ''){
		$time = ($this->input->post('time')!='') ? $this->input->post('time') : 1;
		$time_limit = strtotime(date("Y-m-d H:i:s")." + ".$time." minutes");
		$time_limit = date("Y-m-d H:i:s", $time_limit);
		
		$sql = "UPDATE ms_procurement SET is_started = ?, time_limit = ?, is_suspended = ?  WHERE id = ?";
		$this->db->query($sql, array(1, $time_limit, 0, $id_lelang));
		
		return $time_limit;
	}
	
	function get_barang($id_lelang = ''){
		$sql = "SELECT * FROM ms_procurement_barang WHERE id_procurement = ?";
		$res =  $this->db->query($sql, $id_lelang);
		
		return $res;
	}
	
	function initial_graph($id_lelang = "", $id_barang = ''){
		$start = $this->count_penawaran($id_lelang, $id_barang);
		$start -= 10;
		
		if($start < 0) $start = 0;
		
		$fill = $this->select_data($id_lelang);
		
		if($fill['type_lelang'] == "forward_auction")		$order = "ASC";
		else if($fill['type_lelang'] == "reverse_auction")	$order = "DESC";
		
		$sql = "SELECT a.*, b.nama FROM ms_penawaran a LEFT JOIN ms_vendor b ON a.id_vendor = b.id WHERE a.id_procurement = ? AND a.id_barang = ? ORDER BY a.entry_stamp ".$order." LIMIT ".$start.", 10";
		return $this->db->query($sql, array($id_lelang, $id_barang));		
	}
	
	function count_penawaran($id_lelang = "", $id_barang = ""){
		$sql = "SELECT id FROM ms_penawaran WHERE id_procurement = ? AND id_barang = ?";
		$sql = $this->db->query($sql, array($id_lelang, $id_barang));
		
		return $sql->num_rows();
	}
	
	function convert_to_idr($nilai = '', $id_kurs = '', $id_lelang = ''){
		$sql = "SELECT * FROM ms_procurement_kurs WHERE id_kurs = ? AND id_procurement = ?";
		$sql = $this->db->query($sql, array($id_kurs, $id_lelang));
		$sql = $sql->row_array();
		
		if($sql['id_kurs'] == 1) $sql['rate'] = 1;
		
		return ($nilai * $sql['rate']);
	}
	
	function get_lowest_price($id_lelang = '', $id_barang = ''){
		
		$sql = "SELECT MIN(a.in_rate) AS nilai,
					   b.name
				
				FROM ms_penawaran a
				LEFT JOIN ms_vendor b ON b.id = a.id_vendor 
				 
				WHERE a.id_procurement = ? AND a.id_barang = ? LIMIT 10";
		 	
		$sql = $this->db->query($sql, array($id_lelang, $id_barang));
		$sql = $sql->row_array();
		
		return $sql;
	}
	
	function get_highest_price($id_lelang = '', $id_barang = ''){
		$sql = "SELECT MAX(a.in_rate) AS nilai,
					   b.name AS name
				
				FROM ms_penawaran a
				LEFT JOIN ms_vendor b ON b.id = a.id_vendor 
				 
				WHERE a.id_procurement = ? AND a.id_barang = ? LIMIT 10";
		
		$sql = $this->db->query($sql, array($id_lelang, $id_barang));
		$sql = $sql->row_array();
		
		return $sql;
	}
	
	function force_stop($id_lelang = ''){
		$sql = "UPDATE ms_procurement SET is_started = ?, is_finished = ?, is_suspended = ?, is_fail_auction = ?, end_hour = ?, time_limit = ?  WHERE id = ?";
		$this->db->query($sql, array(1, 1, 0, 1, date("H:i:s"), date("Y-m-d H:i:s"), $id_lelang));
	}
	
	function end_auction($id_lelang = ''){
		$sql = "UPDATE ms_procurement SET is_finished = ?, end_hour = ?, time_limit = ? WHERE id = ?";
		$this->db->query($sql, array(1, date("H:i:s"), date("Y-m-d H:i:s"), $id_lelang));

	}
	
	function cek_hps($id_lelang = ''){
		$sql = "SELECT hps FROM ms_procurement_tatacara WHERE id_lelang = ?";
		
		$sql = $this->db->query($sql, array($id_lelang));
		$sql = $sql->row_array();
		
		return $sql['hps'];
	}
	
	function mark_as_extend($id_lelang = ''){
		$sql = "UPDATE ms_procurement SET is_started = ?, 
										   time_limit = ?, 
										   is_finished = ?, 
										   is_suspended = ?
											  
										   WHERE id = ? ";
		
		$this->db->query($sql, array(0, null, 0, 1, $id_lelang));
	}
	function generate_catalogue($id_lelang){

		$this->load->model('auction_report_model');
		$fill = $this->auction_report_model->get_header($id_lelang);
		$_barang = $this->auction_report_model->get_barang($id_lelang);
		


		foreach($_barang->result() as $data){
			if($data->is_catalogue==1) {
				if($data->id_material==0){
					$this->db->insert('ms_material',array('id_barang'=>$data->id,'nama'=>$data->nama_barang,'entry_stamp'=>date('Y-m-d H-i-s')));
					$id_material = $this->db->insert_id();
				}else{
					$id_material = $data->id_material;	
				}
				
			
				$_peserta = $this->auction_report_model->get_vendor_ranking($id_lelang, $data->id, $fill['auction_type']);
				
				$is_first = true;

				$_result_peserta = $_peserta->result_array();
				
				$id_penawaran = 0;
				foreach($_result_peserta as $key => $row){
					if($row['id_penawaran']!=NULL){
						$id_penawaran = $row['id_penawaran'];
						break;
					}
				}

				if($id_penawaran!=0){
					$penawaran = $this->auction_report_model->get_penawaran($id_penawaran);
					
					$this->db->insert('tr_material_price',array(
																'id_material'	=>$id_material,
																'id_procurement'=>$id_lelang,
																'id_vendor'		=>$_result_peserta[0]['id_peserta'],
																'price'			=>($penawaran['in_rate']/*(preg_replace("/[,]/", "", $in_rate))*/ / $data->volume),
																'date'			=>date('Y-m-d'),
																'entry_stamp'	=>date('Y-m-d H-i-s')
															)
								);	
				}
				
			}
		}

	}

	function vendor_experience($id_procurement){
   		$this->load->model('Auction_report_model','aum');


   		$data['proc'] 		= $this->db->select('ms_procurement.*, ms_contract.id_vendor')
					   			   		->where('ms_procurement.id',$id_procurement)
					   			   		->join('ms_contract', 'ms_contract.id_procurement = ms_procurement.id', 'LEFT')
								   		->get('ms_procurement')->row_array();

   		$data['kurs'] 		= $this->db->select('CONCAT(symbol) symbol')
					   			   		->where('tb_kurs.id',$data['proc']['id_kurs'])
								   		->get('tb_kurs')->row_array();

		$data['barang'] 	= $this->get_barang($id_procurement)->result_array();
		// print_r($data['barang']);
		foreach ($data['barang'] as $key => $value) {
			$vendor_ranking	= $this->get_vendor_ranking($id_procurement, $value['id'], $data['proc']['auction_type']);
			
			foreach($vendor_ranking as $ranking){
				if($ranking['id_penawaran']==NULL){
					continue;
				}
				$vendor_penawaran = 	$this->aum->get_penawaran($ranking['id_penawaran']);
				
				if($vendor_penawaran['id_kurs']==1){
					$vendor_data[$ranking['id_peserta']]['price_idr'] += $vendor_penawaran['nilai'];
				}else{
					$vendor_data[$ranking['id_peserta']]['price_foreign'] += $vendor_penawaran['nilai'];
				}
				$vendor_data[$ranking['id_peserta']]['currency'][] = $vendor_penawaran['symbol'];
			}			
		}
		
		foreach ($vendor_data as $key_vendor => $value_vendor) {
			$exp = array(
					'id_vendor'			=> $key_vendor,
					'job_name' 			=> $data['proc']['name'],
					'job_giver'			=> "PT. Nusantara Regas",
					// 'contract_no'		=> $data['no_contract'],
					// 'contract_start'	=> $data['start_contract'],
					// 'contract_end'		=> $data['end_contract'],
					'price_idr'			=> $value_vendor['price_idr'],
					'price_foreign'		=> $value_vendor['price_foreign'],
					'currency'			=> implode(',', $value_vendor['currency']),
					'entry_stamp'		=> $data['proc']['entry_stamp'],
					'data_status'		=> 2,
					);
			$this->db->insert('ms_pengalaman',$exp);

		}

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
		
		return $this->db->query($sql, array($id_barang, $id_lelang, $id_barang, $id_barang))->result_array();
	}
}