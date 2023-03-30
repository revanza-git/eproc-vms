<?php echo $this->session->flashdata('msgSuccess')?>
<?php echo $this->session->flashdata('msgError')?>
<div class="formDashboard">
	<h1 class="formHeader">Ubah Password</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Password Lama*</label></td>
				<td>
					<input type="password" name="old_password" value="<?php echo (isset($old_password)?$old_password:$this->form->get_temp_data('old_password'));?>">
					<?php echo form_error('old_password'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Password Baru*</label></td>
				<td>
					<input id="new_password" type="password" name="new_password" value="<?php echo (isset($new_password)?$new_password:$this->form->get_temp_data('new_password'));?>">
					<?php echo form_error('new_password'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Konfirmasi Password*</label></td>
				<td>
					<input type="password" name="conf_password" value="<?php echo (isset($conf_password)?$conf_password:$this->form->get_temp_data('conf_password'));?>">
					<?php echo form_error('conf_password'); ?>
				</td>
			</tr>
		</table>
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Update">
		</div>
	</form>
</div>