<?php defined('BASEPATH') OR exit('No direct script access allowed');
class User extends MY_Controller {
	public $form;
	public $modelAlias = 'um';
	public $alias = 'ms_user';
	public $module = 'User';
	public $isClientMenu = true;
	public function __construct(){
		parent::__construct();
		$this->load->model('master/User_model','um');
		$this->load->model('Main_model','mm');

		
		$user = $this->session->userdata('user');
		$this->form = array(
				'form' => array(
					array(
						'field'	=> 	'name',
						'type'	=>	'text',
						'label'	=>	'Nama',
						'rules' => 	'required',
					),
					array(
						'field'	=> 	'id_role_app2',
						'type'	=>	'dropdown',
						'label'	=>	'Role Perencanaan',
						'source'=>	$this->um->getRoleOption(),
					),
					array(
						'field'	=> 	'id_role',
						'type'	=>	'dropdown',
						'label'	=>	'Role Pengadaaan B/J',
						'source'=>	$this->um->getRoleOptionEproc(),
					),
					array(
						'field'	=> 	'id_division',
						'type'	=>	'dropdown',
						'label'	=>	'Divisi',
						'source'=>	$this->mm->getDiv(),
					),
					array(
						'field'	=> 	'email',
						'type'	=>	'text',
						'label'	=>	'Email',
						'rules' => 	'required',
					),
					array(
						'field'	=> 	'username',
						'type'	=>	'text',
						'label'	=>	'Username',
						'rules' => 	'required',
					),
					array(
						'field'	=> 	'raw_password',
						'type'	=>	'text',
						'label'	=>	'Password',
						'rules' => 	'required',
					),
				),
				'successAlert'=>'Berhasil mengubah data!'
			);
		$this->insertUrl = site_url('master/user/save/'.$this->id_client);
		$this->updateUrl = 'master/user/update';
		$this->deleteUrl = 'master/user/delete/';
		$this->getData = $this->um->getData($this->form);
		
		// Filter form elements to only include valid validation rules
		$validation_rules = array();
		foreach ($this->form['form'] as $element) {
			if (isset($element['field']) && isset($element['rules'])) {
				$validation_rules[] = $element;
			}
		}
		if (!empty($validation_rules)) {
			$this->form_validation->set_rules($validation_rules);
		}
	}
	public function index($id = null){
		$this->breadcrumb->addlevel(1, array(
			'url' => site_url('user'),
			'title' => 'User'
		));
		$this->header = 'User';
		$data = array('id' => $id); // Pass id to view to prevent undefined variable error
		$this->content = $this->load->view('master/user/list',$data, TRUE);
		$this->script = $this->load->view('master/user/list_js', $data, TRUE);
		parent::index($id);
	}
	public function insert(){
		$this->form['url'] = $this->insertUrl;
		$this->form['button'] = array(
			array(
				'type' => 'submit',
				'label' => 'Simpan',
			) ,
			array(
				'type' => 'cancel',
				'label' => 'Batal'
			)
		);
		echo json_encode($this->form);
	}
	public function save($data = null){
		// Check if required email field exists
		if (!isset($_POST['email']) || empty($_POST['email'])) {
			// Handle missing email error
			show_error('Email field is required for user creation.');
			return;
		}
		
		$password = password_generator();
		$_POST['username'] = $_POST['email'];
		$_POST['raw_password'] = $password;
		$_POST['password'] = do_hash($password,'sha1');
		$to = $_POST['email'];
		$subject = "Password User";
		$message = "Ini adalah akun anda :<br> Username : '".$_POST['email']."' <br> Password : '".$password."' <br> <a href=".base_url().">Klik Disini</a> untuk login";
		$this->send_mail($to,$subject,$message);
		parent::save($data);
	}

	public function update($id){
		$rawPassword = isset($_POST['raw_password']) ? trim((string) $_POST['raw_password']) : '';
		if ($rawPassword === '') {
			unset($_POST['raw_password']);
			foreach ($this->form['form'] as $key => $element) {
				if (isset($element['field']) && $element['field'] === 'raw_password') {
					$this->form['form'][$key]['rules'] = '';
				}
			}
		}
		if ((!isset($_POST['username']) || $_POST['username'] === '') && isset($_POST['email'])) {
			$_POST['username'] = $_POST['email'];
		}
		
		parent::update($id);
	}

	public function edit($id=null){
		$modelAlias = $this->modelAlias;
		$data   = $this->$modelAlias->selectData($id);
		if (!is_array($data) || empty($data)) {
			$this->form['form'] = array();
			$this->form['url'] = '';
			$this->form['button'] = array(
				array(
					'type' => 'cancel',
					'label' => 'Tutup'
				)
			);
			$this->form['error'] = 'Data user tidak ditemukan';
			echo json_encode($this->form);
			return;
		}
		foreach($this->form['form'] as $key => $element){
			if($this->form['form'][$key]['type']!='password'){
				$this->form['form'][$key]['value'] = isset($data[$element['field']]) ? $data[$element['field']] : '';
			}else{
				$this->form['form'][$key]['label'] = 'Password (Tinggalkan kosong bila tidak diganti)';
			}
		}

		$this->form['url'] = site_url($this->updateUrl .'/'.$id);
		$this->form['button'] = array(
						array(
								'type'=>'submit',
								'label'=>'Ubah'
						),
						array(
								'type'=>'cancel',
								'label'=>'Batal'
						)
				);
		echo json_encode($this->form);
	}

	public function getData($id = null)
	{
		$config['query'] = $this->getData;
		$return = $this->tablegenerator->initialize_vms($config);
		echo json_encode($return);
	}
}
