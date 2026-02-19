<div class="tableWrapper">
	<table class="tableData">
		<thead>
			<tr>
				<?php if(isset($name)){ ?>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>&by=name">Nama Perusahaan<i class="fa fa-sort-<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<?php } ?>
				<?php if(isset($address)){ ?>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['address'] == 'asc') ? 'desc' : 'asc'; ?>&by=address">Alamat<i class="fa fa-sort-<?php echo ($sort['address'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<?php } ?>
				<?php if(isset($city)){ ?>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['city'] == 'asc') ? 'desc' : 'asc'; ?>&by=city">Kota<i class="fa fa-sort-<?php echo ($sort['city'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
		<?php if(isset($name)){
			foreach($name as $row => $value){
			?>	
				<tr>
					<?php if (isset($name)) {?>
					<td><?php echo $value['name'];?></td>
					<?php }?>

					<?php if (isset($address)) {?>
					<td><?php echo $value['address'];?></td>
					<?php }?>

					<?php if (isset($city)) {?>
					<td><?php echo $value['city'];?></td>
					<?php }?>
				</tr>
			<?php 
			}
		}else{?>
			<tr>
				<td colspan="11" class="noData">Data tidak ada</td>
			</tr>
		<?php }?>



		</tbody>
	</table>	
</div>

<div class="pageNumber">
	<?php // echo $pagination ?>
</div>