<?php defined('BASEPATH') OR exit('No direct script access allowed');

class pengurus_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->field_master = array(
								'id_vendor',
								'id_akta',
								'no',
								'name',
								'position',
								// 'position_expire',
								'pengurus_file',
								'expire_date',
								'entry_stamp'
							);
	}

	function save_data($data){
		$_param = array();
		$sql = "INSERT INTO ms_pengurus (
							id_vendor,
							id_akta,
							no,
							name,
							position,
							-- position_expire,
							pengurus_file,
							expire_date,
							entry_stamp
							) 
				VALUES (?,?,?,?,?,?,?,?) ";
		
		
		foreach($this->field_master as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);
		$id = $this->db->insert_id();
		
		return $id;
	}

	function edit_data($data,$id){
		$param = array();
		
		$this->db->where('id',$id);
		$res = $this->db->update('ms_pengurus',$data);

		
		return $res;
	}
	function delete($id){
		$this->db->where('id',$id);
		
		return $this->db->update('ms_pengurus',array('del'=>1));
	}
	
	function get_data($id){
		$user = $this->session->userdata('user');
		$sql = "SELECT * FROM ms_pengurus WHERE id = ".$id;
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	function get_pengurus_list($search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()) 
    {
    	$user = $this->session->userdata('user');
		$this->db->select('*,ms_pengurus.id id,ms_akta.no no_akta, ms_pengurus.no no_identitas');
		$this->db->where('ms_pengurus.del',0);
		$this->db->where('ms_pengurus.id_vendor',$user['id_user']);
		$this->db->join('ms_akta','ms_akta.id = ms_pengurus.id_akta');
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
		$a = $this->filter->generate_query($this->db->group_by('ms_pengurus.id'),$filter);
		
		$query = $a->get('ms_pengurus');
		return $query->result_array();
		
    }

    function get_akta($id_vendor){
		$get = $this->db->select('id,no')->where('id_vendor',$id_vendor)->get('ms_akta');
		$raw = $get->result_array();
		$res = array();
		$res[''] = 'Pilih salah satu';
		foreach($raw as $key => $val){
			$res[$val['id']] = $val['no'];
		}
		// echo $this->db->last_query();
		return $res;
	}

 	function get_pengurus_admin_list($id) 
    {
    	$user = $this->session->userdata('user');
		$this->db->select('*,ms_pengurus.data_status,ms_pengurus.id id,ms_akta.no akta_no, ms_pengurus.no no_identitas');
		$this->db->where('ms_pengurus.del',0);
		$this->db->where('ms_pengurus.id_vendor',$id);
		$this->db->join('ms_akta','ms_akta.id = ms_pengurus.id_akta');
		
		$query = $this->db->get('ms_pengurus');
		// echo $this->db->last_query();		
		return $query->result_array();
    }
}