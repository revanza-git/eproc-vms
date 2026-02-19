<?php 
	if($this->utility->check_administrasi()>0){
		?>
	<p class="noticeMsg">Harap melengkapi data administrasi Penyedia Barang &amp; Jasa.<br>Pilih menu Administrasi di samping atau klik <a href="<?php echo site_url('administrasi');?>">disini</a></p>
		<?php
	}
?>
<?php echo $this->session->flashdata('msgSuccess')?>
<?php echo $this->session->flashdata('klasifikasi')?>
<div class="btnTopGroup clearfix">
	<!-- <a href="<?php echo site_url('pengalaman/pengisian_data');?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a> -->
</div>
	
<div class="tableWrapper">
	<div class="filterBtnWp">
		<a href="<?php echo site_url('pengalaman/pengisian_data');?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a>
		<button class="editBtn lihatData filterBtn">Filter</button>
	</div>
	<div class="tableHeader">
		<!-- <form method="POST">
			<?php echo $filter_list;?>
		</form> -->
	</div>
	<table class="tableData" style="min-width: 1500px; max-width: 1800px">
		<thead>
			<tr>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['job_name'] == 'asc') ? 'desc' : 'asc'; ?>&by=job_name">Nama Paket Pekerjaan<i class="fa fa-sort-<?php echo ($sort['job_name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<!--<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['id_bidang'] == 'asc') ? 'desc' : 'asc'; ?>&by=id_bidang">Bidang Pekerjaan<i class="fa fa-sort-<?php echo ($sort['id_bidang'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['id_sub_bidang'] == 'asc') ? 'desc' : 'asc'; ?>&by=id_sub_bidang">Sub Bidang Pekerjaan<i class="fa fa-sort-<?php echo ($sort['id_sub_bidang'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>-->
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['job_location'] == 'asc') ? 'desc' : 'asc'; ?>&by=job_location">Lokasi<i class="fa fa-sort-<?php echo ($sort['job_location'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['job_giver'] == 'asc') ? 'desc' : 'asc'; ?>&by=job_giver">Pemberi Tugas<i class="fa fa-sort-<?php echo ($sort['job_giver'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['phone_no'] == 'asc') ? 'desc' : 'asc'; ?>&by=phone_no">No. Telp. Pemberi Tugas<i class="fa fa-sort-<?php echo ($sort['phone_no'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td style="width: 75px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['contract_no'] == 'asc') ? 'desc' : 'asc'; ?>&by=contract_no">No. Kontrak<i class="fa fa-sort-<?php echo ($sort['contract_no'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['contract_start'] == 'asc') ? 'desc' : 'asc'; ?>&by=contract_start">Tanggal Mulai Kontrak<i class="fa fa-sort-<?php echo ($sort['contract_start'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['price_idr'] == 'asc') ? 'desc' : 'asc'; ?>&by=price_idr">Nilai Kontrak (Rp)<i class="fa fa-sort-<?php echo ($sort['price_idr'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['price_foreign'] == 'asc') ? 'desc' : 'asc'; ?>&by=price_foreign">Nilai Kontrak (Kurs Asing)<i class="fa fa-sort-<?php echo ($sort['price_foreign'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['contract_end'] == 'asc') ? 'desc' : 'asc'; ?>&by=contract_end">Tanggal Selesai Sesuai Kontrak<i class="fa fa-sort-<?php echo ($sort['contract_end'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['contract_file'] == 'asc') ? 'desc' : 'asc'; ?>&by=contract_file">Lampiran Dokumen Kontrak<i class="fa fa-sort-<?php echo ($sort['contract_file'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td class="actionPanel" style="width: 85px">Action</td>
			</tr>
		</thead>
		<tbody>
		<?php 
		if(count($izin_list)){
			foreach($izin_list as $row => $value){
			?>
				<tr>
					<td><?php echo $value['job_name'];?></td>
					<!--<td><?php echo $value['bidang_name'];?></td>
					<td><?php echo $value['sub_bidang_name'];?></td>-->
					<td><?php echo $value['job_location'];?></td>
					<td><?php echo $value['job_giver'];?></td>
					<td><?php echo $value['phone_no'];?></td>
					<td><?php echo $value['contract_no'];?></td>
					<td><?php echo (strtotime($value['contract_start']) >0) ? default_date($value['contract_start']) : "-";?></td>
					<td><?php echo $value['price_idr'];?></td>
					<td><?php echo number_format($value['currency']).' '.number_format($value['price_foreign']);?></td>
					<td><?php echo (strtotime($value['contract_end']) >0) ? default_date($value['contract_end']) : "-";?></td>
					<td><a href="<?php echo base_url('lampiran/contract_file/'.$value['contract_file']);?>"  target="_blank"><?php echo $value['contract_file'];?> <i class="fa fa-link"></i></a></td>
				
					<td class="actionBlock">
						<a href="<?php echo site_url('pengalaman/edit/'.$value['id'])?>" class="editBtn"><i class="fa fa-cog"></i>Edit</a>
						<a href="<?php echo site_url('pengalaman/hapus/'.$value['id'])?>" class="delBtn"><i class="fa fa-trash"></i>Hapus</a>
					</td>
				</tr>
			<?php 
			}
		}else{?>
			<tr>
				<td colspan="11" class="noData">Data tidak ada</td>
			</tr>
		<?php }
		?>
		</tbody>
	</table>
	
</div>
<div class="pageNumber">
	<?php echo $pagination ?>
</div>
<div class="filterWrapperOverlay"></div>
<div class="filterWrapper">
	<div class="filterWrapperInner">
		<form method="POST">
			<?php echo $filter_list;?>
		</form>
	</div>
</div>