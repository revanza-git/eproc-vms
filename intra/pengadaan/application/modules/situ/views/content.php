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

	<!-- <a href="<?php echo site_url('situ/tambah')?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a> -->

</div>

<div class="tableWrapper">
	<div class="filterBtnWp">
		<a href="<?php echo site_url('situ/tambah')?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a>
		<button class="editBtn lihatData filterBtn">Filter</button>
	</div>


	<div class="tableHeader">	

		<!-- <form method="POST">

			<?php echo $filter_list;?>

		</form>		 -->

	</div>

	<table class="tableData">

		<thead>

			<tr>

				<td style="width: 100px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['type'] == 'asc') ? 'desc' : 'asc'; ?>&by=type">Nama Surat<i class="fa fa-sort-<?php echo ($sort['type'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['issue_by'] == 'asc') ? 'desc' : 'asc'; ?>&by=issue_by">Lembaga Penerbit<i class="fa fa-sort-<?php echo ($sort['issue_by'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['no'] == 'asc') ? 'desc' : 'asc'; ?>&by=no">No. SITU<i class="fa fa-sort-<?php echo ($sort['no'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td style="width: 85px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['issue_date'] == 'asc') ? 'desc' : 'asc'; ?>&by=issue_date">Tanggal<i class="fa fa-sort-<?php echo ($sort['issue_date'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td style="width: 100px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['expire_date'] == 'asc') ? 'desc' : 'asc'; ?>&by=expire_date">Masa Berlaku<i class="fa fa-sort-<?php echo ($sort['expire_date'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['address'] == 'asc') ? 'desc' : 'asc'; ?>&by=address">Alamat<i class="fa fa-sort-<?php echo ($sort['address'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<!-- <td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['file_extension_situ'] == 'asc') ? 'desc' : 'asc'; ?>&by=file_extension_situ">Bukti Perpanjangan<i class="fa fa-sort-<?php echo ($sort['file_extension_situ'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td> -->

				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['situ_file'] == 'asc') ? 'desc' : 'asc'; ?>&by=situ_file">Lampiran<i class="fa fa-sort-<?php echo ($sort['situ_file'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td class="actionPanel" style="width: 85px">Action</td>

			</tr>

		</thead>

		<tbody>

		<?php 
		
		if(count($situ_list)){
			// print_r($situ_list);
			foreach($situ_list as $row => $value){

			?>

				<tr>
					<td><?php echo $value['type'];?></td>
					<td><?php echo $value['issue_by'];?></td>
					<td style="width: 100px; word-break: break-word"><?php echo $value['no'];?></td>
					<td><?php echo (strtotime($value['issue_date']) > 0)? default_date($value['issue_date']) : "-" ; ?></td>
					<td><?php echo ($value['expire_date']=='lifetime')?'Seumur Hidup': ((strtotime($value['expire_date']) > 0)? default_date($value['expire_date']) : "-");?></td>
					<td><?php echo $value['address'];?></td>
					<!-- <td>

						<a href="<?php echo base_url('lampiran/file_extension_situ/'.$value['file_extension_situ']);?>" target="_blank">
							<?php echo $value['file_extension_situ'];?>
							<i class="fa fa-link"></i>
						</a>

					</td> -->
					<td><a href="<?php echo base_url('lampiran/situ_file/'.$value['situ_file']);?>"  target="_blank"><?php echo $value['situ_file'];?> <i class="fa fa-link"></i></a></td>
					<td class="actionBlock">
						<a href="<?php echo site_url('situ/edit/'.$value['id'])?>" class="editBtn"><i class="fa fa-cog"></i>Edit</a>
						<a href="<?php echo site_url('situ/hapus/'.$value['id'])?>" class="delBtn"><i class="fa fa-trash"></i>Hapus</a>
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