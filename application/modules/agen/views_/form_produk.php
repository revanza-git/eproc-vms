<div class="formDashboard">
	<h1 class="formHeader">Tambah Produk</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Produk*</label></td>
				<td>
					<input type="text" name="produk" value="<?php echo $this->form->get_temp_data('produk');?>">
					<?php echo form_error('produk'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Merk*</label></td>
				<td>
					<input type="text" name="merk" value="<?php echo $this->form->get_temp_data('merk');?>">
					<?php echo form_error('merk'); ?>
				</td>
			</tr>
			
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="simpan">
		</div>
	</form>
</div>