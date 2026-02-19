<?php 
	if($this->utility->check_administrasi()>0){
		?>
	<p class="noticeMsg">Harap melengkapi data administrasi Penyedia Barang &amp; Jasa.<br>Pilih menu Administrasi di samping atau klik <a href="<?php echo site_url('administrasi');?>">disini</a></p>
		<?php
	}
?>
<?php echo $this->session->flashdata('msgSuccess')?>
<?php echo $this->session->flashdata('msgError')?>
<div class="btnTopGroup clearfix">
	<!-- <a href="<?php echo site_url('pengurus/tambah')?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a> -->
</div>

<div class="tableWrapper">
	<div class="filterBtnWp">
		<a href="<?php echo site_url('pengurus/tambah')?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a>
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
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['position'] == 'asc') ? 'desc' : 'asc'; ?>&by=position">Posisi<i class="fa fa-sort-<?php echo ($sort['position'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<!-- <td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['position_expire'] == 'asc') ? 'desc' : 'asc'; ?>&by=position_expire">Masa Berlaku Jabatan<i class="fa fa-sort-<?php echo ($sort['position_expire'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td> -->
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['no'] == 'asc') ? 'desc' : 'asc'; ?>&by=no">No. Identitas<i class="fa fa-sort-<?php echo ($sort['no'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>&by=name">Nama<i class="fa fa-sort-<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['expire_date'] == 'asc') ? 'desc' : 'asc'; ?>&by=expire_date">Masa Berlaku Identitas<i class="fa fa-sort-<?php echo ($sort['expire_date'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td style="width: 100px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['no_akta'] == 'asc') ? 'desc' : 'asc'; ?>&by=no_akta">Nomor Akta<i class="fa fa-sort-<?php echo ($sort['no_akta'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['pengurus_file'] == 'asc') ? 'desc' : 'asc'; ?>&by=pengurus_file">Lampiran<i class="fa fa-sort-<?php echo ($sort['pengurus_file'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td class="actionPanel" style="width: 160px">Action</td>
			</tr>
		</thead>
		<tbody>
		<?php 
		if(count($situ_list)){
			foreach($situ_list as $row => $value){
			?>
				<tr>
					<td><?php echo $value['position'];?></td>
					<!-- <td><?php echo $value['position_expire'];?></td> -->
					<td><?php echo $value['no_identitas'];?></td>
					<td><?php echo $value['name'];?></td>
					<td><?php echo ($value['expire_date']=='lifetime')?'Seumur Hidup':default_date($value['expire_date']);?></td>
					<td><?php echo $value['no_akta'];?></td>
					<td>
						<?php if (count($value['pengurus_file'])) {
							# code...
						?>
						<a href="<?php echo base_url('lampiran/pengurus_file/'.$value['pengurus_file']);?>"  target="_blank">
							<?php echo $value['pengurus_file'];?> <i class="fa fa-link"> </i>
						</a>
						<?php
						} else {
							echo "-";
						}
						?>
					</td>
					<td class="actionBlock">
						<a href="<?php echo site_url('pengurus/edit/'.$value['id'])?>" class="editBtn"><i class="fa fa-cog"></i>Edit</a>
						<a style="top: -4px" href="<?php echo site_url('pengurus/hapus/'.$value['id'])?>" class="delBtn"><i class="fa fa-trash"></i>Hapus</a>
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