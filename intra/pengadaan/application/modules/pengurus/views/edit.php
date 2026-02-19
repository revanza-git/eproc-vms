<div class="formDashboard">
	<h1 class="formHeader">Edit Pengurus Perusahaan</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>No. Akta Pengangkatan*</label></td>
				<td>
					<?php echo form_dropdown('id_akta', $akta, ($this->form->get_temp_data('akta'))?$this->form->get_temp_data('akta'):$id_akta,'');?>
					<?php echo form_error('id_akta'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Nama*</label></td>
				<td>
					<input type="text" name="name" value="<?php echo ($this->form->get_temp_data('name'))?$this->form->get_temp_data('name'):$name;?>">
					<?php echo form_error('name'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Nomor Identitas (KTP/Passport/KITAS)*</label></td>
				<td>
					<input type="text" name="no" value="<?php echo ($this->form->get_temp_data('no'))?$this->form->get_temp_data('no'):$no;?>">
					<?php echo form_error('no'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Masa Berlaku*</label></td>
				<td>
					<?php echo $this->form->lifetime_calendar(array('name'=>'expire_date','value'=> ($this->form->get_temp_data('expire_date'))?$this->form->get_temp_data('expire_date'):$expire_date,'text_str'=>'Selamanya'), false);?>
					<?php echo form_error('expire_date'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Jabatan*</label></td>
				<td>
					<input type="text" name="position" value="<?php echo ($this->form->get_temp_data('position'))?$this->form->get_temp_data('position'):$position;?>">
					<?php echo form_error('position'); ?>
				</td>
			</tr>
			<!-- <tr class="input-form">
				<td><label>Masa Berlaku Jabatan*</label></td>
				<td>
					<?php echo $this->form->calendar(array('name'=>'position_expire','value'=>($this->form->get_temp_data('position_expire'))?$this->form->get_temp_data('position_expire'):$position_expire), false);?>
					<?php echo form_error('position_expire'); ?>
				</td>
			</tr> 
			<tr class="input-form">
				<td><label>Masa Berlaku*</label></td>
				<td>
					<?php echo $this->form->calendar(array('name'=>'expire_date','value'=>($this->form->get_temp_data('expire_date'))?$this->form->get_temp_data('expire_date'):$expire_date), false);?>
					<?php echo form_error('expire_date'); ?>
				</td>
			</tr>-->
			<tr class="input-form">
				<td><label>Lampiran*</label></td>
				<td>
					<?php echo $this->form->file(array('name'=>'pengurus_file','value'=>($this->form->get_temp_data('pengurus_file'))?$this->form->get_temp_data('pengurus_file'):$pengurus_file));?>
				</td>
			</tr>
			
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Update">
		</div>
	</form>
</div>