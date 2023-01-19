<div class="registerBlock">

	<h1 class="formHeader">Surat Pernyataan</h1>

	<form method="POST">

		<table>

			<tr class="input-form">

				<td><label>Nama*</label></td>

				<td>

					<input type="text" name="pic_name" value="<?php echo $this->form->get_temp_data('pic_name');?>">

					<?php echo form_error('pic_name'); ?>

				</td>

				<td rowspan="6">

					<p>Menyatakan dengan sesungguhnya bahwa :</p>

					<ol>

						

					</ol>

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Jabatan*</label></td>

				<td>

					<input type="text" name="pic_position" value="<?php echo $this->form->get_temp_data('pic_position');?>">

					<?php echo form_error('pic_position'); ?>

				</td>

			</tr>

			<tr class="input-form">

				<td><label>No Telp*</label></td>

				<td>

					<input type="text" name="pic_phone" value="<?php echo $this->form->get_temp_data('pic_phone'); ?>">

					<?php echo form_error('pic_phone'); ?>

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Email*</label></td>

				<td>

					<input type="text" name="pic_email" value="<?php echo $this->form->get_temp_data('pic_email');?>">

					<?php echo form_error('pic_email'); ?>

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Alamat*</label></td>

				<td>

					<textarea name="pic_address"><?php echo $this->form->get_temp_data('pic_address'); ?></textarea>

					<?php echo form_error('pic_address'); ?>

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Bertindak untuk dan atas nama*</label></td>

				<td>

					<div style="width:270px">

					<?php echo form_dropdown('id_legal', $id_legal, $this->form->get_temp_data('id_legal'),'class="col-14"');?>

					<input type="text" name="name" class="col-24" value="<?php echo $this->form->get_temp_data('name'); ?>">

					</div>

					<?php echo form_error('id_legal'); ?>

					<?php echo form_error('name'); ?>

				</td>

			</tr>

		</table>

		<div class="buttonRegBox clearfix">

			<input type="submit" value="Next" class="btnBlue" name="next">

		</div>

	</form>

</div>