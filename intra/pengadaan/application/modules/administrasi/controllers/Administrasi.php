<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Administrasi extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('user')){
			redirect(site_url());
		}
		$this->load->model('vendor/vendor_model','vm');
		$this->load->library('encrypt');
		$this->load->library('utility');
		$this->load->library('form_validation');
		
	}

	public function index()
	{	
		$data = $this->session->userdata('user');
		if($this->vm->check_pic($data['id_user'])==0){
			redirect(site_url('dashboard/pernyataan'));
		}
		$user = $this->session->userdata('user');
		$data = $this->vm->get_data($user['id_user']);

		$layout['content']= $this->load->view('view',$data,TRUE);

		$item['header'] 	= $this->load->view('dashboard/header',$this->session->userdata('user'),TRUE);
		$item['content'] 	= $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	
	public function edit(){
		$user = $this->session->userdata('user');
		$data = $this->vm->get_data($user['id_user']);
		$data['sbu'] = $this->vm->get_sbu();
		$data['legal'] = $this->vm->get_legal();
		$_POST = $this->securities->clean_input($_POST,'save');
		$user = $this->session->userdata('user');
		$vld = 	array(
			array(
				'field'=>'id_legal',
				'label'=>'Badan hukum',
				'rules'=>'required'
				),
			array(
				'field'=>'name',
				'label'=>'Nama Badan Usaha',
				'rules'=>'required'
				),
			array(
				'field'=>'npwp_code',
				'label'=>'NPWP',
				'rules'=>'required|is_valid_npwp'
				),
			/*array(
				'field'=>'npwp_date',
				'label'=>'Tanggal Pengukuhan',
				'rules'=>'required'
				),*/
			
			array(
				'field'=>'nppkp_code',
				'label'=>'SPPKP',
				'rules'=>'required'
				),
			/*array(
				'field'=>'nppkp_date',
				'label'=>'Tanggal Pengukuhan',
				'rules'=>'required'
				),*/
			
			array(
				'field'=>'vendor_office_status',
				'label'=>'Status',
				'rules'=>'required'
				),
			array(
				'field'=>'vendor_address',
				'label'=>'Alamat',
				'rules'=>'required'
				),
			array(
				'field'=>'vendor_country',
				'label'=>'Negara',
				'rules'=>'required'
				),
			array(
				'field'=>'vendor_province',
				'label'=>'Provinsi',
				'rules'=>'required'
				),
			array(
				'field'=>'vendor_city',
				'label'=>'Kota',
				'rules'=>'required'
				),
			array(
				'field'=>'vendor_postal',
				'label'=>'Kode Pos',
				'rules'=>'required'
				),
			array(
				'field'=>'vendor_fax',
				'label'=>'Fax',
				'rules'=>''
				),
			array(
				'field'=>'vendor_phone',
				'label'=>'No. Telp',
				'rules'=>'required'
				),
			array(
				'field'=>'vendor_email',
				'label'=>'Email',
				'rules'=>'required|valid_email'
				),
			array(
				'field'=>'vendor_website',
				'label'=>'Website',
				'rules'=>''
				),
			/*array(				
				'field'=>'npwp_file',
				'label'=>'NPWP',
				'rules'=>'callback_do_upload[npwp_file]'
				),
			array(				
				'field'=>'nppkp_file',
				'label'=>'NPWP',
				'rules'=>'callback_do_upload[nppkp_file]'
				)*/
			);
		
		if(!empty($_FILES['npwp_file']['name'])||!isset($data['npwp_file'])){
			$vld[] = array(
				'field'=>'npwp_file',
				'label'=>'NPWP',
				'rules'=>'callback_do_upload[npwp_file]'
				);
		}

		if(!empty($_FILES['nppkp_file']['name'])||!isset($data['nppkp_file'])){
			$vld[] = array(
				'field'=>'nppkp_file',
				'label'=>'NPPKP',
				'rules'=>'callback_do_upload[nppkp_file]'
				);
		}

		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);

			$result = $this->vm->edit_data($this->input->post(),$user['id_user']);
			if($result){
				$this->dpt->non_iu_change($user['id_user']);
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('administrasi'));

			}
			
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
