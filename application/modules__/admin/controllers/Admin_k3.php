<?php defined('BASEPATH') OR exit('No direct script access allowed');

class admin_k3 extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('admin')){
			redirect(site_url());
		}
		$this->load->model('k3/k3_model','km');
	}
	public function index(){	
		$search 	= $this->input->get('q');
		$page 		= '';
		$per_page	= 10;
		$sort 		= $this->utility->generateSort(array('id_bidang','name'));


		$data['ms_quest']		= $this->km->get_header_list();
		$data['sub_quest']		= $this->km->get_sub_quest_list();
		$data['data_quest']		= $this->km->get_quest_list();
		$data['data_field']		= $this->km->get_data_field();
		$data['evaluasi']		= $this->km->get_evaluasi_data_list();


		$data['quest_all']		= array();

		foreach($data['ms_quest'] as $key_ms => $row_ms){
			$data['quest_all'][$key_ms]['label'] 	= $row_ms;
		}

		foreach($data['sub_quest'] as $key_sub_quest => $val_sub_quest){
			foreach($val_sub_quest as $k_sub_quest => $v_sub_quest){
				$data['quest_all'][$key_sub_quest]['data'][$k_sub_quest] = $v_sub_quest;
			}
		}

		foreach($data['data_quest'] as $key_quest => $val_quest){
			$data['quest_all'][$val_quest['id_ms_header']]['data'][$val_quest['id_sub_header']]['data'][$val_quest['id']] = array();
		}
		// foreach()

		foreach ($data['data_field'] as $key_data => $value_data) {
			$data['quest_all'][$value_data['id_ms_header']]['data'][$value_data['id_sub_header']]['data'][$value_data['id_question']][$value_data['id']] = $value_data;	
		}

		$vld 			= array(
				array(
					'field'=>'id_evaluasi',
					'label'=>'Pilih Evaluasi Untuk Penilaian',
					'rules'=>'required'
					),
			);

		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			unset($_POST['newGroup']);
			$_POST['entry_stamp'] = date("Y-m-d H:i:s");
			// print_r($this->input->post());
			$res = $this->km->save_group_quest($this->input->post());
			$idh = $this->db->insert_id();
			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah kelompok pertanyaan! <a href="#'.$idh.'">klik disini</a></p>');
				redirect(site_url('admin/admin_k3'));
			}
		}
		// echo print_r($data['quest_all']);
		$layout['content']	= $this->load->view('k3/content',$data,TRUE);

		$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	###################################################
	################  		K3		 	###############
	###################################################



	public function edit_header($id){
		// $data 			= $this->km->get_data_assessment($id);
		$data['header']		= $this->km->get_header($id);
		
		$_POST 			= $this->securities->clean_input($_POST,'save');
		$admin 			= $this->session->userdata('admin');
		$vld 			= array(
				array(
					'field'=>'question',
					'label'=>'Nama Header',
					'rules'=>'required'
					),
			);

		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);

			$res = $this->km->save_edit_header($this->input->post(),$id);

			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('admin/admin_k3'));
			}
		}

		$layout['content']	= $this->load->view('k3/edit_header',$data,TRUE);

		$admin 				= $this->session->userdata('admin');
		$item['header'] 	= $this->load->view('admin/header',$admin,TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function edit_sub_quest($id){
		$data['sub_quest']	= $this->km->get_edit_sub_quest($id);
		
		$_POST 			= $this->securities->clean_input($_POST,'save');
		$admin 			= $this->session->userdata('admin');
		$vld 			= array(
				array(
					'field'=>'question',
					'label'=>'Nama Sub Quest',
					'rules'=>'required'
					),
			);

		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);

			$res = $this->km->save_edit_sub_quest($this->input->post(),$id);

			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('admin/admin_k3'));
			}
		}

		$layout['content']	= $this->load->view('k3/edit_sub_quest',$data,TRUE);

		$admin 				= $this->session->userdata('admin');
		$item['header'] 	= $this->load->view('admin/header',$admin,TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function edit_quest($id){
		$data['quest']	= $this->km->get_edit_quest($id);
		// echo print_r($data['quest']);
		

		$admin 	= $this->session->userdata('admin');
		$vld 	= array(
			array(
				'field'=>'value',
				'label'=>'Pertanyaan',
				'rules'=>'required'
				),
			array(
				'field'=>'type',
				'label'=>'Tipe',
				'rules'=>'required'
				),
			);

			if ($this->input->post('type')=="file"){
				$vld[] = array(
						'field'=>'labelfield',
						'label'=>'Kolom Label',
						'rules'=>''
						);

				$label 			= strtolower($_POST['labelfield']);
				$file			= explode(" ", $label);

				$_POST['label'] = implode("_", $file);
				unset($_POST['labelfield']);
			}

			if ($this->input->post('type')=="radio"){
				$vld[] = array(
						'field'=>'labelfield',
						'label'=>'Kolom Label',
						'rules'=>''
						);

				$label 			= $_POST['labelfield'];
				$_POST['label'] = implode("|", $label);
				unset($_POST['labelfield']);
			}

		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);

			$res = $this->km->save_edit_quest($this->input->post(),$id);

			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('admin/admin_k3'));
			}
		}

		$layout['content']	= $this->load->view('k3/edit_quest',$data,TRUE);

		$admin 				= $this->session->userdata('admin');
		$item['header'] 	= $this->load->view('admin/header',$admin,TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function hapus_header($id){
		if($this->km->hapus_header($id)){
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('admin/admin_k3'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('admin/admin_k3'));
		}
	}

	public function hapus_sub_quest($id){
		if($this->km->hapus_sub_quest($id)){
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('admin/admin_k3'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('admin/admin_k3'));
		}
	}
	public function hapus_quest($id){
		if($this->km->hapus_quest($id)){
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('admin/admin_k3'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('admin/admin_k3'));
		}
	}


	public function tambah_header(){
		// $_POST	= $this->securities->clean_input($_POST,'save');
		$admin 	= $this->session->userdata('admin');
		$vld 	= array(
			array(
				'field'=>'question',
				'label'=>'Nama Header',
				'rules'=>'required'
				)
			);
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			unset($_POST['Simpan']);
			$_POST['entry_stamp'] 	= date("Y-m-d H:i:s");
			$res = $this->km->save_header($this->input->post());
			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');
				redirect(site_url('admin/admin_k3'));
			}
			
		}

		$layout['content']	= $this->load->view('k3/tambah_header',NULL,TRUE);

		$item['header'] 	= $this->load->view('admin/header',$admin,TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function tambah_sub_quest($id){
		// $_POST	= $this->securities->clean_input($_POST,'save');
		$admin 	= $this->session->userdata('admin');
		$vld 	= array(
			array(
				'field'=>'question',
				'label'=>'Nama Sub Quest',
				'rules'=>'required'
				)
			);
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			unset($_POST['Simpan']);
			$_POST['entry_stamp'] 	= date("Y-m-d H:i:s");
			$_POST['id_header']		= $id;
			$res = $this->km->save_sub_quest($this->input->post());
			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');
				redirect(site_url('admin/admin_k3'));
			}else{
				$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menambah data!</p>');
			}
			
		}

		$layout['content']	= $this->load->view('k3/tambah_sub_quest',NULL,TRUE);

		$item['header'] 	= $this->load->view('admin/header',$admin,TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function tambah_quest($id){
		$admin 	= $this->session->userdata('admin');
		$vld 	= array(
			array(
				'field'=>'value',
				'label'=>'Pertanyaan',
				'rules'=>'required'
				),
			array(
				'field'=>'type',
				'label'=>'Tipe',
				'rules'=>'required'
				),
			);

			if ($this->input->post('type')=="file"){
				$vld[] = array(
						'field'=>'labelfield',
						'label'=>'Kolom Label',
						'rules'=>''
						);

				$label 			= strtolower($_POST['labelfield']);
				$file			= explode(" ", $label);

				$_POST['label'] = implode("_", $file);
				unset($_POST['labelfield']);
			}

			if ($this->input->post('type')=="radio"){
				$vld[] = array(
						'field'=>'labelfield',
						'label'=>'Kolom Label',
						'rules'=>''
						);

				$label 			= $_POST['labelfield'];
				$_POST['label'] = implode("|", $label);
				unset($_POST['labelfield']);
			}
		
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			unset($_POST['Simpan']);
			$idsh = 0;

			$_POST['entry_stamp'] 	= date("Y-m-d H:i:s");
			$_POST['id_question']	= $id;
			$res = $this->km->save_quest($this->input->post());
			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');
				redirect(site_url('admin/admin_k3'));
			}
		}
			// print_r($this->input->post());


		$layout['content']	= $this->load->view('k3/tambah_quest',NULL,TRUE);
		$item['header'] 	= $this->load->view('admin/header',$admin,TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function tambah_group_quest($idh, $idsh=0){
		// $_POST	= $this->securities->clean_input($_POST,'save');
		$admin 	= $this->session->userdata('admin');

			unset($_POST['Simpan']);
			$_POST['entry_stamp'] 		= date("Y-m-d H:i:s");
			$_POST['id_ms_header']		= $idh;
			$_POST['id_sub_header']		= $idsh;
			$res = $this->km->save_group_quest($this->input->post());
			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah kelompok pertanyaan! <a href="#'.$idh.'">klik disini</a></p>');
				redirect(site_url('admin/admin_k3'));
			}
	}

	public function edit_group_quest($id){
		$data['header']			= $this->km->get_header_dropdown($id);
		$data['sub_header']		= $this->km->get_sub_header_dropdown($id);
		$data['evaluasi']		= $this->km->get_evaluasi_data_list($id);
		$data['quest']			= $this->km->get_evaluasi_edit($id);
		
		$_POST 			= $this->securities->clean_input($_POST,'save');
		$admin 			= $this->session->userdata('admin');
		$vld 			= array(
				array(
					'field'=>'id_ms_header',
					'label'=>'Pilih Header',
					'rules'=>'required'
					),
				array(
					'field'=>'id_sub_header',
					'label'=>'Pilih Sub Header',
					'rules'=>'required'
					),
				array(
					'field'=>'id_evaluasi',
					'label'=>'Pilih Evaluasi',
					'rules'=>'required'
					)
			);

		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Simpan']);

			$res = $this->km->save_edit_group($this->input->post(),$id);
			print_r($this->input->post());
			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('admin/admin_k3'));
			}
		}

		$layout['content']	= $this->load->view('k3/edit_group_quest',$data,TRUE);

		$admin 				= $this->session->userdata('admin');
		$item['header'] 	= $this->load->view('admin/header',$admin,TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function hapus_group_quest($id){
		if($this->km->hapus_group_quest($id)){
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('admin/admin_k3'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('admin/admin_k3'));
		}
	}
}