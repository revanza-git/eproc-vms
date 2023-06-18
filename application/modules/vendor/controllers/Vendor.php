<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('user')){
			redirect(site_url());
		}
		$this->load->model('Vendor_model','vm');
		$this->load->model('izin/izin_model','im');
		$this->load->model('k3/K3_model','km');
		$this->load->library('encrypt');
		$this->load->library('utility');
		$this->load->library('email');
		
	}

	public function index()
	{
		$this->session->sess_destroy();
		$this->load->view('template/login');
	}

	public function suratPernyataan(){
		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		
		$fill = $this->securities->clean_input($_POST, 'view');

		$item = $vld = $save_data = array();

		
		
		$data['id_legal'] = $this->vm->get_legal();
		$item['content'] = $this->load->view('register/register1',$data,TRUE);
					
		$item['header'] = $this->load->view('register/headerVendorRegister',NULL,TRUE);
		
		$this->load->view('template',$item);
	}
	
	function data_administrasi(){
		$this->load->helper('captcha');
		
		$form = $this->session->userdata('form');


		$vld = 	array(
			array(
				'field'=>'id_sbu',
				'label'=>'Lokasi Pendaftaran',
				'rules'=>'required'
				),
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
			/*
			array(
				'field'=>'npwp_date',
				'label'=>'Tanggal Pengukuhan',
				'rules'=>'required'
				),*/
			
			array(
				'field'=>'nppkp_code',
				'label'=>'NPPKP',
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
				'field'=>'vendor_phone',
				'label'=>'No. Telp',
				'rules'=>'required'
				),
			array(
				'field'=>'vendor_email',
				'label'=>'Email',
				'rules'=>'required|valid_email|is_unique[ms_vendor_admistrasi.vendor_email]'
				),
			array(
				'field'=>'vendor_website',
				'label'=>'Website',
				'rules'=>''
				),
			array(
				'field'=>'captcha',
				'label'=>'Captcha',
				'rules'=>'required|callback_validate_captcha'
				)
			);
		
		
		if(!empty($_FILES['npwp_file']['name'])){
			$vld[] = array(
					'field'=>'npwp_file',
					'label'=>'Lampiran Akta',
					'rules'=>'callback_do_upload[npwp_file]'
					);
		}

		if(!empty($_FILES['nppkp_file']['name'])){
			$vld[] = array(
					'field'=>'nppkp_file',
					'label'=>'Lampiran Pengesahan',
					'rules'=>'callback_do_upload[nppkp_file]'
					);
		}
		$this->form_validation->set_rules($vld);


		if($this->input->post('next')){
			if($this->form_validation->run()==TRUE){
				
				$form = $this->session->userdata('form');
				
				/*Doing Save Data and Upload*/

				$this->session->set_userdata('form',array_merge($form,$this->input->post()));
				$result = $this->save($this->session->userdata('form'));
				
				if($result){
					redirect('vendor/konfirmasiReg');
				}
			}
		}elseif($this->input->post('back')){
			unset($_POST['back']);
			$form = $this->session->userdata('form');
			$this->session->set_userdata('form',array_merge($form,$this->input->post()));
			redirect('vendor/suratPernyataan');
		}

		/*
		/	Create Captcha
		*/
		$vals = array(
		        'word'          => '',
		        'img_path'      => './assets/images/captcha/',
		        'img_url'       => base_url('assets/images/captcha/'),
		        'img_width'     => '150',
		        'img_height'    => 30,
		        'expiration'    => 7200,
		        'word_length'   => 8,
		        'font_size'     => 16,
		        'img_id'        => 'Imageid',
		        'pool'          => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',

		        // White background and border, black text and red grid
		        'colors'        => array(
		                'background' => array(255, 255, 255),
		                'border' => array(255, 255, 255),
		                'text' => array(0, 0, 0),
		                'grid' => array(255, 40, 40)
		        )
		);
		/*======================================*/


		$cap = create_captcha($vals);
		$data['captcha'] = $cap['image'];

        $this->session->set_userdata(array('captcha'=>$cap['word'], 'image' => $cap['time'].'.jpg'));

		$data['sbu'] = $this->vm->get_sbu();
		$data['id_legal'] = $this->vm->get_legal();
		$item['content'] = $this->load->view('register/register2',$data,TRUE);
		$item['script'] = $this->load->view('register/script2',$data,TRUE);
		$item['header'] = $this->load->view('register/headerVendorRegister',NULL,TRUE);
		
		$this->load->view('template',$item);
	}

	public function save($data){
		$param = array();
		$post = $this->securities->clean_input($data);
		$session_id = $this->encrypt->encode($this->utility->password_generator());
		$message = '';

		
		$post['password'] = $this->utility->password_generator();
		$post['entry_stamp'] = date("Y-m-d H:i:s");
		$post['edit_stamp'] = date("Y-m-d H:i:s");
		$post['last_status'] = 1;
		$post['is_active'] = 1;
		$post['ever_blacklisted'] = 0;
		$post['vendor_status'] = 0;
		$post['kategori'] = 1;
		$id = $this->vm->save_data($post);

		/*foreach($field as $_field)
			$this->approval_framework->set_approval('save_edit_'.$_field, $id, $param['id_sbu'], null, true);
		*/

		return $id;



		// return $this->send_email($post['email_pic'], '', $message);
	}

	public function validate_captcha(){
	    if($this->input->post('captcha') != $this->session->userdata('captcha'))
	    {
	        $this->form_validation->set_message('validate_captcha', 'Masukkan kode captcha dengan benar');
	        return false;
	    }else{
	        return true;
	    }

	}

	public function konfirmasiReg(){
		$data = $this->session->userdata('form');
		
		$this->send_email((isset($data['email_pic'])?$data['email_pic']:''),'',$message);
		$item['content'] = $this->load->view('register/register3',$data,TRUE);
		$item['header'] = $this->load->view('register/headerVendorRegister',NULL,TRUE);
		$this->load->view('template',$item);
		
	}

	public function do_upload($name = '', $db_name = ''){	
		$form = $this->session->userdata('form');
		$file_name = $_FILES[$db_name]['name'] = $db_name.'_'.$this->utility->name_generator($_FILES[$db_name]['name']);
		
		$config['upload_path'] = './lampiran/'.$db_name.'/';
		$config['allowed_types'] = 'pdf|jpeg|jpg|png|gif';
		$config['max_size'] = '2096';
		
		$this->load->library('upload');
		$this->upload->initialize($config);
		
		if ( ! $this->upload->do_upload($db_name)){
			$this->form_validation->set_message('do_upload', $this->upload->display_errors());
			return false;
		}else{
			$form[$db_name] = $file_name; 
			$this->session->set_userdata('form',$form);
			return true;
		}
	}

	public function to_waiting_list(){
		$user = $this->session->userdata('user');
		if($this->vm->check_pic($user['id_user'])==0){
			redirect(site_url('dashboard/pernyataan'));
		}
		
		if($this->vm->to_waiting_list()){
				$set_session = array(
					'id_user' 		=> 	$user['id_user'],
					'name'			=>	$user['name'],
					'id_sbu'		=>	$user['id_sbu'],
					'vendor_status'	=>	1,
					'is_active'		=>	$user['is_active'],
					'app'			=>	'vms'
				);
				
				$this->session->set_userdata('user',$set_session);
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Data telah terkirim ke Admin</p>');
				redirect(site_url('dashboard'));

		}
	}
	public function tambah(){
		if(!$this->session->userdata('admin')){
			redirect(site_url());
		}else{

			$_POST = $this->securities->clean_input($_POST,'save');
			$admin = $this->session->userdata('admin');
			$vld = 	array(
				array(
					'field'=>'id_legal',
					'label'=>'Bentuk Perusahaan',
					'rules'=>'required'
					),
				array(
					'field'=>'name',
					'label'=>'Nama',
					'rules'=>'required'
					),
				array(
					'field'=>'npwp_code',
					'label'=>'NPWP',
					'rules'=>'required|is_valid_npwp|is_unique[ms_vendor.npwp_code]|is_unique[ms_vendor_admistrasi.npwp_code]'
					),
				array(
					'field'=>'is_vms',
					'label'=>'Daftar',
					'rules'=>''
					),
				array(
					'field'=>'vendor_email',
					'label'=>'Email',
					'rules'=>'required|valid_email|is_unique[ms_vendor_admistrasi.vendor_email]'
					)
				);
			$this->form_validation->set_rules($vld);
			if($this->form_validation->run()==TRUE){
				$_POST['id_sbu'] = $admin['id_sbu'];
				$_POST['entry_stamp'] = date("Y-m-d H:i:s");
				$_POST['data_status'] = 0;
				$_POST['ever_blacklisted'] = 0;
				$_POST['is_active'] = 1;
				$_POST['password'] = $this->utility->password_generator();
				

				$res = $this->vm->add_vendor($this->input->post());

				if($res){
					$message = 'Perusahaan saudara telah terdaftar kedalam Sistem Aplikasi Kelogistikan PT Nusantara Regas.
					Berikut username &amp; password saudara : <br/><br/>
					
					Username : '.(isset($_POST['vendor_email'])?$_POST['vendor_email']:'').'<br/>
					Password : '.(isset($_POST['password'])?$_POST['password']:'').'<br/><br/>
					
					Untuk selanjutnya, silahkan melengkapi data - data dan dokumen saudara di aplikasi.<br/><br/>
					Terima kasih.<br/>
					PT Nusantara Regas';
					$this->mail($_POST['vendor_email'], $message);
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Data Telah Tersimpan. </br>Username: '.$_POST['vendor_email'].'</br>Password: '.$_POST['password'].'</p>');
				}else{
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Data Telah Tersimpan. Anda telah mendaftarkan Penyedia Barang/Jasa Non-VMS</p>');
				}
				redirect(site_url('admin/admin_vendor/daftar'));
			}
		}

		$data['id_legal'] = $this->vm->get_legal();
		$layout['content']= $this->load->view('tambah',$data,TRUE);
		$layout['script']= $this->load->view('tambah_js',$data,TRUE);
		
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
		
	}
	
	public function is_npwp_empty($str){

		$str = str_replace(' ','',$str);
		if(preg_replace("/[.-]/", "", $str)==''){

			$this->form_validation->set_message('is_npwp_empty','NPWP tidak boleh kosong');
			return FALSE;
		}
		else{
			return TRUE;
		}
	}
	public function data_pic(){
		$_POST = $this->securities->clean_input($_POST,'save');
		$user = $this->session->userdata('user');
		
		$data = $this->vm->get_data_pic($user['id_user']);
		
		$vld = 	array(
			array(
				'field'=>'pic_name',
				'label'=>'Nama',
				'rules'=>'required'
				),
			array(
				'field'=>'pic_position',
				'label'=>'Jabatan',
				'rules'=>'required'
				),
			array(
				'field'=>'pic_phone',
				'label'=>'No. Telpon',
				'rules'=>'required'
				),
			array(
				'field'=>'pic_email',
				'label'=>'Email',
				'rules'=>'required'
				),
			array(
				'field'=>'pic_address',
				'label'=>'Alamat',
				'rules'=>'required'
				),
			);
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);

			$result = $this->vm->edit_data_pic($this->input->post(),$user['id_user']);
			if($result){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('vendor/data_pic'));
			}
		}



		$layout['content']= $this->load->view('vendor_pic',$data,TRUE);

		$user = $this->session->userdata('user');
		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	public function edit_data_pic(){
		
		$_POST = $this->securities->clean_input($_POST,'save');
		$user = $this->session->userdata('user');
		$data = $this->vm->get_data_pic($user['id_user']);
		$vld = 	array(
			array(
				'field'=>'pic_name',
				'label'=>'Nama',
				'rules'=>'required'
				),
			array(
				'field'=>'pic_position',
				'label'=>'Jabatan',
				'rules'=>'required'
				),
			array(
				'field'=>'pic_phone',
				'label'=>'No. Telpon',
				'rules'=>'required'
				),
			array(
				'field'=>'pic_email',
				'label'=>'Email',
				'rules'=>'required'
				),
			array(
				'field'=>'pic_address',
				'label'=>'Alamat',
				'rules'=>'required'
				),
			);
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);

			$result = $this->vm->edit_data_pic($this->input->post(),$user['id_user']);
			if($result){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('vendor/data_pic'));
			}
		}



		$layout['content']= $this->load->view('edit_vendor_pic',$data,TRUE);

		$user = $this->session->userdata('user');
		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	
	public function password_change(){
		$_POST = $this->securities->clean_input($_POST,'save');
		$user = $this->session->userdata('user');
		$data = $this->vm->get_data_pic($user['id_user']);
		$vld = 	array(
			array(
				'field'=>'old_password',
				'label'=>'Password',
				'rules'=>'required|callback_is_password_equal[old_password]'
				),
			array(
				'field'=>'new_password',
				'label'=>'Password Baru',
				'rules'=>'required|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,128}$/]'
				),
				'errors' => array(
					'regex_match' => 'Password minimal 12 karakter, terdiri dari kombinasi huruf kapital, huruf kecil, angka dan karakter khusus. Contoh: Jakarta2023!',
				),
			array(
				'field'=>'conf_password',
				'label'=>'Konfirmasi Password',
				'rules'=>'required|matches[new_password]'
				)
			);
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);

			$result = $this->vm->password_change($this->input->post(),$user['id_user']);
			if($result){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah Password!</p>');
				redirect(site_url('vendor/password_change'));
			}
		}



		$layout['content']= $this->load->view('password_change',$data,TRUE);

		$user = $this->session->userdata('user');
		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	public function is_password_equal($field='',$param=''){
		$user = $this->session->userdata('user');
		$new_password = $this->input->post($param);
		$password = $this->vm->get_password($user['id_user']);
		if($password != $new_password){
			$this->form_validation->set_message('is_password_equal','Password yang Anda masukkan salah');
			return false;
		}else{
			return true;
		}
	}

	function dpt_print($id){

		$data['administrasi']	= $this->vm->get_administrasi_list($id,TRUE);
		// $data['csms_limit']		= $this->vm->get_csms_limit($id,TRUE);
		$data['pengurus']		= $this->vm->get_pengurus_list($id,TRUE);
		$data['surat_izin']		= $this->im->get_izin_report($id,TRUE);
		$data['situ']			= $this->im->get_situ_report($id,TRUE);
		$data['tdp']			= $this->im->get_tdp_report($id,TRUE);
		$data['keagenan']		= $this->im->get_keagenan_report($id,TRUE);
		$data['pengalaman']		= $this->im->get_pengalaman_report($id,TRUE);
		$data['aspek_k3']		= $this->km->get_aspek_k3($id,TRUE);
		$data['csms_limit']		= $this->km->get_k3_all_data($id);
		$data['klasifikasi']	= array(1=>'non-Konstruksi', 2=>'non-Konstruksi',3=>'non-Konstruksi',4=>'Konstruksi',5=>'Konstruksi');


		$layout['content']		= $this->load->view('admin/dpt/print_dpt',$data,TRUE);
		$this->load->view('template_print',$layout);
	}

	function mail($to,$message){
 		$this->email->clear(TRUE);

		$this->email->from('vms-noreply@nusantararegas.com', 'VMS REGAS');
		$this->email->to($to); 

		// $this->email->cc('alicia.ordinary@gmail.com'); 
		//$this->email->bcc('muarifgustiar@gmail.com'); 
		$this->email->subject('Autentikasi Login Sistem Aplikasi Kelogistikan PT Nusantara Regas');
		
		$this->email->message($message);	
		$this->email->send();
	}
	
	function mail_test(){
 		$this->email->clear(TRUE);

		$this->email->from('vms-noreply@nusantararegas.com', 'VMS REGAS');
		$this->email->to('krisna@nusantararegas.com'); 

		// $this->email->cc('alicia.ordinary@gmail.com'); 
		$this->email->bcc('muarifgustiar@gmail.com'); 

		$this->email->subject('Autentikasi Login Sistem Aplikasi Kelogistikan PT Nusantara Regas');
		
		$this->email->message($message);	
		$this->email->send();

		echo $this->email->print_debugger();
	}
	

	public function username_change(){
		$_POST = $this->securities->clean_input($_POST,'save');
		$user = $this->session->userdata('user');
		$data['username'] = $this->vm->get_data_username($user['id_user']);
		$vld = 	array(
			array(
				'field'=>'username',
				'label'=>'Username',
				'rules'=>'required|valid_email'
				)
			);
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);
			
			$result = $this->vm->username_change($this->input->post(),$user['id_user']);
			if($result){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah Username!</p>');
				redirect(site_url('vendor/username_change'));
			}
		}



		$layout['content']= $this->load->view('username_change',$data,TRUE);

		$user = $this->session->userdata('user');
		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
}