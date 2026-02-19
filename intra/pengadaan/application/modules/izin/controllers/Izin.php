<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Izin extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('user')){
			redirect(site_url());
		}
		$this->load->model('Izin_model','im');
		$this->load->library('encrypt');
		$this->load->library('utility');
		
	}

	public function get_field(){
		return array(
			array(
				'label'	=>	'Izin Usaha',
				'filter'=>	array(
								// array('table'=>'ms_ijin_usaha|type' ,'type'=>'text','label'=> 'Jenis Akta'),
								array('table'=>'ms_ijin_usaha|id_dpt_type|get_dpt_type' ,'type'=>'dropdown','label'=> 'Klasifikasi Penyedia Barang/Jasa'),
								array('table'=>'ms_ijin_usaha|type|get_izin_type' ,'type'=>'dropdown','label'=> 'Tipe Izin'),
								array('table'=>'ms_ijin_usaha|no' ,'type'=>'text','label'=> 'No.'),
								array('table'=>'ms_ijin_usaha|issue_date' ,'type'=>'date','label'=> 'Tanggal'),
								array('table'=>'ms_ijin_usaha|qualification|get_kualifikasi' ,'type'=>'dropdown','label'=> 'Kualifikasi'),
								array('table'=>'ms_ijin_usaha|authorize_by' ,'type'=>'text','label'=> 'Lembaga Penerbit'),
								// array('table'=>'ms_ijin_usaha|expire_date' ,'type'=>'date','label'=> 'Masa Berlaku'),
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

		$sort = $this->utility->generateSort(array('dpt_name', 'type', 'no', 'issue_date','qualification', 'authorize_by','izin_file','expire_date'));

		$data['izin_list']=$this->im->get_izin_list($search, $sort, $page, $per_page,TRUE);
		$data['filter_list'] 	= $this->filter->group_filter_post($this->get_field());

		$data['pagination'] = $this->utility->generate_page('izin',$sort, $per_page, $this->im->get_izin_list($search, $sort, '','',FALSE));
		$data['sort'] = $sort;
		$data['siu'] = array('siup'=>'SIUP','ijin_lain'=>'Surat Izin Usaha Lainnya','asosiasi'=>'Sertifikat Asosiasi/Lainnya','siujk'=>'SIUJK','sbu'=>'SBU');
		
		$layout['content']= $this->load->view('content',$data,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$this->session->userdata('user'),TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$item['script'] = $this->load->view('dashboard/script2',NULL,TRUE);
		$this->load->view('template',$item);
	}
	public function klasifikasi(){
		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$user = $this->session->userdata('user');
		$layout['get_dpt_type'] = $this->im->get_dpt_type();
		$vld = 	array(
					array(
						'field'=>'id_dpt_type',
						'label'=>'Jenis',
						'rules'=>'required'
					)
				);
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			unset($_POST['next']);
			$this->session->set_userdata('form',array_merge($form,$this->input->post()));

			redirect('izin/siu');
		}
		$layout['content']= $this->load->view('klasifikasi',$layout,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	public function siu(){
		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$user = $this->session->userdata('user');
		$layout['siu'] = array('siup'=>'SIUP','ijin_lain'=>'Surat Ijin Usaha Lainnya','asosiasi'=>'Sertifikat Asosiasi/Lainnya','siujk'=>'SIUJK','sbu'=>'SBU');
		$layout['dpt'] = array(1=>array('siup','ijin_lain','asosiasi'),2=>array('siup','ijin_lain','asosiasi'),3=>array('siup','ijin_lain','asosiasi'),4=>array('siujk','sbu','asosiasi'),5=>array('siujk','sbu','asosiasi'));
		
		$layout['form'] = $form;
		$vld = 	array(
					array(
						'field'=>'type',
						'label'=>'Jenis Izin',
						'rules'=>'required'
					)
				);
		$this->form_validation->set_rules($vld);
		if($this->input->post('next')){
			if($this->form_validation->run()==TRUE){
				unset($_POST['next']);
				$this->session->set_userdata('form',array_merge($form,$this->input->post()));
				
				redirect('izin/pengisian_data');
			}
		}elseif($this->input->post('back')){
			unset($_POST['back']);
			$form = $this->session->userdata('form');
			$this->session->set_userdata('form',array_merge($form,$this->input->post()));
			redirect('izin/klasifikasi');
		}
		$layout['content']= $this->load->view('siu',$layout,TRUE);

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
						'field'=>'no',
						'label'=>'Nomor',
						'rules'=>'required'
					),array(
						'field'=>'issue_date',
						'label'=>'Tanggal',
						'rules'=>'required'
					),array(
						'field'=>'expire_date',
						'label'=>'Tanggal',
						'rules'=>'required|backdate'
					)
				);
		if(isset($form['authorize_by'])){
			$vld[] = array(
						'field'=>'authorize_by',
						'label'=>'Lembaga',
						'rules'=>''
					);
		}
		if(isset($form['qualification'])){
			$vld[] = array(
						'field'=>'qualification',
						'label'=>'Kualifikasi',
						'rules'=>'required'
					);
		}
		if(!empty($_FILES['izin_file']['name'])){
			$vld[] = array(
						'field'=>'izin_file',
						'label'=>'Lampiran',
						'rules'=>'callback_do_upload[izin_file]'
					);
		}
		$this->form_validation->set_rules($vld);
		
		if($this->input->post('simpan')){
			if($this->form_validation->run()==TRUE){
				unset($_POST['simpan']);
				$form['id_vendor'] = $user['id_user'];
				$form['entry_stamp'] = date('Y-m-d H:i:s');
				$this->session->set_userdata('form',array_merge($form,$this->input->post()));
				
				$result = $this->im->save_data($this->session->userdata('form'));
				if($result){
					$this->dpt->set_email_blast($result,'ms_ijin_usaha','Izin Usaha',$_POST['expire_date']);
					$this->dpt->iu_add($result);
					$this->dpt->iu_change($result);
					$this->session->unset_userdata('form');
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');
					redirect(site_url('izin/'));
				}else{
					$this->session->set_flashdata('errorMsg','<p class="errorMsg">Anda telah memiliki izin usaha yang sama!</p>');
				}
			}
		}elseif($this->input->post('back')){
			unset($_POST['back']);
			$form = $this->session->userdata('form');
			$this->session->set_userdata('form',array_merge($form,$this->input->post()));
			redirect('izin/siu');
		}
		$layout['content']= $this->load->view('pengisian_data',$layout,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	public function edit($id){
		$data = $this->im->get_data($id);
		// echo print_r($data);
		$_POST = $this->securities->clean_input($_POST,'save');
		$user = $this->session->userdata('user');
		$vld = 	array(
					array(
						'field'=>'no',
						'label'=>'Nomor',
						'rules'=>'required'
					),array(
						'field'=>'issue_date',
						'label'=>'Tanggal',
						'rules'=>'required'
					),array(
						'field'=>'expire_date',
						'label'=>'Tanggal',
						'rules'=>'required|backdate'
					)
			);
		if(isset($form['authorize_by'])){
			$vld[] = array(
						'field'=>'authorize_by',
						'label'=>'Lembaga',
						'rules'=>'required'
					);
		}
		if(isset($form['qualification'])){
			$vld[] = array(
						'field'=>'qualification',
						'label'=>'Kualifikasi',
						'rules'=>'required'
					);
		}

		if(!empty($_FILES['izin_file']['name'])){
			$vld[] = array(
					'field'=>'izin_file',
					'label'=>'Lampiran',
					'rules'=>'callback_do_upload[izin_file]'
					);
		}

		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);

			$res = $this->im->edit_data($this->input->post(),$id);

			if($res){
				$this->dpt->edit_data($id,'ms_ijin_usaha');
				$this->dpt->iu_change($id);
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('izin'));
			}
		}



		$layout['content']= $this->load->view('edit',$data,TRUE);

		$user = $this->session->userdata('user');
		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	
	public function hapus($id){
		if($this->im->delete($id)){
			$this->dpt->non_iu_change($user['id_user']);
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('izin'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('izin'));
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

	public function bsb($id){
		$data['id_bsb'] = $id;
		$search = $this->input->get('q');
		$page = '';
		
		$per_page=10;

		$sort = $this->utility->generateSort(array('id_bidang', 'id_sub_bidang'));

		$data['bsb_list']=$this->im->get_bsb_list($id, $search, $sort, $page, $per_page,TRUE);

		$data['pagination'] = $this->utility->generate_page('izin',$sort, $per_page, $this->im->get_bsb_list($id, $search, $sort, '','',FALSE));
		$data['sort'] = $sort;

		$layout['content']= $this->load->view('bsb',$data,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$this->session->userdata('user'),TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$item['script'] = $this->load->view('dashboard/script2',NULL,TRUE);
		$this->load->view('template',$item);
	}

	public function formBidang($id){
		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$user = $this->session->userdata('user');
		$data_izin = $this->im->get_data($id);

		$vld = 	array(
					array(
						'field'=>'id_bidang',
						'label'=>'Bidang',
						'rules'=>'required'
					)
				);
		$this->form_validation->set_rules($vld);
		if($this->input->post('next')){
			if($this->form_validation->run()==TRUE){
				
				$this->session->set_userdata('form',array_merge($form,$this->input->post()));
				redirect(site_url('izin/formSubBidang/'.$id));
			
			}
		}

		$data['id_bidang'] = $this->im->get_bidang($data_izin['id_dpt_type']);


		$layout['content']= $this->load->view('form_bidang',$data,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	public function formSubBidang($id){

		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$user = $this->session->userdata('user');
		

		$vld = 	array(
					array(
						'field'=>'id_sub_bidang',
						'label'=>'Sub Bidang',
						'rules'=>'required'
					)
				);
		$this->form_validation->set_rules($vld);
		if($this->input->post('simpan')){
			if($this->form_validation->run()==TRUE){
				unset($_POST['simpan']);
				$form['id_ijin_usaha'] = $id;
				$form['id_vendor'] = $user['id_user'];
				$form['entry_stamp'] = date('Y-m-d H:i:s');

				$this->session->set_userdata('form',array_merge($form,$this->input->post()));
				
				$result = $this->im->save_bsb($this->session->userdata('form'));
				if($result){
					$this->dpt->non_iu_change($user['id_user']);
					$this->session->unset_userdata('form');
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data bidang!</p>');
					redirect(site_url('izin/bsb/'.$id));
				}
			}
		}elseif($this->input->post('back')){
			unset($_POST['back']);
			$form = $this->session->userdata('form');
			$this->session->set_userdata('form',array_merge($form,$this->input->post()));
			redirect(site_url('izin/formBidang/'.$id));
		}

		$data['id_sub_bidang'] = $this->im->get_sub_bidang($form['id_bidang']);


		$layout['content']= $this->load->view('form_sub_bidang',$data,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	


	/*Khusus Edit bsb*/
	public function formEditBidang($bsb,$id){

		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):$this->im->get_bsb_data($id);
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$user = $this->session->userdata('user');
		$data_izin = $this->im->get_data($bsb);

		$vld = 	array(
					array(
						'field'=>'id_bidang',
						'label'=>'Bidang',
						'rules'=>'required'
					)
				);
		$this->form_validation->set_rules($vld);
		if($this->input->post('next')){
			if($this->form_validation->run()==TRUE){
				unset($_POST['next']);
				$this->session->set_userdata('form',array_merge($form,$this->input->post()));
				redirect(site_url('izin/formEditSubBidang/'.$bsb.'/'.$id));
			
			}
		}
		$data['id_bidang'] = $this->im->get_bidang($data_izin['id_dpt_type']);
		$data['form'] = $form;
		$layout['content']= $this->load->view('form_bidang_edit',$data,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function formEditSubBidang($bsb,$id){

		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):$this->im->get_bsb_data($id);
		
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$user = $this->session->userdata('user');
		

		$vld = 	array(
					array(
						'field'=>'id_sub_bidang',
						'label'=>'Sub Bidang',
						'rules'=>'required'
					)
				);
		$this->form_validation->set_rules($vld);
		if($this->input->post('simpan')){
			if($this->form_validation->run()==TRUE){
				unset($_POST['simpan']);
				
				$form['edit_stamp'] = date('Y-m-d H:i:s');

				$this->session->set_userdata('form',array_merge($form,$this->input->post()));
				
				$result = $this->im->edit_bsb($this->session->userdata('form'),$id);
				if($result){
					$this->dpt->non_iu_change($user['id_user']);
					$this->session->unset_userdata('form');
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data bidang!</p>');
					redirect(site_url('izin/bsb/'.$bsb));
				}
				
			}
		}elseif($this->input->post('back')){
			unset($_POST['back']);
			$form = $this->session->userdata('form');
			$this->session->set_userdata('form',array_merge($form,$this->input->post()));
			redirect(site_url('izin/formEditBidang/'.$id));
		}

		$data['id_sub_bidang'] = $this->im->get_sub_bidang($form['id_bidang']);


		$layout['content']= $this->load->view('form_sub_bidang_edit',$data,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	public function bsb_hapus($bsb,$id){
		if($this->im->delete_bsb($id)){
			$this->dpt->non_iu_change($user['id_user']);
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('izin/bsb/'.$bsb));
		}else{
			$this->session->set_flashdata('msgError','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('izin/bsb/'.$bsb));
		}
	}
	/*Edit Sub bidang*/
}
