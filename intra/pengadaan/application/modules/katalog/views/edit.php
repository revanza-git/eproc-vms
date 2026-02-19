<div class="formDashboard">
	<h1 class="formHeader">Ubah Data <?php echo $category?></h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<!-- <?php // foreach ($data as $key => $value) {?> -->
			<tr class="input-form">
				<td><label>Nama Barang*</label></td>
				<td>
					<input type="text" name="nama" value="<?php echo ($this->form->get_temp_data('nama'))?$this->form->get_temp_data('nama'):$nama;?>" >
					<?php echo form_error('nama'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Keterangan*</label></td>
				<td>
					<textarea name="remark"><?php echo ($this->form->get_temp_data('remark'))?$this->form->get_temp_data('remark'):$remark;?></textarea>
					<?php echo form_error('remark'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Kategori</label></td>
				<td>
					<?php echo strtoupper($category);?>
					<input type=hidden value="<?php echo $category;?>" name="category">
					<?php
						// $options = array(
						//                   ''  		=> '--Pilih Kategori--',
						//                   'barang' 	=> 'Barang',
						//                   'jasa'   	=> 'Jasa',
						//                 );

						// echo form_dropdown('category', $options, ($this->form->get_temp_data('category'))?$this->form->get_temp_data('category'):$category);
					?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Pengguna Barang/Jasa*</label></td>
				<td>
					<select name="for" id="">
						<?php foreach ($for as $key => $value) { ?>
							<option value="<?php echo $key; ?>" <?php $_for = (($v_for == $key) ? 'selected' : ''); echo $_for; ?>><?php echo $value; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Mata Uang*</label></td>
				<td>
					<?php echo $this->form->get_kurs(array('name'=>'id_kurs'),($this->form->get_temp_data('id_kurs'))?$this->form->get_temp_data('id_kurs'):$id_kurs)?>
					<?php echo form_error('id_kurs'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Foto*</label></td>
				<td>
					<?php echo $this->form->file(array('name'=>'gambar_barang','value'=>($this->form->get_temp_data('gambar_barang'))?$this->form->get_temp_data('gambar_barang'):$gambar_barang));?>
					<?php echo form_error('gambar_barang'); ?>
				</td>
			</tr>
			<?php //}?>
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Simpan">
		</div>
	</form>
</div>

