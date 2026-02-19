<div class="progressBar">
	<ul>
		<li>
			Pilih Penyedia Barang &amp; Jasa
		</li>
		<li class="active">
			Pilih Surat Izin
		</li>
	</ul>
</div>
<div class="formDashboard">
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Pilih Ijin Usaha</label></td>
				<td>
					<div>
					<?php echo form_dropdown('id_surat', $id_ijin_usaha, $this->form->get_temp_data('id_surat'));?>
					</div>
					<?php echo form_error('id_surat'); ?>
				</td>
			</tr>
			<tr class="input-form">
					<td>
						Nilai Penawaran : 
					</td>
					<td>
						 Rp.<input type="text" name="idr_value">
					</td>
				</tr><tr class="input-form">
					<td>
						
					</td>
					<td>
						<?php echo $this->form->get_kurs(array('name'=>'id_kurs'),$this->form->get_temp_data('id_kurs'))?><input type="text" name="kurs_value">
					</td>
				</tr>
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="simpan">
			<input type="submit" value="Kembali" class="btnBlue" name="back">
		</div>
	</form>
</div>