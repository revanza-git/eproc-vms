<?php
/**
 * 
 */
class App extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('main_model','mm');
		$this->load->helper('string');
		$this->load->helper('url');
	}

	public function index()
	{		
		$admin = $this->session->userdata('admin');
		if (!is_array($admin) || !isset($admin['id_user'])) {
			redirect(base_url());
			return;
		}

		$getUser = $this->mm->to_app($admin['id_user']);
		if (!is_array($getUser) || !isset($getUser['id'])) {
			$this->session->sess_destroy();
			redirect(base_url());
			return;
		}
		
		$this->session->sess_destroy();

		$idRole = isset($getUser['id_role_app2']) ? $getUser['id_role_app2'] : (isset($getUser['id_role']) ? $getUser['id_role'] : 0);

		$data = array(
			'name' 			=> $getUser['name'],
			'id_user' 		=> $getUser['id'],
			'id_role' 		=> $idRole,
			'id_division'	=> $getUser['id_division'],
			'app_type'		=> 1,
			'email'	 		=> $getUser['email'],
			'photo_profile' => $getUser['photo_profile'],
		);

		$key = random_string('unique').random_string('unique').random_string('unique').random_string('unique');
		$this->db->insert('ms_key_value', array(
			'key' => $key,
			'value'=> json_encode($data),
		));

		$base_app = (string) $this->config->item('base_app');
		$main_base_url = str_replace('/pengadaan/', '/main/', $base_app);
		if ($main_base_url === '') {
			redirect(base_url());
			return;
		}
		if ($main_base_url !== '' && substr($main_base_url, -1) !== '/') {
			$main_base_url .= '/';
		}
		$redirect_url = $main_base_url . "index.php/main/from_eks?key=".$key;
		redirect($redirect_url);
	}
}
