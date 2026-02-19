<?php defined('BASEPATH') OR exit('No direct script access allowed');

class pemilik_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->field_master = array(
								'id_vendor',
								'name',
								'id_akta',
								'percentage',
								'shares',
								'data_status',
								'entry_stamp'
							);
	}

	function save_data($data){
		$_param = array();
		$sql = "INSERT INTO ms_pemilik (
							`id_vendor`,
							`name`,
							`id_akta`,
							`percentage`,
							`shares`,
							`data_status`,
							`entry_stamp`
							) 
				VALUES (?,?,?,?,?,?,?) ";
		
		
		foreach($this->field_master as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);
		$id = $this->db->insert_id();
		
		return $id;
	}

	function edit_data($data,$id){
		$param = array();
		
		$this->db->where('id',$id);
		$res = $this->db->update('ms_pemilik',$data);

		return $res;
	}
	function delete($id){
		$this->db->where('id',$id);
		
		return $this->db->update('ms_pemilik',array('del'=>1));
	}
	
	function get_data($id){
		$user = $this->session->userdata('user');
		$sql = "SELECT * FROM ms_pemilik WHERE id = ".$id;
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	function get_pemilik_list($search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()) 
    {
    	$user = $this->session->userdata('user');
		$this->db->select('msp.*');
		$this->db->where('msp.del',0);
		$this->db->where('msp.id_vendor',$user['id_user']);
		$this->db->join("ms_vendor", "ms_vendor.id = msp.id_vendor");
		
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
		

		$a = $this->filter->generate_query($this->db->group_by('msp.id'),$filter);
		
		$query = $a->get('ms_pemilik as msp');
		
		return $query->result_array();
		
    }

    function get_akta(){
		$get = $this->db->select('id,no')->get('ms_akta');
		$raw = $get->result_array();
		$res = array();
		$res[''] = 'Pilih salah satu';
		foreach($raw as $key => $val){
			$res[$val['id']] = $val['no'];
		}
		return $res;
	}
	function get_akta_list(){
		$get = $this->db->select('*')
						->where('id_vendor',$this->session->userdata('user')['id_user'])
						->get('ms_akta');


		$raw = $get->result_array();
		$res = array();
		$res[''] = 'Pilih salah satu';
		foreach($raw as $key => $val){
			$res[$val['id']] = $val['no'];
		}
		return $res;
	}

	function get_pemilik_admin_list($id) 
    {
    	$user = $this->session->userdata('user');
		$this->db->select('*');
		$this->db->where('ms_pemilik.del',0);
		$this->db->where('ms_pemilik.id_vendor',$id);
		
		$query = $this->db->get('ms_pemilik');
		// echo $this->db->last_query();		
		return $query->result_array();
    }
    function get_total_percentage($id=0){
    	$user = $this->session->userdata('user');
    	$res = $this->db->select('SUM(percentage) as sum')->where('del',0)->where('id<>',$id)->where('id_vendor',$user['id_user'])->get('ms_pemilik')->row_array();
    	return $res['sum'];
    }
	public function get_ubo($id_vendor)
	{
		$a = $this->db->where('del', 0)->where('id_vendor', $id_vendor)->get('tr_surat_ubo')->row_array();
		return $a;
	}
}