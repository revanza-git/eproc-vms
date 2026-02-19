<div class="formDashboard">

	<h1>Ubah Barang/Jasa</h1>

	<form method="POST" enctype="multipart/form-data">

		<table>

			<tr class="input-form">

				<td><label>Kategori</label></td>

				<td>

					<div>
					<?php 
					$arr = array('barang'=>'Barang','jasa'=>'Jasa');
					if(!$is_catalogue){
						echo form_dropdown('category', $arr, ($this->form->get_temp_data('category'))?$this->form->get_temp_data('category'):$category,'class="kategori"');
					}else{
						echo $arr[$category];
						} ?>

					</div>

					<?php echo form_error('category'); ?>

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Nama Barang / Jasa</label></td>

				<td>

					<div>
					<?php 
					
					if(!$is_catalogue){ ?>
						<input type="text" name="nama_barang" value="<?php echo ($this->form->get_temp_data('nama_barang'))?$this->form->get_temp_data('nama_barang'):$nama_barang;?>" >					
					<?php } else{
						
						echo $nama_barang;
					 } ?>
					</div>

					<?php echo form_error('nama_barang'); ?>

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

				<td><label>Harga Satuan</label></td>

				<td>

					<div>

						<input type="text" name="nilai_hps" class="money-masked" value="<?php echo number_format(($this->form->get_temp_data('nilai_hps'))?$this->form->get_temp_data('nilai_hps'):$nilai_hps);?>">					

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