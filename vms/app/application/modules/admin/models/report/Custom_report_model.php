<?php
class Custom_report_model extends CI_Model{

	public function __construct(){
		parent::__construct();
	}

	public function get_master(){
		$sql = "SELECT a.id,
					   a.name, 
					   a.idr_value,
					   a.kurs_value,
					   b.symbol,
					   a.budget_source,
					   a.del,
					   c.name AS pejabat_pengadaan,
					   a.budget_year,
					   d.name AS budget_holder_name,
					   e.name AS budget_spender_name,
					   f.name AS mekanisme,
					   (SELECT aa.name FROM ms_vendor aa LEFT JOIN ms_procurement_peserta ab ON aa.id = ab.id_vendor WHERE ab.id_proc = a.id AND is_winner = 1 LIMIT 0,1) AS pemenang,
					   g.contract_price,
					   g.contract_price_kurs,
					   h.symbol AS symbol_contract,
					   g.start_contract,
					   g.end_contract,
					   g.end_contract AS bast_date  

					   FROM ms_procurement a 

					   LEFT JOIN tb_kurs b ON a.id_kurs = b.id
					   LEFT JOIN tb_pejabat_pengadaan c ON a.id_pejabat_pengadaan = c.id
					   LEFT JOIN tb_budget_holder d ON a.budget_holder = d.id
					   LEFT JOIN tb_budget_spender e ON a.budget_spender = e.id
					   LEFT JOIN tb_mekanisme f ON a.id_mekanisme = f.id 		
					   LEFT JOIN ms_contract g ON a.id = g.id_procurement 
					   LEFT JOIN tb_kurs h ON g.contract_kurs = h.id 

					   WHERE a.del = 0";

		return $this->db->query($sql);	
	}

	public function get_pengadaan_step(){
		$sql = "SELECT * FROM tb_progress_pengadaan";
		return $this->db->query($sql);
	}

	public function get_step_date($id_proc = '', $id_progress = ''){
		$sql = "SELECT * FROM tr_progress_pengadaan WHERE id_proc = ? AND id_progress = ?";
		$sql = $this->db->query($sql, array($id_proc, $id_progress));
		$sql = $sql->row_array();

		return $sql['date'];
	}

	public function get_barang($id = ''){
		$sql = "SELECT a.*, b.symbol FROM ms_procurement_barang a LEFT JOIN tb_kurs b ON a.id_kurs = b.id WHERE a.id_procurement = ?";
		return $this->db->query($sql, $id);
	}

	public function get_peserta($id = ''){
		$sql = "SELECT a.*, b.name FROM ms_procurement_peserta a LEFT JOIN ms_vendor b ON a.id_vendor = b.id WHERE a.id_proc = ?";
		return $this->db->query($sql, $id);
	}

	public function left_join($table = '', $id_procurement = ''){
		$sql = "SELECT * FROM ".$table." WHERE id_procurement = ?";
		return $this->db->query($sql, $id_procurement);
	}

	 function get_pengadaan_progress($search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()) 
    {
    	// $this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
    	$user = $this->session->userdata('user');
    	$admin = $this->session->userdata('admin');
		$this->db->select(' ms_procurement.id id, ms_procurement.name name,ms_procurement.del del');
		$this->db->where('ms_procurement.del',0);
		$this->db->where('ms_procurement.id_mekanisme!=',1);
		if($this->session->userdata('admin')['id_role']==4){
			$this->db->where('ms_procurement.status_procurement=',1);
		}
		if($this->session->userdata('admin')['id_role']==9){
			$this->db->where('ms_procurement.id_division=',$admin['id_division']);
		}
		$this->db->join('ms_procurement_peserta','ms_procurement_peserta.id_proc=ms_procurement.id','LEFT');
		$this->db->join('ms_vendor','ms_procurement_peserta.id=ms_vendor.id','LEFT');
		
		$this->db->join('ms_procurement_bsb','ms_procurement_bsb.id_proc=ms_procurement.id','LEFT');
		$this->db->join('ms_contract','ms_contract.id_procurement=ms_procurement.id','LEFT');




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
		
		return $query->result_array();
    }
     function get_pengadaan_progress_by_id($id) 
    {
    	// $this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
    	$user = $this->session->userdata('user');
    	$admin = $this->session->userdata('admin');
		$this->db->select(' ms_procurement.id id, ms_procurement.name name,ms_procurement.del del');
		$this->db->where('ms_procurement.del',0);
		$this->db->where('ms_procurement.id',$id);
		$this->db->where('ms_procurement.id_mekanisme!=',1);
		if($this->session->userdata('admin')['id_role']==4){
			$this->db->where('ms_procurement.status_procurement=',1);
		}
		if($this->session->userdata('admin')['id_role']==9){
			$this->db->where('ms_procurement.id_division=',$admin['id_division']);
		}
		$this->db->join('ms_procurement_peserta','ms_procurement_peserta.id_proc=ms_procurement.id','LEFT');
		$this->db->join('ms_vendor','ms_procurement_peserta.id=ms_vendor.id','LEFT');
		
		$this->db->join('ms_procurement_bsb','ms_procurement_bsb.id_proc=ms_procurement.id','LEFT');
		$this->db->join('ms_contract','ms_contract.id_procurement=ms_procurement.id','LEFT');
		
		
		$a = $this->filter->generate_query($this->db->group_by('ms_procurement.id'),$filter);

		$query = $a->get('ms_procurement');	
		
		return $query->result_array();
    }
	function get_contract_progress($id){
   		$arr = $this->db->select('*')->where('id_contract',$id)->where('del',0)->get('tr_progress_kontrak')->result_array();
   		return $arr;
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
   		// echo $this->db->last_query();
		return $arr;
   	}
   	function get_denda_day($id){
   		$data = $this->db->where('id_procurement',$id)->get('tr_denda')->row_array();
   		// echo $this->db->last_query();
   		if($data['end_date']!='' && $data['end_date']!='') {
   			return (ceil(strtotime($data['end_date']) - strtotime($data['start_date']))/86400)+1;
   		}else{
   			return 0;
   		}
   	}
    function get_graph($id,$id_procurement){
    	
   		$data 			= $this->get_contract_progress($id);
   		$data_kontrak 	= $this->get_kontrak($id_procurement);

   		$result 		= array();
   		$total_supposed = $total_realization = 0;
   		$color 			= $this->config->item('color');
   		$basecolor 		= $this->config->item('basecolor');
   		$row 			= 0;

   		foreach( $data as $key => $val){
   			$date = ceil((abs(strtotime($val['end_date'])-strtotime($val['start_date'])))/86400)+1;
   			$denda = ($data_kontrak['contract_price']/1000)*$date;
   		
   			$result['supposed']['data'][$val['id']] = $val['supposed'];
   			$result['supposed']['value'][$val['id']] = $val['step_name'].'( '.$date.' Hari ) '.date('d M Y',strtotime($val['start_date'])).' - '.date('d M Y',strtotime($val['end_date']));

   			$total_supposed+= $val['supposed'];
   			$row++;
   		}

   		$result['supposed']['total'] 	= $total_supposed;
   		$result['max'] 					= ($total_supposed>$total_realization)?$total_supposed:$total_realization;
   		
   		return $result;
   	}
}