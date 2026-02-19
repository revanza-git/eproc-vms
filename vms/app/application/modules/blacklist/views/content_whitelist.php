<?php echo $this->session->flashdata('msgSuccess')?>

<?php echo $this->session->flashdata('msgError')?>

<h1 class="formHeader">Daftar Putih</h1>

<!-- <div class="btnTopGroup clearfix">

	<a href="<?php echo site_url('blacklist/tambah/'.$id_blacklist)?>" class="btnBlue"><i class="fa fa-plus"></i>Tambah</a>

</div> --><!-- 

<div class="btnTopGroup clearfix">

	<a href="<?php echo site_url('blacklist/tambah_baru')?>" class="btnBlue"><i class="fa fa-plus"></i>Tambah Baru</a>

</div> -->


<div class="tableWrapper">
	<!-- <form method="POST">

		<?php echo $filter_list;?>

	</form>	 -->
	<div class="filterBtnWp">
		<a href="<?php echo site_url('blacklist/export_whitelist/');?>" class="btnBlue exportBtn"><i class="fa fa-download"></i> Export</a>
		<button class="editBtn lihatData filterBtn">Filter</button>
	</div>

	<div class="tableHeader">


		

	</div>

	<table class="tableData remark">

		<thead>

			<tr>

				<td style="width: 285px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['id_vendor'] == 'asc') ? 'desc' : 'asc'; ?>&by=id_vendor">Penyedia Barang &amp; Jasa<i class="fa fa-sort-<?php echo ($sort['id_vendor'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td style="width: 210px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['white_date'] == 'asc') ? 'desc' : 'asc'; ?>&by=white_date">Tanggal Pemutihan<i class="fa fa-sort-<?php echo ($sort['white_date'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td style="width: 145px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['point'] == 'asc') ? 'desc' : 'asc'; ?>&by=point">Skor Terakhir<i class="fa fa-sort-<?php echo ($sort['point'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td style="width: 120px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['category'] == 'asc') ? 'desc' : 'asc'; ?>&by=category">Kategori<i class="fa fa-sort-<?php echo ($sort['category'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				

				<td width='150'><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['remark'] == 'asc') ? 'desc' : 'asc'; ?>&by=remark">Keterangan<i class="fa fa-sort-<?php echo ($sort['remark'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				
				<td>Total Pemutihan</td>
				<td>File</td>

				<td style="width: 190px" class="actionPanel">Action</td>

			</tr>

		</thead>

		<tbody>

		<?php 

		// echo print_r($whitelist);

		if(count($whitelist)){

			foreach($whitelist as $row => $value){

				$yellow = "";

				if (($value['need_approve_bl'] == 1 && $value['is_white'] == 0)||$value['need_approve']==1){

					$yellow = "background: #f1c40f; font-weight: bold;";

				}else{$yellow="";}

		?>

				<tr style="<?php echo $yellow;?>">

					<td><?php echo $value['name'];?></td>

					<td><?php echo (strtotime($value['white_date']) > 0 ) ? default_date($value['white_date']) : "-";?></td>

					<td><?php echo ( $value['point']!= "") ? $value['point']: "-";?></td>

					<td><?php echo ( $value['category']!= "") ? $value['category']: "-";?></td>

					<td class="remarkTxt text-container">
						<p class="text-content short-text less" text="<?php echo $value['remark'];?>">
							<?php echo $value['remark'];?>
						</p>
						<div class="show-more">
					        <a href="#">Tampilkan Lebih</a>
					    </div>
					    <div class="show-less">
					        <a href="#">Kurangi tampilan</a>
					    </div>
					</td>
					<td><?php echo $value['total_white'];?></td>
					<td><a href="<?php echo base_url('lampiran/blacklist_file/'.$value['blacklist_file']);?>" target="_blank"><?php //echo $value['blacklist_file'];?><i class="fa fa-download"></i></a></td>

					<td class="actionBlock">

						<a href="<?php echo site_url('vendor/dpt_print/'.$value['id_vendor'])?>" class="editBtn lihatData">
							<i class="fa fa-search"></i>&nbsp;Lihat Data
 						</a>

						<?php 

							if($this->session->userdata('admin')['id_role']==8 && $value['need_approve']==1){



								

								?><a class="editBtn aktifkan" href="<?php echo site_url('blacklist/approve_aktif/'.$value['id'])?>" ><i class="fa fa-check-square-o"></i>Setuju Aktifkan kembali</a>

									<?php

								}

							

							// echo $value['is_white'].$value['need_approve_bl'];

						?>

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