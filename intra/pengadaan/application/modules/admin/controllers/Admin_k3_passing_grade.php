<?php defined('BASEPATH') OR exit('No direct script access allowed');

class admin_k3_passing_grade extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('admin')){
			redirect(site_url());
		}

		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->load->model('k3_passing_grade/k3_passing_grade','kpg');
	}


	public function get_field(){
		return array( 
			array(
				'label'	=>	'Passing Grade',
				'filter'=>	array(
								array('table'=>'tb_csms_limit|value' ,'type'=>'text','label'=> 'Kriteria'),
								array('table'=>'tb_csms_limit|start_score' ,'type'=>'text','label'=> 'Nilai Tertinggi'),
								array('table'=>'tb_csms_limit|end_score' ,'type'=>'text','label'=> 'Nilai Terendah'),
							)
			),
			
		);
	}

	public function index(){	
		$search 	= $this->input->get('q');

		$page 		= '';
		$per_page	= 10;
		$sort 		= $this->utility->generateSort(array('value','start_score','end_score'));

		$data['filter_list'] 	= $this->filter->group_filter_post($this->get_field());

		$data['pagination'] 	= $this->utility->generate_page('admin/admin_k3_passing_grade',$sort, $per_page, $this->kpg->get_passing_grade($search, $sort, '','',FALSE));
		$data['sort'] 			= $sort;
		$data['passing_grade']	= $this->kpg->get_passing_grade();

		
		$layout['content']	= $this->load->view('k3_passing_grade/content',$data,TRUE);

		$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function tambah(){

		// $_POST = $this->securities->clean_input($_POST,'save');
		$admin = $this->session->userdata('admin');
		$this->form_validation->set_rules('value','Nama Kriteria','required');
		$score 	= array(
					array(
						'field'=>'start_score',
						'label'=>'Nilai Tertinggi',
						'rules'=>'required|integer'
						),
					array(
						'field'=>'end_score',
						'label'=>'Nilai Terendah',
						'rules'=>'required|integer'
						),
					);

		$start_score_post = $this->input->post('start_score', true);
		$end_score_post = $this->input->post('end_score', true);
		if (($start_score_post !== null && $start_score_post !== '') || ($end_score_post !== null && $end_score_post !== '')) {
			$score 	= array(
					array(
						'field'=>'start_score',
						'label'=>'Nilai Tertinggi',
						'rules'=>'integer'
						),
					array(
						'field'=>'end_score',
						'label'=>'Nilai Terendah',
						'rules'=>'integer'
						),
					);
		}


		$this->form_validation->set_rules($score);
		

		if($this->form_validation->run()==TRUE){
			unset($_POST['Simpan']);
			$_POST['entry_stamp'] 	= date("Y-m-d H:i:s");
			$_POST['start_score']	= ($this->input->post('start_score') != FALSE) ? $this->input->post('start_score') : NULL;
			$_POST['end_score']		= ($this->input->post('end_score') != FALSE) ? $this->input->post('end_score') : NULL;
			$isi 	= $this->input->post(null, true);
			// $isi	= empty($this->input->post('start_score'))? NULL : $this->input->post('start_score');
			$this->kpg->save_data($isi);

			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');
			redirect(site_url('admin/admin_k3_passing_grade'));
		}

		$layout['content']= $this->load->view('k3_passing_grade/tambah',null,TRUE);


		$item['header'] = $this->load->view('admin/header',$admin,TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}


	public function edit($id){

		$data['edit'] 	= $this->kpg->get_data($id);
		$admin 			= $this->session->userdata('admin');
		$score 	= array(
					array(
						'field'=>'start_score',
						'label'=>'Nama Kurs',
						'rules'=>'required|integer'
						),
					array(
						'field'=>'end_score',
						'label'=>'Simbol Kurs',
						'rules'=>'required|integer'
						),
					);

		if ((isset($_POST['start_score']) && $_POST['start_score']!=null) || (isset($_POST['end_score']) && $_POST['end_score']!=null)) {
			$score 	= array(
					array(
						'field'=>'start_score',
						'label'=>'Nama Kurs',
						'rules'=>'integer'
						),
					array(
						'field'=>'end_score',
						'label'=>'Simbol Kurs',
						'rules'=>'integer'
						),
					);
		}

		$this->form_validation->set_rules($score);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);

			$res = $this->kpg->edit_data($this->input->post(),$id);

			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('admin/admin_k3_passing_grade'));
			}
		}

		$layout['content']	= $this->load->view('k3_passing_grade/edit',$data,TRUE);

		$item['header'] 	= $this->load->view('admin/header',$admin,TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}


	public function hapus($id){
		if($this->kpg->delete($id)){
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('admin/admin_k3_passing_grade'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('admin/admin_k3_passing_grade'));
		}
	}
	

}
