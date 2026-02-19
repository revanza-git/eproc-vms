<div class="formDashboard">

	<h1 class="formHeader">Detail Penyedia Barang/Jasa <?php echo $blacklist_name ?></h1>



	<form method="POST" enctype="multipart/form-data">

		<input type="hidden" name="id_blacklist" value="<?php echo $id_blacklist;?>">

		<table>

			<tr class="input-form">

				<td><label>Nama Penyedia Barang/Jasa*</label></td>

				<td>:

					<?php echo $vendor_name?>

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Tanggal Mulai*</label></td>

				<td>:

					<?php echo $start_date; ?>

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Tanggal Selesai*</label></td>

				<td >:

					<?php echo ($end_date=='lifetime')?'Selamanya':$end_date; ?>

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Remark</label></td>

				<td>:

					<?php echo $remark ?>

				</td>

			</tr>

			

			<tr class="input-form">

				<tr class="input-form">

				<td><label>File </label></td>

				<td>: <a href="<?php echo base_url('lampiran/blacklist_file/'.$blacklist_file)?>" target="_blank">Lampiran</a>

					

				</td>

			</tr>

			<?php if(isset($white_file)){ ?>

			<tr class="input-form">

				<tr class="input-form">

				<td><label>Lampiran Pemutihan</label></td>

				<td>: <a href="<?php echo base_url('lampiran/white_file/'.$white_file)?>" target="_blank">Lampiran</a>

					

				</td>

			</tr>

			<?php } ?>

			<?php if(isset($white_date)){ ?>

			<tr class="input-form">

				<tr class="input-form">

				<td><label>Tanggal Pemutihan</label></td>

				<td>: <?php 

					$date = date_create($white_date);

				echo date_format($date, "d F Y");?>

					

				</td>

			</tr>

			<?php } ?>

		</table>



		<div class="buttonRegBox clearfix" >

			<input type="submit" value="Setujui" class="btnBlue" name="approve">

		</div>



	</form>

		

</div>

