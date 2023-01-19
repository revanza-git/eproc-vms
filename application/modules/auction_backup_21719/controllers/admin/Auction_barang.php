<?php
class Auction_barang extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		
		$this->load->model('auction_package/barang_model');
	}
	
	function index($id_lelang = ''){
		$data['id_lelang'] = $id_lelang;
		$data['query'] = $this->barang_model->get_data($id_lelang);

		$this->load->view('content/auction_package/auction_barang', $data);
	}
	
	function form($id_lelang = '', $id = ''){
		
		$data['action'] = "save";
		if($id){
			$data['action'] = "edit";
			$fill = $this->barang_model->select_data($id);
		}
		
		$data['id_kurs'] = $this->drop_down_kurs($id_lelang, 'id_kurs', $fill['id_kurs']);
		
		if($fill['volume']) $volume = $fill['volume'];
		$volume = 1;
		
		$data['volume'] = $this->form->number('volume', $volume);
		$data['use_tax'] = $fill['use_tax'];
		
		$data['content'] = "form/auction_package/auction_barang";
		$data['width'] = 600;
		
		$data['id_lelang'] = $id_lelang;
		$data['id'] = $id;
		$data['name'] = $this->form->text_box('name', $fill['name'], array(50, 200));
		$data['hps'] = $this->form->money('hps', $fill['hps']);
		$data['kurs'] = $fill['kurs'];
		
		$this->load->view("jc-table/form/jc-form", $data);
	}
	
	function drop_down_kurs($id_lelang = '', $id = '', $selected = ''){
		$query = $this->barang_model->get_kurs($id_lelang);

		$return = '<select name="'.$id.'" id="'.$id.'">'; 
		foreach($query->result() as $data){
			$return .= '<option value="'.$data->id.'"';
			if($selected == $data->id) $return .= ' selected="selected"';
			$return .= '>'.$data->name.'</option>';
		}
		$return .= '</select>';
		
		return $return;
	}
	
	function save(){		 
		$param = array(
			'id_lelang' => $_POST['id_lelang'],
			'name' => $_POST['name'],
			'id_kurs' => $_POST['id_kurs'],
			'hps' => $_POST['hps'],
			'use_tax' => $_POST['use_tax'],
			'volume' => $_POST['volume'],
			'entry_stamp' => date("Y-m-d H:i:s")
		);
		$param['hps'] = str_replace(",", "", $param['hps']);
		
		$this->barang_model->save($param);
		
		$json = array(
			'status' => 'success',
			'message' => 'Data barang/jasa auction telah di simpan !'
		);
		die(json_encode($json));
	}

	function edit(){
		$param = array(
			'name' => $_POST['name'],
			'id_kurs' => $_POST['id_kurs'],
			'hps' => $_POST['hps'],
			'use_tax' => $_POST['use_tax'],
			'volume' => $_POST['volume'],
			'edit_stamp' => date("Y-m-d H:i:s"),
			'id' => $_POST['id']
		);
		$param['hps'] = str_replace(",", "", $param['hps']);
		
		$this->barang_model->edit($param);
		
		$json = array(
			'status' => 'success',
			'message' => 'Data barang/jasa auction telah di simpan !'
		);
		die(json_encode($json));
	}
		
	function delete($id = ''){
		$this->barang_model->delete_data($id);
		
		$json = array(
			'status' => 'success',
			'message' => 'Data barang auction telah di hapus !'
		);
		die(json_encode($json));
	}
}