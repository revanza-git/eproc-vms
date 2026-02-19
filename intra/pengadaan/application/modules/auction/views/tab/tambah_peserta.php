<?php echo $this->session->flashdata('msgSuccess')?>
<h2 class="formHeader">Daftar Penyedia Barang / Jasa Terdaftar</h2>
<div class="tableWrapper">
	
	<div class="filterBtnWp" style="justify-content: flex-end;">
		<button class="editBtn lihatData filterBtn">Filter</button>
	</div>
	<table class="tableData">
		<thead>
			<tr>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>&by=name">Nama Penyedia Barang &amp; Jasa<i class="fa fa-sort-<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['point'] == 'asc') ? 'desc' : 'asc'; ?>&by=point">Point<i class="fa fa-sort-<?php echo ($sort['point'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				
				<td class="actionPanel">Action</td>
			</tr>
		</thead>
		<tbody>
		<?php 
		if(count($vendor_list)){
			foreach($vendor_list as $row => $value){
			?>
				<tr>
					<form action="<?php echo site_url('auction/add_peserta/'.$id.'/'.$value['id'])?>" method="post">
					<td><?php echo $value['name'];?> <?php if($value['is_vms']==0){ echo '(Non-VMS)';}?></td>
					<td><?php echo $value['point'];?></td>
					
					<td class="actionBlock">
						<button type="submit" class="btnBlue"><i class="fa fa-plus"></i>&nbsp;Tambah Penyedia Barang &amp; Jasa</button> 
					</td>
					</form>	
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
		<div class="filterWrapperInner">
			<form method="POST">
				<?php echo $filter_list;?>
			</form>
		</div>	
	</div>
</div>