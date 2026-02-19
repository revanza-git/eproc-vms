<?php
class Vendor_dash extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		
		$this->load->model('auction/vendor_dash_model','vdm');
	}
	function index(){
		$this->load->library('form');
		$search = '';
		$page 	= '';
		$post 	= $this->input->post();

		$per_page	=10;
		$sort 		= $this->utility->generateSort(array('name', 'auction_date', 'work_area'));
		
		$data['auction_list']	= $this->vdm->get_auction_list_vendor($search, $sort, $page, $per_page,TRUE);
		/*$data['filter_list'] 	= $this->form->group_filter_post($this->get_field());*/
		$data['pagination'] 	= $this->utility->generate_page('auction/user/vendor_dash',$sort, $per_page, $this->vdm->get_auction_list_vendor($search, $sort, '','',FALSE));
		$data['sort'] 			= $sort;

		$layout['content']		= $this->load->view('auction/user/content',$data,TRUE);
		$item['header'] 		= $this->load->view('dashboard/header',$this->session->userdata('user'),TRUE);
		$item['content'] 		= $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	function view($id_lelang = ''){
		$data['fill'] = $fill = $this->vdm->get_data($id_lelang);
		$data['barang'] = $this->vdm->get_barang($id_lelang);
		$data['kurs_info'] = $this->vdm->kurs_info($id_lelang);
		
		$data['id_lelang'] = $id_lelang;
		$data['penawaran'] = $this->vdm->get_penawaran($id_lelang);
		// echo print_r($data['penawaran']);
		$data['syarat'] = $this->vdm->get_syarat($id_lelang);
		$data['kurs'] = $this->vdm->get_kurs($id_lelang);
		
		$data['action'] = site_url('auction/user/vendor_dash/save_penawaran/');
		

		if($fill['auction_type'] == "forward_auction"){ $data['limit'] = "tertinggi"; $data['persentase'] = "Kenaikan"; }
		if($fill['auction_type'] == "reverse_auction"){ $data['limit'] = "terendah"; $data['persentase'] = "Penurunan"; }

	
		
		$layout['content']		= $this->load->view('auction/user/vendor_dash',$data,TRUE);
		$layout['script']		= $this->load->view('auction/user/content_js',$data,TRUE);
		$item['header'] 		= $this->load->view('dashboard/header',$this->session->userdata('user'),TRUE);
		$item['content'] 		= $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template', $item);
	}
	
	function cek_rating($id_lelang = '', $id_user = ''){
		//debugging tools
		if(!$id_user) $id_user = $this->session->userdata('user')['id_user'];
		
		$return = array();
		$query = $this->vdm->get_barang($id_lelang);
		$fill = $this->vdm->get_data($id_lelang);
		
		if($fill['metode_penawaran'] == "harga_satuan"){
			$x = 0;
			foreach($query->result() as $data){
				$return['barang'][$x]['id'] = $data->id;
							
				if($fill['auction_type'] == "forward_auction")
					$limit = $this->vdm->get_highest($id_lelang, $data->id, $id_user);			
				if($fill['auction_type'] == "reverse_auction")
					$limit = $this->vdm->get_lowest($id_lelang, $data->id, $id_user);
					
				$return['barang'][$x]['position'] = $limit;
				$return['barang'][$x]['is_higher'] = $this->vdm->cek_posisi_penawaran($id_lelang, $data->id, $fill['auction_type']);
				
				$x++;
			}
		}
		else{
			if($fill['auction_type'] == "forward_auction")
				$limit = $this->vdm->get_highest($id_lelang, '', $id_user);			
			if($fill['auction_type'] == "reverse_auction")
				$limit = $this->vdm->get_lowest($id_lelang, '', $id_user);
				
			$return['position'] = $limit;
			$return['is_higher'] = $this->vdm->cek_posisi_penawaran($id_lelang, '', $fill['auction_type']);
		}
		
		die(json_encode($return));
	}
	
	function kurs($id_lelang = ''){
		$query	= $this->vdm->get_kurs($id_lelang);
		$select	= $this->vdm->get_user_currency($id_lelang, 'ASC', $data->id);
		
		foreach($query->result() as $data) $return .= '<option value="'.$data->id.'">'.$data->symbol.'</option>';
		return $return;
	}
	
	function save_penawaran(){
		$fill = $this->vdm->get_data($_POST['id_lelang']);
		$id_lelang = $_POST['id_lelang'];
		$barang = $_POST['id_barang'];
		$kurs = $_POST['id_kurs'];
		$return = array();
		$curr_highest = $curr_lowest = 0;
		$is_first = (isset($_POST['is_first'])) ? $_POST['is_first'] : NULL;

		// echo print_r($fill);
		
		for($i=0;$i<count($barang);$i++){
			$id_barang 			= key($barang);
			$harga_penawaran	= $barang[$id_barang];
			

			if($harga_penawaran){
				$harga_penawaran = str_replace(",", "", $harga_penawaran);
				// echo $harga_penawaran;
				$rate 				= $kurs[$id_barang];
				$hps_info			= $this->vdm->cek_hps($id_lelang, $id_barang);
				$hps				= $hps_info['nilai_hps'];
									
				$penawaran_in_idr 	= $this->vdm->convert_to_idr($harga_penawaran, $rate, $id_lelang);
				// echo $rate;
				$hps_in_idr			= $this->vdm->convert_to_idr($hps, $hps_info['id_kurs'], $id_lelang);

	
				$percent			= $this->vdm->cek_percentage($id_lelang, $id_barang, $penawaran_in_idr);
				
				if(!$is_first){
					//convert to rupiah first
					if($fill['auction_type'] == "forward_auction"){
						$curr_highest = $this->vdm->cek_highest($id_lelang, $id_barang);
						$curr_highest = $curr_highest['nilai'];
						// if($curr_highest>0)
							if($curr_highest > $penawaran_in_idr) die(json_encode(array('status' => 'fail', 'message' => 'Masukan harga penawaran yang lebih tinggi !')));
					}
					else if($fill['auction_type'] == "reverse_auction"){
						$curr_lowest = $this->vdm->cek_lowest($id_lelang, $id_barang);
						$curr_lowest = $curr_lowest['nilai'];
						// if($curr_lowest>0)
							if($curr_lowest < $penawaran_in_idr) die(json_encode(array('status' => 'fail', 'message' => 'Masukan harga penawaran yang lebih rendah !')));
					}

				}
				
				//set parameters 
				$param = array(
					'id_lelang' => $id_lelang,
					'id_vendor' => $this->session->userdata('user')['id_user'],
					'id_barang' => $id_barang,
					'id_rate' => $rate,
					'nilai' => $harga_penawaran,
					'in_rate' => $penawaran_in_idr,
					'down_percent' => abs(number_format(preg_replace("/[,]/", "", $percent), 2)),
					'entry_stamp' => date("Y-m-d H:i:s")
				);
				
				//input data into the database
				$nilai = $this->vdm->save_penawaran($param);
				
				//JSON
				$return['barang'][$i]['id']			= $id_barang;
				$return['barang'][$i]['nilai']		= number_format($harga_penawaran);
				$return['barang'][$i]['in_rate']	= number_format($penawaran_in_idr);
				$return['barang'][$i]['persentase'] = abs($percent);
				$return['barang'][$i]['id_kurs'] 	= $rate;
				$return['barang'][$i]['name']		= $this->vdm->get_nama_barang($id_barang);
				$return['is_first']					= $is_first;
							
				if($fill['auction_type'] == "forward_auction"){
					if($hps_in_idr > $penawaran_in_idr)
						$return['barang'][$i]['is_higher'] = true;
				}				
				else if($fill['auction_type'] == "reverse_auction"){
					if($hps_in_idr < $penawaran_in_idr)
						$return['barang'][$i]['is_higher'] = true;
				}
			}
			next($kurs);
			next($barang);
			
		}
		
		$return['status'] = "success";
		die(json_encode($return));
	}
}