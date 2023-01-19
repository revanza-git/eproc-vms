<div class="tab procView">
	<?php echo $this->utility->tabNav($tabNav,'tatacara');?>
	
	<div class="tableWrapper" style="margin-bottom: 20px">
	<?php if($this->session->userdata('admin')['id_role']==6|7){ ?>
	<div class="btnTopGroup clearfix">
		<form method="POST" enctype="multipart/form-data">
			<h2>Tambah Tatacara</h2>
			<table>
				<tr class="input-form">
					<td><label>Pilih Metode Auction</label></td>
					<td>
						<label class="lbform">
							<?php echo form_radio(array('name'=>'metode_auction'),'posisi',(set_radio('metode_auction','posisi')||((isset($metode_auction)?$metode_auction:$this->form->get_temp_data('metode_auction'))=='posisi'))?TRUE:FALSE)?>Posisi/Ranking 
						</label>
						<label class="lbform">
							<?php echo form_radio(array('name'=>'metode_auction'),'indikator',(set_radio('metode_auction','indikator')||((isset($metode_auction)?$metode_auction:$this->form->get_temp_data('metode_auction'))=='indikator'))?TRUE:FALSE)?>Indikator
						</label>
						<?php echo form_error('metode_auction'); ?>
					</td>
				</tr>
				<tr class="input-form">
					<td><label>Pilih Metode Penawaran</label></td>
					<td>
						<label class="lbform">
							<?php echo form_radio(array('name'=>'metode_penawaran'),'lump_sum',(set_radio('metode_penawaran','lump_sum')||((isset($metode_penawaran)?$metode_penawaran:$this->form->get_temp_data('metode_penawaran'))=='lump_sum'))?TRUE:FALSE)?>Lump Sum 
						</label>
						<label class="lbform">
							<?php echo form_radio(array('name'=>'metode_penawaran'),'harga_satuan',(set_radio('metode_penawaran','harga_satuan')||((isset($metode_penawaran)?$metode_penawaran:$this->form->get_temp_data('metode_penawaran'))=='harga_satuan'))?TRUE:FALSE)?>Harga Satuan
						</label>
						<?php echo form_error('metode_penawaran'); ?>
					</td>
				</tr>
			</table>
			
			<div class="buttonRegBox clearfix">
				<input type="submit" value="Simpan" class="btnBlue" name="simpan">
			</div>
		</form>
	</div>
	<?php } ?>
</div>