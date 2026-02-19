<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Export_timeline extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Export_timeline_model', 'etm');
	}

	function rekap_timeline($year = null){
		$this->load->library('excel');

		// fetch data	
		$dateHead 	= $this->date_week($year);
		$dateDetail = $this->date_detail($year);
		// print_r($dateDetail);die;
		// $data		= $this->ex->rekap_department($year);
		$count_fkpbj = $this->etm->count_rekap_department_fkpbj($year);
		// print_r($data);die;
		$show_table = '<table class="rekap break" align="center" border=1>
					  <b><p align="center">
					    Rekapitulasi Pengadaan Barang/Jasa per Departemen 
					    </p></b>
					  <tr>
					    <th rowspan=3>No</th>
					    <th rowspan=3>Pengguna Barang/Jasa</th>
					    <th colspan=8>pelelangan</th>
					    <th colspan=8>Pemilihan Langsung</th>
					    <th colspan=8>Swakelola</th>
					    <th colspan=8>Penunjukan Langsung</th>
					    <th colspan=8>Pengadaan Langsung</th>
					    <th rowspan=3>Keterangan</th>
					  </tr>
					  <tr>
					    <td rowspan=2>Perencanaan</td>
					    <td colspan=7 align="center">Aktual</td>
					    <td rowspan=2>Perencanaan</td>
					    <td colspan=7 align="center">Aktual</td>
					    <td rowspan=2>Perencanaan</td>
					    <td colspan=7 align="center">Aktual</td>
					    <td rowspan=2>Perencanaan</td>
					    <td colspan=7 align="center">Aktual</td>
					    <td rowspan=2>Perencanaan</td>
					    <td colspan=7 align="center">Aktual</td>
					  </tr>
					  <tr>
					    <td>FPPBJ</td>
					    <td>FKPBJ</td>
					    <td>FP3 Nama Pengadaan</td>
					    <td>FP3 Timeline</td>
					    <td>FP3 Metode Pengadaan</td>
					    <td>FP3 Batal</td>
					    <td>FPPBJ Baru</td>

					    <td>FPPBJ</td>
					    <td>FKPBJ</td>
					    <td>FP3 Nama Pengadaan</td>
					    <td>FP3 Timeline</td>
					    <td>FP3 Metode Pengadaan</td>
					    <td>FP3 Batal</td>
					    <td>FPPBJ Baru</td>

					    <td>FPPBJ</td>
					    <td>FKPBJ</td>
					    <td>FP3 Nama Pengadaan</td>
					    <td>FP3 Timeline</td>
					    <td>FP3 Metode Pengadaan</td>
					    <td>FP3 Batal</td>
					    <td>FPPBJ Baru</td>

					    <td>FPPBJ</td>
					    <td>FKPBJ</td>
					    <td>FP3 Nama Pengadaan</td>
					    <td>FP3 Timeline</td>
					    <td>FP3 Metode Pengadaan</td>
					    <td>FP3 Batal</td>
					    <td>FPPBJ Baru</td>

					    <td>FPPBJ</td>
					    <td>FKPBJ</td>
					    <td>FP3 Nama Pengadaan</td>
					    <td>FP3 Timeline</td>
					    <td>FP3 Metode Pengadaan</td>
					    <td>FP3 Batal</td>
					    <td>FPPBJ Baru</td>
					  </tr>';

		$data = $this->etm->rekap_department($year);

		$__pelelangan = 0;
		$__pemilihan_langsung = 0;
		$__swakelola = 0;
		$__penunjukan_langsung = 0;
		$__pengadaan_langsung = 0;

		$fppbj_pelelangan = 0;
		$fppbj_pemilihan_langsung = 0;
		$fppbj_swakelola = 0;
		$fppbj_penunjukan_langsung = 0;
		$fppbj_pengadaan_langsung = 0;

		$fkpbj_pelelangan = 0;
		$fkpbj_pemilihan_langsung = 0;
		$fkpbj_swakelola = 0;
		$fkpbj_penunjukan_langsung = 0;
		$fkpbj_pengadaan_langsung = 0;

		$fp3_np_pelelangan = 0;
		$fp3_np_pemilihan_langsung = 0;
		$fp3_np_swakelola = 0;
		$fp3_np_penunjukan_langsung = 0;
		$fp3_np_pengadaan_langsung = 0;

		$fp3_tl_pelelangan = 0;
		$fp3_tl_pemilihan_langsung = 0;
		$fp3_tl_swakelola = 0;
		$fp3_tl_penunjukan_langsung = 0;
		$fp3_tl_pengadaan_langsung = 0;

		$fp3_mp_pelelangan = 0;
		$fp3_mp_pemilihan_langsung = 0;
		$fp3_mp_swakelola = 0;
		$fp3_mp_penunjukan_langsung = 0;
		$fp3_mp_pengadaan_langsung = 0;

		$fp3_hps_pelelangan = 0;
		$fp3_hps_pemilihan_langsung = 0;
		$fp3_hps_swakelola = 0;
		$fp3_hps_penunjukan_langsung = 0;
		$fp3_hps_pengadaan_langsung = 0;

		$fppbj_baru_pelelangan = 0;
		$fppbj_baru_pemilihan_langsung = 0;
		$fppbj_baru_swakelola = 0;
		$fppbj_baru_penunjukan_langsung = 0;
		$fppbj_baru_pengadaan_langsung = 0;

		$telat_pelelangan = 0;
		$telat_pemilihan_langsung = 0;
		$telat_swakelola = 0;
		$telat_penunjukan_langsung = 0;
		$telat_pengadaan_langsung = 0;

		$no = 1;
		foreach ($data as $key => $value) {
			$data_fppbj 	 = $this->etm->rekap_department_fppbj($year,$value['id_division']);
			$data_fp3_np 	 = $this->etm->rekap_department_fp3($year,$value['id_division'],'nama');
			$data_fp3_mp 	 = $this->etm->rekap_department_fp3($year,$value['id_division'],'metode');
			$data_fp3_hps 	 = $this->etm->rekap_department_fp3($year,$value['id_division'],'hapus');
			$data_fppbj_baru = $this->etm->rekap_department_fppbj($year,$value['id_division'],'1');

			$show_table .= '<tr>
					<td>'.$no.'</td>
					<td style="text-align: left;">'.$value['divisi_name'].'</td>';
					
				if (count($data_fppbj) > 0) {

					$metodes = [
									1 => 'Pelelangan',
									2 => 'Pemilihan Langsung',
									3 => 'Swakelola',
									4 => 'Penunjukan Langsung',
									5 => 'Pengadaan Langsung'
								];

					foreach ($metodes as $key_metode => $metode) {
						$data_telat = $this->etm->count_rekap_department_fkpbj_telat($year,$value['id_division'],$metode);

						$data_fkpbj 	 = $this->etm->count_rekap_department_fkpbj_tidak_telat($year,$value['id_division'],$metode);

						$telat = (isset($data_telat[0]['metode_'.$key_metode])) ? (int) $data_telat[0]['metode_'.$key_metode] : 0;


						$data_fp3_tl 	 = $this->etm->rekap_department_fp3_timeline($year,$value['id_division'],$metode);

						$fppbj 	 = (isset($data_fppbj[0]['metode_'.$key_metode])) ? (int) $data_fppbj[0]['metode_'.$key_metode] : 0;
						$fkpbj 	 = (isset($data_fkpbj[0]['metode_'.$key_metode])) ? (int) $data_fkpbj[0]['metode_'.$key_metode] : 0;
						$fp3_np  = (isset($data_fp3_np[0]['metode_'.$key_metode])) ? (int) $data_fp3_np[0]['metode_'.$key_metode] : 0;
						$fp3_tl  = (isset($data_fp3_tl[0]['metode_'.$key_metode])) ? (int) $data_fp3_tl[0]['metode_'.$key_metode] : 0;
						$fp3_mp  = (isset($data_fp3_mp[0]['metode_'.$key_metode])) ? (int) $data_fp3_mp[0]['metode_'.$key_metode] : 0;
						$fp3_hps = (isset($data_fp3_hps[0]['metode_'.$key_metode])) ? (int) $data_fp3_hps[0]['metode_'.$key_metode] : 0;
						$fppbj_baru = (isset($data_fppbj_baru[0]['metode_'.$key_metode])) ? (int) $data_fppbj_baru[0]['metode_'.$key_metode] : 0;
						
						$show_table .= ' <td>'.$value['metode_'.$key_metode].'</td>
									<td>'.$fppbj.'</td>
									<td>'.$fkpbj.'</td>
									<td>'.$fp3_np.'</td>
									<td>'.$fp3_tl.'</td>
									<td>'.$fp3_mp.'</td>
									<td>'.$fp3_hps.'</td>
									<td>'.$fppbj_baru.'</td>';
						
						$slug = str_replace(" ", "_", strtolower($metode));
						${'fppbj_'.$slug} += $fppbj;
						${'fkpbj_'.$slug} += $fkpbj;
						${'fp3_np_'.$slug} += $fp3_np;
						${'fp3_tl_'.$slug} += $fp3_tl;
						${'fp3_mp_'.$slug} += $fp3_mp;
						${'fp3_hps_'.$slug} += $fp3_hps;
						${'fppbj_baru_'.$slug} += $fppbj_baru;
						${'telat_'.$slug} += $telat;
						${'__'.$slug} += (int) $value['metode_'.$key_metode];
					}
									
					$show_table .= '<td></td></tr>';

					
				} else {
					$show_table .= '<td>'.$value['metode_1'].'</td>
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
				}

					
			
			$no++;

		}

		$total_perencanaan = $__pelelangan + $__pemilihan_langsung + $__swakelola + $__penunjukan_langsung + $__pengadaan_langsung;

		$total_fppbj = $fppbj_pelelangan + $fppbj_pemilihan_langsung + $fppbj_swakelola + $fppbj_penunjukan_langsung + $fppbj_pengadaan_langsung;

		$total_telat = $telat_pelelangan + $telat_pemilihan_langsung + $telat_swakelola + $telat_penunjukan_langsung + $telat_pengadaan_langsung;

		$total_fkpbj = $fkpbj_pelelangan + $fkpbj_pemilihan_langsung + $fkpbj_swakelola + $fkpbj_penunjukan_langsung + $fkpbj_pengadaan_langsung;

		$total_fp3_np = $fp3_np_pelelangan + $fp3_np_pemilihan_langsung + $fp3_np_swakelola + $fp3_np_penunjukan_langsung + $fp3_np_pengadaan_langsung;

		$total_fp3_tl = $fp3_tl_pelelangan + $fp3_tl_pemilihan_langsung + $fp3_tl_swakelola + $fp3_tl_penunjukan_langsung + $fp3_tl_pengadaan_langsung;

		$total_fp3_mp = $fp3_mp_pelelangan + $fp3_mp_pemilihan_langsung + $fp3_mp_swakelola + $fp3_mp_penunjukan_langsung + $fp3_mp_pengadaan_langsung;

		$total_fp3_hps = $fp3_hps_pelelangan + $fp3_hps_pemilihan_langsung + $fp3_hps_swakelola + $fp3_hps_penunjukan_langsung + $fp3_hps_pengadaan_langsung;

		$total_fppbj_baru = $fppbj_baru_pelelangan + $fppbj_baru_pemilihan_langsung + $fppbj_baru_swakelola + $fppbj_baru_penunjukan_langsung + $fppbj_baru_pengadaan_langsung;

		$total_aktual = $total_fppbj + $total_fkpbj + $total_fp3_np + $total_fp3_tl + $total_fp3_mp + $total_fp3_hps + $total_fppbj_baru;

		$show_table .= '
					<tr>
						<td colspan="2"></td>
						<td style="font-weight: bold;">'.$__pelelangan.'</td>
						<td style="font-weight: bold;">'.$fppbj_pelelangan.'</td>
						<td style="font-weight: bold;">'.$fkpbj_pelelangan.'</td>
						<td style="font-weight: bold;">'.$fp3_np_pelelangan.'</td>
						<td style="font-weight: bold;">'.$fp3_tl_pelelangan.'</td>
						<td style="font-weight: bold;">'.$fp3_mp_pelelangan.'</td>
						<td style="font-weight: bold;">'.$fp3_hps_pelelangan.'</td>
						<td style="font-weight: bold;">'.$fppbj_baru_pelelangan.'</td>

						<td style="font-weight: bold;">'.$__pemilihan_langsung.'</td>
						<td style="font-weight: bold;">'.$fppbj_pemilihan_langsung.'</td>
						<td style="font-weight: bold;">'.$fkpbj_pemilihan_langsung.'</td>
						<td style="font-weight: bold;">'.$fp3_np_pemilihan_langsung.'</td>
						<td style="font-weight: bold;">'.$fp3_tl_pemilihan_langsung.'</td>
						<td style="font-weight: bold;">'.$fp3_mp_pemilihan_langsung.'</td>
						<td style="font-weight: bold;">'.$fp3_hps_pemilihan_langsung.'</td>
						<td style="font-weight: bold;">'.$fppbj_baru_pemilihan_langsung.'</td>

						<td style="font-weight: bold;">'.$__swakelola.'</td>
						<td style="font-weight: bold;">'.$fppbj_swakelola.'</td>
						<td style="font-weight: bold;">'.$fkpbj_swakelola.'</td>
						<td style="font-weight: bold;">'.$fp3_np_swakelola.'</td>
						<td style="font-weight: bold;">'.$fp3_tl_swakelola.'</td>
						<td style="font-weight: bold;">'.$fp3_mp_swakelola.'</td>
						<td style="font-weight: bold;">'.$fp3_hps_swakelola.'</td>
						<td style="font-weight: bold;">'.$fppbj_baru_swakelola.'</td>

						<td style="font-weight: bold;">'.$__penunjukan_langsung.'</td>
						<td style="font-weight: bold;">'.$fppbj_penunjukan_langsung.'</td>
						<td style="font-weight: bold;">'.$fkpbj_penunjukan_langsung.'</td>
						<td style="font-weight: bold;">'.$fp3_np_penunjukan_langsung.'</td>
						<td style="font-weight: bold;">'.$fp3_tl_penunjukan_langsung.'</td>
						<td style="font-weight: bold;">'.$fp3_mp_penunjukan_langsung.'</td>
						<td style="font-weight: bold;">'.$fp3_hps_penunjukan_langsung.'</td>
						<td style="font-weight: bold;">'.$fppbj_baru_penunjukan_langsung.'</td>

						<td style="font-weight: bold;">'.$__pengadaan_langsung.'</td>
						<td style="font-weight: bold;">'.$fppbj_pengadaan_langsung.'</td>
						<td style="font-weight: bold;">'.$fkpbj_pengadaan_langsung.'</td>
						<td style="font-weight: bold;">'.$fp3_np_pengadaan_langsung.'</td>
						<td style="font-weight: bold;">'.$fp3_tl_pengadaan_langsung.'</td>
						<td style="font-weight: bold;">'.$fp3_mp_pengadaan_langsung.'</td>
						<td style="font-weight: bold;">'.$fp3_hps_pengadaan_langsung.'</td>
						<td style="font-weight: bold;">'.$fppbj_baru_pengadaan_langsung.'</td>

						<td style="font-weight: bold;"></td>
					</tr>
					<tr>
	                  	<td style="font-weight: bold;" colspan=2>Total Perencanaan</td>
	                    <td style="font-weight: bold;" colspan=41 align="center">'.$total_perencanaan.'</td>
	                  </tr>
	                  <tr>
	                    <td style="font-weight: bold;" colspan=2>Total Aktual</td>
	                    <td style="font-weight: bold;" colspan=41 align="center">'.$total_aktual.'</td>
	                  </tr>
	                  <tr>
	                    <td style="font-weight: bold;" colspan=2>Total FPPBJ</td>
	                    <td style="font-weight: bold;" colspan=41 align="center">'.$total_fppbj.'</td>
	                  <tr>
	                  <tr>
	                    <td style="font-weight: bold;" colspan=2>Total FKPBJ</td>
	                    <td style="font-weight: bold;" colspan=41 align="center">'.$total_fkpbj.'</td>
	                  <tr>
	                  <tr>
	                    <td style="font-weight: bold;" colspan=2>Total FP3 Nama Pengadaan</td>
	                    <td style="font-weight: bold;" colspan=41 align="center">'.$total_fp3_np.'</td>
	                  </tr>
	                  <tr>
	                    <td style="font-weight: bold;" colspan=2>Total FP3 Timeline</td>
	                    <td style="font-weight: bold;" colspan=41 align="center">'.$total_fp3_tl.'</td>
	                  </tr>
	                  <tr>
	                    <td style="font-weight: bold;" colspan=2>Total FP3 Metode Pengadaan</td>
	                    <td style="font-weight: bold;" colspan=41 align="center">'.$total_fp3_mp.'</td>
	                  </tr>
	                  <tr>
	                    <td style="font-weight: bold;" colspan=2>Total FP3 Batal</td>
	                    <td style="font-weight: bold;" colspan=41 align="center">'.$total_fp3_hps.'</td>
	                  </tr>
	                  <tr>
	                    <td style="font-weight: bold;" colspan=2>Total FPPBJ Baru</td>
	                    <td style="font-weight: bold;" colspan=41 align="center">'.$total_fppbj_baru.'</td>
	                  </tr>';
		$show_table .='</table>';
		

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
							<font color="#ced6e0">&#8718;</font> Grafik actual <span color="#fff" style="border: 1px #000 solid; height: 7px; width: 6px; display: inline-block;"></span> Grafik Perencanaan 
							'.$dateDetail.'
						</div>
					</body>
		</html>';
		header('Content-type: application/ms-excel');

		header('Content-Disposition: attachment; filename=Rekap Perencanaan'.$year.'.xls');
    	header('Cache-Control: max-age=0');
    	echo $page;
	}

	function get_week($ddate=null){
		// $ddate = "2018-04-07";					
		$date = new DateTime($ddate);
		$week = $date->format("W");

		return $week;
	}

	function date_week($year=null){
		if ($year == null) {
			$year = date('Y');
		} 
		if (!defined('NL')) {
			define('NL', "\n");
		}

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
									<th rowspan="4" style="padding: 4px;width:20px; height:10px;">Status Approval</th>
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
		$table = '';
		$jwpp 			= json_decode($jwpp);
		if (!$jwpp || !isset($jwpp->start) || !isset($jwpp->end)) {
			return $table;
		}

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

	function date_detail($year=null){
		$data = $this->etm->rekap_perencanaan($year);
		$dateHead = $this->date_week($year);
		// print_r($data);die;

		$table = $dateHead;
		foreach ($data as $kadiv => $divisions) {
			if (!is_array($divisions)) {
				continue;
			}
			foreach ($divisions as $divisionName => $rows) {
				if (!is_array($rows) || count($rows) === 0) {
					continue;
				}
				$table .= '<tr class="content">
								<td colspan="61" style="text-align: left; font-weight:bold;">'.$kadiv." - ".$divisionName.'</td>
							</tr>';

				foreach ($rows as $key_ => $value__) {
						$pending_status = '-';
						$status_aktual_fkpbj = '-';
						$status_aktual_fp3 = '-';
						$is_approved_hsse = isset($value__['is_approved_hsse']) ? $value__['is_approved_hsse'] : (isset($value__['is_approved_hse']) ? $value__['is_approved_hse'] : 0);
						$id_perencanaan_umum = isset($value__['id_perencanaan_umum']) ? (int) $value__['id_perencanaan_umum'] : 0;
						$pic_name = '-';
						$note_value = '-';
						// print_r($value__);die;
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
						$time_range_ = json_encode(array(
							'start' => isset($value__['jwpp_start']) ? $value__['jwpp_start'] : null,
							'end' => isset($value__['jwpp_end']) ? $value__['jwpp_end'] : null
						));
						
						$week_ 		= $this->date_week_($year, $time_range_, $value__['metode_pengadaan']);

						$get_fkpbj 	= $this->etm->get_fkpbj($value__['id_fppbj']);
						$get_fp3 	= $this->etm->get_fp3($value__['id_fppbj']);
						
						$no__ = $key_+1;
						//FPPBJ
						if ($value__['is_status'] == 0) {
							if ($value__['is_approved'] == 0 && $value__['is_reject'] == 0) {
								$pending_status = 'Menunggu Persetujuan Kadept User';
							} 
							else if ($value__['is_approved'] == 0 && $value__['is_reject'] == 1) {
								$pending_status = 'Di revisi Kadept User';
							}
							// ------------------------------------------ End Status User	
							else if ($value__['is_approved'] == 1 && $value__['tipe_pengadaan'] == 'jasa' && $value__['is_approved_hse'] == '0' && $value__['is_reject'] == 0) {
								$pending_status = 'Menunggu Persetujuan HSSE';
							}
							else if ($value__['is_approved'] == 1 && $value__['tipe_pengadaan'] == 'jasa' && $value__['is_approved_hse'] > '1') {
								$pending_status = 'Di revisi HSSE';
							}
							// ------------------------------------------ End Status HSSE
							else if (($value__['is_approved'] == 1 && $value__['tipe_pengadaan'] == 'barang' && $value__['is_reject'] == 0) OR $value__['is_approved'] == 1 && $value__['tipe_pengadaan'] == 'jasa' && $is_approved_hsse == 1 && $value__['is_reject'] == 0) {
								$pending_status = 'Menunggu Persetujuan Admin Pengendalian';
							}
							else if (($value__['is_approved'] == 1 && $value__['tipe_pengadaan'] == 'barang' && $value__['is_reject'] == 1 && $value__['is_reject'] == 1) OR $value__['is_approved'] == 1 && $value__['tipe_pengadaan'] == 'jasa' && $is_approved_hsse == 1 && $value__['is_reject'] == 1) {
								$pending_status = 'Di revisi Admin Pengendalian';
							}
							// ------------------------------------------ End Status Admin Pengendalian
							else if ($value__['is_approved'] == 2 && $value__['is_reject'] == 0) {
								$pending_status = 'Menunggu Persetujuan Kadept Procurement';
							}
							else if ($value__['is_approved'] == 2 && $value__['is_reject'] == 1) {
								$pending_status = 'Di revisi Kadept Procurement';
							}
							else if ($value__['is_approved'] == 3 && $value__['is_reject'] == 0 && $value__['idr_anggaran'] < 100000000 && ($value__['metode_pengadaan'] == 'Penunjukan Langsung')) {
								$pending_status = 'Di setujui Kadept Procurement';
							}
							else if ($value__['is_approved'] == 3 && $value__['is_reject'] == 0 && $value__['idr_anggaran'] <= 100000000) {
								$pending_status = 'Di setujui Kadept Procurement';
							}
							// ------------------------------------------ End Status Kadept Procurement
							else if ($value__['is_approved'] == "3" && $value__['is_reject'] == 0 && (($value__['idr_anggaran'] > 100000000 && $value__['idr_anggaran'] <= 1000000000) && ($value__['metode_pengadaan'] == 'Penunjukan Langsung' || $value__['metode_pengadaan'] == 'Pemilihan Langsung' || $value__['metode_pengadaan'] == 'Pelelangan'))) {
								$pending_status = 'Menunggu Persetujuan Kadiv SDM & Umum';
							}
							else if ($value__['is_approved'] == "3" && $value__['is_reject'] == 1 && (($value__['idr_anggaran'] > 100000000 && $value__['idr_anggaran'] <= 1000000000) && ($value__['metode_pengadaan'] == 'Penunjukan Langsung' || $value__['metode_pengadaan'] == 'Pemilihan Langsung' || $value__['metode_pengadaan'] == 'Pelelangan'))) {
								$pending_status = 'Direvisi Kadiv SDM & Umum';
							}
							else if ($value__['is_approved'] == "4" && $value__['is_reject'] == 0 && (($value__['idr_anggaran'] > 100000000 && $value__['idr_anggaran'] <= 1000000000) && ($value__['metode_pengadaan'] == 'Penunjukan Langsung' || $value__['metode_pengadaan'] == 'Pemilihan Langsung' || $value__['metode_pengadaan'] == 'Pelelangan'))) {
								$pending_status = 'Disetujui Kadiv SDM & Umum';
							}
							// ------------------------------------------ End Status Kadiv SDM & Umum
							elseif ($value__['is_approved'] == "3" && $value__['is_reject'] == 0 && ($value__['idr_anggaran'] > 1000000000 && $value__['idr_anggaran'] <= 10000000000) && ($value__['metode_pengadaan'] == 'Penunjukan Langsung' || $value__['metode_pengadaan'] == 'Pemilihan Langsung' || $value__['metode_pengadaan'] == 'Pelelangan')) {
								$pending_status = 'Menunggu Persetujuan Dir.Keuangan & Umum';
							}
							elseif ($value__['is_approved'] == "3" && $value__['is_reject'] == 1 && ($value__['idr_anggaran'] > 1000000000 && $value__['idr_anggaran'] <= 10000000000) && ($value__['metode_pengadaan'] == 'Penunjukan Langsung' || $value__['metode_pengadaan'] == 'Pemilihan Langsung' || $value__['metode_pengadaan'] == 'Pelelangan')) {
								$pending_status = 'Direvisi Dir.Keuangan & Umum';
							}
							elseif ($value__['is_approved'] == "4" && $value__['is_reject'] == 0 && ($value__['idr_anggaran'] > 1000000000 && $value__['idr_anggaran'] <= 10000000000) && ($value__['metode_pengadaan'] == 'Penunjukan Langsung' || $value__['metode_pengadaan'] == 'Pemilihan Langsung' || $value__['metode_pengadaan'] == 'Pelelangan')) {
								$pending_status = 'Di setujui Dir.Keuangan & Umum';
							}
							// ------------------------------------------ End Status Dir.Keuangan & Umum
							elseif ($value__['is_approved'] == "3" && $id_perencanaan_umum < 1 && $value__['is_reject'] == 0 && $value__['idr_anggaran'] >= 10000000000 && ($value__['metode_pengadaan'] == 'Penunjukan Langsung' || $value__['metode_pengadaan'] == 'Pemilihan Langsung' || $value__['metode_pengadaan'] == 'Pelelangan')) {
								$pending_status = 'Menunggu Persetujuan Dir.Utama';
							}
							elseif ($value__['is_approved'] == "3" && $id_perencanaan_umum < 1 && $value__['is_reject'] == 1 && $value__['idr_anggaran'] >= 10000000000 && ($value__['metode_pengadaan'] == 'Penunjukan Langsung' || $value__['metode_pengadaan'] == 'Pemilihan Langsung' || $value__['metode_pengadaan'] == 'Pelelangan')) {
								$pending_status = 'Direvisi Persetujuan Dir.Utama';
							}
							elseif ($value__['is_approved'] == "4" && $id_perencanaan_umum < 1 && $value__['is_reject'] == 0 && $value__['idr_anggaran'] >= 10000000000 && ($value__['metode_pengadaan'] == 'Penunjukan Langsung' || $value__['metode_pengadaan'] == 'Pemilihan Langsung' || $value__['metode_pengadaan'] == 'Pelelangan')) {
								$pending_status = 'Disetujui Dir.Utama';
							}
						} 
						//FP3
						else if ($value__['is_status'] == "1") {
							if ($value__['is_approved'] == "0" && $value__['is_reject'] == "0") {
								$pending_status = 'Menunggu Persetujuan Kadept User';
							}
							else if ($value__['is_approved'] == "0" && $value__['is_reject'] == "1") {
								$pending_status = 'Di Revisi Kadept User';
							}
							// ------------------------------------------ End Status User
							else if ($value__['is_approved'] == "1" && $value__['is_reject'] == "0") {
								$pending_status = 'Menunggu Persetujuan Admin Pengendalian';
							}
							else if ($value__['is_approved'] == "1" && $value__['is_reject'] == "1") {
								$pending_status = 'Di Revisi Admin Pengendalian';
							}
							// ------------------------------------------ End Status Admin Pengendalian
							else if ($value__['is_approved'] == "2" && $value__['is_reject'] == "0") {
								$pending_status = 'Menunggu Persetujuan Kadept Procurement';
							}
							else if ($value__['is_approved'] == "2" && $value__['is_reject'] == "1") {
								$pending_status = 'Di Revisi Kadept Procurement';
							}
							else if ($value__['is_approved'] == "3" && $value__['is_reject'] == "0") {
								$pending_status = 'Di Setujui Kadept Procurement';
							}
							// ------------------------------------------ End Status Kadept Procurement

							if ($value__['idr_anggaran'] <= 100000000) {
								$status_aktual_fp3 = 'Di setujui Kadept Procurement';
							}
							else if ((($value__['idr_anggaran'] > 100000000 && $value__['idr_anggaran'] <= 1000000000) && ($value__['metode_pengadaan'] == 'Penunjukan Langsung' || $value__['metode_pengadaan'] == 'Pemilihan Langsung' || $value__['metode_pengadaan'] == 'Pelelangan'))) {
								$status_aktual_fp3 = 'Disetujui Kadiv SDM & Umum';
							}
							else if (($value__['idr_anggaran'] > 1000000000 && $value__['idr_anggaran'] <= 10000000000) && ($value__['metode_pengadaan'] == 'Penunjukan Langsung' || $value__['metode_pengadaan'] == 'Pemilihan Langsung' || $value__['metode_pengadaan'] == 'Pelelangan')) {
								$status_aktual_fp3 = 'Di setujui Dir.Keuangan & Umum';
							}
							elseif ($value__['idr_anggaran'] >= 10000000000 && ($value__['metode_pengadaan'] == 'Penunjukan Langsung' || $value__['metode_pengadaan'] == 'Pemilihan Langsung' || $value__['metode_pengadaan'] == 'Pelelangan')) {
								$status_aktual_fp3 = 'Disetujui Dir.Utama';
							}
						} 
						//FKPBJ
						elseif ($value__['is_status'] == "2") {
							if ($value__['is_approved'] == "0" && $value__['is_reject'] == "0") {
								$pending_status = 'Menunggu Persetujuan Kadept User';
							}
							else if ($value__['is_approved'] == "0" && $value__['is_reject'] == "1") {
								$pending_status = 'Di Revisi Kadept User';
							}
							// ------------------------------------------ End Status User
							else if ($value__['is_approved'] == "1" && $value__['is_reject'] == "0") {
								$pending_status = 'Menunggu Persetujuan Admin Procurement';
							}
							else if ($value__['is_approved'] == "1" && $value__['is_reject'] == "1") {
								$pending_status = 'Di Revisi Admin Procurement';
							}
							// ------------------------------------------ End Status Admin Procurement
							else if ($value__['is_approved'] == "2" && $value__['is_reject'] == "0") {
								$pending_status = 'Menunggu Persetujuan Kadept Procurement';
							}
							else if ($value__['is_approved'] == "2" && $value__['is_reject'] == "1") {
								$pending_status = 'Di Revisi Kadept Procurement';
							}
							else if ($value__['is_approved'] == "3" && $value__['is_reject'] == "0") {
								$pending_status = 'Di Setujui Kadept Procurement';
							}
							// ------------------------------------------ End Status Kadept Procurement
							if ($value__['idr_anggaran'] <= 100000000) {
								$status_aktual_fkpbj = 'Di setujui Kadept Procurement';
							}
							else if ((($value__['idr_anggaran'] > 100000000 && $value__['idr_anggaran'] <= 1000000000) && ($value__['metode_pengadaan'] == 'Penunjukan Langsung' || $value__['metode_pengadaan'] == 'Pemilihan Langsung' || $value__['metode_pengadaan'] == 'Pelelangan'))) {
								$status_aktual_fkpbj = 'Disetujui Kadiv SDM & Umum';
							}
							else if (($value__['idr_anggaran'] > 1000000000 && $value__['idr_anggaran'] <= 10000000000) && ($value__['metode_pengadaan'] == 'Penunjukan Langsung' || $value__['metode_pengadaan'] == 'Pemilihan Langsung' || $value__['metode_pengadaan'] == 'Pelelangan')) {
								$status_aktual_fkpbj = 'Di setujui Dir.Keuangan & Umum';
							}
							elseif ($value__['idr_anggaran'] >= 10000000000 && ($value__['metode_pengadaan'] == 'Penunjukan Langsung' || $value__['metode_pengadaan'] == 'Pemilihan Langsung' || $value__['metode_pengadaan'] == 'Pelelangan')) {
								$status_aktual_fkpbj = 'Disetujui Dir.Utama';
							}
						}

						$pic = $this->etm->get_pic($value__['id_pic']);
						if (is_array($pic) && isset($pic['name']) && $pic['name'] !== '') {
							$pic_name = $pic['name'];
						}

						$note = $this->etm->get_note_reject($value__['id']);
						if (is_array($note) && isset($note['value']) && $note['value'] !== '') {
							$note_value = $note['value'];
						}

						if ($value__['is_reject'] == "0") {
							$keterangan = isset($value__['desc_pengadaan']) ? $value__['desc_pengadaan'] : (isset($value__['desc']) ? $value__['desc'] : '-');
							$ket_fp3 = (!empty($get_fp3) && isset($get_fp3[0]['desc'])) ? $get_fp3[0]['desc'] : '-';
						} else {
							$keterangan = $note_value;
							$ket_fp3 = $note_value;
						}

						if (!empty($get_fkpbj)) {

							if ($value__['idr_anggaran'] != '0.00') {
								$cur = 'Rp. '.number_format($value__['idr_anggaran']).'';
							} else {
								$cur = 'USD. '.number_format($value__['usd_anggaran']).'';
							}

							$table 		.= '<tr class="content" style="border-bottom:0.01em solid black;">
											<td id="'.$value__['id_fppbj'].'" rowspan=2>'.$no__.'</td>
											<td rowspan=2>'.$value__['divisi'].'</td>
											<td>'.$value__['nama_pengadaan'].'</td>
											<td>'.$value__['metode_pengadaan'].'</td>
											<td>'.$cur.'</td>
											<td>'.$jenis_pengadaan.'</td>
											<td>FPPBJ</td>
											<td>'.$status_aktual_fkpbj.'</td>
											<td>'.$pic_name.'</td>
											<td>'.$keterangan.'</td>
											'.$week_.'
										</tr>
										';
						} else {

							if ($value__['idr_anggaran'] != '0.00') {
								$cur = 'Rp. '.number_format($value__['idr_anggaran']).'';
							} else {
								$cur = 'USD. '.number_format($value__['usd_anggaran']).'';
							}

							$table 		.= '<tr class="content" style="border-bottom:0.01em solid black;">
											<td id="'.$value__['id_fppbj'].'">'.$no__.'</td>
											<td>'.$value__['divisi'].'</td>
											<td>'.$value__['nama_pengadaan'].'</td>
											<td>'.$value__['metode_pengadaan'].'</td>
											<td>'.$cur.'</td>
											<td>'.$jenis_pengadaan.'</td>
											<td>FPPBJ</td>
											<td>'.$pending_status.'</td>
											<td>'.$pic_name.'</td>
											<td>'.$keterangan.'</td>
											'.$week_.'
										</tr>
										';
						}

						if (!empty($get_fkpbj)) {
								$keterangan_status = isset($get_fkpbj['desc_pengadaan']) ? $get_fkpbj['desc_pengadaan'] : '-';

								$start_date = $get_fkpbj['jwpp_start'];
								$metode = trim($get_fkpbj['metode_pengadaan_name']);
								// echo($metode);die;
						        if ($metode == "Pelelangan") {
						            $metode_day = 60; //60 hari
						        }else if ($metode == "Pengadaan Langsung") {
						            $metode_day = 10;// 10 hari
						        }else if ($metode == "Pemilihan Langsung"){
						            $metode_day = 45; //45 hari
						        }else if ($metode == "Swakelola"){
						            $metode_day = 0;
						        }else if ($metode == "Penunjukan Langsung") {
						            $metode_day = 20;// 20 hari
						        }else{
						            //$metode_day = 1;
						        }
						        $start_yellow = $metode_day + 14;
						        $end_yellow = $metode_day + 1;

						        $yellow_start = date('Y-m-d', strtotime('-'.$start_yellow.'days', strtotime($start_date)));
						        $yellow_end = date('Y-m-d', strtotime('-'.$end_yellow.'days', strtotime($start_date)));
						        // echo 'Ini yellow start '.$yellow_start;
									
						       	$entry_date = strtotime($get_fkpbj['entry_stamp']);
						       	$yellow_start_ = strtotime($yellow_start);
						       	$yellow_end_ = strtotime($yellow_end);

						       	if ($entry_date > $yellow_end_) {
						       		$date1 = $get_fkpbj['entry_stamp'];
									$date2 = $yellow_end;

									$diff = abs(strtotime($date1) - strtotime($date2));

									$years = floor($diff / (365*60*60*24));
									$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
									$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
									$diff_ = floor($diff / (60*60*24)); 
									if ($years > 0) {
										$fkpbj = 'FP3 (Ubah Timeline)';
										$keterangan_status = 'Telat '.$diff_.' Hari';
									} else if ($diff_ == 0) {
										 $fkpbj= 'FKPBJ ('.date('d-M-Y',strtotime($get_fkpbj['entry_stamp'])).')';
										 $keterangan_status = isset($get_fkpbj['desc_pengadaan']) ? $get_fkpbj['desc_pengadaan'] : '-';
									}
									else {
										$fkpbj = 'FP3 (Ubah Timeline)';
										$keterangan_status = 'Telat '.$diff_.' Hari';
									}

						       		$jwpp_start = $get_fkpbj['jwpp_start'];
						       		$jwpp_end = $get_fkpbj['jwpp_end'];

						       		$start_jwpp = date('Y-m-d', strtotime('+'.$diff_.'days', strtotime($jwpp_start)));
						       		$end_jwpp = date('Y-m-d', strtotime('+'.$diff_.'days', strtotime($jwpp_end)));
						       		$entry_stamp = $get_fkpbj['entry_stamp'];
						       		// echo($entry_stamp.'-'.$end_jwpp).'<br>';
						       		$jwpp = json_encode(array('start'=>$start_jwpp,'end'=>$end_jwpp));
									$week__ = $this->date_week_actual($year, $jwpp, $metode,$entry_stamp);
						       	} else {

						       	 $fkpbj= 'FKPBJ ('.date('d-M-Y',strtotime($get_fkpbj['entry_stamp'])).')';
						       		$jwpp_start = $get_fkpbj['jwpp_start'];
						       		$jwpp_end 	= $get_fkpbj['jwpp_end'];

						       		$date1 = $get_fkpbj['entry_stamp'];
									$date2 = $yellow_end;

									$diff = abs(strtotime($date1) - strtotime($date2));
									$diff_ = floor($diff / (60*60*24)); 
						       		// echo "Diff = ".$diff_;
						       		$start_jwpp = date('Y-m-d', strtotime('-'.$diff_.'days', strtotime($jwpp_start)));
						       		$end_jwpp = date('Y-m-d', strtotime('-'.$diff_.'days', strtotime($jwpp_end)));
						       		$entry_stamp = $get_fkpbj['entry_stamp'];
						       		// echo($entry_stamp.'-'.$end_jwpp).'<br>';
						       		$jwpp = json_encode(array('start'=>$start_jwpp,'end'=>$end_jwpp));
									$week__ 		= $this->date_week_actual($year, $jwpp, $metode,$entry_stamp);

									$keterangan_status = $get_fkpbj['desc_pengadaan'];
						       	}

								if ($get_fkpbj['jenis_pengadaan'] == 'jasa_lainnya') {
									$jenis_pengadaan = 'Jasa Lainnya';
								} else if($get_fkpbj['jenis_pengadaan'] == 'jasa_konstruksi'){
									$jenis_pengadaan = 'Jasa Konstruksi';
								} else if($get_fkpbj['jenis_pengadaan'] == 'jasa_konsultasi'){
									$jenis_pengadaan = 'Jasa Konsultasi';
								} else if($get_fkpbj['jenis_pengadaan'] == 'jasa_lainnya'){
									$jenis_pengadaan = 'Jasa Lainnya';
								} else if($get_fkpbj['jenis_pengadaan'] == 'stock'){
									$jenis_pengadaan = 'Stock';
								} else if($get_fkpbj['jenis_pengadaan'] == 'non_stock'){
									$jenis_pengadaan = 'Non-Stock';
								} else {
									$jenis_pengadaan = '-';
								}
								$no__ = $key_+2;
								
								if ($get_fkpbj['idr_anggaran'] != '0.00') {
									$cur = 'Rp. '.number_format($get_fkpbj['idr_anggaran']).'';
								} else {
									$cur = 'USD. '.number_format($get_fkpbj['usd_anggaran']).'';
								}

								$table 		.= '<tr class="content" style="border-bottom:0.01em solid black;">
												<td style="background-color: #ced6e0">'.$get_fkpbj['nama_pengadaan'].'</td>
												<td style="background-color: #ced6e0">'.$get_fkpbj['metode_pengadaan_name'].'</td>
												<td style="background-color: #ced6e0">'.$cur.'</td>
												<td style="background-color: #ced6e0">'.$jenis_pengadaan.'</td>
												<td style="background-color: #ced6e0">'.$fkpbj.'</td>
												<td style="background-color: #ced6e0">'.$pending_status.'</td>
												<td style="background-color: #ced6e0">'.$pic_name.'</td>
												<td style="background-color: #ced6e0">'.$keterangan_status.'</td>
												'.$week__.'
											</tr>
											';	
						} 

						if (count($get_fp3) > 0) {
							// $no__ = $key+1;

							foreach ($get_fp3 as $key_fp => $value_fp) {
								$start_date = $value_fp['jwpp_start'];
								$metode = trim($value_fp['metode_pengadaan']);
						        if ($metode == "Pelelangan") {
						            $metode_day = 60; //60 hari
						        }else if ($metode == "Pengadaan Langsung") {
						            $metode_day = 10;// 10 hari
						        }else if ($metode == "Pemilihan Langsung"){
						            $metode_day = 45; //45 hari
						        }else if ($metode == "Swakelola"){
						            $metode_day = 0;
						        }else if ($metode == "Penunjukan Langsung") {
						            $metode_day = 20;// 20 hari
						        }else{
						            //$metode_day = 1;
						        }
						        $start_yellow = $metode_day + 14;
						        $end_yellow = $metode_day + 1;

						        $yellow_start = date('Y-m-d', strtotime('-'.$start_yellow.'days', strtotime($start_date)));

						       	$yellow_end = date('Y-m-d', strtotime('-'.$end_yellow.'days', strtotime($start_date)));

						       	$entry_date = strtotime($value_fp['entry_stamp']);
						       	$yellow_start_ = strtotime($yellow_start);
						       	$yellow_end_ = strtotime($yellow_end);

						       	$fp3 = 'FP3 ('.date('d-M-Y',strtotime($value_fp['entry_stamp'])).')';
						       	if ($entry_date > $yellow_end_) {
						       		$date1 = date('Y-m-d');
									$date2 = $yellow_start;

									$diff = abs(strtotime($date1) - strtotime($date2));

									$years = floor($diff / (365*60*60*24));
									$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
									$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

									if ($years > 0) {
										$fp3 = 'FP3 (Telat '.$years.' Tahun, '.$months.' Bulan, '.$days.' Hari)';
									} else {
										$fp3 = 'FP3 (Telat '.$months.' Bulan, '.$days.' Hari)';
									}
						       	}


								if ($value_fp['jenis_pengadaan'] == 'jasa_lainnya') {
									$jenis_pengadaan = 'Jasa Lainnya';
								} else if($value_fp['jenis_pengadaan'] == 'jasa_konstruksi'){
									$jenis_pengadaan = 'Jasa Konstruksi';
								} else if($value_fp['jenis_pengadaan'] == 'jasa_konsultasi'){
									$jenis_pengadaan = 'Jasa Konsultasi';
								} else if($value_fp['jenis_pengadaan'] == 'jasa_lainnya'){
									$jenis_pengadaan = 'Jasa Lainnya';
								} else if($value_fp['jenis_pengadaan'] == 'stock'){
									$jenis_pengadaan = 'Stock';
								} else if($value_fp['jenis_pengadaan'] == 'non_stock'){
									$jenis_pengadaan = 'Non-Stock';
								} else {
									$jenis_pengadaan = '-';
								}
								$year_ = isset($value_fp['year']) ? $value_fp['year'] : $year;
								$time_range__ = json_encode(array(
									'start' => isset($value_fp['jwpp_start']) ? $value_fp['jwpp_start'] : null,
									'end' => isset($value_fp['jwpp_end']) ? $value_fp['jwpp_end'] : null
								));
								$week__		= $this->date_week_($year_, $time_range__, $value_fp['metode_pengadaan']);

								if ($value_fp['idr_anggaran'] != '0.00') {
									$cur = 'Rp. '.number_format($value_fp['idr_anggaran']).'';
								} else {
									$cur = 'USD. '.number_format($value_fp['usd_anggaran']).'';
								}

								$table 		.= '<tr class="content" style="border-bottom:0.01em solid black;">
												<td id="'.$value_fp['id_fppbj'].'"></td>
												<td>'.$value_fp['divisi'].'</td>
												<td>'.$value_fp['nama_pengadaan'].'</td>
												<td>'.$value_fp['metode_pengadaan'].'</td>
												<td>'.$cur.'</td>
												<td>'.$jenis_pengadaan.'</td>
												<td>FP3 ('.ucfirst(($value_fp['status'] == 'hapus') ? 'Batal' : $value_fp['status']).')</td>
												<td>'.$pending_status.'</td>
												<td>'.$pic_name.'</td>
												<td>'.$ket_fp3.'</td>
												'.$week__.'
											</tr>
											';	
							}
						}

					}
				}
			}
		return $table;
	}

	function date_week_actual($year=null, $jwpp, $metode,$fkpbj){
		if (!defined('NL')) {
			define('NL', "\n");
		}
		$table = '';
		$jwpp 			= json_decode($jwpp);
		if (!$jwpp || !isset($jwpp->start) || !isset($jwpp->end)) {
			return $table;
		}
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


		if (($jwpp->end > "2019-12-28") && ($end == 01)) {
			// echo $end;
			$end = 52;
			// echo "----".$start." > start - ".$end." > end - ".$metode." > metode - ".$metode_day." > metode day<br>";
			
		}else{
			$end = $end;
			// echo $start." > start - ".$end." > end - ".$metode." > metode - ".$metode_day." > metode day<br>";
		}

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
						$table .= "<td class='row-small' id='".sprintf('%02d', $week)."' style='border-bottom:0.01em solid black;background-color: #ced6e0;'>&nbsp;</td>";//border-bottom:1px solid black
					}
				}
			}

		// print_r($table);
		return $table;
	}

}

/* End of file Export_timeline.php */
/* Location: ./application/controllers/Export_timeline.php */
