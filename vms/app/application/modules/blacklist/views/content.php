<?php echo $this->session->flashdata('msgSuccess')?>

<h1 class="formHeader">Daftar <?php echo $value; ?></h1>

<div class="btnTopGroup clearfix">

	
	

</div>


<div class="tableWrapper">
	<!-- <form method="POST">


		<?php echo $filter_list;?>

	</form>	 -->
	<div class="filterBtnWp">
		<?php if($this->session->userdata('admin')['id_role']== 1){;?>
		<a href="<?php echo site_url('blacklist/tambah/'.$id_blacklist)?>" class="btnBlue"><i class="fa fa-plus"></i>Tambah</a>
		<?php }?>
		<button class="editBtn lihatData filterBtn">Filter</button>
	</div>
	<div class="tableHeader">
		
	<a href="<?php echo site_url('blacklist/export_excel/'.$id_blacklist);?>" class="btnBlue exportBtn"><i class="fa fa-download"></i> Export</a>
		

	</div>



	<table class="tableData remark">

		<thead>

			<tr>

				<td style="width: 200px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['id_vendor'] == 'asc') ? 'desc' : 'asc'; ?>&by=id_vendor">Nama<i class="fa fa-sort-<?php echo ($sort['id_vendor'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				

				<td style="width: 180px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['point'] == 'asc') ? 'desc' : 'asc'; ?>&by=point">Skor terakhir<i class="fa fa-sort-<?php echo ($sort['point'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td style="width: 120px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['category'] == 'asc') ? 'desc' : 'asc'; ?>&by=category">Kategori<i class="fa fa-sort-<?php echo ($sort['category'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				

				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['remark'] == 'asc') ? 'desc' : 'asc'; ?>&by=remark">Keterangan<i class="fa fa-sort-<?php echo ($sort['remark'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td style="width: 190px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['start_date'] == 'asc') ? 'desc' : 'asc'; ?>&by=start_date">Tanggal Mulai<i class="fa fa-sort-<?php echo ($sort['start_date'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<?php if($id_blacklist!=2){ ?>

				<td style="width: 190px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['end_date'] == 'asc') ? 'desc' : 'asc'; ?>&by=end_date">Tanggal Akhir<i class="fa fa-sort-<?php echo ($sort['end_date'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<?php } ?>

				
				<td style="width: 80px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['total_bl'] == 'asc') ? 'desc' : 'asc'; ?>&by=total_bl">Total<i class="fa fa-sort-<?php echo ($sort['total_bl'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td>File</td>

				<td class="actionPanel">Action</td>

			</tr>

		</thead>

		<tbody>

		<?php 

		// echo print_r($blacklist);

		if(count($blacklist)){

			$yellow = "";

			foreach($blacklist as $row => $value){

				if (($value['need_approve_bl'] == 1 && $value['is_white'] == 0 && $value['del'] == 0 || $value['need_approve_bl'] == 1 && $value['is_white'] == 1 && $value['del'] == 0 )){

					$yellow = "background: #f1c40f; font-weight: bold;";

				}else{$yellow="";}

		?> 

				<tr style="<?php echo $yellow;?>">

					<td><?php echo $value['name'];?></td>

					

					<td ><?php echo $value['point'];?></td>

					<td ><?php echo $value['category'];?></td>

					

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

					<td><?php echo default_date($value['start_date']);?></td>

					<?php if($id_blacklist!=2){ ?>

					<td><?php echo default_date($value['end_date']);?></td>
					

					<?php } ?>

					<td><?php echo $value['total_bl'];?></td>

					<td><a href="<?php echo base_url('lampiran/blacklist_file/'.$value['blacklist_file']);?>" target="_blank"><?php // echo $value['blacklist_file'];?><i class="fa fa-download"></i></a></td>



					<td class="actionBlock">
							<a href="<?php echo site_url('vendor/dpt_print/'.$value['id_vendor'])?>" class="editBtn printSerti">
							<i class="fa fa-print"></i>&nbsp;Print Data
 						</a>
 						<a href="<?php echo site_url('/approval/administrasi/'.$value['id_vendor'])?>" class="editBtn lihatData">
							<i class="fa fa-search"></i>&nbsp;Lihat Data
 						</a>
						<?php 
						if($value['del']==0){

						if($this->session->userdata('admin')['id_role']== 1){;?>

						<a href="<?php echo site_url('blacklist/edit/'.$value['id_tr'].'/'.$value['id_blacklist'])?>" class="editBtn"><i class="fa fa-cog"></i>Edit</a>

						<?php }?>

						<?php 

							if($this->session->userdata('admin')['id_role']==8){



								if($value['need_approve_bl']==1){

									if($value['is_white']==1){ ?>

									<a class="editBtn aktifkan" href="<?php echo site_url('blacklist/approve_aktif/'.$value['id_tr'])?>"  class="alertApprove"><i class="fa fa-check-square-o"></i>Setuju Aktifkan kembali</a>

									<?php } else{ ?>

									<a class="editBtn aktifkan" href="<?php echo site_url('blacklist/approve_form/'.$value['id_vendor'].'/'.$value['id_tr'].'/'.$value['id_blacklist'])?>" class="alertApprove"><i class="fa fa-check-square-o"></i>&nbsp;Setujui Masuk <?php echo $value['blacklist_val']?></a>

									<?php }

								}



							}



							if($this->session->userdata('admin')['id_role']==1 && $value['need_approve_bl'] == 0){

								?>

								<a class="editBtn aktifkan" href="<?php echo site_url('blacklist/aktif/'.$value['id_tr'])?>" ><i class="fa fa-check-square-o"></i>Aktifkan</a>

								<?php

							}

							// echo $value['is_white'].$value['need_approve_bl'];
						}
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



	<ul>

		<li style="border-left: 10px #f1c40f solid; padding-left:20px; margin-bottom: 10px;">Menunggu Approval Supervisor</li>

		<!-- <li style="border-left: 10px #bdc3c7 solid; padding-left:20px;">Menunggu Approval (Whitelist)</li> -->

	</ul>

	

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
