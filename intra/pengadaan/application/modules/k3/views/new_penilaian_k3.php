<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Lampiran CSMS</title>
		<style type="text/css">
			@import url('https://fonts.googleapis.com/css?family=Roboto:300,400,700');
			thead:before, thead:after { display: none; }
			tbody:before, tbody:after { display: none; }
				@page{
					size: A4 portrait;
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
				border-collapse: collapse;
				font-family: 'Roboto', sans-serif;
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
			/*.is-yellow {background-color: #FECA57!important;}
			.is-red {background-color: #FF7675!important;}
			.is-blue {background-color: #54A0FF!important;}*/
			img {
				height: 10px;
			}

			.kelompok-pertanyaan {
				text-align: left;
			}
			.subtotal {
				text-align: left;
			}
			.no {
				vertical-align: top!important;
			}
			.SAM {
				width: 5%;
			}
			.pertanyaan {
				text-align: left;
			}
			.total {
				text-align: left;
				font-weight: 700;
			}
			.zui-table {
			    border: solid 1px #DDEEEE;
			    border-collapse: collapse;
			    border-spacing: 0;
			    font: normal 14px Arial, sans-serif;
			}
			.zui-table th {
			    background-color: #DDEFEF;
			    border: solid 1px #DDEEEE;
			    color: #336B6B;
			    padding: 10px;
			    text-shadow: 1px 1px 1px #fff;
			}
			.zui-table td {
			    border: solid 1px #DDEEEE;
			    color: #333;
			    padding: 10px;
			    text-shadow: 1px 1px 1px #fff;
			}
			.sub-th {
				background-color: #cedede!important;
			    border: solid 1px #cedede!important;
			}
		</style>
	</head>
	<body>
		<form action="" method="POST">
			<table align="center" class="zui-table">
				<tr>
					<th rowspan="3">NO</th>
					<th rowspan="3" colspan="2">ITEM</th>
					<th colspan="4">PENILAIAN</th>
					<th rowspan="3" class="SAM">Score Acuan Max</th>
					<th rowspan="3">KETERANGAN VERIFIKASI DOKUMEN</th>
					<th rowspan="3">KETERANGAN VERIFIKASI LAPANGAN</th>
				</tr>
				<tr>
					<th  class="" colspan="3">Dokumen</th>
					<th class="" >Lapangan</th>
				</tr>
				<tr>
					<th class="">Ya</th>
					<th class="">Tidak</th>
					<th class="score">Score</th>
					<th class="score">Score</th>
				</tr>
				<!-- pertanyaan start Kelompok Pertanyaan - 1 -->
				<tr class="header-pertanyaan">
					<td class="no" rowspan="13">1</td>
					<td class="kelompok-pertanyaan" colspan="9">Komitmen Manajemen</td>
				</tr>
				<tr>
					<td>a</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara mempunyai kebijakan HSE?
					</td>
					<div class="a1">
						<td>
							<input type="radio" name="a1" value="3" data-id="1" class="radio radio-1">
						</td>
						<td>
							<input type="radio" name="a1" value="0" data-id="1" class="radio radio-1">
						</td>
					</div>
					<td class="score"></td>
					<td class="score"></td>
					<td>3</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>b</td>
					<td class="pertanyaan">
						Apakah kebijakan HSE tsb sudah disosialisasikan & dipahami oleh seluruh pekerja?
					</td>
					<div class="b1">
						<td>
							<input type="radio" name="b1" value="2" data-id="1" class="radio radio-1">
						</td>
						<td>
							<input type="radio" name="b1" value="0" data-id="1" class="radio radio-1">
						</td>
					</div>
					<td class="score"></td>
					<td class="score"></td>
					<td>2</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>c</td>
					<td class="pertanyaan">
						Apakah kebijakan HSE tsb ditanda tangani pimpinan tertinggi?
					</td>
					<td>
						<input type="radio" name="c1" value="3" data-id="1" class="radio radio-1">
					</td>
					<td>
						<input type="radio" name="c1" value="0" data-id="1" class="radio radio-1">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>3</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>d</td>
					<td class="pertanyaan">
						 Apakah kebijakan HSE tersebut secara berkala di review/dimutakhirkan sesuai kondisi internal & eksternal perusahaan?
					</td>
					<td>
						<input type="radio" name="d1" value="3" data-id="1" class="radio radio-1">
					</td>
					<td>
						<input type="radio" name="d1" value="0" data-id="1" class="radio radio-1">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>3</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>e</td>
					<td class="pertanyaan">
						Apakah Perusahaan Saudara mempunyai organisasi HSE?
					</td>
					<td>
						<input type="radio" name="e1" value="3" data-id="1" class="radio radio-1">
					</td>
					<td>
						<input type="radio" name="e1" value="0" data-id="1" class="radio radio-1">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>3</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>f</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara memiliki program Inspeksi Manajemen HSE?
					</td>
					<td>
						<input type="radio" name="f1" value="3" data-id="1" class="radio radio-1">
					</td>
					<td>
						<input type="radio" name="f1" value="0" data-id="1" class="radio radio-1">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>3</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>g</td>
					<td class="pertanyaan">
						Apakah hasil temuan Inspeksi Manajemen HSE selalu ditindak lanjuti?
					</td>
					<td>
						<input type="radio" name="g1" value="3" data-id="1" class="radio radio-1">
					</td>
					<td>
						<input type="radio" name="g1" value="0" data-id="1" class="radio radio-1">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>3</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>h</td>
					<td class="pertanyaan">
						Apakah dalam setiap rapat manajemen aspek HSE selalu dibahas?
					</td>
					<td>
						<input type="radio" name="h1" value="3" data-id="1" class="radio radio-1">
					</td>
					<td>
						<input type="radio" name="h1" value="0" data-id="1" class="radio radio-1">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>3</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>i</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara menyelenggarakan rapat–rapat rutin tentang HSE?
					</td>
					<td>
						<input type="radio" name="i1" value="3" data-id="1" class="radio radio-1">
					</td>
					<td>
						<input type="radio" name="i1" value="0" data-id="1" class="radio radio-1">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>3</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>j</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara memiliki program kampanye HSE?
					</td>
					<td>
						<input type="radio" name="j1" value="3" data-id="1" class="radio radio-1">
					</td>
					<td>
						<input type="radio" name="j1" value="0" data-id="1" class="radio radio-1">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>3</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>k</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara melaksanakan audit HSE pada setiap pekerjaan?
					</td>
					<td>
						<input type="radio" name="k1" value="3" data-id="1" class="radio radio-1">
					</td>
					<td>
						<input type="radio" name="k1" value="0" data-id="1" class="radio radio-1">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>3</td>
					<td></td>
					<td></td>
				</tr>
				<tr class="footer-pertanyaan">
					<td colspan="6" class="subtotal">Sub Total 1</td>
					<td class="sub sub-1"></td>
					<td colspan="2"></td>
				</tr>
				<!-- pertanyaan start Kelompok Pertanyaan - 2 -->
				<tr class="header-pertanyaan">
					<td class="no" rowspan="8">2</td>
					<td class="kelompok-pertanyaan" colspan="9">Pembinaan</td>
				</tr>
				<tr>
					<td>a</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara mempunyai program pembelajaran/ pelatihan (teori & praktek) HSE?
					</td>
					<td>
						<input type="radio" name="a2" value="4" data-id="2" class="radio radio-2">
					</td>
					<td>
						<input type="radio" name="a2" value="0" data-id="2" class="radio radio-2">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>4</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>b</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara memiliki program pelatihan Pertolongan Pertama Pada Kecelakaan (P3K)?
					</td>
					<td>
						<input type="radio" name="b2" value="3" data-id="2" class="radio radio-2">
					</td>
					<td>
						<input type="radio" name="b2" value="0" data-id="2" class="radio radio-2">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>3</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>c</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara mempunyai program orientasi HSE bagi karyawan baru?
					</td>
					<td>
						<input type="radio" name="c2" value="3" data-id="2" class="radio radio-2">
					</td>
					<td>
						<input type="radio" name="c2" value="0" data-id="2" class="radio radio-2">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>3</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>d</td>
					<td class="pertanyaan">
						Apakah Perusahaan Saudara melakukan pemeriksaan kesehatan terhadap calon pekerja?
					</td>
					<td>
						<input type="radio" name="d2" value="3" data-id="2" class="radio radio-2">
					</td>
					<td>
						<input type="radio" name="d2" value="0" data-id="2" class="radio radio-2">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>3</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>e</td>
					<td class="pertanyaan">
						Apakah perusahaan Saudara melakukan pemeriksaan kesehatan pekerja secara berkala?
					</td>
					<td>
						<input type="radio" name="e2" value="3" data-id="2" class="radio radio-2">
					</td>
					<td>
						<input type="radio" name="e2" value="0" data-id="2" class="radio radio-2">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>3</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>f</td>
					<td class="pertanyaan">
						 Apakah perusahaan saudara memberikan kesempatan kepada para pekerja untuk mengikuti seminar atau semacamnya yang berkaitan dengan aspek HSE?
					</td>
					<td>
						<input type="radio" name="f2" value="2" data-id="2" class="radio radio-2">
					</td>
					<td>
						<input type="radio" name="f2" value="0" data-id="2" class="radio radio-2">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>2</td>
					<td></td>
					<td></td>
				</tr>
				<tr class="footer-pertanyaan">
					<td colspan="6" class="subtotal">Sub Total 2</td>
					<td class="sub sub-2"></td>
					<td colspan="2"></td>
				</tr>
				<!-- pertanyaan start Kelompok Pertanyaan - 3 -->
				<tr class="header-pertanyaan">
					<td class="no" rowspan="16">3</td>
					<td class="kelompok-pertanyaan" colspan="9">Prosedur</td>
				</tr>
				<tr>
					<td>a</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara mempunyai prosedur keadaan darurat?
					</td>
					<td>
						<input type="radio" name="a3" value="2" data-id="3" class="radio radio-3">
					</td>
					<td>
						<input type="radio" name="a3" value="0" data-id="3" class="radio radio-3">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>2</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>b</td>
					<td class="pertanyaan">
						Apakah prosedur keadaan darurat sudah dipahami oleh semua pekerja?
					</td>
					<td>
						<input type="radio" name="b3" value="2" data-id="3" class="radio radio-3">
					</td>
					<td>
						<input type="radio" name="b3" value="0" data-id="3" class="radio radio-3">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>2</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>c</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara mempunyai program pelatihan untuk menghadapi dan mengatasi keadaan darurat?
					</td>
					<td>
						<input type="radio" name="c3" value="2" data-id="3" class="radio radio-3">
					</td>
					<td>
						<input type="radio" name="c3" value="0" data-id="3" class="radio radio-3">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>2</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>d</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara memiliki prosedur Pertolongan Pertama Pada Kecelakaan (P3K)?
					</td>
					<td>
						<input type="radio" name="d3" value="2" data-id="3" class="radio radio-3">
					</td>
					<td>
						<input type="radio" name="d3" value="0" data-id="3" class="radio radio-3">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>2</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>e</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara mempunyai prosedur pelaporan insiden HSE?
					</td>
					<td>
						<input type="radio" name="e3" value="2" data-id="3" class="radio radio-3">
					</td>
					<td>
						<input type="radio" name="e3" value="0" data-id="3" class="radio radio-3">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>2</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>f</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara mempunyai prosedur investigasi kecelakaan HSE?
					</td>
					<td>
						<input type="radio" name="f3" value="2" data-id="3" class="radio radio-3">
					</td>
					<td>
						<input type="radio" name="f3" value="0" data-id="3" class="radio radio-3">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>2</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>g</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara mempunyai Standard Operating Prosedur (SOP) semua peralatan?
					</td>
					<td>
						<input type="radio" name="g3" value="2" data-id="3" class="radio radio-3">
					</td>
					<td>
						<input type="radio" name="g3" value="0" data-id="3" class="radio radio-3">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>2</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>h</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara mempunyai prosedur yang mensyaratkan pemenuhan aspek HSE terhadap penyediaan dan penggunaan berbagai material kebutuhan operasi?
					</td>
					<td>
						<input type="radio" name="h3" value="2" data-id="3" class="radio radio-3">
					</td>
					<td>
						<input type="radio" name="h3" value="0" data-id="3" class="radio radio-3">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>2</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>i</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara memiliki prosedur penanganan, pengangkutan dan penyimpanan bahan berbahaya dan beracun (B3)?
					</td>
					<td>
						<input type="radio" name="i3" value="2" data-id="3" class="radio radio-3">
					</td>
					<td>
						<input type="radio" name="i3" value="0" data-id="3" class="radio radio-3">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>2</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>j</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara memiliki prosedur penanganan limbah padat, limbah cair, emisi?
					</td>
					<td>
						<input type="radio" name="j3" value="2" data-id="3" class="radio radio-3">
					</td>
					<td>
						<input type="radio" name="j3" value="0" data-id="3" class="radio radio-3">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>2</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>k</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara mempunyai program gerakan hidup sehat?
					</td>
					<td>
						<input type="radio" name="k3" value="2" data-id="3" class="radio radio-3">
					</td>
					<td>
						<input type="radio" name="k3" value="0" data-id="3" class="radio radio-3">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>2</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>l</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara memiliki prosedur/peraturan pencegahan kecelakaan lalulintas?
					</td>
					<td>
						<input type="radio" name="l3" value="2" data-id="3" class="radio radio-3">
					</td>
					<td>
						<input type="radio" name="l3" value="0" data-id="3" class="radio radio-3">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>2</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>m</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara memiliki prosedur/peraturan larangan pemakaian obat-obat terlarang & minuman keras?
					</td>
					<td>
						<input type="radio" name="m3" value="2" data-id="3" class="radio radio-3">
					</td>
					<td>
						<input type="radio" name="m3" value="0" data-id="3" class="radio radio-3">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>2</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>n</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara memiliki dan menerapkan panduan / referensi (buku, standard, kumpulan peraturan perundangan) tentang HSE?
					</td>
					<td>
						<input type="radio" name="n3" value="2" data-id="3" class="radio radio-3">
					</td>
					<td>
						<input type="radio" name="n3" value="0" data-id="3" class="radio radio-3">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>2</td>
					<td></td>
					<td></td>
				</tr>
				<tr class="footer-pertanyaan">
					<td colspan="6" class="subtotal">Sub Total 3</td>
					<td class="sub sub-3"></td>
					<td colspan="2"></td>
				</tr>
				<!-- pertanyaan start Kelompok Pertanyaan - 4 -->
				<tr class="header-pertanyaan">
					<td class="no" rowspan="8">4</td>
					<td class="kelompok-pertanyaan" colspan="9">Peralatan</td>
				</tr>
				<tr>
					<td>a</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara selalu memeriksa dan mensertifikasi secara rutin semua peralatan operasi?
					</td>
					<td>
						<input type="radio" name="a4" value="2" data-id="4" class="radio radio-4">
					</td>
					<td>
						<input type="radio" name="a4" value="0" data-id="4" class="radio radio-4">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>2</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>b</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara memiliki ketentuan yang mengatur pengunaan alat pelindung diri (APD)?
					</td>
					<td>
						<input type="radio" name="b4" value="2" data-id="4" class="radio radio-4">
					</td>
					<td>
						<input type="radio" name="b4" value="0" data-id="4" class="radio radio-4">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>2</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>c</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara menyediakan alat pelindung diri (APD) pada setiap pekerja yang akan melaksanakan pekerjaan?
					</td>
					<td>
						<input type="radio" name="c4" value="3" data-id="4" class="radio radio-4">
					</td>
					<td>
						<input type="radio" name="c4" value="0" data-id="4" class="radio radio-4">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>3</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>d</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara memiliki peralatan pencegahan & penanggulangan pencemaran di darat?
					</td>
					<td>
						<input type="radio" name="d4" value="2" data-id="4" class="radio radio-4">
					</td>
					<td>
						<input type="radio" name="d4" value="0" data-id="4" class="radio radio-4">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>2</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>e</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara memiliki peralatan pencegahan & penanggulangan pencemaran di perairan?
					</td>
					<td>
						<input type="radio" name="e4" value="2" data-id="4" class="radio radio-4">
					</td>
					<td>
						<input type="radio" name="e4" value="0" data-id="4" class="radio radio-4">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>2</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>f</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara memiliki peralatan pencegahan & penanggulangan kebakaran serta penanganan kecelakaan kerja?
					</td>
					<td>
						<input type="radio" name="f4" value="2" data-id="4" class="radio radio-4">
					</td>
					<td>
						<input type="radio" name="f4" value="0" data-id="4" class="radio radio-4">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>2</td>
					<td></td>
					<td></td>
				</tr>
				<tr class="footer-pertanyaan">
					<td colspan="6" class="subtotal">Sub Total 4</td>
					<td class="sub sub-4"></td>
					<td colspan="2"></td>
				</tr>
				<!-- pertanyaan start Kelompok Pertanyaan - 5 -->
				<tr class="header-pertanyaan">
					<td class="no" rowspan="4">5</td>
					<td class="kelompok-pertanyaan" colspan="9">Peralatan</td>
				</tr>
				<tr>
					<td>a</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara mengindentifikasi dan menganalisa setiap potensi bahaya dan resiko yang terkait dengan aktivitas operasional perusahaan?
					</td>
					<td>
						<input type="radio" name="a5" value="3" data-id="5" data-id="5" class="radio radio-5">
					</td>
					<td>
						<input type="radio" name="a5" value="0" data-id="5" data-id="5" class="radio radio-5">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>3</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>b</td>
					<td class="pertanyaan">
						Apakah setiap potensi bahaya dan resiko yang diidentifikasi telah ditentukan rencana mitigasi dan monitoringnya?
					</td>
					<td>
						<input type="radio" name="b5" value="3" data-id="5" class="radio radio-5">
					</td>
					<td>
						<input type="radio" name="b5" value="0" data-id="5" class="radio radio-5">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>3</td>
					<td></td>
					<td></td>
				</tr>
				<tr class="footer-pertanyaan">
					<td colspan="6" class="subtotal">Sub Total 5</td>
					<td class="sub sub-5"></td>
					<td colspan="2"></td>
				</tr>
				<!-- pertanyaan start Kelompok Pertanyaan - 6 -->
				<tr class="header-pertanyaan">
					<td class="no" rowspan="3">6</td>
					<td class="kelompok-pertanyaan" colspan="9">Lain-lain</td>
				</tr>
				<tr>
					<td>a</td>
					<td class="pertanyaan">
						Apakah perusahaan saudara memiliki program implementasi aspek HSE lainnya (diluar dari daftar pertanyaan sebelumnya)?
					</td>
					<td>
						<input type="radio" name="a6" value="3" data-id="6" class=" radio radio-6">
					</td>
					<td>
						<input type="radio" name="a6" value="0" data-id="6" class="radio radio-6">
					</td>
					<td class="score"></td>
					<td class="score"></td>
					<td>3</td>
					<td></td>
					<td></td>
				</tr>
				<tr class="footer-pertanyaan">
					<td colspan="6" class="subtotal">Sub Total 6</td>
					<td class="sub sub-6"></td>
					<td colspan="2"></td>
				</tr>
				<!-- nilai total -->
				<tr>
					<td colspan="7" class="total">Nilai total yang diperoleh (Sub total 1+2+3+4+5+6) :</td>
					<td class="total-keseluruhan"></td>
					<td colspan="2" class="result"></td>
				</tr>
			</table>
		</form>
	</body>
</html>