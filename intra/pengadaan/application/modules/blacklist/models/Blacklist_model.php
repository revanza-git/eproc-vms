<?php defined('BASEPATH') OR exit('No direct script access allowed');

class blacklist_model extends CI_Model{
	public $CI;
	function __construct(){
		parent::__construct();
		$this->field_master = array(
								'id_vendor',
								'id_blacklist',
								'start_date',
								'end_date',
								'remark',
								'blacklist_file',
								'entry_stamp'
							);
		$this->CI =& get_instance();
        $this->CI->load->model('vendor_model','vm');
	}

	function check_blacklist($poin,$id_vendor,$site){

		$blacklist = $this->db->where('del',0)/*->where_in('id',array(1,2))*/->get('tb_blacklist_limit')->result_array();

		$data_vendor = $this->CI->vm->get_vendor_name($id_vendor);

		$data = array('id_vendor'=>$id_vendor, 'vendor_name'=>$data_vendor['name'],'site'=>$site);

		foreach($blacklist as $key => $row){
			
			if(($poin<=$row['start_score']&&$row['end_score']==NULL)||($poin<=$row['start_score']&&$poin>=$row['end_score'])||($row['start_score']==NULL&&$poin>=$row['end_score'])){
				$data['id_blacklist'] = $row['id'];
				$data['poin'] 		= $poin;
				$data['start_date']	= date('Y-m-d');
				$data['end_date']	= ($row['range_time']=='forever') ? 'lifetime' : date('Y-m-d', strtotime('+'.$row['number_range'].' '.$row['range_time'], strtotime($data['start_date'])) );
				return $data;
			}
			
		}

		return false;
	}
	function get_form_data($id){
		$query = $this->db->where('id',$id)->get('tb_blacklist_limit');
		return $query->row_array();
	}

	function blacklist_data($id_blacklist){
		$sql = $this->db->where('tr_blacklist.id',$id_blacklist)
		->join('ms_vendor','tr_blacklist.id_vendor = ms_vendor.id')
		->join('ms_vendor_admistrasi','tr_blacklist.id_vendor = ms_vendor_admistrasi.id_vendor')
		->get('tr_blacklist');
		return $sql->row_array();
	}

	function search_vendor(){
		$result = array();
		$query = $this->db  ->select('id, name')
							->like('name',$this->input->post('term'),'both')
							->where('del',0)
							->where('is_active',1)
							// ->or_where('is_blacklist IS NULL')
							->get('ms_vendor')
							->result_array();

		foreach($query as $key => $value){
			$result[$value['id']]['id'] = $value['id'];
			$result[$value['id']]['name'] = $value['name'];
		}

		return $result;
	}

	function aktif($data){
		$res = $this->db->where('id',$data['id'])
				->update('tr_blacklist',array(
											'is_white'		=> 1,
											'need_approve'	=> 1,
											'white_date' 	=> $data['white_date'],
											'white_file'	=> $data['white_file'],
											'edit_stamp'	=> $data['edit_stamp']
										)
						);
		return $res;
	}

	function save_data($data){
		$_param = array();
		$this->db->where('id_vendor',$data['id_vendor'])->update('tr_blacklist',array('del'=>1));
		

		$sql = "INSERT INTO tr_blacklist (
							id_vendor,
							id_blacklist,
							start_date,
							end_date,
							remark,
							blacklist_file,
							entry_stamp,
							need_approve
							) 
				VALUES (?,?,?,?,?,?,?,1) ";
		
		
		foreach($this->field_master as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);

		$id = $this->db->insert_id();
		
		$this->db->where('id',$data['id_vendor'])->update('ms_vendor',array('vendor_status'=>1));

		return $id;
	}

	function edit_data($data,$id){
		$this->db->where('id',$id);
		

		$result = $this->db->update('tr_blacklist',$data);
		if($result)return $id;
	}
	function delete($id){
		$this->db->where('id',$id);
		$this->db->update('ms_vendor',array('is_active'=>1));

		$this->db->where('id',$id);
		return $this->db->update('tr_blacklist',array('del'=>1));
	}
	
	function get_data($id){

		$sql = "SELECT tr_blacklist.*,ms_vendor.name as vendor_name, ms_vendor_admistrasi.vendor_email as vendor_email, tb_blacklist_limit.value as blacklist_name FROM tr_blacklist 

				JOIN ms_vendor ON ms_vendor.id = tr_blacklist.id_vendor
				JOIN ms_vendor_admistrasi ON ms_vendor_admistrasi.id_vendor = tr_blacklist.id_vendor
				JOIN tb_blacklist_limit ON tb_blacklist_limit.id = tr_blacklist.id_blacklist

				WHERE tr_blacklist.id = ".$id;
		$query = $this->db->query($sql);

		return $query->row_array();
	}

	function get_autocomplete($keyword){
		$this->db->order_by('id', 'DESC');
        $this->db->like("name", $keyword);
        return $this->db->get('ms_vendor')->result_array();
	}

	function get_blacklist_data($id_vendor){
		$sql = $this->db->where('id_vendor',$id_vendor)
		->where('tr_blacklist.del',0)
		->where('is_white',0)
		->where('tr_blacklist.need_approve',0)
		->join('ms_vendor','tr_blacklist.id_vendor = ms_vendor.id')
		->get('tr_blacklist',0,1);
		
		return $sql->row_array();
	}

	function get_blacklist_by_vendor($id){
		$query = 	"	SELECT
							*
						FROM 
							tr_blacklist a
						LEFT JOIN
							ms_vendor b ON a.id_vendor = b.id
						WHERE 
							a.id = ?
							AND a.del 	= ?
					";
		return $this->db->query($query, array($id, 0))->row_array();
	}

	function get_blacklist_list($id, $search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()){
    	$admin = $this->session->userdata('admin');

    	$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

		$this->db->select('	ms_vendor.name,
							tr_blacklist.*, 
							tr_blacklist.id id_tr,
							
							(SELECT COUNT(*) FROM tr_blacklist a where tr_blacklist.id_vendor = a.id_vendor AND tr_blacklist.id_blacklist = '.$id.') as total_bl,  
							tr_assessment_point.point, 
							tb_blacklist_limit.value category, 
							tb_blacklist_limit.value blacklist_val,
							trb.need_approve need_approve_bl,
							trb.del del,
						');
		$this->db->where('tr_blacklist.del', 0);
		$this->db->where('tr_blacklist.id_blacklist', $id);
		$this->db->where('tr_blacklist.id IN (SELECT max(id) FROM tr_blacklist WHERE tr_blacklist.id_blacklist = '.$id.' GROUP BY id_vendor order by id ASC)',NULL,FALSE);

		$this->db->order_by('tr_blacklist.id', 'DESC');
		$this->db->join('ms_vendor','ms_vendor.id=tr_blacklist.id_vendor', 'LEFT');
		$this->db->join('tr_assessment_point','tr_assessment_point.id_vendor=ms_vendor.id', 'LEFT');
		$this->db->join('tb_blacklist_limit','tb_blacklist_limit.id=tr_assessment_point.category', 'LEFT');
		$this->db->join('tr_blacklist trb','tr_blacklist.id_vendor=trb.id_vendor AND trb.del=0', 'LEFT');
    	$a = $this->filter->generate_query($this->db->group_by('ms_vendor.id'),$filter);

		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
		
		
		$query = $a->get('tr_blacklist');
		// echo $this->db->last_query();
		return $query->result_array();
    }


    function get_remark($id_blacklist){
		$this->db->select('id, remark, type')->where('del',0)->where('type',$id_blacklist);
        return $this->db->get('tr_blacklist_remark')->result_array();
	}

	function approve_blacklist($data){
		$app = $this->db->where('id',$data['id_blacklist'])->update('tr_blacklist',array('need_approve'=>0,'edit_stamp'=>$data['edit_stamp']));
		if($app){
			$sql = "UPDATE `ms_vendor` SET `ever_blacklisted` = `ever_blacklisted` + 1 , `is_active` = 0 WHERE id =".$data['id_vendor'];
			return $this->db->query($sql);
		}else{
			return false;
		}
	}


	function whitelist_data($filter = array(), $sort = array(), $page = '', $per_page = 10, $is_page = FALSE){
		
		$admin = $this->session->userdata('admin');    	
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

		$this->db->select('ms_vendor.*, ms_vendor.id id_vendor_bl,tr_assessment_point.point, (SELECT COUNT(*) FROM tr_blacklist a WHERE a.white_date IS NOT NULL AND a.id_vendor = id_vendor_bl) as total_white, tb_blacklist_limit.value category, MAX(white_date) white_date,  tr_blacklist.need_approve need_approve_bl, tr_blacklist.*, tr_blacklist.id id_tr, tb_blacklist_limit.value blacklist_val');


		$this->db->where('ever_blacklisted >',0);

		$this->db->group_by('id_vendor');
		$this->db->join('ms_vendor','ms_vendor.id=tr_blacklist.id_vendor','LEFT');
		
		$this->db->join('tr_assessment_point','tr_assessment_point.id_vendor=ms_vendor.id','LEFT');
		// $this->db->join('tb_blacklist_limit','tb_blacklist_limit.id=tr_assessment_point.category');
		$this->db->join('tb_blacklist_limit','tr_blacklist.id_blacklist=tb_blacklist_limit.id','LEFT');


    	$a = $this->filter->generate_query($this->db->group_by('ms_vendor.id'),$filter);

		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}

    	$a = $this->filter->generate_query($this->db->group_by('ms_vendor.id'),$filter);

		$sql = $a->get('tr_blacklist');
		return $sql->result_array();
	}
	function get_admin_email($id_role){
		$query ="	SELECT
							email
						FROM
							ms_admin
						WHERE 
							id_role = ?
				";
		$query = $this->db->query($query, array($id_role));
		return $query->result_array();
	}
	function approve_white($data){
		$app = $this->db->where('id',$data['id_blacklist'])->update('tr_blacklist',array('need_approve'=>0,'del'=>1,'is_white'=>0,'edit_stamp'=>$data['edit_stamp']));
		$app = $this->db->where('id',$data['id_vendor'])->update('tr_assessment_point',array('point'=>0,'category'=>0,'edit_stamp'=>$data['edit_stamp']));

		if($app){
			
			$sql = "UPDATE `ms_vendor` SET  `is_active` = 1, `need_approve`=0, `vendor_status` = 1, `dpt_first_date` = NULL, `certificate_no`= NULL WHERE id =".$data['id_vendor'];
			return $this->db->query($sql);

		}else{

			return false;

		}
	}
}