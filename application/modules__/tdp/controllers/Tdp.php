<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tdp extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('user')){
			redirect(site_url());
		}
		$this->load->model('tdp_model','tm');
		$this->load->library('encrypt');
		$this->load->library('utility');
		
	}


	public function get_field(){
		return array(
			array(
				'label'	=>	'TDP',
				'filter'=>	array(
								// array('table'=>'ms_tdp|type' ,'type'=>'text','label'=> 'Jenis Akta'),
								array('table'=>'ms_tdp|no' ,'type'=>'text','label'=> 'No. TDP'),
								array('table'=>'ms_tdp|issue_date' ,'type'=>'date','label'=> 'Tanggal'),
								array('table'=>'ms_tdp|authorize_by' ,'type'=>'text','label'=> 'Lembaga Penerbit'),
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

		$sort = $this->utility->generateSort(array('no', 'issue_date', 'authorize_by','expiry_date','tdp_file','extension_file'));

		$data['tdp_list']=$this->tm->get_tdp_list($search, $sort, $page, $per_page,TRUE);
		$data['filter_list'] 	= $this->filter->group_filter_post($this->get_field());
		$data['pagination'] = $this->utility->generate_page('tdp',$sort, $per_page, $this->tm->get_tdp_list($search, $sort, '','',FALSE));
		$data['sort'] = $sort;

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
				'field'=>'no',
				'label'=>'Nomor',
				'rules'=>'required'
				),
			array(
				'field'=>'issue_date',
				'label'=>'Tanggal',
				'rules'=>'required'
				),
			array(
				'field'=>'expiry_date',
				'label'=>'Masa Berlaku',
				'rules'=>'required|backdate'
				),
			array(
				'field'=>'authorize_by',
				'label'=>'Lembaga Penerbit',
				'rules'=>'required'
				),
			array(
				'field'=>'tdp_file',
				'label'=>'Lampiran',
				'rules'=>'callback_do_upload[tdp_file]'
				)
			);
		if(($_FILES['extension_file']['name']) != ""){
			$vld[] = array(
					'field'=>'extension_file',
					'label'=>'Bukti Perpanjangan',
					'rules'=>'callback_do_upload[extension_file]'
					);
		}else{
			$_POST['extension_file'] = '';
		}
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['id_vendor'] = $user['id_user'];
			$_POST['entry_stamp'] = date("Y-m-d H:i:s");
			$_POST['data_status'] = 0;

			$res = $this->tm->save_data($this->input->post());
			if($res){
				$this->dpt->set_email_blast($res,'ms_tdp','Tanda Daftar Perusahaan',$_POST['expiry_date']);
				$this->dpt->non_iu_change($user['id_user']);
				
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');
				redirect(site_url('tdp'));
			}
		}

		$layout['content']= $this->load->view('tambah',NULL,TRUE);


		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function edit($id){
		$data = $this->tm->get_data($id);
		$_POST = $this->securities->clean_input($_POST,'save');
		$user = $this->session->userdata('user');
		$vld = 	array(
			array(
				'field'=>'no',
				'label'=>'Nomor',
				'rules'=>'required'
				),
			array(
				'field'=>'issue_date',
				'label'=>'Tanggal',
				'rules'=>'required'
				),
			array(
				'field'=>'expiry_date',
				'label'=>'Masa Berlaku',
				'rules'=>'required'
				),
			array(
				'field'=>'authorize_by',
				'label'=>'Lembaga Penerbit',
				'rules'=>'required'
				),
			);

		if(!empty($_FILES['tdp_file']['name'])){
			$vld[] = array(
					'field'=>'file_photo',
					'label'=>'Foto Lokasi',
					'rules'=>'callback_do_upload[tdp_file]'
					);
		}
		if(!empty($_FILES['extension_file']['name'])){
			$vld[] = array(
					'field'=>'extension_file',
					'label'=>'Bukti Perpanjangan',
					'rules'=>'callback_do_upload[extension_file]'
					);
		}else{
			$_POST['extension_file'] = '';
		}
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);
			
			$res = $this->tm->edit_data($this->input->post(),$id);
			if($res){
				$this->dpt->edit_data($id,'ms_tdp');
				$this->dpt->non_iu_change($user['id_user']);
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('tdp'));
			}
		}



		$layout['content']= $this->load->view('edit',$data,TRUE);

		$user = $this->session->userdata('user');
		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	public function hapus($id){
		if($this->tm->delete($id)){
			$this->dpt->non_iu_change($user['id_user']);
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('tdp'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('tdp'));
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
