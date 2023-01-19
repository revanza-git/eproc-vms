<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pengurus extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('user')){
			redirect(site_url());
		}
		$this->load->model('pengurus_model','pm');
		$this->load->library('encrypt');
		$this->load->library('utility');
		$this->load->library('form_validation');
	}

	public function get_field(){
		return array(
			array(
				'label'	=>	'Pengurus Perusahaan',
				'filter'=>	array(
								array('table'=>'ms_pengurus|position' ,'type'=>'text','label'=> 'Posisi'),
								array('table'=>'ms_pengurus|no' ,'type'=>'text','label'=> 'No. Identitas'),
								array('table'=>'ms_pengurus|name' ,'type'=>'text','label'=> 'Nama'),
								// array('table'=>'ms_pengurus|expire_date' ,'type'=>'date','label'=> 'Masa Berlaku Identitas'),
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

		$sort = $this->utility->generateSort(array('no', 'no_akta', 'name', 'expire_date', 'position', 'position_expire','pengurus_file'));

		$data['situ_list']=$this->pm->get_pengurus_list($search, $sort, $page, $per_page,TRUE);
		$data['filter_list'] 	= $this->filter->group_filter_post($this->get_field());
		$data['pagination'] = $this->utility->generate_page('pengurus',$sort, $per_page, $this->pm->get_pengurus_list($search, $sort, '','',FALSE));
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
				'field'=>'name',
				'label'=>'Nama',
				'rules'=>'required'
				),
			array(
				'field'=>'no',
				'label'=>'Nomor Identitas (KTP/Passport/KITAS)',
				'rules'=>'required'
				),
			array(
				'field'=>'expire_date',
				'label'=>'Tanggal',
				'rules'=>'required|backdate'
				),
			array(
				'field'=>'position',
				'label'=>'Jabatan',
				'rules'=>'required'
				),
			// array(
			// 	'field'=>'position_expire',
			// 	'label'=>'Masa Berlaku Jabatan',
			// 	'rules'=>'required'
			// 	),
			array(
				'field'=>'id_akta',
				'label'=>'No. Angka Pengangkatan',
				'rules'=>'required'
				),
			array(
				'field'=>'pengurus_file',
				'label'=>'Lampiran',
				'rules'=>'callback_do_upload[pengurus_file]'
				)
			);
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){

			$_POST['id_vendor'] = $user['id_user'];
			$_POST['entry_stamp'] = date("Y-m-d H:i:s");
			$_POST['data_status'] = 0;

			$res = $this->pm->save_data($this->input->post());
			if($res){
				$this->dpt->set_email_blast($res,'ms_pengurus','Pengurus Perusahaan',$_POST['expire_date']);
				$this->dpt->non_iu_change($user['id_user']);
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');

				redirect(site_url('pengurus'));
			}
		}
		$data['id_akta'] = $this->pm->get_akta($user['id_user']);
		
		$layout['content']= $this->load->view('tambah',$data,TRUE);


		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function edit($id){
		$data = $this->pm->get_data($id);
		$_POST = $this->securities->clean_input($_POST,'save');
		$user = $this->session->userdata('user');
		$vld = 	array(
			array(
				'field'=>'name',
				'label'=>'Nama',
				'rules'=>'required'
				),
			array(
				'field'=>'no',
				'label'=>'Nomor Identitas (KTP/Passport/KITAS)',
				'rules'=>'required'
				),
			array(
				'field'=>'expire_date',
				'label'=>'Tanggal',
				'rules'=>'required|backdate'
				),
			array(
				'field'=>'position',
				'label'=>'Jabatan',
				'rules'=>'required'
				),
			// array(
			// 	'field'=>'position_expire',
			// 	'label'=>'Masa Berlaku Jabatan',
			// 	'rules'=>'required'
			// 	),
			array(
				'field'=>'id_akta',
				'label'=>'No. Angka Pengangkatan',
				'rules'=>'required'
				),
			
			);

		if(!empty($_FILES['pengurus_file']['name'])){
			$vld[] = array(
				'field'=>'pengurus_file',
				'label'=>'Lampiran',
				'rules'=>'callback_do_upload[pengurus_file]'
				);
		}

		$data['akta'] = $this->pm->get_akta($user['id_user']);
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);

			$res = $this->pm->edit_data($this->input->post(),$id);
			if($res){
				$this->dpt->edit_data($id,'ms_pengurus');
				$this->dpt->non_iu_change($user['id_user']);
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('pengurus'));
			}
		}



		$layout['content']= $this->load->view('edit',$data,TRUE);

		$user = $this->session->userdata('user');
		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	public function hapus($id){
		if($this->pm->delete($id)){
			$this->dpt->non_iu_change($user['id_user']);
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('pengurus'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('pengurus'));
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
