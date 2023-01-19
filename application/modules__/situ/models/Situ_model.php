<?php defined('BASEPATH') OR exit('No direct script access allowed');



class situ_model extends CI_Model{



	function __construct(){

		parent::__construct();

		$this->field_master = array(

								'id_vendor',

								'type',

								'issue_by',


								'no',

								'issue_date',

								'address',

								'situ_file',

								'file_extension_situ',

								'file_photo',

								'expire_date',

								'entry_stamp',

								'edit_stamp'

							);

	}



	function save_data($data){

		$_param = array();

		$sql = "INSERT INTO ms_situ (

							id_vendor,

							type,

							issue_by,


							no,

							issue_date,

							address,

							situ_file,

							file_extension_situ,
							file_photo,

							expire_date,

							entry_stamp,

							edit_stamp) 

				VALUES (?,?,?,?,?,?,?,?,?,?,?,?) ";

		

		

		foreach($this->field_master as $_param) $param[$_param] = $data[$_param];

		

		$this->db->query($sql, $param);
		// print_r($this->db->last_query());die;
		$id = $this->db->insert_id();

		

		return $id;

	}



	function edit_data($data,$id){

		$param = array();

		

		$this->db->where('id',$id);

		$res = $this->db->update('ms_situ',$data);

		

		return $res;

	}

	function delete($id){

		$this->db->where('id',$id);

		

		return $this->db->update('ms_situ',array('del'=>1));

	}

	

	function get_data($id){

		$user = $this->session->userdata('user');

		$sql = "SELECT * FROM ms_situ WHERE id = ".$id;

		$query = $this->db->query($sql);

		return $query->row_array();

	}



	function get_situ_list($search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()) 

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

		

		$a = $this->filter->generate_query($this->db->group_by('ms_situ.id'),$filter);

		$query = $a->get('ms_situ');

		// echo $this->db->last_query();		

		return $query->result_array();

		

    }



    function get_situ_admin_list($id) 

    {

    	$user = $this->session->userdata('user');

		$this->db->select('*');

		$this->db->where('del',0);

		$this->db->where('id_vendor',$id);

		

		

		$query = $this->db->get('ms_situ');

		// echo $this->db->last_query();		

		return $query->result_array();

    }

}