<div class="tab procView">
	<?php echo $this->utility->tabNav($tabNav,'kontrak');?>
	
	<div class="tableWrapper" style="margin-bottom: 20px;padding-left: 20px;">
		
			<div class="formDashboard">
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Nama Perusahaan* :</label></td>
				<td>
					<?php echo form_dropdown('id_vendor', $vendor_list, ($this->form->get_temp_data('id_vendor'))?$this->form->get_temp_data('id_vendor'):$id_vendor,'');?>
					<?php echo form_error('id_vendor'); ?>
				</td>
			</tr>
			
			<tr class="input-form">
				<td><label>No. SPPBJ*</label></td>
				<td>
					<input type="text" name="no_sppbj" value="<?php echo ($this->form->get_temp_data('no_sppbj'))?$this->form->get_temp_data('no_sppbj'):$no_sppbj;?>">
					<?php echo form_error('no_sppbj'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tanggal SPPBJ*</label></td>
				<td>
					<?php echo $this->form->calendar(array('name'=>'sppbj_date','value'=>($this->form->get_temp_data('sppbj_date'))?$this->form->get_temp_data('sppbj_date'):$sppbj_date, false));?>
					<?php echo form_error('sppbj_date'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>No. SPMK*</label></td>
				<td>
					<input type="text" name="no_spmk" value="<?php echo ($this->form->get_temp_data('no_spmk'))?$this->form->get_temp_data('no_spmk'):$no_spmk;?>">
					<?php echo form_error('no_spmk'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tanggal SPMK*</label></td>
				<td>
					<?php echo $this->form->calendar(array('name'=>'spmk_date','value'=>($this->form->get_temp_data('spmk_date'))?$this->form->get_temp_data('spmk_date'):$spmk_date), false);?>
					<?php echo form_error('spmk_date'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Periode Kerja*</label></td>
				<td>
					<div>Dari Tanggal <?php echo $this->form->calendar(array('name'=>'start_work','value'=>($this->form->get_temp_data('start_work'))?$this->form->get_temp_data('start_work'):$start_work), false);?></div>
					<div style="margin-top: 10px;">Sampai Tanggal <?php echo $this->form->calendar(array('name'=>'end_work','value'=>($this->form->get_temp_data('end_work'))?$this->form->get_temp_data('end_work'):$end_work), false);?></div>
					<?php echo form_error('start_work'); ?>
					<?php echo form_error('end_work'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>No. Kontrak / PO*</label></td>
				<td>
					<div style="margin-bottom: 10px"><input type="text" name="no_contract" value="<?php echo ($this->form->get_temp_data('no_contract'))?$this->form->get_temp_data('no_contract'):$no_contract;?>"></div>
					
					<?php echo form_error('no_contract'); ?>
					
					<p><a href="<?php echo base_url('lampiran/po_file/'.$po_file)?>" target="_blank">Lampiran</a></p>
					<p><b><i style="color: #D62E2E;">Tinggalkan kosong jika tidak diganti</i></b></p>
					<input type="file" name="po_file" value="<?php echo ($this->form->get_temp_data('po_file'))?$this->get_temp_data('po_file'):$po_file;?>">
					<?php echo form_error('po_file'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Nilai Kontrak / PO*</label></td>
				<td>
					<div style="margin-bottom: 10px">Dalam Rupiah : <input type="text" class="money-masked" name="contract_price" value="<?php echo ($this->form->get_temp_data('contract_price'))?$this->form->get_temp_data('contract_price'):$contract_price;?>" maxlength="20"></div>
					<?php echo form_error('contract_price'); ?>
					<div style="margin-bottom: 10px">Dalam Mata Uang Asing : <?php echo $this->form->get_kurs(array('name'=>'contract_kurs'),(($this->form->get_temp_data('contract_kurs'))?$this->form->get_temp_data('contract_kurs'):$contract_kurs)?$this->form->get_temp_data('contract_kurs'):$contract_kurs)?><input type="text" name="contract_price_kurs" value="<?php echo ($this->form->get_temp_data('contract_price_kurs'))?$this->form->get_temp_data('contract_price_kurs'):$contract_price_kurs;?>" class="col-14 money-masked" maxlength="20"></div>
					<?php echo form_error('contract_kurs'); ?>
					<?php echo form_error('contract_price_kurs'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Periode Kontrak / PO*</label></td>
				<td>
					<div>Dari Tanggal <?php echo $this->form->calendar(array('name'=>'start_contract','value'=>$this->form->get_temp_data('start_contract')?$this->form->get_temp_data('start_contract'):$start_contract), false);?></div>
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
