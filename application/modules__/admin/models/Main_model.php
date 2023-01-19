<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Main_model extends CI_Model{

	function __construct(){
		parent::__construct();

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
		if($this->session->userdata('admin')['id_role']==8){
			$query .= " AND a.need_approve = 1 ";
		}
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