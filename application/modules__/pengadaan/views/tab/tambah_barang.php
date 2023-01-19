<div class="formDashboard">

	<h1>Tambah Barang / Jasa</h1>

	<form method="POST" enctype="multipart/form-data">

		<table>

			<tr class="input-form">

				<td><label>Kategori</label></td>

				<td>

					<div>

						<?php echo form_dropdown('category', array('barang'=>'Barang','jasa'=>'Jasa'), $this->form->get_temp_data('category'),'class="kategori"');?>				

					</div>

					<?php echo form_error('category'); ?>

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Nama Barang / Jasa</label></td>

				<td>

					<div>

						<input type="text" name="nama_barang" value="<?php echo $this->form->get_temp_data('nama_barang');?>" id="material_name">&nbsp;<span onClick="resetInput()" class="resetInput"><i class="fa fa-refresh"></i>&nbsp;Reset</span>

						<input type="hidden" id="id_material" name="id_material">

						<div id="cat"><input type="checkbox" name="is_catalogue" value="1">&nbsp; Simpan ke dalam katalog</div>

					</div>

					<?php echo form_error('nama_barang'); ?>

				</td>

			</tr>

			<tr class="input-form kurs_drop" >

				<td><label>Mata Uang</label></td>

				<td>

					<div>
						<input type="hidden" name="id_kurs" id="id_kurs_hide">

						<?php echo form_dropdown('id_kurs', $id_kurs, $this->form->get_temp_data('id_kurs'),array('class'=>'kurs_drop'));?>				

					</div>

					<?php echo form_error('id_kurs'); ?>

				</td>

			</tr>

			

			<tr class="input-form">

				<td><label>Harga Satuan</label></td>

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