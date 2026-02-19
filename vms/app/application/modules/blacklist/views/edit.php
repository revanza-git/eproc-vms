<div class="formDashboard" style="">

	<h1 class="formHeader">Ubah Data Daftar <?php echo $value ?></h1>

	<form method="POST" enctype="multipart/form-data" style="width:500px !important; display:inline-block;">

		<table>

			<tr class="input-form">

				<td><label>Nama Penyedia Barang/Jasa*</label></td>

				<td>

					<input type="hidden" name="id_vendor" value="<?php echo ($this->form->get_temp_data('id_vendor'))?$this->form->get_temp_data('id_vendor'):$blacklist['id_vendor'];?>" >

					<input type="text" name="vendor_name" value="<?php echo ($this->form->get_temp_data('vendor_name'))?$this->form->get_temp_data('vendor_name'):$blacklist['name'];?>" disabled>

				

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Tanggal Mulai*</label></td>

				<td>

					<?php echo $this->form->calendar(array('name'=>'start_date','value'=>($this->form->get_temp_data('start_date'))?$this->form->get_temp_data('start_date'):$blacklist['start_date']), FALSE);?>

					<?php echo form_error('start_date'); ?>

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Tanggal Selesai*</label></td>

				<td>

					<?php 

					if($id_blacklist==1){ ?>

					+ 2 Tahun

						<?php // echo $this->form->lifetime_calendar(array('name'=>'end_date','value'=>(isset($end_date)?$end_date:$this->form->get_temp_data('end_date'))), FALSE);

					 }else{ ?>

					*Selamanya

					<input type="hidden" value="lifetime" name="end_date">

					<?php } ?>

				</td>

			</tr>

			<tr class="input-form">

				<td><label>Keterangan*</label></td>

				<td>

					<textarea name="remark" rows="5" id="remarkArea"><?php echo ($this->form->get_temp_data('remark'))?$this->form->get_temp_data('remark'):$blacklist['remark'];?></textarea>

					<?php echo form_error('remark'); ?>

				</td>

			</tr>

			<tr class="input-form">

				<td></td>

				<td><input type="checkbox" id="remark">Gunakan Keterangan Default</td>

			</tr>

			<tr class="input-form">

				<td><label>Lampiran* </label></td>

				<td>

					<?php echo $this->form->file(array('name'=>'blacklist_file','value'=>($this->form->get_temp_data('blacklist_file'))?$this->form->get_temp_data('blacklist_file'):$blacklist['blacklist_file']));?>

					<?php echo form_error('blacklist_file'); ?>

				</td>

			</tr>

		</table>

		

		<div class="buttonRegBox clearfix">

			<input type="submit" value="Simpan" class="btnBlue" name="Update">

		</div>

	</form>

	<div id="listDefault" style="float:left;">

		<h4>Pilih Keterangan</h4>

		<ul>

			<?php foreach ($remark_list as $key => $value) {?>

			<li>

				<?php $id = 'class=option id=option'.$value['id'];?>

				<?php echo form_checkbox('remarkOption', $value['remark'],FALSE, $id); echo '<p>'.$value['remark'].'</p>';?>

			</li>

			<?php }?>

		</ul>

	</div>

</div>