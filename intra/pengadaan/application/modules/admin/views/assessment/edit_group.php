<div class="formDashboard">
	<h1 class="formHeader">Edit Group Assessment</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Nama Group* :</label></td>
				<td>
					<input type="text" name="name" value="<?php echo ($this->form->get_temp_data('name'))?$this->form->get_temp_data('name'):$name;;?>">
					<?php echo form_error('name'); ?>
				</td>
			</tr>
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Update">
		</div>
	</form>
</div>