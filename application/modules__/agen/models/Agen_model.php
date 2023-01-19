<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Agen_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->field_master = array(
							'no',
							'principal',
							'issue_date',
							'expire_date',
							'type',
							'agen_file',
							'id_vendor',
							'entry_stamp'
							);
		$this->bsb = array('id_vendor',
							'id_agen',
							'id_bsb',
							'entry_stamp');
		$this->produk = array('id_agen',
							'produk',
							'merk',
							'entry_stamp');
	}


	function get_agen_list($search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()) 
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
		
		
		$a = $this->filter->generate_query($this->db->group_by('ms_agen.id'),$filter);
		$query = $a->get('ms_agen');		
		return $query->result_array();
		
    }
   
	function save_data($data){

		$_param = array();
		$sql = "INSERT INTO ms_agen (
							`no`,
							`principal`,
							`issue_date`,
							`expire_date`,
							`type`,
							`agen_file`,
							`id_vendor`,
							`entry_stamp`) 
				VALUES (?,?,?,?,?,?,?,?) ";
		
		
		foreach($this->field_master as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);
		$id = $this->db->insert_id();
		
		return $id;
	}
	function edit_data($data,$id){
				
		$this->db->where('id',$id);
		$this->db->update('ms_agen',$data);

		
		$id = $this->db->insert_id();
		
		return $id;
	}
	function delete($id){
		$this->db->where('id',$id);
		
		return $this->db->update('ms_agen',array('del'=>1));
	}

	function get_data($id){

		$sql = "SELECT * FROM ms_agen WHERE id = ".$id;
		$query = $this->db->query($sql);
		return $query->row_array();
	}
	function get_dpt_agen(){
		$user = $this->session->userdata('user');
		$this->db->select('ms_ijin_usaha.id_dpt_type id_dpt_type');
		$this->db->where('ms_iu_bsb.id_vendor',$user['id_user']);
		$this->db->join('ms_ijin_usaha','ms_ijin_usaha.id=ms_iu_bsb.id_ijin_usaha');
		$sql = $this->db->get('ms_iu_bsb');
		
		return $sql->row_array();
	}
	function get_sub_bidang($id_bidang){
		$get = $this->db->select('id,name')->where('id_bidang',$id_bidang)->get('tb_sub_bidang');
		$raw = $get->result_array();
		$res = array();
		$res[''] = 'Pilih Bidang';
		foreach($raw as $key => $val){
			$res[$val['id']] = $val['name'];
		}
		return $res;
	}
	function get_bsb_list($id, $search='', $sort='', $page='', $per_page='',$is_page=FALSE) 
    {
    	$user = $this->session->userdata('user');
		$this->db->select('ms_agen_bsb.id id,tb_bidang.name as bidang_name,tb_sub_bidang.name as sub_bidang_name')
		->join('tb_sub_bidang','tb_sub_bidang.id=ms_agen_bsb.id_bsb')
		->join('tb_bidang','tb_bidang.id=tb_sub_bidang.id_bidang');
		
		
		$this->db->where('ms_agen_bsb.del',0);
		$this->db->where('ms_agen_bsb.id_vendor',$user['id_user']);
		$this->db->where('id_agen',$id);
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
		
		$query = $this->db->get('ms_agen_bsb');	
		return $query->result_array();
		
    }
    function save_bsb($data){

		$_param = array();
		$sql = "INSERT INTO ms_agen_bsb (
							`id_vendor`,
							`id_agen`,
							`id_bsb`,
							`entry_stamp`
							) 
				VALUES (?,?,?,?) ";
		
		
		foreach($this->bsb as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);
		$id = $this->db->insert_id();
		
		return $id;
	
	}
    function get_bidang($id_dpt_type){
		$get = $this->db->select('id,name')->where('id_dpt_type',$id_dpt_type)->get('tb_bidang');
		$raw = $get->result_array();
		$res = array();
		$res[''] = 'Pilih Bidang';
		foreach($raw as $key => $val){
			$res[$val['id']] = $val['name'];
		}
		return $res;
	}
	function get_bsb_data($id){
		$get = $this->db->select('id_bidang, id_bsb')
		->join('tb_sub_bidang','tb_sub_bidang.id_bidang=ms_agen_bsb.id_bsb')
		->join('tb_bidang','tb_bidang.id=tb_sub_bidang.id_bidang')
		->where('ms_agen_bsb.id',$id)
		->get('ms_agen_bsb');
		// $query = $this->db->query($sql);
		return $get->row_array();
    }
    function edit_bsb($data,$id){

		unset($data['id_bidang']);
		unset($data['id_sub_bidang']);

		$this->db->where('id',$id);
		$res = $this->db->update('ms_agen_bsb',$data);

		
		return $res;
	}
	function delete_bsb($id){
		$this->db->where('id',$id);
		
		return $this->db->update('ms_agen_bsb',array('del'=>1));
	}

	function get_produk_list($id, $search='', $sort='', $page='', $per_page='',$is_page=FALSE) 
    {
    	$user = $this->session->userdata('user');
		$this->db->select('ms_agen_produk.id id,produk,merk');
		
		
		
		$this->db->where('ms_agen_produk.del',0);
		$this->db->where('id_agen',$id);
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
		
		$query = $this->db->get('ms_agen_produk');		
		return $query->result_array();
		
    }

     function save_produk($data){

		$_param = array();
		$sql = "INSERT INTO ms_agen_produk (
							`id_agen`,
							`produk`,
							`merk`,
							`entry_stamp`
							) 
				VALUES (?,?,?,?) ";
		
		
		foreach($this->produk as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);
		$id = $this->db->insert_id();
		
		return $id;
	
	}

	function edit_data_produk($data,$id){

		$this->db->where('id',$id);
		$result = $this->db->update('ms_agen_produk',$data);
		if($result)return $id;

	}

	function get_data_produk($id){

		$sql = "SELECT * FROM ms_agen_produk WHERE id = ".$id;
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	function delete_produk($id){
		$this->db->where('id',$id);
		
		return $this->db->update('ms_agen_produk',array('del'=>1));
	}

	function get_agen_admin_list($id) 
    {
    	$user = $this->session->userdata('user');
		$this->db->select('*');
		$this->db->where('ms_agen.del',0);
		$this->db->where('ms_agen.id_vendor',$id);
		
		$query = $this->db->get('ms_agen');
		//echo $this->db->last_query();		
		return $query->result_array();
    }

    function get_produk_admin_list($id_data,$id) 
    {
    	$user = $this->session->userdata('user');
		$this->db->select('*');
		$this->db->where('ms_agen_produk.del',0);
		
		$query = $this->db->get('ms_agen_produk');
		//echo $this->db->last_query();		
		return $query->result_array();
    }
}