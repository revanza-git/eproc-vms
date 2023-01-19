<div class="progressBar">

	<ul>
		<li>
		Klasifikasi Usaha

		</li><!--

		--><li class="active">			Pilih Bidang

		</li><!--

		--><li>

			Pilih Sub Bidang

		</li>

	</ul>

</div>

<div class="formDashboard">

	<form method="POST" enctype="multipart/form-data">

		<table>

			<tr class="input-form">

				<td><label>Pilih Bidang</label></td>

				<td>

					<div>

					<?php echo form_dropdown('id_bidang', $id_bidang, $this->form->get_temp_data('id_bidang'));?>

					</div>

					<?php echo form_error('id_bidang'); ?>

				</td>

			</tr>

			

		</table>

		

		<div class="buttonRegBox clearfix">

			<input type="submit" value="Lanjut" class="btnBlue" name="next">

			<input type="submit" value="Kembali" class="btnBlue" name="back">

		</div>

	</form>

</div>