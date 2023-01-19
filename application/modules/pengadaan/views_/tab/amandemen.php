<div class="tab procView">
	<?php echo $this->utility->tabNav($tabNav,'submit');?>
	
	<div class="tableWrapper" style="margin-bottom: 20px;padding-left: 20px;">
		<?php echo $this->session->flashdata('msgSuccess')?>
		<div class="formDashboard">
			<form method="POST" enctype="multipart/form-data">
				<table>
					<tr class="input-form">
						<td><label>Tahap Amandemen*</label></td>
						<td>
							<input type="text" name="step_name" value="<?php echo ($this->form->get_temp_data('step_name'))?$this->form->get_temp_data('step_name'):$step_name?>">
							<?php echo form_error('step_name'); ?>
						</td>
					</tr>
					<tr class="input-form">
						<td><label>Tanggal Mulai Amandemen*</label></td>
						<td>
							<?php 
							echo $this->form->calendar(array('name'=>'start_date','value'=>($this->form->get_temp_data('start_date'))?$this->form->get_temp_data('start_date'):(($start_date) ? $start_date : $range['end_date'])), false);?>
							<?php echo form_error('start_date'); ?>
						</td>
					</tr>
					<tr class="input-form">
						<td><label>Tanggal Berakhir Amandemen*</label></td>
						<td>
							<?php 
							echo $this->form->calendar(array('name'=>'end_date','value'=>($this->form->get_temp_data('end_date'))?$this->form->get_temp_data('end_date'):(($end_date) ? $end_date : $range['end_date'])), false);?>
							<?php echo form_error('end_date'); ?>
						</td>
					</tr>
				</table>
				<div class="buttonRegBox clearfix">
					<input type="submit" value="Simpan" class="btnBlue" name="Simpan">
				</div>
			</form>
		</div>
		
		
	</div>
</div>
