<?php echo $this->session->flashdata('msgSuccess')?>
<?php echo $this->session->flashdata('msgError')?>
<div class="btnTopGroup clearfix">
	<!-- <a href="<?php echo site_url('admin/admin_kurs/tambah_kurs')?>" class="btnBlue"><i class="fa fa-plus"></i>Tambah</a> -->
</div>
<div class="tableWrapper">
	<!-- <form method="POST">
		<?php echo $filter_list;?>
	</form>	 -->
	<div class="filterBtnWp">
		<a href="<?php echo site_url('admin/admin_kurs/tambah_kurs')?>" class="btnBlue"><i class="fa fa-plus"></i>Tambah</a>
		<button class="editBtn lihatData filterBtn">Filter</button>
	</div>
	<div class="tableHeader">
		
	</div>
	<table class="tableData">
		<thead>
			<tr>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>&by=name">Nama Mata Uang<i class="fa fa-sort-<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['symbol'] == 'asc') ? 'desc' : 'asc'; ?>&by=symbol">Simbol Mata Uang<i class="fa fa-sort-<?php echo ($sort['symbol'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td class="actionPanel">Action</td>
			</tr>
		</thead>
		<tbody>
		<?php 
		if(count($kurs)){
			foreach($kurs as $row => $value){
			?>
				<tr>
					<td><?php echo $value['name'];?></td>
					<td><?php echo $value['symbol'];?></td>
					<td class="actionBlock">
						<a href="<?php echo site_url('admin/admin_kurs/edit_kurs/'.$value['id'])?>" class="editBtn"><i class="fa fa-cog"></i>Edit</a>
						<a style="top: -4px" href="<?php echo site_url('admin/admin_kurs/hapus_kurs/'.$value['id'])?>" class="delBtn"><i class="fa fa-trash"></i>Hapus</a>
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