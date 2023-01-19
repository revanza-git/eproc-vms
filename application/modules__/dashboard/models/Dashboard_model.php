<?php
class Dashboard_model extends CI_Model{
	 
	function __construct(){
		parent::__construct();
	}
	
	function get_auction(){
		$sql = "SELECT a.* 
						
				FROM ms_procurement a 
				
				LEFT JOIN ms_procurement_peserta b ON a.id = b.id_proc 
				LEFT JOIN ms_vendor c ON b.id_vendor = c.id
				
				WHERE c.id = ? AND a.auction_date <= ? AND a.auction_date >= ? GROUP BY a.id "; 
		
		$res =  $this->db->query($sql, array($this->session->userdata('user')['id_user'],date("Y-m-d"),date("Y-m-d")));
		// echo $this->db->last_query();
		// var_dump($res);
		return $res;
	}

	function get_pernyataan(){
		$query = $this->db 	->select('*')
							->get('tb_pernyataan');

		return $query->result_array();
	}
	
}