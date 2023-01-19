<?php defined('BASEPATH') OR exit('No direct script access allowed');

class admin_badan_hukum extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('admin')){
			redirect(site_url());
		}
		$this->load->model('vendor/Vendor_model','vm');
		
	}
	public function get_field(){
		return array(
			array(
				'label'	=>	'Badan Hukum',
				'filter'=>	array(
								array('table'=>'tb_legal|name' ,'type'=>'text','label'=> 'Nama Badan Hukum'),
							)
			),
			
		);
	}
	public function index(){	
		$this->load->library('form');
		$this->load->library('datatables');

		$data['filter_list'] = $this->filter->group_filter_post($this->get_field());

		$search 	= $this->input->get('q');
		$page 		= '';
		$per_page	= 10;
		$sort 		= $this->utility->generateSort(array('name'));

		$data['hukum']		= $this->vm->get_badan_hukum_list($search, $sort, $page, $per_page,TRUE);
		$data['pagination'] = $this->utility->generate_page('admin/admin_badan_hukum/',$sort, $per_page, $this->vm->get_badan_hukum_list($search, $sort, '','',FALSE));
		$data['sort'] 		= $sort;
		$layout['content']	= $this->load->view('badan_hukum/content',$data,TRUE);

		$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
		// echo print_r($this->db->last_query());
	}
	//#####################################################
	//################  	Badan Hukum		 ##############
	//#####################################################
	public function tambah_badan_hukum(){
		$this->load->model('vendor/Vendor_model','vm');
		$_POST	= $this->securities->clean_input($_POST,'save');
		$admin 	= $this->session->userdata('admin');
		$vld 	= array(
			array(
				'field'=>'name',
				'label'=>'Nama Sub Bidang',
				'rules'=>'required|is_unique[tb_legal.name]'
				),
			);
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			unset($_POST['Simpan']);
			$_POST['entry_stamp'] = date("Y-m-d H:i:s");

			$this->vm->save_badan_hukum($this->input->post());

			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');
			// echo print_r($this->input->post());
			redirect(site_url('admin/admin_badan_hukum/'));
		}

		$data['sbu'] 		= $this->vm->get_sbu();
		$layout['content']	= $this->load->view('badan_hukum/tambah',$data,TRUE);

		$item['header'] 	= $this->load->view('admin/header',$admin,TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}



	public function edit_badan_hukum($id){
		$data 			= $this->vm->get_badan_hukum($id);
		$_POST 			= $this->securities->clean_input($_POST,'save');
		$admin 			= $this->session->userdata('admin');
		$vld 			= array(
			array(
				'field'=>'name',
				'label'=>'Nama Badan Hukum',
				'rules'=>'required'
				),
			);

		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);

			$res = $this->vm->edit_badan_hukum($this->input->post(),$id);

			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('admin/admin_badan_hukum'));
			}
		}

		$layout['content']= $this->load->view('badan_hukum/edit',$data,TRUE);

		$admin = $this->session->userdata('admin');
		$item['header'] = $this->load->view('admin/header',$admin,TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function hapus_badan_hukum($id){
		if($this->vm->delete_badan_hukum($id)){
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('admin/admin_badan_hukum'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('admin/admin_badan_hukum'));
		}
	}
}