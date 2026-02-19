<div class="formDashboard">
	<h1 class="formHeader">Tambah Proses Pengadaan</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Judul*</label></td>
				<td>
					<input type="text" name="value" value="<?php echo $this->form->get_temp_data('value');?>">
					<?php echo form_error('value'); ?>
				</td>
			</tr>
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Simpan">
		</div>
	</form>
</div>