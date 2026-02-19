<?php
class Auction_kurs extends CI_Controller{

	function __construct(){
		parent::__construct();
		
		$this->load->model('auction_package/kurs_model');
	}
	
	function index($id_lelang = ''){
		$data['query'] = $this->kurs_model->get_data($id_lelang);
		$data['id_lelang'] = $id_lelang;
		$data['is_dynamic_rate'] = $this->kurs_model->get_rate_setting($id_lelang);
		
		$this->load->view('content/auction_package/auction_kurs', $data);
	}
	
	function form($id_lelang = '', $id = ''){
		$data['action'] = base_url()."index.php/auction_kurs/save/";
		if($id){ $fill = $this->kurs_model->select_data($id); $data['action'] = base_url()."index.php/auction_kurs/edit/"; }
		
		$setting = array(
			'db' => 'vm_pgn.tb_kurs',
			'id' => 'id',
			'value' => 'symbol'
		);
		$data['id_kurs'] = $this->form->drop_down_db('id_kurs', $fill['id_kurs'], $setting);
		
		$data['id_lelang'] = $id_lelang;
		$data['rate'] = $this->form->money('rate', $fill['rate']);
		$data['content'] = "form/auction_package/auction_kurs";
		$data['width'] = 500;
		
		$this->load->view('jc-table/form/jc-form', $data);
	}
	
	function user_rate_setting(){
		if(!$_POST['value']){ $_POST['value'] = 0; $message = 'User tidak diijinkan untuk mengubah mata uang selama auction berlangsung'; }
		else $message = 'User diijinkan untuk mengubah mata uang selama auction berlangsung';
		
		$this->kurs_model->user_rate_setting(array($_POST['value'], $_POST['id_lelang']));
		die(json_encode(array('status' => 'success', 'message' => $message)));
	}
	
	function save(){
		$param = array(
			'id_lelang' => $_POST['id_lelang'],
			'id_kurs' => $_POST['id_kurs'],
			'rate' => $_POST['rate'],
			'entry_stamp' => date("Y-m-d H:i:s")
		);
		$param['rate'] = str_replace(",", "", $param['rate']);
		
		$this->kurs_model->save_data($param);
		die(json_encode(array('status' => 'success', 'message' => 'data mata uang telah di simpan')));
	}
	
	function edit(){
		$param = array(
			'id_lelang' => $_POST['id_lelang'],
			'id_kurs' => $_POST['id_kurs'],
			'rate' => $_POST['rate'],
			'edit_stamp' => date("Y-m-d H:i:s"),
			'id' => $_POST
		);
		$param['rate'] = str_replace(",", "", $param['rate']);
		
		$this->kurs_model->save_data($param);
		die(json_encode(array('status' => 'success', 'message' => 'data mata uang telah di simpan')));
	}
	
	function delete($id = ''){
		$this->kurs_model->delete_data($id);
		die(json_encode(array('status' => 'success', 'message' => 'data mata uang telah di hapus')));
	}
}