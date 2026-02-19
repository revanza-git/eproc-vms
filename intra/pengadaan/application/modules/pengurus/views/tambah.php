<script type="text/javascript">
	$(function() {
		$( "#datepicker" ).datepicker({ minDate: -20, maxDate: "+1M +10D" });
	});
</script>

<div class="formDashboard">
	<h1 class="formHeader">Tambah Pengurus Perusahaan</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>No. Akta Pengangkatan*</label></td>
				<td>
					<?php echo form_dropdown('id_akta', $id_akta, $this->form->get_temp_data('id_akta'),'');?>
					<?php echo form_error('id_akta'); ?>
				</td>
			</tr>	
			<tr class="input-form">
				<td><label>Nama*</label></td>
				<td>
					<input type="text" name="name" value="<?php echo $this->form->get_temp_data('name');?>">
					<?php echo form_error('name'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Posisi*</label></td>
				<td>
					<?php
						$options = array(
				                  'Direktur Utama'  => 'Direktur Utama',
				                  'Direktur'    => 'Direktur',
				                  'Komisaris'   => 'Komisaris',
				                );

						echo form_dropdown('position', $options, 'large');
					?>
					<?php echo form_error('position'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Nomor Identitas (KTP/Passport/KITAS)*</label></td>
				<td>
					<input type="text" name="no" value="<?php echo $this->form->get_temp_data('no');?>">
					<?php echo form_error('no'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Masa Berlaku*</label></td>
				<td>
					<?php echo $this->form->lifetime_calendar(array('name'=>'expire_date','value'=>$this->form->get_temp_data('expire_date')), false);?>
					<?php echo form_error('expire_date'); ?>
				</td>
			</tr>
			<!-- <tr class="input-form">
				<td><label>Masa Berlaku Jabatan*</label></td>
				<td>
					<?php echo $this->form->calendar(array('name'=>'position_expire','value'=>$this->form->get_temp_data('position_expire')), false);?>
					<?php echo form_error('position_expire'); ?>
				</td>
			</tr> -->
			<tr class="input-form">
				<td><label>Bukti scan Identitas*</label></td>
				<td>
					<input type="file" name="pengurus_file" value="<?php echo $this->form->get_temp_data('pengurus_file');?>">
					<?php echo form_error('pengurus_file'); ?>
				</td>
			</tr>
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Simpan">
		</div>
	</form>
</div>