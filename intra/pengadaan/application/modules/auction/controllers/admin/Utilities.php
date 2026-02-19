<?php
class Utilities extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		
		$this->load->model('util_model');
	}
	
	function get_vendor($id = '', $type = ''){
		if($type == "lelang") $query = $this->util_model->get_vendor($_POST['query']);
	
		$return = array('response' => 'true', 'message' => array());
		foreach($query->result() as $data)
			array_push($return['message'], array('nama' => $data->nama,  'id' => $data->id, 'is_non_vms' => $data->is_non_vms));
		
		echo json_encode($return);
	}
	
	function get_komag(){
		$query = $this->util_model->get_komag($_POST['query']);
		
		$return = array('response' => "true", 'message' => array());
		foreach($query->result() as $data)
			array_push($return['message'], array('komag' => $data->komag, 'nama' => $data->name,  'id' => $data->id));
		
		echo json_encode($return);
	}
	
	function get_uom(){
		$data = $this->util_model->get_uom($_POST['query']);
		
		$return['nama'] = $data['name'];
		$return['id'] = $data['id'];
				
		echo json_encode($return);
	}
}