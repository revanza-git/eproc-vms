<?php defined('BASEPATH') OR exit('No direct script access allowed');
 
class Admin_user extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('admin')){
			redirect(site_url());
		}
		$this->load->model('user/admin_user_model','aum');
		$this->load->model('izin/Izin_model','im');	
		$this->load->model('pengadaan/pengadaan_model','pm');	
	}
	public function get_field(){
		return array(
			array(
				'label'	=>	'User',
				'filter'=>	array(
								array('table'=>'tb_role|name' ,'type'=>'text','label'=> 'Role'),
								array('table'=>'ms_admin|name' ,'type'=>'text','label'=> 'Nama User'),
								array('table'=>'ms_admin|password' ,'type'=>'text','label'=> 'Password User'),
							)
			),
			
		);
	}
	public function index()
	{	
		$this->load->library('form');
		$this->load->library('datatables');

		$data['filter_list'] = $this->filter->group_filter_post($this->get_field());

		$search = $this->input->get('q');
		$page = '';
		
		$per_page=10;

		$sort = $this->utility->generateSort(array('role_name','name','password','email'));

		$data['akta_list']=$this->aum->get_admin_user_list($search, $sort, $page, $per_page,TRUE);

		$data['pagination'] = $this->utility->generate_page('admin/admin_user',$sort, $per_page, $this->aum->get_admin_user_list($search, $sort, '','',FALSE));
		$data['sort'] = $sort;

		$layout['content']= $this->load->view('user/content',$data,TRUE);

		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function tambah()
	{
		$this->load->model('vendor/Vendor_model','vm');
		$_POST = $this->securities->clean_input($_POST,'save');
		$admin = $this->session->userdata('admin');
		$data['budget_spender'] = $this->pm->get_budget_spender();
		$vld = 	array(
			array(
				'field'=>'name',
				'label'=>'Nama User',
				'rules'=>'required'
				),
			array(
				'field'=>'password',
				'label'=>'Password',
				'rules'=>'required'
				),
			array(
				'field'=>'id_role',
				'label'=>'Jabatan',
				'rules'=>'required'
				),
			array(
				'field'=>'email',
				'label'=>'E-mail',
				'rules'=>'required|valid_email'
				)
		);
		if($this->input->post('id_role')==9){
			$vld[] = array(
					'field'=>'id_division',
					'label'=>'Divisi',
					'rules'=>'required'
				);
		}	
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			unset($_POST['Simpan']);
			$_POST['entry_stamp'] = date("Y-m-d H:i:s");

			$this->aum->save_data($this->input->post());

			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');

			redirect(site_url('admin/admin_user'));
		}
		$data['sbu'] = $this->vm->get_sbu();
		$data['role'] = $this->aum->get_role();
		$layout['content']= $this->load->view('user/tambah',$data,TRUE);
		$layout['script']= $this->load->view('user/tambah_js',$data,TRUE);


		$item['header'] = $this->load->view('admin/header',$admin,TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	//ADMIN
	public function edit($id){
		$data 			= $this->aum->get_data($id);
		$data['role'] 		= $this->aum->get_role();
		$_POST 			= $this->securities->clean_input($_POST,'save');
		$admin 			= $this->session->userdata('admin');
		$data['budget_spender'] = $this->pm->get_budget_spender();
		$vld = 	array(
			array(
				'field'=>'name',
				'label'=>'Nama User',
				'rules'=>'required'
				),
			array(
				'field'=>'password',
				'label'=>'Password',
				'rules'=>'required'
				),
			array(
				'field'=>'id_role',
				'label'=>'Role User',
				'rules'=>'required'
				),
			array(
				'field'=>'email',
				'label'=>'E-mail',
				'rules'=>'required|valid_email'
				)
			);
		if($this->input->post('id_role')==9){
			$vld[] = array(
					'field'=>'id_division',
					'label'=>'Divisi',
					'rules'=>'required'
				);
		}	
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);

			$res = $this->aum->edit_data($this->input->post(),$id);

			if($res){
				$_POST['username'] = $this->input->post('name');
				unset($_POST['name']);
				unset($_POST['id_role']);
				$_login['username'] = $_POST['username'];
				$_login['password'] = $_POST['password'];
				$result = $this->aum->edit_data2($_login,$id);


				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('admin/admin_user'));
			}
		}

		$layout['content']	= $this->load->view('user/edit',$data,TRUE);
		$layout['script']= $this->load->view('user/tambah_js',$data,TRUE);
		
		$admin 				= $this->session->userdata('admin');
		$item['header'] 	= $this->load->view('admin/header',$admin,TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	//ADMIN
	public function hapus($id){
		if($this->aum->delete($id)){

			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('admin/admin_user'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('admin/admin_user'));
		}
	}
	
}