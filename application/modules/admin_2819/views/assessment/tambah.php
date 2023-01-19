<style type="text/css">
	#mceu_9{
		width: 706px !important;
	}
</style>
<div class="formDashboard">

	<h1 class="formHeader">Tambah Assessment</h1>

	<form method="POST" enctype="multipart/form-data">

		<table>

			<tr class="input-form">

				<td><label>Pertanyaan Assessment* :</label></td>

				<td>

					<textarea class="wysiwyg" name="value"><?php echo $this->form->get_temp_data('value');?></textarea>

					<?php echo form_error('value'); ?>

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Penilai * :</label></td>

				<td>

					<?php echo form_dropdown('id_role', $role, $this->form->get_temp_data('id_role'),'');?>

					<?php echo form_error('id_role'); ?>

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Point * :</label></td>

				<td>

					<input type="number" name="point" value="<?php echo $this->form->get_temp_data('point');?>">

					<?php echo form_error('point'); ?>

				</td>

			</tr>

			

		</table>

		

		<div class="buttonRegBox clearfix">

			<input type="submit" value="Simpan" class="btnBlue" name="Simpan">

		</div>

	</form>

</div>

