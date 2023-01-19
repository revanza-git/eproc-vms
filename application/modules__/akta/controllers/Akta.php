<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Akta extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('user')){
			redirect(site_url());
		}
		$this->load->model('Akta_model','am');
		$this->load->library('encrypt');
		$this->load->library('utility');
		
	}

	public function get_field(){
		return array(
			array(
				'label'	=>	'Akta',
				'filter'=>	array(
								// array('table'=>'ms_akta|type' ,'type'=>'text','label'=> 'Jenis Akta'),
								array('table'=>'ms_akta|notaris' ,'type'=>'text','label'=> 'Notaris'),
								array('table'=>'ms_akta|issue_date' ,'type'=>'date','label'=> 'Tanggal'),
								array('table'=>'ms_akta|no' ,'type'=>'text','label'=> 'No. Akta'),
							)
			),
			
		);
	}

	public function index()
	{	
		$data = $this->session->userdata('user');
		if($this->vm->check_pic($data['id_user'])==0){
			redirect(site_url('dashboard/pernyataan'));
		}
		$search = $this->input->get('q');
		$page = '';
		
		$per_page=10;

		$sort = $this->utility->generateSort(array('type', 'no', 'notaris', 'issue_date', 'akta_file', 'authorize_by','authorize_no','authorize_date','authorize_file'));

		$data['filter_list'] 	= $this->filter->group_filter_post($this->get_field());
		$data['akta_list']		= $this->am->get_akta_list($search, $sort, $page, $per_page,TRUE);
		$data['pagination'] 	= $this->utility->generate_page('akta',$sort, $per_page, $this->am->get_akta_list($search, $sort, '','',FALSE));
		$data['sort'] 			= $sort;

		$layout['content']= $this->load->view('content',$data,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$this->session->userdata('user'),TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	
	public function tambah()
	{
		$_POST = $this->securities->clean_input($_POST,'save');
		$user = $this->session->userdata('user');
		$vld = 	array(
			array(
				'field'=>'type',
				'label'=>'Jenis',
				'rules'=>'required'
				),
			array(
				'field'=>'no',
				'label'=>'Nomor Akta',
				'rules'=>'required'
				),
			array(
				'field'=>'notaris',
				'label'=>'Notaris',
				'rules'=>'required'
				),
			array(
				'field'=>'issue_date',
				'label'=>'Tanggal',
				'rules'=>'required'
				),
			array(
				'field'=>'akta_file',
				'label'=>'Lampiran Akta',
				'rules'=>'callback_do_upload[akta_file]'
				),
			array(
				'field'=>'authorize_by',
				'label'=>'Lembaga Pengesah',
				'rules'=>'required'
				),
			
			array(
				'field'=>'authorize_no',
				'label'=>'Nomor Pengesahan',
				'rules'=>'required'
				),
			array(
				'field'=>'authorize_date',
				'label'=>'Tanggal Pengesahan',
				'rules'=>'required'
				),
			array(
				'field'=>'authorize_file',
				'label'=>'Lampiran Pengesahan',
				'rules'=>'callback_do_upload[authorize_file]'
				),
			);
		if(($_FILES['file_extension_akta']['name']) != ""){
			$vld[] = array(
					'field'=>'file_extension_akta',
					'label'=>'Bukti Sedang Dalam Proses Perpanjangan',
					'rules'=>'callback_do_upload[file_extension_akta]'
					);
		}

		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['id_vendor'] = $user['id_user'];
			$_POST['entry_stamp'] = date("Y-m-d H:i:s");
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			$_POST['data_status'] = 0;

			$result = $this->am->save_data($this->input->post());
			if($result){
				$this->dpt->non_iu_change($user['id_user']);
			}
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');

			redirect(site_url('akta'));
		}

		$layout['content']= $this->load->view('tambah',NULL,TRUE);


		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function edit($id){
		$data = $this->am->get_data($id);

		$_POST = $this->securities->clean_input($_POST,'save');
		$user = $this->session->userdata('user');
		$vld = 	array(
			array(
				'field'=>'type',
				'label'=>'Jenis',
				'rules'=>'required'
				),
			array(
				'field'=>'no',
				'label'=>'Nomor Akta',
				'rules'=>'required'
				),
			array(
				'field'=>'notaris',
				'label'=>'Notaris',
				'rules'=>'required'
				),
			array(
				'field'=>'issue_date',
				'label'=>'Tanggal',
				'rules'=>'required'
				),
			
			array(
				'field'=>'authorize_by',
				'label'=>'Lembaga Pengesah',
				'rules'=>'required'
				),
			
			array(
				'field'=>'authorize_no',
				'label'=>'Nomor Pengesahan',
				'rules'=>'required'
				),
			array(
				'field'=>'authorize_date',
				'label'=>'Tanggal Pengesahan',
				'rules'=>'required'
				)	
			);
		
		if(($_FILES['file_extension_akta']['name']) != ""){
			$vld[] = array(
					'field'=>'file_extension_akta',
					'label'=>'Bukti Sedang Dalam Proses Perpanjangan',
					'rules'=>'callback_do_upload[file_extension_akta]'
					);
		}
		if(!empty($_FILES['akta_file']['name'])){
			$vld[] = array(
					'field'=>'akta_file',
					'label'=>'Lampiran Akta',
					'rules'=>'callback_do_upload[akta_file]'
					);
		}

		if(!empty($_FILES['authorize_file']['name'])){
			$vld[] = array(
					'field'=>'authorize_file',
					'label'=>'Lampiran Pengesahan',
					'rules'=>'callback_do_upload[authorize_file]'
					);
		}

		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);

			$result = $this->am->edit_data($this->input->post(),$id);
			if($result){
				$this->dpt->edit_data($id,'ms_akta');
				$this->dpt->non_iu_change($user['id_user']);
			}

			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');

			redirect(site_url('akta'));
		}

		

		$layout['content']= $this->load->view('edit',$data,TRUE);

		$user = $this->session->userdata('user');
		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	public function hapus($id){
		if($this->am->delete($id)){
			$this->dpt->non_iu_change($user['id_user']);
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('akta'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('akta'));
		}
	}
	public function do_upload($field, $db_name = ''){	
		
		$file_name = $_FILES[$db_name]['name'] = $db_name.'_'.$this->utility->name_generator($_FILES[$db_name]['name']);
		
		$config['upload_path'] = './lampiran/'.$db_name.'/';
		$config['allowed_types'] = 'pdf|jpeg|jpg|png|gif';
		$config['max_size'] = 0;
		
		$this->load->library('upload');
		$this->upload->initialize($config);
		
		if ( ! $this->upload->do_upload($db_name)){
			$_POST[$db_name] = $file_name;
			$this->form_validation->set_message('do_upload', $this->upload->display_errors('',''));
			return false;
		}else{
			$_POST[$db_name] = $file_name; 
			return true;
		}
	}
}
