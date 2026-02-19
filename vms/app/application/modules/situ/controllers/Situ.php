<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Situ extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('user')){
			redirect(site_url());
		}
		$this->load->model('situ_model','sm');
		$this->load->library('encrypt');
		$this->load->library('utility');
		$this->load->library('form_validation');
		
	}


	public function get_field(){
		return array(
			array(
				'label'	=>	'SITU',
				'filter'=>	array(
								// array('table'=>'ms_situ|type' ,'type'=>'text','label'=> 'Jenis Akta'),
								array('table'=>'ms_situ|issue_by' ,'type'=>'text','label'=> 'Lembaga Penerbit'),
								array('table'=>'ms_situ|issue_date' ,'type'=>'date','label'=> 'Tanggal Penerbit'),
								// array('table'=>'ms_situ|expire_date' ,'type'=>'lifetime_date','label'=> 'Masa Berlaku'),
								array('table'=>'ms_situ|no' ,'type'=>'text','label'=> 'No. SITU'),
								array('table'=>'ms_situ|address' ,'type'=>'text','label'=> 'Alamat'),
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

		$sort = $this->utility->generateSort(array('type','issue_by', 'file_extension_situ', 'no', 'notaris', 'issue_date', 'file_photo', 'address','expire_date','situ_file'));

		$data['situ_list']		= $this->sm->get_situ_list($search, $sort, $page, $per_page,TRUE);
		$data['filter_list'] 	= $this->filter->group_filter_post($this->get_field());
		
		$data['pagination']		= $this->utility->generate_page('situ',$sort, $per_page, $this->sm->get_situ_list($search, $sort, '','',FALSE));
		$data['sort'] 			= $sort;

		$layout['content']		= $this->load->view('content',$data,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$this->session->userdata('user'),TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	
	public function tambah(){

		$_POST 	= $this->securities->clean_input($_POST,'save');
		$user 	= $this->session->userdata('user');
		$vld 	= array(
			array(
				'field'=>'type',
				'label'=>'Nama Surat',
				'rules'=>'required'
				),
			array(
				'field'=>'issue_by',
				'label'=>'Lembaga Penerbit',
				'rules'=>'required'
				),
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
				'field'=>'address',
				'label'=>'Alamat',
				'rules'=>'required'
				),
			array(
				'field'=>'expire_date',
				'label'=>'Masa Berlaku',
				'rules'=>'required|backdate'
				),
			array(
				'field'=>'situ_file',
				'label'=>'Bukti scan dokumen SKDP',
				'rules'=>'callback_do_upload[situ_file]'
				),
			);
		if(isset($_FILES['file_extension_situ']) && ($_FILES['file_extension_situ']['name']) != ""){
			$vld[] = array(
					'field'=>'file_extension_situ',
					'label'=>'Bukti Sedang Dalam Proses Perpanjangan',
					'rules'=>'callback_do_upload[file_extension_situ]'
					);
		}
		
		$this->form_validation->set_rules($vld);
				// print_r($this->input->post());
		if($this->form_validation->run($this)==TRUE){
			$_POST['id_vendor'] 	= $user['id_user'];
			$_POST['entry_stamp'] 	= date("Y-m-d H:i:s");
			$_POST['edit_stamp'] 	= date("Y-m-d H:i:s");
			$_POST['data_status'] 	= 0;

			$res = $this->sm->save_data($this->input->post());
			if($res){
				$this->dpt->set_email_blast($res,'ms_situ',$_POST['type'],$_POST['expire_date']);
				$this->dpt->non_iu_change($user['id_user']);
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');
				redirect(site_url('situ'));
			}
		}
		// echo validation_errors();

		// Initialize variables for the view
		$view_data = array(
			'situ_file' => '',
			'file_extension_situ' => ''
		);
		$layout['content']= $this->load->view('tambah',$view_data,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function edit($id){
		$data = $this->sm->get_data($id);
		$_POST = $this->securities->clean_input($_POST,'save');
		$user = $this->session->userdata('user');
		$vld = 	array(
			array(
				'field'=>'type',
				'label'=>'Nama Surat',
				'rules'=>'required'
				),
			array(
				'field'=>'issue_by',
				'label'=>'Lembaga Penerbit',
				'rules'=>'required'
				),
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
				'field'=>'address',
				'label'=>'Alamat',
				'rules'=>'required'
				),
			
			array(
				'field'=>'expire_date',
				'label'=>'Masa Berlaku',
				'rules'=>'required|backdate'
				)
			);
		if(!empty($_FILES['situ_file']['name'])){
			$vld[] = array(
				'field'=>'situ_file',
				'label'=>'Bukti scan dokumen SKDP',
				'rules'=>'callback_do_upload[situ_file]'
				);
		}

		if(!empty($_FILES['file_photo']['name'])){
			$vld[] = array(
					'field'=>'file_photo',
					'label'=>'Foto Lokasi',
					'rules'=>'callback_do_upload[file_photo]'
					);
		}

		if(isset($_FILES['file_extension_situ']) && ($_FILES['file_extension_situ']['name']) != ""){
			$vld[] = array(
					'field'=>'file_extension_situ',
					'label'=>'Bukti Sedang Dalam Proses Perpanjangan',
					'rules'=>'callback_do_upload[file_extension_situ]'
					);
		}

		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);

			$res = $this->sm->edit_data($this->input->post(),$id);
			if($res){
				$this->dpt->edit_data($id,'ms_situ');
				$this->dpt->non_iu_change($user['id_user']);
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('situ'));
			}
		}



		$layout['content']= $this->load->view('edit',$data,TRUE);

		$user = $this->session->userdata('user');
		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	public function hapus($id){
		$user      = $this->session->userdata('user');
		$situ_data = $this->sm->get_data($id);

		if($this->sm->delete($id)){
			$attachments = array(
				'situ_file'          => 'situ_file',
				'file_extension_situ'=> 'file_extension_situ',
				'file_photo'         => 'file_photo'
			);

			$base_lampiran_path = realpath(FCPATH.'../lampiran');
			if($base_lampiran_path === FALSE){
				$base_lampiran_path = FCPATH.'lampiran';
			}
			$base_lampiran_path = rtrim($base_lampiran_path, '/\\');

			foreach($attachments as $field => $dir){
				if(!empty($situ_data[$field])){
					$path = $base_lampiran_path.'/'.$dir.'/'.$situ_data[$field];
					if(file_exists($path)){
						@unlink($path);
					}
				}
			}

			$this->dpt->non_iu_change($user['id_user']);
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data beserta lampirannya!</p>');
			redirect(site_url('situ'));
		}else{
			$this->session->set_flashdata('errorMsg','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('situ'));
		}
	}
	public function do_upload($field, $db_name = ''){	
		
		$file_name = $_FILES[$db_name]['name'] = $db_name.'_'.$this->utility->name_generator($_FILES[$db_name]['name']);
		
		// Resolve the base "lampiran" directory. In our setup the shared upload
		// folder is located one level above the "app" folder (FCPATH).
		// realpath() will return FALSE if the directory does not exist yet, so
		// we try the parent first and fall back to the original location.

		$base_lampiran_path = realpath(FCPATH.'../lampiran');

		// If the parent-level lampiran folder doesn't exist, we keep using the
		// path that is relative to FCPATH (inside the app directory).
		if ($base_lampiran_path === FALSE) {
			$base_lampiran_path = FCPATH.'lampiran';
		}

		// Ensure we have a trailing slash before appending sub-folder names
		$base_lampiran_path = rtrim($base_lampiran_path, '/\\');

		$config['upload_path'] = $base_lampiran_path.'/'.$db_name.'/';

		// Create the directory if it doesn't exist
		if (!is_dir($config['upload_path'])) {
			mkdir($config['upload_path'], 0755, true);
		}

		// Allow only PDF and common image formats, max 100 MB (102 400 KB)
		$config['allowed_types'] = 'pdf|jpeg|jpg|png';
		$config['max_size']      = 102400; // 100 MB in KB
		
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
