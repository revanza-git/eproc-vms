<?php echo $this->session->flashdata('msgSuccess')?>
<?php echo $this->session->flashdata('msgError')?>


<div class="btnTopGroup clearfix">
	<!-- <a href="<?php echo site_url('admin/admin_bidang/tambah_bidang')?>" class="btnBlue"><i class="fa fa-plus"></i>Tambah</a> -->
</div>
<div class="tableWrapper">
	<!-- <form method="POST">
		<?php echo $filter_list;?>
	</form>	 -->
	<div class="filterBtnWp">
		<a href="<?php echo site_url('admin/admin_bidang/tambah_bidang')?>" class="btnBlue"><i class="fa fa-plus"></i>Tambah</a>
		<button class="editBtn lihatData filterBtn">Filter</button>
	</div>
	<div class="tableHeader">
		
	</div>
	<table class="tableData">
		<thead>
			<tr>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['id_dpt_type'] == 'asc') ? 'desc' : 'asc'; ?>&by=id_dpt_type">Nama Grup Bidang Usaha<i class="fa fa-sort-<?php echo ($sort['id_dpt_type'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>&by=name">Nama Bidang<i class="fa fa-sort-<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td class="actionPanel">Action</td>
			</tr>
		</thead>
		<tbody>
		<?php 
		if(count($akta_list)){
			foreach($akta_list as $row => $value){
			?>
				<tr>
					<td><?php echo $value['id_dpt_type']?></td>
					<td><?php echo $value['name'];?></td>
					<td class="actionBlock">
						<a href="<?php echo site_url('admin/admin_bidang/edit_bidang/'.$value['id'])?>" class="editBtn"><i class="fa fa-cog"></i>Edit</a>
						<a style="top: -4px" href="<?php echo site_url('admin/admin_bidang/hapus_bidang/'.$value['id'])?>" class="delBtn"><i class="fa fa-trash"></i>Hapus</a>
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