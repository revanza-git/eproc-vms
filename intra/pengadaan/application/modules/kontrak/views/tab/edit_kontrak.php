<div class="tab procView">
	<?php echo $this->utility->tabNav($tabNav,'kontrak');?>
	
	<div class="tableWrapper" style="margin-bottom: 20px;padding-left: 20px;">
	<?php echo $this->session->flashdata('msgSuccess')?>
		
		<div class="formDashboard">
			<form method="POST" enctype="multipart/form-data">
				<table>			
					<tr class="input-form">
						<td><label>Nama Perusahaan* </label></td>
						<td>
							<?php echo $winner['winner_name'];?>
							<?php echo form_error('id_vendor'); ?>
							<input type="hidden" name="id_vendor" value="<?php echo $winner['id_vendor']?>">
						</td>
					</tr>	
					<tr class="input-form">
						<td><label>No. Kontrak</label></td>
						<td>
							<input type="text" name="no_contract" value="<?php echo ($this->form->get_temp_data('no_contract'))?$this->form->get_temp_data('no_contract'):$no_contract;?>">
							<?php echo form_error('no_contract'); ?>
						</td>
					</tr>
					<tr class="input-form">
						<td><label>Kurs</label></td>
						<td><?php 
							if(count($kurs)>0){
								echo form_dropdown('id_kurs', $kurs, ($this->form->get_temp_data('id_kurs'))?$this->form->get_temp_data('id_kurs'):$id_kurs,'');
							}else{
								echo '-- Belum Ada Kurs Yang Dipilih --';
							}?>
							<?php echo form_error('id_kurs'); ?>
							
						</td>
					</tr>
					<tr class="input-form">
						<td><label>Nilai Kontrak / PO*</label></td>
						<td>
							<input type="text" class="money-masked" name="contract_price" value="<?php echo ($this->form->get_temp_data('contract_price'))?$this->form->get_temp_data('contract_price'):$contract_price;?>" maxlength="20">
							<?php echo form_error('contract_price'); ?>
						</td>
					</tr>
					<tr class="input-form">
						<td><label>Tanggal Kontrak</label></td>
						<td>
							<div>Dari Tanggal <?php echo $this->form->calendar(array('name'=>'start_contract','value'=>($this->form->get_temp_data('start_contract'))?$this->form->get_temp_data('start_contract'):$start_contract), false);?></div>
							<div style="margin-top: 10px;">Sampai Tanggal <?php echo $this->form->calendar(array('name'=>'end_contract','value'=>($this->form->get_temp_data('end_contract'))?$this->form->get_temp_data('end_contract'):$end_contract), false);?></div>
							<?php echo form_error('start_contract'); ?>
							<?php echo form_error('end_contract'); ?>
						</td>
					</tr>
					<tr class="input-form">
						<td><label>Dokumen Kontrak</label></td>
						<td>
							<?php echo $this->form->file(array('name'=>'po_file','value'=>($this->form->get_temp_data('po_file'))?$this->form->get_temp_data('po_file'):$po_file));?>
							<?php echo form_error('po_file'); ?>
						</td>
					</tr>
				</table>
				<div class="buttonRegBox clearfix">
					<input type="submit" value="Simpan" class="btnBlue" name="Update">
				</div>
			</form>
		</div>
	</div>
</div>