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
<!-- <a href="<?php echo site_url('agen/tambah');?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a> -->
</div>
<div class="tableWrapper">
	<div class="filterBtnWp">
		<a href="<?php echo site_url('agen/tambah');?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a>
		<button class="editBtn lihatData filterBtn">Filter</button>
	</div>
	<div class="tableHeader">
		<!-- <form method="POST">
			<?php echo $filter_list;?>
		</form> -->	
	</div>
	<table class="tableData">
		<thead>
			<tr>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['no'] == 'asc') ? 'desc' : 'asc'; ?>&by=no">No.<i class="fa fa-sort-<?php echo ($sort['no'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['principal'] == 'asc') ? 'desc' : 'asc'; ?>&by=principal">Principal<i class="fa fa-sort-<?php echo ($sort['principal'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['issue_date'] == 'asc') ? 'desc' : 'asc'; ?>&by=issue_date">Tanggal<i class="fa fa-sort-<?php echo ($sort['issue_date'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['type'] == 'asc') ? 'desc' : 'asc'; ?>&by=type">Type<i class="fa fa-sort-<?php echo ($sort['type'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['expire_date'] == 'asc') ? 'desc' : 'asc'; ?>&by=expire_date">Masa Berlaku<i class="fa fa-sort-<?php echo ($sort['expire_date'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['agen_file'] == 'asc') ? 'desc' : 'asc'; ?>&by=agen_file">Lampiran<i class="fa fa-sort-<?php echo ($sort['agen_file'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				
				<td class="actionPanel">Action</td>
			</tr>
		</thead>
		<tbody>
		<?php 
		if(count($izin_list)){
			foreach($izin_list as $row => $value){
			?>
				<tr>
					<td><?php echo $value['no'];?></td>
					<td><?php echo $value['principal'];?></td>
					<td><?php echo default_date(date('d M Y',strtotime($value['issue_date'])));?></td>
					<td><?php echo $value['type'];?></td>
					<td><?php echo ($value['expire_date']=='lifetime')?'Seumur Hidup': default_date(date('d M Y',strtotime($value['expire_date'])));?></td>
					<td><a href="<?php echo base_url('lampiran/agen_file/'.$value['agen_file']);?>"  target="_blank"><?php echo $value['agen_file'];?> <i class="fa fa-link"></i></a></td>
					<td class="actionBlock">
						<!-- <a href="<?php echo site_url('agen/produk/'.$value['id'])?>">Produk / Merk</a> | 
						<a href="<?php echo site_url('agen/bsb/'.$value['id'])?>">Bidang & Sub bidang</a> |  -->
						<a href="<?php echo site_url('agen/edit/'.$value['id'])?>" class="editBtn"><i class="fa fa-cog"></i>Edit</a> | 
						<a href="<?php echo site_url('agen/hapus/'.$value['id'])?>" class="delBtn"><i class="fa fa-trash"></i>Hapus</a>
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