<div class="formDashboard">
	<h1 class="formHeader">Tambah Tanda Daftar Perusahaan (TDP)</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>No.*</label></td>
				<td>
					<input type="text" name="no" value="<?php echo $this->form->get_temp_data('no');?>">
					<?php echo form_error('no'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tanggal Dikeluarkan*</label></td>
				<td>
					<?php echo $this->form->calendar(array('name'=>'issue_date','value'=>$this->form->get_temp_data('issue_date')), false);?>
					<?php echo form_error('issue_date'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Berlaku s/d*</label></td>
				<td>
					<?php echo $this->form->lifetime_calendar(array('name'=>'expiry_date','value'=>(isset($expiry_date)?$expiry_date:$this->form->get_temp_data('expiry_date')),'text_str'=>'Selamanya'),FALSE);?>
					<?php echo form_error('expiry_date'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Lembaga Penerbit*</label></td>
				<td>
					<input type="text" name="authorize_by" value="<?php echo $this->form->get_temp_data('authorize_by');?>">
					<?php echo form_error('authorize_by'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Bukti scan dokumen NIB/TDP*</label></td>
				<td>
					<?php echo $this->form->file(array('name'=>'tdp_file','value'=>($this->form->get_temp_data('tdp_file'))?$this->form->get_temp_data('tdp_file'):$tdp_file));?>
					<?php //echo form_error('tdp_file'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Bukti sedang dalam proses perpanjangan</label></td>
				<td>
					<?php echo $this->form->file(array('name'=>'extension_file','value'=>($this->form->get_temp_data('extension_file'))?$this->form->get_temp_data('extension_file'):$extension_file));?>
					<?php echo form_error('extension_file'); ?>
					<p class="notifReg">hanya diisi bila sedang dalam proses perpanjangan</p>
				</td>
			</tr>
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Simpan">
		</div>
	</form>
</div>