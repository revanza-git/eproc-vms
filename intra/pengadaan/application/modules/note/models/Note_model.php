<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Note_model extends CI_Model{

	
	function save_note($data){
		$_param = array();
		$sql = $this->db->insert('tr_note',array(
								'id_vendor'=>$data['id_vendor'],
								'value'=>$data['value'],
								'entry_stamp'=>$data['entry_stamp']
								));
		return $sql;
	}

	

	function get_note($id_vendor){
		$query = 	"	SELECT
							*
						FROM
							tr_note a
						WHERE
								a.id_vendor = ?
							AND a.is_active = ?
					";
		$result = $this->db->query($query,array($id_vendor,1));
		return $result->result_array();
	}
	function close($id){
		return $this->db
			->where('id',$id)
			->update('tr_note',array('is_active'=>0));
	}
}