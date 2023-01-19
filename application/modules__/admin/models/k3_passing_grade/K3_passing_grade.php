<?php defined('BASEPATH') OR exit('No direct script access allowed');

class k3_passing_grade extends CI_Model{ 

	function __construct(){
		parent::__construct();
		$this->field_master = array(
								'value',
								'start_score',
								'end_score',
								'entry_stamp',
							);
	}

	function get_passing_grade($search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()){
		$this->db->select('*');
		$this->db->where('del',0);
		
		$a = $this->filter->generate_query($this->db->group_by('id'),$filter);

		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
		
		$query = $a->get('tb_csms_limit');
		return $query->result_array();
		
    }

    function save_data($data){
		$this->db->insert('tb_csms_limit', $data);

		return;
	}


	function get_data($id){

		$sql = "SELECT * FROM tb_csms_limit WHERE id = ".$id;
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	function edit_data($data,$id){
		$res 	= $this->db->where('id',$id)->update('tb_csms_limit',$data);
		
		return $res;
	}
	function delete($id){
		$this->db->where('id',$id);
		
		return $this->db->update('tb_csms_limit',array('del'=>1));
	}

}