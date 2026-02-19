<?php echo $this->session->flashdata('msgSuccess')?>
<h2 class="formHeader">History Nilai Penyedia Barang &amp; Jasa</h2>
<div class="tableWrapper">
	<div class="tableHeader">
		<form method="POST">
			<?php //echo $filter_list;?>
		</form>	
	</div>
	<table class="tableData">
		<thead>
			<tr>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>&by=name">Nama Penyedia Barang &amp; Jasa<i class="fa fa-sort-<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['entry_stamp'] == 'asc') ? 'desc' : 'asc'; ?>&by=entry_stamp">Tanggal<i class="fa fa-sort-<?php echo ($sort['entry_stamp'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['score'] == 'asc') ? 'desc' : 'asc'; ?>&by=score">Nilai<i class="fa fa-sort-<?php echo ($sort['score'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<!-- <td class="actionPanel">Action</td> -->
			</tr>
		</thead>
		<tbody>
		<?php 
		if(count($history)){
			foreach($history as $row =>$value){
			?>
				<tr>
					<td><?php echo $value['name'];?></td>
					<td><?php echo default_date($value['entry_stamp']);?></td>
					<td><?php echo $value['score'];?></td>
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
