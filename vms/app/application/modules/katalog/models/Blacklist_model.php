<?php defined('BASEPATH') OR exit('No direct script access allowed');

class blacklist_model extends CI_Model{
	public $CI;
	function __construct(){
		parent::__construct();
		$this->field_master = array(
								'id_vendor',
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

		$blacklist = $this->db->get('tb_blacklist_limit')->result_array();

		$data_vendor = $this->CI->vm->get_data($id_vendor);
		$data = array('id_vendor'=>$id_vendor, 'vendor_name'=>$data_vendor['name'],'site'=>$site);

		foreach($blacklist as $key => $row){
			// echo $poin.' '.$row['start_score'];
			if(($poin>=$row['start_score']&&$poin<=$row['end_score'])||($poin<=$row['start_score']&&$poin>=$row['end_score'])){

				// echo $poin.' '.$row['start_score'];
				$data['start_date']	= date('Y-m-d');
				$data['end_date']	= $futureDate=date('Y-m-d', strtotime('+'.$row['number_range'].' '.$row['range_time'], strtotime($data['start_date'])) );
				return $data;
			}
			
		}
		
		return false;
	}
	function search_vendor(){
		$result = array();
		$query = $this->db->select('id, name')->like('name',$this->input->post('term'),'both')->where('del',0)->get('ms_vendor')->result_array();
		foreach($query as $key => $value){
			$result[$value['id']]['id'] = $value['id'];
			$result[$value['id']]['name'] = $value['name'];
		}
		return $result;
	}
	function save_data($data){
		
		$_param = array();
		$this->db->where('id',$data['id_vendor'])->update('ms_vendor',array('ever_blacklisted'=>1));
		$sql = "INSERT INTO tr_blacklist (
							id_vendor,
							start_date,
							end_date,
							remark,
							blacklist_file,
							entry_stamp
							) 
				VALUES (?,?,?,?,?,?) ";
		
		
		foreach($this->field_master as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);

		$id = $this->db->insert_id();
		
		return $id;
	}

	function edit_data($data,$id){
				
		$this->db->where('id',$id);
		

		$result = $this->db->update('tr_blacklist',$data);
		if($result)return $id;
	}
	function delete($id){
		$this->db->where('id',$id);
		return $this->db->update('tr_blacklist',array('del'=>1));
	}
	
	function get_data($id){

		$sql = "SELECT * FROM tr_blacklist WHERE id = ".$id;
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	function get_autocomplete($keyword){
		$this->db->order_by('id', 'DESC');
        $this->db->like("name", $keyword);
        return $this->db->get('ms_vendor')->result_array();
	}

	function get_blacklist_list($id, $search='', $sort='', $page='', $per_page='',$is_page=FALSE){
    	$admin = $this->session->userdata('admin');

    	$id_vendor ="";
    	$id_vendor_query = $this->db->select('id_vendor')->group_by('id_vendor')->get('tr_blacklist')->result_array();
    	
    		foreach ($id_vendor_query as $row_id => $id_vendor_value) {
    			$id_vendor[] = $id_vendor_value['id_vendor'];
    			// echo $id_vendor.'<br>';
    		}

		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
		
		$query = $this->db->select('*')
				 ->where_in('ms_vendor.id', $id_vendor)
				 // ->where('ever_blacklisted',1)
				 ->group_by('id_vendor')
				 ->join('tr_blacklist','ms_vendor.id=tr_blacklist.id_vendor')
				 ->get('ms_vendor');

		// echo $this->db->last_query();
		return $query->result_array();
    }
}