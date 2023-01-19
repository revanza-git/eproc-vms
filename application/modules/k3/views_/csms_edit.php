<h1 class="formHeader">Edit CSMS</h1>
<div class="formDashboard">
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form lampiran_csms">
				<td><label>Lampiran CSMS</label></td>
				<td>
					<?php echo $this->form->file(array('name'=>'csms_file','value'=>($this->form->get_temp_data('csms_file'))?$this->form->get_temp_data('csms_file'):$csms_file));?>
				
				</td>
			</tr>
			<tr class="input-form lampiran_csms">
				<td><label>Masa Berlaku</label></td>
				<td>
					<?php echo $this->form->calendar(array('name'=>'expiry_date','value'=>(isset($expiry_date)?$expiry_date:$this->form->get_temp_data('expiry_date')), false));?>
					<?php echo form_error('expiry_date'); ?>
				</td>
			</tr>
			<tr class="input-form lampiran_csms">
				<td><label>Nilai CSMS</label></td>
				<td>
					<input type="text" name="score" value="<?php echo (isset($score)?$score:$this->form->get_temp_data('score'))?>">
					<?php echo form_error('score'); ?>
				</td>
			</tr>
			
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="next">
		</div>
	</form>
</div>