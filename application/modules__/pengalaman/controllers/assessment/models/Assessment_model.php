<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Assessment_model extends CI_Model{
	function __construct(){
		parent::__construct();
		
	}
	function get_pengadaan_list($search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()) 
    {
    	$user = $this->session->userdata('user');
		$this->db->select('*, ms_procurement.id id, ms_procurement.name name, tb_blacklist_limit.value category, ms_vendor.id id_vendor, ms_vendor.name pemenang, "-" as pemenang_kontrak , "-" as user_kontrak , proc_date, ms_procurement.del del');
		$this->db->group_by('ms_procurement.id');
		$this->db->where('ms_procurement.del',0);
		// $this->db->where('ms_procurement_peserta.is_winner',1);
		// $this->db->where('ms_vendor.is_active',1);
		$this->db->where('ms_procurement.id_mekanisme!=',1);
		
		if($this->session->userdata('admin')['id_role']==4){
			$this->db->where('ms_procurement.status_procurement=',1);
		}
		$this->db->join('ms_contract','ms_contract.id_procurement=ms_procurement.id','LEFT');
		// $this->db->join('ms_procurement_peserta','ms_procurement_peserta.id_proc=ms_procurement.id','LEFT');
		$this->db->join('ms_vendor','ms_vendor.id=ms_contract.id_vendor');

		$this->db->join('tr_assessment','tr_assessment.id_vendor=ms_vendor.id AND tr_assessment.id_procurement = ms_procurement.id','LEFT');
		
		$this->db->join('tb_blacklist_limit','tb_blacklist_limit.id=tr_assessment.category','LEFT');
		
		$this->db->join('ms_procurement_bsb','ms_procurement_bsb.id_proc=ms_procurement.id','LEFT');
		
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
		
		$a = $this->filter->generate_query($this->db->group_by('ms_procurement.id'),$filter);

		$query = $a->get('ms_procurement');	
		// echo $this->db->last_query();	
		return $query->result_array();
		
    }

   	function get_pengadaan($id){
   		$arr = $this->db->select('*,ms_procurement.name name, tb_budget_holder.name budget_holder_name, tb_budget_spender.name budget_spender_name, tb_pejabat_pengadaan.name pejabat_pengadaan_name,tb_mekanisme.name mekanisme_name')
   		->where('ms_procurement.id',$id)
   		->join('tb_pejabat_pengadaan','tb_pejabat_pengadaan.id=ms_procurement.id_pejabat_pengadaan','LEFT')
		->join('tb_budget_holder','tb_budget_holder.id=ms_procurement.budget_holder','LEFT')
		->join('tb_budget_spender','tb_budget_spender.id=ms_procurement.budget_spender','LEFT')
		->join('tb_mekanisme','tb_mekanisme.id=ms_procurement.id_mekanisme','LEFT')
   		->get('ms_procurement')->row_array();
		
		return $arr;
   	}


   	function get_assessment_vendor_list($id,$search='', $sort='', $page='', $per_page='',$is_page=FALSE){
   		$result = $this->db->select('mpp.id id,ms_vendor.name peserta_name,
   			(SELECT point FROM tr_ass_point where id_vendor = ms_vendor.id AND id_procurement = '.$id.' ORDER BY id DESC LIMIT 0, 1) as `point` , tb_blacklist_limit.value as kategori, mpp.id_vendor id_vendor')
   		->where('mpp.id_proc',$id)
   		->where('ms_vendor.is_active',1)
   		->join('ms_vendor','ms_vendor.id=mpp.id_vendor')
   		->join('tr_assessment','tr_assessment.id_vendor=mpp.id_vendor')
   		->join('tb_blacklist_limit','tb_blacklist_limit.id=tr_assessment.category')
   		->group_by('mpp.id')
   		// ->where('tr_ass_point.id_procurement',$id)
   		/*->where('mpp.del',0)*/;
   		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
		$res = $this->db->get('ms_procurement_peserta mpp')->result_array();
		// echo $this->db->last_query();
   		return $res;
   	}
   	
   	function get_vendor_report($id){
   		$query = $this->db 	->select('ms_vendor.id id, ms_vendor.name name, ms_vendor.npwp_code npwp_code, (SELECT point FROM tr_ass_point where id_vendor = '.$id.' ORDER BY tr_ass_point.id DESC LIMIT 0, 1) point')
   							->where('id',$id)
   							->get('ms_vendor')
   							->result_array();

   		return $query;
   	}

   	function get_assessment_vendor($id,$search='', $sort='', $page='', $per_page='',$is_page=FALSE){

   		$result = $this->db->select('ms_vendor.name peserta_name,tr_ass_point.point point,ms_vendor.id id_vendor, tr_ass_point.date date')
   		->where('id_vendor',$id)
   		->join('ms_vendor','ms_vendor.id=tr_ass_point.id_vendor');
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}

   		$query = $this->db->get('tr_ass_point')->result_array();

		return $query;
   	}

   	function get_procurement_peserta($id,$search='', $sort='', $page='', $per_page='',$is_page=FALSE){

   		$result = $this->db->select('ms_procurement_peserta.id id,ms_vendor.name peserta_name,ms_procurement_peserta.id_vendor id_vendor')->where('ms_procurement_peserta.id_proc',$id)
   		->join('ms_vendor','ms_vendor.id=ms_procurement_peserta.id_vendor')
   		->where('ms_procurement_peserta.del',0);
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
   		return $this->db->get('ms_procurement_peserta')->result_array();

   	}
  


	public function get_question_assessment(){
		$result = array();
		$group_ass = $this->db->select('*')->get('ms_ass_group')->result_array();

		
		foreach($group_ass as $key => $value){
			$result[$value['id']]['name'] = $value['name'];
			$assessment = $this->db->select('*')->where('id_group',$value['id'])->get('ms_ass')->result_array();
			
			foreach($assessment as $ass){
				$result[$value['id']]['quest'][] = $ass;
			}
		}

		return $result;
	}
	public function get_assessment($id_pengadaan,$id_vendor){
		$result = array();
		$data_list = $this->db->select('id_ass, value')
		->where(
				array('id_vendor'=>$id_vendor,'id_procurement'=>$id_pengadaan)
				)
		->get('tr_ass_result')->result_array();
		foreach($data_list as $key=>$row){
			$result[$row['id_ass']] = $row['value'];
		}
		return $result;
	}
	public function check_approve($id_pengadaan, $id_vendor){
		$sql = $this->db->query("SELECT SUM(is_approve) as sum FROM tr_ass_result
				WHERE id_procurement = $id_pengadaan 
				AND id_vendor = $id_vendor")->row_array();
		return $sql['sum'];
	}
	public function get_approve($id_pengadaan,$id_vendor){
		$result = array();
		$data_list = $this->db->select('id_ass, is_approve')
		->where(
				array('id_vendor'=>$id_vendor,'id_procurement'=>$id_pengadaan)
				)
		->get('tr_ass_result')->result_array();
		foreach($data_list as $key=>$row){
			$result[$row['id_ass']] = $row['is_approve'];
		}
		return $result;
	}

	public function save_assessment($id, $post){
		$get_approve = $this->get_approve($post['id_procurement'],$post['id_vendor']);

		foreach($post['ass'] as $key=>$row){

			$this->db->delete('tr_ass_result',array(
				'id_vendor' 		=>$post['id_vendor'],
				'id_procurement' 	=>$post['id_procurement'],
				'id_ass'			=>$key,
			));

			$insert = $this->db->insert('tr_ass_result',array(
				'id_vendor' 		=>$post['id_vendor'],
				'id_procurement' 	=>$post['id_procurement'],
				'id_ass'			=>$key,
				'is_approve'		=>$get_approve[$key],
				'value'				=>$row,
				'entry_stamp'		=>date("Y-m-d H:i:s")
			));

			if(!$insert){

				return false;

			}
		}
		foreach($post['is_approve'] as $key => $row){
			$this->db->where('id_vendor',$post['id_vendor'])
			->where('id_procurement',$post['id_procurement'])
			->where('id_ass',$key)
			->update('tr_ass_result',array('is_approve'=>$row));
		}

		if($this->session->userdata('admin')['id_role']==3){
			$poin = $this->check_poin($id,$post['id_vendor']);
			$this->db->insert('tr_ass_point',array('id_vendor'=>$post['id_vendor'],'id_procurement'=>$post['id_procurement'],'date'=>$post['date'],'point'=>$poin,'entry_stamp'=>date("Y-m-d H:i:s")));
		}
		return true;
	}
	public function set_assessment($id_vendor,$poin,$kategori,$id_procurement){
		$num_rows = $this->db->where('id_vendor',$id_vendor)->where('id_procurement',$id_procurement)->get('tr_assessment')->num_rows();
		if($num_rows==0){
			$this->db->insert('tr_assessment',array(
														'id_vendor'		=>$id_vendor,
														'id_procurement'=>$id_procurement,
														'point'			=>$poin,
														'category'		=>$kategori,
														'entry_stamp'	=>date('Y-m-d H:i:s')
													));
		}else{
			$this->db->where('id_vendor',$id_vendor)->where('id_procurement',$id_procurement)->update('tr_assessment',array(
														'point'			=>$poin,
														'category'		=>$kategori,
														'edit_stamp'	=>date('Y-m-d H:i:s')
													));
		}
		$last_poin = $this->db->where('id_vendor',$id_vendor)->get('tr_assessment_point')->num_rows();
		if($last_poin==0){
			$this->db->insert('tr_assessment_point',array(
														'id_vendor'		=>$id_vendor,
														'point'			=>$poin,
														'category'		=>$kategori,
														'entry_stamp'	=>date('Y-m-d H:i:s')
													));
		}else{
			$this->db->where('id_vendor',$id_vendor)->update('tr_assessment_point',array(
														'point'			=>$poin,
														'category'		=>$kategori,
														'edit_stamp'	=>date('Y-m-d H:i:s')
													));
		}
	}
	public function check_poin($id,$id_vendor){
		$query = $this->db->query('SELECT SUM(value) as num FROM tr_ass_result WHERE id_procurement = '.$id.' AND id_vendor = '.$id_vendor)->row_array();

		return $query['num'];
	}

	function get_mail($id){
		$query = $this->db->select('id_role, name, email')
							->where('id_role', $id)
							->get('ms_admin');
		$res   =  $query->result_array();

		return $res;
	}
	function insert_bast($id, $id_vendor){
		$bast = $this->get_data_bast($id, $id_vendor)->row_array();
		if(empty($bast)){
			return $this->db->insert('tr_bast',
					array(
						'id_vendor'=>$id_vendor,
						'id_procurement'=>$id,
						'value'=>$this->input->post('text'),
						'entry_stamp'=>date('Y-m-d H:i:s')
					)
				);
		}else{
			return $this->db 	->where('id_procurement',$id)
						->where('id_vendor',$id_vendor)
						->update('tr_bast',
									array(
										'value'=>$this->input->post('text'),
										'edit_stamp'=>date('Y-m-d H:i:s')
										)
								);
		}
		
	}
	function get_data_bast($id_procurement, $id_vendor){
		$query = "	SELECT 
						*
					FROM
						tr_bast
					WHERE 
						id_procurement = ?
						AND id_vendor = ?
				";
		$query = $this->db->query($query, array($id_procurement, $id_vendor));
		return $query;
	}

	function get_default_template(){
		$query = "	SELECT 
						*
					FROM
						tb_bast_print
					ORDER BY id DESC
					LIMIT 0,1
				";
		$query = $this->db->query($query);

		return $query;
	}

	function get_bast_fill($id){
		$query =	"	SELECT
							a.name pekerjaan,
							b.contract_price besaran,
							b.contract_price_kurs besaran_kurs,
							b.no_contract no_kontrak,
							b.contract_date tanggal_kontrak,
							b.contract_date tanggal_kontrak,
							c.symbol,
							d.name penyedia,
							e.vendor_address alamat_penyedia,
							f.name legal_name
						FROM
							ms_procurement a

						LEFT JOIN ms_contract b ON b.id_procurement = a.id
						LEFT JOIN tb_kurs c ON b.contract_kurs = c.id						
						LEFT JOIN ms_vendor d ON d.id = b.id_vendor
						LEFT JOIN ms_vendor_admistrasi e ON d.id = e.id_vendor
						LEFT JOIN tb_legal f ON f.id = e.id_legal

						WHERE a.id = ?
					";
		$query = $this->db->query($query,array($id));
		
		return $query;
	}
}