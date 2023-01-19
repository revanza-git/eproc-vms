<?php echo $this->session->flashdata('msgSuccess')?>
<div class="formDashboard" style="width:auto !important;">
	<h1 class="formHeader">Masukan Penyedia Barang/Jasa ke Daftar <?php echo $value ?></h1>

	<form method="POST" enctype="multipart/form-data" style="float:left; width:500px !important; display:inline-block;">
		<input type="hidden" id="id_blacklist" name="id_blacklist" value="<?php echo  $id_blacklist;?>">
		<table>
			<?php if( $vendor['poin']){?>
			<tr  class="input-form">
				<td><label>Nilai Skor</label></td>
				<td>
					<?php echo $vendor['poin'];?>
				</td>
			</tr>
			<?php } ?>
			<tr class="input-form">
				<td><label>Nama Penyedia Barang &amp; Jasa*</label></td>
				<td>
					<input type="input" placeholder="Cari Vendor" name="q" class="suggestionInput" id="vendor_name" value="<?php echo $vendor['vendor_name'];?>" <?php echo ($vendor['vendor_name']) ? 'disabled' : '';?>>
					<input type="hidden" id="id_vendor" name="id_vendor" value="<?php echo  $vendor['id_vendor'];?>">
					<?php echo form_error('id_vendor'); ?>
				<?php //echo $filter_list;?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tanggal Mulai*</label></td>
				<td>
					<?php echo $this->form->calendar(array('name'=>'start_date','value'=>(isset($start_date)?$start_date:$this->form->get_temp_data('start_date'))), FALSE);?>
					<?php echo form_error('start_date'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tanggal Selesai*</label></td>
				<td> 
				<?php 

					if($id_blacklist!=2){ ?>
					+ 2 Tahun
						<?php // echo $this->form->lifetime_calendar(array('name'=>'end_date','value'=>(isset($end_date)?$end_date:$this->form->get_temp_data('end_date'))), FALSE);
					 }else{ ?>
					Selamanya
					<input type="hidden" value="lifetime" name="end_date">
					<?php } ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Keterangan*</label></td>
				<td>
					<textarea name="remark" rows="5" id="remarkArea"></textarea>
					<?php echo form_error('remark'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td></td>
				<td><input type="checkbox" id="remark">Gunakan Keterangan Default</td>
			</tr>
			<tr class="input-form">
				<td><label>Lampiran*</label></td>
				<td>
					<?php echo $this->form->file(array('name'=>'blacklist_file','value'=>$this->form->get_temp_data('blacklist_file')));?>
					
				</td>
			</tr>
		</table>

		<div class="buttonRegBox clearfix" style="float:right; padding-top: 0;">
			<input type="submit" value="Simpan" class="btnBlue" name="Simpan">
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
