<?php echo $this->session->flashdata('msgSuccesss')?>
<div class="formDashboard">
	<h1 class="formHeader">Auction</h1>
	
		<table>
			<tr class="input-form">
				<td><label>Nama Paket Pengadaan</label></td>
				<td>
					: <?php echo $name;?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Sumber Anggaran</label></td>
				<td>
					: <?php echo ($budget_source=='perusahaan')?'Perusahaan':'Non-Perusahaan';?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Pejabat Pengadaan</label></td>
				<td>
					: <?php echo $pejabat_pengadaan_name;?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tipe Auction</label></td>
				<td>
					: <?php echo ($auction_type=='reverse_auction')?'Reverse Auction':'Forward Auction';?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Area Kerja</label></td>
				<td>
					: <?php echo ($work_area=='kantor_pusat')?'Kantor Pusat':'Site Office';?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Ruangan</label></td>
				<td>
					: <?php echo $room?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tanggal</label></td>
				<td>
					: <?php echo default_date($auction_date)?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Durasi Auction</label></td>
				<td>
					: <?php echo $auction_duration?>&nbsp;Menit
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Budget Holder</label></td>
				<td>
					: <?php echo $budget_holder_name?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Budget Spender</label></td>
				<td>
					: <?php echo $budget_spender_name?>
				</td>
			</tr>
		</table>
		<?php if($this->session->userdata('admin')['id_role']==6){ ?>
		<div class="buttonRegBox clearfix">
			<a href="<?php echo site_url('auction/edit/'.$id);?>" class="btnBlue">Edit</a>
		</div>
		<?php } ?>
</div>
<div id="contentWrap">
<?php echo $table ?>
</div>