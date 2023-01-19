<?php
class Auction_progress extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		
		$this->load->model('auction_model','am');
		$this->load->model('pre_auction_model');
		$this->load->model('auction/vendor_dash_model','vdm');
	}
	
	function checker($id_lelang = ''){
		$return = $this->pre_auction_model->get_status($id_lelang);
		die(json_encode($return));
	}
	
	function server_time(){
		$now = new DateTime(); 
		echo $now->format("M j, Y H:i:s O")."\n"; 
	}	
	
	function start($id_lelang){
		$return = $this->pre_auction_model->start_auction($id_lelang);
		die(json_encode(array('status' => 'success', 'time' => $return)));
	}
	
	function index($id_lelang = ''){
		$data['id_lelang'] = $id_lelang;
		
		$data['fill'] 	= $fill = $this->pre_auction_model->select_data($id_lelang);
		$data['barang'] = $this->pre_auction_model->get_barang($id_lelang); 
		$data['syarat'] = $this->vdm->get_syarat($id_lelang);

		if($fill['metode_auction'] == "reverse_auction") $data['symbol'] = '<'; else $data['symbol'] = '>';
		$layout['menu']			= $this->am->get_auction_list();
		$layout['content']		= $this->load->view('auction/admin/master',$data,TRUE);
		$layout['script']		= $this->load->view('auction/admin/master_js',$data,TRUE);
		$item['header'] 		= $this->load->view('auction/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 		= $this->load->view('dashboard',$layout,TRUE);
		$this->load->view('template', $item);
	}
	
	function price_graph($id_lelang = '', $id_barang = ''){
		$fill = $this->pre_auction_model->select_data($id_lelang);
		
		if($fill['auction_type'] == 'forward_auction')
			$return = $this->pre_auction_model->get_highest_price($id_lelang, $id_barang);
		if($fill['auction_type'] == 'reverse_auction')
			$return = $this->pre_auction_model->get_lowest_price($id_lelang, $id_barang);
		
		die(json_encode($return));
	}
	
	function initial_graph($id_lelang = '', $id_barang = ''){
		$fill = $this->pre_auction_model->select_data($id_lelang);
		$query = $this->pre_auction_model->initial_graph($id_lelang, $id_barang);
		$first = $query->row_array();
		
		$return = array();
		
		if($query->num_rows() < 10){
			$total = 10 - $query->num_rows();
			
			for($i=$total;$i>=1;$i--){
				$time = strtotime($first['entry_stamp']." - ".$i." seconds");
				$time = date("Y-m-d H:i:s", $time);
				
				array_push($return, array('x' => $time, 'y' => 0));
			}
		}
		
		foreach($query->result() as $data)
			array_push($return, array('x' => $data->entry_stamp, 'y' => $data->in_rate, 'id' => $data->id_barang, 'name' => $data->nama));
		
		die(json_encode($return));	
	}
	
	function force_stop($id_lelang = ''){
		$time = $this->pre_auction_model->force_stop($id_lelang);
		die(json_encode(array('status' => 'success', 'time' => $time)));
	}
	
	function extend_auction($id_lelang = ''){
		$time = $this->pre_auction_model->extend_lelang($id_lelang);
		die(json_encode(array('status' => 'success', 'time' => $time)));
	}
	
	function end_auction($id_lelang = ''){
		$return = array();
		$fill = $this->pre_auction_model->select_data($id_lelang);

		$query = $this->pre_auction_model->get_barang($id_lelang);
		foreach($query->result() as $data){
			
			$hps = $this->pre_auction_model->convert_to_idr($data->nilai_hps, $data->id_kurs, $id_lelang);
			
			if($fill['auction_type'] == 'reverse_auction'){
				$lowest = $this->pre_auction_model->get_lowest_price($id_lelang, $data->id);
				if($hps < $lowest['nilai']){
					$return['status'] = 'fail';
					$return['message'] = 'Semua penawaran masih diatas HPS. Pengadaan akan kembali di lanjutkan untuk 10 menit';
				}
			}
			if($fill['auction_type'] == 'forward_auction'){					
				$highest = $this->pre_auction_model->get_highest_price($id_lelang, $data->id);
				if($hps > $highest['nilai']){
					$return['status'] = 'fail';
					$return['message'] = 'Semua penawaran masih dibawah HPS. Pengadaan akan kembali di lanjutkan untuk 10 menit';
				}
			}
		}
		
		if(empty($return)){
			$this->pre_auction_model->vendor_experience($id_lelang);
			$this->pre_auction_model->end_auction($id_lelang);
			$this->pre_auction_model->generate_catalogue($id_lelang);
			$return['status'] = 'success';
		}
		else
			$this->pre_auction_model->mark_as_extend($id_lelang);

		die(json_encode($return));
	}
}