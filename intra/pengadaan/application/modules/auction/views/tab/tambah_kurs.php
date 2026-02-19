<div class="formDashboard">
	<h1>Tambah Kurs</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Pilih Kurs</label></td>
				<td>
					<?php echo form_dropdown('id_kurs', $kurs, $this->form->get_temp_data('id_kurs'),' class="kurs"');?>
					<?php echo form_error('id_kurs'); ?>
				</td>
			</tr>
			<tr class="input-form rate">
				<td><label>Rate</label></td>
				<td>
					<input type="text" name="rate" class="money-masked" value="<?php echo $this->form->get_temp_data('rate');?>">
					<?php echo form_error('rate'); ?>
				</td>
			</tr>
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="simpan">
		</div>
	</form>
</div>