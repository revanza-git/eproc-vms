<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Rekap extends MY_Controller {

	public $form;
	public $modelAlias 	= 'pm';
	public $alias 		= 'ms_fppbj';
	public $module 		= 'rekap';
	public $admin		= null;

	public function __construct(){
		parent::__construct();
		include_once APPPATH.'third_party/dompdf2/dompdf_config.inc.php';
		$this->load->model('Pemaketan_model','pm');
		$this->load->model('Fppbj_model','fm');
		$this->load->model('Main_model','mm');
		$this->load->library('session');


		$this->form = array(
			'form' => array(
				array(
					'field'	=> 	'note',
					'type'	=>	'tinymce',
					'label'	=>	'Catatan',
				),array(
					'type'	=>	'date',
					'label'	=>	'Tanggal Closing',
					'field' =>  'date_close',
					// 'rules'	=>	'required'
				),array(
					'field'	=> 	'lampiran',
					'type'	=>	'file',
					'label'	=>	'Lampiran beserta ttd basah',
					'upload_path'=> base_url('assets/lampiran/rekap/'),
					'upload_url'=> site_url('perencanaan/rekap/upload_lampiran'),
					'allowed_types'=> '*',
					//'rules' => 	'required',
				)
			),

			'successAlert'=>'Berhasil mengubah data!',
		);

		$this->admin	 	= $this->session->userdata('admin');
		$year = date('Y'); // Initialize $year variable with current year
		$this->getData 		= $this->pm->getDataRekap($year);
		$this->getDataYear	= $this->pm->getDataYear($year);
		$this->approveURL	= site_url('/perencanaan/rekap/save/');
		
		// Filter form elements to only include valid validation rules
		$validation_rules = array();
		foreach ($this->form['form'] as $element) {
			if (isset($element['field']) && isset($element['rules'])) {
				$validation_rules[] = $element;
			}
		}
		if (!empty($validation_rules)) {
			$this->form_validation->set_rules($validation_rules);
		}

	}

	public function index($id = null){
		$this->breadcrumb->addlevel(1, array(
			'url' => site_url('perencanaan/rekap'),
			'title' => 'Rekap Perencanaan Pertahun'
		));

		$data = array(); // Initialize $data variable
		$this->header = 'Rekap Perencanaan';
		$this->content = $this->load->view('perencanaan/rekap/index',$data, TRUE);
		$this->script = $this->load->view('perencanaan/rekap/index_js', $data, TRUE);
		parent::index($id);
	}

	public function year($year=""){

		$this->breadcrumb->addlevel(1, array(
			'url' => site_url('perencanaan/rekap'),
			'title' => 'Rekap Perencanaan'
		));
		$this->breadcrumb->addlevel(2, array(
			'url' => site_url('year/'.$year),
			'title' => $year
		));

		$data['year']	= $year;
		$data['is_close'] = $this->pm->is_close($year);
		$this->header 	= 'Rekap Perencanaan - '.$year;
		$this->content 	= $this->load->view('perencanaan/rekap/year/list',$data, TRUE);
		$this->script 	= $this->load->view('perencanaan/rekap/year/list_js', $data, TRUE);
		parent::index();
	}

	public function getDataYear($year = null){
		$config['query'] = $this->pm->getDataYear($this->form, $year);
		$return = $this->tablegenerator->initialize($config);
		echo json_encode($return);
	}
	
	public function getData($id = null)
	{
		$config['query'] = $this->getData;
		$return = $this->tablegenerator->initialize($config);
		echo json_encode($return);
	}


	public function getSingleData($id=null){
		$admin = $this->session->userdata('admin');
		$param_  = ($admin['id_role'] == 4) ? ($param_=1) : (($admin['id_role'] == 3) ? ($param_=2) : (($admin['id_role'] == 2) ? ($param_=3) : ''));

		$this->form['url'] 		= site_url('fppbj/approve/'.$id.'/'.$param_);
		$this->form['button'] = array();

		if ($admin['id_role'] == 2 || $admin['id_role'] == 3 || $admin['id_role'] == 4) {
			$push = array(
				// array(
				// 	'type' 	=> 'export',
				// 	'link'	=> site_url($this->exportUrl.$id),
				// 	'label' => '<i class="fas fa-download"></i>&nbsp;Download PDF'
				// ),
				array(
					'type' 	=> 'export',
					'link'	=> $this->form['url'],
					'label' => '<i style="line-height:25px;" class="fas fa-thumbs-up"></i>&nbsp;Setujui Data'
				),
				array(
					'type' => 'cancel',
					'label' => 'Tutup'
				)
			);
			$this->form['button'] = $push;
			// array_push($this->form['button'], $push);
		}else{
			$push = array(
				array(
					'type' => 'cancel',
					'label' => 'Tutup'
				)
			);
			$this->form['button'] = $push;
		}

		parent::getSingleData($id);
	}

	
	public function approve($year){
		$this->session->set_userdata('year', $year);

		$this->form['url'] = $this->approveURL;
		$this->form['button'] = array(
			array(
				'type' => 'submit',
				'label' => '<i class="fas fa-upload"></i>&nbsp;Upload',
			) ,
			array(
				'type' => 'cancel',
				'label' => 'Batal'
			)
		);
		echo json_encode($this->form);
	}

	public function save($data = null){
		$modelAlias = $this->modelAlias;

		// if ($this->validation()) {
			$save = $this->input->post();
			$save['id_fppbj']		= $this->session->userdata('export_id');
			$save['year'] 			= $this->session->userdata('year');
			$save['entry_stamp'] 	= timestamp();
			if ($this->$modelAlias->approve($save)) {
				$this->session->set_flashdata('msg', $this->successMessage);
				$this->deleteTemp($save, $save['lampiran']);
				// return true;
				redirect(site_url('perencanaan/rekap/year/'.$save['year']));
			}
		// }
	}

	function export($year = null){
		$page = '<!DOCTYPE html>
				<html lang="en">
				<head>
				    <title>Table Layout</title>
				    <style>
				        @import url("https://fonts.googleapis.com/css?family=Open+Sans:300,400,700");
				        body {
				            width: 709px;
				            padding: 15px;
				            font-family: "Open Sans";
				            /*display: -webkit-flex;
				            display: -moz-flex;
				            display: -ms-flex;
				            display: -o-flex;
				            display: flex;
				            -webkit-flex-direction: column;
				            -moz-flex-direction: column;
				            -ms-flex-direction: column;
				            -o-flex-direction: column;
				            flex-direction: column;
				            -ms-align-items: center;
				            align-items: center;*/
				            background-color: #f9f9f9;
				        }
				        tr td {
							border: 1px solid #a0a0a0;
							border-spacing:none;
				        }
				        tr th {
							border: 1px solid #a0a0a0;
							border-spacing:none;
				        }
				        .export {
				          background-color: #fff;
				          width: 100%;
				          margin: 5px 0; }
				          .export td, .export th {
				            vertical-align: middle;
				            text-align: center;
				            border-spacing: none;
				            padding: 5px; }
				          .export th {
				            padding: 5px; }
				          .export-logo {
				            margin: 15px; }
				            .export-logo img {
				              height: 55px; }
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
				            height: 150px;
				            padding: 5px; }
				    </style>

				</head>

				<body>
				    <table class="export"> 
				        <tr> 
				            <th colspan="3">
				                <div class="export-logo">
				                    <img src="'.base_url('/assets/images/NUSANTARA-REGAS-2.png').'"">
				                </div>
				            </th> 
				            <th colspan="4">
				                <div class="export-name">
				                    formulir perubahan perencanaan pengadaan b/j ("fp3")
				                </div>
				            </th> 
				        </tr> 
				    </table>
				    <table class="export">
				        <tr> 
				            <td style="width: 50%;">
				                <ul class="export-info">
				                    <li>
				                        <span>Kepada</span> 
				                        <span>:</span> 
				                        <span>Kepala Divisi SDM</span>
				                    </li>
				                    <li>
				                        <span>Dari</span> 
				                        <span>:</span> 
				                        <span>Kepala Divisi SDM</span>
				                    </li>
				                    <li>
				                        <span>Pusat Biaya</span> 
				                        <span>:</span> 
				                        <span>Kepala Divisi SDM</span>
				                    </li>
				                </ul>
				            </td>
				            <td style="vertical-align: top; width: 50%;">
				                <ul class="export-info">
				                    <li>
				                        <span>Nomor</span> 
				                        <span>:</span> 
				                        <span>Kepala Divisi SDM</span>
				                    </li>
				                    <li>
				                        <span>Tanggal</span> 
				                        <span>:</span> 
				                        <span>Kepala Divisi SDM</span>
				                    </li>
				                </ul>
				            </td>
				        </tr>
				    </table>
				    <table class="export">
				        <tr> 
				            <th rowspan="2">No</th> 
				            <th rowspan="2">No PR <br> (*apabila ada)</th> 
				            <th rowspan="2">
				                Nama Pengadaan B/J <br>
				                (Sesuai Perencanaan Pengadaan B/J 
				                Tahun 2018)
				            </th> 
				            <th colspan="3">Perubahan Perencanaan</th> 
				            <th rowspan="2">Keterangan</th>
				        </tr> 
				        <tr>
				            <td>378</td>
				            <td>378</td>
				            <td>378</td>
				        </tr>
				        <tr>
				            <td>378</td>
				            <td>378</td>
				            <td>378</td>
				            <td>378</td>
				            <td>378</td>
				            <td>378</td>
				            <td>378</td>
				        </tr>
				    </table> 
				    <table class="export">
				        <tr>
				            <td colspan="3" rowspan="2">
				                Pengguna Barang/Jasa
				                (setingkat Ka. Dept)
				            </td>
				            <td colspan="4">
				                Persetujuan Perubahan
				            </td>
				        </tr>
				        <tr>
				            <td colspan="4">
				                Pengguna Barang/Jasa
				                (setingkat Ka. Divisi atau Direktur Utama untuk fungsi leher)
				            </td>
				        </tr>
				        <tr>
				            <td colspan="3" class="sign-area" style="width: 50%;">
				                
				            </td>
				            <td colspan="4" class="sign-area" style="width: 50%;">
				                
				            </td>
				        </tr>
				        <tr>
				            <td colspan="3" style="width: 50%;">(.......................)</td>
				            <td colspan="4" style="width: 50%;">(.......................)</td>
				        </tr>
				    </table>

				</body>

				</html>';

		// print_r($page);die;

		$dompdf = new DOMPDF();
		$dompdf->load_html($page);
		$dompdf->set_paper("A4", "landscape");
        // $dompdf->set_option('isHtml5ParserEnabled', TRUE);
		$dompdf->render();

		
			$dompdf->stream("Rekap Perencanaan Pengadaan - ".$year.".pdf", array("Attachment" => 1));
	}

	public function form_rekap_pdf($year)
	{
		$this->form = array(
				'form'=>array(
					array(
						'field' => 'no',
						'type' => 'text',
						'label' => 'Nomor'
					),
					array(
						'field' => 'tanggal',
						'type' => 'date',
						'label' => 'Tanggal'
					),
					array(
						'field'	=> 	'description',
						'type'	=>	'tinymce',
						'label'	=>	'Deskripsi',
					)
				)
		);
		$data = $this->fm->get_description();
		
		foreach($this->form['form'] as $key => $element) {
			$this->form['form'][$key]['value'] = $data[$element['field']];
			if($this->form['form'][$key]['type']=='date_range'){
				$_value = array();
				
				foreach ($this->form['form'][$key]['field'] as $keys => $values) {
					$_value[] = $data[$values];
					
				}
				$this->form['form'][$key]['value'] = $_value;
			}
		}
		$this->form['url'] = site_url('perencanaan/rekap/rekap_pdf/'.$year);
		$this->form['button'] = array(
			array(
				'type' => 'submit',
				'label' => 'Export',
			) ,
			array(
				'type' => 'cancel',
				'label' => 'Batal'
			)
		);
		echo json_encode($this->form);
	}

	function rekap_pdf($year = null){

		$dateHead = $this->date_week($year);
		$dateDetail = $this->date_detail($year);
		$save = $this->input->post();
		$script = '
		<script language="JavaScript" type="text/javascript">
				$(document).ready(function() {
					console.log( "ready!" );
				});
		</script>';
		$page = '<!DOCTYPE html>
				<html lang="en">
				<head>
					<title>Table Layout</title>
					<meta charset="UTF-8" />
					<meta name="viewport" content="width=device-width, initial-scale=1" />
					<style>
						@import url("https://fonts.googleapis.com/css?family=Open+Sans:300,400,700");
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
				
				<div class="export-logo">
					<img src="'.base_url().'assets/images/NUSANTARA-REGAS-2.png" style="float: left">
				</div>
					<table class="export nopadding">
						<tr>
							<td>
								<div class="text-wrapper">
									<div class="text-header" style="font-size: 15px; font-weight: 700; margin-bottom: 10px">
										KOMITMEN PENGADAAN BARANG/JASA <br> TAHUN '.$year.'
									</div>
									<div class="text-content" style="text-align: left; font-size: 12px">
										
									Kami yang bertandatangan di  bawah ini, menyatakan komitmen atas pengadaan barang/jasa tahun '.$year.', sebagai berikut: 
										<ul style="margin-left: -25px">
											<li>
												1. Pengadaan Barang/Jasa dilaksanakan berdasarkan pada prinsip, etika dan kebijakan Pengadaan Barang/Jasa Perusahaan;
											</li>
											<li>
												2. Perencana pengadaan barang/jasa (daftar terlampir) telah disusun berdasarkan rencana kerja masing-masing fungsi di Perusahaan; 
											</li>
											<li>
												3. Perubahan atas pengadaan barang/jasa dalam daftar perencanaan tersebut diatas, baik nama, ruang lingkup, jenis, metode dan jadwal pengadaan, wajib disampaikan tertulis kepada GM SOM dan Umum, dengan melampirkan Formulir Perubahan Perencanaan Pengadaan Barang/Jasa Tahun '.$year.' ("Formulir Perubahan") yang ditandatangani oleh Manager (Fungsi Leher) atau General Manager terkait; 
											</li>
											<li>
												4 . Permintaan pengadaan barang/jasa diluar daftar perencanaan tersebut diatas atau pengadaan barang/jasa yang terlambatlmundur dari jadwal dalam daftar perencanaan terse but diatas, akan tetap diproses namun tidak menjadi prioritas dan mempertimbangkan beban kerja dari Fungsi Pengadaan pada saat permintaan pengadaan barang/jasa tersebut diajukan, kecuali ditentukan lain oleh Direktur Utama.
											</li>
										</ul>
										Demikian komitmen ini kami buat dengan sebenar-benarnya, untuk digunakan sebagaimana mestinya
									</div>
								</div>
							</td>
						</tr>
					</table>
					<table class="export no-border nopadding">
						<caption style="font-size: 13px">Jakarta, 01 April '.$year.' </caption>
						<tr>
							<td colspan="6">
								<div class="sign-wrapper">
									<div class="sign-area"></div>
									<div class="sign-name">
										Bara Frontasia
									</div>
									<div class="sign-position">
										Direktur Operasi dan Komersial
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div class="sign-wrapper">
									<div class="sign-area"></div>
									<div class="sign-name">
										Bara Frontasia
									</div>
									<div class="sign-position">
										Direktur Operasi dan Komersial
									</div>
								</div>
							</td>
							<td colspan="2">
								<div class="sign-wrapper">
									<div class="sign-area"></div>
									<div class="sign-name">
										Bara Frontasia
									</div>
									<div class="sign-position">
										Direktur Operasi dan Komersial
									</div>
								</div>
							</td>
							<td colspan="2">
								<div class="sign-wrapper">
									<div class="sign-area"></div>
									<div class="sign-name">
										Bara Frontasia
									</div>
									<div class="sign-position">
										Direktur Operasi dan Komersial
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="sign-wrapper">
									<div class="sign-area"></div>
									<div class="sign-name">
										Bara Frontasia
									</div>
									<div class="sign-position">
										Direktur Operas; dan Komersial
									</div>
								</div>
							</td>
							<td>
								<div class="sign-wrapper">
									<div class="sign-area"></div>
									<div class="sign-name">
										Bara Frontasia
									</div>
									<div class="sign-position">
										Direktur Operas; dan Komersial
									</div>
								</div>
							</td>
							<td>
								<div class="sign-wrapper">
									<div class="sign-area"></div>
									<div class="sign-name">
										Bara Frontasia
									</div>
									<div class="sign-position">
										Direktur Operas; dan Komersial
									</div>
								</div>
							</td>
							<td>
								<div class="sign-wrapper">
									<div class="sign-area"></div>
									<div class="sign-name">
										Bara Frontasia
									</div>
									<div class="sign-position">
										Direktur Operas; dan Komersial
									</div>
								</div>
							</td>
							<td>
								<div class="sign-wrapper">
									<div class="sign-area"></div>
									<div class="sign-name">
										Bara Frontasia
									</div>
									<div class="sign-position">
										Direktur Operas; dan Komersial
									</div>
								</div>
							</td>
							<td>
								<div class="sign-wrapper">
									<div class="sign-area"></div>
									<div class="sign-name">
										Bara Frontasia
									</div>
									<div class="sign-position">
										Direktur Operas; dan Komersial
									</div>
								</div>
							</td>
						</tr>
					</table>
					<div style="margin-top: 2.1rem" class="break">
						<table class="export smaller-text border" style="width: 1050px; border-collapse: collapse;">
							<caption style="font-size: 15px; font-weight: 700; margin-bottom: 10px">
								Rencana Pengadaan Barang/Jasa Tahun '.$year.' <br> Metode Pelelangan, Pemilihan langsung dan Penunjukan langsung serta Swakelola
							</caption>
							<tr class="gery">
								<th rowspan="4">No.</th>
								<th rowspan="4">Pengguna Barang/Jasa</th>
								<th rowspan="4">Paket Pengadaan Barang/Jasa</th>
								<th rowspan="4">Anggaran (include PPN 10%) </th>
								<th rowspan="4">Metode Pengadaan</th>
								<th colspan="54">Persiapan & Permohonan Pengadaan (PP1) - Proses Pengadaan (PP2) - Pelaksanaan Pekerjaan (PP3)</th>
							</tr>
							'.$dateHead.'
							'.$dateDetail.'
						</table>
					</div>
						'.$script.'
					
				</body>
				
				</html>';

		// print_r($page);die;

		$dompdf = new DOMPDF();
		$dompdf->load_html($page);
		$dompdf->set_paper("A4", "landscape");
        // $dompdf->set_option('isHtml5ParserEnabled', TRUE);
		$dompdf->render();
		
			$dompdf->stream("Rekap Perencanaan Pengadaan - ".$year.".pdf", array("Attachment" => 1));
	}

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

		// --------- YEAR ---------
		$table = "<tr>";
			$table .= "<td class='row-small' colspan='".$_week."'>".$year."</td>";
		$table .= "</tr>";
	
			// --------- MONTH ---------
			$table .= "<tr>";
			foreach ($weekly as $month => $value) {
				$month_ = count($weekly[$month]);
					$table .= "<td class='row-small' colspan='".$month_."'>".$month."</td>";
			}
			$table .= "</tr>";

				// --------- WEEK ---------
				$table .= "<tr>";
				foreach ($weekly as $month => $value) {
						foreach ($value as $week) {
								$table .= "<td class='row-small' id='".$week."'>".$week."</td>";
						}
				}
				$table .= "</tr>";

		// print_r($table);
		return $table;
	}

	function date_week_($year=2018, $jwp, $metode){
		define('NL', "\n");
		$jwp 			= json_decode($jwp);
		
		// print_r();

		// DAY BASED ON METODE PROC
		$metode_day		= 0;
		if ($metode == "Pelelangan") {
			$metode_day = 8; //60 hari
		}else if ($metode == "Pemilihan Langsung") {
			$metode_day = 6; //45 hari
		}else if ($metode == "Swakelola") {
			$metode_day = 0;
		}else if ($metode == "Penunjukan Langsung") {
			$metode_day = 3;// 20 hari
		}

		//Variable 
		$start			= $this->get_week($jwp->start);
		$end			= $this->get_week($jwp->end);
		$start_red		= $start - $metode_day;
		$end_red		= $start;
		$start_yellow	= $start_red - 2;
		$end_yellow		= $end_red ;

		$year           = $year;
		$firstDayOfYear = mktime(0, 0, 0, 1, 1, $year);
		$nextMonday     = strtotime('monday', $firstDayOfYear);
		$nextSunday     = strtotime('sunday', $nextMonday);
		$_month			= 0;
		$_week 			= 1;
		$weekly 		= array();
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
							$table .= "<td id='".sprintf('%02d', $week)."' class='row-small is-blue' >&nbsp;</td>";
						}else if ($week >= $start_red && $week <= $end_red){
							// RED
							$table .= "<td id='".sprintf('%02d', $week)."' class='row-small is-red' >&nbsp;</td>";
						}else if ($week >= $start_yellow && $week <= $end_yellow){
							// YELLOW
							$table .= "<td id='".sprintf('%02d', $week)."' class='row-small is-yellow' >&nbsp;</td>";
						}else{
							// PLAIN
							$table .= "<td class='row-small' id='".sprintf('%02d', $week)."' >&nbsp;</td>";
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

		$data = $this->db->select('ms_fppbj.id id_fppbj, year_anggaran, nama_pengadaan, tb_division.name divisi, ms_fppbj.id, idr_anggaran, jenis_pengadaan, tb_proc_method.name metode_pengadaan, desc, jwp')
						->where('year_anggaran', $year)
						->where('is_approved', 1)
						->where('is_status', 0)
						->join('tb_division', 'tb_division.id = ms_fppbj.id_division')
						->join('tb_proc_method', 'tb_proc_method.id = ms_fppbj.metode_pengadaan')
						->get('ms_fppbj')->result_array();

						// $table = array();
		foreach ($data as $key => $value) {
			# code...
			$week = $this->date_week_($year, $value['jwp'], $value['metode_pengadaan']);
			
			$table .= '
					<tr class="content">
						<td id="'.$value['id_fppbj'].'">'.($key + 1).'</td>
						<td>'.$value['divisi'].'</td>
						<td>'.$value['nama_pengadaan'].'</td>
						<td>Rp. '.number_format($value['idr_anggaran']).'</td>
						<td>'.$value['metode_pengadaan'].'</td>
						<td>'.$value['desc'].'</td>
						'.$week.'
					</tr>';

			
		}
		
		return $table;
	}

	public function getDataFppbj($id=null){
		$this->form = array(
			'form'=>array(
				array(
					'field'	=> 	'no_pr',
					'type'	=>	'text',
					'label'	=>	'No. PR',
				),array(
					'field'	=> 	'tipe_pr',
					'type'	=>	'dropdown',
					'label'	=>	'Tipe PR',
					'source'=>	array(0 => 'Pilih Dibawah Ini', 'direct_charge' => 'Direct Charges', 'services' => 'Services', 'user_purchase' => 'User Purchase'),
				),
				array(
					'field'	=> 	'nama_pengadaan',
					'type'	=>	'text',
					'label'	=>	'Nama Pengadaan',
					'rules' => 	'required',
				),array(
					'field'	=> 	'tipe_pengadaan',
					'type'	=>	'dropdown',
					'label'	=>	'Jenis Pengadaan',
					'source'=>	array(0 => 'Pilih Dibawah Ini', 'jasa' => 'Pengadaan Jasa', 'barang' => 'Pengadaan Barang'),
					'rules'	=>	'required'
				),array(
					'field'	=> 	'jenis_pengadaan',
					'type'	=>	'text',
					'label'	=>	'Jenis Detail Pengadaan',
					'rules'	=>	'required'
				),array(
					'field'	=> 	'metode_pengadaan',
					'type'	=>	'dropdown',
					'label'	=>	'Metode Pengadaan',
					'source'=>	$this->mm->getProcMethod(),
					'rules'	=> 	'required'
				),array(
					'field'	=> 	'idr_anggaran',
					'type'	=>	'currency',
					'label'	=>	'Anggaran (IDR)',
				),array(
					'field'	=> 	'usd_anggaran',
					'type'	=>	'currency',
					'label'	=>	'Anggaran (USD)',
				),array(
					'field'	=> 	'year_anggaran',
					'type'	=>	'number',
					'label'	=>	'Tahun Anggaran',
					'rules' => 	'required'
				),array(
					'field'	=> 	'kak_lampiran',
					'type'	=>	'file',
					'label'	=>	'KAK / Spesifikasi Teknis',
					'upload_path'=> base_url('assets/lampiran/fppbj/'),
					'upload_url'=> site_url('fkpbj/upload_lampiran'),
					'allowed_types'=> '*',
				),array(
					'field'	=> 	'hps',
					'type'	=>	'radio',
					'label'	=>	'Ketersediaan HPS',
					'source'=>	array(1 => 'Ada', 0 => 'Tidak Ada')
				),array(
					'field'	=> 	'desc_dokumen',
					'type'	=>	'textarea',
					'label'	=>	'Keterangan',
				),array(
					'field'	=> 	'penggolongan_penyedia',
					'type'	=>	'dropdown',
					'label'	=>	'Penggolongan Penyedia Jasa (Usulan)',
					'source'=>	array(0 => 'Pilih Dibawah Ini', 'perseorangan' => 'Perseorangan', 'usaha_kecil' => 'Usaha Kecil(K)', 'usaha_menengah' => 'Usaha Menengah(M)', 'usaha_besar' => 'Usaha Besar(B)')
				),
				array(
					'field'	=> 	'jwpp',
					'type'	=>	'dateperiod',
					'label'	=>	'Jangka Waktu Penyelesaian Pekerjaan ("JWPP")'
				),array(
					'field'	=> 	'jwp',
					'type'	=>	'dateperiod',
					'label'	=>	'Masa Pemeliharaan dan/atau Durasi Laporan',
					'required' => 'required|mustBiggerThan' 
				),array(
					'field'	=> 	'desc_metode_pembayaran',
					'type'	=>	'textarea',
					'label'	=>	'Metode Pembayaran (Usulan)',
				),array(
					'field'	=> 	'jenis_kontrak',
					'type'	=>	'dropdown',
					'label'	=>	'Jenis Kontrak (Usulan)',
					'source'=>	array(	''	 	=> 'Pilih Dibawah Ini',
										'po' 	=> 'Purchase Order (PO)',
										'GTC01' => 'GTC01 - Kontrak Jasa Konstruksi non EPC',
										'GTC02' => 'GTC02 - Kontrak Jasa Konsultan',
										'GTC03' => 'GTC03 - Kontrak Jasa Umum',
										'GTC04' => 'GTC04 - Kontrak Jasa Pemeliharaan',
										'GTC05' => 'GTC05 - Kontrak Jasa Pembuatan Software',
										'GTC06' => 'GTC06 - Kontrak Jasa Sewa Fasilitas dan Alat',
										'GTC07' => 'GTC07 - Kontrak Jasa Tenaga Kerja.',
										'spk'	=> 'SPK'
									)
				),array(
					'field'	=> 	'sistem_kontrak',
					'type'	=>	'dropdown',
					'label'	=>	'Sistem Kontrak (Usulan)',
					'source'=>	array(	''	 			=> 'Pilih Dibawah Ini',
										'lumpsum' 		=> 'Perikatan Harga - Lumpsum',
										'unit_price'	=> 'Perikatan Harga - Unit Price',
										'modified' 		=> 'Perikatan Harga - Modified (lumpsum + unit price)',
										'outline' 		=> 'Perikatan Harga - Outline Agreement',
										'turn_key' 		=> 'Delivery - Turn Key',
										'sharing' 		=> 'Delivery - Sharing Contract',
										'success_fee' 	=> 'Delivery - Success Fee',
										'stockless' 	=> 'Delivery - Stockless Purchasing',
										'on_call' 		=> 'Delivery - On Call Basic',
									)
				)
			)
		);

		$modelAlias = $this->modelAlias;
        $getData   = $this->$modelAlias->selectData($id);
		// print_r($getData);
        foreach($this->form['form'] as $key => $value){
			$this->form['form'][$key]['readonly'] = TRUE;
			$getData[$value['field']] = ($getData[$value['field']]) ? $getData[$value['field']] : "-" ;
            $this->form['form'][$key]['value'] = $getData[$value['field']];
           
            if($value['type']=='date_range'){
                foreach($value['field'] as $keyField =>$rowField){
                    $this->form['form'][$key]['value'][] = $getData[$rowField];
                }
            }
            if($value['type']=='dateperiod'){
				$dateperiod = json_decode($getData[$value['field']]);
				$this->form['form'][$key]['value'] = date('d M Y', strtotime($dateperiod->start))." sampai ".date('d M Y', strtotime($dateperiod->end));
            }
            if($value['type']=='money'){
                    $this->form['form'][$key]['value'] = number_format($getData[$value['field']]);
            }
            if($value['type']=='currency'){
                    $this->form['form'][$key]['value'] = number_format($getData[$value['field']],2);
            }
            if($value['type']=='money_asing'){
                $this->form['form'][$key]['value'][] = $getData[$value['field'][0]];
                $this->form['form'][$key]['value'][] = number_format($getData[$value['field'][1]]);
            }
        }

        echo json_encode($this->form);
	}

	public function form($action,$id = "")
	{
		$data['action'] = $action;
		$data['admin'] = $this->session->userdata('admin');
		$data['id'] = $id;
		$this->breadcrumb->addlevel(1, array(
			'url' => site_url('perencanaan/rekap'),
			'title' => 'Tutup Perencanaan'
		));
		$this->header = 'Tutup Perencanaan';
		$this->content = $this->load->view('perencanaan/rekap/form',$data, TRUE);
		$this->script = $this->load->view('perencanaan/rekap/form_js', $data, TRUE);
		parent::index();
	}
}
