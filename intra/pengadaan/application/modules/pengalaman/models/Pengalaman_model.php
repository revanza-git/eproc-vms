<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pengalaman_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->field_master = array(
					'job_name',
					'job_location',
					'job_giver',
					'phone_no',
					'contract_no',
					'contract_start',
					'price_idr',
					'price_foreign',
					'currency',
					'contract_end',
					'bast_date',
					'contract_file',
					'bast_file',
					'id_iu_bsb',
					'id_ijin_usaha',
					'id_vendor',
					'entry_stamp'
				);
		
	}


	function get_dpt_type(){
		$this->db->select('*');
		$query = $this->db->get('tb_dpt_type');
		return $query->result_array();
	}

	function get_data($id){
		$user = $this->session->userdata('user');
		$sql = "SELECT * FROM ms_pengalaman WHERE id = ".$id;
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	function get_pengalaman_list($search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()) 
    {
    	$user = $this->session->userdata('user');
		$this->db->select('ms_pengalaman.*');//,tb_bidang.name bidang_name, tb_sub_bidang.name sub_bidang_name
		$this->db->where('ms_pengalaman.del',0);
		$this->db->where('ms_pengalaman.id_vendor',$user['id_user']);
		// $this->db->join('ms_iu_bsb','ms_iu_bsb.id = ms_pengalaman.id_iu_bsb');
		// $this->db->join('tb_bidang','tb_bidang.id = ms_iu_bsb.id_bidang');
		// $this->db->join('tb_sub_bidang','tb_sub_bidang.id = ms_iu_bsb.id_sub_bidang');
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}

		
		$a = $this->filter->generate_query($this->db->group_by('ms_pengalaman.id'),$filter);
		$query = $a->get('ms_pengalaman');		
		return $query->result_array();
		
    }
   
	function save_data($data){

		$_param = array();
		$sql = "INSERT INTO ms_pengalaman (
							`job_name`,
							`job_location`,
							`job_giver`,
							`phone_no`,
							`contract_no`,
							`contract_start`,
							`price_idr`,
							`price_foreign`,
							`currency`,
							`contract_end`,
							`bast_date`,
							`contract_file`,
							`bast_file`,
							`id_iu_bsb`,
							`id_ijin_usaha`,
							`id_vendor`,
							`entry_stamp`
							) 
				VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
		
		
		foreach($this->field_master as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);
		$id = $this->db->insert_id();
		
		return $id;
	}
	function edit_data($data,$id){
				
		$this->db->where('id',$id);
		$res = $this->db->update('ms_pengalaman',$data);

		return $res;
	}
	function delete($id){
		$this->db->where('id',$id);
		return $this->db->update('ms_pengalaman',array('del'=>1));
	}
	function get_data_full($id_data,$id){
		
		$this->db->select('*,ms_pengalaman.id id, job_name,tb_bidang.name bidang,tb_sub_bidang.name sub_bidang, contract_start , ms_pengalaman.data_status data_status');
		$this->db->where('ms_pengalaman.del',0);
		$this->db->where('ms_pengalaman.id',$id);
		$this->db->where('ms_pengalaman.id_vendor',$id_data);
		$this->db->join('ms_iu_bsb','ms_iu_bsb.id = ms_pengalaman.id_iu_bsb', 'LEFT');
		$this->db->join('tb_bidang','tb_bidang.id = ms_iu_bsb.id_bidang', "LEFT");
		$this->db->join('tb_sub_bidang','tb_sub_bidang.id = ms_iu_bsb.id_sub_bidang', 'LEFT');
		$query = $this->db->get('ms_pengalaman');
		//echo $this->db->last_query();
		return $query->row_array();
	}
	function get_pengalaman_admin_list($id) 
    {
		$this->db->select('ms_pengalaman.id id, job_name,tb_bidang.name bidang,tb_sub_bidang.name sub_bidang, contract_start , ms_pengalaman.data_status data_status');
		$this->db->where('ms_pengalaman.del',0);
		$this->db->where('ms_pengalaman.id_vendor',$id);
		$this->db->join('ms_iu_bsb','ms_iu_bsb.id = ms_pengalaman.id_iu_bsb', 'LEFT');
		$this->db->join('tb_bidang','tb_bidang.id = ms_iu_bsb.id_bidang', 'LEFT');
		$this->db->join('tb_sub_bidang','tb_sub_bidang.id = ms_iu_bsb.id_sub_bidang', 'LEFT');
		$query = $this->db->get('ms_pengalaman');
		return $query->result_array();
    }
}