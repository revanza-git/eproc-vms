<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Izin_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->field_master = array(
							'id_vendor',
							'id_dpt_type',
							'type',
							'no',
							'grade',
							'issue_date',
							'qualification',
							'authorize_by',
							'izin_file',
							'expire_date',
							'entry_stamp'
							);
		$this->field_bidang = array(
							'id_dpt_type',
							'name',
							'entry_stamp',
							);
		$this->field_sub_bidang = array(
							'id_bidang',
							'name',
							'entry_stamp',
							);
		$this->bsb = array('id_vendor',
							'id_ijin_usaha',
							'id_bidang',
							'id_sub_bidang',
							'entry_stamp');
	}


	function get_dpt_type(){
		$this->db->select('*');
		$query = $this->db->get('tb_dpt_type');
		return $query->result_array();
	}

	function get_filter_dpt_type(){
		$this->db->select('*');
		$query = $this->db->get('tb_dpt_type')->result_array();
		$res = array();
		$res[''] = 'Pilih Salah Satu';
		foreach($raw as $key => $val){
			$res[$val['id']] = $val['name'];
		}
		return $res;
	}

	function get_bsb_dropdown($iu){
		$user = $this->session->userdata('user');
		$get = $this->db->select('ms_iu_bsb.id id,CONCAT(tb_bidang.name, "-",tb_sub_bidang.name) as name')->where('id_ijin_usaha',$iu)->join('tb_bidang','tb_bidang.id = ms_iu_bsb.id_bidang')->join('tb_sub_bidang','tb_sub_bidang.id = ms_iu_bsb.id_sub_bidang')->where('id_vendor',$user['id_user'])->get('ms_iu_bsb');
		$raw = $get->result_array();
		$res = array();
		$res[''] = 'Pilih Bidang';
		foreach($raw as $key => $val){
			$res[$val['id']] = $val['name'];
		}
		return $res;
	}
	function get_siu_dropdown(){
		$user = $this->session->userdata('user');
		$get = $this->db->select('ms_ijin_usaha.id id,CONCAT(ms_ijin_usaha.type, "-",ms_ijin_usaha.no) as name')
		->where('id_vendor',$user['id_user'])
		->get('ms_ijin_usaha');
		$raw = $get->result_array();
		$res = array();
		$res[''] = 'Pilih Izin Usaha';
		foreach($raw as $key => $val){
			$res[$val['id']] = $val['name'];
		}
		return $res;
	}
	function get_izin_list($search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()){
    	$user = $this->session->userdata('user');
		$this->db->select('*,ms_ijin_usaha.id as id, tb_dpt_type.name dpt_name');
		$this->db->where('del',0);
		$this->db->where('id_vendor',$user['id_user']);
		$this->db->join('tb_dpt_type','tb_dpt_type.id = ms_ijin_usaha.id_dpt_type');
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
		
		$a = $this->filter->generate_query($this->db->group_by('ms_ijin_usaha.id'),$filter);
		$query = $a->get('ms_ijin_usaha');		
		return $query->result_array();
		
    }
    function get_bsb_data($id){
    	$sql = "SELECT id_bidang, id_sub_bidang FROM ms_iu_bsb WHERE id = ".$id;
		$query = $this->db->query($sql);
		return $query->row_array();
    }
    function get_bsb_list($id, $search='', $sort='', $page='', $per_page='',$is_page=FALSE) 
    {
    	$user = $this->session->userdata('user');
		$this->db->select('ms_iu_bsb.id id,tb_bidang.name as bidang_name,tb_sub_bidang.name as sub_bidang_name')->join('tb_bidang','tb_bidang.id=ms_iu_bsb.id_bidang')->join('tb_sub_bidang','tb_sub_bidang.id=ms_iu_bsb.id_sub_bidang');
		$this->db->where('ms_iu_bsb.del',0);
		$this->db->where('id_vendor',$user['id_user']);
		$this->db->where('id_ijin_usaha',$id);
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
		
		$query = $this->db->get('ms_iu_bsb');		
		return $query->result_array();
		
    }
	function save_data($data){
		$data['authorize_by'] = (isset($data['authorize_by'])?$data['authorize_by']:'');
		$data['qualification'] = (isset($data['qualification'])?$data['qualification']:'');
		$_param = array();

		$get_iu_saved = $this->db->select('id')
		->where('id_dpt_type',$data['id_dpt_type'])
		->where('id_vendor',$data['id_vendor'])
		->where('type',$data['type'])
		->where('no',$data['no'])
		->where("`issue_date`<='".$data['issue_date']."' AND `expire_date` >= '".$data['expire_date']."'",NULL,FALSE)
		->where('del',0)
		->get('ms_ijin_usaha')->num_rows();

		if($get_iu_saved==0){
			$sql = "INSERT INTO ms_ijin_usaha (
								id_vendor,
								id_dpt_type,
								type,
								no,
								grade,
								issue_date,
								qualification,
								authorize_by,
								izin_file,
								expire_date,
								entry_stamp
								) 
					VALUES (?,?,?,?,?,?,?,?,?,?,?) ";
			
			
			foreach($this->field_master as $_param) $param[$_param] = $data[$_param];
			
			$this->db->query($sql, $param);
			$id = $this->db->insert_id();
			
			return $id;
		}else{
			return false;
		}
	}
	function edit_data($data,$id){
				
		$this->db->where('id',$id);
		$res = $this->db->update('ms_ijin_usaha',$data);
		
		return $res;
	}
	function delete($id){
		$this->db->where('id',$id);
		
		return $this->db->update('ms_ijin_usaha',array('del'=>1));
	}
	function get_bidang($id_dpt_type){

		$get = $this->db->select('tb_bidang.id id,tb_bidang.name name')->where('id_dpt_type',$id_dpt_type)->where('del', 0)
		// ->join('tb_sub_bidang','tb_bidang.id=tb_sub_bidang.id_bidang','INNER')
		->get('tb_bidang');
		$raw = $get->result_array();
		$res = array();
		$res[''] = 'Pilih Bidang';
		foreach($raw as $key => $val){
			$res[$val['id']] = $val['name'];
		}
		return $res;
	}

	
	//BIDANG
	function get_bidang_list($search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()){
		$this->db->select('tb_bidang.name name, tb_dpt_type.name id_dpt_type,tb_bidang.id id');
		$this->db->where('del',0);
		$this->db->join('tb_dpt_type','tb_dpt_type.id=tb_bidang.id_dpt_type');
		
		$a = $this->filter->generate_query($this->db->group_by('id'),$filter);

		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
		
		$query = $a->get('tb_bidang');
		return $query->result_array();
    }

    function get_bidang_dropdownlist(){
    	$get = $this->db->select('id,name')->get('tb_dpt_type');
		$raw = $get->result_array();
		$res = array();
		$res[''] = 'Pilih salah satu';
		foreach($raw as $key => $val){
			$res[$val['id']] = $val['name'];
		}
		return $res;
    }

    function get_data_bidang($id){

		$sql = "SELECT * FROM tb_bidang WHERE id = ".$id;
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	function get_data_sub_bidang($id){
		$sql = "SELECT * FROM tb_sub_bidang WHERE id = ".$id;
		$query = $this->db->query($sql);
		return $query->row_array();
	}

    function save_data_bidang($data){
		$data['authorize_by'] 	= (isset($data['authorize_by'])?$data['authorize_by']:'');
		$data['qualification'] 	= (isset($data['qualification'])?$data['qualification']:'');
		$_param 				= array();

		$get_iu_saved 			= $this->db->select('id')
		->where('id_dpt_type',$data['id_dpt_type'])
		->where('name',$data['name'])
		->where('del',0)
		->get('tb_bidang')->num_rows();

		if($get_iu_saved==0){
			$sql = "INSERT INTO tb_bidang (
								id_dpt_type,
								name,
								entry_stamp,
								del
								) 
					VALUES (?,?,?,0) ";
			
			
			foreach($this->field_bidang as $_param) $param[$_param] = $data[$_param];
			
			$this->db->query($sql, $param);
			$id = $this->db->insert_id();
			
			return $id;
		}else{
			return false;
		}
	}
	function edit_data_bidang($data,$id){
				
		$this->db->where('id',$id);
		$res = $this->db->update('tb_bidang',$data);
		
		return $res;
	}
	function delete_bidang($id){
		$this->db->where('id',$id);
		return $this->db->update('tb_bidang',array('del'=>1));
	}
    //END of BIDANG


	function get_sub_bidang_list($search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()){
		$this->db->select('tb_sub_bidang.*, tb_bidang.name id_bidang');
		$this->db->where('tb_sub_bidang.del',0);
		$this->db->join('tb_bidang','tb_bidang.id=tb_sub_bidang.id_bidang');
		
		$a = $this->filter->generate_query($this->db->group_by('id'),$filter);

		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
		
		$query = $a->get('tb_sub_bidang');
		return $query->result_array();
    }
    function edit_data_sub_bidang($data,$id){
				
		$this->db->where('id',$id);
		$res = $this->db->update('tb_sub_bidang',$data);
		
		return $res;
	}
	function delete_sub_bidang($id){
		$this->db->where('id',$id);
		return $this->db->update('tb_sub_bidang',array('del'=>1));
	}
	function get_bidang_dropdown($id_bidang = null){
		$get = $this->db->select('id,name')->get('tb_bidang');
		$raw = $get->result_array();
		$res = array();
		$res[''] = 'Pilih Bidang';
		foreach($raw as $key => $val){
			$res[$val['id']] = $val['name'];
		}
		return $res;
	}
	function get_sub_bidang($id_bidang){
		$get = $this->db->select('id,name')->where('id_bidang',$id_bidang)->get('tb_sub_bidang');
		$raw = $get->result_array();
		$res = array();
		$res[''] = 'Pilih Sub Bidang';
		foreach($raw as $key => $val){
			$res[$val['id']] = $val['name'];
		}
		return $res;
	}
	function save_data_sub_bidang($data){
		$data['authorize_by'] 	= (isset($data['authorize_by'])?$data['authorize_by']:'');
		$data['qualification'] 	= (isset($data['qualification'])?$data['qualification']:'');
		$_param 				= array();

		$get_sub_bidang 		= $this->db->select('*')
								->where('id_bidang',$data['id_bidang'])
								->where('name',$data['name'])
								->where('del',0)
								->get('tb_sub_bidang')->num_rows();

		if($get_sub_bidang==0){
			$sql = "INSERT INTO tb_sub_bidang (
								id_bidang,
								name,
								entry_stamp,
								del
								) 
					VALUES (?,?,?,0) ";
			
			
			foreach($this->field_sub_bidang as $_param) $param[$_param] = $data[$_param];
			
			$this->db->query($sql, $param);
			$id = $this->db->insert_id();
			
			return $id;
		}else{
			return false;
		}
	}
	function get_data($id){

		$sql = "SELECT * FROM ms_ijin_usaha WHERE id = ".$id;
		$query = $this->db->query($sql);
		return $query->row_array();
	}
	function save_bsb($data){

		$_param = array();
		$sql = "INSERT INTO ms_iu_bsb (
							id_vendor,
							id_ijin_usaha,
							id_bidang,
							id_sub_bidang,
							entry_stamp
							) 
				VALUES (?,?,?,?,?) ";
		
		
		foreach($this->bsb as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);
		$id = $this->db->insert_id();
		
		return $id;
	
	}
	function edit_bsb($data,$id){
				
		$this->db->where('id',$id);
		$res = $this->db->update('ms_iu_bsb',$data);

		return $res;
	}
	function delete_bsb($id){
		$this->db->where('id',$id);
		
		return $this->db->update('ms_iu_bsb',array('del'=>1));
	}

	function get_izin_admin_list($id,$type) 
    {
    	$user = $this->session->userdata('user');
		$this->db->select('*,ms_ijin_usaha.id id,tb_dpt_type.name dpt');
		$this->db->where('ms_ijin_usaha.del',0);
		$this->db->where('ms_ijin_usaha.id_vendor',$id);
		$this->db->where('ms_ijin_usaha.type',$type);
		$this->db->join('tb_dpt_type','tb_dpt_type.id = ms_ijin_usaha.id_dpt_type');
		$query = $this->db->get('ms_ijin_usaha');
		// echo $this->db->last_query();		
		return $query->result_array();
    }
    function get_bsb_admin_list($id) 
    {
    	$user = $this->session->userdata('user');
		$this->db->select('ms_iu_bsb.id id, tb_bidang.name bidang,tb_sub_bidang.name sub_bidang,ms_iu_bsb.data_status data_status');
		$this->db->join('tb_bidang','tb_bidang.id = ms_iu_bsb.id_bidang');
		$this->db->join('tb_sub_bidang','tb_sub_bidang.id = ms_iu_bsb.id_sub_bidang');
		$this->db->where('ms_iu_bsb.del',0);
		$this->db->where('ms_iu_bsb.id_ijin_usaha',$id);
		$query = $this->db->get('ms_iu_bsb');
		return $query->result_array();
    }



    //Report 
    function get_izin_report($id){
		$this->db->select('*')
				->where('ms_ijin_usaha.del',0)
				->where('ms_ijin_usaha.id_vendor',$id);

		$query = $this->db->get('ms_ijin_usaha')->result_array();

		$surat_izin	=	array();
		foreach ($query as $key => $value) {
			//bidang-sub-bidang
			$this->db->select('*')
				->where('ms_iu_bsb.del',0)
				->where('ms_iu_bsb.id_ijin_usaha',$value['id']);
			$bsbquery 	= $this->db->get('ms_iu_bsb')->result_array();
			$bidang		= array();

			foreach ($bsbquery as $keybsb => $valuebsb) {
				$value['bsb'][$this->get_bidang_report()[$valuebsb['id_bidang']]['name']][]=$this->get_sub_bidang_report()[$valuebsb['id_sub_bidang']]['name'];
			}
			$surat_izin[$value['type']][] = $value;
		}
		// echo print_r($surat_izin);
		return $surat_izin;
    }

    function get_bidang_report(){
    	$this->db->select('*');
			// ->where('tb_bidang.del',0);

		$query 	= $this->db->get('tb_bidang')->result_array();

		$bidang_list	=	array();
		foreach ($query as $key => $value) {
			$bidang_list[$value['id']] = $value;
		}
		return $bidang_list;
	}

	function get_sub_bidang_report(){
    	$this->db->select('*');
			// ->where('tb_sub_bidang.del',0);

		$query 	= $this->db->get('tb_sub_bidang')->result_array();

		$sub_bidang_list	=	array();
		foreach ($query as $key => $value) {
			$sub_bidang_list[$value['id']] = $value;
		}
		return $sub_bidang_list;
	}

	function get_situ_report($id){
		$this->db->select('*')
					->where('id_vendor', $id)
					->where('del', 0);
		$query 	= $this->db->get('ms_situ');

		return $query->result_array();
	}

	function get_tdp_report($id){
		$this->db->select('*')
					->where('id_vendor', $id)
					->where('del', 0);
		$query 	= $this->db->get('ms_tdp');

		return $query->result_array();
	}

	function get_keagenan_report($id){
		$this->db->select('*')
					->where('id_vendor', $id)
					->where('ms_agen.del', 0)
					->join('ms_agen_produk', 'ms_agen.id=ms_agen_produk.id_agen');


		$query 	= $this->db->get('ms_agen');



		return $query->result_array();
	}

	function get_agen_produk_report($id){
		$this->db->select('*');
		$query 	= $this->db->get('ms_agen_produk');

		return $query->result_array();
	}

    function get_pengalaman_report($id){
		$this->db->select('*')
				->where('id_vendor', $id);

		$query 	= $this->db->get('ms_pengalaman');

		return $query->result_array();
	}
}


	function get_data_tb(){
		$sql['sql'] = "SELECT bidang.*, dpt.name AS group_bidang FROM tb_bidang bidang LEFT JOIN tb_dpt_type dpt ON bidang.id_dpt_type = dpt.id WHERE 1 = 1 ";
		return $sql;
	}
