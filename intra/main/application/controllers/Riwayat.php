<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Riwayat extends MY_Controller
{

	public $modelAlias = 'rp';

	public function __construct()
	{
		parent::__construct();
		$this->load->model('riwayat_model', 'rp');
		$this->load->model('Fppbj_model', 'fm');
		$this->load->model('Pemaketan_model', 'pm');
		$this->load->model('Export_model', 'ex');
		include_once APPPATH . 'third_party/dompdf2/dompdf_config.inc.php';
	}

	public function approval($id)
	{
		$this->breadcrumb->addlevel(1, array(
			'url' => site_url('riwayat'),
			'title' => 'Riwayat Approval'
		));
		$data = array(
			'id' => $id
		);
		$this->header = 'Riwayat Approval';
		$this->content = $this->load->view('riwayat/list', $data, TRUE);
		$this->script = $this->load->view('riwayat/list_js', $data, TRUE);
		parent::index();
	}

	public function getDataApproval($id)
	{
		$config['query'] = $this->rp->getDataApproval($id);
		$return = $this->tablegenerator->initialize($config);
		echo json_encode($return);
	}

	public function form_edit_date($id)
	{
		$modelAlias = $this->modelAlias;
		$data = $this->$modelAlias->getDetailDateApproval($id);

		$this->form = array(
			'form' => array(
				array(
					'field'	=> 	'date',
					'type'	=>	'dateTime',
					'label'	=>	'Tanggal approval',
					'rules' => 	'required',
				)
			)
		);

		foreach ($this->form['form'] as $key => $element) {
			$this->form['form'][$key]['value'] = $data[$element['field']];
		}

		$this->form['url'] = site_url('riwayat/save_updated_date/' . $id);
		$this->form['button'] = array(
			array(
				'type' => 'submit',
				'label' => 'Ubah'
			),
			array(
				'type' => 'cancel',
				'label' => 'Batal'
			)
		);
		echo json_encode($this->form);
	}

	public function save_updated_date($id)
	{
		$modelAlias = $this->modelAlias;
		$save = $this->input->post();
		$a = $this->$modelAlias->save_updated_date($id, $save);
		if ($a) {
			echo json_encode(array('status' => 'success'));
		}
	}

	function pengadaan($id)
	{
		$data = '<!DOCTYPE html>
					<html>
					<head>			
					<meta charset="utf-8">
					<style type="text/css">
						/* Simplified CSS */
						body {
							font-family: Arial, sans-serif;
							font-size: 12px;
						}
						table {
							width: 100%;
							border-collapse: collapse;
						}
						th, td {
							border: 1px solid #000;
							padding: 5px;
							text-align: left;
						}
						.header {
							background-color: #f2f2f2;
						}
						.no-border {
							border: none;
						}
						.text-center {
							text-align: center;
						}
						.text-right {
							text-align: right;
						}
						.catatan {
							font-weight: bold;
						}
						.red { color: red; }
						.yellow { color: yellow; }
						.green { color: green; }
					</style>
					</head>
					<body>';

		$modelAlias = $this->modelAlias;
		$a = $this->$modelAlias->getPengadaanById($id);
		if (!is_array($a) || empty($a)) {
			show_error('Data pengadaan tidak ditemukan', 404);
			return;
		}

		$list_sistem_kontrak = '';
		$is_multiyear_field = '';

		if ($a['is_status'] != 1) {
			$is_status = array(
				0 => 'FPPBJ',
				2 => 'FKPBJ'
			);

			$hps = array(
				0 => 'Tidak Ada',
				1 => 'Ada'
			);

			$penyedia = array(
				'perseorangan' => 'Perseorangan',
				'usaha_kecil' => 'Usaha Kecil (K)',
				'usaha_menengah' => 'Usaha Menengah (M)',
				'usaha_besar' => 'Usaha Besar (B)'
			);

			$jenis_kontrak = array(
				'po'	=> 'Purchase Order (PO)',
				'GTC01' => 'GTC01 - Kontrak Jasa Konstruksi non EPC',
				'GTC02' => 'GTC02 - Kontrak Jasa Konsultan',
				'GTC03' => 'GTC03 - Kontrak Jasa Umum',
				'GTC04' => 'GTC04 - Kontrak Jasa Pemeliharaan',
				'GTC05' => 'GTC05 - Kontrak Jasa Pembuatan Software',
				'GTC06' => 'GTC06 - Kontrak Jasa Sewa Fasilitas dan Alat',
				'GTC07' => 'GTC07 - Kontrak Jasa Tenaga Kerja.',
				'spk' => 'Perjanjian sederhana/SPK',
			);

			$sistem_kontrak = array(
				'lumpsum'		=> 'Perikatan Harga - Lumpsum',
				'unit_price' 	=> 'Perikatan Harga - Unit Price',
				'modified' 		=> 'Perikatan Harga - Modified (lumpsum + unit price)',
				'outline' 		=> 'Perikatan Harga - Outline Agreement',
				'turn_key' 		=> 'Delivery - Turn Key',
				'sharing' 		=> 'Delivery - Sharing Contract',
				'success_fee' 	=> 'Delivery - Success Fee',
				'stockless' 	=> 'Delivery - Stockless Purchasing',
				'on_call' 		=> 'Delivery - On Call Basic'
			);

			if (($a['jwp_start'] == '' || $a['jwp_start'] == null || $a['jwp_start'] == '0000-00-00') && ($a['jwp_end'] == '' || $a['jwp_end'] == null || $a['jwp_end'] == '0000-00-00')) {
				$jwp_date = ' - ';
			} else {
				$jwp_date = date('d M Y', strtotime($a['jwp_start'])) . ' sampai ' . date('d M Y', strtotime($a['jwp_end']));
			}

			if ($a['sistem_kontrak'] == '' || $a['sistem_kontrak'] == 'null') {
				$list_sistem_kontrak = '-';
			} else {
				$no = 1;
				$decode_siskon = json_decode($a['sistem_kontrak']);
				if (!is_array($decode_siskon)) {
					$decode_siskon = array();
				}
				foreach ($decode_siskon as $keys => $values) {
					// print_r($values);//die;
					if (isset($sistem_kontrak[$values])) {
						$list_sistem_kontrak .= $no . '.' . ucfirst(str_replace('_', ' ', $sistem_kontrak[$values])) . '<br>';
						$no++;
					}
				}
				if ($list_sistem_kontrak === '') {
					$list_sistem_kontrak = '-';
				}
			}

			if ($a['is_multiyear'] == 1) {
				$data_multi_years = $this->pm->get_multi_years($id);
				$no = 1;

				foreach ($data_multi_years as $key => $value) {
					$is_multiyear_field .= '<tr>
												<th colspan="2">Detail Anggaran #' . $no . '</th>
											</tr>
											<tr>
												<th>Anggaran (IDR)</th>
												<td>Rp. ' . number_format($value['idr_anggaran']) . '</td>
											</tr>
											<tr>
												<th>Anggaran (USD)</th>
												<td>USD ' . number_format($value['usd_anggaran']) . '</td>
											</tr>
											<tr>
												<th>Tahun Anggaran</th>
												<td>' . $value['year_anggaran'] . '</td>
											</tr>';
					$no++;
				}
			} else {
				$is_multiyear_field = '	<tr>
											<th>Anggaran (IDR)</th>
											
											<td>Rp. ' . number_format($a['idr_anggaran']) . '</td>
										</tr>
										<tr>
											<th>Anggaran (USD)</th>
											
											<td>USD ' . number_format($a['usd_anggaran']) . '</td>
										</tr>
										<tr>
											<th>Tahun Anggaran</th>
											
											<td>' . $a['year_anggaran'] . '</td>
										</tr>';
			}

			$data .= '<table>
						<tr>
							<th colspan="2">Data ' . $is_status[$a['is_status']] . ' - ' . $a['nama_pengadaan'] . '</th>
						</tr>
						<tr>
							<th>No. PR</th>
							
							<td>' . $a['no_pr'] . '</td>
						</tr>
						<tr>
							<th>Tipe PR</th>
							
							<td>' . str_replace('_', ' ', strtoupper($a['tipe_pr'])) . '</td>
						</tr>
						<tr>
							<th>Lampiran PR</th>
							
							<td>' . (($a['pr_lampiran'] == '' || $a['pr_lampiran'] == null) ? '-' : 'Ada') . '</td>
						</tr>
						<tr>
							<th>Nama Pengadaan</th>
							
							<td>' . $a['nama_pengadaan'] . '</td>
						</tr>
						<tr>
							<th>Tipe Pengadaan</th>
							
							<td>' . str_replace('_', ' ', ucfirst($a['tipe_pengadaan'])) . '</td>
						</tr>
						<tr>
							<th>Jenis Detail Pengadaan</th>
							
							<td>' . str_replace('_', ' ', ucfirst($a['jenis_pengadaan'])) . '</td>
						</tr>
						<tr>
							<th>Metode Pengadaan</th>
							
							<td>' . $a['metode_name'] . '</td>
						</tr>
						' . $is_multiyear_field . '
						<tr>
							<th>KAK / Spesifikasi Teknis</th>
							
							<td>' . (($a['kak_lampiran'] == '' || $a['kak_lampiran'] == null) ? '-' : 'Ada') . '</td>
						</tr>
						<tr>
							<th>Ketersediaan HPS</th>
							
							<td>' . (($a['hps'] == '' || $a['hps'] == null) ? '-' : $hps[$a['hps']]) . '</td>
						</tr>
						<tr>
							<th>Lingkup Kerja</th>
							
							<td>' . nl2br(htmlspecialchars(strip_tags((string) $a['lingkup_kerja']), ENT_QUOTES, 'UTF-8')) . '</td>
						</tr>
						<tr>
							<th>Penggolongan Penyedia Jasa (Usulan)</th>
							
							<td>' . (($a['penggolongan_penyedia'] == '' || $a['penggolongan_penyedia'] == null || $a['penggolongan_penyedia'] == '0') ? '-' : $penyedia[$a['penggolongan_penyedia']]) . '</td>
						</tr>
						<tr>
							<th>Masa Penyelesaian Pekerjaan</th>
							
							<td>' . date('d M Y', strtotime($a['jwpp_start'])) . ' sampai ' . date('d M Y', strtotime($a['jwpp_end'])) . '</td>
						</tr>
						<tr>
							<th>Masa Pemeliharaan</th>
							
							<td>' . $jwp_date . '</td>
						</tr>
						<tr>
							<th>Metode Pembayaran (Usulan)</th>
							
							<td>' . nl2br(htmlspecialchars(strip_tags((string) $a['desc_metode_pembayaran']), ENT_QUOTES, 'UTF-8')) . '</td>
						</tr>
						<tr>
							<th>Jenis Kontrak (Usulan)</th>
							
							<td>' . (($a['jenis_kontrak'] == '' || $a['jenis_kontrak'] == null) ? '-' : $jenis_kontrak[$a['jenis_kontrak']]) . '</td>
						</tr>
						<tr>
							<th>Sistem Kontrak (Usulan)</th>
							
							<td>' . $list_sistem_kontrak . '</td>
						</tr>
						<tr>
							<th>Keterangan</th>
							
							<td>' . nl2br(htmlspecialchars(strip_tags((string) $a['desc_dokumen']), ENT_QUOTES, 'UTF-8')) . '</td>
						</tr>
					</table> <br>';

			if ($a['tipe_pengadaan'] == 'jasa') {
				$analisa_resiko = $this->pm->get_data_analisa($id);
				//  print_r($data);
				$table_analisa = '';
				$total_category = '';
				$total = '';
				$no = 1;
				$getCat = array();
				foreach ($analisa_resiko as $key => $value) {
					// Generate Question
					if ($key == 0) {
						$question = "Jenis Pekerjaan";
					} elseif ($key == 1) {
						$question = "Lokasi Kerja";
					} elseif ($key == 2) {
						$question = "Materi Peralatan yang digunakan";
					} elseif ($key == 3) {
						$question = "Potensi paparan terhadap bahaya tempat kerja";
					} elseif ($key == 4) {
						$question = "Potensi paparan terhadap bahaya bagi personil";
					} elseif ($key == 5) {
						$question = "Pekerjaan secara bersamaan oleh kontraktor berbeda";
					} elseif ($key == 6) {
						$question = "Jangka Waktu Pekerjaan";
					} elseif ($key == 7) {
						$question = "Konsekuensi pekerjaan potensian";
					} elseif ($key == 8) {
						$question = "Pengalaman Kontraktor";
					} elseif ($key == 9) {
						$question = "Paparan terhadap publisitas negatif";
					}

					$manusia 	= $this->setCategory($value['manusia']);
					$asset 		= $this->setCategory($value['asset']);
					$lingkungan = $this->setCategory($value['lingkungan']);
					$hukum 		= $this->setCategory($value['hukum']);

					//SET CATEGORY PER QUESTION 
					if ($manusia == "extreme" || $asset == "extreme" || $lingkungan == "extreme" || $hukum == "extreme") {
						$category = '<span id="catatan" class="catatan red">E</span>';
					} else if ($manusia == "high" || $asset == "high" || $lingkungan == "high" || $hukum == "high") {
						$category = '<span id="catatan" class="catatan red">H</span>';
					} else  if ($manusia == "medium" || $asset == "medium" || $lingkungan == "medium" || $hukum == "medium") {
						$category = '<span id="catatan" class="catatan yellow">M</span>';
					} else if ($manusia == "low" || $asset == "low" || $lingkungan == "low" || $hukum == "low") {
						$category = '<span id="catatan" class="catatan green">L</span>';
					} else {
						$category = '<span id="catatan" class="catatan">?</span>';
					}

					array_push($getCat, $category);

					$table_analisa .= '<tr class="q' . $no . '">
												<td>' . $no . '</td>
												<td>' . $question . '</td>
												<td align="center">
													' . $value['apa'] . '
												</td>
												<td align="center">' . $value['manusia'] . '</td>
												<td align="center">' . $value['asset'] . '</td>
												<td align="center">' . $value['lingkungan'] . '</td>
												<td align="center">' . $value['hukum'] . '</td>
												<td align="center">' . $category . '</td>
										</tr>';
					$no++;
				}

				if (in_array('<span id="catatan" class="catatan red">E</span>', $getCat, TRUE)) {
					$total = '<span id="catatan" class="catatan red">E</span>';
				} else if (in_array('<span id="catatan" class="catatan red">H</span>', $getCat, TRUE)) {
					$total = '<span id="catatan" class="catatan red">H</span>';
				} else if (in_array('<span id="catatan" class="catatan yellow">M</span>', $getCat, TRUE)) {
					$total = '<span id="catatan" class="catatan yellow">M</span>';
				} else if (in_array('<span id="catatan" class="catatan green">L</span>', $getCat, TRUE)) {
					$total = '<span id="catatan" class="catatan green">L</span>';
				} else {
					$total = '-';
				}

				$total_category .= '<tr>
										<td colspan="7" style="text-align:right">Hasil Penilaian Keseluruhan :</td><td style="text-align:center!important">' . $total . '</td>
									</tr>';

				$table_analisa_resiko = '<table class="penilaian_resiko preview" border=1>
								 			<thead class="sticky">
								 				<tr>
													<th colspan="8">Penilaian Analisa Risiko</th>
												</tr>
												<tr class="header">
									 				<th rowspan="2">No</th>
									 				<th rowspan="2">Daerah Risiko</th>
									 				<th rowspan="2">Apa</th>
									 				<th colspan="5" style="text-align: center;">Konsekuensi <br> L/M/H</th>
									 			</tr>
									 			<tr class="header bottom">
									 				<th>Manusia</th>
									 				<th>Aset</th>
									 				<th>Lingkungan</th>
									 				<th>Reputasi <br>&amp; Hukum</th>
									 				<th>Catatan</th>
									 			</tr>
								 			</thead>
											<tbody>
											' . $table_analisa . '
											' . $total_category . '
											</tbody>
										</table> <br> <h3>Usulan DPT</h3>';

				$data .= $table_analisa_resiko;
			}

			$get_dpt = $this->ex->get_analisa($id);
			if (!is_array($get_dpt)) {
				$get_dpt = array();
			}
			if (!isset($get_dpt['dpt_list'])) {
				$get_dpt['dpt_list'] = array();
			}
			if (is_string($get_dpt['dpt_list'])) {
				$decoded = json_decode($get_dpt['dpt_list'], true);
				if (is_array($decoded)) {
					$get_dpt['dpt_list'] = $decoded;
				} else {
					$get_dpt['dpt_list'] = array_filter(array_map('trim', explode(',', $get_dpt['dpt_list'])));
				}
			}
			if (!is_array($get_dpt['dpt_list'])) {
				$get_dpt['dpt_list'] = array();
			}
			if (!isset($get_dpt['usulan'])) {
				$get_dpt['usulan'] = '';
			}
			if (!is_array($get_dpt)) {
				$get_dpt = array();
			}
			if (!isset($get_dpt['dpt_list'])) {
				$get_dpt['dpt_list'] = array();
			}
			if (is_string($get_dpt['dpt_list'])) {
				$decoded = json_decode($get_dpt['dpt_list'], true);
				if (is_array($decoded)) {
					$get_dpt['dpt_list'] = $decoded;
				} else {
					$get_dpt['dpt_list'] = array_filter(array_map('trim', explode(',', $get_dpt['dpt_list'])));
				}
			}
			if (!is_array($get_dpt['dpt_list'])) {
				$get_dpt['dpt_list'] = array();
			}
			if (!isset($get_dpt['usulan'])) {
				$get_dpt['usulan'] = '';
			}
			
			$dpt = '<table>
						<thead>
							<tr>
								<th>Nama DPT</th>
							</tr>
						</thead>
						<tbody>';
			$no = 1;
			if (!empty($get_dpt['dpt_list'])) {
				foreach ($get_dpt['dpt_list'] as $key) {
					$dpt .= '<tr>
							<td>' . $no++ . '. ' . $key . '</td>
						</tr>';
				}
			} else {
				$dpt .= '<tr>
							<td> - </td>
						</tr>';
			}
			$dpt .= '<tr><th>Non DPT (Usulan)</th></tr>';
			if ($get_dpt['usulan'] != '') {
				$dpt .= '<tr>
							<td>' . $get_dpt['usulan'] . '</td>
						</tr></tbody>
						</table><br>';
			} else {
				$dpt .= '<tr>
								<td> - </td>
							</tr></tbody>
						</table><br>';
			}
			
			$data .= $dpt;

			

			if ($a['metode_name'] == 'Swakelola') {
				$swakelola = $this->pm->get_swakelola($id);

				if ($swakelola['waktu'] == 1) {
					$waktu_swakelola = 'Penyelesaian Pekerjaan kurang dari 3 Bulan';
				} else if ($swakelola['waktu'] == 2) {
					$waktu_swakelola = 'Penyelesaian Pekerjaan lebih dari 3 Bulan s.d kurang dari 6 Bulan';
				} else {
					$waktu_swakelola = 'Penyelesaian pekerjaan ≥ 6 Bulan';
				}

				if ($swakelola['biaya'] == 1) {
					$biaya_swakelola = 'Biaya pelaksanaan pekerjaan kurang dari 50 juta';
				} else if ($swakelola['biaya'] == 2) {
					$biaya_swakelola = 'Biaya pelaksanaan pekerjaan lebih dari 50 Bulan s.d kurang dari 100 juta';
				} else {
					$biaya_swakelola = 'Biaya pelaksanaan pekerjaan lebih dari 100 juta';
				}

				if ($swakelola['tenaga'] == 1) {
					$tenaga_swakelola = 'Kompetensi dan/atau ketersediaan jumlah Tenaga Kerja di Perusahaan memenuhi sebagai perencana dan pelaksana dan pengawas';
				} else if ($swakelola['tenaga'] == 2) {
					$tenaga_swakelola = 'Kompetensi dan/atau ketersediaan jumlah Tenaga Kerja di Perusahaan memenuhi salah satu atau lebih sebagai perencana dan/atau pelaksana dan/atau pengawas';
				} else {
					$tenaga_swakelola = 'Kompetensi dan/atau ketersediaan jumlah Tenaga Kerja di Perusahaan tidak memenuhi sebagai perencana dan pelaksana dan pengawas';
				}

				if ($swakelola['bahan'] == 1) {
					$bahan_swakelola = 'Bahan mudah didapatkan langsung oleh Pekerja NR';
				} else if ($swakelola['bahan'] == 2) {
					$bahan_swakelola = 'Bahan dapat diadakan melalui pihak ketiga';
				} else {
					$bahan_swakelola = 'Bahan lebih efisien apabila diadakan oleh pihak ketiga';
				}

				if ($swakelola['peralatan'] == 1) {
					$peralatan_swakelola = 'Ketersediaan jumlah dan kemampuan peralatan kerja memenuhi kebutuhan pekerjaan';
				} else if ($swakelola['peralatan'] == 2) {
					$peralatan_swakelola = 'Ketersediaan jumlah dan/atau kemampuan peralatan kerja tidak memenuhi kebutuhan pekerjaan';
				} else {
					$peralatan_swakelola = 'Peralatan lebih efisien apabila diadakan oleh pihak ketiga';
				}

				$table_swakelola = '<table>
					<tr>
						<th colspan="2">Data Swakelola</th>
					</tr>
					<tr>
						<th>Waktu</th>
						
						<td>' . $waktu_swakelola . '</td>
					</tr>
					<tr>
						<th>Biaya</th>
						
						<td>' . $biaya_swakelola . '</td>
					</tr>
					<tr>
						<th>Tenaga Kerja</th>
						
						<td>' . $tenaga_swakelola . '</td>
					</tr>
					<tr>
						<th>Bahan</th>
						
						<td>' . $bahan_swakelola . '</td>
					</tr>
					<tr>
						<th>Peralatan</th>
						
						<td>' . $peralatan_swakelola . '</td>
					</tr>
				</table> <br>';

				$data .= $table_swakelola;
			}
		} else {
			$is_status = array(
				0 => 'FPPBJ',
				2 => 'FKPBJ'
			);

			$hps = array(
				0 => 'Tidak Ada',
				1 => 'Ada'
			);

			$penyedia = array(
				'perseorangan' => 'Perseorangan',
				'usaha_kecil' => 'Usaha Kecil (K)',
				'usaha_menengah' => 'Usaha Menengah (M)',
				'usaha_besar' => 'Usaha Besar (B)'
			);

			$jenis_kontrak = array(
				'po'	=> 'Purchase Order (PO)',
				'GTC01' => 'GTC01 - Kontrak Jasa Konstruksi non EPC',
				'GTC02' => 'GTC02 - Kontrak Jasa Konsultan',
				'GTC03' => 'GTC03 - Kontrak Jasa Umum',
				'GTC04' => 'GTC04 - Kontrak Jasa Pemeliharaan',
				'GTC05' => 'GTC05 - Kontrak Jasa Pembuatan Software',
				'GTC06' => 'GTC06 - Kontrak Jasa Sewa Fasilitas dan Alat',
				'GTC07' => 'GTC07 - Kontrak Jasa Tenaga Kerja.',
				'spk' => 'Perjanjian sederhana/SPK',
			);

			$sistem_kontrak = array(
				'lumpsum'		=> 'Perikatan Harga - Lumpsum',
				'unit_price' 	=> 'Perikatan Harga - Unit Price',
				'modified' 		=> 'Perikatan Harga - Modified (lumpsum + unit price)',
				'outline' 		=> 'Perikatan Harga - Outline Agreement',
				'turn_key' 		=> 'Delivery - Turn Key',
				'sharing' 		=> 'Delivery - Sharing Contract',
				'success_fee' 	=> 'Delivery - Success Fee',
				'stockless' 	=> 'Delivery - Stockless Purchasing',
				'on_call' 		=> 'Delivery - On Call Basic'
			);

			if (($a['jwp_start'] == '' || $a['jwp_start'] == null || $a['jwp_start'] == '0000-00-00') && ($a['jwp_end'] == '' || $a['jwp_end'] == null || $a['jwp_end'] == '0000-00-00')) {
				$jwp_date = ' - ';
			} else {
				$jwp_date = date('d M Y', strtotime($a['jwp_start'])) . ' sampai ' . date('d M Y', strtotime($a['jwp_end']));
			}

			$list_sistem_kontrak = '';
			if ($a['sistem_kontrak'] == '' || $a['sistem_kontrak'] == 'null') {
				$list_sistem_kontrak = '-';
			} else {
				$no = 1;
				$decode_siskon = json_decode($a['sistem_kontrak']);
				if (!is_array($decode_siskon)) {
					$decode_siskon = array();
				}
				foreach ($decode_siskon as $keys => $values) {
					if (isset($sistem_kontrak[$values])) {
						$list_sistem_kontrak .= $no . '.' . ucfirst(str_replace('_', ' ', $sistem_kontrak[$values])) . '<br>';
						$no++;
					}
				}
				if ($list_sistem_kontrak === '') {
					$list_sistem_kontrak = '-';
				}
			}

			if ($a['is_multiyear'] == 1) {
				$data_multi_years = $this->pm->get_multi_years($id);
				$no = 1;

				foreach ($data_multi_years as $key => $value) {
					$is_multiyear_field .= '<tr>
												<th colspan="2">Detail Anggaran #' . $no . '</th>
											</tr>
											<tr>
												<th>Anggaran (IDR)</th>
												<td>Rp. ' . number_format($value['idr_anggaran']) . '</td>
											</tr>
											<tr>
												<th>Anggaran (USD)</th>
												<td>USD ' . number_format($value['usd_anggaran']) . '</td>
											</tr>
											<tr>
												<th>Tahun Anggaran</th>
												<td>' . $value['year_anggaran'] . '</td>
											</tr>';
					$no++;
				}
			} else {
				$is_multiyear_field = '	<tr>
											<th>Anggaran (IDR)</th>
											
											<td>Rp. ' . number_format($a['idr_anggaran']) . '</td>
										</tr>
										<tr>
											<th>Anggaran (USD)</th>
											
											<td>USD ' . number_format($a['usd_anggaran']) . '</td>
										</tr>
										<tr>
											<th>Tahun Anggaran</th>
											
											<td>' . $a['year_anggaran'] . '</td>
										</tr>';
			}

			$data .= '<table>
						<tr>
							<th colspan="2">Data FPPBJ - ' . $a['nama_pengadaan'] . '</th>
						</tr>
						<tr>
							<th>No. PR</th>
							
							<td>' . $a['no_pr'] . '</td>
						</tr>
						<tr>
							<th>Tipe PR</th>
							
							<td>' . str_replace('_', ' ', strtoupper($a['tipe_pr'])) . '</td>
						</tr>
						<tr>
							<th>Lampiran PR</th>
							
							<td>' . (($a['pr_lampiran'] == '' || $a['pr_lampiran'] == null) ? '-' : 'Ada') . '</td>
						</tr>
						<tr>
							<th>Nama Pengadaan</th>
							
							<td>' . $a['nama_pengadaan'] . '</td>
						</tr>
						<tr>
							<th>Tipe Pengadaan</th>
							
							<td>' . str_replace('_', ' ', ucfirst($a['tipe_pengadaan'])) . '</td>
						</tr>
						<tr>
							<th>Jenis Detail Pengadaan</th>
							
							<td>' . str_replace('_', ' ', ucfirst($a['jenis_pengadaan'])) . '</td>
						</tr>
						<tr>
							<th>Metode Pengadaan</th>
							
							<td>' . $a['metode_name'] . '</td>
						</tr>
						' . $is_multiyear_field . '
						<tr>
							<th>KAK / Spesifikasi Teknis</th>
							
							<td>' . (($a['kak_lampiran'] == '' || $a['kak_lampiran'] == null) ? '-' : 'Ada') . '</td>
						</tr>
						<tr>
							<th>Ketersediaan HPS</th>
							
							<td>' . (($a['hps'] == '' || $a['hps'] == null) ? '-' : $hps[$a['hps']]) . '</td>
						</tr>
						<tr>
							<th>Lingkup Kerja</th>
							
							<td>' . nl2br(htmlspecialchars(strip_tags((string) $a['lingkup_kerja']), ENT_QUOTES, 'UTF-8')) . '</td>
						</tr>
						<tr>
							<th>Penggolongan Penyedia Jasa (Usulan)</th>
							
							<td>' . (($a['penggolongan_penyedia'] == '' || $a['penggolongan_penyedia'] == null || $a['penggolongan_penyedia'] == '0') ? '-' : $penyedia[$a['penggolongan_penyedia']]) . '</td>
						</tr>
						<tr>
							<th>Masa Penyelesaian Pekerjaan</th>
							
							<td>' . date('d M Y', strtotime($a['jwpp_start'])) . ' sampai ' . date('d M Y', strtotime($a['jwpp_end'])) . '</td>
						</tr>
						<tr>
							<th>Masa Pemeliharaan</th>
							
							<td>' . $jwp_date . '</td>
						</tr>
						<tr>
							<th>Metode Pembayaran (Usulan)</th>
							
							<td>' . nl2br(htmlspecialchars(strip_tags((string) $a['desc_metode_pembayaran']), ENT_QUOTES, 'UTF-8')) . '</td>
						</tr>
						<tr>
							<th>Jenis Kontrak (Usulan)</th>
							
							<td>' . (($a['jenis_kontrak'] == '' || $a['jenis_kontrak'] == null) ? '-' : $jenis_kontrak[$a['jenis_kontrak']]) . '</td>
						</tr>
						<tr>
							<th>Sistem Kontrak (Usulan)</th>
							
							<td>' . $list_sistem_kontrak . '</td>
						</tr>
						<tr>
							<th>Keterangan</th>
							
							<td>' . nl2br(htmlspecialchars(strip_tags((string) $a['desc_dokumen']), ENT_QUOTES, 'UTF-8')) . '</td>
						</tr>
					</table> <br>';

			if ($a['tipe_pengadaan'] == 'jasa') {
				$analisa_resiko = $this->pm->get_data_analisa($id);
				//  print_r($data);
				$table_analisa = '';
				$total_category = '';
				$total = '';
				$no = 1;
				$getCat = array();
				foreach ($analisa_resiko as $key => $value) {
					// Generate Question
					if ($key == 0) {
						$question = "Jenis Pekerjaan";
					} elseif ($key == 1) {
						$question = "Lokasi Kerja";
					} elseif ($key == 2) {
						$question = "Materi Peralatan yang digunakan";
					} elseif ($key == 3) {
						$question = "Potensi paparan terhadap bahaya tempat kerja";
					} elseif ($key == 4) {
						$question = "Potensi paparan terhadap bahaya bagi personil";
					} elseif ($key == 5) {
						$question = "Pekerjaan secara bersamaan oleh kontraktor berbeda";
					} elseif ($key == 6) {
						$question = "Jangka Waktu Pekerjaan";
					} elseif ($key == 7) {
						$question = "Konsekuensi pekerjaan potensian";
					} elseif ($key == 8) {
						$question = "Pengalaman Kontraktor";
					} elseif ($key == 9) {
						$question = "Paparan terhadap publisitas negatif";
					}

					$manusia 	= $this->setCategory($value['manusia']);
					$asset 		= $this->setCategory($value['asset']);
					$lingkungan = $this->setCategory($value['lingkungan']);
					$hukum 		= $this->setCategory($value['hukum']);

					//SET CATEGORY PER QUESTION 
					if ($manusia == "extreme" || $asset == "extreme" || $lingkungan == "extreme" || $hukum == "extreme") {
						$category = '<span id="catatan" class="catatan red">E</span>';
					} else if ($manusia == "high" || $asset == "high" || $lingkungan == "high" || $hukum == "high") {
						$category = '<span id="catatan" class="catatan red">H</span>';
					} else  if ($manusia == "medium" || $asset == "medium" || $lingkungan == "medium" || $hukum == "medium") {
						$category = '<span id="catatan" class="catatan yellow">M</span>';
					} else if ($manusia == "low" || $asset == "low" || $lingkungan == "low" || $hukum == "low") {
						$category = '<span id="catatan" class="catatan green">L</span>';
					} else {
						$category = '<span id="catatan" class="catatan">?</span>';
					}

					array_push($getCat, $category);

					$table_analisa .= '<tr class="q' . $no . '">
												<td>' . $no . '</td>
												<td>' . $question . '</td>
												<td align="center">
													' . $value['apa'] . '
												</td>
												<td align="center">' . $value['manusia'] . '</td>
												<td align="center">' . $value['asset'] . '</td>
												<td align="center">' . $value['lingkungan'] . '</td>
												<td align="center">' . $value['hukum'] . '</td>
												<td align="center">' . $category . '</td>
										</tr>';
					$no++;
				}

				if (in_array('<span id="catatan" class="catatan red">E</span>', $getCat, TRUE)) {
					$total = '<span id="catatan" class="catatan red">E</span>';
				} else if (in_array('<span id="catatan" class="catatan red">H</span>', $getCat, TRUE)) {
					$total = '<span id="catatan" class="catatan red">H</span>';
				} else if (in_array('<span id="catatan" class="catatan yellow">M</span>', $getCat, TRUE)) {
					$total = '<span id="catatan" class="catatan yellow">M</span>';
				} else if (in_array('<span id="catatan" class="catatan green">L</span>', $getCat, TRUE)) {
					$total = '<span id="catatan" class="catatan green">L</span>';
				} else {
					$total = '-';
				}

				$total_category .= '<tr>
										<td colspan="7" style="text-align:right">Hasil Penilaian Keseluruhan :</td><td style="text-align:center!important">' . $total . '</td>
									</tr>';

				$table_analisa_resiko = '<table class="penilaian_resiko preview" border=1>
								 			<thead class="sticky">
								 				<tr>
													<th colspan="8">Penilaian Analisa Risiko</th>
												</tr>
												<tr class="header">
									 				<th rowspan="2">No</th>
									 				<th rowspan="2">Daerah Risiko</th>
									 				<th rowspan="2">Apa</th>
									 				<th colspan="5" style="text-align: center;">Konsekuensi <br> L/M/H</th>
									 			</tr>
									 			<tr class="header bottom">
									 				<th>Manusia</th>
									 				<th>Aset</th>
									 				<th>Lingkungan</th>
									 				<th>Reputasi <br>&amp; Hukum</th>
									 				<th>Catatan</th>
									 			</tr>
								 			</thead>
											<tbody>
											' . $table_analisa . '
											' . $total_category . '
											</tbody>
										</table> <br> <h3>Usulan DPT</h3>';

				$data .= $table_analisa_resiko;
			}

			$get_dpt = $this->ex->get_analisa($id);
			$dpt = '<table>
						<thead>
							<tr>
								<th>Nama DPT</th>
							</tr>
						</thead>
						<tbody>';
			$no = 1;
			if (!empty($get_dpt['dpt_list'])) {
				foreach ($get_dpt['dpt_list'] as $key) {
					$dpt .= '<tr>
							<td>' . $no++ . '. ' . $key . '</td>
						</tr>';
				}
			} else {
				$dpt .= '<tr>
							<td> - </td>
						</tr>';
			}
			$dpt .= '<tr><th>Non DPT (Usulan)</th></tr>';
			if ($get_dpt['usulan'] != '') {
				$dpt .= '<tr>
							<td>' . $get_dpt['usulan'] . '</td>
						</tr></tbody>
						</table><br>';
			} else {
				$dpt .= '<tr>
								<td> - </td>
							</tr></tbody>
						</table><br>';
			}

			$data .= $dpt;

			if ($a['metode_name'] == 'Swakelola') {
				$swakelola = $this->pm->get_swakelola($id);

				if ($swakelola['waktu'] == 1) {
					$waktu_swakelola = 'Penyelesaian Pekerjaan kurang dari 3 Bulan';
				} else if ($swakelola['waktu'] == 2) {
					$waktu_swakelola = 'Penyelesaian Pekerjaan lebih dari 3 Bulan s.d kurang dari 6 Bulan';
				} else {
					$waktu_swakelola = 'Penyelesaian pekerjaan ≥ 6 Bulan';
				}

				if ($swakelola['biaya'] == 1) {
					$biaya_swakelola = 'Biaya pelaksanaan pekerjaan kurang dari 50 juta';
				} else if ($swakelola['biaya'] == 2) {
					$biaya_swakelola = 'Biaya pelaksanaan pekerjaan lebih dari 50 Bulan s.d kurang dari 100 juta';
				} else {
					$biaya_swakelola = 'Biaya pelaksanaan pekerjaan lebih dari 100 juta';
				}

				if ($swakelola['tenaga'] == 1) {
					$tenaga_swakelola = 'Kompetensi dan/atau ketersediaan jumlah Tenaga Kerja di Perusahaan memenuhi sebagai perencana dan pelaksana dan pengawas';
				} else if ($swakelola['tenaga'] == 2) {
					$tenaga_swakelola = 'Kompetensi dan/atau ketersediaan jumlah Tenaga Kerja di Perusahaan memenuhi salah satu atau lebih sebagai perencana dan/atau pelaksana dan/atau pengawas';
				} else {
					$tenaga_swakelola = 'Kompetensi dan/atau ketersediaan jumlah Tenaga Kerja di Perusahaan tidak memenuhi sebagai perencana dan pelaksana dan pengawas';
				}

				if ($swakelola['bahan'] == 1) {
					$bahan_swakelola = 'Bahan mudah didapatkan langsung oleh Pekerja NR';
				} else if ($swakelola['bahan'] == 2) {
					$bahan_swakelola = 'Bahan dapat diadakan melalui pihak ketiga';
				} else {
					$bahan_swakelola = 'Bahan lebih efisien apabila diadakan oleh pihak ketiga';
				}

				if ($swakelola['peralatan'] == 1) {
					$peralatan_swakelola = 'Ketersediaan jumlah dan kemampuan peralatan kerja memenuhi kebutuhan pekerjaan';
				} else if ($swakelola['peralatan'] == 2) {
					$peralatan_swakelola = 'Ketersediaan jumlah dan/atau kemampuan peralatan kerja tidak memenuhi kebutuhan pekerjaan';
				} else {
					$peralatan_swakelola = 'Peralatan lebih efisien apabila diadakan oleh pihak ketiga';
				}

				$table_swakelola = '<table>
					<tr>
						<th colspan="2">Data Swakelola</th>
					</tr>
					<tr>
						<th>Waktu</th>
						
						<td>' . $waktu_swakelola . '</td>
					</tr>
					<tr>
						<th>Biaya</th>
						
						<td>' . $biaya_swakelola . '</td>
					</tr>
					<tr>
						<th>Tenaga Kerja</th>
						
						<td>' . $tenaga_swakelola . '</td>
					</tr>
					<tr>
						<th>Bahan</th>
						
						<td>' . $bahan_swakelola . '</td>
					</tr>
					<tr>
						<th>Peralatan</th>
						
						<td>' . $peralatan_swakelola . '</td>
					</tr>
				</table> <br>';

				$data .= $table_swakelola;
			}

			$dataFP3 = $this->pm->get_data_fp3($id);

			$status_metode_fp3 = array(
				1 => 'Pelelangan',
				2 => 'Pemilihan Langsung',
				3 => 'Swakelola',
				4 => 'Penunjukan Langsung',
				5 => 'Pengadaan Langsung'
			);

			$table_fp3 = '<table>
				<tr>
					<th colspan=2> Data FP3 - ' . $a['nama_pengadaan'] . '</th>
				</tr>
				<tr>
					<th> Status </th>
					<td>' . ucfirst($dataFP3['status']) . '</td>
				</tr>
				<tr>
					<th>Nama Pengadaan (Lama)</th>
					<td>' . htmlspecialchars((string) $a['nama_pengadaan'], ENT_QUOTES, 'UTF-8') . '</td>
				</tr>
				<tr>
					<th>Nama Pengadaan (Baru)</th>
					<td>' . htmlspecialchars((string) $dataFP3['nama_pengadaan'], ENT_QUOTES, 'UTF-8') . '</td>
				</tr>
				<tr>
					<th>No.PR (Lama)</th>
					<td>' . $a['no_pr'] . '</td>
				</tr>
				<tr>
					<th>No.PR (Baru)</th>
					<td>' . $dataFP3['no_pr'] . '</td>
				</tr>
				<tr>
					<th>Metode Pengadaan (Lama)</th>
					<td>' . $a['metode_name'] . '</td>
				</tr>
				<tr>
					<th>Metode Pengadaan (Baru)</th>
					<td>' . $status_metode_fp3[$dataFP3['metode_pengadaan']] . '</td>
				</tr>
				<tr>
					<th>Jadwal Pengadaan (Lama)</th>
					<td>' . date('d M Y', strtotime($a['jwpp_start'])) . ' sampai ' . date('d M Y', strtotime($a['jwpp_end'])) . '</td>
				</tr>
				<tr>
					<th>Jadwal Pengadaan (Baru)</th>
					<td>' . date('d M Y', strtotime($dataFP3['jwpp_start'])) . ' sampai ' . date('d M Y', strtotime($dataFP3['jwpp_end'])) . '</td>
				</tr>
				<tr>
					<th>Keterangan (Lama)</th>
					<td>' . nl2br(htmlspecialchars(strip_tags((string) $a['desc_dokumen']), ENT_QUOTES, 'UTF-8')) . '</td>
				</tr>
				<tr>
					<th>Keterangan (Baru)</th>
					<td>' . nl2br(htmlspecialchars(strip_tags((string) $dataFP3['desc']), ENT_QUOTES, 'UTF-8')) . '</td>
				</tr>
				<tr>
					<th>KAK / Spesifikasi Teknis (Lama)</th>
					<td>' . (($a['kak_lampiran'] == '' || $a['kak_lampiran'] == null) ? '-' : 'Ada') . '</td>
				</tr>
				<tr>
					<th>KAK / Spesifikasi Teknis (Baru)</th>
					<td>' . (($dataFP3['kak_lampiran'] == '' || $dataFP3['kak_lampiran'] == null) ? '-' : 'Ada') . '</td>
				</tr>
			</table> <br>';

			$data .= $table_fp3;
		}

		$his_approval = $this->$modelAlias->getRiwayatPengadaan($id, 'approval');
		// print_r($his_approval);die;
		$data .= '<table>
			<thead>
				<tr>
					<th colspan="4">Riwayat Approval - ' . $a['nama_pengadaan'] . '</th>
				</tr>
				<tr>
					<th>Tanggal</th>
					<th>Status</th>
					<th>Di Setujui / Revisi Oleh</th>
					<th>Keterangan</th>
				</tr>
			</thead>
			<tbody>';
		
		if (count($his_approval) > 0) {
			foreach ($his_approval as $key => $value) {
				$_status = array(
					0 => 'FPPBJ',
					2 => 'FKPBJ',
					1 => 'FP3'
				);
				if (($value['date_approval'] == '' || $value['date_approval'] == null || $value['date_approval'] == '0000-00-00')) {
					$date_approval = date('d M Y', strtotime($value['entry_stamp']));
				} else {
					$date_approval = date('d M Y', strtotime($value['date_approval']));
				}

				$approve_by = $this->$modelAlias->getDataAdmin($value['approved_by']);
				$approve_by_name = (is_array($approve_by) && isset($approve_by['name']) && $approve_by['name'] !== '') ? $approve_by['name'] : '-';

				$data .= '<tr>
							<td>' . $date_approval . '</td>
							<td>' . ucfirst($value['status']) . ' ' . $_status[$value['is_status']] . '</td>
							<td>' . $approve_by_name . '</td>
							<td>' . (($value['desc_reject'] == '' || $value['desc_reject'] == null) ? '-' : nl2br(htmlspecialchars(strip_tags((string) $value['desc_reject']), ENT_QUOTES, 'UTF-8'))) . '</td>
						</tr>';
			}
		} else {
			$data .= '<tr><td colspan=4 align="center">Tidak Ada Data</td></tr>';
		}

		$data .= '</tbody>
		</table>';

		$data .= '</body>
		</html>';

		

		$data = preg_replace('/&(?![A-Za-z0-9#]+;)/', '&amp;', $data);
		$data = preg_replace('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F\\x7F]/', '', $data);
		if (function_exists('iconv')) {
			$iconvValue = @iconv('UTF-8', 'UTF-8//IGNORE', $data);
			if ($iconvValue !== false) {
				$data = $iconvValue;
			}
		}
		if (class_exists('DOMDocument')) {
			$dom = new DOMDocument('1.0', 'UTF-8');
			$previous = libxml_use_internal_errors(true);
			@$dom->loadHTML('<?xml encoding="UTF-8">' . $data);
			$data = $dom->saveHTML();
			libxml_clear_errors();
			libxml_use_internal_errors($previous);
		}

		$dompdf = new DOMPDF();
		try {
			$dompdf->set_option('enable_html5_parser', true);
		} catch (Exception $e) {
		}
		$dompdf->load_html($data);
		$dompdf->set_paper("A4", "potrait");
		try {
			$dompdf->render();
		} catch (Exception $e) {
			$plain_source = preg_replace('~<style\b[^>]*>.*?</style>~is', '', $data);
			$plain = htmlspecialchars(trim(strip_tags($plain_source)), ENT_QUOTES, 'UTF-8');
			$fallback = '<!DOCTYPE html><html><head><meta charset="utf-8"><style>body{font-family:Arial,sans-serif;font-size:10px;}table{width:100%;border-collapse:collapse;}td{border:1px solid #000;padding:6px;vertical-align:top;}pre{margin:0;white-space:pre-wrap;}</style></head><body><table><tr><td><pre>' . $plain . '</pre></td></tr></table></body></html>';
			$dompdf = new DOMPDF();
			try {
				$dompdf->set_option('enable_html5_parser', true);
			} catch (Exception $e) {
			}
			$dompdf->load_html($fallback);
			$dompdf->set_paper("A4", "potrait");
			$dompdf->render();
		}
		$dompdf->stream("Riwayat Pengadaan - " . $a['nama_pengadaan'] . ".pdf", array("Attachment" => 1));
		return;
	}

	public function setCategory($val)
	{
		if ($val >= 1 && $val <= 4) {
			return 'low';
			// return '<span id="catatan" class="catatan green">L</span>';
		} else if ($val > 4 && $val <= 9) {
			return 'medium';
			// return '<span id="catatan" class="catatan yellow">M</span>';		
		} else if ($val >= 10 && $val <= 14) {
			return 'high';
			// return '<span id="catatan" class="catatan red">H</span>';
		} else if ($val >= 15 && $val <= 25) {
			return 'extreme';
			// return '<span id="catatan" class="catatan red">E</span>';
		} else {
			return false;
		}
	}
}

/* End of file Riwayat_pengadaan.php */
/* Location: ./application/controllers/Riwayat_pengadaan.php */
