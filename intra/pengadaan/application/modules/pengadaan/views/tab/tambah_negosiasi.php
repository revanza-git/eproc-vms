<div class="formDashboard">
	<h1 class="formHeader">Tambah Negosiasi</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Nama Peserta</label></td>
				<td>
					<select name="id_vendor" >
                        <?php foreach ($peserta as $key => $value) { ?>
                            <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                        <?php } ?>
                    </select>
				</td>
			</tr>
            <tr class="input-form">
				<td><label>Nilai Negosiasi</label></td>
				<td>
					<input type="text" name="value" value="<?php echo $this->form->get_temp_data('value');?>">
					<?php echo form_error('value'); ?>
				</td>
			</tr>
            <tr class="input-form">
				<td><label>Nilai Fee</label></td>
				<td>
					<input type="text" name="fee" value="<?php echo $this->form->get_temp_data('fee');?>">
					<?php echo form_error('fee'); ?>
				</td>
			</tr>
            <tr class="input-form">
				<td><label>Keterangan</label></td>
				<td>
					<input type="text" name="remark" value="<?php echo $this->form->get_temp_data('remark');?>">
					<?php echo form_error('remark'); ?>
				</td>
			</tr>
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Simpan">
		</div>
	</form>
</div>