<?php defined('BASEPATH') OR exit('No direct script access allowed');



class Main_model extends CI_Model{



	function __construct(){

		parent::__construct();



	}



	public function cek_login()
	{
		$username = encode_php_tags($this->input->post('username'));
		$password = $this->input->post('password');

		// SECURITY FIX: Only select by username, verify password separately
		$sql = "SELECT * FROM ms_login WHERE username = ?";
		$_sql = $this->db->query($sql, array($username));
		$sql = $_sql->row_array();

		$ct_sql = '';
		if($_sql->num_rows() > 0){
			// Load secure password library for verification
			$this->load->library('secure_password');
			
			// Verify password securely (supports both bcrypt and legacy hashes)
			if (!$this->secure_password->verify_password($password, $sql['password'])) {
				// Password verification failed - increment attempts
				$this->handle_failed_login($username);
				return false;
			}
			
			// Check if password needs rehashing (migration from legacy to bcrypt)
			if ($this->secure_password->needs_rehash($sql['password'])) {
				$new_hash = $this->secure_password->hash_password($password);
				if ($new_hash) {
					$this->db->where('id', $sql['id'])
							 ->update('ms_login', array('password' => $new_hash));
					log_message('info', 'Password migrated to bcrypt for user: ' . $username);
				}
			}

			if($sql['type'] == "user"){
				$ct_sql = "SELECT * FROM ms_vendor WHERE id=? AND is_active =?";
				$ct_sql = $this->db->query($ct_sql, array($sql['id_user'],1));
				if(count($ct_sql->result_array() )> 0){
					$data = $ct_sql->row_array();

				// Clear any existing user/admin sessions without destroying the entire session
				$this->session->unset_userdata('user');
				$this->session->unset_userdata('admin');
				
				$set_session = array(
					'id_user' 		=> 	$data['id'],
					'name'			=>	$data['name'],
					'id_sbu'		=>	$data['id_sbu'],
					'vendor_status'	=>	$data['vendor_status'],
					'is_active'		=>	$data['is_active'],
					'app'			=>	'vms',
					'type'			=> 'user'
				);

				$this->session->set_userdata('user',$set_session);
				$this->db->where('username', $username)->update('ms_login', array(
					'attempts' => 0,
					'lock_time' => NULL
				));
				return true;
				}
				else{
					return false;
				}
			}else if($sql['type'] == "admin" AND $sql['type_app'] == 1){
				$ct_sql = "	SELECT 
								*,
								ms_admin.id id, 
								ms_admin.name name, 
								tb_role.name role_name,
								ms_admin.id_division
							FROM 
								ms_admin 
							JOIN 
								tb_role ON ms_admin.id_role = tb_role.id
							WHERE 
								ms_admin.id = ? AND ms_admin.is_disable = 0";

				$ct_sql = $this->db->query($ct_sql, array($sql['id_user']));
				
				if(count($ct_sql->result_array() )> 0){
					$data = $ct_sql->row_array();

					// Clear any existing user/admin sessions without destroying the entire session
					$this->session->unset_userdata('user');
					$this->session->unset_userdata('admin');
					
					$set_session = array(
						'id_user' 		=> 	$data['id'],
						'name'			=>	$data['name'],
						'id_sbu'		=>	$data['id_sbu'],
						'id_role'		=>	$data['id_role'],
						'role_name'		=>	$data['role_name'],
						'sbu_name'		=>	isset($data['sbu_name']) ? $data['sbu_name'] : '',
						'app'			=>	'vms',
						'app_type'		=>	$sql['type_app'],
						'type'			=> 'admin',
						'id_division'	=>	isset($data['id_division']) ? $data['id_division'] : null,
						'originated_from_vms' => true,  // Track that this session started in VMS
						'login_timestamp' => date('Y-m-d H:i:s')
					);

					$this->session->set_userdata('admin',$set_session);

					$this->db->where('username', $username)->update('ms_login', array(
						'attempts' => 0,
						'lock_time' => NULL
					));
					return true;
				}else{
					return false;
				}

			}else if ($sql['type'] == "admin" AND $sql['type_app'] == 2) {
				$ct_sql = " SELECT 
								a.name,
								a.email,
								a.id_role_app2,
								a.id_division,
								a.photo_profile,
								a.id
							FROM
								ms_admin a 
							WHERE
								 a.id = ? AND a.is_disable = 0
								";

				$ct_sql = $this->db->query($ct_sql, array($sql['id_user']));

				if(count($ct_sql->result_array())> 0){
					$data = $ct_sql->row_array();

					// Clear any existing user/admin sessions without destroying the entire session
					$this->session->unset_userdata('user');
					$this->session->unset_userdata('admin');
					
					$set_session = array(
						'name'			=>	$data['name'],
						'id_user' 		=> 	$data['id'],
						'id_role'		=>	$data['id_role_app2'],
						'id_division'	=>  $data['id_division'],
						'email'			=>  $data['email'],
						'photo_profile' =>  $data['photo_profile'],
						'app_type' 		=>	$sql['type_app'],
						'type'			=> 'admin',
						'originated_from_vms' => true,  // Track that this session started in VMS
						'login_timestamp' => date('Y-m-d H:i:s')
					);
					$this->session->set_userdata('admin',$set_session);
					$this->db->where('username', $username)->update('ms_login', array(
						'attempts' => 0,
						'lock_time' => NULL
					));
					return true;
				}else{
					return false;
				}
			}else{
				$ct_sql = "SELECT *,ms_admin.id id, ms_admin.name name, tb_role.name role_name FROM ms_admin JOIN tb_role ON ms_admin.id_role = tb_role.id WHERE ms_admin.id=? AND ms_admin.del=?";

				$ct_sql = $this->db->query($ct_sql, array($sql['id_user'],0));
				
				if(count($ct_sql->result_array() )> 0){
					$data = $ct_sql->row_array();

					// Clear any existing user/admin sessions without destroying the entire session
					$this->session->unset_userdata('user');
					$this->session->unset_userdata('admin');
					
					$set_session = array(
						'id_user' 		=> 	$data['id'],
						'name'			=>	$data['name'],
						'id_sbu'		=>	$data['id_sbu'],
						'id_role'		=>	$data['id_role'],
						'role_name'		=>	$data['role_name'],
						'sbu_name'		=>	isset($data['sbu_name']) ? $data['sbu_name'] : '',
						'app'			=>	'vms',
						'type'			=> 'admin',
						'originated_from_vms' => true,  // Track that this session started in VMS
						'login_timestamp' => date('Y-m-d H:i:s')
					);

					$this->session->set_userdata('admin',$set_session);
					return true;
				}else{
					return false;
				}
			}
		}else{
			// User not found - handle as failed login
			$this->handle_failed_login($username);
			return false;
		}
	}
	
	/**
	 * Handle failed login attempts with account lockout
	 */
	private function handle_failed_login($username) {
		$data = $this->db->query("SELECT * FROM ms_login WHERE username = ?", array($username))->row_array();
		
		if ($data) {
			$attempts = $data['attempts'];
			$attempts++;

			if ($attempts > 2) {
				$now = date('Y-m-d H:i:s');
				if ($data['type'] == 'admin') {
					$lock_time = date('Y-m-d H:i:s',strtotime($now . "-9hour + 15minute"));
				}else{
					$lock_time = date('Y-m-d H:i:s',strtotime($now . "-9hour + 5minute"));
				}
				$this->db->where('username', $username)->update('ms_login', array(
					'attempts' => 0,
					'lock_time' => $lock_time
				));
				
				// Log security event
				log_message('error', 'Account locked due to multiple failed attempts: ' . $username);
			}else{
				$this->db->set('attempts', $attempts)->where('username', $username)->update('ms_login');
				log_message('info', 'Failed login attempt (' . $attempts . '/3) for user: ' . $username);
			}
		}
	}

	function getKurs(){
		$return = array();
		$query = "SELECT * FROM tb_kurs WHERE del = 0";
		$query = $this->db->query($query);
		foreach ($query->result_array() as $key => $value) {
			$return[$value['id']] = $value['symbol'];
		}
		return $return;
	}

	public function getRole(){
		$return = array();
		$query = "SELECT * FROM tb_role WHERE del = 0";
		$query = $this->db->query($query);
		foreach ($query->result_array() as $key => $value) {
			$return[$value['id']] = $value['name'];
		}
		return $return;
	}

	public function getDiv(){
		$return = array();
		$query = "SELECT * FROM ms_division WHERE del = 0";
		$query = $this->db->query($query);
		foreach ($query->result_array() as $key => $value) {
			$return[$value['id']] = $value['name'];
		}
		return $return;
	}

	function set_admin_queue(){
		// Placeholder for admin queue functionality
	}
}
