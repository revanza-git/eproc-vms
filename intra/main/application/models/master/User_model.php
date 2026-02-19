<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model{
	public $eproc_db;
	public $table = 'ms_user';
	function __construct(){
		parent::__construct();
		$this->eproc_db = $this->load->database('eproc',true);
	}

	public function getData($form)
	{
		$query = " 	SELECT
						a.name,
						b.username,
						b.password,
						a.id
					FROM
						ms_admin a
					JOIN
						ms_login b ON b.id_user=a.id AND b.type = 'admin'
					WHERE
						a.del = 0 ";
		return $query;	
	}

	function getData_($form, $id=null){
		if($id==null){
			$user = $this->session->userdata('user');
		}else{
			$user['id_user'] = $id;
		}
		
		$query = "	SELECT  a.name,
							a.username,
							a.raw_password,
							b.name role_name,
							c.name division,
							a.id
					FROM ".$this->table." a 
					JOIN tb_role b ON a.id_role = b.id
					LEFT JOIN tb_division c ON a.id_division = c.id  
					WHERE a.del = 0";
		if($this->input->post('filter')){
			$query .= $this->filter($form, $this->input->post('filter'), false);
		}
		
		return $query;
	}

	function selectData($id){
		$query = "SELECT
						a.name,
						b.username,
						a.id,
						a.id_role_app2,
						a.id_role,
						a.id_division,
						a.email,
						b.password raw_password
					FROM
						ms_admin a
					JOIN
						ms_login b ON b.id_user=a.id AND b.type = 'admin'
					WHERE
						a.del = 0 AND a.id = ?";
		$query = $this->eproc_db->query($query, array($id));
		return $query->row_array();
	}

	function getRoleOption(){
		$query = "	SELECT id, name 
					FROM tb_role";
		$query = $this->db->query($query);

		$return = array();
		foreach($query->result_array() as $key => $row){
			$return[$row['id']] = $row['name'];
		}
		return $return;
	}

	function getRoleOptionEproc(){
		$query = "	SELECT id, name 
					FROM tb_role";
		$query = $this->eproc_db->query($query);

		$return = array();
		foreach($query->result_array() as $key => $row){
			$return[$row['id']] = $row['name'];
		}
		return $return;
	}


	public function insert($data)
	{
		// print_r($data);die;
		$save_admin = array(
			'id_role'		=>	$data['id_role'],
			'id_role_app2' 	=>	$data['id_role_app2'],
			'name'			=>	$data['name'],
			'id_division'	=>	$data['id_division'],
			'email'			=>	$data['email']
		);

		$this->eproc_db->insert('ms_admin',$save_admin);

		$id_user = $this->eproc_db->insert_id();

		$save_login = array(
			'username'		=>	$data['username'],
			'password'		=>	$data['raw_password'],
			'type'			=>	'admin',
			'type_app'		=>	2,
			'id_user'		=>	$id_user
		);

		return $this->eproc_db->insert('ms_login',$save_login);
	}

	public function update($id,$data)
	{
		// print_r($data);die;

		$save_admin = array(
			'id_role'		=>	$data['id_role'],
			'id_role_app2' 	=>	$data['id_role_app2'],
			'name'			=>	$data['name'],
			'id_division'	=>	$data['id_division'],
			'email'			=>	$data['email']
		);

		$this->eproc_db->where('id',$id)->update('ms_admin',$save_admin);

		$save_login = array(
			'username'		=>	$data['username'],
			'password'		=>	$data['raw_password']
		);

		return $this->eproc_db->where('type','admin')->where('id_user',$id)->update('ms_login',$save_login);
	}

	public function delete($id)
	{
		$arr = array(
			'del'		 => 1
		);

		return $this->eproc_db->where('id',$id)->update('ms_admin',$arr);
	}

	public function insert_admin($data){
		$this->db->insert('ms_admin',$data);
		return $this->db->insert_id();
	}
	public function insert_data_admin($table, $data){
		return $this->db->insert($table,$data);	
	}
	public function update_admin($id, $data){
		$this->db->where('id', $id)->update('ms_admin',$data);

	}
	public function update_data_admin($id, $data){
		return $this->db->where('id_user', $id)->where('type', 'admin')->update('ms_login',$data);	
	}
}
