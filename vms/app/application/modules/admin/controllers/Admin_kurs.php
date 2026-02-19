<?php defined('BASEPATH') OR exit('No direct script access allowed');

class admin_kurs extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('admin')){
			redirect(site_url());
		}
		$this->load->model('user/admin_user_model','am');
		
	}
	public function get_field(){
		return array(
			array(
				'label'	=>	'Mata Uang',
				'filter'=>	array(
								array('table'=>'tb_kurs|name' ,'type'=>'text','label'=> 'Nama Mata Uang'),
								array('table'=>'tb_kurs|symbol' ,'type'=>'text','label'=> 'Simbol Mata Uang'),
							)
			),
			
		);
	}
	public function index()
	{	
		$this->load->library('form');
		$this->load->library('datatables');

		$data['filter_list'] = $this->filter->group_filter_post($this->get_field());

		$search 	= $this->input->get('q');
		$page 		= '';
		$per_page	= 10;
		$sort 		= $this->utility->generateSort(array('name', 'symbol'));

		$data['kurs']		= $this->am->get_kurs_list($search, $sort, $page, $per_page,TRUE);
		$data['pagination'] = $this->utility->generate_page('admin/admin_kurs/',$sort, $per_page, $this->am->get_kurs_list($search, $sort, '','',FALSE));
		$data['sort'] 		= $sort;
		$layout['content']	= $this->load->view('kurs/content',$data,TRUE);

		$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
		// echo print_r($this->db->last_query());
	}
	//#####################################################
	//################  		KURS		 ##############
	//#####################################################
	public function tambah_kurs(){
		$this->load->model('vendor/Vendor_model','am');
		$_POST	= $this->securities->clean_input($_POST,'save');
		$admin 	= $this->session->userdata('admin');
		$vld 	= array(
			array(
				'field'=>'name',
				'label'=>'Nama Kurs',
				'rules'=>'required'
				),
			array(
				'field'=>'symbol',
				'label'=>'Simbol Kurs',
				'rules'=>'required'
				),
			);
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			unset($_POST['Simpan']);
			$_POST['entry_stamp'] = date("Y-m-d H:i:s");

			$this->am->save_kurs($this->input->post());

			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');
			// echo print_r($this->input->post());
			redirect(site_url('admin/admin_kurs/'));
		}

		
		$layout['content']= $this->load->view('kurs/tambah',null,TRUE);

		$admin = $this->session->userdata('admin');
		$item['header'] = $this->load->view('admin/header',$admin,TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}



	public function edit_kurs($id){
		$data 			= $this->am->get_kurs($id);
		$_POST 			= $this->securities->clean_input($_POST,'save');
		$admin 			= $this->session->userdata('admin');
		$vld 			= array(
			array(
				'field'=>'name',
				'label'=>'Nama Kurs',
				'rules'=>'required'
				),			
			array(
				'field'=>'symbol',
				'label'=>'Simbol Kurs',
				'rules'=>'required'
				),
			);

		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);

			$res = $this->am->edit_kurs($this->input->post(),$id);

			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('admin/admin_kurs'));
				// echo print_r($this->db->last_query());
			}
		}

		$layout['content']= $this->load->view('kurs/edit',$data,TRUE);

		$admin = $this->session->userdata('admin');
		$item['header'] = $this->load->view('admin/header',$admin,TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function hapus_kurs($id){
		if($this->am->delete_kurs($id)){
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('admin/admin_kurs'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('admin/admin_kurs'));
		}
	}
}