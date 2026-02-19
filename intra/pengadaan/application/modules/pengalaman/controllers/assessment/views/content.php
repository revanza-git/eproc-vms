<?php echo $this->session->flashdata('msgSuccess')?>
<h2 class="formHeader">Daftar Pengadaan</h2>
<div class="tableWrapper" style="margin-bottom: 20px">
	<div class="tableHeader">
		<form method="POST">
			<?php echo $filter_list;?>
			<!-- <<a href="<?php echo site_url('pengadaan/tambah');?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a> -->
		</form>	
	</div>
	<table class="tableData">
		<thead>
			<tr>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>&by=ms_procurement.name">Nama Pengadaan<i class="fa fa-sort-<?php echo ($sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['pemenang'] == 'asc') ? 'desc' : 'asc'; ?>&by=pemenang">Nama Pemenang Sesuai Kontrak<i class="fa fa-sort-<?php echo ($sort['pemenang'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['point'] == 'asc') ? 'desc' : 'asc'; ?>&by=point">Skor Assessment<i class="fa fa-sort-<?php echo ($sort['point'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['tr_assessment.category'] == 'asc') ? 'desc' : 'asc'; ?>&by=tr_assessment.category">Kategori<i class="fa fa-sort-<?php echo ($sort['tr_assessment.category'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td class="actionPanel">Action</td>
			</tr>
		</thead>
		<tbody>
		<?php 
		if(count($pengadaan_list)){
			foreach($pengadaan_list as $row => $value){
			?>
				<tr>
					<td><?php echo $value['name'];?></td>
					<td><?php echo $value['pemenang'];?></td>
					<td><?php echo $value['point'];?></td>
					<td><?php echo $value['category'];?></td>
					<td class="actionBlock">
						<a href="<?php echo site_url('assessment/form_assessment/'.$value['id'].'/'.$value['id_vendor'])?>" class="editBtn"><i class="fa fa-search"></i>&nbsp;Lihat</a>
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
</div>
