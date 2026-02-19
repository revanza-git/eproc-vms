<?php echo $this->session->flashdata('msgSuccess')?>
<div class="formDashboard">
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td colspan="2">Apakah perusahaan anda memiliki sertifikat CSMS?</td>
			</tr>
			<tr class="input-form">
				<td>
					<label class="lbform">
						<?php echo form_radio(array('name'=>'csms','id'=>'csms_true'),1,(set_radio('csms',1)||($this->form->get_temp_data('csms')==1))?TRUE:FALSE,'class="csms_radio"')?>Punya
					</label>
					<label class="lbform">
						<?php echo form_radio(array('name'=>'csms'),0,(set_radio('csms',0)||($this->form->get_temp_data('csms')==0))?TRUE:FALSE,'class="csms_radio"')?>Belum Punya
					</label>
				</td>
			</tr>
			<tr class="input-form lampiran_csms">
				<td><label>Sertifikat CSMS *</label></td>
				<td>
					<?php echo $this->form->file(array('value'=>$csms_file,'name'=>'csms_file'));?>
					
				</td>
			</tr>
			<!--<tr class="input-form lampiran_csms">
				<td><label>Masa Berlaku *</label></td>
				<td>
					<?php echo $this->form->calendar(array('name'=>'expiry_date','value'=>(isset($expiry_date)?$expiry_date:$this->form->get_temp_data('expiry_date')), false));?>
					<?php echo form_error('expiry_date'); ?>
				</td>
			</tr>-->
			<tr class="input-form lampiran_csms">
				<td><label>Masukkan Nilai CSMS *</label></td>
				<td>
					<input type="text" name="score" value="<?php echo (isset($score)?$score:$this->form->get_temp_data('score'))?>">
					<?php echo form_error('score'); ?>
				</td>
			</tr>
			
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Lanjut" class="btnBlue" name="next">
		</div>
	</form>
</div>