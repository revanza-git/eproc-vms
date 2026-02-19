<div class="formDashboard" style="width:auto !important;">

	<h1 class="formHeader">Data Blacklist</h1>



	<form method="POST" enctype="multipart/form-data" style="width:500px !important; display:inline-block;">

		<input type="hidden" id="id_blacklist" name="id_blacklist" value="<?php echo  $vendor['id_blacklist'];?>">

		<table>

			<tr class="input-form">

				<td><label>Nama Penyedia Barang/Jasa*</label></td>

				<td>

					<input type="input" placeholder="Cari Vendor" name="q" class="suggestionInput" id="vendor_name" value="<?php echo $vendor['vendor_name'];?>" <?php echo ($vendor['vendor_name']) ? 'disabled' : '';?>>

					<input type="hidden" id="id_vendor" name="id_vendor" value="<?php echo  $vendor['id_vendor'];?>">

				<?php //echo $filter_list;?>

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Tanggal Selesai*</label></td>

				<td >

					<?php echo $this->form->lifetime_calendar(array('name'=>'end_date','value'=>(isset($end_date)?$end_date:$this->form->get_temp_data('end_date')),'text_str'=>'Selamanya'),FALSE);?>

					<?php echo form_error('end_date'); ?>

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Lampiran </label></td>

				<td>

					<input type="file" name="white_file" value="<?php echo $this->form->get_temp_data('white_file');?>">

					<?php echo form_error('white_file'); ?>

				</td>

			</tr>

		</table>



		<div class="buttonRegBox clearfix" style="float:left;">

			<input type="submit" value="Simpan" class="btnBlue" name="Simpan">

		</div>

	</form>

		<div id="listDefault" class="">

			<h4>Pilih Remark</h4>

			<ul>

				<?php foreach ($remark_list as $key => $value) {?>

				<li>

					<?php $id = 'class=option id=option'.$value['id'];?>

					<?php echo form_radio('remarkOption', $value['remark'],TRUE, $id); echo '<p>'.$value['remark'].'</p>';?>

				</li>

				<?php }?>

			</ul>

		</div>

</div>

