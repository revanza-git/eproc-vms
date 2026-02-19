<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Pass_change_model extends MY_Model{
	public $table = 'ms_user';
	public $eproc_db;
	function __construct(){
		parent::__construct();
		$this->eproc_db = $this->load->database('eproc',true);
	}

	function selectData($id){
		$user = $this->session->userdata('admin');
		$query = "	SELECT
						a.password,
						a.username,
						b.email,
						b.photo_profile,
						b.name
					FROM 
						ms_login a
					JOIN
						ms_admin b ON b.id=a.id_user AND a.type='admin'
					WHERE 
						a.type = 'admin' AND a.id_user = ".$user['id_user'];

		$query = $this->eproc_db->query($query, array($id));
		return $query->row_array();

	}

	function getCurrentPassword($user_id)
	{
		$query = $this->eproc_db->where('id_user',$user_id)->where('type','admin')
						  ->get('ms_login');
		if ($query->num_rows() > 0) {
			return $query->row();
		}
	}

    function change_password($id,$data){
    	$up_admin = array(
    		'email' 		=> 	$data['email'],
    		'photo_profile'	=>	$data['photo_profile']
    	);

    	$this->eproc_db->where('id',$id)->update('ms_admin',$up_admin);

    	$up_login = array(
    		'username'	=> $data['username'],
    		'password'	=> $data['raw_password']
    	);

		$res = $this->eproc_db->where('id_user',$id)->where('type','admin')->update('ms_login',$up_login);
		
		return $res;
	}
	
}
