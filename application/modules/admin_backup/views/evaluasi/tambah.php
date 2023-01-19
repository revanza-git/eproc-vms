<div class="formDashboard">
	<h1 class="formHeader">Tambah Evaluasi</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Pilih Header Untuk Evaluasi* :</label></td>
				<td style="width: 500px;">
					<?php echo form_dropdown('id_ms_quest', $header, $this->form->get_temp_data('id_ms_quest'),'');?>
					<?php echo form_error('id_ms_quest'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Nama Evaluasi* :</label></td>
				<td style="width: 500px;">
					<input type="text" name="name" placeholder="Nama Evaluasi">
					<?php echo form_error('name'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Nilai* :</label></td>
				<td style="width: 125px;">
					<input style="width: 125px;" type="number" name="point_a" placeholder="Point Pertama">
					<?php echo form_error('point_a'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td></td>
				<td style="width: 125px;">
					<input style="width: 125px;" type="number" name="point_b" placeholder="Point Kedua">
					<?php echo form_error('point_b'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td></td>
				<td style="width: 125px;">
					<input style="width: 125px;" type="number" name="point_c" placeholder="Point Ketiga">
					<?php echo form_error('point_c'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td></td>
				<td style="width: 125px;">
					<input style="width: 125px;" type="number" name="point_d" placeholder="Point Keempat">
					<?php echo form_error('point_d'); ?>
				</td>
			</tr>
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Simpan">
		</div>
	</form>
</div>