<?php echo $this->session->flashdata('msgSuccess')?>
<?php echo $this->session->flashdata('errorMsg')?>
<div class="progressBar">
	<ul>
		<li>
			Klasifikasi Usaha
		</li><!--
		--><li>
			Surat Izin Usaha
		</li><!--
		--><li class="active">
			Pengisian Data
		</li>
	</ul>
</div>
<div class="formDashboard">
	<form method="POST" enctype="multipart/form-data">
		<table>
			<?php if($form['type']!='siup'&&$form['type']!='siujk'){ ?>
			<tr class="input-form">
				<td><label><?php if($form['type']=='sbu'){
						?>Anggota Asosasi<?php
					}else{
						?>Lembaga Penerbit<?php 
					}?></label></td>
				<td>
					<input type="text" name="authorize_by" value="<?php echo $this->form->get_temp_data('authorize_by');?>">
					<?php echo form_error('authorize_by'); ?>
				</td>
			</tr>
			<?php } ?>
			<tr class="input-form">
				<td><label>Nomor*</label></td>
				<td>
					<input type="text" name="no" value="<?php echo $this->form->get_temp_data('no');?>">
					<?php echo form_error('no'); ?>
				</td>
			</tr>
			<?php if($form['type']=='siujk'){?>
			<tr class="input-form">
				<?php 
					$options = array(
					              1=>1,
								  2=>2,
								  3=>3,
								  4=>4,
								  5=>5
					            );

				?>
				<td><label>Grade*</label></td>
				<td>
					<?php echo form_dropdown('grade', $options, '');?>
					<?php echo form_error('grade'); ?>
				</td>
			</tr>
			<?php }?>
			<tr class="input-form">
				<td><label>Tanggal</label></td>
				<td>
					<?php echo $this->form->calendar(array('name'=>'issue_date','value'=>$this->form->get_temp_data('issue_date')), false);?>
					<?php echo form_error('issue_date'); ?>
				</td>
			</tr>
			<?php if($form['type']!='asosiasi'&&$form['type']!='sbu'){?>
			<tr class="input-form">
				<td><label>Kualifikasi</label></td>
				<td>
					<label class="lbform">
						<?php echo form_radio(array('name'=>'qualification'),'kecil',(set_radio('qualification','kecil')||($this->form->get_temp_data('qualification')=='kecil'))?TRUE:FALSE)?>Kecil
					</label>
					<label class="lbform">
						<?php echo form_radio(array('name'=>'qualification'),'menengah',(set_radio('qualification','menengah')||($this->form->get_temp_data('qualification')=='menengah'))?TRUE:FALSE)?>Menengah
					</label>
					<label class="lbform">
						<?php echo form_radio(array('name'=>'qualification'),'besar',(set_radio('qualification','besar')||($this->form->get_temp_data('qualification')=='besar'))?TRUE:FALSE)?>Besar
					</label>
					<?php echo form_error('qualification'); ?>
				</td>
			</tr>
			<?php } ?>
			<tr class="input-form">
				<td><label>Masa Berlaku*</label></td>
				<td>
					<?php echo $this->form->lifetime_calendar(array('name'=>'expire_date','value'=>$this->form->get_temp_data('expire_date'), 'text_str' => "Selama Perusahaan Berdiri"), false);?>
					<?php echo form_error('expire_date'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Lampiran*</label></td>
				<td>
					<?php echo $this->form->file(array('name'=>'izin_file','value'=>($this->form->get_temp_data('izin_file'))?$this->form->get_temp_data('izin_file'):$izin_file));?>
					<?php //echo form_error('izin_file'); ?>
				</td>
			</tr>
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="simpan">
			<input type="submit" value="Kembali" class="btnBlue" name="back">
		</div>
	</form>
</div>