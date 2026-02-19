<div class="formDashboard">
	<h1>Edit Barang</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Nama Barang / Jasa</label></td>
				<td>
					<?php 
										
					if(!$is_catalogue){ ?>
						<input type="text" name="nama_barang" value="<?php echo ($this->form->get_temp_data('nama_barang'))?$this->form->get_temp_data('nama_barang'):$nama_barang;?>" >					
					<?php } else{
						
						echo $nama_barang;
					 } ?>

				</td>
			</tr>
			<tr class="input-form">
				<td><label>Mata Uang</label></td>
				<td>
					<div>
						<?php 
					
						if(!$is_catalogue){ 
							echo form_dropdown('id_kurs', $kurs, ($this->form->get_temp_data('id_kurs'))?$this->form->get_temp_data('id_kurs'):$id_kurs);
						}else{
							echo $kurs_name;
						}?>				
					</div>
					<?php echo form_error('id_kurs'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Volume</label></td>
				<td>
					<input type="number" name="volume" min="1"  id="hps" value="<?php echo ($this->form->get_temp_data('volume')?$this->form->get_temp_data('volume'):1);?>">					
					<?php echo form_error('volume'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Nilai HPS</label></td>
				<td>
					<div>
						<input type="text" name="nilai_hps" class="money-masked" value="<?php echo ($this->form->get_temp_data('nilai_hps'))?$this->form->get_temp_data('nilai_hps'):$nilai_hps;?>">					
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