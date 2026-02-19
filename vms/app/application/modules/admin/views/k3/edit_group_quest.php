<?php foreach ($quest as $key => $value) {?>
<div class="formDashboard">
	<h1 class="formHeader">Edit Evaluasi</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Pilih Header Untuk Evaluasi* :</label></td>
				<td style="width: 500px;">
					<?php echo form_dropdown('id_ms_header', $header,$value["id_ms_header"]);?>
					<?php echo form_error('id_ms_header'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Pilih Sub Header Untuk Evaluasi* :</label></td>
				<td style="width: 500px;">
					<?php echo form_dropdown('id_sub_header', $sub_header ,$value["id_sub_header"]);?>
					<?php echo form_error('id_sub_header'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Nama Evaluasi* :</label></td>
				<td style="width: 500px;">
					<?php echo form_dropdown('id_evaluasi', $evaluasi ,$value["id_evaluasi"]);?>
					<?php echo form_error('id_evaluasi'); ?>
				</td>
			</tr>
		</table>
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Simpan">
		</div>
	</form>
</div>
<?php }?>