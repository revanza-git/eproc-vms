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

	<!-- <a href="<?php echo site_url('akta/tambah')?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a> -->

</div>

<div class="filterBtnWp" style="margin:15px 0;">
	<a href="<?php echo site_url('akta/tambah')?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a>
	<button class="editBtn lihatData filterBtn">Filter</button>
</div>

<div class="tableWrapper">

	<div class="tableHeader">

		<!-- <form method="POST">

			<?php echo $filter_list;?>

		</form>	 -->	

	</div>

	<table class="tableData" style="min-width: 1500px; max-width: 2000px">

		<thead>

			<tr>

				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['type'] == 'asc') ? 'desc' : 'asc'; ?>&by=type">Akta<i class="fa fa-sort-<?php echo ($sort['type'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['notaris'] == 'asc') ? 'desc' : 'asc'; ?>&by=notaris">Notaris<i class="fa fa-sort-<?php echo ($sort['notaris'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td width="120"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['no'] == 'asc') ? 'desc' : 'asc'; ?>&by=no">No. Akta<i class="fa fa-sort-<?php echo ($sort['no'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['issue_date'] == 'asc') ? 'desc' : 'asc'; ?>&by=issue_date">Tanggal<i class="fa fa-sort-<?php echo ($sort['issue_date'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['akta_file'] == 'asc') ? 'desc' : 'asc'; ?>&by=akta_file">Lampiran Akta<i class="fa fa-sort-<?php echo ($sort['akta_file'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['authorize_by'] == 'asc') ? 'desc' : 'asc'; ?>&by=authorize_by">Lembaga Pengesah<i class="fa fa-sort-<?php echo ($sort['authorize_by'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['authorize_no'] == 'asc') ? 'desc' : 'asc'; ?>&by=authorize_no">No. Pengesahan<i class="fa fa-sort-<?php echo ($sort['authorize_no'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td width="100"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['authorize_date'] == 'asc') ? 'desc' : 'asc'; ?>&by=authorize_date">Tanggal Pengesahan<i class="fa fa-sort-<?php echo ($sort['authorize_date'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['authorize_file'] == 'asc') ? 'desc' : 'asc'; ?>&by=authorize_file">Lampiran Pengesahan Akta<i class="fa fa-sort-<?php echo ($sort['authorize_file'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td width="110"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['authorize_file'] == 'asc') ? 'desc' : 'asc'; ?>&by=authorize_file">Lampiran Bukti Perpanjangan<i class="fa fa-sort-<?php echo ($sort['file_extension_akta'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td class="actionPanel" style="width: 100px">Action</td>

			</tr>

		</thead>

		<tbody>

		<?php 

		if(count($akta_list)){

			foreach($akta_list as $row => $value){

			?>

				<tr>

					<td><?php echo ($value['type']=='pendirian')?'Akta Pendirian': (($value['type']=='perubahan') ? 'Akta Perubahan Terakhir (mengenai anggaran dasar)' : (($value['type']=='direksi') ? 'Akta Perubahan Terakhir (mengenai susunan terakhir direksi dan komisaris)' : 'Akta Perubahan Terakhir (mengenai susunan terakhir pemegang saham)'));?></td>

					<td><?php echo $value['notaris'];?></td>

					<td><?php echo $value['no'];?></td>

					<td><?php echo (strtotime($value['issue_date']) > 0) ? default_date($value['issue_date']):"-";?></td>

					<td><a href="<?php echo base_url('lampiran/akta_file/'.$value['akta_file']);?>" target="_blank"><?php echo $value['akta_file'];?> <i class="fa fa-link"></i></a></td>

					<td><?php echo $value['authorize_by'];?></td>

					<td><?php echo $value['authorize_no'];?></td>

					<td><?php echo (strtotime($value['authorize_date']) > 0) ? default_date($value['authorize_date']):"-";?></td>

					<td><a href="<?php echo base_url('lampiran/authorize_file/'.$value['authorize_file']);?>"  target="_blank"><?php echo $value['authorize_file'];?> <i class="fa fa-link"></i></a></td>
					<td><?php if($value['file_extension_akta']!='') {?><a href="<?php echo base_url('lampiran/file_extension_akta/'.$value['file_extension_akta']);?>"  target="_blank"><?php echo $value['file_extension_akta'];?> <i class="fa fa-link"></i></a><?php } ?></td>

					<td class="actionBlock">

						<a href="<?php echo site_url('akta/edit/'.$value['id'])?>" class="editBtn"><i class="fa fa-cog"></i>Edit</a>

						<a style="top: 4px" href="<?php echo site_url('akta/hapus/'.$value['id'])?>" class="delBtn"><i class="fa fa-trash"></i>Hapus</a>

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