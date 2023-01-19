<?php echo $this->session->flashdata('msgSuccess')?>
<h2 class="formHeader">Daftar Penyedia Barang / Jasa Terdaftar</h2>
<div class="tableWrapper">
	<div class="tableHeader">
		<form method="POST">
			<!-- <div class="suggestionGroup">
				<div class="suggestion">
					<input type="input" placeholder="Cari Vendor" name="q" class="suggestionInput" id="vendor_name" >
					<input type="hidden" id="id_vendor" name="id_vendor"></div>
					<button type="submit" class="suggestionSubmit"><i class="fa fa-search"></i></button>
				</div>
				
			</div> -->
			<?php echo $filter_list;?>
		</form>	
	</div>
	<table class="tableData">
		<thead>
			<tr>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>&by=name">Nama Penyedia Barang &amp; Jasa<i class="fa fa-sort-<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['point'] == 'asc') ? 'desc' : 'asc'; ?>&by=point">Point<i class="fa fa-sort-<?php echo ($sort['point'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				
				<td colspan="2">Action</td>
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
						<button type="submit"><i class="fa fa-plus"></i>&nbsp;Tambah Penyedia Barang &amp; Jasa</button> 
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
</div>