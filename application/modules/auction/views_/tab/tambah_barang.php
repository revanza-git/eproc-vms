<div class="formDashboard">
	<h1>Tambah Barang</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Nama Barang / Jasa</label></td>
				<td>
					<div>
						<input type="text" name="nama_barang" value="<?php echo $this->form->get_temp_data('nama_barang');?>" id="material_name">&nbsp;<span onClick="resetInput()" class="resetInput"><i class="fa fa-refresh"></i>&nbsp;Reset</span>
						<input type="hidden" id="id_material" name="id_material">
						<input type="hidden" class="category" name="category" value="barang">
						<div id="cat"><input type="hidden" name="is_catalogue" value="0"><input type="checkbox" name="is_catalogue" value="1">&nbsp; Simpan ke dalam katalog</div>
					</div>
					<?php echo form_error('nama_barang'); ?>
				</td>
			</tr>
			<tr class="input-form kurs_drop">
				<td><label>Mata Uang</label></td>
				<td>
					<div>
						<input type="hidden" name="id_kurs" id="id_kurs_hide">
						<?php 
						if(!empty($id_kurs)){
							echo form_dropdown('id_kurs', $id_kurs, $this->form->get_temp_data('id_kurs'),array('class'=>'kurs_drop'));
						}else{
							echo '-- Mata uang belum dipilih --';
						}
						?>				
					</div>
					<?php echo form_error('id_kurs'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Volume</label></td>
				<td>
					<input type="number" name="volume" min="1"   value="<?php echo ($this->form->get_temp_data('volume')?$this->form->get_temp_data('volume'):1);?>">					
					<?php echo form_error('volume'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Nilai HPS</label></td>
				<td>
					<div>
						<input type="text" name="nilai_hps" class="money-masked" id="hps" value="<?php echo $this->form->get_temp_data('nilai_hps');?>">					
					</div>
					<?php echo form_error('nilai_hps'); ?>
				</td>
			</tr>
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="simpan">
		</div>
	</form>
</div>