<?php echo $this->session->flashdata('msgSuccess')?>
<h2 class="formHeader">Daftar Penyedia Barang/Jasa</h2>
<div class="tableWrapper">
	<!-- <form method="POST">
		<?php echo $filter_list;?>
	</form>	 -->
	<div class="filterBtnWp">
		<a href="<?php echo site_url('admin/admin_vendor/export_excel/');?>" class="btnBlue exportBtn"><i class="fa fa-download"></i> Export</a>
		<button class="editBtn lihatData filterBtn">Filter</button>
	</div>
	<div class="tableHeader">
		
		
	</div>
	<table class="tableData">
		<thead>
			<tr>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['ms_vendor.name'] == 'asc') ? 'desc' : 'asc'; ?>&by=ms_vendor.name">Nama Penyedia Barang &amp; Jasa<i class="fa fa-sort-<?php echo ($sort['ms_vendor.name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td style="width:100px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['legal_name'] == 'asc') ? 'desc' : 'asc'; ?>&by=legal_name">Badan Usaha<i class="fa fa-sort-<?php echo ($sort['legal_name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['username'] == 'asc') ? 'desc' : 'asc'; ?>&by=username">Username<i class="fa fa-sort-<?php echo ($sort['username'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['password'] == 'asc') ? 'desc' : 'asc'; ?>&by=password">Password<i class="fa fa-sort-<?php echo ($sort['password'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td style="width:120px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['is_active'] == 'asc') ? 'desc' : 'asc'; ?>&by=is_active">Status<i class="fa fa-sort-<?php echo ($sort['is_active'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td  style="width:180px" class="actionPanel">Action</td>
			</tr>
		</thead>
		<tbody>
		<?php 
		if(count($vendor_list)){
			foreach($vendor_list as $row => $value){
			?>
				<tr class="<?php echo ($value['is_active']==1)?'':'greyRow';?>">
					<td><?php echo $value['name'];?></td>
					<td><?php echo $value['legal_name'];?></td>
					<td><?php echo $value['username'];?></td>
					<td><?php echo $value['password'];?></td>
					<td><?php echo ($value['is_active']==1)?'Aktif':'Tidak Aktif';?></td>
					<td class="actionBlock">
						<!--<a href="<?php echo site_url('pengalaman/edit/'.$value['id'])?>" class="editBtn">Edit</a> | -->
						<?php if($value['is_active']==1 && $this->session->userdata('admin')['id_role'] == 1){ ?>
						<a href="<?php echo site_url('admin/admin_vendor/hapus/'.$value['id'])?>" class="delBtn"><i class="fa fa-trash"></i>Hapus</a>
						<?php } ?>
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