<div class="tab procView">
	<?php echo $this->utility->tabNav($tabNav,'kurs');?>
	
	<div class="tableWrapper" style="margin-bottom: 20px">
	<?php echo $this->session->flashdata('msgSuccess')?>
	<?php if($this->session->userdata('admin')['id_role']==6|7){ ?>
	<div class="btnTopGroup clearfix">
	
	<a href="<?php echo site_url('auction/tambah_kurs/'.$id);?>" class="btnBlue"><i class="fa fa-plus"></i>Tambah</a>
	</div>
	<?php } ?>
		<table class="tableData">
			<thead>
				<tr>
					<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>&by=name">Nama kurs<i class="fa fa-sort-<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
					<?php if($this->session->userdata('admin')['id_role']==6|7){ ?><td class="actionPanel">Action</td><?php } ?>
				</tr>
			</thead>
			<tbody>
			<?php 
			if(count($list)){
				foreach($list as $row => $value){
				?>
					<tr>
						<td><?php echo $value['name'];?></td>
						<?php if($this->session->userdata('admin')['id_role']==6|7){ ?>
						<td class="actionBlock">
							<a href="<?php echo site_url('auction/hapus_kurs/'.$value['id'].'/'.$id)?>" class="delBtn"><i class="fa fa-trash"></i>&nbsp;Hapus</a>
						</td>
						<?php } ?>
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

</div>