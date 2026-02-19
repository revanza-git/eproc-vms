<div class="formDashboard">

	<h1 class="formHeader">Edit Domisili Perusahaan</h1>

	<form method="POST" enctype="multipart/form-data">

		<table>

			<tr class="input-form">

				<td><label>Nama Surat*</label></td>

				<td>

					<?php 

						$v = 	array(

									''												=>	'-- Pilih --',

									'Surat Keterangan Domisili Perusahaan (SKDP)'	=>	'Surat Keterangan Domisili Perusahaan (SKDP)',

									'Surat Izin Tempat Usaha (SITU)'				=>	'Surat Izin Tempat Usaha (SITU)',

									'Herregisterasi SKDP'							=>	'Herregisterasi SKDP',

								);

						echo form_dropdown('type', $v, ($this->form->get_temp_data('type'))?$this->form->get_temp_data('type'):$type,'');?>

						<?php echo form_error('type'); 

					?>

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Nomor*</label></td>

				<td>

					<input type="text" name="no" value="<?php echo ($this->form->get_temp_data('no'))?$this->form->get_temp_data('no'):$no;?>">

					<?php echo form_error('no'); ?>

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Alamat*</label></td>

				<td>

					<textarea name="address"><?php echo ($this->form->get_temp_data('address'))?$this->form->get_temp_data('address'):$address;?></textarea>

					<?php echo form_error('address'); ?>

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Tanggal*</label></td>

				<td>

					<?php echo $this->form->calendar(array('name'=>'issue_date','value'=>($this->form->get_temp_data('issue_date'))?$this->form->get_temp_data('issue_date'):$issue_date), false);?>

					<?php echo form_error('issue_date'); ?>

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Masa Berlaku*</label></td>

				<td>

					<?php echo $this->form->lifetime_calendar(array('name'=>'expire_date','value'=>($this->form->get_temp_data('expire_date'))?$this->form->get_temp_data('expire_date'):$expire_date), false);?>

					<?php echo form_error('expire_date'); ?>

				</td>

			</tr>
			<tr class="input-form">
				<td><label>Lembaga Penerbit*</label></td>
				<td>

					<input type="text" name="issue_by" value="<?php echo ($this->form->get_temp_data('issue_by'))?$this->form->get_temp_data('issue_by'):$issue_by;?>">
					<?php echo form_error('issue_by'); ?>
				</td>
			</tr>	

			<tr class="input-form">

				<td><label>Bukti scan dokumen SKDP*</label></td>

				<td>

					<?php echo $this->form->file(array('name'=>'situ_file','value'=>($this->form->get_temp_data('situ_file'))?$this->form->get_temp_data('situ_file'):$situ_file));?>

					

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Bukti sedang dalam proses perpanjangan</label></td>

				<td>

					<?php echo $this->form->file(array('name'=>'file_extension_situ','value'=>($this->form->get_temp_data('file_extension_situ'))?$this->form->get_temp_data('file_extension_situ'):$file_extension_situ));?>

					

				</td>

			</tr>

			

		</table>

		

		<div class="buttonRegBox clearfix">

			<input type="submit" value="Simpan" class="btnBlue" name="Update">

		</div>

	</form>

</div>