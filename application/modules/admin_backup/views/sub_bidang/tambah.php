<div class="formDashboard">
	<h1 class="formHeader">Tambah Sub-Bidang</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Pilih Bidang</label></td>
				<td>
					<?php echo form_dropdown('id_bidang', $role, $this->form->get_temp_data('id_bidang'),'');?>
					<?php echo form_error('id_bidang'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Nama Sub Bidang* :</label></td>
				<td>
					<input type="text" name="name" value="<?php echo $this->form->get_temp_data('name');?>">
					<?php echo form_error('name'); ?>
				</td>
			</tr>			
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Simpan">
		</div>
	</form>
</div>