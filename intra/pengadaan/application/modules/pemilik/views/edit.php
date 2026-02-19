<div class="formDashboard">
	<h1 class="formHeader">Edit Susunan Kepemilikan Modal</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>No. Akta Penetapan</label></td>
				<td>
					<?php $selected = ($this->form->get_temp_data('id_akta'))?$this->form->get_temp_data('id_akta'):$id_akta;?>
					<?php echo form_dropdown('id_akta', $id_aktar, $selected);?>
					<?php echo form_error('id_akta');?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Nama*</label></td>
				<td>
					<input type="text" name="name" value="<?php echo ($this->form->get_temp_data('name'))?$this->form->get_temp_data('name'):$name;?>">
					<?php echo form_error('name'); ?>
				</td>
			</tr>
			<!-- 
			<tr class="input-form">
				<td><label>Jabatan*</label></td>
				<td>
					<input type="text" name="position" value="<?php echo $this->form->get_temp_data('position');?>">
					<?php echo form_error('position'); ?>
				</td>
			</tr>
			-->
			<tr class="input-form">
				<td><label>Komposisi Saham (lembar)*</label></td>
				<td>
					<input type="text" name="shares" value="<?php echo ($this->form->get_temp_data('shares'))?$this->form->get_temp_data('shares'):$shares;?>" class="col-14"><span>lembar</span>
					<?php echo form_error('shares'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Komposisi Saham (nominal)*</label></td>
				<td>
					<input class="money-masked" type="text" name="percentage" value="<?php echo ($this->form->get_temp_data('percentage'))?$this->form->get_temp_data('percentage'):$percentage;?>" class="col-14"><span>Rupiah</span>
					<?php echo form_error('percentage'); ?>
				</td>
			</tr>
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Simpan">
		</div>
	</form>
</table>
</div>
	</form>
</div>