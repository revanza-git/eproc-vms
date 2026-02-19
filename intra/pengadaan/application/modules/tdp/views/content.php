<?php 
	if($this->utility->check_administrasi()>0){
		?>
	<p class="noticeMsg">Harap melengkapi data administrasi vendor.<br>Pilih menu Administrasi di samping atau klik <a href="<?php echo site_url('administrasi');?>">disini</a></p>
		<?php
	}
?>
<?php echo $this->session->flashdata('msgSuccess')?>
<?php echo $this->session->flashdata('msgError')?>
<div class="btnTopGroup clearfix">
	<!-- <a href="<?php echo site_url('tdp/tambah')?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a> -->
</div>

<div class="tableWrapper">
	<div class="filterBtnWp">
		<a href="<?php echo site_url('tdp/tambah')?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a>
		<button class="editBtn lihatData filterBtn">Filter</button>
	</div>
	<div class="tableHeader">
		<!-- <form method="POST">
			<?php echo $filter_list;?>
		</form>	 -->
	</div>
	<table class="tableData">
		<thead>
			<tr>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['no'] == 'asc') ? 'desc' : 'asc'; ?>&by=no">Nomor NIB/TDP<i class="fa fa-sort-<?php echo ($sort['no'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['issue_date'] == 'asc') ? 'desc' : 'asc'; ?>&by=issue_date">Tanggal<i class="fa fa-sort-<?php echo ($sort['issue_date'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['authorize_by'] == 'asc') ? 'desc' : 'asc'; ?>&by=authorize_by">Lambaga Penerbit<i class="fa fa-sort-<?php echo ($sort['authorize_by'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td style="width: 100px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['expiry_date'] == 'asc') ? 'desc' : 'asc'; ?>&by=expiry_date">Masa Berlaku<i class="fa fa-sort-<?php echo ($sort['expiry_date'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['tdp_file'] == 'asc') ? 'desc' : 'asc'; ?>&by=tdp_file">Lampiran NIB/TDP<i class="fa fa-sort-<?php echo ($sort['tdp_file'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td style="width: 100px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['extension_file'] == 'asc') ? 'desc' : 'asc'; ?>&by=extension_file">Lampiran Perpanjangan<i class="fa fa-sort-<?php echo ($sort['extension_file'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td class="actionPanel" style="width: 160px">Action</td>
			</tr>
		</thead>
		<tbody>
		<?php 
		if(count($tdp_list)){
			foreach($tdp_list as $row => $value){
			?>
				<tr>
					<td><?php echo $value['no'];?></td>
					<td><?php echo ($value['issue_date'] != "") ? default_date($value['issue_date']) : "-";?></td>
					<td><?php echo $value['authorize_by'];?></td>
					<td>
						<?php $expire = "";?>
						<?php ($value['expiry_date'] == "lifetime") ? $expire = "Seumur Hidup" : $expire = default_date($value['expiry_date']);?>
						<?php echo $expire; ?>
					</td>
					<td><a href="<?php echo base_url('lampiran/tdp_file/'.$value['tdp_file']);?>"  target="_blank"><?php echo $value['tdp_file'];?> <i class="fa fa-link"></i></a></td>
					<td><?php if($value['extension_file']){?>
					<a href="<?php echo base_url('lampiran/extension_file/'.$value['extension_file']);?>"  target="_blank"><?php echo $value['extension_file'];?> <i class="fa fa-link"></i></a><?php } ?></td>
					<td class="actionBlock">
						<a href="<?php echo site_url('tdp/edit/'.$value['id'])?>" class="editBtn"><i class="fa fa-cog"></i>Edit</a>
						<a style="top: -4px" href="<?php echo site_url('tdp/hapus/'.$value['id'])?>" class="delBtn"><i class="fa fa-trash"></i>Hapus</a>
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
	<form method="POST">
		<?php echo $filter_list;?>
	</form>
</div>