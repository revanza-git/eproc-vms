<div class="formDashboard">
	<h1 class="formHeader">Tambah Data <?php echo $category?></h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Nama  <?php echo $category?>*</label></td>
				<td>
					<input type="text" name="nama" value="<?php echo (isset($nama)?$nama:$this->form->get_temp_data('nama'));?>" >
					<?php echo form_error('nama'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Keterangan</label></td>
				<td>
					<textarea name="remark"><?php echo (isset($remark)?$remark:$this->form->get_temp_data('remark'));?></textarea>
					<?php echo form_error('remark'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Kategori</label></td>
				<td>
					<?php echo strtoupper($category);?>
					<input type=hidden value="<?php echo $category;?>" name="category">
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Mata Uang*</label></td>
				<td>
					<?php echo $this->form->get_kurs(array('name'=>'id_kurs'),$this->form->get_temp_data('id_kurs'))?>
					<?php echo form_error('id_kurs'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Foto</label></td>
				<td>
					<?php if(isset($gambar_barang)){
						if($gambar_barang!=''){?>
						<p><a href="<?php echo base_url('lampiran/gambar_barang/'.$gambar_barang)?>" target="_blank">Lampiran</a></p>
						<p><b><i style="color: #D62E2E;">Tinggalkan kosong jika tidak diganti</i></b></p>	
						<?php 
						}
					}?>
					<input type="file" name="gambar_barang" value="<?php echo $this->form->get_temp_data('gambar_barang');?>">
					<?php echo form_error('gambar_barang'); ?>
					
				</td>
			</tr>
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Simpan">
		</div>
	</form>
</div>

