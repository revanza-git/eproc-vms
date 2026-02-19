<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Main_model extends CI_Model{

	function __construct(){
		parent::__construct();

	}

	public function to_app($id)
	{
		$query = "	SELECT
						a.*
						-- b.name role_name
						-- c.name division
					FROM
						ms_admin a
					-- JOIN
					-- 	tb_role b ON b.id=a.id_role
					-- JOIN
					-- 	tb_division c ON c.id=a.id_division
					WHERE
						a.id = ?
		"; 

		$query = $this->db->query($query,array($id))->row_array();
		// echo $this->eproc_db->last_query();die;
		return $query;
	}

	function get_daftar_tunggu_chart(){

		$query = " 	SELECT 
						*

					FROM 
						ms_vendor a

					WHERE 
						a.vendor_status = 1
						AND a.is_active = 1
					";
		// if($this->session->userdata('admin')['id_role']==8){
			$query .= " AND a.need_approve = 1 ";
		// }
		$query .=	" ORDER BY 
						a.edit_stamp DESC
						
					";
		$result = $this->db->query($query);
		return $result;

	}

	function daftar_hitam_chart(){

		$query = " 	SELECT 
						*

					FROM 
						ms_vendor a

					LEFT JOIN 
						tr_blacklist b ON b.id_vendor = a.id

					WHERE 
						a.is_active = 0 
						AND b.del = 0
						AND b.id_blacklist = 2
						AND a.del = 0";
		//if($this->session->userdata('admin')['id_role']==8){
			//$query .= " AND b.need_approve = 1 ";
		//}
		$query .=	" ORDER BY 
						b.start_date DESC
					";
		$result = $this->db->query($query);
		return $result;

	}

	function daftar_merah_chart(){

		$query = " 	SELECT 
						*

					FROM 
						ms_vendor a

					LEFT JOIN 
						tr_blacklist b ON b.id_vendor = a.id

					WHERE 
						a.is_active = 0 
						AND b.del = 0
						AND b.id_blacklist = 1
						AND a.del = 0";
		//if($this->session->userdata('admin')['id_role']==8){
			//$query .= " AND b.need_approve = 1 ";
		//}
		$query .=	" ORDER BY 
						b.start_date DESC
					";
		$result = $this->db->query($query);
		return $result;

	}

	function dpt_chart(){
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
		
		$query = " 	SELECT 
						*

					FROM 
						ms_vendor a

					LEFT JOIN 
						tr_dpt b ON b.id_vendor = a.id 

					WHERE 
						a.is_active = 1
						AND a.vendor_status = 2
						AND a.del = 0
					
					GROUP BY
						a.id 

					ORDER BY 
						b.start_date DESC


					";
		$result = $this->db->query($query);
		return $result;

	}
}