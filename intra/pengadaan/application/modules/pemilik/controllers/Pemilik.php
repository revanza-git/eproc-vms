<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pemilik extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('user')){
			redirect(site_url());
		}
		$this->load->model('pemilik_model','pm');
		$this->load->library('encrypt');
		$this->load->library('utility');
		
	}
	public function get_field(){
		return array(
			array(
				'label'	=>	'Pemilik Modal',
				'filter'=>	array(
								// array('table'=>'ms_pemilik|type' ,'type'=>'text','label'=> 'Jenis Akta'),
								array('table'=>'msp|name' ,'type'=>'text','label'=> 'Nama'),
								array('table'=>'msp|shares' ,'type'=>'text','label'=> 'Saham Dalam Lembar'),
								array('table'=>'msp|percentage' ,'type'=>'text','label'=> 'Nilai Kepemilikan (Rupiah)'),
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

		$sort = $this->utility->generateSort(array('name', 'shares', 'percentage'));

		$data['situ_list']	= $this->pm->get_pemilik_list($search, $sort, $page, $per_page,TRUE);
		$data['filter_list']= $this->filter->group_filter_post($this->get_field());
		$data['pagination'] = $this->utility->generate_page('pemilik',$sort, $per_page, $this->pm->get_pemilik_list($search, $sort, '','',FALSE));
		$data['sort'] 		= $sort;

		$layout['content']= $this->load->view('content',$data,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$this->session->userdata('user'),TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	
	public function tambah(){
		$_POST 	= $this->securities->clean_input($_POST,'save');
		$user 	= $this->session->userdata('user');
		$vld 	= 	array(
			array(
				'field'=>'name',
				'label'=>'Nama',
				'rules'=>'required'
				),
			array(
				'field'=>'id_akta',
				'label'=>'Pilih Nomor Akta',
				'rules'=>'required'
				),
			array(
				'field'=>'shares',
				'label'=>'Komposisi Saham dalam lembar',
				'rules'=>'required'
				),
			array(
				'field'=>'percentage',
				'label'=>'Nominal Saham',
				'rules'=>'required'
				)
			);
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['id_vendor'] 	= $user['id_user'];
			$_POST['entry_stamp'] 	= date("Y-m-d H:i:s");
			$_POST['percentage']	= preg_replace("/[,]/", "", $this->input->post('percentage'));
			$_POST['data_status'] 	= 0;
			$result 				= $this->pm->save_data($this->input->post());
			
			if($result){
				$this->dpt->non_iu_change($user['id_user']);
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');
				redirect(site_url('pemilik'));
			}
		}

		$data['id_akta']	= $this->pm->get_akta_list();
		$layout['content']	= $this->load->view('tambah',$data,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function check_total_percentage($field,$id=0){
		$total = $this->pm->get_total_percentage($id);

		if(($total+$this->input->post('percentage'))>100){
			$this->form_validation->set_message('check_total_percentage','Masukkan persentase kepemilikan dengan benar');
			return false;
		}		
		else{
			return true;
		}
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
			/*array(
				'field'=>'position',
				'label'=>'Jabaran',
				'rules'=>'required'
				),*/
			array(
				'field'=>'percentage',
				'label'=>'Nominal',
				'rules'=>'required'
				),
				
			array(
				'field'=>'shares',
				'label'=>'Komposisi Saham dalam lembar',
				'rules'=>'required'
				)
			);
		//print_r($this->input->post());
		//print_r($id);
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			$_POST['percentage']	= preg_replace("/[,]/", "", $this->input->post('percentage'));
			unset($_POST['Update']);
			unset($_POST['Simpan']);

			$result = $this->pm->edit_data($this->input->post(),$id);

			if($result){
				$this->dpt->edit_data($id,'ms_pemilik');
				$this->dpt->non_iu_change($user['id_user']);
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('pemilik'));
			}else{
				$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal Mengubah data!!</p>');
				redirect(site_url('pemilik'));
			}

		}

		$data['id_aktar']	= $this->pm->get_akta_list();

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
			redirect(site_url('pemilik'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('pemilik'));
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
