<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_assessment extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('admin')){
			redirect(site_url());
		}
		$this->load->model('assessment/admin_assessment_model','aam');	
		$this->load->model('pengadaan/pengadaan_model','pm');
		$this->load->model('user/admin_user_model','aum');
	}
	public function index(){	
		$search 	= $this->input->get('q');
		$page 		= '';
		$per_page	= 10;
		$sort 		= $this->utility->generateSort(array('id_bidang','name'));

		// $data = $this->pm->get_pengadaan($id);
		// $data_kontrak = $this->pm->get_kontrak($id);

		

		$data['assessment']	= $this->aam->get_assessment_quest_list($search, $sort, $page, $per_page,TRUE);
		$data['pagination'] = $this->utility->generate_page('admin/admin_sub_bidang/',$sort, $per_page, $this->aam->get_assessment_quest_list($search, $sort, '','',FALSE));
		$data['sort'] 		= $sort;
		$layout['content']	= $this->load->view('assessment/content',$data,TRUE);

		$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	//#####################################################
	//################  	SUB BIDANG		 ##############
	//#####################################################
	public function tambah_assessment($id){
		
		// $_POST	= $this->securities->clean_input($_POST,'save');
		$admin 	= $this->session->userdata('admin');
		$vld 	= array(
			array(
				'field'=>'value',
				'label'=>'Kalimat',
				'rules'=>'required'
				),
			array(
				'field'=>'id_role',
				'label'=>'Penilai`',
				'rules'=>'required'
				),
			array(
				'field'=>'point',
				'label'=>'bobot',
				'rules'=>'required'
				)
			);
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			unset($_POST['Simpan']);
			$_POST['entry_stamp'] 	= date("Y-m-d H:i:s");
			$_POST['id_group']		= $id;
			$res = $this->aam->save_assessment($this->input->post());
			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');
				redirect(site_url('admin/admin_assessment'));
			}
			
		}
		
		$data['role'] = $this->aum->get_role();
		$layout['content']	= $this->load->view('assessment/tambah',$data,TRUE);
		$layout['script']	= $this->load->view('assessment/content_js',NULL,TRUE);

		$item['header'] 	= $this->load->view('admin/header',$admin,TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function tambah_group(){

		// $_POST	= $this->securities->clean_input($_POST,'save');
		$admin 	= $this->session->userdata('admin');
		$vld 	= array(
			array(
				'field'=>'name',
				'label'=>'Nama Group',
				'rules'=>'required'
				)
			);
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			unset($_POST['Simpan']);
			$_POST['entry_stamp'] 	= date("Y-m-d H:i:s");
			$_POST['id_group']		= $id;
			$res = $this->aam->save_group($this->input->post());
			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');
				redirect(site_url('admin/admin_assessment'));
			}
			
		}

		$layout['content']	= $this->load->view('assessment/tambah_group',NULL,TRUE);

		$item['header'] 	= $this->load->view('admin/header',$admin,TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function edit_assessment($id){
		$data 			= $this->aam->get_data_assessment($id);
		$data['group']	= $this->aam->get_data_group();
		
		// $_POST 			= $this->securities->clean_input($_POST,'save');
		$admin 			= $this->session->userdata('admin');
		$vld 			= array(
				array(
					'field'=>'value',
					'label'=>'Nama Sub Bidang',
					'rules'=>'required'
					),
				array(
					'field'=>'point',
					'label'=>'Nama Sub Bidang',
					'rules'=>'required'
					),
				array(
					'field'=>'id_group',
					'label'=>'Group',
					'rules'=>'required'
					),
			);

		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);

			$res = $this->aam->edit_data_assessment($this->input->post(),$id);

			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				// echo print_r($this->input->post());
				redirect(site_url('admin/admin_assessment'));
			}
		}
		$data['role'] = $this->aum->get_role();
		$layout['content']= $this->load->view('assessment/edit',$data,TRUE);
		$layout['script']	= $this->load->view('assessment/content_js',NULL,TRUE);
		$admin = $this->session->userdata('admin');
		$item['header'] = $this->load->view('admin/header',$admin,TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function hapus_assessment($id){
		if($this->aam->hapus_data_assessment($id)){
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('admin/admin_assessment'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('admin/admin_assessment'));
		}
	}

	public function edit_group($id){
		$data		= $this->aam->get_data_group_list($id);

		// echo print_r($data);
		$_POST 			= $this->securities->clean_input($_POST,'save');
		$admin 			= $this->session->userdata('admin');
		$vld 			= array(
				array(
					'field'=>'name',
					'label'=>'Nama Group',
					'rules'=>'required'
					),
			);

		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);

			$res = $this->aam->edit_data_group($this->input->post(),$id);

			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('admin/admin_assessment'));
			}
		}

		
		$layout['content']	= $this->load->view('assessment/edit_group',$data,TRUE);

		$admin 				= $this->session->userdata('admin');
		$item['header'] 	= $this->load->view('admin/header',$admin,TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function hapus_group($id){
		if($this->aam->hapus_data_group($id)){
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('admin/admin_assessment'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('admin/admin_assessment'));
		}
	}
}