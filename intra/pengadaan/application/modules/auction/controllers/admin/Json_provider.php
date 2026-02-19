<?php
class json_provider extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		$this->load->model('Auction_report_model','am');
		$this->load->model('json_provider_model');
	}
	
	function get_barang($id_lelang = ''){
		$query = $this->json_provider_model->get_barang($id_lelang);
		$return = array();
		
		foreach($query->result() as $data){
			if($data->id_kurs == 1)
				$hps_in_idr = $data->nilai_hps;
			else
				$hps_in_idr = $this->json_provider_model->convert_to_idr($data->nilai_hps, $data->id_kurs, $id_lelang);
			
			array_push($return, array('id' => $data->id, 'name' => $data->nama_barang, 'hps' => $data->nilai_hps, 'hps_in_idr' => $hps_in_idr/*, 'vol' => $data->volume*/));
		}
		
		die(json_encode($return));
	}
	
	function get_peserta($id_lelang = ''){
		$query = $this->json_provider_model->get_peserta($id_lelang);
		// print_r($query->result_array());
		$return = array();
		
		foreach($query->result() as $data)
			array_push($return, array('id' => $data->id_vendor, 'name' => $data->name));
		
		die(json_encode($return));
	}
	
	function get_initial_data($id_lelang = '', $id_barang = ''){
		$data = $this->json_provider_model->get_initial_data($id_lelang, $id_barang);
		
		$name = $this->json_provider_model->select_barang($id_barang);
		$nama = $name['nama_barang'];
		$subtitle = $name['symbol']." ".number_format($name['nilai_hps']);
						
		$lastPos = $this->json_provider_model->get_chart_update($id_lelang);
		$time = date("Y-m-d H:i:s");
		
		die(json_encode(array('id' => $id_barang, 'name' => $nama, 'subtitle' => $subtitle, 'data' => $data, 'last' => $lastPos, 'time' => $time)));
	}
	
	function get_chart_update($id_lelang = ''){
		$data = $this->json_provider_model->get_chart_update($id_lelang);
		$time = date("Y-m-d H:i:s");		

		die(json_encode(array('data' => $data, 'time' => $time)));
	}
	
	function get_user_update($id_lelang = '', $id_user = ''){
		if(!$id_user) $id_user = $this->utility->get_userdata('id_user');
		$fill = $this->json_provider_model->select_lelang($id_lelang);
		$return = array();
		
		if($fill['is_started'])
			$return['data'] = $this->json_provider_model->get_rank($id_lelang, $id_user);
		
		$return['status'] = array(
			'is_started' => $fill['is_started'], 
			'is_finished' => $fill['is_finished'], 
			'is_suspended' => $fill['is_suspended']
		);

		$return['time'] = array(
			'now' => date("Y-m-d H:i:s"), 
			'limit' => $fill['time_limit']
		);
		
		die(json_encode($return));
	}

	public function get_vendor_rank($id_lelang="",$type="")
	{
		$return = $this->json_provider_model->get_list_barang($id_lelang);
		// print_r($return);die;
		$tab = '';

		foreach ($return as $key => $value) {
			$getPeserta = $this->json_provider_model->get_vendor_ranking($id_lelang,$value['id'],$type);
			$tab .= '<table border>
			<thead>
				<tr>
					<th colspan=2>Barang : '.$value['nama_barang'].'</th>
					<tr>
						<th>Peringkat</th>
					<th>Peserta</th>
					</tr>';

					foreach ($getPeserta as $key_ => $value_) {
						$tab .= '<tr>
							<td>'.($key_+1).'</td>
							<td>'.$value_['nama_vendor'].'</td>
						</tr><br>';
						$key++;
					}

				$tab .='</tr>
			</thead>
			<tbody>';
		}

		$tab .= '</tbody>
		</table>';

		echo json_encode($tab);
	}
}