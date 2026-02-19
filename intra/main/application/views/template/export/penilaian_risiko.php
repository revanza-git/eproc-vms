<!DOCTYPE html>
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
				<img src="http://127.0.0.1/eproc_nr/assets/images/NUSANTARA-REGAS-2.png" style="height: 45px" style="float: left">
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
				Pengadaan Jasa audit independen agreed-upon procedures atas biaya operasional fsru dan lng carrier untuk tahun buku yang berakhir 31 desember 2016 dan 31 desember 2017 
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
				Dion Andrianto <br>
				<br>
				<br>
				<br>
				<br>
				Tanggal : 18 Oktober 2016
			</td>
			<td style="text-align: left">
				Disetujui Oleh: <br>
				Azhar Habieb <br>
				<br>
				<br>
				<br>
				<br>
				Tanggal : 18 Oktober 2016
			</td>
		</tr>
	</table>
</body>
</html>