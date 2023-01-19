<?php
class Summary extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		$this->load->model('report/report_pengadaan_model','rpm');
		$this->load->model('pengadaan_model','pm');
	}
	
	function index($id_lelang =  '', $id_vendor = ''){
		$hide = array();
		$summary = array('bsb','peserta','penawaran','barang','progress_pengadaan','pemenang','kontrak','progress_pengerjaan');
		foreach ($summary as $key => $value) {
			$hide[] = $value;
			if($this->session->userdata('summary') == $value){
				break;
			}
		}

		$fill = $this->rpm->get_header($id_lelang);
		$pemenang = $this->rpm->get_pemenang($id_lelang)->row_array();
		$kontrak = $this->rpm->get_kontrak($id_lelang)->row_array();
		$assessment = $this->rpm->get_assessment($id_lelang, $pemenang['id_vendor'])->row_array();

		$pengerjaan = $this->rpm->get_contract_progress($id_lelang);
		$_pengerjaan = "";
		$start_date 	= '';
		$end_date	= '';
		foreach($pengerjaan as $key => $pp){
			if($key==0) $start_date	= $pp['start_date'];
			$_pengerjaan	.= '<tr>';
			$_pengerjaan 	.= '<td>'.$pp["step_name"].'</td>';
			$_pengerjaan 	.= '<td>'.get_range_date($pp['end_date'],$pp['start_date']).' hari. ('.default_date($pp['start_date']).' - '.default_date($pp['end_date']).')</td>';
			$_pengerjaan 	.= '</tr>';
			if(strtotime($end_date)<strtotime($pp['end_date'])) $end_date	= $pp['end_date'];
		}
		$denda = array();
		$denda = $this->pm->get_denda($id_lelang);
		if(count($denda)>0){
			$_pengerjaan	.= '<tr>';
			$_pengerjaan 	.= '<td>Denda</td>';
			$_pengerjaan 	.= '<td>'.get_range_date($denda['end_date'],$denda['start_date']).' hari. ('.default_date($denda['start_date']).' - '.default_date($denda['end_date']).')</td>';
			$_pengerjaan 	.= '</tr>';
			if(strtotime($end_date)<strtotime($denda['end_date'])) $end_date	= $denda['end_date'];
		}
		

		$barang = $this->rpm->get_barang($id_lelang)->result_array();
		$_barang ='';
		$no=1;
		foreach($barang as $key => $data){
			$_barang .= '<tr>';
			$_barang .= '<td >'.$no.'</td>';
			$_barang .= '<td>'.$data["nama_barang"].'</td>';
			$_barang .= '<td>'.$data["kurs"].' '.number_format($data["nilai_hps"]).'</td>';
			$_barang .= '</tr>';
			$no++;
		}

		
		$peserta = $this->rpm->get_peserta($id_lelang)->result_array();

		$_peserta ='';

		foreach($peserta as $data){
			$_peserta .= '<tr>';
			$_peserta .= '<td rowspan="3">'.$data['name'].'</td>';
			$_peserta .= '<td>Hasil Evaluasi</td>';
			$_peserta .= '<td>'.$data["nilai_evaluasi"].'</td>';
			$_peserta .= '</tr>';

			$_peserta .= '<tr>';
			$_peserta .= '<td>Nilai Penawaran</td>';
			$_peserta .= '<td>Rp. '.number_format($data["idr_value"]).'</td>';
			$_peserta .= '</tr>';
			$_peserta .= '<tr>';
			$_peserta .= '<td>Keterangan</td>';
			$_peserta .= '<td>'.$data["remark"].'</td>';
			$_peserta .= '</tr>';

			
		}
		$peserta = $_peserta;

		$denda = $this->pm->get_denda_price($id_lelang);


		$bsb = $this->rpm->get_bsb($id_lelang);
		foreach($bsb->result() as $data)
			$_bsb .= '<li>'.$data->bidang.' - '.$data->sub_bidang.'</li>'; 
		$bsb = $_bsb;
		
		$progress = $this->rpm->get_progress_pengadaan($id_lelang);

		$_progress = '';
		foreach($progress as $key => $data){
			$_progress .= '<tr>';
			$_progress .= '<td>'.$key.'</td>'; 
			$_progress .= '<td>'.(($data['value']==1)?'<i class="fa fa-check-square-o"></i>':'').'</td>'; 
			$_progress .= '<td style="text-align:center">'.(($data['value']==1)?default_date($data['date']):'').'</td>'; 
			$_progress .= '</tr>';
		}
		
		$progress = $_progress;
		
		$kurs = $this->rpm->get_kurs($id_lelang);
		foreach($kurs->result() as $data)
			$_kurs .= '<li>'.$data->name.'</li>'; 
		$kurs = $_kurs;
		
		
		
		$day = (ceil(strtotime($denda['end_date']) - strtotime($denda['start_date']))/86400)+1;

		
			
		$return = '
			<html>
				<head>
					<link rel="stylesheet" href="'.site_url('assets/css/font-awesome.css').'" type="text/css"/>
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

					    table.inside_table {
						  border-collapse: collapse;
						  margin: -1px;
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
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
							<td>
								<h3 style="text-align: center">Laporan Paket Pengadaan Barang / Jasa PT Nusantara Regas</h3>	
							</td>
						</tr>
						<tr>
							<td>
								<input style="border: none; margin:10px 0; padding: 10px 0; font-size: 16px; width:100%;" type="text" value="Tanggal : ">
							</td>
						</tr>
						<tr>
							<td>
								
								<table cellspacing="0" cellpadding="3" border="1" width="100%" class="layout1">
									<tr>
										<td colspan="2" align="center" bgcolor="#eee">
											<b>Data Paket Pengadaan </b>
										</td>
									</tr>
									<tr>
										<td width="30%" valign="top">Nama paket</td>
										<td valign="top">'.$fill['name'].'</td>
									</tr>
									<tr class="input-form">
										<td>
											Nilai HPS: 
										</td>
										<td>
											 Rp.'.number_format($fill['idr_value']).'
										</td>
									</tr>
									<tr class="input-form">
										<td>
											
										</td>
										<td>
											'.$fill['kurs_symbol'].' '.number_format($fill['kurs_value']).'
											
										</td>
									</tr>
									
									<tr>
										<td valign="top">Sumber Dana</td>
										<td valign="top">'.$fill['budget_source'].'</td>
									</tr>

									<tr>
										<td valign="top">Pejabat Pengadaan</td>
										<td valign="top">'.$fill['nama_pejabat'].'</td>
									</tr>
									<tr>
										<td valign="top">Tahun Anggaran</td>
										<td valign="top">'.$fill['budget_year'].'</td>
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
									<tr>
										<td valign="top">Metode Pengadaan</td>
										<td valign="top">'.$fill['mekanisme'].'
										</td>
									</tr>
									<tr>
										<td valign="top">Metode Evaluasi</td>
										<td valign="top">'.$fill['evaluation_method'].'
										</td>
									</tr>
								</table>
								
							</td>
						</tr>
						<tr>
							<td height="30"></td>
						</tr>
						<tr>
							<td>
								
								<table cellspacing="0" cellpadding="3" border="1" width="100%" class="layout1">
									<tr>
										<td colspan="3" align="center" bgcolor="#eee">
											<b>Data e-Procurement</b>
										</td>
									</tr>
									'.((in_array('bsb', $hide)) ? '
									<tr>
										<td valign="top">
											Bidang Sub Bidang
										</td>
										<td valign="top" colspan="3">
											<ol>
												'.$bsb.'
											</ol>
										</td>
									</tr>
									' : '' ).'
									'.((in_array('peserta', $hide)) ? '
									<tr>
										<td colspan="4" align="center" bgcolor="#eee">
											<b>Penyedia Barang & Jasa</b>
										</td>
									</tr>
									
									<tr>
										<td>
											Peserta
										</td>
										<td colspan="3" style="padding:0">
											<table cellspacing="0" cellpadding="3" border="0" width="100%" class="inside_table">
												'.$_peserta.'
											</table>
										</td>
									</tr>
									' : '' ).'
									'.((in_array('pemenang', $hide)) ? '
									<tr>
										<td >
											Pemenang
										</td>
										<td valign="top" colspan="3" style="padding:0">
											<table width="100%" class="layout3" cellspacing="0" cellpadding="3" >
												<tr>
													<td>Nama Pemenang</td>
													<td>'.$pemenang['name'].'</td>
												</tr>
												<tr>
													<td>Nilai</td>
													<td>Rp. '.number_format($pemenang['idr_kontrak']).'</td>
												</tr>
												'.(($pemenang['kurs_kontrak']>0)?'
												<tr>
													<td></td>
													<td>'.$pemenang['kurs'].' '.number_format($pemenang['kurs_kontrak']).'</td>
												</tr>':'').'
												<tr>
													<td>Hasil Evaluasi Teknikal</td>
													<td>'.$pemenang['nilai_evaluasi'].'</td>
												</tr>
												<tr>
													<td>Nilai Evaluasi Assessment</td>
													<td>'.$assessment['point'].'</td>
												</tr>
												<tr>
													<td>Efisiensi</td>
													<td>'.number_format((float)(($fill['idr_value']-$pemenang['idr_kontrak'])/$fill['idr_value'] * 100),2,'.','').' %</td>
												</tr>
												'.(($fill['kurs_value']>0||$pemenang['kurs_value']>0)?'
												<tr>
													<td></td>
													<td>Efisiensi dalam kurs : '.number_format((float)(($fill['kurs_value']-$pemenang['kurs_kontrak'])/$fill['kurs_value'] * 100),2,'.','').' %</td>
												</tr>':'').'
												
												'.((in_array('progress_pengerjaan', $hide)) ? '
													'.(($denda>0)?'
													<tr>
														<td>Denda</td>
														<td>Rp. '.number_format($denda).'</td>
													</tr>':'').'
												' : '' ).'
												
											</table>
										</td>
									</tr>
									' : '' ).'
								</table>
								
							</td>
						</tr>
						<tr>
							<td height="30"></td>
						</tr>
						'.((in_array('barang', $hide)) ? '
						<tr>
							<td>
								<table cellspacing="0" cellpadding="3" border="1" width="100%" class="layout2">
									<tr>
										<td colspan="4" align="center" bgcolor="#eee">
											<b>Barang/Jasa</b>
										</td>
									</tr>
									<tr>
										<td align="center" bgcolor="#eee">
											<b>No</b>
										</td>
										<td style="text-align:center" bgcolor="#eee">
											<b>Nama</b>
										</td>
										<td style="text-align:center"  bgcolor="#eee">
											<b>Nilai Barang</b>
										</td>
									</tr>
									<tr>
										'.$_barang.'
									</tr>
								</table>
							<td>
						</tr>
						' : '' ).'
						<tr>
							<td height="30"></td>
						</tr>
						'.((in_array('kontrak', $hide)) ? '
						<tr>
							<td>
								<!-- KONTRAK COY-->
								<table cellspacing="0" cellpadding="3" border="1" width="100%" class="layout1">
									<tr>
										<td colspan="2" align="center" bgcolor="#eee">
											<b>Penandatanganan Kontrak</b>
										</td>
									</tr>
									<tr>
										<td valign="top">Nama Perusahaan</div>
										</td>
										<td valign="top">
											
												'.$kontrak['name'].'
											
										</td>
									</tr>
									<tr>
										<td valign="top">No. SPPBJ</div>
										</td>
										<td valign="top">
											
												'.$kontrak['no_sppbj'].'
											
										</td>
									</tr>
									<tr>
										<td valign="top">Tanggal SPPBJ</div>
										</td>
										<td valign="top">
											
												'.default_date($kontrak['sppbj_date']).'
											
										</td>
									</tr>
									<tr>
										<td valign="top">No. SPMK</div>
										</td>
										<td valign="top">
											
												'.$kontrak['no_spmk'].'
											
										</td>
									</tr>
									<tr>
										<td valign="top">Tanggal SPMK</div>
										</td>
										<td valign="top">
											
												'.default_date($kontrak['spmk_date']).'
											
										</td>
									</tr>
									<tr>
										<td valign="top">Periode Kerja</div>
										</td>
										<td valign="top">
												'.default_date($kontrak['start_work']).' sampai '.default_date($kontrak['end_work']).'
										</td>
									</tr>
									<tr>
										<td valign="top">No. Kontrak / PO.</div>
										</td>
										<td valign="top">
												'.$kontrak['no_contract'].'
										</td>
									</tr>
									<tr class="input-form">
										<td>
											Nilai Kontrak / PO 
										</td>
										<td>
											 Rp.'.number_format($kontrak['contract_price']).'
										</td>
									</tr>
									<tr class="input-form">
										<td>
											
										</td>
										<td>
											'.$kontrak['kurs_symbol'].' '.number_format($kontrak['contract_price_kurs']).'
											
										</td>
									</tr>
									<tr>
										<td valign="top">Periode Kontrak / PO</div>
										</td>
										<td valign="top">
												'.default_date($kontrak['start_contract']).' sampai '.default_date($kontrak['end_contract']).'
										</td>
									</tr>
								</table>
								
							</td>
						</tr>
						' : '' ).'
						'.((in_array('progress_pengerjaan', $hide)) ? '
						<tr>
							<td height="30"></td>
						</tr>
						<tr>
							<td>
								<table cellspacing="0" cellpadding="3" border="1" width="100%" class="layout1">
									<tr>
										<td colspan="3" align="center" bgcolor="#eee">
											<b>Progress Paket Pekerjaan</b>
										</td>
									</tr>
									<tr>
										<td align="center" bgcolor="#eee">
											<b>Tahap Pekerjaan</b>
										</td>
										<td align="center" bgcolor="#eee">
											<b>Waktu yang ditetapkan</b>
										</td>
										
									</tr>
									<tr>
										'.$_pengerjaan.'
									</tr>
									<tr>
										<td><b>Total Hari</b></td>
										<td>'.get_range_date($end_date,$start_date).' hari. ('.default_date($start_date).' - '.default_date($end_date).')</td>
									</tr>
									
								</table>
							<td>
						</tr>
						' : '' ).'
						'.((in_array('progress_pengadaan', $hide)) ? '
						<tr>
							<td height="30"></td>
						</tr>
						<tr>
							<td>
								
								<table cellspacing="0" cellpadding="3" border="1" width="100%" class="layout1 pengadaan">
									<tr>
										<td colspan="3" align="center" bgcolor="#eee">
											<b>Progress Paket Pengadaan</b>
										</td>
									</tr>
									<tr>
										<td align="center" bgcolor="#eee">
											<b>Tahap Pengadaan</b>
										</td>
										<td align="center" bgcolor="#eee">
											<b>Status</b>
										</td>
										<td align="center" bgcolor="#eee">
											<b>Tanggal</b>
										</td>
									</tr>
									'.$_progress.'
								</table>
								
							</td>
						</tr>

						<tr>
							<td height="30"></td>
						</tr>
						' : '' ).'
						<tr>
							<td>
								<table cellspacing="0" cellpadding="3" border="1" width="100%">
									<tr>
										<td colspan="4" align="center" bgcolor="#eee">
											<b>Keterangan</b>
										</td>
									</tr>
									
									<tr>
										<td>
										'.(($fill['remark'])?$fill['remark'] : '-').'
										</td>
									</tr>
								</table>
							<td>
						</tr>
						
						<tr>
							<td height="30"></td>
						</tr>
					</table>
				</body>
			<html>';
		
		echo $return;
		
		
	}
}