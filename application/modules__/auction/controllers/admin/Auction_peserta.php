<?php
class Auction_peserta extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		
		$this->load->model('auction_package/peserta_model');
	}
	
	function index($id_lelang = ''){
		$data['setting'] = array(
			array('header' => 'Nama Penyedia Barang/Jasa','field' => 'nama','type' => 'text'),
		);
		
		$data['id_lelang'] = $id_lelang;
		
		$data['query'] = $this->peserta_model->get_data($id_lelang);
		$this->load->view('content/auction_package/auction_peserta', $data);
	}
	
	function form($id_lelang = '', $id = ''){
		$data['content'] = "form/auction_package/auction_peserta";
		$data['width'] = "600";
		
		$data['id_lelang'] = $id_lelang;
				
		$this->load->view("jc-table/form/jc-form", $data);
	}
	
	function save(){
		$param = array(
			'id_vendor' => $_POST['id_vendor'],
			'id_lelang' => $_POST['id_lelang'],
			'is_non_vms' => $_POST['is_non_vms'],
			'entry_stamp' => date("Y-m-d H:i:s")
		);

		$this->peserta_model->save($param);
		
		$json = array(
			'status' => 'success',
			'message' => 'Data peserta auction telah di simpan !'
		);
		die(json_encode($json));
	}
	
	function delete($id = ''){
		$this->peserta_model->delete_data($id);
		
		$json = array(
			'status' => 'success',
			'message' => 'Data peserta auction telah di hapus !'
		);
		die(json_encode($json));
	}
}