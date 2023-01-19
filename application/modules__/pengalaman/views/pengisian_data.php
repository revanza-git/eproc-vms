<!--<div class="progressBar">
	<ul>
		<li>
			Izin Usaha
		</li>
		<li>
			Bidang/Sub-bidang Pekerjaan
		</li>
		<li class="active">
			Pengisian Data
		</li>
	</ul>
</div>-->
<h2>Pengisian Data Pengalaman</h2>
<div class="formDashboard">
	<form method="POST" enctype="multipart/form-data">
		<table style="width:700px;">
			<tr class="input-form">
				<td><label>Nama Paket Pekerjaan*</label></td>
				<td>
					<input type="text" name="job_name" value="<?php echo $this->form->get_temp_data('job_name');?>">
					<?php echo form_error('job_name'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Lokasi*</label></td>
				<td>
					<input type="text" name="job_location" value="<?php echo $this->form->get_temp_data('job_location');?>">
					<?php echo form_error('job_location'); ?>
				</td>
			</tr>
			<!--<tr class="input-form">
				<td><label>Satuan Unit/Kerja</label></td>
				<td>
					<?php echo form_dropdown('id_sbu', $sbu, $this->form->get_temp_data('id_sbu'));?>
					
					<?php echo form_error('id_sbu'); ?>
				</td>
			</tr>-->
			<tr class="input-form">
				<td><label>Nama Instansi Pemberi Tugas*</label></td>
				<td>
					<input type="text" name="job_giver" value="<?php echo $this->form->get_temp_data('job_giver');?>">
					<?php echo form_error('job_giver'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>No. Telp Pemberi Tugas</label></td>
				<td>
					<input type="text" name="phone_no" value="<?php echo $this->form->get_temp_data('phone_no');?>">
					<?php echo form_error('phone_no'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>No. Kontrak*</label></td>
				<td>
					<input type="text" name="contract_no" value="<?php echo $this->form->get_temp_data('contract_no');?>">
					<?php echo form_error('contract_no'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tanggal Kontrak*</label></td>
				<td>
					<?php echo $this->form->calendar(array('name'=>'contract_start','value'=>$this->form->get_temp_data('contract_start')), false);?> - <?php echo $this->form->calendar(array('name'=>'contract_end','value'=>$this->form->get_temp_data('contract_end')), false);?>
					<?php echo form_error('contract_start'); ?>
					<?php echo form_error('contract_end'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Nilai Kontrak</label>
					<p class="notifReg"><i>( Dalam Rupiah )</i></p>
				</td>
				<td>
					<span>Rp.&nbsp;&nbsp;</span><input class="money-masked" type="text" name="price_idr" style="width:100px" value="<?php echo $this->form->get_temp_data('price_idr');?>">
					<?php echo form_error('price_idr'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Nilai Kontrak</label>
					<p class="notifReg"><i>( Dalam Mata Uang Asing )</i></p>
				</td>
				<td>
					<?php echo form_dropdown('currency', $curr, $this->form->get_temp_data('currency'));?>
					<input type="text" class="money-masked" name="price_foreign" style="width:100px" value="<?php echo $this->form->get_temp_data('price_foreign');?>">
					<?php echo form_error('price_foreign'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Lampiran (Dokumen kontrak)*</label></td>
				<td>
					<?php echo $this->form->file(array('name'=>'contract_file','value'=>($this->form->get_temp_data('contract_file'))?$this->form->get_temp_data('contract_file'):$contract_file));?>
					<?php //echo form_error('contract_file'); ?>
				</td>
			</tr>
			<!--
			<tr class="input-form">
				<td><label>Tanggal Selesai sesuai BAST</label></td>
				<td>
					<?php echo $this->form->calendar(array('name'=>'bast_date','value'=>$this->form->get_temp_data('bast_date')), false);?>
					<?php echo form_error('bast_date'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Lampiran (Dokumen BAST)</label></td>
				<td>
					<input type="file" name="bast_file" value="<?php echo $this->form->get_temp_data('bast_file');?>">
					<?php echo form_error('bast_file'); ?>
				</td>
			</tr>
			-->

		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="simpan">
			<!-- <input type="submit" value="Kembali" class="btnBlue" name="back"> -->
		</div>
	</form>
</div>