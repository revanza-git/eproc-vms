<?php
/**
 * 
 */
class Cron_model extends CI_Model
{
	public function getData()
	{
		return $this->db->where('del',0)->get('ms_fppbj')->result_array();
	}

	public function getEmail($id)
	{
		//if ($id == 1) {
			$query = $this->db->where('id_division',$id)->get('ms_user')->result_array();
		/*} else {
			$query = $this->db->where_in('id_division',array($id,1))->get('ms_user')->result_array();
		}*/
		return $query;
	}
}