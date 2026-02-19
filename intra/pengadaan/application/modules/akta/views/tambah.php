<div class="formDashboard">
	<h1 class="formHeader">Tambah Akta</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Jenis*</label></td>
				<td>
					<?php
						$v = 	array(
									''			=>	'Pilih Salah Satu',
									'pendirian'	=>	'Akta Pendirian',
									'perubahan'	=>	'Akta - Akta Perubahan Terakhir (mengenai anggaran dasar)',
									'direksi'	=>	'Akta - Akta Perubahan Terakhir (mengenai susunan terakhir direksi dan komisaris)',
									'saham'		=>	'Akta - Akta Perubahan Terakhir (mengenai susunan terakhir pemegang saham)',
								);
						echo form_dropdown('type', $v, $this->form->get_temp_data('type'));
						echo form_error('type'); 
					?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tanggal Akta*</label></td>
				<td>
					<?php echo $this->form->calendar(array('name'=>'issue_date','value'=>$this->form->get_temp_data('issue_date')), false);?>
					<?php echo form_error('issue_date'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Nomor*</label></td>
				<td>
					<input type="text" name="no" value="<?php echo $this->form->get_temp_data('no');?>">
					<?php echo form_error('no'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Notaris*</label></td>
				<td>
					<input type="text" name="notaris" value="<?php echo $this->form->get_temp_data('notaris');?>">
					<?php echo form_error('notaris'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Bukti <i>scan</i> Akta*</label></td>
				<td>
					<?php echo $this->form->file(array('name'=>'akta_file','value'=>($this->form->get_temp_data('akta_file'))?$this->form->get_temp_data('akta_file'):$akta_file));?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>No. Pengesahan*</label></td>
				<td>
					<input type="text" name="authorize_no" value="<?php echo $this->form->get_temp_data('authorize_no');?>">
					<?php echo form_error('authorize_no'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Lembaga Pengesah*</label></td>
				<td>
					<input type="text" name="authorize_by" value="<?php echo $this->form->get_temp_data('authorize_by');?>">
					<?php echo form_error('authorize_by'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tanggal Ditetapkan*</label></td>
				<td>
					<?php echo $this->form->calendar(array('name'=>'authorize_date','value'=>$this->form->get_temp_data('authorize_date')), false);?>
					<?php echo form_error('authorize_date'); ?>
				</td>
			</tr>
			
			<tr class="input-form">
				<td><label>Bukti <i>scan</i> dokumen penetapan*</label></td>
				<td>
					<?php echo $this->form->file(array('name'=>'authorize_file','value'=>($this->form->get_temp_data('authorize_file'))?$this->form->get_temp_data('authorize_file'):$authorize_file));?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Bukti sedang dalam proses pengurusan</label></td>
				<td>
					<?php echo $this->form->file(array('name'=>'file_extension_akta','value'=>($this->form->get_temp_data('file_extension_akta'))?$this->form->get_temp_data('file_extension_akta'):$file_extension_akta));?>
					<?php //echo form_error('file_extension_akta'); ?>
				</td>
			</tr>
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Simpan">
		</div>
	</form>
</div>