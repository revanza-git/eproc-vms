<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Pass_change extends MY_Controller {

	public $form;

	public $modelAlias = 'pcm';

	public $isClientMenu = true;

	public function __construct(){

		parent::__construct();

		$this->load->model('pass_change_model','pcm');

		$this->form = array(
			'form'=>array(
				array(
		            'field'=>	'name',
					'label'=>	'Nama Akun',
					'type' =>	'text'
		        ),
				array(
		            'field'=>	'email',
					'label'=>	'Email',
					'type' =>	'text'
		        ),
		        array(
		            'field'=>	'username',
					'label'=>	'Username',
					'type' =>	'text'
		        ),
	         	array(
		            'field'=>	'password',
					'label'=>	'Password',
					'type' =>	'text',
					'rules'=>	'required|callback_checkPw[]'
		        ),
		        array(
		            'field'	=> 	'photo_profile',
		            'type'	=>	'file',
		            'label'	=>	'Foto Akun',
		            'upload_path'=>base_url('assets/lampiran/photo_profile/'),
					'upload_url'=>site_url('pass_change/upload_lampiran'),
					'allowed_types'=>'pdf|jpeg|jpg|png|gif|rar|zip|doc|docx'
	         	),
	         )
		);
		$this->insertUrl = site_url('pass_change/save/');
		$this->updateUrl = 'pass_change/update/';
		$this->deleteUrl = 'pass_change/delete/';
	}
	
	public function index($id = null){
		$data = $this->session->userdata('admin');
		$this->breadcrumb->addlevel(1, array(
			'url' => site_url('pass_change'),
			'title' => 'Edit Akun'
		));
		$this->header = 'Edit Akun';
		$this->content = $this->load->view('pass_change/list',$data, TRUE);
		$this->script = $this->load->view('pass_change/list_js', $data, TRUE);
		parent::index($id);
	}

	public function view(){
		$user = $this->session->userdata('admin');
		$user_id = $user['id_user'];

		$modelAlias = $this->modelAlias;
		$lastData = $this->$modelAlias->selectData($user_id);

		//print_r($lastData);die;

		foreach($this->form['form'] as $key => $element) {
			$this->form['form'][$key]['value'] = $lastData[$element['field']];
		}

		$this->form['url'] = site_url('pass_change/change_password/'.$user_id);
		$this->form['button'] = array(
                array(
                    'type'=>'submit',
                    'label'=>'Ubah Data'
                )
            );
		echo json_encode($this->form);
	}

	public function change_password($id){
		$modelAlias = $this->modelAlias;
		$lastData = $this->$modelAlias->selectData($id);
		if ($this->validation()) {
			$save = $this->input->post();
			$save_change['name'] = $save['name'];
			if ($save['email'] != '' && $save['username'] == '' && $save['password'] == '' && $save['photo_profile'] == '') {
				$save_change['email'] = $save['email'];
				$save_change['edit_stamp'] = date('Y-m-d H:i:s'); 
			} 

			if ($save['email'] == '' && $save['username'] == '' && $save['password'] == '' && $save['photo_profile'] != '') {
				$save_change['photo_profile'] = $save['photo_profile'];
				$save_change['edit_stamp'] = date('Y-m-d H:i:s'); 
			} 

			if ($save['username'] != '' && $save['email'] == '' && $save['password'] == '' && $save['photo_profile'] == '') {
				$save_change['username'] = $save['username'];
				$save_change['edit_stamp'] = date('Y-m-d H:i:s'); 
			}

			if ($save['password'] != '' && $save['email'] == '' && $save['username'] == '' && $save['photo_profile'] == '') {
				$save_change['password'] = do_hash($save['password'],'sha1');
				$save_change['raw_password'] = $save['password'];
				$save_change['edit_stamp'] = date('Y-m-d H:i:s'); 
			}

			//------------------------------------------------------------------- 2 Kondisi
			if ($save['email'] != '' && $save['username'] != '' && $save['password'] == '' && $save['photo_profile'] == '' ) {
				$save_change['email'] = $save['email'];
				$save_change['username'] = $save['username'];
				$save_change['edit_stamp'] = date('Y-m-d H:i:s'); 
			} 

			if ($save['email'] != '' && $save['password'] != '' && $save['username'] == '' && $save['photo_profile'] == '') {
				$save_change['password'] = do_hash($save['password'],'sha1');
				$save_change['raw_password'] = $save['password'];
				$save_change['email'] = $save['email'];
				$save_change['edit_stamp'] = date('Y-m-d H:i:s'); 
			}

			if ($save['email'] != '' && $save['photo_profile'] != '' && $save['password'] == '' && $save['username'] == '') {
				$save_change['photo_profile'] = $save['photo_profile'];
				$save_change['email'] = $save['email'];
				$save_change['edit_stamp'] = date('Y-m-d H:i:s'); 
			}

			if ($save['username'] != '' && $save['password'] != '' && $save['email'] == '' && $save['photo_profile'] == '') {
				$save_change['password'] = do_hash($save['password'],'sha1');
				$save_change['raw_password'] = $save['password'];
				$save_change['username'] = $save['username'];
				$save_change['edit_stamp'] = date('Y-m-d H:i:s'); 
			}

			if ($save['username'] != '' && $save['photo_profile'] != '' && $save['password'] == ''  && $save['email'] == '') {
				$save_change['username'] = $save['username'];
				$save_change['photo_profile'] = $save['photo_profile'];
				$save_change['edit_stamp'] = date('Y-m-d H:i:s'); 
			} 

			if ($save['password'] != '' && $save['photo_profile'] != '' && $save['email'] == '' && $save['username'] == '') {
				$save_change['password'] = do_hash($save['password'],'sha1');
				$save_change['raw_password'] = $save['password'];
				$save_change['photo_profile'] = $save['photo_profile'];
				$save_change['edit_stamp'] = date('Y-m-d H:i:s'); 
			}

			//------------------------------------------------------------------- 3 Kondisi
			if ($save['email'] != '' && $save['username'] != '' && $save['password'] != '' && $save['photo_profile'] == '' ) {
				$save_change['email'] = $save['email'];
				$save_change['username'] = $save['username'];
				$save_change['password'] = do_hash($save['password'],'sha1');
				$save_change['raw_password'] = $save['password'];
				$save_change['edit_stamp'] = date('Y-m-d H:i:s'); 
			}

			if ($save['email'] != '' && $save['username'] != '' && $save['photo_profile'] != '' && $save['password'] == '') {
				$save_change['email'] = $save['email'];
				$save_change['username'] = $save['username'];
				$save_change['photo_profile'] = $save['photo_profile'];
				$save_change['edit_stamp'] = date('Y-m-d H:i:s'); 
			}

			if ($save['username'] != '' && $save['password'] != '' && $save['photo_profile'] != '' && $save['email'] == '') {
				$save_change['password'] = do_hash($save['password'],'sha1');
				$save_change['raw_password'] = $save['password'];
				$save_change['username'] = $save['username'];
				$save_change['photo_profile'] = $save['photo_profile'];
				$save_change['edit_stamp'] = date('Y-m-d H:i:s'); 
			}
			
			//--------------------------------------------------------------- Semua Kondisi
			if ($save['username'] != '' && $save['email'] != '' && $save['password'] != '' && $save['photo_profile'] != '') {
				$save_change['username'] = $save['username'];
				$save_change['email'] = $save['email'];
				$save_change['password'] = do_hash($save['password'],'sha1');
				$save_change['raw_password'] = $save['password'];
				$save_change['photo_profile'] = $save['photo_profile'];
				$save_change['edit_stamp'] = date('Y-m-d H:i:s'); 
			}		

			if ($this->$modelAlias->change_password($id, $save_change)) {
				$this->session->set_userdata('alert', $this->form['successAlert']);
				$this->deleteTemp($save, $lastData);
				// echo json_encode(array('status'=>'success'));
			}
					
		}
    }

    public function checkPw($data)
	{
		$pwd = $this->input->post('password');
		$pwd = trim($pwd);

		if (strlen($pwd) < 8) {
			$this->form_validation->set_message('checkPw', 'Password minimal 8 karakter!');
			return false;
	    }

	    if (!preg_match("#[0-9]+#", $pwd)) {
	    	$this->form_validation->set_message('checkPw', 'Password harus mengandung minimal satu angka!');
			return false;
	    }

	    if (!preg_match("#[a-z]+#", $pwd)) {
	        $this->form_validation->set_message('checkPw', 'Password harus mengandung minimal satu huruf!');
			return false;
	    }

	    if (!preg_match("#[A-Z]+#", $pwd)) {
	        $this->form_validation->set_message('checkPw', 'Password harus mengandung minimal satu huruf kapital!');
			return false;
	    }

	    if (!preg_match("/[^a-zA-Z\d]/", $pwd)) {
	        $this->form_validation->set_message('checkPw', 'Password harus mengandung simbol khusus!');
			return false;
	    }
	}
}
