<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('user')){
			redirect(site_url());
		}
		$this->load->model('vendor/vendor_model','vm');
		$this->load->model('dashboard/dashboard_model','bm');
		$data = $this->session->userdata('user');
		
	}
	public function index()
	{
		$data = $this->session->userdata('user');

		if($this->vm->check_pic($data['id_user'])==0){
			redirect(site_url('dashboard/pernyataan'));
		}
	
		$this->load->model('approval/approval_model','am');
		$this->load->model('note/note_model','nm');

		$data['approval_data'] = $this->am->get_total_data($data['id_user']);
		$data['note'] = $this->nm->get_note($data['id_user']);
		$data['graphBar'] = array(
									0=>
										array('val'=>(count($data['approval_data'][0]))/$data['approval_data']['total']*100,
											'color'=>'#f39c12'
										),
									1=>
										array('val'=>(count($data['approval_data'][1])+count($data['approval_data'][2]))/$data['approval_data']['total']*100,
											'color'=>'#2cc36b'
										),
									3=>array('val'=>(count($data['approval_data'][3])+count($data['approval_data'][4]))/$data['approval_data']['total']*100,
											'color'=>'#c0392b'
										),
								);
		
		$layout['content']	= $this->load->view('content',$data,TRUE);
		$layout['script']	= $this->load->view('content_js',$data,TRUE);
		
		$item['header'] = $this->load->view('header',$data,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	
	public function pernyataan(){
		
		$data = $this->session->userdata('user');
		$data['id_legal'] = $this->vm->get_legal();
		$data['pernyataan'] = $this->bm->get_pernyataan();

		$data['data_vendor'] = $this->vm->get_pt($data['id_user']);

		$vld = array();
		if($this->input->post('next')){
			$vld = 	array(
				array(
					'field'=>'pic_name',
					'label'=>'Nama',
					'rules'=>'required'
					),
				array(
					'field'=>'pic_position',
					'label'=>'Jabatan',
					'rules'=>'required'
					),
				array(
					'field'=>'pic_phone',
					'label'=>'No Telp',
					'rules'=>'required|numeric'
					),
				array(
					'field'=>'pic_email',
					'label'=>'Email',
					'rules'=>'required|valid_email'
					),
				array(
					'field'=>'pic_address',
					'label'=>'Alamat',
					'rules'=>'required'
					),
				);
		}
		
		$this->form_validation->set_rules($vld);
		
		if($this->form_validation->run()==TRUE){
			$_POST['id_vendor']	= $data['id_user'];
			$_POST['entry_stamp'] = date("Y-m-d H:i:s");
			$res = $this->vm->save_pic($this->input->post());
			if($res){
				unset($_POST['next']);
				$this->session->set_userdata('form',$this->input->post());
				redirect(site_url(''));
			}
		}

		$layout['content']= $this->load->view('pic_form',$data,TRUE);

		$item['header'] = $this->load->view('header',$data,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

}
