<div class="formDashboard">
	<h1 class="formHeader">Tambah Pabrikan/Keagenan/Distributor</h1>
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
				<td><label>Nama Principal*</label></td>
				<td>
					<input type="text" name="principal" value="<?php echo $this->form->get_temp_data('principal');?>">
					<?php echo form_error('principal'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Pabrikan/Keagenan/Distributor</label></td>
				<td>
					<?php echo form_dropdown('type', $pabrik, $this->form->get_temp_data('type'),'');?>
					<?php echo form_error('type'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tanggal*</label></td>
				<td>
					<?php echo $this->form->calendar(array('name'=>'issue_date','value'=>$this->form->get_temp_data('issue_date')), false);?>
					<?php echo form_error('issue_date'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Masa Berlaku*</label></td>
				<td>
					<?php echo $this->form->lifetime_calendar(array('name'=>'expire_date','value'=>$this->form->get_temp_data('expire_date'), 'text_str' => "Selama Perusahaan Berdiri"), false);?>
					<?php echo form_error('expire_date'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Lampiran*</label></td>
				<td>
					<?php echo $this->form->file(array('name'=>'agen_file','value'=>($this->form->get_temp_data('agen_file'))?$this->form->get_temp_data('agen_file'):$agen_file));?>
					<?php //echo form_error('agen_file'); ?>
				</td>
			</tr>
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="simpan">
		</div>
	</form>
</div>