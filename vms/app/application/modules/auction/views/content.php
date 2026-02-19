<?php echo $this->session->flashdata('msgSuccess')?>
<h2 class="formHeader">Daftar Auction</h2>
<div class="tableWrapper" style="margin-bottom: 20px">
	<!-- <form method="POST">
				
		<?php echo $filter_list;?>
		
	</form>	 -->
	<div class="filterBtnWp" >
		<?php if($this->uri->segment(2)!='selesai'&&$this->uri->segment(2)!='langsung'){ ?>
			<a href="<?php echo site_url('auction/tambah');?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a>
		<?php } ?>
		<button class="editBtn lihatData filterBtn">Filter</button>
	</div>
	<div class="tableHeader">		
		<div class="tableHeader">
			
			
			<!-- <?php if($this->uri->segment(2)!='selesai'&&$this->uri->segment(2)!='langsung'){ ?>
				<a href="<?php echo site_url('auction/tambah');?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a>
			<?php } ?> -->
		</div>
	</div>

	<table class="tableData">
		<thead>
			<tr>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>&by=ms_procurement.name">Nama Paket<i class="fa fa-sort-<?php echo ($sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['auction_date'] == 'asc') ? 'desc' : 'asc'; ?>&by=auction_date">Tanggal<i class="fa fa-sort-<?php echo ($sort['auction_date'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['work_area'] == 'asc') ? 'desc' : 'asc'; ?>&by=work_area">Lokasi<i class="fa fa-sort-<?php echo ($sort['work_area'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td class="actionPanel" style="width: 250px;">Action</td>
			</tr>
		</thead>
		<tbody>
		<?php 
		if(count($auction_list)){
			foreach($auction_list as $row => $value){
			?>
				<tr>
					<td><?php echo $value['name'];?></td>
					<td><?php echo default_date($value['auction_date']);?></td>
					<td><?php echo ($value['work_area']=='kantor_pusat')?'Kantor Pusat':'Site Office';?></td>
					<td class="actionBlock">
						<a href="<?php echo site_url('auction/ubah/'.$value['id'])?>" class="editBtn"><i class="fa fa-cog"></i>&nbsp;Ubah</a>
						<a href="<?php echo site_url('auction/duplikat/'.$value['id'])?>" class="editBtn printSerti"><i class="fa fa-files-o"></i></i>&nbsp;Duplikat</a>
						
						<?php if($auction_status=='selesai'){ ?>
						<a href="<?php echo site_url('auction/report/index/internal/'.$value['id'])?>" class="editBtn lihatData">
							<i class="fa fa-file-text-o"></i>&nbsp;Laporan Internal
						</a>
						<a href="<?php echo site_url('auction/report/index/eksternal/'.$value['id'])?>" class="editBtn lihatData">
							<i class="fa fa-file-text-o"></i>&nbsp;Laporan Eksternal
						</a>
						<?php }?>
						
						<a href="<?php echo site_url('auction/admin/auction_progress/index/'.$value['id'])?>" class="editBtn lihatData"><i class="fa fa-search"></i>&nbsp;Lihat Pengadaan</a>
						<a href="<?php echo site_url('auction/hapus/'.$value['id'])?>" class="delBtn" style="top: -4px"><i class="fa fa-trash"></i>&nbsp;Hapus</a>
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
	<div class="pageNumber">
		<?php echo $pagination ?>
	</div>
	<div class="filterWrapperOverlay"></div>
	<div class="filterWrapper">
		<form method="POST">
			<?php echo $filter_list;?>
		</form>
	</div>
</div>
