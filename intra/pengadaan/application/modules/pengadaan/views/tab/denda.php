<div class="tab procView">
	<?php echo $this->utility->tabNav($tabNav,'submit');?>
	
	<div class="tableWrapper" style="margin-bottom: 20px;padding-left: 20px;">
		
		<div class="formDashboard">
			<form method="POST" enctype="multipart/form-data">
				<table>
					<tr class="input-form">
						<td><label>Tanggal Mulai Denda</label></td>
						<td>: <?php echo ($range['end_date'])?default_date($range['end_date']) : '-- Tentukan tanggal akhir pada Jangka Waktu Pelaksanaan Pekerjaan / amandemen --'?>
							<input type="hidden" name="start_date" value="<?php echo $range['end_date']?>">
						</td>
					</tr>
					<tr class="input-form">
						<td><label>Tanggal Berakhir*</label></td>
						<td>: 
						<?php 
							echo $this->form->calendar(array('name'=>'end_date','value'=>($this->form->get_temp_data('end_date'))?$this->form->get_temp_data('end_date'):(($end_date) ? $end_date : $range['end_date'])), false);?>
							<!--<?php echo $this->form->calendar(array('name'=>'end_date','value'=>($this->form->get_temp_data('end_date'))?(($this->form->get_temp_data('end_date'))? $this->form->get_temp_data('end_date'):$range['end_date'] ):$end_date), false);?>-->
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
