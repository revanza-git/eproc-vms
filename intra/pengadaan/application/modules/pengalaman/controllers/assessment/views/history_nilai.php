<?php echo $this->session->flashdata('msgSuccess')?>
<h2 class="formHeader">History Nilai <?php ?></h2>
<div class="tableWrapper" style="margin-bottom: 20px">
	<!-- <div class="tableHeader">
		<form method="POST">
			<?php //echo $filter_list;?>
			<<a href="<?php echo site_url('pengadaan/tambah');?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a>
		</form>	
	</div> -->
	<table class="tableData">
		<thead>
			<tr>
				<td>Nama Penyedia Barang &amp; Jasa</td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['date'] == 'asc') ? 'desc' : 'asc'; ?>&by=date">Tanggal<i class="fa fa-sort-<?php echo ($sort['date'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['point'] == 'asc') ? 'desc' : 'asc'; ?>&by=point">Nilai<i class="fa fa-sort-<?php echo ($sort['point'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<!-- <td class="actionPanel">Action</td> -->
			</tr>
		</thead>
		<tbody>
		<?php 
		if(count($list)){
			foreach($list as $row => $value){
			?>
				<tr>
					<td><?php echo $value['peserta_name'];?></td>
					<?php $date= strtotime($value['date']); ?>
					<td><?php echo date("d F Y",$date);?></td>
					<td><?php echo $value['point'];?></td>
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
