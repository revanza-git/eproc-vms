<div class="tab procView">

	<?php echo $this->utility->tabNav($tabNav,'submit');?>

	

	<div class="tableWrapper" style="margin-bottom: 20px;padding-left: 20px;">

		

		<div class="formDashboard">

			<form method="POST" enctype="multipart/form-data">

				<table>

					

					<tr class="input-form">

						<td><label>Tahap Pekerjaan*</label></td>

						<td>

							<input type="text" name="step_name" value="<?php echo $this->form->get_temp_data('step_name');?>">

							<?php echo form_error('step_name'); ?>

						</td>

					</tr>

					<tr class="input-form">

						<td><label>Waktu Ekspektasi*</label></td>

						<td>

							<input type="number" min="1" name="supposed" value="<?php echo ($ekspektasi) ? $ekspektasi: $this->form->get_temp_data('supposed');?>" class="col-14">&nbsp;<span>hari</span>

							<?php echo form_error('supposed'); ?>

						</td>

					</tr>

					<tr class="input-form">

						<td><label>Waktu Realisasi*</label></td>

						<td>

							<input type="number" min="1" name="realization" value="<?php echo $this->form->get_temp_data('realization');?>" class="col-14">&nbsp;<span>hari</span>

							<?php echo form_error('realization'); ?>

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

