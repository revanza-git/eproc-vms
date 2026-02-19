<style type="text/css">
			html{
				padding: 0;
			}
			.container{
				margin: 0;
			}
		</style>
		<div class="container">
			<div class="header">
				<table border="0" cellpadding="0" cellspacing="0">
					<tbody>
						<tr>
							<td width="250"><img src="<?php echo base_url('assets/images/login-regas-logo.jpg');?>" height="70"></td>
							<td valign="bottom">
								<div style="font-size : 14px">Aplikasi Management Penyedia Barang &amp; Jasa</div>
								<div style="font-size : 11px"><i>Laporan Data Penyedia Barang &amp; Jasa - Administrasi, Akta Pendirian/Perubahan Perusahaan</i></div>
							</td>
							<td align="right" valign="bottom">
								<div>Dicetak pada tanggal : <?php echo date("d-m-Y h:i") ?></div>
							</td>
						</tr>
						<tr>
							<td colspan="3" height="5"></td>
						</tr>
						<tr>
							<td colspan="3">
								<div style="border-bottom : 2px solid #000; border-top : 1px solid #000; height : 2px"></div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="content">			
				<table class="recap-table" border="0" cellpadding="0" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th colspan="3" align="left">Data Administrasi</th>
						</tr>
					</thead>
					<tbody>
						<?php
						// echo print_r($administrasi);
						if(count($administrasi)){
							foreach($administrasi as $row => $value){
						?>
						<tr>
							<td>1.</td>
							<td>Badan Usaha</td>
							<td><?php echo $value['id_legal'];?></td>
						</tr>
						<tr>
							<td width="100">2.</td>
							<td width="30%">Nama</td>
							<td width="68%"><?php echo $value['name'];?></td>
						</tr>
						<tr>
							<td>3.</td>
							<td>NPWP</td>
							<td>
								<?php echo $value['npwp_code'];?>
								<!--<font style="font-size : 11px">tanggal pengukuhan <?php echo $value['npwp_date'];?></font>-->
							</td>
						</tr>
						<tr>
							<td>4.</td>
							<td>NPPKP</td>
							<td>
								<?php echo $value['nppkp_code'];?>
								<!--<font style="font-size : 11px">tanggal pengukuhan <?php echo $value['nppkp_date'];?></font>-->
							</td>
						</tr>
						<tr>
							<td>5.</td>
							<td>Status</td>
							<td>
							<?php 
							if($value['pic_name']=="cabang"){
							?>
								<div style="float : left">
									<i class="fa fa-square-o"></i>
								</div>
								<div style="float : left; margin-left : 5px">Pusat</div>
								<div style="float : left; margin-left : 10px">
									<i class="fa fa-check-square-o"></i>
								</div>
								<div style="float : left; margin-left : 5px">cabang</div>
							<?php
							}else{
							?>	
								<div style="float : left">
									<i class="fa fa-check-square-o"></i>
								</div>
								<div style="float : left; margin-left : 5px">Pusat</div>
								<div style="float : left; margin-left : 10px">
									<i class="fa fa-square-o"></i>
								</div>
								<div style="float : left; margin-left : 5px">cabang</div>
							<?php
							}
							?>	
							</td>
						</tr>
						<tr>
							<td>6.</td>
							<td>Alamat</td>
							<td><?php echo $value['vendor_address'];?></td>
						</tr>
						<tr>
							<td>7.</td>
							<td>Negara</td>
							<td><?php echo $value['vendor_country'];?></td>
						</tr>
						<tr>
							<td>8.</td>
							<td>Provinsi</td>
							<td><?php echo $value['vendor_province'];?></td>
						</tr>
						<tr>
							<td>9.</td>
							<td>Kota</td>
							<td><?php echo $value['vendor_city'];?></td>
						</tr>
						<tr>
							<td>10.</td>
							<td>No. Telp</td>
							<td><?php echo $value['vendor_phone'];?></td>
						</tr>
						<tr>
							<td>11.</td>
							<td>Fax</td>
							<td><?php echo $value['vendor_fax'];?></td>
						</tr>
						<tr>
							<td>12.</td>
							<td>Email</td>
							<td><?php echo $value['vendor_email'];?></td>
						</tr>
						<tr>
							<td>13.</td>
							<td>Website</td>
							<td><?php echo $value['vendor_website'];?></td>
						</tr>
						<?php }} ?>
					</tbody>
				</table>			
			</div>
			<div class="content">	
				<table class="recap-table" border="0" cellpadding="0" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th colspan="7" align="left">Akta Pendirian/Perubahan Perusahaan</th>
						</tr>
						<tr>
							<th>No. Akta</th>
							<th>Notaris</th>
							<th>Tanggal</th>
							<th>Lembaga Pengesah</th>
							<th>No. Pengesahan	</th>
							<th>Tanggal Pengesahan</th>
							<th>Keterangan</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php echo $value['no'];?></td>
							<td><?php echo $value['notaris'];?></td>
							<td><?php echo default_date($value['issue_date']);?></td>
							<td><?php echo $value['authorize_by'];?></td>
							<td><?php echo $value['authorize_no'];?></td>
							<td><?php echo default_date($value['authorize_date']);?></td>
							<td>pendirian</td>
						</tr>
					</tbody>
				</table>			
			</div>
		</div>
		<div class="container">
			<div class="header">
				<table border="0" cellpadding="0" cellspacing="0">
					<tbody><tr>
						<td width="250"><img src="<?php echo base_url('assets/images/login-regas-logo.jpg');?>" height="70"></td>
						<td valign="bottom">
							<div style="font-size : 14px">Aplikasi Management Penyedia Barang &amp; Jasa</div>
							<div style="font-size : 11px"><i>Laporan Data Penyedia Barang &amp; Jasa - Pengurus Perusahaan</i></div>
						</td>
						<td align="right" valign="bottom">
							<div>Dicetak pada tanggal : <?php echo date("d-m-Y h:i") ?></div>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="5"></td>
					</tr>
					<tr>
						<td colspan="3">
							<div style="border-bottom : 2px solid #000; border-top : 1px solid #000; height : 2px"></div>
						</td>
					</tr>
				</tbody></table>
			</div>
						<div class="content">
				
<table class="recap-table" border="0" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th colspan="5" align="left">Pengurus Perusahaan</th>
		</tr>
		<tr>
			<th>Nama</th>
			<th>Nomor Identitas (KTP/Passport/KITAS)</th>
			<!-- <th>Masa Berlaku</th> -->
			<th>Jabatan</th>
			<!-- <th>Keterangan</th> -->
		</tr>
	</thead>
	<tbody>
		<?php foreach ($pengurus as $key => $value) {?>
		<tr>
			<td><?php echo $value['name'];?></td>
			<td><?php echo $value['no'];?></td>
			<td><?php echo $value['position'];?></td>
		</tr>
		<?php }?>
	</tbody>
</table>			</div>
					</div>
				<div class="container">
			<div class="header">
				<table border="0" cellpadding="0" cellspacing="0">
					<tbody><tr>
						<td width="250"><img src="<?php echo base_url('assets/images/login-regas-logo.jpg');?>" height="70"></td>
						<td valign="bottom">
							<div style="font-size : 14px">Aplikasi Management Penyedia Barang &amp; Jasa</div>
							<div style="font-size : 11px"><i>Laporan Data Penyedia Barang &amp; Jasa - SIUP, Surat Ijin Usaha Lainnya</i></div>
						</td>
						<td align="right" valign="bottom">
							<div>Dicetak pada tanggal : <?php echo date("d-m-Y h:i") ?></div>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="5"></td>
					</tr>
					<tr>
						<td colspan="3">
							<div style="border-bottom : 2px solid #000; border-top : 1px solid #000; height : 2px"></div>
						</td>
					</tr>
				</tbody></table>
			</div>
						<div class="content">

<?php
	// if(count($surat_izin)){
	// foreach($surat_izin as $row => $value_siup){
?>		
<table class="recap-table" border="0" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th colspan="6" align="left">SIUP</th>
		</tr>
		<tr>
			<th>No.</th>
			<th>Tanggal</th>
			<th>Kualifikasi</th>
			<th>Masa Berlaku</th>
			<th>Klasifikasi</th>
			<th>Bidang/Sub-Bidang</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if(isset($surat_izin['siup'])){
		foreach($surat_izin['siup'] as $rowsiup => $izin_data){
		?>
		<tr>
			<td><?php echo $izin_data['no'];?></td>
			<td><?php echo default_date($izin_data['issue_date']);?></td>
			<td><?php echo $izin_data['qualification'];?></td>
			<td><?php echo ($izin_data['expire_date'] == 'lifetime')? 'Seumur Hidup':default_date($izin_data['expire_date']);?></td>
			<td><?php echo $klasifikasi[$izin_data['id_dpt_type']];?></td>
			<td class="little">
				<?php
				if(isset($izin_data['bsb'])){
					foreach($izin_data['bsb'] as $rowbsb => $valuebsb){
				?>
				<div><?php echo $rowbsb;?></div><!-- bidang -->
					<?php
						foreach($valuebsb as $rowsb => $valuesb){
					?>
						<div style="margin-left : 10px"> - <?php echo $valuesb;?></div><!-- subbidang --> 
					<?php
						}
					?>
				<?php
					}
				}
				?>
			</td>
		</tr>
		<?php
		}
		?>
		<?php
		}
		?>
	</tbody>
</table>
</div>
						<div class="content">
				
<table class="recap-table" border="0" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th colspan="6" align="left">Surat Ijin Usaha Lainnya</th>
		</tr>
		<tr>
			<th>No.</th>
			<th>Tanggal</th>
			<th>Kualifikasi</th>
			<th>Masa Berlaku</th>
			<th>Klasifikasi</th>
			<th>Bidang/Sub-Bidang</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if(isset($surat_izin['ijin_lain'])){
		foreach($surat_izin['ijin_lain'] as $rowijin_lain => $izin_data_lain){
		?>
		<tr>
			<td><?php echo $izin_data_lain['no'];?></td>
			<td><?php echo default_date($izin_data_lain['issue_date']);?></td>
			<td><?php echo $izin_data_lain['qualification'];?></td>
			<td><?php echo ($izin_data['expire_date'] == 'lifetime')? 'Seumur Hidup':default_date($izin_data_lain['expire_date']);?></td>
			<td><?php echo $klasifikasi[$izin_data_lain['id_dpt_type']];?></td>
			<td class="little">
				<?php
					if(isset($izin_data_lain['bsb'])){
					foreach($izin_data_lain['bsb'] as $rowbsb => $valuebsb){
				?>
				<div><?php echo $rowbsb;?></div><!-- bidang -->
					<?php
						foreach($valuebsb as $rowsb => $valuesb){
					?>
						<div style="margin-left : 10px"> - <?php echo $valuesb;?></div><!-- subbidang --> 
					<?php
						}
					?>
				<?php
					}}
				?>
			</td>
		</tr>
		<?php
		}
		?>
		<?php
		}
		?>
	</tbody>
</table>			</div>
					</div>
				<div class="container">
			<div class="header">
				<table border="0" cellpadding="0" cellspacing="0">
					<tbody><tr>
						<td width="250"><img src="<?php echo base_url('assets/images/login-regas-logo.jpg');?>" height="70"></td>
						<td valign="bottom">
							<div style="font-size : 14px">Aplikasi Management Penyedia Barang &amp; Jasa</div>
							<div style="font-size : 11px"><i>Laporan Data Penyedia Barang &amp; Jasa - SITU/Keterangan Domisili, TDP</i></div>
						</td>
						<td align="right" valign="bottom">
							<div>Dicetak pada tanggal : <?php echo date("d-m-Y h:i") ?></div>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="5"></td>
					</tr>
					<tr>
						<td colspan="3">
							<div style="border-bottom : 2px solid #000; border-top : 1px solid #000; height : 2px"></div>
						</td>
					</tr>
				</tbody></table>
			</div>
						<div class="content">
				
<table class="recap-table" border="0" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th colspan="5" align="left">SITU/Keterangan Domisili</th>
		</tr>
		<tr>
			<th>No.</th>
			<th>Tanggal</th>
			<th>Alamat</th>
			<th>Masa Berlaku</th>
			<th>Klasifikasi</th>
		</tr>
	</thead>
	<tbody>
		<?php
		 foreach ($situ as $rowsitu => $valuesitu) {
		?>
		<tr>
			<td><?php echo $valuesitu['no']; ?></td>
			<td><?php echo default_date($valuesitu['issue_date']); ?></td>
			<td><?php echo $valuesitu['address']; ?></td>
			<td><?php echo ($valuesitu['expire_date'] == 'lifetime')?'Seumur Hidup':default_date($valuesitu['expire_date']); ?></td>
			<td><?php // echo $klasifikasi[$valuesitu['id_dpt_type']]; ?></td>
		</tr>
		<?php
		 }
		?>
	</tbody>
</table>			
	</div>
	<div class="content">
				
<table class="recap-table" border="0" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th colspan="4" align="left">TDP</th>
		</tr>
		<tr>
			<th>No.</th>
			<th>Tanggal</th>
			<th>Masa Berlaku</th>
			<th>Klasifikasi</th>
		</tr>
	</thead>
	<tbody>
		<?php
		 foreach ($tdp as $rowtdp => $valuetdp) {
		?>
		<tr>
			<td><?php echo $valuetdp['no']; ?></td>
			<td><?php echo default_date($valuetdp['issue_date']); ?></td>
			<td><?php echo ($valuetdp['expiry_date'] == 'lifetime')?'Seumur Hidup':default_date($valuetdp['expiry_date']); ?></td>
			<td><?php //echo $klasifikasi[$valuetdp['id_dpt_type']];?></td>
		</tr>
		<?php
		 }
		?>
	</tbody>
</table>			</div>
					</div>
				<div class="container">
			<div class="header">
				<table border="0" cellpadding="0" cellspacing="0">
					<tbody><tr>
						<td width="250"><img src="<?php echo base_url('assets/images/login-regas-logo.jpg');?>" height="70"></td>
						<td valign="bottom">
							<div style="font-size : 14px">Aplikasi Management Penyedia Barang &amp; Jasa</div>
							<div style="font-size : 11px"><i>Laporan Data Penyedia Barang &amp; Jasa - Pabrikan/Keagenan/Distributor, Sertifikat Asosiasi/Lainnya</i></div>
						</td>
						<td align="right" valign="bottom">
							<div>Dicetak pada tanggal : <?php echo date("d-m-Y h:i") ?></div>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="5"></td>
					</tr>
					<tr>
						<td colspan="3">
							<div style="border-bottom : 2px solid #000; border-top : 1px solid #000; height : 2px"></div>
						</td>
					</tr>
				</tbody></table>
			</div>
						<div class="content">
				
					<table class="recap-table" border="0" cellpadding="0" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th colspan="5" align="left">Pabrikan/Keagenan/Distributor</th>
							</tr>
							<tr>
								<th>No.</th>
								<th>Tanggal</th>
								<th>Pabrikan/Keagenan/Distributor</th>
								<th>Masa Berlaku</th>
								<th>Produk/Merk</th>
							</tr>
						</thead>
						<tbody>
							<?php
							 foreach ($keagenan as $rowagen => $valueagen) {
							?>
							<tr>
								<td><?php echo $valueagen['no']; ?></td>
								<td><?php echo default_date($valueagen['issue_date']); ?></td>
								<td><?php echo $valueagen['type']; ?></td>
								<td><?php echo default_date($valueagen['expire_date']); ?></td>
								<td><?php echo $valueagen['produk']; ?> / <?php echo $valueagen['merk']; ?></td>
							</tr>
							<?php
							 }
							?>
						</tbody>
					</table>			</div>
						<div class="content">
				
<table class="recap-table" border="0" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th colspan="7" align="left">Sertifikat Asosiasi/Lainnya</th>
		</tr>
		<tr>
			<th>Lembaga Penerbit</th>
			<th>No.</th>
			<th>Tanggal</th>
			<th>Masa Berlaku</th>
			<th>Klasifikasi</th>
			<th>Bidang/Sub-Bidang</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if(isset($surat_izin['asosiasi'])){
			// print_r($surat_izin['asosiasi']);
		foreach($surat_izin['asosiasi'] as $rowasosiasi => $izin_asosiasi){
		?>
		<tr>
			<td><?php echo $izin_asosiasi['authorize_by'];?></td>
			<td><?php echo $izin_asosiasi['no'];?></td>
			<td><?php echo default_date($izin_asosiasi['issue_date']);?></td>
			<td><?php echo ($izin_asosiasi['expire_date'] != 'lifetime') ?  default_date($izin_asosiasi['expire_date']) : "Seumur Hidup";;?></td>
			<!--<td><?php echo $izin_asosiasi['qualification'];?></td>-->
			<td><?php echo $klasifikasi[$izin_asosiasi['id_dpt_type']];?></td>
			<td class="little">
				<?php
					if(isset($izin_asosiasi['bsb'])){
					foreach($izin_asosiasi['bsb'] as $rowasosiasi => $valueasosiasi){
				?>
				<div><?php echo $rowasosiasi;?></div><!-- bidang -->
					<?php
						foreach($valueasosiasi as $rowsb => $valuesb){
					?>
						<div style="margin-left : 10px"> - <?php echo $valuesb;?></div><!-- subbidang --> 
					<?php
						}
					?>
				<?php
					}}
				?>
			</td>
		</tr>
		<?php
		}}
		?>
	</tbody>
</table>			</div>
					</div>
				<div class="container">
			<div class="header">
				<table border="0" cellpadding="0" cellspacing="0">
					<tbody><tr>
						<td width="250"><img src="<?php echo base_url('assets/images/login-regas-logo.jpg');?>" height="70"></td>
						<td valign="bottom">
							<div style="font-size : 14px">Aplikasi Management Penyedia Barang &amp; Jasa</div>
							<div style="font-size : 11px"><i>Laporan Data Penyedia Barang &amp; Jasa - SIUJK, SBU</i></div>
						</td>
						<td align="right" valign="bottom">
							<div>Dicetak pada tanggal : <?php echo date("d-m-Y h:i") ?></div>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="5"></td>
					</tr>
					<tr>
						<td colspan="3">
							<div style="border-bottom : 2px solid #000; border-top : 1px solid #000; height : 2px"></div>
						</td>
					</tr>
				</tbody></table>
			</div>
						<div class="content">
				
<table class="recap-table" border="0" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th colspan="6" align="left">SIUJK</th>
		</tr>
		<tr>
			<th>No.</th>
			<th>Tanggal</th>
			<th>Kualifikasi</th>
			<th>Masa Berlaku</th>
			<th>Klasifikasi</th>
			<th>Bidang/Sub-Bidang</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if(isset($surat_izin['siujk'])){
		foreach($surat_izin['siujk'] as $rowsiujk => $izin_siujk){
		?>
		<tr>
			<td><?php echo $izin_siujk['no'];?></td>
			<td><?php echo default_date($izin_siujk['issue_date']);?></td>
			<td><?php echo $izin_siujk['qualification'];?></td>
			<td><?php echo default_date($izin_siujk['expire_date']);?></td>
			<td><?php echo $klasifikasi[$izin_siujk['id_dpt_type']];?></td>
			<td class="little">
				<?php
					if(isset($izin_siujk['bsb'])){
					foreach($izin_siujk['bsb'] as $rowsiujkbsb => $valuesiujksb){
				?>
				<div><?php echo $rowsiujkbsb;?></div><!-- bidang -->
					<?php
						foreach($valuesiujksb as $rowsb => $valuesb){
					?>
						<div style="margin-left : 10px"> - <?php echo $valuesb;?></div><!-- subbidang --> 
					<?php
						}
					?>
				<?php
					}}
				?>
			</td>
		</tr>
		<?php
		}}
		?>
	</tbody>
</table>			</div>
						<div class="content">
				
<table class="recap-table" border="0" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th colspan="6" align="left">SBU</th>
		</tr>
		<tr>
			<th>Anggota Asosiasi</th>
			<th>No.</th>
			<th>Tanggal</th>
			<th>Masa Berlaku</th>
			<th>Klasifikasi</th>
			<th>Bidang/Sub-Bidang</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if(isset($surat_izin['sbu'])){
		foreach($surat_izin['sbu'] as $rowsbu => $izin_sbu){
		?>
		<tr>
			<td><?php echo $izin_sbu['no'];?></td>
			<td><?php echo default_date($izin_sbu['issue_date']);?></td>
			<td><?php echo default_date($izin_sbu['expire_date']);?></td>
			<td><?php echo $izin_sbu['qualification'];?></td>
			<td><?php echo $izin_sbu['klasifikasi'];?></td>
			<td class="little">
				<?php
					if(isset($izin_sbu['bsb'])){
					foreach($izin_sbu['bsb'] as $rowsbubsb => $valuesbusb){
				?>
				<div><?php echo $rowsbubsb;?></div><!-- bidang -->
					<?php
						foreach($valuesbusb as $rowsb => $valuesb){
					?>
						<div style="margin-left : 10px"> - <?php echo $valuesb;?></div><!-- subbidang --> 
					<?php
						}
					?>
				<?php
					}}
				?>
			</td>
		</tr>
		<?php
		}}
		?>
	</tbody>
</table>			</div>
					</div>
				<div class="container">
			<div class="header">
				<table border="0" cellpadding="0" cellspacing="0">
					<tbody><tr>
						<td width="250"><img src="<?php echo base_url('assets/images/login-regas-logo.jpg');?>" height="70"></td>
						<td valign="bottom">
							<div style="font-size : 14px">Aplikasi Management Penyedia Barang &amp; Jasa</div><!-- xxx -->
							<div style="font-size : 11px"><i>Laporan Data Penyedia Barang &amp; Jasa - Persyaratan Aspek K3</i></div>
						</td>
						<td align="right" valign="bottom">
							<div>Dicetak pada tanggal : <?php echo date("d-m-Y h:i") ?></div>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="5"></td>
					</tr>
					<tr>
						<td colspan="3">
							<div style="border-bottom : 2px solid #000; border-top : 1px solid #000; height : 2px"></div>
						</td>
					</tr>
				</tbody></table>
			</div>
	<?php if(count($csms_limit)){?>
	<?php // print_r($csms_limit);?>
	<div class="content">
		<table class="recap-table" border="0" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="33%">Klasifikasi</th>
					<th width="33%">Skor</th>
					<th width="33%">Masa Berlaku</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($csms_limit as $aspek_k3 => $value_k3){?>
				<tr>
					<td><?php echo $value_k3['value'];?></td>
					<td align="center"><?php echo $value_k3['score'];?></td>
					<td><?php echo ($value_k3['expiry_date'] != "") ? default_date($value_k3['expiry_date']) : "-";?></td>
				</tr>
				<?php }?>
			</tbody>
		</table>			
	</div>
	<?php }?>
					</div>
				<div class="container">
			<div class="header">
				<table border="0" cellpadding="0" cellspacing="0">
					<tbody><tr>
						<td width="250"><img src="<?php echo base_url('assets/images/login-regas-logo.jpg');?>" height="70"></td>
						<td valign="bottom">
							<div style="font-size : 14px">Aplikasi Management Penyedia Barang &amp; Jasa</div>
							<div style="font-size : 11px"><i>Laporan Data Penyedia Barang &amp; Jasa - Pengalaman</i></div>
						</td>
						<td align="right" valign="bottom">
							<div>Dicetak pada tanggal : <?php echo date("d-m-Y h:i") ?></div>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="5"></td>
					</tr>
					<tr>
						<td colspan="3">
							<div style="border-bottom : 2px solid #000; border-top : 1px solid #000; height : 2px"></div>
						</td>
					</tr>
				</tbody></table>
			</div>
						<div class="content">
				
<table class="recap-table" border="0" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th colspan="13" align="left">Pengalaman</th>
		</tr>
		<tr>
			<th>Nama Paket Pekerjaan</th>
			<!-- <th>Bidang Pekerjaan</th>
			<th>Sub Bidang Pekerjaan</th>
			<th>Klasifikasi</th> -->
			<th>Lokasi</th>
			<th>Pemberi Tugas</th>
			<th>No. Telp Pemberi Tugas</th>
			<th>No. Kontrak</th>
			<th>Tanggal Kontrak</th>
			<th>Nilai Kontrak</th>
			<th>Nilai Kontrak <i style="font-size : 10px">(dalam mata uang asing)</i></th>
			<th>Tanggal Selesai Sesuai Kontrak</th>
			<!-- <th>Tanggal Selesai Sesuai BAST</th> -->
		</tr>
	</thead>
	<tbody>
		<?php
		// print_r($pengalaman);
			if(count($pengalaman)){
				// print_r($pengalaman);
				foreach ($pengalaman as $rowp => $valuep) {
		?>
		<tr>
			<td><?php echo $valuep['job_name']; ?></td>
			<!-- <td><?php //echo $valuep['']; ?></td>
			<td><?php //echo $valuep['']; ?></td> 
			<td>Badan Usaha non-Konstruksi / Pengadaan Barang</td>-->
			<td><?php echo $valuep['job_location']; ?></td>
			<td><?php echo $valuep['job_giver']; ?></td>
			<td><?php echo $valuep['phone_no']; ?></td>
			<td><?php echo $valuep['contract_no']; ?></td>
			<td><?php echo ((strtotime($valuep['contract_start'])) > 0) ? default_date($valuep['contract_start']) :"-"; ?></td>
			<td><?php echo "Rp. ".number_format($valuep['price_idr']); ?></td>
			<td><?php echo $valuep['currency']; ?>&nbsp;<?php echo number_format($valuep['price_foreign']); ?></td>
			<td><?php echo default_date($valuep['contract_end']); ?></td>
			<!-- <td><?php echo default_date($valuep['bast_date']); ?></td> -->
		</tr>
		<?php
			}}
		?>
	</tbody>
</table>			
</div>
					</div>
