<?php
class Hpsoe_group extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		
		$this->load->model('hpsoe/hpsoe_group_model');
	}
	
	function form($id = ''){
		$data['content'] = "form/hpsoe/group";
		$data['width'] = 550;
		
		$data['index_from'] = $this->form->text_box('index_from', '', array(3,5));
		$data['index_to'] = $this->form->text_box('index_to', '', array(3,5));
		$data['name'] = $this->form->text_box('name', '', array(20,100));
		$data['color'] = $this->form->text_box('color', '', array(6,6));
		$data['images'] = $this->form->file('images', '');
		$data['remark'] = $this->form->text_area('remark', '', array(30,3));
		
		$this->load->view("jc-table/form/jc-form", $data);
	}
	
	function save(){
		$this->do_upload('images', 'lampiran_hpsoe');
		
		$param = array(
			'name' => $_POST['name'],
			'images' => $_POST['images'], 
			'index_from' => $_POST['index_from'], 
			'index_to' => $_POST['index_to'],
			'remark' => $_POST['remark'],
			'entry_stamp' => date("Y-m-d H:i:s")
		);
		$this->hpsoe_group_model->save($param);
		
		$json = array(
			'status' => 'success',
			'message' => 'group telah di simpan !'
		);
		die(json_encode($json));
	}
	
	function do_upload($name = '', $db_name = ''){	
		$file_name = $_FILES[$name]['name'] = $name.'_'.$this->utility->name_generator($_FILES[$name]['name']);
		
		$config['upload_path'] = '../lampiran/'.$db_name.'/';
		$config['allowed_types'] = 'pdf|jpeg|jpg|png|gif';
		$config['max_size'] = '2096';
		
		$this->load->library('upload');
		$this->upload->initialize($config);
		
		$this->upload->display_errors('','');
		 
		if(!$this->upload->do_upload($name)){
			$json = array(
				'status' => 'error',	
				'message' => $this->upload->display_errors()
			);
			die(json_encode($json));
		}
		
		$_POST[$name] = $file_name;
		return false;
	}
}