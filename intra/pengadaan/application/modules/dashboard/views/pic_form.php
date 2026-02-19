<style>

	ul,ol{

		list-style: inherit !important;

	}

</style>

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

				<td rowspan="9" style="padding-left: 10px; list-style: inherit !important;">

					<?php foreach ($pernyataan as $key => $value) {?>

					<?php echo $value['value'];?>

					<?php } ?>

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

				<td><label>Bertindak untuk dan atas nama</label></td>

				<td>

					<b><?php echo $data_vendor['legal_name'].' '.$data_vendor['name']; ?></b>

				</td>

			</tr>

		</table>

		<div class="buttonRegBox clearfix">

			<input type="submit" value="Lanjut" class="btnBlue" name="next">

		</div>

	</form>

</div>