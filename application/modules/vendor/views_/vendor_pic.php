<?php echo $this->session->flashdata('msgSuccess')?>
<?php echo $this->session->flashdata('msgError')?>
<div class="formDashboard">
	<h1 class="formHeader">Data Akun</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Nama*</label></td>
				<td>
					<input type="text" name="pic_name" value="<?php echo (isset($pic_name)?$pic_name:$this->form->get_temp_data('pic_name'));?>">
					<?php echo form_error('pic_name'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Jabatan*</label></td>
				<td>
					<input type="text" name="pic_position" value="<?php echo (isset($pic_position)?$pic_position:$this->form->get_temp_data('pic_position'));?>">
					<?php echo form_error('pic_position'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>No. Telp*</label></td>
				<td>
					<input type="text" name="pic_phone" value="<?php echo (isset($pic_phone)?$pic_phone:$this->form->get_temp_data('pic_phone'));?>">
					<?php echo form_error('pic_phone'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Email*</label></td>
				<td>
					<input type="text" name="pic_email" value="<?php echo (isset($pic_email)?$pic_email:$this->form->get_temp_data('pic_email'));?>">
					<?php echo form_error('pic_email'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Alamat*</label></td>
				<td>
					<input type="text" name="pic_address" value="<?php echo (isset($pic_address)?$pic_address:$this->form->get_temp_data('pic_address'));?>">
					<?php echo form_error('pic_address'); ?>
				</td>
			</tr>
		</table>
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Update">
		</div>
	</form>
</div>