<?php echo $this->session->flashdata('msgSuccess')?>
<?php echo $this->session->flashdata('msgError')?>
<div class="btnTopGroup clearfix">
	<!-- <a href="<?php echo site_url('admin/admin_sub_bidang/tambah_sub_bidang')?>" class="btnBlue"><i class="fa fa-plus"></i>Tambah</a> -->
</div>
<div class="tableWrapper">
	<!-- <form method="POST">
		<?php echo $filter_list;?>
	</form> -->
	<div class="filterBtnWp">
		<a href="<?php echo site_url('admin/admin_sub_bidang/tambah_sub_bidang')?>" class="btnBlue"><i class="fa fa-plus"></i>Tambah</a>
		<button class="editBtn lihatData filterBtn">Filter</button>
	</div>
	<div class="tableHeader">
			
	</div>
	<table class="tableData remark">
		<thead>
			<tr>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['tb_bidang.id'] == 'asc') ? 'desc' : 'asc'; ?>&by=tb_bidang.id">Nama Bidang Usaha<i class="fa fa-sort-<?php echo ($sort['tb_bidang.id'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>&by=name">Nama Sub Bidang<i class="fa fa-sort-<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td class="actionPanel">Action</td>
			</tr>
		</thead>
		<tbody>
		<?php 
		if(count($subbidang)){
			foreach($subbidang as $row => $value){
			?>
				<tr>
					<td class="remarkTxt" style="width:50px !important;"><?php echo $value['id_bidang']?></td>
					<td class="remarkTxt"><?php echo $value['name'];?></td>
					<td class="actionBlock">
						<a href="<?php echo site_url('admin/admin_sub_bidang/edit_sub_bidang/'.$value['id'])?>" class="editBtn"><i class="fa fa-cog"></i>Edit</a>
						<a style="top: -4px" href="<?php echo site_url('admin/admin_sub_bidang/hapus_sub_bidang/'.$value['id'])?>" class="delBtn"><i class="fa fa-trash"></i>Hapus</a>
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