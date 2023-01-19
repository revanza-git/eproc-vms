<?php echo $this->session->flashdata('msgSuccess')?>
<div class="formDashboard">
	<h1 class="formHeader">Administrasi</h1>
	
		<table>
			<tr class="input-form">
				<td><label>Lokasi Pendaftaran</label></td>
				<td>
					: <?php echo $sbu_name;?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Badan Hukum</label></td>
				<td>
					: <?php echo $legal_name;?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Nama Badan Usaha</label></td>
				<td>
					: <?php echo $name;?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>NPWP*</label></td>
				<td>
					: <?php echo $npwp_code;?>
				</td>
			</tr>
			<!--
			<tr class="input-form">
				<td><label>Tanggal Pengukuhan</label></td>
				<td>
					<?php echo $npwp_date?>
				</td>
			</tr>-->
			<tr class="input-form">
				<td><label>Lampiran*</label></td>
				<td>
					: <a href="<?php echo base_url('lampiran/npwp_file/'.$npwp_file)?>"><?php echo $npwp_file;?></a>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>NPPKP*</label></td>
				<td>
					: <?php echo $nppkp_code;?>
				</td>
			</tr>
			<!--
			<tr class="input-form">
				<td><label>Tanggal Pengukuhan</label></td>
				<td>
					<?php echo $nppkp_date;?>
				</td>
			</tr>-->
			<tr class="input-form">
				<td><label>Lampiran*</label></td>
				<td>
					: <a href="<?php echo base_url('lampiran/nppkp_file/'.$nppkp_file)?>"><?php echo $nppkp_file;?></a>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Status</label></td>
				<td>
					: <?php echo $vendor_office_status;?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Alamat</label></td>
				<td>
					: <?php echo $vendor_address;?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Negara</label></td>
				<td>
					: <?php echo $vendor_country; ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>No Telp</label></td>
				<td>
				: <?php echo $vendor_phone; ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Provinsi</label></td>
				<td>
					: <?php echo $vendor_province; ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Fax</label></td>
				<td>
					: <?php echo $vendor_fax; ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Kota</label></td>
				<td>
					: <?php echo $vendor_city; ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Email</label></td>
				<td>
					: <?php echo $vendor_email; ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Kode Pos</label></td>
				<td>
					: <?php echo $vendor_postal; ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Website</label></td>
				<td>
					: <?php echo $vendor_website; ?>
				</td>
			</tr>
		</table>
		<div class="buttonRegBox clearfix">
			<a href="<?php echo site_url('administrasi/edit');?>" class="btnBlue">Edit</a>
		</div>

</div>