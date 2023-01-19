<div class="progressBar">
	<ul>
		<li>
			Pilih Bidang
		</li>
		<li class="active">
			Pilih Sub Bidang
		</li>
	</ul>
</div>
<div class="formDashboard">
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Pilih Sub Bidang</label></td>
				<td>
					<div>
					<?php echo form_dropdown('id_sub_bidang', $id_sub_bidang, $this->form->get_temp_data('id_sub_bidang'));?>
					</div>
					<?php echo form_error('id_sub_bidang'); ?>
				</td>
			</tr>
			
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="simpan">
			<input type="submit" value="Kembali" class="btnBlue" name="back">
		</div>
	</form>
</div>