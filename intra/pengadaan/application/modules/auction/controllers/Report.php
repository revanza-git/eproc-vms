<?php
class Report extends CI_Controller{
	
	function __construct(){
		parent::__construct();

		// require_once(BASEPATH."plugins/dompdf/dompdf_config.inc.php");  
		$this->load->model('auction/auction_report_model');
	}
	
	function index( $type = '',$id_lelang =  '', $id_vendor = ''){

		$hasil = '';
		$fill = $this->auction_report_model->get_header($id_lelang);
		
		$_barang = $_peserta = $_pengguna = $_kurs = '';
		$barang = $this->auction_report_model->get_barang($id_lelang);
		foreach($barang->result() as $data)
			$_barang .= '<li>'.$data->nama_barang.'</li>'; 
		$barang = $_barang;
		
		$peserta = $this->auction_report_model->get_peserta($id_lelang);
		foreach($peserta->result() as $data)
			$_peserta .= '<li>'.$data->name.'</li>';
		$peserta = $_peserta;
		
		$pemenang_list = array();
		$tes_idr = '';
		$tes_kurs = '';
		// $pengguna = $this->auction_report_model->get_pengguna($id_lelang);
		// foreach($pengguna->result() as $data)
		// 	$_pengguna .= '<li>'.$data->name.'</li>'; 
		// $pengguna = $_pengguna;

		$pengguna = $this->auction_report_model->get_peserta($id_lelang);
		$nomornye = 1;
		foreach($pengguna->result() as $data){
			$_pengguna .= '<tr>';
			$_pengguna .= '<td align="center" width=5%>'.$nomornye.'.</td>';
			$_pengguna .= '<td>'.$data->legal.'.&nbsp;'.$data->name.'</td>';
			$_pengguna .= '<td></td>';
			$_pengguna .= '</tr>';
			$nomornye++;
		}

		$pengguna = $_pengguna;
		


		$kurs = $this->auction_report_model->get_kurs($id_lelang);
		foreach($kurs->result() as $data)
			$_kurs .= '<li>'.$data->name.'</li>'; 
		$kurs = $_kurs;



		#Hasil e-Auction
		if(!$id_vendor){
			$_barang = $this->auction_report_model->get_barang($id_lelang);
			$return = '<table cellspacing="0" cellpadding="3" border="1" width="100%" class="layout1">
							<tr>
								<td colspan="5" bgcolor="#eee" align="center"><b>Hasil e-Auction</b></td>
							</tr>
							<tr>
								<td bgcolor="#eee" align="center" rowspan="2"><b>Nama Barang/Jasa</b></td>
								<td bgcolor="#eee" align="center" rowspan="2" colspan="2"><b>Peringkat</b></td>
								<td bgcolor="#eee" align="center" colspan="2"><b>Penawaran Terakhir</b></td>
							</tr>
							<tr>
								<td bgcolor="#eee" align="center"><b>Dalam Kurs Asing</b></td>
								<td bgcolor="#eee" align="center"><b>Dalam Rupiah</b></td>
							</tr>';
			
			$peserta_index =0;
			foreach($_barang->result() as $data){
				$_peserta = $this->auction_report_model->get_vendor_ranking($id_lelang, $data->id, $fill['auction_type']);

				/*Set Pemenang List*/
				$peserta_arr		= $_peserta->result_array();

				$pemenang_list[$peserta_index] 	= array(
										'barang' 		=> $data->nama_barang,
										'pemenang'		=> $peserta_arr[0]['nama_vendor'],
									);



				$is_first = true;
				$index = 1;
				foreach($_peserta->result() as $_data){
					// print_r($_data);die;
					if($_data->id_penawaran!=NULL){
						// echo "Masuk Ke Tidak Null";die;
						$penawaran = $this->auction_report_model->get_penawaran($_data->id_penawaran);
						// print_r($penawaran);die;
						$nilai = $in_rate = " - ";
						
						if($penawaran['nilai'] > 0){
							if($penawaran['id_kurs'] == 1)
								$in_rate = number_format($penawaran['in_rate']);	
							else{
								$nilai = $penawaran['symbol']." ".number_format($penawaran['nilai']);
								$in_rate = number_format($penawaran['in_rate']);
							}
						}
						
						// echo($nilai);die;
						$row = $_peserta->num_rows();
						$return .= '<tr>';
						if($is_first){
							$return .= '<td width="40%" rowspan="'.$_peserta->num_rows().'">'.$data->nama_barang.'</td>';
							// if($type=='internal'){
							// 	$return .= '<td rowspan="'.$_peserta->num_rows().'">'.$data->symbol.' '.number_format($data->nilai_hps).'</td>';
							// }
						}
						// echo $in_rate;die;
							$return .= '<td width="5%" align="center">'.$index.'</td>';
							$return .= '<td>'.$_data->nama_vendor.'</td>';
							$return .= '<td>'.$nilai.'</td>';
							$return .= '<td>'.$in_rate.'</td>';
						$return .= '</tr>';
						$is_first = false;
						$index++;
					}else{
						// echo "Masuk Ke Null";die;
						// echo $in_rate;die;
						$return .= '<tr>';
						if($is_first){
							$return .= '<td width="40%" rowspan="'.$_peserta->num_rows().'">'.$data->nama_barang.'</td>';
							// if($type=='internal'){
							// 	$return .= '<td rowspan="'.$_peserta->num_rows().'">Rp. '.number_format($data->nilai_hps).'</td>';
							// }
						}
							$return .= '<td width="5%" align="center">'.$index.'</td>';
							$return .= '<td>'.$_data->nama_vendor.'</td>';
							$return .= '<td>'.$nilai.'</td>';
							$return .= '<td>'.$in_rate.'</td>';
						$return .= '</tr>';
						$is_first = false;
						$index++;
					}
					// echo $in_rate;die;
					$tes_idr .= $in_rate.'-';
					$tes_kurs .= $nilai.'-';
					$pemenang_list[$peserta_index]['nilai_kurs']	= $nilai;
					$pemenang_list[$peserta_index]['nilai_idr']		= $in_rate;
					// print_r($pemenang_list);die;	
				}

				$peserta_index++;
				// print_r($pemenang_list);die;
				
			}
		
			$return .= '</table>';
			$hasil = $return;
		}


		#------------------------------------
		#		PEMENANG
		#------------------------------------
		if($this->session->userdata('admin')['id_role']==6){
			$pemenang_auction	= '<table cellspacing="0" cellpadding="3" border="1" width="100%">
									<tr>
										<td colspan="5" bgcolor="#eee" align="center"><b>Pemenang e-Auction</b></td>
									</tr>
									<tr>
										<td width=5% bgcolor="#eee" align="center"><b>No.</b></td>
										<td bgcolor="#eee" align="center"><b>Nama Barang/Jasa</b></td>
										<td bgcolor="#eee" align="center"><b>Pemenang</b></td>
										<td bgcolor="#eee" align="center"><b>Dalam Kurs Asing</b></td>
										<td bgcolor="#eee" align="center"><b>Dalam Rupiah</b></td>
									</tr>';	
			// $pemenang_list = reset($pemenang_list);
			foreach ($pemenang_list as $keyp => $valuep) {
				$nilai_idr = explode('-', $tes_idr);
				// $nilai_kurs = explode('-', $tes_kurs);
				$pemenang_auction	.= '<tr>
											<td width=5% align="center">'.($keyp+1).'</td>
											<td>'.$valuep['barang'].'</td>
											<td>'.$valuep['pemenang'].'</td>
											<td>'.$valuep['nilai_kurs'].'</td>
											<td>'.$nilai_idr[0].'</td>
										</tr>';
			}
		}
		#------------------------------------
		$pemenang_auction .= '</table>';
		// print_r($pemenang_list);

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
								<th bgcolor="#eee" align="center">Dalam Kurs Asing</th>
								<th bgcolor="#eee" align="center">Dalam Rupiah</th>
							</tr>
						</thead>';
		
		$index = array();
		
			foreach($history->result() as $data){
				if(!isset($index[$data->id_vendor][$data->id_barang])) $index[$data->id_vendor][$data->id_barang] = 1;
				$nilai = $in_rate = " - ";
				
				if($data->nilai > 0){
					if($data->id_kurs == 1)
						$in_rate = number_format($data->in_rate);	
					else{
						$nilai = $data->symbol." ".number_format($data->nilai);
						$in_rate = number_format($data->in_rate);
					}
				}
			// print_r($history);
				$return .= '<tr>';
					$return .= '<td><div class="history-page-break">'.default_date($data->entry_stamp).date(", H:i:s", strtotime($data->entry_stamp)).'</div></td>';
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
		$date= date_create($fill['auction_date']);

		$ttdpengadaan = "";
		if($type=="internal") {
			$ttdpengadaan = '<table>
						<tr>
							<td height="20"></td>
						</tr>
						<tr>
							<td>
								<table cellspacing="0" cellpadding="3" border="1" width="100%">
									<tr>
										<th align="center" bgcolor="#eee">No.</th>
										<th align="center" bgcolor="#eee">Fungsi Pengadaan & Peserta Pengadaan</th>
										<th align="center" bgcolor="#eee">Perusahaan</th>
										<th align="center" bgcolor="#eee">TTD</th>
									</tr>
									<tr height="30px">
										<td width=5%>1.</td>
										<td><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=40%><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=10%></td>
									</tr>
									<tr height="30px">
										<td width=5%>2.</td>
										<td><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=40%><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=10%></td>
									</tr>
									<tr height="30px">
										<td width=5%>3.</td>
										<td><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=40%><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=10%></td>
									</tr>
									<tr height="30px">
										<td width=5%>4.</td>
										<td><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=40%><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=10%></td>
									</tr>
									<tr height="30px">
										<td width=5%>5.</td>
										<td><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=40%><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=10%></td>
									</tr>
									<tr height="30px">
										<td width=5%>6.</td>
										<td><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=40%><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=10%></td>
									</tr>
									<tr height="30px">
										<td width=5%>7.</td>
										<td><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=40%><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=10%></td>
									</tr>
									<tr height="30px">
										<td width=5%>8.</td>
										<td><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=40%><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=10%></td>
									</tr>
									<tr height="30px">
										<td width=5%>9.</td>
										<td><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=40%><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=10%></td>
									</tr>
									<tr height="30px">
										<td width=5%>10.</td>
										<td><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=40%><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=10%></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>';
		}else{
			if($this->session->userdata('admin')['id_role']==6){
				$ttdpengadaan = '<table>
					<tr>
						<td height="20"></td>
					</tr>
					<tr>
						<td>
							<table cellspacing="0" cellpadding="3" border="1" width="100%">
								<tr>
									<th align="center" bgcolor="#eee">No.</th>
									<th align="center" bgcolor="#eee">Peserta e-Auction</th>
									<th align="center" bgcolor="#eee">TTD</th>
								</tr>
								'.$pengguna.'
							</table>
						</td>
					</tr>
				</table>

				<table>
						<tr>
							<td height="20"></td>
						</tr>
						<tr>
							<td>
								<table cellspacing="0" cellpadding="3" border="1" width="100%">
									<tr>
										<th align="center" bgcolor="#eee">No.</th>
										<th align="center" bgcolor="#eee">Fungsi Pengadaan</th>
										<th align="center" bgcolor="#eee">Jabatan</th>
										<th align="center" bgcolor="#eee">TTD</th>
									</tr>
									<tr height="30px">
										<td width=5%>1.</td>
										<td><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=20%><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td></td>
									</tr>
									<tr height="30px">
										<td width=5%>2.</td>
										<td><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=20%><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td></td>
									</tr>
									<tr height="30px">
										<td width=5%>3.</td>
										<td><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td width=20%><input style="border: none; font-size: 14px; width:100%;" type="text"></td>
										<td></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>';
			}
		}
		

		
		
		
		// print_r($data);
 		
 		//tipe auction
 		if($fill['auction_type'] == 'reverse_auction') $fill['auction_type'] = 'Reverse';
		else $fill['auction_type'] = 'Forward';
		//metode penawaran
		if($fill['metode_penawaran'] == 'harga_satuan') $metode_penawaran = 'Harga Satuan';
		else $metode_penawaran = 'Lump Sum';
			// print_r($fill);

		if($fill['work_area'] == 'kantor_pusat') $area_work = 'Kantor Pusat';
		else $area_work = 'Site Office';

		if($fill['budget_source'] == 'non_perusahaan') $fill['budget_source'] = 'Non-Perusahaan';
		else $fill['budget_source'] = 'Perusahaan';

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
							
							table { page-break-inside:avoid; width: 100%}
						    tr    { page-break-inside: avoid; }
						    thead { display:table-header-group; }
					    }
					     table.inside_table {
						  border-collapse: collapse;
						  margin: -1px;
						}
						table.inside_table th{
							background-color: #eee;
						}
						table.inside_table td, table.inside_table th {
						  border: 1px solid black;
						}
						table.inside_table tr:first-child th {
						  border-top: 0;
						}
						table.inside_table tr:last-child td {
						  border-bottom: 0;
						}
						table.inside_table tr td:first-child,
						table.inside_table tr th:first-child {
						  border-left: 0;
						}
						table.inside_table tr td:last-child,
						table.inside_table tr th:last-child {
						  border-right: 0;
						}
						.layout1 tr td:first-child{
							width: 300px;
						}
						.layout2 tr td{
							text-align: left;
						}
						.layout2 tr td:first-child{
							width: 100;
							text-align: center;
						}
						.layout3 tr td:last-child{
							border-left: 1px solid black;
						}
						.pengadaan tr td:nth-child(2){
							width: 100px;
							text-align: center;	
						}
    				</style>
				</head>
				<body>
					<h3 style="text-align: center">Laporan Paket Auction Barang / Jasa PT Nusantara Regas</h3>
					<input style="border: none; margin:10px 0; padding: 10px 0; font-size: 16px; width:100%;" type="text" value="Tanggal : ">
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
							<td>
								<table cellspacing="0" cellpadding="3" border="1" width="100%" class="layout1">
									<tr>
										<td colspan="2" align="center" bgcolor="#eee">
											<b>Data Paket Auction </b>
										</td>
									</tr>
									<tr>
										<td width="30%" valign="top">Nama paket</td>
										<td valign="top">'.$fill['name'].'</td>
									</tr>
									<!--<tr class="input-form">
										<td>
											Nilai HPS: 
										</td>
										<td>
											 Rp.'.number_format($fill['idr_value']).'
										</td>
									</tr>-->
									<!--
									<tr>
										<td valign="top">Nama Barang/Jasa</td>
										<td valign="top">
											<ol>
												'.$barang.'
											</ol>
										</td>
									</tr>
									-->
									<tr>
										<td valign="top">Sumber Dana</td>
										<td valign="top">'.$fill['budget_source'].'</td>
									</tr>
									<tr>
										<td valign="top">Budget Holder</td>
										<td valign="top">'.$fill['budget_holder'].'</td>
									</tr>
									<tr >
										<td valign="top">Pemegang Cost Center</td>
										<td valign="top">'.$fill['budget_spender'].'
										</td>
									</tr>
									<tr >
										<td valign="top">Tanggal Auction</td>
										<td valign="top">'.default_date(date_format($date, "Y-m-d")).'</td>
									</tr>
									<tr>
										<td valign="top">Lama Auction</td>
										<td valign="top">'.$fill['auction_duration'].' menit</td>
									</tr>
									<tr >
										<td valign="top">Tipe Auction</td>
										<td valign="top">'.$fill['auction_type'].'</td>
									</tr>';
									
									/*if(!$id_vendor)
										$return .= 
										'<tr>
											<td valign="top">Pengguna Barang/Jasa</td>
											<td valign="top">
												<ol>
													'.$pengguna.'
												</ol>
											</td>
										</tr>';*/
									
									$return .= '<tr>
										<td valign="top">Pejabat Auction</td>
										<td valign="top">'.$fill['nama_pejabat'].'</td>
									</tr>
									<!--<tr>
										<td valign="top">Remark</td>
										<td valign="top">'.$fill['remark'].'</td>
									</tr>-->
								</table>
							</td>
						</tr>
					</table>
					<table>
						<tr>
							<td height="20"></td>
						</tr>
						<tr>
							<td>
								<table cellspacing="0" cellpadding="3" border="1" width="100%" class="layout1">
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
										<td valign="top">'.default_date($fill['start_time']).date(", H:i:s", strtotime($fill['start_time'])).' Sampai Dengan '.default_date($fill['start_time']).date(", H:i:s", strtotime($fill['time_limit'])).'</td>
									</tr>
									<tr>
										<td valign="top">
											Lokasi 
											<div><i>(satuan kerja-inside bidding room/outside bidding room)</i></div>
										</td>
										<td valign="top">
											'.$area_work.' - '.$fill['room'].'
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
					</table>
					<table>
						<tr>
							<td height="20"></td>
						</tr>
						<tr>
							<td>'.$pemenang_auction.'</td>
						</tr>
					</table>
					<table>
						<tr>
							<td height="20"></td>
						</tr>
						<tr>
							<td>'.$hasil.'</td>
						</tr>
					</table>
					<table>
						<tr>
							<td height="20"></td>
						</tr>
						<tr>
							<td><div id="history-container">'.$history.'</div></td>
						</tr>
					</table>
					<table>
						<tr>
							<td height="20"></td>
						</tr>
						<tr>
							<td>
								<table cellspacing="0" cellpadding="3" border="1" width="100%">
									<tr>
										<td colspan="4" align="center" bgcolor="#eee">
											<b>Keterangan</b>
										</td>
									</tr>
									
									<tr>
										<td height="20">
										'.(($fill['remark'])?$fill['remark'] : '-').'
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>

					'.$ttdpengadaan.'
						

					<!--
					<table cellspacing="0" cellpadding="3" border="0" width="100%" style="margin-top:60px; font-weight:bold;">
						<thead>
							<tr>
								<th colspan="3" height="20">
									<div class="history-page-break">
										asd
									</div>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr style="padding">
								<td height="150" valign="top" width="15%"></td>
								<td height="150" valign="top" width="15%">
									<div class="history-page-break">
										Mengetahui,<br>
										<br><br><br>
										<br><br><br>
										Fungsi Pengadaan
									</div>
								</td>
								<td height="150" valign="top"></td>
								<td height="150" valign="top" width="15%">
									<div class="history-page-break">
										Menyetujui,<br>
										<br><br><br>
										<br><br><br>
										Pejabat Pengadaan
									</div>
								</td>
								<td height="150" valign="top" width="15%"></td>
							</tr>
						</tbody>
					</table>
					-->
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