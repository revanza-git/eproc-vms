<div class="formDashboard">
	<h1 class="formHeader">Edit Passing Grade K3</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Kriteria* :</label></td>
				<td>
					<input type="text" name="value" value="<?php echo $edit['value']; ?>">
					<?php echo form_error('value'); ?>
				</td>
			</tr>
			
			<tr class="input-form">
				<td><label>Nilai Terendah</label></td>
				<td>
					<input type="number" name="start_score" value="<?php echo $edit['start_score']; ?>">
					<?php echo form_error('start_score'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Nilai Tertinggi</label></td>
				<td>
					<input type="number" name="end_score" value="<?php echo $edit['end_score']; ?>">
					<?php echo form_error('end_score'); ?>
				</td>
			</tr>
			
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Update">
		</div>
	</form>
</div>