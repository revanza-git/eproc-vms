<?php defined('BASEPATH') OR exit('No direct script access allowed');

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

	//Export PDF per FPPBJ 
	public function fppbj($id){
		$nomor = $this->input->post()['no'];
		$tanggal = $this->input->post()['tanggal'];
		$view 	= $this->load->view('fppbj/list',null, TRUE);
		$table = '';
		$no  = 1;
		$dataFPPBJ = $this->fm->selectData($id);
		$dataAnalisaResiko = $this->fm->get_analisa_resiko($id);
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
			$tanggal_kesepakatan = $this->input->post()['tanggal'];
		}
			$dataMaster = $this->fm->getFPPBJ();
			$no = 1;
			$dataAnalisa = $this->ex->get_analisa($id);
			if ($dataAnalisa['dpt_list'] != '') {
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
				if ($dataFPPBJ['hps'] != '') {
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
					$no_pr = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> PR : Nomor PR ...</td>';
				} else{
					$no_pr = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> PR : Nomor PR ...</td>';
				}
			} else if ($dataFPPBJ['tipe_pengadaan'] == 'barang') {
				if ($dataAnalisaSwakelola['id_fppbj'] != '' && $dataAnalisaSwakelola['waktu'] != '' && $dataAnalisaSwakelola['biaya'] != '' && $dataAnalisaSwakelola['tenaga'] != '' && $dataAnalisaSwakelola['bahan'] != '' && $dataAnalisaSwakelola['peralatan'] != '') {
				  $form_analisa_swakelola = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Swakelola</td>';
				} else{
					 $form_analisa_swakelola = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Swakelola</td>';
				}
				if ($dataFPPBJ['hps'] != '') {
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
					$no_pr_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> PR : Nomor PR ...</td>';
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

			if($getValCSMS == 'E' || $getValCSMS == 'H'){
				$high = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>High</td>';
			}else{
				$high = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>High</td>';
			}

			if($getValCSMS == 'M'){
				$medium = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>Medium</td>';
			}else{
				$medium = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Medium</td>';
			}

			if($getValCSMS == 'L'){
				$low = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>Low</td>';
			}else{
				$low = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Low</td>';
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

			$dataFPPBJ['jwpp'] = json_decode($dataFPPBJ['jwpp']);
			$dataFPPBJ['jwpp'] = date('d M Y', strtotime($dataFPPBJ['jwpp']->start))." sampai ".date('d M Y', strtotime($dataFPPBJ['jwpp']->end));
			$dataFPPBJ['jwp'] = json_decode($dataFPPBJ['jwp']);
			$dataFPPBJ['jwp'] = date('d M Y', strtotime($dataFPPBJ['jwp']->start))." sampai ".date('d M Y', strtotime($dataFPPBJ['jwp']->end));

		//  header("Content-type: application/vnd.ms-excel");
		//  header("Content-Disposition: attachment; filename=Laporan FPPBJ".default_date(date('Y-m-d')).".xls");
		// 	print_r($dataFPPBJ);

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
								<tr>
									<td rowspan="3">Penggolongan CSMS Penyedia Barang/Jasa (Khusus Pengadaan Jasa dan sesuai hasil analisa risiko)</td>
									'.$high.'
								</tr>
								<tr>
									'.$medium.'
								</tr>
								<tr>
									'.$low.'
								</tr>
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
					 				<th rowspan="2" class="no" style="width: 20px">'.$no++.'</td>
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


	//echo $table_;die;
		$dompdf = new DOMPDF();
		$dompdf->load_html($table_);
		$dompdf->set_paper("A4", "potrait");
        // $dompdf->set_option('isHtml5ParserEnabled', TRUE);
		$dompdf->render();
		$dompdf->stream("FPPBJ.pdf", array("Attachment" => 1));	
	}

	//Export PDF per FKPBJ 
	public function fkpbj($id){
		$nomor = $this->input->post()['no'];
		$tanggal = $this->input->post()['tanggal'];
		$view 	= $this->load->view('fppbj/list',null, TRUE);
		$table = '';
		$no  = 1;
		$dataFKPBJ = $this->fkm->selectData($id);
		$dataAnalisaResiko = $this->fm->get_analisa_resiko($id);
		$dataAnalisaSwakelola = $this->fm->get_analisa_swakelola($id);
		$jwpp = json_decode($dataFKPBJ['jwpp']);
		$jwp = json_decode($dataFKPBJ['jwp']);

		$date_jwpp = strtotime($jwpp->end) - strtotime($jwpp->start);
		$total_jwpp = round($date_jwpp / (60*60*24));

		$date_jwp = strtotime($jwp->end) - strtotime($jwp->start);
		$total_jwp = round($date_jwp / (60*60*24));

		if ($dataFKPBJ['jwpp'] != null) {
			$jwpp = date('d M Y', strtotime(json_decode($dataFKPBJ['jwpp'])->start)).' sampai '.date('d M Y', strtotime(json_decode($dataFKPBJ['jwpp'])->end)).' ('.$total_jwpp.' Hari)';
		} else{
			$jwpp = '-';
		}
		if ($dataFKPBJ['jwp'] != null) {
			$jwp = date('d M Y',strtotime($jwp->start)).' sampai '.date('d M Y',strtotime($jwp->end)).' ('.$total_jwp.' Hari)';
		} else {
			$jwp = '-';
		}

		if (!isset($tanggal)) {
			$tanggal_kesepakatan = '-';
		} else{
			$tanggal_kesepakatan = $this->input->post()['tanggal'];
		}
			$dataMaster = $this->fm->getFPPBJ();
			$no = 1;
			$dataAnalisa = $this->ex->get_analisa($id);
			if ($dataAnalisa['dpt_list_'] != '') {
				$get_dpt = $dataAnalisa['dpt_list_'];
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
									<td>Keterangan : '.$get.'</td>
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
				if ($dataFKPBJ['hps'] != '') {
					$hps = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> HPS (Dalam Amplop Tertutup)</td>';
				} else{
					$hps = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> HPS (Dalam Amplop Tertutup)</td>';
				}
				if ($dataFKPBJ['lampiran_persetujuan'] != '') {
					$kak = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> KAK / Spesifikasi Teknis</td>';
				} else{
					$kak = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> KAK / Spesifikasi Teknis</td>';
				}
				if ($dataFKPBJ['no_pr'] != '') {
					$no_pr = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> PR : Nomor PR ...</td>';
				} else{
					$no_pr = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> PR : Nomor PR ...</td>';
				}
			} else if ($dataFKPBJ['tipe_pengadaan'] == 'barang') {
				if ($dataAnalisaSwakelola['id_fppbj'] != '' || $dataFKPBJ['swakelola'] != '') {
				  $form_analisa_swakelola = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> Swakelola</td>';
				} else{
					 $form_analisa_swakelola = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> Swakelola</td>';
				}
				if ($dataFKPBJ['hps'] != '') {
					$hps_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> HPS (Dalam Amplop Tertutup)</td>';
				} else{
					$hps_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> HPS (Dalam Amplop Tertutup)</td>';
				}
				if ( $dataFKPBJ['lampiran_persetujuan'] != '') {
					$kak_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> KAK / Spesifikasi Teknis</td>';
				} else{
					$kak_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span> KAK / Spesifikasi Teknis</td>';
				}
				if ($dataFKPBJ['no_pr'] != '') {
					$no_pr_ = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span> PR : Nomor PR ...</td>';
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

			if($dataFKPBJ['value'] == 'High'){
				$high = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>High</td>';
			}else{
				$high = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>High</td>';
			}

			if($dataFKPBJ['value'] == 'Medium'){
				$medium = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>Medium</td>';
			}else{
				$medium = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Medium</td>';
			}

			if($dataFKPBJ['value'] == 'Low'){
				$low = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check.png"></span>Low</td>';
			}else{
				$low = '<td><span style="float:left;"><img src="'.base_url().'assets/images/check-box.png"></span>Low</td>';
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
								<tr>
									<td rowspan="3">Penggolongan CSMS Penyedia Barang/Jasa (Khusus Pengadaan Jasa dan sesuai hasil analisa risiko)</td>
									'.$high.'
								</tr>
								<tr>
									'.$medium.'
								</tr>
								<tr>
									'.$low.'
								</tr>
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
					 				<td>'.$dataFKPBJ['desc'].'</td>
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
		$nomor  	 = $this->input->post()['no'];
		$kepada 	 = $this->input->post()['to'];
		$pusat_biaya = $this->input->post()['pb'];
		$tanggal 	 = $this->input->post()['date'];
		$pengguna 	 = $this->input->post()['pbj'];
		$kadep_	 	 = $this->input->post()['kadep_'];
		$kadiv_	 	 = $this->input->post()['kadiv_'];
		$kadep	 	 = $this->input->post()['kadep'];
		$kadiv	 	 = $this->input->post()['kadiv'];
		foreach ($data as $key => $value) {
			$key = $key +1;
			if($value['jadwal_pengadaan'] !== null){
				$date = date('d M Y', strtotime(json_decode($value['jadwal_pengadaan'])->start)).' sampai '.date('d M Y', strtotime(json_decode($value['jadwal_pengadaan'])->end));
			}else{
				$date = '-';
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
							<td>'.$date.'</td>
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
				            <th rowspan="2">Jadwal</th>
				            <tr>
								<th>Lama</th>
								<th>Baru</th>
								<th>Lama</th>
								<th>Baru</th>
								<th>Lama</th>
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
	
				
		//print_r($page);die;
		// Convert > PDF
		$this->export_pdf('FORMULIR PERUBAHAN PERENCANAAN PENGADAAN B/J ("FP3")', $page, 'A4', 'landscape');
	}
	
	// Export PDF Rekap Perencanaan FPPBJ per tahun
	function rekap_perencanaan($year = null){

		// fetch data	
		$dateHead 	= $this->date_week($year);
		$dateDetail = $this->date_detail($year);
		// print_r($dateDetail);die;
		$data	= $this->ex->rekap_department($year);
		$table = '';
		$no = 1;
		$__pelelangan 			= 0;
		$__pemilihan_langsung 	= 0;
		$__penunjukan_langsung 	= 0;
		$__pengadaan_langsung 	= 0;
		$__swakelola 			= 0;
		foreach ($data as $key => $value) {
			$table .= '<tr>
				<td>'.$no.'</td>
				<td style="text-align: left;">'.$value['divisi_name'].'</td>
				<td>'.$value['metode_1'].'</td>
				<td>'.$value['metode_2'].'</td>
				<td>'.$value['metode_3'].'</td>
				<td>'.$value['metode_4'].'</td>
				<td>'.$value['metode_5'].'</td>
				<td></td>
			</tr>
			';
			$__pelelangan 			+= $value['metode_1'];
			$__pemilihan_langsung 	+= $value['metode_2'];
			$__swakelola 			+= $value['metode_3'];
			$__penunjukan_langsung 	+= $value['metode_4'];
			$__pengadaan_langsung 	+= $value['metode_5'];
			$no++;
		}
		$grand_total = $__pelelangan + $__pemilihan_langsung + $__swakelola + $__penunjukan_langsung + $__pengadaan_langsung ;
		$table .= '<tr class="bold" >
						<td colspan="2" style="text-align: right; font-weight: 700">Total</td>
						<td>'.$__pelelangan.'</td>
						<td>'.$__pemilihan_langsung.'</td>
						<td>'.$__swakelola.'</td>
						<td>'.$__penunjukan_langsung.'</td>
						<td>'.$__pengadaan_langsung.'</td>
						<td style="font-weight: 700;font-size:15px;">'.$grand_total.'</td>
					</tr>';
		$divId = 'TableRekap';
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
							<b><p align="center">Perencanaan Pengadaan Barang/Jasa <br> Tahun '.$year.'
							</p></b>
							'.$dateDetail.'
							<div style="margin-top: 2rem">
								<table class="rekap break" align="center">
									<b><p align="center">
										Rekapitulasi Pengadaan Barang/Jasa per Departemen 
									</p></b>
									<tr>
										<th>No</th>
										<th>Pengguna Barang/Jasa</th>
										<th>pelelangan</th>
										<th>Pemilihan Langsung</th>
										<th>Swakelola</th>
										<th>Penunjukan Langsung</th>
										<th>Pengadaan Langsung</th>
										<th>Keterangan</th>
									</tr>
									'.$table.'
								</table>
								</div>
						</div>
					</body>
		</html>';

		// print_r($page);die;
		//<caption style="page-break-inside: avoid; font-size: 15px; font-weight: 700; margin-bottom: 10px">Perencanaan Pengadaan Barang/Jasa <br> Tahun '.$year.'</caption>

		//$this->export_pdf("Rekap Perencanaan Pengadaan - ".$year.".pdf", $page, 'A4', 'landscape');
		header('Content-type: application/ms-excel');

    	header('Content-Disposition: attachment; filename=Rekap Perencanaan'.$year.'.xls');
    	echo $page;
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
								<td style="text-align: left;">'.$value_['division'].'</td>
								<td>'.$value_['name'].'</td>
								<td>'.$value_['email'].'</td>
								<td>'.$value_['username'].'</td>
								<td>'.$value_['raw_password'].'</td>
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
								Rekapitulasi Rencana Pengadaan Barang/Jasa Tahun '.$year.'
							</caption>
							<tr>
								<th>No.</th>
								<th>Satuan Kerja</th>
								<th>Nama</th>
								<th>Email</th>
								<th>Username</th>
							</tr>
							'.$table.'
						</table>
						</div>
						
					</body>
				
				</html>';

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
	function date_week($year=2018){
		define('NL', "\n");

		$year           = $year;
		$firstDayOfYear = mktime(0, 0, 0, 1, 1, $year);
		$nextMonday     = strtotime('monday', $firstDayOfYear);
		$nextSunday     = strtotime('sunday', $nextMonday);
		$_month			= 0;
		$_week 			= 1;
		$weekly 		= array();

		while (date('Y', $nextMonday) == $year) {
			$month = date('M', $nextMonday);

			$weekly[$month][] = $_week;

			// echo "<td>".$_week."</td>";

			$nextMonday = strtotime('+1 week', $nextMonday);
			$nextSunday = strtotime('+1 week', $nextSunday);

			$_week++;
		}
		$_week = $_week - 1;
		// --------- HEADER -------
		$table = '<table align="center" style="max-width:100%;min-width:100%;">
								<tr class="gery">
									<th rowspan="4" style="padding: 2px;width:10px; height:10px;">No</th>
									<th rowspan="4" style="padding: 4px;width:10px; height:10px;">Pengguna Barang/Jasa</th>
									<th rowspan="4" style="padding: 4px;width:40px; height:10px;">Nama Pengadaan Barang/Jasa</th>
									<th rowspan="4" style="padding: 4px;width:20px; height:10px;">Metode Pengadaan</th>
									<th rowspan="4" style="padding: 4px;width:20px; height:10px;">Anggaran (include PPN 10%) </th>
									<th rowspan="4" style="padding: 4px;width:20px; height:10px;">Jenis Pengadaan</th>
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
					foreach ($value as $week) {
						$table .= "<td class='week-small' id='".$week."'>".$week."</td>";
					}
				}
				$table .= "</tr>";

		return $table;
	}

	function date_week_($year=null, $jwpp, $metode){
		define('NL', "\n");
		$jwpp 			= json_decode($jwpp);
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
		$start_yellow	= $start_red - 2;
		$end_yellow		= $end_red ;

		if (($jwpp->end > "2019-12-28") && ($end == 01)) {
			// echo $end;
			$end = 52;
			// echo "----".$start." > start - ".$end." > end - ".$metode." > metode - ".$metode_day." > metode day<br>";
			
		}else{
			$end = $end;
			// echo $start." > start - ".$end." > end - ".$metode." > metode - ".$metode_day." > metode day<br>";
		}
		// echo $start." > start - ".$end." > end - ".$metode." > metode - ".$metode_day." > metode day<br>";

		$year           = $year;
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

	function date_detail($year=null){
		$data = $this->ex->rekap_perencanaan($year);
		$dateHead = $this->date_week($year);
		// print_r($data);die;
		foreach ($data as $divisi => $value) {
			// print_r($value);die;
			$time_range = json_encode(array('start'=>$value['jwpp_start'],'end'=>$value['jwpp_end']));
			// echo $time_range;die;
			$week 		= $this->date_week_($year, $time_range, $value['metode_pengadaan']);

			// $table 		.= '<tr class="content">
			// 					<td id="'.$value['id_fppbj'].'">'.($key + 1).'</td>
			// 					<td>'.$value['divisi'].'</td>
			// 					<td>'.$value['nama_pengadaan'].'</td>
			// 					<td>'.$value['metode_pengadaan'].'</td>
			// 					<td>Rp. '.number_format($value['idr_anggaran']).'</td>
			// 					<td>'.$jenis_pengadaan.'</td>
			// 					'.$week.'
			// 				</tr>';	
							// <td>'.$value['desc'].'</td>

					
			foreach ($value as $key => $value_) {
				
				if (count($value_)>0) {
					$table 		.= $dateHead.'<tr class="content">
										<td colspan="58" style="text-align: left; font-weight:bold;">'.$divisi." - ".$key.'</td>
									</tr>';	
					foreach ($value_ as $key_ => $value__) {
						
						// echo($value__['metode_pengadaan']);die;
						if ($value__['jenis_pengadaan'] == 'jasa_lainnya') {
							$jenis_pengadaan = 'Jasa Lainnya';
						} else if($value__['jenis_pengadaan'] == 'jasa_konstruksi'){
							$jenis_pengadaan = 'Jasa Konstruksi';
						} else if($value__['jenis_pengadaan'] == 'jasa_konsultasi'){
							$jenis_pengadaan = 'Jasa Konsultasi';
						} else if($value__['jenis_pengadaan'] == 'jasa_lainnya'){
							$jenis_pengadaan = 'Jasa Lainnya';
						} else if($value__['jenis_pengadaan'] == 'stock'){
							$jenis_pengadaan = 'Stock';
						} else if($value__['jenis_pengadaan'] == 'non_stock'){
							$jenis_pengadaan = 'Non-Stock';
						} else {
							$jenis_pengadaan = '-';
						}
						$time_range_ = $time_range = json_encode(array('start'=>$value__['jwpp_start'],'end'=>$value__['jwpp_end']));
						// echo $time_range_;die;
						// print_r($value__['metode_pengadaan']);die;
						$week_ 		= $this->date_week_($year, $time_range_, $value__['metode_pengadaan']);
						$table 		.= '<tr class="content" style="border-bottom:0.01em solid black;">
											<td id="'.$value__['id_fppbj'].'">'.($key_ + 1).'</td>
											<td>'.$value__['divisi'].'</td>
											<td>'.$value__['nama_pengadaan'].'</td>
											<td>'.$value__['metode_pengadaan'].'</td>
											<td>Rp. '.number_format($value__['idr_anggaran']).'</td>
											<td>'.$jenis_pengadaan.'</td>
											'.$week_.'
										</tr>
										';	
					}
					$table .= '</table> <br>';
				}
				
			}
		}
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
	
}
