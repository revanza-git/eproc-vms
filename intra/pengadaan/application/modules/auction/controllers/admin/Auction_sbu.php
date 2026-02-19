<?php
class Auction_sbu extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		
		$this->load->model('auction_package/sbu_model');
	}
	
	function index($id_lelang = ''){
		$data['id_lelang'] = $id_lelang;
		$data['query'] = $this->sbu_model->get_data($id_lelang);

		$this->load->view('content/auction_package/auction_sbu', $data);
	}
	
	function form($id_lelang = '', $id = ''){
		$data['content'] = "form/auction_package/auction_sbu";
		$data['width'] = 700;
		
		$data['id_lelang'] = $id_lelang;
		$setting = array(
			'db' => 'tb_sbu_lokasi',
			'id' => 'id',
			'value' => 'name'
		);
		$data['id_sbu'] = $this->form->drop_down_db('id_sbu', '', $setting);
		
		
		$this->load->view("jc-table/form/jc-form", $data);
	}
	
	function save(){
		$param = array(
			'id_sbu' => $_POST['id_sbu'],
			'id_lelang' => $_POST['id_lelang'],
			'entry_stamp' => date("Y-m-d H:i:s")
		);
		$this->sbu_model->save($param);
		
		$json = array(
			'status' => 'success',
			'message' => 'Data telah di simpan !'
		);
		die(json_encode($json));
	}
	
	function delete($id = ''){
		$this->sbu_model->delete_data($id);
		
		$json = array(
			'status' => 'success',
			'message' => 'Data telah di hapus !'
		);
		die(json_encode($json));
	}
}