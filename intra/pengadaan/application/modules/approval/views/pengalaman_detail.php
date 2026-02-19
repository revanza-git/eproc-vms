<?php echo $this->session->flashdata('msgSuccess')?>
<?php echo $this->data_process->generate_progress('agen',$id_data)?>
<div class="formDashboard">
	<h1 class="formHeader">Pabrikan/Keagenan/Distributor</h1>
	<table>
		
		<tr class="input-form">
			<td><label>Nama Paket Pekerjaan</label></td>
			<td>
				<?php echo $job_name;?>
			</td>
		</tr>
		<!--<tr class="input-form">
			<td><label>Bidang Pekerjaan</label></td>
			<td>
				<?php echo $bidang;?>
			</td>
		</tr>
		<tr class="input-form">
			<td><label>Sub Bidang Pekerjaan</label></td>
			<td>
				<?php echo $sub_bidang;?>
			</td>
		</tr>-->
		<tr class="input-form">
			<td><label>Lokasi</label></td>
			<td>
				<?php echo $job_location;?>
			</td>
		</tr>
		<tr class="input-form">
			<td><label>Pemberi Tugas</label></td>
			<td>
				<?php echo $job_giver;?>
			</td>
		</tr>
		<tr class="input-form">
			<td><label>No. Telp Pemberi Tugas</label></td>
			<td>
				<?php echo $phone_no;?>
			</td>
		</tr>
		<tr class="input-form">
			<td><label>No. Kontrak</label></td>
			<td>
				<?php echo $contract_no;?>
			</td>
		</tr>
		<tr class="input-form">
			<td><label>Tanggal Kontrak</label></td>
			<td>
				<?php echo default_date($contract_start);?>
			</td>
		</tr>
		<tr class="input-form">
			<td><label>Nilai Kontrak (Rp)</label></td>
			<td>
				<?php echo $price_idr;?>
			</td>
		</tr>
		<tr class="input-form">
			<td><label>Nilai Kontrak (Kurs Asing)</label></td>
			<td>
				<?php echo $price_foreign;?>
			</td>
		</tr>
		<tr class="input-form">
			<td><label>Tanggal Selesai Sesuai Kontrak</label></td>
			<td>
				<?php echo default_date($contract_end);?>
			</td>
		</tr>
		<tr class="input-form">
			<td><label>Lampiran Dokumen Kontrak</label></td>
			<td>
				<?php if($contract_file!=''){ ?>
				<a href="<?php echo BASE_LINK_EXTERNAL.('lampiran/contract_file/');?><?php echo "/".$contract_file;?>">Lampiran</a>
				<?php }else{ ?>
				-
				<?php } ?>
			</td>
		</tr>
		<!--<tr class="input-form">
			<td><label>Tanggal Selesai Sesuai BAST</label></td>
			<td>
				<?php echo $bast_date;?>
			</td>
		</tr>
		<tr class="input-form">
			<td><label>Lampiran Dokumen BAST</label></td>
			<td>
				<?php echo $bast_file;?>
			</td>
		</tr>-->
	</table>

<div class="buttonRegBox clearfix">
	<a href="<?php echo site_url('approval/pengalaman/'.$id_pengalaman);?>" class="btnBlue"> Kembali</a>
</div>

</div>

