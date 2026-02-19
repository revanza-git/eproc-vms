<?php defined('BASEPATH') OR exit('No direct script access allowed');

class admin_evaluasi extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('admin')){
			redirect(site_url());
		}
		$this->load->model('evaluasi/evaluasi_model','em');
	}
	public function get_field(){
		return array(
			array(
				'label'	=>	'Evaluasi',
				'filter'=>	array(
								array('table'=>'tb_evaluasi|name' ,'type'=>'text','label'=> 'Nama Evaluasi'),
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

		$data['evaluasi']		= $this->em->get_evaluasi_list($search, $sort, $page, $per_page,TRUE);
		$data['pagination'] 	= $this->utility->generate_page('admin/admin_evaluasi',$sort, $per_page, $this->em->get_evaluasi_list($search, $sort, '','',FALSE));
		$data['sort'] 			= $sort;
		
		$layout['content']	= $this->load->view('evaluasi/content',$data,TRUE);

		$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}


	public function tambah(){
		$admin 				= $this->session->userdata('admin');
		$data['header'] 	= $this->em->get_header();
		$vld 	= array(
			array(
				'field'=>'id_ms_quest',
				'label'=>'Tentukan Header Untuk Evaluasi',
				'rules'=>'required'
				),
			array(
				'field'=>'name',
				'label'=>'Nama Evaluasi',
				'rules'=>'required'
				),
			array(
				'field'=>'point_a',
				'label'=>'Point Pertama',
				'rules'=>'required'
				),
			array(
				'field'=>'point_b',
				'label'=>'Point Kedua',
				'rules'=>'required'
				),
			array(
				'field'=>'point_c',
				'label'=>'Point Ketiga',
				'rules'=>'required'
				),
			array(
				'field'=>'point_d',
				'label'=>'Point Keempat',
				'rules'=>'required'
				)
			);
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			unset($_POST['Simpan']);
			// $_POST['entry_stamp'] 	= date("Y-m-d H:i:s");
			$res = $this->em->save_evaluasi($this->input->post());
			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data evaluasi!</p>');
				redirect(site_url('admin/admin_evaluasi'));
			}
			
		}

		$layout['content']	= $this->load->view('evaluasi/tambah',$data,TRUE);

		$item['header'] 	= $this->load->view('admin/header',$admin,TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	function edit($id){
		$data 			= $this->em->get_data($id);
		$data['header'] = $this->em->get_header();
		$_POST 			= $this->securities->clean_input($_POST,'save');
		$admin 			= $this->session->userdata('admin');
		$vld 	= array(
			array(
				'field'=>'id_ms_quest',
				'label'=>'Tentukan Header Untuk Evaluasi',
				'rules'=>'required'
				),
			array(
				'field'=>'name',
				'label'=>'Nama Evaluasi',
				'rules'=>'required'
				),
			array(
				'field'=>'point_a',
				'label'=>'Point Pertama',
				'rules'=>'required'
				),
			array(
				'field'=>'point_b',
				'label'=>'Point Kedua',
				'rules'=>'required'
				),
			array(
				'field'=>'point_c',
				'label'=>'Point Ketiga',
				'rules'=>'required'
				),
			array(
				'field'=>'point_d',
				'label'=>'Point Keempat',
				'rules'=>'required'
				)
			);

		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			// $_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Simpan']);

			$res = $this->em->edit_data($this->input->post(),$id);

			if($res){

				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('admin/admin_evaluasi'));
			}
		}

		$layout['content']	= $this->load->view('evaluasi/edit',$data,TRUE);

		$admin 				= $this->session->userdata('admin');
		$item['header'] 	= $this->load->view('admin/header',$admin,TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
}