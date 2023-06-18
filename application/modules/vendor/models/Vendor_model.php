<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor_model extends CI_Model{
	function __construct(){
		parent::__construct();
		$this->field_master = array(
			'id_sbu',
			'vendor_status',
			'npwp_code',
			'vendor_code',
			'name',
			'is_active',
			'ever_blacklisted',
			'entry_stamp',
			'edit_stamp',
			'is_vms'
			);
		$this->field_admin = array(
			'id_vendor',
			'id_legal',
			'npwp_code',
			'npwp_date',
			'npwp_file',
			'nppkp_code',
			'nppkp_date',
			'nppkp_file',
			'vendor_office_status',
			'vendor_address',
			'vendor_country',
			'vendor_province',
			'vendor_city',
			'vendor_phone',
			'vendor_fax',
			'vendor_email',
			'vendor_postal',
			'vendor_website',
			'entry_stamp',
			'edit_stamp'
			);
		$this->field_pic = array(
			'id_vendor',
			'pic_name',
			'pic_position',
			'pic_phone',
			'pic_email',
			'pic_address',
			'admin_name',
			'admin_position',
			'entry_stamp'
		);
		$this->badan_hukum = array(
			'name',
			'entry_stamp'
			);
	}

	function save_data($data){
		$param = array();
		$sql = "INSERT INTO ms_vendor (
							`id_sbu`,
							`vendor_status`,
							`npwp_code`,
							`vendor_code`,
							`name`,
							`is_active`,
							`ever_blacklisted`,
							`entry_stamp`,
							`edit_stamp`,
							) 
				VALUES (?,?,?,?,?,?,?,?,?,?) ";
		
		
		foreach($this->field_master as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);
		$id = $this->db->insert_id();
		
		
			$param_admin = array();
			$sql = "INSERT INTO ms_vendor_admistrasi (
								`id_vendor`,
								`id_legal`,
								`npwp_code`,
								`npwp_date`,
								`npwp_file`,
								`nppkp_code`,
								`nppkp_date`,
								`nppkp_file`,
								`vendor_office_status`,
								`vendor_address`,
								`vendor_country`,
								`vendor_province`,
								`vendor_city`,
								`vendor_phone`,
								`vendor_fax`,
								`vendor_email`,
								`vendor_postal`,
								`vendor_website`,
								`entry_stamp`,
								`edit_stamp`) 
					VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			
			
			foreach($this->field_admin as $_param) $param_admin[$_param] = $data[$_param];
			$param_admin['id_vendor'] = $id;
			$this->db->query($sql, $param_admin);
			


			$param_pic = array();
			$sql = "INSERT INTO ms_vendor_pic (
								`id_vendor`,
								`pic_name`,
								`pic_position`,
								`pic_phone`,
								`pic_email`,
								`pic_address`,
								`entry_stamp`,
								`edit_stamp`) 
					VALUES (?,?,?,?,?,?,?,?)";
			
			
			foreach($this->field_pic as $_param) $param_pic[$_param] = $data[$_param];
			$param_pic['id_vendor'] = $id;
			$this->db->query($sql, $param_pic);
			
			$sql = "INSERT INTO ms_login (
								`id_user`,
								`username`, 
								`password`,
								`type`,
								`entry_stamp`,
								`edit_stamp`) VALUES (?,?,?,?,?,?) "; 

			$this->db->query($sql, array($id,$data['pic_email'], $data['password'], 'user', $data['entry_stamp'],$data['edit_stamp']));
		
	}

	function get_total_daftar_tunggu(){
		return $this->db->select('*')->where('vendor_status',1)->where('ms_vendor.del',0)->join('ms_vendor_admistrasi','ms_vendor.id=ms_vendor_admistrasi.id_vendor','LEFT')->get('ms_vendor')->num_rows();
	}
	
	function get_total_dpt(){
		return $this->db->select('*')->where('vendor_status',2)->where('ms_vendor.del',0)->join('ms_vendor_admistrasi','ms_vendor.id=ms_vendor_admistrasi.id_vendor','LEFT')->get('ms_vendor')->num_rows();
	}

	function get_legal(){
		$get = $this->db->select('id,name')->get('tb_legal');
		$raw = $get->result_array();
		$res = array();
		$res[''] = 'Pilih salah satu';
		foreach($raw as $key => $val){
			$res[$val['id']] = $val['name'];
		}
		return $res;
	}

	function get_sbu(){
		$get = $this->db->select('id,name')->get('tb_sbu');
		$raw = $get->result_array();
		$res = array();
		$res[''] = 'Pilih salah satu';
		foreach($raw as $key => $val){
			$res[$val['id']] = $val['name'];
		}
		return $res;
	}

	function get_data($id=0){
		if(!$id){
			$user = $this->session->userdata('user');
			$id = $user['id_user'];
		}
		
		$this->db->select('tr_dpt.start_date dpt_date, ms_vendor.certificate_no, ms_vendor.dpt_first_date as first_date, mva.id id,id_sbu,id_legal, ms_vendor.name as name, ms_vendor.npwp_code as npwp_code, npwp_file, npwp_date,nppkp_code, nppkp_file, nppkp_date, vendor_office_status, vendor_address, vendor_country, vendor_phone, vendor_province, vendor_fax, vendor_city, vendor_email, vendor_postal, vendor_website, tb_sbu.name as sbu_name, tb_legal.name as legal_name,data_status')
		->where('ms_vendor.id',$id)
		->join('ms_vendor_admistrasi as mva','mva.id_vendor=ms_vendor.id','LEFT')
		->join('tr_dpt','tr_dpt.id_vendor=ms_vendor.id','LEFT')
		->join('tb_sbu','tb_sbu.id=ms_vendor.id_sbu','LEFT')
		->join('tb_legal','tb_legal.id=mva.id_legal','LEFT')
		->order_by('tr_dpt.id', 'DESC')
		->limit('1');
		$query = $this->db->get('ms_vendor');
		return $query->row_array();
	}

	function get_vendor_name($id){
		return $this->db->where('id',$id)->get('ms_vendor')->row_array();
	}

	function edit_data($data,$id){
		// echo 'data'; print_r($id);
		$fl = array('npwp_code'=>$data['npwp_code'],'name'=>$data['name'],'edit_stamp'=>$data['edit_stamp']);
		$this->db->where('id',$id);
		$res = $this->db->update('ms_vendor',$fl);

		$fl = array();
		$field = array('id_legal','npwp_code','npwp_date','nppkp_code','nppkp_date','vendor_office_status','vendor_address','vendor_country','vendor_province','vendor_city','vendor_phone','vendor_fax','vendor_email','vendor_postal','vendor_website');
		if(isset($data['nppkp_file'])) $field[] = 'nppkp_file';
		if(isset($data['npwp_file'])) $field[] = 'npwp_file';
		foreach($field as $_param) $fl[$_param] = $data[$_param];
		$this->db->where('id_vendor',$id);
		$this->db->update('ms_vendor_admistrasi',$fl);
		
		return $id;
	}

	function get_waiting_list($status,$search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()){
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
	
		$this->db->select('ms_vendor.id as id,tb_legal.name legal_name,ms_vendor.name name, ms_vendor.edit_stamp last_update, mva.vendor_email email,ms_vendor.npwp_code,mva.vendor_address,mva.vendor_phone,ms_vendor.certificate_no,ms_vendor.need_approve,tr_dpt.start_date end_date,ms_vendor.dpt_first_date start_date')
			->join('ms_vendor_admistrasi as mva','mva.id_vendor=ms_vendor.id','LEFT')
			->join('tb_sbu','tb_sbu.id=ms_vendor.id_sbu','LEFT')
			->join('tb_legal','tb_legal.id=mva.id_legal','LEFT');

		if ($status == 1) {
		$this->db->join('tr_dpt','tr_dpt.id_vendor=ms_vendor.id','LEFT')
			->where('ms_vendor.vendor_status',1)
			->where('ms_vendor.is_active',1)
			->where('ms_vendor.del',0)
			->where('ms_vendor.need_approve',1)
			->where('(tr_dpt.end_date IS NOT NULL OR tr_dpt.end_date IS NULL)');
		}else {
		$this->db ->where('ms_vendor.vendor_status',1)
			->where('ms_vendor.is_active',1)
			->where('ms_vendor.need_approve',0);
		}
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}

		$a = $this->filter->generate_query($this->db->group_by('ms_vendor.id'),$filter);
		
		$query = $a->get('ms_vendor');
		return $query->result_array();
	}
	
	function get_dpt_list($search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()){
	$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

		$this->db->select('ms_vendor_admistrasi.vendor_address, ms_vendor_admistrasi.vendor_phone, tb_legal.name legal, ms_vendor.id as id, ms_vendor.certificate_no,ms_vendor.id as id_vendor_list ,ms_vendor.name name, tb_blacklist_limit.value category_name,ms_vendor_admistrasi.npwp_code npwp_code,ms_vendor_admistrasi.nppkp_code nppkp_code, ms_csms.score score,tb_dpt_type.name type_dpt')
		->where('ms_vendor.vendor_status',2)
		->where('ms_vendor.is_active',1)
		->order_by('point')
		->join('ms_pengurus','ms_pengurus.id_vendor=ms_vendor.id','LEFT')
		->join('ms_vendor_admistrasi','ms_vendor_admistrasi.id_vendor=ms_vendor.id','LEFT')
		->join('tb_legal','tb_legal.id=ms_vendor_admistrasi.id_legal','LEFT')
		->join('ms_situ','ms_situ.id_vendor=ms_vendor.id','LEFT')
		->join('ms_ijin_usaha','ms_ijin_usaha.id_vendor=ms_vendor.id','LEFT')
		->join('tr_assessment_point','tr_assessment_point.id_vendor=ms_vendor.id','LEFT')
		->join('tb_blacklist_limit','tb_blacklist_limit.id=tr_assessment_point.category','LEFT')
		->join('ms_pengalaman','ms_pengalaman.id_vendor=ms_vendor.id','LEFT')
		->join('ms_agen','ms_agen.id_vendor=ms_vendor.id','LEFT')
		->join('tb_dpt_type','ms_ijin_usaha.id_dpt_type=tb_dpt_type.id','LEFT')
		->join('ms_csms','ms_csms.id_vendor=ms_vendor.id','LEFT');

		$a = $this->filter->generate_query($this->db->group_by('id'),$filter);	

		if($this->input->get('sort')&&$this->input->get('by')){
			$a->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$a->limit($per_page, $per_page*($cur_page - 1));
		}

		$query = $a->get('ms_vendor');
		return $query->result_array();
	}
	
	function get_waiting_dpt_list($search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()){
	
		$this->db->select('ms_vendor.id as id,tb_legal.name legal_name,ms_vendor.name name, ms_vendor.edit_stamp last_update, mva.vendor_email email')
			->where('ms_vendor.vendor_status',1)
			->where('ms_vendor.is_active',1)
			->join('ms_vendor_admistrasi as mva','mva.id_vendor=ms_vendor.id','LEFT')
			->join('tb_sbu','tb_sbu.id=ms_vendor.id_sbu','LEFT')
			->join('tb_legal','tb_legal.id=mva.id_legal','LEFT')
			->order_by('id', 'desc');

		$query = $a->get('ms_vendor');
		print_r($query->result_array());
		return $query->result_array();
	}
	
	function to_waiting_list(){
		$user = $this->session->userdata('user');
		$this->db->where('id',$user['id_user']);
		$res = $this->db->update('ms_vendor',array(
			'vendor_status'=>1
			)
		);
		
		return $res;
	}

	function add_vendor($data){

		$param = array();

		$this->field_master = array(
			'id_sbu',
			'npwp_code',
			'name',
			'is_active',
			'ever_blacklisted',
			'entry_stamp',
			'is_vms',
			);
		$sql = "INSERT INTO ms_vendor (
							`id_sbu`,
							`npwp_code`,
							`name`,
							`is_active`,
							`ever_blacklisted`,
							`entry_stamp`,
							`is_vms`) 
				VALUES (?,?,?,?,?,?,?) ";
		
		
		foreach($this->field_master as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);
		$id = $this->db->insert_id();
		
		$this->field_admin = array(
			'id_vendor',
			'id_legal',
			'npwp_code',
			'vendor_email',	
			'entry_stamp'
			);
		$param_admin = array();
		$data['id_vendor'] = $id;
	
		$sql = "INSERT INTO ms_vendor_admistrasi (
							`id_vendor`,
							`id_legal`,
							`npwp_code`,
							`vendor_email`,
							`entry_stamp`) 
				VALUES (?,?,?,?,?)";
		
		foreach($this->field_admin as $_param) $param_admin[$_param] = $data[$_param];
		
		$this->db->query($sql, $param_admin);
		
		$sql = "INSERT INTO ms_login (
							`id_user`,
							`username`, 
							`password`,
							`type`,
							`entry_stamp`
							) VALUES (?,?,?,?,?) "; 

		$this->db->query($sql, array($id,$data['vendor_email'], $data['password'], 'user', $data['entry_stamp']));
		

		return $id;
	}

	function get_vendor_list($search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()){
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
 
		$this->db->select('*,ms_vendor.id id, ms_vendor.name name, tb_legal.name legal_name');
		$this->db->where('((ms_vendor.del != 1 AND ms_login.type = "user") or ms_vendor.is_vms = 0)',null,false);
		$this->db->join('ms_vendor_admistrasi','ms_vendor_admistrasi.id_vendor = ms_vendor.id','LEFT');
		$this->db->join('ms_login','ms_login.id_user = ms_vendor.id','LEFT');
		$this->db->join('tb_legal','tb_legal.id = ms_vendor_admistrasi.id_legal','LEFT');

		$a = $this->filter->generate_query($this->db->group_by('ms_vendor.id'),$filter);

		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}else{
			$this->db->order_by('ms_vendor.id', 'desc'); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
		$query = $a->get('ms_vendor');
		return $query->result_array();
	}
	function get_all_vendor_list(){

		$this->db->select('*,ms_vendor.id id, ms_vendor.name name, tb_legal.name legal_name');
		$this->db->where('((ms_vendor.del != 1 AND ms_login.type = "user") or ms_vendor.is_vms = 0)',null,false);
		$this->db->join('ms_vendor_admistrasi','ms_vendor_admistrasi.id_vendor = ms_vendor.id','LEFT');
		$this->db->join('ms_login','ms_login.id_user = ms_vendor.id','LEFT');
		$this->db->join('tb_legal','tb_legal.id = ms_vendor_admistrasi.id_legal','LEFT');
		

		$query = $a->get('ms_vendor');
		print_r($query->result_array());die;
		// echo $this->db->last_query();
		return $query->result_array();
	}

	public function check_pic($id){
		$this->db->select('*')->where('ms_vendor_pic.id_vendor',$id);
		
		$query = $this->db->get('ms_vendor_pic');

		return $query->num_rows();
	}

	public function get_pt($id){
		$this->db->select('tb_legal.name legal_name,ms_vendor.name name')
		->join('ms_vendor_admistrasi','ms_vendor_admistrasi.id_vendor = ms_vendor.id','LEFT')
		->join('tb_legal','tb_legal.id=ms_vendor_admistrasi.id_legal','LEFT')
		->where('ms_vendor.id',$id);
		$query = $this->db->get('ms_vendor');
		return $query->row_array();
	}

	public function save_pic($data){
		$param_pic = array();
		$sql = "INSERT INTO ms_vendor_pic (
							`id_vendor`,
							`pic_name`,
							`pic_position`,
							`pic_phone`,
							`pic_email`,
							`pic_address`,
							`admin_name`,
							`admin_position`,
							`entry_stamp`) 
				VALUES (?,?,?,?,?,?,?,?,?)";


		foreach ($this->field_pic as $_param) $param_pic[$_param] = $data[$_param];

		$result = $this->db->query($sql, $param_pic);
		return $result;
	}
	function get_data_pic($id){

		$user = $this->session->userdata('user');
		$this->db->select('*')
		->where('id_vendor',$id);
		$query = $this->db->get('ms_vendor_pic');

		return $query->row_array();
	}

	function edit_data_pic($data,$id){
		$this->db->where('id_vendor',$id);
		$res = $this->db->update('ms_vendor_pic',$data);
		return $res;
	}
	function get_data_username($id){
		$user = $this->session->userdata('user');
		$this->db->select('*')
		->where('id_user',$id);
		$this->db->where('type','user');
		$query = $this->db->get('ms_login');
		return $query->row_array();
	}
	function username_change($data,$id){
		
		$this->db->where('id_user',$id);
		$this->db->where('type','user');
		$res = $this->db->update('ms_login', $data);
		//echo $this->db->last_query();
		
		return $res;
	}

	function password_change($data,$id){
		
		$this->db->where('id_user',$id);
		$this->db->where('type','user');
		
		$res = $this->db->update('ms_login',array(
				'password'=>$data['new_password']
			));
		// echo $this->db->last_query();
		
		return $res;
	}
	function get_password($id){
		$user = $this->session->userdata('user');
		$this->db->select('password')->where('id_user',$id)->where('type','user');
		$query = $this->db->get('ms_login');
		$res = $query->row_array();
		return $res['password'];
	}



	##################################################
	##################################################
	######				BADAN HUKUM				######
	##################################################
	##################################################
	function get_badan_hukum_list($search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()){
		$this->db->select('*');
		$this->db->where('tb_legal.del',0);
		
		$a = $this->filter->generate_query($this->db->group_by('id'),$filter);

		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
		
		$query = $a->get('tb_legal');
		return $query->result_array();
    }
    function edit_badan_hukum($data,$id){
				
		$this->db->where('id',$id);
		$res = $this->db->update('tb_legal',$data);
		
		return $res;
	}
    function delete_badan_hukum($id){
		$this->db->where('id',$id);
		return $this->db->update('tb_legal',array('del'=>1));
	}
	function get_badan_hukum($id){
		$sql = "SELECT * FROM tb_legal WHERE id = ".$id;
		$query = $this->db->query($sql);
		return $query->row_array();
	}
	function save_badan_hukum($data){

		$_param = array();
		$sql = "INSERT INTO tb_legal (
							name,
							entry_stamp
							) 
				VALUES (?,?) ";
		
		
		foreach($this->badan_hukum as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);
		$id = $this->db->insert_id();
		
		return $id;
	
	}

	##################################################
	##################################################
	######				ADMINISTRASI			######
	##################################################
	##################################################
	function get_administrasi_list($id){
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

		$this->db->select('*, ms_vendor_pic.pic_address pic_address, ms_vendor.id as id ,ms_vendor.name name, mva.npwp_code npwp_code,mva.nppkp_code nppkp_code,mva.nppkp_date nppkp_date, tb_legal.name id_legal, mva.vendor_address ,ms_vendor_pic.pic_name pic_name, mva.npwp_date npwp_date,mva.vendor_office_status kantor, mva.vendor_country country, mva.vendor_province province, mva.vendor_city city, mva.vendor_phone phone, mva.vendor_fax fax, mva.vendor_email email, mva.vendor_website website, ms_akta.no no, ms_akta.notaris notaris, ms_akta.issue_date issue_date, ms_akta.authorize_by authorize_by, ms_akta.authorize_no authorize_no, ms_akta.authorize_date authorize_date, ms_pengurus.name name_pengurus, ms_pengurus.no no_ktp, ms_pengurus.position_expire exp,ms_pengurus.position pos')
		->where('mva.id_vendor',$id)
		->join('ms_vendor_admistrasi as mva','mva.id_vendor=ms_vendor.id','LEFT')
		->join('ms_vendor_pic','ms_vendor_pic.id_vendor=mva.id_vendor','LEFT')
		->join('tb_sbu','tb_sbu.id=ms_vendor.id_sbu','LEFT')
		->join('tb_legal','tb_legal.id=mva.id_legal','LEFT')
		->join('ms_akta','ms_akta.id_vendor=ms_vendor.id','LEFT')
		->join('ms_pengurus','ms_pengurus.id_vendor=ms_vendor.id','LEFT')
		->join('ms_pemilik','ms_pemilik.id_vendor=ms_vendor.id','LEFT')
		->join('ms_situ','ms_situ.id_vendor=ms_vendor.id','LEFT')
		->join('tr_dpt','tr_dpt.id_vendor=ms_vendor.id','LEFT')
		->group_by('ms_vendor.id');

		$query = $this->db->get('ms_vendor');

		return $query->result_array();
	}




	function get_dpt_list_x(){
		$query=$this->datatables->from('ms_vendor');
		return $query;
	}


	function setuju($id){
		$this->db->where('id',$id);
		$update_status = $this->db->update('ms_vendor',array('vendor_status'=>2));
		
		$dpt_list = $this->db->select('*')->where('id_vendor',$id)->where('data_status',1)->get('ms_ijin_usaha')->result_array();
		foreach($dpt_list as $key => $row){
			$this->db->where('id_vendor',$row['id_vendor']);
			$this->db->where('id_dpt_type',$row['id_dpt_type']);
			$update_status = $this->db->update('tr_dpt',array(
					'start_date'=>$_POST['start_date'],
					'status'	=>1
				)
			);
			// echo $this->db->last_query();
		}
		
	}

	function inactive_vendor($id){
		return $this->db->where('id',$id)->update('ms_vendor',array('is_active'=>0));
	}

	function get_dpt_mail($id){
		// $user = $this->session->userdata('user');
	
		$this->db->select('ms_vendor.* ,tb_legal.name legal_name, mva.vendor_email email')
		->where('ms_vendor.vendor_status',1)
		->where('ms_vendor.is_active',1)
		->where('ms_vendor.id',$id)
		->join('ms_vendor_admistrasi as mva','mva.id_vendor=ms_vendor.id','LEFT')
		->join('tb_sbu','tb_sbu.id=ms_vendor.id_sbu','LEFT')
		->join('tb_legal','tb_legal.id=mva.id_legal','LEFT')->where('vendor_status',1);

		if($this->session->userdata('admin')['id_role']==8){ 
			$this->db->where('ms_vendor.need_approve',1);
		}


		$query = $this->db->get('ms_vendor');
		// echo $this->db->last_query();
		return $query->row_array();
	}

	function get_adm_mail($id){
		// $user = $this->session->userdata('user');
	
		$this->db->select('*')
		->where('ms_admin.id_role',$id);
		// echo $this->db->last_query();
		return $this->db->get('ms_admin')->result_array();
	}

	function update_certificate($id, $nomor){
		
		$this->db->where('id',$id);
		$res = $this->db->update('ms_vendor',array(
			'certificate_no'=> $nomor
			)
		);
		// echo $this->db->last_query();
		return $res;
	}
	function change_no($id, $no){
		$data = $this->db->where('id', $id)->where('del',0)->get('tr_certificate')->row_array();

		$this->db->where('id', $id)->update('tr_certificate',array('del'=>1,'is_active'=>0));
		
		$this->db->where('id', $id)->insert('tr_certificate',array('id_vendor'=>$id, 'certificate_no'=>$no, 'dpt_date'=>$data['dpt_date'],'is_active'=>1,'entry_stamp'=>date('Y-m-d H:i:s')));

		return $this->db->where('id', $id)->update('ms_vendor',array('certificate_no'=>$no));
	}



	function get_pengurus_list($id){
		// $user = $this->session->userdata('user');
		$status=array('1', '2');
		$this->db->select('ms_pengurus.*')
		->where_in('ms_pengurus.data_status',$status);


		if ($id > 0) {
			# code...
			$this->db->where('ms_pengurus.id_vendor',$id);
		}
		$query = $this->db->get('ms_pengurus');
		// echo $this->db->last_query();
		return $query->result_array();
	}

	function get_csms_limit(){
		return $this->db->select()->get('tb_csms_limit')->result_array();
	}
}