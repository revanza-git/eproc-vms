<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Agen extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('user')){
			redirect(site_url());
		}
		$this->load->model('Agen_model','am');
		$this->load->library('encrypt');
		$this->load->library('utility');
		
	}

	public function get_field(){
		return array(
			array(
				'label'	=>	'Pabrikan/Keagenan/Distributor',
				'filter'=>	array(
								array('table'=>'ms_agen|principal' ,'type'=>'text','label'=> 'Principal'),
								array('table'=>'ms_agen|issue_date' ,'type'=>'date','label'=> 'Tanggal'),
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

		$sort = $this->utility->generateSort(array('no','principal' ,'issue_date', 'type', 'expire_date','agen_file'));

		$data['izin_list']=$this->am->get_agen_list($search, $sort, $page, $per_page,TRUE);
		$data['filter_list'] 	= $this->filter->group_filter_post($this->get_field());

		$data['pagination'] = $this->utility->generate_page('izin',$sort, $per_page, $this->am->get_agen_list($search, $sort, '','',FALSE));
		$data['sort'] = $sort;

		$layout['content']= $this->load->view('content',$data,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$this->session->userdata('user'),TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$item['script'] = $this->load->view('dashboard/script2',NULL,TRUE);
		$this->load->view('template',$item);
	}
	
	public function tambah(){

		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$user = $this->session->userdata('user');
		
		$layout['form'] = $form;
		$vld = 	array(
					array(
						'field'=>'no',
						'label'=>'No',
						'rules'=>'required'
					),array(
						'field'=>'principal',
						'label'=>'Nama Principal',
						'rules'=>'required'
					),array(
						'field'=>'issue_date',
						'label'=>'Tanggal',
						'rules'=>'required'
					),array(
						'field'=>'expire_date',
						'label'=>'Tanggal',
						'rules'=>'required'
					),array(
						'field'=>'type',
						'label'=>'Pabrikan/Keagenan/Distributor',
						'rules'=>'required'
					),array(
						'field'=>'expire_date',
						'label'=>'Masa Berlaku',
						'rules'=>'required|backdate'
					),array(
						'field'=>'agen_file',
						'label'=>'Lampiran',
						'rules'=>'required'
					)
				);
		
		if(!empty($_FILES['agen_file']['name'])){
			$vld[] = array(
						'field'=>'agen_file',
						'label'=>'Lampiran',
						'rules'=>'callback_do_upload[agen_file]'
					);
		}
		$this->form_validation->set_rules($vld);
		if($this->input->post('simpan')){
			if($this->form_validation->run()==TRUE){
				unset($_POST['simpan']);

				$form['id_vendor'] = $user['id_user'];
				$form['entry_stamp'] = date('Y-m-d H:i:s');
				$this->session->set_userdata('form',array_merge($form,$this->input->post()));
				
				$result = $this->am->save_data($this->session->userdata('form'));
				if($result){
					$this->dpt->set_email_blast($result,'ms_agen','Akta Agen',$_POST['expire_date']);
					$this->dpt->non_iu_change($user['id_user']);	
					$this->session->unset_userdata('form');
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');
					redirect(site_url('agen'));
				}
			}
		}
		$layout['pabrik'] = array(
			'Pabrikan'=> 'Pabrikan',
			'Agen Tunggal'=> 'Agen Tunggal',
			'Distributor Tunggal'=> 'Distributor Tunggal',
			'Distributor'=>'Distributor'
		);
		$layout['content']= $this->load->view('tambah',$layout,TRUE);

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
						'field'=>'no',
						'label'=>'No',
						'rules'=>'required'
					),array(
						'field'=>'issue_date',
						'label'=>'Tanggal',
						'rules'=>'required'
					),array(
						'field'=>'expire_date',
						'label'=>'Tanggal',
						'rules'=>'required'
					),array(
						'field'=>'type',
						'label'=>'Pabrikan/Keagenan/Distributor',
						'rules'=>'required'
					),array(
						'field'=>'expire_date',
						'label'=>'Masa Berlaku',
						'rules'=>'required'
					)
				);
		

		if(!empty($_FILES['agen_file']['name'])){
			$vld[] = array(
						'field'=>'agen_file',
						'label'=>'Lampiran',
						'rules'=>'callback_do_upload[agen_file]'
					);
		}

		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);
			$this->dpt->edit_data($id,'ms_agen');
			$this->dpt->non_iu_change($user['id_user']);	
			$this->am->edit_data($this->input->post(),$id);

			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');

			redirect(site_url('agen'));
		}
		$data['pabrik'] = array(
			'Pabrikan'=> 'Pabrikan',
			'Agen Tunggal'=> 'Agen Tunggal',
			'Distributor Tunggal'=> 'Distributor Tunggal',
			'Distributor'=>'Distributor'
		);
		$layout['content']= $this->load->view('edit',$data,TRUE);

		$user = $this->session->userdata('user');
		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	
	public function hapus($id){
		if($this->am->delete($id)){
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('agen'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('agen'));
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
		$data['id_agen'] = $id;
		$search = $this->input->get('q');
		$page = '';
		
		$per_page=10;

		$sort = $this->utility->generateSort(array('id_bidang', 'id_sub_bidang'));

		$data['bsb_list']=$this->am->get_bsb_list($id, $search, $sort, $page, $per_page,TRUE);

		$data['pagination'] = $this->utility->generate_page('izin',$sort, $per_page, $this->am->get_bsb_list($id, $search, $sort, '','',FALSE));
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
		$data_izin = $this->am->get_dpt_agen($id);

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
				redirect(site_url('agen/formSubBidang/'.$id));
			
			}
		}

		$data['id_bidang'] = $this->am->get_bidang($data_izin['id_dpt_type']);


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
				$form['id_agen'] = $id;
				$form['id_vendor'] = $user['id_user'];
				$form['id_bsb'] = $_POST['id_sub_bidang'];
				$form['entry_stamp'] = date('Y-m-d H:i:s');

				$this->session->set_userdata('form',array_merge($form,$this->input->post()));
				
				$result = $this->am->save_bsb($this->session->userdata('form'));
				if($result){
					$this->dpt->non_iu_change($user['id_user']);
					$this->session->unset_userdata('form');
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data bidang!</p>');
					redirect(site_url('agen/bsb/'.$id));
				}
			}
		}elseif($this->input->post('back')){
			unset($_POST['back']);
			$form = $this->session->userdata('form');
			$this->session->set_userdata('form',array_merge($form,$this->input->post()));
			redirect(site_url('agen/formBidang/'.$id));
		}

		$data['id_sub_bidang'] = $this->am->get_sub_bidang($form['id_bidang']);


		$layout['content']= $this->load->view('form_sub_bidang',$data,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	


	/*Khusus Edit bsb*/
	public function formEditBidang($bsb,$id){

		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):$this->am->get_bsb_data($id);
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$user = $this->session->userdata('user');
		
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
				redirect(site_url('agen/formEditSubBidang/'.$bsb.'/'.$id));
			
			}
		}
		$data['id_bidang'] = $this->am->get_bidang($form['id_bidang']);
		$data['form'] = $form;
		$layout['content']= $this->load->view('form_bidang_edit',$data,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function formEditSubBidang($bsb,$id){

		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):$this->am->get_bsb_data($id);
		
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$user = $this->session->userdata('user');
		

		$vld = 	array(
					array(
						'field'=>'id_bsb',
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
				
				$result = $this->am->edit_bsb($this->session->userdata('form'),$id);
				if($result){
					$this->dpt->non_iu_change($user['id_user']);		
					$this->session->unset_userdata('form');
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data bidang!</p>');
					redirect(site_url('agen/bsb/'.$bsb.'/'.$bsb));
				}
			}
		}elseif($this->input->post('back')){
			unset($_POST['back']);
			$form = $this->session->userdata('form');
			$this->session->set_userdata('form',array_merge($form,$this->input->post()));
			redirect(site_url('agen/formEditBidang/'.$id));
		}

		$data['id_sub_bidang'] = $this->am->get_sub_bidang($form['id_bidang']);
		$data['id_bsb'] = $form['id_bsb'];


		$layout['content']= $this->load->view('form_sub_bidang_edit',$data,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	public function bsb_hapus($bsb,$id){
		if($this->am->delete_bsb($id)){
			$this->dpt->non_iu_change($user['id_user']);
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('izin/bsb/'.$bsb));
		}else{
			$this->session->set_flashdata('msgError','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('izin/bsb/'.$bsb));
		}
	}
	/*Edit Sub bidang*/



	public function produk($id){
		$data['id_agen'] = $id;
		$search = $this->input->get('q');
		$page = '';
		
		$per_page=10;

		$sort = $this->utility->generateSort(array('produk', 'merk'));

		$data['produk_list']=$this->am->get_produk_list($id, $search, $sort, $page, $per_page,TRUE);

		$data['pagination'] = $this->utility->generate_page('izin',$sort, $per_page, $this->am->get_produk_list($id, $search, $sort, '','',FALSE));
		$data['sort'] = $sort;

		$layout['content']= $this->load->view('produk',$data,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$this->session->userdata('user'),TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$item['script'] = $this->load->view('dashboard/script2',NULL,TRUE);
		$this->load->view('template',$item);
	}

	public function formProduk($id){
		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$user = $this->session->userdata('user');

		$vld = 	array(
					array(
						'field'=>'produk',
						'label'=>'Produk',
						'rules'=>'required'
					),
					array(
						'field'=>'merk',
						'label'=>'Merk',
						'rules'=>'required'
					),
				);
		$this->form_validation->set_rules($vld);
		if($this->input->post('simpan')){
			if($this->form_validation->run()==TRUE){
				$_POST['id_agen'] = $id;
				$result = $this->am->save_produk($this->input->post());
				if($result){
					$this->dpt->non_iu_change($user['id_user']);	
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data bidang!</p>');
					redirect(site_url('agen/produk/'.$id));
				}
			}
		}
		$layout['content']= $this->load->view('form_produk',NULL,TRUE);

		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function formEditProduk($produk,$id){

		$data = $this->am->get_data_produk($id);

		$_POST = $this->securities->clean_input($_POST,'save');
		$user = $this->session->userdata('user');
		$vld = 	array(
			array(
				'field'=>'produk',
				'label'=>'Produk',
				'rules'=>'required'
				),
			array(
				'field'=>'merk',
				'label'=>'Merk',
				'rules'=>'required'
				)
			);


		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['simpan']);
			$result = $this->am->edit_data_produk($this->input->post(),$id);
			if($result){
				$this->dpt->non_iu_change($user['id_user']);
			}
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');

			redirect(site_url('agen/produk/'.$produk));
		}

		

		$layout['content']= $this->load->view('produk_edit',$data,TRUE);

		$user = $this->session->userdata('user');
		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	public function produk_hapus($bsb,$id){
		if($this->am->delete_produk($id)){
			$this->dpt->non_iu_change($user['id_user']);
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('agen/produk/'.$bsb));
		}else{
			$this->session->set_flashdata('msgError','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('agen/produk/'.$bsb));
		}
	}
}