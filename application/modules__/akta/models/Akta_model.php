<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Akta_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->field_master = array(
								'id_vendor',
								'type',
								'no',
								'notaris',
								'issue_date',
								'akta_file',
								'file_extension_akta',
								'authorize_by',
								'authorize_no',
								'authorize_file',
								'authorize_date',
								'entry_stamp',
								'edit_stamp'
							);
	}

	function save_data($data){
		$_param = array();
		$sql = "INSERT INTO ms_akta (
							id_vendor,
							type,
							no,
							notaris,
							issue_date,
							akta_file,
							file_extension_akta,
							authorize_by,
							authorize_no,
							authorize_file,
							authorize_date,
							entry_stamp,
							edit_stamp) 
				VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ";
		
		
		foreach($this->field_master as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);
		$id = $this->db->insert_id();
		
		return $id;
	}

	function edit_data($data,$id){
				
		$this->db->where('id',$id);
		

		$result = $this->db->update('ms_akta',$data);
		if($result)return $id;
	}
	function delete($id){
		$this->db->where('id',$id);
		
		return $this->db->update('ms_akta',array('del'=>1));
	}
	
	function get_data($id){
		$user = $this->session->userdata('user');
		$sql = "SELECT * FROM ms_akta WHERE id = ".$id;
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	function get_akta_list($search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()) 
    {
    	$user = $this->session->userdata('user');
		$this->db->select('*');
		$this->db->where('del',0);
		$this->db->where('id_vendor',$user['id_user']);
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
		
		$a = $this->filter->generate_query($this->db->group_by('ms_akta.id'),$filter);
		$query = $a->get('ms_akta');		
		// echo $this->db->last_query();		
		return $query->result_array();
    }

    function get_akta_admin_list($id,$akta_type='pendirian') 
    {
    	$user = $this->session->userdata('user');
		$this->db->select('*');
		$this->db->where('del',0);
		$this->db->where('id_vendor',$id);
		
		if ($akta_type=='pendirian') {
			$this->db->where('type',$akta_type);
		}else{
			$this->db->where('type !=','pendirian');

		}
		
		$query = $this->db->get('ms_akta');
		// echo $this->db->last_query();
		return $query->result_array();
    }
}