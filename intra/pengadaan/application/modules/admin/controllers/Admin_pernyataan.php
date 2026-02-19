<?php defined('BASEPATH') OR exit('No direct script access allowed');

class admin_pernyataan extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('admin')){
			redirect(site_url());
		}
		$this->load->model('pernyataan/pernyataan_model','pm');
		
	}
	/*public function index(){
		
		$data['pernyataan']	=$this->pm->get_pernyataan_list();

		$layout['content']	= $this->load->view('pernyataan/content',$data,TRUE);
		$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}*/

	public function index(){
		$check = $this->pm->get_pernyataan_list();
		// echo print_r($check);
		if (!empty($check)){
			$form 	= ($this->session->userdata('form'))?$this->session->userdata('form'):array();
			$fill 	= $this->securities->clean_input($_POST,'save');
			$item 	= $vld = $save_data = array();
			$admin 	= $this->session->userdata('admin');
			$vld 	= 	array(
							array(
								'field'=>'value',
								'label'=>'value',
								'rules'=>'required'
							),
						);
				$this->form_validation->set_rules($vld);

			if($this->input->post('update')){
				if($this->form_validation->run()==TRUE){
					$id 					= $_POST['id'];
					$form['value'] 			= $_POST['value'];
					$form['edit_stamp'] 	= date('Y-m-d H:i:s');
					unset($_POST['update']);

					$result = $this->pm->edit_pernyataan($id, $form);;
					if($result){
						$this->session->unset_userdata('form');
						$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah pernyataan!</p>');
						// redirect(site_url('auction/view/'.$id.'/pernyataan'));
					}
				}else{
					echo validation_errors();
				}
			}	
				
				$data['pernyataan'] 				= $this->pm->get_pernyataan_list();

				$layout['content']	= $this->load->view('pernyataan/content',$data,TRUE);
				$layout['script']	= $this->load->view('pernyataan/content_js',$data,TRUE);

				$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
				$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
				
			return $this->load->view('template',$item);
		}else{
			 redirect(site_url('admin_pernyataan/tambah_pernyataan/'));
		}
		return $this->load->view('pernyataan/tambah_pernyataan',$data,TRUE);

		$layout['content']	= $this->load->view('pernyataan/content',$data,TRUE);

		$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function tambah_pernyataan(){
		$check = $this->pm->get_pernyataan();
		// echo print_r($check);
		if (empty($check)){
			$form 	= ($this->session->userdata('form'))?$this->session->userdata('form'):array();
			$fill 	= $this->securities->clean_input($_POST,'save');
			$item 	= $vld = $save_data = array();
			$admin 	= $this->session->userdata('admin');
			$vld 	= 	array(
						array(
							'field'=>'value',
							'label'=>'value',
							'rules'=>'required'
						),
					);
			$this->form_validation->set_rules($vld);

			if($this->input->post('simpan')){
				if($this->form_validation->run()==TRUE){
					$_POST['value'] 		= $this->input->post('value');
					$_POST['entry_stamp'] 	= date('Y-m-d H:i:s');

					$result = $this->pm->save_pernyataan($this->input->post());
					if($result){
						$this->session->unset_userdata('form');
						$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah pernyataan!</p>');
						redirect(site_url('admin_pernyataan/'));
					}
				}else{
					echo validation_errors();
				}
			}

				$data['pernyataan'] 				= $this->pm->get_pernyataan_list();

				$layout['content']	= $this->load->view('pernyataan/tambah_pernyataan',$data,TRUE);

				$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
				$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
				
			return $this->load->view('template',$item);
		}else{
			redirect(site_url('admin_pernyataan/'));
		}
	}

}