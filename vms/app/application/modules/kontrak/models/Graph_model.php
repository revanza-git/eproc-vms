<?php
/**
 * 
 */
class Graph_model extends CI_Model
{
	public function get_graph($id_procurement)
	{
		$return = array();

		// ----------- get data contract

		$get_kontrak = $this->get_kontrak($id_procurement);

		// print_r($get_kontrak);die;

		if (!empty($get_kontrak) && isset($get_kontrak['start_contract']) && isset($get_kontrak['end_contract'])) {
			$date = get_range_date($get_kontrak['end_contract'],$get_kontrak['start_contract']);
			$return['data'][] = array(
				'type' 	=> 6,
				'label' => 'Kontrak ( '.$date.' Hari ) '.default_date($get_kontrak['start_contract']).' - '.default_date($get_kontrak['end_contract'])
			);
		}

		if (!empty($get_kontrak) && isset($get_kontrak['id'])) {
			$query = "	SELECT 
						    *
						FROM
						    tr_progress_kontrak
						WHERE
						    del = 0 AND id_procurement = ? AND id_contract = ?
						ORDER BY solid_start_date,type ASC ";

			$data = $this->db->query($query,array($id_procurement,$get_kontrak['id']))->result_array();
		}else{
			$data = array();
		}

		foreach ($data as $key => $value) {
		 	$date = get_range_date($value['end_date'],$value['start_date']);

			if ($value['type'] == '5') {
				$label = $value['step_name'].default_date($value['end_date']);
			} elseif ($value['type'] == '1') {
				$label = '';
			} 
			else {
				$label = $value['step_name'].' ( '.$date.' Hari ) '.default_date($value['start_date']).' - '.default_date($value['end_date']);
			}

			$return['data'][] = array(
				'type' 	=> $value['type'],
				'label' => $label
			);
		 } 
		 // echo $this->db->last_query();die;
		// print_r($return);die;
		return $return;
		// ----------- end process get data contract


		// ----------- get data SPK


		// $get_spk = $this->get_spk($id_procurement);
		// print_r($get_spk);die;
		// foreach ($get_spk as $key => $value) {
		// 	$date = get_range_date($value['end_date'],$value['start_date']);

		// 	// $denda = ($value['type'] == '4') ? ' , Rp.'.number_format($value['denda']) : '';
		// 	if ($value['type'] == '5') {
		// 		$label = $value['step_name'].default_date($value['end_date']);
		// 	} elseif ($value['type'] == '1') {
		// 		$label = '';
		// 	} 
		// 	else {
		// 		$label = $value['step_name'].'( '.$date.' Hari ) '.default_date($value['start_date']).' - '.default_date($value['end_date']);
		// 	}
		// 	$return['data'][] = array(
		// 		'type' 	=> $value['type'],
		// 		'label' => $label
		// 	);
		// }

		// ----------- end process get data SPK


		// ----------- get data amandemen

		// $get_amandemen = $this->get_amandemen($id_procurement);

		// foreach ($get_amandemen as $key => $value) {
		// 	$date = get_range_date($value['end_date'],$value['start_date']);
		// 	$return['data'][] = array(
		// 		'type' 	=> $value['type'],
		// 		'label' => $value['step_name'].' ( '.$date.' Hari ) '.default_date($value['start_date']).' - '.default_date($value['end_date'])
		// 	);
		// }

		// ----------- end process get data amandemen


		// ----------- get data bast

		// $get_bast = $this->get_bast($id_procurement);

		// foreach ($get_bast as $key => $value) {
		// 	$date = get_range_date($value['end_date'],$value['start_date']);
		// 	if ($value['type'] == '5') {
		// 		$label = $value['step_name'].default_date($value['end_date']);
		// 	} elseif ($value['type'] == '1') {
		// 		$label = '';
		// 	} 
		// 	else {
		// 		$label = $value['step_name'].' ( '.$date.' Hari ) '.default_date($value['start_date']).' - '.default_date($value['end_date']);
		// 	}

		// 	$return['data'][] = array(
		// 		'type' 	=> $value['type'],
		// 		'label' => $label
		// 	);
		// }

		// ----------- end process get data bast

		// ----------- get data denda

		// $get_denda = $this->get_denda($id_procurement);

		// foreach ($get_denda as $key => $value) {
		// 	$date = get_range_date($value['end_date'],$value['start_date']);
		// 	$return['data'][] = array(
		// 		'type' 	=> 4,
		// 		'label' => 'Denda ( '.$date.' Hari ) '.default_date($value['start_date']).' - '.default_date($value['end_date']).' '.', Rp. '.number_format($value['denda'])
		// 	);
		// }



		// ----------- end process get data denda
	}

	public function get_kontrak($id_procurement)
	{
		$return = $this->db->where('del',0)->where('id_procurement',$id_procurement)->get('ms_contract');
		return $return->row_array();
	}

	public function get_spk($id_procurement)
	{
		// $return = $this->db->where('del',0)->where('id_procurement',$id_procurement)->where_in('type',array(1,2))->get('tr_progress_kontrak');

		$query = "	SELECT
						*
					FROM
						tr_progress_kontrak
					WHERE
						del = 0 AND id_procurement = $id_procurement AND id_spk IS NOT NULL
					
					order by id_spk ASC, type asc
		";
		$query = $this->db->query($query);

		if (count($query->result_array()) > 0) {
			$data = $query->result_array();
			// foreach ($data as $key => $value) {
			// 	$denda = $this->db->where('id_spk',$value['id_spk'])->get('tr_denda')->result_array();
			// 	$bast = $this->db->where('id_spk',$value['id_spk'])->get('ms_bast')->result_array();

			// 	if (!empty($denda)) {
			// 		foreach ($denda as $k => $v) {
			// 			$data[] = array(
			// 				'type'			=> 5,
			// 				'start_date'	=> $v['start_date'],
			// 				'end_date'	 	=> $v['end_date'],
			// 				'denda'			=> $v['denda'],
			// 				'step_name'		=> 'Denda Coy'
			// 			);
			// 		}
			// 	}

			// 	if (!empty($bast)) {
			// 		foreach ($bast as $k => $v) {
			// 			$data[] = array(
			// 				'type'			=> 4,
			// 				'start_date'	=> $v['start_date'],
			// 				'end_date'	 	=> $v['end_date'],
			// 				'denda'			=> $v['denda'],
			// 				'step_name'		=> 'BAST Coy'
			// 			);
			// 		}
			// 	}
			// }
			// print_r($data);die;
			return $data;

		} else {
			$query = "	SELECT
						*
					FROM
						tr_progress_kontrak
					WHERE
						del = 0 AND id_procurement = $id_procurement AND type = 2
			";
			$query = $this->db->query($query);
			return $query->result_array();
		}
		
	}

	public function get_amandemen($id_procurement)
	{
		$return = $this->db->where('del',0)->where('id_procurement',$id_procurement)->where('type',3)->get('tr_progress_kontrak');
		return $return->result_array();
	}

	public function get_bast($id_procurement)
	{
		// $return = $this->db->where('id_spk =',null)->where('del',0)->where('id_procurement',$id_procurement)->where('type',4)->get('tr_progress_kontrak');
		$query = " 	SELECT
						*
					FROM
						tr_progress_kontrak
					WHERE
						del = 0 AND id_spk IS NULL AND id_bast IS NOT NULL AND id_procurement = ? 
					order by type asc";
		$return = $this->db->query($query, array($id_procurement));
		return $return->result_array();
	}

	public function get_denda($id_procurement)
	{
		$data = $this->db->where('(id_spk IS NULL)')->where('id_procurement',$id_procurement)->get('tr_denda')->result_array();

		$data_kontrak = $this->get_kontrak($id_procurement);

		foreach ($data as $key => $value) {
			$add_date = 0;
			if(isset($value['end_date'])||$value['start_date']){
				$add_date = 1;
			}
			$day_denda 		= (ceil(strtotime($value['end_date']) - strtotime($value['start_date']))/86400)+$add_date;
			$max_denda = $data_kontrak['contract_price'] * 5 / 100;
			$cur_price = $data_kontrak['contract_price'] / 1000 * $day_denda;
			$denda_price = ($max_denda<$cur_price) ? $max_denda : $cur_price;
			isset($value['denda_price']);

			$data[$key][$value['denda_price']] = $denda_price;
		}
		// print_r($data);die;
		return $data;
		
   		// echo $this->db->last_query();
   		// if($data['end_date']!='' && $data['end_date']!='') {
   		// 	return (ceil(strtotime($data['end_date']) - strtotime($data['start_date']))/86400)+1;
   		// }else{
   		// 	return 0;
   		// }
	}
}
