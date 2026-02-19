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
	<!-- <a href="<?php echo site_url('pemilik/tambah')?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a> -->
</div>
<div class="tableWrapper">
	<div class="filterBtnWp">
		<a href="<?php echo site_url('pemilik/tambah')?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a>
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
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>&by=name">Nama<i class="fa fa-sort-<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['shares'] == 'asc') ? 'desc' : 'asc'; ?>&by=shares">Saham dalam lembar<i class="fa fa-sort-<?php echo ($sort['shares'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['percentage'] == 'asc') ? 'desc' : 'asc'; ?>&by=percentage">Nilai Kepemilikan<i class="fa fa-sort-<?php echo ($sort['percentage'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td class="actionPanel">Action</td>
			</tr>
		</thead>
		<tbody>
		<?php 
		if(count($situ_list)){
			$total_share = 0;
			$total_percentage = 0;
			foreach($situ_list as $row => $value){
			?>
				<tr>
					<td><?php echo $value['name'];?></td>
					<td><?php echo $value['shares'];
					$total_share+=$value['shares'];
					?> lembar</td>
					<td>RP. <?php echo number_format($value['percentage']);
					$total_percentage+=$value['percentage'];
					?></td>
					<td class="actionBlock">
						<a href="<?php echo site_url('pemilik/edit/'.$value['id'])?>" class="editBtn"><i class="fa fa-cog"></i>Edit</a>
						<a style='top: -4px' href="<?php echo site_url('pemilik/hapus/'.$value['id'])?>" class="delBtn"><i class="fa fa-trash"></i>Hapus</a>
					</td>
				</tr>
			<?php 
			}?>
			<tr>
				<td></td>
				<td>Total: <?php echo $total_share;?> lembar</td>
				<td>Total: Rp. <?php echo number_format($total_percentage);?></td>
				<td class="actionBlock">
					
				</td>
			</tr>
		<?php
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