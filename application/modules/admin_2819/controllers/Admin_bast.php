<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_bast extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('admin')){
			redirect(site_url());
		}
		$this->load->model('bast/Bast_model','bm');
		
	}

	public function index(){
		$data = $this->bm->get_bast_format();
		$vld 	= 	array(
						array(
							'field'=>'text',
							'label'=>'Format Surat',
							'rules'=>'required'
						),
					);
			$this->form_validation->set_rules($vld);

		if($this->input->post('update')){
			if($this->form_validation->run()==TRUE){
				$form['text'] 			= $_POST['text'];
				$form['entry_stamp'] 	= date('Y-m-d H:i:s');
				unset($_POST['update']);

				$result = $this->bm->edit_bast($form);
				if($result){
					$this->session->unset_userdata('form');
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah Format BAST!</p>');
					redirect(current_url());
				}
			}
		}	

		$layout['content']	= $this->load->view('bast/content',$data,TRUE);
		$layout['script']	= $this->load->view('bast/content_js',$data,TRUE);

		$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	
}