<div class="formDashboard" style="width:auto !important;">
	<h1 class="formHeader">Angkat Kembali Vendor</h1>

	<form method="POST" enctype="multipart/form-data" style="width:500px !important; display:inline-block;">
		<input type="hidden" id="id_blacklist" name="id_blacklist" value="<?php echo  $id_blacklist;?>">
		<table>
			<tr class="input-form">
				<td><label>Nama Penyedia Barang/Jasa*</label></td>
				<td>
					<?php echo $name?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tanggal*</label></td>
				<td >
					<?php echo $this->form->calendar(array('name'=>'white_date','value'=>(isset($white_date)?$white_date:$this->form->get_temp_data('white_date'))),FALSE);?>
					<?php echo form_error('white_date'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Lampiran*</label></td>
				<td>
					<?php echo $this->form->file(array('name'=>'white_file','value'=>(isset($white_file)?$white_file:$this->form->get_temp_data('white_file'))));?>
				</td>
			</tr>
		</table>

		<div class="buttonRegBox clearfix" style="float:left;">
			<input type="submit" value="Simpan" class="btnBlue" name="Simpan">
		</div>
	</form>
</div>
