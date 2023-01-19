<div class="registerBlock">
	<h1 class="formHeader">Isian Data Administrasi</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Lokasi Pendaftaran</label></td>
				<td>
					<?php echo form_dropdown('id_sbu', $sbu, $this->form->get_temp_data('id_sbu'),'class="col-14"');?>
					<p class="notifReg">agar diisi sesuai dengan lokasi pengadaan atau lokasi kantor/cabang PGN terdekat</p>
					<?php echo form_error('id_sbu'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Badan Hukum</label></td>
				<td>
					<?php echo form_dropdown('id_legal', $id_legal, $this->form->get_temp_data('id_legal'));?>
					<?php echo form_error('id_legal'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Nama Badan Usaha</label></td>
				<td>
					<input type="text" name="name" value="<?php echo $this->form->get_temp_data('name');?>">
					<?php echo form_error('name'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>NPWP*</label></td>
				<td>
					<input type="text" name="npwp_code" id="npwp" value="<?php echo $this->form->get_temp_data('npwp_code');?>">
					<?php echo form_error('npwp_code'); ?>
				</td>
			</tr>
			<tr class="input-form">
				
				<td><label>Tanggal Pengukuhan</label></td>
				<td>
					<?php echo $this->form->calendar(array('name'=>'npwp_date','value'=>$this->form->get_temp_data('npwp_date')), false);?>
					<?php echo form_error('npwp_date'); ?>
				</td>
			</tr>
			
			<tr class="input-form">
				<td><label>Lampiran</label></td>
				<td>
					<input type="file" name="npwp_file" value="<?php echo $this->form->get_temp_data('npwp_file')?>">
					<?php echo form_error('npwp_file'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>NPPKP*</label></td>
				<td>
					<input type="text" name="nppkp_code" value="<?php echo $this->form->get_temp_data('nppkp_code') ?>">
					<?php echo form_error('nppkp_code'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tanggal Pengukuhan</label></td>
				<td>
					<?php echo $this->form->calendar(array('name'=>'nppkp_date','value'=>$this->form->get_temp_data('nppkp_date')), false);?>
					<?php echo form_error('nppkp_date'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Lampiran</label></td>
				<td>
					<input type="file" name="nppkp_file" value="<?php echo $this->form->get_temp_data('nppkp_file');?>">
					<?php echo form_error('nppkp_file'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Status</label></td>
				<td>
					<label class="lbform">
						<?php echo form_radio(array('name'=>'vendor_office_status'),'pusat',(set_radio('vendor_office_status','pusat')||($this->form->get_temp_data('vendor_office_status')=='pusat'))?TRUE:FALSE)?>Pusat
					</label>
					<label class="lbform">
						<?php echo form_radio(array('name'=>'vendor_office_status'),'cabang',(set_radio('vendor_office_status','cabang')||($this->form->get_temp_data('vendor_office_status')=='cabang'))?TRUE:FALSE)?>Cabang
					</label>
					<?php echo form_error('vendor_office_status'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Alamat</label></td>
				<td>
					<textarea name="vendor_address" cols="40" rows="3"><?php echo $this->form->get_temp_data('vendor_address');; ?></textarea>
					<p class="notifReg">diisi sesuai dengan Surat Keterangan Domisil Perusahaan (SKDP) / Surat Izin Tempat Usaha (SITU)</p>
					<?php echo form_error('vendor_address'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Negara</label></td>
				<td>
					<input type="text" name="vendor_country" value="<?php echo $this->form->get_temp_data('vendor_country'); ?>">
					<?php echo form_error('vendor_country'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>No Telp</label></td>
				<td>
					<input type="text" name="vendor_phone" value="<?php echo $this->form->get_temp_data('vendor_phone'); ?>">
				<?php echo form_error('vendor_phone'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Provinsi</label></td>
				<td>
					<input type="text" name="vendor_province" value="<?php echo  $this->form->get_temp_data('vendor_province'); ?>">
					<?php echo form_error('vendor_province'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Fax</label></td>
				<td>
					<input type="text" name="vendor_fax" value="<?php echo $this->form->get_temp_data('vendor_fax'); ?>">
					<?php echo form_error('vendor_fax'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Kota</label></td>
				<td>
					<input type="text" name="vendor_city" value="<?php echo $this->form->get_temp_data('vendor_city'); ?>">
					<?php echo form_error('vendor_city'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Email*</label></td>
				<td>
					<input type="text" name="vendor_email" value="<?php echo $this->form->get_temp_data('vendor_email'); ?>">
					<p class="notifReg">Wajib diisi dengan alamat email resmi & aktif. Selanjutnya akan digunakan sebagai username untuk login ke aplikasi ini, dan juga semua pemberitahuan dari sistem ini akan masuk ke alamat email tersebut</p>
					<?php echo form_error('vendor_email'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Kode Pos</label></td>
				<td>
					<input type="text" name="vendor_postal" value="<?php echo $this->form->get_temp_data('vendor_postal'); ?>">
					<?php echo form_error('vendor_postal'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Website</label></td>
				<td>
					<input type="text" name="vendor_website" value="<?php echo $this->form->get_temp_data('vendor_website'); ?>">
					<?php echo form_error('vendor_website'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Masukkan text disamping</label></td>
				<td>
					<?php echo $captcha;?>
					<input type="text" name="captcha" value="" >
					<?php echo form_error('captcha'); ?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="buttonRegBox clearfix">
						<input type="submit" value="Next" class="btnBlue" name="next">
						<input type="submit" value="Back" class="btnBlue" name="back">
						
					</div>
				</td>
			</tr>
		</table>
		
		
	</form>
</div>