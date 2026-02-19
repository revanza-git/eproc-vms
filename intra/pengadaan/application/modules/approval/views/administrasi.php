<?php echo $this->session->flashdata('msgSuccess')?>
<?php echo $this->data_process->generate_progress('administrasi',$id_data)?>

<div class="formDashboard">
	<form method="POST">
		<table>
			<tr class="input-form">
				<td><label>Lokasi Pendaftaran</label></td>
				<td>
					<?php echo isset($sbu_name) ? $sbu_name : '-';?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Badan Hukum</label></td>
				<td>
					<?php echo isset($legal_name) ? $legal_name : '-';?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Nama Badan Usaha</label></td>
				<td>
					<?php echo isset($name) ? $name : '-';?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>NPWP*</label></td>
				<td>
					<?php echo isset($npwp_code) ? $npwp_code : '-';?>
				</td>
			</tr>
			<!--<tr class="input-form">
				<td><label>Tanggal Pengukuhan</label></td>
				<td>
					<?php echo isset($npwp_date) ? $npwp_date : '-'?>
				</td>
			</tr>-->
			<tr class="input-form">
				<td><label>Lampiran</label></td>
				<td>
					<?php if (isset($npwp_file) && $npwp_file): ?>
						<a href="<?php echo BASE_LINK_EXTERNAL.('lampiran/npwp_file/'.$npwp_file)?>"><?php echo $npwp_file;?></a>
					<?php else: ?>
						-
					<?php endif; ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>NPPKP*</label></td>
				<td>
					<?php echo isset($nppkp_code) ? $nppkp_code : '-';?>
				</td>
			</tr>
			<!--<tr class="input-form">
				<td><label>Tanggal Pengukuhan</label></td>
				<td>
					<?php echo isset($nppkp_date) ? $nppkp_date : '-';?>
				</td>
			</tr>-->
			<tr class="input-form">
				<td><label>Lampiran</label></td>
				<td>
					<?php if (isset($nppkp_file) && $nppkp_file): ?>
						<a href="<?php echo BASE_LINK_EXTERNAL.('lampiran/nppkp_file/'.$nppkp_file)?>"><?php echo $nppkp_file;?></a>
					<?php else: ?>
						-
					<?php endif; ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Status</label></td>
				<td>
					<?php echo isset($vendor_office_status) ? $vendor_office_status : '-';?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Negara</label></td>
				<td>
					<?php echo isset($vendor_country) ? $vendor_country : '-'; ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>No Telp</label></td>
				<td>
					<?php echo isset($vendor_phone) ? $vendor_phone : '-'; ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Provinsi</label></td>
				<td>
					<?php echo isset($vendor_province) ? $vendor_province : '-';?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Fax</label></td>
				<td>
					<?php echo isset($vendor_fax) ? $vendor_fax : '-';?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Kota</label></td>
				<td>
					<?php echo isset($vendor_city) ? $vendor_city : '-';?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Email</label></td>
				<td>
					<?php echo isset($vendor_email) ? $vendor_email : '-';?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Kodepos</label></td>
				<td>
					<?php echo isset($vendor_postal) ? $vendor_postal : '-';?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Website</label></td>
				<td>
					<?php echo isset($vendor_website) ? $vendor_website : '-';?>
				</td>
			</tr>
		</table>
		<?php
		$admin = $this->session->userdata('admin');
		if ($admin['id_role'] == 1 || $admin['id_role'] == 10 || $admin['id_role'] == 3) {
		?>
		<div class="clearfix">
			<label class="orangeAtt">
				<input type="checkbox" name="mandatory" value="1" <?php echo isset($data_status) ? $this->data_process->set_mandatory($data_status) : '';?>>&nbsp;<i class="fa fa-exclamation-triangle"></i>&nbsp;Mandatory
			</label>
			<div class="clearfix" style="text-align: right">
				<label class="nephritisAtt">
					<input type="radio" name="status" value="1" <?php echo isset($data_status) ? $this->data_process->set_yes_no(1,$data_status) : '';?>>&nbsp;<i class="fa fa-check"></i>&nbsp;OK
				</label>
				<label class="pomegranateAtt">
					<input type="radio" name="status" value="0" <?php echo isset($data_status) ? $this->data_process->set_yes_no(0,$data_status) : '';?>>&nbsp;<i class="fa fa-times"></i>&nbsp;Not OK
				</label>
				<label class="concreteAtt">
					<input type="radio" name="status" value="2" <?php echo isset($data_status) ? $this->data_process->set_yes_no(2,$data_status) : '';?>>&nbsp;<i class="fa fa-minus"></i>&nbsp;Hold
				</label>
			</div>
		</div>
		<div class="buttonRegBox clearfix">
			<a href="<?= base_url('approval/download_surat_pernyataan/' . $id_data) ?>" target="_blank" class="btnBlue"><i class="fa fa-download"></i> Download Surat Pernyataan</a>
			<input type="submit" value="Simpan" class="btnBlue" name="simpan">
		</div>
		<?php } else { ?>
		<div class="buttonRegBox clearfix">
			<a href="<?= base_url('approval/download_surat_pernyataan/' . $id_data) ?>" target="_blank" class="btnBlue"><i class="fa fa-download"></i> Download Surat Pernyataan</a>
		</div>
		<?php } ?>
	</form>
</div>
