<?php 
/**
 * 
 */
class Kontrak_model_test extends CI_Model
{
	public function save_spk($data)
	{
		$cek_progres = $this->db->where('del',0)->where('id_procurement',$data['id_proc'])->get('tr_progress_pengadaan')->row_array();
	}
}