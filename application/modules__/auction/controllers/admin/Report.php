<?php
class Report extends CI_Controller{
	
	function __construct(){
		parent::__construct();

		require_once(BASEPATH."plugins/dompdf/dompdf_config.inc.php");  
		$this->load->model('auction_progress/auction_report_model');
	}
	
	function index($id_lelang =  '', $id_vendor = ''){
		$fill = $this->auction_report_model->get_header($id_lelang);
		
		$barang = $this->auction_report_model->get_barang($id_lelang);
		foreach($barang->result() as $data)
			$_barang .= '<li>'.$data->name.'</li>'; 
		$barang = $_barang;
		
		$peserta = $this->auction_report_model->get_peserta($id_lelang);
		foreach($peserta->result() as $data)
			$_peserta .= '<li>'.$data->nama.'</li>'; 
		$peserta = $_peserta;
		
		$pengguna = $this->auction_report_model->get_pengguna($id_lelang);
		foreach($pengguna->result() as $data)
			$_pengguna .= '<li>'.$data->name.'</li>'; 
		$pengguna = $_pengguna;
		
		$kurs = $this->auction_report_model->get_kurs($id_lelang);
		foreach($kurs->result() as $data)
			$_kurs .= '<li>'.$data->name.'</li>'; 
		$kurs = $_kurs;
		
		if(!$id_vendor){
			$_barang = $this->auction_report_model->get_barang($id_lelang);
			$return = '<table cellspacing="0" cellpadding="3" border="1" width="100%">
							<tr>
								<td colspan="5" bgcolor="#eee" align="center"><b>Hasil e-Auction</b></td>
							</tr>
							<tr>
								<td bgcolor="#eee" align="center" rowspan="2"><b>Nama Barang/Jasa</b></td>
								<td bgcolor="#eee" align="center" rowspan="2" colspan="2"><b>Peringkat</b></td>
								<td bgcolor="#eee" align="center" colspan="2"><b>Penawaran Terakhir</b></td>
							</tr>
							<tr>
								<td bgcolor="#eee" align="center">Dalam Kurs Asing</td>
								<td bgcolor="#eee" align="center">Dalam Rupiah</td>
							</tr>';
			
			foreach($_barang->result() as $data){
				$_peserta = $this->auction_report_model->get_vendor_ranking($id_lelang, $data->id, $fill['type_lelang']);
				
				$is_first = true;
				$index = 1;
				foreach($_peserta->result() as $_data){
					$penawaran = $this->auction_report_model->get_penawaran($_data->id_penawaran);
					$nilai = $in_rate = " - ";
					
					if($penawaran['nilai'] > 0){
						if($penawaran['id_kurs'] == 1)
							$in_rate = number_format($penawaran['in_rate']);	
						else{
							$nilai = $penawaran['symbol']." ".number_format($penawaran['nilai']);
							$in_rate = number_format($penawaran['in_rate']);
						}
					}
					
					$return .= '<tr>';
						if($is_first) $return .= '<td width="40%" rowspan="'.$_peserta->num_rows().'">'.$data->name.'</td>';
						$return .= '<td width="5%" align="center">'.$index.'</td>';
						$return .= '<td>'.$_data->nama_vendor.'</td>';
						$return .= '<td>'.$nilai.'</td>';
						$return .= '<td>'.$in_rate.'</td>';
					$return .= '</tr>';
					$is_first = false;
					$index++;
				}
			}
		
			$return .= '</table>';
			$hasil = $return;
		}
		/* history */
		
		$history = $this->auction_report_model->get_history($id_lelang, $id_vendor);
		$return = '<table cellpadding="3" cellspacing="0" width="100%" border="1">
						<thead>
							<tr>
								<th colspan="6" bgcolor="#eee" align="center"><b>History e-Auction</b></th>
							</tr>
							<tr>
								<th bgcolor="#eee" align="center" rowspan="2"><b>Waktu</b></th>
								<th bgcolor="#eee" align="center" rowspan="2"><b>Nama Barang/Jasa</b></th>
								<th bgcolor="#eee" align="center" rowspan="2"><b>Nama Penyedia Barang/Jasa</b></th>
								<th bgcolor="#eee" align="center" rowspan="2"><b>Penawaran ke-</b></th>
								<th bgcolor="#eee" align="center" colspan="2"><b>Penawaran</b></th>
							</tr>
							<tr>
								<th bgcolor="#eee" align="center">Dalam Kurs Asing</td>
								<th bgcolor="#eee" align="center">Dalam Rupiah</td>
							</tr>
						</thead>';
		
		$index = array();
		
			foreach($history->result() as $data){
				if(!$index[$data->id_vendor][$data->id_barang]) $index[$data->id_vendor][$data->id_barang] = 1;
				$nilai = $in_rate = " - ";
				
				if($data->nilai > 0){
					if($data->id_kurs == 1)
						$in_rate = number_format($data->in_rate);	
					else{
						$nilai = $data->symbol." ".number_format($data->nilai);
						$in_rate = number_format($data->in_rate);
					}
				}
				
				$return .= '<tr>';
					$return .= '<td><div class="history-page-break">'.date("d M Y, H:i:s", strtotime($data->entry_stamp)).'</div></td>';
					$return .= '<td>'.$data->nama_barang.'</td>';
					$return .= '<td>'.$data->nama_vendor.'</td>';
					$return .= '<td align="center">'.$index[$data->id_vendor][$data->id_barang].'</td>';
					$return .= '<td>'.$nilai.'</td>';
					$return .= '<td>'.$in_rate.'</td>';
				$return .= '</tr>';
				
				$index[$data->id_vendor][$data->id_barang]++;
			}
		$return .= '</table>';
		$history = $return;
		
		if($fill['metode_penawaran'] == 'harga_satuan') $metode_penawaran = 'Harga Satuan';
		else $metode_penawaran = 'Lump Sum';
			
		$return = '
			<html>
				<head>
					<style type="text/css">
						@page{
							size: A4 portrait;
							page-break-after : always;
							margin : 10px;
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
    				</style>
				</head>
				<body>
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
							<td>
								
								<table cellspacing="0" cellpadding="3" border="1" width="100%">
									<tr>
										<td colspan="2" align="center" bgcolor="#eee">
											<b>Data Paket Auction </b>
										</td>
									</tr>
									<tr>
										<td width="30%" valign="top">Nama paket</td>
										<td valign="top">'.$fill['nama'].'</td>
									</tr>
									<tr>
										<td valign="top">Nama Barang/Jasa</td>
										<td valign="top">
											<ol>
												'.$barang.'
											</ol>
										</td>
									</tr>
									<tr>
										<td valign="top">Sumber Dana</td>
										<td valign="top">'.$fill['anggaran'].'</td>
									</tr>';
									
									if(!$id_vendor)
										$return .= 
										'<tr>
											<td valign="top">Pengguna Barang/Jasa</td>
											<td valign="top">
												<ol>
													'.$pengguna.'
												</ol>
											</td>
										</tr>';
									
									$return .= '<tr>
										<td valign="top">Pejabat Pengadaan</td>
										<td valign="top">'.$fill['nama_pejabat'].'</td>
									</tr>
								</table>
								
							</td>
						</tr>
						<tr>
							<td height="30"></td>
						</tr>
						<tr>
							<td>
								
								<table cellspacing="0" cellpadding="3" border="1" width="100%">
									<tr>
										<td colspan="2" align="center" bgcolor="#eee">
											<b>Data e-Auction</b>
										</td>
									</tr>
									<tr>
										<td width="30%" valign="top">
											Waktu Mulai s.d Selesai
											<div><i>(tanggal &amp; jam)</i></div>
										</td>
										<td valign="top">'.date("d M Y, H:i:s", strtotime($fill['start_time'])).' - '.date("d M Y, H:i:s", strtotime($fill['time_limit'])).'</td>
									</tr>
									<tr>
										<td valign="top">
											Lokasi 
											<div><i>(satuan kerja-inside bidding room/outside bidding room)</i></div>
										</td>
										<td valign="top">
											'.$fill['nama_lokasi'].' - '.$fill['lokasi_bidding'].'
										</td>
									</tr>
									<tr>
										<td valign="top">Mata uang</td>
										<td valign="top"><ol>'.$kurs.'</ol></td>
									</tr>
									<tr>
										<td valign="top">
											Metode Penawaran 
										</td>
										<td valign="top">'.$metode_penawaran.'</td>
									</tr>
									<tr>
										<td valign="top">
											Peserta e-Auction 
											<div><i>(Nama Perusahaan)</i></div>
										</td>
										<td valign="top">
											<ol>
												'.$peserta.'
											</ol>
										</td>
									</tr>
								</table>
								
							</td>
						</tr>
						<tr>
							<td height="30"></td>
						</tr>
						<tr>
							<td>'.$hasil.'</td>
						</tr>
						<tr>
							<td height="30"></td>
						</tr>
						<tr>
							<td><div id="history-container">'.$history.'</div></td>
						</tr>
					</table>
				</body>
			<html>';
		
		echo $return;
		
		/*
		$dompdf = new DOMPDF();  
	    $dompdf->load_html($return);  
	    $dompdf->set_paper('A4','portaroid');
	    $dompdf->render();
	
									
        $dompdf->stream("sertifikat-vendor.pdf",array('Attachment' => false));
        */
	}
}