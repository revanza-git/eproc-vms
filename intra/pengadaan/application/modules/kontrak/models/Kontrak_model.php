<?php
/**
 * 
 */
class Kontrak_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function get_contract_list($id,$search='', $sort='', $page='', $per_page='',$is_page=FALSE)
	{
		$result = $this->db->select('ms_contract.*,ms_contract.id id,ms_vendor.name vendor_name')->where('ms_contract.id_procurement',$id)
   		->join('ms_procurement','ms_procurement.id=ms_contract.id_procurement')
   		->join('ms_vendor','ms_vendor.id=ms_contract.id_vendor')
   		->where('ms_contract.del',0);
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
   		return $this->db->get('ms_contract')->result_array();
	}

	public function get_contract_by_procurement($id_procurement)
	{
		return $this->db->where('del',0)->where('id_procurement',$id_procurement)->get('ms_contract')->row_array();
	}

	public function get_amandemen_by_procurement($id_procurement)
	{
		return $this->db->where('del',0)->where('id_proc',$id_procurement)->get('ms_amandemen')->row_array();
	}

	function edit_data($data,$id){
		
		$this->db->where('id',$id);
		$res = $this->db->update('ms_procurement',$data);

		
		return $res;
	}

	public function save_kontrak($data){
		echo 'data : '; print_r($data);
		$this->db->insert('ms_contract',$data);
		$id = $this->db->insert_id();
		echo 'data id : '; print_r($id);
		$this->db->where('id',$data['id_procurement'])->update('ms_procurement',array('status_procurement'=>2));
		$this->db->insert('tr_progress_kontrak',
												array(
													'id_procurement' 		=> $data['id_procurement'],
													'id_contract'	 		=> $id,
													'id_spk' => 1,
													'id_bast' => 1,
													'id_amandemen' => 1,
													'denda' => 0,

													'step_name'		 		=> 'Jangka Waktu Pelaksanaan Pekerjaan',
													'solid_start_date'		=> $data['start_contract'],
													'start_date'			=> $data['start_contract'],
													'end_date'				=> $data['end_contract'],
													'supposed'				=> get_range_date($data['end_contract'],$data['start_contract']),
													'type'					=> 2,
													'parent' => 0,
													'is_kontrak'			=> 1,
													
												)
											);
		
		return $id;
	}

	public function get_data_kontrak($id)
	{
		return $this->db->where('id',$id)->get('ms_contract')->row_array();
	}

	public function edit_kontrak($data,$id){
				
		$this->db->where('id',$id);		

		$result = $this->db->update('ms_contract',$data);

		$get_kontrak = $this->db->where('id',$id)->get('ms_contract')->row_array();

		$type1 = $this->db->where('type',1)->where('id_procurement',$get_kontrak['id_procurement'])->get('tr_progress_kontrak')->num_rows();
		$type2 = $this->db->where('type',2)->where('id_procurement',$get_kontrak['id_procurement'])->get('tr_progress_kontrak')->num_rows();

		if($type2 > 0){
			$this->db->where('type',2)->where('id_procurement',$get_kontrak['id_procurement'])
										->update('tr_progress_kontrak',
												array(
													'id_procurement' 		=> $get_kontrak['id_procurement'],
													'start_date'			=> $data['start_contract'],
													'end_date'				=> $data['end_contract'],
													'supposed'				=> ceil((abs(strtotime($data['end_contract'])-strtotime($data['start_contract']))+1)/86400),			
													'solid_start_date'		=> $data['start_contract']
												)
											);
		}else{
			$this->db->insert('tr_progress_kontrak',
												array(
													'id_procurement' 		=> $get_kontrak['id_procurement'],
													'id_contract'	 		=> $id,
													'step_name'		 		=> 'Jangka Waktu Pelaksanaan Pekerjaan',
													'start_date'			=> $data['start_contract'],
													'end_date'				=> $data['end_contract'],
													'supposed'				=> ceil((abs(strtotime($data['end_contract'])-strtotime($data['start_contract']))+1)/86400),
													'type'					=> 2,			
													'solid_start_date'		=> $data['start_contract']
												)
											);
		}

		if($result)return $id;
	}

	public function countContract($id_procurement)
	{
		return count($this->db->where('id_procurement',$id_procurement)->where('del','0')->get('ms_contract')->result_array());
	}

	public function hapus_kontrak($id)
	{
		$arr = array(
			'edit_stamp' => date('Y-m-d H:i:s'),
			'del' => 1
		);

		$this->db->where('id_contract',$id)->delete('tr_progress_kontrak');

		return $this->db->where('id',$id)->update('ms_contract',$arr);
	}

	public function get_spk_list($id,$search='', $sort='', $page='', $per_page='',$is_page=FALSE)
	{
		$result = $this->db->select('ms_spk.*,ms_spk.id id')->where('ms_spk.id_proc',$id)
   		->join('ms_procurement','ms_procurement.id=ms_spk.id_proc')
   		->where('ms_spk.del',0);
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
   		return $this->db->get('ms_spk')->result_array();
	}

	public function save_spk($data)
	{
		// echo 'data'; print_r($data);

		$this->db->insert('ms_spk',$data);
		$id_spk = $this->db->insert_id();

		$this->db->where('del',0)->where('id_procurement',$data['id_proc'])->where('is_kontrak',1)->update('tr_progress_kontrak',array('del'=>1,'edit_stamp'=>date('Y-m-d H:i:s')));

		$query_spk_before = "	SELECT
									*
								FROM
									ms_spk
								WHERE
									del = 0 AND id_proc = ? AND id != ?
								ORDER BY id desc ";

		$spk_before = $this->db->query($query_spk_before,array($data['id_proc'],$id_spk))->row_array();

		$data_kontrak = $this->db->where('del',0)->where('id_procurement',$data['id_proc'])->get('ms_contract')->row_array();

		if (!empty($spk_before)) {
			if (strtotime($spk_before['end_date']) > strtotime($data['start_date'])) {
				$range = get_range_date($spk_before['end_date'],$data['start_date']);
			} else {
				$range = get_range_date($data['start_date'],$spk_before['end_date']);
			}
		}

		$this->db->insert('tr_progress_kontrak',
												array(
													'id_procurement' 		=> $data['id_proc'],
													'id_contract'	 		=> $data_kontrak['id'],
													'step_name'		 		=> 'Jangka Waktu Pelaksanaan Pekerjaan',
													'start_date'			=> $data['start_date'],
													'end_date'				=> $data['end_date'],
													'supposed'				=> get_range_date($data['end_date'],$data['start_date']),
													'type'					=> 2,
													'id_spk'				=> $id_spk,
													'solid_start_date'		=> $data['start_date'],
													'denda'					=> 0,
												)
											);


		$data['id_spk'] = $id_spk;
		// unset($data['start_date']);
		// $data['start_date'] = $data['end_date'];
		$data['bast_type'] = 'bast_tahapan';
		$this->db->insert('ms_bast',$data);
		$id_bast = $this->db->insert_id();

		return $this->db->insert('tr_progress_kontrak',
												array(
													'id_procurement' 		=> $data['id_proc'],
													'id_contract'	 		=> $data_kontrak['id'],
													'step_name'		 		=> 'BAST Tahapan',
													'start_date'			=> $data['start_date'],
													'end_date'				=> $data['end_date'],
													'supposed'				=> get_range_date($data['end_date'],$data['start_date']),
													'type'					=> 5,
													'id_spk'				=> $id_spk,
													'id_bast'				=> $id_bast,
													'solid_start_date'		=> $data['start_date'],
													'denda'					=> 0
												)
											);
	}

	public function get_data_spk($id)
	{
		return $this->db->where('id',$id)->get('ms_spk')->row_array();
	}

	public function edit_spk($data,$id)
	{
		$this->db->where('id',$id)->update('ms_spk',$data);

		$spk = $this->get_data_spk($id);

		$cek_jarak = $this->db->where('del',0)->where('id_spk',$id)->where('type',1)->get('tr_progress_kontrak')->row_array();

		// print_r($cek_jarak);die;

		// print_r($spk);die;
		$data_kontrak = $this->db->where('del',0)->where('id_procurement',$spk['id_proc'])->get('ms_contract')->row_array();

		// print_r($data_kontrak);die;

		$type2 = $this->db->where('type',2)->where('id_procurement',$data_kontrak['id_procurement'])->get('tr_progress_kontrak')->num_rows();

		if($type2 > 0){
			$this->db->where('type',2)->where('id_spk',$id)
										->update('tr_progress_kontrak',
												array(
													'id_procurement' 		=> $data_kontrak['id_procurement'],
													'id_contract'	 		=> $data_kontrak['id'],
													'start_date'			=> $data['start_date'],
													'end_date'				=> $data['end_date'],
													'supposed'				=> get_range_date($data['end_date'],$data['start_date']),
													'id_spk'				=> $id,
													'solid_start_date'		=> $data['start_date']
												)
											);
		}

		$u_d = array(
			'start_date'	=>	$data['start_date'],
			'edit_stamp'	=>	date('Y-m-d H:i:s')
		);

		$up_denda = $this->db->where('id_spk',$id)->where('type',4)->update('tr_progress_kontrak',$u_d);

		$this->db->where('id_spk',$id)->where('type',5)->update('tr_progress_kontrak',
												array(
													'start_date'			=> $data['start_date'],
													'end_date'				=> $data['end_date'],
													'supposed'				=> ceil((abs(strtotime($data['end_date'])-strtotime($data['start_date']))+1)/86400),								
													'solid_start_date'		=> $data['start_date']
												)
											);

		return $this->db->where('id_spk',$id)->update('ms_bast', $data);
	}

	public function hapus_spk($id)
	{
		$arr = array(
			'edit_stamp' => date('Y-m-d H:i:s'),
			'del' => 1
		);

		$this->db->where('id',$id)->update('ms_spk',$arr);
		$this->db->where('id_spk',$id)->delete('tr_progress_kontrak');
		$this->db->where('id_spk',$id)->where('type',5)->delete('tr_progress_kontrak');

		$this->db->where('id_spk',$id)->where('type',5)->delete('tr_progress_kontrak');

		$spk = $this->db->where('id',$id)->get('ms_spk')->row_array();

		$last_spk = $this->db->where('id_procurement',$spk['id_proc'])->where('id_spk !=',null)->get('tr_progress_kontrak');

		if ($last_spk->num_rows() == 0) {
			$this->db->where('is_kontrak',1)->update('tr_progress_kontrak',array('del'=>0));
		}
		// $this->db->where('parent',$id)->where('type',1)->delete('tr_progress_kontrak');
		return $this->db->where('id_spk',$id)->update('ms_bast',$arr);
	}

	public function get_amandemen_list($id,$search='', $sort='', $page='', $per_page='',$is_page=FALSE)
	{
		$result = $this->db->select('ms_amandemen.*,ms_amandemen.id id')->where('ms_amandemen.id_proc',$id)
   		->join('ms_procurement','ms_procurement.id=ms_amandemen.id_proc')
   		->where('ms_amandemen.del',0);
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
   		return $this->db->get('ms_amandemen')->result_array();
	}

	public function save_amandemen($data)
	{
		echo 'amandemen file'; print_r($data);
		$this->db->insert('ms_amandemen',$data);

		$id_amandemen = $this->db->insert_id();

		$cek_prog = $this->db->where('del',0)->where('id_procurement',$data['id_proc'])->where('id_amandemen',$id_amandemen)->get('tr_progress_kontrak')->result_array();

		$supposed = get_range_date($data['end_date'],$data['start_date']);

		$data_kontrak = $this->db->where('del',0)->where('id_procurement',$data['id_proc'])->get('ms_contract')->row_array();

		if (count($cek_prog) > 0) {
			echo 'update pr kontrak';
			$arr = array(
				'start_date'		=> $data['start_date'],
				'end_date'			=> $data['end_date'],
				'supposed'			=> $supposed,
				'edit_stamp'		=> date('Y-m-d H:i:s')
			);

			$return = $this->db->where('id_amandemen',$id_amandemen)->update('tr_progress_kontrak',$arr);
		} else {
			echo 'insert ama';
			$arr = array(
				'id_procurement'	=> $data['id_proc'],
				'id_contract'		=> $data_kontrak['id'],
				'id_amandemen'		=> $id_amandemen,
				'step_name'			=> 'Amandemen',
				'start_date'		=> $data['start_date'],
				'end_date'			=> $data['end_date'],
				'supposed'			=> $supposed,
				'entry_stamp'		=> date('Y-m-d H:i:s'),
				'type'				=> 3,
				'denda'				=> 0,
				'solid_start_date'	=> $data['start_date'],
			);

			$return = $this->db->insert('tr_progress_kontrak',$arr);
		}

		return $return;
	}

	public function get_data_amandemen($id)
	{
		return $this->db->where('id',$id)->get('ms_amandemen')->row_array();
	}

	public function edit_amandemen($data,$id)
	{
		// print_r($data);die;

		$this->db->where('id',$id)->update('ms_amandemen', $data);

		$cek_prog = $this->db->where('del',0)->where('id_amandemen',$id)->get('tr_progress_kontrak')->result_array();

		$supposed = 0;

		$data_kontrak = $this->db->where('id_procurement',$data['id_proc'])->get('ms_contract')->row_array();

		if (!empty($cek_prog)) {

			$arr = array(
				'start_date'		=> $data['start_date'],
				'end_date'			=> $data['end_date'],
				'supposed'			=> $supposed,
				'edit_stamp'		=> date('Y-m-d H:i:s')
			);

			$return = $this->db->where('id_amandemen',$id)->update('tr_progress_kontrak',$arr);
		} else {
			
			$arr = array(
				'id_procurement'	=> $data['id_proc'],
				'id_contract'		=> $data_kontrak['id'],
				'id_amandemen'		=> $id,
				'step_name'			=> 'Amandemen',
				'start_date'		=> $data['start_date'],
				'end_date'			=> $data['end_date'],
				'supposed'			=> $supposed,
				'entry_stamp'		=> date('Y-m-d H:i:s'),
				'type'				=> 3
			);

			$return = $this->db->insert('tr_progress_kontrak',$arr);
		}

		return $return;
	}

	public function hapus_amandemen($id)
	{
		$arr = array(
			'edit_stamp' => date('Y-m-d H:i:s'),
			'del' => 1
		);
		
		$this->db->where('id_amandemen',$id)->delete('tr_progress_kontrak');

		return $this->db->where('id',$id)->update('ms_amandemen',$arr);
	}

	public function get_bast_list($id,$search='', $sort='', $page='', $per_page='',$is_page=FALSE)
	{
		$result = $this->db->select('ms_bast.*,ms_bast.id id')->where('ms_bast.id_proc',$id)
   		->join('ms_procurement','ms_procurement.id=ms_bast.id_proc')
   		->where('ms_bast.del',0);
		
		if($this->input->get('sort')&&$this->input->get('by')){
			$this->db->order_by($this->input->get('by'), $this->input->get('sort')); 
		}
		if($is_page){
			$cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
			$this->db->limit($per_page, $per_page*($cur_page - 1));
		}
   		return $this->db->get('ms_bast')->result_array();
	}

	public function save_bast($data)
	{
		// echo 'data bast'; print_r($data);
		$data['del'] = 0;
		$this->db->insert('ms_bast',$data);

		$bast_type = ($data['bast_type'] == 'bast_tahapan') ? 'BAST Tahapan' : 'BAST Final';

		$id_bast = $this->db->insert_id();


		$cek_prog = $this->db->where('del',0)->where('id_procurement',$data['id_proc'])->where('id_bast',$id_bast)->get('tr_progress_kontrak')->row_array();

		$data_kontrak = $this->db->where('del',0)->where('id_procurement',$data['id_proc'])->get('ms_contract')->row_array();

		$query_bast_before = " 	SELECT
									*
								FROM
									ms_bast
								WHERE
									del = 0 AND id_proc = ? AND id != ?
								order by id desc";

		$bast_before = $this->db->query($query_bast_before,array($data['id_proc'],$id_bast))->row_array();

		$supposed = 0;

		if ($data['bast_type'] == 'bast_tahapan') {
			// echo 'bast tahapan';
			$spk = $this->get_spk_date($data['id_proc']);

			if ($spk['end_date'] == '' || $spk['start_date'] == '') {
				// echo "string 1";die;
				$amandemen = $this->get_amandemen_date($data['id_proc']);
				// print_r($amandemen);die;
				if (!empty($amandemen)) {
					$end_date = $amandemen['end_date'];
				} else {
					$contract = $this->get_contract_date($data['id_proc']);
					$end_date = $contract['end_date'];
				}
				
			} else {
				$end_date = $spk['end_date'];
			}
		} else {
			// echo 'bast final';
			$amandemen = $this->get_amandemen_date($data['id_proc']);
			// print_r($amandemen);die;
			if ($amandemen['start_date'] != '' && $amandemen['end_date'] != '') {
				$end_date = $amandemen['end_date'];
			} else {
				$contract = $this->get_contract_date($data['id_proc']);
				$end_date = $contract['end_date'];
			}
		}		
		
		if (strtotime($data['end_date']) > strtotime($end_date)) {

			// echo "string 1";
			
			$hari 		  		  = get_range_date($data['end_date'],$end_date);
			// echo $hari;die;
			$total_hari			  = ($hari>50)?50:$hari;
			// echo $total_hari;die;
			// echo $data_kontrak['contract_price'];die;
			$denda		  		  = $total_hari/1000 * $data_kontrak['contract_price'];
			// echo $denda;die;
			$max_denda		  	  = 50/100 * $data_kontrak['contract_price'];
			$denda				  = ($denda>$max_denda) ? $max_denda : $denda;

			$arr = array(
				'id_procurement' => $data['id_proc'],
				'start_date'	 => $end_date,
				'end_date'	 	 => $data['end_date'],
				'denda'			 => $denda,
				'id_bast'		 => $id_bast,
				'entry_stamp'	 => date('Y-m-d H:i:s')
			);

			// print_r($arr);die;

			$this->db->insert('tr_denda',$arr);


			// if (!empty($bast_before)) {
			// 		// echo "string 2.1";die;
			// 		$s_d = strtotime($bast_before['end_date']);
			// 		$e_d = strtotime($data['end_date']);

			// 		if ($s_d > $e_d) {
			// 			$range = get_range_date($bast_before['end_date'],$data['end_date']);
			// 		} else {
			// 			$range = get_range_date($data['end_date'],$bast_before['end_date']);
			// 		}

			// 		$arr__ = array(
			// 			'id_procurement'	=> $data['id_proc'],
			// 			'id_contract'		=> $data_kontrak['id'],
			// 			'id_bast'			=> $id_bast,
			// 			'step_name'			=> 'Jarak BAST '.$bast_before['no'].' dengan BAST '.$data['no'],
			// 			'start_date'	 	=> $end_date,
			// 			'end_date'	 	 	=> $data['end_date'],
			// 			'supposed'			=> $range,
			// 			'entry_stamp'		=> date('Y-m-d H:i:s'),
			// 			'type'				=> 1
			// 		);

			// 		$this->db->insert('tr_progress_kontrak',$arr__);
			// }

			$arr = array(
				'id_procurement'	=> $data['id_proc'],
				'id_contract'		=> $data_kontrak['id'],
				'id_bast'			=> $id_bast,
				'step_name'			=> 'Denda',
				'start_date'	 	=> $end_date,
				'end_date'	 	 	=> $data['end_date'],
				'supposed'			=> get_range_date($data['end_date'],$end_date),
				'entry_stamp'		=> date('Y-m-d H:i:s'),
				'type'				=> 4,
				'solid_start_date'	=> $end_date, 
				'denda'				=> 0
			);

			$this->db->insert('tr_progress_kontrak',$arr);

			$arr_ = array(
					'id_procurement'	=> $data['id_proc'],
					'id_contract'		=> $data_kontrak['id'],
					'id_bast'			=> $id_bast,
					'step_name'			=> $bast_type,
					'start_date'		=> $data['end_date'],
					'end_date'			=> $data['end_date'],
					'supposed'			=> $supposed,
					'entry_stamp'		=> date('Y-m-d H:i:s'),
					'type'				=> 5,
					'solid_start_date'	=> $data['end_date'],
					'denda'				=> 0
				);

			$return = $this->db->insert('tr_progress_kontrak',$arr_);

		} else {
			// echo "string 2";
				// if (!empty($bast_before)) {
				// 	// echo "string 2.1";die;
				// 	$s_d = strtotime($bast_before['end_date']);
				// 	$e_d = strtotime($data['end_date']);

				// 	if ($s_d > $e_d) {
				// 		$range = get_range_date($bast_before['end_date'],$data['end_date']);
				// 	} else {
				// 		$range = get_range_date($data['end_date'],$bast_before['end_date']);
				// 	}

				// 	$arr = array(
				// 		'id_procurement'	=> $data['id_proc'],
				// 		'id_contract'		=> $data_kontrak['id'],
				// 		'id_bast'			=> $id_bast,
				// 		'step_name'			=> 'Jarak BAST '.$bast_before['no'].' dengan BAST '.$data['no'],
				// 		'start_date'		=> $data['end_date'],
				// 		'end_date'			=> $bast_before['end_date'],
				// 		'supposed'			=> $range,
				// 		'entry_stamp'		=> date('Y-m-d H:i:s'),
				// 		'type'				=> 1
				// 	);

				// 	$this->db->insert('tr_progress_kontrak',$arr);
				// }

				// if (!empty($cek_prog)) {

				// 	// echo "string 3";die;

				// 	$arr = array(
				// 		'start_date'		=> $data['end_date'],
				// 		'end_date'			=> $data['end_date'],
				// 		'supposed'			=> $supposed,
				// 		'edit_stamp'		=> date('Y-m-d H:i:s')
				// 	);

				// 	$return = $this->db->where('id_bast',$id_bast)->update('tr_progress_kontrak',$arr);
				// } else {

					// echo "string 4";die;
					
					$arr = array(
						'id_procurement'	=> $data['id_proc'],
						'id_contract'		=> $data_kontrak['id'],
						'id_bast'			=> $id_bast,
						'step_name'			=> $bast_type,
						'start_date'		=> $data['end_date'],
						'end_date'			=> $data['end_date'],
						'supposed'			=> $supposed,
						'entry_stamp'		=> date('Y-m-d H:i:s'),
						'type'				=> 5,
						'solid_start_date'		=> $data['end_date'],
						'denda'				=> 0
					);

					$return = $this->db->insert('tr_progress_kontrak',$arr);
				// }
		}
		
		// echo 'return'; print_r($return);
		return $return;
	}

	public function get_spk_date($id_procurement)
	{
		$this->db
   				->select('max(DATE(`end_date`)) as end_date, min(DATE(`start_date`)) as start_date')
   				->where('id_proc',$id_procurement)
   				->where('del',0);
   		
   		$res =  $this->db->get('ms_spk')->row_array();

   		return $res;
	}

	public function get_spk_date_by_id($id_spk)
	{
		$this->db->where('id',$id_spk)
   				->where('del',0);
   		
   		$res =  $this->db->get('ms_spk')->row_array();

   		return $res;
	}

	public function get_contract_date($id_procurement)
	{
		$this->db
   				->select('max(DATE(`end_contract`)) as end_date, min(DATE(`start_contract`)) as start_date')
   				->where('id_procurement',$id_procurement)
   				->where('del',0);
   		
   		$res =  $this->db->get('ms_contract')->row_array();

   		return $res;
	}

	public function get_amandemen_date($id_procurement)
	{
		$this->db
   				->select('max(DATE(`end_date`)) as end_date, min(DATE(`start_date`)) as start_date')
   				->where('id_proc',$id_procurement)
   				->where('del',0);
   		
   		$res =  $this->db->get('ms_amandemen')->row_array();

   		return $res;
	}

	public function get_data_bast($id)
	{
		return $this->db->where('id',$id)->get('ms_bast')->row_array();
	}

	public function edit_bast($data,$id,$id_spk=0)
	{
		$get_spk = $this->db->where('id',$id_spk)->where('del',0)->get('ms_spk')->row_array();

		// print_r($get_spk);die;

		if (!empty($get_spk)) {
			$data['start_date'] = $get_spk['start_date'];
		}

		// print_r($data);die;

		$this->db->where('id',$id)->update('ms_bast', $data);

		$bast_type = ($data['bast_type'] == 'bast_tahapan') ? 'BAST Tahapan' : 'BAST Final';

		$cek_prog = $this->db->where('del',0)->where('id_bast',$id)->get('tr_progress_kontrak')->row_array();

		// print_r($cek_prog);die;
		// echo $cek_prog['id_procurement'];die;

		$supposed = 0;

		$data_kontrak = $this->db->where('del',0)->where('id_procurement',$cek_prog['id_procurement'])->get('ms_contract')->row_array();

		// print_r($data_kontrak);die;

		if ($data['bast_type'] == 'bast_tahapan') {
			$spk = $this->get_spk_date_by_id($id_spk);

			if ($spk['end_date'] == '' || $spk['start_date'] == '') {
				// echo "string 1";die;
				$amandemen = $this->get_amandemen_date($data['id_proc']);
				// print_r($amandemen);die;
				if (!empty($amandemen)) {
					$end_date = $amandemen['end_date'];
				} else {
					$contract = $this->get_contract_date($data['id_proc']);
					$end_date = $contract['end_date'];
				}
				
			} else {
				// echo "string 2";die;
				$end_date = $spk['end_date'];
			}
		} else {
			$amandemen = $this->get_amandemen_date($cek_prog['id_procurement']);
			// print_r($amandemen);die;
			if (!empty($amandemen)) {
				$end_date = $amandemen['end_date'];
			} else {
				$contract = $this->get_contract_date($cek_prog['id_procurement']);
				$end_date = $contract['end_date'];
			}
		}	

		if ($id_spk != '0') {

			// echo $id_spk." string 1";die;
			// echo $end_date;die;
			// print_r($spk);die;

			if (strtotime($data['end_date']) > strtotime($end_date)) {
				// echo "string 1.1";die;
				$hari 		  		  = get_range_date($data['end_date'],$spk['end_date']);
				// echo $hari;die;
				$total_hari			  = ($hari>50)?50:$hari;
				// echo $data_kontrak['contract_price'];die;
				$denda		  		  = $total_hari/1000 * $data_kontrak['contract_price'];
				// echo $denda;die;
				$max_denda		  	  = 50/100 * $data_kontrak['contract_price'];
				$denda				  = ($denda>$max_denda) ? $max_denda : $denda;

				$arr = array(
					'id_procurement' => $spk['id_proc'],
					'start_date'	 => $spk['end_date'],
					'end_date'	 	 => $data['end_date'],
					'denda'			 => $denda,
					'id_spk'		 => $spk['id'],
					'entry_stamp'	 => date('Y-m-d H:i:s')
				);

				// print_r($arr);die;

				$denda_before = $this->db->where('id_spk',$id_spk)->where('type',4)->get('tr_progress_kontrak')->result_array();

				if (count($denda_before) > 0) {
					$this->db->where('id_spk',$id_spk)->where('type',4)->update('tr_denda',$arr);
				} else {
					$this->db->insert('tr_denda',$arr);	
				}

				$arr['id_contract'] = $data_kontrak['id'];
				$arr['step_name'] 	= 'Denda';
				$arr['type'] 		= 4;
				$arr['solid_start_date'] = $spk['start_date'];

				if (count($denda_before) > 0) {
					$return = $this->db->where('id_spk',$id_spk)->where('type',4)->update('tr_progress_kontrak',$arr);
				} else {
					$return = $this->db->insert('tr_progress_kontrak',$arr);	
				}
			} else {
				// echo "string 1.2";die;
				$this->db->where('id_spk',$spk['id'])->delete('tr_denda');
				$this->db->where('id_spk',$spk['id'])->where('type',4)->delete('tr_progress_kontrak');
			}
		}else{
			// echo "string 3";die;
			// $spk = $this->get_spk_date($cek_prog['id_procurement']);

			// if ($spk['end_date'] == '' || $spk['start_date'] == '') {
			// 	$contract = $this->get_contract_date($cek_prog['id_procurement']);
			// 	$end_date = $contract['end_date'];
			// } else {
			// 	$end_date = $spk['end_date'];
			// }
			// echo $end_date;die;
			if (strtotime($data['end_date']) > strtotime($end_date)) {
				// echo "string 3.1";die;
			
				$hari 		  		  = get_range_date($data['end_date'],$spk['end_date']);
				// echo $hari;die;
				$total_hari			  = ($hari>50)?50:$hari;
				// echo $data_kontrak['contract_price'];die;
				$denda		  		  = $total_hari/1000 * $data_kontrak['contract_price'];
				// echo $denda;die;
				$max_denda		  	  = 50/100 * $data_kontrak['contract_price'];
				$denda				  = ($denda>$max_denda) ? $max_denda : $denda;

				$arr = array(
					'id_procurement' => $data['id_proc'],
					'start_date'	 => $end_date,
					'end_date'	 	 => $data['end_date'],
					'denda'			 => $denda,
					'id_bast'		 => $id_bast,
					'entry_stamp'	 => date('Y-m-d H:i:s')
				);

				$denda_before = $this->db->where('id_bast',$id_bast)->where('type',4)->get('tr_progress_kontrak')->result_array();

				// print_r($arr);die;
				if (!empty($denda_before)) {
					$this->db->where('id_bast',$id_bast)->update('tr_denda',$arr);
				} else {
					$this->db->insert('tr_denda',$arr);
				}				
				
				$arr['solid_start_date'] = $data['end_date'];
				$arr['id_contract'] = $data_kontrak['id'];
				$arr['step_name'] 	= 'Denda';
				$arr['type'] 		= 4;

				if (!empty($denda_before)) {
					$this->db->where('id_bast',$id_bast)->update('tr_progress_kontrak',$arr);
				} else {
					$this->db->insert('tr_progress_kontrak',$arr);
				}				

				$arr_ = array(
					'id_procurement'	=> $data['id_proc'],
					'id_contract'		=> $data_kontrak['id'],
					'id_bast'			=> $id,
					'step_name'			=> $bast_type,
					'start_date'		=> $data['end_date'],
					'end_date'			=> $data['end_date'],
					'supposed'			=> $supposed,
					'entry_stamp'		=> date('Y-m-d H:i:s'),
					'type'				=> 5,
					'solid_start_date'	=> $data['end_date']
				);

				if (!empty($denda_before)) {
					$return = $this->db->where('id_bast',$id_bast)->update('tr_progress_kontrak',$arr_);
				} else {
					$return = $this->db->insert('tr_progress_kontrak',$arr_);
				}
				

			} else {

				$this->db->where('id_bast',$id)->delete('tr_denda');
				$this->db->where('id_bast',$id)->where('type',4)->delete('tr_progress_kontrak');
				// $this->db->where('id_bast',$id)->where('type',5)->delete('tr_progress_kontrak');
			}
		}

		if (!empty($cek_prog)) {
			// echo "string 2.2";die;
			$arr = array(
				'step_name'			=> $bast_type,
				'start_date'		=> $data['end_date'],
				'end_date'			=> $data['end_date'],
				'supposed'			=> $supposed,
				'edit_stamp'		=> date('Y-m-d H:i:s'),
				'solid_start_date'	=> $data['end_date']
			);

			$this->db->where('del',0)->where('id_bast',$id)->update('tr_progress_kontrak',$arr);

			$return = $this->db->where('del',0)->where('parent',$id)->update('tr_progress_kontrak',$arr);
		} else {
			// echo "string 2.3";die;
			$arr = array(
				'id_procurement'	=> $data['id_proc'],
				'id_contract'		=> $data_kontrak['id'],
				'id_bast'			=> $id,
				'step_name'			=> $bast_type,
				'start_date'		=> $data['end_date'],
				'end_date'			=> $data['end_date'],
				'supposed'			=> $supposed,
				'entry_stamp'		=> date('Y-m-d H:i:s'),
				'type'				=> 5,
				'solid_start_date'	=> $data['end_date']
			);

			$return = $this->db->insert('tr_progress_kontrak',$arr);
		}

		return $return;
	}

	public function hapus_bast($id)
	{
		$arr = array(
			'edit_stamp' => date('Y-m-d H:i:s'),
			'del' => 1
		);
		$this->db->where('id_bast',$id)->delete('tr_progress_kontrak');
		$this->db->where('id_bast',$id)->delete('tr_denda');
		return $this->db->where('id',$id)->update('ms_bast',$arr);
	}

	function get_realization($id,$id_spk){
   		$data_kontrak = $this->get_data_kontrak($id);
   		$date_range = $this->get_date_range($id,$id_spk);

   		if(isset($date_range['end_date'])||isset($date_range['start_date'])){

	   		$max_date	= (!empty($date_range['end_date'])) ? $date_range['end_date'] : date('Y-m-d');
	   		
			$day_total 	= get_range_date($max_date,$date_range['start_date']);
			$day_total 	+= $this->get_denda_day($id_procurement,$id_spk);
			$date = (strtotime(date('Y-m-d'))>strtotime($max_date)) ? $max_date : date('Y-m-d');//Jika tanggal sekarang lebih dari tanggal terbesar, jadikan tanggal terbesar menjadi realization date
			$date	= (isset($data_kontrak['end_actual']) || $data_kontrak['end_actual']!=NULL) ? $data_kontrak['end_actual'] : $date;
			
			$day_now = get_range_date($date,$date_range['start_date']);
			$result['range'] = ($day_now / $day_total * 100);
			if($result['range']>100) $result['range'] = 100;
			$range_step = 	array(
								'Jangka Waktu Kontrak'						=>	$this->get_kontrak_day($id,$id_spk),
								'Jangka Waktu Pelaksanaan Pekerjaan'		=>	$this->get_work_day($id,$id_spk),
							);

			foreach ($this->get_amandemen_day($id,$id_spk) as $key => $value) {
				$range_step[$value['step_name']] = $value;
			}

			$range_step['Denda']	=	$this->get_denda($id,$id_spk);

			foreach($range_step as $key => $value){
				if(strtotime($date) >= strtotime($value['start_date']) && strtotime($date) <= strtotime($value['end_date']))
					$step = $key;

			}
			$result['step'] = $step;
			$result['date'] = $date;
			$result['now'] = $day_now;
		}
		
		return $result;
   	}

	function get_graph($id,$id_procurement,$id_spk){

   		$data = $this->get_contract_progress($id,$id_spk);

   		$data_kontrak 		= $this->get_data_kontrak($id_procurement);
   		$amandemen_range 	= $this->get_amandemen_range($id_procurement,$id_spk);
   		$result = array();
   		$total_supposed = 0;

   		/* Work Day */
   		$work_date = $this->get_work_day($id_procurement,$id_spk);
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

	   	$kontrak_date = $this->get_kontrak_day($id_procurement,$id_spk);
	   	/*Kontrak Data*/
	   	$date_kontrak = get_range_date($kontrak_date['end_date'],$kontrak_date['start_date']);
	   	$label_kontrak .= '<br>'.$kontrak_date['step_name'].'( '.$date_kontrak.' Hari ) '.default_date($kontrak_date['start_date']).' - '.default_date($kontrak_date['end_date']).'<br>';

	   	$amandemen = $this->get_amandemen_day($id_procurement,$id_spk);

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
	   	$denda = $this->get_denda($id_procurement,$id_spk);
	   	if(!empty($denda)&&count($data)>0){

   			$denda_day 					= $this->get_denda_day($id_procurement,$id_spk);
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

   	function get_contract_progress($id,$id_spk){
   		if ($id_spk == '' || $id_spk == null) {
   			$arr = $this->db->select('id,step_name,start_date,end_date,supposed,type')->where('id_contract',$id)->where('del',0)->get('tr_progress_kontrak')->result_array();
   		} else {
   			$arr = $this->db->select('id,step_name,start_date,end_date,supposed,type')->where('id_contract',$id)->where('id_spk',$id_spk)->where('del',0)->get('tr_progress_kontrak')->result_array();
   		}
   		
   		return $arr;
   	}

   	function get_amandemen_range($id,$id_spk){
   		$this->db
   				->select('max(DATE(`end_date`)) as end_date, min(DATE(`start_date`)) as start_date')
   				->where('type',3)
   				->where('id_procurement',$id);

   		if ($id_spk != '' || $id_spk != null) {
   			$this->db->where('id_spk',$id_spk);
   		}
   		
   		$res =  $this->db->get('tr_progress_kontrak');
   		if($res->num_rows()>0){
	   		return $res->row_array();
	   	}
	   	return false;
   	}

   	function get_work_day($id,$id_spk){
   		if ($id_spk != '' || $id_spk != null) {
   			$this->db->where('id_spk',$id_spk);
   		}
   		return $this->db->where('type',2)->where('id_procurement',$id)->get('tr_progress_kontrak')->row_array();
   	}

   	function get_kontrak_day($id,$id_spk){
   		if ($id_spk != '' || $id_spk != null) {
   			$this->db->where('id_spk',$id_spk);
   		}
   		return $this->db->where('type',1)->where('id_procurement',$id)->get('tr_progress_kontrak')->row_array();
   	}

   	function get_amandemen_day($id,$id_spk){
   		if ($id_spk != '' || $id_spk != null) {
   			$this->db->where('id_spk',$id_spk);
   		}
   		return $this->db->where('type',3)->where('id_procurement',$id)->get('tr_progress_kontrak')->result_array();
   	}

   	function get_denda_day($id,$id_spk){
   		if ($id_spk != '' || $id_spk != null) {
   			$this->db->where('id_spk',$id_spk);
   		}
   		$data = $this->db->where('id_procurement',$id)->get('tr_denda')->row_array();
   		// echo $this->db->last_query();
   		if($data['end_date']!='' && $data['end_date']!='') {
   			return (ceil(strtotime($data['end_date']) - strtotime($data['start_date']))/86400)+1;
   		}else{
   			return 0;
   		}
   	}

   	function get_date_range($id,$id_spk){
   		$this->db
   				->select('max(DATE(`end_date`)) as end_date, min(DATE(`start_date`)) as start_date')
   				->where('id_procurement',$id);

   		if ($id_spk != '' || $id_spk != null) {
   			$this->db->where('id_spk',$id_spk);
   		}
   		
   		$res =  $this->db->get('tr_progress_kontrak')->row_array();

   		return $res;
   	}

   	function get_denda($id,$id_spk){
   		if ($id_spk != '' || $id_spk != null) {
   			$this->db->where('id_spk',$id_spk);
   		}
   		return $this->db->where('id_procurement',$id)->get('tr_denda')->row_array();
   	}

   	function get_paket_progress($id,$id_spk){
   		if ($id_spk != '' || $id_spk != null) {
   			$this->db->where('id_spk',$id_spk);
   		}
   		
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
			$day_range = get_range_date($date_next,$date_now);
			$result[$key]['day_range'] = $day_range;
			
			$total_day += $day_range;

		}

		
		$data['total_day'] = $total_day;

		foreach ($result as $key => $value) {
			$result[$key]['percent'] = floor($value['day_range'] / $total_day * 100);
		}
		return $result;
   	}

   	function get_denda_price($id){
   		$data_kontrak = $this->get_data_kontrak($id);

		$data['denda'] = $this->get_denda($id);
		$add_date = 0;
		if(isset($data['denda']['end_date'])||$data['denda']['start_date']){
			$add_date = 1;
		}
		$day_denda 		= (ceil(strtotime($data['denda']['end_date']) - strtotime($data['denda']['start_date']))/86400)+$add_date;
		$max_denda = $data_kontrak['contract_price'] * 5 / 100;
		$cur_price = $data_kontrak['contract_price'] / 1000 * $day_denda;
		$denda_price = ($max_denda<$cur_price) ? $max_denda : $cur_price;
		return $denda_price;
   	}
	
	public function get_kurs($id)
	{
		$a = $this->db->where('del', 0)
						->get('tb_kurs')
						->result_array();
		/*$a = $this->db->select('tb_kurs.symbol, tb_kurs.id')
						->join('tb_kurs', 'ms_procurement_kurs.id_kurs=tb_kurs.id', 'LEFT')
						->where('id_procurement', $id)
						->where('ms_procurement_kurs.del', 0)
						->get('ms_procurement_kurs')
						->result_array();*/
		$b = array();
		foreach ($a as $key => $value) {
			$b[$value['id']] = $value['symbol'];
		}
		return $b;
	}
}