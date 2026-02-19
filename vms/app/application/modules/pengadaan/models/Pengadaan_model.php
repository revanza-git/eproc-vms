<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pengadaan_model extends CI_Model{
	public $perencanaan_db;
	function __construct(){
		parent::__construct();
		$this->perencanaan_db = $this->load->database('perencanaan',true);
		$this->field_master = array(
								'name',
								'budget_source',
								'id_pejabat_pengadaan',
								'budget_year',
								'budget_holder',
								'budget_spender',
								'id_mekanisme',
								'entry_stamp',
                                'evaluation_method',
                                'evaluation_method_desc',
								'idr_value',
								'kurs_value',
								'id_kurs',
								'tipe_pengadaan'
							);
		$this->field_contract = array(
							'id_procurement',
							'id_vendor',
							'no_sppbj',
							'sppbj_date',
							'no_spmk',
							'spmk_date',
							'start_work',
							'end_work',
							'no_contract',
							'contract_date',
							'po_file',
							'contract_price',
							'contract_price_kurs',
							'contract_kurs',
							'start_contract',
							'end_contract',
							'entry_stamp'
							);

		$this->bsb = array('id_proc',
							'id_bidang',
							'id_sub_bidang',
							'entry_stamp');

		$this->barang = array(
							'id_procurement',
							'is_catalogue',
							'id_material',
							'nama_barang',
							'id_kurs',
							'nilai_hps',
							'entry_stamp',
							'category');
		
		$this->barang_k = array(
							'id_barang',
							'nama',
							'category',
							'for',
							'entry_stamp',
							'id_kurs');

		$this->barang_h = array(
							'id_material',
							'id_procurement',
							'price',
							'entry_stamp',
							'date');

		$this->peserta = array('id_proc',
							'id_vendor',
							'surat',
							'id_surat',
							// 'id_kurs',
							// 'kurs_value',
							// 'idr_value',
							'entry_stamp');

		$this->progress = array('id_contract',
							'step_name',
							'supposed',
							'realization',
							'entry_stamp');
	}

	public function import_pengadaan()
	{
		$admin = $this->session->userdata('admin');

		$get_pengadaan = $this->perencanaan_db->where('del',0)->where('id_division',$admin['id_division'])->get('ms_fppbj')->result_array();


		// print_r($get_pengadaan);die;
		foreach ($get_pengadaan as $key => $value) {
			if ($value['metode_pengadaan'] == 1) {
				$id_mekanisme = 5; //Pelelangan
			} else if ($value['metode_pengadaan'] == 2) {
				$id_mekanisme = 2; //Pemilihan Langsung
			} else if ($value['metode_pengadaan'] == 3) {
				$id_mekanisme = 1; //Swakelola
			} else if ($value['metode_pengadaan'] == 4) {
				$id_mekanisme = 3; //Penunjukan Langsung
			} else if ($value['metode_pengadaan'] == 5) {
				$id_mekanisme = 6; //Pengadaan Langsung
			}
			$arr = array(
				'name'			=>	$value['nama_pengadaan'],
				'budget_year'	=>	$value['year_anggaran'],
				'id_division'	=>	$value['id_division'],
				'tipe_pengadaan'=>	$value['tipe_pengadaan'],
				'id_fppbj'		=>	$value['id'],
				'idr_value'		=>	$value['idr_anggaran'],
				'id_mekanisme'	=>	$id_mekanisme,
				'entry_stamp'	=>	date('Y-m-d H:i:s')
			);
			
			$cek = $this->db->where('del',0)->where('id_fppbj',$value['id'])->where('id_division',$admin['id_division'])->get('ms_procurement')->result_array();
			if (count($cek) == 0) {
				$this->db->insert('ms_procurement',$arr);
			}
		}

		return true;
	}

	function get_pengadaan_list($search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()) 
    {
    	$admin = $this->session->userdata('admin');
    	// print_r($admin);die;
    	$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

		$this->db->select('*, ms_procurement.id id, ms_procurement.name name, 
		ms_vendor.name as pemenang
		, proc_date, ms_procurement.del del, tb_mekanisme.name mekanisme_name, ms_procurement.idr_value nilai, ms_contract.contract_price');
		$this->db->group_by('ms_procurement.id');
		$this->db->where('ms_procurement.del',0);
		$this->db->where('ms_procurement.id_mekanisme!=',1);
		if($this->session->userdata('admin')['id_role']==4){
            $this->db->where('ms_procurement.status_procurement=',1);
		}
		if($this->session->userdata('admin')['id_role'] == 9 ){
            $this->db->where('ms_procurement.id_division=',$admin['id_division']);
		}
		// if($admin['id_role']==9){
		// 	$division = $this->get_division($admin['id_user']);
		// 	// $this->db->where('budget_spender',$division['id_division']);
		// 	$this->db->where('id_division',$admin['id_division']);
		// }
		
		$this->db->join('ms_procurement_bsb','ms_procurement_bsb.id_proc=ms_procurement.id','LEFT');
		$this->db->join('tb_mekanisme','tb_mekanisme.id=ms_procurement.id_mekanisme','LEFT');
		$this->db->join('ms_contract','ms_contract.id_procurement=ms_procurement.id','LEFT');
		$this->db->join('ms_vendor','ms_contract.id_vendor=ms_vendor.id','LEFT');
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}else{

			$this->db->order_by('ms_procurement.id','DESC');
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

    public function get_pengadaan_division($search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array())
    {
    	$admin = $this->session->userdata('admin');
    	$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

		$this->db->select('ms_procurement.*, ms_procurement.id id');
		$this->db->group_by('ms_procurement.id');
		$this->db->where('ms_procurement.del',0);
		// if($this->session->userdata('admin')['id_role']==4){
  //           $this->db->where('ms_procurement.status_procurement=',1);
		// }
		if($this->session->userdata('admin')['id_role']!=3){
            $this->db->where('ms_procurement.id_division=',$admin['id_division']);
		}
		// if($admin['id_role']==9){
		// 	$division = $this->get_division($admin['id_user']);
		// 	$this->db->where('budget_spender',$division['id_division']);
		// }

		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}else{

			$this->db->order_by('ms_procurement.id','DESC');
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

	function get_division($id){
    	$query = 	"	SELECT 
    						id_division
    					FROM
    						ms_admin
    					WHERE id = ?
    				";
    	$query = $this->db->query($query, array($id));
    	// echo $this->db->last_query();
    	return $query->row_array();
    }
    function get_bidang_list($id){
		$return = array();
		$result = $this->db->select('id, name')->where('id_dpt_type', $id)->get('tb_bidang')->result_array();
		$return[''] =  '--Pilih Data--';
		foreach($result as $key => $value){
			$return[$value['id']] = $value['name'];
		}
		return $return;
	}
   	function save_data($data){
   		$_param = array();
		$sql = "INSERT INTO ms_procurement (
								`name`,
								`budget_source`,
								`id_pejabat_pengadaan`,
								`budget_year`,
								`budget_holder`,
								`budget_spender`,
								`id_mekanisme`,
								`entry_stamp`,
								`evaluation_method`,
								`evaluation_method_desc`,
								`idr_value`,
								`kurs_value`,
								`id_kurs`,
								`tipe_pengadaan`
								) 
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
                //`no_bahp`,`bahp_date`,`price_nego`,`nego_kurs`,`price_nego_kurs`,

		foreach($this->field_master as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);
		$id = $this->db->insert_id();
		
		return $id;
   	}

   	function edit_data($data,$id){
		$param = array();
		
		$this->db->where('id',$id);
		$res = $this->db->update('ms_procurement',$data);

		
		return $res;
	}

   	function save_kontrak($data){
   		// print_r($data);die;
   		$_param = array();
   		// print_r($data);
		$sql = "INSERT INTO ms_contract (
								`id_procurement`,
								`id_vendor`,
								`no_sppbj`,
								`sppbj_date`,
								`no_spmk`,
								`spmk_date`,
								`start_work`,
								`end_work`,
								`no_contract`,
								`contract_date`,
								`po_file`,
								`contract_price`,
								`contract_price_kurs`,
								`contract_kurs`,
								`start_contract`,
								`end_contract`,
								`entry_stamp`
								) 
				VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";

		foreach($this->field_contract as $_param) $param[$_param] = $data[$_param];
		
		$res = $this->db->query($sql, $param);
		$insert_id = $this->db->insert_id();
		$this->db->where('id',$data['id_procurement'])->update('ms_procurement',array('status_procurement'=>2));
		$this->db->insert('tr_progress_kontrak',
												array(
													'id_procurement' 		=> $data['id_procurement'],
													'id_contract'	 		=> $insert_id,
													'step_name'		 		=> 'Jangka Waktu Pelaksanaan Pekerjaan',
													'start_date'			=> $data['start_work'],
													'end_date'				=> $data['end_work'],
													'supposed'				=> ceil((abs(strtotime($data['end_work'])-strtotime($data['start_work']))+1)/86400),
													'type'					=> 2
												)
											);

		$this->db->insert('tr_progress_kontrak',
												array(
													'id_procurement' 		=> $data['id_procurement'],
													'id_contract'	 		=> $insert_id,
													'step_name'		 		=> 'Jangka Waktu Kontrak',
													'start_date'			=> $data['start_contract'],
													'end_date'				=> $data['end_contract'],
													'supposed'				=> ceil((abs(strtotime($data['end_work'])-strtotime($data['end_contract']))+1)/86400),
													'type'					=> 1
												)
											);

		/*$this->db->insert('tr_progress_kontrak',
												array(
													'id_procurement' 		=> $data['id_procurement'],
													'id_contract'	 		=> $insert_id,
													'step_name'		 		=> 'Jangka Waktu Kontrak',
													'start_date'			=> $data['start_contract'],
													'end_date'				=> $data['end_contract'],
													'supposed'				=> ceil((abs(strtotime($data['end_work'])-strtotime($data['end_contract']))+1)/86400),
													'type'					=> 1
												)
											);*/
		
		
		return $res;
   	}

   	function edit_kontrak($data,$id){

		
		$this->db->where('id_procurement',$id);
		$res = $this->db->update('ms_contract',$data);

		$data_kontrak = $this->db->where('id_procurement',$id)->get('ms_contract')->row_array();

		$type1 = $this->db->where('type',1)->where('id_procurement',$id)->get('tr_progress_kontrak')->num_rows();
		$type2 = $this->db->where('type',2)->where('id_procurement',$id)->get('tr_progress_kontrak')->num_rows();
		if($type2 > 0){
			$this->db->where('type',2)->where('id_procurement',$id)
										->update('tr_progress_kontrak',
												array(
													'id_procurement' 		=> $data['id_procurement'],
													'start_date'			=> $data['start_work'],
													'end_date'				=> $data['end_work'],
													'supposed'				=> ceil((abs(strtotime($data['end_work'])-strtotime($data['start_work']))+1)/86400)
												)
											);
		}else{
			$this->db->insert('tr_progress_kontrak',
												array(
													'id_procurement' 		=> $data['id_procurement'],
													'id_contract'	 		=> $data_kontrak['id'],
													'step_name'		 		=> 'Jangka Waktu Pelaksanaan Pekerjaan',
													'start_date'			=> $data['start_work'],
													'end_date'				=> $data['end_work'],
													'supposed'				=> ceil((abs(strtotime($data['end_work'])-strtotime($data['start_work']))+1)/86400),
													'type'					=> 2
												)
											);
		}
		if($type1 > 0){
			$this->db->where('type',1)->where('id_procurement',$id)
										->update('tr_progress_kontrak',
												array(
													'id_procurement' 		=> $data['id_procurement'],
													'start_date'			=> $data['start_contract'],
													'end_date'				=> $data['end_contract'],
													'supposed'				=> ceil((abs(strtotime($data['end_work'])-strtotime($data['end_contract']))+1)/86400)
												)
											);
		}else{
			$this->db->insert('tr_progress_kontrak',
												array(
													'id_procurement' 		=> $data['id_procurement'],
													'id_contract'	 		=> $data_kontrak['id'],
													'step_name'		 		=> 'Jangka Waktu Kontrak',
													'start_date'			=> $data['start_contract'],
													'end_date'				=> $data['end_contract'],
													'supposed'				=> ceil((abs(strtotime($data['end_work'])-strtotime($data['end_contract']))+1)/86400),
													'type'					=> 1
												)
											);
		}

		return $res;
	}

   	function get_pengadaan($id){
   		$arr = $this->db->select('*,ms_procurement.name name, tb_budget_holder.name budget_holder_name, tb_budget_spender.name budget_spender_name, tb_pejabat_pengadaan.name pejabat_pengadaan_name,tb_mekanisme.name mekanisme_name,evaluation_method, evaluation_method_desc, symbol kurs_symbol,idr_value,kurs_value')
   		->where('ms_procurement.id',$id)
   		->join('tb_pejabat_pengadaan','tb_pejabat_pengadaan.id=ms_procurement.id_pejabat_pengadaan','LEFT')
		->join('tb_budget_holder','tb_budget_holder.id=ms_procurement.budget_holder','LEFT')
		->join('tb_budget_spender','tb_budget_spender.id=ms_procurement.budget_spender','LEFT')
		->join('tb_mekanisme','tb_mekanisme.id=ms_procurement.id_mekanisme','LEFT')
		->join('tb_kurs','tb_kurs.id=ms_procurement.id_kurs','LEFT')
   		->get('ms_procurement')->row_array();
		// print_r($arr);die;
		return $arr;
   	}

   	function get_progress_pengadaan($id){
   		$query = 	$this->db
   					->select('tr_progress_pengadaan.*')
   					->where('tr_progress_pengadaan.value',1)
   					->where('id_proc',$id)
   					->where('tr_progress_pengadaan.date IS NOT NULL')
   					->where('tb_progress_pengadaan.del',0)
   					->where('tr_progress_pengadaan.value',1)
   					->join('tb_progress_pengadaan','tr_progress_pengadaan.id_progress=tb_progress_pengadaan.id','LEFT')
   					->order_by('tb_progress_pengadaan.no', 'ASC')
   					->get('tr_progress_pengadaan')
   					->result_array();

   		$return = array();
   		foreach ($query as $key => $value) {
   			$return[$value['id_progress']] = $value;
   		}
   		return $return;
   	}

   	function get_paket_progress($id){
   		$result = $this->db
   					->where('id_proc',$id)
   					->where('tr_progress_pengadaan.value',1)
   					->where('tr_progress_pengadaan.date IS NOT NULL')
   					->where('tb_progress_pengadaan.del',0)
   					->where('tr_progress_pengadaan.value',1)
   					->join('tb_progress_pengadaan','tb_progress_pengadaan.id=tr_progress_pengadaan.id_progress')
   					->order_by('tb_progress_pengadaan.no', 'ASC')
   					->get('tr_progress_pengadaan')
   					->result_array();

		$total_day = 0;
		foreach ($result as $key => $value) {

			$date_now = $result[$key]['date'];
			$date_next = ( isset($result[$key+1]['date'] ) ? $result[$key+1]['date'] : $date_now);
			$day_range = max((int)get_range_date($date_next,$date_now),0);
			$result[$key]['day_range'] = $day_range;
			
			$total_day += $day_range;

		}

		
		$data['total_day'] = $total_day;

		if($total_day > 0){
			foreach ($result as $key => $value) {
				$result[$key]['percent'] = floor($value['day_range'] / $total_day * 100);
			}
		}else{
			$item_count = count($result);
			if($item_count > 0){
				$base_percent = floor(100 / $item_count);
				$remainder = 100 - ($base_percent * $item_count);
				foreach ($result as $key => $value) {
					$result[$key]['percent'] = $base_percent;
				}
				$last_index = array_key_last($result);
				$result[$last_index]['percent'] += $remainder;
			}
		}
		return $result;
   	}

   	function save_progress_pengadaan($id){
		#print_r($this->input->post());die;
   		foreach($this->input->post('progress') as $key => $value){
   			$check = $this->db->where('id_proc',$id)->where('id_progress',$key)->get('tr_progress_pengadaan')->num_rows();
   			if($check>0){
   				if($this->input->post('file')[$key] != ''){
					$res = $this->db->where('id_proc',$id)
									->where('id_progress',$key)
									->update('tr_progress_pengadaan',array('value'=>$value, 'date'=>$this->input->post('date')[$key], 'file'=>$this->input->post('file')[$key]));
				} else{
					$res = $this->db->where('id_proc',$id)
									->where('id_progress',$key)
									->update('tr_progress_pengadaan',array('value'=>$value, 'date'=>$this->input->post('date')[$key]));
				}
   				if(!$res) return false;
   			}else{
   				$res = $this->db->insert('tr_progress_pengadaan',array(
																	'value'=>$value,
																	'id_proc'=>$id,
																	'id_progress'=>$key,
																	'date'=>$this->input->post('date')[$key],
																	'file'=>$this->input->post('file')[$key],
																));
   				if(!$res) return false;
   			}
   		}
   		return true;
   	}

   	function save_remark($id){
   		$res = $this->db->where('id',$id)->update('ms_procurement',array('remark'=>$this->input->post('remark')));
   		return $res;
   	}

   	function get_pengadaan_step(){
   		$query 		= $this->db
							->select('tb_progress_pengadaan.*, tr_progress_pengadaan.file')
							->where('del',0)
							->order_by('tb_progress_pengadaan.no', 'ASC')
							->join('tr_progress_pengadaan', 'tr_progress_pengadaan.id_progress = tb_progress_pengadaan.id', 'LEFT')
							->get('tb_progress_pengadaan')
							->result_array();
							
   		$result = array();
   		foreach($query as $value){
   			$result[$value['id']]['value'] 		= $value['value'];
   			$result[$value['id']]['lampiran'] 	= $value['lampiran'];
   			$result[$value['id']]['file'] 		= $value['file'];
   		}
   		return $result;
   	}

   
   	

   	function save_denda($id,$data){
   		$res = $this->db->where('id_procurement',$id)->get('tr_denda')->num_rows();
   		if($res==0){
	   		return $this->db->insert('tr_denda',array(
	   							'id_procurement'=>$id,
	   							'start_date'=>$data['start_date'],
	   							'denda'=>$data['denda'],
	   							'end_date'=>$data['end_date'],
	   							'entry_stamp'=>$data['entry_stamp']
	   						));
	   	}else{
	   		return $this->db->where('id_procurement',$id)->update('tr_denda',array(
	   							'start_date'=>$data['start_date'],
	   							'end_date'=>$data['end_date'],
	   							'denda'=>$data['denda'],
	   							'edit_stamp'=>$data['edit_stamp']
	   						));
	   	}
   	}


   	function get_date_range($id){
   		$this->db
   				->select('max(DATE(`end_date`)) as end_date, min(DATE(`start_date`)) as start_date')
   				->where('id_procurement',$id);
   		
   		$res =  $this->db->get('tr_progress_kontrak')->row_array();

   		return $res;
   	}

   	

   	function get_date_range_for_denda($id){
   		$this->db
   				->select('max(DATE(`end_date`)) as end_date, min(DATE(`start_date`)) as start_date')
   				->where('id_procurement',$id)
   				->where('type!=',1);
   		
   		$res =  $this->db->get('tr_progress_kontrak')->row_array();

   		return $res;
   	}
   	function stop_pengadaan($id){
   		$date = date('Y-m-d');
   		if(strtotime($date) > strtotime($this->get_kontrak_day($id)['end_date'])) {
   			$date = $this->get_kontrak_day($id)['end_date'];
   			if(strtotime($date) < strtotime($this->get_amandemen_range($id)['end_date'])) $date = $this->get_amandemen_range($id)['end_date'];
   			if(strtotime($date) < strtotime($this->get_denda($id)['end_date'])) $date = $this->get_denda($id)['end_date'];
   		}

   		$result = $this	->db
   						->where('id_procurement',$id)
   						->update('ms_contract',array('end_actual'=>$date));
   		return $result;
   	}
	function get_sppbj_date($id_proc){
		return $this->db->select('date')->where('id_proc', $id_proc)->where('id_progress', 11)->get('tr_progress_pengadaan')->row_array()['date'];
	}
	
   	function get_kontrak($id){
   		$arr = $this->db->select('ms_contract.*,ms_contract.id id,tb_legal.name legal_name, ms_vendor.name vendor_name, tb_kurs.symbol kurs_name')
		->join('ms_vendor','ms_vendor.id=ms_contract.id_vendor')
   		->join('ms_vendor_admistrasi','ms_vendor.id=ms_vendor_admistrasi.id_vendor','LEFT')
   		->join('tb_legal','ms_vendor_admistrasi.id_legal=tb_legal.id','LEFT')
   		->join('tb_kurs','ms_contract.contract_kurs=tb_kurs.id','LEFT')
   		->where('id_procurement',$id)
   		->where('ms_contract.del',0)
   		->get('ms_contract')->row_array();
   		// echo $this->db->last_query();die;
		return $arr;
   	}

   	function get_contract_progress($id){
   		$arr = $this->db->select('id,step_name,start_date,end_date,supposed,type')->where('id_contract',$id)->where('del',0)->get('tr_progress_kontrak')->result_array();
   		return $arr;
   	}

   	function get_amandemen_total($id){
   		return $this->db->where('type',3)->where('id_procurement',$id)->get('tr_progress_kontrak')->num_rows();
   	}
   	function get_amandemen_day($id){
   		return $this->db->where('type',3)->where('id_procurement',$id)->get('tr_progress_kontrak')->result_array();
   	}
   	function get_work_day($id){
   		return $this->db->where('type',2)->where('id_procurement',$id)->get('tr_progress_kontrak')->row_array();
   	}
   	function get_kontrak_day($id){
   		return $this->db->where('type',1)->where('id_procurement',$id)->get('tr_progress_kontrak')->row_array();
   	}
   	function get_denda($id){
   		return $this->db->where('id_procurement',$id)->get('tr_denda')->row_array();
   	}
	function get_amandemen_range($id){
   		$this->db
   				->select('max(DATE(`end_date`)) as end_date, min(DATE(`start_date`)) as start_date')
   				->where('type',3)
   				->where('id_procurement',$id);
   		
   		$res =  $this->db->get('tr_progress_kontrak');
   		if($res->num_rows()>0){
	   		return $res->row_array();
	   	}
	   	return false;
   	}
	function get_graph($id,$id_procurement){

   		$data = $this->get_contract_progress($id);

   		$data_kontrak 		= $this->get_kontrak($id_procurement);
   		$amandemen_range 	= $this->get_amandemen_range($id_procurement);
   		$result = array();
   		$total_supposed = 0;

   		/* Work Day */
   		$work_date = $this->get_work_day($id_procurement);
   		if(!empty($work_date)){
   			$date 						= get_range_date($work_date['end_date'],$work_date['start_date']);
	   		$result['data'][]	= array(
   												'type'	=> $work_date['type'],
   												'value' => $work_date['supposed'],
   												'range' => $date,
   												'label' => $work_date['step_name'].'( '.$date.' Hari ) '.default_date($work_date['start_date']).' - '.default_date($work_date['end_date']),
   												'step'	=> $work_date['step_name']
   												);
	   			$total_supposed+= $work_date['supposed'];
	   			
	   	}

	   	$kontrak_date = $this->get_kontrak_day($id_procurement);
	   	/*Kontrak Data*/
	   	$date_kontrak = get_range_date($kontrak_date['end_date'],$kontrak_date['start_date']);
	   	$label_kontrak .= '<br>'.$kontrak_date['step_name'].'( '.$date_kontrak.' Hari ) '.default_date($kontrak_date['start_date']).' - '.default_date($kontrak_date['end_date']).'<br>';

	   	$amandemen = $this->get_amandemen_day($id_procurement);

	   	foreach ($amandemen as $key => $value) {
	   	
	   		$date 						= get_range_date($value['end_date'],$value['start_date']);
	   		$date_supposed				= $kontrak_date['supposed'];
	   		$kontrak_date['supposed'] 	-= $date;


	   		if(strtotime($value['start_date']) > strtotime($kontrak_date['end_date'])){

   				$result['data'][]	=	array(
   												'type'  => $value['type'],
   												'value' => $date,
   												'range' => $date,
   												'label' => $amandemen[$key]['step_name'].'( '.$date.' Hari ) '.default_date($amandemen[$key]['start_date']).' - '.default_date($amandemen[$key]['end_date']),
   												'step'	=> $amandemen[$key]['step_name'],
   												);

   				$total_supposed+= $date;

	   		}else{
	   			if(strtotime($value['end_date']) <= strtotime($kontrak_date['end_date'])){
	   				
		   			$result['data'][]	=	array(
	   												'type'  => $value['type'],
	   												'value' => $date,
	   												'range' => $date,
	   												'label' => $amandemen[$key]['step_name'].'( '.$date.' Hari ) '.default_date($amandemen[$key]['start_date']).' - '.default_date($amandemen[$key]['end_date']).'<br>'.$label_kontrak,
	   												'step'	=> $amandemen[$key]['step_name'],
	   												'gap'	=> true,
	   												);
	   				$total_supposed+= $date;

	   			}else{
	   				$date_gap			=	get_range_date($kontrak_date['end_date'],$value['start_date']);
	   				$date_remain		=	get_range_date($value['end_date'],$kontrak_date['end_date']);
	   				
	   				$result['data'][]	=	array(
	   												'type'  => $value['type'],
	   												'value' => $date_gap,
	   												'range' => $date,
	   												'label' => $amandemen[$key]['step_name'].'( '.$date.' Hari ) '.default_date($amandemen[$key]['start_date']).' - '.default_date($amandemen[$key]['end_date']).'<br>'.$label_kontrak,
	   												'step'	=> $amandemen[$key]['step_name'],
	   												'gap'	=> true,
	   											);
	   				$total_supposed+= $date_gap;
	   				$result['data'][]	=	array(
	   												'type'  => $value['type'],
	   												'value' => $date_remain,
	   												'range' => $date,
	   												'label' => $amandemen[$key]['step_name'].'( '.$date.' Hari ) '.default_date($amandemen[$key]['start_date']).' - '.default_date($amandemen[$key]['end_date']),
	   												'step'	=> $amandemen[$key]['step_name']
	   											);

	   				$total_supposed+= $date_remain;

	   			}
	   		}


	   	}

	   	/*Denda*/
	   	$denda = $this->pm->get_denda($id_procurement);
	   	if(!empty($denda)&&count($data)>0){

   			$denda_day 					= $this->get_denda_day($id_procurement);
   			$date 						= get_range_date($denda['end_date'],$denda['start_date']);
   			$kontrak_date['supposed']	-= $date;

   			if(strtotime($denda['start_date']) > strtotime($kontrak_date['end_date'])){
				$result['data'][] 			= 	array(
														'type'	=> 5,
														'value' => $denda_day,
														'range' => $date,
														'label' => 'Denda ( '.$date.' Hari ) '.default_date($denda['start_date']).' - '.default_date($denda['end_date']).' '.$data['denda_price']
													);
				$total_supposed+= $date;
			}else{
				if(strtotime($denda['end_date']) <= strtotime($kontrak_date['end_date'])){
					// echo $denda_day['end_date'].' '.$kontrak_date['end_date'];
		   			$result['data'][] 			= 	array(
														'type'	=> 5,
														'value' => $denda_day,
														'range' => $date,
														'label' => $label_kontrak.'<br>'.'Denda ( '.$date.' Hari ) '.default_date($denda['start_date']).' - '.default_date($denda['end_date']).' '.$data['denda_price'],
														'gap'	=> true
													);
	   				$total_supposed+= $date;
	   			}
	   			else{
	   				$date_gap		=	get_range_date($kontrak_date['end_date'],$denda['start_date']);
	   				$date_remain		=	get_range_date($denda['end_date'],$kontrak_date['end_date']);
					
	   				$result['data'][]	=	array(
	   												'type'	=> 5,
	   												'value' => $date_gap,
	   												'range' => $date,
	   												'label' => $label_kontrak.'<br>'.'Denda ( '.$date.' Hari ) '.default_date($denda['start_date']).' - '.default_date($denda['end_date']).' '.$data['denda_price'],
	   												'gap'	=> true,
	   											);
	   				$total_supposed+= $date_gap;
	   				$result['data'][]	=	array(
	   												'type'	=> 5,
	   												'value' => $date_remain,
	   												'range' => $date,
	   												'label' => 'Denda ( '.$date.' Hari ) '.default_date($denda['start_date']).' - '.default_date($denda['end_date']).' '.$data['denda_price'],
	   												
	   											);

	   				$total_supposed+= $date_remain;

	   			}
			}
	   		
	   	}


	   	$kontrak_date['supposed'] = max($kontrak_date['supposed'],0);

   		if(!empty($kontrak_date)){
   			$date 						= get_range_date($kontrak_date['end_date'],$kontrak_date['start_date']);
	   		$result['data'][]	= array(
   												'type'	=> $kontrak_date['type'],
   												'value' => $kontrak_date['supposed'],
   												'range' => $date,
   												'label' => $kontrak_date['step_name'].'( '.$date.' Hari ) '.default_date($kontrak_date['start_date']).' - '.default_date($kontrak_date['end_date']),
   												'step'	=> $kontrak_date['step_name']
   												);
	   		
	   		$total_supposed += $kontrak_date['supposed'];

	   	}

   		
	   	$result['total_day'] = $total_supposed;
   		return $result;
   	}


   	function get_realization($id){
   		$data_kontrak = $this->get_kontrak($id);
   		$date_range = $this->get_date_range($id);
   		$result = array();

   		if(is_array($date_range) && (isset($date_range['end_date'])||isset($date_range['start_date']))){

	   		$max_date	= (!empty($date_range['end_date'])) ? $date_range['end_date'] : date('Y-m-d');
	   		
			$day_total 	= get_range_date($max_date,isset($date_range['start_date']) ? $date_range['start_date'] : '');
			$day_total 	+= $this->get_denda_day($id);
			$day_total = max((int)$day_total,0);
			$date = (strtotime(date('Y-m-d'))>strtotime($max_date)) ? $max_date : date('Y-m-d');//Jika tanggal sekarang lebih dari tanggal terbesar, jadikan tanggal terbesar menjadi realization date
			$date	= (!empty($data_kontrak) && !empty($data_kontrak['end_actual'])) ? $data_kontrak['end_actual'] : $date;
			
			$day_now = get_range_date($date,isset($date_range['start_date']) ? $date_range['start_date'] : '');
			$result['range'] = ($day_total>0) ? ($day_now / $day_total * 100) : 0;
			if($result['range']>100) $result['range'] = 100;
			$range_step = 	array(
								'Jangka Waktu Kontrak'						=>	$this->get_kontrak_day($id),
								'Jangka Waktu Pelaksanaan Pekerjaan'		=>	$this->get_work_day($id),
							);

			foreach ($this->get_amandemen_day($id) as $key => $value) {
				$range_step[$value['step_name']] = $value;
			}

			$range_step['Denda']	=	$this->get_denda($id);

			$step = '';
			foreach($range_step as $key => $value){
				if(!is_array($value) || empty($value['start_date']) || empty($value['end_date']))
					continue;
				if(strtotime($date) >= strtotime($value['start_date']) && strtotime($date) <= strtotime($value['end_date']))
					$step = $key;

			}
			$result['step'] = $step;
			$result['date'] = $date;
			$result['now'] = $day_now;
		}
		
		return $result;
   	}

   	function get_progress($id){
   		$data_kontrak = $this->pm->get_kontrak($id);

   		
		$data['color'] = array('#A2A2A2','#CACACA','#DCDCDC');
		$data['color_amandemen'] = array('#061539','#162955','#2E4172','#4F628E','#7887AB');
		$data['contract'] = $this->pm->get_contract_progress($data_kontrak['id']);

		$data['denda_price'] = ($this->pm->get_denda_price($id)>0) ? 'Denda : Rp. '.$this->pm->get_denda_price($id) :''; 
		$date_range = $this->pm->get_date_range($id);
		$date = (strtotime(date('Y-m-d'))>strtotime($date_range['end_date'])) ? $date_range['end_date'] : date('Y-m-d');//Jika tanggal sekarang lebih dari tanggal terbesar, jadikan tanggal terbesar menjadi realization date
		// $date = (isset($data_kontrak['end_actual']) || $data_kontrak['end_actual']!=NULL) ? $data_kontrak['end_actual'] : $date;
		$data['date'] = $date;


		$day_total = (ceil(strtotime($date_range['end_date']) - strtotime($date_range['start_date']))/86400)+1;
		$day_now = (ceil(strtotime($date) - strtotime($date_range['start_date']))/86400)+1;
		if(isset($date_range['end_date'])&&isset($date_range['start_date'])){
			$data['realization'] = ($day_now / $day_total * 100);
		}
		$data['day_now'] = $day_now;
		$data['graph'] = $this->get_graph($data_kontrak['id'],$id);

		$data['graph']['max'] = ($contract_length>$data['graph']['max'] )?$contract_length:$data['graph']['max'];
		$denda_day = $this->pm->get_denda_day($id);

		// echo $denda_day;
		if($denda_day>0){
			$data['graph']['max']+=$denda_day;
		}

		$data['id_pengadaan'] = $id;
		$data_cp = $this->pm->get_contract_progress($data_kontrak['id']);

		foreach( $data_cp as $key => $val){
   			$data['graph']['supposed']['type'][$val['id']] = $val['type'];
   			$data['graph']['supposed']['percentage'][$val['id']] = (($val['supposed']/$data['graph']['max'])*100);
   			$data['graph']['realization']['percentage'][$val['id']] = (($val['realization']/$data['graph']['max'])*100);
   		}
   		


   		return $data;
   	}


   	
   	function reset_denda($id){
   		return $this->db->where('id_procurement',$id)->delete('tr_denda');
   	}
   	function hapus_amandemen($id){
   		return $this->db->where('id',$id)->delete('tr_progress_kontrak');	
   	}
   	
   	function save_amandemen($id,$data,$id_amandemen=0){
   		$get_num_amandemen 	= $this->get_amandemen_total($id);
   		$date_range 		= $this->get_date_range($id);
   		
   		$supposed = 0;

   		if($id_amandemen==0){
   			
	   		return $this->db->insert('tr_progress_kontrak',array(
	   							'id_procurement'=>$id,
	   							'step_name'=>$data['step_name'],
	   							'id_contract'=>$data['id_contract'],
	   							'start_date'=>$data['start_date'],
	   							'end_date'=>$data['end_date'],
	   							'supposed'=>$supposed,
	   							'entry_stamp'=>$data['entry_stamp'],
	   							'type'=>3
	   						));
	   	}else{

	   		return $this->db->where('id',$id_amandemen)->update('tr_progress_kontrak',array(
	   							'start_date'=>$data['start_date'],
	   							'step_name'=>$data['step_name'],
	   							'end_date'=>$data['end_date'],
	   							'supposed'=>$supposed,
	   							'edit_stamp'=>$data['edit_stamp']
	   						));

	   	}

   	}
   	function get_amandemen($id){
   		return $this->db->where('id',$id)->get('tr_progress_kontrak')->row_array();
   	}

   	function is_amandemen($id_procurement){
   		return $this->db->where('id_procurement',$id_procurement)->where('type',3)->get('tr_progress_kontrak')->result_array();
   	}

   	function get_denda_day($id){
   		$data = $this->db->where('id_procurement',$id)->get('tr_denda')->row_array();
   		// echo $this->db->last_query();
   		if(!empty($data) && !empty($data['start_date']) && !empty($data['end_date'])) {
   			return (ceil(strtotime($data['end_date']) - strtotime($data['start_date']))/86400)+1;
   		}else{
   			return 0;
   		}
   	}
   	function get_denda_price($id){
   		$data_kontrak = $this->get_kontrak($id);

		$data['denda'] = $this->get_denda($id);
		$add_date = 0;
		if(isset($data['denda']['end_date'])||isset($data['denda']['start_date'])){
			$add_date = 1;
		}
		if(empty($data_kontrak) || !isset($data_kontrak['contract_price'])){
			return 0;
		}
		if(empty($data['denda']) || empty($data['denda']['start_date']) || empty($data['denda']['end_date'])){
			return 0;
		}
		$day_denda 		= (ceil(strtotime($data['denda']['end_date']) - strtotime($data['denda']['start_date']))/86400)+$add_date;
		$max_denda = $data_kontrak['contract_price'] * 5 / 100;
		$cur_price = $data_kontrak['contract_price'] / 1000 * $day_denda;
		$denda_price = ($max_denda<$cur_price) ? $max_denda : $cur_price;
		return $denda_price;
   	}
   	function get_procurement_bsb($id,$search='', $sort='', $page='', $per_page='',$is_page=FALSE){

   		$result = $this->db->select('ms_procurement_bsb.id, tb_bidang.name bidang_name,tb_sub_bidang.name sub_bidang_name,')->where('ms_procurement_bsb.id_proc',$id)
   		->join('tb_bidang','tb_bidang.id=ms_procurement_bsb.id_bidang')
   		->join('tb_sub_bidang','tb_sub_bidang.id=ms_procurement_bsb.id_sub_bidang')
   		->where('ms_procurement_bsb.del',0);
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
   		return $this->db->get('ms_procurement_bsb')->result_array();

   	}

   	function get_procurement_barang($id,$search='', $sort='', $page='', $per_page='',$is_page=FALSE){

   		$result = $this->db ->select('*,ms_procurement_barang.id id, tb_kurs.symbol symbol')
					   		->where('ms_procurement_barang.id_procurement',$id)
					   		->where('ms_procurement_barang.del',0)
   							->join('tb_kurs', 'tb_kurs.id=ms_procurement_barang.id_kurs');
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
   		return $this->db->get('ms_procurement_barang')->result_array();

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
   	function get_procurement_penawaran($id,$search='', $sort='', $page='', $per_page='',$is_page=FALSE){

   		$result = $this->db->select('ms_procurement_peserta.*, tb_kurs.symbol symbol, ms_procurement_peserta.id id,ms_vendor.name peserta_name,ms_procurement_peserta.id_vendor id_vendor')
			   		->where('ms_procurement_peserta.id_proc',$id)
			   		->join('ms_vendor','ms_vendor.id=ms_procurement_peserta.id_vendor')
			   		->join('tb_kurs','tb_kurs.id=ms_procurement_peserta.id_kurs','LEFT')
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

   	function get_procurement_penawaran_($id,$search='', $sort='', $page='', $per_page='',$is_page=FALSE){

   		$result = $this->db->select('ms_procurement_peserta.*, tb_kurs.symbol symbol, ms_procurement_peserta.id id,ms_vendor.name peserta_name,ms_procurement_peserta.id_vendor id_vendor')
			   		->where('ms_procurement_peserta.id_proc',$id)
			   		#->where('ms_procurement_peserta.idr_value >', '0')
			   		->join('ms_vendor','ms_vendor.id=ms_procurement_peserta.id_vendor')
			   		->join('tb_kurs','tb_kurs.id=ms_procurement_peserta.id_kurs','LEFT')
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
   	function get_procurement_negosiasi($id,$search='', $sort='', $page='', $per_page='',$is_page=FALSE){

   		$result = $this->db->select('ms_procurement_negosiasi.*, ms_procurement_negosiasi.id id,ms_vendor.name peserta_name,ms_procurement_negosiasi.id_vendor id_vendor')
			   		->where('ms_procurement_negosiasi.id_proc',$id)
			   		#->where('ms_procurement_negosiasi.value >', '0')
			   		->join('ms_vendor','ms_vendor.id=ms_procurement_negosiasi.id_vendor', "LEFT")
                       ->where('ms_procurement_negosiasi.del',0)
                       ;
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}

   		return $this->db->get('ms_procurement_negosiasi')->result_array();

   	}
   	function get_procurement_negosiasi_($id,$search='', $sort='', $page='', $per_page='',$is_page=FALSE){

   		$result = $this->db->select('ms_procurement_peserta.*, tb_kurs.symbol symbol, ms_procurement_peserta.id id,ms_vendor.name peserta_name,ms_procurement_peserta.id_vendor id_vendor')
			   		->where('ms_procurement_peserta.id_proc',$id)
			   		->where('ms_procurement_peserta.negosiasi >', '0')
			   		->join('ms_vendor','ms_vendor.id=ms_procurement_peserta.id_vendor')
			   		->join('tb_kurs','tb_kurs.id=ms_procurement_peserta.id_kurs','LEFT')
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
   	function get_peserta_list($id){

   		$bsb = $this->db->select('id_sub_bidang')->where('id_proc',$id)->get('ms_procurement_bsb')->result_array();
   		$id_sub_bidang = array();
   		foreach($bsb as $key => $val){
   			$id_sub_bidang[] = $val['id_sub_bidang'];
   		}

   		$id_vendor = $this->db->select('ms_vendor.id id, ms_vendor.name name')
   		->join('ms_vendor','ms_vendor.id=ms_iu_bsb.id_vendor','INNER')
   		->join('ms_ijin_usaha','ms_ijin_usaha.id=ms_iu_bsb.id_ijin_usaha','INNER')
   		->where('vendor_status',2)
   		->where_in('id_sub_bidang',$id_sub_bidang)
   		->get('ms_iu_bsb')->result_array();
   		
   		$res = array();
   		foreach($id_vendor as $key => $row){
   			$res[$row['id']] = $row['name'];
   		}

   		return $res;
   	}
   	function get_ijin_list($id,$vendor_id){

   		$id_bsb = array('id_bidang'=>array(),'id_sub_bidang'=>array());
   		foreach($this->get_bsb_procurement($id)->result_array() as $key => $val){
   			$id_bsb['id_bidang'][] = $val['id_bidang'];
   			$id_bsb['id_sub_bidang'][] = $val['id_sub_bidang'];
   		}

   		$id_vendor = $this->db->select('ms_ijin_usaha.id, ms_ijin_usaha.type type')
   		->join('ms_iu_bsb','ms_ijin_usaha.id=ms_iu_bsb.id_ijin_usaha');

   		if(count($id_bsb['id_bidang'])>0){
   			$id_vendor->where_in('id_bidang',$id_bsb['id_bidang']);
   		}

   		if(count($id_bsb['id_sub_bidang'])>0){
   			$id_vendor->where_in('id_sub_bidang',$id_bsb['id_sub_bidang']);
   		}
   		$vendor_list = $id_vendor->where('ms_ijin_usaha.id_vendor',$vendor_id)
   		->get('ms_ijin_usaha')->result_array();
   		

   		$res = array();
   		$name = array('siup'=>'SIUP','ijin_lain'=>'Surat Ijin Usaha Lainnya','asosiasi'=>'Sertifikat Asosiasi/Lainnya','siujk'=>'SIUJK','sbu'=>'SBU');
   		foreach($vendor_list as $key => $row){
   			$res[$row['id'].'|'.$row['type']] = $name[$row['type']];
   		}

   		return $res;
   	}
   	function get_pemenang($id){

   		
   		$id_pemenang = $this->db->select('mpp.*, mpp.id id, mv.id id_vendor, mv.name name,tk.symbol kurs_name, tko.symbol kurs_name_kontrak, fee')
   		->join('ms_vendor mv','mv.id=mpp.id_vendor','LEFT')
   		->join('tb_kurs tk','tk.id=mpp.id_kurs','LEFT')
   		->join('tb_kurs tko','tk.id=mpp.id_kurs_kontrak','LEFT')
   		->where('mpp.id_proc',$id)
   		->where('mpp.is_winner',1)
   		->where('mpp.del',0)
   		->get('ms_procurement_peserta mpp')->row_array();

   		return $id_pemenang;

   	}
   	function get_pemenang_list($id){
   		
   		$id_pemenang = $this->db->select('ms_procurement_peserta.id id, ms_vendor.name name, is_winner, fee')
   		->join('ms_vendor','ms_vendor.id=ms_procurement_peserta.id_vendor')
   		->where('ms_procurement_peserta.id_proc',$id)
   		->where('ms_procurement_peserta.del',0)
   		->where('(ms_procurement_peserta.idr_value IS NOT NULL OR ms_procurement_peserta.kurs_value IS NOT NULL)')
   		->get('ms_procurement_peserta')->result_array();

   		return $id_pemenang;
   	}

   	function save_bsb($data){

		$_param = array();
		$sql = "INSERT INTO ms_procurement_bsb (
							id_proc,
							id_bidang,
							id_sub_bidang,
							entry_stamp
							) 
				VALUES (?,?,?,?) ";
		
		foreach($this->bsb as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);
		$id = $this->db->insert_id();
		
		return $id;
	
	}

	function save_peserta($data){
		$surat = $this->db->select('type')->where('id_vendor',$data['id_vendor'])->where('id',$data['id_surat'])->get('ms_ijin_usaha')->row_array();
		$data['surat'] = $surat['type'];

		$_param = array();
		$sql = "INSERT INTO ms_procurement_peserta (
							`id_proc`,
							`id_vendor`,
							`surat`,
							`id_surat`,
							
							`entry_stamp`
							) 
				VALUES (?,?,?,?,?) ";/*,?,?,?*/
							// -- `id_kurs`,
							// -- `idr_value`,
							// -- `kurs_value`,
		
		
		foreach($this->peserta as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);
		$id = $this->db->insert_id();
		
		return $id;	
	}

	function proses_pemenang($id,$data){
		$this->db->where('id_proc',$id);
		$this->db->update('ms_procurement_peserta',array('is_winner'=>0));

		$this->db->where('id',$data['pemenang']);
		$res = $this->db->where('id_procurement',$id)->update('ms_contract',array(
														'id_vendor'	=>$data['id_vendor'],
													));

		$res = $this->db->where('id_proc',$id)->where('id',$data['pemenang'])->update('ms_procurement_peserta',array(
															'is_winner'			=>1,
															'id_kurs_kontrak'	=>$data['id_kurs_kontrak'],
															'idr_kontrak'		=>$data['idr_kontrak'],
															'kurs_kontrak'		=>$data['kurs_kontrak'],
															'nilai_evaluasi'	=>$data['nilai_evaluasi'],
                                                            'fee'		        =>preg_replace("/[,]/", "", $data['fee']),
														));
		return $res;
	}

	function send_proc($id,$data){
		$this->db->where('id',$id);
		$this->db->update('ms_procurement',$data);
		return $this->db->affected_rows();
	}

	function hapus_pengadaan_bsb($id){
		$this->db->where('id',$id);
		
		return $this->db->update('ms_procurement_bsb',array('del'=>1));
	}

	function hapus_pengerjaan($id){
		$this->db->where('id',$id);
		
		return $this->db->update('tr_progress_kontrak',array('del'=>1));
	}
	function hapus($id,$table){
   		$this->db->where('id',$id);
		return $this->db->delete($table);
   	}
   	function hapus_pengadaan($id){
   		$this->db->where('id',$id);
		return $this->db->update('ms_procurement',array('del'=>1));
   	}

	function hapus_pengadaan_peserta($id){
		$this->db->where('id',$id);
		
		return $this->db->update('ms_procurement_peserta',array('del'=>1));
	}
	function get_pejabat(){
		$arr = $this->db->select('id,name')->get('tb_pejabat_pengadaan')->result_array();
		$result = array();
		foreach($arr as $key => $row){
			$result[$row['id']] = $row['name'];
		}
		return $result;
	}
	function get_budget_holder(){
		$arr = $this->db->select('id,name')->get('tb_budget_holder')->result_array();
		$result = array();
		foreach($arr as $key => $row){
			$result[$row['id']] = $row['name'];
		}
		return $result;
	}
	function get_bsb_procurement($id_proc){
		$bsb = $this->db->select('id_bidang, id_sub_bidang')->where('id_proc',$id_proc)->where('del',0)->get('ms_procurement_bsb');

		return $bsb;
	}
	function get_budget_spender(){
		$arr = $this->db->select('id,name')->get('tb_budget_spender')->result_array();
		$result = array();
		foreach($arr as $key => $row){
			$result[$row['id']] = $row['name'];
		}
		return $result;
	}
	function get_mekanisme(){
		$arr = $this->db->select('id,name')->where('id!=',1)->get('tb_mekanisme')->result_array();
		$result = array();
		foreach($arr as $key => $row){
			$result[$row['id']] = $row['name'];
		}
		return $result;
	}
	function get_proc_vendor($id){
		$id_pemenang = $this->db->select('ms_procurement_peserta.id_vendor id, CONCAT( tb_legal.name, " ",ms_vendor.name) name')
   		->join('ms_vendor','ms_vendor.id=ms_procurement_peserta.id_vendor')
   		->join('ms_vendor_admistrasi','ms_vendor_admistrasi.id_vendor=ms_vendor.id')
   		->join('tb_legal','ms_vendor_admistrasi.id_legal=tb_legal.id')
   		->join('ms_procurement','ms_procurement.id=ms_procurement_peserta.id_proc')
   		->where('ms_procurement_peserta.id_proc',$id)
   		// ->where('ms_procurement.status_procurement',1)
   		->where('ms_procurement_peserta.del',0)
   		->get('ms_procurement_peserta')->result_array();
   		$result = array();
   		// echo $this->db->last_query();
   		foreach($id_pemenang as $key => $value){
   			$result[$value['id']]=$value['name'];
   		}
   		return $result;
	}

	function get_winner_vendor($id_pengadaan){
		return $this->db
		->select('msp.*, msp.id_vendor id_winner,mv.name winner_name')
		->where('id_proc',$id_pengadaan)
		->where('is_winner',1)
		->join('ms_vendor mv','mv.id=msp.id_vendor')
		->get('ms_procurement_peserta msp')->row_array();
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

	public function save_assessment($id, $post){
		$poin = 0;
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
				'value'				=>$row,
				'entry_stamp'		=>date("Y-m-d H:i:s")
			));

			if(!$insert){

				return false;

			}

			$poin += $row;

			
		}
		$this->db->delete('tr_ass_point',array(
				'id_vendor' 		=>$post['id_vendor'],
				'id_procurement' 	=>$post['id_procurement']
			));
		$this->db->insert('tr_ass_point',array('id_vendor'=>$post['id_vendor'],'id_procurement'=>$post['id_procurement'],'point'=>$poin,'entry_stamp'=>date("Y-m-d H:i:s")));
		
		return $poin;
	}


	function query_peserta($id='',$q=''){
		$bsb = $this->get_bsb_procurement($id)->result_array();
		
   		$id_sub_bidang = array();
   		foreach($bsb as $key => $val){
   			$id_sub_bidang[] = $val['id_sub_bidang'];
   		}

   		$peserta = $this->db->select('id_vendor')->where('id_proc',$id)->where('del',0)->get('ms_procurement_peserta')->result_array();

   		$id_peserta = array();

   		foreach($peserta as $key => $val){
   			$id_peserta[] = $val['id_vendor'];
   		}

   		$this->db->select('ms_vendor.id id, is_vms, ms_vendor.name as name,ms_vendor.id as id_vendor_list, ms_vendor_admistrasi.npwp_code npwp_code,ms_vendor_admistrasi.nppkp_code nppkp_code,tr_assessment_point.point')
   		->join('ms_iu_bsb miu_bsb','ms_vendor.id=miu_bsb.id_vendor','LEFT')
   		->join('tb_bidang','tb_bidang.id=miu_bsb.id_bidang','LEFT')
   		->join('tb_dpt_type','tb_dpt_type.id=tb_bidang.id_dpt_type','LEFT')
   		->join('ms_ijin_usaha','ms_ijin_usaha.id=miu_bsb.id_ijin_usaha','LEFT')
   		->join('tr_assessment_point','tr_assessment_point.id_vendor=ms_vendor.id','LEFT');

   		// if(count($id_sub_bidang)>0){
	   	// 	$bsb = "AND id_sub_bidang IN ('".implode("','", $id_sub_bidang)."')";
	   	// }
	   	// if(count($id_peserta)>0){
	   	// 	$peserta_id = "AND ms_vendor.id NOT IN ('".implode("','", $id_peserta)."')";
	   	// }
// echo $this->db->last_query();die;
   		//$this->db->where('((vendor_status = 2 AND is_active = 1 '.$bsb.' AND ms_vendor.name LIKE "%'.$q.'%") OR is_vms = 0) '.$peserta_id)
		$this->db->where('((vendor_status = 2 AND is_active = 1 AND ms_vendor.name LIKE "%'.$q.'%") OR is_vms = 0) '.$peserta_id)
   		->order_by('is_vms','DESC')
   		->group_by('miu_bsb.id_vendor')
   		->join('ms_vendor_admistrasi','ms_vendor_admistrasi.id_vendor=ms_vendor.id','LEFT');
   		return $this->db;
	}
	function get_pengadaan_vendor($id='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()){
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
		
   		$this->db = $this->query_peserta($id,$this->input->get('q'));

		$a = $this->filter->generate_query($this->db->group_by('id'),$filter);
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$a->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$a->limit($per_page, $per_page*($cur_page - 1));
		}

		$query = $a->get('ms_vendor');
		// echo $this->db->last_query();
		return $query->result_array();
	}
	function search_kandidat($id){
		$res = null;
		if($this->input->post('term')!=''){
			$this->db = $this->query_peserta($id,$this->input->post('term'));

			$res_q = $this->db->get('ms_iu_bsb')->result_array();
			foreach($res_q as $row){
				$res[$row['id']]['id'] = $row['id'];
				$res[$row['id']]['name'] =$row['name'];
			}

		}
		// echo $this->db->last_query();
		// echo $this->input->post('id').$this->input->post('q');

		echo json_encode($res);
	}
	function tambah_peserta($id,$id_surat){
		$this->db->insert('ms_procurement_peserta',array('id_vendor'=>$id,'id_surat'=>$id_surat));
	}


	function get_kurs_barang($in=array()){
		$this->db->select('id,name');

		if(!empty($in)){
			$this->db->where_in('id',$in);
		}

		$arr=$this->db->get('tb_kurs')->result_array();
		$result = array();
		foreach($arr as $key => $row){
			$result[$row['id']] = $row['name'];
		}
		return $result;
	}

	function get_auction_list($search='', $sort='', $page='', $per_page='',$is_page=FALSE){
    	$user = $this->session->userdata('user');
		$this->db->select('*, ms_procurement.id id, ms_procurement.name name, ms_procurement.work_area work_area, ms_procurement.auction_date auction_date, ms_vendor.name pemenang, "-" as pemenang_kontrak , "-" as user_kontrak , proc_date, ms_procurement.del del');
		$this->db->group_by('ms_procurement.id');
		$this->db->where('ms_procurement.del',0);
		$this->db->where('ms_procurement.id_mekanisme',1);
		if($this->session->userdata('admin')['id_role']==4){
			$this->db->where('ms_procurement.status_procurement!=',0);
		}
		$this->db->join('ms_procurement_peserta','ms_procurement_peserta.id_proc=ms_procurement.id','LEFT');
		$this->db->join('ms_vendor','ms_procurement_peserta.is_winner=ms_vendor.id','LEFT');

		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}else{
			$this->db->order_by('ms_procurement.id','desc');
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
		
		$query = $this->db->get('ms_procurement');		
		return $query->result_array();
		
    }

	function get_procurement_kurs($id,$search='', $sort='', $page='', $per_page='',$is_page=FALSE){
   		$result = $this->db->select('*,ms_procurement_kurs.id id')
   		->join('tb_kurs', 'ms_procurement_kurs.id_kurs=tb_kurs.id')
   		->where('ms_procurement_kurs.id_procurement',$id)
   		->where('ms_procurement_kurs.del',0);
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
		$res = $this->db->get('ms_procurement_kurs');

   		return $res->result_array();
   	}


	function save_barang($data){
		
		$_param = array();
		$sql = "INSERT INTO ms_procurement_barang(
							`id_procurement`,
							`is_catalogue`,
							`id_material`,
							`nama_barang`,
							`id_kurs`,
							`nilai_hps`,
							`entry_stamp`,
							`category`
							) 
				VALUES (?,?,?,?,?,?,?,?) ";
		
		
		foreach($this->barang as $_param) $param[$_param] = $data[$_param];
		
		$res = $this->db->query($sql, $param);

		$id = $this->db->insert_id();
		
		return $id;	
	}

	function save_barang_catalogue($data){
		$data['for'] = isset($data['for']) ? $data['for'] : (($data['category']=='jasa') ? 6 : 1);
		
		$_param = array();
		$sql = "INSERT INTO ms_material(
							`id_barang`,
							`nama`,
							`category`,
							`for`,
							`entry_stamp`,
							`id_kurs`
							) 
				VALUES (?,?,?,?,?,?) ";
		
		
		foreach($this->barang_k as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);
		
		return $this->db->insert_id();	
	}

	function save_barang_harga($data){

		$_param = array();
		$sql = "INSERT INTO tr_material_price(
							`id_material`,
							`id_procurement`,
							`price`,
							`entry_stamp`,
							`date`
							) 
				VALUES (?,?,?,?,?) ";
		
		
		foreach($this->barang_h as $_param) $param[$_param] = $data[$_param];
		
		$res = $this->db->query($sql, $param);

		// $id = $this->db->insert_id();
		
		return $res;	
	}

	function edit_barang($data,$id){

		$param = array();
		
		$this->db->where('id',$id);
		$res = $this->db->update('ms_procurement_barang',$data);
		
		return $res;
	
	}
	function get_email_user($budget_spender){
		$query = 	"	SELECT
							email
						FROM
							ms_admin
						WHERE id_role = ?
						AND id_division = ?
					";
		$query = $this->db->query($query, array(9, $budget_spender));
		return $query->result_array();
	}
	function get_barang_data($id){
   		$barang			= $this->db->select('*, tb_kurs.name kurs_name')
					   		->where('ms_procurement_barang.id',$id)
					   		->join('tb_kurs', 'ms_procurement_barang.id_kurs=tb_kurs.id','LEFT')
					   		->get('ms_procurement_barang')
					   		->row_array();
   		
   		return $barang;
   	}
	function save_nilai($post){
   		foreach($post as $key => $row){
   			$res = $this->db->where('id',$row)->update('ms_procurement_peserta',array('nilai_evaluasi'=>$row));
   			if(!$res){
   				return false;
   			}
   		}
   		return true;
   	}

   	function save_penawaran($post){
   		foreach($post['id'] as $key => $row){
   			$res = $this->db->where('id',$key)->update('ms_procurement_peserta',
   				array(
   					'nilai_evaluasi'=>$post['nilai_evaluasi'][$key],
   					'idr_value'=>preg_replace("/[,]/", "", $post['idr_value'][$key]),
   					'id_kurs'=>$post['id_kurs'][$key],
   					'kurs_value'=>preg_replace("/[,]/", "", $post['kurs_value'][$key]),
   					'fee'		=> preg_replace("/[,]/", "", $post['fee'][$key]),
   					'remark'=>$post['remark'][$key],
   				)
			);
   			if(!$res){
   				return false;
   			}
   		}

   		return true;
   	}
   	function save_negosiasi($post){
   		// print_r($post);die;
   		foreach($post['negosiasi'] as $id_vendor => $value){	
   			// print_r($key);
   			// print_r($row);die;
   			$id_proc= $this->db->where('id_proc', $key)->get('ms_procurement_peserta')->row_array();

   			$input = array(
   					'id_proc'	=> $post['id_proc'],
   					'id_vendor'	=> $id_vendor,
   					'value'		=> preg_replace("/[,]/", "", $value),
   					'fee'		=> preg_replace("/[,]/", "", $post['fee'][$id_vendor]),
   					'remark'	=> $post['remark'][$id_vendor],
   				);
   			// print_r($input);
   			$res = $this->db->insert('ms_procurement_negosiasi', $input);
   			if(!$res){
   				return false;
   			}
   		}	
   		// die;
   		// print_r($this->db->last_query());die;
   		return true;
   	}

   	function reset($id){

   		$this->db->where('id_proc',$id)->delete('ms_procurement_bsb');
   		$this->db->where('id_procurement',$id)->delete('ms_procurement_barang');
   		$this->db->where('id_proc',$id)->delete('ms_procurement_peserta');
   		$this->db->where('id_procurement',$id)->delete('ms_contract');
   		$this->db->where('id_procurement',$id)->delete('tr_progress_kontrak');
   		$this->db->where('id_proc',$id)->delete('tr_progress_pengadaan');
   		
		return true;
   	}

   	function check_barang($id){
   		$total_barang = $this->db
   						->where('tb_bidang.id_dpt_type',1)
   						->where('ms_procurement_bsb.id_proc',$id)
   						->where('ms_procurement_bsb.del',0)
   						->join('tb_bidang','tb_bidang.id = ms_procurement_bsb.id_bidang')
   						->get('ms_procurement_bsb')
   						->num_rows();
   		if($total_barang>0){
   			return true;
   		}else{
   			return false;
   		}
   	}
   	function pengadaan_graph($id){

   	}

   	function v_exp($data){
   		// print_r($data);die;

   		$proc = $this->db->select('*')
   			   		->where('ms_procurement.id',$data['id_procurement'])
			   		->get('ms_procurement')->row_array();

   		$kurs = $this->db->select('CONCAT(symbol) symbol')
   			   		->where('tb_kurs.id',$data['contract_kurs'])
			   		->get('tb_kurs')->row_array();


		$exp = array(
					'id_vendor'			=> $data['id_vendor'],
					'job_name' 			=> $proc['name'],
					'job_giver'			=> "PT. Nusantara Regas",
					'contract_no'		=> $data['no_contract'],
					'contract_start'	=> $data['start_contract'],
					'contract_end'		=> $data['end_contract'],
					'price_idr'			=> $data['contract_price'],
					'price_foreign'		=> $data['contract_price_kurs'],
					'currency'			=> $kurs['symbol'],
					'entry_stamp'		=> $data['entry_stamp'],
					'data_status'		=> 2,
					);

		$this->db->insert('ms_pengalaman',$exp);

   		// print_r($exp);die;
   		return "asd";
   	}

}
