<div class="formDashboard">
	<h1 class="formHeader">Ubah Pengadaan</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Nama Paket Pengadaan* :</label></td>
				<td>
					<input type="text" name="name" value="<?php echo ($this->form->get_temp_data('name'))?$this->form->get_temp_data('name'):$name;?>">
					<?php echo form_error('name'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>*SPMK</label></td>
			</tr>
			<tr class="input-form">
				<td><label>Nomor :</label></td>
				<td>
					<input type="text" name="no_spmk" value="<?php echo ($this->form->get_temp_data('no_spmk'))?$this->form->get_temp_data('no_spmk'):$no_spmk;?>">
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tanggal :</label></td>
				<td>
					<div>Dari Tanggal <?php echo $this->form->calendar(array('name'=>'start_spmk_date','value'=>($this->form->get_temp_data('start_spmk_date'))?$this->form->get_temp_data('start_spmk_date'):$start_spmk_date), false);?></div>

					<div style="margin-top: 10px;">Sampai Tanggal <?php echo $this->form->calendar(array('name'=>'end_spmk_date','value'=>($this->form->get_temp_data('end_spmk_date'))?$this->form->get_temp_data('end_spmk_date'):$end_spmk_date), false);?></div>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Dokumen :</label></td>
				<td>
					<?php echo $this->form->file(array('name'=>'spmk_file','value'=>($this->form->get_temp_data('spmk_file'))?$this->form->get_temp_data('spmk_file'):$spmk_file));?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>*Contract Formation</label></td>
			</tr>
			<tr class="input-form">
				<td><label>Nomor :</label></td>
				<td>
					<input type="text" name="no_cf" value="<?php echo ($this->form->get_temp_data('no_cf'))?$this->form->get_temp_data('no_cf'):$no_cf;?>">
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tanggal :</label></td>
				<td>
					<div>Dari Tanggal <?php echo $this->form->calendar(array('name'=>'start_cf_date','value'=>($this->form->get_temp_data('start_cf_date'))?$this->form->get_temp_data('start_cf_date'):$start_cf_date), false);?></div>

					<div style="margin-top: 10px;">Sampai Tanggal <?php echo $this->form->calendar(array('name'=>'end_cf_date','value'=>($this->form->get_temp_data('end_cf_date'))?$this->form->get_temp_data('end_cf_date'):$end_cf_date), false);?></div>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Dokumen :</label></td>
				<td>
					<?php echo $this->form->file(array('name'=>'contract_formation_file','value'=>($this->form->get_temp_data('contract_formation_file'))?$this->form->get_temp_data('contract_formation_file'):$contract_formation_file));?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Jenis Pengadaan* :</label></td>
				<td>
					<?php echo form_dropdown('tipe_pengadaan', $ttr, ($this->form->get_temp_data('tipe_pengadaan')) ? $this->form->get_temp_data('tipe_pengadaan') : $tipe_pengadaan, ''); ?>
					
					<?php echo form_error('tipe_pengadaan'); ?>
				</td>
			</tr>
			<tr class="input-form">
					<td>
						Nilai HPS: 
					</td>
					<td>
						 Rp.<input type="text" name="idr_value" class="money-masked" value="<?php echo ($this->form->get_temp_data('idr_value'))?$this->form->get_temp_data('idr_value'):$idr_value;?>">
					</td>
				</tr>
				<tr class="input-form">
					<td>
						
					</td>
					<td>
						<?php echo $this->form->get_kurs(array('name'=>'id_kurs'),($this->form->get_temp_data('id_kurs'))?$this->form->get_temp_data('id_kurs'):$id_kurs)?><input type="text" name="kurs_value" class="money-masked" value="<?php echo ($this->form->get_temp_data('kurs_value'))?$this->form->get_temp_data('kurs_value'):$kurs_value;?>">
						<?php echo (form_error('idr_value'))?form_error('kurs_value'):form_error('kurs_value'); ?>
					</td>
				</tr>
			<tr class="input-form">
				<td><label>Sumber Anggaran*</label></td>
				<td>
					<div class="clearfix">
						<label class="lbform">
							<?php echo form_radio(array('name'=>'budget_source'),'perusahaan',(set_radio('budget_source','perusahaan')||((isset($budget_source)?$budget_source:$this->form->get_temp_data('budget_source'))=='perusahaan'))?TRUE:FALSE)?>Perusahaan 
						</label>
						<label class="lbform">
							<?php echo form_radio(array('name'=>'budget_source'),'non_perusahaan',(set_radio('budget_source','non_perusahaan')||((isset($budget_source)?$budget_source:$this->form->get_temp_data('budget_source'))=='non_perusahaan'))?TRUE:FALSE)?>Non-Perusahaan
						</label>
					</div>
					<?php echo form_error('budget_source'); ?>
				</td>
			</tr>
			<!--<tr class="input-form">
				<td><label>No. BAHP*</label></td>
				<td>
					<input type="text" name="no_bahp" value="<?php echo ($this->form->get_temp_data('no_bahp'))?$this->form->get_temp_data('no_bahp'):$no_bahp;?>">
					<?php echo form_error('no_bahp'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tanggal BAHP*</label></td>
				<td>
					<?php echo $this->form->calendar(array('name'=>'bahp_date','value'=>($this->form->get_temp_data('bahp_date'))?$this->form->get_temp_data('bahp_date'):$bahp_date), false);?>
					<?php echo form_error('bahp_date'); ?>
				</td>
			</tr>-->
			<tr class="input-form">
				<td><label>Pejabat Pengadaan*</label></td>
				<td>
					<?php echo form_dropdown('id_pejabat_pengadaan', $pejabat_pengadaan, ($this->form->get_temp_data('id_pejabat_pengadaan'))?$this->form->get_temp_data('id_pejabat_pengadaan'):$id_pejabat_pengadaan,'');?>
					<?php echo form_error('id_pejabat_pengadaan'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tahun Anggaran*</label></td>
				<td>
					<input type="text" name="budget_year" value="<?php echo ($this->form->get_temp_data('budget_year'))?$this->form->get_temp_data('budget_year'):$budget_year;?>" class="col-14">
					<?php echo form_error('budget_year'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Budget Holder*</label></td>
				<td>
					<?php echo form_dropdown('budget_holder', $budget_holder_list, ($this->form->get_temp_data('budget_holder'))?$this->form->get_temp_data('budget_holder'):$budget_holder,'');?>
					<?php echo form_error('budget_holder'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Pemegang Cost Center*</label></td>
				<td>
					<?php echo form_dropdown('budget_spender', $budget_spender_list, ($this->form->get_temp_data('budget_spender'))?$this->form->get_temp_data('budget_spender'):$budget_spender,'');?>
					<?php echo form_error('budget_spender'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Metode Pengadaan*</label></td>
				<td>
					<?php echo form_dropdown('id_mekanisme', $id_mekanisme_list, ($this->form->get_temp_data('id_mekanisme'))?$this->form->get_temp_data('id_mekanisme'):$id_mekanisme,'');?>
					<?php echo form_error('id_mekanisme'); ?>
				</td>
			</tr>
			<tr  class="input-form">
				<td><label>Metode Evaluasi*</label></td>
				<td>

					<?php 
					echo form_dropdown('evaluation_method', $evaluation_method_list, ($this->form->get_temp_data('evaluation_method'))?$this->form->get_temp_data('evaluation_method'):$evaluation_method,'')?>
					<?php echo form_error('evaluation_method'); ?>
				</td>

			</tr>
			<tr id="evaluation_method_desc_div" class="input-form">
				<td><label>Metode Evaluasi Scoring*</label></td>
				<td>

					<?php 
					echo form_dropdown('evaluation_method_desc', $evaluation_method_desc_list, ($this->form->get_temp_data('evaluation_method_desc'))?$this->form->get_temp_data('evaluation_method_desc'):$evaluation_method_desc,'');?>
					<?php echo form_error('evaluation_method_desc'); ?>
				</td>

			</tr>
			<!--<tr class="input-form">
				<td><label>Harga Penawaran Hasil Auction*</label></td>
				<td>
					<div style="margin-bottom: 10px">Dalam Rupiah : <input type="text" class="money-masked" name="price_auction" value="<?php echo ($this->form->get_temp_data('price_auction'))?$this->form->get_temp_data('price_auction'):$price_auction;?>" maxlength="20"></div>
					<?php echo form_error('price_auction'); ?>
					<div style="margin-bottom: 10px">Dalam Mata Uang Asing : <?php echo $this->form->get_kurs('auction_kurs',($this->form->get_temp_data('auction_kurs'))?$this->form->get_temp_data('auction_kurs'):$auction_kurs)?><input type="text" name="price_auction_kurs" value="<?php echo ($this->form->get_temp_data('price_auction_kurs'))?$this->form->get_temp_data('price_auction_kurs'):$price_auction_kurs;?>" class="col-14 money-masked" maxlength="20"></div>
					<?php echo form_error('auction_kurs'); ?>
					<?php echo form_error('price_auction_kurs'); ?>
				</td>
			</tr>
			
			<tr class="input-form">
				<td><label>Harga Penawaran Setelah Negosiasi*</label></td>
				<td>
					<div style="margin-bottom: 10px">Dalam Rupiah : <input type="text" class="money-masked" name="price_nego" value="<?php echo ($this->form->get_temp_data('price_nego'))?$this->form->get_temp_data('price_nego'):$price_nego;?>" maxlength="20"></div>
					<?php echo form_error('price_nego'); ?>
					<div style="margin-bottom: 10px">Dalam Mata Uang Asing : <?php echo $this->form->get_kurs('nego_kurs',($this->form->get_temp_data('nego_kurs'))?$this->form->get_temp_data('nego_kurs'):$nego_kurs); ?><input type="text" name="price_nego_kurs" value="<?php echo ($this->form->get_temp_data('price_nego_kurs'))?$this->form->get_temp_data('price_nego_kurs'):$price_nego_kurs;?>" class="col-14 money-masked" maxlength="20"></div>
					<?php echo form_error('nego_kurs'); ?>
					<?php echo form_error('price_nego_kurs'); ?>
				</td>
			</tr>-->
		</table>
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Simpan">
		</div>
	</form>
</div>