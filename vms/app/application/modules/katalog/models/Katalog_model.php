<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Katalog_model extends CI_Model{
	public $CI;
	function __construct(){
		parent::__construct();
		$this->field_master = array(
								'id_barang',
								'nama',
								'remark',
								'gambar_barang',
								'category',
								'id_kurs',
								'for',
								'entry_stamp'
							);
		$this->field_harga = array(
								'id_material',
								'price',
								'date',
								'entry_stamp'
							);
		$this->CI =& get_instance();
        $this->CI->load->model('vendor_model','vm');
	}

	function get_katalog($category, $search='', $sort='', $page='', $per_page='',$is_page=FALSE,$filter=array()){
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

		$this->db->select('*, tb_kurs.symbol kurs_name, ms_material.id as id_materials, (select price from tr_material_price WHERE id_material = id_materials ORDER BY date desc LIMIT 0,1)as last_price')
		->like('nama',$search,'both')
		->where('category',$category)
		->join('tr_material_price', 'tr_material_price.id_material = ms_material.id', "LEFT")
		->join('tb_kurs', 'tb_kurs.id = ms_material.id_kurs', "LEFT")
		->order_by('ms_material.nama','asc');

		

		$a = $this->filter->generate_query($this->db->group_by('ms_material.id'),$filter);
		

		if($this->input->get('sort')&&$this->input->get('by')){
			$a->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){

			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;

			$a->limit($per_page, $per_page*($cur_page - 1));
		}

		$this->db->where('ms_material.del',0);
		$query = $a->get('ms_material');

		return $query->result_array();
	}

	function get_barang_compare($compare){

		if (!empty($compare)) {
			$query = $this->db->where_in('id',$compare)->get('ms_material');
			// echo $this->db->last_query();
			return $query->result_array();
		}else{
			return FALSE;
		}
		
	}

	function search_katalog(){
		$kurs = $result = array();
		if($this->input->post('id_procurement')){
			$id_procurement = $this->input->post('id_procurement');
			$query_kurs = "	SELECT 
								id_kurs
							FROM ms_procurement_kurs
							WHERE 
								id_procurement = ?";
			$query_kurs = $this->db->query($query_kurs,array($id_procurement));
			foreach ($query_kurs->result() as $value) {
				$kurs[] = $value->id_kurs;
			}
		}
		
		$this->db  ->select('id, nama,(SELECT AVG(price) FROM tr_material_price WHERE id_material = ms_material.id) as average,id_kurs')
							->like('nama',$this->input->post('term'),'both')
							->where('category',$this->input->post('cat'))
							->where('del',0)
							->group_by('id');
		if(count($kurs)>0){
			$this->db->where_in('id_kurs',$kurs);
		}
		$query = $this->db->get('ms_material');
		foreach($query->result_array() as $key => $value){
			$result[$value['id']]['id'] = $value['id'];
			$result[$value['id']]['name'] = $value['nama'];
			$result[$value['id']]['average'] = $value['average'];
			$result[$value['id']]['id_kurs'] = $value['id_kurs'];
		}
		if($this->input->post('id_procurement')&&count($kurs)<=0){
			$result = array();
		}
		// echo $this->db->last_query();
		return $result;
	}
	function save_barang($data){
		$data['id_barang'] = isset($data['id_barang']) ? $data['id_barang'] : 0;
		$_param = array();
		$sql = "INSERT INTO ms_material (
							`id_barang`,
							`nama`,
								`remark`,
								`gambar_barang`,
								`category`,
								`id_kurs`,
								`for`,
								`entry_stamp`
							) 
				VALUES (?,?,?,?,?,?,?,?) ";
		
		
		foreach($this->field_master as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);

		$id = $this->db->insert_id();
		
		return $id;
	}

	function get_data_barang($id){
		return $this->db
				->join('tb_kurs','tb_kurs.id = ms_material.id_kurs','LEFT')
				->where('ms_material.id',$id)->get('ms_material')->row_array();
	}

	function get_harga($id_arr){
		$this->db->select('*,tr_material_price.id id, ms_vendor.name vendor_name')
		->join('ms_vendor','tr_material_price.id_vendor = ms_vendor.id','LEFT')
		->where('tr_material_price.del',0)
		->where_in('tr_material_price.id_material',$id_arr);

		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		$query = $this->db->get('tr_material_price');

		
		$return =  $query->result_array();
		
		return $return;
	}
	
	function get_harga_barang($id){
		return $this->db->where('id',$id)->get('tr_material_price')->row_array();
	}
	function edit_barang($data,$id){
		$this->db->where('id',$id);
		$result = $this->db->update('ms_material',$data);
		if($result)return $id;
	}
	function edit_harga_barang($data,$id){
		$this->db->where('id',$id);
		$result = $this->db->update('tr_material_price',$data);
		if($result)return $id;
	}
	function delete_barang($id){
		$this->db->where('id',$id);
		return $this->db->update('ms_material',array('del'=>1));
	}
	function delete_harga($id){
		$this->db->where('id',$id);
		return $this->db->update('tr_material_price',array('del'=>1));
	}
	function save_harga_barang($id,$data){
		$_param = array();
		$sql = "INSERT INTO tr_material_price (
								`id_material`,
								`price`,
								`date`,
								`entry_stamp`
							) 
				VALUES (?,?,?,?) ";
		
		
		foreach($this->field_harga as $_param) $param[$_param] = $data[$_param];
		
		$this->db->query($sql, $param);

		$id = $this->db->insert_id();
		
		return $id;
	}
	function data_chart($id){
		$data_label = $this->get_data_barang($id);
		$raw_data 	= $this->db->select(' YEAR(`date`) as years,(SELECT AVG(`price`) FROM `tr_material_price` WHERE id_material = '.$id.'  AND del=0 AND YEAR(`date`)=years) as avg_year')
							->where('id_material',$id)
							->where('tr_material_price.del',0)
							->group_by('years')
							->get('tr_material_price');

		return $raw_data->result_array();
	}


	function get_data_compare($id){
		$query = $this->db->select('ms_material.*, tb_kurs.symbol,tb_kurs.name kurs_name ')->where_in('ms_material.id',$id)->join('tb_kurs','tb_kurs.id = ms_material.id_kurs','LEFT')->get('ms_material')->result_array();

		return $query;
	}

	function data_chart_compare($id){
		$data_label = $this->get_data_barang($id);
		$raw_data 	= $this->db->select(' YEAR(`date`) as years,(SELECT AVG(`price`) FROM `tr_material_price` WHERE id_material = '.$id.' AND del=0 AND YEAR(`date`)=years) as avg_year')
							->where('id_material',$id)
							->group_by('years')
							->order_by('YEAR(`date`)', 'ASC')
							->get('tr_material_price');
		return $raw_data->result_array();
	}

}
