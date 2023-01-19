<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pengalaman extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('user')){
			redirect(site_url());
		}
		$this->load->model('Pengalaman_model','pm');
		$this->load->model('izin/Izin_model','im');
		$this->load->model('vendor/Vendor_model','vm');
		$this->load->library('encrypt');
		$this->load->library('utility');
		
	}

	public function get_field(){
		return array(
			array(
				'label'	=>	'Pengalaman',
				'filter'=>	array(
								array('table'=>'ms_pengalaman|job_name' ,'type'=>'text','label'=> 'Nama Paket Pekerjaan'),
								array('table'=>'ms_pengalaman|job_location' ,'type'=>'text','label'=> 'Lokasi'),
								array('table'=>'ms_pengalaman|job_giver' ,'type'=>'text','label'=> 'Pemberi Tugas'),
								array('table'=>'ms_pengalaman|phone_no' ,'type'=>'text','label'=> 'Nomor Telepon Pemberi Tugas'),
								array('table'=>'ms_pengalaman|price_idr' ,'type'=>'text','label'=> 'Harga Kontrak(IDR)'),
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

		$sort = $this->utility->generateSort(array('job_name', 'id_bidang', 'id_sub_bidang', 'job_location','job_giver', 'phone_no','contract_no','contract_start','price_idr','price_foreign','contract_end','contract_file','bast_date','bast_file'));

		$data['izin_list']=$this->pm->get_pengalaman_list($search, $sort, $page, $per_page,TRUE);
		$data['filter_list'] 	= $this->filter->group_filter_post($this->get_field());

		$data['pagination'] = $this->utility->generate_page('pengalaman',$sort, $per_page, $this->pm->get_pengalaman_list($search, $sort, '','',FALSE));
		$data['sort'] = $sort;

		$layout['content']= $this->load->view('content',$data,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$this->session->userdata('user'),TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$item['script'] = $this->load->view('dashboard/script2',NULL,TRUE);
		$this->load->view('template',$item);
	}

	public function siu(){ 
		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$user = $this->session->userdata('user');
		$layout['get_siu'] = $this->im->get_siu_dropdown();
		$layout['form'] = $form;
		$vld = 	array(
					array(
						'field'=>'id_ijin_usaha',
						'label'=>'Izin Usaha',
						'rules'=>'required'
					)
				);
		$this->form_validation->set_rules($vld);
		if($this->input->post('next')){
			if($this->form_validation->run()==TRUE){
				unset($_POST['next']);
				$this->session->set_userdata('form',array_merge($form,$this->input->post()));

				redirect('pengalaman/form_bsb');
			}
		}
		$layout['content']= $this->load->view('siu',$layout,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function form_bsb(){
		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$user = $this->session->userdata('user');
		$layout['get_bsb'] = $this->im->get_bsb_dropdown($form['id_ijin_usaha']);
		$vld = 	array(
					array(
						'field'=>'id_iu_bsb',
						'label'=>'Bidang/sub-Bidang',
						'rules'=>'required'
					)
				);
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			unset($_POST['next']);
			$this->session->set_userdata('form',array_merge($form,$this->input->post()));

			redirect('pengalaman/pengisian_data');
		}elseif($this->input->post('back')){
			unset($_POST['back']);
			$form = $this->session->userdata('form');
			$this->session->set_userdata('form',array_merge($form,$this->input->post()));
			redirect('pengalaman/siu');
		}
		$layout['content']= $this->load->view('bsb',$layout,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	
	public function pengisian_data(){

		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$user = $this->session->userdata('user');
		
		$layout['form'] = $form;
		$vld = 	array(
					array(
						'field'=>'job_name',
						'label'=>'Nama Paket Pekerjaan',
						'rules'=>'required'
					),array(
						'field'=>'job_location',
						'label'=>'Lokasi Pekerjaan',
						'rules'=>'required'
					),array(
						'field'=>'job_giver',
						'label'=>'Pemberi Tugas',
						'rules'=>'required'
					),array(
						'field'=>'phone_no',
						'label'=>'No. Telp Pemberi Tugas',
						'rules'=>''
					),array(
						'field'=>'contract_no',
						'label'=>'Nomor Kontrak',
						'rules'=>'required'
					),array(
						'field'=>'contract_start',
						'label'=>'Tanggal Kontrak',
						'rules'=>'required'
					),array(
						'field'=>'price_idr',
						'label'=>'Nilai Kontrak',
						'rules'=>''
					),array(
						'field'=>'price_foreign',
						'label'=>'Nilai Kontrak',
						'rules'=>''
					),array(
						'field'=>'currency',
						'label'=>'Mata Uang',
						'rules'=>''
					),array(
						'field'=>'contract_end',
						'label'=>'Tanggal Selesai',
						'rules'=>'required'
					),array(
						'field'=>'bast_date',
						'label'=>'Tanggal Selesai sesuai BAST',
						'rules'=>''
					),array(
						'field'=>'bast_file',
						'label'=>'Lampiran Dokumen BAST',
						'rules'=>''
					),array(
						'field'=>'contract_file',
						'label'=>'Lampiran Kontrak',
						'rules'=>'callback_do_upload[contract_file] | required'
					)
				);
		/*if(($_FILES['contract_file']['name'])){
			$vld[] = array(
						'field'=>'contract_file',
						'label'=>'Lampiran Kontrak',
						'rules'=>'callback_do_upload[contract_file]'
					);
		}*/
		$this->form_validation->set_rules($vld);
		if($this->input->post('simpan')){
			if($this->form_validation->run()==TRUE){
				// print_r($this->input->post());die;
				unset($_POST['simpan']);
				$form['id_vendor'] = $user['id_user'];
				$_POST['price_idr']	= preg_replace("/[,]/", "", $this->input->post('price_idr'));
				$_POST['price_foreign']	= preg_replace("/[,]/", "", $this->input->post('price_foreign'));
				$form['entry_stamp'] = date('Y-m-d H:i:s');
				$this->session->set_userdata('form',array_merge($form,$this->input->post()));
				
				$result = $this->pm->save_data($this->session->userdata('form'));
				if($result){
					$this->dpt->non_iu_change($user['id_user']);
					$this->session->unset_userdata('form');
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');
					redirect(site_url('pengalaman'));
				}
			}
		}elseif($this->input->post('back')){
			unset($_POST['back']);
			$form = $this->session->userdata('form');
			$this->session->set_userdata('form',array_merge($form,$this->input->post()));
			redirect('pengalaman/form_bsb');
		}
		$data['sbu'] = $this->vm->get_sbu();
		$data['curr'] = array(
								'USD'=>'USD',
								'JPY'=>'JPY',
								'CNY'=>'CNY',
								'EUR'=>'EUR',
							);
		$layout['content']= $this->load->view('pengisian_data',$data,TRUE);

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
						'field'=>'id_iu_bsb',
						'label'=>'Bidang/Sub Bidang',
						'rules'=>''
					),array(
						'field'=>'job_name',
						'label'=>'Nama Paket Pekerjaan',
						'rules'=>'required'
					),array(
						'field'=>'job_location',
						'label'=>'Lokasi Pekerjaan',
						'rules'=>'required'
					),array(
						'field'=>'job_giver',
						'label'=>'Pemberi Tugas',
						'rules'=>'required'
					),array(
						'field'=>'phone_no',
						'label'=>'No. Telp Pemberi Tugas',
						'rules'=>''
					),array(
						'field'=>'contract_no',
						'label'=>'Nomor Kontrak',
						'rules'=>'required'
					),array(
						'field'=>'contract_start',
						'label'=>'Tanggal Kontrak',
						'rules'=>'required'
					),array(
						'field'=>'price_idr',
						'label'=>'Nilai Kontrak',
						'rules'=>'required'
					),array(
						'field'=>'price_foreign',
						'label'=>'Nilai Kontrak',
						'rules'=>''
					),array(
						'field'=>'currency',
						'label'=>'Mata Uang',
						'rules'=>''
					),array(
						'field'=>'contract_end',
						'label'=>'Tanggal Selesai',
						'rules'=>'required'
					),array(
						'field'=>'bast_date',
						'label'=>'Tanggal Selesai sesuai BAST',
						'rules'=>''
					)
				);
		
		if(!empty($_FILES['contract_file']['name'])){
			$vld[] = array(
						'field'=>'contract_file',
						'label'=>'Lampiran Kontrak',
						'rules'=>'callback_do_upload[contract_file]'
					);
		}
		if(!empty($_FILES['bast_file']['name'])){
			$vld[] = array(
						'field'=>'bast_file',
						'label'=>'Lampiran Dokumen BAST',
						'rules'=>'callback_do_upload[bast_file]'
					);
		}

		$this->form_validation->set_rules($vld);
		
		if($this->form_validation->run()==TRUE){
			$_POST['price_idr']	= preg_replace("/[,]/", "", $this->input->post('price_idr'));
			$_POST['price_foreign']	= preg_replace("/[,]/", "", $this->input->post('price_foreign'));
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);

			$result = $this->pm->edit_data($this->input->post(),$id);

			if($result){
				$this->dpt->edit_data($id,'ms_pengalaman');
				$this->dpt->non_iu_change($user['id_user']);
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('pengalaman'));
			}
		}

		$data['sbu'] = $this->vm->get_sbu();
		$data['curr'] = array(
							'USD'=>'USD',
							'JPY'=>'JPY',
							'CNY'=>'CNY',
							'EUR'=>'EUR',
						);
		$data['get_bsb'] = $this->im->get_bsb_dropdown($data['id_ijin_usaha']);
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
			redirect(site_url('pengalaman'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('pengalaman'));
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
