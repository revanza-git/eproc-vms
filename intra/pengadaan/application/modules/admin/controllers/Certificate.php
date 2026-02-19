<?php
class certificate extends CI_Controller{
	// protected $id_user;
	
	function __construct()
	{	
		
		ini_set("memory_limit","2048M");
		parent::__construct();
		
		require_once(BASEPATH."plugins/dompdf2/dompdf_config.inc.php");

        // $this->load->helper('exportpdf_helper'); 
		
		$this->load->model('Vendor_model','vm');
		$this->load->model('izin/izin_model','im');
		$this->load->model('k3/K3_model','km');
		
		$this->load->library('utility');
		//$this->output->enable_profiler(TRUE);
	}
	
	function index($id = ""){
		$this->dpt($id);
	}
	function change_no(){
		$this->load->driver('cache', array('adapter' => 'file', 'backup' => 'file'));
		if($this->vm->change_no($this->input->get('id'),$this->input->get('no'))){
			$this->cache->clean();
			echo 1;
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
		}else{
			echo 0;
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal mengubah data!</p>');
		}
	}

	function dpt($id = ""){
		$data['vendor']			= $this->vm->get_data($id,TRUE);
		// print_r($data['vendor']);/
		$data['administrasi']	= $this->vm->get_administrasi_list($id,TRUE);
		$data['surat_izin']		= $this->im->get_izin_report($id,TRUE);
		$data['pengalaman']		= $this->im->get_pengalaman_report($id,TRUE);
		// $data['klasifikasi']	= array(1=>'non-Konstruksi', 2=>'non-Konstruksi',3=>'non-Konstruksi',4=>'Konstruksi',5=>'Konstruksi');
		
		
		$data['get_csms'] 		= $this->km->get_csms($id);
		$data['evaluasi'] 		= $this->km->get_evaluasi();
		$data['evaluasi_list'] 	= $this->km->get_evaluasi_list();
		$data['data_k3']		= $this->km->get_k3_data($id);
		$data['ms_quest']		= $this->km->get_master_header();
		$data['csms_limit']		= $this->km->get_csms_limit($id);
		$data['data_poin']		= $this->km->get_poin($id);
		$k3_all_data = $this->km->get_k3_all_data($id);
		$data['csms_file'] = isset($k3_all_data['csms_file']) ? $k3_all_data['csms_file'] : array();
		
		$csms_file_id = !empty($data['csms_file']) && isset($data['csms_file']['id']) ? $data['csms_file']['id'] : 0;
		$data['value_k3'] = $this->km->get_penilaian_value($id, $csms_file_id);

		///print_r($data);die;
		$first_date = strtotime($data['vendor']['first_date']);
		$logo	= base_url('assets/images/login-regas-logo.jpg');
		// $nomor	= $this->session->userdata('nomor');;
		#Pengurus Vendor
		foreach ($data['administrasi'] as $key => $value)
			$pic_name = $value['pic_name']; $pic_jabatan = $value['pos'];


		#Surat Izin
		$izin_usaha = "";
		foreach ($data['surat_izin'] as $jenis => $valueTotal) {

			$jenis = ($jenis=='siup') ? "SIUP" : (($jenis=='asosiasi' ? "Asosiasi" : ($jenis=='ijin_lain' ? "Izin Lain" : "&nbsp;"))) ;

			$izin_usaha		.= '<table cellpadding="2" cellspacing="0" width="95%" border="1" class="std-table">';
			$izin_usaha		.= 		'<tr>';
			$izin_usaha		.=			'<th style="background : #c0392b; color : #eee" height="20" colspan="5">'.$jenis.'</th>';
			$izin_usaha		.=		'</tr>';

			$izin_usaha		.=		'<tr>';
			$izin_usaha		.=			'<th style="background : #e74c3c; color : #eee"; height="20";>No.</th>';
			$izin_usaha		.=			'<th style="background : #e74c3c; color : #eee"; height="20";>Tanggal</th>';
			$izin_usaha		.=			'<th style="background : #e74c3c; color : #eee"; height="20";>Kualifikasi</th>';
			$izin_usaha		.=			'<th style="background : #e74c3c; color : #eee"; height="20";>Bidang/Sub-Bidang</th>';
			$izin_usaha		.=			'<th style="background : #e74c3c; color : #eee"; height="20";>Masa Berlaku</th>';
			$izin_usaha		.=		'</tr>';

				#DATA
				foreach ($valueTotal as $value) {
					$izin_usaha		.=		'<tr>';
					$izin_usaha		.=			'<td>'.$value['no'].'</td>';
					$izin_usaha		.=			'<td>'.default_date(date("d-m-Y", strtotime($value['issue_date']))).'</td>';
					$izin_usaha		.=			'<td>'.$value['qualification'].'</td>';
					$izin_usaha		.=			'<td>';
					$izin_usaha		.=				'<ul>';

					#Bidang
					if (!empty($value['bsb']) && is_array($value['bsb'])) {
						foreach ($value['bsb'] as $bsb => $bidang) {
							$izin_usaha		.=			'<li>'.$bsb;
							$izin_usaha		.=				'<ol>';
							$izin_usaha		.=					'<li>';

							#Sub Bidang
							foreach ($bidang as $sb => $sub_bidang) {
								$izin_usaha		.=					'<p>'.$sub_bidang.'</p>';
							}

							$izin_usaha		.=					'</li>';
							$izin_usaha		.=				'</ol>';
							$izin_usaha		.=			'</li>';
						}
					}


					$izin_usaha		.=				'</ul>';
					$izin_usaha		.=			'</td>';
					$expire 		 = ($value['expire_date']=="lifetime") ? "Seumur Hidup" : default_date($value['expire_date']);
					$izin_usaha		.=			'<td>'.$expire.'</td>';
					$izin_usaha		.=		'</tr>';
				}
			$izin_usaha		.='</table>';
		}

		// echo $izin_usaha;
		


		#--------------------------------------------------------------------------------------------------
		#--------------------------------------------------------------------------------------------------
		#												CSMS K3
		#--------------------------------------------------------------------------------------------------
		#--------------------------------------------------------------------------------------------------

		#Rekap CSMS k3
		$nomor_rekap = 1;
		$total = 0;

		$rekap_k3 	= '<table class="table-separator" cellpadding=0 cellspacing=0 style=" border-collapse: collapse;">
							<tr>
								<th></th>
								<th colspan=2>POKOK BAHASAN</th>
								<th>NILAI</th>
							</tr>
					';
		foreach($data['evaluasi'] as $key_ms => $value_ms){
			if(isset($value_ms)){
				$rekap_k3		.= '<tr>';
				$rekap_k3		.=		'<td style="text-align:center; vertical-align:top;">&nbsp;'.$nomor_rekap.'&nbsp;</td>';
				$rekap_k3		.=		'<td colspan=2>'.$data['ms_quest'][$key_ms].'</td>';

				$subtotal 	= 0;
				$total_data = count($value_ms);
				foreach($value_ms as $key_ev => $val_ev){
					$subtotal += isset($data['value_k3'][$key_ev]) ? $data['value_k3'][$key_ev] : NULL;
				}
				$total_sub = round($subtotal / $total_data,2);
				$total +=$total_sub;
				$rekap_k3		.= '	<td class="nilai"><i>'.$total_sub.'</i></td>';
				$rekap_k3		.= '</tr>';
			}

			$nomor_rekap++;
		}
				$rekap_k3		.= '<tr>';
				$rekap_k3		.= 		'<td style="border:none;"></td>';
				$rekap_k3		.= 		'<td style="border:none;" width="300px"></td>';
				$rekap_k3		.= 		'<td style="text-align:center;">TOTAL</td>';
				$rekap_k3		.= 		'<td class="nilai"><b><i>'.$total.'</i></b></td>';
				$rekap_k3		.= '</tr>';
		$rekap_k3	.= '</table>';

		#CSMS Detail
		// $total = 0;
		$csms_k3 = '';
		$csms_k3		.= '<div class="panel-group">
								<table class="scoreTable" cellpadding="0" cellspacing="0" style="margin: 0 auto; border-collapse: collapse;">
									<thead>
										<tr>
											<td>
											</td>
											<td style="width: 5%; max-width: 10%;">
												A
											</td>
											<td style="width: 5%; max-width: 10%;">
												B
											</td>
											<td style="width: 5%; max-width: 10%;">
												C
											</td>
											<td style="width: 5%; max-width: 10%;">
												D
											</td>
											<td style="width: 5%; max-width: 10%;">
												Subtotal
											</td>
											<td style="width: 5%; max-width: 10%;">
												Faktor
											</td>
											<td style="width: 5%; max-width: 10%;">
												Total
											</td>
										</tr>
									</thead>
									<tbody>';

		foreach($data['evaluasi'] as $key_ms => $value_ms){
			if(isset($value_ms)){
				$csms_k3		.= '<tr class="doubleBorder">';
				$csms_k3		.=	'<td colspan=2><b>Bagian '.$key_ms.' -  '.$data['ms_quest'][$key_ms].'</b></td>';
				$csms_k3		.=	'<td colspan="6"></td>';
				$csms_k3		.= '</tr>';

				$subtotal 	= 0;
				$total_data = count($value_ms);
				foreach($value_ms as $key_ev => $val_ev){
					$csms_k3	.= '<tr class="evalQuest" style="page-break-inside: avoid;">';
					$csms_k3	.=	'<td class="textQuestLv1">'.$data['evaluasi_list'][$key_ev]['name'].'</td>';
					$csms_k3	.=	'<b>'.$this->utility->generate_checked_k3($key_ev,$data['evaluasi_list'],isset($data['value_k3'][$key_ev]) ? $data['value_k3'][$key_ev] : NULL).'</b>';
					$csms_k3	.= '</tr>';

					$subtotal += isset($data['value_k3'][$key_ev]) ? $data['value_k3'][$key_ev] : NULL;
				}
				$total_sub = round($subtotal / $total_data,2);
				$total +=$total_sub;
				$csms_k3	.= '	<tr class="evalQuest subTotalQuest">
										<td colspan="5">
											Subtotal
										</td>
										<td>
											'.$subtotal.'
										</td>
										<td>
											X '.(($total_data==1) ? 1 :'1/'.$total_data).'
										</td>
										<td>
											'.$total_sub.'
										</td>
									</tr>';
			}
		}
				$csms_k3	.= '<tr class="">
									<td colspan=7>TOTAL</td>
									<td><b>'.$total.'</b></td>
								</tr>';
		$csms_k3			.= '</tbody>
							</table>';
		
		// echo $csms_k3;

		#print K3 DATA
		$k3_data="";
		if (isset($data['get_csms']['value']) && $data['get_csms']['value'] != "") {
			/*$k3_data = '<div class="page second-page" style="margin: 0 auto; page-break-inside: avoid;">
							<div class="rekapContainer">
								<h2 align=center>REKAP NILAI HASIL PRAKUALIFIKASI DOKUMEN CSMS</h2>
								<p class="nomorSertifikat">No. '.$this->session->userdata('nomorCSMS').'</p>
								<table class="info">
									<tr>
										<td style="border:none;">Nama Perusahaan</td>
										<td style="border:none;">: '.$data['vendor']['legal_name'].' <span>'.$data['vendor']['name'].'</td>
									</tr>
									<tr>
										<td style="border:none;">Alamat</td>
										<td style="border:none;">: '.$data['vendor']['vendor_address'].'</td>
									</tr>
								</table>
								'.$rekap_k3.'
								<p class="certificateClass">
									Bedasarkan hasil penilaian sementara, maka perusahaan ini tergolong dalam peringkat:
									<span><i>'.$data['get_csms']['value'].'</i></span>
								</p>
								<div class="ttdContainer">
									<div class="ttdLeft">
										<p>Jakarta, '.default_date(date('d-m-Y')).',<br>
										Ketua Panitia Prakualifikasi CSMS<br>
										PT Nusantara Regas<br>
										<br><br><br><br><br>
										_____________________</p>
									</div>
								</div>
							</div>
						</div>
						<div class="page" style="margin: 0 auto;">
							<h2 align=center>HASIL PENILAIAN PRA KUALIFIKASI CSMS KONTRAKTOR(VERIFIKASI LAPANGAN)</h2>
							'.$csms_k3.'
						</div>';*/
		}

		$logo_path = "";
		$logo_candidates = array(
			"assets/images/login-regas-logo.png",
			"assets/images/login-regas-logo.jpg",
			"assets/images/login-regas-logo1.jpg",
		);
		foreach ($logo_candidates as $candidate) {
			$p = FCPATH . $candidate;
			if (is_file($p)) {
				$logo_path = $p;
				break;
			}
		}

		$dompdf = new DOMPDF();
		$dompdf->load_html('<html><head><style>@page{margin:0;}</style></head><body></body></html>');
		$dompdf->set_paper('A4','landscape');
		$dompdf->render();

		$canvas = $dompdf->get_canvas();
		Font_Metrics::init($canvas);

		$page_w = $canvas->get_width();
		$page_h = $canvas->get_height();

		$bg_path = FCPATH . "assets/images/pdf-bg.jpg";
		if (is_file($bg_path)) {
			$bg_src_w = 1140;
			$bg_src_h = 795;
			$bg_w = $page_h * ($bg_src_w / $bg_src_h);
			$bg_x = ($page_w - $bg_w) / 2;
			$canvas->image($bg_path, $bg_x, 0, $bg_w, $page_h);
		} else {
			$canvas->filled_rectangle(0, 0, $page_w, $page_h, array(1, 1, 1));
		}

		$color_dark = array(0.10, 0.10, 0.10);
		$color_muted = array(0.35, 0.35, 0.35);
		$color_blue = array(0.01, 0.39, 0.67);

		$font_sans = Font_Metrics::get_font("helvetica", "normal");
		$font_sans_bold = Font_Metrics::get_font("helvetica", "bold");

		$cert_no = isset($data['vendor']['certificate_no']) ? trim(strip_tags($data['vendor']['certificate_no'])) : "";
		$vendor_name = strtoupper(trim(strip_tags((isset($data['vendor']['legal_name']) ? $data['vendor']['legal_name'] : "")." ".(isset($data['vendor']['name']) ? $data['vendor']['name'] : ""))));
		$npwp = isset($data['vendor']['npwp_code']) ? trim(strip_tags($data['vendor']['npwp_code'])) : "";
		$addr = isset($data['vendor']['vendor_address']) ? trim(strip_tags($data['vendor']['vendor_address'])) : "";
		$phone = isset($data['vendor']['vendor_phone']) ? trim(strip_tags($data['vendor']['vendor_phone'])) : "";

		if (!empty($logo_path) && is_file($logo_path)) {
			$logo_size = @getimagesize($logo_path);
			$logo_src_w = isset($logo_size[0]) ? (float)$logo_size[0] : 1023.0;
			$logo_src_h = isset($logo_size[1]) ? (float)$logo_size[1] : 257.0;
			$logo_w = 190;
			$logo_h = $logo_w * ($logo_src_h / $logo_src_w);
			$canvas->image($logo_path, ($page_w - $logo_w) / 2, 48, $logo_w, $logo_h);
		}

		$title = "Sertifikat";
		$title_size = 30;
		$title_w = Font_Metrics::get_text_width($title, $font_sans_bold, $title_size);
		$canvas->text(($page_w - $title_w) / 2, 135, $title, $font_sans_bold, $title_size, $color_dark);

		$subtitle = "Penyedia Barang / Jasa Terdaftar";
		$subtitle_size = 13;
		$subtitle_w = Font_Metrics::get_text_width($subtitle, $font_sans, $subtitle_size);
		$canvas->text(($page_w - $subtitle_w) / 2, 170, $subtitle, $font_sans, $subtitle_size, $color_muted);

		$cert_size = 9;
		$cert_w = Font_Metrics::get_text_width($cert_no, $font_sans_bold, $cert_size);
		$canvas->text(($page_w - $cert_w) / 2, 200, $cert_no, $font_sans_bold, $cert_size, $color_muted);

		$vendor_size = 16;
		$vendor_lines = explode("\n", wordwrap($vendor_name, 36, "\n", true));
		$vendor_y = 250;
		foreach ($vendor_lines as $line) {
			$line_w = Font_Metrics::get_text_width($line, $font_sans_bold, $vendor_size);
			$canvas->text(($page_w - $line_w) / 2, $vendor_y, $line, $font_sans_bold, $vendor_size, $color_dark);
			$vendor_y += 22;
		}

		$label_x = 290;
		$sep_x = 355;
		$value_x = 375;
		$row_y = 325;
		$row_h = 22;

		$label_size = 9;
		$value_size = 9;

		$canvas->text($label_x, $row_y, "NPWP", $font_sans, $label_size, $color_muted);
		$canvas->text($sep_x, $row_y, ":", $font_sans, $label_size, $color_muted);
		$canvas->text($value_x, $row_y, $npwp, $font_sans, $value_size, $color_dark);
		$row_y += $row_h;

		$canvas->text($label_x, $row_y, "Alamat", $font_sans, $label_size, $color_muted);
		$canvas->text($sep_x, $row_y, ":", $font_sans, $label_size, $color_muted);
		$addr_lines = explode("\n", wordwrap($addr, 55, "\n", true));
		$addr_first = true;
		foreach ($addr_lines as $line) {
			$canvas->text($value_x, $row_y, ($addr_first ? $line : "  ".$line), $font_sans, 8.5, $color_dark);
			$row_y += 14;
			$addr_first = false;
		}
		$row_y += 6;

		$canvas->text($label_x, $row_y, "No. Telp", $font_sans, $label_size, $color_muted);
		$canvas->text($sep_x, $row_y, ":", $font_sans, $label_size, $color_muted);
		$canvas->text($value_x, $row_y, $phone, $font_sans, $value_size, $color_dark);

		$printed_at = "Dicetak pada tanggal : ".date("d/m/Y, H:i:s");
		$canvas->text(60, $page_h - 58, $printed_at, $font_sans, 7.5, $color_muted);

		$footer_1 = "Dicetak dengan sistem aplikasi kelogistikan PT Nusantara Regas.";
		$footer_2 = "Dokumen ini resmi tanpa stempel dan/atau tanda tangan pejabat.";
		$f2_w = Font_Metrics::get_text_width($footer_2, $font_sans, 7.5);
		$fx = $page_w - 60 - $f2_w;
		$canvas->text($fx, $page_h - 72, $footer_1, $font_sans, 7.5, $color_muted);
		$canvas->text($fx, $page_h - 58, $footer_2, $font_sans, 7.5, $color_muted);
									
		$dompdf->stream("sertifikat - ".$data['vendor']['name'].".pdf",array('Attachment' => 1));
		// $dompdf->output();
	}

	function dpt_($id = ""){
		$data['vendor']			= $this->vm->get_data($id,TRUE);
		// print_r($data['vendor']);/
		$data['administrasi']	= $this->vm->get_administrasi_list($id,TRUE);
		$data['surat_izin']		= $this->im->get_izin_report($id,TRUE);
		$data['pengalaman']		= $this->im->get_pengalaman_report($id,TRUE);
		// $data['klasifikasi']	= array(1=>'non-Konstruksi', 2=>'non-Konstruksi',3=>'non-Konstruksi',4=>'Konstruksi',5=>'Konstruksi');
		
		
		$data['get_csms'] 		= $this->km->get_csms($id);
		$data['evaluasi'] 		= $this->km->get_evaluasi();
		$data['evaluasi_list'] 	= $this->km->get_evaluasi_list();
		$data['data_k3']		= $this->km->get_k3_data($id);
		$data['ms_quest']		= $this->km->get_master_header();
		$data['csms_limit']		= $this->km->get_csms_limit($id);
		$data['data_poin']		= $this->km->get_poin($id);
		$k3_all_data = $this->km->get_k3_all_data($id);
		$data['csms_file'] = isset($k3_all_data['csms_file']) ? $k3_all_data['csms_file'] : array();
		
		$csms_file_id = !empty($data['csms_file']) && isset($data['csms_file']['id']) ? $data['csms_file']['id'] : 0;
		$data['value_k3'] = $this->km->get_penilaian_value($id, $csms_file_id);

		///print_r($data);die;
		$first_date = strtotime($data['vendor']['first_date']);
		$logo	= base_url('assets/images/login-regas-logo.jpg');
		// $nomor	= $this->session->userdata('nomor');;
		#Pengurus Vendor
		foreach ($data['administrasi'] as $key => $value)
			$pic_name = $value['pic_name']; $pic_jabatan = $value['pos'];


		#Surat Izin
		$izin_usaha = "";
		foreach ($data['surat_izin'] as $jenis => $valueTotal) {

			$jenis = ($jenis=='siup') ? "SIUP" : (($jenis=='asosiasi' ? "Asosiasi" : ($jenis=='ijin_lain' ? "Izin Lain" : "&nbsp;"))) ;

			$izin_usaha		.= '<table cellpadding="2" cellspacing="0" width="95%" border="1" class="std-table">';
			$izin_usaha		.= 		'<tr>';
			$izin_usaha		.=			'<th style="background : #c0392b; color : #eee" height="20" colspan="5">'.$jenis.'</th>';
			$izin_usaha		.=		'</tr>';

			$izin_usaha		.=		'<tr>';
			$izin_usaha		.=			'<th style="background : #e74c3c; color : #eee"; height="20";>No.</th>';
			$izin_usaha		.=			'<th style="background : #e74c3c; color : #eee"; height="20";>Tanggal</th>';
			$izin_usaha		.=			'<th style="background : #e74c3c; color : #eee"; height="20";>Kualifikasi</th>';
			$izin_usaha		.=			'<th style="background : #e74c3c; color : #eee"; height="20";>Bidang/Sub-Bidang</th>';
			$izin_usaha		.=			'<th style="background : #e74c3c; color : #eee"; height="20";>Masa Berlaku</th>';
			$izin_usaha		.=		'</tr>';

				#DATA
				foreach ($valueTotal as $value) {
					$izin_usaha		.=		'<tr>';
					$izin_usaha		.=			'<td>'.$value['no'].'</td>';
					$izin_usaha		.=			'<td>'.default_date(date("d-m-Y", strtotime($value['issue_date']))).'</td>';
					$izin_usaha		.=			'<td>'.$value['qualification'].'</td>';
					$izin_usaha		.=			'<td>';
					$izin_usaha		.=				'<ul>';

					#Bidang
					foreach ($value['bsb'] as $bsb => $bidang) {
						$izin_usaha		.=			'<li>'.$bsb;
						$izin_usaha		.=				'<ol>';
						$izin_usaha		.=					'<li>';

						#Sub Bidang
						foreach ($bidang as $sb => $sub_bidang) {
							$izin_usaha		.=					'<p>'.$sub_bidang.'</p>';
						}

						$izin_usaha		.=					'</li>';
						$izin_usaha		.=				'</ol>';
						$izin_usaha		.=			'</li>';
					}


					$izin_usaha		.=				'</ul>';
					$izin_usaha		.=			'</td>';
					$expire 		 = ($value['expire_date']=="lifetime") ? "Seumur Hidup" : default_date($value['expire_date']);
					$izin_usaha		.=			'<td>'.$expire.'</td>';
					$izin_usaha		.=		'</tr>';
				}
			$izin_usaha		.='</table>';
		}

		// echo $izin_usaha;
		


		#--------------------------------------------------------------------------------------------------
		#--------------------------------------------------------------------------------------------------
		#												CSMS K3
		#--------------------------------------------------------------------------------------------------
		#--------------------------------------------------------------------------------------------------

		#Rekap CSMS k3
		$nomor_rekap = 1;

		$rekap_k3 	= '<table class="table-separator" cellpadding=0 cellspacing=0 style=" border-collapse: collapse;">
							<tr>
								<th></th>
								<th colspan=2>POKOK BAHASAN</th>
								<th>NILAI</th>
							</tr>
					';
		foreach($data['evaluasi'] as $key_ms => $value_ms){
			if(isset($value_ms)){
				$rekap_k3		.= '<tr>';
				$rekap_k3		.=		'<td style="text-align:center; vertical-align:top;">&nbsp;'.$nomor_rekap.'&nbsp;</td>';
				$rekap_k3		.=		'<td colspan=2>'.$data['ms_quest'][$key_ms].'</td>';

				$subtotal 	= 0;
				$total_data = count($value_ms);
				foreach($value_ms as $key_ev => $val_ev){
					$subtotal += isset($data['value_k3'][$key_ev]) ? $data['value_k3'][$key_ev] : NULL;
				}
				$total_sub = round($subtotal / $total_data,2);
				$total +=$total_sub;
				$rekap_k3		.= '	<td class="nilai"><i>'.$total_sub.'</i></td>';
				$rekap_k3		.= '</tr>';
			}

			$nomor_rekap++;
		}
				$rekap_k3		.= '<tr>';
				$rekap_k3		.= 		'<td style="border:none;"></td>';
				$rekap_k3		.= 		'<td style="border:none;" width="300px"></td>';
				$rekap_k3		.= 		'<td style="text-align:center;">TOTAL</td>';
				$rekap_k3		.= 		'<td class="nilai"><b><i>'.$total.'</i></b></td>';
				$rekap_k3		.= '</tr>';
		$rekap_k3	.= '</table>';

		#CSMS Detail
		// $total = 0;
		$csms_k3		.= '<div class="panel-group">
								<table class="scoreTable" cellpadding="0" cellspacing="0" style="margin: 0 auto; border-collapse: collapse;">
									<thead>
										<tr>
											<td>
											</td>
											<td style="width: 5%; max-width: 10%;">
												A
											</td>
											<td style="width: 5%; max-width: 10%;">
												B
											</td>
											<td style="width: 5%; max-width: 10%;">
												C
											</td>
											<td style="width: 5%; max-width: 10%;">
												D
											</td>
											<td style="width: 5%; max-width: 10%;">
												Subtotal
											</td>
											<td style="width: 5%; max-width: 10%;">
												Faktor
											</td>
											<td style="width: 5%; max-width: 10%;">
												Total
											</td>
										</tr>
									</thead>
									<tbody>';

		foreach($data['evaluasi'] as $key_ms => $value_ms){
			if(isset($value_ms)){
				$csms_k3		.= '<tr class="doubleBorder">';
				$csms_k3		.=	'<td colspan=2><b>Bagian '.$key_ms.' -  '.$data['ms_quest'][$key_ms].'</b></td>';
				$csms_k3		.=	'<td colspan="6"></td>';
				$csms_k3		.= '</tr>';

				$subtotal 	= 0;
				$total_data = count($value_ms);
				foreach($value_ms as $key_ev => $val_ev){
					$csms_k3	.= '<tr class="evalQuest" style="page-break-inside: avoid;">';
					$csms_k3	.=	'<td class="textQuestLv1">'.$data['evaluasi_list'][$key_ev]['name'].'</td>';
					$csms_k3	.=	'<b>'.$this->utility->generate_checked_k3($key_ev,$data['evaluasi_list'],isset($data['value_k3'][$key_ev]) ? $data['value_k3'][$key_ev] : NULL).'</b>';
					$csms_k3	.= '</tr>';

					$subtotal += isset($data['value_k3'][$key_ev]) ? $data['value_k3'][$key_ev] : NULL;
				}
				$total_sub = round($subtotal / $total_data,2);
				$total +=$total_sub;
				$csms_k3	.= '	<tr class="evalQuest subTotalQuest">
										<td colspan="5">
											Subtotal
										</td>
										<td>
											'.$subtotal.'
										</td>
										<td>
											X '.(($total_data==1) ? 1 :'1/'.$total_data).'
										</td>
										<td>
											'.$total_sub.'
										</td>
									</tr>';
			}
		}
				$csms_k3	.= '<tr class="">
									<td colspan=7>TOTAL</td>
									<td><b>'.$total.'</b></td>
								</tr>';
		$csms_k3			.= '</tbody>
							</table>';
		
		// echo $csms_k3;

		#print K3 DATA
		$k3_data="";
		if (isset($data['get_csms']['value']) && $data['get_csms']['value'] != "") {
			/*$k3_data = '<div class="page second-page" style="margin: 0 auto; page-break-inside: avoid;">
							<div class="rekapContainer">
								<h2 align=center>REKAP NILAI HASIL PRAKUALIFIKASI DOKUMEN CSMS</h2>
								<p class="nomorSertifikat">No. '.$this->session->userdata('nomorCSMS').'</p>
								<table class="info">
									<tr>
										<td style="border:none;">Nama Perusahaan</td>
										<td style="border:none;">: '.$data['vendor']['legal_name'].' <span>'.$data['vendor']['name'].'</td>
									</tr>
									<tr>
										<td style="border:none;">Alamat</td>
										<td style="border:none;">: '.$data['vendor']['vendor_address'].'</td>
									</tr>
								</table>
								'.$rekap_k3.'
								<p class="certificateClass">
									Bedasarkan hasil penilaian sementara, maka perusahaan ini tergolong dalam peringkat:
									<span><i>'.$data['get_csms']['value'].'</i></span>
								</p>
								<div class="ttdContainer">
									<div class="ttdLeft">
										<p>Jakarta, '.default_date(date('d-m-Y')).',<br>
										Ketua Panitia Prakualifikasi CSMS<br>
										PT Nusantara Regas<br>
										<br><br><br><br><br>
										_____________________</p>
									</div>
								</div>
							</div>
						</div>
						<div class="page" style="margin: 0 auto;">
							<h2 align=center>HASIL PENILAIAN PRA KUALIFIKASI CSMS KONTRAKTOR(VERIFIKASI LAPANGAN)</h2>
							'.$csms_k3.'
						</div>';*/
		}

		// Initialize skor_csms variable
		$skor_csms = '';
		if(isset($data['data_poin']['score']) && $data['data_poin']['score'] > 0){
			$skor_csms		= '<tr>
									<td width=150px>Skor CSMS</td>
									<td>:&nbsp;&nbsp;&nbsp;'.$data['data_poin']['score'].'</td>
								</tr>';
		}
		#--------------------------------------------------------------------------------------------------
		#--------------------------------------------------------------------------------------------------

		$return =
			'<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<!--<link type="text/css" rel="stylesheet" href="'.base_url('assets/css/pdf.css').'">-->
					<style type="text/css">
						
						html, body{
							margin : 0px;
							padding : 0px;
							font-family : "Helvetica" !important;
							background-image : url('.base_url('assets/images/pdf-bg.jpg').'); 
							background-repeat : repeat-y;
							background-size: 1140px 795px;
							background-position: top left;

						 }
						 p{
							font-size: 12px;
						 }
						 div.page{
							margin : 0px;
							padding : 0px;
							font-size : 13px;
							/*height : 753px;*//*
							background-image : '.base_url('assets/images/report-kanan.jpg').'; */
							background-repeat : repeat;
						} 
						div.pageDPT{
							margin : 0px;
							padding : 0px;
							font-size : 13px;/*
							background-image : url(../images/report-kanan.jpg); */
							background-repeat : repeat;
						}
						div.page table{
							background: #fff;
						}
						div#first-page{
							position: relative;
							page-break-inside: avoid;
						}
						div#first-page .certificateWrap{
							width: 1140px;
							/*height : 783px;*/
							position: relative;
						}
						div#first-page .certificateWrap #logo{
							width: 223px;
							height: 73px;
							position: absolute;
							/*float: right;*/
							right: 0;
							margin: 30px 50px 0 0;
						}

						div#first-page .certificate{
							width: 640px;
							margin: 0 auto;
							padding: 205px 0 0 100px;
							/*margin: 255px auto 255px auto;*/
						}

						div#first-page .certificate .certificateTitle{
							margin-bottom: 50px;
							padding-left: 30px;
						}
						div#first-page .certificate .certificateTitle h1{
							font-weight: 400;
							padding: 0;
							margin: 0;
						}
						div#first-page .certificate .certificateTitle p{
							font-weight: bold;
							padding: 0;
							margin: 0;
							color: #0364ab;
						}

						div#first-page .certificate .certificateContent{

						}
						div#first-page .certificate .certificateContent h2{
							border-left: 13px #c9cc10 solid;
							font-size: 36px;
							text-transform: uppercase;
							padding: 10px 0 10px 35px;
							font-weight: 400;
						}
						div#first-page .certificate .certificateContent h2 span{
							text-transform: capitalize;
						}
						div#first-page .certificate .certificateContent ul{
							list-style: none;
						}
						div#first-page .certificate .certificateContent table{
							margin: 0 0 50px 50px;
						}
						div#first-page .certificate .certificateContent ul li{
							list-style: none;
						}
						div#first-page .certificate .certificateContent p{
							margin: 75px 0 0 50px;
							font-weight: 500;
							font-size: 16px;
							color: #e22839;
						}

						div#first-page .certificateFooter{
							width: 1090px;
							position: absolute;
							bottom: 10px;
							padding: 0 25px;
							height: 50px;
						}
						div#first-page .certificateFooter .txt{
							position: relative;
						}
						div#first-page .certificateFooter .txt #left{
							/*float: left;*/
							font-size: 10px;
							position: absolute;
							left: 0;
							color: #fff;
						}
						div#first-page .certificateFooter .txt #right{
							/*float: right;*/
							position: absolute;
							right: 0;
							color: #333;
						}



						div.second-page{
							/*background : url(../images/report-kanan.png);*/
							/*background-repeat : repeat;*/
							height : 753px;
							page-break-inside : auto;
							position: relative;
						}
						/*div#second-page:after {
							content : "";
							display: block;
							position: absolute;
							top: 0;
							left: 0;
							background : url(../images/report-kanan.png);
							background-repeat : repeat;
							width: 100%;
							height: 100%;
							opacity : 0.6;
							z-index: -1;
						}*/
						div.table-separator{
							page-break-inside : avoid;
						}
						table.std-table{
							border-collapse : collapse;
							border : 1px solid #000;
							margin : 15px;
						}
						table.std-table th{
							color : #fff;
							border : 1px solid #000;
						}
						table.std-table td{
							background : #fff;
						}

						.rekapContainer h2{
							margin: 15px 0 0 0;
						}
						.rekapContainer p{
							font-size: 13px;
						}
						.rekapContainer table.info{
							min-width: 500px;
							margin: 0 100px;
							font-size: 11px;
						}
						.rekapContainer table.info span{
							text-transform: capitalize;
						}
						.rekapContainer table.info td{
							min-width: 200px;
						}
						.rekapContainer table{
							margin: 15px auto;
						}
						.rekapContainer table th{
							padding: 5px;
							background: #c0392b;
							border: 1px #c0392b solid;
							color: #fff;
						}
						.rekapContainer table td{
							padding: 3px;
							font-size: 11px;
							border: 1px #c0392b solid;
						}
						.rekapContainer table td.nilai{
							text-align: right;
						}
						.nomorSertifikat{
							text-align: center;
							margin: 5px 0;
						}



						.scoreTable{
							width: 90%;
							/*padding: 0 5%;*/
							margin: 0 auto;
						}
						.scoreTable tr td{
							padding: 5px 10px;
						}
						.scoreTable .doubleBorder td{
							border: 1px solid #c0392b;
							background: #c0392b;
							color: #fff;
						}

						.borderQuest td{
							border: 1px solid #c0392b;
						}
						.borderQuest td.radioQuest{
							border-top: none;
							border-bottom: none;
						}
						.borderQuest:last-child td.radioQuest{
							border-top: none;
							border-bottom: 1px solid #c0392b;
						}
						.evalQuest td{
							border: 1px solid #c0392b;
						}
						.scoreTable tr td.textQuestLv2{
							padding-left: 60px;
							font-size: 12px;
						}
						.scoreTable tr td.textQuestLv1{
							padding-left: 40px;
							font-size: 14px;
							font-weight: bold;
							
						}
						.subTotalQuest{
							border: 1px solid #c0392b;
						}
						.totalAllQuest{
							background: #2c3e50;
							color: #fff;
						}

						.certificateClass{
							text-align: center;
							font-size: 12px;
						}
						.certificateClass span{
							text-align: center;
							font-weight: bold;
							text-decoration: underline;
							display: block;
						}

						.ttdContainer{
							width: 80%;
							font-size: 12px;
							margin: 20px auto;
						}
						.ttdContainer p{
							font-size: 13px;
						}
					</style>
				</head>
				<body>
					<div class="pageDPT" id="first-page">
						<div class="certificateWrap">
							<img style="float:right;" id="logo" src="'.$logo.'">
							<div class="certificate">
								<div class="certificateTitle">
									<h1>SERTIFIKAT PENYEDIA BARANG/JASA TERDAFTAR</h1>
									<p>'.$data['vendor']['certificate_no'].'</p>
								</div>
								
								<div class="certificateContent">
									<h2>'.$data['vendor']['legal_name'].' <span>'.$data['vendor']['name'].'</span></h2>
											<table>
												<tr>
													<td width=150px>NPWP</td>
													<td>:&nbsp;&nbsp;&nbsp;'.$data['vendor']['npwp_code'].'</td>
												</tr>
												<tr>
													<td width=150px>Alamat</td>
													<td>:&nbsp;&nbsp;&nbsp;'.nl2br($data['vendor']['vendor_address']).'</td>
												</tr>
												<tr>
													<td width=150px>No. Telp</td>
													<td>:&nbsp;&nbsp;&nbsp;'.$data['vendor']['vendor_phone'].'</td>
												</tr>
												'.$skor_csms.'
											</table>
									<p>Jakarta, '.default_date(date('d-m-Y',strtotime($data['vendor']['first_date']))).'</p>
								</div>
							</div>

							<div class="certificateFooter">
								<div class="txt">
									<p id="left">Dicetak pada tanggal : '.date("d/m/Y, H:i:s").'</p>
									<p id="right">
										Dicetak dengan Sistem Aplikasi Kelogistikan PT Nusantara Regas.<br>
										Dokumen ini resmi tanpa stempel dan/atau tanda tangan pejabat.
									</p>
								</div>
							</div>
						</div>
					</div>
				</body>
			</html>';

		 //echo $return;die;

		// var_dump(libxml_use_internal_errors(true));

		 $dompdf = new DOMPDF();  
		 $dompdf->load_html($return);  
		 $dompdf->set_paper('A4','landscape'); 
		 $dompdf->render();
									
		 $dompdf->stream("sertifikat - ".$data['vendor']['name'].".pdf",array('Attachment' => 1));
		// $dompdf->output();
	}








	function csms($id = ""){

		$data['vendor']			= $this->vm->get_data($id,TRUE);
		$data['administrasi']	= $this->vm->get_administrasi_list($id,TRUE);

		$data['get_csms'] 		= $this->km->get_csms($data['vendor']['id']);
		$data['evaluasi'] 		= $this->km->get_evaluasi();
		$data['evaluasi_list'] 	= $this->km->get_evaluasi_list();
		$data['data_k3']		= $this->km->get_k3_data($data['vendor']['id']);
		$data['ms_quest']		= $this->km->get_master_header();
		$data['csms_limit']		= $this->km->get_csms_limit($data['vendor']['id']);
		$data['data_poin']		= $this->km->get_poin($data['vendor']['id']);
		$k3_all_data = $this->km->get_k3_all_data($data['vendor']['id']);
		$data['csms_file'] = isset($k3_all_data['csms_file']) ? $k3_all_data['csms_file'] : array();
		
		$csms_file_id = !empty($data['csms_file']) && isset($data['csms_file']['id']) ? $data['csms_file']['id'] : 0;
		$data['value_k3'] = $this->km->get_penilaian_value($data['vendor']['id'], $csms_file_id);

		// print_r($data);die;

			$no_id = (6 - strlen($data['vendor']['id']));

			for($i=0;$i<$no_id;$i++) $nomor .= "0";
				$nomor .= $data['vendor']['id'];

			$nomor = $nomor."/NR/CSMS/".date("d/m/Y", $first_date);
		

		// $logo	= base_url('assets/images/login-regas-logo.jpg');
			$logo = 'https://eprocnr.pertmamina.com/internal/lampiran/login-regas-logo.jpg';

		#Pengurus Vendor
		foreach ($data['administrasi'] as $key => $value)
			$pic_name = $value['pic_name']; $pic_jabatan = $value['pos'];


		#--------------------------------------------------------------------------------------------------
		#--------------------------------------------------------------------------------------------------

		#REKAP CSMS
		$nomor_rekap = 1;

		$rekap_k3 	= '<table class="table-separator">
							<tr>
								<th></th>
								<th colspan=2>POKOK BAHASAN</th>
								<th>NILAI</th>
							</tr>
					';
		foreach($data['evaluasi'] as $key_ms => $value_ms){
			if(isset($value_ms)){
				$rekap_k3		.= '<tr>';
				$rekap_k3		.=		'<td style="text-align:center; vertical-align:top;">&nbsp;'.$nomor_rekap.'&nbsp;</td>';
				$rekap_k3		.=		'<td colspan=2>'.$data['ms_quest'][$key_ms].'</td>';

				$subtotal 	= 0;
				$total_data = count($value_ms);
				foreach($value_ms as $key_ev => $val_ev){
					$subtotal += isset($data['value_k3'][$key_ev]) ? $data['value_k3'][$key_ev] : NULL;
				}
				$total_sub = round($subtotal / $total_data,2);
				$total +=$total_sub;
				$rekap_k3		.= '	<td class="nilai"><i>'.$total_sub.'</i></td>';
				$rekap_k3		.= '</tr>';
			}

			$nomor_rekap++;
		}
				$rekap_k3		.= '<tr>';
				$rekap_k3		.= 		'<td style="border:none;"></td>';
				$rekap_k3		.= 		'<td style="border:none;" width="300px"></td>';
				$rekap_k3		.= 		'<td style="text-align:center;">TOTAL</td>';
				$rekap_k3		.= 		'<td class="nilai"><b><i>'.$total.'</i></b></td>';
				$rekap_k3		.= '</tr>';
		$rekap_k3	.= '</table>';

		#--------------------------------------------------------------------------------------------------
		#--------------------------------------------------------------------------------------------------

		#CSMS Detail
		$total = 0;
		$csms_k3		.= '<div class="panel-group">
								<table class="scoreTable" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
									<thead>
										<tr>
											<td>
											</td>
											<td style="width: 5%; max-width: 10%;">
												A
											</td>
											<td style="width: 5%; max-width: 10%;">
												B
											</td>
											<td style="width: 5%; max-width: 10%;">
												C
											</td>
											<td style="width: 5%; max-width: 10%;">
												D
											</td>
											<td style="width: 5%; max-width: 10%;">
												Subtotal
											</td>
											<td style="width: 5%; max-width: 10%;">
												Faktor
											</td>
											<td style="width: 5%; max-width: 10%;">
												Total
											</td>
										</tr>
									</thead>
									<tbody>';

		foreach($data['evaluasi'] as $key_ms => $value_ms){
			if(isset($value_ms)){
				$csms_k3		.= '<tr class="doubleBorder">';
				$csms_k3		.=	'<td colspan=2><b>Bagian '.$key_ms.' -  '.$data['ms_quest'][$key_ms].'</b></td>';
				$csms_k3		.=	'<td colspan="6"></td>';
				$csms_k3		.= '</tr>';

				$subtotal 	= 0;
				$total_data = count($value_ms);
				foreach($value_ms as $key_ev => $val_ev){
					$csms_k3	.= '<tr class="evalQuest" style="page-break-inside: avoid;">';
					$csms_k3	.=	'<td class="textQuestLv1">'.$data['evaluasi_list'][$key_ev]['name'].'</td>';
					$csms_k3	.=	'<b>'.$this->utility->generate_checked_k3($key_ev,$data['evaluasi_list'],isset($data['value_k3'][$key_ev]) ? $data['value_k3'][$key_ev] : NULL).'</b>';
					$csms_k3	.= '</tr>';

					$subtotal += isset($data['value_k3'][$key_ev]) ? $data['value_k3'][$key_ev] : NULL;
				}
				$total_sub = round($subtotal / $total_data,2);
				$total +=$total_sub;
				$csms_k3	.= '	<tr class="evalQuest subTotalQuest">
										<td colspan="5">
											Subtotal
										</td>
										<td>
											'.$subtotal.'
										</td>
										<td>
											X '.(($total_data==1) ? 1 :'1/'.$total_data).'
										</td>
										<td>
											'.$total_sub.'
										</td>
									</tr>';
			}
		}
				$csms_k3	.= '<tr class="">
									<td colspan=7>TOTAL</td>
									<td><b>'.$total.'</b></td>
								</tr>';
		$csms_k3			.= '</tbody>
							</table>';
		
		// echo $csms_k3;


		#--------------------------------------------------------------------------------------------------

		$return =
			'<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<title>'.$data['vendor']['name'].'</title>
					<link type="text/css" rel="stylesheet" href="'.base_url('assets/css/normalize.css').'">
					<link type="text/css" rel="stylesheet" href="'.base_url('assets/css/font-awesome.css').'">
					<link type="text/css" rel="stylesheet" href="'.base_url('assets/css/font-awesome-ie7.css').'">
					<link type="text/css" rel="stylesheet" href="'.base_url('assets/font/auction/flaticon.css').'">
					<link type="text/css" rel="stylesheet" href="'.base_url('assets/font/flaticon.css').'">
					<link type="text/css" rel="stylesheet" href="'.base_url('assets/css/pdf.css').'">
				</head>
				<body>
					<div class="" id="first-page">
						<div class="certificateWrap">
							<img style="float:right;" id="logo" src="'.$logo.'">
							<div class="certificate">
								<div class="certificateTitle">
									<h1>SERTIFIKAT CSMS</h1>
									<p> '.$this->session->userdata('nomorCSMS').'</p>
								</div>
								
								<div class="certificateContent">
									<h2>'.$data['vendor']['legal_name'].' <span>'.$data['vendor']['name'].'</span></h2>
											<table cellpadding="0" cellspacing="0" border="0">
												<tr>
													<td width="140px" valign="top">NPWP</td>
													<td width="10px" valign="top">:</td>
													<td width="350px" valign="top">'.$data['vendor']['npwp_code'].'</td>
												</tr>
												<tr>
													<td valign="top">Skor</td>
													<td valign="top">:</td>
													<td valign="top">'.nl2br($data['data_poin']['score']).'</td>
												</tr>
												<tr>
													<td valign="top">Kategori</td>
													<td valign="top">:</td>
													<td valign="top">'.$data['get_csms']['value'].'</td>
												</tr>
											</table>
									<p>Jakarta, '.default_date(date('d-m-Y',strtotime($data['vendor']['first_date']))).'</p>
								</div>
							</div>

							<div class="certificateFooter">
								<div class="txt">
									<p id="left">Dicetak pada tanggal : '.date("d/m/Y, H:i:s").'</p>
									<p id="right">
										Dicetak oleh Sistem Aplikasi Kelogistikan PT Nusantara Regas.<br>
										Dokumen ini resmi tanpa stempel dan/atau tanda tangan pejabat.
									</p>
								</div>
							</div>
						</div>
					</div>
					<div class="page second-page" style="margin: 0 auto;">
						<div class="rekapContainer">
							<h2 align=center>REKAP NILAI HASIL PRAKUALIFIKASI DOKUMEN CSMS</h2>
							<p class="nomorSertifikat">No. '.$this->session->userdata('nomorCSMS').'</p>
							<table class="info">
								<tr>
									<td style="border:none;">Nama Perusahaan</td>
									<td style="border:none;">: '.$data['vendor']['legal_name'].' <span>'.$data['vendor']['name'].'</td>
								</tr>
								<tr>
									<td style="border:none;">Alamat</td>
									<td style="border:none;">: '.$data['vendor']['vendor_address'].'</td>
								</tr>
							</table>
							'.$rekap_k3.'
							<p class="certificateClass">
								Bedasarkan hasil penilaian sementara, maka perusahaan ini tergolong dalam peringkat:
								<span><i>'.$data['get_csms']['value'].'</i></span>
							</p>
							<div class="ttdContainer">
								<div class="ttdLeft">
									<p>Jakarta, '.default_date(date('d-m-Y')).',<br>
									Ketua Panitia Prakualifikasi CSMS<br>
									PT Nusantara Regas<br>
									<br><br><br><br><br>
									_____________________</p>
								</div>
							</div>
						</div>
					</div>
					<div class="page" style="margin: 0 auto;">
						<h2 align=center>HASIL PENILAIAN PRA KUALIFIKASI CSMS KONTRAKTOR(VERIFIKASI LAPANGAN)</h2>
						'.$csms_k3.'
					</div>
				</body>
			</html>';

		echo $return;

		//$dompdf = new DOMPDF();
		//$dompdf->load_html($return);
		//$dompdf->set_paper('A4','landscape');
		//$dompdf->render();
									
		//$dompdf->stream("sertifikat_csms_".$data['vendor']['name'].".pdf",array('Attachment' => 1));

		
	}

}
