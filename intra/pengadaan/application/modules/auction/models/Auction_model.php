<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auction_model extends CI_Model{
	function __construct(){
		parent::__construct();
		$this->field_master = array(
								'name',
								'id_division',
								'budget_source',
								'id_pejabat_pengadaan',
								'auction_type',
								'work_area',
								'room',
								'auction_date',
								'auction_duration',
								'budget_holder',
								'budget_spender',
								'entry_stamp',
								'id_mekanisme'
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

		$this->peserta = array('id_proc',
							'id_vendor',
							'surat',
							'id_surat',
							'entry_stamp');

		$this->barang = array(
							'id_procurement',
							'is_catalogue',
							'id_material',
							'nama_barang',
							'volume',
							'id_kurs',
							'nilai_hps',
							'entry_stamp');
		
		$this->barang_k = array(
							'id_barang',
							'nama',
							'id_kurs',
							'entry_stamp',
							'category',
							'for');

		$this->tatacara = array(
							'id_procurement',
							'metode_auction',
							'metode_penawaran',
							'entry_stamp');

		$this->peserta = array(
							'id_proc',
							'id_vendor',
							'entry_stamp');
		$this->kurs = array(
							'id_procurement',
							'id_kurs',
							'rate',
							'entry_stamp');

		$this->persyaratan = array(
							'id_proc',
							'description',
							'entry_stamp');
	}
	function get_auction_list($search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()){
    	$user = $this->session->userdata('user');
    	$this->db->query("SET sql_mode=(SELECT REPLACE (@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
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
		$a = $this->filter->generate_query($this->db->group_by('ms_procurement.id'),$filter);
		$query = $a->get('ms_procurement');
		return $query->result_array();
		
    }

    function get_auction_langsung($search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()){
    	$user = $this->session->userdata('user');
    	$this->db->query("SET sql_mode=(SELECT REPLACE (@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
		$this->db->select('*, ms_procurement.id id, ms_procurement.name name, ms_procurement.work_area work_area, ms_procurement.auction_date auction_date, ms_vendor.name pemenang, "-" as pemenang_kontrak , "-" as user_kontrak , proc_date, ms_procurement.del del');
		$this->db->group_by('ms_procurement.id');
		$this->db->where('ms_procurement.del',0);
		$this->db->where('ms_procurement.id_mekanisme',1);
		$this->db->where('ms_procurement.is_started',1);
		$this->db->where('ms_procurement.is_finished',0);
		$this->db->or_where('ms_procurement.is_suspended',1);

		

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
		$a = $this->filter->generate_query($this->db->group_by('ms_procurement.id'),$filter);
		$query = $a->get('ms_procurement');		
		return $query->result_array();
    }

    function get_auction_selesai($search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()){
    	$user = $this->session->userdata('user');
    	$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

		$this->db->select('*, ms_procurement.id id, ms_procurement.name name, ms_procurement.work_area work_area, ms_procurement.auction_date auction_date, ms_vendor.name pemenang, "-" as pemenang_kontrak , "-" as user_kontrak , proc_date, ms_procurement.del del');
		$this->db->group_by('ms_procurement.id');
		$this->db->where('ms_procurement.del',0);
		$this->db->where('ms_procurement.id_mekanisme',1);
		$this->db->where('ms_procurement.is_finished',1);

		

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
		$a = $this->filter->generate_query($this->db->group_by('ms_procurement.id'),$filter);
		$query = $a->get('ms_procurement');		
		return $query->result_array();
    }

	function search_vendor(){
		$result = array();
		$query = $this->db->select('id, name')->like('name',$this->input->post('term'),'both')->where('del',0)->where('vendor_status',2)->get('ms_vendor')->result_array();
		foreach($query as $key => $value){
			$result[$value['id']]['id'] = $value['id'];
			$result[$value['id']]['name'] = $value['name'];
		}

		return $result;
	}
    

   	function save_data($data){
   		$_param = array();
		$sql = "INSERT INTO ms_procurement (								
								`name`,
								`id_division`,
								`budget_source`,
								`id_pejabat_pengadaan`,
								`auction_type`,
								`work_area`,
								`room`,
								`auction_date`,
								`auction_duration`,
								`budget_holder`,
								`budget_spender`,
								`entry_stamp`,
								`id_mekanisme`
								) 
				VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ";

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

   	function hapus($id,$table){
   		$this->db->where('id',$id);
		return $this->db->delete($table);
   	}

   	function duplicate_data($total = 0, $id = ''){
		for($i=0;$i<$total;$i++){
			$sql = "SELECT * FROM ms_procurement WHERE id = ?";
			$sql = $this->db->query($sql, $id);
			$data = $sql->row_array();
			
			$param = array(
				'name'=>$data['name']." - ".($i + 2),
				'budget_source'=>$data['budget_source'],
				'id_pejabat_pengadaan'=>$data['id_pejabat_pengadaan'],
				'auction_type'=>$data['auction_type'],
				'work_area'=>$data['work_area'],
				'room'=>$data['room'],
				'auction_date'=>$data['auction_date'],
				'auction_duration'=>$data['auction_duration'],
				'budget_holder'=>$data['budget_holder'],
				'budget_spender'=>$data['budget_spender'],
				'entry_stamp'=>date("Y-m-d H:i:s"),
				'id_mekanisme'=>$data['id_mekanisme'],
			);
						
			$new_id = $this->save_data($param);
			
			/* SBU */
			/*$sql = "SELECT * FROM ms_sbu_lelang WHERE id_lelang = ?";
			$sql = $this->db->query($sql, $id);
			
			foreach($sql->result() as $data){
				$sql_1 = "INSERT INTO ms_sbu_lelang (`id_sbu`,`id_lelang`,`entry_stamp`) VALUES (?,?,?)";
				$this->db->query($sql_1, array($data->id_sbu, $new_id, date("Y-m-d H:i:s")));	
			}*/
			
			/* TATA CARA */
			$sql = "SELECT * FROM ms_procurement_tatacara WHERE id_procurement = ?";
			$sql = $this->db->query($sql, $id);
			$data = $sql->row_array();
			
			$sql = "INSERT INTO ms_procurement_tatacara (`id_procurement`,`metode_auction`,`metode_penawaran`,`entry_stamp`) VALUES (?,?,?,?)";
			$this->db->query($sql, array($new_id, $data['metode_auction'], $data['metode_penawaran'], date("Y-m-d H:i:s")));	
			

			/* PERSYARATAN */
			$sql = "SELECT * FROM ms_procurement_persyaratan WHERE id_proc = ?";
			$sql = $this->db->query($sql, $id);
			$data = $sql->row_array();
			
			$sql = "INSERT INTO ms_procurement_persyaratan (`id_proc`,`description`,`entry_stamp`) VALUES (?,?,?)";
			$this->db->query($sql, array($new_id, $data['description'], date("Y-m-d H:i:s")));
			
			/* PESERTA */
			$sql = "SELECT * FROM ms_procurement_peserta WHERE id_proc = ?";
			$sql = $this->db->query($sql, $id);
			
			foreach($sql->result() as $data){
				$sql_1 = "INSERT INTO ms_procurement_peserta (`id_vendor`,`id_proc`,`entry_stamp`) VALUES (?,?,?)";
				$this->db->query($sql_1, array($data->id_vendor, $new_id,date("Y-m-d H:i:s")));	
			}
			
			/* KURS */
			$sql = "SELECT * FROM ms_procurement_kurs WHERE id_procurement = ?";
			$sql = $this->db->query($sql, $id);
			
			foreach($sql->result() as $data){
				$sql_1 = "INSERT INTO ms_procurement_kurs (`id_procurement`,`id_kurs`,`rate`,`entry_stamp`) VALUES (?,?,?,?)";
				$this->db->query($sql_1, array($new_id, $data->id_kurs, $data->rate, date("Y-m-d H:i:s")));	
			}
		}
		return true;
	}

   	function get_auction($id){
   		$arr = $this->db->select('*,ms_procurement.name name, tb_budget_holder.name budget_holder_name, tb_budget_spender.name budget_spender_name, tb_pejabat_pengadaan.name pejabat_pengadaan_name,tb_mekanisme.name mekanisme_name')
   		->where('ms_procurement.id',$id)
   		->join('ms_procurement_tatacara','ms_procurement_tatacara.id_procurement=ms_procurement.id','LEFT')
   		->join('tb_pejabat_pengadaan','tb_pejabat_pengadaan.id=ms_procurement.id_pejabat_pengadaan','LEFT')
		->join('tb_budget_holder','tb_budget_holder.id=ms_procurement.budget_holder','LEFT')
		->join('tb_budget_spender','tb_budget_spender.id=ms_procurement.budget_spender','LEFT')
		->join('tb_mekanisme','tb_mekanisme.id=ms_procurement.id_mekanisme','LEFT')
   		->get('ms_procurement')->row_array();
		
		return $arr;
   	}

   	function get_contract_progress($id){
   		$arr = $this->db->select('*')->where('id_contract',$id)->get('tr_progress_kontrak')->result_array();
   		return $arr;
   	}

   	function tambah_progress($id,$data){
   		$contract = $this->get_kontrak($id);
   		$contract_length = ceil((abs(strtotime($contract['end_work'])-strtotime($contract['start_work']))+1)/86400) ;

   		$arr = $this->db->select('supposed,realization')->where('id_contract',$contract['id'])->get('tr_progress_kontrak')->result_array();
   		
   		$total_day = 0;
   		foreach($arr as $key => $value){
   			$total_day += $value['supposed'];
   		}
   		
   		if(($this->input->post('supposed')+$total_day)>$contract_length){
   			$this->session->set_flashdata('msgSuccess','<p class="errorMsg">Tanggal yang ditentukan tidak boleh melebihi tanggal pada kontrak</p>');
   			return false;
   		}else{
   			if(($this->input->post('supposed')+$total_day)==$contract_length){
   				$this->db->where('id',$id);
				$this->db->update('ms_procurement',array('status_procurement'=>2));
   			}
   			$_param = array();
			$sql = "INSERT INTO tr_progress_kontrak (
									`id_contract`,
									`step_name`,
									`supposed`,
									`realization`,
									`entry_stamp`
									) 
					VALUES (?,?,?,?,?) ";

			foreach($this->progress as $_param) $param[$_param] = $data[$_param];
			
			$this->db->query($sql, $param);
			$id = $this->db->insert_id();
			if($id){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Tambah progress berhasil</p>');
				return $id;
			}else{
				$this->session->set_flashdata('msgSuccess','<p class="errorMsg">Gagal Menambahkan Data</p>');
				return false;
			}
   		}
   	}
   	function get_graph($id){
   		$data = $this->get_contract_progress($id);
   	
   		$result = array();
   		$total_supposed = $total_realization = 0;
   		$color = $this->config->item('color');
   		// $color_o = array(array(22, 160, 133),array(39, 174, 96),array(41, 128, 185),array(142, 68, 173),array(243, 156, 18),array(211, 84, 0),array(192, 57, 43));	

   		$row = 0;
   		foreach( $data as $key => $val){
   			$result['supposed']['data'][$val['id']] = $val['supposed'];
   			$result['supposed']['color'][$val['id']] = implode(',',$color[$key]);
   			$result['realization']['data'][$val['id']] = $val['realization'];
   			$result['realization']['color'][$val['id']] = implode(',',$color[$key]);
   			$total_supposed+= $val['supposed'];
   			$total_realization+= $val['realization'];
   			$row++;
   		}

   		$result['supposed']['total'] = $total_supposed;
   		$result['realization']['total'] = $total_realization;

   		$result['max'] = ($total_supposed>$total_realization)?$total_supposed:$total_realization;
   		
   		return $result;

   	}
   	function get_kontrak($id){
   		$arr = $this->db->select('*,ms_contract.id id,CONCAT(tb_legal.name," " , ms_vendor.name) vendor_name, tb_kurs.symbol kurs_name')
		->join('ms_vendor','ms_vendor.id=ms_contract.id_vendor','LEFT')
   		->join('ms_vendor_admistrasi','ms_contract.id_vendor=ms_vendor_admistrasi.id_vendor','LEFT')
   		->join('tb_legal','ms_vendor_admistrasi.id_legal=tb_legal.id','LEFT')
   		->join('tb_kurs','ms_contract.contract_kurs=tb_kurs.id','LEFT')
   		->where('id_procurement',$id)
   		->get('ms_contract')->row_array();

		return $arr;
   	}

   	//---------------------
	//		  BARANG
	//---------------------
   	function get_procurement_barang($id,$search='', $sort='', $page='', $per_page='',$is_page=FALSE){

   		$result = $this->db->select('ms_procurement_barang.*, tb_kurs.name kurs')
   		->join('tb_kurs','ms_procurement_barang.id_kurs=tb_kurs.id','left')
   		->where('ms_procurement_barang.del',0)
   		->where('ms_procurement_barang.id_procurement',$id);
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
   		$result = $this->db->get('ms_procurement_barang')->result_array();

   		return $result;
   	}

   function get_barang_data($id){
   		$barang			= $this->db->select('*, tb_kurs.name kurs_name')
					   		->where('ms_procurement_barang.id',$id)
					   		->join('tb_kurs', 'ms_procurement_barang.id_kurs=tb_kurs.id','LEFT')
					   		->get('ms_procurement_barang')
					   		->row_array();
   		
   		return $barang;
   	}

   	function get_kurs_list($in=array()){
		$this->db->select('id,name');
		if(!empty($in)){
			$this->db->where_not_in('id',$in);
		}
		$arr=$this->db->get('tb_kurs')->result_array();
		$result = array();
		foreach($arr as $key => $row){
			$result[$row['id']] = $row['name'];
		}
		return $result;
	}

	function get_kurs_barang($in=array()){
		$this->db->select('id,name');

		if(!empty($in)){
			$this->db->where_in('id',$in);
			$arr=$this->db->get('tb_kurs')->result_array();
			$result = array();
			foreach($arr as $key => $row){
				$result[$row['id']] = $row['name'];
			}
			return $result;
		}else{
			return array();
		}
		
	}

	function get_kurs($id){
		
	}
   	//---------------------
	//		TATA CARA
	//---------------------
   	function get_procurement_tatacara($id,$search='', $sort='', $page='', $per_page='',$is_page=FALSE){
   		$result = $this->db->select('*')
   		->where('ms_procurement_tatacara.id_procurement',$id)
   		->where('ms_procurement_tatacara.del',0)
   		->join('ms_procurement', 'ms_procurement_tatacara.id_procurement=ms_procurement.id');
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
   		return $this->db->get('ms_procurement_tatacara')->row_array();
   	}

   	function get_tatacara_peserta($id){
   		$check			= $this->db->select('*')
   		->where('id_procurement',$id)
   		->get('ms_procurement_tatacara')
   		->row_array();

   		return $check;
   	}

   	function get_tatacara_list($id){
   		$tatacara			= $this->db->select('id_procurement')
   		->where('id',$id)
   		->get('ms_procurement_tatacara')
   		->result_array();
   		$id_procurement		 = array();
   		foreach($tatacara as $key => $val){
   			$id_procurement[] = $val['id_procurement'];
   		}


   		$id_vendor = $this->db->select('*')
   		->join('ms_procurement','ms_procurement.id=ms_procurement_tatacara.id_procurement')
   		->where('ms_procurement_tatacara.del',0)
   		->get('ms_procurement_tatacara')->result_array();
   		
   		$res = array();
   		foreach($id_vendor as $key => $row){
   			$res[$row['id']] = $row['name'];
   		}

   		return $res;
   	}

	function save_tatacara($data){
		$_param = array();
		$sql = "INSERT INTO ms_procurement_tatacara (
							`id_procurement`,
							`metode_auction`,
							`metode_penawaran`,
							`entry_stamp`
							) 
				VALUES (?,?,?,?) ";
		
		
		foreach($this->tatacara as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);
		$id = $this->db->insert_id();
		
		return $id;	
	}
	
	function edit_tatacara($id, $data){
		$param = array();
		$this->db->where('id_procurement',$id);
		$res = $this->db->update('ms_procurement_tatacara',$data);
		return $res;
	}





	function save_barang($form){
		
		$_param = array();
		$sql = "INSERT INTO ms_procurement_barang(
							`id_procurement`,
							`is_catalogue`,
							`id_material`,
							`nama_barang`,
							`volume`,
							`id_kurs`,
							`nilai_hps`,
							`entry_stamp`
							) 
				VALUES (?,?,?,?,?,?,?,?) ";
		
		
		foreach($this->barang as $_param) $param[$_param] = $form[$_param];
		
		$res = $this->db->query($sql, $param);


  		$insert_id = $this->db->insert_id();
		
		return $insert_id;	
	}

	function save_barang_catalogue($data){
		$data['id_barang'] = isset($data['id_barang']) ? $data['id_barang'] : 0;
		$data['for'] = isset($data['for']) ? $data['for'] : (($data['category']=='jasa') ? 6 : 1);
		
		$_param = array();
		$sql = "INSERT INTO ms_material(
							`id_barang`,
							`nama`,
							`id_kurs`,
							`entry_stamp`,
							`category`,
							`for`
							) 
				VALUES (?,?,?,?,?,?) ";
		
		
		foreach($this->barang_k as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);

		$id = $this->db->insert_id();
		
		return $id;	
	}

	function edit_barang($data,$id){

		$param = array();
		
		$this->db->where('id',$id);
		$res = $this->db->update('ms_procurement_barang',$data);
		
		return $res;
	
	}

	//---------------------
	//		PESERTA
	//---------------------
   	function get_procurement_peserta($id,$search='', $sort='', $page='', $per_page='',$is_page=FALSE){
   		$result = $this->db->select('ms_procurement_peserta.*, ms_vendor.id id_vendor, ms_vendor.name name')
   		->join('ms_vendor', 'ms_procurement_peserta.id_vendor=ms_vendor.id')
   		->where('ms_procurement_peserta.id_proc',$id)
   		->where('ms_procurement_peserta.del',0);
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
		$res = $this->db->get('ms_procurement_peserta');

   		// echo $this->db->last_query();
   		return $res->result_array();
   	}
   	function get_available_peserta($id, $search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()){
   		$id_peserta = array();

   		foreach($this->get_procurement_peserta($id) as $key => $value) {
   			$id_peserta[] = $value['id_vendor'];
   		}
		if(count($id_peserta)>0){
	   		$peserta_id = "AND ms_vendor.id NOT IN ('".implode("','", $id_peserta)."')";
	   	}
	   	$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

		$this->db->select('ms_vendor.id as id,is_vms,ms_vendor.id as id_vendor_list ,ms_vendor.name name, tb_blacklist_limit.value category_name,ms_vendor_admistrasi.npwp_code npwp_code,ms_vendor_admistrasi.nppkp_code nppkp_code')
		->order_by('point')
		->join('ms_vendor_admistrasi','ms_vendor_admistrasi.id_vendor=ms_vendor.id','LEFT')
		->join('tr_assessment_point','tr_assessment_point.id_vendor=ms_vendor.id','LEFT')
		->join('tb_blacklist_limit','tb_blacklist_limit.id=tr_assessment_point.category','LEFT');
		$this->db->where('((vendor_status = 2 AND is_active = 1 AND ms_vendor.name LIKE "%'.$q.'%") OR is_vms = 0) '.$peserta_id);
		
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

   	function get_peserta_list($id){
   		$peserta = $this->db->select('id_proc')
   		->where('id',$id)
   		->get('ms_procurement_peserta')
   		->result_array();
   		$id_proc 		= array();

   		foreach($peserta as $key => $val){
   			$id_proc[] 	= $val['id_proc'];
   		}


   		$id_vendor = $this->db->select('ms_procurement_peserta.*, ms_vendor.name name')
   		->join('ms_vendor','ms_vendor.id=ms_procurement_peserta.id_proc')
   		->where('ms_procurement_peserta.del',0)
   		->get('ms_procurement_peserta')->result_array();
   		
   		$res = array();
   		foreach($id_vendor as $key => $row){
   			$res[$row['id']] = $row['name'];
   		}

   		return $res;
   	}



   	function get_vendor_list(){
		$arr = $this->db->select('id,name')
				->where('ms_vendor.vendor_status',2)
				->get('ms_vendor')->result_array();
		$result = array();
		foreach($arr as $key => $row){
			$result[$row['id']] = $row['name'];
		}
		return $result;
	}

	function save_peserta($data){
		$this->db->where('id_proc',$data['id_proc'])->where('id_vendor',$data['id_vendor'])->delete('ms_procurement_peserta');
		$_param = array();
		$sql = "INSERT INTO ms_procurement_peserta(
							`id_proc`,
							`id_vendor`,
							`entry_stamp`
							) 
				VALUES (?,?,?) ";
		
		
		foreach($this->peserta as $_param) $param[$_param] = $data[$_param];
		
		$res = $this->db->query($sql, $param);
		
		return $res;	
	}


	//---------------------
	//		PESERTA
	//---------------------
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


	function save_kurs($data){
		$_param = array();
		$sql = "INSERT INTO ms_procurement_kurs(
							`id_procurement`,
							`id_kurs`,
							`rate`,
							`entry_stamp`
							) 
				VALUES (?,?,?,?) ";
		
		
		foreach($this->kurs as $_param) $param[$_param] = $data[$_param];
		
		$result = $this->db->query($sql, $param);
		
		return $result;	
	}





	function get_pejabat(){
		$arr = $this->db->select('id,name')->get('tb_pejabat_pengadaan')->result_array();
		$result = array();
		foreach($arr as $key => $row){
			$result[$row['id']] = $row['name'];
		}
		return $result;
	}

	function get_tatacara($id){
		$arr 	= $this->db->select('id,,id_procurement,metode_auction,metode_penawaran')
					->where('ms_procurement_tatacara.id_procurement',$id)
					->get('ms_procurement_tatacara')->result_array();
		$result = array();
		foreach($arr as $key => $row){
			$result[$row['id']] = $row['metode_auction'];
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
	function get_budget_spender(){
		$arr = $this->db->select('id,name')->get('tb_budget_spender')->result_array();
		$result = array();
		foreach($arr as $key => $row){
			$result[$row['id']] = $row['name'];
		}
		return $result;
	}
	function get_mekanisme(){
		$arr = $this->db->select('id,name')->get('tb_mekanisme')->result_array();
		$result = array();
		foreach($arr as $key => $row){
			$result[$row['id']] = $row['name'];
		}
		return $result;
	}


	//-------------------
	//		PERSYARATAN
	//-------------------
	function get_persyaratan($id){
		$arr 	= $this->db->select('*')
					->where('ms_procurement_persyaratan.id_proc',$id)
					->get('ms_procurement_persyaratan')->result_array();
		$result = array();
		foreach($arr as $key => $row){
			$result[$row['id']] = $row['description'];
		}
		return $result;
	}
	function get_procurement_persyaratan($id,$search='', $sort='', $page='', $per_page='',$is_page=FALSE){
   		$result = $this->db->select('ms_procurement_persyaratan.*, ms_procurement.id id_proc')
   		->where('ms_procurement_persyaratan.id_proc',$id)
   		->where('ms_procurement_persyaratan.del',0)
   		->join('ms_procurement', 'ms_procurement_persyaratan.id_proc=ms_procurement.id');
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
   		return $this->db->get('ms_procurement_persyaratan')->row_array();
   	}

   	function save_persyaratan($data){
   		
   		$_param = array();
		$sql = "INSERT INTO ms_procurement_persyaratan (
								`id_proc`,
								`description`,
								`entry_stamp`
								) 
				VALUES (?,?,?) ";

		foreach($this->persyaratan as $_param) $param[$_param] = $data[$_param];

		$this->db->query($sql, $param);
		$id = $this->db->insert_id();
		
		return $id;
   	}

   function edit_persyaratan($id, $data){
		$param = array();
		$this->db->where('id_proc',$id);
		$res = $this->db->update('ms_procurement_persyaratan',$data);
		// echo print_r($data);
		return $res;
	}
	
	function save_remark($id){
   		$res = $this->db->where('id',$id)->update('ms_procurement',array('remark'=>$this->input->post('remark')));
   		return $res;
   	}
}
