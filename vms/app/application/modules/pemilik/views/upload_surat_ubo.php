<div class="formDashboard">
	<h1 class="formHeader">Upload Surat UBO</h1>
	<form method="POST" enctype="multipart/form-data">
		<?php echo '<input type="hidden" name="'.$this->security->get_csrf_token_name().'" value="'.$this->security->get_csrf_hash().'" />'; ?>
		<table>
			<tr class="input-form">
				<td><label>Lampiran Surat UBO</label></td>
				<td>
				<?php echo $this->form->file(array('name'=>'ubo_file','value'=>(($this->form->get_temp_data('ubo_file'))?$this->form->get_temp_data('ubo_file'):(isset($ubo_file) ? $ubo_file : ''))));?>
				<!--<?php if(isset($ubo_file) && $ubo_file!=''){ ?>
					<p><a href="<?php echo base_url('lampiran/ubo_file/'.$ubo_file)?>" target="_blank">Lampiran</a></p>
					<p><b><i style="color: #D62E2E;">Tinggalkan kosong jika tidak diganti</i></b></p>
					<?php } ?>
					<input type="file" name="ubo_file" value="<?php echo ($this->form->get_temp_data('ubo_file'))?$this->form->get_temp_data('ubo_file'):(isset($ubo_file) ? $ubo_file : '');?>">
					<?php echo form_error('ubo_file'); ?>-->
				</td>
			</tr>
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Simpan">
		</div>
	</form>
</div>