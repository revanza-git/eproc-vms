<div class="formDashboard">
	<h1 class="formHeader">Edit Auction</h1>
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
			<tr class="input-form">
				<td><label>Pejabat Pengadaan*</label></td>
				<td>
					<?php echo form_dropdown('id_pejabat_pengadaan', $pejabat_pengadaan, $this->form->get_temp_data('id_pejabat_pengadaan'),'');?>
					<?php echo form_error('id_pejabat_pengadaan'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tipe Auction</label></td>
				<td>
					<div class="clearfix">
						<label class="lbform">
							<?php echo form_radio(array('name'=>'auction_type'),'reverse_auction',(set_radio('auction_type','reverse_auction')||((isset($auction_type)?$auction_type:$this->form->get_temp_data('auction_type'))=='reverse_auction'))?TRUE:FALSE)?>Reverse Auction 
						</label>
						<label class="lbform">
							<?php echo form_radio(array('name'=>'auction_type'),'forward_auction',(set_radio('auction_type','forward_auction')||((isset($auction_type)?$auction_type:$this->form->get_temp_data('auction_type'))=='forward_auction'))?TRUE:FALSE)?>Foward Auction
						</label>
					</div>
					<?php echo form_error('auction_type'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Lokasi*</label></td>
				<td>
						<table>
							<tr class="input-form">
								<td>Area Kerja*</td>
							</tr>
							<tr class="input-form">
								<td>
									<div class="clearfix">
										<label class="lbform">
											<?php echo form_radio(array('name'=>'work_area'),'kantor_pusat',(set_radio('work_area','kantor_pusat')||((isset($work_area)?$work_area:$this->form->get_temp_data('work_area'))=='kantor_pusat'))?TRUE:FALSE)?>Kantor Pusat
										</label>
										<label class="lbform">
											<?php echo form_radio(array('name'=>'work_area'),'site_office',(set_radio('work_area','site_office')||((isset($work_area)?$work_area:$this->form->get_temp_data('work_area'))=='site_office'))?TRUE:FALSE)?>Site Office
										</label>
									</div>
									<?php echo form_error('work_area'); ?>
								</td>
							</tr>
							<tr class="input-form">
								<td>Ruangan*</td>
							</tr>
							<tr>
								<td>
									<input type="text" name="room" value="<?php echo ($this->form->get_temp_data('room'))?$this->form->get_temp_data('room'):$room;?>">
									<?php echo form_error('room'); ?>
								</td>
							</tr>
							<tr class="input-form">
								<td>Tanggal*</td>
							</tr>
							<tr>
								<td>
									<?php echo $this->form->calendar(array('name'=>'auction_date','value'=>(isset($auction_date)?$auction_date:$this->form->get_temp_data('auction_date')), false));?>
									<?php echo form_error('auction_date'); ?>
								</td>
							</tr>
						</table>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Durasi Auction*</label></td>
				<td>
					<input type="text" name="auction_duration" value="<?php echo ($this->form->get_temp_data('auction_duration'))?$this->form->get_temp_data('auction_duration'):$auction_duration;?>" class="col-14">&nbsp;Menit
					<?php echo form_error('auction_duration'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Budget Holder*</label></td>
				<td>
					<?php echo form_dropdown('budget_holder', $budget_holder_list, $this->form->get_temp_data('budget_holder'),'');?>
					<?php echo form_error('budget_holder'); ?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Budget Spender*</label></td>
				<td>
					<?php echo form_dropdown('budget_spender', $budget_spender_list, $this->form->get_temp_data('budget_spender'),'');?>
					<?php echo form_error('budget_spender'); ?>
				</td>
			</tr>
		</table>
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Update">
		</div>
	</form>
</div>