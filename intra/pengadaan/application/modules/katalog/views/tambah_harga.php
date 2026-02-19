<div class="formDashboard">

	<h1 class="formHeader"><?php echo $title ?></h1>

	<form method="POST" enctype="multipart/form-data">

		<table>

			<tr class="input-form">

				<td><label>Tanggal*</label></td>

				<td>

					<?php echo $this->form->calendar(array('name'=>'date','value'=>(isset($date)?$date:$this->form->get_temp_data('date'))), false);?>

					<?php echo form_error('date'); ?>

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Harga</label></td>

				<td>

					<div>
					
						<?php echo isset($symbol) ? $symbol : 'Rp'; ?>
						<input type="text" name="price" class="money-masked" id="hps" value="<?php echo number_format(floatval(isset($price)?$price:$this->form->get_temp_data('price')), 0, ',', '.');?>">					

					</div>

					<?php echo form_error('price'); ?>

				</td>

			</tr>

		</table>

		

		<div class="buttonRegBox clearfix">

			<input type="submit" value="Simpan" class="btnBlue" name="Simpan">

		</div>

	</form>

</div>



