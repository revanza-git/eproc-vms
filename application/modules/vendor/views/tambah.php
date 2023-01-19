<?php echo $this->session->flashdata('msgSuccess')?>
<div class="formDashboard">
	<h1 class="formHeader">Tambah Penyedia Barang &amp; Jasa</h1>
	<form method="POST" enctype="multipart/form-data">
		<table style="width:100%">
			<tr class="input-form">
				<td><label>Nama Badan Usaha</label></td>
				<td>
					<div style="width:270px">
					<?php echo form_dropdown('id_legal', $id_legal, $this->form->get_temp_data('id_legal'),'');?>
					<input type="text" name="name" class="" value="<?php echo $this->form->get_temp_data('name'); ?>">
					</div>
					<?php echo form_error('id_legal'); ?>
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
				<td></td>
				<td>
					<label><input type="hidden" name="is_vms" id="npwp" value="0"><input type="checkbox" name="is_vms" value="1" <?php echo set_checkbox('is_vms', '1'); ?> id="daftarvms"><span>Daftarkan ke VMS</span></label>
					
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Email**</label></td>
				<td>
					<input type="text" name="vendor_email" value="<?php echo $this->form->get_temp_data('vendor_email'); ?>">
					<p class="notifReg">**Jika tidak didaftarkan ke dalam VMS, maka isian email hanya untuk login saat auction. Isilah dengan alamat email resmi & aktif. Selanjutnya akan digunakan sebagai username untuk login ke aplikasi ini, dan juga semua pemberitahuan dari sistem ini akan masuk ke alamat email tersebut</p>
					<?php echo form_error('vendor_email'); ?>
				</td>
			</tr>
			
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Simpan">
		</div>
	</form>
</div>