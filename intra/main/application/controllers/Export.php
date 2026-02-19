<?php defined('BASEPATH') OR exit('No direct script access allowed');

// require 'vendor/autoload.php';

// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Export extends MY_Controller {

	public $form;
	public $modelAlias 	= 'mm';
	public $alias 		= 'ms_fppbj';
	public $module 		= 'export';
	public $admin		= null;

	public function __construct(){
		parent::__construct();
		include_once APPPATH.'third_party/dompdf2/dompdf_config.inc.php';

		$this->load->model('Main_model','mm');
		$this->load->model('Fppbj_model','fm');
		$this->load->model('Fkpbj_model','fkm');
		$this->load->model('Export_model','ex');
		$this->load->model('Export_test_model','ext');
		$this->load->library('session');

		$this->admin	 	= $this->session->userdata('admin');
		$this->approveURL	= site_url('export/filter_rekap_perencanaan');
		$this->form = array(
			'form' => array(
				array(
					'field'			=> 'id_division',
					'type'			=> 'checkbox',
					'label'			=> 'Divisi',
					'source' 		=> array('asd' => 'asd', 'asd_' => 'asd'),
				),array(
					'field'			=> array('start','end'),
					'type'			=> 'date_range',
					'label'			=> 'Rentang Waktu',
				)
			),

			'successAlert'=>'Berhasil mengubah data!',
		);
	}

	private function asset_data_uri($relative_path) {
		$relative_path = ltrim(str_replace('\\', '/', $relative_path), '/');
		$full_path = FCPATH . $relative_path;
		if (!is_file($full_path)) {
			return base_url($relative_path);
		}
		$ext = strtolower(pathinfo($full_path, PATHINFO_EXTENSION));
		$mime = 'application/octet-stream';
		if ($ext === 'png') {
			$mime = 'image/png';
		} else if ($ext === 'jpg' || $ext === 'jpeg') {
			$mime = 'image/jpeg';
		} else if ($ext === 'gif') {
			$mime = 'image/gif';
		} else if ($ext === 'svg') {
			$mime = 'image/svg+xml';
		}
		$data = base64_encode(file_get_contents($full_path));
		return 'data:' . $mime . ';base64,' . $data;
	}

	//Export PDF per FPPBJ 
	public function fppbj($id,$no="",$tanggal = ""){
		$nomor = $no;
		$tanggal = ($tanggal == "") ? date('Y-m-d') : $tanggal;
		//$view 	= $this->load->view('fppbj/list',null, TRUE);
		$table = '';
		$no  = 1;
		$dataFPPBJ = $this->fm->selectData($id);
		$dataAnalisaResiko = $this->fm->get_analisa_resiko($id);
		$dataAnalisaDetail = $this->fm->get_data_analisa_risiko_detail($id);
		$getCat = array();
		if (count($dataAnalisaDetail)) {
			foreach ($dataAnalisaDetail as $key => $value) {
				$manusia 	= $this->setCategory($value['manusia']);
				$asset 		= $this->setCategory($value['asset']);
				$lingkungan = $this->setCategory($value['lingkungan']);
				$hukum 		= $this->setCategory($value['hukum']);

				if ($manusia == "extreme" || $asset == "extreme" || $lingkungan == "extreme" || $hukum == "extreme") {
					$category = 'E';
				}else if ($manusia == "high" || $asset == "high" || $lingkungan == "high" || $hukum == "high") {
					$category = 'H';
				}else  if ($manusia == "medium" || $asset == "medium" || $lingkungan == "medium" || $hukum == "medium") {
					$category = 'M';
				}else if ($manusia == "low" || $asset == "low" || $lingkungan == "low" || $hukum == "low") {
					$category = 'L';
				}else{
					$category = '';
				}
					
				array_push($getCat, $category);
			}
		}
		// print_r($getCat);die;
		if (in_array('E', $getCat, TRUE)){
			$getValCSMS = 'E';
		}else if (in_array('H', $getCat, TRUE)){
			$getValCSMS = 'H';
		}else if (in_array('M', $getCat, TRUE)){
			$getValCSMS = 'M';
		}else if (in_array('L', $getCat, TRUE)){
			$getValCSMS = 'L';
		}else{
			$getValCSMS = '';
		}
		$dataAnalisaSwakelola = $this->fm->get_analisa_swakelola($id);
		$encode_jwpp = json_encode(array('start'=>$dataFPPBJ['jwpp_start'],'end' =>$dataFPPBJ['jwpp_end']));
		$encode_jwp = json_encode(array('start'=>$dataFPPBJ['jwp_start'],'end' =>$dataFPPBJ['jwp_end']));
		$jwpp = json_decode($encode_jwpp);
		$jwp = json_decode($encode_jwp);

		$date_jwpp = strtotime($jwpp->end) - strtotime($jwpp->start);
		$total_jwpp = round($date_jwpp / (60*60*24));

		$date_jwp = strtotime($jwp->end) - strtotime($jwp->start);
		$total_jwp = round($date_jwp / (60*60*24));

		if ($dataFPPBJ['jwpp_start'] != null && $dataFPPBJ['jwpp_end'] != null) {
			$jwpp = date('d M Y', strtotime(json_decode($encode_jwpp)->start)).' sampai '.date('d M Y', strtotime(json_decode($encode_jwpp)->end)).' ('.$total_jwpp.' Hari)';
		} else{
			$jwpp = '-';
		}
		if ($dataFPPBJ['jwp_start'] != null && $dataFPPBJ['jwp_end'] != null) {
			$jwp = date('d M Y',strtotime($jwp->start)).' sampai '.date('d M Y',strtotime($jwp->end)).' ('.$total_jwp.' Hari)';
		} else {
			$jwp = '-';
		}

		if (!isset($tanggal)) {
			$tanggal_kesepakatan = '-';
		} else{
			$post_data = $this->input->post();
			$tanggal_kesepakatan = isset($post_data['tanggal']) ? $post_data['tanggal'] : '-';
		}
			$dataMaster = $this->fm->getFPPBJ();
			$no = 1;
			$dataAnalisa = $this->ex->get_analisa($id);
			if (isset($dataAnalisa['dpt_list']) && $dataAnalisa['dpt_list'] != '') {
				$get_dpt = $dataAnalisa['dpt_list'];
				$get = '';
				foreach ($get_dpt as $key) {
					$get .= $key.', '; 
				}
				// print_r($dataAnalisa);
				$analisa = '<td>
								<span style="float:left;">
										<img src="'.base_url().'assets/images/check.png"></span> Ada</td>
								</tr>
								<tr>
									<td><span style="float:left;">
										<img src="'.base_url().'assets/images/check-box.png"></span> Tidak Ada</td>
								</tr>
								<tr>
									<td>Keterangan : '.$get.' <br> Usulan : '.$dataAnalisa['usulan'].'</td>
								</tr>';
			} else{
				$analisa = '<td>
								<span style="float:left;">
								<img src="'.base_url().'assets/images/check.png"></span> Tidak Ada</td>
								</tr>
								<tr>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Ada</td>
								</tr>
								<tr>
									<td>Keterangan : -</td>
								</tr>';
			}
			$table.=' <tr>
						<td></td>'
						.'<td>'.$dataFPPBJ['jenis_pengadaan'].'</td>'
						.'<td>'.$dataFPPBJ['penggolongan_penyedia'].'</td>'
						.'<td>'.$dataFPPBJ['value'].'</td>'
						.'<td>'.$jwpp.'</td>'
						.'<td>'.$jwp.'</td>'
						.'<td>'.$dataFPPBJ['hps'].'</td>'
						.'<td>'.$dataFPPBJ['desc_metode_pembayaran'].'</td>'
						.'<td>'.$dataFPPBJ['jenis_kontrak'].'</td>
						</tr>';
			;
			$no_pr = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> PR : Nomor PR ...</td>';
			$no_pr_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> PR : Nomor PR ...</td>';
			$hps = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> HPS (Dalam Amplop Tertutup)</td>';
			$hps_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> HPS (Dalam Amplop Tertutup)</td>';
			$kak = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> KAK / Spesifikasi Teknis</td>';
			$kak_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> KAK / Spesifikasi Teknis</td>';
			$no_pr = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> PR : Nomor PR ...</td>';
			$no_pr_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> PR : Nomor PR ...</td>';
			$form_analisa_resiko = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Form Analisa Resiko</td>';
			$form_analisa_swakelola = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Swakelola</td>';
			if ($dataFPPBJ['tipe_pengadaan'] == 'jasa') {
				// echo "sss";
				if ($dataAnalisaResiko['id_fppbj'] != '' ) {
				   $form_analisa_resiko = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Form Analisa Resiko</td>';
				}else{
					$form_analisa_resiko = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Form Analisa Resiko</td>';
				} 
				if ($dataFPPBJ['hps'] != '0') {
					$hps = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> HPS (Dalam Amplop Tertutup)</td>';
				} else{
					$hps = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> HPS (Dalam Amplop Tertutup)</td>';
				}
				if ($dataFPPBJ['kak_lampiran'] != '') {
					$kak = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> KAK / Spesifikasi Teknis</td>';
				} else{
					$kak = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> KAK / Spesifikasi Teknis</td>';
				}
				if ($dataFPPBJ['no_pr'] != '') {
					$no_pr = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> PR : '.$dataFPPBJ['no_pr'].'</td>';
				} else{
					$no_pr = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> PR : Nomor PR ...</td>';
				}
			} else if ($dataFPPBJ['tipe_pengadaan'] == 'barang') {
				if ($dataAnalisaSwakelola['id_fppbj'] != '' && $dataAnalisaSwakelola['waktu'] != '' && $dataAnalisaSwakelola['biaya'] != '' && $dataAnalisaSwakelola['tenaga'] != '' && $dataAnalisaSwakelola['bahan'] != '' && $dataAnalisaSwakelola['peralatan'] != '') {
				  $form_analisa_swakelola = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Swakelola</td>';
				} else{
					 $form_analisa_swakelola = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Swakelola</td>';
				}
				if ($dataFPPBJ['hps'] != '0') {
					$hps_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> HPS (Dalam Amplop Tertutup)</td>';
				} else{
					$hps_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> HPS (Dalam Amplop Tertutup)</td>';
				}
				if ( $dataFPPBJ['kak_lampiran'] != '') {
					$kak_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> KAK / Spesifikasi Teknis</td>';
				} else{
					$kak_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> KAK / Spesifikasi Teknis</td>';
				}
				if ($dataFPPBJ['no_pr'] != '') {
					$no_pr_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> PR : '.$dataFPPBJ['no_pr'].'</td>';
				} else{
					$no_pr_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> PR : Nomor PR ...</td>';
				}
				$barang = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Barang</td>';
			}else{
				$barang = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Barang</td>';
			}

			if($dataFPPBJ['jenis_pengadaan'] == 'jasa_konstruksi'){
				$jasa_konstruksi = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Jasa Konstruksi</td>';
			}else{
				$jasa_konstruksi = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Jasa Konstruksi</td>';
			}

			if($dataFPPBJ['jenis_pengadaan'] == 'jasa_konsultasi'){
				$jasa_konsultasi = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Jasa Konsultasi</td>';
			}else{
				$jasa_konsultasi = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Jasa Konsultasi</td>';
			}

			if($dataFPPBJ['jenis_pengadaan'] == 'jasa_lainnya'){
				$jasa_lainnya = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Jasa Lainnya</td>';
			}else{
				$jasa_lainnya = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Jasa Lainnya</td>';
			}

			if($dataFPPBJ['penggolongan_penyedia'] == 'perseorangan'){
				$perseorangan = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Perseorangan</td>';
			}else{
				$perseorangan = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Perseorangan</td>';
			}

			if($dataFPPBJ['penggolongan_penyedia'] == 'usaha_kecil'){
				$usaha_kecil = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Usaha Kecil (K)</td>';
			}else{
				$usaha_kecil = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Usaha Kecil (K)</td>';
			}

			if($dataFPPBJ['penggolongan_penyedia'] == 'usaha_menengah'){
				$usaha_menengah = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Usaha Menengah (M)</td>';
			}else{
				$usaha_menengah = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Usaha Menengah (M)</td>';
			}

			if($dataFPPBJ['penggolongan_penyedia'] == 'usaha_besar'){
				$usaha_besar = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Usaha Besar (B)</td>';
			}else{
				$usaha_besar = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Usaha Besar (B)</td>';
			}

			// if($getValCSMS == 'E' || $getValCSMS == 'H'){
			// 	$high = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>High</td>';
			// }else{
			// 	$high = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>High</td>';
			// }

			// if($getValCSMS == 'M'){
			// 	$medium = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>Medium</td>';
			// }else{
			// 	$medium = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Medium</td>';
			// }

			// if($getValCSMS == 'L'){
			// 	$low = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>Low</td>';
			// }else{
			// 	$low = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Low</td>';
			// }

			if ($getValCSMS == 'E' || $getValCSMS == 'H') {
				$checked_csms = '<tr>
									<td rowspan="3">Penggolongan CSMS Penyedia Barang/Jasa (Khusus Pengadaan Jasa dan sesuai hasil analisa risiko)</td>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>High</td>
								</tr>
								<tr>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Medium</td>
								</tr>
								<tr>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Low</td>
								</tr>';
			} else if ($getValCSMS == 'M') {
				$checked_csms = '<tr>
									<td rowspan="3">Penggolongan CSMS Penyedia Barang/Jasa (Khusus Pengadaan Jasa dan sesuai hasil analisa risiko)</td>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>High</td>
								</tr>
								<tr>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>Medium</td>
								</tr>
								<tr>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Low</td>
								</tr>';
			} elseif ($getValCSMS == 'L') {
				$checked_csms = '<tr>
									<td rowspan="3">Penggolongan CSMS Penyedia Barang/Jasa (Khusus Pengadaan Jasa dan sesuai hasil analisa risiko)</td>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>High</td>
								</tr>
								<tr>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Medium</td>
								</tr>
								<tr>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>Low</td>
								</tr>';
			} else {
				$checked_csms = '<tr>
									<td rowspan="3">Penggolongan CSMS Penyedia Barang/Jasa (Khusus Pengadaan Jasa dan sesuai hasil analisa risiko)</td>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>High</td>
								</tr>
								<tr>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Medium</td>
								</tr>
								<tr>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Low</td>
								</tr>';
			}

			if($dataFPPBJ['jenis_kontrak'] == 'po'){
				$po = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>Purchase Order (PO)</td>';
			}else{
				$po = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Purchase Order (PO)</td>';
			}

			if($dataFPPBJ['jenis_kontrak'] == 'GTC01'){
				$GTC01 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>GTC01 - Kontrak Jasa Konstruksi non EPC</td>';
			}else{
				$GTC01 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>GTC01 - Kontrak Jasa Konstruksi non EPC</td>';
			}

			if($dataFPPBJ['jenis_kontrak'] == 'GTC02'){
				$GTC02 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>GTC02 - Kontrak Jasa Konsultan</td>';
			}else{
				$GTC02 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>GTC02 - Kontrak Jasa Konsultan</td>';
			}

			if($dataFPPBJ['jenis_kontrak'] == 'GTC03'){
				$GTC03 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>GTC03 - Kontrak Jasa Umum</td>';
			}else{
				$GTC03 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>GTC03 - Kontrak Jasa Umum</td>';
			}

			if($dataFPPBJ['jenis_kontrak'] == 'GTC04'){
				$GTC04 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>GTC04 - Kontrak Jasa Pemeliharaan</td>';
			}else{
				$GTC04 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>GTC04 - Kontrak Jasa Pemeliharaan</td>';
			}

			if($dataFPPBJ['jenis_kontrak'] == 'GTC05'){
				$GTC05 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>GTC05 - Kontrak Jasa Pembuatan Software</td>';
			}else{
				$GTC05 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>GTC05 - Kontrak Jasa Pembuatan Software</td>';
			}

			if($dataFPPBJ['jenis_kontrak'] == 'GTC06'){
				$GTC06 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>GTC06 - Kontrak Jasa Sewa Fasilitas dan Alat</td>';
			}else{
				$GTC06 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>GTC06 - Kontrak Jasa Sewa Fasilitas dan Alat</td>';
			}

			if($dataFPPBJ['jenis_kontrak'] == 'GTC07'){
				$GTC07 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>GTC07 - Kontrak Jasa Tenaga Kerja</td>';
			}else{
				$GTC07 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>GTC07 - Kontrak Jasa Tenaga Kerja</td>';
			}
			if($dataFPPBJ['jenis_kontrak'] == 'spk'){
				$SPK = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>SPK</td>';
			}else{
				$SPK = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>SPK</td>';
			}

			$jwpp_decoded = json_decode($dataFPPBJ['jwpp']);
			if (is_object($jwpp_decoded) && isset($jwpp_decoded->start) && isset($jwpp_decoded->end)) {
				$dataFPPBJ['jwpp'] = date('d M Y', strtotime($jwpp_decoded->start))." sampai ".date('d M Y', strtotime($jwpp_decoded->end));
			} else {
				$dataFPPBJ['jwpp'] = '-';
			}
			
			$jwp_decoded = json_decode($dataFPPBJ['jwp']);
			if (is_object($jwp_decoded) && isset($jwp_decoded->start) && isset($jwp_decoded->end)) {
				$dataFPPBJ['jwp'] = date('d M Y', strtotime($jwp_decoded->start))." sampai ".date('d M Y', strtotime($jwp_decoded->end));
			} else {
				$dataFPPBJ['jwp'] = '-';
			}

		//  header("Content-type: application/vnd.ms-excel");
		//  header("Content-Disposition: attachment; filename=Laporan FPPBJ".default_date(date('Y-m-d')).".xls");
		// 	print_r($dataFPPBJ);

			$table_='<html>
						<head>
							<title></title>
							<style type="text/css">
								thead:before, thead:after { display: none; }
								tbody:before, tbody:after { display: none; }
								span { float: none !important; display: inline-block; }
									@media all{
										ol{
											padding-left : 20px;
											padding-top : -15px;
											padding-bottom : -15px;
										}
										thead { display:table-header-group; }
									}
								table {
									width: 705px;
									border : 1px solid #000;
									border-collapse: collapse;
									border-spacing : 0;
									align: center;
								}
								.no{
									vertical-align: top;
								}
								td, th {
									border : 1px solid #000;
									padding: 3px 5px;
									word-wrap: break-word;
								}
								.desc{
									margin-top: 50px;
									margin-bottom: 50px;
								}
								.desc, .desc td, .desc th{
									border: none !important;
								}
								span img{
									width: 15px !important;
									margin: 0 5px;
								}
								.ttd{
									width: 705px;
									margin-top: 25px;
								}
								.ttd td, .ttd th{
									padding: 5px;
								}
							</style>
						</head>
						<body>

							<table align="center">
								<tr>
									<td>
										<img src="'.base_url().'assets/images/NUSANTARA-REGAS-2.png" style="height: 45px">
									</td>
									<td>
										<div style="font-size: 14px;">
											FORMULIR PERMOHONAN PENGADAAN BARANG/JASA (FPPBJ) 
										</div>
									</td>
								</tr>
							</table>
							<table align="center" style="border:none; margin-top: 25px">
								<tr>
									<td style="border:none; width: 170px">Nomor</td>
									<td style="border:none">: '.$nomor.' </td>
								</tr>
								<tr>
									<td style="border:none; width: 170px">Tanggal Kesepakatan</td>
									<td style="border:none">: '.default_date($tanggal_kesepakatan).' </td>
								</tr>
								<tr>
									<td style="border:none; width: 170px">Div/Dept</td>
									<td style="border:none">: '.$dataFPPBJ['division'].'</td>
								</tr>
								<tr>
									<td style="border:none; width: 170px">Judul Pengadaan</td>
									<td style="border:none">: '.$dataFPPBJ['nama_pengadaan'].'</td>
								</tr>
							</table>
							<table align="center" style="margin-top: 25px">
								<tr>
									<th style="width: 20px;">
										No.
									</th>
									<th>
										SYARAT KELENGKAPAN
									</th>
									<th style="width: 170px;">
										DATA
									</th>
								</tr>
								<tr>
									<th rowspan="9" style="vertical-align: top; width: 20px">'.$no++.'</th>
									<th colspan="2" style="text-align:left;">Kelengkapan Dokumen Permohonan</th>
								</tr>
								<tr>
									<td rowspan="4">Pengadaan Jasa</td>
									'.$form_analisa_resiko.'
								</tr>	
								<tr>
									'.$hps.'
								</tr>
								<tr>
									'.$kak.'
								</tr>
								<tr>
									'.$no_pr.'
								</tr>
								<tr>
									<td rowspan="4">Pengadaan Barang</td>
									'.$form_analisa_swakelola.'
								</tr>
								<tr>
									'.$hps_.'
								</tr>
								<tr>
									'.$kak_.'
								</tr>
								<tr>
									'.$no_pr_.'
								</tr>
								<tr>
									<th rowspan="8" style="vertical-align: top; width: 20px">'.$no++.'</th>
									<th colspan="2" style="text-align:left;">Uraian Pengadaan Barang/Jasa</th>
								</tr>
								<tr>
									<td rowspan="4">Penggolongan Penyedia Barang/Jasa (usulan)</td>
									'.$perseorangan.'
								</tr>
								<tr>
									'.$usaha_kecil.'
								</tr>
								<tr>
									'.$usaha_menengah.'
								</tr>
								<tr>
									'.$usaha_besar.'
								</tr>
								'.$checked_csms.'
							</table>
							<table align="center" style="page-break-before: always; margin-top: 25px">
								<tr>
									<th rowspan="16" style="vertical-align: top; width: 20px"></th>
									<th colspan="2" style="text-align:left;">Uraian Pengadaan Barang/Jasa</th>
								</tr>
								<tr>
									<td>Jangka Waktu Penyelesaian Pekerjaan ("JWPP") (Apabila tidak sama dengan JWP)</td>
									<td>'.$jwpp.'</td>
								</tr>
								<tr>
									<td>Jangka Waktu Perjanjian ("JWP") (Adalah : JWPP + Masa Pemeliharaan dan/atau Durasi Laporan)</td>
									<td>'.$jwp.'</td>
								</tr>
								<tr>
									<td rowspan="3">Ketersediaan Penyedia Barang/Jasa (usulan)</td>
									'.$analisa.'
								<tr>
									<td>Metode Pembayaran (usulan)</td>
									<td>'.$dataFPPBJ['desc_metode_pembayaran'].'</td>
								</tr>
								<tr>
									<td rowspan="9">Jenis Kontrak (usulan)</td>
									'.$po.'
								</tr>
								<tr>
									'.$GTC01.'
								</tr>
								<tr>
									'.$GTC02.'
								</tr>
								<tr>
									'.$GTC03.'
								</tr>
								<tr>
									'.$GTC04.'
								</tr>
								<tr>
									'.$GTC05.'
								</tr>
								<tr>
									'.$GTC06.'
								</tr>
								<tr>
									'.$GTC07.'
								</tr>
								<tr>
									'.$SPK.'
								</tr>
								<tr>
					 				<th rowspan="2" class="no" style="width: 20px">'.$no++.'</th>
					 				<th colspan="2" style="text-align:left;">Lainnya</th>
					 			</tr>
					 			<tr>
					 				<td>Keterangan</td>
					 				<td>'.$dataFPPBJ['desc'].'</td>
					 			</tr>
							</table>';
							$table_ .= '<table align="center" class="ttd">
								<tr>
									<td colspan="3" style="font-style: italic;">* FPPBJ ini telah disetujui oleh Pengguna Barang/Jasa, Pejabat Pengadaan dan Fungsi Pengadaan melalui sistem Aplikasi Kelogistikan.</td>
								</tr>
							</table>
							
						</body>';


		// echo $table_;die;
		$table_ = str_replace('style="float:left;"', '', $table_);
		$table_ = str_replace(
			array(
				base_url().'assets/images/check.png',
				base_url().'assets/images/check-box.png',
				base_url().'assets/images/NUSANTARA-REGAS-2.png'
			),
			array(
				$this->asset_data_uri('assets/images/check.png'),
				$this->asset_data_uri('assets/images/check-box.png'),
				$this->asset_data_uri('assets/images/NUSANTARA-REGAS-2.png')
			),
			$table_
		);
		$dompdf = new DOMPDF();
		$dompdf->set_option('enable_css_float', true);
		$dompdf->set_option('enable_html5_parser', true);
		$dompdf->load_html($table_);
		$dompdf->set_paper("A4", "portrait");
        // $dompdf->set_option('isHtml5ParserEnabled', TRUE);
		$dompdf->render();
		$dompdf->stream("FPPBJ.pdf", array("Attachment" => 1));	
	}

	//Export PDF per FKPBJ 
	public function fkpbj($id,$no="",$tanggal = ""){
		$nomor = $no;
		$tanggal = ($tanggal == "") ? date('Y-m-d') : $tanggal;
		//$view 	= $this->load->view('fppbj/list',null, TRUE);
		$table = '';
		$no  = 1;
		$dataFKPBJ = $this->fkm->get_fkpbj_by_id_fppbj($id);
		// print_r($dataFKPBJ);die;
		$dataAnalisaResiko = $this->fm->get_analisa_resiko($id);
		$dataAnalisaSwakelola = $this->fm->get_analisa_swakelola($id);
		$dataAnalisaDetail = $this->fm->get_data_analisa_risiko_detail($id);
		$getCat = array();
		if (count($dataAnalisaDetail)) {
			foreach ($dataAnalisaDetail as $key => $value) {
				$manusia 	= $this->setCategory($value['manusia']);
				$asset 		= $this->setCategory($value['asset']);
				$lingkungan = $this->setCategory($value['lingkungan']);
				$hukum 		= $this->setCategory($value['hukum']);

				if ($manusia == "extreme" || $asset == "extreme" || $lingkungan == "extreme" || $hukum == "extreme") {
					$category = 'E';
				}else if ($manusia == "high" || $asset == "high" || $lingkungan == "high" || $hukum == "high") {
					$category = 'H';
				}else  if ($manusia == "medium" || $asset == "medium" || $lingkungan == "medium" || $hukum == "medium") {
					$category = 'M';
				}else if ($manusia == "low" || $asset == "low" || $lingkungan == "low" || $hukum == "low") {
					$category = 'L';
				}else{
					$category = '';
				}
					
				array_push($getCat, $category);
			}
		}
		// print_r($getCat);die;
		if (in_array('E', $getCat, TRUE)){
			$getValCSMS = 'E';
		}else if (in_array('H', $getCat, TRUE)){
			$getValCSMS = 'H';
		}else if (in_array('M', $getCat, TRUE)){
			$getValCSMS = 'M';
		}else if (in_array('L', $getCat, TRUE)){
			$getValCSMS = 'L';
		}else{
			$getValCSMS = '';
		}
		$encode_jwpp = json_encode(array('start'=>$dataFKPBJ['jwpp_start'],'end' =>$dataFKPBJ['jwpp_end']));
		$encode_jwp = json_encode(array('start'=>$dataFKPBJ['jwp_start'],'end' =>$dataFKPBJ['jwp_end']));
		$jwpp = json_decode($encode_jwpp);
		$jwp = json_decode($encode_jwp);

		$date_jwpp = strtotime($jwpp->end) - strtotime($jwpp->start);
		$total_jwpp = round($date_jwpp / (60*60*24));

		$date_jwp = strtotime($jwp->end) - strtotime($jwp->start);
		$total_jwp = round($date_jwp / (60*60*24));

		if ($dataFKPBJ['jwpp_start'] != null && $dataFKPBJ['jwpp_end'] != null) {
			$jwpp = date('d M Y', strtotime(json_decode($encode_jwpp)->start)).' sampai '.date('d M Y', strtotime(json_decode($encode_jwpp)->end)).' ('.$total_jwpp.' Hari)';
		} else{
			$jwpp = '-';
		}
		if ($dataFKPBJ['jwp_start'] != null || $dataFKPBJ['jwp_end'] != null) {
			$jwp = date('d M Y',strtotime($jwp))." sampai ".date('d M Y',strtotime($dataFKPBJ['jwp_end']));	
		} else {
			$jwp = '-';
		}

		if ($dataFKPBJ['jwp_start'] != 0000-00-00 || $dataFKPBJ['jwp_end'] != 0000-00-00) {
			$jwp = date('d M Y', strtotime(json_decode($encode_jwp)->start)).' sampai '.date('d M Y', strtotime(json_decode($encode_jwp)->end)).' ('.$total_jwp.' Hari)';	
		} else {
			$jwp = '-';
		}
		// echo $jwpp.' = '.$jwp;die;
		if (!isset($tanggal)) {
			$tanggal_kesepakatan = '-';
		} else{
			$post_data = $this->input->post();
			$tanggal_kesepakatan = isset($post_data['tanggal']) ? $post_data['tanggal'] : '-';
		}
			$dataMaster = $this->fm->getFPPBJ();
			$no = 1;
			$dataAnalisa = $this->ex->get_analisa($id);
			// print_r($dataAnalisa);die;
			// echo $dataAnalisa['dpt_list_']->dpt.' - '.$dataAnalisa['dpt_list_']->usulan;die;
			if ($dataAnalisa['dpt_list_']->dpt == '' && $dataAnalisa['dpt_list_']->usulan == '') {
				// echo "string 1";die;
				$analisa = '<td>
								<span style="float:left;">
								<img src="'.base_url().'assets/images/check.png"></span> Tidak Ada</td>
								</tr>
								<tr>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Ada</td>
								</tr>
								<tr>
									<td>Keterangan : -</td>
								</tr>';
			} else{
				// echo "string 2";die;
				$get_dpt = $dataAnalisa['dpt_list'];
				// print_r($get_dpt);die;
				// $get = '';
				// foreach ($get_dpt as $key) {
				// 	if ($key != '') {
				// 		$get .= $key.', '; 
				// 	}
				// }
				$__usulan = $dataAnalisa['usulan'];
				if (!empty($get_dpt)) {
					$_dpt_choosed = implode(',', $get_dpt);
					$show_dpt = 'Keterangan :'.$_dpt_choosed.' <br> Usulan :'.$__usulan;
				} else {
					$show_dpt = 'Usulan : '.$__usulan;
				}
				
				// print_r($dataAnalisa);
				$analisa = '<td>
								<span style="float:left;">
										<img src="'.base_url().'assets/images/check.png"></span> Ada</td>
								</tr>
								<tr>
									<td><span style="float:left;">
										<img src="'.base_url().'assets/images/check-box.png"></span> Tidak Ada</td>
								</tr>
								<tr>
									<td>'.$show_dpt.'</td>
								</tr>';
			}
			$table.=' <tr>
						<td></td>'
						.'<td>'.$dataFKPBJ['jenis_pengadaan'].'</td>'
						.'<td>'.$dataFKPBJ['penggolongan_penyedia'].'</td>'
						.'<td>'.$dataFKPBJ['value'].'</td>'
						.'<td>'.$jwpp.'</td>'
						.'<td>'.$jwp.'</td>'
						.'<td>'.$dataFKPBJ['hps'].'</td>'
						.'<td>'.$dataFKPBJ['desc_metode_pembayaran'].'</td>'
						.'<td>'.$dataFKPBJ['jenis_kontrak'].'</td>
						</tr>';
			;
			$no_pr = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> PR : Nomor PR ...</td>';
			$no_pr_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> PR : Nomor PR ...</td>';
			$hps = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> HPS (Dalam Amplop Tertutup)</td>';
			$hps_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> HPS (Dalam Amplop Tertutup)</td>';
			$kak = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> KAK / Spesifikasi Teknis</td>';
			$kak_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> KAK / Spesifikasi Teknis</td>';
			$no_pr = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> PR : Nomor PR ...</td>';
			$no_pr_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> PR : Nomor PR ...</td>';
			$form_analisa_resiko = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Form Analisa Resiko</td>';
			$form_analisa_swakelola = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Swakelola</td>';
			if ($dataFKPBJ['tipe_pengadaan'] == 'jasa') {
				// echo "sss";
				if ($dataAnalisaResiko['id_fppbj'] != '' ) {
				   $form_analisa_resiko = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Form Analisa Resiko</td>';
				}else{
					$form_analisa_resiko = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Form Analisa Resiko</td>';
				} 
				if ($dataFKPBJ['hps'] != '0') {
					$hps = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> HPS (Dalam Amplop Tertutup)</td>';
				} else{
					$hps = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> HPS (Dalam Amplop Tertutup)</td>';
				}
				if ($dataFKPBJ['kak_lampiran'] != '') {
					$kak = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> KAK / Spesifikasi Teknis</td>';
				} else{
					$kak = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> KAK / Spesifikasi Teknis</td>';
				}
				if ($dataFKPBJ['no_pr'] != '') {
					$no_pr = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> PR : '.$dataFKPBJ['no_pr'].'</td>';
				} else{
					$no_pr = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> PR : Nomor PR ...</td>';
				}
			} else if ($dataFKPBJ['tipe_pengadaan'] == 'barang') {
				// $dataAnalisaSwakelola['id_fppbj'] != '' || 
				if ($dataFKPBJ['swakelola'] != '') {
				  $form_analisa_swakelola = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Swakelola</td>';
				} else{
					 $form_analisa_swakelola = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Swakelola</td>';
				}
				if ($dataFKPBJ['hps'] != 0) {
					$hps_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> HPS (Dalam Amplop Tertutup)</td>';
				} else{
					$hps_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> HPS (Dalam Amplop Tertutup)</td>';
				}
				if ( $dataFKPBJ['kak_lampiran'] != '') {
					$kak_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> KAK / Spesifikasi Teknis</td>';
				} else{
					$kak_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> KAK / Spesifikasi Teknis</td>';
				}
				if ($dataFKPBJ['no_pr'] != '') {
					$no_pr_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> PR : '.$dataFKPBJ['no_pr'].'</td>';
				} else{
					$no_pr_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> PR : Nomor PR ...</td>';
				}
				$barang = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Barang</td>';
			}else{
				$barang = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Barang</td>';
			}

			if($dataFKPBJ['jenis_pengadaan'] == 'jasa_konstruksi'){
				$jasa_konstruksi = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Jasa Konstruksi</td>';
			}else{
				$jasa_konstruksi = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Jasa Konstruksi</td>';
			}

			if($dataFKPBJ['jenis_pengadaan'] == 'jasa_konsultasi'){
				$jasa_konsultasi = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Jasa Konsultasi</td>';
			}else{
				$jasa_konsultasi = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Jasa Konsultasi</td>';
			}

			if($dataFKPBJ['jenis_pengadaan'] == 'jasa_lainnya'){
				$jasa_lainnya = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Jasa Lainnya</td>';
			}else{
				$jasa_lainnya = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Jasa Lainnya</td>';
			}

			if($dataFKPBJ['penggolongan_penyedia'] == 'perseorangan'){
				$perseorangan = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Perseorangan</td>';
			}else{
				$perseorangan = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Perseorangan</td>';
			}

			if($dataFKPBJ['penggolongan_penyedia'] == 'usaha_kecil'){
				$usaha_kecil = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Usaha Kecil (K)</td>';
			}else{
				$usaha_kecil = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Usaha Kecil (K)</td>';
			}

			if($dataFKPBJ['penggolongan_penyedia'] == 'usaha_menengah'){
				$usaha_menengah = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Usaha Menengah (M)</td>';
			}else{
				$usaha_menengah = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Usaha Menengah (M)</td>';
			}

			if($dataFKPBJ['penggolongan_penyedia'] == 'usaha_besar'){
				$usaha_besar = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Usaha Besar (B)</td>';
			}else{
				$usaha_besar = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Usaha Besar (B)</td>';
			}

			// if($getValCSMS == 'E' || $getValCSMS == 'H'){
			// 	$high = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>High</td>';
			// }else{
			// 	$high = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>High</td>';
			// }

			// if($getValCSMS == 'M'){
			// 	$medium = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>Medium</td>';
			// }else{
			// 	$medium = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Medium</td>';
			// }

			// if($getValCSMS == 'L'){
			// 	$low = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>Low</td>';
			// }else{
			// 	$low = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Low</td>';
			// }

			if ($getValCSMS == 'E' || $getValCSMS == 'H') {
				$checked_csms = '<tr>
									<td rowspan="3">Penggolongan CSMS Penyedia Barang/Jasa (Khusus Pengadaan Jasa dan sesuai hasil analisa risiko)</td>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>High</td>
								</tr>
								<tr>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Medium</td>
								</tr>
								<tr>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Low</td>
								</tr>';
			} else if ($getValCSMS == 'M') {
				$checked_csms = '<tr>
									<td rowspan="3">Penggolongan CSMS Penyedia Barang/Jasa (Khusus Pengadaan Jasa dan sesuai hasil analisa risiko)</td>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>High</td>
								</tr>
								<tr>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>Medium</td>
								</tr>
								<tr>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Low</td>
								</tr>';
			} elseif ($getValCSMS == 'L') {
				$checked_csms = '<tr>
									<td rowspan="3">Penggolongan CSMS Penyedia Barang/Jasa (Khusus Pengadaan Jasa dan sesuai hasil analisa risiko)</td>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>High</td>
								</tr>
								<tr>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Medium</td>
								</tr>
								<tr>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>Low</td>
								</tr>';
			} else {
				$checked_csms = '<tr>
									<td rowspan="3">Penggolongan CSMS Penyedia Barang/Jasa (Khusus Pengadaan Jasa dan sesuai hasil analisa risiko)</td>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>High</td>
								</tr>
								<tr>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Medium</td>
								</tr>
								<tr>
									<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Low</td>
								</tr>';
			}

			if($dataFKPBJ['jenis_kontrak'] == 'po'){
				$po = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>Purchase Order (PO)</td>';
			}else{
				$po = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Purchase Order (PO)</td>';
			}

			if($dataFKPBJ['jenis_kontrak'] == 'GTC01'){
				$GTC01 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>GTC01 - Kontrak Jasa Konstruksi non EPC</td>';
			}else{
				$GTC01 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>GTC01 - Kontrak Jasa Konstruksi non EPC</td>';
			}

			if($dataFKPBJ['jenis_kontrak'] == 'GTC02'){
				$GTC02 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>GTC02 - Kontrak Jasa Konsultan</td>';
			}else{
				$GTC02 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>GTC02 - Kontrak Jasa Konsultan</td>';
			}

			if($dataFKPBJ['jenis_kontrak'] == 'GTC03'){
				$GTC03 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>GTC03 - Kontrak Jasa Umum</td>';
			}else{
				$GTC03 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>GTC03 - Kontrak Jasa Umum</td>';
			}

			if($dataFKPBJ['jenis_kontrak'] == 'GTC04'){
				$GTC04 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>GTC04 - Kontrak Jasa Pemeliharaan</td>';
			}else{
				$GTC04 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>GTC04 - Kontrak Jasa Pemeliharaan</td>';
			}

			if($dataFKPBJ['jenis_kontrak'] == 'GTC05'){
				$GTC05 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>GTC05 - Kontrak Jasa Pembuatan Software</td>';
			}else{
				$GTC05 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>GTC05 - Kontrak Jasa Pembuatan Software</td>';
			}

			if($dataFKPBJ['jenis_kontrak'] == 'GTC06'){
				$GTC06 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>GTC06 - Kontrak Jasa Sewa Fasilitas dan Alat</td>';
			}else{
				$GTC06 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>GTC06 - Kontrak Jasa Sewa Fasilitas dan Alat</td>';
			}

			if($dataFKPBJ['jenis_kontrak'] == 'GTC07'){
				$GTC07 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>GTC07 - Kontrak Jasa Tenaga Kerja</td>';
			}else{
				$GTC07 = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>GTC07 - Kontrak Jasa Tenaga Kerja</td>';
			}
			if($dataFKPBJ['jenis_kontrak'] == 'spk'){
				$SPK = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>SPK</td>';
			}else{
				$SPK = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>SPK</td>';
			}

			$dataFKPBJ['jwpp'] = json_decode($dataFKPBJ['jwpp']);
			$dataFKPBJ['jwpp'] = date('d M Y', strtotime($dataFKPBJ['jwpp']->start))." sampai ".date('d M Y', strtotime($dataFKPBJ['jwpp']->end));
			$dataFKPBJ['jwp'] = json_decode($dataFKPBJ['jwp']);
			$dataFKPBJ['jwp'] = date('d M Y', strtotime($dataFKPBJ['jwp']->start))." sampai ".date('d M Y', strtotime($dataFKPBJ['jwp']->end));

		//  header("Content-type: application/vnd.ms-excel");
		//  header("Content-Disposition: attachment; filename=Laporan FPPBJ".default_date(date('Y-m-d')).".xls");
		// 	print_r($dataFKPBJ);

			$table_='<html>
						<head>
							<title></title>
							<style type="text/css">
								thead:before, thead:after { display: none; }
								tbody:before, tbody:after { display: none; }
									// @page{
									// 	size: A4 portrait;
									// 	// page-break-after : always;
										
									// }
									
									@media all{
										ol{
											padding-left : 20px;
											padding-top : -15px;
											padding-bottom : -15px;
										}
										
										// table { page-break-inside:avoid; }
										// tr    { page-break-inside: avoid; }
										thead { display:table-header-group; }
									}
								table {
									width: 705px;
									border : 1px solid #000;
									border-spacing : 0;
									align: center;
								}
								.no{
									vertical-align: top;
								}
								td, th {
									border : 1px solid #000;
									padding: 3px 5px;
									word-wrap: break-word;
								}
								// tr{
								// 	page-break-inside: avoid; 
								// }
								// tr td:nth-child(2) {
								// 		width: 280px;
								// 		border : 1px solid #000;
								// }
								.desc{
									margin-top: 50px;
									margin-bottom: 50px;
								}
								.desc, .desc td, .desc th{
									border: none !important;
								}
								span img{
									width: 15px !important;
									margin: 0 5px;
								}
								.ttd{
									width: 705px;
									margin-top: 25px;
								}
								.ttd td, .ttd th{
									padding: 5px;
								}
							</style>
						</head>
						<body>

							<table align="center">
								<tr>
									<td>
										<img src="'.base_url().'assets/images/NUSANTARA-REGAS-2.png" style="height: 45px">
									</td>
									<td>
										<div style="font-size: 14px;">
											FORMULIR KESEPAKATAN PENGADAAN BARANG/JASA (FKPBJ) 
										</div>
									</td>
								</tr>
							</table>
							<table align="center" style="border:none; margin-top: 25px">
								<tr>
									<td style="border:none; width: 170px">Nomor</td>
									<td style="border:none">: '.$nomor.' </td>
								</tr>
								<tr>
									<td style="border:none; width: 170px">Tanggal Kesepakatan</td>
									<td style="border:none">: '.default_date($tanggal_kesepakatan).' </td>
								</tr>
								<tr>
									<td style="border:none; width: 170px">Div/Dept</td>
									<td style="border:none">: '.$dataFKPBJ['division'].'</td>
								</tr>
								<tr>
									<td style="border:none; width: 170px">Judul Pengadaan</td>
									<td style="border:none">: '.$dataFKPBJ['nama_pengadaan'].'</td>
								</tr>
							</table>
							<table align="center" style="margin-top: 25px">
								<tr>
									<th style="width: 20px;">
										No.
									</th>
									<th>
										SYARAT KELENGKAPAN
									</th>
									<th style="width: 170px;">
										DATA
									</th>
								</tr>
								<tr>
									<th rowspan="9" style="vertical-align: top; width: 20px">'.$no++.'</th>
									<th colspan="2" style="text-align:left;">Kelengkapan Dokumen Permohonan</th>
								</tr>
								<tr>
									<td rowspan="4">Pengadaan Jasa</td>
									'.$form_analisa_resiko.'
								</tr>	
								<tr>
									'.$hps.'
								</tr>
								<tr>
									'.$kak.'
								</tr>
								<tr>
									'.$no_pr.'
								</tr>
								<tr>
									<td rowspan="4">Pengadaan Barang</td>
									'.$form_analisa_swakelola.'
								</tr>
								<tr>
									'.$hps_.'
								</tr>
								<tr>
									'.$kak_.'
								</tr>
								<tr>
									'.$no_pr_.'
								</tr>
								<tr>
									<th rowspan="12" style="vertical-align: top; width: 20px">'.$no++.'</th>
									<th colspan="2" style="text-align:left;">Uraian Pengadaan Barang/Jasa</th>
								</tr>
								<tr>
									<td rowspan="4">Penggolongan Penyedia Barang/Jasa (usulan)</td>
									'.$perseorangan.'
								</tr>
								<tr>
									'.$usaha_kecil.'
								</tr>
								<tr>
									'.$usaha_menengah.'
								</tr>
								<tr>
									'.$usaha_besar.'
								</tr>
								'.$checked_csms.'
							</table>
							<table align="center" style="page-break-before: always; margin-top: 25px">
								<tr>
									<th rowspan="18" style="vertical-align: top; width: 20px"></th>
									<th colspan="2" style="text-align:left;">Uraian Pengadaan Barang/Jasa</th>
								</tr>
								<tr>
									<td>Metode Pengadaan</td>
									<td>'.$dataFKPBJ['metode_name'].'</td>
								</tr>
								<tr>
									<td>Jangka Waktu Penyelesaian Pekerjaan ("JWPP") (Apabila tidak sama dengan JWP)</td>
									<td>'.$jwpp.'</td>
								</tr>
								<tr>
									<td>Jangka Waktu Perjanjian ("JWP") (Adalah : JWPP + Masa Pemeliharaan dan/atau Durasi Laporan)</td>
									<td>'.$jwp.'</td>
								</tr>
								<tr>
									<td rowspan="3">Ketersediaan Penyedia Barang/Jasa (usulan)</td>
									'.$analisa.'
								<tr>
									<td>Lingkup kerja</td>
									<td>'.$dataFKPBJ['lingkup_kerja'].'</td>
								</tr>
								<tr>
									<td>Metode Pembayaran (usulan)</td>
									<td>'.$dataFKPBJ['desc_metode_pembayaran'].'</td>
								</tr>
								<tr>
									<td rowspan="9">Jenis Kontrak (usulan)</td>
									'.$po.'
								</tr>
								<tr>
									'.$GTC01.'
								</tr>
								<tr>
									'.$GTC02.'
								</tr>
								<tr>
									'.$GTC03.'
								</tr>
								<tr>
									'.$GTC04.'
								</tr>
								<tr>
									'.$GTC05.'
								</tr>
								<tr>
									'.$GTC06.'
								</tr>
								<tr>
									'.$GTC07.'
								</tr>
								<tr>
									'.$SPK.'
								</tr>
								<tr>
					 				<th rowspan="2" class="no" style="width: 20px">'.$no++.'</td>
					 				<th colspan="2" style="text-align:left;">Lainnya</th>
					 			</tr>
					 			<tr>
					 				<td>Keterangan</td>
					 				<td>'.$dataFKPBJ['desc_dokumen_fppbj'].'</td>
					 			</tr>
							</table>';
							$table_ .= '<table align="center" class="ttd">
								<tr>
									<td colspan="3" style="font-style: italic;">* FPPBJ ini telah disetujui oleh Pengguna Barang/Jasa, Pejabat Pengadaan dan Fungsi Pengadaan melalui sistem Aplikasi Kelogistikan.</td>
								</tr>
							</table>
							
						</body>';


		// echo $table_;die;
		$dompdf = new DOMPDF();
		$dompdf->load_html($table_);
		$dompdf->set_paper("A4", "potrait");
        // $dompdf->set_option('isHtml5ParserEnabled', TRUE);
		$dompdf->render();
		$dompdf->stream("FKPBJ.pdf", array("Attachment" => 1));	
	}

	// Export PDF FP3 per FPPBJ
	function fp3($id){
		$data = $this->ex->get_exportFP3($id);
		$get_fppbj = $this->ex->fppbj($id);
		$post_data = $this->input->post();
		$nomor  	 = isset($post_data['no']) ? $post_data['no'] : '';
		$kepada 	 = isset($post_data['to']) ? $post_data['to'] : '';
		$pusat_biaya = isset($post_data['pb']) ? $post_data['pb'] : '';
		$tanggal 	 = isset($post_data['date']) ? $post_data['date'] : '';
		$pengguna 	 = isset($post_data['pbj']) ? $post_data['pbj'] : '';
		$kadep_	 	 = isset($post_data['kadep_']) ? $post_data['kadep_'] : '';
		$kadiv_	 	 = isset($post_data['kadiv_']) ? $post_data['kadiv_'] : '';
		$kadep	 	 = isset($post_data['kadep']) ? $post_data['kadep'] : '';
		$kadiv	 	 = isset($post_data['kadiv']) ? $post_data['kadiv'] : '';
		// Initialize table variable
		$table = '';
		// print_r($data);die;
		foreach ($data as $key => $value) {
			$key = $key +1;
			if($value['jwpp_start'] != null){
				$new_date = date('d M Y', strtotime($value['jwpp_start'])).' sampai '.date('d M Y', strtotime($value['jwpp_end']));
			}else{
				$new_date = '-';
			}

			if($value['jadwal_start_fppbj'] !== null){
				$fppbj_date = date('d M Y', strtotime($value['jadwal_start_fppbj'])).' sampai '.date('d M Y', strtotime($value['jadwal_end_fppbj']));
			}else{
				$fppbj_date = '-';
			}
			if ($get_fppbj['metode_pengadaan'] == 1) {
				$metode_pengadaan_fp3 = 'Pelelangan';
			} else if ($get_fppbj['metode_pengadaan'] == 2) {
				$metode_pengadaan_fp3 = 'Pemilihan Langsung';
			} else if ($get_fppbj['metode_pengadaan'] == 3) {
				$metode_pengadaan_fp3 = 'Swakelola';
			} else if ($get_fppbj['metode_pengadaan'] == 4) {
				$metode_pengadaan_fp3 = 'Penunjukan Langsung';
			} else {
				$metode_pengadaan_fp3 = 'Pengadaan Langsung';
			}
			$table .= '<tr>
							<td>'.$key.'</td>
							<td>'.$value['status'].'</td>
							<td>'.$value['nama_pengadaan_fppbj'].'</td>
							<td>'.$value['nama_pengadaan_fppbj'].'</td>
							<td>'.$value['nama_pengadaan'].'</td>
							<td>'.$metode_pengadaan_fp3.'</td>
							<td>'.$value['metode_pengadaan'].'</td>
							<!--<td>Rp. '.number_format($get_fppbj['idr_anggaran']).'</td>
							<td>Rp. '.number_format($value['idr_anggaran']).'</td>-->
							<td colspan="2">'.$new_date.'</td>
							<td>'.$fppbj_date.'</td>
							<td>'.$value['desc'].'</td>
						</tr>';
		}

		$page = '<!DOCTYPE html>
				<html lang="en">
				<head>
				    <title>Table Layout</title>
				    <style>
				        thead:before, thead:after { display: none; }
						tbody:before, tbody:after { display: none; }
							@page{
								size: A4 landscape;
								page-break-after : always;
								
							}
							
							@media all{
								ol{
									padding-left : 20px;
									padding-top : -15px;
									padding-bottom : -15px;
								}
								
								table { page-break-inside:avoid; }
								tr    { page-break-inside: avoid; }
								thead { display:table-header-group; }
							}
						table {
							/*width: 705px;*/
							width: 857px;
				    		font-size: 14px;
							border : 1px solid #000;
							border-spacing : 0;
							align: center;
						}
						.no{
							text-align: center;
							width: 20px;
						}
						td, th {
							border : 1px solid #000;
							padding: 3px 5px;
							word-wrap: break-word;
							text-align: center;
						}
						tr{
							page-break-inside: avoid; 
						}
						.desc{
							margin-top: 50px;
							margin-bottom: 50px;
						}
						.desc, .desc td, .desc th{
							border: none !important;
						}
						span img{
							width: 15px !important;
							margin: 0 5px;
						}
						.ttd{
							width: 705px;
							margin-top: 25px;
						}
						.ttd td, .ttd th{
							padding: 5px;
						}
						.is-yellow {background-color: #FECA57!important;}
						.is-red {background-color: #FF7675!important;}
						.is-blue {background-color: #54A0FF!important;}
						img {
							height: 10px;
						}
				    </style>

				</head>

				<body>
				    <table class="export" style="border-collapse: collapse" align="center"> 
				        <tr> 
				            <th colspan="3" style="text-align: left">
				                <img style="height:45px" src="'.base_url('/assets/images/NUSANTARA-REGAS-2.png').'"">
				            </th> 
				            <th colspan="4">
				                <div class="export-name">
				                    Formulir Perubahan Perencanaan Pengadaan B/J ("FP3")
				                </div>
				            </th> 
				        </tr> 
				    </table>
				    <table class="export no-border" style="border:none;" align="center">
				        <tr> 
				            <td style="border:none; text-align:left">
				                <ul class="export-info" style="list-style:none">
				                    <li>
				                        <span>Kepada</span> 
				                        <span>:</span> 
				                        <span>'.$kepada.'</span>
				                    </li>
				                    <li>
				                        <span>Dari</span> 
				                        <span>:</span> 
				                        <span>'.$value['nama_divisi'].'</span>
				                    </li>
				                    <li>
				                        <span>Pusat Biaya</span> 
				                        <span>:</span> 
				                        <span>'.$pusat_biaya.'</span>
				                    </li>
				                </ul>
				            </td>
				            <td style="vertical-align: top; width: 50%; border:none; text-align:left">
				                <ul class="export-info" style="list-style:none">
				                    <li>
				                        <span>Nomor</span> 
				                        <span>:</span> 
				                        <span>'.$nomor.'</span>
				                    </li>
				                    <li>
				                        <span>Tanggal</span> 
				                        <span>:</span> 
				                        <span>'.default_date($tanggal).'</span>
				                    </li>
				                </ul>
				            </td>
				        </tr>
				    </table>
				    <table class="export" style="margin-top: 15px; border-collapse:collapse" align="center">
				        <tr> 
				            <th rowspan="3">No</th> 
				            <th rowspan="3">Status</th> 
				            <th rowspan="3">
				                Nama Pengadaan B/J <br>
				                (Sesuai Perencanaan Pengadaan B/J 
				                Tahun 2018)
				            </th> 
				            <th colspan="7">Perubahan Perencanaan</th> 
				            <th rowspan="3">Keterangan</th>
				        </tr> 
				        <tr>
				            <th colspan="2">Nama</th>
				            <th colspan="2">Metode</th>
				            <!--<th colspan="2">Anggaran</th>-->
				            <th colspan="3">Jadwal</th>
				            <tr>
								<th>Lama</th>
								<th>Baru</th>
								<th>Lama</th>
								<th>Baru</th>
								<th colspan="2">Lama</th>
								<th>Baru</th>
				            </tr>
						</tr>
						'.$table.'
						<tr>
				            <td colspan="5" rowspan="2">
				                Pengguna Barang/Jasa
				                ('.$kadep_.')
				            </td>
				            <td colspan="6">
				                Persetujuan Perubahan
				            </td>
				        </tr>
				        <tr>
				            <td colspan="6">
				                Pengguna Barang/Jasa
				                ('.$kadiv_.')
				            </td>
				        </tr>
				        <tr>
				            <td colspan="5" class="sign-area" style="height:120px">

				            </td>
				            <td colspan="6" class="sign-area" style="height:120px">

				            </td>
				        </tr>
				        <tr>
				            <td colspan="5" style="text-align:center">('.$kadep.')</td>
				            <td colspan="6" style="text-align:center">('.$kadiv.')</td>
				        </tr>
				    </table> 

				</body>

				</html>';
	
				
		// print_r($page);die;
		// Convert > PDF
		$this->export_pdf('FORMULIR PERUBAHAN PERENCANAAN PENGADAAN B/J ("FP3")', $page, 'A4', 'landscape');
	}

	public function setCategory($val){
    	if ($val >= 1 && $val <= 4) {
		return 'low';
		// return '<span id="catatan" class="catatan green">L</span>';
		}else if ($val > 4 && $val <= 9) {
			return 'medium';
			// return '<span id="catatan" class="catatan yellow">M</span>';		
		}else if ($val >= 10 && $val <= 14) {
			return 'high';
			// return '<span id="catatan" class="catatan red">H</span>';
		}else if ($val >= 15 && $val <= 25) {
			return 'extreme';
			// return '<span id="catatan" class="catatan red">E</span>';
		}else{
			return false;
		}
    }
	
	// Export PDF Rekap Perencanaan FPPBJ per tahun
	function rekap_perencanaan($year = null)
	{
		$this->load->library('excel');
		$this->load->model('Main_model', 'mm');

		// fetch data	
		$dateHead 	= $this->date_week($year);
		$dateDetail = $this->date_detail($year);
		$fppbj_selesai		= $this->mm->get_fppbj_selesai($year);

		// print_r($dateDetail);die;
		// $data		= $this->ex->rekap_department($year);
		$count_fkpbj = $this->ex->count_rekap_department_fkpbj($year);
		$show_table = '<table class="rekap break" align="center" border=1>
					  <b><p align="center">
					    Rekapitulasi Pengadaan Barang/Jasa per Departemen 
					    </p></b>
					  <tr>
					    <th rowspan=3>No</th>
					    <th rowspan=3>Pengguna Barang/Jasa</th>
					    <th colspan=9>pelelangan</th>
					    <th colspan=9>Pemilihan Langsung</th>
					    <th colspan=9>Swakelola</th>
					    <th colspan=9>Penunjukan Langsung</th>
					    <th colspan=9>Pengadaan Langsung</th>
					    <th rowspan=3>Keterangan</th>
					  </tr>
					  <tr>
					    <td rowspan=2>Perencanaan</td>
					    <td colspan=8 align="center">Aktual</td>
					    <td rowspan=2>Perencanaan</td>
					    <td colspan=8 align="center">Aktual</td>
					    <td rowspan=2>Perencanaan</td>
					    <td colspan=8 align="center">Aktual</td>
					    <td rowspan=2>Perencanaan</td>
					    <td colspan=8 align="center">Aktual</td>
					    <td rowspan=2>Perencanaan</td>
					    <td colspan=8 align="center">Aktual</td>
					  </tr>
					  <tr>
					    <td>FPPBJ</td>
					    <td>FKPBJ</td>
					    <td>FP3 Nama Pengadaan</td>
					    <td>FP3 Timeline</td>
					    <td>FP3 Metode Pengadaan</td>
					    <td>FP3 Batal</td>
						<td>FPPBJ Baru</td>
						<td>FKPBJ Baru</td>

					    <td>FPPBJ</td>
					    <td>FKPBJ</td>
					    <td>FP3 Nama Pengadaan</td>
					    <td>FP3 Timeline</td>
					    <td>FP3 Metode Pengadaan</td>
					    <td>FP3 Batal</td>
						<td>FPPBJ Baru</td>
						<td>FKPBJ Baru</td>

					    <td>FPPBJ</td>
					    <td>FKPBJ</td>
					    <td>FP3 Nama Pengadaan</td>
					    <td>FP3 Timeline</td>
					    <td>FP3 Metode Pengadaan</td>
					    <td>FP3 Batal</td>
						<td>FPPBJ Baru</td>
						<td>FKPBJ Baru</td>

					    <td>FPPBJ</td>
					    <td>FKPBJ</td>
					    <td>FP3 Nama Pengadaan</td>
					    <td>FP3 Timeline</td>
					    <td>FP3 Metode Pengadaan</td>
					    <td>FP3 Batal</td>
						<td>FPPBJ Baru</td>
						<td>FKPBJ Baru</td>

					    <td>FPPBJ</td>
					    <td>FKPBJ</td>
					    <td>FP3 Nama Pengadaan</td>
					    <td>FP3 Timeline</td>
					    <td>FP3 Metode Pengadaan</td>
					    <td>FP3 Batal</td>
						<td>FPPBJ Baru</td>
						<td>FKPBJ Baru</td>
					  </tr>';

		$data = $this->ext->rekap_department($year);
		$div = $this->ext->getDivision();

		// Initialize procurement method variables
		$fppbj_pelelangan = 0; $perencanaan_pelelangan = 0; $fkpbj_pelelangan = 0;
		$fp3_np_pelelangan = 0; $fp3_tl_pelelangan = 0; $fp3_mp_pelelangan = 0; $fp3_hps_pelelangan = 0;
		$fp3_baru_np_pelelangan = 0; $fp3_baru_tl_pelelangan = 0; $fp3_baru_mp_pelelangan = 0; $fp3_baru_hps_pelelangan = 0;
		$fppbj_baru_pelelangan = 0; $fkpbj_baru_pelelangan = 0; $fp3_baru_pelelangan = 0; $fp3_lama_pelelangan = 0;
		$telat_pelelangan = 0; $__pelelangan = 0;

		$fppbj_pemilihan_langsung = 0; $perencanaan_pemilihan_langsung = 0; $fkpbj_pemilihan_langsung = 0;
		$fp3_np_pemilihan_langsung = 0; $fp3_tl_pemilihan_langsung = 0; $fp3_mp_pemilihan_langsung = 0; $fp3_hps_pemilihan_langsung = 0;
		$fp3_baru_np_pemilihan_langsung = 0; $fp3_baru_tl_pemilihan_langsung = 0; $fp3_baru_mp_pemilihan_langsung = 0; $fp3_baru_hps_pemilihan_langsung = 0;
		$fppbj_baru_pemilihan_langsung = 0; $fkpbj_baru_pemilihan_langsung = 0; $fp3_baru_pemilihan_langsung = 0; $fp3_lama_pemilihan_langsung = 0;
		$telat_pemilihan_langsung = 0; $__pemilihan_langsung = 0;

		$fppbj_swakelola = 0; $perencanaan_swakelola = 0; $fkpbj_swakelola = 0;
		$fp3_np_swakelola = 0; $fp3_tl_swakelola = 0; $fp3_mp_swakelola = 0; $fp3_hps_swakelola = 0;
		$fp3_baru_np_swakelola = 0; $fp3_baru_tl_swakelola = 0; $fp3_baru_mp_swakelola = 0; $fp3_baru_hps_swakelola = 0;
		$fppbj_baru_swakelola = 0; $fkpbj_baru_swakelola = 0; $fp3_baru_swakelola = 0; $fp3_lama_swakelola = 0;
		$telat_swakelola = 0; $__swakelola = 0;

		$fppbj_penunjukan_langsung = 0; $perencanaan_penunjukan_langsung = 0; $fkpbj_penunjukan_langsung = 0;
		$fp3_np_penunjukan_langsung = 0; $fp3_tl_penunjukan_langsung = 0; $fp3_mp_penunjukan_langsung = 0; $fp3_hps_penunjukan_langsung = 0;
		$fp3_baru_np_penunjukan_langsung = 0; $fp3_baru_tl_penunjukan_langsung = 0; $fp3_baru_mp_penunjukan_langsung = 0; $fp3_baru_hps_penunjukan_langsung = 0;
		$fppbj_baru_penunjukan_langsung = 0; $fkpbj_baru_penunjukan_langsung = 0; $fp3_baru_penunjukan_langsung = 0; $fp3_lama_penunjukan_langsung = 0;
		$telat_penunjukan_langsung = 0; $__penunjukan_langsung = 0;

		$fppbj_pengadaan_langsung = 0; $perencanaan_pengadaan_langsung = 0; $fkpbj_pengadaan_langsung = 0;
		$fp3_np_pengadaan_langsung = 0; $fp3_tl_pengadaan_langsung = 0; $fp3_mp_pengadaan_langsung = 0; $fp3_hps_pengadaan_langsung = 0;
		$fp3_baru_np_pengadaan_langsung = 0; $fp3_baru_tl_pengadaan_langsung = 0; $fp3_baru_mp_pengadaan_langsung = 0; $fp3_baru_hps_pengadaan_langsung = 0;
		$fppbj_baru_pengadaan_langsung = 0; $fkpbj_baru_pengadaan_langsung = 0; $fp3_baru_pengadaan_langsung = 0; $fp3_lama_pengadaan_langsung = 0;
		$telat_pengadaan_langsung = 0; $__pengadaan_langsung = 0;

		// Initialize fkpbj_baru_1_ variables
		$fkpbj_baru_1_pelelangan = 0;
		$fkpbj_baru_1_pemilihan_langsung = 0;
		$fkpbj_baru_1_swakelola = 0;
		$fkpbj_baru_1_penunjukan_langsung = 0;
		$fkpbj_baru_1_pengadaan_langsung = 0;

		$no = 1;
		foreach ($data as $key => $value) {
			$data_fppbj 	 = $this->ext->rekap_department_fppbj($year, $value['id_division'], 1);
			$data_perencanaan	 = $this->ext->rekap_fppbj_perencanaan($year, $value['id_division']);
			$data_fp3_np 	 = $this->ext->rekap_department_fp3($year, $value['id_division'], 'nama');
			$data_fp3_mp 	 = $this->ext->rekap_department_fp3($year, $value['id_division'], 'metode');
			$data_fp3_hps 	 = $this->ext->rekap_department_fp3($year, $value['id_division'], 'hapus');

			$data_fp3_baru_np 	 = $this->ext->rekap_department_fp3($year, $value['id_division'], 'nama', 2);
			$data_fp3_baru_mp 	 = $this->ext->rekap_department_fp3($year, $value['id_division'], 'metode', 2);
			$data_fp3_baru_hps 	 = $this->ext->rekap_department_fp3($year, $value['id_division'], 'hapus', 2);

			$data_fppbj_baru = $this->ext->rekap_department_fppbj($year, $value['id_division']);
			$data_fkpbj_baru = $this->ext->rekap_department_fkpbj_baru($year, $value['id_division'], 2);
			$data_fp3_baru = $this->ext->rekap_department_fp3_by_type($year, $value['id_division']);
			$data_fp3_lama = $this->ext->rekap_department_fp3_by_type($year, $value['id_division'], 1);

			$show_table .= '<tr>
					<td>' . $no . '</td>
					<td style="text-align: left;">' . $value['divisi_name'] . '</td>';

			if (count($data_fppbj) > 0) {

				$metodes = [
					1 => 'Pelelangan',
					2 => 'Pemilihan Langsung',
					3 => 'Swakelola',
					4 => 'Penunjukan Langsung',
					5 => 'Pengadaan Langsung'
				];

				foreach ($metodes as $key_metode => $metode) {
					$data_telat = $this->ext->count_rekap_department_fkpbj_telat($year, $value['id_division'], $metode);

					$data_fkpbj = $this->ext->count_rekap_department_fkpbj($year, $value['id_division'], $metode);

					$telat = $data_telat[0]['metode_' . $key_metode];


					$data_fp3_tl 	 = $this->ext->rekap_department_fp3_timeline($year, $value['id_division'], $metode);
					$data_fp3_baru_tl 	 = $this->ext->rekap_department_fp3_timeline($year, $value['id_division'], $metode, 2);

					$fppbj 	 = isset($data_fppbj[0]['metode_' . $key_metode]) ? $data_fppbj[0]['metode_' . $key_metode] : 0;
					$perencanaan = isset($data_perencanaan[0]['metode_' . $key_metode]) ? $data_perencanaan[0]['metode_' . $key_metode] : 0;
					$fkpbj 	 = isset($data_fkpbj[0]['metode_' . $key_metode]) ? $data_fkpbj[0]['metode_' . $key_metode] : 0;

					$fp3_np  = isset($data_fp3_np[0]['metode_' . $key_metode]) ? $data_fp3_np[0]['metode_' . $key_metode] : 0;
					$fp3_tl  = isset($data_fp3_tl[0]['metode_' . $key_metode]) ? $data_fp3_tl[0]['metode_' . $key_metode] : 0;
					$fp3_mp  = isset($data_fp3_mp[0]['metode_' . $key_metode]) ? $data_fp3_mp[0]['metode_' . $key_metode] : 0;
					$fp3_hps = isset($data_fp3_hps[0]['metode_' . $key_metode]) ? $data_fp3_hps[0]['metode_' . $key_metode] : 0;

					$fp3_baru_np  = isset($data_fp3_baru_np[0]['metode_' . $key_metode]) ? $data_fp3_baru_np[0]['metode_' . $key_metode] : 0;
					$fp3_baru_tl  = isset($data_fp3_baru_tl[0]['metode_' . $key_metode]) ? $data_fp3_baru_tl[0]['metode_' . $key_metode] : 0;
					$fp3_baru_mp  = isset($data_fp3_baru_mp[0]['metode_' . $key_metode]) ? $data_fp3_baru_mp[0]['metode_' . $key_metode] : 0;
					$fp3_baru_hps = isset($data_fp3_baru_hps[0]['metode_' . $key_metode]) ? $data_fp3_baru_hps[0]['metode_' . $key_metode] : 0;

					$fppbj_baru = isset($data_fppbj_baru[0]['metode_' . $key_metode]) ? $data_fppbj_baru[0]['metode_' . $key_metode] : 0;
					$fkpbj_baru = isset($data_fkpbj_baru[0]['metode_' . $key_metode]) ? $data_fkpbj_baru[0]['metode_' . $key_metode] : 0;
					$fp3_baru = isset($data_fp3_baru[0]['metode_' . $key_metode]) ? $data_fp3_baru[0]['metode_' . $key_metode] : 0;
					$fp3_lama = isset($data_fp3_lama[0]['metode_' . $key_metode]) ? $data_fp3_lama[0]['metode_' . $key_metode] : 0;

					$show_table .= ' <td>' . $perencanaan . '</td>
									<td>' . $fppbj . '</td>
									<td>' . $fkpbj . '</td>
									<td>' . $fp3_np . '</td>
									<td>' . $fp3_tl . '</td>
									<td>' . $fp3_mp . '</td>
									<td>' . $fp3_hps . '</td>
									<td>' . $fppbj_baru . '</td>
									<td>' . $fkpbj_baru . '</td>';

					${'fppbj_' . (str_replace(" ", "_", strtolower($metode)))} += $fppbj;
					${'perencanaan_' . (str_replace(" ", "_", strtolower($metode)))} += $perencanaan;
					${'fkpbj_' . (str_replace(" ", "_", strtolower($metode)))} += $fkpbj;

					${'fp3_np_' . (str_replace(" ", "_", strtolower($metode)))} += $fp3_np;
					${'fp3_tl_' . (str_replace(" ", "_", strtolower($metode)))} += $fp3_tl;
					${'fp3_mp_' . (str_replace(" ", "_", strtolower($metode)))} += $fp3_mp;
					${'fp3_hps_' . (str_replace(" ", "_", strtolower($metode)))} += $fp3_hps;

					${'fp3_baru_np_' . (str_replace(" ", "_", strtolower($metode)))} += $fp3_baru_np;
					${'fp3_baru_tl_' . (str_replace(" ", "_", strtolower($metode)))} += $fp3_baru_tl;
					${'fp3_baru_mp_' . (str_replace(" ", "_", strtolower($metode)))} += $fp3_baru_mp;
					${'fp3_baru_hps_' . (str_replace(" ", "_", strtolower($metode)))} += $fp3_baru_hps;

					${'fppbj_baru_' . (str_replace(" ", "_", strtolower($metode)))} += $fppbj_baru;
					${'fkpbj_baru_' . (str_replace(" ", "_", strtolower($metode)))} += $fkpbj_baru;
					${'fp3_baru_' . (str_replace(" ", "_", strtolower($metode)))} += $fp3_baru;
					${'fp3_lama_' . (str_replace(" ", "_", strtolower($metode)))} += $fp3_lama;
					${'telat_' . (str_replace(" ", "_", strtolower($metode)))} += $telat;
					${'__' . (str_replace(" ", "_", strtolower($metode)))} += $value['metode_' . $key_metode];
				}

				$show_table .= '<td></td></tr>';
			} else {
				$show_table .= '<td>' . $value['metode_1'] . '</td>
						<td>' . $value['metode_2'] . '</td>
						<td>' . $value['metode_3'] . '</td>
						<td>' . $value['metode_4'] . '</td>
						<td>' . $value['metode_5'] . '</td>
						<td></td>
					</tr>
					';

				$__pelelangan 			+= $value['metode_1'];
				$__pemilihan_langsung 	+= $value['metode_2'];
				$__swakelola 			+= $value['metode_3'];
				$__penunjukan_langsung 	+= $value['metode_4'];
				$__pengadaan_langsung 	+= $value['metode_5'];
			}

			$no++;
		}

		foreach ($div as $key => $value) {
			$data_fkpbj_baru_1 = $this->ext->rekap_department_fkpbj_baru($year, $value['id'], 2);

			$metodes = [
				1 => 'Pelelangan',
				2 => 'Pemilihan Langsung',
				3 => 'Swakelola',
				4 => 'Penunjukan Langsung',
				5 => 'Pengadaan Langsung'
			];

			foreach ($metodes as $key_metode => $metode) {
				$fkpbj_baru_1 = isset($data_fkpbj_baru_1[0]['metode_' . $key_metode]) ? $data_fkpbj_baru_1[0]['metode_' . $key_metode] : 0;
				${'fkpbj_baru_1_' . (str_replace(" ", "_", strtolower($metode)))} += $fkpbj_baru_1;
			}
		}

		$total_fkpbj = $fkpbj_pelelangan + $fkpbj_pemilihan_langsung + $fkpbj_swakelola + $fkpbj_penunjukan_langsung + $fkpbj_pengadaan_langsung;

		$total_perencanaan = $perencanaan_pelelangan + $perencanaan_pemilihan_langsung + $perencanaan_swakelola + $perencanaan_penunjukan_langsung + $perencanaan_pengadaan_langsung;

		$total_fppbj = $fppbj_pelelangan + $fppbj_pemilihan_langsung + $fppbj_swakelola + $fppbj_penunjukan_langsung + $fppbj_pengadaan_langsung;

		$total_telat = $telat_pelelangan + $telat_pemilihan_langsung + $telat_swakelola + $telat_penunjukan_langsung + $telat_pengadaan_langsung;

		$total_fp3_np = $fp3_np_pelelangan + $fp3_np_pemilihan_langsung + $fp3_np_swakelola + $fp3_np_penunjukan_langsung + $fp3_np_pengadaan_langsung;

		$total_fp3_tl = $fp3_tl_pelelangan + $fp3_tl_pemilihan_langsung + $fp3_tl_swakelola + $fp3_tl_penunjukan_langsung + $fp3_tl_pengadaan_langsung;

		$total_fp3_mp = $fp3_mp_pelelangan + $fp3_mp_pemilihan_langsung + $fp3_mp_swakelola + $fp3_mp_penunjukan_langsung + $fp3_mp_pengadaan_langsung;

		$total_fp3_hps = $fp3_hps_pelelangan + $fp3_hps_pemilihan_langsung + $fp3_hps_swakelola + $fp3_hps_penunjukan_langsung + $fp3_hps_pengadaan_langsung;

		$total_fp3_baru_np = $fp3_baru_np_pelelangan + $fp3_baru_np_pemilihan_langsung + $fp3_baru_np_swakelola + $fp3_baru_np_penunjukan_langsung + $fp3_baru_np_pengadaan_langsung;

		$total_fp3_baru_tl = $fp3_baru_tl_pelelangan + $fp3_baru_tl_pemilihan_langsung + $fp3_baru_tl_swakelola + $fp3_baru_tl_penunjukan_langsung + $fp3_baru_tl_pengadaan_langsung;

		$total_fp3_baru_mp = $fp3_baru_mp_pelelangan + $fp3_baru_mp_pemilihan_langsung + $fp3_baru_mp_swakelola + $fp3_baru_mp_penunjukan_langsung + $fp3_baru_mp_pengadaan_langsung;

		$total_fp3_baru_hps = $fp3_baru_hps_pelelangan + $fp3_baru_hps_pemilihan_langsung + $fp3_baru_hps_swakelola + $fp3_baru_hps_penunjukan_langsung + $fp3_baru_hps_pengadaan_langsung;

		$total_fppbj_baru = $fppbj_baru_pelelangan + $fppbj_baru_pemilihan_langsung + $fppbj_baru_swakelola + $fppbj_baru_penunjukan_langsung + $fppbj_baru_pengadaan_langsung;

		$total_fkpbj_baru_1 = $fkpbj_baru_1_pelelangan + $fkpbj_baru_1_pemilihan_langsung + $fkpbj_baru_1_swakelola + $fkpbj_baru_1_penunjukan_langsung + $fkpbj_baru_1_pengadaan_langsung;

		$total_fp3_baru = $fp3_baru_pelelangan + $fp3_baru_pemilihan_langsung + $fp3_baru_swakelola + $fp3_baru_penunjukan_langsung + $fp3_baru_pengadaan_langsung;

		$total_fp3_perencanaan = $fp3_lama_pelelangan + $fp3_lama_pemilihan_langsung + $fp3_lama_swakelola + $fp3_lama_penunjukan_langsung + $fp3_lama_pengadaan_langsung;

		$total_aktual = $total_fkpbj + $total_fkpbj_baru_1 + $total_fp3_np + $total_fp3_tl + $total_fp3_mp + $total_fp3_baru_np + $total_fp3_baru_tl + $total_fp3_baru_mp + $total_fp3_baru_hps + $total_fppbj_baru - $total_fp3_hps;

		$terealisasi = $total_fkpbj + $total_fp3_np + $total_fp3_tl + $total_fp3_mp + $total_fp3_hps;

		$terealisasi_baru = $total_fkpbj_baru_1 + $total_fp3_baru_np + $total_fp3_baru_tl + $total_fp3_baru_mp + $total_fppbj_baru - $total_fp3_baru_hps;

		$show_table .= '
					<tr>
						<td colspan="2"></td>
						<td style="font-weight: bold;">' . $perencanaan_pelelangan . '</td>
						<td style="font-weight: bold;">' . $fppbj_pelelangan . '</td>
						<td style="font-weight: bold;">' . $fkpbj_pelelangan . '</td>
						<td style="font-weight: bold;">' . $fp3_np_pelelangan . '</td>
						<td style="font-weight: bold;">' . $fp3_tl_pelelangan . '</td>
						<td style="font-weight: bold;">' . $fp3_mp_pelelangan . '</td>
						<td style="font-weight: bold;">' . $fp3_hps_pelelangan . '</td>
						<td style="font-weight: bold;">' . $fppbj_baru_pelelangan . '</td>
						<td style="font-weight: bold;">' . $fkpbj_baru_pelelangan . '</td>

						<td style="font-weight: bold;">' . $perencanaan_pemilihan_langsung . '</td>
						<td style="font-weight: bold;">' . $fppbj_pemilihan_langsung . '</td>
						<td style="font-weight: bold;">' . $fkpbj_pemilihan_langsung . '</td>
						<td style="font-weight: bold;">' . $fp3_np_pemilihan_langsung . '</td>
						<td style="font-weight: bold;">' . $fp3_tl_pemilihan_langsung . '</td>
						<td style="font-weight: bold;">' . $fp3_mp_pemilihan_langsung . '</td>
						<td style="font-weight: bold;">' . $fp3_hps_pemilihan_langsung . '</td>
						<td style="font-weight: bold;">' . $fppbj_baru_pemilihan_langsung . '</td>
						<td style="font-weight: bold;">' . $fkpbj_baru_pemilihan_langsung . '</td>

						<td style="font-weight: bold;">' . $perencanaan_swakelola . '</td>
						<td style="font-weight: bold;">' . $fppbj_swakelola . '</td>
						<td style="font-weight: bold;">' . $fkpbj_swakelola . '</td>
						<td style="font-weight: bold;">' . $fp3_np_swakelola . '</td>
						<td style="font-weight: bold;">' . $fp3_tl_swakelola . '</td>
						<td style="font-weight: bold;">' . $fp3_mp_swakelola . '</td>
						<td style="font-weight: bold;">' . $fp3_hps_swakelola . '</td>
						<td style="font-weight: bold;">' . $fppbj_baru_swakelola . '</td>
						<td style="font-weight: bold;">' . $fkpbj_baru_swakelola . '</td>

						<td style="font-weight: bold;">' . $perencanaan_penunjukan_langsung . '</td>
						<td style="font-weight: bold;">' . $fppbj_penunjukan_langsung . '</td>
						<td style="font-weight: bold;">' . $fkpbj_penunjukan_langsung . '</td>
						<td style="font-weight: bold;">' . $fp3_np_penunjukan_langsung . '</td>
						<td style="font-weight: bold;">' . $fp3_tl_penunjukan_langsung . '</td>
						<td style="font-weight: bold;">' . $fp3_mp_penunjukan_langsung . '</td>
						<td style="font-weight: bold;">' . $fp3_hps_penunjukan_langsung . '</td>
						<td style="font-weight: bold;">' . $fppbj_baru_penunjukan_langsung . '</td>
						<td style="font-weight: bold;">' . $fkpbj_baru_penunjukan_langsung . '</td>

						<td style="font-weight: bold;">' . $perencanaan_pengadaan_langsung . '</td>
						<td style="font-weight: bold;">' . $fppbj_pengadaan_langsung . '</td>
						<td style="font-weight: bold;">' . $fkpbj_pengadaan_langsung . '</td>
						<td style="font-weight: bold;">' . $fp3_np_pengadaan_langsung . '</td>
						<td style="font-weight: bold;">' . $fp3_tl_pengadaan_langsung . '</td>
						<td style="font-weight: bold;">' . $fp3_mp_pengadaan_langsung . '</td>
						<td style="font-weight: bold;">' . $fp3_hps_pengadaan_langsung . '</td>
						<td style="font-weight: bold;">' . $fppbj_baru_pengadaan_langsung . '</td>
						<td style="font-weight: bold;">' . $fkpbj_baru_pengadaan_langsung . '</td>
						<td style="font-weight: bold;"></td>
					</tr>
					<tr>
	                  	<td style="font-weight: bold; text-align:left;" colspan=4>Total Perencanaan</td>
	                    <td style="font-weight: bold;" colspan=44 align="center">' . count($fppbj_selesai->result())	. '</td>
	                  </tr>
	                  <tr>
	                    <td style="font-weight: bold; text-align:left;" colspan=4>Total Aktual</td>
	                    <td style="font-weight: bold;" colspan=44 align="center">' . $total_aktual . '</td>
					  </tr>
	                  <tr>
						<td style="font-weight: bold; text-align:left;" colspan=48>1) Tidak Terealisasi</td>
					  </tr>
					  <tr>
						<td style="font-weight: bold; text-align:left;" colspan=4> a) FPPBJ</td>
	                    <td style="font-weight: bold;" colspan=44 align="center">' . $total_fppbj . '</td>
					  </tr>
					  <tr>
						  <td style="font-weight: bold; text-align:left;" colspan=4>2) Terealisasi - Sesuai Perencanaan</td>
						  <td style="font-weight: bold;" colspan=44 align="center">' . $terealisasi . '</td>
					</tr>
					<tr>
					  <td style="font-weight: bold; text-align:left;" colspan=4> a) FKPBJ</td>
					  <td style="font-weight: bold;" colspan=44 align="center">' . $total_fkpbj . '</td>
					</tr>
					<tr>
					  <td style="font-weight: bold; text-align:left;" colspan=4>b) Total FP3 Timeline</td>
					  <td style="font-weight: bold;" colspan=44 align="center">' . $total_fp3_tl . '</td>
					</tr>
					<tr>
					  <td style="font-weight: bold; text-align:left;" colspan=4>c) Total FP3 Nama Pengadaan</td>
					  <td style="font-weight: bold;" colspan=44 align="center">' . $total_fp3_np . '</td>
					</tr>
					<tr>
					  <td style="font-weight: bold; text-align:left;" colspan=4>d) Total FP3 Metode Pengadaan</td>
					  <td style="font-weight: bold;" colspan=44 align="center">' . $total_fp3_mp . '</td>
					</tr>
					<tr>
					  <td style="font-weight: bold; text-align:left;" colspan=4>e) Total FP3 Batal</td>
					  <td style="font-weight: bold;" colspan=44 align="center">' . $total_fp3_hps . '</td>
					</tr>
					<tr>
					 	 <td style="font-weight: bold; text-align:left;" colspan=4>3) Terealisasi - Diluar Perencanaan (Baru)</td>
						  <td style="font-weight: bold;" colspan=44 align="center">' . $terealisasi_baru . '</td>
					</tr>
					<tr>
						<td style="font-weight: bold; text-align:left;" colspan=4> a) FPPBJ</td>
	                    <td style="font-weight: bold;" colspan=44 align="center">' . $total_fppbj_baru . '</td>
					  </tr>
					<tr>
					  <td style="font-weight: bold; text-align:left;" colspan=4> b) FKPBJ</td>
					  <td style="font-weight: bold;" colspan=44 align="center">' . $total_fkpbj_baru_1 . '</td>
					</tr>
					<tr>
					  <td style="font-weight: bold; text-align:left;" colspan=4>c) Total FP3 Timeline</td>
					  <td style="font-weight: bold;" colspan=44 align="center">' . $total_fp3_baru_tl . '</td>
					</tr>
					<tr>
					  <td style="font-weight: bold; text-align:left;" colspan=4>d) Total FP3 Nama Pengadaan</td>
					  <td style="font-weight: bold;" colspan=44 align="center">' . $total_fp3_baru_np . '</td>
					</tr>
					<tr>
					  <td style="font-weight: bold; text-align:left;" colspan=4>e) Total FP3 Metode Pengadaan</td>
					  <td style="font-weight: bold;" colspan=44 align="center">' . $total_fp3_baru_mp . '</td>
					</tr>
					<tr>
					  <td style="font-weight: bold; text-align:left;" colspan=4>f) Total FP3 Batal</td>
					  <td style="font-weight: bold;" colspan=44 align="center">' . $total_fp3_baru_hps . '</td>
					</tr>';
		$show_table .= '</table>';


		$page = '<!DOCTYPE html>
					<html lang="en">
					<head>
						<meta charset="UTF-8" />
						<meta name="viewport" content="width=device-width, initial-scale=1" />
						<style>
						
							thead:before, thead:after { display: none; }
							tbody:before, tbody:after { display: none; }
								@page{
									size: A4 landscape;
									page-break-after : avoid-column;
								}

								@media print {
								     body {margin-top: 50mm; margin-bottom: 50mm; 
								           margin-left: 0mm; margin-right: 0mm;}
								}
								
								@media print{
									ol{
										padding-left : 20px;
										padding-top : -15px;
										padding-bottom : -15px;
									}

									table { page-break-inside:auto }
									tr    { page-break-before:always;page-break-inside:avoid;page-break-after:always }
									td    { page-break-inside:avoid; page-break-after:auto }
									thead { display:table-header-group }
									tfoot { display:table-footer-group }
								}
							table {
								/*width: 705px;*/
								width: 857px;
					    		font-size: 14px;
								border : 1px solid #000;
								border-spacing : 0;
								align: center;
								border-collapse:collapse;
								page-break-inside: avoid !important;
							}
							.no{
								text-align: center;
								width: 20px;
							}
							td, th {
								border : 1px solid #000;
								padding: 3px 1px;
								word-wrap: break-word;
								text-align: center;
								page-break-inside: avoid !important;
								page-break-after:always;
								font-size: 11px;
								border-collapse:collapse;
								border-bottom: 0.01em solid black;
								position: relative;
							}
							tr{
								page-break-inside: avoid; 
								
								border-collapse:collapse;
							}
							.desc{
								margin-top: 50px;
								margin-bottom: 50px;
							}
							.desc, .desc td, .desc th{
								border: none !important;
							}
							span img{
								width: 15px !important;
								margin: 0 5px;
							}
							.ttd{
								width: 705px;
								margin-top: 25px;
							}
							.ttd td, .ttd th{
								padding: 5px;
							}
							.is-yellow {background-color: #FECA57!important;}
							.is-red {background-color: #FF7675!important;}
							.is-blue {background-color: #54A0FF!important;}
							img {
								height: 10px;
							}
							.row-small , .week-small {
								font-size: 10px;
								font-weight: 700;
								padding: 3px 1px;
								width: 2%;
							}
							.more_ {
								background-color: black;
							}
							.row-small:nth-child(1), .week-small:nth-child(1) {padding: 3px;}
							.row-small:nth-child(2), .week-small:nth-child(2) {padding: 3px;}
							.row-small:nth-child(3), .week-small:nth-child(3) {padding: 3px;}
							.row-small:nth-child(4), .week-small:nth-child(4) {padding: 3px;}
							.row-small:nth-child(5), .week-small:nth-child(5) {padding: 3px;}
							.row-small:nth-child(6), .week-small:nth-child(6) {padding: 3px;}
							.row-small:nth-child(7), .week-small:nth-child(7) {padding: 3px;}
							.row-small:nth-child(8), .week-small:nth-child(8) {padding: 3px;}
							.row-small:nth-child(9), .week-small:nth-child(9) {padding: 3px;}
							.row-small:nth-child(10) {padding: 4px;}
							.row-small:nth-child(11) {padding: 4px;}
							.row-small:nth-child(12) {padding: 4px;}
							.row-small:nth-child(13) {padding: 4px;}
							.row-small:nth-child(14) {padding: 4px;}
							.row-small:nth-child(15) {padding: 4px;}
							.row-small:nth-child(16) {padding: 4px;}
						</style>
					</head>
					
					<body>
						<div id="TableRekap">
							<b><p align="center">Perencanaan Pengadaan Barang/Jasa <br> Tahun ' . $year . '
							</p></b>
							<font color="#ced6e0">&#8718;</font> Grafik actual <span color="#fff" style="border: 1px #000 solid; height: 7px; width: 6px; display: inline-block;"></span> Grafik Perencanaan 
							' . $dateDetail . '
							<br>
							<br>
							<div style="margin-top: 2rem">
							<br><br>
								' . $show_table . '
								</div>
						</div>
					</body>
		</html>';

		header('Content-type: application/ms-excel');
		header('Content-Disposition: attachment; filename=Rekap Perencanaan' . $year . '.xls');
		header('Cache-Control: max-age=0');

		echo $page;
	}

	function removeDuplicates($array) {
	    $arr = [];   
	    foreach ($array as $obj => $val) {
	       $arr[$val] = $val;
	    }

	    return $arr;
	}

	// Export PDF Rekap Perencanaan FPPBJ per department per tahun
	function rekap_department($year = null){
		
		$title 	= 'Perencanaan Pengadaan B/J'.$year;
		$data	= $this->ex->rekap_department($year);
		
		// fetch data table
		foreach ($data as $key => $value) {
			$key = $key + 1;
			$table.= '<tr class="bold">
							<td>'.$key.'</td>
							<td colspan="7" style="text-align: left; font-weight: 700"><b>'.$value['kadiv'].'</b></td>
						</tr>';

			foreach ($value['detail'] as $key_ => $value_) {
				$key_ = $key_ + 1;
				$table.= '<tr>
								<td>'.$key_.'</td>
								<td style="text-align: left;">'.$value_['division'].'</td>
								<td>'.$value_['pelelangan'].'</td>
								<td>'.$value_['pemilihan_langsung'].'</td>
								<td>'.$value_['penunjukan_langsung'].'</td>
								<td>'.$value_['pengadaan_langsung'].'</td>
								<td>'.$value_['swakelola'].'</td>
								<td>&nbsp;</td>
							</tr>';
				
				$_pelelangan 			+= $value_['pelelangan'];
				$_pemilihan_langsung 	+= $value_['pemilihan_langsung'];
				$_penunjukan_langsung 	+= $value_['penunjukan_langsung'];
				$_pengadaan_langsung 	+= $value_['pengadaan_langsung'];
				$_swakelola 			+= $value_['swakelola'];
			}

			$table.='<tr class="bold">
						<td colspan="2" style="text-align: right; font-weight: 700">Sub total</td>
						<td>'.$_pelelangan.'</td>
						<td>'.$_pemilihan_langsung.'</td>
						<td>'.$_penunjukan_langsung.'</td>
						<td>'.$_pengadaan_langsung.'</td>
						<td>'.$_swakelola.'</td>
						<td></td>
					</tr>';

			$__pelelangan 			+= $_pelelangan;
			$__pemilihan_langsung 	+= $_pemilihan_langsung;
			$__penunjukan_langsung 	+= $_penunjukan_langsung;
			$__pengadaan_langsung 	+= $_pengadaan_langsung;
			$__swakelola 			+= $__swakelola;

		}

		$grand_total = $__pelelangan + $__pemilihan_langsung + $__penunjukan_langsung + $__pengadaan_langsung + $__swakelola;
		$table .= '<tr class="bold">
						<td colspan="2" style="text-align: right; font-weight: 700">Total</td>
						<td>'.$__pelelangan.'</td>
						<td>'.$__pemilihan_langsung.'</td>
						<td>'.$__penunjukan_langsung.'</td>
						<td>'.$__pengadaan_langsung.'</td>
						<td>'.$__swakelola.'</td>
						<td>'.$grand_total.'</td>
					</tr>';

		$page 	= '<!DOCTYPE html>
					<html lang="en">
					<head>
						<title>'.$title.'</title>
						<meta charset="UTF-8" />
						<meta name="viewport" content="width=device-width, initial-scale=1" />
						<style>
							thead:before, thead:after { display: none; }
							tbody:before, tbody:after { display: none; }
								@page{
									size: A4 landscape;
									page-break-after : always;
									
								}
								
								@media all{
									ol{
										padding-left : 20px;
										padding-top : -15px;
										padding-bottom : -15px;
									}
									
									table { page-break-inside:avoid; }
									tr    { page-break-inside: avoid; }
									thead { display:table-header-group; }
								}
							table {
								/*width: 705px;*/
								width: 857px;
					    		font-size: 14px;
								border : 1px solid #000;
								border-spacing : 0;
								align: center;
							}
							.no{
								text-align: center;
								width: 20px;
							}
							td, th {
								border : 1px solid #000;
								padding: 3px 5px;
								word-wrap: break-word;
								text-align: center;
							}
							tr{
								page-break-inside: avoid; 
							}
							tr td:nth-child(2) {
									width: 280px;
									border : 1px solid #000;
							}
							.desc{
								margin-top: 50px;
								margin-bottom: 50px;
							}
							.desc, .desc td, .desc th{
								border: none !important;
							}
							span img{
								width: 15px !important;
								margin: 0 5px;
							}
							.ttd{
								width: 705px;
								margin-top: 25px;
							}
							.ttd td, .ttd th{
								padding: 5px;
							}
							.is-yellow {background-color: #FECA57!important;}
							.is-red {background-color: #FF7675!important;}
							.is-blue {background-color: #54A0FF!important;}
							img {
								font-size: 10px;
								font-weight: 700;
							}
						</style>
					</head>
					
					<body>
					

						<!-- Data Table -->
						<div  class="break-before"></div>
						<div>
						<table id="dirkeu" class="export smaller-text" style="width: calc(95% - 15px * 2)" style="border-collapse: collapse;">
							<caption style="text-align: left; font-weight: 700">
								Rekapitulasi Rencana Pengadaan Barang/Jasa Tahun '.$year.'
							</caption>
							<tr>
								<th>No.</th>
								<th>Satuan Kerja</th>
								<th>pelelangan</th>
								<th>Pemilihan Langsung</th>
								<th>Penunjukan Langsung</th>
								<th>Pengadaan Langsung</th>
								<th>Swakelola</th>
								<th>Keterangan</th>
							</tr>
							'.$table.'
						</table>
						</div>
						
					</body>
					
					</html>';
		
		// print_r($page);die;

		$this->export_pdf($title, $page, 'A4', 'landscape');
					
	}

	function export_user(){
		$data = $this->ex->get_exportUser();

		foreach ($data as $key_ => $value_) {
				$key_ = $key_ + 1;
				$table.= '<tr>
								<td>'.$key_.'</td>
								<td>'.$value_['division'].'</td>
								<td>'.$value_['username'].'</td>
								<td>'.$value_['password'].'</td>
								<td>'.$value_['email'].'</td>
							</tr>';
		}

		$page = '<!DOCTYPE html>
				<html lang="en">
					<head>
						<title>'.$title.'</title>
						<meta charset="UTF-8" />
						<meta name="viewport" content="width=device-width, initial-scale=1" />
						<style>
							@import url("https://fonts.googleapis.com/css?family=Open+Sans:300,400,700");
							* {
								box-sizing: border-box;
								-moz-box-sizing: border-box;
							}
							#dirkeu {
								border-collapse: collapse;
								width: 1000px;
							}
							@page {
								margin-top: 0cm;
								margin-bottom: 0cm;
							}
							.break{
								page-break-inside: always;
								page-break-after  : always;
								page-break-before : always;
							}
							#dirkeu td, #dirkeu th {
								border: 1px solid #ddd;
								padding: 8px;
							}
					
							#dirkeu tr:nth-child(even){background-color: #f2f2f2;}
					
							#dirkeu .bold td {
								border: 2px solid #ddd;
							}
							* {
								box-sizing: border-box;
								-moz-box-sizing: border-box;
								}
								@page {
									margin-top: 0cm;
									margin-bottom: 0cm;
								}
								.break{
									page-break-inside: always;
									page-break-after  : always;
									page-break-before : always;
								}
								.break-before{
									page-break-before : always;
								}
								body {
									width: 100%;
									font-family: "Open Sans";
									background-color: #f9f9f9;
								}
								.border tr td , .border tr th {border: none; padding: 2px}
								.no-border tr td {border: none;}
								.no-border tr th {border: none;}
								tr.gery {background-color: #ddd;}
								.export {
								background-color: #fff;
								width: 1050px;
								border-right: 1px solid #ddd;
								margin: 0 15px; }
								.nopadding td {
									padding: 0!important;
								}
								.export td, .export th {
									vertical-align: middle;
									text-align: center;
									border-collapse: collapse;
									border-spacing: none;
									padding: 5px;
									word-wrap: break-word; }
								.export th {
									padding: 0; }
								.export-logo {
									margin: 5px;
									float: left; }
									.export-logo img {
									left: 15px;
									height: 35px; }
								.export-name {
									font-size: 1.2rem;
									font-weight: 400;
									margin: 15px;
									text-transform: uppercase; }
								.export-info li {
									display: flex; }
									.export-info li span {
									padding: 5px 15px;
									text-align: left; }
									.export-info li span:nth-child(1) {
										width: calc(40% - 15px * 2); }
									.export-info li span:nth-child(2) {
										width: calc(10% - 15px * 2); }
									.export-info li span:nth-child(3) {
										width: calc(60% - 15px * 2); }
								.export .sign-area {
									height: 71px; }
						
								ul li {list-style: none;}
								.sign-name {
									font-size: 13px;
								}
								.sign-position {
									font-size: 11px;
								}
								.smaller-text {
									font-size: 10px;
									width: 670px;
								}
								.is-yellow {
									background-color: #FECA57!important;
								}
								.is-red {
									background-color: #FF7675!important;
								}
								.is-blue {
									background-color: #54A0FF!important;
								}
								.row-small {
									border: 1px solid #ddd!important;
								}
								.gery th:last-child {
									border: 1px solid #ddd!important;
									background-color: #fff;
									color: #3b4758;
								}
								.content:nth-child(odd) {background-color: #fff!important}
								.content {background-color: #f9f9f9!important}
								.content td.row-small {background-color: white; color: #3b4758}
						</style>
					</head>
					
					<body>
						<!-- Data Table -->
						<div style="margin-top: 2rem">
						<table id="dirkeu" class="export smaller-text" style="width: calc(95% - 15px * 2)" style="border-collapse: collapse;">
							<caption style="text-align: left; font-weight: 700">
							</caption>
							<tr>
								<th>No.</th>
								<th>Divisi</th>
								<th>Username</th>
								<th>Password</th>
								<th>Email</th>
							</tr>
							'.$table.'
						</table>
						</div>
						
					</body>
				
				</html>';
//echo $page;die;
		$this->export_pdf('Data User Sistem', $page, 'A4', 'potrait');
	}

	function analisa_risiko($id_fppbj){

		$data = $this->ex->getAnalisaRisiko($id_fppbj);

		$page = '<!DOCTYPE html>
				<html lang="en">
					<head>
						<meta charset="UTF-8">
						<title>Document</title>
						<style>
							thead:before, thead:after { display: none; }
							tbody:before, tbody:after { display: none; }
								/*// @page{
								// 	size: A4 portrait;
								// 	// page-break-after : always;
									
								// }*/
								
								@media all{
									ol{
										padding-left : 20px;
										padding-top : -15px;
										padding-bottom : -15px;
									}
									
									/*// table { page-break-inside:avoid; }
									// tr    { page-break-inside: avoid; }*/
									thead { display:table-header-group; }
								}
							table {
								width: 705px;
								border : 1px solid #000;
								border-spacing : 0;
								align: center;
								border-collapse: collapse;
							}
							.no{
								vertical-align: top;
							}
							td, th {
								border : 1px solid #000;
								padding: 3px 5px;
								word-wrap: break-word;
								text-align: center;
							}
							tr{
								page-break-inside: avoid; 
							}
							.desc{
								margin-top: 50px;
								margin-bottom: 50px;
							}
							.desc, .desc td, .desc th{
								border: none !important;
							}
							span img{
								width: 15px !important;
								margin: 0 5px;
							}
							.ttd{
								width: 705px;
								margin-top: 25px;
							}
							.ttd td, .ttd th{
								padding: 5px;
							}
							.catatan {
								padding: 0 6px;
								border-radius: 25px;
								background-color: #ddd;
								color: #fff; 
							}
							.red {
								background-color: #e74c3c;
							}
							.yellow {
								background-color: #fed330;
								padding: 0 5px;
							}
							.green {
								background-color: #2ecc71;
								padding: 0 8px;
							}
						</style>
					</head>
					<body>
						<table align="center">
							<tr>
								<td style="width: 80px">
									<img src="'.base_url().'assets/images/NUSANTARA-REGAS-2.png" style="height: 45px" style="float: left">
								</td>
								<td>
									<div style="font-size: 14px; font-weight: 700; text-align: center;">
										PENILAIAN RISIKO
									</div>
								</td>
							</tr>
						</table>
						<table align="center" style="border:none; margin-top: 15px;">
							<tr>
								<td style="border:none; width:165px; vertical-align:top">Nama Proyek/Pekerjaan : </td>
								<td style="text-transform:uppercase; border:none; text-align: left; font-weight: 700">
									'.$data['nama_pengadaan'].'
								</td>
							</tr>
						</table>
						<table align="center" style="margin-top: 25px">
							<tr>
								<th rowspan="2" class="no">No</th>
								<th rowspan="2">Daerah Risiko</th>
								<th rowspan="2">Apa</th>
								<th colspan="5">Konsekuensi <br> L/M/H</th>
							</tr>
							<tr>
								<th>Manusia</th>
								<th>Aset</th>
								<th>Lingkungan</th>
								<th>Reputasi & Hukum</th>
								<th>Catatan</th>
							</tr>
							<tr class="q1"> 
								<td>1.</td> 
								<td style="text-align:left">Jenis Pekerjaan</td> 
								<td>Isi</td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span id="catatan" class="catatan">?</span></td> 
							</tr> 
							<tr class="q2"> 
								<td>2.</td> 
								<td style="text-align:left">Lokasi Kerja</td> 
								<td>Isi</td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span id="catatan" class="catatan">?</span></td>  
							</tr>
							<tr class="q3"> 
								<td>3.</td> 
								<td style="text-align:left">Materi Peralatan yang digunakan.</td> 
								<td>Isi</td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span id="catatan" class="catatan">?</span></td>  
							</tr> 
							<tr class="q4"> 
								<td>4.</td> 
								<td style="text-align:left">Potensi paparan terhadap bahaya tempat kerja.</td> 
								<td>Isi</td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span id="catatan" class="catatan">?</span></td> 
							</tr> 
							<tr class="q5"> 
								<td>5.</td> 
								<td style="text-align:left">Potensi paparan terhadap bahaya bagi personil.</td> 
								<td>Isi</td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span id="catatan" class="catatan">?</span></td>  
							</tr> 
							<tr class="q6"> 
								<td>6.</td> 
								<td style="text-align:left">Pekerjaan secara bersamaan oleh kontraktor berbeda.</td> 
								<td>Isi</td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span id="catatan" class="catatan">?</span></td>  
							</tr> 
							<tr class="q7"> 
								<td>7.</td> 
								<td style="text-align:left">Jangka Waktu Pekerjaan.</td> 
								<td>Isi</td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span id="catatan" class="catatan">?</span></td> 
							</tr> 
							<tr class="q8"> 
								<td>8.</td> 
								<td style="text-align:left">Konsekuensi pekerjaan potensian.</td> 
								<td>Isi</td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span id="catatan" class="catatan">?</span></td> 
							</tr> 
							<tr class="q9"> 
								<td>9.</td> 
								<td style="text-align:left">Pengalaman Kontraktor.</td> 
								<td>Isi</td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span id="catatan" class="catatan">?</span></td> 
							</tr> 
							<tr class="q10"> 
								<td>10.</td> 
								<td style="text-align:left">Paparan terhadap publisitas negatif.</td> 
								<td>Isi</td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span></span></td> 
								<td><span id="catatan" class="catatan">?</span></td> 
							</tr>
							<tr>
								<td colspan="2"></td>
								<th colspan="3" style="text-align: left">Hasil Penilaian Keseluruhan :</th>
								<th colspan="3" style="text-align: right">L (Risiko Rendah/Low)</th>
							</tr> 
						</table>
						<table align="center" style="margin-top: 25px">
							<tr>
								<td style="text-align: left">
									Dinilai Oleh: <br>
									'.$data['dinilai_'].' <br>
									<br>
									<br>
									<br>
									<br>
									Tanggal : 18 Oktober 2016
								</td>
								<td style="text-align: left">
									Disetujui Oleh: <br>
									'.$data['disetujui_'].'<br>
									<br>
									<br>
									<br>
									<br>
									Tanggal : 18 Oktober 2016
								</td>
							</tr>
						</table>
					</body>
				</html>';

		$this->export_pdf('PENILAIAN ANALISA RISIKO', $page, 'A4', 'potrait');
	}


	public function filter($year){
		
		$this->session->set_userdata('year', $year);
		$this->form = array(
			'form' => array(
				array(
					'field'			=> 'id_division',
					'type'			=> 'checkbox',
					'label'			=> 'Divisi',
					'source' 		=> $this->ex->getDivision($year)
				),array(
					'field'			=> array('start','end'),
					'type'			=> 'date_range',
					'label'			=> 'Rentang Waktu'
				)
			),

			'successAlert'=>'Berhasil mengubah data!',
		);

		$this->form['url'] = site_url('export/rekap_filter/'.$year);
		$this->form['button'] = array(
			array(
				'type' => 'submit',
				'label' => '<i class="fas fa-download"></i>&nbsp;Download',
			) ,
			array(
				'type' => 'cancel',
				'label' => 'Batal'
			)
		);
		echo json_encode($this->form);
	}
	
	public function rekap_filter($year)
	{
		$post = $this->input->post();
		$data = $this->ex->getDataRekapFilter($year,$post);

		$page = '<!DOCTYPE html>
		<html>
		<head>
			<meta charset="UTF-8" />
						<meta name="viewport" content="width=device-width, initial-scale=1" />
						<style>
						
							thead:before, thead:after { display: none; }
							tbody:before, tbody:after { display: none; }
								@page{
									size: A4 landscape;
									page-break-after : avoid-column;
								}

								@media print {
								     body {margin-top: 50mm; margin-bottom: 50mm; 
								           margin-left: 0mm; margin-right: 0mm;}
								}
								
								@media print{
									ol{
										padding-left : 20px;
										padding-top : -15px;
										padding-bottom : -15px;
									}

									table { page-break-inside:auto }
									tr    { page-break-before:always;page-break-inside:avoid;page-break-after:always }
									td    { page-break-inside:avoid; page-break-after:auto }
									thead { display:table-header-group }
									tfoot { display:table-footer-group }
								}
							table {
								/*width: 705px;*/
								width: 657px;
					    		font-size: 14px;
								border : 1px solid #000;
								border-spacing : 0;
								align: left;
								border-collapse:collapse;
								page-break-inside: avoid !important;
							}
							.no{
								text-align: center;
								width: 20px;
							}
							th {
								border : 1px solid #000;
								padding: 3px 0px;
								word-wrap: break-word;
								text-align: center;
								page-break-inside: avoid !important;
								page-break-after:always;
								font-size: 17px;
								border-collapse:collapse;
								border-bottom: 0.01em solid black;
								position: relative;
							}
							td {
								border : 1px solid #000;
								padding: 3px 0px;
								word-wrap: break-word;
								text-align: left;
								page-break-inside: avoid !important;
								page-break-after:always;
								font-size: 15px;
								border-collapse:collapse;
								border-bottom: 0.01em solid black;
								position: relative;
							}
							tr{
								page-break-inside: avoid; 
								
								border-collapse:collapse;
							}
							.desc{
								margin-top: 50px;
								margin-bottom: 50px;
							}
							.desc, .desc td, .desc th{
								border: none !important;
							}
							span img{
								width: 15px !important;
								margin: 0 5px;
							}
							.ttd{
								width: 605px;
								margin-top: 25px;
							}
							.ttd td, .ttd th{
								padding: 5px;
							}
							.is-yellow {background-color: #FECA57!important;}
							.is-red {background-color: #FF7675!important;}
							.is-blue {background-color: #54A0FF!important;}
							img {
								height: 10px;
							}
							.row-small , .week-small {
								font-size: 10px;
								font-weight: 700;
								padding: 3px 1px;
								width: 2%;
							}
							.more_ {
								background-color: black;
							}
						</style>
		</head>
		<body>
			<table align="center" border="1">
			<caption style="page-break-inside: avoid; font-size: 21px; font-weight: 700; margin-bottom: 10px;text-align:center;">Perencanaan Pengadaan Barang/Jasa <br> Tahun '.$year.'
							</caption>
				<thead>
					<tr>
						<th>No</th>
						<th>Divisi</th>
						<th>Nama Pengadaan</th>
						<th>Tahun Anggaran</th>
					</tr>
				</thead>
				<tbody>';
		$no = 1;
		if (count($data) > 0) {
			foreach ($data as $key => $value) {
				$page .='<tr>
							<td style="text-align:center;">'.$no.'</td>
							<td>'.$value['division'].'</td>
							<td>'.$value['name'].'</td>
							<td style="text-align:center;">'.$value['year'].'</td>
						</tr>
					';
					$no++;
			}
		} else {
			$page .= '<tr><td colspan="4" style="text-align:center;">Data Tidak Ada</td></tr>';
		}

		$page .= '</tbody>
			</table>
			</body>
		</html>';
		//echo $page;die;
		$this->export_pdf('Rekap Perencanaan pertahun '.$year, $page, 'A4', 'potrait');
	}

	function custom_rekap_perencanaan($division = null, $time = null){

		// fetch data	
		$dateHead 	= $this->date_week($year);
		$dateDetail = $this->date_detail($year);

		$data	= $this->ex->rekap_department($year);
		
		// fetch data table
		foreach ($data as $key => $value) {
			$key = $key + 1;
			$table.= '<tr class="bold">
							<td>'.$key.'</td>
							<td colspan="7" style="text-align: left; font-weight: 700"><b>'.$value['kadiv'].'</b></td>
						</tr>';

			$_pelelangan 			= 0;
			$_pemilihan_langsung 	= 0;
			$_penunjukan_langsung 	= 0;
			$_pengadaan_langsung 	= 0;
			$_swakelola 			= 0;
			foreach ($value['detail'] as $key_ => $value_) {
				$key_ = $key_ + 1;
				$table.= '<tr>
								<td>'.$key_.'</td>
								<td style="text-align: left;">'.$value_['division'].'</td>
								<td>'.$value_['pelelangan'].'</td>
								<td>'.$value_['pemilihan_langsung'].'</td>
								<td>'.$value_['penunjukan_langsung'].'</td>
								<td>'.$value_['pengadaan_langsung'].'</td>
								<td>'.$value_['swakelola'].'</td>
								<td>&nbsp;</td>
							</tr>';
				
				$_pelelangan 			+= $value_['pelelangan'];
				$_pemilihan_langsung 	+= $value_['pemilihan_langsung'];
				$_penunjukan_langsung 	+= $value_['penunjukan_langsung'];
				$_pengadaan_langsung 	+= $value_['pengadaan_langsung'];
				$_swakelola 			+= $value_['swakelola'];
			}

			$table.='<tr class="bold">
						<td colspan="2" style="text-align: right; font-weight: 700">Sub total</td>
						<td>'.$_pelelangan.'</td>
						<td>'.$_pemilihan_langsung.'</td>
						<td>'.$_penunjukan_langsung.'</td>
						<td>'.$_pengadaan_langsung.'</td>
						<td>'.$_swakelola.'</td>
						<td></td>
					</tr>';

			$__pelelangan 			+= $_pelelangan;
			$__pemilihan_langsung 	+= $_pemilihan_langsung;
			$__penunjukan_langsung 	+= $_penunjukan_langsung;
			$__pengadaan_langsung 	+= $_pengadaan_langsung;
			$__swakelola 			+= $__swakelola;

		}

		$grand_total = $__pelelangan + $__pemilihan_langsung + $__penunjukan_langsung + $__pengadaan_langsung + $__swakelola;
		$table .= '<tr class="bold" >
						<td colspan="2" style="text-align: right; font-weight: 700">Total</td>
						<td>'.$__pelelangan.'</td>
						<td>'.$__pemilihan_langsung.'</td>
						<td>'.$__penunjukan_langsung.'</td>
						<td>'.$__pengadaan_langsung.'</td>
						<td>'.$__swakelola.'</td>
						<td>'.$grand_total.'</td>
					</tr>';
					
		$page = '<!DOCTYPE html>
					<html lang="en">
					<head>
						<meta charset="UTF-8" />
						<meta name="viewport" content="width=device-width, initial-scale=1" />
						<style>
						
							thead:before, thead:after { display: none; }
							tbody:before, tbody:after { display: none; }
								@page{
									size: A4 landscape;
									page-break-after : avoid-column;
								}

								@media print {
								     body {margin-top: 50mm; margin-bottom: 50mm; 
								           margin-left: 0mm; margin-right: 0mm}
								}
								
								@media print{
									ol{
										padding-left : 20px;
										padding-top : -15px;
										padding-bottom : -15px;
									}

									table { page-break-inside:auto }
									tr    { page-break-before:always;page-break-inside:avoid;page-break-after:always }
									td    { page-break-inside:avoid; page-break-after:auto }
									thead { display:table-header-group }
									tfoot { display:table-footer-group }
								}
							table {
								/*width: 705px;*/
								width: 857px;
					    		font-size: 14px;
								border : 1px solid #000;
								border-spacing : 0;
								align: center;
								border-collapse:collapse;
								page-break-inside: avoid !important;
							}
							.no{
								text-align: center;
								width: 20px;
							}
							td, th {
								border : 1px solid #000;
								padding: 3px 1px;
								word-wrap: break-word;
								text-align: center;
								page-break-inside: avoid !important;
								font-size: 11px;
							}
							tr{
								page-break-inside: avoid; 
							}
							.desc{
								margin-top: 50px;
								margin-bottom: 50px;
							}
							.desc, .desc td, .desc th{
								border: none !important;
							}
							span img{
								width: 15px !important;
								margin: 0 5px;
							}
							.ttd{
								width: 705px;
								margin-top: 25px;
							}
							.ttd td, .ttd th{
								padding: 5px;
							}
							.is-yellow {background-color: #FECA57!important;}
							.is-red {background-color: #FF7675!important;}
							.is-blue {background-color: #54A0FF!important;}
							img {
								height: 10px;
							}
							.row-small , .week-small {
								font-size: 10px;
								font-weight: 700;
								padding: 3px 1px;
								width: 2%;
							}
							.more_ {
								background-color: black;
							}
							.row-small:nth-child(1), .week-small:nth-child(1) {padding: 3px;}
							.row-small:nth-child(2), .week-small:nth-child(2) {padding: 3px;}
							.row-small:nth-child(3), .week-small:nth-child(3) {padding: 3px;}
							.row-small:nth-child(4), .week-small:nth-child(4) {padding: 3px;}
							.row-small:nth-child(5), .week-small:nth-child(5) {padding: 3px;}
							.row-small:nth-child(6), .week-small:nth-child(6) {padding: 3px;}
							.row-small:nth-child(7), .week-small:nth-child(7) {padding: 3px;}
							.row-small:nth-child(8), .week-small:nth-child(8) {padding: 3px;}
							.row-small:nth-child(9), .week-small:nth-child(9) {padding: 3px;}
							.row-small:nth-child(10) {padding: 4px;}
							.row-small:nth-child(11) {padding: 4px;}
							.row-small:nth-child(12) {padding: 4px;}
							.row-small:nth-child(13) {padding: 4px;}
							.row-small:nth-child(14) {padding: 4px;}
							.row-small:nth-child(15) {padding: 4px;}
							.row-small:nth-child(16) {padding: 4px;}
						</style>
					</head>
					
					<body>
						<div>
							<table align="center" style="border-collapse: collapse; font-size:12px; page-break-inside: avoid !important;">
								<caption style="page-break-inside: avoid; font-size: 15px; font-weight: 700; margin-bottom: 10px">
									Perencanaan Pengadaan Barang/Jasa <br> Tahun '.$year.'
								</caption>
								<tr class="gery">
									<th rowspan="4" style="padding: 2px">No</th>
									<th rowspan="4" style="padding: 4px">Pengguna Barang/Jasa</th>
									<th rowspan="4" style="padding: 4px">Nama Pengadaan Barang/Jasa</th>
									<th rowspan="4" style="padding: 4px">Metode Pengadaan</th>
									<th rowspan="4" style="padding: 4px">Anggaran (include PPN 10%) </th>
									<th rowspan="4" style="padding: 4px">Jenis Pengadaan</th>
								'.$dateHead.'
								'.$dateDetail.'
								<div style="margin-top: 2rem">
								<table class="rekap break" align="center">
									<caption style="font-size: 15px; font-weight: 700; margin-bottom: 10px; page-break-before: always;">
										Rekapitulasi Pengadaan Barang/Jasa per Departemen 
									</caption>
									<tr>
										<th>No</th>
										<th>Satuan Kerja</th>
										<th>pelelangan</th>
										<th>Pemilihan Langsung</th>
										<th>Penunjukan Langsung</th>
										<th>Pengadaan Langsung</th>
										<th>Swakelola</th>
										<th>Keterangan</th>
									</tr>
									'.$table.'
								</table>
								</div>
							</table>
						</div>
						
					</body>
		</html>';

		// print_r($page);die;

		$this->export_pdf("Rekap Perencanaan Pengadaan - ".$year.".pdf", $page, 'A4', 'landscape');
	}

	public function filter_rekap_perencanaan(){
		print_r($this->input->post());die;
		$this->custom_rekap_perencanaan();
		echo json_encode(array('status' => 'success'));
	}

	

	//======================================================//
	//======================================================//
	//					OTHER FUCTION						//
	//======================================================//
	//======================================================//
	function date_week($year=null){
		if ($year == null) {
			$year = date('Y');
		} 
		if (!defined('NL')) {
			define('NL', "\n");
		}

		$year           = (int)$year; // Ensure year is integer
		$firstDayOfYear = mktime(0, 0, 0, 1, 1, $year);
		$nextMonday     = strtotime('monday', $firstDayOfYear);
		$nextSunday     = strtotime('sunday', $nextMonday);
		$_month			= 0;
		$_week 			= 1;
		$weekly 		= array();
		$table 			= '';

		while (date('Y', $nextMonday) == $year) {
			$month = date('M', $nextMonday);

			$weekly[$month][] = $_week;

			// echo "<td>".$_week."</td>";

			$nextMonday = strtotime('+1 week', $nextMonday);
			$nextSunday = strtotime('+1 week', $nextSunday);

			$_week++;
		}
		// echo $_week;
		$_week = $_week - 1;
		// print_r($weekly);die;
		// --------- HEADER -------
		$table = '<table align="center" style="max-width:100%;min-width:100%;">
								<tr class="gery">
									<th rowspan="4" style="padding: 2px;width:10px; height:10px;">No</th>
									<th rowspan="4" style="padding: 4px;width:10px; height:10px;">Pengguna Barang/Jasa</th>
									<th rowspan="4" style="padding: 4px;width:40px; height:10px;">Nama Pengadaan Barang/Jasa</th>
									<th rowspan="4" style="padding: 4px;width:20px; height:10px;">Metode Pengadaan</th>
									<th rowspan="4" style="padding: 4px;width:20px; height:10px;">Anggaran (include PPN 10%) </th>
									<th rowspan="4" style="padding: 4px;width:20px; height:10px;">Jenis Pengadaan</th>
									<th rowspan="4" style="padding: 4px;width:20px; height:10px;">Status</th>
									<th rowspan="4" style="padding: 4px;width:20px; height:10px;">Status Perencanaan</th>
									<th rowspan="4" style="padding: 4px;width:20px; height:10px;">PIC</th>
									<th rowspan="4" style="padding: 4px;width:20px; height:10px;">Keterangan</th>
									<th colspan="'.$_week.'" style="vertical-align: middle">
										<font color="#FECA57">&#8718;</font> Persiapan & Permohonan Pengadaan (PP1) - <font color="#FF7675">&#8718;</font> 
										Proses Pengadaan (PP2) - <font color="#54A0FF">&#8718;</font>
										Pelaksanaan Pekerjaan (PP3)
									</th>
								</tr>
								<tr>
									<td style="font-weight:700" colspan='.$_week.'>'.$year.'</td>
								</tr>
							';

		// --------- YEAR ---------
		// $table .= "<tr>";
		// 	$table .= "<td style='font-weight:700' colspan='".$_week."'>".$year."</td>";
		// $table .= "</tr>";

		
	
			// --------- MONTH ---------
			$table .= "<tr>";
			foreach ($weekly as $month => $value) {
				$month_ = count($weekly[$month]);
					$table .= "<td class='month-row' style='font-weight:700' colspan='".$month_."'>".$month."</td>";
			}
			$table .= "</tr>";

				// --------- WEEK ---------
				
				$table .= "<tr>";
				foreach ($weekly as $month => $value) {
					// print_r($value);die;
					foreach ($value as $week => $day) {
						$table .= "<td class='week-small' id='".$week."'>".($week+1)."</td>";
					}
				}
				$table .= "</tr>";

		return $table;
	}

	function date_week_($year=null, $jwpp, $metode){
		if (!defined('NL')) {
			define('NL', "\n");
		}
		$jwpp 			= json_decode($jwpp);
		$table			= '';
		// echo $metode;die;
		
		$get_year = explode(',',$year);

		$year = $get_year[0];

		// echo $year." <br>";

		$metode = trim($metode);
		// DAY BASED ON METODE PROC
		$metode_day		= 0;
		if ($metode == "Pelelangan") {
			$metode_day = 13; //60 hari
		}else if ($metode == "Pengadaan Langsung") {
			$metode_day = 1;// 10 hari
		}else if ($metode == "Pemilihan Langsung"){
			$metode_day = 6; //45 hari
		}else if ($metode == "Swakelola"){
			$metode_day = 0;
		}else if ($metode == "Penunjukan Langsung") {
			$metode_day = 3;// 20 hari
		}else{
			// $metode_day = 1;
		}

		//Variable 
		$start			= $this->get_week($jwpp->start);
		$end			= $this->get_week($jwpp->end);
		$start_red		= $start - $metode_day;
		$end_red		= $start;
		$start_yellow	= $start_red - 2;
		$end_yellow		= $end_red ;
		// echo $jwpp->end." <br>";
		if (($jwpp->end > "2019-12-28") && (intval($end) == 1)) {
			// echo $end;
			$end = 52;
			// echo "----".$start." > start - ".$end." > end - ".$metode." > metode - ".$metode_day." > metode day<br>";
			
		}else{
			$end = $end;
			// echo $start." > start - ".$end." > end - ".$metode." > metode - ".$metode_day." > metode day<br>";
		}
		// echo $start." > start - ".$end." > end - ".$metode." > metode - ".$metode_day." > metode day<br>";

		$year           = (int)$year; // Ensure year is integer
		$firstDayOfYear = mktime(0, 0, 0, 1, 1, $year);
		$nextMonday     = strtotime('monday', $firstDayOfYear);
		$nextSunday     = strtotime('sunday', $nextMonday);
		$weekly 		= array();
		$_month			= 0;
		$_week 			= 1;
		$_yellow		= 'class="is-yellow"';
		$_red			= 'class="is-red"';
		$_blue			= 'class="is-blue"';
		

		// print_r($start_red);
		while (date('Y', $nextMonday) == $year) {
			$month = date('M', $nextMonday);

			$weekly[$month][] = $_week;

			$nextMonday = strtotime('+1 week', $nextMonday);
			$nextSunday = strtotime('+1 week', $nextSunday);

			$_week++;
		}
			// --------- WEEK ---------
			foreach ($weekly as $month => $value) {
				foreach ($value as $week) {
					//echo $end_red." Ini adalah end red - ".$start_red." Ini adalah start red - ".$week." Ini adalah week - ".$end." Ini adalah end <br>";
					// echo $week." > ini adalah week <br>";
					if ($week >= $start && $week <= $end) {
						// BLUE
						$table .= "<td id='".sprintf('%02d', $week)."' class='row-small is-blue' style='border-bottom:0.01em solid black' bgcolor='#54A0FF'>&nbsp;</td>";
					}else if ($week >= $start_red && $week <= $end_red){
						// RED
						$table .= "<td id='".sprintf('%02d', $week)."' class='row-small is-red' style='border-bottom:0.01em solid black' bgcolor='#FF7675'>&nbsp;</td>";
					}else if ($week >= $start_yellow && $week <= $end_yellow){
						// YELLOW
						$table .= "<td id='".sprintf('%02d', $week)."' class='row-small is-yellow' style='border-bottom:0.01em solid black' bgcolor='#FECA57'>&nbsp;</td>";
					}else{
						// PLAIN
						$table .= "<td class='row-small' id='".sprintf('%02d', $week)."' style='border-bottom:0.01em solid black'>&nbsp;</td>";//border-bottom:1px solid black
					}
				}
			}

		// print_r($table);
		return $table;
	}

	function get_week($ddate=null){
		// $ddate = "2018-04-07";					
		$date = new DateTime($ddate);
		$week = $date->format("W");

		return $week;
	}

	function date_detail($year = null)
	{
		$data = $this->ex->rekap_perencanaan($year);
		$dateHead = $this->date_week($year);

		$table = '';
		$table .= $dateHead;
		foreach ($data as $divisi => $value) {
			$jwpp_start = isset($value['jwpp_start']) ? $value['jwpp_start'] : '';
			$jwpp_end = isset($value['jwpp_end']) ? $value['jwpp_end'] : '';
			$metode_pengadaan = isset($value['metode_pengadaan']) ? $value['metode_pengadaan'] : '';
			$time_range = json_encode(array('start' => $jwpp_start, 'end' => $jwpp_end));
			$week 		= $this->date_week_($year, $time_range, $metode_pengadaan);

			foreach ($value as $key => $value_) {

				if (count($value_) > 0) {
					$table 		.= '<tr class="content">
										<td colspan="62" style="text-align: left; font-weight:bold;">' . $divisi . " - " . $key . '</td>
									</tr>';

					foreach ($value_ as $key_ => $value__) {
						if ($value__['jenis_pengadaan'] == 'jasa_lainnya') {
							$jenis_pengadaan = 'Jasa Lainnya';
						} else if ($value__['jenis_pengadaan'] == 'jasa_konstruksi') {
							$jenis_pengadaan = 'Jasa Konstruksi';
						} else if ($value__['jenis_pengadaan'] == 'jasa_konsultasi') {
							$jenis_pengadaan = 'Jasa Konsultasi';
						} else if ($value__['jenis_pengadaan'] == 'jasa_lainnya') {
							$jenis_pengadaan = 'Jasa Lainnya';
						} else if ($value__['jenis_pengadaan'] == 'stock') {
							$jenis_pengadaan = 'Stock';
						} else if ($value__['jenis_pengadaan'] == 'non_stock') {
							$jenis_pengadaan = 'Non-Stock';
						} else {
							$jenis_pengadaan = '-';
						}
						$time_range_ = $time_range = json_encode(array('start' => $value__['jwpp_start'], 'end' => $value__['jwpp_end']));

						$week_ 		= $this->date_week_($year, $time_range_, $value__['metode_pengadaan']);

						$pic = $this->ex->getPicById($value__['id_pic']);
						$pic_name = (is_array($pic) && isset($pic['name']) && $pic['name'] !== '') ? $pic['name'] : '-';
						$get_fkpbj 	= $this->ex->get_fkpbj($value__['id_fppbj']);
						$get_fp3 	= $this->ex->get_fp3($value__['id_fppbj']);
						$no__ = $key_ + 1;

						if ($value__['idr_anggaran'] != '0.00') {
							$cur = 'Rp. ' . number_format($value__['idr_anggaran']) . '';
						} else {
							$cur = 'USD. ' . number_format($value__['usd_anggaran']) . '';
						}

						$status_perencanaan = ($value__['is_perencanaan'] == 1) ? 'Perencanaan' : 'Diluar perencanaan';
						$status_approved = ($value__['is_reject'] == 1) ? ' (Direvisi) ' : '';

						if (!empty($get_fkpbj) && !empty($get_fp3)) {
							$div_head = '	<td id="' . $value__['id_fppbj'] . '" rowspan=3>' . $no__ . '</td>
											<td rowspan=3>' . $value__['divisi'] . '</td>';
						} else if (!empty($get_fkpbj) && empty($get_fp3)) {
							$div_head = '	<td id="' . $value__['id_fppbj'] . '" rowspan=2>' . $no__ . '</td>
											<td rowspan=2>' . $value__['divisi'] . '</td>';
						} else if (empty($get_fkpbj) && !empty($get_fp3)) {
							$div_head = '	<td id="' . $value__['id_fppbj'] . '" rowspan=2>' . $no__ . '</td>
											<td rowspan=2>' . $value__['divisi'] . '</td>';
						} else {
							if ($value__['idr_anggaran'] != '0.00') {
								$cur = 'Rp. ' . number_format($value__['idr_anggaran']) . '';
							} else {
								$usd_anggaran = isset($value__['usd_anggaran']) ? $value__['usd_anggaran'] : 0;
								$cur = 'USD. ' . number_format($usd_anggaran) . '';
							}

							$div_head = '	<td id="' . $value__['id_fppbj'] . '">' . $no__ . '</td>
											<td>' . $value__['divisi'] . '</td>';
						}

						$table 		.= '<tr class="content" style="border-bottom:0.01em solid black;">
											' . $div_head . '
											<td>' . $value__['nama_pengadaan'] . '</td>
											<td>' . $value__['metode_pengadaan'] . '</td>
											<td>' . $cur . '</td>
											<td>' . $jenis_pengadaan . '</td>
											<td>FPPBJ</td>
											<td>' . $status_perencanaan . '</td>
											<td>' . $pic_name . '</td>
											<td>' . (isset($value__['desc_pengadaan']) ? $value__['desc_pengadaan'] : '') . '</td>
											' . $week_ . '
										</tr>
										';

						if ($value__['is_status'] == 2) {
							if ($value__['is_approved'] == 3) {
								$status_approved_fk = '';
							} else {
								$status_approved_fk = ' (belum selesai approval) ';
							}
						} else if ($value__['is_status'] == 1) {
							$appr_1 = $value__['is_approved'] == 3 && ($value__['idr_anggaran'] <= 100000000 || ($value__['idr_anggaran'] > 100000000 && $value__['metode'] == 3));
							$appr_2 = $value__['is_approved'] == 4 && $value__['idr_anggaran'] > 100000000;
							if (($appr_1) || ($appr_2)) {
								$status_approved_fp3 = '';
							} else {
								$status_approved_fp3 = ' (belum selesai approval) ';
							}
						}

						if (!empty($get_fkpbj)) {
							$start_date = $get_fkpbj['jwpp_start'];
							$metode = trim($get_fkpbj['metode_pengadaan_name']);
							if ($metode == "Pelelangan") {
								$metode_day = 60; //60 hari
							} else if ($metode == "Pengadaan Langsung") {
								$metode_day = 10; // 10 hari
							} else if ($metode == "Pemilihan Langsung") {
								$metode_day = 45; //45 hari
							} else if ($metode == "Swakelola") {
								$metode_day = 0;
							} else if ($metode == "Penunjukan Langsung") {
								$metode_day = 20; // 20 hari
							} else {
								//$metode_day = 1;
							}
							$start_yellow = $metode_day + 14;
							$end_yellow = $metode_day + 1;

							$yellow_start = date('Y-m-d', strtotime('-' . $start_yellow . 'days', strtotime($start_date)));
							$yellow_end = date('Y-m-d', strtotime('-' . $end_yellow . 'days', strtotime($start_date)));

							$entry_date = strtotime($get_fkpbj['entry_stamp']);
							$yellow_start_ = strtotime($yellow_start);
							$yellow_end_ = strtotime($yellow_end);

							if ($entry_date > $yellow_end_) {
								$date1 = $get_fkpbj['entry_stamp'];
								$date2 = $yellow_end;

								$diff = abs(strtotime($date1) - strtotime($date2));

								$years = floor($diff / (365 * 60 * 60 * 24));
								$months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
								$days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
								$diff_ = floor($diff / (60 * 60 * 24));
								// if ($years > 0) {
								// 	$fkpbj = 'FP3 (Ubah Timeline)';
								// 	$keterangan_status = 'Telat '.$diff_.' Hari';
								// 	// $keterangan_status = 'Telat '.$years.' Tahun, '.$months.' Bulan, '.$days.' Hari';
								// } else if ($diff_ == 0) {
								$__a = ($get_fkpbj['entry_stamp'] == null) ? '' : '(' . date('d-M-Y', strtotime($get_fkpbj['entry_stamp'])) . ')';
								$fkpbj = 'FKPBJ ' .  $__a;
								// }
								// else {
								// 	$fkpbj = 'FP3 (Ubah Timeline)';
								// 	$keterangan_status = 'Telat '.$diff_.' Hari';
								// 	// $keterangan_status = 'Telat '.$diff_.' Hari ('.date('d-M-Y',strtotime($get_fkpbj['entry_stamp'])).')';
								// }

								$keterangan_status = 'Telat ' . $diff_ . ' Hari';

								// $time_range_telat = json_encode(array('start'=>,'end'=>));
								// $week__		= $this->date_week_($year, $time_range_telat, $get_fkpbj['metode_pengadaan']);
								// (printf("%d tahun, %d bulan, %d hari\n", $years, $months, $days))
								// echo $fkpbj;

								// $diff = abs(strtotime($jwpp_end) - strtotime($entry_stamp));
								// $diff_ = floor($diff / (60*60*24));

								// $jwpp_start = date('Y-m-d',strtotime('-'.$diff_.' days', strtotime($get_fkpbj['jwpp_start'])));
								// $jwpp_end 	= date('Y-m-d',strtotime('-'.$diff_.' days', strtotime($get_fkpbj['jwpp_end'])));

								$jwpp_start = $get_fkpbj['jwpp_start'];
								$jwpp_end = $get_fkpbj['jwpp_end'];

								$start_jwpp = date('Y-m-d', strtotime('+' . $diff_ . 'days', strtotime($jwpp_start)));
								$end_jwpp = date('Y-m-d', strtotime('+' . $diff_ . 'days', strtotime($jwpp_end)));
								$entry_stamp = $get_fkpbj['entry_stamp'];
								// echo($entry_stamp.'-'.$end_jwpp).'<br>';
								$jwpp = json_encode(array('start' => $start_jwpp, 'end' => $end_jwpp));
								$week__ = $this->date_week_actual($year, $jwpp, $metode, $entry_stamp);
							} else {

								$__a = ($get_fkpbj['entry_stamp'] == null) ? '' : '(' . date('d-M-Y', strtotime($get_fkpbj['entry_stamp'])) . ')';
								$fkpbj = 'FKPBJ ' .  $__a;

								$jwpp_start = $get_fkpbj['jwpp_start'];
								$jwpp_end 	= $get_fkpbj['jwpp_end'];

								$date1 = $get_fkpbj['entry_stamp'];
								$date2 = $yellow_end;

								$diff = abs(strtotime($date1) - strtotime($date2));
								$diff_ = floor($diff / (60 * 60 * 24));
								// echo "Diff = ".$diff_;
								$start_jwpp = date('Y-m-d', strtotime('-' . $diff_ . 'days', strtotime($jwpp_start)));
								$end_jwpp = date('Y-m-d', strtotime('-' . $diff_ . 'days', strtotime($jwpp_end)));
								$entry_stamp = $get_fkpbj['entry_stamp'];
								// echo($entry_stamp.'-'.$end_jwpp).'<br>';
								$jwpp = json_encode(array('start' => $start_jwpp, 'end' => $end_jwpp));
								$week__ 		= $this->date_week_actual($year, $jwpp, $metode, $entry_stamp);

								$keterangan_status = isset($get_fkpbj['desc_pengadaan']) ? $get_fkpbj['desc_pengadaan'] : '';
							}

							if ($get_fkpbj['jenis_pengadaan'] == 'jasa_lainnya') {
								$jenis_pengadaan = 'Jasa Lainnya';
							} else if ($get_fkpbj['jenis_pengadaan'] == 'jasa_konstruksi') {
								$jenis_pengadaan = 'Jasa Konstruksi';
							} else if ($get_fkpbj['jenis_pengadaan'] == 'jasa_konsultasi') {
								$jenis_pengadaan = 'Jasa Konsultasi';
							} else if ($get_fkpbj['jenis_pengadaan'] == 'jasa_lainnya') {
								$jenis_pengadaan = 'Jasa Lainnya';
							} else if ($get_fkpbj['jenis_pengadaan'] == 'stock') {
								$jenis_pengadaan = 'Stock';
							} else if ($get_fkpbj['jenis_pengadaan'] == 'non_stock') {
								$jenis_pengadaan = 'Non-Stock';
							} else {
								$jenis_pengadaan = '-';
							}
							$no__ = $key_ + 2;
							// if (count($get_fp3) > 0) {
							// 	$no__ = $key_+2;
							// }
							if ($get_fkpbj['idr_anggaran'] != '0.00') {
								$cur = 'Rp. ' . number_format($get_fkpbj['idr_anggaran']) . '';
							} else {
								$usd_anggaran = isset($get_fkpbj['usd_anggaran']) ? $get_fkpbj['usd_anggaran'] : 0;
								$cur = 'USD. ' . number_format($usd_anggaran) . '';
							}

							$table 		.= '<tr class="content" style="border-bottom:0.01em solid black;">
												<td style="background-color: #ced6e0">' . $get_fkpbj['nama_pengadaan'] . '</td>
												<td style="background-color: #ced6e0">' . $get_fkpbj['metode_pengadaan_name'] . '</td>
												<td style="background-color: #ced6e0">' . $cur . '</td>
												<td style="background-color: #ced6e0">' . $jenis_pengadaan . '</td>
												<td style="background-color: #ced6e0">' . $fkpbj . '</td>
												<td style="background-color: #ced6e0">' . $status_perencanaan . $status_approved . $status_approved_fk . '</td>
												<td style="background-color: #ced6e0">' . $pic_name . '</td>
												<td style="background-color: #ced6e0">' . $keterangan_status . '</td>
												' . $week__ . '
											</tr>
											';
							// }
						}

						// if (count($get_fp3) > 0) {
						// 	$no__ = $key_+3;
						// }

						if (count($get_fp3) > 0) {
							// $no__ = $key+1;

							foreach ($get_fp3 as $key_fp => $value_fp) {
								if (!is_array($value_fp)) {
									continue;
								}
								$start_date = $value_fp['jwpp_start'] ?? null;
								$metode = trim($value_fp['metode_pengadaan'] ?? '');
								if ($metode == "Pelelangan") {
									$metode_day = 60; //60 hari
								} else if ($metode == "Pengadaan Langsung") {
									$metode_day = 10; // 10 hari
								} else if ($metode == "Pemilihan Langsung") {
									$metode_day = 45; //45 hari
								} else if ($metode == "Swakelola") {
									$metode_day = 0;
								} else if ($metode == "Penunjukan Langsung") {
									$metode_day = 20; // 20 hari
								} else {
									//$metode_day = 1;
								}
								$start_yellow = $metode_day + 14;
								$end_yellow = $metode_day + 1;

								$yellow_start = date('Y-m-d', strtotime('-' . $start_yellow . 'days', strtotime($start_date)));
								// echo 'Ini yellow start '.$yellow_start;

								$yellow_end = date('Y-m-d', strtotime('-' . $end_yellow . 'days', strtotime($start_date)));
								// echo 'Ini yellow start '.$yellow_start;

								$entry_stamp = $value_fp['entry_stamp'] ?? null;
								$entry_date = $entry_stamp ? strtotime($entry_stamp) : 0;
								$yellow_start_ = strtotime($yellow_start);
								$yellow_end_ = strtotime($yellow_end);

								$fp3 = 'FP3 (' . ($entry_stamp ? date('d-M-Y', strtotime($entry_stamp)) : '-') . ')';
								if ($entry_date > $yellow_end_) {
									$date1 = date('Y-m-d');
									$date2 = $yellow_start;

									$diff = abs(strtotime($date1) - strtotime($date2));

									$years = floor($diff / (365 * 60 * 60 * 24));
									$months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
									$days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

									if ($years > 0) {
										$fp3 = 'FP3 (Telat ' . $years . ' Tahun, ' . $months . ' Bulan, ' . $days . ' Hari)';
									} else {
										$fp3 = 'FP3 (Telat ' . $months . ' Bulan, ' . $days . ' Hari)';
									}
									// (printf("%d tahun, %d bulan, %d hari\n", $years, $months, $days))
									// echo $fkpbj;
								}


								$jenis_pengadaan_value = $value_fp['jenis_pengadaan'] ?? '';
								if ($jenis_pengadaan_value == 'jasa_lainnya') {
									$jenis_pengadaan = 'Jasa Lainnya';
								} else if ($jenis_pengadaan_value == 'jasa_konstruksi') {
									$jenis_pengadaan = 'Jasa Konstruksi';
								} else if ($jenis_pengadaan_value == 'jasa_konsultasi') {
									$jenis_pengadaan = 'Jasa Konsultasi';
								} else if ($jenis_pengadaan_value == 'jasa_lainnya') {
									$jenis_pengadaan = 'Jasa Lainnya';
								} else if ($jenis_pengadaan_value == 'stock') {
									$jenis_pengadaan = 'Stock';
								} else if ($jenis_pengadaan_value == 'non_stock') {
									$jenis_pengadaan = 'Non-Stock';
								} else {
									$jenis_pengadaan = '-';
								}
								$year = $value_fp['year'] ?? $year;
								$time_range__ = json_encode(array('start' => $value_fp['jwpp_start'] ?? null, 'end' => $value_fp['jwpp_end'] ?? null));
								$week__		= $this->date_week_($year, $time_range__, $value_fp['metode_pengadaan'] ?? '');
								
								$pic_name_fp = $pic_name;
								if (isset($value_fp['id_pic'])) {
									$pic_fp = $this->ex->getPicById($value_fp['id_pic']);
									$pic_name_fp = (is_array($pic_fp) && isset($pic_fp['name']) && $pic_fp['name'] !== '') ? $pic_fp['name'] : $pic_name_fp;
								}

								if (($value_fp['idr_anggaran'] ?? '0.00') != '0.00') {
									$cur = 'Rp. ' . number_format($value_fp['idr_anggaran'] ?? 0) . '';
								} else {
									$usd_anggaran = isset($value_fp['usd_anggaran']) ? $value_fp['usd_anggaran'] : 0;
									$cur = 'USD. ' . number_format($usd_anggaran) . '';
								}

								$table 		.= '<tr class="content" style="border-bottom:0.01em solid black;background-color: #F5F5DC;">
												<td style="background-color: #F5F5DC">' . ($value_fp['nama_pengadaan'] ?? '-') . '</td>
												<td style="background-color: #F5F5DC">' . ($value_fp['metode_pengadaan'] ?? '-') . '</td>
												<td style="background-color: #F5F5DC">' . $cur . '</td>
												<td style="background-color: #F5F5DC">' . $jenis_pengadaan . '</td>
												<td style="background-color: #F5F5DC">FP3 (' . ucfirst((($value_fp['status'] ?? '') == 'hapus') ? 'Batal' : ($value_fp['status'] ?? '-')) . ')</td>
												<td style="background-color: #F5F5DC">' . $status_perencanaan . $status_approved . $status_approved_fp3 . '</td>
												<td style="background-color: #F5F5DC">' . $pic_name_fp . '</td>
												<td style="background-color: #F5F5DC">' . ($value_fp['desc'] ?? '') . '</td>
												' . $week__ . '
											</tr>
											';
							}
						}
					}
					// $table .= '</table>';
				}
			}
		}
		return $table;
	}

	function date_week_actual($year=null, $jwpp, $metode,$fkpbj){
		if (!defined('NL')) {
			define('NL', "\n");
		}
		$jwpp 			= json_decode($jwpp);
		$table			= '';
		// echo $metode;die;

		$metode = trim($metode);
		// DAY BASED ON METODE PROC
		$metode_day		= 0;
		if ($metode == "Pelelangan") {
			$metode_day = 13; //60 hari
		}else if ($metode == "Pengadaan Langsung") {
			$metode_day = 1;// 10 hari
		}else if ($metode == "Pemilihan Langsung"){
			$metode_day = 6; //45 hari
		}else if ($metode == "Swakelola"){
			$metode_day = 0;
		}else if ($metode == "Penunjukan Langsung") {
			$metode_day = 3;// 20 hari
		}else{
			// $metode_day = 1;
		}

		//Variable 
		$start			= $this->get_week($jwpp->start);
		$end			= $this->get_week($jwpp->end);
		$start_red		= $start - $metode_day;
		$end_red		= $start;
		$start_yellow	= $start_red - 2;  // FKPBJ must be submited within 2 weeks
		$end_yellow		= $end_red ;

		// KECEPETAN
		// print_r($fkpbj."<br>");
		// if ($fkpbj < $start_yellow) {
		// 	# code...

		// 	// blue
		// 	$start			= $this->get_week($jwpp->start);
		// 	$end			= $this->get_week($jwpp->end);
		// 	// red
		// 	$start_red		= $start - $metode_day;
		// 	$end_red		= $start;
		// 	//yellow
		// 	$start_yellow 	= $this->get_week($fkpbj);
		// 	$end_yellow		= $end_red ;
		// 	// echo(">>>".$start_yellow);

		// // TELAT
		// }else if($fkpbj > $end_yellow){

		// 	$lately			= $this->get_week($fkpbj) - $end_yellow ; // Selisih entry_stamp fkpbj dan $end_yellow

		// 	// blue
		// 	$start			= $this->get_week($jwpp->start) - $lately;
		// 	$end			= $this->get_week($jwpp->end) - $lately;

		// 	// red
		// 	$start_red		= $start - $metode_day ;
		// 	$end_red		= $start;

		// 	//yellow
		// 	$start_yellow 	= $start_red - 2 - ($lately * -1) ;
		// 	$end_yellow 	= $end_red;

		// 	// echo("<<<".$lately."-".$fkpbj."<br><br>");
		// }


		if (($jwpp->end > "2019-12-28") && (intval($end) == 1)) {
			// echo $end;
			$end = 52;
			// echo "----".$start." > start - ".$end." > end - ".$metode." > metode - ".$metode_day." > metode day<br>";
			
		}else{
			$end = $end;
			// echo $start." > start - ".$end." > end - ".$metode." > metode - ".$metode_day." > metode day<br>";
		}
		// echo $start." > start - ".$end." > end - ".$metode." > metode - ".$metode_day." > metode day<br>";

		$year           = (int)$year; // Ensure year is integer
		$firstDayOfYear = mktime(0, 0, 0, 1, 1, $year);
		$nextMonday     = strtotime('monday', $firstDayOfYear);
		$nextSunday     = strtotime('sunday', $nextMonday);
		$weekly 		= array();
		$_month			= 0;
		$_week 			= 1;
		$_yellow		= 'class="is-yellow"';
		$_red			= 'class="is-red"';
		$_blue			= 'class="is-blue"';
		

		// print_r($start_red);
		while (date('Y', $nextMonday) == $year) {
			$month = date('M', $nextMonday);

			$weekly[$month][] = $_week;

			$nextMonday = strtotime('+1 week', $nextMonday);
			$nextSunday = strtotime('+1 week', $nextSunday);

			$_week++;
		}
			// --------- WEEK ---------
			foreach ($weekly as $month => $value) {
				foreach ($value as $week) {
					//echo $end_red." Ini adalah end red - ".$start_red." Ini adalah start red - ".$week." Ini adalah week - ".$end." Ini adalah end <br>";
					// echo $week." > ini adalah week <br>";
					if ($week >= $start && $week <= $end) {
						// BLUE
						$table .= "<td id='".sprintf('%02d', $week)."' class='row-small is-blue' style='border-bottom:0.01em solid black' bgcolor='#54A0FF'>&nbsp;</td>";
					}else if ($week >= $start_red && $week <= $end_red){
						// RED
						$table .= "<td id='".sprintf('%02d', $week)."' class='row-small is-red' style='border-bottom:0.01em solid black' bgcolor='#FF7675'>&nbsp;</td>";
					}else if ($week >= $start_yellow && $week <= $end_yellow){
						// YELLOW
						$table .= "<td id='".sprintf('%02d', $week)."' class='row-small is-yellow' style='border-bottom:0.01em solid black' bgcolor='#FECA57'>&nbsp;</td>";
					}else{
						// PLAIN
						$table .= "<td class='row-small' id='".sprintf('%02d', $week)."' style='border-bottom:0.01em solid black;background-color: #ced6e0;'>&nbsp;</td>";//border-bottom:1px solid black
					}
				}
			}

		// print_r($table);
		return $table;
	}

	function export_pdf($name="", $page="-", $paper = "A4", $orientation = "potrait"){

		$dompdf = new DOMPDF();
		$dompdf->load_html($page);
		$dompdf->set_paper($paper, $orientation);
		
		$dompdf->render();

			$dompdf->stream($name.".pdf", array("Attachment" => 1));
	}
	
	function export_pdf_perencanaan($title="",$name="", $page="-", $paper = "A4", $orientation = "potrait"){

		$dompdf = new DOMPDF();
		$dompdf->load_html($page);
		$dompdf->set_paper($paper, $orientation);
		
		$dompdf->render();
		$canvas = $dompdf->get_canvas();
		$font = Font_Metrics::get_font("helvetica", "bold");

		// the same call as in my previous example
		$canvas->page_text(292, 18, $title,
                   $font, 10, array(0,0,0));
		$dompdf->stream($name.".pdf", array("Attachment" => 1));
	}

	public function compare()
	{
		$fppbj = $this->db->where('del',0)->where('is_status',2)->get('ms_fppbj')->result_array();
		$fkpbj = $this->db->where('del',0)->get('ms_fkpbj')->result_array();

		$tf = '<table border=1>
			<thead>
				<tr>
					<th>No</th>
					<th>Nama Pengadaan</th>
				</tr>
			</thead>
			<tbody>';
			$nf = 1;
				foreach ($fppbj as $key => $value) {
					$tf .= '<tr>
					<td>'.($nf++).'</td>
					<td>'.$value['nama_pengadaan'].'</td>
				</tr>';
				}
			$tf .= '</tbody>
		</table>';

		$tfk = '<table border=1>
			<thead>
				<tr>
					<th>No</th>
					<th>Nama Pengadaan</th>
				</tr>
			</thead>
			<tbody>';
			$nfk = 1;
				foreach ($fkpbj as $key => $value) {
					$tfk .= '<tr>
					<td>'.($nfk++).'</td>
					<td>'.$value['nama_pengadaan'].'</td>
				</tr>';
				}
			$tfk .= '</tbody>
		</table>';

		echo $tf.' '.$tfk;
	}

	public function in_man()
	{
		$query = "	SELECT
						*
					FROM
						ms_fppbj
					WHERE
						id IN(284,200,223,287,289,292,293,294,295,340,363,375) ";//id IN(411,412,413)
		$query = $this->db->query($query);

		foreach ($query->result_array() as $key => $value) {
			$this->db->where('id', $value['id'])->update('ms_fppbj', array(
				'is_status' => 2,
				'is_approved' => 3
			));
			
			$this->db->where('id_fppbj', $value['id'])->delete('ms_fkpbj');

			$this->db->insert('ms_fkpbj', array(
				'id_fppbj' => $value['id'],
				'id_pic' => $value['id_pic'],
				'id_division' => $value['id_division'],
				'is_status' => 2,
				'is_approved' => 3,
				'is_reject' => 0,
				'idr_anggaran' => $value['idr_anggaran'],
				'no_pr' => $value['no_pr'],
				'tipe_pr' => $value['tipe_pr'],
				'pr_lampiran' => $value['pr_lampiran'],
				'nama_pengadaan' => $value['nama_pengadaan'],
				'usd_anggaran' => isset($value['usd_anggaran']) ? $value['usd_anggaran'] : 0,
				'year_anggaran' => $value['year_anggaran'],
				'desc_pengadaan' => isset($value['desc_pengadaan']) ? $value['desc_pengadaan'] : '',
				'hps' => $value['hps'],
				'kak_lampiran' => $value['kak_lampiran'],
				'desc_dokumen' => $value['desc_dokumen'],
				'jenis_pengadaan' => $value['jenis_pengadaan'],
				'metode_pengadaan' => $value['metode_pengadaan'],
				'penggolongan_penyedia' => $value['penggolongan_penyedia'],
				'desc_metode_pembayaran' => $value['desc_metode_pembayaran'],
				'jenis_kontrak' => $value['jenis_kontrak'],
				'sistem_kontrak' => $value['sistem_kontrak'],
				'lingkup_kerja' => $value['lingkup_kerja'],
				'jwpp_start' => $value['jwpp_start'],
				'jwpp_end' => $value['jwpp_end'],
				'jwp_start' => $value['jwp_start'],
				'jwp_start' => $value['jwp_start'],
				'entry_stamp' => null,
				'del' => 0
			));
		}
	}
	
}
