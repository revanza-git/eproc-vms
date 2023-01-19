<div class="tab procView">
	<?php echo $this->utility->tabNav($tabNav,'kontrak');?>
	
	<div class="tableWrapper" style="margin-bottom: 20px;padding-left: 20px;">
	<?php echo $this->session->flashdata('msgSuccess')?>
		
		<div class="formDashboard">
			<form method="POST" enctype="multipart/form-data">
				<table>
					<tr class="input-form">
						<td><label>Nama Perusahaan* :</label></td>
						<td>
							<?php echo $winner['winner_name'];?>
							<?php echo form_error('id_vendor'); ?>
							<input type="hidden" name="id_vendor" value="<?php echo $winner['id_vendor']?>">
						</td>
					</tr>
					
					<tr class="input-form">
						<td><label>No. SPPBJ</label></td>
						<td>
							<input type="text" name="no_sppbj" value="<?php echo ($this->form->get_temp_data('no_sppbj'))?$this->form->get_temp_data('no_sppbj'):$no_sppbj;?>">
							<?php echo form_error('no_sppbj'); ?>
						</td>
					</tr>
					<tr class="input-form">
						<td><label>Tanggal SPPBJ</label></td>
						<td>
							<?php echo (isset($sppbj_date)) ? default_date($sppbj_date) : '-';?>
							
						</td>
					</tr>
					<tr class="input-form">
						<td><label>No. SPMK</label></td>
						<td>
							<input type="text" name="no_spmk" value="<?php echo ($this->form->get_temp_data('no_spmk'))?$this->form->get_temp_data('no_spmk'):$no_spmk;?>">
							<?php echo form_error('no_spmk'); ?>
						</td>
					</tr>
					<tr class="input-form">
						<td><label>Tanggal SPMK</label></td>
						<td>
							<?php echo $this->form->calendar(array('name'=>'spmk_date','value'=>($this->form->get_temp_data('spmk_date'))?$this->form->get_temp_data('spmk_date'):$spmk_date), false);?>
							<?php echo form_error('spmk_date'); ?>
						</td>
					</tr>
					<tr class="input-form">
						<td><label>Tanggal Kontrak*</label></td>
						<td>
							<?php echo $this->form->calendar(array('name'=>'contract_date','value'=>($this->form->get_temp_data('contract_date'))?$this->form->get_temp_data('contract_date'):$contract_date), false);?>
							<?php echo form_error('contract_date'); ?>
						</td>
					</tr>
					<tr class="input-form">
						<td><label>No. Kontrak / PO*</label></td>
						<td>
							<div style="margin-bottom: 10px"><input type="text" name="no_contract" value="<?php echo ($this->form->get_temp_data('no_contract'))?$this->form->get_temp_data('no_contract'):$no_contract;?>"></div>
							<?php echo $this->form->file(array('name'=>'po_file','value'=>($this->form->get_temp_data('po_file'))?$this->form->get_temp_data('po_file'):$po_file));?>
							<?php echo form_error('no_contract'); ?>
						</td>
					</tr>
					<tr class="input-form">
						<td>
							Nilai Kontrak / PO*
						</td>
						<td>
							 Rp.<input type="text" class="money-masked" name="contract_price" value="<?php echo (isset($winner['idr_kontrak']))?$winner['idr_kontrak']:(($this->form->get_temp_data('contract_price'))?$this->form->get_temp_data('contract_price'):$contract_price);?>" maxlength="20">
						</td>
					</tr>
					<tr class="input-form">
						<td>
							
						</td>
						<td>
							<?php echo $this->form->get_kurs(array('name'=>'contract_kurs'),(isset($winner['id_kurs_kontrak']))?$winner['id_kurs_kontrak']:(($this->form->get_temp_data('contract_kurs'))?$this->form->get_temp_data('contract_kurs'):$contract_kurs))?><input type="text" name="contract_price_kurs" value="<?php echo (isset($winner['kurs_kontrak']))?$winner['kurs_kontrak']:(($this->form->get_temp_data('contract_price_kurs'))?$this->form->get_temp_data('contract_price_kurs'):$contract_price_kurs);?>" class="money-masked" maxlength="20">
							<?php echo (form_error('kurs_value'))?form_error('kurs_value'):form_error('kurs_value'); ?>
						</td>
					</tr>
					<tr class="input-form">
						<td><label>Jangka Waktu Pelaksanaan Pekerjaan</label></td>
						<td>
							<div>Dari Tanggal <?php echo $this->form->calendar(array('name'=>'start_work','value'=>($this->form->get_temp_data('start_work'))?$this->form->get_temp_data('start_work'):$start_work), false);?></div>
							<div style="margin-top: 10px;">Sampai Tanggal <?php echo $this->form->calendar(array('name'=>'end_work','value'=>($this->form->get_temp_data('end_work'))?$this->form->get_temp_data('end_work'):$end_work), false);?></div>
							<?php echo form_error('start_work'); ?>
							<?php echo form_error('end_work'); ?>
						</td>
					</tr>
					<tr class="input-form">
						<td><label>Jangka Waktu Kontrak*</label></td>
						<td>
							<div>Dari Tanggal <?php echo $this->form->calendar(array('name'=>'start_contract','value'=>($this->form->get_temp_data('start_contract'))?$this->form->get_temp_data('start_contract'):$start_contract), false);?></div>
							<div style="margin-top: 10px;">Sampai Tanggal <?php echo $this->form->calendar(array('name'=>'end_contract','value'=>($this->form->get_temp_data('end_contract'))?$this->form->get_temp_data('end_contract'):$end_contract), false);?></div>
							
							<?php echo form_error('start_contract'); ?>
							<?php echo form_error('end_contract'); ?>
							
						</td>
					</tr>
				</table>
				<div class="buttonRegBox clearfix">
					<input type="submit" value="Simpan" class="btnBlue" name="Simpan">
				</div>
			</form>
		</div>
	</div>
</div>
